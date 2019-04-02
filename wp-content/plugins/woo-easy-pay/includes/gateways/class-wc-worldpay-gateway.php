<?php
use Worldpay\Worldpay;
use Worldpay\WorldpayException;

if (! class_exists ( 'WC_Payment_Gateway' )) {
	return;
}
class WC_Online_Worldpay_Payment_Gateway extends WC_Payment_Gateway {

	public $service_key = '';

	public $client_key = '';

	public $payment_nonce_key = '';

	public $payment_token_key = '';

	public $payment_type_key = '';

	public $payment_token_type_key = '';

	public $update_payment_method_request = false;

	public static $capturing_charge = false;

	/**
	 *
	 * @var \Worldpay\Worldpay
	 */
	public $worldpay = null;

	public static function init() {
		add_action ( 'woocommerce_payment_token_deleted', array( 
				__CLASS__, 'payment_token_deleted' 
		), 10, 2 );
		add_filter ( 'woocommerce_credit_card_type_labels', array( 
				__CLASS__, 'credit_card_labels' 
		) );
	}

	public function __construct() {
		$this->init_form_fields ();
		$this->init_settings ();
		$this->enabled = $this->get_option ( 'enabled' );
		$this->has_fields = true;
		$this->payment_nonce_key = $this->id . '_payment_nonce';
		$this->payment_token_key = $this->id . '_payment_token';
		$this->payment_type_key = $this->id . '_payment_type_key';
		$this->payment_token_type_key = $this->id . '_payment_token_type';
		$this->set_supports ();
		$this->add_actions ();
		$this->connect ();
	}

	public function add_actions() {
		add_action ( 'woocommerce_update_options_payment_gateways_' . $this->id, array( 
				$this, 'process_admin_options' 
		) );
		add_action ( 'worldpay_before_process_payment', array( 
				$this, 'maybe_save_payment_token' 
		), 10, 2 );
		add_action ( $this->id . '_payment_token_deleted', array( 
				$this, 'delete_payment_method' 
		), 10, 2 );
		add_action ( 'woocommerce_subscriptions_pre_update_payment_method', array( 
				$this, 'pre_update_payment_method' 
		), 10, 2 );
		add_filter ( 'woocommerce_subscription_payment_meta', array( 
				$this, 'subscription_payment_meta' 
		), 10, 2 );
		add_action ( 'woocommerce_scheduled_subscription_payment_' . $this->id, array( 
				$this, 'process_subscription_payment' 
		), 10, 2 );
		add_action ( 'woocommerce_order_status_completed', array( 
				$this, 'capture_authorized_order' 
		), 10, 2 );
		add_filter ( 'woocommerce_get_customer_payment_tokens', array( 
				$this, 'filter_payment_tokens' 
		), 10, 3 );
	}

	/**
	 * Setup connection info using API keys.
	 */
	public function connect($env = '') {
		try {
			$this->worldpay = new Worldpay ( $this->get_service_key ( $env ) );
			if (worldpay_get_environment () === 'test') {
				$this->worldpay->disableSSLCheck ( true );
			}
			$this->worldpay->setPluginData ( 'woo-easy-pay', worldpay ()->version () );
		} catch ( WorldpayException $e ) {
			worldpay_log_error ( sprintf ( __ ( 'Error creating Worldpay connection. Reason: %s', 'worldpay' ), $e->getMessage () ) );
		}
	}

	public function set_supports() {
		$this->supports = array( 'subscriptions', 
				'products', 'add_payment_method', 
				'subscription_cancellation', 
				'multiple_subscriptions', 
				'subscription_amount_changes', 
				'subscription_date_changes', 
				'default_credit_card_form', 'refunds', 
				'pre-orders', 
				'subscription_payment_method_change_admin', 
				'subscription_reactivation', 
				'subscription_suspension', 
				'subscription_payment_method_change_customer' 
		);
	}

	/**
	 *
	 * @return \Worldpay\Worldpay
	 */
	public function worldpay() {
		return $this->worldpay;
	}

