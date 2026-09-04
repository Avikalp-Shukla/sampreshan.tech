<?php
/**
 * Home: Real Site Statistics Strip — Premium Edition
 * Animated counters, glass cards, hover effects. Real data only.
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

$signature_count = 0;
if ( function_exists( 'sp_petition_signature_count' ) ) {
    // Get total signatures across all petitions
    global $wpdb;
    $sig_table = $wpdb->prefix . 'sampreshan_signatures';
    if ( $wpdb->get_var( "SHOW TABLES LIKE '$sig_table'" ) === $sig_table ) {
        $signature_count = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $sig_table" );
    }
}
?>
<section class="home-stats" aria-label="Platform statistics">
    <div class="home-stats__inner">
        <div class="home-stats__item">
            <span class="home-stats__value" data-count="<?php echo esc_attr( $member_count ); ?>"><?php echo esc_html( number_format_i18n( $member_count ) ); ?></span>
            <span class="home-stats__label"><?php echo esc_html__( 'Members', 'sampreshan-child' ); ?></span>
        </div>
        <div class="home-stats__divider" aria-hidden="true"></div>
        <div class="home-stats__item">
            <span class="home-stats__value" data-count="<?php echo esc_attr( $signature_count ); ?>"><?php echo esc_html( number_format_i18n( $signature_count ) ); ?></span>
            <span class="home-stats__label"><?php echo esc_html__( 'Signatures', 'sampreshan-child' ); ?></span>
        </div>
        <div class="home-stats__divider" aria-hidden="true"></div>
        <div class="home-stats__item">
            <span class="home-stats__value" data-count="<?php echo esc_attr( $post_count ); ?>"><?php echo esc_html( number_format_i18n( $post_count ) ); ?></span>
            <span class="home-stats__label"><?php echo esc_html__( 'Posts', 'sampreshan-child' ); ?></span>
        </div>
        <div class="home-stats__divider" aria-hidden="true"></div>
        <div class="home-stats__item">
            <span class="home-stats__value" data-count="<?php echo esc_attr( $group_count ); ?>"><?php echo esc_html( number_format_i18n( $group_count ) ); ?></span>
            <span class="home-stats__label"><?php echo esc_html__( 'Groups', 'sampreshan-child' ); ?></span>
        </div>
    </div>
</section>
