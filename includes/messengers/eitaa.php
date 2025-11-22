<?php
/**
 * Send text message to Eitaa (OOP version)
 *
 * @param string $message   The message text
 * @param string $hashtag   Message hashtag (test, message, order)
 *
 * @return array            Success/error response
 */
public function send_text_message( string $message, string $hashtag = '' ) : array {

    if ( empty( $this->api_token ) || empty( $this->channel_id ) ) {
        return [
            'success' => false,
            'error'   => 'Eitaa API token or channel ID is missing.',
        ];
    }

    // Build API URL exactly like procedural version
    $url = "https://eitaayar.ir/api/{$this->api_token}/sendMessage";

    // Combine message and hashtag
    $final_message = trim( $message . "\n\n" . $hashtag );

    $post_fields = [
        'chat_id'    => $this->channel_id,
        'text'       => $final_message,
        'parse_mode' => 'HTML',
    ];

    $args = [
        'body'    => $post_fields,
        'timeout' => 45,
        'headers' => [
            'Content-Type' => 'application/x-www-form-urlencoded'
        ],
    ];

    $response = wp_remote_post( $url, $args );

    // Check error
    if ( is_wp_error( $response ) ) {
        return [
            'success' => false,
            'error'   => $response->get_error_message(),
        ];
    }

    $http_code = wp_remote_retrieve_response_code( $response );
    $response_body = wp_remote_retrieve_body( $response );

    // Match procedural plugin result structure:
    return [
        'success' => ( $http_code == 200 ),
        'error'   => $response_body,
    ];
}
?>