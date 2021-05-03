<?php
/**
* WCFM plugin controllers
*
* Facebook Marketplace Settings Controller
*
* @author 		WC Lovers
* @package 	wcfm/controllers
* @version   1.1.6
*/

class WCFMu_Facebook_Marketplace_Get_Sync_Status_Controller {

	public function __construct() {

		$this->vendor_id   = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );

		$this->processing();
	}

	public function processing() {
		global $WCFMu;

		check_admin_referer( $WCFMu->wcfmu_facebook_marketplace::ACTION_GET_SYNC_STATUS, 'nonce' );

		$remaining_products = 0;

		$jobs = facebook_for_woocommerce()->get_products_sync_background_handler()->get_jobs( array(
			'status' => 'processing',
		) );

		if ( ! empty( $jobs ) ) {

			// there should only be one processing job at a time, pluck the latest to convey status
			$job = $jobs[0];

			$remaining_products = ! empty( $job->total ) ? $job->total : count( $job->requests );

			if ( ! empty( $job->progress ) ) {
				$remaining_products -= $job->progress;
			}

		}

		wp_send_json_success( $remaining_products );
	}
}
