<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Messenger Notifier Free – Admin Controller
 */

require_once MNI_FREE_PATH . 'includes/traits/singleton.php';

class mni_free_admin {
    use mni_singleton;

    /**
     * Constructor.
     */
    private function __construct() {
        add_action( 'admin_menu', [ $this, 'register_menu' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
    }

    private function register_test_hook() {

        add_action( 'admin_init', function () {

            if (
                isset( $_GET['mni_test_message'] ) &&
                current_user_can( 'manage_options' )
            ) {

                $result = mni_free_messenger_manager::instance()
                    ->send( 'سلام! این یک پیام تست است 🚀', '#test' );

                error_log( print_r( $result, true ) );
            }
        });
    }

    /**
     * Register plugin menu & pages.
     */
    public function register_menu() {
        add_menu_page(
            __( 'Messenger Notifier', 'messengernotifier' ),
            __( 'Messenger Notifier', 'messengernotifier' ),
            'manage_options',
            'mni_free_wizard',
            [ $this, 'render_wizard_page' ],
            'dashicons-megaphone',
            65
        );
    }

    /**
     * Enqueue admin CSS/JS.
     */
    public function enqueue_assets( $hook ) {
        if ( strpos( $hook, 'mni_free' ) === false ) {
            return;
        }

        wp_enqueue_style(
            'mni-free-admin',
            MNI_FREE_URL . 'assets/css/admin.css',
            [],
            MNI_FREE_VERSION
        );

        wp_localize_script( 'mni-wizard-js-handle', 'mniWizard', [
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'mni_free_test_api_nonce' ),
        ] );

        wp_enqueue_script(
            'mni-free-admin',
            MNI_FREE_URL . 'assets/js/admin.js',
            [ 'jquery' ],
            MNI_FREE_VERSION,
            true
        );
    }

    /**
     * Default callback – render wizard page (if wizard.php exists).
     */
    public function render_wizard_page() {
        $wizard_view = MNI_FREE_PATH . 'views/admin/wizard.php';
        if ( file_exists( $wizard_view ) ) {
            include $wizard_view;
        } else {
            echo '<div class="wrap"><h2>' . esc_html__( 'Setup Wizard', 'messengernotifier' ) . '</h2>';
            echo '<p>' . esc_html__( 'Wizard view file missing.', 'messengernotifier' ) . '</p></div>';
        }
    }
}
