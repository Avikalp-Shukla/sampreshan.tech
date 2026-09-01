<?php
/**
 * Zoom Settings 2.0 field registrations and AJAX handlers.
 *
 * @package BuddyBossPro/Integration/Zoom
 * @since   3.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Register Settings 2.0 hooks.
// This file is loaded from bb_zoom_register_settings_fields() in bp-zoom-loader.php
// which already checks function_exists('bb_register_feature_field').
bb_zoom_register_settings_panels();
add_action( 'wp_ajax_bb_zoom_save_s2s_credentials', 'bb_zoom_save_s2s_credentials_ajax' );
add_action( 'wp_ajax_bb_zoom_save_sdk_credentials', 'bb_zoom_save_sdk_credentials_ajax' );
add_action( 'wp_ajax_bb_zoom_get_account_emails', 'bb_zoom_get_account_emails_ajax' );
add_action( 'bb_admin_save_feature_settings_after', 'bb_zoom_settings_save', 10, 3 );

/**
 * Register Zoom side panels, sections, and fields for Settings 2.0.
 *
 * Called from bb_zoom_register_settings_fields() during the
 * `bb_after_register_features` action, after the Zoom integration feature
 * itself has been registered.
 *
 * @since 3.0.0
 */
function bb_zoom_register_settings_panels() {

	$feature_id = 'zoom';

	// =========================================================================
	// SIDE PANELS
	// =========================================================================

	// Single panel: Zoom Settings (contains all 3 sections).
	bb_register_side_panel(
		$feature_id,
		'zoom-settings',
		array(
			'title'      => __( 'Zoom Settings', 'buddyboss-pro' ),
			'icon'       => array(
				'type'  => 'font',
				'class' => 'bb-icons-rl bb-icons-rl-gear',
			),
			'order'      => 10,
			'is_default' => true,
		)
	);

	// =========================================================================
	// SECTION 1: Zoom Gutenberg Blocks (in "Zoom Settings" panel)
	// =========================================================================

	$s2s_is_connected = bb_zoom_is_connected();
	$s2s_has_errors   = ! empty( bb_get_zoom_block_settings( 'zoom_errors' ) );
	$s2s_has_warnings = ! empty( bb_get_zoom_block_settings( 'zoom_warnings' ) ) || empty( bb_zoom_account_email() );

	// Determine S2S badge type.
	$s2s_status_type = 'warning';
	if ( $s2s_is_connected ) {
		$s2s_status_type = ( $s2s_has_errors || $s2s_has_warnings ) ? 'warning' : 'success';
	}
	if ( $s2s_has_errors ) {
		$s2s_status_type = 'error';
	}

	bb_register_feature_section(
		$feature_id,
		'zoom-settings',
		'zoom_gutenberg_blocks',
		array(
			'title'       => __( 'Zoom Gutenberg Blocks', 'buddyboss-pro' ),
			'order'       => 10,
			'status'      => array(
				'type' => $s2s_status_type,
				'text' => $s2s_is_connected
					? __( 'Connected', 'buddyboss-pro' )
					: __( 'Not Connected', 'buddyboss-pro' ),
			),
			'description' => __( 'To create Zoom meetings and webinars using Gutenberg blocks, create a Server-to-Server OAuth app in your Zoom account and connect it below.', 'buddyboss-pro' ),
			'help_url'    => bp_get_admin_url(
				add_query_arg(
					array(
						'page'    => 'bp-help',
						'article' => '638231',
					),
					'admin.php'
				)
			),
		)
	);

	// S2S Account ID.
	bb_register_feature_field(
		$feature_id,
		'zoom-settings',
		'zoom_gutenberg_blocks',
		array(
			'name'              => 'bb-zoom-s2s-account-id',
			'label'             => __( 'API Keys', 'buddyboss-pro' ),
			'type'              => 'password',
			'default'           => bb_zoom_account_id(),
			'sanitize_callback' => 'sanitize_text_field',
			'placeholder'       => __( 'Enter Account ID', 'buddyboss-pro' ),
			'group'             => array(
				'key'   => 'zoom_s2s_credentials',
				'label' => __( 'Account ID', 'buddyboss-pro' ),
			),
			'order'             => 10,
		)
	);

	// S2S Client ID.
	bb_register_feature_field(
		$feature_id,
		'zoom-settings',
		'zoom_gutenberg_blocks',
		array(
			'name'              => 'bb-zoom-s2s-client-id',
			'label'             => '',
			'type'              => 'password',
			'default'           => bb_zoom_client_id(),
			'sanitize_callback' => 'sanitize_text_field',
			'placeholder'       => __( 'Enter Client ID', 'buddyboss-pro' ),
			'group'             => array(
				'key'   => 'zoom_s2s_credentials',
				'label' => __( 'Client ID', 'buddyboss-pro' ),
			),
			'order'             => 20,
		)
	);

	// S2S Client Secret.
	bb_register_feature_field(
		$feature_id,
		'zoom-settings',
		'zoom_gutenberg_blocks',
		array(
			'name'              => 'bb-zoom-s2s-client-secret',
			'label'             => '',
			'type'              => 'password',
			'default'           => bb_zoom_client_secret(),
			'sanitize_callback' => 'sanitize_text_field',
			'placeholder'       => __( 'Enter Client Secret', 'buddyboss-pro' ),
			'group'             => array(
				'key'   => 'zoom_s2s_credentials',
				'label' => __( 'Client Secret', 'buddyboss-pro' ),
			),
			'order'             => 30,
		)
	);

	// Account Email — select dropdown populated from API after S2S validation.
	// Legacy: disabled when single account, enabled when multiple accounts.
	$account_emails = bb_get_zoom_account_emails();
	$account_email  = bb_zoom_account_email();
	$email_options  = array();

	if ( ! empty( $account_emails ) ) {
		foreach ( $account_emails as $email_key => $email_label ) {
			$email_options[] = array(
				'value' => $email_key,
				'label' => $email_label,
			);
		}
	}

	// Disabled when 0 or 1 accounts (same as legacy `is-disabled` class).
	$email_disabled = ( count( $account_emails ) <= 1 );

	bb_register_feature_field(
		$feature_id,
		'zoom-settings',
		'zoom_gutenberg_blocks',
		array(
			'name'              => 'bb-zoom-account-email',
			'label'             => '',
			'type'              => 'select',
			'default'           => $account_email,
			'sanitize_callback' => 'sanitize_email',
			'options'           => ! empty( $email_options ) ? $email_options : array(
				array(
					'value' => '',
					'label' => __( '- Select a Zoom account -', 'buddyboss-pro' ),
				),
			),
			'disabled'          => $email_disabled,
			'field_class'       => 'bb-admin-settings-form__field--select-full',
			'group'             => array(
				'key'   => 'zoom_s2s_credentials',
				'label' => __( 'Account Email', 'buddyboss-pro' ),
			),
			// Fetch account emails from Zoom API when all 3 S2S credential fields are filled.
			'fetch_on_change'   => array(
				'fields'         => array( 'bb-zoom-s2s-account-id', 'bb-zoom-s2s-client-id', 'bb-zoom-s2s-client-secret' ),
				'require_all'    => true,
				'ajax_action'    => 'bb_zoom_get_account_emails',
				'debounce'       => 500,
				'loading_text'   => __( 'Fetching Zoom accounts...', 'buddyboss-pro' ),
				'disable_fields' => array( '_bb_zoom_save_s2s_credentials' ),
			),
			'order'             => 40,
		)
	);

	// S2S Secret Token.
	bb_register_feature_field(
		$feature_id,
		'zoom-settings',
		'zoom_gutenberg_blocks',
		array(
			'name'              => 'bb-zoom-s2s-secret-token',
			'label'             => '',
			'type'              => 'password',
			'default'           => bb_zoom_secret_token(),
			'sanitize_callback' => 'sanitize_text_field',
			'placeholder'       => __( 'Enter Secret Token', 'buddyboss-pro' ),
			'group'             => array(
				'key'   => 'zoom_s2s_credentials',
				'label' => __( 'Secret Token', 'buddyboss-pro' ),
			),
			'order'             => 50,
		)
	);

	// S2S Update button — bb_verify_popup always sends current field values.
	bb_register_feature_field(
		$feature_id,
		'zoom-settings',
		'zoom_gutenberg_blocks',
		array(
			'name'              => '_bb_zoom_save_s2s_credentials',
			'label'             => '',
			'type'              => 'bb_verify_popup',
			'button_label'      => __( 'Update', 'buddyboss-pro' ),
			'ajax_action'       => 'bb_zoom_save_s2s_credentials',
			'related_fields'    => array(
				'bb-zoom-s2s-account-id',
				'bb-zoom-s2s-client-id',
				'bb-zoom-s2s-client-secret',
				'bb-zoom-account-email',
				'bb-zoom-s2s-secret-token',
			),
			'is_connected'      => $s2s_is_connected,
			'verify_config'     => array(
				'modal_title'     => __( 'Verify Zoom Gutenberg Blocks', 'buddyboss-pro' ),
				'loading_message' => __( 'Verifying Zoom credentials', 'buddyboss-pro' ),
				'loading_icon'    => 'bb-icon-f bb-icon-brand-zoom',
			),
			'sanitize_callback' => '__return_empty_string',
			'group'             => 'zoom_s2s_credentials',
			'order'             => 60,
		)
	);

	// Webhook URL (read-only with copy). Disabled when S2S not connected.
	$webhook_url = bb_zoom_notification_url();

	bb_register_feature_field(
		$feature_id,
		'zoom-settings',
		'zoom_gutenberg_blocks',
		array(
			'name'              => 'bb-zoom-webhook-url',
			'label'             => __( 'Notification URL', 'buddyboss-pro' ),
			'type'              => 'text',
			'default'           => $webhook_url,
			'sanitize_callback' => '__return_empty_string',
			'disabled'          => ! $s2s_is_connected, // Disable when S2S not connected.
			'description'       => __( 'Enter as the Event notification endpoint URL when configuring Event Subscriptions in your Zoom app\'s settings.', 'buddyboss-pro' ),
			'field_class'       => 'bb-admin-settings-field--full-control-width bb-admin-settings-form__field--copy',
			// React's evaluateCondition() treats boolean `value` specially (truthy/falsy
			// comparison) so any stored 0/1 integer or "0"/"1" string is compared correctly.
			// Do not change to `1` — that would switch React to strict equality and break
			// the match when the runtime value type differs from the expected type.
			'conditional'       => array(
				'field'  => '_bb_zoom_s2s_is_connected',
				'value'  => true,
				'action' => 'disable',
			),
			'order'             => 70,
		)
	);

	// Hidden field to track S2S connection state for conditionals.
	// Stored as 0/1 integer so React conditionals can do strict equality.
	// The field is never meant to persist — bb_zoom_settings_save() deletes the
	// zombie wp_options row created by the core AJAX save pipeline.
	bb_register_feature_field(
		$feature_id,
		'zoom-settings',
		'zoom_gutenberg_blocks',
		array(
			'name'              => '_bb_zoom_s2s_is_connected',
			'label'             => '',
			'type'              => 'hidden',
			'default'           => $s2s_is_connected ? 1 : 0,
			'sanitize_callback' => 'absint',
			'order'             => 1,
		)
	);

	// =========================================================================
	// SECTION 2: Zoom In-Browser Meetings (in "Zoom Settings" panel)
	// =========================================================================

	$sdk_is_connected = bb_zoom_is_sdk_connected();
	$sdk_has_errors   = ! empty( bb_get_zoom_block_settings( 'zoom_sdk_errors' ) );

	$sdk_status_type = 'warning';
	if ( $sdk_is_connected ) {
		$sdk_status_type = 'success';
	}
	if ( $sdk_has_errors ) {
		$sdk_status_type = 'error';
	}

	bb_register_feature_section(
		$feature_id,
		'zoom-settings',
		'zoom_in_browser_meetings',
		array(
			'title'       => __( 'Zoom In-Browser Meetings', 'buddyboss-pro' ),
			'order'       => 20,
			'status'      => array(
				'type' => $sdk_status_type,
				'text' => $sdk_is_connected
					? __( 'Connected', 'buddyboss-pro' )
					: __( 'Not Connected', 'buddyboss-pro' ),
			),
			'description' => __( 'To require members attend your Zoom meetings and webinars directly on your site, create a Meeting SDK app in your Zoom account and connect it below. When enabled, members will not be able to attend using the Zoom app.', 'buddyboss-pro' ),
			'notice'      => sprintf(
				'%1$s %2$s',
				esc_html__( 'For webinars, hosts will be required to join the webinar using the Zoom app and only authenticated users will be able to join.', 'buddyboss-pro' ),
				esc_html__( 'Members will not be able to register for webinars on your site or participate in polls while accessing webinars through their browser.', 'buddyboss-pro' )
			),
			'help_url'    => bp_get_admin_url(
				add_query_arg(
					array(
						'page'    => 'bp-help',
						'article' => '638233',
					),
					'admin.php'
				)
			),
		)
	);

	// SDK Client ID.
	bb_register_feature_field(
		$feature_id,
		'zoom-settings',
		'zoom_in_browser_meetings',
		array(
			'name'              => 'bb-zoom-sdk-client-id',
			'label'             => __( 'API Keys', 'buddyboss-pro' ),
			'type'              => 'password',
			'default'           => bb_zoom_sdk_client_id(),
			'sanitize_callback' => 'sanitize_text_field',
			'placeholder'       => __( 'Enter Client ID', 'buddyboss-pro' ),
			'group'             => array(
				'key'   => 'zoom_sdk_credentials',
				'label' => __( 'Client ID', 'buddyboss-pro' ),
			),
			'order'             => 10,
		)
	);

	// SDK Client Secret.
	bb_register_feature_field(
		$feature_id,
		'zoom-settings',
		'zoom_in_browser_meetings',
		array(
			'name'              => 'bb-zoom-sdk-client-secret',
			'label'             => '',
			'type'              => 'password',
			'default'           => bb_zoom_sdk_client_secret(),
			'sanitize_callback' => 'sanitize_text_field',
			'placeholder'       => __( 'Enter Client Secret', 'buddyboss-pro' ),
			'group'             => array(
				'key'   => 'zoom_sdk_credentials',
				'label' => __( 'Client Secret', 'buddyboss-pro' ),
			),
			'order'             => 20,
		)
	);

	// SDK Update button — bb_verify_popup always sends current field values.
	bb_register_feature_field(
		$feature_id,
		'zoom-settings',
		'zoom_in_browser_meetings',
		array(
			'name'              => '_bb_zoom_save_sdk_credentials',
			'label'             => '',
			'type'              => 'bb_verify_popup',
			'button_label'      => __( 'Update', 'buddyboss-pro' ),
			'ajax_action'       => 'bb_zoom_save_sdk_credentials',
			'related_fields'    => array(
				'bb-zoom-sdk-client-id',
				'bb-zoom-sdk-client-secret',
			),
			'is_connected'      => $sdk_is_connected,
			'verify_config'     => array(
				'modal_title'     => __( 'Verify Zoom In-Browser Meetings', 'buddyboss-pro' ),
				'loading_message' => __( 'Verifying Zoom credentials', 'buddyboss-pro' ),
				'loading_icon'    => 'bb-icon-f bb-icon-brand-zoom',
			),
			'sanitize_callback' => '__return_empty_string',
			'group'             => 'zoom_sdk_credentials',
			'order'             => 30,
		)
	);

	// "Enabled for" radio. Disabled when SDK not connected.
	bb_register_feature_field(
		$feature_id,
		'zoom-settings',
		'zoom_in_browser_meetings',
		array(
			'name'              => 'bb-zoom-meeting-hide-zoom-urls',
			'label'             => __( 'Enabled for', 'buddyboss-pro' ),
			'type'              => 'radio',
			'field_class'       => 'bb-admin-settings-field__radio--vertical',
			'default'           => bb_get_zoom_meeting_hide_url_enabled( 'meetings-webinar' ),
			'sanitize_callback' => 'sanitize_text_field',
			'options'           => array(
				array(
					'label' => __( 'Meetings and Webinars', 'buddyboss-pro' ),
					'value' => 'meetings-webinar',
				),
				array(
					'label' => __( 'Meetings', 'buddyboss-pro' ),
					'value' => 'meetings',
				),
				array(
					'label' => __( 'Webinars', 'buddyboss-pro' ),
					'value' => 'webinar',
				),
				array(
					'label' => __( 'None', 'buddyboss-pro' ),
					'value' => 'none',
				),
			),
			'disabled'          => ! $sdk_is_connected, // Disable when SDK not connected.
			// Boolean `value` triggers React's truthy/falsy comparison path, which
			// correctly matches 1/0/"1"/"0" regardless of runtime type.
			'conditional'       => array(
				'field'  => '_bb_zoom_sdk_is_connected',
				'value'  => true,
				'action' => 'disable',
			),
			'order'             => 40,
		)
	);

	// Hidden field to track SDK connection state for conditionals.
	// Stored as 0/1 integer; zombie row is cleaned by bb_zoom_settings_save().
	bb_register_feature_field(
		$feature_id,
		'zoom-settings',
		'zoom_in_browser_meetings',
		array(
			'name'              => '_bb_zoom_sdk_is_connected',
			'label'             => '',
			'type'              => 'hidden',
			'default'           => $sdk_is_connected ? 1 : 0,
			'sanitize_callback' => 'absint',
			'order'             => 1,
		)
	);

	// Webinar caveat notice — rendered as a plain section-foot description so
	// it survives the React migration (the section-level `notice` arg above is
	// kept for legacy parity but is not consumed by FeatureSettingsScreen).
	bb_register_feature_field(
		$feature_id,
		'zoom-settings',
		'zoom_in_browser_meetings',
		array(
			'name'              => '_bb_zoom_sdk_webinar_notice',
			'label'             => '',
			'type'              => 'notice',
			'notice_type'       => 'plain',
			'description'       => sprintf(
				'%1$s %2$s',
				esc_html__( 'For webinars, hosts will be required to join the webinar using the Zoom app and only authenticated users will be able to join.', 'buddyboss-pro' ),
				esc_html__( 'Members will not be able to register for webinars on your site or participate in polls while accessing webinars through their browser.', 'buddyboss-pro' )
			),
			'sanitize_callback' => '__return_empty_string',
			'full_width'        => true,
			'order'             => 50,
		)
	);

	// =========================================================================
	// SECTION 3: Zoom Settings (in "Zoom Settings" panel)
	// =========================================================================

	bb_register_feature_section(
		$feature_id,
		'zoom-settings',
		'zoom_settings',
		array(
			'title'    => __( 'Zoom Settings', 'buddyboss-pro' ),
			'order'    => 30,
			'help_url' => bp_get_admin_url(
				add_query_arg(
					array(
						'page'    => 'bp-help',
						'article' => '638235',
					),
					'admin.php'
				)
			),
		)
	);

	// Social Groups toggle.
	$groups_active = bp_is_active( 'groups' );

	bb_register_feature_field(
		$feature_id,
		'zoom-settings',
		'zoom_settings',
		array(
			'name'              => 'bp-zoom-enable-groups',
			'label'             => __( 'Social Groups', 'buddyboss-pro' ),
			'type'              => 'toggle',
			'default'           => $groups_active ? (int) bp_get_option( 'bp-zoom-enable-groups', 0 ) : 0,
			'sanitize_callback' => 'absint',
			'description'       => __( 'Allow Zoom meetings in social groups', 'buddyboss-pro' ),
			'help_text'         => ! $groups_active
				? __( 'The Social Groups component must be active to enable this setting.', 'buddyboss-pro' )
				: __( 'Allow group organizers to connect their Zoom account to their groups, in order to create and synchronize meetings and webinars with the groups.', 'buddyboss-pro' ),
			'disabled'          => ! $groups_active,
			'order'             => 10,
		)
	);

	// Recordings toggle (grouped with Recording Links checkbox).
	bb_register_feature_field(
		$feature_id,
		'zoom-settings',
		'zoom_settings',
		array(
			'name'              => 'bp-zoom-enable-recordings',
			'label'             => __( 'Recordings', 'buddyboss-pro' ),
			'type'              => 'toggle',
			'default'           => (int) bp_get_option( 'bp-zoom-enable-recordings', 1 ),
			'sanitize_callback' => 'absint',
			'description'       => __( 'Display Zoom recordings for past meetings', 'buddyboss-pro' ),
			'group'             => array(
				'key'   => 'zoom_recordings',
				'label' => '',
			),
			'order'             => 20,
		)
	);

	// Recording Links checkbox (child of Recordings, same group). Disabled when Recordings disabled.
	bb_register_feature_field(
		$feature_id,
		'zoom-settings',
		'zoom_settings',
		array(
			'name'              => 'bp-zoom-enable-recordings-links',
			'label'             => '',
			'type'              => 'checkbox',
			'default'           => (int) bp_get_option( 'bp-zoom-enable-recordings-links', 1 ),
			'sanitize_callback' => 'absint',
			'description'       => __( "Display buttons to 'Download' recording, and to 'Copy Link' to the recording", 'buddyboss-pro' ),
			// Boolean `value` uses React's truthy/falsy path, matching 1/0/"1"/"0".
			'conditional'       => array(
				'field'  => 'bp-zoom-enable-recordings',
				'value'  => true,
				'action' => 'disable',
			),
			'group'             => 'zoom_recordings',
			'order'             => 30,
		)
	);

	/**
	 * Fires after Zoom Settings 2.0 fields have been registered.
	 *
	 * Third-party plugins can hook this to register additional fields in
	 * the Zoom feature's side panel via bb_register_feature_field().
	 *
	 * @since 3.0.0
	 */
	do_action( 'bb_zoom_after_register_settings_fields' );
}

