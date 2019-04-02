<?php
if (! defined ( 'ABSPATH' )) {
	exit (); // Exit if accessed directly
}
if (! class_exists ( 'WC_Payment_Token' )) {
	exit ();
}
class WC_Payment_Token_Worldpay_PayPal extends WC_Payment_Token {

	protected $type = 'Worldpay_PayPal';

	protected $extra_data = array( 'order_id' => '', 
			'original_order_code' => '' 
	);

	public function set_original_order_code($value) {
		$this->set_prop ( 'original_order_code', $value );
	}

	public function get_original_order_code($context = '') {
		return $this->get_prop ( 'original_order_code', $context );
	}

	public function set_order_id($value) {
		$this->set_prop ( 'order_id', $value );
	}

	public function get_order_id($context = '') {
		return $this->get_prop ( 'order_id', $context );
	}
}