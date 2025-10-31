<?php
/**
 * Eitaa Messenger API Handler
 *
 * @package MessengerNotifier
 * @subpackage API
 */

namespace MNI_FREE\API;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Prevent direct access
}

/**
 * Class Eitaa
 *
 * Handles all message-sending requests to the Eitaa Messenger API.
 */
class Eitaa {

    /**
     * API endpoint base.
     *
     * @var string
     */
    private $api_base = 'https://eitaayar.ir/api/';

    /**
     * Eitaa token
     *
     * @var string
     */
    private $token;

    /**
     * Eitaa channel ID
     *
     * @var string|int
     */
    private $channel_id;

    /**
     * Constructor.
     *
     * @param string $token Eitaa API token.
     * @param string|int $channel_id Eitaa Channel ID.
     */
    public function __construct( $token, $channel_id ) {
        $this->token      = sanitize_text_field( $token );
        $this->channel_id = sanitize_text_field( $channel_id );
    }

    /**
     * Send a text message to Eitaa.
     *
     * @param string $message  Message text.
     * @param string $hashtag  Type of message (e.g. #order, #comment).
     * @return array {
     *     @type bool   $success Whether message sent successfully.
     *     @type string $error   Error message or response.
     * }
     */
    public function send_message( $message, $hashtag = '' ) {
        $url = trailingslashit( $this->api_base . $this->token ) . 'sendMessage';

        $post_fields = array(
            'chat_id'    => $this->channel_id,
            'text'       => $message . "\n\n" . $hashtag,
            'parse_mode' => 'HTML',
        );

        $args = array(
            'body'      => $post_fields,
            'timeout'   => 45,
            'headers'   => array(
                'Content-Type' => 'application/x-www-form-urlencoded',
            ),
        );

        $response = wp_remote_post( $url, $args );

        if ( is_wp_error( $response ) ) {
            return [
                'success' => false,
                'error'   => $response->get_error_message(),
            ];
        }

        $http_code = wp_remote_retrieve_response_code( $response );
        $body      = wp_remote_retrieve_body( $response );

        return [
            'success' => ( 200 === $http_code ),
            'error'   => $body,
        ];
    }

    /**
     * Test the API connection by sending a test message.
     *
     * @return array
     */
    public function test_connection() {
        $test_message = __( '✅ Test message from Messenger Notifier', 'messengernotifier' );
        return $this->send_message( $test_message, '#test' );
    }

    /**
     * Get Messenger Name.
     *
     * @return string
     */
    public function get_name() {
        return 'Eitaa';
    }
}
