<?php
/**
 * Platform Settings Loader.
 *
 * @package BuddyBossPro/Platform Settings
 * @since 1.2.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Set up the platform settings.
 *
 * @since 1.2.0
 */
function bb_register_platform_pro_settings() {
	// Buddyboss platform filters.
	require_once dirname( __FILE__ ) . '/profiles/bb-pro-profiles-filters.php';

	// Buddyboss platform functions.
	require_once dirname( __FILE__ ) . '/profiles/bb-pro-profiles-functions.php';

	// Buddyboss platform profile settings.
	require_once dirname( __FILE__ ) . '/profiles/class-bb-pro-profiles-settings.php';
	BB_Pro_Profiles_Settings::instance();

	// Buddyboss platform group settings (only when groups component is active).
	if ( bp_is_active( 'groups' ) ) {
		require_once dirname( __FILE__ ) . '/groups/bb-pro-groups-filters.php';
		require_once dirname( __FILE__ ) . '/groups/bb-pro-groups-functions.php';
		require_once dirname( __FILE__ ) . '/groups/class-bb-pro-groups-settings.php';
		BB_Pro_Groups_Settings::instance();
	}

	// Buddyboss platform activity settings (only when activity component is active).
	if ( bp_is_active( 'activity' ) ) {
		require_once dirname( __FILE__ ) . '/activity/bb-pro-activity-functions.php';
		require_once dirname( __FILE__ ) . '/activity/class-bb-pro-activity-settings.php';
		BB_Pro_Activity_Settings::instance();
	}
}
add_action( 'bp_setup_components', 'bb_register_platform_pro_settings' );
