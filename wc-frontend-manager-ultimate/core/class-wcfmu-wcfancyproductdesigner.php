<?php

/**
 * WCFM plugin core
 *
 * Appointments WC Fany Product Designer Support
 *
 * @author 		WC Lovers
 * @package 	wcfm/core
 * @version   5.5.0
 */
 
class WCFMu_WCFanyProductDesigner {
	
	const REACT_NO_CONFLICT_JS = 'window.lodash = _.noConflict(); window.underscore = _.noConflict();';
	
	public function __construct() {
    global $WCFM, $WCFMu;
    
    if( apply_filters( 'wcfm_is_pref_fancy_product_designer' , true ) && apply_filters( 'wcfm_is_allow_fancy_product_designer' , true ) ) {
			if( WCFMu_Dependencies::wcfm_wc_fancy_product_designer_active_check() ) {
				// WCFM Appointments Query Var Filter
				add_filter( 'wcfm_query_vars', array( &$this, 'wcfpd_wcfm_query_vars' ), 90 );
				add_filter( 'wcfm_endpoint_title', array( &$this, 'wcfpd_wcfm_endpoint_title' ), 90, 2 );
				add_action( 'init', array( &$this, 'wcfpd_wcfm_init' ), 90 );
				
				// WCFM Appointments Endpoint Edit
				add_filter( 'wcfm_endpoints_slug', array( $this, 'wcfpd_wcfm_endpoints_slug' ) );
				
				// WCFM Menu Filter
				add_filter( 'wcfm_menus', array( &$this, 'wcfpd_wcfm_menus' ), 90 );
				
				// Appointments Load WCFMu Scripts
				add_action( 'after_wcfm_load_scripts', array( &$this, 'wcfpd_load_scripts' ), 90 );
				
				// Appointments Load WCFMu Styles
				add_action( 'after_wcfm_load_styles', array( &$this, 'wcfpd_load_styles' ), 90 );
				
				// Appointments Load WCFMu views
				add_action( 'wcfm_load_views', array( &$this, 'wcfpd_load_views' ), 90 );
				
				// Appointments Ajax Controllers
				add_action( 'after_wcfm_ajax_controller', array( &$this, 'wcfpd_ajax_controller' ) );
				
				// Appointments General Block
				add_action( 'end_wcfm_products_manage', array( &$this, 'wcfpd_product_manage_general' ), 90 );
				
				// Order Details Fancy View
				add_action( 'end_wcfm_orders_details', array( &$this, 'wcfpd_orders_details_load_views' ), 18 );
				
				// Vendor wise product filter
				add_filter( 'fpd_get_products_sql_attrs', array( &$this, 'wcfpd_get_products_sql_attrs' ) );
				add_filter( 'fpd_get_categories_sql_attrs', array( &$this, 'wcfpd_get_categories_sql_attrs' ) );
			}
		}
  }
  
  /**
   * WC Appointments Query Var
   */
  function wcfpd_wcfm_query_vars( $query_vars ) {
  	$wcfm_modified_endpoints = wcfm_get_option( 'wcfm_endpoints', array() );
  	
		$query_appointments_vars = array(
			'wcfm-fncy-product-designer'       => ! empty( $wcfm_modified_endpoints['wcfm-fncy-product-designer'] ) ? $wcfm_modified_endpoints['wcfm-fncy-product-designer'] : 'fncy-product-designer',
			'wcfm-fncy-product-builder'        => ! empty( $wcfm_modified_endpoints['wcfm-fncy-product-builder'] ) ? $wcfm_modified_endpoints['wcfm-fncy-product-builder'] : 'fncy-product-builder',
		);
		
		$query_vars = array_merge( $query_vars, $query_appointments_vars );
		
		return $query_vars;
  }
  
  /**
   * WC Fany Product Designer End Point Title
   */
  function wcfpd_wcfm_endpoint_title( $title, $endpoint ) {
  	global $WCFM, $WCFMu, $wp;
  	
  	switch ( $endpoint ) {
  		case 'wcfm-fncy-product-designer' :
				$title = __( 'Product Designer', 'wc-frontend-manager-ultimate' );
			break;
			case 'wcfm-fncy-product-builder' :
				$title = __( 'Product Builder', 'wc-frontend-manager-ultimate' );
			break;
  	}
  	
  	return $title;
  }
  
