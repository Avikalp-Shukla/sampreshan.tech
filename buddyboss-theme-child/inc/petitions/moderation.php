<?php
/**
 * Petitions: Victory declarations + user reports.
 *
 * Victory (Change.org parity): the petition author (or anyone who can edit
 * the petition) marks a finished cause as won. Sets `sampreshan_status`
 * to `victory`, which automatically stops new signatures (the sign
 * handler only accepts `active`) and flips every badge via ucfirst().
 *
 * Reports (trust & safety): any logged-in user can flag a petition once.
 * Reports accumulate in `sp_reports` post meta + `sp_report_count` for
 * the admin list table. No auto-moderation — a human reviews.
 *
 * AJAX endpoints (logged-in users):
 *   wp_ajax_sp_petition_victory
 *   wp_ajax_sp_report_petition
 *
 * @package SampreShan_Child
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! function_exists( 'sp_petition_report_reasons' ) ) {
    /**
     * Fixed allow-list of report reasons (keys stored, labels shown).
     */
    function sp_petition_report_reasons() {
        return array(
            'spam'       => __( 'Spam or scam', 'sampreshan-child' ),
            'misleading' => __( 'Misleading or false claims', 'sampreshan-child' ),
            'hateful'    => __( 'Hateful or abusive content', 'sampreshan-child' ),
            'unsafe'     => __( 'Unsafe or illegal content', 'sampreshan-child' ),
            'other'      => __( 'Something else', 'sampreshan-child' ),
        );
    }
}

if ( ! function_exists( 'sp_petition_can_declare_victory' ) ) {
    /**
     * Only the author (or an editor/admin) of a *published* petition.
     */
    function sp_petition_can_declare_victory( $petition_id, $user_id = 0 ) {
        $petition_id = (int) $petition_id;
        $user_id     = (int) ( $user_id ?: get_current_user_id() );
        if ( $petition_id <= 0 || $user_id <= 0 ) { return false; }
        $post = get_post( $petition_id );
        if ( ! $post || 'petition' !== $post->post_type || 'publish' !== $post->post_status ) { return false; }
        if ( 'victory' === get_post_meta( $petition_id, 'sampreshan_status', true ) ) { return false; }
        return ( (int) $post->post_author === $user_id ) || current_user_can( 'edit_petition', $petition_id );
    }
}

if ( ! function_exists( 'sp_ajax_petition_victory' ) ) {
    function sp_ajax_petition_victory() {
        check_ajax_referer( 'sp_petition_sign', 'nonce' );
        if ( ! is_user_logged_in() ) {
            wp_send_json_error( array( 'message' => __( 'You must be logged in.', 'sampreshan-child' ) ), 401 );
        }
        $petition_id = isset( $_POST['petition_id'] ) ? (int) $_POST['petition_id'] : 0;
        if ( ! sp_petition_can_declare_victory( $petition_id, get_current_user_id() ) ) {
            wp_send_json_error( array( 'message' => __( 'Only the petition starter can declare victory.', 'sampreshan-child' ) ), 403 );
        }
        update_post_meta( $petition_id, 'sampreshan_status', 'victory' );
        update_post_meta( $petition_id, 'sampreshan_victory_at', current_time( 'mysql' ) );
        if ( function_exists( 'sp_petition_log_table' ) ) {
            global $wpdb;
            $wpdb->insert( sp_petition_log_table(), array(
                'petition_id' => $petition_id,
                'user_id'     => get_current_user_id(),
                'action'      => 'victory',
            ), array( '%d', '%d', '%s' ) );
        }
        do_action( 'sampreshan_petition_victory', $petition_id, get_current_user_id() );
        wp_send_json_success( array( 'message' => __( 'Victory declared! Congratulations.', 'sampreshan-child' ) ) );
    }
    add_action( 'wp_ajax_sp_petition_victory', 'sp_ajax_petition_victory' );
}

if ( ! function_exists( 'sp_petition_has_reported' ) ) {
    function sp_petition_has_reported( $petition_id, $user_id = 0 ) {
        $petition_id = (int) $petition_id;
        $user_id     = (int) ( $user_id ?: get_current_user_id() );
        if ( $petition_id <= 0 || $user_id <= 0 ) { return false; }
        $reports = get_post_meta( $petition_id, 'sp_reports', true );
        if ( ! is_array( $reports ) ) { return false; }
        foreach ( $reports as $r ) {
            if ( isset( $r['user_id'] ) && (int) $r['user_id'] === $user_id ) { return true; }
        }
        return false;
    }
}

if ( ! function_exists( 'sp_ajax_report_petition' ) ) {
    function sp_ajax_report_petition() {
        check_ajax_referer( 'sp_petition_sign', 'nonce' );
        if ( ! is_user_logged_in() ) {
            wp_send_json_error( array( 'message' => __( 'You must be logged in to report.', 'sampreshan-child' ) ), 401 );
        }
        $petition_id = isset( $_POST['petition_id'] ) ? (int) $_POST['petition_id'] : 0;
        $reason      = isset( $_POST['reason'] ) ? sanitize_key( wp_unslash( $_POST['reason'] ) ) : '';
        $post = get_post( $petition_id );
        if ( ! $post || 'petition' !== $post->post_type ) {
            wp_send_json_error( array( 'message' => __( 'Petition not found.', 'sampreshan-child' ) ), 404 );
        }
        if ( (int) $post->post_author === get_current_user_id() ) {
            wp_send_json_error( array( 'message' => __( 'You cannot report your own petition.', 'sampreshan-child' ) ), 400 );
        }
        $reasons = sp_petition_report_reasons();
        if ( ! isset( $reasons[ $reason ] ) ) {
            wp_send_json_error( array( 'message' => __( 'Please choose a reason.', 'sampreshan-child' ) ), 400 );
        }
        if ( sp_petition_has_reported( $petition_id, get_current_user_id() ) ) {
            wp_send_json_error( array( 'message' => __( 'You have already reported this petition.', 'sampreshan-child' ) ), 409 );
        }
        $reports = get_post_meta( $petition_id, 'sp_reports', true );
        if ( ! is_array( $reports ) ) { $reports = array(); }
        $reports[] = array(
            'user_id' => get_current_user_id(),
            'reason'  => $reason,
            'time'    => current_time( 'mysql' ),
        );
        update_post_meta( $petition_id, 'sp_reports', $reports );
        update_post_meta( $petition_id, 'sp_report_count', count( $reports ) );
        do_action( 'sampreshan_petition_reported', $petition_id, get_current_user_id(), $reason );
        wp_send_json_success( array( 'message' => __( 'Thanks — our team will review this petition.', 'sampreshan-child' ) ) );
    }
    add_action( 'wp_ajax_sp_report_petition', 'sp_ajax_report_petition' );
}
