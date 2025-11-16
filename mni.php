<?php
/**
 * Plugin Name: Messenger Notifier OOP
 * Description: Sends WordPress notifications to supported messengers like Eitaa.
 * Version: 2.0.0
 * Author: Salehweb
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Security: No direct access

// ==== Define constants ====
define( 'MNI_FREE_VERSION', '2.0.0' );
define( 'MNI_FREE_PATH', plugin_dir_path( __FILE__ ) );
define( 'MNI_FREE_URL', plugin_dir_url( __FILE__ ) );

// ==== Include core files ====
require_once MNI_FREE_PATH . 'includes/core/init.php';
require_once MNI_FREE_PATH . 'includes/core/activator.php';
require_once MNI_FREE_PATH . 'includes/core/deactivator.php';

// ==== Hooks ====
register_activation_hook( __FILE__, [ 'mni_free_activator', 'activate' ] );
register_deactivation_hook( __FILE__, [ 'mni_free_deactivator', 'deactivate' ] );

// ==== Initialize Plugin ====
mni_free_init::instance();
