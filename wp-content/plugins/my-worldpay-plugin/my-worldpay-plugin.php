<?php
/**
 * Plugin Name:       my worldpay plugin for woocommerece
 * Plugin URI:        www.youtubecounter.com
 * Description:       woocommerece world plugin for testing purpose
 * Version:           1.0.0
 * Author:            Hamza khan
*/
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

            echo "
                <form id='paymentForm' method='post' action='http://localhost/worldpay/create_order_custom_fields.php'>
                <div id='myPaymentSection'></div>
                <input type='button' onclick='Worldpay.submitTemplateForm()' value='Place Order' />
              </form>
              ";
        }
        function process_payment($order_id)
        {
            global $woocommerce;
            $order = new WC_Order($order_id);


            $redirect    = add_query_arg('order', $order->id, add_query_arg('key', $order->order_key, get_permalink(woocommerce_get_page_id('pay'))));
            return array(
                'result' => 'success',
                'redirect' => $redirect
            );

            //die("+++++++++++++++++++++++++");



            // Mark as on-hold (we're awaiting the cheque)
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
