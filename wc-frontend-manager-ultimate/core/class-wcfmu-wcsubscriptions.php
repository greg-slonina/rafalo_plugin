<?php

/**
 * WCFMu plugin core
 *
 * WC Subscriptions Support
 *
 * @author 		WC Lovers
 * @package 	wcfmu/core
 * @version   2.2.2
 */
 
class WCFMu_WCSubscriptions {
	
	/**
	 * Billing fields.
	 *
	 * @var array
	 */
	protected static $billing_fields = array();

	/**
	 * Shipping fields.
	 *
	 * @var array
	 */
	protected static $shipping_fields = array();
	
	public function __construct() {
    global $WCFM, $WCFMu;
    
    if( wcfm_is_subscription() ) {
    	
    	// WC Subscriptions Query Var Filter
			add_filter( 'wcfm_query_vars', array( &$this, 'wcs_wcfm_query_vars' ), 20 );
			add_filter( 'wcfm_endpoint_title', array( &$this, 'wcs_wcfm_endpoint_title' ), 20, 2 );
			add_action( 'init', array( &$this, 'wcs_wcfm_init' ), 20 );
			
			// Subscriptions Endpoint Edit
			add_filter( 'wcfm_endpoints_slug', array( $this, 'wcs_wcfm_endpoints_slug' ) );	
			
			// WC Subscriptions Menu Filter
			add_filter( 'wcfm_menus', array( &$this, 'wcs_wcfm_menus' ), 20 );
    	
    	// Subscriptions Product Type
    	add_filter( 'wcfm_product_types', array( &$this, 'wcs_product_types' ), 40 );
    	
    	// Subscriptions Load WCFMu Scripts
			add_action( 'wcfm_load_scripts', array( &$this, 'wcs_load_scripts' ), 30 );
			
			// Subscriptions Load WCFMu Styles
			add_action( 'wcfm_load_styles', array( &$this, 'wcs_load_styles' ), 30 );
			
			// Subscriptions Load WCFMu views
			add_action( 'wcfm_load_views', array( &$this, 'wcs_load_views' ), 30 );
			
			// Subscriptions Ajax Controllers
			add_action( 'after_wcfm_ajax_controller', array( &$this, 'wcs_ajax_controller' ) );
    	
    	// Subscriptions Product options
    	add_filter( 'wcfm_product_manage_fields_general', array( &$this, 'wcs_product_manage_fields_general' ), 40, 5 );
    	add_filter( 'wcfm_product_manage_fields_shipping', array( &$this, 'wcs_product_manage_fields_shipping' ), 40, 2 );
    	add_filter( 'wcfm_product_manage_fields_advanced', array( &$this, 'wcs_product_manage_fields_advanced' ), 40, 2 );
    	add_filter( 'wcfm_product_manage_fields_variations', array( &$this, 'wcs_product_manage_fields_variations' ), 40, 4 );
    	
    	// Subscriptions Product Meta Data Save
    	add_action( 'after_wcfm_products_manage_meta_save', array( &$this, 'wcs_wcfm_product_meta_save' ), 40, 2 );
    	add_action( 'after_wcfm_product_variation_meta_save', array( &$this, 'wcs_product_variation_save' ), 40, 4 );
    	
    	// Subscription Product Date Edit
    	add_filter( 'wcfm_variation_edit_data', array( &$this, 'wcs_product_data_variations' ), 40, 3 );
    	
    	// Subscription Status Update
			add_action( 'wp_ajax_wcfm_modify_subscription_status', array( &$this, 'wcfm_modify_subscription_status' ) );
    }
    
  }
  
  /**
   * WC Subscriptions Query Var
   */
  function wcs_wcfm_query_vars( $query_vars ) {
  	$wcfm_modified_endpoints = wcfm_get_option( 'wcfm_endpoints', array() );
  	
  	// WC 3.6 FIX
		if( isset( $wcfm_modified_endpoints['wcfm-subscriptions'] ) && !empty( $wcfm_modified_endpoints['wcfm-subscriptions'] ) && $wcfm_modified_endpoints['wcfm-subscriptions'] == 'subscriptions' ) $wcfm_modified_endpoints['wcfm-subscriptions'] = 'subscriptionslist';
  	
		$query_subscriptions_vars = array(
			'wcfm-subscriptions'                 => ! empty( $wcfm_modified_endpoints['wcfm-subscriptions'] ) ? $wcfm_modified_endpoints['wcfm-subscriptions'] : 'subscriptionslist',
			'wcfm-subscriptions-manage'          => ! empty( $wcfm_modified_endpoints['wcfm-subscriptions-manage'] ) ? $wcfm_modified_endpoints['wcfm-subscriptions-manage'] : 'subscriptions-manage',
		);
		
		$query_vars = array_merge( $query_vars, $query_subscriptions_vars );
		
		return $query_vars;
  }
  
