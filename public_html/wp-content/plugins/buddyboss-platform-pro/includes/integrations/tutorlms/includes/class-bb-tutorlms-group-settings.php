<?php
/**
 * BuddyBoss Groups TutorLMS Group Settings.
 *
 * @package BuddyBoss\Groups\TutorLMS
 * @since 2.4.40
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Class BB_TutorLMS_Group_Setting
 */
class BB_TutorLMS_Group_Setting extends BP_Group_Extension {
	/**
	 * Your __construct() method will contain configuration options for
	 * TutorLMS extension.
	 *
	 * @since 2.4.40
	 */
	public function __construct() {
		$can_allow_tab              = ! ( ! bb_tutorlms_enable() || ! bb_tutorlms_course_visibility() );
		$this->name                 = apply_filters( 'bb_tutorlms_courses_group_tab_name', __( 'Courses', 'buddyboss-pro' ) );
		$this->slug                 = tutor()->course_post_type;
		$this->create_step_position = 40;
		$this->nav_item_position    = 120;
		$this->enable_nav_item      = bb_tutorlms_enable();

		$args = array(
			'access'  => apply_filters( 'bb_tutorlms_courses_group_tab_enabled', $this->enable_nav_item ),
			'screens' => array(
				'create' => array(
					'enabled'  => apply_filters( 'bb_tutorlms_courses_group_tab_enabled/screen=create', $can_allow_tab ),
					'name'     => apply_filters( 'bb_tutorlms_courses_group_tab_name/screen=create', $this->name ),
					'slug'     => $this->slug,
					'position' => apply_filters( 'bb_tutorlms_courses_group_tab_position/screen=create', $this->create_step_position ),
				),

				'edit' => array(
					'enabled'  => apply_filters( 'bb_tutorlms_courses_group_tab_enabled/screen=edit', bb_tutorlms_manage_tab() ),
					'name'     => apply_filters( 'bb_tutorlms_courses_group_tab_name/screen=edit', $this->name ),
					'slug'     => $this->slug,
					'position' => apply_filters( 'bb_tutorlms_courses_group_tab_position/screen=edit', $this->nav_item_position ),
				),
			),
		);

		parent::init( $args );

		$this->setup_actions();
	}

	/**
	 * Setup the group courses class actions.
	 *
	 * @since 2.4.40
	 */
	private function setup_actions() {
		// Settings 2.0: Register group meta fields for the edit modal.
		if ( function_exists( 'bb_feature_registry' ) ) {
			add_action( 'bb_register_groups_meta_fields', array( $this, 'register_group_meta_fields' ), 10, 2 );
		} else {
			// @todo: Remove after 3 release. Legacy metabox for old Group Admin UI.
			add_action( 'bp_groups_admin_meta_boxes', array( $this, 'bb_tutorlms_group_admin_ui_edit_screen' ) );

			// Saves the TutorLMS options if they come from the BuddyBoss Group Admin UI.
			// @todo: Remove after 3 release.
			add_action( 'bp_group_admin_edit_after', array( $this, 'bb_tutorlms_admin_settings_screen_save' ) );
		}

		add_action( 'wp_ajax_bb_tutorlms_group_course', array( $this, 'bb_tutorlms_group_course' ) );
	}

	/**
	 * Adds a TutorLMS metabox to BuddyBoss Group Admin UI.
	 *
	 * @since 2.4.40
	 *
	 * @uses add_meta_box
	 */
	public function bb_tutorlms_group_admin_ui_edit_screen() {
		if ( ! bb_tutorlms_enable() || ! bb_tutorlms_course_visibility() ) {
			return;
		}

		add_meta_box(
			'bb_tutorlms_group_admin_ui_meta_box',
			__( 'TutorLMS', 'buddyboss-pro' ),
			array( $this, 'bb_tutorlms_group_admin_ui_display_metabox' ),
			get_current_screen()->id,
			'advanced',
			'low'
		);
	}

	/**
	 * Displays the TutorLMS metabox in BuddyBoss Group Admin UI.
	 *
	 * @param object $item (group object).
	 *
	 * @since 2.4.40
	 */
	public function bb_tutorlms_group_admin_ui_display_metabox( $item = false ) {
		$this->edit_screen( $item );
	}

