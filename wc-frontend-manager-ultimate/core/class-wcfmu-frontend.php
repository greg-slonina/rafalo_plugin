<?php
/**
 * WCFMu plugin core
 *
 * Plugin Frontend Controler
 *
 * @author 		WC Lovers
 * @package 	wcfmu/core
 * @version   1.0.0
 */
 
class WCFMu_Frontend {
	
	public function __construct() {
		global $WCFM, $WCFMu;
		
		// WCFM Order Details Status Update
		add_filter( 'wcfm_order_status_modify', array( &$this, 'wcfm_order_status_modify' ), 10, 2 );
		
		// WCFM Product Manage Archive
		add_action( 'wcfm_product_manage', array( &$this, 'wcfm_product_manage_ultimate' ), 10, 2 );
		
		// Order Customer Note 
		add_action( 'woocommerce_view_order', array( &$this, 'wcfm_order_customer_add_note' ), 9 );
		
		// WCFMu Report Menu
		add_filter( 'wcfm_reports_menus', array( &$this, 'wcfmu_reports_menus' ) );
		
		// WCFMu Sales by Date Filters
		add_action( 'wcfm_report_sales_by_date_filters', array( &$this, 'wcfmu_report_sales_by_date_filters' ) );
		
		// WCFMu Reports URL
		add_filter( 'sales_by_product_report_url', array( &$this, 'sales_by_product_report_url' ), 10, 2 );
		add_filter( 'low_in_stock_report_url', array( &$this, 'low_in_stock_report_url' ) );
		
		// WCFMu Product Additional Options
		add_action( 'wcfm_product_manager_right_panel_after', array( &$this, 'wcfmu_products_manage_visibility' ), 20 );
		//add_action( 'wcfm_products_manage_attributes', array( &$this, 'wcfmu_products_manage_text_attributes' ), 20 );
		add_filter( 'wcfm_product_manage_fields_variations', array( &$this, 'wcfmu_product_manage_fields_variations' ), 10, 4 );
		
		// Categoy- Attribute Mapping Settings - 6.1.0
		add_action( 'end_wcfm_settings', array( &$this, 'wcfmu_category_attribute_mapping_settings' ), 9 );
		add_action( 'wcfm_settings_update', array( &$this, 'wcfmu_category_attribute_mapping_settings_update' ), 9 );
		
		// Orders Manage Custom Add Link
		add_action( 'wcfm_orders_manage_after_customers_list',array( &$this, 'wcorder_add_customer_link' ), 40 );
		
		//enqueue scripts
		add_action('wp_enqueue_scripts', array(&$this, 'wcfmu_scripts'), 20);
		//enqueue styles
		add_action('wp_enqueue_scripts', array(&$this, 'wcfmu_styles'), 20);
	}
	
	/**
	 * WCFM Order Details Status Update
	 */
	public function wcfm_order_status_modify( $order_status, $order ) {
		global $WCFM, $WCFMu;
		
		
		
		return $order_status;
	}
	
	/**
	 * WCFM Product Manage
	 */
	function wcfm_product_manage_ultimate( $pro_id, $_product ) {
		global $WCFM, $WCFMu;
		
		if( !apply_filters( 'wcfm_is_allow_quick_edit_product', true ) ) return;
		
		?>
		 <?php if( apply_filters( 'wcfm_is_allow_edit_products', true ) && apply_filters( 'wcfm_is_allow_edit_specific_products', true, $pro_id ) ) { ?>
			 <a class="wcfm_button wcfmu_product_quick_edit" href="#" data-product="<?php echo $pro_id; ?>"><span class="wcfmfa fa-link text_tip" data-tip="<?php echo esc_attr__( 'Quick Edit', 'wc-frontend-manager-ultimate' ); ?>"></span></a>		
			 <span class="wcfm_button_separator">|</span>
		 <?php } ?>
	  <?php
	}
	
