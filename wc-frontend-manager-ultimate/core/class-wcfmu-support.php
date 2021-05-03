<?php
/**
 * WCFM plugin core
 *
 * Support board core
 *
 * @author 		WC Lovers
 * @package 	wcfmu/core
 * @version   4.0.3
 */
 
class WCFMu_Support {
	
	public $wcfm_myaccount_view_support_ticket_endpoint = 'support-tickets';
	public $wcfm_myaccount_view_inquiry_endpoint = 'view-support-ticket';
		

	public function __construct() {
		global $WCFM, $WCFMu;
		
		$wcfm_myac_modified_endpoints = wcfm_get_option( 'wcfm_myac_endpoints', array() );
		$this->wcfm_myaccount_support_ticket_endpoint = ! empty( $wcfm_myac_modified_endpoints['support-tickets'] ) ? $wcfm_myac_modified_endpoints['support-tickets'] : 'support-tickets';
		$this->wcfm_myaccount_view_support_ticket_endpoint = ! empty( $wcfm_myac_modified_endpoints['view-support-ticket'] ) ? $wcfm_myac_modified_endpoints['view-support-ticket'] : 'view-support-ticket';
		
		add_filter( 'wcfm_query_vars', array( &$this, 'wcfm_support_query_vars' ), 20 );
		add_filter( 'wcfm_endpoint_title', array( &$this, 'wcfm_support_endpoint_title' ), 20, 2 );
		add_action( 'init', array( &$this, 'wcfm_support_init' ), 20 );
		
		// Support Endpoint Edit
		add_filter( 'wcfm_endpoints_slug', array( $this, 'support_wcfm_endpoints_slug' ) );
		
		// Support menu on WCfM dashboard
		if( apply_filters( 'wcfm_is_allow_support', true ) ) {
			add_filter( 'wcfm_menus', array( &$this, 'wcfm_support_menus' ), 30 );
		}
		
		// Support Load Scripts
		add_action( 'wcfm_load_scripts', array( &$this, 'load_scripts' ), 30 );
		add_action( 'after_wcfm_load_scripts', array( &$this, 'load_scripts' ), 30 );
		
		// Support Load Styles
		add_action( 'wcfm_load_styles', array( &$this, 'load_styles' ), 30 );
		add_action( 'after_wcfm_load_styles', array( &$this, 'load_styles' ), 30 );
		
		// Support Load views
		add_action( 'wcfm_load_views', array( &$this, 'load_views' ), 30 );
		
		// Support Ajax Controllers
		add_action( 'after_wcfm_ajax_controller', array( &$this, 'ajax_controller' ) );
		add_action( 'wp_ajax_nopriv_wcfm_ajax_controller', array( &$this, 'ajax_controller' ) );
		
		// My Account Support End Point
		add_action( 'init', array( &$this, 'wcfm_support_my_account_endpoints' ) );
		
		// My Account Support Query Vars
		add_filter( 'query_vars', array( &$this, 'wcfm_support_my_account_query_vars' ), 0 );
		
		// My Account Support Rule Flush
		register_activation_hook( $WCFMu->file, array( &$this,'wcfm_support_my_account_flush_rewrite_rules' ) );
		register_deactivation_hook( $WCFMu->file, array( &$this, 'wcfm_support_my_account_flush_rewrite_rules' ) );
		
		// My Account Support Menu
		add_filter( 'woocommerce_account_menu_items', array( &$this, 'wcfm_support_my_account_menu_items' ), 201 );
		
		// My Account Support End Point Title
		add_filter( 'the_title', array( &$this, 'wcfm_support_my_account_endpoint_title' ) );
		
		// My Account Support End Point Content
		add_action( 'woocommerce_account_'.$this->wcfm_myaccount_support_ticket_endpoint.'_endpoint', array( &$this, 'wcfm_support_my_account_endpoint_content' ) );
		add_action( 'woocommerce_account_'.$this->wcfm_myaccount_view_support_ticket_endpoint.'_endpoint', array( &$this, 'wcfm_support_view_my_account_endpoint_content' ) );
		
		// WC My Account Order action
		add_filter( 'woocommerce_my_account_my_orders_actions', array( &$this, 'wcfm_support_order_action' ), 100, 2 );
		
		// Generate Support Form Html
    add_action('wp_ajax_wcfmu_support_form_html', array( &$this, 'wcfmu_support_form_html' ) );
		
		// Delete Support
		add_action( 'wp_ajax_delete_wcfm_support', array( &$this, 'delete_wcfm_support' ) );
		
		// Support list in WCFM Dashboard
		//add_action( 'after_wcfm_dashboard_zone_analytics', array( $this, 'wcfm_dashboard_support_list' ) );
		
		// Support direct message type
		add_filter( 'wcfm_message_types', array( &$this, 'wcfm_support_message_types' ), 25 );
		
		//enqueue scripts
		add_action('wp_enqueue_scripts', array(&$this, 'wcfm_support_scripts'));
		//enqueue styles
		add_action('wp_enqueue_scripts', array(&$this, 'wcfm_support_styles'));
		
		// Disable Dokan Support Button
		add_action( 'dokan_after_store_tabs', array( $this, 'wcfm_disable_dokan_support_button' ), 20 );
		
		// Disable WC Marketplace Report Abuse
		add_action( 'wcmp_show_report_abuse_link', array(&$this, 'wcfm_disable_wcmp_report_abuse'), 40, 2 );
	}
	
