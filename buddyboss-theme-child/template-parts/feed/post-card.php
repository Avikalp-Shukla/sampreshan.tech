<?php
/**
 * Post / Feed Card Template
 * Truth Social inspired — premium, responsive, no emojis
 * Uses real existing welcome post (ID: 99)
 *
 * @package SampreShan_Child
 */

$post_id = 99; // "Sampreshan Mein Aapka Swagat Hai" — real existing post
$post = get_post( $post_id );

if ( ! $post ) {
    return;
}

$post_title   = $post->post_title;
$post_content = $post->post_content;
$post_url     = get_permalink( $post_id );
$post_date    = get_the_date( 'F j, Y', $post );
$post_author  = get_the_author_meta( 'display_name', $post->post_author );
$post_avatar  = get_avatar_url( $post->post_author, array( 'size' => 45 ) );
?>

<article class="post-card" aria-label="Post by <?php echo esc_attr( $post_author ); ?>">
    <header class="post-card__header">
        <div class="post-card__avatar" aria-hidden="true">
            <?php if ( $post_avatar ) : ?>
                <img src="<?php echo esc_url( $post_avatar ); ?>" alt="" width="45" height="45" loading="lazy">
            <?php else : ?>
                <?php echo esc_html( strtoupper( substr( $post_author, 0, 2 ) ) ); ?>
            <?php endif; ?>
        </div>
        <div class="post-card__author">
            <h3 class="post-card__author-name"><?php echo esc_html( $post_author ); ?></h3>
            <p class="post-card__meta">
                <time datetime="<?php echo esc_attr( get_the_date( 'c', $post ) ); ?>"><?php echo esc_html( $post_date ); ?></time>
            </p>
        </div>
    </header>

    <div class="post-card__content">
        <?php echo wp_kses_post( wpautop( wp_trim_words( $post_content, 60 ) ) ); ?>
    </div>

    <div class="post-card__actions">
        <button class="post-card__action" type="button" aria-label="Like this post">
            <span aria-hidden="true">&#9829;</span> Like
        </button>
        <button class="post-card__action" type="button" aria-label="Comment on this post">
            <span aria-hidden="true">&#9998;</span> Comment
        </button>
        <a class="post-card__action" href="<?php echo esc_url( $post_url ); ?>" aria-label="Share this post">
            <span aria-hidden="true">&#128279;</span> Share
        </a>
    </div>
</article>
