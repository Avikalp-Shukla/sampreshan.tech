<?php
/**
 * Front Page (Homepage) — Premium 3D Dark Edition
 * Full-viewport immersive: hero → stats → what/why → petitions → trust CTA.
 *
 * @package SampreShan_Child
 */

get_header();
?>

<main id="main" class="site-main site-main--landing" role="main">
    <div class="sp-landing" style="max-width:none; padding:0; gap:0;">

        <?php
        /* === HERO (full-viewport dark immersive) === */
        $hero_tpl = get_stylesheet_directory() . '/template-parts/home/hero-mission.php';
        if ( file_exists( $hero_tpl ) ) { include $hero_tpl; }

        /* === SECTION DIVIDER (original wave artwork) === */
        if ( function_exists( 'sp_art_url' ) && sp_art_url( 'wave.svg' ) ) {
            echo '<img class="sp-wave" src="' . esc_url( sp_art_url( 'wave.svg' ) ) . '"'
                . ' alt="" width="1440" height="120" loading="lazy" decoding="async" aria-hidden="true" />' . "\n";
        }

        /* === STATS STRIP (glass counters, scroll-triggered) === */
        $stats_tpl = get_stylesheet_directory() . '/template-parts/home/stats-strip.php';
        if ( file_exists( $stats_tpl ) ) { include $stats_tpl; }

        /* === WHAT IS SAMPRESHAN + HOW IT WORKS === */
        $about_tpl = get_stylesheet_directory() . '/template-parts/home/about-site.php';
        if ( file_exists( $about_tpl ) ) { include $about_tpl; }

        /* === FEATURED PETITION (cinematic dark card) === */
        $featured_tpl = get_stylesheet_directory() . '/template-parts/home/featured-petition.php';
        if ( file_exists( $featured_tpl ) ) { include $featured_tpl; }

        /* === ACTIVE PETITIONS (horizontal scroll carousel) === */
        $list_tpl = get_stylesheet_directory() . '/template-parts/home/petitions-list.php';
        if ( file_exists( $list_tpl ) ) { include $list_tpl; }

        /* === I-SHOWCASE (how one tap becomes an I) === */
        $ishow_tpl = get_stylesheet_directory() . '/template-parts/home/i-showcase.php';
        if ( file_exists( $ishow_tpl ) ) { include $ishow_tpl; }

        /* === COMMUNITY PULSE (social fusion) === */
        $pulse_tpl = get_stylesheet_directory() . '/template-parts/home/social-fusion.php';
        if ( file_exists( $pulse_tpl ) ) { include $pulse_tpl; }

        /* === FLAG CINEMA === */
        $flag_tpl = get_stylesheet_directory() . '/template-parts/home/flag-cinema.php';
        if ( file_exists( $flag_tpl ) ) { include $flag_tpl; }
        ?>

        <!-- TRUST CTA — Dark immersive, with the jali lattice behind it -->
        <section class="d3-cta sp-jali" aria-label="About the Trust">
            <div class="d3-cta__inner d3-reveal">
                <p class="d3-section__eyebrow" style="justify-content:center; margin-bottom:1rem;"><?php esc_html_e( 'By ShivBodh Trust', 'sampreshan-child' ); ?></p>
                <h2 class="d3-cta__title"><?php esc_html_e( 'Dedicated to all who have faith in Sanatan Dharma.', 'sampreshan-child' ); ?></h2>
                <p class="d3-cta__sub"><?php esc_html_e( 'No donations on this platform. No fees. Just a clean space for Sanatan voices to be heard — every sampradaya respected equally.', 'sampreshan-child' ); ?></p>
                <div class="d3-cta__actions">
                    <a class="d3-btn d3-btn--primary d3-btn--lg" href="<?php echo esc_url( home_url( '/about/' ) ); ?>"><?php esc_html_e( 'About the mission', 'sampreshan-child' ); ?></a>
                    <a class="d3-btn d3-btn--ghost d3-btn--lg" href="<?php echo esc_url( home_url( '/login/' ) ); ?>"><?php esc_html_e( 'Join the community', 'sampreshan-child' ); ?></a>
                </div>
            </div>
        </section>

        <?php
        /* === WELCOME FEED === */
        $feed_tpl = get_stylesheet_directory() . '/template-parts/home/welcome-feed.php';
        if ( file_exists( $feed_tpl ) ) { include $feed_tpl; }
        ?>

    </div>
</main>

<?php
/* === MOBILE BOTTOM TAB NAV === */
$mobile_tpl = get_stylesheet_directory() . '/template-parts/home/mobile-tab-nav.php';
if ( file_exists( $mobile_tpl ) ) { include $mobile_tpl; }

/* === FOOTER === */
$footer_tpl = get_stylesheet_directory() . '/template-parts/footer/site-footer.php';
if ( file_exists( $footer_tpl ) ) {
    include $footer_tpl;
} else {
    get_footer();
}
?>
