<?php
/**
 * BuddyBoss TutorLMS Integration Loader.
 *
 * @package BuddyBossPro/Integration/TutorLMS
 *
 * @since 2.4.40
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Redirect legacy TutorLMS integration tab to Settings 2.0.
 *
 * Registered unconditionally because the loader runs before Settings 2.0
 * bootstrap. The function_exists check happens at admin_init time when
 * Settings 2.0 classes are available.
 *
 * @since 3.0.0
 *
 * @return void
 */
function bb_tutorlms_redirect_legacy_integration_tab() {
	if ( ! function_exists( 'bb_register_feature_field' ) ) {
		return;
	}

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Redirect only, no data modification.
	$page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : '';

	if ( 'bp-integrations' === $page && 'bb-tutorlms' === $tab ) {
		wp_safe_redirect( admin_url( 'admin.php?page=bb-settings&tab=tutorlms&panel=tutorlms_settings' ) );
		exit;
	}
}
add_action( 'admin_init', 'bb_tutorlms_redirect_legacy_integration_tab' );

/**
 * Register TutorLMS as a managed integration in the Integration Bridge.
 *
 * Runs at bb_integration_bridge_init (bp_loaded priority 2) so the bridge
 * filter is in place before BP_Integration::is_activated() is called
 * during bp_setup_integrations (priority 3).
 *
 * @since 3.0.0
 */
function bb_tutorlms_register_managed_integration() {
	if ( ! function_exists( 'bb_integration_bridge' ) ) {
		return;
	}

	bb_integration_bridge()->register_managed_integration( 'tutorlms', 'tutorlms' );
}
add_action( 'bb_integration_bridge_init', 'bb_tutorlms_register_managed_integration' );

/**
 * Register TutorLMS as an integration feature card in Settings 2.0.
 *
 * This makes the TutorLMS integration appear in the features grid under
 * the "BUDDYBOSS INTEGRATIONS" category with a toggle to enable/disable.
 *
 * @since 3.0.0
 */
function bb_tutorlms_register_integration_feature() {
	if ( ! function_exists( 'bb_register_integration' ) ) {
		return;
	}

	bb_register_integration(
		'tutorlms',
		array(
			'label'          => __( 'Tutor LMS', 'buddyboss-pro' ),
			'description'    => __( 'Create and sell online courses with assignments, quizzes, and certificates directly within your community.', 'buddyboss-pro' ),
			'icon'           => array(
				'type'  => 'svg',
				'url'  => 'https://bb-features-marketing.s3.amazonaws.com/images/svg/TutorLMS.svg',
			),
			'license_tier'   => 'pro',
			'settings_route' => '/settings/tutorlms',
			// Keep in sync with the S3 placeholder catalog so the card
			// lands in the same slot whether the placeholder or the
			// registered feature is rendering it.
			'order'          => 40,
		)
	);
}
add_action( 'bb_after_register_features', 'bb_tutorlms_register_integration_feature' );

/**
 * Register TutorLMS Settings 2.0 settings fields.
 *
 * Registers side panel, sections, and fields for the TutorLMS integration
 * settings page in the React admin UI.
 *
 * @since 3.0.0
 */
function bb_tutorlms_register_settings_2_fields() {
	if ( ! function_exists( 'bb_register_feature_field' ) ) {
		return;
	}

	// Settings 2.0 panels are admin-only — skip registration on frontend.
	// The defaults computed in bb-tutorlms-settings.php call bb_get_option()
	// helpers (license-tier checks, TutorLMS sync-state lookups) that read
	// DB rows; this gate avoids those reads on every frontend hit.
	if ( ! is_admin() && ! wp_doing_ajax() ) {
		return;
	}

	require_once __DIR__ . '/bb-tutorlms-settings.php';
}
add_action( 'bb_after_register_features', 'bb_tutorlms_register_settings_2_fields', 15 );

/**
 * Set up the BB TutorLMS integration.
 *
 * @since 2.4.40
 */
function bb_register_tutorlms_integration() {

	// Check if TutorLMS feature is disabled via Integration Bridge.
	if ( function_exists( 'bb_integration_bridge' ) && bb_integration_bridge()->is_managed_integration( 'tutorlms' ) ) {
		if ( ! bb_integration_bridge()->is_integration_feature_enabled( 'tutorlms' ) ) {
			return;
		}
	}

	require_once __DIR__ . '/bb-tutorlms-integration.php';
	buddypress()->integrations['tutorlms'] = new BB_TutorLMS_Integration();
}
add_action( 'bp_setup_integrations', 'bb_register_tutorlms_integration', 20 );
