<?php
/**
 * WCFMu plugin controllers
 *
 * Plugin Settings Controller
 *
 * @author 		WC Lovers
 * @package 	wcfmu/controllers
 * @version   2.2.6
 */

class WCFMu_Settings_Controller {
	
	public function __construct() {
		global $WCFM, $WCFMu;
		
		add_action( 'wcfm_settings_update', array( &$this, 'wcfmu_settings_update' ) );
	}
	
	function wcfmu_settings_update( $wcfm_settings_form ) {
		global $WCFM, $WCFMu, $WCFM_Query;
		
		if( isset( $wcfm_settings_form['wcfm_endpoints'] ) ) {
			wcfm_update_option( 'wcfm_endpoints', $wcfm_settings_form['wcfm_endpoints'] );
			
			$permalink_refresh = true;
			
			global $sitepress;
			if ( function_exists('icl_object_id') && $sitepress ) {
				$default_lang = $sitepress->get_default_language();
				$current_lang = ICL_LANGUAGE_CODE;
				if( $default_lang != $current_lang ) {
					$permalink_refresh = false;
				}
			}
			
			if( $permalink_refresh ) {
				// Intialize WCFM End points
				$WCFM_Query->init_query_vars();
				$WCFM_Query->add_endpoints();
			
				// Flush rules after endpoint update
				flush_rewrite_rules();
			}
		}
	}
	
}