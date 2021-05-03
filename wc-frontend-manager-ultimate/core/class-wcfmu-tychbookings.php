<?php

/**
 * WCFM plugin core
 *
 * Booking Tych Booking Support
 *
 * @author 		WC Lovers
 * @package 	wcfmu/core
 * @version   5.4.7
 */
 
class WCFMu_TychBookings {
	
	public function __construct() {
    global $WCFM;
    
    	
		// WC Booking Query Var Filter
		add_filter( 'wcfm_query_vars', array( &$this, 'wcb_wcfm_query_vars' ), 20 );
		add_filter( 'wcfm_endpoint_title', array( &$this, 'wcb_wcfm_endpoint_title' ), 20, 2 );
		add_action( 'init', array( &$this, 'wcb_wcfm_init' ), 20 );
			
		if ( apply_filters( 'wcfm_is_allow_manage_booking', true ) ) {
			// WC Booking Menu Filter
			add_filter( 'wcfm_menus', array( &$this, 'wcb_wcfm_menus' ), 20 );
			
			// WCFM Bookings Endpoint Edit
				add_filter( 'wcfm_endpoints_slug', array( $this, 'wcb_wcfm_endpoints_slug' ) );
			
			// Bookings Load WCFMu Scripts
			add_action( 'wcfm_load_scripts', array( &$this, 'wcb_load_scripts' ), 30 );
			add_action( 'after_wcfm_load_scripts', array( &$this, 'wcb_load_scripts' ), 30 );
			
			// Bookings Load WCFMu Styles
			add_action( 'wcfm_load_styles', array( &$this, 'wcb_load_styles' ), 30 );
			add_action( 'after_wcfm_load_styles', array( &$this, 'wcb_load_styles' ), 30 );
			
			// Bookings Load WCFMu views
			add_action( 'wcfm_load_views', array( &$this, 'wcb_load_views' ), 30 );
			
			// Bookings Ajax Controllers
			add_action( 'after_wcfm_ajax_controller', array( &$this, 'wcb_ajax_controller' ) );
			
			// Booking Product Manage View
			add_action( 'end_wcfm_products_manage', array( &$this, 'wcb_wcfm_products_manage_form_load_views' ), 20 );
		}
		
		// Booking Mark as Confirmed
		add_action( 'wp_ajax_wcfm_tych_booking_mark_confirm', array( &$this, 'wcfm_booking_mark_confirm' ) );
		
		// Booking Calendar Popup
		add_action( 'wp_ajax_wcfm_tych_booking_calender_content', array( &$this, 'wcfm_tych_booking_calender_content' ) );
		
		// Booking Status Update
		add_action( 'wp_ajax_wcfm_modify_tych_booking_status', array( &$this, 'wcfm_modify_booking_status' ) );
    
    // Add vendor email for confirm booking email
		add_filter( 'woocommerce_email_recipient_new_booking', array( $this, 'wcfm_filter_booking_emails' ), 20, 2 );

		// Add vendor email for cancelled booking email
		add_filter( 'woocommerce_email_recipient_booking_cancelled', array( $this, 'wcfm_filter_booking_emails' ), 20, 2 );
  }
  
  /**
   * WC Booking Query Var
   */
  function wcb_wcfm_query_vars( $query_vars ) {
  	$wcfm_modified_endpoints = wcfm_get_option( 'wcfm_endpoints', array() );
  	
  	if( isset( $wcfm_modified_endpoints['wcfm-booking'] ) && !empty( $wcfm_modified_endpoints['wcfm-booking'] ) && $wcfm_modified_endpoints['wcfm-booking'] == 'booking' ) $wcfm_modified_endpoints['wcfm-booking'] = 'bookinglist';
  	
		$query_booking_vars = array(
			'wcfm-booking-dashboard'       => ! empty( $wcfm_modified_endpoints['wcfm-booking-dashboard'] ) ? $wcfm_modified_endpoints['wcfm-booking-dashboard'] : 'booking-dashboard',
			'wcfm-booking'                 => ! empty( $wcfm_modified_endpoints['wcfm-booking'] ) ? $wcfm_modified_endpoints['wcfm-booking'] : 'bookinglist',
			'wcfm-booking-resources'       => ! empty( $wcfm_modified_endpoints['wcfm-booking-resources'] ) ? $wcfm_modified_endpoints['wcfm-booking-resources'] : 'booking-resources',
			'wcfm-booking-resources-manage'=> ! empty( $wcfm_modified_endpoints['wcfm-booking-resources-manage'] ) ? $wcfm_modified_endpoints['wcfm-booking-resources-manage'] : 'booking-resources-manage',
			'wcfm-booking-manual'          => ! empty( $wcfm_modified_endpoints['wcfm-booking-manual'] ) ? $wcfm_modified_endpoints['wcfm-booking-manual'] : 'booking-manual',
			'wcfm-booking-calendar'        => ! empty( $wcfm_modified_endpoints['wcfm-booking-calendar'] ) ? $wcfm_modified_endpoints['wcfm-booking-calendar'] : 'booking-calendar',
			'wcfm-booking-details'         => ! empty( $wcfm_modified_endpoints['wcfm-booking-details'] ) ? $wcfm_modified_endpoints['wcfm-booking-details'] : 'booking-details',
			//'wcfm-booking-settings'        => ! empty( $wcfm_modified_endpoints['wcfm-booking-settings'] ) ? $wcfm_modified_endpoints['wcfm-booking-settings'] : 'booking-settings',
		);
		
		$query_vars = array_merge( $query_vars, $query_booking_vars );
		
		return $query_vars;
  }
  
