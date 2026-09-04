<?php
/**
 * Petitions: Custom Post Type + Taxonomy + Roles
 *
 * Registers the `petition` CPT, the `cause_category` taxonomy,
 * the `petitioner` custom role, and on activation installs the
 * persistent database tables used by the signature store.
 *
 * @package SampreShan_Child
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! function_exists( 'sp_register_petition_cpt' ) ) {
    /**
     * Register the `petition` custom post type.
     */
    function sp_register_petition_cpt() {
        $labels = array(
            'name'                  => _x( 'Petitions', 'post type general name', 'sampreshan-child' ),
            'singular_name'         => _x( 'Petition', 'post type singular name', 'sampreshan-child' ),
            'menu_name'             => _x( 'Petitions', 'admin menu', 'sampreshan-child' ),
            'name_admin_bar'        => _x( 'Petition', 'add new on admin bar', 'sampreshan-child' ),
            'add_new'               => __( 'Add New Petition', 'sampreshan-child' ),
            'add_new_item'          => __( 'Add New Petition', 'sampreshan-child' ),
            'new_item'              => __( 'New Petition', 'sampreshan-child' ),
            'edit_item'             => __( 'Edit Petition', 'sampreshan-child' ),
            'view_item'             => __( 'View Petition', 'sampreshan-child' ),
            'view_items'            => __( 'View Petitions', 'sampreshan-child' ),
            'all_items'             => __( 'All Petitions', 'sampreshan-child' ),
            'search_items'          => __( 'Search Petitions', 'sampreshan-child' ),
            'not_found'             => __( 'No petitions found', 'sampreshan-child' ),
            'not_found_in_trash'    => __( 'No petitions found in trash', 'sampreshan-child' ),
            'featured_image'        => __( 'Cover Image', 'sampreshan-child' ),
            'set_featured_image'    => __( 'Set cover image', 'sampreshan-child' ),
            'remove_featured_image' => __( 'Remove cover image', 'sampreshan-child' ),
            'archives'              => __( 'Petition archive', 'sampreshan-child' ),
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'show_in_rest'       => true,            // Gutenberg + REST API
            'rest_base'          => 'petitions',
            'rest_namespace'     => 'sampreshan/v1',
            'menu_icon'          => 'dashicons-megaphone',
            'menu_position'      => 5,
            'supports'           => array( 'title', 'editor', 'excerpt', 'thumbnail', 'comments', 'author', 'revisions', 'custom-fields' ),
            // No CPT archive: the /petitions/ URL belongs to the "Petitions"
            // page (template-petitions.php). An archive slug here would win
            // the rewrite match (petitions/?$ => post_type=petition) and
            // hijack the page. Singles stay at /petition/<slug>/.
            'has_archive'        => false,
            'rewrite'            => array(
                'slug'       => 'petition',
                'with_front' => false,
            ),
            'capability_type'    => array( 'petition', 'petitions' ),
            'map_meta_cap'       => true,
            'show_in_nav_menus'   => true,
            'show_in_admin_bar'   => true,
            'can_export'          => true,
        );

        register_post_type( 'petition', $args );
    }
    add_action( 'init', 'sp_register_petition_cpt' );
}

if ( ! function_exists( 'sp_register_cause_taxonomy' ) ) {
    /**
     * Register the `cause_category` custom taxonomy.
     */
    function sp_register_cause_taxonomy() {
        $labels = array(
            'name'                       => _x( 'Cause Categories', 'taxonomy general name', 'sampreshan-child' ),
            'singular_name'              => _x( 'Cause Category', 'taxonomy singular name', 'sampreshan-child' ),
            'menu_name'                  => __( 'Categories', 'sampreshan-child' ),
            'all_items'                  => __( 'All Categories', 'sampreshan-child' ),
            'edit_item'                  => __( 'Edit Category', 'sampreshan-child' ),
            'add_new_item'               => __( 'Add New Category', 'sampreshan-child' ),
            'new_item_name'              => __( 'New Category Name', 'sampreshan-child' ),
            'search_items'               => __( 'Search Categories', 'sampreshan-child' ),
            'popular_items'              => __( 'Popular Categories', 'sampreshan-child' ),
            'separate_items_with_commas' => __( 'Separate categories with commas', 'sampreshan-child' ),
        );

        register_taxonomy( 'cause_category', array( 'petition' ), array(
            'labels'            => $labels,
            'public'            => true,
            'show_in_rest'      => true,
            'rest_base'         => 'cause-categories',
            'hierarchical'      => true,
            'show_admin_column' => true,
            'rewrite'           => array(
                'slug'         => 'cause',
                'with_front'   => false,
                'hierarchical' => false,
            ),
        ) );
    }
    add_action( 'init', 'sp_register_cause_taxonomy' );
}