  /**
   * WC Subscriptions End Point Title
   */
  function wcs_wcfm_endpoint_title( $title, $endpoint ) {
  	global $wp;
  	switch ( $endpoint ) {
			case 'wcfm-subscriptions' :
				$title = __( 'Subscriptions List', 'wc-frontend-manager-ultimate' );
			break;
			case 'wcfm-subscriptions-manage' :
				$title = sprintf( __( 'Subscription Manage #%s', 'wc-frontend-manager-ultimate' ), $wp->query_vars['wcfm-subscriptions-manage'] );
			break;
  	}
  	
  	return $title;
  }
  
  /**
   * WC Subscriptions Endpoint Intialize
   */
  function wcs_wcfm_init() {
  	global $WCFM_Query;
	
		// Intialize WCFM End points
		$WCFM_Query->init_query_vars();
		$WCFM_Query->add_endpoints();
		
		if( !get_option( 'wcfm_updated_end_point_wc_subscriptions' ) ) {
			// Flush rules after endpoint update
			flush_rewrite_rules();
			update_option( 'wcfm_updated_end_point_wc_subscriptions', 1 );
		}
  }
  
  /**
   * WC Subscriptions Menu
   */
  function wcs_wcfm_menus( $menus ) {
  	global $WCFM;
  	
		if( apply_filters( 'wcfm_is_allow_subscriptions', true ) && apply_filters( 'wcfm_is_allow_subscription_list', true ) ) {	
			$menus = array_slice($menus, 0, 3, true) +
												array( 'wcfm-subscriptions' => array(   'label'  => __( 'Subscriptions', 'woocommerce-subscriptions'),
																										 'url'       => get_wcfm_subscriptions_url(),
																										 'icon'      => 'money-bill-alt',
																										 'priority'  => 21
																										) )	 +
													array_slice($menus, 3, count($menus) - 3, true) ;
		}
		
  	return $menus;
  }
  
  /**
	 * Subscriptions Endpoiint Edit
	 */
	function wcs_wcfm_endpoints_slug( $endpoints ) {
		
		$subscriptions_endpoints = array(
													'wcfm-subscriptions'          => 'subscriptionslist',
													'wcfm-subscriptions-manage'   => 'subscriptions-manage',
													);
		
		$endpoints = array_merge( $endpoints, $subscriptions_endpoints );
		
		return $endpoints;
	}
  
  /**
   * WC Subscriptions Product Type
   */
  function wcs_product_types( $pro_types ) {
  	global $WCFM, $WCFMu;
  	
  	$pro_types['variable-subscription'] = __( 'Variable subscription', 'woocommerce-subscriptions' );
  	
  	return $pro_types;
  }
  
