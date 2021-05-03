<?php
/**
 * WCFM plugin core
 *
 * WCFM Dokan Subscription core
 *
 * @author 		WC Lovers
 * @package 	wcfm/core
 * @version   4.1.1
 */
 
class WCFMu_Dokan_Subscription {
	
	public $vendor_id;  

	public function __construct() {
		global $WCFM, $WCFMu;
		
		$this->vendor_id   = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );
		
		if( class_exists( 'Dokan_Product_Subscription' ) && WCFM_Dependencies::dokanpro_plugin_active_check() ) {
		  // WCFM Dokan_subscription Query Var Filter - 2.5.3
			add_filter( 'wcfm_query_vars', array( &$this, 'wcfm_dokan_subscription_query_vars' ), 10 );
			add_filter( 'wcfm_endpoint_title', array( &$this, 'wcfm_dokan_subscription_endpoint_title' ), 10, 2 );
			add_action( 'init', array( &$this, 'wcfm_dokan_subscription_init' ), 120 );
			
			if( wcfm_is_vendor() ) {
    	
				// WCFMu Dokan_subscription Load WCFMu views
				add_action( 'wcfm_load_views', array( &$this, 'wcfm_dokan_subscription_load_views' ), 10 );
				
				// Dokan_subscription menu on WCfM dashboard
				if( apply_filters( 'wcfm_is_allow_dokan_subscription', true ) ) {
					add_filter( 'wcfm_menus', array( &$this, 'wcfm_dokan_subscription_menus' ), 30 );
				}
				
				add_filter( 'wcfm_is_allow_product_limit', array( &$this, 'wcfmcap_dokan_is_allow_product_limit' ), 600 );
				add_filter( 'wcfm_products_limit_label', array( &$this, 'wcfmcap_dokan_products_limit_label' ), 60 );
				add_filter( 'wcfm_allowed_taxonomies', array( &$this, 'wcfmcap_dokan_allowed_taxonomies' ), 600, 3 );
			}
		}
		
	}
	
	/**
   * WCMp Query Var
   */
  function wcfm_dokan_subscription_query_vars( $query_vars ) {
  	$wcfm_modified_endpoints = wcfm_get_option( 'wcfm_endpoints', array() );
  	
		$query_wcmp_vars = array(
			//'wcfm-payments'        => ! empty( $wcfm_modified_endpoints['wcfm-payments'] ) ? $wcfm_modified_endpoints['wcfm-payments'] : 'wcfm-payments',
			'wcfm-subscription-packs'      => ! empty( $wcfm_modified_endpoints['wcfm-subscription-packs'] ) ? $wcfm_modified_endpoints['wcfm-subscription-packs'] : 'subscription-packs',
		);
		$query_vars = array_merge( $query_vars, $query_wcmp_vars );
		
		return $query_vars;
  }
  
  /**
   * WCMp End Point Title
   */
  function wcfm_dokan_subscription_endpoint_title( $title, $endpoint ) {
  	
  	switch ( $endpoint ) {
			//case 'wcfm-payments' :
				//$title = __( 'Payments History', 'wc-frontend-manager-ultimate' );
			//break;
			
			case 'wcfm-subscription-packs' :
				$title = __( 'Subscription Packs', 'wc-frontend-manager-ultimate' );
			break;
  	}
  	
  	return $title;
  }
  
  /**
   * WCMp Endpoint Intialize
   */
  function wcfm_dokan_subscription_init() {
  	global $WCFM_Query;
	
		// Intialize WCFM End points
		$WCFM_Query->init_query_vars();
		$WCFM_Query->add_endpoints();
		
		//if( !get_option( 'wcfm_updated_end_point_payment' ) ) {
			// Flush rules after endpoint update
			flush_rewrite_rules();
			update_option( 'wcfm_updated_end_point_dokan_subscription', 1 );
		//}
  }
  
	/**
   * WCFM Dokan_subscription Menu
   */
  function wcfm_dokan_subscription_menus( $menus ) {
  	global $WCFM;
  		
		$menus = array_slice($menus, 0, 3, true) +
												array( 'wcfm-subscription-packs' => array( 'label'  => __( 'Subscription Packs', 'wc-frontend-manager-ultimate' ),
																										 'url'        => wcfm_dokan_subscription_url(),
																										 'icon'       => 'user-plus',
																										 'priority'   => 72
																										) )	 +
													array_slice($menus, 3, count($menus) - 3, true) ;
  	return $menus;
  }
  
	/**
   * WCMp Views
   */
  function wcfm_dokan_subscription_load_views( $end_point ) {
	  global $WCFM, $WCFMu;
	  
	  switch( $end_point ) {
      case 'wcfm-subscription-packs':
      	$WCFMu->template->get_template( 'dokan-subscription/wcfmu-view-dokan-subscription.php' );
      break;
	  }
	}
	
	// WCFM wcfmcap Add Products
  function wcfmcap_dokan_is_allow_product_limit( $allow ) {
  	if ( class_exists( 'Dokan_Product_Subscription' ) && dokan_is_seller_enabled( $this->vendor_id ) ) {
			//$remaining_product = Helper::get_vendor_remaining_products( get_current_user_id() );
			$vendor = dokan()->vendor->get( $this->vendor_id )->subscription;
			if ( ! $vendor ) {
				$remaining_product = 0;
			} else {
        $remaining_product = $vendor->get_remaining_products();
      }
			if ( $remaining_product == 0 || !Dokan_Product_Subscription::can_post_product() ) {
				$allow = false;
			} else {
				$productlimit = apply_filters( 'wcfm_vendor_verification_product_limit', $remaining_product );
				if( $productlimit ) $productlimit = absint($productlimit);
				if( $productlimit && ( $productlimit >= 0 ) ) {
					if( $productlimit == 1989 ) {
						$allow = false;
					} else {
						//$dps               = Dokan_Product_Subscription::init();
						$remaining_product =  $productlimit; //- $dps->get_number_of_product_by_seller( $this->vendor_id );
						if ( $remaining_product == 0 ) {
							$allow = false;
						}
					}
				}
			}
		}
  	return $allow;
  }
  
  // WCFM Product Limit Label
  function wcfmcap_dokan_products_limit_label( $label ) {
  	
  	if ( class_exists( 'Dokan_Product_Subscription' ) && dokan_is_seller_enabled( $this->vendor_id ) ) {
			//$remaining_product = Helper::get_vendor_remaining_products( get_current_user_id() );
			$vendor = dokan()->vendor->get( $this->vendor_id )->subscription;
			if ( ! $vendor ) {
				$remaining_product = 0;
			} else {
        $remaining_product = $vendor->get_remaining_products();
      }
			$label = __( 'Products Limit: ', 'wc-frontend-manager' );
			if ( $remaining_product == 0 || !Dokan_Product_Subscription::can_post_product() ) {
  			$label .= ' 0 ' . __( 'remaining', 'wc-frontend-manager' );
  		} else {
  			$productlimit = apply_filters( 'wcfm_vendor_verification_product_limit', $remaining_product );
				if( $productlimit ) $productlimit = absint($productlimit);
				if( $productlimit && ( $productlimit >= 0 ) ) {
					if( $productlimit == 1989 ) {
						$label .= ' 0 ' . __( 'remaining', 'wc-frontend-manager' );
					} else {
						//$dps             = Dokan_Product_Subscription::init();
						$remaining_product =  $productlimit; //- $dps->get_number_of_product_by_seller( $this->vendor_id );
						if ( $remaining_product == 0 ) {
							$label .= ' 0 ' . __( 'remaining', 'wc-frontend-manager' );
						} else {
							$label .= ' ' . $remaining_product . ' ' . __( 'remaining', 'wc-frontend-manager' );
						}
					}
				} else {
					$label .= ' ' . $remaining_product . ' ' . __( 'remaining', 'wc-frontend-manager' );
				}
  		}
  		$label = '<span class="wcfm_products_limit_label">' . $label . '</span>';
  	}
  	
  	return $label;
  }
  
  // Allowed Taxonomies
  function wcfmcap_dokan_allowed_taxonomies( $allow, $taxonomy, $term_id ) {
		global $WCFM, $WCFMu;
		
		if( $taxonomy == 'product_cat' ) {
			$taxonomy = 'categories';
			if ( class_exists( 'Dokan_Product_Subscription' ) && dokan_is_seller_enabled( $this->vendor_id ) ) {
				$can_post_product  = get_user_meta( $this->vendor_id, 'can_post_product', true );
        $subscription_pack_id  = get_user_meta( $this->vendor_id, 'product_package_id', true );

        if ( ( $can_post_product == '1' ) && $subscription_pack_id ) {
					$override_cat = get_user_meta( $this->vendor_id, 'vendor_allowed_categories', true );
					$selected_cat = !empty( $override_cat ) ? $override_cat : get_post_meta( $subscription_pack_id, '_vendor_allowed_categories', true );
					
					if ( !empty( $selected_cat ) ) {
						if( !in_array( $term_id, $selected_cat ) ) {
							$allow = false;
						}
					}
        }
			}
		}
		
		return $allow;
	}
	
}