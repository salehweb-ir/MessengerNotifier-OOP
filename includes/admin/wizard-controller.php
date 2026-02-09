<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class MNI_Free_Wizard_Controller {

    public function __construct() {
        add_action(
            'admin_post_mni_free_save_wizard',
            [ $this, 'handle_save' ]
        );
    }

    /**
     * Handle wizard form submission
     */
    public function handle_save() {

        // 1️⃣ Capability check
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( 'Access denied' );
        }

        // 2️⃣ Nonce check
        check_admin_referer( 'mni_wizard_nonce' );

        // 3️⃣ Sanitize & prepare data
        $settings = MNI_Free_Settings_Sanitizer::sanitize(
            $_POST['settings'] ?? []
        );

        // 4️⃣ Save settings
        update_option( 'mni_free_settings', $settings );

        // 5️⃣ Mark wizard as completed
        update_option( 'mni_free_wizard_completed', 1 );

        // 6️⃣ Redirect back to wizard page
        wp_redirect(
            admin_url( 'admin.php?page=mni_free_wizard&saved=1' )
        );
        exit;
    }
}