	/**
   * Support Query Var
   */
  function wcfm_support_query_vars( $query_vars ) {
  	$wcfm_modified_endpoints = wcfm_get_option( 'wcfm_endpoints', array() );
  	
		$query_support_vars = array(
			'wcfm-support'                 => ! empty( $wcfm_modified_endpoints['wcfm-support'] ) ? $wcfm_modified_endpoints['wcfm-support'] : 'support',
			'wcfm-support-manage'          => ! empty( $wcfm_modified_endpoints['wcfm-support-manage'] ) ? $wcfm_modified_endpoints['wcfm-support-manage'] : 'support-manage'
		);
		
		$query_vars = array_merge( $query_vars, $query_support_vars );
		
		return $query_vars;
  }
  
  /**
   * Support End Point Title
   */
  function wcfm_support_endpoint_title( $title, $endpoint ) {
  	global $wp;
  	switch ( $endpoint ) {
  		case 'wcfm-support' :
				$title = __( 'Support Tickets', 'wc-frontend-manager-ultimate' );
			break;
			case 'wcfm-support-manage' :
				$title = __( 'Support Ticket Manager', 'wc-frontend-manager-ultimate' );
			break;
  	}
  	
  	return $title;
  }
  
  /**
   * Support Endpoint Intialize
   */
  function wcfm_support_init() {
  	global $WCFM_Query;
	
		// Intialize WCFM End points
		$WCFM_Query->init_query_vars();
		$WCFM_Query->add_endpoints();
		
		add_rewrite_endpoint( $this->wcfm_myaccount_support_ticket_endpoint, EP_ROOT | EP_PAGES );
		add_rewrite_endpoint( $this->wcfm_myaccount_view_support_ticket_endpoint, EP_ROOT | EP_PAGES );
		
		if( !get_option( 'wcfm_updated_end_point_support' ) ) {
			// Flush rules after endpoint update
			flush_rewrite_rules();
			update_option( 'wcfm_updated_end_point_support', 1 );
		}
  }
  
  /**
	 * Support Endpoiint Edit
	 */
	function support_wcfm_endpoints_slug( $endpoints ) {
		
		$support_endpoints = array(
													'wcfm-support'          => 'support',
													'wcfm-support-manage'   => 'support-manage',
													);
		
		$endpoints = array_merge( $endpoints, $support_endpoints );
		
		return $endpoints;
	}
	
	/**
   * WCFM Support Menu
   */
  function wcfm_support_menus( $menus ) {
  	global $WCFM;
  		
		$menus = array_slice($menus, 0, 3, true) +
												array( 'wcfm-support' => array( 'label'  => __( 'Support', 'wc-frontend-manager-ultimate' ),
																										 'url'        => wcfm_support_url(),
																										 'icon'       => 'life-ring',
																										 'priority'   => 69.2
																										) )	 +
													array_slice($menus, 3, count($menus) - 3, true) ;
  	return $menus;
  }  
	
