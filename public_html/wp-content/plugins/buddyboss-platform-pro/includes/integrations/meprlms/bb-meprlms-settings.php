<?php
/**
 * BuddyBoss MeprLMS Integration — Settings 2.0 Registration.
 *
 * Registers side panel, sections, and fields for the MeprLMS integration
 * in the Settings 2.0 React admin UI.
 *
 * @package BuddyBossPro/Integration/MemberpressLMS
 * @since 3.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Register MeprLMS Settings 2.0 panels, sections, and fields.
 *
 * Called from bb-meprlms-loader.php at bb_after_register_features priority 15.
 *
 * @since 3.0.0
 *
 * @return void
 */
function bb_meprlms_register_settings_2_panels() {

	$feature_id = 'meprlms';

	// =========================================================================
	// SIDE PANEL: MemberPress Settings (default — single panel).
	// =========================================================================

	bb_register_side_panel(
		$feature_id,
		'meprlms_settings',
		array(
			'title'      => __( 'MemberPress Settings', 'buddyboss-pro' ),
			'icon'       => array(
				'type'  => 'font',
				'class' => 'bb-icons-rl bb-icons-rl-gear-six',
			),
			'order'      => 10,
			'is_default' => true,
		)
	);

	// If MemberPress Courses plugin is not active, show "Requires Plugin to Activate" notice only.
	if ( ! class_exists( 'memberpress\courses\helpers\Courses' ) ) {
		bb_meprlms_register_plugin_required_notice( $feature_id );
		return;
	} elseif ( ! bp_is_active( 'groups' ) ) {
		bb_meprlms_register_require_component_notice( $feature_id );
		return;
	}

	// =========================================================================
	// SECTION 1: MemberPress Group Sync — with section-level toggle.
	// =========================================================================

	bb_register_feature_section(
		$feature_id,
		'meprlms_settings',
		'meprlms_group_sync',
		array(
			'title'          => __( 'MemberPress Group Sync', 'buddyboss-pro' ),
			'order'          => 10,
			'section_toggle' => 'bb-meprlms-enable',
			'help_url'       => bp_get_admin_url(
				add_query_arg(
					array(
						'page'    => 'bp-help',
						'article' => '638229',
					),
					'admin.php'
				)
			),
		)
	);

	// Description as notice field (matches Figma: visible text, no border).
	bb_register_feature_field(
		$feature_id,
		'meprlms_settings',
		'meprlms_group_sync',
		array(
			'name'              => '_bb_meprlms_group_sync_notice',
			'label'             => '',
			'type'              => 'notice',
			'notice_type'       => 'plain',
			'description'       => __( 'When enabled, the group sync functionality for MemberPress courses will be available.', 'buddyboss-pro' ),
			'sanitize_callback' => '__return_empty_string',
			'order'             => 5,
		)
	);

	// Course Visibility toggle.
	// Read default directly from serialized option because bb_get_meprlms_settings()
	// is not available yet — it's defined in bb-meprlms-functions.php which is loaded
	// by BB_MeprLMS_Integration::includes() at bp_setup_integrations (after this file runs).
	$bb_meprlms_opts        = bp_get_option( 'bb-meprlms', array() );
	$bb_meprlms_opts        = is_array( $bb_meprlms_opts ) ? $bb_meprlms_opts : array();
	$course_visibility_dflt = isset( $bb_meprlms_opts['bb-meprlms-course-visibility'] ) ? $bb_meprlms_opts['bb-meprlms-course-visibility'] : 1;

	bb_register_feature_field(
		$feature_id,
		'meprlms_settings',
		'meprlms_group_sync',
		array(
			'name'              => 'bb-meprlms-course-visibility',
			'label'             => __( 'Course Visibility', 'buddyboss-pro' ),
			'description'       => __( 'Allow admins to link courses to groups during group creation and management.', 'buddyboss-pro' ),
			'type'              => 'toggle',
			'default'           => absint( $course_visibility_dflt ),
			'sanitize_callback' => 'absint',
			'conditional'       => array(
				'field'  => 'bb-meprlms-enable',
				'value'  => true,
				'action' => 'show',
			),
			'order'             => 20,
		)
	);

	// Display Course Activity and Posts in Activity Feed fields are registered
	// lazily via bb_admin_settings_before_get_feature hook because
	// bb_meprlms_course_activities() and bb_meprlms_get_post_types() depend on
	// functions loaded at bp_init, after settings registration at bp_loaded.
	// See bb_meprlms_lazy_register_activity_fields() below.

	/**
	 * Fires after MeprLMS settings panels and static fields are registered.
	 *
	 * Note: Activity and CPT feed fields are registered lazily via
	 * bb_admin_settings_before_get_feature and may not be available yet.
	 * Use bb_meprlms_after_register_lazy_fields for fields that depend
	 * on those being present.
	 *
	 * @since 3.0.0
	 */
	do_action( 'bb_meprlms_after_register_settings_fields' );
}
bb_meprlms_register_settings_2_panels();

