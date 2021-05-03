jQuery(document).ready(function($) {
	$('#adq_visibility_quote').select2();
	$('#adq_visibility_price').select2();
	$('#adq_visibility_cart').select2();
	$('#adq_product_force_button').select2();
	
  var toggleProductAdqOptions = function( el ) {                       
		if (el.is(':checked')) {
			el.parent().find( "div" ).slideUp( 200 );
		} else {
			el.parent().find( "div" ).slideDown( 200 );                
		}
	};            
	
	$( '#_adq_inherit_visibility_quote' ).change(function() { toggleProductAdqOptions ($(this)) }).change();
	$( '#_adq_inherit_visibility_price' ).change(function() { toggleProductAdqOptions ($(this)) }).change();
	$( '#_adq_inherit_visibility_cart' ).change(function() { toggleProductAdqOptions ($(this)) }).change();
	$( '#_adq_inherit_allow_product_comments' ).change(function() { toggleProductAdqOptions ($(this)) }).change();    
	$( '#adq_product_force_button_check').change(function(event) {
			if ( $(event.target).is(':checked') ) {
					$('#adq_product_force_button').removeAttr('disabled')
			} else {
					$('#adq_product_force_button').attr('disabled', 'true');
			}
	}).change()
});