	/**
	 * Support Categories
	 */
	function wcfm_support_categories() {
		global $WCFM, $WCFMu;
		$support_categories = array(
													0 => __( 'General query', 'wc-frontend-manager-ultimate' ),
													1 => __( 'Suggestion', 'wc-frontend-manager-ultimate' ),
													2 => __( 'Delivery issue', 'wc-frontend-manager-ultimate' ),
													3 => __( 'Damage item received', 'wc-frontend-manager-ultimate' ),
													4 => __( 'Wrong item received', 'wc-frontend-manager-ultimate' ),
													5 => __( 'Other', 'wc-frontend-manager-ultimate' )
													);
		
		return apply_filters( 'wcfm_support_categories', $support_categories );
	}
	
	/**
	 * Support Priority types
	 */
	function wcfm_support_priority_types( $with_all = false ) {
		global $WCFM, $WCFMu;
		
		if( $with_all ) {
			$support_priority_types = array(
				                    'all'    => __( 'All', 'wc-frontend-manager-ultimate' ),
														'normal'   => __( 'Normal', 'wc-frontend-manager-ultimate' ), 
														'low'      => __( 'Low', 'wc-frontend-manager-ultimate' ),
														'medium'   => __( 'Medium', 'wc-frontend-manager-ultimate' ),
														'high'     => __( 'High', 'wc-frontend-manager-ultimate' ),
														'urgent'   => __( 'Urgent', 'wc-frontend-manager-ultimate' ),
														'critical' => __( 'Critical', 'wc-frontend-manager-ultimate' ),
														);
		} else {
			$support_priority_types = array(
													'normal'   => __( 'Normal', 'wc-frontend-manager-ultimate' ), 
													'low'      => __( 'Low', 'wc-frontend-manager-ultimate' ),
													'medium'   => __( 'Medium', 'wc-frontend-manager-ultimate' ),
													'high'     => __( 'High', 'wc-frontend-manager-ultimate' ),
													'urgent'   => __( 'Urgent', 'wc-frontend-manager-ultimate' ),
													'critical' => __( 'Critical', 'wc-frontend-manager-ultimate' ),
													);
		}
		
		return apply_filters( 'wcfm_support_priority_types', $support_priority_types );
	}
	
	/**
	 * Support Status types
	 */
	function wcfm_support_status_types( $with_all = false ) {
		global $WCFM, $WCFMu;
		
		if( $with_all ) {
			$support_status_types = array(
														'all'    => __( 'All', 'wc-frontend-manager-ultimate' ),
														'open'   => __( 'Open', 'wc-frontend-manager-ultimate' ), 
														'close'  => __( 'Closed', 'wc-frontend-manager-ultimate' ),
														);
		} else {
			$support_status_types = array(
													'open'   => __( 'Open', 'wc-frontend-manager-ultimate' ), 
													'close'  => __( 'Closed', 'wc-frontend-manager-ultimate' ),
													);
		}
		
		return apply_filters( 'wcfm_support_status_types', $support_status_types );
	}
  
  /**
   * Support Scripts
   */
  public function load_scripts( $end_point ) {
	  global $WCFM, $WCFMu;
    
	  switch( $end_point ) {
	  	case 'wcfm-support':
      	$WCFM->library->load_datatable_lib();
      	$WCFM->library->load_daterangepicker_lib();
      	$WCFM->library->load_select2_lib();
      	wp_enqueue_script( 'wcfmu_support_js', $WCFMu->library->js_lib_url . 'support/wcfmu-script-support.js', array('jquery'), $WCFMu->version, true );
      	
      	$wcfm_screen_manager_data = array();
    		if( !$WCFMu->is_marketplace || wcfm_is_vendor() ) {
	    		$wcfm_screen_manager_data[7] = 'yes';
	    	}
	    	$wcfm_screen_manager_data = apply_filters( 'wcfm_screen_manager_data_columns', $wcfm_screen_manager_data, 'support' );
	    	wp_localize_script( 'wcfmu_support_js', 'wcfm_support_screen_manage', $wcfm_screen_manager_data );
      break;
      
      case 'wcfm-support-manage':
      	$WCFM->library->load_select2_lib();
      	$WCFM->library->load_multiinput_lib();
      	$WCFM->library->load_collapsible_lib();
      	wp_enqueue_script( 'wcfmu_support_manage_js', $WCFMu->library->js_lib_url . 'support/wcfmu-script-support-manage.js', array('jquery'), $WCFMu->version, true );
      	// Localized Script
        $wcfm_messages = get_wcfm_support_manage_messages();
			  wp_localize_script( 'wcfmu_support_manage_js', 'wcfm_support_manage_messages', $wcfm_messages );
      break;
	  }
	}
	
