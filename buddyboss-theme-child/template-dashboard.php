<?php
/**
 * Template: User Dashboard — Clean Edition
 * Minimal, stylish account home: profile, stats, petitions, activity.
 *
 * @package SampreShan_Child
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! is_user_logged_in() ) {
    wp_safe_redirect( wp_login_url( home_url( '/dashboard/' ) ) );
    exit;
}

get_header();

$current_user = wp_get_current_user();
$user_id      = (int) $current_user->ID;

/* --- My petitions --- */
$my_petitions = get_posts( array(
    'post_type'      => 'petition',
    'author'         => $user_id,
    'posts_per_page' => 20,
    'post_status'    => array( 'publish', 'draft', 'pending' ),
    'orderby'        => 'date',
    'order'          => 'DESC',
) );

$petitions_count = count( $my_petitions );
$published_count = 0;
$draft_count     = 0;
foreach ( $my_petitions as $p ) {
    if ( 'publish' === $p->post_status ) {
        $published_count++;
    } else {
        $draft_count++;
    }
}

/* --- Signatures received (from table when available, else meta) --- */
$total_signatures_received = 0;
$sig_table_ok = false;
if ( function_exists( 'sp_petitions_table' ) ) {
    global $wpdb;
    $sig_table = sp_petitions_table();
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery
    $exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $sig_table ) );
    if ( $exists === $sig_table ) {
        $sig_table_ok = true;
    }
}
if ( $sig_table_ok && ! empty( $my_petitions ) ) {
    global $wpdb;
    $ids          = array_map( 'intval', wp_list_pluck( $my_petitions, 'ID' ) );
    $placeholders = implode( ',', array_fill( 0, count( $ids ), '%d' ) );
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery
    $total_signatures_received = (int) $wpdb->get_var(
        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
        $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}sampreshan_signatures WHERE petition_id IN ($placeholders)", $ids )
    );
} else {
    foreach ( $my_petitions as $p ) {
        $total_signatures_received += (int) get_post_meta( $p->ID, 'sampreshan_signatures', true );
    }
}

/* --- Petitions I signed --- */
$signed_count = 0;
$signed_recent = array();
if ( $sig_table_ok ) {
    global $wpdb;
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery
    $signed_count = (int) $wpdb->get_var(
        $wpdb->prepare( "SELECT COUNT(DISTINCT petition_id) FROM {$wpdb->prefix}sampreshan_signatures WHERE user_id = %d", $user_id )
    );
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery
    $signed_recent = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT s.petition_id, s.created_at, p.post_title
               FROM {$wpdb->prefix}sampreshan_signatures s
               JOIN {$wpdb->posts} p ON p.ID = s.petition_id
              WHERE s.user_id = %d AND p.post_status = 'publish'
              ORDER BY s.created_at DESC LIMIT 5",
            $user_id
        )
    );
}

/* --- Recent supporters on my petitions --- */
$recent_signatures = array();
if ( $sig_table_ok && ! empty( $my_petitions ) && function_exists( 'sp_petition_get_signatures' ) ) {
    $first = $my_petitions[0];
    // Collect latest 8 across my petitions (simple + safe: query per petition, merge, sort).
    $pool = array();
    foreach ( array_slice( $my_petitions, 0, 6 ) as $mp ) {
        $rows = sp_petition_get_signatures( array( 'petition_id' => $mp->ID, 'per_page' => 4, 'page' => 1 ) );
        if ( $rows ) {
            foreach ( $rows as $r ) {
                $r->petition_title = $mp->post_title;
                $r->petition_id    = $mp->ID;
                $pool[] = $r;
            }
        }
    }
    usort( $pool, function ( $a, $b ) {
        return strcmp( $b->created_at, $a->created_at );
    } );
    $recent_signatures = array_slice( $pool, 0, 8 );
}

$avatar_url  = get_avatar_url( $user_id, array( 'size' => 160 ) );
$member_since = date_i18n( 'M Y', strtotime( $current_user->user_registered ) );
$profile_url  = home_url( '/profile/' );
$petitions_url = home_url( '/start-a-petition/' );
$browse_url    = home_url( '/petitions/' );
$my_url        = home_url( '/my-petitions/' );
$signed_url    = home_url( '/signed-petitions/' );
$settings_url  = home_url( '/settings/' );
$guidelines_url = home_url( '/community-guidelines/' );
$logout_url    = wp_logout_url( home_url( '/' ) );

