<?php
return array( 
		'enabled' => array( 
				'title' => __ ( 'Enable/Disable', 'woocommerce' ), 
				'type' => 'checkbox', 
				'label' => __ ( 'Enable Credit Card Gateway', 'worldpay' ), 
				'value' => 'yes', 'default' => 'yes' 
		), 
		'test_cards' => array( 'type' => 'title', 
				'description' => __ ( '<p>If you are using the test environment, you can use the following <a target="blank" href="https://developer.worldpay.com/jsonapi/docs/testing">Test Cards</a></p>', 'worldpay' ) 
		), 
		'form_settings' => array( 'type' => 'title', 
				'title' => __ ( 'Form Settings', 'type' ), 
				'description' => __ ( 'This plugin offers two form options. For SAQ A compliance you can use the template form which is a hosted iFrame from Worldpay. The custom forms are SAQ A-EP compliant and provide better UX and are mobile responsive.', 'worldpay' ) 
		), 
		'form_type' => array( 'type' => 'select', 
				'title' => __ ( 'Form Type', 'worldypay' ), 
				'options' => array( 
						'template_form' => __ ( 'iFrame Form', 'worldpay' ), 
						'custom_form' => __ ( 'Custom Form', 'worldpay' ) 
				), 'default' => 'template_form', 
				'desc_tip' => true, 
				'description' => __ ( 'The iFrame form is SAQ A compliant. The custom forms are SAQ A-EP complaint.', 'worldpay' ) 
		), 
		'test_card_template' => array( 'type' => 'text', 
				'title' => __ ( 'Test Card Template', 'worldpay' ), 
				'default' => '', 'desc_tip' => true, 
				'description' => __ ( 'If you are using the SAQ A iFrame solution, you can enter your card template here. If left blank, the default Worldpay form will be used.', 'worldpay' ), 
				'custom_attributes' => array( 
						'data-show-if' => array( 
								'environment' => 'test', 
								'form_type' => 'template_form' 
						) 
				) 
		), 
		'test_cvc_template' => array( 'type' => 'text', 
				'title' => __ ( 'Test CVC Template', 'worldpay' ), 
				'default' => '', 'desc_tip' => true, 
				'description' => __ ( 'The CVC template is for rendering a CVC field when using saved payment methods. IF left blank, the default CVC field will be used.', 'worldpay' ), 
				'custom_attributes' => array( 
						'data-show-if' => array( 
								'environment' => 'test', 
								'form_type' => 'template_form' 
						) 
				) 
		), 
		'live_card_template' => array( 'type' => 'text', 
				'title' => __ ( 'Live Card Template', 'worldpay' ), 
				'default' => '', 'desc_tip' => true, 
				'description' => __ ( 'If you are using the SAQ A iFrame solution, you can enter your card template here. If left blank, the default Worldpay form will be used.', 'worldpay' ), 
				'custom_attributes' => array( 
						'data-show-if' => array( 
								'environment' => 'live', 
								'form_type' => 'template_form' 
						) 
				) 
		), 
		'live_cvc_template' => array( 'type' => 'text', 
				'title' => __ ( 'Live CVC Template', 'worldpay' ), 
				'default' => '', 'desc_tip' => true, 
				'description' => __ ( 'The CVC template is for rendering a CVC field when using saved payment methods. IF left blank, the default CVC field will be used.', 'worldpay' ), 
				'custom_attributes' => array( 
						'data-show-if' => array( 
								'environment' => 'live', 
								'form_type' => 'template_form' 
						) 
				) 
		), 
		'custom_form_design' => array( 'type' => 'select', 
				'title' => __ ( 'Custom Form' ), 
				'default' => 'basic-form.php', 
				'options' => worldpay_custom_forms (), 
				'custom_attributes' => array( 
						'data-show-if' => array( 
								'form_type' => 'custom_form' 
						) 
				) 
		), 
		'cvc_enabled' => array( 'type' => 'checkbox', 
				'title' => __ ( 'CVC Credit Card', 'worldpay' ), 
				'default' => 'yes', 'value' => 'no', 
				'custom_attributes' => array( 
						'data-show-if' => array( 
								'form_type' => 'custom_form' 
						) 
				), 
				'description' => __ ( 'If enabled, a CVC entry will be required when a credit card is used for payment. You can require CVC in your Worldpay Risk Settings which
						will reject any order where a credit card is used and the CVC is not provided or is invalid.', 'worldpay' ) 
		), 
		'general_settings' => array( 
				'title' => __ ( 'General Settings', 'worldpay' ), 
				'type' => 'title' 
		), 
		'title' => array( 
				'title' => __ ( 'Title', 'woocommerce' ), 
				'type' => 'text', 
				'description' => __ ( 'This controls the title which the user sees during checkout.', 'worldpay' ), 
				'default' => __ ( 'Worldpay', 'woocommerce' ), 
				'desc_tip' => true 
		), 
		'cvc_saved_card' => array( 'type' => 'checkbox', 
				'title' => __ ( 'CVC for Saved Card', 'worldpay' ), 
				'default' => 'yes', 'value' => 'no', 
				'description' => __ ( 'If enabled, a CVC entry will be required when a saved payment method is used. You can require CVC in your Worldpay Risk Settings which
						will reject any order where a saved card is used and the CVC is not provided or is invalid.', 'worldpay' ) 
		), 
		'description' => array( 
				'title' => __ ( 'Description', 'worldpay' ), 
				'type' => 'text', 'desc_tip' => true, 
				'default' => '', 
				'description' => __ ( 'You can provide a description of the gateway that will appear on the frontend.', 'worldpay' ) 
		), 
		'accepted_cards' => array( 
				'title' => __ ( 'Accepted Cards', 'worldpay' ), 
				'type' => 'multiselect', 
				'class' => 'wc-enhanced-select worldpay-accepted-cards', 
				'default' => array( 'visa', 'amex', 
						'master_card', 'discover' 
				), 
				'options' => array( 
						'visa' => __ ( 'Visa', 'worldpay' ), 
						'amex' => __ ( 'Amex', 'worldpay' ), 
						'master_card' => __ ( 'Master Card', 'worldpay' ), 
						'discover' => __ ( 'Discover', 'worldpay' ) 
				), 'desc_tip' => true, 
				'description' => __ ( 'When selected, an icon of the payment method will appear next to the gateway.', 'worldpay' ) 
		), 
		'charge_type' => array( 
				'title' => __ ( 'Charge Type', 'worldpay' ), 
				'type' => 'select', 
				'options' => array( 
						'capture' => __ ( 'Capture', 'worldpay' ), 
						'authorize' => __ ( 'Authorize', 'worldpay' ) 
				), 'default' => 'capture', 
				'desc_tip' => true, 
				'description' => __ ( 'This setting determines if the order amount is captured immediately or is authorized and can be captured later.', 'worldpay' ) 
		), 
		'authorize_status' => array( 'type' => 'select', 
				'title' => __ ( 'Authorized Order Status', 'worldpay' ), 
				'default' => 'wc-on-hold', 
				'options' => wc_get_order_statuses (), 
				'description' => __ ( 'This is the order status assigend when payment is authorized and not captured immediately.', 'worldpay' ), 
				'custom_attributes' => array( 
						'data-show-if' => array( 
								'charge_type' => 'authorize' 
						) 
				) 
		), 
		'settlement_currency' => array( 'type' => 'select', 
				'title' => __ ( 'Settlement Currency', 'worldpay' ), 
				'default' => get_woocommerce_currency (), 
				'options' => worldpay_get_settlement_currencies (), 
				'desc_tip' => false, 
				'description' => __ ( 'The settlement currency is the currency that you are paid in via Worldpay. For more information, you can read about settlement currency on <a target="_blank"
						href="https://online.worldpay.com/support/articles/how-do-i-add-settlement-currencies-to-my-account">Online Worldpay</a>.', 'worldpay' ) 
		), 
		'order_prefix' => array( 
				'title' => __ ( 'Order Prefix', 'worldpay' ), 
				'type' => 'text', 'default' => '', 
				'desc_tip' => true, 
				'description' => __ ( 'You can add a prefix to the Worldpay order code and it will appear in the Worldpay gateway.', 'worldpay' ) 
		), 
		'order_suffix' => array( 
				'title' => __ ( 'Order Suffix', 'worldpay' ), 
				'type' => 'text', 'default' => '', 
				'desc_tip' => true, 
				'description' => __ ( 'You can add a suffix to the Worldpay order code and it will appear in the Worldpay gateway.', 'worldpay' ) 
		), 
		'3d_secure_title' => array( 'type' => 'title', 
				'title' => __ ( '3D Secure', 'worldpay' ), 
				'description' => __ ( 'If enabled, 3DS will be presented to your customers when purchasing products.', 'worldpay' ) 
		), 
		'3ds_enabled' => array( 
				'title' => __ ( '3D Secure Enabled', 'worldpay' ), 
				'type' => 'checkbox', 'default' => 'no', 
				'value' => 'yes', 'default' => 'no' 
		), 
		'3dsecure_page' => array( 'type' => 'select', 
				'title' => '3DS Page', 'value' => '', 
				'default' => worldpay_get_3ds_shortcode_page (), 
				'options' => worldpay_get_blog_pages_as_options (), 
				'description' => __ ( 'The page where the <b>[online_worldpay_3ds]</b> shortcode has been set.', 'worldpay' ) 
		) 
);



