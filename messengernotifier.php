<?php
/**
 * Plugin Name:       Messenger Notifier
 * Plugin URI:        https://eitaa.com/messengernotifier
 * Description:       Sends WordPress and WooCommerce events to Iranian messengers via Eitaa API.
 * Version:           1.1.0
 * Author:            MJAsia
 * Author URI:        https://eitaa.com/mjasia
 * License:           GPL-3.0-or-later
 * Text Domain:       messengernotifier
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ------------------------------------------------------------
 * Define constants
 * ------------------------------------------------------------
 */
if ( ! defined( 'MNI_FREE_VERSION' ) ) {
	define( 'MNI_FREE_VERSION', '1.0.0' );
}

if ( ! defined( 'MNI_FREE_PATH' ) ) {
	define( 'MNI_FREE_PATH', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'MNI_FREE_URL' ) ) {
	define( 'MNI_FREE_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'MNI_FREE_BASENAME' ) ) {
	define( 'MNI_FREE_BASENAME', plugin_basename( __FILE__ ) );
}

/**
 * ------------------------------------------------------------
 * Initialize plugin (OOP entry point)
 * ------------------------------------------------------------
 */
function mni_free_init() {
	require_once MNI_FREE_PATH . 'includes/init.php';

	if ( class_exists( '\MNI_FREE\Init' ) ) {
		\MNI_FREE\Init::run();
	}
}
add_action( 'plugins_loaded', 'mni_free_init', 5 );