  /**
   * WC Fany Product Designer Endpoint Intialize
   */
  function wcfpd_wcfm_init() {
  	global $WCFM_Query;
	
		// Intialize WCFM End points
		$WCFM_Query->init_query_vars();
		$WCFM_Query->add_endpoints();
		
		if( !get_option( 'wcfm_updated_end_point_wc_fancyproductdesigner' ) ) {
			// Flush rules after endpoint update
			flush_rewrite_rules();
			update_option( 'wcfm_updated_end_point_wc_fancyproductdesigner', 1 );
		}
  }
  
  /**
	 * WC Fany Product Designer Endpoiint Edit
	 */
	function wcfpd_wcfm_endpoints_slug( $endpoints ) {
		
		$appointment_endpoints = array(
													'wcfm-fncy-product-designer'        => 'fncy-product-designer',
													'wcfm-fncy-product-builder'         => 'fncy-product-builder',
													);
		
		$endpoints = array_merge( $endpoints, $appointment_endpoints );
		
		return $endpoints;
	}
  
  /**
   * WC Fany Product Designer Menu
   */
  function wcfpd_wcfm_menus( $menus ) {
  	global $WCFM;
  	
		$menus = array_slice($menus, 0, 3, true) +
												array( 'wcfm-fncy-product-designer' => array(   'label'  => __( 'Product Designer', 'wc-frontend-manager-ultimate'),
																										 'url'        => get_wcfm_fncy_product_designer_url(),
																										 'icon'       => 'object-group',
																										 'priority'   => 20
																										) )	 +
													array_slice($menus, 3, count($menus) - 3, true) ;
		
  	return $menus;
  }
  
