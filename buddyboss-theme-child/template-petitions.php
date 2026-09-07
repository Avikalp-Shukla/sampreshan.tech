<?php
/**
 * Template Name: All Petitions
 *
 * Browse every published petition with search + cause filter.
 *
 * @package SampreShan_Child
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

get_header();

$search = isset( $_GET['q'] ) ? sanitize_text_field( wp_unslash( $_GET['q'] ) ) : '';
$cause  = isset( $_GET['cause'] ) ? (int) $_GET['cause'] : 0;
$paged  = max( 1, (int) get_query_var( 'paged', 1 ) );

$args = array(
    'post_type'      => 'petition',
    'post_status'    => 'publish',
    'posts_per_page' => 12,
    'paged'          => $paged,
    'orderby'        => 'date',
    'order'          => 'DESC',
);
if ( '' !== $search ) {
    $args['s'] = $search;
}
if ( $cause > 0 ) {
    $args['tax_query'] = array(
        array(
            'taxonomy' => 'cause_category',
            'field'    => 'term_id',
            'terms'    => $cause,
        ),
    );
}
$q = new WP_Query( $args );

$causes = get_terms( array( 'taxonomy' => 'cause_category', 'hide_empty' => false ) );
if ( is_wp_error( $causes ) ) { $causes = array(); }

$start_url = home_url( '/start-a-petition/' );
?>

<main class="sp-page sp-users" role="main">
    <header class="sp-users__hero">
        <p class="sp-section__eyebrow"><?php esc_html_e( 'Community causes', 'sampreshan-child' ); ?></p>
        <h1 class="sp-users__title"><?php sp_icon_auto( 'petition', 'sp-icon--md sp-icon--saffron', '' ); ?> <?php esc_html_e( 'All Petitions', 'sampreshan-child' ); ?></h1>
        <p class="sp-users__sub"><?php esc_html_e( 'Browse every cause the community is rallying behind. Give an I to what moves you — or start your own.', 'sampreshan-child' ); ?></p>
        <div class="sp-users__hero-actions">
            <a class="btn btn--primary" href="<?php echo esc_url( $start_url ); ?>">
                <?php sp_icon_e( 'plus', 'sp-icon--sm', '' ); ?>
                <?php esc_html_e( 'Start a Petition', 'sampreshan-child' ); ?>
            </a>
        </div>
    </header>

    <form class="sp-users__filters" method="get" action="<?php echo esc_url( get_permalink() ); ?>" role="search">
        <label class="sp-users__search">
            <span class="screen-reader-text"><?php esc_html_e( 'Search petitions', 'sampreshan-child' ); ?></span>
            <?php sp_icon_e( 'search', 'sp-icon--sm', '' ); ?>
            <input type="search" name="q" value="<?php echo esc_attr( $search ); ?>" placeholder="<?php esc_attr_e( 'Search petitions…', 'sampreshan-child' ); ?>" />
        </label>
        <label class="sp-users__select">
            <span class="screen-reader-text"><?php esc_html_e( 'Filter by cause', 'sampreshan-child' ); ?></span>
            <select name="cause" onchange="this.form.submit()">
                <option value="0"><?php esc_html_e( 'All causes', 'sampreshan-child' ); ?></option>
                <?php foreach ( $causes as $t ) : ?>
                    <option value="<?php echo esc_attr( $t->term_id ); ?>" <?php selected( $cause, $t->term_id ); ?>>
                        <?php echo esc_html( $t->name ); ?> (<?php echo esc_html( $t->count ); ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <button class="btn btn--ghost" type="submit"><?php esc_html_e( 'Search', 'sampreshan-child' ); ?></button>
        <?php if ( '' !== $search || $cause > 0 ) : ?>
            <a class="sp-text-link" href="<?php echo esc_url( get_permalink() ); ?>"><?php esc_html_e( 'Clear', 'sampreshan-child' ); ?></a>
        <?php endif; ?>
    </form>

    <?php if ( $q->have_posts() ) : ?>
        <p class="sp-users__count">
            <?php
            printf(
                /* translators: %s = number */
                esc_html__( '%s petitions found', 'sampreshan-child' ),
                esc_html( number_format_i18n( (int) $q->found_posts ) )
            );
            ?>
        </p>
        <div class="sp-fu-petgrid">
            <?php while ( $q->have_posts() ) : $q->the_post(); ?>
                <?php get_template_part( 'template-parts/petition/card', null, array( 'id' => get_the_ID() ) ); ?>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>

        <nav class="sp-users__pagination" aria-label="Petitions pages">
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
            <h3 class="sp-dash-empty__title"><?php esc_html_e( 'No petitions found', 'sampreshan-child' ); ?></h3>
            <p class="sp-dash-empty__desc"><?php esc_html_e( 'Try a different search — or be the first to raise this cause.', 'sampreshan-child' ); ?></p>
            <a class="btn btn--primary" href="<?php echo esc_url( $start_url ); ?>"><?php esc_html_e( 'Start a Petition', 'sampreshan-child' ); ?></a>
        </div>
    <?php endif; ?>
</main>

<?php get_footer(); ?>
