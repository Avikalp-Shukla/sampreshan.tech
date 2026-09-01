<?php
/**
 * BuddyBoss Groups Settings.
 *
 * @package BuddyBossPro/Platform Settings/Groups
 *
 * @since 1.2.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Set up the bb groups settings class.
 *
 * @since 1.2.0
 */
class BB_Pro_Groups_Settings {

	/**
	 * The single instance of the class.
	 *
	 * @since 1.2.0
	 *
	 * @var self
	 */
	private static $instance = null;

	/**
	 * Groups Settings Constructor.
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
	 * Setup actions for groups Settings.
	 *
	 * @since 1.2.0
	 */
	public function setup_actions() {

		// Settings 2.0 hooks (when Feature Registry is available).
		if ( function_exists( 'bb_register_feature' ) ) {
			// Enrich pro-only fields with real values at AJAX time.
			add_filter( 'bb_admin_settings_format_field_data', array( $this, 'bb_enrich_groups_field_data' ), 10, 3 );

			// Save pro fields with their pro-specific option names.
			add_action( 'bb_admin_save_feature_settings_after', array( $this, 'bb_admin_groups_settings_save_react' ), 10, 3 );
		} else {
			// @todo: Remove after 3 release.
			// Legacy Settings 1.0 hooks.
			// Registered group cover image width.
			add_filter( 'bb_admin_setting_field_bb-cover-group-width', array( $this, 'bb_admin_register_group_cover_image_width_field' ) );
			// Registered group cover image height.
			add_filter( 'bb_admin_setting_field_bb-cover-group-height', array( $this, 'bb_admin_register_group_cover_image_height_field' ) );

			// Registered group grid style.
			add_filter( 'bb_admin_setting_field_bb-group-directory-layout-grid-style', array( $this, 'bb_admin_register_group_grid_style_field' ) );

			// Registered group elements.
			add_filter( 'bb_admin_setting_field_bb-group-directory-layout-element-cover-images', array( $this, 'bb_admin_register_group_directory_layout_elements_field' ) );
			add_filter( 'bb_admin_setting_field_bb-group-directory-layout-element-avatars', array( $this, 'bb_admin_register_group_directory_layout_elements_field' ) );
			add_filter( 'bb_admin_setting_field_bb-group-directory-layout-element-group-privacy', array( $this, 'bb_admin_register_group_directory_layout_elements_field' ) );
			add_filter( 'bb_admin_setting_field_bb-group-directory-layout-element-group-type', array( $this, 'bb_admin_register_group_directory_layout_elements_field' ) );
			add_filter( 'bb_admin_setting_field_bb-group-directory-layout-element-last-activity', array( $this, 'bb_admin_register_group_directory_layout_elements_field' ) );
			add_filter( 'bb_admin_setting_field_bb-group-directory-layout-element-members', array( $this, 'bb_admin_register_group_directory_layout_elements_field' ) );
			add_filter( 'bb_admin_setting_field_bb-group-directory-layout-element-group-descriptions', array( $this, 'bb_admin_register_group_directory_layout_elements_field' ) );
			add_filter( 'bb_admin_setting_field_bb-group-directory-layout-element-join-buttons', array( $this, 'bb_admin_register_group_directory_layout_elements_field' ) );

			// Registered group header style.
			add_filter( 'bb_admin_setting_field_bb-group-header-style', array( $this, 'bb_admin_register_group_header_style_field' ) );

			// Registered group headers elements.
			add_filter( 'bb_admin_setting_field_bb-group-headers-element-group-type', array( $this, 'bb_admin_register_group_directory_layout_headers_elements_field' ) );
			add_filter( 'bb_admin_setting_field_bb-group-headers-element-group-activity', array( $this, 'bb_admin_register_group_directory_layout_headers_elements_field' ) );
			add_filter( 'bb_admin_setting_field_bb-group-headers-element-group-description', array( $this, 'bb_admin_register_group_directory_layout_headers_elements_field' ) );
			add_filter( 'bb_admin_setting_field_bb-group-headers-element-group-organizers', array( $this, 'bb_admin_register_group_directory_layout_headers_elements_field' ) );
			add_filter( 'bb_admin_setting_field_bb-group-headers-element-group-privacy', array( $this, 'bb_admin_register_group_directory_layout_headers_elements_field' ) );

			// Save settings.
			add_action( 'bp_admin_tab_setting_save', array( $this, 'bb_admin_registered_group_setting_fields_save' ), 10, 1 );
		}
	}

