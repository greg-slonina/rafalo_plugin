<?php

/**
 * WCfM Product Types plugin core
 *
 * WC Product Addons Support
 *
 * @author 		WC Lovers
 * @package 	wcfmu/core
 * @version   2.4.1
 */
 
class WCFMu_WCAddons {
	
	public function __construct() {
    global $WCFM, $WCFMu;
    
    if( apply_filters( 'wcfm_is_pref_wc_product_addon' , true ) && apply_filters( 'wcfm_is_allow_wc_product_addon' , true ) ) {
			if( WCFMu_Dependencies::wcfm_wc_addons_active_check() || WCFMu_Dependencies::wcfm_wc_appointments_active_check() ) {
				
				// WC Addons 3.0+ compatibility
				if( ( defined( 'WC_PRODUCT_ADDONS_VERSION' ) && version_compare( WC_PRODUCT_ADDONS_VERSION, '3.0.0', '>=' ) ) || WCFMu_Dependencies::wcfm_wc_appointments_active_check() ) {
					// WC Product Addons Product Manage View
					add_action( 'end_wcfm_products_manage', array( &$this, 'wcaddons_wcfm_products_manage_3' ), 210 );
				
					// WC Product Addons Load WCFMu Scripts
					add_action( 'wcfm_load_scripts', array( &$this, 'wcaddons_load_scripts_3' ), 90 );
					add_action( 'after_wcfm_load_scripts', array( &$this, 'wcaddons_load_scripts_3' ), 90 );
					
					// WC Product Addons Product Manage View
					add_action( 'after_wcfm_products_manage_meta_save', array( &$this, 'wcaddons_wcfm_products_manage_meta_save_3' ), 200, 2 );
				} else {
				
					// WC Product Addons Product Manage View
					add_action( 'end_wcfm_products_manage', array( &$this, 'wcaddons_wcfm_products_manage' ), 210 );
					
					// WC Product Addons Load WCFMu Scripts
					add_action( 'wcfm_load_scripts', array( &$this, 'wcaddons_load_scripts' ), 90 );
					add_action( 'after_wcfm_load_scripts', array( &$this, 'wcaddons_load_scripts' ), 90 );
					
					// WC Product Addons Product Manage View
					add_action( 'after_wcfm_products_manage_meta_save', array( &$this, 'wcaddons_wcfm_products_manage_meta_save' ), 200, 2 );
				}
			}
		}
	}
	
	/**
   * WC Product Addons load views
   */
  function wcaddons_wcfm_products_manage_3( $product_id ) {
  	global $WCFM, $WCFMu;
  	
		if( $product_id ) {
			$product        = wc_get_product( $product_id );
			$exists         = (bool) $product->get_id();
			$product_addons = apply_filters( 'wcfm_wc_product_addon_elements', array_filter( (array) $product->get_meta( '_product_addons' ) ), $product_id );
			$exclude_global = $product->get_meta( '_product_addons_exclude_global' );
		} else {
			$product        = '';
			$exists         = false;
			$product_addons = apply_filters( 'wcfm_wc_product_addon_elements', array(), $product_id );
			$exclude_global = 0;
		}
		
		?>
		<div class="page_collapsible products_manage_wcaddons wcaddons" id="wcfm_products_manage_form_wcaddons_head"><label class="wcfmfa fa-gem"></label><?php _e('Add-ons', 'wc-frontend-manager-ultimate'); ?><span></span></div>
		<div class="wcfm-container wcaddons">
			<div id="wcfm_products_manage_form_wcaddons_expander" class="wcfm-content">
				<?php include_once( $WCFMu->plugin_path . 'includes/wcaddons/html-addon-panel.php' ); ?>
			</div>
		</div>
		<!-- end collapsible -->
		<div class="wcfm_clearfix"></div>
		<?php
  }
  
