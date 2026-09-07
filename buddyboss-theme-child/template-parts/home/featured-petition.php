<?php
/**
 * Home: Featured Petition — Premium 3D Dark Edition
 * Cinematic dark card with spotlight, floating 3D icon, glass progress.
 *
 * @package SampreShan_Child
 */

$featured_id = 0;
$featured_q  = new WP_Query( array(
    'post_type'      => 'petition',
    'post_status'    => 'publish',
    'meta_query'     => array( array( 'key' => 'sampreshan_featured', 'value' => 1 ) ),
    'posts_per_page' => 1,
    'orderby'        => 'date',
    'order'          => 'DESC',
) );
if ( $featured_q->have_posts() ) {
    $featured_q->the_post();
    $featured_id = get_the_ID();
    wp_reset_postdata();
}
if ( ! $featured_id ) {
    $latest = new WP_Query( array(
        'post_type' => 'petition', 'post_status' => 'publish',
        'posts_per_page' => 1, 'orderby' => 'date', 'order' => 'DESC', 'fields' => 'ids',
    ) );
    if ( $latest->have_posts() ) { $featured_id = (int) $latest->posts[0]; }
    wp_reset_postdata();
}
if ( ! $featured_id ) { return; }
$petition = get_post( $featured_id );
if ( ! $petition || 'petition' !== $petition->post_type ) { return; }

$petition_id      = (int) $featured_id;
$petition_url     = get_permalink( $petition_id );
$petition_title   = $petition->post_title;
$petition_excerpt = wp_trim_words( strip_tags( $petition->post_content ), 30, '...' );
$author_id        = (int) $petition->post_author;
$author_name      = get_the_author_meta( 'display_name', $author_id );
$author_avatar    = get_avatar_url( $author_id, array( 'size' => 80 ) );
$author_url       = function_exists( 'bp_core_get_user_domain' ) ? bp_core_get_user_domain( $author_id ) : get_author_posts_url( $author_id );

$signature_current = function_exists( 'sp_petition_signature_count' )
    ? (int) sp_petition_signature_count( $petition_id )
    : (int) get_post_meta( $petition_id, 'sampreshan_signatures', true );
$signature_goal = (int) get_post_meta( $petition_id, 'sampreshan_goal', true );
if ( $signature_goal <= 0 ) { $signature_goal = 50000; }
$signature_pct  = min( 100, ( $signature_current / $signature_goal ) * 100 );

$user_signed = is_user_logged_in() && function_exists( 'sp_petition_user_has_signed' ) && sp_petition_user_has_signed( $petition_id );
$is_petition_cpt = ( 'petition' === get_post_type( $petition_id ) );

$featured_terms = get_the_terms( $petition_id, 'cause_category' );
$featured_cause = ( $featured_terms && ! is_wp_error( $featured_terms ) && isset( $featured_terms[0]->slug ) ) ? $featured_terms[0]->slug : '';
$featured_icon  = function_exists( 'sp_cause_icon' ) ? sp_cause_icon( $featured_cause ) : 'petition';
?>
<section class="d3-section" aria-labelledby="d3-featured-heading">
    <div class="d3-section__light" aria-hidden="true"></div>
    <div class="d3-section__inner">
        <div class="d3-reveal" style="text-align:center; max-width:640px; margin:0 auto clamp(2rem, 5vw, 4rem);">
            <p class="d3-section__eyebrow" style="justify-content:center;"><?php esc_html_e( 'Featured Cause', 'sampreshan-child' ); ?></p>
            <h2 class="d3-section__title" id="d3-featured-heading"><?php esc_html_e( 'A voice that needs yours', 'sampreshan-child' ); ?></h2>
        </div>

        <div class="d3-featured d3-reveal-scale">
            <div class="d3-featured__media">
                <div class="d3-featured__media-bg"></div>
                <div class="d3-featured__media-icon">
                    <?php sp_icon_e( $featured_icon, 'sp-icon--2xl', __( 'Featured', 'sampreshan-child' ) ); ?>
                </div>
                <span class="d3-featured__badge">
                    <?php sp_icon_auto( 'star', 'sp-icon--xs', '' ); ?>
                    <?php echo esc_html__( 'Featured Cause', 'sampreshan-child' ); ?>
                </span>
            </div>

            <div class="d3-featured__body">
                <div class="d3-featured__author">
                    <?php if ( $author_avatar ) : ?>
                        <img src="<?php echo esc_url( $author_avatar ); ?>" alt="" loading="lazy" />
                    <?php endif; ?>
                    <span>
                        <?php printf( esc_html__( 'Started by %s', 'sampreshan-child' ),
                            '<a href="' . esc_url( $author_url ) . '" style="color:var(--d-text);font-weight:600;">' . esc_html( $author_name ) . '</a>'
                        ); ?>
                    </span>
                </div>

                <h3 class="d3-featured__title">
                    <a href="<?php echo esc_url( $petition_url ); ?>"><?php echo esc_html( $petition_title ); ?></a>
                </h3>
                <p class="d3-featured__desc"><?php echo esc_html( $petition_excerpt ); ?></p>

                <div class="d3-featured__progress">
                    <div class="d3-featured__progress-head">
                        <span class="d3-featured__progress-count">
                            <span data-count="<?php echo esc_attr( $signature_current ); ?>"><?php echo esc_html( number_format_i18n( $signature_current ) ); ?></span>
                            <small><?php echo esc_html__( 'I', 'sampreshan-child' ); ?></small>
                        </span>
                        <span class="d3-featured__progress-goal">
                            <?php printf( esc_html__( 'Goal: %s', 'sampreshan-child' ), esc_html( number_format_i18n( $signature_goal ) ) ); ?>
                        </span>
                    </div>
                    <div class="d3-featured__bar" role="progressbar" aria-valuenow="<?php echo esc_attr( $signature_current ); ?>" aria-valuemin="0" aria-valuemax="<?php echo esc_attr( $signature_goal ); ?>">
                        <div class="d3-featured__bar-fill" style="width:<?php echo esc_attr( $signature_pct ); ?>%;"></div>
                    </div>
                </div>

                <div class="d3-featured__actions">
                    <?php if ( $is_petition_cpt && is_user_logged_in() && current_user_can( 'sign_petitions' ) ) : ?>
                        <button type="button" class="d3-btn d3-btn--primary d3-btn--lg sp-sign-button<?php echo $user_signed ? ' is-signed' : ''; ?>" data-petition-id="<?php echo esc_attr( $petition_id ); ?>" data-signed="<?php echo $user_signed ? '1' : '0'; ?>">
                            <?php sp_icon_e( 'ibadge', 'sp-icon--sm', '' ); ?>
                            <?php echo $user_signed ? esc_html__( 'Supported', 'sampreshan-child' ) : esc_html__( 'I Support This Issue', 'sampreshan-child' ); ?>
                        </button>
                    <?php else : ?>
                        <a class="d3-btn d3-btn--primary d3-btn--lg" href="<?php echo esc_url( $petition_url ); ?>">
                            <?php sp_icon_e( 'ibadge', 'sp-icon--sm', '' ); ?>
                            <?php echo esc_html__( 'I Support This Issue', 'sampreshan-child' ); ?>
                        </a>
                    <?php endif; ?>
                    <a class="d3-btn d3-btn--ghost d3-btn--lg" href="<?php echo esc_url( $petition_url ); ?>">
                        <?php echo esc_html__( 'Read Full Cause', 'sampreshan-child' ); ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
