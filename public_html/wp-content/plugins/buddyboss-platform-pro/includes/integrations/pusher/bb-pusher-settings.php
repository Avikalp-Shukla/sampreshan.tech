<?php
/**
 * Pusher Settings 2.0 field registrations and AJAX handlers.
 *
 * @package BuddyBossPro\Integration\Pusher
 * @since   3.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Register Settings 2.0 hooks.
// This file is loaded from bb_pusher_register_settings_fields() in bb-pusher-loader.php
// which already checks function_exists('bb_register_feature_field').
bb_pusher_register_settings_panels();
add_action( 'wp_ajax_bb_pusher_save_credentials', 'bb_pusher_save_credentials_ajax' );
add_action( 'bb_admin_save_feature_settings_after', 'bb_pusher_settings_save', 10, 3 );

/**
 * Register Pusher side panels, sections, and fields for Settings 2.0.
 *
 * @since 3.0.0
 */
function bb_pusher_register_settings_panels() {

	$feature_id = 'pusher';

	// =========================================================================
	// SIDE PANEL
	// =========================================================================

	bb_register_side_panel(
		$feature_id,
		'pusher-settings',
		array(
			'title'      => __( 'Pusher Settings', 'buddyboss-pro' ),
			'icon'       => array(
				'type'  => 'font',
				'class' => 'bb-icons-rl bb-icons-rl-gear',
			),
			'order'      => 10,
			'is_default' => true,
		)
	);

	// =========================================================================
	// SECTION 1: Pusher Connection
	// =========================================================================

	$is_connected = bb_pusher_is_enabled();
	$errors       = get_transient( 'bb_pusher_error' );
	$warnings     = get_transient( 'bb_pusher_warning' );

	// Determine status badge.
	$status_type = 'warning';
	if ( $is_connected ) {
		$status_type = 'success';
		if ( ! empty( $warnings ) ) {
			$status_type = 'warning';
		}
	}
	if ( ! empty( $errors ) ) {
		$status_type = 'error';
	}

	bb_register_feature_section(
		$feature_id,
		'pusher-settings',
		'pusher_connection',
		array(
			'title'       => __( 'Pusher', 'buddyboss-pro' ),
			'order'       => 10,
			'status'      => array(
				'type' => $status_type,
				'text' => $is_connected
					? __( 'Connected', 'buddyboss-pro' )
					: __( 'Not Connected', 'buddyboss-pro' ),
			),
			'description' => sprintf(
				/* translators: 1: Pusher Channels link, 2: account link */
				__( 'The BuddyBoss Platform integrates with %1$s, a WebSocket service that powers real-time features in your BuddyBoss community, such as live messaging. After creating your app in your Pusher Channels %2$s, enter the App Keys below to connect it to this site.', 'buddyboss-pro' ),
				'<a href="https://pusher.com/channels" target="_blank" rel="noopener noreferrer">' . __( 'Pusher Channels', 'buddyboss-pro' ) . '</a>',
				'<a href="https://dashboard.pusher.com/channels" target="_blank" rel="noopener noreferrer">' . __( 'account', 'buddyboss-pro' ) . '</a>'
			),
			'help_url'    => bp_get_admin_url(
				add_query_arg(
					array(
						'page'    => 'bp-help',
						'article' => '638245',
					),
					'admin.php'
				)
			),
		)
	);

	// Connection error/warning notice (only shown when there are issues).
	$connection_notice = '';
	$notice_type       = 'error';

	if ( ! empty( $errors ) ) {
		$connection_notice = is_array( $errors ) ? wp_kses_post( implode( '<br/>', $errors ) ) : wp_kses_post( $errors );
	} elseif ( ! empty( $warnings ) ) {
		$connection_notice = is_array( $warnings ) ? wp_kses_post( implode( '<br/>', $warnings ) ) : wp_kses_post( $warnings );
		$notice_type       = 'warning';
	}

	if ( ! empty( $connection_notice ) ) {
		bb_register_feature_field(
			$feature_id,
			'pusher-settings',
			'pusher_connection',
			array(
				'name'              => '_bb_pusher_connection_notice',
				'label'             => '',
				'type'              => 'notice',
				'notice_type'       => $notice_type,
				'description'       => $connection_notice,
				'sanitize_callback' => '__return_empty_string',
				'order'             => 5,
			)
		);
	}

	// App ID.
	bb_register_feature_field(
		$feature_id,
		'pusher-settings',
		'pusher_connection',
		array(
			'name'              => 'bb-pusher-app-id',
			'label'             => __( 'API Keys', 'buddyboss-pro' ),
			'type'              => 'text',
			'field_class'       => 'bb-admin-settings-form__field--input-full',
			'default'           => bb_pusher_app_id(),
			'sanitize_callback' => 'sanitize_text_field',
			'placeholder'       => __( 'Enter pusher app id', 'buddyboss-pro' ),
			'group'             => array(
				'key'   => 'pusher_credentials',
				'label' => __( 'App ID', 'buddyboss-pro' ),
			),
			'order'             => 10,
		)
	);

	// App Key.
	bb_register_feature_field(
		$feature_id,
		'pusher-settings',
		'pusher_connection',
		array(
			'name'              => 'bb-pusher-app-key',
			'label'             => '',
			'type'              => 'password',
			'default'           => bb_pusher_app_key(),
			'sanitize_callback' => 'sanitize_text_field',
			'placeholder'       => __( 'Enter pusher app key', 'buddyboss-pro' ),
			'group'             => array(
				'key'   => 'pusher_credentials',
				'label' => __( 'App Key', 'buddyboss-pro' ),
			),
			'order'             => 20,
		)
	);

	// Secret Key.
	bb_register_feature_field(
		$feature_id,
		'pusher-settings',
		'pusher_connection',
		array(
			'name'              => 'bb-pusher-app-secret',
			'label'             => '',
			'type'              => 'password',
			'default'           => bb_pusher_app_secret(),
			'sanitize_callback' => 'sanitize_text_field',
			'placeholder'       => __( 'Enter pusher secret key', 'buddyboss-pro' ),
			'group'             => array(
				'key'   => 'pusher_credentials',
				'label' => __( 'Secret Key', 'buddyboss-pro' ),
			),
			'order'             => 30,
		)
	);

	// Cluster select.
	$cluster_options = array(
		array(
			'value' => 'mt1',
			'label' => 'mt1 (N. Virginia)',
		),
		array(
			'value' => 'us2',
			'label' => 'us2 (Ohio)',
		),
		array(
			'value' => 'us3',
			'label' => 'us3 (Oregon)',
		),
		array(
			'value' => 'eu',
			'label' => 'eu (Ireland)',
		),
		array(
			'value' => 'ap1',
			'label' => 'ap1 (Singapore)',
		),
		array(
			'value' => 'ap2',
			'label' => 'ap2 (Mumbai)',
		),
		array(
			'value' => 'ap3',
			'label' => 'ap3 (Tokyo)',
		),
		array(
			'value' => 'ap4',
			'label' => 'ap4 (Sydney)',
		),
		array(
			'value' => 'sa1',
			'label' => 'sa1 (São Paulo)',
		),
		array(
			'value' => 'custom',
			'label' => __( 'Custom', 'buddyboss-pro' ),
		),
	);

	bb_register_feature_field(
		$feature_id,
		'pusher-settings',
		'pusher_connection',
		array(
			'name'              => 'bb-pusher-app-cluster',
			'label'             => '',
			'type'              => 'select',
			'field_class'       => 'bb-admin-settings-form__field--select-full',
			'default'           => bb_pusher_app_cluster(),
			'sanitize_callback' => 'sanitize_text_field',
			'options'           => $cluster_options,
			'group'             => array(
				'key'   => 'pusher_credentials',
				'label' => __( 'Cluster', 'buddyboss-pro' ),
			),
			'order'             => 40,
		)
	);

	// Custom cluster name (conditional: only when cluster='custom').
	bb_register_feature_field(
		$feature_id,
		'pusher-settings',
		'pusher_connection',
		array(
			'name'              => 'bb-pusher-app-custom-cluster',
			'label'             => '',
			'type'              => 'text',
			'field_class'       => 'bb-admin-settings-form__field--input-full',
			'default'           => bb_pusher_app_custom_cluster(),
			'sanitize_callback' => 'sanitize_text_field',
			'placeholder'       => __( 'Enter cluster name', 'buddyboss-pro' ),
			'conditional'       => array(
				'field'  => 'bb-pusher-app-cluster',
				'value'  => 'custom',
				'action' => 'show',
			),
			'group'             => array(
				'key'   => 'pusher_credentials',
				'label' => __( 'Cluster Name', 'buddyboss-pro' ),
			),
			'order'             => 45,
		)
	);

	// Update button — bb_verify_popup.
	bb_register_feature_field(
		$feature_id,
		'pusher-settings',
		'pusher_connection',
		array(
			'name'              => '_bb_pusher_verify',
			'label'             => '',
			'type'              => 'bb_verify_popup',
			'button_label'      => __( 'Connect', 'buddyboss-pro' ),
			'ajax_action'       => 'bb_pusher_save_credentials',
			'related_fields'    => array(
				'bb-pusher-app-id',
				'bb-pusher-app-key',
				'bb-pusher-app-secret',
				'bb-pusher-app-cluster',
				'bb-pusher-app-custom-cluster',
			),
			'is_connected'      => $is_connected,
			'verify_config'     => array(
				'modal_title'     => __( 'Verify Pusher', 'buddyboss-pro' ),
				'loading_message' => __( 'Verifying Pusher credentials', 'buddyboss-pro' ),
				'loading_icon'    => 'bb-icons-rl bb-icon-brand-pusher',
			),
			'sanitize_callback' => '__return_empty_string',
			'group'             => 'pusher_credentials',
			'order'             => 50,
		)
	);

	// Hidden field to track connection state for conditionals.
	bb_register_feature_field(
		$feature_id,
		'pusher-settings',
		'pusher_connection',
		array(
			'name'              => '_bb_pusher_is_connected',
			'label'             => '',
			'type'              => 'hidden',
			'default'           => $is_connected ? 1 : 0,
			'sanitize_callback' => '__return_empty_string',
			'order'             => 1,
		)
	);

	// =========================================================================
	// "Enable Feature" — inside same section as credentials (matches Figma).
	// =========================================================================

	$messages_active = bp_is_active( 'messages' );
	$live_messaging  = bb_pusher_is_feature_enabled( 'live-messaging' );

	// Compute disabled state + notice for the Live Messaging toggle.
	$live_messaging_disabled = ! $is_connected || ! $messages_active;
	$live_messaging_notice   = '';
	if ( ! $messages_active ) {
		$live_messaging_notice = __( 'The Messages component must be active to enable this feature.', 'buddyboss-pro' );
	} elseif ( ! $is_connected ) {
		$live_messaging_notice = __( 'Connect your Pusher credentials to enable this feature.', 'buddyboss-pro' );
	}

	// Live Messaging toggle with "Enable Feature" as the field label.
	bb_register_feature_field(
		$feature_id,
		'pusher-settings',
		'pusher_connection',
		array(
			'name'              => 'bb-pusher-live-messaging',
			'label'             => __( 'Enable Feature', 'buddyboss-pro' ),
			'type'              => 'toggle',
			'default'           => $live_messaging ? 1 : 0,
			'sanitize_callback' => 'absint',
			'description'       => __( 'Live Messaging', 'buddyboss-pro' ),
			'help_text'         => __( 'When enabled, members will send and receive private messages in realtime across their devices.', 'buddyboss-pro' ),
			'disabled'          => $live_messaging_disabled,
			'disabled_notice'   => $live_messaging_notice,
			'order'             => 60,
		)
	);

	// Footer text — plain text at bottom of section (no icon/background per Figma).
	bb_register_feature_field(
		$feature_id,
		'pusher-settings',
		'pusher_connection',
		array(
			'name'              => '_bb_pusher_footer_notice',
			'label'             => '',
			'type'              => 'notice',
			'notice_type'       => 'plain',
			'description'       => __( 'In your app\'s settings, please enable "Client events" and "Authorized connections" for this integration to work correctly.', 'buddyboss-pro' ),
			'sanitize_callback' => '__return_empty_string',
			'order'             => 70,
		)
	);
}

