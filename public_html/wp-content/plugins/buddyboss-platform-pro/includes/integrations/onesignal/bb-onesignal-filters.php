<?php
/**
 * OneSignal integration filters
 *
 * @package BuddyBoss\OneSignal
 * @since   2.0.3
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Settings 2.0 path vs legacy path.
if ( function_exists( 'bb_register_feature_field' ) ) {
	// Settings 2.0: register fields into Platform's web push panel.
	add_action( 'bb_notifications_web_push_after_settings_fields', 'bb_onesignal_register_settings_2_fields' );
	// Note: button_only and related_fields are natively handled by the core AJAX handler
	// (class-bb-admin-settings-ajax.php lines 692-693), so no filter extension is needed.
	add_action( 'bb_admin_save_feature_settings_after', 'bb_onesignal_settings_2_save', 10, 3 );
	add_action( 'wp_ajax_bb_onesignal_save_credentials', 'bb_onesignal_save_credentials_ajax' );

} else {
	// Legacy Settings 1.0 path.
	// @todo: Remove after 3 release.
	add_filter( 'bb_notification_web_push_notification_settings', 'bb_onesignal_admin_settings_web_push', 20, 1 );
	add_action( 'bp_admin_tab_setting_save', 'bb_onesignal_web_push_setting_fields_save', 99, 1 );
}

// Avatar AJAX handler filters — needed by both legacy plupload AND Settings 2.0 ImageUploadField.
add_filter( 'bp_core_avatar_ajax_upload_params', 'bb_onesignal_avatar_ajax_upload_params', 20, 1 );
add_filter( 'bp_attachments_current_user_can', 'bb_onesignal_attachments_current_user_can', 20, 3 );
add_filter( 'bp_core_avatar_dir', 'bb_onesignal_bp_core_avatar_dir', 20, 2 );
add_filter( 'bb_avatar_ajax_set_avatar_dir', 'bb_onesignal_notification_set_avatar_dir', 20, 2 );
add_filter( 'bb_avatar_crop_set_avatar_dir', 'bb_onesignal_notification_set_avatar_dir', 20, 2 );

// Legacy-only plupload script data filters — Settings 2.0 ImageUploadField uses its own fetch-based upload.
// @todo: Remove after 3 release.
if ( ! function_exists( 'bb_register_feature_field' ) ) {
	add_filter( 'bp_attachment_avatar_script_data', 'bb_onesignal_attachment_notification_script_data', 10, 2 );
	add_filter( 'bp_attachments_get_plupload_l10n', 'bb_onesignal_attachments_get_plupload_l10n', 20, 1 );
}

add_filter( 'bb_web_notification_enabled', 'bb_onesignal_web_push_enabled', 10, 1 );

add_filter( 'bp_core_do_avatar_handle_crop', 'bb_onesignal_custom_image_handle_crop_remove', 10, 2 );

add_filter( 'bb_pro_onesignal_notification_fire', 'bb_onesignal_manage_web_push_notification', 99, 2 );

/**
 * Added Web Push notification settings.
 *
 * @since 2.0.3
 *
 * @param array $fields Array of fieldsets.
 *
 * @return mixed
 */
function bb_onesignal_admin_settings_web_push( $fields ) {

	if (
		! function_exists( 'bb_enabled_legacy_email_preference' ) ||
		( function_exists( 'bb_enabled_legacy_email_preference' ) && bb_enabled_legacy_email_preference() )
	) {
		return $fields;
	}

	$fields['bb-onesignal-enabled-web-push'] = array(
		'title'             => esc_html__( 'Enable Web Push Notifications', 'buddyboss-pro' ),
		'callback'          => 'bb_onesignal_admin_setting_callback_push_notification_fields',
		'sanitize_callback' => 'intval',
		'args'              => array(),
	);

	if ( bb_pro_should_lock_features() ) {
		return $fields;
	}

	$fields['bb-onesignal-default-notification-icon'] = array(
		'title'    => esc_html__( 'Default Notification Icon', 'buddyboss-pro' ),
		'callback' => 'bb_onesignal_admin_setting_callback_default_notification_icon_fields',
		'args'     => array( 'class' => 'bb-onesignal-default-notification-icon bp-hide' ),
	);

	$fields['bb_web_push_skip_active_members'] = array(
		'title'    => esc_html__( 'Skip Active Members', 'buddyboss-pro' ),
		'callback' => 'bb_onesignal_admin_setting_callback_web_push_skip_active_members',
		'args'     => array( 'class' => 'bb-onesignal-web-push-skip-active-members bp-hide' ),
	);

	$fields['bb-onesignal-request-permission'] = array(
		'title'    => esc_html__( 'Automatically Request Permission', 'buddyboss-pro' ),
		'callback' => 'bb_onesignal_admin_setting_callback_request_permission_fields',
		'args'     => array( 'class' => 'bb-onesignal-request-permission bp-hide' ),
	);

	$fields['bb-onesignal-enable-soft-prompt'] = array(
		'title'    => esc_html__( 'Enable Soft Prompt', 'buddyboss-pro' ),
		'callback' => 'bb_onesignal_admin_setting_callback_enable_soft_prompt_fields',
		'args'     => array( 'class' => 'bb-onesignal-enable-soft-prompt bp-hide' ),
	);

	$fields['bb-onesignal-enable-soft-prompt-message'] = array(
		'title'    => '',
		'callback' => 'bb_onesignal_admin_setting_callback_enable_soft_prompt_fields_message',
		'args'     => array( 'class' => 'bb-onesignal-enable-soft-prompt-extra-fields bp-hide' ),
	);

	$fields['bb-onesignal-enable-soft-prompt-image'] = array(
		'title'    => '',
		'callback' => 'bb_onesignal_admin_setting_callback_enable_soft_prompt_fields_image',
		'args'     => array( 'class' => 'bb-onesignal-enable-soft-prompt-extra-fields bp-hide' ),
	);

	$fields['bb-onesignal-enable-soft-prompt-buttons'] = array(
		'title'    => '',
		'callback' => 'bb_onesignal_admin_setting_callback_enable_soft_prompt_fields_buttons',
		'args'     => array( 'class' => 'bb-onesignal-enable-soft-prompt-extra-fields group-field bp-hide' ),
	);

	$fields['bb-onesignal-enable-soft-prompt-preview-box'] = array(
		'title'    => '',
		'callback' => 'bb_onesignal_admin_setting_callback_enable_soft_prompt_fields_preview_box',
		'args'     => array( 'class' => 'bb-onesignal-enable-soft-prompt-extra-fields bp-hide' ),
	);

	return $fields;
}

