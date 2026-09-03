<?php
/**
 * Front Page (Homepage)
 * SampreShan — Sanatan Voice Platform
 * Truth Social + Change.org fusion layout
 * Premium, fully responsive, no AI content, no emojis
 *
 * @package SampreShan_Child
 */

get_header();
?>

<main class="site-main" role="main">

    <?php
    /* === LEFT SIDEBAR (desktop only) === */
    $left_tpl = get_stylesheet_directory() . '/template-parts/home/left-sidebar.php';
    if ( file_exists( $left_tpl ) ) {
        include $left_tpl;
    }
    ?>

    <div class="site-main__content">

        <?php
        /* === HERO + MISSION === */
        $hero_tpl = get_stylesheet_directory() . '/template-parts/home/hero-mission.php';
        if ( file_exists( $hero_tpl ) ) {
            include $hero_tpl;
        }

        /* === STATS STRIP (real WP counts) === */
        $stats_tpl = get_stylesheet_directory() . '/template-parts/home/stats-strip.php';
        if ( file_exists( $stats_tpl ) ) {
            include $stats_tpl;
        }

        /* === FEATURED PETITION (real page ID 94) === */
        $featured_tpl = get_stylesheet_directory() . '/template-parts/home/featured-petition.php';
        if ( file_exists( $featured_tpl ) ) {
            include $featured_tpl;
        }

        /* === ACTIVE PETITIONS GRID === */
        $list_tpl = get_stylesheet_directory() . '/template-parts/home/petitions-list.php';
        if ( file_exists( $list_tpl ) ) {
            include $list_tpl;
        }

        /* === WELCOME FEED POST (real post ID 99) === */
        $feed_tpl = get_stylesheet_directory() . '/template-parts/home/welcome-feed.php';
        if ( file_exists( $feed_tpl ) ) {
            include $feed_tpl;
        }
        ?>

    </div>

    <?php
    /* === RIGHT SIDEBAR (desktop + tablet) === */
    $right_tpl = get_stylesheet_directory() . '/template-parts/home/right-sidebar.php';
    if ( file_exists( $right_tpl ) ) {
        include $right_tpl;
    }
    ?>

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
