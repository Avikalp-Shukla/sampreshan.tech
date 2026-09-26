<?php
/**
 * Single community story.
 *
 * @package SampreShan_Child
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

get_header();

while ( have_posts() ) : the_post();
    $author_id = (int) get_the_author_meta( 'ID' );
    $avatar    = get_avatar_url( $author_id, array( 'size' => 96 ) );
    ?>
    <main id="main" class="sp-story-page" role="main">
        <article <?php post_class( 'sp-story' ); ?>>
            <header class="sp-story__header">
                <div class="sp-story__author">
                    <span class="sp-story__avatar" aria-hidden="true">
                        <?php if ( $avatar ) : ?><img src="<?php echo esc_url( $avatar ); ?>" alt="" width="52" height="52" /><?php endif; ?>
                    </span>
                    <div>
                        <p class="sp-story__eyebrow"><?php esc_html_e( 'Community story', 'sampreshan-child' ); ?></p>
                        <p class="sp-story__byline"><?php echo esc_html( get_the_author() ); ?> <span aria-hidden="true">&middot;</span> <?php echo esc_html( get_the_date( 'F j, Y' ) ); ?></p>
                    </div>
                </div>
                <h1 class="sp-story__title"><?php the_title(); ?></h1>
            </header>

            <?php if ( has_post_thumbnail() ) : ?>
                <figure class="sp-story__media">
                    <?php the_post_thumbnail( 'large', array( 'loading' => 'eager' ) ); ?>
                </figure>
            <?php endif; ?>

            <div class="sp-story__content">
                <?php the_content(); ?>
            </div>

            <footer class="sp-story__footer">
                <a class="sp-story__back" href="<?php echo esc_url( function_exists( 'sampreshan_dashboard_url' ) ? sampreshan_dashboard_url() : home_url( '/dashboard/' ) ); ?>">&larr; <?php esc_html_e( 'Back to dashboard', 'sampreshan-child' ); ?></a>
                <a class="sp-story__back" href="<?php echo esc_url( function_exists( 'sp_feed_url' ) ? sp_feed_url() : home_url( '/feed-2/' ) ); ?>"><?php esc_html_e( 'Community feed', 'sampreshan-child' ); ?> &rarr;</a>
            </footer>
        </article>
    </main>
    <?php
endwhile;

get_footer();
