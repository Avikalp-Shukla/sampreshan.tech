<?php
/**
 * Sampreshan Sahayak — AJAX endpoint + rate limiting.
 *
 * Public endpoint (works for guests and logged-in members). The upstream
 * API key never leaves the server; only the assistant's reply text is
 * returned to the browser.
 *
 * @package SampreShan_Child
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

require_once __DIR__ . '/client.php';

/**
 * Per-visitor rate limit: 12 messages per 5 minutes. Identified by user ID
 * when logged in, otherwise by a short-lived signed cookie token so a guest
 * can't simply clear cookies mid-burst without also losing conversation
 * context in the widget.
 */
function sp_ai_agent_rate_limit_key() {
    if ( is_user_logged_in() ) {
        return 'sp_ai_rl_u' . get_current_user_id();
    }
    $ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : 'unknown';
    return 'sp_ai_rl_ip' . md5( $ip );
}

function sp_ai_agent_check_rate_limit() {
    $key   = sp_ai_agent_rate_limit_key();
    $count = (int) get_transient( $key );
    if ( $count >= 12 ) {
        return false;
    }
    set_transient( $key, $count + 1, 5 * MINUTE_IN_SECONDS );
    return true;
}

function sp_ajax_ai_agent_chat() {
    check_ajax_referer( 'sp_ai_agent_chat', 'nonce' );

    if ( ! sp_ai_agent_check_rate_limit() ) {
        wp_send_json_error( array( 'message' => __( 'Too many messages. Please wait a moment and try again.', 'sampreshan-child' ) ), 429 );
    }

    $message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';
    $message = trim( $message );

    if ( '' === $message ) {
        wp_send_json_error( array( 'message' => __( 'Please type a message.', 'sampreshan-child' ) ), 400 );
    }
    if ( mb_strlen( $message ) > 1000 ) {
        wp_send_json_error( array( 'message' => __( 'Message is too long (max 1000 characters).', 'sampreshan-child' ) ), 400 );
    }

    // Optional short history from the client for continuity (validated, capped).
    $history = array();
    if ( isset( $_POST['history'] ) ) {
        $raw_history = json_decode( wp_unslash( $_POST['history'] ), true );
        if ( is_array( $raw_history ) ) {
            $raw_history = array_slice( $raw_history, -8 );
            foreach ( $raw_history as $turn ) {
                if ( ! is_array( $turn ) || empty( $turn['role'] ) || empty( $turn['content'] ) ) {
                    continue;
                }
                $role = 'user' === $turn['role'] ? 'user' : 'assistant';
                $content = sanitize_textarea_field( (string) $turn['content'] );
                if ( '' === $content || mb_strlen( $content ) > 1000 ) {
                    continue;
                }
                $history[] = array( 'role' => $role, 'content' => $content );
            }
        }
    }

    $history[] = array( 'role' => 'user', 'content' => $message );

    $result = sp_ai_agent_chat( $history );

    if ( is_wp_error( $result ) ) {
        wp_send_json_error( array( 'message' => $result->get_error_message() ), 502 );
    }

    wp_send_json_success( array( 'reply' => $result['reply'] ) );
}
add_action( 'wp_ajax_sp_ai_agent_chat', 'sp_ajax_ai_agent_chat' );
add_action( 'wp_ajax_nopriv_sp_ai_agent_chat', 'sp_ajax_ai_agent_chat' );
