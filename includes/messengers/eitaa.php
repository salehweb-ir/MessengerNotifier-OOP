<?php
namespace MessengerNotifier\Messengers;

if (!defined('ABSPATH')) {
    exit;
}

class Eitaa implements MessengerInterface
{
    private $token;
    private $channel_id;

    public function __construct($settings = [])
    {
        $this->token      = isset($settings['token']) ? sanitize_text_field($settings['token']) : '';
        $this->channel_id = isset($settings['channel_id']) ? sanitize_text_field($settings['channel_id']) : '';
    }

    /**
     * Send a text message to Eitaa API
     */
    public function send_message($message, $hashtag = '')
    {
        if (empty($this->token) || empty($this->channel_id)) {
            return [
                'success' => false,
                'error'   => 'Eitaa token or channel ID is missing.'
            ];
        }

        $url = "https://eitaayar.ir/api/" . $this->token . "/sendMessage";

        $post_fields = [
            'chat_id'    => $this->channel_id,
            'text'       => $message . "\n\n" . $hashtag,
            'parse_mode' => 'HTML'
        ];

        $args = [
            'body'    => $post_fields,
            'timeout' => 45,
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded'
            ],
        ];

        $response = wp_remote_post($url, $args);

        if (is_wp_error($response)) {
            return [
                'success' => false,
                'error'   => $response->get_error_message(),
            ];
        }

        $http_code      = wp_remote_retrieve_response_code($response);
        $response_body  = wp_remote_retrieve_body($response);

        return [
            'success' => $http_code == 200,
            'error'   => $response_body,
        ];
    }
}
