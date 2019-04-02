jQuery(function($){
	settings = {
			prefix: '#' + $('#worldpay_settings_prefix').val(),
			init: function(){
				$('[name^="woocommerce_online_worldpay"]').on('change', this.display_children);
				
				$('select.worldpay-accepted-cards').on('select2:select', this.reorder_multiselect);
				
				this.display_children();
			},
			display_children: function(e){
				$('[data-show-if]').each(function(el){
					var $this = $(this);
					var values = $this.data('show-if');
					var hidden = [];
					$.each(values, function(k, v){
						if(hidden.indexOf($this.attr('id')) == -1 && $(settings.prefix + k).val() == v){
							$this.closest('tr').show();
						}else{
							$this.closest('tr').hide();
							hidden.push($this.attr('id'));
						}
					});
				});
			},
			reorder_multiselect: function(e){
				var element = e.params.data.element;
				var $element = $(element);
				$element.detach();
				$(this).append($element);
				$(this).trigger('change');
			}
	};
	settings.init();
});