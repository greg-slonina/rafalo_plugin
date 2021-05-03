<?php
/**
 * WCFM plugin controllers
 *
 * Plugin Chats History Controller
 *
 * @author 		WC Lovers
 * @package 	wcfm/controllers/chatbox
 * @version   6.4.3
 */

class WCFMu_Chats_History_Controller {
	
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
		
		$the_orderby = ! empty( $_POST['orderby'] ) ? sanitize_text_field( $_POST['orderby'] ) : 'created_at';
		$the_order   = ( ! empty( $_POST['order'] ) && 'asc' === $_POST['order'] ) ? 'ASC' : 'DESC';
		
		$items_per_page = $length;
		
		$sql = "SELECT count(conversation_id) FROM {$wpdb->prefix}wcfm_fbc_chat_sessions AS commission";
		$sql .= " LEFT JOIN {$wpdb->prefix}wcfm_fbc_chat_visitors AS visitors";
		$sql .= " ON commission.user_id = visitors.user_id";
		$sql .= " WHERE 1 = 1";
		
		if( wcfm_is_vendor() ) { 
			$sql .= " AND visitors.`vendor_id` = {$vendor_id}";
		} elseif ( ! empty( $_POST['chats_vendor'] ) ) {
			$sql .= " AND visitors.`vendor_id` = {$chats_vendor}";
		}
		if ( ! empty( $_POST['filter_date_form'] ) && ! empty( $_POST['filter_date_to'] ) ) {
			$start_date = date( 'Y-m-d', strtotime( $_POST['filter_date_form'] ) );
			$end_date = date( 'Y-m-d', strtotime( $_POST['filter_date_to'] ) );
			$time_filter = " AND DATE( commission.mail_date ) BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
			$sql .= $time_filter;
		}
		$sql = apply_filters( 'wcfm_chats_history_count_query', $sql);
		
		$total_chats_history = $wpdb->get_var( $sql );
		
		$chats_history_query = "SELECT * FROM {$wpdb->prefix}wcfm_fbc_chat_sessions AS commission";
		$chats_history_query .= " LEFT JOIN {$wpdb->prefix}wcfm_fbc_chat_visitors AS visitors";
		$chats_history_query .= " ON commission.user_id = visitors.user_id";
		$chats_history_query .= " WHERE 1 = 1";
		
		if( wcfm_is_vendor() ) { 
			$chats_history_query .= " AND visitors.`vendor_id` = {$vendor_id}";
		} elseif ( ! empty( $_POST['chats_vendor'] ) ) {
			$chats_history_query .= " AND visitors.`vendor_id` = {$chats_vendor}";
		}
		if ( ! empty( $_POST['filter_date_form'] ) && ! empty( $_POST['filter_date_to'] ) ) {
			$chats_history_query .= $time_filter;
		}
		$chats_history_query = apply_filters( 'wcfm_chats_history_list_query', $chats_history_query );
		
		$chats_history_query .= " ORDER BY commission.`{$the_orderby}` {$the_order}";

		$chats_history_query .= " LIMIT {$items_per_page}";

		$chats_history_query .= " OFFSET {$offset}";
		
		
		$wcfm_chats_history_array = $wpdb->get_results( $chats_history_query );
		
		if( defined('WCFM_REST_API_CALL') ) {
			return $wcfm_chats_history_array;
		}
		
		// Generate Chats_history JSON
		$wcfm_chats_history_json = '';
		$wcfm_chats_history_json = '{
															"draw": ' . wc_clean($_POST['draw']) . ',
															"recordsTotal": ' . $total_chats_history . ',
															"recordsFiltered": ' . $total_chats_history . ',
															"data": ';
		if(!empty($wcfm_chats_history_array)) {
			$index = 0;
			$wcfm_chats_history_json_arr = array();
			foreach($wcfm_chats_history_array as $wcfm_chats_history_single) {
				
				// User
				$wcfm_chats_history_json_arr[$index][] =  $wcfm_chats_history_single->user_name;
				
				// User Email
				$wcfm_chats_history_json_arr[$index][] =  '<a class="wcfm_dashboard_item_title" href="mailto:'.$wcfm_chats_history_single->user_email.'">' . $wcfm_chats_history_single->user_email . '</a>';
				
				// Total Messages
				$messages_sql = "SELECT count(message_id) FROM {$wpdb->prefix}wcfm_fbc_chat_rows AS chat_rows";
				$messages_sql .= " WHERE 1 = 1";
				$messages_sql .= " AND `conversation_id` = '" . $wcfm_chats_history_single->conversation_id . "'";
				$wcfm_chats_history_json_arr[$index][] =  $wpdb->get_var( $messages_sql );
				
				// Author
				if( !wcfm_is_vendor() ) {
					if( wcfm_is_vendor( $wcfm_chats_history_single->vendor_id ) ) {
						$wcfm_chats_history_json_arr[$index][] = wcfm_get_vendor_store( $wcfm_chats_history_single->vendor_id );
					} else {
						$wcfm_chats_history_json_arr[$index][] =  '&ndash;';
					}
				} else {
					$wcfm_chats_history_json_arr[$index][] = '&ndash;';
				}
				
				// Duration
				$wcfm_chats_history_json_arr[$index][] =  $wcfm_chats_history_single->duration;
				
				// Evaluaton
				if( $wcfm_chats_history_single->evaluation == 'good' ) {
					$wcfm_chats_history_json_arr[$index][] =  '<span class="wcfmfa fa-thumbs-up text_tip" data-tip="' . esc_attr__( 'Good', 'wc-frontend-manager-ultimate' ) . '"></span>';
				} elseif( $wcfm_chats_history_single->evaluation == 'bad' ) {
					$wcfm_chats_history_json_arr[$index][] =  '<span class="wcfmfa fa-thumbs-down text_tip" data-tip="' . esc_attr__( 'Bad', 'wc-frontend-manager-ultimate' ) . '"></span>';
				} else {
					$wcfm_chats_history_json_arr[$index][] =  '&ndash;';
				} 
				
				// Date
				$gmt_offset = get_option( 'gmt_offset' );
				$timestamp  = ( $wcfm_chats_history_single->created_at / 1000 ) + ( $gmt_offset * 3600 );
				$wcfm_chats_history_json_arr[$index][] =  date_i18n( wc_date_format() . ' ' . wc_time_format(), $timestamp );
				
				// Actions
				$actions = '<a class="wcfm_show_chat_conversation wcfm-action-icon" href="#" data-conversation="' . $wcfm_chats_history_single->conversation_id . '"><span class="wcfmfa fa-eye text_tip" data-tip="' . esc_attr__( 'Show Conversation History', 'wc-frontend-manager-ultimate' ) . '"></span></a>';
				if( apply_filters( 'wcfm_is_allow_chats_history_delete', true ) ) {
					$actions .= '<a class="wcfm_chats_history_delete wcfm-action-icon" href="#" data-conversation="' . $wcfm_chats_history_single->conversation_id . '"><span class="wcfmfa fa-trash-alt text_tip" data-tip="' . esc_attr__( 'Delete', 'wc-frontend-manager' ) . '"></span></a>';
				}
				$wcfm_chats_history_json_arr[$index][] =  $actions;
				
				
				$index++;
			}												
		}
		if( !empty($wcfm_chats_history_json_arr) ) $wcfm_chats_history_json .= json_encode($wcfm_chats_history_json_arr);
		else $wcfm_chats_history_json .= '[]';
		$wcfm_chats_history_json .= '
													}';
													
		echo $wcfm_chats_history_json;
	}
}