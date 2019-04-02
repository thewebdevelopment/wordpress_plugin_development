=== Online Worldpay For WooCommerce ===
Contributors: support@paymentplugins.com
Donate link: 
Tags: online worldpay, worldpay uk, worldpay, subscriptions, worldpay woocommerce
Requires at least: 3.0.1
Tested up to: 5.1.1
Stable tag: 2.0.1
Copyright: Payment Plugins, support@paymentplugins.com
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==
<strong>This Plugin is for <a target="_blank" href="https://online.worldpay.com/">Online Worldpay</a> Accounts only.</strong>

Worldpay for Woocommerce integrates your Online Worldpay merchant account with your wordpress site. Merchants can accept credit card and PayPal payments for their goods and services via WooCommerce. 
By enabling webhooks, you can receive on demand information about the status of your orders. Worldpay is the UK's lagest payment provider. They have years of experience providing payment solutions that meet the demands
of eCommerce sites. PHP version 5.4 and greater is recommended. For more information on the plugin, please visit https://wordpress.paymentplugins.com.

= Features =
- SAQ A PCI compliant template forms
- 3DS Checkout
- Integrates Online Worldpay with Woocommerce
- Integrates Online Worldpay with Woocommerce Subscriptions 2.0.0+
- Integrates with Currency Switchers
- Accept credit card and Paypal payments
- Webhooks integration
- Customers can use saved payment methods.
- Issue refunds via Woocomerce
- Edit Recurring Orders via Woocommerce
- Change subscription payment method
- Debug log for troubleshooting.

Online Worldpay For WooCommerce allows merchants to accept credit card and Paypal payments via their Worldpay account. By integrating with Woocommerce Subscriptions, merchants that have a Worldpay account can
manage their recurring billing easily and efficienly. Orders can be refunded fully or partially, which will update the Worldpay backend. An in depth transaction log is viewable by admins to review and trouble shoot payment failures.
If you have any issues related to the plugin, please email mr.clayton@paymentplugins.com for assistance. 

== Frequently Asked Questions ==

= Where do I get my API keys from? = 
You API keys can be found by logging into Worldpay at https://online.worldpay.com/login. Once you login, click on Settings and then API keys. 
Copy and paste the API keys from your sandbox and production environment into the plugin configuration page. 

= What is a template form? = 
A template form, is a credit card payment form that you create using Worldpay's form creator. The template form is hosted on Worldpay's server. Once you create and
save a template form, a template code will be generated. The template code is used in the plugin to identify which form you want to be used on your payment pages. 
Since the form is hosted by Worldpay, no payment information touches your server making it a SAQ A PCI compliant solution.

= Does this plugin work with Woocommerce? = 
Yes, Worldpay for Woocommerce integrates with Woocommerce so that you can sell your goods & services through your eCommerce website.
In addition to integrating with Woocommerce, this plugin integrates with Woocommerce Subscriptions 2.0+.

== Screenshots ==

1. API Keys config screen. 
2. WoCommerce Settings.
3. WooCommerce Subscriptions settings.
4. Webhook settings.
5. Debug log.
6. Tutorials.


== Changelog ==
= 2.0.1 = 
* Fixed - Round order total for transaction to prevent JSON parse error
* Fixed - Support popup on settings pages
* Updated - removal of php_uname
= 2.0.0 = 
* Updated - Major update. Credit Card and PayPal now separate gateways. WP Rest API integration with webhooks.
* Added - custom payment forms
* Updated - Admin UI
* Removed - Donations - this is now exclusively a WC plugin.
= 1.2.5 = 
* Added - WC Tested up to 3.4.4
= 1.2.4 = 
* Fixed - PHP 7.1+ warning messages.
= 1.2.3 = 
* Added - Updates for WC 3.0+
* Fixed - Debug log error.
* Updated - Improved redirect logic for 3DS and PayPal.
= 1.2.2 = 
* Added - Version 2.1.0 of Worldpay php library added.
= 1.2.1 = 
* Added - Update to WC Subscription integration.
= 1.2.0 = 
* Fixed - stock properly reduced after payment complete.
= 1.1.9 = 
* Added - Customers can maintain their payment methods via the my account page.
* Added - 3DS integration
= 1.1.8 = 
* Added - Improved subscription logic. A new order is created now for every subscription that is processed.
= 1.1.7 = 
* Fixed - Response codes for failed payment methods now showing correctly. 
* Added - Extra detail added to debugger for failed payments.
= 1.1.6 = 
* Added - Support for saved payment methods added.
* Added - Improved admin ui.
* Added - Webhooks support.
= 1.1.5 = 
* Added - Links on settings page under WooCommerce checkout tab. Some indivduals reported that the admin menu did not appear. 
= 1.1.4 = 
* Fixed - Missing PayPal button when link enabled.
= 1.1.3 = 
* Fixed - Incompatibility with PayPal plugin resolved. 
= 1.1.0 = 
* Added - Additional security measures. 
= 1.0.9 = 
* Fixed - Warning messages on WooPayments admin page when config hasn't been set yet. 
= 1.0.8 = 
* Added - Improved integration with Woocommerce checkout. 
= 1.0.7 = 
* Fixed = With PHP Version 5.3, references to shorthand arrays caused parse error. Resolved now for backwards compatiblity. 
= 1.0.6 = 
* Fixed - Warning messages on admin config page. 
= 1.0.5 = 
* Fixed - Warning messages when WP_DEBUG set to true resolved. Updated deprecated Subscription methods. 
= 1.0.4 = 
* Added - Instructional video on how to maintain configuration settings.
* Removed - Worldpay has confirmed a bug with their PayPal subscription code base. PayPal can still be used for regular and variable products
but not for subscriptions. 
= 1.0.3 = 
* Added - Ability to track plugin users to better assit with technical issues. 
= 1.0.2 = 
* Fixed - If php version < 5.4, then arrays initialized using [] will fail. Backward compatability added. 
= 1.0.1 = 
* Added - Merchants can now select the Paypal logo that displays on their checkout page for Woocommerce and donations. 
* Added - Modal and inline design for donation form. 
= 1.0.0 = 
* Added - First version release. Support for Woocommerce, Woocommerce Subscriptions, order refunds, donations, and Paypal. 
