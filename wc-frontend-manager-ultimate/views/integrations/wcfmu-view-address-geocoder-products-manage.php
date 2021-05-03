<?php
/**
 * WCFM plugin views
 *
 * Plugin address_geocoder Products Manage Views
 *
 * @author 		WC Lovers
 * @package 	wcfmu/views/thirdparty
 * @version   3.0.0
 */
 
global $wp, $WCFM, $WCFMu, $address_geocoder;

if( !$wcfm_allow_address_geocoder = apply_filters( 'wcfm_is_allow_address_geocoder', true ) ) {
	return;
}

wp_nonce_field( 'save_latlng', 'geocoder_nonce' );
$address_geocoder_options = get_option('address_geocoder_options');
$apikey = $address_geocoder_options['apikey'];
if ( !$apikey || $apikey == '' ) return;

$martygeocoderaddress = apply_filters( 'wcfm_geo_locator_default_address', '' );
$martygeocoderlatlng = apply_filters( 'wcfm_geo_locator_default_latlng', '' );
if( isset( $wp->query_vars['wcfm-products-manage'] ) && !empty( $wp->query_vars['wcfm-products-manage'] ) ) {
	$product_id = $wp->query_vars['wcfm-products-manage'];
	if( $product_id ) {
		$martygeocoderaddress = get_post_meta( $product_id, 'martygeocoderaddress', true );
		$martygeocoderlatlng = get_post_meta( $product_id, 'martygeocoderlatlng', true );
	}
}

?>

<div class="page_collapsible products_manage_address_geocoder simple variable external grouped booking" id="wcfm_products_manage_form_address_geocoder_head"><label class="wcfmfa fa-map-marker"></label><?php echo $address_geocoder_options['meta-box-title']; ?><span></span></div>
<div class="wcfm-container simple variable external grouped booking">
	<div id="wcfm_products_manage_form_address_geocoder_expander" class="wcfm-content">
	  <?php
	  $WCFM->wcfm_fields->wcfm_generate_form_field( array( 
			"martygeocoderaddress" => array( 'label' => __('Address', 'wc-frontend-manager-ultimate'), 'type' => 'text', 'class' => 'wcfm-text wcfm_ele simple variable external grouped booking', 'label_class' => 'wcfm_title simple variable external grouped booking', 'value' => $martygeocoderaddress ),
			"martygeocoderlatlng" => array( 'label' => __('Lat/Lng', 'wc-frontend-manager-ultimate'), 'type' => 'text', 'class' => 'wcfm-text wcfm_ele simple variable external grouped booking', 'label_class' => 'wcfm_title simple variable external grouped booking', 'value' => $martygeocoderlatlng ),
			) );
	  ?>
	  <div class="wcfm_clearfix"></div>
	  <p>
			<a id="geocode" class="button wcfm_submit_button"><?php _e( 'Geocode Address', 'wc-frontend-manager-ultimate' ); ?></a>
	  </p>
	  <div class="wcfm_clearfix"></div><br />
	  <div id="geocodepreview" style="width:400px; height:400px; border:1px solid #DFDFDF;float: right;margin-right:10px;"></div>
		<div class="wcfm_clearfix"></div>
	</div>
</div>