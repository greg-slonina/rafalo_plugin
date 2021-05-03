jQuery(document).ready(function($) {
  $('#booking_enable_only_day').change(function() {
  	$('.booking_type_sub_block').addClass('wcfm_ele_hide');
  	$('.booking_type_sub_block_'+$(this).val()).removeClass('wcfm_ele_hide');
  }).change();
  
  $('#booking_enable_date_time').change(function() {
  	$('.booking_type_sub_block').addClass('wcfm_ele_hide');
  	$('.booking_type_sub_block_'+$(this).val()).removeClass('wcfm_ele_hide');
  }).change();
  
  $('#booking_enable_type').change(function() {
  	$('.booking_type_block').addClass('wcfm_ele_hide');
  	$('.booking_type_block_'+$(this).val()).removeClass('wcfm_ele_hide');
  	
  	$('.booking_type_sub_block').addClass('wcfm_ele_hide');
  	$('.booking_type_sub_block').addClass('wcfm_ele_hide');
  	if( $(this).val() == 'booking_enable_only_day' ) {
  		$('#booking_enable_only_day').change();
  	} else {
  		$('#booking_enable_date_time').change();
  	}
  }).change();
  
  $('#bkap_enable_block_pricing_type').change(function() {
  	$('.bkap_enable_block_pricing_type_block').addClass('wcfm_ele_hide');
  	$('.bkap_enable_block_pricing_type_block_'+$(this).val()).removeClass('wcfm_ele_hide');
  }).change();
  
  append_weekdays();
  
  // Enable Specific dates
  jQuery('#specific_date_checkbox').click( function() {
  	jQuery( ".specific_date" ).addClass('wcfm_ele_hide');
    if( $(this).is(':checked') ) {
    	jQuery( ".specific_date" ).removeClass('wcfm_ele_hide');
    	resetCollapsHeight($('.specific_date'));
    }
  });
  if( jQuery('#specific_date_checkbox').prop("checked") == false ) {
    jQuery( ".specific_date" ).addClass('wcfm_ele_hide');
  }
  
  // Resources
	$('#_bkap_resource').change(function() {
		if($(this).is(':checked')) {
			$('.resources').removeClass('wcfm_ele_hide wcfm_block_hide wcfm_head_hide');
			collapsHeight += (46 + 21);
			resetCollapsHeight($('.collapse-open').next('.wcfm-container').find('.wcfm_ele:not(.wcfm_title):first'));
		} else {
			$('.resources').addClass('wcfm_ele_hide wcfm_block_hide wcfm_head_hide');
		}
	}).change();
  
  $( document.body ).on( 'wcfm_products_manage_form_validate', function( event, validating_form ) {
  	booking_options = bkap_get_general_tab_data();
		settings_data = bkap_get_availability_data();
		gcal_settings = bkap_get_gcal_data();
		fixed_block_data  = bkap_fixed_block_data();
		price_range_data  = bkap_price_range_data();
		
		var fixed_blocks_enable = '';
		var price_ranges_enable = '';
		var block_price         = '';
		var block_price     = jQuery( '#bkap_enable_block_pricing_type').val();
		
		if( block_price != undefined ){
			if ( block_price.length > 0 && block_price == "booking_block_price_enable" ) {
				price_ranges_enable = block_price;
			}
			
			if ( block_price.length > 0 && block_price == "booking_fixed_block_enable" ) {
				fixed_blocks_enable = block_price;
			}
		}
	
		// setup the data
		var data = {
			booking_options: JSON.stringify( booking_options ),
			settings_data: JSON.stringify( settings_data ),
			gcal_data: JSON.stringify( gcal_settings ),
			ranges_enabled: price_ranges_enable,
			blocks_enabled: fixed_blocks_enable,
			fixed_block_data: JSON.stringify( fixed_block_data ),
			price_range_data: price_range_data,
			
			product_id: bkap_settings_params.post_id,
			action: 'bkap_save_settings'
		};
		
		$('#booking_booking_options').val( JSON.stringify( booking_options ) );
		$('#booking_settings_data').val( JSON.stringify( settings_data ) );
		$('#booking_gcal_data').val( JSON.stringify( gcal_settings ) );
		$('#booking_ranges_enabled').val( price_ranges_enable );
		$('#booking_blocks_enabled').val( fixed_blocks_enable );
		$('#booking_fixed_block_data').val( JSON.stringify( fixed_block_data ) );
		$('#booking_price_range_data').val( JSON.stringify( price_range_data ) );
	});
  
});

/**
 * This function appends/removes the weekdays selected
 * in the Weekdays UI from the weekday list in the Date
 * & Time table
 *
 * @function append_weekdays
 *
 * @since 4.0
 */
function append_weekdays() {
  jQuery( "#set_weekdays" ).on( 'click', '[id^="booking_weekday_"]', function() {
      // Update the date/day selector for time slots 
    var id = this.id;
    var name = this.name;
		if ( jQuery( '#' + id ).is( ":checked" ) ) { // add the weekday
			
			// add the weekday in the default row
			jQuery( '#bkap_date_timeslot_table tr[id="bkap_default_date_time_row"]' ).each(function (i, row) {
				jQuery( '#bkap_dateday_selector' ).append( '<option value="' + id + '">' + name + '</option>' );
			});
			
			// add the weekday from the existing rows
			jQuery( '#bkap_date_timeslot_table tr[id^="bkap_date_time_row_"]' ).each(function (i, row) {
				var element_id = jQuery( this ).find( 'select[id^="bkap_dateday_selector_"]' ).attr( 'id' );
				element_id = '#' + element_id;
			
				// first check if the row is visible or no, if yes, then simply add the option value
				if ( jQuery( this ).is( ':visible' ) ) {
					jQuery( element_id ).append( '<option value="' + id + '">' + name + '</option>' );
				} else {
				
					// if not then it possible that the row was hidden as the weekday was disabled
					var weekday_selected = jQuery( element_id ).val();
					if ( weekday_selected == id ) {
					
						// if yes, then make the row visible again
						jQuery( this ).toggle();
					} else {
						jQuery( element_id ).append( '<option value="' + id + '">' + name + '</option>' );
					}
				
				}
			});
		} else { // remove the weekday
			
			// remove the weekday in the default row
			jQuery( '#bkap_date_timeslot_table tr[id="bkap_default_date_time_row"]' ).each(function (i, row) {
				var selector = '#bkap_dateday_selector option[value="' + id + '"]';
				jQuery( selector ).remove();
			});
		
			// remove the weekday from all the existing rows
			jQuery( '#bkap_date_timeslot_table tr[id^="bkap_date_time_row_"]' ).each(function (i, row) {
				var element_id = jQuery( this ).find( 'select[id^="bkap_dateday_selector_"]' ).attr( 'id' );
				element_id = '#' + element_id;
				
				// first check if the same weekday has been selected here
				var weekday_selected = jQuery( element_id ).val();
				
				// if yes then we need to hide that row
				if ( weekday_selected == id ) {
					jQuery( this ).hide();
				} else { // else simply remove the option from the drop down
					var selector = element_id + ' option[value="' + id + '"]';
						jQuery( selector ).remove();
				}
			});
		}
	});
	
	jQuery( '[id^="booking_weekday_"]' ).each( function() {
      // Update the date/day selector for time slots 
    var id = this.id;
    var name = this.name;
		if ( jQuery( '#' + id ).is( ":checked" ) ) { // add the weekday
			
			// add the weekday in the default row
			jQuery( '#bkap_date_timeslot_table tr[id="bkap_default_date_time_row"]' ).each(function (i, row) {
				jQuery( '#bkap_dateday_selector' ).append( '<option value="' + id + '">' + name + '</option>' );
			});
			
			// add the weekday from the existing rows
			jQuery( '#bkap_date_timeslot_table tr[id^="bkap_date_time_row_"]' ).each(function (i, row) {
				var element_id = jQuery( this ).find( 'select[id^="bkap_dateday_selector_"]' ).attr( 'id' );
				element_id = '#' + element_id;
			
				// first check if the row is visible or no, if yes, then simply add the option value
				if ( jQuery( this ).is( ':visible' ) ) {
					jQuery( element_id ).append( '<option value="' + id + '">' + name + '</option>' );
				} else {
				
					// if not then it possible that the row was hidden as the weekday was disabled
					var weekday_selected = jQuery( element_id ).val();
					if ( weekday_selected == id ) {
					
						// if yes, then make the row visible again
						//jQuery( this ).toggle();
					} else {
						jQuery( element_id ).append( '<option value="' + id + '">' + name + '</option>' );
					}
				
				}
			});
		} 
	});
}

/**
 * This function appends/removes the specific dates selected
 * in the Specific Dates Table from the Day/Date column in the 
 * Date & Time table
 *
 * @function append_specific_dates
 * 
 * @param {string} id - ID of the div to append specific dates
 * @param {string} value - specific dates values
 *
 * @since 4.0
 */
function append_specific_dates( id, value ) {
  
  if ( id != undefined && value != undefined ) {
    var specific_id_split   = id.split( '_' );
    var row_number      = specific_id_split[ 3 ];
    
    var range_id      = '#range_dropdown_' + row_number;
    var range_set       = jQuery( range_id ).val();
        
    if ( 'specific_dates' == range_set ) {
      
      var specific_dates  = value;
      
      if( specific_dates != "" ){ // If specific dates are blank in the textarea then don't check for adding dates
        
      var specific_list   = specific_dates.split( ',' );
      
      // loop through all the dates
      specific_list.forEach( function( date ) {
        
        // add the date in the default row
          jQuery( '#bkap_date_timeslot_table tr[id="bkap_default_date_time_row"]' ).each(function (i, row) {
            jQuery( '#bkap_dateday_selector' ).append( '<option value="' + date + '">' + date + '</option>' );
          });
          
          // add the date in the existing rows
        jQuery( '#bkap_date_timeslot_table tr[id^="bkap_date_time_row_"]' ).each(function (i, row) {
            
          var element_id  = jQuery( this ).find( 'select[id^="bkap_dateday_selector_"]' ).attr( 'id' );
            element_id    = '#' + element_id;
            var selector  = element_id + ' option[value=\'' + date + '\']';
            
            if ( jQuery( selector ).length == 0 ) { // check if the date is already present in the dropdown
              jQuery( element_id ).append( '<option value="' + date + '">' + date + '</option>' );
            } 
          });
      });
      }
      
      //To remove the date from the select2 dropdown if the date is unselected.
      
      var all_specific_datess = "";
      //run through each row and get all the dates in specific dates.
        jQuery( '.specific tr[class^="added_specific_date_range_row_"]' ).each(function (i, row) {
          var all_specific_dates    = jQuery( this ).find( 'select[id^="range_dropdown"]' ).val();
          var specific_dates_each_row = jQuery( this ).find( 'textarea[id*="specific_dates_multidatepicker"]' ).val();
          
          if( specific_dates_each_row != "" ){
            all_specific_datess += ","+specific_dates_each_row; 
          }
        });
        
        if( all_specific_datess != "" ){ // if there are no specific date then don't go for check for removing dates from select2 dropdown.
        
        var all_specific_datess_array = all_specific_datess.split( ',' );
        
        all_specific_datess_array.slice(1);
        
        var all_dates_in_select2_option = [];
        jQuery( "#bkap_dateday_selector > option" ).each( function(){
          var value = this.innerHTML;
          if( !isNaN( value.charAt(0) ) ){
            all_dates_in_select2_option.push( value );
          }
        });
        
        // Remove date from the option if not present.
        all_dates_in_select2_option.forEach( function( date1 ) {
           
          if( jQuery.inArray( date1, all_specific_datess_array ) == -1 ){
            // Remove the date in the default row.
            jQuery( '#bkap_date_timeslot_table tr[id="bkap_default_date_time_row"]' ).each(function (i, row) {
                jQuery( '#bkap_dateday_selector' ).find('option[value="'+date1+'"]').remove();
              });
            
            // Remove date from existing row
            jQuery( '#bkap_date_timeslot_table tr[id^="bkap_date_time_row"]' ).each( function (i, row) {
                var element_id = jQuery( this ).find( 'select[id^="bkap_dateday_selector_"]' ).attr( 'id' );
                element_id = '#' + element_id;
                jQuery(element_id).find('option[value="'+date1+'"]').remove();
                
              });
          }
        });
      
        }
      
    }
  } 
}

