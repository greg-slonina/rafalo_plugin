<?php
/**
 * WCFM plugin view
 *
 * WCFM WC Fancy Product Manage View
 *
 * @author 		WC Lovers
 * @package 	wcfmu/views/fancy_products
 * @version   5.4.6
 */
 
global $wp, $WCFM, $WCFMu, $post, $woocommerce;

if( !apply_filters( 'wcfm_is_pref_fancy_product_designer' , true ) || !apply_filters( 'wcfm_is_allow_fancy_product_designer', true ) ) {
	return;
}

$product_id = 0;
$custom_fields = array();

if( isset( $wp->query_vars['wcfm-products-manage'] ) && !empty( $wp->query_vars['wcfm-products-manage'] ) ) {
	$product_id = $wp->query_vars['wcfm-products-manage'];
	
	if( $product_id ) {
		$custom_fields = get_post_custom( $product_id );
	}
}

//DESKTOP
$source_type = isset( $custom_fields["fpd_source_type"] ) ? $custom_fields["fpd_source_type"][0] : "category";
$current_ind_settings = isset( $custom_fields["fpd_product_settings"] ) ? $custom_fields["fpd_product_settings"][0] : "";

$selected_categories = isset( $custom_fields["fpd_product_categories"] ) ? $custom_fields["fpd_product_categories"][0] : "";
if( is_serialized($selected_categories) )
	$selected_categories = unserialize($selected_categories); //V2.0, saved as array in db
else
	$selected_categories = empty($selected_categories)? array() : explode(',', $selected_categories); //V3.0 saved as string in db

$selected_products = isset( $custom_fields["fpd_products"] ) ? $custom_fields["fpd_products"][0] : "";
if( is_serialized($selected_products) )
	$selected_products = unserialize($selected_products); //V2.0, saved as array in db
else
	$selected_products = empty($selected_products)? array() : explode(',', $selected_products); //V3.0 saved as string in db

//MOBILE
$source_type_mobile = isset( $custom_fields["fpd_source_type_mobile"] ) ? $custom_fields["fpd_source_type_mobile"][0] : "category";

$selected_categories_mobile = isset( $custom_fields["fpd_product_categories_mobile"] ) ? $custom_fields["fpd_product_categories_mobile"][0] : "";

$selected_categories_mobile = empty($selected_categories_mobile)? array() : explode(',', $selected_categories_mobile);

$selected_products_mobile = isset( $custom_fields["fpd_products_mobile"] ) ? $custom_fields["fpd_products_mobile"][0] : "";
$selected_products_mobile = empty($selected_products_mobile)? array() : explode(',', $selected_products_mobile);

