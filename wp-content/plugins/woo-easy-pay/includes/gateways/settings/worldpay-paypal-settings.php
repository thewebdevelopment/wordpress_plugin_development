<?php
return array( 
		'enabled' => array( 
				'title' => __ ( 'Enable/Disable', 'woocommerce' ), 
				'type' => 'checkbox', 
				'label' => __ ( 'Enable PayPal', 'worldpay' ), 
				'value' => 'yes', 'default' => 'no' 
		), 
		'general_settings' => array( 'type' => 'title', 
				'title' => __ ( 'General Settings', 'worldpay' ) 
		), 
		'title' => array( 
				'title' => __ ( 'Title', 'woocommerce' ), 
				'type' => 'text', 
				'description' => __ ( 'This controls the title which the user sees during checkout.', 'worldpay' ), 
				'default' => __ ( 'PayPal', 'woocommerce' ), 
				'desc_tip' => true 
		), 
		'description' => array( 
				'title' => __ ( 'Description', 'worldpay' ), 
				'type' => 'text', 'desc_tip' => true, 
				'default' => '', 
				'description' => __ ( 'You can provide a description of the gateway that will appear on the frontend.', 'worldpay' ) 
		), 
		'charge_type' => array( 
				'title' => __ ( 'Capture Type', 'worldpay' ), 
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
		'pending_status' => array( 'type' => 'select', 
				'title' => __ ( 'Pending Status', 'worldpay' ), 
				'default' => 'wc-on-hold', 
				'options' => wc_get_order_statuses (), 
				'desc_tip' => true, 
				'description' => __ ( 'This is the status assigned to the order when a customer has made a PayPal payment but the payment is pending.', 'worldpay' ) 
		) 
);