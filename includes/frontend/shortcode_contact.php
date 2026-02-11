<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class MNI_Free_Shortcode_Contact {

    public function __construct() {
        add_shortcode( 'mni_contact_form', [ $this, 'render' ] );
    }

    public function render( $atts = [], $content = null ) {

        $settings = get_option( 'mni_free_settings', [] );

        $template = $settings['contact_page']['template'] ?? 'default';

        return MNI_Free_Template_Loader::load( $template );
    }
}
