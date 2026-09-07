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
    // Cover placeholder + category chips show the cause's own symbol.
    $single_cause_icon = ( $categories && function_exists( 'sp_cause_icon' ) ) ? sp_cause_icon( $categories[0] ) : 'petition';
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
                        <?php sp_icon_e( $single_cause_icon, 'sp-icon--3xl sp-icon--saffron sp-icon--float', __( 'Petition', 'sampreshan-child' ) ); ?>
                    </div>
                <?php endif; ?>

                <div class="sp-petition-single__identity">
                    <?php if ( $categories ) : ?>
                        <ul class="sp-profile__chips">
                            <?php foreach ( $categories as $cat ) : ?>
                                <li class="chip-3d">
                                    <?php sp_icon_e( function_exists( 'sp_cause_icon' ) ? sp_cause_icon( $cat ) : 'petition', 'sp-icon--sm sp-icon--saffron', __( 'Category', 'sampreshan-child' ) ); ?>
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
                                <small><?php echo esc_html__( 'I', 'sampreshan-child' ); ?></small>
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
                    <?php if ( 'victory' === $status ) : ?>
                        <div class="sp-victory-banner" role="status">
                            <?php sp_icon_auto( 'verified', 'sp-icon--md', '' ); ?>
                            <div>
                                <strong><?php esc_html_e( 'Victory! This petition won.', 'sampreshan-child' ); ?></strong>
                                <span><?php esc_html_e( 'Thanks to every supporter who supported and shared this cause.', 'sampreshan-child' ); ?></span>
                            </div>
                        </div>
                    <?php endif; ?>

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
                                    <?php sp_icon_e( 'ibadge', 'sp-icon--sm', '' ); ?>
                                    <?php echo $user_signed ? esc_html__( 'Supported', 'sampreshan-child' ) : esc_html__( 'Support this issue', 'sampreshan-child' ); ?>
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
                        <?php if ( is_user_logged_in() ) :
                            $saved = function_exists( 'sp_is_petition_saved' ) && sp_is_petition_saved( $pid );
                        ?>
                            <button
                                type="button"
                                class="sp-save-btn<?php echo $saved ? ' is-saved' : ''; ?>"
                                data-petition-id="<?php echo esc_attr( $pid ); ?>"
                                data-saved="<?php echo $saved ? '1' : '0'; ?>"
                                aria-pressed="<?php echo $saved ? 'true' : 'false'; ?>"
                            >
                                <?php sp_icon_auto( 'bookmark', 'sp-icon--sm', __( 'Save', 'sampreshan-child' ) ); ?>
                                <span><?php echo $saved ? esc_html__( 'Saved', 'sampreshan-child' ) : esc_html__( 'Save', 'sampreshan-child' ); ?></span>
                            </button>
                        <?php endif; ?>
                        <?php if ( is_user_logged_in() && get_current_user_id() !== (int) $author_id ) : ?>
                            <button
                                type="button"
                                class="sp-report-toggle"
                                aria-expanded="false"
                            >
                                <?php sp_icon_auto( 'flag', 'sp-icon--sm', __( 'Report', 'sampreshan-child' ) ); ?>
                                <?php esc_html_e( 'Report', 'sampreshan-child' ); ?>
                            </button>
                        <?php endif; ?>
                    </div>
                    <?php if ( is_user_logged_in() && get_current_user_id() !== (int) $author_id ) : ?>
                        <form class="sp-report-form" data-petition-id="<?php echo esc_attr( $pid ); ?>" hidden novalidate>
                            <label class="sp-report-form__label" for="sp-report-reason-<?php echo esc_attr( $pid ); ?>">
                                <?php esc_html_e( 'Why are you reporting this?', 'sampreshan-child' ); ?>
                            </label>
                            <div class="sp-report-form__row">
                                <select id="sp-report-reason-<?php echo esc_attr( $pid ); ?>" name="reason" class="sp-report-form__select" required>
                                    <?php foreach ( sp_petition_report_reasons() as $key => $label ) : ?>
                                        <option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" class="btn-3d btn-3d--sm"><?php esc_html_e( 'Send report', 'sampreshan-child' ); ?></button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </article>

        <div class="sp-petition-single__body">
            <section class="sp-petition-single__content card-3d" aria-label="Petition content">
                <?php echo $content; ?>
            </section>

            <aside class="sp-petition-single__signers card-3d" aria-labelledby="sp-petition-signers-heading">
                <h2 id="sp-petition-signers-heading" class="sp-petition-single__signers-title">
                    <?php sp_icon_e( 'ibadge', 'sp-icon--md', __( 'Supporters', 'sampreshan-child' ) ); ?>
                    <?php esc_html_e( 'Recent supporters', 'sampreshan-child' ); ?>
                </h2>
                <?php if ( $signatures ) : ?>
                    <ul class="sp-petition-single__signers-list">
                        <?php foreach ( $signatures as $sig ) : ?>
                            <li class="sp-petition-single__signer">
                                <?php echo get_avatar( $sig->user_id, 40 ); ?>
                                <div class="sp-petition-single__signer-text">
                                    <span><?php echo $sig->is_anonymous ? esc_html__( 'Anonymous supporter', 'sampreshan-child' ) : esc_html( $sig->display_name ?: $sig->user_display ); ?></span>
                                    <?php if ( ! empty( $sig->comment ) ) : ?>
                                        <span class="sp-petition-single__signer-reason">&ldquo;<?php echo esc_html( wp_trim_words( $sig->comment, 30 ) ); ?>&rdquo;</span>
                                    <?php endif; ?>
                                    <time datetime="<?php echo esc_attr( $sig->created_at ); ?>"><?php echo esc_html( human_time_diff( strtotime( $sig->created_at ), current_time( 'timestamp' ) ) ); ?></time>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else : ?>
                    <p class="sp-petition-single__signers-empty"><?php esc_html_e( 'Be the first to support this cause.', 'sampreshan-child' ); ?></p>
                <?php endif; ?>
            </aside>
        </div>

        <?php $sp_updates = function_exists( 'sp_get_petition_updates' ) ? sp_get_petition_updates( $pid ) : array(); ?>
        <section class="sp-petition-single__updates card-3d" aria-labelledby="sp-updates-heading">
            <h2 id="sp-updates-heading" class="sp-petition-single__signers-title">
                <?php sp_icon_auto( 'bell', 'sp-icon--md sp-icon--saffron', __( 'Updates', 'sampreshan-child' ) ); ?>
                <?php esc_html_e( 'Updates from the starter', 'sampreshan-child' ); ?>
            </h2>
            <?php if ( function_exists( 'sp_can_post_petition_update' ) && sp_can_post_petition_update( $pid ) ) : ?>
                <form class="sp-update-form" data-petition-id="<?php echo esc_attr( $pid ); ?>" novalidate>
                    <label class="sp-update-form__label" for="sp-update-text-<?php echo esc_attr( $pid ); ?>">
                        <?php esc_html_e( 'Share progress with your supporters', 'sampreshan-child' ); ?>
                    </label>
                    <textarea
                        id="sp-update-text-<?php echo esc_attr( $pid ); ?>"
                        name="text"
                        class="sp-form-textarea"
                        rows="3"
                        maxlength="1000"
                        required
                        placeholder="<?php esc_attr_e( 'What happened since you started? Meetings, milestones, media coverage…', 'sampreshan-child' ); ?>"
                    ></textarea>
                    <button type="submit" class="btn-3d btn-3d--sm"><?php esc_html_e( 'Post update', 'sampreshan-child' ); ?></button>
                </form>
            <?php endif; ?>
            <?php if ( ! empty( $sp_updates ) ) : ?>
                <ul class="sp-updates-list">
                    <?php foreach ( $sp_updates as $u ) : ?>
                        <li class="sp-updates-list__item">
                            <p class="sp-updates-list__text"><?php echo esc_html( $u['text'] ); ?></p>
                            <time class="sp-updates-list__time" datetime="<?php echo esc_attr( $u['time'] ); ?>"><?php echo esc_html( human_time_diff( strtotime( $u['time'] ), current_time( 'timestamp' ) ) ); ?> <?php esc_html_e( 'ago', 'sampreshan-child' ); ?></time>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else : ?>
                <p class="sp-petition-single__signers-empty"><?php esc_html_e( 'No updates yet. The starter will post progress here.', 'sampreshan-child' ); ?></p>
            <?php endif; ?>
        </section>

        <section class="sp-petition-single__comments card-3d" aria-labelledby="sp-discussion-heading">
            <h2 id="sp-discussion-heading" class="sp-petition-single__signers-title">
                <?php sp_icon_auto( 'comment', 'sp-icon--md sp-icon--saffron', __( 'Discussion', 'sampreshan-child' ) ); ?>
                <?php esc_html_e( 'Discussion', 'sampreshan-child' ); ?>
            </h2>
            <?php
            if ( comments_open() || get_comments_number() ) {
                comments_template();
            } else {
                echo '<p class="sp-petition-single__signers-empty">' . esc_html__( 'Discussion is closed for this petition.', 'sampreshan-child' ) . '</p>';
            }
            ?>
        </section>

    </main>
    <?php
endwhile;

$footer_tpl = get_stylesheet_directory() . '/template-parts/footer/site-footer.php';
if ( file_exists( $footer_tpl ) ) {
    include $footer_tpl;
} else {
    get_footer();
}