  /**
	 * WC Booking Endpoiint Edit
	 */
	function wcb_wcfm_endpoints_slug( $endpoints ) {
		
		$booking_endpoints = array(
													'wcfm-booking-dashboard'        => 'booking-dashboard',
													'wcfm-booking'                  => 'bookinglist',
													'wcfm-booking-resources'        => 'booking-resources',
													'wcfm-booking-resources-manage' => 'booking-resources-manage',
													'wcfm-booking-manual'    		    => 'booking-manual',
													'wcfm-booking-calendar'  		    => 'booking-calendar',
													'wcfm-booking-details'          => 'booking-details',
													//'wcfm-booking-settings'         => 'bookings-settings'
													);
		
		$endpoints = array_merge( $endpoints, $booking_endpoints );
		
		return $endpoints;
	}
  
  /**
   * WC Booking End Point Title
   */
  function wcb_wcfm_endpoint_title( $title, $endpoint ) {
  	global $wp;
  	switch ( $endpoint ) {
  		case 'wcfm-booking-dashboard' :
				$title = __( 'Bookings Dashboard', 'wc-frontend-manager' );
			break;
			case 'wcfm-booking' :
				$title = __( 'Bookings List', 'wc-frontend-manager' );
			break;
			case 'wcfm-booking-resources' :
				$title = __( 'Bookings Resources', 'wc-frontend-manager' );
			break;
			case 'wcfm-booking-resources-manage' :
				$title = __( 'Bookings Resources Manage', 'wc-frontend-manager' );
			break;
			case 'wcfm-booking-manual' :
				$title = __( 'Create Bookings', 'wc-frontend-manager' );
			break;
			case 'wcfm-booking-calendar' :
				$title = __( 'Bookings Calendar', 'wc-frontend-manager' );
			break;
			case 'wcfm-booking-details' :
				$title = sprintf( __( 'Booking Details #%s', 'wc-frontend-manager' ), $wp->query_vars['wcfm-bookings-details'] );
			break;
			case 'wcfm-booking-settings' :
				$title = __( 'Bookings settings', 'wc-frontend-manager' );
			break;
  	}
  	
  	return $title;
  }
  
  /**
   * WC Booking Endpoint Intialize
   */
  function wcb_wcfm_init() {
  	global $WCFM_Query;
	
		// Intialize WCFM End points
		$WCFM_Query->init_query_vars();
		$WCFM_Query->add_endpoints();
		
		if( !get_option( 'wcfm_updated_end_point_tych_bookings' ) ) {
			// Flush rules after endpoint update
			flush_rewrite_rules();
			update_option( 'wcfm_updated_end_point_tych_bookings', 1 );
		}
  }
  
  /**
   * WC Booking Menu
   */
  function wcb_wcfm_menus( $menus ) {
  	global $WCFM;
  	
  	if ( apply_filters( 'wcfm_is_allow_manage_booking', true ) ) {
			$menus = array_slice($menus, 0, 3, true) +
													array( 'wcfm-booking-dashboard' => array(   'label'  => __( 'Bookings', 'woocommerce-bookings'),
																											 'url'       => get_wcfm_tych_booking_dashboard_url(),
																											 'icon'      => 'calendar',
																											 'priority'  => 15
																											) )	 +
														array_slice($menus, 3, count($menus) - 3, true) ;
			
		}
		
  	return $menus;
  }
  
