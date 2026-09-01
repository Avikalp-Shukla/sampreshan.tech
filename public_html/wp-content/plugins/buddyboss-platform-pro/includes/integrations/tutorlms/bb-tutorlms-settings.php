<?php
/**
 * BuddyBoss TutorLMS Integration — Settings 2.0 Registration.
 *
 * Registers side panel, sections, and fields for the TutorLMS integration
 * in the Settings 2.0 React admin UI.
 *
 * @package BuddyBossPro/Integration/TutorLMS
 * @since 3.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Register TutorLMS Settings 2.0 panels, sections, and fields.
 *
 * Called from bb-tutorlms-loader.php at bb_after_register_features priority 15.
 *
 * @since 3.0.0
 *
 * @return void
 */
function bb_tutorlms_register_settings_2_panels() {

	$feature_id = 'tutorlms';

	// =========================================================================
	// SIDE PANEL: Tutor LMS Settings (default — single panel).
	// =========================================================================

	bb_register_side_panel(
		$feature_id,
		'tutorlms_settings',
		array(
			'title'      => __( 'Tutor LMS Settings', 'buddyboss-pro' ),
			'icon'       => array(
				'type'  => 'font',
				'class' => 'bb-icons-rl bb-icons-rl-gear-six',
			),
			'order'      => 10,
			'is_default' => true,
		)
	);

	// If TutorLMS plugin is not active, show "Requires Plugin to Activate" notice only.
	if ( ! function_exists( 'tutor' ) ) {
		bb_tutorlms_register_plugin_required_notice( $feature_id );
		return;
	} elseif ( ! bp_is_active( 'groups' ) ) {
		bb_tutorlms_register_require_component_notice( $feature_id );
		return;
	}

	// Note: The legacy bb-tutorlms-migration-notice (BuddyPress → BuddyBoss group
	// course migration) is intentionally not migrated to Settings 2.0. The migration
	// was introduced in v2.4.40 and sites upgrading from BuddyPress have had ample
	// time to migrate. Adding it here would increase complexity for a diminishing audience.

	// =========================================================================
	// SECTION 1: Tutor LMS Group Sync — with section-level toggle.
	// =========================================================================

	bb_register_feature_section(
		$feature_id,
		'tutorlms_settings',
		'tutorlms_group_sync',
		array(
			'title'          => __( 'Tutor LMS Group Sync', 'buddyboss-pro' ),
			'order'          => 10,
			'section_toggle' => 'bb-tutorlms-enable',
			'help_url'       => bp_get_admin_url(
				add_query_arg(
					array(
						'page'    => 'bp-help',
						'article' => '638227',
					),
					'admin.php'
				)
			),
		)
	);

	// Description as notice field (matches Figma: visible text, no border).
	bb_register_feature_field(
		$feature_id,
		'tutorlms_settings',
		'tutorlms_group_sync',
		array(
			'name'              => '_bb_tutorlms_group_sync_notice',
			'label'             => '',
			'type'              => 'notice',
			'notice_type'       => 'plain',
			'description'       => __( 'When enabled, the group sync functionality for Tutor LMS will be available.', 'buddyboss-pro' ),
			'sanitize_callback' => '__return_empty_string',
			'order'             => 5,
		)
	);

	// Course Visibility toggle.
	// Read default directly from serialized option because bb_get_tutorlms_settings()
	// is not available yet — it's defined in bb-tutorlms-functions.php which is loaded
	// by BB_TutorLMS_Integration::includes() at bp_setup_integrations (after this file runs).
	$bb_tutorlms_opts       = bp_get_option( 'bb-tutorlms', array() );
	$bb_tutorlms_opts       = is_array( $bb_tutorlms_opts ) ? $bb_tutorlms_opts : array();
	$course_visibility_dflt = isset( $bb_tutorlms_opts['bb-tutorlms-course-visibility'] ) ? $bb_tutorlms_opts['bb-tutorlms-course-visibility'] : 1;

	bb_register_feature_field(
		$feature_id,
		'tutorlms_settings',
		'tutorlms_group_sync',
		array(
			'name'              => 'bb-tutorlms-course-visibility',
			'label'             => __( 'Course Visibility', 'buddyboss-pro' ),
			'description'       => __( 'Allow instructors to link courses to groups during group creation and management.', 'buddyboss-pro' ),
			'type'              => 'toggle',
			'default'           => absint( $course_visibility_dflt ),
			'sanitize_callback' => 'absint',
			'conditional'       => array(
				'field'  => 'bb-tutorlms-enable',
				'value'  => true,
				'action' => 'show',
			),
			'order'             => 20,
		)
	);

	// Display Course Activity and Posts in Activity Feed fields are registered
	// lazily via bb_admin_settings_before_get_feature hook because
	// bb_tutorlms_course_activities() and bb_tutorlms_get_post_types() depend on
	// functions loaded at bp_init, after settings registration at bp_loaded.
	// See bb_tutorlms_lazy_register_activity_fields() below.

	/**
	 * Fires after TutorLMS settings panels and static fields are registered.
	 *
	 * Note: Activity and CPT feed fields are registered lazily via
	 * bb_admin_settings_before_get_feature and may not be available yet.
	 * Use bb_tutorlms_after_register_lazy_fields for fields that depend
	 * on those being present.
	 *
	 * @since 3.0.0
	 */
	do_action( 'bb_tutorlms_after_register_settings_fields' );
}
bb_tutorlms_register_settings_2_panels();

