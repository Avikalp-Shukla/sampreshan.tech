<?php
/**
 * ShivBodh Trust Child Theme Functions (PATCHED v2.0.1)
 *
 * - Walker class moved to inc/class-nav-walker.php and loaded here
 * - New CPTs registered: petition, donation, member
 * - Font enqueue uses display=swap + preconnect already added
 * - Preload critical font
 *
 * @package ShivBodh_Child
 * @version 2.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'SHIVBODH_CHILD_VERSION', '2.0.1' );
define( 'SHIVBODH_CHILD_DIR', get_stylesheet_directory() );
define( 'SHIVBODH_CHILD_URI', get_stylesheet_directory_uri() );

/**
 * Load walker BEFORE header.php is parsed.
 */
require_once SHIVBODH_CHILD_DIR . '/inc/class-nav-walker.php';

/**
 * Enqueue styles and scripts
 */
function shivbodh_child_enqueue_assets() {
    wp_enqueue_style(
        'astra-parent',
        get_template_directory_uri() . '/style.css',
        array(),
        wp_get_theme( 'astra' )->get( 'Version' )
    );

    wp_enqueue_style(
        'shivbodh-child',
        SHIVBODH_CHILD_URI . '/style.css',
        array( 'astra-parent' ),
        SHIVBODH_CHILD_VERSION
    );

    wp_enqueue_style(
        'shivbodh-responsive',
        SHIVBODH_CHILD_URI . '/responsive.css',
        array( 'shivbodh-child' ),
        SHIVBODH_CHILD_VERSION
    );

    wp_enqueue_style(
        'shivbodh-google-fonts',
        'https://fonts.googleapis.com/css2?family=Noto+Sans:wght@300;400;500;600;700&family=Noto+Sans+Devanagari:wght@300;400;500;600;700&family=Noto+Serif+Devanagari:wght@400;500;600;700&display=swap',
        array(),
        null
    );

    wp_enqueue_script(
        'shivbodh-main',
        SHIVBODH_CHILD_URI . '/js/main.js',
        array(),
        SHIVBODH_CHILD_VERSION,
        true
    );

    wp_localize_script( 'shivbodh-main', 'shivbodhAjax', array(
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
        'nonce'   => wp_create_nonce( 'shivbodh_nonce' ),
    ) );
}
add_action( 'wp_enqueue_scripts', 'shivbodh_child_enqueue_assets' );

/**
 * Theme setup
 */
function shivbodh_child_setup() {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ) );
    add_theme_support( 'custom-logo', array(
        'height'      => 60,
        'width'       => 200,
        'flex-height' => true,
        'flex-width'  => true,
    ) );

    register_nav_menus( array(
        'primary'   => esc_html__( 'Primary Menu', 'shivbodh-child' ),
        'footer'    => esc_html__( 'Footer Menu', 'shivbodh-child' ),
        'mobile'    => esc_html__( 'Mobile Menu', 'shivbodh-child' ),
    ) );
}
add_action( 'after_setup_theme', 'shivbodh_child_setup' );

/**
 * Register widget areas
 */
function shivbodh_child_widgets_init() {
    register_sidebar( array(
        'name'          => esc_html__( 'Blog Sidebar', 'shivbodh-child' ),
        'id'            => 'blog-sidebar',
        'description'   => esc_html__( 'Widgets in this area will appear on blog pages.', 'shivbodh-child' ),
        'before_widget' => '<div id="%1$s" class="sb-sidebar-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="sb-sidebar-widget-title">',
        'after_title'   => '</h3>',
    ) );

    for ( $i = 1; $i <= 3; $i++ ) {
        register_sidebar( array(
            'name'          => sprintf( esc_html__( 'Footer Column %d', 'shivbodh-child' ), $i ),
            'id'            => 'footer-' . $i,
            'before_widget' => '<div id="%1$s" class="%2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h4 class="sb-footer-heading">',
            'after_title'   => '</h4>',
        ) );
    }
}
add_action( 'widgets_init', 'shivbodh_child_widgets_init' );

/**
 * Custom excerpt length
 */
function shivbodh_child_excerpt_length( $length ) {
    return 25;
}
add_filter( 'excerpt_length', 'shivbodh_child_excerpt_length' );

