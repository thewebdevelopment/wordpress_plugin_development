<?php
/**
 * @version 2.0.0
*/
?>
<div class="worldpay-simple-form-container">
	<label><?php _e('Card Number', 'worldpay')?></label>
	<div class="simple-row card-number">
		<input type="text" id="worldpay_cc_number" data-worldpay="number" placeholder="<?php _e('1234 1234 1234 1234', 'worldpay')?>"/>
		<img class="worldpay-card-type" src="<?php echo worldpay ()->assets_url () . 'img/cards/cc_format.svg'?>"/>
	</div>
	<label><?php _e('Exp Date', 'worldpay')?></label>
	<div class="simple-row exp-date">
		<input type="text" id="worldpay_exp_date" data-worldpay="exp-monthyear" placeholder="<?php _e('MM/YYYY')?>"/>
	</div>
	<?php if($gateway->is_active('cvc_enabled')):?>
	<label><?php _e('CVC', 'worldpay')?></label>
	<div class="simple-row cvc">
		<input id="worldpay_cvc" type="text" data-worldpay="cvc" class="cvc" placeholder="<?php _e('123', 'worldpay')?>"/>
	</div>
	<?php endif;?>
</div>