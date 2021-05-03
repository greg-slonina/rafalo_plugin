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
 
use LicenseManagerForWooCommerce\Enums\LicenseStatus;
use LicenseManagerForWooCommerce\Repositories\Resources\License as LicenseResourceRepository;

class WCFMu_License_Keys_Controller {
	
	public function __construct() {
		global $WCFM;
		
		$this->processing();
	}
	
	public function processing() {
		global $WCFM, $wpdb, $_POST;
		
		$dateFormat = get_option('date_format');
		$timeFormat = get_option('time_format');
		$gmtOffset  = get_option('gmt_offset');
		
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
		
		$sql = "SELECT count(ID) FROM {$wpdb->prefix}lmfwc_licenses AS commission";
		$sql .= " WHERE 1 = 1";
		
		if( wcfm_is_vendor() ) { 
			$vendor_products = $WCFM->wcfm_vendor_support->wcfm_get_products_by_vendor( $vendor_id );
			if( empty($vendor_products) ) {
				$sql .= " AND `product_id` in (0)";
			} else {
				$sql .= " AND `product_id` in (" . implode(',', array_keys( $vendor_products ) ) . ")";
			}
		} elseif ( ! empty( $_POST['license_vendor'] ) ) {
			$vendor_products = $WCFM->wcfm_vendor_support->wcfm_get_products_by_vendor( $_POST['license_vendor'] );
			if( empty($vendor_products) ) {
				$sql .= " AND `product_id` in (0)";
			} else {
				$sql .= " AND `product_id` in (" . implode(',', array_keys( $vendor_products ) ) . ")";
			}
		}
		
		if( $license_status ) {
			$sql .= " AND `status` = {$license_status}";
		}
		
		if( $search_val ) {
			$sql .= " AND `name` like '%{$search_val}%'";
		}
		
		$sql = apply_filters( 'wcfm_license_keys_count_query', $sql);
		
		$total_license_keys = $wpdb->get_var( $sql );
		
		$license_keys_query = "SELECT * FROM {$wpdb->prefix}lmfwc_licenses AS commission";
		$license_keys_query .= " WHERE 1 = 1";
		
		if( wcfm_is_vendor() ) { 
			if( empty($vendor_products) ) {
				$license_keys_query .= " AND `product_id` in (0)";
			} else {
				$license_keys_query .= " AND `product_id` in (" . implode(',', array_keys( $vendor_products ) ) . ")";
			}
		} elseif ( ! empty( $_POST['license_vendor'] ) ) {
			if( empty($vendor_products) ) {
				$license_keys_query .= " AND `product_id` in (0)";
			} else {
				$license_keys_query .= " AND `product_id` in (" . implode(',', array_keys( $vendor_products ) ) . ")";
			}
		}
		
		if( $license_status ) {
			$license_keys_query .= " AND `status` = {$license_status}";
		}
		
		if( $search_val ) {
			$license_keys_query .= " AND `name` like '%{$search_val}%'";
		}
		
		$license_keys_query = apply_filters( 'wcfm_license_keys_list_query', $license_keys_query );
		
		$license_keys_query .= " ORDER BY commission.`{$the_orderby}` {$the_order}";

		$license_keys_query .= " LIMIT {$items_per_page}";

		$license_keys_query .= " OFFSET {$offset}";
		
		
		$wcfm_license_keys_array = $wpdb->get_results( $license_keys_query );
		
		if( defined('WCFM_REST_API_CALL') ) {
			return $wcfm_license_keys_array;
		}
		
		// Generate License_keys JSON
		$wcfm_license_keys_json = '';
		$wcfm_license_keys_json = '{
															"draw": ' . $_POST['draw'] . ',
															"recordsTotal": ' . $total_license_keys . ',
															"recordsFiltered": ' . $total_license_keys . ',
															"data": ';
		if(!empty($wcfm_license_keys_array)) {
			$index = 0;
			$wcfm_license_keys_json_arr = array();
			foreach($wcfm_license_keys_array as $wcfm_license_keys_single) {
				
				// Status
				$status = '';
				switch ($wcfm_license_keys_single->status) {
            case LicenseStatus::SOLD:
                $status = sprintf(
                    '<div class="lmfwc-status lmfwc-status-sold"><span class="dashicons dashicons-yes"></span><br/>%s</div>',
                    __('Sold', 'lmfwc')
                );
                break;
            case LicenseStatus::DELIVERED:
                $status = sprintf(
                    '<div class="lmfwc-status lmfwc-status-delivered"><span class="wcfmfa fa-truck"></span><br/>%s</div>',
                    __('Delivered', 'lmfwc')
                );
                break;
            case LicenseStatus::ACTIVE:
                $status = sprintf(
                    '<div class="lmfwc-status lmfwc-status-active"><span class="dashicons dashicons-marker"></span><br/>%s</div>',
                    __('Active', 'lmfwc')
                );
                break;
            case LicenseStatus::INACTIVE:
                $status = sprintf(
                    '<div class="lmfwc-status lmfwc-status-inactive"><span class="dashicons dashicons-marker"></span><br/>%s</div>',
                    __('Inactive', 'lmfwc')
                );
                break;
            default:
                $status = sprintf(
                    '<div class="lmfwc-status unknown">%s</div>',
                    __('Unknown', 'lmfwc')
                );
                break;
        }
        $wcfm_license_keys_json_arr[$index][] =  $status;
				
				// Key
				$title = sprintf(
														'<code class="lmfwc-placeholder">%s</code>',
														apply_filters('lmfwc_decrypt', $wcfm_license_keys_single->license_key)
												);
        $wcfm_license_keys_json_arr[$index][] =  $title;
				
				// Order
				$order_label = '&ndash;';
				if ($order = wc_get_order($wcfm_license_keys_single->order_id)) {
					if( apply_filters( 'wcfm_is_allow_order_details', true ) && $WCFM->wcfm_vendor_support->wcfm_is_order_for_vendor( $wcfm_license_keys_single->order_id ) ) {
            $order_label = sprintf(
                '<a class="wcfm_dashboard_item_title" href="%s" target="_blank">#%s</a>',
                get_wcfm_view_order_url($wcfm_license_keys_single->order_id),
                $order->get_order_number()
            );
          } else {
          	$order_label = sprintf(
                '<span class="wcfm_dashboard_item_title">#%s</span>',
                $order->get_order_number()
            );
          }
          	
        }
        $wcfm_license_keys_json_arr[$index][] =  $order_label;
				
				// Product
				$product_label = '&ndash;';
				if ($product = wc_get_product($wcfm_license_keys_single->product_id)) {

            if ($parentId = $product->get_parent_id()) {
                $product_label = sprintf(
                    '<span class="wcfm_dashboard_item_title">#%s - %s</span>',
                    $product->get_id(),
                    $product->get_name()
                );

                if ($parent = wc_get_product($parentId)) {
                    $product_label .= sprintf(
                        '<br><small>%s <a href="%s" target="_blank">#%s - %s</a></small>',
                        __('Variation of', 'lmfwc'),
                        get_permalink($parent->get_id()),
                        $parent->get_id(),
                        $parent->get_name()
                    );
                }
            } else {
                $product_label = sprintf(
                    '<a class="wcfm_dashboard_item_title" href="%s" target="_blank">#%s - %s</a>',
                    get_permalink($wcfm_license_keys_single->product_id),
                    $product->get_id(),
                    $product->get_name()
                );
            }
        }
				$wcfm_license_keys_json_arr[$index][] =  $product_label;
				
				// Activaton
				$activation_label = '';

        $timesActivated    = intval($wcfm_license_keys_single->times_activated);
        $timesActivatedMax = intval($wcfm_license_keys_single->times_activated_max);

        if ($timesActivated == $timesActivatedMax) {
            $icon = '<span class="dashicons dashicons-yes"></span>';
            $status = 'activation done';
        } else {
            $icon = '';
            $status = 'activation pending';
        }

        if ($timesActivated || $timesActivatedMax) {
            $activation_label = sprintf(
                '<div class="lmfwc-status %s">%s <small>%d</small> / <b>%d</b></div>',
                $status,
                $icon,
                $timesActivated,
                $timesActivatedMax
            );
        }
        $wcfm_license_keys_json_arr[$index][] =  $activation_label;
				
				// Valid For
				$valid_for_label = '';

        if ($wcfm_license_keys_single->valid_for) {
            $valid_for_label = sprintf(
                '<b>%d</b> %s<br><small>%s</small>',
                intval($wcfm_license_keys_single->valid_for),
                __('day(s)', 'lmfwc'),
                __('After purchase', 'lmfwc')
            );
        }
        
        $wcfm_license_keys_json_arr[$index][] =  $valid_for_label;
				
				// Expires At
				$expiresat_label = '&ndash;';
				if ($wcfm_license_keys_single->expires_at) {
					$offsetSeconds      = floatval($gmtOffset) * 60 * 60;
					$timestampExpiresAt = strtotime($wcfm_license_keys_single->expires_at) + $offsetSeconds;
					$timestampNow       = strtotime('now') + $offsetSeconds;
					$datetimeString     = date('Y-m-d H:i:s', $timestampExpiresAt);
					$date               = new DateTime($datetimeString);
	
					if ($timestampNow > $timestampExpiresAt) {
							$expiresat_label = sprintf(
									'<span class="lmfwc-date lmfwc-status expired" title="%s">%s, %s</span><br>',
									__('Expired'),
									$date->format($dateFormat),
									$date->format($timeFormat)
							);
					} else {
						$expiresat_label = sprintf(
								'<span class="lmfwc-date lmfwc-status">%s, %s</span>',
								$date->format($dateFormat),
								$date->format($timeFormat)
						);
					}
				}
        $wcfm_license_keys_json_arr[$index][] =  $expiresat_label;
				
				// Created
				$created_label = '';

        if ($wcfm_license_keys_single->created_at) {
					$offsetSeconds = floatval($gmtOffset) * 60 * 60;
					$timestamp     = strtotime($wcfm_license_keys_single->created_at) + $offsetSeconds;
					$result        = date('Y-m-d H:i:s', $timestamp);
					$date          = new DateTime($result);

					$created_label .= sprintf(
							'<span>%s <b>%s, %s</b></span>',
							__('at', 'lmfwc'),
							$date->format($dateFormat),
							$date->format($timeFormat)
					);
        }

        if ($wcfm_license_keys_single->created_by) {
					/** @var WP_User $user */
					$user = get_user_by('id', $wcfm_license_keys_single->created_by);

					if ($user instanceof WP_User) {
						$created_label .= sprintf(
								'<br><span>%s %s</span>',
								__('by', 'lmfwc'),
								$user->display_name
						);
					}
        }
        $wcfm_license_keys_json_arr[$index][] =  $created_label;
        
				
				// Updated
				$updated_label = '';

        if ($wcfm_license_keys_single->updated_at) {
            $offsetSeconds = floatval($gmtOffset) * 60 * 60;
            $timestamp     = strtotime($wcfm_license_keys_single->updated_at) + $offsetSeconds;
            $result        = date('Y-m-d H:i:s', $timestamp);
            $date          = new DateTime($result);

            $updated_label .= sprintf(
                '<span>%s <b>%s, %s</b></span>',
                __('at', 'lmfwc'),
                $date->format($dateFormat),
                $date->format($timeFormat)
            );
        }

        if ($wcfm_license_keys_single->updated_by) {
					/** @var WP_User $user */
					$user = get_user_by('id', $wcfm_license_keys_single->updated_by);

					if ($user instanceof WP_User) {
						$updated_label .= sprintf(
								'<br><span>%s %s</span>',
								__('by', 'lmfwc'),
								$user->display_name
						);
					}
        }
        $wcfm_license_keys_json_arr[$index][] =  $updated_label;

				
				// Action
				$actions = '';
				$actions .= '<a class="wcfm-action-icon wcfm_license_key_manage" href="#" data-licenseid="' . $wcfm_license_keys_single->id . '"><span class="wcfmfa fa-edit text_tip" data-tip="' . esc_attr__( 'Edit', 'wc-frontend-manager-ultimate' ) . '"></span></a>';
				$actions .= '<a class="wcfm-action-icon wcfm_license_key_delete" href="#" data-licenseid="' . $wcfm_license_keys_single->id . '"><span class="wcfmfa fa-trash-alt text_tip" data-tip="' . esc_attr__( 'Delete', 'wc-frontend-manager-ultimate' ) . '"></span></a>';
				
				$wcfm_license_keys_json_arr[$index][] = apply_filters ( 'wcfm_rental_license_keys_actions', $actions, $wcfm_license_keys_single );
				
				$index++;
			}												
		}
		if( !empty($wcfm_license_keys_json_arr) ) $wcfm_license_keys_json .= json_encode($wcfm_license_keys_json_arr);
		else $wcfm_license_keys_json .= '[]';
		$wcfm_license_keys_json .= '
													}';
													
		echo $wcfm_license_keys_json;
	}
}