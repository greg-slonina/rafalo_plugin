$chats_vendor = '';

jQuery(document).ready(function($) {
	
	$wcfm_chats_offline_table = $('#wcfm-chats_offline').DataTable( {
		"processing": true,
		"serverSide": true,
		"responsive": true,
		"pageLength": parseInt(dataTables_config.pageLength),
		"language"  : $.parseJSON(dataTables_language),
		"columns"   : [
			              { responsivePriority: 1 },
										{ responsivePriority: 2 },
										{ responsivePriority: 1 },
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
				d.action     = 'wcfm_ajax_controller',
				d.controller = 'wcfm-chats-offline',
				d.chats_vendor   = $chats_vendor
			},
			"complete" : function () {
				initiateTip();
				
				// Fire wcfm-chats_offline table refresh complete
				$( document.body ).trigger( 'updated_wcfm-chats_offline' );
			}
		}
	} );
	
	if( $('#dropdown_vendor').length > 0 ) {
		$('#dropdown_vendor').on('change', function() {
			$chats_vendor = $('#dropdown_vendor').val();
			$wcfm_chats_offline_table.ajax.reload();
		}).select2( $wcfm_vendor_select_args );
	}
	
	// Chat Offline Messages Actions
	$( document.body ).on( 'updated_wcfm-chats_offline', function() {
		// Chat Offline Messages Delete
		$('.wcfm_chats_offline_delete').each(function() {
			$(this).click(function(event) {
				event.preventDefault();
				var rconfirm = confirm( wcfm_chats_offline_messages.message_delete_confirm );
				if(rconfirm) {
					$('#wcfm_chats_offline_listing_expander').block({
						message: null,
						overlayCSS: {
							background: '#fff',
							opacity: 0.6
						}
					});
					var data = {
						action       : 'wcfm_chats_offline_delete',
						messageid    : $(this).data('messageid'),
					}	
					$.post(wcfm_params.ajax_url, data, function(response) {
						if(response) {
							$wcfm_chats_offline_table.ajax.reload();
							$('#wcfm_chats_offline_listing_expander').unblock();
						}
					});
				}
			});
		});
	});
	
	// Dashboard FIlter
	if( $('.wcfm_filters_wrap').length > 0 ) {
		$('.dataTable').before( $('.wcfm_filters_wrap') );
		$('.wcfm_filters_wrap').css( 'display', 'inline-block' );
	}
	
	// Screen Manager
	$( document.body ).on( 'updated_wcfm-chats_offline', function() {
		$.each(wcfm_chats_offline_screen_manage, function( column, column_val ) {
		  $wcfm_chats_offline_table.column(column).visible( false );
		} );
	});
	
} );