	/**
	 * Save the admin Group TutorLMS settings on edit group.
	 *
	 * @since 2.4.40
	 *
	 * @param int $group_id Group ID.
	 */
	public function bb_tutorlms_admin_settings_screen_save( $group_id = 0 ) {

		// Bail if not a POST action or manage tab is disabled.
		if ( ! bp_is_post_request() || ! bb_tutorlms_manage_tab() ) {
			return;
		}

		// Admin Nonce check.
		check_admin_referer( 'groups_edit_save_tutorlms', 'tutorlms_group_admin_ui' );

		$group_id = ! empty( $group_id ) ? $group_id : bp_get_current_group_id();

		/**
		 * Fire before saving Tutor LMS group course settings in the admin group screen.
		 *
		 * @since 2.4.40
		 *
		 * @param int $group_id Group ID.
		 */
		do_action( 'bb_tutorlms_group_admin_screen_before_save', $group_id );

		$this->bb_tutorlms_save_group_settings( $group_id, $_POST );

		/**
		 * Fire after saving Tutor LMS group course settings in the admin group screen.
		 *
		 * @since 2.4.40
		 *
		 * @param int $group_id Group ID.
		 */
		do_action( 'bb_tutorlms_group_admin_screen_after_save', $group_id );
	}

	/**
	 * Function to fetch group courses.
	 *
	 * @since 2.4.40
	 *
	 * @return void
	 */
	public function bb_tutorlms_group_course() {
		tutor_utils()->checking_nonce();

		$search  = isset( $_GET['q'] ) ? sanitize_text_field( $_GET['q'] ) : '';
		$page    = isset( $_GET['page'] ) ? intval( $_GET['page'] ) : 1;
		$courses = bb_tutorlms_get_courses( array( 'fields' => 'ids', 's' => $search, 'paged' => $page ) );

		$matches = array();
		$more    = false;
		if ( ! empty( $courses->posts ) ) {
			foreach ( $courses->posts as $course_id ) {
				$matches[] = array(
					'label' => html_entity_decode( get_the_title( $course_id ), ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401 ),
					'value' => $course_id,
				);
			}

			$more = ! empty( $courses->found_posts ) && ( count( $matches ) < $courses->found_posts );
		}

		wp_die(
			wp_json_encode(
				array(
					'matches' => $matches,
					'more'    => $more,
				)
			)
		);
	}

	/**
	 * The primary display function for group courses.
	 *
	 * @since 2.4.40
	 *
	 * @param int|null $group_id ID of the group to display.
	 */
	public function display( $group_id = null ) {

		do_action( 'template_notices' );

		do_action( 'bb_tutorlms_before_courses_page_content' );

		bp_get_template_part( 'groups/single/courses' );

		do_action( 'bb_tutorlms_after_courses_page_content' );
	}

	/**
	 * Show courses settings when creating a group.
	 *
	 * @since 2.4.40
	 *
	 * @param int $group_id Group ID.
	 *
	 * @return void
	 */
	public function create_screen( $group_id = 0 ) {
		// Bail if not looking at this screen
		if ( ! bp_is_group_creation_step( $this->slug ) ) {
			return;
		}

		$group_id = $group_id ?: bp_get_new_group_id();

		bp_locate_template( 'groups/single/admin/edit-courses.php', true, true, array( 'action' => 'create', 'group_id' => $group_id ) );
	}

	/**
	 * Save the Group courses data on create.
	 *
	 * @since 2.4.40
	 *
	 * @param int $group_id Group ID.
	 */
	public function create_screen_save( $group_id = 0 ) {

		/**
		 * Fire before saving Tutor LMS group course settings in the create group screen.
		 *
		 * @since 2.4.40
		 *
		 * @param int $group_id Group ID.
		 */
		do_action( 'bb_tutorlms_create_group_screen_before_save', $group_id );

		// Nonce check.
		check_admin_referer( 'groups_create_save_' . $this->slug );

		$this->bb_tutorlms_save_group_settings( $group_id, $_POST );

		/**
		 * Fire before saving Tutor LMS group course settings in the create group screen.
		 *
		 * @since 2.4.40
		 *
		 * @param int $group_id Group ID.
		 */
		do_action( 'bb_tutorlms_create_group_screen_after_save', $group_id );
	}

