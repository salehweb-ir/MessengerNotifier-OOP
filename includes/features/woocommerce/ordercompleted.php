<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class mni_free_feature_ordercompleted {
    use mni_singleton;

    private bool $enabled = false;

    public function __construct() {

        // Feature toggle check
        $features = get_option( 'mni_free_features', [] );

        $this->enabled = (empty( $features['newordercompleted'] ) ? false : true);

    add_action( 'plugins_loaded', [ $this, 'maybe_hook' ] );
}

public function maybe_hook(): void {

    if ( ! class_exists( 'WooCommerce' ) ) {
        error_log('[MNI] WooCommerce not active');
        return;
    }

    add_action(
        'woocommerce_payment_complete',
        [ $this, 'handle_order_completed' ],
        10,
        1
    );
}


    /**
     * Hook callback when WooCommerce order is completed
     */
    public function handle_order_completed( int $order_id ) {

        $order = wc_get_order( $order_id );
        if ( ! $order ) {
            return;
        }

        $message = $this->build_message( $order );
        
        // Feature disabled → log message only
        if ( ! $this->enabled ) {
            error_log( '[MNI Woo Order Disabled] ' . $message );
            // return;
        }

        // Feature enabled → send to messengers
        $manager = mni_free_messenger_manager::instance();
        $manager->send($message, 'new_order');

    }

    /**
     * Build the order completed message
     */
    private function build_message( $order ): string {

        $order_id      = $order->get_id();
        $customer_name = trim(
            $order->get_billing_first_name() . ' ' . $order->get_billing_last_name()
        );

        $total_price  = strip_tags( wc_price( $order->get_total() ) );
        $order_status = wc_get_order_status_name( $order->get_status() );

        $order_link = admin_url(
            "admin.php?page=wc-orders&action=edit&id={$order_id}"
        );

        $message  = "🛒 " . __( 'New order received!', 'messengernotifier' ) . "\n";
        $message .= "🔢 " . __( 'Order ID:', 'messengernotifier' ) . " {$order_id}\n";
        $message .= "👤 " . __( 'Customer:', 'messengernotifier' ) . " {$customer_name}\n";
        $message .= "💰 " . __( 'Total amount:', 'messengernotifier' ) . " {$total_price}\n";
        $message .= "📌 " . __( 'Order status:', 'messengernotifier' ) . " {$order_status}\n";
        $message .= "🔗 " . __( 'Order Link:', 'messengernotifier' ) . " {$order_link}\n";

        return $message;
    }
}