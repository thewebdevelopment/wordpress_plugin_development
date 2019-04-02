<?php
return array( 
		'wcs_settings' => array( 'type' => 'title', 
				'title' => __ ( 'Subscription Settings', 'worldpay' ) 
		), 
		'wcs_charge_type' => array( 
				'title' => __ ( 'Charge Type', 'worldpay' ), 
				'type' => 'select', 
				'options' => array( 
						'capture' => __ ( 'Capture', 'worldpay' ), 
						'authorize' => __ ( 'Authorize', 'worldpay' ) 
				), 'default' => 'capture', 
				'desc_tip' => true, 
				'description' => __ ( 'This setting determines if the order amount is captured immediately or is authorized and can be captured later.', 'worldpay' ) 
		), 
		'wcs_authorized_status' => array( 
				'type' => 'select', 
				'title' => __ ( 'Authorized Order Status', 'worldpay' ), 
				'default' => 'wc-on-hold', 
				'options' => wc_get_order_statuses (), 
				'custom_attributes' => array( 
						'data-show-if' => array( 
								'wcs_charge_type' => 'authorize' 
						) 
				), 'desc_tip' => false, 
				'description' => __ ( 'This is the order status that is assigned when payment for a renewal order is authorized and not captured immediately.', 'worldpay' ) 
		) 
);