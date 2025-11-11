<?php
/**
 * Messenger Manager
 *
 * @package MessengerNotifier
 * @subpackage Includes
 */

namespace MNI_FREE;

use MNI_FREE\API\Eitaa;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Prevent direct access
}

/**
 * Class Messenger_Manager
 *
 * Responsible for selecting the active messengers and routing messages.
 */
class Messenger_Manager {

	/**
	 * The list of active messengers.
	 *
	 * @var array
	 */
	private $active_messengers = [];

	/**
	 * Plugin options.
	 *
	 * @var array
	 */
	private $options = [];

	/**
	 * Constructor.
	 *
	 * Loads plugin settings and initializes active messengers.
	 */
	public function __construct() {
		$this->options = get_option( 'mni_free_settings', [] );
		$this->init_active_messengers();
	}

	/**
	 * Initialize active messengers based on plugin settings.
	 *
	 * Example structure:
	 * [
	 *   'eitaa' => [ 'token' => '...', 'channel_id' => '...' ],
	 *   'bale'  => [ 'token' => '...', 'chat_id' => '...' ],
	 * ]
	 *
	 * @return void
	 */
	private function init_active_messengers() {
		if ( empty( $this->options['messengers'] ) ) {
			return;
		}

		foreach ( $this->options['messengers'] as $messenger_key => $data ) {
			switch ( $messenger_key ) {
				case 'eitaa':
					$this->active_messengers['eitaa'] = new Eitaa(
						$data['token'] ?? '',
						$data['channel_id'] ?? ''
					);
					break;

				// Example for future expansion:
				// case 'bale':
				//     $this->active_messengers['bale'] = new Bale( $data['token'], $data['chat_id'] );
				//     break;
			}
		}
	}

	/**
	 * Send a message to all active messengers.
	 *
	 * @param string $message  The message text.
	 * @param string $hashtag  The message type (e.g. #order, #comment).
	 * @return array List of messenger results.
	 */
	public function send_to_all( $message, $hashtag = '' ) {
		$results = [];

		if ( empty( $this->active_messengers ) ) {
			return [ 'error' => __( 'No active messengers configured.', 'messengernotifier' ) ];
		}

		foreach ( $this->active_messengers as $key => $messenger ) {
			if ( method_exists( $messenger, 'send_message' ) ) {
				$results[ $key ] = $messenger->send_message( $message, $hashtag );
			} else {
				$results[ $key ] = [
					'success' => false,
					'error'   => sprintf( __( 'Messenger %s does not support message sending.', 'messengernotifier' ), $key ),
				];
			}
		}

		return $results;
	}

	/**
	 * Test all active messenger connections.
	 *
	 * @return array
	 */
	public function test_all() {
		$results = [];
		foreach ( $this->active_messengers as $key => $messenger ) {
			if ( method_exists( $messenger, 'test_connection' ) ) {
				$results[ $key ] = $messenger->test_connection();
			}
		}
		return $results;
	}
}
