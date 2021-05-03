<?php
/**
 * WCFM plugin controllers
 *
 * Plugin Products Stock Manager Controller
 *
 * @author 		WC Lovers
 * @package 	wcfmu/controllers
 * @version   3.2.7
 */

class WCFMu_Stock_Manage_Update_Controller {
	
	public function __construct() {
		global $WCFM, $WCFMu;
		
		$this->processing();
	}
	
	public function processing() {
		global $WCFM, $WCFMu, $wpdb, $_POST;
		
		$wcfm_stock_manage_form_data = array();
	  parse_str($_POST['wcfm_stock_manage_form'], $wcfm_stock_manage_form_data);
	  
	  if( isset( $wcfm_stock_manage_form_data['stock_manage'] ) ) {
	  	foreach( $wcfm_stock_manage_form_data['stock_manage'] as $product_id => $stock_data ) {
	  		$manage_stock = $stock_data['manage_stock'];
	  		$status_options = $stock_data['status_options'];
	  		$backorder = $stock_data['backorder'];
	  		$stock_qty = $stock_data['stock_qty'];
	  		
	  		if($manage_stock == 'yes' ) {
	  			update_post_meta( $product_id, '_manage_stock', 'yes' );
					wc_update_product_stock( $product_id, $stock_qty );
	  		} else {
	  			update_post_meta( $product_id, '_manage_stock', 'no' );
	  			update_post_meta( $product_id, '_stock', '' );
	  		}
	  		
	  		wc_update_product_stock_status( $product_id, $status_options );
	  		update_post_meta( $product_id, '_backorders', $backorder );
	  	}
	  }
	  
	  echo '{ "status": true, "message": "' . __( 'Stock Successfully updated.', 'wc-frontend-manager-ultimate' ) . '" }';
	  
	  die;
	}
}
 
 
class WCFMu_Stock_Manage_Controller {
	
	public function __construct() {
		global $WCFM;
		
		$this->processing();
		
	}
	