/**
 * The default notification attachment script data.
 *
 * @since 2.0.3
 *
 * @param array  $script_data The avatar script data.
 * @param string $object whole object.
 *
 * @return mixed
 */
function bb_onesignal_attachment_notification_script_data( $script_data, $object = '' ) {

	if ( function_exists( 'bp_core_get_admin_active_tab' ) && 'bp-notifications' === bp_core_get_admin_active_tab() ) {
		$script_data['bp_params'] = array(
			'object'     => 'notification',
			'item_id'    => 0,
			'item_type'  => 'default',
			'has_avatar' => bb_has_default_custom_upload_profile_avatar(),
			'nonces'     => array(
				'set'    => wp_create_nonce( 'bp_avatar_cropstore' ),
				'remove' => wp_create_nonce( 'bp_delete_avatar_link' ),
			),
		);

		// Set feedback messages.
		$script_data['feedback_messages'] = array(
			1 => esc_html__( 'There was a problem cropping custom profile avatar.', 'buddyboss-pro' ),
			2 => esc_html__( 'The custom profile avatar was uploaded successfully.', 'buddyboss-pro' ),
			3 => esc_html__( 'There was a problem deleting custom profile avatar. Please try again.', 'buddyboss-pro' ),
			4 => esc_html__( 'The custom profile avatar was deleted successfully!', 'buddyboss-pro' ),
		);
	}

	return $script_data;
}

/**
 * Save registered settings to DB.
 *
 * @since 2.0.3
 *
 * @param string $current_tab Current setting tab.
 */
function bb_onesignal_web_push_setting_fields_save( $current_tab ) {
	if ( 'bp-notifications' !== $current_tab ) {
		return;
	}

	$bb_onesignal_permission_validate = bb_pro_filter_input_string( INPUT_POST, 'bb-onesignal-permission-validate' );
	$bb_onesignal_request_permission  = filter_input( INPUT_POST, 'bb-onesignal-request-permission', FILTER_VALIDATE_BOOLEAN );
	if ( $bb_onesignal_request_permission ) {
		bp_update_option( 'bb-onesignal-permission-validate', $bb_onesignal_permission_validate );
	}

	$bb_onesignal_allow_button  = bb_pro_filter_input_string( INPUT_POST, 'bb-onesignal-enable-soft-prompt-allow-button' );
	$bb_onesignal_cancel_button = bb_pro_filter_input_string( INPUT_POST, 'bb-onesignal-enable-soft-prompt-cancel-button' );

	bp_update_option( 'bb-onesignal-enable-soft-prompt-allow-button', $bb_onesignal_allow_button );
	bp_update_option( 'bb-onesignal-enable-soft-prompt-cancel-button', $bb_onesignal_cancel_button );
}

/**
 * Ajax notification attachment upload dir.
 *
 * @since 2.0.3
 *
 * @param array $bp_params Array of upload params.
 *
 * @return array
 */
function bb_onesignal_avatar_ajax_upload_params( $bp_params ) {
	if ( empty( $bp_params['object'] ) || ( 'notification' !== $bp_params['object'] && 'prompt' !== $bp_params['object'] ) ) {
		return $bp_params;
	}

	if ( 'notification' === $bp_params['object'] ) {
		$bp_params['upload_dir_filter'] = 'bb_onesignal_notification_attachment_upload_dir';
	} elseif ( 'prompt' === $bp_params['object'] ) {
		$bp_params['upload_dir_filter'] = 'bb_onesignal_prompt_attachment_upload_dir';
	}

	return $bp_params;
}

