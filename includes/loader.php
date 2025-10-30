<?php
/**
 * Messenger Notifier - Loader
 *
 * Handles dynamic loading of plugin components (features, admin pages, API manager, etc.)
 *
 * @package MessengerNotifier
 */

namespace MNI_FREE;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // 🔒 Security: Prevent direct access
}

/**
 * Loader Class
 *
 * Responsible for including core files in a controlled and extendable way.
 */
class Loader {

    /**
     * Initialize all required components.
     */
    public static function init() {
        self::load_hooks();
        self::load_features();
        self::load_admin();
        self::load_api();
    }

    /**
     * Load the plugin-wide hooks file.
     */
    private static function load_hooks() {
        $hooks_file = MNI_FREE_PATH . 'includes/hooks.php';

        if ( file_exists( $hooks_file ) ) {
            require_once $hooks_file;
        } else {
            do_action( 'mni_free_missing_file', $hooks_file );
        }
    }

    /**
     * Load feature modules (comments, WooCommerce, shortcodes, etc.)
     */
    private static function load_features() {
        $features_path = MNI_FREE_PATH . 'includes/features/';

        $feature_files = [
            'comments.php',
            'woocommerce.php',
            'shortcodes.php',
        ];

        foreach ( $feature_files as $file ) {
            $path = $features_path . $file;

            if ( file_exists( $path ) ) {
                require_once $path;
                self::init_feature_class( $file );
            } else {
                do_action( 'mni_free_missing_file', $path );
            }
        }
    }

    /**
     * Load admin components (settings, wizard, etc.)
     */
    private static function load_admin() {
        if ( ! is_admin() ) {
            return;
        }

        $admin_path = MNI_FREE_PATH . 'admin/';
        $admin_files = [
            'settings.php',
            'wizard.php',
        ];

        foreach ( $admin_files as $file ) {
            $path = $admin_path . $file;

            if ( file_exists( $path ) ) {
                require_once $path;
                self::init_admin_class( $file );
            } else {
                do_action( 'mni_free_missing_file', $path );
            }
        }
    }

    /**
     * Load the API manager and messengers.
     */
    private static function load_api() {
        $api_manager = MNI_FREE_PATH . 'api/messenger-manager.php';

        if ( file_exists( $api_manager ) ) {
            require_once $api_manager;

            if ( class_exists( '\MNI_FREE\API\Messenger_Manager' ) ) {
                \MNI_FREE\API\Messenger_Manager::init();
            }
        } else {
            do_action( 'mni_free_missing_file', $api_manager );
        }
    }

    /**
     * Initialize a feature class automatically by file name.
     *
     * e.g., `comments.php` → `\MNI_FREE\Features\Comments`
     *
     * @param string $file File name of the feature.
     */
    private static function init_feature_class( $file ) {
        $class_name = '\\MNI_FREE\\Features\\' . ucfirst( basename( $file, '.php' ) );

        if ( class_exists( $class_name ) && method_exists( $class_name, 'init' ) ) {
            $class_name::init();
        }
    }

    /**
     * Initialize an admin class automatically by file name.
     *
     * e.g., `settings.php` → `\MNI_FREE\Admin\Settings`
     *
     * @param string $file File name of the admin file.
     */
    private static function init_admin_class( $file ) {
        $class_name = '\\MNI_FREE\\Admin\\' . ucfirst( basename( $file, '.php' ) );

        if ( class_exists( $class_name ) && method_exists( $class_name, 'init' ) ) {
            $class_name::init();
        }
    }
}


// wizard only runs after activation. should it iclude always? THINK ABOUT IT!
