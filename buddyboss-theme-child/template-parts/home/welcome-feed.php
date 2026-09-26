<?php
/**
 * Home: Welcome Feed — Premium Edition
 * Rich post card with reaction animations, real data only
 *
 * @package SampreShan_Child
 */

$welcome_posts = get_posts( array(
    'post_type'      => 'post',
    'post_status'    => 'publish',
    'posts_per_page' => 6,
    'orderby'        => 'date',
    'order'          => 'DESC',
) );

if ( empty( $welcome_posts ) ) {
    return;
}
?>
<section class="sp-home-stories" aria-label="<?php esc_attr_e( 'Latest community stories', 'sampreshan-child' ); ?>">
    <div class="sp-home-stories__grid">
        <?php foreach ( $welcome_posts as $welcome ) :
            $wauthor_id   = (int) $welcome->post_author;
            $wauthor_name = get_the_author_meta( 'display_name', $wauthor_id );
            $wauthor_url  = function_exists( 'bp_core_get_user_domain' ) ? bp_core_get_user_domain( $wauthor_id ) : get_author_posts_url( $wauthor_id );
            $wavatar      = get_avatar_url( $wauthor_id, array( 'size' => 96 ) );
            $wurl         = get_permalink( $welcome->ID );
            $wexcerpt     = wp_trim_words( strip_tags( $welcome->post_content ), 40, '...' );
        ?>
            <article class="post-card card-3d fade-in" aria-label="<?php echo esc_attr( $welcome->post_title ); ?>">
                <header class="post-card__header">
                    <a class="post-card__avatar" href="<?php echo esc_url( $wauthor_url ); ?>" aria-label="<?php echo esc_attr( $wauthor_name ); ?>">
                        <?php if ( $wavatar ) : ?><img class="post-card__avatar-img" src="<?php echo esc_url( $wavatar ); ?>" alt="" loading="lazy" /><?php endif; ?>
                    </a>
                    <div class="post-card__author">
                        <h3 class="post-card__author-name"><a href="<?php echo esc_url( $wauthor_url ); ?>"><?php echo esc_html( $wauthor_name ); ?></a></h3>
                        <p class="post-card__meta"><time datetime="<?php echo esc_attr( get_the_date( 'c', $welcome ) ); ?>"><?php echo esc_html( get_the_date( 'F j, Y', $welcome ) ); ?></time> <span aria-hidden="true">&middot;</span> <span><?php esc_html_e( 'Community story', 'sampreshan-child' ); ?></span></p>
                    </div>
                </header>
                <?php if ( has_post_thumbnail( $welcome ) ) : ?><a class="post-card__image" href="<?php echo esc_url( $wurl ); ?>"><img src="<?php echo esc_url( get_the_post_thumbnail_url( $welcome, 'large' ) ); ?>" alt="" loading="lazy" /></a><?php endif; ?>
                <div class="post-card__content"><p><?php echo esc_html( $wexcerpt ); ?></p></div>
                <footer class="post-card__actions"><a class="post-card__action" href="<?php echo esc_url( $wurl ); ?>"><span><?php esc_html_e( 'Read story', 'sampreshan-child' ); ?> &rarr;</span></a><a class="post-card__action" href="<?php echo esc_url( $wurl ); ?>#comments"><span><?php esc_html_e( 'Discuss', 'sampreshan-child' ); ?></span></a></footer>
            </article>
        <?php endforeach; ?>
    </div>
</section>
