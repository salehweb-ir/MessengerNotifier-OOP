<?php
/**
 * Messenger Notifier (Free)
 * 
 * Initializes the plugin core, autoloads classes, and prepares environment.
 * 
 * @package MessengerNotifier
 */

namespace MNI_FREE;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Main Initializer Class
 */
final class Init {

    /**
     * Boot the plugin.
     */
    public static function run() {
        self::define_constants();
        self::autoload();
        self::load_loader();
        self::register_hooks();

        /**
         * Fires once Messenger Notifier (Free) has been bootstrapped.
         * 
         * @since 1.0.0
         */
        do_action( 'mni_free_bootstrapped' );
    }

    /**
     * Define plugin constants if not already defined.
     */
    private static function define_constants() {
        if ( ! defined( 'MNI_FREE_VERSION' ) ) {
            define( 'MNI_FREE_VERSION', '2.0.0' );
        }

        if ( ! defined( 'MNI_FREE_PATH' ) ) {
            define( 'MNI_FREE_PATH', plugin_dir_path( dirname( __FILE__ ) ) );
        }

        if ( ! defined( 'MNI_FREE_URL' ) ) {
            define( 'MNI_FREE_URL', plugin_dir_url( dirname( __FILE__ ) ) );
        }

        if ( ! defined( 'MNI_FREE_BASENAME' ) ) {
            define( 'MNI_FREE_BASENAME', plugin_basename( MNI_FREE_PATH . 'messengernotifier.php' ) );
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
    }

    /**
     * Setup PSR-4-style autoloading for the plugin.
     */
    private static function autoload() {
        spl_autoload_register( function ( $class ) {
            // Only autoload MNI_FREE classes
            if ( strpos( $class, __NAMESPACE__ . '\\' ) !== 0 ) {
                return;
            }

            $class_name = str_replace( __NAMESPACE__ . '\\', '', $class );
            $class_name = strtolower( str_replace( '_', '-', $class_name ) );
            $file_path  = MNI_FREE_PATH . 'includes/' . str_replace( '\\', '/', $class_name ) . '.php';

            if ( file_exists( $file_path ) ) {
                require_once $file_path;
            } else {
                /**
                 * Fires when an autoloaded file is missing.
                 *
                 * @param string $file_path The missing file path.
                 */
                do_action( 'mni_free_missing_file', $file_path );
            }
        });
    }

    /**
     * Load the loader class that manages features and hooks.
     */
    private static function load_loader() {
        $loader_file = MNI_FREE_PATH . 'includes/loader.php';

        if ( file_exists( $loader_file ) ) {
            require_once $loader_file;

            if ( class_exists( __NAMESPACE__ . '\\Loader' ) ) {
                Loader::init();
            }
        } else {
            do_action( 'mni_free_missing_file', $loader_file );
        }
    }

    /**
     * Register internal WordPress hooks early.
     */
    private static function register_hooks() {
        // Example hook: plugin activation
        register_activation_hook( MNI_FREE_BASENAME, [ __CLASS__, 'on_activate' ] );

        // Example hook: plugin deactivation
        register_deactivation_hook( MNI_FREE_BASENAME, [ __CLASS__, 'on_deactivate' ] );
    }

    /**
     * Fired on plugin activation.
     */
    public static function on_activate() {
        // Prepare default options
        if ( ! get_option( 'mni_free_messenger' ) ) {
            update_option( 'mni_free_messenger', 'eitaa' ); // Default to Eitaa
        }

        do_action( 'mni_free_activated' );
    }

    /**
     * Fired on plugin deactivation.
     */
    public static function on_deactivate() {
        do_action( 'mni_free_deactivated' );
    }
}
