<?php
if (! class_exists ( 'WC_Settings_API' )) {
	return;
}
class WC_Online_Worldpay_API_Settings extends WC_Settings_API {

	public static function init() {
		add_action ( 'woocommerce_payment_gateways', function ($gateways) {
			new WC_Online_Worldpay_API_Settings ();
			return $gateways;
		} );
	}

	public function __construct() {
		$this->id = 'online_worldpay_api';
		$this->init_form_fields ();
		$this->init_settings ();
		add_action ( 'woocommerce_update_options_checkout_' . $this->id, array( 
				$this, 'process_admin_options' 
		) );
		add_filter ( 'woocommerce_get_settings_' . $this->id, array( 
				$this, 'get_admin_settings' 
		), 10, 2 );
		add_action ( 'woocommerce_settings_checkout', array( 
				$this, 'output' 
		) );
	}

	public function init_form_fields() {
		$this->form_fields = apply_filters ( 'worldpay_api_gateway_settings', include 'worldpay-api-settings.php' );
	}

	public function get_admin_settings($settings, $current_section) {
		$settings = array_merge ( $settings, $this->settings );
		return $settings;
	}

	public function output() {
		global $current_section;
		if ($current_section === $this->id) {
			$this->admin_options ();
		}
	}

	public function process_admin_options() {
		$old_email = $this->get_option ( 'support_email', '' );
		$old_merchant_id = $this->get_option ( 'live_merchant_id' );
		$user = wp_get_current_user ();
		parent::process_admin_options ();
		$merchant_id = $this->get_option ( 'live_merchant_id' );
		$changes = array( 
				$old_email !== $this->get_option ( 'support_email' ), 
				$old_merchant_id !== $this->get_option ( 'live_merchant_id' ) 
		);
		if (! empty ( $merchant_id ) && $this->get_option ( 'support_enabled' ) === 'yes' && ( ! get_user_meta ( $user->ID, 'wc_worldpay_support_id', true ) || in_array ( true, $changes ) )) {
			$response = wp_remote_post ( 'https://wordpress.paymentplugins.com/wp-json/zendesk/v1/user', array( 
					'body' => array( 'plugin' => 'worldpay', 
							'id' => get_user_meta ( $user->ID, 'wc_worldpay_support_id', true ), 
							'name' => $user->first_name . ' ' . $user->last_name, 
							'email' => $this->get_option ( 'support_email' ), 
							'worldpay_merchant_id' => $this->get_option ( 'live_merchant_id' ) 
					) 
			) );
			if (is_wp_error ( $response )) {
				$this->add_error ( __ ( 'There was an error registering you for support. Please contact support@paymentplugins.com', 'worldpay' ) );
			} else {
				$result = json_decode ( $response[ 'body' ], true );
				if ($result[ 'result' ] !== 'success') {
					WC_Admin_Settings::add_error ( sprintf ( __ ( 'There was an error registering you for support. Reason: %1$s Please contact support@paymentplugins.com', 'worldpay' ), $result[ 'message' ] ) );
				} else {
					WC_Admin_Settings::add_message ( __ ( 'You have been registered/updated in our Support system!', 'worldpay' ) );
					update_user_meta ( $user->ID, 'wc_worldpay_support_id', $result[ 'user' ][ 'id' ] );
				}
			}
		}
	}

	public function admin_options() {
		echo '<div class="worldpay-settings-cotnainer">';
		include worldpay ()->base_path () . 'includes/admin/views/settings-nav.php';
		printf ( '<input type="hidden" id="worldpay_settings_prefix" value="%1$s"/>', $this->plugin_id . $this->id . '_' );
		parent::admin_options ();
		echo '</div>';
	}

	public function get_custom_attribute_html($attribs) {
		if (! empty ( $attribs[ 'custom_attributes' ] ) && is_array ( $attribs[ 'custom_attributes' ] )) {
			foreach ( $attribs[ 'custom_attributes' ] as $k => $v ) {
				if (is_array ( $v )) {
					$attribs[ 'custom_attributes' ][ $k ] = htmlspecialchars ( wp_json_encode ( $v ) );
				}
			}
		}
		return parent::get_custom_attribute_html ( $attribs );
	}

	public function validate_support_enabled_field($key, $value) {
		$merchant_id = $this->get_option ( 'live_merchant_id' );
		if (empty ( $merchant_id ) && $value === '1' && ! get_user_meta ( get_current_user_id (), 'wc_worldpay_support_id', true )) {
			WC_Admin_Settings::add_error ( __ ( 'In order to register for support you must enter your Worldpay Live Merchant ID', 'worldpay' ) );
		}
		return parent::validate_checkbox_field ( $key, $value );
	}
}
WC_Online_Worldpay_API_Settings::init ();