/**
 * Register "Requires Plugin to Activate" notice for when MemberPress Courses is not installed.
 *
 * @since 3.0.0
 *
 * @param string $feature_id Feature ID.
 *
 * @return void
 */
function bb_meprlms_register_plugin_required_notice( $feature_id ) {

	bb_register_feature_section(
		$feature_id,
		'meprlms_settings',
		'meprlms_group_sync',
		array(
			'title'       => __( 'MemberPress Group Sync', 'buddyboss-pro' ),
			'description' => __( 'BuddyBoss Platform Pro has integration settings for MemberPress Courses. If using MemberPress Courses, we add the ability to add courses to groups as an instructor and utilize the BuddyBoss activity feeds for Course, Lessons & Topics. We have also taken the time to style MemberPress Courses to match our theme for styling.', 'buddyboss-pro' ),
			'order'       => 10,
		)
	);

	bb_register_feature_field(
		$feature_id,
		'meprlms_settings',
		'meprlms_group_sync',
		array(
			'name'              => '_bb-meprlms-plugin-required',
			'label'             => '',
			'type'              => 'empty_state',
			'icon'              => 'bb-icons-rl bb-icons-rl-info',
			'empty_state_title' => __( 'Requires Plugin to Activate', 'buddyboss-pro' ),
			'sanitize_callback' => '__return_empty_string',
			'full_width'        => true,
			'order'             => 20,
		)
	);
}

/**
 * Register "Requires Social Groups Component" notice when Groups is inactive.
 *
 * @since 3.0.0
 *
 * @param string $feature_id Feature ID.
 *
 * @return void
 */
function bb_meprlms_register_require_component_notice( $feature_id ) {

	bb_register_feature_section(
		$feature_id,
		'meprlms_settings',
		'meprlms_group_sync',
		array(
			'title'       => __( 'MemberPress Group Sync', 'buddyboss-pro' ),
			'description' => __( 'BuddyBoss Platform Pro has integration settings for MemberPress Courses. If using MemberPress Courses, we add the ability to add courses to groups as an instructor and utilize the BuddyBoss activity feeds for Course, Lessons & Topics. We have also taken the time to style MemberPress Courses to match our theme for styling.', 'buddyboss-pro' ),
			'order'       => 10,
		)
	);

	$groups_url = bp_get_admin_url( 'admin.php?page=bb-settings' );

	bb_register_feature_field(
		$feature_id,
		'meprlms_settings',
		'meprlms_group_sync',
		array(
			'name'              => '_bb-meprlms-require-component',
			'label'             => '',
			'type'              => 'empty_state',
			'icon'              => 'bb-icons-rl bb-icons-rl-info',
			'empty_state_title' => sprintf(
				/* translators: 1: opening anchor tag, 2: closing anchor tag */
				__( 'You need to activate the %1$sSocial Groups%2$s in order to sync MemberPress Courses with Social Groups.', 'buddyboss-pro' ),
				'<a href="' . esc_url( $groups_url ) . '">',
				'</a>'
			),
			'sanitize_callback' => '__return_empty_string',
			'full_width'        => true,
			'order'             => 20,
		)
	);
}

/**
 * Sanitize course activities toggle list.
 *
 * @since 3.0.0
 *
 * @param mixed $value The submitted value.
 *
 * @return array Sanitized course activities.
 */
function bb_meprlms_sanitize_course_activities( $value ) {
	if ( ! is_array( $value ) ) {
		return array();
	}

	$sanitized = array();
	foreach ( $value as $key => $val ) {
		$sanitized[ sanitize_key( $key ) ] = absint( $val );
	}

	return $sanitized;
}

/**
 * Custom save handler for MeprLMS settings.
 *
 * Settings 2.0 saves individual fields via update_option(), but MeprLMS
 * stores its settings in a serialized array under 'bb-meprlms'. This handler
 * reads the saved values and merges them back into the serialized option.
 *
 * @since 3.0.0
 *
 * @param string $feature_id Feature ID.
 * @param array  $settings   Full submitted settings array.
 * @param array  $saved      Keys and values saved by core handler.
 *
 * @return void
 */
