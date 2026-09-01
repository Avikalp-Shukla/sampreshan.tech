<?php
/**
 * Reaction Action.
 *
 * @since   2.4.50
 * @package BuddyBossPro
 */

// Actions.
add_action( 'bbp_pro_core_install', 'bb_pro_reaction_migration' );

// Load reaction background job class.
add_action( 'bp_init', 'bb_pro_init_reactions_background_process', 50 );
add_action( 'bb_pro_init_reactions_background_process', 'bb_pro_schedule_reactions_background_process' );

// Check the migration before save the settings.
add_action( 'bb_reaction_before_setting_save', 'bb_pro_reaction_check_data_before_save', 10, 1 );

// Register Ajax requests.
// Checks total count as per actions.
add_action( 'wp_ajax_bb_pro_reaction_footer_migration', 'bb_pro_reaction_footer_migration' );
// Check the data previously submitted when delete the emotion.
add_action( 'wp_ajax_bb_pro_reaction_check_delete_emotion', 'bb_pro_reaction_check_delete_emotion' );
// Do later migration.
add_action( 'wp_ajax_bb_pro_reaction_migration_start_conversion', 'bb_pro_reaction_migration_start_conversion' );
// Do later migration.
add_action( 'wp_ajax_bb_pro_reaction_migration_do_later', 'bb_pro_reaction_migration_do_later' );
// Dismiss site-wide notice.
add_action( 'wp_ajax_bb_pro_reaction_dismiss_migration_notice', 'bb_pro_reaction_dismiss_migration_notice' );
// Stop Migration from notice.
add_action( 'wp_ajax_bb_pro_reaction_migration_stop_conversion', 'bb_pro_reaction_migration_stop_conversion' );
// Check migration status (for React auto-refresh).
add_action( 'wp_ajax_bb_pro_reaction_check_migration', 'bb_pro_reaction_check_migration' );

// Add site-wide notice.
add_action( 'admin_notices', 'bb_pro_reaction_show_global_notice' );

// Clean up stale temp options left by failed reaction mode saves.
add_action( 'bb_pro_init_reactions_background_process', 'bb_pro_reaction_cleanup_stale_temp_options' );

/**
 * Return to add default reaction data.
 *
 * @since 2.4.50
 *
 * @return void
 */
function bb_pro_reaction_migration() {
	$all_emotions = bb_pro_get_reactions( 'emotions', false );

	if ( empty( $all_emotions ) ) {
		$reactions = bb_pro_get_reaction_default_data();

		if ( class_exists( 'BB_Reaction' ) ) {
			$bb_reaction = BB_Reaction::instance();
			foreach ( $reactions as $reaction ) {
				$bb_reaction->bb_add_reaction( $reaction );
			}

			$bb_reaction->bb_update_reactions_transient();
		}
	}
}

/**
 * Load reaction background class.
 *
 * @since 2.4.50
 *
 * @return void
 */
function bb_pro_init_reactions_background_process() {
	global $bb_reaction_background_process;

	if ( ! class_exists( 'BB_Reactions_Background_Process' ) ) {
		include_once bb_reaction_path( 'includes/class-bb-reactions-background-process.php' );
	}

	if ( class_exists( 'BB_Reactions_Background_Process' ) ) {
		$bb_reaction_background_process = new BB_Reactions_Background_Process();

		/**
		 * Fires inside the 'bb_pro_init_reactions_background_process' function, where BB updates data.
		 *
		 * @since 2.4.50
		 */
		do_action( 'bb_pro_init_reactions_background_process' );
	}
}

/**
 * Check and reschedule the newly added reactions background process if the queue is not empty.
 *
 * @since 2.4.50
 */
function bb_pro_schedule_reactions_background_process() {
	global $bb_reaction_background_process;

	if (
		is_object( $bb_reaction_background_process ) &&
		$bb_reaction_background_process->is_updating()
	) {
		$bb_reaction_background_process->schedule_event();
	}
}

