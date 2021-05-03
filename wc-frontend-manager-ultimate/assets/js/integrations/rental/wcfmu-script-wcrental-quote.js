$wcfm_quotes_table = '';

jQuery(document).ready(function($) {
	
	$wcfm_quotes_table = $('#wcfm-quotes').DataTable( {
		"processing": true,
		"serverSide": true,
		"pageLength": parseInt(dataTables_config.pageLength),
		"responsive": true,
		"language"  : $.parseJSON(dataTables_language),
		"columns"   : [
										{ responsivePriority: 1 },
										{ responsivePriority: 1 },
										{ responsivePriority: 2 },
										{ responsivePriority: 4 },
										{ responsivePriority: 3 },
										{ responsivePriority: 5 },
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
				d.action       = 'wcfm_ajax_controller',
				d.controller   = 'wcfm-rental-quote',
				d.quote_status = GetURLParameter( 'quote_status' )
			},
			"complete" : function () {
				initiateTip();
				
				// Fire wcfm-quotes table refresh complete
				$( document.body ).trigger( 'updated_wcfm-quotes' );
			}
		}
	} );
} );