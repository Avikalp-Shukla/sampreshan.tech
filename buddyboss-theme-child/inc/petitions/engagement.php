<?php
/**
 * Petitions: Saved bookmarks + starter updates.
 *
 * Saved (Change.org "Saved" parity): any logged-in user can bookmark a
 * petition. Stored as a capped ID list in `sp_saved_petitions` user meta
 * (fast reads, no extra table). Unpublishing auto-hides items because
 * listings query publish-only.
 *
 * Updates (Change.org parity): the petition starter (or anyone who can
 * edit the petition) posts timestamped updates. Stored newest-first in
 * `sp_updates` post meta (capped at 50), rendered as a timeline on the
 * single petition page.
 *
 * AJAX endpoints (logged-in users):
 *   wp_ajax_sp_save_petition
 *   wp_ajax_sp_unsave_petition
 *   wp_ajax_sp_post_update
 *
 * @package SampreShan_Child
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! function_exists( 'sp_get_saved_petition_ids' ) ) {
    /**
     * Saved petition IDs for a user, newest first. Always ints.
     */
    function sp_get_saved_petition_ids( $user_id = 0 ) {
        $user_id = (int) ( $user_id ?: get_current_user_id() );
        if ( $user_id <= 0 ) { return array(); }
        $ids = get_user_meta( $user_id, 'sp_saved_petitions', true );
        if ( ! is_array( $ids ) ) { return array(); }
        return array_values( array_unique( array_filter( array_map( 'intval', $ids ) ) ) );
    }
}

if ( ! function_exists( 'sp_is_petition_saved' ) ) {
    function sp_is_petition_saved( $petition_id, $user_id = 0 ) {
        return in_array( (int) $petition_id, sp_get_saved_petition_ids( $user_id ), true );
    }
}

if ( ! function_exists( 'sp_set_petition_saved' ) ) {
    /**
     * Save or unsave. Returns true when the stored state changed.
     */
    function sp_set_petition_saved( $petition_id, $save, $user_id = 0 ) {
        $petition_id = (int) $petition_id;
        $user_id     = (int) ( $user_id ?: get_current_user_id() );
        if ( $petition_id <= 0 || $user_id <= 0 ) { return false; }
        if ( 'petition' !== get_post_type( $petition_id ) ) { return false; }
        $ids    = sp_get_saved_petition_ids( $user_id );
        $exists = in_array( $petition_id, $ids, true );
        if ( $save && ! $exists ) {
            array_unshift( $ids, $petition_id );
            update_user_meta( $user_id, 'sp_saved_petitions', array_slice( $ids, 0, 200 ) );
            return true;
        }
        if ( ! $save && $exists ) {
            update_user_meta( $user_id, 'sp_saved_petitions', array_values( array_diff( $ids, array( $petition_id ) ) ) );
            return true;
        }
        return false;
    }
}

if ( ! function_exists( 'sp_ajax_save_petition' ) ) {
    function sp_ajax_save_petition() {
        check_ajax_referer( 'sp_petition_sign', 'nonce' );
        if ( ! is_user_logged_in() ) {
            wp_send_json_error( array( 'message' => __( 'You must be logged in to save.', 'sampreshan-child' ) ), 401 );
        }
        $petition_id = isset( $_POST['petition_id'] ) ? (int) $_POST['petition_id'] : 0;
        sp_set_petition_saved( $petition_id, true );
        wp_send_json_success( array( 'saved' => true, 'message' => __( 'Saved to your list.', 'sampreshan-child' ) ) );
    }
    add_action( 'wp_ajax_sp_save_petition', 'sp_ajax_save_petition' );
}

if ( ! function_exists( 'sp_ajax_unsave_petition' ) ) {
    function sp_ajax_unsave_petition() {
        check_ajax_referer( 'sp_petition_sign', 'nonce' );
        if ( ! is_user_logged_in() ) {
            wp_send_json_error( array( 'message' => __( 'You must be logged in.', 'sampreshan-child' ) ), 401 );
        }
        $petition_id = isset( $_POST['petition_id'] ) ? (int) $_POST['petition_id'] : 0;
        sp_set_petition_saved( $petition_id, false );
        wp_send_json_success( array( 'saved' => false, 'message' => __( 'Removed from your list.', 'sampreshan-child' ) ) );
    }
    add_action( 'wp_ajax_sp_unsave_petition', 'sp_ajax_unsave_petition' );
}

if ( ! function_exists( 'sp_get_petition_updates' ) ) {
    /**
     * Starter updates, newest first. Each: array( text, time, author ).
     */
    function sp_get_petition_updates( $petition_id ) {
        $petition_id = (int) $petition_id;
        if ( $petition_id <= 0 ) { return array(); }
        $updates = get_post_meta( $petition_id, 'sp_updates', true );
        return is_array( $updates ) ? array_values( $updates ) : array();
    }
}

if ( ! function_exists( 'sp_can_post_petition_update' ) ) {
    function sp_can_post_petition_update( $petition_id, $user_id = 0 ) {
        $petition_id = (int) $petition_id;
        $user_id     = (int) ( $user_id ?: get_current_user_id() );
        if ( $petition_id <= 0 || $user_id <= 0 ) { return false; }
        $post = get_post( $petition_id );
        if ( ! $post || 'petition' !== $post->post_type || 'publish' !== $post->post_status ) { return false; }
        return ( (int) $post->post_author === $user_id ) || current_user_can( 'edit_petition', $petition_id );
    }
}

if ( ! function_exists( 'sp_ajax_post_petition_update' ) ) {
    function sp_ajax_post_petition_update() {
        check_ajax_referer( 'sp_petition_sign', 'nonce' );
        if ( ! is_user_logged_in() ) {
            wp_send_json_error( array( 'message' => __( 'You must be logged in.', 'sampreshan-child' ) ), 401 );
        }
        $petition_id = isset( $_POST['petition_id'] ) ? (int) $_POST['petition_id'] : 0;
        $text        = isset( $_POST['text'] ) ? sanitize_textarea_field( wp_unslash( $_POST['text'] ) ) : '';
        $text        = trim( preg_replace( '/\s+/', ' ', $text ) );
        if ( mb_strlen( $text ) < 10 ) {
            wp_send_json_error( array( 'message' => __( 'Please write at least a sentence (10 characters).', 'sampreshan-child' ) ), 400 );
        }
        if ( mb_strlen( $text ) > 1000 ) {
            wp_send_json_error( array( 'message' => __( 'Keep updates under 1000 characters.', 'sampreshan-child' ) ), 400 );
        }
        if ( ! sp_can_post_petition_update( $petition_id, get_current_user_id() ) ) {
            wp_send_json_error( array( 'message' => __( 'Only the petition starter can post updates.', 'sampreshan-child' ) ), 403 );
        }
        $updates = sp_get_petition_updates( $petition_id );
        array_unshift( $updates, array(
            'text'   => $text,
            'time'   => current_time( 'mysql' ),
            'author' => get_current_user_id(),
        ) );
        update_post_meta( $petition_id, 'sp_updates', array_slice( $updates, 0, 50 ) );
        do_action( 'sampreshan_petition_updated', $petition_id, get_current_user_id() );
        wp_send_json_success( array(
            'message' => __( 'Update posted.', 'sampreshan-child' ),
            'update'  => array(
                'text' => $text,
                'time' => current_time( 'mysql' ),
            ),
        ) );
    }
    add_action( 'wp_ajax_sp_post_update', 'sp_ajax_post_petition_update' );
}
