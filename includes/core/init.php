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
            if(file_exists( MNI_FREE_PATH . 'includes/admin/admin.php' ) ) {
                require_once MNI_FREE_PATH . 'includes/admin/admin.php';
                new MNI_Free_Registry();
            }

            // Wizard (THIS IS THE IMPORTANT PART)
            if(file_exists( MNI_FREE_PATH . 'includes/admin/wizard-controller.php' ) ) {
                require_once MNI_FREE_PATH . 'includes/admin/wizard-controller.php';
                new MNI_Free_Wizard_Controller();
            }
            
            // Settings (THIS IS THE IMPORTANT PART)
            if(file_exists( MNI_FREE_PATH . 'includes/admin/settings-controller.php' ) ) {
                require_once MNI_FREE_PATH . 'includes/admin/settings-controller.php';
                new MNI_Free_Settings_Controller();
            }

            // Settings page (if exists)
            if ( file_exists( MNI_FREE_PATH . 'includes/admin/settings.php' ) ) {
                require_once MNI_FREE_PATH . 'includes/admin/settings.php';
            }
            
            if(file_exists( MNI_FREE_PATH . 'includes/admin/registry.php' ) ) {
                require_once MNI_FREE_PATH . 'includes/admin/registry.php';
                new MNI_Free_Registry();
            }
            
            // messengers manager page (if exists)
            if ( file_exists( MNI_FREE_PATH . 'includes/messengers/manager.php' ) ) {
                require_once MNI_FREE_PATH . 'includes/messengers/manager.php';
            }

        } else {
            // require_once MNI_FREE_PATH . 'includes/frontend/shortcode_contact.php';
            if(file_exists( MNI_FREE_PATH . 'includes/frontend/shortcode_contact.php' ) ) {
                require_once MNI_FREE_PATH . 'includes/frontend/shortcode_contact.php';
                new MNI_Free_Shortcode_Contact();
            }
        }

        // Load feature hooks
        $this->load_features();
    }

    private function load_features() {

        require_once MNI_FREE_PATH . 'includes/features/comment.php';
        mni_free_feature_comment::instance();

        require_once MNI_FREE_PATH . 'includes/features/newuser.php';
        mni_free_newuser::instance();
        

        // load WooCommerce features after loading WooCommerce
        add_action( 'woocommerce_loaded', function() {

            require_once MNI_FREE_PATH . 'includes/features/woocommerce/ordercompleted.php';
            mni_free_feature_ordercompleted::instance();

        });

    }

    // redirect to wizard after activating the plugin
    public function maybe_redirect_to_wizard() {
        if ( get_transient( '_mni_free_activation_redirect' ) ) {
            delete_transient( '_mni_free_activation_redirect' );
            wp_safe_redirect( admin_url( 'admin.php?page=mni_free_wizard' ) );
            exit;
        }
    }
}
