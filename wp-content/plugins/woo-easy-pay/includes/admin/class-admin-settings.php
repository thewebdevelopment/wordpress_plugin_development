<?php
class WC_Online_Worldpay_Admin_Settings {

	public static function init() {
		add_action ( 'woocommerce_update_options_checkout', array( 
				__CLASS__, 'save' 
		) );
	}

	public static function save() {
		global $current_section;
		if ($current_section && ! did_action ( 'woocommerce_update_options_checkout_' . $current_section )) {
			do_action ( 'woocommerce_update_options_checkout_' . $current_section );
		}
	}
}
WC_Online_Worldpay_Admin_Settings::init ();