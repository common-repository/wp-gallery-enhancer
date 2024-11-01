<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link https://vedathemes.com
 * @since 1.0.0
 * @package WP_Gallery_Enhancer
 *
 * @wordpress-plugin
 * Plugin Name: WP Gallery Enhancer
 * Description: Simple and easy way to add featured text and media content to your website.
 * Version: 1.1
 * Author: vedathemes
 * Author URI: https://vedathemes.com
 * License: GPL-3.0+
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain: wp-gallery-enhancer
 * Domain Path: /lang
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Define plugin constants.
define( 'WP_GALLERY_ENHANCER_DIR', plugin_dir_path( __FILE__ ) );

// Currently plugin version.
define( 'WP_GALLERY_ENHANCER_VERSION', '1.1' );

// Load plugin textdomain.
add_action( 'plugins_loaded', 'wp_gallery_enhancer_plugins_loaded' );

// Load plugin's front-end functionality.
require WP_GALLERY_ENHANCER_DIR . '/frontend/class-frontend.php';

// Load plugin's admin functionality.
require WP_GALLERY_ENHANCER_DIR . '/backend/class-backend.php';

/**
 * Load plugin text domain.
 *
 * @since 1.0.0
 */
function wp_gallery_enhancer_plugins_loaded() {
	load_plugin_textdomain( 'WP_Gallery_Enhancer', false, WP_GALLERY_ENHANCER_DIR . 'lang/' );
}
