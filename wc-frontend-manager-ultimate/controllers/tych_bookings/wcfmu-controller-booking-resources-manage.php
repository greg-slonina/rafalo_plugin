<?php
/**
 * WCFM plugin controllers
 *
 * Plugin Tych Booking Resources Manage Controller
 *
 * @author 		WC Lovers
 * @package 	wcfmu/controllers/tych_bookings
 * @version   6.0.0
 */

class WCFMu_Booking_Resources_Manage_Controller {
	
	public function __construct() {
		global $WCFM, $WCFMu;
		
		$this->processing();
	}
	
	public function processing() {
		global $WCFM, $WCFMu, $wpdb, $wcfm_resource_manager_form_data;
		
		$wcfm_resource_manager_form_data = array();
	  parse_str($_POST['wcfm_resources_manage_form'], $wcfm_resource_manager_form_data);
	  
	  $wcfm_resource_messages = get_wcfm_resources_manage_messages();
	  $has_error = false;
	  
	  if(isset($wcfm_resource_manager_form_data['title']) && !empty($wcfm_resource_manager_form_data['title'])) {
	  	$resource_id = 0;
	  	if(isset($wcfm_resource_manager_form_data['resource_id']) && $wcfm_resource_manager_form_data['resource_id'] == 0) {
				$id = wp_insert_post( array(
					'post_title'   => $wcfm_resource_manager_form_data['title'],
					'menu_order'   => 0,
					'post_content' => '',
					'post_status'  => 'publish',
					'post_author'  => apply_filters( 'wcfm_current_vendor_id', get_current_user_id() ),
					'post_type'    => 'bkap_resource',
				), true );
		
				if ( $id && ! is_wp_error( $id ) ) {
					
					update_post_meta( $id, '_bkap_resource_qty', 1 );
					update_post_meta( $id, '_bkap_resource_availability', array() );
		
					$resource_id = $id;
				}
			} else { // For Update
				$resource_id = $wcfm_resource_manager_form_data['resource_id'];
				//$resource = new BKAP_Product_Resource( $resource_id );
			}
			
			if( $resource_id ) {
				$qty = !empty($wcfm_resource_manager_form_data['_bkap_booking_qty']) ? $wcfm_resource_manager_form_data['_bkap_booking_qty'] : 1;
				update_post_meta( $resource_id, '_bkap_resource_qty', $qty );
				
				$availability = array();
				$row_size     = isset($wcfm_resource_manager_form_data['wc_booking_availability_type'] ) ? sizeof($wcfm_resource_manager_form_data['wc_booking_availability_type'] ) : 0;
		
				if ( isset($wcfm_resource_manager_form_data['wc_booking_availability_bookable_hidden'] ) ) {
					$_POST['wc_booking_availability_bookable'] =$wcfm_resource_manager_form_data['wc_booking_availability_bookable_hidden']; // Assiging hidden values for bookable data.    
				}    
		
				for ( $i = 0; $i < $row_size; $i ++ ) {
		
					$availability[ $i ]['bookable'] = 0;
	
					if( isset($wcfm_resource_manager_form_data['wc_booking_availability_bookable'] ) ) {
						$availability[ $i ]['bookable'] = wc_clean($wcfm_resource_manager_form_data['wc_booking_availability_bookable'][ $i ] );
					}
	
					$availability[ $i ]['type']     = wc_clean($wcfm_resource_manager_form_data['wc_booking_availability_type'][ $i ] );
	
					$availability[ $i ]['priority'] = intval($wcfm_resource_manager_form_data['wc_booking_availability_priority'][ $i ] );
	
					switch ( $availability[ $i ]['type'] ) {
						case 'custom' :
							$availability[ $i ]['from'] = wc_clean($wcfm_resource_manager_form_data['wc_booking_availability_from_date'][ $i ] );
							$availability[ $i ]['to']   = wc_clean($wcfm_resource_manager_form_data['wc_booking_availability_to_date'][ $i ] );
							break;
						case 'months' :
							$availability[ $i ]['from'] = wc_clean($wcfm_resource_manager_form_data['wc_booking_availability_from_month'][ $i ] );
							$availability[ $i ]['to']   = wc_clean($wcfm_resource_manager_form_data['wc_booking_availability_to_month'][ $i ] );
							break;
						case 'weeks' :
							$availability[ $i ]['from'] = wc_clean($wcfm_resource_manager_form_data['wc_booking_availability_from_week'][ $i ] );
							$availability[ $i ]['to']   = wc_clean($wcfm_resource_manager_form_data['wc_booking_availability_to_week'][ $i ] );
							break;
						case 'days' :
							$availability[ $i ]['from'] = wc_clean($wcfm_resource_manager_form_data['wc_booking_availability_from_day_of_week'][ $i ] );
							$availability[ $i ]['to']   = wc_clean($wcfm_resource_manager_form_data['wc_booking_availability_to_day_of_week'][ $i ] );
							break;
						case 'time' :
						case 'time:1' :
						case 'time:2' :
						case 'time:3' :
						case 'time:4' :
						case 'time:5' :
						case 'time:6' :
						case 'time:7' :
							$availability[ $i ]['from'] = wc_booking_sanitize_time($wcfm_resource_manager_form_data['wc_booking_availability_from_time'][ $i ] );
							$availability[ $i ]['to']   = wc_booking_sanitize_time($wcfm_resource_manager_form_data['wc_booking_availability_to_time'][ $i ] );
							break;
						case 'time:range' :
							$availability[ $i ]['from'] = wc_booking_sanitize_time($wcfm_resource_manager_form_data['wc_booking_availability_from_time'][ $i ] );
							$availability[ $i ]['to']   = wc_booking_sanitize_time($wcfm_resource_manager_form_data['wc_booking_availability_to_time'][ $i ] );

							$availability[ $i ]['from_date'] = wc_clean($wcfm_resource_manager_form_data['wc_booking_availability_from_date'][ $i ] );
							$availability[ $i ]['to_date']   = wc_clean($wcfm_resource_manager_form_data['wc_booking_availability_to_date'][ $i ] );
							break;
					}
				}
				
				update_post_meta( $resource_id, '_bkap_resource_availability', $availability );
	  	} else {
	  		$has_error = true;
	  	}
	  	
	  	if(!$has_error) { echo '{"status": true, "message": "' . $wcfm_resource_messages['resource_published'] . '", "redirect": "' . get_wcfm_tych_booking_resources_manage_url( $resource_id ) . '"}'; }
	  	else { echo '{"status": false, "message": "' . $wcfm_resource_messages['resource_failed'] . '"}'; }
	  	
	  	
	  }  else {
			echo '{"status": false, "message": "' . $wcfm_resource_messages['no_title'] . '"}';
		}
		
		die;
	}
}