	public function process_payment($order_id) {
		$order = wc_get_order ( $order_id );
		do_action ( 'worldpay_before_process_payment', $order_id, $order, $this );
		if (wc_notice_count ( 'error' ) > 0) {
			return array( 'result' => 'failure' 
			);
		}
		if ($this->update_payment_method_request) {
			return array( 'result' => 'success', 
					'redirect' => wc_get_endpoint_url ( 'view-subscription', $order_id, wc_get_page_permalink ( 'myaccount' ) ) 
			);
		}
		if ($order->get_total () == 0) {
			return $this->process_zero_amount_order ( $order );
		}
		$session_id = $this->get_shopper_session_id ();
		$args = apply_filters ( 'worldpay_process_payment_args', array( 
				'customerOrderCode' => $order->get_order_number (), 
				'token' => $this->get_payment_token (), 
				'amount' => round ( $order->get_total () * pow ( 10, worldpay_get_currency_code_exponent ( $order->get_currency () ) ), 0, PHP_ROUND_HALF_UP ), 
				'currencyCode' => $order->get_currency (), 
				'name' => $this->get_order_name ( $order ), 
				'authorizeOnly' => $this->get_option ( 'charge_type' ) === 'authorize', 
				'orderDescription' => sprintf ( __ ( 'Order %s', 'worldpay' ), $order_id ), 
				'billingAddress' => array( 
						'address1' => $order->get_billing_address_1 (), 
						'address2' => $order->get_billing_address_2 (), 
						'postalCode' => $order->get_billing_postcode (), 
						'city' => $order->get_billing_city (), 
						'state' => $order->get_billing_state (), 
						'countryCode' => $order->get_billing_country () 
				), 
				'deliveryAddress' => $order->get_shipping_address_1 () ? array( 
						'firstName' => $order->get_shipping_first_name (), 
						'lastName' => $order->get_shipping_last_name (), 
						'address1' => $order->get_shipping_address_1 (), 
						'address2' => $order->get_shipping_address_2 (), 
						'postalCode' => $order->get_shipping_postcode (), 
						'city' => $order->get_shipping_city (), 
						'state' => $order->get_shipping_state (), 
						'countryCode' => $order->get_shipping_country () 
				) : array(), 
				'orderCodePrefix' => $this->get_option ( 'order_prefix' ), 
				'orderCodeSuffix' => $this->get_option ( 'order_suffix' ), 
				'settlementCurrency' => $this->get_option ( 'settlement_currency' ), 
				'shopperEmailAddress' => $order->get_billing_email (), 
				'shopperIpAddress' => $order->get_customer_ip_address (), 
				'shopperSessionId' => $session_id 
		), $order, $this->id );
		try {
			$return = array();
			$session = WC ()->session;
			$response = ! $this->is_apm_order () ? $this->worldpay->createOrder ( $args ) : $this->worldpay->createApmOrder ( $args );
			if (! $this->is_apm_order ()) {
				$this->add_order_meta ( $order, $response );
			}
			worldpay_log_info ( sprintf ( __ ( 'Order %1$s processed. Response: %2$s', 'worldpay' ), $order_id, print_r ( $response, true ) ) );
			if ($response[ 'paymentStatus' ] === 'SUCCESS') {
				$order->payment_complete ( $response[ 'orderCode' ] );
			} else if ($response[ 'paymentStatus' ] === 'AUTHORIZED') {
				$order->update_status ( apply_filters ( 'worldpay_authorized_order_status', $this->get_option ( 'authorize_status' ), $order, $this->id ) );
			} else if ($response[ 'paymentStatus' ] === 'PRE_AUTHORIZED') {
				if ($this->is_active ( '3ds_enabled' ) && ! empty ( $response[ 'redirectURL' ] )) {
					$threeDS_data = array( 
							'redirect_url' => $response[ 'redirectURL' ], 
							'3ds_token' => $response[ 'oneTime3DsToken' ], 
							'order_code' => $response[ 'orderCode' ], 
							'order_id' => $order_id, 
							'session_id' => $session_id, 
							'return_url' => $this->get_3ds_return_url () 
					);
					$session->set ( 'worldpay_3ds_data', $threeDS_data );
					$order->save ();
					$return[ 'result' ] = 'success';
					$return[ 'redirect' ] = get_permalink ( $this->get_option ( '3dsecure_page' ) );
					return $return;
				} else if ($this->is_apm_order ()) {
					$session->set ( 'worldpay_paypal_order', array( 
							'order_id' => $order_id, 
							'response' => $response 
					) );
					$order->set_transaction_id ( $response[ 'orderCode' ] );
					$return[ 'result' ] = 'success';
					$return[ 'redirect' ] = $response[ 'redirectURL' ];
					return $return;
				}
			} else {
				throw new WorldpayException ( $response[ 'paymentStatusReason' ] );
			}
			WC ()->cart->empty_cart ();
			$order->save ();
			$return[ 'result' ] = 'success';
			$return[ 'redirect' ] = $order->get_checkout_order_received_url ();
			return $return;
		} catch ( WorldpayException $e ) {
			$order->update_status ( apply_filters ( 'worldpay_failed_order_status', 'failed' ) );
			$order->add_order_note ( sprintf ( __ ( 'Error processing payment. Reason: %s', 'worldpay' ), $e->getMessage () ) );
			wc_add_notice ( sprintf ( __ ( 'Error processing payment. Reason: %s', 'worldpay' ), $e->getMessage () ), 'error' );
			worldpay_log_error ( sprintf ( __ ( 'Error processing Order %1$s. Reason: %2$s. JSON: %3$s', 'worldpay' ), $order->get_id (), $e->getMessage (), print_r ( $args, true ) ) );
			return array( 'success' => 'failure' 
			);
		}
	}

