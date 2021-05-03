<?php
/**
 * WCFM plugin views
 *
 * Plugin Toolset Types Products Type wise settings Views
 *
 * @author 		WC Lovers
 * @package 	wcfmu/views/thirdparty
 * @version   3.1.7
 */
global $wp, $WCFM, $WCFMu;

if( !$wcfm_allow_toolset = apply_filters( 'wcfm_is_allow_toolset_types', true ) ) {
	return;
}

include_once( WPCF_EMBEDDED_ABSPATH . '/includes/fields-post.php' );
$product_post = get_post();
$product_post->post_type = 'product';
$field_groups = wpcf_admin_post_get_post_groups_fields( $product_post );
$field_group_lists = array();

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
    
    $wcfm_is_allowed_toolset_field_group = apply_filters( 'wcfm_is_allow_setting_toolset_field_group', true, $field_group_index, $field_group );
    if( !$wcfm_is_allowed_toolset_field_group ) continue;
    
		if ( !empty( $field_group['fields'] ) ) {
			$field_group_lists[$field_group['id']] = $field_group['name'];
		}
	}
}

$wcfm_product_type_toolset_fields = (array) get_option( 'wcfm_product_type_toolset_fields' );
?>
<!-- collapsible -->
<div class="page_collapsible" id="wcfm_toolset_settings_form_pages_head">
	<label class="wcfmfa fa-wrench"></label>
	<?php _e('Product Type Toolset Fields', 'wc-frontend-manager-ultimate'); ?><span></span>
</div>
<div class="wcfm-container">
	<div id="wcfm_toolset_settings_form_pages_expander" class="wcfm-content">
		<?php
			$product_types = apply_filters( 'wcfm_product_types', array( 'simple' => __( 'Simple Product', 'wc-frontend-manager' ), 'variable' => __( 'Variable Product', 'wc-frontend-manager' ), 'grouped' => __( 'Grouped Product', 'wc-frontend-manager' ), 'external' => __( 'External/Affiliate Product', 'wc-frontend-manager' ) ) );
			
			if( !empty( $product_types ) ) {
				foreach( $product_types as $product_type => $product_type_label ) {
					$product_type_filed_groups = isset( $wcfm_product_type_toolset_fields[$product_type] ) ? $wcfm_product_type_toolset_fields[$product_type] : array();
				?>
				<p class="wcfm_title catlimit_title"><strong><?php echo $product_type_label . ' '; _e( 'Field Groups', 'wc-frontend-manager' ); ?></strong></p><label class="screen-reader-text" for="product_type_toolset_fields"><?php echo $product_type_label . ' '; _e( 'Field Groups', 'wc-frontend-manager' ); ?></label>
				<select id="wcfm_product_type_toolset_fields_<?php echo $product_type; ?>" name="wcfm_product_type_toolset_fields[<?php echo $product_type; ?>][]" class="wcfm-select wcfm_ele wcfm_product_type_toolset_fields" multiple="multiple" data-catlimit="-1" style="width: 60%; margin-bottom: 10px;">
					<?php
						if ( $field_group_lists ) {
							foreach( $field_group_lists as $field_group_list_slug => $field_group_list_name ) {
								?>
								<option value="<?php echo $field_group_list_slug; ?>" <?php if( in_array( $field_group_list_slug, $product_type_filed_groups ) ) { echo 'selected="selected"'; } ?>><?php echo $field_group_list_name; ?></option>
								<?php
							}
						}
					?>
				</select>
				<?php
				}
			}
		?>
		<p class="description"><?php _e( 'Create group of your Store Toolset Types Fields as per Product Types. Product Manager will work according to that.', 'wc-frontend-manager-ultimate' ); ?></p>
	</div>
</div>
<div class="wcfm_clearfix"></div>
<!-- end collapsible -->