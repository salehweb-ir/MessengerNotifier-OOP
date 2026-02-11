<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class MNI_Free_Template_Loader {
  
  const MNI_TEMPLATE_PATH = MNI_FREE_PATH . 'views/frontend/';

    public static function load( string $template ): string {

        $template = sanitize_key( $template );

        $file = self::MNI_TEMPLATE_PATH . $template . '.php';

        // اگر قالب وجود نداشت → fallback
        if ( ! file_exists( $file ) ) {
            $file = self::MNI_TEMPLATE_PATH . 'mni_default.php';
        }

        ob_start();
        include $file;
        return ob_get_clean();
    }
}
