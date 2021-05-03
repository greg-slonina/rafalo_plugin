jQuery(document).ready(function($) {
	// Mark Featured - 3.0.1
	$( document.body ).on( 'updated_wcfm-products', function() {
		$('.wcfm_product_featured').each(function() {
			$(this).click(function(event) {
				event.preventDefault();
				jQuery('#wcfm-products_wrapper').block({
					message: null,
					overlayCSS: {
						background: '#fff',
						opacity: 0.6
					}
				});
				var data = {
					action   : 'wcfmu_product_featured',
					proid    : $(this).data('proid'),
					featured : $(this).data('featured')
				}	
				jQuery.ajax({
					type:		'POST',
					url: wcfm_params.ajax_url,
					data: data,
					success:	function(response) {
						$wcfm_products_table.ajax.reload();
						jQuery('#wcfm-products_wrapper').unblock();
					}
				});
				return false;
			});
		});
	});
	
	// Dupliacte Product - 2.5.2
	$( document.body ).on( 'updated_wcfm-products', function() {
		$('.wcfm_product_duplicate').each(function() {
			$(this).click(function(event) {
				event.preventDefault();
				jQuery('#wcfm-products_wrapper').block({
					message: null,
					overlayCSS: {
						background: '#fff',
						opacity: 0.6
					}
				});
				var data = {
					action : 'wcfmu_duplicate_product',
					proid : $(this).data('proid')
				}	
				jQuery.ajax({
					type:		'POST',
					url: wcfm_params.ajax_url,
					data: data,
					success:	function(response) {
						if(response) {
							$response_json = $.parseJSON(response);
							if($response_json.status) {
								if( $response_json.redirect ) window.location = $response_json.redirect;	
							}
						}
					}
				});
				return false;
			});
		});
	});
} );