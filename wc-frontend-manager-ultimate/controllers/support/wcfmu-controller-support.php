<?php
/**
 * WCFM plugin controllers
 *
 * Plugin Support Controller
 *
 * @author 		WC Lovers
 * @package 	wcfmu/controllers/support
 * @version   4.0.3
 */

class WCFMu_Support_Controller {
	
	public function __construct() {
		global $WCFM;
		
		if( !defined('WCFM_REST_API_CALL') ) {
			$this->processing();
		}
	}
	
	public function processing() {
		global $WCFM, $WCFMu, $wpdb, $_POST;
		
		$length = $_POST['length'];
		$offset = $_POST['start'];
		
		$vendor_id = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );
		
		$support_status = '';
		if ( ! empty( $_POST['support_status'] ) ) {
			$support_status = esc_sql( $_POST['support_status'] );
			if( $support_status == 'all' ) $support_status = '';
		}
		
		$support_product = '';
		if ( ! empty( $_POST['support_product'] ) ) {
			$support_product = esc_sql( $_POST['support_product'] );
		}
		
		$support_vendor = '';
		if ( ! empty( $_POST['support_vendor'] ) ) {
			$support_vendor = esc_sql( $_POST['support_vendor'] );
		}
		
		$support_priority = '';
		if ( ! empty( $_POST['support_priority'] ) ) {
			$support_priority = esc_sql( $_POST['support_priority'] );
			if( $support_priority == 'all' ) $support_priority = '';
		}
		
		$time_filter = '';
		
		$the_orderby = ! empty( $_POST['orderby'] ) ? sanitize_text_field( $_POST['orderby'] ) : 'ID';
		$the_order   = ( ! empty( $_POST['order'] ) && 'asc' === $_POST['order'] ) ? 'ASC' : 'DESC';
		
		$items_per_page = $length;
		
		$sql = "SELECT count(ID) FROM {$wpdb->prefix}wcfm_support AS commission";
		$sql .= " WHERE 1 = 1";
		
		if( $support_product ){
			$sql .= " AND `product_id` = {$support_product}";
		}
		
		if( $support_priority ){
			$sql .= " AND `priority` = '{$support_priority}'";
		}
		
		if( $support_status ){
			$sql .= " AND `status` = '{$support_status}'";
		}
		
		if( wcfm_is_vendor() ) { 
			$sql .= " AND `vendor_id` = {$vendor_id}";
		} elseif ( ! empty( $_POST['support_vendor'] ) ) {
			$sql .= " AND `vendor_id` = {$support_vendor}";
		}
		if ( ! empty( $_POST['filter_date_form'] ) && ! empty( $_POST['filter_date_to'] ) ) {
			$start_date = date( 'Y-m-d', strtotime( $_POST['filter_date_form'] ) );
			$end_date = date( 'Y-m-d', strtotime( $_POST['filter_date_to'] ) );
			$time_filter = " AND DATE( commission.posted ) BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
			$sql .= $time_filter;
		}
		$sql = apply_filters( 'wcfm_support_count_query', $sql);
		
		$total_supports = $wpdb->get_var( $sql );
		
		$support_query = "SELECT * FROM {$wpdb->prefix}wcfm_support AS commission";
		$support_query .= " WHERE 1 = 1";
		
		if( $support_product ) {
			$support_query .= " AND `product_id` = {$support_product}";
		}
		
		if( $support_status ) {
			$support_query .= " AND `status` = '{$support_status}'";
		}
		
		if( $support_priority ) {
			$support_query .= " AND `priority` = '{$support_priority}'";
		}
		
		if( wcfm_is_vendor() ) { 
			$support_query .= " AND `vendor_id` = {$vendor_id}";
		} elseif ( ! empty( $_POST['support_vendor'] ) ) {
			$support_query .= " AND `vendor_id` = {$support_vendor}";
		}
		if ( ! empty( $_POST['filter_date_form'] ) && ! empty( $_POST['filter_date_to'] ) ) {
			$support_query .= $time_filter;
		}
		$support_query = apply_filters( 'wcfm_support_list_query', $support_query );
		
		$support_query .= " ORDER BY commission.`{$the_orderby}` {$the_order}";

		$support_query .= " LIMIT {$items_per_page}";

		$support_query .= " OFFSET {$offset}";
		
		
		$wcfm_supports_array = $wpdb->get_results( $support_query );
		
		if( defined('WCFM_REST_API_CALL') ) {
			return $wcfm_supports_array;
		}
		
