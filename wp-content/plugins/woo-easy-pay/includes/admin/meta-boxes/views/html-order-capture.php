<?php
?>
<div id="woocommerce-worldpay-actions">
	<div class="wc-worldpay-buttons-container">
		<?php if($order->get_meta ( '_worldpay_payment_status' ) === 'AUTHORIZED'):?>
		<button type="button" class="button capture-charge"><?php esc_html_e('Capture Charge', 'worldpay')?></button>
		<button type="button" class="button cancel-order do-api-cancel"><?php esc_html_e('Cancel Order', 'worldpay')?></button>
		<?php endif;?>
	</div>
	<div class="wc-order-data-row wc-order-capture-charge"
		style="display: none;">
		<div class="wc-order-capture-charge-container">
			<table class="wc-order-capture-charge">
				<tr>
					<td class="label"><?php esc_html_e('Total available to capture', 'worldpay')?>:</td>
					<td class="total"><?php echo wc_price($order->get_total())?></td>
				</tr>
				<tr>
					<td class="label"><?php esc_html_e('Amount To Capture', 'worldpay')?>:</td>
					<td class="total"><input type="text" id="worldpay_capture_amount"
						name="capture_amount" class="wc_input_price" />
						<div class="clear"></div></td>
				</tr>
			</table>
		</div>
		<div class="clear"></div>
		<div class="capture-actions">
			<button type="button" class="button button-primary do-api-capture"><?php esc_html_e( 'Capture', 'worldpay' ); ?></button>
			<button type="button" class="button cancel-action"><?php esc_html_e( 'Cancel', 'worldpay' ); ?></button>
		</div>
		<div class="clear"></div>
	</div>
</div>
