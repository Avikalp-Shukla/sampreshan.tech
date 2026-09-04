<?php
/**
 * Petitions: Signature store + AJAX handler
 *
 * The signatures table (`wp_sampreshan_signatures`) holds one row per
 * user per petition (UNIQUE constraint), so signing twice is a no-op.
 * The signature count is always derived from this table — never from
 * post meta — so the UI and the count can never drift.
 *
 * Exposes:
 *   sp_petition_signature_count( $petition_id, $distinct = true )
 *   sp_petition_user_has_signed( $petition_id, $user_id )
 *   sp_petition_add_signature( $args )       returns int|false (id or false)
 *   sp_petition_remove_signature( $petition_id, $user_id )
 *   sp_petition_get_signatures( $args )      paginated list
 *
 * AJAX endpoints (logged-in users):
 *   wp_ajax_sp_sign_petition
 *   wp_ajax_sp_unsign_petition
 *
 * @package SampreShan_Child
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! function_exists( 'sp_petitions_table' ) ) {
    function sp_petitions_table() {
        global $wpdb;
        return $wpdb->prefix . 'sampreshan_signatures';
    }
}

if ( ! function_exists( 'sp_petition_log_table' ) ) {
    function sp_petition_log_table() {
        global $wpdb;
        return $wpdb->prefix . 'sampreshan_signature_log';
    }
}

if ( ! function_exists( 'sp_petition_signature_count' ) ) {
    /**
     * Count signatures on a petition. `distinct=true` is the default and
     * returns the number of unique user signatures.
     */
    function sp_petition_signature_count( $petition_id, $distinct = true ) {
        global $wpdb;
        $petition_id = (int) $petition_id;
        if ( $petition_id <= 0 ) { return 0; }
        $sql = $distinct
            ? "SELECT COUNT(*) FROM " . sp_petitions_table() . " WHERE petition_id = %d"
            : "SELECT COUNT(*) FROM " . sp_petitions_table() . " WHERE petition_id = %d";
        return (int) $wpdb->get_var( $wpdb->prepare( $sql, $petition_id ) );
    }
}

if ( ! function_exists( 'sp_petition_user_has_signed' ) ) {
    function sp_petition_user_has_signed( $petition_id, $user_id = 0 ) {
        global $wpdb;
        $user_id = $user_id ?: get_current_user_id();
        if ( $user_id <= 0 ) { return false; }
        $row = $wpdb->get_var( $wpdb->prepare(
            "SELECT id FROM " . sp_petitions_table() . " WHERE petition_id = %d AND user_id = %d LIMIT 1",
            $petition_id, $user_id
        ) );
        return ! empty( $row );
    }
}

if ( ! function_exists( 'sp_petition_add_signature' ) ) {
    /**
     * Insert a signature. Returns the row id, or false on failure (including
     * duplicate — the UNIQUE index makes a second call a no-op).
     */
    function sp_petition_add_signature( $args ) {
        global $wpdb;

        $defaults = array(
            'petition_id'  => 0,
            'user_id'      => get_current_user_id(),
            'comment'      => '',
            'is_anonymous' => 0,
        );
        $args = wp_parse_args( $args, $defaults );

        $petition_id = (int) $args['petition_id'];
        $user_id     = (int) $args['user_id'];
        if ( $petition_id <= 0 || $user_id <= 0 ) { return false; }
        if ( 'petition' !== get_post_type( $petition_id ) ) { return false; }
        if ( sp_petition_user_has_signed( $petition_id, $user_id ) ) { return false; }

        $ip_binary = ip2binary( isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : '' );

        $inserted = $wpdb->insert(
            sp_petitions_table(),
            array(
                'petition_id'  => $petition_id,
                'user_id'      => $user_id,
                'user_ip'      => $ip_binary,
                'user_agent'   => substr( (string) ( $_SERVER['HTTP_USER_AGENT'] ?? '' ), 0, 250 ),
                'display_name' => get_the_author_meta( 'display_name', $user_id ),
                'comment'      => sanitize_textarea_field( $args['comment'] ),
                'is_anonymous' => ! empty( $args['is_anonymous'] ) ? 1 : 0,
            ),
            array( '%d', '%d', '%s', '%s', '%s', '%s', '%d' )
        );

        if ( false === $inserted ) { return false; }

        $id = (int) $wpdb->insert_id;

        // Append to the audit log.
        $wpdb->insert( sp_petition_log_table(), array(
            'petition_id' => $petition_id,
            'user_id'     => $user_id,
            'action'      => 'sign',
            'meta'        => wp_json_encode( array( 'sig_id' => $id ) ),
        ), array( '%d', '%d', '%s', '%s' ) );

        // Auto-promote subscribers to petitioners.
        if ( function_exists( 'sp_auto_promote_subscriber_to_petitioner' ) ) {
            sp_auto_promote_subscriber_to_petitioner( $user_id );
        }

        // Notify the petition author (asynchronously via wp_schedule_single_event
        // so the AJAX response stays fast).
        if ( function_exists( 'sp_petition_queue_signature_email' ) ) {
            sp_petition_queue_signature_email( $petition_id, $user_id, $id );
        }

        // Fire action for downstream listeners (BuddyBoss activity, etc.).
        do_action( 'sampreshan_petition_signed', $petition_id, $user_id, $id );

        return $id;
    }
}