  /**
	* WC Product Addons Scripts
	*/
  public function wcaddons_load_scripts_3( $end_point ) {
	  global $WCFM, $WCFMu;
    
	  switch( $end_point ) {
	  	case 'wcfm-products-manage':
				wp_enqueue_style( 'woocommerce_product_addons_css', WC_PRODUCT_ADDONS_PLUGIN_URL . '/assets/css/admin.css', array(), WC_PRODUCT_ADDONS_VERSION );
				
				if( WCFMu_Dependencies::wcfm_wc_appointments_active_check() ) {
					wp_enqueue_style( 'wc_appointments_admin_styles', WC_APPOINTMENTS_PLUGIN_URL . '/assets/css/admin.css', array( 'woocommerce_product_addons_css' ), WC_APPOINTMENTS_VERSION );
				}

				$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		
				wp_register_script( 'woocommerce_product_addons', plugins_url( 'assets/js/admin' . $suffix . '.js', WC_PRODUCT_ADDONS_MAIN_FILE ), array( 'jquery' ), WC_PRODUCT_ADDONS_VERSION, true );
		
				$params = array(
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'nonce'    => array(
						'get_addon_options' => wp_create_nonce( 'wc-pao-get-addon-options' ),
						'get_addon_field'   => wp_create_nonce( 'wc-pao-get-addon-field' ),
					),
					'i18n'     => array(
						'required_fields'       => __( 'All fields must have a title and/or option name. Please review the settings highlighted in red border.', 'woocommerce-product-addons' ),
						'limit_price_range'         => __( 'Limit price range', 'woocommerce-product-addons' ),
						'limit_quantity_range'      => __( 'Limit quantity range', 'woocommerce-product-addons' ),
						'limit_character_length'    => __( 'Limit character length', 'woocommerce-product-addons' ),
						'restrictions'              => __( 'Restrictions', 'woocommerce-product-addons' ),
						'confirm_remove_addon'      => __( 'Are you sure you want remove this add-on field?', 'woocommerce-product-addons' ),
						'confirm_remove_option'     => __( 'Are you sure you want delete this option?', 'woocommerce-product-addons' ),
						'add_image_swatch'          => __( 'Add Image Swatch', 'woocommerce-product-addons' ),
						'add_image'                 => __( 'Add Image', 'woocommerce-product-addons' ),
					),
				);
		
				wp_localize_script( 'woocommerce_product_addons', 'wc_pao_params', apply_filters( 'wc_pao_params', $params ) );
		
				wp_enqueue_script( 'woocommerce_product_addons' );
				
				wp_enqueue_script( 'wcfmu_wcaddons_products_manage_js', $WCFMu->library->js_lib_url . 'products/wcfmu-script-wcaddons-products-manage.js', array( 'jquery', 'wcfm_products_manage_js' ), $WCFMu->version, true );
			break;
		}
	}
	
