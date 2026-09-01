<?php
/**
 * BuddyBoss Profiles Settings.
 *
 * @package BuddyBossPro/PlatformSettings/Profiles
 *
 * @since 1.2.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Setup the bb profile settings class.
 *
 * @since 1.2.0
 */
class BB_Pro_Profiles_Settings {

	/**
	 * The single instance of the class.
	 *
	 * @since 1.2.0
	 *
	 * @var self
	 */
	private static $instance = null;

	/**
	 * Profile Settings Constructor.
	 *
	 * @since 1.2.0
	 */
	public function __construct() {

		// Include the code.
		$this->setup_actions();
	}

	/**
	 * Get the instance of this class.
	 *
	 * @since 1.2.0
	 *
	 * @return object Instance.
	 */
	public static function instance() {

		if ( null === self::$instance ) {
			$class_name     = __CLASS__;
			self::$instance = new $class_name();
		}

		return self::$instance;
	}

	/**
	 * Setup actions for Profile Settings.
	 *
	 * @since 1.2.0
	 */
	public function setup_actions() {

		// Settings 2.0 hooks (when Feature Registry is available).
		if ( function_exists( 'bb_register_feature' ) ) {
			// Enrich pro-only fields with real values at AJAX time.
			add_filter( 'bb_admin_settings_format_field_data', array( $this, 'bb_enrich_members_field_data' ), 10, 3 );

			// Save pro fields with their pro-specific option names.
			add_action( 'bb_admin_save_feature_settings_after', array( $this, 'bb_admin_members_settings_save_react' ), 10, 3 );
		} else {
			// @todo: Remove after 3 release.
			// Legacy Settings 1.0 hooks.

			// Registered profile cover image width.
			add_filter( 'bb_admin_setting_field_bb-cover-profile-width', array( $this, 'bb_admin_register_profile_cover_image_width_field' ) );
			// Registered profile cover image height.
			add_filter( 'bb_admin_setting_field_bb-cover-profile-height', array( $this, 'bb_admin_register_profile_cover_image_height_field' ) );

			// Registered profile header style.
			add_filter( 'bb_admin_setting_field_bb-profile-headers-layout-style', array( $this, 'bb_admin_register_profile_header_style_field' ) );
			// Registered profile header elements.
			add_filter( 'bb_admin_setting_field_bb-profile-headers-layout-elements-online-status', array( $this, 'bb_admin_register_profile_header_elements_field' ) );
			add_filter( 'bb_admin_setting_field_bb-profile-headers-layout-elements-profile-type', array( $this, 'bb_admin_register_profile_header_elements_field' ) );
			add_filter( 'bb_admin_setting_field_bb-profile-headers-layout-elements-member-handle', array( $this, 'bb_admin_register_profile_header_elements_field' ) );
			add_filter( 'bb_admin_setting_field_bb-profile-headers-layout-elements-joined-date', array( $this, 'bb_admin_register_profile_header_elements_field' ) );
			add_filter( 'bb_admin_setting_field_bb-profile-headers-layout-elements-last-active', array( $this, 'bb_admin_register_profile_header_elements_field' ) );
			add_filter( 'bb_admin_setting_field_bb-profile-headers-layout-elements-followers', array( $this, 'bb_admin_register_profile_header_elements_field' ) );
			add_filter( 'bb_admin_setting_field_bb-profile-headers-layout-elements-following', array( $this, 'bb_admin_register_profile_header_elements_field' ) );
			add_filter( 'bb_admin_setting_field_bb-profile-headers-layout-elements-social-networks', array( $this, 'bb_admin_register_profile_header_elements_field' ) );

			// Registered member directories elements.
			add_filter( 'bb_admin_setting_field_bb-member-directory-element-online-status', array( $this, 'bb_admin_register_member_directory_elements_field' ) );
			add_filter( 'bb_admin_setting_field_bb-member-directory-element-online', array( $this, 'bb_admin_register_member_directory_elements_field' ) );
			add_filter( 'bb_admin_setting_field_bb-member-directory-element-profile-type', array( $this, 'bb_admin_register_member_directory_elements_field' ) );
			add_filter( 'bb_admin_setting_field_bb-member-directory-element-followers', array( $this, 'bb_admin_register_member_directory_elements_field' ) );
			add_filter( 'bb_admin_setting_field_bb-member-directory-element-last-active', array( $this, 'bb_admin_register_member_directory_elements_field' ) );
			add_filter( 'bb_admin_setting_field_bb-member-directory-element-joined-date', array( $this, 'bb_admin_register_member_directory_elements_field' ) );

			// Registered member directories profile actions.
			add_filter( 'bb_admin_setting_field_bb-member-profile-action-follow', array( $this, 'bb_admin_register_member_directory_profile_actions_field' ) );
			add_filter( 'bb_admin_setting_field_bb-member-profile-action-connect', array( $this, 'bb_admin_register_member_directory_profile_actions_field' ) );
			add_filter( 'bb_admin_setting_field_bb-member-profile-action-message', array( $this, 'bb_admin_register_member_directory_profile_actions_field' ) );

			// Registered member directories primary action.
			add_filter( 'bb_admin_setting_field_bb-member-profile-primary-action', array( $this, 'bb_admin_register_member_directory_primary_action_field' ) );

			// Save settings.
			add_action( 'bp_admin_tab_setting_save', array( $this, 'bb_admin_registered_profile_setting_fields_save' ), 10, 1 );
		}
	}

