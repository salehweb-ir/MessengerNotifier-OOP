<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class MNI_Free_Settings_Sanitizer {

    public static function sanitize( array $data ) : array {

        $clean = [];

        // 🧩 Messengers
        $clean['messengers'] = isset( $data['messengers'] )
            ? array_values(
                array_map( 'sanitize_key', (array) $data['messengers'] )
              )
            : [];

        // 🧩 Actions
        $clean['actions'] = isset( $data['actions'] )
            ? array_values(
                array_map( 'sanitize_key', (array) $data['actions'] )
              )
            : [];

        // 🧩 Messenger config
        if ( isset( $data['config'] ) && is_array( $data['config'] ) ) {
            foreach ( $data['config'] as $msgr => $conf ) {
                $key = sanitize_key( $msgr );

                $clean['config'][ $key ] = [
                    'token'   => sanitize_text_field( $conf['token'] ?? '' ),
                    'channel' => sanitize_text_field( $conf['channel'] ?? '' ),
                ];
            }
        } else {
            $clean['config'] = [];
        }

        return $clean;
    }
}
