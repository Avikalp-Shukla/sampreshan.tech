<?php
/**
 * Template Name: Signed Petitions
 *
 * Petitions the logged-in user has signed.
 *
 * @package SampreShan_Child
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! is_user_logged_in() ) {
    wp_safe_redirect( wp_login_url( home_url( '/signed-petitions/' ) ) );
    exit;
}

get_header();

$user_id = get_current_user_id();
$signed  = array();

if ( function_exists( 'sp_petitions_table' ) ) {
    global $wpdb;
    $table = sp_petitions_table();
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery
    $exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) );
    if ( $exists === $table ) {
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery
        $signed = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT s.petition_id, s.created_at, p.post_title
                   FROM $table s
                   JOIN {$wpdb->posts} p ON p.ID = s.petition_id
                  WHERE s.user_id = %d AND p.post_status = 'publish'
                  ORDER BY s.created_at DESC LIMIT 50",
                $user_id
            )
        );
    }
}

$browse_url = home_url( '/petitions/' );
?>

<main class="sp-page sp-users" role="main">
    <header class="sp-users__hero">
        <p class="sp-section__eyebrow"><?php esc_html_e( 'Your support', 'sampreshan-child' ); ?></p>
        <h1 class="sp-users__title"><?php sp_icon_auto( 'check', 'sp-icon--md sp-icon--saffron', '' ); ?> <?php esc_html_e( 'Issues You Supported', 'sampreshan-child' ); ?></h1>
        <p class="sp-users__sub">
            <?php
            printf(
                /* translators: %s = count */
                esc_html__( 'You have supported %s so far. Every signature counts.', 'sampreshan-child' ),
                esc_html( number_format_i18n( count( $signed ) ) )
            );
            ?>
        </p>
        <div class="sp-users__hero-actions">
            <a class="btn btn--primary" href="<?php echo esc_url( $browse_url ); ?>"><?php esc_html_e( 'Discover More', 'sampreshan-child' ); ?></a>
        </div>
    </header>

    <?php if ( ! empty( $signed ) ) : ?>
        <ul class="sp-signed-list sp-dash-card">
            <?php foreach ( $signed as $s ) :
                $pid       = (int) $s->petition_id;
                $sig_count = function_exists( 'sp_petition_signature_count' ) ? (int) sp_petition_signature_count( $pid ) : 0;
                $cover     = get_the_post_thumbnail_url( $pid, 'thumbnail' );
            ?>
                <li class="sp-dash-row">
                    <a class="sp-dash-row__thumb" href="<?php echo esc_url( get_permalink( $pid ) ); ?>" tabindex="-1" aria-hidden="true">
                        <?php if ( $cover ) : ?>
                            <img src="<?php echo esc_url( $cover ); ?>" alt="" loading="lazy" width="84" height="84" />
                        <?php else : ?>
                            <span class="sp-dash-row__thumb--ph"><?php sp_icon_e( 'check', 'sp-icon--md', '' ); ?></span>
                        <?php endif; ?>
                    </a>
                    <div class="sp-dash-row__main">
                        <span class="sp-dash-pill sp-dash-pill--active"><?php esc_html_e( 'Signed', 'sampreshan-child' ); ?></span>
                        <h2 class="sp-dash-row__title"><a href="<?php echo esc_url( get_permalink( $pid ) ); ?>"><?php echo esc_html( $s->post_title ); ?></a></h2>
                        <p class="sp-dash-row__meta">
                            <?php echo esc_html( sprintf( __( 'You signed %s ago', 'sampreshan-child' ), human_time_diff( strtotime( $s->created_at ), current_time( 'timestamp' ) ) ) ); ?> &middot;
                            <?php echo esc_html( sprintf( _n( '%s total signature', '%s total signatures', $sig_count, 'sampreshan-child' ), number_format_i18n( $sig_count ) ) ); ?>
                        </p>
                        <div class="sp-dash-row__actions">
                            <a class="sp-dash-link" href="<?php echo esc_url( get_permalink( $pid ) ); ?>"><?php esc_html_e( 'View', 'sampreshan-child' ); ?></a>
                            <button class="sp-dash-link sp-share-btn" type="button" data-url="<?php echo esc_url( get_permalink( $pid ) ); ?>" data-title="<?php echo esc_attr( $s->post_title ); ?>"><?php esc_html_e( 'Share', 'sampreshan-child' ); ?></button>
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else : ?>
        <div class="sp-dash-empty sp-dash-card">
            <span class="sp-dash-empty__icon" aria-hidden="true"><?php sp_icon_e( 'heart', 'sp-icon--xl', '' ); ?></span>
            <h3 class="sp-dash-empty__title"><?php esc_html_e( 'Nothing signed yet', 'sampreshan-child' ); ?></h3>
            <p class="sp-dash-empty__desc"><?php esc_html_e( 'Browse community causes and add your first signature.', 'sampreshan-child' ); ?></p>
            <a class="btn btn--primary" href="<?php echo esc_url( $browse_url ); ?>"><?php esc_html_e( 'Browse Petitions', 'sampreshan-child' ); ?></a>
        </div>
    <?php endif; ?>
</main>

<?php get_footer(); ?>
