<?php
/**
 * Messenger Notifier (Free)
 * 
 * Loader – manages the initialization of hooks, features, and dependencies.
 * 
 * @package MessengerNotifier
 */

namespace MNI_FREE;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Loader
 * Handles dynamic loading of plugin modules.
 */
final class Loader {

    /**
     * Boot the Loader.
     */
    public static function run() {
        self::load_hooks();
        self::load_features();

        /**
         * Fires when all Messenger Notifier Free components are loaded.
         * 
         * @since 1.0.0
         */
        do_action( 'mni_free_loaded' );
    }

    /**
     * Load the hooks.php file if available.
     */
    private static function load_hooks() {
        $hooks_file = MNI_FREE_PATH . 'includes/hooks.php';

        if ( file_exists( $hooks_file ) ) {
            require_once $hooks_file;

            if ( class_exists( __NAMESPACE__ . '\\Hooks' ) ) {
                Hooks::init();
            }
        } else {
            do_action( 'mni_free_missing_file', $hooks_file );
        }
    }

    /**
     * Dynamically load feature files (e.g., shortcodes, WooCommerce, comments).
     */
    private static function load_features() {
        $features_dir = MNI_FREE_PATH . 'includes/features/';

        if ( ! is_dir( $features_dir ) ) {
            return;
        }

        $files = scandir( $features_dir );

        foreach ( $files as $file ) {
            if ( $file === '.' || $file === '..' ) {
                continue;
            }

            $file_path = $features_dir . $file;

            if ( is_file( $file_path ) && pathinfo( $file_path, PATHINFO_EXTENSION ) === 'php' ) {
                require_once $file_path;

                // Derive class name from file (e.g., shortcodes.php → Shortcodes)
                $class_base = ucfirst( str_replace( '-', '_', pathinfo( $file, PATHINFO_FILENAME ) ) );
                $class_name = __NAMESPACE__ . '\\Features\\' . $class_base;

                if ( class_exists( $class_name ) && method_exists( $class_name, 'init' ) ) {
                    $class_name::init();
                } else {
                    /**
                     * Fires when a feature class is missing or not initializable.
                     * 
                     * @param string $class_name Name of the class that failed.
                     */
                    do_action( 'mni_free_missing_feature', $class_name );
                }
            }
        }
    }
}
