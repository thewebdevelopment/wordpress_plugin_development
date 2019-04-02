jQuery(function($){
	if (typeof worldpay_own_form_params === "undefined") {
		return;
	}
	var worldpay = {
			vars : worldpay_own_form_params,
			cvc_selector: '#worldpay_cvc',
			payment_method_received: false,
			cvc_received: false,
			init: function(){
				this.gateway = this.vars.gateway;
				this.payment_type_selector = '[name="' + this.gateway + '_payment_type_key"]';
				this.container = 'li.payment_method_' + this.gateway;
				this.nonce_selector = '#' + this.gateway + '_payment_nonce';
				this.token_selector = '#' + this.gateway + '_payment_token';
				this.$form = $(worldpay.container).closest('form');
				if (typeof this.$form.attr('id') == "undefined") {
					this.$form.attr('id', 'checkout-form');
				}
				this.cvc_required = this.vars.cvc_required === "1";
				this.has_saved_methods = this.vars.has_saved_methods === "1";
				$('form.checkout').on(
						'checkout_place_order_' + this.gateway,
						this.process_order);
				$(document.body).on('updated_checkout', this.updated_checkout);
				$(document.body).on('click', '#place_order', this.place_order);
				$(document.body).on('checkout_error', this.checkout_error);
				$(document.body).on('keyup', '[data-worldpay="number"]', this.update_card_type);
				/*$(document.body).on('keyup', '[data-worldpay="number"]', this.format_card_number);*/
				$(document.body).on('worldpay_new_method_selected', this.new_method_selected);
				$(document.body).on('worldpay_saved_method_selected', this.saved_method_selected);
				$(document.body).on('worldpay_payment_method_changed', this.update_token_attribute);
				
				if(typeof Worldpay !== "undefined"){
					if(Worldpay.card){
						var reuseToken = Worldpay.card.reuseToken;
						Worldpay.card.reuseToken = function(a, b, c){
							if(worldpay.cvc_received){
								return true;
							}
							if(reuseToken){
								reuseToken.call(Worldpay, a, b, c);
							}
						}
						var createToken = Worldpay.card.createToken;
						Worldpay.card.createToken = function(a, b){
							if(worldpay.payment_method_received){
								return true;
							}
							if(createToken){
								createToken.call(Worldpay, a, b);
							}
						}
					}
				}
				this.initialize_form();
				if(this.has_saved_methods){
					this.initialize_cvv();
				}
			},
			new_method_selected: function(){
				worldpay.initialize_form();
			},
			saved_method_selected: function(){
				worldpay.initialize_cvv();
			},
			initialize_form: function(){
				Worldpay.useOwnForm({
					clientKey: worldpay.vars.client_key,
					form: worldpay.$form[0],
					reusable: worldpay.vars.reusable === "1",
					formatCardNumber: true,
					templateType: 'card',
					cvc: false,
					callback: function(status, response){
						if(response.error && status != 200){
							worldpay.submit_error(response.error);
							return;
						}
						if(response.token){
							worldpay.worldpay_form_function = worldpay.$form.attr('onsubmit');
							worldpay.$form.attr('onsubmit', '');
							worldpay.on_payment_nonce_received(response);
						}
					}
				}, $.extend($(document.body).triggerHandler('worldpay_own_form_params', {})));
			},
			initialize_cvv: function(){
				if(worldpay.cvc_required_for_vaulted()){
					Worldpay.setClientKey(worldpay.vars.client_key);
					Worldpay.useOwnForm({
						clientKey: worldpay.vars.client_key,
						form: worldpay.$form[0],
						reusable: worldpay.vars.reusable === "1",
						templateType: 'cvc',
						useReusableToken: true,
						callback: function(status, response){
							if(response.error && status != 200){
								worldpay.submit_error(response.error);
								return;
							}
							worldpay.worldpay_form_function = document.getElementById(worldpay.$form.attr('id')).onsubmit;
							worldpay.$form.attr('onsubmit', '');
							worldpay.on_cvc_received(response);
						}
					});
				}
			},
			place_order: function(){
				if(worldpay.is_gateway_selected()){
					$('[data-worldpay="name"]').val($('#billing_first_name').val() + ' ' + $("#billing_last_name").val());
					if(worldpay.use_saved_card()){
						if(worldpay.cvc_required){
							worldpay.initialize_cvv();
						}
					}else{
						worldpay.initialize_form();
					}
				}
			},
			update_token_attribute: function(e, token){
				$(worldpay.token_selector).attr('data-worldpay', 'token');
			},
			updated_checkout: function(){
				worldpay.initialize_form();
				if(worldpay.has_saved_methods){
					worldpay.initialize_cvv();
				}
			},
			process_order: function(){
				if(worldpay.use_saved_card()){
					if(worldpay.cvc_required){
						return worldpay.cvc_received;
					}
					return true;
				}else{
					return worldpay.payment_method_received;
				}
			},
			on_payment_nonce_received: function(response){
				worldpay.payment_method_received = true;
				worldpay.set_nonce(response.token);
				worldpay.$form.submit();
			},
			on_cvc_received: function(response){
				worldpay.cvc_received = true;
				worldpay.$form.submit();
			},
			set_nonce: function(nonce){
				$(worldpay.nonce_selector).val(nonce);
			},
			is_gateway_selected : function() {
				return $('input[name="payment_method"]:checked').val() === worldpay.gateway
			},
			checkout_error: function(){
				document.getElementById(worldpay.$form.attr('id')).onsubmit = worldpay.worldpay_form_function;
				worldpay.payment_method_received = false;
				worldpay.cvc_received = false;
				worldpay.set_nonce("");
				$('[data-worldpay="number"]').trigger('keypress');
			},
			submit_error: function(error){
				$(document.body).triggerHandler('worldpay_error_message_handler', {error: error, container: worldpay.$form, gateway: worldpay.gateway});
			},
			use_saved_card: function(){
				return $(worldpay.payment_type_selector).length && $(worldpay.payment_type_selector + ':checked').val() === "token";
			},
			update_card_type: function(e){
				var number = $(this).val();
				var type = false;
				type = Worldpay.card.cardFromNumber(number);
				if(typeof type != "undefined"){
					$('.worldpay-card-type').attr('src', worldpay.vars.cards[type.type]);
				}else{
					$('.worldpay-card-type').attr('src', worldpay.vars.cards['cc_format']);
				}
			},
			cvc_required_for_vaulted: function(){
				return worldpay.cvc_required;
			}
	};
	worldpay.init();
});