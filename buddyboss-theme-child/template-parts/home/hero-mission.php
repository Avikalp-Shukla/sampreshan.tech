<?php
/**
 * Home: Hero / Mission Section — Clean Edition
 * Explains what Sampreshan is in one glance. Uses the original logo.
 *
 * @package SampreShan_Child
 */

$site_name  = get_bloginfo( 'name' );
$start_url  = home_url( '/start-a-petition/' );
$about_url  = home_url( '/about/' );
$feed_url   = function_exists( 'bp_get_activity_directory_permalink' ) ? bp_get_activity_directory_permalink() : home_url( '/activity/' );
$logo_url   = function_exists( 'sp_logo_url' ) ? sp_logo_url() : content_url( 'uploads/2026/06/sampreshan-logo-svg.svg' );
$is_logged_in = is_user_logged_in();
?>
<section class="sp-hero" aria-label="Welcome to <?php echo esc_attr( $site_name ); ?>">
    <div class="sp-hero__inner">
        <div class="sp-hero__copy">
            <p class="sp-hero__badge">
                <img class="sp-hero__badge-logo" src="<?php echo esc_url( $logo_url ); ?>" alt="" width="20" height="20" />
                <?php esc_html_e( 'A non-commercial initiative of ShivBodh Trust', 'sampreshan-child' ); ?>
            </p>
            <h1 class="sp-hero__title">
                <?php esc_html_e( 'One voice for', 'sampreshan-child' ); ?>
                <span class="sp-hero__accent"><?php esc_html_e( 'Sanatan Dharma.', 'sampreshan-child' ); ?></span>
            </h1>
            <p class="sp-hero__sub">
                <?php esc_html_e( 'Sampreshan is a community platform where you can raise local issues, start petitions, gather signatures, and connect with people who share Dharmic values — together turning a single voice into collective impact.', 'sampreshan-child' ); ?>
            </p>
            <div class="sp-hero__actions">
                <a class="btn btn--primary btn--lg" href="<?php echo esc_url( $start_url ); ?>">
                    <?php sp_icon_e( 'plus', 'sp-icon--sm', '' ); ?>
                    <?php esc_html_e( 'Start a Petition', 'sampreshan-child' ); ?>
                </a>
                <a class="btn btn--ghost btn--lg" href="<?php echo esc_url( $is_logged_in ? $feed_url : $about_url ); ?>">
                    <?php echo $is_logged_in ? esc_html__( 'Explore Community', 'sampreshan-child' ) : esc_html__( 'What is Sampreshan?', 'sampreshan-child' ); ?>
                </a>
            </div>
            <ul class="sp-hero__points">
                <li><?php sp_icon_e( 'check', 'sp-icon--sm', '' ); ?><?php esc_html_e( 'No donations, no fees', 'sampreshan-child' ); ?></li>
                <li><?php sp_icon_e( 'check', 'sp-icon--sm', '' ); ?><?php esc_html_e( 'Every tradition respected', 'sampreshan-child' ); ?></li>
                <li><?php sp_icon_e( 'check', 'sp-icon--sm', '' ); ?><?php esc_html_e( 'Hindi + English friendly', 'sampreshan-child' ); ?></li>
            </ul>
        </div>
        <div class="sp-hero__art" aria-hidden="true">
            <div class="sp-hero__logo-card">
                <img class="sp-hero__logo" src="<?php echo esc_url( $logo_url ); ?>" alt="" width="120" height="120" fetchpriority="high" />
                <p class="sp-hero__logo-name"><?php echo esc_html( $site_name ); ?></p>
                <p class="sp-hero__logo-tag"><?php esc_html_e( 'Sampreshan · Samvad · Samarthan', 'sampreshan-child' ); ?></p>
            </div>
        </div>
    </div>
</section>
