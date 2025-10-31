<?php
/**
 * WooCommerce Integration Loader
 *
 * Handles initialization of WooCommerce-related features.
 * Loads all action-specific files from the /woocommerce/ directory.
 *
 * @package MessengerNotifier
 */

namespace MNI_FREE\Features;

use MNI_FREE\Loader;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Prevent direct access
}

/**
 * Class WooCommerce
 *
 * Loads WooCommerce integrations dynamically.
 */
class WooCommerce {

	/**
	 * Initialize WooCommerce integrations.
	 *
	 * @return void
	 */
	public static function init() {
		// Make sure WooCommerce is active
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}

		$directory = MNI_FREE_PATH . 'features/woocommerce/';

		if ( ! is_dir( $directory ) ) {
			do_action( 'mni_free_missing_file', $directory );
			return;
		}

		$files = glob( $directory . '*.php' );

		if ( ! empty( $files ) ) {
			foreach ( $files as $file ) {
				require_once $file;
			}
		}

		do_action( 'mni_free_woocommerce_loaded' );
	}

}
