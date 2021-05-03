<?php
/**
 * WCFM plugin views
 *
 * Plugin Tych Booking Products Manage Views
 *
 * @author 		WC Lovers
 * @package 	wcfmu/views/tych_bookings
 * @version   5.4.7
 */
global $wp, $WCFM, $WCFMu, $bkap_weekdays, $bkap_months, $bkap_dates_months_availability, $bkap_fixed_days;

$product_id = 0;

$bkap_resource = '';

$booking_type = '';
$enable_date              = '';
$booking_enable_type      = 'booking_enable_only_day';
$booking_enable_only_day  = 'booking_enable_single_day';
$booking_enable_date_time = '';

$enable_inline_calendar = '';
$date_show = '';
$without_date = '';
$requires_confirmation = '';

$min_days = 0;
$max_date = "30";
$readonly_no_of_dates_to_choose = "";
$lockout_date = "60";
$minimum_day_multiple = "0";
$maximum_day_multiple = "365";

$recurring_weekdays = array();
$special_prices     = array();
$recurring_lockout  = array();

$specific_date_checkbox = '';
$booking_type = $booking_custom_ranges = $booking_holiday_ranges = $booking_month_ranges = $booking_specific_dates = $booking_special_prices = $booking_product_holiday = array();

$number = 0;
$recurring_weekdays = array();
$specific_dates     = array();

$bkap_day_date = $bkap_from_time = $bkap_to_time = $bkap_lockout = $bkap_price = $bkap_global = $bkap_note = "";


$date_time_table = $duration_time_table = "display:none;";

$booking_times                      = array();
$bkap_encode_booking_times          = array();
$bkap_display_time_slots_pagination = 'display:none;';
$bkap_total_time_slots_number       = 1;
$bkap_total_pages                   = 0;
$bkap_per_page_time_slots           = absint( apply_filters( 'bkap_time_slots_per_page', 15 ) );

// Duration initialization of variable
$duration_label         = "";
$duration               = 1;
$duration_min           = 1;
$duration_max           = 1;
$duration_max_booking   = 0;
$duration_price         = "";
$first_duration         = "";
$end_duration           = "";
$duration_type          = "";

$duration_type_array = bkap_get_duration_types();

$bkap_fixed_blocks_check = "";
$bkap_price_ranges_check = "";
$bkap_enable_block_pricing_type = '';

$product_attributes  = '';
$width               = "";
$count_attributes    = 0;

if ( is_array( $product_attributes ) && count( $product_attributes ) > 0 ) {
	$count_attributes = count( $product_attributes );
}

$count_attributes += 4;
$available_width  = 90;

$width_size       = ($available_width/$count_attributes);
$width_size       = round($width_size, 2);
$width            = 'width="'.$width_size.'%"';

$product          = '';
$product_type 	  = 'simple';

$price_range_booking_data = array();


$resource_label             = '';
$resource_selection         = '';

$resource_args 				      = array( 'post_type'  => 'bkap_resource', 'posts_per_page'=> -1, 'post_status' => 'publish', );
$resource_args              = apply_filters( 'get_booking_resources_args', $resource_args );
$all_resources 				      = get_posts( $resource_args );
$resources_of_product 		  = array();
$resources_cost_of_product 	= array();

$currency_symbol = get_woocommerce_currency_symbol(); 