function shivbodh_child_excerpt_more( $more ) {
    return '&hellip;';
}
add_filter( 'excerpt_more', 'shivbodh_child_excerpt_more' );

/**
 * Preload critical resources
 */
function shivbodh_child_preload_resources() {
    ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="style" href="<?php echo esc_url( SHIVBODH_CHILD_URI . '/style.css' ); ?>">
    <?php
}
add_action( 'wp_head', 'shivbodh_child_preload_resources', 1 );

/**
 * Defer non-critical JS (main.js + any future handles)
 */
function shivbodh_child_defer_scripts( $tag, $handle ) {
    $defer_handles = array( 'shivbodh-main' );
    if ( in_array( $handle, $defer_handles, true ) ) {
        return str_replace( ' src', ' defer src', $tag );
    }
    return $tag;
}
add_filter( 'script_loader_tag', 'shivbodh_child_defer_scripts', 10, 2 );

/**
 * Apply dark mode class early to prevent flash
 */
function shivbodh_child_inline_script() {
    ?>
    <script>
    (function() {
        var saved = localStorage.getItem('sb-theme');
        if (saved) {
            document.documentElement.setAttribute('data-theme', saved);
        } else if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
            document.documentElement.setAttribute('data-theme', 'dark');
        }
    })();
    </script>
    <?php
}
add_action( 'wp_head', 'shivbodh_child_inline_script', 0 );

/**
 * Peetham data helper
 */
function shivbodh_get_peethams() {
    return array(
        array(
            'name'         => 'Sringeri Sharada Peetham',
            'name_hi'      => 'श्रृंगेरी शारदा पीठ',
            'direction'    => 'Dakshinamnaya (South)',
            'direction_hi' => 'दक्षिणाम्नाय',
            'veda'         => 'Yajur Veda',
            'mahavakya'    => 'Aham Brahmāsmi',
            'url'          => '/sringeri-peetha/',
            'acharya'      => '/swami-vidhushekhara-bharati/',
            'acharya_name' => 'Sri Sri Vidhushekhara Bharati',
            'image'        => 'https://shivbodhtrust.org/wp-content/uploads/2026/03/sringeri.jpg',
        ),
        array(
            'name'         => 'Dwarka Sharada Peetham',
            'name_hi'      => 'द्वारका शारदा पीठ',
            'direction'    => 'Paschimamnaya (West)',
            'direction_hi' => 'पश्चिमाम्नाय',
            'veda'         => 'Sama Veda',
            'mahavakya'    => 'Tattvamasi',
            'url'          => '/dwarka-peetha/',
            'acharya'      => '/swami-sadanand-saraswati/',
            'acharya_name' => 'Swami Sadanand Saraswat',
            'image'        => 'https://shivbodhtrust.org/wp-content/uploads/2026/03/dwarka.jpg',
        ),
        array(
            'name'         => 'Jyotirmath Peetham',
            'name_hi'      => 'ज्योतिर्मठ पीठ',
            'direction'    => 'Uttaraminaya (North)',
            'direction_hi' => 'उत्तराम्नाय',
            'veda'         => 'Atharva Veda',
            'mahavakya'    => 'Ayam Ātmā Brahma',
            'url'          => '/jyotishpeetha/',
            'acharya'      => '/swami-avimukta/',
            'acharya_name' => 'Swami Avimukteshwaranand',
            'image'        => 'https://shivbodhtrust.org/wp-content/uploads/2026/03/jyotirmath.jpg',
        ),
        array(
            'name'         => 'Govardhan Math, Puri',
            'name_hi'      => 'गोवर्धन मठ, पुरी',
            'direction'    => 'Pūrvāmnāya (East)',
            'direction_hi' => 'पूर्वाम्नाय',
            'veda'         => 'Rig Veda',
            'mahavakya'    => 'Prajnānam Brahma',
            'url'          => '/puri-peetha/',
            'acharya'      => '/swami-nishchalananda-saraswati/',
            'acharya_name' => 'Swami Nischalananda Saraswati',
            'image'        => 'https://shivbodhtrust.org/wp-content/uploads/2026/03/puri.jpg',
        ),
    );
}

/**
 * Register Custom Post Types
 */
