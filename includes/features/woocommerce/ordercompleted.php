<?php
if ( ! defined( 'ABSPATH' ) ) exit;

require_once MNI_FREE_PATH . 'includes/traits/singleton.php';

class mni_free_ordercompleted {
    use mni_singleton;

    private function __construct() {

        // Only activate if WooCommerce is installed
        if ( ! class_exists( 'WooCommerce' ) ) {
            return;
        }

        if ( get_option( 'mni_free_action_ordercompleted', 1 ) ) {
            add_action( 'woocommerce_order_status_completed', [ $this, 'notify_order_completed' ], 10, 1 );
        }
    }

    /**
     * Notify when a WooCommerce order is completed
     */
    public function notify_order_completed( $order_id ) {
        $order = wc_get_order( $order_id );
        if ( ! $order ) {
            return;
        }

        $total  = wc_price( $order->get_total() );
        $name   = sanitize_text_field( $order->get_formatted_billing_full_name() );
        $email  = sanitize_email( $order->get_billing_email() );

        $message  = "🛒 <b>Order Completed</b>\n";
        $message .= "📦 Order ID: {$order_id}\n";
        $message .= "👤 Customer: {$name}\n";
        $message .= "📧 Email: {$email}\n";
        $message .= "💳 Total: {$total}\n";

        $hashtag = "#order";

        $messenger_manager = mni_free_messenger_manager::instance();
        $messenger_manager->send( $message . "\n\n" . $hashtag );
    }
}
