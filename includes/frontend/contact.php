<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Messenger Notifier Free – Frontend Contact Page
 */

require_once MNI_FREE_PATH . 'includes/traits/singleton.php';

class mni_free_contact {
    use mni_singleton;

    private function __construct() {
        // Register shortcode
        add_shortcode( 'mni_free_contact_form', [ $this, 'render_contact_form' ] );

        // Handle form submission
        add_action( 'init', [ $this, 'handle_form_submission' ] );
    }

    /**
     * Render contact form shortcode.
     */
    public function render_contact_form() {
        ob_start();

        $template = MNI_FREE_PATH . 'views/frontend/default.php';

        if ( file_exists( $template ) ) {
            include $template;
        } else {
            echo '<form method="post">';
            echo '<textarea name="mni_free_message" rows="5" style="width:100%;"></textarea>';
            wp_nonce_field( 'mni_free_send_message', 'mni_free_nonce' );
            echo '<button type="submit" name="mni_free_submit">' . esc_html__( 'Send', 'messengernotifier' ) . '</button>';
            echo '</form>';
        }

        return ob_get_clean();
    }

    /**
     * Handle form submission.
     */
    public function handle_form_submission() {
        if ( isset( $_POST['mni_free_submit'] ) ) {
            if ( ! isset( $_POST['mni_free_nonce'] ) || ! wp_verify_nonce( $_POST['mni_free_nonce'], 'mni_free_send_message' ) ) {
                wp_die( esc_html__( 'Security check failed.', 'messengernotifier' ) );
            }

            $message = sanitize_textarea_field( $_POST['mni_free_message'] ?? '' );

            if ( ! empty( $message ) ) {
                // Send to messenger APIs (placeholder)
                do_action( 'mni_free_send_to_messengers', $message );

                wp_safe_redirect( add_query_arg( 'mni_free_sent', '1', wp_get_referer() ) );
                exit;
            }
        }
    }
}