	public function process_refund($order_id, $amount = null, $reason = '') {
		// return WP_Error instance if there is an error processing refund.
		$order = wc_get_order ( $order_id );
		$amount = $amount * pow ( 10, worldpay_get_currency_code_exponent ( $order->get_currency () ) );
		$transaction_id = $order->get_transaction_id ();
		$this->connect ( $order->get_meta ( '_worldpay_environment' ) );
		$return = true;
		try {
			$this->worldpay->refundOrder ( $transaction_id, $amount );
			$order->add_order_note ( __ ( 'Refund successfully processed by Worldpay.', 'worldpay' ) );
		} catch ( WorldpayException $e ) {
			$message = sprintf ( __ ( 'Error refunding order. Reason: %s', 'worldpay' ), $e->getMessage () );
			worldpay_log_error ( $message );
			$order->add_order_note ( $message );
			$return = new WP_Error ( 'refund-error', $message );
		}
		$this->connect ();
		return $return;
	}

	/**
	 * Process an order for which the order amount is $0.
	 * This is common for subscriptions that have a trial date or are synchronized.
	 *
	 * @param WC_Order $order        	
	 */
	public function process_zero_amount_order($order) {
		if (worldpay_wcs_active ()) {
			if (wcs_order_contains_subscription ( $order )) {
				$token = $this->get_payment_token ();
				$payment_token = worldpay_get_payment_token ( $order->get_customer_id (), $token, $this->id );
				$payment_method_title = worldpay_get_payment_method_title ( $payment_token );
				$order->set_payment_method_title ( $payment_method_title );
				$order->add_meta_data ( '_payment_method_token', $token, true );
				$order->add_meta_data ( '_worldpay_environment', worldpay_get_environment (), true );
				$order->add_meta_data ( '_worldpay_version', worldpay ()->version (), true );
			}
			foreach ( wcs_get_subscriptions_for_order ( $order ) as $subscription ) {
				$subscription->set_payment_method_title ( $payment_method_title );
				$subscription->add_meta_data ( '_payment_method_token', $token, true );
				$subscription->add_meta_data ( '_worldpay_environment', worldpay_get_environment (), true );
				$subscription->add_meta_data ( '_worldpay_version', worldpay ()->version (), true );
				$subscription->save ();
			}
		}
		$order->save ();
		$order->payment_complete ();
		WC ()->cart->empty_cart ();
		return array( 'result' => 'success', 
				'redirect' => $order->get_checkout_order_received_url () 
		);
	}