	/**
   * WC Booking Scripts
   */
  public function wcb_load_scripts( $end_point ) {
	  global $WCFM, $WCFMu;
    
	  switch( $end_point ) {
	  	case 'wcfm-products-manage':
	  		$bkap_calendar_img = plugins_url() . "/woocommerce-booking/assets/images/cal.gif";
	  		wp_enqueue_script( 'wcfm_wcbookings_products_manage_js', $WCFMu->library->js_lib_url . 'tych_bookings/wcfmu-script-booking-products-manage.js', array('jquery'), $WCFMu->version, true );
	  		
	  		wp_localize_script( 
					'wcfm_wcbookings_products_manage_js', 
					'bkap_settings_params', 
					array(
						'ajax_url'                   => WC()->ajax_url(),
						'post_id'                    => 0,
						'specific_label'             => __( 'Specific Dates', 'woocommerce-booking' ),
						'general_update_msg'         => __( 'General Booking settings have been saved.', 'woocommerce-booking' ),
						'availability_update_msg'    => __( 'Booking Availability settings have been saved.', 'woocommerce-booking' ),
						'gcal_update_msg'            => __( 'Google Calendar Sync settings have been saved.', 'woocommerce-booking' ),
						'only_day_text'              => __( 'Use this for full day bookings or bookings spanning multiple nights.' , 'woocommerce-booking' ),
						'date_time_text'             => __( 'Use this if you wish to take bookings for time slots. For e.g. coaching classes, appointments, ground on rent etc.', 'woocommerce-booking' ),
						'fixed_time_text'            => __( 'Use this if you have fixed time slots for bookings. For e.g. coaching classes, appointments etc.', 'woocommerce-booking' ),
						'duration_time_text'         => __( 'Use this if you want your customer to select required duration for booking. For e.g. sports ground booking, appointments etc.', 'woocommerce-booking' ),
						'single_day_text'            => __( 'Use this to take bookings like single day tours, event, appointments etc.' , 'woocommerce-booking' ),
						'multiple_nights_text'       => __( 'Use this for hotel bookings, rentals, etc. Checkout date is not included in the booking period.', 'woocommerce-booking' ),
						'multiple_nights_price_text' => __( 'Please enter the per night price in the Regular or Sale Price box in the Product meta box as needed. In case if you wish to charge special prices for a weekday, please enter them above.', 'woocommerce-booking' ) 
					) 
				);
	
				// Messages for Block Pricing
				wp_localize_script( 
					'wcfm_wcbookings_products_manage_js', 
					'bkap_block_pricing_params', 
					array(
						'save_fixed_blocks'                 => __( 'Fixed Blocks have been saved.', 'woocommerce-booking' ),
						'delete_fixed_block'                => __( 'Fixed Block have been deleted.', 'woocommerce-booking' ),
						'delete_all_fixed_blocks'           => __( 'All Fixed Blocks have been deleted.', 'woocommerce-booking' ),
						'confirm_delete_fixed_block'        => __( 'Are you sure you want to delete this fixed block?', 'woocommerce-booking' ),
						'confirm_delete_all_fixed_blocks'   => __( 'Are you sure you want to delete all the blocks?', 'woocommerce-booking' ),
						
						'save_price_ranges'                 => __( 'Price ranges have been saved.', 'woocommerce-booking' ),
						'delete_price_range'                => __( 'Price Range have been deleted.', 'woocommerce-booking' ),
						'delete_all_price_ranges'           => __( 'All Price Ranges have been deleted.', 'woocommerce-booking' ),
						'confirm_delete_price_range'        => __( 'Are you sure you want to delete this price range?', 'woocommerce-booking' ),
						'confirm_delete_all_price_ranges'   => __( 'Are you sure you want to delete all the ranges?', 'woocommerce-booking' ), 
					) 
				);
				
				$reousrce_args = array(
									'ajax_url'                   => WC()->ajax_url(),
									'post_id'                    => 0,
									'bkap_calendar'				       => $bkap_calendar_img,
									'delete_resource_conf'		   => __( 'Are you sure you want to delete this resource?' , 'woocommerce-booking' ),
									'delete_resource_conf_all'	 => __( 'Are you sure you want to delete all resources?' , 'woocommerce-booking' ),
									'delete_resource'         	 => __( 'Resource have been deleted.', 'woocommerce-booking' ),
				);
					
				wp_localize_script( 'wcfm_wcbookings_products_manage_js', 'bkap_resource_params', $reousrce_args );
				
				wp_register_script( 
					'multiDatepicker', 
					$WCFMu->library->js_lib_url . 'tych_bookings/jquery-ui.multidatespicker.js', 
					'', 
					$WCFMu->version, 
					true );
				wp_enqueue_script( 'multiDatepicker' );
	
				wp_register_script( 
					'datepick', 
					$WCFMu->library->js_lib_url . 'tych_bookings/jquery.datepick.js', 
					'', 
					$WCFMu->version, 
					true );
				wp_enqueue_script( 'datepick' );
	
				wp_enqueue_script( 
					'bkap-tabsjquery', 
					$WCFMu->library->js_lib_url . 'tych_bookings/zozo.tabs.min.js', 
					'', 
					$WCFMu->version, 
					true );
	  	break;
	  	
	  	case 'wcfm-booking-dashboard':
	    	wp_enqueue_script( 'wcfm_bookings_dashboard_js', $WCFMu->library->js_lib_url . 'tych_bookings/wcfmu-script-booking-dashboard.js', array('jquery'), $WCFMu->version, true );
      break;
      
	  	case 'wcfm-booking':
      	$WCFM->library->load_datatable_lib();
      	$WCFM->library->load_daterangepicker_lib();
	    	wp_enqueue_script( 'wcfm_bookings_js', $WCFMu->library->js_lib_url . 'tych_bookings/wcfmu-script-booking.js', array('jquery', 'dataTables_js'), $WCFMu->version, true );
	    	
	    	// Screen manager
	    	$wcfm_screen_manager = get_option( 'wcfm_screen_manager', array() );
	    	$wcfm_screen_manager_data = array();
	    	if( isset( $wcfm_screen_manager['booking'] ) ) $wcfm_screen_manager_data = $wcfm_screen_manager['booking'];
	    	if( !isset( $wcfm_screen_manager_data['admin'] ) ) {
					$wcfm_screen_manager_data['admin'] = $wcfm_screen_manager_data;
					$wcfm_screen_manager_data['vendor'] = $wcfm_screen_manager_data;
				}
				if( wcfm_is_vendor() ) {
					$wcfm_screen_manager_data = $wcfm_screen_manager_data['vendor'];
				} else {
					$wcfm_screen_manager_data = $wcfm_screen_manager_data['admin'];
				}
				if( apply_filters( 'wcfm_bookings_additonal_data_hidden', true ) ) {
					$wcfm_screen_manager_data[9] = 'yes';
				}
	    	wp_localize_script( 'wcfm_bookings_js', 'wcfm_bookings_screen_manage', $wcfm_screen_manager_data );
      break;
      
      case 'wcfm-booking-details':
      	$WCFM->library->load_datepicker_lib();
	    	wp_enqueue_script( 'wcfm_bookings_details_js', $WCFMu->library->js_lib_url . 'tych_bookings/wcfmu-script-booking-details.js', array('jquery'), $WCFMu->version, true );
      break;
      
      case 'wcfm-booking-resources':
      	$WCFM->library->load_datatable_lib();
	    	wp_enqueue_script( 'wcfmu_bookings_resources_js', $WCFMu->library->js_lib_url . 'tych_bookings/wcfmu-script-booking-resources.js', array('jquery', 'dataTables_js'), $WCFMu->version, true );
      break;
      
      case 'wcfm-booking-resources-manage':
      	$WCFM->library->load_datepicker_lib();
      	$bkap_calendar_img = plugins_url() . "/woocommerce-booking/assets/images/cal.gif";
	    	wp_enqueue_script( 'wcfmu_bookings_resources_manage_js', $WCFMu->library->js_lib_url . 'tych_bookings/wcfmu-script-booking-resources-manage.js', array('jquery', 'jquery-ui-sortable', 'jquery-ui-datepicker'), $WCFMu->version, true );
	    	
	    	// Localized Script
        $wcfm_messages = get_wcfm_resources_manage_messages();
			  wp_localize_script( 'wcfmu_bookings_resources_manage_js', 'wcfm_resources_manage_messages', $wcfm_messages );
			  
				$args = array(
									'ajax_url'                   => WC()->ajax_url(),
									'post_id'                    => 0,
									'bkap_calendar'				       => $bkap_calendar_img,
									'delete_resource_conf'		   => __( 'Are you sure you want to delete this resource?' , 'woocommerce-booking' ),
									'delete_resource_conf_all'	 => __( 'Are you sure you want to delete all resources?' , 'woocommerce-booking' ),
									'delete_resource'         	 => __( 'Resource have been deleted.', 'woocommerce-booking' ),
				);
					
				wp_localize_script( 'wcfmu_bookings_resources_manage_js', 'bkap_resource_params', $args );
	
				wp_localize_script( 'wcfmu_bookings_resources_manage_js', 'bkap_resource_params', $args );
	
      break;
      
      case 'wcfm-booking-manual':
      	$WCFM->library->load_select2_lib();
	    	wp_enqueue_script( 'wcfmu_bookings_manual_js', $WCFMu->library->js_lib_url . 'tych_bookings/wcfmu-script-booking-manual.js', array('jquery', 'select2_js'), $WCFMu->version, true );
      break;
      
      case 'wcfm-booking-calendar':
      	$WCFM->library->load_tiptip_lib();
      	$WCFM->library->load_datepicker_lib();
	    	
	    	wp_enqueue_script( 'jquery' );
				wp_deregister_script( 'jqueryui');
	
				wp_enqueue_script( 
					'bkap-jqueryui', 
					'//ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js', 
					'', 
					$WCFMu->version, 
					false );
	
				wp_register_script( 
					'moment-js', 
					plugins_url( '/woocommerce-booking/assets/js/fullcalendar/lib/moment.min.js' ) );
				wp_register_script( 
					'full-js', 
					plugins_url( '/woocommerce-booking/assets/js/fullcalendar/fullcalendar.min.js' ) );
				wp_register_script( 
					'bkap-images-loaded', 
					plugins_url( '/woocommerce-booking/assets/js/imagesloaded.pkg.min.js' ) );
				wp_register_script( 
					'bkap-qtip', 
					plugins_url( '/woocommerce-booking/assets/js/jquery.qtip.min.js' ), 
					array( 'jquery', 'bkap-images-loaded' ) );
	
				wp_enqueue_script( 
					'booking-calender-js', 
					$WCFMu->library->js_lib_url . 'tych_bookings/wcfmu-script-booking-calendar.js', 
					array( 'jquery', 'bkap-qtip' ,'moment-js', 'full-js', 'bkap-images-loaded', 'jquery-ui-core','jquery-ui-widget','jquery-ui-position', 'jquery-ui-selectmenu' ), $WCFMu->version, true );
				
				if( wcfm_is_vendor() ) {
					woocommerce_booking::localize_script( apply_filters( 'wcfm_current_vendor_id', get_current_user_id() ) );
				} else {
					woocommerce_booking::localize_script();
				}
	    	
	    	
	    	
      break;
      
      case 'wcfm-bookings-settings':
      	//$WCFM->library->load_datepicker_lib();
	    	//wp_enqueue_script( 'wcfmu_bookings_settings_js', $WCFMu->library->js_lib_url . 'tych_bookings/wcfmu-script-booking-settings.js', array('jquery'), $WCFMu->version, true );
      break;
	  }
	}
	