	/**
	 * Displays the settings for course settings.
	 *
	 * @since 2.4.40
	 *
	 * @param int|object $group (the group to edit if in Group Admin UI).
	 */
	public function edit_screen( $group = null ) {
		$group_id  = empty( $group->id ) ? bp_get_new_group_id() : $group->id;
		if ( empty( $group_id ) ) {
			$group_id = $group;
		}

		bp_locate_template( 'groups/single/admin/edit-courses.php', true, true, array( 'action' => 'edit', 'group_id' => $group_id ) );
	}

	/**
	 * Save the Group courses data on manage.
	 *
	 * @since 2.4.40
	 *
	 * @param int $group_id Group ID.
	 */
	public function edit_screen_save( $group_id = 0 ) {

		/**
		 * Fire before saving Tutor LMS group course settings in the manage group screen.
		 *
		 * @since 2.4.40
		 *
		 * @param int $group_id Group ID.
		 */
		do_action( 'bb_tutorlms_group_manage_screen_before_save', $group_id );

		// Nonce check.
		check_admin_referer( 'groups_edit_save_tutorlms', 'tutorlms_group_admin_ui' );

		$this->bb_tutorlms_save_group_settings( $group_id, $_POST );

		/**
		 * Fire after saving Tutor LMS group course settings in the manage group screen.
		 *
		 * @since 2.4.40
		 *
		 * @param int $group_id Group ID.
		 */
		do_action( 'bb_tutorlms_group_manage_screen_after_save', $group_id );
	}

	/**
	 * Determine whether the current user should see this nav tab.
	 * Note that this controls only the display of the navigation item.
	 * Access to the tab is controlled by the user_can_visit() check.
	 *
	 * @since 2.4.40
	 *
	 * @param bool $user_can_see_nav_item Whether or not the user can see the nav item.
	 *
	 * @return bool
	 */
	public function user_can_see_nav_item( $user_can_see_nav_item = false ) {
		$group_id = bp_get_current_group_id();
		if ( ! bb_tutorlms_group_courses_is_enable( $group_id ) ) {
			return false;
		}

		$bb_tutorlms_groups = bb_load_tutorlms_group()->get(
			array(
				'group_id' => $group_id,
				'fields'   => 'course_id',
				'per_page' => 1,
			)
		);
		if ( empty( $bb_tutorlms_groups['courses'] ) ) {
			return false;
		}

		// Always allow moderators to see nav items, even if explicitly 'noone'
		if ( ( 'noone' !== $this->params['show_tab'] ) && bp_current_user_can( 'bp_moderate' ) ) {
			return true;
		}

		return $this->user_can_see_nav_item;
	}

	/**
	 * Determine whether the current user has access to visit this tab.
	 * Note that this controls the ability of a user to access a tab.
	 * Display of the navigation item is controlled by user_can_see_nav_item().
	 *
	 * @since 2.4.40
	 *
	 * @param bool $user_can_visit Whether or not the user can visit the tab.
	 *
	 * @return bool
	 */
	public function user_can_visit( $user_can_visit = false ) {
		$group_id = bp_get_current_group_id();
		if ( ! bb_tutorlms_group_courses_is_enable( $group_id ) ) {
			return false;
		}

		$bb_tutorlms_groups = bb_load_tutorlms_group()->get(
			array(
				'group_id' => $group_id,
				'fields'   => 'course_id',
				'per_page' => 1,
			)
		);
		if ( empty( $bb_tutorlms_groups['courses'] ) ) {
			return false;
		}

		// Always allow moderators to visit a tab, even if explicitly 'noone'
		if ( ( 'noone' !== $this->params['access'] ) && bp_current_user_can( 'bp_moderate' ) ) {
			return true;
		}

		return $this->user_can_visit;
	}

