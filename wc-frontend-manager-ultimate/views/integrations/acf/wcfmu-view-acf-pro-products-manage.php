<?php
/**
 * WCFM plugin views
 *
 * Plugin Advanced Custom Fields(ACF) Pro Products Manage Views
 *
 * @author 		WC Lovers
 * @package 	wcfmu/views/thirdparty
 * @version   3.3.7
 */
global $wp, $WCFM, $WCFMu;

if( !$wcfm_allow_acf_fields = apply_filters( 'wcfm_is_allow_acf_fields', true ) ) {
	return;
}

$product_id = 0;
$cg_product_id = 1;

if( isset( $wp->query_vars['wcfm-products-manage'] ) && !empty( $wp->query_vars['wcfm-products-manage'] ) ) {
	$product_id = $wp->query_vars['wcfm-products-manage'];
}

$field_groups = acf_get_field_groups();
//print_r($field_groups);
//die;

$filter = array( 
	'post_id'	=> $cg_product_id, 
	'post_type'	=> 'product' 
);
$product_group_ids = array();
//print_r($product_group_ids);
//die;

// Getting Product Category specific field groups

$cat_group_id_map = array(); 
$cat_group_ids = array();
$product_categories   = get_terms( 'product_cat', 'orderby=name&hide_empty=0&parent=0' );
if( !empty( $field_groups )) {
	foreach( $field_groups as $field_group_index => $field_group ) {
		if( !$field_group['active'] ) continue;
		if( empty($field_group['location']) ) continue;
		
		foreach( $field_group['location'] as $group_id => $group ) {
			if( empty($group) ) continue;
			
			foreach( $group as $rule_id => $rule ) {
				switch($rule['param']) {
					case 'post_type' :
						if( ( $rule['operator'] == '==' ) && ( $rule['value'] == 'product' ) ) {
							$product_group_ids[$field_group['ID']] = $field_group['ID'];
						}
					break;
					
					case 'post_taxonomy' :
						if( !empty( $product_categories ) ) {
							foreach ( $product_categories as $cat ) {
								if( ( $rule['operator'] == '==' ) && ( $rule['value'] == 'product_cat:'.$cat->slug ) ) {
									$cat_group_id_map[$cat->term_id][$field_group['ID']] = $field_group['ID'];
									$cat_group_ids = array_merge( $cat_group_ids, $cat_group_id_map[$cat->term_id] );
									unset( $product_group_ids[$field_group['ID']] );
								}
								
								// Level 1
								$product_child_categories   = get_terms( 'product_cat', 'orderby=name&hide_empty=0&parent=' . absint( $cat->term_id ) );
								if ( $product_child_categories ) {
									foreach ( $product_child_categories as $child_cat ) {
										if( ( $rule['operator'] == '==' ) && ( $rule['value'] == 'product_cat:'.$child_cat->slug ) ) {
											$cat_group_id_map[$child_cat->term_id][$field_group['ID']] = $field_group['ID'];
											$cat_group_ids = array_merge( $cat_group_ids, $cat_group_id_map[$child_cat->term_id] );
											unset( $product_group_ids[$field_group['ID']] );
										} else {
											
											// Level 2
											$product_child_categories2   = get_terms( 'product_cat', 'orderby=name&hide_empty=0&parent=' . absint( $child_cat->term_id ) );
											if ( $product_child_categories2 ) {
												foreach ( $product_child_categories2 as $child_cat2 ) {
													if( ( $rule['operator'] == '==' ) && ( $rule['value'] == 'product_cat:'.$child_cat2->slug ) ) {
														$cat_group_id_map[$child_cat2->term_id][$field_group['ID']] = $field_group['ID'];
														$cat_group_ids = array_merge( $cat_group_ids, $cat_group_id_map[$child_cat2->term_id] );
														unset( $product_group_ids[$field_group['ID']] );
													} else {
														
														// Level 3
														$product_child_categories3   = get_terms( 'product_cat', 'orderby=name&hide_empty=0&parent=' . absint( $child_cat2->term_id ) );
														if ( $product_child_categories3 ) {
															foreach ( $product_child_categories3 as $child_cat3 ) {
																if( ( $rule['operator'] == '==' ) && ( $rule['value'] == 'product_cat:'.$child_cat3->slug ) ) {
																	$cat_group_id_map[$child_cat3->term_id][$field_group['ID']] = $field_group['ID'];
																	$cat_group_ids = array_merge( $cat_group_ids, $cat_group_id_map[$child_cat3->term_id] );
																	unset( $product_group_ids[$field_group['ID']] );
																} else {
																	
																	// Level 4
																	$product_child_categories4   = get_terms( 'product_cat', 'orderby=name&hide_empty=0&parent=' . absint( $child_cat3->term_id ) );
																	if ( $product_child_categories4 ) {
																		foreach ( $product_child_categories4 as $child_cat4 ) {
																			if( ( $rule['operator'] == '==' ) && ( $rule['value'] == 'product_cat:'.$child_cat4->slug ) ) {
																				$cat_group_id_map[$child_cat4->term_id][$field_group['ID']] = $field_group['ID'];
																				$cat_group_ids = array_merge( $cat_group_ids, $cat_group_id_map[$child_cat4->term_id] );
																				unset( $product_group_ids[$field_group['ID']] );
																			}
																		}
																	}
																}
															}
														}
													}
												}
											}
										}
									}
								}
							}
						}
					break;
					
					default:
					  //continue;
					 break;
				}
			}
		}
	}
}
		