	/**
   * WC Booking Styles
   */
	public function wcb_load_styles( $end_point ) {
	  global $WCFM, $WCFMu;
		
	  switch( $end_point ) {
	  	case 'wcfm-products-manage':
	  		wp_enqueue_style( 'wcfm_wcbookings_products_manage_css',  $WCFMu->library->css_lib_url . 'tych_bookings/wcfmu-style-booking-products-manage.css', array(), $WCFMu->version );
	  		
	  		// css file for the multi datepicker in admin product pages.
			  wp_enqueue_style( 
				'bkap-datepick', 
				plugins_url( '/woocommerce-booking/assets/css/jquery.datepick.css' ), 
				'', 
				$WCFMu->version, 
				false );

				$global_settings = json_decode( get_option( 'woocommerce_booking_global_settings' ) );
				$calendar_theme = "";
	
				if ( isset( $global_settings ) ) {
					$calendar_theme = $global_settings->booking_themes;
				}
				if ( $calendar_theme == "" ) $calendar_theme = 'base';
				
				wp_dequeue_style( 'jquery-ui-style' );
				wp_register_style( 
					'bkap-jquery-ui', 
					plugins_url( "/woocommerce-booking/assets/css/themes/$calendar_theme/jquery-ui.css" ), 
					'', 
					$WCFMu->version, 
					false );
	
				wp_enqueue_style( 'bkap-jquery-ui' );
	  	break;
	  	
	  	case 'wcfm-booking-dashboard':
	    	wp_enqueue_style( 'wcfm_bookings_dashboard_css',  $WCFMu->library->css_lib_url . 'tych_bookings/wcfmu-style-booking-dashboard.css', array(), $WCFMu->version );
		  break;
		  
	    case 'wcfm-booking':
	    	wp_enqueue_style( 'wcfm_bookings_css',  $WCFMu->library->css_lib_url . 'tych_bookings/wcfmu-style-booking.css', array(), $WCFMu->version );
		  break;
		  
		  case 'wcfm-booking-details':
		  	wp_enqueue_style( 'collapsible_css',  $WCFM->library->css_lib_url . 'wcfm-style-collapsible.css', array(), $WCFM->version );
	    	wp_enqueue_style( 'wcfm_bookings_details_css',  $WCFMu->library->css_lib_url . 'tych_bookings/wcfmu-style-booking-details.css', array(), $WCFMu->version );
		  break;
		  
		  case 'wcfm-booking-resources':
	    	wp_enqueue_style( 'wcfmu_bookings_resources_css',  $WCFMu->library->css_lib_url . 'tych_bookings/wcfmu-style-booking-resources.css', array(), $WCFMu->version );
		  break;
		  
		  case 'wcfm-booking-resources-manage':
		  	wp_enqueue_style( 'collapsible_css',  $WCFM->library->css_lib_url . 'wcfm-style-collapsible.css', array(), $WCFMu->version );
	    	wp_enqueue_style( 'wcfmu_bookings_resources_manage_css',  $WCFMu->library->css_lib_url . 'tych_bookings/wcfmu-style-booking-resources-manage.css', array(), $WCFMu->version );
		  break;
		  
		  case 'wcfm-booking-manual':
		  	wp_enqueue_style( 'collapsible_css',  $WCFM->library->css_lib_url . 'wcfm-style-collapsible.css', array(), $WCFMu->version );
	    	wp_enqueue_style( 'wcfmu_bookings_manual_css',  $WCFMu->library->css_lib_url . 'tych_bookings/wcfmu-style-booking-manual.css', array(), $WCFMu->version );
		  break;
		  
		  case 'wcfm-booking-calendar':
		  	wp_enqueue_style( 'collapsible_css',  $WCFM->library->css_lib_url . 'wcfm-style-collapsible.css', array(), $WCFMu->version );
	    	wp_enqueue_style( 'wcfmu_bookings_calendar_css',  $WCFMu->library->css_lib_url . 'tych_bookings/wcfmu-style-booking-calendar.css', array(), $WCFMu->version );
	    	
	    	wp_enqueue_style( 'bkap-data', plugins_url('/woocommerce-booking/assets/css/view.booking.style.css' ) , '', $WCFMu->version, false );
					
				wp_enqueue_style( 'bkap-fullcalendar-css', plugins_url().'/woocommerce-booking/assets/js/fullcalendar/fullcalendar.css' );
					
				// this is for displying the full calender view.
				wp_enqueue_style( 'full-css', plugins_url( '/woocommerce-booking/assets/js/fullcalendar/fullcalendar.css' ) );
					
				// this is used for displying the hover effect in calendar view.
				wp_enqueue_style( 'bkap-qtip-css', plugins_url( '/woocommerce-booking/assets/css/jquery.qtip.min.css' ), array() );
				
				// javascript for handling clicks of calendar icon changes
				wp_register_script( 'bkap-calendar-change', plugins_url( '/woocommerce-booking/assets/js/global-booking-settings.js' ), '', $WCFMu->version, false );
				wp_enqueue_script( 'bkap-calendar-change' );
		  break;
		  
		  case 'wcfm-booking-settings':
		  	//wp_enqueue_style( 'collapsible_css',  $WCFM->library->css_lib_url . 'wcfm-style-collapsible.css', array(), $WCFMu->version );
	    	//wp_enqueue_style( 'wcfmu_bookings_settings_css',  $WCFMu->library->css_lib_url . 'tych_bookings/wcfmu-style-booking-settings.css', array(), $WCFMu->version );
		  break;
	  }
	}
	
