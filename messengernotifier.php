<?php
/**
 * Plugin Name:       Messenger Notifier
 * Plugin URI:        https://eitaa.com/messengernotifier
 * Description:       Sends WordPress notifications to supported messengers like Eitaa. Allows anonymous contact form and WooCommerce order alerts.
 * Version:           2.0.0
 * Author:            MJAsia
 * Author URI:        https://eitaa.com/mjasia
 * License:           GPL-3.0-or-later
 * Text Domain:       messengernotifier
 * Domain Path:       /languages
 */

// ==================================================
//  Security check
// ==================================================
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ==================================================
//  Activation hook – setup wizard
// ==================================================
register_activation_hook( __FILE__, 'mni_free_on_activate' );

function mni_free_on_activate() {
	require_once MNI_FREE_PATH . 'includes/wizard.php';

	if ( class_exists( '\MNI_FREE\Wizard' ) ) {
		\MNI_FREE\Wizard::run_setup();
	}
}

// ==================================================
//  Initialize plugin
// ==================================================
add_action( 'plugins_loaded', 'mni_free_init_plugin', 1 );

function mni_free_init_plugin() {
	require_once MNI_FREE_PATH . 'includes/init.php';

	if ( class_exists( '\MNI_FREE\Init' ) ) {
		\MNI_FREE\Init::run();
	}
}