	/**
   * Support Styles
   */
	public function load_styles( $end_point ) {
	  global $WCFM, $WCFMu;
		
	  switch( $end_point ) {
	  	case 'wcfm-support':
		    wp_enqueue_style( 'wcfmu_support_css',  $WCFMu->library->css_lib_url . 'support/wcfmu-style-support.css', array(), $WCFMu->version );
		  break;
		  
		  case 'wcfm-support-manage':
		  	wp_enqueue_style( 'collapsible_css',  $WCFM->library->css_lib_url . 'wcfm-style-collapsible.css', array(), $WCFM->version );
		  	wp_enqueue_style( 'wcfmu_support_manage_css',  $WCFMu->library->css_lib_url . 'support/wcfmu-style-support-manage.css', array(), $WCFMu->version );
		  break;
	  }
	}
	
	/**
   * Support Views
   */
  public function load_views( $end_point ) {
	  global $WCFM, $WCFMu;
	  
	  switch( $end_point ) {
	  	case 'wcfm-support':
        $WCFMu->template->get_template( 'support/wcfmu-view-support.php' );
      break;
      
      case 'wcfm-support-manage':
        $WCFMu->template->get_template( 'support/wcfmu-view-support-manage.php' );
      break;
	  }
	}
	
	/**
   * Support Ajax Controllers
   */
  public function ajax_controller() {
  	global $WCFM, $WCFMu;
  	
  	$controllers_path = $WCFMu->plugin_path . 'controllers/support/';
  	
  	$controller = '';
  	if( isset( $_POST['controller'] ) ) {
  		$controller = $_POST['controller'];
  		
  		switch( $controller ) {
  			case 'wcfm-support':
					include_once( $controllers_path . 'wcfmu-controller-support.php' );
					
					if( defined('WCFM_REST_API_CALL') ) {
            $wcfm_support_manage_object = new WCFMu_Support_Controller();
            return $wcfm_support_manage_object->processing();
          } else {
            new WCFMu_Support_Controller();
          }
				break;
				
				case 'wcfm-support-manage':
					include_once( $controllers_path . 'wcfmu-controller-support-manage.php' );
					new WCFMu_Support_Manage_Controller();
				break;
				
				case 'wcfm-support-form':
					include_once( $controllers_path . 'wcfmu-controller-support-form.php' );
					new WCFMu_Support_Form_Controller();
				break;
				
				case 'wcfm-my-account-support-manage':
					include_once( $controllers_path . 'wcfmu-controller-support-manage.php' );
					new WCFMu_My_Account_Support_Manage_Controller();
				break;
  		}
  	}
  }
  
  function wcfm_support_my_account_endpoints() {
		add_rewrite_endpoint( $this->wcfm_myaccount_support_ticket_endpoint, EP_ROOT | EP_PAGES );
		add_rewrite_endpoint( $this->wcfm_myaccount_view_support_ticket_endpoint, EP_ROOT | EP_PAGES );
	}
	
	function wcfm_support_my_account_query_vars( $vars ) {
		$vars[] = $this->wcfm_myaccount_support_ticket_endpoint;
		$vars[] = $this->wcfm_myaccount_view_support_ticket_endpoint;
	
		return $vars;
	}
	
	function wcfm_support_my_account_flush_rewrite_rules() {
		add_rewrite_endpoint( $this->wcfm_myaccount_support_ticket_endpoint, EP_ROOT | EP_PAGES );
		add_rewrite_endpoint( $this->wcfm_myaccount_view_support_ticket_endpoint, EP_ROOT | EP_PAGES );
		flush_rewrite_rules();
	}
	
	function wcfm_support_my_account_menu_items( $items ) {
		// Remove the logout menu item.
		//$logout = $items['customer-logout'];
		//unset( $items['customer-logout'] );
		
		// Insert your custom endpoint.
		$items = array_slice($items, 0, count($items) - 3, true) +
																	array(
																				$this->wcfm_myaccount_support_ticket_endpoint => __( 'Support Tickets', 'wc-frontend-manager-ultimate' )
																				) +
																	array_slice($items, count($items) - 3, count($items) - 1, true) ;
		//$items['support-tickets'] = __( 'Support Tickets', 'wc-frontend-manager-ultimate' );
	
		// Insert back the logout item.
		//$items['customer-logout'] = __( 'Logout', 'wc-frontend-manager-ultimate' );
	
		return $items;
	}
	
