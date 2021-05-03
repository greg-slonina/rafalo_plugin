<?php
/**
 * WCFM plugin view
 *
 * WCFM WooCommerce Product Badge Product Manage View
 *
 * @author 		WC Lovers
 * @package 	wcfmu/views/thirdparty
 * @version   6.0.5
 */
 
global $wp, $WCFM, $WCFMu, $post, $woocommerce;

if( !apply_filters( 'wcfm_is_allow_wc_product_badge', true ) ) {
	return;
}

$product_id = 0;

$wpbm_choose_badge          = array();

if( isset( $wp->query_vars['wcfm-products-manage'] ) && !empty( $wp->query_vars['wcfm-products-manage'] ) ) {
	$product_id = $wp->query_vars['wcfm-products-manage'];
	
	if( $product_id ) {
		$woo_pro_badge_meta_box         = (array) get_post_meta( $product_id, 'woo_pro_badge_meta_box', true );
		
		if( isset( $woo_pro_badge_meta_box['wpbm_choose_badge'] ) ) {
			$wpbm_choose_badge = $woo_pro_badge_meta_box['wpbm_choose_badge']; 
		}
	}
}

$pro_badges = get_posts(array(
	'post_type' => 'woo_product_badges',
	'posts_per_page' => -1
));	

$badge_list = array();
foreach ($pro_badges as $badge) {
	$badge_list[$badge->ID] = $badge->post_title;
}
?>

<div class="page_collapsible products_manage_wc_product_badge simple variable external" id="wcfm_products_manage_form_wc_product_badge_head"><label class="wcfmfa fa-certificate"></label><?php _e('Badges', 'wc-frontend-manager-ultimate'); ?><span></span></div>
<div class="wcfm-container simple variable external">
	<div id="wcfm_products_manage_form_wc_product_badge_expander" class="wcfm-content">
	  <div id='wc_product_badge' class='panel woocommerce_options_panel'>
			<h2><?php _e('Product Badges', 'wc-frontend-manager-ultimate'); ?></h2>
			<div class="wcfm-clearfix"></div>
			
			<?php
			$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_product_manage_wc_min_max_quantities_fields', array( 
						"wpbm_choose_badge" => array( 'label' => __( 'Select Badges', 'woo_product_badge_manager_txtd' ), 'type' => 'select', 'name' => 'woo_pro_badge_meta_box[wpbm_choose_badge]', 'options' => $badge_list, 'class' => 'wcfm-select wcfm_ele simple variable external groupd booking', 'label_class' => 'wcfm_title wcfm_ele simple variable external groupd booking', 'value' => $wpbm_choose_badge, 'hints' => __( 'You can directly pick badges to assign with this product.', 'woo_product_badge_manager_txtd' ), 'attributes' => array( 'multiple' => 'multiple', 'style' => 'width: 60%;' ) ),
			), $product_id ) );
			?>
			<div class="wcfm-clearfix"></div>
	  </div>
	</div>
	<script>
	jQuery( document ).ready( function( $ ) {
		$("#wpbm_choose_badge").select2({ placeholder: wcfm_dashboard_messages.choose_select2 + ' ...' });
	});
	</script>
</div>