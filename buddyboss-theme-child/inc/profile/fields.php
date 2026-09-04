<?php
/**
 * Profile: Custom fields + BuddyBoss integration
 *
 * Adds Sanatan-specific fields to user profiles:
 *   - gotra       (e.g. "Kashyap", "Bharadwaj")
 *   - sampradaya  (e.g. "Smarta", "Vaishnava", "Shaiva")
 *   - location    (city, state, country)
 *   - bio         (short intro)
 *   - sampreshan profile completeness (computed)
 *
 * All fields are stored as user meta. BuddyBoss `xprofile` is the preferred
 * store when available; otherwise we fall back to plain user meta.
 *
 * @package SampreShan_Child
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! function_exists( 'sp_profile_field_keys' ) ) {
    function sp_profile_field_keys() {
        return array(
            'gotra'      => 'sp_gotra',
            'sampradaya' => 'sp_sampradaya',
            'location'   => 'sp_location',
            'bio'        => 'sp_bio',
        );
    }
}

if ( ! function_exists( 'sp_profile_get_field' ) ) {
    /**
     * Get a single field for a user. Returns '' when missing.
     */
    function sp_profile_get_field( $field, $user_id = 0 ) {
        $keys = sp_profile_field_keys();
        if ( ! isset( $keys[ $field ] ) ) { return ''; }
        $user_id = (int) ( $user_id ?: get_current_user_id() );
        if ( $user_id <= 0 ) { return ''; }
        return (string) get_user_meta( $user_id, $keys[ $field ], true );
    }
}

if ( ! function_exists( 'sp_profile_set_field' ) ) {
    /**
     * Update a field for the current user (or a specified user with caps).
     */
    function sp_profile_set_field( $field, $value, $user_id = 0 ) {
        $keys = sp_profile_field_keys();
        if ( ! isset( $keys[ $field ] ) ) { return false; }
        $user_id = (int) ( $user_id ?: get_current_user_id() );
        if ( $user_id <= 0 ) { return false; }
        if ( $user_id !== get_current_user_id() && ! current_user_can( 'edit_user', $user_id ) ) {
            return false;
        }
        $value = 'bio' === $field ? sanitize_textarea_field( $value ) : sanitize_text_field( $value );
        return (bool) update_user_meta( $user_id, $keys[ $field ], $value );
    }
}

if ( ! function_exists( 'sp_profile_completeness' ) ) {
    /**
     * Profile completeness percent for a user (0-100).
     * Counts: avatar, display_name, bio, gotra, sampradaya, location, signature count >= 1.
     */
    function sp_profile_completeness( $user_id = 0 ) {
        $user_id = (int) ( $user_id ?: get_current_user_id() );
        if ( $user_id <= 0 ) { return 0; }

        $checks = array(
            'avatar'     => (bool) get_user_meta( $user_id, 'sp_has_avatar', true )
                            || ( function_exists( 'bp_get_user_has_avatar' ) && bp_get_user_has_avatar( $user_id ) )
                            || (bool) get_avatar( $user_id ),
            'name'       => (bool) trim( get_the_author_meta( 'display_name', $user_id ) ),
            'bio'        => (bool) trim( sp_profile_get_field( 'bio', $user_id ) ),
            'gotra'      => (bool) trim( sp_profile_get_field( 'gotra', $user_id ) ),
            'sampradaya' => (bool) trim( sp_profile_get_field( 'sampradaya', $user_id ) ),
            'location'   => (bool) trim( sp_profile_get_field( 'location', $user_id ) ),
            'signed_one' => false,
        );
        // Has signed at least one petition?
        if ( function_exists( 'sp_petitions_table' ) ) {
            global $wpdb;
            $checks['signed_one'] = (bool) $wpdb->get_var( $wpdb->prepare(
                "SELECT id FROM " . sp_petitions_table() . " WHERE user_id = %d LIMIT 1",
                $user_id
            ) );
        }
        $total = count( $checks );
        $have  = count( array_filter( $checks ) );
        return (int) round( ( $have / max( 1, $total ) ) * 100 );
    }
}

if ( ! function_exists( 'sp_profile_user_stats' ) ) {
    /**
     * Aggregate stats for a user: petitions started, signatures given, supporters of their petitions.
     */
    function sp_profile_user_stats( $user_id = 0 ) {
        $user_id = (int) ( $user_id ?: get_current_user_id() );
        if ( $user_id <= 0 ) { return array(); }
        global $wpdb;

        $petitions_started = (int) $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'petition' AND post_author = %d AND post_status = 'publish'",
            $user_id
        ) );

        $signatures_given = 0;
        if ( function_exists( 'sp_petitions_table' ) ) {
            $signatures_given = (int) $wpdb->get_var( $wpdb->prepare(
                "SELECT COUNT(*) FROM " . sp_petitions_table() . " WHERE user_id = %d",
                $user_id
            ) );
        }

        // Supporters = unique signers of petitions this user has authored.
        $supporters_received = (int) $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(DISTINCT s.user_id)
               FROM " . sp_petitions_table() . " s
               INNER JOIN {$wpdb->posts} p ON p.ID = s.petition_id
              WHERE p.post_author = %d",
            $user_id
        ) );

        return array(
            'petitions_started'  => $petitions_started,
            'signatures_given'   => $signatures_given,
            'supporters_received'=> $supporters_received,
        );
    }
}