	/**
   * WC Booking Views
   */
  public function wcb_load_views( $end_point ) {
	  global $WCFM, $WCFMu;
	  
	  switch( $end_point ) {
	  	case 'wcfm-booking-dashboard':
        $WCFMu->template->get_template( 'tych_bookings/wcfmu-view-booking-dashboard.php' );
      break;
      
	  	case 'wcfm-booking':
        $WCFMu->template->get_template( 'tych_bookings/wcfmu-view-booking.php' );
      break;
      
      case 'wcfm-booking-details':
        $WCFMu->template->get_template( 'tych_bookings/wcfmu-view-booking-details.php' );
      break;
      
      case 'wcfm-booking-resources':
        $WCFMu->template->get_template( 'tych_bookings/wcfmu-view-booking-resources.php' );
      break;
      
      case 'wcfm-booking-resources-manage':
        $WCFMu->template->get_template( 'tych_bookings/wcfmu-view-booking-resources-manage.php' );
      break;
      
      case 'wcfm-booking-manual':
        $WCFMu->template->get_template( 'tych_bookings/wcfmu-view-booking-manual.php' );
      break;
      
      case 'wcfm-booking-calendar':
        $WCFMu->template->get_template( 'tych_bookings/wcfmu-view-booking-calendar.php' );
      break;
      
      case 'wcfm-booking-settings':
        //$WCFMu->template->get_template( 'tych_bookings/wcfmu-view-booking-settings.php' );
      break;
	  }
	}
	
