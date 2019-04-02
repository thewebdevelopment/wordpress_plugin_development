<?php
if (! class_exists ( '\Worldpay\Connection' )) {
	require ( dirname ( __FILE__ ) . '/lib/Connection.php' );
}
if (! class_exists ( '\Worldpay\AbstractAddress' )) {
	require ( dirname ( __FILE__ ) . '/lib/AbstractAddress.php' );
}
if (! class_exists ( '\Worldpay\DeliveryAddress' )) {
	require ( dirname ( __FILE__ ) . '/lib/DeliveryAddress.php' );
}
if (! class_exists ( '\Worldpay\BillingAddress' )) {
	require ( dirname ( __FILE__ ) . '/lib/BillingAddress.php' );
}
if (! class_exists ( '\Worldpay\AbstractOrder' )) {
	require ( dirname ( __FILE__ ) . '/lib/AbstractOrder.php' );
}
if (! class_exists ( '\Worldpay\Order' )) {
	require ( dirname ( __FILE__ ) . '/lib/Order.php' );
}
if (! class_exists ( '\Worldpay\APMOrder' )) {
	require ( dirname ( __FILE__ ) . '/lib/APMOrder.php' );
}
if (! class_exists ( '\Worldpay\Error' )) {
	require ( dirname ( __FILE__ ) . '/lib/Error.php' );
}
if (! class_exists ( '\Worldpay\OrderService' )) {
	require ( dirname ( __FILE__ ) . '/lib/OrderService.php' );
}
if (! class_exists ( '\Worldpay\TokenService' )) {
	require ( dirname ( __FILE__ ) . '/lib/TokenService.php' );
}
if (! class_exists ( '\Worldpay\Utils' )) {
	require ( dirname ( __FILE__ ) . '/lib/Utils.php' );
}
if (! class_exists ( '\Worldpay\WorldpayException' )) {
	require ( dirname ( __FILE__ ) . '/lib/WorldpayException.php' );
}
if (! class_exists ( '\Worldpay\Worldpay' )) {
	require ( dirname ( __FILE__ ) . '/lib/Worldpay.php' );
}