	/**
	 * Save group settings.
	 *
	 * @since 2.4.40
	 *
	 * @param int   $group_id Group ID.
	 * @param array $post     Post data.
	 *
	 * @return void
	 */
	public function bb_tutorlms_save_group_settings( $group_id, $post ) {
		if ( ! bb_tutorlms_manage_tab() ) {
			return;
		}

		$group_id = ! empty( $group_id ) ? $group_id : bp_get_current_group_id();

		$bb_tutorlms_groups = isset( $post['bb-tutorlms-group'] ) ? $post['bb-tutorlms-group'] : array();

		$edit_groups_courses_status = isset( $bb_tutorlms_groups['bb-tutorlms-group-course-is-enable'] ) ? (bool) $bb_tutorlms_groups['bb-tutorlms-group-course-is-enable'] : false;
		groups_update_groupmeta( $group_id, 'bb-tutorlms-group-course-is-enable', $edit_groups_courses_status );

		$groups_courses_activities = isset( $bb_tutorlms_groups['course-activity'] ) ? $bb_tutorlms_groups['course-activity'] : array();
		groups_update_groupmeta( $group_id, 'bb-tutorlms-groups-courses-activities', $groups_courses_activities );

		$courses = isset( $bb_tutorlms_groups['courses'] ) ? $bb_tutorlms_groups['courses'] : array();
		bb_load_tutorlms_group()->add( array( 'group_id' => $group_id, 'course_id' => $courses ) );
	}

