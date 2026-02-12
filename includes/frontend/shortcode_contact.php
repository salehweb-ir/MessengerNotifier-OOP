<?php
if ( ! defined( 'ABSPATH' ) ) exit;

  require_once MNI_FREE_PATH . 'includes/core/registry.php';

class MNI_Free_Shortcode_Contact {

    public function __construct() {
        add_shortcode( 'mni_contact_form', [ $this, 'render' ] );
    }

    public function render( $atts = [], $content = null ) {

        $settings = get_option( 'mni_free_settings', [] );
        
        $template = $settings['contact_page']['template'] ?? 'default';

        $template = sanitize_key( $template );

        // Validate template
        $available = MNI_Free_Registry::page_templates();

        if ( ! isset( $available[ $template ] ) ) {
            $template = 'dafault';
        }

        $this->enqueue_assets( $template );

        ob_start();
        ?>

        <div class="mni-contact mni-template-<?php echo esc_attr( $template ); ?>">
            <form method="post" class="mni-contact-form">

                <?php wp_nonce_field( 'mni_send_message', 'mni_nonce' ); ?>
                
                <label for="mni_message" id="mni-contact-form-title" class="mni-title"> <?php  esc_html_e(
                'Tell me whatever you want, I miss you.', 'messengernotifier');
                ?></label>

                <textarea name="mni_message" required></textarea>

                <button type="submit">
                    <?php esc_html_e( 'Send', 'messengernotifier' ); ?>
                </button>

            </form>
        </div>

        <?php
        return ob_get_clean();
    }

    private function enqueue_assets( string $template ) {

        // 1️⃣ Base CSS
        wp_enqueue_style(
            'mni-base',
            MNI_FREE_URL . 'views/frontend/css/default.css',
            [],
            MNI_FREE_VERSION
        );

        // 2️⃣ Template CSS
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
