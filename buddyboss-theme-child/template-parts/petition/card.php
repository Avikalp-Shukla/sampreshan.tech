<?php
/**
 * Petition Card Template
 * Change.org inspired — premium, responsive, no emojis
 * Uses real existing petition data (page ID: 94 — "Start a Petition")
 *
 * @package SampreShan_Child
 */

// Get petition data from existing page
$petition_id = 94; // "Start a Petition" — real existing page
$petition = get_post( $petition_id );

if ( ! $petition ) {
    return;
}

$petition_title   = $petition->post_title;
$petition_content = $petition->post_content;
$petition_url     = get_permalink( $petition_id );
$petition_date    = get_the_date( 'F j, Y', $petition );
$petition_author  = get_the_author_meta( 'display_name', $petition->post_author );

// Signature counts from real post meta only — no fake numbers.
$signature_current = (int) get_post_meta( $petition_id, 'sampreshan_signatures', true );
$signature_goal    = (int) get_post_meta( $petition_id, 'sampreshan_goal', true );
if ( $signature_goal <= 0 ) {
    $signature_goal = 50000; // sensible default goal until admin sets one
}
$signature_pct = $signature_goal > 0 ? min( 100, ( $signature_current / $signature_goal ) * 100 ) : 0;
?>

<article class="petition-card" aria-label="Petition: <?php echo esc_attr( $petition_title ); ?>">
    <div class="petition-card__image" aria-hidden="true">
        <span aria-label="Petition icon" style="font-family: var(--font-display); font-size: var(--text-4xl); color: var(--saffron-800);">&#10003;</span>
    </div>

    <div class="petition-card__body">
        <div class="petition-card__champion">
            <span>Started by <?php echo esc_html( $petition_author ); ?></span>
            <span aria-hidden="true">&middot;</span>
            <time datetime="<?php echo esc_attr( $petition_date ); ?>"><?php echo esc_html( $petition_date ); ?></time>
        </div>

        <h3 class="petition-card__title">
            <a href="<?php echo esc_url( $petition_url ); ?>"><?php echo esc_html( $petition_title ); ?></a>
        </h3>

        <p class="petition-card__desc">
            <?php echo esc_html( wp_trim_words( strip_tags( $petition_content ), 25 ) ); ?>
        </p>

        <div class="petition-card__progress" aria-label="Signature progress">
            <div class="petition-card__progress-label">
                <span><?php echo number_format( $signature_current ); ?> supporters</span>
                <span class="petition-card__progress-count">Goal: <?php echo number_format( $signature_goal ); ?></span>
            </div>
            <div class="petition-card__progress-bar" role="progressbar" aria-valuenow="<?php echo esc_attr( $signature_current ); ?>" aria-valuemin="0" aria-valuemax="<?php echo esc_attr( $signature_goal ); ?>">
                <div class="petition-card__progress-fill" style="width: <?php echo esc_attr( min( $signature_pct, 100 ) ); ?>%;"></div>
            </div>
        </div>

        <div class="petition-card__actions">
            <a class="btn btn--primary btn--sm" href="<?php echo esc_url( $petition_url ); ?>">Sign Petition</a>
            <a class="btn btn--outline btn--sm" href="<?php echo esc_url( $petition_url ); ?>">Read More</a>
        </div>
    </div>
</article>
