<?php
/**
 * WCFM plugin controllers
 *
 * Plugin Appointment Staffs Manage Controller
 *
 * @author 		WC Lovers
 * @package 	wcfmu/controllers
 * @version   2.4.0
 */

class WCFMu_WCAppointments_Staffs_Manage_Controller {
	
	public function __construct() {
		global $WCFM, $WCFMu;
		
		$this->processing();
	}
	
	public function processing() {
		global $WCFM, $WCFMu, $wpdb, $wcfm_staff_manager_form_data;
		
		$wcfm_staff_manager_form_data = array();
	  parse_str($_POST['wcfm_staffs_manage_form'], $wcfm_staff_manager_form_data);
	  
	  $wcfm_staff_messages = get_wcfm_staffs_manage_messages();
	  $has_error = false;
	  
	  if(isset($wcfm_staff_manager_form_data['user_name']) && !empty($wcfm_staff_manager_form_data['user_name'])) {
	  	if(isset($wcfm_staff_manager_form_data['user_email']) && !empty($wcfm_staff_manager_form_data['user_email'])) {
				$staff_id = 0;
				$is_update = false;
				$user_email = sanitize_email( $wcfm_staff_manager_form_data['user_email'] );
				if( isset($wcfm_staff_manager_form_data['staff_id']) && $wcfm_staff_manager_form_data['staff_id'] != 0 ) {
					$staff_id = absint( $wcfm_staff_manager_form_data['staff_id'] );
					$is_update = true;
				} else {
					if( username_exists( $wcfm_staff_manager_form_data['user_name'] ) ) {
						$has_error = true;
						echo '{"status": false, "message": "' . $wcfm_staff_messages['username_exists'] . '"}';
					} else {
						if( email_exists($user_email) == false ) {
							
						} else {
							$has_error = true;
							echo '{"status": false, "message": "' . $wcfm_staff_messages['email_exists'] . '"}';
						}
					}
				}
				
				if( !$has_error ) {
					$staff_user_role = apply_filters( 'wcfm_staff_user_role', 'shop_staff' );
					
					$user_data = array( 'user_login'     => $wcfm_staff_manager_form_data['user_name'],
															'user_email'     => $wcfm_staff_manager_form_data['user_email'],
															'display_name'   => $wcfm_staff_manager_form_data['user_name'],
															'nickname'       => $wcfm_staff_manager_form_data['user_name'],
															'first_name'     => $wcfm_staff_manager_form_data['first_name'],
															'last_name'      => $wcfm_staff_manager_form_data['last_name'],
															'user_pass'      => wp_generate_password( $length = 12, $include_standard_special_chars=false ),
															'role'           => $staff_user_role,
															'ID'             => $staff_id
															);
					if( $is_update ) {
						unset( $user_data['user_login'] );
						unset( $user_data['display_name'] );
						unset( $user_data['nickname'] );
						unset( $user_data['user_pass'] );
						unset( $user_data['role'] );
						$staff_id = wp_update_user( $user_data ) ;
					} else {
						$staff_id = wp_insert_user( $user_data ) ;
					}
				}
						
				if( !$staff_id ) {
					$has_error = true;
				} else {
					
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
																								"from"        => '',
																								"to"          => '',
																								"appointable" => '',
																								"qty"         => ''
																							);
					if( isset($wcfm_staff_manager_form_data['_wc_appointment_availability']) && !empty($wcfm_staff_manager_form_data['_wc_appointment_availability']) ) {
						foreach( $wcfm_staff_manager_form_data['_wc_appointment_availability'] as $availability_rule ) {
							$current_id = !empty( $availability_rule['avail_id'] ) ? intval( $availability_rule['avail_id'] ) : 0;
				
							$availability = new WC_Appointments_Availability( $current_id );
							$availability->set_ordering( $availability_rule_index );
							$availability->set_range_type( $availability_rule['type'] );
							$availability->set_kind( 'availability#staff' );
							$availability->set_kind_id( $staff_id );
							
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
						
					do_action( 'wcfm_staffs_manage', $staff_id );
				}
						
				if(!$has_error) { echo '{"status": true, "message": "' . $wcfm_staff_messages['staff_saved'] . '", "redirect": "' . get_wcfm_appointments_staffs_url() . '"}'; }
				else { echo '{"status": false, "message": "' . $wcfm_staff_messages['staff_failed'] . '"}'; }
						
			} else {
				echo '{"status": false, "message": "' . $wcfm_staff_messages['no_email'] . '"}';
			}
	  	
	  } else {
			echo '{"status": false, "message": "' . $wcfm_staff_messages['no_username'] . '"}';
		}
		
		die;
	}
}