	/**
	 * WC Product Addons Product Meta data save
	 */
	function wcaddons_wcfm_products_manage_meta_save_3( $new_product_id, $wcfm_products_manage_form_data ) {
		global $WCFM, $WCFMu;
	
		$product_addons = array();

		if ( isset( $wcfm_products_manage_form_data['product_addon_name'] ) ) {
			$addon_name               = $wcfm_products_manage_form_data['product_addon_name'];
			$addon_title_format       = $wcfm_products_manage_form_data['product_addon_title_format'];
			$addon_description_enable = isset( $wcfm_products_manage_form_data['product_addon_description_enable'] ) ? $wcfm_products_manage_form_data['product_addon_description_enable'] : array();
			$addon_description        = $wcfm_products_manage_form_data['product_addon_description'];
			$addon_type               = $wcfm_products_manage_form_data['product_addon_type'];
			$addon_display            = $wcfm_products_manage_form_data['product_addon_display'];
			$addon_position           = $wcfm_products_manage_form_data['product_addon_position'];
			$addon_required           = isset( $wcfm_products_manage_form_data['product_addon_required'] ) ? $wcfm_products_manage_form_data['product_addon_required'] : array();
			$addon_option_label       = $wcfm_products_manage_form_data['product_addon_option_label'];
			$addon_option_price       = $wcfm_products_manage_form_data['product_addon_option_price'];
			$addon_option_price_type  = $wcfm_products_manage_form_data['product_addon_option_price_type'];
			$addon_option_image       = $wcfm_products_manage_form_data['product_addon_option_image'];
			$addon_restrictions       = isset( $wcfm_products_manage_form_data['product_addon_restrictions'] ) ? $wcfm_products_manage_form_data['product_addon_restrictions'] : array();
			$addon_restrictions_type  = $wcfm_products_manage_form_data['product_addon_restrictions_type'];
			$addon_adjust_price       = isset( $wcfm_products_manage_form_data['product_addon_adjust_price'] ) ? $wcfm_products_manage_form_data['product_addon_adjust_price'] : array();
			$addon_price_type         = $wcfm_products_manage_form_data['product_addon_price_type'];
			$addon_price              = $wcfm_products_manage_form_data['product_addon_price'];
			$addon_min                = $wcfm_products_manage_form_data['product_addon_min'];
			$addon_max                = $wcfm_products_manage_form_data['product_addon_max'];
			
			$_POST                    = $wcfm_products_manage_form_data;

			for ( $i = 0; $i < count( $addon_name ); $i++ ) {
				if ( ! isset( $addon_name[ $i ] ) || ( '' == $addon_name[ $i ] ) ) {
					continue;
				}

				$addon_options = array();

				if ( isset( $addon_option_label[ $i ] ) ) {
					$option_label      = $addon_option_label[ $i ];
					$option_price      = $addon_option_price[ $i ];
					$option_price_type = $addon_option_price_type[ $i ];
					$option_image      = $addon_option_image[ $i ];

					for ( $ii = 0; $ii < count( $option_label ); $ii++ ) {
						$label      = sanitize_text_field( stripslashes( $option_label[ $ii ] ) );
						$price      = wc_format_decimal( sanitize_text_field( stripslashes( $option_price[ $ii ] ) ) );
						$image      = sanitize_text_field( stripslashes( $option_image[ $ii ] ) );
						$price_type = sanitize_text_field( stripslashes( $option_price_type[ $ii ] ) );

						$addon_options[] = array(
							'label'      => $label,
							'price'      => $price,
							'image'      => $image,
							'price_type' => $price_type,
						);
					}
				}

				$data                       = array();
				$data['name']               = sanitize_text_field( stripslashes( $addon_name[ $i ] ) );
				$data['title_format']       = sanitize_text_field( stripslashes( $addon_title_format[ $i ] ) );
				$data['description_enable'] = isset( $addon_description_enable[ $i ] ) ? 1 : 0;
				$data['description']        = wp_kses_post( stripslashes( $addon_description[ $i ] ) );
				$data['type']               = sanitize_text_field( stripslashes( $addon_type[ $i ] ) );
				$data['display']            = sanitize_text_field( stripslashes( $addon_display[ $i ] ) );
				$data['position']           = absint( $addon_position[ $i ] );
				$data['required']           = isset( $addon_required[ $i ] ) ? 1 : 0;
				$data['restrictions']       = isset( $addon_restrictions[ $i ] ) ? 1 : 0;
				$data['restrictions_type']  = sanitize_text_field( stripslashes( $addon_restrictions_type[ $i ] ) );
				$data['adjust_price']       = isset( $addon_adjust_price[ $i ] ) ? 1 : 0;
				$data['price_type']         = sanitize_text_field( stripslashes( $addon_price_type[ $i ] ) );
				$data['price']              = (float) sanitize_text_field( stripslashes( $addon_price[ $i ] ) );
				$data['min']                = (float) sanitize_text_field( stripslashes( $addon_min[ $i ] ) );
				$data['max']                = (float) sanitize_text_field( stripslashes( $addon_max[ $i ] ) );

				// If restrictions is enabled and minimum is greater than 0, make required.
				if ( $data['restrictions'] && 0 < $data['min'] ) {
					$data['required'] = 1; 
				}

				if ( ! empty( $addon_options ) ) {
					$data['options'] = $addon_options;
				}

				// Add to array.
				$product_addons[] = apply_filters( 'woocommerce_product_addons_save_data', $data, $i );
			}
		}

		if ( ! empty( $wcfm_products_manage_form_data['import_product_addon'] ) ) {
			$import_addons = maybe_unserialize( maybe_unserialize( stripslashes( trim( $wcfm_products_manage_form_data['import_product_addon'] ) ) ) );

			if ( is_array( $import_addons ) && sizeof( $import_addons ) > 0 ) {
				$valid = true;

				foreach ( $import_addons as $addon ) {
					if ( ! isset( $addon['name'] ) || ! $addon['name'] ) {
						$valid = false;
					}
					if ( ! isset( $addon['description'] ) ) {
						$valid = false;
					}
					if ( ! isset( $addon['type'] ) ) {
						$valid = false;
					}
					if ( ! isset( $addon['position'] ) ) {
						$valid = false;
					}
					if ( ! isset( $addon['required'] ) ) {
						$valid = false;
					}
				}

				if ( $valid ) {
					$product_addons = array_merge( $product_addons, $import_addons );
				}
			}
		}

		uasort( $product_addons, array( $this, 'addons_cmp' ) );
		
		
		$product_addons_exclude_global = isset( $wcfm_products_manage_form_data['_product_addons_exclude_global'] ) ? 1 : 0;

		$product = wc_get_product( $new_product_id );
		$product->update_meta_data( '_product_addons', $product_addons );
		$product->update_meta_data( '_product_addons_exclude_global', $product_addons_exclude_global );
		$product->save();
		
	}
	