	/**
	 * Order Customer Add Note
	 */
	function wcfm_order_customer_add_note( $order_id ) {
		global $WCFM, $WCFMu;
		
		if( !apply_filters( 'wcfm_is_allow_customer_add_note', true ) ) return;
		?>
		<h2><?php _e( 'Add your note', 'wc-frontend-manager-ultimate' ); ?></h2>
		<form action="" method="POST" enctype="multipart/form-data">
			<table class="woocommerce-table">
				<tbody>
					<tr>
						<th><?php _e( 'Add Note', 'wc-frontend-manager-ultimate' ); ?></th>
						<td><textarea name="wcfm_cus_add_note" id="wcfm_cus_add_note"></textarea></td>
					</tr>
					<tr>
						<th><?php _e( 'Upload File', 'wc-frontend-manager-ultimate' ); ?></th>
						<td><input type="file" name="wcfm_cus_add_note_file" id="wcfm_cus_add_note_file"></td>
					</tr>
					<tr>
						<td colspan="2"><input type="submit" name="wcfm_cus_note_submit" id="wcfm_cus_note_submit" value="<?php _e( 'Submit', 'wc-frontend-manager-ultimate' ); ?>"></td>
					</tr>
				</tbody>
			</table>
		</form>
		<?php
		//note and attachment submit code
		if ( isset( $_POST['wcfm_cus_note_submit'] ) ) {
			
			$final_note = '';
			$order = wc_get_order(  $order_id );
			if ( !empty( $_POST['wcfm_cus_add_note'] ) ) {
				$final_note .= $_POST['wcfm_cus_add_note'];
			}
				
			if ( $_FILES ) {
				if ( ! function_exists( 'wp_handle_upload' ) ) {
					require_once( ABSPATH . 'wp-admin/includes/file.php' );
				}
				$uploadedfile = $_FILES['wcfm_cus_add_note_file'];
				$upload_overrides = array( 'test_form' => false );
				$movefile = wp_handle_upload( $uploadedfile, $upload_overrides );
				if ( $movefile && ! isset( $movefile['error'] ) ) {
					if ( !empty( $movefile['url'] ) ) {
						$final_note .= '<p><a target="_blank" class="wcfm_dashboard_item_title wcfm_linked_attached" href ="' . $movefile['url'] . '">' . __( 'See Attachment', 'wc-frontend-manager-ultimate' ) . '</a></p>';
					}
				} 
			}
			
			if( $final_note ) {
				// Add the note
				$order->add_order_note( $final_note, 1, true );
				
				// Save the data
				$order->save();
				
				_e( 'Your note succesfully submitted.', 'wc-frontend-manager-ultimate' );
			
				// Admin NOtification
				$wcfm_messages = sprintf( __( '<b>%s</b> order note added by customer - %s', 'wc-frontend-manager-ultimate' ), '#<a target="_blank" class="wcfm_dashboard_item_title" href="' . get_wcfm_view_order_url($order_id) . '">' . $order_id . '</a>', '<br />' . $final_note );
				$WCFM->wcfm_notification->wcfm_send_direct_message( -2, 0, 1, 0, $wcfm_messages, 'status-update' );
				
				// Vendor Notification
				$line_items = $order->get_items( 'line_item' );
				foreach ( $line_items as $item_id => $item ) {
					$product_id  = $item->get_product_id();
					$vendor_id   = wcfm_get_vendor_id_by_post( $product_id );
					
					if( $vendor_id ) {
						$WCFM->wcfm_notification->wcfm_send_direct_message( -1, $vendor_id, 1, 0, $wcfm_messages, 'status-update' );
					}
				}
			}
		}
	}
	
	/**
	 * WCFMu Reports Menu
	 */
	function wcfmu_reports_menus( $reports_menus ) {
		global $WCFM, $WCFMu;
		
		unset($reports_menus['out-of-stock']);
		$reports_menus['sales-by-product'] = __( 'Sales by product', 'wc-frontend-manager-ultimate');
		$reports_menus['coupons-by-date'] = __( 'Coupons by date', 'wc-frontend-manager-ultimate');
		$reports_menus['low-in-stock'] = __( 'Low in stock', 'wc-frontend-manager-ultimate');
		$reports_menus['out-of-stock'] = __( 'Out of stock', 'wc-frontend-manager-ultimate');
		
		return $reports_menus;
	}
	
	/**
	 * WCFMu Sales by Date Reports Custom Filter
	 */
	function wcfmu_report_sales_by_date_filters() {
		global $WCFM, $WCFMu;
		
		//$WCFMu->template->get_template( 'reports/wcfmu-view-reports-sales-by-date.php' );
	}
	
	/**
	 * WCFMu Reports URL
	 */
	function low_in_stock_report_url( $reports_url ) {
		$reports_url = get_wcfm_reports_url( '', 'wcfm-reports-low-in-stock' );
		return $reports_url;
	}
	
