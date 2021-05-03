<?php
/**
 * WCFM plugin view
 *
 * WCFM WC Advanced Product Fields Product Manage View
 *
 * @author 		WC Lovers
 * @package 	wcfmu/views/thirdparty
 * @version   6.0.0
 */
 
global $wp, $WCFM, $WCFMu, $post, $woocommerce;

if( !apply_filters( 'wcfm_is_allow_wc_advanced_product_labels', true ) ) {
	return;
}

$product_id = 0;

$_wapl_label_type   = '';
$_wapl_label_text   = '';
$_wapl_label_style  = '';
$_wapl_label_align  = '';

$label_data = array();
if ( empty( $label_data['custom_bg_color'] ) ) {
	$label_data['custom_bg_color'] = '#D9534F';
}
if ( empty( $label_data['custom_text_color'] ) ) {
	$label_data['custom_text_color'] = '#fff';
}
$label_data['style_attr'] = ! empty( $label_data['style'] ) && 'custom' == $label_data['style'] ? "style='background-color: {$label_data['custom_bg_color']}; color: {$label_data['custom_text_color']};'" : '';


$default_warranty = true;
$warranty_label = __('Warranty', 'wc_warranty');
$control_type = 'parent';

if( isset( $wp->query_vars['wcfm-products-manage'] ) && !empty( $wp->query_vars['wcfm-products-manage'] ) ) {
	$product_id = $wp->query_vars['wcfm-products-manage'];
	
	if( $product_id ) {
		$_wapl_label_type  = get_post_meta( $product_id, '_wapl_label_type', true );
		$_wapl_label_text  = get_post_meta( $product_id, '_wapl_label_text', true );
		$_wapl_label_style = get_post_meta( $product_id, '_wapl_label_style', true );
		$_wapl_label_align = get_post_meta( $product_id, '_wapl_label_align', true );
		
		$label_data = array(
			'id'                => $product_id,
			'exclude'           => get_post_meta( $product_id, '_wapl_label_exclude', true ),
			'type'              => $_wapl_label_type,
			'text'              => $_wapl_label_text,
			'style'             => $_wapl_label_style,
			'align'             => $_wapl_label_align,
			'custom_bg_color'   => get_post_meta( $product_id, '_wapl_custom_bg_color', true ),
			'custom_text_color' => get_post_meta( $product_id, '_wapl_custom_text_color', true ),
		);
		
		if ( empty( $label_data['custom_bg_color'] ) ) {
			$label_data['custom_bg_color'] = '#D9534F';
		}
		if ( empty( $label_data['custom_text_color'] ) ) {
			$label_data['custom_text_color'] = '#fff';
		}
		$label_data['style_attr'] = ! empty( $label_data['style'] ) && 'custom' == $label_data['style'] ? "style='background-color: {$label_data['custom_bg_color']}; color: {$label_data['custom_text_color']};'" : '';
	}
}
?>

