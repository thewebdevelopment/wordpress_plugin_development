<?php
class WC_Worldpay_Frontend_Scripts {

	private static $scripts = array();

	private static $styles = array();

	public static $prefix = 'worldpay-';

	private static $localized_scripts = array();

	public static function init() {
		add_action ( 'wp_enqueue_scripts', __CLASS__ . '::enqueue_scripts' );
		// add_action ( 'wp_print_scripts', __CLASS__ . '::localize_scripts' );
		add_action ( 'wp_print_footer_scripts', __CLASS__ . '::localize_scripts', 5 );
		
		define ( 'WORLDPAY_JS', 'https://cdn.worldpay.com/v1/worldpay.js' );
	}

	/**
	 * Register and enqueue scripts that are needed by the plugin.
	 */
	public static function enqueue_scripts() {
		self::register_scripts ();
		self::register_styles ();
		self::enqueue_media ();
	}

	private static function register_scripts() {
		$suffix = defined ( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG == true ? '' : '.min';
		$path = worldpay ()->assets_url ();
		self::register_script ( 'external-js', $path . 'js/frontend/worldpay' . $suffix . '.js' );
		
		do_action ( 'worldpay_register_frontend_scripts', __CLASS__, $path, self::$prefix, $suffix );
		
		self::register_script ( 'payment-methods', $path . 'js/frontend/payment-methods' . $suffix . '.js', array( 
				'jquery', 'select2' 
		) );
		self::register_script ( 'message-handler', $path . 'js/frontend/message-handler' . $suffix . '.js', array( 
				'jquery', 'select2' 
		) );
	}

	private static function register_styles() {
		$path = worldpay ()->assets_url () . 'css/';
		self::register_style ( 'worldpay', $path . 'worldpay.css' );
		do_action ( 'worldpay_register_frontend_styles', __CLASS__, $path, self::$prefix );
	}

	private static function enqueue_media() {
		do_action ( 'worldpay_enqueue_frontend_media', __CLASS__, self::$prefix );
		if (is_checkout () || is_account_page ()) {
			if (! is_add_payment_method_page ()) {
				wp_enqueue_script ( self::$prefix . 'payment-methods' );
			}
			wp_enqueue_script ( self::$prefix . 'message-handler' );
			
			// styles
			wp_enqueue_style ( self::$prefix . 'worldpay' );
		}
	}

	public static function register_script($handle, $src, $deps = array()) {
		$handle = self::$prefix . $handle;
		self::$scripts[] = $handle;
		wp_register_script ( $handle, $src, $deps, worldpay ()->version (), true );
	}

	public static function register_style($handle, $src, $deps = array()) {
		$handle = self::$prefix . $handle;
		self::$styles[] = $handle;
		wp_register_style ( $handle, $src, $deps, worldpay ()->version () );
	}

	public static function localize_scripts() {
		foreach ( self::$scripts as $handle ) {
			if (wp_script_is ( $handle ) && ! in_array ( $handle, self::$localized_scripts )) {
				self::$localized_scripts[] = $handle;
				$data = self::get_localized_script ( $handle );
				if ($data) {
					$object_name = str_replace ( '-', '_', $handle ) . '_params';
					wp_localize_script ( $handle, $object_name, apply_filters ( $handle, $data ) );
				}
			}
		}
	}

	public static function get_localized_script($handle) {
		$handle = str_replace ( self::$prefix, '', $handle );
		$data = apply_filters ( "worldpay_localize_{$handle}_script", array(), $handle );
		return ! empty ( $data ) ? $data : null;
	}
}
WC_Worldpay_Frontend_Scripts::init ();