<?php
/**
 * WCFM plugin controllers
 *
 * Plugin WC Fancy Products Designer Manage Controller
 *
 * @author 		WC Lovers
 * @package 	wcfmu/controllers/fancy_products
 * @version   5.4.6
 */

class WCFMu_WCFancy_Products_Manage_Controller {
	
	public function __construct() {
		global $WCFM;
		
		// Third Party Product Meta Data Save
    add_action( 'after_wcfm_products_manage_meta_save', array( &$this, 'wcfm_wc_fancy_products_manage_meta_save' ), 210, 2 );
	}
	
	/**
	 * WC Fancy Products Field Product Meta data save
	 */
	function wcfm_wc_fancy_products_manage_meta_save( $new_product_id, $wcfm_products_manage_form_data ) {
		global $WCFM, $WCFMu;
		
		
		if(isset($wcfm_products_manage_form_data["fpd_product_settings"]))
			update_post_meta( $new_product_id, 'fpd_product_settings', htmlentities($wcfm_products_manage_form_data['fpd_product_settings']) );

		//DESKTOP
		if(isset($wcfm_products_manage_form_data["fpd_source_type"]))
			update_post_meta( $new_product_id, 'fpd_source_type', $wcfm_products_manage_form_data['fpd_source_type'] );

		if(isset($wcfm_products_manage_form_data["fpd_products"]))
			update_post_meta( $new_product_id, 'fpd_products', $wcfm_products_manage_form_data['fpd_products'] );

		if(isset($wcfm_products_manage_form_data["fpd_product_categories"]))
			update_post_meta( $new_product_id, 'fpd_product_categories', $wcfm_products_manage_form_data['fpd_product_categories'] );

		//MOBILE
		if(isset($wcfm_products_manage_form_data["fpd_source_type_mobile"]))
			update_post_meta( $new_product_id, 'fpd_source_type_mobile', $wcfm_products_manage_form_data['fpd_source_type_mobile'] );

		if(isset($wcfm_products_manage_form_data["fpd_products_mobile"]))
			update_post_meta( $new_product_id, 'fpd_products_mobile', $wcfm_products_manage_form_data['fpd_products_mobile'] );

		if(isset($wcfm_products_manage_form_data["fpd_product_categories_mobile"]))
			update_post_meta( $new_product_id, 'fpd_product_categories_mobile', $wcfm_products_manage_form_data['fpd_product_categories_mobile'] );

	}
}