if ( ! function_exists( 'ip2binary' ) ) {
    /**
     * Convert an IPv4 or IPv6 string to its 16-byte packed form.
     * IPv4 addresses are mapped into the IPv4-in-IPv6 range for storage.
     */
    function ip2binary( $ip ) {
        if ( ! $ip ) { return null; }
        $packed = @inet_pton( $ip );
        if ( false === $packed ) { return null; }
        // Pad IPv4 (4 bytes) to 16 bytes so the VARBINARY(16) column always holds a full address.
        if ( strlen( $packed ) === 4 ) {
            $packed = "\0\0\0\0\0\0\0\0\0\0\xff\xff" . $packed;
        }
        return $packed;
    }
}

if ( ! function_exists( 'sp_petition_remove_signature' ) ) {
    function sp_petition_remove_signature( $petition_id, $user_id = 0 ) {
        global $wpdb;
        $user_id = $user_id ?: get_current_user_id();
        if ( $user_id <= 0 ) { return false; }
        $deleted = $wpdb->delete( sp_petitions_table(), array(
            'petition_id' => (int) $petition_id,
            'user_id'     => (int) $user_id,
        ), array( '%d', '%d' ) );
        if ( $deleted ) {
            $wpdb->insert( sp_petition_log_table(), array(
                'petition_id' => (int) $petition_id,
                'user_id'     => (int) $user_id,
                'action'      => 'unsign',
            ), array( '%d', '%d', '%s' ) );
            do_action( 'sampreshan_petition_unsigned', $petition_id, $user_id );
        }
        return (bool) $deleted;
    }
}

if ( ! function_exists( 'sp_petition_get_signatures' ) ) {
    function sp_petition_get_signatures( $args = array() ) {
        global $wpdb;
        $defaults = array(
            'petition_id' => 0,
            'per_page'    => 20,
            'page'        => 1,
            'order'       => 'DESC',
        );
        $args = wp_parse_args( $args, $defaults );
        $petition_id = (int) $args['petition_id'];
        $per_page    = max( 1, min( 100, (int) $args['per_page'] ) );
        $page        = max( 1, (int) $args['page'] );
        $offset      = ( $page - 1 ) * $per_page;
        $order       = 'DESC' === strtoupper( $args['order'] ) ? 'DESC' : 'ASC';

        return $wpdb->get_results( $wpdb->prepare(
            "SELECT s.*, u.display_name AS user_display, u.user_email
               FROM " . sp_petitions_table() . " s
               LEFT JOIN {$wpdb->users} u ON u.ID = s.user_id
              WHERE s.petition_id = %d
              ORDER BY s.created_at $order
              LIMIT %d OFFSET %d",
            $petition_id, $per_page, $offset
        ) );
    }
}

/* ============================================================
   AJAX handlers
   ============================================================ */