/** This function displays the Weekdays setup UI
 * It is called for multiple days
 *
 * @function bkap_add_weekdays
 * 
 * @since 4.0
 */
function bkap_add_weekdays() {
  
  jQuery( "#set_weekdays" ).css( "display", "block" );
  jQuery( ".weekdays_flex_child_3" ).css( "display", "none" );
  
}

/** This function will allow only numeric value
 *  in the time input of the date and time table.
 *
 * @function bkap_isNumberKey
 * 
 * @param {object} evt - Event Object
 * @returns {bool}
 * @since 4.0
 */
function bkap_isNumberKey(evt){
    
  var charCode = (evt.which) ? evt.which : evt.keyCode;
    
    if( ( evt.target.value.length == 0 || evt.target.value.length == 1 || evt.target.value.length == 3 || evt.target.value.length == 4 ) && charCode == 58)
    return false;
    
    if( evt.target.value.length == 2  && charCode != 58)
        return false;
    
    if ( charCode > 31 && ( charCode < 48 || charCode > 57 ) && charCode != 58 )
        return false;
    return true;
}

/**
 * This function displays the weekdays setup UI
 * for single days and date & time
 * 
 * @function bkap_add_weekdays_avail
 * 
 * @since 4.0
 */
function bkap_add_weekdays_avail() {
  
  jQuery( "#set_weekdays" ).css( "display", "block" );
  jQuery( ".weekdays_flex_child_3" ).css( "display", "block" );
}

/**
 * Gets the data for the general tab from the fields and 
 * creates & returns an array.
 *
 * @function bkap_get_general_tab_data
 * 
 * @returns {object} JSON Object containing Booking Options
 * @since 4.0
 */
function bkap_get_general_tab_data() {
  
  // General tab
  var booking_options = {};
  
  if ( jQuery( "#booking_enable_date" ).attr( "checked" ) ) {
    booking_options[ 'booking_enable_date' ] = jQuery( "#booking_enable_date" ).val();
  } else {
    booking_options[ 'booking_enable_date' ] = '';
  }

  var booking_type = 'only_day';  
  
  var booking_type_radio = jQuery( '#booking_enable_type').val();
  
  if ( booking_type_radio != undefined ) {
    
    if ( 'booking_enable_only_day' == booking_type_radio.trim() ) {      
      booking_type = bkap_get_only_day_type();
    } else if( 'booking_enable_date_and_time' == booking_type_radio.trim() ) {
      booking_type = bkap_get_date_and_time_type();      
    }
  } 

  booking_options[ 'booking_type' ] = booking_type;
  
  if ( typeof booking_options_additional == 'function' ) { 
    booking_options = booking_options_additional(booking_options); 
  }  
  
  if ( jQuery( "#enable_inline_calendar" ).attr( "checked" ) ) {
    booking_options[ 'enable_inline' ] = jQuery( "#enable_inline_calendar" ).val();
  } else {
    booking_options[ 'enable_inline' ] = '';
  }
  
  if ( jQuery( "#booking_purchase_without_date" ).attr( "checked" ) ) {
    booking_options[ 'purchase_wo_date' ] = jQuery( "#booking_purchase_without_date" ).val();
  } else {
    booking_options[ 'purchase_wo_date' ] = '';
  }
  
  if ( jQuery( "#bkap_requires_confirmation" ).attr( "checked" ) ) {
    booking_options[ 'requires_confirmation' ] = jQuery( "#bkap_requires_confirmation" ).val();
  } else {
    booking_options[ 'requires_confirmation' ] = '';
  }

  /* POS Addon - Block single week  */
  if( jQuery( "#wkpbk_block_single_week" ).attr( "checked" ) ){
    booking_options[ 'wkpbk_block_single_week' ] = jQuery( "#wkpbk_block_single_week" ).val();  
    booking_options[ 'special_booking_start_weekday' ] = jQuery( "#special_booking_start_weekday" ).val();
    booking_options[ 'special_booking_end_weekday' ]   = jQuery( "#special_booking_end_weekday" ).val();

  }else {
    booking_options[ 'wkpbk_block_single_week' ] = '';
    booking_options[ 'special_booking_start_weekday' ] = '';
    booking_options[ 'special_booking_end_weekday' ]   = '';
    
  }
  
  return booking_options;
  
}

function bkap_get_only_day_type() {
  
  var only_day_type = jQuery( 'input:radio[name=booking_enable_only_day]:checked').val();

  var booking_type = 'only_day';
      
  if ( only_day_type != undefined ) {
    
    if( 'booking_enable_single_day' == only_day_type.trim() ) {
      booking_type = 'only_day';
    } else if ( 'booking_enable_multiple_days' == only_day_type.trim() ) {
      booking_type = 'multiple_days';
    }

  }

  return booking_type;
}

function bkap_get_date_and_time_type() {

  var date_time_type = jQuery( '#booking_enable_date_time').val();
      
  booking_type = 'date_time';
  
  if ( date_time_type != undefined ) {
    
    if( 'booking_enable_fixed_time' == date_time_type.trim() ) {
      booking_type = 'date_time';
    } else if ( 'booking_enable_duration_time' == date_time_type.trim() ) {
      booking_type = 'duration_time';
    }

  }

  return booking_type;
}

/**
 * This function will get all the settings on
 * the Availability tab and return them as an array
 *
 * @function bkap_get_availability_data
 * 
 * @returns {object} JSON Object containing Booking Availability Data
 * @since 4.0
 */
function bkap_get_availability_data() {
  
  // settings tab
  var settings_data = {};
  
  settings_data[ 'abp' ]          = jQuery( "#booking_minimum_number_days" ).val();
  settings_data[ 'max_bookable' ] = jQuery( "#booking_maximum_number_days" ).val();
  
  if ( jQuery( "#booking_lockout_date" ).length > 0 ) {
    settings_data[ 'date_lockout' ] = jQuery( "#booking_lockout_date" ).val();
  }
  
  if ( jQuery( "#booking_minimum_number_days_multiple" ).length > 0 ) {
    settings_data[ 'min_days_multiple' ] = jQuery( "#booking_minimum_number_days_multiple" ).val();
  }
  
  if ( jQuery( "#booking_maximum_number_days_multiple" ).length > 0 ) {
    settings_data[ 'max_days_multiple' ] = jQuery( "#booking_maximum_number_days_multiple" ).val();
  }
  
  // all weekdays data   
  var all_weekdays  = jQuery('*[id^="booking_weekday_"]');
  var all_lockout   = jQuery('*[id^="weekday_lockout_"]');
  var all_price     = jQuery('*[id^="weekday_price_"]');
  
  for ( i=0; i <= 6; i++ ) {
    
    // weekdays
    var weekday_name = 'booking_weekday_' + i;
    
    if ( jQuery( all_weekdays[ i ] ).is( ":checked" ) ) {
      settings_data[ weekday_name ] = jQuery( all_weekdays[ i ] ).val();
    } else {
      settings_data[ weekday_name ] = '';
    }
    
    // lockout for each weekday
    var lockout_name = 'weekday_lockout_' + i;
    settings_data[ lockout_name ] = jQuery( all_lockout[ i ] ).val();
    
    // price for each weekday
    var price_name = 'weekday_price_' + i;
    settings_data[ price_name ] = jQuery( all_price[ i ] ).val();
  }

  var custom_range        = '';
  var specific_dates_list = '';
  var range_months        = '';
  var holidays_list       = '';
  var holiday_range       = '';
  
  if ( jQuery( '#specific_date_checkbox' ).attr( 'checked' ) ) {
    settings_data[ 'enable_specific' ] = jQuery( '#specific_date_checkbox' ).val();
  } else {
    settings_data[ 'enable_specific' ] = '';
  }
    
  //run through each row
  jQuery( '.specific tr[class^="added_specific_date_range_row_"]' ).each(function (i, row) {
    
    var range_set = jQuery(this).find( 'select[id^="range_dropdown"]' ).val();
    
    switch( range_set ) {
      
      case 'custom_range':
        
        var custom_start = jQuery( this ).find( 'input[class*="datepicker_start_date"]' ).val();
        var custom_end = jQuery( this ).find( 'input[class*="datepicker_end_date"]' ).val();
        
        if ( jQuery( this ).find( 'input[id*="bkap_bookable_nonbookable"]' ).attr( 'checked' ) ) {
          var booking_status = 'on';
        } else {
          var booking_status = '';
        }
        
        var recur_years = jQuery( this ).find( 'input[id*="bkap_number_of_year_to_recur_custom_range"]' ).val();
        
        if ( booking_status == 'on' ) {
          custom_range += custom_start + '+' + custom_end + '+' + recur_years + ';';
        } else {
          holiday_range += custom_start + '+' + custom_end + '+' + recur_years + '+' + range_set + ';';
        }
        
        break;
      
      case 'specific_dates':
        
        var specific_dates = jQuery( this ).find( 'textarea[id*="specific_dates_multidatepicker"]' ).val();
        if ( jQuery( this ).find( 'input[id*="bkap_bookable_nonbookable"]' ).attr( 'checked' ) ) {
          var booking_status = 'on';
        } else {
          var booking_status = '';
        }
        
        var lockout = jQuery( this ).find( 'input[id*="bkap_specific_date_lockout"]' ).val();
        var price = jQuery( this ).find( 'input[id*="bkap_specific_date_price"]' ).val();
        
        if ( booking_status == 'on' ) {
          specific_dates_list += specific_dates + '+' + lockout + '+' + price + ';';
        } else {
          holidays_list += specific_dates +  '+' + range_set + ';';
        }
        
        break;
      
      case 'range_of_months':
        
        var custom_start = jQuery( this ).find( 'select[id*="bkap_availability_from_month"]' ).val();
        var custom_end = jQuery( this ).find( 'select[id*="bkap_availability_to_month"]' ).val();
        if ( jQuery( this ).find( 'input[id*="bkap_bookable_nonbookable"]' ).attr( 'checked' ) ) {
          var booking_status = 'on';
        } else {
          var booking_status = '';
        }
        var recur_years = jQuery( this ).find( 'input[id*="bkap_number_of_year_to_recur_month"]' ).val();
        
         
        if ( booking_status == 'on' ) {
          range_months += custom_start + '+' + custom_end + '+' + recur_years + ';';
        } else {
          holiday_range += custom_start + '+' + custom_end + '+' + recur_years +  '+' + range_set + ';';
        }
        
        break;
      
      case 'holidays':
        
        var holidays = jQuery( this ).find( 'textarea[id*="holidays_multidatepicker"]' ).val();
        if ( jQuery( this ).find( 'input[id*="bkap_bookable_nonbookable"]' ).attr( 'checked' ) ) {
          var booking_status = 'on';
        } else {
          var booking_status = '';
        }
        
        var recur_years = jQuery( this ).find( 'input[id*="bkap_number_of_year_to_recur_holiday"]' ).val();
        
        if ( booking_status == 'on' ) {
          specific_dates_list += holidays + '+0' + ';';
        } else {
          holidays_list += holidays + '+' + recur_years +  '+' + range_set + ';';
        }
        
        break;
      
      default:
      break;
    }      
  }); 
    
  settings_data[ 'holidays_list' ]  = holidays_list;
  settings_data[ 'specific_list' ]  = specific_dates_list;
  settings_data[ 'month_range' ]    = range_months;
  settings_data[ 'custom_range' ]   = custom_range;
  settings_data[ 'holiday_range' ]  = holiday_range;

  //run through each row of the date time table
  var booking_times       = {}; 

  var booking_type_radio  = jQuery( '#booking_enable_type').val();
  
  if ( booking_type_radio !== undefined  && 'booking_enable_date_and_time' == booking_type_radio.trim() ) {

    time_type = bkap_get_date_and_time_type();

    if ( time_type == "date_time" ) {

      var j = 0;
      jQuery( '#bkap_date_timeslot_table tr[id^="bkap_date_time_row_"]' ).each(function (i, row) {
        
        booking_times[j] = {};
        booking_times[j][ 'day' ]           = jQuery( this ).find( 'select[id^="bkap_dateday_selector_"]' ).val();        
        booking_times[j][ 'from_time' ]     = jQuery( this ).find( 'input[id^="bkap_from_time_"]' ).val();        
        booking_times[j][ 'to_time' ]       = jQuery( this ).find( 'input[id^="bkap_to_time_"]' ).val();        
        booking_times[j][ 'lockout_slot' ]  = jQuery( this ).find( 'input[id^="bkap_lockout_time_"]' ).val();        
        booking_times[j][ 'slot_price' ]    = jQuery( this ).find( 'input[id^="bkap_price_time_"]' ).val();
        
        var global_check = '';
        if ( jQuery( this ).find( 'input[id*="bkap_global_time"]' ).attr( 'checked' ) ) {
          global_check = 'on';
        } 
        
        booking_times[j][ 'global_time_check' ] = global_check;        
        booking_times[j][ 'booking_notes' ]     = jQuery( this ).find( 'textarea[id^="bkap_note_time_"]' ).val();
        
        j++;

      });

      jQuery( '.bkap_date_timeslot_div' ).trigger( 'bkap_added' );
      jQuery( '.bkap_date_timeslot_div' ).trigger( 'bkap_row_updated' );

     // var bkap_data_toolbar = jQuery( '.bkap_date_timeslot_div' ).find( '.bkap_toolbar' );

      //var bkap_time_slots   =  JSON.parse( bkap_data_toolbar.attr( 'data-time-slots') );
      /**
       * Count is greater than 4 because when we have empty object its minimum length will be 2. So more than that will make sure that we have a data.
       * @since: 4.5.0
       */
      //if ( Object.keys( bkap_time_slots ).length > 0  ) {
          //booking_times = bkap_time_slots;
      //}

      settings_data[ 'booking_times' ] = booking_times;

    } else if ( time_type == "duration_time" ){
          
          var duration_times = {};

          var duration = duration_min = duration_max = 1;
          if ( jQuery( "#bkap_duration" ).val() != "" ) {
            duration = parseInt( jQuery( "#bkap_duration" ).val() );
          }
          if ( jQuery( "#bkap_duration_min" ).val() != "" ) {
            duration_min = parseInt( jQuery( "#bkap_duration_min" ).val() );
          }
          if ( jQuery( "#bkap_duration_max" ).val() != "" ) {
            duration_max = parseInt( jQuery( "#bkap_duration_max" ).val() );
          }
          
          duration_times[ 'duration_label' ]        = jQuery( "#bkap_duration_label" ).val();
          duration_times[ 'duration' ]              = duration;
          duration_times[ 'duration_min' ]          = duration_min;
          duration_times[ 'duration_max' ]          = duration_max;
          duration_times[ 'duration_max_booking' ]  = parseInt( jQuery( "#bkap_duration_max_booking" ).val() );
          duration_times[ 'duration_price' ]        = jQuery( "#bkap_duration_price" ).val();
          duration_times[ 'first_duration' ]        = jQuery( "#bkap_duration_start" ).val();
          duration_times[ 'end_duration' ]          = jQuery( "#bkap_duration_end" ).val();

          if ( jQuery( '#bkap_duration_type' ).length > 0 ) {
            duration_times[ 'duration_type' ] = jQuery( '#bkap_duration_type' ).val();
          }

          settings_data['duration_times']           = duration_times
    }    
  }

  return settings_data;
}

