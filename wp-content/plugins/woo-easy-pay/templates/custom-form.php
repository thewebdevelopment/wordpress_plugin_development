<?php
/**
 * @version 2.0.0
 */
?>
<input type="hidden" id="worldpay_cardholder" data-worldpay="name"/>
<?php worldpay_get_template('custom-forms/' . $gateway->get_option("custom_form_design"), array('gateway' => $gateway));
?>
