<?php
/**
 * WCFM plugin controllers
 *
 * Plugin WC Tych Booking Dashboard Controller
 *
 * @author 		WC Lovers
 * @package 	wcfm/controllers/tych_bookings
 * @version   6.0.0
 */

class WCFMu_Booking_Controller {
	
	public function __construct() {
		global $WCFM;
		
		$this->processing();
	}
	
	public function processing() {
		global $WCFM, $wpdb, $_POST;
		
		$wc_get_booking_status_name = apply_filters( 'wcfmu_bookings_menus', array( 'paid' => __('Paid & Confirmed', 'wc-frontend-manager' ), 'pending-confirmation' => __('Pending Confirmation', 'wc-frontend-manager' ), 'unpaid' => __('Un-paid', 'wc-frontend-manager' ), 'cancelled' => __('Cancelled', 'wc-frontend-manager' ), 'complete' => __('Complete', 'wc-frontend-manager' ), 'confirmed' => __('Confirmed', 'wc-frontend-manager' ) ) );
		
		$length = $_POST['length'];
		$offset = $_POST['start'];
		
		$include_bookings = apply_filters( 'wcfm_wcb_include_tych_bookings', '' );
		
		$args = array(
							'posts_per_page'   => $length,
							'offset'           => $offset,
							'category'         => '',
							'category_name'    => '',
							'orderby'          => 'date',
							'order'            => 'DESC',
							'include'          => $include_bookings,
							'exclude'          => '',
							'meta_key'         => '',
							'meta_value'       => '',
							'post_type'        => 'bkap_booking',
							'post_mime_type'   => '',
							'post_parent'      => '',
							//'author'	   => get_current_user_id(),
							'post_status'      => array_keys( $wc_get_booking_status_name ),
							//'suppress_filters' => 0 
						);
		if( isset( $_POST['search'] ) && !empty( $_POST['search']['value'] )) $args['s'] = $_POST['search']['value'];
		if( isset( $_POST['booking_status'] ) && !empty( $_POST['booking_status'] ) ) { $args['post_status'] = $_POST['booking_status']; }
		
		//if( isset( $_POST['booking_filter'] ) && !empty( $_POST['booking_filter'] ) ) { $args['meta_key'] = '_booking_product_id'; $args['meta_value'] = $_POST['booking_filter']; }
		$current_timestamp = current_time( 'timestamp' );
		$current_time = date( 'YmdHis', $current_timestamp );
		$current_date = date( 'Ymd', $current_timestamp );
		$date = ( isset( $_REQUEST['m'] ) && '' !== $_REQUEST['m'] ) ? $_REQUEST['m'] : '';
				
		if ( ! empty( $_REQUEST['booking_filter'] ) && ! empty( $_REQUEST['booking_view_filter'] ) ) {
					 
			switch ( $_REQUEST['booking_view_filter'] ) {
				case 'today_onwards':
					$args['meta_query'] = array(
						array(
							'key'   => '_bkap_start',
							'value' => $current_time,
							'compare' => '>=',
						),
						array(
							'key'   => '_bkap_product_id',
							'value' => absint( $_REQUEST['booking_filter'] ),
						),
						array( 
							'key' => '_bkap_start',
							'value' => $date,
							'compare' => 'LIKE',
						),
						array(
							'key' => '_bkap_end',
							'value' => $date,
							'compare' => 'LIKE',
						)
					);
					break;
				case 'today_checkin':
					$args['meta_query'] = array(
						array(
							'key'   => '_bkap_start',
							'value' => $current_date,
							'compare' => 'LIKE',
						),
						array(
							'key'   => '_bkap_product_id',
							'value' => absint( $_REQUEST['booking_filter'] ),
						),
						array( 
							'key' => '_bkap_start',
							'value' => $date,
							'compare' => 'LIKE',
						),
						array(
							'key' => '_bkap_end',
							'value' => $date,
							'compare' => 'LIKE',
						)
					);
					break;
				case 'today_checkout':
					$args['meta_query'] = array(
						array(
							'key'   => '_bkap_end',
							'value' => $current_date,
							'compare' => 'LIKE',
						),
						array(
							'key'   => '_bkap_start',
							'value' => $current_date,
							'compare' => 'NOT LIKE',
						),
						array(
							'key'   => '_bkap_product_id',
							'value' => absint( $_REQUEST['booking_filter'] ),
						),
						array( 
							'key' => '_bkap_start',
							'value' => $date,
							'compare' => 'LIKE',
						),
						array(
							'key' => '_bkap_end',
							'value' => $date,
							'compare' => 'LIKE',
						)
					);
					break;
				case 'gcal':
					$args['meta_query'] = array(
						array(
							'key'   => '_bkap_gcal_event_uid',
							'value' => false,
							'compare' => '!=',
						),
						array(
							'key'   => '_bkap_product_id',
							'value' => absint( $_REQUEST['booking_filter'] ),
						),
					);
					break;
			}
		} else if ( ! empty( $_REQUEST['filter_products'] ) ) {
			$args['meta_query'] = array(
				array(
					'key'   => '_bkap_product_id',
					'value' => absint( $_REQUEST['booking_filter'] ),
				),
				array( 
					'key' => '_bkap_start',
					'value' => $date,
					'compare' => 'LIKE',
				),
				array(
					'key' => '_bkap_end',
					'value' => $date,
					'compare' => 'LIKE',
				)
			);
		} else if ( ! empty( $_REQUEST['booking_view_filter'] ) ) {
			 
			switch ( $_REQUEST['booking_view_filter'] ) {
				case 'today_onwards':
					$args['meta_query'] = array(
						array(
							'key'   => '_bkap_start',
							'value' => $current_time,
							'compare' => '>=',
						),
						array( 
							'key' => '_bkap_start',
							'value' => $date,
							'compare' => 'LIKE',
						),
						array(
							'key' => '_bkap_end',
							'value' => $date,
							'compare' => 'LIKE',
						)
					);
					break;
				case 'today_checkin':
					$args['meta_query'] = array(
						array(
							'key'   => '_bkap_start',
							'value' => $current_date,
							'compare' => 'LIKE',
						),
						array( 
							'key' => '_bkap_start',
							'value' => $date,
							'compare' => 'LIKE',
						),
						array(
							'key' => '_bkap_end',
							'value' => $date,
							'compare' => 'LIKE',
						)
					);
					break;
				case 'today_checkout':
					$args['meta_query'] = array(
						array(
							'key'   => '_bkap_end',
							'value' => $current_date,
							'compare' => 'LIKE',
						),
						array(
							'key'   => '_bkap_start',
							'value' => $current_date,
							'compare' => 'NOT LIKE',
						),
						array( 
							'key' => '_bkap_start',
							'value' => $date,
							'compare' => 'LIKE',
						),
						array(
							'key' => '_bkap_end',
							'value' => $date,
							'compare' => 'LIKE',
						)
					);
					break;
				case 'gcal':
					$args['meta_query'] = array(
						array(
							'key'   => '_bkap_gcal_event_uid',
							'value' => false,
							'compare' => '!=',
						),
					);
					break;
			}
		}
		
		if ( ! empty( $_POST['filter_date_form'] ) && ! empty( $_POST['filter_date_to'] ) ) {
			$fyear  = absint( substr( $_POST['filter_date_form'], 0, 4 ) );
			$fmonth = absint( substr( $_POST['filter_date_form'], 5, 2 ) );
			$fday   = absint( substr( $_POST['filter_date_form'], 8, 2 ) );
			
			$tyear  = absint( substr( $_POST['filter_date_to'], 0, 4 ) );
			$tmonth = absint( substr( $_POST['filter_date_to'], 5, 2 ) );
			$tday   = absint( substr( $_POST['filter_date_to'], 8, 2 ) );
			
			$args['date_query'] = array(
																	'after' => array(
																										'year'  => $fyear,
																										'month' => $fmonth,
																										'day'   => $fday,
																									),
																	'before' => array(
																										'year'  => $tyear,
																										'month' => $tmonth,
																										'day'   => $tday,
																									),
																	'inclusive' => true
															);
		}
		
		$args = apply_filters( 'wcfm_bookings_args', $args );
		
		$wcfm_bookings_array = get_posts( $args );
		
		if(defined('WCFM_REST_API_CALL')){
      return $wcfm_bookings_array;
    }
		
		// Get Product Count
		$booking_count = 0;
		$filtered_booking_count = 0;
		$wcfm_bookings_count = wp_count_posts('wc_booking');
		$booking_count = count($wcfm_bookings_array);
		// Get Filtered Post Count
		$args['posts_per_page'] = -1;
		$args['offset'] = 0;
		$wcfm_filterd_bookings_array = get_posts( $args );
		$filtered_booking_count = count($wcfm_filterd_bookings_array);
		
		
		// Generate Products JSON
		$wcfm_bookings_json = '';
		$wcfm_bookings_json = '{
															"draw": ' . $_POST['draw'] . ',
															"recordsTotal": ' . $booking_count . ',
															"recordsFiltered": ' . $filtered_booking_count . ',
															"data": ';
		if(!empty($wcfm_bookings_array)) {
			$index = 0;
			$wcfm_bookings_json_arr = array();
			foreach($wcfm_bookings_array as $wcfm_bookings_single) {
				$booking = new BKAP_Booking( $wcfm_bookings_single->ID );
				//$product_id  = $booking->get_product_id( 'edit' );
				$product     = $booking->get_product();
				$the_order   = $booking->get_order();
				
				$status      = $booking->get_status();
				
				if ( in_array( array( 'was-in-cart', 'in-cart' ), $status ) ) continue;
				
				// Status
				$booking_statuses = bkap_common::get_bkap_booking_statuses();
				$status_label = ( array_key_exists( $status, $booking_statuses ) ) ? $booking_statuses[ $status ] : ucwords( $status );
				$wcfm_bookings_json_arr[$index][] =  '<span class="booking-status tips wcicon-status-' . sanitize_title( $status ) . ' text_tip" data-tip="' . esc_attr( $status_label ) . '"></span>';
				
				// Booking
				$booking_label =  '<a href="' . get_wcfm_view_tych_booking_url($wcfm_bookings_single->ID, $booking) . '" class="wcfm_booking_title">' . __( '#', 'wc-frontend-manager' ) . $wcfm_bookings_single->ID . '</a>';
				
				$customer = $booking->get_customer();
				if ( ! isset( $customer->user_id ) || 0 == $customer->user_id ) {
					$booking_label .= ' by ';
					if( $customer->name ) {
						$guest_name = $customer->name;
						$guest_name = $guest_name; 
						if( apply_filters( 'wcfm_allow_view_customer_email', true ) && $customer->email ) {
							$booking_label .= '<a href="mailto:' .  $customer->email . '">' . $guest_name . '</a>';
						} else {
							$booking_label .= $guest_name;
						}
					} else {
						$booking_label .= sprintf( _x( 'Guest (%s)', 'Guest string with name from booking order in brackets', 'wc-frontend-manager' ), '&ndash;' );
					}
				} elseif ( $customer ) {
					if( $the_order ) {
						$guest_name = $customer->name;
						$booking_label .= ' by ';
						if( apply_filters( 'wcfm_allow_view_customer_email', true ) ) {
							$booking_label .= '<a href="mailto:' .  $customer->email . '">' . $guest_name . '</a>';
						} else {
							$booking_label .= $guest_name;
						}
					} else {
						$guest_name = $customer->name;
						$booking_label .= ' by ';
						if( apply_filters( 'wcfm_allow_view_customer_email', true ) ) {
							$booking_label .= '<a href="mailto:' .  $customer->email . '">' . $guest_name . '</a>';
						} else {
							$booking_label .= $guest_name;
						}
					}
				}
				$wcfm_bookings_json_arr[$index][] = $booking_label;
				
				// Product
				$resource_id 	= $booking->get_resource();
				$product_label = '';
				if ( $product ) {
					$product_label = '<a target="_blank" href="' . get_wcfm_edit_product_url( ( is_callable( array( $product, 'get_id' ) ) ? $product->get_id() : $product->id ) ) . '">' . $product->get_title() . '</a>';
					
					if( $resource_id != "" ) {
						$show_resource = apply_filters( "bkap_display_resource_info_on_view_booking", true, $product, $resource_id );
						if ( $show_resource ) {
							$resource_title = $booking->get_resource_title();
							$product_label .= '<br>( <a target="_blank" href="' . get_wcfm_tych_booking_resources_manage_url( $resource_id ) . '">' . $resource_title . '</a> )';
						}
					}
				} else {
					$product_label = '&ndash;';
				} 
				$wcfm_bookings_json_arr[$index][] = $product_label;
				

				// Order
				if ( $the_order ) {
					if( apply_filters( 'wcfm_is_allow_order_details', true ) && $WCFM->wcfm_vendor_support->wcfm_is_order_for_vendor( $the_order->get_order_number() ) ) {
						$wcfm_bookings_json_arr[$index][] = '<span class="booking-orderno"><a href="' . get_wcfm_view_order_url( $the_order->get_order_number(), $the_order ) . '">#' . $the_order->get_order_number() . '</a></span><br />' . esc_html( wc_get_order_status_name( $the_order->get_status() ) );
					} else {
						$wcfm_bookings_json_arr[$index][] = '<span class="booking-orderno">#' . $the_order->get_order_number() . '</span><br /> ' . esc_html( wc_get_order_status_name( $the_order->get_status() ) );
					}
				} else {
					$wcfm_bookings_json_arr[$index][] = '&ndash;';
				}
				
				// Start Date
				$wcfm_bookings_json_arr[$index][] = $booking->get_start_date() . "<br>" . $booking->get_start_time();
				
				// End Date
				$wcfm_bookings_json_arr[$index][] = $booking->get_start_date() . "<br>" . $booking->get_end_time();
				
				// Quantity
				$wcfm_bookings_json_arr[$index][] = $booking->get_quantity();
				
				// Amount
				$amount = $booking->get_cost();
				$final_amt = $amount * $booking->get_quantity();
				$order_id = $booking->get_order_id();
				
				if ( absint( $order_id ) > 0 && false !== get_post_status( $order_id ) ) {
					$the_order          = wc_get_order( $order_id );
					$currency           = ( version_compare( WOOCOMMERCE_VERSION, "3.0.0" ) < 0 ) ? $the_order->get_order_currency() : $the_order->get_currency();
				} else {
					// get default woocommerce currency
					$currency = get_woocommerce_currency();
				}
				$currency_symbol    = get_woocommerce_currency_symbol( $currency );
				
				$wcfm_bookings_json_arr[$index][] = wc_price( $final_amt, array( 'currency' => $currency) );
				
				// Order Date
				$wcfm_bookings_json_arr[$index][] = $booking->get_date_created();
				
				// Additional Info
				if ( $the_order ) {
					$wcfm_bookings_json_arr[$index][] = apply_filters( 'wcfm_bookings_additonal_data', '&ndash;', $wcfm_bookings_single->ID, $the_order->get_order_number() );
				} else {
					$wcfm_bookings_json_arr[$index][] = apply_filters( 'wcfm_bookings_additonal_data', '&ndash;', $wcfm_bookings_single->ID, 0 );
				}
				
				// Action
				$actions = '';
				if ( current_user_can( 'manage_bookings_settings' ) || current_user_can( 'manage_bookings' ) ) {
					 if ( in_array( $booking->get_status(), array( 'pending-confirmation' ) ) ) $actions = '<a class="wcfm_tych_booking_mark_confirm wcfm-action-icon" href="#" data-bookingid="' . $wcfm_bookings_single->ID . '"><span class="wcfmfa fa-check-circle text_tip" data-tip="' . esc_attr__( 'Mark as Confirmed', 'wc-frontend-manager' ) . '"></span></a>';
				}
				$actions .= apply_filters ( 'wcfm_bookings_actions', '<a class="wcfm-action-icon" href="' . get_wcfm_view_tych_booking_url( $wcfm_bookings_single->ID, $the_booking ) . '"><span class="wcfmfa fa-eye text_tip" data-tip="' . esc_attr__( 'View Details', 'wc-frontend-manager' ) . '"></span></a>', $wcfm_bookings_single, $the_booking );
				$wcfm_bookings_json_arr[$index][] = $actions;  
				
				
				$index++;
			}												
		}
		if( !empty($wcfm_bookings_json_arr) ) $wcfm_bookings_json .= json_encode($wcfm_bookings_json_arr);
		else $wcfm_bookings_json .= '[]';
		$wcfm_bookings_json .= '
													}';
													
		echo $wcfm_bookings_json;
	}
}