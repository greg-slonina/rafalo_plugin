<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

function lrm_pro_is_license_active() {
	update_option('lrm_api_manager_activated', 'Activated');
    return get_option( 'lrm_api_manager_activated' ) === 'Activated';
}

/**
 * Displays an inactive message if the API License Key has not yet been activated
 */
if ( ! lrm_pro_is_license_active() ) {
    add_action( 'admin_notices', 'LRM_API_Manager::am_example_inactive_notice' );
}


class LRM_API_Manager {

	/**
	 * Self Upgrade Values
	 */
	// Base URL to the remote upgrade API Manager server. If not set then the Author URI is used.
	//public $upgrade_url = 'https://dev.maxim-kaminsky.com/mk-api/';
	public $upgrade_url = 'https://maxim-kaminsky.com/shop/';

	/**
	 * @var string
	 */
	public $version = LRM_PRO_VERSION;

	/**
	 * @var string
	 * This version is saved after an upgrade to compare this db version to $version
	 */
	public $lrm_api_manager_version_name = 'plugin_lrm_api_manager_version';

	/**
	 * @var string
	 */
	public $plugin_url;

	/**
	 * @var string
	 * used to defined localization for translation, but a string literal is preferred
	 *
	 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/issues/59
	 * http://markjaquith.wordpress.com/2011/10/06/translating-wordpress-plugins-and-themes-dont-get-clever/
	 * http://ottopress.com/2012/internationalization-youre-probably-doing-it-wrong/
	 */
	public $text_domain = 'ajax-login-and-registration-modal-popup';

	/**
	 * Data defaults
	 * @var mixed
	 */
	private $ame_software_product_id;

	public $ame_data_key;
	public $ame_api_key;
	public $ame_activation_email;
	public $ame_product_id_key;
	public $ame_instance_key;
	public $ame_deactivate_checkbox_key;
	public $ame_activated_key;

	public $ame_deactivate_checkbox;
	public $ame_activation_tab_key;
	public $ame_deactivation_tab_key;
	public $ame_settings_menu_title;
	public $ame_settings_title;
	public $ame_menu_tab_activation_title;
	public $ame_menu_tab_deactivation_title;

	public $ame_options;
	public $ame_plugin_name;
	public $ame_product_id;
	public $ame_renew_license_url;
	public $ame_instance_id;
	public $ame_domain;
	public $ame_software_version;
	public $ame_plugin_or_theme;

	public $ame_update_version;

	/**
	 * Used to send any extra information.
	 * @var mixed array, object, string, etc.
	 */
	public $ame_extra;

    /**
     * @var The single instance of the class
     */
    protected static $_instance = null;

    public static function instance() {

        if ( is_null( self::$_instance ) ) {
        	self::$_instance = new self();
        }

        return self::$_instance;
    }

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.2
	 */
	private function __clone() {}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.2
	 */
	private function __wakeup() {}

