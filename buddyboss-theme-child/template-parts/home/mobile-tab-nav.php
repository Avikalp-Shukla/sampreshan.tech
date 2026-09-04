<?php
/**
 * Home: Mobile Bottom Tab Nav (v5 — User pages)
 * Sticky bottom tab bar — visible only on mobile (CSS-controlled).
 *
 * @package SampreShan_Child
 */

$home_url      = home_url( '/' );
$petitions_url = home_url( '/petitions/' );
$start_url     = home_url( '/start-a-petition/' );
// Never hardcode /feed/ — that slug serves the RSS feed. Resolve our Feed page.
$feed_url      = function_exists( 'sp_feed_url' ) ? sp_feed_url() : home_url( '/feed-2/' );
$feed_page_id  = $feed_url ? url_to_postid( $feed_url ) : 0;
$community_url = home_url( '/community/' );
$is_logged_in  = is_user_logged_in();
$dashboard_url = home_url( '/dashboard/' );
$account_url   = $is_logged_in ? $dashboard_url : wp_login_url( get_permalink() );

$current_slug = '';
if ( is_front_page() ) { $current_slug = 'home'; }
elseif ( is_page( 'petitions' ) || is_singular( 'petition' ) ) { $current_slug = 'petitions'; }
elseif ( is_page( 'start-a-petition' ) ) { $current_slug = 'start'; }
elseif ( $feed_page_id && is_page( $feed_page_id ) ) { $current_slug = 'feed'; }
elseif ( is_page( array( 'community', 'members' ) ) ) { $current_slug = 'community'; }
elseif ( is_page( array( 'dashboard', 'my-petitions', 'signed-petitions', 'settings', 'profile' ) ) ) { $current_slug = 'dashboard'; }

if ( ! function_exists( 'sp_tab_item' ) ) {
function sp_tab_item( $slug, $current, $url, $icon, $label ) {
    $active = $slug === $current ? ' is-active' : '';
    $aria   = $slug === $current ? ' aria-current="page"' : '';
    ob_start();
    ?>
    <a class="mobile-tab-nav__item<?php echo esc_attr( $active ); ?><?php echo 'start' === $slug ? ' mobile-tab-nav__item--start' : ''; ?>" href="<?php echo esc_url( $url ); ?>"<?php echo $aria; ?>>
        <?php sp_icon_e( $icon, 'sp-icon mobile-tab-nav__icon', '' ); ?>
        <span><?php echo esc_html( $label ); ?></span>
    </a>
    <?php
    echo ob_get_clean();
}
} // end function_exists guard
?>
<nav class="mobile-tab-nav" aria-label="Primary mobile navigation">
    <?php
    sp_tab_item( 'home', $current_slug, $home_url, 'home', __( 'Home', 'sampreshan-child' ) );
    sp_tab_item( 'petitions', $current_slug, $petitions_url, 'petition', __( 'Petitions', 'sampreshan-child' ) );
    sp_tab_item( 'start', $current_slug, $start_url, 'plus', __( 'Start', 'sampreshan-child' ) );
    sp_tab_item( 'feed', $current_slug, $feed_url, 'feed', __( 'Feed', 'sampreshan-child' ) );
    if ( $is_logged_in ) {
        sp_tab_item( 'dashboard', $current_slug, $dashboard_url, 'star', __( 'Dashboard', 'sampreshan-child' ) );
    } else {
        sp_tab_item( 'dashboard', $current_slug, $account_url, 'account', __( 'Account', 'sampreshan-child' ) );
    }
    ?>
</nav>
