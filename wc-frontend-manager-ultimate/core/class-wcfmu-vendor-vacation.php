<?php
/**
 * WCFMu plugin core
 *
 * Plugin Vendor Vacation Controler
 *
 * @author 		WC Lovers
 * @package 	wcfmu/core
 * @version   4.1.8
 */
 
class WCFMu_Vendor_Vacation {
	
	public function __construct() {
		
		// Vacation Mode Checking
		add_filter( 'woocommerce_is_purchasable', array( &$this, 'wcfm_store_vacation_mode_is_purchasable' ), 600, 2 );
		
		//add_action( 'wcv_after_vendor_store_header',			array( &$this, 'wcfm_vacation_mode' ) );
		//add_action( 'woocommerce_before_single_product',			array( &$this, 'wcfm_vacation_mode' ) );
		if( apply_filters( 'wcfm_is_allow_vacation_message_before_main_content', false ) ) {
			add_action( 'woocommerce_before_main_content',			array( &$this, 'wcfm_vacation_mode' ) );
		}
		
		// Loop Add to Cart URL check 
		add_filter( 'woocommerce_product_add_to_cart_url', array( &$this, 'wcfm_vacation_mode_check_loop_add_to_cart_url' ), 100, 2 );
		
		add_action( 'woocommerce_after_shop_loop_item',	array( &$this, 'wcfm_vacation_mode' ), 9 );
		
		add_action( 'woocommerce_single_product_summary', array( &$this, 'wcfm_vacation_mode' ), 25 );
		
		// YiTH Quick View Vacation Message Show
		add_action( 'yith_wcqv_product_summary', array( &$this, 'wcfm_vacation_mode' ), 24 );
		
		// Flatsome Quick View Vacation Message Show
		add_action( 'woocommerce_single_product_lightbox_summary', array( &$this, 'wcfm_vacation_mode' ), 29 );
		
		// WooCommerce Quick View Pro Vacation Message Show
		add_action( 'wc_quick_view_pro_quick_view_product_details', array( &$this, 'wcfm_vacation_mode' ), 29 );
		
		// WCFM Marketplace Stroe Page
		add_action( 'wcfmmp_before_store_product', array( &$this, 'wcfm_vacation_mode' ), 25 );
		
		//add_action( 'wcfmmp_before_store_product', array( &$this, 'wcfm_vacation_mode' ), 25 );
	}
	
	/**
	 * Restrict Store Product Purchase at OFF Time
	 */
	function wcfm_store_vacation_mode_is_purchasable( $is_purchasable, $product ) {
		global $WCFM, $WCFMmp;
		
		$is_marketplace = wcfm_is_marketplace();
		$product_id = $product->get_id();
		if( $product_id ) {
			$vendor_id = wcfm_get_vendor_id_by_post( $product_id );
			if( $vendor_id ) {
				$vendor_has_vacation = $WCFM->wcfm_vendor_support->wcfm_vendor_has_capability( $vendor_id, 'vacation' );
				if( $vendor_has_vacation ) {
					if( $is_marketplace == 'wcfmmarketplace' ) {
						$vendor_data = get_user_meta( $vendor_id, 'wcfmmp_profile_settings', true );
						$vacation_mode = isset( $vendor_data['wcfm_vacation_mode'] ) ? $vendor_data['wcfm_vacation_mode'] : 'no';
						$disable_vacation_purchase = isset( $vendor_data['wcfm_disable_vacation_purchase'] ) ? $vendor_data['wcfm_disable_vacation_purchase'] : 'no';
						$wcfm_vacation_mode_type = isset( $vendor_data['wcfm_vacation_mode_type'] ) ? $vendor_data['wcfm_vacation_mode_type'] : 'instant';
						$wcfm_vacation_start_date = isset( $vendor_data['wcfm_vacation_start_date'] ) ? $vendor_data['wcfm_vacation_start_date'] : '';
						$wcfm_vacation_end_date = isset( $vendor_data['wcfm_vacation_end_date'] ) ? $vendor_data['wcfm_vacation_end_date'] : '';
						$vacation_msg = ! empty( $vendor_data['wcfm_vacation_mode_msg'] ) ? $vendor_data['wcfm_vacation_mode_msg'] : '';
					} else {
						$vacation_mode 		= ( get_user_meta( $vendor_id, 'wcfm_vacation_mode', true ) ) ? get_user_meta( $vendor_id, 'wcfm_vacation_mode', true ) : 'no';
						$disable_vacation_purchase = ( get_user_meta( $vendor_id, 'wcfm_disable_vacation_purchase', true ) ) ? get_user_meta( $vendor_id, 'wcfm_disable_vacation_purchase', true ) : 'no';
						$wcfm_vacation_mode_type = ( get_user_meta( $vendor_id, 'wcfm_vacation_mode_type', true ) ) ? get_user_meta( $vendor_id, 'wcfm_vacation_mode_type', true ) : 'instant';
						$wcfm_vacation_start_date = ( get_user_meta( $vendor_id, 'wcfm_vacation_start_date', true ) ) ? get_user_meta( $vendor_id, 'wcfm_vacation_start_date', true ) : '';
						$wcfm_vacation_end_date = ( get_user_meta( $vendor_id, 'wcfm_vacation_end_date', true ) ) ? get_user_meta( $vendor_id, 'wcfm_vacation_end_date', true ) : '';
						$vacation_msg 		= ( $vacation_mode ) ? get_user_meta( $vendor_id , 'wcfm_vacation_mode_msg', true ) : ''; 
					}
					
					if( ( $vacation_mode == 'yes' ) && ( $disable_vacation_purchase == 'yes' ) ) {
						if( $wcfm_vacation_mode_type == 'instant' ) {
							$is_purchasable = false;
						} elseif( $wcfm_vacation_start_date && $wcfm_vacation_end_date ) {
							$current_time = strtotime( 'midnight', current_time( 'timestamp' ) );
							$start_time = strtotime( $wcfm_vacation_start_date );
							$end_time = strtotime( $wcfm_vacation_end_date );
							if( ($current_time >= $start_time) && ($current_time <= $end_time) ) {
								remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
								$is_purchasable = false;
							}
						}
					}
				}
			}
		}
		
		return $is_purchasable;
	}
	
