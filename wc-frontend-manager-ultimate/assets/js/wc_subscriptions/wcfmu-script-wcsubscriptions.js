$wcfm_subscriptions_table = '';
$subscription_status = '';	
$subscription_product = '';
$subscription_filter = '';	

jQuery(document).ready(function($) {
	
	$wcfm_subscriptions_table = $('#wcfm-subscriptions').DataTable( {
		"processing": true,
		"serverSide": true,
		"pageLength": parseInt(dataTables_config.pageLength),
		"bFilter"   : false,
		"responsive": true,
		"language"  : $.parseJSON(dataTables_language),
		"columns"   : [
										{ responsivePriority: 2 },
										{ responsivePriority: 1 },
										{ responsivePriority: 4 },
										{ responsivePriority: 5 },
										{ responsivePriority: 3 },
										{ responsivePriority: 6 },
										{ responsivePriority: 7 },
										{ responsivePriority: 8 },
										{ responsivePriority: 9 },
										{ responsivePriority: 3 },
										{ responsivePriority: 9 },
										{ responsivePriority: 1 }
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
										{ "targets": 9, "orderable" : false },
										{ "targets": 10, "orderable" : false },
										{ "targets": 11, "orderable" : false },
									],
		'ajax': {
			"type"   : "POST",
			"url"    : wcfm_params.ajax_url,
			"data"   : function( d ) {
				d.action       = 'wcfm_ajax_controller',
				d.controller   = 'wcfm-subscriptions',
				d.subscription_status  = GetURLParameter( 'subscription_status' ),
				d.subscription_product = $subscription_product,
				d.subscription_filter  = $subscription_filter,
				d.filter_date_form     = $filter_date_form,
				d.filter_date_to       = $filter_date_to
			},
			"complete" : function () {
				initiateTip();
				
				// Fire wcfm-subscriptions table refresh complete
				$( document.body ).trigger( 'updated_wcfm-subscriptions' );
			}
		}
	} );
	
	if( $('#subscription_product').length > 0 ) {
		$('#subscription_product').on('change', function() {
		  $subscription_product = $('#subscription_product').val();
		  $wcfm_subscriptions_table.ajax.reload();
		}).select2( $wcfm_product_select_args );
	}
	
	$( document.body ).on( 'wcfm-date-range-refreshed', function() {
		$wcfm_subscriptions_table.ajax.reload();
	});
	
	// Dashboard FIlter
	if( $('.wcfm_filters_wrap').length > 0 ) {
		$('.dataTable').before( $('.wcfm_filters_wrap') );
		$('.wcfm_filters_wrap').css( 'display', 'inline-block' );
	}
	
	// Screen Manager
	$( document.body ).on( 'updated_wcfm-subscriptions', function() {
		$.each(wcfm_subscriptions_screen_manage, function( column, column_val ) {
		  $wcfm_subscriptions_table.column(column).visible( false );
		} );
	});
} );