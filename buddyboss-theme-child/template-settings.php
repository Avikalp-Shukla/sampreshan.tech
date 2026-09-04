<?php
/**
 * Template Name: Profile Settings
 *
 * Edit display name, Sanatan fields, and password.
 *
 * @package SampreShan_Child
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! is_user_logged_in() ) {
    wp_safe_redirect( wp_login_url( home_url( '/settings/' ) ) );
    exit;
}

$user_id = get_current_user_id();
$user    = get_user_by( 'id', $user_id );
$notice  = '';
$errors  = array();

if ( 'POST' === $_SERVER['REQUEST_METHOD'] && isset( $_POST['sp_settings_nonce'] ) ) {
    if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['sp_settings_nonce'] ) ), 'sp_save_settings' ) ) {
        $errors[] = __( 'Security check failed. Please try again.', 'sampreshan-child' );
    } else {
        $display = isset( $_POST['display_name'] ) ? sanitize_text_field( wp_unslash( $_POST['display_name'] ) ) : '';
        $bio     = isset( $_POST['bio'] ) ? sanitize_textarea_field( wp_unslash( $_POST['bio'] ) ) : '';
        $gotra   = isset( $_POST['gotra'] ) ? sanitize_text_field( wp_unslash( $_POST['gotra'] ) ) : '';
        $sampr   = isset( $_POST['sampradaya'] ) ? sanitize_text_field( wp_unslash( $_POST['sampradaya'] ) ) : '';
        $loc     = isset( $_POST['location'] ) ? sanitize_text_field( wp_unslash( $_POST['location'] ) ) : '';
        $pass1   = isset( $_POST['new_password'] ) ? (string) $_POST['new_password'] : '';
        $pass2   = isset( $_POST['new_password2'] ) ? (string) $_POST['new_password2'] : '';

        if ( '' === $display ) {
            $errors[] = __( 'Display name cannot be empty.', 'sampreshan-child' );
        }
        if ( '' !== $pass1 || '' !== $pass2 ) {
            if ( $pass1 !== $pass2 ) {
                $errors[] = __( 'Passwords do not match.', 'sampreshan-child' );
            } elseif ( strlen( $pass1 ) < 8 ) {
                $errors[] = __( 'Password must be at least 8 characters.', 'sampreshan-child' );
            }
        }

        if ( empty( $errors ) ) {
            wp_update_user( array( 'ID' => $user_id, 'display_name' => $display ) );
            if ( function_exists( 'sp_profile_set_field' ) ) {
                sp_profile_set_field( 'bio', $bio, $user_id );
                sp_profile_set_field( 'gotra', $gotra, $user_id );
                sp_profile_set_field( 'sampradaya', $sampr, $user_id );
                sp_profile_set_field( 'location', $loc, $user_id );
            } else {
                update_user_meta( $user_id, 'description', $bio );
                update_user_meta( $user_id, 'sp_location', $loc );
            }
            if ( '' !== $pass1 ) {
                wp_set_password( $pass1, $user_id );
                // Re-auth after password change.
                wp_set_current_user( $user_id );
                wp_set_auth_cookie( $user_id, true );
            }
            $notice = __( 'Settings saved successfully.', 'sampreshan-child' );
            $user   = get_user_by( 'id', $user_id );
        }
    }
}

$display_name = $user ? $user->display_name : '';
$bio   = function_exists( 'sp_profile_get_field' ) ? sp_profile_get_field( 'bio', $user_id ) : (string) get_user_meta( $user_id, 'description', true );
$gotra = function_exists( 'sp_profile_get_field' ) ? sp_profile_get_field( 'gotra', $user_id ) : '';
$sampr = function_exists( 'sp_profile_get_field' ) ? sp_profile_get_field( 'sampradaya', $user_id ) : '';
$loc   = function_exists( 'sp_profile_get_field' ) ? sp_profile_get_field( 'location', $user_id ) : (string) get_user_meta( $user_id, 'sp_location', true );
$dash_url = home_url( '/dashboard/' );

get_header();
?>

<main class="sp-page sp-users sp-users--narrow" role="main">
    <header class="sp-users__hero">
        <p class="sp-section__eyebrow"><?php esc_html_e( 'Your account', 'sampreshan-child' ); ?></p>
        <h1 class="sp-users__title"><?php esc_html_e( 'Profile Settings', 'sampreshan-child' ); ?></h1>
        <p class="sp-users__sub"><?php esc_html_e( 'Update how your name, intro and Sanatan details appear across the community.', 'sampreshan-child' ); ?></p>
    </header>

    <div class="sp-dash-card sp-dash-card--pad">
        <?php if ( '' !== $notice ) : ?>
            <div class="sp-form-notice sp-form-notice--success" role="status"><?php echo esc_html( $notice ); ?></div>
        <?php endif; ?>
        <?php if ( ! empty( $errors ) ) : ?>
            <div class="sp-form-notice sp-form-notice--error" role="alert">
                <?php echo esc_html( implode( ' ', $errors ) ); ?>
            </div>
        <?php endif; ?>

        <form method="post" action="<?php echo esc_url( get_permalink() ); ?>" class="sp-settings-form">
            <?php wp_nonce_field( 'sp_save_settings', 'sp_settings_nonce' ); ?>

            <div class="sp-form-group">
                <label class="sp-form-label" for="sp-display"><?php esc_html_e( 'Display name', 'sampreshan-child' ); ?></label>
                <input class="sp-form-input" id="sp-display" type="text" name="display_name" value="<?php echo esc_attr( $display_name ); ?>" maxlength="60" required />
            </div>

            <div class="sp-form-group">
                <label class="sp-form-label" for="sp-bio"><?php esc_html_e( 'Short bio', 'sampreshan-child' ); ?></label>
                <textarea class="sp-form-textarea" id="sp-bio" name="bio" rows="3" maxlength="300" placeholder="<?php esc_attr_e( 'A line or two about yourself…', 'sampreshan-child' ); ?>"><?php echo esc_textarea( $bio ); ?></textarea>
            </div>

            <div class="sp-form-row">
                <div class="sp-form-group sp-form-group--half">
                    <label class="sp-form-label" for="sp-gotra"><?php esc_html_e( 'Gotra (optional)', 'sampreshan-child' ); ?></label>
                    <input class="sp-form-input" id="sp-gotra" type="text" name="gotra" value="<?php echo esc_attr( $gotra ); ?>" maxlength="60" />
                </div>
                <div class="sp-form-group sp-form-group--half">
                    <label class="sp-form-label" for="sp-sampr"><?php esc_html_e( 'Sampradaya (optional)', 'sampreshan-child' ); ?></label>
                    <input class="sp-form-input" id="sp-sampr" type="text" name="sampradaya" value="<?php echo esc_attr( $sampr ); ?>" maxlength="60" />
                </div>
            </div>

            <div class="sp-form-group">
                <label class="sp-form-label" for="sp-loc"><?php esc_html_e( 'Location', 'sampreshan-child' ); ?></label>
                <input class="sp-form-input" id="sp-loc" type="text" name="location" value="<?php echo esc_attr( $loc ); ?>" maxlength="100" placeholder="<?php esc_attr_e( 'City, State', 'sampreshan-child' ); ?>" />
            </div>

            <h2 class="sp-settings__subhead"><?php esc_html_e( 'Change password (optional)', 'sampreshan-child' ); ?></h2>
            <div class="sp-form-row">
                <div class="sp-form-group sp-form-group--half">
                    <label class="sp-form-label" for="sp-pass1"><?php esc_html_e( 'New password', 'sampreshan-child' ); ?></label>
                    <input class="sp-form-input" id="sp-pass1" type="password" name="new_password" autocomplete="new-password" minlength="8" />
                </div>
                <div class="sp-form-group sp-form-group--half">
                    <label class="sp-form-label" for="sp-pass2"><?php esc_html_e( 'Repeat password', 'sampreshan-child' ); ?></label>
                    <input class="sp-form-input" id="sp-pass2" type="password" name="new_password2" autocomplete="new-password" minlength="8" />
                </div>
            </div>

            <div class="sp-settings__actions">
                <button class="btn btn--primary btn--lg" type="submit"><?php esc_html_e( 'Save Changes', 'sampreshan-child' ); ?></button>
                <a class="btn btn--ghost" href="<?php echo esc_url( $dash_url ); ?>"><?php esc_html_e( 'Back to Dashboard', 'sampreshan-child' ); ?></a>
            </div>
        </form>
    </div>
</main>

<?php get_footer(); ?>
