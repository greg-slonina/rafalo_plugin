$wcfm_support_submited = false;

jQuery(document).ready(function($) {
	$support_form_show = false;
	
	$('.wcfm-support-action').click(function(event) {
	  event.preventDefault();
	  $order_id = $(this).attr('href');
	  
	  var data = {
			action   : 'wcfmu_support_form_html',
			order_id : $order_id
		}	
		
		jQuery.ajax({
			type    :		'POST',
			url     : wcfm_params.ajax_url,
			data    : data,
			success :	function(response) {
				// Intialize colorbox
				jQuery.colorbox( { html: response, width: $popup_width,
					onComplete:function() {
						
						if( jQuery('.anr_captcha_field').length > 0 ) {
							if (typeof grecaptcha != "undefined") {
								wcfm_support_anr_onloadCallback();
							}
						}
				
						// Intialize Quick Update Action
						jQuery('#wcfm_support_submit_button').click(function(event) {
							jQuery('#wcfm_support_submit_button').hide();
							event.preventDefault();
							jQuery('#wcfm_support_form_wrapper').block({
								message: null,
								overlayCSS: {
									background: '#fff',
									opacity: 0.6
								}
							});
							jQuery('.wcfm-message').html('').removeClass('wcfm-error').removeClass('wcfm-success').slideUp();
							if( jQuery('#wcfm_support_query').val().length == 0 ) {
								//alert(wcfm_support_manage_messages.no_query);
								jQuery('#wcfm_support_form .wcfm-message').html('<span class="wcicon-status-cancelled"></span>' + wcfm_support_manage_messages.no_query).addClass('wcfm-error').slideDown();
								jQuery('#wcfm_support_submit_button').hide();
								jQuery('#wcfm_support_form_wrapper').unblock();
							} else {
								var data = {
									action            : 'wcfm_ajax_controller',
									controller        : 'wcfm-support-form', 
									wcfm_support_form : jQuery('#wcfm_support_form').serialize()
								}	
								jQuery.post(wcfm_params.ajax_url, data, function(response) {
									if(response) {
										jQueryresponse_json = jQuery.parseJSON(response);
										jQuery('.wcfm-message').html('').removeClass('wcfm-error').removeClass('wcfm-success').slideUp();
										if(jQueryresponse_json.status) {
											jQuery('#wcfm_support_form .wcfm-message').html('<span class="wcicon-status-completed"></span>' + jQueryresponse_json.message).addClass('wcfm-success').slideDown();
											setTimeout(function() {
												jQuery.colorbox.remove();
											}, 2000);
										} else {
											jQuery('#wcfm_support_form .wcfm-message').html('<span class="wcicon-status-cancelled"></span>' + jQueryresponse_json.message).addClass('wcfm-error').slideDown();
											jQuery('#wcfm_support_submit_button').hide();
										}
										if( $('.wcfm_gglcptch_wrapper').length > 0 ) {
											if (typeof grecaptcha != "undefined") {
												grecaptcha.reset();
											}
										}
										jQuery('#wcfm_support_form_wrapper').unblock();
									}
								} );
							}
							return false;
						});
					}
				});
			}
		});
	});
	
	$('.add_support').click(function() {
		if( $support_form_show ) {
			$('.support_form_wrapper_hide').slideUp( "slow" );
			$support_form_show = false;
		} else {
			$('.support_form_wrapper_hide').slideDown( "slow" );
			$support_form_show = true;
		}
	});
	
	// Submit Support
	$('#wcfm_support_submit_button').click(function(event) {
	  event.preventDefault();
	  $wcfm_support_submited = false;
	  wcfm_support_form_submit($(this).parent().parent());
	});
	
});
	
	
function wcfm_support_form_validate($support_form) {
	$is_valid = true;
	jQuery('.wcfm-message').html('').removeClass('wcfm-success').removeClass('wcfm-error').slideUp();
	var support_comment = jQuery.trim($support_form.find('#support_comment').val());
	if(support_comment.length == 0) {
		$is_valid = false;
		$support_form.find('.wcfm-message').html('<span class="wcicon-status-cancelled"></span>' + wcfm_support_manage_messages.no_support).addClass('wcfm-error').slideDown();
	}
	
	if( $support_form.find('#support_author').length > 0 ) {
		var support_author = jQuery.trim($support_form.find('#support_author').val());
		if(support_author.length == 0) {
			if( $is_valid )
				$support_form.find('.wcfm-message').html('<span class="wcicon-status-cancelled"></span>' + wcfm_support_manage_messages.no_name).addClass('wcfm-error').slideDown();
			else
				$support_form.find('.wcfm-message').append('<br /><span class="wcicon-status-cancelled"></span>' + wcfm_support_manage_messages.no_name).addClass('wcfm-error').slideDown();
			
			$is_valid = false;
		}
	}
	
	if( $support_form.find('#support_email').length > 0 ) {
		var support_email = jQuery.trim($support_form.find('#support_email').val());
		if(support_email.length == 0) {
			if( $is_valid )
				$support_form.find('.wcfm-message').html('<span class="wcicon-status-cancelled"></span>' + wcfm_support_manage_messages.no_email).addClass('wcfm-error').slideDown();
			else
				$support_form.find('.wcfm-message').append('<br /><span class="wcicon-status-cancelled"></span>' + wcfm_support_manage_messages.no_email).addClass('wcfm-error').slideDown();
			
			$is_valid = false;
		}
	}
	return $is_valid;
}

function wcfm_support_form_submit($support_form) {
	
	// Validations
	$is_valid = wcfm_support_form_validate($support_form);
	
	if($is_valid) {
		$support_form.block({
			message: null,
			overlayCSS: {
				background: '#fff',
				opacity: 0.6
			}
		});
		
		var data = {
			action                   : 'wcfm_ajax_controller',
			controller               : 'wcfm-support-tab',
			wcfm_support_tab_form    : $support_form.serialize(),
			status                   : 'submit'
		}	
		jQuery.post(wcfm_params.ajax_url, data, function(response) {
			if(response) {
				$response_json = jQuery.parseJSON(response);
				if($response_json.status) {
					$support_form.find('.wcfm-message').html('<span class="wcicon-status-completed"></span>' + $response_json.message).addClass('wcfm-success').slideDown( "slow" );
					setTimeout(function() {
						jQuery('.support_form_wrapper_hide').slideUp( "slow" );
						$support_form_show = false;
						$support_form.find('#support_comment').val('');
					}, 2000 );
					$wcfm_support_submited = true;
				} else {
					$support_form.find('.wcfm-message').html('<span class="wcicon-status-cancelled"></span>' + $response_json.message).addClass('wcfm-error').slideDown();
				}
				$support_form.unblock();
			}
		});
	}
}