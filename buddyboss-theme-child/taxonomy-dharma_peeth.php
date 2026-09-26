<?php
/**
 * Peeth archive.
 *
 * @package SampreShan_Child
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

get_header();
$term = get_queried_object();
?>
<main id="main" class="sp-page sp-dharma-directory" role="main">
    <header class="sp-dharma-hero">
        <p class="sp-section__eyebrow"><?php esc_html_e( 'Peeth tradition', 'sampreshan-child' ); ?></p>
        <h1><?php single_term_title(); ?></h1>
        <p><?php esc_html_e( 'Profiles and verified updates connected with this Peeth tradition.', 'sampreshan-child' ); ?></p>
        <a class="btn btn--ghost" href="<?php echo esc_url( sp_dharma_directory_url() ); ?>"><?php esc_html_e( 'All Acharyas & Peeths', 'sampreshan-child' ); ?></a>
    </header>
    <?php if ( have_posts() ) : ?>
        <div class="sp-dharma-profile-grid">
            <?php while ( have_posts() ) : the_post(); ?>
                <article class="sp-dharma-profile-card">
                    <p class="sp-dharma-card__peeth"><?php echo esc_html( $term->name ); ?></p>
                    <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                    <p><?php echo esc_html( get_the_excerpt() ); ?></p>
                    <div class="sp-dharma-card__footer"><a href="<?php the_permalink(); ?>"><?php esc_html_e( 'Open profile', 'sampreshan-child' ); ?></a><?php sp_dharma_follow_button( get_the_ID() ); ?></div>
                </article>
            <?php endwhile; ?>
        </div>
        <?php the_posts_pagination(); ?>
    <?php else : ?>
        <div class="sp-dash-empty sp-dash-card"><h2><?php esc_html_e( 'No profiles are published yet', 'sampreshan-child' ); ?></h2></div>
    <?php endif; ?>
</main>
<?php get_footer(); ?>
