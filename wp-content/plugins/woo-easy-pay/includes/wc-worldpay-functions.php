<?php

/**
 * Returns the provided template;
 * @param unknown $template
 * @param array $args
 */
function worldpay_get_template($template, $args = array()) {
	$default_path = worldpay ()->template_path ();
	$template_path = 'woo-easy-pay/';
	return wc_get_template ( $template, $args, $template_path, $default_path );
}

/**
 * Returns the provided template as a string
 *
 * @param string $template        	
 * @param array $args        	
 * @return string
 */
function worldpay_get_template_html($template, $args = array()) {
	$default_path = worldpay ()->template_path ();
	$template_path = 'woo-easy-pay/';
	return wc_get_template_html ( $template, $args, $template_path, $default_path );
}

/**
 * Return the ID of the page that contains the 3DS shortcode
 *
 * @since 2.6.40
 */
function worldpay_get_3ds_shortcode_page() {
	$settings = get_option ( 'woocommerce_online_worldpay_settings', array() );
	return $settings[ '3dsecure_page' ];
}

/**
 */
function worldpay_get_blog_pages_as_options() {
	$pages = get_posts ( array( 'posts_per_page' => - 1, 
			'post_type' => 'page', 
			'post_status' => 'publish' 
	) );
	$options = array();
	foreach ( $pages as $page ) {
		$options[ $page->ID ] = $page->post_title;
	}
	return $options;
}

function worldpay_get_settlement_currencies() {
	return array( 
			'GBP' => __ ( 'Pounds Sterling', 'onlineworldpay' ), 
			'EUR' => __ ( 'Euros', 'onlineworldpay' ), 
			'USD' => __ ( 'US Dollars', 'onlineworldpay' ), 
			'CAD' => __ ( 'Canadian Dollars', 'onlineworldpay' ), 
			'DKK' => __ ( 'Danish Krone', 'onlineworldpay' ), 
			'HKD' => __ ( 'Hong Kong Dollar', 'onlineworldpay' ), 
			'NOK' => __ ( 'Norwegian Krone', 'onlineworldpay' ), 
			'SEK' => __ ( 'Swedish Krona', 'onlineworldpay' ), 
			'SGD' => __ ( 'Singapore Dollar', 'onlineworldpay' ) 
	);
}

function worldpay_template_form_enabled() {
	$settings = get_option ( 'woocommerce_online_worldpay_settings', array() );
	return ! empty ( $settings[ 'form_type' ] ) && $settings[ 'form_type' ] === 'template_form';
}

function worldpay_live_active() {
	$settings = get_option ( 'woocommerce_online_worldpay_settings', array( 
			'environment' => 'test' 
	) );
	return $settings[ 'environment' ] === 'live';
}

/**
 * Output input field that is used to store a payment nonce.
 *
 * @param WC_Payment_Gateway $gateway        	
 * @param string $value        	
 */
function worldpay_payment_nonce_field($gateway, $value = '') {
	printf ( '<input type="hidden" class="worldpay-payment-nonce" id="%1$s_payment_nonce" name="%1$s_payment_nonce" data-nonce-gateway="%1$s" value="%2$s"/>', $gateway->id, $value );
}

/**
 * Output input field that is used to store a payment token.
 *
 * @param WC_Payment_Gateway $gateway        	
 * @param string $value        	
 */
function worldpay_payment_token_field($gateway, $value = '') {
	printf ( '<input type="hidden" class="worldpay-payment-token" id="%1$s_payment_token" name="%1$s_payment_token" data-token-gateway="%1$s" value="%2$s"/>', $gateway->id, $value );
}

/**
 * Output input field that is used to store a payment token type.
 *
 * @param WC_Payment_Gateway $gateway        	
 * @param string $value        	
 */
function worldpay_payment_token_type_field($gateway, $value = '') {
	printf ( '<input type="hidden" id="%1$s_payment_token_type" name="%1$s_payment_token_type" data-token-type-gateway="%1$s" value="%2$s"/>', $gateway->id, $value );
}

/**
 * Log the error message to the Worldpay log file.
 *
 * @param unknown $message        	
 */
function worldpay_log_error($message) {
	worldpay_log ( WC_Log_Levels::ERROR, $message );
}

/**
 * Log the info message to the Worldpay log file.
 *
 * @param unknown $message        	
 */
function worldpay_log_info($message) {
	worldpay_log ( WC_Log_Levels::INFO, $message );
}

/**
 * Log the message to the Worldpay log file.
 *
 * @param unknown $level        	
 * @param unknown $message        	
 */
