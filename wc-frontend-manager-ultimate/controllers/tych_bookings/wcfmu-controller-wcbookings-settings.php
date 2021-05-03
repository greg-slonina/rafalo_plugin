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
		global $WCFM, $WCFMu, $wpdb, $wcfm_wcbookings_settings_form_data;
		
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
				$availability_rules[$availability_rule_index]['bookable'] = $availability_rule['bookable'];
				$availability_rules[$availability_rule_index]['priority'] = $availability_rule['priority'];
				
				if( wcfm_is_vendor() && ( $WCFM->is_marketplace == 'wcpvendors' ) ) {
					$availability_rules[$availability_rule_index]['vendor'] = absint( WC_Product_Vendors_Utils::get_logged_in_vendor() );
				} else {
					$availability_rules[$availability_rule_index]['vendor'] = absint( $availability_rule['vendor'] );
				}
				$availability_rule_index++;
			}
		}
		
		remove_all_filters( 'pre_option_wc_global_booking_availability' );
		remove_all_filters( 'pre_update_option_wc_global_booking_availability' );
		if( wcfm_is_vendor() && ( $WCFM->is_marketplace == 'wcpvendors' ) ) {
			$old_values = get_option( 'wc_global_booking_availability', array() );
			$modified_old_values = array();

			foreach ( $old_values as $old_value ) {
				if ( ! empty( $old_value['vendor'] ) && (int) WC_Product_Vendors_Utils::get_logged_in_vendor() === $old_value['vendor'] ) {
					continue;
				}
				$modified_old_values[] = $old_value;
			}
			$availability_rules = array_merge( $availability_rules, $modified_old_values );
		}
		
		update_option( 'wc_global_booking_availability', $availability_rules );
	
		if(!$has_error) { echo '{"status": true, "message": "' . $wcfm_settings_messages['settings_saved'] . '"}'; }
		else { echo '{"status": false, "message": "' . $wcfm_settings_messages['settings_failed'] . '"}'; }
		
		die;
	}
}