<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/* send text message to eitaa
 * token = Eitaa API token
 * channel_id = numeral channel idate
 * message = message sent to Eitaa
 * hashtag = sent message type (test, message, order)
 */
function messengernotifier_send_text_message($token, $channel_id, $message, $hashtag) {
    $url = "https://eitaayar.ir/api/".$token."/sendMessage";

    $post_fields = array(
        'chat_id' => $channel_id,
        'text' => $message .'
		
		'. $hashtag,
        'parse_mode' => 'HTML'
    );

    $args = array(
        'body'    => $post_fields,
        'timeout' => 45, // set wait time to avoid timeout
        'headers' => array(
        'Content-Type' => 'application/x-www-form-urlencoded'
        ),
    );

    $response = wp_remote_post($url, $args);

    // check error 
    if (is_wp_error($response)) {
        return [
            'success' => false,
            'error'   => $response->get_error_message(),
        ];
    }

    // retrieve response code and body from API
    $http_code = wp_remote_retrieve_response_code($response);
    $response_body = wp_remote_retrieve_body($response);

    // set API response log
	return [
			'success' => $http_code == 200,
			'error'    => $response_body,
		];
}

// this is procedural file

?>
