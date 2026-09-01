<?php
/**
 * BuddyBoss OneSignal Integration Loader.
 *
 * @package BuddyBossPro/Integration/OneSignal
 * @since 2.0.3
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Legacy integration tab redirect handled centrally by Platform's
// bb_redirect_legacy_settings_to_settings_2() via bb_legacy_integration_tabs_mapping filter.
// See bb_pro_legacy_integration_tabs_mapping() in bb-pro-core-actions.php.

/**
 * Register OneSignal as a managed integration in the Integration Bridge.
 *
 * Runs at bb_integration_bridge_init (bp_loaded priority 2) so the bridge
 * filter is in place before BP_Integration::is_activated() is called
 * during bp_setup_integrations (priority 3).
 *
 * @since 3.0.0
 */
function bb_onesignal_register_managed_integration() {
	if ( ! function_exists( 'bb_integration_bridge' ) ) {
		return;
	}

	bb_integration_bridge()->register_managed_integration( 'onesignal', 'onesignal' );
}
add_action( 'bb_integration_bridge_init', 'bb_onesignal_register_managed_integration' );

/**
 * Register OneSignal as an integration feature card in Settings 2.0.
 *
 * This makes the OneSignal integration appear in the features grid under
 * the "BUDDYBOSS INTEGRATIONS" category with a toggle to enable/disable.
 *
 * @since 3.0.0
 */
function bb_onesignal_register_integration_feature() {
	if ( ! function_exists( 'bb_register_integration' ) ) {
		return;
	}

	bb_register_integration(
		'onesignal',
		array(
			'label'       => __( 'OneSignal', 'buddyboss-pro' ),
			'description' => __( 'Send targeted push notifications to keep members engaged and drive them back to your community.', 'buddyboss-pro' ),
			'icon'        => array(
				'type'  => 'svg',
				'url'  => 'https://bb-features-marketing.s3.amazonaws.com/images/svg/OneSignal.svg',
			),
			'license_tier'   => 'pro',
			'depends_on'     => array( 'notifications' ),
			'settings_route' => '/settings/notifications/onesignal',
			// Keep in sync with the S3 placeholder catalog so the card
			// lands in the same slot whether the placeholder or the
			// registered feature is rendering it.
			'order'          => 90,
		)
	);
}
add_action( 'bb_after_register_features', 'bb_onesignal_register_integration_feature' );

/**
 * Set up the BB OneSignal integration.
 *
 * @since 2.0.3
 */
function bb_register_onesignal_integration() {
	if (
		! function_exists( 'bp_is_labs_notification_preferences_support_enabled' ) ||
		! bp_is_labs_notification_preferences_support_enabled() ||
		! defined( 'BP_PLATFORM_VERSION' ) ||
		version_compare( BP_PLATFORM_VERSION, '2.0.2', '<' ) ||
		! bp_is_active( 'notifications' )
	) {
		return;
	}

	// Check if OneSignal feature is disabled via Integration Bridge.
	if ( function_exists( 'bb_integration_bridge' ) && bb_integration_bridge()->is_managed_integration( 'onesignal' ) ) {
		if ( ! bb_integration_bridge()->is_integration_feature_enabled( 'onesignal' ) ) {
			return;
		}
	}

	require_once dirname( __FILE__ ) . '/bb-onesignal-integration.php';
	buddypress()->integrations['onesignal'] = new BB_OneSignal_Integration();
}
add_action( 'bp_setup_integrations', 'bb_register_onesignal_integration' );
