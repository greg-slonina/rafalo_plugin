jQuery(document).ready(function($) {
	// Quote Status Update
	$('#wcfm_modify_quote_status').click(function(event) {
		event.preventDefault();
		modifyWCFMRentalQuoteStatus();
		return false;
	});
		
	function modifyWCFMRentalQuoteStatus() {
		$('#quotes_details_general_expander').block({
			message: null,
			overlayCSS: {
				background: '#fff',
				opacity: 0.6
			}
		});
		var data = {
			action       : 'wcfm_modify_rental_quote_status',
			quote_status : $('#wcfm_quote_status').val(),
			quote_price  : $('#wcfm_quote_price').val(),
			quote_id     : $('#wcfm_modify_quote_status').data('quoteid')
		}	
		$.ajax({
			type:		'POST',
			url: wcfm_params.ajax_url,
			data: data,
			success:	function(response) {
				$response_json = $.parseJSON(response);
				$('.wcfm-message').html('').removeClass('wcfm-error').removeClass('wcfm-success').slideUp();
				if($response_json.status) {
					wcfm_notification_sound.play();
					$('#quotes_details_general_expander .wcfm-message').html('<span class="wcicon-status-completed"></span>' + $response_json.message).addClass('wcfm-success').slideDown( "slow" );
				}
				$('#quotes_details_general_expander').unblock();
			}
		});
	}
	
	// Order Add Note
	$('#wcfm_add_order_note').click(function(event) {
		event.preventDefault();
		addWCFMOrderNote();
		return false;
	});
		
	function addWCFMOrderNote() {
		$('#wcfm_order_notes_options').block({
			message: null,
			overlayCSS: {
				background: '#fff',
				opacity: 0.6
			}
		});
		var data = {
			action    : 'wcfm_rental_quote_message',
			note      : $('#add_order_note').val(),
			quote_id  : $('#wcfm_add_order_note').data('quote_id')
		}	
		$.ajax({
			type:		'POST',
			url: wcfm_params.ajax_url,
			data: data,
			success:	function(response) {
				window.location = window.location.href;
				//$('#notes_holder').append(response);
				$('#add_order_note').val('');
				$('#wcfm_order_notes_options').unblock();
			}
		});
	}
});