function bb_meprlms_handle_settings_save( $feature_id, $settings, $saved ) {
	if ( 'meprlms' !== $feature_id ) {
		return;
	}

	$bb_meprlms = bp_get_option( 'bb-meprlms', array() );
	$bb_meprlms = is_array( $bb_meprlms ) ? $bb_meprlms : array();
	$dirty      = false;

	// Merge serialized option keys from the saved fields.
	$serialized_keys = array( 'bb-meprlms-enable', 'bb-meprlms-course-visibility', 'bb-meprlms-course-activity' );

	foreach ( $serialized_keys as $key ) {
		if ( ! isset( $saved[ $key ] ) ) {
			continue;
		}

		$bb_meprlms[ $key ] = $saved[ $key ];
		$dirty              = true;

		// Delete the individual option created by core save — it should only live in serialized array.
		delete_option( $key );
	}

	if ( $dirty ) {
		bp_update_option( 'bb-meprlms', $bb_meprlms );
	}

	// Sync blogs component state after CPT feed toggles change.
	// Uses the option-reading callback pattern (not $_POST) since Settings 2.0 saves
	// options before this hook fires. Mirrors bb_activity_sync_blogs_component_after_save().
	if ( function_exists( 'bb_sync_blogs_component_state' ) && function_exists( 'bb_post_type_feed_option_name' ) ) {
		bb_sync_blogs_component_state(
			function ( $cpt ) {
				$option_name = bb_post_type_feed_option_name( $cpt );
				return (bool) bp_get_option( $option_name, false );
			}
		);
	}

	// Disable comment option when parent CPT feed is toggled off.
	// Mirrors bb_activity_sync_blogs_component_after_save() cascade behavior.
	if ( function_exists( 'bb_meprlms_get_post_types' ) && function_exists( 'bb_post_type_feed_option_name' ) && function_exists( 'bb_post_type_feed_comment_option_name' ) ) {
		foreach ( bb_meprlms_get_post_types() as $post_type ) {
			$pt_opt  = bb_post_type_feed_option_name( $post_type );
			$ptc_opt = bb_post_type_feed_comment_option_name( $post_type );

			if ( empty( bp_get_option( $pt_opt, '' ) ) ) {
				bp_update_option( $ptc_opt, 0 );
			}
		}
	}
}
add_action( 'bb_admin_save_feature_settings_after', 'bb_meprlms_handle_settings_save', 10, 3 );

/**
 * Enrich MeprLMS field data for Settings 2.0 AJAX response.
 *
 * MeprLMS stores settings in a serialized 'bb-meprlms' option, so the
 * default bp_get_option($field_name) calls in the AJAX handler return empty.
 * This filter injects the real values from the serialized option.
 *
 * @since 3.0.0
 *
 * @param array  $field_data Formatted field data.
 * @param array  $field      Original field registration data.
 * @param string $feature_id Feature ID.
 *
 * @return array Modified field data.
 */
function bb_meprlms_enrich_field_data( $field_data, $field, $feature_id ) {
	if ( 'meprlms' !== $feature_id ) {
		return $field_data;
	}

	// Guard: helper function may not be loaded if integration is disabled via bridge.
	if ( ! function_exists( 'bb_get_meprlms_settings' ) ) {
		return $field_data;
	}

	$field_name = $field_data['name'] ?? ''; // phpcs:ignore PHPCompatibility.Operators.NewOperators.t_coalesceFound -- PHP 7.4+ required.

	// Enrich serialized option fields — read from bb-meprlms array.
	// Note: bb-meprlms-enable is handled by section_toggle + pre_option filter, not as a field.
	if ( 'bb-meprlms-course-visibility' === $field_name ) {
		$field_data['value'] = absint( bb_get_meprlms_settings( 'bb-meprlms-course-visibility', 1 ) );
		return $field_data;
	}

	// Enrich course activity toggle_list — build {key: 0|1} from serialized sub-array.
	if ( 'bb-meprlms-course-activity' === $field_name ) {
		$activities = bb_get_meprlms_settings( 'bb-meprlms-course-activity', array() );
		$all_keys   = function_exists( 'bb_meprlms_course_activities' ) ? array_keys( bb_meprlms_course_activities() ) : array();

		$toggle_values = array();
		foreach ( $all_keys as $key ) {
			$toggle_values[ $key ] = isset( $activities[ $key ] ) ? absint( $activities[ $key ] ) : 0;
		}

		$field_data['value'] = $toggle_values;
		return $field_data;
	}

	// Parent CPT toggles (bp-feed-custom-post-type-{slug}) use standard individual
	// options and are read correctly by the default bp_get_option() in the AJAX handler
	// — no enrichment needed. Only comment checkboxes need enrichment below.

	// CPT comment fields: value must respect MeprLMS global comment support.
	// Use preg_match to extract the slug — str_replace would strip '-comments' from mid-slug too.
	if ( preg_match( '/^bp-feed-custom-post-type-(.+)-comments$/', $field_name, $matches ) ) {
		$post_type = $matches[1];
		if ( ! empty( $post_type ) && function_exists( 'bb_is_post_type_feed_comment_enable' ) ) {
			$field_data['value'] = absint( bb_is_post_type_feed_comment_enable( $post_type, false ) );
		}
		return $field_data;
	}

	return $field_data;
}
add_filter( 'bb_admin_settings_format_field_data', 'bb_meprlms_enrich_field_data', 10, 3 );

