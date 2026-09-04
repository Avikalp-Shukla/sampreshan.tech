<?php
/**
 * Template Name: Terms & Conditions
 *
 * Terms of service for SampreShan platform.
 *
 * @package SampreShan_Child
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

get_header();
?>

<main class="site-main sp-page" role="main">

    <!-- HERO -->
    <header class="sp-page__hero">
        <div class="sp-page__hero-icon">
            <?php sp_icon_e( 'pen', 'sp-icon--3xl sp-icon--saffron sp-icon--float', __( 'Terms', 'sampreshan-child' ) ); ?>
        </div>
        <h1 class="sp-page__hero-title"><?php esc_html_e( 'Terms & Conditions', 'sampreshan-child' ); ?></h1>
        <p class="sp-page__hero-sub">
            <?php esc_html_e( 'Please read these terms carefully before using SampreShan. By using the platform, you agree to these terms.', 'sampreshan-child' ); ?>
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
                <?php sp_icon_e( 'info', 'sp-icon--md sp-icon--saffron', __( 'Acceptance', 'sampreshan-child' ) ); ?>
                <?php esc_html_e( 'Acceptance of Terms', 'sampreshan-child' ); ?>
            </h2>
            <div class="sp-page__content">
                <p><?php esc_html_e( 'By accessing or using SampreShan (sampreshan.tech), you agree to be bound by these Terms and Conditions. If you do not agree with any part of these terms, please do not use the platform.', 'sampreshan-child' ); ?></p>
            </div>
        </section>

        <section class="sp-page__card">
            <h2 class="sp-page__section-title">
                <?php sp_icon_e( 'info', 'sp-icon--md sp-icon--saffron', __( 'Platform', 'sampreshan-child' ) ); ?>
                <?php esc_html_e( 'Platform Description', 'sampreshan-child' ); ?>
            </h2>
            <div class="sp-page__content">
                <p><?php esc_html_e( 'SampreShan is a non-commercial digital platform operated by ShivBodh Trust. It serves as a community awareness platform for Sanatana Dharma followers to:', 'sampreshan-child' ); ?></p>
                <ul>
                    <li><?php esc_html_e( 'Start and sign petitions on social, cultural, and religious issues', 'sampreshan-child' ); ?></li>
                    <li><?php esc_html_e( 'Connect with fellow community members', 'sampreshan-child' ); ?></li>
                    <li><?php esc_html_e( 'Share perspectives and discuss Dharmic topics', 'sampreshan-child' ); ?></li>
                    <li><?php esc_html_e( 'Learn about various sampradayas and spiritual traditions', 'sampreshan-child' ); ?></li>
                </ul>
            </div>
        </section>

        <section class="sp-page__card">
            <h2 class="sp-page__section-title">
                <?php sp_icon_e( 'lock', 'sp-icon--md sp-icon--blue', __( 'Account', 'sampreshan-child' ); ?>
                <?php esc_html_e( 'User Accounts', 'sampreshan-child' ); ?>
            </h2>
            <div class="sp-page__content">
                <p><?php esc_html_e( 'To use certain features, you must create an account. When creating an account:', 'sampreshan-child' ); ?></p>
                <ul>
                    <li><?php esc_html_e( 'You must provide accurate and complete information', 'sampreshan-child' ); ?></li>
                    <li><?php esc_html_e( 'You are responsible for maintaining the security of your account', 'sampreshan-child' ); ?></li>
                    <li><?php esc_html_e( 'You must be at least 13 years of age to create an account', 'sampreshan-child' ); ?></li>
                    <li><?php esc_html_e( 'One person may maintain only one account', 'sampreshan-child' ); ?></li>
                    <li><?php esc_html_e( 'You agree to notify us immediately of any unauthorized use', 'sampreshan-child' ); ?></li>
                </ul>
            </div>
        </section>

        <section class="sp-page__card">
            <h2 class="sp-page__section-title">
                <?php sp_icon_e( 'petition', 'sp-icon--md sp-icon--saffron', __( 'Petitions', 'sampreshan-child' ) ); ?>
                <?php esc_html_e( 'Petitions & Content', 'sampreshan-child' ); ?>
            </h2>
            <div class="sp-page__content">
                <p><?php esc_html_e( 'When you create a petition or post content on SampreShan:', 'sampreshan-child' ); ?></p>
                <ul>
                    <li><?php esc_html_e( 'You retain ownership of your original content', 'sampreshan-child' ); ?></li>
                    <li><?php esc_html_e( 'You grant SampreShan a non-exclusive license to display, distribute, and promote your content on the platform', 'sampreshan-child' ); ?></li>
                    <li><?php esc_html_e( 'Your content must be truthful, accurate, and not misleading', 'sampreshan-child' ); ?></li>
                    <li><?php esc_html_e( 'Your content must not infringe on the rights of others', 'sampreshan-child' ); ?></li>
                    <li><?php esc_html_e( 'We reserve the right to remove content that violates our Community Guidelines', 'sampreshan-child' ); ?></li>
                </ul>
            </div>
        </section>

        <section class="sp-page__card">
            <h2 class="sp-page__section-title">
                <?php sp_icon_e( 'heart', 'sp-icon--md sp-icon--saffron', __( 'Conduct', 'sampreshan-child' ) ); ?>
                <?php esc_html_e( 'Prohibited Conduct', 'sampreshan-child' ); ?>
            </h2>
            <div class="sp-page__content">
                <p><?php esc_html_e( 'You agree not to:', 'sampreshan-child' ); ?></p>
                <ul>
                    <li><?php esc_html_e( 'Use the platform for any unlawful purpose', 'sampreshan-child' ); ?></li>
                    <li><?php esc_html_e( 'Impersonate another person or entity', 'sampreshan-child' ); ?></li>
                    <li><?php esc_html_e( 'Attempt to gain unauthorized access to other accounts or systems', 'sampreshan-child' ); ?></li>
                    <li><?php esc_html_e( 'Use automated tools to interact with the platform', 'sampreshan-child' ); ?></li>
                    <li><?php esc_html_e( 'Transmit spam, chain letters, or unsolicited communications', 'sampreshan-child' ); ?></li>
                    <li><?php esc_html_e( 'Engage in commercial activities or solicitation', 'sampreshan-child' ); ?></li>
                    <li><?php esc_html_e( 'Upload malicious code or harmful content', 'sampreshan-child' ); ?></li>
                </ul>
            </div>
        </section>

        <section class="sp-page__card">
            <h2 class="sp-page__section-title">
                <?php sp_icon_e( 'warning', 'sp-icon--md sp-icon--warning', __( 'Liability', 'sampreshan-child' ) ); ?>
                <?php esc_html_e( 'Limitation of Liability', 'sampreshan-child' ); ?>
            </h2>
            <div class="sp-page__content">
                <p><?php esc_html_e( 'SampreShan is provided "as is" without warranties of any kind. ShivBodh Trust shall not be liable for:', 'sampreshan-child' ); ?></p>
                <ul>
                    <li><?php esc_html_e( 'Any indirect, incidental, or consequential damages', 'sampreshan-child' ); ?></li>
                    <li><?php esc_html_e( 'Loss of data or interruption of service', 'sampreshan-child' ); ?></li>
                    <li><?php esc_html_e( 'Actions taken based on content found on the platform', 'sampreshan-child' ); ?></li>
                    <li><?php esc_html_e( 'Third-party links or content accessed through the platform', 'sampreshan-child' ); ?></li>
                </ul>
            </div>
        </section>

        <section class="sp-page__card">
            <h2 class="sp-page__section-title">
                <?php sp_icon_e( 'lock', 'sp-icon--md sp-icon--blue', __( 'Termination', 'sampreshan-child' ); ?>
                <?php esc_html_e( 'Termination', 'sampreshan-child' ); ?>
            </h2>
            <div class="sp-page__content">
                <p><?php esc_html_e( 'We reserve the right to suspend or terminate your account at any time, without prior notice, for conduct that violates these terms or is harmful to the community.', 'sampreshan-child' ); ?></p>
                <p><?php esc_html_e( 'You may delete your account at any time by contacting us at info@sampreshan.tech.', 'sampreshan-child' ); ?></p>
            </div>
        </section>

        <section class="sp-page__card">
            <h2 class="sp-page__section-title">
                <?php sp_icon_e( 'mail', 'sp-icon--md sp-icon--saffron', __( 'Contact', 'sampreshan-child' ) ); ?>
                <?php esc_html_e( 'Contact Us', 'sampreshan-child' ); ?>
            </h2>
            <div class="sp-page__content">
                <p><?php esc_html_e( 'For questions about these Terms & Conditions:', 'sampreshan-child' ); ?></p>
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
