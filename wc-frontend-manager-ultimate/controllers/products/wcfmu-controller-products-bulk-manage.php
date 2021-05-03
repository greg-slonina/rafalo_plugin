<?php
/**
 * WCFMu plugin controllers
 *
 * Plugin Products Bulk Manage Controller
 *
 * @author 		WC Lovers
 * @package 	wcfmu/controllers
 * @version   3.2.4
 */

class WCFMu_Products_Bulk_Manage_Controller {
	
	public function __construct() {
		global $WCFM;
		
		$this->processing();
	}
	
	public function processing() {
		global $WCFM, $WCFMu, $wpdb, $_POST;
		
		$wcfm_bulk_edit_form_data = array();
	  parse_str($_POST['wcfm_bulk_edit_form'], $wcfm_bulk_edit_form_data);
	  //print_r($wcfm_bulk_edit_form_data);
	  $wcfm_products_manage_messages = get_wcfm_products_manager_messages();
	  $has_error = false;
	  
	  $selected_product_str = $wcfm_bulk_edit_form_data['wcfm_bulk_edit_products'];
	  
	  if( $selected_product_str ) {
	  	$selected_products = explode( ",", $selected_product_str );
	  	if( is_array( $selected_products ) && !empty( $selected_products ) ) {
	  		foreach( $selected_products as $post_id ) {
	  			if( $post_id ) {
	  				$post = get_post( $post_id );
	  				$product = wc_get_product( $post );
	  				
	  				$data_store        = $product->get_data_store();
						$old_regular_price = $product->get_regular_price();
						$old_sale_price    = $product->get_sale_price();
				
						// Save fields
						if ( ! empty( $wcfm_bulk_edit_form_data['_product_status'] ) ) {
							$product_status = wc_clean( $wcfm_bulk_edit_form_data['_product_status'] );
							$update_product = array(
																			'ID'           => $post_id,
																			'post_status'  => $product_status,
																			'post_type'    => 'product',
																		 );
							wp_update_post( $update_product, true );
						}
						
						if( $allow_tax = apply_filters( 'wcfm_is_allow_tax', true ) ) {
							if ( ! empty( $wcfm_bulk_edit_form_data['_tax_status'] ) ) {
								$product->set_tax_status( wc_clean( $wcfm_bulk_edit_form_data['_tax_status'] ) );
							}
					
							if ( ! empty( $wcfm_bulk_edit_form_data['_tax_class'] ) ) {
								$tax_class = wc_clean( $wcfm_bulk_edit_form_data['_tax_class'] );
								if ( 'standard' == $tax_class ) {
									$tax_class = '';
								}
								$product->set_tax_class( $tax_class );
							}
						}
						
						if( $allow_shipping = apply_filters( 'wcfm_is_allow_shipping', true ) ) {
							if ( ! empty( $wcfm_bulk_edit_form_data['change_weight'] ) && isset( $wcfm_bulk_edit_form_data['_weight'] ) ) {
								$product->set_weight( wc_clean( stripslashes( $wcfm_bulk_edit_form_data['_weight'] ) ) );
							}
					
							if ( ! empty( $wcfm_bulk_edit_form_data['change_dimensions'] ) ) {
								if ( isset( $wcfm_bulk_edit_form_data['_length'] ) ) {
									$product->set_length( wc_clean( stripslashes( $wcfm_bulk_edit_form_data['_length'] ) ) );
								}
								if ( isset( $wcfm_bulk_edit_form_data['_width'] ) ) {
									$product->set_width( wc_clean( stripslashes( $wcfm_bulk_edit_form_data['_width'] ) ) );
								}
								if ( isset( $wcfm_bulk_edit_form_data['_height'] ) ) {
									$product->set_height( wc_clean( stripslashes( $wcfm_bulk_edit_form_data['_height'] ) ) );
								}
							}
				
							if ( ! empty( $wcfm_bulk_edit_form_data['_shipping_class'] ) ) {
								if ( '_no_shipping_class' === $wcfm_bulk_edit_form_data['_shipping_class'] ) {
									$product->set_shipping_class_id( 0 );
								} else {
									$shipping_class_id = $data_store->get_shipping_class_id_by_slug( wc_clean( $wcfm_bulk_edit_form_data['_shipping_class'] ) );
									$product->set_shipping_class_id( $shipping_class_id );
								}
							}
						}
						
						if( apply_filters( 'wcfm_is_allow_products_manage_visibility', true ) ) {
							if ( ! empty( $wcfm_bulk_edit_form_data['_visibility'] ) ) {
								$product->set_catalog_visibility( wc_clean( $wcfm_bulk_edit_form_data['_visibility'] ) );
							}
						}
					
						if( $wcfm_is_allow_featured_product = apply_filters( 'wcfm_is_allow_featured_product', true ) ) {
							if ( ! empty( $wcfm_bulk_edit_form_data['_featured'] ) ) {
								$product->set_featured( stripslashes( $wcfm_bulk_edit_form_data['_featured'] ) );
							}
						}
				
						// Sold Individually
						if( $allow_inventory = apply_filters( 'wcfm_is_allow_inventory', true ) ) {
							if ( ! empty( $wcfm_bulk_edit_form_data['_sold_individually'] ) ) {
								if ( 'yes' === $wcfm_bulk_edit_form_data['_sold_individually'] ) {
									$product->set_sold_individually( 'yes' );
								} else {
									$product->set_sold_individually( '' );
								}
							}
						}
				
						// Handle price - remove dates and set to lowest
						$change_price_product_types = apply_filters( 'woocommerce_bulk_edit_save_price_product_types', array( 'simple', 'external' ) );
						$can_product_type_change_price = false;
						foreach ( $change_price_product_types as $product_type ) {
							if ( $product->is_type( $product_type ) ) {
								$can_product_type_change_price = true;
								break;
							}
						}
				
						if ( $can_product_type_change_price ) {
				
							$price_changed = false;
				
							if ( ! empty( $wcfm_bulk_edit_form_data['change_regular_price'] ) ) {
								$change_regular_price = absint( $wcfm_bulk_edit_form_data['change_regular_price'] );
								$regular_price = esc_attr( stripslashes( $wcfm_bulk_edit_form_data['_regular_price'] ) );
				
								switch ( $change_regular_price ) {
									case 1 :
										$new_price = $regular_price;
										break;
									case 2 :
										if ( strstr( $regular_price, '%' ) ) {
											$percent = str_replace( '%', '', $regular_price ) / 100;
											$new_price = $old_regular_price + ( round( $old_regular_price * $percent, wc_get_price_decimals() ) );
										} else {
											$new_price = $old_regular_price + $regular_price;
										}
										break;
									case 3 :
										if ( strstr( $regular_price, '%' ) ) {
											$percent = str_replace( '%', '', $regular_price ) / 100;
											$new_price = max( 0, $old_regular_price - ( round( $old_regular_price * $percent, wc_get_price_decimals() ) ) );
										} else {
											$new_price = max( 0, $old_regular_price - $regular_price );
										}
										break;
				
									default :
										break;
								}
				
								if ( isset( $new_price ) && $new_price != $old_regular_price ) {
									$price_changed = true;
									$new_price = round( $new_price, wc_get_price_decimals() );
									$product->set_regular_price( $new_price );
								}
							}
				
							if ( ! empty( $wcfm_bulk_edit_form_data['change_sale_price'] ) ) {
								$change_sale_price = absint( $wcfm_bulk_edit_form_data['change_sale_price'] );
								$sale_price        = esc_attr( stripslashes( $wcfm_bulk_edit_form_data['_sale_price'] ) );
				
								switch ( $change_sale_price ) {
									case 1 :
										$new_price = $sale_price;
										break;
									case 2 :
										if ( strstr( $sale_price, '%' ) ) {
											$percent = str_replace( '%', '', $sale_price ) / 100;
											$new_price = $old_sale_price + ( $old_sale_price * $percent );
										} else {
											$new_price = $old_sale_price + $sale_price;
										}
										break;
									case 3 :
										if ( strstr( $sale_price, '%' ) ) {
											$percent = str_replace( '%', '', $sale_price ) / 100;
											$new_price = max( 0, $old_sale_price - ( $old_sale_price * $percent ) );
										} else {
											$new_price = max( 0, $old_sale_price - $sale_price );
										}
										break;
									case 4 :
										if ( strstr( $sale_price, '%' ) ) {
											$percent = str_replace( '%', '', $sale_price ) / 100;
											$new_price = max( 0, $product->regular_price - ( $product->regular_price * $percent ) );
										} else {
											$new_price = max( 0, $product->regular_price - $sale_price );
										}
										break;
				
									default :
										break;
								}
				
								if ( isset( $new_price ) && $new_price != $old_sale_price ) {
									$price_changed = true;
									$new_price = ! empty( $new_price ) || '0' === $new_price ? round( $new_price, wc_get_price_decimals() ) : '';
									$product->set_sale_price( $new_price );
								}
							}
				
							if ( $price_changed ) {
								$product->set_date_on_sale_to( '' );
								$product->set_date_on_sale_from( '' );
				
								if ( $product->get_regular_price() < $product->get_sale_price() ) {
									$product->set_sale_price( '' );
								}
							}
						}
				
						// Handle Stock Data
						if( $allow_inventory = apply_filters( 'wcfm_is_allow_inventory', true ) ) {
							$was_managing_stock = $product->get_manage_stock() ? 'yes' : 'no';
							$stock_status       = $product->get_stock_status();
							$backorders         = $product->get_backorders();
							$backorders         = ! empty( $wcfm_bulk_edit_form_data['_backorders'] ) ? wc_clean( $wcfm_bulk_edit_form_data['_backorders'] ) : $backorders;
							$stock_status       = ! empty( $wcfm_bulk_edit_form_data['_stock_status'] ) ? wc_clean( $wcfm_bulk_edit_form_data['_stock_status'] ) : $stock_status;
					
							if ( ! empty( $wcfm_bulk_edit_form_data['_manage_stock'] ) ) {
								$manage_stock = 'yes' === wc_clean( $wcfm_bulk_edit_form_data['_manage_stock'] ) && 'grouped' !== $product->get_type() ? 'yes' : 'no';
							} else {
								$manage_stock = $was_managing_stock;
							}
					
							$stock_amount = 'yes' === $manage_stock && ! empty( $wcfm_bulk_edit_form_data['change_stock'] ) ? wc_stock_amount( $wcfm_bulk_edit_form_data['_stock'] ) : $product->get_stock_quantity();
					
							$product->set_manage_stock( $manage_stock );
							$product->set_backorders( $backorders );
					
							if ( 'yes' === get_option( 'woocommerce_manage_stock' ) ) {
								$product->set_stock_quantity( $stock_amount );
							}
					
							// Apply product type constraints to stock status.
							if ( $product->is_type( 'external' ) ) {
								// External products are always in stock.
								$product->set_stock_status( 'instock' );
							} elseif ( $product->is_type( 'variable' ) && ! $product->get_manage_stock() ) {
								// Stock status is determined by children.
								foreach ( $product->get_children() as $child_id ) {
									$child = wc_get_product( $child_id );
									if ( ! $product->get_manage_stock() ) {
										$child->set_stock_status( $stock_status );
										//$child->set_manage_stock( $manage_stock );
										//$child->set_stock_quantity( $stock_amount );
										$child->save();
									}
								}
								$product = WC_Product_Variable::sync( $product, false );
							} else {
								$product->set_stock_status( $stock_status );
							}
						}
						
						$product->save();
						
						do_action( 'wcfm_product_bulk_edit_save', $product, $wcfm_bulk_edit_form_data );
						
						// Clear cache and transients
						wc_delete_product_transients( $product->get_id() );
	  			}
	  		}
	  	}
	  }
			
		echo '{"status": true, "message": "' . $wcfm_products_manage_messages['product_saved'] . '"}';
	  
	  die;
	}
}