<?php
if ( ! defined( 'ABSPATH' ) ) exit;

require_once MNI_FREE_PATH . 'includes/traits/singleton.php';

class mni_free_wizard {
    use mni_singleton;

    private $messengers = [
        'eitaa' => ['label' => 'Eitaa'],
        'bale' => ['label' => 'Bale'],
        'soroush' => ['label' => 'Soroush'],
        'gap' => ['label' => 'Gap'],
        'telegram' => ['label' => 'Telegram'],
        'rubika' => ['label' => 'Rubika'],
        'whatsapp' => ['label' => 'WhatsApp']
    ];

    private $actions = [
        'new_comment' => 'New Comment',
        'new_user'    => 'User Registration',
        'order_completed' => 'WooCommerce Order Completed'
    ];

    private function __construct() {
        add_action( 'admin_menu', [ $this, 'add_wizard_page' ] );
        add_action( 'admin_post_mni_free_save_wizard', [ $this, 'save' ] );
    }

    public function add_wizard_page() {
        add_menu_page(
            'Messenger Notifier Setup',
            'Messenger Wizard',
            'manage_options',
            'mni_free_wizard'
        );
    }

    public function get_messengers() {
        return $this->messengers;
    }

    public function get_active_messengers() {
        return get_option( 'mni_free_active_messengers', ['eitaa'] );
    }

    public function get_available_actions() {
        return $this->actions;
    }

    public function is_action_enabled( $action ) {
        $enabled = get_option( 'mni_free_enabled_actions', [] );
        return in_array( $action, $enabled );
    }

    public function get_messenger_settings( $msgr ) {
        $all = get_option( 'mni_free_messenger_settings', [] );
        return $all[$msgr] ?? [];
    }

    public function render() {
        require MNI_FREE_PATH . 'views/admin/wizard.php';
    }

    public function save() {
        if ( ! check_admin_referer( 'mni_free_wizard_save', 'mni_wizard_nonce' ) ) {
            wp_die('Security check failed');
        }

        update_option( 'mni_free_active_messengers', $_POST['active_messengers'] ?? ['eitaa'] );
        update_option( 'mni_free_enabled_actions', $_POST['enabled_actions'] ?? [] );
        update_option( 'mni_free_messenger_settings', $_POST['messenger_settings'] ?? [] );

        wp_safe_redirect( admin_url('admin.php?page=mni_free_wizard&saved=1') );
        exit;
    }

}
