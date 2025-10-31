<?php
/**
 * WooCommerce → Order Completed Action
 *
 * Sends a notification when an order is marked as completed.
 *
 * @package MessengerNotifier
 */

namespace MNI_FREE\Features\WooCommerce;

use MNI_FREE\Messenger_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Prevent direct access
}

/**
 * Hook into WooCommerce order completion
 */
add_action( 'woocommerce_order_status_completed', function( $order_id ) {

	if ( ! $order_id ) {
		return;
	}

	$order = wc_get_order( $order_id );

	if ( ! $order ) {
		do_action( 'mni_free_log_error', 'Order not found for ID: ' . $order_id );
		return;
	}

	$customer_name = trim( $order->get_billing_first_name() . ' ' . $order->get_billing_last_name() );

	// Base message data
	$data = [
		'context' => 'woocommerce_order_completed',
		'title'   => __( '✅ WooCommerce Order Completed', 'messengernotifier' ),
		'content' => sprintf(
			__( "🛒 Order #%d has been completed!\n👤 Customer: %s\n💰 Total: %s", 'messengernotifier' ),
			$order_id,
			$customer_name,
			wp_strip_all_tags( wc_price( $order->get_total() ) )
		),
		'meta'    => [
			'order_id'   => $order_id,
			'status'     => $order->get_status(),
			'order_link' => admin_url( "post.php?post={$order_id}&action=edit" ),
		],
	];

	/**
	 * 🔄 Allow Pro version and add-ons to add or modify data before sending.
	 * Example: MessengerNotifierPro might add product list, shipping info, etc.
	 */
	$data = apply_filters( 'mni_free_message_data', $data, 'woocommerce_order_completed' );

	/**
	 * 🚀 Trigger the unified messenger sending process.
	 */
	do_action( 'mni_free_send_message', $data );
});
