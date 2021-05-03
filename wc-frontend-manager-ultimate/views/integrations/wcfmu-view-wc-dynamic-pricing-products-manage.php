<?php
/**
 * WCFM plugin view
 *
 * WCFM WC Dynamic Pricing Product Manage View
 *
 * @author 		WC Lovers
 * @package 	wcfmu/views/thirdparty
 * @version   6.2.9
 */
 
global $wp, $WCFM, $WCFMu, $post, $woocommerce;

if( !apply_filters( 'wcfm_is_allow_wc_dynamic_pricing', true ) ) {
	return;
}

$product_id = 0;

$pricing_rule_sets = array();

if( isset( $wp->query_vars['wcfm-products-manage'] ) && !empty( $wp->query_vars['wcfm-products-manage'] ) ) {
	$product_id = absint($wp->query_vars['wcfm-products-manage']);
	
	if( $product_id ) {
		$product           = wc_get_product( $product_id );
		$pricing_rule_sets = WC_Dynamic_Pricing_Compatibility::get_product_meta( $product, '_pricing_rules' );
		$pricing_rule_sets = ! empty( $pricing_rule_sets ) ? $pricing_rule_sets : array();
	}
}


?>

<div class="page_collapsible products_manage_wc_dynamic_pricing simple variable external" id="wcfm_products_manage_form_wc_dynamic_pricing_head"><label class="wcfmfa fa-weight"></label><?php _e('Dynamic Pricing', 'wc-frontend-manager-ultimate'); ?><span></span></div>
<div class="wcfm-container simple variable external">
	<div id="wcfm_products_manage_form_wc_dynamic_pricing_expander" class="wcfm-content">
	  <div id="dynamic_pricing_data" class="panel woocommerce_options_panel wc-metaboxes-wrapper">
			<h2><?php _e('Dynamic Pricing', 'wc-frontend-manager-ultimate'); ?></h2>
			<div class="wcfm-clearfix"></div>
			
      <div id="woocommerce-pricing-rules-wrap" data-setindex="<?php echo count( $pricing_rule_sets ); ?>">
				<?php if ( $pricing_rule_sets && is_array( $pricing_rule_sets ) && sizeof( $pricing_rule_sets ) > 0 ) : ?>

					<?php 
					global $wc_product_pricing_admin, $post;
					if( $product_id ) {
						$post = get_post( $product_id );
					}
					require WC_Dynamic_Pricing::plugin_path() . '/admin/admin-init.php';
					$wc_product_pricing_admin->create_rulesets( $pricing_rule_sets ); 
					?>

				<?php endif; ?>
      </div>

			<button title="<?php _e( 'Allows you to configure another Price Adjustment.  Useful if you have different sets of conditions and pricing adjustments which need to be applied to this product.', 'woocommerce-dynamic-pricing' ); ?>" id="woocommerce-pricing-add-ruleset" type="button" class="button button-primary"><?php _e( 'Add Pricing Group', 'woocommerce-dynamic-pricing' ); ?></button>
			<div class="clear"></div>
			<div class="wcfm-clearfix"></div>
	  </div>
	</div>
	<?php
	$dynamic_pricing_args = array(
			'product_id'         => $product_id,
			'price_discount'     => __( 'Price Discount', 'woocommerce-dynamic-pricing' ),
			'percent_discount'   => __( 'Percentage Discount', 'woocommerce-dynamic-pricing' ),
			'fixed_price'        => __( 'Fixed Price', 'woocommerce-dynamic-pricing' ),
			'add_img'            => WC_Dynamic_Pricing::plugin_url() . '/assets/images/add.png',
			'remove_img'         => WC_Dynamic_Pricing::plugin_url() . '/assets/images/remove.png',
			'no'                 => __( 'No', 'woocommerce-dynamic-pricing' ),
			'yes'                => __( 'Yes', 'woocommerce-dynamic-pricing' ),
			'remove_price'       => __( 'Are you sure you would like to remove this price adjustment?', 'woocommerce-dynamic-pricing' ),
			'remove_price_set'   => __( 'Are you sure you would like to remove this price set?', 'woocommerce-dynamic-pricing' )
	);
	
	wp_localize_script( 'wcfmu_wc_dynamic_pricing_products_manage_js', 'wcfm_dynamic_pricing_args', $dynamic_pricing_args );
	?>
</div>