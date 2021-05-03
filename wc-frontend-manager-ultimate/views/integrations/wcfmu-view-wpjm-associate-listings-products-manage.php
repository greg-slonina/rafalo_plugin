<?php
/**
 * WCFM plugin views
 *
 * Plugin Products WP Job Manager Products Manage Views
 *
 * @author 		WC Lovers
*  @package 	wcfmu/views/thirdparty
 * @version   2.1.0
 */
global $wp, $WCFM, $WCFMu;

$wcfm_is_allow_listings = apply_filters( 'wcfm_is_allow_listings', true );
if( !$wcfm_is_allow_listings || !apply_filters( 'wcfm_is_allow_associate_listings_for_products', true ) ) {
	return;
}

// WP Job Manage Support
$listings = array();
$wpjm_listings = '';

if( isset( $wp->query_vars['wcfm-products-manage'] ) && !empty( $wp->query_vars['wcfm-products-manage'] ) ) {
	$product_id = $wp->query_vars['wcfm-products-manage'];
	if( $product_id ) {
		// WP Job Manage Support
		$wpjm_listings = get_post_meta( $product_id, '_wpjm_listings', true );
	}
}

$args = array(
	'posts_per_page'   => -1,
	'offset'           => 0,
	'category'         => '',
	'category_name'    => '',
	'orderby'          => 'date',
	'order'            => 'DESC',
	'include'          => '',
	'exclude'          => '',
	'meta_key'         => '',
	'meta_value'       => '',
	'post_type'        => 'job_listing',
	'post_mime_type'   => '',
	'post_parent'      => '',
	//'author'	   => get_current_user_id(),
	'post_status'      => array('publish'),
	'suppress_filters' => 0 
);
$args = apply_filters( 'wcfm_listing_args', $args );

$listings_objs = get_posts( $args );
$wpjm_listings_array = array();
if( !empty($listings_objs) ) {
	foreach( $listings_objs as $listings_obj ) {
		$wpjm_listings_array[esc_attr( $listings_obj->ID )] = esc_html( $listings_obj->post_title );
	}
}
?>
<!-- collapsible 15 - WP Job Manage Support -->
<div class="page_collapsible products_manage_wpjm_listings simple variable grouped external booking" id="wcfm_products_manage_form_wpjm_listings_head"><label class="wcfmfa fa-list-ul"></label><?php _e('Associate Listings', 'wc-frontend-manager-ultimate'); ?><span></span></div>
<div class="wcfm-container simple variable external grouped booking">
	<div id="wcfm_products_manage_form_wpjm_listings_expander" class="wcfm-content">
		<?php
		$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'product_manage_fields_wpjm_listings', array(  
																																												"wpjm_listings" => array('label' => __('Listings', 'wc-frontend-manager-ultimate') , 'type' => 'select', 'attributes' => array( 'multiple' => 'multiple', 'style' => 'width: 60%;' ), 'class' => 'wcfm-select wcfm_ele simple variable grouped external booking', 'label_class' => 'wcfm_title wcfm_ele simple variable grouped external booking', 'options' => $wpjm_listings_array, 'value' => $wpjm_listings, 'hints' => __( 'Associate this product with your Listings.', 'wc-frontend-manager-ultimate' ))
																																							)) );
		?>
	</div>
</div>
<!-- end collapsible -->
<div class="wcfm_clearfix"></div>