	/**
	 * Enrich members pro-only field data at AJAX time for Settings 2.0.
	 *
	 * When Pro is active and licensed, this method removes the pro_only flag
	 * and loads saved values from Pro-specific option names.
	 *
	 * @since 3.0.0
	 *
	 * @param array  $field_data Formatted field data.
	 * @param array  $field      Original field registration data.
	 * @param string $feature_id Feature ID.
	 *
	 * @return array Modified field data.
	 */
	public function bb_enrich_members_field_data( $field_data, $field, $feature_id ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundInMiddle -- $field is the raw registration array; $field_data already contains the formatted version. Required by the filter signature.

		// Only handle members feature.
		if ( 'members' !== $feature_id ) {
			return $field_data;
		}

		// If Pro features are locked, leave fields as-is (pro_only stays true).
		if ( bb_pro_should_lock_features() ) {
			return $field_data;
		}

		$field_name = isset( $field_data['name'] ) ? $field_data['name'] : '';

		switch ( $field_name ) {

			// Profile header style (image_radio).
			case 'bb-profile-headers-layout-style':
				$field_data['pro_only'] = false;
				$field_data['value']    = function_exists( 'bb_platform_pro_profile_headers_style' )
					? bb_platform_pro_profile_headers_style()
					: bp_get_option( 'bb-pro-profile-headers-layout-style', 'left' );
				break;

			// Profile header elements (toggle_list).
			case 'bb-profile-headers-layout-elements':
				$field_data['pro_only'] = false;

				// Always override with dynamic options from getter — includes bp-hide/disabled
				// states based on active components. Platform hardcodes static options for the
				// Pro-disabled case; Pro replaces them with dynamic ones here.
				if ( function_exists( 'bb_get_profile_header_elements' ) && function_exists( 'bb_members_elements_to_options' ) ) {
					$field_data['options'] = bb_members_elements_to_options( bb_get_profile_header_elements() );
				}

				// Pro stores as array of enabled element slugs; toggle_list needs key => 0/1.
				$saved_elements = bp_get_option(
					'bb-pro-profile-headers-layout-elements',
					array( 'online-status', 'profile-type', 'member-handle', 'joined-date', 'last-active', 'followers', 'following', 'social-networks' )
				);
				if ( ! is_array( $saved_elements ) ) {
					$saved_elements = array();
				}

				// Build toggle_list value from saved array.
				$toggle_value = array();
				if ( ! empty( $field_data['options'] ) && is_array( $field_data['options'] ) ) {
					foreach ( $field_data['options'] as $option ) {
						$key                  = isset( $option['value'] ) ? $option['value'] : '';
						$toggle_value[ $key ] = in_array( $key, $saved_elements, true ) ? 1 : 0;
					}
				}
				$field_data['value'] = $toggle_value;
				break;

			// Member directory elements (toggle_list).
			case 'bb-member-directory-elements':
				$field_data['pro_only'] = false;

				// Always override with dynamic options from getter — includes bp-hide/disabled
				// states based on active components. Platform hardcodes static options for the
				// Pro-disabled case; Pro replaces them with dynamic ones here.
				if ( function_exists( 'bb_get_member_directory_elements' ) && function_exists( 'bb_members_elements_to_options' ) ) {
					$field_data['options'] = bb_members_elements_to_options( bb_get_member_directory_elements() );
				}

				// Pro stores as array of enabled element slugs; toggle_list needs key => 0/1.
				$saved_elements = bp_get_option(
					'bb-pro-member-directory-elements',
					array( 'online-status', 'profile-type', 'followers', 'last-active', 'joined-date' )
				);
				if ( ! is_array( $saved_elements ) ) {
					$saved_elements = array();
				}

				// Build toggle_list value from saved array.
				$toggle_value = array();
				if ( ! empty( $field_data['options'] ) && is_array( $field_data['options'] ) ) {
					foreach ( $field_data['options'] as $option ) {
						$key                  = isset( $option['value'] ) ? $option['value'] : '';
						$toggle_value[ $key ] = in_array( $key, $saved_elements, true ) ? 1 : 0;
					}
				}
				$field_data['value'] = $toggle_value;
				break;

			// Member directory profile actions (toggle_list).
			case 'bb-member-profile-actions':
				$field_data['pro_only'] = false;

				// Always override with dynamic options from getter — includes bp-hide/disabled
				// states based on active components. Platform hardcodes static options for the
				// Pro-disabled case; Pro replaces them with dynamic ones here.
				if ( function_exists( 'bb_get_member_directory_profile_actions' ) && function_exists( 'bb_members_elements_to_options' ) ) {
					$field_data['options'] = bb_members_elements_to_options( bb_get_member_directory_profile_actions() );
				}

				// Use existing getter so apply_filters('bb_pro_get_member_directory_profile_actions') fires.
				$saved_actions = function_exists( 'bb_platform_pro_get_member_directory_profile_actions' )
					? bb_platform_pro_get_member_directory_profile_actions()
					: bp_get_option( 'bb-pro-member-profile-actions', array( 'follow', 'connect', 'message' ) );
				if ( ! is_array( $saved_actions ) ) {
					$saved_actions = array();
				}

				// Build toggle_list value from saved array.
				$toggle_value = array();
				if ( ! empty( $field_data['options'] ) && is_array( $field_data['options'] ) ) {
					foreach ( $field_data['options'] as $option ) {
						$key                  = isset( $option['value'] ) ? $option['value'] : '';
						$toggle_value[ $key ] = in_array( $key, $saved_actions, true ) ? 1 : 0;
					}
				}
				$field_data['value'] = $toggle_value;
				break;

			// Member directory primary action (select).
			case 'bb-member-profile-primary-action':
				$field_data['pro_only'] = false;

				// Always override with dynamic options — only show non-disabled actions.
				// Platform hardcodes static options for the Pro-disabled case;
				// Pro replaces them with dynamic ones filtered by component state.
				if ( function_exists( 'bb_get_member_directory_profile_actions' ) && function_exists( 'bb_members_elements_to_options' ) ) {
					$all_options            = bb_members_elements_to_options( bb_get_member_directory_profile_actions() );
					$primary_action_options = array(
						array(
							'label' => __( 'None', 'buddyboss-pro' ),
							'value' => '',
						),
					);
					foreach ( $all_options as $option ) {
						if ( empty( $option['disabled'] ) ) {
							$primary_action_options[] = $option;
						}
					}
					$field_data['options'] = $primary_action_options;
				}

				// Use existing getter so apply_filters('bb_pro_get_member_directory_primary_action') fires.
				$field_data['value'] = function_exists( 'bb_platform_pro_get_member_directory_primary_action' )
					? bb_platform_pro_get_member_directory_primary_action()
					: bp_get_option( 'bb-pro-member-profile-primary-action', '' );
				break;

			// Profile cover image width (select, Pro renames from platform name).
			case 'bb-cover-profile-width':
				$field_data['pro_only'] = false;
				$field_data['name']     = 'bb-pro-cover-profile-width';
				$field_data['value']    = bp_get_option( 'bb-pro-cover-profile-width', 'default' );
				break;

			// Profile cover image height (select, Pro renames from platform name).
			case 'bb-cover-profile-height':
				$field_data['pro_only'] = false;
				$field_data['name']     = 'bb-pro-cover-profile-height';
				$field_data['value']    = bp_get_option( 'bb-pro-cover-profile-height', 'small' );
				break;
		}

		return $field_data;
	}

