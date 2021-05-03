<?php
/**
 * WCFM plugin controllers
 *
 * Plugin Toolset Types Product Type wise settings Controller
 *
 * @author 		WC Lovers
 * @package 	wcfmu/controllers/thirdparty
 * @version   3.1.7
 */

class WCFMu_Toolset_Types_Settings_Controller {
	
	public function __construct() {
		global $WCFM;
		
		// Third Party Settings Data Save
    add_action( 'wcfm_settings_update', array( &$this, 'wcfm_toolset_types_settings_save' ), 150, 2 );
	}
	
	/**
	 * Toolset Field Product Type wise settings data save
	 */
	function wcfm_toolset_types_settings_save( $wcfm_settings_form ) {
		global $WCFM;
		if( isset( $wcfm_settings_form['wcfm_product_type_toolset_fields'] ) ) {
			update_option( 'wcfm_product_type_toolset_fields', $wcfm_settings_form['wcfm_product_type_toolset_fields'] );
		}  else {
			update_option( 'wcfm_product_type_toolset_fields', array() );
		}
	}
}