	function sales_by_product_report_url( $reports_url, $top_seller = '' ) {
		$reports_url = get_wcfm_reports_url( '', 'wcfm-reports-sales-by-product' );
		if($top_seller) $reports_url = add_query_arg( 'product_ids', $top_seller, $reports_url );
		return $reports_url;
	}
	
	/**
	 * WCFMu Product Visibility
	 */
	function wcfmu_products_manage_visibility( $product_id ) {
		global $WCFM, $WCFMu, $wp;
		
		$product_object     = $product_id ? wc_get_product( $product_id ) : new WC_Product();
		$current_visibility = $product_object->get_catalog_visibility();
		$visibility_options = wc_get_product_visibility_options();
		
		$advanced_class = '';
		if( !apply_filters( 'wcfm_is_allow_products_manage_visibility', true ) ) $advanced_class = ' wcfm_custom_hide';
		
		if( isset( $wp->query_vars['wcfm-products-manage'] ) ) $advanced_class .= ' wcfm_full_ele catalog_visibility_ele';
		
		$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'product_simple_fields_visibility', array(
																																																		"catalog_visibility" => array('label' => __('Catalog visibility:', 'woocommerce'), 'type' => 'select', 'options' => $visibility_options, 'class' => 'wcfm-select wcfm_ele simple variable external grouped booking ' . $advanced_class, 'label_class' => 'wcfm_title ' . $advanced_class, 'value' => $current_visibility ),
																																											)) );
	}
	
	/**
	 * WCFMu Product Text Attributes using WC Taxonomy Attribute
	 */
	function wcfmu_products_manage_text_attributes( $product_id = 0 ) {
		global $WCFM, $WCFMu, $wc_product_attributes;
		
		$wcfm_attributes = array();
		if( $product_id ) {
			$wcfm_attributes = get_post_meta( $product_id, '_product_attributes', true );
		}
		
		$attribute_taxonomies = wc_get_attribute_taxonomies();
		$attributes = array();
		$acnt = 0;
		if ( ! empty( $attribute_taxonomies ) ) {
			foreach ( $attribute_taxonomies as $attribute_taxonomy ) {
				if ( ( 'text' === $attribute_taxonomy->attribute_type ) && $attribute_taxonomy->attribute_name ) {
					$att_taxonomy = wc_attribute_taxonomy_name( $attribute_taxonomy->attribute_name );
					$attributes[$acnt]['term_name'] = $att_taxonomy;
					$attributes[$acnt]['name'] = wc_attribute_label( $att_taxonomy );
					$attributes[$acnt]['attribute_taxonomy'] = $attribute_taxonomy;
					$attributes[$acnt]['tax_name'] = $att_taxonomy;
					$attributes[$acnt]['is_taxonomy'] = 1;
				
					$attributes[$acnt]['value']          = esc_attr( implode( ' ' . WC_DELIMITER . ' ', wp_get_post_terms( $product_id, $att_taxonomy, array( 'fields' => 'names' ) ) ) );
					$attributes[$acnt]['is_active']      = '';
					$attributes[$acnt]['is_visible']     = '';
					$attributes[$acnt]['is_variation']   = '';
					
					if( $product_id && !empty( $wcfm_attributes ) ) {
						foreach( $wcfm_attributes as $wcfm_attribute ) {
							if ( $wcfm_attribute['is_taxonomy'] ) {
								if( $att_taxonomy == $wcfm_attribute['name'] ) {
									unset( $attributes[$acnt] );
									$acnt--;
								}
							}
						}
					}
				}
				
				$acnt++;
			}
			
			if( !empty( $attributes ) ) {
				$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'product_simple_fields_custom_text_attributes', array(  
																																																"text_attributes" => array( 'type' => 'multiinput', 'class' => 'wcfm-text wcfm_ele simple variable external grouped booking', 'label_class' => 'wcfm_title', 'value' => $attributes, 'options' => array(
																																																		"term_name" => array('type' => 'hidden'),
																																																		"is_active" => array('label' => __('Active?', 'wc-frontend-manager'), 'type' => 'checkbox', 'value' => 'enable', 'custom_attributes' => array( 'tip' => __( 'Check to associate this attribute with the product', 'wc-frontend-manager-ultimate' ) ), 'class' => 'wcfm-checkbox wcfm_ele attribute_ele simple variable external grouped booking text_tip', 'label_class' => 'wcfm_title attribute_ele checkbox_title'),
																																																		"name" => array('label' => __('Name', 'wc-frontend-manager'), 'type' => 'text', 'attributes' => array( 'readonly' => true ), 'class' => 'wcfm-text wcfm_ele attribute_ele simple variable external grouped booking', 'label_class' => 'wcfm_title attribute_ele'),
																																																		"value" => array('label' => __('Value(s):', 'wc-frontend-manager'), 'type' => 'textarea', 'class' => 'wcfm-textarea wcfm_ele simple variable external grouped booking', 'label_class' => 'wcfm_title' ),
																																																		"is_visible" => array('label' => __('Visible on the product page', 'wc-frontend-manager'), 'type' => 'checkbox', 'value' => 'enable', 'class' => 'wcfm-checkbox wcfm_ele simple variable external grouped booking', 'label_class' => 'wcfm_title checkbox_title'),
																																																		"is_variation" => array('label' => __('Use as Variation', 'wc-frontend-manager'), 'type' => 'checkbox', 'value' => 'enable', 'class' => 'wcfm-checkbox wcfm_ele variable variable-subscription', 'label_class' => 'wcfm_title checkbox_title wcfm_ele variable variable-subscription'),
																																																		"tax_name" => array('type' => 'hidden'),
																																																		"is_taxonomy" => array('type' => 'hidden')
																																																))
																																											)) );
			}
				
		}
	}
	
	/**
	 * WCFMu Variation aditional options
	 */
	function wcfmu_product_manage_fields_variations( $variation_fileds, $variations, $variation_shipping_option_array, $variation_tax_classes_options ) {
		global $WCFM, $WCFMu;
		
		$variation_fileds = array_slice($variation_fileds, 0, 2, true) +
																	array(
																				"is_downloadable" => array('label' => __('Downloadable', 'wc-frontend-manager-ultimate'), 'type' => 'checkbox', 'value' => 'enable', 'class' => 'wcfm-checkbox wcfm_ele variable variable-subscription pw-gift-card variation_is_downloadable_ele', 'label_class' => 'wcfm_title checkbox_title')
																				) +
																	array_slice($variation_fileds, 2, count($variation_fileds) - 1, true) ;
		
		$wcfmu_variation_fields = array(
																		"weight" => array('label' => __('Weight', 'wc-frontend-manager-ultimate') . ' ('.get_option( 'woocommerce_weight_unit', 'kg' ).')' , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele wcfm_half_ele variable variable-subscription variation_non_virtual_ele', 'label_class' => 'wcfm_title wcfm_half_ele_title wcfm_ele variable variable-subscription variation_non_virtual_ele'),
																		"length" => array('label' => __('Length', 'wc-frontend-manager-ultimate') . ' ('.get_option( 'woocommerce_dimension_unit', 'cm' ).')', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele wcfm_half_ele variable variable-subscription variation_non_virtual_ele', 'label_class' => 'wcfm_title wcfm_half_ele_title wcfm_ele variable variable-subscription variation_non_virtual_ele'),
																		"width" => array('label' => __('Width', 'wc-frontend-manager-ultimate') . ' ('.get_option( 'woocommerce_dimension_unit', 'cm' ).')' , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele wcfm_half_ele variable variable-subscription variation_non_virtual_ele', 'label_class' => 'wcfm_title wcfm_half_ele_title wcfm_ele variable variable-subscription variation_non_virtual_ele'),
																		"height" => array('label' => __('Height', 'wc-frontend-manager-ultimate') . ' ('.get_option( 'woocommerce_dimension_unit', 'cm' ).')' , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele wcfm_half_ele variable variable-subscription variation_non_virtual_ele', 'label_class' => 'wcfm_title wcfm_half_ele_title wcfm_ele variable variable-subscription variation_non_virtual_ele'),
																		"shipping_class" => array('label' => __('Shipping class', 'wc-frontend-manager-ultimate') , 'type' => 'select', 'options' => $variation_shipping_option_array, 'class' => 'wcfm-select wcfm_ele wcfm_half_ele variable variable-subscription', 'label_class' => 'wcfm_title wcfm_half_ele_title wcfm_ele variable variable-subscription'),
																		"tax_class" => array('label' => __('Tax class', 'wc-frontend-manager-ultimate') , 'type' => 'select', 'options' => $variation_tax_classes_options, 'class' => 'wcfm-select wcfm_ele wcfm_half_ele variable variable-subscription pw-gift-card', 'label_class' => 'wcfm_title wcfm_half_ele_title'),
																		"wcfm_element_breaker_variation_3" => array( 'type' => 'html', 'value' => '<div class="wcfm-cearfix"></div>'),
																		"description" => array('label' => __('Description', 'wc-frontend-manager-ultimate') , 'type' => 'textarea', 'class' => 'wcfm-textarea wcfm_ele wcfm_full_ele variable variable-subscription pw-gift-card', 'label_class' => 'wcfm_title'),
																		"downloadable_file_name" => array('label' => __('File Name', 'wc-frontend-manager-ultimate'), 'type' => 'text', 'class' => 'wcfm-text wcfm_ele wcfm_half_ele variable variable-subscription pw-gift-card variation_downloadable_ele', 'label_class' => 'wcfm_title wcfm_half_ele_title variation_downloadable_ele'),
																		"downloadable_file" => array('label' => __('File', 'wc-frontend-manager-ultimate'), 'type' => 'upload', 'mime' => 'Uploads', 'button_class' => 'downloadable_product', 'class' => 'wcfm-text wcfm_ele wcfm_half_ele variable variable-subscription pw-gift-card variation_downloadable_ele downlodable_file', 'label_class' => 'wcfm_title wcfm_half_ele_title variation_downloadable_ele'),
																		"download_limit" => array('label' => __('Download Limit', 'wc-frontend-manager-ultimate') , 'type' => 'number', 'placeholder' => __('Unlimited', 'wc-frontend-manager-ultimate'), 'class' => 'wcfm-text wcfm_ele wcfm_half_ele variable variable-subscription pw-gift-card variation_downloadable_ele', 'label_class' => 'wcfm_title wcfm_half_ele_title variation_downloadable_ele'),
																		"download_expiry" => array('label' => __('Download Expiry', 'wc-frontend-manager-ultimate') , 'type' => 'number', 'placeholder' => __('Never', 'wc-frontend-manager-ultimate'), 'class' => 'wcfm-text wcfm_ele wcfm_half_ele variable variable-subscription pw-gift-card variation_downloadable_ele', 'label_class' => 'wcfm_title wcfm_half_ele_title variation_downloadable_ele'),
																		);
		$variation_fileds = array_merge( $variation_fileds, $wcfmu_variation_fields );
		
		if( isset( $variation_fileds['sale_price'] ) ) {
			$variation_fileds['sale_price']['desc'] = __( 'schedule', 'wc-frontend-manager' );
			$variation_fileds['sale_price']['desc_class'] = 'wcfm_ele variable variable-subscription var_sales_schedule'; 
		}
		
		return $variation_fileds;
	}
	
	/**
	 * WCFMu Category - Attributes Mapping Setting
	 */
	function wcfmu_category_attribute_mapping_settings( $wcfm_options ) {
		global $WCFM, $WCFMu;
		$wcfm_category_attributes_mapping = wcfm_get_option( 'wcfm_category_attributes_mapping', array() );
		$attribute_taxonomies = wc_get_attribute_taxonomies();
		?>
		<!-- collapsible -->
		<div class="page_collapsible" id="wcfm_settings_form_category_attributes_mapping_head">
			<label class="wcfmfa fa-server"></label>
			<?php _e('Categories wise Attributes', 'wc-frontend-manager-ultimate'); ?><span></span>
		</div>
		<div class="wcfm-container">
			<div id="wcfm_settings_form_category_attributes_mapping_expander" class="wcfm-content">
				<h2><?php _e('Category Specific Attributes Setup', 'wc-frontend-manager-ultimate'); ?></h2>
				<?php wcfm_video_tutorial( 'https://docs.wclovers.com/attributes/#category-attributes-mapping' ); ?>
				<div class="wcfm_clearfix"></div>
				<?php
				  if( apply_filters( 'wcfm_is_allow_sub_category_attributes_mapping', false ) ) {
				  	$product_category_lists = get_terms( apply_filters( 'wcfm_category_attributes_mapping_taxonomy_query_args', array( 'taxonomy' => 'product_cat', 'hide_empty' => false, 'fields' => 'id=>name' ) ) );
				  } else {
				  	$product_category_lists = get_terms( apply_filters( 'wcfm_category_attributes_mapping_taxonomy_query_args', array( 'taxonomy' => 'product_cat', 'hide_empty' => false, 'parent' => 0, 'fields' => 'id=>name' ) ) );
				  }
					if( !empty( $product_category_lists ) ) {
						foreach( $product_category_lists as $product_category_id => $product_category_name ) {
							$category_attributes = isset( $wcfm_category_attributes_mapping[$product_category_id] ) ? $wcfm_category_attributes_mapping[$product_category_id] : array();
						?>
						<p class="wcfm_title catlimit_title"><strong><?php echo $product_category_name . ' '; _e( 'Attributes', 'wc-frontend-manager-ultimate' ); ?></strong></p><label class="screen-reader-text"><?php echo $product_category_name . ' '; _e( 'Attributes', 'wc-frontend-manager' ); ?></label>
						<select id="wcfm_category_attributes_mapping<?php echo $product_category_name; ?>" name="wcfm_category_attributes_mapping[<?php echo $product_category_id; ?>][]" class="wcfm-select wcfm_ele wcfm_category_attributes_mapping" multiple="multiple" data-catlimit="-1" style="width: 60%; margin-bottom: 10px;">
							<?php
								if ( $attribute_taxonomies ) {
									foreach ( $attribute_taxonomies as $attribute_taxonomy ) {
										$att_taxonomy = wc_attribute_taxonomy_name( $attribute_taxonomy->attribute_name );
										$is_checked = '';
										if( in_array( $att_taxonomy, $category_attributes ) ) $is_checked = 'selected';
										echo '<option value="' . $att_taxonomy . '" ' . $is_checked . '>' . wc_attribute_label( $att_taxonomy ) . '</option>';
									}
								}
							?>
						</select>
						<?php
						}
					}
				?>
				<p class="description instructions"><?php _e( 'Create Attributes group as per Categories. If no group for a category then all attributes will be available for that.', 'wc-frontend-manager-ultimate' ); ?></p>
			</div>
		</div>
		<div class="wcfm_clearfix"></div>
		<!-- end collapsible -->
		
		<?php
		
	}
	
	/**
	 * WCFMu Category - Attributes Mapping Setting Update
	 */
	function wcfmu_category_attribute_mapping_settings_update( $wcfm_settings_form ) {
		global $WCFM, $WCFMu, $_POST;
		
		if( isset( $wcfm_settings_form['wcfm_category_attributes_mapping'] ) ) {
			$wcfm_category_attributes_mapping = $wcfm_settings_form['wcfm_category_attributes_mapping'];
			wcfm_update_option( 'wcfm_category_attributes_mapping',  $wcfm_category_attributes_mapping );
		}
	}
	
	/**
	 * Order Manage Add Customer Link
	 */
	function wcorder_add_customer_link() {
		global $WCFM, $WCFMu;
		
		if( apply_filters( 'wcfm_is_allow_manage_customer', true ) && apply_filters( 'wcfm_is_allow_edit_customer', true ) ) {
			?>
			<div class="wcfm_order_add_new_customer_box">
				<p class="description wcfm_full_ele wcfm_order_add_new_customer"><span class="wcfmfa fa-plus-circle"></span>&nbsp;<?php _e( 'Add new customer', 'wc-frontend-manager-ultimate' ); ?></p>
			</div>
			<?php
		}
	}
	
	/**
	 * WCFMu Core JS
	 */
	function wcfmu_scripts() {
 		global $WCFMu;
 		
 		if( isset( $_REQUEST['fl_builder'] ) ) return;
 		
 		// WCFMu Core JS
	  //wp_enqueue_script( 'wcfmu_core_js', $WCFMu->library->js_lib_url . 'wcfmu-script-core.js', array( 'jquery' ), $WCFMu->version, true );
	  
	  // Localize Script
	  //$wcfm_messages = get_wcfm_products_manager_messages();
		//wp_localize_script( 'wcfmu_core_js', 'wcfmu_products_manage_messages', $wcfm_messages );
 	}
 	
 	/**
 	 * WCFMu Core CSS
 	 */
 	function wcfmu_styles() {
 		global $WCFMu;
 		
 		if( isset( $_REQUEST['fl_builder'] ) ) return;
 		
	  // WCFMu Core CSS
	  //wp_enqueue_style( 'wcfmu_core_css',  $WCFMu->library->css_lib_url . 'wcfmu-style-core.css', array(), $WCFMu->version );
 	}
}