/**
 * Save the booking settings
 *
 * @function bkap_save_product_settings
 * 
 * @returns {object} JSON Object containing Booking Options
 * @since 4.6.0
 */
function bkap_save_product_settings(){
  booking_options = bkap_get_general_tab_data();
  settings_data = bkap_get_availability_data();
  gcal_settings = bkap_get_gcal_data();
  fixed_block_data  = bkap_fixed_block_data();
  price_range_data  = bkap_price_range_data();
  
  var fixed_blocks_enable = '';
  var price_ranges_enable = '';
  var block_price         = '';
  var block_price     = jQuery( '#bkap_enable_block_pricing_type').val();
  
  if( block_price != undefined ){
    if ( block_price.length > 0 && block_price == "booking_block_price_enable" ) {
      price_ranges_enable = block_price;
    }
    
    if ( block_price.length > 0 && block_price == "booking_fixed_block_enable" ) {
      fixed_blocks_enable = block_price;
    }
  }

  // setup the data
  var data = {
    booking_options: JSON.stringify( booking_options ),
    settings_data: JSON.stringify( settings_data ),
    gcal_data: JSON.stringify( gcal_settings ),
    ranges_enabled: price_ranges_enable,
    blocks_enabled: fixed_blocks_enable,
    fixed_block_data: JSON.stringify( fixed_block_data ),
    price_range_data: price_range_data,
    
    product_id: bkap_settings_params.post_id,
    action: 'bkap_save_settings'
  };

  return data;
}

/**
 * Populate Fixed Block Data
 *
 * @function bkap_fixed_block_data
 * 
 * @returns {mixed} JSON Object containing fixed block data else false
 *
 * @since 4.0.0
 */ 
function bkap_fixed_block_data(){
  
  var fixed_block_data    = {};
  var fixed_block_row_data  = '';
  var validate_field      = "";
  
  jQuery( '#bkap_fixed_block_booking_table tr[id^="bkap_fixed_block_row_"]' ).each( function ( i, row ) {
    
    var block_name          = jQuery( this ).find( 'input[id*="booking_block_name"]' ).val();
    var number_of_days        = jQuery( this ).find( 'input[id*="number_of_days"]' ).val();
    var start_day           = jQuery( this ).find( 'select[id*="start_day"]' ).val();
    var end_day           = jQuery( this ).find( 'select[id*="end_day"]' ).val();
    var price             = jQuery( this ).find( 'input[id*="fixed_block_price"]' ).val();
    var fixed_block_row_id      = this.id;
    var split_data = fixed_block_row_id.split( '_' );
      var fixed_block_row_id_number = split_data[ 4 ];
        
    if( block_name == "" ){
      alert("Block name must be filled out");
      validate_field = "FAILED";
          return false;
    }
    if( number_of_days == "" ){
      alert("Number of days must be filled out");
      validate_field = "FAILED";
          return false;
    }
    if( price == "" ){      
      alert("Price must be filled out");
      validate_field = "FAILED";
          return false;
    }
    
    fixed_block_row_data += block_name + '&&' + number_of_days + '&&' + start_day + '&&' + end_day + '&&' + price + '&&' + fixed_block_row_id_number + ';';   
  });
  
  fixed_block_data[ 'bkap_fixed_block_data' ] = fixed_block_row_data;
  
  if( validate_field == "FAILED" ){
    return false;
  }else{
    return fixed_block_data;
  }
  
}

/**
 * Get Price Range Data
 *
 * @function bkap_price_range_data
 * 
 * @returns {mixed} JSON Object containing Price Range Data else false
 * @since 4.1.0
 */
function bkap_price_range_data(){
  
  var price_range_data    = {};
  var price_range_row_data  = '';
  var validate_field      = "";
  
  var attribute_count = 0; 
  
  // Calculating new ID to assign new tr.
  jQuery( "tr#bkap_default_price_range_row > td select[id^='attribute_']" ).each( function(){
    attribute_count++;
  });
  
  jQuery( '#bkap_price_range_booking_table tr[id^="bkap_price_range_row_"]' ).each( function ( i, row ) {
    var attributes = "";
    var attribute_values = "";  
    if( attribute_count > 0 ){
      
      jQuery( "tr#"+this.id+" > td select[id^='attribute_']" ).each( function(){
        attribute_count++;
        
        var select_id = jQuery(this)[0].id;
        
        var select_new_id = "#"+select_id+" :selected ";
        
        var value = jQuery(select_new_id).val();
        attribute_values += value+"~~";
        
      });
      if( attribute_values.length > 0 ){
        //attribute_values = attribute_values.substring(0, attribute_values.length - 2);
      }
    }
    
    var min_number            = jQuery( this ).find( 'input[id*="number_of_start_days"]' ).val();
    var max_number            = jQuery( this ).find( 'input[id*="number_of_end_days"]' ).val();
    var per_day_price           = jQuery( this ).find( 'input[id*="per_day_price"]' ).val();
    var fixed_price           = jQuery( this ).find( 'input[id*="fixed_price"]' ).val();
    
    var price_range_row_id        = this.id;
    var split_data            = price_range_row_id.split( '_' );
      var price_range_row_id_number     = split_data[ 4 ];
    
    if( min_number == "" ){
      alert("Minimum Days must be filled out");
      validate_field = "FAILED";
          return false;
    }
    if( max_number == "" ){
      alert("Maximum Days must be filled out");
      validate_field = "FAILED";
          return false;
    }
    
    if( max_number != "" && min_number != "" ){
      if( parseInt(max_number) < parseInt(min_number) ){
        alert("Minimum days should be less than the Maximum days.");
        validate_field = "FAILED";
            return false;
      }
    }
    
    if( per_day_price == "" && fixed_price == "" ){     
      alert("Price must be filled out");
      validate_field = "FAILED";
          return false;
    }
    
    price_range_row_data += attribute_values + min_number + '~~' + max_number + '~~' + per_day_price + '~~' + fixed_price + '~~' + price_range_row_id_number + ';;';   
  });
  
  price_range_data = price_range_row_data;
  
  if( validate_field == "FAILED" ){
    return false;
  }else{
    return price_range_data;
  }
  
}

/**
 * This function will get the settings on
 * the Google Calendar tab and return an array
 *
 * @function bkap_get_gcal_data
 * 
 * @returns {object} JSON Object containing GCal Settings
 * @since 4.0
 */
