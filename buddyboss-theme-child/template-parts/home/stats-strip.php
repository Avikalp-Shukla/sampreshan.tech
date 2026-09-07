<?php
/**
 * Home: Real Site Statistics — Premium 3D Dark Edition
 * Glass counters on dark, scroll-triggered animated numbers.
 *
 * @package SampreShan_Child
 */

$member_count = 0;
if ( function_exists( 'bp_get_total_member_count' ) ) {
    $member_count = (int) bp_get_total_member_count();
} elseif ( function_exists( 'count_users' ) ) {
    $users = count_users();
    $member_count = isset( $users['total_users'] ) ? (int) $users['total_users'] : 0;
}
$post_count = (int) wp_count_posts( 'post' )->publish;
$group_count = 0;
if ( function_exists( 'groups_get_total_group_count' ) ) {
    $group_count = (int) groups_get_total_group_count();
}
$signature_count = 0;
if ( function_exists( 'sp_petition_signature_count' ) ) {
    global $wpdb;
    $sig_table = $wpdb->prefix . 'sampreshan_signatures';
    if ( $wpdb->get_var( "SHOW TABLES LIKE '$sig_table'" ) === $sig_table ) {
        $signature_count = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $sig_table" );
    }
}
?>
<div class="d3-stats d3-reveal">
    <div class="d3-stats__inner">
        <div class="d3-stats__item">
            <span class="d3-stats__number" data-count="<?php echo esc_attr( $member_count ); ?>"><?php echo esc_html( number_format_i18n( $member_count ) ); ?></span>
            <span class="d3-stats__label"><?php echo esc_html__( 'Members', 'sampreshan-child' ); ?></span>
        </div>
        <div class="d3-stats__item d3-stats__item--accent">
            <span class="d3-stats__number" data-count="<?php echo esc_attr( $signature_count ); ?>"><?php echo esc_html( number_format_i18n( $signature_count ) ); ?></span>
            <span class="d3-stats__label"><?php echo esc_html__( 'Is Gathered', 'sampreshan-child' ); ?></span>
        </div>
        <div class="d3-stats__item">
            <span class="d3-stats__number" data-count="<?php echo esc_attr( $post_count ); ?>"><?php echo esc_html( number_format_i18n( $post_count ) ); ?></span>
            <span class="d3-stats__label"><?php echo esc_html__( 'Posts', 'sampreshan-child' ); ?></span>
        </div>
        <div class="d3-stats__item">
            <span class="d3-stats__number" data-count="<?php echo esc_attr( $group_count ); ?>"><?php echo esc_html( number_format_i18n( $group_count ) ); ?></span>
            <span class="d3-stats__label"><?php echo esc_html__( 'Groups', 'sampreshan-child' ); ?></span>
        </div>
    </div>
</div>
