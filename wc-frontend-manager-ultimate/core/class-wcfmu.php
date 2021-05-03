<?php

class WCFMu {

	public $plugin_base_name;
	public $plugin_url;
	public $plugin_path;
	public $version;
	public $token;
	public $text_domain;
	public $library;
	public $shortcode;
	public $admin;
	public $frontend;
	public $template;
	public $ajax;
	public $file;
	public $settings;
	public $license;
	public $wcfmu_fields;
	public $is_marketplace;
	public $wcfmu_marketplace;
	public $wcfmu_vendor_support;
	public $wcfmu_capability;
	public $wcfmu_orders;
	public $wcfmu_wcbooking;
	public $wcfmu_tychbooking;
	public $wcfmu_wcsubscriptions;
	public $wcfmu_xasubscriptions;
	public $wcfmu_wcappointments;
	public $wcfmu_wcfancyproducts;
	public $wcfmu_integrations;
	public $wcfmu_customfield_support;
	public $wcfmu_wcaddons;
	public $wcfmu_wcaccommodation;
	public $wcfmu_non_ajax;
	public $wcfmu_preferences;
	public $wcfmu_sitepress_wpml;
	public $bulk_edit;
	public $vendor_badges;
	public $vendor_verification;
	public $vendor_verification_auth;
	public $wcfmu_reviews;
	public $wcfmu_support;
	public $wcfmu_shipment_tracking;
	public $wcfmu_custom_validation;
	public $wcfmu_vendor_followers;
	public $wcfmu_vendor_vacation;
	public $wcfmu_vendor_invoice;
	public $wcfmu_vendor_chatbox;
	public $wcfmu_shipstation;
	public $wcfmu_facebook_marketplace;
	public $wcfmu_dokan_subscription;
	public $wcfm_has_vacation = false;
	public $wcfm_listing_product_loaded = false;

	public function __construct($file) {

		$this->file = $file;
		$this->plugin_base_name = plugin_basename( $file );
		$this->plugin_url = trailingslashit(plugins_url('', $plugin = $file));
		$this->plugin_path = trailingslashit(dirname($file));
		$this->token = WCFMu_TOKEN;
		$this->text_domain = WCFMu_TEXT_DOMAIN;
		$this->version = WCFMu_VERSION;

		add_action( 'wcfm_init', array( &$this, 'init_wcfmu' ), 12 );

		add_action( 'wp', array( &$this, 'wcfmu_init_after_wp' ), 500 );

		add_action( 'plugins_loaded', array( $this, 'wca_includes' ), 10 );

		add_filter( 'wcfm_modules',  array( &$this, 'get_wcfmu_modules' ), 20 );
	}

	function wca_includes() {
		if( WCFMu_Dependencies::wcfm_wc_appointments_active_check() ) {
			if ( ! class_exists( 'WC_Appointments_Integration_GCal' ) ) {
				include_once $this->plugin_path . 'includes/appointments_gcal/class-wcfm-appointments-integration-gcal.php';
			}
		}

		if ( WCFMu_Dependencies::wcfm_wc_pdf_voucher_active_check() ) {
			add_filter( 'woo_vou_edit_vendor_role', array( &$this, 'wcfm_woo_vou_edit_vendor_role' ), 180 );
		}
	}

