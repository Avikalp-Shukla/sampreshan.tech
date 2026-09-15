<?php
/**
 * Home: Hero / Mission — Premium 3D Dark Edition
 * Full-viewport dark immersive, CSS perspective 3D floating object,
 * dramatic lighting, scroll-triggered reveals.
 *
 * @package SampreShan_Child
 */

$site_name  = get_bloginfo( 'name' );
$start_url  = home_url( '/start-a-petition/' );
$about_url  = home_url( '/about/' );
$feed_url   = function_exists( 'sp_feed_url' ) ? sp_feed_url() : home_url( '/feed-2/' );
$logo_url   = function_exists( 'sp_logo_url' ) ? sp_logo_url() : content_url( 'uploads/2026/06/sampreshan-logo-svg.svg' );
$is_logged_in = is_user_logged_in();
?>
<section class="d3-hero" aria-label="Welcome to <?php echo esc_attr( $site_name ); ?>">
    <div class="d3-hero__light" aria-hidden="true"></div>
    <div class="d3-hero__grid" aria-hidden="true"></div>

    <div class="d3-hero__grid-inner">
        <div class="d3-hero__copy">
            <p class="d3-hero__eyebrow">
                <?php esc_html_e( 'Sanatan Voice Platform', 'sampreshan-child' ); ?>
            </p>
            <h1 class="d3-hero__title">
                <?php esc_html_e( 'One voice for', 'sampreshan-child' ); ?>
                <span class="d3-hero__accent"><?php esc_html_e( 'Sanatan Dharma.', 'sampreshan-child' ); ?></span>
            </h1>
            <p class="d3-hero__sub">
                <?php esc_html_e( 'A worldwide community platform where Sanatanis connect, raise issues, start petitions, and gather support — every tool of social media, plus Issue Sampreshan for Dharma.', 'sampreshan-child' ); ?>
            </p>
            <div class="d3-hero__actions">
                <a class="d3-btn d3-btn--primary d3-btn--lg" href="<?php echo esc_url( $start_url ); ?>">
                    <?php sp_icon_auto( 'plus', 'sp-icon--sm', '' ); ?>
                    <?php esc_html_e( 'Start a Petition', 'sampreshan-child' ); ?>
                </a>
                <a class="d3-btn d3-btn--ghost d3-btn--lg" href="<?php echo esc_url( $is_logged_in ? $feed_url : $about_url ); ?>">
                    <?php echo $is_logged_in ? esc_html__( 'Explore Community', 'sampreshan-child' ) : esc_html__( 'What is Sampreshan?', 'sampreshan-child' ); ?>
                </a>
            </div>
            <ul class="d3-hero__points">
                <li><?php sp_icon_auto( 'dharma', 'sp-icon--sm', '' ); ?><?php esc_html_e( 'One platform for Sanatanis worldwide', 'sampreshan-child' ); ?></li>
                <li><?php sp_icon_auto( 'shankh', 'sp-icon--sm', '' ); ?><?php esc_html_e( 'Issue Sampreshan — raise your voice', 'sampreshan-child' ); ?></li>
                <li><span class="sp-ibadge sp-ibadge--sm" aria-hidden="true">I</span><?php esc_html_e( 'Every support counts', 'sampreshan-child' ); ?></li>
            </ul>
        </div>

        <div class="d3-hero__art" aria-hidden="true">
            <?php if ( function_exists( 'sp_art_url' ) && sp_art_url( 'voice-rings.svg' ) ) : ?>
                <img class="d3-hero__rings" src="<?php echo esc_url( sp_art_url( 'voice-rings.svg' ) ); ?>" alt="" width="600" height="600" loading="eager" decoding="async" />
            <?php endif; ?>
            <?php if ( function_exists( 'sp_art_url' ) && sp_art_url( 'mandala.svg' ) ) : ?>
                <img class="d3-hero__mandala" src="<?php echo esc_url( sp_art_url( 'mandala.svg' ) ); ?>" alt="" width="400" height="400" loading="lazy" decoding="async" />
            <?php endif; ?>
            <div class="d3-hero__object">
                <div class="d3-hero__card">
                    <img class="d3-hero__logo" src="<?php echo esc_url( $logo_url ); ?>" alt="" width="120" height="120" fetchpriority="high" />
                    <p class="d3-hero__logo-name"><?php echo esc_html( $site_name ); ?></p>
                    <p class="d3-hero__logo-tag"><?php esc_html_e( 'Voice · Dialogue · Support', 'sampreshan-child' ); ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="d3-hero__scroll" aria-hidden="true">
        <span><?php esc_html_e( 'Scroll', 'sampreshan-child' ); ?></span>
        <div class="d3-hero__scroll-line"></div>
    </div>