	/**
	* WC Fany Product Designer Scripts
	*/
  public function wcfpd_load_scripts( $end_point ) {
	  global $WCFM, $WCFMu, $wp;
	  
	  $fpd_admin_opts = array(
														'adminAjaxUrl' => admin_url('admin-ajax.php'),
														'ajaxNonce' => wp_create_nonce( 'fpd_ajax_nonce' ),
														//'ajaxNonce' => FPD_Admin::$ajax_nonce,
														'adminUrl' => admin_url(),
														'localTest' => Fancy_Product_Designer::LOCAL,
														'enterTitlePrompt' => __('Please enter a title!', 'radykal'),
														'tryAgain' => __('Something went wrong. Please try again!', 'radykal'),
														'addToLibrary' => __('Add imported image source to media library?', 'radykal'),
														'remove' => __('Do you really want to delete the item?', 'radykal'),
														'chooseThumbnail' => __('Choose Thumbnail', 'radykal'),
														'dialogCancel' => __('Cancel', 'radykal'),
														'dialogAlertButton' => __('Got It', 'radykal'),
														'dialogConfirmButton' => __('Yes', 'radykal'),
														'dialogConfirmCancel' => __('No', 'radykal'),
														'dialogPromptButton' => __('Okay', 'radykal'),
													);
    
	  switch( $end_point ) {
	  	case 'wcfm-products-manage':
	  		
	  		wp_enqueue_style( 'wp-color-picker' );
		    wp_enqueue_style( 'radykal-admin' );
				wp_enqueue_style( 'fpd-admin' );
				wp_enqueue_style( 'fpd-semantic-ui' );

				wp_enqueue_script( 'wp-color-picker' );
				wp_enqueue_script( 'radykal-admin' );
				wp_enqueue_script( 'fpd-admin' );
				wp_enqueue_script( 'fpd-semantic-ui' );

				wp_enqueue_script( 'radykal-select-sortable' );
				
				wp_localize_script( 'fpd-admin', 'fpd_admin_opts',$fpd_admin_opts );
	  		
				//wp_enqueue_script( 'wcfmu_wc_appointments_products_manage_js', $WCFMu->library->js_lib_url . 'wc_fncy_product_designer/wcfmu-script-wcappointments-products-manage.js', array( 'jquery' ), $WCFMu->version, true );
			break;
			
			case 'wcfm-orders-details':
				global $post_id;
				$order_id = 0;
				if( isset( $wp->query_vars['wcfm-orders-details'] ) && !empty( $wp->query_vars['wcfm-orders-details'] ) ) {
					$post_id = $order_id = $wp->query_vars['wcfm-orders-details'];
				}
				
				require_once( FPD_PLUGIN_ADMIN_DIR . '/labels/order-viewer.php' );
				
				wp_enqueue_script( 'wcfm-fpd-order-viewer', $WCFMu->library->js_lib_url . 'wc_fncy_product_designer/wcfmu-script-fancy-order-viewer.js', array(
					'fpd-semantic-ui',
					'fpd-admin',
					'jquery-fpd',
				), Fancy_Product_Designer::VERSION );

				$order_viewer_opts = array(
					'labels' => json_encode( FPD_Labels_Order_Viewer::get_labels() ),
					'templatesDirectory' => plugins_url('/assets/templates/', FPD_PLUGIN_ROOT_PHP ),
					'printReadyExportEnabled' => class_exists('Fancy_Product_Designer_Export'),
					'options' => array(
						'enabled_fonts' => json_decode(FPD_Fonts::to_json(FPD_Fonts::get_enabled_fonts())),
						'fpd_depositphotosApiKey' => fpd_get_option('fpd_depositphotosApiKey'),
						'fpd_depositphotosUsername' => fpd_get_option('fpd_depositphotosUsername'),
						'fpd_depositphotosPassword' => fpd_get_option('fpd_depositphotosPassword'),
						'fpd_depositphotosImageSize' => fpd_get_option('fpd_depositphotosImageSize'),
					)
				);
				
				wp_localize_script( 'fpd-admin', 'fpd_admin_opts', $fpd_admin_opts );
				
				wp_localize_script( 'wcfm-fpd-order-viewer', 'fpd_order_viewer_opts', $order_viewer_opts );
				
				wp_add_inline_script( 'wcfm-fpd-order-viewer', self::REACT_NO_CONFLICT_JS, 'after' );
				
				//js vars for order viewer
				/*wp_localize_script( 'wcfm-fpd-order-viewer', 'fpd_order_viewer', array(
						'order_id' 					=> intval($order_id),
						'templates_dir' 			=> plugins_url('/assets/templates/', FPD_PLUGIN_ROOT_PHP ),
						'enabled_fonts' 			=> FPD_Fonts::to_json(FPD_Fonts::get_enabled_fonts()),
						'loading_data_text' 		=> __( 'Loading data...', 'radykal' ),
						'order_data_error_text' 	=> __( 'Order data could not be loaded. Please try again!', 'radykal' ),
						'svg_bitmap_text' 			=> __( 'You cannot create an SVG file from a bitmap, you can only do this by using a text element or another SVG image file', 'radykal' ),
						'image_creation_fail_text' 	=> __( 'Image creation failed. Please try again!', 'radykal' ),
						'no_element_text' 			=> __('No element selected!', 'radykal'),
						'no_width_text' 			=> __( 'No width has been entered. Please set one!', 'radykal' ),
						'no_height_text' 			=> __( 'No height has been entered. Please set one!', 'radykal' ),
						'pdf_creation_fail_text' 	=> __( 'PDF creation failed - There is too much data being sent. To fix this please increase the WordPress memory limit in your php.ini file. You could export a single view or use the JPEG image format! ', 'radykal' ),
						'json_parse_error_text' 	=> __('JSON could not be parsed. Go to wp-content/fancy_products_orders/pdfs and check if a PDF has been generated.'),
						'no_fp_select_text' 		=> __( 'No Product is selected. Please load one from the Order Items table!', 'radykal' ),
						'popup_block_text' 			=> __( 'Your Pop-Up Blocker is enabled so the image will be opened in a new window. Please choose to allow this website in your pop-up blocker!', 'radykal' ),
						'load_order_error_text' 	=> __( 'Could not load order item image. Please try again!', 'radykal' ),
						'hexNames' => FPD_Settings_Colors::get_hex_names_object_string(),
						'order_type' => 'gf',
						'dp_api_key' => fpd_get_option('fpd_depositphotosApiKey'),
						'dp_username' => fpd_get_option('fpd_depositphotosUsername'),
						'dp_password' => fpd_get_option('fpd_depositphotosPassword'),
						'dp_image_size' => fpd_get_option('fpd_depositphotosImageSize'),
						'uploadZonesTopped' => fpd_get_option('fpd_uploadZonesTopped'),
						'automated_export_enabled' => empty(fpd_get_option('fpd_ae_admin_api_key')) ? 0 : 1
					)
				);*/
			break;
			
			case 'wcfm-fncy-product-designer':
      	require_once( FPD_PLUGIN_ADMIN_DIR . '/labels/products.php' );

				wp_enqueue_media();
				wp_enqueue_script( 'fpd-admin' );
			    
				wp_enqueue_script( 'wcfm-fpd-manage-fancy-products', $WCFMu->library->js_lib_url . 'wc_fncy_product_designer/wcfmu-script-manage-fancy-products-designer.js', array(
					'jquery-ui-core',
					'jquery-ui-mouse',
					'jquery-ui-sortable',
					'jquery-ui-droppable',
					'fpd-semantic-ui'
				), Fancy_Product_Designer::VERSION );
				
				wp_localize_script( 'wcfm-fpd-manage-fancy-products', 'fpd_fancy_products_opts', array(
					'labels' => json_encode(FPD_Labels_Products::get_labels()),
					'productBuilderUri' => get_wcfm_fncy_product_builder_url(),
					'currentUserId' => get_current_user_id(),
					'dokanUsers' => wcfm_is_vendor() ? get_users( array('fields' => array('ID', 'user_nicename')) ) : null,
				));
				
				/*$imports_dir_url = wp_upload_dir();
				$imports_dir_url = $imports_dir_url['baseurl'] . '/fpd-imports';
				wp_localize_script( 'wcfm-fpd-manage-fancy-products', 'fpd_fancy_products_opts', array(
						'selectProduct' => __('Please select a Product first to assign the category!', 'radykal'),
						'nothingToExport' => __('This product does not contain any views!', 'radykal'),
						'noJSON' => __('Sorry, but the selected file is not a valid JSON object. Are you sure you have selected the correct file to import?', 'radykal'),
						'chooseThumbnail' => __('Choose a thumbnail', 'radykal'),
						'importedFileStored' => __('If no, the images are stored in: ', 'radykal'). '<br /><i>'.$imports_dir_url.'</i>',
					)
				);*/
				
				wp_localize_script( 'wcfm-fpd-manage-fancy-products', 'fpd_admin_opts', $fpd_admin_opts );
				
				wp_add_inline_script( 'wcfm-fpd-manage-fancy-products', self::REACT_NO_CONFLICT_JS, 'after' );
      break;
      
      case 'wcfm-fncy-product-builder':
      	$WCFM->library->load_colorpicker_lib();

				require_once( FPD_PLUGIN_ADMIN_DIR . '/labels/product-builder.php' );

				wp_enqueue_media();

				wp_enqueue_style( 'radykal-select2' );
				wp_enqueue_style( 'jquery-fpd' );
				wp_enqueue_script( 'radykal-select2' );
				wp_enqueue_script( 'fpd-admin' );
				wp_enqueue_script( 'jquery-fpd' );
	
				wp_register_script( 'wcfm-fpd-product-builder', $WCFMu->library->js_lib_url . 'wc_fncy_product_designer/wcfmu-script-manage-fancy-products-builder.js', array(
					'jquery-ui-core',
					'jquery-ui-mouse',
					'jquery-ui-sortable',
					'jquery-ui-droppable',
					'fpd-semantic-ui'
				), Fancy_Product_Designer::VERSION );
				
				
				$script_options = FPD_Resource_Options::get_options(array(
					'fpd_common_parameter_originX',
					'fpd_common_parameter_originY',
					'fpd_uploadZonesTopped',
					'fpd_fabricjs_texture_size',
					'fpd_font',
					'enabled_fonts',
					'primary_layout_props',
					'design_categories',
					'plus_enabled'
				));

				$script_options['templates_directory'] = plugins_url('/assets/templates/', FPD_PLUGIN_ROOT_PHP );
				$script_options['products'] = FPD_Resource_Products::get_products( array('limit' => -1) );
				$script_options['adminUrl'] = admin_url();
				$script_options['labels'] = FPD_Labels_Product_Builder::get_labels();

				wp_localize_script( 'wcfm-fpd-product-builder', 'fpd_product_builder_opts', $script_options );
				
				wp_enqueue_script( 'wcfm-fpd-product-builder' );
				
				/*wp_localize_script( 'wcfm-fpd-product-builder', 'fpd_product_builder_opts', array(
						'adminUrl' => admin_url(),
						'builder_url' => get_wcfm_fncy_product_builder_url(),
						'originX' => fpd_get_option('fpd_common_parameter_originx'),
						'originY' => fpd_get_option('fpd_common_parameter_originy'),
						'paddingControl' => fpd_get_option('fpd_padding_controls'),
						'defaultFont' => get_option('fpd_font') ? get_option('fpd_font') : 'Arial',
						'enterTitlePrompt' => __('Enter a title for the element', 'radykal'),
						'chooseElementImageTitle' => __( 'Choose an element image', 'radykal' ),
						'set' => __( 'Set', 'radykal' ),
						'enterYourText' => __( 'Enter your text.', 'radykal' ),
						'removeElement' => __('Remove element?', 'radykal'),
						'notChanged' => __('You have not saved your changes!', 'radykal'),
						'changeImageSource' => __('Change Image Source', 'radykal'),
						'loading' => __('Loading', 'radykal'),
						'uploadZonesTopped' => fpd_get_option('fpd_uploadZonesTopped'),
						'enabled_fonts' => FPD_Fonts::to_json(FPD_Fonts::get_enabled_fonts()),
						'mask_svg_alert' => __( 'The image is not a SVG, you can only use SVG as view mask!', 'radykal' ),
						'bounding_box_color' => '#005ede',
						'page_size_missing' => __( 'Please define a print size!', 'radykal' ),
						'templates_directory' => plugins_url('/assets/templates/', FPD_PLUGIN_ROOT_PHP ),
						'general_props' => array_keys(FPD_Parameters::get_general_props()),
						'image_props' => array_keys(FPD_Parameters::get_image_props()),
						'text_props' => array_keys(FPD_Parameters::get_text_props()),
						'mask_svg_path_alert' => __( 'The SVG has more than one paths. Only the first path of a SVG will be used as mask.', 'radykal' ),
					)
				);*/
	
				wp_localize_script( 'wcfm-fpd-product-builder', 'fpd_admin_opts', $fpd_admin_opts );
				
				wp_add_inline_script( 'wcfm-fpd-product-builder', self::REACT_NO_CONFLICT_JS, 'after' );
      break;
	  }
	}
	
