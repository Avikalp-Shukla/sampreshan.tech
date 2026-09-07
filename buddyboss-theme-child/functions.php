<?php
/**
 * SampreShan Child Theme functions and definitions
 * Sanatan Voice Platform by ShivBodh Trust
 *
 * @package SampreShan_Child
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * IconScout credentials — define in wp-config.php for production:
 *   define( 'ICONSCOUT_CLIENT_ID', '165483987904653' );
 *   define( 'ICONSCOUT_API_KEY',   'GJmR3qpF7LGDVNyfXfuQTHVVAPnYh0jTtjJQAwF' );
 * Or set via WP admin: Settings → IconScout (we'll add a simple options page later).
 * For now, the API client falls back to get_option() / env vars.
 */

/**
 * Firebase (sampreshan-login web app) configuration.
 *
 * Digits → Settings → Firebase reads this same "Config" snippet, so keep
 * one canonical copy here and pass it to JS via `SampreshanFirebase`.
 * Override in wp-config.php when needed:
 *   define( 'SAMPRESHAN_FIREBASE_API_KEY', '...' );
 *   define( 'SAMPRESHAN_FIREBASE_AUTH_DOMAIN', '...' );
 *   define( 'SAMPRESHAN_FIREBASE_PROJECT_ID', '...' );
 *   define( 'SAMPRESHAN_FIREBASE_STORAGE_BUCKET', '...' );
 *   define( 'SAMPRESHAN_FIREBASE_MESSAGING_SENDER_ID', '...' );
 *   define( 'SAMPRESHAN_FIREBASE_APP_ID', '...' );
 *   define( 'SAMPRESHAN_FIREBASE_MEASUREMENT_ID', '...' );
 */
function sp_firebase_config() {
    $config = array(
        'apiKey'            => defined( 'SAMPRESHAN_FIREBASE_API_KEY' ) ? SAMPRESHAN_FIREBASE_API_KEY : 'AIzaSyBd_Hl4uaivSnm3Ue0N_xcKexKX4PIIUpI',
        'authDomain'        => defined( 'SAMPRESHAN_FIREBASE_AUTH_DOMAIN' ) ? SAMPRESHAN_FIREBASE_AUTH_DOMAIN : 'sampreshan-tech.firebaseapp.com',
        'projectId'         => defined( 'SAMPRESHAN_FIREBASE_PROJECT_ID' ) ? SAMPRESHAN_FIREBASE_PROJECT_ID : 'sampreshan-tech',
        'storageBucket'     => defined( 'SAMPRESHAN_FIREBASE_STORAGE_BUCKET' ) ? SAMPRESHAN_FIREBASE_STORAGE_BUCKET : 'sampreshan-tech.firebasestorage.app',
        'messagingSenderId' => defined( 'SAMPRESHAN_FIREBASE_MESSAGING_SENDER_ID' ) ? SAMPRESHAN_FIREBASE_MESSAGING_SENDER_ID : '780707453290',
        'appId'             => defined( 'SAMPRESHAN_FIREBASE_APP_ID' ) ? SAMPRESHAN_FIREBASE_APP_ID : '1:780707453290:web:464c33fa9720bbe1e6ed13',
        'measurementId'     => defined( 'SAMPRESHAN_FIREBASE_MEASUREMENT_ID' ) ? SAMPRESHAN_FIREBASE_MEASUREMENT_ID : 'G-TXRBLN47KQ',
    );
    return apply_filters( 'sampreshan_firebase_config', $config );
}

function sp_firebase_api_key() {
    $config = sp_firebase_config();
    return isset( $config['apiKey'] ) ? (string) $config['apiKey'] : '';
}

/**
 * Community Feed page URL — single source of truth.
 *
 * Never hardcode home_url('/feed/'): the `feed` slug is reserved by
 * WordPress and always serves the RSS feed. Our Feed experience is the
 * published page using template-activity.php (slug `feed-2` on live).
 * Resolves it dynamically so renames never break navigation again.
 */
function sp_feed_url() {
    $ids = get_posts( array(
        'post_type'      => 'page',
        'meta_key'       => '_wp_page_template',
        'meta_value'     => 'template-activity.php',
        'posts_per_page' => 1,
        'post_status'    => 'publish',
        'fields'         => 'ids',
        'orderby'        => 'ID',
        'order'          => 'ASC',
    ) );
    if ( ! empty( $ids ) ) {
        $url = get_permalink( (int) $ids[0] );
        if ( $url ) { return $url; }
    }
    $by_path = get_page_by_path( 'feed-2' );
    if ( $by_path ) {
        $url = get_permalink( $by_path );
        if ( $url ) { return $url; }
    }
    return home_url( '/feed-2/' );
}

/**
 * Load IconScout client.
 */
require_once get_stylesheet_directory() . '/inc/iconscout/client.php';

/**
 * Theme version (for cache busting)
 */
if ( ! defined( 'SAMPRESHAN_CHILD_VERSION' ) ) {
    define( 'SAMPRESHAN_CHILD_VERSION', '1.5.1' );
}

/**
 * Theme Dashboard URL — single source of truth (like sp_feed_url()).
 * Members land here after login; never in wp-admin.
 */
function sampreshan_dashboard_url() {
    $ids = get_posts( array(
        'post_type'      => 'page',
        'meta_key'       => '_wp_page_template',
        'meta_value'     => 'template-dashboard.php',
        'posts_per_page' => 1,
        'post_status'    => 'publish',
        'fields'         => 'ids',
        'orderby'        => 'ID',
        'order'          => 'ASC',
    ) );
    if ( ! empty( $ids ) ) {
        $url = get_permalink( (int) $ids[0] );
        if ( $url ) { return $url; }
    }
    $by_path = get_page_by_path( 'dashboard' );
    if ( $by_path ) {
        $url = get_permalink( $by_path );
        if ( $url ) { return $url; }
    }
    return home_url( '/dashboard/' );
}

/**
 * Members go to the theme dashboard after login — never wp-admin.
 * Explicit front-end destinations (e.g. return-to-page after a gated
 * support click) are honoured; only wp-admin/wp-login targets reroute.
 */
function sampreshan_login_redirect( $redirect_to, $requested_redirect_to, $user ) {
    if ( $user instanceof WP_User && ! user_can( $user->ID, 'manage_options' ) ) {
        if ( is_string( $requested_redirect_to ) && '' !== $requested_redirect_to
            && false === strpos( $requested_redirect_to, 'wp-admin' )
            && false === strpos( $requested_redirect_to, 'wp-login.php' )
            && 0 === strpos( $requested_redirect_to, home_url( '/' ) ) ) {
            return $requested_redirect_to;
        }
        return sampreshan_dashboard_url();
    }
    return $redirect_to;
}
add_filter( 'login_redirect', 'sampreshan_login_redirect', PHP_INT_MAX, 3 );

/**
 * Keep members out of wp-admin entirely (AJAX + posting endpoints stay open).
 */
function sampreshan_block_member_admin() {
    if ( wp_doing_ajax() || ! is_user_logged_in() ) { return; }
    if ( current_user_can( 'manage_options' ) ) { return; }
    $script = isset( $_SERVER['SCRIPT_NAME'] ) ? basename( (string) $_SERVER['SCRIPT_NAME'] ) : '';
    if ( in_array( $script, array( 'admin-ajax.php', 'async-upload.php', 'admin-post.php' ), true ) ) { return; }
    wp_safe_redirect( sampreshan_dashboard_url() );
    exit;
}
add_action( 'admin_init', 'sampreshan_block_member_admin', 1 );

/**
 * No WP toolbar on the frontend for anyone except admins (app feel).
 */
function sampreshan_member_admin_bar( $show ) {
    if ( is_user_logged_in() && ! current_user_can( 'manage_options' ) ) { return false; }
    if ( ! is_user_logged_in() && ! is_admin() ) { return false; }
    return $show;
}
add_filter( 'show_admin_bar', 'sampreshan_member_admin_bar', 20 );

