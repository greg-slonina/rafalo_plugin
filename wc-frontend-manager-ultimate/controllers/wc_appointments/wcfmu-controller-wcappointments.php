<?php
/**
 * WCFM plugin controllers
 *
 * Plugin WC Appointment Dashboard Controller
 *
 * @author 		WC Lovers
 * @package 	wcfm/controllers
 * @version   2.4.0
 */

class WCFMu_WCAppointments_Controller {
	
	public function __construct() {
		global $WCFM;
		
		$this->processing();
	}
	
	public function processing() {
		global $WCFM, $wpdb, $_POST, $WCFMu;
		
		$wc_get_appointment_status_name = array( 'paid' => __('Paid', 'wc-frontend-manager-ultimate' ), 'pending-confirmation' => __('Pending Confirmation', 'wc-frontend-manager-ultimate' ), 'unpaid' => __('Un-paid', 'wc-frontend-manager-ultimate' ), 'cancelled' => __('Cancelled', 'wc-frontend-manager-ultimate' ), 'complete' => __('Complete', 'wc-frontend-manager-ultimate' ), 'confirmed' => __('Confirmed', 'wc-frontend-manager-ultimate' ) );
		
		if ( class_exists( 'WC_Deposits' ) ) {
			$wc_get_appointment_status_name['wc-partial-payment'] = __( 'Partial Paid', 'wc-frontend-manager-ultimate' );
		}
			
			
		
		$length = $_POST['length'];
		$offset = $_POST['start'];
		
		$include_appointments = apply_filters( 'wcfm_wca_include_appointments', '' );
		
		$args = array(
							'posts_per_page'   => $length,
							'offset'           => $offset,
							'category'         => '',
							'category_name'    => '',
							'orderby'    => 'date',
							'order'      => 'DESC',
							'include'          => $include_appointments,
							'exclude'          => '',
							'meta_key'         => '',
							'meta_value'       => '',
							'post_type'        => 'wc_appointment',
							'post_mime_type'   => '',
							'post_parent'      => '',
							//'author'	   => get_current_user_id(),
							'post_status'      => array_keys( $wc_get_appointment_status_name ),
							//'suppress_filters' => 0 
						);
		if( isset( $_POST['search'] ) && !empty( $_POST['search']['value'] )) $args['s'] = $_POST['search']['value'];
		if( isset( $_POST['appointment_status'] ) && !empty( $_POST['appointment_status'] ) && ( $_POST['appointment_status'] != 'all' ) ) { $args['post_status'] = $_POST['appointment_status']; }
		if( !empty( $_POST['appointment_filter'] ) && !empty( $_POST['appointment_staff_filter'] ) ) {
			$args['meta_query'] = array(
																	'relation' => 'AND',
																	array(
																		'key'   => '_appointment_product_id',
																		'value' => absint( $_POST['appointment_filter'] ),
																	),
																	array(
																		'key'   => '_appointment_staff_id',
																		'value' => absint( $_POST['appointment_staff_filter'] ),
																	),
																);
		} elseif( !empty( $_POST['appointment_filter'] ) && empty( $_POST['appointment_staff_filter'] ) ) { 
			$args['meta_query'] = array(
																	array(
																		'key'   => '_appointment_product_id',
																		'value' => absint( $_POST['appointment_filter'] ),
																	)
																);
		} elseif( empty( $_POST['appointment_filter'] ) && !empty( $_POST['appointment_staff_filter'] ) ) { 
			$args['meta_query'] = array(
																	array(
																		'key'   => '_appointment_staff_id',
																		'value' => absint( $_POST['appointment_staff_filter'] ),
																	),
																);
		}
		
		if ( ! empty( $_POST['filter_date_form'] ) && ! empty( $_POST['filter_date_to'] ) ) {
			$args['meta_query'][] = array(
																		'relation' => 'AND',
																		array(
																			'key'   => '_appointment_start',
																			'value' => esc_sql( date( 'Ymd000000',  strtotime( $_POST['filter_date_form'] ) ) ),
																			'compare' => '>=',
																		),
																		array(
																			'key' => '_appointment_start',
																			'value' => esc_sql( date( 'Ymd000000',  strtotime( $_POST['filter_date_to'] . ' +1 day' ) ) ),
																			'compare' => '<=',
																		)
															);
		}
		
		$args = apply_filters( 'wcfm_appointments_args', $args );
		$wcfm_appointments_array = get_posts( $args );
		
		// Get Product Count
		$appointment_count = 0;
		$filtered_appointment_count = 0;
		$wcfm_appointments_count = wp_count_posts('wc_appointment');
		$appointment_count = count($wcfm_appointments_array);
		// Get Filtered Post Count
		$args['posts_per_page'] = -1;
		$args['offset'] = 0;
		$wcfm_filterd_appointments_array = get_posts( $args );
		$filtered_appointment_count = count($wcfm_filterd_appointments_array);
		
		
		// Generate Products JSON
		$wcfm_appointments_json = '';
		$wcfm_appointments_json = '{
															"draw": ' . $_POST['draw'] . ',
															"recordsTotal": ' . $appointment_count . ',
															"recordsFiltered": ' . $filtered_appointment_count . ',
															"data": ';
		if(!empty($wcfm_appointments_array)) {
			$index = 0;
			$wcfm_appointments_json_arr = array();
			foreach($wcfm_appointments_array as $wcfm_appointments_single) {
				$the_appointment = new WC_Appointment( $wcfm_appointments_single->ID );
				$product_id  = $the_appointment->get_product_id( 'edit' );
				$staff_ids	 = $the_appointment->get_staff_ids( 'edit' );
				$staff_ids	 = ! is_array( $staff_ids ) ? array( $staff_ids ) : $staff_ids;
				$product     = $the_appointment->get_product( $product_id );
				$the_order   = $the_appointment->get_order();
				if ( $the_appointment->has_status( array( 'was-in-cart', 'in-cart' ) ) ) continue;
				
				// Status
				$wcfm_appointments_json_arr[$index][] =  '<span class="appointment-status tips wcicon-status-' . sanitize_title( $the_appointment->get_status( ) ) . ' text_tip" data-tip="' . $wc_get_appointment_status_name[$the_appointment->get_status()] . '"></span>';
				
				// Appointment
				$appointment_label =  '<a href="' . get_wcfm_view_appointment_url($wcfm_appointments_single->ID, $the_appointment) . '" class="wcfm_appointment_title">' . __( '#', 'wc-frontend-manager-ultimate' ) . $wcfm_appointments_single->ID . '</a>';
				
				$customer = $the_appointment->get_customer();
				if ( ! isset( $customer->user_id ) || 0 == $customer->user_id ) {
					$appointment_label .= ' by ';
					if( $customer->full_name ) {
						$guest_name = $customer->full_name;
					} else {
						$guest_name = ' - ';
					}
					$appointment_label .= sprintf( _x( 'Guest (%s)', 'Guest string with name from appointment order in brackets', 'wc-frontend-manager-ultimate' ), $guest_name );
				} elseif ( $customer ) {
					$appointment_label .= ' ' . __( 'by', 'wc-frontend-manager' ) . ' ';
					if( apply_filters( 'wcfm_allow_view_customer_email', true ) ) {
						$appointment_label .= '<a class="wcfm_appointment_by_customer" href="mailto:' .  $customer->email . '">' . $customer->full_name . '</a>';
					} else {
						$appointment_label .= '<span class="wcfm_appointment_by_customer">' . $customer->full_name . '</span>';
					}
				}
				$wcfm_appointments_json_arr[$index][] = $appointment_label;
				
				// Product
				//$resource = $the_appointment->get_resource();

				if ( $product ) {
					$product_post = get_post($product->get_ID());
					$wcfm_appointments_json_arr[$index][] = $product_post->post_title;
					//if ( $resource ) {
						//$wcfm_appointments_json_arr[$index][] = $resource->post_title;
					//}
				} else {
					$wcfm_appointments_json_arr[$index][] = '&ndash;';
				}
				
				// #of Persons
				/*$persons = get_post_meta( $wcfm_appointments_single->ID, '_appointment_persons', true );
				$total_persons = 0;
				if ( ! empty( $persons ) && is_array( $persons ) ) {
					foreach ( $persons as $person_count ) {
						$total_persons = $total_persons + $person_count;
					}
				}

				$wcfm_appointments_json_arr[$index][] =  esc_html( $total_persons );*/
				
				// Order
				if ( $the_order ) {
					if( apply_filters( 'wcfm_is_allow_order_details', true ) && $WCFM->wcfm_vendor_support->wcfm_is_order_for_vendor( $the_order->get_order_number() ) ) {
						$wcfm_appointments_json_arr[$index][] = '<span class="appointment-orderno"><a href="' . get_wcfm_view_order_url( $the_order->get_order_number(), $the_order ) . '">#' . $the_order->get_order_number() . '</a></span>' . esc_html( wc_get_order_status_name( $the_order->get_status() ) );
					} else  {
						$wcfm_appointments_json_arr[$index][] = '<span class="appointment-orderno">#' . $the_order->get_order_number() . '</span>' . esc_html( wc_get_order_status_name( $the_order->get_status() ) );
					}
				} else {
					$wcfm_appointments_json_arr[$index][] = '&ndash;';
				}
				
				// Staff
				$staffs = '';
				foreach( $staff_ids as $staff_id ) {
					$staff          = new WC_Product_Appointment_Staff( $staff_id );
					if( $staffs ) $staffs .= ', ';  
					$staffs .= $staff->display_name;
				}
				if( !$staffs ) $staffs = '&ndash;';
				$wcfm_appointments_json_arr[$index][] = $staffs;
				
				// Start Date
				$wcfm_appointments_json_arr[$index][] = date_i18n( wc_date_format() . ' ' . wc_time_format(), $the_appointment->get_start( 'edit' ) );
				
				// End Date
				$wcfm_appointments_json_arr[$index][] = date_i18n( wc_date_format() . ' ' . wc_time_format(), $the_appointment->get_end( 'edit' ) );
				
				// Additional Info
				if ( $the_order ) {
					$wcfm_appointments_json_arr[$index][] = apply_filters( 'wcfm_appointments_additonal_data', '&ndash;', $wcfm_appointments_single->ID, $the_order->get_id() );
				} else {
					$wcfm_appointments_json_arr[$index][] = apply_filters( 'wcfm_appointments_additonal_data', '&ndash;', $wcfm_appointments_single->ID, 0 );
				}
				
				// Action
				$actions = '';
				if ( current_user_can( 'manage_appointments' ) ) {
					if ( in_array( $the_appointment->get_status(), array( 'pending-confirmation' ) ) ) $actions = '<a class="wcfm_appointment_mark_confirm wcfm-action-icon" href="#" data-appointmentid="' . $wcfm_appointments_single->ID . '"><span class="wcfmfa fa-check-circle text_tip" data-tip="' . esc_attr__( 'Mark as Confirmed', 'wc-frontend-manager-ultimate' ) . '"></span></a>';
				}
				$actions .= apply_filters ( 'wcfm_appointments_actions', '<a class="wcfm-action-icon" href="' . get_wcfm_view_appointment_url( $wcfm_appointments_single->ID, $the_appointment ) . '"><span class="wcfmfa fa-eye text_tip" data-tip="' . esc_attr__( 'View Details', 'wc-frontend-manager-ultimate' ) . '"></span></a>', $wcfm_appointments_single, $the_appointment );
				$wcfm_appointments_json_arr[$index][] = $actions;  
				
				
				$index++;
			}												
		}
		if( !empty($wcfm_appointments_json_arr) ) $wcfm_appointments_json .= json_encode($wcfm_appointments_json_arr);
		else $wcfm_appointments_json .= '[]';
		$wcfm_appointments_json .= '
													}';
													
		echo $wcfm_appointments_json;
	}
}