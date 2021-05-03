<?php
/**
 * WCFMu plugin core
 *
 * Plugin Sitepress WPML Controler
 *
 * @author 		WC Lovers
 * @package 	wcfmu/core
 * @version   3.2.0
 */
 
class WCFMu_Sitepress_WPML extends WPML_WPDB_And_SP_User {

	public function __construct() {
		global $WCFM, $WCFMu;
		
		if ( defined( 'ICL_SITEPRESS_VERSION' ) && ! ICL_PLUGIN_INACTIVE && class_exists( 'SitePress' ) ) {
			add_filter( 'wcfm_get_endpoint_url', array( &$this, 'wpml_wcfm_get_endpoint_url' ), 500, 4 );
			
			add_action( 'wcfm_load_scripts', array( &$this, 'wpml_wcfm_load_scripts' ), 80 );
			add_action( 'after_wcfm_load_scripts', array( &$this, 'wpml_wcfm_load_scripts' ), 80 );
			
			add_action( 'wp_ajax_wcfm_product_translations', array( &$this, 'wpml_wcfm_product_translations' ) );
			
			add_action( 'wp_ajax_wcfm_product_new_translation', array( &$this, 'wpml_wcfm_product_new_translation' ) );
			
			add_action( 'wcfm_product_manager_right_panel_after', array( &$this, 'wpml_wcfm_product_manager_translations' ), 200 );
		}
	}
	
	function wpml_wcfm_get_endpoint_url( $url, $endpoint, $value, $permalink ) {
		global $WCFM;
		
		switch( $endpoint ) {
			case 'wcfm-products-manage':
				if( $value ) {
					$product_language_details = apply_filters( 'wpml_post_language_details', NULL, $value ) ;
					$language_code = isset( $product_language_details['language_code'] ) ? $product_language_details['language_code'] : '';
					if( $language_code ) {
						$pages = get_option("wcfm_page_options");
						if(isset($pages['wc_frontend_manager_page_id'])) {
							$permalink = get_permalink( icl_object_id( $pages['wc_frontend_manager_page_id'], 'page', false, $language_code ) );
							$permalink = apply_filters( 'wpml_permalink', $permalink, $language_code );
							if ( get_option( 'permalink_structure' ) ) {
								if ( strstr( $permalink, '?' ) ) {
									$query_string = '?' . parse_url( $permalink, PHP_URL_QUERY );
									$permalink    = current( explode( '?', $permalink ) );
								} else {
									$query_string = '';
								}
								$url = trailingslashit( $permalink ) . $endpoint . '/' . $value . $query_string;
							} else {
								$url = add_query_arg( $endpoint, $value, $permalink );
							}
						}
					}
				}
			break;
		}
		
		return $url;
	}
	
	/**
   * WPML Scripts
   */
  public function wpml_wcfm_load_scripts( $end_point ) {
	  global $WCFM, $WCFMu;
    
	  switch( $end_point ) {
	  	case 'wcfm-products-manage':
	  		wp_enqueue_script( 'wcfm_wpml_products_manage_js', $WCFMu->library->js_lib_url . 'integrations/wcfm-script-wpml-products-manage.js', array( 'jquery', 'wcfm_products_manage_js' ), $WCFMu->version, true );
	  	break;
	  }
	}
	
