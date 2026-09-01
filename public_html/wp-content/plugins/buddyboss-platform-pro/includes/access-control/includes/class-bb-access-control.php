<?php
/**
 * BuddyBoss Access Control Class.
 *
 * @since   1.0.7
 * @package BuddyBossPro
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Setup the bp access control class.
 *
 * @since 1.1.0
 */
class BB_Access_Control {

	/**
	 * Unique ID for the access control.
	 *
	 * @var string Access Control.
	 *
	 * @since 1.1.0
	 */
	public $id = 'access-control';

	/**
	 * Static cache for access control lists.
	 *
	 * @since 3.0.0
	 *
	 * @var array|null
	 */
	private static $access_control_lists_cache = null;

	/**
	 * Access Control Constructor.
	 *
	 * @since 1.1.0
	 */
	public function __construct() {

		// Include the code.
		$this->includes();
		$this->setup_actions();

	}

	/**
	 * Setup actions for access control.
	 *
	 * @since 1.1.0
	 */
	public function setup_actions() {

		add_action( 'bp_enqueue_scripts', array( $this, 'enqueue_script' ) );

		if ( ! function_exists( 'bb_register_feature' ) ) {
			// @todo: Remove after 3 release - start.
			add_action( 'bp_admin_enqueue_scripts', array( $this, 'enqueue_scripts_styles' ) );
			add_action( 'bp_admin_setting_activity_register_fields', array( $this, 'bb_admin_setting_activity_access_control_register_fields' ) );
			add_action( 'bp_admin_setting_groups_register_fields', array( $this, 'bb_admin_setting_groups_access_control_register_fields' ) );
			add_action( 'bp_admin_setting_messages_register_fields', array( $this, 'bb_admin_setting_messages_access_control_register_fields' ) );
			add_action( 'bp_admin_setting_media_register_fields', array( $this, 'bb_admin_setting_media_access_control_register_fields' ) );
			add_action( 'bp_admin_setting_friends_register_fields', array( $this, 'bb_admin_setting_friends_access_control_register_fields' ) );
			// @todo: Remove after 3 release - end.
		} else {
			// Settings 2.0: Enrich access-control field data for React UI.
			add_filter( 'bb_admin_settings_format_field_data', array( $this, 'bb_enrich_access_control_field_data' ), 10, 3 );
		}

		add_action( 'wp_ajax_get_access_control_level_options', array( $this, 'bb_get_access_control_level_options' ) );
		add_action( 'wp_ajax_plugin_get_access_control_level_options', array( $this, 'bb_get_plugin_access_control_level_options' ) );
		add_action( 'wp_ajax_gamipress_get_access_control_level_options', array( $this, 'bb_get_gamipress_access_control_level_options' ) );
	}

