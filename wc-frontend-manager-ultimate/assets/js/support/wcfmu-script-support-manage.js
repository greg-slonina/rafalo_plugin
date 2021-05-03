jQuery(document).ready( function($) {
	$wcfm_messages_table = '';
	
	// Save Settings
	$('#wcfm_reply_send_button').click(function(event) {
	  event.preventDefault();
	  
	  var support_ticket_reply = getWCFMEditorContent( 'support_ticket_reply' );
  
	  // Validations
	  $is_valid = true;
	  
	  $('.wcfm-message').html('').removeClass('wcfm-error').removeClass('wcfm-success').slideUp();
		if(wcfmstripHtml(support_ticket_reply).length <= 1) {
			$is_valid = false;
			$('#wcfm_support_ticket_reply_form .wcfm-message').html('<span class="wcicon-status-cancelled"></span>' + wcfm_support_manage_messages.no_reply).addClass('wcfm-error').slideDown();
			audio.play();
		}
	  
	  if($is_valid) {
			$('#wcfm_support_ticket_reply_form').block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
			
			$form_data = new FormData( document.getElementById('wcfm_support_ticket_reply_form') );
			$form_data.append( 'support_ticket_reply', support_ticket_reply );
			$form_data.append( 'wcfm_support_ticket_reply_form', $('#wcfm_support_ticket_reply_form').serialize() ); 
			$form_data.append( 'action', 'wcfm_ajax_controller' ); 
			$form_data.append( 'controller', 'wcfm-support-manage' ); 
			
			$.ajax({
				type         : 'POST',
				url          : wcfm_params.ajax_url,
				data         : $form_data,
				contentType  : false,
				cache        : false,
				processData  :false,
				success: function(response) {
					if(response) {
						$response_json = $.parseJSON(response);
						$('.wcfm-message').html('').removeClass('wcfm-error').removeClass('wcfm-success').slideUp();
						if($response_json.status) {
							wcfm_notification_sound.play();
							$('#wcfm_support_ticket_reply_form .wcfm-message').html('<span class="wcicon-status-completed"></span>' + $response_json.message).addClass('wcfm-success').slideDown( "slow" , function() {
								if( $response_json.redirect ) window.location = $response_json.redirect;	
							} );
						} else {
							audio.play();
							$('#wcfm_support_ticket_reply_form .wcfm-message').html('<span class="wcicon-status-cancelled"></span>' + $response_json.message).addClass('wcfm-error').slideDown();
						}
						$('#wcfm_support_ticket_reply_form').unblock();
					}
				}
			});	
		}
	});
});