if ( ! function_exists( 'sp_ajax_sign_petition' ) ) {
    function sp_ajax_sign_petition() {
        check_ajax_referer( 'sp_petition_sign', 'nonce' );

        if ( ! is_user_logged_in() ) {
            wp_send_json_error( array( 'message' => __( 'You must be logged in to sign.', 'sampreshan-child' ) ), 401 );
        }
        if ( ! current_user_can( 'sign_petitions' ) ) {
            wp_send_json_error( array( 'message' => __( 'Your account cannot sign petitions.', 'sampreshan-child' ) ), 403 );
        }

        $petition_id = isset( $_POST['petition_id'] ) ? (int) $_POST['petition_id'] : 0;
        $comment     = isset( $_POST['comment'] ) ? (string) wp_unslash( $_POST['comment'] ) : '';
        $is_anon     = ! empty( $_POST['is_anonymous'] );

        // Lightweight rate limit: 1 sign per petition per 2s for the same user.
        $lock_key = 'sp_sign_lock_' . get_current_user_id() . '_' . $petition_id;
        if ( get_transient( $lock_key ) ) {
            wp_send_json_error( array( 'message' => __( 'Please wait a moment before signing again.', 'sampreshan-child' ) ), 429 );
        }

        $status = get_post_meta( $petition_id, 'sampreshan_status', true ) ?: 'active';
        if ( 'active' !== $status ) {
            wp_send_json_error( array( 'message' => __( 'This petition is not currently accepting signatures.', 'sampreshan-child' ) ), 400 );
        }

        $id = sp_petition_add_signature( array(
            'petition_id'  => $petition_id,
            'user_id'      => get_current_user_id(),
            'comment'      => $comment,
            'is_anonymous' => $is_anon ? 1 : 0,
        ) );

        if ( ! $id ) {
            wp_send_json_error( array( 'message' => __( 'You have already signed this petition.', 'sampreshan-child' ) ), 409 );
        }

        set_transient( $lock_key, 1, 2 );

        $count = sp_petition_signature_count( $petition_id );
        update_post_meta( $petition_id, 'sampreshan_signatures', $count );

        wp_send_json_success( array(
            'id'      => $id,
            'count'   => $count,
            'message' => __( 'Thank you for your support!', 'sampreshan-child' ),
        ) );
    }
    add_action( 'wp_ajax_sp_sign_petition', 'sp_ajax_sign_petition' );
}

if ( ! function_exists( 'sp_ajax_unsign_petition' ) ) {
    function sp_ajax_unsign_petition() {
        check_ajax_referer( 'sp_petition_sign', 'nonce' );
        if ( ! is_user_logged_in() ) {
            wp_send_json_error( array( 'message' => __( 'You must be logged in.', 'sampreshan-child' ) ), 401 );
        }
        $petition_id = isset( $_POST['petition_id'] ) ? (int) $_POST['petition_id'] : 0;
        $ok = sp_petition_remove_signature( $petition_id, get_current_user_id() );
        if ( ! $ok ) {
            wp_send_json_error( array( 'message' => __( 'You have not signed this petition.', 'sampreshan-child' ) ), 404 );
        }
        $count = sp_petition_signature_count( $petition_id );
        update_post_meta( $petition_id, 'sampreshan_signatures', $count );
        wp_send_json_success( array( 'count' => $count ) );
    }
    add_action( 'wp_ajax_sp_unsign_petition', 'sp_ajax_unsign_petition' );
}

/* ============================================================
   Inline JS data — exposes the nonce and a small REST-ish URL map
   so templates don't have to enqueue anything extra.
   ============================================================ */
if ( ! function_exists( 'sp_petition_localize_data' ) ) {
    function sp_petition_localize_data() {
        wp_localize_script( 'sampreshan-navigation', 'SampreshanPetition', array(
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'sp_petition_sign' ),
            'i18n'    => array(
                'signing'   => __( 'Signing…', 'sampreshan-child' ),
                'signed'    => __( 'Signed', 'sampreshan-child' ),
                'error'     => __( 'Something went wrong. Please try again.', 'sampreshan-child' ),
            ),
        ) );
    }
    add_action( 'wp_enqueue_scripts', 'sp_petition_localize_data' );
}
