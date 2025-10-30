<?php
/**
 * Messenger Notifier (Free)
 *
 * Hooks – registers all WordPress actions and filters for the plugin.
 *
 * @package MessengerNotifier
 */

namespace MNI_FREE;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Hooks
 * Registers and manages plugin-wide hooks and filters.
 */
final class Hooks {

    /**
     * Initialize the hooks system.
     */
    public static function init() {

        // 🔹 Core plugin hooks
        add_action( 'init', [ __CLASS__, 'register_shortcodes' ] );
        add_action( 'plugins_loaded', [ __CLASS__, 'load_textdomain' ] );

        // 🔹 Admin notifications (for missing files or feature classes)
        add_action( 'mni_free_missing_file', [ __CLASS__, 'handle_missing_file' ] );
        add_action( 'mni_free_missing_feature', [ __CLASS__, 'handle_missing_feature' ] );

        // 🔹 Filters for extending data (Pro-ready)
        self::register_filters();
    }

    /**
     * Register textdomain for translations.
     */
    public static function load_textdomain() {
        load_plugin_textdomain(
            'messengernotifier',
            false,
            dirname( MNI_FREE_BASENAME ) . '/languages/'
        );
    }

    /**
     * Register shortcode loading (delegated to Shortcodes feature).
     */
    public static function register_shortcodes() {
        do_action( 'mni_free_register_shortcodes' );
    }

    /**
     * Handle missing file warnings gracefully.
     *
     * @param string $file_path Path to the missing file.
     */
    public static function handle_missing_file( $file_path ) {
        if ( is_admin() ) {
            error_log( "[MessengerNotifierFree] Missing file: {$file_path}" );
        }
    }

    /**
     * Handle missing feature warnings gracefully.
     *
     * @param string $class_name Name of the missing class.
     */
    public static function handle_missing_feature( $class_name ) {
        if ( is_admin() ) {
            error_log( "[MessengerNotifierFree] Missing feature class: {$class_name}" );
        }
    }

    /**
     * Register WordPress filters for extendable data points.
     * 
     * These allow the Pro version to inject extra information into messages.
     */
    private static function register_filters() {

        /**
         * 🔸 Filter: Modify data before sending to messenger API.
         *
         * @param array $data {
         *   @type string $token
         *   @type string $channel_id
         *   @type string $message
         *   @type string $hashtag
         *   @type string $messenger  (e.g., 'eitaa')
         * }
         */
        add_filter( 'mni_free_before_send_message', function( $data ) {
            return $data; // Default: no modification
        }, 10, 1 );

        /**
         * 🔸 Filter: Modify comment mess*