/**
 * Register "Requires Plugin to Activate" notice for when TutorLMS is not installed.
 *
 * @since 3.0.0
 *
 * @param string $feature_id Feature ID.
 *
 * @return void
 */
function bb_tutorlms_register_plugin_required_notice( $feature_id ) {

	bb_register_feature_section(
		$feature_id,
		'tutorlms_settings',
		'tutorlms_group_sync',
		array(
			'title'       => __( 'Tutor LMS Group Sync', 'buddyboss-pro' ),
			'description' => __( 'BuddyBoss Platform Pro has integration settings for TutorLMS. If using TutorLMS we add the ability to add courses to groups as an instructor and utilize the BuddyBoss activity feeds for Course, Lessons & Topics. We have also taken the time to style TutorLMS to match our theme for styling.', 'buddyboss-pro' ),
			'order'       => 10,
		)
	);

	bb_register_feature_field(
		$feature_id,
		'tutorlms_settings',
		'tutorlms_group_sync',
		array(
			'name'              => '_bb-tutorlms-plugin-required',
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
 * Matches legacy bb_tutorlms_require_component_notice() behavior: shows a
 * message explaining that Social Groups must be activated for TutorLMS sync.
 *
 * @since 3.0.0
 *
 * @param string $feature_id Feature ID.
 *
 * @return void
 */
function bb_tutorlms_register_require_component_notice( $feature_id ) {

	bb_register_feature_section(
		$feature_id,
		'tutorlms_settings',
		'tutorlms_group_sync',
		array(
			'title'       => __( 'Tutor LMS Group Sync', 'buddyboss-pro' ),
			'description' => __( 'BuddyBoss Platform Pro has integration settings for TutorLMS. If using TutorLMS we add the ability to add courses to groups as an instructor and utilize the BuddyBoss activity feeds for Course, Lessons & Topics. We have also taken the time to style TutorLMS to match our theme for styling.', 'buddyboss-pro' ),
			'order'       => 10,
		)
	);

	$groups_url = bp_get_admin_url( 'admin.php?page=bb-settings' );

	bb_register_feature_field(
		$feature_id,
		'tutorlms_settings',
		'tutorlms_group_sync',
		array(
			'name'              => '_bb-tutorlms-require-component',
			'label'             => '',
			'type'              => 'empty_state',
			'icon'              => 'bb-icons-rl bb-icons-rl-info',
			'empty_state_title' => sprintf(
				/* translators: 1: opening anchor tag, 2: closing anchor tag */
				__( 'You need to activate the %1$sSocial Groups%2$s in order to sync TutorLMS with Social Groups.', 'buddyboss-pro' ),
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
function bb_tutorlms_sanitize_course_activities( $value ) {
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
 * Custom save handler for TutorLMS settings.
 *
 * Settings 2.0 saves individual fields via update_option(), but TutorLMS
 * stores its settings in a serialized array under 'bb-tutorlms'. This handler
 * reads the saved values and merges them back into the serialized option.
 *
 * Also handles CPT feed options which are stored as individual options
 * (bp-feed-custom-post-type-{cpt}).
 *
 * @since 3.0.0
 *
 * @param string $feature_id Feature ID.
 * @param array  $settings   Full submitted settings array.
 * @param array  $saved      Keys and values saved by core handler.
 *
 * @return void
 */
function bb_tutorlms_handle_settings_save( $feature_id, $settings, $saved ) {
	if ( 'tutorlms' !== $feature_id ) {
		return;
	}

	$bb_tutorlms = bp_get_option( 'bb-tutorlms', array() );
	$bb_tutorlms = is_array( $bb_tutorlms ) ? $bb_tutorlms : array();
	$dirty       = false;

	// Merge serialized option keys from the saved fields.
	$serialized_keys = array( 'bb-tutorlms-enable', 'bb-tutorlms-course-visibility', 'bb-tutorlms-course-activity' );

	foreach ( $serialized_keys as $key ) {
		if ( ! isset( $saved[ $key ] ) ) {
			continue;
		}

		$bb_tutorlms[ $key ] = $saved[ $key ];
		$dirty               = true;

		// Delete the individual option created by core save — it should only live in serialized array.
		delete_option( $key );
	}

	if ( $dirty ) {
		bp_update_option( 'bb-tutorlms', $bb_tutorlms );
	}

	// CPT feed options (bp-feed-custom-post-type-*) use standard option names
	// and are saved directly by the AJAX handler — no custom handling needed.
	// However, we must sync the blogs component state after CPT feed toggles change.
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
	if ( function_exists( 'bb_tutorlms_get_post_types' ) && function_exists( 'bb_post_type_feed_option_name' ) && function_exists( 'bb_post_type_feed_comment_option_name' ) ) {
		foreach ( bb_tutorlms_get_post_types() as $post_type ) {
			$pt_opt  = bb_post_type_feed_option_name( $post_type );
			$ptc_opt = bb_post_type_feed_comment_option_name( $post_type );

			if ( empty( bp_get_option( $pt_opt, '' ) ) ) {
				bp_update_option( $ptc_opt, 0 );
			}
		}
	}
}
add_action( 'bb_admin_save_feature_settings_after', 'bb_tutorlms_handle_settings_save', 10, 3 );

/**
 * Enrich TutorLMS field data for Settings 2.0 AJAX response.
 *
 * TutorLMS stores settings in a serialized 'bb-tutorlms' option, so the
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
function bb_tutorlms_enrich_field_data( $field_data, $field, $feature_id ) {
	if ( 'tutorlms' !== $feature_id ) {
		return $field_data;
	}

	// Guard: helper function may not be loaded if integration is disabled via bridge.
	if ( ! function_exists( 'bb_get_tutorlms_settings' ) ) {
		return $field_data;
	}

	$field_name = $field_data['name'] ?? ''; // phpcs:ignore PHPCompatibility.Operators.NewOperators.t_coalesceFound -- PHP 7.4+ required.

	// Enrich serialized option fields — read from bb-tutorlms array.
	// Note: bb-tutorlms-enable is handled by section_toggle + pre_option filter, not as a field.
	if ( 'bb-tutorlms-course-visibility' === $field_name ) {
		$field_data['value'] = absint( bb_get_tutorlms_settings( 'bb-tutorlms-course-visibility', 1 ) );
		return $field_data;
	}

	// Enrich course activity toggle_list — build {key: 0|1} from serialized sub-array.
	if ( 'bb-tutorlms-course-activity' === $field_name ) {
		$activities = bb_get_tutorlms_settings( 'bb-tutorlms-course-activity', array() );
		$all_keys   = function_exists( 'bb_tutorlms_course_activities' ) ? array_keys( bb_tutorlms_course_activities() ) : array();

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

	// CPT comment fields: value must respect TutorLMS global comment support.
	// The DB may store '1' but if TutorLMS has comments disabled for this CPT,
	// the effective value is 0 (matching legacy bb_is_post_type_feed_comment_enable behavior).
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
add_filter( 'bb_admin_settings_format_field_data', 'bb_tutorlms_enrich_field_data', 10, 3 );

/**
 * Intercept reads to bb-tutorlms-enable option.
 *
 * The section toggle mechanism reads bb-tutorlms-enable via bp_get_option(),
 * but the value lives in the serialized 'bb-tutorlms' option. This filter
 * returns the correct value from the serialized array.
 *
 * @since 3.0.0
 *
 * @param mixed $pre_option The pre-option value. Default false.
 *
 * @return int The option value from serialized array (0 or 1).
 */
function bb_tutorlms_intercept_enable_option( $pre_option ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found -- Required by pre_option filter signature.
	// Guard: helper function may not be loaded if integration is disabled via bridge.
	if ( ! function_exists( 'bb_get_tutorlms_settings' ) ) {
		return 0;
	}

	// Remove filter temporarily to prevent recursion.
	remove_filter( 'pre_option_bb-tutorlms-enable', 'bb_tutorlms_intercept_enable_option' );

	$value = bb_get_tutorlms_settings( 'bb-tutorlms-enable', null );

	add_filter( 'pre_option_bb-tutorlms-enable', 'bb_tutorlms_intercept_enable_option' );

	// If the key is absent from the serialized array, return 0 (disabled by default).
	// Returning false would tell WordPress "I did not intercept" and cause it to
	// look for a standalone wp_options row, which may be stale or missing.
	if ( null === $value ) {
		return 0;
	}

	return absint( $value );
}
add_filter( 'pre_option_bb-tutorlms-enable', 'bb_tutorlms_intercept_enable_option' );

/**
 * Lazily register Display Course Activity and Posts in Activity Feed fields.
 *
 * Registered via bb_admin_settings_before_get_feature hook because
 * bb_tutorlms_course_activities() and bb_tutorlms_get_post_types() depend
 * on functions loaded at bp_init, after settings registration at bp_loaded.
 * This hook fires during AJAX when all functions are available.
 *
 * @since 3.0.0
 *
 * @param string $feature_id Feature ID.
 *
 * @return void
 */
function bb_tutorlms_lazy_register_activity_fields( $feature_id ) {
	static $registered = false;

	if ( 'tutorlms' !== $feature_id ) {
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

	// ── Display Course Activity (toggle_list from bb_tutorlms_course_activities). ──
	if ( function_exists( 'bb_tutorlms_course_activities' ) ) {
		$course_activities = bb_tutorlms_course_activities();
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
				'tutorlms_settings',
				'tutorlms_group_sync',
				array(
					'name'              => 'bb-tutorlms-course-activity',
					'label'             => __( 'Display Course Activity', 'buddyboss-pro' ),
					'type'              => 'toggle_list',
					'options'           => $activity_options,
					'default'           => array(),
					'sanitize_callback' => 'bb_tutorlms_sanitize_course_activities',
					'help_text'         => __( 'Any option selected above will appear on the group creation and management screens, allowing the group organizer to enable or disable course activity posts for their group.', 'buddyboss-pro' ),
					'conditional'       => array(
						'field'  => 'bb-tutorlms-enable',
						'value'  => true,
						'action' => 'show',
					),
					'order'             => 30,
				)
			);
		}
	}

	// ── Posts in Activity Feed (individual CPT toggles with comment children). ──
	if ( ! function_exists( 'bb_tutorlms_get_post_types' ) ) {
		return;
	}

	$tutorlms_post_types = bb_tutorlms_get_post_types();
	if ( empty( $tutorlms_post_types ) ) {
		return;
	}

	// Temporarily remove the TutorLMS exclusion filter so we can check real comment support.
	if ( function_exists( 'bb_feed_not_allowed_tutorlms_post_types' ) ) {
		remove_filter( 'bb_feed_excluded_post_types', 'bb_feed_not_allowed_tutorlms_post_types' );
	}

	$cpt_order = 40;
	$is_first  = true;

	foreach ( $tutorlms_post_types as $post_type ) {
		$option_name         = bb_post_type_feed_option_name( $post_type );
		$comment_option_name = bb_post_type_feed_comment_option_name( $post_type );
		$post_type_obj       = get_post_type_object( $post_type );

		if ( ! $post_type_obj ) {
			continue;
		}

		// Child: comment toggle or "not supported" description.
		$comments_not_allowed = in_array( $post_type, bb_feed_not_allowed_comment_post_types(), true );

		// Parent toggle: enable CPT in activity feed.
		$cpt_field_args = array(
			'name'              => $option_name,
			'label'             => $is_first ? __( 'Posts in Activity Feed', 'buddyboss-pro' ) : '',
			'description'       => $post_type_obj->labels->name,
			'type'              => 'toggle',
			'default'           => absint( bp_is_post_type_feed_enable( $post_type, false ) ),
			'sanitize_callback' => 'absint',
			'conditional'       => array(
				'field'  => 'bb-tutorlms-enable',
				'value'  => true,
				'action' => 'show',
			),
			'group'             => 'tutorlms_cpt_feed',
			'order'             => $cpt_order,
		);

		// For unsupported comment types, show inline help_text (no separator, no child row).
		if ( $comments_not_allowed ) {
			$cpt_field_args['help_text'] = sprintf(
				/* translators: %s: post type label */
				__( 'Comments are not supported for %s.', 'buddyboss-pro' ),
				$post_type_obj->labels->name
			);
		}

		bb_register_feature_field( $feature_id, 'tutorlms_settings', 'tutorlms_group_sync', $cpt_field_args );

		if ( ! $comments_not_allowed ) {
			// Comment checkbox — child under the CPT toggle.
			// Disabled when CPT global comments are off in TutorLMS settings.
			$is_cpt_comment_enabled = bb_activity_is_enabled_cpt_global_comment( $post_type );

			bb_register_feature_field(
				$feature_id,
				'tutorlms_settings',
				'tutorlms_group_sync',
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
						'field'  => 'bb-tutorlms-enable',
						'value'  => true,
						'action' => 'show',
					),
					'group'             => 'tutorlms_cpt_feed',
					'order'             => $cpt_order + 1,
				)
			);
		}

		$cpt_order += 10;
		$is_first   = false;
	}

	// Re-add the exclusion filter.
	if ( function_exists( 'bb_feed_not_allowed_tutorlms_post_types' ) ) {
		add_filter( 'bb_feed_excluded_post_types', 'bb_feed_not_allowed_tutorlms_post_types' );
	}

	// Help text below CPT feed fields.
	bb_register_feature_field(
		$feature_id,
		'tutorlms_settings',
		'tutorlms_group_sync',
		array(
			'name'              => '_bb-tutorlms-cpt-feed-help',
			'label'             => '',
			'type'              => 'notice',
			'notice_type'       => 'plain',
			'description'       => __( 'Select which custom post types show in the activity feed when members instructors and site owners publish them, you can select whether or not to show comments in these activity posts.', 'buddyboss-pro' ),
			'sanitize_callback' => '__return_empty_string',
			'conditional'       => array(
				'field'  => 'bb-tutorlms-enable',
				'value'  => true,
				'action' => 'show',
			),
			'group'             => 'tutorlms_cpt_feed',
			'order'             => $cpt_order,
		)
	);

	/**
	 * Fires after TutorLMS lazy activity/CPT fields are registered.
	 *
	 * Unlike bb_tutorlms_after_register_settings_fields (which fires at
	 * registration time), this hook fires during AJAX when all TutorLMS
	 * functions are available.
	 *
	 * @since 3.0.0
	 *
	 * @param string $feature_id Feature ID.
	 */
	do_action( 'bb_tutorlms_after_register_lazy_fields', $feature_id );
}
add_action( 'bb_admin_settings_before_get_feature', 'bb_tutorlms_lazy_register_activity_fields' );
