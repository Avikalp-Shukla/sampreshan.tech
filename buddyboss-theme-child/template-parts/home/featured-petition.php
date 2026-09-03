<?php
/**
 * Home: Featured Petition
 * Real data only. Pulls existing page ID 94 ("Start a Petition").
 * No AI content. No fake signature counts — shows 0 unless meta key set.
 *
 * @package SampreShan_Child
 */

$petition_id = 94;
$petition    = get_post( $petition_id );

if ( ! $petition ) {
    return;
}

$petition_url   = get_permalink( $petition_id );
$petition_title = $petition->post_title;
$petition_excerpt = wp_trim_words( strip_tags( $petition->post_content ), 30, '…' );
$author_id      = (int) $petition->post_author;
$author_name    = get_the_author_meta( 'display_name', $author_id );
$author_avatar  = get_avatar_url( $author_id, array( 'size' => 80 ) );
$author_url     = function_exists( 'bp_core_get_user_domain' ) ? bp_core_get_user_domain( $author_id ) : get_author_posts_url( $author_id );

// Signature counts from real post meta (if present) — otherwise 0
$signature_current = (int) get_post_meta( $petition_id, 'sampreshan_signatures', true );
$signature_goal    = (int) get_post_meta( $petition_id, 'sampreshan_goal', true );
if ( $signature_goal <= 0 ) {
    $signature_goal = 50000;
}
$signature_pct = $signature_goal > 0 ? min( 100, ( $signature_current / $signature_goal ) * 100 ) : 0;
?>
<article class="petition-card petition-card--featured" aria-label="Featured petition">
    <div class="petition-card__image" aria-hidden="true">
        <svg viewBox="0 0 64 64" width="56" height="56" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M32 6 L40 24 L60 26 L44 40 L48 60 L32 50 L16 60 L20 40 L4 26 L24 24 Z" />
        </svg>
    </div>

    <div class="petition-card__body">
        <div class="petition-card__badge-row">
            <span class="badge badge--saffron"><?php echo esc_html__( 'Featured Cause', 'sampreshan-child' ); ?></span>
        </div>

        <div class="petition-card__champion">
            <?php if ( $author_avatar ) : ?>
                <img class="post-card__avatar-img" src="<?php echo esc_url( $author_avatar ); ?>" alt="" loading="lazy" />
            <?php endif; ?>
            <span>
                <?php
                printf(
                    /* translators: %s = author display name */
                    esc_html__( 'Started by %s', 'sampreshan-child' ),
                    '<a href="' . esc_url( $author_url ) . '">' . esc_html( $author_name ) . '</a>'
                );
                ?>
            </span>
        </div>

        <h3 class="petition-card__title">
            <a href="<?php echo esc_url( $petition_url ); ?>"><?php echo esc_html( $petition_title ); ?></a>
        </h3>

        <p class="petition-card__desc"><?php echo esc_html( $petition_excerpt ); ?></p>

        <div class="petition-card__progress" aria-label="Signature progress">
            <div class="petition-card__progress-label">
                <span class="petition-card__progress-count">
                    <?php echo esc_html( number_format_i18n( $signature_current ) ); ?>
                    <small><?php echo esc_html__( 'supporters', 'sampreshan-child' ); ?></small>
                </span>
                <span>
                    <?php
                    printf(
                        /* translators: %s = formatted goal number */
                        esc_html__( 'Goal: %s', 'sampreshan-child' ),
                        esc_html( number_format_i18n( $signature_goal ) )
                    );
                    ?>
                </span>
            </div>
            <div class="petition-card__progress-bar" role="progressbar" aria-valuenow="<?php echo esc_attr( $signature_current ); ?>" aria-valuemin="0" aria-valuemax="<?php echo esc_attr( $signature_goal ); ?>">
                <div class="petition-card__progress-fill" style="width: <?php echo esc_attr( $signature_pct ); ?>%;"></div>
            </div>
        </div>

        <div class="petition-card__actions">
            <a class="btn btn--primary" href="<?php echo esc_url( $petition_url ); ?>">
                <?php echo esc_html__( 'Sign This Petition', 'sampreshan-child' ); ?>
            </a>
            <a class="btn btn--outline" href="<?php echo esc_url( $petition_url ); ?>">
                <?php echo esc_html__( 'Read Full Cause', 'sampreshan-child' ); ?>
            </a>
        </div>
    </div>
</article>
