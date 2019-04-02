<?php
class WC_Worldpay_Install {

	public static function init() {
		add_filter ( 'plugin_action_links_' . OWP_BASE_NAME, array( 
				__CLASS__, 'plugin_action_links' 
		) );
	}

	public static function plugin_action_links($links) {
		$action_links = array( 
				'settings' => sprintf ( '<a href="%1$s">%2$s</a>', admin_url ( 'admin.php?page=wc-settings&tab=checkout&section=online_worldpay_api' ), esc_html__ ( 'Settings', 'worldpay' ) ) 
		);
		return array_merge ( $action_links, $links );
	}

	/**
	 * Install plugin data
	 */
	public static function install() {
		/**
		 * Flush re-write rules since this plugin has custom urls.
		 */
		flush_rewrite_rules ();
		
		/**
		 * If the installation has already occurred, do not run again.
		 */
		if (get_option ( 'online_worldpay_version' )) {
			return;
		}
		$threeds_page_id = wp_insert_post ( array( 
				'post_content' => '[online_worldpay_3ds]', 
				'post_title' => __ ( 'Worldpay 3DS Shortcode Page', 'worldpay' ), 
				'post_type' => 'page', 
				'post_status' => 'publish' 
		), true );
		if (! is_wp_error ( $threeds_page_id )) {
			// update 3ds page option
			$settings = get_option ( 'woocommerce_online_worldpay_settings', array() );
			$settings[ '3dsecure_page' ] = $threeds_page_id;
			update_option ( 'woocommerce_online_worldpay_settings', $settings );
		}
		// WC_Worldpay_Admin_Update::update_plugin();
		delete_option ( 'onlineworldpay_for_woocommerce_version' );
		
		update_option ( 'online_worldpay_version', worldpay ()->version () );
	}
}
WC_Worldpay_Install::init ();