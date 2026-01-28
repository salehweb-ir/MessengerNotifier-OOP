<?php
defined( 'ABSPATH' ) || exit;

class mni_free_assets {

    public function __construct() {
        add_action( 'admin_enqueue_scripts', [ $this, 'admin_assets' ] );
        add_action( 'wp_enqueue_scripts', [ $this, 'frontend_assets' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'wizard_assets' ] );
    }

    /**
     * Admin scripts & styles
     */
    public function admin_assets( $hook ) {

        // Load ONLY on your plugin pages
        if ( strpos( $hook, 'mni_free' ) === false ) {
            return;
        }

        wp_enqueue_script(
            'mni-free-admin',
            MNI_FREE_URL . 'assets/js/admin.js',
            [ 'jquery' ],
            MNI_FREE_VERSION,
            true
        );

        wp_enqueue_style(
            'mni-free-admin',
            MNI_FREE_URL . 'assets/css/admin.css',
            [],
            MNI_FREE_VERSION
        );
    }

    /**
     * Frontend scripts
     */
    public function frontend_assets() {

        wp_enqueue_script(
            'mni-free-frontend',
            MNI_FREE_URL . 'assets/js/frontend.js',
            [],
            MNI_FREE_VERSION,
            true
        );
    }

    /**
     * Wizard scripts & styles
     */
    public function wizard_assets( $hook ) {

        // Load ONLY on your plugin pages
        if ( strpos( $hook, 'mni_free' ) === false ) {
            return;
        }

        wp_enqueue_script(
            'mni-free-wizard',
            MNI_FREE_URL . 'assets/js/wizard.js',
            [ 'jquery' ],
            MNI_FREE_VERSION,
            true
        );

        wp_enqueue_style(
            'mni-free-wizard',
            MNI_FREE_URL . 'assets/css/wizard.css',
            [],
            MNI_FREE_VERSION
        );
    }
}
