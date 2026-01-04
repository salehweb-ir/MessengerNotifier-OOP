<?php
namespace MessengerNotifier\Messengers;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once MNI_FREE_PATH . 'includes/traits/singleton.php';

class Eitaa implements MessengerInterface {

    use \mni_singleton;

    /**
     * Messenger unique ID
     */
    public function get_id(): string {
        return 'eitaa';
    }

    /**
     * Human-readable name
     */
    public function get_name(): string {
        return 'Eitaa';
    }

    /**
     * Check if Eitaa is configured correctly
     */
        public function is_configured(): bool {

            $settings = get_option( 'mni_free_settings', [] );

            return (
                isset( $settings['eitaa']['token'], $settings['eitaa']['channel'] )
                && $settings['eitaa']['token'] !== ''
                && $settings['eitaa']['channel'] !== ''
            );
        }

    /**
     * Send message to Eitaa
     */
    public function send( string $message, string $type = '' ): array {

        if ( ! $this->is_configured() ) {
            return [
                'success' => false,
                'error'   => 'Eitaa is not configured.',
            ];
        }

        $settings = get_option( 'mni_free_settings', [] );

        $token      = $settings['eitaa']['token'];
        $channel_id = $settings['eitaa']['channel'];

        $url = "https://eitaayar.ir/api/{$token}/sendMessage";

        $body = [
            'chat_id'    => $channel_id,
            'text'       => $message . ( $type ? "\n\n#" . $type : '' ),
            'parse_mode' => 'HTML',
        ];

        $response = wp_remote_post( $url, [
            'body'    => $body,
            'timeout' => 45,
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
        ] );

        if ( is_wp_error( $response ) ) {
            return [
                'success' => false,
                'error'   => $response->get_error_message(),
            ];
        }

        $code = wp_remote_retrieve_response_code( $response );

        return [
            'success' => $code === 200,
            'code'    => $code,
            'body'    => wp_remote_retrieve_body( $response ),
        ];
    }
}