if( isset( $wp->query_vars['wcfm-products-manage'] ) && !empty( $wp->query_vars['wcfm-products-manage'] ) ) {
	$product_id = $wp->query_vars['wcfm-products-manage'];
	if( $product_id ) {
		
		$booking_settings   = get_post_meta( $product_id, 'woocommerce_booking_settings', true ); 
		$booking_type       = get_post_meta( $product_id, '_bkap_booking_type', true );
		$post_type          = get_post_type( $product_id );  
		
		$bkap_resource      = get_post_meta( $product_id, '_bkap_resource', true );
		
		// Genral
		if ( isset( $booking_settings[ 'booking_enable_date' ] ) && $booking_settings[ 'booking_enable_date' ] == 'on' ) {
														 
			$enable_date              = 'on';
			$booking_enable_type      = 'booking_enable_only_day';
			$booking_enable_only_day  = 'booking_enable_single_day';
			$booking_enable_date_time = '';
			 
			if( isset( $booking_settings[ 'booking_specific_booking' ]) && $booking_settings[ 'booking_specific_booking' ] == 'on' ){
				$specific_date_table = '';
			}   
		}

		switch ( $booking_type ) {
			case 'only_day':
				$booking_enable_type      = 'booking_enable_only_day';
				$booking_enable_only_day  = 'booking_enable_single_day';
				$booking_enable_date_time = '';
				break;
			case 'multiple_days':
				$booking_enable_type      = 'booking_enable_only_day';
				$booking_enable_only_day  = 'booking_enable_multiple_days';
				$booking_enable_date_time = '';
				break;
			case 'date_time':
				$booking_enable_type      = 'booking_enable_date_and_time';
				$booking_enable_only_day  = '';
				$booking_enable_date_time = 'booking_enable_fixed_time';
				break;
			case 'duration_time':
				$booking_enable_type      = 'booking_enable_date_and_time';
				$booking_enable_only_day  = '';
				$booking_enable_date_time = 'booking_enable_duration_time';
				break;
		}
		
		
		if( isset( $booking_settings[ 'enable_inline_calendar' ] ) && $booking_settings[ 'enable_inline_calendar' ] == 'on' ) {
			$enable_inline_calendar = 'on';
		}
		
		if( isset( $booking_settings['booking_purchase_without_date'] ) && $booking_settings['booking_purchase_without_date'] == 'on' ) {
		  $without_date = 'on';
		} else {
			$without_date = '';
		}
		
		if( isset( $booking_settings[ 'booking_confirmation' ] ) && 'on' == $booking_settings[ 'booking_confirmation' ] ) {
			$requires_confirmation = 'on';
		} else {
			$requires_confirmation = '';
		}
		
		// Availability
		if ( isset( $booking_settings['booking_minimum_number_days'] ) && $booking_settings['booking_minimum_number_days'] != "" ) {
			$min_days = $booking_settings['booking_minimum_number_days'];
		}
		
		if( isset( $booking_settings[ 'booking_date_range' ] ) && $booking_settings[ 'booking_date_range' ] != "" && count( $booking_settings[ 'booking_date_range' ]) > 0 ){
			$readonly_no_of_dates_to_choose = "readonly";
		}
		 
		if ( isset( $booking_settings[ 'booking_maximum_number_days' ] ) && $booking_settings[ 'booking_maximum_number_days' ] != "" ) {
			$max_date = $booking_settings[ 'booking_maximum_number_days' ];
		} else {
			$max_date = "30";
		}
		
		if ( isset( $booking_settings['booking_date_lockout'] ) ) {
		  $lockout_date = $booking_settings['booking_date_lockout'];
		  //sanitize_text_field( $lockout_date, true )
		} else {
		  $lockout_date = "60";
		}
		
		if ( isset( $booking_settings[ 'booking_minimum_number_days_multiple' ] ) && $booking_settings[ 'booking_minimum_number_days_multiple' ] != "" ) {
			$minimum_day_multiple = $booking_settings[ 'booking_minimum_number_days_multiple' ];
		} else {
			$minimum_day_multiple = "0";
		} 
		
		if ( isset( $booking_settings[ 'booking_maximum_number_days_multiple' ] ) && $booking_settings[ 'booking_maximum_number_days_multiple' ] != "" ) {
			$maximum_day_multiple = $booking_settings[ 'booking_maximum_number_days_multiple' ];
		} else {
			$maximum_day_multiple = "365";
		} 
		
		$recurring_weekdays     = ( isset( $booking_settings[ 'booking_recurring' ] ) ) ? $booking_settings[ 'booking_recurring' ] : array();
		$recurring_lockout      = ( isset( $booking_settings[ 'booking_recurring_lockout' ] ) ? $booking_settings[ 'booking_recurring_lockout' ] : array());
		$booking_special_prices = get_post_meta( $product_id, '_bkap_special_price', true );
		$special_prices         = array();
		
		/** Create a list of the special prices as day and price **/
		if ( is_array( $booking_special_prices ) && count( $booking_special_prices ) > 0 ) {
			foreach ( $booking_special_prices as $special_key => $special_value ) {
				$weekday_set = $special_value[ 'booking_special_weekday' ];
				if ( $weekday_set != "" ) {
					$special_prices[ $weekday_set ] = $special_value[ 'booking_special_price' ];
				} 
			}
		}
		
		if( isset( $booking_settings[ 'booking_specific_booking' ]) && $booking_settings[ 'booking_specific_booking' ] == 'on' ){
			$specific_date_checkbox = 'on';
		}
		
		
		// Fetching data from post meta.
		$booking_custom_ranges  = get_post_meta( $product_id, '_bkap_custom_ranges', true );
		$booking_holiday_ranges = get_post_meta( $product_id, '_bkap_holiday_ranges', true );
		$booking_month_ranges   = get_post_meta( $product_id, '_bkap_month_ranges', true );
		$booking_specific_dates = get_post_meta( $product_id, '_bkap_specific_dates', true );
		$booking_special_prices = get_post_meta( $product_id, '_bkap_special_price', true );
		$booking_product_holiday = isset( $booking_settings['booking_product_holiday'] ) ? $booking_settings['booking_product_holiday'] : "" ;

		// sorting holidays in chronological order.
		if ( is_array( $booking_product_holiday ) && count( $booking_product_holiday ) > 0 ) {
				uksort( $booking_product_holiday, 'bkap_orderby_date_key' );
		}
		
		// Calculating counts for ranges.
		$count_custom_ranges    = $booking_custom_ranges  != "" ? count( $booking_custom_ranges ) : 0;
		$count_holiday_ranges   = $booking_holiday_ranges != "" ? count( $booking_holiday_ranges ) : 0;
		$count_month_ranges     = $booking_month_ranges   != "" ? count( $booking_month_ranges ) : 0;
		$count_specific_dates   = $booking_specific_dates != "" ? count( $booking_specific_dates ) : 0;
		
		$count_special_prices   = $booking_special_prices != "" ? count( $booking_special_prices ) : 0;
		$count_product_holiday  = $booking_product_holiday != "" ? count( $booking_product_holiday ) : 0;
		
		$array_of_all_added_ranges  = array();
		$bkap_range_count           = 0;            

		if ( isset( $booking_custom_ranges ) && $count_custom_ranges > 0 ) {
			for ( $bkap_range = 0; $bkap_range < $count_custom_ranges; $bkap_range++ ) {
				$array_of_all_added_ranges[$bkap_range]['bkap_type']            = "custom_range";
				$array_of_all_added_ranges[$bkap_range]['bkap_start']           = $booking_custom_ranges[$bkap_range]['start'];
				$array_of_all_added_ranges[$bkap_range]['bkap_end']             = $booking_custom_ranges[$bkap_range]['end'];
				$array_of_all_added_ranges[$bkap_range]['bkap_years_to_recur']  = $booking_custom_ranges[$bkap_range]['years_to_recur'];
				$bkap_range_count++;
			}
		}
		
		if ( isset( $booking_product_holiday ) && $count_product_holiday > 0 ) {
			foreach ( $booking_product_holiday as  $booking_product_holiday_keys => $booking_product_holiday_values ) {
				$array_of_all_added_ranges[$bkap_range_count]['bkap_type']            = "holidays";
				$array_of_all_added_ranges[$bkap_range_count]['bkap_holiday_date']    = $booking_product_holiday_keys;
				$array_of_all_added_ranges[$bkap_range_count]['bkap_years_to_recur']  = $booking_product_holiday_values;
				$bkap_range_count++;
			}
		}
		
		if ( isset( $booking_month_ranges ) && $count_month_ranges > 0 ) {
			for ( $bkap_range = 0; $bkap_range < $count_month_ranges; $bkap_range++ ) {
				$array_of_all_added_ranges[$bkap_range_count]['bkap_type']            = "range_of_months";
				$array_of_all_added_ranges[$bkap_range_count]['bkap_start']           = $booking_month_ranges[$bkap_range]['start'];
				$array_of_all_added_ranges[$bkap_range_count]['bkap_end']             = $booking_month_ranges[$bkap_range]['end'];
				$array_of_all_added_ranges[$bkap_range_count]['bkap_years_to_recur']  = $booking_month_ranges[$bkap_range]['years_to_recur'];
				$bkap_range_count++;
			}
		}
		
		if ( isset( $booking_specific_dates ) && $count_specific_dates > 0 ) {
			foreach ( $booking_specific_dates as  $booking_specific_dates_keys => $booking_specific_dates_values ) {
				$array_of_all_added_ranges[$bkap_range_count]['bkap_type']            = "specific_dates";
				$array_of_all_added_ranges[$bkap_range_count]['bkap_specific_date']   = $booking_specific_dates_keys;
				$array_of_all_added_ranges[$bkap_range_count]['bkap_specific_lockout']= $booking_specific_dates_values;
				// check if that date has a special price set
				$array_of_all_added_ranges[ $bkap_range_count ][ 'bkap_specific_price' ] = ( isset( $special_prices[ $booking_specific_dates_keys ] ) ) ? $special_prices[ $booking_specific_dates_keys ] : '';
				$bkap_range_count++;
			}
		}

		// if the booking type is multiple day, then no data is present in specific dates, so loop through the special prices
		if ( 'multiple_days' == $booking_type ) {
			if ( is_array( $special_prices ) && count( $special_prices ) > 0 ) {
				foreach ( $special_prices as $sp_date => $sp_price ) {
					$array_of_all_added_ranges[$bkap_range_count]['bkap_type']            = "specific_dates";
					$array_of_all_added_ranges[$bkap_range_count]['bkap_specific_date']   = $sp_date;
					$array_of_all_added_ranges[$bkap_range_count]['bkap_specific_lockout']= '';
					$array_of_all_added_ranges[ $bkap_range_count ][ 'bkap_specific_price' ] = $sp_price;
					$bkap_range_count++;
				}
			}
		}
		
		if ( isset( $booking_holiday_ranges ) && $count_holiday_ranges > 0 ) {
			for ( $bkap_range = 0; $bkap_range < $count_holiday_ranges; $bkap_range++ ) {
				$bkap_holiday_from_month        = date('F',strtotime( $booking_holiday_ranges[$bkap_range]['start'] ) );
				$bkap_holiday_to_month          = date('F',strtotime( $booking_holiday_ranges[$bkap_range]['end'] ) );
				$holiday_start_date_of_month    = date('1-n-Y',strtotime( $booking_holiday_ranges[$bkap_range]['start'] ) );
				$holiday_end_date_of_month      = date('t-n-Y',strtotime( $booking_holiday_ranges[$bkap_range]['end'] ) );
				
				// Check if the start date is the start of the month and end date is the end date of the month then range type should be month range.
				if ( $booking_holiday_ranges[ $bkap_range ]['start'] == $holiday_start_date_of_month && $holiday_end_date_of_month == $booking_holiday_ranges[$bkap_range]['end'] ) {
					$array_of_all_added_ranges[$bkap_range_count]['bkap_type']           = ( isset( $booking_holiday_ranges[$bkap_range]['range_type'] ) ) ? $booking_holiday_ranges[$bkap_range]['range_type'] : "range_of_months";
					$array_of_all_added_ranges[$bkap_range_count]['bkap_start']          = $booking_holiday_ranges[$bkap_range]['start'];
					$array_of_all_added_ranges[$bkap_range_count]['bkap_end']            = $booking_holiday_ranges[$bkap_range]['end'];
					$array_of_all_added_ranges[$bkap_range_count]['bkap_years_to_recur'] = $booking_holiday_ranges[$bkap_range]['years_to_recur'];
					$array_of_all_added_ranges[$bkap_range_count]['bkap_bookable'] = "off";
				} else {
					$array_of_all_added_ranges[$bkap_range_count]['bkap_type']           = ( isset( $booking_holiday_ranges[$bkap_range]['range_type'] ) ) ? $booking_holiday_ranges[$bkap_range]['range_type'] : "custom_range";
					$array_of_all_added_ranges[$bkap_range_count]['bkap_start']          = $booking_holiday_ranges[$bkap_range]['start'];
					$array_of_all_added_ranges[$bkap_range_count]['bkap_end']            = $booking_holiday_ranges[$bkap_range]['end'];
					$array_of_all_added_ranges[$bkap_range_count]['bkap_years_to_recur'] = $booking_holiday_ranges[$bkap_range]['years_to_recur'];
					$array_of_all_added_ranges[$bkap_range_count]['bkap_bookable'] = "off";
				}

				$bkap_range_count++;
			}
		}
		
		$bookable           = bkap_common::bkap_get_bookable_status( $product_id );

		if ( $bookable && isset( $booking_settings[ 'booking_recurring' ] ) && count( $booking_settings[ 'booking_recurring' ] ) > 0 ) { // bookable product
			$recurring_weekdays = $booking_settings[ 'booking_recurring' ];
		} else if ( ! $bookable ) { // it's a new product
			foreach ( $bkap_weekdays as $day_name => $day_value ) {
				$recurring_weekdays[ $day_name ] = 'on'; // all weekdays are on by default
			}
		}
		
		if ( $bookable && isset( $booking_settings[ 'booking_specific_date' ] ) && count( $booking_settings[ 'booking_specific_date' ] ) > 0 ) {
			$specific_dates = $booking_settings[ 'booking_specific_date' ];
		}
		
		if( isset( $booking_settings[ 'booking_time_settings' ] ) && is_array( $booking_settings['booking_time_settings'] ) ) {
			$number = count( $booking_settings['booking_time_settings'] );
    }
		
		
		if ( isset( $booking_settings ['booking_enable_time'] ) ) {
			if ( $booking_settings ['booking_enable_time'] == "on" ) {
				$date_time_table = "";
			} elseif( $booking_settings ['booking_enable_time'] == "duration_time" ){                    
				$duration_time_table = "";
			}
		}

		if ( isset( $booking_settings['bkap_duration_settings'] ) && count( $booking_settings['bkap_duration_settings'] ) > 0 ) {
			$duration_settings = $booking_settings['bkap_duration_settings'];
			
			$duration_label         = $duration_settings['duration_label'];
			$duration               = $duration_settings['duration'];
			$duration_type          = $duration_settings['duration_type'];
			$duration_min           = $duration_settings['duration_min'];
			$duration_max           = $duration_settings['duration_max'];
			$duration_max_booking   = $duration_settings['duration_max_booking'];
			$duration_price         = $duration_settings['duration_price'];
			$first_duration         = $duration_settings['first_duration'];
			$end_duration           = $duration_settings['end_duration'];
		}           

		if ( isset( $booking_settings[ 'booking_time_settings' ] ) && is_array( $booking_settings['booking_time_settings'] ) ) {
				
				foreach ( $booking_settings['booking_time_settings'] as $bkap_weekday_key => $bkap_weekday_value ) {

					foreach ( $bkap_weekday_value as $day_key => $time_data  ) {
							
						$bkap_from_hr      = ( isset( $time_data['from_slot_hrs'] ) && !is_null( $time_data['from_slot_hrs'] ) ) ? $time_data['from_slot_hrs'] : "";
						$bkap_from_min     = ( isset( $time_data['from_slot_min'] ) && !is_null( $time_data['from_slot_min'] ) ) ? $time_data['from_slot_min'] : "";
						
						$bkap_from_time    = $bkap_from_hr.":".$bkap_from_min;
						 
						$bkap_to_hr        = ( isset( $time_data['to_slot_hrs'] ) && !is_null( $time_data['to_slot_hrs'] ) ) ? $time_data['to_slot_hrs'] : "";
						$bkap_to_min       = ( isset( $time_data['to_slot_min'] ) && !is_null( $time_data['to_slot_min'] ) ) ? $time_data['to_slot_min'] : "";
						
						$bkap_to_time      = ( $bkap_to_hr === '0' && $bkap_to_min === '00' ) ? '' : "$bkap_to_hr:$bkap_to_min";
						 
						$bkap_lockout      = ( isset( $time_data['lockout_slot'] ) && !is_null( $time_data['lockout_slot'] ) ) ? $time_data['lockout_slot'] : "";
						$bkap_price        = ( isset( $time_data['slot_price'] ) && !is_null( $time_data['slot_price'] ) ) ? $time_data['slot_price'] : "";
						 
						$bkap_global       = ( isset( $time_data['global_time_check'] ) && !is_null( $time_data['global_time_check'] ) ) ? $time_data['global_time_check'] : "";
						$bkap_note         = ( isset( $time_data['booking_notes'] ) && !is_null( $time_data['booking_notes'] ) ) ? $time_data['booking_notes'] : "";

						$booking_times[ $bkap_total_time_slots_number ] = array();
						$booking_times[ $bkap_total_time_slots_number ] [ 'day' ]               = $bkap_weekday_key;
						$booking_times[ $bkap_total_time_slots_number ] [ 'from_time' ]         = $bkap_from_time;
						$booking_times[ $bkap_total_time_slots_number ] [ 'to_time' ]           = $bkap_to_time;
						$booking_times[ $bkap_total_time_slots_number ] [ 'lockout_slot' ]      = $bkap_lockout;
						$booking_times[ $bkap_total_time_slots_number ] [ 'slot_price' ]        = $bkap_price;
						$booking_times[ $bkap_total_time_slots_number ] [ 'global_time_check' ] = $bkap_global;
						$booking_times[ $bkap_total_time_slots_number ] [ 'booking_notes' ]     = $bkap_note;

						$bkap_total_time_slots_number ++;
				}
			}
			
			if ( $bkap_total_time_slots_number > 1 ) {
				$bkap_display_time_slots_pagination = '';
				$bkap_total_time_slots_number--;
			}

			$bkap_total_pages           = ceil( $bkap_total_time_slots_number / $bkap_per_page_time_slots );                
			$bkap_encode_booking_times  = htmlspecialchars( json_encode ( $booking_times, JSON_FORCE_OBJECT ) );
		} else {

			/**
			 * When we add a new product we need to pass this array as a string so we are creating a json object string.
			 */

			$bkap_encode_booking_times = htmlspecialchars( json_encode ( $booking_times, JSON_FORCE_OBJECT ) );
		}
		
		// Block Cost
		$bkap_fixed_block_option     = get_post_meta( $product_id, '_bkap_fixed_blocks', true );
		$bkap_price_range_option     = get_post_meta( $product_id, '_bkap_price_ranges', true );
		
		if( isset( $bkap_fixed_block_option ) && $bkap_fixed_block_option != "" )
				$bkap_enable_block_pricing_type = "booking_fixed_block_enable";
				
		if( isset( $bkap_price_range_option ) && $bkap_price_range_option != "" )
				$bkap_enable_block_pricing_type = "booking_block_price_enable";
			
			
		$product_attributes  = get_post_meta( $product_id, '_product_attributes', true );
		$width               = "";
		$count_attributes    = 0;
							
		if ( is_array( $product_attributes ) && count( $product_attributes ) > 0 ) {
			$count_attributes = count( $product_attributes );
		}
		
		$count_attributes += 4;
		$available_width  = 90;
		
		$width_size       = ($available_width/$count_attributes);
		$width_size       = round($width_size, 2);
		$width            = 'width="'.$width_size.'%"';
		
		$product 		      = wc_get_product( $product_id );
		$product_type 	  = $product->get_type();
		
		
		$result      = get_post_meta( $product_id, '_bkap_price_range_data', true );
		$price_range_booking_data     = ( isset( $result ) && $result != "" ) ? $result : array();
		    
		
		
		$resource_label = Class_Bkap_Product_Resource::bkap_get_resource_label( $product_id );
		$resource_selection = Class_Bkap_Product_Resource::bkap_product_resource_selection( $product_id );
		
		$resources_of_product 		= Class_Bkap_Product_Resource::bkap_get_product_resources( $product_id );
		$resources_cost_of_product 	= Class_Bkap_Product_Resource::bkap_get_resource_costs( $product_id );
	}
}
?>

