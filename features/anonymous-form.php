<?php
/**
 * Anonymous Form Shortcode
 *
 * Displays a public form for anonymous users to send messages to admin
 * via selected messenger(s) (Eitaa, future Telegram, etc.)
 *
 * @package MNI_FREE\Features
 */

namespace MNI_FREE\Features;

use MNI_FREE\Includes\Messenger_Manager;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Anonymous_Form
 */
class Anonymous_Form {

    /**
     * Initialize shortcode registration.
     */
    public static function init() {
        add_shortcode( 'mni_free_anonymous_form', [ __CLASS__, 'render_form_shortcode' ] );
    }

    /**
     * Render the anonymous message form.
     *
     * @return string
     */
    public static function render_form_shortcode() {
        ob_start();

        if ( isset( $_POST['mni_free_submit'] ) ) {
            self::handle_form_submission();
        }

        include MNI_FREE_PATH . 'templates/default.php';

        return ob_get_clean();
    }

    /**
     * Handle form submission securely.
     */
    private static function handle_form_submission() {
        if (
            ! isset( $_POST['mni_free_nonce'] ) ||
            ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mni_free_nonce'] ) ), 'mni_free_nonce_action' )
        ) {
            wp_die( esc_html__( 'Security check failed.', 'messengernotifier' ) );
        }

        $message = isset( $_POST['message'] )
            ? sanitize_text_field( wp_unslash( $_POST['message'] ) )
            : '';

        if ( empty( $message ) ) {
            echo '<div class="notice notice-error"><p>' .
                 esc_html__( 'Message cannot be empty.', 'messengernotifier' ) .
                 '</p></div>';
            return;
        }

        // Get the messenger manager and send message
        $result = Messenger_Manager::send_message_to_active_messengers( $message, '#Message' );

        if ( $result['success'] ) {
            echo '<div class="notice notice-success is-dismissible"><p>' .
                 esc_html__( 'Message sent successfully!', 'messengernotifier' ) .
                 '</p></div>';
        } else {
            echo '<div class="notice notice-error is-dismissible"><p>' .
                 esc_html__( 'Message sending failed: ', 'messengernotifier' ) .
                 esc_html( $result['error'] ?? 'Unknown error' ) .
                 '</p></div>';
        }
    }
}

// Initialize the shortcode
Anonymous_Form::init();