	/**
	 * Save members pro-only fields with their pro-specific option names (Settings 2.0).
	 *
	 * The core save handler saves fields using Platform field names. This method
	 * re-saves the values under Pro-specific option names so that existing Pro
	 * getter functions continue to work correctly.
	 *
	 * @since 3.0.0
	 *
	 * @param string $feature_id Feature ID.
	 * @param array  $settings   Full submitted settings from React.
	 * @param array  $saved      Keys and values saved by the core handler.
	 *
	 * @return void
	 */
	public function bb_admin_members_settings_save_react( $feature_id, $settings, $saved ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed -- $saved contains core-saved key/value pairs; available for context/extensions but intentionally unused here.

		// Only handle members feature.
		if ( 'members' !== $feature_id ) {
			return;
		}

		// Don't save Pro settings if features are locked.
		if ( bb_pro_should_lock_features() ) {
			return;
		}

		// Profile header style: platform name -> pro option name.
		if ( isset( $settings['bb-profile-headers-layout-style'] ) ) {
			$allowed = array( 'left', 'centered' );
			$value   = sanitize_key( $settings['bb-profile-headers-layout-style'] );
			if ( ! in_array( $value, $allowed, true ) ) {
				$value = 'left';
			}
			bp_update_option( 'bb-pro-profile-headers-layout-style', $value );
		}

		// Profile header elements: toggle_list (key => 0/1) -> pro array of enabled keys.
		if ( isset( $settings['bb-profile-headers-layout-elements'] ) && is_array( $settings['bb-profile-headers-layout-elements'] ) ) {
			$enabled = array();
			foreach ( $settings['bb-profile-headers-layout-elements'] as $key => $val ) {
				if ( absint( $val ) ) {
					$enabled[] = sanitize_key( $key );
				}
			}
			bp_update_option( 'bb-pro-profile-headers-layout-elements', $enabled );
		}

		// Member directory elements: toggle_list (key => 0/1) -> pro array of enabled keys.
		if ( isset( $settings['bb-member-directory-elements'] ) && is_array( $settings['bb-member-directory-elements'] ) ) {
			$enabled = array();
			foreach ( $settings['bb-member-directory-elements'] as $key => $val ) {
				if ( absint( $val ) ) {
					$enabled[] = sanitize_key( $key );
				}
			}
			bp_update_option( 'bb-pro-member-directory-elements', $enabled );
		}

		// Member directory profile actions: toggle_list (key => 0/1) -> pro array of enabled keys.
		if ( isset( $settings['bb-member-profile-actions'] ) && is_array( $settings['bb-member-profile-actions'] ) ) {
			$enabled = array();
			foreach ( $settings['bb-member-profile-actions'] as $key => $val ) {
				if ( absint( $val ) ) {
					$enabled[] = sanitize_key( $key );
				}
			}
			bp_update_option( 'bb-pro-member-profile-actions', $enabled );
		}

		// Member directory primary action.
		if ( isset( $settings['bb-member-profile-primary-action'] ) ) {
			bp_update_option( 'bb-pro-member-profile-primary-action', sanitize_text_field( $settings['bb-member-profile-primary-action'] ) );
		}

		// Cover image sizes: children already use pro option names.
		if ( isset( $settings['bb-pro-cover-profile-width'] ) ) {
			$allowed = array( 'default', 'full' );
			$value   = sanitize_key( $settings['bb-pro-cover-profile-width'] );
			if ( ! in_array( $value, $allowed, true ) ) {
				$value = 'default';
			}
			bp_update_option( 'bb-pro-cover-profile-width', $value );
		}

		if ( isset( $settings['bb-pro-cover-profile-height'] ) ) {
			$allowed = array( 'small', 'large' );
			$value   = sanitize_key( $settings['bb-pro-cover-profile-height'] );
			if ( ! in_array( $value, $allowed, true ) ) {
				$value = 'small';
			}
			bp_update_option( 'bb-pro-cover-profile-height', $value );
		}
	}

