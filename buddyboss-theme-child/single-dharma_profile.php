<?php
/**
 * Dharma profile.
 *
 * @package SampreShan_Child
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

get_header();
while ( have_posts() ) : the_post();
    $profile_id = get_the_ID();
    $peeth      = (string) get_post_meta( $profile_id, '_sp_dharma_peeth', true );
    $direction  = (string) get_post_meta( $profile_id, '_sp_dharma_direction', true );
    $veda       = (string) get_post_meta( $profile_id, '_sp_dharma_veda', true );
    $mahavakya  = (string) get_post_meta( $profile_id, '_sp_dharma_mahavakya', true );
    $image_url  = sp_dharma_profile_image_url( $profile_id, 'large' );
    $updates    = sp_dharma_updates_for_profiles( array( $profile_id ), 10 );
?>
<main id="main" class="sp-page sp-dharma-profile" role="main">
    <header class="sp-dharma-profile__hero">
        <p class="sp-section__eyebrow"><?php echo esc_html( $peeth ); ?></p>
        <h1><?php the_title(); ?></h1>
        <?php if ( $image_url ) : ?><img class="sp-dharma-profile__image" src="<?php echo esc_url( $image_url ); ?>" alt="" width="960" height="540" loading="eager" /><?php endif; ?>
        <?php if ( $mahavakya ) : ?><p class="sp-dharma-profile__maha"><?php echo esc_html( $mahavakya ); ?></p><?php endif; ?>
        <p class="sp-dharma-profile__intro"><?php echo esc_html( get_the_excerpt() ); ?></p>
        <div class="sp-dharma-profile__actions">
            <?php sp_dharma_follow_button( $profile_id ); ?>
            <a class="btn btn--ghost" href="<?php echo esc_url( add_query_arg( 'profile', $profile_id, home_url( '/start-a-petition/' ) ) ); ?>"><?php esc_html_e( 'Raise a connected issue', 'sampreshan-child' ); ?></a>
            <a class="btn btn--ghost" href="<?php echo esc_url( sp_dharma_directory_url() ); ?>"><?php esc_html_e( 'All profiles', 'sampreshan-child' ); ?></a>
        </div>
        <p class="sp-dharma-profile__followers"><?php echo esc_html( sprintf( _n( '%s member follows this profile', '%s members follow this profile', sp_dharma_follow_count( $profile_id ), 'sampreshan-child' ), number_format_i18n( sp_dharma_follow_count( $profile_id ) ) ) ); ?></p>
    </header>
    <div class="sp-dharma-profile__layout">
        <article class="sp-dharma-content">
            <?php the_content(); ?>
        </article>
        <aside class="sp-dharma-facts-panel" aria-label="<?php esc_attr_e( 'Peeth information', 'sampreshan-child' ); ?>">
            <h2><?php esc_html_e( 'Peeth information', 'sampreshan-child' ); ?></h2>
            <dl><div><dt><?php esc_html_e( 'Peeth', 'sampreshan-child' ); ?></dt><dd><?php echo esc_html( $peeth ); ?></dd></div><div><dt><?php esc_html_e( 'Direction', 'sampreshan-child' ); ?></dt><dd><?php echo esc_html( $direction ); ?></dd></div><div><dt><?php esc_html_e( 'Veda', 'sampreshan-child' ); ?></dt><dd><?php echo esc_html( $veda ); ?></dd></div><?php if ( $mahavakya ) : ?><div><dt><?php esc_html_e( 'Mahavakya', 'sampreshan-child' ); ?></dt><dd><?php echo esc_html( $mahavakya ); ?></dd></div><?php endif; ?></dl>
        </aside>
    </div>
    <section class="sp-dharma-profile__updates" aria-labelledby="sp-dharma-updates-title">
        <p class="sp-section__eyebrow"><?php esc_html_e( 'Verified updates', 'sampreshan-child' ); ?></p>
        <h2 id="sp-dharma-updates-title"><?php esc_html_e( 'Programmes, activities & news', 'sampreshan-child' ); ?></h2>
        <?php if ( $updates->have_posts() ) : ?>
            <div class="sp-dharma-update-list"><?php while ( $updates->have_posts() ) : $updates->the_post(); ?><article><p><?php echo esc_html( get_the_date() ); ?></p><h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3><p><?php echo esc_html( get_the_excerpt() ); ?></p></article><?php endwhile; wp_reset_postdata(); ?></div>
        <?php else : ?>
            <p class="sp-dharma-muted"><?php esc_html_e( 'Verified programme, activity, and news updates will appear here when published.', 'sampreshan-child' ); ?></p>
        <?php endif; ?>
    </section>
</main>
<?php endwhile; get_footer(); ?>
