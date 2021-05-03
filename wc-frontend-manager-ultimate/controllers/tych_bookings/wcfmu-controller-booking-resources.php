<?php
/**
 * WCFM plugin controllers
 *
 * Plugin WC Tych Bookings Resources Controller
 *
 * @author 		WC Lovers
 * @package 	wcfmu/controllers/tych_bookings
 * @version   6.0.0
 */

class WCFMu_Booking_Resources_Controller {
	
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
							'post_type'        => 'bkap_resource',
							'post_mime_type'   => '',
							'post_parent'      => '',
							//'author'	   => get_current_user_id(),
							'post_status'      => 'publish',
							//'suppress_filters' => 0 
						);
		if( isset( $_POST['search'] ) && !empty( $_POST['search']['value'] )) $args['s'] = $_POST['search']['value'];
		
		$args = apply_filters( 'get_booking_resources_args', $args );
		
		$wcfm_bookings_resources_array = get_posts( $args );
		
		// Get Product Count
		$booking_resources_count = 0;
		$filtered_booking_resources_count = 0;
		$wcfm_bookings_resurces_count = wp_count_posts('bkap_resource');
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
				$booking_label =  '<a href="' . get_wcfm_tych_booking_resources_manage_url($wcfm_bookings_resources_single->ID) . '" class="wcfm_booking_title">' . __( '#', 'wc-frontend-manager-ultimate' ) . $wcfm_bookings_resources_single->ID . ' - ' . $wcfm_bookings_resources_single->post_title . '</a>';
				$wcfm_bookings_resources_json_arr[$index][] = $booking_label;
				
				// End Date
				$wcfm_bookings_resources_json_arr[$index][] = date_i18n( wc_date_format() . ' ' . wc_time_format(), strtotime($wcfm_bookings_resources_single->post_date) );
				
				// Action
				$actions = apply_filters ( 'wcfm_bookings_resources_actions', '<a class="wcfm-action-icon" href="' . get_wcfm_tych_booking_resources_manage_url( $wcfm_bookings_resources_single->ID ) . '"><span class="wcfmfa fa-edit text_tip" data-tip="' . esc_attr__( 'Manage Resource', 'wc-frontend-manager-ultimate' ) . '"></span></a>', $wcfm_bookings_resources_single );
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