/**
 * Setup the notification upload directory for a user.
 *
 * @since 2.0.3
 *
 * @param string $directory The root directory name. Optional.
 * @param int    $user_id   The user ID. Optional.
 *
 * @return array Array containing the path, URL, and other helpful settings.
 */
function bb_onesignal_notification_attachment_upload_dir( $directory = 'notification/icon', $user_id = 0 ) {

	// Use displayed user if no user ID was passed.
	if ( empty( $user_id ) ) {
		$user_id = bp_displayed_user_id();
	}

	// Failsafe against accidentally nooped $directory parameter.
	if ( empty( $directory ) ) {
		$directory = 'notification/icon';
	}

	$path      = bp_core_avatar_upload_path() . '/' . $directory . '/' . $user_id;
	$newbdir   = $path;
	$newurl    = bp_core_avatar_url() . '/' . $directory . '/' . $user_id;
	$newburl   = $newurl;
	$newsubdir = '/' . $directory . '/' . $user_id;

	/**
	 * Filters the avatar upload directory for a user.
	 *
	 * @since 2.0.3
	 *
	 * @param array $value Array containing the path, URL, and other helpful settings.
	 */
	return apply_filters(
		'bb_onesignal_notification_attachment_upload_dir',
		array(
			'path'    => $path,
			'url'     => $newurl,
			'subdir'  => $newsubdir,
			'basedir' => $newbdir,
			'baseurl' => $newburl,
			'error'   => false,
		)
	);
}

/**
 * Setup the soft prompt image upload directory for a user.
 *
 * @since 2.0.3
 *
 * @param string $directory The root directory name. Optional.
 * @param int    $user_id   The user ID. Optional.
 *
 * @return array Array containing the path, URL, and other helpful settings.
 */
function bb_onesignal_prompt_attachment_upload_dir( $directory = 'notification/prompt', $user_id = 0 ) {

	// Use displayed user if no user ID was passed.
	if ( empty( $user_id ) ) {
		$user_id = bp_displayed_user_id();
	}

	// Failsafe against accidentally nooped $directory parameter.
	if ( empty( $directory ) ) {
		$directory = 'notification/prompt';
	}

	$path      = bp_core_avatar_upload_path() . '/' . $directory . '/' . $user_id;
	$newbdir   = $path;
	$newurl    = bp_core_avatar_url() . '/' . $directory . '/' . $user_id;
	$newburl   = $newurl;
	$newsubdir = '/' . $directory . '/' . $user_id;

	/**
	 * Filters the avatar upload directory for a user.
	 *
	 * @since 2.0.3
	 *
	 * @param array $value Array containing the path, URL, and other helpful settings.
	 */
	return apply_filters(
		'bb_onesignal_prompt_attachment_upload_dir',
		array(
			'path'    => $path,
			'url'     => $newurl,
			'subdir'  => $newsubdir,
			'basedir' => $newbdir,
			'baseurl' => $newburl,
			'error'   => false,
		)
	);
}

/**
 * Check the current user's capability to edit an avatar for a given object.
 *
 * @since 2.0.3
 *
 * @param bool   $can        Whether to check permission granted or not.
 * @param string $capability The capability to check.
 * @param array  $args       An array containing the item_id and the object to check.
 *
 * @return bool
 */
function bb_onesignal_attachments_current_user_can( $can, $capability, $args ) {
	if ( empty( $args['object'] ) || ( 'notification' !== $args['object'] && 'prompt' !== $args['object'] ) ) {
		return $can;
	}

	return bp_core_can_edit_settings();
}

/**
 * Notification icon/soft prompt image upload directory.
 *
 * @since 2.0.3
 *
 * @param string $avatar_dir Avatar directory.
 * @param array  $object     The object to check.
 *
 * @return mixed|string
 */
function bb_onesignal_bp_core_avatar_dir( $avatar_dir, $object ) {
	if ( empty( $object ) || ( 'notification' !== $object && 'prompt' !== $object ) ) {
		return $avatar_dir;
	}

	if ( 'notification' === $object ) {
		return 'notification/icon';
	} elseif ( 'prompt' === $object ) {
		return 'notification/prompt';
	}

	return $avatar_dir;
}

/**
 * Updated attachment arguments.
 *
 * @since 2.0.3
 *
 * @param array $strings Attachment data.
 *
 * @return mixed
 */
function bb_onesignal_attachments_get_plupload_l10n( $strings ) {
	if ( function_exists( 'bp_core_get_admin_active_tab' ) && 'bp-notifications' !== bp_core_get_admin_active_tab() ) {
		return $strings;
	}

	$strings['has_avatar_warning'] = '';

	return $strings;
}

/**
 * Notification icon/soft prompt image upload directory.
 *
 * @since 2.0.3
 *
 * @param string $avatar_dir  Avatar directory name.
 * @param array  $avatar_data Avatar data.
 *
 * @return mixed|string
 */