	function wcfm_vacation_mode_check_loop_add_to_cart_url( $add_to_cart_url, $product ) {
		
		$is_purchasable = $this->wcfm_store_vacation_mode_is_purchasable( true, $product );
		if( !$is_purchasable ) $add_to_cart_url = '';
		
		return $add_to_cart_url;
	}
	
	/**
	 * Show Vacation mode Message above vendor store
	 *
	 * @since 2.3.1
	 */
	public function wcfm_vacation_mode() {
		global $WCFM, $WCFMu;
		
		$vendor_id   		= 0;
		$vacation_mode = 'no';
		$disable_vacation_purchase = 'no'; 
		$vacation_msg = '';
		$is_marketplace = wcfm_is_marketplace();
		if( !$is_marketplace ) return;
		
		if ( ( !function_exists( 'wcfm_is_store_page' ) || ( function_exists( 'wcfm_is_store_page' ) && !wcfm_is_store_page() ) ) && ( is_product() || is_shop() || is_product_category() ) ) {
			global $product, $post; 
			if ( is_object( $product ) ) { 
				$vendor_id   		= wcfm_get_vendor_id_by_post( $product->get_id() ); 
			} else if ( is_product() ) {
				$vendor_id   		= wcfm_get_vendor_id_by_post( $post->ID );
			}
			if( $vendor_id ) {
				$vendor_has_vacation = $WCFM->wcfm_vendor_support->wcfm_vendor_has_capability( $vendor_id, 'vacation' );
				if( !$vendor_has_vacation ) return;
				
				if( $is_marketplace == 'wcpvendors' ) {
					$vendor_data = get_term_meta( $vendor_id, 'vendor_data', true );
					$vacation_mode = isset( $vendor_data['wcfm_vacation_mode'] ) ? $vendor_data['wcfm_vacation_mode'] : 'no';
					$disable_vacation_purchase = isset( $vendor_data['wcfm_disable_vacation_purchase'] ) ? $vendor_data['wcfm_disable_vacation_purchase'] : 'no';
					$wcfm_vacation_mode_type = isset( $vendor_data['wcfm_vacation_mode_type'] ) ? $vendor_data['wcfm_vacation_mode_type'] : 'instant';
					$wcfm_vacation_start_date = isset( $vendor_data['wcfm_vacation_start_date'] ) ? $vendor_data['wcfm_vacation_start_date'] : '';
					$wcfm_vacation_end_date = isset( $vendor_data['wcfm_vacation_end_date'] ) ? $vendor_data['wcfm_vacation_end_date'] : '';
					$vacation_msg = ! empty( $vendor_data['wcfm_vacation_mode_msg'] ) ? $vendor_data['wcfm_vacation_mode_msg'] : '';
					$vendor_id = 0;
				} elseif( $is_marketplace == 'dokan' ) {
					$vendor_data = get_user_meta( $vendor_id, 'dokan_profile_settings', true );
					$vacation_mode = isset( $vendor_data['wcfm_vacation_mode'] ) ? $vendor_data['wcfm_vacation_mode'] : 'no';
					$disable_vacation_purchase = isset( $vendor_data['wcfm_disable_vacation_purchase'] ) ? $vendor_data['wcfm_disable_vacation_purchase'] : 'no';
					$wcfm_vacation_mode_type = isset( $vendor_data['wcfm_vacation_mode_type'] ) ? $vendor_data['wcfm_vacation_mode_type'] : 'instant';
					$wcfm_vacation_start_date = isset( $vendor_data['wcfm_vacation_start_date'] ) ? $vendor_data['wcfm_vacation_start_date'] : '';
					$wcfm_vacation_end_date = isset( $vendor_data['wcfm_vacation_end_date'] ) ? $vendor_data['wcfm_vacation_end_date'] : '';
					$vacation_msg = ! empty( $vendor_data['wcfm_vacation_mode_msg'] ) ? $vendor_data['wcfm_vacation_mode_msg'] : '';
					$vendor_id = 0;
				}
			}
		} elseif( did_action( 'woocommerce_after_shop_loop_item' ) || did_action( 'yith_wcqv_product_summary' ) || did_action( 'woocommerce_single_product_lightbox_summary' ) || did_action( 'wc_quick_view_pro_quick_view_product_details' ) ) {
			global $product; 
			if ( is_object( $product ) ) { 
				$vendor_id   		= wcfm_get_vendor_id_by_post( $product->get_id() ); 
			}
		} else {
			if( $is_marketplace == 'wcvendors' ) {
				if ( WCV_Vendors::is_vendor_page() ) {
					$vendor_shop 		= urldecode( get_query_var( 'vendor_shop' ) );
					$vendor_id   		= WCV_Vendors::get_vendor_id( $vendor_shop ); 
				}
			} elseif( $is_marketplace == 'wcmarketplace' ) {
		  	if (is_tax('dc_vendor_shop')) {
		  		$vendor = get_wcmp_vendor_by_term(get_queried_object()->term_id);
		  		$vendor_id   		= $vendor->id;
		  	}
		  } elseif( $is_marketplace == 'wcpvendors' ) {
		  	if (is_tax('wcpv_product_vendors')) {
		  		$vendor_shop = get_queried_object()->term_id;
		  		$vendor_data = get_term_meta( $vendor_shop, 'vendor_data', true );
		  		$vacation_mode = isset( $vendor_data['wcfm_vacation_mode'] ) ? $vendor_data['wcfm_vacation_mode'] : 'no';
		  		$disable_vacation_purchase = isset( $vendor_data['wcfm_disable_vacation_purchase'] ) ? $vendor_data['wcfm_disable_vacation_purchase'] : 'no';
		  		$wcfm_vacation_mode_type = isset( $vendor_data['wcfm_vacation_mode_type'] ) ? $vendor_data['wcfm_vacation_mode_type'] : 'instant';
		  		$wcfm_vacation_start_date = isset( $vendor_data['wcfm_vacation_start_date'] ) ? $vendor_data['wcfm_vacation_start_date'] : '';
		  		$wcfm_vacation_end_date = isset( $vendor_data['wcfm_vacation_end_date'] ) ? $vendor_data['wcfm_vacation_end_date'] : '';
		  		$vacation_msg = ! empty( $vendor_data['wcfm_vacation_mode_msg'] ) ? $vendor_data['wcfm_vacation_mode_msg'] : '';
		  	}
		  } elseif( $is_marketplace == 'dokan' ) {
		  	if( dokan_is_store_page() ) {
		  		$custom_store_url = dokan_get_option( 'custom_store_url', 'dokan_general', 'store' );
		  		$store_name = get_query_var( $custom_store_url );
		  		$vendor_id  = 0;
		  		if ( !empty( $store_name ) ) {
            $store_user = get_user_by( 'slug', $store_name );
          }
		  		$vendor_data = get_user_meta( $store_user->ID, 'dokan_profile_settings', true );
		  		$vacation_mode = isset( $vendor_data['wcfm_vacation_mode'] ) ? $vendor_data['wcfm_vacation_mode'] : 'no';
					$disable_vacation_purchase = isset( $vendor_data['wcfm_disable_vacation_purchase'] ) ? $vendor_data['wcfm_disable_vacation_purchase'] : 'no';
					$wcfm_vacation_mode_type = isset( $vendor_data['wcfm_vacation_mode_type'] ) ? $vendor_data['wcfm_vacation_mode_type'] : 'instant';
					$wcfm_vacation_start_date = isset( $vendor_data['wcfm_vacation_start_date'] ) ? $vendor_data['wcfm_vacation_start_date'] : '';
					$wcfm_vacation_end_date = isset( $vendor_data['wcfm_vacation_end_date'] ) ? $vendor_data['wcfm_vacation_end_date'] : '';
					$vacation_msg = ! empty( $vendor_data['wcfm_vacation_mode_msg'] ) ? $vendor_data['wcfm_vacation_mode_msg'] : '';
		  	}
		  } elseif( $is_marketplace == 'wcfmmarketplace' ) {
		  	if( wcfm_is_store_page() ) {
		  		$custom_store_url = get_option( 'wcfm_store_url', 'store' );
		  		$store_name = get_query_var( $custom_store_url );
		  		if ( !empty( $store_name ) ) {
            $store_user = get_user_by( 'slug', $store_name );
						if( $store_user ) {
							$vendor_id  = $store_user->ID;
						}
					}
		  	}
		  }
		}

		if ( $vendor_id ) {
			$vendor_has_vacation = $WCFM->wcfm_vendor_support->wcfm_vendor_has_capability( $vendor_id, 'vacation' );
			if( !$vendor_has_vacation ) return;
			
			if( $is_marketplace == 'wcfmmarketplace' ) {
				$vendor_data = get_user_meta( $vendor_id, 'wcfmmp_profile_settings', true );
				$vacation_mode = isset( $vendor_data['wcfm_vacation_mode'] ) ? $vendor_data['wcfm_vacation_mode'] : 'no';
				$disable_vacation_purchase = isset( $vendor_data['wcfm_disable_vacation_purchase'] ) ? $vendor_data['wcfm_disable_vacation_purchase'] : 'no';
				$wcfm_vacation_mode_type = isset( $vendor_data['wcfm_vacation_mode_type'] ) ? $vendor_data['wcfm_vacation_mode_type'] : 'instant';
				$wcfm_vacation_start_date = isset( $vendor_data['wcfm_vacation_start_date'] ) ? $vendor_data['wcfm_vacation_start_date'] : '';
				$wcfm_vacation_end_date = isset( $vendor_data['wcfm_vacation_end_date'] ) ? $vendor_data['wcfm_vacation_end_date'] : '';
				$vacation_msg = ! empty( $vendor_data['wcfm_vacation_mode_msg'] ) ? $vendor_data['wcfm_vacation_mode_msg'] : '';
			} else {
				$vacation_mode 		= ( get_user_meta( $vendor_id, 'wcfm_vacation_mode', true ) ) ? get_user_meta( $vendor_id, 'wcfm_vacation_mode', true ) : 'no';
				$disable_vacation_purchase = ( get_user_meta( $vendor_id, 'wcfm_disable_vacation_purchase', true ) ) ? get_user_meta( $vendor_id, 'wcfm_disable_vacation_purchase', true ) : 'no';
				$wcfm_vacation_mode_type = ( get_user_meta( $vendor_id, 'wcfm_vacation_mode_type', true ) ) ? get_user_meta( $vendor_id, 'wcfm_vacation_mode_type', true ) : 'instant';
				$wcfm_vacation_start_date = ( get_user_meta( $vendor_id, 'wcfm_vacation_start_date', true ) ) ? get_user_meta( $vendor_id, 'wcfm_vacation_start_date', true ) : '';
				$wcfm_vacation_end_date = ( get_user_meta( $vendor_id, 'wcfm_vacation_end_date', true ) ) ? get_user_meta( $vendor_id, 'wcfm_vacation_end_date', true ) : '';
				$vacation_msg 		= ( $vacation_mode ) ? get_user_meta( $vendor_id , 'wcfm_vacation_mode_msg', true ) : ''; 
			}
		}
		
		$disable_vacation_purchase = apply_filters( 'wcfm_disable_vacation_purchase', $disable_vacation_purchase );
		
		if( ( $vacation_mode == 'yes' ) && ( $disable_vacation_purchase == 'yes' ) ) {
			if( $wcfm_vacation_mode_type == 'instant' ) {
				$WCFMu->wcfm_has_vacation = true;
				add_filter( 'woocommerce_is_purchasable', '__return_false' );
				remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
				remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
				
				// YiTH Quick View Support
				remove_action( 'yith_wcqv_product_summary', 'woocommerce_template_single_add_to_cart', 25 );
				
				// Flatsome Quick View Support
				remove_action( 'woocommerce_single_product_lightbox_summary', 'woocommerce_template_single_add_to_cart', 30 );
				
				// WooCommerce Quick View Pro Support
				remove_action( 'wc_quick_view_pro_quick_view_product_details', 'woocommerce_template_single_add_to_cart', 30 );
			} elseif( $wcfm_vacation_start_date && $wcfm_vacation_end_date ) {
				$current_time = strtotime( 'midnight', current_time( 'timestamp' ) );
				$start_time = strtotime( $wcfm_vacation_start_date );
				$end_time = strtotime( $wcfm_vacation_end_date );
				if( ($current_time >= $start_time) && ($current_time <= $end_time) ) {
					$WCFMu->wcfm_has_vacation = true;
					add_filter( 'woocommerce_is_purchasable', '__return_false' );
					remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
					remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
					
					// YiTH Quick View Support
					remove_action( 'yith_wcqv_product_summary', 'woocommerce_template_single_add_to_cart', 25 );
					
					// Flatsome Quick View Support
					remove_action( 'woocommerce_single_product_lightbox_summary', 'woocommerce_template_single_add_to_cart', 30 );
					
					// WooCommerce Quick View Pro Support
					remove_action( 'wc_quick_view_pro_quick_view_product_details', 'woocommerce_template_single_add_to_cart', 30 );
				}
			}
		} else {
			if( apply_filters( 'wcfm_is_allow_add_to_cart_restore', true ) && $WCFMu->wcfm_has_vacation ) {
				add_filter( 'woocommerce_is_purchasable', '__return_true' );
				if ( apply_filters( 'wcfm_is_allow_add_to_cart_restore', true ) && !function_exists( 'rehub_option' ) && !function_exists( 'astra_header' ) && !function_exists( 'zita_post_loader' ) && !function_exists( 'oceanwp_get_sidebar' ) && !function_exists( 'martfury_content_columns' ) && !function_exists( 'x_get_stack' ) ) {
					add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
				}
				//add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
			}
		}
		
		if ( function_exists( 'wcfm_is_store_page' ) && wcfm_is_store_page() && did_action( 'woocommerce_after_shop_loop_item' ) ) return;
		
		if( did_action( 'woocommerce_after_shop_loop_item' ) && !apply_filters( 'wcfm_is_allow_vacation_message_after_shop_loop_item', true ) ) return;
		
		//if( did_action( 'woocommerce_before_main_content' ) && !apply_filters( 'wcfm_is_allow_vacation_message_before_main_content', false ) ) return;
		
		if( did_action( 'woocommerce_single_product_summary' ) && !apply_filters( 'wcfm_is_allow_vacation_message_single_product_summary', true ) ) return;
		
		
		$vacation_msg = apply_filters( 'wcfm_vacation_message_text', $vacation_msg );

		if ( $vacation_mode == 'yes' ) {
			if( $wcfm_vacation_mode_type == 'instant' ) {
			?>
			<div class="wcfm_vacation_msg">
				<?php echo $vacation_msg; ?>
			</div>
		<?php 
			} elseif( $wcfm_vacation_start_date && $wcfm_vacation_end_date ) {
				$current_time = strtotime( 'midnight', current_time( 'timestamp' ) );
				$start_time = strtotime( $wcfm_vacation_start_date );
				$end_time = strtotime( $wcfm_vacation_end_date );
				if( ($current_time >= $start_time) && ($current_time <= $end_time) ) {
					?>
						<div class="wcfm_vacation_msg">
							<?php echo $vacation_msg; ?>
						</div>
					<?php 
				}
			}
		}

	}
}