<?php
class WC_Worldpay_Admin_Order_Metaboxes {

	public static function init() {
		add_action ( 'add_meta_boxes', function () {
			add_meta_box ( 'woocommerce-worldpay-order-actions', __ ( 'Worldpay Actions', 'worldpay' ), array( 
					__CLASS__, 'capture_charge_view' 
			), 'shop_order', 'normal', 'default' );
		}, 10 );
	}

	/**
	 *
	 * @param WP_Post $post        	
	 */
	public static function capture_charge_view($post) {
		$order = wc_get_order ( $post->ID );
		$payment_method = $order->get_payment_method ();
		if ($payment_method === 'online_worldpay' || $payment_method === 'online_worldpay_paypal') {
			$status = $order->get_meta ( '_worldpay_payment_status' );
			if ($status === 'SUCCESS' || $status === 'CANCELLED') {
				printf ( esc_html__ ( 'Payment status is %1$s. No actions availabe at this time.', 'worldpay' ), $status );
				return;
			}
			include dirname ( __FILE__ ) . '/views/html-order-capture.php';
		}
	}
}
WC_Worldpay_Admin_Order_Metaboxes::init ();