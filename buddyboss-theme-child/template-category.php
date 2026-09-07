<?php
/**
 * Template: Category / Cause page
 * Used by Temple Preservation, Cultural Heritage, Religious Education, Environmental Causes, Community Welfare
 *
 * @package SampreShan_Child
 */
get_header();
$term = get_queried_object();
// Per-category 3D hero icon — one topic = one symbol (see sp_cause_icon()).
$cause_icons = array(
    'category-temple-preservation'  => 'mandir',
    'category-cultural-heritage'    => 'pothi',
    'category-religious-education'  => 'vedagranth',
    'category-environmental-causes' => 'peepal',
    'category-community-welfare'    => 'seva',
);
$cause_slug = ( $term && isset( $term->post_name ) ) ? (string) $term->post_name : '';
$cause_icon = isset( $cause_icons[ $cause_slug ] ) ? $cause_icons[ $cause_slug ] : 'temple';
?>
<main class="sp-page sp-page--cause" aria-label="<?php echo esc_attr( $term->post_title ?? __( 'Category', 'sampreshan-child' ) ); ?>">
    <div class="sp-page__hero sp-page__hero--cause" style="--cause-accent: var(--saffron-500);">
        <div class="sp-page__hero-mask"></div>
        <div class="sp-page__hero-inner">
            <?php sp_icon_auto( $cause_icon, 'sp-icon--2xl sp-page__hero-icon', '' ); ?>
            <h1 class="sp-page__title"><?php the_title(); ?></h1>
            <p class="sp-page__lead"><?php the_excerpt(); ?></p>
            <a class="btn btn--primary" href="<?php echo esc_url( home_url( '/start-a-petition/' ) ); ?>">
                <?php esc_html_e( 'Start a Petition in this Category', 'sampreshan-child' ); ?>
            </a>
        </div>
    </div>
    <div class="sp-page__body sp-container">
        <section class="sp-section" aria-labelledby="sp-cause-petitions-h">
            <h2 class="sp-section__title" id="sp-cause-petitions-h"><?php sp_icon_auto( 'petition', 'sp-icon--md sp-icon--saffron', '' ); ?> <?php esc_html_e( 'Active Petitions in This Category', 'sampreshan-child' ); ?></h2>
            <?php
            $q = new WP_Query( array(
                'post_type'      => 'petition',
                'post_status'    => 'publish',
                'posts_per_page' => 12,
                'tax_query'      => array(
                    array(
                        'taxonomy' => 'petition_category',
                        'field'    => 'slug',
                        'terms'    => $term->post_name,
                    ),
                ),
                'orderby'        => 'date',
                'order'          => 'DESC',
            ) );
            if ( $q->have_posts() ) : ?>
                <ul class="sp-petition-grid">
                    <?php while ( $q->have_posts() ) : $q->the_post(); ?>
                        <li>
                            <a href="<?php the_permalink(); ?>" class="sp-petition-card">
                                <?php
                                $thumb = get_the_post_thumbnail_url( get_the_ID(), 'medium' );
                                if ( $thumb ) : ?>
                                    <img class="sp-petition-card__thumb" src="<?php echo esc_url( $thumb ); ?>" alt="" width="240" height="140" loading="lazy" />
                                <?php endif; ?>
                                <div class="sp-petition-card__body">
                                    <span class="sp-badge sp-badge--kesariya"><?php echo esc_html( $term->post_title ); ?></span>
                                    <h3><?php the_title(); ?></h3>
                                    <p><?php echo wp_trim_words( get_the_excerpt(), 15 ); ?></p>
                                    <div class="sp-petition-card__meta">
                                        <span><?php echo esc_html( get_the_author() ); ?></span>
                                        <span><?php echo esc_html( human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ) . ' ago' ); ?></span>
                                    </div>
                                </div>
                            </a>
                        </li>
                    <?php endwhile; ?>
                </ul>
                <?php
                echo paginate_links( array(
                    'total' => $q->max_num_pages,
                    'mid_size' => 2,
                ) );
                wp_reset_postdata();
            else : ?>
                <p class="sp-dash-muted"><?php esc_html_e( 'No petitions in this category yet. Be the first to start one!', 'sampreshan-child' ); ?></p>
            <?php endif; ?>
        </section>
        <section class="sp-section sp-section--center" aria-labelledby="sp-cause-call-h">
            <h2 class="sp-section__title" id="sp-cause-call-h"><?php sp_icon_e( 'shankh', 'sp-icon--md sp-icon--saffron', '' ); ?> <?php esc_html_e( 'Make Your Voice Heard', 'sampreshan-child' ); ?></h2>
            <p class="sp-section__sub"><?php printf( esc_html__( 'Every cause starts with one person. Start a petition under %s and rally the community.', 'sampreshan-child' ), '<strong>' . esc_html( get_the_title() ) . '</strong>' ); ?></p>
            <a class="btn btn--primary btn--lg" href="<?php echo esc_url( home_url( '/start-a-petition/' ) ); ?>">
                <?php sp_icon_e( 'plus', 'sp-icon--sm sp-icon--white', '' ); ?>
                <?php esc_html_e( 'Start a Petition', 'sampreshan-child' ); ?>
            </a>
        </section>
    </div>
</main>
<?php get_footer(); ?>