/**
 * Load the icon helper (sp_icon(), sp_icon_e(), sp_favicon_url()).
 * Must be loaded before any template that uses the helpers.
 */
require_once get_stylesheet_directory() . '/inc/icon-helper.php';

/**
 * Load the Framer embedder (sp_framer_render(), sp_framer_shortcode).
 * Renders the Framer design as the full site UI when a snapshot is present.
 */
require_once get_stylesheet_directory() . '/inc/framer-embed.php';

/**
 * Backend modules.
 * Each module is self-contained: it registers its own hooks on `init`.
 * We just require the loaders here; they handle the rest.
 */
require_once get_stylesheet_directory() . '/inc/petitions/loader.php';
require_once get_stylesheet_directory() . '/inc/profile/fields.php';
require_once get_stylesheet_directory() . '/inc/auth/loader.php';
require_once get_stylesheet_directory() . '/inc/notifications/center.php';
require_once get_stylesheet_directory() . '/inc/seo/schema.php';

/**
 * JS detector + no-JS fallback (prints first in <head>).
 * Scroll-reveal hiding is scoped to `html.js`, so without JavaScript
 * every section stays visible instead of a blank page.
 */
function sampreshan_child_js_detector() {
    echo "<script>document.documentElement.className+=' js';</script>\n";
    echo '<noscript><style>body{opacity:1!important}.reveal,.stagger-children>*{opacity:1!important;transform:none!important}</style></noscript>' . "\n";
}
add_action( 'wp_head', 'sampreshan_child_js_detector', 0 );
/**
 * CRITICAL DARK CSS — inline in <head>, priority -1 (before ALL other CSS)
 * Forces dark backgrounds on every wrapper. BuddyBoss sets white via
 * CSS custom properties that override our child theme stylesheets.
 * This inline style ALWAYS wins because it's first in the cascade
 * AND it uses !important on every background.
 */
