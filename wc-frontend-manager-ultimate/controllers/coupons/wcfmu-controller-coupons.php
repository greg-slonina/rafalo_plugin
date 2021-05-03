<?php
/**
 * WCFMu plugin controllers
 *
 * Plugin Coupons Controller
 *
 * @author 		WC Lovers
 * @package 	wcfmu/controllers
 * @version   1.0.0
 */

class WCFMu_Coupons_Controller {
	
	public function __construct() {
		global $WCFM, $WCFMu;
		
		add_filter( 'wcfm_coupons_actions', array( &$this, 'wcfmu_coupons_actions' ), 10, 2);
		
		add_filter ( 'wcfm_coupons_args', array( &$this, 'wcfmu_coupons_args' ) );
	}
	
	public function wcfmu_coupons_actions( $actions, $wcfm_coupons_single ) {
  	global $WCFM, $WCFMu;
  	
  	if( $wcfm_coupons_single->post_status == 'publish' ) {
  	  $actions .= ( current_user_can( 'delete_published_shop_coupons' ) ) ? '<a class="wcfm_coupon_delete wcfm-action-icon" href="#" data-couponid="' . $wcfm_coupons_single->ID . '"><span class="wcfmfa fa-trash-alt text_tip" data-tip="' . esc_attr__( 'Delete', 'wc-frontend-manager-ultimate' ) . '"></span></a>' : '';
  	} else {
  		$actions .= ( current_user_can( 'delete_shop_coupons' ) ) ? '<a class="wcfm_coupon_delete wcfm-action-icon" href="#" data-couponid="' . $wcfm_coupons_single->ID . '"><span class="wcfmfa fa-trash-alt text_tip" data-tip="' . esc_attr__( 'Delete', 'wc-frontend-manager-ultimate' ) . '"></span></a>' : '';
  	}
  	
  	return $actions;
  }
  
  public function wcfmu_coupons_args( $args ) {
  	global $WCFM, $WCFMu, $_POST;
  	
  	if( isset($_POST['coupon_type']) && !empty($_POST['coupon_type']) ) {
			$args['meta_value']    = $_POST['coupon_type'];
			$args['meta_key']      = 'discount_type';
		}
		
		return $args;
  }
  
}