<?php
/**
 * Template Name: About
 *
 * About SampreShan — mission, vision, ShivBodh Trust, team, timeline.
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
            <?php sp_icon_e( 'dharma', 'sp-icon--3xl sp-icon--saffron sp-icon--float', __( 'SampreShan', 'sampreshan-child' ) ); ?>
        </div>
        <h1 class="sp-page__hero-title"><?php esc_html_e( 'About SampreShan', 'sampreshan-child' ); ?></h1>
        <p class="sp-page__hero-sub">
            <?php esc_html_e( 'A non-commercial digital platform by ShivBodh Trust — giving the Sanatana Dharma community a voice that travels farther, together.', 'sampreshan-child' ); ?>
        </p>
    </header>

    <div class="sp-page__body">

        <!-- MISSION -->
        <section class="sp-page__card">
            <h2 class="sp-page__section-title">
                <?php sp_icon_e( 'heart', 'sp-icon--md sp-icon--saffron', __( 'Mission', 'sampreshan-child' ) ); ?>
                <?php esc_html_e( 'Our Mission', 'sampreshan-child' ); ?>
            </h2>
            <div class="sp-page__content">
                <p>
                    <?php esc_html_e( 'SampreShan exists to unify the voices of Sanatana Dharma followers across the world. We believe that every community issue — whether it concerns a local temple, cultural heritage, religious education, or environmental protection — deserves to be heard, discussed, and acted upon collectively.', 'sampreshan-child' ); ?>
                </p>
                <p>
                    <?php esc_html_e( 'Our platform enables individuals to raise petitions, gather signatures, and build awareness around causes that matter to the Dharmic community. No donations. No membership fees. No financial transactions. Purely a community-driven awareness platform.', 'sampreshan-child' ); ?>
                </p>
            </div>
        </section>

        <!-- VISION -->
        <section class="sp-page__card">
            <h2 class="sp-page__section-title">
                <?php sp_icon_e( 'star', 'sp-icon--md sp-icon--gold', __( 'Vision', 'sampreshan-child' ) ); ?>
                <?php esc_html_e( 'Our Vision', 'sampreshan-child' ); ?>
            </h2>
            <div class="sp-page__content">
                <p>
                    <?php esc_html_e( 'We envision a world where every Sanatana Dharma follower — regardless of their sampradaya, region, or language — has access to a trusted platform to voice concerns, seek support, and connect with like-minded individuals who share their commitment to Dharma.', 'sampreshan-child' ); ?>
                </p>
                <p>
                    <?php esc_html_e( 'One Dharma. Many paths. All traditions respected equally.', 'sampreshan-child' ); ?>
                </p>
            </div>
        </section>

        <!-- WHAT WE DO -->
        <section class="sp-page__card">
            <h2 class="sp-page__section-title">
                <?php sp_icon_e( 'petition', 'sp-icon--md sp-icon--saffron', __( 'What We Do', 'sampreshan-child' ) ); ?>
                <?php esc_html_e( 'What We Do', 'sampreshan-child' ); ?>
            </h2>
            <div class="sp-page__features">
                <div class="sp-feature-card">
                    <div class="sp-feature-card__icon">
                        <?php sp_icon_e( 'petition', 'sp-icon--2xl sp-icon--saffron', __( 'Petitions', 'sampreshan-child' ) ); ?>
                    </div>
                    <h3 class="sp-feature-card__title"><?php esc_html_e( 'Raise Petitions', 'sampreshan-child' ); ?></h3>
                    <p class="sp-feature-card__desc"><?php esc_html_e( 'Start a petition on any social, religious, or cultural issue affecting the Dharmic community. Bring local problems to a wider audience.', 'sampreshan-child' ); ?></p>
                </div>
                <div class="sp-feature-card">
                    <div class="sp-feature-card__icon">
                        <?php sp_icon_e( 'heart', 'sp-icon--2xl sp-icon--saffron', __( 'Support', 'sampreshan-child' ) ); ?>
                    </div>
                    <h3 class="sp-feature-card__title"><?php esc_html_e( 'Gather Support', 'sampreshan-child' ); ?></h3>
                    <p class="sp-feature-card__desc"><?php esc_html_e( 'Collect signatures and build momentum. Every signature amplifies your voice and brings you closer to collective impact.', 'sampreshan-child' ); ?></p>
                </div>
                <div class="sp-feature-card">
                    <div class="sp-feature-card__icon">
                        <?php sp_icon_e( 'network', 'sp-icon--2xl sp-icon--saffron', __( 'Connect', 'sampreshan-child' ) ); ?>
                    </div>
                    <h3 class="sp-feature-card__title"><?php esc_html_e( 'Connect & Discuss', 'sampreshan-child' ); ?></h3>
                    <p class="sp-feature-card__desc"><?php esc_html_e( 'Join a community of like-minded Sanatana Dharma followers. Share perspectives, discuss issues, and build meaningful connections.', 'sampreshan-child' ); ?></p>
                </div>
                <div class="sp-feature-card">
                    <div class="sp-feature-card__icon">
                        <?php sp_icon_e( 'om', 'sp-icon--2xl sp-icon--saffron', __( 'Awareness', 'sampreshan-child' ) ); ?>
                    </div>
                    <h3 class="sp-feature-card__title"><?php esc_html_e( 'Acharya Awareness', 'sampreshan-child' ); ?></h3>
                    <p class="sp-feature-card__desc"><?php esc_html_e( 'Learn about the four sacred peethas, various sampradayas, and the wisdom of traditional spiritual institutions.', 'sampreshan-child' ); ?></p>
                </div>
            </div>
        </section>

        <!-- SHIVBODH TRUST -->
        <section class="sp-page__card">
            <h2 class="sp-page__section-title">
                <?php sp_icon_e( 'verified', 'sp-icon--md sp-icon--saffron', __( 'Trust', 'sampreshan-child' ) ); ?>
                <?php esc_html_e( 'About ShivBodh Trust', 'sampreshan-child' ); ?>
            </h2>
            <div class="sp-page__content">
                <p>
                    <?php esc_html_e( 'SampreShan is an initiative of ShivBodh Trust — a non-profit organization dedicated to the preservation and promotion of Sanatana Dharma values. The Trust operates without any commercial interests, focusing entirely on community welfare and Dharmic awareness.', 'sampreshan-child' ); ?>
                </p>
                <p>
                    <?php esc_html_e( 'For more information about the Trust, its activities, and its mission,', 'sampreshan-child' ); ?>
                    <a href="https://shivbodhtrust.org" target="_blank" rel="noopener"><?php esc_html_e( 'visit shivbodhtrust.org', 'sampreshan-child' ); ?></a>.
                </p>
            </div>
        </section>

        <!-- TIMELINE -->
        <section class="sp-page__card">
            <h2 class="sp-page__section-title">
                <?php sp_icon_e( 'dharma', 'sp-icon--md sp-icon--gold', __( 'Journey', 'sampreshan-child' ) ); ?>
                <?php esc_html_e( 'Our Journey', 'sampreshan-child' ); ?>
            </h2>
            <div class="sp-timeline">
                <div class="sp-timeline__item">
                    <div class="sp-timeline__year"><?php esc_html_e( '2024', 'sampreshan-child' ); ?></div>
                    <div class="sp-timeline__title"><?php esc_html_e( 'ShivBodh Trust Established', 'sampreshan-child' ); ?></div>
                    <p class="sp-timeline__desc"><?php esc_html_e( 'The Trust was founded with a vision to create a digital platform for the Sanatana Dharma community.', 'sampreshan-child' ); ?></p>
                </div>
                <div class="sp-timeline__item">
                    <div class="sp-timeline__year"><?php esc_html_e( '2025', 'sampreshan-child' ); ?></div>
                    <div class="sp-timeline__title"><?php esc_html_e( 'SampreShan Platform Launched', 'sampreshan-child' ); ?></div>
                    <p class="sp-timeline__desc"><?php esc_html_e( 'The platform went live with petition system, community feed, and member profiles.', 'sampreshan-child' ); ?></p>
                </div>
                <div class="sp-timeline__item">
                    <div class="sp-timeline__year"><?php esc_html_e( '2026', 'sampreshan-child' ); ?></div>
                    <div class="sp-timeline__title"><?php esc_html_e( 'Growing Community', 'sampreshan-child' ); ?></div>
                    <p class="sp-timeline__desc"><?php esc_html_e( 'Expanding features, onboarding sampradayas, and building a stronger collective voice for Dharma.', 'sampreshan-child' ); ?></p>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="sp-page__cta">
            <h2 class="sp-page__cta-title"><?php esc_html_e( 'Ready to Make a Difference?', 'sampreshan-child' ); ?></h2>
            <p class="sp-page__cta-sub"><?php esc_html_e( 'Join thousands of community members who are raising their voice for Dharma.', 'sampreshan-child' ); ?></p>
            <div class="sp-page__cta-actions">
                <a class="btn-3d btn-3d--lg" href="<?php echo esc_url( home_url( '/start-a-petition/' ) ); ?>">
                    <?php sp_icon_e( 'plus', 'sp-icon--sm sp-icon--white', __( 'Start', 'sampreshan-child' ) ); ?>
                    <?php esc_html_e( 'Start a Petition', 'sampreshan-child' ); ?>
                </a>
                <a class="btn-3d btn-3d--lg btn-3d--royal" href="<?php echo esc_url( home_url( '/login/' ) ); ?>">
                    <?php sp_icon_e( 'lock', 'sp-icon--sm sp-icon--white', __( 'Join', 'sampreshan-child' ) ); ?>
                    <?php esc_html_e( 'Join the Community', 'sampreshan-child' ); ?>
                </a>
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