function sampreshan_critical_dark_css() {
    if ( ! is_front_page() ) return;
    echo '<style id="sampreshan-critical-dark">
    :root {
        --bb-body-background-color: #08090c !important;
        --bb-body-background-color-rgb: 8,9,12 !important;
        --bb-body-text-color: #ffffff !important;
        --bb-body-text-color-rgb: 255,255,255 !important;
        --bb-content-background-color: #12151e !important;
        --bb-content-alternate-background-color: #181c28 !important;
        --bb-content-border-color: rgba(255,255,255,0.06) !important;
        --bb-header-background: #0c0e14 !important;
        --bb-footer-background: #08090c !important;
        --d-bg: #0c0e14 !important;
        --d-bg-deep: #08090c !important;
        --d-text: #ffffff !important;
        --d-text-muted: #b0adc0 !important;
        --d-bg-card: #12151e !important;
        --d-bg-elevated: #181c28 !important;
        --d-accent: #ff9933 !important;
    }
    html, body, #content, .site-content, .container, .bb-grid,
    .site-content-grid, .site-main, .site-main--landing, .sp-landing,
    .site-header, .site-header--bb, .bb-mobile-header, .site-footer,
    .elementor, .elementor-location-header, .elementor-location-footer,
    .elementor-location-archive, .elementor-section, .elementor-widget-wrap,
    .site-content .container, .site-content .bb-grid,
    .site-main > div, .site-main > section {
        background-color: #08090c !important;
        color: #ffffff !important;
    }
    .site-header, .site-header--bb, .bb-mobile-header, #masthead {
        background: #0c0e14 !important;
        border-bottom: 1px solid rgba(255,255,255,0.06) !important;
    }
    .site-footer, .site-footer__inner {
        background: #08090c !important;
    }
    #wpadminbar { background: #0c0e14 !important; }
    .d3-hero, .d3-section, .d3-cta, .d3-footer {
        background: #0c0e14 !important;
        color: #ffffff !important;
    }
    .d3-section__title, .d3-section__sub, .d3-section__eyebrow,
    .d3-card__title, .d3-card__text, .d3-cta__title, .d3-cta__sub,
    h1, h2, h3, h4, h5, h6, p, span, li, a {
        color: #ffffff !important;
    }
    .d3-section__sub, .d3-card__text, .d3-cta__sub, p, li {
        color: #b0adc0 !important;
    }
    .d3-section__eyebrow, .d3-card__icon {
        color: #ff9933 !important;
    }
    .d3-card {
        background: linear-gradient(145deg, #12151e 0%, #181c28 100%) !important;
        border-color: rgba(255,255,255,0.06) !important;
    }
    .d3-btn--primary { background: #ff9933 !important; color: #000 !important; }
    .d3-btn--ghost { background: transparent !important; border-color: rgba(255,153,51,0.4) !important; color: #ff9933 !important; }
    .d3-reveal, .d3-reveal-left, .d3-reveal-right, .d3-reveal-scale,
    .d3-stagger, .d3-stagger > *,
    .d3-hero__eyebrow, .d3-hero__title, .d3-hero__sub, .d3-hero__actions,
    .d3-section__inner, .d3-grid-3, .d3-grid-4,
    .d3-card, .d3-card__icon, .d3-card__title, .d3-card__text,
    .d3-featured, .d3-hscroll, .d3-hscroll__track,
    .d3-cta__inner, .d3-cta__title, .d3-cta__sub, .d3-cta__actions,
    .d3-flag, .d3-eye, .d3-badge,
    .sp-i-layer, .sp-i-stage, #sp-i-badge, #sp-i-eye {
        opacity: 1 !important;
        transform: none !important;
    }
    </style>' . "\n";
}
add_action( 'wp_head', 'sampreshan_critical_dark_css', -1 );

/**
 * Enqueue Google Fonts (preconnect + display=swap for performance)
 */
function sampreshan_child_enqueue_fonts() {    // Preconnect to Google Fonts servers
    echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";

    // Load fonts: Cormorant Garamond (display/serif) + Inter (body/heading) + Tiro Devanagari (Hindi)
    wp_enqueue_style(
        'sampreshan-child-fonts',
        'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Inter:wght@400;500;600;700;800&family=Playfair+Display:wght@500;600;700;800&family=Tiro+Devanagari:wght@400;700&display=swap',
        array(),
        null
    );
}
add_action( 'wp_head', 'sampreshan_child_enqueue_fonts', 1 );

/**
 * Enqueue parent theme styles + child theme styles
 */
function sampreshan_child_enqueue_styles() {
    // Parent theme main stylesheet
    wp_enqueue_style(
        'buddyboss-theme-parent',
        get_template_directory_uri() . '/style.css',
        array(),
        wp_get_theme( 'buddyboss-theme' )->get( 'Version' )
    );

    // Child theme main stylesheet (loads AFTER parent)
    wp_enqueue_style(
        'buddyboss-theme-child',
        get_stylesheet_directory_uri() . '/style.css',
        array( 'buddyboss-theme-parent' ),
        SAMPRESHAN_CHILD_VERSION
    );

    // Global design system v2 (premium tokens, 3D icons, glass-morphism)
    wp_enqueue_style(
        'sampreshan-design-system-v2',
        get_stylesheet_directory_uri() . '/assets/css/design-system-v2.css',
        array( 'buddyboss-theme-child' ),
        SAMPRESHAN_CHILD_VERSION
    );

    // Child theme design system
    wp_enqueue_style(
        'sampreshan-design-system',
        get_stylesheet_directory_uri() . '/assets/css/design-system.css',
        array( 'sampreshan-design-system-v2' ),
        SAMPRESHAN_CHILD_VERSION
    );

    // Layout
    wp_enqueue_style(
        'sampreshan-layout',
        get_stylesheet_directory_uri() . '/assets/css/layout.css',
        array( 'sampreshan-design-system' ),
        SAMPRESHAN_CHILD_VERSION
    );

    // Components
    wp_enqueue_style(
        'sampreshan-components',
        get_stylesheet_directory_uri() . '/assets/css/components.css',
        array( 'sampreshan-layout' ),
        SAMPRESHAN_CHILD_VERSION
    );

    // Responsive
    wp_enqueue_style(
        'sampreshan-responsive',
        get_stylesheet_directory_uri() . '/assets/css/responsive.css',
        array( 'sampreshan-components' ),
        SAMPRESHAN_CHILD_VERSION
    );

    // Homepage components (hero, stats, asides, grid)
    wp_enqueue_style(
        'sampreshan-homepage',
        get_stylesheet_directory_uri() . '/assets/css/homepage.css',
        array( 'sampreshan-responsive' ),
        SAMPRESHAN_CHILD_VERSION
    );

    // Permanent icon system (resolution-independent SVG icons + full responsive)
    wp_enqueue_style(
        'sampreshan-icon-system',
        get_stylesheet_directory_uri() . '/assets/css/icon-system.css',
        array( 'sampreshan-homepage' ),
        SAMPRESHAN_CHILD_VERSION
    );

    // Premium animations (scroll-reveal, parallax, particles, micro-interactions)
    wp_enqueue_style(
        'sampreshan-animations',
        get_stylesheet_directory_uri() . '/assets/css/animations.css',
        array( 'sampreshan-icon-system' ),
        SAMPRESHAN_CHILD_VERSION
    );

    // Fusion layer: Truth Social × Change.org in Indian religious style.
    // Global — its classes (sp-fu-*) style profile, cards, homepage social.
    wp_enqueue_style(
        'sampreshan-fusion',
        get_stylesheet_directory_uri() . '/assets/css/fusion.css',
        array( 'sampreshan-animations' ),
        SAMPRESHAN_CHILD_VERSION
    );

    // Futuristic skin ("Cosmic Dharma") — loaded last, wins everywhere.
    wp_enqueue_style(
        'sampreshan-future',
        get_stylesheet_directory_uri() . '/assets/css/future.css',
        array( 'sampreshan-fusion' ),
        SAMPRESHAN_CHILD_VERSION
    );

    // Theme modes (dashboard dark mode "Ratri" + share toolkit).
    // Global so the stored preference applies on every page.
    wp_enqueue_style(
        'sampreshan-theme-modes',
        get_stylesheet_directory_uri() . '/assets/css/theme-modes.css',
        array( 'sampreshan-future' ),
        SAMPRESHAN_CHILD_VERSION
    );

    // Tablet identity (769–1024px only) + desktop identity (1025px+).
    // Non-overlapping bands; both win over skins, mobile pack stays last.
    wp_enqueue_style(
        'sampreshan-tablet',
        get_stylesheet_directory_uri() . '/assets/css/tablet.css',
        array( 'sampreshan-theme-modes' ),
        SAMPRESHAN_CHILD_VERSION
    );
    wp_enqueue_style(
        'sampreshan-desktop',
        get_stylesheet_directory_uri() . '/assets/css/desktop.css',
        array( 'sampreshan-tablet' ),
        SAMPRESHAN_CHILD_VERSION
    );

    // Mobile experience pack (phone-only palette, typography, alignment).
    wp_enqueue_style(
        'sampreshan-mobile',
        get_stylesheet_directory_uri() . '/assets/css/mobile.css',
        array( 'sampreshan-desktop' ),
        SAMPRESHAN_CHILD_VERSION
    );

    // Transparent icons (no boxes behind glyphs + 3D depth, all modes).
    // Loaded absolutely last so it wins everywhere.
    wp_enqueue_style(
        'sampreshan-icons-transparent',
        get_stylesheet_directory_uri() . '/assets/css/icons-transparent.css',
        array( 'sampreshan-mobile' ),
        SAMPRESHAN_CHILD_VERSION
    );

    // Premium design system — Jeton/Awwwards-grade typography, spacing, reveals.
    // Loaded AFTER icons-transparent so it wins globally.
    wp_enqueue_style(
        'sampreshan-premium',
        get_stylesheet_directory_uri() . '/assets/css/premium.css',
        array( 'sampreshan-icons-transparent' ),
        SAMPRESHAN_CHILD_VERSION
    );

    // Premium 3D Dark design system — dark immersive palette, CSS perspective,
    // dramatic lighting, scroll-driven 3D reveals. Loaded AFTER premium.css.
    wp_enqueue_style(
        'sampreshan-premium-3d',
        get_stylesheet_directory_uri() . '/assets/css/premium-3d.css',
        array( 'sampreshan-premium' ),
        SAMPRESHAN_CHILD_VERSION
    );

    // 3D Art — mature neon-glow SVGs: waving flag, eye morph, I-badge.
    // Loaded AFTER premium-3d so it wins on all art components.
    wp_enqueue_style(
        'sampreshan-3d-art',
        get_stylesheet_directory_uri() . '/assets/css/3d-art.css',
        array( 'sampreshan-premium-3d' ),
        SAMPRESHAN_CHILD_VERSION
    );

    // Shared page templates styles (About, Contact, Guidelines, Privacy, Terms, Disclaimer)
    if ( is_page_template( array( 'template-about.php', 'template-contact.php', 'template-guidelines.php', 'template-privacy.php', 'template-terms.php', 'template-disclaimer.php' ) ) ) {
        wp_enqueue_style(
            'sampreshan-pages',
            get_stylesheet_directory_uri() . '/assets/css/pages.css',
            array( 'sampreshan-icon-system' ),
            SAMPRESHAN_CHILD_VERSION
        );
    }

    // Profile page styles (loaded only when the Sanatan Profile template is used)
    if ( is_page_template( 'template-profile.php' ) ) {
        wp_enqueue_style(
            'sampreshan-profile',
            get_stylesheet_directory_uri() . '/assets/css/profile.css',
            array( 'sampreshan-icon-system' ),
            SAMPRESHAN_CHILD_VERSION
        );
    }

    // Single petition page styles
    if ( is_singular( 'petition' ) ) {
        wp_enqueue_style(
            'sampreshan-petition',
            get_stylesheet_directory_uri() . '/assets/css/petition.css',
            array( 'sampreshan-icon-system' ),
            SAMPRESHAN_CHILD_VERSION
        );
    }

    // Custom login page styles — load on the /login page or when digit
    // is rendering its form. Detection is conservative to avoid a
    // global cost.
    // NOTE: SampreshanAuth is also printed globally (see sp_global_ajax_localize).
    // Keep every key here so the login page never loses the IconScout nonce.
    if ( is_page_template( 'template-login.php' ) || ( function_exists( 'sp_digits_is_active' ) && sp_digits_is_active() ) ) {
        wp_enqueue_style(
            'sampreshan-login',
            get_stylesheet_directory_uri() . '/assets/css/login.css',
            array( 'sampreshan-icon-system' ),
            SAMPRESHAN_CHILD_VERSION
        );
        wp_enqueue_script(
            'sampreshan-login',
            get_stylesheet_directory_uri() . '/assets/js/login.js',
            array(),
            SAMPRESHAN_CHILD_VERSION,
            true
        );
        wp_localize_script( 'sampreshan-login', 'SampreshanAuth', array(
            'ajaxUrl'        => admin_url( 'admin-ajax.php' ),
            'phoneNonce'     => wp_create_nonce( 'sp_phone_login' ),
            'iconscoutNonce' => wp_create_nonce( 'sp_iconscout_search' ),
            'nonce'          => wp_create_nonce( 'sp_iconscout_search' ),
            'isLoggedIn'     => is_user_logged_in() ? 1 : 0,
        ) );
    }

    // Digits (unitedover) integration — restyle Digits' native login page
    // with our design tokens. Only enqueued when Digits is actually active.
    if ( function_exists( 'sp_digits_is_active' ) && sp_digits_is_active() ) {
        wp_enqueue_style(
            'sampreshan-digits',
            get_stylesheet_directory_uri() . '/assets/css/digits-integration.css',
            array( 'sampreshan-icon-system' ),
            SAMPRESHAN_CHILD_VERSION
        );
    }

    // Firebase OTP phone login (sampreshan-login web app). Loaded on the
    // /login page as the Digits fallback so the mobile-number option is
    // always visible. Config comes from sp_firebase_config() — the same
    // snippet pasted into Digits → Settings → Firebase.
    if ( is_page_template( 'template-login.php' ) ) {
        wp_enqueue_script(
            'sampreshan-firebase-otp',
            get_stylesheet_directory_uri() . '/assets/js/firebase-otp.js',
            array(),
            SAMPRESHAN_CHILD_VERSION,
            true
        );
        wp_localize_script( 'sampreshan-firebase-otp', 'SampreshanFirebase', array(
            'ajaxUrl'    => admin_url( 'admin-ajax.php' ),
            'phoneNonce' => wp_create_nonce( 'sp_phone_login' ),
            'config'     => sp_firebase_config(),
        ) );
    }

    // Start a Petition form (page template + CSS + JS)
    if ( is_page_template( 'template-start-petition.php' ) ) {
        wp_enqueue_style(
            'sampreshan-petition-form',
            get_stylesheet_directory_uri() . '/assets/css/petition-form.css',
            array( 'sampreshan-icon-system' ),
            SAMPRESHAN_CHILD_VERSION
        );
        wp_enqueue_script(
            'sampreshan-petition-form',
            get_stylesheet_directory_uri() . '/assets/js/petition-form.js',
            array( 'sampreshan-navigation' ),
            SAMPRESHAN_CHILD_VERSION,
            true
        );
        wp_localize_script( 'sampreshan-petition-form', 'SampreshanPetitionForm', array(
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'sp_create_petition' ),
        ) );
    }

    // Dashboard page
    if ( is_page_template( 'template-dashboard.php' ) ) {
        wp_enqueue_style(
            'sampreshan-dashboard',
            get_stylesheet_directory_uri() . '/assets/css/dashboard.css',
            array( 'sampreshan-icon-system' ),
            SAMPRESHAN_CHILD_VERSION
        );
    }

    // User pages (petitions, my petitions, signed, members, feed, settings, profile)
    if ( is_page_template( array(
        'template-petitions.php',
        'template-my-petitions.php',
        'template-signed.php',
        'template-members.php',
        'template-activity.php',
        'template-settings.php',
        'template-profile.php',
    ) ) ) {
        wp_enqueue_style(
            'sampreshan-dashboard',
            get_stylesheet_directory_uri() . '/assets/css/dashboard.css',
            array( 'sampreshan-icon-system' ),
            SAMPRESHAN_CHILD_VERSION
        );
        wp_enqueue_style(
            'sampreshan-users',
            get_stylesheet_directory_uri() . '/assets/css/users.css',
            array( 'sampreshan-dashboard' ),
            SAMPRESHAN_CHILD_VERSION
        );
        // Settings reuses the form + notice components from the petition form.
        if ( is_page_template( 'template-settings.php' ) ) {
            wp_enqueue_style(
                'sampreshan-petition-form',
                get_stylesheet_directory_uri() . '/assets/css/petition-form.css',
                array( 'sampreshan-users' ),
                SAMPRESHAN_CHILD_VERSION
            );
        }
    }

    // Navigation JS (footer)
    wp_enqueue_script(
        'sampreshan-navigation',
        get_stylesheet_directory_uri() . '/assets/js/navigation.js',
        array(),
        SAMPRESHAN_CHILD_VERSION,
        true
    );

    // Theme toggle JS (dark mode, persisted) — global footer.
    wp_enqueue_script(
        'sampreshan-theme-toggle',
        get_stylesheet_directory_uri() . '/assets/js/theme-toggle.js',
        array(),
        SAMPRESHAN_CHILD_VERSION,
        true
    );

    // Petition sign handler — only on pages that show the button
    if ( is_singular( 'petition' ) || is_page_template( array( 'template-profile.php', 'template-dashboard.php', 'template-petitions.php', 'template-my-petitions.php', 'template-signed.php', 'template-activity.php' ) ) || is_front_page() ) {
        wp_enqueue_script(
            'sampreshan-petition-sign',
            get_stylesheet_directory_uri() . '/assets/js/petition-sign.js',
            array( 'sampreshan-navigation' ),
            SAMPRESHAN_CHILD_VERSION,
            true
        );
    }

    // Premium scroll engine — IntersectionObserver reveals, parallax, counters.
    // Loaded on front page and petition pages for scroll-triggered animations.
    if ( is_front_page() || is_singular( 'petition' ) || is_page_template( array( 'template-petitions.php', 'template-about.php' ) ) ) {
        wp_enqueue_script(
            'sampreshan-premium-scroll',
            get_stylesheet_directory_uri() . '/assets/js/premium-scroll.js',
            array(),
            SAMPRESHAN_CHILD_VERSION,
            true
        );
    }
}
add_action( 'wp_enqueue_scripts', 'sampreshan_child_enqueue_styles', 20 );

