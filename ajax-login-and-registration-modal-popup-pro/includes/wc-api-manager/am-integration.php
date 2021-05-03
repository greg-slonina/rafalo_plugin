<?php
/**
 * Start WooCommerce API Manager integration
 */

/**
 * This string must match exactly the WooCommerce product edit screen > API tab Software Title.
 * This string is sent to the API's.
 */
function mapify_plugin_title() {
	return MAPIFY_PLUGIN_NAME;
}

/**
 * This is the Settings Menu name to activate the API License.
 */
// function mapify_settings_menu_title() {
// 	return MAPIFY_PLUGIN_NAME . ' License Activation';
// }

function mapify_settings_menu_title() {
	return 'Activación de MemoryMap';
}

// URL used to talk to the WooCommerce API Manager on your store.
if ( ! defined( 'MAPIFY_UPGRADE_URL' ) ) {
	define( 'MAPIFY_UPGRADE_URL', 'http://www.mapifypro.com/' );
}

// Customer dashboard URL.
if ( ! defined( 'MAPIFY_MY_ACCOUNT_URL' ) ) {
	define( 'MAPIFY_MY_ACCOUNT_URL', 'http://www.mapifypro.com/my-account' );
}

require_once( MAPIFY_PLUGIN_DIR . '/am/mapify-api-manager.php' );

/**
 * End WooCommerce API Manager integration
 */
