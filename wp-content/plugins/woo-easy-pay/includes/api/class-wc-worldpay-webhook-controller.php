<?php
class WC_Worldpay_Webhook_Controller {

	public function __construct() {
		add_action ( 'rest_api_init', array( $this, 
				'register_route' 
		) );
	}

	public function register_route() {
		register_rest_route ( '/worldpay/v1/webhook/', 'order', array( 
				'methods' => WP_REST_Server::EDITABLE, 
				'callback' => array( $this, 'update_order' 
				), 
				'permission_callback' => array( $this, 
						'check_merchant_id' 
				) 
		) );
	}

	/**
	 *
	 * @param WP_REST_Request $request        	
	 */
	public function check_merchant_id($request) {
		$merchant_id = $request->get_param ( 'merchantId' );
		$merchant_id = null == $merchant_id ? '' : $merchant_id;
		if (! hash_equals ( $merchant_id, worldpay_get_merchant_id () )) {
			return new WP_Error ( 'worldpay_error', __ ( 'You are not authorized to access this resource.', 'worldpay' ), array( 
					'status' => 401 
			) );
		}
		return true;
	}

	/**
	 *
	 * @param WP_REST_Request $request        	
	 */
	public function update_order($request) {
		worldpay_log_info ( sprintf ( __ ( 'Webhook received. Data: %1$s', 'worldpay' ), print_r ( $request->get_params (), true ) ) );
		$response = new WP_REST_Response ();
		global $wpdb;
		$order_code = $request->get_param ( 'orderCode' );
		$query = $wpdb->prepare ( "SELECT post_id from {$wpdb->prefix}postmeta WHERE meta_key = %s AND meta_value = %s", '_transaction_id', $order_code );
		try {
			$result = $wpdb->get_results ( $query );
			if (! $result) {
				throw new Exception ( sprintf ( __ ( 'Transaction %1$s not found in system.', 'worldpay' ), $order_code ), 200 );
			}
			$order_id = $result[ 0 ]->post_id;
			$order = wc_get_order ( $order_id );
			$status = $request->get_param ( 'paymentStatus' );
			if ($status) {
				$order->add_meta_data ( '_worldpay_payment_status', $status, true );
				$order->save ();
				/**
				 * Allow other plugins to hook in to this action and perofrm order operations based on the request data.
				 */
				do_action ( 'worldpay_webhook_order', $order, $request, $order_code, $status );
			}
			$response->set_status ( 200 );
		} catch ( Exception $e ) {
			worldpay_log_error ( sprintf ( __ ( 'Webhook error. Reason: %1$s', 'worldpay' ), $e->getMessage () ) );
			$response->set_status ( $e->getCode () );
		}
		return $response;
	}
}
new WC_Worldpay_Webhook_Controller ();