<?php
/**
 * WCFM plugin controllers
 *
 * Plugin License Keys Form Controller
 *
 * @author 		WC Lovers
 * @package 	wcfmu/controllers/integrations/license-manager
 * @version   6.4.0
 */

use LicenseManagerForWooCommerce\Enums\LicenseStatus;
use LicenseManagerForWooCommerce\Repositories\Resources\License as LicenseResourceRepository;
use LicenseManagerForWooCommerce\Models\Resources\License as LicenseResourceModel;

class WCFMu_License_Keys_Manage_Controller {
	
	public function __construct() {
		global $WCFM, $WCFMu;
		
		$this->processing();
	}
	
	public function processing() {
		global $WCFM, $WCFMu, $wpdb;
		
		$wcfm_license_key_form_data = array();
	  parse_str($_POST['wcfm_license_key_form'], $wcfm_license_key_form_data);
	  
	  $has_error = false;
	  
	  if( isset( $wcfm_license_key_form_data['edit__license_key'] ) && !empty( $wcfm_license_key_form_data['edit__license_key'] ) ) {
	  	$licenseId         = absint($wcfm_license_key_form_data['wcfm_license_keys_id']);
			$status            = absint($wcfm_license_key_form_data['edit__status']);
			$orderId           = null;
			$productId         = null;
			$validFor          = null;
			$expiresAt         = null;
			$timesActivatedMax = null;

			if (array_key_exists('edit__order', $wcfm_license_key_form_data) && $wcfm_license_key_form_data['edit__order']) {
				$orderId = $wcfm_license_key_form_data['edit__order'];
			}

			if (array_key_exists('edit__product', $wcfm_license_key_form_data) && $wcfm_license_key_form_data['edit__product']) {
				$productId = $wcfm_license_key_form_data['edit__product'];
			}

			if (array_key_exists('edit__valid_for', $wcfm_license_key_form_data) && $wcfm_license_key_form_data['edit__valid_for']) {
				$validFor  = $wcfm_license_key_form_data['edit__valid_for'];
				$expiresAt = null;
			}

			if (array_key_exists('edit__expires_at', $wcfm_license_key_form_data) && $wcfm_license_key_form_data['edit__expires_at']) {
				$validFor  = null;
				$expiresAt = $wcfm_license_key_form_data['edit__expires_at'];
			}

			if (array_key_exists('edit__times_activated_max', $wcfm_license_key_form_data) && $wcfm_license_key_form_data['edit__times_activated_max']) {
				$timesActivatedMax = absint($wcfm_license_key_form_data['edit__times_activated_max']);
			}

			// Check for duplicates
			if (apply_filters('lmfwc_duplicate', $wcfm_license_key_form_data['edit__license_key'], $licenseId)) {
				echo '{"status": false, "message": "' . __('The license key already exists.', 'lmfwc') . '"}';
			} else {

				/** @var LicenseResourceModel $license */
				$license = LicenseResourceRepository::instance()->update(
						$licenseId,
						array(
								'order_id'            => $orderId,
								'product_id'          => $productId,
								'license_key'         => apply_filters('lmfwc_encrypt', $wcfm_license_key_form_data['edit__license_key']),
								'hash'                => apply_filters('lmfwc_hash', $wcfm_license_key_form_data['edit__license_key']),
								'expires_at'          => $expiresAt,
								'valid_for'           => $validFor,
								'source'              => $wcfm_license_key_form_data['edit__source'],
								'status'              => $status,
								'times_activated_max' => $timesActivatedMax
						)
				);
	
				// Add a message and redirect
				if ($license) {
					echo '{"status": true, "message": "' . __('Your license key has been updated successfully.', 'lmfwc') . '"}';
				} else {
					echo '{"status": false, "message": "' . __('There was a problem updating the license key.', 'lmfwc') . '"}';
				}
			}
		} else {
			echo '{"status": false, "message": "' . __('There was a problem updating the license key.', 'lmfwc') . '"}';
		}
		
		die;
	}
}

class WCFMu_License_Keys_Delete_Controller {
	
	public function __construct() {
		global $WCFM, $WCFMu;
		
		$this->processing();
	}
	
	public function processing() {
		global $WCFM, $WCFMu, $wpdb;
		
		$licenseid = wc_clean( $_POST['licenseid'] );
		
		$result = LicenseResourceRepository::instance()->deleteBy(array('id' => (array)($licenseid)));

		$message = sprintf(esc_html__('%d license key(s) permanently deleted.', 'lmfwc'), $result);
		
		echo '{"status": true, "message": "' . $message. '" }';
		
		die;
	}
}