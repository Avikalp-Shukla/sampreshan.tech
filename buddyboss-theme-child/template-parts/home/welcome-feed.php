<?php
/**
 * Home: Welcome Feed — Premium Edition
 * Rich post card with reaction animations, real data only
 *
 * @package SampreShan_Child
 */

$welcome_id = 99;
$welcome    = get_post( $welcome_id );

if ( ! $welcome ) {
    return;
}

$wauthor_id   = (int) $welcome->post_author;
$wauthor_name = get_the_author_meta( 'display_name', $wauthor_id );
$wauthor_url  = function_exists( 'bp_core_get_user_domain' ) ? bp_core_get_user_domain( $wauthor_id ) : get_author_posts_url( $wauthor_id );
$wavatar      = get_avatar_url( $wauthor_id, array( 'size' => 96 ) );
$wdate        = get_the_date( 'F j, Y', $welcome );
$wdate_iso    = get_the_date( 'c', $welcome );
$wurl         = get_permalink( $welcome_id );
$wexcerpt     = wp_trim_words( strip_tags( $welcome->post_content ), 40, '...' );
?>
<article class="post-card card-3d fade-in" aria-label="<?php echo esc_attr( $welcome->post_title ); ?>">
    <header class="post-card__header">
        <a class="post-card__avatar" href="<?php echo esc_url( $wauthor_url ); ?>" aria-label="<?php echo esc_attr( $wauthor_name ); ?>">
            <?php if ( $wavatar ) : ?>
                <img class="post-card__avatar-img" src="<?php echo esc_url( $wavatar ); ?>" alt="" loading="lazy" />
            <?php else : ?>
                <span aria-hidden="true"><?php echo esc_html( mb_substr( $wauthor_name, 0, 1 ) ); ?></span>
            <?php endif; ?>
        </a>
        <div class="post-card__author">
            <h3 class="post-card__author-name">
                <a href="<?php echo esc_url( $wauthor_url ); ?>"><?php echo esc_html( $wauthor_name ); ?></a>
            </h3>
            <p class="post-card__meta">
                <time datetime="<?php echo esc_attr( $wdate_iso ); ?>"><?php echo esc_html( $wdate ); ?></time>
                <span aria-hidden="true">&middot;</span>
                <span><?php echo esc_html__( 'Welcome message', 'sampreshan-child' ); ?></span>
            </p>
        </div>
    </header>

    <div class="post-card__content">
        <p><?php echo esc_html( $wexcerpt ); ?></p>
    </div>

    <footer class="post-card__actions">
        <a class="post-card__action" href="<?php echo esc_url( $wurl ); ?>">
            <?php sp_icon_e( 'heart', 'sp-icon--sm sp-icon--saffron', __( 'Support', 'sampreshan-child' ) ); ?>
            <span><?php echo esc_html__( 'Support', 'sampreshan-child' ); ?></span>
        </a>
        <a class="post-card__action" href="<?php echo esc_url( $wurl ); ?>#comments">
            <?php sp_icon_e( 'comment', 'sp-icon--sm sp-icon--saffron', __( 'Discuss', 'sampreshan-child' ) ); ?>
            <span><?php echo esc_html__( 'Discuss', 'sampreshan-child' ); ?></span>
        </a>
        <a class="post-card__action" href="<?php echo esc_url( $wurl ); ?>">
            <?php sp_icon_e( 'share', 'sp-icon--sm sp-icon--saffron', __( 'Share', 'sampreshan-child' ) ); ?>
            <span><?php echo esc_html__( 'Share', 'sampreshan-child' ); ?></span>
        </a>
    </footer>
</article>
