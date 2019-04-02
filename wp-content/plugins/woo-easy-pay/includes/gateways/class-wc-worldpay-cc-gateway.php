<?php
use Worldpay\Worldpay;
use Worldpay\WorldpayException;
use Worldpay\Utils;

if (! class_exists ( 'WC_Online_Worldpay_Payment_Gateway' )) {
	return;
}
class WC_Online_Worldpay_CC_Payment_Gateway extends WC_Online_Worldpay_Payment_Gateway {

	/**
	 *
	 * @var Worldpay
	 */
	public $worldpay;

	public static $ID = 'online_worldpay';

	public $payment_token;

	public $service_key = '';

	public $client_key = '';

	public $update_payment_method_request = false;

	/**
	 * Initializes the filters and functionality of this gateway
	 */
	public static function init() {
		add_filter ( 'woocommerce_payment_gateways', __CLASS__ . '::add_gateway' );
	}

	/**
	 * Adds the WC_Online_Worldpay_CC_Payment_Gateway class to the array of WC payment gateways.
	 *
	 * @param array $gateways        	
	 */
	public static function add_gateway($gateways) {
		$gateways[] = __CLASS__;
		return $gateways;
	}

	public function __construct() {
		$this->id = 'online_worldpay';
		$this->method_title = __ ( 'Online Worldpay CC Gateway', 'online-worldpay' );
		$this->method_description = __ ( 'Functionality for the Online Worldpay credit card payment gateway.', 'online-worldpay' );
		parent::__construct ();
		$this->title = $this->get_option ( 'title' );
		$this->description = $this->get_option ( 'description' );
	}

	public function add_actions() {
		parent::add_actions ();
		add_action ( 'worldpay_register_frontend_scripts', array( 
				$this, 'register_scripts' 
		), 10, 4 );
		add_action ( 'worldpay_enqueue_frontend_media', array( 
				$this, 'enqueue_media' 
		), 10, 2 );
		add_filter ( 'worldpay_localize_template-form_script', array( 
				$this, 'localize_template_form_script' 
		), 10, 2 );
		add_filter ( 'worldpay_localize_own-form_script', array( 
				$this, 'localize_own_form_script' 
		), 10, 2 );
		add_filter ( 'worldpay_localize_payment-methods_script', array( 
				$this, 'localize_payment_methods_script' 
		), 10, 2 );
		add_filter ( 'worldpay_process_payment_args', array( 
				$this, 'payment_args' 
		), 10, 3 );
		add_action ( 'worldpay_query_process_3ds_order', array( 
				$this, 'process_3ds_order' 
		) );
		add_action ( 'worldpay_cc_gateway_settings', array( 
				$this, 'add_subscription_settings' 
		) );
		add_action ( 'woocommerce_subscription_payment_method_updated_to_' . $this->id, array( 
				$this, 'subscription_payment_method_updated' 
		), 10, 1 );
	}

	public function payment_fields() {
		echo wpautop ( wptexturize ( $this->get_description () ) );
		$card_template = $this->get_option ( 'form_type' ) === 'template_form' ? 'hosted-form.php' : 'custom-form.php';
		$cvc_template = $this->get_option ( 'form_type' ) === 'template_form' ? 'hosted-cvc.php' : 'custom-cvc.php';
		if (is_add_payment_method_page ()) {
			worldpay_payment_nonce_field ( $this );
			worldpay_payment_token_type_field ( $this );
			worldpay_get_template ( $card_template, array( 
					'gateway' => $this, 
					'has_methods' => false 
			) );
		} else {
			$methods = WC_Payment_Tokens::get_customer_tokens ( wp_get_current_user ()->ID, $this->id );
			worldpay_get_template ( 'checkout/payment-method.php', array( 
					'gateway' => $this, 
					'methods' => $methods, 
					'has_methods' => ( bool ) $methods, 
					'card_template' => $card_template, 
					'cvc_template' => $cvc_template 
			) );
		}
	}

	/**
	 *
	 * @param array $args        	
	 * @param WC_Order $order        	
	 * @param string $gateway_id        	
	 */
	public function payment_args($args, $order, $gateway_id) {
		if ($this->id === $gateway_id) {
			$args[ 'is3DSOrder' ] = $this->is_active ( '3ds_enabled' );
		}
		return $args;
	}

	/**
	 * Process a WC_Order for which the total is zero.
	 *
	 * @param WC_Order $order        	
	 */
	protected function process_zero_payment_order($order) {
		/**
		 * Save the meta data used to process future payments.
		 */
		if (worldpay_wcs_active ()) {
			if (wcs_order_contains_subscription ( $order )) {
			}
		}
	}

