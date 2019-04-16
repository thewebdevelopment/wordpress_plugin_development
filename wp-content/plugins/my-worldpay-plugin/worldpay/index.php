<?php
namespace Worldpay;
use Worldpay\WorldpayException;
require_once( __DIR__ . '/vendor/autoload.php');

if ( !isset($_POST['test'])) {
	


ini_set("display_erros", "0");
ini_set("display_erros", "off");
error_reporting("0");

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

$directOrder = isset($_POST['direct-order']) ? $_POST['direct-order'] : false;
$token = (isset($_POST['token'])) ? $_POST['token'] : null;
$name = $_POST['name'];
$shopperEmailAddress = $_POST['shopper-email'];

$amount = 0;
if (isset($_POST['amount']) && !empty($_POST['amount'])) {
    $amount = is_numeric($_POST['amount']) ? $_POST['amount']*100 : -1;
}

$orderType = $_POST['order-type'];

$_3ds = (isset($_POST['3ds'])) ? $_POST['3ds'] : false;
$authorizeOnly = (isset($_POST['authorizeOnly'])) ? $_POST['authorizeOnly'] : false;
$customerIdentifiers = (!empty($_POST['customer-identifiers'])) ? json_decode($_POST['customer-identifiers']) : array();

// include('vendor/worldpay/worldpay-lib-php/examples/header.php');

// Try catch
try {
    // Customers billing address
    $billing_address = array(
        "address1"=> $_POST['address1'],
        "address2"=> $_POST['address2'],
        "address3"=> $_POST['address3'],
        "postalCode"=> $_POST['postcode'],
        "city"=> $_POST['city'],
        "state"=> $_POST['state'],
        "countryCode"=> $_POST['countryCode'],
        "telephoneNumber"=> $_POST['telephoneNumber']
    );

    // Customers delivery address
    $delivery_address = array(
        "firstName" => $_POST['delivery-firstName'],
        "lastName" => $_POST['delivery-lastName'],
        "address1"=> $_POST['delivery-address1'],
        "address2"=> $_POST['delivery-address2'],
        "address3"=> $_POST['delivery-address3'],
        "postalCode"=> $_POST['delivery-postcode'],
        "city"=> $_POST['delivery-city'],
        "state"=> $_POST['delivery-state'],
        "countryCode"=> $_POST['delivery-countryCode'],
        "telephoneNumber"=> $_POST['delivery-telephoneNumber']
    );

    if ($orderType == 'APM') {

        $obj = array(
            'orderDescription' => $_POST['description'], // Order description of your choice
            'amount' => $amount, // Amount in pence
            'currencyCode' => $_POST['currency'], // Currency code
            'settlementCurrency' => $_POST['settlement-currency'], // Settlement currency code
            'name' => $name, // Customer name
            'shopperEmailAddress' => $shopperEmailAddress, // Shopper email address
            'billingAddress' => $billing_address, // Billing address array
            'deliveryAddress' => $delivery_address, // Delivery address array
            'customerIdentifiers' => (!is_null($customerIdentifiers)) ? $customerIdentifiers : array(), // Custom indentifiers
            'statementNarrative' => $_POST['statement-narrative'],
            'orderCodePrefix' => $_POST['code-prefix'],
            'orderCodeSuffix' => $_POST['code-suffix'],
            'customerOrderCode' => $_POST['customer-order-code'], // Order code of your choice
            'successUrl' => $_POST['success-url'], //Success redirect url for APM
            'pendingUrl' => $_POST['pending-url'], //Pending redirect url for APM
            'failureUrl' => $_POST['failure-url'], //Failure redirect url for APM
            'cancelUrl' => $_POST['cancel-url'] //Cancel redirect url for APM
        );

        if ($directOrder) {
            $obj['directOrder'] = true;
            $obj['shopperLanguageCode'] = isset($_POST['language-code']) ? $_POST['language-code'] : "";
            $obj['reusable'] = (isset($_POST['chkReusable']) && $_POST['chkReusable'] == 'on') ? true : false;

            $apmFields = array();
            if (isset($_POST['swiftCode'])) {
                $apmFields['swiftCode'] = $_POST['swiftCode'];
            }

            if (isset($_POST['shopperBankCode'])) {
                $apmFields['shopperBankCode'] = $_POST['shopperBankCode'];
            }

            if (empty($apmFields)) {
                $apmFields =  new stdClass();
            }

            $obj['paymentMethod'] = array(
                  "apmName" => $_POST['apm-name'],
                  "shopperCountryCode" => $_POST['countryCode'],
                  "apmFields" => $apmFields
            );
        }
        else {
            $obj['token'] = $token; // The token from WorldpayJS
        }

        $response = $worldpay->createApmOrder($obj);

        if ($response['paymentStatus'] === 'PRE_AUTHORIZED') {
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

    }
    else {

        $obj = array(
            'orderDescription' => $_POST['description'], // Order description of your choice
            'amount' => $amount, // Amount in pence
            'is3DSOrder' => $_3ds, // 3DS
            'authorizeOnly' => $authorizeOnly,
            'siteCode' => $_POST['site-code'],
            'orderType' => $_POST['order-type'], //Order Type: ECOM/MOTO/RECURRING
            'currencyCode' => $_POST['currency'], // Currency code
            'settlementCurrency' => $_POST['settlement-currency'], // Settlement currency code
            'name' => ($_3ds && true) ? '3D' : $name, // Customer name
            'shopperEmailAddress' => $shopperEmailAddress, // Shopper email address
            'billingAddress' => $billing_address, // Billing address array
            'deliveryAddress' => $delivery_address, // Delivery address array
            'customerIdentifiers' => (!is_null($customerIdentifiers)) ? $customerIdentifiers : array(), // Custom indentifiers
            'statementNarrative' => $_POST['statement-narrative'],
            'orderCodePrefix' => $_POST['code-prefix'],
            'orderCodeSuffix' => $_POST['code-suffix'],
            'customerOrderCode' => $_POST['customer-order-code'] // Order code of your choice
        );

        if ($directOrder) {
            $obj['directOrder'] = true;
            $obj['shopperLanguageCode'] = isset($_POST['language-code']) ? $_POST['language-code'] : "";
            $obj['reusable'] = (isset($_POST['chkReusable']) && $_POST['chkReusable'] == 'on') ? true : false;
            $obj['paymentMethod'] = array(
                  "name" => $_POST['name'],
                  "expiryMonth" => $_POST['expiration-month'],
                  "expiryYear" => $_POST['expiration-year'],
                  "cardNumber"=>$_POST['card'],
                  "cvc"=>$_POST['cvc']
            );
        }
        else {
            $obj['token'] = $token; // The token from WorldpayJS
        }

        $response = $worldpay->createOrder($obj);

        if ($response['paymentStatus'] === 'SUCCESS' ||  $response['paymentStatus'] === 'AUTHORIZED') {
            // Create order was successful!
            $worldpayOrderCode = $response['orderCode'];
            echo '<p>Order Code: <span id="order-code">' . $worldpayOrderCode . '</span></p>';
            echo '<p>Token: <span id="token">' . $response['token'] . '</span></p>';
            echo '<p>Payment Status: <span id="payment-status">' . $response['paymentStatus'] . '</span></p>';
            echo '<pre>' . print_r($response, true). '</pre>';
            // TODO: Store the order code somewhere..
        } elseif ($response['is3DSOrder']) {
            // Redirect to URL
            // STORE order code in session
            $_SESSION['orderCode'] = $response['orderCode'];
            ?>
            <form id="submitForm" method="post" action="<?php echo $response['redirectURL'] ?>">
                <input type="hidden" name="PaReq" value="<?php echo $response['oneTime3DsToken']; ?>"/>
                <input type="hidden" id="termUrl" name="TermUrl" value="http://localhost/3ds_redirect.php"/>
                <script>
                    document.getElementById('termUrl').value = window.location.href.replace('create_order.php', '3ds_redirect.php');
                    document.getElementById('submitForm').submit();
                </script>
            </form>
            <?php
        } else {
            // Something went wrong
            echo '<p id="payment-status">' . $response['paymentStatus'] . '</p>';
            throw new WorldpayException(print_r($response, true));
        }
    }
} catch (WorldpayException $e) { // PHP 5.3+
    // Worldpay has thrown an exception
    echo 'Error code: ' . $e->getCustomCode() . '<br/>
    HTTP status code:' . $e->getHttpStatusCode() . '<br/>
    Error description: ' . $e->getDescription()  . ' <br/>
    Error message: ' . $e->getMessage();
}

}
?>


<!DOCTYPE html>
    <html>
    <head>
      <title></title>
      <script src='https://cdn.worldpay.com/v1/worldpay.js'></script>
    </head>
    <body>
      <form id='paymentForm' method='post' action='http://localhost/worldpay/'>

        <div id='myPaymentSection'>
        	
<!-- 	
	site-code<input type="text" name="site-code" value="sdfsdafsda"> <br>
	currency<input type="text" name="currency" value="USD"><br>
	settlement-currency<input type="text" name="settlement-currency" value="USD"><br>
	code-prefix<input type="text" name="code-prefix" value="codeprefix"><br>
	customer-order-code<input type="text" name="customer-order-code" value="1as2d3f1s2ad"><br>
	name<input type="text" name="name" value="Hamza"><br>
	description<input type="text" name="description" value="sdfdafasdfsad"><br>
	shopper-email<input type="text" name="shopper-email" value="shopper-email@gmail.com"><br>
	order type<input type="text" name="order-type" value=""><br>
	address<input type="text" name="address1" value="address 1"><br>
	address<input type="text" name="address2" value="address 2"><br>
	address<input type="text" name="address3" value="address 3"><br>
	postcode<input type="text" name="postcode" value="54800"><br>
	city<input type="text" name="city" value="Lahore"><br>
	state<input type="text" name="state" value="Punjab"><br>
	country code<input type="text" name="countryCode" value="PK"><br>
	telephone number<input type="text" name="telephoneNumber" value="03059847261"><br>

    <input type="text" name="delivery-firstName" value="first name deliver"><br>
	<input type="text" name="delivery-lastName" value="last name deliver"><br>
	<input type="text" name="delivery-address1" value="address 1 deliver"><br>
	<input type="text" name="delivery-address2" value="address 2 deliver"><br>
	<input type="text" name="delivery-address3" value="address 3 deliver"><br>
	<input type="text" name="delivery-postcode" value="12312312312"><br>
	<input type="text" name="delivery-city" value="Lahore"><br>
	<input type="text" name="delivery-state" value="Punjab"><br>
	<input type="text" name="delivery-countryCode" value="12"><br>
	<input type="text" name="delivery-telephoneNumber" value="123132123"><br>
	<input type="hidden" name="test" value="yes"><br>
 -->



        </div>
        <input type='button' onclick='Worldpay.submitTemplateForm()' value='Place Order' />
      </form>
      <script type='text/javascript'>
        window.onload = function() {
          Worldpay.useTemplateForm({
            'clientKey':'T_C_5a5c185b-ef1d-424d-9978-b3d0f929183a',
            'saveButton':false,
            'form':'paymentForm',
            'paymentSection':'myPaymentSection',
            'display':'inline',
            'reusable':true,
            'type':'card',
            'callback': function(obj) {
              if (obj && obj.token) {
                var _el = document.createElement('input');
                _el.value = obj.token;
                _el.type = 'hidden';
                _el.name = 'token';
                document.getElementById('paymentForm').appendChild(_el);
                document.getElementById('paymentForm').submit();
              }
            }
          });
        }
      </script>
    </body>
    </html>