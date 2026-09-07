<?php
/**
 * Front Page (Homepage) — Ideal Landing Edition
 * Clean storytelling flow: hero → stats → what/why → petitions → trust CTA.
 *
 * @package SampreShan_Child
 */

get_header();
?>

<main class="site-main site-main--landing" role="main">
    <div class="sp-landing">

        <?php
        /* === HERO (what the site is, in one glance) === */
        $hero_tpl = get_stylesheet_directory() . '/template-parts/home/hero-mission.php';
        if ( file_exists( $hero_tpl ) ) {
            include $hero_tpl;
        }

        /* === STATS STRIP (real WP counts) === */
        $stats_tpl = get_stylesheet_directory() . '/template-parts/home/stats-strip.php';
        if ( file_exists( $stats_tpl ) ) {
            include $stats_tpl;
        }

        /* === WHAT IS SAMPRESHAN + HOW IT WORKS === */
        $about_tpl = get_stylesheet_directory() . '/template-parts/home/about-site.php';
        if ( file_exists( $about_tpl ) ) {
            include $about_tpl;
        }

        /* === FEATURED PETITION === */
        $featured_tpl = get_stylesheet_directory() . '/template-parts/home/featured-petition.php';
        if ( file_exists( $featured_tpl ) ) {
            include $featured_tpl;
        }

        /* === ACTIVE PETITIONS === */
        $list_tpl = get_stylesheet_directory() . '/template-parts/home/petitions-list.php';
        if ( file_exists( $list_tpl ) ) {
            include $list_tpl;
        }

        /* === COMMUNITY PULSE (social fusion: CTA + live + trending) === */
        $pulse_tpl = get_stylesheet_directory() . '/template-parts/home/social-fusion.php';
        if ( file_exists( $pulse_tpl ) ) {
            include $pulse_tpl;
        }
        ?>

        <!-- TRUST BANNER -->
        <section class="sp-trust" aria-label="About the Trust">
            <div class="sp-trust__inner">
                <div class="sp-trust__copy">
                    <p class="sp-section__eyebrow sp-section__eyebrow--light"><?php esc_html_e( 'By ShivBodh Trust', 'sampreshan-child' ); ?></p>
                    <h2 class="sp-trust__title"><?php sp_icon_auto( 'shield', 'sp-icon--md sp-icon--gold', '' ); ?> <?php esc_html_e( 'Dedicated to all who have faith in Sanatan Dharma.', 'sampreshan-child' ); ?></h2>
                    <p class="sp-trust__sub"><?php esc_html_e( 'No donations on this platform. No fees. Just a clean space for Sanatan voices to be heard — every sampradaya respected equally.', 'sampreshan-child' ); ?></p>
                </div>
                <div class="sp-trust__actions">
                    <a class="btn btn--light btn--lg" href="<?php echo esc_url( home_url( '/about/' ) ); ?>"><?php esc_html_e( 'About the mission', 'sampreshan-child' ); ?></a>
                    <a class="btn btn--outline-light btn--lg" href="<?php echo esc_url( home_url( '/login/' ) ); ?>"><?php esc_html_e( 'Join the community', 'sampreshan-child' ); ?></a>
                </div>
            </div>
        </section>

        <?php
        /* === WELCOME FEED (community voice) === */
        $feed_tpl = get_stylesheet_directory() . '/template-parts/home/welcome-feed.php';
        if ( file_exists( $feed_tpl ) ) {
            include $feed_tpl;
        }
        ?>

    </div>
</main>

<?php
/* === MOBILE BOTTOM TAB NAV (visible only <=768px via CSS) === */
$mobile_tpl = get_stylesheet_directory() . '/template-parts/home/mobile-tab-nav.php';
if ( file_exists( $mobile_tpl ) ) {
    include $mobile_tpl;
}

/* === FOOTER === */
$footer_tpl = get_stylesheet_directory() . '/template-parts/footer/site-footer.php';
if ( file_exists( $footer_tpl ) ) {
    include $footer_tpl;
} else {
    get_footer();
}
?>
