<?php
/**
 * WCFM plugin controllers
 *
 * Plugin PW Gift Catds for WooCommerce Controller
 *
 * @author 		WC Lovers
 * @package 	wcfmu/controllers/integrations/pw-gift-cards
 * @version   6.4.5
 */
 
class WCFMu_WC_PW_Gift_Cards_Controller {
	
	public function __construct() {
		global $WCFM;
		
		$this->processing();
	}
	
	public function processing() {
		global $WCFM, $wpdb, $_POST;
		
		$active_sql = '';
		
		$length = $_POST['length'];
		$offset = $_POST['start'];
		
		$vendor_id = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );
		
		$search_val = '';
		if( isset( $_POST['search'] ) && !empty( $_POST['search']['value'] )) {
			$search_val = wc_clean( $_POST['search']['value'] );
		}
		
		if( $search_val ) {
			$search_terms = '%' . $search_val . '%';
		} else {
			$search_terms = '%';
			$active_sql = 'AND gift_card.active = true';
		}

		$license_vendor = '';
		if ( ! empty( $_POST['license_vendor'] ) ) {
			$license_vendor = wc_clean( $_POST['license_vendor'] );
		}
		
		$the_orderby = ! empty( $_POST['orderby'] ) ? sanitize_text_field( $_POST['orderby'] ) : 'ID';
		$the_order   = ( ! empty( $_POST['order'] ) && 'asc' === $_POST['order'] ) ? 'ASC' : 'DESC';
		
		$items_per_page = $length;
		
		$vendor_filter = '';
		if( wcfm_is_vendor() ) { 
			$vendor_filter = "LEFT JOIN
						             `{$wpdb->prefix}woocommerce_order_itemmeta` AS order_itemmeta_vendor ON (order_itemmeta_vendor.meta_key = '_vendor_id' AND order_itemmeta_vendor.order_item_id = order_itemmeta_number.order_item_id)";
			$active_sql .= ' AND order_itemmeta_vendor.meta_value = '.$vendor_id;	
		} elseif ( ! empty( $_POST['license_vendor'] ) ) {
			$vendor_filter = "LEFT JOIN
						             `{$wpdb->prefix}woocommerce_order_itemmeta` AS order_itemmeta_vendor ON (order_itemmeta_vendor.meta_key = '_vendor_id' AND order_itemmeta_vendor.order_item_id = order_itemmeta_number.order_item_id)";
			$active_sql .= ' AND order_itemmeta_vendor.meta_value = '.$license_vendor;	
		}
		
