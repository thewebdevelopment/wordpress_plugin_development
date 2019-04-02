<?php
return array( 
		'environment_settings' => array( 
				'title' => __ ( 'Environment Settings', 'worldpay' ), 
				'type' => 'title', 
				'description' => __ ( 'When set to <b>Test</b> mode you can simulate payments on your site. When set to <b>Live</b> you can accept real payments on your site.', 'worldpay' ) 
		), 
		'environment' => array( 
				'title' => __ ( 'Environment', 'worldpay' ), 
				'type' => 'select', 
				'class' => 'wc-enhanced-select', 
				'default' => 'test', 
				'options' => array( 
						'live' => __ ( 'Live', 'worldpay' ), 
						'test' => __ ( 'Test', 'worldpay' ) 
				), 'desc_tip' => true, 
				'description' => __ ( 'When set to live, you are ready to accept real payments. Test is for testing your integration.', 'worldpay' ) 
		), 
		'api_settings' => array( 
				'title' => __ ( 'API Settings', 'worldpay' ), 
				'type' => 'title', 'description' => '' 
		), 
		'test_merchant_id' => array( 
				'title' => __ ( 'Test Merchant ID', 'worldpay' ), 
				'type' => 'text', 'default' => '', 
				'desc_tip' => true, 
				'description' => __ ( 'Identifier for your gateway.', 'worldpay' ), 
				'custom_attributes' => array( 
						'data-show-if' => array( 
								'environment' => 'test' 
						) 
				) 
		), 
		'test_service_key' => array( 
				'title' => __ ( 'Test Service Key', 'worldpay' ), 
				'type' => 'text', 'default' => '', 
				'desc_tip' => true, 
				'description' => __ ( 'Key used for authenticating your server when it connects to Worldpay.', 'worldpay' ), 
				'custom_attributes' => array( 
						'data-show-if' => array( 
								'environment' => 'test' 
						) 
				) 
		), 
		'test_client_key' => array( 
				'title' => __ ( 'Test Client Key', 'worldpay' ), 
				'type' => 'text', 'default' => '', 
				'desc_tip' => true, 
				'description' => __ ( 'Key used for authenticating your browser when it requests information from Worldpay.', 'worldpay' ), 
				'custom_attributes' => array( 
						'data-show-if' => array( 
								'environment' => 'test' 
						) 
				) 
		), 
		'live_merchant_id' => array( 
				'title' => __ ( 'Live Merchant ID', 'worldpay' ), 
				'type' => 'text', 'default' => '', 
				'desc_tip' => true, 
				'description' => __ ( 'Identifier for your gateway.', 'worldpay' ), 
				'custom_attributes' => array( 
						'data-show-if' => array( 
								'environment' => 'live' 
						) 
				) 
		), 
		'live_service_key' => array( 
				'title' => __ ( 'Live Service Key', 'worldpay' ), 
				'type' => 'text', 'default' => '', 
				'desc_tip' => true, 
				'description' => __ ( 'Key used for authenticating your server when it connects to Worldpay.', 'worldpay' ), 
				'custom_attributes' => array( 
						'data-show-if' => array( 
								'environment' => 'live' 
						) 
				) 
		), 
		'live_client_key' => array( 
				'title' => __ ( 'Live Client Key', 'worldpay' ), 
				'type' => 'text', 'default' => '', 
				'desc_tip' => true, 
				'description' => __ ( 'Key used for authenticating your browser when it requests information from Worldpay.', 'worldpay' ), 
				'custom_attributes' => array( 
						'data-show-if' => array( 
								'environment' => 'live' 
						) 
				) 
		), 
		'support_title' => array( 'type' => 'title', 
				'title' => __ ( 'Support Options', 'worldpay' ) 
		), 
		'support_enabled' => array( 'type' => 'checkbox', 
				'title' => __ ( 'Enabled', 'worldpay' ), 
				'default' => 'yes', 
				'description' => __ ( 'Allow us to collect your merchant ID and email address so we can create an account for you in our support system. This will make helping you troubleshoot issues easier and quicker.', 'worldpay' ) 
		), 
		'support_email' => array( 'type' => 'text', 
				'title' => __ ( 'Email', 'worldpay' ), 
				'default' => get_option ( 'admin_email' ), 
				'description' => __ ( 'This is the email associated with your account. It will be used to create your support account.', 'worldpay' ) 
		), 
		'debug_title' => array( 
				'title' => __ ( 'Debug', 'worldpay' ), 
				'type' => 'title' 
		), 
		'debug_enabled' => array( 'type' => 'checkbox', 
				'title' => __ ( 'Enable Debug', 'worldpay' ), 
				'default' => 'yes', 'values' => 'yes', 
				'description' => __ ( 'If enabled, the plugin will capture debug information related to payments. This is useful for troubleshooting errors.<a href="' . admin_url ( 'admin.php?page=wc-status&tab=logs' ) . '" target="_blank"><p>Debug Log</p></a>', 'worldpay' ) 
		) 
);