/**
 * Handle S2S credential save via separate AJAX endpoint.
 *
 * Replicates the legacy S2S save logic from BP_Zoom_Admin_Integration_Tab::settings_save()
 * (bp-zoom-admin-tab.php lines 94-168) for the Settings 2.0 React admin.
 *
 * @since 3.0.0
 */
function bb_zoom_save_s2s_credentials_ajax() {

	if ( ! bp_current_user_can( 'bp_moderate' ) ) {
		wp_send_json_error(
			array(
				'message' => esc_html__( 'You do not have permission to perform this action.', 'buddyboss-pro' ),
			)
		);
		return;
	}

	check_ajax_referer( 'bb_admin_settings', 'nonce' );

	// Sanitize inputs.
	// Account ID: Numeric Zoom account identifier (e.g., "123456789").
	$account_id = isset( $_POST['bb-zoom-s2s-account-id'] ) ? sanitize_text_field( wp_unslash( $_POST['bb-zoom-s2s-account-id'] ) ) : '';
	// Client ID: OAuth credential from Zoom App Marketplace.
	$client_id = isset( $_POST['bb-zoom-s2s-client-id'] ) ? sanitize_text_field( wp_unslash( $_POST['bb-zoom-s2s-client-id'] ) ) : '';
	// Client Secret: OAuth credential (sensitive, never logged).
	$client_secret = isset( $_POST['bb-zoom-s2s-client-secret'] ) ? sanitize_text_field( wp_unslash( $_POST['bb-zoom-s2s-client-secret'] ) ) : '';
	// Account Email: Email address of Zoom account owner.
	$account_email = isset( $_POST['bb-zoom-account-email'] ) ? sanitize_email( wp_unslash( $_POST['bb-zoom-account-email'] ) ) : '';
	// Secret Token: Webhook validation token for Zoom event notifications.
	$secret_token = isset( $_POST['bb-zoom-s2s-secret-token'] ) ? sanitize_text_field( wp_unslash( $_POST['bb-zoom-s2s-secret-token'] ) ) : '';

	// Read existing settings and merge.
	$settings = bb_get_zoom_block_settings();

	// Capture the prior account email before we overwrite it below. Needed
	// after a successful validation to detect an account email change.
	$old_account_email = isset( $settings['account-email'] ) ? $settings['account-email'] : '';

	// Set submitted values.
	$settings['s2s-account-id']    = $account_id;
	$settings['s2s-client-id']     = $client_id;
	$settings['s2s-client-secret'] = $client_secret;
	$settings['account-email']     = $account_email;
	$settings['s2s-secret-token']  = $secret_token;

	// Reset connection state.
	// NOTE: 'sidewide_errors' is a historical typo in bb-zoom; preserved for backward compatibility.
	$settings['zoom_errors']                = array();
	$settings['zoom_warnings']              = array();
	$settings['sidewide_errors']            = array();
	$settings['account_host']               = '';
	$settings['account_host_user']          = array();
	$settings['account_host_user_settings'] = array();
	$settings['zoom_is_connected']          = false;

	$response_errors   = array();
	$response_warnings = array();

	// Detect disconnect action: all credential fields are empty.
	$is_disconnecting = empty( $account_id ) && empty( $client_id ) && empty( $client_secret );

	// Only clear the account emails list on an explicit disconnect. Clearing it
	// eagerly before validation would wipe the existing dropdown on any partial
	// credential typo, even though the prior connection data is still valid.
	if ( $is_disconnecting ) {
		bp_update_option( 'bb-zoom-account-emails', array() );
	}

	// Validate and connect if all required fields are present.
	if (
		! empty( $account_id ) &&
		! empty( $client_id ) &&
		! empty( $client_secret )
	) {
		$fetch_data = bb_zoom_fetch_account_emails(
			array(
				'account_id'    => $account_id,
				'client_id'     => $client_id,
				'client_secret' => $client_secret,
				'account_email' => $account_email,
				'force_api'     => true,
			)
		);

		if ( is_wp_error( $fetch_data ) ) {
			$settings['zoom_errors'][] = $fetch_data;
			$settings['account-email'] = '';
			$response_errors[]         = $fetch_data->get_error_message();
		} elseif ( ! empty( $fetch_data ) && ! is_wp_error( $fetch_data ) ) {
			$settings['zoom_is_connected'] = true;

			$email = $settings['account-email'];
			if ( ! array_key_exists( $email, $fetch_data ) ) {
				$warning                     = new WP_Error( 'email_not_found', __( 'Email not found in Zoom account.', 'buddyboss-pro' ) );
				$settings['zoom_warnings'][] = $warning;
				$settings['account-email']   = '';
				$response_warnings[]         = $warning->get_error_message();
			}

			$settings['account_host_user']          = get_transient( 'bp_zoom_account_host_user' );
			$settings['account_host_user_settings'] = get_transient( 'bp_zoom_account_host_user_settings' );
			$is_webinar_enabled                     = get_transient( 'bp_zoom_is_webinar_enabled' );

			// Check webinar is enabled or not.
			if ( true === $is_webinar_enabled ) {
				bp_update_option( 'bp-zoom-enable-webinar', true );
			} else {
				bp_delete_option( 'bp-zoom-enable-webinar' );
			}

			// Delete transients.
			delete_transient( 'bp_zoom_account_host_user' );
			delete_transient( 'bp_zoom_account_host_user_settings' );
			delete_transient( 'bp_zoom_is_webinar_enabled' );

			// Hide/Un-hide group meetings/webinars when account email changes.
			if (
				! empty( $settings['account-email'] ) &&
				$old_account_email !== $settings['account-email']
			) {
				bb_zoom_group_update_site_connection_group_meetings( $settings['account-email'], $old_account_email );
			}
		}
	} elseif ( ! $is_disconnecting ) {
		// Not all required fields are present.
		if ( empty( $account_id ) ) {
			$error                     = new WP_Error( 'no_zoom_account_id', __( 'The Account ID is required.', 'buddyboss-pro' ) );
			$settings['zoom_errors'][] = $error;
			$response_errors[]         = $error->get_error_message();
		} elseif ( empty( $client_id ) ) {
			$error                     = new WP_Error( 'no_zoom_client_id', __( 'The Client ID is required.', 'buddyboss-pro' ) );
			$settings['zoom_errors'][] = $error;
			$response_errors[]         = $error->get_error_message();
		} elseif ( empty( $client_secret ) ) {
			$error                     = new WP_Error( 'no_zoom_client_secret', __( 'The Client Secret is required.', 'buddyboss-pro' ) );
			$settings['zoom_errors'][] = $error;
			$response_errors[]         = $error->get_error_message();
		}
	}

	// Save the merged settings.
	bp_update_option( 'bb-zoom', $settings );

	// Determine connection state for response.
	$is_connected = ! empty( $settings['zoom_is_connected'] );
	$has_errors   = ! empty( $response_errors );
	$has_warnings = ! empty( $response_warnings );

	// Determine section status badge.
	$status_type = 'warning';
	if ( $is_connected ) {
		$status_type = ( $has_warnings || $has_errors ) ? 'warning' : 'success';
	}
	if ( $has_errors ) {
		$status_type = 'error';
	}

	// Build message.
	$message = '';
	if ( $is_disconnecting ) {
		$message = esc_html__( 'Zoom credentials cleared successfully.', 'buddyboss-pro' );
	} elseif ( $has_errors ) {
		$message = esc_html( reset( $response_errors ) );
	} elseif ( $has_warnings ) {
		$message = esc_html( reset( $response_warnings ) );
	} elseif ( $is_connected && empty( $message ) ) {
		$message = esc_html__( 'Zoom Gutenberg Blocks connected successfully.', 'buddyboss-pro' );
	}

	// Get account emails for React to update the email field.
	$account_emails = bp_get_option( 'bb-zoom-account-emails', array() );

	// Build the email select's option list so React refreshes the SELECT after
	// connect/disconnect. Without this, the React state retains the prior
	// page-load options array — on disconnect the form value clears to '' but
	// the SELECT still renders the stale email as its first option, which
	// looks to the admin like the disconnect didn't take. Mirrors the same
	// structure produced at field registration time (see line 162-189 above).
	$email_options = array();
	if ( ! empty( $account_emails ) ) {
		foreach ( $account_emails as $email_key => $email_label ) {
			$email_options[] = array(
				'value' => $email_key,
				'label' => $email_label,
			);
		}
	} else {
		$email_options[] = array(
			'value' => '',
			'label' => __( '- Select a Zoom account -', 'buddyboss-pro' ),
		);
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
			'_bb_zoom_s2s_is_connected' => $is_connected ? 1 : 0,
			'bb-zoom-account-email'     => $settings['account-email'],
		),
		'field_options'  => array(
			'bb-zoom-account-email' => $email_options,
		),
	);

	// Include debug details only when WP_DEBUG is enabled — never exposed in production.
	if ( defined( 'WP_DEBUG' ) && WP_DEBUG && ! $is_connected && ! $is_disconnecting ) {
		$response_data['debug'] = array(
			'errors'   => $response_errors,
			'warnings' => $response_warnings,
		);
	}

	// bb_verify_popup: success = green checkmark, error = red X.
	// On disconnect, return success even though $is_connected is false.
	if ( $is_connected || $is_disconnecting ) {
		wp_send_json_success( $response_data );
	} else {
		wp_send_json_error( $response_data );
	}
}