/**
 * Handle Pusher credential save via separate AJAX endpoint.
 *
 * Saves all 4 credential fields + custom cluster, then validates via
 * Pusher API test trigger. Returns connection status for the verify popup modal.
 *
 * @since 3.0.0
 */
function bb_pusher_save_credentials_ajax() {

	if ( ! bp_current_user_can( 'bp_moderate' ) ) {
		wp_send_json_error(
			array(
				'message' => esc_html__( 'You do not have permission to perform this action.', 'buddyboss-pro' ),
			)
		);
	}

	check_ajax_referer( 'bb_admin_settings', 'nonce' );

	// Sanitize inputs — related_fields + payload_fields are sent by bb_verify_popup.
	$app_id         = isset( $_POST['bb-pusher-app-id'] ) ? sanitize_text_field( wp_unslash( $_POST['bb-pusher-app-id'] ) ) : '';
	$app_key        = isset( $_POST['bb-pusher-app-key'] ) ? sanitize_text_field( wp_unslash( $_POST['bb-pusher-app-key'] ) ) : '';
	$app_secret     = isset( $_POST['bb-pusher-app-secret'] ) ? sanitize_text_field( wp_unslash( $_POST['bb-pusher-app-secret'] ) ) : '';
	$app_cluster    = isset( $_POST['bb-pusher-app-cluster'] ) ? sanitize_text_field( wp_unslash( $_POST['bb-pusher-app-cluster'] ) ) : '';
	$custom_cluster = isset( $_POST['bb-pusher-app-custom-cluster'] ) ? sanitize_text_field( wp_unslash( $_POST['bb-pusher-app-custom-cluster'] ) ) : '';

	// Detect disconnect action: all credential fields are empty.
	$is_disconnecting = empty( $app_id ) && empty( $app_key ) && empty( $app_secret );

	// Snapshot previous values so we can roll back on validation failure.
	// bb_pusher_credential_validate() reads credentials via bp_get_option(),
	// so we must write them before validating — the rollback restores the
	// previous state if the new values don't authenticate successfully.
	$previous = array(
		'bb-pusher-app-id'             => bp_get_option( 'bb-pusher-app-id', '' ),
		'bb-pusher-app-key'            => bp_get_option( 'bb-pusher-app-key', '' ),
		'bb-pusher-app-secret'         => bp_get_option( 'bb-pusher-app-secret', '' ),
		'bb-pusher-app-cluster'        => bp_get_option( 'bb-pusher-app-cluster', '' ),
		'bb-pusher-app-custom-cluster' => bp_get_option( 'bb-pusher-app-custom-cluster', '' ),
		'bb-pusher-enabled'            => bp_get_option( 'bb-pusher-enabled', false ),
	);

	// Write candidate credential options so the validator can read them.
	bp_update_option( 'bb-pusher-app-id', $app_id );
	bp_update_option( 'bb-pusher-app-key', $app_key );
	bp_update_option( 'bb-pusher-app-secret', $app_secret );
	bp_update_option( 'bb-pusher-app-cluster', $app_cluster );

	// Handle custom cluster (same as legacy settings_save).
	if ( 'custom' === $app_cluster && ! empty( $custom_cluster ) ) {
		bp_update_option( 'bb-pusher-app-custom-cluster', $custom_cluster );
	} else {
		bp_delete_option( 'bb-pusher-app-custom-cluster' );
	}

	// Clear previous error/warning transients before validation.
	delete_transient( 'bb_pusher_error' );
	delete_transient( 'bb_pusher_warning' );

	// Only validate when credentials are present (skip on disconnect).
	if ( ! $is_disconnecting ) {
		bb_pusher_credential_validate();
	} else {
		// Explicitly disable on disconnect.
		bp_delete_option( 'bb-pusher-enabled' );
	}

	// Roll back credential writes if validation failed. This prevents stale or
	// invalid values from bleeding into the UI on the next page load. A
	// disconnect (all empty) is a valid outcome and never rolls back.
	if ( ! $is_disconnecting && ! bb_pusher_is_enabled() ) {
		foreach ( $previous as $option_name => $previous_value ) {
			if ( '' === $previous_value || false === $previous_value ) {
				bp_delete_option( $option_name );
			} else {
				bp_update_option( $option_name, $previous_value );
			}
		}
	}

	// Determine connection state for response.
	$is_connected = bb_pusher_is_enabled();
	$errors       = get_transient( 'bb_pusher_error' );
	$warnings     = get_transient( 'bb_pusher_warning' );
	$has_errors   = ! empty( $errors );
	$has_warnings = ! empty( $warnings );

	// Determine section status badge.
	$status_type = 'warning';
	if ( $is_connected ) {
		$status_type = 'success';
		if ( $has_warnings ) {
			$status_type = 'warning';
		}
	}
	if ( $has_errors ) {
		$status_type = 'error';
	}

	// Build response message.
	$message = '';
	if ( $is_disconnecting ) {
		$message = esc_html__( 'Pusher credentials cleared successfully.', 'buddyboss-pro' );
	} elseif ( $is_connected ) {
		$message = esc_html__( 'Pusher connected successfully.', 'buddyboss-pro' );
	} elseif ( $has_errors ) {
		$message = is_array( $errors ) ? wp_kses_post( implode( ' ', $errors ) ) : wp_kses_post( $errors );
	} elseif ( $has_warnings ) {
		$message = is_array( $warnings ) ? wp_kses_post( implode( ' ', $warnings ) ) : wp_kses_post( $warnings );
	} else {
		$message = esc_html__( 'Connection failed. Please verify your credentials.', 'buddyboss-pro' );
	}

	$response_data = array(
		'is_connected'   => $is_connected,
		'message'        => $message,
		'status'         => array(
			'type' => $status_type,
			'text' => $is_connected
				? __( 'Connected', 'buddyboss-pro' )
				: __( 'Not Connected', 'buddyboss-pro' ),
		),
		'updated_fields' => array(
			'_bb_pusher_is_connected' => $is_connected ? 1 : 0,
		),
	);

	// Include debug details when WP_DEBUG is enabled.
	if ( defined( 'WP_DEBUG' ) && WP_DEBUG && ! $is_connected && ! $is_disconnecting ) {
		$response_data['debug'] = array(
			'errors'   => $has_errors ? ( is_array( $errors ) ? $errors : array( $errors ) ) : array(),
			'warnings' => $has_warnings ? ( is_array( $warnings ) ? $warnings : array( $warnings ) ) : array(),
			'cluster'  => $app_cluster,
		);
	}

	// bb_verify_popup: success = green checkmark, error = red X.
	// On disconnect, return success even though is_connected is false.
	if ( $is_connected || $is_disconnecting ) {
		wp_send_json_success( $response_data );
	} else {
		wp_send_json_error( $response_data );
	}
}

