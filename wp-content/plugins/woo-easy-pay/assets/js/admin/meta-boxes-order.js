jQuery(function($){
	wc_meta_box_actions = {
			params: worldpay_admin_meta_boxes,
			init: function(){
				$('#woocommerce-worldpay-actions').on('click', '.capture-charge', this.capture_charge).
				on('click', '.cancel-action', this.cancel).
				on('click', '.do-api-capture', this.api_capture).
				on('click', '.do-api-cancel', this.api_cancel);
			},
			capture_charge: function(){
				$('div.wc-order-capture-charge').slideDown();
				$('div.wc-worldpay-buttons-container').slideUp();
				return false;
			},
			cancel: function(){
				$('div.wc-order-capture-charge').slideUp();
				$('div.wc-worldpay-buttons-container').slideDown();
				return false;
			},
			api_capture: function(){
				wc_meta_box_actions.block();
				
				var data = {
						order_id: woocommerce_admin_meta_boxes.post_id,
						amount: $('#worldpay_capture_amount').val()
				};
				
				$.post(wc_meta_box_actions.params.capture_url, data, function(response){
					if(response.success){
						window.location.href = window.location.href;
					}else{
						wc_meta_box_actions.unblock();
						window.alert(response.message);
					}
				}, 'json').fail(function(jqXHR, textStatus, errorThrown){
					wc_meta_box_actions.unblock();
					window.alert(errorThrown);
				});
			},
			api_cancel: function(){
				wc_meta_box_actions.block();
				var data = {
						order_id: woocommerce_admin_meta_boxes.post_id
				};
				
				$.post(wc_meta_box_actions.params.cancel_url, data, function(response){
					if(response.success){
						window.location.href = window.location.href;
					}else{
						wc_meta_box_actions.unblock();
						window.alert(response.message);
					}
				}, 'json').fail(function(jqXHR, textStatus, errorThrown){
					wc_meta_box_actions.unblock();
					window.alert(errorThrown);
				});
			},
			block: function() {
				$( '#woocommerce-worldpay-order-actions' ).block({
					message: null,
					overlayCSS: {
						background: '#fff',
						opacity: 0.6
					}
				});
			},
			unblock: function() {
				$( '#woocommerce-worldpay-order-actions' ).unblock();
			},
	}
	wc_meta_box_actions.init();
})