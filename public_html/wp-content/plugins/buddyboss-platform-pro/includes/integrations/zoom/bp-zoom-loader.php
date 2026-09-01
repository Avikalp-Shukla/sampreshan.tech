<?php
/**
 * BuddyBoss Zoom Integration Loader.
 *
 * @package BuddyBossPro/Integration/Zoom
 * @since 1.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Legacy integration tab redirect handled centrally by Platform's
// bb_redirect_legacy_settings_to_settings_2() via bb_legacy_integration_tabs_mapping filter.
// See bb_pro_legacy_integration_tabs_mapping() in bb-pro-core-actions.php.

/**
 * Register Zoom as a managed integration in the Integration Bridge.
 *
 * Runs at bb_integration_bridge_init (bp_loaded priority 2) so the bridge
 * filter is in place before BP_Integration::is_activated() is called
 * during bp_setup_integrations (priority 3).
 *
 * @since 3.0.0
 */
function bb_zoom_register_managed_integration() {
	if ( ! function_exists( 'bb_integration_bridge' ) ) {
		return;
	}

	bb_integration_bridge()->register_managed_integration( 'zoom', 'zoom' );
}
add_action( 'bb_integration_bridge_init', 'bb_zoom_register_managed_integration' );

/**
 * Register Zoom as an integration feature card in Settings 2.0.
 *
 * This makes the Zoom integration appear in the features grid under
 * the "BUDDYBOSS INTEGRATIONS" category with a toggle to enable/disable.
 *
 * @since 3.0.0
 */
function bb_zoom_register_integration_feature() {
	if ( ! function_exists( 'bb_register_integration' ) ) {
		return;
	}

	/**
	 * Fires the legacy bp_zoom_integration_is_active filter for backward compatibility.
	 *
	 * Third-party plugins that previously hooked this filter to disable the Zoom
	 * integration tab will still be notified, but should migrate to the Integration
	 * Bridge toggle in Settings 2.0.
	 *
	 * @since 3.0.0
	 * @deprecated 3.0.0 Use the Integration Bridge toggle in Settings 2.0 instead.
	 */
	apply_filters_deprecated(
		'bp_zoom_integration_is_active',
		array( true ),
		'3.0.0',
		'',
		__( 'Use the Zoom integration toggle in Settings 2.0 instead.', 'buddyboss-pro' )
	);

	bb_register_integration(
		'zoom',
		array(
			'label'          => __( 'Zoom', 'buddyboss-pro' ),
			'description'    => __( 'Host live virtual meetings, webinars, and group sessions directly within your community platform.', 'buddyboss-pro' ),
			'icon'           => array(
				'type'  => 'svg',
				'url'  => 'https://bb-features-marketing.s3.amazonaws.com/images/svg/Zoom.svg',
			),
			'license_tier'   => 'pro',
			'settings_route' => '/settings/zoom',
			// Keep in sync with the S3 placeholder catalog
			// (https://bb-features-marketing.s3.amazonaws.com/bb-features.json).
			// When an older Pro install is running and this feature isn't
			// registered yet, the catalog placeholder renders in the same
			// slot; when Pro updates and the feature registers, it takes
			// over the same slot without visually shifting the grid.
			'order'          => 70,
		)
	);
}
add_action( 'bb_after_register_features', 'bb_zoom_register_integration_feature' );

/**
 * Register Zoom Settings 2.0 settings fields.
 *
 * Registers side panels, sections, and fields for the Zoom integration
 * settings page in the React admin UI.
 *
 * @since 3.0.0
 */
function bb_zoom_register_settings_fields() {
	if ( ! function_exists( 'bb_register_feature_field' ) ) {
		return;
	}

	// Settings 2.0 panels are admin-only — skip registration on frontend.
	// The defaults computed in bb-zoom-settings.php call bb_get_option() and
	// bp_zoom_*() helpers (zoom_account_email lookups, server-to-server token
	// state) that read DB rows; this gate avoids those reads on every
	// frontend hit.
	if ( ! is_admin() && ! wp_doing_ajax() ) {
		return;
	}

	// Skip settings registration when Zoom is disabled via integration toggle.
	if ( function_exists( 'bb_integration_bridge' ) && bb_integration_bridge()->is_managed_integration( 'zoom' ) ) {
		if ( ! bb_integration_bridge()->is_integration_feature_enabled( 'zoom' ) ) {
			return;
		}
	}

	require_once __DIR__ . '/bb-zoom-settings.php';
}
add_action( 'bb_after_register_features', 'bb_zoom_register_settings_fields', 15 );

/**
 * Set up the bp zoom integration.
 *
 * @since 1.0.0
 */
function bp_register_zoom_integration() {

	// Check if Zoom feature is disabled via Integration Bridge.
	if ( function_exists( 'bb_integration_bridge' ) && bb_integration_bridge()->is_managed_integration( 'zoom' ) ) {
		if ( ! bb_integration_bridge()->is_integration_feature_enabled( 'zoom' ) ) {
			return;
		}
	}

	require_once __DIR__ . '/bp-zoom-integration.php';
	buddypress()->integrations['zoom'] = new BP_Zoom_Integration();
}
add_action( 'bp_setup_integrations', 'bp_register_zoom_integration' );
