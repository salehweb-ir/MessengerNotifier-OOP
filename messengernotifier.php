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
//  Define plugin constants
// ==================================================
if ( ! defined( 'MNI_FREE_VERSION' ) ) {
	define( 'MNI_FREE_VERSION', '1.0.0' );
}

if ( ! defined( 'MNI_FREE_PLUGIN_FILE' ) ) {
	define( 'MNI_FREE_PLUGIN_FILE', __FILE__ );
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

if ( ! defined( 'MNI_FREE_OPTION_KEYS' ) ) {
	define(
		'MNI_FREE_OPTION_KEYS',
		json_encode([
			'mni_free_token_eitaa_api',
			'mni_free_eitaa_channel_id',
			'mni_free_pageid',
		])
	);
}

// ==================================================
//  Activation hook – setup wizard
// ==================================================
register_activation_hook( __FILE__, 'mni_free_on_activate' );

function mni_free_on_activate() {
	require_once MNI_FREE_PATH . 'includes/wizard.php';

	if ( class_exists( '\MNI_FREE\Includes\Wizard' ) ) {
		(new \MNI_FREE\Includes\Wizard)->init();
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