/**
 * Validate migration before save the settings.
 *
 * @since 2.4.50
 *
 * @param string $current_tab Current tab slug.
 */
function bb_pro_reaction_check_data_before_save( $current_tab ) {
	if (
		'bp-reactions' !== $current_tab &&
		! (
			function_exists( 'bb_register_feature' ) &&
			'reactions' === $current_tab
		)
	) {
		return;
	}

	$is_settings_2 = function_exists( 'bb_register_feature' ) && 'reactions' === $current_tab;

	if ( 'inprogress' === bb_pro_reaction_get_migration_status() ) {
		// For Settings 1.0, redirect. For Settings 2.0, the AJAX handler will check this separately.
		if ( ! $is_settings_2 ) {
			bp_core_redirect( bp_core_admin_setting_url( $current_tab ) );
		}
		return;
	}

	// New values.
	// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified by the settings framework before this hook fires.
	$new_reaction_mode = ! empty( $_POST['bb_reaction_mode'] ) ? sanitize_text_field( wp_unslash( $_POST['bb_reaction_mode'] ) ) : '';

	if ( ! empty( $new_reaction_mode ) ) {

		// Old values.
		$old_reaction_mode = bb_get_reaction_mode();

		$current_action   = '';
		$current_from_ids = '';

		if ( 'likes' === $old_reaction_mode && 'emotions' === $new_reaction_mode ) {
			$current_action   = 'like_to_emotions_action';
			$old_likes        = bb_pro_get_reactions();
			$likes            = ! empty( $old_likes ) ? current( $old_likes ) : array();
			$current_from_ids = isset( $likes['id'] ) ? $likes['id'] : '';
		} elseif ( 'emotions' === $old_reaction_mode && 'likes' === $new_reaction_mode ) {
			$current_action   = 'emotions_to_like_action';
			$old_emotions     = bb_pro_get_reactions( 'emotions', false );
			$current_from_ids = implode( ',', array_filter( array_column( $old_emotions, 'id' ) ) );
		}

		if ( ! empty( $current_action ) && ! empty( $current_from_ids ) ) {

			// Reset migration.
			bb_pro_reaction_delete_migration();

			$reaction_instance = bb_load_reaction();
			if ( $reaction_instance ) {
				$reaction_count = $reaction_instance->bb_get_user_reactions_count(
					array(
						'reaction_id' => $current_from_ids,
					)
				);

				if ( 0 < $reaction_count ) {
					// Set migration action.
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
}

/**
 * Check migration is available while update the settings.
 *
 * @since 2.4.50
 */
function bb_pro_reaction_footer_migration() {
	if ( ! bp_current_user_can( 'bp_moderate' ) ) {
		wp_send_json_error(
			array(
				'content' => '',
				'message' => esc_html__( 'You do not have permission to perform this action.', 'buddyboss-pro' ),
			)
		);
	}

	$nonce = isset( $_REQUEST['nonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['nonce'] ) ) : '';
	if ( ! wp_verify_nonce( $nonce, 'bb-pro-check-footer-migration' ) ) {
		wp_send_json_error(
			array(
				'content' => '',
				'message' => esc_html__( 'Unable to submit this form, please refresh and try again.', 'buddyboss-pro' ),
			)
		);
	}

	if ( 'inprogress' === bb_pro_reaction_get_migration_status() ) {
		wp_send_json_error(
			array(
				'content' => '',
				'message' => esc_html__( 'This action can not be proceed while the conversion is in progress.', 'buddyboss-pro' ),
			)
		);
	}

	$content         = '';
	$wizard_sections = bb_pro_reaction_get_migration_wizard(
		array(
			'action_type' => 'footer_wizard',
		)
	);

	if ( ! empty( $wizard_sections['wizard_screen1'] ) ) {
		ob_start();
		?>
		<div class="bbpro_migration_wizard_screens bbpro_migration_wizard_1 active">
			<div class="bbpro-modal-box__body">
				<?php echo wp_kses( $wizard_sections['wizard_screen1'], bb_pro_reaction_wizard_allowed_tags() ); ?>
			</div>
			<div class="bbpro-modal-box__footer">
				<button type="button" class="button-secondary cancel_migration_wizard"><?php esc_html_e( 'Cancel', 'buddyboss-pro' ); ?></button>
				<button type="button" class="button-primary footer_next_wizard_screen disabled"><?php esc_html_e( 'Continue', 'buddyboss-pro' ); ?></button>
			</div>
		</div>
		<?php
		$content .= ob_get_clean();
	}

	if ( ! empty( $wizard_sections['wizard_screen2'] ) ) {
		ob_start();
		?>
		<div class="bbpro_migration_wizard_screens bbpro_migration_wizard_2">
			<div class="bbpro-modal-box__body">
				<?php echo wp_kses( $wizard_sections['wizard_screen2'], bb_pro_reaction_wizard_allowed_tags() ); ?>
			</div>
			<div class="bbpro-modal-box__footer">
				<button type="button" class="button-secondary cancel_migration_wizard"><?php esc_html_e( 'Cancel', 'buddyboss-pro' ); ?></button>
				<button type="button" class="button-primary start_migration_wizard"><?php esc_html_e( 'Start conversion', 'buddyboss-pro' ); ?></button>
			</div>
		</div>
		<?php
		$content .= ob_get_clean();
	}

	wp_send_json_success(
		array(
			'content' => $content,
			'message' => '',
		)
	);
}

/**
 * Check the emotion data exists while deleting it.
 *
 * @since 2.4.50
 */
function bb_pro_reaction_check_delete_emotion() {
	if ( ! bp_current_user_can( 'bp_moderate' ) ) {
		wp_send_json_error(
			array(
				'status'  => 'error',
				'message' => esc_html__( 'You do not have permission to perform this action.', 'buddyboss-pro' ),
			)
		);
	}

	$nonce = isset( $_REQUEST['nonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['nonce'] ) ) : '';
	if ( ! wp_verify_nonce( $nonce, 'bb-pro-check-delete-emotion' ) ) {
		wp_send_json_error(
			array(
				'status'  => 'error',
				'message' => __( 'Unable to processed this request, please refresh and try again.', 'buddyboss-pro' ),
			)
		);
	}

	if ( 'inprogress' === bb_pro_reaction_get_migration_status() ) {
		wp_send_json_error(
			array(
				'content' => '',
				'message' => esc_html__( 'This action can not be proceed while the conversion is in progress.', 'buddyboss-pro' ),
			)
		);
	}

	$emotion_id = ! empty( $_POST['emotion_id'] ) ? absint( $_POST['emotion_id'] ) : 0;

	if ( empty( $emotion_id ) ) {
		wp_send_json_error(
			array(
				'status'  => 'error',
				'message' => __( 'Emotion ID is required.', 'buddyboss-pro' ),
			)
		);
	}

	$emotion  = array();
	$emotions = bb_pro_get_reactions( 'emotions', false );
	if ( ! empty( $emotions ) ) {
		foreach ( $emotions as $item ) {

			if ( (int) $emotion_id === (int) $item['id'] ) {
				$emotion = $item;
				break;
			}
		}
	}

	if ( empty( $emotion ) ) {
		wp_send_json_error(
			array(
				'status'  => 'error',
				'message' => __( 'Provided emotion ID is not correct. please refresh and try again.', 'buddyboss-pro' ),
			)
		);
	}

	$reaction_instance = bb_load_reaction();
	if ( ! $reaction_instance ) {
		wp_send_json_error(
			array(
				'status'  => 'error',
				'message' => esc_html__( 'Reactions not available.', 'buddyboss-pro' ),
			)
		);
	}

	$emotion_count = $reaction_instance->bb_get_user_reactions_count(
		array(
			'reaction_id' => $emotion_id,
		)
	);

	if ( 0 < $emotion_count ) {
		$modal_content = sprintf(
			'<p>%s</p>',
			sprintf(
			/* translators: 1: Emotion name, 2: Emotion count. */
				__( 'You are about to delete the %1$s Emotion, including all %2$s instances of members using this emotion as a reaction.', 'buddyboss-pro' ),
				'<b>' . esc_html( $emotion['icon_text'] ) . '</b>',
				'<b>' . bp_core_number_format( $emotion_count ) . '</b>'
			)
		);

		$modal_content .= sprintf(
			'<p>%s</p>',
			__( 'If you want to retain this data, you can:', 'buddyboss-pro' )
		);

		$modal_content .= sprintf(
			'<ul><li>%1$s</li><li>%2$s</li></ul>',
			__( 'Edit or deactivate this Emotion instead of deleting it', 'buddyboss-pro' ),
			__( 'Use the migration wizard to convert this Emotion’s data to a different Emotion before deleting', 'buddyboss-pro' )
		);

		$modal_content .= sprintf(
			'<p>%s</p>',
			__( 'Otherwise, click the button below to proceed with deleting.', 'buddyboss-pro' )
		);

		$modal_content .= sprintf(
			'<p>%s</p>',
			sprintf(
				/* translators: %s: "cannot" in bold HTML. */
				__( 'This action %s be undone.', 'buddyboss-pro' ),
				sprintf(
					'<b>%s</b>',
					__( 'cannot', 'buddyboss-pro' )
				)
			)
		);
	} else {
		$modal_content = sprintf(
			'<p>%s</p>',
			sprintf(
			/* translators: 1: Emotion name. */
				__( 'Are you sure you want to delete the %s Emotion?', 'buddyboss-pro' ),
				'<b>' . esc_html( $emotion['icon_text'] ) . '</b>'
			)
		);

		$modal_content .= sprintf(
			'<p>%s</p>',
			sprintf(
				/* translators: %s: "cannot" in bold HTML. */
				__( 'This action %s be undone.', 'buddyboss-pro' ),
				sprintf(
					'<b>%s</b>',
					__( 'cannot', 'buddyboss-pro' )
				)
			)
		);
	}

	$modal_content = sprintf( '<div class="bbpro-modal-box__body">%s</div>', $modal_content );

	$modal_content .= sprintf(
		'<div class="bbpro-modal-box__footer">
					<button class="button-secondary bb-pro-reaction-cancel-delete-emotion">%1$s</button>
					<button class="button-primary bb-pro-reaction-delete-emotion">%2$s</button>
				</div>',
		__( 'Cancel', 'buddyboss-pro' ),
		__( 'Confirm', 'buddyboss-pro' )
	);

	wp_send_json_success(
		array(
			'status'  => true,
			'content' => $modal_content,
		)
	);
}

/**
 * Build a pop-up for migration.
 *
 * @since 2.4.50
 */
function bb_pro_reaction_migration_start_conversion() {
	if ( ! bp_current_user_can( 'bp_moderate' ) ) {
		wp_send_json_error(
			array(
				'content' => '',
				'message' => esc_html__( 'You do not have permission to perform this action.', 'buddyboss-pro' ),
			)
		);
	}

	$nonce = isset( $_REQUEST['nonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['nonce'] ) ) : '';
	if ( ! wp_verify_nonce( $nonce, 'bb-pro-reaction-migration-start-conversion' ) ) {
		wp_send_json_error(
			array(
				'content' => '',
				'message' => esc_html__( 'Unable to submit this form, please refresh and try again.', 'buddyboss-pro' ),
			)
		);
	}

	if ( 'inprogress' === bb_pro_reaction_get_migration_status() ) {
		wp_send_json_error(
			array(
				'content' => '',
				'message' => esc_html__( 'This action can not be proceed while the conversion is in progress.', 'buddyboss-pro' ),
			)
		);
	}

	$is_exists = bb_pro_reaction_get_migration_action();
	if ( empty( $is_exists ) ) {
		wp_send_json_error(
			array(
				'content' => '',
				'message' => __( 'Unable to find any existing data for migration.', 'buddyboss-pro' ),
			)
		);
	}

	if ( 'like_to_emotions_action' === $is_exists['action'] ) {
		$reaction_mode    = 'likes';
		$wizard_label     = __( 'Convert Likes', 'buddyboss-pro' );
		$old_likes        = bb_pro_get_reactions();
		$likes            = ! empty( $old_likes ) ? current( $old_likes ) : array();
		$current_from_ids = $likes['id'];
	} else {
		$reaction_mode    = 'emotions';
		$wizard_label     = __( 'Convert Reactions', 'buddyboss-pro' );
		$old_emotions     = bb_pro_get_reactions( 'emotions', false );
		$current_from_ids = implode( ',', array_filter( array_column( $old_emotions, 'id' ) ) );
	}

	$content             = '';
	$is_notice_dismissed = false;
	$reaction_count      = 0;
	if ( ! empty( $current_from_ids ) ) {
		$reaction_instance = bb_load_reaction();
		if ( $reaction_instance ) {
			$reaction_count = $reaction_instance->bb_get_user_reactions_count(
				array(
					'reaction_id' => $current_from_ids,
				)
			);
		}

		if ( 0 < $reaction_count ) {
			$wizard_sections = bb_pro_reaction_get_migration_wizard(
				array(
					'action_type'   => 'switch_wizard',
					'reaction_mode' => $reaction_mode,
				)
			);

			// Check if count mismatch then update it.
			if ( (int) $is_exists['total_reactions'] !== $reaction_count ) {
				$is_exists['total_reactions'] = $reaction_count;
				bb_pro_reaction_update_migration_action( $is_exists );
			}
		} else {
			// No migration data found.
			$wizard_screen1 = bb_pro_reaction_get_no_data_screen( bb_get_reaction_mode() );

			$wizard_sections = array(
				'wizard_screen1' => $wizard_screen1,
				'wizard_screen2' => '',
			);

			// Reset migration.
			bb_pro_reaction_delete_migration();

			// Dismissed the existing notice.
			$is_notice_dismissed = true;
		}
	}

	if ( ! empty( $wizard_sections['wizard_screen1'] ) ) {
		ob_start();
		?>
		<div class="bbpro_migration_wizard_screens bbpro_migration_wizard_1 active">
			<div class="bbpro-modal-box__body">
				<?php echo wp_kses( $wizard_sections['wizard_screen1'], bb_pro_reaction_wizard_allowed_tags() ); ?>
			</div>
			<div class="bbpro-modal-box__footer">
				<button type="button" class="button-secondary cancel_migration_wizard"><?php esc_html_e( 'Cancel', 'buddyboss-pro' ); ?></button>
				<button type="button" class="button-primary footer_next_wizard_screen disabled"><?php esc_html_e( 'Continue', 'buddyboss-pro' ); ?></button>
			</div>
		</div>
		<?php
		$content .= ob_get_clean();
	}

	if ( ! empty( $wizard_sections['wizard_screen2'] ) ) {
		ob_start();
		?>
		<div class="bbpro_migration_wizard_screens bbpro_migration_wizard_2">
			<div class="bbpro-modal-box__body">
				<?php echo wp_kses( $wizard_sections['wizard_screen2'], bb_pro_reaction_wizard_allowed_tags() ); ?>
			</div>
			<div class="bbpro-modal-box__footer">
				<button type="button" class="button-secondary cancel_migration_wizard"><?php esc_html_e( 'Cancel', 'buddyboss-pro' ); ?></button>
				<button type="button" class="button-primary start_migration_wizard"><?php esc_html_e( 'Start conversion', 'buddyboss-pro' ); ?></button>
			</div>
		</div>
		<?php
		$content .= ob_get_clean();
	}

	wp_send_json_success(
		array(
			'content'             => $content,
			'label'               => $wizard_label,
			'total_reactions'     => (int) $reaction_count,
			'is_notice_dismissed' => (bool) $is_notice_dismissed,
		)
	);
}

/**
 * Dismiss migration notice for current user (do later).
 *
 * @since 2.4.50
 */
function bb_pro_reaction_migration_do_later() {
	if ( ! bp_current_user_can( 'bp_moderate' ) ) {
		wp_send_json_error(
			array(
				'status'  => 'error',
				'message' => esc_html__( 'You do not have permission to perform this action.', 'buddyboss-pro' ),
			)
		);
	}

	$nonce = isset( $_REQUEST['nonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['nonce'] ) ) : '';
	if ( ! wp_verify_nonce( $nonce, 'bb-pro-reaction-migration-do-later' ) ) {
		wp_send_json_error(
			array(
				'status'  => 'error',
				'message' => __( 'Unable to processed this request, please refresh and try again.', 'buddyboss-pro' ),
			)
		);
	}

	bb_pro_reaction_update_migration_action(
		array(
			'hide_for' => array( get_current_user_id() ),
		)
	);

	wp_send_json_success(
		array(
			'status' => true,
		)
	);
}

/**
 * Dismiss the site-wide migration notice.
 *
 * @since 2.4.50
 */
function bb_pro_reaction_dismiss_migration_notice() {
	if ( ! bp_current_user_can( 'bp_moderate' ) ) {
		wp_send_json_error(
			array(
				'status'  => 'error',
				'message' => esc_html__( 'You do not have permission to perform this action.', 'buddyboss-pro' ),
			)
		);
	}

	$nonce = isset( $_REQUEST['nonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['nonce'] ) ) : '';
	if ( ! wp_verify_nonce( $nonce, 'bb-pro-reaction-dismiss-migration-notice' ) ) {
		wp_send_json_error(
			array(
				'status'  => 'error',
				'message' => esc_html__( 'Unable dismiss notice, please refresh and try again.', 'buddyboss-pro' ),
			)
		);
	}

	bp_delete_option( 'bb_pro_reaction_migration_notice' );
	// Also delete completed status so notice doesn't reappear on reload.
	bp_delete_option( 'bb_pro_reaction_migration_completed' );
	// Delete the migration action data so the pending notice doesn't reappear
	// after the completed status expires or is dismissed.
	bb_pro_reaction_delete_migration();

	wp_send_json_success(
		array(
			'status' => 'success',
		)
	);
}

/**
 * Ajax to stop the migration.
 *
 * @since 2.4.50
 */
function bb_pro_reaction_migration_stop_conversion() {
	if ( ! bp_current_user_can( 'bp_moderate' ) ) {
		wp_send_json_error(
			array(
				'message' => esc_html__( 'You do not have permission to perform this action.', 'buddyboss-pro' ),
			)
		);
	}

	$nonce = isset( $_REQUEST['nonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['nonce'] ) ) : '';
	if ( ! wp_verify_nonce( $nonce, 'bb-pro-reaction-migration-stop-conversion' ) ) {
		wp_send_json_error(
			array(
				'message' => esc_html__( 'Unable to stop migration, please refresh and try again.', 'buddyboss-pro' ),
			)
		);
	}
	global $bb_reaction_background_process;

	if ( $bb_reaction_background_process->is_processing() ) {
		$bb_reaction_background_process->pause();
	}

	if ( $bb_reaction_background_process->is_active() ) {
		$bb_reaction_background_process->kill_process();
		$bb_reaction_background_process->cancel_process();

		// Delete migration data (also clears bb_pro_reaction_migration and bb_pro_reaction_migration_completed).
		bb_pro_reaction_delete_migration();

		// Delete migration notice.
		bp_delete_option( 'bb_pro_reaction_migration_notice' );

		// Delete option for running.
		delete_option( 'is_reaction_migration' );

		// Reset only the reactions cache group instead of flushing entire object cache.
		bp_core_reset_incrementor( 'bb_reactions' );

		wp_send_json_success();
	} else {
		wp_send_json_error();
	}

	wp_die();
}

/**
 * Function to show global notice when reaction migration is running.
 *
 * @since 2.4.50
 *
 * @return void
 */
function bb_pro_reaction_show_global_notice() {
	$is_show_notice = bp_get_option( 'bb_pro_reaction_migration_notice' );
	if ( ! empty( $is_show_notice ) && 'inprogress' === bb_pro_reaction_get_migration_status() ) {
		printf(
			'<div id="bb-pro-reaction-global-notice" class="notice notice-warning is-dismissible">
						<p>%s</p>
					</div>',
			esc_html__( 'Reactions are currently being migrated. Once complete, the new reactions will be visible on your site.', 'buddyboss-pro' )
		);
	}
}

/**
 * Function to remove the like button if there is not a valid license.
 *
 * @since 2.11.0
 *
 * @param array $buttons     Array of activity entry buttons.
 * @param int   $activity_id The activity ID.
 *
 * @return array
 */
function bb_pro_remove_reactions_if_not_valid_license( $buttons, $activity_id ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed

	if ( 'likes' !== bb_get_reaction_mode() && bb_pro_should_lock_features() ) {
		unset( $buttons['activity_favorite'] );
	}
	return $buttons;
}

add_filter( 'bp_nouveau_get_activity_entry_buttons', 'bb_pro_remove_reactions_if_not_valid_license', PHP_INT_MAX, 2 );

/**
 * Function to remove the like button from the App if there is not a valid license.
 *
 * @since 2.11.0
 *
 * @param mixed $value Whether the activity can be favorited.
 *
 * @return false|mixed
 */
function bb_pro_activity_can_favorite( $value ) {

	if ( bb_pro_should_lock_features() ) {
		return false;
	}
	return $value;
}

add_filter( 'bp_activity_can_favorite', 'bb_pro_activity_can_favorite', PHP_INT_MAX, 1 );

/**
 * Clean up stale temp options created by bb_capture_old_reaction_mode().
 *
 * These temp options (bb_reaction_mode_before_save_{user_id}_{uuid}) are normally
 * deleted in the same request they are created. However, if the request fails
 * mid-save (fatal error, timeout, etc.), the temp option persists in wp_options.
 * This cleanup runs once per init to remove any options older than 5 minutes.
 *
 * @since 3.0.0
 */
function bb_pro_reaction_cleanup_stale_temp_options() {
	global $wpdb;

	// Only run cleanup once per hour to avoid unnecessary queries.
	$last_cleanup = get_option( 'bb_reaction_temp_options_last_cleanup', 0 );
	if ( ( time() - $last_cleanup ) < HOUR_IN_SECONDS ) {
		return;
	}

	update_option( 'bb_reaction_temp_options_last_cleanup', time(), false );

	// Delete stale temp options older than 5 minutes.
	// These are created with autoload=false, so they won't impact normal option loading.
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$wpdb->query(
		$wpdb->prepare(
			"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
			$wpdb->esc_like( 'bb_reaction_mode_before_save_' ) . '%'
		)
	);
}

/**
 * Check the migration status for React auto-refresh.
 *
 * Returns current migration status and data for the React admin UI
 * to update progress indicators without reloading the page.
 *
 * @since 3.0.0
 */
function bb_pro_reaction_check_migration() {
	if ( ! bp_current_user_can( 'bp_moderate' ) ) {
		wp_send_json_error(
			array(
				'message' => esc_html__( 'You do not have permission to perform this action.', 'buddyboss-pro' ),
			)
		);
	}

	$nonce = ! empty( $_REQUEST['nonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['nonce'] ) ) : '';
	if ( ! wp_verify_nonce( $nonce, 'bb-pro-check-reaction-migration' ) ) {
		wp_send_json_error(
			array(
				'message' => esc_html__( 'Unable to check migration status, please refresh and try again.', 'buddyboss-pro' ),
			)
		);
	}

	$migration_status = bb_pro_reaction_get_migration_status();
	$migration_data   = bb_pro_reaction_get_migration_action();

	wp_send_json_success(
		array(
			'migration_status' => $migration_status,
			'migration_data'   => ! empty( $migration_data ) ? $migration_data : array(),
		)
	);
}
