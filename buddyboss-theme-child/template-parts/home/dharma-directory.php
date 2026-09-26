<?php
/**
 * Home: Dharma Acharya and Peeth discovery.
 *
 * @package SampreShan_Child
 */

if ( ! function_exists( 'sp_dharma_profile_post_type' ) ) {
    return;
}

$profiles = get_posts(
    array(
        'post_type'      => sp_dharma_profile_post_type(),
        'post_status'    => 'publish',
        'posts_per_page' => 4,
        'orderby'        => 'menu_order title',
        'order'          => 'ASC',
    )
);

if ( empty( $profiles ) ) {
    return;
}
?>
<section class="home-section sp-dharma-home" aria-labelledby="sp-dharma-home-title">
    <div class="sp-fu-sechead">
        <div>
            <p class="sp-fu-eyebrow"><?php esc_html_e( 'Dharma directory', 'sampreshan-child' ); ?></p>
            <h2 id="sp-dharma-home-title" class="sp-fu-sectitle"><?php esc_html_e( 'Acharyas & Peeths', 'sampreshan-child' ); ?></h2>
        </div>
        <a class="sp-fu-link" href="<?php echo esc_url( sp_dharma_directory_url() ); ?>"><?php esc_html_e( 'Explore all profiles', 'sampreshan-child' ); ?> &rarr;</a>
    </div>
    <p class="sp-dharma-home__intro"><?php esc_html_e( 'Find recognised Peeth traditions, understand their Vedic associations, and choose Anusaran to receive verified programme, activity, and news updates.', 'sampreshan-child' ); ?></p>
    <div class="sp-dharma-card-grid">
        <?php foreach ( $profiles as $profile ) : ?>
            <?php
            $peeth     = (string) get_post_meta( $profile->ID, '_sp_dharma_peeth', true );
            $mahavakya = (string) get_post_meta( $profile->ID, '_sp_dharma_mahavakya', true );
            $kind      = (string) get_post_meta( $profile->ID, '_sp_dharma_kind', true );
            $image_url = sp_dharma_profile_image_url( $profile->ID, 'medium_large' );
            ?>
            <article class="sp-dharma-card">
                <?php if ( $image_url ) : ?><a class="sp-dharma-card__image" href="<?php echo esc_url( get_permalink( $profile ) ); ?>" tabindex="-1" aria-hidden="true"><img src="<?php echo esc_url( $image_url ); ?>" alt="" loading="lazy" /></a><?php endif; ?>
                <?php if ( $kind ) : ?><p class="sp-dharma-card__kind"><?php echo esc_html( 'peeth' === $kind ? __( 'Peeth profile', 'sampreshan-child' ) : __( 'Acharya profile', 'sampreshan-child' ) ); ?></p><?php endif; ?>
                <p class="sp-dharma-card__peeth"><?php echo esc_html( $peeth ); ?></p>
                <h3><a href="<?php echo esc_url( get_permalink( $profile ) ); ?>"><?php echo esc_html( $profile->post_title ); ?></a></h3>
                <?php if ( $mahavakya ) : ?><p class="sp-dharma-card__maha"><?php echo esc_html( $mahavakya ); ?></p><?php endif; ?>
                <div class="sp-dharma-card__footer">
                    <a href="<?php echo esc_url( get_permalink( $profile ) ); ?>"><?php esc_html_e( 'View profile', 'sampreshan-child' ); ?></a>
                    <?php sp_dharma_follow_button( $profile->ID, 'sp-dharma-follow--compact' ); ?>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>
