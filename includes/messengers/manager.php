<?php
if ( ! defined( 'ABSPATH' ) ) exit;

require_once MNI_FREE_PATH . 'includes/traits/singleton.php';
require_once MNI_FREE_PATH . 'includes/messengers/MessengerInterface.php';
require_once MNI_FREE_PATH . 'includes/messengers/eitaa.php';

use MessengerNotifier\Messengers\MessengerInterface;
use MessengerNotifier\Messengers\Eitaa;

class mni_free_messenger_manager {
    use mni_singleton;

    /**
     * @var MessengerInterface[]
     */
    private $messengers = [];

    private function __construct() {
        $this->load_messengers();
    }

    private function load_messengers() : void {

        $active = get_option( 'mni_free_messengers', [] );

        if ( ! is_array( $active ) ) {
            return;
        }

        $class_map = [
            'eitaa' => Eitaa::class,
        ];

        foreach ( $active as $messenger_id ) {

            if ( isset( $class_map[ $messenger_id ] ) ) {
                $class = $class_map[ $messenger_id ];
                $this->messengers[ $messenger_id ] = $class::instance();
            }
        }

    }

    public function send( string $message, string $type = '' ) : array {

        $results = [];

        foreach ( $this->messengers as $id => $messenger ) {
            $results[ $id ] = $messenger->send( $message, $type );
        }

        return $results;
    }
}
