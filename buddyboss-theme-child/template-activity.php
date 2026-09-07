<?php
/**
 * Template Name: Community Feed
 *
 * Latest petitions + posts in one clean river.
 *
 * @package SampreShan_Child
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

get_header();

$items = new WP_Query( array(
    'post_type'      => array( 'petition', 'post' ),
    'post_status'    => 'publish',
    'posts_per_page' => 15,
    'orderby'        => 'date',
    'order'          => 'DESC',
) );

$start_url = home_url( '/start-a-petition/' );
?>

<main class="sp-page sp-users sp-users--narrow" role="main">
    <header class="sp-users__hero sp-users__hero--center">
        <p class="sp-section__eyebrow"><?php esc_html_e( 'Fresh from the community', 'sampreshan-child' ); ?></p>
        <h1 class="sp-users__title"><?php sp_icon_auto( 'feed', 'sp-icon--md sp-icon--saffron', '' ); ?> <?php esc_html_e( 'Community Feed', 'sampreshan-child' ); ?></h1>
        <p class="sp-users__sub"><?php esc_html_e( 'The newest petitions and stories — sign, share, and join the conversation.', 'sampreshan-child' ); ?></p>
    </header>

    <?php if ( $items->have_posts() ) : ?>
        <div class="sp-feed">
            <?php while ( $items->have_posts() ) : $items->the_post();
                $is_pet  = 'petition' === get_post_type();
                $pid     = get_the_ID();
                $sig     = $is_pet ? (int) get_post_meta( $pid, 'sampreshan_signatures', true ) : 0;
                $goal    = $is_pet ? (int) get_post_meta( $pid, 'sampreshan_goal', true ) : 0;
                $prog    = ( $is_pet && $goal > 0 ) ? min( 100, round( ( $sig / $goal ) * 100 ) ) : 0;
                $aid     = (int) get_post_field( 'post_author', $pid );
                $avatar  = get_avatar_url( $aid, array( 'size' => 80 ) );
            ?>
                <article class="sp-feed__item sp-dash-card sp-dash-card--pad">
                    <div class="sp-feed__head">
                        <span class="sp-feed__avatar" aria-hidden="true">
                            <?php if ( $avatar ) : ?>
                                <img src="<?php echo esc_url( $avatar ); ?>" alt="" width="36" height="36" loading="lazy" />
                            <?php else : ?>
                                <?php sp_icon_auto( 'account', 'sp-icon--sm', '' ); ?>
                            <?php endif; ?>
                        </span>
                        <div class="sp-feed__who">
                            <strong><?php echo esc_html( get_the_author_meta( 'display_name', $aid ) ); ?></strong>
                            <small>
                                <?php echo $is_pet ? esc_html__( 'started a petition', 'sampreshan-child' ) : esc_html__( 'posted a story', 'sampreshan-child' ); ?>
                                &middot; <?php echo esc_html( human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ) ); ?> <?php esc_html_e( 'ago', 'sampreshan-child' ); ?>
                            </small>
                        </div>
                        <span class="sp-dash-pill <?php echo $is_pet ? 'sp-dash-pill--active' : 'sp-dash-pill--draft'; ?>">
                            <?php echo $is_pet ? esc_html__( 'Petition', 'sampreshan-child' ) : esc_html__( 'Story', 'sampreshan-child' ); ?>
                        </span>
                    </div>
                    <h2 class="sp-feed__title"><a href="<?php echo esc_url( get_permalink() ); ?>"><?php the_title(); ?></a></h2>
                    <p class="sp-feed__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt() ?: wp_strip_all_tags( get_the_content() ), 28 ) ); ?></p>
                    <?php if ( $is_pet ) : ?>
                        <div class="sp-dash-bar"><span class="sp-dash-bar__fill" style="width: <?php echo esc_attr( $prog ); ?>%"></span></div>
                        <p class="sp-dash-row__meta">
                            <?php echo esc_html( sprintf( _n( '%s I', '%s Is', $sig, 'sampreshan-child' ), number_format_i18n( $sig ) ) ); ?>
                            <?php if ( $goal > 0 ) : ?>&middot; <?php echo esc_html( sprintf( __( 'goal %s', 'sampreshan-child' ), number_format_i18n( $goal ) ) ); ?><?php endif; ?>
                        </p>
                    <?php endif; ?>
                    <div class="sp-dash-row__actions">
                        <a class="sp-dash-link" href="<?php echo esc_url( get_permalink() ); ?>"><?php echo $is_pet ? esc_html__( 'View & sign →', 'sampreshan-child' ) : esc_html__( 'Read →', 'sampreshan-child' ); ?></a>
                        <button class="sp-dash-link sp-share-btn" type="button" data-url="<?php echo esc_url( get_permalink() ); ?>" data-title="<?php echo esc_attr( get_the_title() ); ?>"><?php esc_html_e( 'Share', 'sampreshan-child' ); ?></button>
                    </div>
                </article>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>
    <?php else : ?>
        <div class="sp-dash-empty sp-dash-card">
            <h3 class="sp-dash-empty__title"><?php esc_html_e( 'Nothing here yet', 'sampreshan-child' ); ?></h3>
            <p class="sp-dash-empty__desc"><?php esc_html_e( 'Be the first to start a conversation.', 'sampreshan-child' ); ?></p>
            <a class="btn btn--primary" href="<?php echo esc_url( $start_url ); ?>"><?php esc_html_e( 'Start a Petition', 'sampreshan-child' ); ?></a>
        </div>
    <?php endif; ?>
</main>

<?php get_footer(); ?>
