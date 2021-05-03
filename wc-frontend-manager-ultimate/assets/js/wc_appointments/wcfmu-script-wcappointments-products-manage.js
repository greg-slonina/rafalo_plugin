jQuery(document).ready(function($) {
		
	$('#_wc_appointment_restricted_days').select2();
	
	$('#_wc_appointment_duration_unit').change(function() {
	  $_wc_appointment_duration_unit = $(this).val();
	  $('#_wc_appointment_interval, ._wc_appointment_interval, #_wc_appointment_interval_unit, #_wc_appointment_customer_timezones, ._wc_appointment_customer_timezones').removeClass('wcfm_ele_hide');
	  $('#_wc_appointment_padding_duration, ._wc_appointment_padding_duration, #_wc_appointment_padding_duration_unit').removeClass('wcfm_ele_hide');
	  if( $_wc_appointment_duration_unit == 'day' ) {
	  	$('#_wc_appointment_interval, ._wc_appointment_interval, #_wc_appointment_interval_unit, #_wc_appointment_customer_timezones, ._wc_appointment_customer_timezones').addClass('wcfm_ele_hide');
	  } else if( $_wc_appointment_duration_unit == 'month' ) {
	  	$('#_wc_appointment_interval, ._wc_appointment_interval, #_wc_appointment_interval_unit, #_wc_appointment_customer_timezones, ._wc_appointment_customer_timezones').addClass('wcfm_ele_hide');
	  	$('#_wc_appointment_padding_duration, ._wc_appointment_padding_duration, #_wc_appointment_padding_duration_unit').addClass('wcfm_ele_hide');
	  }
	}).change();
		
	// Availability rules type
	function availabilityRules() {
		$('#_wc_appointment_availability_rules').find('.multi_input_block').each(function() {
			$(this).find('.avail_range_type').change(function() {
				$avail_range_type = $(this).val();
				$(this).parent().find('.avail_rule_field').addClass('wcfm_ele_hide');
				if( $avail_range_type == 'custom' || $avail_range_type == 'months' || $avail_range_type == 'weeks' || $avail_range_type == 'days' ) {
					$(this).parent().find('.avail_rule_' + $avail_range_type).removeClass('wcfm_ele_hide');
				} else if( ( $avail_range_type == 'time:range' ) || ( $avail_range_type == 'custom:daterange' ) ) {
					$(this).parent().find('.avail_rule_custom').removeClass('wcfm_ele_hide');
					$(this).parent().find('.avail_rule_time').removeClass('wcfm_ele_hide');
				} else {
					$(this).parent().find('.avail_rule_time').removeClass('wcfm_ele_hide');
				}
			}).change();
		});
	}
	availabilityRules();
	$('#_wc_appointment_availability_rules').find('.add_multi_input_block').click(function() {
	  availabilityRules();
	});
	
	// Track Deleting Rules
	$('#_wc_appointment_availability_rules').children('.multi_input_block').children('.remove_multi_input_block').click(function() {
	  removed_variations.push($(this).parent().find('.avail_id').val());
	});
	
	// Cost rules type
	function costRules() {
		$('#_wc_appointment_cost_rules').find('.multi_input_block').each(function() {
			$(this).find('.cost_range_type').change(function() {
				$cost_range_type = $(this).val();
				$(this).parent().find('.cost_rule_field').addClass('wcfm_ele_hide');
				if( $cost_range_type == 'custom' || $cost_range_type == 'months' || $cost_range_type == 'weeks' || $cost_range_type == 'days' ) {
					$(this).parent().find('.cost_rule_' + $cost_range_type).removeClass('wcfm_ele_hide');
				} else if( $cost_range_type == 'quant' || $cost_range_type == 'slots' ) {
					$(this).parent().find('.cost_rule_count').removeClass('wcfm_ele_hide');
				} else if( $cost_range_type == 'time:range' ) {
					$(this).parent().find('.cost_rule_custom').removeClass('wcfm_ele_hide');
					$(this).parent().find('.cost_rule_time').removeClass('wcfm_ele_hide');
				} else {
					$(this).parent().find('.cost_rule_time').removeClass('wcfm_ele_hide');
				}
			}).change();
		});
	}
	costRules();
	$('#_wc_appointment_cost_rules').find('.add_multi_input_block').click(function() {
	  costRules();
	});
	
	// Staff Type Selection
	function trackUsedStaffs() {
		$('#_wc_appointment_staffs').find('.multi_input_block').each(function() {
			$staff_id = $(this).find( 'input[data-name="staff_id"]' ).val();
			$( 'select#_wc_appointment_all_staffs' ).find( 'option[value="' + $staff_id + '"]' ).attr( 'disabled','disabled' );
		});
	}
	trackUsedStaffs();
	
	// Staff Type selection
	$( 'select#_wc_appointment_all_staffs' ).change(function() {
		if( $(this).val() != -1 ) {
			$('#_wc_appointment_staffs').find('.multi_input_block:last').find('.add_multi_input_block').click();
			$('#_wc_appointment_staffs').find('.multi_input_block:last').find('input[data-name="staff_id"]').val($(this).val());
			$('#_wc_appointment_staffs').find('.multi_input_block:last').find('input[data-name="staff_title"]').val($(this).find("option:selected").html());
			$('#_wc_appointment_staffs').find('.multi_input_block:last').find('.remove_multi_input_block').click(function() {
				$staff_id = $(this).parent().find( 'input[data-name="staff_id"]' ).val();
				$( 'select#_wc_appointment_all_staffs' ).find( 'option[value="' + $staff_id + '"]' ).removeAttr( 'disabled' );
				trackUsedStaffs();
			});
			trackUsedStaffs();
		}
	});
	
	// Track Deleting Staffs
	$('#_wc_appointment_staffs').find('.remove_multi_input_block').click(function() {
		$staff_id = $(this).parent().find( 'input[data-name="staff_id"]' ).val();
		$( 'select#_wc_appointment_all_staffs' ).find( 'option[value="' + $staff_id + '"]' ).removeAttr( 'disabled' );
	  trackUsedStaffs();
	});
});