if ( ! function_exists( 'sp_register_default_cause_terms' ) ) {
    /**
     * Seed the default cause categories the first time the taxonomy registers
     * with an empty term set. Safe to re-run; only inserts missing terms.
     */
    function sp_register_default_cause_terms() {
        $defaults = array(
            'Temple Preservation',
            'Cultural Heritage',
            'Religious Education',
            'Community Welfare',
            'Environmental',
            'Gau Seva',
            'Sanskrit & Vedic Studies',
            'Pilgrimage & Tirtha',
        );
        foreach ( $defaults as $name ) {
            if ( ! term_exists( $name, 'cause_category' ) ) {
                wp_insert_term( $name, 'cause_category' );
            }
        }
    }
    add_action( 'init', 'sp_register_default_cause_terms', 20 );
}

if ( ! function_exists( 'sp_register_petitioner_role' ) ) {
    /**
     * Add the `petitioner` role on activation. Same baseline as subscriber,
     * plus the ability to create petitions.
     */
    function sp_register_petitioner_role() {
        if ( ! get_role( 'petitioner' ) ) {
            $subscriber = get_role( 'subscriber' );
            $caps = $subscriber ? $subscriber->capabilities : array( 'read' => true );
            $caps['read_petitions']         = true;
            $caps['edit_petition']          = true;
            $caps['edit_petitions']         = true;
            $caps['edit_others_petitions']   = true;   // community moderation
            $caps['publish_petitions']      = true;
            $caps['delete_petition']        = true;
            $caps['delete_petitions']       = true;
            $caps['read_private_petitions'] = true;
            $caps['sign_petitions']         = true;   // custom cap for the signature handler
            add_role( 'petitioner', __( 'Petitioner', 'sampreshan-child' ), $caps );
        }
    }
    add_action( 'init', 'sp_register_petitioner_role', 1 );
}

if ( ! function_exists( 'sp_grant_petitioner_caps_to_admins' ) ) {
    /**
     * Make sure admins and editors also get the petition caps.
     * Without this, CPTs with `capability_type=petition` would be invisible
     * to anyone except the role we explicitly registered.
     */
    function sp_grant_petitioner_caps_to_admins() {
        $caps = array(
            'edit_petition', 'edit_petitions', 'edit_others_petitions',
            'publish_petitions', 'read_petition', 'read_private_petitions',
            'delete_petition', 'delete_petitions', 'delete_others_petitions',
            'edit_published_petitions', 'delete_published_petitions',
            'manage_petition_terms', 'edit_petition_terms', 'delete_petition_terms',
            'assign_petition_terms', 'sign_petitions',
        );
        foreach ( array( 'administrator', 'editor' ) as $role_name ) {
            $role = get_role( $role_name );
            if ( $role ) {
                foreach ( $caps as $c ) {
                    $role->add_cap( $c );
                }
            }
        }
    }
    add_action( 'init', 'sp_grant_petitioner_caps_to_admins', 2 );
}

if ( ! function_exists( 'sp_auto_promote_subscriber_to_petitioner' ) ) {
    /**
     * When a user signs their first petition, promote them to `petitioner`
     * so they can start their own causes without leaving the site.
     */
    function sp_auto_promote_subscriber_to_petitioner( $user_id ) {
        $user = get_user_by( 'id', $user_id );
        if ( ! $user ) { return; }
        if ( in_array( 'petitioner', (array) $user->roles, true ) ) { return; }
        if ( array_intersect( array( 'administrator', 'editor', 'author', 'contributor' ), (array) $user->roles ) ) {
            return; // already elevated
        }
        $user->set_role( 'petitioner' );
    }
    // Hooked from the signature handler.
}

if ( ! function_exists( 'sp_install_petition_tables' ) ) {
    /**
     * Create the persistent tables:
     *   - wp_sampreshan_signatures  : per-user signatures (one row per user per petition)
     *   - wp_sampreshan_signature_log : append-only audit log
     * Idempotent — uses dbDelta.
     */
    function sp_install_petition_tables() {
        global $wpdb;
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $charset_collate = $wpdb->get_charset_collate();

        $signatures = $wpdb->prefix . 'sampreshan_signatures';
        $sql1 = "CREATE TABLE $signatures (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            petition_id BIGINT UNSIGNED NOT NULL,
            user_id BIGINT UNSIGNED NOT NULL,
            user_ip VARBINARY(16) DEFAULT NULL,
            user_agent VARCHAR(255) DEFAULT '',
            display_name VARCHAR(100) DEFAULT '',
            comment TEXT DEFAULT NULL,
            is_anonymous TINYINT(1) NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY uniq_user_petition (petition_id, user_id),
            KEY idx_petition (petition_id),
            KEY idx_user (user_id),
            KEY idx_created (created_at)
        ) $charset_collate;";

        $log = $wpdb->prefix . 'sampreshan_signature_log';
        $sql2 = "CREATE TABLE $log (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            petition_id BIGINT UNSIGNED NOT NULL,
            user_id BIGINT UNSIGNED NOT NULL,
            action VARCHAR(20) NOT NULL,
            meta TEXT DEFAULT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY idx_petition_action (petition_id, action),
            KEY idx_user (user_id)
        ) $charset_collate;";

        dbDelta( $sql1 );
        dbDelta( $sql2 );

        update_option( 'sampreshan_db_version', '1.0.0' );
    }
}
