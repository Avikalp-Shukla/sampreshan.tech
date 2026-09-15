<?php
/**
 * Template Name: Privacy Policy
 *
 * Privacy policy for SampreShan platform.
 *
 * @package SampreShan_Child
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

get_header();
?>

<main id="main" class="site-main sp-page" role="main">

    <!-- HERO -->
    <header class="sp-page__hero">
        <div class="sp-page__hero-icon">
            <?php sp_icon_auto( 'lock', 'sp-icon--3xl sp-icon--saffron sp-icon--float', __( 'Privacy', 'sampreshan-child' ) ); ?>
        </div>
        <h1 class="sp-page__hero-title"><?php esc_html_e( 'Privacy Policy', 'sampreshan-child' ); ?></h1>
        <p class="sp-page__hero-sub">
            <?php esc_html_e( 'Your privacy matters to us. This policy explains how we collect, use, and protect your information on SampreShan.', 'sampreshan-child' ); ?>
        </p>
        <p class="sp-page__hero-sub" style="font-size: var(--text-xs, 0.75rem); margin-top: 0.5rem; opacity: 0.7;">
            <?php
            printf(
                /* translators: %s = date */
                esc_html__( 'Last updated: %s', 'sampreshan-child' ),
                esc_html( date_i18n( 'F j, Y' ) )
            );
            ?>
        </p>
    </header>

    <div class="sp-page__body">

        <section class="sp-page__card">
            <h2 class="sp-page__section-title">
                <?php sp_icon_auto( 'info', 'sp-icon--md sp-icon--saffron', __( 'Information', 'sampreshan-child' ) ); ?>
                <?php esc_html_e( 'Information We Collect', 'sampreshan-child' ); ?>
            </h2>
            <div class="sp-page__content">
                <p><?php esc_html_e( 'When you use SampreShan, we may collect the following types of information:', 'sampreshan-child' ); ?></p>
                <ul>
                    <li><strong><?php esc_html_e( 'Account Information:', 'sampreshan-child' ); ?></strong> <?php esc_html_e( 'Name, email address, phone number, and profile details you provide during registration.', 'sampreshan-child' ); ?></li>
                    <li><strong><?php esc_html_e( 'Usage Data:', 'sampreshan-child' ); ?></strong> <?php esc_html_e( 'Pages visited, actions taken, petitions signed, and interaction patterns on the platform.', 'sampreshan-child' ); ?></li>
                    <li><strong><?php esc_html_e( 'Device Information:', 'sampreshan-child' ); ?></strong> <?php esc_html_e( 'Browser type, operating system, device type, and IP address for security and optimization purposes.', 'sampreshan-child' ); ?></li>
                    <li><strong><?php esc_html_e( 'Cookies:', 'sampreshan-child' ); ?></strong> <?php esc_html_e( 'Essential cookies required for platform functionality and session management.', 'sampreshan-child' ); ?></li>
                </ul>
            </div>
        </section>

        <section class="sp-page__card">
            <h2 class="sp-page__section-title">
                <?php sp_icon_auto( 'verified', 'sp-icon--md sp-icon--saffron', __( 'Use', 'sampreshan-child' ) ); ?>
                <?php esc_html_e( 'How We Use Your Information', 'sampreshan-child' ); ?>
            </h2>
            <div class="sp-page__content">
                <p><?php esc_html_e( 'We use the information we collect to:', 'sampreshan-child' ); ?></p>
                <ul>
                    <li><?php esc_html_e( 'Provide and maintain the SampreShan platform', 'sampreshan-child' ); ?></li>
                    <li><?php esc_html_e( 'Process petition signatures and community interactions', 'sampreshan-child' ); ?></li>
                    <li><?php esc_html_e( 'Send important updates about your petitions and account', 'sampreshan-child' ); ?></li>
                    <li><?php esc_html_e( 'Improve platform performance and user experience', 'sampreshan-child' ); ?></li>
                    <li><?php esc_html_e( 'Ensure security and prevent misuse of the platform', 'sampreshan-child' ); ?></li>
                    <li><?php esc_html_e( 'Comply with legal obligations', 'sampreshan-child' ); ?></li>
                </ul>
            </div>
        </section>

        <section class="sp-page__card">
            <h2 class="sp-page__section-title">
                <?php sp_icon_auto( 'heart', 'sp-icon--md sp-icon--saffron', __( 'Sharing', 'sampreshan-child' ) ); ?>
                <?php esc_html_e( 'Information Sharing', 'sampreshan-child' ); ?>
            </h2>
            <div class="sp-page__content">
                <p><?php esc_html_e( 'We do not sell, trade, or rent your personal information to third parties. We may share your information only in the following circumstances:', 'sampreshan-child' ); ?></p>
                <ul>
                    <li><?php esc_html_e( 'With your explicit consent', 'sampreshan-child' ); ?></li>
                    <li><?php esc_html_e( 'To comply with legal requirements or court orders', 'sampreshan-child' ); ?></li>
                    <li><?php esc_html_e( 'To protect the rights, safety, or property of the community', 'sampreshan-child' ); ?></li>
                    <li><?php esc_html_e( 'With service providers who assist in platform operations (under strict confidentiality agreements)', 'sampreshan-child' ); ?></li>
                </ul>
            </div>
        </section>

        <section class="sp-page__card">
            <h2 class="sp-page__section-title">
                <?php sp_icon_auto( 'lock', 'sp-icon--md sp-icon--blue', __( 'Security', 'sampreshan-child' ) ); ?>
                <?php esc_html_e( 'Data Security', 'sampreshan-child' ); ?>
            </h2>
            <div class="sp-page__content">
                <p><?php esc_html_e( 'We implement industry-standard security measures to protect your personal information, including:', 'sampreshan-child' ); ?></p>
                <ul>
                    <li><?php esc_html_e( 'SSL/TLS encryption for data transmission', 'sampreshan-child' ); ?></li>
                    <li><?php esc_html_e( 'Secure authentication via Firebase OTP', 'sampreshan-child' ); ?></li>
                    <li><?php esc_html_e( 'Regular security audits and updates', 'sampreshan-child' ); ?></li>
                    <li><?php esc_html_e( 'Limited access to personal data by authorized personnel only', 'sampreshan-child' ); ?></li>
                </ul>
            </div>
        </section>

        <section class="sp-page__card">
            <h2 class="sp-page__section-title">
                <?php sp_icon_auto( 'pen', 'sp-icon--md sp-icon--saffron', __( 'Rights', 'sampreshan-child' ) ); ?>
                <?php esc_html_e( 'Your Rights', 'sampreshan-child' ); ?>
            </h2>
            <div class="sp-page__content">
                <p><?php esc_html_e( 'You have the right to:', 'sampreshan-child' ); ?></p>
                <ul>
                    <li><?php esc_html_e( 'Access the personal data we hold about you', 'sampreshan-child' ); ?></li>
                    <li><?php esc_html_e( 'Request correction of inaccurate data', 'sampreshan-child' ); ?></li>
                    <li><?php esc_html_e( 'Request deletion of your account and associated data', 'sampreshan-child' ); ?></li>
                    <li><?php esc_html_e( 'Opt out of non-essential communications', 'sampreshan-child' ); ?></li>
                </ul>
                <p>
                    <?php esc_html_e( 'To exercise any of these rights, contact us at', 'sampreshan-child' ); ?>
                    <a href="mailto:info@sampreshan.tech">info@sampreshan.tech</a>.
                </p>
            </div>
        </section>

        <section class="sp-page__card">
            <h2 class="sp-page__section-title">
                <?php sp_icon_auto( 'mail', 'sp-icon--md sp-icon--saffron', __( 'Contact', 'sampreshan-child' ) ); ?>
                <?php esc_html_e( 'Contact Us', 'sampreshan-child' ); ?>
            </h2>
            <div class="sp-page__content">
                <p><?php esc_html_e( 'If you have any questions about this Privacy Policy, please contact us:', 'sampreshan-child' ); ?></p>
                <p>
                    <strong><?php esc_html_e( 'ShivBodh Trust', 'sampreshan-child' ); ?></strong><br>
                    <?php esc_html_e( 'Email:', 'sampreshan-child' ); ?> <a href="mailto:info@sampreshan.tech">info@sampreshan.tech</a><br>
                    <?php esc_html_e( 'Website:', 'sampreshan-child' ); ?> <a href="https://shivbodhtrust.org" target="_blank" rel="noopener">shivbodhtrust.org</a>
                </p>
            </div>
        </section>

    </div>
</main>

<?php
$footer_tpl = get_stylesheet_directory() . '/template-parts/footer/site-footer.php';
if ( file_exists( $footer_tpl ) ) {
    include $footer_tpl;
} else {
    get_footer();
}
