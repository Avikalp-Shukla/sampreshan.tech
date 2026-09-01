<?php
/**
 * Frontend frame handler for the CSS Selector Picker.
 *
 * Loads an overlay on the frontend (in a new tab) that lets an admin user
 * visually pick an element and returns a selector back to the snippet editor.
 *
 * @package WPCode
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WPCode_CSS_Selector_Picker_Frame
 */
class WPCode_CSS_Selector_Picker_Frame {

	/**
	 * Snippet ID (optional, used only for context).
	 *
	 * @var int
	 */
	private $snippet_id = 0;

	/**
	 * Picker token passed from the admin tab.
	 *
	 * @var string
	 */
	private $token = '';

	/**
	 * Nonce from the current request (reused across navigation).
	 *
	 * @var string
	 */
	private $nonce = '';

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'template_redirect', array( $this, 'maybe_load_picker' ) );
	}

	/**
	 * Check if we're in picker mode and load the necessary scripts.
	 *
	 * @return void
	 */
	public function maybe_load_picker() {
		if ( ! is_user_logged_in() ) {
			return;
		}

		if ( ! current_user_can( 'wpcode_edit_snippets' ) ) { // phpcs:ignore
			return;
		}

		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		if ( ! isset( $_GET['wpcode_css_picker'] ) || '1' !== $_GET['wpcode_css_picker'] ) {
			return;
		}

		$this->nonce = isset( $_GET['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ) : '';
		if ( empty( $this->nonce ) || ! wp_verify_nonce( $this->nonce, 'wpcode_css_picker' ) ) {
			wp_die( esc_html__( 'Invalid request.', 'insert-headers-and-footers' ) );
		}

		if ( isset( $_GET['snippet_id'] ) ) {
			$this->snippet_id = absint( $_GET['snippet_id'] );
		}
		if ( isset( $_GET['token'] ) ) {
			$this->token = sanitize_text_field( wp_unslash( $_GET['token'] ) );
		}
		// phpcs:enable WordPress.Security.NonceVerification.Recommended

		// Hide the WP admin bar in picker tab.
		add_filter( 'show_admin_bar', '__return_false' );

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_picker_assets' ), 10 );
	}

	/**
	 * Enqueue the picker overlay script/styles.
	 *
	 * @return void
	 */
	public function enqueue_picker_assets() {
		$handle  = 'wpcode-css-selector-picker';
		$src     = WPCODE_PLUGIN_URL . 'build/css-selector-picker.js';
		$version = defined( 'WPCODE_VERSION' ) ? WPCODE_VERSION : false;

		$deps       = array();
		$asset_file = WPCODE_PLUGIN_PATH . 'build/css-selector-picker.asset.php';
		if ( file_exists( $asset_file ) ) {
			$asset = require $asset_file;
			if ( isset( $asset['dependencies'] ) && is_array( $asset['dependencies'] ) ) {
				$deps = $asset['dependencies'];
			}
			if ( isset( $asset['version'] ) ) {
				$version = $asset['version'];
			}
		}

		wp_enqueue_script( $handle, $src, $deps, $version, true );

		$css_file = WPCODE_PLUGIN_PATH . 'build/css-selector-picker.css';
		if ( file_exists( $css_file ) ) {
			wp_enqueue_style( $handle, WPCODE_PLUGIN_URL . 'build/css-selector-picker.css', array(), $version );
		}

		$this->localize_picker_data();
	}

	/**
	 * Localize picker data for the JS.
	 *
	 * @return void
	 */
	public function localize_picker_data() {
		$admin_origin = '';
		$parsed_admin = wp_parse_url( admin_url() );
		if ( ! empty( $parsed_admin['scheme'] ) && ! empty( $parsed_admin['host'] ) ) {
			$admin_origin = $parsed_admin['scheme'] . '://' . $parsed_admin['host'];
			if ( ! empty( $parsed_admin['port'] ) ) {
				$admin_origin .= ':' . absint( $parsed_admin['port'] );
			}
		}

		wp_localize_script(
			'wpcode-css-selector-picker',
			'wpcodeCssSelectorPicker',
			array(
				'adminOrigin' => $admin_origin,
				'logoUrl'     => esc_url( WPCODE_PLUGIN_URL . 'admin/images/wpcode-logo.png' ),
				'snippetId'   => $this->snippet_id,
				'token'       => $this->token,
				'params'      => array(
					'wpcode_css_picker' => '1',
					'snippet_id'        => $this->snippet_id,
					'_wpnonce'          => $this->nonce,
					'token'             => $this->token,
				),
			)
		);
	}
}

new WPCode_CSS_Selector_Picker_Frame();
