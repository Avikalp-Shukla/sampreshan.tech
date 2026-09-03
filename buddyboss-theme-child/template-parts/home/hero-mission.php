<?php
/**
 * Home: Hero / Mission Section
 * Premium saffron gradient hero with site tagline + mission.
 * No AI content. No emojis. Uses real blog info only.
 *
 * @package SampreShan_Child
 */

$site_name        = get_bloginfo( 'name' );
$site_description = get_bloginfo( 'description' );
$site_url         = home_url( '/' );
$start_url        = function_exists( 'wc_get_page_id' ) ? '#' : home_url( '/start-a-petition/' );
$about_url        = home_url( '/about/' );
?>
<section class="home-hero" aria-label="Welcome to <?php echo esc_attr( $site_name ); ?>">
    <div class="home-hero__pattern" aria-hidden="true"></div>

    <div class="home-hero__inner">
        <p class="home-hero__eyebrow">
            <?php echo esc_html__( 'Join the Sanatan Sampreshan', 'sampreshan-child' ); ?>
        </p>

        <h1 class="home-hero__title display">
            <?php echo esc_html( $site_name ); ?>
        </h1>

        <p class="home-hero__sub">
            <?php echo esc_html__( 'A non-commercial platform by ShivBodh Trust where Sanatana Dharma followers connect, raise local issues, share perspectives, and gather signatures to amplify a single voice into collective impact.', 'sampreshan-child' ); ?>
        </p>

        <div class="home-hero__actions">
            <a class="btn btn--primary btn--lg" href="<?php echo esc_url( $start_url ); ?>">
                <?php echo esc_html__( 'Start a Petition', 'sampreshan-child' ); ?>
            </a>
            <a class="btn btn--outline btn--lg" href="<?php echo esc_url( $about_url ); ?>">
                <?php echo esc_html__( 'Learn More', 'sampreshan-child' ); ?>
            </a>
        </div>
    </div>
</section>
