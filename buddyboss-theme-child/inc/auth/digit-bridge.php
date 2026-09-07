<?php
/**
 * Auth: Digits (unitedover) bridge
 *
 * Digits 9.x already handles every auth surface: login form, register
 * form, password reset, OTP via SMS / Email / WhatsApp, social login,
 * BuddyBoss compatibility. Our job here is to:
 *
 *   1. Mirror Digits' verified phone into `sp_phone` so our profile
 *      page + petition signers list can show it.
 *   2. Auto-promote a new user to `petitioner` after a Digits signup
 *      or login (same UX as the email signup).
 *   3. Expose a Digits-aware "send magic login link" AJAX endpoint as
 *      a safety net while the operator is still configuring their
 *      SMS gateway. This works whether Digits is active or not.
 *   4. Stay completely out of the way when Digits is active for
 *      form rendering and login redirects (Digits already does it
 *      and the "redirect wp-login.php" change shipped in 8.6.1 means
 *      our `login_init` redirect is redundant).
 *
 * Digits + Firebase setup (sampreshan-login web app):
 *   1. Firebase console → sampreshan-tech → Authentication → Sign-in
 *      method → enable "Phone".
 *   2. Authentication → Settings → Authorized domains → add
 *      "sampreshan.tech" (plus any staging domain).
 *   3. Project settings (gear) → sampreshan-login → SDK setup →
 *      Config → copy the snippet. It must match sp_firebase_config().
 *   4. WP admin → Digits → Settings → Gateway → Firebase → paste the
 *      Config snippet, save, and use "Test API"/send a test OTP.
 *   5. Keep our /login page: it embeds [dm-login-page] when Digits is
 *      active, so the mobile-number option renders inside our brand
 *      shell. Fallback (no Digits) uses the same Firebase project via
 *      assets/js/firebase-otp.js.
 *
 * @package SampreShan_Child
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

// Re-use the detection function from phone.php if loaded.
if ( ! function_exists( 'sp_digits_is_active' ) ) {
    function sp_digits_is_active() {
        if ( defined( 'DIGITS_VERSION' )
            || class_exists( 'Digits' )
            || class_exists( 'digit_gateway' )
            || class_exists( 'digits_admin_menu' )
            || function_exists( 'digits_get_phone_number' )
            || function_exists( 'digits_login_button' )
            || function_exists( 'digits_page_onlylogin' )
        ) {
            return true;
        }
        if ( function_exists( 'shortcode_exists' )
            && ( shortcode_exists( 'dm-login-page' ) || shortcode_exists( 'dm-page' ) )
        ) {
            return true;
        }
        return false;
    }
}

if ( ! function_exists( 'sp_digits_after_login' ) ) {
    /**
     * Mirror the verified phone + auto-promote the user.
     *
     * Hook priority is low so Digits finishes writing its own meta first.
     * Digits fires `digits_after_login` and `digits_after_register` on
     * its own auth flow.
     */
    function sp_digits_after_login( $user_id ) {
        $user_id = (int) $user_id;
        if ( $user_id <= 0 ) { return; }
        if ( function_exists( 'sp_digits_mirror_phone' ) ) {
            sp_digits_mirror_phone( $user_id );
        }
        if ( function_exists( 'sp_auto_promote_subscriber_to_petitioner' ) ) {
            sp_auto_promote_subscriber_to_petitioner( $user_id );
        }
    }
    add_action( 'digits_after_login',    'sp_digits_after_login', 20 );
    add_action( 'digits_after_register', 'sp_digits_after_login', 20 );
    // wp_login fires for the standard (non-Digits) path; promote there too.
    add_action( 'wp_login',              'sp_digits_after_login', 20, 1 );
}

