jQuery(document).ready(function($) {
  $('.variable-subscription_period').change(function() {
  	$(this).parent().find('.variable-subscription_length_ele').addClass('wcfm_ele_hide wcfm_title_hide');
  	if( $('#product_type').val() == 'variable-subscription' ) {
  		$(this).parent().find('.variable-subscription_length_' + $(this).val()).removeClass('wcfm_ele_hide wcfm_title_hide');
  	}
  }).change();
  
  $( document.body ).on( 'wcfm_product_type_changed', function() {
  	if( $('#product_type').val() == 'variable-subscription' ) {
  		$('.variable-subscription_period').change();
  	}
  });
  if( $('#product_type').val() == 'variable-subscription' ) {
		$('.variable-subscription_period').change();
	}
  
  $('#variations').find('.add_multi_input_block').click(function() {
  	$('.variable-subscription_period:last').change(function() {
			$(this).parent().find('.variable-subscription_length_ele').addClass('wcfm_ele_hide wcfm_title_hide');
			if( $('#product_type').val() == 'variable-subscription' ) {
				$(this).parent().find('.variable-subscription_length_' + $(this).val()).removeClass('wcfm_ele_hide wcfm_title_hide');
			}
		});
  });
});