wp_localize_script( 'wcfmu_acf_pro_products_manage_js', 'wcfm_cat_based_acf_pro_fields', $cat_group_id_map );
$cat_group_ids = array_unique($cat_group_ids);
//print_r($cat_group_ids);
//print_r($cat_group_id_map);
//die;

$process_field_groups = array();

if( !empty( $field_groups )) {
	foreach( $field_groups as $field_group_index => $field_group ) {
		
		if( !in_array( $field_group['ID'], $product_group_ids ) && !in_array( $field_group['ID'], $cat_group_ids ) ) continue;
		if( in_array( $field_group['ID'], $process_field_groups ) ) continue;
		$process_field_groups[$field_group['ID']] = $field_group['ID'];
		
		$cat_group_class = '';
		if( in_array( $field_group['ID'], $cat_group_ids ) ) $cat_group_class = 'wcfm_cat_based_acf_pro_product_manager_fields';
		if( in_array( $field_group['ID'], $product_group_ids ) ) $cat_group_class = '';
    
    $wcfm_is_allowed_acf_field_group = apply_filters( 'wcfm_is_allowed_acf_field_group', true, $field_group['ID'] );
    if( !$wcfm_is_allowed_acf_field_group ) continue;
    
    $field_group_fields = acf_get_fields( $field_group );
    //print_r($field_group_fields);
    
		if ( !empty( $field_group_fields ) ) { 
			?>
			<div class="page_collapsible wcfm_acf_products_manage_<?php echo $field_group['ID']; ?>_collapsible <?php echo $cat_group_class; ?> simple variable external grouped booking" id="wcfm_products_manage_form_<?php echo sanitize_title($field_group['title']); ?>_head"><label class="wcfmfa fa-certificate"></label><?php echo $field_group['title']; ?><span></span></div>
			<div class="wcfm-container wcfm_acf_products_manage_<?php echo $field_group['ID']; ?>_container <?php echo $cat_group_class; ?> simple variable external grouped booking">
				<div id="wcfm_products_manage_form_<?php echo sanitize_title($field_group['title']); ?>_expander" class="wcfm-content">
				  <h2><?php echo $field_group['title']; ?></h2>
				  <div class="wcfm_clearfix"></div>
				  <?php
				  if ( !empty( $field_group_fields ) ) {
				  	foreach( $field_group_fields as $field_group_field ) {
				  		if( ( $field_group_field['type'] != 'message' ) && ( !isset( $field_group_field['name'] ) || empty( $field_group_field['name'] ) ) ) continue;
				  		$field_value = '';
				  		if( isset( $field_group_field['default_value'] ) ) {
				  			$field_value = $field_group_field['default_value'];
				  			if( ( !isset( $field_group_field['multiple'] ) || ( $field_group_field['multiple'] == 0 ) ) && is_array( $field_group_field['default_value'] ) && isset( $field_group_field['default_value'][0] ) ) {
				  				$field_value = $field_group_field['default_value'][0];
				  			}
				  		}
				  		if( $product_id ) $field_value = get_post_meta( $product_id, $field_group_field['name'], true );
				  		
				  		// Is Required
				  		$custom_attributes = array();
				  		if( isset( $field_group_field['required'] ) && $field_group_field['required'] ) $custom_attributes = array( 'required' => 1 );
				  		
				  		// Conditional Logic
				  		if( !empty($field_group_field['conditional_logic']) ) {
								$custom_attributes['conditions'] = json_encode( $field_group_field['conditional_logic'] );
							}
				  		
				  		// Hidden ACF Key field
				  		$WCFM->wcfm_fields->wcfm_generate_form_field(  array( $field_group_field['key'] => array( 'name' => 'acf[_' . $field_group_field['name'] . ']', 'type' => 'hidden', 'value' => $field_group_field['key'] ) ) );
				  		
				  		switch( $field_group_field['type'] ) {
				  			case 'email':
								case 'text':
									$WCFM->wcfm_fields->wcfm_generate_form_field(  array( $field_group_field['name'] => array( 'label' => $field_group_field['label'], 'custom_attributes' => $custom_attributes, 'placeholder' => $field_group_field['placeholder'], 'hints' => $field_group_field['instructions'], 'name' => 'acf[' . $field_group_field['name'] . ']', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele simple variable external grouped booking ' . $field_group_field['key'], 'label_class' => 'wcfm_title ' . $field_group_field['key'], 'value' => $field_value ) ) );
								break;
								
								case 'number':
									$WCFM->wcfm_fields->wcfm_generate_form_field(  array( $field_group_field['name'] => array( 'label' => $field_group_field['label'], 'custom_attributes' => $custom_attributes, 'placeholder' => $field_group_field['placeholder'], 'hints' => $field_group_field['instructions'], 'name' => 'acf[' . $field_group_field['name'] . ']', 'type' => 'number', 'attributes' => array( 'min' => $field_group_field['min'], 'max' => $field_group_field['max'], 'step' => $field_group_field['step'] ), 'class' => 'wcfm-text wcfm_ele simple variable external grouped booking ' . $field_group_field['key'], 'label_class' => 'wcfm_title ' . $field_group_field['key'], 'value' => $field_value ) ) );
								break;
								
								case 'wysiwyg':
									$rich_editor = apply_filters( 'wcfm_is_allow_rich_editor', 'rich_editor' );
									$wpeditor = apply_filters( 'wcfm_is_allow_product_wpeditor', 'wpeditor' );
									if( $wpeditor && $rich_editor ) {
										$rich_editor = 'wcfm_wpeditor';
									} else {
										$wpeditor = 'textarea';
									}
									$WCFM->wcfm_fields->wcfm_generate_form_field(  array( $field_group_field['name'] => array( 'label' => $field_group_field['label'], 'custom_attributes' => $custom_attributes, 'placeholder' => isset( $field_group_field['placeholder'] ) ? $field_group_field['placeholder'] : '', 'hints' => $field_group_field['instructions'], 'name' => 'acf[' . $field_group_field['name'] . ']', 'type' => $wpeditor, 'class' => 'wcfm-textarea wcfm_ele simple variable external grouped booking wcfm_custom_field_editor ' . $rich_editor . ' ' . $field_group_field['key'], 'label_class' => 'wcfm_title ' . $field_group_field['key'], 'value' => $field_value, 'teeny' => true ) ) );
								break;
								
								case 'textarea':
									$WCFM->wcfm_fields->wcfm_generate_form_field(  array( $field_group_field['name'] => array( 'label' => $field_group_field['label'], 'custom_attributes' => $custom_attributes, 'placeholder' => isset( $field_group_field['placeholder'] ) ? $field_group_field['placeholder'] : '', 'hints' => $field_group_field['instructions'], 'name' => 'acf[' . $field_group_field['name'] . ']', 'type' => 'textarea', 'class' => 'wcfm-textarea wcfm_ele simple variable external grouped booking ' . $field_group_field['key'], 'label_class' => 'wcfm_title ' . $field_group_field['key'], 'value' => $field_value ) ) );
								break;
								
								case 'date_picker':
									$custom_attributes['date_format'] = wcfm_wp_date_format_to_js( $field_group_field['return_format'] );
									if( $field_value ) $field_value = date( $field_group_field['return_format'], strtotime( $field_value ) );
									$WCFM->wcfm_fields->wcfm_generate_form_field(  array( $field_group_field['name'] => array( 'label' => $field_group_field['label'], 'custom_attributes' => $custom_attributes, 'placeholder' => $field_group_field['return_format'], 'hints' => $field_group_field['instructions'], 'name' => 'acf[' . $field_group_field['name'] . ']', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele wcfm_datepicker simple variable external grouped booking ' . $field_group_field['key'], 'label_class' => 'wcfm_title ' . $field_group_field['key'], 'value' => $field_value ) ) );
								break;
								
								case 'date_time_picker':
									//$custom_attributes['date_format'] = wcfm_wp_date_format_to_js( $field_group_field['return_format'] );
									if( $field_value ) $field_value = date( $field_group_field['return_format'], strtotime( $field_value ) );
									$WCFM->wcfm_fields->wcfm_generate_form_field(  array( $field_group_field['name'] => array( 'label' => $field_group_field['label'], 'custom_attributes' => $custom_attributes, 'placeholder' => $field_group_field['return_format'], 'hints' => $field_group_field['instructions'], 'name' => 'acf[' . $field_group_field['name'] . ']', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele wcfm_datetimepicker simple variable external grouped booking ' . $field_group_field['key'], 'label_class' => 'wcfm_title ' . $field_group_field['key'], 'value' => $field_value ) ) );
								break;
								
								case 'time_picker':
									$custom_attributes['date_format'] = $field_group_field['return_format'];
									$WCFM->wcfm_fields->wcfm_generate_form_field(  array( $field_group_field['name'] => array( 'label' => $field_group_field['label'], 'custom_attributes' => $custom_attributes, 'placeholder' => $field_group_field['return_format'], 'hints' => $field_group_field['instructions'], 'name' => 'acf[' . $field_group_field['name'] . ']', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele wcfm_timepicker simple variable external grouped booking ' . $field_group_field['key'], 'label_class' => 'wcfm_title ' . $field_group_field['key'], 'value' => $field_value ) ) );
								break;
								
								case 'true_false':
									$WCFM->wcfm_fields->wcfm_generate_form_field(  array( $field_group_field['name'] => array( 'label' => $field_group_field['label'], 'custom_attributes' => $custom_attributes, 'hints' => $field_group_field['instructions'], 'name' => 'acf[' . $field_group_field['name'] . ']', 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele simple variable external grouped booking ' . $field_group_field['key'], 'label_class' => 'wcfm_title checkbox-title ' . $field_group_field['key'], 'value' => '1', 'dfvalue' => $field_value ) ) );
								break;
								
								case 'checkbox':
									if( $field_value && !is_array( $field_value ) ) $field_value = array($field_value);
									$WCFM->wcfm_fields->wcfm_generate_form_field(  array( $field_group_field['name'] => array( 'label' => $field_group_field['label'], 'custom_attributes' => $custom_attributes, 'hints' => $field_group_field['instructions'] , 'name' => 'acf[' . $field_group_field['name'] . ']', 'type' => 'checklist', 'class' => 'wcfm-checkbox wcfm_ele simple variable external grouped booking ' . $field_group_field['key'], 'label_class' => 'wcfm_title ' . $field_group_field['key'], 'options' => $field_group_field['choices'], 'value' => $field_value ) ) );
							  break;
							  
								case 'radio':
									$WCFM->wcfm_fields->wcfm_generate_form_field(  array( $field_group_field['name'] => array( 'label' => $field_group_field['label'], 'custom_attributes' => $custom_attributes, 'hints' => $field_group_field['instructions'] , 'name' => 'acf[' . $field_group_field['name'] . ']', 'type' => 'radio', 'class' => 'wcfm-radio wcfm_ele simple variable external grouped booking ' . $field_group_field['key'], 'label_class' => 'wcfm_title ' . $field_group_field['key'], 'options' => $field_group_field['choices'], 'value' => $field_value ) ) );
								break;
								
								case 'message':
									$WCFM->wcfm_fields->wcfm_generate_form_field(  array( $field_group_field['key'] => array( 'label' => $field_group_field['label'], 'type' => 'html', 'class' => 'wcfm-html wcfm_ele simple variable external grouped booking ' . $field_group_field['key'], 'label_class' => 'wcfm_title ' . $field_group_field['key'], 'value' => wpautop( $field_group_field['message'] ) ) ) );
								break;
								
								case 'select':
									if( isset( $field_group_field['multiple'] ) && ( $field_group_field['multiple'] == 1 ) ) {
										$WCFM->wcfm_fields->wcfm_generate_form_field(  array( $field_group_field['name'] => array( 'label' => $field_group_field['label'], 'custom_attributes' => $custom_attributes, 'hints' => $field_group_field['instructions'] , 'name' => 'acf[' . $field_group_field['name'] . ']', 'type' => 'select', 'class' => 'wcfm-select wcfm-acf-pro-multi-select wcfm_ele simple variable external grouped booking ' . $field_group_field['key'], 'label_class' => 'wcfm_title ' . $field_group_field['key'], 'attributes' => array( 'multiple' => 'multiple', 'style' => 'width: 60%;' ), 'options' => $field_group_field['choices'], 'value' => $field_value ) ) );
									} else {
										$field_group_field['choices'] = array( '' => __( '-Select-', 'wc-frontend-manager-ultimate' ) ) + (array) $field_group_field['choices'];
										$WCFM->wcfm_fields->wcfm_generate_form_field(  array( $field_group_field['name'] => array( 'label' => $field_group_field['label'], 'custom_attributes' => $custom_attributes, 'hints' => $field_group_field['instructions'] , 'name' => 'acf[' . $field_group_field['name'] . ']', 'type' => 'select', 'class' => 'wcfm-select wcfm_ele simple variable external grouped booking ' . $field_group_field['key'], 'label_class' => 'wcfm_title ' . $field_group_field['key'], 'options' => $field_group_field['choices'], 'value' => $field_value ) ) );
									}
								break;
								
								case 'image':
									//if( $product_id && $field_value ) $field_value = wp_get_attachment_url( $field_value );
									$WCFM->wcfm_fields->wcfm_generate_form_field(  array( $field_group_field['name'] => array( 'label' => $field_group_field['label'], 'custom_attributes' => $custom_attributes, 'hints' => $field_group_field['instructions'], 'name' => 'acf[' . $field_group_field['name'] . ']', 'type' => 'upload', 'class' => 'wcfm_ele simple variable external grouped booking ' . $field_group_field['key'], 'label_class' => 'wcfm_title ' . $field_group_field['key'], 'value' => $field_value ) ) );
								break;
								
								case 'file':
									//if( $product_id && $field_value ) $field_value = wp_get_attachment_url( $field_value );
									$WCFM->wcfm_fields->wcfm_generate_form_field(  array( $field_group_field['name'] => array( 'label' => $field_group_field['label'], 'custom_attributes' => $custom_attributes, 'hints' => $field_group_field['instructions'], 'name' => 'acf[' . $field_group_field['name'] . ']', 'type' => 'upload', 'mime' => 'Uploads', 'class' => 'wcfm_ele simple variable external grouped booking ' . $field_group_field['key'], 'label_class' => 'wcfm_title ' . $field_group_field['key'], 'value' => $field_value ) ) );
								break;
								
								case 'user':
									$args = array(
										'role__in'     => apply_filters( 'wcfm_allwoed_user_roles', array( 'dc_vendor', 'vendor', 'seller', 'disable_vendor' ) ),
										'orderby'      => 'login',
										'order'        => 'ASC',
										//'include'      => $wcfm_allow_vendors_list,
										'count_total'  => false,
										'fields'       => array( 'ID', 'user_login', 'display_name' )
									 ); 
									if( isset($field_group_field['role']) && !empty( $field_group_field['role'] ) ) {
										$args['role__in'] = array_values( $field_group_field['role'] );
									}
									$all_users = get_users( $args );
									$user_arr = array();
									if( !isset( $field_group_field['multiple'] ) || ( $field_group_field['multiple'] != 1 ) ) {
										$user_arr[] = __( '-Select-', 'wc-frontend-manager-ultimate' );
									}
									if( !empty( $all_users ) ) {
										foreach( $all_users as $all_user ) {
											$user_arr[$all_user->ID] = $all_user->user_login . ' (' . $all_user->display_name . ')';
										}
									}
									if( isset( $field_group_field['multiple'] ) && ( $field_group_field['multiple'] == 1 ) ) {
										$WCFM->wcfm_fields->wcfm_generate_form_field(  array( $field_group_field['name'] => array( 'label' => $field_group_field['label'], 'custom_attributes' => $custom_attributes, 'hints' => $field_group_field['instructions'] , 'name' => 'acf[' . $field_group_field['name'] . ']', 'type' => 'select', 'class' => 'wcfm-select wcfm-acf-pro-multi-select wcfm_ele simple variable external grouped booking ' . $field_group_field['key'], 'label_class' => 'wcfm_title ' . $field_group_field['key'], 'attributes' => array( 'multiple' => 'multiple', 'style' => 'width: 60%;' ), 'options' => $user_arr, 'value' => $field_value ) ) );
									} else {
										$WCFM->wcfm_fields->wcfm_generate_form_field(  array( $field_group_field['name'] => array( 'label' => $field_group_field['label'], 'custom_attributes' => $custom_attributes, 'hints' => $field_group_field['instructions'] , 'name' => 'acf[' . $field_group_field['name'] . ']', 'type' => 'select', 'class' => 'wcfm-select wcfm_ele simple variable external grouped booking ' . $field_group_field['key'], 'label_class' => 'wcfm_title ' . $field_group_field['key'], 'options' => $user_arr, 'value' => $field_value ) ) );
									}
								break;
								
								case 'google_map':
									$acf_map_key = apply_filters( 'acf/fields/google_map/api', '' );
									
									if( !$acf_map_key ) {
										$acf_map_key = acf_get_setting( 'google_api_key' );
									}
									
									if ( $acf_map_key ) {
										$map_address = isset( $field_value['address'] ) ? $field_value['address'] : '';
										$map_lat     = isset( $field_value['lat'] ) ? $field_value['lat'] : '';
										$map_lng     = isset( $field_value['lng'] ) ? $field_value['lng'] : '';
										$WCFM->wcfm_fields->wcfm_generate_form_field( array(
																																				$field_group_field['name']."_address" => array( 'label' => __( 'Find Location', 'wc-frontend-manager' ), 'placeholder' => __( 'Type an address to find', 'wc-frontend-manager' ), 'type' => 'text', 'class' => 'wcfm-text wcfm_acf_map_location wcfm_ele simple variable external grouped booking ' . $field_group_field['key'], 'label_class' => 'wcfm_title wcfm_ele simple variable external grouped booking ' . $field_group_field['key'], 'name' => 'acf[' . $field_group_field['name'] . '][address]', 'value' => $map_address ),
																																				$field_group_field['name']."_lat" => array( 'type' => 'hidden', 'name' => 'acf[' . $field_group_field['name'] . '][lat]', 'value' => $map_lat, 'class' => 'wcfm_acf_map_lat' ),
																																				$field_group_field['name']."_lng" => array( 'type' => 'hidden', 'name' => 'acf[' . $field_group_field['name'] . '][lng]', 'value' => $map_lng, 'class' => 'wcfm_acf_map_lng' ),
																																				$field_group_field['name']."_map" => array( 'type' => 'html', 'value' => '<div id="' . $field_group_field['name']. '_location_map" class="wcfm_acf_map"></div><div class="wcfm_clearfix" style="margin-bottom:15px;"></div>', 'class' => $field_group_field['key'] ),
																																				) );
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