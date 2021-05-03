<?php
/**
 * WCFMu plugin views
 *
 * Plugin Toolset Types Taxonomy Views
 *
 * @author 		WC Lovers
 * @package 	wcfmu/views/thirdparty
 * @version   3.0.2
 */
global $wp, $WCFM, $WCFMu;

if( !$wcfm_allow_toolset = apply_filters( 'wcfm_is_allow_toolset_types', true ) ) {
	return;
}

if( version_compare( TYPES_VERSION, '3.0', '>=' ) || version_compare( TYPES_VERSION, '3.0.1', '>=' ) ) {
	$factory = Toolset_Field_Group_Term_Factory::get_instance();
} else {
	$factory = Types_Field_Group_Term_Factory::get_instance();
}
$field_groups = $factory->get_groups_by_taxonomy( WC_PRODUCT_VENDORS_TAXONOMY );

if( !empty( $field_groups )) {
	foreach( $field_groups as $field_group_index => $field_group ) {
		$field_definitions = $field_group->get_field_definitions();
		
		if ( !empty( $field_definitions ) ) { 
			?>
			<div class="page_collapsible products_manage_<?php echo sanitize_title($field_group->get_display_name()); ?>" id="wcfm_products_manage_form_<?php echo sanitize_title($field_group->get_display_name()); ?>_head"><label class="wcfmfa fa-certificate"></label><?php echo $field_group->get_display_name(); ?><span></span></div>
			<div class="wcfm-container">
				<div id="wcfm_products_manage_form_<?php echo sanitize_title($field_group->get_display_name()); ?>_expander" class="wcfm-content">
				  <h2><?php echo $field_group->get_display_name(); ?></h2>
				  <div class="wcfm_clearfix"></div>
					<?php
					if ( !empty( $field_definitions ) ) {
						foreach( $field_definitions as $field_definition ) {
							$toolset_field = new WPCF_Field_Instance_Term( $field_definition, WC_Product_Vendors_Utils::get_logged_in_vendor() );
							$field_group_field = wptoolset_form_filter_types_field( $toolset_field->get_definition()->get_definition_array(), $toolset_field->get_object_id() );
							//print_r($field_group_field);
							$field_value = $toolset_field->get_value();
							if( is_array( $field_value ) && isset( $field_value[0] ) ) $field_value = $field_value[0];
							else $field_value = $field_group_field['user_default_value'];
							switch( $field_group_field['type'] ) {
								case 'url':
								case 'phone':
								case 'textfield':
								case 'google_address':
									$WCFM->wcfm_fields->wcfm_generate_form_field(   array( $field_group_field['meta_key'] => array( 'label' => $field_definition->get_display_name(), 'hints' => $field_group_field['description'], 'name' => 'wpcf[' . $field_group_field['meta_key'] . ']', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title', 'value' => $field_value ) ) );
								break;
								
								case 'numeric':
									$WCFM->wcfm_fields->wcfm_generate_form_field(   array( $field_group_field['meta_key'] => array( 'label' => $field_definition->get_display_name(), 'hints' => $field_group_field['description'], 'name' => 'wpcf[' . $field_group_field['meta_key'] . ']', 'type' => 'number', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title', 'value' => $field_value ) ) );
								break;
								
								case 'wysiwyg':
								case 'textarea':
									$WCFM->wcfm_fields->wcfm_generate_form_field(   array( $field_group_field['meta_key'] => array( 'label' => $field_definition->get_display_name(), 'hints' => $field_group_field['description'], 'name' => 'wpcf[' . $field_group_field['meta_key'] . ']', 'type' => 'textarea', 'class' => 'wcfm-textarea wcfm_ele', 'label_class' => 'wcfm_title', 'value' => $field_value ) ) );
								break;
								
								case 'date':
									if($field_value) $field_value = date( wc_date_format(), $field_value ); 
									$WCFM->wcfm_fields->wcfm_generate_form_field(   array( $field_group_field['meta_key'] => array( 'label' => $field_definition->get_display_name(), 'hints' => $field_group_field['description'], 'name' => 'wpcf[' . $field_group_field['meta_key'] . ']', 'type' => 'text', 'placeholder' => apply_filters( 'wcfm_date_filter_format', wc_date_format() ), 'class' => 'wcfm-text wcfm_ele dc_datepicker', 'label_class' => 'wcfm_title', 'value' => $field_value ) ) );
								break;
								
								case 'timepicker':
									$WCFM->wcfm_fields->wcfm_generate_form_field(   array( $field_group_field['meta_key'] => array( 'label' => $field_definition->get_display_name(), 'hints' => $field_group_field['description'], 'name' => 'wpcf[' . $field_group_field['meta_key'] . ']', 'type' => 'time', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title', 'value' => $field_value ) ) );
								break;
								
								case 'checkbox':
									$WCFM->wcfm_fields->wcfm_generate_form_field(   array( $field_group_field['meta_key'] => array( 'label' => $field_definition->get_display_name(), 'hints' => $field_group_field['description'], 'name' => 'wpcf[' . $field_group_field['meta_key'] . ']', 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'label_class' => 'wcfm_title checkbox_title', 'value' => $field_group_field['data']['set_value'], 'dfvalue' => $field_value ) ) );
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
									$WCFM->wcfm_fields->wcfm_generate_form_field(   array( $field_group_field['meta_key'] => array( 'label' => $field_group_field['name'], 'hints' => $field_group_field['description'] , 'name' => 'wpcf[' . $field_group_field['meta_key'] . ']', 'type' => 'radio', 'class' => 'wcfm-select wcfm_ele', 'label_class' => 'wcfm_title', 'options' => $radio_opt_vals, 'value' => $field_value ) ) );
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
									$WCFM->wcfm_fields->wcfm_generate_form_field(   array( $field_group_field['meta_key'] => array( 'label' => $field_group_field['name'], 'hints' => $field_group_field['description'] , 'name' => 'wpcf[' . $field_group_field['meta_key'] . ']', 'type' => 'select', 'class' => 'wcfm-select wcfm_ele', 'label_class' => 'wcfm_title', 'options' => $select_opt_vals, 'value' => $field_value ) ) );
								break;
								
								case 'image':
									$WCFM->wcfm_fields->wcfm_generate_form_field(  array( $field_group_field['meta_key'] => array( 'label' => $field_group_field['name'], 'hints' => $field_group_field['description'], 'name' => 'wpcf[' . $field_group_field['meta_key'] . ']', 'type' => 'upload', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title', 'value' => $field_value ) ) );
								break;
								
								case 'file':
								case 'audio':
								case 'video':
									$WCFM->wcfm_fields->wcfm_generate_form_field(  array( $field_group_field['meta_key'] => array( 'label' => $field_group_field['name'], 'hints' => $field_group_field['description'], 'name' => 'wpcf[' . $field_group_field['meta_key'] . ']', 'type' => 'upload', 'mime' => 'Uploads', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title', 'value' => $field_value ) ) );
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