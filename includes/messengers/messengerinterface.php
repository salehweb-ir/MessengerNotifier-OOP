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
     * Send a text message
     */
    public function send( string $message, string $type = '' ): array;
}
