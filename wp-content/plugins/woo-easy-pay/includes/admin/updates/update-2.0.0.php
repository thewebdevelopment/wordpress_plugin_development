<?php
/**
 * Update for version 2.0.0
 */
$option_name = base64_encode ( 'onlineworldpay_payment_settings' );
if (get_option ( $option_name, false )) {
	// only perform update if the old settings option exists.
	$api_mappings = array( 
			'production_merchant_id' => 'live_merchant_id', 
			'production_service_key' => 'live_service_key', 
			'production_client_key' => 'live_client_key', 
			'sandbox_merchant_id' => 'test_merchant_id', 
			'sandbox_service_key' => 'test_service_key', 
			'sandbox_client_key' => 'test_client_key', 
			'environment' => 'environment' 
	);
	
	$cc_mappings = array( 'title_text' => 'title', 
			'enabled' => 'enabled', 
			'order_prefix' => 'order_prefix', 
			'order_suffix' => 'order_suffix', 
			'enable_3ds_secure' => '3ds_enabled', 
			'3ds_secure_page' => '3dsecure_page', 
			'production_card_template' => 'live_card_template', 
			'sandbox_card_template' => 'test_card_template', 
			'production_cvc_template' => 'live_cvc_template', 
			'sandbox_cvc_template' => 'test_cvc_template' 
	);
	
	$pp_mappings = array( 'enable_paypal' => 'enabled', 
			'order_prefix' => 'order_prefix', 
			'order_suffix' => 'order_suffix' 
	);
	
	$old_settings = maybe_unserialize ( base64_decode ( get_option ( $option_name ) ) );
	/**
	 * ******************************* API Settings ******************************************************
	 */
	$api_settings = get_option ( 'woocommerce_online_worldpay_api_settings', array() );
	foreach ( $api_mappings as $old_key => $new_key ) {
		switch ($old_key) {
			case 'environment' :
				$api_settings[ $new_key ] = $old_settings[ $old_key ] === 'sandbox' ? 'test' : 'live';
				break;
			default :
				$api_settings[ $new_key ] = $old_settings[ $old_key ];
				break;
		}
	}
	update_option ( 'woocommerce_online_worldpay_api_settings', $api_settings );
	
	/**
	 * *********************** CC Settings ************************************************************
	 */
	$cc_settings = get_option ( 'woocommerce_online_worldpay_settings', array() );
	foreach ( $cc_mappings as $old_key => $new_key ) {
		switch ($old_key) {
			default :
				$cc_settings[ $new_key ] = $old_settings[ $old_key ];
				break;
		}
	}
	$cc_settings[ 'form_type' ] = 'template_form';
	update_option ( 'woocommerce_online_worldpay_settings', $cc_settings );
	
	/**
	 * ******************************** PayPal Settings ***********************************************
	 */
	$pp_settings = get_option ( 'woocommerce_online_worldpay_paypal_settings', array() );
	foreach ( $pp_mappings as $old_key => $new_key ) {
		switch ($old_key) {
			default :
				$pp_settings[ $new_key ] = $old_settings[ $old_key ];
				break;
		}
	}
	update_option ( 'woocommerce_online_worldpay_paypal_settings', $pp_settings );
	/**
	 * ******************************* 3DS Settings *************************************************
	 */
	// add new 3ds short code to existing page.
	$page_id = $old_settings[ '3ds_secure_page' ];
	if ($page_id) {
		wp_update_post ( array( 'ID' => $page_id, 
				'post_content' => '[online_worldpay_3ds]' 
		) );
	}
	
	/**
	 * ************************ WCS Updates **********************************************************
	 */
	global $wpdb;
	$query = "UPDATE {$wpdb->prefix}postmeta SET meta_value = %s WHERE meta_key = %s AND meta_value = %s";
	$query = $wpdb->prepare ( $query, 'online_worldpay', '_payment_method', 'online_worldpay_gateway' );
	$wpdb->query ( $query );
}
update_option ( 'worldpay_donations_message', true );
add_action ( 'admin_notices', function () {
	?>
<div class="notice notice-info">
	<p style="font-size: 16px;">
							<?php printf(__('Version 2.0.0 is a major update. Please check all your <a href="%1$s">Settings</a> and register for our new <a href="%1$s">Support</a> feature.', 'worldpay'), admin_url( 'admin.php?page=wc-settings&tab=checkout&section=online_worldpay_api'))?>
						</p>
</div>
<?php
} );