<?php
/**
 * Home: Left Sidebar
 * Quick links + Categories. Real BuddyBoss profile / member / group URLs.
 * No AI content. No fake links — all point to real WP endpoints or anchors.
 *
 * @package SampreShan_Child
 */

$petitions_url = home_url( '/start-a-petition/' );
$feed_url      = function_exists( 'bp_get_activity_directory_permalink' ) ? bp_get_activity_directory_permalink() : home_url( '/activity/' );
$members_url   = function_exists( 'bp_get_members_directory_permalink' ) ? bp_get_members_directory_permalink() : home_url( '/members/' );
$groups_url    = function_exists( 'bp_get_groups_directory_permalink' ) ? bp_get_groups_directory_permalink() : home_url( '/groups/' );
?>
<aside class="home-aside home-aside--left" aria-label="Primary navigation">
    <div class="card card--aside">
        <h3 class="card__title card__title--aside"><?php echo esc_html__( 'Quick Links', 'sampreshan-child' ); ?></h3>
        <ul class="home-aside__list">
            <li>
                <a class="home-aside__link" href="<?php echo esc_url( $petitions_url ); ?>">
                    <span class="home-aside__link-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                            <polyline points="14 2 14 8 20 8" />
                        </svg>
                    </span>
                    <span><?php echo esc_html__( 'Start a Petition', 'sampreshan-child' ); ?></span>
                </a>
            </li>
            <li>
                <a class="home-aside__link" href="<?php echo esc_url( $feed_url ); ?>">
                    <span class="home-aside__link-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 12h4l3-9 4 18 3-9h4" />
                        </svg>
                    </span>
                    <span><?php echo esc_html__( 'Activity Feed', 'sampreshan-child' ); ?></span>
                </a>
            </li>
            <li>
                <a class="home-aside__link" href="<?php echo esc_url( $members_url ); ?>">
                    <span class="home-aside__link-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" /><circle cx="9" cy="7" r="4" />
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87" /><path d="M16 3.13a4 4 0 0 1 0 7.75" />
                        </svg>
                    </span>
                    <span><?php echo esc_html__( 'Browse Members', 'sampreshan-child' ); ?></span>
                </a>
            </li>
            <li>
                <a class="home-aside__link" href="<?php echo esc_url( $groups_url ); ?>">
                    <span class="home-aside__link-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10" /><circle cx="12" cy="12" r="4" />
                        </svg>
                    </span>
                    <span><?php echo esc_html__( 'Groups', 'sampreshan-child' ); ?></span>
                </a>
            </li>
        </ul>
    </div>

    <div class="card card--aside">
        <h3 class="card__title card__title--aside"><?php echo esc_html__( 'Cause Categories', 'sampreshan-child' ); ?></h3>
        <ul class="home-aside__cats">
            <li><a href="<?php echo esc_url( home_url( '/category/temple-preservation/' ) ); ?>"><?php echo esc_html__( 'Temple Preservation', 'sampreshan-child' ); ?></a></li>
            <li><a href="<?php echo esc_url( home_url( '/category/cultural-heritage/' ) ); ?>"><?php echo esc_html__( 'Cultural Heritage', 'sampreshan-child' ); ?></a></li>
            <li><a href="<?php echo esc_url( home_url( '/category/religious-education/' ) ); ?>"><?php echo esc_html__( 'Religious Education', 'sampreshan-child' ); ?></a></li>
            <li><a href="<?php echo esc_url( home_url( '/category/community-welfare/' ) ); ?>"><?php echo esc_html__( 'Community Welfare', 'sampreshan-child' ); ?></a></li>
            <li><a href="<?php echo esc_url( home_url( '/category/environmental/' ) ); ?>"><?php echo esc_html__( 'Environmental', 'sampreshan-child' ); ?></a></li>
        </ul>
    </div>
</aside>