	/**
	* initilize plugin on WCFM init
	*/
	function init_wcfmu() {
		global $WCFM, $WCFMu;

		// Init Text Domain
		$this->load_plugin_textdomain();

		if( ( version_compare( WC_VERSION, '3.0', '<' ) ) ) {
			//add_action( 'admin_notices', 'wcfm_woocommerce_version_notice' );
			return;
		}

		//if (!is_admin() || defined('DOING_AJAX')) {
		$this->load_class( 'preferences' );
		$this->wcfmu_preferences = new WCFMu_Preferences();
		//}

		//if ( !is_admin() || defined('DOING_AJAX')) {
		$this->load_class( 'capability' );
		$this->wcfmu_capability = new WCFMu_Capability();
		//}

		if ( !is_admin() || defined('DOING_AJAX') ) {
			if( WCFMu_Dependencies::wcfm_sitepress_wpml_active_check() ) {
				if( apply_filters( 'wcfm_is_allow_product_wpml', true ) ) {
					$this->load_class( 'sitepress-wpml' );
					$this->wcfmu_sitepress_wpml = new WCFMu_Sitepress_WPML();
				}
			}
		}

		// Check Marketplace
		$this->is_marketplace = wcfm_is_marketplace();
		if ( $this->is_marketplace ) {
			$this->load_class( 'vendor-support' );
			$this->wcfmu_vendor_support = new WCFMu_Vendor_Support();
		}

		if (!is_admin() || defined('DOING_AJAX') || defined('WCFM_REST_API_CALL') ) {
			if( $this->is_marketplace ) {
				if( wcfm_is_vendor()) {
					$this->load_class( $this->is_marketplace );
					if( $this->is_marketplace == 'wcvendors' ) $this->wcfmu_marketplace = new WCFMu_WCVendors();
					if( $this->is_marketplace == 'wcpvendors' ) $this->wcfmu_marketplace = new WCFMu_WCPVendors();
					if( $this->is_marketplace == 'wcmarketplace' ) $this->wcfmu_marketplace = new WCFMu_WCMarketplace();
					if( $this->is_marketplace == 'dokan' ) $this->wcfmu_marketplace = new WCFMu_Dokan();
					if( $this->is_marketplace == 'wcfmmarketplace' ) $this->wcfmu_marketplace = new WCFMu_Marketplace();
				}
			}
		}

		if( $this->is_marketplace && ( $this->is_marketplace == 'wcfmmarketplace' ) && apply_filters( 'wcfm_is_allow_orders', true ) && apply_filters( 'wcfm_is_allow_order_details', true ) && apply_filters( 'wcfm_is_allow_manage_order', true ) ) {
			$this->load_class( 'orders' );
			$this->wcfmu_orders = new WCFMu_Orders();
		}

		// Load Dokan Subscription Module
		if( $this->is_marketplace && in_array( $this->is_marketplace, array( 'dokan' ) ) ) {
			$this->load_class( 'dokan-subscription' );
			$this->wcfmu_dokan_subscription = new WCFMu_Dokan_Subscription();
		}

		// Load Reviews module
		if( apply_filters( 'wcfm_is_pref_vendor_reviews', true ) ) {
			if (!is_admin() || defined('DOING_AJAX')) {
				if( $this->is_marketplace && in_array( $this->is_marketplace, array( 'dokan' ) ) ) {
					$this->load_class( 'reviews' );
					$this->wcfmu_reviews = new WCFMu_Reviews();
				}
			}
		}

		// Check WC Booking
		if( wcfm_is_booking() ) {
			$this->load_class('wcbookings');
			$this->wcfmu_wcbooking = new WCFMu_WCBookings();
		}

		// Check Tych Booking
		if( WCFMu_Dependencies::wcfm_tych_booking_active_check() ) {
			if (!is_admin() || defined('DOING_AJAX')) {
				$this->load_class('tychbookings');
				$this->wcfmu_tychbooking = new WCFMu_TychBookings();
			}
		}

		// Check WC Subscription
		if( wcfm_is_subscription() ) {
			if (!is_admin() || defined('DOING_AJAX')) {
				$this->load_class('wcsubscriptions');
				$this->wcfmu_wcsubscriptions = new WCFMu_WCSubscriptions();
			}
		}

		// Check XA Subscription
		if( function_exists( 'wcfm_is_xa_subscription' ) && wcfm_is_xa_subscription() ) {
			if (!is_admin() || defined('DOING_AJAX')) {
				$this->load_class('xasubscriptions');
				$this->wcfmu_xasubscriptions = new WCFMu_XASubscriptions();
			}
		}

		// Check WC Appointments - 2.4.0
		if( WCFMu_Dependencies::wcfm_wc_appointments_active_check() ) {
			if (!is_admin() || defined('DOING_AJAX')) {
				$this->load_class('wcappointments');
				$this->wcfmu_wcappointments = new WCFMu_WCAppointments();
			}
		} else {
			delete_option( 'wcfm_updated_end_point_wc_appointments' );
		}

		// Check WC Fany Product Designer - 5.5.0
		if( $this->is_marketplace && ( $this->is_marketplace == 'wcfmmarketplace' ) ) {
			if( WCFMu_Dependencies::wcfm_wc_fancy_product_designer_active_check() ) {
				if (!is_admin() || defined('DOING_AJAX')) {
					$this->load_class('wcfancyproductdesigner');
					$this->wcfmu_wcfancyproducts = new WCFMu_WCFanyProductDesigner();
				}
			} else {
				delete_option( 'wcfm_updated_end_point_wc_fancyproductdesigner' );
			}
		}

		// Check WC Product Addons - 2.4.1
		if( apply_filters( 'wcfm_is_allow_products_addons', true ) ) {
			if( WCFMu_Dependencies::wcfm_wc_addons_active_check() || WCFMu_Dependencies::wcfm_wc_appointments_active_check() ) {
				if (!is_admin() || defined('DOING_AJAX')) {
					$this->load_class('wcaddons');
					$this->wcfmu_wcaddons = new WCFMu_WCAddons();
				}
			}
		}

		// Check WC Booking Accommodation - 2.4.4
		if( apply_filters( 'wcfm_is_allow_wc_accommodation', true ) ) {
			if( WCFMu_Dependencies::wcfm_wc_accommodation_active_check() ) {
				if (!is_admin() || defined('DOING_AJAX')) {
					$this->load_class('wcaccommodation');
					$this->wcfmu_wcaccommodation = new WCFM_WCAccommodation();
				}
			}
		}

		// Init Bulk Edit
		if( apply_filters( 'wcfm_is_allow_bulk_edit', true ) ) {
			if ( !is_admin() || defined('DOING_AJAX') ) {
				$this->load_class('bulk-edit');
				$this->bulk_edit = new WCFMu_Bulk_Edit();
			}
		}

		// Init library
		$this->load_class('library');
		$this->library = new WCFMu_Library();

		// Init ajax
		if (defined('DOING_AJAX')) {
			$this->load_class('ajax');
			$this->ajax = new WCFMu_Ajax();
		}

		if (!is_admin() || defined('DOING_AJAX')) {
			$this->load_class('frontend');
			$this->frontend = new WCFMu_Frontend();
		}

		if ( !is_admin() || defined('DOING_AJAX') ) {
			$this->load_class( 'integrations' );
			$this->wcfmu_integrations = new WCFMu_Integrations();
		}

		// Load WCFM Custom Field Module
		if( apply_filters( 'wcfm_is_pref_custom_field', true ) ) {
			if ( !is_admin() || defined('DOING_AJAX') ) {
				$this->load_class( 'customfield-support' );
				$this->wcfmu_customfield_support = new WCFMu_Custom_Field_Support();
			}
		}

		// Load Vendor Verification Module
		if( apply_filters( 'wcfm_is_pref_vendor_verification', true ) && apply_filters( 'wcfm_is_pref_profile', true ) ) {
			if (!is_admin() || defined('DOING_AJAX')) {
				if( wcfm_is_marketplace() ) {
					$this->load_class('vendor-verification');
					$this->vendor_verification = new WCFMu_Vendor_Verification();
				}
			}
		}

		// Load Vendor Badges Module
		if( apply_filters( 'wcfm_is_pref_vendor_badges', true ) ) {
			if (!is_admin() || defined('DOING_AJAX')) {
				if( $this->is_marketplace ) {
					$this->load_class('vendor-badges');
					$this->vendor_badges = new WCFMu_Vendor_Badges();
				}
			}
		}

		// Load Follower Module - 4.1.2
		if( apply_filters( 'wcfm_is_pref_vendor_followers', true ) ) {
			if ( !is_admin() || defined('DOING_AJAX') ) {
				if( $this->is_marketplace ) {
					$this->load_class( 'vendor-followers' );
					$this->wcfmu_vendor_followers = new WCFMu_Vendor_Followers();
				}
			}
		}

		// Load Vendor Vacation Module
		if( apply_filters( 'wcfm_is_pref_vendor_vacation', true ) ) {
			if ( !is_admin() || defined('DOING_AJAX') ) {
				if( $this->is_marketplace ) {
					$this->load_class( 'vendor-vacation' );
					$this->wcfmu_vendor_vacation = new WCFMu_Vendor_Vacation();
				}
			}
		}

		// Load Vendor Invoice Module
		if( apply_filters( 'wcfm_is_pref_vendor_invoice', true ) ) {
			$this->load_class( 'vendor-invoice' );
			$this->wcfmu_vendor_invoice = new WCFMu_Vendor_Invoice();
		}

		// Load Support Module
		if( apply_filters( 'wcfm_is_pref_support', true ) ) {
			if ( !is_admin() || defined('DOING_AJAX') || defined('WCFM_REST_API_CALL') ) {
				$this->load_class( 'support' );
				$this->wcfmu_support = new WCFMu_Support();
			}
		}

		// Load Chatbox Module
		if( apply_filters( 'wcfm_is_pref_chatbox', true ) ) {
			if ( !is_admin() || defined('DOING_AJAX') ) {
				$this->load_class( 'vendor-chatbox' );
				$this->wcfmu_vendor_chatbox = new WCFMu_Vendor_Chatbox();
			}
		}

		// Load Shipstation Module
		if( apply_filters( 'wcfm_is_pref_shipstation', true ) ) {
			if ( !is_admin() || defined('DOING_AJAX') ) {
				if( $this->is_marketplace && ( $this->is_marketplace == 'wcfmmarketplace' ) ) {
					$this->load_class( 'shipstation' );
					$this->wcfmu_shipstation = new WCFMu_Shipstation();
				}
			}
		}

		// Load Facebook Matketplace Module
		if( apply_filters( 'wcfm_is_pref_facebook_marketplace', true ) && WCFMu_Dependencies::wcfm_facebook_for_woocommerce_active_check() ) {
			if ( !is_admin() || defined('DOING_AJAX') ) {
				if( $this->is_marketplace && ( $this->is_marketplace == 'wcfmmarketplace' ) ) {
					$this->load_class( 'vendor-facebook-marketplace' );
					$this->wcfmu_facebook_marketplace = new WCFMu_Vendor_Facebook_Matketplace();
				}
			}
		}

		// Load Shipment Tracking Module
		if( apply_filters( 'wcfm_is_pref_shipment_tracking', true ) ) {
			if ( !is_admin() || defined('DOING_AJAX') ) {
				$this->load_class( 'shipment-tracking' );
				$this->wcfmu_shipment_tracking = new WCFMu_Shipment_Tracking();
			}
		}

		// Load Product Manager custom Restriction - 4.0.5
		if( apply_filters( 'wcfm_is_pref_custom_validation', true ) ) {
			if ( !is_admin() || defined('DOING_AJAX') ) {
				$this->load_class( 'custom-validation' );
				$this->wcfmu_custom_validation = new WCFMu_Custom_Validation();
			}
		}

		// DC License Activation
		if (is_admin()) {
			$this->load_class('license');
			$this->license = WCFMu_LICENSE();
		}

		if( !defined('DOING_AJAX') ) {
			$this->load_class( 'non-ajax' );
			$this->wcfmu_non_ajax = new WCFMu_Non_Ajax();
		}

		// template loader
		$this->load_class( 'template' );
		$this->template = new WCFMu_Template();

		$this->wcfmu_fields = $WCFM->wcfm_fields;

	}

