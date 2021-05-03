<?php
/**
 * WCFM plugin controllers
 *
 * Plugin Advanced Custom Fields(ACF) Products Manage Controller
 *
 * @author 		WC Lovers
 * @package 	wcfmu/controllers/thirdparty
 * @version   3.0.4
 */

class WCFMu_ACF_Products_Manage_Controller {
	
	public function __construct() {
		global $WCFM;
		
		// Third Party Product Meta Data Save
    add_action( 'after_wcfm_products_manage_meta_save', array( &$this, 'wcfm_acf_products_manage_meta_save' ), 160, 2 );
	}
	
	/**
	 * ACF Field Product Meta data save
	 */
	function wcfm_acf_products_manage_meta_save( $new_product_id, $wcfm_products_manage_form_data ) {
		global $WCFM;
		
		if( isset( $wcfm_products_manage_form_data['acf'] ) && ! empty( $wcfm_products_manage_form_data['acf'] ) ) {
			foreach( $wcfm_products_manage_form_data['acf'] as $acf_filed_key => $acf_filed_value ) {
				update_post_meta( $new_product_id, $acf_filed_key, $acf_filed_value );
			}
			
			// For saving only Image & File fields - 3.0.6
			$field_groups = acf_get_field_groups();
			
			$filter = array( 
				'post_id'	=> $new_product_id, 
				'post_type'	=> 'product' 
			);
			$product_group_ids = array();
			
			$cat_group_id_map = array(); 
			$cat_group_ids = array();
			$product_categories   = get_terms( 'product_cat', 'orderby=name&hide_empty=0&parent=0' );
			if( !empty( $field_groups )) {
				foreach( $field_groups as $field_group_index => $field_group ) {
					if( !$field_group['active'] ) continue;
					if( empty($field_group['location']) ) continue;
					
					foreach( $field_group['location'] as $group_id => $group ) {
						if( empty($group) ) continue;
						
						//print_r($group);
						
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
									continue;
								 break;
							}
						}
					}
				}
			}
			
			wp_localize_script( 'wcfmu_acf_products_manage_js', 'wcfm_cat_based_acf_fields', $cat_group_id_map );
			$cat_group_ids = array_unique($cat_group_ids);
			
			
			$process_field_groups = array();
			if( !empty( $field_groups )) {
				foreach( $field_groups as $field_group_index => $field_group ) {
					
					if( !in_array( $field_group['ID'], $product_group_ids ) && !in_array( $field_group['ID'], $cat_group_ids ) ) continue;
					if( in_array( $field_group['ID'], $process_field_groups ) ) continue;
					$process_field_groups[$field_group['ID']] = $field_group['ID'];
					
					$cat_group_class = '';
					if( in_array( $field_group['ID'], $cat_group_ids ) ) $cat_group_class = 'wcfm_cat_based_acf_product_manager_fields';
					if( in_array( $field_group['ID'], $product_group_ids ) ) $cat_group_class = '';
					
					$wcfm_is_allowed_acf_field_group = apply_filters( 'wcfm_is_allowed_acf_field_group', true, $field_group['ID'] );
					if( !$wcfm_is_allowed_acf_field_group ) continue;
					
					$field_group_fields = acf_get_fields( $field_group );
    
					if ( !empty( $field_group_fields ) ) {
						if ( !empty( $field_group_fields ) ) {
							foreach( $field_group_fields as $field_group_field ) {
								if( $field_group_field['type'] == 'image' || $field_group_field['type'] == 'file' ) {
									if( isset( $wcfm_products_manage_form_data['acf'][$field_group_field['name']] ) && ! empty( $wcfm_products_manage_form_data['acf'][$field_group_field['name']] ) ) {
										$uploaded_file_id = $WCFM->wcfm_get_attachment_id( $wcfm_products_manage_form_data['acf'][$field_group_field['name']] );
										update_post_meta( $new_product_id, $field_group_field['name'], $uploaded_file_id );
									}
								} elseif( $field_group_field['type'] == 'checkbox' || $field_group_field['type'] == 'true_false' || $field_group_field['type'] == 'radio' ) {
									if( !isset( $wcfm_products_manage_form_data['acf'][$field_group_field['name']] ) && empty( $wcfm_products_manage_form_data['acf'][$field_group_field['name']] ) ) {
										update_post_meta( $new_product_id, $field_group_field['name'], '' );
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