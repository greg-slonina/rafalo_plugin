<?php
/**
 * WCFMu plugin core
 *
 * Plugin Product manager custom validation Controler
 *
 * @author 		WC Lovers
 * @package 	wcfmu/core
 * @version   4.0.5
 */
 
class WCFMu_Custom_Validation {
	
	public $wcfm_custom_validation_options = array();
	
	public function __construct() {
		global $WCFM, $WCFMu;
		
		$this->wcfm_custom_validation_options = get_option( 'wcfm_custom_validation_options', array() );
		
		// Custom Validation Settings
		add_action( 'end_wcfm_settings', array( &$this, 'wcfmu_custom_validation_settings' ), 13 );
		add_action( 'wcfm_settings_update', array( &$this, 'wcfmu_custom_validation_settings_update' ), 13 );
		
		// Set Custom Validation Validation
		add_filter( 'wcfm_product_manage_fields_pricing',  array( &$this, 'wcfmu_set_custom_validation' ), 500 );
		add_filter( 'wcfm_product_manage_fields_content',  array( &$this, 'wcfmu_set_custom_validation' ), 500 );
		add_filter( 'wcfm_product_manage_fields_images',   array( &$this, 'wcfmu_set_custom_validation' ), 500 );
		add_filter( 'wcfm_product_simple_fields_tag',      array( &$this, 'wcfmu_set_custom_validation' ), 500 );
		add_filter( 'wcfm_product_fields_stock',           array( &$this, 'wcfmu_set_custom_validation' ), 500 );
		add_filter( 'wcfm_product_manage_fields_shipping', array( &$this, 'wcfmu_set_custom_validation' ), 500 );
		add_filter( 'wcfm_product_simple_fields_tax',      array( &$this, 'wcfmu_set_custom_validation' ), 500 );
		add_filter( 'wcfm_product_manage_fields_linked',   array( &$this, 'wcfmu_set_custom_validation' ), 500 );
		
		add_filter( 'wcfm_taxonomy_custom_label',          array( &$this, 'wcfmu_taxonomy_custom_label' ), 500, 2 );
		add_filter( 'wcfm_taxonomy_custom_attributes',     array( &$this, 'wcfmu_taxonomy_custom_validation' ), 500, 2 );
	}
	