	/**
   * WC Fany Product Designer Styles
   */
	public function wcfpd_load_styles( $end_point ) {
	  global $WCFM, $WCFMu;
		
	  switch( $end_point ) {
	  	case 'wcfm-products-manage':
				//wp_enqueue_style( 'wcfmu_wc_appointments_products_manage_css', $WCFMu->library->css_lib_url . 'wc_fncy_product_designer/wcfmu-style-wcappointments-products-manage.css', array( ), $WCFMu->version );
			break;
			
			case 'wcfm-orders-details':
				wp_enqueue_style( 'wcfm-fpd-order-viewer', $WCFMu->library->css_lib_url . 'wc_fncy_product_designer/wcfmu-style-fancy-order-viewer.css', array(
					'fpd-semantic-ui',
					'jquery-fpd'
				), Fancy_Product_Designer::VERSION );
			break;
			
			case 'wcfm-fncy-product-designer':
	    	wp_enqueue_style( 'wcfm-fpd-manage-fancy-products', $WCFMu->library->css_lib_url . 'wc_fncy_product_designer/wcfmu-style-manage-fancy-products-designer.css', array(
					'fpd-semantic-ui'
				), Fancy_Product_Designer::VERSION );
		  break;
		  
		  case 'wcfm-fncy-product-builder':
	    	wp_enqueue_style( 'wcfm-fpd-react-product-builder', $WCFMu->library->css_lib_url . 'wc_fncy_product_designer/wcfmu-style-manage-fancy-products-builder.css', array(
					'fpd-semantic-ui'
				), Fancy_Product_Designer::VERSION );
		  break;
	  }
	}
	
