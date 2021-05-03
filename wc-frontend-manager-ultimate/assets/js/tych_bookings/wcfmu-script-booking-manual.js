jQuery(document).ready( function($) {
	$("#bookable_product_id").select2({ });
	
	$("#customer_id").select2({ });
	
	$('input[name=create_booking_2]').click(function() {
	  $('input[name=add-to-cart]').attr( 'name', 'wcfm-add-to-cart' );
	});
});