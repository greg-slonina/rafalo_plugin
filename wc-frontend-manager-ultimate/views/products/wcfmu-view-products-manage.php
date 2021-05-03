<?php
/**
 * WCFM plugin view
 *
 * WCFM Product Manage View
 *
 * @author 		WC Lovers
 * @package 	wcfmu/view
 * @version   1.0.0
 */
 
$wcfm_is_allow_manage_products = apply_filters( 'wcfm_is_allow_manage_products', true );
if( !$wcfm_is_allow_manage_products ) {
	return;
}

global $wp, $WCFM, $WCFMu;

$product_id = '';
$enable_reviews = 'enable';
$menu_order = '';
$purchase_note = '';

if( isset( $wp->query_vars['wcfm-products-manage'] ) && !empty( $wp->query_vars['wcfm-products-manage'] ) ) {
	$product_id = $wp->query_vars['wcfm-products-manage'];
	if( $product_id ) {
		$product = wc_get_product( $product_id );
		// Fetching Product Data
		if($product && !empty($product)) {
			// Product Advance Options
			$product_post = get_post( $product_id );
			$enable_reviews = ( $product_post->comment_status == 'open' ) ? 'enable' : '';
			$menu_order = $product_post->menu_order;
			$purchase_note = get_post_meta( $product_id, '_purchase_note', true );
			
		}
	}
}

?>

  <?php 
  $wcfm_pm_block_class_advanced = apply_filters( 'wcfm_pm_block_class_advanced', 'simple variable external grouped booking' );
  if( !apply_filters( 'wcfm_is_allow_advanced', true ) || !apply_filters( 'wcfm_is_allow_pm_advanced', true ) ) { 
    $wcfm_pm_block_class_advanced = 'wcfm_block_hide';
  }
  ?>
	<!-- collapsible 10 -->
	<div class="page_collapsible products_manage_advanced <?php echo $wcfm_pm_block_class_advanced; ?> <?php echo apply_filters( 'wcfm_pm_block_custom_class_advanced', '' ); ?>" id="wcfm_products_manage_form_advanced_head"><label class="wcfmfa fa-th-large"></label><?php _e('Advanced', 'wc-frontend-manager-ultimate'); ?><span></span></div>
	<div class="wcfm-container <?php echo $wcfm_pm_block_class_advanced; ?>">
		<div id="wcfm_products_manage_form_advanced_expander" class="wcfm-content">
			<?php
			$enable_reviews_class = '';
			if ( 'yes' !== get_option( 'woocommerce_enable_reviews', 'yes' ) ) {
				$enable_reviews_class = 'wcfm_custom_hide';
			}
			$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_product_manage_fields_advanced', array(  
																																													"enable_reviews" => array('label' => __('Enable reviews', 'wc-frontend-manager-ultimate') , 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele simple variable external grouped booking ' . $enable_reviews_class, 'value' => 'enable', 'label_class' => 'wcfm_title checkbox_title ' . $enable_reviews_class, 'dfvalue' => $enable_reviews),
																																													"menu_order" => array('label' => __('Menu Order', 'wc-frontend-manager-ultimate') , 'type' => 'number', 'class' => 'wcfm-text wcfm_ele simple variable external grouped booking', 'label_class' => 'wcfm_title', 'value' => $menu_order, 'hints' => __( 'Custom ordering position.', 'wc-frontend-manager-ultimate' )),
																																													"purchase_note" => array('label' => __('Purchase Note', 'wc-frontend-manager-ultimate') , 'type' => 'textarea', 'class' => 'wcfm-textarea wcfm_ele simple variable grouped booking', 'label_class' => 'wcfm_ele wcfm_title simple variable grouped booking', 'value' => $purchase_note, 'hints' => __( 'Enter an optional note to send the customer after purchase.', 'wc-frontend-manager-ultimate' ))
																																								), $product_id ) );
			?>
		</div>
	</div>
	<!-- end collapsible -->
	<div class="wcfm_clearfix"></div>

  <?php do_action( 'end_wcfmu_products_manage', $product_id ); ?>