	/**
	* Load Modules after WP fully loaded
	*/
	function wcfmu_init_after_wp() {
		// Load Vendor Verification Module
		if( apply_filters( 'wcfm_is_pref_vendor_verification', true ) && apply_filters( 'wcfm_is_pref_profile', true ) ) {
			if (!is_admin() || defined('DOING_AJAX')) {
				if( wcfm_is_marketplace() ) {
					$this->load_class('vendor-verification-auth');
					$this->vendor_verification_auth = new WCFMu_Vendor_Verification_Auth();
				}
			}
		}
	}

	/**
	* WC PDF Vouchers Vendor Role
	*/
	function wcfm_woo_vou_edit_vendor_role( $user_roles ) {
		$user_roles = array( 'adminstrator', 'wcfm_vendor', 'seller', 'vendor', WOO_VOU_VENDOR_ROLE );
		return $user_roles;
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
		$locale = apply_filters( 'plugin_locale', $locale, 'wc-frontend-manager-ultimate' );

		//load_textdomain( 'wc-frontend-manager-ultimate', WP_LANG_DIR . "/wc-frontend-manager-ultimate/wc-frontend-manager-ultimate-$locale.mo");
		load_textdomain( 'wc-frontend-manager-ultimate', $this->plugin_path . "lang/wc-frontend-manager-ultimate-$locale.mo");
		load_textdomain( 'wc-frontend-manager-ultimate', ABSPATH . "wp-content/languages/plugins/wc-frontend-manager-ultimate-$locale.mo");
	}