function bkap_get_gcal_data() {

  var gcal_settings = {}; 

  var gcal_sync_mode = jQuery( 'input:radio[name=product_sync_integration_mode]:checked').val();
  
  if ( gcal_sync_mode != undefined ) {
    gcal_settings[ 'gcal_sync_mode' ] = gcal_sync_mode.trim();
  }
  
  gcal_settings[ 'key_file_name' ] = jQuery( '#product_sync_key_file_name' ).val();
  
  gcal_settings[ 'service_acc_email' ] = jQuery( '#product_sync_service_acc_email_addr' ).val();
  
  gcal_settings[ 'calendar_id' ] = jQuery( '#product_sync_calendar_id' ).val();
  
  if ( jQuery( "#enable_automated_mapping" ).attr( "checked" ) ) {
    gcal_settings[ 'gcal_auto_mapping'] = jQuery( "#enable_automated_mapping" ).val();
  } 
  
  if ( jQuery( '#gcal_default_variation' ).length > 0 ) {
    gcal_settings[ 'default_variation' ] = jQuery( '#gcal_default_variation' ).val();
  }
  
  for( var key = 0; ; key++ ) {
  
    var field_name = 'product_ics_fee_url_' + key;
    if ( jQuery( '#' + field_name ).length > 0 ) {
      gcal_settings[ 'ics_feed_url_' + key ] = jQuery( '#' + field_name ).val();
    } else {
      break;
    }
  }

  return gcal_settings;

}

