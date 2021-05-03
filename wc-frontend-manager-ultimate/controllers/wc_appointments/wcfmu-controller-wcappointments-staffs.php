<?php
/**
 * WCFM plugin controllers
 *
 * Plugin WC Appointments Staffs Controller
 *
 * @author 		WC Lovers
 * @package 	wcfmu/controllers
 * @version   2.3.5
 */

class WCFMu_WCAppointments_Staffs_Controller {
	
	public function __construct() {
		global $WCFM;
		
		$this->processing();
	}
	
	public function processing() {
		global $WCFM, $wpdb, $_POST, $WCFMu;
		
		$length = $_POST['length'];
		$offset = $_POST['start'];
		
		$args = array(
									'role__in'     => array( 'shop_staff' ),
									'orderby'      => 'ID',
									'order'        => 'ASC',
									'offset'       => $offset,
									'number'       => $length,
									'count_total'  => false
								 ); 
		
		if( isset( $_POST['search'] ) && !empty( $_POST['search']['value'] )) $args['search'] = $_POST['search']['value'];
		
		$args = apply_filters( 'get_appointment_staff_args', $args );
		
		$wcfm_appointments_staffs_array = get_users( $args );
		
		// Get Product Count
		$appointment_staffs_count = 0;
		$filtered_appointment_staffs_count = 0;
		$appointment_staffs_count = count($wcfm_appointments_staffs_array);
		// Get Filtered Post Count
		$args['number'] = -1;
		$args['offset'] = 0;
		$wcfm_filterd_appointments_staffs_array = get_users( $args );
		$filtered_appointment_staffs_count = count($wcfm_filterd_appointments_staffs_array);
		
		
		// Generate Products JSON
		$wcfm_appointments_staffs_json = '';
		$wcfm_appointments_staffs_json = '{
															"draw": ' . $_POST['draw'] . ',
															"recordsTotal": ' . $appointment_staffs_count . ',
															"recordsFiltered": ' . $filtered_appointment_staffs_count . ',
															"data": ';
		$index = 0;
		$wcfm_appointments_staffs_json_arr = array();
		if(!empty($wcfm_appointments_staffs_array)) {
			foreach( $wcfm_appointments_staffs_array as $wcfm_appointments_staffs_single ) {
				
				// Staff
				$appointment_label =  '<a href="' . get_wcfm_appointments_staffs_manage_url($wcfm_appointments_staffs_single->ID) . '" class="wcfm_appointment_title">' . $wcfm_appointments_staffs_single->user_login . '</a>';
				$wcfm_appointments_staffs_json_arr[$index][] = $appointment_label;
				
				// Name
				$wcfm_appointments_staffs_json_arr[$index][] = $wcfm_appointments_staffs_single->first_name . ' ' . $wcfm_appointments_staffs_single->last_name;
				
				// Email
				$wcfm_appointments_staffs_json_arr[$index][] = $wcfm_appointments_staffs_single->user_email;
				
				// Action
				$actions = apply_filters ( 'wcfm_appointments_staffs_actions', '<a class="wcfm-action-icon" href="' . get_wcfm_appointments_staffs_manage_url( $wcfm_appointments_staffs_single->ID ) . '"><span class="wcfmfa fa-edit text_tip" data-tip="' . esc_attr__( 'Manage Staff', 'wc-frontend-manager-ultimate' ) . '"></span></a>', $wcfm_appointments_staffs_single );
				$wcfm_appointments_staffs_json_arr[$index][] = $actions;  
				
				
				$index++;
			}												
		}
		if( !empty($wcfm_appointments_staffs_json_arr) ) $wcfm_appointments_staffs_json .= json_encode($wcfm_appointments_staffs_json_arr);
		else $wcfm_appointments_staffs_json .= '[]';
		$wcfm_appointments_staffs_json .= '
													}';
													
		echo $wcfm_appointments_staffs_json;
	}
}