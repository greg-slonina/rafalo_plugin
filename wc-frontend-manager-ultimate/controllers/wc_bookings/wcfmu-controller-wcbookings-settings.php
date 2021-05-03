<?php
/**
 * WCFM plugin controllers
 *
 * Plugin Booking Resources Manage Controller
 *
 * @author 		WC Lovers
 * @package 	wcfmu/controllers
 * @version   2.3.5
 */

class WCFMu_WCBookings_Settings_Controller {
	
	public function __construct() {
		global $WCFM, $WCFMu;
		
		$this->processing();
	}
	
	public function processing() {
		global $WCFM, $WCFMmp, $WCFMu, $wpdb, $wcfm_wcbookings_settings_form_data;
		
		$wcfm_wcbookings_settings_form_data = array();
	  parse_str($_POST['wcfm_wcbookings_settings_form'], $wcfm_wcbookings_settings_form_data);
	  
	  $wcfm_settings_messages = get_wcfm_wcappointments_settings_messages();
	  $has_error = false;
	  
		$availability_rule_index = 0;
		$availability_rules = array();
		$availability_default_rules = array(  "type"       => 'custom',
																					"from"       => '',
																					"to"         => '',
																					"bookable"   => '',
																					"priority"   => 10,
																					"av_id"      => '',
																					"vendor"     => ''
																				);
		if( isset($wcfm_wcbookings_settings_form_data['wc_global_booking_availability']) && !empty($wcfm_wcbookings_settings_form_data['wc_global_booking_availability']) ) {
			foreach( $wcfm_wcbookings_settings_form_data['wc_global_booking_availability'] as $availability_rule ) {
				$availability_rules[$availability_rule_index] = $availability_default_rules;
				$availability_rules[$availability_rule_index]['type'] = $availability_rule['type'];
				if( $availability_rule['type'] == 'custom' ) {
					$availability_rules[$availability_rule_index]['from'] = $availability_rule['from_custom'];
					$availability_rules[$availability_rule_index]['to']   = $availability_rule['to_custom'];
				} elseif( $availability_rule['type'] == 'months' ) {
					$availability_rules[$availability_rule_index]['from'] = $availability_rule['from_months'];
					$availability_rules[$availability_rule_index]['to']   = $availability_rule['to_months'];
				} elseif($availability_rule['type'] == 'weeks' ) {
					$availability_rules[$availability_rule_index]['from'] = $availability_rule['from_weeks'];
					$availability_rules[$availability_rule_index]['to']   = $availability_rule['to_weeks'];
				} elseif($availability_rule['type'] == 'days' ) {
					$availability_rules[$availability_rule_index]['from'] = $availability_rule['from_days'];
					$availability_rules[$availability_rule_index]['to']   = $availability_rule['to_days'];
				} elseif($availability_rule['type'] == 'time:range' ) {
					$availability_rules[$availability_rule_index]['from_date'] = $availability_rule['from_custom'];
					$availability_rules[$availability_rule_index]['to_date']   = $availability_rule['to_custom'];
					$availability_rules[$availability_rule_index]['from'] = $availability_rule['from_time'];
					$availability_rules[$availability_rule_index]['to']   = $availability_rule['to_time'];
				} else {
					$availability_rules[$availability_rule_index]['from'] = $availability_rule['from_time'];
					$availability_rules[$availability_rule_index]['to']   = $availability_rule['to_time'];
				}
				$availability_rules[$availability_rule_index]['title']    = $availability_rule['av_title'];
				$availability_rules[$availability_rule_index]['bookable'] = $availability_rule['bookable'];
				$availability_rules[$availability_rule_index]['priority'] = $availability_rule['priority'];
				$availability_rules[$availability_rule_index]['av_id']    = isset( $availability_rule['av_id'] ) ? $availability_rule['av_id'] : '';
				
				$availability_rule_index++;
			}
		}
		
		remove_all_filters( 'pre_option_wc_global_booking_availability' );
		remove_all_filters( 'pre_update_option_wc_global_booking_availability' );
		
		
		$availability_table_table = $wpdb->query( "SHOW tables like '{$wpdb->prefix}wc_bookings_availability'");
		if( $availability_table_table ) {
			
			
			// Deleting old Rules
			$vendor_id = 0;
			if( wcfm_is_vendor() && ( in_array( $WCFM->is_marketplace, array( 'wcfmmarketplace', 'wcpvendors' ) ) ) )  {
				if( $WCFM->is_marketplace == 'wcfmmarketplace' ) {
					$vendor_id = $WCFMmp->vendor_id;
				} elseif( $WCFM->is_marketplace == 'wcpvendor' ) {
					$vendor_id = (int) WC_Product_Vendors_Utils::get_logged_in_vendor();
				}
			}
			
			$global_availabilities = WC_Data_Store::load( 'booking-global-availability' )->get_all();
			
			if( $global_availabilities ) {
				if ( $vendor_id ) {
					// filter rules that belong to this vendor's product.
					$filtered_global_availabilities = array_filter(
						$global_availabilities,
						function ( WC_Global_Availability $availability ) use ( $vendor_id ) {
							return (int) $availability->get_meta( 'vendor_id' ) === (int) $vendor_id;
						}
					);
				} else {
					// filter rules that don't belong to any vendor.
					$filtered_global_availabilities = array_filter(
						$global_availabilities,
						function ( WC_Global_Availability $availability ) {
							return empty( $availability->get_meta( 'vendor_id' ) );
						}
					);
				}
				
				//print_r($filtered_global_availabilities);
				
				if( !empty( $filtered_global_availabilities ) ) {
					foreach( $filtered_global_availabilities as $a_index => $availability_rule ) {
						$wpdb->query( "DELETE FROM {$wpdb->prefix}wc_bookings_availability WHERE ID = " . $availability_rule->get_id() );
					}
				}
			}
			
			
			if ( ! empty( $availability_rules ) ) {
				$index = 0;
	
				foreach ( $availability_rules as $rule ) {
					$type       = ! empty( $rule['type'] ) ? $rule['type'] : '';
					$title      = ! empty( $rule['title'] ) ? $rule['title'] : '';
					$from_range = ! empty( $rule['from'] ) ? $rule['from'] : '';
					$to_range   = ! empty( $rule['to'] ) ? $rule['to'] : '';
					$from_date  = ! empty( $rule['from_date'] ) ? $rule['from_date'] : '';
					$to_date    = ! empty( $rule['to_date'] ) ? $rule['to_date'] : '';
					$bookable   = ! empty( $rule['bookable'] ) ? $rule['bookable'] : '';
					$priority   = ! empty( $rule['priority'] ) ? $rule['priority'] : '';

					$wpdb->insert(
						$wpdb->prefix . 'wc_bookings_availability',
						array(
							'gcal_event_id' => '',
							'title'         => $title,
							'range_type'    => $type,
							'from_range'    => $from_range,
							'to_range'      => $to_range,
							'from_date'     => $from_date,
							'to_date'       => $to_date,
							'bookable'      => $bookable,
							'priority'      => $priority,
							'ordering'      => $index,
							'date_created'  => current_time( 'mysql' ),
							'date_modified' => current_time( 'mysql' ),
						)
					);
					
					$availability = new WC_Global_Availability();
					$availability->set_id( $wpdb->insert_id );
					if( wcfm_is_vendor() ) {
						if( $vendor_id ) {
							$availability->add_meta_data( 'vendor_id', $vendor_id  );
						}
					}
					$availability->save();
					$availability->save_meta_data();
	
					$index++;
				}
				
				// The function incr_cache_prefix is deprecated in WooCommerce 3.9.
				if ( method_exists( 'WC_Cache_Helper', 'invalidate_cache_group' ) ) {
					WC_Cache_Helper::invalidate_cache_group( 'wc-bookings-availability' );
				} else {
					WC_Cache_Helper::incr_cache_prefix( 'wc-bookings-availability' );
				}
				WC_Bookings_Cache::delete_booking_slots_transient();
			}
		}
	
		if(!$has_error) { echo '{"status": true, "message": "' . $wcfm_settings_messages['settings_saved'] . '"}'; }
		else { echo '{"status": false, "message": "' . $wcfm_settings_messages['settings_failed'] . '"}'; }
		
		die;
	}
}