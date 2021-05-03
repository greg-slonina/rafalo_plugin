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

class WCFMu_Facebook_Marketplace_Settings_Controller {

	public function __construct() {

		$this->processing();
	}

	public function processing() {
		global $WCFM, $WCFM_Query, $wpdb, $_POST;

		$form_data = array();
		parse_str($_POST['form'], $form_data);

		if( !defined('WCFM_REST_API_CALL') ) {
			if( isset( $form_data['wcfm_nonce'] ) && !empty( $form_data['wcfm_nonce'] ) ) {
				if( !wp_verify_nonce( $form_data['wcfm_nonce'], 'wcfm_facebook_marketplace_settings' ) ) {
					echo '{"status": false, "message": "' . __( 'Invalid nonce! Refresh your page and try again.', 'wc-frontend-manager-ultimate' ) . '"}';
					die;
				}

				// no need to save nonce field to database
				unset( $form_data['wcfm_nonce'] );
			}
		}

		if( wcfm_is_vendor() ) {
			$user_id = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );
		} else {
			$user_id = absint( $form_data['vendor_id'] );

			// no need to save vendor_id to database
			unset( $form_data['vendor_id'] );
		}

		// WCFM form custom validation filter
		$custom_validation_results = apply_filters( 'wcfm_form_custom_validation', $form_data, 'facebook_marketplace_settings_manage' );
		if(isset($custom_validation_results['has_error']) && !empty($custom_validation_results['has_error'])) {
			$custom_validation_error = __( 'There has some error in submitted data.', 'wc-frontend-manager-ultimate' );
			if( isset( $custom_validation_results['message'] ) && !empty( $custom_validation_results['message'] ) ) { $custom_validation_error = $custom_validation_results['message']; }
			echo '{"status": false, "message": "' . $custom_validation_error . '"}';
			die;
		}

		update_user_meta( $user_id, 'wcfm_facebook_marketplace_settings', $form_data );

		echo '{"status": true, "message": "' . __( 'Settings saved successfully', 'wc-frontend-manager-ultimate' ) . '"}';

		die;
	}
}
