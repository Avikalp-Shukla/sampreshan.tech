<?php
/**
 * Custom site header — Premium Edition
 * Mega-nav, search overlay, notification bell, user avatar menu
 *
 * @package SampreShan_Child
 */

$site_name    = get_bloginfo( 'name' );
$site_tagline = get_bloginfo( 'description' );
$logo_url     = function_exists( 'sp_logo_url' ) ? sp_logo_url() : content_url( 'uploads/2026/06/sampreshan-logo-svg.svg' );
$current_user = wp_get_current_user();
$is_logged_in = is_user_logged_in();
// Community Feed page (never hardcode /feed/ — that slug serves RSS).
$sp_feed_url = function_exists( 'sp_feed_url' ) ? sp_feed_url() : home_url( '/feed-2/' );
$sp_feed_id  = $sp_feed_url ? url_to_postid( $sp_feed_url ) : 0;
?>
<noscript><style>body{opacity:1!important}.reveal{opacity:1!important;transform:none!important}</style></noscript>
<header class="site-header" role="banner" id="site-header">
    <div class="site-header__inner">
        <a class="site-header__brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
            <img class="site-header__logo"
                 src="<?php echo esc_url( $logo_url ); ?>"
                 alt="<?php echo esc_attr( $site_name ); ?>"
                 width="40" height="40" fetchpriority="high">
            <span class="site-header__title"><?php echo esc_html( $site_name ); ?></span>
        </a>

        <nav class="site-header__nav" aria-label="Primary navigation">
            <a class="site-header__nav-link <?php echo is_front_page() ? 'is-active' : ''; ?>"
               href="<?php echo esc_url( home_url( '/' ) ); ?>">
                <?php sp_icon_auto( 'home', 'sp-icon--xs', '' ); ?>
                Home
            </a>
            <a class="site-header__nav-link <?php echo is_page( 'petitions' ) || is_page( 'start-a-petition' ) || is_singular( 'petition' ) ? 'is-active' : ''; ?>"
               href="<?php echo esc_url( home_url( '/petitions/' ) ); ?>">
                <?php sp_icon_auto( 'petition', 'sp-icon--xs', '' ); ?>
                Petitions
            </a>
            <a class="site-header__nav-link <?php echo ( $sp_feed_id && is_page( $sp_feed_id ) ) ? 'is-active' : ''; ?>"
               href="<?php echo esc_url( $sp_feed_url ); ?>">
                <?php sp_icon_auto( 'feed', 'sp-icon--xs', '' ); ?>
                Feed
            </a>
            <a class="site-header__nav-link <?php echo is_page( 'community' ) ? 'is-active' : ''; ?>"
               href="<?php echo esc_url( home_url( '/community/' ) ); ?>">
                <?php sp_icon_auto( 'network', 'sp-icon--xs', '' ); ?>
                Community
            </a>
            <a class="site-header__nav-link <?php echo is_page( 'about' ) ? 'is-active' : ''; ?>"
               href="<?php echo esc_url( home_url( '/about/' ) ); ?>">
                <?php sp_icon_auto( 'info', 'sp-icon--xs', '' ); ?>
                About
            </a>
            <?php if ( $is_logged_in ) : ?>
                <a class="site-header__nav-link <?php echo is_page( 'dashboard' ) ? 'is-active' : ''; ?>"
                   href="<?php echo esc_url( home_url( '/dashboard/' ) ); ?>">
                    <?php sp_icon_auto( 'star', 'sp-icon--xs', '' ); ?>
                    Dashboard
                </a>
            <?php endif; ?>
        </nav>

        <div class="site-header__actions">
            <a class="btn btn--primary btn--sm site-header__start" href="<?php echo esc_url( home_url( '/start-a-petition/' ) ); ?>">
                <?php sp_icon_auto( 'plus', 'sp-icon--xs', '' ); ?>
                <?php esc_html_e( 'Start', 'sampreshan-child' ); ?>
            </a>
            <button class="site-header__search-btn" type="button" aria-label="Search" id="header-search-btn">
                <?php sp_icon_auto( 'search', 'sp-icon--md', __( 'Search', 'sampreshan-child' ) ); ?>
            </button>
            <button class="site-header__menu-toggle" type="button" aria-label="<?php esc_attr_e( 'Open menu', 'sampreshan-child' ); ?>" aria-expanded="false" aria-controls="sp-mobile-menu" data-menu-toggle>
                <?php sp_icon_auto( 'menu', 'sp-icon--md', '' ); ?>
            </button>

            <?php if ( $is_logged_in ) : ?>
                <button class="site-header__notification-btn" type="button" aria-label="Notifications">
                    <?php sp_icon_auto( 'bell', 'sp-icon--md', __( 'Notifications', 'sampreshan-child' ) ); ?>
                    <span class="site-header__notification-badge" aria-hidden="true"></span>
                </button>

                <div class="site-header__user-menu">
                    <?php
                    $bp_name = function_exists( 'bp_core_get_username' ) ? bp_core_get_username( $current_user->ID ) : '';
                    $profile_link = $bp_name ? home_url( '/members/' . $bp_name . '/' ) : home_url( '/profile/' );
                    ?>
                    <a class="site-header__avatar" href="<?php echo esc_url( $profile_link ); ?>" aria-label="<?php echo esc_attr( $current_user->display_name ); ?>">
                        <?php
                        $avatar_url = get_avatar_url( $current_user->ID, array( 'size' => 72 ) );
                        if ( $avatar_url ) : ?>
                            <img src="<?php echo esc_url( $avatar_url ); ?>" alt="" />
                        <?php else : ?>
                            <?php echo esc_html( mb_substr( $current_user->display_name, 0, 1 ) ); ?>
                        <?php endif; ?>
                    </a>
                </div>
            <?php else : ?>
                <a class="btn btn--outline btn--sm" href="<?php echo esc_url( wp_login_url( get_permalink() ) ); ?>">Sign In</a>
                <a class="btn btn--primary btn--sm" href="<?php echo esc_url( wp_registration_url() ); ?>">Join</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<!-- Mobile drawer menu (tablet + mobile, toggled via data-menu-toggle) -->
