<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class mni_free_deactivator {
    public static function deactivate() {
        flush_rewrite_rules();
    }
}
