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
    define( 'SAMPRESHAN_CHILD_VERSION', '1.0.0' );
}

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

    // Child theme design system
    wp_enqueue_style(
        'sampreshan-design-system',
        get_stylesheet_directory_uri() . '/assets/css/design-system.css',
        array( 'buddyboss-theme-child' ),
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

    // Navigation JS (footer)
    wp_enqueue_script(
        'sampreshan-navigation',
        get_stylesheet_directory_uri() . '/assets/js/navigation.js',
        array(),
        SAMPRESHAN_CHILD_VERSION,
        true
    );
}
add_action( 'wp_enqueue_scripts', 'sampreshan_child_enqueue_styles', 20 );

/**
 * Child theme setup
 */
function sampreshan_child_setup() {
    load_child_theme_textdomain( 'sampreshan-child', get_stylesheet_directory() . '/languages' );
}
add_action( 'after_setup_theme', 'sampreshan_child_setup' );
