<?php
/**
 * WCFM plugin controllers
 *
 * Plugin Toolset Types Articles Manage Controller
 *
 * @author 		WC Lovers
 * @package 	wcfmu/controllers/thirdparty
 * @version   4.2.3
 */

class WCFMu_Toolset_Types_Articles_Manage_Controller {
	
	public function __construct() {
		global $WCFM;
		
		// Third Party Article Meta Data Save
    add_action( 'after_wcfm_articles_manage_meta_save', array( &$this, 'wcfm_toolset_types_articles_manage_meta_save' ), 150, 2 );
	}
	
	/**
	 * Toolset Field Article Meta data save
	 */
	function wcfm_toolset_types_articles_manage_meta_save( $new_article_id, $wcfm_articles_manage_form_data ) {
		global $WCFM;
		
		if( isset( $wcfm_articles_manage_form_data['wpcf'] ) && ! empty( $wcfm_articles_manage_form_data['wpcf'] ) ) {
			foreach( $wcfm_articles_manage_form_data['wpcf'] as $toolset_types_filed_key => $toolset_types_filed_value ) {
				update_post_meta( $new_article_id, $toolset_types_filed_key, $toolset_types_filed_value );
				if( is_array( $toolset_types_filed_value ) ) {
					delete_post_meta( $new_article_id, $toolset_types_filed_key );
					foreach( $toolset_types_filed_value as $toolset_types_filed_value_field ) {
						if( isset( $toolset_types_filed_value_field['field'] ) ) {
							add_post_meta( $new_article_id, $toolset_types_filed_key, $toolset_types_filed_value_field['field'] );
						}
					}
				}
			}
			
			
			
			include_once( WPCF_EMBEDDED_ABSPATH . '/includes/fields-post.php' );
			$article_post = get_post( $new_article_id );
			$article_post->post_type = 'post';
			
			if( class_exists( 'Types_Post_Type' ) ) {
				$Types_Post_Type = new Types_Post_Type( 'post' );
				$field_groups = $Types_Post_Type->get_field_groups();
				$field_groups = wpcf_admin_post_get_post_groups_fields( $article_post );
				 
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
						
						$wcfm_is_allowed_toolset_field_group = apply_filters( 'wcfm_is_allow_article_toolset_field_group', true, $field_group_index, $field_group );
						if( !$wcfm_is_allowed_toolset_field_group ) continue;
						
						if ( !empty( $field_group['fields'] ) ) { 
							if ( !empty( $field_group['fields'] ) ) {
								foreach( $field_group['fields'] as $field_group_field ) {
									$wcfm_is_allowed_toolset_field = apply_filters( 'wcfm_is_allow_article_toolset_field', true, $field_group_field );
									if( !$wcfm_is_allowed_toolset_field ) continue;
									
									switch( $field_group_field['type'] ) {
										case 'date':
											if ( !wpcf_admin_is_repetitive( $field_group_field ) ) {
												if( isset( $wcfm_articles_manage_form_data['wpcf'][$field_group_field['meta_key']] ) && ! empty( $wcfm_articles_manage_form_data['wpcf'][$field_group_field['meta_key']] ) ) {
													update_post_meta( $new_article_id, $field_group_field['meta_key'], strtotime( $wcfm_articles_manage_form_data['wpcf'][$field_group_field['meta_key']] ) );
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
	}
}