jQuery( document ).ready( function () {
	

	  // JS actions for Resources start.

	  // Type box.
	  jQuery( '.bkap_type_box' ).appendTo( '#woocommerce-booking .hndle span' );

	  // Clicking on Single Day will hide Set Weekdays & Availability button and show Set Weekdays button
	  jQuery( "#_bkap_resource" ).click( function() {
	    //jQuery("#block_booking").removeAttr( "style" );

	      if ( jQuery( "#_bkap_resource" ).prop( "checked" ) == false){
	        jQuery("#resource_tab_settings").css( "display", "none" );
	        
	      }else{
	        jQuery("#resource_tab_settings").removeAttr( "style" );
	        
	      }
	  });

	  // Add a resource in the meta box
	  jQuery('#bkap_resource_section').on('click', 'button.bkap_add_resource', function(){
	    var loop              = jQuery('.bkap_resource_row').length;
	    var add_resource_id   = jQuery('select.bkap_add_resource_id').val();
	    var add_resource_name = '';

	    if ( ! add_resource_id ) {
	      add_resource_name = prompt( "Please enter resource name:" );

	      if ( ! add_resource_name ) {
	        return false;
	      }
	    }
	    
	    jQuery( '.woocommerce_bookable_resources' ).block( { message: null } );

	    var data = {
	      action:            'bkap_add_resource',
	      post_id:           bkap_settings_params.post_id,
	      loop:              loop,
	      add_resource_id:   add_resource_id,
	      add_resource_name: add_resource_name,      
	    };

	    jQuery.post( bkap_settings_params.ajax_url, data, function( response ) {
	      if ( response.error ) {
	        alert( response.error );
	      } else {
	        jQuery( '.bkap_resource_info' ).append( response.html ).unblock();
	        
	        if ( add_resource_id ) {
	          jQuery( '.bkap_add_resource_id' ).find( 'option[value=' + add_resource_id + ']' ).remove();
	        }
	      }
	    });

	    return false;
	  });


    jQuery( document ).on( "click", "[id^=bkap_remove_resource]" , function() {

      var clicked_resource_id = jQuery(this)[0].id;       
      var split_data          = clicked_resource_id.split( '_' );
      var resource_id         = split_data[ 3 ];

      if ( resource_id == undefined ) {
        resource_id = 0;
        var y = confirm( bkap_resource_params.delete_resource_conf_all );
      }else{
        var y = confirm( bkap_resource_params.delete_resource_conf );
      }    
      
      if( y == true ) {
      	if ( resource_id == 0 ) {
					jQuery('.bkap_resource_row').remove();
				} else{
					bkap_rr_id = "#"+clicked_resource_id;
					jQuery( bkap_rr_id ).parent().remove();
				}
			
				// add a fade out message which informs the admin that the settings have been saved
				jQuery( '#resource_update_notification' ).addClass( 'bkap_updated_notice' );
				jQuery( '#resource_update_notification' ).css( 'display', 'block' );
				jQuery( '#resource_update_notification' ).html( bkap_resource_params.delete_resource ).fadeOut( 5000 );
      }

    });

	  // JS actions for Resources end.

  // on change event for product type
  jQuery( '#product-type' ).on( 'change', function(){
    
    var product_type = jQuery( '#product-type' ).val();

    if ( 'simple' != product_type ) {
       jQuery( '#resource_tab_settings' ).css( "display", "none" );
    } else if ( 'simple' == product_type ) {
       jQuery( '#resource_tab_settings' ).css( "display", "block" );
    }

    if( 'grouped' == product_type ) {
      jQuery( '#bkap_gcal_msg' ).css( "display", "block" );
      jQuery( '#bkap_gcal_fields').prop( 'disabled', 'disabled' );
    } else {
      jQuery( '#bkap_gcal_msg' ).css( "display", "none" );
      jQuery( '#bkap_gcal_fields').prop( 'disabled', '' );      
    }

  });
  
  /*
   * Expand/Collapse rows in the Availability by Dates/Months table.
     */
  jQuery( document ).on( 'click', '.bkap_expand_all', function() {
    jQuery( ".bkap_row_toggle" ).show();
    return false;
  })
  jQuery( document ).on( 'click', '.bkap_close_all', function() {
    jQuery( ".bkap_row_toggle" ).hide();
    return false;
  });
  
  /*
   * Expand/Collapse rows in the Weekdays/Dates And It's Timeslots  table.
     */
  jQuery( document ).on( 'click', '.bkap_time_expand_all', function() {
    jQuery( ".bkap_time_row_toggle" ).show();
    return false;
  })
  jQuery( document ).on( 'click', '.bkap_time_close_all', function() {
    jQuery( ".bkap_time_row_toggle" ).hide();
    return false;
  });
  
  /*
   * Expand/Collapse rows in the Fixed Booking Blocks table.
     */
  jQuery( document ).on( 'click', '.bkap_fixed_expand_all', function() {
    
    jQuery( ".bkap_fixed_row_toggle" ).show();
    return false;
  });
  jQuery( document ).on( 'click', '.bkap_fixed_close_all', function() {
    jQuery( ".bkap_fixed_row_toggle" ).hide();
    return false;
  });
  
  /*
   * Expand/Collapse rows in the Fixed Booking Blocks table.
     */
  jQuery( document ).on( 'click', '.bkap_price_range_expand_all', function() {
    
    jQuery( ".bkap_price_range_row_toggle" ).show();
    return false;
  });
  jQuery( document ).on( 'click', '.bkap_price_range_close_all', function() {
    jQuery( ".bkap_price_range_row_toggle" ).hide();
    return false;
  });
  
  
  /*
   * Below code will show a message based on the hovered on the Booking Type.
   */
  jQuery( "#enable_booking_day_type" ).click( function() {
    jQuery( ".show-booking-day-description" ).removeAttr( "style" );
    jQuery( ".show-booking-day-description" ).html( "<b><i>" + bkap_settings_params.only_day_text + "</i></b>" );
  })
  .mouseout(function() {
    //jQuery( ".show-booking-day-description" ).css( "display", "none" );
  });
  
  jQuery( "#enable_booking_day_and_time_type" ).click( function() {
    jQuery( ".show-booking-day-description" ).css( "display", "block" );
    jQuery( ".show-booking-day-description" ).html( "<b><i>" + bkap_settings_params.date_time_text + "</i></b>" );
    jQuery( "#enable_date_time_booking_section" ).removeAttr( "style" );

  })
  .mouseout(function() {
    //jQuery( ".show-booking-day-description" ).css( "display", "none" );
  });
  
  // Hide only day section and availability buttons 
  jQuery( "#enable_booking_day_and_time_type" ).click( function() {
    //jQuery( "#set_availability_button_section" ).css( "display", "none" );
      jQuery( "#enable_only_day_booking_section" ).css( "display", "none" );
      
      //Hide Fixed Block Booking and Price By Range tab when Date and Time option. 
      jQuery( "#block_booking" ).css( "display", "none" );
      jQuery( "#block_booking_price" ).css( "display", "none" );
      
      bkap_add_weekdays_avail();
      // hide the multiple days setup fields in the Availability tab
      jQuery( ".multiple_days_setup" ).css( "display", "none" );
      // display the purchase without date setting in the General tab
      jQuery( "#purchase_wo_date_section" ).removeAttr( "style" );
      
      // Display day/date and timeslot table when Date and time type is enabled.
      if( jQuery('#enable_fixed_time').is(':checked') ) { 
        jQuery( ".bkap_date_timeslot_div" ).removeAttr( "style" );
        //append_weekdays();
      }

      if( jQuery('#enable_duration_time').is(':checked') ) { 
        jQuery( ".bkap_date_timeslot_div" ).css( "display", "none" );
      }      
  });  

  jQuery( "#enable_fixed_time" ).click( function() {
    jQuery( ".show-booking-day-description" ).css( "display", "block" );
    jQuery( ".show-booking-day-description" ).html( "<b><i>" + bkap_settings_params.fixed_time_text + "</i></b>" );
    // Display day/date and timeslot table when Date and time type is enabled.
    if( jQuery('#enable_fixed_time').is(':checked') ) { 
      jQuery( ".bkap_date_timeslot_div" ).removeAttr( "style" );
      jQuery( ".bkap_duration_date_timeslot_div" ).css( "display", "none" );
      //append_weekdays();
    }
  });

  jQuery( "#enable_duration_time" ).click( function() {
    jQuery( ".show-booking-day-description" ).css( "display", "block" );
    jQuery( ".show-booking-day-description" ).html( "<b><i>" + bkap_settings_params.duration_time_text + "</i></b>" );
    // Display day/date and timeslot table when Date and time type is enabled.
    if( jQuery('#enable_duration_time').is(':checked') ) { 
      jQuery( ".bkap_date_timeslot_div" ).css( "display", "none" );
      jQuery( ".bkap_duration_date_timeslot_div" ).removeAttr( "style" );
    }
    bkap_add_weekdays();
  });

  
    
    // Show only day section when selecting Only Day Booking Method.    
    jQuery( "#enable_booking_day_type" ).click( function() {
        jQuery( "#enable_only_day_booking_section" ).removeAttr( "style" );
        jQuery( ".bkap_date_timeslot_div" ).css( "display", "none" );
        jQuery( "#enable_date_time_booking_section" ).css( "display", "none" );
        jQuery( "#bkap_duration_date_timeslot_div" ).css( "display", "none" );
        
        /* When switching to Only Day from Date & Time and Mulitple Days was already selected
         * then show Fixed Block Booking and Price By Range tab.
         */
        if ( jQuery( "#enable_booking_multiple_days" ).prop( "checked" ) == false){
        jQuery("#block_booking_price").css( "display", "none" );
        jQuery("#block_booking").css( "display", "none" );
      }else{
        jQuery("#block_booking_price").removeAttr( "style" );
        jQuery("#block_booking").removeAttr( "style" );
      }
        
        jQuery("input#specific_date_checkbox").removeAttr("title");
        
        // display the multiple days setup fields in the Availability tab if multiple days is already selected
        var only_day_type = jQuery( 'input:radio[name=booking_enable_only_day]:checked').val();
        if ( only_day_type != undefined ) {
      if ( 'booking_enable_multiple_days' == only_day_type.trim() ) {
        jQuery( ".multiple_days_setup" ).removeAttr( "style" );
        // hide the purchase without date setting in the General tab
          //jQuery( "#purchase_wo_date_section" ).css( "display", "none" );
          bkap_add_weekdays();
      } else {
        // display the purchase without date setting in the General tab
          jQuery( "#purchase_wo_date_section" ).removeAttr( "style" );
          bkap_add_weekdays_avail();
      }
        }
    });
  
    // Clicking on Single Day will hide Set Weekdays button and show Set Weekdays & Availability button 
   
    // apply Select 2 when displaying records in Weekdays/Dates And It's Timeslots table.

    jQuery( "#enable_booking_single" ).click( function() {
      // hide the multiple days setup fields in the Availability tab
      jQuery( ".multiple_days_setup" ).css( "display", "none" );
      jQuery( ".show-multiple-day-per-night-price-description" ).css( "display", "none" );
      jQuery( ".show-booking-day-description" ).html( "<b><i>" + bkap_settings_params.single_day_text + "</i></b>" );
      // Hide Fixed Block Booking and Price By Range tab when Single Day option is selected. 
      jQuery( "#block_booking" ).css( "display", "none" );
      jQuery( "#block_booking_price" ).css( "display", "none" );
      
      // hide the weekdays setup UI
    jQuery( "#set_weekdays" ).css( "display", "none" );
    // display the purchase without date setting in the General tab
    jQuery( "#purchase_wo_date_section" ).removeAttr("style");
      bkap_add_weekdays_avail();
  });
    
    // Clicking on Single Day will hide Set Weekdays & Availability button and show Set Weekdays button
    jQuery( "#enable_booking_multiple_days" ).click( function() {
      jQuery( ".show-booking-day-description" ).html( "<b><i>" + bkap_settings_params.multiple_nights_text + "</i></b>" );
      jQuery( ".show-multiple-day-per-night-price-description" ).html( "<b><i>" + bkap_settings_params.multiple_nights_price_text + "</i></b>" );
  	
  	// apply Select 2 when displaying records in Weekdays/Dates And It's Timeslots table.
      jQuery( ".show-multiple-day-per-night-price-description" ).removeAttr( "style" );
      // display the multiple days setup fields in the Availability tab
      jQuery( ".multiple_days_setup" ).removeAttr( "style" );
      // hide the weekdays setup UI
    jQuery( "#set_weekdays" ).css( "display", "none" );
    // hide the purchase without date setting in the General tab
    //jQuery( "#purchase_wo_date_section" ).css( "display", "none" );
    
    jQuery("#block_booking_price").removeAttr( "style" );
    jQuery("#block_booking").removeAttr( "style" );
    
      bkap_add_weekdays();
  });
    
    // apply Select 2 when displaying records in Weekdays/Dates And It's Timeslots table.

    if ( jQuery(".bkap_dateday_selector").length > 0 ) {
      jQuery(".bkap_dateday_selector").select2({
        allowClear: false,
        width: '100%',
      });
    }
    
    // Enable and disable lockout based on enable/disable weekday.
    
    jQuery( document ).on( "click", "[id^=week_day_]" , function() {
      var week_id = lastChar = lock_id = weekday_id = "";
       week_id    = jQuery(this)[0].id;
       week_id        = "#"+week_id;
       lastChar   = week_id.slice(-1);
       lock_id    = "#weekday_lockout_"+lastChar;
      
       weekday_id = "#"+week_id+"[checked]";  
      
      if ( jQuery( week_id ).prop("checked") == false){
        jQuery( lock_id ).val("");
        jQuery(lock_id).prop("disabled", true);
      }else{
        jQuery( lock_id ).removeAttr( "disabled" );
      }
    });
    
  // Bkap box.
    jQuery( '.bkap_box' ).appendTo( '#woocommerce-booking .hndle span' );
    
    
    jQuery( function() {
    // Prevent inputs in meta box headings opening/closing contents.
      jQuery( '#woocommerce-booking' ).find( '.hndle' ).unbind( 'click.postboxes' );

      jQuery( '#woocommerce-booking' ).on( 'click', '.hndle', function( event ) {

      // If the user clicks on some form input inside the h3 the box should not be toggled.
      if ( jQuery( event.target ).filter( 'input, option, label, select' ).length ) {
        return;
      }

      jQuery( '#woocommerce-booking' ).toggleClass( 'closed' );
    });
  });
    
    // This will add datepicker to the Start Date textbox.
    jQuery(".specific").on("focus","input.datepicker_start_date",function () {
      
      var from_start_date_id  = from_start_date_new_id = "";
        
      from_start_date_id    = jQuery(this)[0].id;
      from_start_date_new_id  = "#"+from_start_date_id;
        jQuery( from_start_date_new_id ).datepicker({
          minDate: 0,
          dateFormat: "d-m-yy"  
        });
          
    });
    
  // This will add datepicker to the End Date textbox.
    jQuery(".specific").on( "focus", "input.datepicker_end_date", function() {
      
      var to_end_date_id  = to_end_date_new_id = "";
        
      to_end_date_id    = jQuery(this)[0].id;
      to_end_date_new_id  = "#"+to_end_date_id;
        jQuery( to_end_date_new_id ).datepicker({
          minDate: 0,
          dateFormat: "d-m-yy"  
        });
          
    });
    
    // This will add multiple dates picker to the specific and holiday textarea.
    jQuery(".specific").on( "focus", "textarea", function() {
      
      var multiple_dates_specific_holiday_id  = multiple_dates_specific_holiday_new_id = "";
        
      multiple_dates_specific_holiday_id    = jQuery(this)[0].id;
      multiple_dates_specific_holiday_new_id  = "#"+multiple_dates_specific_holiday_id;
        multiple_dates_specific_holiday_value  = jQuery( multiple_dates_specific_holiday_new_id ).val();
      
        var formats               = ["d.m.y", "d-m-yyyy","MM d, yy"];
      jQuery( multiple_dates_specific_holiday_new_id ).datepick({
        minDate: new Date(), 
        dateFormat: formats[1], 
        multiSelect: 999, 
        monthsToShow: 1, 
        showTrigger: '#calImg',
        onClose: function() {
          
          append_specific_dates( multiple_dates_specific_holiday_id, multiple_dates_specific_holiday_value ); 
        }
      });
    });
    
    // Clicking on Calendar Image should populate the
    
    
    jQuery( ".specific" ).on( "click", "[id^=custom_check]",  function() {
      
      var custom_check_cal_id       = jQuery(this)[0].id;
      var spliting_custom_check_cal_id  = custom_check_cal_id.split( '_' );
      var custom_check_cal_id_number    = parseInt( spliting_custom_check_cal_id[3] );
      
      if( spliting_custom_check_cal_id[1] == "checkout" ){
          var custom_checkout_textbox_id = "#datepicker_textbox__"+custom_check_cal_id_number;
          jQuery(custom_checkout_textbox_id).focus();
      }else if ( spliting_custom_check_cal_id[1] == "checkin" ){
          var custom_checkin_textbox_id = "#datepicker_textbox_"+custom_check_cal_id_number;
          jQuery(custom_checkin_textbox_id).focus();
      }
    });
  
    
    jQuery( ".specific" ).on( "click", ".bkap_multiple_datepicker_cal_image",  function() {
      
      var multiple_datepicker_image_id      = jQuery(this)[0].id;
      var spliting_multiple_datepicker_image_id   = multiple_datepicker_image_id.split( '_' );
      
      if ( spliting_multiple_datepicker_image_id.length == 5 ){
        var multiple_datepicker_image_id_number   = parseInt( spliting_multiple_datepicker_image_id[4] );
      }else{
        var multiple_datepicker_image_id_number   = parseInt( spliting_multiple_datepicker_image_id[3] );
      }
      
      if( spliting_multiple_datepicker_image_id[0] == "specific" ){
        
          var specific_date_multidate_cal_id  = "#specific_dates_multidatepicker_"+multiple_datepicker_image_id_number;
          var specific_date_multidate_cal_value  = jQuery( specific_date_multidate_cal_id ).val();
          var formats             = ["d.m.y", "d-m-yyyy","MM d, yy"];
        
          jQuery( specific_date_multidate_cal_id ).datepick({
            minDate: new Date(), 
            dateFormat: formats[1], 
            multiSelect: 999, 
            monthsToShow: 1, 
            showTrigger: '#calImg',
            onClose: function() {
              append_specific_dates( specific_date_multidate_cal_id, specific_date_multidate_cal_value ); 
          }
          });

          jQuery( specific_date_multidate_cal_id ).focus();
          
      }else if ( spliting_multiple_datepicker_image_id[0] == "holiday" ){
        
          var holiday_multidate_cal_id  = "#holidays_multidatepicker_"+multiple_datepicker_image_id_number;
          var formats           = ["d.m.y", "d-m-yyyy","MM d, yy"];
        
          jQuery( holiday_multidate_cal_id ).datepick({minDate: new Date(), dateFormat: formats[1], multiSelect: 999, monthsToShow: 1, showTrigger: '#calImg'});
          jQuery( holiday_multidate_cal_id ).focus();
      }
      
    });

    var booking_type_radio = jQuery( '#booking_enable_type' ).val();
  
  if ( booking_type_radio != undefined && 'booking_enable_date_and_time' == booking_type_radio.trim() ) {
      //append_weekdays();
  }
    // This will remove the tr from the specific date/month table when clicking on the delete button.
    
    jQuery( ".specific" ).on( "click", "[id^=bkap_close]",  function() {

    // place an ajax call to update the DB
      var id = this.id;
      
      var split_data = id.split( '_' );
      var row_number = split_data[ 2 ];

      var range_set = jQuery( '#range_dropdown_' + row_number ).val();
      var record_type = range_set;
      
      switch( range_set ) {
          case 'custom_range':
            var start = jQuery( '#datepicker_textbox_' + row_number ).val();
          var end = jQuery( '#datepicker_textbox__' + row_number ).val();
                if ( jQuery( '#bkap_bookable_nonbookable_' + row_number ).attr( 'checked' ) ) {
                } else {
                  record_type = 'holiday_range'; 
                }
                break;
          case 'range_of_months':
            var start_name = 'select[id="bkap_availability_from_month_' + row_number + '"]'
          var start = jQuery( start_name ).val();
            
            var end_name = 'select[id="bkap_availability_to_month_' + row_number + '"]'
          var end = jQuery( end_name ).val();
          if ( jQuery( '#bkap_bookable_nonbookable_' + row_number ).attr( 'checked' ) ) {
          } else {
            record_type = 'holiday_range';
          }
          break;
          case 'specific_dates':
            var start = jQuery( '#specific_dates_multidatepicker_' + row_number ).val();
            var end = '';
            break;
          case 'holidays':
            var start = jQuery( '#holidays_multidatepicker_' + row_number ).val();
            var end = '';
            break;
        default:
        break;
              
      }
      
      var data = {
          product_id: bkap_settings_params.post_id,
          record_type: record_type,
          start: start,
          end: end,
          action: 'bkap_delete_specific_range'
      };
      
      //jQuery.post( bkap_settings_params.ajax_url, data, function(response) {
        jQuery( '.added_specific_date_range_row_' + row_number ).remove();
      //});
    });
  
    // This will remove the tr from the date and time table when clicking on the delete button.
    
    jQuery( ".bkap_date_timeslot_div" ).on( "click", "[id^=bkap_close]",  function() {
      
      //bkap_time_slots_meta_box_ajax.bkap_block();
    // place an ajax to update the DB
      var id = this.id;

      var split_data = id.split( '_' );
      var row_number = split_data[ 2 ];
      
      var day_id = '#bkap_dateday_selector_' + row_number;
      var from_time_id = '#bkap_from_time_' + row_number;
      var to_time_id = '#bkap_to_time_' + row_number;
      
      var day = jQuery( day_id ).val();
      var from_time = jQuery( from_time_id ).val();
      var to_time = jQuery( to_time_id ).val();
      
      var data = {
        product_id: bkap_settings_params.post_id,
      day: day,
      from_time: from_time,
      to_time: to_time,
      action: 'bkap_delete_date_time'
      };
      
      //jQuery.post( bkap_settings_params.ajax_url, data, function(response) {  
            jQuery( '#bkap_date_time_row_' + row_number ).remove();
            jQuery( '.bkap_date_timeslot_div' ).trigger( 'bkap_row_deleted', row_number );
            //bkap_time_slots_meta_box_ajax.unblock();
          //});
    });
    
    // This will add new row in SET AVAILABILITY BY DATE/MONTHS table
  jQuery( ".bkap_add_new_range" ).on( "click" , function() {
    var each_row    = new Array();
    var i         = 0;
    var last_class_name = "";
    
    
    // Calculating new ID to assign new tr.
    jQuery( "tr[class^='added_specific_date_range_row']" ).each( function(){
      
      var class_name_row  = jQuery(this)[0].className;
      last_class_name = class_name_row;
      var res = class_name_row.replace("added_specific_date_range_row_", "");
      
      if( res == class_name_row && each_row.length == 0){
        each_row[i] = 1;
      }else{
        each_row[i] = parseInt(res);
      }
      i++;
    
    });
    
    if( isNaN( each_row[0] ) ){
      each_row.shift();
    }
    var max = Math.max.apply(Math,each_row);
    var new_id = max + 1;
    
    
    
    // This will written complete tr element.
    var tr = jQuery(".added_specific_date_range_row")[0].outerHTML; // This will written complete tr element.
    // Removing style and giving new id to the tr
    var new_tr = tr.replace("class=\"added_specific_date_range_row\" style=\"display: none;\"", "class=\"added_specific_date_range_row_"+new_id+"\"");
    
    // Changing id of elements with the new one.
    new_tr = new_tr.replace( "id=\"datepicker_textbox1\""         , "id=\"datepicker_textbox_"+new_id+"\"" ); 
    new_tr = new_tr.replace( "id=\"datepicker_textbox2\""         , "id=\"datepicker_textbox__"+new_id+"\"" ); 
    
    new_tr = new_tr.replace( "id=\"range_dropdown\""          , "id=\"range_dropdown_"+new_id+"\"" );
    new_tr = new_tr.replace( "id=\"textareamultidate_cal1\""      , "id=\"specific_dates_multidatepicker_"+new_id+"\"" );
    new_tr = new_tr.replace( "id=\"textareamultidate_cal2\""      , "id=\"holidays_multidatepicker_"+new_id+"\"" );
    
    new_tr = new_tr.replace( "id=\"bkap_availability_from_month\""    , "id=\"bkap_availability_from_month_"+new_id+"\"" );
    new_tr = new_tr.replace( "id=\"bkap_booking_availability_to_month\"", "id=\"bkap_booking_availability_to_month_"+new_id+"\"" );
    
    new_tr = new_tr.replace( "id=\"custom_checkin_cal\""        , "id=\"custom_checkin_cal_"+new_id+"\"" );
    new_tr = new_tr.replace( "id=\"custom_checkout_cal\""       , "id=\"custom_checkout_cal_"+new_id+"\"" );
    
    new_tr = new_tr.replace( "id=\"month_checkin_cal\""         , "id=\"month_checkin_cal_"+new_id+"\"" );
    new_tr = new_tr.replace( "id=\"month_checkout_cal\""        , "id=\"month_checkout_cal_"+new_id+"\"" );
    
    new_tr = new_tr.replace( "id=\"specific_date_multidate_cal\""   , "id=\"specific_date_multidate_cal_"+new_id+"\"" );
    new_tr = new_tr.replace( "id=\"holiday_multidate_cal\""       , "id=\"holiday_multidate_cal_"+new_id+"\"" );
    
    new_tr = new_tr.replace( "id=\"bkap_bookable_nonbookable\""     , "id=\"bkap_bookable_nonbookable_"+new_id+"\"" );
    new_tr = new_tr.replace( "id=\"bkap_close\""            , "id=\"bkap_close_"+new_id+"\"" );
    new_tr = new_tr.replace( "id=\"bkap_myPopup\""            , "id=\"bkap_myPopup_"+new_id+"\"" );
    
    // changing the id of defauld fields for Lockout column to new ID.
    
    new_tr = new_tr.replace( "id=\"bkap_number_of_year_to_recur_custom_range\"" , "id=\"bkap_number_of_year_to_recur_custom_range_"+new_id+"\"" );
    new_tr = new_tr.replace( "id=\"bkap_number_of_year_to_recur_holiday\""    , "id=\"bkap_number_of_year_to_recur_holiday_"+new_id+"\"" );
    new_tr = new_tr.replace( "id=\"bkap_number_of_year_to_recur_month\""    , "id=\"bkap_number_of_year_to_recur_month_"+new_id+"\"" );
    new_tr = new_tr.replace( "id=\"bkap_specific_date_lockout\""        , "id=\"bkap_specific_date_lockout_"+new_id+"\"" );
    new_tr = new_tr.replace( "id=\"bkap_specific_date_price\""          , "id=\"bkap_specific_date_price_"+new_id+"\"" );
    
    last_class_name = last_class_name.replace(" bkap_row_toggle", "");
    var last_class_name_new = "."+last_class_name;
    
    jQuery( last_class_name_new ).after(new_tr);
    
    resetCollapsHeight($('.specific_date'));
  });
  
  // This will add new row in SET DATE/DAYS and TIMESLOT table
  
  jQuery( ".bkap_add_new_date_time_range" ).on( "click" , function() {
    
    var each_row_time     = [];
    var i_time        = 0;
    var last_class_name_time = "bkap_default_date_time_row";
    
    
    // Calculating new ID to assign new tr.
    jQuery( "tr[id^='bkap_date_time_row']" ).each( function(){
      
      var class_name_row_time  = jQuery(this)[0].id;
      
      last_class_name_time = class_name_row_time;
      
      //bkap_date_time_row_1
      
      var res = class_name_row_time.replace("bkap_date_time_row_", "");
      
      if( res == class_name_row_time && each_row_time.length == 0){
        each_row_time[i_time] = 1;
      }else{
        each_row_time[i_time] = parseInt(res);
      }
      i_time++;
    
    });

    if( each_row_time.length == 0 ){
      each_row_time[i_time] = 1;
    }
    if( isNaN( each_row_time[0] ) ){
      each_row_time.shift();
    }
    var max_time = Math.max.apply(Math,each_row_time);
    var new_id_time = max_time + 1;
    
    // This will written complete tr element.
    var time_tr = jQuery("#bkap_default_date_time_row")[0].outerHTML; // This will written complete tr element.
    // Removing style and giving new id to the tr
    var new_time_tr = time_tr.replace('id="bkap_default_date_time_row" style="display: none;"', 'id="bkap_date_time_row_'+new_id_time+'" class="bkap_added"');
    
    // Changing the id of elements with new one.
    new_time_tr = new_time_tr.replace("id=\"bkap_dateday_selector\"",   "id=\"bkap_dateday_selector_"+new_id_time+"\"");
    new_time_tr = new_time_tr.replace("id=\"bkap_from_time\"",      "id=\"bkap_from_time_"+new_id_time+"\"");
    new_time_tr = new_time_tr.replace("id=\"bkap_to_time\"",      "id=\"bkap_to_time_"+new_id_time+"\"");
    new_time_tr = new_time_tr.replace("id=\"bkap_lockout_time\"",     "id=\"bkap_lockout_time_"+new_id_time+"\"");
    new_time_tr = new_time_tr.replace("id=\"bkap_price_time\"",     "id=\"bkap_price_time_"+new_id_time+"\"");
    new_time_tr = new_time_tr.replace("id=\"bkap_global_time\"",    "id=\"bkap_global_time_"+new_id_time+"\"");
    new_time_tr = new_time_tr.replace("id=\"bkap_note_time\"",      "id=\"bkap_note_time_"+new_id_time+"\"");
    //var new_time_tr = time_tr.replace("id=\"bkap_default_date_time_row\" style=\"display:none\"", "id=\"bkap_date_time_row_"+new_id_time+"\"");
    new_time_tr = new_time_tr.replace( "id=\"bkap_close\"",       "id=\"bkap_close_"+new_id_time+"\"" );
    
    var last_class_name_time_new = "#"+last_class_name_time;
    
    jQuery( last_class_name_time_new ).after(new_time_tr);
    
    jQuery( document ).find( '#bkap_dateday_selector_' + new_id_time ).select2({width:'100%'});
    
    resetCollapsHeight($('.bkap_date_timeslot_div'));
  });
  
  
  // This will add new row in Fixed Block Booking table
  
  jQuery( ".bkap_add_new_fixed_block" ).on( "click" , function() {
    
    var each_fixed_block      = new Array();
    var i_fixed_bock        = 0;
    var last_class_name_fixed_block = "bkap_default_fixed_block_row";
    
    
    // Calculating new ID to assign new tr.
    jQuery( "tr[id^='bkap_fixed_block_row']" ).each( function(){
      
      var id_of_fixed_block_row   = jQuery(this)[0].id;
      
      last_class_name_fixed_block = id_of_fixed_block_row;
      var res           = id_of_fixed_block_row.replace("bkap_fixed_block_row_", "");
      
      if( res == id_of_fixed_block_row && each_fixed_block.length == 0){
        each_fixed_block[i_fixed_bock] = 0;
      }else{
        each_fixed_block[i_fixed_bock] = parseInt(res);
      }
      i_fixed_bock++;
    
    });
    
    if( each_fixed_block.length == 0 ){
      var new_id_fixed_block = 0;
      //each_fixed_block[i_fixed_bock] = 0;
    }else{
      if( isNaN( each_fixed_block[0] ) ){
        each_fixed_block.shift();
      }
      
      var max_fixed_block = Math.max.apply(Math,each_fixed_block);
      var new_id_fixed_block = max_fixed_block+1;
      
    }
    
    
    // This will written complete tr element.
    var time_tr = jQuery("#bkap_default_fixed_block_row")[0].outerHTML; // This will written complete tr element.
    // Removing style and giving new id to the tr
    var new_time_tr = time_tr.replace("id=\"bkap_default_fixed_block_row\" style=\"display: none;\"", "id=\"bkap_fixed_block_row_"+new_id_fixed_block+"\"");
    
    // Changing the id of elements with new one.
    new_time_tr = new_time_tr.replace("id=\"booking_block_name\"",  "id=\"booking_block_name_"+new_id_fixed_block+"\"");
    new_time_tr = new_time_tr.replace("id=\"number_of_days\"",    "id=\"number_of_days_"+new_id_fixed_block+"\"");
    new_time_tr = new_time_tr.replace("id=\"start_day\"",       "id=\"start_day_"+new_id_fixed_block+"\"");
    new_time_tr = new_time_tr.replace("id=\"end_day\"",       "id=\"end_day_"+new_id_fixed_block+"\"");
    new_time_tr = new_time_tr.replace("id=\"fixed_block_price\"",   "id=\"fixed_block_price_"+new_id_fixed_block+"\"");
    new_time_tr = new_time_tr.replace("id=\"bkap_fixed_block_close\"",      "id=\"bkap_fixed_block_close_"+new_id_fixed_block+"\"" );
    
    var last_class_name_fixed_block_new = "#"+last_class_name_fixed_block;
    
    jQuery( last_class_name_fixed_block_new ).after(new_time_tr);
  });

  /*
  * This is to delete all fixed booking block.
  */
  
  jQuery( ".bkap_remove_all_fixed_blocks" ).on( "click" , function() {
    var y = confirm( bkap_block_pricing_params.confirm_delete_all_fixed_blocks );
    
    var available_fixed_block_row = jQuery( '#bkap_fixed_block_booking_table tr[id^="bkap_fixed_block_row_"]' ).length;
    
    if( y == true && available_fixed_block_row > 0 ) {
      
      var passed_id = bkap_settings_params.post_id;
      var data = {
        post_id: passed_id, 
        action: "bkap_delete_all_blocks"
      };
      
      /*jQuery.ajax({
                url: bkap_settings_params.ajax_url,
                type: "POST",
                data : data,
                beforeSend: function() {
                 //loading  

                },
                success: function( data, textStatus, xhr ) {
                    // Hide all the rows except the first one (column names)
                  jQuery( '#bkap_fixed_block_booking_table tr[id^="bkap_fixed_block_row_"]' ).each(function (i, row) {
                  
                  jQuery( this ).remove();
                });
                
                // add a fade out message which informs the admin that the settings have been saved
                  jQuery( '#block_pricing_update_notification' ).addClass( 'bkap_updated_notice' );
                  jQuery( '#block_pricing_update_notification' ).css( 'display', 'block' );
                  jQuery( '#block_pricing_update_notification' ).html( bkap_block_pricing_params.delete_all_fixed_blocks ).fadeOut( 10000 );
                },
                error: function( xhr, textStatus, errorThrown ) {
                  // error status
                }
            });*/
      
    }
    
  });
  
  // This is to delete a specific block.
  jQuery( "#bkap_fixed_block_booking_table" ).on( "click", "[id^=bkap_fixed_block_close]",  function() {
    var y = confirm( bkap_block_pricing_params.confirm_delete_fixed_block );
    
    var id = this.id;
    var split_data = id.split( '_' );
      var row_number = split_data[ 4 ];
      
      var block_name    = jQuery( '#booking_block_name_' + row_number ).val();
      var number_of_days  = jQuery( '#number_of_days_' + row_number ).val();
      var start_day     = jQuery( '#start_day_' + row_number ).val();
      var end_day     = jQuery( '#end_day_' + row_number ).val();
      var block_price   = jQuery( '#fixed_block_price_' + row_number ).val();
      

      var fixed_block_row_data = block_name+"&&"+number_of_days+"&&"+start_day+"&&"+end_day+"&&"+block_price;
    
    
    if( y == true ) {
      
      var passed_id = bkap_settings_params.post_id;
      var data = {
        post_id: passed_id,
        fixed_block_key : row_number,
        action: "bkap_delete_block"
      };
      
      /*jQuery.ajax({
                url: bkap_settings_params.ajax_url,
                type: "POST",
                data : data,
                beforeSend: function() {
                 //loading  

                },
                success: function( data, textStatus, xhr ) {
                    // Hide all the rows except the first one (column names)
                  //jQuery( '#bkap_fixed_block_booking_table tr[id^="bkap_fixed_block_row_"]' ).each(function (i, row) {
                    jQuery( '#bkap_fixed_block_row_' + row_number ).remove();
                  //jQuery( this ).remove();
                //});
                
                // add a fade out message which informs the admin that the settings have been saved
                  jQuery( '#block_pricing_update_notification' ).addClass( 'bkap_updated_notice' );
                  jQuery( '#block_pricing_update_notification' ).css( 'display', 'block' );
                  jQuery( '#block_pricing_update_notification' ).html( bkap_block_pricing_params.delete_fixed_block ).fadeOut( 10000 );
                },
                error: function( xhr, textStatus, errorThrown ) {
                  // error status
                }
            });*/
      
    }
  });
  
  
  /*
  * This is to delete all fixed booking block.
  */
  
  jQuery( ".bkap_remove_all_price_ranges" ).on( "click" , function() {
    var y = confirm( bkap_block_pricing_params.confirm_delete_all_price_ranges );
    
    var available_price_range_row = jQuery( '#bkap_price_range_booking_table tr[id^="bkap_price_range_row_"]' ).length;
    
    if( y == true && available_price_range_row > 0 ) {
      
      var passed_id = bkap_settings_params.post_id;
      var data = {
        post_id: passed_id, 
        action: "bkap_delete_all_ranges"
      };
      
      /*jQuery.ajax({
                url: bkap_settings_params.ajax_url,
                type: "POST",
                data : data,
                beforeSend: function() {
                 //loading  

                },
                success: function( data, textStatus, xhr ) {
                    // Hide all the rows except the first one (column names)
                  jQuery( '#bkap_price_range_booking_table tr[id^="bkap_price_range_row_"]' ).each(function (i, row) {
                  
                  jQuery( this ).remove();
                });
                
                // add a fade out message which informs the admin that the settings have been saved
                  jQuery( '#block_pricing_update_notification' ).addClass( 'bkap_updated_notice' );
                  jQuery( '#block_pricing_update_notification' ).css( 'display', 'block' );
                  jQuery( '#block_pricing_update_notification' ).html( bkap_block_pricing_params.delete_all_price_ranges ).fadeOut( 10000 );
                },
                error: function( xhr, textStatus, errorThrown ) {
                  // error status
                }
            });*/
      
    }
    
  });
  
  
  
  // This is to delete a specific price rnage.
  jQuery( "#bkap_price_range_booking_table" ).on( "click", "[id^=bkap_price_range_close]",  function() {
    var y = confirm( bkap_block_pricing_params.confirm_delete_price_range );
    
    if( y == true ) {
      
      var id = this.id;
      var split_data = id.split( '_' );
        var row_number = split_data[ 4 ];
      
      var passed_id = bkap_settings_params.post_id;
      var data = {
        post_id: passed_id,
        price_range_key : row_number,
        action: "bkap_delete_range"
      };
      
      /*jQuery.ajax({
                url: bkap_settings_params.ajax_url,
                type: "POST",
                data : data,
                beforeSend: function() {
                 //loading  

                },
                success: function( data, textStatus, xhr ) {
                    // Hide all the rows except the first one (column names)
                  //jQuery( '#bkap_fixed_block_booking_table tr[id^="bkap_fixed_block_row_"]' ).each(function (i, row) {
                    jQuery( '#bkap_price_range_row_' + row_number ).remove();
                  //jQuery( this ).remove();
                //});
                
                // add a fade out message which informs the admin that the settings have been saved
                  jQuery( '#block_pricing_update_notification' ).addClass( 'bkap_updated_notice' );
                  jQuery( '#block_pricing_update_notification' ).css( 'display', 'block' );
                  jQuery( '#block_pricing_update_notification' ).html( bkap_block_pricing_params.delete_price_range ).fadeOut( 10000 );
                },
                error: function( xhr, textStatus, errorThrown ) {
                  // error status
                }
            });*/
      
    }
  });
  
  // Clear Selection of the Block Pricing option.
  
  jQuery( document ).on( 'click', '.bkap_clear_block_pricing_selection', function() {
    jQuery( '[name="bkap_enable_block_pricing_type"]' ).removeAttr('checked');
    
    
    jQuery( '[name="bkap_enable_block_pricing_type"]' ).removeAttr('checked');
    jQuery('.bkap_fixed_block_booking').addClass( 'bkap_disable_block_pricing' );
    jQuery('.bkap_price_range_booking').addClass( 'bkap_disable_block_pricing' );
    
    
    
    // setup the data
      var data = {
              product_id: bkap_settings_params.post_id,
              action: 'bkap_block_pricing_options'
      };
              
      //jQuery.post( bkap_settings_params.ajax_url, data, function(response) { 
      //});
      
    return false;
  });
  
// This will add new row in Fixed Block Booking table
  
  jQuery( ".bkap_add_new_price_range" ).on( "click" , function() {
    
    var attribute_count = 0; 
    
    // Calculating new ID to assign new tr.
    jQuery( "tr#bkap_default_price_range_row > td select[id^='attribute_']" ).each( function(){
      attribute_count++;
    });
    
    var each_price_range      = new Array();
    var i_price_range         = 0;
    var last_class_name_price_range = "bkap_default_price_range_row";
    
    // Calculating new ID to assign new tr.
    jQuery( "tr[id^='bkap_price_range_row']" ).each( function(){
      
      var id_of_price_range_row   = jQuery(this)[0].id;
      
      last_class_name_price_range = id_of_price_range_row;
      var res           = id_of_price_range_row.replace("bkap_price_range_row_", "");
      
      if( res == id_of_price_range_row && each_price_range.length == 0){
        each_price_range[i_price_range] = 0;
      }else{
        each_price_range[i_price_range] = parseInt(res);
      }
      i_price_range++;
    
    });
    
    if( each_price_range.length == 0 ){
      var new_id_price_range = 0;
    }else{
      if( isNaN( each_price_range[0] ) ){
        each_price_range.shift();
      }
      
      var max_price_range = Math.max.apply(Math,each_price_range);
      var new_id_price_range = max_price_range+1;
    }
    
    // This will return complete tr element.
    var price_range_tr = jQuery("#bkap_default_price_range_row")[0].outerHTML; // This will written complete tr element.
    
    // Removing style and giving new id to the tr
    var new_time_tr = price_range_tr.replace("id=\"bkap_default_price_range_row\" style=\"display: none;\"", "id=\"bkap_price_range_row_"+new_id_price_range+"\"");
    
    // Changing the id of elements with new one.
    
    if( attribute_count > 0 ) {
      for( i=1; i <= attribute_count; i++ ){
        new_time_tr = new_time_tr.replace("id=\"attribute_"+i+"\" value=\"\"",  "id=\"attribute_"+i+"_"+new_id_price_range+"\"");
      }
    }
    
    new_time_tr = new_time_tr.replace("id=\"number_of_start_days\"",  "id=\"number_of_start_days_"+new_id_price_range+"\"");
    new_time_tr = new_time_tr.replace("id=\"number_of_end_days\"",    "id=\"number_of_end_days_"+new_id_price_range+"\"");
    new_time_tr = new_time_tr.replace("id=\"per_day_price\"",       "id=\"per_day_price_"+new_id_price_range+"\"");
    new_time_tr = new_time_tr.replace("id=\"end_day\"",         "id=\"end_day_"+new_id_price_range+"\"");
    new_time_tr = new_time_tr.replace("id=\"fixed_price\"",       "id=\"fixed_price_"+new_id_price_range+"\"");
    new_time_tr = new_time_tr.replace("id=\"bkap_price_range_close\"",  "id=\"bkap_price_range_close_"+new_id_price_range+"\"" );
    
    var last_class_name_price_range_new = "#"+last_class_name_price_range;
    
    jQuery( last_class_name_price_range_new ).after(new_time_tr);
  });
  
  /*
  * This is to change the From(Start Date) and To(End Date) field based on the selected option in the Range type option.
  */
  
  jQuery( ".specific" ).on( "change", "select[id^=range_dropdown]",  function() {
    
    var select_element_id     = jQuery(this)[0].id;
    var select_element_id_name  = "#"+select_element_id;
    
    var id_name         = jQuery(select_element_id_name).closest("tr").prop("class");
    var id            = "."+id_name;
    
    var tr_element        = jQuery(id);
    
    switch ( this.value )
    {
       case "specific_dates":
      var added_specific_date_range_row = jQuery(".added_specific_date_range_row");
      jQuery( id ).find( ".date_selection_textbox1" ).css( "display", "none" );
      jQuery( id ).find( ".date_selection_textbox2" ).css( "display", "none" );
      jQuery( id ).find( ".date_selection_textbox3" ).removeAttr( "style" ); // enabling specidif
      jQuery( id ).find( ".date_selection_textbox4" ).css( "display", "none" );
      jQuery( id ).find( ".date_selection_textbox5" ).css( "display", "none" );
      jQuery( id ).find( ".date_selection_textbox6" ).css( "display", "none" );
      jQuery( id ).find( ".bkap_lockout_column_data_1" ).css( "display", "none" );
      jQuery( id ).find( ".bkap_lockout_column_data_2" ).css( "display", "none" );
      jQuery( id ).find( ".bkap_lockout_column_data_3" ).css( "display", "none" );
      
      jQuery( id ).find( ".bkap_lockout_column_data_4" ).removeAttr( "style" ); 
      
          break;
       case "holidays":
            var added_specific_date_range_row = jQuery(".added_specific_date_range_row");
      jQuery( id ).find( ".date_selection_textbox1" ).css( "display", "none" );
      jQuery( id ).find( ".date_selection_textbox2" ).css( "display", "none" );
      jQuery( id ).find( ".date_selection_textbox3" ).css( "display", "none" );
      jQuery( id ).find( ".date_selection_textbox4" ).css( "display", "none" );
      jQuery( id ).find( ".date_selection_textbox6" ).removeAttr( "style" );
      jQuery( id ).find( ".date_selection_textbox5" ).css( "display", "none" );
      
      jQuery( id ).find( ".bkap_lockout_column_data_1" ).css( "display", "none" );
      jQuery( id ).find( ".bkap_lockout_column_data_2" ).removeAttr( "style" );
      jQuery( id ).find( ".bkap_lockout_column_data_3" ).css( "display", "none" );
      jQuery( id ).find( ".bkap_lockout_column_data_4" ).css( "display", "none" ); 
      
      
           break;
       case "custom_range":
          var added_specific_date_range_row = jQuery(".added_specific_date_range_row");
      
      jQuery( id ).find( ".date_selection_textbox1" ).removeAttr( "style" );
      jQuery( id ).find( ".date_selection_textbox2" ).removeAttr( "style" );
      jQuery( id ).find( ".date_selection_textbox3" ).css( "display", "none" );
      jQuery( id ).find( ".date_selection_textbox4" ).css( "display", "none" );
      jQuery( id ).find( ".date_selection_textbox5" ).css( "display", "none" );
      jQuery( id ).find( ".date_selection_textbox6" ).css( "display", "none" );
      
      jQuery( id ).find( ".bkap_lockout_column_data_1" ).removeAttr( "style" );
      jQuery( id ).find( ".bkap_lockout_column_data_2" ).css( "display", "none" );
      jQuery( id ).find( ".bkap_lockout_column_data_3" ).css( "display", "none" );
      jQuery( id ).find( ".bkap_lockout_column_data_4" ).css( "display", "none" ); 
      
      break;
     case "range_of_months":
        var added_specific_date_range_row = jQuery(".added_specific_date_range_row");
      jQuery( id ).find( ".date_selection_textbox1" ).css( "display", "none" );
      jQuery( id ).find( ".date_selection_textbox2" ).css( "display", "none" );
      jQuery( id ).find( ".date_selection_textbox3" ).css( "display", "none" );
      jQuery( id ).find( ".date_selection_textbox4" ).removeAttr( "style" );
      jQuery( id ).find( ".date_selection_textbox5" ).removeAttr( "style" );
      jQuery( id ).find( ".date_selection_textbox6" ).css( "display", "none" );
      
      jQuery( id ).find( ".bkap_lockout_column_data_1" ).css( "display", "none" );
      jQuery( id ).find( ".bkap_lockout_column_data_2" ).css( "display", "none" );
      jQuery( id ).find( ".bkap_lockout_column_data_3" ).removeAttr( "style" );
      jQuery( id ).find( ".bkap_lockout_column_data_4" ).css( "display", "none" ); 
      
       break;
       default: 
           alert('Default case');
    }
  });
    
    
    // Enable and disable lockout and price fields based on enable/disable weekday.
    
    jQuery( document ).on( "click", "[id^=booking_weekday]" , function() {
    
      var week_id = lastChar = lock_id = weekday_id = "";
       week_id    = jQuery(this)[0].id;
       week_id        = "#"+week_id;
       
       lastChar   = week_id.slice(-1);
       
       lock_id    = "#weekday_lockout_"+lastChar;
      
      if ( jQuery( week_id ).prop("checked") == false){
        // enable the lockout field
        jQuery(lock_id).prop( "disabled", true );
      }else{
        // disable the lockout field
        jQuery( lock_id ).prop( "disabled", false );
      }
    });
    
    // IF multiple day is enable then show Fixed and Range field in the Booking meta box header.
      
  if ( jQuery( "#enable_booking_multiple_days" ).prop( "checked" ) == false){
    jQuery("#block_booking_price").css( "display", "none" );
    jQuery("#block_booking").css( "display", "none" );
  }else{
    jQuery("#block_booking_price").removeAttr( "style" );
    jQuery("#block_booking").removeAttr( "style" );
  }
  
  // Collapesing the tabs of Booking meta box.
  
  jQuery( document ).on( "click", "#bkap_collapse" , function() {
    
    var style_tab = jQuery(".z-tabs-nav").attr("style");
    
    if( style_tab.search("width: 6%") == -1 ){
      jQuery(".bkap_tab").css( "font-size", "0" );
      jQuery(".z-tabs.vertical.top-left > ul").css( "width", "6%" );
        jQuery("span.dashicons-admin-collapse").css( "transform", "rotate(180deg)" );
        
        jQuery(".bkap_tab .fa").css( "font-size", "12px" );
        jQuery("#bkap_collapse").css( "font-size", "0" );
    }else{
      jQuery(".z-tabs.vertical.top-left > ul").css( "width", "20%" );
        jQuery("span.dashicons-admin-collapse").css( "transform", "" );
        jQuery(".bkap_tab").css( "font-size", "12px" );
        jQuery("#bkap_collapse").css( "font-size", "12px" );
    }
    
    });
  
  
  // This will add multiple dates picker to the specific and holiday textarea.
    jQuery("#bkap_date_timeslot_table").on( "focus", "[id^=bkap_note_time]", function() {
    // Automatically increase textarea of Note in date/day and timeslot table.
      week_id   = jQuery(this)[0].id;
      var textarea = document.getElementById(week_id);
      var limit = 200;

      textarea.oninput = function() {
        textarea.style.height = "";
        textarea.style.height = Math.min(textarea.scrollHeight, 300) + "px";
      };
      
    });
    
    // To show message when bookable is enabled/disabled for Holidays and Specific Dates.
    
  jQuery( ".specific" ).on( "click", "[id^=bkap_bookable_nonbookable]",  function() {
  
    var id      = this.id;
    var row_number  = id.lastIndexOf("_");
    row_number    = id.substring(row_number + 1);

    var range_set   = jQuery( '#range_dropdown_' + row_number ).val();
    var record_type = range_set;
    var popup_id  = "bkap_myPopup_"+row_number
    
      var popup     = document.getElementById( popup_id );
    
    if( record_type == "holidays" ){
      jQuery( popup ).text("You can't set Holiday Dates as Bookable.");
      jQuery( popup ).addClass("bkap_show");
      setTimeout( function(){
          var selected_bookable_id = "#"+id;
          jQuery( selected_bookable_id ).attr( 'checked', false );
        }, 500);
        setTimeout( function(){ jQuery( popup ).removeClass("bkap_show"); 
        jQuery( popup ).text("");
        }, 4000);
    }
    
    if( record_type == "specific_dates" ){
      var selected_specific_id = "#"+id;
      
      if ( jQuery( selected_specific_id ).prop("checked") === false){
        
        jQuery( popup ).text("All the selected Specific Dates will be considered as Holiday Dates if the bookable is switched to OFF");
        jQuery( popup ).addClass("bkap_show");
        
          setTimeout( function(){ jQuery( popup ).removeClass("bkap_show"); 
          jQuery( popup ).text("");
          }, 4000);
      }
    }
  });

  // If price is not specified, show a message that Booking box wont be displayed
  jQuery( '#booking_enable_date' ).change(function() {
    if ( this.checked && 
         jQuery( '#product-type' ).val() === 'simple' && 
         ( jQuery( '#_regular_price' ).val() === '' && jQuery( '#_sale_price' ).val() === '' ) ) {

      var msg_append = '<div class="booking_options-flex-child" id="bkap_price_message">'+
        '<p class="show-bkap-price-message">'+
        '<strong><i>Please note that Booking fields will not be displayed on front end until Price is added for the product</i></strong>'+
        '</p></div>';
      jQuery( '#enable_booking_options_section' ).after( msg_append );
    }else if( jQuery( '#bkap_price_message' ).length > 0 ){
      jQuery( '#bkap_price_message' ).remove();
    }
  });

  // Copy the booking setting when clicking on copy settings button

  jQuery( '.bkap_export_booking_link' ).click( function() {

    jQuery( '#bkap_product_status_txt' ).select();    
    document.execCommand( 'copy' );

    jQuery( "#bkap_myPopup" ).text( "Settings are copied!" );
    jQuery( "#bkap_myPopup" ).addClass( "bkap_show" );
  
    setTimeout( function(){ jQuery( "#bkap_myPopup" ).removeClass( "bkap_show" ); 
      jQuery( "#bkap_myPopup" ).text("");
    }, 2000);

    return false;
  });

});

