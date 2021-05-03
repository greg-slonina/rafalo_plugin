<?php
/**
 * WCFM plugin controllers
 *
 * Plugin Appointment Settings Controller
 *
 * @author 		WC Lovers
 * @package 	wcfmu/controllers
 * @version   2.4.2
 */

class WCFMu_WCAppointments_Settings_Controller {
	
	public function __construct() {
		global $WCFM, $WCFMu;
		
		$this->processing();
	}
	
	public function processing() {
		global $WCFM, $WCFMu, $wpdb, $wcfm_wcappointments_settings_form_data;
		
		$wcfm_wcappointments_settings_form_data = array();
	  parse_str($_POST['wcfm_wcappointments_settings_form'], $wcfm_wcappointments_settings_form_data);
	  
	  $wcfm_staff_messages = get_wcfm_wcappointments_settings_messages();
	  $has_error = false;
	  
	  // Remove Rules
		if(isset($_POST['removed_rules']) && !empty($_POST['removed_rules'])) {
			foreach($_POST['removed_rules'] as $removed_rule) {
				$availability_object = new WC_Appointments_Availability( $removed_rule );
				$availability_object->delete();
			}
		}
	  
	  // Availability Rules
		$availability_rule_index = 0;
		$availability_rules = array();
		$availability_default_rules = array(  "type"        => 'custom',
																				  "avail_id"    => '',
			                                    "title"       => '',
																					"from"        => '',
																					"to"          => '',
																					"appointable" => '',
																					"priority"    => ''
																				);
	  
		if( isset($wcfm_wcappointments_settings_form_data['wc_global_appointment_availability']) && !empty($wcfm_wcappointments_settings_form_data['wc_global_appointment_availability']) ) {
			foreach( $wcfm_wcappointments_settings_form_data['wc_global_appointment_availability'] as $availability_rule ) {
				//$availability_rules[$availability_rule_index] = $availability_default_rules;
				//$availability_rules[$availability_rule_index]['type'] = $availability_rule['type'];
				
				$current_id = !empty( $availability_rule['avail_id'] ) ? intval( $availability_rule['avail_id'] ) : 0;
				
				$availability = new WC_Appointments_Availability( $current_id );
				$availability->set_ordering( $availability_rule_index );
				$availability->set_range_type( $availability_rule['type'] );
				$availability->set_kind( 'availability#global' );
				
				$availability->set_appointable( wc_clean( wp_unslash( $availability_rule['appointable'] ) ) );
				
				$availability->set_title( sanitize_text_field( wp_unslash( $availability_rule['title'] ) ) );
				
				$availability->set_priority( intval( $availability_rule['priority'] ) );
				
				
				if( $availability_rule['type'] == 'custom' ) {
					$availability->set_from_range( wc_clean( wp_unslash( $availability_rule['from_custom'] ) ) );
					$availability->set_to_range( wc_clean( wp_unslash( $availability_rule['to_custom'] ) ) );
				} elseif( $availability_rule['type'] == 'months' ) {
					$availability->set_from_range( wc_clean( wp_unslash( $availability_rule['from_months'] ) ) );
					$availability->set_to_range( wc_clean( wp_unslash( $availability_rule['to_months'] ) ) );
				} elseif($availability_rule['type'] == 'weeks' ) {
					$availability->set_from_range( wc_clean( wp_unslash( $availability_rule['from_weeks'] ) ) );
					$availability->set_to_range( wc_clean( wp_unslash( $availability_rule['to_weeks'] ) ) );
				} elseif($availability_rule['type'] == 'days' ) {
					$availability->set_from_range( wc_clean( wp_unslash( $availability_rule['from_days'] ) ) );
					$availability->set_to_range( wc_clean( wp_unslash( $availability_rule['to_days'] ) ) );
				} elseif($availability_rule['type'] == 'custom:daterange' ) {
					$availability->set_from_date( wc_clean( wp_unslash( $availability_rule['from_custom'] ) ) );
					$availability->set_to_date( wc_clean( wp_unslash( $availability_rule['to_custom'] ) ) );
					$availability->set_from_range( wc_appointment_sanitize_time( wp_unslash( $availability_rule['from_time'] ) ) );
					$availability->set_to_range( wc_appointment_sanitize_time( wp_unslash( $availability_rule['to_time'] ) ) );
				} elseif($availability_rule['type'] == 'time:range' ) {
					$availability->set_from_date( wc_clean( wp_unslash( $availability_rule['from_custom'] ) ) );
					$availability->set_to_date( wc_clean( wp_unslash( $availability_rule['to_custom'] ) ) );
					$availability->set_from_range( wc_appointment_sanitize_time( wp_unslash( $availability_rule['from_time'] ) ) );
					$availability->set_to_range( wc_appointment_sanitize_time( wp_unslash( $availability_rule['to_time'] ) ) );
				} else {
					$availability->set_from_range( wc_appointment_sanitize_time( wp_unslash( $availability_rule['from_time'] ) ) );
					$availability->set_to_range( wc_appointment_sanitize_time( wp_unslash( $availability_rule['to_time'] ) ) );
				}
				
				$availability->save();
				
				$availability_rule_index++;
			}
		}
			
		if(!$has_error) { echo '{"status": true, "message": "' . $wcfm_staff_messages['settings_saved'] . '"}'; }
		else { echo '{"status": false, "message": "' . $wcfm_staff_messages['settings_failed'] . '"}'; }
		
		die;
	}
}