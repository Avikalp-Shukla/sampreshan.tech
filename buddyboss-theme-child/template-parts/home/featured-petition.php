<?php
/**
 * Home: Featured Petition — Premium Edition
 * Live signature count, AJAX sign button, animated progress bar
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
    // Fallback: latest published petition (never a regular page).
    $latest = new WP_Query( array(
        'post_type'      => 'petition',
        'post_status'    => 'publish',
        'posts_per_page' => 1,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'fields'         => 'ids',
    ) );
    if ( $latest->have_posts() ) {
        $featured_id = (int) $latest->posts[0];
    }
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

$user_signed = is_user_logged_in()
    && function_exists( 'sp_petition_user_has_signed' )
    && sp_petition_user_has_signed( $petition_id );
$is_petition_cpt = ( 'petition' === get_post_type( $petition_id ) );
?>
<article class="petition-card petition-card--featured card-3d fade-in" data-petition-card data-petition-card-id="<?php echo esc_attr( $petition_id ); ?>" aria-label="Featured petition">
    <div class="petition-card__image" aria-hidden="true">
        <?php sp_icon_e( 'dharma', 'sp-icon--2xl sp-icon--saffron sp-icon--float', __( 'Featured', 'sampreshan-child' ) ); ?>
    </div>

    <div class="petition-card__body">
        <div class="petition-card__badge-row">
            <span class="chip-3d chip-3d--gold">
                <?php sp_icon_e( 'star', 'sp-icon--sm sp-icon--gold', __( 'Featured', 'sampreshan-child' ) ); ?>
                <?php echo esc_html__( 'Featured Cause', 'sampreshan-child' ); ?>
            </span>
        </div>

        <div class="petition-card__champion">
            <?php if ( $author_avatar ) : ?>
                <img class="post-card__avatar-img" src="<?php echo esc_url( $author_avatar ); ?>" alt="" loading="lazy" />
            <?php endif; ?>
            <span>
                <?php
                printf(
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
                    <span data-sp-signature-count><?php echo esc_html( number_format_i18n( $signature_current ) ); ?></span>
                    <small><?php echo esc_html__( 'supporters', 'sampreshan-child' ); ?></small>
                </span>
                <span>
                    <?php
                    printf(
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
            <?php if ( $is_petition_cpt && is_user_logged_in() && current_user_can( 'sign_petitions' ) ) : ?>
                <button
                    type="button"
                    class="btn-3d btn-3d--lg sp-sign-button<?php echo $user_signed ? ' is-signed' : ''; ?>"
                    data-petition-id="<?php echo esc_attr( $petition_id ); ?>"
                    data-signed="<?php echo $user_signed ? '1' : '0'; ?>"
                >
                    <?php sp_icon_e( $user_signed ? 'check' : 'pen', 'sp-icon--sm sp-icon--white', $user_signed ? __( 'Signed', 'sampreshan-child' ) : __( 'Sign', 'sampreshan-child' ) ); ?>
                    <?php echo $user_signed ? esc_html__( 'You signed', 'sampreshan-child' ) : esc_html__( 'Sign This Petition', 'sampreshan-child' ); ?>
                </button>
            <?php else : ?>
                <a class="btn-3d btn-3d--lg" href="<?php echo esc_url( $petition_url ); ?>">
                    <?php sp_icon_e( 'pen', 'sp-icon--sm sp-icon--white', __( 'Sign', 'sampreshan-child' ) ); ?>
                    <?php echo esc_html__( 'Sign This Petition', 'sampreshan-child' ); ?>
                </a>
            <?php endif; ?>

            <a class="btn-3d btn-3d--lg btn-3d--royal" href="<?php echo esc_url( $petition_url ); ?>">
                <?php echo esc_html__( 'Read Full Cause', 'sampreshan-child' ); ?>
            </a>
        </div>
    </div>
</article>
