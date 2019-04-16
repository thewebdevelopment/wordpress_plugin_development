<?php
/**
 * Plugin Name:       my worldpay plugin for woocommerece
 * Plugin URI:        www.youtubecounter.com
 * Description:       woocommerece world plugin for testing purpose
 * Version:           1.0.0
 * Author:            Hamza khan
*/
// namespace Worldpay;

// require_once(__DIR__ . '/worldpay/vendor/autoload.php');
// use Worldpay\WorldpayException;



/**
 * Tell woocommerece that your payment class exists.
 */
function add_gateway_class($methods)
{
    $methods[] = 'WC_Worldpay';
    return $methods;
}
add_filter('woocommerce_payment_gateways', 'add_gateway_class');




add_action('plugins_loaded', 'init_your_gateway_class');
/**
 *   extend WC payment gateway class to acces its core funtions.
 */
function init_your_gateway_class()
{
    class WC_Worldpay extends WC_Payment_Gateway
    {
        public $keys_link = "<a href='https://online.worldpay.com/settings/keys' target='_blank'>Get Account Keys</a>";
        public $marchant_id = "<a href='https://online.worldpay.com/settings/keys' target='_blank'>Get Marchant ID</a>";


        public function __construct()
        {
            $this->notify_url            = str_replace('https:', 'http:', add_query_arg('wc-api', 'WC_Worldpay', home_url('/')));
            $this->id = 'custom_worldpay';
            $this->icon = "https://d1yjjnpx0p53s8.cloudfront.net/styles/logo-original-577x577/s3/082014/worldpay_logo_2014.fw_.png?itok=y6AqFXPh";
            $this->has_fields = true;  // if we have field in this gateway.
            $this->method_title = "Custom Worldpay Gateway Plugin";
            $this->method_description = "Custom Worldpay Gateway Plugin";

            $this->init_form_fields(); // define your own payment gateway  fields.


            $this->log = new WC_Logger();


            // Load the settings.
            $this->init_settings();

            $this->title = "Custom WorldPay Plugin";
            $this->description = "Custom WorldPay Plugin";

            $this->enabled = $this->get_option('enabled');
            $this->environment = $this->get_option('title');
            $this->custom_worldpay_marchant_id = $this->testmode ? $this->get_option('custom_worldpay_marchant_id') : $this->get_option('custom_worldpay_marchant_id');
            $this->custom_worldpay_service_key = $this->testmode ? $this->get_option('custom_worldpay_service_key') : $this->get_option('custom_worldpay_service_key');
            $this->custom_worldpay_client_key = $this->testmode ? $this->get_option('custom_worldpay_client_key') : $this->get_option('custom_worldpay_client_key');

            // This action hook saves the settings
            add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));

            // // We need custom JavaScript to obtain a token
            // add_action( 'wp_enqueue_scripts', array( $this, 'payment_scripts' ) );
            add_action('woocommerce_receipt_' . $this->id, array($this, 'receipt_page'));
            add_action('woocommerce_api_' . $this->id, array($this, 'check_notify_response'));
        }


        /**
         *  Administrator Form Fields.
         */
        public function init_form_fields()
        {
            $this->form_fields = array(

                'enabled' => array(
                    'title'       => 'Enable/Disable',
                    'label'       => 'Enable Gateway',
                    'type'        => 'checkbox',
                    'description' => '',
                    'default'     => 'no'
                ),

                'environment' => array(
                    'title' => 'Switch Environment',
                    'description' => 'Select worldpay environment from dropdown.',
                    'type' => 'select',
                    'options' => array(
                        'sandbox' => 'Sandbox',
                        'live' => 'Live'
                    )
                ),
                'custom_worldpay_marchant_id' => array(
                    'title'       => 'Marchant ID',
                    'type'        => 'text',
                    'description' => 'Enter worldpay account Marchant ID. ' . $this->marchant_id,
                ),
                'custom_worldpay_service_key' => array(
                    'title'       => 'Service Key',
                    'type'        => 'text',
                    'description' => 'Enter worldpay account service key. ' . $this->keys_link,
                ),
                'custom_worldpay_client_key' => array(
                    'title'       => 'Client Key',
                    'type'        => 'text',
                    'description' => 'Enter worldpay account client key. ' . $this->keys_link,
                )
            );
        }
        public function receipt_page($order_id)
        {
            wp_enqueue_script('worldpay_external_script', 'https://cdn.worldpay.com/v1/worldpay.js', '', '', false);
            wp_enqueue_script('_custom_worldpay_', plugin_dir_url(__FILE__) . 'js/form.js', '', '1.0.0', false);

            $this->log->add('novature', 'receipt_page ' . $order_id);
            $this->log->add('novature', print_r($_REQUEST, true));

            echo '
                <form id="paymentForm" method="post">
                    <span id="paymentErrors"></span>
                    <div class="form-row">
                        <label>Name on Card</label>
                        <input data-worldpay="name" name="name" type="text" value="Visa" id="name" />
                    </div>
                    <div class="form-row">
                        <label>Card Number</label>
                        <input data-worldpay="number" size="20" type="text" value="4012888888881881" />
                    </div>
                    <div class="form-row">
                        <label>Expiration (MM/YYYY)</label>
                        <input data-worldpay="exp-month" size="2" type="text" value="10" />
                        <label> / </label>
                        <input data-worldpay="exp-year" size="4" type="text" value="2020" />
                    </div>
                    <div class="form-row">
                        <label>CVC</label>
                        <input data-worldpay="cvc" size="4" type="text" value="123" />
                    </div>


                    <input type="submit" value="Place Order" />
                </form>
              ';
        }

        public function check_notify_response($order_id)
        {
            echo "----------";
            exit;
            die("xxxxxxxxxxxxxxxxxxxxx");


            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.worldpay.com/v1/orders",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => "{\r\n\"token\": \"TEST_RU_9b1c1780-8c13-40a0-b3e9-a314bfea133e\",\r\n \"orderType\": \"ECOM\",\r\n \"orderDescription\": \"Goods and Services\",\r\n        \"amount\": 10917,\r\n        \"currencyCode\": \"GBP\",\r\n        \"name\": \"3D\",\r\n        \"billingAddress\": {\r\n            \"address1\": \"18 Linver Road\", \r\n            \"postalCode\": \"SW6 3RB\", \r\n            \"city\": \"London\", \r\n            \"countryCode\": \"GB\"\r\n        },\r\n        \"customerIdentifiers\": {\r\n                \"email\": \"john.smith@gmail.com\"\r\n        },\r\n        \"is3DSOrder\": true,\r\n        \"shopperAcceptHeader\": \"acceptheader\",\r\n        \"shopperUserAgent\": \"user agent 1\",\r\n        \"shopperSessionId\": \"123\",\r\n        \"shopperIpAddress\": \"195.35.90.111\"\r\n}",
                CURLOPT_HTTPHEADER => array(
                    "Authorization: T_S_173289a2-4d03-4160-b37f-c6d890379c36",
                    "Postman-Token: c6d2a625-5302-4ee6-9348-8150444a9ac2",
                    "cache-control: no-cache",
                    "TermUrl=https://www.yourmerchantsite.co.uk/3DSecureConfirmation"
                ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);
            if ($err) {
                echo "cURL Error #:" . $err;
            } else {
                echo $response . " is your responce";
            }
        }
        function process_payment($order_id)
        {
            global $woocommerce;
            $order = new WC_Order($order_id);


            $redirect    = add_query_arg(' order ', $order->id, add_query_arg(' key ', $order->order_key, get_permalink(woocommerce_get_page_id(' pay '))));
            return array(
                ' result ' => ' success ',
                ' redirect ' => $redirect
            );

            //die("+++++++++++++++++++++++++");



            // Mark as on-hold (we' re awaiting the che
            
            $order->update_status('on-hold', __('Awaiting cheque payment', 'woocommerce'));

            // Reduce stock levels
            $order->reduce_order_stock();

            // Remove cart
            $woocommerce->cart->empty_cart();

            // Return thankyou redirect
            return array(
                'result' => 'success',
                'redirect' => $this->get_return_url($order)
            );
        }
        /**
		 * Display front-end forom details.
		 */
        public function payment_fields()
        { }
        /**
         * Validate payment_fields
         */
        // public function validate_fields(){

        //     if( empty( $_POST[ 'billing_first_name' ]) ) {
        //         wc_add_notice(  'First name is required!', 'error' );
        //         return false;
        //     }
        //     return true;

        // }
    }
}