	/**
   * WC Booking Ajax Controllers
   */
  public function wcb_ajax_controller() {
  	global $WCFM, $WCFMu;
  	
  	$controllers_path = $WCFMu->plugin_path . 'controllers/tych_bookings/';
  	
  	$controller = '';
  	if( isset( $_POST['controller'] ) ) {
  		$controller = $_POST['controller'];
  		
  		switch( $controller ) {
  			case 'wcfm-products-manage':
  				include_once( $controllers_path . 'wcfmu-controller-booking-products-manage.php' );
					new WCFMu_Booking_Products_Manage_Controller();
  			break;
  			
  			case 'wcfm-booking':
					include_once( $controllers_path . 'wcfmu-controller-booking.php' );
					if( defined('WCFM_REST_API_CALL') ) {
            $booking_wcfm_manage_object = new WCFMu_Booking_Controller();
            return $booking_wcfm_manage_object->processing();
          } else {
            new WCFMu_Booking_Controller();
          }
				break;
				
				case 'wcfm-booking-resources':
					include_once( $controllers_path . 'wcfmu-controller-booking-resources.php' );
					new WCFMu_Booking_Resources_Controller();
				break;
				
				case 'wcfm-booking-resources-manage':
					include_once( $controllers_path . 'wcfmu-controller-booking-resources-manage.php' );
					new WCFMu_Booking_Resources_Manage_Controller();
				break;
				
				case 'wcfm-booking-settings':
					//include_once( $controllers_path . 'wcfmu-controller-wcbooking-settings.php' );
					//new WCFMu_WCBookings_Settings_Controller();
				break;
				
				case 'wcfm-booking-schedule-manage':
					//include_once( $controllers_path . 'wcfm-controller-wcbooking-schedule-manage.php' );
					//new WCFM_WCBookings_Schedule_Manage_Controller();
				break;
  		}
  	}
  }
	
