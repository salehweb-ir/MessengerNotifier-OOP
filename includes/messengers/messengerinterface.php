<?php
namespace MessengerNotifier\Messengers;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

interface MessengerInterface {

    /**
     * Messenger unique ID (e.g. eitaa, telegram)
     */
    public function get_id(): string;

    /**
     * Human-readable name
     */
    public function get_name(): string;

    /**
     * Check if messenger is configured correctly
     */
    // public function is_configured(): bool;

    /**
     * Send a text message
     */
    public function send( string $message, string $type = '' ): array;
}
