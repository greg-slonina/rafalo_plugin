<?php
/**
 * WCFM plugin view
 *
 * WCFM WC Variation Swatches Product Manage View
 *
 * @author 		WC Lovers
 * @package 	wcfmu/views/thirdparty
 * @version   6.2.9
 */
 
global $wp, $WCFM, $WCFMu, $post, $woocommerce, $wp_roles;

if( !apply_filters( 'wcfm_is_allow_wc_variaton_swatch', true ) ) {
	return;
}

$product_id = 0;

if( isset( $wp->query_vars['wcfm-products-manage'] ) && !empty( $wp->query_vars['wcfm-products-manage'] ) ) {
	$product_id = absint($wp->query_vars['wcfm-products-manage']);
}

if ( !isset( $wp_roles ) ) {
	$wp_roles = new WP_Roles();
}

$roles = $wp_roles->get_names();

if ( $product_id ) {
	$adq_inherit_visibility_quote = get_post_meta( (int)$product_id, '_adq_inherit_visibility_quote', true );
	$adq_inherit_visibility_price = get_post_meta( (int)$product_id, '_adq_inherit_visibility_price', true );
	$adq_inherit_visibility_cart = get_post_meta( (int)$product_id, '_adq_inherit_visibility_cart', true );
	$adq_inherit_allow_product_comments = get_post_meta( (int)$product_id, '_adq_inherit_allow_product_comments', true );

	$adq_visibility_quote = get_post_meta( (int)$product_id, 'adq_visibility_quote', true );
	$adq_visibility_price = get_post_meta( (int)$product_id, 'adq_visibility_price', true );
	$adq_visibility_cart = get_post_meta( (int)$product_id, 'adq_visibility_cart', true );
	$adq_allow_product_comments = get_post_meta( (int)$product_id, 'adq_allow_product_comments', true );
} else {

	$adq_inherit_visibility_quote = "yes";

	$adq_inherit_visibility_price = "yes";

	$adq_inherit_visibility_cart = "yes";

	$adq_inherit_allow_product_comments = "yes";

	$adq_visibility_quote = get_option( 'adq_visibility_quote' );

	$adq_visibility_price = get_option( 'adq_visibility_price' );

	$adq_visibility_cart = get_option( 'adq_visibility_cart' );

	$adq_allow_product_comments = get_option( 'adq_allow_product_comments' );
}

if ( $adq_inherit_visibility_quote == "" ) {
	$adq_inherit_visibility_quote = "yes";
}

if ( $adq_inherit_visibility_price == "" ) {
	$adq_inherit_visibility_price = "yes";
}

if ( $adq_inherit_visibility_cart == "" ) {
	$adq_inherit_visibility_cart = "yes";
}

if ( $adq_inherit_allow_product_comments == "" ) {
	$adq_inherit_allow_product_comments = "yes";
}

if ( $adq_visibility_quote == "" ) {
	$adq_visibility_quote = get_option( 'adq_visibility_quote' );
}

if ( $adq_visibility_price == "" ) {
	$adq_visibility_price = get_option( 'adq_visibility_price' );
}

if ( $adq_visibility_cart == "" ) {
	$adq_visibility_cart = get_option( 'adq_visibility_cart' );
}

if ( $adq_allow_product_comments == "" ) {
	$adq_allow_product_comments = get_option( 'adq_allow_product_comments' );
}

$adq_enable_button = get_post_meta( (int)$product_id, 'adq_enable_button', true );
$adq_enable_button = $adq_enable_button ? $adq_enable_button : 'global';

$adq_enable_payment = get_post_meta( (int)$product_id, 'adq_enable_payment', true );
$adq_enable_payment = $adq_enable_payment ? $adq_enable_payment : 'global';

