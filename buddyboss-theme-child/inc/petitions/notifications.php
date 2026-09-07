<?php
/**
 * Petitions: Email notifications + WP-Cron
 *
 * Notifications:
 *   - signature received        (sent to the petition author, debounced per user/hour)
 *   - milestone reached         (25%, 50%, 75%, 100% of goal)
 *
 * Cron:
 *   - hourly  : send queued signature notification emails
 *   - daily   : scan petitions and fire milestone notifications
 *
 * @package SampreShan_Child
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! function_exists( 'sp_petition_queue_signature_email' ) ) {
    /**
     * Queue a signature notification. Instead of sending inside the AJAX
     * request (which would slow the response and risk email failures
     * mid-handler), we drop the recipient + petition IDs onto a WP option
     * that the hourly cron picks up.
     */
    function sp_petition_queue_signature_email( $petition_id, $user_id, $signature_id ) {
        $petition = get_post( $petition_id );
        if ( ! $petition ) { return; }
        $author_id = (int) $petition->post_author;
        if ( $author_id === (int) $user_id ) { return; } // don't email yourself

        $queue = get_option( 'sp_petition_email_queue', array() );
        $queue[] = array(
            'petition_id'  => (int) $petition_id,
            'signer_id'    => (int) $user_id,
            'signature_id' => (int) $signature_id,
            'author_id'    => $author_id,
            'queued_at'    => time(),
        );
        // Cap the queue so a viral moment doesn't blow up the option row.
        if ( count( $queue ) > 2000 ) { $queue = array_slice( $queue, -2000 ); }
        update_option( 'sp_petition_email_queue', $queue, false );
    }
}

if ( ! function_exists( 'sp_petition_send_queued_emails' ) ) {
    /**
     * Hourly cron: drain the queue, batch by author (one digest per author per hour).
     */
    function sp_petition_send_queued_emails() {
        $queue = get_option( 'sp_petition_email_queue', array() );
        if ( empty( $queue ) ) { return; }

        // Group by (author_id, petition_id) to make a nice digest.
        $grouped = array();
        foreach ( $queue as $item ) {
            $key = $item['author_id'] . ':' . $item['petition_id'];
            $grouped[ $key ][] = $item;
        }

        foreach ( $grouped as $key => $items ) {
            list( $author_id, $petition_id ) = array_map( 'intval', explode( ':', $key ) );
            $petition = get_post( $petition_id );
            $author   = get_user_by( 'id', $author_id );
            if ( ! $petition || ! $author ) { continue; }

            $count     = count( $items );
            $title     = $petition->post_title;
            $url       = get_permalink( $petition_id );
            $total     = function_exists( 'sp_petition_signature_count' ) ? sp_petition_signature_count( $petition_id ) : 0;
            $goal      = (int) get_post_meta( $petition_id, 'sampreshan_goal', true );

            $subject = sprintf(
                /* translators: 1: petition title, 2: signature count */
                _n( '[%1$s] %2$d new signature', '[%1$s] %2$d new signatures', $count, 'sampreshan-child' ),
                wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ),
                $count
            );

            $body = sprintf(
                "%s,\n\n" .
                "%d new %s signed your petition \"%s\".\n\n" .
                "Total signatures: %s (goal: %s)\n\n" .
                "View: %s\n\n" .
                "— %s",
                $author->display_name,
                $count,
                _n( 'person has', 'people have', $count, 'sampreshan-child' ),
                $title,
                number_format_i18n( $total ),
                number_format_i18n( $goal ),
                $url,
                wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES )
            );

            wp_mail( $author->user_email, $subject, $body );
        }

        delete_option( 'sp_petition_email_queue' );
    }
    add_action( 'sp_petition_hourly_cron', 'sp_petition_send_queued_emails' );
}

if ( ! function_exists( 'sp_petition_check_milestones' ) ) {
    /**
     * Daily cron: fire milestone notifications at 25/50/75/100% of goal.
     * State is stored as post meta so each milestone only fires once.
     */
    function sp_petition_check_milestones() {
        $petitions = get_posts( array(
            'post_type'      => 'petition',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'fields'         => 'ids',
        ) );
        if ( ! $petitions ) { return; }

        $milestones = array( 0.25, 0.50, 0.75, 1.00 );
        foreach ( $petitions as $pid ) {
            $goal = (int) get_post_meta( $pid, 'sampreshan_goal', true );
            if ( $goal <= 0 ) { continue; }
            $count = function_exists( 'sp_petition_signature_count' ) ? sp_petition_signature_count( $pid ) : 0;
            $pct   = $count / $goal;
            $fired = (array) get_post_meta( $pid, 'sampreshan_milestones_fired', true );

            foreach ( $milestones as $m ) {
                $key = (string) (int) ( $m * 100 );
                if ( $pct >= $m && ! in_array( $key, $fired, true ) ) {
                    sp_petition_send_milestone_email( $pid, $m, $count, $goal );
                    $fired[] = $key;
                    update_post_meta( $pid, 'sampreshan_milestones_fired', array_values( array_unique( $fired ) ) );
                }
            }
        }
    }
    add_action( 'sp_petition_daily_cron', 'sp_petition_check_milestones' );

    function sp_petition_send_milestone_email( $petition_id, $milestone, $count, $goal ) {
        $petition = get_post( $petition_id );
        $author   = get_user_by( 'id', $petition->post_author );
        if ( ! $author ) { return; }
        $pct = (int) ( $milestone * 100 );
        $subject = sprintf(
            '[%s] %d%% of goal reached on "%s"',
            wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ),
            $pct,
            $petition->post_title
        );
        $body = sprintf(
            "%s,\n\nYour petition has reached %d%% of its goal!\n\n%d of %d signatures.\n\nView: %s",
            $author->display_name,
            $pct,
            $count,
            $goal,
            get_permalink( $petition_id )
        );
        wp_mail( $author->user_email, $subject, $body );
    }
}

if ( ! function_exists( 'sp_petition_schedule_cron' ) ) {
    /**
     * Register hourly + daily schedules. Run on `init` to make sure the
     * schedules exist when cron fires.
     */
    function sp_petition_schedule_cron() {
        if ( ! wp_next_scheduled( 'sp_petition_hourly_cron' ) ) {
            wp_schedule_event( time() + 60, 'hourly', 'sp_petition_hourly_cron' );
        }
        if ( ! wp_next_scheduled( 'sp_petition_daily_cron' ) ) {
            wp_schedule_event( time() + 300, 'daily', 'sp_petition_daily_cron' );
        }
    }
    add_action( 'init', 'sp_petition_schedule_cron' );
}
