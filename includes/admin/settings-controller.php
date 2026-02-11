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

        /* ---------------------------------
         * Permission
         * --------------------------------- */
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( 'Access denied' );
        }

        /* ---------------------------------
         * Nonce
         * --------------------------------- */
        check_admin_referer( 'mni_free_settings_save' );

        /* ---------------------------------
         * Old Settings (for page ID)
         * --------------------------------- */
        $old_settings = (array) get_option( 'mni_free_settings', [] );
        $page_id      = $old_settings['contact_page']['id'] ?? 0;

        /* ---------------------------------
         * Sanitize Input
         * --------------------------------- */
        $settings = ( new MNI_Free_Sanitizer() )
            ->sanitize_settings( $_POST['settings'] ?? [] );

        /* ---------------------------------
         * Preserve existing page ID
         * --------------------------------- */
        if ( $page_id ) {
            $settings['contact_page']['id'] = $page_id;
        }

        /* ---------------------------------
         * Update Page Template if changed
         * --------------------------------- */
        if (
            $page_id &&
            ! empty( $settings['contact_page']['template'] )
        ) {
            update_post_meta(
                $page_id,
                '_wp_page_template',
                $settings['contact_page']['template']
            );
        }

        /* ---------------------------------
         * Save Settings
         * --------------------------------- */
        update_option( 'mni_free_settings', $settings );

        /* ---------------------------------
         * Redirect
         * --------------------------------- */
        wp_redirect(
            admin_url( 'admin.php?page=mni_free_settings&saved=1' )
        );
        exit;
    }
}
