<?php
global $wp, $WCFM, $WCFMu;

$coupon_id = 0;
$minimum_amount = '';
$maximum_amount = '';
$individual_use = '';
$exclude_sale_items = '';
$product_ids = array();
$exclude_product_ids = array();
$include_product_categories = array();
$exclude_product_categories = array();
$customer_email = '';

$usage_limit = '';
$limit_usage_to_x_items = '';
$usage_limit_per_user = '';

$wcfm_vendor_coupon_all_product = '';

if( isset( $wp->query_vars['wcfm-coupons-manage'] ) && !empty( $wp->query_vars['wcfm-coupons-manage'] ) ) {
	$coupon_post = get_post( $wp->query_vars['wcfm-coupons-manage'] );
	// Fetching Coupon Data
	if($coupon_post && !empty($coupon_post)) {
		$coupon_id = $wp->query_vars['wcfm-coupons-manage'];
		$wc_coupon = new WC_Coupon( $coupon_id );
		
		$minimum_amount = get_post_meta( $coupon_id, 'minimum_amount', true);
		$maximum_amount = get_post_meta( $coupon_id, 'maximum_amount', true);
		$individual_use = ( get_post_meta( $coupon_id, 'individual_use', true) == 'yes' ) ? 'enable' : '';
		$exclude_sale_items = ( get_post_meta( $coupon_id, 'exclude_sale_items', true) == 'yes' ) ? 'enable' : '';
		$product_ids = $wc_coupon->get_product_ids();
		$exclude_product_ids = $wc_coupon->get_excluded_product_ids();
		$include_product_categories = $wc_coupon->get_product_categories();
		$exclude_product_categories = $wc_coupon->get_excluded_product_categories();
		$customer_email = implode( ', ', (array) get_post_meta( $coupon_id, 'customer_email', true ) );
		
		$usage_limit = $wc_coupon->get_usage_limit() ? $wc_coupon->get_usage_limit() : '';
		$limit_usage_to_x_items = $wc_coupon->get_limit_usage_to_x_items() ? $wc_coupon->get_limit_usage_to_x_items() : '';
		$usage_limit_per_user = $wc_coupon->get_usage_limit_per_user() ? $wc_coupon->get_usage_limit_per_user() : '';
		
		$wcfm_vendor_coupon_all_product = get_post_meta( $coupon_id, '_wcfm_vendor_coupon_all_product', true );
	}
}

$products_array = $excude_products_array = array();
if( wcfm_is_vendor() && $wcfm_vendor_coupon_all_product ) {
	/*$product_ids = array( 0 => -1 );
	$products_objs = $WCFM->wcfm_vendor_support->wcfm_get_products_by_vendor( apply_filters( 'wcfm_current_vendor_id', get_current_user_id() ), 'publish' );
	$products_array = array();
	if( !empty($products_objs) ) {
		foreach( $products_objs as $products_obj ) {
			$products_array[esc_attr( $products_obj->ID )] = esc_html( $products_obj->post_title );
			$product_ids[] = esc_attr( $products_obj->ID );
		}
	}*/
} else {                              
	if( !empty( $product_ids ) ) {
		foreach( $product_ids as $include_product_id ) {
			if ( get_post_status ( $include_product_id ) ) {
				$products_array[$include_product_id] = get_post( absint($include_product_id) )->post_title;	
			}
		}
	}
}

if( !empty( $exclude_product_ids ) ) {
	foreach( $exclude_product_ids as $exclude_product_id ) {
		if ( get_post_status ( $exclude_product_id ) ) {
			$excude_products_array[$exclude_product_id] = get_post( absint($exclude_product_id) )->post_title;	
		}
	}
}

$product_categories   = get_terms( 'product_cat', 'orderby=name&hide_empty=0&parent=0' );

$product_ids_class = '';
//if( wcfm_is_vendor() )
	//$product_ids_class = 'wcfm_ele_hide wcfm_ele_for_vendor';
?>

