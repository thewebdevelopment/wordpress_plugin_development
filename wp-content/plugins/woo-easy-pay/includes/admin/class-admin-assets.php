<?php
class WC_OWP_Admin_Assets {

	public function __construct() {
		add_action ( 'admin_enqueue_scripts', array( 
				$this, 'enqueue_scripts' 
		) );
		add_action ( 'admin_footer', array( $this, 
				'footer_scripts' 
		) );
	}

	public function enqueue_scripts() {
		$screen = get_current_screen ();
		$screen_id = $screen ? $screen->id : '';
		$sections = array( 'online_worldpay', 
				'online_worldpay_paypal', 
				'online_worldpay_api', 
				'online_worldpay_webhook' 
		);
		wp_register_script ( 'worldpay-admin-settings', worldpay ()->assets_url () . 'js/admin/gateway-settings.js', array( 
				'jquery' 
		), worldpay ()->version (), true );
		wp_register_style ( 'worldpay-admin-style', worldpay ()->assets_url () . 'css/admin/admin.css', array(), OWP_VERSION );
		if (strpos ( $screen_id, 'wc-settings' ) != false) {
			if (isset ( $_REQUEST[ 'section' ] ) && in_array ( $_REQUEST[ 'section' ], $sections )) {
				wp_enqueue_script ( 'worldpay-admin-settings' );
				wp_enqueue_style ( 'worldpay-admin-style' );
				wp_register_script ( 'worldpay-help-widget', worldpay ()->assets_url () . 'js/admin/help-widget.js', array( 
						'jquery' 
				), worldpay ()->version (), true );
			}
		}
		if ($screen_id === 'shop_order') {
			wp_register_script ( 'worldpay-admin-order-metaboxes', worldpay ()->assets_url () . 'js/admin/meta-boxes-order.js', array( 
					'jquery' 
			), OWP_VERSION, true );
			wp_enqueue_script ( 'worldpay-admin-order-metaboxes' );
			wp_enqueue_style ( 'worldpay-admin-style' );
			
			wp_localize_script ( 'worldpay-admin-order-metaboxes', 'worldpay_admin_meta_boxes', array( 
					'_wpnonce' => wp_create_nonce ( 'wp_rest' ), 
					'capture_url' => wp_nonce_url ( get_rest_url ( null, 'worldpay/v1/admin/order/capture-charge' ), 'wp_rest', '_wpnonce' ), 
					'cancel_url' => wp_nonce_url ( get_rest_url ( null, 'worldpay/v1/admin/order/cancel' ), 'wp_rest', '_wpnonce' ) 
			) );
		}
	}

	public function footer_scripts() {
		$api_options = get_option ( 'woocommerce_online_worldpay_api_settings', array( 
				'support_enabled' => 'yes' 
		) );
		if (get_user_meta ( get_current_user_id (), 'wc_worldpay_support_id', true ) && $api_options[ 'support_enabled' ] === 'yes') {
			if (wp_script_is ( 'worldpay-help-widget', 'registered' )) {
				wp_enqueue_script ( 'worldpay-help-widget' );
			}
		}
	}
}
new WC_OWP_Admin_Assets ();