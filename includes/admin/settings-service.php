<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class MNI_Free_Settings_Service {

    private array $settings;

    public function __construct() {
        $this->settings = (array)mni_free_init::instance()->mni_get_settings();

    }

    /* -------------------------------------------------
     * Messengers
     * ------------------------------------------------- */
    public function get_messengers(): array {

        $all     = MNI_Free_Registry::messengers();
        $active  = $this->settings['messengers'] ?? [];

        foreach ( $all as $id => &$m ) {
            if ( is_array( $m ) ) {
                $m['active'] = in_array( $id, $active, true );
            }
        }

        return $all;
    }

    /* -------------------------------------------------
     * Actions
     * ------------------------------------------------- */
    public function get_actions(): array {

        $all     = MNI_Free_Registry::actions();
        $active  = $this->settings['actions'] ?? [];

        foreach ( $all as $id => &$a ) {
            if ( is_array( $a ) ) {
                $a['active'] = in_array( $id, $active, true );
            }
        }

        return $all;
    }

    /* -------------------------------------------------
     * Contact Page
     * ------------------------------------------------- */
    public function get_contact_page(): array {

        $page_id = $this->settings['contact_page']['id'] ?? 0;

        if ( ! $page_id ) {
            return [
                'id'       => 0,
                'title'    => '',
                'slug'     => '',
                'status'   => '',
                'template' => '',
            ];
        }

        $post = get_post( $page_id );

        if ( ! $post || $post->post_type !== 'page' ) {
            return [
                'id'       => 0,
                'title'    => '',
                'slug'     => '',
                'status'   => '',
                'template' => '',
            ];
        }

        return [
            'id'       => $page_id,
            'title'    => $post->post_title,
            'slug'     => $post->post_name,
            'status'   => $post->post_status,
            'template' => get_page_template_slug( $page_id ),
        ];
    }

    /* -------------------------------------------------
     * Messenger Config
     * ------------------------------------------------- */
    public function get_config( string $messenger ): array {

        return $this->settings['config'][ $messenger ] ?? [];
    }
}
