<?php
/**
 * WCFMu plugin controllers
 *
 * WC Variation Image Gallery Products Manage Controller
 *
 * @author 		WC Lovers
 * @package 	wcfmu/controllers/thirdparty/
 * @version   3.1.0
 */

class WCFMu_WC_Variation_Gallery_Products_Manage_Controller {
	
	public function __construct() {
		global $WCFM, $WCFMu;
		
		add_filter( 'wcfm_product_variation_data_factory', array( &$this, 'wcfmu_wc_vatiation_gallery_product_variation_save' ), 160, 5 );
	}
	
	/**
	 * WC Variation Image Gallery Variation Data Save
	 */
	function wcfmu_wc_vatiation_gallery_product_variation_save( $wcfm_variation_data, $new_product_id, $variation_id, $variations, $wcfm_products_manage_form_data ) {
	 	global $wpdb, $WCFM, $WCFMu;
	 	
	 	if( function_exists( 'woodmart_vg_admin_html' ) ) {
			if ( ! woodmart_get_opt( 'variation_gallery' ) ) {
				return $wcfm_variation_data;
			}
		}
	 	  
		if(isset($variations['wc_additional_variation_images']) && !empty($variations['wc_additional_variation_images'])) {
			$gallery = array();
			foreach($variations['wc_additional_variation_images'] as $gallery_imgs) {
				if(isset($gallery_imgs['gallery_image']) && !empty($gallery_imgs['gallery_image'])) {
					$gallery_img_id = $WCFM->wcfm_get_attachment_id($gallery_imgs['gallery_image']);
					$gallery[] = $gallery_img_id;
				}
			}
			if ( ! empty( $gallery ) ) {
				if( function_exists( 'woodmart_vg_admin_html' ) ) {
					$variation_gallery_data = get_post_meta( $new_product_id, 'woodmart_variation_gallery_data', true );
					$output                 = $variation_gallery_data ? $variation_gallery_data : array();
					$ids                    = implode( ',', $gallery );
			
					$output[ $variation_id ] = $ids;
					$output                  = array_filter( $output );
			
					update_post_meta( $new_product_id, 'woodmart_variation_gallery_data', $output );
				} else {
					update_post_meta( $variation_id, '_wc_additional_variation_images', implode( ',', $gallery ) );
				}
			}
		}
		
		return $wcfm_variation_data;
	}
}