<?php
/**
 * Auth: Phone number field (Digits-aware)
 *
 * Strategy:
 *   - If the Digits (unitedover) plugin is active, it owns the phone field.
 *     We *read* Digits' user meta so our /profile/ and petitions can show
 *     the verified phone. We do NOT render a second phone input on forms
 *     because Digits injects its own.
 *   - If Digits is not active, we render our own E.164 phone field on
 *     BuddyBoss / WP register forms, save it as `sp_phone`, and validate
 *     duplicates.
 *
 * The Digits plugin stores phone in user meta key `digits_phone_no` and
 * the country code in `digt_countrycode` (confirmed in the Digits 9.x
 * public source).
 *
 * @package SampreShan_Child
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! function_exists( 'sp_digits_is_active' ) ) {
    /**
     * True when the Digits (unitedover) plugin is active. We probe
     * for known markers across the 8.x/9.x line — 9.x (e.g. 9.2.1)
     * defines neither DIGITS_VERSION nor a Digits class, so the
     * shortcode + login-function probes below are the reliable ones
     * on the frontend. If we miss one, the worst that happens is the
     * integration falls back to our default phone flow.
     */
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
        // Most robust: the dm-* login shortcodes Digits registers on init.
        if ( function_exists( 'shortcode_exists' )
            && ( shortcode_exists( 'dm-login-page' ) || shortcode_exists( 'dm-page' ) )
        ) {
            return true;
        }
        return false;
    }
}

if ( ! function_exists( 'sp_phone_normalize' ) ) {
    /**
     * Normalize a phone number to E.164. India is the default country.
     */
    function sp_phone_normalize( $phone, $country = 'IN' ) {
        $phone = preg_replace( '/[^\d+]/', '', (string) $phone );
        if ( '' === $phone ) { return ''; }
        if ( str_starts_with( $phone, '+' ) ) {
            $digits = substr( $phone, 1 );
            if ( preg_match( '/^\d{8,15}$/', $digits ) ) { return '+' . $digits; }
            return '';
        }
        $codes = array(
            'IN' => '91', 'US' => '1', 'CA' => '1', 'GB' => '44',
            'AU' => '61', 'NP' => '977', 'LK' => '94', 'BD' => '880',
            'MY' => '60', 'SG' => '65', 'AE' => '971', 'SA' => '966',
            'ZA' => '27', 'NG' => '234', 'DE' => '49', 'FR' => '33',
        );
        $cc = $codes[ strtoupper( $country ) ] ?? '91';
        if ( str_starts_with( $phone, '0' ) ) { $phone = substr( $phone, 1 ); }
        if ( str_starts_with( $phone, $cc ) ) { $phone = substr( $phone, strlen( $cc ) ); }
        if ( ! preg_match( '/^\d{6,14}$/', $phone ) ) { return ''; }
        return '+' . $cc . $phone;
    }
}

if ( ! function_exists( 'sp_phone_get' ) ) {
    /**
     * Get a user's verified phone.
     * Reads Digits' meta when present, falls back to sp_phone.
     */
    function sp_phone_get( $user_id = 0 ) {
        $user_id = (int) ( $user_id ?: get_current_user_id() );
        if ( $user_id <= 0 ) { return ''; }

        // Digits stores phone + country in two meta keys.
        if ( sp_digits_is_active() ) {
            $no   = (string) get_user_meta( $user_id, 'digits_phone_no', true );
            $cc   = (string) get_user_meta( $user_id, 'digt_countrycode', true );
            $cc   = ltrim( $cc, '+' );
            $no   = ltrim( $no, '0' );
            if ( $no !== '' ) {
                // Strip leading country code if Digits stored it as part of the number.
                if ( $cc !== '' && str_starts_with( $no, $cc ) ) {
                    $no = substr( $no, strlen( $cc ) );
                }
                $combined = '+' . ltrim( $cc . $no, '+0' );
                if ( preg_match( '/^\+\d{8,15}$/', $combined ) ) {
                    return $combined;
                }
            }
        }

        // Our own meta.
        return (string) get_user_meta( $user_id, 'sp_phone', true );
    }
}

if ( ! function_exists( 'sp_phone_set' ) ) {
    function sp_phone_set( $user_id, $phone, $country = 'IN' ) {
        $user_id = (int) $user_id;
        if ( $user_id <= 0 ) { return false; }
        $normalized = sp_phone_normalize( $phone, $country );
        if ( '' === $normalized ) { return false; }
        update_user_meta( $user_id, 'sp_phone',         $normalized );
        update_user_meta( $user_id, 'sp_phone_country', strtoupper( $country ) );
        update_user_meta( $user_id, 'sp_phone_raw',     (string) $phone );
        do_action( 'sampreshan_phone_updated', $user_id, $normalized );
        return $normalized;
    }
}