	/**
	 * Sort addons.
	 *
	 * @param  array $a First item to compare.
	 * @param  array $b Second item to compare.
	 * @return bool
	 */
	protected function addons_cmp( $a, $b ) {
		if ( $a['position'] == $b['position'] ) {
			return 0;
		}

		return ( $a['position'] < $b['position'] ) ? -1 : 1;
	}
	
	/**
	 * Converts the field type key to display name.
	 *
	 * @since 3.0.0
	 * @param string $type
	 * @return string $name
	 */
	public function convert_type_name( $type = '' ) {
		switch ( $type ) {
			case 'checkboxes':
				$name = __( 'Checkbox', 'woocommerce-product-addons' );
				break;
			case 'custom_price':
				$name = __( 'Price', 'woocommerce-product-addons' );
				break;
			case 'input_multiplier':
				$name = __( 'Quantity', 'woocommerce-product-addons' );
				break;
			case 'custom_text':
				$name = __( 'Short Text', 'woocommerce-product-addons' );
				break;
			case 'custom_textarea':
				$name = __( 'Long Text', 'woocommerce-product-addons' );
				break;
			case 'file_upload':
				$name = __( 'File Upload', 'woocommerce-product-addons' );
				break;
			case 'select':
				$name = __( 'Dropdown', 'woocommerce-product-addons' );
				break;
			case 'multiple_choice':
			default:
				$name = __( 'Multiple Choice', 'woocommerce-product-addons' );
				break;
		}

		return $name;
	}
	
	
	////////////////////////////////////////////////////// Old Version Support < 3.0 /////////////////////////////////////////////////////////////////////////////////////
	
