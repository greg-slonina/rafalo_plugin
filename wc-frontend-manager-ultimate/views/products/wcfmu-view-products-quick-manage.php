<?php
global $WCFM, $WCFMu, $wpdb;

$product_id = '';
if( isset($_POST['product']) ) {
	$product_id = $_POST['product'];
	$product = wc_get_product( $product_id );
	
	// Custom Validation Check
	$wcfm_custom_validation_options = get_option( 'wcfm_custom_validation_options', array() );
	$sku_required = false;
	$price_required = false;
	$sales_price_required = false;
	$stock_required = false;
	if( !empty( $wcfm_custom_validation_options ) ) {
		if( isset( $wcfm_custom_validation_options['sku'] ) ) $sku_required = true;
		if( isset( $wcfm_custom_validation_options['regular_price'] ) ) $price_required = true;
		if( isset( $wcfm_custom_validation_options['sale_price'] ) ) $sales_price_required = true;
		if( isset( $wcfm_custom_validation_options['stock_qty'] ) ) $stock_required = true;
	}
	
	// Capaility Check
	$is_manage_sku = true;
	$is_manage_price = true;
	$is_manage_sales_price = true;
	if( wcfm_is_vendor() ) {
		$vendor_id = wcfm_get_vendor_id_by_post( $product_id );
		$is_manage_sku = $WCFM->wcfm_vendor_support->wcfm_vendor_has_capability( $vendor_id, 'manage_sku' );
		$is_manage_price = $WCFM->wcfm_vendor_support->wcfm_vendor_has_capability( $vendor_id, 'manage_price' );
		$is_manage_sales_price = $WCFM->wcfm_vendor_support->wcfm_vendor_has_capability( $vendor_id, 'manage_sales_price' );
	}
	
	$current_visibility = $product->get_catalog_visibility();
	$visibility_options = wc_get_product_visibility_options();
	
	$wcfm_is_translated_product = false;
	$wcfm_wpml_edit_disable_element = '';
	if ( $product_id && defined( 'ICL_SITEPRESS_VERSION' ) && ! ICL_PLUGIN_INACTIVE && class_exists( 'SitePress' ) ) {
		global $sitepress, $wpml_post_translations;
		$default_language = $sitepress->get_default_language();
		$current_language = $sitepress->get_current_language();
		
		$source_language  = $wpml_post_translations->get_source_lang_code( $product_id );
		
		//echo $source_language . "::" . $current_language . "::" . $default_language;
			
		if( $source_language && ( $source_language != $current_language ) ) {
			$wcfm_is_translated_product = true;
			$wcfm_wpml_edit_disable_element = 'wcfm_wpml_hide';
		}
	}
		
	if( $product ) {
	  ?>
	  <form id="wcfm_quick_edit_form" class="wcfm_popup_wrapper">
	    <div style="margin-bottom: 15px;"><h2 style="float: none;"><?php _e( 'Quick Update', 'wc-frontend-manager-ultimate' ); ?></h2></div>
	    
	    <table>
	      <tbody>
	        <tr>
	          <td>
	            <p class="wcfm_quick_edit_form_label wcfm_popup_label"><?php _e( 'Product', 'wc-frontend-manager-ultimate' ); ?></p>
	            <?php
	            if( $product_id && apply_filters( 'wcfm_is_pref_product_multivendor', true ) && apply_filters( 'wcfm_is_allow_product_multivendor_title_edit_disable', true ) ) {
								$sql     = "SELECT * FROM `{$wpdb->prefix}wcfm_marketplace_product_multivendor` WHERE ( ( `product_id` = $product_id ) OR ( `parent_product_id` = $product_id ) )";
								$results = $wpdb->get_row( $sql );
								if ( $results ) {
									?>
									<input type="text" class="wcfm_popup_input" name="wcfm_quick_edit_title" value="<?php echo $product->get_title(); ?>" readonly="readonly" />
									<p class="comment-notes" style="margin-left:39%; margin-top:2px!important;"><?php _e( 'Title edit disabeled, it has other sellers!', 'wc-multivendor-marketplace' ); ?></p>
									<?php
								} else {
									?>
									<input type="text" class="wcfm_popup_input" name="wcfm_quick_edit_title" value="<?php echo $product->get_title(); ?>" />
									<?php
								}
							} else {
								?>
								<input type="text" class="wcfm_popup_input" name="wcfm_quick_edit_title" value="<?php echo $product->get_title(); ?>" />
								<?php
							}
							?>
	          </td>
	        </tr>
	        <?php if( apply_filters( 'wcfm_is_allow_sku', true ) && $is_manage_sku ) { ?>
						<tr class="<?php echo $wcfm_wpml_edit_disable_element; ?>">
							<td>
							  <p class="wcfm_quick_edit_form_label wcfm_popup_label"><?php _e( 'SKU', 'wc-frontend-manager-ultimate' ); ?><?php if( $sku_required ) { ?><span class="required">*</span><?php } ?></p>
							  <input type="text" name="wcfm_quick_edit_sku" class="wcfm_popup_input" value="<?php echo get_post_meta($product_id, '_sku', true); ?>" <?php if( $sku_required ) { ?>data-required="1" data-required_message="<?php echo __( 'SKU', 'wc-frontend-manager-ultimate' ) . ': ' . __( 'This field is required.', 'wc-frontend-manager' ); ?>"<?php } ?> />
							</td>
						</tr>
					<?php } ?>
	        <?php if( in_array( $product->get_type(), array('simple', 'external') ) ) { ?>
	        	<?php if( $is_manage_price ) { ?>
							<tr class="<?php echo $wcfm_wpml_edit_disable_element; ?>">
								<td>
								  <p class="wcfm_quick_edit_form_label wcfm_popup_label"><?php _e( 'Regular Price', 'wc-frontend-manager-ultimate' ); ?><?php if( $price_required ) { ?><span class="required">*</span><?php } ?></p>
								  <input type="number" name="wcfm_quick_edit_regular_price" class="wcfm_popup_input" value="<?php echo get_post_meta($product_id, '_regular_price', true); ?>" <?php if( $price_required ) { ?>data-required="1" data-required_message="<?php echo __( 'Regular Price', 'wc-frontend-manager-ultimate' ) . ': ' . __( 'This field is required.', 'wc-frontend-manager' ); ?>"<?php } ?> />
								</td>
							</tr>
						<?php } ?>
						<?php if( $is_manage_sales_price ) { ?>
							<tr class="<?php echo $wcfm_wpml_edit_disable_element; ?>">
								<td>
								  <p class="wcfm_quick_edit_form_label wcfm_popup_label"><?php _e( 'Sale Price', 'wc-frontend-manager-ultimate' ); ?><?php if( $sales_price_required ) { ?><span class="required">*</span><?php } ?></p>
								  <input type="number" name="wcfm_quick_edit_sale_price" class="wcfm_popup_input" value="<?php echo get_post_meta($product_id, '_sale_price', true); ?>" <?php if( $sales_price_required ) { ?>data-required="1" data-required_message="<?php echo __( 'Sale Price', 'wc-frontend-manager-ultimate' ) . ': ' . __( 'This field is required.', 'wc-frontend-manager' ); ?>"<?php } ?> />
								</td>
							</tr>
						<?php } ?>
	        <?php } ?>
	        <?php if( $product->get_type() == 'simple' && $product->managing_stock() && apply_filters( 'wcfm_is_allow_inventory', true ) ) { ?>
						<tr class="<?php echo $wcfm_wpml_edit_disable_element; ?>">
							<td>
							  <p class="wcfm_quick_edit_form_label wcfm_popup_label"><?php _e( 'Stock', 'wc-frontend-manager-ultimate' ); ?><?php if( $stock_required ) { ?><span class="required">*</span><?php } ?></p>
							  <input type="number" name="wcfm_quick_edit_stock" class="wcfm_popup_input" value="<?php echo $product->get_total_stock(); ?>" <?php if( $stock_required ) { ?>data-required="1" data-required_message="<?php echo __( 'Stock', 'wc-frontend-manager-ultimate' ) . ': ' . __( 'This field is required.', 'wc-frontend-manager' ); ?>"<?php } ?> />
							</td>
						</tr>
					<?php } ?>
					<?php if( apply_filters( 'wcfm_is_allow_products_manage_visibility', true ) ) { ?>
						<tr class="<?php echo $wcfm_wpml_edit_disable_element; ?>">
							<td>
								<p class="wcfm_quick_edit_form_label wcfm_popup_label"><?php _e( 'Catalog visibility:', 'woocommerce' ); ?></p>
								<?php
								$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'product_simple_fields_visibility', array(
																																																			"catalog_visibility" => array('type' => 'select', 'options' => $visibility_options, 'class' => 'wcfm-select wcfm_ele wcfm_popup_input', 'value' => $current_visibility ),
																																												)) );
								?>
							</td>
						</tr>
					<?php } ?>
					
					<?php do_action( 'wcfm_product_quick_edit_end', $product_id ); ?>
					
	      </tbody>
	    </table>
	    <input type="hidden" name="wcfm_quick_edit_product_id" value="<?php echo $product_id; ?>" />
	    <div class="wcfm-message" tabindex="-1"></div>
	    <input type="button" class="wcfm_quick_edit_button wcfm_popup_button wcfm_submit_button" id="wcfm_quick_edit_button" value="<?php _e( 'Update', 'wc-frontend-manager-ultimate' ); ?>" />
	    
	    <div class="wcfm_clearfix"></div>
	  </form>
	  <?php
	}
}
?>