	public function processing() {
		global $WCFM, $wpdb, $_POST;
		
		$length = $_POST['length'];
		$offset = $_POST['start'];
		
		$args = array(
							'posts_per_page'   => $length,
							'offset'           => $offset,
							'category'         => '',
							'category_name'    => '',
							'orderby'          => 'date',
							'order'            => 'DESC',
							'include'          => '',
							'exclude'          => '',
							'meta_key'         => '',
							'meta_value'       => '',
							'post_type'        => 'product',
							'post_mime_type'   => '',
							'post_parent'      => '',
							//'author'	   => get_current_user_id(),
							'post_status'      => array('draft', 'pending', 'publish', 'private'),
							'suppress_filters' => 0 
						);
		$for_count_args = $args;
		
		if( isset( $_POST['search'] ) && !empty( $_POST['search']['value'] )) {
			$args['s'] = $_POST['search']['value'];
		}
		
		if( isset($_POST['product_status']) && !empty($_POST['product_status']) ) $args['post_status'] = $_POST['product_status'];
  	
  	if( isset($_POST['product_type']) && !empty($_POST['product_type']) ) {
			if ( 'downloadable' == $_POST['product_type'] ) {
				$args['meta_value']    = 'yes';
				$args['meta_key']      = '_downloadable';
			} elseif ( 'virtual' == $_POST['product_type'] ) {
				$args['meta_value']    = 'yes';
				$args['meta_key']      = '_virtual';
			} elseif ( 'variable' == $_POST['product_type'] || 'simple' == $_POST['product_type'] ) {
				$args['tax_query'][] = array(
																		'taxonomy' => 'product_type',
																		'field' => 'slug',
																		'terms' => array($_POST['product_type']),
																		'operator' => 'IN'
																	);
			} else {
				$args['tax_query'][] = array(
																		'taxonomy' => 'product_type',
																		'field' => 'slug',
																		'terms' => array($_POST['product_type']),
																		'operator' => 'IN'
																	);
			}
		}
		
		if( isset($_POST['product_cat']) && !empty($_POST['product_cat']) ) {
			$args['tax_query'][] = array(
																		'taxonomy' => 'product_cat',
																		'field' => 'term_id',
																		'terms' => array($_POST['product_cat']),
																		'operator' => 'IN'
																	);
		}
		
		// Vendor Filter
		if( isset($_POST['product_vendor']) && !empty($_POST['product_vendor']) ) {
			$is_marketplace = wcfm_is_marketplace();
			if( $is_marketplace ) {
				if( !wcfm_is_vendor() ) {
					if( $is_marketplace == 'wcpvendors' ) {
						$args['tax_query'][] = array(
																					'taxonomy' => WC_PRODUCT_VENDORS_TAXONOMY,
																					'field' => 'term_id',
																					'terms' => $_POST['product_vendor'],
																				);
					} elseif( $is_marketplace == 'wcvendors' ) {
						$args['author'] = $_POST['product_vendor'];
					} elseif( $is_marketplace == 'wcmarketplace' ) {
						$vendor_term = absint( get_user_meta( $_POST['product_vendor'], '_vendor_term_id', true ) );
						$args['tax_query'][] = array(
																					'taxonomy' => 'dc_vendor_shop',
																					'field' => 'term_id',
																					'terms' => $vendor_term,
																				);
					} elseif( $is_marketplace == 'dokan' ) {
						$args['author'] = $_POST['product_vendor'];
					} elseif( $is_marketplace == 'wcfmmarketplace' ) {
						$args['author'] = wc_clean($_POST['product_vendor']);
					}
				}
			}
		}
		
		$args = apply_filters( 'wcfm_products_args', $args );
		
		$wcfm_products_array = get_posts( $args );
		
		// Get Product Count
		$pro_count = 0;
		$filtered_pro_count = 0;
		$for_count_args['post_type'] = array( 'product', 'variation' );
		$for_count_args['posts_per_page'] = -1;
		$for_count_args['offset'] = 0;
		$for_count_args = apply_filters( 'wcfm_products_args', $for_count_args );
		$wcfm_products_count = get_posts( $for_count_args );
		$pro_count = count($wcfm_products_count);
		// Get Filtered Post Count
		$args['posts_per_page'] = -1;
		$args['offset'] = 0;
		$wcfm_filterd_products_array = get_posts( $args );
		$filtered_pro_count = count($wcfm_filterd_products_array);
		
		if( isset( $_POST['search'] ) && !empty( $_POST['search']['value'] )) {
			unset( $args['s'] );
			
			$search_ids = array();
			$terms      = explode( ',', $_POST['search']['value'] );
	
			foreach ( $terms as $term ) {
				if ( is_numeric( $term ) ) {
					$search_ids[] = $term;
				}
	
				// Attempt to get a SKU
				$sku_to_id = $wpdb->get_results( $wpdb->prepare( "SELECT ID, post_parent FROM {$wpdb->posts} LEFT JOIN {$wpdb->postmeta} ON {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id WHERE meta_key='_sku' AND meta_value LIKE %s;", '%' . $wpdb->esc_like( wc_clean( $term ) ) . '%' ) );
				$sku_to_id = array_merge( wp_list_pluck( $sku_to_id, 'ID' ), wp_list_pluck( $sku_to_id, 'post_parent' ) );
	
				if ( ( $sku_to_id != 0 ) && sizeof( $sku_to_id ) > 0 ) {
					$search_ids = array_merge( $search_ids, $sku_to_id );
				}
			}
			
			if( !empty( $search_ids ) ) {
				if( ( !is_array( $args['include'] ) && $args['include'] == '' ) || ( is_array($args['include']) && empty( $args['include'] ) ) ) {
					$args['include'] = $search_ids;
				} elseif( is_array($args['include']) && !empty( $args['include'] ) ) {
					$args['include'] = array_merge( $args['include'], $search_ids );
				}
			
				$wcfm_sku_search_products_array = get_posts( $args );
				
				if( count( $wcfm_sku_search_products_array ) > 0 ) {
					$wcfm_products_array = array_merge( $wcfm_products_array, $wcfm_sku_search_products_array );
					$filtered_pro_count += count( $wcfm_sku_search_products_array );
				}
			}
		}
		
		// Considerring Variations
		if(!empty($wcfm_products_array)) {
			$non_stockable_product_types = apply_filters( 'wcfm_non_stockable_product_types', array( 'booking', 'accommodation-booking', 'appointment', 'rental', 'auction' ) );
			//print_r($wcfm_products_array);
			$wcfm_products_array_looping = $wcfm_products_array;
			$wcfm_products_array = array();
			$wcfm_products_array_looping_index = 0;
			$new_counter = 199;
			foreach($wcfm_products_array_looping as $wcfm_products_index => $wcfm_products_single) {
				$the_product = wc_get_product( $wcfm_products_single );
				$product_type = $the_product->get_type();
				
				if( in_array( $product_type, $non_stockable_product_types ) ) {
					//$pro_count--;
					$filtered_pro_count--;
					continue;
				}
				
				$wcfm_products_array[$wcfm_products_array_looping_index] = $wcfm_products_single;
				
				if( in_array( $product_type, array( 'variable', 'variable-subscription' ) ) ) {
					$wcfm_product_childs_array = array();
					foreach ( $the_product->get_children() as $child_id ) {
						//$pro_count++;
						$filtered_pro_count++;
						$wcfm_products_array_looping_index++;
						$wcfm_product_childs_array[$wcfm_products_array_looping_index] = get_post( $child_id );
					}
					$wcfm_products_array = array_merge($wcfm_products_array, $wcfm_product_childs_array );
				}
				$wcfm_products_array_looping_index++;
			}
		}
		
		
		
		// Generate Products JSON
		$wcfm_products_json = '';
		$wcfm_products_json = '{
															"draw": ' . $_POST['draw'] . ',
															"recordsTotal": ' . $pro_count . ',
															"recordsFiltered": ' . $filtered_pro_count . ',
															"data": ';
		if(!empty($wcfm_products_array)) {
			$index = 0;
			$wcfm_products_json_arr = array();
			foreach($wcfm_products_array as $wcfm_products_single) {
				$the_product = wc_get_product( $wcfm_products_single );
				$product_type = $the_product->get_type();
				
				// Action Checkbox
				$wcfm_products_json_arr[$index][] =  '<input type="checkbox" class="wcfm-checkbox stock_manage_checkbox_single" name="stock_manage['.$wcfm_products_single->ID.'][process]" value="' . $wcfm_products_single->ID . '" />';
				
				// Title
				$product_edit_id = $wcfm_products_single->ID;
				if( $product_type == 'variation' ) {
					$product_edit_id = $wcfm_products_single->post_parent;
				}
				if( apply_filters( 'wcfm_is_allow_edit_products', true ) && apply_filters( 'wcfm_is_allow_edit_specific_products', true, $product_edit_id ) ) {
					$wcfm_products_json_arr[$index][] =  '<a target="_blank" href="' . get_wcfm_edit_product_url($product_edit_id, $the_product) . '" class="wcfm_product_title">' . $wcfm_products_single->post_title . '</a>';
				} else {
					if( $wcfm_products_single->post_status == 'publish' ) {
						$wcfm_products_json_arr[$index][] =  $wcfm_products_single->post_title;
					} elseif( apply_filters( 'wcfm_is_allow_edit_products', true ) && apply_filters( 'wcfm_is_allow_edit_specific_products', true, $product_edit_id ) ) {
						$wcfm_products_json_arr[$index][] =  apply_filters( 'wcfm_product_title_dashboard', '<a target="_blank" href="' . get_wcfm_edit_product_url($product_edit_id, $the_product) . '" class="wcfm_product_title">' . $wcfm_products_single->post_title . '</a>', $wcfm_products_single->ID );
					} else {
						$wcfm_products_json_arr[$index][] =  apply_filters( 'wcfm_product_title_dashboard', $wcfm_products_single->post_title, $wcfm_products_single->ID );
					}
				}
				
				// SKU
				$product_sku =  ( get_post_meta($wcfm_products_single->ID, '_sku', true) ) ? get_post_meta( $wcfm_products_single->ID, '_sku', true ) : '-';
				$wcfm_products_json_arr[$index][] =  apply_filters( 'wcfm_product_sku_dashboard', $product_sku, $wcfm_products_single->ID );
				
				// Status
				if( $product_type == 'variation' ) {
					$wcfm_products_json_arr[$index][] =  '<span class="product-status product-status-variation">'. __( 'Variation', 'wc-frontend-manager-ultimate' ) . '</span>';
				} elseif( $wcfm_products_single->post_status == 'publish' ) {
					$wcfm_products_json_arr[$index][] =  '<span class="product-status product-status-' . $wcfm_products_single->post_status . '">' . __( 'Published', 'wc-frontend-manager' ) . '</span>';
				} else {
					$wcfm_products_json_arr[$index][] =  '<span class="product-status product-status-' . $wcfm_products_single->post_status . '">' . __( ucfirst( $wcfm_products_single->post_status ), 'wc-frontend-manager' ) . '</span>';
				}
				
				// Vendor
				$vendor_name = '&ndash;';
				if( !$WCFM->is_marketplace || wcfm_is_vendor() ) {
					$wcfm_products_json_arr[$index][] =  $vendor_name;
				} else {
					$vendor_id = wcfm_get_vendor_id_by_post( $wcfm_products_single->ID );
					$store_name = $WCFM->wcfm_vendor_support->wcfm_get_vendor_store_by_vendor( $vendor_id );
					if( $store_name ) {
						$vendor_name = $store_name;
					}
					$wcfm_products_json_arr[$index][] =  $vendor_name;
				}
				
				// Manage Stock?
				$manage_stock = $the_product->managing_stock() ? 'enable' : '';
				if( $manage_stock )
					$manage_stock_options = '<option value="no">' . __( 'NO', 'wc-frontend-manager-ultimate' ) . '</option><option value="yes" selected>' . __( 'YES', 'wc-frontend-manager-ultimate' ) . '</option>';
				else
					$manage_stock_options = '<option value="no">' . __( 'NO', 'wc-frontend-manager-ultimate' ) . '</option><option value="yes">' . __( 'YES', 'wc-frontend-manager-ultimate' ) . '</option>';
				$wcfm_products_json_arr[$index][] =  '<select class="wcfm-select stock_manage" name="stock_manage['.$wcfm_products_single->ID.'][manage_stock]">' . $manage_stock_options . '</select>';
				
				// Stock Status
				$stock_status = $the_product->get_stock_status();
				if( $stock_status == 'instock' )
					$stock_status_options = '<option value="instock" selected>' . __( 'In stock', 'wc-frontend-manager' ) . '</option><option value="outofstock">' . __( 'Out of stock', 'wc-frontend-manager' ) . '</option>';
				else
					$stock_status_options = '<option value="instock">' . __( 'In stock', 'wc-frontend-manager' ) . '</option><option value="outofstock" selected>' . __( 'Out of stock', 'wc-frontend-manager' ) . '</option>';
				$wcfm_products_json_arr[$index][] =  '<select class="wcfm-select" name="stock_manage['.$wcfm_products_single->ID.'][status_options]">' . $stock_status_options . '</select>';
				
				// Backorder
				$backorder = $the_product->get_backorders();
				if( $backorder == 'notify' )
					$backorder_options = '<option value="no">' . __( 'Do not Allow', 'wc-frontend-manager' ) . '</option><option value="notify" selected>' . __( 'Allow, but notify customer', 'wc-frontend-manager' ) . '</option><option value="yes">' . __( 'Allow', 'wc-frontend-manager' ) . '</option>';
				elseif( $backorder == 'yes' )
					$backorder_options = '<option value="no">' . __( 'Do not Allow', 'wc-frontend-manager' ) . '</option><option value="notify">' . __( 'Allow, but notify customer', 'wc-frontend-manager' ) . '</option><option value="yes" selected>' . __( 'Allow', 'wc-frontend-manager' ) . '</option>';
				else
					$backorder_options = '<option value="no" selected>' . __( 'Do not Allow', 'wc-frontend-manager' ) . '</option><option value="notify">' . __( 'Allow, but notify customer', 'wc-frontend-manager' ) . '</option><option value="yes">' . __( 'Allow', 'wc-frontend-manager' ) . '</option>';
				$wcfm_products_json_arr[$index][] =  '<select class="wcfm-select" name="stock_manage['.$wcfm_products_single->ID.'][backorder]">' . $backorder_options . '</select>';
				
				
				// Stock Status
				$stock_qty = $the_product->get_stock_quantity(); 
				$wcfm_products_json_arr[$index][] =  '<input type="number" class="wcfm-text" name="stock_manage['.$wcfm_products_single->ID.'][stock_qty]" value="' . $stock_qty . '" />';
				
				
				$index++;
			}												
		}
		
		if( !empty($wcfm_products_json_arr) ) $wcfm_products_json .= json_encode($wcfm_products_json_arr);
		else $wcfm_products_json .= '[]';
		$wcfm_products_json .= '
													}';
													
		echo $wcfm_products_json;
	}
}