	/**
   * WC Fany Product Designer Views
   */
  public function wcfpd_load_views( $end_point ) {
	  global $WCFM, $WCFMu;
	  
	  switch( $end_point ) {
	  	case 'wcfm-fncy-product-designer':
        $WCFMu->template->get_template( 'wc_fncy_product_designer/wcfmu-view-fncy-product-designer.php' );
      break;
      
      case 'wcfm-fncy-product-builder':
        $WCFMu->template->get_template( 'wc_fncy_product_designer/wcfmu-view-fncy-product-builder.php' );
      break;
	  }
	}
	
	/**
   * WC Fany Product Designer Ajax Controllers
   */
  public function wcfpd_ajax_controller() {
  	global $WCFM, $WCFMu;
  	
  	$controllers_path = $WCFMu->plugin_path . 'controllers/wc_fncy_product_designer/';
  	
  	$controller = '';
  	if( isset( $_POST['controller'] ) ) {
  		$controller = $_POST['controller'];
  		
  		switch( $controller ) {
  			case 'wcfm-products-manage':
  				include_once( $controllers_path . 'wcfmu-controller-fncy-product-designer.php' );
					new WCFMu_WCFancy_Products_Manage_Controller();
				break;
  		}
  	}
  }
  
  /**
   * WC Fany Product General Options
   */
  function wcfpd_product_manage_general( $product_id ) {
  	global $WCFM, $WCFMu;
  	
  	include_once( $WCFMu->library->views_path . 'wc_fncy_product_designer/wcfmu-view-fancy-products-manage.php' );
  }
  
