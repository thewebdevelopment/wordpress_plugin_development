jQuery(function($){
	if(typeof worldpay_payment_methods_params === "undefined"){
		return;
	}
	methods = {
			gateways: {},
			vars: worldpay_payment_methods_params,
			container: '.worldpay-gateway-container',
			token_selector: '.worldpay-payment-token',
			new_method_container: '.worldpay-new-method-container',
			saved_method_container: '.worldpay-saved-methods-container',
			init: function(){
				this.init_data_worldpay_attribs();
				$(document.body).on('updated_checkout', this.updated_checkout);
				$(document.body).on('change', '.worldpay-saved-cards', this.change_payment_method);
		
				$(document.body).on('click', '.wc-worldpay-payment-type', this.display_payment_container);
				
				this.setup_dropdown();
				$(document.body).on('payment_method_selected', this.payment_method_selected);
				$(document.body).on('click', 'input[name="payment_method"]', this.payment_method_selected);
			},
			init_data_worldpay_attribs: function(){
				this.data_attribs = {};
				$.each(this.vars.gateways, function(i, gateway){
					$container = $('li.payment_method_' + gateway);
					methods.data_attribs[gateway] = {
							new_method: [],
							saved_method: []
					}
					var attribs = $container.find('.worldpay-new-method-container [data-worldpay]');
					$.each(attribs, function(i, el){
						methods.data_attribs[gateway]['new_method'].push({
							id: '#' + $(el).attr('id'),
							attr: $(el).attr('data-worldpay')
						});
					});
					var attribs = $container.find('.worldpay-saved-methods-container [data-worldpay]');
					$.each(attribs, function(i, el){
						methods.data_attribs[gateway]['saved_method'].push({
							id: '#' + $(el).attr('id'),
							attr: $(el).attr('data-worldpay')
						});
					});
				});
				methods.prepare_form_attribs();
			},
			prepare_form_attribs: function(){
				methods.update_worldpay_attributes();
			},
			display_payment_container: function(e){
				methods.update_worldpay_attributes();
				if($(this).val() === 'token'){
					$(this).closest(methods.container).find(methods.new_method_container).slideUp(400, function(){
						$(this).closest(methods.container).find(methods.saved_method_container).slideDown(400);
						$(document.body).triggerHandler('worldpay_saved_method_selected', $('[name="payment_method"]:checked').val());
					});
				}else{
					$(this).closest(methods.container).find(methods.saved_method_container).slideUp(400, function(){
						$(this).closest(methods.container).find(methods.new_method_container).slideDown(400);
						$(document.body).triggerHandler('worldpay_new_method_selected', $('[name="payment_method"]:checked').val());
					});
				}
			},
			updated_checkout: function(){
				methods.setup_dropdown();
				methods.prepare_form_attribs();
			},
			get_selected_gateway_container: function(){
				var gateway = $('input[name="payment_method"]:checked').val();
				var $container = $('li.payment_method_' + gateway);
				return $container;
			},
			setup_dropdown: function(){
				if($().select2 && $('.worldpay-saved-cards').length){
					$('.worldpay-saved-cards:not(.select2-hidden-accessible)').select2({
						width: '100%',
						templateResult: methods.output_template,
						templateSelection: methods.output_template
					});
					$('.worldpay-saved-cards').trigger('change');
				}
			},
			change_payment_method: function(e){
				$(this).closest(methods.container).find(methods.token_selector).val($(this).val());
				$(document.body).triggerHandler('worldpay_payment_method_changed', $(this).val());
			},
			payment_method_selected: function(e){
				$(document.body).trigger('worldpay_payment_method_selected');
				methods.update_worldpay_attributes();
			},
			update_worldpay_attributes: function(new_method_visible){
				var gateway = $('input[name="payment_method"]:checked').val();
				$('[data-worldpay]').removeAttr('data-worldpay');
				var $payment_type_key = $('[name="' + gateway + '_payment_type_key"]:checked');
				if(!methods.data_attribs[gateway]){
					return;
				}
				if(!$payment_type_key.length || $payment_type_key.val() === 'nonce'){
					var attrs = methods.data_attribs[gateway].new_method;
				}else{
					var attrs = methods.data_attribs[gateway].saved_method;
				}
				$.each(attrs, function(i, obj){
					$(obj.id).attr('data-worldpay', obj.attr);
				})
			},
			output_template: function(data, container){
				var card = $(data.element).data('card-type');
				var img = '';
				if(card){
					$(container).addClass('worldpay-select2-container')
					img = methods.vars.cards[card];
				}
				return $.parseHTML(img + data.text);
			}
	};
	methods.init();
});