	/**
   * WC Product Addons load views
   */
  function wcaddons_wcfm_products_manage( $product_id ) {
		global $WCFM, $WCFMu;
	  
	  $_product_addons = array();
	  $_product_addons_exclude_global = 0;

		if( $product_id ) {
			$_product_addons = (array) get_post_meta( $product_id, '_product_addons', true );
			$_product_addons_exclude_global = get_post_meta( $product_id, '_product_addons_exclude_global', true ) ? get_post_meta( $product_id, '_product_addons_exclude_global', true ) : 0;
		}
		
		$group_types = apply_filters( 'wcfm_wcaddon_group_types', array(  'custom_price'               => __( 'Additional custom price input', 'wc-frontend-manager-ultimate' ),
																																			'input_multiplier'           => __( 'Additional price multiplier', 'wc-frontend-manager-ultimate' ),
																																			'checkbox'                   => __( 'Checkboxes', 'wc-frontend-manager-ultimate' ),
																																			'custom_textarea'            => __( 'Custom input (textarea)', 'wc-frontend-manager-ultimate' ),
																																			'custom'                     => __( 'Any text', 'wc-frontend-manager-ultimate' ),
																																			'custom_email'               => __( 'Email address', 'wc-frontend-manager-ultimate' ),
																																			'custom_letters_only'        => __( 'Only letters', 'wc-frontend-manager-ultimate' ),
																																			'custom_letters_or_digits'   => __( 'Only letters and numbers', 'wc-frontend-manager-ultimate' ),
																																			'custom_digits_only'         => __( 'Only numbers', 'wc-frontend-manager-ultimate' ),
																																			'file_upload'                => __( 'File upload', 'wc-frontend-manager-ultimate' ),
																																			'radiobutton'                => __( 'Radio buttons', 'wc-frontend-manager-ultimate' ),
																																			'select'                     => __( 'Select box', 'wc-frontend-manager-ultimate' )
																																		) );
		
		?>
		
		<div class="page_collapsible products_manage_wcaddons wcaddons" id="wcfm_products_manage_form_wcaddons_head"><label class="wcfmfa fa-gem"></label><?php _e('Add-ons', 'wc-frontend-manager-ultimate'); ?><span></span></div>
		<div class="wcfm-container wcaddons">
			<div id="wcfm_products_manage_form_wcaddons_expander" class="wcfm-content">
				<?php
				$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_product_manage_fields_wcaddons', array( 
					"_product_addons" =>     array('label' => __('Add-ons', 'wc-frontend-manager-ultimate') , 'type' => 'multiinput', 'class' => 'wcfm_ele wcaddons', 'label_class' => 'wcfm_title wcaddons', 'value' => $_product_addons, 'options' => array(
												"type" => array('label' => __('Group', 'wc-frontend-manager-ultimate'), 'type' => 'select', 'options' => $group_types, 'class' => 'wcfm-select addon_fields_option wcaddons', 'label_class' => 'wcfm_title wcaddons' ),
												"name" => array('label' => __('Name', 'wc-frontend-manager-ultimate'), 'type' => 'text', 'class' => 'wcfm-text', 'label_class' => 'wcfm_title' ),
												"position" => array( 'type' => 'hidden' ),
												"description" => array('label' => __('Description', 'wc-frontend-manager-ultimate'), 'type' => 'textarea', 'class' => 'wcfm-textarea', 'label_class' => 'wcfm_title' ),
												"required" => array('label' => __('Required fields?', 'wc-frontend-manager-ultimate'), 'type' => 'checkbox', 'class' => 'wcfm-checkbox', 'label_class' => 'wcfm_title checkbox_title', 'value' => 1 ),
												"options" =>     array('label' => __('Options', 'wc-frontend-manager-ultimate') . '<span class="fields_collapser wcfmfa fa-arrow-circle-down"></span>', 'type' => 'multiinput', 'class' => 'wcfm_ele wcaddons wcfm_wcaddons_fields', 'label_class' => 'wcfm_title wcaddons', 'options' => array(
														"label" => array('label' => __('Label', 'wc-frontend-manager-ultimate'), 'type' => 'text', 'class' => 'wcfm-text', 'label_class' => 'wcfm_title' ),
														"price" => array('label' => __('Price', 'wc-frontend-manager-ultimate'), 'type' => 'text', 'class' => 'wcfm-text addon_fields addon_price', 'label_class' => 'wcfm_title addon_fields addon_price' ),
														"min" => array('label' => __('Min', 'wc-frontend-manager-ultimate'), 'type' => 'number', 'class' => 'wcfm-text addon_fields addon_minmax', 'label_class' => 'wcfm_title addon_fields addon_minmax' ),
														"max" => array('label' => __('Max', 'wc-frontend-manager-ultimate'), 'type' => 'number', 'class' => 'wcfm-text addon_fields addon_minmax', 'label_class' => 'wcfm_title addon_fields addon_minmax' ),
													) )
												)	),
											 "_product_addons_exclude_global" => array('label' => __('Global Addon Exclusion', 'wc-frontend-manager-ultimate'), 'type' => 'checkbox', 'class' => 'wcfm-checkbox', 'label_class' => 'wcfm_title checkbox_title', 'value' => 1, 'dfvalue' => $_product_addons_exclude_global, 'hints' => __( 'Check this to exclude this product from all Global Addons', 'wc-frontend-manager-ultimate' ) )
					) ) );
				?>
			</div>
		</div>
		<!-- end collapsible -->
		<div class="wcfm_clearfix"></div>
		<?php
	}
	
	/**
	* WC Product Addons Scripts
	*/
  public function wcaddons_load_scripts( $end_point ) {
	  global $WCFM, $WCFMu;
    
	  switch( $end_point ) {
	  	case 'wcfm-products-manage':
				wp_enqueue_script( 'wcfmu_wcaddons_products_manage_js', $WCFMu->library->js_lib_url . 'products/wcfmu-script-wcaddons-products-manage.js', array( 'jquery', 'wcfm_products_manage_js' ), $WCFMu->version, true );
			break;
		}
	}
	
	/**
	 * WC Product Addons Product Meta data save
	 */
	function wcaddons_wcfm_products_manage_meta_save( $new_product_id, $wcfm_products_manage_form_data ) {
		global $WCFM, $WCFMu;
		
		$_product_addons = array();
		
		if( isset( $wcfm_products_manage_form_data['_product_addons'] ) && !empty( $wcfm_products_manage_form_data['_product_addons'] ) ) {
		  $_product_addons = $wcfm_products_manage_form_data['_product_addons'];
		  
		  if( !empty( $_product_addons ) ) {
		  	$loop_index = 0;
		  	foreach( $_product_addons as $_product_addon_index => $_product_addon ) {
		  		$_product_addons[$_product_addon_index]['position'] = $loop_index;
		  		if( isset( $_product_addon['required'] ) ) $_product_addons[$_product_addon_index]['required'] = 1;
		  		else $_product_addons[$_product_addon_index]['required'] = 0;
		  		$loop_index++;
		  	}
		  }
		  update_post_meta( $new_product_id, '_product_addons', $_product_addons );
		}
		
		if( isset( $wcfm_products_manage_form_data['_product_addons_exclude_global'] ) && !empty( $wcfm_products_manage_form_data['_product_addons_exclude_global'] ) ) {
			update_post_meta( $new_product_id, '_product_addons_exclude_global', 1 );
		} else {
			update_post_meta( $new_product_id, '_product_addons_exclude_global', 0 );
		}
	}
	
}