		// Generate Supports JSON
		$wcfm_supports_json = '';
		$wcfm_supports_json = '{
															"draw": ' . $_POST['draw'] . ',
															"recordsTotal": ' . $total_supports . ',
															"recordsFiltered": ' . $total_supports . ',
															"data": ';
		if(!empty($wcfm_supports_array)) {
			$index = 0;
			$wcfm_supports_json_arr = array();
			foreach($wcfm_supports_array as $wcfm_supports_single) {
				// Status
				if( $wcfm_supports_single->status == 'open' ) {
					$wcfm_supports_json_arr[$index][] =  '<span class="support-status tips wcicon-status-processing text_tip" data-tip="' . __( 'Open', 'wc-frontend-manager-ultimate' ) . '"></span>';
				} else {
					$wcfm_supports_json_arr[$index][] =  '<span class="support-status tips wcicon-status-completed text_tip" data-tip="' . __( 'Closed', 'wc-frontend-manager-ultimate' ) . '"></span>';
				}
				
				// Ticket
				$wcfm_supports_json_arr[$index][] =  '<a href="' . get_wcfm_support_manage_url($wcfm_supports_single->ID) . '" class="wcfm_dashboard_item_title">' . '#' . sprintf( '%06u', $wcfm_supports_single->ID ) . '</a>';
				
				// Category
				$wcfm_supports_json_arr[$index][] =  $wcfm_supports_single->category;
				
				// Issue
				if( $wcfm_supports_single->query ) {
					$wcfm_supports_json_arr[$index][] =  $wcfm_supports_single->query;
				} else {
					$wcfm_supports_json_arr[$index][] = '&ndash;'; 
				}
				
				// Order
				if( apply_filters( 'wcfm_is_allow_order_details', true ) && $WCFM->wcfm_vendor_support->wcfm_is_order_for_vendor( $wcfm_supports_single->order_id ) ) {
					$wcfm_supports_json_arr[$index][] =  '<a target="_blank" href="' . get_wcfm_view_order_url($wcfm_supports_single->order_id) . '" class="wcfm_dashboard_item_title">' . __( 'Order', 'wc-frontend-manager-ultimate' ) . ' #' . $wcfm_supports_single->order_id . '</a>' . "<br />" . '<a class="wcfm-support-product" target="_blank" href="' . get_permalink($wcfm_supports_single->product_id) . '">' . get_the_title($wcfm_supports_single->product_id) . '</a>';
				} else {
					$wcfm_supports_json_arr[$index][] =  __( 'Order', 'wc-frontend-manager-ultimate' ) . ' #' . $wcfm_supports_single->order_id . "<br />" . '<a class="wcfm-support-product" target="_blank" href="' . get_permalink($wcfm_supports_single->product_id) . '">' . get_the_title($wcfm_supports_single->product_id) . '</a>';
				}
				
				// Product
				//$wcfm_supports_json_arr[$index][] =  '<a class="wcfm-support-product" target="_blank" href="' . get_permalink($wcfm_supports_single->product_id) . '">' . get_the_title($wcfm_supports_single->product_id) . '</a>';
				
				// Customer
				$customer_details = '';
				if( apply_filters( 'wcfm_allow_view_customer_name', true ) ) {
					if( $wcfm_supports_single->customer_id && apply_filters( 'wcfm_is_allow_view_customer', true ) ) {
						$customer_details =  '<a target="_blank" href="' . get_wcfm_customers_details_url($wcfm_supports_single->customer_id) . '" class="wcfm_support_by_customer">' . $wcfm_supports_single->customer_name . '</a>';
					} else {
						$customer_details =  $wcfm_supports_single->customer_name;
					}
					if( apply_filters( 'wcfm_allow_view_customer_email', true ) ) {
						$customer_details .= "<br />" . $wcfm_supports_single->customer_email;
					}
				} 
				$wcfm_supports_json_arr[$index][] =  apply_filters( 'wcfm_support_customer_name_display', $customer_details, $wcfm_supports_single->customer_id, $wcfm_supports_single );
				
				// Priority
				$support_priority_types = $WCFMu->wcfmu_support->wcfm_support_priority_types();
				$wcfm_supports_json_arr[$index][] =  '<span class="support-priority support-priority-' . $wcfm_supports_single->priority . '">' . $support_priority_types[$wcfm_supports_single->priority] . '</span>';
				
				// Store
				$vendor_name = '&ndash;';
				if( !$WCFM->is_marketplace || wcfm_is_vendor() ) {
					$wcfm_supports_json_arr[$index][] =  $vendor_name;
				} else {
					$store_name = $WCFM->wcfm_vendor_support->wcfm_get_vendor_store_by_vendor( $wcfm_supports_single->vendor_id );
					if( $store_name ) {
						$vendor_name = $store_name;
					}
					$wcfm_supports_json_arr[$index][] =  $vendor_name;
				}
				
				// Date
				$wcfm_supports_json_arr[$index][] = date_i18n( wc_date_format() . ' ' . wc_time_format(), strtotime( $wcfm_supports_single->posted ) );
				
				// Action
				$actions = '<a class="wcfm-action-icon" href="' . get_wcfm_support_manage_url($wcfm_supports_single->ID) . '"><span class="wcfmfa fa-reply-all text_tip" data-tip="' . esc_attr__( 'Reply', 'wc-frontend-manager-ultimate' ) . '"></span></a>';
				
				if( apply_filters( 'wcfm_is_allow_support_dalate', true ) ) {
					$actions .= '<a class="wcfm_support_delete wcfm-action-icon" href="#" data-supportid="' . $wcfm_supports_single->ID . '"><span class="wcfmfa fa-trash-alt text_tip" data-tip="' . esc_attr__( 'Delete', 'wc-frontend-manager-ultimate' ) . '"></span></a>';
				}
				
				
				$wcfm_supports_json_arr[$index][] = apply_filters ( 'wcfm_support_actions', $actions, $wcfm_supports_single );
				
				$index++;
			}												
		}
		if( !empty($wcfm_supports_json_arr) ) $wcfm_supports_json .= json_encode($wcfm_supports_json_arr);
		else $wcfm_supports_json .= '[]';
		$wcfm_supports_json .= '
													}';
													
		echo $wcfm_supports_json;
	}
}