function worldpay_log($level, $message) {
	$option = get_option ( 'woocommerce_online_worldpay_api_settings', array( 
			'debug_enabled' => 'yes' 
	) );
	if (isset ( $option[ 'debug_enabled' ] ) && $option[ 'debug_enabled' ] === 'yes') {
		$log = wc_get_logger ();
		$log->log ( $level, $message, array( 
				'source' => 'worldpay' 
		) );
	}
}

/**
 * Wrapper for WCS to check if the cart contains subscriptions.
 *
 * @return boolean
 */
function worldpay_cart_has_subscriptions() {
	$has_subscriptions = false;
	if (worldpay_wcs_active ()) {
		$has_subscriptions = WC_Subscriptions_Cart::cart_contains_subscription ();
	}
	return $has_subscriptions;
}

/**
 * True if WooCommerce Subscriptions is active.
 * Should not be called before plugins_loaded
 *
 * @return boolean
 */
function worldpay_wcs_active() {
	return class_exists ( 'WC_Subscriptions' );
}

/**
 * Return a card type that translates the Worldpay card type to a plugin readable card type.
 *
 * @param string $card_type        	
 * @return string
 */
function worldpay_get_card_type($card_type) {
	$type = $card_type;
	if (preg_match ( '/visa/i', $card_type )) {
		$type = 'visa';
	} elseif (preg_match ( '/mastercard/i', $card_type )) {
		$type = 'mastercard';
	} elseif (preg_match ( '/maestro/i', $card_type )) {
		$type = 'maestro';
	} elseif (preg_match ( '/amex/i', $card_type )) {
		$type = 'amex';
	} elseif (preg_match ( '/jcb/i', $card_type )) {
		$type = 'jcb';
	} elseif (preg_match ( '/diners/i', $card_type )) {
		$type = 'diners';
	}
	return $type;
}

/**
 * Return the exponent that indicates the smalles measuring point for a currency.
 * <div>Country exponents used from <a href="https://en.wikipedia.org/wiki/ISO_4217">Wiki ISO 4712</a>.
 *
 * @param string $currency        	
 */
function worldpay_get_currency_code_exponent($currency = 'GBP') {
	$array = array( 'AED' => 2, 'ARS' => 2, 'AUD' => 2, 
			'BDT' => 2, 'BGN' => 2, 'BRL' => 2, 'CAD' => 2, 
			'CHF' => 2, 'CLP' => 0, 'CNY' => 2, 'COP' => 2, 
			'CZK' => 2, 'DKK' => 2, 'DOP' => 2, 'EGP' => 2, 
			'EUR' => 2, 'GBP' => 2, 'HKD' => 2, 'HRK' => 2, 
			'HUF' => 2, 'IDR' => 2, 'ILS' => 2, 'INR' => 2, 
			'ISK' => 0, 'JPY' => 0, 'KES' => 2, 'LAK' => 2, 
			'KRW' => 0, 'MXN' => 2, 'MYR' => 2, 'NGN' => 2, 
			'NOK' => 2, 'NPR' => 2, 'NZD' => 2, 'PHP' => 2, 
			'PKR' => 2, 'PLN' => 2, 'PYG' => 0, 'RON' => 2, 
			'RUB' => 2, 'SEK' => 2, 'SGD' => 2, 'THB' => 2, 
			'TRY' => 2, 'TWD' => 2, 'UAH' => 2, 'USD' => 2, 
			'VND' => 0, 'ZAR' => 2 
	);
	return $array[ $currency ];
}

/**
 * Create a WC Payment Token from an array of Worldpay attributes related to a payment method.
 *
 * @param string $token        	
 * @param array $payment        	
 * @param string $gateway_id        	
 * @param array $meta_data        	
 * @return WC_Payment_Token_CC
 */
