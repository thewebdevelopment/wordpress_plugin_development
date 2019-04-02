jQuery(function($){
	if(typeof worldpay_paypal_params === "undefined"){
		return;
	}
	var paypal = {
			params: worldpay_paypal_params,
			payment_nonce: "",
			payment_nonce_received: false,
			init: function(){
				this.gateway = this.params.gateway;
				this.payment_type_selector = '[name="' + this.gateway + '_payment_type_key"]';
				this.reusable = this.params.reusable === "1";
				this.container = 'li.payment_method_' + this.gateway;
				this.nonce_selector = '#' + this.gateway + '_payment_nonce';
				this.token_selector = '#' + this.gateway + '_payment_token';
				this.token_type_selector = '#'+ this.gateway + '_payment_token_type';
				this.$form = $(this.container).closest('form');
				
				$('form.checkout').on(
						'checkout_place_order_' + this.gateway,
						this.process_order);
				$(document.body).on('updated_checkout', this.updated_checkout);
				$(document.body).on('click', '#place_order, .worldpay-paypal-button', this.place_order);
				$(document.body).on('checkout_error', this.checkout_error);
				$(document.body).on('click', '.worldpay-paypal', this.place_order);
				$(document.body).on('worldpay_payment_method_selected', this.payment_gateway_selected);
				$(document.body).on('worldpay_new_method_selected', this.new_method_selected);
				$(document.body).on('worldpay_saved_method_selected', this.saved_method_selected);
				$(document.body).on('worldpay_template_form_callback', function(e, status, response){
					paypal.on_payment_nonce_received(response);
				});
				this.create_button();
			},
			create_button: function(){
				if(paypal.$button){
					paypal.$button.remove();
				}
				paypal.$button = $(paypal.params.button_html);
				paypal.$button.hide();
				$("#place_order").parent().append(paypal.$button);
			},
			process_order: function(){
				if(paypal.use_saved_card()){
					return true;
				}else{
					return paypal.payment_nonce_received;
				}
			},
			on_payment_nonce_received: function(response){
				paypal.payment_nonce_received = true;
				paypal.set_nonce(response.token);
				$(paypal.token_type_selector).val(response.paymentMethod.type);
				paypal.$form.submit();
			},
			updated_checkout: function(){
				paypal.create_button();
				paypal.payment_gateway_selected();
			},
			place_order: function(){
				$('[data-worldpay="country-code"]').val($('#billing_country').val());
				paypal.prepare_form();
				paypal.$form.submit();
			},
			prepare_form: function(){
				if(paypal.is_gateway_selected() && !paypal.use_saved_card()){
					Worldpay.tokenType = "apm";
					Worldpay.setClientKey(paypal.params.client_key);
					Worldpay.reusable = true;
					Worldpay.useForm(paypal.$form[0], function(status, response){
						if(response.error && status != 200){
							paypal.submit_error(response.error);
							return;
						}
						if(response.token){
							paypal.worldpay_form_function = document.getElementById(paypal.$form.attr('id')).onsubmit;
							paypal.$form.attr('onsubmit', '');
							paypal.on_payment_nonce_received(response);	
						}
					});
				}
			},
			set_nonce: function(val){
				$(paypal.nonce_selector).val(val);
			},
			checkout_error: function(){
				Worldpay.tokenType = "";
				$("#place_order").prop('disabled', false);
				paypal.payment_nonce = "";
				paypal.set_nonce("");
				paypal.payment_nonce_received = false;
			},
			submit_error: function(error){
				$(document.body).triggerHandler('worldpay_error_message_handler', {error: error, container: paypal.$form, gateway: paypal.gateway});
			},
			use_saved_card: function(){
				return $(paypal.payment_type_selector).length && $(paypal.payment_type_selector + ':checked').val() === "token";
			},
			is_gateway_selected: function(){
				return $('[name="payment_method"]:checked').val() === paypal.gateway;
			},
			payment_gateway_selected: function(){
				$button = $("#place_order");
				if(paypal.is_gateway_selected()){
					if(paypal.use_saved_card() || paypal.payment_nonce_received){
						paypal.$button.hide();
						$button.show();
					}else{
						paypal.$button.show();
						$button.hide();
					}
				}else{
					paypal.$button.hide();
					$button.show();
				}
			},
			new_method_selected: function(){
				paypal.payment_gateway_selected();
			},
			saved_method_selected: function(){
				paypal.$form.attr('onsubmit', '');
				paypal.payment_gateway_selected();
			}
	}
	paypal.init();
})