	/**
	 * Create field attributes array of profile cover image width field.
	 *
	 * @since 1.2.0
	 *
	 * @param array $args Current field attribute array.
	 *
	 * @return array Field attributes array of profile cover image width.
	 */
	public function bb_admin_register_profile_cover_image_width_field( $args ) {
		$args['name']     = 'bb-pro-cover-profile-width';
		$args['disabled'] = false;

		return $args;
	}

	/**
	 * Create field attributes array of profile cover image height field.
	 *
	 * @since 1.2.0
	 *
	 * @param array $args Current field attribute array.
	 *
	 * @return array Field attributes array of profile cover image height.
	 */
	public function bb_admin_register_profile_cover_image_height_field( $args ) {
		$args['name']     = 'bb-pro-cover-profile-height';
		$args['disabled'] = false;

		return $args;
	}

	/**
	 * Create field attributes array of profile header style field.
	 *
	 * @since 1.2.0
	 *
	 * @param array $args Current field attribute array.
	 *
	 * @return array Field attributes array of profile cover image height.
	 */
	public function bb_admin_register_profile_header_style_field( $args ) {
		$args['name']     = 'bb-pro-profile-headers-layout-style';
		$args['disabled'] = false;
		$args['value']    = bb_platform_pro_profile_headers_style();

		return $args;
	}