function worldpay_create_wc_payment_token($token, $payment_method, $gateway_id, $meta_data = array()) {
	$user = wp_get_current_user ();
	$payment_token = null;
	if ($payment_method[ 'type' ] === 'APM' && $payment_method[ 'apmName' ] === 'paypal') {
		$payment_token = new WC_Payment_Token_Worldpay_PayPal ();
		$payment_token->set_original_order_code ( isset ( $meta_data[ 'order_code' ] ) ? $meta_data[ 'order_code' ] : '' );
		$payment_token->set_order_id ( isset ( $meta_data[ 'order_id' ] ) ? $meta_data[ 'order_id' ] : '' );
	} else {
		$payment_token = new WC_Payment_Token_CC ();
		$payment_token->set_card_type ( worldpay_get_card_type ( $payment_method[ 'cardType' ] ) );
		$payment_token->set_expiry_month ( $payment_method[ 'expiryMonth' ] );
		$payment_token->set_expiry_year ( $payment_method[ 'expiryYear' ] );
		$matches = array();
		preg_match ( '/[\d]+/', $payment_method[ 'maskedCardNumber' ], $matches );
		$payment_token->set_last4 ( $matches[ 0 ] );
	}
	$payment_token->set_gateway_id ( $gateway_id );
	$payment_token->set_user_id ( $user->ID );
	$payment_token->set_token ( $token );
	$payment_token->add_meta_data ( 'environment', worldpay_get_environment (), true );
	return $payment_token;
}

/**
 * Return the currently active environment for Worldpay
 */
function worldpay_get_environment() {
	$settings = get_option ( 'woocommerce_online_worldpay_api_settings', array( 
			'environment' => 'live' 
	) );
	return $settings[ 'environment' ];
}

/**
 *
 * @since 2.0.1
 * @param string $env        	
 */
function worldpay_get_merchant_id($env = '') {
	$env = empty ( $env ) ? worldpay_get_environment () : $env;
	$settings = get_option ( 'woocommerce_online_worldpay_api_settings', array( 
			'test_merchant_id' => '', 
			'live_merchant_id' => '' 
	) );
	return $settings[ "{$env}_merchant_id" ];
}

/**
 * Return the service key for the provided environment.
 * If no environment is provided, then the
 * configured environment will be used.
 *
 * @param string $env        	
 */
function worldpay_get_service_key($env = '') {
	$env = empty ( $env ) ? worldpay_get_environment () : $env;
	$settings = get_option ( 'woocommerce_online_worldpay_api_settings', array( 
			'test_service_key' => '', 
			'live_service_key' => '' 
	) );
	return $settings[ "{$env}_service_key" ];
}

/**
 * Return the client key for the provided environment.
 * If no environment is provided, then the
 * configured environment will be used.
 *
 * @param string $env        	
 */
function worldpay_get_client_key($env = '') {
	$env = empty ( $env ) ? worldpay_get_environment () : $env;
	$settings = get_option ( 'woocommerce_online_worldpay_api_settings', array() );
	return $settings[ "{$env}_client_key" ];
}

/**
 * Return an array of card icons.
 *
 * @return mixed
 */
function worldpay_get_card_icons() {
	$url = worldpay ()->assets_url () . 'img/';
	$template = '<img class="worldpay-method-icon" src="%s"/>';
	return apply_filters ( 'worldpay_get_card_icons', array( 
			'visa' => sprintf ( $template, $url . 'cards/visa.svg' ), 
			'amex' => sprintf ( $template, $url . 'cards/amex.svg' ), 
			'discover' => sprintf ( $template, $url . 'cards/discover.svg' ), 
			'mastercard' => sprintf ( $template, $url . 'cards/master_card.svg' ), 
			'maestro' => sprintf ( $template, $url . 'cards/maestro.svg' ), 
			'jcb' => sprintf ( $template, $url . 'cards/jcb.svg' ), 
			'diners' => sprintf ( $template, $url . 'cards/maestro.svg' ), 
			'paypal' => sprintf ( $template, $url . 'paypal_short.svg' ) 
	) );
}

function worldpay_get_card_icon_urls() {
	$url = worldpay ()->assets_url () . 'img/cards/';
	return apply_filters ( 'worldpay_get_card_icon_urls', array( 
			'visa' => $url . 'visa.svg', 
			'amex' => $url . 'amex.svg', 
			'discover' => $url . 'discover.svg', 
			'mastercard' => $url . 'master_card.svg', 
			'maestro' => $url . 'maestro.svg', 
			'jcb' => $url . 'jcb.svg', 
			'dinersclub' => $url . 'diners_club_international.svg', 
			'cc_format' => $url . 'cc_format.svg' 
	) );
}

/**
 * Return a human readable representation of a credit card for display on the frontend.
 *
 * @param WC_Payment_Token $method        	
 */
function worldpay_get_card_display_name($method) {
	if ($method instanceof WC_Payment_Token_CC) {
		$text = sprintf ( __ ( '%1$s ending in %2$s', 'worldpay' ), wc_get_credit_card_type_label ( $method->get_card_type () ), $method->get_last4 () );
	} elseif ($method instanceof WC_Payment_Token_Worldpay_PayPal) {
		$text = __ ( 'PayPal', 'worldpay' );
	}
	return apply_filters ( 'worldpay_get_card_display_name', $text, $method );
}

