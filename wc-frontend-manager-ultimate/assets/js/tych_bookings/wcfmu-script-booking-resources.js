$wcfm_bookings_resources_table = '';

jQuery(document).ready(function($) {
	
	$wcfm_bookings_resources_table = $('#wcfm-bookings-resources').DataTable( {
		"processing": true,
		"serverSide": true,
		"pageLength": parseInt(dataTables_config.pageLength),
		"responsive": true,
		"language"  : $.parseJSON(dataTables_language),
		"columns"   : [
										{ responsivePriority: 1 },
										{ responsivePriority: 2 },
										{ responsivePriority: 1 }
								],
		"columnDefs": [ { "targets": 0, "orderable" : false }, 
									  { "targets": 1, "orderable" : false }, 
										{ "targets": 2, "orderable" : false }, 
									],
		'ajax': {
			"type"   : "POST",
			"url"    : wcfm_params.ajax_url,
			"data"   : function( d ) {
				d.action       = 'wcfm_ajax_controller',
				d.controller   = 'wcfm-booking-resources'
			},
			"complete" : function () {
				initiateTip();
				
				// Fire wcfm-bookings table refresh complete
				$( document.body ).trigger( 'updated_wcfm-booking-resources' );
			}
		}
	} );
} );