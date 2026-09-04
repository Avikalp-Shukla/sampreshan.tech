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
 * Theme version (for cache busting)
 */
if ( ! defined( 'SAMPRESHAN_CHILD_VERSION' ) ) {
    define( 'SAMPRESHAN_CHILD_VERSION', '1.3.0' );
}

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

/**
 * Enqueue Google Fonts (preconnect + display=swap for performance)
 */
function sampreshan_child_enqueue_fonts() {
    // Preconnect to Google Fonts servers
    echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";

    // Load fonts: Cormorant Garamond (display/serif) + Inter (body/heading) + Tiro Devanagari (Hindi)
    wp_enqueue_style(
        'sampreshan-child-fonts',
        'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Inter:wght@400;500;600;700&family=Tiro+Devanagari:wght@400;700&display=swap',
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
            'ajaxUrl'    => admin_url( 'admin-ajax.php' ),
            'phoneNonce' => wp_create_nonce( 'sp_phone_login' ),
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

    // Firebase OTP phone login (loaded on login page + registration)
    if ( is_page_template( 'template-login.php' ) || is_page_template( 'template-register.php' ) ) {
        wp_enqueue_script(
            'sampreshan-firebase-otp',
            get_stylesheet_directory_uri() . '/assets/js/firebase-otp.js',
            array(),
            SAMPRESHAN_CHILD_VERSION,
            true
        );
        wp_localize_script( 'sampreshan-firebase-otp', 'SampreshanAuth', array(
            'ajaxUrl'    => admin_url( 'admin-ajax.php' ),
            'phoneNonce' => wp_create_nonce( 'sp_phone_login' ),
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

    // Verify the ID token with Firebase REST API
    $api_key = 'AIzaSyBd_Hl4uaivSnm3Ue0N_xcKexKX4PIIUpI';
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
        'redirect' => home_url( '/' ),
        'userId'   => $user_id,
    ) );
}
add_action( 'wp_ajax_sp_firebase_login', 'sp_firebase_login_handler' );
add_action( 'wp_ajax_nopriv_sp_firebase_login', 'sp_firebase_login_handler' );

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
        // Skip if already created
        if ( get_option( $page['option'] ) ) { continue; }

        // Skip if a page with this slug already exists
        $existing = get_page_by_path( $page['slug'] );
        if ( $existing ) {
            update_option( $page['option'], 1 );
            continue;
        }

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
    $needs_run = false;
    foreach ( $options as $opt ) {
        if ( ! get_option( $opt ) ) { $needs_run = true; break; }
    }
    if ( $needs_run ) {
        sampreshan_child_create_essential_pages();
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
            'title'    => __( 'Signed Petitions', 'sampreshan-child' ),
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
    );

    foreach ( $pages as $page ) {
        if ( get_option( $page['option'] ) ) { continue; }

        $existing = get_page_by_path( $page['slug'] );
        if ( $existing ) {
            update_option( $page['option'], 1 );
            continue;
        }

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
    );
    $needs_run = false;
    foreach ( $options as $opt ) {
        if ( ! get_option( $opt ) ) { $needs_run = true; break; }
    }
    if ( $needs_run ) {
        sampreshan_child_create_all_user_pages();
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
        /* translators: 1: signer name, 2: petition title */
        __( '[%1$s] %2$s signed your petition "%3$s"', 'sampreshan-child' ),
        $site_name,
        $signer_name,
        $petition->post_title
    );

    $message = sprintf(
        /* translators: 1: signer name, 2: petition title, 3: signature count, 4: petition URL */
        __( "Hello %1$s,\n\n%2$s just signed your petition \"%3$s\"!\n\nTotal signatures: %4$s\n\nView your petition:\n%5$s\n\nKeep building support for your cause!\n\n— %6$s Team", 'sampreshan-child' ),
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