function shivbodh_child_register_cpts() {

    $cpts = array(

        'sb_petition' => array(
            'label'        => 'धार्मिक याचिकाएँ',
            'singular'     => 'याचिका',
            'plural'       => 'याचिकाएँ',
            'slug'         => 'petition',
            'menu_icon'    => 'dashicons-megaphone',
            'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
            'public'       => true,
            'has_archive'  => true,
            'show_in_rest' => true,
        ),

        'sb_donation' => array(
            'label'        => 'दान',
            'singular'     => 'दान',
            'plural'       => 'दान सूची',
            'slug'         => 'donation',
            'menu_icon'    => 'dashicons-heart',
            'supports'     => array( 'title' ),
            'public'       => false,
            'show_ui'      => true,
            'show_in_rest' => true,
        ),

        'sb_member' => array(
            'label'        => 'सदस्य',
            'singular'     => 'सदस्य',
            'plural'       => 'सदस्य सूची',
            'slug'         => 'member',
            'menu_icon'    => 'dash-groups',
            'supports'     => array( 'title' ),
            'public'       => false,
            'show_ui'      => true,
            'show_in_rest' => true,
        ),

        'sb_shloka' => array(
            'label'        => 'श्लोक',
            'singular'     => 'श्लोक',
            'plural'       => 'श्लोक संग्रह',
            'slug'         => 'shloka',
            'menu_icon'    => 'dashicons-book',
            'supports'     => array( 'title', 'editor' ),
            'public'       => false,
            'show_ui'      => true,
            'show_in_rest' => true,
        ),

    );

    foreach ( $cpts as $cpt => $args ) {
        $labels = array(
            'name'          => $args['plural'],
            'singular_name' => $args['singular'],
            'menu_name'     => $args['label'],
            'add_new_item'  => 'नया ' . $args['singular'] . ' जोड़ें',
            'edit_item'     => $args['singular'] . ' संपादित करें',
        );

        register_post_type( $cpt, array(
            'labels'        => $labels,
            'public'        => isset( $args['public'] ) ? $args['public'] : true,
            'show_ui'       => isset( $args['show_ui'] ) ? $args['show_ui'] : true,
            'show_in_rest'  => $args['show_in_rest'],
            'has_archive'   => isset( $args['has_archive'] ) ? $args['has_archive'] : false,
            'menu_icon'     => $args['menu_icon'],
            'supports'      => $args['supports'],
            'rewrite'       => array( 'slug' => $args['slug'] ),
            'capability_type' => 'post',
        ) );
    }
}
add_action( 'init', 'shivbodh_child_register_cpts' );

/**
 * Register Taxonomies
 */
function shivbodh_child_register_taxonomies() {

    register_taxonomy( 'petition_category', 'sb_petition', array(
        'labels'       => array(
            'name' => 'याचिका श्रेणी',
            'singular_name' => 'श्रेणी',
        ),
        'public'       => true,
        'hierarchical' => true,
        'show_in_rest' => true,
        'rewrite'      => array( 'slug' => 'petition-category' ),
    ) );

    register_taxonomy( 'petition_peetham', 'sb_petition', array(
        'labels'       => array(
            'name' => 'संबंधित पीठ',
            'singular_name' => 'पीठ',
        ),
        'public'       => true,
        'hierarchical' => true,
        'show_in_rest' => true,
        'rewrite'      => array( 'slug' => 'peetham' ),
    ) );
}
add_action( 'init', 'shivbodh_child_register_taxonomies' );

/**
 * Shiva Gita chapters helper
 */
