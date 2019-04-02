<?php
class WC_Worldpay_Query {

	public function __construct() {
		add_action ( 'template_redirect', array( $this, 
				'process_query' 
		) );
		add_action ( 'init', array( $this, 
				'init_rewrite_rules' 
		) );
		add_action ( 'init', array( $this, 
				'init_rewrite_tags' 
		) );
	}

	public function init_rewrite_rules() {
		add_rewrite_rule ( '^worldpay/v1/3ds/process?', 'index.php?worldpay_action=process_3ds_order', 'top' );
		add_rewrite_rule ( '^worldpay/v1/paypal/success?', 'index.php?worldpay_action=paypal_success', 'top' );
		add_rewrite_rule ( '^worldpay/v1/paypal/pending?', 'index.php?worldpay_action=paypal_pending', 'top' );
		add_rewrite_rule ( '^worldpay/v1/paypal/failure?', 'index.php?worldpay_action=paypal_failure', 'top' );
		add_rewrite_rule ( '^worldpay/v1/paypal/cancel?', 'index.php?worldpay_action=paypal_cancel', 'top' );
	}

	public function init_rewrite_tags() {
		add_rewrite_tag ( '%worldpay_action%', '[\w\d_]+' );
	}

	public function process_query() {
		if (( $var = get_query_var ( 'worldpay_action', false ) ) != false) {
			// init gateways so any actions that should be called are initialized.
			WC ()->payment_gateways ();
			do_action ( 'worldpay_query_' . $var, $var );
		}
	}
}
new WC_Worldpay_Query ();