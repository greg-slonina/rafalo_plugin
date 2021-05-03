<?php
/**
 * WCFM plugin views
 *
 * Plugin WC Vatiations Additional Image Products Manage Views
 *
 * @author 		WC Lovers
 * @package 	wcfmu/views/thirdparty
 * @version   3.0.2
 */

// WC Vatiations Additional Image Variaton Date Edit
add_filter( 'wcfm_variation_edit_data', 'wcfmu_thirdparty_wc_vatiation_gallery_product_data_variations', 10, 4 );

function wcfmu_thirdparty_wc_vatiation_gallery_product_data_variations( $variations, $variation_id, $variation_id_key, $product_id ) {
	global $wp, $WCFM, $WCFMu, $wpdb;
	
	if( $variation_id  ) {
		
		$gallery_img_ids  = '';
		$gallery_img_urls = array();
		if( function_exists( 'woodmart_vg_admin_html' ) ) {
			if ( ! woodmart_get_opt( 'variation_gallery' ) ) {
				return $variations;
			}
			
			$variation_gallery_data = get_post_meta( $product_id, 'woodmart_variation_gallery_data', true );
			if( is_array( $variation_gallery_data ) && !empty( $variation_gallery_data ) ) {
				foreach ( $variation_gallery_data as $variation_gallery_data_id => $image_ids ) {
					if ( $variation_id == $variation_gallery_data_id ) {
						$gallery_img_ids = array_filter( explode( ',', $image_ids ) );
					}
				}
			}
		} else {
			$gallery_img_ids = get_post_meta( $variation_id, '_wc_additional_variation_images', true );
			$gallery_img_ids = array_filter( explode( ',', $gallery_img_ids ) );
		}
		if( $gallery_img_ids ) {
			if( is_array( $gallery_img_ids ) && !empty( $gallery_img_ids ) ) {
				foreach( $gallery_img_ids as $gallery_img_id ) {
					$gallery_img_urls[]['gallery_image'] = $gallery_img_id; //wp_get_attachment_url( $gallery_img_id );
				}
			}
		}
		
		$variations[$variation_id_key]['wc_additional_variation_images'] = $gallery_img_urls;
	}
	return $variations;
}

// WC Vatiations Additional Image View
add_filter( 'wcfm_product_manage_fields_variations', 'wcfmu_thirdparty_wc_vatiation_gallery_product_manage_fields_variations', 160, 4 );

function wcfmu_thirdparty_wc_vatiation_gallery_product_manage_fields_variations( $variation_fileds, $variations, $variation_shipping_option_array, $variation_tax_classes_options ) {
	global $wp, $WCFM, $WCFMu, $wpdb;
	
	if( function_exists( 'woodmart_vg_admin_html' ) ) {
		if ( ! woodmart_get_opt( 'variation_gallery' ) ) {
			return $variation_fileds;
		}
	}
	
	if( $wcfm_is_allow_gallery = apply_filters( 'wcfm_is_allow_gallery', true ) ) {
		$gallerylimit = apply_filters( 'wcfm_gallerylimit', -1 );
		$variation_gallery_fields = array( "wc_additional_variation_images" => array( 'label' => __( 'Aditional Images', 'wc-frontend-manager-ultimate' ), 'type' => 'multiinput', 'class' => 'wcfm_additional_variation_images wcfm_ele variable', 'label_class' => 'wcfm_title', 'custom_attributes' => array( 'limit' => $gallerylimit ), 'options' => array(
																															"gallery_image" => array( 'type' => 'upload', 'prwidth' => 75),
																													) ) );
		
		$image_index = array_search( 'image', array_keys( $variation_fileds ) );
		if( !$image_index ) { $image_index = 6; } else { $image_index += 1; }
		
		$variation_fileds = array_slice($variation_fileds, 0, $image_index, true) +
																	$variation_gallery_fields +
																	array_slice($variation_fileds, $image_index, count($variation_fileds) - 1, true) ;
	}
	
	return $variation_fileds;
}
?>