<?php
use Worldpay\WorldpayException;

if (! class_exists ( 'WC_Payment_Gateway' )) {
	return;
}
class WC_Online_Worldpay_PayPal_Gateway extends WC_Online_Worldpay_Payment_Gateway {

	public static function init() {
		add_filter ( 'woocommerce_payment_gateways', array( 
				__CLASS__, 'add_gateway' 
		) );
	}

	public static function add_gateway($gateways) {
		$gateways[] = __CLASS__;
		return $gateways;
	}

	public function __construct() {
		$this->id = 'online_worldpay_paypal';
		$this->title = $this->get_option ( 'title' );
		$this->method_title = __ ( 'Online Worldpay PayPal', 'worldpay' );
		$this->method_description = __ ( 'Accept PayPal through your Worldpay account.', 'worldpay' );
		parent::__construct ();
		$this->icon = worldpay ()->assets_url () . 'img/paypal.svg';
	}

	public function add_actions() {
		parent::add_actions ();
		add_filter ( 'worldpay_paypal_gateway_settings', array( 
				$this, 'add_subscription_settings' 
		) );
		add_action ( 'worldpay_register_frontend_scripts', array( 
				$this, 'register_scripts' 
		), 10, 4 );
		add_action ( 'worldpay_enqueue_frontend_media', array( 
				$this, 'enqueue_media' 
		), 10, 2 );
		add_filter ( 'worldpay_localize_paypal_script', array( 
				$this, 'localize_paypal_script' 
		), 10, 2 );
		add_filter ( 'worldpay_process_payment_args', array( 
				$this, 'payment_args' 
		), 10, 3 );
		add_action ( 'worldpay_query_paypal_success', array( 
				$this, 'process_paypal_response' 
		) );
		add_action ( 'worldpay_query_paypal_pending', array( 
				$this, 'process_paypal_response' 
		) );
		add_action ( 'worldpay_query_paypal_cancel', array( 
				$this, 'process_paypal_response' 
		) );
		add_action ( 'worldpay_query_paypal_failure', array( 
				$this, 'process_paypal_response' 
		) );
		add_filter ( 'worldpay_process_subscription_payment_args', array( 
				$this, 'subscription_payment_args' 
		), 10, 3 );
		// add_filter ( 'woocommerce_payment_methods_list_item', 'worldpay_payment_methods_list_item_paypal', 10, 2 );
		add_filter ( 'woocommerce_saved_payment_methods_list', 'worldpay_saved_payment_methods_list_paypal', 99, 2 );
	}

	public function payment_fields() {
		worldpay_get_template ( 'paypal.php', array( 
				'has_methods' => false, 'methods' => array(), 
				'gateway' => $this 
		) );
		/*
		 * if (is_add_payment_method_page ()) {
		 * worldpay_get_template ( 'paypal.php', array(
		 * 'has_methods' => false,
		 * 'methods' => array(), 'gateway' => $this
		 * ) );
		 * } else {
		 * $methods = WC_Payment_Tokens::get_customer_tokens ( wp_get_current_user ()->ID, $this->id );
		 * worldpay_get_template ( 'paypal.php', array(
		 * 'has_methods' => ( bool ) $methods,
		 * 'methods' => $methods,
		 * 'gateway' => $this
		 * ) );
		 * }
		 */
	}

	public function is_apm_order() {
		return true;
	}

	public function set_supports() {
		parent::set_supports ();
		$no_support = array( 'add_payment_method', 
				'subscription_payment_method_change_customer' 
		);
		if (! $this->is_active ( 'wcs_paypal_enabled' )) {
			$no_support[] = 'subscriptions';
		}
		foreach ( $no_support as $type ) {
			$key = array_search ( $type, $this->supports );
			unset ( $this->supports[ $key ] );
		}
	}

	public function payment_args($args, $order, $gateway_id) {
		if ($this->id === $gateway_id) {
			$args[ 'successUrl' ] = wp_nonce_url ( get_site_url () . '/worldpay/v1/paypal/success', 'paypal-action', '_paypal_nonce' );
			$args[ 'pendingUrl' ] = wp_nonce_url ( get_site_url () . '/worldpay/v1/paypal/pending', 'paypal-action', '_paypal_nonce' );
			$args[ 'failureUrl' ] = wp_nonce_url ( get_site_url () . '/worldpay/v1/paypal/failure', 'paypal-action', '_paypal_nonce' );
			// $args['errorURL'] = 'http://example.com/success?orderCode=2471fa63-912c-4dc7-90da-347176464617';
			$args[ 'cancelUrl' ] = wp_nonce_url ( get_site_url () . '/worldpay/v1/paypal/cancel', 'paypal-action', '_paypal_nonce' );
		}
		return $args;
	}

