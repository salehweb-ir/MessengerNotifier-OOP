<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class mni_free_wizard {

    use mni_singleton;

    private function __construct() {
        add_action( 'admin_menu', [ $this, 'register_page' ] );
        add_action( 'admin_post_mni_free_save_wizard', [ $this, 'save_wizard' ] );
    }

    public function register_page() {
        add_submenu_page(
            null,
            __( 'Messenger Notifier Wizard', 'messengernotifier' ),
            __( 'Messenger Notifier Wizard', 'messengernotifier' ),
            'manage_options',
            'mni_free_wizard'/* ,
            [ $this, 'render' ] */
        );
    }

    public function render() {
        require MNI_FREE_PATH . 'views/admin/wizard.php';
    }

    public function get_settings() {
        return [
            'messengers'    => get_option( 'mni_free_selected_messengers', [] ),
            'contact_title' => get_option( 'mni_free_contact_title', '' ),
            'contact_slug'  => get_option( 'mni_free_contact_slug', '' ),
            'actions'       => get_option( 'mni_free_actions', [] ),
            'eitaa'         => [
                'token'   => get_option( 'mni_free_eitaa_token', '' ),
                'channel' => get_option( 'mni_free_eitaa_channel', '' ),
            ],
        ];
    }

    public function get_messengers() {
        return [
            'eitaa' => [
                'label' => __( 'Eitaa', 'messengernotifier' ),
            ],
        ];
    }

    public function get_actions() {
        return [
            'new_comment'   => __( 'New Comment', 'messengernotifier' ),
            'new_user'      => __( 'New User Registered', 'messengernotifier' ),
            'wc_completed'  => __( 'WooCommerce Order Completed', 'messengernotifier' ),
        ];
    }

    public function save_wizard() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( 'Permission denied.', 'messengernotifier' ) );
        }

        if (
            ! isset( $_POST['mni_free_wizard_nonce'] ) ||
            ! check_admin_referer( 'mni_free_wizard_action', 'mni_free_wizard_nonce' )
        ) {
            wp_die( __( 'Security check failed.', 'messengernotifier' ) );
        }

        // --- Sanitize and save settings ---
        $messengers = isset( $_POST['messengers'] ) ?
            array_map( 'sanitize_text_field', $_POST['messengers'] ) : [];

        update_option( 'mni_free_selected_messengers', $messengers );

        update_option( 'mni_free_contact_title', sanitize_text_field( $_POST['contact_title'] ?? '' ) );
        update_option( 'mni_free_contact_slug', sanitize_title( $_POST['contact_slug'] ?? '' ) );

        $actions = isset( $_POST['actions'] ) ?
            array_map( 'sanitize_text_field', $_POST['actions'] ) : [];
        update_option( 'mni_free_actions', $actions );

        update_option( 'mni_free_eitaa_token', sanitize_text_field( $_POST['eitaa_token'] ?? '' ) );
        update_option( 'mni_free_eitaa_channel', sanitize_text_field( $_POST['eitaa_channel'] ?? '' ) );

        // Redirect back to wizard with success flag
        $redirect = add_query_arg(
            'saved',
            '1',
            admin_url( 'admin.php?page=mni_free_wizard' )
        );

        wp_safe_redirect( $redirect );
        exit;
    }
}
