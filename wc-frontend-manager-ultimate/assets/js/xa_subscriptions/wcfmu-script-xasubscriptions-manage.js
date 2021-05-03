jQuery(document).ready(function($) {
	// Subscription Status Update
	$('#wcfm_modify_subscription_status').click(function(event) {
		event.preventDefault();
		modifyWCFMSubscriptionStatus();
		return false;
	});
		
	function modifyWCFMSubscriptionStatus() {
		$('#subscriptions_details_general_expander').block({
			message: null,
			overlayCSS: {
				background: '#fff',
				opacity: 0.6
			}
		});
		var data = {
			action              : 'wcfm_modify_subscription_status',
			subscription_status : $('#wcfm_subscription_status').val(),
			subscription_id     : $('#wcfm_modify_subscription_status').data('subscriptionid')
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
					$('#wcfm_subscription_status_update_wrapper .wcfm-message').html('<span class="wcicon-status-completed"></span>' + $response_json.message).addClass('wcfm-success').slideDown( "slow" );
				}
				$('#subscriptions_details_general_expander').unblock();
			}
		});
	}
	
	// Subscription BillingSchedule Update
	$('#wcfm_subscription_billing_button').click(function(event) {
	  event.preventDefault();
	  
	  // Validations
		$('.wcfm-message').html('').removeClass('wcfm-error').removeClass('wcfm-success').slideUp();
		$wcfm_is_valid_form = true;
		$( document.body ).trigger( 'wcfm_form_validate', $('#wcfm_wcs_billing_schedule_update_form') );
		$is_valid = $wcfm_is_valid_form;
	  
	  if($is_valid) {
			$('#subscriptions_details_billing_schedule_expander').block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
			var data = {
				action                                : 'wcfm_ajax_controller',
				controller                            : 'wcfm-subscriptions-manage',
				wcfm_wcs_billing_schedule_update_form : $('#wcfm_wcs_billing_schedule_update_form').serialize(),
			}	
			$.post(wcfm_params.ajax_url, data, function(response) {
				if(response) {
					$response_json = $.parseJSON(response);
					$('.wcfm-message').html('').removeClass('wcfm-error').removeClass('wcfm-success').slideUp();
					if($response_json.status) {
						wcfm_notification_sound.play();
						$('#subscriptions_details_billing_schedule_expander .wcfm-message').html('<span class="wcicon-status-completed"></span>' + $response_json.message).addClass('wcfm-success').slideDown();
					} else {
						wcfm_notification_sound.play();
						$('#subscriptions_details_billing_schedule_expander .wcfm-message').html('<span class="wcicon-status-cancelled"></span>' + $response_json.message).addClass('wcfm-error').slideDown();
					}
					$('#subscriptions_details_billing_schedule_expander').unblock();
				}
			});	
		}
	});
});