  /**
   * WC Subscription Scripts
   */
  public function wcs_load_scripts( $end_point ) {
	  global $WCFM, $WCFMu;
    
	  switch( $end_point ) {
	  	case 'wcfm-subscriptions':
      	$WCFM->library->load_datatable_lib();
      	$WCFM->library->load_daterangepicker_lib();
      	$WCFM->library->load_select2_lib();
	    	wp_enqueue_script( 'wcfm_subscriptions_js', $WCFMu->library->js_lib_url . 'wc_subscriptions/wcfmu-script-wcsubscriptions.js', array('jquery', 'dataTables_js'), $WCFMu->version, true );
	    	
	    	// Screen manager
	    	$wcfm_screen_manager = (array) get_option( 'wcfm_screen_manager' );
	    	$wcfm_screen_manager_data = array();
	    	if( isset( $wcfm_screen_manager['subscription'] ) ) $wcfm_screen_manager_data = $wcfm_screen_manager['subscription'];
	    	if( !isset( $wcfm_screen_manager_data['admin'] ) ) {
					$wcfm_screen_manager_data['admin'] = $wcfm_screen_manager_data;
					$wcfm_screen_manager_data['vendor'] = $wcfm_screen_manager_data;
				}
				if( wcfm_is_vendor() ) {
					$wcfm_screen_manager_data = $wcfm_screen_manager_data['vendor'];
				} else {
					$wcfm_screen_manager_data = $wcfm_screen_manager_data['admin'];
				}
				if( apply_filters( 'wcfm_subscriptions_additonal_data_hidden', true ) ) {
					$wcfm_screen_manager_data[10] = 'yes';
				}
	    	wp_localize_script( 'wcfm_subscriptions_js', 'wcfm_subscriptions_screen_manage', $wcfm_screen_manager_data );
      break;
      
      case 'wcfm-subscriptions-manage':
      	$WCFM->library->load_datepicker_lib();
      	wp_register_script( 'wcfm_jstz', plugin_dir_url( WC_Subscriptions::$plugin_file ) . 'assets/js/admin/jstz.min.js' );
      	wp_register_script( 'wcfm_momentjs', plugin_dir_url( WC_Subscriptions::$plugin_file ) . 'assets/js/admin/moment.min.js' );
	    	wp_enqueue_script( 'wcfm_subscriptions_manage_js', $WCFMu->library->js_lib_url . 'wc_subscriptions/wcfmu-script-wcsubscriptions-manage.js', array('jquery', 'wcfm_jstz', 'wcfm_momentjs'), $WCFMu->version, true );
	    	
	    	wp_localize_script( 'wcfm_subscriptions_manage_js', 'wcs_admin_meta_boxes', apply_filters( 'wcfm_subscriptions_admin_meta_boxes_script_parameters', array(
					'i18n_start_date_notice'         => __( 'Please enter a start date in the past.', 'woocommerce-subscriptions' ),
					'i18n_past_date_notice'          => __( 'Please enter a date at least one hour into the future.', 'woocommerce-subscriptions' ),
					'i18n_next_payment_start_notice' => __( 'Please enter a date after the trial end.', 'woocommerce-subscriptions' ),
					'i18n_next_payment_trial_notice' => __( 'Please enter a date after the start date.', 'woocommerce-subscriptions' ),
					'i18n_trial_end_start_notice'    => __( 'Please enter a date after the start date.', 'woocommerce-subscriptions' ),
					'i18n_trial_end_next_notice'     => __( 'Please enter a date before the next payment.', 'woocommerce-subscriptions' ),
					'i18n_end_date_notice'           => __( 'Please enter a date after the next payment.', 'woocommerce-subscriptions' ),
					'process_renewal_action_warning' => __( "Are you sure you want to process a renewal?\n\nThis will charge the customer and email them the renewal order (if emails are enabled).", 'woocommerce-subscriptions' ),
					//'payment_method'                 => wcs_get_subscription( $post )->get_payment_method(),
					//'search_customers_nonce'         => wp_create_nonce( 'search-customers' ),
				) ) );
      break;
	  }
	}
	
	/**
   * WC Subscription Styles
   */
	public function wcs_load_styles( $end_point ) {
	  global $WCFM, $WCFMu;
		
	  switch( $end_point ) {
	    case 'wcfm-subscriptions':
	    	wp_enqueue_style( 'wcfm_subscriptions_css',  $WCFMu->library->css_lib_url . 'wc_subscriptions/wcfmu-style-wcsubscriptions.css', array(), $WCFMu->version );
		  break;
		  
		  case 'wcfm-subscriptions-manage':
		  	wp_enqueue_style( 'collapsible_css',  $WCFM->library->css_lib_url . 'wcfm-style-collapsible.css', array(), $WCFMu->version );
	    	wp_enqueue_style( 'wcfm_subscriptions_manage_css',  $WCFMu->library->css_lib_url . 'wc_subscriptions/wcfmu-style-wcsubscriptions-manage.css', array(), $WCFMu->version );
		  break;
	  }
	}
	
	/**
   * WC Subscription Views
   */
  public function wcs_load_views( $end_point ) {
	  global $WCFM, $WCFMu;
	  
	  switch( $end_point ) {
	  	case 'wcfm-subscriptions':
        $WCFMu->template->get_template( 'wc_subscriptions/wcfmu-view-wcsubscriptions.php' );
      break;
      
      case 'wcfm-subscriptions-manage':
        $WCFMu->template->get_template( 'wc_subscriptions/wcfmu-view-wcsubscriptions-manage.php' );
      break;
	  }
	}
	