	public function process_3ds_order() {}

	public function is_apm_order() {
		return isset ( $_POST[ $this->payment_token_type_key ] ) && $_POST[ $this->payment_token_type_key ] === 'apm';
	}

	/**
	 * Add order meta data to the order after payment has been processed.
	 *
	 * @param WC_Order $order        	
	 * @param array $response        	
	 */
	protected function add_order_meta($order, $response) {
		$payment_token = worldpay_create_wc_payment_token ( $response[ 'token' ], $response[ 'paymentResponse' ], $this->id );
		$payment_method_title = worldpay_get_payment_method_title ( $payment_token );
		$order->add_meta_data ( '_payment_method_token', $response[ 'token' ], true );
		$order->add_meta_data ( '_payment_data', $response[ 'paymentResponse' ], true );
		$order->add_meta_data ( ( '_worldpay_payment_status' ), $response[ 'paymentStatus' ], true );
		$order->add_meta_data ( '_worldpay_environment', worldpay_get_environment (), true );
		$order->add_meta_data ( '_worldpay_version', worldpay ()->version (), true );
		$order->set_payment_method_title ( $payment_method_title );
		$order->set_transaction_id ( $response[ 'orderCode' ] );
		$order->save ();
		/**
		 * Save meta for WCS if applicable.
		 */
		if (worldpay_wcs_active ()) {
			if (wcs_order_contains_subscription ( $order )) {
				$subscriptions = wcs_get_subscriptions_for_order ( $order );
				foreach ( $subscriptions as $subscription ) {
					$subscription->add_meta_data ( '_payment_method_token', $response[ 'token' ], true );
					$subscription->add_meta_data ( '_worldpay_environment', worldpay_get_environment (), true );
					$subscription->add_meta_data ( '_worldpay_version', worldpay ()->version (), true );
					$subscription->add_meta_data ( '_original_order_code', $response[ 'orderCode' ], true );
					$subscription->set_payment_method_title ( $payment_method_title );
					$subscription->set_transaction_id ( $response[ 'orderCode' ] );
					$subscription->save ();
				}
			}
		}
	}

	public function get_custom_attribute_html($attribs) {
		if (! empty ( $attribs[ 'custom_attributes' ] ) && is_array ( $attribs[ 'custom_attributes' ] )) {
			foreach ( $attribs[ 'custom_attributes' ] as $k => $v ) {
				if (is_array ( $v )) {
					$attribs[ 'custom_attributes' ][ $k ] = htmlspecialchars ( wp_json_encode ( $v ) );
				}
			}
		}
		return parent::get_custom_attribute_html ( $attribs );
	}

	public function generate_multiselect_html($key, $data) {
		$value = ( array ) $this->get_option ( $key, array() );
		$data[ 'options' ] = array_merge ( array_flip ( $value ), $data[ 'options' ] );
		return parent::generate_multiselect_html ( $key, $data );
	}

	public function admin_options() {
		echo '<div class="worldpay-settings-cotnainer">';
		include worldpay ()->base_path () . 'includes/admin/views/settings-nav.php';
		echo '<h2>' . esc_html ( $this->get_method_title () );
		wc_back_link ( __ ( 'Return to payments', 'woocommerce' ), admin_url ( 'admin.php?page=wc-settings&tab=checkout' ) );
		echo '</h2>';
		printf ( '<input type="hidden" id="worldpay_settings_prefix" value="%s"/>', $this->plugin_id . $this->id . '_' );
		echo '<table class="form-table">' . $this->generate_settings_html ( $this->get_form_fields (), false ) . '</table>'; // WPCS: XSS ok.
		echo '</div>';
	}

	public function is_active($key) {
		return $this->get_option ( $key, '' ) === 'yes';
	}

	/**
	 * Return the service key for the currently active environment.
	 *
	 * @param string $env        	
	 * @return string
	 */
	public function get_service_key($env = '') {
		return worldpay_get_service_key ( $env );
	}