  /**
   * WC Booking load views
   */
  function wcb_wcfm_products_manage_form_load_views( ) {
		global $WCFM, $WCFMu;
	  
	 $WCFMu->template->get_template( 'tych_bookings/wcfmu-view-booking-products-manage.php' );
	}
	
	/**
   * Handle Booking confirmation
   */
  public function wcfm_booking_mark_confirm() {
  	global $WCFM, $WCFMu;
  	
  	$booking_id = $_POST['bookingid'];
  	
  	$item_id = get_post_meta( $booking_id, '_bkap_order_item_id', true );
		
		bkap_booking_confirmation::bkap_save_booking_status( $item_id, 'confirmed' );
		die;
  }
  
  /**
   * Handle Booking Calendar Popup
   */
  public function wcfm_tych_booking_calender_content() {
  	$content         = '';
		$date_formats    = bkap_get_book_arrays( 'bkap_date_formats' );
		// get the global settings to find the date formats
		$global_settings = json_decode( get_option( 'woocommerce_booking_global_settings' ) );
		$date_format_set = $date_formats[ $global_settings->booking_date_format ];

		$order_txt 		= __( 'Order:', 		'woocommerce-booking' );
		$product_txt 	= __( 'Product Name:', 	'woocommerce-booking' );
		$customer_txt 	= __( 'Customer Name: ','woocommerce-booking' );
		$qty_txt 		= __( 'Quantity: ', 	'woocommerce-booking' );
		$startdate_txt 	= __( 'Start Date: ', 	'woocommerce-booking' );
		$enddate_txt 	= __( 'End Date: ', 	'woocommerce-booking' );
		$time_txt 		= __( 'Time: ', 		'woocommerce-booking' );
		$resource_txt 	= __( 'Resource: ', 	'woocommerce-booking' );


		if( !empty( $_REQUEST['order_id'] ) && ! empty( $_REQUEST[ 'event_value' ] ) ){
			$order_id                    =   $_REQUEST[ 'order_id' ];
			$order                       =   new WC_Order( $order_id );
			
			$order_items                 =   $order->get_items();
			$attribute_name              =   '';
			$attribute_selected_value    =   '';
			
			if ( version_compare( WOOCOMMERCE_VERSION, "3.0.0" ) < 0 ) {
				$billing_first_name          =   $order->billing_first_name;
				$billing_last_name           =   $order->billing_last_name;
			} else {
				$billing_first_name          =   $order->get_billing_first_name();
				$billing_last_name           =   $order->get_billing_last_name();
			}
			
			$value[]                     =   $_REQUEST[ 'event_value' ];

			$content                     =   "<table>";
			
			if( apply_filters( 'wcfm_is_allow_order_details', true ) ) {
				$content                     .=  "<tr> <td> <strong>".$order_txt."</strong></td><td><a style=\"color:#17a2b8;\" href=\"". get_wcfm_view_order_url( $order_id ) ."\">#". $order_id ." </a> </td> </tr>"; 
			}
			
			$content                      .=  "<tr> <td> <strong>".$product_txt."</strong></td><td> ".get_the_title( $value[0]['post_id'] )."</td> </tr>";
			$content                      .=  "<tr> <td> <strong>".$customer_txt."</strong></td><td> ".$billing_first_name . " " . $billing_last_name ."</td> </tr>" ;
			
			foreach ( $order_items as $item_id => $item ) {
				 
				if ( $item[ 'variation_id' ] != '' && $value[ 0 ][ 'post_id' ] == $item[ 'product_id' ] && $value[ 0 ][ 'order_item_id' ] == $item_id ){
					$variation_product               = get_post_meta( $item[ 'product_id' ] );
					$product_variation_array_string  = $variation_product[ '_product_attributes' ];
					$product_variation_array         = unserialize( $product_variation_array_string[0] );
					 
					foreach ( $product_variation_array as $product_variation_key => $product_variation_value ) {		
						if ( isset( $item[ $product_variation_key ] ) && '' !== $item[ $product_variation_key ] ){
					
							$attribute_name              = $product_variation_value[ 'name' ];
							$attribute_selected_value    = $item [ $product_variation_key ];
							$content                    .= " <tr> <td> <strong>".$attribute_name.":</strong></td> <td> ".$attribute_selected_value."</td> </tr> ";
						}
					}
				}
					
				if ( $item[ 'qty' ] != '' && $value[ 0 ][ 'post_id' ] == $item[ 'product_id' ] && $value[ 0 ][ 'order_item_id' ] == $item_id ){
					$content  .= " <tr> <td> <strong>".$qty_txt."</strong></td> <td> ".$item[ 'qty' ]."</td> </tr> ";
				}
				
			}	        	
			if ( isset( $value[ 0 ][ 'start_date' ] ) && $value[ 0 ][ 'start_date' ] != '' ){
				$value_date  = $value[ 0 ][ 'start_date' ];
				$content    .= " <tr> <td> <strong>".$startdate_txt."</strong></td><td> ".$value_date."</td> </tr>";
			}
				
			if ( isset( $value[ 0 ][ 'end_date' ] ) && $value[ 0 ][ 'end_date' ] != '' ){
				$value_end_date  = $value[ 0 ][ 'end_date' ];
				$content        .= " <tr> <td> <strong>".$enddate_txt."</strong></td><td> ".$value_end_date."</td> </tr> ";
			}
				
			// Booking Time
			$time = '';
			if ( isset( $value[ 0 ][ 'from_time' ] ) && $value[ 0 ][ 'from_time' ] != "" && isset( $value[ 0 ][ 'to_time' ] ) && $value[0]['to_time'] != "" ) {
			if ( $global_settings->booking_time_format == 12 ) {
					$to_time     = '';
					$from_time   = date( 'h:i A', strtotime( $value[0]['from_time'] ) );
					$time        = $from_time ;
					
					if ( isset( $value[0]['to_time'] ) && $value[0]['to_time'] != '' ){
						$to_time = date( 'h:i A', strtotime( $value[0]['to_time'] ) );
						$time    = $from_time . " - " . $to_time;
					}
					 
				}else {
					$time = $time = $value[0]['from_time'] . " - " . $value[0]['to_time'];
				}
				
				$content .= "<tr> <td> <strong>".$time_txt."</strong></td><td> ".$time."</td> </tr>";
				
			}else if ( isset( $value[ 0 ][ 'from_time' ] ) && $value[ 0 ][ 'from_time' ] != "" ) {
			if ( $global_settings->booking_time_format == 12 ) {
					
					$to_time = '';
					$from_time = date( 'h:i A', strtotime( $value[0]['from_time'] ) );
					$time = $from_time. " - Open-end" ;
				}else {
					$time = $time = $value[0]['from_time'] ." - Open-end";
				}
				$content .= "<tr> <td> <strong>".$time_txt."</strong></td><td> ".$time."</td> </tr>";
			}
			
			if ( isset( $value[ 0 ][ 'resource' ] ) && $value[ 0 ][ 'resource' ] != '' ){
				$value_resource  = $value[ 0 ][ 'resource' ];
				$content        .= " <tr> <td> <strong>".$resource_txt."</strong></td><td> ".$value_resource."</td> </tr> ";
			}
			
			$content .= '</table>';
				
			if ( $value[0]['post_id'] ){
				$post_image = get_the_post_thumbnail( $value[0]['post_id'], array( 100, 100 ) );
				
				if ( !empty( $post_image ) ){
					$content = '<div style="float:left; margin:0px 5px 5px 0px; ">'.$post_image.'</div>'.$content;
				}
			}
		}

		echo $content;
		die();
  }
  
