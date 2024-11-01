<?php
/**
 * The back-end specific functionality of the plugin.
 *
 * @package WP_Gallery_Enhancer
 * @since 1.0.0
 */

namespace WP_Gallery_Enhancer;

/**
 * The back-end specific functionality of the plugin.
 *
 * @since 1.0.0
 */
class Backend {

	/**
	 * Holds the instance of this class.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    object
	 */
	protected static $instance = null;

	/**
	 * Constructor method.
	 *
	 * @since  1.0.0
	 */
	public function __construct() {}

	/**
	 * Register hooked functions.
	 *
	 * @since 1.0.0
	 */
	public static function init() {
		// Load plugin's widgetlayer class.
		require WP_GALLERY_ENHANCER_DIR . '/backend/inc/class-widgetlayer.php';

		add_action( 'enqueue_block_editor_assets', [ self::get_instance(), 'enqueue_admin_scripts' ] );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_admin_scripts() {
		wp_enqueue_script(
			'wpge-flickity',
			plugins_url( '/frontend/js/flickity.pkgd.min.js', dirname( __FILE__ ) ),
			[],
			WP_GALLERY_ENHANCER_VERSION,
			true
		);

		wp_enqueue_script(
			'wpge-bricklayer',
			plugins_url( '/frontend/js/bricklayer.build.js', dirname( __FILE__ ) ),
			[],
			WP_GALLERY_ENHANCER_VERSION,
			true
		);

		wp_enqueue_script(
			'wpge-blocks-js',
			plugin_dir_url( __FILE__ ) . 'js/blocks.build.js',
			array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-components', 'wp-editor', 'wp-data', 'wp-block-editor', 'wp-compose', 'wp-hooks', 'wpge-flickity', 'wpge-bricklayer' ),
			WP_GALLERY_ENHANCER_VERSION,
			true
		);

		wp_enqueue_style(
			'wpge-blocks-css',
			plugins_url( '/frontend/css/style.css', dirname( __FILE__ ) ),
			array(),
			WP_GALLERY_ENHANCER_VERSION
		);
	}

	/**
	 * Returns the instance of this class.
	 *
	 * @since  1.0.0
	 *
	 * @return object Instance of this class.
	 */
	public static function get_instance() {

		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}

Backend::init();
