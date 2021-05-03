$product_type = '';	
$product_cat = '';
$product_vendor = '';

jQuery(document).ready(function($) {
		
	$('.stock_manage_checkbox_all').click(function() {
		if( $(this).is(':checked') ) {
			$('.stock_manage_checkbox_all').attr( 'checked', true );
			$('.stock_manage_checkbox_single').attr( 'checked', true );
		}	else {
			$('.stock_manage_checkbox_all').attr( 'checked', false );
			$('.stock_manage_checkbox_single').attr( 'checked', false );
		}
	});
	
	$wcfm_products_stock_manage_table = $('#wcfm-stock-manage').DataTable( {
		"processing": true,
		"serverSide": true,
		"pageLength": parseInt(dataTables_config.pageLength),
		"responsive": true,
		"language"  : $.parseJSON(dataTables_language),
		"columns"   : [
			              { responsivePriority: 1 },
										{ responsivePriority: 1 },
										{ responsivePriority: 6 },
										{ responsivePriority: 6 },
										{ responsivePriority: 5 },
										{ responsivePriority: 2 },
										{ responsivePriority: 3 },
										{ responsivePriority: 4 },
										{ responsivePriority: 1 },
								],
		"columnDefs": [ { "targets": 0, "orderable" : false }, 
									  { "targets": 1, "orderable" : false }, 
										{ "targets": 2, "orderable" : false }, 
										{ "targets": 3, "orderable" : false }, 
										{ "targets": 4, "orderable" : false }, 
										{ "targets": 5, "orderable" : false },
										{ "targets": 6, "orderable" : false },
										{ "targets": 7, "orderable" : false },
										{ "targets": 8, "orderable" : false },
									],
		'ajax': {
			"type"   : "POST",
			"url"    : wcfm_params.ajax_url,
			"data"   : function( d ) {
				d.action     = 'wcfm_ajax_controller',
				d.controller = 'wcfm-stock-manage',
				d.product_type     = $product_type,
				d.product_cat      = $product_cat,
				d.product_vendor   = $product_vendor,
				d.product_status   = GetURLParameter( 'product_status' )
			},
			"complete" : function () {
				// Fire wcfm-stock-manage table refresh complete
				$( document.body ).trigger( 'updated_wcfm-stock-manage' );
			}
		}
	} );
	
	if( $('#dropdown_product_type').length > 0 ) {
		$('#dropdown_product_type').on('change', function() {
		  $product_type = $('#dropdown_product_type').val();
		  $wcfm_products_stock_manage_table.ajax.reload();
		});
	}
	
	if( $('#dropdown_product_cat').length > 0 ) {
		$('#dropdown_product_cat').on('change', function() {
			$product_cat = $('#dropdown_product_cat').val();
			$wcfm_products_stock_manage_table.ajax.reload();
		}).select2( $wcfm_taxonomy_select_args );
	}
	
	if( $('.dropdown_product_cat').length > 0 ) {
		$('.dropdown_product_cat').on('change', function() {
			$product_cat = $('.dropdown_product_cat').val();
			$wcfm_products_stock_manage_table.ajax.reload();
		});
	}
	
	if( $('#dropdown_vendor').length > 0 ) {
		$('#dropdown_vendor').on('change', function() {
			$product_vendor = $('#dropdown_vendor').val();
			$wcfm_products_stock_manage_table.ajax.reload();
		}).select2( $wcfm_vendor_select_args );
	}
	
	// Screen Manager
	$( document.body ).on( 'updated_wcfm-stock-manage', function() {
		$.each(wcfm_stocks_screen_manage, function( column, column_val ) {
		  $wcfm_products_stock_manage_table.column(column).visible( false );
		} );
	});
	
	// Update Stock Manager
	jQuery('#wcfm_stock_manager_submit_button').click(function( event ) {
		event.preventDefault();
		jQuery('#wcfm_stock_manage_form').block({
			message: null,
			overlayCSS: {
				background: '#fff',
				opacity: 0.6
			}
		});
		jQuery('.wcfm-message').html('').removeClass('wcfm-error').removeClass('wcfm-success').slideUp();
		var data = {
			action : 'wcfm_ajax_controller',
			controller : 'wcfm-stock-manage-update', 
			wcfm_stock_manage_form : jQuery('#wcfm_stock_manage_form').serialize()
		}	
		jQuery.post(wcfm_params.ajax_url, data, function(response) {
			if(response) {
				jQueryresponse_json = jQuery.parseJSON(response);
				jQuery('.wcfm-message').html('').removeClass('wcfm-error').removeClass('wcfm-success').slideUp();
				if(jQueryresponse_json.status) {
					jQuery('#wcfm_stock_manage_form .wcfm-message').html('<span class="wcicon-status-completed"></span>' + jQueryresponse_json.message).addClass('wcfm-success').slideDown();
				} else {
					jQuery('#wcfm_stock_manage_form .wcfm-message').html('<span class="wcicon-status-cancelled"></span>' + jQueryresponse_json.message).addClass('wcfm-error').slideDown();
				}
				$wcfm_products_stock_manage_table.ajax.reload();
				jQuery('#wcfm_stock_manage_form').unblock();
			}
		} );
	});
	
	// Dashboard FIlter
	if( $('.wcfm_filters_wrap').length > 0 ) {
		$('.dataTable').before( $('.wcfm_filters_wrap') );
		$('.wcfm_filters_wrap').css( 'display', 'inline-block' );
	}
	
} );