/* --- Truth Social tools: feed, people, notifications, messages --- */
$feed_url      = function_exists( 'sp_feed_url' ) ? sp_feed_url() : home_url( '/feed-2/' );
$community_url = home_url( '/community/' );
$bp_domain     = function_exists( 'bp_loggedin_user_domain' ) ? (string) bp_loggedin_user_domain() : '';
$notif_url     = ( '' !== $bp_domain && function_exists( 'bp_is_active' ) && bp_is_active( 'notifications' ) ) ? trailingslashit( $bp_domain ) . 'notifications/' : '';
$messages_url  = ( '' !== $bp_domain && function_exists( 'bp_is_active' ) && bp_is_active( 'messages' ) ) ? trailingslashit( $bp_domain ) . 'messages/' : '';

/* --- Change.org tool: amplify the newest live petition (share toolkit) --- */
$share_candidates = get_posts( array(
    'post_type'      => 'petition',
    'author'         => $user_id,
    'post_status'    => 'publish',
    'posts_per_page' => 1,
    'orderby'        => 'date',
    'order'          => 'DESC',
    'fields'         => 'ids',
) );
$share_pid   = ! empty( $share_candidates ) ? (int) $share_candidates[0] : 0;
$share_url   = $share_pid > 0 ? (string) get_permalink( $share_pid ) : '';
$share_title = $share_pid > 0 ? (string) get_the_title( $share_pid ) : '';
?>

