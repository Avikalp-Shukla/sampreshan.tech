<?php
/**
 * BuddyBoss Pusher Integration Loader.
 *
 * @package BuddyBossPro\Integration\Pusher
 * @since 2.1.6
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Register Pusher as a managed integration in the Integration Bridge.
 *
 * @since 3.0.0
 */
function bb_pusher_register_managed_integration() {
	if ( ! function_exists( 'bb_integration_bridge' ) ) {
		return;
	}

	bb_integration_bridge()->register_managed_integration( 'pusher', 'pusher' );
}
add_action( 'bb_integration_bridge_init', 'bb_pusher_register_managed_integration' );

/**
 * Register Pusher as an integration feature card in Settings 2.0.
 *
 * @since 3.0.0
 */
function bb_pusher_register_integration_feature() {
	if ( ! function_exists( 'bb_register_integration' ) ) {
		return;
	}

	bb_register_integration(
		'pusher',
		array(
			'label'          => __( 'Pusher', 'buddyboss-pro' ),
			'description'    => __( 'Enable real-time messaging and instant notifications so members always see live activity without refreshing.', 'buddyboss-pro' ),
			'icon'           => array(
				'type'  => 'svg',
				'url'  => 'https://bb-features-marketing.s3.amazonaws.com/images/svg/Pusher.svg',
			),
			'license_tier'   => 'pro',
			'settings_route' => '/settings/pusher',
			// Keep in sync with the S3 placeholder catalog so the card
			// lands in the same slot whether the placeholder or the
			// registered feature is rendering it.
			'order'          => 80,
		)
	);
}
add_action( 'bb_after_register_features', 'bb_pusher_register_integration_feature' );

/**
 * Register Pusher Settings 2.0 settings fields.
 *
 * @since 3.0.0
 */
function bb_pusher_register_settings_fields() {
	if ( ! function_exists( 'bb_register_feature_field' ) ) {
		return;
	}

	// Settings 2.0 panels are admin-only — no need to populate the registry on
	// every frontend request. The registration block reads several DB-backed
	// helpers (bb_pusher_app_id, bb_pusher_app_cluster, bb_pusher_is_enabled)
	// which were firing per frontend hit before this gate.
	if ( ! is_admin() && ! wp_doing_ajax() ) {
		return;
	}

	// Skip settings registration when Pusher is disabled via integration toggle.
	// bp_register_pusher_integration() applies the same gate below, so when the
	// integration is off, bb-pusher-functions.php is never loaded — registering
	// the settings panel would then fatal on the unloaded helpers
	// (bb_pusher_is_enabled, bb_pusher_app_id, etc.).
	if ( function_exists( 'bb_integration_bridge' ) && bb_integration_bridge()->is_managed_integration( 'pusher' ) ) {
		if ( ! bb_integration_bridge()->is_integration_feature_enabled( 'pusher' ) ) {
			return;
		}
	}

	require_once __DIR__ . '/bb-pusher-settings.php';
}
add_action( 'bb_after_register_features', 'bb_pusher_register_settings_fields', 15 );

/**
 * Set up the BB Pusher integration.
 *
 * @since 2.1.6
 */
function bb_pro_register_pusher_integration() {
	if (
		! defined( 'BP_PLATFORM_VERSION' ) ||
		version_compare( BP_PLATFORM_VERSION, '2.2', '<' ) ||
		! function_exists( 'bb_platform_pro' ) ||
		version_compare( bb_platform_pro()->version, '2.1.6', '<' )
	) {
		return;
	}

	// Check if Pusher feature is disabled via Integration Bridge.
	if ( function_exists( 'bb_integration_bridge' ) && bb_integration_bridge()->is_managed_integration( 'pusher' ) ) {
		if ( ! bb_integration_bridge()->is_integration_feature_enabled( 'pusher' ) ) {
			return;
		}
	}

	require_once dirname( __FILE__ ) . '/bb-pusher-integration.php';
	buddypress()->integrations['pusher'] = new BB_Pusher_Integration();
}
add_action( 'bp_setup_integrations', 'bb_pro_register_pusher_integration' );