	/**
	 * Complete payment for the 3DS order.
	 */
	public function process_3ds_order() {
		if (isset ( $_GET[ '_3ds_nonce' ] ) && wp_verify_nonce ( $_GET[ '_3ds_nonce' ], 'process-3ds-order' )) {
			$data = WC ()->session->get ( 'worldpay_3ds_data' );
			$order = wc_get_order ( $data[ 'order_id' ] );
			$response_code = isset ( $_POST[ 'PaRes' ] ) ? $_POST[ 'PaRes' ] : '';
			try {
				if (empty ( $response_code )) {
					throw new WorldpayException ( __ ( '3DS response cannot be empty.', 'worldpay' ) );
				}
				
				Utils::setThreeDSShopperObject ( array_merge ( Utils::getThreeDSShopperObject (), array( 
						'shopperSessionId' => $data[ 'session_id' ] 
				) ) );
				
				$response = $this->worldpay->authorize3DSOrder ( $data[ 'order_code' ], $response_code );
				$order->add_meta_data ( '_worldpay_payment_status', $response[ 'paymentStatus' ], true );
				$order->save ();
				switch ($response[ 'paymentStatus' ]) {
					case 'SUCCESS' :
						$order->payment_complete ( $data[ 'order_code' ] );
						break;
					case 'AUTHORIZED' :
						$order->update_status ( apply_filters ( 'worldpay_authorized_order_status', 'on-hold' ) );
						break;
					case 'FAILED' :
					default :
						$order->update_status ( apply_filters ( 'worldpay_failed_order_status', 'failed' ) );
						$order->add_order_note ( __ ( '3D secure authentication of customer\'s credit card failed.', 'worldpay' ) );
						throw new WorldpayException ( __ ( 'Verification of your payment method failed.', 'worldpay' ) );
				}
				WC ()->cart->empty_cart ();
				wp_redirect ( $order->get_checkout_order_received_url () );
				exit ();
			} catch ( WorldpayException $e ) {
				wc_add_notice ( sprintf ( __ ( 'Your payment could not be processed. Reason: %s', 'worldpay' ), $e->getMessage () ), 'error' );
				worldpay_get_template ( 'checkout/checkout-error.php' );
				die ();
			}
		}
	}

	public function get_icon() {
		worldpay_get_template ( 'card-icons.php', array( 
				'icons' => $this->get_option ( 'accepted_cards' ) 
		) );
	}

	public function init_form_fields() {
		$this->form_fields = apply_filters ( 'worldpay_cc_gateway_settings', include 'settings/worldpay-cc-settings.php', $this->id );
	}

	public function localize_template_form_script($data, $handle) {
		if ($this->is_available ()) {
			$environment = $this->get_option ( 'environment' );
			$data = array( 'gateway' => $this->id, 
					'3ds_enabled' => $this->is_active ( '3ds_enabled' ), 
					'client_key' => $this->get_client_key (), 
					'reusable' => is_add_payment_method_page () || worldpay_cart_has_subscriptions () || isset ( $_GET[ 'change_payment_method' ] ), 
					'form_template' => $this->get_option ( "{$environment}_card_template" ), 
					'cvc_template' => $this->get_option ( "{$environment}_cvc_template" ), 
					'cvc_required' => $this->is_active ( 'cvc_saved_card' ) 
			);
		}
		return $data;
	}

	public function localize_own_form_script($data, $handle) {
		if ($this->is_available ()) {
			$environment = $this->get_option ( 'environment' );
			$user = wp_get_current_user ();
			$tokens = WC_Payment_Tokens::get_customer_tokens ( $user->ID, $this->id );
			$data = array( 'gateway' => $this->id, 
					'3ds_enabled' => $this->is_active ( '3ds_enabled' ), 
					'client_key' => $this->get_client_key (), 
					'reusable' => is_add_payment_method_page () || worldpay_cart_has_subscriptions () || isset ( $_GET[ 'change_payment_method' ] ), 
					'cards' => worldpay_get_card_icon_urls (), 
					'cvc_required' => $this->is_active ( 'cvc_saved_card' ), 
					'has_saved_methods' => ! empty ( $tokens ) && ! is_add_payment_method_page () 
			);
		}
		return $data;
	}

	public function localize_payment_methods_script($data, $handle) {
		$url = worldpay ()->assets_url () . 'img/cards/';
		$template = '<img class="worldpay-method-icon" src="%s"/>';
		$data[ 'cards' ] = worldpay_get_card_icons ();
		$data[ 'gateways' ] = array( 'online_worldpay', 
				'online_worldpay_paypal' 
		);
		return $data;
	}

	public function add_subscription_settings($settings) {
		if (worldpay_wcs_active ()) {
			$settings = array_merge ( $settings, include 'settings/worldpay-wcs-settings.php' );
		}
		return $settings;
	}

	/**
	 *
	 * @param WC_Worldpay_Frontend_Scripts $class        	
	 * @param string $path        	
	 * @param string $suffix        	
	 * @param string $suffix        	
	 */
	public function register_scripts($class, $path, $prefix, $suffix) {
		$class::register_script ( 'template-form', $path . 'js/frontend/template-form' . $suffix . '.js', array( 
				'jquery', $class::$prefix . 'external-js' 
		) );
		$class::register_script ( 'own-form', $path . 'js/frontend/own-form' . $suffix . '.js', array( 
				'jquery', $class::$prefix . 'external-js' 
		) );
		$class::register_style ( 'custom-forms', $path . 'css/custom-forms.css' );
	}

	/**
	 *
	 * @param WC_Worldpay_Frontend_Scripts $class        	
	 * @param string $prefix        	
	 */
	public function enqueue_media($class, $prefix) {
		if ($this->is_available ()) {
			if (is_checkout () || is_add_payment_method_page ()) {
				if ($this->get_option ( 'form_type' ) === 'template_form') {
					wp_enqueue_script ( $prefix . 'template-form' );
				} else {
					wp_enqueue_script ( $prefix . 'own-form' );
					wp_enqueue_style ( $prefix . 'custom-forms' );
				}
			}
		}
	}
}
WC_Online_Worldpay_CC_Payment_Gateway::init ();