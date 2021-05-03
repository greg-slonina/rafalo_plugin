<?php
/**
 * WCFM plugin controllers
 *
 * Plugin Advanced Custom Fields(ACF) Articles Manage Controller
 *
 * @author 		WC Lovers
 * @package 	wcfmu/controllers/thirdparty
 * @version   3.0.4
 */

class WCFMu_ACF_Articles_Manage_Controller {
	
	public function __construct() {
		global $WCFM;
		
		// Third Party Article Meta Data Save
    add_action( 'after_wcfm_articles_manage_meta_save', array( &$this, 'wcfm_acf_articles_manage_meta_save' ), 160, 2 );
	}
	
	/**
	 * ACF Field Article Meta data save
	 */
	function wcfm_acf_articles_manage_meta_save( $new_article_id, $wcfm_articles_manage_form_data ) {
		global $WCFM;
		
		if( isset( $wcfm_articles_manage_form_data['acf'] ) && ! empty( $wcfm_articles_manage_form_data['acf'] ) ) {
			foreach( $wcfm_articles_manage_form_data['acf'] as $acf_filed_key => $acf_filed_value ) {
				update_post_meta( $new_article_id, $acf_filed_key, $acf_filed_value );
			}
			
			// For saving only Image & File fields - 3.0.6
			$filter = array( 
				'post_id'	=> $new_article_id, 
				'post_type'	=> 'post' 
			);
			$article_group_ids = array();
			$article_group_ids = apply_filters( 'acf/location/match_field_groups', $article_group_ids, $filter );
			
			// For saving only Image & File fields - 3.0.6
			$field_groups = acf_get_field_groups();
			
			$process_field_groups = array();
			if( !empty( $field_groups )) {
				foreach( $field_groups as $field_group_index => $field_group ) {
					//if( !in_array( $field_group['id'], $article_group_ids ) ) continue;
					if( in_array( $field_group['ID'], $process_field_groups ) ) continue;
					$process_field_groups[$field_group['ID']] = $field_group['ID'];
					
					$wcfm_is_allowed_acf_field_group = apply_filters( 'wcfm_is_allowed_acf_field_group', true, $field_group['ID'] );
					if( !$wcfm_is_allowed_acf_field_group ) continue;
					
					$field_group_fields = acf_get_fields( $field_group );
					//print_r($field_group_fields);
					
					if ( !empty( $field_group_fields ) ) {
						if ( !empty( $field_group_fields ) ) {
							foreach( $field_group_fields as $field_group_field ) {
								if( $field_group_field['type'] == 'image' || $field_group_field['type'] == 'file' ) {
									if( isset( $wcfm_articles_manage_form_data['acf'][$field_group_field['name']] ) && !empty( $wcfm_articles_manage_form_data['acf'][$field_group_field['name']] ) ) {
										$uploaded_file_id = $WCFM->wcfm_get_attachment_id( $wcfm_articles_manage_form_data['acf'][$field_group_field['name']] );
										update_post_meta( $new_article_id, $field_group_field['name'], $uploaded_file_id );
									}
								} elseif( $field_group_field['type'] == 'checkbox' || $field_group_field['type'] == 'true_false' || $field_group_field['type'] == 'radio' ) {
									if( !isset( $wcfm_articles_manage_form_data['acf'][$field_group_field['name']] ) && empty( $wcfm_articles_manage_form_data['acf'][$field_group_field['name']] ) ) {
										update_post_meta( $new_article_id, $field_group_field['name'], '' );
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