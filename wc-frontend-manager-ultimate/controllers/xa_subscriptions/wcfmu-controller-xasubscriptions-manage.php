<?php
/**
 * WCFM plugin controllers
 *
 * Plugin XA Subscription Manage Controller
 *
 * @author 		WC Lovers
 * @package 	wcfm/controllers/wc_subscriptions
 * @version   4.1.0
 */

class WCFMu_XASubscriptions_Manage_Controller {
	
	public function __construct() {
		global $WCFM;
		
		$this->processing();
	}
	
	public function processing() {
		global $WCFM, $WCFMu, $wpdb, $wcfm_wcs_billing_schedule_update_form_data;
		
		$wcfm_wcs_billing_schedule_update_form_data = array();
	  parse_str($_POST['wcfm_wcs_billing_schedule_update_form'], $wcfm_wcs_billing_schedule_update_form_data);
	  
	  // WCFM form custom validation filter
		$custom_validation_results = apply_filters( 'wcfm_form_custom_validation', $wcfm_wcs_billing_schedule_update_form_data, 'wcs_billing_schedule' );
		if(isset($custom_validation_results['has_error']) && !empty($custom_validation_results['has_error'])) {
			$custom_validation_error = __( 'There has some error in submitted data.', 'wc-frontend-manager' );
			if( isset( $custom_validation_results['message'] ) && !empty( $custom_validation_results['message'] ) ) { $custom_validation_error = $custom_validation_results['message']; }
			echo '{"status": false, "message": "' . $custom_validation_error . '"}';
			die;
		}
	  
	  $subscription_id = absint( $wcfm_wcs_billing_schedule_update_form_data['subscription_id'] );
	  
	  if( $subscription_id ) {
	  	
	  	if ( isset( $wcfm_wcs_billing_schedule_update_form_data['_billing_interval'] ) ) {
				update_post_meta( $subscription_id, '_billing_interval', $wcfm_wcs_billing_schedule_update_form_data['_billing_interval'] );
			}
			
			if (!empty($wcfm_wcs_billing_schedule_update_form_data['next_payment'])) {
				$next_payment_date = $wcfm_wcs_billing_schedule_update_form_data['next_payment'] . ' ' . $wcfm_wcs_billing_schedule_update_form_data['next_payment_hour'] . ':' . $wcfm_wcs_billing_schedule_update_form_data['next_payment_minute'] . ':' . date("s");
				update_post_meta($subscription_id, '_schedule_next_payment', $next_payment_date);
			} else {
				$next_payment_date = date('Y-m-d H:i:s', current_time('timestamp', true));
				update_post_meta($subscription_id, '_next_payment', $next_payment_date);
				update_post_meta($subscription_id, '_schedule_next_payment', $next_payment_date);
			}

			if ( ! empty( $wcfm_wcs_billing_schedule_update_form_data['_billing_period'] ) ) {
				update_post_meta( $subscription_id, '_billing_period', $wcfm_wcs_billing_schedule_update_form_data['_billing_period'] );
			}

			$subscription = hforce_get_subscription( $subscription_id );

			$dates = array();

			foreach ( hforce_get_subscription_available_date_types() as $date_type => $date_label ) {
				$date_key = hf_normalise_date_type_key( $date_type );

				if ( 'last_order_date_created' == $date_key ) {
					continue;
				}

				$utc_timestamp_key = $date_type . '_timestamp_utc';

				// A subscription needs a created date, even if it wasn't set or is empty
				if ( 'date_created' === $date_key && empty( $wcfm_wcs_billing_schedule_update_form_data[ $utc_timestamp_key ] ) ) {
					$datetime = current_time( 'timestamp', true );
				} elseif ( isset( $wcfm_wcs_billing_schedule_update_form_data[ $utc_timestamp_key ] ) ) {
					$datetime = $wcfm_wcs_billing_schedule_update_form_data[ $utc_timestamp_key ];
				} else { // No date to set
					continue;
				}

				$dates[ $date_key ] = gmdate( 'Y-m-d H:i:s', $datetime );
			}
			if (isset($next_payment_date)) {
				$dates['next_payment'] = $next_payment_date;
			}
			try {
				$subscription->update_dates( $dates, 'gmt' );

				wp_cache_delete( $subscription_id, 'posts' );
			} catch ( Exception $e ) {
				echo '{"status": false, "message": "' . $e->getMessage() . '"}';
				//wcs_add_admin_notice( $e->getMessage(), 'error' );
			}

			$subscription->save();
	  	
	  	do_action( 'wcfm_wcs_billing_schedule_update', $subscription_id, $wcfm_wcs_billing_schedule_update_form_data );
	  	
	  	echo '{"status": true, "message": "' . __( 'Subscription schedule updated successfully', 'wc-frontend-manager-ultimate' ) . '"}';
	  } else {
	  	echo '{"status": false, "message": "' . __( 'Subscription schedule updated failed!', 'wc-frontend-manager-ultimate' ) . '"}';
	  }
		 
		die;
	}
}