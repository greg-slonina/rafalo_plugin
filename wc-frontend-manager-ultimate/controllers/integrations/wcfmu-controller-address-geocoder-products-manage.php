<?php
/**
 * WCFM plugin controllers
 *
 * Plugin Address Geocoder Products Manage Controller
 *
 * @author 		WC Lovers
 * @package 	wcfmu/controllers/thirdparty
 * @version   3.1.1
 */

class WCFMu_Address_Geocoder_Products_Manage_Controller {
	
	public function __construct() {
		global $WCFM;
		
		// Third Party Product Meta Data Save
    add_action( 'after_wcfm_products_manage_meta_save', array( &$this, 'wcfm_acf_products_manage_meta_save' ), 170, 2 );
	}
	
	/**
	 * ACF Field Product Meta data save
	 */
	function wcfm_acf_products_manage_meta_save( $new_product_id, $wcfm_products_manage_form_data ) {
		global $WCFM;
		
		if( isset( $wcfm_products_manage_form_data['martygeocoderaddress'] ) && ! empty( $wcfm_products_manage_form_data['martygeocoderaddress'] ) ) {
		  update_post_meta( $new_product_id, 'martygeocoderaddress', $wcfm_products_manage_form_data['martygeocoderaddress'] );
		  update_post_meta( $new_product_id, 'martygeocoderlatlng', $wcfm_products_manage_form_data['martygeocoderlatlng'] );
		}
	}
}