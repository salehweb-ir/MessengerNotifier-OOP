<?php
if ( ! defined( 'ABSPATH' ) ) exit;

require_once MNI_FREE_PATH . 'includes/core/registry.php';
require_once MNI_FREE_PATH . 'includes/messengers/manager.php';

class MNI_Free_Shortcode_Contact {

    public function __construct() {
        add_shortcode( 'mni_contact_form', [ $this, 'render' ] );

        // Proccessing the form before output
        add_action( 'init', [ $this, 'handle_form_submit' ] );
    }

    public function render() {

        $settings = mni_free_init::instance()->mni_get_settings();

        $template = $settings['contact_page']['template'] ?? 'default';
        $template = sanitize_key( $template );

        $available = MNI_Free_Registry::page_templates();
        if ( ! isset( $available[ $template ] ) ) {
            $template = 'default';
        }

        $this->enqueue_assets( $template );

        ob_start();

        // Successful message
        if ( isset( $_GET['mni_sent'] ) ) {
            echo '<div class="mni-success">Message sent successfully.</div>';
        }
        ?>

        <div class="mni-contact mni-template-<?php echo esc_attr( $template ); ?>">
            <form method="post" class="mni-contact-form">

                <?php wp_nonce_field( 'mni_send_message', 'mni_nonce' ); ?>

                <label class="mni-title">
                    <?php esc_html_e( 'Tell me whatever you want, I miss you.', 'messengernotifier'); ?>
                </label>

                <textarea name="mni_message" required></textarea>

                <button type="submit">
                    <?php esc_html_e( 'Send', 'messengernotifier' ); ?>
                </button>

            </form>
        </div>

        <?php
        return ob_get_clean();
    }

    /**
     * Handle form submit safely (before output)
     */
    public function handle_form_submit(): void {

        if ( $_SERVER['REQUEST_METHOD'] !== 'POST' ) {
            return;
        }

        if ( empty( $_POST['mni_message'] ) ) {
            return;
        }

        if (
            empty( $_POST['mni_nonce'] ) ||
            ! wp_verify_nonce( $_POST['mni_nonce'], 'mni_send_message' )
        ) {
            return;
        }

        $message = sanitize_textarea_field( $_POST['mni_message'] );

        if ( empty( $message ) ) {
            return;
        }

        // Send to Manager
        $manager = mni_free_messenger_manager::instance();
        $manager->send( $message, 'contact_form' );

        // Redirect safely
        wp_safe_redirect( add_query_arg( 'mni_sent', '1', wp_get_referer() ) );
        exit;
    }

    private function enqueue_assets( string $template ) {

        wp_enqueue_style(
            'mni-base',
            MNI_FREE_URL . 'views/frontend/css/default.css',
            [],
            MNI_FREE_VERSION
        );

        $file_path = MNI_FREE_PATH . 'views/frontend/css/' . $template . '.css';

        if ( file_exists( $file_path ) ) {
            wp_enqueue_style(
                'mni-template-' . $template,
                MNI_FREE_URL . 'views/frontend/css/' . $template . '.css',
                [ 'mni-base' ],
                MNI_FREE_VERSION
            );
        }
    }
}
