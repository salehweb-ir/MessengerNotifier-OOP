<?php
if ( ! defined( 'ABSPATH' ) ) exit;

require_once MNI_FREE_PATH . 'includes/traits/sanitizer.php';

class MNI_Free_Wizard_Controller {

    public function __construct() {
        add_action( 'admin_post_mni_free_save_wizard', [ $this, 'save' ] );
    }

    public function save() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( 'Access denied' );
        }

        check_admin_referer( 'mni_free_wizard_save' );

        $settings = ( new MNI_Free_Sanitizer() )->sanitize_settings( $_POST['settings'] ?? [] );
        
        error_log("POST settings: \n" . print_r($_POST['settings'], true) . "\n");
        
        
        error_log("settings: " . print_r($settings, true) . "\n");

        update_option( 'mni_free_settings', $settings );
        update_option( 'mni_free_wizard_completed', 1 );

        wp_redirect( admin_url( 'admin.php?page=mni_free_settings&saved=1' ) );
        exit;
    }
}
