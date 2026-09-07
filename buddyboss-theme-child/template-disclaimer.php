<?php
/**
 * Template Name: Disclaimer
 *
 * Legal disclaimer for SampreShan platform.
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
            <?php sp_icon_auto( 'warning', 'sp-icon--3xl sp-icon--saffron sp-icon--float', __( 'Disclaimer', 'sampreshan-child' ) ); ?>
        </div>
        <h1 class="sp-page__hero-title"><?php esc_html_e( 'Disclaimer', 'sampreshan-child' ); ?></h1>
        <p class="sp-page__hero-sub">
            <?php esc_html_e( 'Important information about the SampreShan platform and its limitations.', 'sampreshan-child' ); ?>
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
                <?php sp_icon_auto( 'info', 'sp-icon--md sp-icon--saffron', __( 'General', 'sampreshan-child' ) ); ?>
                <?php esc_html_e( 'General Disclaimer', 'sampreshan-child' ); ?>
            </h2>
            <div class="sp-page__content">
                <p><?php esc_html_e( 'The information provided on SampreShan (sampreshan.tech) is for general informational and community awareness purposes only. While we strive to keep the information accurate and up-to-date, we make no representations or warranties of any kind about the completeness, accuracy, reliability, or availability of the platform or the information contained on it.', 'sampreshan-child' ); ?></p>
            </div>
        </section>

        <section class="sp-page__card">
            <h2 class="sp-page__section-title">
                <?php sp_icon_auto( 'petition', 'sp-icon--md sp-icon--saffron', __( 'Petitions', 'sampreshan-child' ) ); ?>
                <?php esc_html_e( 'Petition Disclaimer', 'sampreshan-child' ); ?>
            </h2>
            <div class="sp-page__content">
                <p><?php esc_html_e( 'Petitions on SampreShan are created by community members and represent their individual views and concerns. The views expressed in petitions do not necessarily reflect the views of ShivBodh Trust or SampreShan administrators.', 'sampreshan-child' ); ?></p>
                <p><?php esc_html_e( 'While we verify petitions for compliance with our Community Guidelines, we do not independently verify all claims made in petitions. Signers should exercise their own judgment before supporting any cause.', 'sampreshan-child' ); ?></p>
                <p><?php esc_html_e( 'Petition outcomes depend on various factors including but not limited to community support, relevant authorities, and legal frameworks. We do not guarantee any specific outcomes from petition campaigns.', 'sampreshan-child' ); ?></p>
            </div>
        </section>

        <section class="sp-page__card">
            <h2 class="sp-page__section-title">
                <?php sp_icon_auto( 'network', 'sp-icon--md sp-icon--saffron', __( 'External Links', 'sampreshan-child' ) ); ?>
                <?php esc_html_e( 'External Links', 'sampreshan-child' ); ?>
            </h2>
            <div class="sp-page__content">
                <p><?php esc_html_e( 'SampreShan may contain links to external websites or third-party content. These links are provided for convenience and informational purposes only. We do not endorse or assume responsibility for the content, privacy policies, or practices of any third-party sites.', 'sampreshan-child' ); ?></p>
            </div>
        </section>

        <section class="sp-page__card">
            <h2 class="sp-page__section-title">
                <?php sp_icon_auto( 'lock', 'sp-icon--md sp-icon--blue', __( 'Professional Advice', 'sampreshan-child' ) ); ?>
                <?php esc_html_e( 'Professional Advice', 'sampreshan-child' ); ?>
            </h2>
            <div class="sp-page__content">
                <p><?php esc_html_e( 'Nothing on SampreShan constitutes professional advice of any kind, including but not limited to legal, financial, medical, or spiritual advice. Any reliance you place on information from this platform is strictly at your own risk.', 'sampreshan-child' ); ?></p>
            </div>
        </section>

        <section class="sp-page__card">
            <h2 class="sp-page__section-title">
                <?php sp_icon_auto( 'heart', 'sp-icon--md sp-icon--saffron', __( 'Non-Commercial', 'sampreshan-child' ) ); ?>
                <?php esc_html_e( 'Non-Commercial Nature', 'sampreshan-child' ); ?>
            </h2>
            <div class="sp-page__content">
                <p><?php esc_html_e( 'SampreShan is a strictly non-commercial platform. No financial transactions, donations, or commercial activities are facilitated through this platform. Any attempt to use the platform for commercial purposes is a violation of our Terms & Conditions.', 'sampreshan-child' ); ?></p>
            </div>
        </section>

        <section class="sp-page__card">
            <h2 class="sp-page__section-title">
                <?php sp_icon_auto( 'verified', 'sp-icon--md sp-icon--saffron', __( 'Accuracy', 'sampreshan-child' ) ); ?>
                <?php esc_html_e( 'Content Accuracy', 'sampreshan-child' ); ?>
            </h2>
            <div class="sp-page__content">
                <p><?php esc_html_e( 'User-generated content on SampreShan, including posts, comments, and petition descriptions, represents the views of individual users. ShivBodh Trust and SampreShan administrators do not verify the accuracy of all user-generated content and are not responsible for any inaccuracies.', 'sampreshan-child' ); ?></p>
            </div>
        </section>

        <section class="sp-page__card">
            <h2 class="sp-page__section-title">
                <?php sp_icon_auto( 'mail', 'sp-icon--md sp-icon--saffron', __( 'Contact', 'sampreshan-child' ) ); ?>
                <?php esc_html_e( 'Contact Us', 'sampreshan-child' ); ?>
            </h2>
            <div class="sp-page__content">
                <p><?php esc_html_e( 'If you have any questions about this Disclaimer, please contact us:', 'sampreshan-child' ); ?></p>
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