	function wcfm_support_my_account_endpoint_title( $title ) {
		global $wp_query;
	
		$is_endpoint = isset( $wp_query->query_vars[$this->wcfm_myaccount_support_ticket_endpoint] );
	
		if ( $is_endpoint && ! is_admin() && is_main_query() && in_the_loop() && is_account_page() ) {
			// New page title.
			$title = __( 'Support Tickets', 'wc-frontend-manager-ultimate' );
			remove_filter( 'the_title', array( $this, 'wcfm_support_my_account_endpoint_title' ) );
		}
		
		$is_endpoint = isset( $wp_query->query_vars[$this->wcfm_myaccount_view_support_ticket_endpoint] );
	
		if ( $is_endpoint && ! is_admin() && is_main_query() && in_the_loop() && is_account_page() ) {
			// New page title.
			$title = __( 'Support Ticket', 'wc-frontend-manager-ultimate' ) . ' #' . sprintf( '%06u', $wp_query->query_vars[$this->wcfm_myaccount_view_support_ticket_endpoint] );
			remove_filter( 'the_title', array( $this, 'wcfm_support_my_account_endpoint_title' ) );
		}
	
		return $title;
	}
	
	function wcfm_support_my_account_endpoint_content() {
		global $WCFM, $WCFMu, $wpdb;
		$WCFMu->template->get_template( 'support/wcfmu-view-my-account-support.php' );
	}
	
	function wcfm_support_view_my_account_endpoint_content() {
		global $_POST, $wp_query, $wp, $WCFM, $WCFMu;
		$WCFMu->template->get_template( 'support/wcfmu-view-my-account-support-manage.php' );
	}
  
  /**
   * WCFM Support action at My Account Order actions
   */
  function wcfm_support_order_action( $actions, $order ) {
  	global $WCFM, $WCFMu;
  	
  	$order_status = sanitize_title( $order->get_status() );
		if( in_array( $order_status, apply_filters( 'wcfm_support_disable_order_status', array( 'failed', 'cancelled', 'refunded', 'pending', 'on-hold', 'request', 'proposal', 'proposal-sent', 'proposal-expired', 'proposal-rejected', 'proposal-canceled', 'proposal-accepted' ) ) ) ) return $actions;
  	
  	$order_id = $order->get_id();
  	$actions['wcfm-support-action'] = array( 'name' => __( 'Support', 'wc-frontend-manager-ultimate' ), 'url' => '#' . $order_id );
  	return $actions;
  }
  
  /**
   * Support Form HTML
   */
  function wcfmu_support_form_html() {
  	global $WCFM, $WCFMu, $_POST;
  	if( isset( $_POST['order_id'] ) && !empty( $_POST['order_id'] ) ) {
  		$WCFMu->template->get_template( 'support/wcfmu-view-support-popup.php' );
  	}
  	die;
  }
  
  /**
	 * Support Reply Attachments Get/Show
	 */
	public function wcfm_support_reply_attachments( $wcfm_support_reply_id, $context = 'view' ) {
		global $WCFM, $WCFMu, $wpdb;
		
		$attachments = '';
		if( $wcfm_support_reply_id ) {
			$wcfm_options = $WCFM->wcfm_options;
			$wcfm_support_allow_attachment = isset( $wcfm_options['wcfm_support_allow_attachment'] ) ? $wcfm_options['wcfm_support_allow_attachment'] : 'yes';
			if( ( $wcfm_support_allow_attachment == 'yes' ) && apply_filters( 'wcfm_is_allow_support_reply_attachment', true ) ) {
				$wcfm_support_attachments = $wpdb->get_results( "SELECT value from {$wpdb->prefix}wcfm_support_response_meta WHERE `key` = 'attchment' AND `support_response_id` = " . $wcfm_support_reply_id );
				if( !empty( $wcfm_support_attachments ) ) {
					foreach( $wcfm_support_attachments as $wcfm_support_attachment ) {
						if( $wcfm_support_attachment->value ) {
							$attachments = maybe_unserialize( $wcfm_support_attachment->value );
							if( $attachments && is_array( $attachments ) && !empty( $attachments ) ) {
								if( $context == 'view' ) {
									echo '<div class="wcfm_clearfix"></div><br /><h2 style="font-size:15px;">' . __( 'Attachment(s)', 'wc-frontend-manager' ) . '</h2><div class="wcfm_clearfix"></div>';
									foreach( $attachments as $attachment ) {
										echo '<a class="wcfm-wp-fields-uploader wcfm_linked_attached" target="_blank" style="width:32px;height:32px;margin-right:10px;" href="' . $attachment . '"><span style="font-size:32px;color:	#f86c6b;display:inline-block;" class="wcfmfa fa-file-image"></span></a>';
									}
									return;
								}
							}
						}
					}
				}
			}
		}
		
		return $attachments;
	}
  