<main class="sp-page sp-dashboard" role="main">
    <div class="sp-dashboard__inner">

        <!-- PROFILE BAR -->
        <section class="sp-dash-top" aria-label="Your profile">
            <div class="sp-dash-id">
                <div class="sp-dash-avatar">
                    <?php if ( $avatar_url ) : ?>
                        <img src="<?php echo esc_url( $avatar_url ); ?>" alt="<?php echo esc_attr( $current_user->display_name ); ?>" width="72" height="72" />
                    <?php else : ?>
                        <span class="sp-dash-avatar__initial" aria-hidden="true"><?php echo esc_html( mb_substr( $current_user->display_name, 0, 1 ) ); ?></span>
                    <?php endif; ?>
                </div>
                <div class="sp-dash-id__text">
                    <p class="sp-dash-eyebrow"><?php esc_html_e( 'My Dashboard', 'sampreshan-child' ); ?></p>
                    <h1 class="sp-dash-name"><?php echo esc_html( $current_user->display_name ); ?></h1>
                    <p class="sp-dash-meta">@<?php echo esc_html( $current_user->user_nicename ); ?> &middot; <?php esc_html_e( 'Member since', 'sampreshan-child' ); ?> <?php echo esc_html( $member_since ); ?></p>
                </div>
            </div>
            <div class="sp-dash-top__actions">
                <a class="btn btn--primary" href="<?php echo esc_url( $petitions_url ); ?>">
                    <?php sp_icon_auto( 'plus', 'sp-icon--sm', '' ); ?>
                    <?php esc_html_e( 'New Petition', 'sampreshan-child' ); ?>
                </a>
                <a class="btn btn--ghost" href="<?php echo esc_url( $profile_url ); ?>"><?php esc_html_e( 'View Profile', 'sampreshan-child' ); ?></a>
                <button id="sp-theme-toggle" class="btn btn--ghost sp-theme-toggle" type="button" aria-pressed="false" title="<?php esc_attr_e( 'Dark mode', 'sampreshan-child' ); ?>">
                    <?php sp_icon_auto( 'spark', 'sp-icon--sm', '' ); ?>
                    <span class="sp-theme-toggle__label"><?php esc_html_e( 'Dark mode', 'sampreshan-child' ); ?></span>
                </button>
                <a class="btn btn--ghost" href="<?php echo esc_url( $logout_url ); ?>"><?php esc_html_e( 'Log out', 'sampreshan-child' ); ?></a>
            </div>
        </section>

        <!-- STATS -->
        <section class="sp-dash-stats" aria-label="Your statistics">
            <div class="sp-dash-stat">
                <span class="sp-dash-stat__icon sp-dash-stat__icon--saffron" aria-hidden="true"><?php sp_icon_auto( 'petition', 'sp-icon--md', '' ); ?></span>
                <span class="sp-dash-stat__num" data-count="<?php echo esc_attr( $petitions_count ); ?>"><?php echo esc_html( number_format_i18n( $petitions_count ) ); ?></span>
                <span class="sp-dash-stat__label"><?php esc_html_e( 'My petitions', 'sampreshan-child' ); ?></span>
            </div>
            <div class="sp-dash-stat">
                <span class="sp-dash-stat__icon sp-dash-stat__icon--rose" aria-hidden="true"><?php sp_icon_auto( 'hand', 'sp-icon--md', '' ); ?></span>
                <span class="sp-dash-stat__num" data-count="<?php echo esc_attr( $total_signatures_received ); ?>"><?php echo esc_html( number_format_i18n( $total_signatures_received ) ); ?></span>
                <span class="sp-dash-stat__label"><?php esc_html_e( 'Signatures received', 'sampreshan-child' ); ?></span>
            </div>
            <div class="sp-dash-stat">
                <span class="sp-dash-stat__icon sp-dash-stat__icon--blue" aria-hidden="true"><?php sp_icon_auto( 'check', 'sp-icon--md', '' ); ?></span>
                <span class="sp-dash-stat__num" data-count="<?php echo esc_attr( $signed_count ); ?>"><?php echo esc_html( number_format_i18n( $signed_count ) ); ?></span>
                <span class="sp-dash-stat__label"><?php esc_html_e( 'Petitions signed', 'sampreshan-child' ); ?></span>
            </div>
            <div class="sp-dash-stat">
                <span class="sp-dash-stat__icon sp-dash-stat__icon--green" aria-hidden="true"><?php sp_icon_auto( 'verified', 'sp-icon--md', '' ); ?></span>
                <span class="sp-dash-stat__num" data-count="<?php echo esc_attr( $published_count ); ?>"><?php echo esc_html( number_format_i18n( $published_count ) ); ?></span>
                <span class="sp-dash-stat__label"><?php esc_html_e( 'Published', 'sampreshan-child' ); ?></span>
            </div>
        </section>

        <div class="sp-dash-grid">

            <!-- MY PETITIONS -->
            <section class="sp-dash-card" aria-labelledby="sp-dash-petitions-h">
                <div class="sp-dash-card__head">
                    <div>
                        <h2 class="sp-dash-card__title" id="sp-dash-petitions-h"><?php sp_icon_auto( 'petition', 'sp-icon--sm sp-icon--saffron', '' ); ?> <?php esc_html_e( 'My petitions', 'sampreshan-child' ); ?></h2>
                        <p class="sp-dash-card__sub">
                            <?php
                            printf(
                                /* translators: 1: published, 2: drafts */
                                esc_html__( '%1$s published · %2$s in drafts', 'sampreshan-child' ),
                                esc_html( number_format_i18n( $published_count ) ),
                                esc_html( number_format_i18n( $draft_count ) )
                            );
                            ?>
                        </p>
                    </div>
                    <div class="sp-dash-filter" role="tablist" aria-label="Filter petitions">
                        <button class="sp-dash-filter__btn is-active" type="button" data-filter="all"><?php esc_html_e( 'All', 'sampreshan-child' ); ?></button>
                        <button class="sp-dash-filter__btn" type="button" data-filter="publish"><?php esc_html_e( 'Published', 'sampreshan-child' ); ?></button>
                        <button class="sp-dash-filter__btn" type="button" data-filter="draft"><?php esc_html_e( 'Drafts', 'sampreshan-child' ); ?></button>
                    </div>
                </div>

                <?php if ( empty( $my_petitions ) ) : ?>
                    <div class="sp-dash-empty">
                        <span class="sp-dash-empty__icon" aria-hidden="true"><?php sp_icon_auto( 'megaphone', 'sp-icon--xl', '' ); ?></span>
                        <h3 class="sp-dash-empty__title"><?php esc_html_e( 'You have not started a petition yet', 'sampreshan-child' ); ?></h3>
                        <p class="sp-dash-empty__desc"><?php esc_html_e( 'Describe a local issue, set a signature goal, and share it with the community.', 'sampreshan-child' ); ?></p>
                        <a class="btn btn--primary" href="<?php echo esc_url( $petitions_url ); ?>"><?php esc_html_e( 'Start your first petition', 'sampreshan-child' ); ?></a>
                    </div>
                <?php else : ?>
                    <ul class="sp-dash-list" id="sp-dash-list">
                        <?php foreach ( $my_petitions as $petition ) :
                            $pid       = (int) $petition->ID;
                            $sig_count = (int) get_post_meta( $pid, 'sampreshan_signatures', true );
                            $goal      = (int) get_post_meta( $pid, 'sampreshan_goal', true );
                            $status    = get_post_meta( $pid, 'sampreshan_status', true ) ?: 'active';
                            $cover     = get_the_post_thumbnail_url( $pid, 'thumbnail' );
                            $progress  = $goal > 0 ? min( 100, round( ( $sig_count / $goal ) * 100 ) ) : 0;
                            $terms     = get_the_terms( $pid, 'cause_category' );
                            $cause     = ( $terms && ! is_wp_error( $terms ) ) ? $terms[0]->name : '';
                            $pet_url   = get_permalink( $pid );
                            $is_live   = 'publish' === $petition->post_status;
                        ?>
                            <li class="sp-dash-row" data-status="<?php echo $is_live ? 'publish' : 'draft'; ?>">
                                <a class="sp-dash-row__thumb" href="<?php echo esc_url( $pet_url ); ?>" tabindex="-1" aria-hidden="true">
                                    <?php if ( $cover ) : ?>
                                        <img src="<?php echo esc_url( $cover ); ?>" alt="" loading="lazy" width="96" height="96" />
                                    <?php else : ?>
                                        <span class="sp-dash-row__thumb--ph"><?php sp_icon_auto( 'petition', 'sp-icon--md', '' ); ?></span>
                                    <?php endif; ?>
                                </a>
                                <div class="sp-dash-row__main">
                                    <div class="sp-dash-row__topline">
                                        <?php if ( $cause ) : ?>
                                            <span class="sp-dash-cause"><?php echo esc_html( $cause ); ?></span>
                                        <?php endif; ?>
                                        <span class="sp-dash-pill sp-dash-pill--<?php echo $is_live ? esc_attr( $status ) : 'draft'; ?>">
                                            <?php echo $is_live ? esc_html( ucfirst( $status ) ) : esc_html__( 'Draft', 'sampreshan-child' ); ?>
                                        </span>
                                    </div>
                                    <h3 class="sp-dash-row__title">
                                        <a href="<?php echo esc_url( $pet_url ); ?>"><?php echo esc_html( $petition->post_title ); ?></a>
                                    </h3>
                                    <p class="sp-dash-row__meta">
                                        <?php echo esc_html( get_the_date( '', $petition ) ); ?> &middot;
                                        <?php echo esc_html( sprintf( _n( '%s signature', '%s signatures', $sig_count, 'sampreshan-child' ), number_format_i18n( $sig_count ) ) ); ?>
                                        <?php if ( $goal > 0 ) : ?>
                                            &middot; <?php echo esc_html( sprintf( __( 'goal %s', 'sampreshan-child' ), number_format_i18n( $goal ) ) ); ?>
                                        <?php endif; ?>
                                    </p>
                                    <div class="sp-dash-bar" role="progressbar" aria-valuenow="<?php echo esc_attr( $progress ); ?>" aria-valuemin="0" aria-valuemax="100" aria-label="Signature progress">
                                        <span class="sp-dash-bar__fill" style="width: <?php echo esc_attr( $progress ); ?>%"></span>
                                    </div>
                                    <div class="sp-dash-row__actions">
                                        <a class="sp-dash-link" href="<?php echo esc_url( $pet_url ); ?>"><?php esc_html_e( 'View', 'sampreshan-child' ); ?></a>
                                        <a class="sp-dash-link" href="<?php echo esc_url( get_edit_post_link( $pid, 'raw' ) ); ?>"><?php esc_html_e( 'Edit', 'sampreshan-child' ); ?></a>
                                        <button class="sp-dash-link sp-share-btn" type="button" data-url="<?php echo esc_url( $pet_url ); ?>" data-title="<?php echo esc_attr( $petition->post_title ); ?>"><?php esc_html_e( 'Share', 'sampreshan-child' ); ?></button>
                                        <?php if ( $is_live && 'victory' !== $status && ( (int) $petition->post_author === $user_id || current_user_can( 'edit_petition', $pid ) ) ) : ?>
                                            <button class="sp-dash-link sp-victory-btn" type="button" data-petition-id="<?php echo esc_attr( $pid ); ?>"><?php esc_html_e( 'Declare victory', 'sampreshan-child' ); ?></button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <p class="sp-dash-list__empty" id="sp-dash-list-empty" hidden><?php esc_html_e( 'Nothing in this view yet.', 'sampreshan-child' ); ?></p>
                <?php endif; ?>
            </section>

            <!-- SIDE RAIL -->
            <div class="sp-dash-rail">

                <section class="sp-dash-card sp-dash-card--pad" aria-labelledby="sp-dash-actions-h">
                    <h2 class="sp-dash-card__title" id="sp-dash-actions-h"><?php esc_html_e( 'Quick actions', 'sampreshan-child' ); ?></h2>
                    <ul class="sp-dash-actions">
                        <li>
                            <a class="sp-dash-action" href="<?php echo esc_url( $petitions_url ); ?>">
                                <span class="sp-dash-action__icon sp-dash-action__icon--saffron" aria-hidden="true"><?php sp_icon_auto( 'plus', 'sp-icon--sm', '' ); ?></span>
                                <span class="sp-dash-action__text"><strong><?php esc_html_e( 'Start a petition', 'sampreshan-child' ); ?></strong><small><?php esc_html_e( 'Raise a new cause', 'sampreshan-child' ); ?></small></span>
                                <span aria-hidden="true">&rarr;</span>
                            </a>
                        </li>
                        <li>
                            <a class="sp-dash-action" href="<?php echo esc_url( $my_url ); ?>">
                                <span class="sp-dash-action__icon sp-dash-action__icon--blue" aria-hidden="true"><?php sp_icon_auto( 'petition', 'sp-icon--sm', '' ); ?></span>
                                <span class="sp-dash-action__text"><strong><?php esc_html_e( 'My petitions', 'sampreshan-child' ); ?></strong><small><?php esc_html_e( 'Track and edit', 'sampreshan-child' ); ?></small></span>
                                <span aria-hidden="true">&rarr;</span>
                            </a>
                        </li>
                        <li>
                            <a class="sp-dash-action" href="<?php echo esc_url( $signed_url ); ?>">
                                <span class="sp-dash-action__icon sp-dash-action__icon--rose" aria-hidden="true"><?php sp_icon_auto( 'heart', 'sp-icon--sm', '' ); ?></span>
                                <span class="sp-dash-action__text"><strong><?php esc_html_e( 'Signed petitions', 'sampreshan-child' ); ?></strong><small><?php esc_html_e( 'Causes you support', 'sampreshan-child' ); ?></small></span>
                                <span aria-hidden="true">&rarr;</span>
                            </a>
                        </li>
                        <li>
                            <a class="sp-dash-action" href="<?php echo esc_url( $browse_url ); ?>">
                                <span class="sp-dash-action__icon sp-dash-action__icon--saffron" aria-hidden="true"><?php sp_icon_auto( 'search', 'sp-icon--sm', '' ); ?></span>
                                <span class="sp-dash-action__text"><strong><?php esc_html_e( 'Discover petitions', 'sampreshan-child' ); ?></strong><small><?php esc_html_e( 'Find causes to sign', 'sampreshan-child' ); ?></small></span>
                                <span aria-hidden="true">&rarr;</span>
                            </a>
                        </li>
                        <li>
                            <a class="sp-dash-action" href="<?php echo esc_url( $feed_url ); ?>">
                                <span class="sp-dash-action__icon sp-dash-action__icon--blue" aria-hidden="true"><?php sp_icon_auto( 'feed', 'sp-icon--sm', '' ); ?></span>
                                <span class="sp-dash-action__text"><strong><?php esc_html_e( 'Community feed', 'sampreshan-child' ); ?></strong><small><?php esc_html_e( 'Posts from the sangha', 'sampreshan-child' ); ?></small></span>
                                <span aria-hidden="true">&rarr;</span>
                            </a>
                        </li>
                        <li>
                            <a class="sp-dash-action" href="<?php echo esc_url( $community_url ); ?>">
                                <span class="sp-dash-action__icon sp-dash-action__icon--rose" aria-hidden="true"><?php sp_icon_auto( 'network', 'sp-icon--sm', '' ); ?></span>
                                <span class="sp-dash-action__text"><strong><?php esc_html_e( 'People & groups', 'sampreshan-child' ); ?></strong><small><?php esc_html_e( 'Follow the community', 'sampreshan-child' ); ?></small></span>
                                <span aria-hidden="true">&rarr;</span>
                            </a>
                        </li>
                        <?php if ( '' !== $notif_url ) : ?>
                            <li>
                                <a class="sp-dash-action" href="<?php echo esc_url( $notif_url ); ?>">
                                    <span class="sp-dash-action__icon sp-dash-action__icon--saffron" aria-hidden="true"><?php sp_icon_auto( 'bell', 'sp-icon--sm', '' ); ?></span>
                                    <span class="sp-dash-action__text"><strong><?php esc_html_e( 'Notifications', 'sampreshan-child' ); ?></strong><small><?php esc_html_e( 'Mentions and updates', 'sampreshan-child' ); ?></small></span>
                                    <span aria-hidden="true">&rarr;</span>
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if ( '' !== $messages_url ) : ?>
                            <li>
                                <a class="sp-dash-action" href="<?php echo esc_url( $messages_url ); ?>">
                                    <span class="sp-dash-action__icon sp-dash-action__icon--blue" aria-hidden="true"><?php sp_icon_auto( 'comment', 'sp-icon--sm', '' ); ?></span>
                                    <span class="sp-dash-action__text"><strong><?php esc_html_e( 'Messages', 'sampreshan-child' ); ?></strong><small><?php esc_html_e( 'Private conversations', 'sampreshan-child' ); ?></small></span>
                                    <span aria-hidden="true">&rarr;</span>
                                </a>
                            </li>
                        <?php endif; ?>
                        <li>
                            <a class="sp-dash-action" href="<?php echo esc_url( $profile_url ); ?>">
                                <span class="sp-dash-action__icon sp-dash-action__icon--blue" aria-hidden="true"><?php sp_icon_auto( 'account', 'sp-icon--sm', '' ); ?></span>
                                <span class="sp-dash-action__text"><strong><?php esc_html_e( 'View profile', 'sampreshan-child' ); ?></strong><small><?php esc_html_e( 'How others see you', 'sampreshan-child' ); ?></small></span>
                                <span aria-hidden="true">&rarr;</span>
                            </a>
                        </li>
                        <li>
                            <a class="sp-dash-action" href="<?php echo esc_url( $settings_url ); ?>">
                                <span class="sp-dash-action__icon sp-dash-action__icon--green" aria-hidden="true"><?php sp_icon_auto( 'settings', 'sp-icon--sm', '' ); ?></span>
                                <span class="sp-dash-action__text"><strong><?php esc_html_e( 'Edit settings', 'sampreshan-child' ); ?></strong><small><?php esc_html_e( 'Name, bio and password', 'sampreshan-child' ); ?></small></span>
                                <span aria-hidden="true">&rarr;</span>
                            </a>
                        </li>
                        <li>
                            <a class="sp-dash-action" href="<?php echo esc_url( $guidelines_url ); ?>">
                                <span class="sp-dash-action__icon sp-dash-action__icon--green" aria-hidden="true"><?php sp_icon_auto( 'verified', 'sp-icon--sm', '' ); ?></span>
                                <span class="sp-dash-action__text"><strong><?php esc_html_e( 'Community guidelines', 'sampreshan-child' ); ?></strong><small><?php esc_html_e( 'What is allowed', 'sampreshan-child' ); ?></small></span>
                                <span aria-hidden="true">&rarr;</span>
                            </a>
                        </li>
                    </ul>
                </section>

                <?php if ( '' !== $share_url ) :
                    $wa_url = 'https://wa.me/?text=' . rawurlencode( $share_title . ' ' . $share_url );
                    $x_url  = 'https://x.com/intent/tweet?text=' . rawurlencode( $share_title ) . '&url=' . rawurlencode( $share_url );
                    $fb_url = 'https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode( $share_url );
                ?>
                    <section class="sp-dash-card sp-dash-card--pad sp-dash-share" aria-labelledby="sp-dash-share-h">
                        <h2 class="sp-dash-card__title" id="sp-dash-share-h"><?php sp_icon_auto( 'share', 'sp-icon--sm sp-icon--saffron', '' ); ?> <?php esc_html_e( 'Amplify your cause', 'sampreshan-child' ); ?></h2>
                        <p class="sp-dash-muted sp-dash-share__title"><?php echo esc_html( $share_title ); ?></p>
                        <ul class="sp-dash-share__grid">
                            <li><button class="sp-dash-share__btn" type="button" data-sp-copy="<?php echo esc_attr( $share_url ); ?>"><?php sp_icon_auto( 'link', 'sp-icon--sm', '' ); ?><span><?php esc_html_e( 'Copy link', 'sampreshan-child' ); ?></span></button></li>
                            <li><button class="sp-dash-share__btn" type="button" data-sp-share="<?php echo esc_attr( $share_url ); ?>" data-sp-share-title="<?php echo esc_attr( $share_title ); ?>"><?php sp_icon_auto( 'send', 'sp-icon--sm', '' ); ?><span><?php esc_html_e( 'Share', 'sampreshan-child' ); ?></span></button></li>
                            <li><a class="sp-dash-share__btn" href="<?php echo esc_url( $wa_url ); ?>" target="_blank" rel="noopener"><?php sp_icon_auto( 'comment', 'sp-icon--sm', '' ); ?><span><?php esc_html_e( 'WhatsApp', 'sampreshan-child' ); ?></span></a></li>
                            <li><a class="sp-dash-share__btn" href="<?php echo esc_url( $x_url ); ?>" target="_blank" rel="noopener"><?php sp_icon_auto( 'share', 'sp-icon--sm', '' ); ?><span><?php esc_html_e( 'Post on X', 'sampreshan-child' ); ?></span></a></li>
                            <li><a class="sp-dash-share__btn" href="<?php echo esc_url( $fb_url ); ?>" target="_blank" rel="noopener"><?php sp_icon_auto( 'globe', 'sp-icon--sm', '' ); ?><span><?php esc_html_e( 'Facebook', 'sampreshan-child' ); ?></span></a></li>
                        </ul>
                        <p class="sp-dash-muted sp-dash-share__note"><?php esc_html_e( 'Share your petition with friends and community to gather support faster.', 'sampreshan-child' ); ?></p>
                    </section>
                <?php endif; ?>

                <section class="sp-dash-card sp-dash-card--pad" aria-labelledby="sp-dash-support-h">
                    <h2 class="sp-dash-card__title" id="sp-dash-support-h"><?php sp_icon_auto( 'heart', 'sp-icon--sm sp-icon--saffron', '' ); ?> <?php esc_html_e( 'Latest supporters', 'sampreshan-child' ); ?></h2>
                    <?php if ( empty( $recent_signatures ) ) : ?>
                        <p class="sp-dash-muted"><?php esc_html_e( 'No signatures yet. Share a petition and supporters will appear here.', 'sampreshan-child' ); ?></p>
                    <?php else : ?>
                        <ul class="sp-dash-activity">
                            <?php foreach ( $recent_signatures as $sig ) :
                                $is_anon = ! empty( $sig->is_anonymous );
                                $name    = $is_anon ? __( 'Anonymous', 'sampreshan-child' ) : ( $sig->display_name ?: $sig->user_display );
                                $ago     = human_time_diff( strtotime( $sig->created_at ), current_time( 'timestamp' ) );
                            ?>
                                <li class="sp-dash-activity__item">
                                    <span class="sp-dash-activity__avatar" aria-hidden="true">
                                        <?php if ( ! $is_anon && ! empty( $sig->user_id ) ) : ?>
                                            <img src="<?php echo esc_url( get_avatar_url( (int) $sig->user_id, array( 'size' => 64 ) ) ); ?>" alt="" width="32" height="32" loading="lazy" />
                                        <?php else : ?>
                                            <?php sp_icon_auto( 'account', 'sp-icon--sm', '' ); ?>
                                        <?php endif; ?>
                                    </span>
                                    <span class="sp-dash-activity__text">
                                        <strong><?php echo esc_html( $name ); ?></strong>
                                        <?php esc_html_e( 'signed', 'sampreshan-child' ); ?>
                                        <a href="<?php echo esc_url( get_permalink( (int) $sig->petition_id ) ); ?>"><?php echo esc_html( $sig->petition_title ); ?></a>
                                        <small><?php echo esc_html( $ago ); ?> <?php esc_html_e( 'ago', 'sampreshan-child' ); ?></small>
                                    </span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </section>

                <?php if ( ! empty( $signed_recent ) ) : ?>
                    <section class="sp-dash-card sp-dash-card--pad" aria-labelledby="sp-dash-signed-h">
                        <h2 class="sp-dash-card__title" id="sp-dash-signed-h"><?php sp_icon_auto( 'check', 'sp-icon--sm sp-icon--saffron', '' ); ?> <?php esc_html_e( 'Recently signed by you', 'sampreshan-child' ); ?></h2>
                        <ul class="sp-dash-signed">
                            <?php foreach ( $signed_recent as $s ) : ?>
                                <li>
                                    <a href="<?php echo esc_url( get_permalink( (int) $s->petition_id ) ); ?>"><?php echo esc_html( $s->post_title ); ?></a>
                                    <small><?php echo esc_html( human_time_diff( strtotime( $s->created_at ), current_time( 'timestamp' ) ) ); ?> <?php esc_html_e( 'ago', 'sampreshan-child' ); ?></small>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </section>
                <?php endif; ?>

            </div>
        </div>
    </div>
