<?php
/**
 * WCFMu plugin controllers
 *
 * Plugin Products Controller
 *
 * @author 		WC Lovers
 * @package 	wcfmu/controllers
 * @version   1.0.0
 */

class WCFMu_Reports_Out_Of_Stock_Controller {
	
	public function __construct() {
		global $WCFM, $WCFMu;
		
		add_filter( 'wcfm_products_actions', array( &$this, 'wcfmu_products_actions' ), 10, 2);
		
	}
	
	public function wcfmu_products_actions( $actions, $the_product ) {
  	global $WCFM, $WCFMu;
  	
  	if( apply_filters( 'wcfm_is_allow_edit_products', true ) ) {
  		//$actions = '<a class="wcfmu_product_quick_edit" href="#" data-product="'. $the_product->get_ID() . '"><span class="wcicon-link text_tip" data-tip="' . esc_attr__( 'Quick Edit', 'wc-frontend-manager-ultimate' ) . '"></span></a>' . $actions;
  	}
  	
  	return $actions;
  }
}