<?php
/**
 * Template Name: Community Members
 *
 * Member directory (BuddyBoss-aware, WP fallback).
 *
 * @package SampreShan_Child
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

get_header();

$search = isset( $_GET['q'] ) ? sanitize_text_field( wp_unslash( $_GET['q'] ) ) : '';
$paged  = max( 1, (int) get_query_var( 'paged', 1 ) );

$user_args = array(
    'number'  => 24,
    'paged'   => $paged,
    'orderby' => 'registered',
    'order'   => 'DESC',
    'fields'  => 'all',
);
if ( '' !== $search ) {
    $user_args['search']         = '*' . $search . '*';
    $user_args['search_columns'] = array( 'user_login', 'user_nicename', 'display_name' );
}
$members_q = new WP_User_Query( $user_args );
$members   = $members_q->get_results();
$total     = (int) $members_q->get_total();
$pages     = (int) ceil( $total / 24 );

$bb_members_url = function_exists( 'bp_get_members_directory_permalink' ) ? bp_get_members_directory_permalink() : '';
?>

<main id="main" class="sp-page sp-users" role="main">
    <header class="sp-users__hero">
        <p class="sp-section__eyebrow"><?php esc_html_e( 'The sangha', 'sampreshan-child' ); ?></p>
        <h1 class="sp-users__title"><?php sp_icon_auto( 'network', 'sp-icon--md sp-icon--saffron', '' ); ?> <?php esc_html_e( 'Community Members', 'sampreshan-child' ); ?></h1>
        <p class="sp-users__sub">
            <?php
            printf(
                /* translators: %s = count */
                esc_html__( '%s members strong — connect, follow causes, and grow the voice of Dharma together.', 'sampreshan-child' ),
                esc_html( number_format_i18n( $total ) )
            );
            ?>
        </p>
    </header>

    <form class="sp-users__filters" method="get" action="<?php echo esc_url( get_permalink() ); ?>" role="search">
        <label class="sp-users__search">
            <span class="screen-reader-text"><?php esc_html_e( 'Search members', 'sampreshan-child' ); ?></span>
            <?php sp_icon_auto( 'search', 'sp-icon--sm', '' ); ?>
            <input type="search" name="q" value="<?php echo esc_attr( $search ); ?>" placeholder="<?php esc_attr_e( 'Search members…', 'sampreshan-child' ); ?>" />
        </label>
        <button class="btn btn--ghost" type="submit"><?php esc_html_e( 'Search', 'sampreshan-child' ); ?></button>
        <?php if ( '' !== $search ) : ?>
            <a class="sp-text-link" href="<?php echo esc_url( get_permalink() ); ?>"><?php esc_html_e( 'Clear', 'sampreshan-child' ); ?></a>
        <?php endif; ?>
    </form>

    <?php if ( ! empty( $members ) ) : ?>
        <div class="sp-member-grid">
            <?php foreach ( $members as $m ) :
                $mid      = (int) $m->ID;
                $avatar   = get_avatar_url( $mid, array( 'size' => 160 ) );
                $loc      = function_exists( 'sp_profile_get_field' ) ? sp_profile_get_field( 'location', $mid ) : '';
                $bio      = function_exists( 'sp_profile_get_field' ) ? sp_profile_get_field( 'bio', $mid ) : '';
                $prof_url = function_exists( 'bp_core_get_user_domain' ) ? bp_core_get_user_domain( $mid ) : home_url( '/members/' . $m->user_nicename . '/' );
                $pet_n    = count_user_posts( $mid, 'petition' );
            ?>
                <article class="sp-member-card">
                    <a class="sp-member-card__avatar" href="<?php echo esc_url( $prof_url ); ?>" tabindex="-1" aria-hidden="true">
                        <?php if ( $avatar ) : ?>
                            <img src="<?php echo esc_url( $avatar ); ?>" alt="" loading="lazy" width="72" height="72" />
                        <?php else : ?>
                            <span class="sp-dash-avatar__initial"><?php echo esc_html( mb_substr( $m->display_name, 0, 1 ) ); ?></span>
                        <?php endif; ?>
                    </a>
                    <h2 class="sp-member-card__name"><a href="<?php echo esc_url( $prof_url ); ?>"><?php echo esc_html( $m->display_name ); ?></a></h2>
                    <p class="sp-member-card__handle">@<?php echo esc_html( $m->user_nicename ); ?></p>
                    <?php if ( $loc ) : ?>
                        <p class="sp-member-card__loc"><?php sp_icon_auto( 'location', 'sp-icon--xs', '' ); ?> <?php echo esc_html( $loc ); ?></p>
                    <?php endif; ?>
                    <?php if ( $bio ) : ?>
                        <p class="sp-member-card__bio"><?php echo esc_html( mb_substr( $bio, 0, 90 ) ); ?></p>
                    <?php endif; ?>
                    <p class="sp-member-card__stats">
                        <?php echo esc_html( sprintf( _n( '%s petition', '%s petitions', $pet_n, 'sampreshan-child' ), number_format_i18n( $pet_n ) ) ); ?>
                    </p>
                    <a class="sp-dash-link" href="<?php echo esc_url( $prof_url ); ?>"><?php esc_html_e( 'View profile →', 'sampreshan-child' ); ?></a>
                </article>
            <?php endforeach; ?>
        </div>
        <?php if ( $pages > 1 ) : ?>
            <nav class="sp-users__pagination" aria-label="Members pages">
                <?php
                echo paginate_links( array(
                    'total'   => $pages,
                    'current' => $paged,
                    'prev_text' => __( '&larr; Prev', 'sampreshan-child' ),
                    'next_text' => __( 'Next &rarr;', 'sampreshan-child' ),
                ) );
                ?>
            </nav>
        <?php endif; ?>
    <?php else : ?>
        <div class="sp-dash-empty sp-dash-card">
            <h3 class="sp-dash-empty__title"><?php esc_html_e( 'No members found', 'sampreshan-child' ); ?></h3>
            <p class="sp-dash-empty__desc"><?php esc_html_e( 'Try a different search.', 'sampreshan-child' ); ?></p>
        </div>
    <?php endif; ?>
</main>

<?php get_footer(); ?>
