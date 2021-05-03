<?php
/**
 * WCFM plugin controllers
 *
 * Plugin WC Tych Booking Products Manage Controller
 *
 * @author 		WC Lovers
 * @package 	wcfm/controllers
 * @version   6.0.0
 */

class WCFMu_Booking_Products_Manage_Controller {
	
	public function __construct() {
		global $WCFM;
		
		// Booking Product Meta Data Save
    add_action( 'after_wcfm_products_manage_meta_save', array( &$this, 'wcb_wcfm_products_manage_meta_save' ), 20, 2 );
	}
	
	/**
	 * WC Booking Product Meta data save
	 */
	function wcb_wcfm_products_manage_meta_save( $new_product_id, $wcfm_products_manage_form_data ) {
		global $wpdb, $WCFM, $bkap_weekdays;
		
		// Product ID
		$post_id = $new_product_id;
		$product_id = bkap_common::bkap_get_product_id( $post_id );
		
		// Booking Options Tab settings
		$clean_booking_options = '';
		//if ( isset( $wcfm_products_manage_form_data[ 'booking_options' ] ) ) {
			$post_booking_options = $wcfm_products_manage_form_data;
			
			$post_booking_options['booking_type'] = 'only_day';
			if( isset( $wcfm_products_manage_form_data['booking_enable_type'] ) ) {
				if( $wcfm_products_manage_form_data['booking_enable_type'] == 'booking_enable_date_and_time' ) {
					$post_booking_options['booking_type'] = 'date_time';
					if( isset( $wcfm_products_manage_form_data['booking_enable_date_time'] ) ) {
						if( $wcfm_products_manage_form_data['booking_enable_date_time'] == 'booking_enable_duration_time' ) {
							$post_booking_options['booking_type'] = 'duration_time';
						}
					}
				} else {
					if( isset( $wcfm_products_manage_form_data['booking_enable_only_day'] ) ) {
						if( $wcfm_products_manage_form_data['booking_enable_only_day'] == 'booking_enable_multiple_days' ) {
							$post_booking_options['booking_type'] = 'multiple_days';
						}
					}
				}
			}
			
			//$tempData = str_replace( "\\", "", $post_booking_options );
			$clean_booking_options = (object) $post_booking_options;
		//}
		
				
		// Settings Tab settings
		$clean_settings_data = '';
		if ( isset( $wcfm_products_manage_form_data[ 'booking_settings_data' ] ) ) {
			$post_settings_data = $wcfm_products_manage_form_data[ 'booking_settings_data' ];
			$tempData = str_replace( "\\", "", $post_settings_data );
			$clean_settings_data = json_decode($tempData);
		}
		
		$ranges_array = array();
		// Fixed Blocks Tab
		if ( isset( $wcfm_products_manage_form_data[ 'booking_blocks_enabled' ] ) ) {
			$ranges_array[ 'blocks_enabled' ] = $wcfm_products_manage_form_data[ 'booking_blocks_enabled' ];
		}
		
		// Fixed Block Booking table data.
		$clean_fixed_block_data = '';
		if ( isset( $wcfm_products_manage_form_data[ 'booking_fixed_block_data' ] ) ) {
			$post_fixed_block_data = $wcfm_products_manage_form_data[ 'booking_fixed_block_data' ];
			$tempData = str_replace( "\\", "", $post_fixed_block_data );
			$clean_fixed_block_data = json_decode( $tempData );
		}
		
		// Fixed Block Booking table data.
		$clean_price_range_data = '';
		if ( isset( $wcfm_products_manage_form_data[ 'booking_price_range_data' ] ) ) {
			$post_price_range_data = $wcfm_products_manage_form_data[ 'booking_price_range_data' ];
			$clean_price_range_data = (object) array( 
					'bkap_price_range_data' => stripslashes( $post_price_range_data ) );
		}
		
		
		// Price Ranges Tab
		if ( isset( $wcfm_products_manage_form_data[ 'booking_ranges_enabled' ] ) ) {
			$ranges_array[ 'ranges_enabled' ] = $wcfm_products_manage_form_data[ 'booking_ranges_enabled' ];
		}
		
		// GCal Tab
		$clean_gcal_data = '';
		if ( isset( $wcfm_products_manage_form_data[ 'booking_gcal_data' ] ) ) {
			$post_gcal_data = $wcfm_products_manage_form_data[ 'booking_gcal_data' ];
			$tempData = str_replace( "\\", "", $post_gcal_data );
			$clean_gcal_data = json_decode($tempData);
		}
		
		$booking_box_class = new bkap_booking_box_class();
		$booking_box_class->setup_data( $product_id, $clean_booking_options, $clean_settings_data, $ranges_array, $clean_gcal_data, $clean_fixed_block_data, $clean_price_range_data );
		
		
		// Resources 
		$booking_settings[ '_bkap_resource' ] = '';

        
		if ( isset( $wcfm_products_manage_form_data[ '_bkap_resource' ] ) && "on" == $wcfm_products_manage_form_data[ '_bkap_resource' ] ) {
			$booking_settings[ '_bkap_resource' ] = "on";
			update_post_meta( $product_id, '_bkap_resource', $wcfm_products_manage_form_data['_bkap_resource'] );
		} else {
			update_post_meta( $product_id, '_bkap_resource', '' );
		}
		
		if ( isset( $wcfm_products_manage_form_data['bkap_product_resource_lable'] ) ) {
			update_post_meta( $product_id, '_bkap_product_resource_lable', $wcfm_products_manage_form_data['bkap_product_resource_lable'] );	
		}

		if ( isset( $wcfm_products_manage_form_data['bkap_product_resource_selection'] ) ) {
			update_post_meta( $product_id, '_bkap_product_resource_selection', $wcfm_products_manage_form_data['bkap_product_resource_selection'] );
		}
		
		$resource_id = array();
		
		if ( isset( $wcfm_products_manage_form_data['resource_id'] ) ) {
			$resource_id = $wcfm_products_manage_form_data['resource_id'];
			update_post_meta( $product_id, '_bkap_product_resources', $resource_id );
		}
		
		$resource_cost = array();
		
		if ( isset( $wcfm_products_manage_form_data['resource_cost'] ) ) {
			foreach ( $resource_id as $key => $value ) {
				$resource_cost[$value] = $wcfm_products_manage_form_data['resource_cost'][$key];
			}
			update_post_meta( $product_id, '_bkap_resource_base_costs', $resource_cost );
		}
	}
	
}