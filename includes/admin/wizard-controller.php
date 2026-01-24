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
        check_admin_referer( 'mni_free_wizard_save' );

        // 3️⃣ Sanitize & prepare data
        $settings = $this->sanitize_settings( $_POST );

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
    private function sanitize_settings( array $data ) : array {

        $clean = [];

        // 🧩 Features
        $clean['features'] = [
            'comment'        => ! empty( $data['feature_comment'] ),
            'new_user'       => ! empty( $data['feature_new_user'] ),
            'ordercompleted' => ! empty( $data['feature_ordercompleted'] ),
        ];

        // 🧩 Messengers (active)
        $clean['messengers'] = isset( $data['messengers'] )
            ? array_map( 'sanitize_key', (array) $data['messengers'] )
            : [];

        // 🧩 Messenger settings
        if ( in_array( 'eitaa', $clean['messengers'], true ) ) {
            $clean['eitaa'] = [
                'token'   => sanitize_text_field( $data['eitaa_token'] ?? '' ),
                'channel' => sanitize_text_field( $data['eitaa_channel'] ?? '' ),
            ];
        }

        // 🧩 User phone field (for New User feature)
        if ( isset( $data['user_phone_field'] ) ) {
            $clean['user_phone_field'] = sanitize_key( $data['user_phone_field'] );
        }

        return $clean;
    }
}
