<?php
/**
 * WCFMu plugin controllers
 *
 * WC Per Product Shipping Products Manage Controller
 *
 * @author 		WC Lovers
 * @package 	wcfmu/controllers/thirdparty/
 * @version   2.5.0
 */

class WCFMu_WC_Per_Product_Shipping_Products_Manage_Controller {
	
	public $per_product_shipping_table = 'wcpv_per_product_shipping_rules';
	
	public function __construct() {
		global $WCFM, $WCFMu;
		
		if( WCFMu_Dependencies::wcfm_wc_per_peroduct_shipping_active_check() ) {
			$this->per_product_shipping_table = 'woocommerce_per_product_shipping_rules';
		}
		
		add_action( 'after_wcfm_products_manage_meta_save', array( &$this, 'wcfm_wc_per_product_shipping_product_meta_save' ), 150, 2 );
		
		add_filter( 'wcfm_product_variation_data_factory', array( &$this, 'wcfmu_wc_per_product_shipping_product_variation_save' ), 150, 5 );
	}
	
	/**
	 * WC Per Product Shipping Product Meta data save
	 */
	function wcfm_wc_per_product_shipping_product_meta_save( $new_product_id, $wcfm_products_manage_form_data ) {
		global $wpdb, $WCFM, $_POST, $wpdb;
		
		if( isset( $wcfm_products_manage_form_data['_per_product_shipping_rules'] ) ) {
	
			// Enabled or Disabled.
			if( WCFMu_Dependencies::wcfm_wc_per_peroduct_shipping_active_check() ) {
				if ( ! empty( $wcfm_products_manage_form_data['_per_product_shipping'] ) ) {
					update_post_meta( $new_product_id, '_per_product_shipping', 'yes' );
					update_post_meta( $new_product_id, '_per_product_shipping_add_to_all', ! empty( $wcfm_products_manage_form_data['_per_product_shipping_add_to_all'] ) ? 'yes' : 'no' );
				} else {
					delete_post_meta( $new_product_id, '_per_product_shipping' );
					delete_post_meta( $new_product_id, '_per_product_shipping_add_to_all' );
				}
			}
			
			$product_existing_rules = array();
			$rules = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}{$this->per_product_shipping_table} WHERE product_id = %d ORDER BY rule_order;", $new_product_id ) );
			if( !empty($rules) ) {
				foreach ( $rules as $rule ) {
					$product_existing_rules[$rule->rule_id] = $rule->rule_id;
				}
			}
			
			$rule_order = 0;
			if( isset( $wcfm_products_manage_form_data['_per_product_shipping_rules'] ) && ! empty( $wcfm_products_manage_form_data['_per_product_shipping_rules'] ) ) {
				foreach( $wcfm_products_manage_form_data['_per_product_shipping_rules'] as $per_product_shipping_rule ) {
					$per_product_shipping_rule_id = ( isset( $per_product_shipping_rule['item_id'] ) ) ? $per_product_shipping_rule['item_id'] : 0;
					if( !$per_product_shipping_rule_id ) {
						if( !empty($per_product_shipping_rule['country']) || !empty($per_product_shipping_rule['state']) || !empty($per_product_shipping_rule['postcode']) || !empty($per_product_shipping_rule['cost']) || !empty($per_product_shipping_rule['item_cost']) ) {
							$wpdb->insert(
								$wpdb->prefix . $this->per_product_shipping_table,
								apply_filters( 'wcfm_per_product_shipping_data_for_save', array(
									'rule_country'   => esc_attr( $per_product_shipping_rule['country'] ),
									'rule_state'     => esc_attr( $per_product_shipping_rule['state'] ),
									'rule_postcode'  => esc_attr( $per_product_shipping_rule['postcode'] ),
									'rule_cost'      => esc_attr( $per_product_shipping_rule['cost'] ),
									'rule_item_cost' => esc_attr( $per_product_shipping_rule['item_cost'] ),
									'rule_order'     => $rule_order++,
									'product_id'     => absint( $new_product_id ),
								) )
							);
						}
					} else {
						if( !empty($per_product_shipping_rule['country']) || !empty($per_product_shipping_rule['state']) || !empty($per_product_shipping_rule['postcode']) || !empty($per_product_shipping_rule['cost']) || !empty($per_product_shipping_rule['item_cost']) ) {
							$wpdb->update(
								$wpdb->prefix . $this->per_product_shipping_table,
								apply_filters( 'wcfm_per_product_shipping_data_for_save', array(
									'rule_country'   => esc_attr( $per_product_shipping_rule['country'] ),
									'rule_state'     => esc_attr( $per_product_shipping_rule['state'] ),
									'rule_postcode'  => esc_attr( $per_product_shipping_rule['postcode'] ),
									'rule_cost'      => esc_attr( $per_product_shipping_rule['cost'] ),
									'rule_item_cost' => esc_attr( $per_product_shipping_rule['item_cost'] ),
									'rule_order'     => $rule_order++,
								) ),
								array(
									'product_id' => absint( $new_product_id ),
									'rule_id'    => absint( $per_product_shipping_rule_id ),
								)
							);
							unset( $product_existing_rules[$per_product_shipping_rule_id] );
						}
					}
				}
			}
			// Remove Old Deleted Rules
			if( !empty( $product_existing_rules ) ) {
				foreach( $product_existing_rules as $product_existing_rule ) {
					$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}{$this->per_product_shipping_table} WHERE product_id = %d AND rule_id = %s;", absint( $new_product_id ), absint( $product_existing_rule ) ) );
				}
			}
		}
	}
	
	/**
	 * WC Per Product Shipping Variation Data Save
	 */
	function wcfmu_wc_per_product_shipping_product_variation_save( $wcfm_variation_data, $new_product_id, $variation_id, $variations, $wcfm_products_manage_form_data ) {
	 	global $wpdb, $WCFM, $WCFMu;
	 	  
		if( isset( $variations['per_product_shipping_rules'] ) ) {
	
			// Enabled or Disabled.
			if( WCFMu_Dependencies::wcfm_wc_per_peroduct_shipping_active_check() ) {
				if ( ! empty( $variations['per_product_shipping'] ) ) {
					update_post_meta( $variation_id, '_per_product_shipping', 'yes' );
					update_post_meta( $variation_id, '_per_product_shipping_add_to_all', ! empty( $variations['_per_product_shipping_add_to_all'] ) ? 'yes' : 'no' );
				} else {
					delete_post_meta( $variation_id, '_per_product_shipping' );
					delete_post_meta( $variation_id, '_per_product_shipping_add_to_all' );
				}
			}
			
			$product_existing_rules = array();
			$rules = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}{$this->per_product_shipping_table} WHERE product_id = %d ORDER BY rule_order;", $variation_id ) );
			if( !empty($rules) ) {
				foreach ( $rules as $rule ) {
					$product_existing_rules[$rule->rule_id] = $rule->rule_id;
				}
			}
			
			$rule_order = 0;
			if( isset( $variations['per_product_shipping_rules'] ) && ! empty( $variations['per_product_shipping_rules'] ) ) {
				foreach( $variations['per_product_shipping_rules'] as $per_product_shipping_rule ) {
					$per_product_shipping_rule_id = ( isset( $per_product_shipping_rule['item_id'] ) ) ? $per_product_shipping_rule['item_id'] : 0;
					if( !$per_product_shipping_rule_id ) {
						if( !empty($per_product_shipping_rule['country']) || !empty($per_product_shipping_rule['state']) || !empty($per_product_shipping_rule['postcode']) || !empty($per_product_shipping_rule['cost']) || !empty($per_product_shipping_rule['item_cost']) ) {
							$wpdb->insert(
								$wpdb->prefix . $this->per_product_shipping_table,
								apply_filters( 'wcfm_per_product_shipping_data_for_save', array(
									'rule_country'   => esc_attr( $per_product_shipping_rule['country'] ),
									'rule_state'     => esc_attr( $per_product_shipping_rule['state'] ),
									'rule_postcode'  => esc_attr( $per_product_shipping_rule['postcode'] ),
									'rule_cost'      => esc_attr( $per_product_shipping_rule['cost'] ),
									'rule_item_cost' => esc_attr( $per_product_shipping_rule['item_cost'] ),
									'rule_order'     => $rule_order++,
									'product_id'     => absint( $variation_id ),
								) )
							);
						}
					} else {
						if( !empty($per_product_shipping_rule['country']) || !empty($per_product_shipping_rule['state']) || !empty($per_product_shipping_rule['postcode']) || !empty($per_product_shipping_rule['cost']) || !empty($per_product_shipping_rule['item_cost']) ) {
							$wpdb->update(
								$wpdb->prefix . $this->per_product_shipping_table,
								apply_filters( 'wcfm_per_product_shipping_data_for_save', array(
									'rule_country'   => esc_attr( $per_product_shipping_rule['country'] ),
									'rule_state'     => esc_attr( $per_product_shipping_rule['state'] ),
									'rule_postcode'  => esc_attr( $per_product_shipping_rule['postcode'] ),
									'rule_cost'      => esc_attr( $per_product_shipping_rule['cost'] ),
									'rule_item_cost' => esc_attr( $per_product_shipping_rule['item_cost'] ),
									'rule_order'     => $rule_order++,
								) ),
								array(
									'product_id' => absint( $variation_id ),
									'rule_id'    => absint( $per_product_shipping_rule_id ),
								)
							);
							unset( $product_existing_rules[$per_product_shipping_rule_id] );
						}
					}
				}
			}
			
			// Remove Old Deleted Rules
			if( !empty( $product_existing_rules ) ) {
				foreach( $product_existing_rules as $product_existing_rule ) {
					$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}{$this->per_product_shipping_table} WHERE product_id = %d AND rule_id = %s;", absint( $variation_id ), absint( $product_existing_rule ) ) );
				}
			}
		}
		
		return $wcfm_variation_data;
	}
}