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
$feed_url   = function_exists( 'sp_feed_url' ) ? sp_feed_url() : home_url( '/feed-2/' );
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
                <?php esc_html_e( 'Sampreshan is a community platform where you can raise local issues, start petitions, gather Is, and connect with people who share Dharmic values — a worldwide Sanatani social media with every Facebook-Twitter tool, plus Issue Sampreshan for Dharma.', 'sampreshan-child' ); ?>
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
    <?php /* Messenger pigeons — decorative flight layer (CSS-only motion). */ ?>
    <div class="sp-pigeon-flight" aria-hidden="true">
        <div class="sp-pigeon sp-pigeon--near"><div class="sp-pigeon__bob"><?php if ( function_exists( 'sp_pigeon' ) ) { sp_pigeon( 'near', true ); } ?></div></div>
        <div class="sp-pigeon sp-pigeon--mid"><div class="sp-pigeon__bob"><?php if ( function_exists( 'sp_pigeon' ) ) { sp_pigeon( 'mid', false ); } ?></div></div>
        <div class="sp-pigeon sp-pigeon--far"><div class="sp-pigeon__bob"><?php if ( function_exists( 'sp_pigeon' ) ) { sp_pigeon( 'far', false ); } ?></div></div>
    </div>
</section>

<?php
/**
 * What Sampreshan is: worldwide Sanatani social media + Issue Sampreshan.
 * One topic = one symbol: network (connect), shankh (raise voice),
 * I-badge (support, like a Like).
 */
?>
<section class="sp-section sp-section--center" aria-labelledby="sp-platform-h">
    <div class="sp-section__head">
        <p class="sp-section__eyebrow"><?php esc_html_e( 'This is Sampreshan', 'sampreshan-child' ); ?></p>
        <h2 class="sp-section__title" id="sp-platform-h"><?php sp_icon_e( 'dharma', 'sp-icon--md sp-icon--saffron', '' ); ?> <?php esc_html_e( 'One worldwide social media for Sanatanis', 'sampreshan-child' ); ?></h2>
        <p class="sp-section__sub"><?php esc_html_e( 'Hindus — and everyone across the world living by Sanatan Dharma — connect here. Every tool of Facebook and Twitter, among your own people. Plus one special tool: Issue Sampreshan — like Change.org, only for Dharma.', 'sampreshan-child' ); ?></p>
    </div>
    <div class="sp-steps">
        <div class="sp-step-card">
            <span class="sp-step-card__num" aria-hidden="true">1</span>
            <span class="sp-step-card__icon" aria-hidden="true"><?php sp_icon_e( 'network', 'sp-icon--md', '' ); ?></span>
            <h3><?php esc_html_e( 'Connect Like Family', 'sampreshan-child' ); ?></h3>
            <p><?php esc_html_e( 'Make friends, post, share photos and videos, like, comment and discuss. The whole world\u2019s Sanatani family, on one platform.', 'sampreshan-child' ); ?></p>
        </div>
        <div class="sp-step-card">
            <span class="sp-step-card__num" aria-hidden="true">2</span>
            <span class="sp-step-card__icon" aria-hidden="true"><?php sp_icon_e( 'shankh', 'sp-icon--md', '' ); ?></span>
            <h3><?php esc_html_e( 'Issue Sampreshan', 'sampreshan-child' ); ?></h3>
            <p><?php esc_html_e( 'Raise any Dharma-related problem directly on the platform. People sign up with email, read your issue and carry it forward.', 'sampreshan-child' ); ?></p>
        </div>
        <div class="sp-step-card">
            <span class="sp-step-card__num" aria-hidden="true">3</span>
            <span class="sp-step-card__icon" aria-hidden="true"><span class="sp-ibadge" aria-hidden="true">I</span></span>
            <h3><?php esc_html_e( 'I — The Badge of Support', 'sampreshan-child' ); ?></h3>
            <p><?php esc_html_e( 'Every support is an I badge — just like a Like. The more people share and support, the more Is you collect. More Is, louder voice.', 'sampreshan-child' ); ?></p>
        </div>
    </div>
    <div class="sp-section__actions">
        <a class="btn btn--primary btn--lg" href="<?php echo esc_url( $start_url ); ?>"><?php esc_html_e( 'Start an Issue Sampreshan', 'sampreshan-child' ); ?></a>
        <a class="btn btn--ghost btn--lg" href="<?php echo esc_url( wp_registration_url() ); ?>"><?php esc_html_e( 'Join with Email', 'sampreshan-child' ); ?></a>
    </div>
</section>

<?php
/**
 * IconScout search widget — only visible to logged-in users.
 */
if ( is_user_logged_in() ) : ?>
    <section class="sp-section" aria-labelledby="sp-iconscout-h">
        <div class="sp-section__head">
            <p class="sp-section__eyebrow"><?php esc_html_e( 'Icon Library', 'sampreshan-child' ); ?></p>
            <h2 class="sp-section__title" id="sp-iconscout-h"><?php sp_icon_auto( 'search', 'sp-icon--md sp-icon--saffron', '' ); ?> <?php esc_html_e( 'Search Icons, Illustrations & Lottie', 'sampreshan-child' ); ?></h2>
            <p class="sp-section__sub"><?php esc_html_e( 'Browse the IconScout collection directly in your dashboard.', 'sampreshan-child' ); ?></p>
        </div>
        <div class="sp-icon-scout">
            <div class="sp-icon-scout__search">
                <input type="search" id="sp-icon-scout-input" class="sp-icon-scout__input" placeholder="<?php esc_attr_e( 'Search icons, illustrations, lottie…', 'sampreshan-child' ); ?>" aria-label="<?php esc_attr_e( 'Search icons', 'sampreshan-child' ); ?>" />
                <select id="sp-icon-scout-asset" class="sp-icon-scout__select" aria-label="<?php esc_attr_e( 'Asset type', 'sampreshan-child' ); ?>">
                    <option value="icon"><?php esc_html_e( 'Icons', 'sampreshan-child' ); ?></option>
                    <option value="illustration"><?php esc_html_e( 'Illustrations', 'sampreshan-child' ); ?></option>
                    <option value="lottie"><?php esc_html_e( 'Lottie', 'sampreshan-child' ); ?></option>
                </select>
                <button type="button" id="sp-icon-scout-btn" class="btn btn--primary btn--sm"><?php esc_html_e( 'Search', 'sampreshan-child' ); ?></button>
            </div>
            <div id="sp-icon-scout-results" class="sp-icon-scout__results" aria-live="polite"></div>
            <div id="sp-icon-scout-loading" class="sp-icon-scout__loading" hidden>
                <span class="sp-spinner"></span> <?php esc_html_e( 'Searching IconScout…', 'sampreshan-child' ); ?>
            </div>
            <p id="sp-icon-scout-error" class="sp-icon-scout__error" hidden></p>
        </div>
    </section>
<?php endif; ?>
