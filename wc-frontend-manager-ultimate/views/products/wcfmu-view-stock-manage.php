<?php
global $WCFM, $wp_query;

$wcfm_is_allow_manage_products = apply_filters( 'wcfm_is_allow_manage_products', true );
if( !apply_filters( 'wcfm_is_pref_bulk_stock_manager', true ) || !$wcfm_is_allow_manage_products || !apply_filters( 'wcfm_is_allow_inventory', true ) || !apply_filters( 'wcfm_is_allow_stock_manager', true ) ) {
	wcfm_restriction_message_show( "Stock Manager" );
	return;
}

$wcfmu_products_menus = apply_filters( 'wcfmu_products_menus', array( 'any' => __( 'All', 'wc-frontend-manager'), 
																																			'publish' => __( 'Published', 'wc-frontend-manager'),
																																			'draft' => __( 'Draft', 'wc-frontend-manager'),
																																			'pending' => __( 'Pending', 'wc-frontend-manager')
																																		) );

$product_status = ! empty( $_GET['product_status'] ) ? sanitize_text_field( $_GET['product_status'] ) : 'any';

$current_user_id = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );
if( current_user_can( 'administrator' ) ) $current_user_id = 0;
$count_products = array();
$count_products['publish'] = wcfm_get_user_posts_count( $current_user_id, 'product', 'publish' );
$count_products['pending'] = wcfm_get_user_posts_count( $current_user_id, 'product', 'pending' );
$count_products['draft']   = wcfm_get_user_posts_count( $current_user_id, 'product', 'draft' );
$count_products['any']     = $count_products['publish'] + $count_products['pending'] + $count_products['draft'];

?>

