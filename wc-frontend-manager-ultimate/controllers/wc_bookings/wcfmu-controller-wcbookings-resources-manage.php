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

class WCFMu_WCBookings_Resources_Manage_Controller {
	
	public function __construct() {
		global $WCFM, $WCFMu;
		
		$this->processing();
	}
	
	public function processing() {
		global $WCFM, $WCFMu, $wpdb, $wcfm_resource_manager_form_data;
		
		$wcfm_resource_manager_form_data = array();
	  parse_str($_POST['wcfm_resources_manage_form'], $wcfm_resource_manager_form_data);
	  
	  $wcfm_resource_messages = get_wcfm_resources_manage_messages();
	  $has_error = false;
	  
	  if(isset($wcfm_resource_manager_form_data['title']) && !empty($wcfm_resource_manager_form_data['title'])) {
	  	$resource_id = 0;
	  	if(isset($wcfm_resource_manager_form_data['resource_id']) && $wcfm_resource_manager_form_data['resource_id'] == 0) {
				$nresource = new WC_Product_Booking_Resource();
				$nresource->set_name( $wcfm_resource_manager_form_data['title'] );
				$resource_id = $nresource->save();
			} else { // For Update
				$resource_id = $wcfm_resource_manager_form_data['resource_id'];
				$resource = new WC_Product_Booking_Resource( $resource_id );
				$resource->set_name( $wcfm_resource_manager_form_data['title'] );
				$resource->save();
			}
			
			if( $resource_id ) {
				$qty = !empty($wcfm_resource_manager_form_data['qty']) ? $wcfm_resource_manager_form_data['qty'] : 1;
				update_post_meta( $resource_id, 'qty', $qty );
				
				$availability_rule_index = 0;
				$availability_rules = array();
				$availability_default_rules = array(  "type"       => 'custom',
																							"from"       => '',
																							"to"         => '',
																							"bookable"   => '',
																							"priority"   => 10
																						);
				if( isset($wcfm_resource_manager_form_data['_wc_booking_availability_rules']) && !empty($wcfm_resource_manager_form_data['_wc_booking_availability_rules']) ) {
					foreach( $wcfm_resource_manager_form_data['_wc_booking_availability_rules'] as $availability_rule ) {
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
						$availability_rule_index++;
					}
				}
				
				update_post_meta( $resource_id, '_wc_booking_availability', $availability_rules );
	  	} else {
	  		$has_error = true;
	  	}
	  	
	  	if(!$has_error) { echo '{"status": true, "message": "' . $wcfm_resource_messages['resource_published'] . '", "redirect": "' . get_wcfm_bookings_resources_url() . '"}'; }
	  	else { echo '{"status": false, "message": "' . $wcfm_resource_messages['resource_failed'] . '"}'; }
	  	
	  	
	  }  else {
			echo '{"status": false, "message": "' . $wcfm_resource_messages['no_title'] . '"}';
		}
		
		die;
	}
}