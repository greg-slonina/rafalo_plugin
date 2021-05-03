<?php

/**
 * WCFM Elementor plugin core
 *
 * Plugin intiate
 *
 * @author 		WC Lovers
 * @package 	wc-frontend-manager-elementor
 * @version   1.0.0
 */
 
class WCFM_Elementor {
	
	public $plugin_base_name;
	public $plugin_url;
	public $plugin_path;
	public $version;
	public $token;
	public $text_domain;
	
	public function __construct($file) {

		$this->file = $file;
		$this->plugin_base_name = plugin_basename( $file );
		$this->plugin_url = trailingslashit(plugins_url('', $plugin = $file));
		$this->plugin_path = trailingslashit(dirname($file));
		$this->token = WCFMem_TOKEN;
		$this->text_domain = WCFMem_TEXT_DOMAIN;
		$this->version = WCFMem_VERSION;
		
		add_action( 'init', array( &$this, 'init' ), 8 );
		
		add_action( 'elementor_pro/init', array( &$this, 'wcfmem_init' ) );
	}
	
	function init() {
		// Store Template Loader
		//$this->load_class( 'store-template' );
	  //new WCFMem_Store_Template();
	}
	
	function wcfmem_init() {
		global $WCFM, $WCFMem;
		
		// Init Text Domain
		$this->load_plugin_textdomain();
		
		require_once $WCFMem->plugin_path . 'includes/Abstracts/ModuleBase.php';
		require_once $WCFMem->plugin_path . 'includes/Abstracts/DataTagBase.php';
		require_once $WCFMem->plugin_path . 'includes/Abstracts/TagBase.php';
		
		require_once $WCFMem->plugin_path . 'includes/Conditions/Store.php';
		require_once $WCFMem->plugin_path . 'includes/Documents/StorePage.php';
		
		require_once $WCFMem->plugin_path . 'includes/Controls/DynamicHidden.php';
		require_once $WCFMem->plugin_path . 'includes/Controls/SortableList.php';
		
		require_once $WCFMem->plugin_path . 'includes/Traits/WCFM_Elementor_Position_Controls.php';
		
		add_action( 'elementor/elements/categories_registered', [ &$this, 'wcfmem_categories' ] );
		
		// Templates
		$this->load_class( 'templates' );
	  new WCFM_Elementor_Templates();
	  
	  // Module
		$this->load_class( 'module' );
	  new WCFM_Elementor_Module();
	}
	
	public function wcfmem_elementor() {
		return \Elementor\Plugin::instance();
	}

	/**
	 * Is editing or preview mode running
	 *
	 * @return bool
	 */
	public function is_edit_or_preview_mode() {
		$is_edit_mode    = $this->wcfmem_elementor()->editor->is_edit_mode();
		$is_preview_mode = $this->wcfmem_elementor()->preview->is_preview_mode();

		if ( empty( $is_edit_mode ) && empty( $is_preview_mode ) ) {
			if ( ! empty( $_REQUEST['action'] ) && ! empty( $_REQUEST['editor_post_id'] ) ) {
				$is_edit_mode = true;
			} else if ( ! empty( $_REQUEST['preview'] ) && $_REQUEST['preview'] && ! empty( $_REQUEST['theme_template_id'] ) ) {
				$is_preview_mode = true;
			}
		}

		if ( $is_edit_mode || $is_preview_mode ) {
			return true;
		}

		return false;
	}

	/**
	 * Default store data for widgets
	 *
	 * @param string $prop
	 *
	 * @return mixed
	 */
	public function get_wcfmem_store_data( $prop = null ) {
		$this->load_class( 'store-data' );
	  $default_store_data = new WCFM_Elementor_StoreData();

		return $default_store_data->get_data( $prop );
	}
	
	/**
	 * Social network name mapping to elementor icon names
	 *
	 * @return array
	 */
	public function get_social_networks_map() {
			$map = [
					'fb'        => 'fab fa-facebook',
					'gplus'     => 'fab fa-google-plus',
					'twitter'   => 'fab fa-twitter',
					'pinterest' => 'fab fa-pinterest',
					'linkedin'  => 'fab fa-linkedin-in',
					'youtube'   => 'fab fa-youtube',
					'instagram' => 'fab fa-instagram',
					'flickr'    => 'fab fa-flickr',
			];

			return apply_filters( 'wcfmem_elementor_social_network_map', $map );
	}
	
	/**
	 * Register Elementor "WCFM Marketplace" category
	 */
	function wcfmem_categories( $elements_manager ) {
		global $WCFM, $WCFMem;
		
		$elements_manager->add_category(
			'wcfmem-store-elements-single',
			[
				'title' => __( 'Marketplace', 'wc-frontend-manager-elementor' ),
				'icon' => 'fa fa-plug',
			]
		);
	}
	
	/**
	 * Load Localisation files.
	 *
	 * Note: the first-loaded translation file overrides any following ones if the same translation is present
	 *
	 * @access public
	 * @return void
	 */
	public function load_plugin_textdomain() {
		$locale = function_exists( 'get_user_locale' ) ? get_user_locale() : get_locale();
		$locale = apply_filters( 'plugin_locale', $locale, 'wc-frontend-manager-elementor' );
		
		//load_plugin_textdomain( 'wcfm-tuneer-orders' );
		//load_textdomain( 'wc-frontend-manager-elementor', WP_LANG_DIR . "/wc-frontend-manager-elementor/wc-frontend-manager-elementor-$locale.mo");
		load_textdomain( 'wc-frontend-manager-elementor', $this->plugin_path . "lang/wc-frontend-manager-elementor-$locale.mo");
		load_textdomain( 'wc-frontend-manager-elementor', ABSPATH . "wp-content/languages/plugins/wc-frontend-manager-elementor-$locale.mo");
	}
	
	public function load_class($class_name = '') {
		if ('' != $class_name && '' != $this->token) {
			require_once ($this->plugin_path . 'includes/class-' . esc_attr($this->token) . '-' . esc_attr($class_name) . '.php');
		} // End If Statement
	}
}