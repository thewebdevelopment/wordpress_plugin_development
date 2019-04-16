<?php
namespace Worldpay;
use Worldpay\WorldpayException;
require_once( __DIR__ . '/vendor/autoload.php');


var_dump($_POST);

$worldpay = new Worldpay('T_S_173289a2-4d03-4160-b37f-c6d890379c36');
 
$response = $worldpay->createOrder(array(
    'token' => $_POST['token'],
    'amount-in-cents' => 500,
    'amount' => 500,
    'currencyCode' => 'USD',
    'name' => 'test name',
    'billingAddress' => array(
        'address1' => 'address1',
        'postalCode' => 'postCode',
        'city' => 'city',
        'countryCode' => 'GB',
    ),
    'orderDescription' => 'Order description',
    'customerOrderCode' => 'Order code',
    'settlementCurrency'=> 'GBP'        
));
?>