<?php

if ( ! defined('ABSPATH') ) exit;

class mni_free_wizard {

    use mni_singleton;

    private $option_key = 'mni_free_settings';

    public function init() {

        // Load view
        add_action('admin_menu', [ $this, 'register_wizard_page' ]);

        // Handle form submit
        add_action('admin_post_mni_free_save_wizard', [ $this, 'save_wizard' ]);
    }


    /** ------------------------------------------------------------------
     * WIZARD PAGE
     * ------------------------------------------------------------------*/
    public function register_wizard_page() {

        add_menu_page(
            __('Messenger Notifier Wizard', 'messengernotifier'),
            __('Messenger Wizard', 'messengernotifier'),
            'manage_options',
            'mni-free-wizard',
            [ $this, 'render_wizard_page' ],
            'dashicons-format-status',
            2
        );
    }


    public function render_wizard_page() {

        $settings = get_option($this->option_key, []);

        require MNI_FREE_PATH . 'views/admin/wizard.php';
    }


    /** ------------------------------------------------------------------
     * FORM SAVE LOGIC
     * ------------------------------------------------------------------*/
    public function save_wizard() {

        // Nonce check
        if ( ! isset($_POST['_wpnonce']) ||
             ! wp_verify_nonce($_POST['_wpnonce'], 'mni_free_wizard_nonce') ) {
            wp_die('Security check failed.');
        }

        // Only admins
        if ( ! current_user_can('manage_options') ) {
            wp_die('Permission denied.');
        }

        // Sanitize data
        $data = [];

        // Messengers
        $data['messengers'] = isset($_POST['messengers']) ? array_map('sanitize_text_field', $_POST['messengers']) : [];

        // Contact page settings
        $data['contact_title'] = sanitize_text_field($_POST['contact_title'] ?? '');
        $data['contact_slug']  = sanitize_title($_POST['contact_slug'] ?? '');

        // Enabled actions
        $data['actions'] = isset($_POST['actions']) ? array_map('sanitize_text_field', $_POST['actions']) : [];

        // Messenger-specific settings
        $data['eitaa'] = [
            'token'   => sanitize_text_field($_POST['eitaa_token'] ?? ''),
            'channel' => sanitize_text_field($_POST['eitaa_channel'] ?? ''),
        ];

        // Save
        update_option($this->option_key, $data);

        // Redirect with success flag
        wp_redirect(
            add_query_arg(
                ['page' => 'mni-free-wizard', 'saved' => '1'],
                admin_url('admin.php')
            )
        );
        exit;
    }


    /** Utility for messengers */
    public function get_messengers() {
        return [
            'eitaa' => [
                'title' => __('Eitaa', 'messengernotifier'),
                'help'  => 'https://help.eitaa.com/',
            ],
        ];
    }

    /** Actions list */
    public function get_actions() {
        return [
            'comment' => __('New Comment', 'messengernotifier'),
            'user'    => __('New User Registered', 'messengernotifier'),
            'order'   => __('WooCommerce Order Completed', 'messengernotifier'),
        ];
    }
}
