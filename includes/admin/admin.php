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
        
        require_once MNI_FREE_PATH . 'includes/admin/wizard-service.php';
        require_once MNI_FREE_PATH . 'includes/admin/wizard-controller.php';
        
        require_once MNI_FREE_PATH . 'includes/admin/settings-service.php';
        require_once MNI_FREE_PATH . 'includes/admin/settings-controller.php';
        
        require_once MNI_FREE_PATH . 'includes/traits/sanitizer.php';
        
        add_action( 'admin_menu', [ $this, 'register_menu' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
    }

    /**
     * Register plugin menu & pages.
     */
    public function register_menu() {
      
      // Main menu
        add_menu_page(
            __( 'Messenger Notifier', 'messengernotifier' ),
            __( 'Messenger Notifier', 'messengernotifier' ),
            'manage_options',
            'mni_free_settings',
            [ $this, 'render_settings_page' ],
            'dashicons-megaphone',
            65
        );
        
        $wizard_completed = get_option('mni_free_wizard_completed', 0);

        if ( ! $wizard_completed ) {
            add_submenu_page(
                'mni_free',
                __( 'Messenger notifier wizard', 'messengernotifier' ),
                __( 'wizard', 'messengernotifier' ),
                'manage_options',
                'mni_free_wizard',
                [ $this, 'render_wizard_page' ]
            );
        }
        
        // Settings (submenu)
        add_submenu_page(
            'mni_free',
            __( 'Settings', 'messengernotifier' ),
            __( 'Settings', 'messengernotifier' ),
            'manage_options',
            'mni_free_settings',
            [ $this, 'render_settings_page' ]
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
        wp_enqueue_style(
            'mni-free-wizard',
            MNI_FREE_URL . 'assets/css/wizard.css',
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
        
        wp_enqueue_script(
            'mni-free-wizard',
            MNI_FREE_URL . 'assets/js/wizard.js',
            [ 'jquery' , 'wp-i18n'  ],
            MNI_FREE_VERSION,
            true
        );

        wp_set_script_translations(
            'mni-free-wizard',   // script handle
            'mni',               // plugin text domain
            MNI_FREE_PATH . 'languages' // path to translation files
        );
        
        wp_enqueue_script(
          'mni-free-settings',
          MNI_FREE_URL . 'assets/js/settings.js',
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
    
    
    /**
     * render settings page
     */
    public function render_settings_page() {

      $settings_view = MNI_FREE_PATH . 'views/admin/settings.php';
  
      if ( file_exists( $settings_view ) ) {
          include $settings_view;
      } else {
          echo '<div class="wrap"><h2>' .
              esc_html__( 'Settings', 'messengernotifier' ) .
              '</h2><p>' .
              esc_html__( 'Settings view file missing.', 'messengernotifier' ) .
              '</p></div>';
      }
  }
}