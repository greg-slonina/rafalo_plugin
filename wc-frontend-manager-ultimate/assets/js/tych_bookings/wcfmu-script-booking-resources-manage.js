jQuery(document).ready(function($) {
	
	function wcfm_resources_manage_form_validate() {
		$is_valid = true;
		$('.wcfm-message').html('').removeClass('wcfm-error').slideUp();
		var title = $.trim($('#wcfm_resources_manage_form').find('#title').val());
		if(title.length == 0) {
			$is_valid = false;
			$('#wcfm_resources_manage_form .wcfm-message').html('<span class="wcicon-status-cancelled"></span>' + wcfm_resources_manage_messages.no_title).addClass('wcfm-error').slideDown();
			audio.play();
		}
		return $is_valid;
	}
	
	// Submit Resource
	$('#wcfm_resource_manager_submit_button').click(function(event) {
	  event.preventDefault();
	  
	  // Validations
	  $is_valid = wcfm_resources_manage_form_validate();
	  
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
				controller               : 'wcfm-booking-resources-manage',
				wcfm_resources_manage_form : $('#wcfm_resources_manage_form').serialize(),
				status                   : 'submit'
			}	
			$.post(wcfm_params.ajax_url, data, function(response) {
				if(response) {
					$response_json = $.parseJSON(response);
					if($response_json.redirect) {
						audio.play();
						$('#wcfm_resources_manage_form .wcfm-message').html('<span class="wcicon-status-completed"></span>' + $response_json.message).addClass('wcfm-success').slideDown( "slow", function() {
						  if( $response_json.redirect ) window.location = $response_json.redirect;	
						} );
					} else {
						audio.play();
						$('.wcfm-message').html('').removeClass('wcfm-success').slideUp();
						$('#wcfm_resources_manage_form .wcfm-message').html('<span class="wcicon-status-cancelled"></span>' + $response_json.message).addClass('wcfm-error').slideDown();
					}
					if($response_json.id) $('#resource_id').val($response_json.id);
					$('#wcfm-content').unblock();
				}
			});
		}
	});
	
});

/**
 * Edit Booking Class for manipulating data on modal pop-up
 * @namespace bkap_resource
 * @since 4.6.0
 */
jQuery( document ).ready( function ($) {

	/**
	 * Event for showing the saved resource details
	 *
	 * @fires event:click
	 * @since 4.6.0
	 */
	$( '#bkap_resource_availability, #bookings_pricing, .bookings_extension' ).on( 'change', '.wc_booking_availability_type select, .wc_booking_pricing_type select', function() {
		var value = $(this).val();
		var row   = $(this).closest('tr');		

		$(row).find('.from_date, .from_day_of_week, .from_month, .from_week, .from_time, .from').hide();
		$(row).find('.to_date, .to_day_of_week, .to_month, .to_week, .to_time, .to').hide();
		$( '.repeating-label' ).hide();
		$( '.bookings-datetime-select-to' ).removeClass( 'bookings-datetime-select-both' );
		$( '.bookings-datetime-select-from' ).removeClass( 'bookings-datetime-select-both' );
		$( '.bookings-to-label-row .bookings-datetimerange-second-label' ).hide();


		if ( value == 'custom' ) {
			$(row).find('.from_date, .to_date').show();
		}
		if ( value == 'months' ) {
			$(row).find('.from_month, .to_month').show();
		}
		if ( value == 'weeks' ) {
			$(row).find('.from_week, .to_week').show();
		}
		if ( value == 'days' ) {
			$(row).find('.from_day_of_week, .to_day_of_week').show();
		}
		if ( value.match( "^time" ) ) {
			$(row).find('.from_time, .to_time').show();
			// Show the date range as well if "time range for custom dates" is selected
			if ( 'time:range' === value ) {
				$(row).find('.from_date, .to_date').show();
				$( '.repeating-label' ).show();
				$( '.bookings-datetime-select-to' ).addClass( 'bookings-datetime-select-both' );
				$( '.bookings-datetime-select-from' ).addClass( 'bookings-datetime-select-both' );
				$( '.bookings-to-label-row .bookings-datetimerange-second-label' ).show();
			}
		}
		if ( value == 'persons' || value == 'duration' || value == 'blocks' ) {
			$(row).find('.from, .to').show();
		}
	});

	/**
	 * Event for adding rows to the resources table
	 *
	 * @fires event:bkap_row_added
	 * @since 4.6.0
	 */
	$('body').on('bkap_row_added', function(){

		$('.wc_booking_availability_type select, .wc_booking_pricing_type select').change();

		$( '.date-picker' ).datepicker({
			dateFormat: 'yy-mm-dd',
			minDate: 0,
			numberOfMonths: 1,
			showButtonPanel: true,
			showOn: 'button',
			buttonImage: bkap_resource_params.bkap_calendar,
  			buttonText: "Select Date",
			buttonImageOnly: true
		});
	});

	/**
	 * Callback Function when Edit Booking Button is clicked
	 *
	 * @function wc_bookings_trigger_change_events
	 * @return {bool} stop further propogation of event
	 * @since 4.6.0
	 */
	function wc_bookings_trigger_change_events() {
		$('.wc_booking_availability_type select, .wc_booking_pricing_type select, #_wc_booking_duration_type, #_wc_booking_user_can_cancel, #_wc_booking_duration_unit, #_wc_booking_has_persons, #_wc_booking_has_resources, #_wc_booking_has_person_types').change();
	}

	/**
	 * Event when add new row is clicked
	 *
	 * @fires event:click
	 * @since 4.6.0
	 */
	$( '.bkap_add_row_resource' ).click(function( e ){
			
		var newRowIndex = $(e.target).closest('table').find( '#pricing_rows tr' ).length;
		var newRow 		= $( this ).data( 'row' );
		newRow 			= newRow.replace( /bookings_cost_js_index_replace/ig, newRowIndex.toString() );
		
		$(this).closest('table').find('tbody').append( newRow);

		/**
		 * Indicates that the row is added
		 * 
		 * @event bkap_row_added
		 * @since 4.6.0
		 */
		$('body').trigger('bkap_row_added');
		return false;
	});

	/**
	 * Event when Checkbox is clicked on Availability Rows
	 *
	 * @fires event:click
	 * @since 4.6.0
	 */
	jQuery( "#availability_rows" ).on( 'click', '.bkap_checkbox', function( e ) {
		
		var bkap_checkbox = $( this ).parent();
		
		if ( $( e.target).prop("checked") == true ){
			$( bkap_checkbox ).find( ".bkap_hidden_checkbox" ).val("1");
		}else{
			$( bkap_checkbox ).find( ".bkap_hidden_checkbox" ).val("0");
		}
	});

	/**
	 * Event when Close Resource clicked
	 *
	 * @fires event:click
	 * @since 4.6.0
	 */
	jQuery('#availability_rows').on( 'click', '#bkap_close_resource', function( e ) {
		$(this).parent().remove();
	});

	$( '.date-picker' ).datepicker({
		dateFormat: 'yy-mm-dd',
		minDate: 0,
		numberOfMonths: 1,
		showButtonPanel: true,
		showOn: 'button',
		buttonImage: bkap_resource_params.bkap_calendar,
  		buttonText: "Select Date",
		buttonImageOnly: true
	});	

	wc_bookings_trigger_change_events();
});