//jQuery(document).ready(function () {
  /*jQuery("#bkap-tabbed-nav").zozoTabs({
      orientation: "vertical",
      position: "top-left",
      size: "medium",
      animation: {
        easing: "easeInOutExpo",
        duration: 400,
        effects: "none"
      },
  });*/
//});

/*
 * This code is to disable the lockout field if the weekdays are OFF. 
 * As well as to remove the classes added by the ZoZo tabs.
 * */

jQuery( window ).on( "load", function(){
      
  var all_zozo_classes = jQuery( "#bkap-tabbed-nav" ).attr( "class" ); 
  
  if ( all_zozo_classes != undefined ) {
    var new_all_zozo_classes =  all_zozo_classes.replace('z-shadows ','');
    var new_all_zozo_classes1 =  new_all_zozo_classes.replace('z-bordered ','');
    var new_all_zozo_classes2 =  new_all_zozo_classes1.replace('silver','');
    
    jQuery( "#bkap-tabbed-nav" ).removeClass();
    jQuery( "#bkap-tabbed-nav" ).addClass(new_all_zozo_classes2);
    
    
    var all_weekdays = jQuery('*[id^="week_day_"]');
      var all_lockout  = jQuery('*[id^="weekday_lockout_"]');
      
      for( var i = 0; i < all_weekdays.length; i++) {
        
        var d        = all_weekdays[i].id;
        var weekday_id = "#"+all_weekdays[i].id+"[checked]";
        var lockout_id = "#"+all_lockout[i].id;
        
        if ( jQuery(weekday_id).length  == 1 ){
          jQuery( lockout_id ).removeAttr( "disabled" );
        }
        
      }
  }
});
  
// Remove the classes of the ZoZo tab when screen is resized.
jQuery( window ).resize(function() {
  var all_zozo_classes = jQuery( "#bkap-tabbed-nav" ).attr( "class" ); 
  
  if (all_zozo_classes) {
  var new_all_zozo_classes =  all_zozo_classes.replace('z-shadows ','');
  var new_all_zozo_classes1 =  new_all_zozo_classes.replace('z-bordered ','');
  var new_all_zozo_classes2 =  new_all_zozo_classes1.replace('silver','');
  
  jQuery( "#bkap-tabbed-nav" ).removeClass();
  jQuery( "#bkap-tabbed-nav" ).addClass(new_all_zozo_classes2);
  }
  
});