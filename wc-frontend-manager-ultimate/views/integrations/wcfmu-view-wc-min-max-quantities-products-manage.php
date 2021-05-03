<?php
/**
 * WCFM plugin view
 *
 * WCFM WC Min/Max Quantities Product Manage View
 *
 * @author 		WC Lovers
 * @package 	wcfmu/views/thirdparty
 * @version   6.0.5
 */
 
global $wp, $WCFM, $WCFMu, $post, $woocommerce;

if( !apply_filters( 'wcfm_is_allow_wc_min_max_quantities', true ) ) {
	return;
}

$product_id = 0;

$minimum_allowed_quantity          = '';
$maximum_allowed_quantity          = '';
$group_of_quantity                 = '';

$allow_combination                 = '';
$minmax_do_not_count               = '';
$minmax_cart_exclude               = '';
$minmax_category_group_of_exclude  = '';

if( isset( $wp->query_vars['wcfm-products-manage'] ) && !empty( $wp->query_vars['wcfm-products-manage'] ) ) {
	$product_id = $wp->query_vars['wcfm-products-manage'];
	
	if( $product_id ) {
		$minimum_allowed_quantity         = get_post_meta( $product_id, 'minimum_allowed_quantity', true );
		$maximum_allowed_quantity         = get_post_meta( $product_id, 'maximum_allowed_quantity', true );
		$group_of_quantity                = get_post_meta( $product_id, 'group_of_quantity', true );
		
		$allow_combination                = get_post_meta( $product_id, 'allow_combination', true ) ? get_post_meta( $product_id, 'allow_combination', true ) : 'no';
		$minmax_do_not_count              = get_post_meta( $product_id, 'minmax_do_not_count', true ) ? get_post_meta( $product_id, 'minmax_do_not_count', true ) : 'no';
		$minmax_cart_exclude              = get_post_meta( $product_id, 'minmax_cart_exclude', true ) ? get_post_meta( $product_id, 'minmax_cart_exclude', true ) : 'no';
		$minmax_category_group_of_exclude = get_post_meta( $product_id, 'minmax_category_group_of_exclude', true ) ? get_post_meta( $product_id, 'minmax_category_group_of_exclude', true ) : 'no';
	}
}
?>

<div class="page_collapsible products_manage_wc_min_max_quantities simple variable external" id="wcfm_products_manage_form_wc_min_max_quantities_head"><label class="wcfmfa fa-thermometer-quarter"></label><?php _e('Min/Max Quantities', 'wc-frontend-manager-ultimate'); ?><span></span></div>
<div class="wcfm-container simple variable external">
	<div id="wcfm_products_manage_form_wc_min_max_quantities_expander" class="wcfm-content">
	  <div id='wc_min_max_quantities' class='panel woocommerce_options_panel'>
			<h2><?php _e('MIn/Max Quantities', 'wc-frontend-manager-ultimate'); ?></h2>
			<div class="wcfm-clearfix"></div>
			
			<?php
			$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_product_manage_wc_min_max_quantities_fields', array( 
						"minimum_allowed_quantity" => array( 'label' => __( 'Minimum quantity', 'woocommerce-min-max-quantities' ) , 'type' => 'number', 'class' => 'wcfm-text wcfm_ele simple variable external', 'label_class' => 'wcfm_title wcfm_ele simple variable external', 'value' => $minimum_allowed_quantity, 'hints' => __( 'Enter a quantity to prevent the user buying this product if they have fewer than the allowed quantity in their cart', 'woocommerce-min-max-quantities' ), 'attributes' => array( 'min' => 0, 'step' => 1 ) ),
						"maximum_allowed_quantity" => array( 'label' => __( 'Maximum quantity', 'woocommerce-min-max-quantities' ) , 'type' => 'number', 'class' => 'wcfm-text wcfm_ele simple variable external', 'label_class' => 'wcfm_title wcfm_ele simple variable external', 'value' => $maximum_allowed_quantity, 'hints' => __( 'Enter a quantity to prevent the user buying this product if they have more than the allowed quantity in their cart', 'woocommerce-min-max-quantities' ), 'attributes' => array( 'min' => 0, 'step' => 1 ) ),
						"group_of_quantity" => array( 'label' => __( 'Group of...', 'woocommerce-min-max-quantities' ) , 'type' => 'number', 'class' => 'wcfm-text wcfm_ele simple variable external', 'label_class' => 'wcfm_title wcfm_ele simple variable external', 'value' => $group_of_quantity, 'hints' => __( 'Enter a quantity to only allow this product to be purchased in groups of X', 'woocommerce-min-max-quantities' ), 'attributes' => array( 'min' => 0, 'step' => 1 ) ),
						
						"allow_combination" => array( 'label' => __( 'Allow Combination', 'woocommerce-min-max-quantities' ) , 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele variable', 'label_class' => 'wcfm_title wcfm_ele variable checkbox_title', 'value' =>'yes', 'dfvalue' => $allow_combination, 'hints' => __( 'Allow combination of variations to satisfy the min/max rules above.', 'woocommerce-min-max-quantities' ) ),
						"minmax_do_not_count" => array( 'label' => __( 'Order rules: Do not count', 'woocommerce-min-max-quantities' ) , 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele simple variable external', 'label_class' => 'wcfm_title wcfm_ele simple variable external checkbox_title', 'value' =>'yes', 'dfvalue' => $minmax_do_not_count, 'hints' => __( 'Don\'t count this product against your minimum order quantity/value rules.', 'woocommerce-min-max-quantities' ) ),
						"minmax_cart_exclude" => array( 'label' => __( 'Order rules: Exclude', 'woocommerce-min-max-quantities' ) , 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele simple variable external', 'label_class' => 'wcfm_title wcfm_ele simple variable external checkbox_title', 'value' =>'yes', 'dfvalue' => $minmax_cart_exclude, 'hints' => __( 'Exclude this product from minimum order quantity/value rules. If this is the only item in the cart, rules will not apply.', 'woocommerce-min-max-quantities' ) ),
						"minmax_category_group_of_exclude" => array( 'label' => __( 'Category rules: Exclude', 'woocommerce-min-max-quantities' ) , 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele simple variable external', 'label_class' => 'wcfm_title wcfm_ele simple variable external checkbox_title', 'value' =>'yes', 'dfvalue' => $minmax_category_group_of_exclude, 'hints' => __( 'Exclude this product from category group-of-quantity rules. This product will not be counted towards category groups.', 'woocommerce-min-max-quantities' ) ),
			), $product_id ) );
			?>
			<div class="wcfm-clearfix"></div>
	  </div>
	</div>
</div>