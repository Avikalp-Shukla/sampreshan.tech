<?php
/**
 * Template Name: Community Feed
 *
 * Latest petitions + posts in one clean river.
 *
 * @package SampreShan_Child
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

get_header();

$items = new WP_Query( array(
    'post_type'      => array( 'petition', 'post' ),
    'post_status'    => 'publish',
    'posts_per_page' => 15,
    'orderby'        => 'date',
    'order'          => 'DESC',
) );

$show_story_form = is_user_logged_in() && ! empty( $_GET['new_story'] );
$start_url       = home_url( '/start-a-petition/' );
?>

<main id="main" class="sp-page sp-users sp-users--narrow" role="main">
    <header class="sp-users__hero sp-users__hero--center">
        <p class="sp-section__eyebrow"><?php esc_html_e( 'Fresh from the community', 'sampreshan-child' ); ?></p>
        <h1 class="sp-users__title"><?php sp_icon_auto( 'feed', 'sp-icon--md sp-icon--saffron', '' ); ?> <?php esc_html_e( 'Community Feed', 'sampreshan-child' ); ?></h1>
        <p class="sp-users__sub"><?php esc_html_e( 'The newest petitions and stories — sign, share, and join the conversation.', 'sampreshan-child' ); ?></p>
    </header>

    <?php if ( $show_story_form ) : ?>
        <div class="sp-dash-card sp-dash-card--pad sp-community-composer">
            <div class="sp-dash-card__head sp-community-composer__head">
                <div>
                    <h2 class="sp-dash-card__title"><?php esc_html_e( 'Share a community update', 'sampreshan-child' ); ?></h2>
                    <p class="sp-dash-card__sub"><?php esc_html_e( 'Post a quick update, story, or community note.', 'sampreshan-child' ); ?></p>
                </div>
            </div>
            <form method="post" action="<?php echo esc_url( $GLOBALS['wp']->request ? home_url( $GLOBALS['wp']->request ) : home_url( '/community/' ) ); ?>" enctype="multipart/form-data" class="sp-community-composer__form">
                <?php wp_nonce_field( 'sampreshan_community_story', 'sampreshan_community_story_nonce' ); ?>
                <textarea name="sampreshan_story" rows="5" placeholder="Write something for the community..." class="sp-community-composer__field" required></textarea>

                <div class="sp-story-image-wrap">
                    <label for="sampreshan_story_image" class="sp-community-composer__label">
                        <?php esc_html_e( 'Add image', 'sampreshan-child' ); ?>
                    </label>
                    <input id="sampreshan_story_image" type="file" name="sampreshan_story_image" accept="image/*" class="sp-community-composer__input" />
                    <img id="sampreshan_story_image_preview" alt="" class="sp-community-composer__preview" />
                </div>

                <div class="sp-community-composer__actions">
                    <a class="btn btn--ghost" href="<?php echo esc_url( remove_query_arg( 'new_story' ) ); ?>"><?php esc_html_e( 'Cancel', 'sampreshan-child' ); ?></a>
                    <button class="btn btn--primary" type="submit"><?php esc_html_e( 'Post to community', 'sampreshan-child' ); ?></button>
                </div>
            </form>
        </div>
    <?php endif; ?>

    <?php if ( $items->have_posts() ) : ?>
        <div class="sp-feed">
            <?php while ( $items->have_posts() ) : $items->the_post();
                $is_pet  = 'petition' === get_post_type();
                $pid     = get_the_ID();
                $sig     = $is_pet ? (int) get_post_meta( $pid, 'sampreshan_signatures', true ) : 0;
                $goal    = $is_pet ? (int) get_post_meta( $pid, 'sampreshan_goal', true ) : 0;
                $prog    = ( $is_pet && $goal > 0 ) ? min( 100, round( ( $sig / $goal ) * 100 ) ) : 0;
                $aid     = (int) get_post_field( 'post_author', $pid );
                $avatar  = get_avatar_url( $aid, array( 'size' => 80 ) );
            ?>
                <article class="sp-feed__item sp-dash-card sp-dash-card--pad">
                    <div class="sp-feed__head">
                        <span class="sp-feed__avatar" aria-hidden="true">
                            <?php if ( $avatar ) : ?>
                                <img src="<?php echo esc_url( $avatar ); ?>" alt="" width="36" height="36" loading="lazy" />
                            <?php else : ?>
                                <?php sp_icon_auto( 'account', 'sp-icon--sm', '' ); ?>
                            <?php endif; ?>
                        </span>
                        <div class="sp-feed__who">
                            <strong><?php echo esc_html( get_the_author_meta( 'display_name', $aid ) ); ?></strong>
                            <small>
                                <?php echo $is_pet ? esc_html__( 'started a petition', 'sampreshan-child' ) : esc_html__( 'posted a story', 'sampreshan-child' ); ?>
                                &middot; <?php echo esc_html( human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ) ); ?> <?php esc_html_e( 'ago', 'sampreshan-child' ); ?>
                            </small>
                        </div>
                        <span class="sp-dash-pill <?php echo $is_pet ? 'sp-dash-pill--active' : 'sp-dash-pill--draft'; ?>">
                            <?php echo $is_pet ? esc_html__( 'Petition', 'sampreshan-child' ) : esc_html__( 'Story', 'sampreshan-child' ); ?>
                        </span>
                    </div>
                    <h2 class="sp-feed__title"><a href="<?php echo esc_url( get_permalink() ); ?>"><?php the_title(); ?></a></h2>
                    <p class="sp-feed__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt() ?: wp_strip_all_tags( get_the_content() ), 28 ) ); ?></p>
                    <?php if ( $is_pet ) : ?>
                        <div class="sp-dash-bar"><span class="sp-dash-bar__fill" style="width: <?php echo esc_attr( $prog ); ?>%"></span></div>
                        <p class="sp-dash-row__meta">
                            <?php echo esc_html( sprintf( _n( '%s I', '%s Is', $sig, 'sampreshan-child' ), number_format_i18n( $sig ) ) ); ?>
                            <?php if ( $goal > 0 ) : ?>&middot; <?php echo esc_html( sprintf( __( 'goal %s', 'sampreshan-child' ), number_format_i18n( $goal ) ) ); ?><?php endif; ?>
                        </p>
                    <?php endif; ?>
                    <div class="sp-dash-row__actions">
                        <a class="sp-dash-link" href="<?php echo esc_url( get_permalink() ); ?>"><?php echo $is_pet ? esc_html__( 'View & sign →', 'sampreshan-child' ) : esc_html__( 'Read →', 'sampreshan-child' ); ?></a>
                        <button class="sp-dash-link sp-share-btn" type="button" data-url="<?php echo esc_url( get_permalink() ); ?>" data-title="<?php echo esc_attr( get_the_title() ); ?>"><?php esc_html_e( 'Share', 'sampreshan-child' ); ?></button>
                    </div>
                </article>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>
    <?php else : ?>
        <div class="sp-dash-empty sp-dash-card">
            <h3 class="sp-dash-empty__title"><?php esc_html_e( 'Nothing here yet', 'sampreshan-child' ); ?></h3>
            <p class="sp-dash-empty__desc"><?php esc_html_e( 'Be the first to start a conversation.', 'sampreshan-child' ); ?></p>
            <a class="btn btn--primary" href="<?php echo esc_url( $start_url ); ?>"><?php esc_html_e( 'Start a Petition', 'sampreshan-child' ); ?></a>
        </div>
    <?php endif; ?>
</main>

<style>
.sp-community-composer {
    max-width: 760px;
    margin: 0 auto;
    width: 100%;
    background: linear-gradient(180deg, rgba(17, 24, 39, 0.92), rgba(9, 14, 22, 0.98));
    border: 1px solid rgba(255, 184, 92, 0.25);
    box-shadow: 0 22px 44px rgba(3, 7, 18, 0.42);
}

.sp-community-composer__head {
    padding: 0 0 1rem;
    border-bottom: 1px solid rgba(255, 191, 125, 0.18);
    margin-bottom: 1rem;
}

.sp-community-composer__form {
    display: grid;
    gap: 1rem;
}

.sp-community-composer__field {
    width: 100%;
    min-height: 170px;
    border: 1px solid rgba(255, 209, 145, 0.26);
    border-radius: 18px;
    padding: 1rem 1.05rem;
    font: 400 1rem/1.7 "SFMono-Regular", "Consolas", "Liberation Mono", monospace;
    resize: vertical;
    color: #f8fafc;
    background: linear-gradient(180deg, rgba(15, 23, 42, 0.96), rgba(9, 14, 22, 0.92));
    box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.02), inset 0 0 22px rgba(0, 0, 0, 0.25);
    transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
    letter-spacing: 0.02em;
}

.sp-community-composer__field::placeholder {
    color: rgba(226, 232, 240, 0.62);
}

.sp-community-composer__field:focus {
    outline: none;
    border-color: rgba(251, 146, 60, 0.9);
    box-shadow: 0 0 0 3px rgba(251, 146, 60, 0.18), inset 0 0 22px rgba(0, 0, 0, 0.22);
}

.sp-community-composer__label {
    display: block;
    margin-bottom: 0.45rem;
    font-weight: 700;
    color: #f8fafc;
}

.sp-community-composer__input {
    display: block;
    width: 100%;
    padding: 0.8rem 0.9rem;
    border: 1px dashed rgba(255, 183, 92, 0.48);
    border-radius: 12px;
    background: rgba(12, 18, 28, 0.75);
    color: #f8fafc;
}

.sp-community-composer__preview {
    display: none;
    width: 100%;
    max-height: 260px;
    object-fit: cover;
    margin-top: 0.9rem;
    border-radius: 12px;
    border: 1px solid rgba(255, 183, 92, 0.26);
    box-shadow: 0 12px 30px rgba(2, 6, 23, 0.26);
}

.sp-community-composer__actions {
    display: flex;
    justify-content: flex-end;
    gap: 0.75rem;
    margin-top: 0.25rem;
    flex-wrap: wrap;
}

@media (max-width: 560px) {
    .sp-community-composer {
        border-radius: 18px;
    }
    .sp-community-composer__field {
        min-height: 150px;
        font-size: 0.96rem;
    }
    .sp-community-composer__actions {
        justify-content: stretch;
    }
    .sp-community-composer__actions > * {
        flex: 1 1 auto;
    }
}
</style>

<script>
(function () {
    var input = document.getElementById('sampreshan_story_image');
    var preview = document.getElementById('sampreshan_story_image_preview');
    if (!input || !preview) { return; }

    input.addEventListener('change', function () {
        var file = this.files && this.files[0];
        if (!file) {
            preview.style.display = 'none';
            preview.src = '';
            return;
        }
        var reader = new FileReader();
        reader.onload = function (event) {
            preview.src = event.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    });
})();
</script>

<?php get_footer(); ?>
