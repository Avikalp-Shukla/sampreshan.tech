<?php
/**
 * Home: Mobile Bottom Tab Nav
 * Sticky bottom tab bar — visible only on mobile (CSS-controlled).
 * Real WP links, no fake destinations.
 *
 * @package SampreShan_Child
 */

$home_url      = home_url( '/' );
$petitions_url = home_url( '/start-a-petition/' );
$feed_url      = function_exists( 'bp_get_activity_directory_permalink' ) ? bp_get_activity_directory_permalink() : home_url( '/activity/' );
$members_url   = function_exists( 'bp_get_members_directory_permalink' ) ? bp_get_members_directory_permalink() : home_url( '/members/' );
?>
<nav class="mobile-tab-nav" aria-label="Primary mobile navigation">
    <a class="mobile-tab-nav__item is-active" href="<?php echo esc_url( $home_url ); ?>" aria-current="page">
        <svg class="mobile-tab-nav__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" /><polyline points="9 22 9 12 15 12 15 22" />
        </svg>
        <span><?php echo esc_html__( 'Home', 'sampreshan-child' ); ?></span>
    </a>
    <a class="mobile-tab-nav__item" href="<?php echo esc_url( $petitions_url ); ?>">
        <svg class="mobile-tab-nav__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" /><polyline points="14 2 14 8 20 8" />
        </svg>
        <span><?php echo esc_html__( 'Petitions', 'sampreshan-child' ); ?></span>
    </a>
    <a class="mobile-tab-nav__item" href="<?php echo esc_url( $feed_url ); ?>">
        <svg class="mobile-tab-nav__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M3 12h4l3-9 4 18 3-9h4" />
        </svg>
        <span><?php echo esc_html__( 'Feed', 'sampreshan-child' ); ?></span>
    </a>
    <a class="mobile-tab-nav__item" href="<?php echo esc_url( $members_url ); ?>">
        <svg class="mobile-tab-nav__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" /><circle cx="9" cy="7" r="4" />
            <path d="M23 21v-2a4 4 0 0 0-3-3.87" /><path d="M16 3.13a4 4 0 0 1 0 7.75" />
        </svg>
        <span><?php echo esc_html__( 'Network', 'sampreshan-child' ); ?></span>
    </a>
    <a class="mobile-tab-nav__item" href="<?php echo esc_url( wp_login_url( get_permalink() ) ); ?>">
        <svg class="mobile-tab-nav__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" /><circle cx="12" cy="7" r="4" />
        </svg>
        <span><?php echo esc_html__( 'Account', 'sampreshan-child' ); ?></span>
    </a>
</nav>