	/**
	 * Return the client key for the currently active environment.
	 *
	 * @return string
	 */
	public function get_client_key() {
		return worldpay_get_client_key ();
	}

	/**
	 * Return the string representation of the current environment.
	 *
	 * @return string
	 */
	public function get_environment() {
		return $this->get_option ( 'environment', 'test' );
	}

	public function add_payment_method() {
		$token = isset ( $_POST[ $this->payment_nonce_key ] ) ? sanitize_text_field ( $_POST[ $this->payment_nonce_key ] ) : '';
		$user = wp_get_current_user ();
		try {
			$payment_method = $this->worldpay->getStoredCardDetails ( $token );
			$payment_token = worldpay_create_wc_payment_token ( $token, $payment_method, $this->id );
			$payment_token->save ();
			WC_Payment_Tokens::set_users_default ( $user->ID, $payment_token->get_id () );
		} catch ( WorldpayException $e ) {
			wc_add_notice ( sprintf ( __ ( 'There was an error saving your payment method. Reason: %s', 'worldpay' ), $e->getMessage () ), 'error' );
			worldpay_log_error ( sprintf ( __ ( 'Error saving User %s payment method. Reason: %s', 'worldpay' ), $user->ID, $e->getMessage () ) );
			// if result is not equal to success or failure, then WC doesn't add a notice. That's good because
			// we want to use our own notice here.
			return array( 'result' => 'exception' 
			);
		}
		return array( 'result' => 'success', 
				'redirect' => wc_get_account_endpoint_url ( 'payment-methods' ) 
		);
	}

	/**
	 * This method is static because WC does not initialize gateways during the delete payment method call.
	 *
	 * @param int $token_id        	
	 * @param WC_Payment_Token $token        	
	 */
	public static function payment_token_deleted($token_id, $token) {
		if (! did_action ( 'woocommerce_payment_gateways' )) {
			WC_Payment_Gateways::instance ();
		}
		do_action ( $token->get_gateway_id () . '_payment_token_deleted', $token_id, $token );
	}

	/**
	 *
	 * @param int $token_id        	
	 * @param WC_Payment_Token $token        	
	 */
	public function delete_payment_method($token_id, $token) {
		try {
			$this->worldpay->deleteToken ( $token->get_token () );
		} catch ( WorldpayException $e ) {
			worldpay_log_error ( sprintf ( __ ( 'Error deleting payment token %s in Worldpay. Reason: %s', 'worldpay' ), $token, $e->getMessage () ) );
		}
	}

	/**
	 * Returns a Worldpay token used for processing payments.
	 */
	public function get_payment_token() {
		return $this->use_saved_method () ? $_POST[ $this->payment_token_key ] : $_POST[ $this->payment_nonce_key ];
	}

	/**
	 * Return true if a saved payment method is being used.
	 */
	public function use_saved_method() {
		return ! empty ( $_POST[ $this->payment_type_key ] ) && $_POST[ $this->payment_type_key ] === 'token';
	}

	/**
	 *
	 * @param WC_Order $order        	
	 */
	public function get_order_name($order) {
		$name = '';
		if ($this->is_test_mode () && $this->is_active ( '3ds_enabled' )) {
			$name = '3D';
		} else {
			$name = sprintf ( '%s %s', $order->get_billing_first_name (), $order->get_billing_last_name () );
		}
		return $name;
	}

	protected function is_test_mode() {
		return worldpay_get_environment () === 'test';
	}

	/**
	 */
	protected function get_shopper_session_id() {
		return uniqid ();
	}

	/**
	 * Save the payment method if the cart has a subscription and a new payment method is being used.
	 *
	 * @param int $order_id        	
	 * @param WC_Order $order        	
	 */
	public function maybe_save_payment_token($order_id, $order) {
		if (worldpay_wcs_active () && ! $this->use_saved_method () && $order->get_payment_method () === $this->id) {
			if (wcs_order_contains_subscription ( $order )) {
				$token = sanitize_text_field ( $_POST[ $this->payment_nonce_key ] );
				$user = wp_get_current_user ();
				try {
					$response = $this->worldpay->getStoredCardDetails ( $token );
					$payment_token = worldpay_create_wc_payment_token ( $token, $response, $this->id );
					$payment_token->save ();
					WC_Payment_Tokens::set_users_default ( $user->ID, $payment_token->get_id () );
				} catch ( WorldpayException $e ) {
					wc_add_notice ( sprintf ( __ ( 'Error saving payment method for subscription. Reason: %s', 'worldpay' ), $e->getMessage () ) );
				}
			}
		}
	}

