<?php
/**
 * Home: Left Sidebar — Premium Edition
 * What You Can Create + Quick Links
 *
 * @package SampreShan_Child
 */

$petitions_url = home_url( '/start-a-petition/' );
$dashboard_url = home_url( '/dashboard/' );
$feed_url      = function_exists( 'sp_feed_url' ) ? sp_feed_url() : home_url( '/feed-2/' );
$members_url   = home_url( '/community/' );
$is_logged_in  = is_user_logged_in();
?>
<aside class="home-aside home-aside--left" aria-label="Quick navigation">

    <!-- WHAT YOU CAN CREATE -->
    <div class="card card--aside card--aside-create glass">
        <h3 class="card__title card__title--aside">
            <?php sp_icon_auto( 'plus', 'sp-icon--sm sp-icon--saffron', '' ); ?>
            <?php echo esc_html__( 'What You Can Create', 'sampreshan-child' ); ?>
        </h3>
        <ul class="home-aside__create-list">
            <li>
                <a class="home-aside__create-link" href="<?php echo esc_url( $petitions_url ); ?>">
                    <span class="home-aside__create-icon home-aside__create-icon--saffron">
                        <?php sp_icon_auto( 'petition', 'sp-icon--sm sp-icon--saffron', __( 'Petition', 'sampreshan-child' ) ); ?>
                    </span>
                    <div class="home-aside__create-text">
                        <span class="home-aside__create-name"><?php echo esc_html__( 'Start a Petition', 'sampreshan-child' ); ?></span>
                        <span class="home-aside__create-desc"><?php echo esc_html__( 'Raise your voice, gather support', 'sampreshan-child' ); ?></span>
                    </div>
                    <span class="home-aside__create-arrow" aria-hidden="true">&rarr;</span>
                </a>
            </li>
            <li>
                <a class="home-aside__create-link" href="<?php echo esc_url( $feed_url ); ?>">
                    <span class="home-aside__create-icon home-aside__create-icon--blue">
                        <?php sp_icon_auto( 'feed', 'sp-icon--sm sp-icon--blue', __( 'Post', 'sampreshan-child' ) ); ?>
                    </span>
                    <div class="home-aside__create-text">
                        <span class="home-aside__create-name"><?php echo esc_html__( 'Share a Post', 'sampreshan-child' ); ?></span>
                        <span class="home-aside__create-desc"><?php echo esc_html__( 'Thoughts, news, discussions', 'sampreshan-child' ); ?></span>
                    </div>
                    <span class="home-aside__create-arrow" aria-hidden="true">&rarr;</span>
                </a>
            </li>
            <li>
                <a class="home-aside__create-link" href="<?php echo esc_url( $members_url ); ?>">
                    <span class="home-aside__create-icon home-aside__create-icon--gold">
                        <?php sp_icon_auto( 'network', 'sp-icon--sm sp-icon--gold', __( 'Connect', 'sampreshan-child' ) ); ?>
                    </span>
                    <div class="home-aside__create-text">
                        <span class="home-aside__create-name"><?php echo esc_html__( 'Build Network', 'sampreshan-child' ); ?></span>
                        <span class="home-aside__create-desc"><?php echo esc_html__( 'Connect with community', 'sampreshan-child' ); ?></span>
                    </div>
                    <span class="home-aside__create-arrow" aria-hidden="true">&rarr;</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- QUICK LINKS -->
    <div class="card card--aside glass">
        <h3 class="card__title card__title--aside"><?php sp_icon_auto( 'bookmark', 'sp-icon--sm sp-icon--saffron', '' ); ?> <?php echo esc_html__( 'Quick Links', 'sampreshan-child' ); ?></h3>
        <ul class="home-aside__list">
            <?php if ( $is_logged_in ) : ?>
                <li>
                    <a class="home-aside__link" href="<?php echo esc_url( $dashboard_url ); ?>">
                        <span class="home-aside__link-icon" aria-hidden="true">
                            <?php sp_icon_auto( 'star', 'sp-icon--sm sp-icon--saffron', __( 'Dashboard', 'sampreshan-child' ) ); ?>
                        </span>
                        <span><?php echo esc_html__( 'My Dashboard', 'sampreshan-child' ); ?></span>
                    </a>
                </li>
            <?php endif; ?>
            <li>
                <a class="home-aside__link" href="<?php echo esc_url( $petitions_url ); ?>">
                    <span class="home-aside__link-icon" aria-hidden="true">
                        <?php sp_icon_auto( 'petition', 'sp-icon--sm sp-icon--saffron', __( 'Petitions', 'sampreshan-child' ) ); ?>
                    </span>
                    <span><?php echo esc_html__( 'Start a Petition', 'sampreshan-child' ); ?></span>
                </a>
            </li>
            <li>
                <a class="home-aside__link" href="<?php echo esc_url( $feed_url ); ?>">
                    <span class="home-aside__link-icon" aria-hidden="true">
                        <?php sp_icon_auto( 'feed', 'sp-icon--sm sp-icon--saffron', __( 'Feed', 'sampreshan-child' ) ); ?>
                    </span>
                    <span><?php echo esc_html__( 'Activity Feed', 'sampreshan-child' ); ?></span>
                </a>
            </li>
            <li>
                <a class="home-aside__link" href="<?php echo esc_url( $members_url ); ?>">
                    <span class="home-aside__link-icon" aria-hidden="true">
                        <?php sp_icon_auto( 'network', 'sp-icon--sm sp-icon--saffron', __( 'Network', 'sampreshan-child' ) ); ?>
                    </span>
                    <span><?php echo esc_html__( 'Browse Members', 'sampreshan-child' ); ?></span>
                </a>
            </li>
        </ul>
    </div>
</aside>
