<?php
/**
 * Home: Welcome Feed (Activity / Latest Post)
 * Real existing post ID 99 only. No AI content.
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
$wcontent     = apply_filters( 'the_content', $welcome->post_content );
$wexcerpt     = wp_trim_words( strip_tags( $welcome->post_content ), 40, '…' );
?>
<article class="post-card" aria-label="<?php echo esc_attr( $welcome->post_title ); ?>">
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
            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
            </svg>
            <span><?php echo esc_html__( 'Support', 'sampreshan-child' ); ?></span>
        </a>
        <a class="post-card__action" href="<?php echo esc_url( $wurl ); ?>#comments">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
            </svg>
            <span><?php echo esc_html__( 'Discuss', 'sampreshan-child' ); ?></span>
        </a>
        <a class="post-card__action" href="<?php echo esc_url( $wurl ); ?>">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <circle cx="18" cy="5" r="3" /><circle cx="6" cy="12" r="3" /><circle cx="18" cy="19" r="3" />
                <line x1="8.59" y1="13.51" x2="15.42" y2="17.49" />
                <line x1="15.41" y1="6.51" x2="8.59" y2="10.49" />
            </svg>
            <span><?php echo esc_html__( 'Share', 'sampreshan-child' ); ?></span>
        </a>
    </footer>
</article>
