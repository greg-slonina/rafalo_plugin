<?php

//add_action( 'before_wcfm_products', 'wcfmu_products_filter_menu' );

function wcfmu_products_filter_menu() {
	global $WCFM, $WCFMu, $wp_query;
	
	$wcfmu_products_menus = apply_filters( 'wcfmu_products_menus', array( 'all' => __( 'All', 'wc-frontend-manager-ultimate'), 
																																				'publish' => __( 'Published', 'wc-frontend-manager-ultimate'),
																																				'draft' => __( 'Draft', 'wc-frontend-manager-ultimate'),
																																				'pending' => __( 'Pending', 'wc-frontend-manager-ultimate')
																																			) );
	
	$product_status = ! empty( $_GET['product_status'] ) ? sanitize_text_field( $_GET['product_status'] ) : 'all';
	$count_products = wp_count_posts( 'product' );
	$count_products->all = $count_products->publish + $count_products->pending + $count_products->draft;
	
	if( apply_filters( 'wcfm_is_allow_archive_product', true ) ) {
		$wcfmu_products_menus['archive'] = __( 'Archive', 'wc-frontend-manager-ultimate');
		$count_products->all += $count_products->archive;
	}
	?>
	<ul class="wcfm_products_menus">
		<?php
		$is_first = true;
		foreach( $wcfmu_products_menus as $wcfmu_products_menu_key => $wcfmu_products_menu) {
			?>
			<li class="wcfm_products_menu_item">
				<?php
				if($is_first) $is_first = false;
				else echo "&nbsp;|&nbsp;";
				?>
				<a class="<?php echo ( $wcfmu_products_menu_key == $product_status ) ? 'active' : ''; ?>" href="<?php echo get_wcfm_products_url( $wcfmu_products_menu_key ); ?>"><?php echo $wcfmu_products_menu . ' ('. $count_products->$wcfmu_products_menu_key .')'; ?></a>
			</li>
			<?php
		}
		?>
	</ul>
	
	<div class="wcfm_products_filter_wrap wcfm_filters_wrap">
	<?php	
	// Category Filtering
	$product_categories   = get_terms( 'product_cat', 'orderby=name&hide_empty=0&parent=0' );
	$categories = array();
	
	echo '<select id="dropdown_product_cat" name="dropdown_product_cat" class="dropdown_product_cat" style="width: 150px;">';
	  echo '<option value="" selected="selected">' . __( 'Select a category', 'wc-frontend-manager-ultimate' ) . '</option>';
		if ( $product_categories ) {
			$WCFM->library->generateTaxonomyHTML( 'product_cat', $product_categories, $categories );
		}
	echo '</select>';
	
	// Type filtering
	$product_types = apply_filters( 'wcfm_product_types', array('simple' => __('Simple Product', 'wc-frontend-manager'), 'variable' => __('Variable Product', 'wc-frontend-manager'), 'grouped' => __('Grouped Product', 'wc-frontend-manager'), 'external' => __('External/Affiliate Product', 'wc-frontend-manager') ) );
	$output  = '<select name="product_type" id="dropdown_product_type" style="width: 160px;">';
	$output .= '<option value="">' . __( 'Show all product types', 'wc-frontend-manager-ultimate' ) . '</option>';
	
	foreach ( $product_types as $product_type_name => $product_type_label ) {
		$output .= '<option value="' . $product_type_name . '">' . $product_type_label . '</option>';
	
		if ( 'simple' == $product_type_name ) {
			
			$product_type_options = apply_filters( 'wcfm_non_allowd_product_type_options', array( 'virtual' => 'virtual', 'downloadable' => 'downloadable' ) ); 
			
			if( !empty( $product_type_options['downloadable'] ) ) {
				$output .= '<option value="downloadable" > &rarr; ' . __( 'Downloadable', 'wc-frontend-manager-ultimate' ) . '</option>';
			}
			
			if( !empty( $product_type_options['virtual'] ) ) {
				$output .= '<option value="virtual" > &rarr;  ' . __( 'Virtual', 'wc-frontend-manager-ultimate' ) . '</option>';
			}
		}
	}
	
	$output .= '</select>';
	
	echo apply_filters( 'woocommerce_product_filters', $output );
	
	
	$is_marketplace = wcfm_is_marketplace();
	$user_arr = array( '' => __('All Vendors', 'wc-frontend-manager' ) );
	if( $is_marketplace ) {
		if( !wcfm_is_vendor() ) {
			if( $is_marketplace == 'wcpvendors' ) {
				$vendors = WC_Product_Vendors_Utils::get_vendors();
				if( !empty( $vendors ) ) {
					foreach ( $vendors as $vendor ) {
						$user_arr[$vendor->term_id] = esc_html( $vendor->name );
					}
				}
			} else {
				$args = array(
					'role__in'     => apply_filters( 'wcfm_allwoed_vendor_user_roles', array( 'dc_vendor', 'vendor', 'seller', 'wcfm_vendor', 'disable_vendor' ) ),
					'orderby'      => 'login',
					'order'        => 'ASC',
					'count_total'  => false,
					'fields'       => array( 'ID', 'display_name' )
				 ); 
				$all_users = get_users( $args );
				if( !empty( $all_users ) ) {
					foreach( $all_users as $all_user ) {
						$user_arr[$all_user->ID] = $all_user->display_name;
					}
				}
			}
	
			$WCFM->wcfm_fields->wcfm_generate_form_field( array(
																								"dropdown_vendor" => array( 'type' => 'select', 'options' => $user_arr, 'attributes' => array( 'style' => 'width: 150px;' ) )
																								 ) );
		}
	}
	
	echo '</div>';
}

?>