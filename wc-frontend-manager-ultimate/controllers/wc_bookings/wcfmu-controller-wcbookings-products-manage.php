<?php
/**
 * WCFMu plugin controllers
 *
 * Plugin WC Booking Products Manage Controller
 *
 * @author 		WC Lovers
 * @package 	wcfmu/controllers
 * @version   2.0.0
 */

class WCFMu_WCBookings_Products_Manage_Controller {
	
	public function __construct() {
		global $WCFM, $WCFMu;
		
		// Booking Product Meta Data Save
    add_filter( 'wcfm_booking_data_factory', array( &$this, 'wcfmu_booking_data_factory' ), 20, 4 );
    
    // Booking Resource and Person Manager
    add_filter( 'wcfm_booking_data_factory', array( &$this, 'wcfmu_booking_resource_person_data_factory' ), 30, 4 );
	}
	
	/**
	 * WC Booking Product Meta data save
	 */
	function wcfmu_booking_data_factory( $wcfm_booking_data, $new_product_id, $product, $wcfm_products_manage_form_data ) {
		global $wpdb, $WCFM, $WCFMu, $_POST;
		
		// Only set props if the product is a bookable product.
		$product_type = empty( $wcfm_products_manage_form_data['product_type'] ) ? WC_Product_Factory::get_product_type( $new_product_id ) : sanitize_title( stripslashes( $wcfm_products_manage_form_data['product_type'] ) );
		if ( 'booking' != $product_type ) {
			return;
		}
		
		// Availability Rules - 2.1.1
		$availability_rule_index = 0;
		$availability_rules = array();
		$availability_default_rules = array(  "type"       => 'custom',
																				  "from"       => '',
																				  "to"         => '',
																					"bookable"   => '',
																					"priority"   => 10
																				);
		if( isset($wcfm_products_manage_form_data['_wc_booking_availability_rules']) && !empty($wcfm_products_manage_form_data['_wc_booking_availability_rules']) ) {
			foreach( $wcfm_products_manage_form_data['_wc_booking_availability_rules'] as $availability_rule ) {
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
				if( $availability_rules[$availability_rule_index]['from'] && $availability_rules[$availability_rule_index]['to'] ) {
					$availability_rules[$availability_rule_index]['bookable'] = $availability_rule['bookable'];
					$availability_rules[$availability_rule_index]['priority'] = $availability_rule['priority'];
					$availability_rule_index++;
				} else {
					unset( $availability_rules[$availability_rule_index] );
				}
			}
		}
		
		// Cost Rules - 2.1.2
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
		if( isset($wcfm_products_manage_form_data['_wc_booking_cost_rules']) && !empty($wcfm_products_manage_form_data['_wc_booking_cost_rules']) ) {
			foreach( $wcfm_products_manage_form_data['_wc_booking_cost_rules'] as $cost_rule ) {
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
				} elseif($cost_rule['type'] == 'persons' ) {
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
		
		$wcfmu_booking_data = array(
																'check_start_block_only'     => 'start' === $wcfm_products_manage_form_data['_wc_booking_check_availability_against'],
																'has_restricted_days'        => isset( $wcfm_products_manage_form_data['_wc_booking_has_restricted_days'] ),
																'restricted_days'            => isset( $wcfm_products_manage_form_data['_wc_booking_restricted_days'] ) ? array_combine( wc_clean( $wcfm_products_manage_form_data['_wc_booking_restricted_days'] ), wc_clean( $wcfm_products_manage_form_data['_wc_booking_restricted_days'] ) ) : '',
																'first_block_time'           => wc_clean( $wcfm_products_manage_form_data['_wc_booking_first_block_time'] ),
																'availability'               => $availability_rules,
																'pricing'                    => $cost_rules,
															);
		
		$wcfm_booking_data = array_merge( $wcfm_booking_data, $wcfmu_booking_data );
		return $wcfm_booking_data;		
	}
	
	/**
	 * WC Booking Resource and Persons Manager
	 */
	function wcfmu_booking_resource_person_data_factory( $wcfm_booking_data, $new_product_id, $product, $wcfm_products_manage_form_data ) {
		global $wpdb, $WCFM, $WCFMu, $_POST;
		
		// Only set props if the product is a bookable product.
		$product_type = empty( $wcfm_products_manage_form_data['product_type'] ) ? WC_Product_Factory::get_product_type( $new_product_id ) : sanitize_title( stripslashes( $wcfm_products_manage_form_data['product_type'] ) );
		if ( 'booking' != $product_type ) {
			return;
		}
		
	  // Person Types
		$person_type_index = 0;
		$person_types = array();
		if( isset( $wcfm_products_manage_form_data['_wc_booking_has_persons'] ) && isset($wcfm_products_manage_form_data['_wc_booking_person_types']) && !empty($wcfm_products_manage_form_data['_wc_booking_person_types']) ) {
			foreach( $wcfm_products_manage_form_data['_wc_booking_person_types'] as $booking_person_types ) {
				$loop    = intval( $person_type_index );
		
				$person_type_id = ( isset( $booking_person_types['person_id'] ) ) ? $booking_person_types['person_id'] : 0;
				if( !$person_type_id ) {
					$person_type = new WC_Product_Booking_Person_Type();
					$person_type->set_parent_id( $product->get_id() );
					$person_type->set_sort_order( $loop );
					$person_type_id = $person_type->save();
				} else {
					$person_type = new WC_Product_Booking_Person_Type( $person_type_id );
				}
				
				$person_type->set_props( array(
					'name'        => wc_clean( stripslashes( $booking_person_types['person_name'] ) ),
					'description' => wc_clean( stripslashes( $booking_person_types['person_description'] ) ),
					'sort_order'  => absint( $person_type_index ),
					'cost'        => wc_clean( $booking_person_types['person_cost'] ),
					'block_cost'  => wc_clean( $booking_person_types['person_block_cost'] ),
					'min'         => wc_clean( $booking_person_types['person_min'] ),
					'max'         => wc_clean( $booking_person_types['person_max'] ),
					'parent_id'   => $product->get_id(),
				) );
				$person_types[] = $person_type;
				$person_type_index++;
			}
		}
		
		// Resources
		$resource_index = 0;
		$resources = array();
		if( isset( $wcfm_products_manage_form_data['_wc_booking_has_resources'] ) && isset($wcfm_products_manage_form_data['_wc_booking_resources']) && !empty($wcfm_products_manage_form_data['_wc_booking_resources']) ) {
			foreach( $wcfm_products_manage_form_data['_wc_booking_resources'] as $booking_resources ) {
				if( $booking_resources[ 'resource_title' ] ) {
					$loop    = intval( $resource_index );
					$resource_id = ( isset( $booking_resources['resource_id'] ) ) ? absint( $booking_resources['resource_id'] ) : 0;
					// Creating new Resource
					if( !$resource_id ) {
						$nresource = new WC_Product_Booking_Resource();
						$nresource->set_name( $booking_resources[ 'resource_title' ] );
						$resource_id = $nresource->save();
					}
					$resources[ $resource_id ] = array(
						'base_cost'  => wc_clean( $booking_resources[ 'resource_base_cost' ] ),
						'block_cost' => wc_clean( $booking_resources[ 'resource_block_cost' ] ),
					);
					
					// Resource Quantity
					if( isset( $booking_resources[ 'resource_quantity' ] ) ) {
						update_post_meta( $resource_id, 'qty', $booking_resources[ 'resource_quantity' ] );
					}
					
					$resource_index++;
					
					do_action( 'wcfm_after_booking_product_edit_resource_update', $resource_id, $booking_resources );
				}
			}
		}
		
		// Remove Deleted Person Types
		if(isset($_POST['removed_person_types']) && !empty($_POST['removed_person_types'])) {
			foreach($_POST['removed_person_types'] as $removed_person_type) {
				$person_type_id = intval( $removed_person_type );
				$person_type    = new WC_Product_Booking_Person_Type( $person_type_id );
				$person_type->delete();
			}
		}
		
		$wcfmu_booking_data = array(
																'has_person_cost_multiplier' => isset( $wcfm_products_manage_form_data['_wc_booking_person_cost_multiplier'] ),
																'has_person_qty_multiplier'  => isset( $wcfm_products_manage_form_data['_wc_booking_person_qty_multiplier'] ),
																'has_person_types'           => isset( $wcfm_products_manage_form_data['_wc_booking_has_person_types'] ),
																'has_persons'                => isset( $wcfm_products_manage_form_data['_wc_booking_has_persons'] ),
																'has_resources'              => isset( $wcfm_products_manage_form_data['_wc_booking_has_resources'] ),
																'max_persons'                => wc_clean( $wcfm_products_manage_form_data['_wc_booking_max_persons_group'] ),
																'min_persons'                => wc_clean( $wcfm_products_manage_form_data['_wc_booking_min_persons_group'] ),
																'person_types'               => $person_types,
																'resource_label'             => wc_clean( $wcfm_products_manage_form_data['_wc_booking_resource_label'] ),
																'resource_base_costs'        => wp_list_pluck( $resources, 'base_cost' ),
																'resource_block_costs'       => wp_list_pluck( $resources, 'block_cost' ),
																'resource_ids'               => array_keys( $resources ),
																'resources_assignment'       => wc_clean( $wcfm_products_manage_form_data['_wc_booking_resources_assignment'] ),
															);
		
		$wcfm_booking_data = array_merge( $wcfm_booking_data, $wcfmu_booking_data );
		return $wcfm_booking_data;
	}
	
}