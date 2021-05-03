<?php
/**
 * WCFMu plugin controllers
 *
 * Plugin Orders Controller
 *
 * @author 		WC Lovers
 * @package 	wcfmu/controllers
 * @version   1.0.0
 */

class WCFMu_Orders_Controller {
	
	public function __construct() {
		global $WCFM, $WCFMu;
		
		add_filter( 'wcfm_orders_args', array( &$this, 'wcfmu_orders_args' ) );
		
		add_filter( 'wcfm_orders_actions', array( &$this, 'wcfmu_orders_actions' ), 10, 3 );
		
		if( apply_filters( 'wcfm_is_allow_vendor_order_delete', false ) && apply_filters( 'wcfm_is_allow_order_delete', true ) ) {
			add_filter( 'wcfmmarketplace_orders_actions', array( &$this, 'wcfmu_vendor_orders_actions' ), 10, 4 );
		}
	}
	
	/**
	 * WCFMu Orders args Filter
	 */
	function wcfmu_orders_args( $args ) {
		global $WCFM, $WCFMu;
		
		if( isset( $_POST['order_status'] ) && !empty( $_POST['order_status'] ) ) { $args['post_status'] = 'wc-' . $_POST['order_status']; }
		
		return $args;
	}
	
	public function wcfmu_orders_actions( $actions, $wcfm_orders_single, $the_order ) {
  	global $WCFM, $WCFMu;
  	
  	if( apply_filters( 'wcfm_is_allow_order_delete', true ) ) {
  		$actions .= '<a class="wcfm_order_delete wcfm-action-icon" href="#" data-orderid="' . $the_order->get_id() . '"><span class="wcfmfa fa-trash-alt text_tip" data-tip="' . esc_attr__( 'Delete', 'wc-frontend-manager-ultimate' ) . '"></span></a>';
  	}
  	
  	return $actions;
  }
  
  public function wcfmu_vendor_orders_actions( $actions, $user_id, $order, $the_order ) {
  	global $WCFM, $WCFMu;
  	
  	$actions .= '<a class="wcfm_order_delete wcfm-action-icon" href="#" data-orderid="' . $the_order->get_id() . '"><span class="wcfmfa fa-trash-alt text_tip" data-tip="' . esc_attr__( 'Delete', 'wc-frontend-manager-ultimate' ) . '"></span></a>';
  	
  	return $actions;
  }
}