	/**
	 * Register TutorLMS group meta fields for the Settings 2.0 edit modal.
	 *
	 * @since 3.0.0
	 *
	 * @param BB_Admin_Meta_Field_Registry $registry  The registry instance.
	 * @param string                       $component The component identifier.
	 */
	public function register_group_meta_fields( $registry, $component ) {

		// Enable TutorLMS courses checkbox.
		$registry->register(
			$component,
			'tutorlms_enable',
			array(
				'label'             => __( 'Yes, I want to add courses to this group', 'buddyboss-pro' ),
				'type'              => 'checkbox',
				'tab'               => 'integrations',
				'order'             => 600,
				'save_phase'        => 'after',
				'get_value'         => function ( $group ) {
					return bb_tutorlms_group_courses_is_enable( $group->id ) ? '1' : '0';
				},
				'save_value'        => function ( $group, $value ) {
					$enabled = ! empty( $value ) && '0' !== $value;
					groups_update_groupmeta( $group->id, 'bb-tutorlms-group-course-is-enable', $enabled );
				},
				'sanitize_callback' => 'intval',
				'is_visible'        => function () {
					return bb_tutorlms_manage_tab();
				},
			)
		);

		// TutorLMS course activities checkboxes.
		$registry->register(
			$component,
			'tutorlms_course_activities',
			array(
				'label'             => __( 'Course Activities', 'buddyboss-pro' ),
				'type'              => 'toggle_list',
				'tab'               => 'integrations',
				'order'             => 710,
				'save_phase'        => 'after',
				'conditional'       => array(
					'field' => 'tutorlms_enable',
					'value' => true,
				),
				'get_value'         => function ( $group ) {
					// Only show globally enabled activities (same as legacy template).
					$global_enabled   = bb_get_enabled_tutorlms_course_activities();
					$all_activities   = ! empty( $global_enabled ) ? bb_tutorlms_course_activities( $global_enabled ) : array();
					$saved_activities = bb_tutorlms_get_group_course_activities( $group->id );
					$result           = array();

					foreach ( array_keys( $all_activities ) as $slug ) {
						// Handle both legacy associative format (slug => '1') and flat indexed format.
						$is_saved        = isset( $saved_activities[ $slug ] ) || in_array( $slug, $saved_activities, true );
						$result[ $slug ] = $is_saved ? '1' : '0';
					}

					return $result;
				},
				'get_options'       => function () {
					// Only show globally enabled activities (same as legacy template).
					$global_enabled = bb_get_enabled_tutorlms_course_activities();
					$activities     = ! empty( $global_enabled ) ? bb_tutorlms_course_activities( $global_enabled ) : array();
					$options        = array();

					foreach ( $activities as $slug => $label ) {
						$options[] = array(
							'value' => $slug,
							'label' => $label,
						);
					}

					return $options;
				},
				'save_value'        => function ( $group, $value ) {
					// Store as associative array (slug => '1') to match legacy format
					// used by the frontend template's isset() check.
					$activities = array();
					if ( is_array( $value ) ) {
						foreach ( $value as $slug => $enabled ) {
							if ( ! empty( $enabled ) ) {
								$activities[ sanitize_key( $slug ) ] = '1';
							}
						}
					}
					groups_update_groupmeta( $group->id, 'bb-tutorlms-groups-courses-activities', $activities );
				},
				'sanitize_callback' => function ( $value ) {
					return map_deep( $value, 'sanitize_text_field' );
				},
				'is_visible'        => function () {
					// Hide if no activities are globally enabled (same as legacy template check).
					return bb_tutorlms_manage_tab() && ! empty( bb_get_enabled_tutorlms_course_activities() );
				},
			)
		);

		// TutorLMS course selection multi-select.
		$registry->register(
			$component,
			'tutorlms_courses',
			array(
				'label'             => __( 'Select Courses', 'buddyboss-pro' ),
				'type'              => 'ajax_multiselect',
				'tab'               => 'integrations',
				'order'             => 720,
				'save_phase'        => 'after',
				'conditional'       => array(
					'field' => 'tutorlms_enable',
					'value' => true,
				),
				'get_value'         => function ( $group ) {
					$loader = bb_load_tutorlms_group();
					if ( null === $loader ) {
						return array();
					}

					// Use per_page => 0 to skip pagination (absint(-1) = 1 which limits to 1 row).
					$data = $loader->get(
						array(
							'group_id' => $group->id,
							'fields'   => 'course_id',
							'per_page' => 0,
						)
					);

					return ! empty( $data['courses'] ) ? array_map( 'intval', $data['courses'] ) : array();
				},
				'get_extra_data'    => function ( $group ) {
					$loader = bb_load_tutorlms_group();
					if ( null === $loader ) {
						return array();
					}

					// Use per_page => 0 to skip pagination (absint(-1) = 1 which limits to 1 row).
					$data           = $loader->get(
						array(
							'group_id' => $group->id,
							'fields'   => 'course_id',
							'per_page' => 0,
						)
					);
					$course_ids     = ! empty( $data['courses'] ) ? $data['courses'] : array();
					$selected_items = array();

					foreach ( $course_ids as $course_id ) {
						$selected_items[] = array(
							'value' => intval( $course_id ),
							'label' => html_entity_decode( get_the_title( $course_id ), ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401 ),
						);
					}

					return array(
						'selected_items'     => $selected_items,
						'ajax_action'        => 'bb_tutorlms_group_course',
						'ajax_nonce'         => wp_create_nonce( 'tutor_nonce_action' ),
						'nonce_param'        => '_tutor_nonce',
						'search_placeholder' => __( 'Search courses...', 'buddyboss-pro' ),
					);
				},
				'save_value'        => function ( $group, $value ) {
					$course_ids = ! empty( $value ) ? array_map( 'absint', (array) $value ) : array();

					/**
					 * Fire before saving Tutor LMS group course settings in the admin group screen.
					 *
					 * @since 3.0.0
					 *
					 * @param int $group_id Group ID.
					 */
					do_action( 'bb_tutorlms_group_admin_screen_before_save', $group->id );

					$loader = bb_load_tutorlms_group();
					if ( null === $loader ) {
						return;
					}

					$loader->add(
						array(
							'group_id'  => $group->id,
							'course_id' => $course_ids,
						)
					);

					/**
					 * Fire after saving Tutor LMS group course settings in the admin group screen.
					 *
					 * @since 3.0.0
					 *
					 * @param int $group_id Group ID.
					 */
					do_action( 'bb_tutorlms_group_admin_screen_after_save', $group->id );
				},
				'sanitize_callback' => function ( $value ) {
					return array_map( 'absint', (array) $value );
				},
				'is_visible'        => function () {
					return bb_tutorlms_manage_tab();
				},
			)
		);
	}
}
