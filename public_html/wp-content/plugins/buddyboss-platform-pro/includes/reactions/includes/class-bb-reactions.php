<?php
/**
 * BuddyBoss Pro Reactions.
 *
 * @since   2.4.50
 * @package BuddyBossPro
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Setup the bp reaction class.
 *
 * @since 2.4.50
 */
class BB_Reactions {

	/**
	 * Class instance.
	 *
	 * @var $instance
	 */
	public static $instance;

	/**
	 * Unique ID for the reaction.
	 *
	 * @var string reaction.
	 *
	 * @since 2.4.50
	 */
	public $id = 'reactions';

	/**
	 * Old reaction mode value captured before save (same-request only).
	 *
	 * Used to detect mode changes without DB round-trips.
	 *
	 * @var string|null
	 * @since 3.0.0
	 */
	private static $old_reaction_mode = null;

	/**
	 * Reaction Constructor.
	 *
	 * @since 2.4.50
	 */
	public function __construct() {
		// Include the code.
		$this->includes();
		$this->setup_actions();

		// Instantiate the emotion picker class.
		BB_Reactions_Picker::instance();
	}

	/**
	 * Get the instance of the class.
	 *
	 * @return BB_Reactions
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			$class_name     = __CLASS__;
			self::$instance = new $class_name();
		}

		return self::$instance;
	}

	/**
	 * Setup actions for reaction.
	 *
	 * @since 2.4.50
	 */
	public function setup_actions() {
		add_action( 'bp_enqueue_scripts', array( $this, 'enqueue_script' ) );

		// @todo: Remove after 3 release.
		if ( ! function_exists( 'bb_register_feature' ) ) {
			add_action( 'bp_admin_enqueue_scripts', array( $this, 'enqueue_scripts_styles' ) );
			// Save settings.
			add_action( 'bp_admin_tab_setting_save', array( $this, 'bb_admin_reaction_setting_fields_save' ), 10, 1 );

			// Add Migration popup into footer.
			add_action( 'bp_admin_tab_form_html', array( $this, 'bb_reaction_migration_popup' ), 10, 2 );
		} else {
			// Settings 2.0 hooks.
			// Register Pro emotion fields on the canonical bb_after_register_features
			// hook (priority 15) — same point Pusher / Zoom / OneSignal / MEPR-LMS /
			// TutorLMS use. Previously this was on bp_init which fired AFTER the
			// bb_register_features lifecycle, putting Reactions out of sync with
			// every other Pro integration. Translations (the original reason cited
			// for bp_init) are loaded on plugins_loaded — well before bp_loaded /
			// bb_after_register_features — so they're available here.
			add_action( 'bb_after_register_features', array( $this, 'bb_register_pro_emotion_fields' ), 15 );
			add_action( 'bp_admin_enqueue_scripts', array( $this, 'enqueue_react_admin_scripts' ) );
			add_action( 'admin_footer', array( $this, 'bb_reaction_migration_popup_react' ) );

			// Lazy-register the per-field filter only when React loads the
			// reactions feature. Registering globally would fire this listener
			// for every field of every feature, wasting work on every Settings
			// 2.0 AJAX response. Using `before_get_feature` means the listener
			// is attached once per request, only if `feature_id === 'reactions'`.
			add_action(
				'bb_admin_settings_before_get_feature',
				array( $this, 'bb_register_reaction_field_data_filter' )
			);

			add_filter( 'pre_update_option_bb_reaction_mode', array( $this, 'bb_capture_old_reaction_mode' ), 10, 2 );
			add_action( 'bb_admin_save_feature_settings_after', array( $this, 'bb_admin_reaction_settings_save_react' ), 10, 3 );
			add_filter( 'bb_admin_save_feature_settings_response', array( $this, 'bb_add_migration_data_to_response' ), 10, 4 );
		}
	}

	/**
	 * Attach the field-data enrichment filter only when the reactions feature
	 * is being loaded by Settings 2.0 — avoids running the filter closure for
	 * every field of every unrelated feature on every admin AJAX request.
	 *
	 * @since 3.0.0
	 *
	 * @param string $feature_id The feature being loaded for AJAX response.
	 */
	public function bb_register_reaction_field_data_filter( $feature_id ) {
		if ( 'reactions' !== $feature_id ) {
			return;
		}

		if ( ! has_filter( 'bb_admin_settings_format_field_data', array( $this, 'bb_extend_reaction_field_data' ) ) ) {
			add_filter( 'bb_admin_settings_format_field_data', array( $this, 'bb_extend_reaction_field_data' ), 10, 2 );
		}
	}