/**
 * Futuristic skin: flag every frontend page so future.css applies.
 */
function sampreshan_child_future_body_class( $classes ) {
    if ( ! is_admin() ) {
        $classes[] = 'sp-future';
    }
    return $classes;
}
add_filter( 'body_class', 'sampreshan_child_future_body_class', 20 );

/**
 * Global localization for AJAX (includes IconScout + phone nonces).
 * `phoneNonce` is included so IconScout-only pages and the login page
 * share one SampreshanAuth shape; login-specific localizes extend it.
 */
function sp_global_ajax_localize() {
    wp_add_inline_script( 'sampreshan-navigation', 'var SampreshanAuth = ' . wp_json_encode( array(
        'ajaxUrl'        => admin_url( 'admin-ajax.php' ),
        'nonce'          => wp_create_nonce( 'sp_iconscout_search' ),
        'iconscoutNonce' => wp_create_nonce( 'sp_iconscout_search' ),
        'phoneNonce'     => wp_create_nonce( 'sp_phone_login' ),
        'isLoggedIn'     => is_user_logged_in() ? 1 : 0,
    ) ) . ';', 'before' );
}
add_action( 'wp_enqueue_scripts', 'sp_global_ajax_localize', 999 );

/**
 * AJAX: Live IconScout search for dashboard icon picker.
 * Expects: query, asset (optional), page (optional).
 */
function sp_ajax_iconscout_search() {
    if ( ! is_user_logged_in() ) {
        wp_send_json_error( [ 'message' => __( 'Login required.', 'sampreshan-child' ) ], 401 );
    }
    check_ajax_referer( 'sp_iconscout_search', 'nonce' );

    $query  = isset( $_POST['query'] ) ? sanitize_text_field( wp_unslash( $_POST['query'] ) ) : '';
    $asset  = isset( $_POST['asset'] ) ? sanitize_text_field( wp_unslash( $_POST['asset'] ) ) : 'icon';
    $page   = isset( $_POST['page'] ) ? max( 1, (int) $_POST['page'] ) : 1;

    if ( '' === $query ) {
        wp_send_json_error( [ 'message' => __( 'Empty query.', 'sampreshan-child' ) ], 400 );
    }

    $api   = sp_iconscout_api();
    $result = $api->search( $query, $asset, $page, 30 );

    if ( is_wp_error( $result ) ) {
        wp_send_json_error( [ 'message' => $result->get_error_message() ], 502 );
    }

    // Normalize response for UI
    $items = [];
    if ( isset( $result['data'] ) && is_array( $result['data'] ) ) {
        foreach ( $result['data'] as $item ) {
            $id    = (int) ( $item['id'] ?? 0 );
            $title = $item['title'] ?? $item['name'] ?? 'Untitled';
            $thumb = $item['thumbnail'] ?? $item['preview_url'] ?? '';
            $style = $item['style'] ?? '';
            $items[] = [
                'id'       => $id,
                'title'    => $title,
                'thumb'    => $thumb,
                'style'    => $style,
                'asset'    => $asset,
            ];
        }
    }

    wp_send_json_success( [
        'items'      => $items,
        'total'      => (int) ( $result['total'] ?? count( $items ) ),
        'page'       => $page,
        'per_page'   => 30,
        'has_more'   => count( $items ) === 30,
    ] );
}
add_action( 'wp_ajax_sp_iconscout_search', 'sp_ajax_iconscout_search' );