  /**
   * Delete Support 
   */
  function delete_wcfm_support() {
  	global $WCFM, $wpdb, $_POST;
  	
  	if( isset( $_POST['supportid'] ) && !empty( $_POST['supportid'] ) ) {
  		$supportid = $_POST['supportid'];
  		$wpdb->query( "DELETE FROM {$wpdb->prefix}wcfm_support WHERE ID = {$supportid}" );
  		$wpdb->query( "DELETE FROM {$wpdb->prefix}wcfm_support_meta WHERE support_id = {$supportid}" );
  		$wpdb->query( "DELETE FROM {$wpdb->prefix}wcfm_support_response WHERE support_id = {$supportid}" );
  	}
  	
  	echo "success";
  	die;
  }
	
	/**
   * Support List on WCFM Dashboard
   *
   * @since 4.0.4
   */
	function wcfm_dashboard_support_list() {
		global $WCFM, $WCFMu, $wpdb;
		
		if( apply_filters( 'wcfm_is_pref_support', true ) && apply_filters( 'wcfm_is_allow_support', true ) ) {
			$vendor_id = apply_filters( 'wcfm_message_author', get_current_user_id() );
			
			$support_query = "SELECT * FROM {$wpdb->prefix}wcfm_enquiries AS wcfm_enquiries";
			$support_query .= " WHERE 1 = 1";
			$support_query .= " AND `reply` = ''";
			if( wcfm_is_vendor() ) { 
				$support_query .= " AND `vendor_id` = {$vendor_id}";
			}
			$support_query = apply_filters( 'wcfm_enquery_list_query', $support_query );
			$support_query .= " ORDER BY wcfm_enquiries.`ID` DESC";
			$support_query .= " LIMIT 8";
			$support_query .= " OFFSET 0";
			
			$wcfm_supports_array = $wpdb->get_results( $support_query );
			
			?>
			<div class="wcfm_dashboard_enquiries">
				<div class="page_collapsible" id="wcfm_dashboard_enquiries"><span class="wcfmfa fa-question-circle fa-question-circle-o"></span><span class="dashboard_widget_head"><?php _e('Enquiries', 'wc-frontend-manager-ultimate'); ?></span></div>
				<div class="wcfm-container">
					<div id="wcfm_dashboard_enquiries_expander" class="wcfm-content">
					  <?php
					  if( !empty( $wcfm_supports_array ) ) {
					  	$counter = 0;
							foreach($wcfm_supports_array as $wcfm_supports_single) {
								if( $counter == 6 ) break;
								echo '<div class="wcfm_dashboard_support"><a href="' . get_wcfm_support_manage_url($wcfm_supports_single->ID) . '" class="wcfm_dashboard_item_title"><span class="wcfmfa fa-question-circle-o"></span>' . substr( $wcfm_supports_single->support, 0, 80 ) . ' ...</a></div>';
								$counter++;
							}
							if( count( $wcfm_supports_array ) > 6 ) {
								echo '<div class="wcfm_dashboard_support_show_all"><a class="wcfm_submit_button" href="' . get_wcfm_support_url() . '">' . __( 'Show All', 'wc-frontend-manager-ultimate' ) . ' >></a></div>';
							}
						} else {
							_e( 'There is no support yet!!', 'wc-frontend-manager-ultimate' );
						}
						?>
					</div>
				</div>
			</div>
			<?php
		}
	}
	
	/**
   * Support Tab content on Single Product
   */
	function wcfm_support_product_tab_content() {
		global $WCFM, $WCFMu, $wp;
		$WCFMu->template->get_template( 'support/wcfmu-view-support-tab.php' );
	}
	