	/**
	 * Create field attributes array of profile header elements field.
	 *
	 * @since 1.2.0
	 *
	 * @param array $args Current field attribute array.
	 *
	 * @return array Field attributes array of profile cover image height.
	 */
	public function bb_admin_register_profile_header_elements_field( $args ) {
		$args['name']     = 'bb-pro-profile-headers-layout-elements[]';
		$args['disabled'] = false;
		$args['selected'] = bb_platform_pro_profile_header_element_enable( $args['value'] ) ? $args['value'] : '';

		return $args;
	}

	/**
	 * Create field attributes array of member directories elements field.
	 *
	 * @since 1.2.0
	 *
	 * @param array $args Current field attribute array.
	 *
	 * @return array Field attributes array of member directories elements.
	 */
	public function bb_admin_register_member_directory_elements_field( $args ) {
		$args['name']     = 'bb-pro-member-directory-elements[]';
		$args['disabled'] = false;

		return $args;
	}

	/**
	 * Create field attributes array of member directories profile actions field.
	 *
	 * @since 1.2.0
	 *
	 * @param array $args Current field attribute array.
	 *
	 * @return array Field attributes array of member directories profile actions.
	 */
	public function bb_admin_register_member_directory_profile_actions_field( $args ) {
		$args['name']     = 'bb-pro-member-profile-actions[]';
		$args['disabled'] = false;

		return $args;
	}

	/**
	 * Create field attributes array of member directories primary action field.
	 *
	 * @since 1.2.0
	 *
	 * @param array $args Current field attribute array.
	 *
	 * @return array Field attributes array of member directories primary action.
	 */
	public function bb_admin_register_member_directory_primary_action_field( $args ) {
		$args['name']     = 'bb-pro-member-profile-primary-action';
		$args['disabled'] = false;

		return $args;
	}

