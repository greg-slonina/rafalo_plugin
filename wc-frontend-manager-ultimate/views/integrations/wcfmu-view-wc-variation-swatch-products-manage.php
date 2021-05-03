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
 
global $wp, $WCFM, $WCFMu, $post, $woocommerce;

if( !apply_filters( 'wcfm_is_allow_wc_variaton_swatch', true ) ) {
	return;
}

$product_id = 0;

if( isset( $wp->query_vars['wcfm-products-manage'] ) && !empty( $wp->query_vars['wcfm-products-manage'] ) ) {
	$product_id = absint($wp->query_vars['wcfm-products-manage']);
}

?>
<div class="page_collapsible products_manage_wc_variaton_swatch variable" id="wcfm_products_manage_form_wc_variaton_swatch_head"><label class="wcfmfa fa-swatchbook"></label><?php _e('Swatches Setting', 'wc-frontend-manager'); ?><span></span></div>
<div class="wcfm-container variable">
	<div id="wcfm_products_manage_form_wc_variaton_swatch_expander" class="wcfm-content">
		<h2><?php _e('Swatches Setting', 'wc-frontend-manager'); ?></h2>
		<div class="wcfm_clearfix"></div><br/>
		<div id="wvs-pro-product-variable-swatches-options" class="panel wc-metaboxes-wrapper hidden">
			<?php
			
			if( $product_id) {
			
				$product_object = wc_get_product( $product_id );
				
				// global $post, $thepostid, $product_object;
				// Product attributes - taxonomies and custom, ordered, with visibility and variation attributes set
				$attributes = $product_object->get_attributes();
				//$variation_attributes   = array_filter( $product_object->get_attributes(), array( __CLASS__, 'filter_variation_attributes' ) );
				
				// $saved_product_attributes = (array) get_post_meta( $product_id, '_wvs_product_attributes', true );
				$saved_product_attributes = (array) wvs_pro_get_product_option( $product_id );
				
				//print_r( $saved_product_attributes); die;
				
				$wvs_pro_attributes           = array();
				$attribute_types              = wc_get_attribute_types();
				$attribute_types[ 'custom' ]  = esc_html__( 'Custom', 'woo-variation-swatches-pro' );
				$attribute_types_configurable = wc_get_attribute_types();
				unset( $attribute_types_configurable[ 'select' ], $attribute_types_configurable[ 'radio' ] );
				
				
				?>
				<div class="wvs-pro-product-variable-swatches-options wc-metaboxes">
					<div class="product-settings">
						<?php if ( woo_variation_swatches()->get_option( 'enable_catalog_mode' ) ): ?>
							<table cellpadding="0" cellspacing="0">
								<tbody>
									<tr>
										<td><?php esc_html_e( 'Catalog mode attribute', 'woo-variation-swatches-pro' ) ?></td>
										<td>
											<select class="wcfm-select" name="_wvs_pro_swatch_option[catalog_attribute]">
												<?php foreach ( wvs_pro_get_attribute_taxonomies_option( esc_html__( 'Global', 'woo-variation-swatches-pro' ) ) as $key => $label ):
												$selected_catalog_attribute = isset( $saved_product_attributes[ 'catalog_attribute' ] ) ? trim( $saved_product_attributes[ 'catalog_attribute' ] ) : '';
												?>
												<option <?php selected( $selected_catalog_attribute, $key ) ?> value="<?php echo esc_attr( $key ) ?>"><?php echo esc_html( $label ) ?></option>
												<?php
												endforeach; ?>
											</select>
										</td>
									</tr>
								</tbody>
							</table>
						<?php endif; ?>
					
						<?php if ( woo_variation_swatches()->get_option( 'enable_single_variation_preview' ) ): ?>
							<table cellpadding="0" cellspacing="0">
								<tbody>
									<tr>
										<td><?php esc_html_e( 'Single variation preview attribute', 'woo-variation-swatches-pro' ) ?></td>
										<td>
											<select class="wcfm-select" name="_wvs_pro_swatch_option[single_variation_preview_attribute]">
												<?php foreach ( wvs_pro_get_attribute_taxonomies_option( esc_html__( 'Global', 'woo-variation-swatches-pro' ) ) as $key => $label ):
												$selected_catalog_attribute = isset( $saved_product_attributes[ 'single_variation_preview_attribute' ] ) ? trim( $saved_product_attributes[ 'single_variation_preview_attribute' ] ) : '';
												?>
												<option <?php selected( $selected_catalog_attribute, $key ) ?> value="<?php echo esc_attr( $key ) ?>"><?php echo esc_html( $label ) ?></option>
												<?php
												endforeach; ?>
											</select>
										</td>
									</tr>
								</tbody>
							</table>
						<?php endif; ?>
					</div>
				
					<?php
					
					foreach ( $attributes as $attribute ) {
						
						// Class WC_Product_Attribute
						$use_for_variation = $attribute->get_variation();
						$attribute_name    = $attribute->get_name();
						$options           = $attribute->get_options();
						
						if ( ! $use_for_variation ) {
							continue;
						}
						
						if ( $attribute->is_taxonomy() && $attribute_taxonomy = $attribute->get_taxonomy_object() ) {
							
							$options = ! empty( $options ) ? $options : array();
							
							$wvs_pro_attributes[ $attribute_name ][ 'taxonomy_exists' ] = true;
							$wvs_pro_attributes[ $attribute_name ][ 'taxonomy' ]        = (array) $attribute_taxonomy;
							$wvs_pro_attributes[ $attribute_name ][ 'terms' ]           = array();
							
							$terms = array();
							
							$args = array(
								'orderby'    => 'name',
								'hide_empty' => 0,
							);
							
							$all_terms = get_terms( $attribute->get_taxonomy(), apply_filters( 'woocommerce_product_attribute_terms', $args ) );
							if ( $all_terms ) {
								foreach ( $all_terms as $term ) {
									if ( in_array( $term->term_id, $options, true ) ) {
										$terms[ $term->term_id ] = esc_attr( apply_filters( 'woocommerce_product_attribute_term_name', $term->name, $term ) );
									}
								}
								$wvs_pro_attributes[ $attribute_name ][ 'terms' ] = $terms;
							}
						} else {
							// TextAria custom attribute which added by Red | Blur | Green
							$attribute_name = $attribute->get_name();
							$attribute_name = strtolower( sanitize_title( $attribute_name ) );
							$options        = $attribute->get_options();
							$options        = ! empty( $options ) ? $options : array();
							$terms          = array_reduce( $options, function ( $opt, $option ) {
								$opt[ $option ] = $option;
								
								return $opt;
							}, array() );
							
							$wvs_pro_attributes[ $attribute_name ][ 'taxonomy_exists' ] = false;
							$wvs_pro_attributes[ $attribute_name ][ 'taxonomy' ]        = array(
								'attribute_id'    => strtolower( sanitize_title( $attribute_name ) ),
								'attribute_type'  => 'select',
								'attribute_name'  => strtolower( sanitize_title( $attribute_name ) ),
								'attribute_label' => $attribute->get_name()
							);
							$wvs_pro_attributes[ $attribute_name ][ 'terms' ]           = $terms;
						}
					}
					
					if ( ! empty( $wvs_pro_attributes ) ) {
						include( $WCFMu->plugin_path . 'views/integrations/html/wcfmu-html-wc-variation-swatch-attributes.php' );
					} else {
						?>
						<div class="inline notice woocommerce-message">
							<p><?php echo wp_kses_post( __( 'Before you can add a variation you need to add some variation attributes on the <strong>Attributes</strong> tab.', 'woocommerce' ) ); ?></p>
							<p><a class="button-primary" href="<?php echo esc_url( apply_filters( 'woocommerce_docs_url', 'https://docs.woocommerce.com/document/variable-product/', 'product-variations' ) ); ?>" target="_blank"><?php esc_html_e( 'Learn more', 'woocommerce' ); ?></a></p>
						</div>
						<?php
					}
				?>
				</div>
				<?php
				wp_localize_script( 'wcfmu_wc_variaton_swatch_products_manage_js', 'wvs_pro_product_variation_data', apply_filters( 'wvs_pro_product_variation_data', array(
						'attribute_types' => wc_get_attribute_types(),
						'post_id'         => $product_id ? $product_id : '',
						'ajax_url'        => admin_url( 'admin-ajax.php' ),
						'nonce'           => wp_create_nonce(),
						'reset_notice'    => esc_html__( 'Are you sure you want to reset it to default setting?', 'woo-variation-swatches-pro' )
					) ) );
				} else {
					echo "<p class='description instructions'>" . __( 'Please save the product first then set swatches setting!', 'wc-frontend-manager-ultimate' ) . "</p>";
				}
			?>
		</div>
	</div>
</div>