  /**
   * Handle Booking Details Status Update
   */
  public function wcfm_modify_booking_status() {
  	global $WCFM, $WCFMu;
  	
  	$booking_id = $_POST['booking_id'];
  	$booking_status = $_POST['booking_status'];
  	
  	$item_id = get_post_meta( $booking_id, '_bkap_order_item_id', true );
		
		bkap_booking_confirmation::bkap_save_booking_status( $item_id, $booking_status );
  	
  	// Status Update Notification
  	$user_id = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );
		$shop_name =  get_user_by( 'ID', $user_id )->display_name;
		if( wcfm_is_vendor() ) {
			$shop_name =  $WCFM->wcfm_vendor_support->wcfm_get_vendor_store_by_vendor( absint($user_id) );
		}
  	$wcfm_messages = sprintf( __( '<b>%s</b> booking status updated to <b>%s</b> by <b>%s</b>', 'wc-frontend-manager-ultimate' ), '#<a target="_blank" class="wcfm_dashboard_item_title" href="' . get_wcfm_view_booking_url($booking_id) . '">' . $booking_id . '</a>', ucfirst( $booking_status ), $shop_name );
		$WCFM->wcfm_notification->wcfm_send_direct_message( -2, 0, 1, 0, $wcfm_messages, 'status-update' );
  	
  	echo '{"status": true, "message": "' . __( 'Booking status updated.', 'wc-frontend-manager-ultimate' ) . '"}';
  	
		die;
  }
	
	/**
	 * Add vendor email to booking admin emails - 2.6.2
	 */
	public function wcfm_filter_booking_emails( $recipients, $this_email ) {
		global $WCFM;
		if ( ! empty( $this_email ) ) {
			if( $WCFM->is_marketplace ) {
				
				$vendor_email = $WCFM->wcfm_vendor_support->wcfm_get_vendor_email_from_product( $this_email->product_id );
				if ( isset( $recipients ) ) {
					$recipients .= ',' . $vendor_email;
				} else {
					$recipients = $vendor_email;
				}
		
			}
		}

		return $recipients;
	}
}