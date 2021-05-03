jQuery(document).ready(function($) {
	$('#_wc_booking_restricted_days').select2();
	if( $('#_wc_accommodation_booking_restricted_days').length > 0 ) { $('#_wc_accommodation_booking_restricted_days').select2(); }
	// Availability rules type
	function availabilityRules() {
		$('#_wc_booking_availability_rules, #_wc_accommodation_booking_availability_rules').find('.multi_input_block').each(function() {
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
	$('#_wc_booking_availability_rules').find('.add_multi_input_block').click(function() {
	  availabilityRules();
	  $('#_wc_booking_availability_rules').find('.multi_input_block:last').find('.avail_rule_priority').val('10');
	});
	$('#_wc_accommodation_booking_availability_rules').find('.add_multi_input_block').click(function() {
	  availabilityRules();
	  $('#_wc_accommodation_booking_availability_rules').find('.multi_input_block:last').find('.avail_rule_priority').val('10');
	});
	
	// Cost rules type
	function costRules() {
		$('#_wc_booking_cost_rules, #_wc_accommodation_booking_cost_rules').find('.multi_input_block').each(function() {
			if ( $(this).find('.cost_range_type').parent().is( "span" ) ) { $(this).find('.cost_range_type').unwrap( "span" ); }
			$(this).find('.cost_range_type').change(function() {
				$cost_range_type = $(this).val();
				$(this).parent().find('.cost_rule_field').addClass('wcfm_ele_hide');
				if( $cost_range_type == 'custom' || $cost_range_type == 'months' || $cost_range_type == 'weeks' || $cost_range_type == 'days' ) {
					$(this).parent().find('.cost_rule_' + $cost_range_type).removeClass('wcfm_ele_hide');
				} else if( $cost_range_type == 'persons' || $cost_range_type == 'blocks' ) {
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
	$('#_wc_booking_cost_rules').find('.add_multi_input_block').click(function() {
	  costRules();
	});
	$('#_wc_accommodation_booking_cost_rules').find('.add_multi_input_block').click(function() {
	  costRules();
	});
	
  // Persons
	$('#_wc_booking_has_persons').change(function() {
		if($(this).is(':checked')) {
			$('.persons').removeClass('wcfm_ele_hide wcfm_block_hide wcfm_head_hide');
			collapsHeight += (46 + 21);
			resetCollapsHeight($('.collapse-open').next('.wcfm-container').find('.wcfm_ele:not(.wcfm_title):first'));
		} else {
			$('.persons').addClass('wcfm_ele_hide wcfm_block_hide wcfm_head_hide');
		}
		if( ( $('#product_type').val() != 'booking' ) && ( $('#product_type').val() != 'accommodation-booking' ) ) $('.persons').addClass('wcfm_ele_hide wcfm_block_hide wcfm_head_hide');
	}).change();
	
	// Person Types
	$('#_wc_booking_has_person_types').change(function() {
		if($(this).is(':checked')) {
			$('.person_types').removeClass('wcfm_ele_hide wcfm_block_hide wcfm_head_hide');
		} else {
			$('.person_types').addClass('wcfm_ele_hide wcfm_block_hide wcfm_head_hide');
		}
		resetCollapsHeight($('#_wc_booking_has_person_types'));
	}).change();
	
	// Resources
	$('#_wc_booking_has_resources').change(function() {
		if($(this).is(':checked')) {
			$('.resources').removeClass('wcfm_ele_hide wcfm_block_hide wcfm_head_hide');
			collapsHeight += (46 + 21);
			resetCollapsHeight($('.collapse-open').next('.wcfm-container').find('.wcfm_ele:not(.wcfm_title):first'));
		} else {
			$('.resources').addClass('wcfm_ele_hide wcfm_block_hide wcfm_head_hide');
		}
		if( ( $('#product_type').val() != 'booking' ) && ( $('#product_type').val() != 'accommodation-booking' ) ) $('.resources').addClass('wcfm_ele_hide wcfm_block_hide wcfm_head_hide');
	}).change();
	
	// Product Type Change
	$( document.body ).on( 'wcfm_product_type_changed', function() {
		if( ( $('#product_type').val() == 'booking' ) || ( $('#product_type').val() == 'accommodation-booking' ) ) {
			$('#_wc_booking_has_persons').change();
			$('#_wc_booking_has_resources').change();
		}
	});
	if( ( $('#product_type').val() == 'booking' ) || ( $('#product_type').val() == 'accommodation-booking' ) ) {
		$('#_wc_booking_has_persons').change();
		$('#_wc_booking_has_resources').change();
	}
	
	// Track Deleting Person Types
	$('#_wc_booking_person_types').find('.remove_multi_input_block').click(function() {
	  removed_person_types.push($(this).parent().find('.person_id').val());
	});
  
	// Resource Type Selection
	function trackUsedResources() {
		$('#_wc_booking_resources').find('.multi_input_block').each(function() {
			$resource_id = $(this).find( 'input[data-name="resource_id"]' ).val();
			$( 'select#_wc_booking_all_resources' ).find( 'option[value="' + $resource_id + '"]' ).attr( 'disabled','disabled' );
		});
	}
	trackUsedResources();
	
	// Resource Type selection
	$( 'select#_wc_booking_all_resources' ).change(function() {
		if( $(this).val() != -1 ) {
			$('#_wc_booking_resources').find('.multi_input_block:last').find('.add_multi_input_block').click();
			$('#_wc_booking_resources').find('.multi_input_block:last').find('input[data-name="resource_id"]').val($(this).val());
			$('#_wc_booking_resources').find('.multi_input_block:last').find('input[data-name="resource_title"]').val($(this).find("option:selected").html());
			$('#_wc_booking_resources').find('.multi_input_block:last').find('.remove_multi_input_block').click(function() {
				$resource_id = $(this).parent().find( 'input[data-name="resource_id"]' ).val();
				$( 'select#_wc_booking_all_resources' ).find( 'option[value="' + $resource_id + '"]' ).removeAttr( 'disabled' );
				trackUsedResources();
			});
			trackUsedResources();
		}
	});
	
	// Track Deleting Resources
	$('#_wc_booking_resources').find('.remove_multi_input_block').click(function() {
		$resource_id = $(this).parent().find( 'input[data-name="resource_id"]' ).val();
		$( 'select#_wc_booking_all_resources' ).find( 'option[value="' + $resource_id + '"]' ).removeAttr( 'disabled' );
	  trackUsedResources();
	});
	
});