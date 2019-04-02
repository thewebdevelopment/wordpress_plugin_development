<?php
/**
 * @version 2.0.0
 */
?>
<div class="worldpay-basic-form-container">
	<div class="row">
		<div class="col">
			<div class="card-number-container">
				<label><?php _e('Card Number', 'worldpay')?></label>
				<div class="card-number">
					<input type="text" id="worldpay_cc_number" data-worldpay="number" placeholder="<?php _e('1234 1234 1234 1234', 'worldpay')?>"/>
					<img class="worldpay-card-type" src="<?php echo worldpay ()->assets_url () . 'img/cards/cc_format.svg'?>"/>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-6 exp-container">
			<label><?php _e('Exp Date', 'worldpay')?></label>
			<select id="worldpay_exp_month" data-worldpay="exp-month" class="exp-month">
				<optgroup label="<?php _e('Month', 'worldpay')?>">
					<?php foreach(worldpay_get_month_options() as $option):?>
						<?php echo $option;?>
					<?php endforeach;?>
				</optgroup>
			</select>
			<select id="worldpay_exp_year" data-worldpay="exp-year" class="exp-year">
				<optgroup label="<?php _e('Year', 'worldpay')?>">
					<?php foreach(worldpay_get_year_options() as $option):?>
						<?php echo $option?>
					<?php endforeach;?>
				</optgroup>
			</select>
		</div>
		<?php if($gateway->is_active('cvc_enabled')):?>
		<div class="col-6 cvv-container">
			<div class="cvv-group">
				<label><?php _e('CVC', 'worldpay')?></label>
				<input id="worldpay_cvc" type="text" data-worldpay="cvc" class="cvc" placeholder="<?php _e('123', 'worldpay')?>"/>
			</div>
		</div>
		<?php endif;?>
	</div>
</div>