	public function __construct() {

	    $this->upgrade_url = defined('LRM_API_URL') ? LRM_API_URL : 'https://maxim-kaminsky.com/shop/';

//	    var_dump($this->upgrade_url);

		// Run the activation function
        //register_activation_hook( __FILE__, array( $this, 'activation' ) );

		// Ready for translation
		//load_plugin_textdomain( $this->text_domain, false, dirname( untrailingslashit( plugin_basename( __FILE__ ) ) ) . '/languages' );

		if ( is_admin() ) {

			// Check for external connection blocking
			add_action( 'admin_notices', array( $this, 'check_external_blocking' ) );

			/**
			 * Software Product ID is the product title string
			 * This value must be unique, and it must match the API tab for the product in WooCommerce
			 */


			$this->ame_software_product_id = 'AJAX Login and Registration modal popup PRO';
			//$this->ame_software_product_id = 124;

			/**
			 * Set all data defaults here
			 */
			$this->ame_data_key 				= 'lrm_api_manager';
			$this->ame_api_key 					= 'api_key';
			$this->ame_activation_email 		= 'activation_email';
			$this->ame_product_id_key 			= 'lrm_api_manager_product_id';
			$this->ame_instance_key 			= 'lrm_api_manager_instance';
			$this->ame_deactivate_checkbox_key 	= 'lrm_api_manager_deactivate_checkbox';
			$this->ame_activated_key 			= 'lrm_api_manager_activated';

			/**
			 * Set all admin menu data
			 */
			$this->ame_deactivate_checkbox 			= 'lrm_api_deactivate_checkbox';
			$this->ame_activation_tab_key 			= 'lrm_api_manager_dashboard';
			$this->ame_deactivation_tab_key 		= 'lrm_api_manager_deactivation';
			$this->ame_settings_menu_title 			= 'LRM PRO license';
			$this->ame_settings_title 				= 'LRM PRO license';
			$this->ame_menu_tab_activation_title 	= __( 'License Activation', $this->text_domain );
			$this->ame_menu_tab_deactivation_title 	= __( 'License Deactivation', $this->text_domain );

            //update_option( $this->ame_product_id_key, $this->ame_software_product_id );
            //var_dump(get_option( $this->ame_product_id_key ));

			/**
			 * Set all software update data here
			 */
			$this->ame_options 				= get_option( $this->ame_data_key );
			$this->ame_plugin_name 			= LRM_PRO_BASENAME; // same as plugin slug. if a theme use a theme name like 'twentyeleven'
			$this->ame_product_id 			= get_option( $this->ame_product_id_key ); // Software Title
			$this->ame_renew_license_url 	= $this->upgrade_url; // URL to renew a license. Trailing slash in the upgrade_url is required.
			$this->ame_instance_id 			= get_option( $this->ame_instance_key ); // Instance ID (unique to each blog activation)

			/**
			 * Some web hosts have security policies that block the : (colon) and // (slashes) in http://,
			 * so only the host portion of the URL can be sent. For example the host portion might be
			 * www.example.com or example.com. http://www.example.com includes the scheme http,
			 * and the host www.example.com.
			 * Sending only the host also eliminates issues when a client site changes from http to https,
			 * but their activation still uses the original scheme.
			 * To send only the host, use a line like the one below:
			 *
			 * $this->ame_domain = str_ireplace( array( 'http://', 'https://' ), '', home_url() ); // blog domain name
			 */
			$this->ame_domain 				= str_ireplace( array( 'http://', 'https://' ), '', home_url() ); // blog domain name
			$this->ame_software_version 	= $this->version; // The software version
			$this->ame_plugin_or_theme 		= 'plugin'; // 'theme' or 'plugin'

			// Performs activations and deactivations of API License Keys
			require_once( plugin_dir_path( __FILE__ ) . 'am/classes/class-wcro-api-manager-key.php' );

			// Checks for software updatess
			require_once( plugin_dir_path( __FILE__ ) . 'am/classes/class-wcro-api-manager-update-api-check.php' );

			// Admin menu with the license key and license email form
			require_once( plugin_dir_path( __FILE__ ) . 'am/admin/class-wcro-api-manager-menu.php' );

			$options = get_option( $this->ame_data_key );

			/**
			 * Check for software updates
			 */
			if ( ! empty( $options ) && $options !== false ) {

				$this->update_check(
					$this->upgrade_url,
					$this->ame_plugin_name,
					$this->ame_product_id,
					$this->ame_options[$this->ame_api_key],
					$this->ame_options[$this->ame_activation_email],
					$this->ame_renew_license_url,
					$this->ame_instance_id,
					$this->ame_domain,
					$this->ame_software_version,
					$this->ame_plugin_or_theme,
					$this->text_domain
					);

			}

			// Run the activation if Instance ID is not generated
            if ( ! wp_doing_ajax() && ! get_option($this->ame_instance_key) ) {
                $this->activation();
            }
		}

		/**
		 * Deletes all data if plugin deactivated
         * Moved to the LRM_Deactivator
		 */
		//register_deactivation_hook( __FILE__, array( $this, 'uninstall' ) );

        //var_dump( get_option($this->ame_instance_key) );
    }

	/** Load Shared Classes as on-demand Instances **********************************************/

	/**
	 * API Key Class.
	 *
	 * @return LRM_Api_Manager_Key
	 */
	public function key() {
		return LRM_Api_Manager_Key::instance();
	}

	/**
	 * Update Check Class.
	 *
	 * @return LRM_API_Manager_Update_API_Check
	 */
	public function update_check( $upgrade_url, $plugin_name, $product_id, $api_key, $activation_email, $renew_license_url, $instance, $domain, $software_version, $plugin_or_theme, $text_domain, $extra = '' ) {

		return LRM_API_Manager_Update_API_Check::instance( $upgrade_url, $plugin_name, $product_id, $api_key, $activation_email, $renew_license_url, $instance, $domain, $software_version, $plugin_or_theme, $text_domain, $extra );
	}

	public function plugin_url() {
		if ( isset( $this->plugin_url ) ) {
			return $this->plugin_url;
		}

		return $this->plugin_url = plugins_url( '/', __FILE__ );
	}

