<?php
/**
 * WCFMu plugin controllers
 *
 * Plugin Products Quick Manage Controller
 *
 * @author 		WC Lovers
 * @package 	wcfm/controllers
 * @version   1.0.0
 */

class WCFMu_Products_Quick_Manage_Controller {
	
	public function __construct() {
		global $WCFM;
		
		$this->processing();
	}
	
	public function processing() {
		global $WCFM, $WCFMu, $wpdb, $_POST;
		
		$wcfm_quick_edit_form_data = array();
	  parse_str($_POST['wcfm_quick_edit_form'], $wcfm_quick_edit_form_data);
	  //print_r($wcfm_quick_edit_form_data);
	  $wcfm_products_manage_messages = get_wcfm_products_manager_messages();
	  $has_error = false;
	  
	  if(isset($wcfm_quick_edit_form_data['wcfm_quick_edit_title']) && !empty($wcfm_quick_edit_form_data['wcfm_quick_edit_title'])) {
	  	$product_id = $wcfm_quick_edit_form_data['wcfm_quick_edit_product_id'];
	  	$title = $wcfm_quick_edit_form_data['wcfm_quick_edit_title'];
	  	
	  	// Update Basic
	  	$update_product['ID'] = $product_id;
	  	$update_product['post_title'] = $title;
	  	$update_product_id = wp_update_post( $update_product, true );
	  	
	  	$product = wc_get_product( $product_id );
	  	
	  	// Update SKU
	  	if( apply_filters( 'wcfm_is_allow_sku', true ) ) { 
				if(isset($wcfm_quick_edit_form_data['wcfm_quick_edit_sku']) && !empty($wcfm_quick_edit_form_data['wcfm_quick_edit_sku'])) {
					//update_post_meta( $product_id, '_sku', $wcfm_quick_edit_form_data['wcfm_quick_edit_sku'] );
					$new_sku = (string) wc_clean( $wcfm_quick_edit_form_data['wcfm_quick_edit_sku'] );
					$old_sku = get_post_meta( $product_id, '_sku', true );
					if( $new_sku !== $old_sku ) {
						$unique_sku = wc_product_has_unique_sku( $product_id, $wcfm_quick_edit_form_data['wcfm_quick_edit_sku'] );
						if ( ! $unique_sku ) {
							echo '{"status": false, "message": "' . $wcfm_products_manage_messages['sku_unique'] . '"}';
							$has_error = true;
						} else {
							update_post_meta( $product_id, '_sku', $new_sku );
						}
					}
				} else {
					update_post_meta( $product_id, '_sku', '' );
				}
			}
	  	
	  	// Update Price
	  	if( isset($wcfm_quick_edit_form_data['wcfm_quick_edit_regular_price']) ) {
	  		if( !empty($wcfm_quick_edit_form_data['wcfm_quick_edit_regular_price']) ) {
	  			$product->set_regular_price( wc_format_decimal($wcfm_quick_edit_form_data['wcfm_quick_edit_regular_price']) );
				} else {
					$product->set_regular_price( '' );
				}
	  	}
	  	if( isset($wcfm_quick_edit_form_data['wcfm_quick_edit_sale_price']) ) {
				if( !empty($wcfm_quick_edit_form_data['wcfm_quick_edit_sale_price']) ) {
					$product->set_sale_price( wc_format_decimal($wcfm_quick_edit_form_data['wcfm_quick_edit_sale_price']) );
				} else {
					$product->set_sale_price( '' );
				}
				delete_transient( 'wc_products_onsale' );
			}
			
			// Update Stock
			if( apply_filters( 'wcfm_is_allow_inventory', true ) ) {
				if( isset($wcfm_quick_edit_form_data['wcfm_quick_edit_stock']) ) {
					$product->set_stock_quantity( $wcfm_quick_edit_form_data['wcfm_quick_edit_stock'] );
					$product->set_stock_status( 'instock' );
				} else {
					//update_post_meta( $product_id, '_stock', '' );
					//update_post_meta( $product_id, '_stock_status', 'outofstock' );
				}
			}
			
			if( apply_filters( 'wcfm_is_allow_products_manage_visibility', true ) ) {
				if ( isset( $wcfm_quick_edit_form_data['catalog_visibility'] ) && ! empty( $wcfm_quick_edit_form_data['catalog_visibility'] ) ) { 
					$product->set_catalog_visibility( wc_clean( $wcfm_quick_edit_form_data['catalog_visibility'] ) ); 
				}
			}
			
			$product->save();
			
			do_action( 'wcfm_product_quick_edit_save', $product_id, $product, $wcfm_quick_edit_form_data );
			
			// Clear cache and transients
			wc_delete_product_transients( $product_id );
			
			if( !$has_error ) {
				echo '{"status": true, "message": "' . $wcfm_products_manage_messages['product_saved'] . '"}';
			}
	  } else {
	  	echo '{"status": false, "message": "' . $wcfm_products_manage_messages['no_title'] . '"}';
	  }
	  
	  die;
	}
}