</main>

<script>
(function () {
    /* Victory declarations (petition starter only, with confirm). */
    document.querySelectorAll('.sp-victory-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            if (!window.confirm('Declare victory for this petition? Signing will close.')) { return; }
            btn.disabled = true;
            var body = new URLSearchParams();
            body.append('action', 'sp_petition_victory');
            body.append('nonce', (window.SampreshanPetition && window.SampreshanPetition.nonce) || '');
            body.append('petition_id', btn.getAttribute('data-petition-id') || '0');
            fetch((window.SampreshanPetition && window.SampreshanPetition.ajaxUrl) || '/wp-admin/admin-ajax.php', {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: body.toString()
            })
                .then(function (r) { return r.json(); })
                .then(function (res) {
                    if (res && res.success) { window.location.reload(); return; }
                    btn.disabled = false;
                    alert((res && res.data && res.data.message) || 'Something went wrong.');
                })
                .catch(function () {
                    btn.disabled = false;
                    alert('Network error. Please try again.');
                });
        });
    });

    /* Copy-link + native-share handlers (Amplify toolkit). */
    document.querySelectorAll('[data-sp-copy]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var url = btn.getAttribute('data-sp-copy') || '';
            var done = function () {
                var label = btn.querySelector('span');
                if (!label) { return; }
                var orig = label.textContent;
                label.textContent = 'Copied';
                setTimeout(function () { label.textContent = orig; }, 1600);
            };
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(url).then(done, function () { fallbackCopy(url); done(); });
            } else { fallbackCopy(url); done(); }
        });
    });
    function fallbackCopy(text) {
        var ta = document.createElement('textarea');
        ta.value = text;
        ta.style.position = 'fixed';
        ta.style.opacity = '0';
        document.body.appendChild(ta);
        ta.select();
        try { document.execCommand('copy'); } catch (e) { /* ignore */ }
        document.body.removeChild(ta);
    }
    document.querySelectorAll('[data-sp-share]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var url = btn.getAttribute('data-sp-share') || location.href;
            var title = btn.getAttribute('data-sp-share-title') || document.title;
            if (navigator.share) {
                navigator.share({ title: title, text: title, url: url }).catch(function () { /* dismissed */ });
            } else if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(url);
                var label = btn.querySelector('span');
                if (label) {
                    var orig = label.textContent;
                    label.textContent = 'Link copied';
                    setTimeout(function () { label.textContent = orig; }, 1600);
                }
            }
        });
    });

    var btns = document.querySelectorAll('.sp-dash-filter__btn');
    var list = document.getElementById('sp-dash-list');
    var empty = document.getElementById('sp-dash-list-empty');
    if (!btns.length || !list) { return; }
    btns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            btns.forEach(function (b) { b.classList.remove('is-active'); });
            btn.classList.add('is-active');
            var f = btn.getAttribute('data-filter');
            var rows = list.querySelectorAll('.sp-dash-row');
            var visible = 0;
            rows.forEach(function (row) {
                var show = (f === 'all') || (row.getAttribute('data-status') === f);
                row.style.display = show ? '' : 'none';
                if (show) { visible++; }
            });
            if (empty) { empty.hidden = visible !== 0; }
        });
    });
})();
</script>

<?php get_footer(); ?>
