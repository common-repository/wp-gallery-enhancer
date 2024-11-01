<?php
/**
 * The front end specific functionality of the plugin.
 *
 * @package WP_Gallery_Enhancer
 * @subpackage Frontend
 * @since 1.0.0
 */

namespace WP_Gallery_Enhancer;

/**
 * The front-end specific functionality of the plugin.
 *
 * @since 1.0.0
 */
class Frontend {

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
		add_action( 'wp_enqueue_scripts', [ self::get_instance(), 'enqueue_public_scripts' ] );
		add_filter( 'body_class', [ self::get_instance(), 'add_body_classes' ] );
	}

	/**
	 * Register the frontend scripts.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_public_scripts() {
		wp_enqueue_style(
			'wpge-styles',
			plugin_dir_url( __FILE__ ) . 'css/style.css',
			[],
			WP_GALLERY_ENHANCER_VERSION,
			'all'
		);

		wp_enqueue_script(
			'flickity',
			plugin_dir_url( __FILE__ ) . 'js/flickity.pkgd.min.js',
			[],
			WP_GALLERY_ENHANCER_VERSION,
			true
		);

		wp_enqueue_script(
			'bricklayer',
			plugin_dir_url( __FILE__ ) . 'js/bricklayer.build.js',
			[],
			WP_GALLERY_ENHANCER_VERSION,
			true
		);

		wp_enqueue_script(
			'wpge-scripts',
			plugin_dir_url( __FILE__ ) . 'js/scripts.build.js',
			[ 'flickity', 'bricklayer' ],
			WP_GALLERY_ENHANCER_VERSION,
			true
		);
	}

	/**
	 * Extend the default WordPress body classes.
	 *
	 * @since 1.0.0
	 *
	 * @param array $classes Classes for the body element.
	 * @return array
	 */
	public function add_body_classes( $classes ) {
		$classes[] = 'wpge';
		return $classes;
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

Frontend::init();
