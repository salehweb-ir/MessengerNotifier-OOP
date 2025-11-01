<?php
/**
 * Messenger Notifier Free — Hooks
 *
 * @package MessengerNotifierFree
 * @since 1.1.0
 */

namespace MNI_FREE;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Hooks
 *
 * Responsible for registering and handling all WordPress hooks and actions
 * used across the plugin.
 */
class Hooks {

	/**
	 * Initialize all plugin hooks.
	 *
	 * @return void
	 */
	public static function init() {
		// Core WordPress hooks.
		add_action( 'init', [ __CLASS__, 'register_shortcodes' ] );

		// Comment notification.
		add_action( 'comment_post', [ __CLASS__, 'on_comment_posted' ], 10, 2 );

		// WooCommerce integration hooks.
		if ( class_exists( 'WooCommerce' ) ) {
			add_action( 'woocommerce_order_status_completed', [ __CLASS__, 'on_order_completed' ], 10, 1 );
			// In future releases, more actions will be added dynamically from /features/woocommerce/.
		}
	}

	/**
	 * Register all plugin shortcodes.
	 *
	 * @return void
	 */
	public static function register_shortcodes() {
		$shortcode = new \MNI_FREE\Features\Shortcode_Anonymous();
		$shortcode->register();
	}

	/**
	 * Handle new comment submission.
	 *
	 * @param int        $comment_ID   Comment ID.
	 * @param int|string $comment_approved  Comment approval status.
	 *
	 * @return void
	 */
	public static function on_comment_posted( $comment_ID, $comment_approved ) {
		if ( 1 !== (int) $comment_approved ) {
			return;
		}

		$comment_data = get_comment( $comment_ID );
		if ( ! $comment_data ) {
			return;
		}

		$message_data = [
			'type'      => 'comment',
			'author'    => $comment_data->comment_author,
			'content'   => $comment_data->comment_content,
			'post_link' => get_permalink( $comment_data->comment_post_ID ),
			'post_title'=> get_the_title( $comment_data->comment_post_ID ),
			'date'      => $comment_data->comment_date,
		];

		// Allow Pro version to extend message data.
		$message_data = apply_filters( 'mni_free_message_data', $message_data, 'comment_post' );

		// Send via Messenger Manager.
		$manager = new \MNI_FREE\Messenger_Manager();
		$manager->send_message( $message_data );
	}

	/**
	 * Handle WooCommerce order completed event.
	 *
	 * @param int $order_id Order ID.
	 *
	 * @return void
	 */
	public static function on_order_completed( $order_id ) {
		if ( ! $order_id ) {
			return;
		}

		$order = wc_get_order( $order_id );
		if ( ! $order ) {
			return;
		}

		$message_data = [
			'type'        => 'woocommerce_order_completed',
			'order_id'    => $order->get_id(),
			'customer'    => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
			'total'       => $order->get_total(),
			'currency'    => $order->get_currency(),
			'email'       => $order->get_billing_email(),
			'date'        => $order->get_date_completed() ? $order->get_date_completed()->date_i18n() : current_time( 'mysql' ),
		];

		// Let Pro version or other add-ons extend data.
		$message_data = apply_filters( 'mni_free_message_data', $message_data, 'order_completed' );

		$manager = new \MNI_FREE\Messenger_Manager();
		$manager->send_message( $message_data );
	}
}