	function wcfmu_set_custom_validation( $form_fields ) {
		if( !empty( $form_fields ) ) {
			foreach( $form_fields as $form_field_key => $form_field ) {
				if( isset( $this->wcfm_custom_validation_options[$form_field_key] ) ) {
					
					// Release Cross Sells and Upsell products for 
					if( in_array( $form_field_key, array( 'upsell_ids', 'crosssell_ids' ) ) ) {
						$current_user_id = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );
						if( !wcfm_is_vendor() ) $current_user_id = 0;
						$publish_products_count = wcfm_get_user_posts_count( $current_user_id, 'product', 'publish' );
						if( $publish_products_count < 3 ) continue;
					}
					
					if( !in_array( $form_field_key, array( 'gallery_img' ) ) ) {
						if( !isset( $form_fields[$form_field_key]['custom_attributes'] ) ) {
							$form_fields[$form_field_key]['custom_attributes'] = array( 'required' => true );
						} else {
							$form_fields[$form_field_key]['custom_attributes']['required'] = true;
						}
					}
					
					// Set Custom Options for validation
					switch( $form_field_key ) {
					  case 'shipping_class':
						  $shipping_class_options = $form_fields[$form_field_key]['options'];
						  $shipping_option_array = array( '' => __( 'Choose shipping class', 'wc-frontend-manager-ultimate' ) );
						  if( !empty( $shipping_class_options ) ) {
						  	foreach( $shipping_class_options as $shipping_class_option_key => $shipping_class_option ) {
						  		if( $shipping_class_option_key == '_no_shipping_class' ) continue;
						  		$shipping_option_array[$shipping_class_option_key] = $shipping_class_option;
						  	}
						  }
						  $form_fields[$form_field_key]['options'] = $shipping_option_array;
						break;
						
						case 'gallery_img':
							if( isset( $form_fields[$form_field_key]['options']['image'] ) ) {
								if( !isset( $form_fields[$form_field_key]['custom_attributes'] ) ) {
									$form_fields[$form_field_key]['options']['image']['custom_attributes'] = array( 'required' => true, 'required_message' => __( 'Gallery Images', 'wc-frontend-manager-ultimate' )  . ': ' . __( 'This field is required.', 'wc-frontend-manager' ) );
								} else {
									$form_fields[$form_field_key]['options']['image']['custom_attributes']['required'] = true;
									$form_fields[$form_field_key]['options']['image']['custom_attributes']['required_message'] = __( 'Gallery Images', 'wc-frontend-manager-ultimate' )  . ': ' . __( 'This field is required.', 'wc-frontend-manager' );
								}
							} elseif( isset( $form_fields[$form_field_key]['options']['gimage'] ) ) {
								if( !isset( $form_fields[$form_field_key]['custom_attributes'] ) ) {
									$form_fields[$form_field_key]['options']['gimage']['custom_attributes'] = array( 'required' => true, 'required_message' => __( 'Gallery Images', 'wc-frontend-manager-ultimate' )  . ': ' . __( 'This field is required.', 'wc-frontend-manager' ) );
								} else {
									$form_fields[$form_field_key]['options']['gimage']['custom_attributes']['required'] = true;
									$form_fields[$form_field_key]['options']['gimage']['custom_attributes']['required_message'] = __( 'Gallery Images', 'wc-frontend-manager-ultimate' )  . ': ' . __( 'This field is required.', 'wc-frontend-manager' );
								}
							}
						break;
					}
				}
			}
		}
		return $form_fields;
	}
	
	function wcfmu_taxonomy_custom_label( $tax_select_lavel, $taxonomy ) {
		if( isset( $this->wcfm_custom_validation_options[$taxonomy] ) ) {
			$tax_select_lavel .= '<span class="required">*</span>';
		}
		return $tax_select_lavel;
	}
	
	function wcfmu_taxonomy_custom_validation( $tax_custom_attributes, $taxonomy ) {
		if( isset( $this->wcfm_custom_validation_options[$taxonomy] ) ) {
			$tax_custom_attributes[] = 'data-required="1"';
			$tax_custom_attributes[] = 'data-required_message="' . __( str_replace( '_', ' ', ucfirst( $taxonomy ) ), 'wc-frontend-manager' ) . ': ' . __( 'This field is required.', 'wc-frontend-manager' ) . '"';
		}
		return $tax_custom_attributes;
	}
	
	function wcfmu_custom_validation_settings( $wcfm_options ) {
		global $WCFM, $WCFMu;
		//print_r($this->wcfm_custom_validation_options);
		?>
		<!-- collapsible -->
		<div class="page_collapsible" id="wcfm_settings_form_custom_validation_head">
			<label class="fab fa-gg-circle"></label>
			<?php _e('Product Custom Validation', 'wc-frontend-manager-ultimate'); ?><span></span>
		</div>
		<div class="wcfm-container">
			<div id="wcfm_settings_form_custom_validation_expander" class="wcfm-content">
			  <div class="module_head_message"><?php _e( 'Configure what to set required for product manager', 'wc-frontend-manager-ultimate' ); ?></div>
				
			  <div class="wcfm_clearfix"></div>
				<div class="wcfm_vendor_settings_heading"><h3><?php _e( 'Pricing', 'wc-frontend-manager-ultimate' ); ?></h3></div>
				<div class="wcfm_setting_indent_block">
					<?php
					$WCFM->wcfm_fields->wcfm_generate_form_field( array( "wcfmcr_regular_price" => array('label' => __('Price', 'wc-frontend-manager') , 'name' => 'wcfm_custom_validation_options[regular_price]','type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => isset( $this->wcfm_custom_validation_options['regular_price'] ) ? 'yes' : 'no' ),
																															 "wcfmcr_sale_price"    => array('label' => __('Sale Price', 'wc-frontend-manager') , 'name' => 'wcfm_custom_validation_options[sale_price]','type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => isset( $this->wcfm_custom_validation_options['sale_price'] ) ? 'yes' : 'no' ),
																										) );
					?>
				</div>
				
				<div class="wcfm_clearfix"></div>
				<div class="wcfm_vendor_settings_heading"><h3><?php _e( 'Content', 'wc-frontend-manager-ultimate' ); ?></h3></div>
				<div class="wcfm_setting_indent_block">
					<?php
					$WCFM->wcfm_fields->wcfm_generate_form_field( array( "wcfmcr_excerpt"      => array('label' => __('Excerpt', 'wc-frontend-manager-ultimate') , 'name' => 'wcfm_custom_validation_options[excerpt]','type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => isset( $this->wcfm_custom_validation_options['excerpt'] ) ? 'yes' : 'no' ),
																															 "wcfmcr_description"  => array('label' => __('Description', 'wc-frontend-manager') , 'name' => 'wcfm_custom_validation_options[description]','type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => isset( $this->wcfm_custom_validation_options['description'] ) ? 'yes' : 'no' ),
																										) );
					?>
				</div>
				
				<div class="wcfm_clearfix"></div>
				<div class="wcfm_vendor_settings_heading"><h3><?php _e( 'Sidebar', 'wc-frontend-manager-ultimate' ); ?></h3></div>
				<div class="wcfm_setting_indent_block">
					<?php
					$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_custom_validation_taxonomy_elements', 
																														array( "wcfmcr_featured_img"    => array('label' => __('Featured Image', 'wc-frontend-manager-ultimate') , 'name' => 'wcfm_custom_validation_options[featured_img]','type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => isset( $this->wcfm_custom_validation_options['featured_img'] ) ? 'yes' : 'no' ),
																																	 "wcfmcr_gallery_img"     => array('label' => __('Gallery Image', 'wc-frontend-manager-ultimate') , 'name' => 'wcfm_custom_validation_options[gallery_img]','type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => isset( $this->wcfm_custom_validation_options['gallery_img'] ) ? 'yes' : 'no' ),
																																	 "wcfmcr_product_cat"     => array('label' => __('Categories', 'wc-frontend-manager') , 'name' => 'wcfm_custom_validation_options[product_cat]','type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => isset( $this->wcfm_custom_validation_options['product_cat'] ) ? 'yes' : 'no' ),
																																	 "wcfmcr_product_tags"    => array('label' => __('Tags', 'wc-frontend-manager') , 'name' => 'wcfm_custom_validation_options[product_tags]','type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => isset( $this->wcfm_custom_validation_options['product_tags'] ) ? 'yes' : 'no' ),
																															   ) 
																														) );
					
					// Custom taxonomy validation
					$product_taxonomies = get_object_taxonomies( 'product', 'objects' );
					if( !empty( $product_taxonomies ) ) {
						foreach( $product_taxonomies as $product_taxonomy ) {
							if( !in_array( $product_taxonomy->name, array( 'product_cat', 'product_tag', 'wcpv_product_vendors' ) ) ) {
								if( $product_taxonomy->public && $product_taxonomy->show_ui && $product_taxonomy->meta_box_cb && $product_taxonomy->hierarchical ) {
									$WCFM->wcfm_fields->wcfm_generate_form_field( array(
																																		"wcfmcr_".$product_taxonomy->name     => array( 'label' => $product_taxonomy->label , 'name' => 'wcfm_custom_validation_options['.$product_taxonomy->name.']','type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => isset( $this->wcfm_custom_validation_options[$product_taxonomy->name] ) ? 'yes' : 'no' )
																																		) );
								}
							}
						}
					}
					?>
				</div>
				
				<div class="wcfm_clearfix"></div>
				<div class="wcfm_vendor_settings_heading"><h3><?php _e( 'Inventory', 'wc-frontend-manager-ultimate' ); ?></h3></div>
				<div class="wcfm_setting_indent_block">
					<?php
					$WCFM->wcfm_fields->wcfm_generate_form_field( array( "wcfmcr_sku"           => array('label' => __('SKU', 'wc-frontend-manager') , 'name' => 'wcfm_custom_validation_options[sku]','type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => isset( $this->wcfm_custom_validation_options['sku'] ) ? 'yes' : 'no' ),
																															 "wcfmcr_manage_stock"  => array('label' => __('Manage Stock', 'wc-frontend-manager') , 'name' => 'wcfm_custom_validation_options[manage_stock]','type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => isset( $this->wcfm_custom_validation_options['manage_stock'] ) ? 'yes' : 'no' ),
																															 "wcfmcr_stock_qty"     => array('label' => __('Stock Qty', 'wc-frontend-manager') , 'name' => 'wcfm_custom_validation_options[stock_qty]','type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => isset( $this->wcfm_custom_validation_options['stock_qty'] ) ? 'yes' : 'no' ),
																										) );
					?>
				</div>
				
				<div class="wcfm_clearfix"></div>
				<div class="wcfm_vendor_settings_heading"><h3><?php _e( 'Shipping', 'wc-frontend-manager-ultimate' ); ?></h3></div>
				<div class="wcfm_setting_indent_block">
					<?php
					$WCFM->wcfm_fields->wcfm_generate_form_field( array( "wcfmcr_weight"           => array('label' => __('Weight', 'wc-frontend-manager') , 'name' => 'wcfm_custom_validation_options[weight]','type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => isset( $this->wcfm_custom_validation_options['weight'] ) ? 'yes' : 'no' ),
																															 "wcfmcr_length"           => array('label' => __('Length', 'wc-frontend-manager') , 'name' => 'wcfm_custom_validation_options[length]','type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => isset( $this->wcfm_custom_validation_options['length'] ) ? 'yes' : 'no' ),
																															 "wcfmcr_width"            => array('label' => __('Width', 'wc-frontend-manager') , 'name' => 'wcfm_custom_validation_options[width]','type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => isset( $this->wcfm_custom_validation_options['width'] ) ? 'yes' : 'no' ),
																															 "wcfmcr_height"           => array('label' => __('Height', 'wc-frontend-manager') , 'name' => 'wcfm_custom_validation_options[height]','type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => isset( $this->wcfm_custom_validation_options['height'] ) ? 'yes' : 'no' ),
																															 "wcfmcr_shipping_class"   => array('label' => __('Shipping Class', 'wc-frontend-manager-ultimate') , 'name' => 'wcfm_custom_validation_options[shipping_class]','type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => isset( $this->wcfm_custom_validation_options['shipping_class'] ) ? 'yes' : 'no' ),
																										) );
					?>
				</div>
				
				<div class="wcfm_clearfix"></div>
				<div class="wcfm_vendor_settings_heading"><h3><?php _e( 'Tax', 'wc-frontend-manager-ultimate' ); ?></h3></div>
				<div class="wcfm_setting_indent_block">
					<?php
					$WCFM->wcfm_fields->wcfm_generate_form_field( array( "wcfmcr_tax_status" => array('label' => __('Tax Status', 'wc-frontend-manager') , 'name' => 'wcfm_custom_validation_options[tax_status]','type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => isset( $this->wcfm_custom_validation_options['tax_status'] ) ? 'yes' : 'no' ),
																															 //"wcfmcr_tax_class"  => array('label' => __('Tax Class', 'wc-frontend-manager') , 'name' => 'wcfm_custom_validation_options[tax_class]','type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => isset( $this->wcfm_custom_validation_options['tax_class'] ) ? 'yes' : 'no' ),
																										) );
					?>
				</div>
				
				<div class="wcfm_clearfix"></div>
				<div class="wcfm_vendor_settings_heading"><h3><?php _e( 'Linked', 'wc-frontend-manager-ultimate' ); ?></h3></div>
				<div class="wcfm_setting_indent_block">
					<?php
					$WCFM->wcfm_fields->wcfm_generate_form_field( array( "wcfmcr_upsell_ids"     => array('label' => __('Up-sells', 'wc-frontend-manager') , 'name' => 'wcfm_custom_validation_options[upsell_ids]','type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => isset( $this->wcfm_custom_validation_options['upsell_ids'] ) ? 'yes' : 'no' ),
																															 "wcfmcr_crosssell_ids"  => array('label' => __('Cross-sells', 'wc-frontend-manager') , 'name' => 'wcfm_custom_validation_options[crosssell_ids]','type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => isset( $this->wcfm_custom_validation_options['crosssell_ids'] ) ? 'yes' : 'no' ),
																										) );
					?>
				</div>
				
			</div>
		</div>
		<div class="wcfm_clearfix"></div>
		<!-- end collapsible -->
		
		<?php
		
	}
	
	function wcfmu_custom_validation_settings_update( $wcfm_settings_form ) {
		global $WCFM, $WCFMu, $_POST;
		
		if( isset( $wcfm_settings_form['wcfm_custom_validation_options'] ) ) {
			$wcfm_custom_validation_options = $wcfm_settings_form['wcfm_custom_validation_options'];
			$this->wcfm_custom_validation_options = $wcfm_custom_validation_options;
			update_option( 'wcfm_custom_validation_options',  $wcfm_custom_validation_options );
		} else {
			$this->wcfm_custom_validation_options = array();
			update_option( 'wcfm_custom_validation_options',  array() );
		}
	}
}