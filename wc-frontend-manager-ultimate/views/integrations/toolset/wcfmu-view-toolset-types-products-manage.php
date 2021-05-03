<?php
/**
 * WCFM plugin views
 *
 * Plugin Toolset Types Products Manage Views
 *
 * @author 		WC Lovers
 * @package 	wcfmu/views/thirdparty
 * @version   2.5.0
 */
global $wp, $WCFM, $WCFMu;

if( !apply_filters( 'wcfm_is_allow_toolset_types', true ) || !apply_filters( 'wcfm_is_allow_toolset_types_product', true ) ) {
	return;
}

include_once( WPCF_EMBEDDED_ABSPATH . '/includes/fields-post.php' );

$product_id = 0;

if( isset( $wp->query_vars['wcfm-products-manage'] ) && !empty( $wp->query_vars['wcfm-products-manage'] ) ) {
	$product_id = $wp->query_vars['wcfm-products-manage'];
}

if( !$product_id ) {
	//$product_post = get_default_post_to_edit( 'product', false );
}

$product_post = get_post();
$product_post->post_type = 'product';

if( class_exists( 'Types_Post_Type' ) ) {
	 $Types_Post_Type = new Types_Post_Type( 'product' );
	 $field_groups = $Types_Post_Type->get_field_groups();
} else {
	return;
}

// Get groups
$field_groups = wpcf_admin_post_get_post_groups_fields( $product_post );
//print_r($field_groups);
//die;