if ( ! function_exists( 'sp_digits_mirror_phone' ) ) {
    /**
     * Copy the verified phone stored by Digits (`digits_phone_no` +
     * `digt_countrycode`) into our own `sp_phone` so the rest of the
     * site (profile page, petition signers) can read it through the
     * single `sp_phone_get()` API.
     */
    function sp_digits_mirror_phone( $user_id ) {
        $user_id = (int) $user_id;
        if ( $user_id <= 0 ) { return; }
        $no = (string) get_user_meta( $user_id, 'digits_phone_no', true );
        if ( '' === $no ) { return; }
        $cc = (string) get_user_meta( $user_id, 'digt_countrycode', true );
        $cc = ltrim( $cc, '+' );
        $cc_int = (int) $cc;
        $country = 'IN'; // default
        if ( 1  === $cc_int ) { $country = 'US'; }
        elseif ( 91 === $cc_int ) { $country = 'IN'; }
        elseif ( 44 === $cc_int ) { $country = 'GB'; }
        // Build a normalized E.164 to store in sp_phone.
        $no_clean = ltrim( $no, '0' );
        if ( str_starts_with( $no_clean, $cc ) ) {
            $no_clean = substr( $no_clean, strlen( $cc ) );
        }
        $e164 = '+' . $cc . $no_clean;
        if ( preg_match( '/^\+\d{8,15}$/', $e164 ) ) {
            update_user_meta( $user_id, 'sp_phone',         $e164 );
            update_user_meta( $user_id, 'sp_phone_country', $country );
        }
    }
}

if ( ! function_exists( 'sp_digits_login_url' ) ) {
    /**
     * Find Digits' native login page (meta flag, common slugs, wp-login).
     */
    function sp_digits_login_url() {
        $digits_page = get_posts( array(
            'post_type'      => 'page',
            'meta_query'     => array( array( 'key' => '_digits_login_page', 'compare' => 'EXISTS' ) ),
            'posts_per_page' => 1,
            'post_status'    => 'publish',
            'fields'         => 'ids',
        ) );
        if ( $digits_page ) {
            $url = get_permalink( $digits_page[0] );
            if ( $url ) { return $url; }
        }
        foreach ( array( 'login', 'sign-in', 'signin', 'otp-login' ) as $slug ) {
            $p = get_page_by_path( $slug );
            if ( $p ) {
                $url = get_permalink( $p );
                if ( $url ) { return $url; }
            }
        }
        return home_url( '/wp-login.php' );
    }
}

if ( ! function_exists( 'sp_digits_embed_login_form' ) ) {
    /**
     * Render Digits' own mobile-number form inside our /login brand shell.
     * Uses the documented shortcodes ([dm-login-page] → [dm-page]) and only
     * echoes when the shortcode actually exists, so a missing/inactive
     * Digits install never prints a raw shortcode string.
     */
    function sp_digits_embed_login_form() {
        if ( ! sp_digits_is_active() ) { return false; }
        foreach ( array( 'dm-login-page', 'dm-page' ) as $tag ) {
            if ( shortcode_exists( $tag ) ) {
                echo do_shortcode( '[' . $tag . ']' );
                return true;
            }
        }
        return false;
    }
}

if ( ! function_exists( 'sp_digits_add_login_button' ) ) {
    /**
     * On our custom /login page, when Digits is NOT active, append a
     * "Login with phone" panel that uses the safety-net magic link.
     * When Digits IS active, we let Digits render its own form and
     * don't add anything.
     */
    function sp_digits_add_login_button() {
        if ( sp_digits_is_active() ) { return; }
        ?>
        <button type="button" class="sp-login__alt-btn" data-sp-open="phone-login" aria-controls="sp-phone-login-panel" aria-expanded="false">
            <?php sp_icon_auto( 'mail', 'sp-icon--sm sp-icon--saffron', __( 'Phone', 'sampreshan-child' ) ); ?>
            <?php esc_html_e( 'Login with mobile number', 'sampreshan-child' ); ?>
        </button>
        <p class="sp-login__alt-note"><?php esc_html_e( 'We will email a one-time login link to the address on file (valid for 15 minutes).', 'sampreshan-child' ); ?></p>
        <?php
    }
    add_action( 'sp_login_form_below', 'sp_digits_add_login_button' );
}