<nav class="sp-mobile-menu" id="sp-mobile-menu" data-mobile-menu aria-label="<?php esc_attr_e( 'Mobile navigation', 'sampreshan-child' ); ?>">
    <a class="<?php echo is_front_page() ? 'is-active' : ''; ?>" href="<?php echo esc_url( home_url( '/' ) ); ?>">
        <?php sp_icon_auto( 'home', 'sp-icon--xs', '' ); ?> <?php esc_html_e( 'Home', 'sampreshan-child' ); ?>
    </a>
    <a class="<?php echo is_page( 'petitions' ) || is_page( 'start-a-petition' ) || is_singular( 'petition' ) ? 'is-active' : ''; ?>" href="<?php echo esc_url( home_url( '/petitions/' ) ); ?>">
        <?php sp_icon_auto( 'petition', 'sp-icon--xs', '' ); ?> <?php esc_html_e( 'Petitions', 'sampreshan-child' ); ?>
    </a>
    <a class="<?php echo ( $sp_feed_id && is_page( $sp_feed_id ) ) ? 'is-active' : ''; ?>" href="<?php echo esc_url( $sp_feed_url ); ?>">
        <?php sp_icon_auto( 'feed', 'sp-icon--xs', '' ); ?> <?php esc_html_e( 'Feed', 'sampreshan-child' ); ?>
    </a>
    <a class="<?php echo is_page( 'community' ) ? 'is-active' : ''; ?>" href="<?php echo esc_url( home_url( '/community/' ) ); ?>">
        <?php sp_icon_auto( 'network', 'sp-icon--xs', '' ); ?> <?php esc_html_e( 'Community', 'sampreshan-child' ); ?>
    </a>
    <a class="<?php echo is_page( 'about' ) ? 'is-active' : ''; ?>" href="<?php echo esc_url( home_url( '/about/' ) ); ?>">
        <?php sp_icon_auto( 'info', 'sp-icon--xs', '' ); ?> <?php esc_html_e( 'About', 'sampreshan-child' ); ?>
    </a>
    <?php if ( $is_logged_in ) : ?>
        <a class="<?php echo is_page( 'dashboard' ) ? 'is-active' : ''; ?>" href="<?php echo esc_url( home_url( '/dashboard/' ) ); ?>">
            <?php sp_icon_auto( 'star', 'sp-icon--xs', '' ); ?> <?php esc_html_e( 'Dashboard', 'sampreshan-child' ); ?>
        </a>
    <?php else : ?>
        <a href="<?php echo esc_url( wp_login_url( get_permalink() ) ); ?>">
            <?php sp_icon_auto( 'account', 'sp-icon--xs', '' ); ?> <?php esc_html_e( 'Sign In', 'sampreshan-child' ); ?>
        </a>
        <a href="<?php echo esc_url( wp_registration_url() ); ?>">
            <?php sp_icon_auto( 'plus', 'sp-icon--xs', '' ); ?> <?php esc_html_e( 'Join', 'sampreshan-child' ); ?>
        </a>
    <?php endif; ?>
    <a href="<?php echo esc_url( home_url( '/start-a-petition/' ) ); ?>">
        <?php sp_icon_auto( 'plus', 'sp-icon--xs', '' ); ?> <?php esc_html_e( 'Start a Petition', 'sampreshan-child' ); ?>
    </a>
</nav>

<!-- Search Overlay -->
<div class="search-overlay" id="search-overlay" role="dialog" aria-label="Search">
    <div class="search-overlay__inner">
        <div class="search-overlay__input-wrap">
            <?php sp_icon_auto( 'search', 'sp-icon--md sp-icon--muted', '' ); ?>
            <input class="search-overlay__input" type="search" placeholder="Search petitions, members, topics..." autofocus aria-label="Search" />
            <button class="search-overlay__close" type="button" id="search-overlay-close" aria-label="Close search">
                <?php sp_icon_auto( 'close', 'sp-icon--sm', '' ); ?>
            </button>
        </div>
        <div class="search-overlay__results" id="search-results">
            <div style="padding: var(--space-8); text-align: center; color: var(--text-soft); font-size: var(--text-sm);">
                Start typing to search...
            </div>
        </div>
    </div>
</div>