/**
 * Intercept reads to bb-meprlms-enable option.
 *
 * The section toggle mechanism reads bb-meprlms-enable via bp_get_option(),
 * but the value lives in the serialized 'bb-meprlms' option. This filter
 * returns the correct value from the serialized array.
 *
 * @since 3.0.0
 *
 * @param mixed $pre_option The pre-option value. Default false.
 *
 * @return int The option value from serialized array (0 or 1).
 */
function bb_meprlms_intercept_enable_option( $pre_option ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found -- Required by pre_option filter signature.
	// Guard: helper function may not be loaded if integration is disabled via bridge.
	if ( ! function_exists( 'bb_get_meprlms_settings' ) ) {
		return 0;
	}

	// Remove filter temporarily to prevent recursion.
	remove_filter( 'pre_option_bb-meprlms-enable', 'bb_meprlms_intercept_enable_option' );

	$value = bb_get_meprlms_settings( 'bb-meprlms-enable', null );

	add_filter( 'pre_option_bb-meprlms-enable', 'bb_meprlms_intercept_enable_option' );

	// If the key is absent from the serialized array, return 0 (disabled by default).
	// Returning false would tell WordPress "I did not intercept" and cause it to
	// look for a standalone wp_options row, which may be stale or missing.
	if ( null === $value ) {
		return 0;
	}

	return absint( $value );
}
add_filter( 'pre_option_bb-meprlms-enable', 'bb_meprlms_intercept_enable_option' );

/**
 * Lazily register Display Course Activity and Posts in Activity Feed fields.
 *
 * Registered via bb_admin_settings_before_get_feature hook because
 * bb_meprlms_course_activities() and bb_meprlms_get_post_types() depend
 * on functions loaded at bp_init, after settings registration at bp_loaded.
 * This hook fires during AJAX when all functions are available.
 *
 * @since 3.0.0
 *
 * @param string $feature_id Feature ID.
 *
 * @return void
 */
