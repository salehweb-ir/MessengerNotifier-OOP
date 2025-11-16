<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Messenger Notifier Free – Admin AJAX handlers
 */

// Only load in admin context
add_action( 'wp_ajax_mni_free_test_api', function() {

    // Check capability (only admins should test)
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( [ 'message' => __( 'Insufficient permissions', 'messengernotifier' ) ], 403 );
    }

    // Verify nonce
    if ( empty( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'mni_free_test_api_nonce' ) ) {
        wp_send_json_error( [ 'message' => __( 'Invalid nonce', 'messengernotifier' ) ], 400 );
    }

    // Required params
    $messenger = isset( $_POST['messenger'] ) ? sanitize_text_field( wp_unslash( $_POST['messenger'] ) ) : '';
    $token     = isset( $_POST['token'] ) ? sanitize_text_field( wp_unslash( $_POST['token'] ) ) : '';
    $channel   = isset( $_POST['channel'] ) ? sanitize_text_field( wp_unslash( $_POST['channel'] ) ) : '';

    if ( empty( $messenger ) ) {
        wp_send_json_error( [ 'message' => __( 'Missing messenger id', 'messengernotifier' ) ], 400 );
    }

    // Allow messenger implementations to handle the test.
    // Handler should call wp_send_json_success() or wp_send_json_error() itself.
    $handled = apply_filters( 'mni_free_test_api_request', false, [
        'messenger' => $messenger,
        'token'     => $token,
        'channel'   => $channel,
    ] );

    // If a hook returned something truthy we assume handler took care of response.
    if ( $handled ) {
        // nothing to do: the hooked handler should already have returned a JSON response.
        wp_die();
    }

    // Default behavior: very basic check — token must not be empty.
    if ( empty( $token ) ) {
        wp_send_json_error( [ 'message' => __( 'API token is empty. Please enter a token to test.', 'messengernotifier' ) ], 200 );
    }

    // Basic success placeholder
    wp_send_json_success( [ 'message' => __( 'Test request sent (placeholder). Implement real test in messenger class.', 'messengernotifier' ) ] );
} );
