<?php
if ( ! defined( 'ABSPATH' ) ) exit;

require_once MNI_FREE_PATH . 'includes/traits/singleton.php';
require_once MNI_FREE_PATH . 'includes/messengers/MessengerInterface.php';
require_once MNI_FREE_PATH . 'includes/messengers/eitaa.php';
require_once MNI_FREE_PATH . 'includes/messengers/bale.php';

use MessengerNotifier\Messengers\MessengerInterface;
use MessengerNotifier\Messengers\Eitaa;
use MessengerNotifier\Messengers\Bale;

class mni_free_messenger_manager {
    use mni_singleton;

    /**
     * @var MessengerInterface[]
     */
    private $messengers = [];
    private $class_map = [];

    private function __construct() {
        $this->class_map = [
            'eitaa' => \MessengerNotifier\Messengers\Eitaa::class,
            'bale' => \MessengerNotifier\Messengers\Bale::class,
        ];
        $this->load_messengers();
    }

    public function send_test( string $messenger_id, array $config ) : array {

        if ( ! isset( $class_map[ $messenger_id ] ) ) {
            return [
                'success' => false,
                'message' => 'Messenger not supported'
            ];
        }

        $class = $this->class_map[ $messenger_id ];

        if ( ! class_exists( $class ) ) {
            return [
                'success' => false,
                'message' => 'Messenger class not found'
            ];
        }

        /** @var MessengerInterface $instance */
        $instance = new $class( $config ); // Sending settings

        return $instance->send( '✅ Test message from Messenger Notifier','test' );
    }

    /*!SECTION
    * load active messengers
    */
    private function load_messengers() : void {

        $settings = mni_free_init::instance()->mni_get_settings();

        $active = $settings['messengers'] ?? [];

        if ( ! is_array( $active ) ) {
            return;
        }

        foreach ( $active as $messenger_id ) {

            if ( isset( $this->class_map[ $messenger_id ] ) ) {

            
                $class = $this->class_map[ $messenger_id ];
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