	public function process_paypal_response($type) {
		if (isset ( $_GET[ '_paypal_nonce' ] ) && wp_verify_nonce ( $_GET[ '_paypal_nonce' ], 'paypal-action' )) {
			$type = str_replace ( 'paypal_', '', $type );
			$data = WC ()->session->get ( 'worldpay_paypal_order', array() );
			$order = wc_get_order ( $data[ 'order_id' ] );
			$redirect = '';
			try {
				switch ($type) {
					case 'success' :
					case 'pending' :
						$response = $this->worldpay->getOrder ( $data[ 'response' ][ 'orderCode' ] );
						$test = $this->worldpay->getStoredCardDetails ( $response[ 'token' ] );
						$this->add_order_meta ( $order, $response );
						if ($type === 'success') {
							if ($response[ 'paymentStatus' ] === 'SUCCESS') {
								$order->payment_complete ( $response[ 'orderCode' ] );
							} elseif ($response[ 'paymentStatus' ] === 'AUTHORIZED') {
								$order->update_status ( apply_filters ( 'worldpay_authorized_order_status', $this->get_option ( 'authorize_status' ), $order, $this->id ) );
							}
						} else {
							$order->update_status ( apply_filters ( 'worldpay_paypal_pending_status', $this->get_option ( 'pending_status' ) ) );
						}
						if (worldpay_wcs_active () && wcs_order_contains_subscription ( $order )) {
							$token = worldpay_create_wc_payment_token ( $response[ 'token' ], $response[ 'paymentResponse' ], $this->id, array( 
									'order_code' => $response[ 'orderCode' ], 
									'order_id' => $order->get_id () 
							) );
							$token->save ();
						}
						WC ()->cart->empty_cart ();
						$redirect = $redirect = $order->get_checkout_order_received_url ();
						break;
					case 'cancel' :
						wc_add_notice ( __ ( 'Your PayPal payment has been canceled.', 'worldpay' ), 'notice' );
						$redirect = wc_get_checkout_url ();
						break;
					case 'failure' :
						$order->update_status ( 'failed' );
						$order->add_order_note ( __ ( 'Customer\'s PayPal payment failed.', 'worldpay' ) );
						throw new WorldpayException ( __ ( 'PayPal authentication failed.', 'worldpay' ) );
				}
			} catch ( \Worldpay\WorldpayException $e ) {
				$order->save ();
				wc_add_notice ( sprintf ( __ ( 'Your payment could not be processed. Reason: %s', 'worldpay' ), $e->getMessage () ), 'error' );
				worldpay_get_template ( 'checkout/checkout-error.php' );
			}
			$order->save ();
			wp_redirect ( $redirect );
			exit ();
		}
	}

	public function init_form_fields() {
		$this->form_fields = apply_filters ( 'worldpay_paypal_gateway_settings', include 'settings/worldpay-paypal-settings.php' );
	}

	public function add_subscription_settings($settings) {
		if (worldpay_wcs_active ()) {
			$settings = array_merge ( $settings, include 'settings/worldpay-wcs-settings.php', include 'settings/worldpay-wcs-paypal-settings.php' );
		}
		return $settings;
	}

	public function localize_paypal_script($data, $handle) {
		if ($this->is_available ()) {
			$data[ 'gateway' ] = $this->id;
			$data[ 'button_html' ] = worldpay_get_template_html ( 'paypal-button.php' );
			$data[ 'client_key' ] = worldpay_get_client_key ();
			$data[ 'reusable' ] = is_add_payment_method_page () || worldpay_cart_has_subscriptions () || isset ( $_GET[ 'change_payment_method' ] );
		}
		return $data;
	}

	/**
	 *
	 * @param WC_Worldpay_Frontend_Scripts $class        	
	 * @param string $path        	
	 * @param string $suffix        	
	 * @param string $suffix        	
	 */
	public function register_scripts($class, $path, $prefix, $suffix) {
		$class::register_script ( 'paypal', $path . 'js/frontend/paypal' . $suffix . '.js', array( 
				'jquery', $class::$prefix . 'external-js' 
		) );
		// $class::register_style ( 'paypal', $path . 'css/paypal.css' );
	}

	/**
	 *
	 * @param WC_Worldpay_Frontend_Scripts $class        	
	 * @param string $prefix        	
	 */
	public function enqueue_media($class, $prefix) {
		if ($this->is_available ()) {
			if (is_checkout () || is_add_payment_method_page ()) {
				wp_enqueue_script ( $prefix . 'paypal' );
				// wp_enqueue_style ( $prefix . 'paypal' );
			}
		}
	}

	/**
	 *
	 * @param array $args        	
	 * @param WC_Order $order        	
	 * @param string $gateway_id        	
	 */
	public function subscription_payment_args($args, $order, $gateway_id) {
		if ($gateway_id === $this->id) {
			$subscription_id = get_post_meta ( $order->get_id (), '_subscription_renewal', true );
			$subscription = wcs_get_subscription ( $subscription_id );
			$original_order_id = $subscription->get_parent_id ();
			if ($original_order_id) {
				$args[ 'customerIdentifiers' ] = array( 
						'originalOrderCode' => get_post_meta ( $original_order_id, '_transaction_id', true ) 
				);
			}
		}
		return $args;
	}
}
WC_Online_Worldpay_PayPal_Gateway::init ();