<!-- collapsible Booking 1 -->
<div class="page_collapsible products_manage_downloadable simple variable" id="wcfm_products_manage_form_booking_options_head"><label class="wcfmfa fa-calendar"></label><?php _e('Booking Options', 'wc-frontend-manager'); ?><span></span></div>
<div class="wcfm-container simple variable">
	<div id="wcfm_products_manage_form_downloadable_expander" class="wcfm-content">
		<?php
			$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_tych_boking_enable_fields', array(  
																				"booking_enable_date" => array('label' => __( 'Enable Booking', 'woocommerce-booking' ), 'type' => 'checkbox', 'hints' => __( 'Enable Booking Date on Products Page', 'woocommerce-booking' ), 'class' => 'wcfm-checkbox', 'label_class' => 'wcfm_title checkbox_title', 'value' => 'on', 'dfvalue' => $enable_date ),
																		), $product_id ) );
			
			$WCFM->wcfm_fields->wcfm_generate_form_field(  apply_filters( 'bkap_extra_options', array(
																				'_bkap_resource' => array( 'label' => __( 'Booking Resource', 'woocommerce-booking' ), 'type' => 'checkbox', 'class' => 'wcfm-checkbox', 'label_class' => 'wcfm_title checkbox_title', 'hints'   => __( 'Resource is your objects (like rooms, cars or tables) or services, what can be booked by visitors of your sites.', 'woocommerce-booking' ), 'value' => 'on', 'dfvalue' => $bkap_resource ),
																		) ) );
      
      $WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_tych_boking_general_fields', array(  
																				"booking_enable_type" => array( 'label' => __( 'Booking Type', 'woocommerce-booking' ), 'type' => 'select', 'options' => array( 'booking_enable_only_day' => __( 'Only Day', 'woocommerce-booking' ), 'booking_enable_date_and_time' => __( 'Date & Time', 'woocommerce-booking' ) ), 'class' => 'wcfm-select', 'label_class' => 'wcfm_title', 'value' => $booking_enable_type, 'hints' => __( 'Choose booking type for your business', 'woocommerce-booking' ) ),
																				"booking_enable_only_day" => array( 'label' => __( 'Specification', 'woocommerce-booking' ), 'type' => 'select', 'options' => array( 'booking_enable_single_day' => __( 'Single Day', 'woocommerce-booking' ), 'booking_enable_multiple_days' => __( 'Multiple Nights', 'woocommerce-booking' ) ), 'class' => 'wcfm-select booking_type_block booking_type_block_booking_enable_only_day', 'label_class' => 'wcfm_title booking_type_block booking_type_block_booking_enable_only_day', 'value' => $booking_enable_only_day ),
																				"booking_enable_date_time" => array( 'label' => __( 'Specification', 'woocommerce-booking' ), 'type' => 'select', 'options' => array( 'booking_enable_fixed_time' => __( 'Fixed Time', 'woocommerce-booking' ), 'booking_enable_duration_time' => __( 'Duration Based Time', 'woocommerce-booking' ) ), 'class' => 'wcfm-select booking_type_block booking_type_block_booking_enable_date_and_time', 'label_class' => 'wcfm_title booking_type_block booking_type_block_booking_enable_date_and_time', 'value' => $booking_enable_date_time ),
																				
																				"enable_inline_calendar" => array('label' => __( 'Enable Inline Calendar', 'woocommerce-booking' ), 'name' => 'enable_inline', 'type' => 'checkbox', 'class' => 'wcfm-checkbox', 'label_class' => 'wcfm_title checkbox_title', 'value' => 'on', 'dfvalue' => $enable_inline_calendar, 'hints' => __( 'Enable Inline Calendar on Products Page', 'woocommerce-booking' ) ),
																				"booking_purchase_without_date" => array('label' => __( 'Purchase without choosing a date', 'woocommerce-booking' ), 'name' => 'purchase_wo_date', 'type' => 'checkbox', 'class' => 'wcfm-checkbox', 'label_class' => 'wcfm_title checkbox_title', 'value' => 'on', 'dfvalue' => $without_date, 'hints' => __( 'Enables your customers to purchase without choosing a date. Select this option if you want the ADD TO CART button always visible on the product page. Cannot be applied to products that require confirmation.', 'woocommerce-booking' ) ),
																				"bkap_requires_confirmation" => array('label' => __( 'Requires Confirmation?', 'woocommerce-booking' ), 'name' => 'requires_confirmation', 'type' => 'checkbox', 'class' => 'wcfm-checkbox', 'label_class' => 'wcfm_title checkbox_title', 'value' => 'on', 'dfvalue' => $requires_confirmation, 'hints' => __( 'Enable this setting if the booking requires admin approval/confirmation. Payment will not be taken at Checkout', 'woocommerce-booking' ) ),
				
															      ), $product_id ) );
			
			$WCFM->wcfm_fields->wcfm_generate_form_field( array(  
																													"booking_booking_options"  => array( 'type' => 'hidden' ),
																													"booking_settings_data"    => array( 'type' => 'hidden' ),
																													"booking_gcal_data"        => array( 'type' => 'hidden' ),
																													"booking_ranges_enabled"   => array( 'type' => 'hidden' ),
																													"booking_blocks_enabled"   => array( 'type' => 'hidden' ),
																													"booking_fixed_block_data" => array( 'type' => 'hidden' ),
																													"booking_price_range_data" => array( 'type' => 'hidden' ),
																												 ) );
				
		
		?>
	</div>