function bb_onesignal_notification_set_avatar_dir( $avatar_dir, $avatar_data ) {
	if ( empty( $avatar_data['object'] ) || ( 'notification' !== $avatar_data['object'] && 'prompt' !== $avatar_data['object'] ) ) {
		return $avatar_dir;
	}

	if ( 'notification' === $avatar_data['object'] ) {
		return 'notification/icon';
	} elseif ( 'prompt' === $avatar_data['object'] ) {
		return 'notification/prompt';
	}

	return $avatar_dir;
}

/**
 * Check push notification is enabled or not.
 *
 * @since 2.0.3
 *
 * @param bool $is_enabled Whether the push notification is enabled or not.
 *
 * @return bool|mixed
 */
function bb_onesignal_web_push_enabled( $is_enabled ) {

	if ( ! $is_enabled && bp_is_active( 'notifications' ) && ! bb_pro_should_lock_features() && bb_onesignal_enabled_web_push() && (int) bb_onesignal_request_permission() ) {
		$is_enabled = true;
	}

	return $is_enabled;
}

/**
 * Prevent to crop the avatar image.
 *
 * @since 2.0.3
 *
 * @param bool   $value Whether to crop the avatar image or not.
 * @param object $r     The avatar image object.
 *
 * @return false|mixed
 */
function bb_onesignal_custom_image_handle_crop_remove( $value, $r ) {
	if ( ! empty( $r['object'] ) && ( 'notification' === $r['object'] || 'prompt' === $r['object'] ) && ! empty( $r['item_type'] ) && 'default' === $r['item_type'] ) {
		return false;
	}
	return $value;
}

/**
 * Needs to check the web push needs to fired or not.
 *
 * @since 2.2.3
 *
 * @param bool                          $retval       Return value.
 * @param BP_Notifications_Notification $notification Notification object.
 *
 * @return mixed
 */
function bb_onesignal_manage_web_push_notification( $retval, $notification ) {
	if (
		! empty( $notification->id ) &&
		! empty( $notification->component_action ) &&
		in_array(
			$notification->component_action,
			array(
				'bb_activity_following_post',
				'bb_groups_subscribed_activity',
				'bb_groups_subscribed_discussion',
				'bb_forums_subscribed_reply',
				'bb_forums_subscribed_discussion',
				'bbp_new_reply',
			),
			true
		) &&
		true === (bool) bp_notifications_get_meta( $notification->id, 'not_send_web', true )
	) {
		return false;
	}

	return $retval;
}

/**
 * Register OneSignal fields for Settings 2.0 Web Push Notifications panel.
 *
 * Hooked to `bb_notifications_web_push_after_settings_fields`.
 *
 * @since 3.0.0
 */
