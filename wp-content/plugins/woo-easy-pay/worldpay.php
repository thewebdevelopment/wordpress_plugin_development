<?php

/*
 * Plugin Name: Online Worldpay For WooCommerce
 * Plugin URI: https://wordpress.paymentplugins.com
 * Description: Accept credit card and PayPal payments your wordpress site using your Online Worldpay merchant account. This plugin is SAQ A compliant.
 * Version: 2.0.1
 * Author: Payment Plugins, support@paymentplugins.com
 * Author URI:
 * Tested up to: 5.1.1
 * WC requires at least: 3.0.0
 * WC tested up to: 3.5.6
 */
function wc_worldpay_invalid_version() {
	echo '<div class="notice notice-error"><p>' . sprintf ( __ ( 'Online Worldpay For WooCommerce requires at least PHP Version 5.3 but you are using version %s', 'worldpay' ), PHP_VERSION ) . '</p></div>';
}

if (version_compare ( PHP_VERSION, '5.3', '<' )) {
	add_action ( 'admin_notices', 'wc_worldpay_invalid_version' );
	return;
}

define ( 'OWP_BASE_PATH', plugin_dir_path ( __FILE__ ) );
define ( 'OWP_BASE_URL', plugin_dir_url ( __FILE__ ) );
define ( 'OWP_BASE_NAME', plugin_basename ( __FILE__ ) );
define ( 'OWP_VERSION', '2.0.1' );

// include_once ( ONLINEWORLDPAY_PLUGIN_PATH . 'class-loader.php' );
/**
 * Base Worldpay class that handles global functionality.
 *
 * @author User
 *        
 */
class Online_Worldpay_Manager {

	private static $instance = null;

	/**
	 *
	 * @var string
	 */
	private $url_path;

	/**
	 *
	 * @var string
	 */
	private $plugin_name;

	private $version;

	public static function instance() {
		if (! self::$instance) {
			self::$instance = new self ();
		}
		return self::$instance;
	}

	public function __construct() {
		$this->init_version ();
		$this->plugin_name = OWP_BASE_NAME;
		$this->admin_includes ();
		$this->includes ();
		add_action ( 'woocommerce_init', array( $this, 
				'wc_includes' 
		) );
		add_action ( 'admin_init', array( $this, 
				'check_woocommerce' 
		) );
	}

	private function init_version() {
		$data = get_file_data ( __FILE__, array( 
				'version' => 'Version' 
		) );
		if ($data && isset ( $data[ 'version' ] )) {
			$this->version = $data[ 'version' ];
		}
	}

	public function base_path() {
		return OWP_BASE_PATH;
	}

	public function plugin_url() {
		return OWP_BASE_URL;
	}

	public function assets_url() {
		return $this->plugin_url () . 'assets/';
	}

	/**
	 * Include all WC relevant classes and functions in this method
	 */
	public function wc_includes() {
		/**
		 * Functions
		 */
		include_once OWP_BASE_PATH . 'includes/wc-worldpay-functions.php';
		/**
		 * Gateways
		 */
		include_once OWP_BASE_PATH . 'includes/gateways/class-wc-worldpay-gateway.php';
		include_once OWP_BASE_PATH . 'includes/gateways/class-wc-worldpay-cc-gateway.php';
		include_once OWP_BASE_PATH . 'includes/gateways/class-wc-worldpay-paypal-gateway.php';
		
		/**
		 * Settings
		 */
		include_once OWP_BASE_PATH . 'includes/admin/settings/class-wc-worldpay-api-settings.php';
		include_once OWP_BASE_PATH . 'includes/admin/settings/class-wc-worldpay-webhook-settings.php';
		
		include_once OWP_BASE_PATH . 'includes/class-wc-payment-token-paypal.php';
		
		include_once OWP_BASE_PATH . 'includes/class-worldpay-frontend-scripts.php';
		include_once OWP_BASE_PATH . 'includes/shortcodes/class-wc-shortcode-worldpay.php';
	}

	public function admin_includes() {
		include_once OWP_BASE_PATH . 'includes/admin/class-admin-assets.php';
		include_once OWP_BASE_PATH . 'includes/admin/class-admin-update.php';
		include_once OWP_BASE_PATH . 'includes/admin/class-admin-settings.php';
		include_once OWP_BASE_PATH . 'includes/admin/meta-boxes/class-wc-admin-order-metaboxes.php';
		include_once OWP_BASE_PATH . 'includes/admin/api/class-wc-admin-order-controller.php';
	}

	public function includes() {
		include_once OWP_BASE_PATH . 'worldpay-lib-php/init.php';
		include_once OWP_BASE_PATH . 'includes/class-worldpay-install.php';
		include_once OWP_BASE_PATH . 'includes/class-wc-query.php';
		include_once OWP_BASE_PATH . 'includes/class-wc-worldpay-payments-conversion.php';
		include_once OWP_BASE_PATH . 'includes/api/class-wc-worldpay-webhook-controller.php';
	}

	public function check_woocommerce() {
		if (function_exists ( 'WC' )) {
			if (version_compare ( WC ()->version, '3.0.0', '<' )) {
				add_action ( 'admin_notices', function () {
					$message = __ ( 'The Online Worldpay plugin requires at least WooCommerce Version 3.0.0', 'worldpay' );
					echo '<div class="notice notice-error"><p>' . $message . '</p></div>';
				} );
			}
		}
	}

	/**
	 * The base path for the plugin
	 */
	public function template_path() {
		return OWP_BASE_PATH . 'templates/';
	}

	/**
	 * Returns the plugin name.
	 * Example woo-easy-pay/worldpay
	 */
	public function plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * Return the plugin version.
	 */
	public function version() {
		return $this->version;
	}
}

/**
 * Return an instance of the Online_Worldpay_Manager
 *
 * @return Online_Worldpay_Manager
 */
function worldpay() {
	return Online_Worldpay_Manager::instance ();
}

register_activation_hook ( __FILE__, array( 
		'WC_Worldpay_Install', 'install' 
) );

/**
 * Initial function call to setup all functionality.
 */
worldpay ();