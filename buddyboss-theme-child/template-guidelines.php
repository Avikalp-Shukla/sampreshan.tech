<?php
/**
 * Template Name: Community Guidelines
 *
 * Community rules and behavioral expectations for SampreShan.
 *
 * @package SampreShan_Child
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

get_header();
?>

<main class="site-main sp-page" role="main">

    <!-- HERO -->
    <header class="sp-page__hero">
        <div class="sp-page__hero-icon">
            <?php sp_icon_e( 'verified', 'sp-icon--3xl sp-icon--saffron sp-icon--float', __( 'Guidelines', 'sampreshan-child' ) ); ?>
        </div>
        <h1 class="sp-page__hero-title"><?php esc_html_e( 'Community Guidelines', 'sampreshan-child' ); ?></h1>
        <p class="sp-page__hero-sub">
            <?php esc_html_e( 'These guidelines help us maintain a respectful, meaningful, and productive space for everyone in the Sanatana Dharma community.', 'sampreshan-child' ); ?>
        </p>
    </header>

    <div class="sp-page__body">

        <!-- GUIDELINES -->
        <section class="sp-page__card">
            <h2 class="sp-page__section-title">
                <?php sp_icon_e( 'heart', 'sp-icon--md sp-icon--saffron', __( 'Respect', 'sampreshan-child' ) ); ?>
                <?php esc_html_e( 'Core Principles', 'sampreshan-child' ); ?>
            </h2>
            <ol class="sp-rules-list">
                <li class="sp-rules-list__item">
                    <span class="sp-rules-list__item-num">1</span>
                    <div class="sp-rules-list__item-text">
                        <div class="sp-rules-list__item-title"><?php esc_html_e( 'Respect All Traditions', 'sampreshan-child' ); ?></div>
                        <p class="sp-rules-list__item-desc"><?php esc_html_e( 'Sanatana Dharma encompasses many sampradayas, traditions, and paths. Treat every tradition with equal respect. Do not denigrate or mock any practice, deity, or spiritual path.', 'sampreshan-child' ); ?></p>
                    </div>
                </li>
                <li class="sp-rules-list__item">
                    <span class="sp-rules-list__item-num">2</span>
                    <div class="sp-rules-list__item-text">
                        <div class="sp-rules-list__item-title"><?php esc_html_e( 'Speak with Purpose', 'sampreshan-child' ); ?></div>
                        <p class="sp-rules-list__item-desc"><?php esc_html_e( 'Every post, petition, and comment should aim to uplift, inform, or bring positive change. Avoid gossip, personal attacks, or content that does not serve the community.', 'sampreshan-child' ); ?></p>
                    </div>
                </li>
                <li class="sp-rules-list__item">
                    <span class="sp-rules-list__item-num">3</span>
                    <div class="sp-rules-list__item-text">
                        <div class="sp-rules-list__item-title"><?php esc_html_e( 'Stay Focused on Dharma', 'sampreshan-child' ); ?></div>
                        <p class="sp-rules-list__item-desc"><?php esc_html_e( 'This platform is for social, cultural, and religious issues related to Sanatana Dharma. Keep discussions relevant to the mission of community awareness and collective action.', 'sampreshan-child' ); ?></p>
                    </div>
                </li>
                <li class="sp-rules-list__item">
                    <span class="sp-rules-list__item-num">4</span>
                    <div class="sp-rules-list__item-text">
                        <div class="sp-rules-list__item-title"><?php esc_html_e( 'No Commercial Activity', 'sampreshan-child' ); ?></div>
                        <p class="sp-rules-list__item-desc"><?php esc_html_e( 'SampreShan is a non-commercial platform. Do not use it for selling products, promoting businesses, soliciting donations, or any financial transactions.', 'sampreshan-child' ); ?></p>
                    </div>
                </li>
                <li class="sp-rules-list__item">
                    <span class="sp-rules-list__item-num">5</span>
                    <div class="sp-rules-list__item-text">
                        <div class="sp-rules-list__item-title"><?php esc_html_e( 'Protect Privacy', 'sampreshan-child' ); ?></div>
                        <p class="sp-rules-list__item-desc"><?php esc_html_e( 'Do not share personal information of others without their consent. Respect the privacy and dignity of every community member.', 'sampreshan-child' ); ?></p>
                    </div>
                </li>
                <li class="sp-rules-list__item">
                    <span class="sp-rules-list__item-num">6</span>
                    <div class="sp-rules-list__item-text">
                        <div class="sp-rules-list__item-title"><?php esc_html_e( 'No Hate Speech or Violence', 'sampreshan-child' ); ?></div>
                        <p class="sp-rules-list__item-desc"><?php esc_html_e( 'Content promoting hatred, violence, discrimination, or harm against any individual or group is strictly prohibited and will result in immediate removal.', 'sampreshan-child' ); ?></p>
                    </div>
                </li>
                <li class="sp-rules-list__item">
                    <span class="sp-rules-list__item-num">7</span>
                    <div class="sp-rules-list__item-text">
                        <div class="sp-rules-list__item-title"><?php esc_html_e( 'Petition Integrity', 'sampreshan-child' ); ?></div>
                        <p class="sp-rules-list__item-desc"><?php esc_html_e( 'Petitions must be truthful, well-intentioned, and aligned with community welfare. Misleading or manipulative petitions will be removed.', 'sampreshan-child' ); ?></p>
                    </div>
                </li>
                <li class="sp-rules-list__item">
                    <span class="sp-rules-list__item-num">8</span>
                    <div class="sp-rules-list__item-text">
                        <div class="sp-rules-list__item-title"><?php esc_html_e( 'Build, Don\'t Destroy', 'sampreshan-child' ); ?></div>
                        <p class="sp-rules-list__item-desc"><?php esc_html_e( 'Our goal is to strengthen the community through constructive dialogue. Even when disagreeing, do so with civility and a spirit of learning.', 'sampreshan-child' ); ?></p>
                    </div>
                </li>
            </ol>
        </section>

        <!-- CONSEQUENCES -->
        <section class="sp-page__card">
            <h2 class="sp-page__section-title">
                <?php sp_icon_e( 'warning', 'sp-icon--md sp-icon--warning', __( 'Enforcement', 'sampreshan-child' ) ); ?>
                <?php esc_html_e( 'Enforcement', 'sampreshan-child' ); ?>
            </h2>
            <div class="sp-page__content">
                <p><?php esc_html_e( 'Violations of these guidelines may result in:', 'sampreshan-child' ); ?></p>
                <ul>
                    <li><?php esc_html_e( 'Content removal or editing', 'sampreshan-child' ); ?></li>
                    <li><?php esc_html_e( 'Warning notifications', 'sampreshan-child' ); ?></li>
                    <li><?php esc_html_e( 'Temporary account suspension', 'sampreshan-child' ); ?></li>
                    <li><?php esc_html_e( 'Permanent account termination in severe cases', 'sampreshan-child' ); ?></li>
                </ul>
                <p><?php esc_html_e( 'The ShivBodh Trust team reserves the right to take appropriate action to maintain the integrity and safety of the platform.', 'sampreshan-child' ); ?></p>
            </div>
        </section>

        <!-- REPORT -->
        <section class="sp-page__card">
            <h2 class="sp-page__section-title">
                <?php sp_icon_e( 'mail', 'sp-icon--md sp-icon--saffron', __( 'Report', 'sampreshan-child' ) ); ?>
                <?php esc_html_e( 'Report a Violation', 'sampreshan-child' ); ?>
            </h2>
            <div class="sp-page__content">
                <p><?php esc_html_e( 'If you encounter content that violates these guidelines, please report it immediately.', 'sampreshan-child' ); ?></p>
                <p>
                    <?php esc_html_e( 'Email us at', 'sampreshan-child' ); ?>
                    <a href="mailto:info@sampreshan.tech">info@sampreshan.tech</a>
                    <?php esc_html_e( 'with the details and we will take appropriate action.', 'sampreshan-child' ); ?>
                </p>
            </div>
        </section>

        <!-- CTA -->
        <section class="sp-page__cta">
            <h2 class="sp-page__cta-title"><?php esc_html_e( 'Together We Are Stronger', 'sampreshan-child' ); ?></h2>
            <p class="sp-page__cta-sub"><?php esc_html_e( 'By following these guidelines, you help create a space where every voice is heard and every tradition is honored.', 'sampreshan-child' ); ?></p>
            <div class="sp-page__cta-actions">
                <a class="btn-3d btn-3d--lg" href="<?php echo esc_url( home_url( '/' ) ); ?>">
                    <?php sp_icon_e( 'home', 'sp-icon--sm sp-icon--white', __( 'Home', 'sampreshan-child' ) ); ?>
                    <?php esc_html_e( 'Back to Home', 'sampreshan-child' ); ?>
                </a>
            </div>
        </section>

    </div>
</main>

<?php
$footer_tpl = get_stylesheet_directory() . '/template-parts/footer/site-footer.php';
if ( file_exists( $footer_tpl ) ) {
    include $footer_tpl;
} else {
    get_footer();
}