if( !empty( $field_groups )) {
	foreach( $field_groups as $field_group_index => $field_group ) {
		//If Access plugin activated
		if ( function_exists( 'wpcf_access_register_caps' ) ) {
			//If user can't view own profile fields
			if ( !current_user_can( 'view_fields_in_edit_page_' . $field_group['slug'] ) ) {
				continue;
			}
			//If user can modify current group in own profile
			if ( !current_user_can( 'modify_fields_in_edit_page_' . $field_group['slug'] ) ) {
				continue;
			}
		}
		
		if ( isset( $group['__show_meta_box'] ) && $group['__show_meta_box'] == false ) continue;
		if( version_compare( TYPES_VERSION, '3.0', '>=' ) || version_compare( TYPES_VERSION, '3.0.1', '>=' ) ) {
			$field_group_load = Toolset_Field_Group_Post_Factory::load( $field_group['slug'] );
		} else {
			$field_group_load = Types_Field_Group_Post_Factory::load( $field_group['slug'] );
		}
    if( null === $field_group_load ) continue;
    
    // WooCommerce Filter Views discard
    if( $field_group['slug'] == 'woocommerce-views-filter-fields' ) continue;
    
    $wcfm_is_allowed_toolset_field_group = apply_filters( 'wcfm_is_allow_product_toolset_field_group', true, $field_group_index, $field_group );
    if( !$wcfm_is_allowed_toolset_field_group ) continue;
    
		if ( !empty( $field_group['fields'] ) ) { 
			?>
			<div class="page_collapsible wcfm_toolset_products_manage_collapsible wcfm_toolset_products_manage_<?php echo $field_group['id']; ?>_collapsible simple variable external grouped booking" id="wcfm_products_manage_form_<?php echo $field_group['id']; ?>_head"><label class="wcfmfa fa-certificate"></label><?php echo $field_group['name']; ?><span></span></div>
			<div class="wcfm-container simple variable external grouped booking wcfm_toolset_products_manage_container wcfm_toolset_products_manage_<?php echo $field_group['id']; ?>_container">
				<div id="wcfm_products_manage_form_<?php echo $field_group['id']; ?>_expander" class="wcfm-content">
				  <h2><?php echo $field_group['name']; ?></h2>
				  <div class="wcfm_clearfix"></div>
				  <?php
				  if ( !empty( $field_group['fields'] ) ) {
				  	foreach( $field_group['fields'] as $field_group_field ) {
				  		
				  		$wcfm_is_allowed_toolset_field = apply_filters( 'wcfm_is_allow_product_toolset_field', true, $field_group_field );
				  		if( !$wcfm_is_allowed_toolset_field ) continue;
				  		
				  		// Field Value
				  		$field_value = '';
				  		if( isset( $field_group_field['data'] ) && isset( $field_group_field['data']['user_default_value'] ) ) $field_value = $field_group_field['data']['user_default_value'];
				  		if( $product_id ) $field_value = get_post_meta( $product_id, $field_group_field['meta_key'], true );
				  		
				  		// Paceholder
				  		$field_paceholder = '';
				  		if( isset( $field_group_field['data'] ) && isset( $field_group_field['data']['placeholder'] ) ) $field_paceholder = $field_group_field['data']['placeholder'];
				  		
				  		// Is Required
				  		$custom_attributes = array();
				  		if( isset( $field_group_field['data'] ) && isset( $field_group_field['data']['validate'] ) && isset( $field_group_field['data']['validate']['required'] ) && $field_group_field['data']['validate']['required'] ) $custom_attributes = array( 'required' => 1 );
				  		if( isset( $field_group_field['data'] ) && isset( $field_group_field['data']['validate'] ) && isset( $field_group_field['data']['validate']['required'] ) && $field_group_field['data']['validate']['required'] && isset( $field_group_field['data']['validate']['message'] ) && $field_group_field['data']['validate']['message'] ) $custom_attributes['required_message'] = $field_group_field['data']['validate']['message'];
				  		
				  		// For Multi-line Fields
				  		if ( wpcf_admin_is_repetitive( $field_group_field ) ) {
				  			$field_value = array();
				  			$field_value_repetatives = (array) get_post_meta( $product_id, $field_group_field['meta_key'] );
				  			if( !empty( $field_value_repetatives ) ) {
				  				foreach( $field_value_repetatives as $field_value_repetative ) {
				  					$field_value[] = array( 'field' => $field_value_repetative );
				  				}
				  			}
				  		}
				  		
				  		switch( $field_group_field['type'] ) {
								case 'url':
				  			case 'phone':
								case 'textfield':
									if ( wpcf_admin_is_repetitive( $field_group_field ) ) {
										$WCFM->wcfm_fields->wcfm_generate_form_field(  array( $field_group_field['meta_key'] => array( 'label' => $field_group_field['name'], 'custom_attributes' => $custom_attributes, 'desc' => $field_group_field['description'], 'type' => 'multiinput', 'name' => 'wpcf[' . $field_group_field['meta_key'] . ']', 'label_class' => 'wcfm_title', 'value' => $field_value, 'options' => array (
																																					'field' => array( 'type' => 'text', 'custom_attributes' => $custom_attributes, 'placeholder' => $field_paceholder, 'class' => 'wcfm-text wcfm_ele wcfm_full_ele simple variable external grouped booking' )
																																				) ) ) );
									} else {
										$WCFM->wcfm_fields->wcfm_generate_form_field(  array( $field_group_field['meta_key'] => array( 'label' => $field_group_field['name'], 'custom_attributes' => $custom_attributes, 'placeholder' => $field_paceholder, 'hints' => $field_group_field['description'], 'name' => 'wpcf[' . $field_group_field['meta_key'] . ']', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele simple variable external grouped booking', 'label_class' => 'wcfm_title', 'value' => $field_value ) ) );
									}
								break;
								
								case 'google_address':
									if( WCFMu_Dependencies::wcfm_toolset_address_map_active_check() ) {
										if( apply_filters( 'wcfm_is_allow_toolset_address_map', true ) ) {
											$data_coordinates = '';
											if ( empty( $field_value ) ) { $field_value = apply_filters( 'wcfm_geo_locator_default_address', '' ); }
											if ( ! empty( $field_value ) ) {
												$has_coordinates = Toolset_Addon_Maps_Common::get_coordinates( $field_value );
												if ( is_array( $has_coordinates ) ) {
													$data_coordinates = '{' . esc_attr( $has_coordinates['lat'] ) . ',' . esc_attr( $has_coordinates['lon'] ) . '}';
												}
											}
											?>
											<div class="toolset-google-map-container js-toolset-google-map-container">
												<div class="toolset-google-map-inputs-container js-toolset-google-map-inputs-container">
													<?php $WCFM->wcfm_fields->wcfm_generate_form_field(  array( $field_group_field['meta_key'] => array( 'label' => $field_group_field['name'], 'custom_attributes' => $custom_attributes, 'hints' => $field_group_field['description'], 'name' => 'wpcf[' . $field_group_field['meta_key'] . ']', 'custom_attributes' => array( 'coordinates' => $data_coordinates ), 'type' => 'text', 'class' => 'wcfm-text wcfm_ele simple variable external grouped booking toolset-google-map js-toolset-google-map', 'label_class' => 'wcfm_title', 'value' => $field_value ) ) ); ?>
												</div>
											 </div>
											 <style>
												 .wcfm_toolset_products_manage_<?php echo $field_group['slug']; ?>_container .toolset-google-map-inputs-container {
															width: 52% !important;
															float: left;
													}
												 .wcfm_toolset_products_manage_<?php echo $field_group['slug']; ?>_container .toolset-google-map-preview {
														width: 45% !important;
														height: 200px;
														float: right;
													}
											 </style>
											<?php
											wp_register_script( 'wcfm-toolset-google-map-editor-script', $WCFMu->library->js_lib_url . 'integrations/toolset/wcfmu-scripts-toolset-addon-maps-editor.js', array( 'jquery-geocomplete' ), $WCFMu->version, true );
											wp_localize_script(
													'wcfm-toolset-google-map-editor-script',
													'toolset_google_address_i10n',
													array(
																'showhidecoords'	=> __( 'Show/Hide coordinates', 'toolset-maps' ),
																				'latitude'			=> __( 'Latitude', 'toolset-maps' ),
																				'longitude'			=> __( 'Longitude', 'toolset-maps' ),
																'usethisaddress'	=> __( 'Use this address', 'toolset-maps' ),
																'closestaddress'	=> __( 'Closest address: ', 'toolset-maps' ),
																'autocompleteoff'	=> __( 'We could not connect to the Google Maps autocomplete service, but you can add an address manually.', 'wpv-views' ),
																'wcfmtoolsetmapsblockhead' => 'wcfm_products_manage_form_' . $field_group['slug'] . '_head'
																)
													);
											wp_enqueue_script('wcfm-toolset-google-map-editor-script');
										}
									}
								break;
								
								case 'numeric':
									if ( wpcf_admin_is_repetitive( $field_group_field ) ) {
										$WCFM->wcfm_fields->wcfm_generate_form_field(  array( $field_group_field['meta_key'] => array( 'label' => $field_group_field['name'], 'custom_attributes' => $custom_attributes, 'desc' => $field_group_field['description'], 'type' => 'multiinput', 'name' => 'wpcf[' . $field_group_field['meta_key'] . ']', 'label_class' => 'wcfm_title', 'value' => $field_value, 'options' => array (
																																					'field' => array( 'type' => 'number', 'custom_attributes' => $custom_attributes, 'placeholder' => $field_paceholder, 'class' => 'wcfm-text wcfm_ele wcfm_full_ele simple variable external grouped booking' )
																																				) ) ) );
									} else {
									  $WCFM->wcfm_fields->wcfm_generate_form_field(  array( $field_group_field['meta_key'] => array( 'label' => $field_group_field['name'], 'custom_attributes' => $custom_attributes, 'placeholder' => $field_paceholder, 'hints' => $field_group_field['description'], 'name' => 'wpcf[' . $field_group_field['meta_key'] . ']', 'type' => 'number', 'class' => 'wcfm-text wcfm_ele simple variable external grouped booking', 'label_class' => 'wcfm_title', 'value' => $field_value ) ) );
									}
								break;
								
								case 'wysiwyg':
								case 'textarea':
									if ( wpcf_admin_is_repetitive( $field_group_field ) ) {
										$WCFM->wcfm_fields->wcfm_generate_form_field(  array( $field_group_field['meta_key'] => array( 'label' => $field_group_field['name'], 'custom_attributes' => $custom_attributes, 'desc' => $field_group_field['description'], 'type' => 'multiinput', 'name' => 'wpcf[' . $field_group_field['meta_key'] . ']', 'label_class' => 'wcfm_title', 'value' => $field_value, 'options' => array (
																																					'field' => array( 'type' => 'textarea', 'custom_attributes' => $custom_attributes, 'placeholder' => $field_paceholder, 'class' => 'wcfm-textarea wcfm_ele wcfm_full_ele simple variable external grouped booking' )
																																				) ) ) );
									} else {
										$WCFM->wcfm_fields->wcfm_generate_form_field(  array( $field_group_field['meta_key'] => array( 'label' => $field_group_field['name'], 'custom_attributes' => $custom_attributes, 'placeholder' => $field_paceholder, 'hints' => $field_group_field['description'], 'name' => 'wpcf[' . $field_group_field['meta_key'] . ']', 'type' => 'textarea', 'class' => 'wcfm-textarea wcfm_ele simple variable external grouped booking', 'label_class' => 'wcfm_title', 'value' => $field_value ) ) );
									}
								break;
								
								case 'date':
									if ( wpcf_admin_is_repetitive( $field_group_field ) ) {
										$WCFM->wcfm_fields->wcfm_generate_form_field(  array( $field_group_field['meta_key'] => array( 'label' => $field_group_field['name'], 'custom_attributes' => $custom_attributes, 'desc' => $field_group_field['description'], 'type' => 'multiinput', 'name' => 'wpcf[' . $field_group_field['meta_key'] . ']', 'label_class' => 'wcfm_title', 'value' => $field_value, 'options' => array (
																																					'field' => array( 'type' => 'text', 'custom_attributes' => $custom_attributes, 'placeholder' => apply_filters( 'wcfm_date_filter_format', wc_date_format() ), 'class' => 'wcfm-text wcfm_ele wcfm_datepicker simple variable external grouped booking' )
																																				) ) ) );
									} else {
										if($field_value) $field_value = date( wc_date_format(), $field_value ); 
										$WCFM->wcfm_fields->wcfm_generate_form_field(  array( $field_group_field['meta_key'] => array( 'label' => $field_group_field['name'], 'custom_attributes' => $custom_attributes, 'hints' => $field_group_field['description'], 'name' => 'wpcf[' . $field_group_field['meta_key'] . ']', 'type' => 'text', 'placeholder' => apply_filters( 'wcfm_date_filter_format', wc_date_format() ), 'class' => 'wcfm-text wcfm_ele wcfm_datepicker simple variable external grouped booking', 'label_class' => 'wcfm_title', 'value' => $field_value ) ) );
									}
								break;
								
								case 'timepicker':
									if ( wpcf_admin_is_repetitive( $field_group_field ) ) {
										$WCFM->wcfm_fields->wcfm_generate_form_field(  array( $field_group_field['meta_key'] => array( 'label' => $field_group_field['name'], 'custom_attributes' => $custom_attributes, 'desc' => $field_group_field['description'], 'type' => 'multiinput', 'name' => 'wpcf[' . $field_group_field['meta_key'] . ']', 'label_class' => 'wcfm_title', 'value' => $field_value, 'options' => array (
																																					'field' => array( 'type' => 'time', 'custom_attributes' => $custom_attributes, 'class' => 'wcfm-text wcfm_ele simple variable external grouped booking' )
																																				) ) ) );
									} else {
										$WCFM->wcfm_fields->wcfm_generate_form_field(  array( $field_group_field['meta_key'] => array( 'label' => $field_group_field['name'], 'custom_attributes' => $custom_attributes, 'hints' => $field_group_field['description'], 'name' => 'wpcf[' . $field_group_field['meta_key'] . ']', 'type' => 'time', 'class' => 'wcfm-text wcfm_ele simple variable external grouped booking', 'label_class' => 'wcfm_title', 'value' => $field_value ) ) );
									}
								break;
								
								case 'colorpicker':
									if ( wpcf_admin_is_repetitive( $field_group_field ) ) {
										$WCFM->wcfm_fields->wcfm_generate_form_field(  array( $field_group_field['meta_key'] => array( 'label' => $field_group_field['name'], 'custom_attributes' => $custom_attributes, 'desc' => $field_group_field['description'], 'type' => 'multiinput', 'name' => 'wpcf[' . $field_group_field['meta_key'] . ']', 'label_class' => 'wcfm_title', 'value' => $field_value, 'options' => array (
																																					'field' => array( 'type' => 'colorpicker', 'custom_attributes' => $custom_attributes, 'placeholder' => $field_paceholder, 'class' => 'wcfm-text wcfm_ele colorpicker simple variable external grouped booking' )
																																				) ) ) );
									} else {
										$WCFM->wcfm_fields->wcfm_generate_form_field(  array( $field_group_field['meta_key'] => array( 'label' => $field_group_field['name'], 'custom_attributes' => $custom_attributes, 'placeholder' => $field_paceholder, 'hints' => $field_group_field['description'], 'name' => 'wpcf[' . $field_group_field['meta_key'] . ']', 'type' => 'colorpicker', 'class' => 'wcfm-text wcfm_ele colorpicker simple variable external grouped booking', 'label_class' => 'wcfm_title', 'value' => $field_value ) ) );
									}
								break;
								
								case 'checkbox':
									$WCFM->wcfm_fields->wcfm_generate_form_field(  array( $field_group_field['meta_key'] => array( 'label' => $field_group_field['name'], 'custom_attributes' => $custom_attributes, 'hints' => $field_group_field['description'], 'name' => 'wpcf[' . $field_group_field['meta_key'] . ']', 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele simple variable external grouped booking', 'label_class' => 'wcfm_title checkbox-title', 'value' => $field_group_field['data']['set_value'], 'dfvalue' => $field_value ) ) );
								break;
								
								case 'checkboxes':
									$checklist_opt_vals = array();
									if( !empty ( $field_group_field['data']['options'] ) ) {
										$WCFM->wcfm_fields->wcfm_generate_form_field(  array( $field_group_field['meta_key'] => array( 'label' => $field_group_field['name'], 'custom_attributes' => $custom_attributes, 'hints' => $field_group_field['description'] , 'type' => 'html', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title checkbox_title', 'value' => '' ) ) );
										foreach( $field_group_field['data']['options'] as $checklist_option_key => $checklist_option ) {
											$checklist_opt_vals = array();
											if( !empty($checklist_option) && isset( $checklist_option['set_value'] ) && isset( $checklist_option['title'] ) ) {
												$checklist_opt_vals[$checklist_option['set_value']] = $checklist_option['title'];
												$checklist_val = array();
												if( isset( $field_value[$checklist_option_key] ) ) $checklist_val = $field_value[$checklist_option_key];
												$WCFM->wcfm_fields->wcfm_generate_form_field(  array( $field_group_field['meta_key'] => array( 'label' => '', 'custom_attributes' => $custom_attributes, 'name' => 'wpcf[' . $field_group_field['meta_key'] . '][0][field]['.$checklist_option_key.']', 'type' => 'checklist', 'class' => 'wcfm-checkbox wcfm_ele', 'label_class' => 'wcfm_title checkbox_title', 'options' => $checklist_opt_vals, 'value' => $checklist_val ) ) );
											}
										}
									}
								break;
								
								case 'radio':
									$radio_opt_vals = array();
									if( !empty ( $field_group_field['data']['options'] ) ) {
										foreach( $field_group_field['data']['options'] as $radio_option ) {
											if( !empty($radio_option) && isset( $radio_option['value'] ) && isset( $radio_option['title'] ) ) {
												$radio_opt_vals[$radio_option['value']] = $radio_option['title'];
											}
										}
									}
									$WCFM->wcfm_fields->wcfm_generate_form_field(  array( $field_group_field['meta_key'] => array( 'label' => $field_group_field['name'], 'custom_attributes' => $custom_attributes, 'hints' => $field_group_field['description'] , 'name' => 'wpcf[' . $field_group_field['meta_key'] . ']', 'type' => 'radio', 'class' => 'wcfm-select wcfm_ele', 'label_class' => 'wcfm_title', 'options' => $radio_opt_vals, 'value' => $field_value ) ) );
								break;
								
								case 'select':
									$select_opt_vals = array( '' => __( '--- not set ---', 'wc-frontend-manager-ultimate' ) );
									if( !empty ( $field_group_field['data']['options'] ) ) {
										foreach( $field_group_field['data']['options'] as $select_option ) {
											if( !empty($select_option) && isset( $select_option['value'] ) && isset( $select_option['title'] ) ) {
												$select_opt_vals[$select_option['value']] = $select_option['title'];
											}
										}
									}
									$WCFM->wcfm_fields->wcfm_generate_form_field(  array( $field_group_field['meta_key'] => array( 'label' => $field_group_field['name'], 'custom_attributes' => $custom_attributes, 'hints' => $field_group_field['description'] , 'name' => 'wpcf[' . $field_group_field['meta_key'] . ']', 'type' => 'select', 'class' => 'wcfm-select wcfm_ele simple variable external grouped booking', 'label_class' => 'wcfm_title', 'options' => $select_opt_vals, 'value' => $field_value ) ) );
								break;
								
								case 'image':
									if ( wpcf_admin_is_repetitive( $field_group_field ) ) {
										$WCFM->wcfm_fields->wcfm_generate_form_field(  array( $field_group_field['meta_key'] => array( 'label' => $field_group_field['name'], 'custom_attributes' => $custom_attributes, 'desc' => $field_group_field['description'], 'type' => 'multiinput', 'name' => 'wpcf[' . $field_group_field['meta_key'] . ']', 'label_class' => 'wcfm_title', 'value' => $field_value, 'options' => array (
																																					'field' => array( 'type' => 'upload', 'custom_attributes' => $custom_attributes, 'class' => 'wcfm-text wcfm_ele simple variable external grouped booking' )
																																				) ) ) );
									} else {
										$WCFM->wcfm_fields->wcfm_generate_form_field(  array( $field_group_field['meta_key'] => array( 'label' => $field_group_field['name'], 'custom_attributes' => $custom_attributes, 'hints' => $field_group_field['description'], 'name' => 'wpcf[' . $field_group_field['meta_key'] . ']', 'type' => 'upload', 'class' => 'wcfm-text wcfm_ele simple variable external grouped booking', 'label_class' => 'wcfm_title', 'value' => $field_value ) ) );
									}
								break;
								
								case 'file':
								case 'audio':
								case 'video':
									if ( wpcf_admin_is_repetitive( $field_group_field ) ) {
										$WCFM->wcfm_fields->wcfm_generate_form_field(  array( $field_group_field['meta_key'] => array( 'label' => $field_group_field['name'], 'custom_attributes' => $custom_attributes, 'desc' => $field_group_field['description'], 'type' => 'multiinput', 'name' => 'wpcf[' . $field_group_field['meta_key'] . ']', 'label_class' => 'wcfm_title', 'value' => $field_value, 'options' => array (
																																					'field' => array( 'type' => 'upload', 'mime' => 'Uploads', 'custom_attributes' => $custom_attributes, 'class' => 'wcfm-text wcfm_ele simple variable external grouped booking' )
																																				) ) ) );
									} else {
										$WCFM->wcfm_fields->wcfm_generate_form_field(  array( $field_group_field['meta_key'] => array( 'label' => $field_group_field['name'], 'custom_attributes' => $custom_attributes, 'hints' => $field_group_field['description'], 'name' => 'wpcf[' . $field_group_field['meta_key'] . ']', 'type' => 'upload', 'mime' => 'Uploads', 'class' => 'wcfm-text wcfm_ele simple variable external grouped booking', 'label_class' => 'wcfm_title', 'value' => $field_value ) ) );
									}
								break;
							}
				  	}
				  }
				  ?>
				</div>
			</div>
			<?php
		}
	}
}


?>