</div>
<!-- end collapsible Booking -->
<div class="wcfm_clearfix"></div>

<!-- Collapsible Booking 2  -->
<div class="page_collapsible products_manage_availability simple variable" id="wcfm_products_manage_form_availability_head"><label class="wcfmfa fa-clock"></label><?php _e('Availability', 'woocommerce-bookings'); ?><span></span></div>
<div class="wcfm-container simple variable">
	<div id="wcfm_products_manage_form_availability_expander" class="wcfm-content">
		<?php
		$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_tych_boking_availability_fields', array(  
																				"booking_minimum_number_days" => array( 'label' => __( 'Advance Booking Period (in hours)', 'woocommerce-booking' ), 'name' => 'abp', 'type' => 'number', 'class' => 'wcfm-text', 'label_class' => 'wcfm_title', 'value' => $min_days, 'hints' => __( 'Enable Booking after X number of hours from the current time. The customer can select a booking date/time slot that is available only after the minimum hours that are entered here. For example, if you need 12 hours advance notice for a booking, enter 12 here.', 'woocommerce-booking' ), 'attributes' => array( 'min' => '0', 'step' => '1' ) ),
																				"booking_maximum_number_days" => array( 'label' => __( 'Number of dates to choose', 'woocommerce-booking' ), 'name' => 'max_bookable', 'type' => 'number', 'attributes' => array( 'min' => 1 ), 'class' => 'wcfm-text', 'label_class' => 'wcfm_title', 'value' => $max_date, 'hints' => __( 'The maximum number of booking dates you want to be available for your customers to choose from. For example, if you take only 2 months booking in advance, enter 60 here.', 'woocommerce-booking' ) ),
																				"booking_lockout_date" => array( 'label' => __( 'Maximum Bookings On Any Date', 'woocommerce-booking' ), 'name' => 'date_lockout', 'type' => 'number', 'class' => 'wcfm-text booking_type_sub_block booking_type_sub_block_booking_enable_multiple_days', 'label_class' => 'wcfm_title booking_type_sub_block booking_type_sub_block_booking_enable_multiple_days', 'value' => sanitize_text_field( $lockout_date, true ), 'hints' => __( 'Set this field if you want to place a limit on maximum bookings on any given date. If you can manage up to 15 bookings in a day, set this value to 15. Once 15 orders have been booked, then that date will not be available for further bookings.', 'woocommerce-booking' ) ),
																				"booking_minimum_number_days_multiple" => array( 'label' => __( 'Minimum number of nights to book', 'woocommerce-booking' ), 'name' => 'min_days_multiple', 'type' => 'number', 'class' => 'wcfm-text booking_type_sub_block booking_type_sub_block_booking_enable_multiple_days', 'label_class' => 'wcfm_title booking_type_sub_block booking_type_sub_block_booking_enable_multiple_days', 'value' => $minimum_day_multiple, 'hints' => __( 'The minimum number of booking days you want to book for multiple days booking. For example, if you take minimum 2 days of booking, add 2 in the field here.', 'woocommerce-booking' ) ),
																				"booking_maximum_number_days_multiple" => array( 'label' => __( 'Maximum number of nights to book', 'woocommerce-booking' ), 'name' => 'max_days_multiple', 'type' => 'number', 'class' => 'wcfm-text booking_type_sub_block booking_type_sub_block_booking_enable_multiple_days', 'label_class' => 'wcfm_title booking_type_sub_block booking_type_sub_block_booking_enable_multiple_days', 'value' => $maximum_day_multiple, 'hints' => __( 'The maximum number of booking days you want to book for multiple days booking. For example, if you take maximum 60 days of booking, add 60 in the field here.', 'woocommerce-booking' ) ),
																		), $product_id ) );
		
		include( 'products-manage/weekdays.php' );
		
		$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_tych_boking_availability_date_months_fields', array(  
																				    "specific_date_checkbox" => array( 'label' => __( 'Set Availability by Dates/Months', 'woocommerce-booking' ), 'name' => 'enable_specific', 'type' => 'checkbox', 'class' => 'wcfm-checkbox', 'label_class' => 'wcfm_title checkbox_title', 'value' => 'on', 'dfvalue' => $specific_date_checkbox ),
																				), $product_id ) );
		
		include( 'products-manage/availability_date_months.php' );
		
		include( 'products-manage/availability_time_slots.php' );
		
		include( 'products-manage/availability_datetime_slots.php' );
		
		?>
		
		
	</div>