function bb_meprlms_lazy_register_activity_fields( $feature_id ) {
	static $registered = false;

	if ( 'meprlms' !== $feature_id ) {
		return;
	}

	// Prevent double-registration — hook fires on both GET and SAVE AJAX calls.
	if ( $registered ) {
		return;
	}
	$registered = true;

	if ( ! bp_is_active( 'activity' ) ) {
		return;
	}

	// ── Display Course Activity (toggle_list from bb_meprlms_course_activities). ──
	if ( function_exists( 'bb_meprlms_course_activities' ) ) {
		$course_activities = bb_meprlms_course_activities();
		$activity_options  = array();

		foreach ( $course_activities as $key => $label ) {
			$activity_options[] = array(
				'value' => $key,
				'label' => $label,
			);
		}

		if ( ! empty( $activity_options ) ) {
			bb_register_feature_field(
				$feature_id,
				'meprlms_settings',
				'meprlms_group_sync',
				array(
					'name'              => 'bb-meprlms-course-activity',
					'label'             => __( 'Display Course Activity', 'buddyboss-pro' ),
					'type'              => 'toggle_list',
					'options'           => $activity_options,
					'default'           => array(),
					'sanitize_callback' => 'bb_meprlms_sanitize_course_activities',
					'help_text'         => __( 'Any option selected above will appear on the group creation and management screens, allowing only site admins to enable or disable course activity posts for groups.', 'buddyboss-pro' ),
					'conditional'       => array(
						'field'  => 'bb-meprlms-enable',
						'value'  => true,
						'action' => 'show',
					),
					'order'             => 30,
				)
			);
		}
	}

	// ── Posts in Activity Feed (individual CPT toggles with comment children). ──
	if ( ! function_exists( 'bb_meprlms_get_post_types' ) ) {
		return;
	}

	$meprlms_post_types = bb_meprlms_get_post_types();
	if ( empty( $meprlms_post_types ) ) {
		return;
	}

	// Temporarily remove the MeprLMS exclusion filter so we can check real comment support.
	if ( function_exists( 'bb_feed_not_allowed_meprlms_post_types' ) ) {
		remove_filter( 'bb_feed_excluded_post_types', 'bb_feed_not_allowed_meprlms_post_types' );
	}

	$cpt_order = 40;
	$is_first  = true;

	foreach ( $meprlms_post_types as $post_type ) {
		$option_name         = bb_post_type_feed_option_name( $post_type );
		$comment_option_name = bb_post_type_feed_comment_option_name( $post_type );
		$post_type_obj       = get_post_type_object( $post_type );

		if ( ! $post_type_obj ) {
			continue;
		}

		// Parent toggle: enable CPT in activity feed.
		bb_register_feature_field(
			$feature_id,
			'meprlms_settings',
			'meprlms_group_sync',
			array(
				'name'              => $option_name,
				'label'             => $is_first ? __( 'Posts in Activity Feed', 'buddyboss-pro' ) : '',
				'description'       => $post_type_obj->labels->name,
				'type'              => 'toggle',
				'default'           => absint( bp_is_post_type_feed_enable( $post_type, false ) ),
				'sanitize_callback' => 'absint',
				'conditional'       => array(
					'field'  => 'bb-meprlms-enable',
					'value'  => true,
					'action' => 'show',
				),
				'group'             => 'meprlms_cpt_feed',
				'order'             => $cpt_order,
			)
		);

		// Comment checkbox — child under the CPT toggle.
		// MeprLMS dynamically adds 'comments' support to its CPTs via
		// add_post_type_support() when "Show Course Comments" is enabled in
		// MemberPress → Courses → Settings. When disabled, post_type_supports()
		// returns false and the checkbox should be disabled (greyed out).
		$is_cpt_comment_enabled = post_type_supports( $post_type, 'comments' );

		bb_register_feature_field(
			$feature_id,
			'meprlms_settings',
			'meprlms_group_sync',
			array(
				'name'              => $comment_option_name,
				'label'             => sprintf(
					/* translators: %s: post type label */
					__( 'Enable %s comments in the activity feed.', 'buddyboss-pro' ),
					$post_type_obj->labels->name
				),
				'type'              => 'checkbox',
				'default'           => absint( bb_is_post_type_feed_comment_enable( $post_type, false ) ),
				'sanitize_callback' => 'absint',
				'disabled'          => ! $is_cpt_comment_enabled,
				'parent_field'      => $option_name,
				'conditional'       => array(
					'field'  => 'bb-meprlms-enable',
					'value'  => true,
					'action' => 'show',
				),
				'group'             => 'meprlms_cpt_feed',
				'order'             => $cpt_order + 1,
			)
		);

		$cpt_order += 10;
		$is_first   = false;
	}

	// Re-add the exclusion filter.
	if ( function_exists( 'bb_feed_not_allowed_meprlms_post_types' ) ) {
		add_filter( 'bb_feed_excluded_post_types', 'bb_feed_not_allowed_meprlms_post_types' );
	}

	// Help text below CPT feed fields.
	bb_register_feature_field(
		$feature_id,
		'meprlms_settings',
		'meprlms_group_sync',
		array(
			'name'              => '_bb-meprlms-cpt-feed-help',
			'label'             => '',
			'type'              => 'notice',
			'notice_type'       => 'plain',
			'description'       => __( 'Select which custom post types show in the activity feed when members, instructors, and site owners publish them; you can select whether or not to show comments in these activity posts.', 'buddyboss-pro' ),
			'sanitize_callback' => '__return_empty_string',
			'conditional'       => array(
				'field'  => 'bb-meprlms-enable',
				'value'  => true,
				'action' => 'show',
			),
			'group'             => 'meprlms_cpt_feed',
			'order'             => $cpt_order,
		)
	);

	/**
	 * Fires after MeprLMS lazy activity/CPT fields are registered.
	 *
	 * Unlike bb_meprlms_after_register_settings_fields (which fires at
	 * registration time), this hook fires during AJAX when all MeprLMS
	 * functions are available.
	 *
	 * @since 3.0.0
	 *
	 * @param string $feature_id Feature ID.
	 */
	do_action( 'bb_meprlms_after_register_lazy_fields', $feature_id );
}
add_action( 'bb_admin_settings_before_get_feature', 'bb_meprlms_lazy_register_activity_fields' );
