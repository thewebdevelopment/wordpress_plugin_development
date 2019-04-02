<?php
class WC_Worldpay_Admin_Order_Controller {

	public function __construct() {
		add_action ( 'rest_api_init', array( $this, 
				'register_route' 
		) );
	}

	public function register_route() {
		register_rest_route ( '/worldpay/v1/admin/order', '/capture-charge', array( 
				'methods' => WP_REST_Server::EDITABLE, 
				'callback' => array( $this, 
						'capture_charge' 
				) 
		) );
		register_rest_route ( '/worldpay/v1/admin/order', '/cancel', array( 
				'methods' => WP_REST_Server::EDITABLE, 
				'callback' => array( $this, 'cancel_order' 
				) 
		) );
	}

	/**
	 *
	 * @param $request WP_REST_Request        	
	 */
	public function capture_charge($request) {
		$response = new WP_REST_Response ();
		$order_id = $request->get_param ( 'order_id' );
		$order = wc_get_order ( $order_id );
		$amount = $request->get_param ( 'amount' );
		if (! is_numeric ( $amount )) {
			return new WP_Error ( 'invalid_data', __ ( 'Invalid amount entered.', 'worldpay' ), array( 
					'success' => false, 'status' => 200 
			) );
		}
		$gateway = WC ()->payment_gateways ()->get_available_payment_gateways ()[ $order->get_payment_method () ];
		$result = $gateway->capture_charge ( $order, $amount );
		if (is_wp_error ( $result )) {
			return new WP_Error ( 'capture_error', $result->get_error_message (), array( 
					'success' => false, 'status' => 200 
			) );
		}
		$response->set_data ( array( 'success' => true 
		) );
		return $response;
	}

	/**
	 *
	 * @param $request WP_REST_Request        	
	 */
	public function cancel_order($request) {
		$response = new WP_REST_Response ();
		$order_id = $request->get_param ( 'order_id' );
		$order = wc_get_order ( $order_id );
		$gateway = WC ()->payment_gateways ()->get_available_payment_gateways ()[ $order->get_payment_method () ];
		$result = $gateway->cancel_order ( $order->get_transaction_id (), $order );
		if (is_wp_error ( $result )) {
			return new WP_Error ( 'capture_error', $result->get_error_message (), array( 
					'success' => false, 'status' => 200 
			) );
		}
		$response->set_data ( array( 'success' => true 
		) );
		return $response;
	}
}

new WC_Worldpay_Admin_Order_Controller ();