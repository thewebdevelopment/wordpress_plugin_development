<?php
namespace Worldpay;
use Worldpay\WorldpayException;
require_once( __DIR__ . '/vendor/autoload.php');	

ini_set("display_erros", "0");
ini_set("display_erros", "off");
error_reporting("0");

print("<pre>");
print_r($_POST);

/**
 * PHP library version: 2.1.0
 */
require_once('vendor/worldpay/worldpay-lib-php/init.php');

// Initialise Worldpay class with your SERVICE KEY
$worldpay = new Worldpay("T_S_173289a2-4d03-4160-b37f-c6d890379c36");

// Sometimes your SSL doesnt validate locally
// DONT USE IN PRODUCTION
$worldpay->disableSSLCheck(true);






// include('vendor/worldpay/worldpay-lib-php/examples/header.php');

// Try catch
try {
    // Customers billing address
    $billing_address = array(
        "address1"=> "1270 Rubaiyat Road",
        "postalCode"=> "49444",
        "city"=> "Muskegon Heights",
        "state"=> "Michigan",
        "countryCode"=> "DZ",
        "telephoneNumber"=> 2317372567
    );

    // Customers delivery address
    $delivery_address = array(
        "firstName" => "Hamza",
        "lastName" => "Khan",
        "address1"=> "18/35 A Block Samanabad Lahore",
        "postalCode"=> "54800",
        "city"=> "Lahore",
        "state"=> "Punjab",
        "countryCode"=> "AD",
        "telephoneNumber"=> "03054487456"
    );

    $obj = array(
        'orderDescription' => "test test test description order", // Order description of your choice
        'amount' => "123", // Amount in pence
        'currencyCode' => "AOA", // Currency code
        'settlementCurrency' => "AOA", // Settlement currency code
        'name' => "Jonathon", // Customer name
        'shopperEmailAddress' => "shoppingAddress@gmail.com", // Shopper email address
        'billingAddress' => $billing_address, // Billing address array
        'deliveryAddress' => $delivery_address, // Delivery address array
        'statementNarrative' => "statement_narrortor",
        'orderCodePrefix' => "ahp_",
        'orderCodeSuffix' => "_sadf",
        'customerOrderCode' => "sdaf3asd1f32a1sd23f", // Order code of your choice
        'successUrl' => "http://www.google.com/", //Success redirect url for APM
        'pendingUrl' => "http://www.hotmail.com/", //Pending redirect url for APM
        'failureUrl' => "http://www.yahoo.com/", //Failure redirect url for APM
        'cancelUrl' => "http://www.bing.com/", 
        'token' => $_POST['token'],
        'CVV' => "111",
    );

    
    $response = $worldpay->createApmOrder($obj);

    var_dump($response);
       

    if ($response['paymentStatus'] === 'PRE_AUTHORIZED')
    {
        // Redirect to URL
        $_SESSION['orderCode'] = $response['orderCode'];
        ?>
        <script>
            window.location.replace("<?php echo $response['redirectURL'] ?>");
        </script>
        <?php
    } else {
        // Something went wrong
        echo '<p id="payment-status">' . $response['paymentStatus'] . '</p>';
        throw new WorldpayException(print_r($response, true));
    }

    


} catch (WorldpayException $e) { // PHP 5.3+
    // Worldpay has thrown an exception
    echo 'Error code: ' . $e->getCustomCode() . '<br/>
    HTTP status code:' . $e->getHttpStatusCode() . '<br/>
    Error description: ' . $e->getDescription()  . ' <br/>
    Error message: ' . $e->getMessage();
}