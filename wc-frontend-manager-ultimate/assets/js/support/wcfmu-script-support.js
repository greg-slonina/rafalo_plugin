$wcfm_support_table = '';
var supportBoardRefrsherTime = '';

jQuery(document).ready(function($) {
	$support_product  = '';
	$support_vendor   = '';
	$support_priority = '';
	$report_for       = '';
	
	$wcfm_support_table = $('#wcfm-support').DataTable( {
		"processing": true,
		"serverSide": true,
		"pageLength": parseInt(dataTables_config.pageLength),
		"bFilter"   : false,
		"responsive": true,
		"language"  : $.parseJSON(dataTables_language),
		"columns"   : [
										{ responsivePriority: 1 },
										{ responsivePriority: 1 },
										{ responsivePriority: 1 },
										{ responsivePriority: 2 },
										{ responsivePriority: 3 },
										{ responsivePriority: 5 },
										{ responsivePriority: 2 },
										{ responsivePriority: 4 },
										{ responsivePriority: 6 },
										{ responsivePriority: 2 },
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
									],
		'ajax': {
			"type"   : "POST",
			"url"    : wcfm_params.ajax_url,
			"data"   : function( d ) {
				d.action          = 'wcfm_ajax_controller',
				d.controller      = 'wcfm-support',
				d.support_product = $support_product,
				d.support_vendor  = $support_vendor,
				d.support_priority= $support_priority,
				d.support_status  = GetURLParameter( 'support_status' ),
				d.filter_date_form  = $filter_date_form,
				d.filter_date_to    = $filter_date_to
			},
			"complete" : function () {
				initiateTip();
				
				// Fire wcfm-groups table refresh complete
				$( document.body ).trigger( 'updated_wcfm-support' );
			}
		}
	} );
	
	// Screen Manager
	$( document.body ).on( 'updated_wcfm-support', function() {
		$.each(wcfm_support_screen_manage, function( column, column_val ) {
		  $wcfm_support_table.column(column).visible( false );
		} );
	});
	
	$( document.body ).on( 'wcfm-date-range-refreshed', function() {
		$wcfm_support_table.ajax.reload();
	});
	
	if( $('#support_product').length > 0 ) {
		$('#support_product').on('change', function() {
		  $support_product = $('#support_product').val();
		  $wcfm_support_table.ajax.reload();
		}).select2( $wcfm_product_select_args );
	}
	
	if( $('#support_priority').length > 0 ) {
		$('#support_priority').on('change', function() {
		  $support_priority = $('#support_priority').val();
		  $wcfm_support_table.ajax.reload();
		});
	}
	
	if( $('#dropdown_vendor').length > 0 ) {
		$('#dropdown_vendor').on('change', function() {
			$support_vendor = $('#dropdown_vendor').val();
			$wcfm_support_table.ajax.reload();
		}).select2( $wcfm_vendor_select_args );
	}
	
	// Delete support
	$( document.body ).on( 'updated_wcfm-support', function() {
		$('.wcfm_support_delete').each(function() {
			$(this).click(function(event) {
				event.preventDefault();
				var rconfirm = confirm(wcfm_dashboard_messages.support_delete_confirm);
				if(rconfirm) deleteWCFMSupport($(this));
				return false;
			});
		});
	});
	
	function deleteWCFMSupport(item) {
		jQuery('#wcfm_support_listing_expander').block({
			message: null,
			overlayCSS: {
				background: '#fff',
				opacity: 0.6
			}
		});
		var data = {
			action   : 'delete_wcfm_support',
			supportid : item.data('supportid')
		}	
		jQuery.ajax({
			type:		'POST',
			url: wcfm_params.ajax_url,
			data: data,
			success:	function(response) {
				if($wcfm_support_table) $wcfm_support_table.ajax.reload();
				jQuery('#wcfm_support_listing_expander').unblock();
			}
		});
	}
	
	// Support Board auto Refresher
	function supportBoardRefrsher() {
		clearTimeout(supportBoardRefrsherTime);
		supportBoardRefrsherTime = setTimeout(function() {
			$wcfm_support_table.ajax.reload();
			supportBoardRefrsher();
		}, 30000 );
	}
	//supportBoardRefrsher();
	
	// Dashboard FIlter
	if( $('.wcfm_filters_wrap').length > 0 ) {
		$('.dataTable').before( $('.wcfm_filters_wrap') );
		$('.wcfm_filters_wrap').css( 'display', 'inline-block' );
	}
} );