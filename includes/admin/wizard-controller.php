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
        $settings = $this->sanitize_settings( $_POST['settings'] );

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

    /**
     * Sanitize wizard settings
     */
    private function sanitize_settings( array $settings ) : array {

    $clean = [];

    // 1️⃣ messengers
    $clean['messengers'] = [];
    if ( isset( $settings['messengers'] ) && is_array( $settings['messengers'] ) ) {
        foreach ( $settings['messengers'] as $messenger ) {
            $clean['messengers'][] = sanitize_key( $messenger );
        }
        $clean['messengers'] = array_values( array_unique( $clean['messengers'] ) );
    }

    // 2️⃣ actions
    $clean['actions'] = [];
    if ( isset( $settings['actions'] ) && is_array( $settings['actions'] ) ) {
        foreach ( $settings['actions'] as $action ) {
            $clean['actions'][] = sanitize_key( $action );
        }
        $clean['actions'] = array_values( array_unique( $clean['actions'] ) );
    }

    // 3️⃣ config
    $clean['config'] = [];
    if ( isset( $settings['config'] ) && is_array( $settings['config'] ) ) {
        foreach ( $settings['config'] as $messenger => $config ) {

            $messenger = sanitize_key( $messenger );

            if ( ! is_array( $config ) ) {
                continue;
            }

            $clean['config'][ $messenger ] = [];

            foreach ( $config as $key => $value ) {
                $clean['config'][ $messenger ][ sanitize_key( $key ) ]
                    = sanitize_text_field( $value );
            }
        }
    }

    return $clean;
}

}