function worldpay_get_month_options() {
	return apply_filters ( 'worldpay_get_month_options', array( 
			sprintf ( '<option value="%1$s">%2$s</option>', 1, __ ( 'Jan', 'worldpay' ) ), 
			sprintf ( '<option value="%1$s">%2$s</option>', 2, __ ( 'Feb', 'worldpay' ) ), 
			sprintf ( '<option value="%1$s">%2$s</option>', 3, __ ( 'Mar', 'worldpay' ) ), 
			sprintf ( '<option value="%1$s">%2$s</option>', 4, __ ( 'Apr', 'worldpay' ) ), 
			sprintf ( '<option value="%1$s">%2$s</option>', 5, __ ( 'May', 'worldpay' ) ), 
			sprintf ( '<option value="%1$s">%2$s</option>', 6, __ ( 'Jun', 'worldpay' ) ), 
			sprintf ( '<option value="%1$s">%2$s</option>', 7, __ ( 'Jul', 'worldpay' ) ), 
			sprintf ( '<option value="%1$s">%2$s</option>', 8, __ ( 'Aug', 'worldpay' ) ), 
			sprintf ( '<option value="%1$s">%2$s</option>', 9, __ ( 'Sep', 'worldpay' ) ), 
			sprintf ( '<option value="%1$s">%2$s</option>', 10, __ ( 'Oct', 'worldpay' ) ), 
			sprintf ( '<option value="%1$s">%2$s</option>', 11, __ ( 'Nov', 'worldpay' ) ), 
			sprintf ( '<option value="%1$s">%2$s</option>', 12, __ ( 'Dec', 'worldpay' ) ) 
	) );
}

function worldpay_get_year_options() {
	$year = date ( 'Y', time () );
	$size = 8;
	$options = array();
	for($i = 0; $i < $size; $i ++) {
		$options[ $year + $i ] = sprintf ( '<option value="%1$s">%1$s</option>', ( int ) $year + $i );
	}
	return apply_filters ( 'worldpay_get_year_options', $options );
}

/**
 * Return a human readable representation of a credit card
 *
 * @param WC_Payment_Token $token        	
 */
function worldpay_get_payment_method_title($token) {
	$text = $token->get_type ();
	if ($token instanceof WC_Payment_Token_CC) {
		$text = sprintf ( __ ( '%1$s ending in %2$s', 'worldpay' ), wc_get_credit_card_type_label ( $token->get_card_type () ), $token->get_last4 () );
	} elseif ($token instanceof WC_Payment_Token_Worldpay_PayPal) {
		$text = __ ( 'PayPal', 'worldpay' );
	}
	return apply_filters ( 'worldpay_get_payment_method_title', $text, $token );
}

function worldpay_saved_payment_methods_list_paypal($list, $customer_id) {
	unset ( $list[ 'worldpay_paypal' ] );
	return $list;
}

function worldpay_get_order_status_options() {
	$statuses = wc_get_order_statuses ();
	foreach ( $statuses as $status => $status_name ) {
	}
}

/**
 *
 * @param array $item        	
 * @param WC_Payment_Token $payment_token        	
 * @return array
 */
function worldpay_payment_methods_list_item_paypal($item, $payment_token) {
	if ('Worldpay_PayPal' !== $payment_token->get_type ()) {
		return $item;
	}
	$item[ 'method' ][ 'brand' ] = 'paypal';
	return $item;
}

/**
 * Returns a WC_Payment_Token based on the provided user_id, token, and gateway.
 *
 * @param string $token        	
 * @return mixed bool|WC_Payment_Token
 */
function worldpay_get_payment_token($user_id, $token, $gateway) {
	$tokens = WC_Payment_Tokens::get_tokens ( array( 
			'user_id' => $user_id, 'token' => $token, 
			'gateway_id' => $gateway 
	) );
	if ($tokens) {
		foreach ( $tokens as $payment_token ) {
			if ($payment_token->get_token () === $token) {
				return $payment_token;
			}
		}
	}
	return false;
}

/**
 * Return an array of custom forms available for the Worldpay CC Gateway.
 *
 * @return mixed
 */
function worldpay_custom_forms() {
	return apply_filters ( 'worldpay_custom_forms', array( 
			'basic-form.php' => __ ( 'Basic Form', 'worldpay' ), 
			'simple-form.php' => __ ( 'Simple Form', 'worldpay' ) 
	) );
}