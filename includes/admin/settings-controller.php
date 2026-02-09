<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class MNI_Free_Settings_Controller {

    public function __construct() {
        add_action(
            'admin_post_mni_free_save_settings',
            [ $this, 'handle_save' ]
        );
    }

    public function handle_save() {

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( 'Access denied' );
        }

        check_admin_referer( 'mni_free_save_settings' );

        $raw = $_POST['settings'] ?? [];
       $clean = MNI_Free_Settings_Sanitizer::sanitize(
            $_POST['settings'] ?? []
        );


        update_option( 'mni_free_settings', $clean );

        wp_redirect(
            admin_url( 'admin.php?page=mni_free_settings&saved=1' )
        );
        exit;
    }
}
new MNI_Free_Settings_Controller();
