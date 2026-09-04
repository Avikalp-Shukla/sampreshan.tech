<?php
/**
 * Template Name: Single Petition
 * Template Post Type: petition
 *
 * Renders a single `petition` post: hero, signatures, progress, comments,
 * sign form, related petitions.
 *
 * @package SampreShan_Child
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();

while ( have_posts() ) : the_post();
    $pid           = get_the_ID();
    $title         = get_the_title();
    $content       = apply_filters( 'the_content', get_the_content() );
    $excerpt       = get_the_excerpt();
    $author_id     = (int) get_post_field( 'post_author', $pid );
    $author_name   = get_the_author_meta( 'display_name', $author_id );
    $author_url    = function_exists( 'bp_core_get_user_domain' ) ? bp_core_get_user_domain( $author_id ) : get_author_posts_url( $author_id );
    $author_avatar = get_avatar_url( $author_id, array( 'size' => 96 ) );
    $cover         = get_the_post_thumbnail_url( $pid, 'full' );

    $goal     = (int) get_post_meta( $pid, 'sampreshan_goal', true ) ?: 25000;
    $status   = get_post_meta( $pid, 'sampreshan_status', true ) ?: 'active';
    $deadline = get_post_meta( $pid, 'sampreshan_deadline', true );

    $count    = function_exists( 'sp_petition_signature_count' ) ? (int) sp_petition_signature_count( $pid ) : 0;
    $pct      = $goal > 0 ? min( 100, round( ( $count / $goal ) * 100, 1 ) ) : 0;
    $user_signed = is_user_logged_in() && function_exists( 'sp_petition_user_has_signed' ) && sp_petition_user_has_signed( $pid );
    $can_sign    = is_user_logged_in() && current_user_can( 'sign_petitions' ) && 'active' === $status;

    $signatures = function_exists( 'sp_petition_get_signatures' ) ? sp_petition_get_signatures( array( 'petition_id' => $pid, 'per_page' => 12, 'page' => 1 ) ) : array();

    $categories = wp_list_pluck( wp_get_post_terms( $pid, 'cause_category' ), 'name' );
    ?>
    <main class="site-main sp-petition-single" role="main">

        <article class="sp-petition-single__hero divine-bg" aria-labelledby="sp-petition-title">
            <div class="sp-petition-single__hero-inner">
                <?php if ( $cover ) : ?>
                    <div class="sp-petition-single__cover">
                        <img src="<?php echo esc_url( $cover ); ?>" alt="" />
                    </div>
                <?php else : ?>
                    <div class="sp-petition-single__cover sp-petition-single__cover--icon">
                        <?php sp_icon_e( 'dharma', 'sp-icon--3xl sp-icon--saffron sp-icon--float', __( 'Petition', 'sampreshan-child' ) ); ?>
                    </div>
                <?php endif; ?>

                <div class="sp-petition-single__identity">
                    <?php if ( $categories ) : ?>
                        <ul class="sp-profile__chips">
                            <?php foreach ( $categories as $cat ) : ?>
                                <li class="chip-3d">
                                    <?php sp_icon_e( 'verified', 'sp-icon--sm sp-icon--saffron', __( 'Category', 'sampreshan-child' ) ); ?>
                                    <?php echo esc_html( $cat ); ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>

                    <h1 id="sp-petition-title" class="sp-petition-single__title display"><?php echo esc_html( $title ); ?></h1>

                    <div class="sp-petition-single__champion">
                        <?php if ( $author_avatar ) : ?>
                            <img class="sp-petition-single__avatar" src="<?php echo esc_url( $author_avatar ); ?>" alt="" loading="lazy" />
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
                        <?php if ( $deadline ) : ?>
                            <span aria-hidden="true">·</span>
                            <span><?php
                                /* translators: %s = deadline date */
                                printf( esc_html__( 'Ends %s', 'sampreshan-child' ), esc_html( date_i18n( 'M j, Y', strtotime( $deadline ) ) ) );
                            ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="sp-petition-single__progress" aria-label="Signature progress">
                        <div class="sp-petition-single__progress-label">
                            <span class="sp-petition-single__progress-count">
                                <span data-sp-signature-count><?php echo esc_html( number_format_i18n( $count ) ); ?></span>
                                <small><?php echo esc_html__( 'supporters', 'sampreshan-child' ); ?></small>
                            </span>
                            <span><?php
                                printf(
                                    /* translators: 1: percent, 2: goal */
                                    esc_html__( '%1$s%% of %2$s goal', 'sampreshan-child' ),
                                    '<strong>' . esc_html( $pct ) . '</strong>',
                                    esc_html( number_format_i18n( $goal ) )
                                );
                            ?></span>
                        </div>
                        <div class="sp-petition-single__progress-bar" role="progressbar" aria-valuenow="<?php echo esc_attr( $count ); ?>" aria-valuemin="0" aria-valuemax="<?php echo esc_attr( $goal ); ?>">
                            <div class="sp-petition-single__progress-fill" style="width: <?php echo esc_attr( $pct ); ?>%;"></div>
                        </div>
                    </div>

                    <div class="sp-petition-single__actions">
                        <?php if ( $can_sign ) : ?>
                            <div class="sp-petition-single__sign-form">
                                <textarea
                                    class="sp-sign-comment sp-form-textarea"
                                    rows="2"
                                    maxlength="300"
                                    placeholder="<?php esc_attr_e( 'Leave a message of support (optional)...', 'sampreshan-child' ); ?>"
                                    aria-label="<?php esc_attr_e( 'Support message', 'sampreshan-child' ); ?>"
                                ></textarea>
                                <button
                                    type="button"
                                    class="btn-3d btn-3d--lg sp-sign-button<?php echo $user_signed ? ' is-signed' : ''; ?>"
                                    data-petition-id="<?php echo esc_attr( $pid ); ?>"
                                    data-signed="<?php echo $user_signed ? '1' : '0'; ?>"
                                >
                                    <?php sp_icon_e( $user_signed ? 'check' : 'pen', 'sp-icon--sm sp-icon--white', $user_signed ? __( 'Signed', 'sampreshan-child' ) : __( 'Sign', 'sampreshan-child' ) ); ?>
                                    <?php echo $user_signed ? esc_html__( 'You signed — thank you!', 'sampreshan-child' ) : esc_html__( 'Sign this petition', 'sampreshan-child' ); ?>
                                </button>
                            </div>
                        <?php elseif ( ! is_user_logged_in() ) : ?>
                            <a class="btn-3d btn-3d--lg" href="<?php echo esc_url( wp_login_url( get_permalink() ) ); ?>">
                                <?php sp_icon_e( 'lock', 'sp-icon--sm sp-icon--white', __( 'Sign in', 'sampreshan-child' ) ); ?>
                                <?php esc_html_e( 'Sign in to support', 'sampreshan-child' ); ?>
                            </a>
                        <?php elseif ( 'closed' === $status ) : ?>
                            <span class="btn-3d btn-3d--lg" aria-disabled="true" style="opacity:.6">
                                <?php sp_icon_e( 'lock', 'sp-icon--sm sp-icon--white', __( 'Closed', 'sampreshan-child' ) ); ?>
                                <?php esc_html_e( 'Closed', 'sampreshan-child' ); ?>
                            </span>
                        <?php endif; ?>

                        <button
                            type="button"
                            class="btn-3d btn-3d--lg btn-3d--royal sp-share-btn"
                            data-url="<?php echo esc_url( get_permalink() ); ?>"
                            data-title="<?php echo esc_attr( $title ); ?>"
                        >
                            <?php sp_icon_e( 'share', 'sp-icon--sm sp-icon--white', __( 'Share', 'sampreshan-child' ) ); ?>
                            <?php esc_html_e( 'Share', 'sampreshan-child' ); ?>
                        </button>
                    </div>
                </div>
            </div>
        </article>

        <div class="sp-petition-single__body">
            <section class="sp-petition-single__content card-3d" aria-label="Petition content">
                <?php echo $content; ?>
            </section>

            <aside class="sp-petition-single__signers card-3d" aria-labelledby="sp-petition-signers-heading">
                <h2 id="sp-petition-signers-heading" class="sp-petition-single__signers-title">
                    <?php sp_icon_e( 'heart', 'sp-icon--md sp-icon--saffron', __( 'Supporters', 'sampreshan-child' ) ); ?>
                    <?php esc_html_e( 'Recent supporters', 'sampreshan-child' ); ?>
                </h2>
                <?php if ( $signatures ) : ?>
                    <ul class="sp-petition-single__signers-list">
                        <?php foreach ( $signatures as $sig ) : ?>
                            <li class="sp-petition-single__signer">
                                <?php echo get_avatar( $sig->user_id, 40 ); ?>
                                <span><?php echo $sig->is_anonymous ? esc_html__( 'Anonymous supporter', 'sampreshan-child' ) : esc_html( $sig->display_name ?: $sig->user_display ); ?></span>
                                <time datetime="<?php echo esc_attr( $sig->created_at ); ?>"><?php echo esc_html( human_time_diff( strtotime( $sig->created_at ), current_time( 'timestamp' ) ) ); ?></time>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else : ?>
                    <p class="sp-petition-single__signers-empty"><?php esc_html_e( 'Be the first to support this cause.', 'sampreshan-child' ); ?></p>
                <?php endif; ?>
            </aside>
        </div>

    </main>
    <?php
endwhile;

$footer_tpl = get_stylesheet_directory() . '/template-parts/footer/site-footer.php';
if ( file_exists( $footer_tpl ) ) {
    include $footer_tpl;
} else {
    get_footer();
}
