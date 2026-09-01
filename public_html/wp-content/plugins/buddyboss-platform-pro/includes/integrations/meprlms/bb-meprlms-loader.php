<?php
/**
 * BuddyBoss MemberpressLMS Integration Loader.
 *
 * @package BuddyBossPro/Integration/MemberpressLMS
 *
 * @since 2.6.30
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Redirect legacy MeprLMS integration tab to Settings 2.0.
 *
 * Registered unconditionally because the loader runs before Settings 2.0
 * bootstrap. The function_exists check happens at admin_init time when
 * Settings 2.0 classes are available.
 *
 * @since 3.0.0
 *
 * @return void
 */
function bb_meprlms_redirect_legacy_integration_tab() {
	if ( ! function_exists( 'bb_register_feature_field' ) ) {
		return;
	}

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Redirect only, no data modification.
	$page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : '';

	if ( 'bp-integrations' === $page && 'bb-meprlms' === $tab ) {
		wp_safe_redirect( admin_url( 'admin.php?page=bb-settings&tab=meprlms&panel=meprlms_settings' ) );
		exit;
	}
}
add_action( 'admin_init', 'bb_meprlms_redirect_legacy_integration_tab' );

/**
 * Register MeprLMS as a managed integration in the Integration Bridge.
 *
 * Runs at bb_integration_bridge_init (bp_loaded priority 2) so the bridge
 * filter is in place before BP_Integration::is_activated() is called
 * during bp_setup_integrations (priority 3).
 *
 * @since 3.0.0
 */
function bb_meprlms_register_managed_integration() {
	if ( ! function_exists( 'bb_integration_bridge' ) ) {
		return;
	}

	bb_integration_bridge()->register_managed_integration( 'meprlms', 'meprlms' );
}
add_action( 'bb_integration_bridge_init', 'bb_meprlms_register_managed_integration' );

/**
 * Register MeprLMS as an integration feature card in Settings 2.0.
 *
 * This makes the MeprLMS integration appear in the features grid under
 * the "BUDDYBOSS INTEGRATIONS" category with a toggle to enable/disable.
 *
 * @since 3.0.0
 */
function bb_meprlms_register_integration_feature() {
	if ( ! function_exists( 'bb_register_integration' ) ) {
		return;
	}

	bb_register_integration(
		'meprlms',
		array(
			'label'          => __( 'MemberPress Courses', 'buddyboss-pro' ),
			'description'    => __( 'Combine membership access control with built-in course creation to monetize your community content.', 'buddyboss-pro' ),
			'icon'           => array(
				'type'  => 'svg',
				'url'  => 'https://bb-features-marketing.s3.amazonaws.com/images/svg/MemberPress.svg',
			),
			'license_tier'   => 'pro',
			'settings_route' => '/settings/meprlms',
			// Keep in sync with the S3 placeholder catalog so the card
			// lands in the same slot whether the placeholder or the
			// registered feature is rendering it.
			'order'          => 50,
		)
	);
}
add_action( 'bb_after_register_features', 'bb_meprlms_register_integration_feature' );

/**
 * One-time migration: activate the MeprLMS feature card in Settings 2.0
 * if the legacy bb-meprlms-enable option was ON.
 *
 * On plugin update, existing sites that had MeprLMS enabled via the old
 * serialized option (bb-meprlms → bb-meprlms-enable) need the feature
 * to appear toggled ON in the Settings 2.0 grid. Without this, the
 * Integration Bridge defaults to enabled, but the feature card toggle
 * shows OFF because bb-active-features has no entry for 'meprlms'.
 *
 * @since 3.0.0
 *
 * @return void
 */
function bb_meprlms_migrate_legacy_activation() {
	if ( ! function_exists( 'bb_feature_registry' ) ) {
		return;
	}

	// Only run once — check if meprlms is already in bb-active-features.
	$active_features = bp_get_option( 'bb-active-features', array() );
	$active_features = is_array( $active_features ) ? $active_features : array();
	if ( isset( $active_features['meprlms'] ) ) {
		return;
	}

	// Read the legacy serialized option.
	$bb_meprlms = bp_get_option( 'bb-meprlms', array() );
	$bb_meprlms = is_array( $bb_meprlms ) ? $bb_meprlms : array();

	// If legacy option had enable = 1, activate the feature in Settings 2.0.
	if ( ! empty( $bb_meprlms['bb-meprlms-enable'] ) ) {
		$active_features['meprlms'] = 1;
		bp_update_option( 'bb-active-features', $active_features );

		// Also sync to legacy bp-active-components for bp_is_active() compat.
		$active_components            = bp_get_option( 'bp-active-components', array() );
		$active_components            = is_array( $active_components ) ? $active_components : array();
		$active_components['meprlms'] = 1;
		bp_update_option( 'bp-active-components', $active_components );
	}
}
add_action( 'bb_after_register_features', 'bb_meprlms_migrate_legacy_activation', 5 );

/**
 * Register MeprLMS Settings 2.0 settings fields.
 *
 * Registers side panel, sections, and fields for the MeprLMS integration
 * settings page in the React admin UI.
 *
 * @since 3.0.0
 */
function bb_meprlms_register_settings_2_fields() {
	if ( ! function_exists( 'bb_register_feature_field' ) ) {
		return;
	}

	// Settings 2.0 panels are admin-only — skip registration on frontend.
	// The defaults computed in bb-meprlms-settings.php call bb_get_option()
	// helpers (license-tier checks, MeprLMS course-list lookups) that read
	// DB rows; this gate avoids those reads on every frontend hit.
	if ( ! is_admin() && ! wp_doing_ajax() ) {
		return;
	}

	require_once __DIR__ . '/bb-meprlms-settings.php';
}
add_action( 'bb_after_register_features', 'bb_meprlms_register_settings_2_fields', 15 );

/**
 * Set up the BB MemberpressLMS integration.
 *
 * @since 2.6.30
 */
function bb_register_meprlms_integration() {

	// Check if MeprLMS feature is disabled via Integration Bridge.
	if ( function_exists( 'bb_integration_bridge' ) && bb_integration_bridge()->is_managed_integration( 'meprlms' ) ) {
		if ( ! bb_integration_bridge()->is_integration_feature_enabled( 'meprlms' ) ) {
			return;
		}
	}

	require_once __DIR__ . '/includes/class-bb-meprlms-integration.php';
	buddypress()->integrations['meprlms'] = new BB_MeprLMS_Integration();
}
add_action( 'bp_setup_integrations', 'bb_register_meprlms_integration', 20 );
