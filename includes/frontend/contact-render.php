<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class MNI_Anonymous_Renderer {

    public static function render() : string {

        $settings = get_option( 'mni_free_settings', [] );
        error_log(print_r($settings,true));
        $template = $settings['config']['contact_page']['template'] ?? 'default';

        $file = MNI_FREE_PATH . "views/frontend/{$template}.php";

        if ( ! file_exists( $file ) ) {
            return '<p>' . esc_html__( 'Template not found.', 'messengernotifier' ) . '</p>';
        }

        ob_start();
        include $file;
        return ob_get_clean();
    }
}
