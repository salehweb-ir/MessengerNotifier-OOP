<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once MNI_FREE_PATH . 'includes/traits/singleton.php';

class mni_free_wizard {
    use mni_singleton;

    private $messengers = [
        'eitaa'   => [ 'label' => 'Eitaa' ],
        'telegram'=> [ 'label' => 'Telegram' ],
        'bale'    => [ 'label' => 'Bale' ],
        'igap'    => [ 'label' => 'iGap' ],
        'soroush' => [ 'label' => 'Soroush' ],
    ];

    private $actions = [
        'new_comment'   => 'New Comment',
        'new_user'      => 'New User Registered',
        'wc_completed'  => 'WooCommerce Order Completed',
    ];

    private function __construct() {
        add_action( 'admin_menu', [ $this, 'add_wizard_page' ] );
        add_action( 'admin_post_mni_free_save_wizard', [ $this, 'save' ] );
    }

    public function add_wizard_page() {
        add_menu_page(
            __( 'Messenger Notifier Setup', 'messengernotifier' ),
            __( 'Messenger Notifier', 'messengernotifier' ),
            'manage_options',
            'mni_free_wizard',
            [ $this, 'render' ],
            'dashicons-megaphone',
            65
        );
    }

    public function render() {
        // ensure view exists
        $view = MNI_FREE_PATH . 'views/admin/wizard.php';
        if ( file_exists( $view ) ) {
            require $view;
        } else {
            echo '<div class="wrap"><h2>' . esc_html__( 'Wizard view missing', 'messengernotifier' ) . '</h2></div>';
        }
    }

    /**
     * Return available messengers (all)
     */
    public function get_messengers() {
        return $this->messengers;
    }

    /**
     * Return active messengers from DB (fallback to eitaa)
     */
    public function get_active_messengers() {
        return get_option( 'mni_free_active_messengers', [ 'eitaa' ] );
    }

    /**
     * Return available actions
     */
    public function get_actions() {
        return $this->actions;
    }

    /**
     * Get saved messenger settings array from DB
     * returns e.g. [ 'eitaa' => ['token'=>'','channel'=>''], ... ]
     */
    public function get_messenger_settings_from_db() {
        return (array) get_option( 'mni_free_messenger_settings', [] );
    }

    /**
     * Save handler (admin_post)
     */
    public function save() {
        // Capability
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'Permission denied.', 'messengernotifier' ) );
        }

        // Nonce check
        if ( ! isset( $_POST['mni_wizard_nonce'] ) || ! check_admin_referer( 'mni_free_wizard_save', 'mni_wizard_nonce' ) ) {
            wp_die( esc_html__( 'Security check failed.', 'messengernotifier' ) );
        }

        // Read selected messengers from hidden field (JSON)
        $selected = [];
        if ( ! empty( $_POST['mni_selected_messengers'] ) ) {
            $decoded = json_decode( wp_unslash( $_POST['mni_selected_messengers'] ), true );
            if ( is_array( $decoded ) ) {
                // sanitize values and keep only known messengers
                foreach ( $decoded as $m ) {
                    $m = sanitize_key( $m );
                    if ( isset( $this->messengers[ $m ] ) ) {
                        $selected[] = $m;
                    }
                }
            }
        }

        // fallback: if nothing selected, try POST active_messengers[] (older form) or DB default
        if ( empty( $selected ) ) {
            if ( ! empty( $_POST['active_messengers'] ) && is_array( $_POST['active_messengers'] ) ) {
                $tmp = array_map( 'sanitize_key', $_POST['active_messengers'] );
                foreach ( $tmp as $m ) {
                    if ( isset( $this->messengers[ $m ] ) ) {
                        $selected[] = $m;
                    }
                }
            }
        }

        if ( empty( $selected ) ) {
            // ensure at least one
            $selected = array( 'eitaa' );
        }

        // Save active messengers to option
        update_option( 'mni_free_active_messengers', $selected );

        // Actions
        $enabled_actions = array();
        if ( ! empty( $_POST['enabled_actions'] ) && is_array( $_POST['enabled_actions'] ) ) {
            foreach ( $_POST['enabled_actions'] as $act ) {
                $act = sanitize_key( $act );
                if ( isset( $this->actions[ $act ] ) ) {
                    $enabled_actions[] = $act;
                }
            }
        }
        update_option( 'mni_free_enabled_actions', $enabled_actions );

        // Messenger settings (nested array messenger_settings[msgr][token|channel|test])
        $raw_settings = ! empty( $_POST['messenger_settings'] ) && is_array( $_POST['messenger_settings'] ) ? $_POST['messenger_settings'] : array();

        $clean_settings = array();
        foreach ( $raw_settings as $msgr => $vals ) {
            $msgr_key = sanitize_key( $msgr );
            if ( ! isset( $this->messengers[ $msgr_key ] ) ) {
                continue;
            }
            $clean_settings[ $msgr_key ] = array(
                'token'   => isset( $vals['token'] ) ? sanitize_text_field( wp_unslash( $vals['token'] ) ) : '',
                'channel' => isset( $vals['channel'] ) ? sanitize_text_field( wp_unslash( $vals['channel'] ) ) : '',
                'test'    => isset( $vals['test'] ) ? sanitize_textarea_field( wp_unslash( $vals['test'] ) ) : '',
            );
        }

        // Persist messenger settings
        update_option( 'mni_free_messenger_settings', $clean_settings );

        // Redirect back with success
        $redirect = add_query_arg( 'saved', '1', admin_url( 'admin.php?page=mni_free_wizard' ) );
        wp_safe_redirect( $redirect );
        exit;
    }
}