	public static function credit_card_labels($labels) {
		$labels[ 'amex' ] = __ ( 'American Express', 'woocommerce' );
		$labels[ 'maestro' ] = __ ( 'Maestro', 'worldpay' );
		$labels[ 'paypal' ] = __ ( 'PayPal', 'worldpay' );
		return $labels;
	}

	/**
	 * Return the url that is called when a 3DS order has been authenticated.
	 *
	 * @return string
	 */
	protected function get_3ds_return_url() {
		return wp_nonce_url ( get_site_url () . '/worldpay/v1/3ds/process', 'process-3ds-order', '_3ds_nonce' );
	}

	/**
	 *
	 * @param WC_Order $order        	
	 * @param float $amount        	
	 */
	public function capture_charge($order, $amount) {
		if (static::$capturing_charge) {
			// prevents double call of method since capture_charge is also hooked to order status complete.
			return;
		}
		static::$capturing_charge = true;
		$amount_in_cents = $amount * pow ( 10, worldpay_get_currency_code_exponent ( $order->get_currency () ) );
		$order_code = $order->get_transaction_id ();
		$this->connect ( $order->get_meta ( '_worldpay_environment' ) );
		$return = true;
		try {
			$this->worldpay->captureAuthorizedOrder ( $order_code, $amount_in_cents );
			// update order amount so it reflects how much was captured.
			$order->set_total ( $amount );
			$order->add_order_note ( sprintf ( __ ( 'Authorized charge was captured. Amount: %1$s', 'worldpay' ), wc_price ( $amount ) ) );
			$order->payment_complete ( $order_code );
		} catch ( WorldpayException $e ) {
			worldpay_log_error ( sprintf ( __ ( 'Error capturing amount %1$s for order %2$s. Reason: %3$s', 'worldpay' ), $amount, $order->get_id (), $e->getMessage () ) );
			$return = new WP_Error ( 'capture_error', sprintf ( __ ( 'Error capturing amount %1$s for order %2$s. Reason: %3$s', 'worldpay' ), $amount, $order->get_id (), $e->getMessage () ) );
		}
		try {
			$response = $this->worldpay->getOrder ( $order_code );
			$order->add_meta_data ( '_worldpay_payment_status', $response[ 'paymentStatus' ], true );
			$order->save ();
		} catch ( WorldpayException $e ) {
			worldpay_log_error ( sprintf ( __ ( 'Error updating order %1$s payment status. Reason: %2$s', 'worldpay' ), $order->get_id (), $e->getMessage () ) );
			$return = new WP_Error ( 'capture_error', sprintf ( __ ( 'Error updating order %1$s payment status. Reason: %2$s', 'worldpay' ), $order->get_id (), $e->getMessage () ) );
		}
		static::$capturing_charge = false;
		$this->connect ();
		return $return;
	}