	/**
	 * Enqueue admin related scripts and styles.
	 *
	 * @since 2.4.50
	 */
	public function enqueue_scripts_styles() {
		$current_tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if ( 'bp-reactions' === $current_tab ) {
			$min     = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
			$rtl_css = is_rtl() ? '-rtl' : '';

			wp_enqueue_style( 'bb-reactions-admin', bb_reaction_url( '/assets/css/bb-reactions-admin' . $rtl_css . $min . '.css' ), array(), bb_platform_pro()->version );
			wp_enqueue_script( 'bb-reaction-admin', bb_reaction_url( '/assets/js/admin/bb-reaction-admin' . $min . '.js' ), array(), bb_platform_pro()->version ); // phpcs:ignore
			wp_localize_script(
				'bb-reaction-admin',
				'bbReactionAdminVars',
				array(
					'ajax_url'         => esc_url( admin_url( 'admin-ajax.php' ) ),
					'wizard_label'     => __( 'Migration wizard', 'buddyboss-pro' ),
					'migration_status' => bb_pro_reaction_get_migration_status(),
					'nonce'            => array(
						'check_delete_emotion'       => wp_create_nonce( 'bb-pro-check-delete-emotion' ),
						'footer_migration'           => wp_create_nonce( 'bb-pro-check-footer-migration' ),
						'check_migration'            => wp_create_nonce( 'bb-pro-check-reaction-migration' ),
						'dismiss_migration_notice'   => wp_create_nonce( 'bb-pro-reaction-dismiss-migration-notice' ),
						'migration_start_conversion' => wp_create_nonce( 'bb-pro-reaction-migration-start-conversion' ),
						'migration_stop_conversion'  => wp_create_nonce( 'bb-pro-reaction-migration-stop-conversion' ),
						'migration_do_later'         => wp_create_nonce( 'bb-pro-reaction-migration-do-later' ),
					),
				)
			);
		}
	}

	/**
	 * Enqueue related scripts and styles.
	 *
	 * @since 2.4.50
	 */
	public function enqueue_script() {
		if ( ! bp_is_active( 'activity' ) ) {
			return;
		}

		$min = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		wp_enqueue_script( 'bb-reaction', bb_reaction_url( '/assets/js/bb-reaction' . $min . '.js' ), array(), bb_platform_pro()->version, true );
		wp_localize_script(
			'bb-reaction',
			'bbReactionVars',
			array(
				'ajax_url' => esc_url( admin_url( 'admin-ajax.php' ) ),
			)
		);
	}

