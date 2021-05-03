<?php
global $WCFM, $WCFMu, $wpdb, $wp_query, $wp, $_GET;

$request_view_id = NULL;
if( isset( $wp->query_vars['wcfm-fncy-product-builder'] ) && !empty( $wp->query_vars['wcfm-fncy-product-builder'] ) ) {
	$request_view_id = $wp->query_vars['wcfm-fncy-product-builder'];
}

/*$vendor_id   = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );
	
//get all fancy products
if( wcfm_is_vendor() ) {
	$fancy_products = FPD_Product::get_products( array(
		'order_by' 	=> 'title ASC',
		'where' => "user_id={$vendor_id }"
	) );
} else {
	$fancy_products = FPD_Product::get_products( array(
		'order_by' 	=> 'title ASC',
	) );
}

if(sizeof($fancy_products) == 0) {
	echo '<div class="updated"><p><strong>'.__('There are no products!', 'radykal').'</strong></p></div></div>';
	return;
}*/

$_GET['page'] = 'fpd_product_builder';
?>

<div class="collapse wcfm-collapse" id="wcfm_build_listing">
	
	<div class="wcfm-page-headig">
		<span class="wcfmfa fa-object-group"></span>
		<span class="wcfm-page-heading-text"><?php _e( 'Product Builder', 'wc-frontend-manager-ultimate' ); ?></span>
		<?php do_action( 'wcfm_page_heading' ); ?>
	</div>
	<div class="wcfm-collapse-content">
		<div id="wcfm_page_load"></div>
		<?php do_action( 'before_wcfm_fpd_product_builder' ); ?>
		
		<div class="wcfm-container wcfm-top-element-container">
			<h2><?php _e('Product Builder', 'wc-frontend-manager-ultimate' ); ?></h2>
			
			<?php
			echo '<a id="add_new_product_dashboard" class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_fncy_product_designer_url().'" data-tip="' . __('Product Designer', 'wc-frontend-manager-ultimate') . '"><span class="wcfmfa fa-object-group"></span><span class="text">' . __( 'Product Designer', 'wc-frontend-manager-ultimate' ) . '</span></a>';
			?>
			
			<div class="wcfm-clearfix"></div>
	  </div>
	  <div class="wcfm-clearfix"></div><br />
		

		<div class="wcfm-container">
			<div id="wcfm_fpd_product_builder_expander" class="wcfm-content">
			
				<?php do_action( 'fpd_backend_react_page_start' ); ?>
				<div id="fpd-react-root"></div>
				<?php do_action( 'fpd_backend_react_page_end' ); ?>

				<div class="wcfm-clearfix"></div>
			</div>
			<div class="wcfm-clearfix"></div>
		</div>
	
		<div class="wcfm-clearfix"></div>
		<?php
		do_action( 'after_wcfm_fpd_product_builder' );
		?>
	</div>
</div>

