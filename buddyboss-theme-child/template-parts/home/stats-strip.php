<?php
/**
 * Home: Real Site Statistics Strip
 * Uses real, live WordPress data only — no fake numbers, no AI content.
 *
 * @package SampreShan_Child
 */

// Real counts from WordPress
$member_count = 0;
if ( function_exists( 'bp_get_total_member_count' ) ) {
    $member_count = (int) bp_get_total_member_count();
} elseif ( function_exists( 'count_users' ) ) {
    $users = count_users();
    $member_count = isset( $users['total_users'] ) ? (int) $users['total_users'] : 0;
}

$post_count = (int) wp_count_posts( 'post' )->publish;
$page_count = (int) wp_count_posts( 'page' )->publish;
$group_count = 0;
if ( function_exists( 'groups_get_total_group_count' ) ) {
    $group_count = (int) groups_get_total_group_count();
}

// Signatures = 0 unless real meta data is added later. No fake numbers.
$signature_count = 0;
?>
<section class="home-stats" aria-label="Platform statistics">
    <div class="home-stats__inner">
        <div class="home-stats__item">
            <span class="home-stats__value"><?php echo esc_html( number_format_i18n( $member_count ) ); ?></span>
            <span class="home-stats__label"><?php echo esc_html__( 'Members', 'sampreshan-child' ); ?></span>
        </div>
        <div class="home-stats__divider" aria-hidden="true"></div>
        <div class="home-stats__item">
            <span class="home-stats__value"><?php echo esc_html( number_format_i18n( $post_count ) ); ?></span>
            <span class="home-stats__label"><?php echo esc_html__( 'Posts', 'sampreshan-child' ); ?></span>
        </div>
        <div class="home-stats__divider" aria-hidden="true"></div>
        <div class="home-stats__item">
            <span class="home-stats__value"><?php echo esc_html( number_format_i18n( $page_count ) ); ?></span>
            <span class="home-stats__label"><?php echo esc_html__( 'Petitions', 'sampreshan-child' ); ?></span>
        </div>
        <div class="home-stats__divider" aria-hidden="true"></div>
        <div class="home-stats__item">
            <span class="home-stats__value"><?php echo esc_html( number_format_i18n( $group_count ) ); ?></span>
            <span class="home-stats__label"><?php echo esc_html__( 'Groups', 'sampreshan-child' ); ?></span>
        </div>
    </div>
</section>