if ( ! function_exists( 'sp_phone_find_user_by_phone' ) ) {
    function sp_phone_find_user_by_phone( $phone, $country = 'IN' ) {
        $normalized = sp_phone_normalize( $phone, $country );
        if ( '' === $normalized ) { return false; }
        $users = get_users( array(
            'meta_key'   => 'sp_phone',
            'meta_value' => $normalized,
            'number'     => 1,
            'fields'     => array( 'ID', 'user_login', 'user_email' ),
        ) );
        return $users ? $users[0] : false;
    }
}

if ( ! function_exists( 'sp_phone_register_field' ) ) {
    /**
     * Render our phone field on the WP / BuddyBoss register form — ONLY
     * when Digits is not active. If Digits is active, it injects its own
     * phone input + OTP flow and we stay out of the way.
     */
    function sp_phone_register_field() {
        if ( sp_digits_is_active() ) { return; }
        $countries = array(
            'IN' => 'India (+91)', 'NP' => 'Nepal (+977)', 'LK' => 'Sri Lanka (+94)',
            'BD' => 'Bangladesh (+880)', 'US' => 'United States (+1)', 'CA' => 'Canada (+1)',
            'GB' => 'United Kingdom (+44)', 'AU' => 'Australia (+61)', 'MY' => 'Malaysia (+60)',
            'SG' => 'Singapore (+65)', 'AE' => 'UAE (+971)', 'SA' => 'Saudi Arabia (+966)',
            'ZA' => 'South Africa (+27)', 'NG' => 'Nigeria (+234)', 'DE' => 'Germany (+49)', 'FR' => 'France (+33)',
        );
        ?>
        <p class="sp-register-field">
            <label for="sp_phone_country"><?php esc_html_e( 'Country', 'sampreshan-child' ); ?></label><br>
            <select name="sp_phone_country" id="sp_phone_country" class="input" style="width:100%;max-width:100%">
                <?php foreach ( $countries as $code => $label ) : ?>
                    <option value="<?php echo esc_attr( $code ); ?>" <?php selected( 'IN', $code ); ?>><?php echo esc_html( $label ); ?></option>
                <?php endforeach; ?>
            </select>
        </p>
        <p class="sp-register-field">
            <label for="sp_phone"><?php esc_html_e( 'Mobile number (required)', 'sampreshan-child' ); ?></label><br>
            <input
                type="tel" name="sp_phone" id="sp_phone" class="input" required
                autocomplete="tel" inputmode="tel" pattern="[0-9+\-\s()]{6,20}"
                placeholder="<?php esc_attr_e( 'e.g. 98765 43210', 'sampreshan-child' ); ?>" style="width:100%"
            />
        </p>
        <?php
    }
    add_action( 'register_form', 'sp_phone_register_field' );
    add_action( 'bp_account_details_fields', 'sp_phone_register_field' );
    add_action( 'bp_signup_profile_fields',  'sp_phone_register_field' );
}

if ( ! function_exists( 'sp_phone_register_validate' ) ) {
    function sp_phone_register_validate( $errors ) {
        if ( sp_digits_is_active() ) { return $errors; }
        $phone   = isset( $_POST['sp_phone'] ) ? (string) wp_unslash( $_POST['sp_phone'] ) : '';
        $country = isset( $_POST['sp_phone_country'] ) ? (string) wp_unslash( $_POST['sp_phone_country'] ) : 'IN';
        if ( '' === $phone ) {
            $errors->add( 'sp_phone_required', __( '<strong>Mobile number</strong> is required.', 'sampreshan-child' ) );
            return $errors;
        }
        $normalized = sp_phone_normalize( $phone, $country );
        if ( '' === $normalized ) {
            $errors->add( 'sp_phone_invalid', __( 'Please enter a valid mobile number.', 'sampreshan-child' ) );
            return $errors;
        }
        if ( sp_phone_find_user_by_phone( $normalized, $country ) ) {
            $errors->add( 'sp_phone_taken', __( 'This mobile number is already registered. Please sign in instead.', 'sampreshan-child' ) );
        }
        return $errors;
    }
    add_filter( 'registration_errors', 'sp_phone_register_validate' );
    add_filter( 'bp_signup_validate',    'sp_phone_register_validate' );
}