	/**
	 * Save registered settings to DB.
	 *
	 * @since 1.2.0
	 *
	 * @param string $current_tab Current setting tab.
	 */
	public function bb_admin_registered_profile_setting_fields_save( $current_tab ) {

		if ( 'bp-xprofile' === $current_tab ) {

			// Profile cover sizes default options.
			$profile_cover_width  = 'default';
			$profile_cover_height = 'small';

			// Group style default options.
			$profile_headers_style    = 'left';
			$profile_headers_elements = array( 'online-status', 'profile-type', 'member-handle', 'joined-date', 'last-active', 'followers', 'following', 'social-networks' );

			// Member directories default options.
			$member_directory_elements        = array( 'online-status', 'profile-type', 'followers', 'last-active', 'joined-date' );
			$member_directory_profile_actions = array( 'follow', 'connect', 'message' );
			$member_directory_primary_action  = '';

			if ( ! bb_pro_should_lock_features() ) {
				// phpcs:ignore WordPress.Security.NonceVerification.Missing
				$profile_cover_width = isset( $_POST['bb-pro-cover-profile-width'] ) ? sanitize_text_field( wp_unslash( $_POST['bb-pro-cover-profile-width'] ) ) : 'default';
				// phpcs:ignore WordPress.Security.NonceVerification.Missing
				$profile_cover_height = isset( $_POST['bb-pro-cover-profile-height'] ) ? sanitize_text_field( wp_unslash( $_POST['bb-pro-cover-profile-height'] ) ) : 'small';

				// phpcs:ignore WordPress.Security.NonceVerification.Missing
				$profile_headers_style = isset( $_POST['bb-pro-profile-headers-layout-style'] ) ? sanitize_text_field( wp_unslash( $_POST['bb-pro-profile-headers-layout-style'] ) ) : 'left';

				$profile_headers_elements = array();
				// phpcs:ignore WordPress.Security.NonceVerification.Missing
				if ( isset( $_POST['bb-pro-profile-headers-layout-elements'] ) ) {
					// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotValidated
					$profile_headers_elements = is_array( $_POST['bb-pro-profile-headers-layout-elements'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['bb-pro-profile-headers-layout-elements'] ) ) : sanitize_text_field( wp_unslash( $_POST['bb-pro-profile-headers-layout-elements'] ) );
				}

				$member_directory_elements = array();
				// phpcs:ignore WordPress.Security.NonceVerification.Missing
				if ( isset( $_POST['bb-pro-member-directory-elements'] ) ) {
					// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotValidated
					$member_directory_elements = is_array( $_POST['bb-pro-member-directory-elements'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['bb-pro-member-directory-elements'] ) ) : sanitize_text_field( wp_unslash( $_POST['bb-pro-member-directory-elements'] ) );
				}

				$member_directory_profile_actions = array();
				// phpcs:ignore WordPress.Security.NonceVerification.Missing
				if ( isset( $_POST['bb-pro-member-profile-actions'] ) ) {
					// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotValidated
					$member_directory_profile_actions = is_array( $_POST['bb-pro-member-profile-actions'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['bb-pro-member-profile-actions'] ) ) : sanitize_text_field( wp_unslash( $_POST['bb-pro-member-profile-actions'] ) );
				}
				// phpcs:ignore WordPress.Security.NonceVerification.Missing
				$member_directory_primary_action = isset( $_POST['bb-pro-member-profile-primary-action'] ) ? sanitize_text_field( wp_unslash( $_POST['bb-pro-member-profile-primary-action'] ) ) : '';
			}

			bp_update_option( 'bb-pro-cover-profile-width', $profile_cover_width );
			bp_update_option( 'bb-pro-cover-profile-height', $profile_cover_height );

			bp_update_option( 'bb-pro-profile-headers-layout-style', $profile_headers_style );
			bp_update_option( 'bb-pro-profile-headers-layout-elements', $profile_headers_elements );

			bp_update_option( 'bb-pro-member-directory-elements', $member_directory_elements );
			bp_update_option( 'bb-pro-member-profile-actions', $member_directory_profile_actions );
			bp_update_option( 'bb-pro-member-profile-primary-action', $member_directory_primary_action );
		}
	}
}
