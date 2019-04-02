jQuery(function($){
	handler = {
			init: function(){
				$(document.body).on('worldpay_error_message_handler', this.handle_message);
			},
			handle_message: function(e, object){
				var message = object.error.message, $container = $(object.container);
				$( '.woocommerce-NoticeGroup-checkout, .woocommerce-error, .woocommerce-message' ).remove();
				message =  '<ul class="woocommerce-error"><li>' + message + '</li></ul>'
				$container.prepend( '<div class="woocommerce-NoticeGroup woocommerce-NoticeGroup-checkout">' + message + '</div>' );
				handler.scroll_to_notices($container);
			},
			get_message_from_token: function(){},
			scroll_to_notices: function($container){
				$element = $( '.woocommerce-NoticeGroup-updateOrderReview, .woocommerce-NoticeGroup-checkout' );
				if(!$element){
					$element = $container;
				}
				if($.scroll_to_notices){
					$.scroll_to_notices($element);
				}else{
					$( 'html, body' ).animate( {
						scrollTop: ( $element.offset().top - 100 )
					}, 1000 );
				}
			}
	};
	
	handler.init();
});