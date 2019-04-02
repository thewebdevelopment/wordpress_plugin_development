<?php
defined ( 'ABSPATH' ) || exit ();

/**
 *
 * @author User
 *        
 */
class WC_Shortcode_Worldpay {

	public function __construct() {
		add_shortcode ( 'online_worldpay_3ds', array( 
				$this, 'output_3ds' 
		) );
	}

	/**
	 * Output the 3DS page.
	 */
	public function output_3ds() {
		if (( $session = WC ()->session ) == null) {
			return;
		}
		$data = $session->get ( 'worldpay_3ds_data', false );
		if (! $data) {
			wc_add_notice ( __ ( 'You do not have permission to access this page.', 'worldpay' ), 'error' );
		}
		if (wc_notice_count ( 'error' ) > 0) {
			wc_print_notices ();
		} else {
			worldpay_get_template ( 'checkout/3ds-form.php', array( 
					'data' => $data 
			) );
		}
	}
}
new WC_Shortcode_Worldpay ();