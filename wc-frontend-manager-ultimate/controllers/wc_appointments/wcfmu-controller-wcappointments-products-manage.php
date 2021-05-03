<?php
/**
 * WCFM plugin controllers
 *
 * Plugin WC Appointments Products Manage Controller
 *
 * @author 		WC Lovers
 * @package 	wcfm/controllers
 * @version   2.4.0
 */

class WCFMu_WCAppointments_Products_Manage_Controller {
	
	public function __construct() {
		global $WCFM, $WCFMu;
		
		// Appointments Product Meta Data Save
    add_action( 'after_wcfm_products_manage_meta_save', array( &$this, 'wca_wcfm_products_manage_meta_save' ), 90, 2 );
	}
	
	/**
	 * WC Appointments Product Meta data save
	 */
	function wca_wcfm_products_manage_meta_save( $new_product_id, $wcfm_products_manage_form_data ) {
		global $wpdb, $WCFM, $WCFMu;
		
		$product_type = empty( $wcfm_products_manage_form_data['product_type'] ) ? WC_Product_Factory::get_product_type( $new_product_id ) : sanitize_title( stripslashes( $wcfm_products_manage_form_data['product_type'] ) );
		$classname    = WC_Product_Factory::get_product_classname( $new_product_id, $product_type ? $product_type : 'simple' );
		$product      = new $classname( $new_product_id );
		
		// Only set props if the product is a bookable product.
		if ( ! is_a( $product, 'WC_Product_Appointment' ) ) {
			return;
		}
		
		if( isset( $wcfm_products_manage_form_data['_wc_appointment_qty'] ) ) {
			
		}
		
		// Pricing Rules
		$cost_rule_index = 0;
		$cost_rules = array();
		$cost_default_rules = array(  "type"            => 'custom',
																	"from"            => '',
																	"to"              => '',
																	"base_modifier"   => '',
																	"base_cost"       => '',
																	"modifier"        => '',
																	"cost"            => ''
																);
		if( isset($wcfm_products_manage_form_data['_wc_appointment_cost_rules']) && !empty($wcfm_products_manage_form_data['_wc_appointment_cost_rules']) ) {
			foreach( $wcfm_products_manage_form_data['_wc_appointment_cost_rules'] as $cost_rule ) {
				$cost_rules[$cost_rule_index] = $cost_default_rules;
				$cost_rules[$cost_rule_index]['type'] = $cost_rule['type'];
				if( $cost_rule['type'] == 'custom' ) {
					$cost_rules[$cost_rule_index]['from'] = $cost_rule['from_custom'];
					$cost_rules[$cost_rule_index]['to']   = $cost_rule['to_custom'];
				} elseif( $cost_rule['type'] == 'months' ) {
					$cost_rules[$cost_rule_index]['from'] = $cost_rule['from_months'];
					$cost_rules[$cost_rule_index]['to']   = $cost_rule['to_months'];
				} elseif($cost_rule['type'] == 'weeks' ) {
					$cost_rules[$cost_rule_index]['from'] = $cost_rule['from_weeks'];
					$cost_rules[$cost_rule_index]['to']   = $cost_rule['to_weeks'];
				} elseif($cost_rule['type'] == 'days' ) {
					$cost_rules[$cost_rule_index]['from'] = $cost_rule['from_days'];
					$cost_rules[$cost_rule_index]['to']   = $cost_rule['to_days'];
				} elseif($cost_rule['type'] == 'quant' ) {
					$cost_rules[$cost_rule_index]['from'] = $cost_rule['from_count'];
					$cost_rules[$cost_rule_index]['to']   = $cost_rule['to_count'];
				} elseif($cost_rule['type'] == 'blocks' ) {
					$cost_rules[$cost_rule_index]['from'] = $cost_rule['from_count'];
					$cost_rules[$cost_rule_index]['to']   = $cost_rule['to_count'];
				} elseif($cost_rule['type'] == 'time:range' ) {
					$cost_rules[$cost_rule_index]['from_date'] = $cost_rule['from_custom'];
					$cost_rules[$cost_rule_index]['to_date']   = $cost_rule['to_custom'];
					$cost_rules[$cost_rule_index]['from'] = $cost_rule['from_time'];
					$cost_rules[$cost_rule_index]['to']   = $cost_rule['to_time'];
				} else {
					$cost_rules[$cost_rule_index]['from'] = $cost_rule['from_time'];
					$cost_rules[$cost_rule_index]['to']   = $cost_rule['to_time'];
				}
				if( $cost_rules[$cost_rule_index]['from'] && $cost_rules[$cost_rule_index]['to'] ) {
					$cost_rules[$cost_rule_index]['base_modifier'] = $cost_rule['base_modifier'];
					$cost_rules[$cost_rule_index]['base_cost'] = $cost_rule['base_cost'];
					$cost_rules[$cost_rule_index]['modifier'] = $cost_rule['block_modifier'];
					$cost_rules[$cost_rule_index]['cost'] = $cost_rule['block_cost'];
					$cost_rule_index++;
				} else {
					unset( $cost_rules[$cost_rule_index] );
				}
			}
		}
		
		// Remove Rules
		if(isset($_POST['removed_variations']) && !empty($_POST['removed_variations'])) {
			foreach($_POST['removed_variations'] as $removed_rule) {
				$availability_object = new WC_Appointments_Availability( $removed_rule );
				$availability_object->delete();
			}
		}
		
		// Availability Rules
		$availability_rule_index = 0;
		if( isset($wcfm_products_manage_form_data['_wc_appointment_availability_rules']) && !empty($wcfm_products_manage_form_data['_wc_appointment_availability_rules']) ) {
			foreach( $wcfm_products_manage_form_data['_wc_appointment_availability_rules'] as $availability_rule ) {
				$current_id = !empty( $availability_rule['avail_id'] ) ? intval( $availability_rule['avail_id'] ) : 0;
				
				$availability = new WC_Appointments_Availability( $current_id );
				$availability->set_ordering( $availability_rule_index );
				$availability->set_range_type( $availability_rule['type'] );
				$availability->set_kind( 'availability#product' );
				$availability->set_kind_id( $new_product_id );
				
				$availability->set_appointable( wc_clean( wp_unslash( $availability_rule['appointable'] ) ) );
				
				$availability->set_qty( sanitize_text_field( wp_unslash( $availability_rule['qty'] ) ) );
				
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
		
		// Staffs
		$staffs = array();
		if( isset($wcfm_products_manage_form_data['_wc_appointment_staffs']) && !empty($wcfm_products_manage_form_data['_wc_appointment_staffs']) ) {
			foreach( $wcfm_products_manage_form_data['_wc_appointment_staffs'] as $appointment_staffs ) {
				if( $appointment_staffs[ 'staff_id' ] ) {
					$staff_id = ( isset( $appointment_staffs['staff_id'] ) ) ? absint( $appointment_staffs['staff_id'] ) : 0;
					$staffs[ $staff_id ] = array(
						'base_cost'  => wc_clean( $appointment_staffs[ 'staff_base_cost' ] ),
						'qty'        => wc_clean( $appointment_staffs[ 'staff_qty' ] ),
					);
				}
			}
		}
		
		$errors = $product->set_props( apply_filters( 'wcfm_appointment_data_factory', array(
			'has_price_label' 		     => isset( $wcfm_products_manage_form_data['_wc_appointment_has_price_label'] ),
			'price_label'					     => wc_clean( $wcfm_products_manage_form_data['_wc_appointment_price_label'] ),
			'has_pricing'	 				     => isset( $wcfm_products_manage_form_data['_wc_appointment_has_pricing'] ),
			'pricing'						       => $cost_rules,
			'qty'							         => wc_clean( $wcfm_products_manage_form_data['_wc_appointment_qty'] ),
			'qty_min'						       => wc_clean( $wcfm_products_manage_form_data['_wc_appointment_qty_min'] ),
			'qty_max'						       => wc_clean( $wcfm_products_manage_form_data['_wc_appointment_qty_max'] ),
			'duration_unit'				     => wc_clean( $wcfm_products_manage_form_data['_wc_appointment_duration_unit'] ),
			'duration'						     => wc_clean( $wcfm_products_manage_form_data['_wc_appointment_duration'] ),
			'interval_unit'				     => wc_clean( $wcfm_products_manage_form_data['_wc_appointment_interval_unit'] ),
			'interval'						     => wc_clean( $wcfm_products_manage_form_data['_wc_appointment_interval'] ),
			'padding_duration_unit'    => wc_clean( $wcfm_products_manage_form_data['_wc_appointment_padding_duration_unit'] ),
			'padding_duration'		     => wc_clean( $wcfm_products_manage_form_data['_wc_appointment_padding_duration'] ),
			'min_date_unit'				     => wc_clean( $wcfm_products_manage_form_data['_wc_appointment_min_date_unit'] ),
			'min_date'						     => wc_clean( $wcfm_products_manage_form_data['_wc_appointment_min_date'] ),
			'max_date_unit'				     => wc_clean( $wcfm_products_manage_form_data['_wc_appointment_max_date_unit'] ),
			'max_date'						     => wc_clean( $wcfm_products_manage_form_data['_wc_appointment_max_date'] ),
			'user_can_cancel'			     => isset( $wcfm_products_manage_form_data['_wc_appointment_user_can_cancel'] ),
			'cancel_limit_unit'		     => wc_clean( $wcfm_products_manage_form_data['_wc_appointment_cancel_limit_unit'] ),
			'cancel_limit'				     => wc_clean( $wcfm_products_manage_form_data['_wc_appointment_cancel_limit'] ),
			'customer_timezones'			 => isset( $wcfm_products_manage_form_data['_wc_appointment_customer_timezones'] ),
			'cal_color'						     => '#0073aa', //wc_clean( $wcfm_products_manage_form_data['_wc_appointment_cal_color'] ),
			'requires_confirmation'	   => isset( $wcfm_products_manage_form_data['_wc_appointment_requires_confirmation'] ),
			'availability_span'  	     => wc_clean( $wcfm_products_manage_form_data['_wc_appointment_availability_span'] ),
			'availability_autoselect'  => isset( $wcfm_products_manage_form_data['_wc_appointment_availability_autoselect'] ),
			'has_restricted_days'     => isset( $wcfm_products_manage_form_data['_wc_appointment_has_restricted_days'] ),
			'restricted_days'         => isset( $wcfm_products_manage_form_data['_wc_appointment_restricted_days'] ) ? array_combine( wc_clean( $wcfm_products_manage_form_data['_wc_appointment_restricted_days'] ), wc_clean( $wcfm_products_manage_form_data['_wc_appointment_restricted_days'] ) ) : '',
			'staff_label'					     => wc_clean( $wcfm_products_manage_form_data['_wc_appointment_staff_label'] ),
			'staff_ids'						     => array_keys( $staffs ),
			'staff_base_costs'			   => wp_list_pluck( $staffs, 'base_cost' ),
			'staff_qtys'			         => wp_list_pluck( $staffs, 'qty' ),
			'staff_assignment'			   => wc_clean( $wcfm_products_manage_form_data['_wc_appointment_staff_assignment'] ),
		), $new_product_id, $product, $wcfm_products_manage_form_data ) );
		
		if ( is_wp_error( $errors ) ) {
			//echo '{"status": false, "message": "' . $errors->get_error_message() . '", "id": "' . $new_product_id . '", "redirect": "' . get_permalink( $new_product_id ) . '"}';
		}
		
		$product->save();
	}
	
}