<div class="page_collapsible products_manage_wc_advanced_product_labels simple variable external grouped booking" id="wcfm_products_manage_form_wc_advanced_product_labels_head"><label class="wcfmfa fa-tags"></label><?php _e('Product Labels', 'wc-frontend-manager-ultimate'); ?><span></span></div>
<div class="wcfm-container simple variable external grouped booking">
	<div id="wcfm_products_manage_form_wc_advanced_product_labels_expander" class="wcfm-content">
	  <div id='woocommerce_advanced_product_labels' class='panel woocommerce_options_panel'>
			<h2><?php _e('Product Labels', 'wc-frontend-manager-ultimate'); ?></h2>
			<div class="wcfm-clearfix"></div>
			
			<div class='wapl-column'>
				<?php
				$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_product_manage_wc_advanced_product_labels_fields', array( 
							"_wapl_label_type" => array( 'label' => __( 'Label type', 'woocommerce-advanced-product-labels' ) , 'type' => 'select', 'class' => 'wcfm-select wcfm_ele simple variable external grouped booking', 'label_class' => 'wcfm_title simple variable external grouped booking', 'options' => wapl_get_label_types(), 'value' => $_wapl_label_type, 'hints' => __( '<strong>\'Flash\'</strong> is positioned on top of the product image<br/><strong>\'Label\'</strong> is positioned above the product title', 'woocommerce-advanced-product-labels' ) ),
							"_wapl_label_text" => array( 'label' => __( 'Label text', 'woocommerce-advanced-product-labels' ) , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele simple variable external grouped booking', 'label_class' => 'wcfm_title simple variable external grouped booking', 'value' => $_wapl_label_text, 'hints' => __( 'What text do you want the label to show?', 'woocommerce-advanced-product-labels' ) ),
							"_wapl_label_style" => array( 'label' => __( 'Label style', 'woocommerce-advanced-product-labels' ) , 'type' => 'select', 'class' => 'wcfm-select wcfm_ele simple variable external grouped booking', 'label_class' => 'wcfm_title simple variable external grouped booking', 'options' => wapl_get_label_styles(), 'value' => $_wapl_label_style ),
							"_wapl_label_align" => array( 'label' => __( 'Label align', 'woocommerce-advanced-product-labels' ) , 'type' => 'select', 'class' => 'wcfm-select wcfm_ele simple variable external grouped booking', 'label_class' => 'wcfm_title simple variable external grouped booking', 'options' => array( 'none'   => __( 'None', 'woocommerce-advanced-product-labels' ), 'left'   => __( 'Left', 'woocommerce-advanced-product-labels' ), 'right'  => __( 'Right', 'woocommerce-advanced-product-labels' ), 'center' => __( 'Center', 'woocommerce-advanced-product-labels' ) ), 'value' => $_wapl_label_align ),
				), $product_id ) );
				
				$label_custom_bg_color   = isset( $label_data['custom_bg_color'] ) ? $label_data['custom_bg_color'] : '#D9534F';
				$label_custom_text_color = isset( $label_data['custom_text_color'] ) ? $label_data['custom_text_color'] : '#fff';
		
				?><p class='form-field _wapl_label_custom_bg_color_field wapl-custom-colors custom-colors <?php echo isset( $label_data['style'] ) && $label_data['style'] == 'custom' ? '' : 'hidden'; ?>'>
					<span class="wcfm_title" for='wapl-custom-background'><strong><?php _e( 'Background color', 'woocommerce-advanced-product-labels' ); ?></strong></span>
					<input type='text' name='_wapl_custom_bg_color' value='<?php echo $label_custom_bg_color; ?>' id='wapl-custom-background' class='color-picker wcfm-text' />
		
					<span class="wcfm_title" for='wapl-custom-text'><strong><?php _e( 'Text color', 'woocommerce-advanced-product-labels' ); ?></strong></span>
					<input type='text' name='_wapl_custom_text_color' value='<?php echo $label_custom_text_color; ?>' id='wapl-custom-text' class='color-picker wcfm-text' />
				</p>
				<div class="wcfm-clearfix"></div>
			</div>
			
			<div class='wapl-column' style='width: 100%; margin-top: 50px; padding-left: 39%;position:relative'>
				<div id='wapl-label-preview'>
					<img width='150' height='150' title='' alt='' src='<?php echo apply_filters( 'wapl_preview_image_src', 'data:image/gif;base64,R0lGODdhlgCWAOMAAMzMzJaWlr6+vpycnLGxsaOjo8XFxbe3t6qqqgAAAAAAAAAAAAAAAAAAAAAAAAAAACwAAAAAlgCWAAAE/hDISau9OOvNu/9gKI5kaZ5oqq5s675wLM90bd94ru987//AoHBILBqPyKRyyWw6n9CodEqtWq/YrHbL7Xq/4LB4TC6bz+i0es1uu9/wuHxOr9vv+Lx+z+/7/4CBgoOEhYaHiImKi4yNjo+QkZKTlJWWl5iZmpucnZ6foHcCAwMTAaenBxMCBQEFBiajpRKoqautr2cEp7MApwjAAhIGA64BvSK7x6YBwAjCAMTGyGK7rb3LFbsEAAgBqsnTptQA293fZQaq2b7krbACzSPq7eMW7wDxCGjsxwTPE4oNc2XhlIB4ATT0G/APGgCB0Qie6VcL2kIL3oDJy0ARlUVsz+TEsEPw6sDGi/dIFdgwsuRJkPxCZkNZAaFDDOwozIQ5MSREiAYkVggaAJZCnwkfJg26sucEcEol4NN3QRm3o08DJp260Uw2k9yYSjDnDarOAgVC6pwFNmJTsujKoD3VtFjauNKuXWh1wGSBffdaSbRbDFzenGNqLb12VcIoV0YrnKI1uWCtYYwpPM4VqrPnz6BDix5NurTp06hTq17NurXr17Bjy55Nu7bt27hz697Nu7fv38CDCx9OvLjx48iTK1/OvLnz59CjS59OvfqLCAA7' ); ?>' /><?php
					echo wapl_get_label_html( $label_data );
					?><p><strong>Product name</strong></p>
					<style>
					.wapl-label-id-<?php echo $product_id; ?>, .wapl-label-id-undefined{width:150px!important;}
					.hidden{display:none;}
					</style>
				</div>
				<div class="wcfm-clearfix"></div>
			</div>
	  </div>
	</div>
</div>