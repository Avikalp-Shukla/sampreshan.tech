<?php
/**
 * BuddyBoss Pro Topics.
 *
 * @since   2.7.40
 * @package BuddyBossPro
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Set up the bp topics class.
 *
 * @since 2.7.40
 */
class BB_Topics {

	/**
	 * Class instance.
	 *
	 * @since 2.7.40
	 *
	 * @var $instance
	 */
	public static $instance;

	/**
	 * Unique ID for the topics.
	 *
	 * @since 2.7.40
	 *
	 * @var string
	 */
	public $id = 'topics';

	/**
	 * Topics Constructor.
	 *
	 * @since 2.7.40
	 */
	public function __construct() {

		// Include the code.
		$this->includes();
		$this->setup_actions();

		add_action( 'bp_init', array( $this, 'load_init' ), 5 );
	}

	/**
	 * Get the instance of the class.
	 *
	 * @since 2.7.40
	 *
	 * @return BB_Topics
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			$class_name     = __CLASS__;
			self::$instance = new $class_name();
		}

		return self::$instance;
	}

	/**
	 * Load the init.
	 *
	 * @since 2.7.40
	 */
	public function load_init() {
		if (
			bp_is_active( 'activity' ) &&
			bp_is_active( 'groups' ) &&
			function_exists( 'bb_is_enabled_activity_topics' ) &&
			bb_is_enabled_activity_topics()
		) {
			require_once __DIR__ . '/class-bb-group-activity-topics-settings.php';
			$extension = new BB_Group_Activity_Topics_Setting();
			add_action( 'bp_actions', array( $extension, '_register' ), 8 );
		}

		// Register the template for topics.
		bp_register_template_stack( array( $this, 'bb_register_topics_template' ) );
	}

	/**
	 * Setup actions for topics.
	 *
	 * @since 2.7.40
	 */
	public function setup_actions() {
		// @todo: Remove after 3 release. Legacy admin enqueue — Settings 2.0 React has its own styles. - Start
		if ( ! function_exists( 'bb_register_feature' ) ) {
			add_action( 'bp_admin_enqueue_scripts', array( $this, 'bb_admin_enqueue_script' ) );
		}
		// @todo: Remove after 3 release. Legacy admin enqueue — Settings 2.0 React has its own styles. - End
		add_action( 'bp_enqueue_scripts', array( $this, 'bb_enqueue_script' ) );

		// Settings 2.0: Register Group Topic Options field early enough (before bp_loaded).
		add_action( 'bb_activity_settings_after_topics_fields', array( $this, 'bb_register_group_topic_options_field' ) );
	}

	/**
	 * Register Group Topic Options field for Settings 2.0.
	 *
	 * This must be registered in setup_actions() (bp_setup_components) rather than
	 * in BB_Group_Activity_Topics_Setting (bp_init) because bb_register_features
	 * fires on bp_loaded which is before bp_init.
	 *
	 * @since 3.0.0
	 */
	public function bb_register_group_topic_options_field() {
		if ( ! function_exists( 'bb_register_feature_field' ) || ! bp_is_active( 'groups' ) ) {
			return;
		}

		bb_register_feature_field(
			'activity',
			'activity_topics',
			'group_topics',
			array(
				'name'              => 'bb-group-activity-topics-options',
				'label'             => __( 'Group Topic Options', 'buddyboss-pro' ),
				'type'              => 'radio',
				'description'       => '',
				'default'           => bb_get_group_activity_topic_options(),
				'options'           => array(
					array(
						'label' => __( 'Allow group organizers to use topics from only activity topics.', 'buddyboss-pro' ),
						'value' => 'only_from_activity_topics',
					),
					array(
						'label' => __( 'Allow group organizers to create own topics', 'buddyboss-pro' ),
						'value' => 'create_own_topics',
					),
					array(
						'label' => __( 'Both', 'buddyboss-pro' ),
						'value' => 'allow_both',
					),
				),
				'order'             => 20,
				'pro_only'          => true,
				'sanitize_callback' => array( $this, 'bb_sanitize_group_activity_topic_options' ),
				'conditional'       => array(
					'field' => 'bb-enable-group-activity-topics',
					'value' => true,
				),
			)
		);
	}

	/**
	 * Sanitize group activity topic options for Settings 2.0 (whitelist).
	 *
	 * @since 3.0.0
	 *
	 * @param string $value Submitted value.
	 *
	 * @return string One of only_from_activity_topics, create_own_topics, allow_both.
	 */
	public function bb_sanitize_group_activity_topic_options( $value ) {
		$allowed = array( 'only_from_activity_topics', 'create_own_topics', 'allow_both' );

		return in_array( $value, $allowed, true ) ? $value : 'only_from_activity_topics';
	}

