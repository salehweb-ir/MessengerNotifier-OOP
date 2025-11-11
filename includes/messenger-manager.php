<?php
/**
 * Messenger Manager
 *
 * @package MessengerNotifier
 * @subpackage Includes
 */

namespace MNI_FREE;

use MNI_FREE\API;

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
	 * Holds loaded messenger instances.
	 *
	 * @var array<string, object>
	 */
	private static $messengers = [];

	/**
	 * Initialize and load active messengers.
	 *
	 * Called on plugin init or activation wizard setup.
	 *
	 * @since 1.1.0
	 */
	public static function init_active_messengers() {
		$active_messengers = get_option( 'mni_free_active_messengers', [ 'Eitaa' ] );

		if ( empty( $active_messengers ) || ! is_array( $active_messengers ) ) {
			$active_messengers = [ 'Eitaa' ]; // Default fallback.
		}

		foreach ( $active_messengers as $messenger_name ) {
			self::load_messenger( $messenger_name );
		}

		do_action( 'mni_free_messengers_initialized', self::$messengers );
	}

	/**
	 * Load a single messenger API file and instantiate it.
	 *
	 * @param string $messenger_name Messenger name (e.g., "Eitaa", "Bale").
	 * @return void
	 */
	private static function load_messenger( string $messenger_name ) {
		$file_path  = MNI_FREE_PATH . 'api/' . strtolower( $messenger_name ) . '.php';
		$class_name = '\\MNI_FREE\\API\\' . $messenger_name;

		if ( ! file_exists( $file_path ) ) {
			do_action( 'mni_free_missing_messenger_file', $messenger_name, $file_path );
			return;
		}

		require_once $file_path;

		if ( class_exists( $class_name ) ) {
			self::$messengers[ $messenger_name ] = new $class_name();
		} else {
			do_action( 'mni_free_invalid_messenger_class', $messenger_name, $class_name );
		}
	}

	/**
	 * Return loaded messenger instances.
	 *
	 * @return array<string, object>
	 */
	public static function get_messengers(): array {
		return self::$messengers;
	}

	/**
	 * Send message through all active messengers.
	 *
	 * @param string $message  The text message to send.
	 * @param array  $extra    Optional additional data (e.g., WooCommerce order info).
	 *
	 * @return array  Result array with success/error per messenger.
	 */
	public static function send_message( string $message, array $extra = [] ): array {
		$results = [];

		// Ensure messengers are loaded.
		if ( empty( self::$messengers ) ) {
			self::init_active_messengers();
		}

		foreach ( self::$messengers as $name => $instance ) {
			if ( method_exists( $instance, 'send_message' ) ) {
				$results[ $name ] = $instance->send_message( $message, $extra );
			} else {
				$results[ $name ] = [
					'success' => false,
					'error'   => sprintf( __( '%s messenger class does not implement send_message().', 'messengernotifier' ), $name ),
				];
			}
		}

		do_action( 'mni_free_message_sent', $message, $results, $extra );

		return $results;
	}

	/**
	 * Check if a specific messenger is active.
	 *
	 * @param string $messenger_name Messenger name.
	 * @return bool
	 */
	public static function is_active( string $messenger_name ): bool {
		return isset( self::$messengers[ $messenger_name ] );
	}

	/**
	 * Add or replace a messenger instance (extendable by Pro or third-party).
	 *
	 * @param string $messenger_name
	 * @param object $instance
	 * @return void
	 */
	public static function register_messenger( string $messenger_name, object $instance ): void {
		self::$messengers[ $messenger_name ] = $instance;
		do_action( 'mni_free_messenger_registered', $messenger_name, $instance );
	}
}
