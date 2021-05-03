<?php
/**
 * Plugin Name: WCFM - WooCommerce Multivendor Marketplace - Elementor
 * Plugin URI: https://wclovers.com/
 * Description: Create your marketplace store pages using Elementor with your own design. Easily and Beatifully.
 * Author: WC Lovers
 * Version: 1.1.4
 * Author URI: https://wclovers.com
 *
 * Text Domain: wc-frontend-manager-elementor
 * Domain Path: /lang/
 *
 * WC requires at least: 3.0.0
 * WC tested up to: 5.1.0
 *
 */

if(!defined('ABSPATH')) exit; // Exit if accessed directly

if ( ! class_exists( 'WCFMem_Dependencies' ) )
	require_once 'helpers/class-wcfmem-dependencies.php';

if( !WCFMem_Dependencies::woocommerce_plugin_active_check() )
	return;

if( !WCFMem_Dependencies::wcfm_plugin_active_check() )
	return;

if( !WCFMem_Dependencies::wcfmmp_plugin_active_check() )
	return;

if( !WCFMem_Dependencies::elementor_plugin_active_check() )
	return;

require_once 'helpers/wcfmem-core-functions.php';
require_once 'wc-frontend-manager-elementor-config.php';

if(!class_exists('WCFM_Elementor')) {
	include_once( 'core/class-wcfmem.php' );
	global $WCFM, $WCFMem, $WCFM_Query;
	$WCFMem = new WCFM_Elementor( __FILE__ );
	$GLOBALS['WCFMem'] = $WCFMem;
}