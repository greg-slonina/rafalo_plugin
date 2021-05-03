<?php
/**
 * WCFM plugin views
 *
 * Plugin Toolset Types User Profile Views
 *
 * @author 		WC Lovers
 * @package 	wcfmu/views/thirdparty
 * @version   3.0.1
 */
global $wp, $WCFM, $WCFMu;

if( !apply_filters( 'wcfm_is_allow_toolset_types', true ) || !apply_filters( 'wcfm_is_allow_toolset_types_user', true ) ) {
	return;
}

include_once( WPCF_EMBEDDED_ABSPATH . '/includes/usermeta-post.php' );

if( isset( $wp->query_vars['wcfm-customers-manage'] ) ) {
	$user_id = !empty( $wp->query_vars['wcfm-customers-manage'] ) ? $wp->query_vars['wcfm-customers-manage'] : 0;
} else {
	$user_id = get_current_user_id();
}
//apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );

$user_id = get_userdata( $user_id );
if ( !is_object($user_id) ){
	$user_id = new stdClass();
	$user_id->ID = 0;
}
$current_user_roles = isset( $user_id->roles ) ? $user_id->roles : apply_filters( 'wcfm_allwoed_user_roles', array( 'vendor', 'dc_vendor', 'seller', 'customer', 'disable_vendor', 'wcfm_vendor' ) );
$current_user_roles = array_values( $current_user_roles );
$user_role = array_shift( $current_user_roles );

// Get groups
$field_groups = wpcf_admin_usermeta_get_groups_fields();
//print_r($field_groups);
//die;

if( !empty( $field_groups )) {
	foreach( $field_groups as $field_group_index => $field_group ) {
		
		// User Role Based Fields
		$for_users = wpcf_admin_get_groups_showfor_by_group($field_group['id']);
		if ( count( $for_users ) != 0 ) {
			if ( !in_array( $user_role,$for_users ) ) {
				continue;
			}
		}
				
		//If Access plugin activated
		if ( function_exists( 'wpcf_access_register_caps' ) ) {
			//If user can't view own profile fields
			if ( !current_user_can( 'view_own_in_profile_' . $field_group['slug'] ) ) {
				continue;
			}
			//If user can modify current group in own profile
			if ( !current_user_can( 'modify_own_' . $field_group['slug'] ) ) {
				continue;
			}
		}
		
		if( version_compare( TYPES_VERSION, '3.0', '>=' ) || version_compare( TYPES_VERSION, '3.0.1', '>=' ) ) {
			$field_group_load = Toolset_Field_Group_User_Factory::load( $field_group['slug'] );
		} else {
			$field_group_load = Types_Field_Group_User_Factory::load( $field_group['slug'] );
		}
    if( null === $field_group_load ) continue;
    
    $wcfm_is_allowed_toolset_field_group = apply_filters( 'wcfm_is_allow_user_toolset_field_group', true, $field_group_index, $field_group, $user_id->ID );
    if( !$wcfm_is_allowed_toolset_field_group ) continue;
    
		if ( !empty( $field_group['fields'] ) ) { 
			?>
			<div class="page_collapsible products_manage_<?php echo $field_group['id']; ?>" id="wcfm_products_manage_form_<?php echo $field_group['id']; ?>_head"><label class="wcfmfa fa-certificate"></label><?php echo $field_group['name']; ?><span></span></div>
			<div class="wcfm-container">
				<div id="wcfm_products_manage_form_<?php echo $field_group['id']; ?>_expander" class="wcfm-content">
				  <h2><?php echo $field_group['name']; ?></h2>
				  <div class="wcfm_clearfix"></div>
				  <?php
				  if ( !empty( $field_group['fields'] ) ) {
				  	foreach( $field_group['fields'] as $field_group_field ) {
				  		
				  		$wcfm_is_allowed_toolset_field = apply_filters( 'wcfm_is_allow_user_toolset_field', true, $field_group_field, $user_id->ID );
				  		if( !$wcfm_is_allowed_toolset_field ) continue;
				  		
				  		// Field Value
				  		$field_value = '';
				  		if( isset( $field_group_field['data'] ) && isset( $field_group_field['data']['user_default_value'] ) ) $field_value = $field_group_field['data']['user_default_value'];
				  		$toolset_types_filed_meta_key = apply_filters( 'wcfm_toolset_profile_field_meta', $field_group_field['meta_key'] );
				  		if( $user_id->ID ) $field_value = get_user_meta( $user_id->ID, $toolset_types_filed_meta_key, true );
				  		
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
				  			$field_value_repetatives = (array) get_user_meta( $user_id->ID, $toolset_types_filed_meta_key );
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
								case 'google_address':
									if ( wpcf_admin_is_repetitive( $field_group_field ) ) {
										$WCFM->wcfm_fields->wcfm_generate_form_field(  array( $field_group_field['meta_key'] => array( 'label' => $field_group_field['name'], 'custom_attributes' => $custom_attributes, 'desc' => $field_group_field['description'], 'type' => 'multiinput', 'name' => 'wpcf[' . $field_group_field['meta_key'] . ']', 'label_class' => 'wcfm_title', 'value' => $field_value, 'options' => array (
																																					'field' => array( 'type' => 'text', 'custom_attributes' => $custom_attributes, 'placeholder' => $field_paceholder, 'class' => 'wcfm-text wcfm_ele wcfm_full_ele simple variable external grouped booking' )
																																				) ) ) );
									} else {
										$WCFM->wcfm_fields->wcfm_generate_form_field(  array( $field_group_field['meta_key'] => array( 'label' => $field_group_field['name'], 'custom_attributes' => $custom_attributes, 'placeholder' => $field_paceholder, 'hints' => $field_group_field['description'], 'name' => 'wpcf[' . $field_group_field['meta_key'] . ']', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele simple variable external grouped booking', 'label_class' => 'wcfm_title', 'value' => $field_value ) ) );
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
																																					'field' => array( 'type' => 'time', 'custom_attributes' => $custom_attributes, 'placeholder' => $field_paceholder, 'class' => 'wcfm-text wcfm_ele simple variable external grouped booking' )
																																				) ) ) );
									} else {
										$WCFM->wcfm_fields->wcfm_generate_form_field(  array( $field_group_field['meta_key'] => array( 'label' => $field_group_field['name'], 'custom_attributes' => $custom_attributes, 'placeholder' => $field_paceholder, 'hints' => $field_group_field['description'], 'name' => 'wpcf[' . $field_group_field['meta_key'] . ']', 'type' => 'time', 'class' => 'wcfm-text wcfm_ele simple variable external grouped booking', 'label_class' => 'wcfm_title', 'value' => $field_value ) ) );
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
																																					'field' => array( 'type' => 'upload', 'custom_attributes' => $custom_attributes, 'mime' => 'Uploads', 'class' => 'wcfm-text wcfm_ele simple variable external grouped booking' )
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