/**
 * Handle Meeting SDK credential save via separate AJAX endpoint.
 *
 * Replicates the legacy SDK save logic from BP_Zoom_Admin_Integration_Tab::settings_save()
 * (bp-zoom-admin-tab.php lines 191-211) for the Settings 2.0 React admin.
 *
 * @since 3.0.0
 */
function bb_zoom_save_sdk_credentials_ajax() {

	if ( ! bp_current_user_can( 'bp_moderate' ) ) {
		wp_send_json_error(
			array(
				'message' => esc_html__( 'You do not have permission to perform this action.', 'buddyboss-pro' ),
			)
		);
		return;
	}

	check_ajax_referer( 'bb_admin_settings', 'nonce' );

	// Sanitize inputs.
	$sdk_client_id     = isset( $_POST['bb-zoom-sdk-client-id'] ) ? sanitize_text_field( wp_unslash( $_POST['bb-zoom-sdk-client-id'] ) ) : '';
	$sdk_client_secret = isset( $_POST['bb-zoom-sdk-client-secret'] ) ? sanitize_text_field( wp_unslash( $_POST['bb-zoom-sdk-client-secret'] ) ) : '';

	// Read existing settings and merge.
	$settings = bb_get_zoom_block_settings();

	// Set submitted values.
	$settings['meeting-sdk-client-id']     = $sdk_client_id;
	$settings['meeting-sdk-client-secret'] = $sdk_client_secret;

	// Reset SDK connection state.
	$settings['zoom_sdk_is_connected'] = false;
	$settings['zoom_sdk_errors']       = array();
	$settings['zoom_sdk_warning']      = array();

	$response_errors = array();

	// Detect disconnect: both creds cleared by the React verify popup. Skip
	// the Meeting SDK validation API entirely and treat the empty payload as
	// a successful disconnect — otherwise the response falls through to the
	// error path and the modal renders "Verification failed" even though the
	// credentials were intentionally cleared. Mirrors the S2S handler above
	// and the OneSignal fix in commit a4384c17.
	$is_disconnecting = empty( $sdk_client_id ) && empty( $sdk_client_secret );

	// Validate and connect if both fields are present.
	if (
		! empty( $sdk_client_id ) &&
		! empty( $sdk_client_secret )
	) {
		$validate = bp_zoom_conference()->bb_zoom_validate_meeting_sdk( $sdk_client_id, $sdk_client_secret );

		if ( true === $validate ) {
			$settings['zoom_sdk_is_connected'] = true;
		} else {
			$settings['zoom_sdk_is_connected'] = false;
			if ( is_wp_error( $validate ) ) {
				$settings['zoom_sdk_errors'][] = $validate;
				$response_errors[]             = $validate->get_error_message();
			}
		}

		delete_transient( 'bb-zoom-meeting-sdk-validate' );
	}

	// Save the merged settings.
	bp_update_option( 'bb-zoom', $settings );

	// Determine connection state for response.
	$is_connected = ! empty( $settings['zoom_sdk_is_connected'] );
	$has_errors   = ! empty( $response_errors );

	// Determine section status badge.
	$status_type = 'warning';
	if ( $is_connected ) {
		$status_type = 'success';
	}
	if ( $has_errors ) {
		$status_type = 'error';
	}

	// Build message.
	$message = '';
	if ( $is_disconnecting ) {
		$message = esc_html__( 'Zoom In-Browser Meetings disconnected.', 'buddyboss-pro' );
	} elseif ( $is_connected ) {
		$message = esc_html__( 'Zoom In-Browser Meetings connected successfully.', 'buddyboss-pro' );
	} elseif ( $has_errors ) {
		$message = esc_html( reset( $response_errors ) );
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
			'_bb_zoom_sdk_is_connected' => $is_connected ? 1 : 0,
		),
	);

	// Include debug details when WP_DEBUG is enabled.
	if ( defined( 'WP_DEBUG' ) && WP_DEBUG && ! $is_connected && ! $is_disconnecting ) {
		$response_data['debug'] = array(
			'errors' => $response_errors,
		);
	}

	// bb_verify_popup: success = green checkmark, error = red X.
	// On disconnect, return success even though $is_connected is false.
	if ( $is_connected || $is_disconnecting ) {
		wp_send_json_success( $response_data );
	} else {
		wp_send_json_error( $response_data );
	}
}

