<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class mni_i18n {
    public static function load_textdomain() {
        load_plugin_textdomain(
            'messengernotifier',
            false,
            dirname( plugin_basename( MNI_FREE_PATH . 'messengernotifier.php' ) ) . '/languages'
        );
    }
}