	/**
   * WC Subscription Ajax Controllers
   */
  public function wcs_ajax_controller() {
  	global $WCFM, $WCFMu;
  	
  	$controllers_path = $WCFMu->plugin_path . 'controllers/wc_subscriptions/';
  	
  	$controller = '';
  	if( isset( $_POST['controller'] ) ) {
  		$controller = $_POST['controller'];
  		
  		switch( $controller ) {
  			case 'wcfm-subscriptions':
					include_once( $controllers_path . 'wcfmu-controller-wcsubscriptions.php' );
					new WCFMu_WCSubscriptions_Controller();
				break;
				case 'wcfm-subscriptions-manage':
					include_once( $controllers_path . 'wcfmu-controller-wcsubscriptions-manage.php' );
					new WCFMu_WCSubscriptions_Manage_Controller();
				break;
  		}
  	}
  }
  
  /**
	 * WC Subscriptions Product General options
	 */
	function wcs_product_manage_fields_general( $general_fields, $product_id, $product_type, $wcfm_is_translated_product = false, $wcfm_wpml_edit_disable_element = ''  ) {
		global $WCFM, $WCFMu;
		
		$sign_up_fee         = '';
		$chosen_trial_length = 0;
		$chosen_trial_period = '';
		
		if( $product_id ) {
			$sign_up_fee         = get_post_meta( $product_id, '_subscription_sign_up_fee', true );
			$chosen_trial_length = WC_Subscriptions_Product::get_trial_length( $product_id );
			$chosen_trial_period = WC_Subscriptions_Product::get_trial_period( $product_id );
		}
		
		$general_fields = array_slice($general_fields, 0, 12, true) +
																	array( 
																				"_subscription_sign_up_fee" => array('label' => sprintf( esc_html__( 'Sign-up fee (%s)', 'woocommerce-subscriptions' ), esc_html( get_woocommerce_currency_symbol() ) ), 'type' => 'text', 'placeholder' => 'e.g. 9.90', 'class' => 'wcfm-text wcfm_ele wcfm_ele_hide subscription' . ' ' . $wcfm_wpml_edit_disable_element, 'label_class' => 'wcfm_title wcfm_ele wcfm_ele_hide subscription' . ' ' . $wcfm_wpml_edit_disable_element, 'hints' => __( 'Optionally include an amount to be charged at the outset of the subscription. The sign-up fee will be charged immediately, even if the product has a free trial or the payment dates are synced.', 'woocommerce-subscriptions' ), 'value' => $sign_up_fee ),
																				"_subscription_trial_length" => array( 'label' => esc_html__( 'Free Trial', 'woocommerce-subscriptions' ), 'type' => 'number', 'class' => 'wcfm-text wcfm_ele wcfm_ele_hide subscription_price_ele subscription' . ' ' . $wcfm_wpml_edit_disable_element, 'label_class' => 'wcfm_title wcfm_ele wcfm_ele_hide subscription' . ' ' . $wcfm_wpml_edit_disable_element, 'hints' => __( 'An optional period of time to wait before charging the first recurring payment. Any sign up fee will still be charged at the outset of the subscription.', 'woocommerce-subscriptions' ), 'value' => $chosen_trial_length ),
																				"_subscription_trial_period" => array( 'type' => 'select', 'options' => wcs_get_available_time_periods(), 'class' => 'wcfm-select wcfm_ele wcfm_ele_hide subscription_price_ele subscription' . ' ' . $wcfm_wpml_edit_disable_element, 'label_class' => 'wcfm_title wcfm_ele wcfm_ele_hide subscription' . ' ' . $wcfm_wpml_edit_disable_element, 'value' => $chosen_trial_period ),
																				) +
																	array_slice($general_fields, 12, count($general_fields) - 1, true) ;
		return $general_fields;
	}
	
