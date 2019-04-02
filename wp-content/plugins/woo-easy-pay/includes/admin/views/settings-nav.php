<?php global $current_section?>
<div class="worldpay-settings-nav">
	<a
		class="nav-tab <?php if($current_section === 'online_worldpay_api'){echo 'nav-tab-active';}?>"
		href="<?php echo admin_url( 'admin.php?page=wc-settings&tab=checkout&section=online_worldpay_api')?>"><?php _e('API Settings', 'worldpay')?></a>
	<a
		class="nav-tab <?php if($current_section === 'online_worldpay'){echo 'nav-tab-active';}?>"
		href="<?php echo admin_url( 'admin.php?page=wc-settings&tab=checkout&section=online_worldpay')?>"><?php _e('Credit Card Gateway', 'worldpay')?></a>
	<a
		class="nav-tab <?php if($current_section === 'online_worldpay_paypal'){echo 'nav-tab-active';}?>"
		href="<?php echo admin_url( 'admin.php?page=wc-settings&tab=checkout&section=online_worldpay_paypal')?>"><?php _e('PayPal Gateway', 'worldpay')?></a>
	<a
		class="nav-tab <?php if($current_section === 'online_worldpay_webhook'){echo 'nav-tab-active';}?>"
		href="<?php echo admin_url( 'admin.php?page=wc-settings&tab=checkout&section=online_worldpay_webhook')?>"><?php _e('Webhook Settings', 'worldpay')?></a>
		<a target="_blank"
		class="nav-tab"
		href="http://eepurl.com/ghxdM9"><?php _e('Join Mailing List', 'worldpay')?></a>
</div>
<div class="clear"></div>