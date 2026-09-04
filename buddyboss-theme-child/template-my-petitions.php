<?php
/**
 * Template Name: My Petitions
 *
 * Logged-in user's own petitions (published + drafts).
 *
 * @package SampreShan_Child
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! is_user_logged_in() ) {
    wp_safe_redirect( wp_login_url( home_url( '/my-petitions/' ) ) );
    exit;
}

get_header();

$user_id = get_current_user_id();
$paged   = max( 1, (int) get_query_var( 'paged', 1 ) );

$q = new WP_Query( array(
    'post_type'      => 'petition',
    'author'         => $user_id,
    'post_status'    => array( 'publish', 'draft', 'pending' ),
    'posts_per_page' => 12,
    'paged'          => $paged,
    'orderby'        => 'date',
    'order'          => 'DESC',
) );

$start_url = home_url( '/start-a-petition/' );
$dash_url  = home_url( '/dashboard/' );
?>

<main class="sp-page sp-users" role="main">
    <header class="sp-users__hero">
        <p class="sp-section__eyebrow"><?php esc_html_e( 'Your voice', 'sampreshan-child' ); ?></p>
        <h1 class="sp-users__title"><?php sp_icon_auto( 'petition', 'sp-icon--md sp-icon--saffron', '' ); ?> <?php esc_html_e( 'My Petitions', 'sampreshan-child' ); ?></h1>
        <p class="sp-users__sub"><?php esc_html_e( 'Everything you have started — track signatures, edit drafts, and share.', 'sampreshan-child' ); ?></p>
        <div class="sp-users__hero-actions">
            <a class="btn btn--primary" href="<?php echo esc_url( $start_url ); ?>">
                <?php sp_icon_e( 'plus', 'sp-icon--sm', '' ); ?>
                <?php esc_html_e( 'New Petition', 'sampreshan-child' ); ?>
            </a>
            <a class="btn btn--ghost" href="<?php echo esc_url( $dash_url ); ?>"><?php esc_html_e( 'Back to Dashboard', 'sampreshan-child' ); ?></a>
        </div>
    </header>

    <?php if ( $q->have_posts() ) : ?>
        <ul class="sp-dash-list sp-dash-card">
            <?php while ( $q->have_posts() ) : $q->the_post();
                $pid       = get_the_ID();
                $sig_count = (int) get_post_meta( $pid, 'sampreshan_signatures', true );
                $goal      = (int) get_post_meta( $pid, 'sampreshan_goal', true );
                $status    = get_post_meta( $pid, 'sampreshan_status', true ) ?: 'active';
                $progress  = $goal > 0 ? min( 100, round( ( $sig_count / $goal ) * 100 ) ) : 0;
                $cover     = get_the_post_thumbnail_url( $pid, 'thumbnail' );
                $is_live   = 'publish' === get_post_status();
            ?>
                <li class="sp-dash-row">
                    <a class="sp-dash-row__thumb" href="<?php echo esc_url( get_permalink() ); ?>" tabindex="-1" aria-hidden="true">
                        <?php if ( $cover ) : ?>
                            <img src="<?php echo esc_url( $cover ); ?>" alt="" loading="lazy" width="84" height="84" />
                        <?php else : ?>
                            <span class="sp-dash-row__thumb--ph"><?php sp_icon_e( 'petition', 'sp-icon--md', '' ); ?></span>
                        <?php endif; ?>
                    </a>
                    <div class="sp-dash-row__main">
                        <div class="sp-dash-row__topline">
                            <span class="sp-dash-pill sp-dash-pill--<?php echo $is_live ? esc_attr( $status ) : 'draft'; ?>">
                                <?php echo $is_live ? esc_html( ucfirst( $status ) ) : esc_html__( 'Draft', 'sampreshan-child' ); ?>
                            </span>
                            <span class="sp-dash-row__meta"><?php echo esc_html( get_the_date() ); ?></span>
                        </div>
                        <h2 class="sp-dash-row__title"><a href="<?php echo esc_url( get_permalink() ); ?>"><?php the_title(); ?></a></h2>
                        <p class="sp-dash-row__meta">
                            <?php echo esc_html( sprintf( _n( '%s signature', '%s signatures', $sig_count, 'sampreshan-child' ), number_format_i18n( $sig_count ) ) ); ?>
                            <?php if ( $goal > 0 ) : ?>&middot; <?php echo esc_html( sprintf( __( 'goal %s', 'sampreshan-child' ), number_format_i18n( $goal ) ) ); ?><?php endif; ?>
                        </p>
                        <div class="sp-dash-bar"><span class="sp-dash-bar__fill" style="width: <?php echo esc_attr( $progress ); ?>%"></span></div>
                        <div class="sp-dash-row__actions">
                            <a class="sp-dash-link" href="<?php echo esc_url( get_permalink() ); ?>"><?php esc_html_e( 'View', 'sampreshan-child' ); ?></a>
                            <a class="sp-dash-link" href="<?php echo esc_url( get_edit_post_link( $pid, 'raw' ) ); ?>"><?php esc_html_e( 'Edit', 'sampreshan-child' ); ?></a>
                            <button class="sp-dash-link sp-share-btn" type="button" data-url="<?php echo esc_url( get_permalink() ); ?>" data-title="<?php echo esc_attr( get_the_title() ); ?>"><?php esc_html_e( 'Share', 'sampreshan-child' ); ?></button>
                        </div>
                    </div>
                </li>
            <?php endwhile; wp_reset_postdata(); ?>
        </ul>
        <nav class="sp-users__pagination" aria-label="My petitions pages">
            <?php
            echo paginate_links( array(
                'total'   => (int) $q->max_num_pages,
                'current' => $paged,
                'prev_text' => __( '&larr; Prev', 'sampreshan-child' ),
                'next_text' => __( 'Next &rarr;', 'sampreshan-child' ),
            ) );
            ?>
        </nav>
    <?php else : ?>
        <div class="sp-dash-empty sp-dash-card">
            <span class="sp-dash-empty__icon" aria-hidden="true"><?php sp_icon_e( 'petition', 'sp-icon--xl', '' ); ?></span>
            <h3 class="sp-dash-empty__title"><?php esc_html_e( 'No petitions yet', 'sampreshan-child' ); ?></h3>
            <p class="sp-dash-empty__desc"><?php esc_html_e( 'Start your first petition and it will appear here.', 'sampreshan-child' ); ?></p>
            <a class="btn btn--primary" href="<?php echo esc_url( $start_url ); ?>"><?php esc_html_e( 'Start a Petition', 'sampreshan-child' ); ?></a>
        </div>
    <?php endif; ?>
</main>

<?php get_footer(); ?>
