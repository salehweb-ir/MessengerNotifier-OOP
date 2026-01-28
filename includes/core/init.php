<?php
if ( ! defined( 'ABSPATH' ) ) exit;

require_once MNI_FREE_PATH . 'includes/traits/singleton.php';

class mni_free_init {
    use mni_singleton;

    private function __construct() {
        // Load translations
        require_once MNI_FREE_PATH . 'includes/core/i18n.php';
        mni_i18n::load_textdomain();

        // Redirect after activation
        add_action( 'admin_init', [ $this, 'maybe_redirect_to_wizard' ] );

        // Load admin or frontend logic
        if ( is_admin() ) {
            require_once MNI_FREE_PATH . 'includes/admin/admin.php';
            mni_free_admin::instance();

            // Settings page (if exists)
            if ( file_exists( MNI_FREE_PATH . 'includes/admin/settings.php' ) ) {
                require_once MNI_FREE_PATH . 'includes/admin/settings.php';
            }
            
            // Settings page (if exists)
            if ( file_exists( MNI_FREE_PATH . 'includes/messengers/manager.php' ) ) {
                require_once MNI_FREE_PATH . 'includes/messengers/manager.php';
            }
            
            // Settings page (if exists)
            if ( file_exists( MNI_FREE_PATH . 'includes/core/assets.php' ) ) {
                require_once MNI_FREE_PATH . 'includes/core/assets.php';
                new mni_free_assets();
            }

        } else {
            require_once MNI_FREE_PATH . 'includes/frontend/contact.php';
            mni_free_contact::instance();
        }

        // Load feature hooks
        $this->load_features();
    }

    private function load_features() {
        require_once MNI_FREE_PATH . 'includes/features/comment.php';
        mni_free_feature_comment::instance();

        require_once MNI_FREE_PATH . 'includes/features/newuser.php';
        
        require_once MNI_FREE_PATH . 'includes/features/woocommerce/ordercompleted.php';
        mni_free_feature_ordercompleted::instance();

    }

    public function maybe_redirect_to_wizard() {
        if ( get_transient( '_mni_free_activation_redirect' ) ) {
            delete_transient( '_mni_free_activation_redirect' );
            wp_safe_redirect( admin_url( 'admin.php?page=mni_free_wizard' ) );
            exit;
        }
    }
}