	/**
	 * Create field attributes array of group cover image width field.
	 *
	 * @since 1.2.0
	 *
	 * @param array $args Current field attribute array.
	 *
	 * @return array Field attributes array of group cover image width.
	 */
	public function bb_admin_register_group_cover_image_width_field( $args ) {
		$args['name']     = 'bb-pro-cover-group-width';
		$args['disabled'] = false;

		return $args;
	}

	/**
	 * Create field attributes array of group cover image height field.
	 *
	 * @since 1.2.0
	 *
	 * @param array $args Current field attribute array.
	 *
	 * @return array Field attributes array of group cover image height.
	 */
	public function bb_admin_register_group_cover_image_height_field( $args ) {
		$args['name']     = 'bb-pro-cover-group-height';
		$args['disabled'] = false;

		return $args;
	}

	/**
	 * Create field attributes array of group header style field.
	 *
	 * @since 1.2.0
	 *
	 * @param array $args Current field attribute array.
	 *
	 * @return array Field attributes array of group header style.
	 */
	public function bb_admin_register_group_header_style_field( $args ) {

		$args['name']     = 'bb-pro-group-single-page-header-style';
		$args['disabled'] = false;
		$args['value']    = bb_platform_pro_group_header_style();

		return $args;
	}

	/**
	 * Create field attributes array of group headers elements field.
	 *
	 * @since 1.2.0
	 *
	 * @param array $args Current field attribute array.
	 *
	 * @return array Field attributes array of group headers elements.
	 */
	public function bb_admin_register_group_directory_layout_headers_elements_field( $args ) {

		$args['name']     = 'bb-pro-group-single-page-headers-elements[]';
		$args['disabled'] = false;
		$args['selected'] = bb_platform_pro_group_headers_element_enable( $args['value'] ) ? $args['value'] : '';

		return $args;
	}

	/**
	 * Create field attributes array of group grid style field.
	 *
	 * @since 1.2.0
	 *
	 * @param array $args Current field attribute array.
	 *
	 * @return array Field attributes array of group grid style.
	 */
	public function bb_admin_register_group_grid_style_field( $args ) {
		$args['name']     = 'bb-pro-group-directory-layout-grid-style';
		$args['disabled'] = false;
		$args['value']    = bb_platform_pro_group_grid_style();

		return $args;
	}

	/**
	 * Create field attributes array of group elements field.
	 *
	 * @since 1.2.0
	 *
	 * @param array $args Current field attribute array.
	 *
	 * @return array Field attributes array of group elements.
	 */
	public function bb_admin_register_group_directory_layout_elements_field( $args ) {
		$args['name']     = 'bb-pro-group-directory-layout-elements[]';
		$args['disabled'] = false;
		$args['selected'] = bb_platform_pro_group_element_enable( $args['value'] ) ? $args['value'] : '';

		return $args;
	}

	/**
	 * Save registered settings to DB.
	 *
	 * @since 1.2.0
	 *
	 * @param string $current_tab Current setting tab.
	 */
	public function bb_admin_registered_group_setting_fields_save( $current_tab ) {

		if ( 'bp-groups' === $current_tab ) {

			// Group cover sizes default options.
			$group_cover_width  = 'default';
			$group_cover_height = 'small';

			// Group style default options.
			$group_grid_style   = 'left';
			$group_header_style = 'left';
			$headers_elements   = array(
				'group-type',
				'group-activity',
				'group-description',
				'group-organizers',
				'group-privacy',
			);
			$group_elements     = array(
				'cover-images',
				'avatars',
				'group-privacy',
				'group-type',
				'last-activity',
				'members',
				'group-descriptions',
				'join-buttons',
			);

			if ( ! bb_pro_should_lock_features() ) {
				// phpcs:ignore WordPress.Security.NonceVerification.Missing
				$group_cover_width = isset( $_POST['bb-pro-cover-group-width'] ) ? sanitize_text_field( wp_unslash( $_POST['bb-pro-cover-group-width'] ) ) : 'default';
				// phpcs:ignore WordPress.Security.NonceVerification.Missing
				$group_cover_height = isset( $_POST['bb-pro-cover-group-height'] ) ? sanitize_text_field( wp_unslash( $_POST['bb-pro-cover-group-height'] ) ) : 'small';

				// phpcs:ignore WordPress.Security.NonceVerification.Missing
				$group_grid_style = isset( $_POST['bb-pro-group-directory-layout-grid-style'] ) ? sanitize_text_field( wp_unslash( $_POST['bb-pro-group-directory-layout-grid-style'] ) ) : 'left';
				// phpcs:ignore WordPress.Security.NonceVerification.Missing
				if ( isset( $_POST['bb-pro-group-directory-layout-elements'] ) ) {
					// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotValidated
					$group_elements = is_array( $_POST['bb-pro-group-directory-layout-elements'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['bb-pro-group-directory-layout-elements'] ) ) : sanitize_text_field( wp_unslash( $_POST['bb-pro-group-directory-layout-elements'] ) );
				} else {
					$group_elements = array();
				}
				// phpcs:ignore WordPress.Security.NonceVerification.Missing
				$group_header_style = isset( $_POST['bb-pro-group-single-page-header-style'] ) ? sanitize_text_field( wp_unslash( $_POST['bb-pro-group-single-page-header-style'] ) ) : 'left';
				// phpcs:ignore WordPress.Security.NonceVerification.Missing
				if ( isset( $_POST['bb-pro-group-single-page-headers-elements'] ) ) {
					// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotValidated
					$headers_elements = is_array( $_POST['bb-pro-group-single-page-headers-elements'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['bb-pro-group-single-page-headers-elements'] ) ) : sanitize_text_field( wp_unslash( $_POST['bb-pro-group-single-page-headers-elements'] ) );
				} else {
					$headers_elements = array();
				}
			}

			bp_update_option( 'bb-pro-cover-group-width', $group_cover_width );
			bp_update_option( 'bb-pro-cover-group-height', $group_cover_height );

			bp_update_option( 'bb-pro-group-directory-layout-grid-style', $group_grid_style );
			bp_update_option( 'bb-pro-group-directory-layout-elements', $group_elements );

			bp_update_option( 'bb-pro-group-single-page-header-style', $group_header_style );
			bp_update_option( 'bb-pro-group-single-page-headers-elements', $headers_elements );
		}
	}

