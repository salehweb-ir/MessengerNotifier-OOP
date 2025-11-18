<?php
if ( ! defined( 'ABSPATH' ) ) exit;

require_once MNI_FREE_PATH . 'includes/traits/singleton.php';

class mni_free_messenger_manager {
    use mni_singleton;

    /** @var array Loaded active messenger classes */
    private $active_messengers = [];

    private function __construct() {
        $this->load_active_messengers();
    }

    /**
     * Load messenger classes based on wizard settings (step 1)
     */
    private function load_active_messengers() {
        $messengers = get_option( 'mni_free_active_messengers', [] );

        if ( empty( $messengers ) || ! is_array( $messengers ) ) {
            return;
        }

        foreach ( $messengers as $messenger_id ) {
            $class_file = MNI_FREE_PATH . "includes/messengers/{$messenger_id}.php";

            if ( file_exists( $class_file ) ) {
                require_once $class_file;

                $class_name = "mni_free_messenger_{$messenger_id}";

                if ( class_exists( $class_name ) ) {
                    $this->active_messengers[$messenger_id] = $class_name::instance();
                }
            }
        }
    }

    /**
     * Send message to all active messengers
     */
    public function send( string $text ) : array {
        if ( empty( $this->active_messengers ) ) {
            return [ 'error' => 'No active messengers.' ];
        }

        $results = [];

        foreach ( $this->active_messengers as $id => $messenger ) {
            if ( method_exists( $messenger, 'send_message' ) ) {
                $results[$id] = $messenger->send_message( $text );
            }
        }

        return $results;
    }

    /**
     * Get settings for all active messengers (used by wizard)
     */
    public function get_active_messengers() : array {
        return $this->active_messengers;
    }

    /**
     * Check if messenger exists
     */
    public function get_messenger( string $id ) {
        return $this->active_messengers[$id] ?? false;
    }
}