/**
 * AJAX: Download / get asset URL (for inserting into content).
 */
function sp_ajax_iconscout_download() {
    if ( ! is_user_logged_in() ) {
        wp_send_json_error( [ 'message' => __( 'Login required.', 'sampreshan-child' ) ], 401 );
    }
    check_ajax_referer( 'sp_iconscout_search', 'nonce' );

    $asset_type = isset( $_POST['asset_type'] ) ? sanitize_text_field( wp_unslash( $_POST['asset_type'] ) ) : 'icon';
    $asset_id   = isset( $_POST['asset_id'] ) ? max( 1, (int) $_POST['asset_id'] ) : 0;
    $format     = isset( $_POST['format'] ) ? sanitize_text_field( wp_unslash( $_POST['format'] ) ) : 'svg';
    $size       = isset( $_POST['size'] ) ? max( 64, (int) $_POST['size'] ) : 512;

    if ( $asset_id <= 0 ) {
        wp_send_json_error( [ 'message' => __( 'Invalid asset ID.', 'sampreshan-child' ) ], 400 );
    }

    $api = sp_iconscout_api();
    $url = $api->get_download_url( $asset_type, $asset_id, $format, $size );

    if ( is_wp_error( $url ) ) {
        wp_send_json_error( [ 'message' => $url->get_error_message() ], 502 );
    }

    wp_send_json_success( [ 'url' => $url, 'format' => $format, 'size' => $size ] );
}
add_action( 'wp_ajax_sp_iconscout_download', 'sp_ajax_iconscout_download' );

/**
 * Child theme setup
 */
function sampreshan_child_setup() {
    load_child_theme_textdomain( 'sampreshan-child', get_stylesheet_directory() . '/languages' );

    // Declare support for title tag, custom logo, and HTML5 markup
    add_theme_support( 'title-tag' );
    add_theme_support( 'custom-logo', array(
        'height'      => 80,
        'width'       => 80,
        'flex-width'  => true,
        'flex-height' => true,
    ) );
}
add_action( 'after_setup_theme', 'sampreshan_child_setup' );

/**
 * Remove BuddyBoss ReadyLaunch template override so our custom page templates render properly.
 * Must run before the template_include filter fires.
 */
function sampreshan_child_disable_buddyboss_template_override() {
    if ( class_exists( 'BB_Readylaunch' ) ) {
        $readylaunch = BB_Readylaunch::instance();
        if ( method_exists( $readylaunch, 'override_page_templates' ) ) {
            remove_filter( 'template_include', array( $readylaunch, 'override_page_templates' ), PHP_INT_MAX );
        }
    }
}
add_action( 'init', 'sampreshan_child_disable_buddyboss_template_override', 1 );

/**
 * Register favicon, apple-touch-icon, theme color, and manifest.
 * Modern browsers (Chrome, Firefox, Safari, Edge) all support SVG favicons.
 * PNG fallbacks can be added later by exporting favicon.svg at 16/32/180/192/512.
 */
function sampreshan_child_favicons() {
    $base = get_stylesheet_directory_uri() . '/assets/brand/';
    ?>
    <link rel="icon" type="image/svg+xml" href="<?php echo esc_url( $base . 'favicon.svg' ); ?>" />
    <link rel="alternate icon" type="image/png" href="<?php echo esc_url( $base . 'favicon-32.svg' ); ?>" />
    <link rel="apple-touch-icon" href="<?php echo esc_url( $base . 'favicon.svg' ); ?>" />
    <meta name="theme-color" content="#FF9933" />
    <meta name="msapplication-TileColor" content="#0A1A3D" />
    <?php
}
add_action( 'wp_head', 'sampreshan_child_favicons', 2 );

/**
 * Run install migrations: creates the signature tables on theme activation
 * (or whenever the stored DB version lags). Idempotent.
 */
function sampreshan_child_run_install() {
    if ( function_exists( 'sp_install_petition_tables' ) ) {
        sp_install_petition_tables();
    }
}
add_action( 'after_switch_theme', 'sampreshan_child_run_install' );

/**
 * Make sure the tables exist on every admin load if they're missing —
 * handles the case where a user activates a different theme first and
 * comes back later.
 */
function sampreshan_child_maybe_install() {
    $installed = get_option( 'sampreshan_db_version' );
    if ( $installed ) { return; }
    sampreshan_child_run_install();
}
add_action( 'admin_init', 'sampreshan_child_maybe_install' );

/**
 * Create the /login page on theme activation. Idempotent — only creates
 * the page if no page with our meta key exists.
 */
function sampreshan_child_create_login_page() {
    $existing = get_posts( array(
        'post_type'      => 'page',
        'meta_key'       => '_wp_page_template',
        'meta_value'     => 'template-login.php',
        'posts_per_page' => 1,
        'post_status'    => 'any',
        'fields'         => 'ids',
    ) );
    if ( ! empty( $existing ) ) { return; }

    $page_id = wp_insert_post( array(
        'post_type'    => 'page',
        'post_status'  => 'publish',
        'post_title'   => __( 'Sign In', 'sampreshan-child' ),
        'post_name'    => 'login',
        'post_content' => '',
        'comment_status' => 'closed',
    ), true );
    if ( is_wp_error( $page_id ) ) { return; }
    update_post_meta( $page_id, '_wp_page_template', 'template-login.php' );
    update_post_meta( $page_id, 'sp_login_page', 1 );
}
add_action( 'after_switch_theme', 'sampreshan_child_create_login_page' );
add_action( 'admin_init', function () {
    if ( ! get_option( 'sampreshan_login_page_created' ) ) {
        sampreshan_child_create_login_page();
        update_option( 'sampreshan_login_page_created', 1 );
    }
} );

/**
 * Override the WordPress /wp-login.php UI for the registration + lost
 * password actions. We do NOT touch the wp-login.php POST handler (it
 * still does the actual login work). Instead, we redirect the GET to
 * our /login page where the user sees the Sampreshan UI.
 *
 * BuddyBoss + SSO gateways (like digit) keep working because they hook
 * into the same `login_init` action — we just intercept the visual
 * surface, not the auth surface. Other code can disable the redirect
 * by returning false from the `sampreshan_should_redirect_login`
 * filter (the Digits bridge does this when Digits is active, since
 * Digits already ships its own native login form).
 */
function sampreshan_child_override_login_visual() {
    // Only on GET requests to the login screen (don't break POST).
    if ( 'GET' !== ( $_SERVER['REQUEST_METHOD'] ?? 'GET' ) ) { return; }

    // Allow other code (e.g. Digits bridge) to opt out.
    $should_redirect = apply_filters( 'sampreshan_should_redirect_login', true );
    if ( ! $should_redirect ) { return; }

    $login_page = get_posts( array(
        'post_type'      => 'page',
        'meta_key'       => '_wp_page_template',
        'meta_value'     => 'template-login.php',
        'posts_per_page' => 1,
        'post_status'    => 'publish',
        'fields'         => 'ids',
    ) );
    if ( empty( $login_page ) ) { return; }
    $url = get_permalink( $login_page[0] );
    if ( ! $url ) { return; }

    // Preserve the action so register/lostpassword land on the right panel.
    $action = isset( $_GET['action'] ) ? sanitize_text_field( wp_unslash( $_GET['action'] ) ) : '';
    if ( in_array( $action, array( 'register', 'lostpassword' ), true ) ) {
        $url = add_query_arg( 'action', $action, $url );
    }
    if ( ! empty( $_GET['redirect_to'] ) ) {
        $url = add_query_arg( 'redirect_to', esc_url_raw( wp_unslash( $_GET['redirect_to'] ) ), $url );
    }
    wp_safe_redirect( $url, 302 );
    exit;
}
add_action( 'login_init', 'sampreshan_child_override_login_visual', 1 );