	/**
	 * WC Subscriptions Product Shipping options
	 */
	function wcs_product_manage_fields_shipping( $shipping_fields, $product_id ) {
		global $WCFM, $WCFMu;
		
		$one_time_shipping           = 'no';
		
		if( $product_id ) {
			$one_time_shipping         = get_post_meta( $product_id, '_subscription_one_time_shipping', true ) ? get_post_meta( $product_id, '_subscription_one_time_shipping', true ) : 'no';
		}
		
		$shipping_fields = array_slice( $shipping_fields, 0, 5, true) +
																	array( 
																				"_subscription_one_time_shipping" => array( 'label' => esc_html__( 'One time shipping', 'woocommerce-subscriptions' ), 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele subscription variable-subscription', 'label_class' => 'wcfm_title wcfm_ele subscription variable-subscription', 'hints' => __( 'Shipping for subscription products is normally charged on the initial order and all renewal orders. Enable this to only charge shipping once on the initial order. Note: for this setting to be enabled the subscription must not have a free trial or a synced renewal date.', 'woocommerce-subscriptions' ), 'value' => 'yes', 'dfvalue' => $one_time_shipping )
																				) +
																	array_slice( $shipping_fields, 5, count($shipping_fields) - 1, true) ;
		return $shipping_fields;
		
	}
	
	/**
	 * WC Subscriptions Product Advanced options
	 */
	function wcs_product_manage_fields_advanced( $advanced_fields, $product_id ) {
		global $WCFM, $WCFMu;
		
		$subscription_limit           = '';
		
		if( $product_id ) {
			$subscription_limit         = get_post_meta( $product_id, '_subscription_limit', true );
		}
		
		$advanced_fields = array_slice( $advanced_fields, 0, 3, true) +
																	array( 
																				"_subscription_limit" => array( 'label' => esc_html__( 'Limit subscription', 'woocommerce-subscriptions' ), 'type' => 'select', 'options' => array( 'no' => __( 'Do not limit', 'woocommerce-subscriptions' ), 'active' => __( 'Limit to one active subscription', 'woocommerce-subscriptions' ), 'any' => __( 'Limit to one of any status', 'woocommerce-subscriptions' ) ), 'class' => 'wcfm-select wcfm_ele subscription variable-subscription', 'label_class' => 'wcfm_title wcfm_ele subscription variable-subscription', 'hints' => __( 'Only allow a customer to have one subscription to this product.', 'woocommerce-subscriptions' ), 'value' => $subscription_limit )
																				) +
																	array_slice( $advanced_fields, 3, count($advanced_fields) - 1, true) ;
		return $advanced_fields;
		
	}
	
	/**
	 * WC Subscriptions Variation aditional options
	 */
	function wcs_product_manage_fields_variations( $variation_fileds, $variations, $variation_shipping_option_array, $variation_tax_classes_options ) {
		global $WCFM, $WCFMu;
		
		$variation_fileds = array_slice($variation_fileds, 0, 6, true) +
																	array(  "_subscription_price" => array('label' => sprintf( esc_html__( 'Subscription price (%s)', 'woocommerce-subscriptions' ), esc_html( get_woocommerce_currency_symbol() ) ), 'type' => 'text', 'class' => 'wcfm-text wcfm_ele subscription_price_ele variable-subscription', 'label_class' => 'wcfm_title wcfm_ele variable-subscription', 'hints' => __( 'Choose the subscription price, billing interval and period.', 'woocommerce-subscriptions' ) ),
																					"_subscription_period_interval" => array( 'type' => 'select', 'options' => wcs_get_subscription_period_interval_strings(), 'class' => 'wcfm-select wcfm_ele subscription_price_ele variable-subscription', 'label_class' => 'wcfm_title wcfm_ele variable-subscription' ),
																					"_subscription_period" => array( 'type' => 'select', 'options' => wcs_get_subscription_period_strings(), 'class' => 'wcfm-select wcfm_ele subscription_price_ele variable-subscription_period variable-subscription', 'label_class' => 'wcfm_title wcfm_ele variable-subscription' ),
																					"_subscription_length_day" => array( 'label' => __('Subscription length', 'woocommerce-subscriptions' ), 'type' => 'select', 'options' => wcs_get_subscription_ranges( 'day' ), 'class' => 'wcfm-select wcfm_ele variable-subscription_length_ele variable-subscription_length_day variable-subscription', 'label_class' => 'wcfm_title wcfm_ele variable-subscription_length_ele variable-subscription_length_day variable-subscription', 'hints' => __( 'Automatically expire the subscription after this length of time. This length is in addition to any free trial or amount of time provided before a synchronised first renewal date.', 'woocommerce-subscriptions' ) ),
																					"_subscription_length_week" => array( 'label' => __('Subscription length', 'woocommerce-subscriptions' ), 'type' => 'select', 'options' => wcs_get_subscription_ranges( 'week' ), 'class' => 'wcfm-select wcfm_ele variable-subscription_length_ele variable-subscription_length_week variable-subscription', 'label_class' => 'wcfm_title wcfm_ele variable-subscription_length_ele variable-subscription_length_week variable-subscription', 'hints' => __( 'Automatically expire the subscription after this length of time. This length is in addition to any free trial or amount of time provided before a synchronised first renewal date.', 'woocommerce-subscriptions' ) ),
																					"_subscription_length_month" => array( 'label' => __('Subscription length', 'woocommerce-subscriptions' ), 'type' => 'select', 'options' => wcs_get_subscription_ranges( 'month' ), 'class' => 'wcfm-select wcfm_ele variable-subscription_length_ele variable-subscription_length_month variable-subscription', 'label_class' => 'wcfm_title wcfm_ele variable-subscription_length_ele variable-subscription_length_month variable-subscription', 'hints' => __( 'Automatically expire the subscription after this length of time. This length is in addition to any free trial or amount of time provided before a synchronised first renewal date.', 'woocommerce-subscriptions' ) ),
																					"_subscription_length_year" => array( 'label' => __('Subscription length', 'woocommerce-subscriptions' ), 'type' => 'select', 'options' => wcs_get_subscription_ranges( 'year' ), 'class' => 'wcfm-select wcfm_ele variable-subscription_length_ele variable-subscription_length_year variable-subscription', 'label_class' => 'wcfm_title wcfm_ele variable-subscription_length_ele variable-subscription_length_year variable-subscription', 'hints' => __( 'Automatically expire the subscription after this length of time. This length is in addition to any free trial or amount of time provided before a synchronised first renewal date.', 'woocommerce-subscriptions' ) ),
																					"_subscription_sign_up_fee" => array('label' => sprintf( esc_html__( 'Sign-up fee (%s)', 'woocommerce-subscriptions' ), esc_html( get_woocommerce_currency_symbol() ) ), 'type' => 'text', 'placeholder' => 'e.g. 9.90', 'class' => 'wcfm-text wcfm_ele variable-subscription', 'label_class' => 'wcfm_title wcfm_ele variable-subscription', 'hints' => __( 'Optionally include an amount to be charged at the outset of the subscription. The sign-up fee will be charged immediately, even if the product has a free trial or the payment dates are synced.', 'woocommerce-subscriptions' ) ),
																					"_subscription_trial_length" => array( 'label' => esc_html__( 'Free Trial', 'woocommerce-subscriptions' ), 'type' => 'number', 'class' => 'wcfm-text wcfm_ele subscription_trial_ele variable-subscription', 'label_class' => 'wcfm_title wcfm_ele variable-subscription', 'hints' => __( 'An optional period of time to wait before charging the first recurring payment. Any sign up fee will still be charged at the outset of the subscription.', 'woocommerce-subscriptions' ) ),
																					"_subscription_trial_period" => array( 'type' => 'select', 'options' => wcs_get_available_time_periods(), 'class' => 'wcfm-select wcfm_ele subscription_trial_ele variable-subscription', 'label_class' => 'wcfm_title wcfm_ele variable-subscription' ),
																				) +
																	array_slice($variation_fileds, 6, count($variation_fileds) - 1, true) ;
																	
	  return $variation_fileds;									
	}
	
	/**
	 * WC Subscriptions Product Meta data save
	 */
	function wcs_wcfm_product_meta_save( $new_product_id, $wcfm_products_manage_form_data ) {
		global $wpdb, $WCFM, $WCFMu, $_POST;
		
		if( $wcfm_products_manage_form_data['product_type'] == 'subscription' ) {
			// Make sure trial period is within allowable range
			$subscription_ranges = wcs_get_subscription_ranges();
	
			$max_trial_length = count( $subscription_ranges[ $wcfm_products_manage_form_data['_subscription_trial_period'] ] ) - 1;
	
			$wcfm_products_manage_form_data['_subscription_trial_length'] = absint( $wcfm_products_manage_form_data['_subscription_trial_length'] );
	
			if ( $wcfm_products_manage_form_data['_subscription_trial_length'] > $max_trial_length ) {
				$wcfm_products_manage_form_data['_subscription_trial_length'] = $max_trial_length;
			}
	
			update_post_meta( $new_product_id, '_subscription_trial_length', $wcfm_products_manage_form_data['_subscription_trial_length'] );
	
			$wcfm_products_manage_form_data['_subscription_sign_up_fee']       = wc_format_decimal( $wcfm_products_manage_form_data['_subscription_sign_up_fee'] );
			$wcfm_products_manage_form_data['_subscription_one_time_shipping'] = isset( $wcfm_products_manage_form_data['_subscription_one_time_shipping'] ) ? 'yes' : 'no';
	
			$subscription_fields = array(
				'_subscription_sign_up_fee',
				'_subscription_trial_period',
				'_subscription_limit',
				'_subscription_one_time_shipping',
			);
	
			foreach ( $subscription_fields as $field_name ) {
				if ( isset( $wcfm_products_manage_form_data[ $field_name ] ) ) {
					update_post_meta( $new_product_id, $field_name, stripslashes( $wcfm_products_manage_form_data[ $field_name ] ) );
				}
			}
		}
	}
	
	/**
	 * WC Subscriptions Variation Data Save
	 */
	function wcs_product_variation_save( $new_product_id, $variation_id, $variations, $wcfm_products_manage_form_data ) {
	 	global $wpdb, $WCFM, $WCFMu;
	 	
	 	if ( WC_Subscriptions_Product::is_subscription( $new_product_id ) ) {
	 	  
			$subscription_price = isset( $variations['_subscription_price'] ) ? wc_format_decimal( $variations['_subscription_price'] ) : '';
			update_post_meta( $variation_id, '_subscription_price', $subscription_price );
			update_post_meta( $variation_id, '_regular_price', $subscription_price );
			update_post_meta( $new_product_id, '_price', $subscription_price );
			update_post_meta( $variation_id, '_price', $subscription_price );
	
			$subscription_fields = array(
				'_subscription_period',
				'_subscription_period_interval',
				'_subscription_sign_up_fee',
				'_subscription_trial_period',
				'_subscription_trial_length'
			);
	
			foreach ( $subscription_fields as $field_name ) {
				if ( isset( $variations[ $field_name ] ) ) {
					update_post_meta( $variation_id, $field_name, stripslashes( $variations[ $field_name ] ) );
				}
			}
			
			update_post_meta( $variation_id, '_subscription_length', stripslashes( $variations[ '_subscription_length_' . $variations[ '_subscription_period' ]  ] ) );
			
			if ( WC_Subscriptions::is_woocommerce_pre( '3.0' ) ) {
				$variable_subscription = wc_get_product( $new_product_id );
				$variable_subscription->variable_product_sync();
			} else {
				WC_Product_Variable::sync( $new_product_id );
			}
		}
	}
	
	/**
	 * WC Subscriptions Variaton edit data
	 */
	function wcs_product_data_variations( $variations, $variation_id, $variation_id_key ) {
		global $WCFM, $WCFMu;
		
		if( $variation_id  ) {
			$variations[$variation_id_key]['_subscription_price'] = get_post_meta( $variation_id, '_subscription_price', true );
			$variations[$variation_id_key]['_subscription_period'] = get_post_meta( $variation_id, '_subscription_period', true);
			$variations[$variation_id_key]['_subscription_period_interval'] = get_post_meta( $variation_id, '_subscription_period_interval', true);
			$variations[$variation_id_key]['_subscription_sign_up_fee'] = get_post_meta( $variation_id, '_subscription_sign_up_fee', true);
			$variations[$variation_id_key]['_subscription_trial_period'] = get_post_meta( $variation_id, '_subscription_trial_period', true);
			$variations[$variation_id_key]['_subscription_trial_length'] = get_post_meta( $variation_id, '_subscription_trial_length', true);
			$variations[$variation_id_key]['_subscription_length_day'] = get_post_meta( $variation_id, '_subscription_length', true);
			$variations[$variation_id_key]['_subscription_length_week'] = get_post_meta( $variation_id, '_subscription_length', true);
			$variations[$variation_id_key]['_subscription_length_month'] = get_post_meta( $variation_id, '_subscription_length', true);
			$variations[$variation_id_key]['_subscription_length_year'] = get_post_meta( $variation_id, '_subscription_length', true);
		}
		
		return $variations;
	}
	
	/**
   * Handle Subscriptions Details Status Update
   */
  public function wcfm_modify_subscription_status() {
  	global $WCFM, $WCFMu;
  	
  	$subscription_id     = $_POST['subscription_id'];
  	$subscription_status = $_POST['subscription_status'];
  	
  	$subscription = wcs_get_subscription( $subscription_id );
  	$subscription->update_status( $subscription_status );
  	
  	// Status Update Notification
  	$user_id = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );
		$shop_name =  get_user_by( 'ID', $user_id )->display_name;
		if( wcfm_is_vendor() ) {
			$shop_name =  $WCFM->wcfm_vendor_support->wcfm_get_vendor_store_by_vendor( absint($user_id) );
		}
  	$wcfm_messages = sprintf( __( '<b>%s</b> subscription status updated to <b>%s</b> by <b>%s</b>', 'wc-frontend-manager-ultimate' ), '#<a target="_blank" class="wcfm_dashboard_item_title" href="' . get_wcfm_subscriptions_manage_url($subscription_id) . '">' . $subscription_id . '</a>', ucfirst( $subscription_status ), $shop_name );
		$WCFM->wcfm_notification->wcfm_send_direct_message( -2, 0, 1, 0, $wcfm_messages, 'status-update' );
  	
  	echo '{"status": true, "message": "' . __( 'Subscription status updated.', 'wc-frontend-manager-ultimate' ) . '"}';
  	
		die;
  }
	
	
	public static function init_address_fields() {

		self::$billing_fields = apply_filters( 'woocommerce_admin_billing_fields', array(
			'first_name' => array(
				'label' => __( 'First Name', 'woocommerce' ),
				'show'  => false
			),
			'last_name' => array(
				'label' => __( 'Last Name', 'woocommerce' ),
				'show'  => false
			),
			'company' => array(
				'label' => __( 'Company', 'woocommerce' ),
				'show'  => false
			),
			'address_1' => array(
				'label' => __( 'Address 1', 'woocommerce' ),
				'show'  => false
			),
			'address_2' => array(
				'label' => __( 'Address 2', 'woocommerce' ),
				'show'  => false
			),
			'city' => array(
				'label' => __( 'City', 'woocommerce' ),
				'show'  => false
			),
			'postcode' => array(
				'label' => __( 'Postcode', 'woocommerce' ),
				'show'  => false
			),
			'country' => array(
				'label'   => __( 'Country', 'woocommerce' ),
				'show'    => false,
				'class'   => 'js_field-country select short',
				'type'    => 'select',
				'options' => array( '' => __( 'Select a country&hellip;', 'woocommerce' ) ) + WC()->countries->get_allowed_countries()
			),
			'state' => array(
				'label' => __( 'State/County', 'woocommerce' ),
				'class'   => 'js_field-state select short',
				'show'  => false
			),
			'email' => array(
				'label' => __( 'Email', 'woocommerce' ),
			),
			'phone' => array(
				'label' => __( 'Phone', 'woocommerce' ),
			),
		) );

		self::$shipping_fields = apply_filters( 'woocommerce_admin_shipping_fields', array(
			'first_name' => array(
				'label' => __( 'First Name', 'woocommerce' ),
				'show'  => false
			),
			'last_name' => array(
				'label' => __( 'Last Name', 'woocommerce' ),
				'show'  => false
			),
			'company' => array(
				'label' => __( 'Company', 'woocommerce' ),
				'show'  => false
			),
			'address_1' => array(
				'label' => __( 'Address 1', 'woocommerce' ),
				'show'  => false
			),
			'address_2' => array(
				'label' => __( 'Address 2', 'woocommerce' ),
				'show'  => false
			),
			'city' => array(
				'label' => __( 'City', 'woocommerce' ),
				'show'  => false
			),
			'postcode' => array(
				'label' => __( 'Postcode', 'woocommerce' ),
				'show'  => false
			),
			'country' => array(
				'label'   => __( 'Country', 'woocommerce' ),
				'show'    => false,
				'type'    => 'select',
				'class'   => 'js_field-country select short',
				'options' => array( '' => __( 'Select a country&hellip;', 'woocommerce' ) ) + WC()->countries->get_shipping_countries()
			),
			'state' => array(
				'label' => __( 'State/County', 'woocommerce' ),
				'class'   => 'js_field-state select short',
				'show'  => false
			),
		) );
	}
}