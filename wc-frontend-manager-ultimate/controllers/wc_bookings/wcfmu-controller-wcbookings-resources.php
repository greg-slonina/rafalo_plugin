<?php
/**
 * WCFM plugin controllers
 *
 * Plugin WC Bookings Resources Controller
 *
 * @author 		WC Lovers
 * @package 	wcfmu/controllers
 * @version   2.3.5
 */

class WCFMu_WCBookings_Resources_Controller {
	
	public function __construct() {
		global $WCFM;
		
		$this->processing();
	}
	
	public function processing() {
		global $WCFM, $wpdb, $_POST, $WCFMu;
		
		$length = $_POST['length'];
		$offset = $_POST['start'];
		
		$args = array(
							'posts_per_page'   => $length,
							'offset'           => $offset,
							'category'         => '',
							'category_name'    => '',
							'bookingby'          => 'date',
							'booking'            => 'DESC',
							'include'          => '',
							'exclude'          => '',
							'meta_key'         => '',
							'meta_value'       => '',
							'post_type'        => 'bookable_resource',
							'post_mime_type'   => '',
							'post_parent'      => '',
							//'author'	   => get_current_user_id(),
							'post_status'      => 'any',
							//'suppress_filters' => 0 
						);
		if( isset( $_POST['search'] ) && !empty( $_POST['search']['value'] )) $args['s'] = $_POST['search']['value'];
		
		// Vendor Filter
		if( isset($_POST['resource_vendor']) && !empty($_POST['resource_vendor']) ) {
			$is_marketplace = wcfm_is_marketplace();
			if( $is_marketplace ) {
				if( !wcfm_is_vendor() ) {
					$args['author'] = wc_clean($_POST['resource_vendor']);
				}
			}
		}
		
		$args = apply_filters( 'get_booking_resources_args', $args );
		
		$wcfm_bookings_resources_array = get_posts( $args );
		
		// Get Product Count
		$booking_resources_count = 0;
		$filtered_booking_resources_count = 0;
		$wcfm_bookings_resurces_count = wp_count_posts('bookable_resource');
		$booking_resources_count = count($wcfm_bookings_resources_array);
		// Get Filtered Post Count
		$args['posts_per_page'] = -1;
		$args['offset'] = 0;
		$wcfm_filterd_bookings_resources_array = get_posts( $args );
		$filtered_booking_resources_count = count($wcfm_filterd_bookings_resources_array);
		
		
		// Generate Products JSON
		$wcfm_bookings_resources_json = '';
		$wcfm_bookings_resources_json = '{
															"draw": ' . $_POST['draw'] . ',
															"recordsTotal": ' . $booking_resources_count . ',
															"recordsFiltered": ' . $filtered_booking_resources_count . ',
															"data": ';
		$index = 0;
		$wcfm_bookings_resources_json_arr = array();
		if(!empty($wcfm_bookings_resources_array)) {
			foreach($wcfm_bookings_resources_array as $wcfm_bookings_resources_single) {
				
				// Resource
				$booking_label =  '<a href="' . get_wcfm_bookings_resources_manage_url($wcfm_bookings_resources_single->ID) . '" class="wcfm_booking_title">' . __( '#', 'wc-frontend-manager-ultimate' ) . $wcfm_bookings_resources_single->ID . ' ' . $wcfm_bookings_resources_single->post_title . '</a>';
				$wcfm_bookings_resources_json_arr[$index][] = $booking_label;
				
				// Parent Products
				$parents      = $wpdb->get_col( $wpdb->prepare( "SELECT product_id FROM {$wpdb->prefix}wc_booking_relationships WHERE resource_id = %d ORDER BY sort_order;", $wcfm_bookings_resources_single->ID ) );
				$parent_posts = array();
				foreach ( $parents as $parent_id ) {
					if ( empty( get_the_title( $parent_id ) ) ) {
						continue;
					}

					if( apply_filters( 'wcfm_is_allow_edit_products', true ) ) {
						$parent_posts[] = '<a class="wcfm_booking_title" href="' . get_wcfm_edit_product_url( $parent_id ) . '" target="_blank">' . get_the_title( $parent_id ) . '</a>';
					} else {
						$parent_posts[] = '<span class="wcfm_booking_title">' . get_the_title( $parent_id ) . '</span>';
					}
				}
				$wcfm_bookings_resources_json_arr[$index][] = $parent_posts ? wp_kses_post( implode( ', ', $parent_posts ) ) : esc_html__( 'N/A', 'woocommerce-bookings' );
				
				// Store
				if( !wcfm_is_vendor() ) {
					if( wcfm_is_vendor( $wcfm_bookings_resources_single->post_author ) ) {
						$wcfm_bookings_resources_json_arr[$index][] = wcfm_get_vendor_store_by_post( $wcfm_bookings_resources_single->ID );
					} else {
						$author = get_user_by( 'id', $wcfm_bookings_resources_single->post_author );
						if( $author ) {
							$wcfm_bookings_resources_json_arr[$index][] =  $author->display_name;
						} else {
							$wcfm_bookings_resources_json_arr[$index][] =  '&ndash;';
						}
					}
				} else {
					$wcfm_bookings_resources_json_arr[$index][] = '&ndash;';
				}
				
				// Available Quantity
				$wcfm_bookings_resources_json_arr[$index][] = get_post_meta( $wcfm_bookings_resources_single->ID, 'qty', true );
				
				// End Date
				$wcfm_bookings_resources_json_arr[$index][] = date_i18n( wc_date_format(), strtotime($wcfm_bookings_resources_single->post_date) );
				
				// Action
				$actions = '<a class="wcfm-action-icon" href="' . get_wcfm_bookings_resources_manage_url( $wcfm_bookings_resources_single->ID ) . '"><span class="wcfmfa fa-edit text_tip" data-tip="' . esc_attr__( 'Manage Resource', 'wc-frontend-manager-ultimate' ) . '"></span></a>';
				if( apply_filters( 'wcfm_is_allow_booking_resource_delete', true ) ) {
					$actions .= '<a class="wcfm_booking_resource_delete wcfm-action-icon" href="#" data-resourceid="' . $wcfm_bookings_resources_single->ID . '"><span class="wcfmfa fa-trash-alt text_tip" data-tip="' . esc_attr__( 'Delete', 'wc-frontend-manager' ) . '"></span></a>';
				}
				$actions = apply_filters ( 'wcfm_bookings_resources_actions', $actions, $wcfm_bookings_resources_single );
				$wcfm_bookings_resources_json_arr[$index][] = $actions;  
				
				
				$index++;
			}												
		}
		if( !empty($wcfm_bookings_resources_json_arr) ) $wcfm_bookings_resources_json .= json_encode($wcfm_bookings_resources_json_arr);
		else $wcfm_bookings_resources_json .= '[]';
		$wcfm_bookings_resources_json .= '
													}';
													
		echo $wcfm_bookings_resources_json;
	}
}