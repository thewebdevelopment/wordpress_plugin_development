<?php
if (! class_exists ( 'WC_Settings_API' )) {
	return;
}
class WC_Online_Worldpay_Webhook_Settings extends WC_Settings_API {

	public static function init() {
		add_action ( 'woocommerce_payment_gateways', function ($gateways) {
			new WC_Online_Worldpay_Webhook_Settings ();
			return $gateways;
		} );
	}

	public function __construct() {
		$this->id = 'online_worldpay_webhook';
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
		$this->form_fields = apply_filters ( 'worldpay_webhook_gateway_settings', include 'worldpay-webhook-settings.php' );
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

	public function generate_paragraph_html($key, $data) {
		$field_key = $this->get_field_key ( $key );
		$defaults = array( 'title' => '', 'label' => '', 
				'class' => '', 'css' => '', 'type' => 'text', 
				'desc_tip' => false, 'description' => '', 
				'custom_attributes' => array() 
		);
		$data = wp_parse_args ( $data, $defaults );
		if (! $data[ 'label' ]) {
			$data[ 'label' ] = $data[ 'title' ];
		}
		ob_start ();
		include worldpay ()->base_path () . 'includes/admin/views/html-paragraph.php';
		return ob_get_clean ();
	}
}
WC_Online_Worldpay_Webhook_Settings::init ();