  public function wcfpd_orders_details_load_views( ) {
	  global $WCFMu;
	  
	 $WCFMu->template->get_template( 'wc_fncy_product_designer/wcfmu-view-fancy-order-viewer.php' );
	}
	
	public function wcfpd_get_products_sql_attrs( $attrs ) {

		$where = isset( $attrs['where'] ) ? $attrs['where'] : null;

		if( wcfm_is_vendor() ) {

			$user_ids = array(get_current_user_id());

			//add fpd products from user
			/*$fpd_products_user_id = fpd_get_option( 'fpd_wc_dokan_user_global_products' );

			//skip if no use is set or on product builder
			if( $fpd_products_user_id !== 'none' && !(isset( $_GET['page'] ) && $_GET['page'] === 'fpd_product_builder') )
				array_push( $user_ids, $fpd_products_user_id );*/

			$user_ids = join( ",", $user_ids );

			$where = empty($where) ? "user_id IN ($user_ids)" : $where." AND user_id IN ($user_ids)";

		}

		//manage products filter
		if( isset($_POST['fpd_filter_users_select']) && $_POST['fpd_filter_users_select'] != "-1" ) {
			$where = "user_id=".$_POST['fpd_filter_users_select'];
		}

		$attrs['where'] = $where;

		return $attrs;

	}

	public function wcfpd_get_categories_sql_attrs( $attrs ) {

		$where = isset( $attrs['where'] ) ? $attrs['where'] : null;

		//only return products created by the current logged-in user
		if( wcfm_is_vendor() ) {
			$where = empty($where) ? 'user_id='.get_current_user_id() : $where.' AND user_id='.get_current_user_id();
		}

		$attrs['where'] = $where;

		return $attrs;

	}
  
  public function wcfmfpd_get_product_item_html( $id, $title, $category_ids='', $thumbnail='', $user_id='' ) {

		if( !empty($thumbnail) ) {
			$thumbnail = '<img src="'.$thumbnail.'" />';
		}
	
		$actions = array(
			'fpd-add-view' => array(
				'title' => __('Add View', 'radykal'),
				'icon'  => 'fpd-admin-icon-add-box'
			),
			'fpd-edit-product-title' => array(
				'title' => __('Edit Title', 'radykal'),
				'icon'  => 'fpd-admin-icon-mode-edit'
			),
			'fpd-edit-product-options' => array(
				'title' => __('Edit Options', 'radykal'),
				'icon'  => 'fpd-admin-icon-settings'
			),
			'fpd-export-product' => array(
				'title' => __('Export', 'radykal'),
				'icon'  => 'fpd-admin-icon-cloud-download'
			),
			'fpd-save-as-template' => array(
				'title' => __('Save as template', 'radykal'),
				'icon'  => 'fpd-admin-icon-template'
			),
			'fpd-duplicate-product' => array(
				'title' => __('Duplicate', 'radykal'),
				'icon'  => 'fpd-admin-icon-content-copy'
			),
			'fpd-remove-product' => array(
				'title' => __('Delete', 'radykal'),
				'icon'  => 'fpd-admin-icon-bin'
			),
		);
	
		$actions = apply_filters( 'fpd_admin_manage_products_product_actions', $actions, $id, $user_id );
	
		$user_info = get_userdata( intval($user_id) );
		$username = $user_info ?  __(' | ', 'radykal') . $user_info->user_nicename : '';
	
		ob_start();
		?>
		<li id="<?php echo $id; ?>" data-categories="<?php echo $category_ids; ?>" class="fpd-product-item fpd-clearfix">
			<span class="fpd-clearfix">
				<span class="fpd-single-image-upload fpd-admin-tooltip" title="<?php _e('Product Thumbnail', 'radykal'); ?>">
					<span class="fpd-remove">
						<span class="dashicons dashicons-minus"></span>
					</span>
					<?php echo $thumbnail; ?>
				</span>
				<span class="fpd-product-meta">
					<span class="fpd-item-id"># <?php echo $id . $username; ?></span>
					<span class="fpd-product-title"><?php echo $title; ?></span>
				</span>
			</span>
			<span>
				<?php
	
					foreach( $actions as $key => $action )
						echo '<a href="#" class="'.$key.' fpd-admin-tooltip" title="'.$action['title'].'"><i class="'.$action['icon'].'"></i></a>';
	
				?>
			</span>
		</li>
		<?php
	
		$output = ob_get_contents();
		ob_end_clean();
	
		return $output;
	
	}
	