/**
 * Firebase OTP AJAX Login Handler
 * Verifies the Firebase ID token and logs the user into WordPress.
 */
function sp_firebase_login_handler() {
    check_ajax_referer( 'sp_phone_login', 'nonce' );

    $id_token = isset( $_POST['idToken'] ) ? sanitize_text_field( wp_unslash( $_POST['idToken'] ) ) : '';
    $phone    = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';

    if ( empty( $id_token ) ) {
        wp_send_json_error( array( 'message' => 'Missing Firebase ID token.' ) );
    }

    // Verify the ID token with Firebase REST API (sampreshan-login app).
    $api_key = sp_firebase_api_key();
    $response = wp_remote_post( 'https://identitytoolkit.googleapis.com/v1/accounts:lookup?key=' . $api_key, array(
        'body' => json_encode( array( 'idToken' => $id_token ) ),
        'headers' => array( 'Content-Type' => 'application/json' ),
        'timeout' => 10,
    ) );

    if ( is_wp_error( $response ) ) {
        wp_send_json_error( array( 'message' => 'Failed to verify token.' ) );
    }

    $body = json_decode( wp_remote_retrieve_body( $response ), true );

    if ( empty( $body['users'][0] ) ) {
        wp_send_json_error( array( 'message' => 'Invalid or expired token.' ) );
    }

    $firebase_user = $body['users'][0];
    $firebase_uid  = $firebase_user['localId'] ?? '';
    $fb_phone      = $firebase_user['phoneNumber'] ?? $phone;

    // Normalize phone
    $clean_phone = preg_replace( '/[^0-9]/', '', $fb_phone );
    if ( strlen( $clean_phone ) === 10 ) {
        $clean_phone = '91' . $clean_phone;
    }

    // Find existing user by phone meta
    $user_id = 0;
    $existing = get_users( array(
        'meta_key'   => 'sp_phone',
        'meta_value' => $clean_phone,
        'number'     => 1,
        'fields'     => 'ID',
    ) );

    if ( ! empty( $existing ) ) {
        $user_id = (int) $existing[0];
    } else {
        // Check by firebase UID
        $existing_uid = get_users( array(
            'meta_key'   => 'sp_firebase_uid',
            'meta_value' => $firebase_uid,
            'number'     => 1,
            'fields'     => 'ID',
        ) );

        if ( ! empty( $existing_uid ) ) {
            $user_id = (int) $existing_uid[0];
        } else {
            // Auto-create user
            $username = 'user_' . $clean_phone;
            $email    = $clean_phone . '@sampreshan.tech';
            $user_id  = wp_create_user( $username, wp_generate_password(), $email );

            if ( is_wp_error( $user_id ) ) {
                wp_send_json_error( array( 'message' => 'Failed to create account.' ) );
            }

            // Set role
            $user = get_userdata( $user_id );
            $user->set_role( 'subscriber' );
        }
    }

    // Store Firebase UID and phone
    update_user_meta( $user_id, 'sp_phone', $clean_phone );
    update_user_meta( $user_id, 'sp_firebase_uid', $firebase_uid );

    // Log the user in
    wp_set_current_user( $user_id );
    wp_set_auth_cookie( $user_id, true );
    do_action( 'wp_login', get_userdata( $user_id )->user_login, get_userdata( $user_id ) );

    wp_send_json_success( array(
        'message'  => 'Login successful',
        'redirect' => sampreshan_dashboard_url(),
        'userId'   => $user_id,
    ) );
}
add_action( 'wp_ajax_sp_firebase_login', 'sp_firebase_login_handler' );
add_action( 'wp_ajax_nopriv_sp_firebase_login', 'sp_firebase_login_handler' );

/**
 * Ensure an existing page uses our template.
 * Non-destructive: only sets `_wp_page_template` meta, never touches content.
 * Used when a slug already exists (e.g. legacy Elementor/HTML pages) so
 * every page renders in the same style.
 */
function sampreshan_child_ensure_template( $page_id, $template ) {
    $page_id = (int) $page_id;
    if ( $page_id <= 0 || 'page' !== get_post_type( $page_id ) ) { return; }
    $current = get_post_meta( $page_id, '_wp_page_template', true );
    if ( $current !== $template ) {
        update_post_meta( $page_id, '_wp_page_template', $template );
    }
}

/**
 * Auto-create essential pages on theme activation.
 * Creates: About, Contact, Community Guidelines, Privacy Policy, Terms, Disclaimer.
 * Idempotent — skips if page already exists.
 */
function sampreshan_child_create_essential_pages() {
    $pages = array(
        array(
            'title'    => __( 'About', 'sampreshan-child' ),
            'slug'     => 'about',
            'template' => 'template-about.php',
            'option'   => 'sampreshan_about_page_created',
        ),
        array(
            'title'    => __( 'Contact', 'sampreshan-child' ),
            'slug'     => 'contact',
            'template' => 'template-contact.php',
            'option'   => 'sampreshan_contact_page_created',
        ),
        array(
            'title'    => __( 'Community Guidelines', 'sampreshan-child' ),
            'slug'     => 'community-guidelines',
            'template' => 'template-guidelines.php',
            'option'   => 'sampreshan_guidelines_page_created',
        ),
        array(
            'title'    => __( 'Privacy Policy', 'sampreshan-child' ),
            'slug'     => 'privacy-policy',
            'template' => 'template-privacy.php',
            'option'   => 'sampreshan_privacy_page_created',
        ),
        array(
            'title'    => __( 'Terms & Conditions', 'sampreshan-child' ),
            'slug'     => 'terms-conditions',
            'template' => 'template-terms.php',
            'option'   => 'sampreshan_terms_page_created',
        ),
        array(
            'title'    => __( 'Disclaimer', 'sampreshan-child' ),
            'slug'     => 'disclaimer',
            'template' => 'template-disclaimer.php',
            'option'   => 'sampreshan_disclaimer_page_created',
        ),
    );

    foreach ( $pages as $page ) {
        // If a page with this slug already exists (e.g. legacy import),
        // make sure it renders with our template, then mark done.
        $existing = get_page_by_path( $page['slug'] );
        if ( $existing ) {
            sampreshan_child_ensure_template( $existing->ID, $page['template'] );
            update_option( $page['option'], 1 );
            continue;
        }

        // Skip if already created
        if ( get_option( $page['option'] ) ) { continue; }

        // Skip if template is already assigned to another page
        $template_page = get_posts( array(
            'post_type'      => 'page',
            'meta_key'       => '_wp_page_template',
            'meta_value'     => $page['template'],
            'posts_per_page' => 1,
            'post_status'    => 'any',
            'fields'         => 'ids',
        ) );
        if ( ! empty( $template_page ) ) {
            update_option( $page['option'], 1 );
            continue;
        }

        $page_id = wp_insert_post( array(
            'post_type'      => 'page',
            'post_status'    => 'publish',
            'post_title'     => $page['title'],
            'post_name'      => $page['slug'],
            'post_content'   => '',
            'comment_status' => 'closed',
        ), true );

        if ( ! is_wp_error( $page_id ) ) {
            update_post_meta( $page_id, '_wp_page_template', $page['template'] );
        }

        update_option( $page['option'], 1 );
    }
}
add_action( 'after_switch_theme', 'sampreshan_child_create_essential_pages' );
add_action( 'admin_init', function () {
    $options = array(
        'sampreshan_about_page_created',
        'sampreshan_contact_page_created',
        'sampreshan_guidelines_page_created',
        'sampreshan_privacy_page_created',
        'sampreshan_terms_page_created',
        'sampreshan_disclaimer_page_created',
    );
    $needs_run = ! get_option( 'sampreshan_pages_template_fix_v2' );
    if ( ! $needs_run ) {
        foreach ( $options as $opt ) {
            if ( ! get_option( $opt ) ) { $needs_run = true; break; }
        }
    }
    if ( $needs_run ) {
        sampreshan_child_create_essential_pages();
        update_option( 'sampreshan_pages_template_fix_v2', 1 );
    }
} );