function bb_onesignal_register_settings_2_fields() {

	// Settings 2.0 panels are admin-only — skip registration on frontend page
	// loads to avoid the per-request DB reads from helpers like
	// bb_onesignal_app_is_connected() / bb_onesignal_get_settings().
	if ( ! is_admin() && ! wp_doing_ajax() ) {
		return;
	}

	if (
		function_exists( 'bb_enabled_legacy_email_preference' ) &&
		bb_enabled_legacy_email_preference()
	) {
		return;
	}

	$feature_id = 'notifications';
	$panel_id   = 'onesignal';

	// -------------------------------------------------------------------------
	// SECTION: OneSignal Connection.
	// -------------------------------------------------------------------------
	bb_register_feature_section(
		$feature_id,
		$panel_id,
		'onesignal_connection',
		array(
			'title'       => __( 'OneSignal', 'buddyboss-pro' ),
			'order'       => 20,
			'status'      => array(
				'type' => bb_onesignal_app_is_connected() ? 'success' : 'warning',
				'text' => bb_onesignal_app_is_connected()
					? __( 'Connected', 'buddyboss-pro' )
					: __( 'Not Connected', 'buddyboss-pro' ),
			),
			'description' => sprintf(
				/* translators: %s: OneSignal URL */
				__( 'To use <a href="%s" target="_blank" rel="noopener noreferrer">OneSignal</a> for web push notifications, create an app in your account and enter the API credentials from the settings below.', 'buddyboss-pro' ),
				'https://onesignal.com/'
			),
			'help_url'    => bp_get_admin_url(
				add_query_arg(
					array(
						'page'    => 'bp-help',
						'article' => '638212',
					),
					'admin.php'
				)
			),
		)
	);

	// Connection warning/error notice (only shown when there are issues).
	$onesignal_settings = bb_onesignal_get_settings();
	$connection_notice  = '';

	if ( ! empty( $onesignal_settings['errors'] ) ) {
		$connection_notice = wp_kses_post( reset( $onesignal_settings['errors'] ) );
	} elseif ( ! empty( $onesignal_settings['is_connected'] ) && ! empty( $onesignal_settings['warnings'] ) ) {
		$connection_notice = wp_kses_post( reset( $onesignal_settings['warnings'] ) );
	}

	if ( ! empty( $connection_notice ) ) {
		bb_register_feature_field(
			$feature_id,
			$panel_id,
			'onesignal_connection',
			array(
				'name'              => '_bb_onesignal_connection_notice',
				'label'             => '',
				'type'              => 'notice',
				'notice_type'       => ! empty( $onesignal_settings['errors'] ) ? 'error' : 'warning',
				'description'       => $connection_notice,
				'sanitize_callback' => '__return_empty_string',
				'order'             => 8,
			)
		);
	}

	// App ID (password field).
	bb_register_feature_field(
		$feature_id,
		$panel_id,
		'onesignal_connection',
		array(
			'name'              => 'bb-onesignal-app-id',
			'label'             => __( 'API Credentials', 'buddyboss-pro' ),
			'type'              => 'password',
			'default'           => bb_onesignal_app_id(),
			'sanitize_callback' => 'sanitize_text_field',
			'placeholder'       => __( 'Enter OneSignal APP ID', 'buddyboss-pro' ),
			'group'             => array(
				'key'   => 'onesignal_credentials',
				'label' => __( 'App ID', 'buddyboss-pro' ),
			),
			'order'             => 10,
		)
	);

	// REST API Key (password field).
	bb_register_feature_field(
		$feature_id,
		$panel_id,
		'onesignal_connection',
		array(
			'name'              => 'bb-onesignal-rest-api',
			'label'             => '',
			'type'              => 'password',
			'default'           => bb_onesignal_rest_api_key(),
			'sanitize_callback' => 'sanitize_text_field',
			'placeholder'       => __( 'Enter Rest API key', 'buddyboss-pro' ),
			'group'             => array(
				'key'   => 'onesignal_credentials',
				'label' => __( 'Rest API Key', 'buddyboss-pro' ),
			),
			'order'             => 20,
		)
	);

	// Verify button — hidden when connected, appears when credentials change.
	bb_register_feature_field(
		$feature_id,
		$panel_id,
		'onesignal_connection',
		array(
			'name'              => '_bb_onesignal_save_credentials',
			'label'             => '',
			'type'              => 'bb_verify_popup',
			'button_label'      => __( 'Update', 'buddyboss-pro' ),
			'ajax_action'       => 'bb_onesignal_save_credentials',
			'related_fields'    => array( 'bb-onesignal-app-id', 'bb-onesignal-rest-api' ),
			'is_connected'      => bb_onesignal_app_is_connected(),
			'verify_config'     => array(
				'modal_title'     => __( 'Verify OneSignal', 'buddyboss-pro' ),
				'loading_message' => __( 'Verifying OneSignal credentials', 'buddyboss-pro' ),
				'loading_icon'    => 'bb-icons-rl bb-icons-rl-broadcast',
			),
			'sanitize_callback' => '__return_empty_string',
			'group'             => 'onesignal_credentials',
			'order'             => 30,
		)
	);

	// -------------------------------------------------------------------------
	// SECTION: Notification Settings.
	// -------------------------------------------------------------------------
	bb_register_feature_section(
		$feature_id,
		$panel_id,
		'onesignal_settings',
		array(
			'title'       => __( 'Notification Settings', 'buddyboss-pro' ),
			'order'       => 30,
			'conditional' => array(
				'field'  => '_bb_onesignal_is_connected',
				'value'  => true,
				'action' => 'disable',
			),
			'help_url'    => bp_get_admin_url(
				add_query_arg(
					array(
						'page'    => 'bp-help',
						'article' => '636150',
					),
					'admin.php'
				)
			),
		)
	);

	// Virtual field to track connection state for section conditional.
	// Cannot use bb-onesignal-app-id because typing in the input updates
	// the settings value before the actual connection is established.
	bb_register_feature_field(
		$feature_id,
		$panel_id,
		'onesignal_settings',
		array(
			'name'              => '_bb_onesignal_is_connected',
			'label'             => '',
			'type'              => 'hidden',
			'default'           => bb_onesignal_app_is_connected() ? 1 : 0,
			'sanitize_callback' => '__return_empty_string',
			'order'             => 1,
		)
	);

	// Web Push Notifications toggle.
	bb_register_feature_field(
		$feature_id,
		$panel_id,
		'onesignal_settings',
		array(
			'name'              => 'bb-onesignal-enabled-web-push',
			'label'             => __( 'Web Push Notifications', 'buddyboss-pro' ),
			'type'              => 'toggle',
			'default'           => (int) bp_get_option( 'bb-onesignal-enabled-web-push', 0 ),
			'sanitize_callback' => 'absint',
			'description'       => __( 'Allow members to subscribe to notifications through their browser', 'buddyboss-pro' ),
			'help_text'         => __( 'Once enabled, members will be able to opt-in to receive all BuddyBoss Notifications as push notifications through their web browser.', 'buddyboss-pro' ),
			'order'             => 10,
		)
	);

	// Default Notification Icon.
	bb_register_feature_field(
		$feature_id,
		$panel_id,
		'onesignal_settings',
		array(
			'name'              => 'bb-onesignal-default-notification-icon',
			'label'             => __( 'Default Notification Icon', 'buddyboss-pro' ),
			'type'              => 'image_upload',
			'default'           => bb_onesignal_default_notification_icon(),
			'sanitize_callback' => 'esc_url_raw',
			'description'       => __( 'Upload an image to be the default icon used for web push notifications. Certain notification types may use different icons. The recommended size is 384px by 384px.', 'buddyboss-pro' ),
			'upload_config'     => array(
				'type'      => 'avatar',
				'object'    => 'notification',
				'item_id'   => 0,
				'item_type' => 'default',
				'label'     => '',
				'help_text' => '',
			),
			'conditional'       => array(
				'field'  => 'bb-onesignal-enabled-web-push',
				'value'  => true,
				'action' => 'disable',
			),
			'order'             => 20,
		)
	);

	// Skip Active Members.
	bb_register_feature_field(
		$feature_id,
		$panel_id,
		'onesignal_settings',
		array(
			'name'              => 'bb_web_push_skip_active_members',
			'label'             => __( 'Skip Active Members', 'buddyboss-pro' ),
			'type'              => 'toggle',
			'default'           => (int) bp_get_option( 'bb_web_push_skip_active_members', 0 ),
			'sanitize_callback' => 'absint',
			'description'       => __( "Don't send push notifications when members are active on a device.", 'buddyboss-pro' ),
			'help_text'         => __( "When a member is actively using your site on any device, they won't receive web push notifications. Once a member is inactive, they will begin receiving push notifications after a short delay.", 'buddyboss-pro' ),
			'conditional'       => array(
				'field'  => 'bb-onesignal-enabled-web-push',
				'value'  => true,
				'action' => 'disable',
			),
			'order'             => 30,
		)
	);

	// Auto Request Permission.
	bb_register_feature_field(
		$feature_id,
		$panel_id,
		'onesignal_settings',
		array(
			'name'                 => 'bb-onesignal-request-permission',
			'label'                => __( 'Auto Request', 'buddyboss-pro' ),
			'type'                 => 'toggle',
			'default'              => (int) bp_get_option( 'bb-onesignal-request-permission', 0 ),
			'sanitize_callback'    => 'absint',
			/* translators: %s: Login/Visit select dropdown. */
			'description'          => __( 'Request notification permission on first %s through a new browser', 'buddyboss-pro' ),
			'help_text'            => __( 'When enabled, the browser will prompt members to allow web push notifications. Members must click \'Allow\' to subscribe. If disabled, permission can only be granted through the Notification Preferences tab in Account Settings.', 'buddyboss-pro' ),
			'description_controls' => array(
				array(
					'type'              => 'select',
					'name'              => 'bb-onesignal-permission-validate',
					'default'           => bb_onesignal_permission_validate(),
					'options'           => array(
						array(
							'label' => __( 'Login', 'buddyboss-pro' ),
							'value' => 'login',
						),
						array(
							'label' => __( 'Visit', 'buddyboss-pro' ),
							'value' => 'visit',
						),
					),
					'sanitize_callback' => 'sanitize_text_field',
				),
			),
			'conditional'          => array(
				'field'  => 'bb-onesignal-enabled-web-push',
				'value'  => true,
				'action' => 'disable',
			),
			'order'                => 40,
		)
	);

	// Enable Soft Prompt.
	bb_register_feature_field(
		$feature_id,
		$panel_id,
		'onesignal_settings',
		array(
			'name'              => 'bb-onesignal-enable-soft-prompt',
			'label'             => __( 'Enable Soft Prompt', 'buddyboss-pro' ),
			'type'              => 'toggle',
			'default'           => (int) bp_get_option( 'bb-onesignal-enable-soft-prompt', 0 ),
			'sanitize_callback' => 'absint',
			'description'       => __( 'Show a "soft prompt" before triggering the browser\'s native permission prompt.', 'buddyboss-pro' ),
			'help_text'         => sprintf(
				/* translators: %s: Learn More link */
				__( 'A "soft prompt" is a customizable prompt that is shown to the member before the "hard prompt" or the native permission prompt is triggered by the browser. %s', 'buddyboss-pro' ),
				'<a href="https://documentation.onesignal.com/docs/permission-requests#soft-prompts" target="_blank" rel="noopener noreferrer">' . __( 'Learn More', 'buddyboss-pro' ) . '</a>'
			),
			'conditional'       => array(
				'field'  => 'bb-onesignal-enabled-web-push',
				'value'  => true,
				'action' => 'disable',
			),
			'group'             => 'soft_prompt_fields',
			'order'             => 50,
		)
	);

	// Prompt Message.
	bb_register_feature_field(
		$feature_id,
		$panel_id,
		'onesignal_settings',
		array(
			'name'              => 'bb-onesignal-enable-soft-prompt-message',
			'label'             => '',
			'type'              => 'textarea',
			'default'           => bb_onesignal_soft_prompt_message_text(),
			'sanitize_callback' => 'bb_onesignal_sanitize_prompt_message',
			'placeholder'       => __( 'Subscribe to push notifications to keep up to date.', 'buddyboss-pro' ),
			'maxlength'         => 90,
			'conditional'       => array(
				'field' => 'bb-onesignal-enable-soft-prompt',
				'value' => true,
			),
			'group'             => array(
				'key'   => 'soft_prompt_fields',
				'label' => __( 'Prompt Message', 'buddyboss-pro' ),
			),
			'order'             => 60,
		)
	);

	// Soft Prompt Image (read-only — image comes from OneSignal app config).
	bb_register_feature_field(
		$feature_id,
		$panel_id,
		'onesignal_settings',
		array(
			'name'              => '_bb_onesignal_soft_prompt_image',
			'label'             => '',
			'type'              => 'static_text',
			'help_text'         => sprintf(
			/* translators: 1. Field name, 2. Learn more link. */
				__( 'To change the image used in your prompt, enter the URL of the image into the %1$s field in your OneSignal app\'s settings. %2$s', 'buddyboss-pro' ),
				'<strong>' . esc_html__( 'Default Icon Url', 'buddyboss-pro' ) . '</strong>',
				'<a href="https://documentation.onesignal.com/docs/permission-requests#soft-prompts" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Learn More', 'buddyboss-pro' ) . '</a>'
			),
			'sanitize_callback' => '__return_empty_string',
			'conditional'       => array(
				'field' => 'bb-onesignal-enable-soft-prompt',
				'value' => true,
			),
			'group'             => array(
				'key'   => 'soft_prompt_fields',
				'label' => __( 'Image', 'buddyboss-pro' ),
			),
			'order'             => 65,
		)
	);

	// Allow Button text.
	bb_register_feature_field(
		$feature_id,
		$panel_id,
		'onesignal_settings',
		array(
			'name'              => 'bb-onesignal-enable-soft-prompt-allow-button',
			'label'             => '',
			'type'              => 'text',
			'default'           => bb_onesignal_soft_prompt_allow_btn_text(),
			'sanitize_callback' => 'bb_onesignal_sanitize_button_text',
			'placeholder'       => bb_onesignal_soft_prompt_allow_btn_placeholder_text(),
			'conditional'       => array(
				'field' => 'bb-onesignal-enable-soft-prompt',
				'value' => true,
			),
			'group'             => array(
				'key'    => 'soft_prompt_fields',
				'label'  => __( 'Allow Button', 'buddyboss-pro' ),
				'inline' => true,
			),
			'order'             => 70,
		)
	);

	// Cancel Button text.
	bb_register_feature_field(
		$feature_id,
		$panel_id,
		'onesignal_settings',
		array(
			'name'              => 'bb-onesignal-enable-soft-prompt-cancel-button',
			'label'             => '',
			'type'              => 'text',
			'default'           => bb_onesignal_soft_prompt_cancel_btn_text(),
			'sanitize_callback' => 'bb_onesignal_sanitize_button_text',
			'placeholder'       => bb_onesignal_soft_prompt_cancel_btn_placeholder_text(),
			'conditional'       => array(
				'field' => 'bb-onesignal-enable-soft-prompt',
				'value' => true,
			),
			'group'             => array(
				'key'    => 'soft_prompt_fields',
				'label'  => __( 'Cancel Button', 'buddyboss-pro' ),
				'inline' => true,
			),
			'order'             => 80,
		)
	);
}

