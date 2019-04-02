<?php
/**
 * @version 2.0.0
 */
?>
<form method="post" name="form" action="<?php echo wc_get_checkout_url()?>">
	<input type="hidden" name="worldpay_checkout_error" value="true"/>
</form>
<script>
window.onload = function(){
	document.form.submit();
}
</script>