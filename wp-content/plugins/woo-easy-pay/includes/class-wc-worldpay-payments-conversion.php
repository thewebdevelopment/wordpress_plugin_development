<?php
class WC_Worldpay_Payments_Conversion {

	public static function init() {
		add_action ( 'init', array( __CLASS__, 
				'convert_payment_methods' 
		), 80 );
	}

	public static function convert_payment_methods() {
		if (is_user_logged_in () && did_action ( 'woocommerce_loaded' )) {
			$user = wp_get_current_user ();
			$env = worldpay_get_environment ();
			$env = 'live' === $env ? 'production' : 'sandbox';
			$option_name = "onlineworldpay_{$env}_paymentmethods";
			
			$old_methods = get_user_meta ( $user->ID, $option_name, true );
			if ($old_methods) {
				$gateways = WC ()->payment_gateways ()->payment_gateways ();
				$card_gateway = $gateways[ 'online_worldpay' ];
				/**
				 *
				 * @var \Worldpay\Worldpay
				 */
				$worldpay = $card_gateway->worldpay ();
				if ($worldpay) {
					$max_time = time () + 10; // add 10 seconds to current time.
					foreach ( $old_methods as $token => $method ) {
						/**
						 * Don't want the conversion of payment methods to take too long.
						 * Do it in steps if necessary.
						 */
						if (time () < $max_time) {
							try {
								$card = $worldpay->getStoredCardDetails ( $token );
								$payment_token = worldpay_create_wc_payment_token ( $token, $card, $card_gateway->id );
								$payment_token->save ();
								unset ( $old_methods[ $token ] );
							} catch ( \Worldpay\WorldpayException $e ) {
								worldpay_log_error ( sprintf ( __ ( 'User %s payment methods could not be converted. Reason: %s', 'worldpay' ), $user->ID, $e->getMessage () ) );
								if ($e->getHttpStatusCode () == 404) {
									unset ( $old_methods[ $token ] );
								}
							}
						}
					}
					// delete old option value now that all payment methods are converted.
					if (empty ( $old_methods )) {
						delete_user_meta ( $user->ID, $option_name );
					} else {
						update_user_meta ( $user->ID, $option_name, $old_methods );
					}
				}
			}
		}
	}
}
WC_Worldpay_Payments_Conversion::init ();