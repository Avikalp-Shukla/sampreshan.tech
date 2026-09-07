<?php
/**
 * Template Name: Sanatan Profile
 *
 * Truth Social × Change.org fusion: cover banner, overlapping avatar,
 * name + handle, bio, Sanatan chips, stats, tabs (Posts / Signatures).
 *
 * @package SampreShan_Child
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

get_header();

// ---- Determine whose profile we're showing ----
$user_id = 0;

if ( function_exists( 'bp_displayed_user_id' ) ) {
    $bp_id = (int) bp_displayed_user_id();
    if ( $bp_id > 0 ) { $user_id = $bp_id; }
}
if ( ! $user_id && get_query_var( 'author' ) ) {
    $user_id = (int) get_query_var( 'author' );
}
if ( ! $user_id && isset( $_GET['user_id'] ) ) {
    $user_id = (int) $_GET['user_id'];
}
if ( $user_id <= 0 ) {
    $user_id = get_current_user_id();
}
if ( $user_id <= 0 ) {
    ?>
    <main class="site-main sp-profile sp-profile--guest sp-fu-wash" role="main">
        <div class="sp-container sp-empty">
            <?php sp_icon_auto( 'lock', 'sp-icon--3xl sp-icon--saffron', __( 'Locked', 'sampreshan-child' ) ); ?>
            <h1 class="sp-empty__title"><?php esc_html_e( 'Sign in to view your profile', 'sampreshan-child' ); ?></h1>
            <p class="sp-empty__body"><?php esc_html_e( 'Your Sanatan profile, petitions, and signatures live behind the sign-in door.', 'sampreshan-child' ); ?></p>
            <a class="sp-fu-btn sp-fu-btn--primary" href="<?php echo esc_url( wp_login_url( get_permalink() ) ); ?>">
                <?php esc_html_e( 'Sign In', 'sampreshan-child' ); ?>
            </a>
        </div>
    </main>
    <?php
    get_footer();
    return;
}

$user          = get_user_by( 'id', $user_id );
$display_name  = $user ? $user->display_name : '';
$nicename      = $user ? $user->user_nicename : '';
$avatar_url    = get_avatar_url( $user_id, array( 'size' => 264 ) );
$joined        = $user ? date_i18n( 'M Y', strtotime( $user->user_registered ) ) : '';
$bio           = function_exists( 'sp_profile_get_field' ) ? sp_profile_get_field( 'bio', $user_id ) : '';
$gotra         = function_exists( 'sp_profile_get_field' ) ? sp_profile_get_field( 'gotra', $user_id ) : '';
$sampradaya    = function_exists( 'sp_profile_get_field' ) ? sp_profile_get_field( 'sampradaya', $user_id ) : '';
$location      = function_exists( 'sp_profile_get_field' ) ? sp_profile_get_field( 'location', $user_id ) : '';
$completeness  = function_exists( 'sp_profile_completeness' ) ? (int) sp_profile_completeness( $user_id ) : 0;
$stats         = function_exists( 'sp_profile_user_stats' ) ? sp_profile_user_stats( $user_id ) : array(
    'petitions_started' => 0, 'signatures_given' => 0, 'supporters_received' => 0,
);
$is_me = ( get_current_user_id() === $user_id );

// My petitions (authored by this user)
$my_petitions = new WP_Query( array(
    'post_type'      => 'petition',
    'post_status'    => 'publish',
    'author'         => $user_id,
    'posts_per_page' => 10,
    'orderby'        => 'date',
    'order'          => 'DESC',
) );

// Petitions I have signed
$my_signatures = array();
if ( function_exists( 'sp_petitions_table' ) ) {
    global $wpdb;
    $table = sp_petitions_table();
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery
    $exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) );
    if ( $exists === $table ) {
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery
        $my_signatures = $wpdb->get_results( $wpdb->prepare(
            "SELECT s.*, p.post_title, p.post_status
               FROM $table s
               INNER JOIN {$wpdb->posts} p ON p.ID = s.petition_id
              WHERE s.user_id = %d
              ORDER BY s.created_at DESC
              LIMIT 10",
            $user_id
        ) );
    }
}

$settings_url = home_url( '/settings/' );
$start_url    = home_url( '/start-a-petition/' );
?>
<main class="site-main sp-fu-wash" role="main">
    <div class="sp-fu-profile">

        <!-- Cover -->
        <div class="sp-fu-cover" role="img" aria-label="<?php echo esc_attr( sprintf( __( '%s cover', 'sampreshan-child' ), $display_name ) ); ?>">
            <span class="sp-fu-cover__om" aria-hidden="true">ॐ</span>
        </div>

        <!-- Identity row -->
        <div class="sp-fu-id">
            <div class="sp-fu-avatar">
                <?php if ( $avatar_url ) : ?>
                    <img src="<?php echo esc_url( $avatar_url ); ?>" alt="<?php echo esc_attr( $display_name ); ?>" width="132" height="132" />
                <?php else : ?>
                    <?php sp_icon_auto( 'account', 'sp-icon--3xl', '' ); ?>
                <?php endif; ?>
            </div>
            <div class="sp-fu-id__actions">
                <?php if ( $is_me ) : ?>
                    <a class="sp-fu-btn sp-fu-btn--mor sp-fu-btn--sm" href="<?php echo esc_url( $settings_url ); ?>"><?php esc_html_e( 'Edit profile', 'sampreshan-child' ); ?></a>
                    <a class="sp-fu-btn sp-fu-btn--primary sp-fu-btn--sm" href="<?php echo esc_url( $start_url ); ?>">
                        <?php sp_icon_auto( 'plus', 'sp-icon--xs', '' ); ?>
                        <?php esc_html_e( 'Start a Petition', 'sampreshan-child' ); ?>
                    </a>
                <?php else : ?>
                    <button class="sp-fu-btn sp-fu-btn--mor sp-fu-btn--sm sp-share-btn" type="button" data-url="<?php echo esc_url( get_author_posts_url( $user_id ) ); ?>" data-title="<?php echo esc_attr( $display_name ); ?>"><?php esc_html_e( 'Share', 'sampreshan-child' ); ?></button>
                <?php endif; ?>
            </div>
        </div>

        <!-- Names -->
        <div class="sp-fu-names">
            <h1 class="sp-fu-name">
                <?php echo esc_html( $display_name ); ?>
                <?php if ( $completeness >= 70 ) : ?>
                    <span class="sp-fu-verified" title="<?php esc_attr_e( 'Established voice', 'sampreshan-child' ); ?>" aria-hidden="true"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>
                <?php endif; ?>
            </h1>
            <p class="sp-fu-handle">@<?php echo esc_html( $nicename ); ?></p>
            <?php if ( $bio ) : ?>
                <p class="sp-fu-bio"><?php echo esc_html( $bio ); ?></p>
            <?php endif; ?>
            <div class="sp-fu-meta">
                <?php if ( $location ) : ?>
                    <span><?php sp_icon_auto( 'location', 'sp-icon--xs', '' ); ?><?php echo esc_html( $location ); ?></span>
                <?php endif; ?>
                <span><?php sp_icon_auto( 'calendar', 'sp-icon--xs', '' ); ?><?php echo esc_html( sprintf( __( 'Joined %s', 'sampreshan-child' ), $joined ) ); ?></span>
            </div>
            <?php if ( $sampradaya || $gotra ) : ?>
                <ul class="sp-fu-chips">
                    <?php if ( $sampradaya ) : ?>
                        <li><?php sp_icon_auto( 'om', 'sp-icon--xs', '' ); ?><?php echo esc_html( $sampradaya ); ?></li>
                    <?php endif; ?>
                    <?php if ( $gotra ) : ?>
                        <li><?php echo esc_html( sprintf( __( 'Gotra: %s', 'sampreshan-child' ), $gotra ) ); ?></li>
                    <?php endif; ?>
                </ul>
            <?php endif; ?>
        </div>

        <?php if ( $is_me && $completeness < 100 ) : ?>
            <div class="sp-fu-complete">
                <?php echo esc_html( sprintf( __( 'Profile %s%% complete — add a bio and Sanatan details to earn trust.', 'sampreshan-child' ), $completeness ) ); ?>
                <span class="sp-fu-complete__bar" role="progressbar" aria-valuenow="<?php echo esc_attr( $completeness ); ?>" aria-valuemin="0" aria-valuemax="100"><span class="sp-fu-complete__fill" style="width: <?php echo esc_attr( $completeness ); ?>%"></span></span>
            </div>
        <?php endif; ?>

        <!-- Stats -->
        <div class="sp-fu-stats" aria-label="<?php esc_attr_e( 'Activity stats', 'sampreshan-child' ); ?>">
            <span><strong><?php echo esc_html( number_format_i18n( (int) $stats['petitions_started'] ) ); ?></strong> <?php esc_html_e( 'Petitions', 'sampreshan-child' ); ?></span>
            <span><strong><?php echo esc_html( number_format_i18n( (int) $stats['signatures_given'] ) ); ?></strong> <?php esc_html_e( 'Is given', 'sampreshan-child' ); ?></span>
            <span><strong><?php echo esc_html( number_format_i18n( (int) $stats['supporters_received'] ) ); ?></strong> <?php esc_html_e( 'Supporters', 'sampreshan-child' ); ?></span>
        </div>

        <!-- Tabs -->
        <div class="sp-fu-tabs" role="tablist" aria-label="<?php esc_attr_e( 'Profile sections', 'sampreshan-child' ); ?>">
            <button class="sp-fu-tab is-active" type="button" role="tab" aria-selected="true" data-pane="posts"><?php esc_html_e( 'Posts', 'sampreshan-child' ); ?></button>
            <button class="sp-fu-tab" type="button" role="tab" aria-selected="false" data-pane="signatures"><?php esc_html_e( 'Is', 'sampreshan-child' ); ?></button>
        </div>

        <!-- Pane: posts (their petitions as timeline) -->
        <div class="sp-fu-pane is-active" data-pane="posts" role="tabpanel">
            <?php if ( $my_petitions->have_posts() ) : ?>
                <?php while ( $my_petitions->have_posts() ) : $my_petitions->the_post();
                    $pid   = get_the_ID();
                    $count = function_exists( 'sp_petition_signature_count' ) ? (int) sp_petition_signature_count( $pid ) : 0;
                    $goal  = (int) get_post_meta( $pid, 'sampreshan_goal', true );
                    $ago   = human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) );
                    $signed = is_user_logged_in() && function_exists( 'sp_petition_user_has_signed' ) && sp_petition_user_has_signed( $pid );
                ?>
                    <article class="sp-fu-post">
                        <div class="sp-fu-post__head">
                            <span class="sp-fu-post__avatar" aria-hidden="true">
                                <?php if ( $avatar_url ) : ?>
                                    <img src="<?php echo esc_url( $avatar_url ); ?>" alt="" width="40" height="40" loading="lazy" />
                                <?php endif; ?>
                            </span>
                            <div class="sp-fu-post__who">
                                <strong><?php echo esc_html( $display_name ); ?></strong>
                                <small>@<?php echo esc_html( $nicename ); ?> &middot; <?php echo esc_html( $ago ); ?> <?php esc_html_e( 'ago', 'sampreshan-child' ); ?></small>
                            </div>
                        </div>
                        <h2 class="sp-fu-post__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                        <p class="sp-fu-post__meta">
                            <?php echo esc_html( sprintf( _n( '%s I', '%s Is', $count, 'sampreshan-child' ), number_format_i18n( $count ) ) ); ?>
                            <?php if ( $goal > 0 ) : ?>&middot; <?php echo esc_html( sprintf( __( 'goal %s', 'sampreshan-child' ), number_format_i18n( $goal ) ) ); ?><?php endif; ?>
                        </p>
                        <div class="sp-fu-post__engage">
                            <?php if ( is_user_logged_in() && current_user_can( 'sign_petitions' ) ) : ?>
                                <button type="button" class="sp-fu-btn sp-fu-btn--primary sp-fu-btn--sm sp-sign-button<?php echo $signed ? ' is-signed' : ''; ?>" data-petition-id="<?php echo esc_attr( $pid ); ?>" data-signed="<?php echo $signed ? '1' : '0'; ?>">
                                    <?php if ( $signed ) : ?><?php sp_icon_e( 'ibadge', 'sp-icon sp-icon--xs', '' ); ?> <?php echo esc_html__( 'Supported', 'sampreshan-child' ); ?><?php else : ?><?php sp_icon_e( 'ibadge', 'sp-icon sp-icon--xs', '' ); ?> <?php echo esc_html__( 'I Support', 'sampreshan-child' ); ?><?php endif; ?>
                                </button>
                            <?php endif; ?>
                            <a class="sp-fu-link" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Read →', 'sampreshan-child' ); ?></a>
                            <button class="sp-fu-link sp-share-btn" type="button" data-url="<?php echo esc_url( get_permalink() ); ?>" data-title="<?php echo esc_attr( get_the_title() ); ?>" style="background:none;border:0;cursor:pointer;"><?php esc_html_e( 'Share', 'sampreshan-child' ); ?></button>
                        </div>
                    </article>
                <?php endwhile; wp_reset_postdata(); ?>
            <?php else : ?>
                <div class="sp-fu-post">
                    <p class="sp-fu-post__meta" style="margin:0;">
                        <?php echo $is_me ? esc_html__( 'You have not posted any petitions yet. Start your first cause above.', 'sampreshan-child' ) : esc_html__( 'No posts yet.', 'sampreshan-child' ); ?>
                    </p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Pane: signatures -->
        <div class="sp-fu-pane" data-pane="signatures" role="tabpanel">
            <?php if ( ! empty( $my_signatures ) ) : ?>
                <?php foreach ( $my_signatures as $sig ) : ?>
                    <article class="sp-fu-post">
                        <h2 class="sp-fu-post__title"><a href="<?php echo esc_url( get_permalink( (int) $sig->petition_id ) ); ?>"><?php echo esc_html( $sig->post_title ); ?></a></h2>
                        <p class="sp-fu-post__meta">
                            <?php echo esc_html( sprintf( __( 'Supported %s ago', 'sampreshan-child' ), human_time_diff( strtotime( $sig->created_at ), current_time( 'timestamp' ) ) ) ); ?>
                        </p>
                    </article>
                <?php endforeach; ?>
            <?php else : ?>
                <div class="sp-fu-post">
                    <p class="sp-fu-post__meta" style="margin:0;"><?php esc_html_e( 'No Is yet.', 'sampreshan-child' ); ?></p>
                </div>
            <?php endif; ?>
        </div>

    </div>
</main>

<script>
(function () {
    var tabs = document.querySelectorAll('.sp-fu-tab');
    var panes = document.querySelectorAll('.sp-fu-pane');
    if (!tabs.length) { return; }
    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            var key = tab.getAttribute('data-pane');
            tabs.forEach(function (t) {
                var on = t === tab;
                t.classList.toggle('is-active', on);
                t.setAttribute('aria-selected', on ? 'true' : 'false');
            });
            panes.forEach(function (p) {
                p.classList.toggle('is-active', p.getAttribute('data-pane') === key);
            });
        });
    });
})();
</script>

<?php
$footer_tpl = get_stylesheet_directory() . '/template-parts/footer/site-footer.php';
if ( file_exists( $footer_tpl ) ) {
    include $footer_tpl;
} else {
    get_footer();
}