/**
 * Handle custom save logic for Zoom settings in Settings 2.0.
 *
 * Credential fields are handled by their respective Update button AJAX handlers,
 * so we intercept them here to prevent the standard save pipeline from
 * persisting them as standalone options.
 *
 * The "Zoom Settings" panel fields (groups, recordings, recording links) and
 * the "meeting-hide-zoom-urls" radio are standard options saved by the core
 * pipeline via bp_update_option(), so they do not need custom save handling.
 *
 * In addition, the core save pipeline does not distinguish saveable from
 * display/button field types, so this handler also deletes the zombie rows it
 * creates for the webhook URL display, verify-popup buttons, and hidden
 * state-sync fields registered in this feature.
 *
 * @since 3.0.0
 *
 * @param string $feature_id Feature ID.
 * @param array  $settings   Full submitted settings.
 * @param array  $saved      Keys and values already saved by core pipeline.
 */
function bb_zoom_settings_save( $feature_id, $settings, $saved ) {

	if ( 'zoom' !== $feature_id ) {
		return;
	}

	// Credential fields are saved via their AJAX Update buttons.
	// If they were submitted through auto-save, redirect them to the serialized option.
	$s2s_credential_keys = array(
		'bb-zoom-s2s-account-id'    => 's2s-account-id',
		'bb-zoom-s2s-client-id'     => 's2s-client-id',
		'bb-zoom-s2s-client-secret' => 's2s-client-secret',
		'bb-zoom-account-email'     => 'account-email',
		'bb-zoom-s2s-secret-token'  => 's2s-secret-token',
	);

	$sdk_credential_keys = array(
		'bb-zoom-sdk-client-id'     => 'meeting-sdk-client-id',
		'bb-zoom-sdk-client-secret' => 'meeting-sdk-client-secret',
	);

	$all_credential_keys = array_merge( $s2s_credential_keys, $sdk_credential_keys );
	$needs_update        = false;
	$update              = array();

	foreach ( $all_credential_keys as $field_key => $option_key ) {
		if ( array_key_exists( $field_key, $saved ) ) {
			$needs_update          = true;
			$update[ $option_key ] = $saved[ $field_key ];
			// Remove standalone option that the core pipeline saved.
			bp_delete_option( $field_key );
		}
	}

	// Handle meeting-hide-zoom-urls: it is part of the serialized bb-zoom option, not a standalone option.
	if ( array_key_exists( 'bb-zoom-meeting-hide-zoom-urls', $saved ) ) {
		$needs_update                     = true;
		$update['meeting-hide-zoom-urls'] = $saved['bb-zoom-meeting-hide-zoom-urls'];
		// Remove standalone option that the core pipeline saved.
		bp_delete_option( 'bb-zoom-meeting-hide-zoom-urls' );
	}

	// Delete non-saveable fields that the core AJAX pipeline persists as zombie options:
	// - Display-only fields (webhook URL is a computed value, not user input).
	// - bb_verify_popup Update buttons (their state is ephemeral).
	// - Hidden connection-state mirrors (derived from bb-zoom on every render).
	$non_saveable_keys = array(
		'bb-zoom-webhook-url',
		'_bb_zoom_save_s2s_credentials',
		'_bb_zoom_save_sdk_credentials',
		'_bb_zoom_s2s_is_connected',
		'_bb_zoom_sdk_is_connected',
	);
	foreach ( $non_saveable_keys as $zombie_key ) {
		if ( array_key_exists( $zombie_key, $saved ) ) {
			bp_delete_option( $zombie_key );
		}
	}

	// Single write: read once, merge all changes, write once to avoid race conditions.
	if ( $needs_update && ! empty( $update ) ) {
		$zoom_settings = bb_get_zoom_block_settings();
		$zoom_settings = array_merge( $zoom_settings, $update );
		bp_update_option( 'bb-zoom', $zoom_settings );
	}
}

