<?php
/**
 * Setup Wizard for Messenger Notifier
 *
 * Runs once on plugin activation to initialize default options
 * and create the anonymous contact page.
 *
 * @package MessengerNotifier
 */

namespace MNI_FREE;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Prevent direct access
}

class Wizard {

	/**
	 * Run setup wizard on activation.
	 *
	 * @return void
	 */
	public static function run_setup() {
		self::create_default_options();
		self::create_anonymous_page();
	}

	/**
	 * Create default plugin options if not already set.
	 *
	 * @return void
	 */
	private static function create_default_options() {

		/**
		 * Define supported messengers.
		 * Future messengers (Bale, Gap, Telegram, etc.) can easily be added here.
		 */
		$messengers = [
			'eitaa' => [
				'token_option'  => 'mni_free_token_eitaa_api',
				'id_option'     => 'mni_free_eitaa_channel_id',
			],
			// Example for future messengers:
			// 'bale'  => [
			// 	'token_option'  => 'mni_free_token_bale_api',
			// 	'id_option'     => 'mni_free_bale_channel_id',
			// ],
		];

		// Global default options
		$defaults = [
			'mni_free_selected_messengers' => [ 'eitaa' ], // Default active messenger
			'mni_free_enable_anon_page'    => true,
			'mni_free_version'             => MNI_FREE_VERSION,
		];

		// Add messenger-specific options dynamically
		foreach ( $messengers as $slug => $fields ) {
			foreach ( $fields as $key => $option_name ) {
				$defaults[ $option_name ] = '';
			}
		}

		// Save each option only if missing
		foreach ( $defaults as $key => $value ) {
			if ( get_option( $key ) === false ) {
				add_option( $key, $value );
			}
		}
	}

	/**
	 * Create anonymous contact page automatically if not exists.
	 *
	 * @return void
	 */
	private static function create_anonymous_page() {
		$page_title = __( 'Anonymous Message', 'messengernotifier' );
		$page_check = get_page_by_title( $page_title );

		if ( ! $page_check ) {
			$page_id = wp_insert_post( [
				'post_title'   => $page_title,
				'post_content' => '[messengernotifier_form]',
				'post_status'  => 'publish',
				'post_type'    => 'page',
			] );

			if ( $page_id && ! is_wp_error( $page_id ) ) {
				update_option( 'mni_free_anonymous_page_id', $page_id );
			}
		}
	}
}
