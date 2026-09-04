<?php
/**
 * Shared petition card — Change.org pattern, Indian style.
 *
 * Usage:
 *   get_template_part( 'template-parts/petition/card', null, array( 'id' => $pid ) );
 *
 * Expects $args['id'] = petition post ID.
 *
 * @package SampreShan_Child
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

$pid = 0;
if ( isset( $args['id'] ) ) {
    $pid = (int) $args['id'];
} else {
    $qid = get_query_var( 'sp_card_pid', 0 );
    $pid = (int) $qid;
}
if ( $pid <= 0 || 'petition' !== get_post_type( $pid ) ) { return; }

$title     = get_the_title( $pid );
$purl      = get_permalink( $pid );
$author_id = (int) get_post_field( 'post_author', $pid );
$author_nm = get_the_author_meta( 'display_name', $author_id );
$sig_count = function_exists( 'sp_petition_signature_count' ) ? (int) sp_petition_signature_count( $pid ) : (int) get_post_meta( $pid, 'sampreshan_signatures', true );
$goal      = (int) get_post_meta( $pid, 'sampreshan_goal', true );
$progress  = $goal > 0 ? min( 100, round( ( $sig_count / $goal ) * 100 ) ) : 0;
$terms     = get_the_terms( $pid, 'cause_category' );
$cause_nm  = ( $terms && ! is_wp_error( $terms ) ) ? $terms[0]->name : '';
$cover     = get_the_post_thumbnail_url( $pid, 'medium' );
$signed    = is_user_logged_in() && function_exists( 'sp_petition_user_has_signed' ) && sp_petition_user_has_signed( $pid );
$can_sign  = is_user_logged_in() && current_user_can( 'sign_petitions' );
$saved     = is_user_logged_in() && function_exists( 'sp_is_petition_saved' ) && sp_is_petition_saved( $pid );

// First supporter faces for the avatar stack.
$faces = array();
if ( function_exists( 'sp_petition_get_signatures' ) ) {
    $rows = sp_petition_get_signatures( array( 'petition_id' => $pid, 'per_page' => 3, 'page' => 1 ) );
    if ( $rows ) {
        foreach ( $rows as $r ) {
            $faces[] = empty( $r->is_anonymous ) ? get_avatar_url( (int) $r->user_id, array( 'size' => 52 ) ) : '';
        }
    }
}
?>
<article class="sp-fu-pet" data-petition-card data-petition-card-id="<?php echo esc_attr( $pid ); ?>" aria-label="<?php echo esc_attr( $title ); ?>">
    <a class="sp-fu-pet__media" href="<?php echo esc_url( $purl ); ?>" tabindex="-1" aria-hidden="true">
        <?php if ( $cover ) : ?>
            <img src="<?php echo esc_url( $cover ); ?>" alt="" loading="lazy" />
        <?php else : ?>
            <span class="sp-fu-pet__ph" aria-hidden="true">ॐ</span>
        <?php endif; ?>
        <?php if ( $cause_nm ) : ?>
            <span class="sp-fu-pet__cause"><?php echo esc_html( $cause_nm ); ?></span>
        <?php endif; ?>
    </a>
    <div class="sp-fu-pet__body">
        <h3 class="sp-fu-pet__title"><a href="<?php echo esc_url( $purl ); ?>"><?php echo esc_html( $title ); ?></a></h3>
        <p class="sp-fu-pet__by">
            <?php echo esc_html( sprintf( __( 'Started by %s', 'sampreshan-child' ), $author_nm ) ); ?>
        </p>
        <div class="sp-fu-pet__sigrow">
            <?php if ( ! empty( $faces ) ) : ?>
                <span class="sp-fu-avatars" aria-hidden="true">
                    <?php foreach ( $faces as $f ) : ?>
                        <?php if ( $f ) : ?>
                            <img src="<?php echo esc_url( $f ); ?>" alt="" loading="lazy" width="26" height="26" />
                        <?php else : ?>
                            <span>ॐ</span>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </span>
            <?php endif; ?>
            <span class="sp-fu-pet__count" data-sp-signature-count-wrap>
                <strong data-sp-signature-count><?php echo esc_html( number_format_i18n( $sig_count ) ); ?></strong>
                <?php esc_html_e( 'signatures', 'sampreshan-child' ); ?>
            </span>
        </div>
        <div class="sp-fu-pet__bar" role="progressbar" aria-valuenow="<?php echo esc_attr( $progress ); ?>" aria-valuemin="0" aria-valuemax="100" aria-label="<?php esc_attr_e( 'Signature progress', 'sampreshan-child' ); ?>">
            <span class="sp-fu-pet__fill" style="width: <?php echo esc_attr( $progress ); ?>%;"></span>
        </div>
        <p class="sp-fu-pet__goal">
            <?php if ( $goal > 0 ) : ?>
                <?php echo esc_html( sprintf( __( '%s%% of %s goal', 'sampreshan-child' ), number_format_i18n( $progress ), number_format_i18n( $goal ) ) ); ?>
            <?php else : ?>
                <?php esc_html_e( 'Every signature counts', 'sampreshan-child' ); ?>
            <?php endif; ?>
        </p>
        <div class="sp-fu-pet__foot">
            <?php if ( $can_sign ) : ?>
                <button type="button" class="sp-fu-btn sp-fu-btn--primary sp-fu-btn--sm sp-sign-button<?php echo $signed ? ' is-signed' : ''; ?>" data-petition-id="<?php echo esc_attr( $pid ); ?>" data-signed="<?php echo $signed ? '1' : '0'; ?>">
                    <?php echo $signed ? esc_html__( 'Signed ✓', 'sampreshan-child' ) : esc_html__( 'Sign', 'sampreshan-child' ); ?>
                </button>
            <?php else : ?>
                <a class="sp-fu-btn sp-fu-btn--primary sp-fu-btn--sm" href="<?php echo esc_url( $purl ); ?>"><?php esc_html_e( 'Sign', 'sampreshan-child' ); ?></a>
            <?php endif; ?>
            <a class="sp-fu-link" href="<?php echo esc_url( $purl ); ?>"><?php esc_html_e( 'Read', 'sampreshan-child' ); ?></a>
            <?php if ( is_user_logged_in() ) : ?>
                <button type="button" class="sp-save-btn sp-save-btn--sm<?php echo $saved ? ' is-saved' : ''; ?>" data-petition-id="<?php echo esc_attr( $pid ); ?>" data-saved="<?php echo $saved ? '1' : '0'; ?>" aria-pressed="<?php echo $saved ? 'true' : 'false'; ?>" aria-label="<?php esc_attr_e( 'Save petition', 'sampreshan-child' ); ?>">
                    <?php sp_icon_auto( 'bookmark', 'sp-icon--sm', '' ); ?>
                </button>
            <?php endif; ?>
        </div>
    </div>
</article>