	/**
	 * Includes files.
	 *
	 * @since 2.4.50
	 */
	public function includes() {

		$bb_platform_pro = bb_platform_pro();
		$slashed_path    = trailingslashit( $bb_platform_pro->reactions_dir );

		$includes = array(
			'cache',
			'functions',
			'filters',
			'actions',
		);

		// Loop through files to be included.
		foreach ( (array) $includes as $file ) {

			if ( empty( $this->bb_reaction_check_has_licence() ) ) {
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
				if ( is_file( $slashed_path . $path ) ) {
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
	 * @since 2.4.50
	 *
	 * @return mixed Return the default.
	 */
	protected function bb_reaction_check_has_licence( $has_access = true ) {

		if ( bb_pro_should_lock_features() ) {
			return false;
		}

		return $has_access;
	}

	/**
	 * Save Reaction emotions to DB.
	 *
	 * @since 2.4.50
	 *
	 * @param string $current_tab Current setting tab.
	 */
	public function bb_admin_reaction_setting_fields_save( $current_tab ) {

		if ( 'bp-reactions' !== $current_tab ) {
			return;
		}

		if ( ! check_admin_referer( $current_tab . '-options' ) ) {
			return;
		}

		$bb_reaction      = BB_Reaction::instance();
		$reaction_mode    = ! empty( $_POST['bb_reaction_mode'] ) ? sanitize_text_field( wp_unslash( $_POST['bb_reaction_mode'] ) ) : '';
		$migration_action = ! empty( $_POST['migration_action'] ) ? sanitize_text_field( wp_unslash( $_POST['migration_action'] ) ) : 'no';
		$action_status    = array( 'updated' => true );

		// Add and update reaction post and settings.
		if (
			'no' === $migration_action &&
			'emotions' === $reaction_mode &&
			class_exists( 'BB_Reaction' )
		) {
			$new_emotion_ids = array();
			if ( ! empty( $_POST['reaction_items'] ) ) {

				$reaction_items  = ! empty( $_POST['reaction_items'] ) && is_array( $_POST['reaction_items'] ) ? wp_unslash( $_POST['reaction_items'] ) : array(); // phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				$reaction_checks = ! empty( $_POST['reaction_checks'] ) && is_array( $_POST['reaction_checks'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['reaction_checks'] ) ) : array(); // phpcs:ignore WordPress.Security.NonceVerification.Missing

				$index = 1;
				foreach ( $reaction_items as $id => $reaction_item ) {
					$reaction_item = array_map( 'sanitize_text_field', json_decode( wp_unslash( $reaction_item ), true ) );

					$reaction_item['mode']       = 'emotions';
					$reaction_item['menu_order'] = $index++;

					// If reaction id exists, update the reaction otherwise create a new one.
					if ( ! empty( $reaction_item['id'] ) ) {
						$reaction_id                        = absint( $reaction_item['id'] );
						$reaction_item['is_emotion_active'] = ! empty( $reaction_checks[ $id ] );
						$bb_reaction->bb_update_reaction( $reaction_id, $reaction_item );
					} else {
						unset( $reaction_item['id'] );
						$reaction_item['is_emotion_active'] = true;

						$reaction_id = $bb_reaction->bb_add_reaction( $reaction_item );
					}

					$new_emotion_ids[] = $reaction_id;
				}
			}

			$all_emotions       = bb_pro_get_reactions( 'emotions', false );
			$all_emotion_ids    = ! empty( $all_emotions ) ? array_column( $all_emotions, 'id' ) : array();
			$all_emotion_names  = ! empty( $all_emotions ) ? array_column( array_filter( $all_emotions ), 'icon_text', 'id' ) : array();
			$remove_emotion_ids = array_diff( $all_emotion_ids, $new_emotion_ids );

			if ( ! empty( $remove_emotion_ids ) ) {

				$deleted_emotion_names = '';
				foreach ( $remove_emotion_ids as $reaction_id ) {
					$bb_reaction->bb_remove_reaction( $reaction_id );
					$deleted_emotion_names .= ! empty( $all_emotion_names[ $reaction_id ] ) ? $all_emotion_names[ $reaction_id ] . ', ' : '';
				}

				// Register a background job for delete the emotion data.
				bb_pro_reaction_dispatch_migration( $remove_emotion_ids, 'delete' );

				// Set emotion delete message.
				$action_status         = array( 'updated' => 'emotion_deleted' );
				$deleted_emotion_names = rtrim( $deleted_emotion_names, ', ' );
				if ( ! empty( $deleted_emotion_names ) ) {
					$message = sprintf(
					/* translators: Emotion names with comma separator. */
						__( 'The %s Emotion was successfully deleted.', 'buddyboss-pro' ),
						'<b>' . $deleted_emotion_names . '</b>'
					);
					set_transient( $action_status['updated'], $message );
				}
			}
		} elseif ( in_array( $migration_action, array( 'footer', 'switch' ), true ) ) {

			// Defined variables.
			$from_emotions  = array();
			$to_emotions    = 0;
			$migration_data = bb_pro_reaction_get_migration_action();
			$likes_id       = (int) $bb_reaction->bb_reactions_get_like_reaction_id();

			// Get posted values form pop-up.
			$checkbox_emotions = array();
			if ( isset( $_POST['from_all_emotions'] ) ) {
				$all_emotions      = bb_pro_get_reactions( 'emotions', false );
				$checkbox_emotions = ! empty( $all_emotions ) ? array_column( $all_emotions, 'id' ) : array();
			}

			if ( isset( $_POST['from_reactions'] ) ) {
				$from_reactions    = ! empty( $_POST['from_reactions'] ) && is_array( $_POST['from_reactions'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['from_reactions'] ) ) : array(); // phpcs:ignore WordPress.Security.NonceVerification.Missing
				$checkbox_emotions = array_merge( $checkbox_emotions, $from_reactions );
			}

			$select_emotions = (int) ( ! empty( $_POST['to_reactions'] ) ? sanitize_text_field( wp_unslash( $_POST['to_reactions'] ) ) : 0 );

			// Arrange values based on type of migration.
			if ( 'footer' === $migration_action ) {
				$reaction_mode = bb_get_reaction_mode();
				$from_emotions = $checkbox_emotions;

				$to_emotions = $select_emotions;
				if ( 'likes' === $reaction_mode ) {
					$to_emotions = $likes_id;
				}

				$migration_data = array(
					'action' => $reaction_mode,
					'type'   => 'footer',
				);

			} elseif ( 'switch' === $migration_action ) {
				if ( ! empty( $migration_data ) && 'like_to_emotions_action' === $migration_data['action'] ) {
					$from_emotions = array( $likes_id );
					$to_emotions   = $select_emotions;
				} elseif ( ! empty( $migration_data ) && 'emotions_to_like_action' === $migration_data['action'] ) {
					$from_emotions = $checkbox_emotions;
					$to_emotions   = $likes_id;
				}
			}

			if ( ! empty( $from_emotions ) && ! empty( $to_emotions ) ) {
				$from_emotions = array_filter( array_diff( $from_emotions, array( $to_emotions ) ) );

				if ( ! empty( $from_emotions ) ) {

					// Updated current migration status.
					$migration_data['from_emotions']   = $from_emotions;
					$migration_data['to_emotions']     = $to_emotions;
					$migration_data['status']          = 'running';
					$migration_data['total_reactions'] = bb_load_reaction()->bb_get_user_reactions_count(
						array(
							'reaction_id' => $from_emotions,
						)
					);

					if ( isset( $migration_data['total_reactions'] ) && 0 < $migration_data['total_reactions'] ) {
						if ( (int) $likes_id === (int) $to_emotions ) {
							$migration_data['from_emotions_name'] = __( 'reactions', 'buddyboss-pro' );
							$migration_data['to_emotions_name']   = __( 'Likes', 'buddyboss-pro' );
						} else {
							$migration_data['from_emotions_name'] = __( 'Likes', 'buddyboss-pro' );
							$migration_data['to_emotions_name']   = '';

							$all_emotions = bb_pro_get_reactions( 'emotions', false );
							$all_emotions = array_column( $all_emotions, 'icon_text', 'id' );
							if (
								! empty( $all_emotions ) &&
								! empty( $all_emotions[ $to_emotions ] )
							) {
								$migration_data['to_emotions_name'] = $all_emotions[ $to_emotions ];
							}
						}

						bb_pro_reaction_update_migration_action( $migration_data );

						// Show site-wide notice.
						bp_update_option( 'bb_pro_reaction_migration_notice', 'yes' );

						// Register a background job for migrate the emotion data.
						bb_pro_reaction_dispatch_migration( $from_emotions, $to_emotions );
					} else {
						// If no reaction found then delete the migration notice.
						bb_pro_reaction_delete_migration();
					}
				}
			}

			// No success message show.
			$action_status = array( 'updated' => 'no_message' );
		}

		bp_core_redirect( bp_core_admin_setting_url( $current_tab, $action_status ) );
	}

	/**
	 * Empty callback of blocks.
	 *
	 * @since 2.4.50
	 *
	 * @param string $tab_name Current tab name.
	 * @param mixed  $tab      Current tab object (unused).
	 */
	public function bb_reaction_migration_popup( $tab_name, $tab ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
		// Validate the current screen.
		if ( empty( $tab_name ) || 'bp-reactions' !== $tab_name ) {
			return;
		}
		?>
		<input type="hidden" name="migration_action" id="migration_action" value="no">
		<div id="bbpro_migration_wizard" class="bbpro-modal-box bbpro-modal-box_detached">
			<div class="media-modal-backdrop"></div>
			<div class="media-modal">
				<div class="media-modal-content">
					<div class="bbpro-modal-box__header">
						<h3 class="wizard-label"><?php echo esc_html__( 'Migration wizard', 'buddyboss-pro' ); ?></h3>
						<button type="button" id="bbpro_icon_modal_close" class="media-modal-close">
							<span class="media-modal-icon">
								<span class="screen-reader-text"><?php echo esc_html__( 'Close', 'buddyboss-pro' ); ?></span>
							</span>
						</button>
					</div>
					<div class="modal-content">
						<div class="bbpro-modal-box_loader">
							<span class="bb-icons bb-icon-spinner animate-spin"></span>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div id="bbpro_reaction_delete_confirmation" class="bbpro-modal-box bbpro_reaction_delete_confirmation bbpro-modal-box_detached">
			<div class="media-modal-backdrop"></div>
			<div class="media-modal">
				<div class="media-modal-content">
					<div class="bbpro-modal-box__header">
						<h3><?php echo esc_html__( 'Delete Emotion', 'buddyboss-pro' ); ?></h3>
						<button type="button" id="bbpro_icon_modal_close" class="media-modal-close">
							<span class="media-modal-icon">
								<span class="screen-reader-text"><?php echo esc_html__( 'Close', 'buddyboss-pro' ); ?></span>
							</span>
						</button>
					</div>
					<div class="bb-reaction-delete-modal-content">
						<div class="bb-reaction-delete-modal__content">
							<div class="bbpro-modal-box_loader">
								<span class="bb-icons bb-icon-spinner animate-spin"></span>
							</div>
						</div>
					</div>

				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Register Pro-specific emotion fields for Settings 2.0.
	 *
	 * @since 3.0.0
	 */
	public function bb_register_pro_emotion_fields() {
		// Settings 2.0 panels are admin-only — match the gate the other 5 Pro
		// integrations (mepr/onesignal/pusher/tutorlms/zoom) apply on this same
		// hook so frontend requests don't pay registration cost.
		// Note: bb_after_register_features:15 fires earlier than
		// bp_setup_current_user (bp_loaded:10) — do NOT call bp_loggedin_user_id()
		// from inside this method; it will return 0.
		if ( ! is_admin() && ! wp_doing_ajax() ) {
			return;
		}

		// Note: migration_data and migration_status are NOT fetched here to avoid
		// wasted DB reads at registration time. Fresh values are injected by
		// bb_extend_reaction_field_data() when the settings page is loaded.

		// Register migration warning notice field (shows when mode changed, migration needed).
		bb_register_feature_field(
			'reactions',
			'reactions',
			'reactions_settings',
			array(
				'name'             => 'bb_reaction_migration',
				'label'            => __( 'Migration Notice', 'buddyboss-pro' ), // Required, but won't be displayed.
				'type'             => 'reaction_migration',
				'description'      => '',
				'migration_data'   => array(),
				'migration_status' => '',
				'order'            => 5,
			)
		);

		// Register migration status notice field (shows progress/completion).
		bb_register_feature_field(
			'reactions',
			'reactions',
			'reactions_settings',
			array(
				'name'             => 'bb_reaction_notice',
				'label'            => __( 'Migration Status', 'buddyboss-pro' ), // Required, but won't be displayed.
				'type'             => 'reaction_notice',
				'description'      => '',
				'migration_status' => '',
				'migration_data'   => array(),
				'order'            => 6,
			)
		);

		// Register migration wizard info field.
		bb_register_feature_field(
			'reactions',
			'reactions',
			'reactions_settings',
			array(
				'name'        => 'bb_reactions_migration_notice',
				'label'       => __( 'Migration Info', 'buddyboss-pro' ),
				'type'        => 'reaction_info',
				'description' => __( 'When switching reactions mode, use our {link} to map existing reactions to the new options.', 'buddyboss-pro' ),
				'link'        => array(
					'url'  => esc_url( admin_url( 'admin.php?page=bp-tools&tab=bb-reactions-migration' ) ),
					'text' => __( 'migration wizard', 'buddyboss-pro' ),
				),
				'order'       => 30,
			)
		);
	}

	/**
	 * Render migration notice for React UI.
	 *
	 * @since 3.0.0
	 *
	 * @return string HTML output.
	 */
	public function bb_render_migration_notice() {
		ob_start();
		$html = bb_admin_setting_reaction_notice_field_callback( false );
		echo wp_kses_post( $html );
		return ob_get_clean();
	}

	/**
	 * Enqueue scripts and styles for Settings 2.0 React admin.
	 *
	 * @since 3.0.0
	 */
	public function enqueue_react_admin_scripts() {
		$screen = get_current_screen();

		// Only enqueue on Settings 2.0 page (bb-settings) and reactions feature.
		if ( ! $screen || 'buddyboss_page_bb-settings' !== $screen->id ) {
			return;
		}

		$page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( ! function_exists( 'bb_register_feature' ) && 'bb-settings' !== $page ) {
			return;
		}

		// Check if we're on reactions feature page.
		$tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( ! function_exists( 'bb_register_feature' ) && 'bp-reactions' !== $tab ) {
			return;
		}

		$min     = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
		$rtl_css = is_rtl() ? '-rtl' : '';

		wp_enqueue_style( 'bb-reactions-admin', bb_reaction_url( '/assets/css/bb-reactions-admin' . $rtl_css . $min . '.css' ), array(), bb_platform_pro()->version );
		wp_enqueue_script( 'bb-reaction-admin', bb_reaction_url( '/assets/js/admin/bb-reaction-admin' . $min . '.js' ), array(), bb_platform_pro()->version, true );
		wp_localize_script(
			'bb-reaction-admin',
			'bbReactionAdminVars',
			array(
				'ajax_url'         => esc_url( admin_url( 'admin-ajax.php' ) ),
				'is_settings_v2'   => true,
				'wizard_label'     => __( 'Migration wizard', 'buddyboss-pro' ),
				'migration_status' => bb_pro_reaction_get_migration_status(),
				'nonce'            => array(
					'check_delete_emotion'       => wp_create_nonce( 'bb-pro-check-delete-emotion' ),
					'footer_migration'           => wp_create_nonce( 'bb-pro-check-footer-migration' ),
					'check_migration'            => wp_create_nonce( 'bb-pro-check-reaction-migration' ),
					'dismiss_migration_notice'   => wp_create_nonce( 'bb-pro-reaction-dismiss-migration-notice' ),
					'migration_start_conversion' => wp_create_nonce( 'bb-pro-reaction-migration-start-conversion' ),
					'migration_stop_conversion'  => wp_create_nonce( 'bb-pro-reaction-migration-stop-conversion' ),
					'migration_do_later'         => wp_create_nonce( 'bb-pro-reaction-migration-do-later' ),
				),
			)
		);
	}

	/**
	 * Add migration popup modals to admin footer for Settings 2.0.
	 *
	 * @since 3.0.0
	 */
	public function bb_reaction_migration_popup_react() {
		$screen = get_current_screen();

		// Only show on Settings 2.0 page.
		if ( ! $screen || 'buddyboss_page_bb-settings' !== $screen->id ) {
			return;
		}

		$page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( ! function_exists( 'bb_register_feature' ) && 'bb-settings' !== $page ) {
			return;
		}

		// Reuse existing modal HTML (same as old system).
		$this->bb_reaction_migration_popup( 'bp-reactions', null );
	}

	/**
	 * Extend reaction field data with Pro-specific information.
	 *
	 * @since 3.0.0
	 *
	 * @param array $field_data Formatted field data.
	 * @param array $field      Original field data.
	 * @return array Modified field data.
	 */
	public function bb_extend_reaction_field_data( $field_data, $field ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
		// Extend reaction_mode field.
		if ( 'bb_reaction_mode' === $field_data['name'] ) {
			// Add Pro notice/badge if needed.
			if ( bb_pro_should_lock_features() ) {
				// Disable emotions option if no valid license.
				if ( isset( $field_data['options'] ) && is_array( $field_data['options'] ) ) {
					foreach ( $field_data['options'] as $key => $option ) {
						if ( 'emotions' === $option['value'] ) {
							$field_data['options'][ $key ]['disabled'] = true;
						}
					}
				}
			}

			// Add reactions data (emotions array) for React to use.
			$field_data['reactions'] = array(
				'emotions' => bb_pro_get_reactions( 'emotions', true ),
				'likes'    => bb_pro_get_reactions( 'likes', true ),
			);
		}

		// Extend reaction_button field.
		if ( 'bb_reactions_button' === $field_data['name'] ) {
			$button_settings           = function_exists( 'bb_get_reaction_button_settings' ) ? bb_get_reaction_button_settings() : array();
			$field_data['button_data'] = $button_settings;
		}

		// Extend reaction_migration field with fresh migration data.
		if ( 'bb_reaction_migration' === $field_data['name'] ) {
			$migration_data   = bb_pro_reaction_get_migration_action();
			$migration_status = bb_pro_reaction_get_migration_status();

			// Don't show pending notice if migration has already completed.
			// The 'status' field inside migration_data tracks completion independently
			// of bb_pro_reaction_migration_completed option.
			if ( ! empty( $migration_data['status'] ) && 'completed' === $migration_data['status'] ) {
				$migration_data = array();
			}

			// Check if the current user has dismissed the pending migration notice ("Do Later").
			// If so, clear migration_data so React won't show the pending notice.
			// This mirrors the check in bb_admin_setting_reaction_notice_field_callback().
			$hide_for = ( ! empty( $migration_data['hide_for'] ) && is_array( $migration_data['hide_for'] ) ) ? $migration_data['hide_for'] : array();
			if (
				! empty( $migration_data['action'] ) &&
				in_array( $migration_data['action'], array( 'like_to_emotions_action', 'emotions_to_like_action' ), true ) &&
				in_array( get_current_user_id(), $hide_for, true )
			) {
				// User has dismissed, clear migration data so React won't show the notice.
				$migration_data = array();
			}

			$field_data['migration_data']   = $migration_data;
			$field_data['migration_status'] = $migration_status;
		}

		// Extend reaction_notice field with fresh migration data.
		if ( 'bb_reaction_notice' === $field_data['name'] ) {
			$field_data['migration_data']   = bb_pro_reaction_get_migration_action();
			$field_data['migration_status'] = bb_pro_reaction_get_migration_status();
		}

		// Extend reaction_info field (migration wizard notice).
		if ( 'bb_reactions_migration_notice' === $field_data['name'] ) {
			// Ensure link data is passed correctly to React.
			if ( ! isset( $field_data['link'] ) || empty( $field_data['link'] ) ) {
				$field_data['link'] = array(
					'url'  => esc_url( admin_url( 'admin.php?page=bp-tools&tab=bb-reactions-migration' ) ),
					'text' => __( 'migration wizard', 'buddyboss-pro' ),
				);
			}
		}

		return $field_data;
	}

	/**
	 * Save handler for Settings 2.0 React admin.
	 *
	 * @since 3.0.0
	 *
	 * @param string $feature_id Feature ID.
	 * @param array  $settings   Settings array.
	 * @param array  $saved      Keys and values saved to options by the main handler.
	 */
	public function bb_admin_reaction_settings_save_react( $feature_id, $settings, $saved ) {
		// Only handle reactions feature.
		if ( 'reactions' !== $feature_id ) {
			return;
		}

		/**
		 * Fires before saving the reaction settings (Settings 2.0).
		 *
		 * @since 3.0.0
		 *
		 * @param string $feature_id Feature ID ('reactions').
		 */
		do_action( 'bb_reaction_before_setting_save', $feature_id );

		$bb_reaction      = BB_Reaction::instance();
		$reaction_mode    = ! empty( $settings['bb_reaction_mode'] ) ? sanitize_text_field( $settings['bb_reaction_mode'] ) : '';
		$migration_action = ! empty( $settings['migration_action'] ) ? sanitize_text_field( $settings['migration_action'] ) : 'no';

		// Check if reaction mode changed and create migration action if needed.
		$mode_just_changed = false;
		// Only check for mode change if bb_reaction_mode was actually saved (means it changed).
		if ( ! empty( $reaction_mode ) && 'no' === $migration_action && isset( $saved['bb_reaction_mode'] ) ) {

			// Try to get the old value captured by pre_update_option filter (same request only).
			$old_reaction_mode = self::$old_reaction_mode;

			if ( empty( $old_reaction_mode ) ) {
				// Fallback: check based on what reactions exist.
				$old_likes    = bb_pro_get_reactions();
				$old_emotions = bb_pro_get_reactions( 'emotions', false );

				if ( 'likes' === $reaction_mode && ! empty( $old_emotions ) ) {
					$old_reaction_mode = 'emotions';
				} elseif ( 'emotions' === $reaction_mode && ! empty( $old_likes ) && empty( $old_emotions ) ) {
					$old_reaction_mode = 'likes';
				} else {
					$old_reaction_mode = $reaction_mode; // Same, no change.
				}
			}
			// Clean up static property.
			self::$old_reaction_mode = null;

			if ( $old_reaction_mode !== $reaction_mode ) {
				$mode_just_changed = true;
				$current_action    = '';
				$current_from_ids  = '';

				// Always clear old migration data when mode changes.
				bb_pro_reaction_delete_migration();

				if ( 'likes' === $old_reaction_mode && 'emotions' === $reaction_mode ) {
					$current_action   = 'like_to_emotions_action';
					$old_likes        = bb_pro_get_reactions();
					$likes            = ! empty( $old_likes ) ? current( $old_likes ) : array();
					$current_from_ids = isset( $likes['id'] ) ? $likes['id'] : '';
				} elseif ( 'emotions' === $old_reaction_mode && 'likes' === $reaction_mode ) {
					$current_action   = 'emotions_to_like_action';
					$old_emotions     = bb_pro_get_reactions( 'emotions', false );
					$current_from_ids = implode( ',', array_filter( array_column( $old_emotions, 'id' ) ) );
				}

				// Only add new migration if there are reactions to migrate.
				$reaction_instance = bb_load_reaction();
				if ( ! empty( $current_action ) && ! empty( $current_from_ids ) && $reaction_instance ) {
					$reaction_count = $reaction_instance->bb_get_user_reactions_count(
						array( 'reaction_id' => $current_from_ids )
					);

					if ( 0 < $reaction_count ) {
						bb_pro_reaction_add_migration_action(
							array(
								'action'          => $current_action,
								'total_reactions' => $reaction_count,
							)
						);
					}
				}
			}
		}

		// Process emotion updates only when reaction_items were submitted.
		// Skip when mode was just changed or when no reaction_items are in the request
		// (e.g., user only toggled the mode radio) to prevent deletion of existing emotions.
		if (
			'no' === $migration_action &&
			'emotions' === $reaction_mode &&
			class_exists( 'BB_Reaction' ) &&
			! $mode_just_changed &&
			! empty( $settings['reaction_items'] )
		) {
			$new_emotion_ids = array();
			if ( ! empty( $settings['reaction_items'] ) ) {

				$reaction_items  = $settings['reaction_items'];
				$reaction_checks = ! empty( $settings['reaction_checks'] ) ? $settings['reaction_checks'] : array();

				$index = 1;
				foreach ( $reaction_items as $id => $reaction_item ) {
					// Handle JSON encoded data.
					if ( is_string( $reaction_item ) ) {
						$reaction_item = json_decode( wp_unslash( $reaction_item ), true );

						// Skip invalid JSON entries.
						if ( json_last_error() !== JSON_ERROR_NONE || ! is_array( $reaction_item ) ) {
							continue;
						}
					}

					// Ensure $reaction_item is an array before processing.
					if ( ! is_array( $reaction_item ) ) {
						continue;
					}

					$reaction_item = array_map( 'sanitize_text_field', $reaction_item );

					$reaction_item['mode']       = 'emotions';
					$reaction_item['menu_order'] = $index++;

					// React-generated IDs (e.g., react_key_123) or non-numeric IDs = new emotion. Otherwise, update existing.
					$reaction_id_raw = isset( $reaction_item['id'] ) ? $reaction_item['id'] : '';
					$is_react_key    = is_string( $reaction_id_raw ) && 0 === strpos( (string) $reaction_id_raw, 'react_key_' );
					$is_new_emotion  = ( '' === $reaction_id_raw || 0 === absint( $reaction_id_raw ) || $is_react_key );

					if ( ! $is_new_emotion ) {
						$reaction_id                        = absint( $reaction_item['id'] );
						$reaction_item['is_emotion_active'] = ! empty( $reaction_checks[ $id ] );
						$bb_reaction->bb_update_reaction( $reaction_id, $reaction_item );
					} else {
						unset( $reaction_item['id'] );
						$reaction_item['is_emotion_active'] = true;

						$reaction_id = $bb_reaction->bb_add_reaction( $reaction_item );
					}

					$new_emotion_ids[] = $reaction_id;
				}
			}

			$all_emotions       = bb_pro_get_reactions( 'emotions', false );
			$all_emotion_ids    = ! empty( $all_emotions ) ? array_column( $all_emotions, 'id' ) : array();
			$all_emotion_names  = ! empty( $all_emotions ) ? array_column( array_filter( $all_emotions ), 'icon_text', 'id' ) : array();
			$remove_emotion_ids = array_diff( $all_emotion_ids, $new_emotion_ids );

			if ( ! empty( $remove_emotion_ids ) ) {

				$deleted_emotion_names = '';
				foreach ( $remove_emotion_ids as $reaction_id ) {
					$bb_reaction->bb_remove_reaction( $reaction_id );
					$deleted_emotion_names .= ! empty( $all_emotion_names[ $reaction_id ] ) ? $all_emotion_names[ $reaction_id ] . ', ' : '';
				}

				// Register a background job for delete the emotion data.
				bb_pro_reaction_dispatch_migration( $remove_emotion_ids, 'delete' );
			}
		} elseif ( in_array( $migration_action, array( 'footer', 'switch' ), true ) ) {
			// Handle migration (reuse existing logic from old save handler).
			// This code handles migration wizard submissions.
			$this->bb_handle_migration_save( $settings, $reaction_mode, $migration_action, $bb_reaction );
		}

		// Note: bb_reactions_button is saved by the Platform core AJAX handler via
		// bb_reactions_sanitize_button_settings() sanitize_callback. No duplicate save needed.
	}

	/**
	 * Handle migration save logic (extracted for reuse).
	 *
	 * @since 3.0.0
	 *
	 * @param array  $settings         Posted settings.
	 * @param string $reaction_mode    Current reaction mode.
	 * @param string $migration_action Migration action type.
	 * @param object $bb_reaction      BB_Reaction instance.
	 */
	private function bb_handle_migration_save( $settings, $reaction_mode, $migration_action, $bb_reaction ) {
		// Defined variables.
		$from_emotions  = array();
		$to_emotions    = 0;
		$migration_data = bb_pro_reaction_get_migration_action();
		$likes_id       = (int) $bb_reaction->bb_reactions_get_like_reaction_id();

		// Get posted values from settings.
		$checkbox_emotions = array();
		if ( isset( $settings['from_all_emotions'] ) ) {
			$all_emotions      = bb_pro_get_reactions( 'emotions', false );
			$checkbox_emotions = ! empty( $all_emotions ) ? array_column( $all_emotions, 'id' ) : array();
		}

		if ( isset( $settings['from_reactions'] ) ) {
			$from_reactions    = ! empty( $settings['from_reactions'] ) ? array_map( 'sanitize_text_field', $settings['from_reactions'] ) : array();
			$checkbox_emotions = array_merge( $checkbox_emotions, $from_reactions );
		}

		$select_emotions = (int) ( ! empty( $settings['to_reactions'] ) ? sanitize_text_field( $settings['to_reactions'] ) : 0 );

		// Arrange values based on type of migration.
		if ( 'footer' === $migration_action ) {
			$reaction_mode = bb_get_reaction_mode();
			$from_emotions = $checkbox_emotions;

			$to_emotions = $select_emotions;
			if ( 'likes' === $reaction_mode ) {
				$to_emotions = $likes_id;
			}

			$migration_data = array(
				'action' => $reaction_mode,
				'type'   => 'footer',
			);

		} elseif ( 'switch' === $migration_action ) {
			if ( ! empty( $migration_data ) && 'like_to_emotions_action' === $migration_data['action'] ) {
				$from_emotions = array( $likes_id );
				$to_emotions   = $select_emotions;
			} elseif ( ! empty( $migration_data ) && 'emotions_to_like_action' === $migration_data['action'] ) {
				$from_emotions = $checkbox_emotions;
				$to_emotions   = $likes_id;
			}
		}

		if ( ! empty( $from_emotions ) && ! empty( $to_emotions ) ) {
			$from_emotions     = array_filter( array_diff( $from_emotions, array( $to_emotions ) ) );
			$reaction_instance = bb_load_reaction();

			if ( ! empty( $from_emotions ) && $reaction_instance ) {

				// Updated current migration status.
				$migration_data['from_emotions']   = $from_emotions;
				$migration_data['to_emotions']     = $to_emotions;
				$migration_data['status']          = 'running';
				$migration_data['total_reactions'] = $reaction_instance->bb_get_user_reactions_count(
					array(
						'reaction_id' => $from_emotions,
					)
				);

				if ( isset( $migration_data['total_reactions'] ) && 0 < $migration_data['total_reactions'] ) {
					if ( (int) $likes_id === (int) $to_emotions ) {
						$migration_data['from_emotions_name'] = __( 'reactions', 'buddyboss-pro' );
						$migration_data['to_emotions_name']   = __( 'Likes', 'buddyboss-pro' );
					} else {
						$migration_data['from_emotions_name'] = __( 'Likes', 'buddyboss-pro' );
						$migration_data['to_emotions_name']   = '';

						$all_emotions = bb_pro_get_reactions( 'emotions', false );
						$all_emotions = array_column( $all_emotions, 'icon_text', 'id' );
						if (
							! empty( $all_emotions ) &&
							! empty( $all_emotions[ $to_emotions ] )
						) {
							$migration_data['to_emotions_name'] = $all_emotions[ $to_emotions ];
						}
					}

					bb_pro_reaction_update_migration_action( $migration_data );

					// Show site-wide notice.
					bp_update_option( 'bb_pro_reaction_migration_notice', 'yes' );

					// Register a background job for migrate the emotion data.
					bb_pro_reaction_dispatch_migration( $from_emotions, $to_emotions );
				} else {
					// If no reaction found then delete the migration notice.
					bb_pro_reaction_delete_migration();
				}
			}
		}
	}

	/**
	 * Capture the old reaction mode value before it's updated.
	 *
	 * @since 3.0.0
	 *
	 * @param mixed $value     The new option value.
	 * @param mixed $old_value The old option value.
	 * @return mixed The new value (unchanged).
	 */
	public function bb_capture_old_reaction_mode( $value, $old_value ) {
		self::$old_reaction_mode = $old_value;
		return $value;
	}

	/**
	 * Add migration data to the save feature settings response.
	 *
	 * @since 3.0.0
	 *
	 * @param array  $response_data Response data to be sent.
	 * @param string $feature_id    Feature ID.
	 * @param array  $settings      Full submitted settings.
	 * @param array  $saved         Keys and values saved to options.
	 * @return array Modified response data.
	 */
	public function bb_add_migration_data_to_response( $response_data, $feature_id, $settings, $saved ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
		// Only add for reactions feature.
		if ( 'reactions' !== $feature_id ) {
			return $response_data;
		}

		// Get current migration data and status.
		$migration_data   = bb_pro_reaction_get_migration_action();
		$migration_status = bb_pro_reaction_get_migration_status();

		// Always include migration data and status in response so frontend can update/clear notice.
		$response_data['migration_data']   = ! empty( $migration_data ) ? $migration_data : array();
		$response_data['migration_status'] = $migration_status;

		return $response_data;
	}
}
