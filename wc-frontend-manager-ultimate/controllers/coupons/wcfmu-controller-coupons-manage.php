<?php
/**
 * WCFMu plugin controllers
 *
 * Plugin Coupons Manage Controller
 *
 * @author 		WC Lovers
 * @package 	wcfmu/controllers
 * @version   1.0.0
 */

class WCFMu_Coupons_Manage_Controller {
	
	public function __construct() {
		global $WCFM, $WCFMu;
		
		add_filter( 'wcfm_coupon_data_factory', array( &$this, 'wcfmu_coupon_data_factory' ), 10, 3);
		
	}
	
	public function wcfmu_coupon_data_factory( $wcfm_coupon_data, $new_coupon_id, $wcfm_coupon_manager_form_data ) {
		global $WCFM, $WCFMu;
		
		$default_product_ids = array();
		if( wcfm_is_vendor() ) { $default_product_ids = array( 0 => -1 ); } 
		
		$product_ids                = isset( $wcfm_coupon_manager_form_data['product_ids'] ) ? (array) $wcfm_coupon_manager_form_data['product_ids'] : array();
		
		if( wcfm_is_vendor() ) {
			delete_post_meta( $new_coupon_id, '_wcfm_vendor_coupon_all_product' );
			if( empty( $product_ids ) ) {
				$products_objs = $WCFM->wcfm_vendor_support->wcfm_get_products_by_vendor( apply_filters( 'wcfm_current_vendor_id', get_current_user_id() ), 'publish' );
				$products_array = array();
				if( !empty($products_objs) ) {
					foreach( $products_objs as $products_obj ) {
						$product_ids[] = esc_attr( $products_obj->ID );
					}
					update_post_meta( $new_coupon_id, '_wcfm_vendor_coupon_all_product', 'yes' );
				} else {
					$product_ids = $default_product_ids;
				}
			}
		}
		
		$product_categories         = isset( $wcfm_coupon_manager_form_data['product_categories'] ) ? (array) $wcfm_coupon_manager_form_data['product_categories'] : array();
		$exclude_product_categories = isset( $wcfm_coupon_manager_form_data['exclude_product_categories'] ) ? (array) $wcfm_coupon_manager_form_data['exclude_product_categories'] : array();
		
		$wcfmu_coupon_data = array(
															'individual_use'              => isset( $wcfm_coupon_manager_form_data['individual_use'] ),
															'product_ids'                 => $product_ids,
															'excluded_product_ids'        => isset( $wcfm_coupon_manager_form_data['exclude_product_ids'] ) ? array_filter( array_map( 'intval', (array) $wcfm_coupon_manager_form_data['exclude_product_ids'] ) ) : array(),
															'usage_limit'                 => absint( $wcfm_coupon_manager_form_data['usage_limit'] ),
															'usage_limit_per_user'        => absint( $wcfm_coupon_manager_form_data['usage_limit_per_user'] ),
															'limit_usage_to_x_items'      => absint( $wcfm_coupon_manager_form_data['limit_usage_to_x_items'] ),
															'product_categories'          => array_filter( array_map( 'intval', $product_categories ) ),
															'excluded_product_categories' => array_filter( array_map( 'intval', $exclude_product_categories ) ),
															'exclude_sale_items'          => isset( $wcfm_coupon_manager_form_data['exclude_sale_items'] ),
															'minimum_amount'              => wc_format_decimal( $wcfm_coupon_manager_form_data['minimum_amount'] ),
															'maximum_amount'              => wc_format_decimal( $wcfm_coupon_manager_form_data['maximum_amount'] ),
															'email_restrictions'          => array_filter( array_map( 'trim', explode( ',', wc_clean( $wcfm_coupon_manager_form_data['customer_email'] ) ) ) ),
														);
		$wcfm_coupon_data = array_merge( $wcfm_coupon_data, $wcfmu_coupon_data );
		
		return $wcfm_coupon_data;
	}
	
}