</div>
<!-- end collapsible Booking -->
<div class="wcfm_clearfix"></div>

<!-- Collapsible Booking 3  -->
<div class="page_collapsible products_manage_costs simple variable booking_type_sub_block booking_type_sub_block_booking_enable_multiple_days" id="wcfm_products_manage_form_costs_head"><label class="wcfmfa fa-currency"><?php echo get_woocommerce_currency_symbol() ; ?></label><?php _e('Block Pricing', 'woocommerce-booking'); ?><span></span></div>
<div class="wcfm-container simple variable">
	<div id="wcfm_products_manage_form_costs_expander" class="wcfm-content">
		<?php
			$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_tych_boking_block_cost_fields', array(  
				  "bkap_enable_block_pricing_type" => array( 'label' => __( 'Block Pricing', 'woocommerce-booking' ), 'type' => 'select', 'options' => array( '' => __( '- Select Block Type -', 'wc-frontend-manager-ultimate' ), 'booking_fixed_block_enable' => __( 'Fixed Block Booking', 'woocommerce-booking' ), 'booking_block_price_enable' => __( 'Price By Range Of Nights', 'woocommerce-booking' ) ), 'class' => 'wcfm-select', 'label_class' => 'wcfm_title', 'value' => $bkap_enable_block_pricing_type, 'hints' => __( 'Select Fixed Block Booking option if you want customers to book or rent this product for fixed number of days. </br> Select Price By Range Of Nights option if you want to charge customers different prices for different day ranges.', 'woocommerce-booking' ) )
				) ) );
			
			include( 'products-manage/block-cost-fixed.php' );
			
			include( 'products-manage/block-cost-range.php' );
		?>
	</div>