	/**
	 *
	 * @param string $order_code        	
	 * @param WC_Order $order        	
	 */
	public function cancel_order($order_code, $order) {
		$this->connect ( $order->get_meta ( '_worldpay_environment' ) );
		$return = true;
		$this->connect ();
		try {
			$this->worldpay->cancelAuthorizedOrder ( $order_code );
			$order->update_status ( apply_filters ( 'worldpay_cancel_authorized_order_status', 'cancelled' ) );
			$order->add_order_note ( __ ( 'Order cancelled in Worldpay.', 'worldpay' ) );
		} catch ( WorldpayException $e ) {
			worldpay_log_error ( sprintf ( __ ( 'Error canceling order %1$s. Reason: %2$s', 'worldpay' ), $order->get_id () ) );
			$return = new WP_Error ( 'cancel_error', sprintf ( __ ( 'Error canceling order %1$s. Reason: %2$s', 'worldpay', 'worldpay' ), $order->get_id (), $e->getMessage () ) );
		}
		try {
			$response = $this->worldpay->getOrder ( $order_code );
			$order->add_meta_data ( '_worldpay_payment_status', $response[ 'paymentStatus' ], true );
			$order->save ();
		} catch ( WorldpayException $e ) {
			worldpay_log_error ( sprintf ( __ ( 'Error updating order %1$s payment status. Reason: %2$s', 'worldpay' ), $order->get_id (), $e->getMessage () ) );
			$return = new WP_Error ( 'cancel_error', sprintf ( __ ( 'Error updating order %1$s payment status. Reason: %2$s', 'worldpay' ), $order->get_id (), $e->getMessage () ) );
		}
		return $return;
	}

	/**
	 *
	 * @param WC_Subscription $subscription        	
	 */
	public function subscription_payment_method_updated($subscription) {
		$token = '';
		$payment_token = null;
		$user = wp_get_current_user ();
		/**
		 * If the payment nonce isn't empty, then this is a new payment method and should be added.
		 */
		if (! empty ( $_POST[ $this->payment_nonce_key ] )) {
			try {
				$token = sanitize_text_field ( $_POST[ $this->payment_nonce_key ] );
				$payment_method = $this->worldpay->getStoredCardDetails ( $token );
				$payment_token = worldpay_create_wc_payment_token ( $token, $payment_method, $this->id );
				$payment_token->save ();
				WC_Payment_Tokens::set_users_default ( $user->ID, $payment_token->get_id () );
			} catch ( WorldpayException $e ) {
				worldpay_log_error ( sprintf ( __ ( 'Error saving payment token %1$s for user ID %2$s, Reason: %3$s', 'worldpay' ), $token, $user->ID, $e->getMessage () ) );
				wc_add_notice ( sprintf ( __ ( 'Error saving your payment method. Reason: %s', 'worldpay' ), $e->getMessage () ), 'error' );
			}
		} else {
			$token = sanitize_text_field ( $_POST[ $this->payment_token_key ] );
			$payment_token = worldpay_get_payment_token ( $user->ID, $token, $this->id );
		}
		$subscription->add_meta_data ( '_worldpay_version', worldpay ()->version (), true );
		$subscription->add_meta_data ( '_worldpay_environment', worldpay_get_environment (), true );
		$subscription->add_meta_data ( '_payment_method_token', $token, true );
		$subscription->set_payment_method_title ( worldpay_get_payment_method_title ( $payment_token ) );
		$subscription->save ();
	}

	/**
	 *
	 * @param WC_Subscription $subscription        	
	 * @param string $new_payment_method        	
	 */
	public function pre_update_payment_method($subscription, $new_payment_method) {
		if ($new_payment_method === $this->id) {
			$this->update_payment_method_request = true;
		}
	}

	/**
	 *
	 * @param array $payment_meta        	
	 * @param WC_Subscription $subscription        	
	 */
	public function subscription_payment_meta($payment_meta, $subscription) {
		$payment_meta[ $this->id ] = array( 
				'post_meta' => array( 
						'_payment_method_token' => array( 
								'value' => $subscription->get_meta ( '_payment_method_token' ), 
								'label' => __ ( 'Payment Method Token', 'worldpay' ) 
						) 
				) 
		);
		return $payment_meta;
	}