	/**
	 * Enqueue admin related scripts and styles.
	 *
	 * @since 2.7.40
	 *
	 * @param string $hook_suffix The current admin page.
	 */
	public function bb_admin_enqueue_script( $hook_suffix ) {
		// @todo: Remove after 3 release. The 'bp-groups' condition is dead code — legacy Group Admin page removed. - Start
		if (
			is_admin() &&
			(
				false === strpos( $hook_suffix, 'bp-groups' ) &&
				false === strpos( $hook_suffix, 'bp-settings' )
			)
		) {
			return;
		}
		// @todo: Remove after 3 release. The 'bp-groups' condition is dead code — legacy Group Admin page removed. - End

		if (
			! is_admin() &&
			function_exists( 'bb_topics_manager_instance' ) &&
			method_exists( bb_topics_manager_instance(), 'bb_load_topics_scripts' ) &&
			bb_topics_manager_instance()->bb_load_topics_scripts()
		) {
			return;
		}

		$min     = bp_core_get_minified_asset_suffix();
		$rtl_css = is_rtl() ? '-rtl' : '';

		wp_enqueue_style(
			'bb-topics-admin-style',
			bb_topics_url( '/assets/css/bb-topics-admin' . $rtl_css . $min . '.css' ),
			array(),
			bb_platform_pro()->version
		);

		$this->bb_common_enqueue_script();
	}

	/**
	 * Enqueue frontend related scripts.
	 *
	 * @since 2.7.40
	 */
	public function bb_enqueue_script() {
		if (
			function_exists( 'bb_topics_manager_instance' ) &&
			method_exists( bb_topics_manager_instance(), 'bb_load_topics_scripts' ) &&
			bb_topics_manager_instance()->bb_load_topics_scripts()
		) {
			return;
		}

		$min     = bp_core_get_minified_asset_suffix();
		$rtl_css = is_rtl() ? '-rtl' : '';
		wp_enqueue_style(
			'bb-topics-style',
			bb_topics_url( '/assets/css/bb-topics' . $rtl_css . $min . '.css' ),
			array(),
			bb_platform_pro()->version
		);

		$this->bb_common_enqueue_script();
	}

	/**
	 * Enqueue admin and frontend related scripts.
	 *
	 * @since 2.7.40
	 */
	public function bb_common_enqueue_script() {
		$group_activity_topic_options = bb_get_group_activity_topic_options();
		$allow_select                 = 'only_from_activity_topics' === $group_activity_topic_options || 'allow_both' === $group_activity_topic_options;
		if ( $allow_select ) {
			wp_enqueue_style( 'bp-select2' );
			wp_enqueue_script( 'bp-select2' );
		}

		$min = bp_core_get_minified_asset_suffix();

		$src = bb_topics_url( '/assets/js/bb-topics' . $min . '.js' );

		if (
			function_exists( 'bb_is_readylaunch_enabled' ) &&
			bb_is_readylaunch_enabled() &&
			class_exists( 'BB_Readylaunch' ) &&
			bb_load_readylaunch()->bb_is_readylaunch_enabled_for_page()
		) {
			$src = bb_topics_url( '/assets/js/bb-rl-topics' . $min . '.js' );
		}

		wp_enqueue_script(
			'bb-topics-script',
			$src,
			array(
				'bb-topics-manager',
			),
			bb_platform_pro()->version,
			true
		);

		wp_localize_script(
			'bb-topics-script',
			'bbTopics',
			array(
				'ajax_url'            => admin_url( 'admin-ajax.php' ),
				'group_topic_options' => bb_get_group_activity_topic_options(),
			)
		);
	}

	/**
	 * Includes files.
	 *
	 * @since 2.7.40
	 *
	 * @param array $includes list of the files.
	 */
	public function includes( $includes = array() ) {

		$bb_platform_pro = bb_platform_pro();
		$slashed_path    = trailingslashit( $bb_platform_pro->topics_dir );

		$includes = array(
			'functions',
			'filters',
		);

		// Loop through files to be included.
		foreach ( (array) $includes as $file ) {

			if ( empty( $this->bb_topics_check_has_licence() ) ) {
				if ( in_array( $file, array( 'filters', 'rest-filters' ), true ) ) {
					continue;
				}
			}

			$paths = array(

				// Passed with no extension.
				'bb-' . $this->id . '-' . $file . '.php',
				'bb-' . $this->id . '/' . $file . '.php',

				// Passed with an extension.
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

			unset( $paths );
		}

		unset( $includes );
	}

	/**
	 * Function to return the default value if no licence.
	 *
	 * @since 2.7.40
	 *
	 * @param bool $has_access Whether it has access.
	 *
	 * @return bool Return the default.
	 */
	protected function bb_topics_check_has_licence( $has_access = true ) {

		if ( bb_pro_should_lock_features() ) {
			return false;
		}

		return $has_access;
	}

	/**
	 * Register the template for topics.
	 *
	 * @since 2.7.40
	 */
	public function bb_register_topics_template() {
		return bb_platform_pro()->topics_dir . '/templates';
	}
}
