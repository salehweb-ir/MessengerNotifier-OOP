<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class mni_free_activator {
    public static function activate() {
        // Default options
        add_option( 'mni_free_settings', ['messengers' => ['eitaa', 'bale']] );

        add_option( 'mni_free_settings', ['actions' => ['comment', 'newuser', 'ordercompleted']] );

        // Trigger redirect
        set_transient( '_mni_free_activation_redirect', true, 30 );

        // Flush rewrite rules (if CPT used)
        flush_rewrite_rules();
    }
}
