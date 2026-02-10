<?php
if ( ! defined( 'ABSPATH' ) ) exit;

require_once MNI_FREE_PATH . 'includes/frontend/contact-render.php';

add_shortcode( 'mni_anonymous', function () {
    return MNI_Anonymous_Renderer::render();
});