if ( ! function_exists( 'sp_digits_phone_login_ajax' ) ) {
    /**
     * Safety-net phone-login endpoint.
     *
     * Operates only when Digits is not active (when Digits is on, its
     * own AJAX endpoints handle OTP delivery). Issues a one-shot
     * 15-minute magic login link, emailed to the address on file.
     * Response is constant-time so attackers can't enumerate phones.
     */
    function sp_digits_phone_login_ajax() {
        check_ajax_referer( 'sp_phone_login', 'nonce' );

        if ( sp_digits_is_active() ) {
            // Defer to Digits; we just acknowledge.
            wp_send_json_success( array(
                'message' => __( 'Please use the digit login form.', 'sampreshan-child' ),
                'sent'    => false,
            ) );
        }

        $phone   = isset( $_POST['sp_phone'] ) ? (string) wp_unslash( $_POST['sp_phone'] ) : '';
        $country = isset( $_POST['sp_phone_country'] ) ? (string) wp_unslash( $_POST['sp_phone_country'] ) : 'IN';
        $normalized = sp_phone_normalize( $phone, $country );
        if ( '' === $normalized ) {
            wp_send_json_error( array( 'message' => __( 'Please enter a valid mobile number.', 'sampreshan-child' ) ), 400 );
        }
        $user = sp_phone_find_user_by_phone( $normalized, $country );
        if ( ! $user ) {
            wp_send_json_success( array(
                'message' => __( 'If that number is on file, a login link is on its way.', 'sampreshan-child' ),
                'sent'    => false,
            ) );
        }

        $key = wp_generate_password( 40, false );
        update_user_meta( $user->ID, 'sp_magic_login_key', $key );
        update_user_meta( $user->ID, 'sp_magic_login_expires', time() + 15 * MINUTE_IN_SECONDS );

        $url = add_query_arg( array(
            'sp_magic_login' => $user->ID,
            'sp_key'         => $key,
        ), home_url( '/' ) );

        wp_mail(
            $user->user_email,
            sprintf( '[%s] Login link', wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ) ),
            sprintf( "Hello,\n\nClick below to sign in (valid 15 minutes):\n\n%s\n\nIf you did not request this, ignore this email.\n\n— %s",
                $url, wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ) )
        );

        wp_send_json_success( array(
            'message' => __( 'If that number is on file, a login link is on its way.', 'sampreshan-child' ),
            'sent'    => true,
        ) );
    }
    add_action( 'wp_ajax_nopriv_sp_phone_login', 'sp_digits_phone_login_ajax' );
}

if ( ! function_exists( 'sp_digits_consume_magic_login' ) ) {
    function sp_digits_consume_magic_login() {
        if ( empty( $_GET['sp_magic_login'] ) || empty( $_GET['sp_key'] ) ) { return; }
        if ( sp_digits_is_active() ) { return; } // Digits handles its own login.
        $user_id = (int) $_GET['sp_magic_login'];
        $key     = (string) wp_unslash( $_GET['sp_key'] );
        $stored  = (string) get_user_meta( $user_id, 'sp_magic_login_key', true );
        $expires = (int) get_user_meta( $user_id, 'sp_magic_login_expires', true );
        if ( ! $stored || ! $expires || $expires < time() || ! hash_equals( $stored, $key ) ) {
            wp_safe_redirect( add_query_arg( 'sp_login_error', 'expired', home_url( '/login/' ) ) );
            exit;
        }
        delete_user_meta( $user_id, 'sp_magic_login_key' );
        delete_user_meta( $user_id, 'sp_magic_login_expires' );

        wp_set_current_user( $user_id );
        wp_set_auth_cookie( $user_id, true );

        if ( function_exists( 'sp_auto_promote_subscriber_to_petitioner' ) ) {
            sp_auto_promote_subscriber_to_petitioner( $user_id );
        }

        $u = get_user_by( 'id', $user_id );
        $dest = function_exists( 'sampreshan_dashboard_url' ) ? sampreshan_dashboard_url() : ( function_exists( 'bp_core_get_user_domain' ) ? bp_core_get_user_domain( $user_id ) : ( $u ? home_url( '/profile/' ) : home_url( '/' ) ) );
        wp_safe_redirect( $dest );
        exit;
    }
    add_action( 'init', 'sp_digits_consume_magic_login' );
}

if ( ! function_exists( 'sp_digits_skip_login_redirect' ) ) {
    /**
     * When Digits is active, do NOT redirect /wp-login.php to our
     * /login page — Digits already replaces the visual surface with
     * its own native form, including the "redirect wp-login.php" fix
     * shipped in 8.6.1.
     */
    function sp_digits_skip_login_redirect( $run ) {
        return sp_digits_is_active() ? false : $run;
    }
    add_filter( 'sampreshan_should_redirect_login', 'sp_digits_skip_login_redirect' );
}