if ( ! function_exists( 'sp_profile_register_rest_fields' ) ) {
    /**
     * Expose the custom fields on the WP REST API user endpoint.
     */
    function sp_profile_register_rest_fields() {
        $keys = sp_profile_field_keys();
        foreach ( array_keys( $keys ) as $field ) {
            register_rest_field( 'user', 'sampreshan_' . $field, array(
                'get_callback' => function ( $user_data ) use ( $field ) {
                    return sp_profile_get_field( $field, (int) $user_data['id'] );
                },
                'update_callback' => function ( $value, $user_object ) use ( $field ) {
                    return sp_profile_set_field( $field, $value, (int) $user_object->ID );
                },
                'schema' => array(
                    'type'        => 'string',
                    'description' => ucfirst( $field ),
                    'context'     => array( 'view', 'edit' ),
                ),
            ) );
        }
        register_rest_field( 'user', 'sampreshan_completeness', array(
            'get_callback' => function ( $user_data ) {
                return sp_profile_completeness( (int) $user_data['id'] );
            },
            'schema' => array(
                'type'        => 'integer',
                'description' => 'Profile completeness percent',
                'context'     => array( 'view' ),
            ),
        ) );
    }
    add_action( 'rest_api_init', 'sp_profile_register_rest_fields' );
}

if ( ! function_exists( 'sp_profile_user_edit_admin_fields' ) ) {
    /**
     * Show the custom fields on the wp-admin user-edit screen.
     */
    function sp_profile_user_edit_admin_fields( $user ) {
        if ( ! current_user_can( 'edit_user', $user->ID ) ) { return; }
        $keys = sp_profile_field_keys();
        ?>
        <h2 id="sampreshan-profile"><?php esc_html_e( 'Sanatan Profile', 'sampreshan-child' ); ?></h2>
        <table class="form-table">
            <tr>
                <th><label for="sp_gotra"><?php esc_html_e( 'Gotra', 'sampreshan-child' ); ?></label></th>
                <td><input type="text" name="sp_gotra" id="sp_gotra" value="<?php echo esc_attr( get_user_meta( $user->ID, $keys['gotra'], true ) ); ?>" class="regular-text" placeholder="<?php esc_attr_e( 'e.g. Kashyap, Bharadwaj', 'sampreshan-child' ); ?>" /></td>
            </tr>
            <tr>
                <th><label for="sp_sampradaya"><?php esc_html_e( 'Sampradaya', 'sampreshan-child' ); ?></label></th>
                <td>
                    <select name="sp_sampradaya" id="sp_sampradaya">
                        <?php
                        $opts = array( '', 'Smarta', 'Vaishnava', 'Shaiva', 'Shakta', 'Ganapatya', 'Saura' );
                        $current = get_user_meta( $user->ID, $keys['sampradaya'], true );
                        foreach ( $opts as $o ) {
                            printf( '<option value="%s" %s>%s</option>', esc_attr( $o ), selected( $current, $o, false ), esc_html( $o ?: '—' ) );
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="sp_location"><?php esc_html_e( 'Location', 'sampreshan-child' ); ?></label></th>
                <td><input type="text" name="sp_location" id="sp_location" value="<?php echo esc_attr( get_user_meta( $user->ID, $keys['location'], true ) ); ?>" class="regular-text" placeholder="<?php esc_attr_e( 'City, State, Country', 'sampreshan-child' ); ?>" /></td>
            </tr>
            <tr>
                <th><label for="sp_bio"><?php esc_html_e( 'Bio', 'sampreshan-child' ); ?></label></th>
                <td><textarea name="sp_bio" id="sp_bio" rows="4" class="large-text"><?php echo esc_textarea( get_user_meta( $user->ID, $keys['bio'], true ) ); ?></textarea></td>
            </tr>
        </table>
        <?php
    }
    add_action( 'show_user_profile', 'sp_profile_user_edit_admin_fields' );
    add_action( 'edit_user_profile', 'sp_profile_user_edit_admin_fields' );

    function sp_profile_save_admin_fields( $user_id ) {
        if ( ! current_user_can( 'edit_user', $user_id ) ) { return false; }
        $keys = sp_profile_field_keys();
        foreach ( $keys as $field => $meta_key ) {
            if ( ! isset( $_POST[ $meta_key ] ) ) { continue; }
            $val = wp_unslash( $_POST[ $meta_key ] );
            $val = 'sp_bio' === $meta_key ? sanitize_textarea_field( $val ) : sanitize_text_field( $val );
            update_user_meta( $user_id, $meta_key, $val );
        }
    }
    add_action( 'personal_options_update', 'sp_profile_save_admin_fields' );
    add_action( 'edit_user_profile_update', 'sp_profile_save_admin_fields' );
}