/**
 * Handle custom save logic for Pusher settings in Settings 2.0.
 *
 * Credential fields are handled by the Update button AJAX handler.
 * The Live Messaging toggle auto-saves and needs to be mapped to the
 * `bb-pusher-enabled-features` array format.
 *
 * @since 3.0.0
 *
 * @param string $feature_id Feature ID.
 * @param array  $settings   Full submitted settings.
 * @param array  $saved      Keys and values already saved by core pipeline.
 */
function bb_pusher_settings_save( $feature_id, $settings, $saved ) {

	if ( 'pusher' !== $feature_id ) {
		return;
	}

	// Note: credential fields (bb-pusher-app-*) are persisted by the dedicated
	// Connect AJAX handler (bb_pusher_save_credentials_ajax), not the auto-save
	// pipeline. Pusher stores them as individual options — unlike Zoom which
	// uses a serialized array — so no shape transformation is needed here.

	// Handle Live Messaging toggle → map to bb-pusher-enabled-features array.
	// The core save pipeline writes bb-pusher-live-messaging as a standalone
	// option; this handler rewrites it into the legacy array shape that the
	// rest of the Pusher module reads from, and cleans up the standalone.
	if ( array_key_exists( 'bb-pusher-live-messaging', $saved ) ) {
		$enabled_features = bp_get_option( 'bb-pusher-enabled-features', array() );
		if ( ! is_array( $enabled_features ) ) {
			$enabled_features = array();
		}

		if ( ! empty( $saved['bb-pusher-live-messaging'] ) ) {
			$enabled_features['live-messaging'] = 1;
		} else {
			unset( $enabled_features['live-messaging'] );
		}

		bp_update_option( 'bb-pusher-enabled-features', $enabled_features );
	}

	// Always remove the standalone option, even when the field wasn't in this
	// save payload. This cleans up any stray value left behind by a prior
	// interrupted request (e.g. fatal error between the two writes above).
	bp_delete_option( 'bb-pusher-live-messaging' );
}
