<?php
/**
 * WCFM plugin core
 *
 * Facebook Marketplace core
 *
 * @author 		WC Lovers
 * @package     wcfmu/core
 * @version     1.0.0
 * @since       1.0.0
 */

class WCFMu_Vendor_Facebook_Matketplace {

	/** @var string the sync products action */
	const ACTION_SYNC_PRODUCTS = 'wcfm_facebook_sync_products';

	/** @var string the get sync status action */
	const ACTION_GET_SYNC_STATUS = 'wcfm_facebook_get_sync_status';

	/** @var string the vendor user id */
	private $vendor_id;

	/** @var \WCFMu\Facebook\Handlers\Connection connection handler */
	private $connection_handler;

	/** @var \WCFMu\Facebook\Products\Sync products sync handler */
	private $products_sync_handler;

	/** @var \WCFMu\Facebook\Products\Sync\Background background sync handler */
	private $sync_background_handler;

	/** @var \WCFMu_Facebookcommerce_Integration instance */
	private $integration;

	/** @var SkyVerge\WooCommerce\Facebook\API instance */
	private $api;

	public function __construct() {
		global $WCFM, $WCFMu, $WCFMmp;

		$this->vendor_id   = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );

		$this->includes();

		if( !class_exists( '\\WCFMu\\Facebook\\Products\\Sync' ) || !class_exists( '\\WCFMu\\Facebook\\Products\\Sync\\Background' ) || !class_exists( '\\WCFMu\\Facebook\\Handlers\\Connection' ) ) return;

		$this->init();

		// load style
		add_action( 'wcfm_load_styles', array( &$this, 'wcfm_facebook_marketplace_load_styles' ) );

		// load script
		add_action( 'wcfm_load_scripts', array( &$this, 'wcfm_facebook_marketplace_load_scripts' ) );

        // WCFM Facebook Matketplace Endpoints
		add_filter( 'wcfm_query_vars', array( &$this, 'wcfmu_facebook_marketplace_wcfm_query_vars' ), 100 );

		add_filter( 'wcfm_endpoint_title', array( &$this, 'wcfmu_facebook_marketplace_endpoint_title' ), 100, 2 );

		add_action( 'init', array( &$this, 'wcfmu_facebook_marketplace_init' ), 100 );

        // WCFM Facebook Matketplace Endpoint Edit
		add_filter( 'wcfm_endpoints_slug', array( &$this, 'wcfmu_facebook_marketplace_endpoints_slug' ) );

		// WCFM Menu Filter
		add_filter( 'wcfm_menus', array( &$this, 'wcfmu_facebook_marketplace_menus' ), 305 );

		// Load views
		add_action( 'wcfm_load_views', array( &$this, 'wcfm_load_view_facebook_marketplace' ) );

		// webhook to capture connection success
		add_action( 'woocommerce_api_' . $this->get_connection_handler()::ACTION_CONNECT, array( &$this, 'handle_connect' ) );

