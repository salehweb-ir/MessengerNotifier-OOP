<?php
if ( ! defined( 'ABSPATH' ) ) exit;

require_once MNI_FREE_PATH . 'includes/traits/sanitizer.php';

class MNI_Free_Wizard_Controller {

    public function __construct() {
        add_action( 'admin_post_mni_free_save_wizard', [ $this, 'save' ] );
    }

    public function save() {

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( 'Access denied' );
        }

        check_admin_referer( 'mni_free_wizard_save' );

        $raw_settings = $_POST['settings'] ?? [];

        $settings = ( new MNI_Free_Sanitizer() )->sanitize_settings( $raw_settings );

        /*
         * -------------------------------------------------
         * Create Anonymous Contact Page (if needed)
         * -------------------------------------------------
         */
        if ( ! empty( $settings['contact_page'] ) ) {

            $page_id = $this->create_contact_page( $settings['contact_page'] );

            if ( $page_id ) {
                $settings['contact_page']['id'] = $page_id;
            }
        }

        update_option( 'mni_free_settings', $settings );
        update_option( 'mni_free_wizard_completed', 1 );

        wp_redirect( admin_url( 'admin.php?page=mni_free_settings&saved=1' ) );
        exit;
    }

    /**
     * Create the anonymous contact page if it doesn't exist.
     *
     * @param array $contact_settings
     * @return int Page ID
     */
    private function create_contact_page( $contact_settings ) {

        $title    = sanitize_text_field( $contact_settings['title'] ?? 'ناشناس' );
        $slug     = sanitize_title( $contact_settings['slug'] ?? 'nashenas' );
        $template = sanitize_text_field( $contact_settings['template'] ?? '' );

        // 1️⃣ اگر صفحه‌ای با این اسلاگ قبلاً وجود دارد، همان را استفاده کن
        $existing = get_posts([
            'name'        => $slug,
            'post_type'   => 'page',
            'post_status' => 'publish',
            'numberposts' => 1,
            'fields'      => 'ids',
        ]);

        if ( ! empty( $existing ) ) {
            return (int) $existing[0];
        }

        // 2️⃣ ایجاد صفحه جدید
        $page_id = wp_insert_post([
            'post_title'   => $title,
            'post_name'    => $slug,
            'post_content' => '',
            'post_status'  => 'publish',
            'post_type'    => 'page',
        ]);

        if ( is_wp_error( $page_id ) ) {
            return 0;
        }

        // 3️⃣ اگر قالب انتخاب شده باشد، روی صفحه ست شود
        if ( $template ) {
            update_post_meta( $page_id, '_wp_page_template', $template );
        }

        return (int) $page_id;
    }
}