jQuery(document).ready(function($) {
		
	$wcfm_reviews_table = $('#wcfm-reviews').DataTable( {
		"processing": true,
		"serverSide": true,
		"pageLength": parseInt(dataTables_config.pageLength),
		"bFilter"   : false,
		"responsive": true,
		"language"  : $.parseJSON(dataTables_language),
		"columns"   : [
										{ responsivePriority: 1 },
										{ responsivePriority: 2 },
										{ responsivePriority: 2 },
										{ responsivePriority: 3 }
								],
		"columnDefs": [ { "targets": 0, "orderable" : false }, 
									  { "targets": 1, "orderable" : false }, 
										{ "targets": 2, "orderable" : false }, 
										{ "targets": 3, "orderable" : false },
									],
		'ajax': {
			"type"   : "POST",
			"url"    : wcfm_params.ajax_url,
			"data"   : function( d ) {
				d.action       = 'wcfm_ajax_controller',
				d.controller   = 'wcfm-reviews',
				d.status_type  = GetURLParameter( 'reviews_status' )
			},
			"complete" : function () {
				initiateTip();
				
				// Fire wcfm-reviews table refresh complete
				$( document.body ).trigger( 'updated_wcfm-reviews' );
			}
		}
	} );
	
	// Review Status Update
	$( document.body ).on( 'updated_wcfm-reviews', function() {
		$('.wcfm_review_status_update').each(function() {
			$(this).click(function(event) {
				event.preventDefault();
				var rconfirm = confirm(wcfm_dashboard_messages.review_status_update_confirm);
				if(rconfirm) reviewStatusUpdate($(this));
				return false;
			});
		});
	});
	
	function reviewStatusUpdate(item) {
		jQuery('#wcfm-reviews_wrapper').block({
			message: null,
			overlayCSS: {
				background: '#fff',
				opacity: 0.6
			}
		});
		var data = {
			action : 'wcfmu_reviews_status_update',
			reviewid : item.data('reviewid'),
			status   : item.data('status')
		}	
		jQuery.ajax({
			type:		'POST',
			url: wcfm_params.ajax_url,
			data: data,
			success:	function(response) {
				if($wcfm_reviews_table) $wcfm_reviews_table.ajax.reload();
				jQuery('#wcfm-reviews_wrapper').unblock();
			}
		});
	}
	
	// Dashboard FIlter
	if( $('.wcfm_filters_wrap').length > 0 ) {
		$('.dataTable').before( $('.wcfm_filters_wrap') );
		$('.wcfm_filters_wrap').css( 'display', 'inline-block' );
	}
} );