jQuery(document).ready(function($) {
	
	// Availability rules type
	function availabilityRules() {
		$('#wc_global_booking_availability').find('.multi_input_block').each(function() {
			if ( $(this).find('.avail_range_type').parent().is( "span" ) ) { $(this).find('.avail_range_type').unwrap( "span" ); }
			$(this).find('.avail_range_type').change(function() {
				$avail_range_type = $(this).val();
				$(this).parent().find('.avail_rule_field').addClass('wcfm_ele_hide');
				if( $avail_range_type == 'custom' || $avail_range_type == 'months' || $avail_range_type == 'weeks' || $avail_range_type == 'days' ) {
					$(this).parent().find('.avail_rule_' + $avail_range_type).removeClass('wcfm_ele_hide');
				} else if( $avail_range_type == 'time:range' ) {
					$(this).parent().find('.avail_rule_custom').removeClass('wcfm_ele_hide');
					$(this).parent().find('.avail_rule_time').removeClass('wcfm_ele_hide');
				} else {
					$(this).parent().find('.avail_rule_time').removeClass('wcfm_ele_hide');
				}
			}).change();
		});
	}
	availabilityRules();
	$('#wc_global_booking_availability').find('.add_multi_input_block').click(function() {
	  availabilityRules();
	  $('#wc_global_booking_availability').find('.multi_input_block:last').find('.avail_rule_priority').val('10');
	});
	
	// Submit Resource
	$('#wcfm_wcbookings_settings_submit_button').click(function(event) {
	  event.preventDefault();
	  
	  // Validations
	  $is_valid = true;
	  
	  if($is_valid) {
			$('#wcfm-content').block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
			var data = {
				action                   : 'wcfm_ajax_controller',
				controller               : 'wcfm-bookings-settings',
				wcfm_wcbookings_settings_form : $('#wcfm_wcbookings_settings_form').serialize(),
				status                   : 'submit'
			}	
			$.post(wcfm_params.ajax_url, data, function(response) {
				if(response) {
					$response_json = $.parseJSON(response);
					if($response_json.status) {
						audio.play();
						$('#wcfm_wcbookings_settings_form .wcfm-message').html('<span class="wcicon-status-completed"></span>' + $response_json.message).addClass('wcfm-success').slideDown();
					} else {
						audio.play();
						$('.wcfm-message').html('').removeClass('wcfm-success').slideUp();
						$('#wcfm_wcbookings_settings_form .wcfm-message').html('<span class="wcicon-status-cancelled"></span>' + $response_json.message).addClass('wcfm-error').slideDown();
					}
					if($response_json.id) $('#resource_id').val($response_json.id);
					$('#wcfm-content').unblock();
				}
			});
		}
	});
	
});