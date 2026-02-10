<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once MNI_FREE_PATH . 'includes/traits/sanitizer.php';

class MNI_Free_Settings_Controller {

    public function __construct() {
        add_action(
            'admin_post_mni_free_save_settings',
            [ $this, 'handle_save' ]
        );
    }

    public function handle_save() {
      
        // Permission
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( 'Access denied' );
        }

        // Nonce
        check_admin_referer( 'mni_free_settings_save' );

        // Sanitize
        $settings = ( new MNI_Free_Sanitizer() )->sanitize_settings( $_POST['settings'] ?? [] );

        // Save
        update_option( 'mni_free_settings', $settings );

        // Redirect back
        wp_redirect(
            admin_url( 'admin.php?page=mni_free_settings&saved=1' )
        );
        exit;
    }
}
