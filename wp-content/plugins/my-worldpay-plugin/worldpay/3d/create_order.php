<?php
namespace Worldpay;
use Worldpay\WorldpayException;
require_once( __DIR__ . '../../vendor/autoload.php');

$worldpay = new Worldpay('T_S_173289a2-4d03-4160-b37f-c6d890379c36');

 
try {

    $billingAddress = array(
        "address1"=>'123 House Road',
        "address2"=> 'A village',
        "address3"=> 'A village',
        "postalCode"=> '3434343',
        "city"=> 'sadfsaf',
        "state"=> 'BA',
        "countryCode"=> 'PK',
    );


    $response = $worldpay->createOrder(array(
            'token' => $_POST['token'],
            'orderType' => 'your-orderType-option',
            'amount' => 500,
            'currencyCode' => 'GBP',
            'name' => 'John Smith',
            'billingAddress' => $billingAddress ,
                    
            'orderDescription' => 'Order description',
            'customerOrderCode' => 'Order code',
            'is3DSOrder' => true  // always make it true if its 3ds order
    ));


    $_SESSION['orderCode'] = $response['orderCode'];
    $oneTime3DsToken = $response['oneTime3DsToken'];
    $redirectURL = $response['redirectURL'];

    print "<pre>";
    print_r($response);
    echo "Order Successfully Created on worldpay!";


} catch (WorldpayException $e) {
    echo 'Error code: ' .$e->getCustomCode() .'  
    HTTP status code:' . $e->getHttpStatusCode() . '  
    Error description: ' . $e->getDescription()  . ' 
    Error message: ' . $e->getMessage();
}