	/**
	 * Get Translations table content for Product manager
	 *
	 * @return string
	 */
	function wpml_wcfm_product_translations() {
		global $WCFM, $WCFMu, $sitepress, $wpml_post_translations, $_POST;
		
		$translation_html = '';
		if( isset( $_POST['proid'] ) && !empty( $_POST['proid'] ) ) {
			$product_id = $_POST['proid'];
			if( $product_id ) {
				$active_languages = $this->get_filtered_active_lanugages();
				if ( count( $active_languages ) <= 1 ) {
					return;
				}
		
				$current_language = $sitepress->get_current_language();
				unset( $active_languages[ $current_language ] );
		
				if ( count( $active_languages ) > 0 ) {
					foreach ( $active_languages as $language_data ) {
						$translated_id = $wpml_post_translations->element_id_in( $product_id, $language_data['code'] );
						$trid = $wpml_post_translations->get_element_trid ( $product_id );
						$translation_edit_url = '';
						if( $translated_id ) {
							$translate_text = sprintf( __ ( 'Edit the %s translation', 'sitepress' ), $language_data['display_name'] );
							//wcfm_log( $translated_id . ":aa:" . $language_data['code'] );
							$translation_edit_url = '<a href="' . get_wcfm_edit_product_url( $translated_id, array(), $language_data['code'] ) . '" title="' . $translate_text . '"><img style="padding:1px;margin:2px;" border="0" src="' . ICL_PLUGIN_URL . '/res/img/edit_translation.png" alt="' . $translate_text . '" width="16" height="16" /></a>';
						} else {
							$translate_text = sprintf( __ ( 'Add translation to %s', 'sitepress' ), $language_data['display_name'] );
							$translation_edit_url = '<a href="#" class="wcfm_product_new_translation" data-trid="' . $trid . '" data-source_lang="' . $current_language . '" data-proid="' . $product_id . '" data-lang="' . $language_data['code'] . '" title="' . $translate_text . '"><img style="padding:1px;margin:2px;" border="0" src="' . ICL_PLUGIN_URL . '/res/img/add_translation.png" alt="' . $translate_text . '" width="16" height="16" /></a>';
						}
						
						$translation_html .= '<tr><td><img src="' . $sitepress->get_flag_url( $language_data['code'] ). '" width="18" height="12" alt="' . $language_data['display_name'] . '" title="' . $language_data['display_name'] . '" style="margin:2px" /></td>';
						$translation_html .= '<td>' . $translation_edit_url . '</td></tr>';
					}
				}
			}
		}
		
		echo $translation_html;
		die;
	}
	