<div class="collapse wcfm-collapse" id="wcfm_products_stock_manage_listing">
	
	<div class="wcfm-page-headig">
		<span class="wcfmfa fa-cube"></span>
		<span class="wcfm-page-heading-text"><?php _e( 'Stock Manager', 'wc-frontend-manager' ); ?></span>
		<?php do_action( 'wcfm_page_heading' ); ?>
	</div>
	<div class="wcfm-collapse-content">
		<div id="wcfm_page_load"></div>
		
		<div class="wcfm-container wcfm-top-element-container">
			<ul class="wcfm_products_stock_manage_menus">
				<?php
				$is_first = true;
				foreach( $wcfmu_products_menus as $wcfmu_products_menu_key => $wcfmu_products_menu) {
					?>
					<li class="wcfm_products_stock_manage_menu_item">
						<?php
						if($is_first) $is_first = false;
						else echo " | ";
						?>
						<a class="<?php echo ( $wcfmu_products_menu_key == $product_status ) ? 'active' : ''; ?>" href="<?php echo get_wcfm_stock_manage_url( $wcfmu_products_menu_key ); ?>"><?php echo $wcfmu_products_menu . ' ('. $count_products[$wcfmu_products_menu_key] .')'; ?></a>
					</li>
					<?php
				}
				?>
			</ul>
			
			<?php
			if( $is_allow_products_export = apply_filters( 'wcfm_is_allow_products_export', true ) ) {
				?>
				<a class="add_new_wcfm_ele_dashboard text_tip" href="<?php echo get_wcfm_export_product_url(); ?>" data-screen="product" data-tip="<?php _e( 'Products Export', 'wc-frontend-manager' ); ?>"><span class="wcfmfa fa-download"></span></a>
				<?php
			}
			
			if( $is_allow_products_import = apply_filters( 'wcfm_is_allow_products_import', true ) ) {
				if( !WCFM_Dependencies::wcfmu_plugin_active_check() ) {
					if( $is_wcfmu_inactive_notice_show = apply_filters( 'is_wcfmu_inactive_notice_show', true ) ) {
						?>
						<a class="add_new_wcfm_ele_dashboard text_tip" href="#" onclick="return false;" data-tip="<?php wcfmu_feature_help_text_show( 'Products Import', false, true ); ?>"><span class="wcfmfa fa-upload"></span></a>
						<?php
					}
				} else {
					?>
					<a class="wcfm_import_export text_tip" href="<?php echo get_wcfm_import_product_url(); ?>" data-tip="<?php _e( 'Products Import', 'wc-frontend-manager' ); ?>"><span class="wcfmfa fa-upload"></span></a>
					<?php
				}
			}
			
			if( $has_new = apply_filters( 'wcfm_add_new_product_sub_menu', true ) ) {
				echo '<a id="add_new_product_dashboard" class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_edit_product_url().'" data-tip="' . __('Add New Product', 'wc-frontend-manager') . '"><span class="wcfmfa fa-cube"></span><span class="text">' . __( 'Add New', 'wc-frontend-manager') . '</span></a>';
			}
			?>
			<div class="wcfm-clearfix"></div>
		</div>
	  <div class="wcfm-clearfix"></div><br />
	  
		<?php do_action( 'before_wcfm_products_stock_manage' ); ?>
		
		<div class="wcfm_products_stock_manage_filter_wrap wcfm_filters_wrap">
			<?php	
			// Category Filtering
			if( apply_filters( 'wcfm_is_products_taxonomy_filter', true, 'product_cat' ) && apply_filters( 'wcfm_is_products_category_filter', true ) ) {
				$product_categories   = get_terms( 'product_cat', 'orderby=name&hide_empty=0&parent=0' );
				$categories = array();
				
				$WCFM->wcfm_fields->wcfm_generate_form_field( array(
																											"dropdown_product_cat" => array( 'type' => 'select', 'options' => $categories, 'custom_attributes' => array( 'taxonomy' => 'product_cat', 'parent' => '' ), 'attributes' => array( 'style' => 'width: 150px;' ) )
																											 ) );
				
			}
			
			// Type filtering
			if( $wcfm_is_products_type_filter = apply_filters( 'wcfm_is_products_type_filter', true ) ) {
				$product_types = apply_filters( 'wcfm_product_types', array('simple' => __('Simple Product', 'wc-frontend-manager'), 'variable' => __('Variable Product', 'wc-frontend-manager'), 'grouped' => __('Grouped Product', 'wc-frontend-manager'), 'external' => __('External/Affiliate Product', 'wc-frontend-manager') ) );
				$output  = '<select name="product_type" id="dropdown_product_type" style="width: 160px;">';
				$output .= '<option value="">' . __( 'Show all product types', 'wc-frontend-manager' ) . '</option>';
				
				foreach ( $product_types as $product_type_name => $product_type_label ) {
					$output .= '<option value="' . $product_type_name . '">' . $product_type_label . '</option>';
				
					if ( 'simple' == $product_type_name ) {
						
						$product_type_options = apply_filters( 'wcfm_non_allowd_product_type_options', array( 'virtual' => 'virtual', 'downloadable' => 'downloadable' ) ); 
						
						if( !empty( $product_type_options['downloadable'] ) ) {
							$output .= '<option value="downloadable" > &rarr; ' . __( 'Downloadable', 'wc-frontend-manager' ) . '</option>';
						}
						
						if( !empty( $product_type_options['virtual'] ) ) {
							$output .= '<option value="virtual" > &rarr;  ' . __( 'Virtual', 'wc-frontend-manager' ) . '</option>';
						}
					}
				}
				
				$output .= '</select>';
				
				echo apply_filters( 'woocommerce_product_filters', $output );
			}
			
			if( $wcfm_is_products_vendor_filter = apply_filters( 'wcfm_is_products_vendor_filter', true ) ) {
				$is_marketplace = wcfm_is_marketplace();
				if( $is_marketplace ) {
					if( !wcfm_is_vendor() ) {
						$vendor_arr = array(); //$WCFM->wcfm_vendor_support->wcfm_get_vendor_list();
						$WCFM->wcfm_fields->wcfm_generate_form_field( array(
																											"dropdown_vendor" => array( 'type' => 'select', 'options' => $vendor_arr, 'attributes' => array( 'style' => 'width: 150px;' ) )
																											 ) );
					}
				}
			}
			?>
		</div>

		<form id="wcfm_stock_manage_form">
		  <div class="wcfm-container">
			  <div id="wcfm_products_stock_manage_listing_expander" class="wcfm-content">
					<table id="wcfm-stock-manage" class="display" cellspacing="0" width="100%">
						<thead>
							<tr>
								<th>
									<input type="checkbox" class="wcfm-checkbox stock_manage_checkbox_all" name="stock_manage_checkbox_all_top" value="yes" data-tip="<?php _e( 'Select all for bulk edit', 'wc-frontend-manager' ); ?>" />
								</th>
								<th><?php _e( 'Name', 'wc-frontend-manager' ); ?></th>
								<th><?php _e( 'SKU', 'wc-frontend-manager' ); ?></th>
								<th><?php _e( 'Status', 'wc-frontend-manager' ); ?></th>
								<th><?php _e( 'Store', 'wc-frontend-manager' ); ?></th>
								<th><?php _e( 'Manage', 'wc-frontend-manager' ); ?></th>
								<th><?php _e( 'Status', 'wc-frontend-manager' ); ?></th>
								<th><?php _e( 'Backorders', 'wc-frontend-manager-ultimate' ); ?></th>
								<th><?php _e( 'Stock Quantity', 'wc-frontend-manager-ultimate' ); ?></th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th>
									<input type="checkbox" class="wcfm-checkbox stock_manage_checkbox_all" name="stock_manage_checkbox_all_top" value="yes" data-tip="<?php _e( 'Select all for bulk edit', 'wc-frontend-manager' ); ?>" />
								</th>
								<th><?php _e( 'Name', 'wc-frontend-manager' ); ?></th>
								<th><?php _e( 'SKU', 'wc-frontend-manager' ); ?></th>
								<th><?php _e( 'Status', 'wc-frontend-manager' ); ?></th>
								<th><?php _e( 'Store', 'wc-frontend-manager' ); ?></th>
								<th><?php _e( 'Manage', 'wc-frontend-manager' ); ?></th>
								<th><?php _e( 'Status', 'wc-frontend-manager' ); ?></th>
								<th><?php _e( 'Backorders', 'wc-frontend-manager-ultimate' ); ?></th>
								<th><?php _e( 'Stock Quantity', 'wc-frontend-manager-ultimate' ); ?></th>
							</tr>
						</tfoot>
					</table>
					<div class="wcfm-clearfix"></div>
				</div>
				<div class="wcfm-clearfix"></div>
		  </div>
	
			<div id="wcfm_stock_manager_submit" class="wcfm_form_simple_submit_wrapper">
				<div class="wcfm-message" tabindex="-1"></div>
				
				<input type="submit" name="submit-data" value="<?php _e( 'Submit', 'wc-frontend-manager-ultimate' ); ?>" id="wcfm_stock_manager_submit_button" class="wcfm_submit_button" />
			</div>
		</form>
		<div class="wcfm-clearfix"></div>
		<?php
		do_action( 'after_wcfm_products_stock_manage' );
		?>
	</div>
</div>