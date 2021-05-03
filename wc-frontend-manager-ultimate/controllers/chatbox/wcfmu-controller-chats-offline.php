<?php
/**
 * WCFM plugin controllers
 *
 * Plugin Chats Offline Controller
 *
 * @author 		WC Lovers
 * @package 	wcfm/controllers/chatbox
 * @version   6.4.3
 */

class WCFMu_Chats_Offline_Controller {
	
	public function __construct() {
		global $WCFM;
		
		$this->processing();
		
	}
	
	public function processing() {
		global $WCFM, $wpdb, $_POST;
		
		$length = wc_clean($_POST['length']);
		$offset = wc_clean($_POST['start']);
		
		$vendor_id = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );
		
		$chats_vendor = '';
		if ( ! empty( $_POST['chats_vendor'] ) ) {
			$chats_vendor = esc_sql( $_POST['chats_vendor'] );
		}
		
		$time_filter = '';
		
		$the_orderby = ! empty( $_POST['orderby'] ) ? sanitize_text_field( $_POST['orderby'] ) : 'ID';
		$the_order   = ( ! empty( $_POST['order'] ) && 'asc' === $_POST['order'] ) ? 'ASC' : 'DESC';
		
		$items_per_page = $length;
		
		$sql = "SELECT count(ID) FROM {$wpdb->prefix}wcfm_fbc_offline_messages AS commission";
		$sql .= " WHERE 1 = 1";
		
		if( wcfm_is_vendor() ) { 
			$sql .= " AND `vendor_id` = {$vendor_id}";
		} elseif ( ! empty( $_POST['chats_vendor'] ) ) {
			$sql .= " AND `vendor_id` = {$chats_vendor}";
		}
		if ( ! empty( $_POST['filter_date_form'] ) && ! empty( $_POST['filter_date_to'] ) ) {
			$start_date = date( 'Y-m-d', strtotime( $_POST['filter_date_form'] ) );
			$end_date = date( 'Y-m-d', strtotime( $_POST['filter_date_to'] ) );
			$time_filter = " AND DATE( commission.mail_date ) BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
			$sql .= $time_filter;
		}
		$sql = apply_filters( 'wcfm_chats_offline_count_query', $sql);
		
		$total_chats_offline = $wpdb->get_var( $sql );
		
		$chats_offline_query = "SELECT * FROM {$wpdb->prefix}wcfm_fbc_offline_messages AS commission";
		$chats_offline_query .= " WHERE 1 = 1";
		
		if( wcfm_is_vendor() ) { 
			$chats_offline_query .= " AND `vendor_id` = {$vendor_id}";
		} elseif ( ! empty( $_POST['chats_vendor'] ) ) {
			$chats_offline_query .= " AND `vendor_id` = {$chats_vendor}";
		}
		if ( ! empty( $_POST['filter_date_form'] ) && ! empty( $_POST['filter_date_to'] ) ) {
			$chats_offline_query .= $time_filter;
		}
		$chats_offline_query = apply_filters( 'wcfm_chats_offline_list_query', $chats_offline_query );
		
		$chats_offline_query .= " ORDER BY commission.`{$the_orderby}` {$the_order}";

		$chats_offline_query .= " LIMIT {$items_per_page}";

		$chats_offline_query .= " OFFSET {$offset}";
		
		
		$wcfm_chats_offline_array = $wpdb->get_results( $chats_offline_query );
		
		if( defined('WCFM_REST_API_CALL') ) {
			return $wcfm_chats_offline_array;
		}
		
		// Generate Chats_offline JSON
		$wcfm_chats_offline_json = '';
		$wcfm_chats_offline_json = '{
															"draw": ' . wc_clean($_POST['draw']) . ',
															"recordsTotal": ' . $total_chats_offline . ',
															"recordsFiltered": ' . $total_chats_offline . ',
															"data": ';
		if(!empty($wcfm_chats_offline_array)) {
			$index = 0;
			$wcfm_chats_offline_json_arr = array();
			foreach($wcfm_chats_offline_array as $wcfm_chats_offline_single) {
				
				// User
				$wcfm_chats_offline_json_arr[$index][] =  $wcfm_chats_offline_single->user_name;
				
				// User Email
				$wcfm_chats_offline_json_arr[$index][] =  '<a class="wcfm_dashboard_item_title" href="mailto:'.$wcfm_chats_offline_single->user_email.'">' . $wcfm_chats_offline_single->user_email . '</a>';
				
				// Message
				$wcfm_chats_offline_json_arr[$index][] =  $wcfm_chats_offline_single->user_message;
				
				// Author
				if( !wcfm_is_vendor() ) {
					if( wcfm_is_vendor( $wcfm_chats_offline_single->vendor_id ) ) {
						$wcfm_chats_offline_json_arr[$index][] = wcfm_get_vendor_store( $wcfm_chats_offline_single->vendor_id );
					} else {
						$wcfm_chats_offline_json_arr[$index][] =  '&ndash;';
					}
				} else {
					$wcfm_chats_offline_json_arr[$index][] = '&ndash;';
				}
				
				// Date
				$wcfm_chats_offline_json_arr[$index][] =  date_i18n( wc_date_format(), strtotime($wcfm_chats_offline_single->mail_date) );
				
				// Actions
				$actions = '&ndash;';
				if( apply_filters( 'wcfm_is_allow_chats_offline_delete', true ) ) {
					$actions = '<a class="wcfm_chats_offline_delete wcfm-action-icon" href="#" data-messageid="' . $wcfm_chats_offline_single->id . '"><span class="wcfmfa fa-trash-alt text_tip" data-tip="' . esc_attr__( 'Delete', 'wc-frontend-manager' ) . '"></span></a>';
				}
				$wcfm_chats_offline_json_arr[$index][] = $actions;
				
				$index++;
			}												
		}
		if( !empty($wcfm_chats_offline_json_arr) ) $wcfm_chats_offline_json .= json_encode($wcfm_chats_offline_json_arr);
		else $wcfm_chats_offline_json .= '[]';
		$wcfm_chats_offline_json .= '
													}';
													
		echo $wcfm_chats_offline_json;
	}
}