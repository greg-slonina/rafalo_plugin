<?php
/**
* WCFM plugin controllers
*
* Facebook Marketplace Disconnect Controller
*
* @author 		WC Lovers
* @package 	wcfm/controllers
* @version   1.1.6
*/

class WCFMu_Facebook_Marketplace_Disconnect_Controller {

	public function __construct() {

		$this->vendor_id   = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );

		$this->processing();
	}

	public function processing() {
		global $WCFMu;

		check_admin_referer( $WCFMu->wcfmu_facebook_marketplace->get_connection_handler( $this->vendor_id )::ACTION_DISCONNECT );

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_die( __( 'You do not have permission to uninstall Facebook Business Extension.', 'wc-frontend-manager-ultimate' ) );
		}

		try {

			$user_id = get_user_meta( $this->vendor_id, $WCFMu->wcfmu_facebook_marketplace->get_connection_handler( $this->vendor_id )::OPTION_SYSTEM_USER_ID, true );
			$response = $WCFMu->wcfmu_facebook_marketplace->get_api( $this->vendor_id )->get_user( $user_id );
			$response = $WCFMu->wcfmu_facebook_marketplace->get_api( $this->vendor_id )->delete_user_permission( $response->get_id(), 'manage_business_extension' );

			$this->disconnect();

			facebook_for_woocommerce()->get_message_handler()->add_message( __( 'Uninstall successful', 'wc-frontend-manager-ultimate' ) );

		} catch ( SV_WC_API_Exception $exception ) {

			facebook_for_woocommerce()->log( sprintf( 'Uninstall failed: %s', $exception->getMessage() ) );

			facebook_for_woocommerce()->get_message_handler()->add_error( __( 'Uninstall unsuccessful. Please try again.', 'wc-frontend-manager-ultimate' ) );
		}

		wp_safe_redirect( wcfm_get_endpoint_url( 'wcfm-facebook-marketplace', '', get_wcfm_page() ) );
		exit;
	}

	/**
	 * Disconnects the plugin.
	 *
	 * Deletes local asset data.
	 *
	 * @since 2.0.0
	 */
	private function disconnect() {
		global $WCFMu;

		$connection_handler = $WCFMu->wcfmu_facebook_marketplace->get_connection_handler( $this->vendor_id );
		$integration = $WCFMu->wcfmu_facebook_marketplace->get_integration( $this->vendor_id );

		// $connection_handler->update_access_token( '' );
		// $connection_handler->update_merchant_access_token( '' );
		// $connection_handler->update_system_user_id( '' );
		// $connection_handler->update_business_manager_id( '' );
		// $connection_handler->update_ad_account_id( '' );
		// $integration->update_facebook_page_id( '' );
		// $integration->update_facebook_pixel_id( '' );
		// $integration->update_product_catalog_id( '' );

		delete_user_meta( $this->vendor_id, $connection_handler::OPTION_ACCESS_TOKEN );
		delete_user_meta( $this->vendor_id, $connection_handler::OPTION_MERCHANT_ACCESS_TOKEN );
		delete_user_meta( $this->vendor_id, $connection_handler::OPTION_SYSTEM_USER_ID );
		delete_user_meta( $this->vendor_id, $connection_handler::OPTION_BUSINESS_MANAGER_ID );
		delete_user_meta( $this->vendor_id, $connection_handler::OPTION_AD_ACCOUNT_ID );
		delete_user_meta( $this->vendor_id, $integration::SETTING_FACEBOOK_PAGE_ID );
		delete_user_meta( $this->vendor_id, $integration::SETTING_FACEBOOK_PIXEL_ID );
		delete_user_meta( $this->vendor_id, $integration::OPTION_PRODUCT_CATALOG_ID );
	}
}