	/**
	 * Enrich groups pro-only field data at AJAX time for Settings 2.0.
	 *
	 * When Pro is active and licensed, this method removes the pro_only flag
	 * and loads saved values from Pro-specific option names.
	 *
	 * @since 3.0.0
	 *
	 * @param array  $field_data  Formatted field data.
	 * @param array  $field       Original field registration data.
	 * @param string $feature_id  Feature ID.
	 *
	 * @return array Modified field data.
	 */
	public function bb_enrich_groups_field_data( $field_data, $field, $feature_id ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundInMiddle -- $field is required by the filter signature but this method dispatches on $feature_id and $field_data['name'] only.

		// Only handle groups feature.
		if ( 'groups' !== $feature_id ) {
			return $field_data;
		}

		// If Pro features are locked, leave fields as-is (pro_only stays true).
		if ( bb_pro_should_lock_features() ) {
			return $field_data;
		}

		$field_name = $field_data['name'] ?? '';

		switch ( $field_name ) {

			// Header style (image_radio).
			case 'bb-group-header-style':
				$field_data['pro_only'] = false;
				$field_data['value']    = bb_platform_pro_group_header_style();
				break;

			// Header elements (toggle_list).
			case 'bb-group-headers-elements':
				$field_data['pro_only'] = false;

				// Pro stores as array of enabled element slugs; toggle_list needs key => 0/1.
				$saved_elements = bp_get_option(
					'bb-pro-group-single-page-headers-elements',
					array( 'group-type', 'group-activity', 'group-description', 'group-organizers', 'group-privacy' )
				);
				if ( ! is_array( $saved_elements ) ) {
					$saved_elements = array();
				}

				// Build toggle_list value from saved array.
				$toggle_value = array();
				if ( ! empty( $field_data['options'] ) && is_array( $field_data['options'] ) ) {
					foreach ( $field_data['options'] as $option ) {
						$key                  = $option['value'] ?? '';
						$toggle_value[ $key ] = in_array( $key, $saved_elements, true ) ? 1 : 0;
					}
				}
				$field_data['value'] = $toggle_value;
				break;

			// Grid style (image_radio).
			case 'bb-group-directory-layout-grid-style':
				$field_data['pro_only'] = false;
				$field_data['value']    = bb_platform_pro_group_grid_style();
				break;

			// Directory elements (toggle_list).
			case 'bb-group-directory-layout-elements':
				$field_data['pro_only'] = false;

				// Pro stores as array of enabled element slugs; toggle_list needs key => 0/1.
				$saved_elements = bp_get_option(
					'bb-pro-group-directory-layout-elements',
					array( 'cover-images', 'avatars', 'group-privacy', 'group-type', 'last-activity', 'members', 'group-descriptions', 'join-buttons' )
				);
				if ( ! is_array( $saved_elements ) ) {
					$saved_elements = array();
				}

				// Build toggle_list value from saved array.
				$toggle_value = array();
				if ( ! empty( $field_data['options'] ) && is_array( $field_data['options'] ) ) {
					foreach ( $field_data['options'] as $option ) {
						$key                  = $option['value'] ?? '';
						$toggle_value[ $key ] = in_array( $key, $saved_elements, true ) ? 1 : 0;
					}
				}
				$field_data['value'] = $toggle_value;
				break;

			// Cover image width: platform name -> pro option name.
			// Matches legacy bb_admin_setting_field_bb-cover-group-width filter pattern.
			case 'bb-cover-group-width':
				$field_data['pro_only'] = false;
				$field_data['name']     = 'bb-pro-cover-group-width';
				$field_data['value']    = bp_get_option( 'bb-pro-cover-group-width', 'default' );
				break;

			// Cover image height: platform name -> pro option name.
			// Matches legacy bb_admin_setting_field_bb-cover-group-height filter pattern.
			case 'bb-cover-group-height':
				$field_data['pro_only'] = false;
				$field_data['name']     = 'bb-pro-cover-group-height';
				$field_data['value']    = bp_get_option( 'bb-pro-cover-group-height', 'small' );
				break;
		}

		return $field_data;
	}