	/**
	 * Function will return the third party plugin options membership sub options based on the membership.
	 *
	 * @since 1.1.0
	 */
	public function bb_get_access_control_level_options() {

		if ( ! bp_current_user_can( 'bp_moderate' ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'You do not have permission to perform this action.', 'buddyboss-pro' ) ) );
		}

		$access_controls              = self::bb_get_access_control_lists();
		$selected_access_control_type = bb_pro_filter_input_string( INPUT_POST, 'value' );
		$key                          = bb_pro_filter_input_string( INPUT_POST, 'key' );
		$threaded                     = filter_input( INPUT_POST, 'threaded', FILTER_VALIDATE_BOOLEAN );
		$label                        = bb_pro_filter_input_string( INPUT_POST, 'label' );
		$sub_label                    = bb_pro_filter_input_string( INPUT_POST, 'sub_label' );
		$component_settings           = ! empty( $_POST['component_settings'] ) ? map_deep( wp_unslash( $_POST['component_settings'] ), 'sanitize_text_field' ) : array();
		$html                         = '';
		$ajax                         = true;

		// Settings 2.0: return JSON data instead of HTML when format=json.
		$format = bb_pro_filter_input_string( INPUT_POST, 'format' );
		if ( 'json' === $format && '' !== trim( $selected_access_control_type ) ) {
			check_ajax_referer( 'bb_admin_settings', 'nonce' );
			$options = $this->bb_get_options_for_type( $selected_access_control_type );
			wp_send_json_success( array( 'options' => $options ) );
		}

		// @todo: Remove after 3 release - start.
		check_ajax_referer( 'bb_access_control_admin', 'nonce' );

		if ( '' !== trim( $selected_access_control_type ) ) {
			$options_lists = $access_controls[ $selected_access_control_type ]['class']::instance()->get_level_lists();

			ob_start();
			foreach ( $options_lists as $option ) {
				$default = $option['default'];
				$disable = ( $default ) ? ' disabled' : '';
				$checked = ( $default ) ? ' checked' : '';

				if ( 'disabled' === trim( $disable ) && 'checked' === trim( $checked ) ) {
					continue;
				}

				if ( $threaded ) {
					require bb_access_control_path() . 'templates/multiple-options.php';
				} else {
					require bb_access_control_path() . 'templates/single-options.php';
				}
			}

			$html = ob_get_contents();
			ob_end_clean();

		}

		// Show info message when no records found.
		if ( empty( $html ) ) {

			$no_record_label = '';
			if ( 'bp_member_type' === $selected_access_control_type ) {
				$no_record_label = esc_html__( 'Profile types', 'buddyboss-pro' );
			} elseif ( 'gender' === $selected_access_control_type ) {
				$no_record_label = esc_html__( 'Gender', 'buddyboss-pro' );
			}

			$html = '<p class="description">' .
				sprintf(
					/* translators: Record type label. s*/
					esc_html__( 'No %s found.', 'buddyboss-pro' ),
					$no_record_label
				) .
			'</p>';
		}

		wp_send_json_success(
			array(
				'message' => $html,
			)
		);
		// @todo: Remove after 3 release - end.

	}

	/**
	 * Function will return the gamipress options membership sub options based on the membership.
	 *
	 * @since 1.1.0
	 */
	public function bb_get_gamipress_access_control_level_options() {

		if ( ! bp_current_user_can( 'bp_moderate' ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'You do not have permission to perform this action.', 'buddyboss-pro' ) ) );
		}

		$access_controls              = self::bb_get_access_control_lists();
		$selected_access_control_type = bb_pro_filter_input_string( INPUT_POST, 'value' );
		$key                          = bb_pro_filter_input_string( INPUT_POST, 'key' );
		$html                         = '';
		$threaded                     = filter_input( INPUT_POST, 'threaded', FILTER_VALIDATE_BOOLEAN );
		$label                        = bb_pro_filter_input_string( INPUT_POST, 'label' );
		$sub_label                    = bb_pro_filter_input_string( INPUT_POST, 'sub_label' );
		$component_settings           = ! empty( $_POST['component_settings'] ) ? map_deep( wp_unslash( $_POST['component_settings'] ), 'sanitize_text_field' ) : array();
		$ajax                         = true;

		// Settings 2.0: return JSON data instead of HTML when format=json.
		$format = bb_pro_filter_input_string( INPUT_POST, 'format' );
		if ( 'json' === $format && '' !== trim( $selected_access_control_type ) ) {
			check_ajax_referer( 'bb_admin_settings', 'nonce' );
			$options = $this->bb_get_options_for_type( 'gamipress', $selected_access_control_type );
			wp_send_json_success( array( 'options' => $options ) );
		}

		// @todo: Remove after 3 release - start.
		check_ajax_referer( 'bb_access_control_admin', 'nonce' );

		if ( '' !== trim( $selected_access_control_type ) ) {
			$plugin_lists  = $access_controls['gamipress']['class']::instance()->bb_get_access_control_gamipress_lists();
			$options_lists = $plugin_lists[ $selected_access_control_type ]['class']::instance()->get_level_lists();

			ob_start();
			foreach ( $options_lists as $option ) {

				$default = $option['default'];
				$disable = ( $default ) ? ' disabled' : '';
				$checked = ( $default ) ? ' checked' : '';

				if ( 'disabled' === trim( $disable ) && 'checked' === trim( $checked ) ) {
					continue;
				}

				if ( $threaded ) {
					require bb_access_control_path() . 'templates/multiple-options.php';
				} else {
					require bb_access_control_path() . 'templates/single-options.php';
				}
			}

			$html = ob_get_contents();
			ob_end_clean();

		}

		// Show info message when no records found.
		if ( empty( $html ) ) {
			$html = sprintf( '<p class="description" >No GamiPress %ss found.</p>', esc_html( ucfirst( $selected_access_control_type ) ) );
		}

		wp_send_json_success(
			array(
				'message' => $html,
			)
		);
		// @todo: Remove after 3 release - end.

	}

	/**
	 * Function will return the options membership sub options based on the membership.
	 *
	 * @since 1.1.0
	 */
	public function bb_get_plugin_access_control_level_options() {

		if ( ! bp_current_user_can( 'bp_moderate' ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'You do not have permission to perform this action.', 'buddyboss-pro' ) ) );
		}

		$access_controls              = self::bb_get_access_control_lists();
		$selected_access_control_type = bb_pro_filter_input_string( INPUT_POST, 'value' );
		$key                          = bb_pro_filter_input_string( INPUT_POST, 'key' );
		$html                         = '';
		$threaded                     = filter_input( INPUT_POST, 'threaded', FILTER_VALIDATE_BOOLEAN );
		$label                        = bb_pro_filter_input_string( INPUT_POST, 'label' );
		$sub_label                    = bb_pro_filter_input_string( INPUT_POST, 'sub_label' );
		$component_settings           = ! empty( $_POST['component_settings'] ) ? map_deep( wp_unslash( $_POST['component_settings'] ), 'sanitize_text_field' ) : array();
		$ajax                         = true;

		// Settings 2.0: return JSON data instead of HTML when format=json.
		$format = bb_pro_filter_input_string( INPUT_POST, 'format' );
		if ( 'json' === $format && '' !== trim( $selected_access_control_type ) ) {
			check_ajax_referer( 'bb_admin_settings', 'nonce' );
			$options = $this->bb_get_options_for_type( 'membership', $selected_access_control_type );
			wp_send_json_success( array( 'options' => $options ) );
		}

		// @todo: Remove after 3 release - start.
		check_ajax_referer( 'bb_access_control_admin', 'nonce' );

		if ( '' !== trim( $selected_access_control_type ) ) {
			$plugin_lists  = $access_controls['membership']['class']::instance()->bb_get_access_control_plugins_lists();
			$options_lists = $plugin_lists[ $selected_access_control_type ]['class']::instance()->get_level_lists();

			ob_start();
			if ( ! empty( $options_lists ) ) {
				foreach ( $options_lists as $option ) {
					$default = $option['default'];
					$disable = ( $default ) ? ' disabled' : '';
					$checked = ( $default ) ? ' checked' : '';

					if ( 'disabled' === trim( $disable ) && 'checked' === trim( $checked ) ) {
						continue;
					}

					if ( $threaded ) {
						require bb_access_control_path() . 'templates/multiple-options.php';
					} else {
						require bb_access_control_path() . 'templates/single-options.php';
					}
				}
			}

			$html = ob_get_contents();
			ob_end_clean();

		}

		// Show info message when no records found.
		if ( empty( $html ) ) {

			$no_record_label = esc_html__( 'Memberships', 'buddyboss-pro' );
			if ( 'learndash' === $selected_access_control_type ) {
				$no_record_label = esc_html__( 'Groups', 'buddyboss-pro' );
			}

			$html = '<p class="description">' .
				sprintf(
					/* translators: Record type label. s*/
					esc_html__( 'No %s found.', 'buddyboss-pro' ),
					$no_record_label
				) .
			'</p>';
		}

		wp_send_json_success(
			array(
				'message' => $html,
			)
		);
		// @todo: Remove after 3 release - end.

	}

	/**
	 * Enqueue admin related scripts and styles.
	 *
	 * @since 1.1.0
	 */
	public function enqueue_scripts_styles() {
		$min     = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
		$rtl_css = is_rtl() ? '-rtl' : '';
		wp_enqueue_style( 'bb-access-control-admin', bb_access_control_url( '/assets/css/bb-access-control-admin' . $rtl_css . $min . '.css' ), array(), bb_platform_pro()->version );
        wp_enqueue_script( 'bb-access-control-admin', bb_access_control_url( '/assets/js/bb-access-control-admin' . $min . '.js' ), array(), bb_platform_pro()->version ); // phpcs:ignore
		wp_localize_script(
			'bb-access-control-admin',
			'bbAccessControlAdminVars',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'bb_access_control_admin' ),
			)
		);

	}

	/**
	 * Enqueue related scripts and styles.
	 *
	 * @since 1.1.0
	 */
	public function enqueue_script() {
		$rtl_css = is_rtl() ? '-rtl' : '';
		$min     = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
		wp_enqueue_style( 'bb-access-control', bb_access_control_url( '/assets/css/bb-access-control' . $rtl_css . $min . '.css' ), array(), bb_platform_pro()->version );
	}


	/**
	 * Function which return the list of access control.
	 *
	 * @return array list of the access control.
	 * @since 1.1.0
	 */
	public static function bb_get_access_control_lists() {
		if ( null !== self::$access_control_lists_cache ) {
			return self::$access_control_lists_cache;
		}

		$access_controls = array(
			'wp_role'        => array(
				'label'      => __( 'WordPress Role', 'buddyboss-pro' ),
				'is_enabled' => true,
				'class'      => BB_Access_Control_WordPress_Role::class,
			),
			'bp_member_type' => array(
				'label'      => __( 'Profile Type', 'buddyboss-pro' ),
				'is_enabled' => ( true === bp_member_type_enable_disable() ) ? true : false,
				'class'      => BB_Access_Control_Member_Type::class,
			),
			'gamipress'      => array(
				'label'      => __( 'GamiPress', 'buddyboss-pro' ),
				'is_enabled' => ( class_exists( 'GamiPress' ) ) ? true : false,
				'class'      => BB_Access_Control_Gamipress::class,
			),
			'membership'     => array(
				'label'      => __( 'Membership', 'buddyboss-pro' ),
				'is_enabled' => ( BB_Access_Control_Access_Control::instance()->bb_is_access_control_available() ) ? true : false,
				'class'      => BB_Access_Control_Access_Control::class,
			),
			'gender'         => array(
				'label'      => __( 'Gender', 'buddyboss-pro' ),
				'is_enabled' => ( function_exists( 'bp_get_xprofile_gender_type_field_id' ) && bp_get_xprofile_gender_type_field_id() ) ? true : false,
				'class'      => BB_Access_Control_Gender::class,
			),
		);

		self::$access_control_lists_cache = apply_filters( 'bb_get_access_control_lists', $access_controls );

		return self::$access_control_lists_cache;
	}

	/**
	 * Clear the static cache for access control lists.
	 *
	 * Should be called after saving access control settings so that
	 * subsequent reads within the same request return fresh data.
	 *
	 * @since 3.0.0
	 */
	public static function bb_clear_access_control_lists_cache() {
		self::$access_control_lists_cache = null;
	}

	/**
	 * Register the settings field.
	 *
	 * @param string $setting settings field.
	 *
	 * @since 1.1.0
	 */
	public function bb_admin_setting_activity_access_control_register_fields( $setting = null ) {
		// Settings 2.0 context: hook fires via do_action_deprecated() with no $setting object.
		// Access control is registered separately via bb_enrich_access_control_field_data filter.
		if ( null === $setting ) {
			return;
		}

		// Main General Settings Section.
		if ( version_compare( BP_PLATFORM_VERSION, '1.5.7.3', '<=' ) ) {
			$setting->add_section(
				'activity_access_control_block',
				sprintf(
					'%1s <span class="require-licence">%2s</span>',
					__( 'Activity Access', 'buddyboss-pro' ),
					( bb_pro_should_lock_features() ? __( ' — requires license', 'buddyboss-pro' ) : '' )
				)
			);
		} else {
			$setting->add_section(
				'activity_access_control_block',
				sprintf(
					'%1s <span class="require-licence">%2s</span>',
					__( 'Activity Access', 'buddyboss-pro' ),
					( bb_pro_should_lock_features() ? __( ' — requires license', 'buddyboss-pro' ) : '' )
				),
				'',
				'bb_admin_access_control_setting_tutorial'
			);
		}

		if ( ! bb_pro_should_lock_features() ) {
			$args = array();
			$setting->add_field(
				bb_access_control_create_activity_key(),
				__( 'Activity Posts', 'buddyboss-pro' ),
				array(
					$this,
					'bb_admin_activity_access_control_setting_callback_create_activity',
				),
				'string',
				$args
			);

			if ( version_compare( BP_PLATFORM_VERSION, '1.5.7.3', '<=' ) ) {
				$setting->add_field( 'bb-access-control-setting-tutorial', '', 'bb_admin_access_control_setting_tutorial' );
			}

			// simply add notice.
			$setting->add_field(
				'activity_access_control_block_notice',
				sprintf(
					'<span class="access-control_settings-notice-wrapper" >
		<span class="access-control_settings-notice">%s</span>
		</span>',
					__( 'Note: These settings do not apply to administrators or group activity feeds.', 'buddyboss-pro' )
				),
				array( $this, 'bb_access_control_empty_callback' ),
				'string',
				$args
			);
		} else {
			// simply add notice.
			$setting->add_field(
				'connection_access_control_block_notice',
				sprintf(
					'<span class="access-control_settings-notice-wrapper no-access-controls" >
		<span class="access-control_settings-notice">%s</span>
		</span>',
					sprintf(
					/* translators: %1$s - Platform Pro string, %2$s - License URL */
						esc_html__( 'You need to activate a license key for %1$s to unlock this feature. %2$s', 'buddyboss-pro' ),
						'<strong>' . esc_html__( 'BuddyBoss Platform Pro', 'buddyboss-pro' ) . '</strong>',
						sprintf(
							'<a href="%s">%s</a>',
							esc_url(
								bp_get_admin_url(
									add_query_arg(
										array(
											'page' => 'buddyboss-updater',
											'tab'  => 'buddyboss_theme',
										),
										'admin.php'
									)
								)
							),
							esc_html__( 'Add License key', 'buddyboss-pro' )
						)
					)
				),
				array( $this, 'bb_access_control_empty_callback' ),
				'string',
				array()
			);
		}

	}

	/**
	 * Callback function for the create activity settings.
	 *
	 * @since 1.1.0
	 */
	public function bb_admin_activity_access_control_setting_callback_create_activity() {
		$label    = __( 'Select which members should have access to create activity posts, based on:', 'buddyboss-pro' );
		$settings = bb_access_control_create_activity_settings();
		self::bb_admin_print_access_control_setting( bb_access_control_create_activity_key(), bb_access_control_create_activity_key(), '', $label, $settings );
	}

	/**
	 * Register the settings field.
	 *
	 * @param string $setting settings field.
	 *
	 * @since 1.1.0
	 */
	public function bb_admin_setting_friends_access_control_register_fields( $setting ) {

		// Main General Settings Section.
		if ( version_compare( BP_PLATFORM_VERSION, '1.5.7.3', '<=' ) ) {
			$setting->add_section(
				'connection_access_control_block',
				sprintf(
					'%1s <span class="require-licence">%2s</span>',
					__( 'Connection Access', 'buddyboss-pro' ),
					( bb_pro_should_lock_features() ? __( ' — requires license', 'buddyboss-pro' ) : '' )
				)
			);
		} else {
			$setting->add_section(
				'connection_access_control_block',
				sprintf(
					'%1s <span class="require-licence">%2s</span>',
					__( 'Connection Access', 'buddyboss-pro' ),
					( bb_pro_should_lock_features() ? __( ' — requires license', 'buddyboss-pro' ) : '' )
				),
				'',
				'bb_admin_access_control_setting_tutorial'
			);
		}

		if ( ! bb_pro_should_lock_features() ) {

			$args = array();
			$setting->add_field(
				bb_access_control_friends_key(),
				__( 'Connection Request', 'buddyboss-pro' ),
				array(
					$this,
					'bb_admin_friends_access_control_setting_callback_connection',
				),
				'string',
				$args
			);

			if ( version_compare( BP_PLATFORM_VERSION, '1.5.7.3', '<=' ) ) {
				$setting->add_field( 'bb-access-control-setting-tutorial', '', 'bb_admin_access_control_setting_tutorial' );
			}

			// simply add notice.
			$setting->add_field(
				'connection_access_control_block_notice',
				sprintf(
					'<span class="access-control_settings-notice-wrapper" >
		<span class="access-control_settings-notice">%s</span>
		</span>',
					__( 'Note: These settings do not apply to administrators.', 'buddyboss-pro' )
				),
				array( $this, 'bb_access_control_empty_callback' ),
				'string',
				$args
			);

		} else {
			// simply add notice.
			$setting->add_field(
				'connection_access_control_block_notice',
				sprintf(
					'<span class="access-control_settings-notice-wrapper no-access-controls" >
		<span class="access-control_settings-notice">%s</span>
		</span>',
					sprintf(
					/* translators: %1$s - Platform Pro string, %2$s - License URL */
						esc_html__( 'You need to activate a license key for %1$s to unlock this feature. %2$s', 'buddyboss-pro' ),
						'<strong>' . esc_html__( 'BuddyBoss Platform Pro', 'buddyboss-pro' ) . '</strong>',
						sprintf(
							'<a href="%s">%s</a>',
							esc_url(
								bp_get_admin_url(
									add_query_arg(
										array(
											'page' => 'buddyboss-updater',
											'tab'  => 'buddyboss_theme',
										),
										'admin.php'
									)
								)
							),
							esc_html__( 'Add License key', 'buddyboss-pro' )
						)
					)
				),
				array( $this, 'bb_access_control_empty_callback' ),
				'string',
				array()
			);
		}
	}

	/**
	 * Empty Callback function for the display notices only.
	 *
	 * @since 1.1.0
	 */
	public function bb_access_control_empty_callback() {
		if ( bb_pro_should_lock_features() ) {
			?>
			<style type="text/css">
				#messages_access_control_block + p.submit {
					display: none;
				}
			</style>
			<?php
		}
	}

	/**
	 * Register the settings field.
	 *
	 * @param string $setting settings field.
	 *
	 * @since 1.1.0
	 */
	public function bb_admin_setting_messages_access_control_register_fields( $setting ) {

		// Main General Settings Section.
		if ( version_compare( BP_PLATFORM_VERSION, '1.5.7.3', '<=' ) ) {
			$setting->add_section(
				'messages_access_control_block',
				sprintf(
					'%1s <span class="require-licence">%2s</span>',
					__( 'Messages Access', 'buddyboss-pro' ),
					( bb_pro_should_lock_features() ? __( ' — requires license', 'buddyboss-pro' ) : '' )
				)
			);
		} else {
			$setting->add_section(
				'messages_access_control_block',
				sprintf(
					'%1s <span class="require-licence">%2s</span>',
					__( 'Messages Access', 'buddyboss-pro' ),
					( bb_pro_should_lock_features() ? __( ' — requires license', 'buddyboss-pro' ) : '' )
				),
				'',
				'bb_admin_access_control_setting_tutorial'
			);
		}

		if ( ! bb_pro_should_lock_features() ) {
			$args = array();
			$setting->add_field(
				bb_access_control_send_message_key(),
				__( 'Send Messages', 'buddyboss-pro' ),
				array(
					$this,
					'bb_admin_messages_access_control_setting_callback_send_message',
				),
				'string',
				$args
			);

			if ( version_compare( BP_PLATFORM_VERSION, '1.5.7.3', '<=' ) ) {
				$setting->add_field( 'bb-access-control-setting-tutorial', '', 'bb_admin_access_control_setting_tutorial' );
			}

			// simply add notice.
			$setting->add_field(
				'messages_access_control_block_notice',
				sprintf(
					'<span class="access-control_settings-notice-wrapper" >
		<span class="access-control_settings-notice">%s</span>
		</span>',
					__( 'Note: These settings do not apply to administrators or group messages.', 'buddyboss-pro' )
				),
				array( $this, 'bb_access_control_empty_callback' ),
				'string',
				$args
			);
		} else {
			// simply add notice.
			$setting->add_field(
				'messages_access_control_block_notice',
				sprintf(
					'<span class="access-control_settings-notice-wrapper no-access-control" >
		<span class="access-control_settings-notice">%s</span>
		</span>',
					sprintf(
					/* translators: %1$s - Platform Pro string, %2$s - License URL */
						esc_html__( 'You need to activate a license key for %1$s to unlock this feature. %2$s', 'buddyboss-pro' ),
						'<strong>' . esc_html__( 'BuddyBoss Platform Pro', 'buddyboss-pro' ) . '</strong>',
						sprintf(
							'<a href="%s">%s</a>',
							esc_url(
								bp_get_admin_url(
									add_query_arg(
										array(
											'page' => 'buddyboss-updater',
											'tab'  => 'buddyboss_theme',
										),
										'admin.php'
									)
								)
							),
							esc_html__( 'Add License key', 'buddyboss-pro' )
						)
					)
				),
				array( $this, 'bb_access_control_empty_callback' ),
				'string',
				array()
			);
		}
	}

	/**
	 * Empty callback of blocks.
	 *
	 * @since 1.1.0
	 */
	public function bb_admin_access_control_setting_callback_empty_block() {
		?>
		<style type="text/css">
			#messages_access_control_block + p.submit {
				display: none;
			}
		</style>
		<div class="section-bb_access_control_invalid_licence_settings_section">
			<p>
				<?php
				printf(
				/* translators: %1$s - Platform Pro string, %2$s - License URL */
					esc_html__( 'You need to activate a license key for %1$s to unlock this feature. %2$s', 'buddyboss-pro' ),
					'<strong>' . esc_html__( 'BuddyBoss Platform Pro', 'buddyboss-pro' ) . '</strong>',
					sprintf(
						'<a href="%s">%s</a>',
						esc_url(
							bp_get_admin_url(
								add_query_arg(
									array(
										'page' => 'buddyboss-updater',
										'tab'  => 'buddyboss_theme',
									),
									'admin.php'
								)
							)
						),
						esc_html__( 'Add License key', 'buddyboss-pro' )
					)
				)
				?>
			</p>
		</div>
		<?php
	}

	/**
	 * Function to show the default messsage when site doesn't have valid licence.
	 *
	 * @since 1.1.0
	 */
	public function bb_access_control_invalid_licence_callback_message() {

		printf(
		/* translators: %1$s - Platform Pro string, %2$s - License URL */
			esc_html__( 'You need to activate a license key for %1$s to unlock this feature. %2$s', 'buddyboss-pro' ),
			'<strong>' . esc_html__( 'BuddyBoss Platform Pro', 'buddyboss-pro' ) . '</strong>',
			sprintf(
				'<a href="%s">%s</a>',
				esc_url(
					bp_get_admin_url(
						add_query_arg(
							array(
								'page' => 'buddyboss-updater',
								'tab'  => 'buddyboss_theme',
							),
							'admin.php'
						)
					)
				),
				esc_html__( 'Add License key', 'buddyboss-pro' )
			)
		);

	}

	/**
	 * Callback function for the message settings.
	 *
	 * @since 1.1.0
	 */
	public function bb_admin_messages_access_control_setting_callback_send_message() {
		$label     = __( 'Select which members should have access to send messages to other members, based on:', 'buddyboss-pro' );
		$sub_label = __( 'Members with the {{option_value}} {{select_value}} can send messages to members with - Any Member / With Specific {{select_value}}(s)', 'buddyboss-pro' );
		$settings  = bb_access_control_send_messages_settings();
		self::bb_admin_print_access_control_setting( bb_access_control_send_message_key(), bb_access_control_send_message_key(), '', $label, $settings, true, $sub_label );
	}

	/**
	 * Callback function for the friendship settings.
	 *
	 * @since 1.1.0
	 */
	public function bb_admin_friends_access_control_setting_callback_connection() {
		$label     = __( 'Select which members should have access to send connection requests to other members, based on:', 'buddyboss-pro' );
		$sub_label = __( 'Members with the {{option_value}} {{select_value}} can send connection request to members with - Any Member / With Specific {{select_value}}(s)', 'buddyboss-pro' );
		$settings  = bb_access_control_friends_settings();
		self::bb_admin_print_access_control_setting( bb_access_control_friends_key(), bb_access_control_friends_key(), '', $label, $settings, true, $sub_label );
	}

	/**
	 * Register the settings field.
	 *
	 * @param string $setting settings field.
	 *
	 * @since 1.1.0
	 */
	public function bb_admin_setting_media_access_control_register_fields( $setting ) {

		// Main General Settings Section.
		if ( version_compare( BP_PLATFORM_VERSION, '1.5.7.3', '<=' ) ) {
			$setting->add_section(
				'media_access_control_block',
				sprintf(
					'%1s <span class="require-licence">%2s</span>',
					__( 'Media Access', 'buddyboss-pro' ),
					( bb_pro_should_lock_features() ? __( ' — requires license', 'buddyboss-pro' ) : '' )
				)
			);
		} else {
			$setting->add_section(
				'media_access_control_block',
				sprintf(
					'%1s <span class="require-licence">%2s</span>',
					__( 'Media Access', 'buddyboss-pro' ),
					( bb_pro_should_lock_features() ? __( ' — requires license', 'buddyboss-pro' ) : '' )
				),
				'',
				'bb_admin_access_control_setting_tutorial'
			);
		}

		if ( ! bb_pro_should_lock_features() ) {

			$args = array();

			$setting->add_field(
				bb_access_control_upload_media_key(),
				__( 'Upload Photos', 'buddyboss-pro' ),
				array(
					$this,
					'bb_admin_media_access_control_setting_callback_upload_photos',
				),
				'string',
				$args
			);

			$setting->add_field(
				bb_access_control_upload_document_key(),
				__( 'Upload Documents', 'buddyboss-pro' ),
				array(
					$this,
					'bb_admin_media_access_control_setting_callback_upload_documents',
				),
				'string',
				$args
			);

			$setting->add_field(
				bb_access_control_upload_video_key(),
				__( 'Upload Videos', 'buddyboss-pro' ),
				array(
					$this,
					'bb_admin_media_access_control_setting_callback_upload_videos',
				),
				'string',
				$args
			);

			if ( version_compare( BP_PLATFORM_VERSION, '1.5.7.3', '<=' ) ) {
				$setting->add_field( 'bb-access-control-setting-tutorial', '', 'bb_admin_access_control_setting_tutorial' );
			}

			// simply add notice.
			$setting->add_field(
				'media_access_control_block_notice',
				sprintf(
					'<span class="access-control_settings-notice-wrapper" >
		<span class="access-control_settings-notice">%s</span>
		</span>',
					__( 'Note: These settings do not apply to administrators or group media.', 'buddyboss-pro' )
				),
				array( $this, 'bb_access_control_empty_callback' ),
				'string',
				$args
			);
		} else {
			// simply add notice.
			$setting->add_field(
				'media_access_control_block_notice',
				sprintf(
					'<span class="access-control_settings-notice-wrapper no-access-controls" >
		<span class="access-control_settings-notice">%s</span>
		</span>',
					sprintf(
					/* translators: %1$s - Platform Pro string, %2$s - License URL */
						esc_html__( 'You need to activate a license key for %1$s to unlock this feature. %2$s', 'buddyboss-pro' ),
						'<strong>' . esc_html__( 'BuddyBoss Platform Pro', 'buddyboss-pro' ) . '</strong>',
						sprintf(
							'<a href="%s">%s</a>',
							esc_url(
								bp_get_admin_url(
									add_query_arg(
										array(
											'page' => 'buddyboss-updater',
											'tab'  => 'buddyboss_theme',
										),
										'admin.php'
									)
								)
							),
							esc_html__( 'Add License key', 'buddyboss-pro' )
						)
					)
				),
				array( $this, 'bb_access_control_empty_callback' ),
				'string',
				array()
			);
		}

	}

	/**
	 * Callback function for the upload media upload settings.
	 *
	 * @since 1.1.0
	 */
	public function bb_admin_media_access_control_setting_callback_upload_photos() {
		$label              = __( 'Select which members should have access to upload photos, based on:', 'buddyboss-pro' );
		$settings           = bb_access_control_upload_photos_settings();
		$component_settings = array(
			'component' => 'media',
			'notices'   => array(
				'disable_photos_creation' => array(
					'is_disabled' => ( ! bp_is_profile_media_support_enabled() && ! bp_is_messages_media_support_enabled() && ! bp_is_forums_media_support_enabled() ) ? true : false,
					'message'     => __( 'Enable upload photos settings above in either profiles or messages or forums, to control which members can upload photos in components above.', 'buddyboss-pro' ),
					'type'        => 'info',
				),
			),
		);
		self::bb_admin_print_access_control_setting( bb_access_control_upload_media_key(), bb_access_control_upload_media_key(), '', $label, $settings, false, '', $component_settings );
	}

	/**
	 * Callback function for the upload video upload settings.
	 *
	 * @since 1.1.4
	 */
	public function bb_admin_media_access_control_setting_callback_upload_videos() {
		$label              = __( 'Select which members should have access to upload videos, based on:', 'buddyboss-pro' );
		$settings           = bb_access_control_upload_videos_settings();
		$component_settings = array(
			'component' => 'video',
			'notices'   => array(
				'disable_videos_creation' => array(
					'is_disabled' => ( ! bp_is_profile_video_support_enabled() && ! bp_is_messages_video_support_enabled() && ! bp_is_forums_video_support_enabled() ) ? true : false,
					'message'     => __( 'Enable upload videos settings above in either profiles or messages or forums, to control which members can upload videos in components above.', 'buddyboss-pro' ),
					'type'        => 'info',
				),
			),
		);
		self::bb_admin_print_access_control_setting( bb_access_control_upload_video_key(), bb_access_control_upload_video_key(), '', $label, $settings, false, '', $component_settings );
	}

	/**
	 * Callback function for the upload document upload settings.
	 *
	 * @since 1.1.0
	 */
	public function bb_admin_media_access_control_setting_callback_upload_documents() {
		$label              = __( 'Select which members should have access to upload documents, based on:', 'buddyboss-pro' );
		$settings           = bb_access_control_upload_document_settings();
		$component_settings = array(
			'component' => 'document',
			'notices'   => array(
				'disable_document_creation' => array(
					'is_disabled' => ( ! bp_is_profile_document_support_enabled() && ! bp_is_messages_document_support_enabled() && ! bp_is_forums_document_support_enabled() ) ? true : false,
					'message'     => __( 'Enable upload documents settings above in either profiles or messages or forums, to control which members can upload documents in components above.', 'buddyboss-pro' ),
					'type'        => 'info',
				),
			),
		);
		self::bb_admin_print_access_control_setting( bb_access_control_upload_document_key(), bb_access_control_upload_document_key(), '', $label, $settings, false, '', $component_settings );
	}

	/**
	 * Callback function for the group create settings.
	 *
	 * @since 1.1.0
	 */
	public function bb_admin_group_access_control_setting_callback_create_groups() {
		$label    = __( 'Select which members should have access to create groups, based on:', 'buddyboss-pro' );
		$settings = bb_access_control_create_group_settings();

		$component_settings = array(
			'component' => 'groups',
			'notices'   => array(
				'disable_group_creation' => array(
					'is_disabled' => ( bp_restrict_group_creation() ) ? true : false,
					'message'     => __( 'Enable social group creation by all members above to control which members can create groups.', 'buddyboss-pro' ),
					'type'        => 'info',
				),
			),
		);
		self::bb_admin_print_access_control_setting( bb_access_control_create_group_key(), bb_access_control_create_group_key(), '', $label, $settings, false, '', $component_settings );
	}

	/**
	 * Register the settings field.
	 *
	 * @param string $setting settings field.
	 *
	 * @since 1.1.0
	 */
	public function bb_admin_setting_groups_access_control_register_fields( $setting ) {

		// Main General Settings Section.
		if ( version_compare( BP_PLATFORM_VERSION, '1.5.7.3', '<=' ) ) {
			$setting->add_section(
				'group_access_control_block',
				sprintf(
					'%1s <span class="require-licence">%2s</span>',
					__( 'Group Access', 'buddyboss-pro' ),
					( bb_pro_should_lock_features() ? __( ' — requires license', 'buddyboss-pro' ) : '' )
				)
			);
		} else {
			$setting->add_section(
				'group_access_control_block',
				sprintf(
					'%1s <span class="require-licence">%2s</span>',
					__( 'Group Access', 'buddyboss-pro' ),
					( bb_pro_should_lock_features() ? __( ' — requires license', 'buddyboss-pro' ) : '' )
				),
				'',
				'bb_admin_access_control_setting_tutorial'
			);
		}

		if ( ! bb_pro_should_lock_features() ) {

			$args = array();
			$setting->add_field(
				bb_access_control_create_group_key(),
				__( 'Create Groups', 'buddyboss-pro' ),
				array(
					$this,
					'bb_admin_group_access_control_setting_callback_create_groups',
				),
				'string',
				$args
			);
			$setting->add_field(
				bb_access_control_join_group_key(),
				__( 'Join Groups', 'buddyboss-pro' ),
				array(
					$this,
					'bb_admin_group_access_control_setting_callback_join_groups',
				),
				'string',
				$args
			);

			if ( version_compare( BP_PLATFORM_VERSION, '1.5.7.3', '<=' ) ) {
				$setting->add_field( 'bb-access-control-setting-tutorial', '', 'bb_admin_access_control_setting_tutorial' );
			}

			// simply add notice.
			$setting->add_field(
				'group_access_control_block_notice',
				sprintf(
					'<span class="access-control_settings-notice-wrapper" >
		<span class="access-control_settings-notice">%s</span>
		</span>',
					__( 'Note: These settings do not apply to administrators.', 'buddyboss-pro' )
				),
				array( $this, 'bb_access_control_empty_callback' ),
				'string',
				$args
			);

		} else {

			// simply add notice.
			$setting->add_field(
				'group_access_control_block_notice',
				sprintf(
					'<span class="access-control_settings-notice-wrapper no-access-controls" >
		<span class="access-control_settings-notice">%s</span>
		</span>',
					sprintf(
					/* translators: %1$s - Platform Pro string, %2$s - License URL */
						esc_html__( 'You need to activate a license key for %1$s to unlock this feature. %2$s', 'buddyboss-pro' ),
						'<strong>' . esc_html__( 'BuddyBoss Platform Pro', 'buddyboss-pro' ) . '</strong>',
						sprintf(
							'<a href="%s">%s</a>',
							esc_url(
								bp_get_admin_url(
									add_query_arg(
										array(
											'page' => 'buddyboss-updater',
											'tab'  => 'buddyboss_theme',
										),
										'admin.php'
									)
								)
							),
							esc_html__( 'Add License key', 'buddyboss-pro' )
						)
					)
				),
				array( $this, 'bb_access_control_empty_callback' ),
				'string',
				array()
			);
		}
	}

	/**
	 * Callback function for the group join group settings.
	 *
	 * @since 1.1.0
	 */
	public function bb_admin_group_access_control_setting_callback_join_groups() {
		$label    = __( 'Select which members should have access to join public groups or request access to private groups, based on:', 'buddyboss-pro' );
		$settings = bb_access_control_join_group_settings();
		self::bb_admin_print_access_control_setting( bb_access_control_join_group_key(), bb_access_control_join_group_key(), '', $label, $settings, false );
	}

	/**
	 * Function which will print the display settings.
	 *
	 * @param string $db_option_key           key to be stored in DB.
	 * @param string $option_name             option name.
	 * @param string $class_name              class name.
	 * @param string $label                   label to be displayed.
	 * @param array  $access_control_settings existing stored settings in DB.
	 * @param false  $multiple                show the threader or not.
	 * @param string $sub_label               sub label to be displayed.
	 * @param array  $component_settings      component settings.
	 *
	 * @since 1.1.0
	 */
	public function bb_admin_print_access_control_setting( $db_option_key = '', $option_name = '', $class_name = '', $label = '', $access_control_settings = array(), $multiple = false, $sub_label = '', $component_settings = array() ) {

		$option_access_controls = '';
		if ( isset( $access_control_settings ) && isset( $access_control_settings['access-control-type'] ) && '' !== $access_control_settings['access-control-type'] ) {
			$option_access_controls = $access_control_settings['access-control-type'];
		}

		$access_controls = self::bb_get_access_control_lists();
		$threaded        = $multiple;
		$multiple        = ( $multiple ) ? 'display-threaded' : 'single';
		$ajax            = false;

		require bb_access_control_path() . 'templates/core-options.php';

		$variable     = 'membership';
		$plugin_lists = $access_controls[ $variable ]['class']::instance()->bb_get_access_control_plugins_lists();
		require bb_access_control_path() . 'templates/plugin-options.php';

		$variable     = 'gamipress';
		$plugin_lists = $access_controls[ $variable ]['class']::instance()->bb_get_access_control_gamipress_lists();
		require bb_access_control_path() . 'templates/gamipress-options.php';

		require bb_access_control_path() . 'templates/checkboxes-selected.php';

	}

	/**
	 * Includes files.
	 *
	 * @param array $includes list of the files.
	 *
	 * @since 1.1.0
	 */
	public function includes( $includes = array() ) {

		$bb_platform_pro = bb_platform_pro();
		$slashed_path    = trailingslashit( $bb_platform_pro->access_control_dir );

		$includes = array(
			'cache',
			'filters',
			'rest-filters',
			'template',
			'functions',
		);

		// Loop through files to be included.
		foreach ( (array) $includes as $file ) {

			if ( empty( $this->bb_access_control_check_has_licence() ) ) {
				if ( in_array( $file, array( 'filters', 'rest-filters' ), true ) ) {
					continue;
				}
			}

			$paths = array(

				// Passed with no extension.
				'bb-' . $this->id . '-' . $file . '.php',
				'bb-' . $this->id . '/' . $file . '.php',

				// Passed with extension.
				$file,
				'bb-' . $this->id . '-' . $file,
				'bb-' . $this->id . '/' . $file,
			);

			foreach ( $paths as $path ) {
                // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
				if ( @is_file( $slashed_path . $path ) ) {
					require $slashed_path . $path;
					break;
				}
			}
		}
	}

	/**
	 * Function to return the default value if no licence.
	 *
	 * @param bool $has_access Whether has access.
	 *
	 * @since 1.1.0
	 *
	 * @return mixed Return the default.
	 */
	protected function bb_access_control_check_has_licence( $has_access = true ) {

		if ( bb_pro_should_lock_features() ) {
			return false;
		}

		return $has_access;

	}

	/**
	 * Populate access-control field data for the Settings 2.0 React UI.
	 *
	 * Returns the types dropdown
	 * options, the currently-selected type, and the initial toggle options
	 * for that type so the React component can render immediately without
	 * an extra AJAX round-trip on first load.
	 *
	 * @since 3.0.0
	 *
	 * @param array  $data       Default empty access-control data.
	 * @param string $field_name The field option name (e.g. 'bb-access-control-create-activity').
	 * @param string $feature_id Feature ID (e.g. 'activity').
	 *
	 * @return array Enriched data.
	 */
	public function bb_populate_access_control_field_data( $data, $field_name, $feature_id ) {

		// Map of supported features and their field-name → settings getter.
		$supported_features = array(
			'activity' => array(
				'bb-access-control-create-activity' => 'bb_access_control_create_activity_settings',
			),
			'media'    => array(
				'bb-access-control-upload-media'    => 'bb_access_control_upload_photos_settings',
				'bb-access-control-upload-video'    => 'bb_access_control_upload_videos_settings',
				'bb-access-control-upload-document' => 'bb_access_control_upload_document_settings',
			),
			'groups'   => array(
				'bb-access-control-create-groups' => 'bb_access_control_create_group_settings',
				'bb-access-control-join-groups'   => 'bb_access_control_join_group_settings',
			),
			'members'  => array(
				'bb-access-control-friends' => 'bb_access_control_friends_settings',
			),
			'messages' => array(
				'bb-access-control-send-message' => 'bb_access_control_send_messages_settings',
			),
		);

		// Only handle known features.
		if ( ! isset( $supported_features[ $feature_id ] ) ) {
			return $data;
		}

		// Build types array from the access-control lists.
		$lists = self::bb_get_access_control_lists();
		$types = array();
		foreach ( $lists as $key => $info ) {
			$type_data = array(
				'label'    => $info['label'],
				'value'    => $key,
				'disabled' => ! $info['is_enabled'],
			);

			// Check if this type has sub-types (provider does not have get_level_lists).
			if ( ! empty( $info['class'] ) && class_exists( $info['class'] ) ) {
				$provider = $info['class']::instance();

				if ( ! method_exists( $provider, 'get_level_lists' ) ) {
					$type_data['sub_types'] = $this->bb_get_sub_types_for_type( $provider );
				}
			}

			$types[] = $type_data;
		}
		$data['types'] = $types;

		// Load currently saved settings to populate the initial options.
		$field_getters = $supported_features[ $feature_id ];
		$settings_func = isset( $field_getters[ $field_name ] ) ? $field_getters[ $field_name ] : '';

		if ( ! empty( $settings_func ) && function_exists( $settings_func ) ) {
			$settings             = $settings_func();
			$data['current_type'] = isset( $settings['access-control-type'] ) ? $settings['access-control-type'] : '';

			// Include saved sub-type keys.
			$data['current_sub_type'] = '';
			if ( ! empty( $settings['plugin-access-control-type'] ) ) {
				$data['current_sub_type']     = $settings['plugin-access-control-type'];
				$data['current_sub_type_key'] = 'plugin-access-control-type';
			} elseif ( ! empty( $settings['gamipress-access-control-type'] ) ) {
				$data['current_sub_type']     = $settings['gamipress-access-control-type'];
				$data['current_sub_type_key'] = 'gamipress-access-control-type';
			}

			if ( ! empty( $data['current_type'] ) ) {
				$data['options'] = $this->bb_get_options_for_type( $data['current_type'], $data['current_sub_type'] );
			}

			// For threaded fields, include per-option sub-settings so React renders without extra AJAX.
			if ( ! empty( $settings['access-control-options'] ) && is_array( $settings['access-control-options'] ) ) {
				$per_option = array();
				foreach ( $settings['access-control-options'] as $opt_id ) {
					$sub_key = 'access-control-' . $opt_id . '-options';
					if ( isset( $settings[ $sub_key ] ) ) {
						$per_option[ $opt_id ] = $settings[ $sub_key ];
					}
				}
				if ( ! empty( $per_option ) ) {
					$data['per_option_settings'] = $per_option;
				}
			}
		}

		return $data;
	}

	/**
	 * Get options (label/value pairs) for a given access-control type.
	 *
	 * For direct types (wp_role, bp_member_type, gender) this returns
	 * the toggle options directly. For grouped types (membership, gamipress)
	 * a sub-type key must be provided to fetch the correct sub-provider options.
	 *
	 * @since 3.0.0
	 *
	 * @param string $type     Access-control type key (e.g. 'wp_role', 'membership').
	 * @param string $sub_type Optional sub-type key for grouped types (e.g. 'learndash', 'achievement').
	 *
	 * @return array Array of { label, value } items.
	 */
	private function bb_get_options_for_type( $type, $sub_type = '' ) {

		$lists   = self::bb_get_access_control_lists();
		$options = array();

		if ( ! isset( $lists[ $type ] ) || empty( $lists[ $type ]['class'] ) ) {
			return $options;
		}

		if ( ! class_exists( $lists[ $type ]['class'] ) ) {
			return $options;
		}

		$provider = $lists[ $type ]['class']::instance();

		// Direct type — provider has get_level_lists().
		if ( method_exists( $provider, 'get_level_lists' ) ) {
			$level_lists = $provider->get_level_lists();
		} elseif ( ! empty( $sub_type ) ) {
			// Grouped type — find sub-provider via sub-type lists.
			$level_lists  = array();
			$sub_type_map = $this->bb_get_sub_type_lists( $provider );

			if ( isset( $sub_type_map[ $sub_type ] ) && ! empty( $sub_type_map[ $sub_type ]['class'] ) && class_exists( $sub_type_map[ $sub_type ]['class'] ) ) {
				$sub_provider = $sub_type_map[ $sub_type ]['class']::instance();
				if ( method_exists( $sub_provider, 'get_level_lists' ) ) {
					$level_lists = $sub_provider->get_level_lists();
				}
			}
		} else {
			return $options;
		}

		if ( ! empty( $level_lists ) && is_array( $level_lists ) ) {
			foreach ( $level_lists as $level ) {
				// Skip default-checked/disabled items (e.g. administrators).
				if ( ! empty( $level['default'] ) ) {
					continue;
				}
				$options[] = array(
					'label' => isset( $level['text'] ) ? $level['text'] : '',
					'value' => isset( $level['id'] ) ? (string) $level['id'] : '',
				);
			}
		}

		return $options;
	}

	/**
	 * Get sub-types data for a grouped access-control provider.
	 *
	 * Returns the sub-type items, placeholder, action, and save key
	 * so the React component can render the second dropdown.
	 *
	 * @since 3.0.0
	 *
	 * @param object $provider The access-control provider instance.
	 *
	 * @return array Sub-type config or empty array if not a grouped type.
	 */
	private function bb_get_sub_types_for_type( $provider ) {

		$sub_type_map = $this->bb_get_sub_type_lists( $provider );

		if ( empty( $sub_type_map ) ) {
			return array();
		}

		$items = array();
		foreach ( $sub_type_map as $sub_key => $sub_info ) {
			$items[] = array(
				'label'    => isset( $sub_info['label'] ) ? $sub_info['label'] : '',
				'value'    => $sub_key,
				'disabled' => isset( $sub_info['is_enabled'] ) ? ! $sub_info['is_enabled'] : false,
			);
		}

		// Determine the save key and placeholder based on which method was found.
		if ( method_exists( $provider, 'bb_get_access_control_plugins_lists' ) ) {
			$key         = 'plugin-access-control-type';
			$placeholder = __( 'Select Membership Type', 'buddyboss-pro' );
			$action      = 'plugin_get_access_control_level_options';
		} elseif ( method_exists( $provider, 'bb_get_access_control_gamipress_lists' ) ) {
			$key         = 'gamipress-access-control-type';
			$placeholder = __( 'Select GamiPress Type', 'buddyboss-pro' );
			$action      = 'gamipress_get_access_control_level_options';
		} else {
			return array();
		}

		return array(
			'key'         => $key,
			'placeholder' => $placeholder,
			'action'      => $action,
			'items'       => $items,
		);
	}

	/**
	 * Get the raw sub-type lists from a grouped provider.
	 *
	 * Checks for known sub-list methods on the provider instance.
	 *
	 * @since 3.0.0
	 *
	 * @param object $provider The access-control provider instance.
	 *
	 * @return array Raw sub-type list (key => array with label, class, is_enabled).
	 */
	private function bb_get_sub_type_lists( $provider ) {

		if ( method_exists( $provider, 'bb_get_access_control_plugins_lists' ) ) {
			return $provider->bb_get_access_control_plugins_lists();
		}

		if ( method_exists( $provider, 'bb_get_access_control_gamipress_lists' ) ) {
			return $provider->bb_get_access_control_gamipress_lists();
		}

		return array();
	}

	/**
	 * Enrich access_control field data at AJAX time for Settings 2.0.
	 *
	 * Hooked to `bb_admin_settings_format_field_data`. Adds the `access_control_data`
	 * key with types, options, saved value, and threaded props so the React component
	 * can render immediately.
	 *
	 * @since 3.0.0
	 *
	 * @param array  $field_data Formatted field data.
	 * @param array  $field      Original field registration data.
	 * @param string $feature_id Feature ID (e.g. 'activity', 'groups', 'members').
	 *
	 * @return array
	 */
	public function bb_enrich_access_control_field_data( $field_data, $field, $feature_id = '' ) {

		if ( 'access_control' !== ( $field_data['type'] ?? '' ) ) {
			return $field_data;
		}

		// Populate the access_control_data via the existing method.
		$field_data['access_control_data'] = $this->bb_populate_access_control_field_data(
			array(
				'types'              => array(),
				'current_type'       => '',
				'options'            => array(),
				'select_placeholder' => __( 'Select Role', 'buddyboss-pro' ),
			),
			$field_data['name'],
			$feature_id
		);

		// Pass through threaded mode props from registration to field data.
		if ( ! empty( $field['threaded'] ) ) {
			$field_data['threaded']           = true;
			$field_data['threaded_sub_label'] = $field['threaded_sub_label'] ?? '';
		}

		// Load saved value from db.
		$saved = bp_get_option( $field_data['name'], '' );
		if ( ! empty( $saved ) && is_array( $saved ) ) {
			$field_data['value'] = $saved;
		}

		return $field_data;
	}
}