	public function wcfmfpd_get_view_item_html( $id, $image, $title, $user_id='' ) {
	
		$product_builder_url = get_wcfm_fncy_product_builder_url($id);
	
		$actions = array(
			'fpd-edit-view-layers' => array(
				'title' => __('Edit view in product builder', 'radykal'),
				'icon'  => 'fpd-admin-icon-layers',
				'href' 	=> esc_attr( $product_builder_url )
			),
			'fpd-edit-view-title' => array(
				'title' => __('Edit Title', 'radykal'),
				'icon'  => 'fpd-admin-icon-mode-edit'
			),
			'fpd-duplicate-view' => array(
				'title' => __('Duplicate', 'radykal'),
				'icon'  => 'fpd-admin-icon-content-copy'
			),
			'fpd-remove-view' => array(
				'title' => __('Delete', 'radykal'),
				'icon'  => 'fpd-admin-icon-bin'
			),
		);
	
		$actions = apply_filters( 'fpd_admin_manage_products_view_actions', $actions, $id, $user_id );
	
		ob_start();
		?>
		<li id="<?php esc_attr_e( $id ); ?>" class="fpd-view-item fpd-clearfix">
			<span>
				<img src="<?php esc_attr_e( $image ); ?>" class="fpd-admin-tooltip" title="<?php _e( 'View Thumbnail', 'radykal' ); ?>" />
				<label><?php esc_html_e( $title ); ?></label>
			</span>
			<span>
				<?php
	
					foreach( $actions as $key => $action ) {
	
						$href = isset( $action['href'] ) ? $action['href'] : '#';
	
						echo '<a href="'. $href .'" class="'. $key .' fpd-admin-tooltip" title="'. $action['title'] .'" target="_self"><i class="'. $action['icon'] .'"></i></a>';
	
					}
	
	
				?>
			</span>
		</li>
		<?php
	
		$output = ob_get_contents();
		ob_end_clean();
	
		return $output;
	
	}
	
	public function wcfmfpd_get_category_item_html( $id, $title ) {
	
		$active_filter = '';
		$url_params = '?page=fancy_product_designer&category_id='.$id;
		if( isset($_GET['category_id']) &&  $_GET['category_id'] === $id ) {
			$active_filter = 'fpd-active';
			$url_params = '?page=fancy_product_designer';
		}
	
	
		return '<li id="'.$id.'" class="fpd-category-item fpd-clearfix"><span><div class="fpd-ad-checkbox"><input type="checkbox" id="fpd_category_'.$id.'" /><label for="fpd_category_'.$id.'">'.$title.'</label></div></span><span><a href="'.$url_params.'" class="fpd-filter-category fpd-admin-tooltip '.$active_filter.'" title="'.__('Show only products of this category', 'radykal').'"><i class="fpd-admin-icon-remove-red-eye"></i></a><a href="#" class="fpd-edit-category-title fpd-admin-tooltip" title="'.__('Edit Title', 'radykal').'"><i class="fpd-admin-icon-mode-edit"></i></a><a href="#" class="fpd-remove-category fpd-admin-tooltip" title="'.__('Delete', 'radykal').'"><i class="fpd-admin-icon-bin"></i></a></span></li>';
	
	}
	
	public function wcfmfpd_get_template_link_html( $template_id, $title) {
		return "<li><a href='#' id='".esc_attr( $template_id )."'>".$title."</a><a href='#' class='fpd-remove-template fpd-right'><i class='fpd-admin-icon-close'></i></a></li>";
	}
}