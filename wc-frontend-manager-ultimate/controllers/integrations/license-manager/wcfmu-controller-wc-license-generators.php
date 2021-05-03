<?php
/**
 * WCFM plugin controllers
 *
 * Plugin License Manager for WooCommerce Controller
 *
 * @author 		WC Lovers
 * @package 	wcfmu/controllers/integrations/license-manager
 * @version   6.4.0
 */

class WCFMu_License_Generators_Controller {
	
	public function __construct() {
		global $WCFM;
		
		$this->processing();
	}
	
	public function processing() {
		global $WCFM, $wpdb, $_POST;
		
		$length = $_POST['length'];
		$offset = $_POST['start'];
		
		$vendor_id = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );
		
		$license_status = '';
		if( isset($_POST['license_status']) && !empty($_POST['license_status']) && ( $_POST['license_status'] != 'all' ) ) {
			$license_status = wc_clean( $_POST['license_status'] );
		}
		
		$search_val = '';
		if( isset( $_POST['search'] ) && !empty( $_POST['search']['value'] )) {
			$search_val = wc_clean( $_POST['search']['value'] );
		}

		$license_vendor = '';
		if ( ! empty( $_POST['license_vendor'] ) ) {
			$license_vendor = wc_clean( $_POST['license_vendor'] );
		}
		
		$the_orderby = ! empty( $_POST['orderby'] ) ? sanitize_text_field( $_POST['orderby'] ) : 'ID';
		$the_order   = ( ! empty( $_POST['order'] ) && 'asc' === $_POST['order'] ) ? 'ASC' : 'DESC';
		
		$items_per_page = $length;
		
		$sql = "SELECT count(ID) FROM {$wpdb->prefix}lmfwc_generators AS commission";
		$sql .= " WHERE 1 = 1";
		
		if( wcfm_is_vendor() ) { 
			$sql .= " AND `created_by` = {$vendor_id}";
		} elseif ( ! empty( $_POST['license_vendor'] ) ) {
			$sql .= " AND `created_by` = {$license_vendor}";
		}
		
		if( $search_val ) {
			$sql .= " AND `name` like '%{$search_val}%'";
		}
		
		$sql = apply_filters( 'wcfm_license_generators_count_query', $sql);
		
		$total_generators = $wpdb->get_var( $sql );
		
		$generators_query = "SELECT * FROM {$wpdb->prefix}lmfwc_generators AS commission";
		$generators_query .= " WHERE 1 = 1";
		
		if( wcfm_is_vendor() ) { 
			$generators_query .= " AND `created_by` = {$vendor_id}";
		} elseif ( ! empty( $_POST['license_vendor'] ) ) {
			$generators_query .= " AND `created_by` = {$license_vendor}";
		}
		
		if( $search_val ) {
			$generators_query .= " AND `name` like '%{$search_val}%'";
		}
		
		$generators_query = apply_filters( 'wcfm_license_generators_list_query', $generators_query );
		
		$generators_query .= " ORDER BY commission.`{$the_orderby}` {$the_order}";

		$generators_query .= " LIMIT {$items_per_page}";

		$generators_query .= " OFFSET {$offset}";
		
		
		$wcfm_license_generators_array = $wpdb->get_results( $generators_query );
		
		if( defined('WCFM_REST_API_CALL') ) {
			return $wcfm_license_generators_array;
		}
		
		// Generate License_generators JSON
		$wcfm_license_generators_json = '';
		$wcfm_license_generators_json = '{
															"draw": ' . $_POST['draw'] . ',
															"recordsTotal": ' . $total_generators . ',
															"recordsFiltered": ' . $total_generators . ',
															"data": ';
		if(!empty($wcfm_license_generators_array)) {
			$index = 0;
			$wcfm_license_generators_json_arr = array();
			foreach($wcfm_license_generators_array as $wcfm_license_generators_single) {
				
				// Name
				$products = apply_filters( 'lmfwc_get_assigned_products', $wcfm_license_generators_single->id );
				$title = '<span class="wcfm_quote_title wcfm_dashboard_item_title">#' . $wcfm_license_generators_single->name . '</span>';
				if (count($products) > 0) {
            $title .= ' - ' . sprintf(
                '<span class="lmfwc-badge info" title="%s">%d</span>',
                __('Number of products assigned to this generator', 'lmfwc'),
                count($products)
            );
        }
				$wcfm_license_generators_json_arr[$index][] = $title;
				
				// Store
				if( !wcfm_is_vendor() ) {
					if( wcfm_is_vendor( $wcfm_license_generators_single->created_by ) ) {
						$wcfm_license_generators_json_arr[$index][] = wcfm_get_vendor_store( $wcfm_license_generators_single->created_by );
					} else {
						$author = get_user_by( 'id', $wcfm_license_generators_single->created_by );
						if( $author ) {
							$wcfm_license_generators_json_arr[$index][] =  $author->display_name;
						} else {
							$wcfm_license_generators_json_arr[$index][] =  '&ndash;';
						}
					}
				} else {
					$wcfm_license_generators_json_arr[$index][] = '&ndash;';
				}
				
				// Max Activation Count
				$wcfm_license_generators_json_arr[$index][] =  $wcfm_license_generators_single->times_activated_max;
				
				// Expires In
				$expiresIn = '&ndash;';
				if( $wcfm_license_generators_single->expires_in ) {
					$expiresIn = sprintf('%d %s', $wcfm_license_generators_single->expires_in, __('day(s)', 'lmfwc'));
					$expiresIn .= '<br>';
					$expiresIn .= sprintf('<small>%s</small>', __('After purchase', 'lmfwc'));
				}
				$wcfm_license_generators_json_arr[$index][] = $expiresIn;

				// Action
				$actions = '';
				$actions .= '<a class="wcfm-action-icon wcfm_license_generator_manage" href="#" data-generatorid="' . $wcfm_license_generators_single->id . '"><span class="wcfmfa fa-edit text_tip" data-tip="' . esc_attr__( 'Edit', 'wc-frontend-manager-ultimate' ) . '"></span></a>';
				if (count($products) == 0) {
					$actions .= '<a class="wcfm-action-icon wcfm_license_generator_delete" href="#" data-generatorid="' . $wcfm_license_generators_single->id . '"><span class="wcfmfa fa-trash-alt text_tip" data-tip="' . esc_attr__( 'Delete', 'wc-frontend-manager-ultimate' ) . '"></span></a>';
				}
				
				$wcfm_license_generators_json_arr[$index][] = apply_filters ( 'wcfm_rental_license_generators_actions', $actions, $wcfm_license_generators_single );
				
				$index++;
			}												
		}
		if( !empty($wcfm_license_generators_json_arr) ) $wcfm_license_generators_json .= json_encode($wcfm_license_generators_json_arr);
		else $wcfm_license_generators_json .= '[]';
		$wcfm_license_generators_json .= '
													}';
													
		echo $wcfm_license_generators_json;
	}
}