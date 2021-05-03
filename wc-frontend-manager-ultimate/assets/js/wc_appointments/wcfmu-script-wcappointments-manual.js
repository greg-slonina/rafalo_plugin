jQuery(document).ready( function($) {
	$("#appointable_product_id").select2({ });
	
	$("#customer_id").select2({ });
	
	$('.wcfm_order_add_new_customer_box .wcfm_order_add_new_customer').click(function(event) {
		event.preventDefault();
		jQueryquick_edit = $(this);
		
		// Ajax Call for Fetching Quick Edit HTML
		$('#wwcfm_appointments_listing_expander').block({
			message: null,
			overlayCSS: {
				background: '#fff',
				opacity: 0.6
			}
		});
		var data = {
			action  : 'wcfm_order_add_customer_html'
		}	
		
		jQuery.ajax({
			type    :		'POST',
			url     : wcfm_params.ajax_url,
			data    : data,
			success :	function(response) {
				// Intialize colorbox
				jQuery.colorbox( { html: response, height: 525, width: $popup_width,
					onComplete:function() {
				
						// Intialize Quick Update Action
						$('#wcfm_order_add_customer_button').click(function() {
							$wcfm_is_valid_form = true;
							$('#wcfm_order_add_customer_form').block({
								message: null,
								overlayCSS: {
									background: '#fff',
									opacity: 0.6
								}
							});
							$('.wcfm-message').html('').removeClass('wcfm-error').removeClass('wcfm-success').slideUp();
							
							$( document.body ).trigger( 'wcfm_form_validate', $('#wcfm_order_add_customer_form') );
							if( !$wcfm_is_valid_form ) {
								wcfm_notification_sound.play();
								$('#wcfm_order_add_customer_form').unblock();
							} else {
								$('.wcfm_order_add_customer_button').hide();
								var data = {
									action : 'wcfm_ajax_controller',
									controller : 'wcfm-orders-manage-add-customer', 
									wcfm_order_add_customer_form : $('#wcfm_order_add_customer_form').serialize()
								}	
								jQuery.post(wcfm_params.ajax_url, data, function(response) {
									if(response) {
										jQueryresponse_json = jQuery.parseJSON(response);
										$('.wcfm-message').html('').removeClass('wcfm-error').removeClass('wcfm-success').slideUp();
										if(jQueryresponse_json.status) {
											$('#wcfm_order_add_customer_form .wcfm-message').html('<span class="wcicon-status-completed"></span>' + jQueryresponse_json.message).addClass('wcfm-success').slideDown();
											if( $('#wwcfm_appointments_listing_expander').find('#customer_id').length > 0 ) {
												$('#wwcfm_appointments_listing_expander').find('#customer_id').append('<option value="' + jQueryresponse_json.customer_id + '" selected>#' + jQueryresponse_json.customer_id + ' ' + jQueryresponse_json.username  + '</option>');
												$('#wwcfm_appointments_listing_expander').find('#customer_id').change();
												$('#wwcfm_appointments_listing_expander').find('#customer_id').trigger('chosen:updated'); 
											}
											setTimeout(function() {
												jQuery.colorbox.remove();
											}, 2000);
										} else {
											$('#wcfm_order_add_customer_form .wcfm-message').html('<span class="wcicon-status-cancelled"></span>' + jQueryresponse_json.message).addClass('wcfm-error').slideDown();
										}
										wcfm_notification_sound.play();
										$('.wcfm_order_add_customer_button').show();
										$('#wcfm_order_add_customer_form').unblock();
									}
								} );
							}
						});
					}
				});
				$('#wwcfm_appointments_listing_expander').unblock();
			}
		});
		
		return false;
  });
	
	$('input[name=add_appointment_2]').click(function() {
	  $('input[name=add-to-cart]').attr( 'name', 'wcfm-add-to-cart' );
	});
	
	$('.wc-pao-addon-checkbox').each(function() {
		$(this).addClass('wcfm-checkbox');
	});
	$('.wc-pao-addon-select').each(function() {
		$(this).addClass('wcfm-select');
	});
});