?>
<div class="page_collapsible products_manage_wc_fancy_product simple variable external grouped booking" id="wcfm_products_manage_form_wc_fancy_products_head"><label class="wcfmfa fa-object-group"></label><?php _e('Fancy Product', 'wc-frontend-manager-ultimate'); ?><span></span></div>
<div class="wcfm-container simple variable external grouped booking">
	<div id="wcfm_products_manage_form_wc_fancy_products_expander" class="wcfm-content">
	  <h2><?php _e('Fancy Product Designer', 'wc-frontend-manager-ultimate'); ?></h2>
	  <div class="wcfm-clearfix"></div>
	  
	  <div class="radykal-tabs">
			<div class="ui pointing secondary menu">
				<a class="active item" data-tab="desktop"><?php _e('Desktop', 'radykal'); ?></a>
				<a class="item" data-tab="mobile"><?php _e('Mobile', 'radykal'); ?></a>
			</div>
		
		
			<div class="ui active tab" data-tab="desktop">
	
				<div>
					<span class="wcfm_title"><strong><?php _e( 'Source Type', 'radykal' ); ?></strong></span>
					<span style="padding-right: 20px;">
						<input type="radio" name="fpd_source_type" value="category" <?php checked($source_type, 'category') ?> />
						<?php _e( 'Category', 'radykal' ); ?>
					</span>
					<span>
						<input type="radio" name="fpd_source_type" value="product" <?php checked($source_type, 'product') ?> />
						<?php _e( 'Product', 'radykal' ); ?>
					</span>
				</div>
				<div>
					<div class="fpd-categories">
						<span class="wcfm_title"><strong><?php _e( 'Product Categories', 'radykal' ); ?></strong></span>
						<select multiple="multiple" data-placeholder="<?php _e( 'Add categories to selection.', 'radykal' ); ?>" class="radykal-select-sortable" style="width: 100%;" data-selected="<?php echo implode(',', $selected_categories); ?>" name="fpd_product_categories">
						<?php
	
							$categories = FPD_Category::get_categories( array(
								'order_by' => 'title ASC'
							) );
	
							foreach($categories as $category) {
								$cat_title = '#'.$category->ID . ' - ' . $category->title;
								echo '<option value="'.$category->ID.'" data-title="'.$cat_title.'">'.$cat_title.'</option>';
							}
	
						?>
						</select>
						<p class="description"><?php _e( 'Sort items by drag & drop.', 'radykal' ); ?></p>
					</div>
					<div class="fpd-products">
						<span class="wcfm_title"><strong><?php _e( 'Products', 'radykal' ); ?></strong></span>
						<select data-placeholder="<?php _e( 'Add products to selection.', 'radykal' ); ?>" class="radykal-select-sortable" style="width: 100%;" name="fpd_products" data-selected="<?php echo implode(',', $selected_products); ?>">
							<?php
							
							 $vendor_id   = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );
	
							 if( wcfm_is_vendor() ) {
									$products = FPD_Product::get_products( array(
										'order_by' 	=> "ID ASC",
										'where'     => "user_id={$vendor_id }"
									) );
							 } else {
								 $products = FPD_Product::get_products( array(
									'order_by' 	=> "ID ASC",
								 ) );
							 }
	
							 if( !empty( $products ) ) {
									foreach($products as $fpd_product) {
										$product_title = '#'.$fpd_product->ID . ' - ' . $fpd_product->title;
										echo '<option value="'.$fpd_product->ID.'" data-title="'.$product_title.'">'.$product_title.'</option>';
									}
							 }
	
							?>
						</select>
						<p class="description"><?php _e( 'Sort items by drag & drop.', 'radykal' ); ?></p>
					</div>
				</div>
	
			</div><!-- Tab: Desktop -->

			<div class="ui tab" data-tab="mobile">
	
				<div>
					<span class="wcfm_title"><strong><?php _e( 'Source Type', 'radykal' ); ?></strong></span>
					<span style="padding-right: 20px;">
						<input type="radio" name="fpd_source_type_mobile" value="category" <?php checked($source_type_mobile, 'category') ?> />
						<?php _e( 'Category', 'radykal' ); ?>
					</span>
					<span>
						<input type="radio" name="fpd_source_type_mobile" value="product" <?php checked($source_type_mobile, 'product') ?> />
						<?php _e( 'Product', 'radykal' ); ?>
					</span>
				</div>
				<div>
					<div class="fpd-categories">
						<span class="wcfm_title"><strong><?php _e( 'Product Categories', 'radykal' ); ?></strong></span>
						<select multiple="multiple" data-placeholder="<?php _e( 'Add categories to selection.', 'radykal' ); ?>" class="radykal-select-sortable" style="width: 100%;" data-selected="<?php echo implode(',', $selected_categories_mobile); ?>" name="fpd_product_categories_mobile">
						<?php
	
							$categories = FPD_Category::get_categories( array(
								'order_by' => 'title ASC'
							) );
	
							foreach($categories as $category) {
								$cat_title = '#'.$category->ID . ' - ' . $category->title;
								echo '<option value="'.$category->ID.'" data-title="'.$cat_title.'">'.$cat_title.'</option>';
							}
	
						?>
						</select>
						<p class="description"><?php _e( 'Sort items by drag & drop.', 'radykal' ); ?></p>
					</div>
					<div class="fpd-products">
						<span class="wcfm_title"><strong><?php _e( 'Products', 'radykal' ); ?></strong></span>
						<select data-placeholder="<?php _e( 'Add products to selection.', 'radykal' ); ?>" class="radykal-select-sortable" style="width: 100%;" name="fpd_products_mobile" data-selected="<?php echo implode(',', $selected_products_mobile); ?>">
							<?php
	
							if( wcfm_is_vendor() ) {
									$products = FPD_Product::get_products( array(
										'order_by' 	=> "ID ASC",
										'where'     => "user_id={$vendor_id }"
									) );
							 } else {
								 $products = FPD_Product::get_products( array(
									'order_by' 	=> "ID ASC",
								 ) );
							 }
	
							 if( !empty( $products ) ) {
									foreach($products as $fpd_product) {
										$product_title = '#'.$fpd_product->ID . ' - ' . $fpd_product->title;
										echo '<option value="'.$fpd_product->ID.'" data-title="'.$product_title.'">'.$product_title.'</option>';
									}
							 }
	
							?>
						</select>
						<p class="description"><?php _e( 'Sort items by drag & drop.', 'radykal' ); ?></p>
					</div>
				</div>
	
			</div>
		
		</div>
		
		<div>
			<input type="hidden" name="fpd_product_settings" class="widefat" value="<?php echo $current_ind_settings; ?>" />
			<a class="wcfm_submit_button" href="#" id="fpd-change-settings"><?php _e( 'Individual Product Settings', 'radykal' ); ?></a>
		</div>
		
		
		<script type="text/javascript">
		
			jQuery(document).ready(function($) {
					
					$('#wcfm_products_manage_form_wc_fancy_products_expander .menu > .item').tab();
		
				//FANCY PRODUCT CHECKBOX
				$('#_fancy_product').change(function() {
					if($(this).is(':checked')) {
						$('.hide_if_fancy_product').show();
					}
					else {
						$('.hide_if_fancy_product').hide();
					}
				}).change();
		
				//source type
				$('[name="fpd_source_type"], [name="fpd_source_type_mobile"]').change(function() {
		
					var $tabContent = $(this).parents('.radykal-tab-content:first');
		
					if($tabContent.find('input[type="radio"]:checked').val() === 'category') {
						$tabContent.find('.fpd-categories').show();
						$tabContent.find('.fpd-products').hide();
					}
					else {
						$tabContent.find('.fpd-categories').hide();
						$tabContent.find('.fpd-products').show();
					}
		
				}).change();
		
			});
		
		</script>
		
		
		<?php
		require_once(FPD_PLUGIN_ADMIN_DIR.'/views/modal-individual-product-settings.php');
		?>
	  
	</div>
</div>