// bb_onesignal_extend_field_data removed — button_only and related_fields
// are natively handled by the core AJAX handler (class-bb-admin-settings-ajax.php).

/**
 * Handle OneSignal credential save via separate AJAX endpoint.
 *
 * Credentials are NOT auto-saved; they are saved only when the user
 * clicks "Save Credentials". This handler validates via the OneSignal API
 * and returns updated connection status for the React UI.
 *
 * @since 3.0.0
 */
function bb_onesignal_save_credentials_ajax() {

	if ( ! bp_current_user_can( 'bp_moderate' ) ) {
		wp_send_json_error( array( 'message' => esc_html__( 'You do not have permission to perform this action.', 'buddyboss-pro' ) ) );
	}

	check_ajax_referer( 'bb_admin_settings', 'nonce' );

	$app_id  = isset( $_POST['bb-onesignal-app-id'] ) ? sanitize_text_field( wp_unslash( $_POST['bb-onesignal-app-id'] ) ) : '';
	$api_key = isset( $_POST['bb-onesignal-rest-api'] ) ? sanitize_text_field( wp_unslash( $_POST['bb-onesignal-rest-api'] ) ) : '';

	// Disconnect: both creds empty means the user clicked Disconnect. Clear
	// stored credentials + connection status, skip the OneSignal API round-trip,
	// and return success so the React verify popup shows a green "Disconnected"
	// message instead of "Verification failed".
	if ( empty( $app_id ) && empty( $api_key ) ) {
		bb_onesignal_update_settings(
			array(
				'app_id'          => '',
				'rest_api_key'    => '',
				'is_connected'    => false,
				'app_name'        => '',
				'app_details'     => array(),
				'warnings'        => array(),
				'errors'          => array(),
				'sidewide_errors' => array(),
			)
		);

		wp_send_json_success(
			array(
				'is_connected'   => false,
				'message'        => __( 'OneSignal disconnected.', 'buddyboss-pro' ),
				'status'         => array(
					'type' => 'warning',
					'text' => __( 'Not Connected', 'buddyboss-pro' ),
				),
				'updated_fields' => array(
					'bb-onesignal-app-id'        => '',
					'bb-onesignal-rest-api'      => '',
					'_bb_onesignal_is_connected' => 0,
				),
			)
		);
	}

	// Reset and save credentials.
	bb_onesignal_update_settings(
		array(
			'app_id'          => $app_id,
			'rest_api_key'    => $api_key,
			'is_connected'    => false,
			'app_name'        => '',
			'app_details'     => array(),
			'warnings'        => array(),
			'errors'          => array(),
			'sidewide_errors' => array(),
		)
	);

	// Validate via OneSignal API if both present.
	if ( ! empty( $app_id ) && ! empty( $api_key ) ) {
		bb_onesignal_update_app_details();
	}

	// Determine connection state for response.
	$is_connected = bb_onesignal_app_is_connected();
	$settings     = bb_onesignal_get_settings();
	$has_errors   = ! empty( $settings['errors'] );
	$has_warnings = ! empty( $settings['warnings'] );

	// Determine section status badge.
	$status_type = $is_connected ? 'success' : 'warning';
	if ( $has_errors ) {
		$status_type = 'error';
	}

	// Build response message for the verify popup modal.
	$message = '';
	if ( $is_connected ) {
		$message = __( 'OneSignal connected successfully.', 'buddyboss-pro' );
	} elseif ( $has_errors ) {
		$message = wp_kses_post( reset( $settings['errors'] ) );
	} elseif ( $has_warnings ) {
		$message = wp_kses_post( reset( $settings['warnings'] ) );
	}

	// Build response data.
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
			'_bb_onesignal_is_connected' => $is_connected ? 1 : 0,
		),
	);

	// Include debug details when WP_DEBUG is enabled.
	if ( defined( 'WP_DEBUG' ) && WP_DEBUG && ! $is_connected ) {
		$response_data['debug'] = array(
			'errors'   => ! empty( $settings['errors'] ) ? $settings['errors'] : array(),
			'warnings' => ! empty( $settings['warnings'] ) ? $settings['warnings'] : array(),
		);
	}

	// bb_verify_popup: success = green checkmark, error = red X.
	if ( $is_connected ) {
		wp_send_json_success( $response_data );
	} else {
		wp_send_json_error( $response_data );
	}
}

