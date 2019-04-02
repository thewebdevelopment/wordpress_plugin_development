<?php
/**
 * @version 2.0.0
 */
?>
<form name="secure_form" method="POST"
	action="<?php echo $data['redirect_url']?>">
	<input type="hidden" name="PaReq"
		value="<?php echo $data['3ds_token']?>" /> <input type="hidden"
		name="TermUrl" value="<?php echo $data['return_url']?>" /> <input
		type="hidden" name="MD"
		value="<?php echo base64_encode(wp_json_encode(array('test', 'guy')))?>" />
</form>
<script>
window.onload = function(){
	document.secure_form.submit();
}
</script>