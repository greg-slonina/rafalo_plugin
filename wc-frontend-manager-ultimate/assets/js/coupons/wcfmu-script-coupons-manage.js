jQuery(document).ready( function($) {
	if( ! $("#product_ids").hasClass('wcfm_ele_for_vendor') ) {
		$("#product_ids").select2( $wcfm_product_select_args );
	}
	
	// Select2 Intialize
	$("#exclude_product_ids").select2( $wcfm_product_select_args );
  
  $("#product_categories").select2({
		placeholder: wcfm_dashboard_messages.choose_category_select2
	});
	
	$("#exclude_product_categories").select2({
		placeholder: wcfm_dashboard_messages.no_category_select2
	});
});