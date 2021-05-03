<?php
/**
 * WCFM plugin controllers
 *
 * Plugin Toolset Types User Profile Controller
 *
 * @author 		WC Lovers
 * @package 	wcfmu/controllers/thirdparty
 * @version   3.0.1
 */

class WCFMu_Toolset_Types_User_Profile_Controller {
	
	public function __construct() {
		global $WCFM;
		
		// Third Party Product Meta Data Save
    add_action( 'wcfm_profile_update', array( &$this, 'wcfm_toolset_types_user_profile_meta_save' ), 150, 2 );
    add_action( 'wcfm_customers_manage', array( &$this, 'wcfm_toolset_types_user_profile_meta_save' ), 150, 2 );
	}
	
	/**
	 * Toolset Field User Meta data save
	 */
	function wcfm_toolset_types_user_profile_meta_save( $user_id, $wcfm_profile_form ) {
		global $WCFM;
		
		if( isset( $wcfm_profile_form['wpcf'] ) && ! empty( $wcfm_profile_form['wpcf'] ) ) {
			foreach( $wcfm_profile_form['wpcf'] as $toolset_types_filed_key => $toolset_types_filed_value ) {
				$toolset_types_filed_meta_key = apply_filters( 'wcfm_toolset_profile_field_meta', $toolset_types_filed_key );
				update_user_meta( $user_id, $toolset_types_filed_meta_key, $toolset_types_filed_value );
				if( is_array( $toolset_types_filed_value ) ) {
					delete_user_meta( $user_id, $toolset_types_filed_meta_key );
					foreach( $toolset_types_filed_value as $toolset_types_filed_value_field ) {
						if( isset( $toolset_types_filed_value_field['field'] ) ) {
							add_user_meta( $user_id, $toolset_types_filed_meta_key, $toolset_types_filed_value_field['field'] );
						}
					}
				}
			}
			
			do_action( 'wcfm_toolset_profile_field_save', $user_id, $wcfm_profile_form['wpcf'] );
			
			
			include_once( WPCF_EMBEDDED_ABSPATH . '/includes/usermeta-post.php' );
			$user_id = get_userdata( $user_id );
			if ( !is_object($user_id) ){
				$user_id = new stdClass();
				$user_id->ID = 0;
			}
			$current_user_roles = isset( $user_id->roles ) ? $user_id->roles : apply_filters( 'wcfm_allwoed_user_roles', array( 'vendor', 'dc_vendor', 'seller', 'customer', 'disable_vendor', 'wcfm_vendor' ) );
			$current_user_roles = array_values( $current_user_roles );
			$user_role = array_shift( $current_user_roles );
			
			
			$field_groups = wpcf_admin_usermeta_get_groups_fields();
			
			$vendor_id   = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );
			
			if( !empty( $field_groups )) {
				foreach( $field_groups as $field_group_index => $field_group ) {
					
					// User Role Based Fields
					$for_users = wpcf_admin_get_groups_showfor_by_group($field_group['id']);
					if ( count( $for_users ) != 0 ) {
						if ( !in_array( $user_role, $for_users ) ) {
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
						foreach( $field_group['fields'] as $field_group_field ) {
							$wcfm_is_allowed_toolset_field = apply_filters( 'wcfm_is_allow_user_toolset_field', true, $field_group_field, $user_id->ID );
				  		if( !$wcfm_is_allowed_toolset_field ) continue;
							
							switch( $field_group_field['type'] ) {
								case 'date':
									if ( !wpcf_admin_is_repetitive( $field_group_field ) ) {
										if( isset( $wcfm_profile_form['wpcf'][$field_group_field['meta_key']] ) && ! empty( $wcfm_profile_form['wpcf'][$field_group_field['meta_key']] ) ) {
											$toolset_types_filed_meta_key = apply_filters( 'wcfm_toolset_profile_field_meta', $field_group_field['meta_key'] );
											update_user_meta( $user_id->ID, $toolset_types_filed_meta_key, strtotime( $wcfm_profile_form['wpcf'][$field_group_field['meta_key']] ) );
										}
									}
								break;
							}
						}
					}
				}
			}
		}
	}
}