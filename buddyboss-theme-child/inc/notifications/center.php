<?php
/**
 * Sampreshan Notification Center — on-site petition notifications.
 *
 * Petition events (new I, starter updates, victory, welcome) are pushed
 * into the BuddyPress/BuddyBoss native notifications API, so they appear
 * in the theme's own bell + notifications screen on desktop, tablet and
 * mobile with zero custom UI to maintain. Email + dashboard activity
 * continue to work alongside.
 *
 * @package SampreShan_Child
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Push one notification (no-op when BP notifications are unavailable).
 */
function sp_notify_user( $user_id, $action, $petition_id = 0, $secondary_id = 0, $allow_duplicate = false ) {
    $user_id = (int) $user_id;
    if ( $user_id <= 0 ) { return false; }
    if ( ! function_exists( 'bp_notifications_add_notification' ) ) { return false; }
    if ( function_exists( 'bp_is_active' ) && ! bp_is_active( 'notifications' ) ) { return false; }

    return bp_notifications_add_notification( array(
        'user_id'           => $user_id,
        'item_id'           => (int) $petition_id,
        'secondary_item_id' => (int) $secondary_id,
        'component_name'    => 'sampreshan',
        'component_action'  => sanitize_key( $action ),
        'date_notified'     => function_exists( 'bp_core_current_time' ) ? bp_core_current_time() : current_time( 'mysql' ),
        'is_new'            => 1,
        'allow_duplicate'   => (bool) $allow_duplicate,
    ) );
}

/**
 * Recent signer IDs for fan-out (excludes nobody; caller filters actor).
 */
function sp_notif_recent_signer_ids( $petition_id, $limit = 15 ) {
    $ids = array();
    if ( ! function_exists( 'sp_petition_get_signatures' ) ) { return $ids; }
    $rows = sp_petition_get_signatures( array( 'petition_id' => (int) $petition_id, 'per_page' => (int) $limit, 'page' => 1 ) );
    if ( ! $rows ) { return $ids; }
    foreach ( $rows as $r ) {
        $uid = isset( $r->user_id ) ? (int) $r->user_id : 0;
        if ( $uid > 0 && ! in_array( $uid, $ids, true ) ) { $ids[] = $uid; }
    }
    return $ids;
}

/**
 * Drop older unread notifications of the same kind so updates never pile up.
 */
function sp_notif_clear_kind( $user_id, $petition_id, $action ) {
    if ( ! function_exists( 'bp_notifications_delete_notifications_by_item_id' ) ) { return; }
    bp_notifications_delete_notifications_by_item_id( (int) $user_id, (int) $petition_id, 'sampreshan', sanitize_key( $action ) );
}

/**
 * Render our notifications inside BP/BuddyBoss bell + screens.
 */
function sp_notif_format_for_user( $action, $item_id, $secondary_item_id, $total_items, $format, $component_action_name = '', $component_name = '', $id = 0 ) {
    if ( 'sampreshan' !== $component_name ) { return $action; }

    $pid      = (int) $item_id;
    $petition = $pid > 0 ? get_post( $pid ) : null;
    $title    = ( $petition && 'petition' === $petition->post_type ) ? $petition->post_title : __( 'a petition', 'sampreshan-child' );
    $link     = ( $petition && 'petition' === $petition->post_type && function_exists( 'get_permalink' ) ) ? get_permalink( $pid ) : home_url( '/dashboard/' );

    switch ( $component_action_name ) {
        case 'new_signature':
            $who  = $secondary_item_id > 0 ? get_userdata( (int) $secondary_item_id ) : false;
            $name = $who ? $who->display_name : __( 'Someone', 'sampreshan-child' );
            $text = sprintf( __( '%1$s gave an I to your issue "%2$s"', 'sampreshan-child' ), $name, $title );
            break;
        case 'petition_updated':
            $text = sprintf( __( 'New update on "%s"', 'sampreshan-child' ), $title );
            break;
        case 'petition_victory':
            $text = sprintf( __( 'Victory! "%s" won.', 'sampreshan-child' ), $title );
            break;
        case 'welcome':
            $text = __( 'Welcome to Sampreshan — raise your first Issue Sampreshan.', 'sampreshan-child' );
            $link = home_url( '/start-a-petition/' );
            break;
        default:
            return $action;
    }

    if ( 'string' === $format ) {
        return '<a href="' . esc_url( $link ) . '">' . esc_html( $text ) . '</a>';
    }
    return array( 'text' => $text, 'link' => $link );
}
add_filter( 'bp_notifications_get_notifications_for_user', 'sp_notif_format_for_user', 10, 8 );

/**
 * New I → notify the petition author (never self-notify).
 */
function sp_notif_on_signature( $petition_id, $signer_id, $signature_id = 0 ) {
    $petition = get_post( (int) $petition_id );
    if ( ! $petition || 'petition' !== $petition->post_type ) { return; }
    $author_id = (int) $petition->post_author;
    if ( $author_id <= 0 || $author_id === (int) $signer_id ) { return; }
    sp_notify_user( $author_id, 'new_signature', (int) $petition_id, (int) $signer_id );
}
add_action( 'sampreshan_petition_signed', 'sp_notif_on_signature', 10, 3 );

/**
 * Starter update → notify recent signers (not the author writing it).
 */
function sp_notif_on_update( $petition_id, $actor_id = 0 ) {
    $petition = get_post( (int) $petition_id );
    if ( ! $petition || 'petition' !== $petition->post_type ) { return; }
    foreach ( sp_notif_recent_signer_ids( (int) $petition_id, 15 ) as $uid ) {
        if ( $uid === (int) $actor_id || $uid === (int) $petition->post_author ) { continue; }
        sp_notif_clear_kind( $uid, (int) $petition_id, 'petition_updated' );
        sp_notify_user( $uid, 'petition_updated', (int) $petition_id, (int) $actor_id, true );
    }
}
add_action( 'sampreshan_petition_updated', 'sp_notif_on_update', 10, 2 );

/**
 * Victory → notify author + recent signers.
 */
function sp_notif_on_victory( $petition_id, $actor_id = 0 ) {
    $petition = get_post( (int) $petition_id );
    if ( ! $petition || 'petition' !== $petition->post_type ) { return; }
    $targets = sp_notif_recent_signer_ids( (int) $petition_id, 15 );
    $targets[] = (int) $petition->post_author;
    foreach ( array_unique( $targets ) as $uid ) {
        if ( $uid <= 0 || $uid === (int) $actor_id ) { continue; }
        sp_notify_user( $uid, 'petition_victory', (int) $petition_id, (int) $actor_id );
    }
}
add_action( 'sampreshan_petition_victory', 'sp_notif_on_victory', 10, 2 );

/**
 * Welcome notification on registration (email, OTP, or standard signup).
 */
function sp_notif_on_register( $user_id ) {
    sp_notify_user( (int) $user_id, 'welcome', 0, 0 );
}
add_action( 'user_register', 'sp_notif_on_register', 20, 1 );
