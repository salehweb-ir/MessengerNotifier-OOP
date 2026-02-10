<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class MNI_Free_Settings_Service {

    private array $settings;

    public function __construct() {
        $this->settings = (array) get_option( 'mni_free_settings', [] );
    }

    public function get_settings(): array {
        return $this->settings;
    }

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

    public function get_contact_page(): array {
        return $this->settings['contact_page'] ?? [
            'title'    => '',
            'slug'     => '',
            'template' => 'default',
        ];
    }

    public function get_config( string $messenger ): array {
        return $this->settings['config'][ $messenger ] ?? [];
    }
}