	/**
	 * Process the WCS scheduled payment.
	 *
	 * @param float $amount        	
	 * @param WC_Order $order        	
	 */
	public function process_subscription_payment($amount, $order) {
		$args = apply_filters ( 'worldpay_process_subscription_payment_args', array( 
				'orderType' => 'RECURRING', 
				'customerOrderCode' => $order->get_order_number (), 
				'token' => $order->get_meta ( '_payment_method_token' ), 
				'amount' => $amount * pow ( 10, worldpay_get_currency_code_exponent ( $order->get_currency () ) ), 
				'currencyCode' => $order->get_currency (), 
				'name' => sprintf ( __ ( '%1$s %2$s', 'worldpay' ), $order->get_billing_first_name (), $order->get_billing_last_name () ), 
				'authorizeOnly' => $this->get_option ( 'wcs_charge_type' ) === 'authorize', 
				'orderDescription' => sprintf ( __ ( 'Renewal order %s', 'worldpay' ), $order->get_order_number () ), 
				'billingAddress' => array( 
						'address1' => $order->get_billing_address_1 (), 
						'address2' => $order->get_billing_address_2 (), 
						'postalCode' => $order->get_billing_postcode (), 
						'city' => $order->get_billing_city (), 
						'state' => $order->get_billing_state (), 
						'countryCode' => $order->get_billing_country () 
				), 
				'deliveryAddress' => $order->get_shipping_address_1 () ? array( 
						'firstName' => $order->get_shipping_first_name (), 
						'lastName' => $order->get_shipping_last_name (), 
						'address1' => $order->get_shipping_address_1 (), 
						'address2' => $order->get_shipping_address_2 (), 
						'postalCode' => $order->get_shipping_postcode (), 
						'city' => $order->get_shipping_city (), 
						'state' => $order->get_shipping_state (), 
						'countryCode' => $order->get_shipping_country () 
				) : array(), 
				'orderCodePrefix' => $this->get_option ( 'order_prefix' ), 
				'orderCodeSuffix' => $this->get_option ( 'order_suffix' ), 
				'settlementCurrency' => $this->get_option ( 'settlement_currency' ), 
				'shopperEmailAddress' => $order->get_billing_email (), 
				'shopperIpAddress' => $order->get_customer_ip_address () 
		), $order, $this->id );
		try {
			$this->connect ( $order->get_meta ( '_worldpay_environment' ) );
			$response = $this->worldpay->createOrder ( $args );
			$this->add_order_meta ( $order, $response );
			$order->save ();
			if ($response[ 'paymentStatus' ] === 'SUCCESS') {
				$order->payment_complete ( $response[ 'orderCode' ] );
				$order->add_order_note ( __ ( 'Recurring payment charged in Worldpay.', 'worldpay' ) );
			} else if ($response[ 'paymentStatus' ] === 'AUTHORIZED') {
				$order->update_status ( apply_filters ( 'worldpay_authorized_order_status', $this->get_option ( 'wcs_authorized_status' ) ) );
				$order->add_order_note ( __ ( 'Recurring payment authorized in Worldpay.', 'worldpay' ) );
			} else if ($response[ 'paymentStatus' ] === 'FAILED') {
				throw new WorldpayException ( $response[ 'paymentStatusReason' ] );
			}
		} catch ( WorldpayException $e ) {
			$order->add_order_note ( sprintf ( __ ( 'Recurring payment failed. Reason: %s', 'worldpay' ), $e->getMessage () ) );
			$order->update_status ( 'failed' );
			return;
		}
	}

	/**
	 *
	 * @param int $order_id        	
	 * @param WC_Order $order        	
	 */
	public function capture_authorized_order($order_id, $order) {
		if ($order->get_meta ( '_worldpay_payment_status' ) === 'AUTHORIZED') {
			$result = $this->capture_charge ( $order, $order->get_total () );
			if (is_wp_error ( $result )) {
				$order->add_order_note ( $result->get_error_message () );
			}
		}
	}

	/**
	 * Filter payment tokens based on the current environment.
	 *
	 * @param WC_Payment_Token[] $tokens        	
	 * @param int $user_id        	
	 * @param string $gateway_id        	
	 */
	public function filter_payment_tokens($tokens, $user_id, $gateway_id) {
		$env = worldpay_get_environment ();
		foreach ( $tokens as $i => $token ) {
			if ($token->get_gateway_id () === $this->id) {
				if ($env !== $token->get_meta ( 'environment' )) {
					unset ( $tokens[ $i ] );
				}
			}
		}
		return $tokens;
	}
}
WC_Online_Worldpay_Payment_Gateway::init ();