</div>
<!-- end collapsible Booking -->
<div class="wcfm_clearfix"></div>

<!-- Collapsible Booking 4  -->
<div class="page_collapsible products_manage_resources resources simple variable" id="wcfm_products_manage_form_resources_head"><label class="wcfmfa fa-briefcase"></label><?php _e('Resources', 'woocommerce-bookings'); ?><span></span></div>
<div class="wcfm-container resources simple variable">
	<div id="wcfm_products_manage_form_resources_expander" class="wcfm-content">
		<?php
		$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_tych_boking_resource_fields', array(
					"bkap_product_resource_lable" => array( 'label' => __( 'Label', 'woocommerce-booking'), 'type' => 'text', 'class' => 'wcfm-text wcfm_ele simple variable', 'label_class' => 'wcfm_title simple variable', 'value' => $resource_label, 'hints' => __( 'Enter the name to be appear on the front end for selecting resource', 'woocommerce-booking' ) ),
					"bkap_product_resource_selection" => array( 'label' => __('Resources are', 'woocommerce-booking'), 'type' => 'select', 'options' => array( 'bkap_customer_resource' => __( "Customer Assigned", "woocommerce-booking" ), 'bkap_automatic_resource' => __( 'Automatically Assigned', "woocommerce-booking" ) ), 'class' => 'wcfm-select wcfm_ele simple variable', 'label_class' => 'wcfm_title simple variable', 'value' => $resource_selection, 'hints' => __( 'Customer selected will allow customer to choose resource on the front end booking form', 'woocommerce-booking' ) ),
																						) ) );
		?>
		
		<p style="padding:1%;" class="notice notice-info">
			<i><?php _e( 'Resources are used if you have multiple bookable items, e.g. room types, instructors or ticket types. Availability for resources is global across all bookable products.', 'woocommerce-booking' ); ?></i>
		</p>
		<div id="bkap_resource_section">
			
			<table class="bkap_resource_info">
				<tr>
					<th><?php echo __('Resource Title', 'woocommerce-booking'); ?></th>
					<th><?php echo __('Pricing', 'woocommerce-booking'); ?></th>
					<th id="bkap_remove_resource"><i class="fa fa-trash" aria-hidden="true"></i></th>
					<th>
						<a href="<?php echo admin_url( 'edit.php?post_type=bkap_resource' ); ?>" target="_blank">
							<i class="fa fa-external-link" aria-hidden="true"></i>
						</a>
					</th>
				</tr>
		
			<?php		
				$loop                 		= 0;
				
				if ( is_array($resources_of_product) && count( $resources_of_product ) > 0 ) {
					foreach ( $resources_of_product as $resource_id ) {
		
						if( get_post_status( $resource_id ) ) {
							$resource            = new BKAP_Product_Resource( $resource_id );
							$resource_base_cost  = isset( $resources_cost_of_product[ $resource_id ] ) ? $resources_cost_of_product[ $resource_id ] : '';
							include( BKAP_BOOKINGS_TEMPLATE_PATH . 'meta-boxes/html-bkap-resource.php' );
							$loop++;
						}
						
					}
				}
			?>
			</table>
		
			<div class="bkap_resource_add_section">
				
				<a href="<?php echo get_wcfm_tych_booking_resources_url(); ?>" target="_blank"><?php _e( 'All Resources', 'woocommerce-booking' ); ?></a>
		
				<button type="button" class="button button-primary bkap_add_resource wcfm_submit_button"><?php _e( 'Add/link Resource', 'woocommerce-booking' ); ?></button>
				<select name="add_resource_id" class="bkap_add_resource_id wcfm-select" style="width:150px;margin-top:10px;float:right;">
					<option value=""><?php _e( 'New resource', 'woocommerce-booking' ); ?></option>
					<?php
						if ( $all_resources ) {
							foreach ( $all_resources as $resource ) {
								echo '<option value="' . esc_attr( $resource->ID ) . '">#' . absint( $resource->ID ) . ' - ' . esc_html( $resource->post_title ) . '</option>';
							}
						}
					?>
				</select>
				
			</div>
		</div>
	</div>
</div>
<!-- end collapsible Booking -->
<div class="wcfm_clearfix"></div>