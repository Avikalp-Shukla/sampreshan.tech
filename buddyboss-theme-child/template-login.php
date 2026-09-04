<?php
/**
 * Template Name: Sanatan Login
 *
 * Behavior:
 *   - When the Digits (unitedover) plugin is ACTIVE: render a thin
 *     branded shell with a "Continue to sign in" button that takes
 *     the user to Digits' native login page (which already replaces
 *     /wp-login.php). We do NOT render our own form because Digits
 *     owns the OTP/email/WhatsApp flow and is far more complete.
 *   - When Digits is INACTIVE: render the full custom Sampreshan
 *     login form (login | register | lostpassword) using the 3D icon
 *     system + design tokens. This is the safety-net experience.
 *
 * Reads the `?action` query var for non-Digits mode.
 *
 * @package SampreShan_Child
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

$digits_active = function_exists( 'sp_digits_is_active' ) && sp_digits_is_active();

// If already logged in, send to profile.
if ( is_user_logged_in() && ! $digits_active ) {
    $u    = wp_get_current_user();
    $home = function_exists( 'bp_core_get_user_domain' ) ? bp_core_get_user_domain( $u->ID ) : home_url( '/profile/' );
    wp_safe_redirect( $home );
    exit;
}

$action = isset( $_GET['action'] ) ? sanitize_text_field( wp_unslash( $_GET['action'] ) ) : 'login';
if ( ! in_array( $action, array( 'login', 'register', 'lostpassword' ), true ) ) {
    $action = 'login';
}

$login_error = isset( $_GET['sp_login_error'] ) ? sanitize_text_field( wp_unslash( $_GET['sp_login_error'] ) ) : '';
$error_messages = array(
    'expired' => __( 'That login link has expired. Please request a new one.', 'sampreshan-child' ),
    'invalid' => __( 'That login link is invalid.', 'sampreshan-child' ),
);
$login_notice = ( '' !== $login_error && isset( $error_messages[ $login_error ] ) ) ? $error_messages[ $login_error ] : '';
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta name="theme-color" content="#FF9933" />
    <title><?php
        $titles = array(
            'login'         => __( 'Sign In', 'sampreshan-child' ),
            'register'      => __( 'Join SampreShan', 'sampreshan-child' ),
            'lostpassword'  => __( 'Reset Password', 'sampreshan-child' ),
        );
        echo esc_html( $titles[ $action ] . ' — ' . get_bloginfo( 'name' ) );
    ?></title>
    <?php wp_head(); ?>
</head>
<body class="sp-login-body<?php echo $digits_active ? ' sp-login-body--digits' : ''; ?>">

<div class="sp-login" data-sp-mode="<?php echo esc_attr( $action ); ?>" data-sp-digits="<?php echo $digits_active ? '1' : '0'; ?>">

    <a class="sp-login__brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php esc_attr_e( 'Home', 'sampreshan-child' ); ?>">
        <?php if ( function_exists( 'sp_logo_e' ) ) : ?>
            <?php sp_logo_e( 'sp-login__logo', 48 ); ?>
        <?php else : ?>
            <?php sp_icon_e( 'dharma', 'sp-icon--xl sp-icon--saffron sp-icon--float', __( 'SampreShan', 'sampreshan-child' ) ); ?>
        <?php endif; ?>
        <span class="sp-login__brand-name display"><?php bloginfo( 'name' ); ?></span>
    </a>

    <?php if ( $digits_active ) : ?>

        <!-- ======================================================
             DIGITS MODE: hand off to Digits' own login page.
             Digits handles the entire OTP / phone / email flow.
             ====================================================== -->
        <main class="sp-login__card glass sp-login__card--digits" role="main">
            <div class="sp-login__art" aria-hidden="true">
                <?php sp_icon_e( 'lotus', 'sp-icon--3xl sp-icon--saffron sp-icon--float', __( 'Lotus', 'sampreshan-child' ) ); ?>
                <?php sp_icon_e( 'om',    'sp-icon--2xl sp-icon--gold sp-icon--pulse',   __( 'Om',    'sampreshan-child' ) ); ?>
                <?php sp_icon_e( 'diya',  'sp-icon--lg sp-icon--saffron sp-icon--bounce', __( 'Diya',  'sampreshan-child' ) ); ?>
            </div>

            <header class="sp-login__head">
                <?php sp_icon_e( 'lock', 'sp-icon--lg sp-icon--saffron', __( 'Sign in', 'sampreshan-child' ) ); ?>
                <h1 class="sp-login__title display"><?php esc_html_e( 'Sign in with mobile OTP', 'sampreshan-child' ); ?></h1>
                <p class="sp-login__sub"><?php esc_html_e( 'We use the Digits plugin to send a one-time passcode to your mobile number — no passwords, no email required.', 'sampreshan-child' ); ?></p>
            </header>

            <ul class="sp-login__feature-list">
                <li><?php sp_icon_e( 'verified', 'sp-icon--sm sp-icon--success', __( 'Verified', 'sampreshan-child' ) ); ?><?php esc_html_e( 'Mobile OTP via SMS, Email, or WhatsApp', 'sampreshan-child' ); ?></li>
                <li><?php sp_icon_e( 'lock',     'sp-icon--sm sp-icon--blue',    __( 'Secure',  'sampreshan-child' ) ); ?><?php esc_html_e( 'No passwords to remember', 'sampreshan-child' ); ?></li>
                <li><?php sp_icon_e( 'star',     'sp-icon--sm sp-icon--gold',    __( 'Instant', 'sampreshan-child' ) ); ?><?php esc_html_e( 'Get signed in within seconds', 'sampreshan-child' ); ?></li>
            </ul>

            <?php
            // Embed Digits' own mobile-number form (shortcodes [dm-login-page]
            // / [dm-page]) so the number option renders inside our brand shell.
            $digits_embedded = function_exists( 'sp_digits_embed_login_form' ) ? sp_digits_embed_login_form() : false;
            $digits_login_url = function_exists( 'sp_digits_login_url' ) ? sp_digits_login_url() : home_url( '/wp-login.php' );
            ?>
            <?php if ( ! $digits_embedded ) : ?>
                <a class="sp-login__submit btn-3d btn-3d--lg" href="<?php echo esc_url( $digits_login_url ); ?>">
                    <?php sp_icon_e( 'check', 'sp-icon--sm sp-icon--white', __( 'Continue', 'sampreshan-child' ) ); ?>
                    <?php esc_html_e( 'Continue to sign in', 'sampreshan-child' ); ?>
                </a>
            <?php else : ?>
                <p class="sp-login__alt-row" style="justify-content:center">
                    <a class="sp-login__alt" href="<?php echo esc_url( $digits_login_url ); ?>">
                        <?php esc_html_e( 'Open full Digits login page', 'sampreshan-child' ); ?>
                    </a>
                </p>
            <?php endif; ?>

            <?php if ( is_user_logged_in() ) : ?>
                <p class="sp-login__alt-row" style="margin-top:1rem;text-align:center">
                    <a class="sp-login__alt" href="<?php echo esc_url( wp_logout_url( get_permalink() ) ); ?>">
                        <?php sp_icon_e( 'logout', 'sp-icon--sm sp-icon--muted', __( 'Sign out', 'sampreshan-child' ) ); ?>
                        <?php esc_html_e( 'Sign out', 'sampreshan-child' ); ?>
                    </a>
                </p>
            <?php endif; ?>
        </main>

    <?php else : ?>

        <!-- ======================================================
             FALLBACK MODE: custom Sampreshan login form.
             Used when Digits is not installed.
             ====================================================== -->
        <main class="sp-login__card glass" role="main">

            <div class="sp-login__art" aria-hidden="true">
                <?php sp_icon_e( 'lotus', 'sp-icon--3xl sp-icon--saffron sp-icon--float', __( 'Lotus', 'sampreshan-child' ) ); ?>
                <?php sp_icon_e( 'om',    'sp-icon--2xl sp-icon--gold sp-icon--pulse',   __( 'Om',    'sampreshan-child' ) ); ?>
                <?php sp_icon_e( 'diya',  'sp-icon--lg sp-icon--saffron sp-icon--bounce', __( 'Diya',  'sampreshan-child' ) ); ?>
            </div>

            <?php if ( $login_notice ) : ?>
                <div class="sp-login__notice" role="alert">
                    <?php sp_icon_e( 'warning', 'sp-icon--md sp-icon--warning', __( 'Notice', 'sampreshan-child' ) ); ?>
                    <span><?php echo esc_html( $login_notice ); ?></span>
                </div>
            <?php endif; ?>
            <?php if ( isset( $_GET['deleted'] ) && '1' === $_GET['deleted'] ) : ?>
                <div class="sp-login__notice" role="status">
                    <?php sp_icon_e( 'check', 'sp-icon--md sp-icon--success', __( 'Deleted', 'sampreshan-child' ) ); ?>
                    <span><?php esc_html_e( 'Your account has been deleted. Thank you for being part of SampreShan.', 'sampreshan-child' ); ?></span>
                </div>
            <?php endif; ?>

            <?php if ( 'login' === $action ) : ?>
                <header class="sp-login__head">
                    <?php sp_icon_e( 'lock', 'sp-icon--lg sp-icon--saffron', __( 'Sign in', 'sampreshan-child' ) ); ?>
                    <h1 class="sp-login__title display"><?php esc_html_e( 'Sign In', 'sampreshan-child' ); ?></h1>
                    <p class="sp-login__sub"><?php esc_html_e( 'Welcome back. Sign in to support causes, sign petitions, and follow champions.', 'sampreshan-child' ); ?></p>
                </header>

                <?php if ( ! empty( $_GET['login'] ) && 'failed' === $_GET['login'] ) : ?>
                    <div class="sp-login__error" role="alert">
                        <?php sp_icon_e( 'close', 'sp-icon--md sp-icon--danger', __( 'Error', 'sampreshan-child' ) ); ?>
                        <span><?php esc_html_e( 'Invalid username or password. Please try again.', 'sampreshan-child' ); ?></span>
                    </div>
                <?php elseif ( ! empty( $_GET['loggedout'] ) ) : ?>
                    <div class="sp-login__notice" role="status">
                        <?php sp_icon_e( 'check', 'sp-icon--md sp-icon--success', __( 'Signed out', 'sampreshan-child' ) ); ?>
                        <span><?php esc_html_e( 'You have been signed out.', 'sampreshan-child' ); ?></span>
                    </div>
                <?php endif; ?>

                <form class="sp-login__form" method="post" action="<?php echo esc_url( site_url( 'wp-login.php', 'login_post' ) ); ?>" novalidate>
                    <?php wp_nonce_field( 'sp_login_attempt', 'sp_login_nonce' ); ?>
                    <input type="hidden" name="redirect_to" value="<?php echo esc_attr( home_url( '/profile/' ) ); ?>" />

                    <label class="sp-login__field">
                        <span class="sp-login__label"><?php sp_icon_e( 'profile', 'sp-icon--sm sp-icon--muted', __( 'Username', 'sampreshan-child' ) ); ?><?php esc_html_e( 'Username or email', 'sampreshan-child' ); ?></span>
                        <input type="text" name="log" id="sp_user_login" class="input" required autocomplete="username" autofocus />
                    </label>

                    <label class="sp-login__field">
                        <span class="sp-login__label"><?php sp_icon_e( 'lock', 'sp-icon--sm sp-icon--muted', __( 'Password', 'sampreshan-child' ) ); ?><?php esc_html_e( 'Password', 'sampreshan-child' ); ?></span>
                        <input type="password" name="pwd" id="sp_user_pass" class="input" required autocomplete="current-password" />
                    </label>

                    <label class="sp-login__remember">
                        <input type="checkbox" name="rememberme" value="forever" />
                        <?php esc_html_e( 'Keep me signed in', 'sampreshan-child' ); ?>
                    </label>

                    <button type="submit" name="wp-submit" class="sp-login__submit btn-3d btn-3d--lg">
                        <?php sp_icon_e( 'check', 'sp-icon--sm sp-icon--white', __( 'Sign in', 'sampreshan-child' ) ); ?>
                        <?php esc_html_e( 'Sign In', 'sampreshan-child' ); ?>
                    </button>

                    <?php do_action( 'sp_login_form_below' ); ?>

                </form>

                <div class="sp-login__divider">
                    <span><?php esc_html_e( 'OR', 'sampreshan-child' ); ?></span>
                </div>

                <!-- Firebase OTP Phone Login (sibling form — never nested) -->
                <div class="sp-firebase-otp-section">
                    <h2 class="sp-login__sub-title"><?php esc_html_e( 'Sign in with Mobile OTP', 'sampreshan-child' ); ?></h2>
                    <p class="sp-login__sub" style="font-size: 13px; margin-bottom: 16px;"><?php esc_html_e( 'No password needed. Get a one-time code on your phone.', 'sampreshan-child' ); ?></p>

                    <div class="sp-notice-container"></div>

                    <form class="sp-firebase-phone-form sp-login__form" novalidate>
                            <div class="sp-phone-section">
                                <label class="sp-login__field">
                                    <span class="sp-login__label">
                                        <?php sp_icon_auto( 'phone', 'sp-icon--sm sp-icon--muted', __( 'Phone', 'sampreshan-child' ) ); ?>
                                        <?php esc_html_e( 'Mobile number', 'sampreshan-child' ); ?>
                                    </span>
                                    <div class="sp-phone-input-group">
                                        <span class="sp-phone-prefix">+91</span>
                                        <input type="tel" name="phone" class="input sp-phone-input" required inputmode="tel" autocomplete="tel" placeholder="98765 43210" maxlength="12" />
                                    </div>
                                </label>
                                <button type="submit" class="sp-otp-send-btn btn-3d btn-3d--lg btn-3d--block">
                                    <?php sp_icon_e( 'mail', 'sp-icon--sm sp-icon--white', __( 'Send OTP', 'sampreshan-child' ) ); ?>
                                    <?php esc_html_e( 'Send OTP', 'sampreshan-child' ); ?>
                                </button>
                            </div>

                            <div class="sp-otp-section" style="display:none;">
                                <div class="sp-otp-phone-display"></div>
                                <label class="sp-login__field">
                                    <span class="sp-login__label">
                                        <?php sp_icon_e( 'verified', 'sp-icon--sm sp-icon--muted', __( 'OTP', 'sampreshan-child' ) ); ?>
                                        <?php esc_html_e( 'Enter 6-digit OTP', 'sampreshan-child' ); ?>
                                    </span>
                                    <input type="text" name="otp" class="input sp-otp-input" required inputmode="numeric" pattern="[0-9]{6}" maxlength="6" autocomplete="one-time-code" placeholder="000000" />
                                </label>
                                <button type="submit" class="sp-otp-verify-btn btn-3d btn-3d--lg btn-3d--block">
                                    <?php sp_icon_e( 'check', 'sp-icon--sm sp-icon--white', __( 'Verify', 'sampreshan-child' ) ); ?>
                                    <?php esc_html_e( 'Verify & Login', 'sampreshan-child' ); ?>
                                </button>
                                <p class="sp-otp-resend" style="text-align:center; margin-top:12px;">
                                    <a href="#" class="sp-login__alt" style="font-size:13px;">
                                        <?php esc_html_e( 'Resend OTP', 'sampreshan-child' ); ?>
                                    </a>
                                </p>
                            </div>
                    </form>
                </div>

                <div class="sp-login__alt-row">
                    <a class="sp-login__alt" href="<?php echo esc_url( add_query_arg( 'action', 'register', get_permalink() ) ); ?>">
                        <?php sp_icon_e( 'plus', 'sp-icon--sm sp-icon--saffron', __( 'Register', 'sampreshan-child' ) ); ?>
                        <?php esc_html_e( 'New here? Create an account', 'sampreshan-child' ); ?>
                    </a>
                    <a class="sp-login__alt" href="<?php echo esc_url( add_query_arg( 'action', 'lostpassword', get_permalink() ) ); ?>">
                        <?php sp_icon_e( 'lock', 'sp-icon--sm sp-icon--muted', __( 'Reset', 'sampreshan-child' ) ); ?>
                        <?php esc_html_e( 'Forgot password', 'sampreshan-child' ); ?>
                    </a>
                </div>

            <?php elseif ( 'register' === $action ) :
                if ( ! get_option( 'users_can_register' ) ) {
                    echo '<p>' . esc_html__( 'Registration is currently disabled.', 'sampreshan-child' ) . '</p>';
                } else {
            ?>
                <header class="sp-login__head">
                    <?php sp_icon_e( 'plus', 'sp-icon--lg sp-icon--saffron', __( 'Join', 'sampreshan-child' ) ); ?>
                    <h1 class="sp-login__title display"><?php esc_html_e( 'Join SampreShan', 'sampreshan-child' ); ?></h1>
                    <p class="sp-login__sub"><?php esc_html_e( 'Create your Sanatan profile, sign petitions, and champion causes that matter.', 'sampreshan-child' ); ?></p>
                </header>

                <form class="sp-login__form" method="post" action="<?php echo esc_url( site_url( 'wp-login.php?action=register', 'login_post' ) ); ?>" novalidate>
                    <?php wp_nonce_field( 'sp_register_attempt', 'sp_register_nonce' ); ?>

                    <label class="sp-login__field">
                        <span class="sp-login__label"><?php sp_icon_e( 'profile', 'sp-icon--sm sp-icon--muted', __( 'Username', 'sampreshan-child' ) ); ?><?php esc_html_e( 'Username', 'sampreshan-child' ); ?></span>
                        <input type="text" name="user_login" class="input" required autocomplete="username" />
                    </label>

                    <label class="sp-login__field">
                        <span class="sp-login__label"><?php sp_icon_e( 'mail', 'sp-icon--sm sp-icon--muted', __( 'Email', 'sampreshan-child' ) ); ?><?php esc_html_e( 'Email', 'sampreshan-child' ); ?></span>
                        <input type="email" name="user_email" class="input" required autocomplete="email" />
                    </label>

                    <?php do_action( 'register_form' ); ?>

                    <button type="submit" name="wp-submit" class="sp-login__submit btn-3d btn-3d--lg">
                        <?php sp_icon_e( 'check', 'sp-icon--sm sp-icon--white', __( 'Create', 'sampreshan-child' ) ); ?>
                        <?php esc_html_e( 'Create Account', 'sampreshan-child' ); ?>
                    </button>

                    <div class="sp-login__alt-row">
                        <a class="sp-login__alt" href="<?php echo esc_url( remove_query_arg( 'action' ) ); ?>">
                            <?php sp_icon_e( 'check', 'sp-icon--sm sp-icon--saffron', __( 'Sign in', 'sampreshan-child' ) ); ?>
                            <?php esc_html_e( 'Already have an account? Sign in', 'sampreshan-child' ); ?>
                        </a>
                    </div>
                </form>
            <?php } ?>

            <?php elseif ( 'lostpassword' === $action ) : ?>
                <header class="sp-login__head">
                    <?php sp_icon_e( 'lock', 'sp-icon--lg sp-icon--saffron', __( 'Reset', 'sampreshan-child' ) ); ?>
                    <h1 class="sp-login__title display"><?php esc_html_e( 'Reset Password', 'sampreshan-child' ); ?></h1>
                    <p class="sp-login__sub"><?php esc_html_e( 'Enter your email and we will send you a link to set a new password.', 'sampreshan-child' ); ?></p>
                </header>

                <form class="sp-login__form" method="post" action="<?php echo esc_url( site_url( 'wp-login.php?action=lostpassword', 'login_post' ) ); ?>" novalidate>
                    <?php wp_nonce_field( 'sp_lostpassword', 'sp_lostpassword_nonce' ); ?>

                    <label class="sp-login__field">
                        <span class="sp-login__label"><?php sp_icon_e( 'mail', 'sp-icon--sm sp-icon--muted', __( 'Email', 'sampreshan-child' ) ); ?><?php esc_html_e( 'Email or username', 'sampreshan-child' ); ?></span>
                        <input type="text" name="user_login" class="input" required autocomplete="username" />
                    </label>

                    <button type="submit" name="wp-submit" class="sp-login__submit btn-3d btn-3d--lg">
                        <?php sp_icon_e( 'mail', 'sp-icon--sm sp-icon--white', __( 'Send', 'sampreshan-child' ) ); ?>
                        <?php esc_html_e( 'Send Reset Link', 'sampreshan-child' ); ?>
                    </button>

                    <div class="sp-login__alt-row">
                        <a class="sp-login__alt" href="<?php echo esc_url( remove_query_arg( 'action' ) ); ?>">
                            <?php sp_icon_e( 'check', 'sp-icon--sm sp-icon--saffron', __( 'Sign in', 'sampreshan-child' ) ); ?>
                            <?php esc_html_e( 'Back to sign in', 'sampreshan-child' ); ?>
                        </a>
                    </div>
                </form>
            <?php endif; ?>

        </main>
    <?php endif; ?>

    <footer class="sp-login__footer">
        <p>
            <?php sp_icon_e( 'dharma', 'sp-icon--sm sp-icon--saffron', __( 'SampreShan', 'sampreshan-child' ) ); ?>
            <?php
            printf(
                /* translators: %s = site name */
                esc_html__( '%s — a non-commercial initiative of ShivBodh Trust.', 'sampreshan-child' ),
                '<strong>' . esc_html( get_bloginfo( 'name' ) ) . '</strong>'
            );
            ?>
        </p>
    </footer>

</div>

<?php wp_footer(); ?>
</body>
</html>