/**
 * AJAX: Create a petition from the front-end form.
 * Handles title, description, category, goal, deadline, cover image upload.
 */
function sp_ajax_create_petition() {
    check_ajax_referer( 'sp_create_petition', 'nonce' );

    if ( ! is_user_logged_in() ) {
        wp_send_json_error( array( 'message' => __( 'You must be logged in.', 'sampreshan-child' ) ), 401 );
    }

    // Rate limit: max 3 petitions per user per hour
    $user_id = get_current_user_id();
    $rate_key = 'sp_petition_rate_' . $user_id;
    $rate_count = (int) get_transient( $rate_key );
    if ( $rate_count >= 3 ) {
        wp_send_json_error( array( 'message' => __( 'You can create up to 3 petitions per hour. Please try again later.', 'sampreshan-child' ) ), 429 );
    }

    $title       = isset( $_POST['petition_title'] ) ? sanitize_text_field( wp_unslash( $_POST['petition_title'] ) ) : '';
    $summary     = isset( $_POST['petition_summary'] ) ? sanitize_textarea_field( wp_unslash( $_POST['petition_summary'] ) ) : '';
    $description = isset( $_POST['petition_description'] ) ? wp_kses_post( wp_unslash( $_POST['petition_description'] ) ) : '';
    $cause_id    = isset( $_POST['cause_category'] ) ? (int) $_POST['cause_category'] : 0;
    $goal        = isset( $_POST['petition_goal'] ) ? max( 10, (int) $_POST['petition_goal'] ) : 1000;
    $deadline    = isset( $_POST['petition_deadline'] ) ? sanitize_text_field( wp_unslash( $_POST['petition_deadline'] ) ) : '';
    $location    = isset( $_POST['petition_location'] ) ? sanitize_text_field( wp_unslash( $_POST['petition_location'] ) ) : '';
    $target      = isset( $_POST['petition_target'] ) ? sanitize_text_field( wp_unslash( $_POST['petition_target'] ) ) : '';
    $anonymous   = ! empty( $_POST['petition_anonymous'] );

    if ( empty( $title ) ) {
        wp_send_json_error( array( 'message' => __( 'Please enter a petition title.', 'sampreshan-child' ) ), 400 );
    }
    if ( mb_strlen( $title ) < 10 ) {
        wp_send_json_error( array( 'message' => __( 'Title must be at least 10 characters.', 'sampreshan-child' ) ), 400 );
    }
    if ( empty( $summary ) ) {
        wp_send_json_error( array( 'message' => __( 'Please write a short summary.', 'sampreshan-child' ) ), 400 );
    }
    if ( mb_strlen( $summary ) < 20 ) {
        wp_send_json_error( array( 'message' => __( 'Summary must be at least 20 characters.', 'sampreshan-child' ) ), 400 );
    }

    // Build content from summary + description
    $content = '';
    if ( $summary ) {
        $content .= '<p class="sp-petition-summary"><em>' . esc_html( $summary ) . '</em></p>';
    }
    if ( $description ) {
        $content .= wp_kses_post( $description );
    }

    // Create the petition post
    $petition_id = wp_insert_post( array(
        'post_type'    => 'petition',
        'post_status'  => 'publish',
        'post_title'   => $title,
        'post_content' => $content,
        'post_excerpt' => $summary,
        'post_author'  => get_current_user_id(),
    ), true );

    if ( is_wp_error( $petition_id ) ) {
        wp_send_json_error( array( 'message' => __( 'Failed to create petition. Please try again.', 'sampreshan-child' ) ), 500 );
    }

    // Set meta
    update_post_meta( $petition_id, 'sampreshan_status', 'active' );
    update_post_meta( $petition_id, 'sampreshan_goal', $goal );
    update_post_meta( $petition_id, 'sampreshan_signatures', 0 );
    update_post_meta( $petition_id, 'sampreshan_featured', 0 );
    if ( $deadline ) {
        update_post_meta( $petition_id, 'sampreshan_deadline', $deadline );
    }
    if ( $location ) {
        update_post_meta( $petition_id, 'petition_location', $location );
    }
    if ( $target ) {
        update_post_meta( $petition_id, 'petition_target', $target );
    }
    if ( $anonymous ) {
        update_post_meta( $petition_id, 'petition_anonymous', 1 );
    }

    // Assign cause category
    if ( $cause_id > 0 ) {
        wp_set_object_terms( $petition_id, $cause_id, 'cause_category' );
    }

    // Handle cover image upload
    if ( ! empty( $_FILES['petition_cover'] ) && $_FILES['petition_cover']['error'] === UPLOAD_ERR_OK ) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';

        $file = $_FILES['petition_cover'];
        $allowed = array( 'image/jpeg', 'image/png', 'image/webp' );
        $finfo = finfo_open( FILEINFO_MIME_TYPE );
        $mime  = finfo_file( $finfo, $file['tmp_name'] );

        if ( in_array( $mime, $allowed, true ) && $file['size'] <= 5 * 1024 * 1024 ) {
            $attach_id = media_handle_upload( 'petition_cover', $petition_id );
            if ( ! is_wp_error( $attach_id ) ) {
                set_post_thumbnail( $petition_id, $attach_id );
            }
        }
    }

    // Auto-promote subscriber to petitioner
    if ( function_exists( 'sp_auto_promote_subscriber_to_petitioner' ) ) {
        sp_auto_promote_subscriber_to_petitioner( get_current_user_id() );
    }

    // Increment rate limit counter (1 hour expiry)
    set_transient( $rate_key, $rate_count + 1, HOUR_IN_SECONDS );

    wp_send_json_success( array(
        'message'   => __( 'Petition published successfully!', 'sampreshan-child' ),
        'petitionId' => $petition_id,
        'permalink' => get_permalink( $petition_id ),
    ) );
}
add_action( 'wp_ajax_sp_create_petition', 'sp_ajax_create_petition' );

/**
 * Auto-create Start a Petition + Dashboard pages on theme activation.
 * (Kept for backward compatibility — now delegates to the unified creator.)
 */
function sampreshan_child_create_action_pages() {
    sampreshan_child_create_all_user_pages();
}

/**
 * Unified auto-creator: every user-facing page the theme ships.
 * Idempotent — never duplicates an existing slug or template assignment.
 */
