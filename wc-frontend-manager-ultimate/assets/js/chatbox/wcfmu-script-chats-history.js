$chats_vendor = '';

jQuery(document).ready(function($) {
	
	$wcfm_chats_history_table = $('#wcfm-chats_history').DataTable( {
		"processing": true,
		"serverSide": true,
		"responsive": true,
		"pageLength": parseInt(dataTables_config.pageLength),
		"language"  : $.parseJSON(dataTables_language),
		"columns"   : [
			              { responsivePriority: 1 },
										{ responsivePriority: 2 },
										{ responsivePriority: 3 },
										{ responsivePriority: 5 },
										{ responsivePriority: 4 },
										{ responsivePriority: 6 },
										{ responsivePriority: 2 },
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
									],
		'ajax': {
			"type"   : "POST",
			"url"    : wcfm_params.ajax_url,
			"data"   : function( d ) {
				d.action     = 'wcfm_ajax_controller',
				d.controller = 'wcfm-chats-history',
				d.chats_vendor   = $chats_vendor
			},
			"complete" : function () {
				initiateTip();
				
				// Fire wcfm-chats_history table refresh complete
				$( document.body ).trigger( 'updated_wcfm-chats_history' );
			}
		}
	} );
	
	if( $('#dropdown_vendor').length > 0 ) {
		$('#dropdown_vendor').on('change', function() {
			$chats_vendor = $('#dropdown_vendor').val();
			$wcfm_chats_history_table.ajax.reload();
		}).select2( $wcfm_vendor_select_args );
	}
	
	// Chat History Actions
	$( document.body ).on( 'updated_wcfm-chats_history', function() {
		// Chat History Details
		$('.wcfm_show_chat_conversation').each(function() {
			$(this).click(function(event) {
				event.preventDefault();
				$conversation = $(this).data('conversation');
				initChatHistoryPopup( $conversation );
			});
		});
		
		// Chat Conversation Delete
		$('.wcfm_chats_history_delete').each(function() {
			$(this).click(function(event) {
				event.preventDefault();
				var rconfirm = confirm( wcfm_chats_history_messages.message_delete_confirm );
				if(rconfirm) {
					$('#wcfm_chats_history_listing_expander').block({
						message: null,
						overlayCSS: {
							background: '#fff',
							opacity: 0.6
						}
					});
					var data = {
						action       : 'wcfm_chats_history_delete',
						conversation : $(this).data('conversation'),
					}	
					$.post(wcfm_params.ajax_url, data, function(response) {
						if(response) {
							$wcfm_chats_history_table.ajax.reload();
							$('#wcfm_chats_history_listing_expander').unblock();
						}
					});
				}
			});
		});
	});
	
	function initChatHistoryPopup( $conversation ) {
		
		var data = {
			action          : 'wcfmu_show_conversation_html',
			conversation    : $conversation,
		}	
		
		jQuery.ajax({
			type    :		'POST',
			url     : wcfm_params.ajax_url,
			data    : data,
			success :	function(response) {
				// Intialize colorbox
				jQuery.colorbox( { html: response, width: $popup_width, height: 450 } );
			}
		});
	}
	
	// Dashboard FIlter
	if( $('.wcfm_filters_wrap').length > 0 ) {
		$('.dataTable').before( $('.wcfm_filters_wrap') );
		$('.wcfm_filters_wrap').css( 'display', 'inline-block' );
	}
	
	// Screen Manager
	$( document.body ).on( 'updated_wcfm-chats_history', function() {
		$.each(wcfm_chats_history_screen_manage, function( column, column_val ) {
		  $wcfm_chats_history_table.column(column).visible( false );
		} );
	});
	
} );