	/**
	 * Save groups pro-only fields with their pro-specific option names (Settings 2.0).
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
	 */
	public function bb_admin_groups_settings_save_react( $feature_id, $settings, $saved ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed -- $saved is required by the hook signature but this handler only needs $feature_id and $settings.

		// Only handle groups feature.
		if ( 'groups' !== $feature_id ) {
			return;
		}

		// Don't save Pro settings if features are locked.
		if ( bb_pro_should_lock_features() ) {
			return;
		}

		// Header style: platform name -> pro option name (allowlist validation).
		if ( isset( $settings['bb-group-header-style'] ) ) {
			$value = function_exists( 'bb_groups_sanitize_header_style' )
				? bb_groups_sanitize_header_style( $settings['bb-group-header-style'] )
				: sanitize_key( $settings['bb-group-header-style'] );
			bp_update_option( 'bb-pro-group-single-page-header-style', $value );
		}

		// Header elements: toggle_list (key => 0/1) -> pro array of enabled keys.
		if ( isset( $settings['bb-group-headers-elements'] ) && is_array( $settings['bb-group-headers-elements'] ) ) {
			$enabled = array();
			foreach ( $settings['bb-group-headers-elements'] as $key => $val ) {
				if ( absint( $val ) ) {
					$enabled[] = sanitize_key( $key );
				}
			}
			bp_update_option( 'bb-pro-group-single-page-headers-elements', $enabled );
		}

		// Grid style: platform name -> pro option name (allowlist validation).
		if ( isset( $settings['bb-group-directory-layout-grid-style'] ) ) {
			$value = function_exists( 'bb_groups_sanitize_grid_style' )
				? bb_groups_sanitize_grid_style( $settings['bb-group-directory-layout-grid-style'] )
				: sanitize_key( $settings['bb-group-directory-layout-grid-style'] );
			bp_update_option( 'bb-pro-group-directory-layout-grid-style', $value );
		}

		// Directory elements: toggle_list (key => 0/1) -> pro array of enabled keys.
		if ( isset( $settings['bb-group-directory-layout-elements'] ) && is_array( $settings['bb-group-directory-layout-elements'] ) ) {
			$enabled = array();
			foreach ( $settings['bb-group-directory-layout-elements'] as $key => $val ) {
				if ( absint( $val ) ) {
					$enabled[] = sanitize_key( $key );
				}
			}
			bp_update_option( 'bb-pro-group-directory-layout-elements', $enabled );
		}

		// Cover image sizes: children already use pro option names, save them explicitly.
		// Use allowlist sanitizers to prevent arbitrary values being stored.
		$allowed_widths  = array( 'default', 'full' );
		$allowed_heights = array( 'small', 'default', 'large' );

		if ( isset( $settings['bb-pro-cover-group-width'] ) ) {
			$cover_width = function_exists( 'bb_groups_sanitize_cover_width' )
				? bb_groups_sanitize_cover_width( $settings['bb-pro-cover-group-width'] )
				: ( in_array( $settings['bb-pro-cover-group-width'], $allowed_widths, true ) ? $settings['bb-pro-cover-group-width'] : 'default' );
			bp_update_option( 'bb-pro-cover-group-width', $cover_width );
		}

		if ( isset( $settings['bb-pro-cover-group-height'] ) ) {
			$cover_height = function_exists( 'bb_groups_sanitize_cover_height' )
				? bb_groups_sanitize_cover_height( $settings['bb-pro-cover-group-height'] )
				: ( in_array( $settings['bb-pro-cover-group-height'], $allowed_heights, true ) ? $settings['bb-pro-cover-group-height'] : 'small' );
			bp_update_option( 'bb-pro-cover-group-height', $cover_height );
		}
	}
}
