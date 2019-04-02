<?php
/**
 *@version 2.0.0 
 */
?>
<div class="worldpay-card-icons">
	<?php foreach($icons as $icon):?>
	<img class="worldpay-card-icon" src="<?php echo worldpay()->assets_url() . 'img/cards/' . $icon . '.svg'?>"/>
	<?php endforeach;?>
</div>