<?php
/**
 * WCFM plugin controllers
 *
 * Plugin WC Tabs Manager Products Manage Controller
 *
 * @author 		WC Lovers
 * @package 	wcfmu/controllers/thirdparty
 * @version   4.1.0
 */

class WCFMu_WC_Tabs_Manager_Products_Manage_Controller {
	
	public function __construct() {
		global $WCFM;
		
		// Third Party Product Meta Data Save
    add_action( 'after_wcfm_products_manage_meta_save', array( &$this, 'wcfm_wc_tabs_manager_products_manage_meta_save' ), 170, 2 );
	}
	
	/**
	 * WC Tabs Manager Field Product Meta data save
	 */
	function wcfm_wc_tabs_manager_products_manage_meta_save( $new_product_id, $wcfm_products_manage_form_data ) {
		global $WCFM;
		
		$tab_active    = isset( $wcfm_products_manage_form_data['product_tab_active'] )   ? $wcfm_products_manage_form_data['product_tab_active']   : array();
		$tab_positions = isset( $wcfm_products_manage_form_data['product_tab_position'] ) ? $wcfm_products_manage_form_data['product_tab_position'] : array();
		$tab_types     = isset( $wcfm_products_manage_form_data['product_tab_type'] )     ? $wcfm_products_manage_form_data['product_tab_type']     : array();
		$tab_headings  = isset( $wcfm_products_manage_form_data['product_tab_heading'] )  ? $wcfm_products_manage_form_data['product_tab_heading']  : array(); // available only for the core description/additional_information tabs
		$tab_titles    = isset( $wcfm_products_manage_form_data['product_tab_title'] )    ? $wcfm_products_manage_form_data['product_tab_title']    : array();
		$tab_content   = isset( $wcfm_products_manage_form_data['product_tab_content'] )  ? $wcfm_products_manage_form_data['product_tab_content']  : array(); // available only for product tab type
		$tab_ids       = isset( $wcfm_products_manage_form_data['product_tab_id'] )       ? $wcfm_products_manage_form_data['product_tab_id']       : array();
		
		$tabs = array();
	
		// create the new set of active tabs (if any)
		for ( $i = 0; $i < count( $tab_positions ); $i++ ) {
			if ( $tab_active[ $i ] ) {
				$tab = array( 'position' => $tab_positions[ $i ], 'type' => $tab_types[ $i ], 'id' => $tab_ids[ $i ] );
	
				if ( isset( $tab_titles[ $i ] ) ) $tab['title'] = $tab_titles[ $i ];
	
				if ( 'product' === $tab['type'] ) {
	
					if ( ! $tab['id'] ) {
						// new custom product tab
	
						$new_tab_data = array(
							'post_title'    => $tab_titles[ $i ],
							'post_content'  => $tab_content[ $i ],
							'post_status'   => 'publish',
							'ping_status'   => 'closed',
							'post_author'   => get_current_user_id(),
							'post_type'     => 'wc_product_tab',
							'post_parent'   => $new_product_id,
							'post_password' => uniqid( 'tab_', false ) // Protects the post just in case
						);
	
						$tab['id'] = wp_insert_post( $new_tab_data );
					} else {
						// update existing custom product tab
	
						$tab_data = array(
							'ID'           => $tab['id'],
							'post_title'   => $tab_titles[ $i ],
							'post_content' => $tab_content[ $i ],
						);
						wp_update_post( $tab_data );
					}
	
				}
	
				// only the core description and additional information tabs have a heading
				if ( isset( $tab_headings[ $i ] ) ) {
					$tab['heading'] = $tab_headings[ $i ];
				}
	
				$tabs[ $tab['type'] . '_tab_' . $tab['id'] ] = $tab;
			} else {
				// tab removed
				if ( 'product' === $tab_types[ $i ] ) {
					// for product custom tabs, remove the tab post record
					wp_delete_post( $tab_ids[ $i ] );
				}
			}
		}
	
		// sort the tabs according to position
		if ( ! function_exists( 'product_tabs_cmp' ) ) {
	
			function product_tabs_cmp( $a, $b ) {
	
				if ( $a['position'] == $b['position'] ) {
					return 0;
				}
	
				return $a['position'] < $b['position'] ? -1 : 1;
			}
		}
	
		uasort( $tabs, 'product_tabs_cmp' );
	
		// make sure the position values are 0, 1, 2 ...
		$i = 0;
		foreach ( $tabs as &$tab ) {
			$tab['position'] = $i++;
		}
	
	
		// it's important to generate unique names to use for the tab/tab panel css ids, so that
		//  clicking a tab brings up the correct tab panel (since we can't change their names)
		//  We'll generate names like 'description', 'description-1', 'description-2', etc
		$found_names = array();
		$tab_names   = array();
	
		// first off, the core tabs get priority on naming (which for them is their id)
		foreach ( $tabs as &$tab ) {
	
			if ( 'core' === $tab['type'] ) {
	
				$tab_name = $tab['id'];
	
				if ( ! isset( $found_names[ $tab_name ] ) ) {
					$found_names[ $tab_name ] = 0;
				}
	
				$found_names[ $tab_name ]++;
			}
		}
	
		// next up: the 3rd party tabs; we don't want to clash with their keys
		foreach ( $tabs as &$tab ) {
	
			if ( 'third_party' === $tab['type'] ) {
	
				$tab_name = $tab['id'];
	
				if ( ! isset( $found_names[ $tab_name ] ) ) {
					$found_names[ $tab_name ] = 0;
				}
	
				$found_names[ $tab_name ]++;
			}
		}
	
		// next up are the global tabs
		foreach ( $tabs as &$tab ) {
	
			if ( 'global' === $tab['type'] ) {
	
				// see product tab comment below for naming discussion
				if ( strlen( $tab['title'] ) !== strlen( utf8_encode( $tab['title'] ) ) ) {
					$tab_name = 'global-tab';
				} else {
					$tab_name = sanitize_title( $tab['title'] );
				}
	
				if ( ! isset( $found_names[ $tab_name ] ) ) {
					$found_names[ $tab_name ] = 0;
				}
	
				$found_names[ $tab_name ]++;
	
				if ( $found_names[ $tab_name ] > 1 ) $tab_name .= '-' . ( $found_names[ $tab_name ] - 1 );
	
				$tab['name'] = $tab_name;
	
				// once the title is used to generate the unique name, it is no longer needed as it will be pulled from the tab post
				unset( $tab['title'] );
			}
		}
	
		// finally the custom product tabs
		foreach ( $tabs as &$tab ) {
	
			if ( 'product' === $tab['type'] ) {
	
				// we try to generate a clean unique tab name based off of the tab title,
				//  however the page javascript (jquery) that controls the tab switching can not
				//  handle unicode class id's, escaped or otherwise.  The compromise is to
				//  use the "pretty" name for non-unicode strings, and just use a safe "product-tab"
				//  identifier for tab titles containing unicode
				if ( strlen( $tab['title'] ) !== strlen( utf8_encode( $tab['title'] ) ) ) {
					$tab_name = 'product-tab';
				} else {
					$tab_name = sanitize_title( $tab['title'] );
				}
	
				if ( ! isset( $found_names[ $tab_name ] ) ) {
					$found_names[ $tab_name ] = 0;
				}
	
				$found_names[ $tab_name ]++;
	
				if ( $found_names[ $tab_name ] > 1 ) {
					$tab_name .= '-' . ( $found_names[ $tab_name ] - 1 );
				}
	
				$tab['name'] = $tab_name;
	
				// once the title is used to generate the unique name, it is no longer needed as it will be pulled from the tab post
				unset( $tab['title'] );
			}
		}
		
		$new_tabs = $tabs;
		$old_tabs = get_post_meta( $new_product_id, '_product_tabs', true );

		if ( ! is_array( $old_tabs ) ) {
			$old_tabs = array();
		}
	
		update_post_meta( $new_product_id, '_product_tabs', $new_tabs );
	
		do_action( 'wc_tab_manager_product_tabs_updated', $new_tabs, $old_tabs );
	
		// Whether the tab layout defined at the product level should be used.
		$override_tab_layout = isset( $wcfm_products_manage_form_data['_override_tab_layout'] ) && $wcfm_products_manage_form_data['_override_tab_layout'] ? 'yes' : 'no';
	
		update_post_meta( $new_product_id, '_override_tab_layout', $override_tab_layout );
	
		// Update / remove tab content meta.
		$args = array(
			'product_id' => $new_product_id,
		);
	
		if ( 'yes' === $override_tab_layout ) {
			$args['action'] = 'update';
		} else {
			$args['action'] = 'remove';
		}
	
		// Extract product & global tab IDs from tab data array.
		$tab_id_list = array();
		foreach ( $new_tabs as $key => $tab ) {
			if ( 'product' === $tab['type'] || 'global' === $tab['type'] ) {
				$tab_id_list[] = $tab['id'];
			}
		}
	
		// Only update meta if we have any tabs to process.
		if ( ! empty( $tab_id_list ) ) {
			wc_tab_manager()->get_search_instance()->update_products_for_tabs( $tab_id_list, $args );
		}
	}
}