function shivbodh_get_gita_chapters() {
    return array(
        array( 'num' => 1,  'title' => 'Dhyanam',          'url' => '/shiva-gita-chapter-1/' ),
        array( 'num' => 2,  'title' => 'Prakaranam',       'url' => '/shiva-gita-chapter-2/' ),
        array( 'num' => 3,  'title' => 'Paramatma Viveka', 'url' => '/shiva-gita-chapter-3/' ),
        array( 'num' => 4,  'title' => 'Jnana Karma',      'url' => '/shiva-gita-chapter-4/' ),
        array( 'num' => 5,  'title' => 'Vairagya',         'url' => '/shiva-gita-chapter-5/' ),
        array( 'num' => 6,  'title' => 'Bhakti Yoga',      'url' => '/shiva-gita-chapter-6/' ),
        array( 'num' => 7,  'title' => 'Jnana Yogam',      'url' => '/shiva-gita-chapter-7/' ),
        array( 'num' => 8,  'title' => 'Ashrama',          'url' => '/shiva-gita-chapter-8/' ),
        array( 'num' => 9,  'title' => 'Sadhana',          'url' => '/shiva-gita-chapter-9/' ),
        array( 'num' => 10, 'title' => 'Guhya',            'url' => '/shiva-gita-chapter-10/' ),
        array( 'num' => 11, 'title' => 'Vijnana',          'url' => '/shiva-gita-chapter-11/' ),
        array( 'num' => 12, 'title' => 'Upasana',          'url' => '/shiva-gita-chapter-12/' ),
        array( 'num' => 13, 'title' => 'Sankhya',          'url' => '/shiva-gita-chapter-13/' ),
        array( 'num' => 14, 'title' => 'Asha',             'url' => '/shiva-gita-chapter-14/' ),
        array( 'num' => 15, 'title' => 'Moksha',           'url' => '/shiva-gita-chapter-15/' ),
        array( 'num' => 16, 'title' => 'Ananda',           'url' => '/shiva-gita-chapter-16/' ),
    );
}

/**
 * Add body classes
 */
function shivbodh_child_body_classes( $classes ) {
    if ( is_front_page() ) {
        $classes[] = 'sb-front-page';
    }
    if ( is_single() ) {
        $classes[] = 'sb-single-page';
    }
    if ( is_singular( 'sb_petition' ) ) {
        $classes[] = 'sb-petition-page';
    }
    return $classes;
}
add_filter( 'body_class', 'shivbodh_child_body_classes' );

/**
 * Disable Astra header/footer on front page
 */
function shivbodh_child_disable_astra_header_footer() {
    if ( is_front_page() ) {
        add_filter( 'astra_header_layouts', '__return_empty_array' );
    }
}
add_action( 'init', 'shivbodh_child_disable_astra_header_footer' );

/**
 * Schema.org Organization markup (homepage only)
 */
function shivbodh_child_schema_markup() {
    if ( ! is_front_page() ) {
        return;
    }
    $schema = array(
        '@context'    => 'https://schema.org',
        '@type'       => 'Organization',
        'name'        => 'ShivBodh Trust',
        'url'         => 'https://shivbodhtrust.org',
        'logo'        => 'https://shivbodhtrust.org/wp-content/uploads/2026/09/cropped-shivbodh-trust-new-fine-logo-200x54.jpg',
        'description' => 'ShivBodh Trust - Preserving and propagating the authentic teachings of Sanatana Dharma through the wisdom of the four Amnaya Peethams.',
        'sameAs'      => array(
            'https://www.facebook.com/shivbodhtrust',
            'https://twitter.com/shivbodhtrust',
            'https://www.youtube.com/@shivbodhtrust',
        ),
    );
    ?>
    <script type="application/ld+json"><?php echo wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ); ?></script>
    <?php
}
add_action( 'wp_head', 'shivbodh_child_schema_markup' );

/**
 * REST: Petition sign endpoint (double opt-in stub)
 */
function shivbodh_child_register_rest_routes() {
    register_rest_route( 'sb/v1', '/petition/sign', array(
        'methods'             => 'POST',
        'callback'            => 'shivbodh_child_rest_petition_sign',
        'permission_callback' => function () {
            return is_user_logged_in() || wp_verify_nonce( $_REQUEST['nonce'] ?? '', 'shivbodh_nonce' );
        },
        'args' => array(
            'petition_id' => array( 'required' => true,  'type' => 'integer' ),
            'name'        => array( 'required' => true,  'type' => 'string'  ),
            'email'       => array( 'required' => true,  'type' => 'string'  ),
        ),
    ) );

    register_rest_route( 'sb/v1', '/petition/(?P<slug>[a-z0-9-]+)/signers', array(
        'methods'             => 'GET',
        'callback'            => 'shivbodh_child_rest_petition_signers',
        'permission_callback' => '__return_true',
    ) );
}
add_action( 'rest_api_init', 'shivbodh_child_register_rest_routes' );

