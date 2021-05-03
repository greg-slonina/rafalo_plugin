<?php
/**
 * Plugin Name: WCFM - WooCommerce Frontend Manager - Ultimate
 * Plugin URI: https://wclovers.com
 * Description: Now manage your WooCommerce Store from your Store Front with more Powers. Easily and Peacefully.
 * Author: WC Lovers
 * Version: 6.5.5
 * Author URI: https://wclovers.com
 *
 * Text Domain: wc-frontend-manager-ultimate
 * Domain Path: /lang/
 *
 * WC requires at least: 3.0.0
 * WC tested up to: 4.8.0
 *
 */

if(!defined('ABSPATH')) exit; // Exit if accessed directly

if ( ! class_exists( 'WCFMu_Dependencies' ) )
	require_once 'helpers/class-wcfmu-dependencies.php';

require_once 'helpers/wcfmu-core-functions.php';
require_once 'wc_frontend_manager_ultimate_config.php';

if(!defined('WCFMu_TOKEN')) exit;
if(!defined('WCFMu_TEXT_DOMAIN')) exit;


if(!WCFMu_Dependencies::woocommerce_plugin_active_check()) {
	add_action( 'admin_notices', 'wcfmu_woocommerce_inactive_notice' );
} else {

	if(!WCFMu_Dependencies::wcfm_plugin_active_check()) {
		add_action( 'admin_notices', 'wcfmu_wcfm_inactive_notice' );
	} else {
		if(!class_exists('WCFMu')) {
			include_once( 'core/class-wcfmu.php' );
			global $WCFMu;
			$WCFMu = new WCFMu( __FILE__ );
			$GLOBALS['WCFMu'] = $WCFMu;
			
			// Activation Hooks
			register_activation_hook( __FILE__, array('WCFMu', 'activate_wcfm') );
			register_activation_hook( __FILE__, 'flush_rewrite_rules' );
			
			// Deactivation Hooks
			register_deactivation_hook( __FILE__, array('WCFMu', 'deactivate_wcfm') );
		}
	}
}
?>