<!-- wrap -->
<div class="wcfm-tabWrap">

	<!-- collapsible -->
	<div class="page_collapsible" id="coupons_manage_restriction"><label class="wcfmfa fa-user"></label><?php _e('Restriction', 'wc-frontend-manager-ultimate'); ?></div>
	<div class="wcfm-container">
		<div id="coupons_manage_restriction_expander" class="wcfm-content">
			<p>
				<?php
					$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'coupon_manager_fields_restriction', array( 	"minimum_amount" => array('label' => __('Minimum spend', 'wc-frontend-manager-ultimate'), 'type' => 'text', 'placeholder' => __( 'No Minimum', 'wc-frontend-manager-ultimate'), 'class' => 'wcfm-text wcfm_ele wcfm_non_negative_input', 'label_class' => 'wcfm_ele wcfm_title', 'value' => $minimum_amount),
																																													"maximum_amount" => array('label' => __('Maximum spend', 'wc-frontend-manager-ultimate'), 'type' => 'text', 'placeholder' => __( 'No Maximum', 'wc-frontend-manager-ultimate'), 'class' => 'wcfm-text wcfm_ele wcfm_non_negative_input', 'label_class' => 'wcfm_ele wcfm_title', 'value' => $maximum_amount),
																																													"individual_use" => array('label' => __('Individual use only', 'wc-frontend-manager-ultimate') , 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'enable', 'label_class' => 'wcfm_title checkbox_title', 'hints' => __('Check this box if the coupon cannot be used in conjunction with other coupons.', 'wc-frontend-manager-ultimate'), 'dfvalue' => $individual_use),
																																													"exclude_sale_items" => array('label' => __('Exclude sale items', 'wc-frontend-manager-ultimate') , 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'enable', 'label_class' => 'wcfm_title checkbox_title', 'hints' => __('Check this box if the coupon should not apply to items on sale. Per-item coupons will only work if the item is not on sale. Per-cart coupons will only work if there are no sale items in the cart.', 'wc-frontend-manager-ultimate'), 'dfvalue' => $exclude_sale_items),
																																													"product_ids" => array('label' => __('Products', 'wc-frontend-manager-ultimate') , 'type' => 'select', 'attributes' => array( 'multiple' => 'multiple', 'style' => 'width: 60%;' ), 'class' => 'wcfm-select wcfm_ele ' . $product_ids_class, 'label_class' => 'wcfm_title ' . $product_ids_class, 'options' => $products_array, 'value' => $product_ids, 'hints' => __( 'Products which need to be in the cart to use this coupon or, for `Product Discounts`, which products are discounted.', 'wc-frontend-manager-ultimate' ) ),
																																													"exclude_product_ids" => array('label' => __('Exclude products', 'wc-frontend-manager-ultimate') , 'type' => 'select', 'attributes' => array( 'multiple' => 'multiple', 'style' => 'width: 60%;' ), 'class' => 'wcfm-select wcfm_ele', 'label_class' => 'wcfm_title', 'options' => $excude_products_array, 'value' => $exclude_product_ids, 'hints' => __( 'Products which must not be in the cart to use this coupon or, for `Product Discounts`, which products are not discounted.', 'wc-frontend-manager-ultimate' ) )
																																								), $coupon_id ) );
				?>
				
				<div class="wcfm_clearfix"></div>
				<p class="product_categories wcfm_title"><strong><?php _e('Product categories', 'wc-frontend-manager-ultimate'); ?></strong><span class="img_tip wcfmfa fa-question" data-tip="<?php _e('A product must be in this category for the coupon to remain valid or, for `Product Discounts`, products in these categories will be discounted.', 'wc-frontend-manager-ultimate'); ?>"></span></p>
				<label class="screen-reader-text" for="product_categories"><?php _e('Product categories', 'wc-frontend-manager-ultimate'); ?></label>
				<select id="product_categories" name="product_categories[]" class="wcfm-select wcfm_ele" multiple="multiple" style="width: 60%;">
					<?php
						$category_ids = (array) $include_product_categories;
		
						if ( $product_categories ) {
							foreach ( $product_categories as $cat ) {
								echo '<option value="' . esc_attr( $cat->term_id ) . '"' . selected( in_array( $cat->term_id, $category_ids ), true, false ) . '>' . esc_html( $cat->name ) . '</option>';
								$product_child_categories   = get_terms( 'product_cat', 'orderby=name&hide_empty=0&parent=' . absint( $cat->term_id ) );
								if ( $product_child_categories ) {
									foreach ( $product_child_categories as $child_cat ) {
										echo '<option value="' . esc_attr( $child_cat->term_id ) . '"' . selected( in_array( $child_cat->term_id, $category_ids ), true, false ) . '>' . '&nbsp;&nbsp;' . esc_html( $child_cat->name ) . '</option>';
									}
								}
							}
						}
					?>
				</select>
				
				<div class="wcfm_clearfix"></div>
				<p class="exclude_product_categories wcfm_title"><strong><?php _e('Exclude categories', 'wc-frontend-manager-ultimate'); ?></strong><span class="img_tip wcfmfa fa-question" data-tip="<?php _e('Product must not be in this category for the coupon to remain valid or, for `Product Discounts`, products in these categories will not be discounted.', 'wc-frontend-manager-ultimate'); ?>"></span></p>
				<label class="screen-reader-text" for="exclude_product_categories"><?php _e('Exclude categories', 'wc-frontend-manager-ultimate'); ?></label>                                                                                                                                                                                   
				<select id="exclude_product_categories" name="exclude_product_categories[]" class="wcfm-select wcfm_ele" multiple="multiple" style="width: 60%;">
					<?php
						$category_ids = (array) $exclude_product_categories;
		
						if ( $product_categories ) {
							foreach ( $product_categories as $cat ) {
								echo '<option value="' . esc_attr( $cat->term_id ) . '"' . selected( in_array( $cat->term_id, $category_ids ), true, false ) . '>' . esc_html( $cat->name ) . '</option>';
								$product_child_categories   = get_terms( 'product_cat', 'orderby=name&hide_empty=0&parent=' . absint( $cat->term_id ) );
								if ( $product_child_categories ) {
									foreach ( $product_child_categories as $child_cat ) {
										echo '<option value="' . esc_attr( $child_cat->term_id ) . '"' . selected( in_array( $child_cat->term_id, $category_ids ), true, false ) . '>' . '&nbsp;&nbsp;' . esc_html( $child_cat->name ) . '</option>';
									}
								}
							}
						}
					?>
				</select>
				
				<?php
					$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'coupon_manager_fields_email', array( "customer_email" => array('label' => __('Email restrictions', 'wc-frontend-manager-ultimate'), 'type' => 'text', 'placeholder' => __( 'No restrictions', 'wc-frontend-manager-ultimate'), 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_ele wcfm_title', 'hints' => __('List of allowed emails to check against the customer\'s billing email when an order is placed. Separate email addresses with commas.', 'wc-frontend-manager-ultimate'), 'value' => $customer_email)
																															), $coupon_id ) );
				?>
			</p>
		</div>
	</div>
	<div class="wcfm_clearfix"></div>
	<!-- end collapsible -->
	 
	<!-- collapsible -->
	<div class="page_collapsible" id="coupons_manage_limit"><label class="wcfmfa fa-user-times"></label><?php _e('Limit', 'wc-frontend-manager-ultimate'); ?></div>
		<div class="wcfm-container">
			<div id="coupons_manage_limit_expander" class="wcfm-content">
				<p>
					<?php
						$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'coupon_manager_fields_limits', array(  "usage_limit" => array('label' => __('Usage limit per coupon', 'wc-frontend-manager-ultimate'), 'type' => 'number', 'placeholder' => __( 'Unlimited usage', 'wc-frontend-manager-ultimate'), 'class' => 'wcfm-text wcfm-text-limit wcfm_non_negative_input wcfm_ele', 'label_class' => 'wcfm_ele wcfm_title limit_title', 'attributes' => array('min' => 0, 'steps' => 1), 'hints' => __('How many times this coupon can be used before it is void.', 'wc-frontend-manager-ultimate'), 'value' => $usage_limit),
																																														"limit_usage_to_x_items" => array('label' => __('Limit usage to X items', 'wc-frontend-manager-ultimate'), 'type' => 'number', 'placeholder' => __( 'Apply to all qualifying items in cart', 'wc-frontend-manager-ultimate'), 'class' => 'wcfm-text wcfm-text-limit wcfm_non_negative_input wcfm_ele', 'label_class' => 'wcfm_ele wcfm_title limit_title', 'attributes' => array('min' => 0, 'steps' => 1), 'hints' => __('The maximum number of individual items this coupon can apply to when using product discounts. Leave blank to apply to all qualifying items in cart.', 'wc-frontend-manager-ultimate'), 'value' => $limit_usage_to_x_items),
																																														"usage_limit_per_user" => array('label' => __('Usage limit per user', 'wc-frontend-manager-ultimate'), 'type' => 'number', 'placeholder' => __( 'Unlimited usage', 'wc-frontend-manager-ultimate'), 'class' => 'wcfm-text wcfm-text-limit wcfm_non_negative_input wcfm_ele', 'label_class' => 'wcfm_ele wcfm_title limit_title', 'attributes' => array('min' => 0, 'steps' => 1), 'hints' => __('How many times this coupon can be used by an invidual user. Uses billing email for guests, and user ID for logged in users.', 'wc-frontend-manager-ultimate'), 'value' => $usage_limit_per_user)
																																									), $coupon_id ) );
					?>
				</p>
		</div>
	</div>
	<div class="wcfm_clearfix"></div>
	<!-- end collapsible -->
</div> <!-- tabwrap -->