function shivbodh_child_rest_petition_sign( WP_REST_Request $req ) {
    $pid   = absint( $req->get_param( 'petition_id' ) );
    $name  = sanitize_text_field( $req->get_param( 'name' ) );
    $email = sanitize_email( $req->get_param( 'email' ) );

    if ( ! $pid || ! $name || ! $email ) {
        return new WP_Error( 'sb_invalid', 'अमान्य अनुरोध', array( 'status' => 400 ) );
    }

    $token = wp_generate_password( 32, false );

    $sig_id = wp_insert_post( array(
        'post_type'   => 'sb_signature',
        'post_status' => 'pending',
        'post_title'  => $name . ' — #' . $pid,
    ) );

    if ( is_wp_error( $sig_id ) ) {
        return new WP_Error( 'sb_db', 'DB error', array( 'status' => 500 ) );
    }

    update_post_meta( $sig_id, 'petition_id',  $pid );
    update_post_meta( $sig_id, 'signer_name',  $name );
    update_post_meta( $sig_id, 'signer_email', $email );
    update_post_meta( $sig_id, 'verification_token', $token );

    $verify_url = add_query_arg(
        array( 'sb_verify' => $token, 'sig' => $sig_id ),
        home_url( '/verify-signature/' )
    );

    wp_mail(
        $email,
        'अपना हस्ताक्षर सत्यापित करें — ShivBodh Trust',
        "नमस्ते {$name},\n\nकृपया अपना हस्ताक्षर सत्यापित करने के लिए नीचे दिए गए लिंक पर क्लिक करें:\n{$verify_url}\n\n— ShivBodh Trust"
    );

    return rest_ensure_response( array(
        'status'  => 'pending_verification',
        'message' => 'सत्यापन लिंक ईमेल पर भेजा गया है।',
    ) );
}

function shivbodh_child_rest_petition_signers( WP_REST_Request $req ) {
    $slug = sanitize_title( $req->get_param( 'slug' ) );

    $posts = get_posts( array(
        'post_type'      => 'sb_petition',
        'name'           => $slug,
        'posts_per_page' => 1,
    ) );

    if ( empty( $posts ) ) {
        return new WP_Error( 'sb_not_found', 'याचिका नहीं मिली', array( 'status' => 404 ) );
    }

    $petition_id = $posts[0]->ID;
    $count       = (int) get_post_meta( $petition_id, 'current_signatures', true );

    return rest_ensure_response( array(
        'petition_id'  => $petition_id,
        'slug'         => $slug,
        'current_count' => $count,
        'verified'     => true,
    ) );
}

/**
 * Verify signature (frontend page hook)
 */
function shivbodh_child_verify_signature_handler() {
    if ( ! isset( $_GET['sb_verify'] ) ) {
        return;
    }
    $token = sanitize_text_field( wp_unslash( $_GET['sb_verify'] ) );
    $sig   = absint( $_GET['sig'] ?? 0 );

    $stored = get_post_meta( $sig, 'verification_token', true );
    if ( ! $stored || ! hash_equals( $stored, $token ) ) {
        return;
    }

    wp_update_post( array( 'ID' => $sig, 'post_status' => 'publish' ) );
    $pid = (int) get_post_meta( $sig, 'petition_id', true );
    $count = (int) get_post_meta( $pid, 'current_signatures', true );
    update_post_meta( $pid, 'current_signatures', $count + 1 );

    wp_safe_redirect( home_url( '/signature-confirmed/' ) );
    exit;
}
add_action( 'template_redirect', 'shivbodh_child_verify_signature_handler' );

/**
 * AJAX: load more shlokas (footer widget)
 */
function shivbodh_child_ajax_shloka() {
    check_ajax_referer( 'shivbodh_nonce', 'nonce' );

    $shlokas = get_posts( array(
        'post_type'      => 'sb_shloka',
        'posts_per_page' => 1,
        'orderby'        => 'rand',
    ) );

    if ( empty( $shlokas ) ) {
        wp_send_json_error( 'कोई श्लोक उपलब्ध नहीं है।' );
    }

    wp_send_json_success( array(
        'title'   => $shlokas[0]->post_title,
        'content' => wpautop( $shlokas[0]->post_content ),
    ) );
}
add_action( 'wp_ajax_sb_shloka',        'shivbodh_child_ajax_shloka' );
add_action( 'wp_ajax_nopriv_sb_shloka', 'shivbodh_child_ajax_shloka' );
