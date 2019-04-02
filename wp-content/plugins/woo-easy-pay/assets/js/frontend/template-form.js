jQuery(function($) {
	if (typeof worldpay_template_form_params === "undefined") {
		return;
	}
	var worldpay = {
		vars : worldpay_template_form_params,
		gateway : worldpay_template_form_params.gateway,
		container : 'li.payment_method_' + worldpay_template_form_params.gateway,
		payment_section : 'worldpay_template_form_container',
		cvc_section: 'worldpay_cvc_template_container',
		payment_method_received: false,
		init : function() {
			this.gateway = this.vars.gateway;
			this.payment_type_selector = '[name="' + this.gateway + '_payment_type_key"]';
			this.nonce_selector = '#' + this.gateway + '_payment_nonce';
			this.token_selector = '#' + this.gateway + '_payment_token';
			this.token_type_selector = '#' + this.gateway
					+ '_payment_token_type';
			this.cvc_required = this.vars.cvc_required === "1";
			
			$(document.body).on('updated_checkout', this.updated_checkout);
			$(document.body).on('checkout_error', this.checkout_error);
			$(document.body).on('click', '#place_order', this.submit_template_form);
			$('form.checkout').on(
					'checkout_place_order_' + worldpay.gateway,
					this.process_order);
			this.$form = $(this.container).closest('form');
			if (typeof this.$form.attr('id') == "undefined") {
				this.$form.attr('id', 'checkout-form');
			}
			$(document.body).on('worldpay_payment_method_changed', this.setup_cvc_template);
			$(document.body).on('worldpay_new_method_selected', this.initialize_card_frame);
			$(document.body).on('worldpay_saved_method_selected', this.setup_cvc_template);
			
			var helper = Worldpay.helpers.templateFormCallback;
			Worldpay.helpers.templateFormCallback = function(a, b, c){
				helper.call(Worldpay.helpers, a, b, c);
				if(b.token){
					$(document.body).triggerHandler('worldpay_template_form_callback', [a, b, c]);	
				}
			}
			
			this.initialize_card_frame();
		},
		updated_checkout: function(){
			if(!worldpay.has_saved_methods()){
				worldpay.initialize_card_frame();
			}
		},
		initialize_card_frame : function() {
			$("#" + worldpay.payment_section).empty();
			$("#" + worldpay.cvc_section).empty();
			Worldpay.useTemplateForm(worldpay.get_card_template_attribs());
		},
		setup_cvc_template: function(e){
			$("#" + worldpay.cvc_section).empty();
			$("#" + worldpay.payment_section).empty();
			Worldpay.useTemplateForm(worldpay.get_cvc_template_attribs());
		},
		get_card_template_attribs : function() {
			var attribs = {
				clientKey : worldpay.vars.client_key,
				code: worldpay.vars.form_template,
				form : worldpay.$form.attr('id'),
				saveButton : false,
				paymentSection : worldpay.payment_section,
				display : 'inline',
				reusable : worldpay.vars.reusable === "1",
				type : 'card',
				templateOptions: {
					dimensions: {
						width: false,
						height: false
					}
				},
				callback : function(response) {
					worldpay.on_payment_nonce_received(response);
				},
				validationError : function(error) {
					
				}
			};
			return attribs;
		},
		get_cvc_template_attribs: function(token){
			var attribs = {
					clientKey : worldpay.vars.client_key,
					code: worldpay.vars.cvc_template,
					token: $('#online_worldpay_token').val(),
					form : worldpay.$form.attr('id'),
					paymentSection: worldpay.cvc_section,
					display: 'inline',
					type: 'cvc',
					saveButton : false,
					callback: function(response){
						worldpay.on_cvc_token_received(response);
					},
					validationError: function(error){
						//console.log(error);
					}
			};
			if(worldpay.vars.cvc_template == ""){
				attribs['templateOptions'] = {
					iframeHolderInline: 'background-color: transparent;',
					dimensions: {
					      width: '240px',
					      height: '165px'
					},
					images: {enabled: false}
				}
			}
			return attribs;
		},
		submit_template_form : function(e) {
			if (worldpay.gateway_selected()) {
				if(worldpay.use_saved_card()){
					if(worldpay.cvc_required_for_vaulted()){
						e.preventDefault();
						Worldpay.submitTemplateForm();
					}
				}else{
					e.preventDefault();
					Worldpay.submitTemplateForm();
				}
			}
		},
		on_payment_nonce_received : function(response) {
			worldpay.payment_method_received = true;
			worldpay.set_nonce(response.token);
			worldpay.set_token_type(response.paymentMethod.type);
			$(worldpay.container).closest('form').submit();
		},
		on_cvc_token_received: function(response){
			worldpay.payment_method_received = true;
			//$(worldpay.token_selector).val(response.token);
			$(worldpay.container).closest('form').submit();
		},
		process_order : function() {
			if (worldpay.use_saved_card() && !worldpay.cvc_required_for_vaulted()) {
				return true;
			}else if(worldpay.cvc_required_for_vaulted()){
				return worldpay.payment_method_received;
			} else {
				return worldpay.payment_method_received;
			}
		},
		nonce_available : function() {
			return $(worldpay.nonce_selector).val() !== "";
		},
		gateway_selected : function() {
			return $('input[name="payment_method"]:checked').val() === worldpay.gateway
		},
		checkout_error : function() {
			$("#place_order").prop('disabled', false);
			worldpay.clear_nonce();
			worldpay.payment_method_received = false;
		},
		set_nonce : function(token) {
			$(worldpay.nonce_selector).val(token)
		},
		set_token_type : function(type) {
			$(worldpay.token_type_selector).val(type)
		},
		clear_nonce : function() {
			$(worldpay.nonce_selector).val("");
		},
		has_checkout_error : function() {
			return $('#owp_checkout_error').length > 0;
		},
		use_saved_card : function() {
			return $(worldpay.payment_type_selector).length && $(worldpay.payment_type_selector + ':checked').val() === "token";
		},
		has_saved_methods: function(){
			return $(worldpay.token_selector).length > 0;
		},
		cvc_required_for_vaulted: function(){
			return worldpay.cvc_required;
		},
		submit_error: function(error){
			$(document.body).triggerHandler('worldpay_error_message_handler', {error: error, container: worldpay.$form, gateway: worldpay.gateway});
		},
	}
	worldpay.init();
});