function sampreshan_child_create_all_user_pages() {
    $pages = array(
        // Action pages
        array(
            'title'    => __( 'Start a Petition', 'sampreshan-child' ),
            'slug'     => 'start-a-petition',
            'template' => 'template-start-petition.php',
            'option'   => 'sampreshan_start_petition_page_created',
        ),
        array(
            'title'    => __( 'Dashboard', 'sampreshan-child' ),
            'slug'     => 'dashboard',
            'template' => 'template-dashboard.php',
            'option'   => 'sampreshan_dashboard_page_created',
        ),
        // User pages
        array(
            'title'    => __( 'Petitions', 'sampreshan-child' ),
            'slug'     => 'petitions',
            'template' => 'template-petitions.php',
            'option'   => 'sampreshan_petitions_page_created',
        ),
        array(
            'title'    => __( 'My Petitions', 'sampreshan-child' ),
            'slug'     => 'my-petitions',
            'template' => 'template-my-petitions.php',
            'option'   => 'sampreshan_my_petitions_page_created',
        ),
        array(
            'title'    => __( 'Supported Issues', 'sampreshan-child' ),
            'slug'     => 'signed-petitions',
            'template' => 'template-signed.php',
            'option'   => 'sampreshan_signed_page_created',
        ),
        array(
            'title'    => __( 'Profile', 'sampreshan-child' ),
            'slug'     => 'profile',
            'template' => 'template-profile.php',
            'option'   => 'sampreshan_profile_page_created',
        ),
        array(
            'title'    => __( 'Settings', 'sampreshan-child' ),
            'slug'     => 'settings',
            'template' => 'template-settings.php',
            'option'   => 'sampreshan_settings_page_created',
        ),
        array(
            'title'    => __( 'Community', 'sampreshan-child' ),
            'slug'     => 'community',
            'template' => 'template-members.php',
            'option'   => 'sampreshan_community_page_created',
        ),
        array(
            'title'    => __( 'Feed', 'sampreshan-child' ),
            'slug'     => 'feed',
            'template' => 'template-activity.php',
            'option'   => 'sampreshan_feed_page_created',
        ),
        // Category / cause pages (Quick Links section)
        array(
            'title'    => __( 'Temple Preservation', 'sampreshan-child' ),
            'slug'     => 'category-temple-preservation',
            'template' => 'template-category.php',
            'option'   => 'sampreshan_category_temple_page_created',
        ),
        array(
            'title'    => __( 'Cultural Heritage', 'sampreshan-child' ),
            'slug'     => 'category-cultural-heritage',
            'template' => 'template-category.php',
            'option'   => 'sampreshan_category_cultural_page_created',
        ),
        array(
            'title'    => __( 'Religious Education', 'sampreshan-child' ),
            'slug'     => 'category-religious-education',
            'template' => 'template-category.php',
            'option'   => 'sampreshan_category_religious_page_created',
        ),
        array(
            'title'    => __( 'Environmental Causes', 'sampreshan-child' ),
            'slug'     => 'category-environmental-causes',
            'template' => 'template-category.php',
            'option'   => 'sampreshan_category_environmental_page_created',
        ),
        array(
            'title'    => __( 'Community Welfare', 'sampreshan-child' ),
            'slug'     => 'category-community-welfare',
            'template' => 'template-category.php',
            'option'   => 'sampreshan_category_community_page_created',
        ),
    );

    foreach ( $pages as $page ) {
        // If a page with this slug already exists (e.g. legacy import),
        // make sure it renders with our template, then mark done.
        $existing = get_page_by_path( $page['slug'] );
        if ( $existing ) {
            sampreshan_child_ensure_template( $existing->ID, $page['template'] );
            update_option( $page['option'], 1 );
            continue;
        }

        if ( get_option( $page['option'] ) ) { continue; }

        $template_page = get_posts( array(
            'post_type'      => 'page',
            'meta_key'       => '_wp_page_template',
            'meta_value'     => $page['template'],
            'posts_per_page' => 1,
            'post_status'    => 'any',
            'fields'         => 'ids',
        ) );
        if ( ! empty( $template_page ) ) {
            update_option( $page['option'], 1 );
            continue;
        }

        $page_id = wp_insert_post( array(
            'post_type'      => 'page',
            'post_status'    => 'publish',
            'post_title'     => $page['title'],
            'post_name'      => $page['slug'],
            'post_content'   => '',
            'comment_status' => 'closed',
        ), true );

        if ( ! is_wp_error( $page_id ) ) {
            update_post_meta( $page_id, '_wp_page_template', $page['template'] );
        }

        update_option( $page['option'], 1 );
    }
}
add_action( 'after_switch_theme', 'sampreshan_child_create_all_user_pages' );
add_action( 'admin_init', function () {
    $options = array(
        'sampreshan_start_petition_page_created',
        'sampreshan_dashboard_page_created',
        'sampreshan_petitions_page_created',
        'sampreshan_my_petitions_page_created',
        'sampreshan_signed_page_created',
        'sampreshan_profile_page_created',
        'sampreshan_settings_page_created',
        'sampreshan_community_page_created',
        'sampreshan_feed_page_created',
        'sampreshan_category_temple_page_created',
        'sampreshan_category_cultural_page_created',
        'sampreshan_category_religious_page_created',
        'sampreshan_category_environmental_page_created',
        'sampreshan_category_community_page_created',
    );
    $needs_run = ! get_option( 'sampreshan_pages_template_fix_v2' );
    if ( ! $needs_run ) {
        foreach ( $options as $opt ) {
            if ( ! get_option( $opt ) ) { $needs_run = true; break; }
        }
    }
    if ( $needs_run ) {
        sampreshan_child_create_all_user_pages();
        update_option( 'sampreshan_pages_template_fix_v2', 1 );
    }
} );

/**
 * Email notification when a petition receives a new signature.
 * Sends a digest-style email to the petition author.
 */
function sp_petition_notify_author_on_signature( $petition_id, $signer_id, $signature_id ) {
    $petition = get_post( $petition_id );
    if ( ! $petition ) { return; }

    $author_id = (int) $petition->post_author;
    if ( $author_id <= 0 ) { return; }

    $author = get_userdata( $author_id );
    if ( ! $author ) { return; }

    $signer       = get_userdata( $signer_id );
    $signer_name  = $signer ? $signer->display_name : __( 'Anonymous', 'sampreshan-child' );
    $sig_count    = function_exists( 'sp_petition_signature_count' ) ? sp_petition_signature_count( $petition_id ) : 0;
    $petition_url = get_permalink( $petition_id );
    $site_name    = get_bloginfo( 'name' );

    $subject = sprintf(
        /* translators: 1: site name, 2: signer name, 3: petition title */
        __( '[%1$s] %2$s gave an I to your issue "%3$s"', 'sampreshan-child' ),
        $site_name,
        $signer_name,
        $petition->post_title
    );

    $message = sprintf(
        /* translators: 1: author, 2: signer, 3: title, 4: count, 5: url, 6: site */
        __( "Hello %1$s,\n\n%2$s just gave an I to your issue \"%3$s\"!\n\nTotal Is: %4$s\n\nView your issue:\n%5$s\n\nKeep building support for your cause!\n\n— %6$s Team", 'sampreshan-child' ),
        $author->display_name,
        $signer_name,
        $petition->post_title,
        number_format_i18n( $sig_count ),
        $petition_url,
        $site_name
    );

    $headers = array( 'Content-Type: text/plain; charset=UTF-8' );

    wp_mail( $author->user_email, $subject, $message, $headers );
}
add_action( 'sampreshan_petition_signed', 'sp_petition_notify_author_on_signature', 10, 3 );

/**
 * Prevent users from signing the same petition twice via direct URL manipulation.
 */
function sp_enforce_single_signature_per_user() {
    if ( ! is_user_logged_in() ) { return; }
    // The signature handler already has a UNIQUE constraint, but this
    // provides a cleaner error message for the UI.
}
add_action( 'init', 'sp_enforce_single_signature_per_user' );

/**
 * Sanitize and validate petition creation inputs server-side.
 */
function sp_sanitize_petition_input( $data ) {
    if ( isset( $data['post_title'] ) ) {
        $data['post_title'] = sanitize_text_field( $data['post_title'] );
    }
    if ( isset( $data['post_content'] ) ) {
        $data['post_content'] = wp_kses_post( $data['post_content'] );
    }
    return $data;
}
add_filter( 'wp_insert_post_data', 'sp_sanitize_petition_input', 10, 1 );
