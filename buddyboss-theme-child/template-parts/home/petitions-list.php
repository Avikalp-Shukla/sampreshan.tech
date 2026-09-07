<?php
/**
 * Home: Active Petitions Grid — Change.org fusion
 * Shared card component, real petition data only.
 *
 * @package SampreShan_Child
 */

$petitions = new WP_Query( array(
    'post_type'      => 'petition',
    'post_status'    => 'publish',
    'posts_per_page' => 6,
    'orderby'        => 'date',
    'order'          => 'DESC',
    'meta_query'     => array(
        'relation' => 'OR',
        array( 'key' => 'sampreshan_status', 'value' => 'active' ),
        array( 'key' => 'sampreshan_status', 'compare' => 'NOT EXISTS' ),
    ),
) );

$all_url   = home_url( '/petitions/' );
$start_url = home_url( '/start-a-petition/' );
?>
<section class="home-section" aria-labelledby="home-petitions-heading">
    <div class="sp-fu-sechead">
        <div>
            <p class="sp-fu-eyebrow"><?php echo esc_html__( 'Active Causes', 'sampreshan-child' ); ?></p>
            <h2 id="home-petitions-heading" class="sp-fu-sectitle"><?php sp_icon_e( 'petition', 'sp-icon--md sp-icon--saffron', '' ); ?> <?php echo esc_html__( 'Petitions & Campaigns', 'sampreshan-child' ); ?></h2>
        </div>
        <a class="sp-fu-link" href="<?php echo esc_url( $all_url ); ?>">
            <?php echo esc_html__( 'View all', 'sampreshan-child' ); ?> &rarr;
        </a>
    </div>

    <?php if ( $petitions->have_posts() ) : ?>
        <div class="sp-fu-petgrid">
            <?php while ( $petitions->have_posts() ) : $petitions->the_post(); ?>
                <?php get_template_part( 'template-parts/petition/card', null, array( 'id' => get_the_ID() ) ); ?>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>
    <?php else : ?>
        <div class="empty-state glass">
            <div class="empty-state__icon">
                <?php sp_icon_e( 'petition', 'sp-icon--xl sp-icon--saffron', '' ); ?>
            </div>
            <h3 class="empty-state__title"><?php echo esc_html__( 'No active petitions yet', 'sampreshan-child' ); ?></h3>
            <p class="empty-state__desc"><?php echo esc_html__( 'Be the first to start a petition and rally support for a cause that matters to the community.', 'sampreshan-child' ); ?></p>
            <a class="sp-fu-btn sp-fu-btn--primary" href="<?php echo esc_url( $start_url ); ?>">
                <?php sp_icon_e( 'plus', 'sp-icon--sm', '' ); ?>
                <?php echo esc_html__( 'Start a Petition', 'sampreshan-child' ); ?>
            </a>
        </div>
    <?php endif; ?>
</section>
