<?php
return array( 
		'wcs_paypal_enabled' => array( 'type' => 'checkbox', 
				'title' => __ ( 'Allow Subscriptions', 'worldpay' ), 
				'default' => 'yes', 'value' => 'yes', 
				'description' => __ ( 'If enabled, PayPal will be available for subscriptions. You must enable reference transactions by contacting Worldpay.
						<a href="https://developer.worldpay.com/jsonapi/docs/make-paypal-payment" target="_blank">Read about it here</a>
						<ol><li>Email RSC_Admin.Wpdev@worldpay.com and request reference transactions</li><li>Enable reference transactions in your PayPal account.</li></ol>', 'worldpay' ) 
		) 
);