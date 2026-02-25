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

    private function __construct() {
        $this->load_messengers();
    }

    public function send_test( string $messenger_id, array $config ) : array {

        $class_map = [
            'eitaa' => \MessengerNotifier\Messengers\Eitaa::class,
            'bale' => \MessengerNotifier\Messengers\Bale::class,
            // future: 'telegram' => Telegram::class,
        ];

        if ( ! isset( $class_map[ $messenger_id ] ) ) {
            return [
                'success' => false,
                'message' => 'Messenger not supported'
            ];
        }

        $class = $class_map[ $messenger_id ];

        if ( ! class_exists( $class ) ) {
            return [
                'success' => false,
                'message' => 'Messenger class not found'
            ];
        }

        /** @var MessengerInterface $instance */
        $instance = new $class( $config ); // 👈 مهم: ارسال تنظیمات فرم

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

        $class_map = [
            'eitaa' => Eitaa::class,
            'bale' => Bale::class,
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
