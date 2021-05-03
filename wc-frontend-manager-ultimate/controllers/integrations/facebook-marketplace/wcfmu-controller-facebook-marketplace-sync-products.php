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

class WCFMu_Facebook_Marketplace_Sync_Products_Controller {

	public function __construct() {

		$this->vendor_id   = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );

		$this->processing();
	}

	public function processing() {
		global $WCFMu;

		check_admin_referer( $WCFMu->wcfmu_facebook_marketplace::ACTION_SYNC_PRODUCTS, 'nonce' );

		$WCFMu->wcfmu_facebook_marketplace->get_products_sync_handler( $this->vendor_id )->create_or_update_all_products();

		wp_send_json_success();
	}
}