	function wcfm_support_message_types( $message_types ) {
		$message_types['support'] = __( 'New Support', 'wc-frontend-manager-ultimate' );
		return $message_types;
	}
	
	/**
	 * WCFM Support JS
	 */
	function wcfm_support_scripts() {
 		global $WCFM, $WCFMu, $wp, $WCFM_Query;
 		
 		if( is_account_page() ) {
 			if( is_user_logged_in() ) {
				if( isset( $wp->query_vars[$this->wcfm_myaccount_view_support_ticket_endpoint] ) && !empty( $wp->query_vars[$this->wcfm_myaccount_view_support_ticket_endpoint] ) ) {
					$WCFM->library->load_blockui_lib();
					$WCFM->library->load_select2_lib();
					$WCFM->library->load_multiinput_lib();
					$WCFM->library->load_collapsible_lib();
					wp_enqueue_script( 'wcfmu_support_manage_js', $WCFMu->library->js_lib_url . 'support/wcfmu-script-my-account-support-manage.js', array('jquery'), $WCFMu->version, true );
					// Localized Script
					$wcfm_messages = get_wcfm_support_manage_messages();
					wp_localize_script( 'wcfmu_support_manage_js', 'wcfm_support_manage_messages', $wcfm_messages );
					
					$wcfm_dashboard_messages = get_wcfm_dashboard_messages();
					wp_localize_script( 'wcfmu_support_manage_js', 'wcfm_dashboard_messages', $wcfm_dashboard_messages );
				} else {
					$WCFM->library->load_blockui_lib();
					wp_enqueue_script( 'wcfmu_support_popup_js', $WCFMu->library->js_lib_url . 'support/wcfmu-script-support-popup.js', array('jquery' ), $WCFMu->version, true );
					// Localized Script
					$wcfm_messages = get_wcfm_support_manage_messages();
					wp_localize_script( 'wcfmu_support_popup_js', 'wcfm_support_manage_messages', $wcfm_messages );
				}
			}
 		}
 	}
 	
 	/**
 	 * WCFM Support CSS
 	 */
 	function wcfm_support_styles() {
 		global $WCFM, $WCFMu, $wp, $WCFM_Query;
 		
 		if( is_account_page() ) {
 			if( is_user_logged_in() ) {
 				if( isset( $wp->query_vars[$this->wcfm_myaccount_view_support_ticket_endpoint] ) && !empty( $wp->query_vars[$this->wcfm_myaccount_view_support_ticket_endpoint] ) ) {
 					//wp_enqueue_style( 'collapsible_css',  $WCFM->library->css_lib_url . 'wcfm-style-collapsible.css', array(), $WCFM->version );
 					wp_enqueue_style( 'wcfm_menu_css',  $WCFM->library->css_lib_url . 'menu/wcfm-style-menu.css', array(), $WCFM->version );
 					wp_enqueue_style( 'wcfmu_support_manage_css',  $WCFMu->library->css_lib_url . 'support/wcfmu-style-support-manage.css', array(), $WCFMu->version );
 					wp_enqueue_style( 'wcfmu_my_account_support_manage_css',  $WCFMu->library->css_lib_url . 'support/wcfmu-style-my-account-support-manage.css', array(), $WCFMu->version );
 				} else {
 					wp_enqueue_style( 'wcfmu_support_popup_css',  $WCFMu->library->css_lib_url . 'support/wcfmu-style-support-popup.css', array(), $WCFMu->version );
 				}
 			}
 		}
 	}
 	
 	/**
 	 * Disable Dokan Support Button
 	 */
 	function wcfm_disable_dokan_support_button( $store_id ) {
 		if( apply_filters( 'wcfm_is_allow_dokan_support_disable', true ) ) {
 			?>
 			<style>
 			.dokan-store-support-btn-wrap { display: none !important; }
 			</style>
 			<?php
 		}
 	}
 	
 	/**
 	 * Disable WC Marketplace Report Abuse
 	 */
 	function wcfm_disable_wcmp_report_abuse( $allow, $product ) {
 		if( apply_filters( 'wcfm_is_allow_wcmp_report_abuse_disable', true ) ) {
 			return false;
 		}
 		return $allow;
 	}
}