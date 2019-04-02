<?php
return array( 
		'webhook_url' => array( 'title' => 'Webhook Url', 
				'type' => 'paragraph', 
				'class' => 'worldpay-webhook-url', 
				'text' => get_rest_url ( null, 'worldpay/v1/webhook/order' ), 
				'default' => get_rest_url ( null, 'worldpay/v1/webhook/order' ), 
				'description' => __ ( 'This is the url you use in your Worldpay Settings if you accept webhook notifications.
						<p>To enter the webhook url.</p><ol><li>Log in to <a target="_blank" href="https://online.worldpay.com/">Online Worldpay</a>.</li>
						<li>Click <strong>Settings</strong> > <strong>Webhooks</strong></li>
						<li>Click <strong>Add Webhook</strong></li>
						<li>Paste the Url into the text field and click <strong>Save</strong></li>
						<li>Click <strong>Test</strong> and verify that the response code is <strong>200</strong></li>
						<li>Navigate to the <a target="_blank" href="' . admin_url ( 'admin.php?page=wc-status&tab=logs' ) . '">Worldpay Logs</a> and there should be an entry for the webhook test indicating it was received. Debugging must be enabled for the log entry to exist.</li></ol>', 'worldpay' ) 
		) 
);