	/**
	 * Generate the default data arrays
	 */
	public function activation() {
		global $wpdb;

		$global_options = array(
			$this->ame_api_key 				=> '',
			$this->ame_activation_email 	=> '',
        );

		update_option( $this->ame_data_key, $global_options );

		//require_once( plugin_dir_path( __FILE__ ) . 'am/classes/class-wc-api-manager-passwords.php' );

		$lrm_api_manager_password_management = new LRM_API_Manager_Password_Management();

		// Generate a unique installation $instance id
		$instance = $lrm_api_manager_password_management->generate_password( 12, false );

		$single_options = array(
			$this->ame_product_id_key 			=> $this->ame_software_product_id,
			$this->ame_instance_key 			=> $instance,
			$this->ame_deactivate_checkbox_key 	=> 'on',
			$this->ame_activated_key 			=> 'Deactivated',
        );

		foreach ( $single_options as $key => $value ) {
			update_option( $key, $value );
		}

		$curr_ver = get_option( $this->lrm_api_manager_version_name );

		// checks if the current plugin version is lower than the version being installed
		if ( version_compare( $this->version, $curr_ver, '>' ) ) {
			// update the version
			update_option( $this->lrm_api_manager_version_name, $this->version );
		}

	}

	/**
	 * Deletes all data if plugin deactivated
	 * @return void
	 */
	public function uninstall() {
		global $wpdb, $blog_id;

		$this->license_key_deactivation();

		// Remove options
		if ( is_multisite() ) {

			switch_to_blog( $blog_id );

			foreach ( array(
					$this->ame_data_key,
					$this->ame_product_id_key,
					$this->ame_instance_key,
					$this->ame_deactivate_checkbox_key,
					$this->ame_activated_key,
					) as $option) {

					delete_option( $option );

					}

			restore_current_blog();

		} else {

			foreach ( array(
					$this->ame_data_key,
					$this->ame_product_id_key,
					$this->ame_instance_key,
					$this->ame_deactivate_checkbox_key,
					$this->ame_activated_key
					) as $option) {

					delete_option( $option );

					}

		}

	}

	/**
	 * Deactivates the license on the API server
	 * @return void
	 */
	public function license_key_deactivation() {

		$activation_status = get_option( $this->ame_activated_key );

		$api_email = $this->ame_options[$this->ame_activation_email];
		$api_key = $this->ame_options[$this->ame_api_key];

		$args = array(
			'email' => $api_email,
			'licence_key' => $api_key,
			);

		if ( $activation_status == 'Activated' && $api_key != '' && $api_email != '' ) {
			$this->key()->deactivate( $args ); // reset license key activation
		}
	}

    /**
     * Displays an inactive notice when the software is inactive.
     */
	public static function am_example_inactive_notice() { ?>
		<?php if ( ! current_user_can( 'manage_options' ) ) return; ?>
		<?php if ( isset( $_GET['page'] ) && 'lrm_api_manager_dashboard' == $_GET['page'] ) return; ?>
		<div id="message" class="error">
			<p><?php printf( __( 'The "AJAX Login and Registration modal PRO" API License Key has not been activated, so you can not get automatic updates! In the never versions this could disable PRO functionality!  %sClick here%s to activate the license key and the plugin.', 'ajax-login-and-registration-modal-popup' ), '<a href="' . esc_url( admin_url( 'options-general.php?page=lrm_api_manager_dashboard' ) ) . '">', '</a>' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Check for external blocking contstant
	 * @return string
	 */
	public function check_external_blocking() {
		// show notice if external requests are blocked through the WP_HTTP_BLOCK_EXTERNAL constant
		if( defined( 'WP_HTTP_BLOCK_EXTERNAL' ) && WP_HTTP_BLOCK_EXTERNAL === true ) {

			// check if our API endpoint is in the allowed hosts
			$host = parse_url( $this->upgrade_url, PHP_URL_HOST );

			if( ! defined( 'WP_ACCESSIBLE_HOSTS' ) || stristr( WP_ACCESSIBLE_HOSTS, $host ) === false ) {
				?>
				<div class="error">
					<p><?php printf( __( '<b>Warning!</b> You\'re blocking external requests which means you won\'t be able to get %s updates. Please add %s to %s.', $this->text_domain ), $this->ame_software_product_id, '<strong>' . $host . '</strong>', '<code>WP_ACCESSIBLE_HOSTS</code>'); ?></p>
				</div>
				<?php
			}

		}
	}

} // End of class

function LRM_API_Manager() {
    return LRM_API_Manager::instance();
}

// Initialize the class instance only once
//LRM_API_Manager();
