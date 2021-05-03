<?php
/**
 * WCFM plugin controllers
 *
 * Plugin WC Warranty Products Manage Controller
 *
 * @author 		WC Lovers
 * @package 	wcfmu/controllers/thirdparty
 * @version   4.1.5
 */

class WCFMu_WC_Warranty_Products_Manage_Controller {
	
	public function __construct() {
		global $WCFM;
		
		// Third Party Product Meta Data Save
    add_action( 'after_wcfm_products_manage_meta_save', array( &$this, 'wcfm_wc_warranty_products_manage_meta_save' ), 210, 2 );
	}
	
	/**
	 * WC Warranty Field Product Meta data save
	 */
	function wcfm_wc_warranty_products_manage_meta_save( $new_product_id, $wcfm_products_manage_form_data ) {
		global $WCFM;
		
		if ( !empty( $wcfm_products_manage_form_data['product_warranty_default'] ) && $wcfm_products_manage_form_data['product_warranty_default'] == 'yes' ) {
			delete_post_meta( $new_product_id, '_warranty' );
		} elseif ( isset($wcfm_products_manage_form_data['product_warranty_type']) ) {
			$product_warranty = array();

			if ( $wcfm_products_manage_form_data['product_warranty_type'] == 'no_warranty' ) {
				$product_warranty = array('type' => 'no_warranty');
				update_post_meta( $new_product_id, '_warranty', $product_warranty );
			} elseif ( $wcfm_products_manage_form_data['product_warranty_type'] == 'included_warranty' ) {
				$product_warranty = array(
						'type'      => 'included_warranty',
						'length'    => $wcfm_products_manage_form_data['included_warranty_length'],
						'value'     => $wcfm_products_manage_form_data['limited_warranty_length_value'],
						'duration'  => $wcfm_products_manage_form_data['limited_warranty_length_duration']
				);
				update_post_meta( $new_product_id, '_warranty', $product_warranty );
			} elseif ( $wcfm_products_manage_form_data['product_warranty_type'] == 'addon_warranty' ) {
				$no_warranty= (isset($wcfm_products_manage_form_data['addon_no_warranty'])) ? $wcfm_products_manage_form_data['addon_no_warranty'] : 'no';
				$amounts    = $wcfm_products_manage_form_data['addon_warranty_amount'];
				$values     = $wcfm_products_manage_form_data['addon_warranty_length_value'];
				$durations  = $wcfm_products_manage_form_data['addon_warranty_length_duration'];
				$addons     = array();

				for ($x = 0; $x < count($amounts); $x++) {
					if (!isset($amounts[$x]) || !isset($values[$x]) || !isset($durations[$x])) continue;

					$addons[] = array(
							'amount'    => $amounts[$x],
							'value'     => $values[$x],
							'duration'  => $durations[$x]
					);
				}

				$product_warranty = array(
						'type'                  => 'addon_warranty',
						'addons'                => $addons,
						'no_warranty_option'    => $no_warranty
				);
				update_post_meta( $new_product_id, '_warranty', $product_warranty );
			}

			if ( isset($wcfm_products_manage_form_data['warranty_label']) ) {
				update_post_meta( $new_product_id, '_warranty_label', stripslashes($wcfm_products_manage_form_data['warranty_label']) );
			}
		}
	}
}