if ( ! function_exists( 'sp_phone_register_save' ) ) {
    function sp_phone_register_save( $user_id ) {
        if ( sp_digits_is_active() ) { return; }
        $phone   = isset( $_POST['sp_phone'] ) ? (string) wp_unslash( $_POST['sp_phone'] ) : '';
        $country = isset( $_POST['sp_phone_country'] ) ? (string) wp_unslash( $_POST['sp_phone_country'] ) : 'IN';
        if ( $phone ) { sp_phone_set( $user_id, $phone, $country ); }
    }
    add_action( 'user_register',       'sp_phone_register_save' );
    add_action( 'bp_core_signup_user', 'sp_phone_register_save' );
}

if ( ! function_exists( 'sp_phone_admin_profile_field' ) ) {
    function sp_phone_admin_profile_field( $user ) {
        if ( ! current_user_can( 'edit_user', $user->ID ) ) { return; }
        $phone         = sp_phone_get( $user->ID );
        $phone_country = (string) get_user_meta( $user->ID, 'sp_phone_country', true ) ?: 'IN';
        $digits_active = sp_digits_is_active();
        ?>
        <tr>
            <th><label for="sp_phone"><?php esc_html_e( 'Mobile number', 'sampreshan-child' ); ?></label></th>
            <td>
                <?php if ( $digits_active ) : ?>
                    <code style="font-size:1.1em"><?php echo esc_html( $phone ?: '—' ); ?></code>
                    <p class="description">
                        <?php esc_html_e( 'Managed by the Digits plugin. To change it, use the Digits UI or have the user re-verify in their account page.', 'sampreshan-child' ); ?>
                    </p>
                <?php else : ?>
                    <input type="text" name="sp_phone_country" id="sp_phone_country" value="<?php echo esc_attr( $phone_country ); ?>" placeholder="IN" style="width:60px" />
                    <input type="tel" name="sp_phone" id="sp_phone" value="<?php echo esc_attr( $phone ); ?>" class="regular-text" placeholder="+91 98765 43210" />
                    <p class="description"><?php esc_html_e( 'Required. E.164 format, e.g. +919876543210', 'sampreshan-child' ); ?></p>
                <?php endif; ?>
            </td>
        </tr>
        <?php
    }
    add_action( 'show_user_profile', 'sp_phone_admin_profile_field' );
    add_action( 'edit_user_profile', 'sp_phone_admin_profile_field' );

    function sp_phone_admin_profile_save( $user_id ) {
        if ( sp_digits_is_active() ) { return; }
        if ( ! current_user_can( 'edit_user', $user_id ) ) { return false; }
        $phone   = isset( $_POST['sp_phone'] ) ? (string) wp_unslash( $_POST['sp_phone'] ) : '';
        $country = isset( $_POST['sp_phone_country'] ) ? strtoupper( (string) wp_unslash( $_POST['sp_phone_country'] ) ) : 'IN';
        if ( $phone ) { sp_phone_set( $user_id, $phone, $country ); }
        else {
            delete_user_meta( $user_id, 'sp_phone' );
            delete_user_meta( $user_id, 'sp_phone_country' );
            delete_user_meta( $user_id, 'sp_phone_raw' );
        }
    }
    add_action( 'personal_options_update', 'sp_phone_admin_profile_save' );
    add_action( 'edit_user_profile_update', 'sp_phone_admin_profile_save' );
}

if ( ! function_exists( 'sp_phone_rest_field' ) ) {
    function sp_phone_rest_field() {
        register_rest_field( 'user', 'sampreshan_phone', array(
            'get_callback' => function ( $user_data ) {
                return sp_phone_get( (int) $user_data['id'] );
            },
            'update_callback' => function ( $value, $user_object ) {
                if ( sp_digits_is_active() ) {
                    return new WP_Error( 'managed_by_digits', 'Phone is managed by the Digits plugin.', array( 'status' => 403 ) );
                }
                $country = get_user_meta( $user_object->ID, 'sp_phone_country', true ) ?: 'IN';
                return sp_phone_set( (int) $user_object->ID, (string) $value, (string) $country );
            },
            'schema' => array(
                'type' => 'string', 'description' => 'Mobile number (E.164)',
                'context' => array( 'view' ),
            ),
        ) );
    }
    add_action( 'rest_api_init', 'sp_phone_rest_field' );
}
