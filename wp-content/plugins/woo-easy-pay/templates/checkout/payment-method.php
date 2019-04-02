<?php
/**
 * @version 2.0.0
 */
?>
<div class="<?php echo $gateway->id?>_gateway_container worldpay-gateway-container">
<?php
worldpay_payment_nonce_field ( $gateway );
worldpay_payment_token_type_field ( $gateway );
?>
	<?php if($has_methods):?>
	<input type="radio" class="wc-worldpay-payment-type" id="<?php echo $gateway->id?>_use_nonce" name="<?php echo $gateway->payment_type_key?>" value="nonce"/>
	<label class="wc-worldpay-label-payment-type"  for="<?php echo $gateway->id?>_use_nonce"><?php _e('New Card', 'worldpay')?></label>
	<?php endif;?>
	<div class="<?php echo $gateway->id?>_new_card worldpay-new-method-container" <?php if($has_methods):?>style="display: none;"<?php endif;?>>
			<?php worldpay_get_template($card_template, array('gateway' => $gateway));?>
	</div>
	<?php if($has_methods):?>
	<input type="radio" class="wc-worldpay-payment-type" checked="checked" id="<?php echo $gateway->id?>_use_token" name="<?php echo $gateway->payment_type_key?>" value="token"/>
	<label for="<?php echo $gateway->id?>_use_token" class="wc-worldpay-label-payment-type"><?php _e('Saved Cards', 'worldpay')?></label>
	<div class="<?php echo $gateway->id?>_saved_cards worldpay-saved-methods-container">
	<?php worldpay_payment_token_field ( $gateway );?>
		<select id="<?php echo $gateway->id?>_token" class="worldpay-saved-cards" data-worldpay="token">
			<?php foreach($methods as $method):?>
				<option data-card-type="<?php echo $method->get_card_type()?>" value="<?php echo $method->get_token()?>" <?php if($method->is_default()):?>selected<?php endif;?>><?php echo worldpay_get_card_display_name($method)?></option>
			<?php endforeach;?>
		</select>
		<?php if($gateway->is_active('cvc_saved_card')):?>
		<?php worldpay_get_template($cvc_template, array('gateway' => $gateway))?>
		<?php endif;?>
	</div>
	<?php endif;?>
</div>