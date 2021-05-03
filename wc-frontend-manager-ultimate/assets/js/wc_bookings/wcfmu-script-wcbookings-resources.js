$wcfm_bookings_resources_table = '';
$resource_vendor = '';

jQuery(document).ready(function($) {
	
	$wcfm_bookings_resources_table = $('#wcfm-bookings-resources').DataTable( {
		"processing": true,
		"serverSide": true,
		"pageLength": parseInt(dataTables_config.pageLength),
		"responsive": true,
		"language"  : $.parseJSON(dataTables_language),
		"columns"   : [
										{ responsivePriority: 1 },
										{ responsivePriority: 3 },
										{ responsivePriority: 4 },
										{ responsivePriority: 2 },
										{ responsivePriority: 5 },
										{ responsivePriority: 1 }
								],
		"columnDefs": [ { "targets": 0, "orderable" : false }, 
									  { "targets": 1, "orderable" : false }, 
										{ "targets": 2, "orderable" : false },
										{ "targets": 3, "orderable" : false },
										{ "targets": 4, "orderable" : false },
										{ "targets": 5, "orderable" : false },
									],
		'ajax': {
			"type"   : "POST",
			"url"    : wcfm_params.ajax_url,
			"data"   : function( d ) {
				d.action          = 'wcfm_ajax_controller',
				d.controller      = 'wcfm-bookings-resources',
				d.resource_vendor = $resource_vendor
			},
			"complete" : function () {
				initiateTip();
				
				// Fire wcfm-bookings table refresh complete
				$( document.body ).trigger( 'updated_wcfm-bookings-resources' );
			}
		}
	} );
	
	if( $('#dropdown_vendor').length > 0 ) {
		$('#dropdown_vendor').on('change', function() {
			$resource_vendor = $('#dropdown_vendor').val();
			$wcfm_bookings_resources_table.ajax.reload();
		}).select2( $wcfm_vendor_select_args );
	}
	
	// Delete Article
	$( document.body ).on( 'updated_wcfm-bookings-resources', function() {
		$('.wcfm_booking_resource_delete').each(function() {
			$(this).click(function(event) {
				event.preventDefault();
				var rconfirm = confirm(wcfm_dashboard_messages.resource_delete_confirm);
				if(rconfirm) deleteWCFMBookingResource($(this));
				return false;
			});
		});
	});
	
	function deleteWCFMBookingResource(item) {
		jQuery('#wcfm-bookings-resources_wrapper').block({
			message: null,
			overlayCSS: {
				background: '#fff',
				opacity: 0.6
			}
		});
		var data = {
			action    : 'delete_wcfm_booking_resource',
			resourceid : item.data('resourceid')
		}	
		jQuery.ajax({
			type:		'POST',
			url: wcfm_params.ajax_url,
			data: data,
			success:	function(response) {
				if($wcfm_bookings_resources_table) $wcfm_bookings_resources_table.ajax.reload();
				jQuery('#wcfm-bookings-resources_wrapper').unblock();
			}
		});
	}
	
	// Dashboard FIlter
	if( $('.wcfm_filters_wrap').length > 0 ) {
		$('.dataTable').before( $('.wcfm_filters_wrap') );
		$('.wcfm_filters_wrap').css( 'display', 'inline-block' );
	}
	
	// Screen Manager
	$( document.body ).on( 'updated_wcfm-bookings-resources', function() {
		$.each(wcfm_booking_resources_screen_manage, function( column, column_val ) {
		  $wcfm_bookings_resources_table.column(column).visible( false );
		} );
	});
} );