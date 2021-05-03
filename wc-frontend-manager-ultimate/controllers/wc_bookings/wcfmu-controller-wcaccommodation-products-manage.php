<?php
/**
 * WCFMu plugin controllers
 *
 * Plugin WC Booking Accommodation Products Manage Controller
 *
 * @author 		WC Lovers
 * @package 	wcfmu/controllers
 * @version   2.4.4
 */

class WCFMu_WCAccommodation_Products_Manage_Controller {
	
	public function __construct() {
		global $WCFM, $WCFMu;
		
		// Booking Product Meta Data Save
    add_action( 'after_wcfm_products_manage_meta_save', array( &$this, 'wcba_product_manager_data_save' ), 30, 2 );
	}
	
		/**
	 * WC Booking Accommodation Product Meta data save
	 */
	function wcba_product_manager_data_save( $new_product_id, $wcfm_products_manage_form_data ) {
		global $wpdb, $WCFM, $WCFMu, $_POST;
		
		// Only set props if the product is a Accommodation product.
		$product_type = empty( $wcfm_products_manage_form_data['product_type'] ) ? WC_Product_Factory::get_product_type( $new_product_id ) : sanitize_title( stripslashes( $wcfm_products_manage_form_data['product_type'] ) );
		if ( 'accommodation-booking' != $product_type ) {
			return;
		}

		$meta_to_save = array(
			'_wc_booking_has_persons'                              => 'issetyesno',
			'_wc_booking_person_qty_multiplier'                    => 'yesno',
			'_wc_booking_person_cost_multiplier'                   => 'yesno',
			'_wc_booking_min_persons_group'                        => 'int',
			'_wc_booking_max_persons_group'                        => 'int',
			'_wc_booking_has_person_types'                         => 'yesno',
			'_wc_booking_has_resources'                            => 'issetyesno',
			'_wc_booking_resources_assignment'                     => '',
			'_wc_booking_resouce_label'                            => '',
			'_wc_accommodation_booking_calendar_display_mode'      => '',
			'_wc_accommodation_booking_requires_confirmation'      => 'yesno',
			'_wc_accommodation_booking_user_can_cancel'            => '',
			'_wc_accommodation_booking_cancel_limit'               => 'int',
			'_wc_accommodation_booking_cancel_limit_unit'          => '',
			'_wc_accommodation_booking_max_date'                   => 'max_date',
			'_wc_accommodation_booking_max_date_unit'              => 'max_date_unit',
			'_wc_accommodation_booking_min_date'                   => 'int',
			'_wc_accommodation_booking_min_date_unit'              => '',
			'_wc_accommodation_booking_qty'                        => 'int',
			'_wc_accommodation_booking_base_cost'                  => 'float',
			'_wc_accommodation_booking_display_cost'               => '',
			'_wc_accommodation_booking_min_duration'               => 'int',
			'_wc_accommodation_booking_max_duration'               => 'int',
		);

		foreach ( $meta_to_save as $meta_key => $sanitize ) {
			$value = ! empty( $wcfm_products_manage_form_data[ $meta_key ] ) ? $wcfm_products_manage_form_data[ $meta_key ] : '';
			switch ( $sanitize ) {
				case 'int' :
					$value = $value ? absint( $value ) : '';
					break;
				case 'float' :
					$value = $value ? floatval( $value ) : '';
					break;
				case 'yesno' :
					$value = $value == 'yes' ? 'yes' : 'no';
					break;
				case 'issetyesno' :
					$value = $value ? 'yes' : 'no';
					break;
				case 'issetyesnoempty' :
					$value = $value ? 'yes' : '';
					break;
				case 'max_date' :
					$value = absint( $value );
					if ( $value == 0 ) {
						$value = 1;
					}
					break;
				default :
					$value = sanitize_text_field( $value );
			}

			$meta_key = str_replace( '_wc_accommodation_booking_', '_wc_booking_', $meta_key );
			update_post_meta( $new_product_id, $meta_key, $value );

			if ( '_wc_booking_display_cost' === $meta_key ) {
				update_post_meta( $new_product_id, '_wc_display_cost', $value );
			}
			
			if ( '_wc_booking_base_cost' === $meta_key ) {
				update_post_meta( $new_product_id, '_wc_booking_block_cost', $value );
			}
			
			if ( '_wc_booking_resource_label' === $meta_key ) {
				update_post_meta( $new_product_id, 'wc_booking_resource_label', $value );
			}
		}
		
		// Restricted days.
		update_post_meta( $new_product_id, '_wc_booking_has_restricted_days', isset( $wcfm_products_manage_form_data['_wc_accommodation_booking_has_restricted_days'] ) );
		$restricted_days = isset( $wcfm_products_manage_form_data['_wc_accommodation_booking_restricted_days'] ) ? array_combine( wc_clean( $wcfm_products_manage_form_data['_wc_accommodation_booking_restricted_days'] ), wc_clean( $wcfm_products_manage_form_data['_wc_accommodation_booking_restricted_days'] ) ) : '';
		update_post_meta( $new_product_id, '_wc_booking_restricted_days', $restricted_days );
		
		// Availability Rules
		$availability_rule_index = 0;
		$availability_rules = array();
		$availability_default_rules = array(  "type"       => 'custom',
																				  "from"       => '',
																				  "to"         => '',
																					"bookable"   => '',
																					"priority"   => 10
																				);
		if( isset($wcfm_products_manage_form_data['_wc_accommodation_booking_availability_rules']) && !empty($wcfm_products_manage_form_data['_wc_accommodation_booking_availability_rules']) ) {
			foreach( $wcfm_products_manage_form_data['_wc_accommodation_booking_availability_rules'] as $availability_rule ) {
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
		update_post_meta( $new_product_id, '_wc_booking_availability', $availability_rules );
		
		// Resources
		$resource_index = 0;
		$resources = array();
		$resource_base_costs = array();
		$resource_block_costs = array();
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
					
					$wpdb->query( "DELETE FROM {$wpdb->prefix}wc_booking_relationships WHERE `product_id` = {$new_product_id} AND `resource_id` = {$resource_id}" );
					$wc_booking_relationships = "INSERT into {$wpdb->prefix}wc_booking_relationships 
																			(`sort_order`, `product_id`, `resource_id`)
																			VALUES
																			({$resource_index}, {$new_product_id}, {$resource_id})
																			";
					$wpdb->query($wc_booking_relationships);
					
					$resources[ $resource_id ] = array(
						'base_cost'  => wc_clean( $booking_resources[ 'resource_base_cost' ] ),
						'block_cost' => wc_clean( $booking_resources[ 'resource_block_cost' ] ),
					);
					$resource_base_costs[ $resource_id ] = wc_clean( $booking_resources[ 'resource_base_cost' ] );
					$resource_block_costs[ $resource_id ] = wc_clean( $booking_resources[ 'resource_block_cost' ] );
					$resource_index++;
				}
			}
			update_post_meta( $new_product_id, '_resource_base_costs', $resource_base_costs );
			update_post_meta( $new_product_id, '_resource_block_costs', $resource_block_costs );
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
					$person_type->set_parent_id( $new_product_id );
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
					'parent_id'   => $new_product_id,
				) );
				$person_type->save();
				$person_types[] = $person_type;
				$person_type_index++;
			}
		}
		
		// Cost Rules
		$cost_rule_index = 0;
		$cost_rules = array();
		$cost_default_rules = array(  "type"            => 'custom',
																	"from"            => '',
																	"to"              => '',
																	"override_block"   => '',
																);
		if( isset($wcfm_products_manage_form_data['_wc_accommodation_booking_cost_rules']) && !empty($wcfm_products_manage_form_data['_wc_accommodation_booking_cost_rules']) ) {
			foreach( $wcfm_products_manage_form_data['_wc_accommodation_booking_cost_rules'] as $cost_rule ) {
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
				}
				if( $cost_rules[$cost_rule_index]['from'] && $cost_rules[$cost_rule_index]['to'] ) {
					$cost_rules[$cost_rule_index]['base_cost'] = 0;
					$cost_rules[$cost_rule_index]['base_modifier'] = 'plus';
					$cost_rules[$cost_rule_index]['override_block'] = $cost_rule['override_block'];
					$cost_rule_index++;
				} else {
					unset( $cost_rules[$cost_rule_index] );
				}
			}
			update_post_meta( $new_product_id, '_wc_booking_pricing', $cost_rules );
			update_post_meta( $new_product_id, '_wc_booking_cost', '' );
		}
		
		update_post_meta( $new_product_id, '_regular_price', '' );
		update_post_meta( $new_product_id, '_sale_price', '' );
		update_post_meta( $new_product_id, '_manage_stock', 'no' );

		// Set price so filters work - using get_base_cost()
		$product = wc_get_product( $new_product_id );
		update_post_meta( $new_product_id, '_price', $product->get_base_cost() );
		
		// Set Product Type - AccoMmodation
		wp_set_object_terms( $new_product_id, $wcfm_products_manage_form_data['product_type'], 'product_type' );
	}
	
}