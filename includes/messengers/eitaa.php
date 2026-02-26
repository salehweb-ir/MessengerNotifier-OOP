<?php
namespace MessengerNotifier\Messengers;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once MNI_FREE_PATH . 'includes/traits/singleton.php';

class Eitaa implements MessengerInterface {

    use \mni_singleton;

    private $token;
    private $channel;

    public function __construct( array $config = [] ) {

        if ( ! empty( $config ) ) {
            // For testing connection
            $this->token   = $config['token'] ?? '';
            $this->channel = $config['channel'] ?? '';
        } else {
            // Normal mode (from settings)
            $settings = get_option('mni_free_settings', []);

            $this->token   = $settings['config']['eitaa']['token'] ?? '';
            $this->channel = $settings['config']['eitaa']['channel'] ?? '';
        }
    }


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
     * Send message to Eitaa
     */
    public function send( string $message, string $type = '' ): array {

        $token      = $this->token;
        $channel_id = $this->channel;

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
