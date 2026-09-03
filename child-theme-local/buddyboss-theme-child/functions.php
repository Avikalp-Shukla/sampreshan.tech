<?php
/**
 * SampreShan Child Theme functions and definitions
 *
 * @package SampreShan_Child
 * @since   1.0.0
 */

if ( ! defined( \"ABSPATH\" ) ) {
    exit;
}

/**
 * Enqueue parent theme styles + child theme styles
 */
function sampreshan_child_enqueue_styles() {
    // Parent theme main stylesheet
    wp_enqueue_style(
        \"buddyboss-theme-parent\",
        get_template_directory_uri() . \"/style.css\",
        array(),
        wp_get_theme( \"buddyboss-theme\" )->get( \"Version\" )
    );

    // Child theme main stylesheet (loads AFTER parent)
    wp_enqueue_style(
        \"buddyboss-theme-child\",
        get_stylesheet_directory_uri() . \"/style.css\",
        array( \"buddyboss-theme-parent\" ),
        wp_get_theme()->get( \"Version\" )
    );
}
add_action( \"wp_enqueue_scripts\", \"sampreshan_child_enqueue_styles\", 20 );

/**
 * Child theme setup
 */
function sampreshan_child_setup() {
    // Load child theme textdomain for translations
    load_child_theme_textdomain( \"sampreshan-child\", get_stylesheet_directory() . \"/languages\" );
}
add_action( \"after_setup_theme\", \"sampreshan_child_setup\" );

/**
 * Add custom theme support (extend BuddyBoss parent)
 */
function sampreshan_child_theme_support() {
    // Add any custom theme supports here
    // e.g. add_theme_support( \"post-thumbnails\" );
}
add_action( \"after_setup_theme\", \"sampreshan_child_theme_support\" );