?>
<div class="page_collapsible products_manage_wc_quotation simple variable" id="wcfm_products_manage_form_wc_quotation_head"><label class="wcfmfa fa-question-circle"></label><?php _e('Quotation', 'wc-frontend-manager'); ?><span></span></div>
<div class="wcfm-container simple variable">
	<div id="wcfm_products_manage_form_wc_quotation_expander" class="wcfm-content">
		<h2><?php _e('Quotation Setting', 'wc-frontend-manager'); ?></h2>
		<div class="wcfm_clearfix"></div><br/>
		
		<div id="adq_quotation_options_data" class="adq_option_products panel woocommerce_options_panel">
				<div class="form-field form-required">
						<p class="wcfm_title checkbox_title"><strong><?php _e( 'Visibility add to quote button:', 'woocommerce-quotation' ) ?></strong></p>
						<input type="checkbox" class="wcfm-checkbox" value="yes" <?php checked( $adq_inherit_visibility_quote, "yes" ) ?> id="_adq_inherit_visibility_quote" name="_adq_inherit_visibility_quote">
						<p class="description wcfm_page_options_desc"><?php _e( 'Use global settings', 'woocommerce-quotation' ) ?></p>
						<div>
								<p class="wcfm_title checkbox_title"><strong>&nbsp;</strong></p>
								<select multiple="multiple" class="multiselect chosen_select wcfm-select" name="adq_visibility_quote[]" id="adq_visibility_quote">
										<?php foreach ( $roles as $key => $role ) { ?>
												<option value="<?php echo $key ?>" <?php $this->select_array( $key, $adq_visibility_quote ); ?>><?php echo $role ?></option>
										<?php } ?>
								</select>
								<p class="description wcfm_page_options_desc">
										<?php _e( 'Choose the the roles can view...', 'woocommerce-quotation' ); ?>
								</p>
						</div>                
				</div>
				<div class="form-field form-required">
						<p class="wcfm_title checkbox_title"><strong><?php _e( 'Visibility price on shop:', 'woocommerce-quotation' ) ?></strong></p>
						<input type="checkbox" class="wcfm-checkbox" value="yes" <?php checked( $adq_inherit_visibility_price, "yes" ) ?> id="_adq_inherit_visibility_price" name="_adq_inherit_visibility_price">
						<p class="description wcfm_page_options_desc"><?php _e( 'Use global settings', 'woocommerce-quotation' ) ?></p>
						<div>
								<p class="wcfm_title checkbox_title"><strong>&nbsp;</strong></p>
								<select multiple="multiple" class="multiselect chosen_select wcfm-select" name="adq_visibility_price[]" id="adq_visibility_price">
										<?php foreach ( $roles as $key => $role ) { ?>
												<option value="<?php echo $key ?>" <?php $this->select_array( $key, $adq_visibility_price ); ?>><?php echo $role ?></option>
										<?php } ?>
								</select>
								<p class="description wcfm_page_options_desc">
										<?php _e( 'Choose the the roles can view...', 'woocommerce-quotation' ); ?>
								</p>
						</div>
				</div>
				<div class="form-field form-required">
						<p class="wcfm_title checkbox_title"><strong><?php _e( 'Visibility add to cart button:', 'woocommerce-quotation' ) ?></strong></p>
						<input type="checkbox" class="wcfm-checkbox" value="yes" <?php checked( $adq_inherit_visibility_cart, "yes" ) ?> id="_adq_inherit_visibility_cart" name="_adq_inherit_visibility_cart">
						<p class="description wcfm_page_options_desc"><?php _e( 'Use global settings', 'woocommerce-quotation' ) ?></p>
						<div>
								<p class="wcfm_title checkbox_title"><strong>&nbsp;</strong></p>
								<select multiple="multiple" class="multiselect chosen_select wcfm-select" name="adq_visibility_cart[]" id="adq_visibility_cart">
										<?php foreach ( $roles as $key => $role ) { ?>
												<option value="<?php echo $key ?>" <?php $this->select_array( $key, $adq_visibility_cart ); ?>><?php echo $role ?></option>
										<?php } ?>
								</select>
								<p class="description wcfm_page_options_desc">
										<?php _e( 'Choose the the roles can view...', 'woocommerce-quotation' ); ?>
								</p>
						</div>
				</div>
				<div class="form-field form-required">
						<p class="wcfm_title checkbox_title"><strong><?php _e( 'Allow comments:', 'woocommerce-quotation' ) ?></strong></p>
						<input type="checkbox" class="wcfm-checkbox" value="yes" <?php checked( $adq_inherit_allow_product_comments, "yes" ) ?> id="_adq_inherit_allow_product_comments" name="_adq_inherit_allow_product_comments">
						<p class="description wcfm_page_options_desc"><?php _e( 'Use global settings', 'woocommerce-quotation' ) ?></p>
						<div>
								<p class="wcfm_title checkbox_title"><strong>&nbsp;</strong></p>
								<input type="checkbox" class="wcfm-checkbox" value="yes" <?php checked( $adq_allow_product_comments, "yes" ) ?> id="adq_allow_product_comments" name="adq_allow_product_comments">
								<p class="description wcfm_page_options_desc">
										<?php _e( 'Allow comments on products in quote list', 'woocommerce-quotation' ) ?>
								</p>
						</div>
				</div>
				<div class="form-field form-required">
						<p class="wcfm_title checkbox_title"><strong><?php _e( 'Force show \'Add to quote\' button when this product is out of stock', 'woocommerce-quotation' ) ?></strong></p>
								<?php $option_value = get_post_meta( (int)$product_id, 'adq_product_force_button', true );
								if ( $option_value == '' || !is_array($option_value) ) {
										$option_value = array(
												'active' => true,
												'roles' => array()
										);
								}
								?>
										<input type="checkbox"
													 value="yes"
													 id = "adq_product_force_button_check"
													 name="adq_product_force_button_check"
													 class="wcfm-checkbox"
													 <?php echo $option_value['active'] ? 'checked' : '' ?>
										>
								<p class="wcfm_title checkbox_title"><strong>&nbsp;</strong></p>
								<select
										class="wc-enhanced-select wcfm-select"
										name="adq_product_force_button[]"
										id="adq_product_force_button"
										multiple="multiple"
								>
										<?php

										foreach ( $roles as $key => $val ) {
												?>
												<option value="<?php echo esc_attr( $key ); ?>"
														<?php

														if ( is_array( $option_value['roles'] ) ) {
																selected( in_array( (string) $key, $option_value['roles'], true ), true );
														} else {
																selected( $option_value['roles'], (string) $key );
														}

														?>
												>
														<?php echo esc_html( $val ); ?></option>
												<?php
										}
										?>
								</select>
								<p class="description wcfm_page_options_desc"><?php _e( 'Choose the roles can view...', 'woocommerce-quotation' ) // WPCS: XSS ok. ?></p>
				</div>
				<div class="form-field form-required">
						<p class="wcfm_title"><strong><?php _e( 'Enable payment', 'woocommerce-quotation' ) ?></strong></p>
						<select class="chosen_select wcfm-select" name="adq_enable_payment" id="adq_enable_payment">
								<option value="global" <?php echo $adq_enable_payment == 'global' ? 'selected=\'selected\'' : '' ?>><?php _e( 'Use global settings', 'woocommerce-quotation' ) ?></option>
								<option value="yes" <?php echo $adq_enable_payment == 'yes' ? 'selected=\'selected\'' : '' ?>><?php _e( 'Yes', 'woocommerce-quotation' ) ?></option>
								<option value="no" <?php echo $adq_enable_payment == 'no' ? 'selected=\'selected\'' : '' ?>><?php _e( 'No', 'woocommerce-quotation' ) ?></option>
						</select>
						<p class="description wcfm_page_options_desc">
								<?php _e( 'You can enable the payment when this product is out of stock', 'woocommerce-quotation' ) ?>
						</p>
				</div>
				<div class="form-field form-required">
						<p class="wcfm_title checkbox_title"><strong><?php _e( 'Enable button \'Add to quote\' even if the product is out of stock.', 'woocommerce-quotation' ) ?></strong></p>
						<select class="chosen_select wcfm-select" name="adq_enable_button" id="adq_enable_button">
								<option value="global" <?php echo $adq_enable_button == 'global' ? 'selected=\'selected\'' : '' ?>><?php _e( 'Use global settings', 'woocommerce-quotation' ) ?></option>
								<option value="yes" <?php echo $adq_enable_button == 'yes' ? 'selected=\'selected\'' : '' ?>><?php _e( 'Yes', 'woocommerce-quotation' ) ?></option>
								<option value="no" <?php echo $adq_enable_button == 'no' ? 'selected=\'selected\'' : '' ?>><?php _e( 'No', 'woocommerce-quotation' ) ?></option>
						</select>
				</div>
		</div>
	</div>
</div>