	public function load_class($class_name = '') {
		if ('' != $class_name && '' != $this->token) {
			require_once ('class-' . esc_attr($this->token) . '-' . esc_attr($class_name) . '.php');
		} // End If Statement
	}

	// End load_class()

	/**
	* Install upon activation.
	*
	* @access public
	* @return void
	*/
	static function activate_wcfm() {
		global $WCFM, $WCFMu;

		// License Activation
		$WCFMu->load_class('license');
		WCFMu_LICENSE()->activation();

		update_option('wcfmu_installed', 1);
	}

	/**
	* UnInstall upon deactivation.
	*
	* @access public
	* @return void
	*/
	static function deactivate_wcfm() {
		global $WCFM, $WCFMu;

		// License Deactivation
		$WCFMu->load_class('license');
		WCFMu_LICENSE()->uninstall();

		delete_option('wcfmu_installed');
	}

	/**
	* List of WCFMu modules
	*/
	function get_wcfmu_modules( $wcfm_modules ) {
		global $WCFM, $WCFMu;
		$wcfmu_modules = array(
			'product_import'        => array( 'label' => __( 'Product Import', 'wc-frontend-manager' ), 'notice' => true ),
			'bulk_stock_manager'    => array( 'label' => __( 'Bulk Stock Manager', 'wc-frontend-manager' ) ),
			'shipment_tracking'     => array( 'label' => __( 'Shipment Tracking', 'wc-frontend-manager-ultimate' ) ),
			'support'             	=> array( 'label' => __( 'Support Ticket', 'wc-frontend-manager-ultimate' ) ),
			'vendor_invoice'        => array( 'label' => __( 'Vendor Invoice', 'wc-frontend-manager-ultimate' ) ),
			'vendor_vacation'       => array( 'label' => __( 'Vendor Vacation', 'wc-frontend-manager-ultimate' ) ),
			'vendor_verification'   => array( 'label' => __( 'Vendor Verification', 'wc-frontend-manager-ultimate' ) ),
			'vendor_badges'         => array( 'label' => __( 'Vendor Badges', 'wc-frontend-manager-ultimate' ) ),
			'vendor_followers'      => array( 'label' => __( 'Following / Followers', 'wc-frontend-manager-ultimate' ) ),
			'chatbox'             	=> array( 'label' => __( 'Chat Box', 'wc-frontend-manager-ultimate' ) ),
			//'custom_field'        => array( 'label' => __( 'Custom Field', 'wc-frontend-manager' ) ),
			//'submenu'             => array( 'label' => __( 'Sub-menu', 'wc-frontend-manager' ), 'hints' => __( 'This will disable `Add New` sub-menus on hover.', 'wc-frontend-manager' ) ),
		);

		if( $WCFMu->is_marketplace && ( $WCFMu->is_marketplace == 'wcfmmarketplace' ) ) {
			$wcfmu_modules['shipstation'] = array( 'label' => __( 'ShipStation', 'wc-frontend-manager-ultimate' ) );
			$wcfmu_modules['facebook_marketplace'] = array( 'label' => __( 'Facebook for Marketplace', 'wc-frontend-manager-ultimate' ) );
		}

		$wcfm_modules = array_merge( $wcfm_modules, $wcfmu_modules );
		return $wcfm_modules;
	}

}