		// Facebook Marketplace Settings Ajax Controllers
		add_action( 'after_wcfm_ajax_controller', array( &$this, 'wcfm_facebook_marketplace_settings_ajax_controller' ) );
	}

	/**
	 * Includes the necessary classes.
	 *
	 * @internal
	 */
	public function includes() {

		global $WCFMu;

		if( version_compare( facebook_for_woocommerce()::VERSION, '2.1.4' ) <= 0 ) {
			include_once $WCFMu->plugin_path . 'includes/facebook-marketplace/old/Handlers/Connection.php';
			include_once $WCFMu->plugin_path . 'includes/facebook-marketplace/old/Products/Sync/Background.php';
			include_once $WCFMu->plugin_path . 'includes/facebook-marketplace/old/class-wcfmu-facebookcommerce-integration.php';
		} else {
			include_once $WCFMu->plugin_path . 'includes/facebook-marketplace/Handlers/Connection.php';
			include_once $WCFMu->plugin_path . 'includes/facebook-marketplace/Products/Sync/Background.php';
			include_once $WCFMu->plugin_path . 'includes/facebook-marketplace/class-wcfmu-facebookcommerce-integration.php';
		}

		include_once $WCFMu->plugin_path . 'includes/facebook-marketplace/Products.php';
		include_once $WCFMu->plugin_path . 'includes/facebook-marketplace/Products/Sync.php';
	}

	/**
	 * Initializes the necessary classes.
	 *
	 * @internal
	 */
	public function init() {

		$this->products_sync_handler   = new \WCFMu\Facebook\Products\Sync();
		$this->sync_background_handler = new \WCFMu\Facebook\Products\Sync\Background();
		$this->connection_handler = new \WCFMu\Facebook\Handlers\Connection( $this );
		$this->connection_handler = $this->get_connection_handler( $this->vendor_id );
	}

	/**
	 * Gets the products sync handler.
	 *
	 * @since 1.0.0
	 *
	 * @return \WCFMu\Facebook\Products\Sync
	 */
	public function get_products_sync_handler( $vendor_id = 0 ) {

		if( $vendor_id ) $this->products_sync_handler->set_vendor_id( $vendor_id );

		return $this->products_sync_handler;
	}

	/**
	 * Gets the products sync background handler.
	 *
	 * @since 1.0.0
	 *
	 * @return \WCFMu\Facebook\Products\Sync\Background
	 */
	public function get_products_sync_background_handler() {

		return $this->sync_background_handler;
	}

	/**
	 * Gets the connection handler.
	 *
	 * @since 1.0.0
	 *
	 * @return Connection
	 */
	public function get_connection_handler( $vendor_id = 0 ) {

		if( $vendor_id ) $this->connection_handler->set_vendor_id( $vendor_id );

		return $this->connection_handler;
	}

	/**
	 * Gets the integration instance.
	 *
	 * @since 1.0.0
	 *
	 * @return \WCFMu_Facebookcommerce_Integration instance
	 */
	public function get_integration( $vendor_id = 0 ) {

		if ( null === $this->integration ) {
			$this->integration = new WCFMu_Facebookcommerce_Integration();
		}

		if( $vendor_id ) $this->integration->set_vendor_id( $vendor_id );

		return $this->integration;
	}

	/**
	 * Gets the API instance.
	 *
	 * @since 1.0.0
	 *
	 * @return \SkyVerge\WooCommerce\Facebook\API
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function get_api( $vendor_id ) {

		global $WCFMu;

		if ( ! $WCFMu->wcfmu_facebook_marketplace->get_connection_handler( $vendor_id )->get_access_token() ) {
			throw new Framework\SV_WC_API_Exception( __( 'Cannot create the API instance because the access token is missing.', 'facebook-for-woocommerce' ) );
		}

		if ( ! class_exists( API\Traits\Rate_Limited_API::class ) ) {
			require_once facebook_for_woocommerce()->get_plugin_path() . '/includes/API/Traits/Rate_Limited_API.php';
		}

		if ( ! class_exists( API\Traits\Rate_Limited_Request::class ) ) {
			require_once facebook_for_woocommerce()->get_plugin_path() . '/includes/API/Traits/Rate_Limited_Request.php';
		}

		if ( ! class_exists( API\Traits\Rate_Limited_Response::class ) ) {
			require_once facebook_for_woocommerce()->get_plugin_path() . '/includes/API/Traits/Rate_Limited_Response.php';
		}

		if ( ! trait_exists( API\Traits\Paginated_Response::class, false ) ) {
			require_once facebook_for_woocommerce()->get_plugin_path() . '/includes/API/Traits/Paginated_Response.php';
		}

		if ( ! class_exists( API::class ) ) {
			require_once facebook_for_woocommerce()->get_plugin_path() . '/includes/API.php';
		}

		if ( ! class_exists( API\Request::class ) ) {
			require_once facebook_for_woocommerce()->get_plugin_path() . '/includes/API/Request.php';
		}

		if ( ! class_exists( API\Response::class ) ) {
			require_once facebook_for_woocommerce()->get_plugin_path() . '/includes/API/Response.php';
		}

		if ( ! class_exists( API\Pixel\Events\Request::class ) ) {
			require_once facebook_for_woocommerce()->get_plugin_path() . '/includes/API/Pixel/Events/Request.php';
		}

		if ( ! class_exists( API\Business_Manager\Request::class ) ) {
			require_once facebook_for_woocommerce()->get_plugin_path() . '/includes/API/Business_Manager/Request.php';
		}

		if ( ! class_exists( API\Business_Manager\Response::class ) ) {
			require_once facebook_for_woocommerce()->get_plugin_path() . '/includes/API/Business_Manager/Response.php';
		}

		if ( ! class_exists( API\Catalog\Request::class ) ) {
			require_once facebook_for_woocommerce()->get_plugin_path() . '/includes/API/Catalog/Request.php';
		}

		if ( ! class_exists( API\Catalog\Response::class ) ) {
			require_once facebook_for_woocommerce()->get_plugin_path() . '/includes/API/Catalog/Response.php';
		}

		if ( ! class_exists( API\Catalog\Send_Item_Updates\Request::class ) ) {
			require_once facebook_for_woocommerce()->get_plugin_path() . '/includes/API/Catalog/Send_Item_Updates/Request.php';
		}

		if ( ! class_exists( API\Catalog\Send_Item_Updates\Response::class ) ) {
			require_once facebook_for_woocommerce()->get_plugin_path() . '/includes/API/Catalog/Send_Item_Updates/Response.php';
		}

		if ( ! class_exists( API\Catalog\Product_Group\Products\Read\Request::class ) ) {
			require_once facebook_for_woocommerce()->get_plugin_path() . '/includes/API/Catalog/Product_Group/Products/Read/Request.php';
		}

		if ( ! class_exists( API\Catalog\Product_Group\Products\Read\Response::class ) ) {
			require_once facebook_for_woocommerce()->get_plugin_path() . '/includes/API/Catalog/Product_Group/Products/Read/Response.php';
		}

		if ( ! class_exists( API\Catalog\Product_Item\Response::class ) ) {
			require_once facebook_for_woocommerce()->get_plugin_path() . '/includes/API/Catalog/Product_Item/Response.php';
		}

		if ( ! class_exists( API\Catalog\Product_Item\Find\Request::class ) ) {
			require_once facebook_for_woocommerce()->get_plugin_path() . '/includes/API/Catalog/Product_Item/Find/Request.php';
		}

		if ( ! class_exists( API\User\Request::class ) ) {
			require_once facebook_for_woocommerce()->get_plugin_path() . '/includes/API/User/Request.php';
		}

		if ( ! class_exists( API\User\Response::class ) ) {
			require_once facebook_for_woocommerce()->get_plugin_path() . '/includes/API/User/Response.php';
		}

		if ( ! class_exists( API\User\Permissions\Delete\Request::class ) ) {
			require_once facebook_for_woocommerce()->get_plugin_path() . '/includes/API/User/Permissions/Delete/Request.php';
		}

		if ( ! class_exists( API\FBE\Installation\Request::class ) ) {
			require_once facebook_for_woocommerce()->get_plugin_path() . '/includes/API/FBE/Installation/Request.php';
		}

		if ( ! class_exists( API\FBE\Installation\Read\Request::class ) ) {
			require_once facebook_for_woocommerce()->get_plugin_path() . '/includes/API/FBE/Installation/Read/Request.php';
		}

		if ( ! class_exists( API\FBE\Installation\Read\Response::class ) ) {
			require_once facebook_for_woocommerce()->get_plugin_path() . '/includes/API/FBE/Installation/Read/Response.php';
		}

		if ( ! class_exists( API\FBE\Configuration\Request::class ) ) {
			require_once facebook_for_woocommerce()->get_plugin_path() . '/includes/API/FBE/Configuration/Request.php';
		}

		if ( ! class_exists( API\FBE\Configuration\Messenger::class ) ) {
			require_once facebook_for_woocommerce()->get_plugin_path() . '/includes/API/FBE/Configuration/Messenger.php';
		}

		if ( ! class_exists( API\FBE\Configuration\Read\Response::class ) ) {
			require_once facebook_for_woocommerce()->get_plugin_path() . '/includes/API/FBE/Configuration/Read/Response.php';
		}

		if ( ! class_exists( API\FBE\Configuration\Update\Request::class ) ) {
			require_once facebook_for_woocommerce()->get_plugin_path() . '/includes/API/FBE/Configuration/Update/Request.php';
		}

		if ( ! class_exists( API\Pages\Read\Request::class ) ) {
			require_once facebook_for_woocommerce()->get_plugin_path() . '/includes/API/Pages/Read/Request.php';
		}

		if ( ! class_exists( API\Pages\Read\Response::class ) ) {
			require_once facebook_for_woocommerce()->get_plugin_path() . '/includes/API/Pages/Read/Response.php';
		}

		if ( ! class_exists( API\Exceptions\Request_Limit_Reached::class ) ) {
			require_once facebook_for_woocommerce()->get_plugin_path() . '/includes/API/Exceptions/Request_Limit_Reached.php';
		}

		$this->api = new SkyVerge\WooCommerce\Facebook\API( $this->get_connection_handler( $vendor_id )->get_access_token() );

		return $this->api;
	}

	/**
	 * load styles
	 */
	 public function wcfm_facebook_marketplace_load_styles( $end_point ) {
		 global $WCFMu;

		 if( 'wcfm-facebook-marketplace' == $end_point ) {
			 wp_register_style(
	 			 'facebook-marketplace-style',
	 			 $WCFMu->library->css_lib_url . 'facebook-marketplace/wcfmu-style-facebook-marketplace.css',
	 			 array(),
	 			 $WCFMu->version
	 		 );

			 wp_enqueue_style( 'facebook-marketplace-style' );
		 }
	 }

	 /**
 	 * load scripts
 	 */
 	 public function wcfm_facebook_marketplace_load_scripts( $end_point ) {
 		 global $WCFM, $WCFMu;

		 if( 'wcfm-facebook-marketplace' == $end_point ) {
			 wp_register_script(
				 'facebook-marketplace-settings-script',
				 $WCFMu->library->js_lib_url . 'facebook-marketplace/wcfmu-script-facebook-marketplace-settings.js',
				 array( 'jquery' ),
				 $WCFMu->version,
				 true
			 );

			 wp_localize_script( 'facebook-marketplace-settings-script', 'wcfm_facebook_marketplace_setting_options', array( 'default_tab' => apply_filters( 'wcfm_facebook_marketplace_setting_default_tab', 'wcfm_facebook_marketplace_settings_connection_head' ) ) );

			 $WCFM->library->load_collapsible_lib();
			 $WCFM->library->load_select2_lib();
			 wp_enqueue_script( 'facebook-marketplace-settings-script' );

			 wp_register_script(
				'facebook-marketplace-sync-script',
				$WCFMu->library->js_lib_url . 'facebook-marketplace/wcfmu-script-facebook-marketplace-sync.js',
				array( 'jquery' ),
				$WCFMu->version,
				true
			);

			wp_enqueue_script( 'facebook-marketplace-sync-script' );

			/* translators: Placeholders: {count} number of remaining items */
			$sync_remaining_items_string = _n_noop( '{count} item remaining.', '{count} items remaining.', 'wc-frontend-manager-ultimate' );

			wp_localize_script( 'facebook-marketplace-sync-script', 'facebook_marketplace_sync_options', array(
				'ajax_url'                        => admin_url( 'admin-ajax.php' ),
				'set_excluded_terms_prompt_nonce' => wp_create_nonce( 'set-excluded-terms-prompt' ),
				'sync_products_nonce'             => wp_create_nonce( self::ACTION_SYNC_PRODUCTS ),
				'sync_status_nonce'               => wp_create_nonce( self::ACTION_GET_SYNC_STATUS ),
				'sync_in_progress'                => $this->get_products_sync_handler()::is_sync_in_progress(),
				'excluded_category_ids'           => $this->get_integration( $this->vendor_id )->get_excluded_product_category_ids(),
				'excluded_tag_ids'                => $this->get_integration( $this->vendor_id )->get_excluded_product_tag_ids(),
				'i18n'                            => array(
					/* translators: Placeholders %s - html code for a spinner icon */
					'confirm_resync'                => esc_html__( 'Your products will now be resynced to Facebook, this may take some time.', 'wc-frontend-manager-ultimate' ),
					'confirm_sync'                  => esc_html__( "Facebook for WooCommerce automatically syncs your products on create/update. Are you sure you want to force product resync?\n\nThis will query all published products and may take some time. You only need to do this if your products are out of sync or some of your products did not sync.", 'wc-frontend-manager-ultimate' ),
					'sync_in_progress'              => sprintf( esc_html__( 'Your products are syncing - you may safely leave this page %s', 'wc-frontend-manager-ultimate' ), '<span class="spinner is-active"></span>' ),
					'sync_remaining_items_singular' => sprintf( esc_html( translate_nooped_plural( $sync_remaining_items_string, 1 ) ), '<strong>', '</strong>', '<span class="spinner is-active"></span>' ),
					'sync_remaining_items_plural'   => sprintf( esc_html( translate_nooped_plural( $sync_remaining_items_string, 2 ) ), '<strong>', '</strong>', '<span class="spinner is-active"></span>' ),
					'general_error'                 => esc_html__( 'There was an error trying to sync the products to Facebook.', 'wc-frontend-manager-ultimate' ),
					'feed_upload_error'             => esc_html__( 'Something went wrong while uploading the product information, please try again.', 'wc-frontend-manager-ultimate' ),
				),
			) );
		 }
 	 }

    /**
     * WCFM Facebook Matketplace Query Var
     */
	public function wcfmu_facebook_marketplace_wcfm_query_vars( $query_vars ) {
		$wcfm_modified_endpoints = get_option( 'wcfm_endpoints', array() );

		$query_facebook_marketplace_vars = array(
			'wcfm-facebook-marketplace'  => ! empty( $wcfm_modified_endpoints['wcfm-facebook-marketplace'] ) ? $wcfm_modified_endpoints['wcfm-facebook-marketplace'] : 'facebook-marketplace',
		);

		$query_vars = array_merge( $query_vars, $query_facebook_marketplace_vars );

		return $query_vars;
	}

    /**
     * WCFM Facebook Matketplace Endpoint Title
     */
    public function wcfmu_facebook_marketplace_endpoint_title( $title, $endpoint ) {
    	switch ( $endpoint ) {
    		case 'wcfm-facebook-marketplace' :
        		$title = __( 'Facebook for Matketplace', 'wc-frontend-manager-ultimate' );
        		break;
    	}

    	return $title;
    }

    /**
     * WCFM Facebook Matketplace Endpoint Intialize
     */
    public function wcfmu_facebook_marketplace_init() {
    	global $WCFM_Query;

  		// Intialize WCFM End points
    	$WCFM_Query->init_query_vars();
    	$WCFM_Query->add_endpoints();

    	if( !get_option( 'wcfm_updated_end_point_facebook_marketplace' ) ) {
  			// Flush rules after endpoint update
    		flush_rewrite_rules();
    		update_option( 'wcfm_updated_end_point_facebook_marketplace', 1 );
    	}
    }

    /**
  	 * WCFM Facebook Marketplace Endpoint Edit
  	 */
    public function wcfmu_facebook_marketplace_endpoints_slug( $endpoints ) {
    	$wcfmu_facebook_marketplace_endpoints = array(
    		'wcfm-facebook-marketplace' => 'facebook-marketplace',
    	);

    	$endpoints = array_merge( $endpoints, $wcfmu_facebook_marketplace_endpoints );

    	return $endpoints;
    }

	/**
     * WCFM Facebook Matketplace Menu
     */
    function wcfmu_facebook_marketplace_menus( $menus ) {
    	global $WCFM;

    	if( apply_filters( 'wcfm_is_facebook_marketplace', true ) && wcfm_is_vendor() ) {
			if( ! isset( $menus['facebook-marketplace'] ) ) {
	    		$menus['facebook-marketplace'] = array(
					'label'      => __( 'Facebook for Matketplace', 'wc-frontend-manager-ultimate'),
	    			'url'        => get_wcfm_facebook_marketplace_url(),
	    			'icon'       => 'store',
	    			'priority'   => 6
	    		);
			}
    	}

    	return $menus;
    }

	/**
	 * Load Facebook Matketplace Settings
	 */
	public function wcfm_load_view_facebook_marketplace( $end_point ) {
		global $WCFMu;

		if( 'wcfm-facebook-marketplace' == $end_point ) {
			$WCFMu->template->get_template( 'integrations/facebook-marketplace/wcfmu-view-facebook-marketplace.php' );
		}
	}

	/**
	 * Processes the returned connection.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function handle_connect() {

		$vendor_id = ! empty( $_GET['vendor_id'] ) ? sanitize_text_field( $_GET['vendor_id'] ) : 0;

		// don't handle anything unless the user can manage WooCommerce settings
		if ( ! user_can( $vendor_id, 'manage_woocommerce' ) ) {
			return;
		}

		try {

			if( ! $vendor_id ) {
				throw new SV_WC_API_Exception( 'Invalid Vendor Id' );
			}

			if ( empty( $_GET['nonce'] ) || ! wp_verify_nonce( $_GET['nonce'], $this->get_connection_handler( $vendor_id )::ACTION_CONNECT ) ) {
				throw new SV_WC_API_Exception( 'Invalid nonce' );
			}

			$merchant_access_token = ! empty( $_GET['merchant_access_token'] ) ? sanitize_text_field( $_GET['merchant_access_token'] ) : '';

			if ( ! $merchant_access_token ) {
				throw new SV_WC_API_Exception( 'Access token is missing' );
			}

			$system_user_access_token = ! empty( $_GET['system_user_access_token'] ) ? sanitize_text_field( $_GET['system_user_access_token'] ) : '';

			if ( ! $system_user_access_token ) {
				throw new SV_WC_API_Exception( 'System User access token is missing' );
			}

			$system_user_id = ! empty( $_GET['system_user_id'] ) ? sanitize_text_field( $_GET['system_user_id'] ) : '';

			if ( ! $system_user_id ) {
				throw new SV_WC_API_Exception( 'System User ID is missing' );
			}

			$this->get_connection_handler( $vendor_id )->update_access_token( $system_user_access_token );
			$this->get_connection_handler( $vendor_id )->update_merchant_access_token( $merchant_access_token );
			$this->get_connection_handler( $vendor_id )->update_system_user_id( $system_user_id );
			$this->update_installation_data( $vendor_id );

			//facebook_for_woocommerce()->get_products_sync_handler()->create_or_update_all_products();

			wcfm_fb_log(__( 'Connection complete! Thanks for using Facebook for WooCommerce.', 'wc-frontend-manager-ultimate' ));

		} catch ( SV_WC_API_Exception $exception ) {

			wcfm_fb_log(sprintf( 'Connection failed: %s', $exception->getMessage() ));

			set_transient( 'wcfm_facebook_connection_failed', time(), 30 );
		}

		wp_safe_redirect( wcfm_get_endpoint_url( 'wcfm-facebook-marketplace', '', get_wcfm_page() ) );
		exit;
	}

	/**
	 * Retrieves and stores the connected installation data.
	 *
	 * @since 1.0.0
	 *
	 * @throws SV_WC_API_Exception
	 */
	private function update_installation_data( $vendor_id ) {

		$response = $this->get_api( $vendor_id )->get_installation_ids( $this->get_connection_handler( $vendor_id )->get_external_business_id() );

		if ( $response->get_page_id() ) {
			$this->get_integration( $vendor_id )->update_facebook_page_id( sanitize_text_field( $response->get_page_id() ) );
		}

		if ( $response->get_pixel_id() ) {
			$this->get_integration( $vendor_id )->update_facebook_pixel_id( sanitize_text_field( $response->get_pixel_id() ) );
		}

		if ( $response->get_catalog_id() ) {
			$this->get_integration( $vendor_id )->update_product_catalog_id( sanitize_text_field( $response->get_catalog_id() ) );
		}

		if ( $response->get_business_manager_id() ) {
			$this->get_connection_handler( $vendor_id )->update_business_manager_id( sanitize_text_field( $response->get_business_manager_id() ) );
		}

		if ( $response->get_ad_account_id() ) {
			$this->get_connection_handler( $vendor_id )->update_ad_account_id( sanitize_text_field( $response->get_ad_account_id() ) );
		}
	}

	/**
	 * Facebook Marketplace Settings Ajax Controllers
	 */
	public function wcfm_facebook_marketplace_settings_ajax_controller() {
		global $WCFMu;

		$controllers_path = $WCFMu->plugin_path . 'controllers/integrations/facebook-marketplace/';

		$controller = '';
		if( isset( $_POST['controller'] ) || $_GET['controller'] ) {
			$controller = wc_clean( $_POST['controller'] ) ? wc_clean( $_POST['controller'] ) : wc_clean( $_GET['controller'] );

			switch( $controller ) {
				case 'wcfm-facebook-marketplace-settings':
					include_once( $controllers_path . 'wcfmu-controller-facebook-marketplace-settings.php' );
					new WCFMu_Facebook_Marketplace_Settings_Controller();
				break;

				case 'wcfm-facebook-marketplace-sync-products':
					include_once( $controllers_path . 'wcfmu-controller-facebook-marketplace-sync-products.php' );
					new WCFMu_Facebook_Marketplace_Sync_Products_Controller();
				break;

				case 'wcfm-facebook-marketplace-get-sync-status':
					include_once( $controllers_path . 'wcfmu-controller-facebook-marketplace-get-sync-status.php' );
					new WCFMu_Facebook_Marketplace_Get_Sync_Status_Controller();
				break;

				case 'wcfm-facebook-marketplace-disconnect':
					include_once( $controllers_path . 'wcfmu-controller-facebook-marketplace-disconnect.php' );
					new WCFMu_Facebook_Marketplace_Disconnect_Controller();
				break;
			}
		}
	}
}