/**
 * Settings 2.0: Fetch Zoom account emails via AJAX for React select field.
 *
 * Separate AJAX action from the legacy `zoom_api_get_account_emails` which is
 * also used by the frontend group wizard. This admin-only handler requires
 * `bp_moderate` capability and the `bb_admin_settings` nonce.
 *
 * @since 3.0.0
 */
function bb_zoom_get_account_emails_ajax() {

	if ( ! bp_current_user_can( 'bp_moderate' ) ) {
		wp_send_json_error( array( 'message' => esc_html__( 'You do not have permission to perform this action.', 'buddyboss-pro' ) ) );
		return;
	}

	check_ajax_referer( 'bb_admin_settings', 'nonce' );

	// fetch_on_change sends field values using their registered field names.
	$account_id    = isset( $_POST['bb-zoom-s2s-account-id'] ) ? sanitize_text_field( wp_unslash( $_POST['bb-zoom-s2s-account-id'] ) ) : '';
	$client_id     = isset( $_POST['bb-zoom-s2s-client-id'] ) ? sanitize_text_field( wp_unslash( $_POST['bb-zoom-s2s-client-id'] ) ) : '';
	$client_secret = isset( $_POST['bb-zoom-s2s-client-secret'] ) ? sanitize_text_field( wp_unslash( $_POST['bb-zoom-s2s-client-secret'] ) ) : '';

	if ( empty( $account_id ) || empty( $client_id ) || empty( $client_secret ) ) {
		wp_send_json_error( array( 'message' => esc_html__( 'All three S2S credentials are required.', 'buddyboss-pro' ) ) );
		return;
	}

	// Snapshot state BEFORE the API call. The Zoom conference API class writes
	// zoom_is_connected=false to the DB when token generation fails
	// (class-bp-zoom-conference-api.php ~line 1060), and also populates
	// `bb-zoom-account-emails` and three transients as a side effect of a
	// successful probe. This fetch is a read-only probe — it must not corrupt
	// any stored state, since the user has not yet pressed Save.
	$original_zoom_settings       = bp_get_option( 'bb-zoom', array() );
	$original_zoom_account_emails = bp_get_option( 'bb-zoom-account-emails', array() );
	$original_account_host_user   = get_transient( 'bp_zoom_account_host_user' );
	$original_host_user_settings  = get_transient( 'bp_zoom_account_host_user_settings' );
	$original_is_webinar_enabled  = get_transient( 'bp_zoom_is_webinar_enabled' );

	$options = bb_zoom_fetch_account_emails(
		array(
			'account_id'    => $account_id,
			'client_id'     => $client_id,
			'client_secret' => $client_secret,
			'force_api'     => true,
			'error_type'    => 'bool',
		)
	);

	// Restore any state the probe may have mutated.
	$current_zoom_settings = bp_get_option( 'bb-zoom', array() );
	if ( $current_zoom_settings !== $original_zoom_settings ) {
		bp_update_option( 'bb-zoom', $original_zoom_settings );
	}

	$current_zoom_account_emails = bp_get_option( 'bb-zoom-account-emails', array() );
	if ( $current_zoom_account_emails !== $original_zoom_account_emails ) {
		bp_update_option( 'bb-zoom-account-emails', $original_zoom_account_emails );
	}

	// Transients: only restore if the probe set them and we had no prior value,
	// or if the value changed. Never leave probe-populated transients behind.
	if ( false === $original_account_host_user ) {
		delete_transient( 'bp_zoom_account_host_user' );
	} else {
		set_transient( 'bp_zoom_account_host_user', $original_account_host_user );
	}
	if ( false === $original_host_user_settings ) {
		delete_transient( 'bp_zoom_account_host_user_settings' );
	} else {
		set_transient( 'bp_zoom_account_host_user_settings', $original_host_user_settings );
	}
	if ( false === $original_is_webinar_enabled ) {
		delete_transient( 'bp_zoom_is_webinar_enabled' );
	} else {
		set_transient( 'bp_zoom_is_webinar_enabled', $original_is_webinar_enabled );
	}

	if ( empty( $options ) || ! is_array( $options ) ) {
		wp_send_json_error( array( 'message' => esc_html__( 'No Zoom account found. Please verify your credentials.', 'buddyboss-pro' ) ) );
		return;
	}

	// Build JSON options array for React SelectControl.
	$json_options    = array();
	$default_email   = bb_zoom_account_email();
	$all_emails      = array_keys( $options );
	$select_disabled = ( count( $options ) <= 1 );

	if ( ! in_array( $default_email, $all_emails, true ) ) {
		$default_email = '';
	}

	foreach ( $options as $email => $label ) {
		// Auto-select default if none previously selected.
		if ( empty( $default_email ) && false !== strpos( $label, 'Default' ) ) {
			$default_email = $email;
		}

		$json_options[] = array(
			'value' => $email,
			'label' => $label,
		);
	}

	wp_send_json_success(
		array(
			'options'       => $json_options,
			'default_value' => $default_email,
			'disabled'      => $select_disabled,
		)
	);
}
