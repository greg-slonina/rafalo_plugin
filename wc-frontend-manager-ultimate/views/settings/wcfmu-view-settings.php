<?php
/**
 * WCFMu plugin Views
 *
 * Plugin Settings View
 *
 * @author 		WC Lovers
 * @package 	wcfmu/views
 * @version   2.2.6
 */
?>

<?php

/**
 * WCFM enpoints edit
 */
add_action( 'wcfm_settings_endpoints', 'wcfmu_settings_endpoints' );

if( !function_exists( 'wcfmu_settings_endpoints' ) ) {
	function wcfmu_settings_endpoints() {
		global $WCFM, $WCFMu;
		
		$wcfm_endpoints = apply_filters( 'wcfm_endpoints_slug', array( 
														'wcfm-products'            => 'products', //get_option( 'wcfm_products_endpoint', 'wc-products' ),
														'wcfm-products-manage'     => 'products-manage', //get_option( 'wcfm_products_simple_endpoint', 'wc-products-simple' ),
														'wcfm-stock-manage'        => 'stock-manage',
														
														// Import/Export
														'wcfm-products-import'     => 'products-import',
														'wcfm-products-export'     => 'products-export',
														
														// Coupon
														'wcfm-coupons'             => 'coupons',
														'wcfm-coupons-manage'      => 'coupons-manage',
														
														// Order
														'wcfm-orders'              => 'orderslist',
														'wcfm-orders-details'      => 'orders-details',
														
														// Reports
														'wcfm-reports-sales-by-date'   => 'reports-sales-by-date',
														'wcfm-reports-sales-by-vendor' => 'reports-sales-by-vendor',
														'wcfm-reports-out-of-stock'    => 'reports-out-of-stock',
														
														// WCFMu Reports
														'wcfm-reports-sales-by-product' => 'reports-sales-by-product',
														'wcfm-reports-coupons-by-date'  => 'reports-coupons-by-date',
														'wcfm-reports-low-in-stock'     => 'reports-low-in-stock',
														
														// Profile
														'wcfm-profile'  => 'profile',
														
														// Settings
														'wcfm-settings'   => 'settings',
														'wcfm-capability' => 'capability',
														
														// Knowledgebase
														'wcfm-knowledgebase'         => 'knowledgebase',
														'wcfm-knowledgebase-manage'  => 'knowledgebase-manage',
														
														// Notices
														'wcfm-notices'        => 'notices',
														'wcfm-notice-manage'  => 'notice-manage',
														'wcfm-notice-view'    => 'notice-view',
														
														// Messages
														'wcfm-messages'  => 'messages',
													) );
		
		$wcfm_modified_endpoints = wcfm_get_option( 'wcfm_endpoints', array() );
		
		// WC 3.6 FIX
		if( isset( $wcfm_modified_endpoints['wcfm-orders'] ) && !empty( $wcfm_modified_endpoints['wcfm-orders'] ) && $wcfm_modified_endpoints['wcfm-orders'] == 'orders' ) $wcfm_modified_endpoints['wcfm-orders'] = 'orderslist';
		if( isset( $wcfm_modified_endpoints['wcfm-booking'] ) && !empty( $wcfm_modified_endpoints['wcfm-booking'] ) && $wcfm_modified_endpoints['wcfm-booking'] == 'booking' ) $wcfm_modified_endpoints['wcfm-booking'] = 'bookinglist';
		if( isset( $wcfm_modified_endpoints['wcfm-bookings'] ) && !empty( $wcfm_modified_endpoints['wcfm-bookings'] ) && $wcfm_modified_endpoints['wcfm-bookings'] == 'bookings' ) $wcfm_modified_endpoints['wcfm-bookings'] = 'bookingslist';
		if( isset( $wcfm_modified_endpoints['wcfm-subscriptions'] ) && !empty( $wcfm_modified_endpoints['wcfm-subscriptions'] ) && $wcfm_modified_endpoints['wcfm-subscriptions'] == 'subscriptions' ) $wcfm_modified_endpoints['wcfm-subscriptions'] = 'subscriptionslist';
		if( isset( $wcfm_modified_endpoints['wcfm-sell-items-catalog'] ) && !empty( $wcfm_modified_endpoints['wcfm-sell-items-catalog'] ) && $wcfm_modified_endpoints['wcfm-sell-items-catalog'] == 'sell-items-catalog' ) $wcfm_modified_endpoints['wcfm-sell-items-catalog'] = 'add-to-my-store-catalog';
		
		$wcfm_endpoints_edit_fileds = array();
		
		foreach( $wcfm_endpoints as $wcfm_endpoint_key => $wcfm_endpoint_val ) {
			$wcfm_endpoints_edit_fileds[$wcfm_endpoint_key] = array( 'label' => $wcfm_endpoint_key, 'name' => 'wcfm_endpoints[' . $wcfm_endpoint_key . ']','type' => 'text', 'class' => 'wcfm-text wcfm_ele wcfm_slug_input', 'placeholder' => $wcfm_endpoint_val, 'value' => ! empty( $wcfm_modified_endpoints[$wcfm_endpoint_key] ) ? $wcfm_modified_endpoints[$wcfm_endpoint_key] : '', 'label_class' => 'wcfm_title' );
		}
		$WCFM->wcfm_fields->wcfm_generate_form_field( $wcfm_endpoints_edit_fileds );
	}
}