/**
 * Handle custom save logic for OneSignal notification settings in Settings 2.0.
 *
 * Credential fields are handled by the Save Credentials button AJAX handler,
 * so we intercept them here to prevent the standard save pipeline from
 * persisting them as standalone options.
 *
 * @since 3.0.0
 *
 * @param string $feature_id Feature ID.
 * @param array  $settings   Full submitted settings.
 * @param array  $saved      Keys and values already saved by core pipeline.
 */
function bb_onesignal_settings_2_save( $feature_id, $settings, $saved ) {

	if ( 'notifications' !== $feature_id ) {
		return;
	}

	// Credential fields are saved via the Save Credentials button AJAX.
	// If they were submitted through auto-save, update the serialized option.
	$credential_keys = array( 'bb-onesignal-app-id', 'bb-onesignal-rest-api' );
	$has_credential  = false;

	foreach ( $credential_keys as $key ) {
		if ( array_key_exists( $key, $saved ) ) {
			$has_credential = true;
			// Remove standalone option that the core pipeline saved.
			bp_delete_option( $key );
		}
	}

	// If credentials were submitted via auto-save, update the serialized setting.
	if ( $has_credential ) {
		$update = array();
		if ( isset( $saved['bb-onesignal-app-id'] ) ) {
			$update['app_id'] = $saved['bb-onesignal-app-id'];
		}
		if ( isset( $saved['bb-onesignal-rest-api'] ) ) {
			$update['rest_api_key'] = $saved['bb-onesignal-rest-api'];
		}
		if ( ! empty( $update ) ) {
			bb_onesignal_update_settings( $update );
		}
	}

	// Note: bb-onesignal-permission-validate is now saved automatically by the core
	// description_controls pipeline in BB_Admin_Settings_Ajax::bb_admin_save_feature_settings().
}

/**
 * Sanitize soft a prompt message text (max 90 characters).
 *
 * @since 3.0.0
 *
 * @param string $value Input value.
 *
 * @return string Sanitized value.
 */
function bb_onesignal_sanitize_prompt_message( $value ) {
	return mb_substr( sanitize_text_field( $value ), 0, 90 );
}

/**
 * Sanitize button text (max 15 characters).
 *
 * @since 3.0.0
 *
 * @param string $value Input value.
 *
 * @return string Sanitized value.
 */
function bb_onesignal_sanitize_button_text( $value ) {
	return mb_substr( sanitize_text_field( $value ), 0, 15 );
}