</section>

<?php
/**
 * What Sampreshan is — dark immersive section with 3D cards.
 */
?>
<section class="d3-section" aria-labelledby="sp-platform-h">
    <div class="d3-section__light" aria-hidden="true"></div>
    <div class="d3-section__inner">
        <div class="d3-reveal" style="text-align:center; max-width:640px; margin:0 auto clamp(2rem, 5vw, 4rem);">
            <p class="d3-section__eyebrow" style="justify-content:center;"><?php esc_html_e( 'This is Sampreshan', 'sampreshan-child' ); ?></p>
            <h2 class="d3-section__title" id="sp-platform-h"><?php esc_html_e( 'One worldwide social media for Sanatanis', 'sampreshan-child' ); ?></h2>
            <p class="d3-section__sub" style="margin-left:auto;margin-right:auto;"><?php esc_html_e( 'Hindus — and everyone across the world living by Sanatan Dharma — connect here. Every tool of Facebook and Twitter, among your own people. Plus one special tool: Issue Sampreshan — like Change.org, only for Dharma.', 'sampreshan-child' ); ?></p>
        </div>

        <div class="d3-grid-3 d3-stagger">
            <div class="d3-card">
                <div class="d3-card__icon"><?php sp_icon_auto( 'network', 'sp-icon--md', '' ); ?></div>
                <h3 class="d3-card__title"><?php esc_html_e( 'Connect Like Family', 'sampreshan-child' ); ?></h3>
                <p class="d3-card__text"><?php esc_html_e( 'Make friends, post, share photos and videos, like, comment and discuss. The whole world\u2019s Sanatani family, on one platform.', 'sampreshan-child' ); ?></p>
            </div>
            <div class="d3-card">
                <div class="d3-card__icon"><?php sp_icon_auto( 'shankh', 'sp-icon--md', '' ); ?></div>
                <h3 class="d3-card__title"><?php esc_html_e( 'Issue Sampreshan', 'sampreshan-child' ); ?></h3>
                <p class="d3-card__text"><?php esc_html_e( 'Raise any Dharma-related problem directly on the platform. People sign up with email, read your issue and carry it forward.', 'sampreshan-child' ); ?></p>
            </div>
            <div class="d3-card">
                <div class="d3-card__icon"><span class="sp-ibadge" aria-hidden="true">I</span></div>
                <h3 class="d3-card__title"><?php esc_html_e( 'I — The Badge of Support', 'sampreshan-child' ); ?></h3>
                <p class="d3-card__text"><?php esc_html_e( 'Every support counts — just like a Like. The more people share and support, the more supporters you gather. More supporters, louder voice.', 'sampreshan-child' ); ?></p>
            </div>
        </div>

        <div class="d3-reveal" style="text-align:center; margin-top:clamp(2rem, 4vw, 3rem);">
            <a class="d3-btn d3-btn--primary d3-btn--lg" href="<?php echo esc_url( $start_url ); ?>"><?php esc_html_e( 'Start an Issue Sampreshan', 'sampreshan-child' ); ?></a>
            <a class="d3-btn d3-btn--ghost d3-btn--lg" href="<?php echo esc_url( wp_registration_url() ); ?>" style="margin-left:0.75rem;"><?php esc_html_e( 'Join with Email', 'sampreshan-child' ); ?></a>
        </div>
    </div>
</section>
