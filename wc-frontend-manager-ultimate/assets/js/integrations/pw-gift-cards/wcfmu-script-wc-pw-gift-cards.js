$wcfm_pw_gift_cards_table = '';
$pw_gift_cards_vendor = '';

jQuery(document).ready(function($) {
	
	$wcfm_pw_gift_cards_table = $('#wcfm-pw-gift-cards').DataTable( {
		"processing": true,
		"serverSide": true,
		"pageLength": parseInt(dataTables_config.pageLength),
		"responsive": true,
		"language"  : $.parseJSON(dataTables_language),
		"buttons"   : $wcfm_datatable_button_args,
		"columns"   : [
										{ responsivePriority: 1 },
										{ responsivePriority: 4 },
										{ responsivePriority: 3 },
										{ responsivePriority: 1 },
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
				d.controller   = 'wcfm-gift-cards',
				d.license_vendor = $pw_gift_cards_vendor
			},
			"complete" : function () {
				initiateTip();
				
				// Fire wcfm-pw_gift_cards table refresh complete
				$( document.body ).trigger( 'updated_wcfm_pw_gift_cards' );
			}
		}
	} );
	
	if( $('#dropdown_vendor').length > 0 ) {
		$('#dropdown_vendor').on('change', function() {
			$pw_gift_cards_vendor = $('#dropdown_vendor').val();
			$wcfm_pw_gift_cards_table.ajax.reload();
		}).select2( $wcfm_vendor_select_args );
	}
	
	// Gift Card Activity
	$( document.body ).on( 'updated_wcfm_pw_gift_cards', function() {
		$('.wcfm_pw_gift_card_activity').each(function() {
			$(this).click(function(event) {
				event.preventDefault();
				giftcardActivity( $(this) );
				return false;
			});
		});
	});
	
	function giftcardActivity( item ) {
		
		var data = {
							  action         : 'pw-gift-cards-view_activity',
							  card_number    : item.data('cardnumber'),
							  security       : wcfm_pwgc.nonces.view_activity
							}
		jQuery.ajax({
			type    :		'POST',
			url     : wcfm_params.ajax_url,
			data    : data,
			success :	function(response) {
														 
				// Intialize colorbox
				$.colorbox( { html: response.html, width: $popup_width });
			}
		});
	}
	
	// Gift Card Delete
	$( document.body ).on( 'updated_wcfm_pw_gift_cards', function() {
		$('.wcfm_pw_gift_card_delete').each(function() {
			$(this).click(function(event) {
				event.preventDefault();
				deleteGiftCard( $(this) );
				return false;
			});
		});
	});
	
	function deleteGiftCard( item ) {
		
		var data = {
								action        : 'pw-gift-cards-delete', 
								card_number   : item.data('cardnumber'),
								security      : wcfm_pwgc.nonces.delete
							}	
		$.post(wcfm_params.ajax_url, data, function(response) {
			if(response) {
				$wcfm_pw_gift_cards_table.ajax.reload();
			}
		});
	}
	
	// Gift Card Restore
	$( document.body ).on( 'updated_wcfm_pw_gift_cards', function() {
		$('.wcfm_pw_gift_card_restore').each(function() {
			$(this).click(function(event) {
				event.preventDefault();
				restoreGiftCard( $(this) );
				return false;
			});
		});
	});
	
	function restoreGiftCard( item ) {
		
		var data = {
								action        : 'pw-gift-cards-restore', 
								card_number   : item.data('cardnumber'),
								security      : wcfm_pwgc.nonces.restore
							}	
		$.post(wcfm_params.ajax_url, data, function(response) {
			if(response) {
				$wcfm_pw_gift_cards_table.ajax.reload();
			}
		});
	}
	
	// Dashboard FIlter
	if( $('.wcfm_filters_wrap').length > 0 ) {
		$('.dataTable').before( $('.wcfm_filters_wrap') );
		$('.wcfm_filters_wrap').css( 'display', 'inline-block' );
	}
	
	// Screen Manager
	$( document.body ).on( 'updated_wcfm_pw_gift_cards', function() {
		$.each(wcfm_pw_gift_cards_screen_manage, function( column, column_val ) {
		  $wcfm_pw_gift_cards_table.column(column).visible( false );
		} );
	});
} );