	/**
	 * Generate new Translation for WCFM Products
	 *
	 */
	function wpml_wcfm_product_new_translation() {
		global $WCFM, $WCFMu, $sitepress, $wpml_post_translations, $_POST, $wpdb;
		
		if( isset( $_POST['proid'] ) && !empty( $_POST['proid'] ) ) {
			$product_id = absint($_POST['proid']);
			if( $product_id ) {
				if( isset( $_POST['lang'] ) && !empty( $_POST['lang'] ) ) {
					$lang_code = $_POST['lang'];
					if( $lang_code ) {
						$product = wc_get_product( $product_id );
						if ( false === $product ) {
							/* translators: %s: product id */
							echo '{"status": false, "message": "' . sprintf( __( 'Product creation failed, could not find original product: %s', 'woocommerce' ), $product_id ) . '" }';
						}
				
						if( !class_exists( 'WC_Admin_Duplicate_Product' ) ) {
							include( WC_ABSPATH . 'includes/admin/class-wc-admin-duplicate-product.php' );
						}
						$WC_Admin_Duplicate_Product = new WC_Admin_Duplicate_Product();
						$duplicate = $WC_Admin_Duplicate_Product->product_duplicate( $product );
				
						// Hook rename to match other woocommerce_product_* hooks, and to move away from depending on a response from the wp_posts table.
						//do_action( 'woocommerce_product_duplicate', $duplicate, $product );
						//do_action( 'after_wcfm_product_duplicate', $duplicate->get_id(), $product );
						
						$vendor_id = wcfm_get_vendor_id_by_post( $product_id );	
						if( !$vendor_id ) {
							$vendor_id = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );
						}
						
						// Update translated post to sete title/content empty
						$my_post = apply_filters( 'wcfm_translated_product_content_before_save', array(
								'ID'           => $duplicate->get_id(),
								'post_title'   => get_the_title( $product_id ) . ' (' . $lang_code . ' copy)',
								'post_author'  => $vendor_id,
								'post_content' => '',
								'post_excerpt' => '',
						), $product_id );
						wp_update_post( $my_post );
						
						update_post_meta( $duplicate->get_id(), '_wcfm_product_views', 0 );
						
						$source_lang = $_POST['source_lang'];
						$dest_lang   = $_POST['lang'];
						$trid        = $_POST['trid'];
						
						// Connect Translations
						$original_element_language = $sitepress->get_default_language();
						$trid_elements             = $sitepress->get_element_translations( $trid, 'post_product' );
						if($trid_elements) {
							foreach ( $trid_elements as $trid_element ) {
								if ( $trid_element->original ) {
									$original_element_language = $trid_element->language_code;
									break;
								}
							}
						}
						$wpdb->update(
							$wpdb->prefix . 'icl_translations',
							array( 'source_language_code' => $original_element_language, 'trid' => $trid ),
							array( 'element_id' => $duplicate->get_id(), 'element_type' => 'post_product' ),
							array( '%s', '%d', '%s' ),
							array( '%d', '%s' )
						);
			
			
						do_action(
							'wpml_translation_update',
							array(
								'type' => 'update',
								'trid' => $trid,
								'element_id' => $duplicate->get_id(),
								'element_type' => 'post_product',
								'context' => 'post'
							)
						);
						
						// Product Custom Taxonomies - 6.0.3
						$product_taxonomies = get_object_taxonomies( 'product', 'objects' );
						if( !empty( $product_taxonomies ) ) {
							foreach( $product_taxonomies as $product_taxonomy ) {
								if( !in_array( $product_taxonomy->name, array( 'product_cat', 'product_tag', 'wcpv_product_vendors' ) ) ) {
									if( $product_taxonomy->public && $product_taxonomy->show_ui && $product_taxonomy->meta_box_cb && $product_taxonomy->hierarchical ) {
										$taxonomy_values = get_the_terms( $product->get_id(), $product_taxonomy->name );
										$is_translated = $sitepress->is_translated_taxonomy( $product_taxonomy );
										$is_first = true;
										if( !empty($taxonomy_values) ) {
											foreach($taxonomy_values as $pkey => $ptaxonomy) {
												if( $is_translated ) {
													$term_id = apply_filters( 'translate_object_id', (int)$ptaxonomy->term_id, $product_taxonomy->name, false, $dest_lang );
												} else {
													$term_id = (int)$ptaxonomy->term_id;
												}
												if($is_first) {
													$is_first = false;
													wp_set_object_terms( $duplicate->get_id(), $term_id, $product_taxonomy->name );
												} else {
													wp_set_object_terms( $duplicate->get_id(), $term_id, $product_taxonomy->name, true );
												}
											}
										}
									}
								}
							}
						}
						
						// Associate Vendor on Translation
						//$is_marketplace = wcfm_is_marketplace();
						//define( 'DOING_WCFM_WPML', true );
						//if( $is_marketplace ) {
							//$vendor_id = wcfm_get_vendor_id_by_post( $product_id );
							//if( $vendor_id ) {
								//$WCFM->wcfm_vendor_support->wcfm_associate_vendor_save( $duplicate->get_id(), array( 'wcfm_associate_vendor' => $vendor_id ) );
							//}
						//}
						
						do_action( 'wcfm_after_translated_new_product', $duplicate->get_id() );
						
						// Redirect to the edit screen for the new draft page
						echo '{"status": true, "redirect": "' . get_wcfm_edit_product_url( $duplicate->get_id() ) . '", "id": "' . $duplicate->get_id() . '"}';
					}
				}
			}
		}
		die;
	}
	
	/**
	 * Generate Translation block for WCFM Product manager
	 *
	 */
	function wpml_wcfm_product_manager_translations( $product_id ) {
		global $WCFM, $WCFMu, $sitepress, $wpml_post_translations;
		
		if( !$product_id ) return;
		
		$active_languages = $this->get_filtered_active_lanugages();
		if ( count( $active_languages ) <= 1 ) {
			return;
		}

		$current_language = $sitepress->get_current_language();
		unset( $active_languages[ $current_language ] );

		if ( count( $active_languages ) > 0 ) {
			$translation_html = '';
			?>
			<div style="max-width: 214px; margin: 0 auto;">
				<p class="product_translations wcfm_title wcfm_full_ele"><strong><?php _e( 'Translations', 'wc-frontend-manager-ultimate' ); ?></strong></p>
				<label class="screen-reader-text" for="product_translations"><?php _e( 'Translations', 'wc-frontend-manager-ultimate' ); ?></label>
				
				<table style="margin-top:0px;">
					<tbody id="wcfm_product_translations" data-product_id="<?php echo $product_id; ?>">
						<?php echo $translation_html; ?>
					</tbody>
				</table>
			</div>
			<?php
		}
	}
	
	/**
	 * Get list of active languages.
	 *
	 * @return array
	 */
	private function get_filtered_active_lanugages() {
		global $sitepress; 
		
		$active_languages = $sitepress->get_active_languages();
		return apply_filters( 'wpml_active_languages_access', $active_languages, array( 'action' => 'edit' ) );
	}
}