		if ( PWGC_UTF8_SEARCH ) {
			$sql = $wpdb->prepare( "
					SELECT
							COUNT(gift_card.pimwick_gift_card_id)
					FROM
							`{$wpdb->pimwick_gift_card}` AS gift_card
					LEFT JOIN
							`{$wpdb->prefix}woocommerce_order_itemmeta` AS order_itemmeta_number ON (order_itemmeta_number.meta_key = 'pw_gift_card_number' AND CONVERT(order_itemmeta_number.meta_value USING utf8) = CONVERT(gift_card.number USING utf8) )
					LEFT JOIN
							`{$wpdb->prefix}woocommerce_order_itemmeta` AS order_itemmeta_to ON (order_itemmeta_to.meta_key = CONVERT('pw_gift_card_to' USING utf8) AND order_itemmeta_to.order_item_id = order_itemmeta_number.order_item_id)
					$vendor_filter
					WHERE
							(gift_card.number LIKE %s OR order_itemmeta_to.meta_value LIKE %s)
							$active_sql
			", $search_terms, $search_terms );
		} else {
			$sql = $wpdb->prepare( "
					SELECT
							COUNT(gift_card.pimwick_gift_card_id)
					FROM
							`{$wpdb->pimwick_gift_card}` AS gift_card
					LEFT JOIN
							`{$wpdb->prefix}woocommerce_order_itemmeta` AS order_itemmeta_number ON (order_itemmeta_number.meta_key = 'pw_gift_card_number' AND order_itemmeta_number.meta_value = gift_card.number )
					LEFT JOIN
							`{$wpdb->prefix}woocommerce_order_itemmeta` AS order_itemmeta_to ON (order_itemmeta_to.meta_key = 'pw_gift_card_to' AND order_itemmeta_to.order_item_id = order_itemmeta_number.order_item_id)
					$vendor_filter
					WHERE
							(gift_card.number LIKE %s OR order_itemmeta_to.meta_value LIKE %s)
							$active_sql
			", $search_terms, $search_terms );
		}
		
		$sql = apply_filters( 'wcfm_pw_gift_cards_count_query', $sql);
		
		$total_pw_gift_cards = $wpdb->get_var( $sql );
		if( !$total_pw_gift_cards ) $total_pw_gift_cards = 0;
		
		if ( PWGC_UTF8_SEARCH ) {
			$pw_gift_cards_query = $wpdb->prepare( "
					SELECT
							gift_card.*,
							(SELECT SUM(amount) FROM {$wpdb->pimwick_gift_card_activity} AS a WHERE a.pimwick_gift_card_id = gift_card.pimwick_gift_card_id) AS balance
					FROM
							`{$wpdb->pimwick_gift_card}` AS gift_card
					LEFT JOIN
							`{$wpdb->prefix}woocommerce_order_itemmeta` AS order_itemmeta_number ON (order_itemmeta_number.meta_key = 'pw_gift_card_number' AND CONVERT(order_itemmeta_number.meta_value USING utf8) = CONVERT(gift_card.number USING utf8) )
					LEFT JOIN
							`{$wpdb->prefix}woocommerce_order_itemmeta` AS order_itemmeta_to ON (order_itemmeta_to.meta_key = CONVERT('pw_gift_card_to' USING utf8) AND order_itemmeta_to.order_item_id = order_itemmeta_number.order_item_id)
					$vendor_filter
					WHERE
							(gift_card.number LIKE %s OR order_itemmeta_to.meta_value LIKE %s)
							$active_sql
					ORDER BY
							gift_card.create_date {$the_order},
							gift_card.pimwick_gift_card_id {$the_order}
			", $search_terms, $search_terms );
		} else {
			$pw_gift_cards_query = $wpdb->prepare( "
					SELECT
							gift_card.*,
							(SELECT SUM(amount) FROM {$wpdb->pimwick_gift_card_activity} AS a WHERE a.pimwick_gift_card_id = gift_card.pimwick_gift_card_id) AS balance
					FROM
							`{$wpdb->pimwick_gift_card}` AS gift_card
					LEFT JOIN
							`{$wpdb->prefix}woocommerce_order_itemmeta` AS order_itemmeta_number ON (order_itemmeta_number.meta_key = 'pw_gift_card_number' AND order_itemmeta_number.meta_value = gift_card.number )
					LEFT JOIN
							`{$wpdb->prefix}woocommerce_order_itemmeta` AS order_itemmeta_to ON (order_itemmeta_to.meta_key = 'pw_gift_card_to' AND order_itemmeta_to.order_item_id = order_itemmeta_number.order_item_id)
					$vendor_filter
					WHERE
							(gift_card.number LIKE %s OR order_itemmeta_to.meta_value LIKE %s)
							$active_sql
					ORDER BY
							gift_card.create_date {$the_order},
							gift_card.pimwick_gift_card_id {$the_order}
			", $search_terms, $search_terms );
		}
		
		$pw_gift_cards_query = apply_filters( 'wcfm_pw_gift_cards_list_query', $pw_gift_cards_query );
		
		$pw_gift_cards_query .= " LIMIT {$items_per_page}";

		$pw_gift_cards_query .= " OFFSET {$offset}";
		
		
		$wcfm_pw_gift_cards_array = $wpdb->get_results( $pw_gift_cards_query );
		
		if( defined('WCFM_REST_API_CALL') ) {
			return $wcfm_pw_gift_cards_array;
		}
		
		// Generate Pw_gift_cards JSON
		$wcfm_pw_gift_cards_json = '';
		$wcfm_pw_gift_cards_json = '{
															"draw": ' . $_POST['draw'] . ',
															"recordsTotal": ' . $total_pw_gift_cards . ',
															"recordsFiltered": ' . $total_pw_gift_cards . ',
															"data": ';
		if(!empty($wcfm_pw_gift_cards_array)) {
			$index = 0;
			$wcfm_pw_gift_cards_json_arr = array();
			foreach($wcfm_pw_gift_cards_array as $wcfm_pw_gift_cards_single) {
				
				$gift_card = new PW_Gift_Card( $wcfm_pw_gift_cards_single );
				
        // Card Number
        $card_number = '<span class="gift_card_number">' . esc_html( $gift_card->get_number() ) . '</span>';
        if ( !$gift_card->get_active() ) { 
        	$card_number .= "<br />" . __( 'Card has been deleted.', 'pw-woocommerce-gift-cards' );
        }
        $wcfm_pw_gift_cards_json_arr[$index][] = $card_number;
        
        // Balance
        $wcfm_pw_gift_cards_json_arr[$index][] = wc_price( $gift_card->get_balance() );
				
				// Expires At
        $wcfm_pw_gift_cards_json_arr[$index][] = $gift_card->get_expiration_date_html();
				
				
				// Action
				$actions = '';
				$actions .= '<a class="wcfm-action-icon wcfm_pw_gift_card_activity" href="#" data-cardnumber="' . $gift_card->get_number() . '"><span class="wcfmfa fas fa-history text_tip" data-tip="' . esc_attr__( 'View activity', 'pw-woocommerce-gift-cards' ) . '"></span></a>';
				if ( $gift_card->get_active() ) {
					$actions .= '<a class="wcfm-action-icon wcfm_pw_gift_card_delete" href="#" data-cardnumber="' . $gift_card->get_number() . '"><span class="wcfmfa fa-trash-alt text_tip" data-tip="' . esc_attr__( 'Delete', 'wc-frontend-manager-ultimate' ) . '"></span></a>';
				} else {
					$actions .= '<a class="wcfm-action-icon wcfm_pw_gift_card_restore" href="#" data-cardnumber="' . $gift_card->get_number() . '"><span class="wcfmfa fa-undo text_tip" data-tip="' . esc_attr__( 'Restore', 'wc-frontend-manager-ultimate' ) . '"></span></a>';
				}
				
				$wcfm_pw_gift_cards_json_arr[$index][] = apply_filters ( 'wcfm_rental_pw_gift_cards_actions', $actions, $wcfm_pw_gift_cards_single );
				
				$index++;
			}												
		}
		if( !empty($wcfm_pw_gift_cards_json_arr) ) $wcfm_pw_gift_cards_json .= json_encode($wcfm_pw_gift_cards_json_arr);
		else $wcfm_pw_gift_cards_json .= '[]';
		$wcfm_pw_gift_cards_json .= '
													}';
													
		echo $wcfm_pw_gift_cards_json;
	}
}