<?php
/**
 * Custom site header
 * Replaces parent BuddyBoss header via 'get_header' filter or direct override
 *
 * @package SampreShan_Child
 */

$site_name    = get_bloginfo( 'name' );
$site_tagline = get_bloginfo( 'description' );
$logo_svg     = get_stylesheet_directory_uri() . '/assets/images/logo.svg';
$logo_png     = '/wp-content/uploads/2026/06/sampreshan-logo-svg.svg'; // existing upload
?>
<header class="site-header" role="banner">
    <div class="site-header__inner">
        <a class="site-header__brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
            <img class="site-header__logo"
                 src="<?php echo esc_url( $logo_png ); ?>"
                 alt="<?php echo esc_attr( $site_name ); ?>"
                 width="40" height="40">
            <span class="site-header__title"><?php echo esc_html( $site_name ); ?></span>
        </a>

        <nav class="site-header__nav" aria-label="Primary navigation">
            <a class="site-header__nav-link <?php echo is_front_page() ? 'is-active' : ''; ?>"
               href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a>
            <a class="site-header__nav-link <?php echo is_page( 'petitions' ) || is_page( 'start-a-petition' ) ? 'is-active' : ''; ?>"
               href="<?php echo esc_url( home_url( '/start-a-petition/' ) ); ?>">Petitions</a>
            <a class="site-header__nav-link <?php echo is_page( 'activity-feeds' ) ? 'is-active' : ''; ?>"
               href="<?php echo esc_url( home_url( '/activity-feeds/' ) ); ?>">Feed</a>
            <a class="site-header__nav-link <?php echo is_page( 'about' ) ? 'is-active' : ''; ?>"
               href="<?php echo esc_url( home_url( '/about/' ) ); ?>">About</a>
        </nav>

        <div class="site-header__actions">
            <?php if ( is_user_logged_in() ) : ?>
                <a class="btn btn--ghost btn--sm" href="<?php echo esc_url( home_url( '/members/' . bp_core_get_username( get_current_user_id() ) . '/' ) ); ?>">
                    Profile
                </a>
            <?php else : ?>
                <a class="btn btn--outline btn--sm" href="<?php echo esc_url( wp_login_url( get_permalink() ) ); ?>">Sign In</a>
                <a class="btn btn--primary btn--sm" href="<?php echo esc_url( wp_registration_url() ); ?>">Join</a>
            <?php endif; ?>
        </div>
    </div>
</header>
