<?php
/**
 * WCFMu plugin core
 *
 * Plugin Vendor Followers Controler
 *
 * @author 		WC Lovers
 * @package 	wcfmu/core
 * @version   4.0.5
 */
 
class WCFMu_Vendor_Followers {
	
	public $wcfm_followers_options = array();
	public $wcfm_myaccount_followings_endpoint = 'followings';
	
	public function __construct() {
		global $WCFM, $WCFMu;
		
		$this->wcfm_followers_options = get_option( 'wcfm_followers_options', array() );
		
		$wcfm_myac_modified_endpoints = wcfm_get_option( 'wcfm_myac_endpoints', array() );
		$this->wcfm_myaccount_followings_endpoint = ! empty( $wcfm_myac_modified_endpoints['followings'] ) ? $wcfm_myac_modified_endpoints['followings'] : 'followings';
		
		add_filter( 'wcfm_query_vars', array( &$this, 'wcfm_followers_query_vars' ), 20 );
		add_filter( 'wcfm_endpoint_title', array( &$this, 'wcfm_followers_endpoint_title' ), 20, 2 );
		add_action( 'init', array( &$this, 'wcfm_followers_init' ), 20 );
		
		// Followers Endpoint Edit
		add_filter( 'wcfm_endpoints_slug', array( $this, 'followers_wcfm_endpoints_slug' ) );
		
		// Followers menu on WCfM dashboard
		if( apply_filters( 'wcfm_is_allow_followers', true ) ) {
			add_filter( 'wcfm_menus', array( &$this, 'wcfm_followers_menus' ), 30 );
		}
		
		// Followers Load Scripts
		add_action( 'wcfm_load_scripts', array( &$this, 'load_scripts' ), 30 );
		add_action( 'after_wcfm_load_scripts', array( &$this, 'load_scripts' ), 30 );
		
		// Followers Load Styles
		add_action( 'wcfm_load_styles', array( &$this, 'load_styles' ), 30 );
		add_action( 'after_wcfm_load_styles', array( &$this, 'load_styles' ), 30 );
		
		// Followers Load views
		add_action( 'wcfm_load_views', array( &$this, 'load_views' ), 30 );
		
		// Followers Ajax Controllers
		add_action( 'after_wcfm_ajax_controller', array( &$this, 'ajax_controller' ) );
		add_action( 'wp_ajax_nopriv_wcfm_ajax_controller', array( &$this, 'ajax_controller' ) );
		
		// My Account Followers End Point
		add_action( 'init', array( &$this, 'wcfm_followers_my_account_endpoints' ) );
		
		// My Account Followers Query Vars
		add_filter( 'query_vars', array( &$this, 'wcfm_followers_my_account_query_vars' ), 0 );
		
		// My Account Followers Rule Flush
		register_activation_hook( $WCFMu->file, array( &$this,'wcfm_followers_my_account_flush_rewrite_rules' ) );
		register_deactivation_hook( $WCFMu->file, array( &$this, 'wcfm_followers_my_account_flush_rewrite_rules' ) );
		
		// My Account Followers Menu
		add_filter( 'woocommerce_account_menu_items', array( &$this, 'wcfm_followers_my_account_menu_items' ), 190 );
		
		// My Account Followers End Point Title
		add_filter( 'the_title', array( &$this, 'wcfm_followers_my_account_endpoint_title' ) );
		
		// My Account Followers End Point Content
		//add_action( 'woocommerce_account_followers_endpoint', array( &$this, 'wcfm_followers_my_account_endpoint_content' ) );
		add_action( 'woocommerce_account_'.$this->wcfm_myaccount_followings_endpoint.'_endpoint', array( &$this, 'wcfm_followings_my_account_endpoint_content' ) );
		
		// Followers notification on new product submited
		if( apply_filters( 'wcfm_is_allow_followers_new_product_notification', true ) ) {
			add_action( 'wcfm_after_new_product_by_vendor', array( &$this, 'wcfmu_vendors_followers_notify_on_new_product' ), 10, 2 );
		}
		
		// Followers Settings
		//add_action( 'end_wcfm_settings', array( &$this, 'wcfmu_followers_settings' ), 13 );
		//add_action( 'wcfm_settings_update', array( &$this, 'wcfmu_followers_settings_update' ), 13 );
		
		// List Vendor Followers
    add_action( 'wp_ajax_wcfmu_vendors_followers_list', array( &$this, 'wcfmu_vendors_followers_list' ) );
		
		// Update Vendor Followers
    add_action( 'wp_ajax_wcfmu_vendors_followers_update', array( &$this, 'wcfmu_vendors_followers_update' ) );
    
    // Delete Followers
    add_action( 'wp_ajax_delete_wcfm_followers', array( &$this, 'wcfmu_vendors_followers_delete' ) );
    
    // Delete Followings
    add_action( 'wp_ajax_delete_wcfm_followings', array( &$this, 'wcfmu_vendors_followings_delete' ) );
    
    // Followers direct message type
		add_filter( 'wcfm_message_types', array( &$this, 'wcfm_followers_message_types' ), 45 );
		
		//enqueue scripts
		add_action('wp_enqueue_scripts', array(&$this, 'wcfm_followers_scripts'));
		//enqueue styles
		add_action('wp_enqueue_scripts', array(&$this, 'wcfm_followers_styles'));
		
		if( $WCFMu->is_marketplace == 'wcmarketplace' ) {
			add_action( 'before_wcmp_vendor_information', array( &$this, 'before_wcmp_vendor_information' ), 16 );
			//add_action( 'after_sold_by_text_shop_page', array( &$this, 'after_sold_by_text_shop_page'), 15 );
			//add_action( 'woocommerce_after_shop_loop_item', array( &$this, 'template_loop_seller_badges' ), 90 );
			//add_action( 'after_wcmp_singleproductmultivendor_vendor_name', array( &$this, 'wcmp_singleproductmultivendor_table_name' ), 15, 2 );
		} elseif( $WCFMu->is_marketplace == 'wcvendors' ) {
			if( version_compare( WCV_VERSION, '2.0.0', '<' ) ) {
				if ( WC_Vendors::$pv_options->get_option( 'sold_by' ) ) { 
					//add_action( 'woocommerce_after_shop_loop_item', array( &$this, 'template_loop_seller_badges' ), 9 );
				}
			} else {
				if ( get_option('wcvendors_display_label_sold_by_enable') ) { 
					//add_action( 'woocommerce_after_shop_loop_item', array( &$this, 'template_loop_seller_badges' ), 9 );
				}
			}
			//add_filter( 'wcvendors_cart_sold_by', array( &$this, 'after_wcv_cart_sold_by' ), 15, 3 );
			//add_filter( 'wcvendors_cart_sold_by_meta', array( &$this, 'after_wcv_cart_sold_by' ), 15, 3 );
			if( WCFM_Dependencies::wcvpro_plugin_active_check() ) {
				add_action( 'wcv_after_vendor_store_title', array( &$this, 'after_wcv_pro_store_header' ), 16 );
			} else {
				add_action( 'wcv_after_main_header', array( &$this, 'after_wcv_store_header' ), 16 );
				add_action( 'wcv_after_mini_header', array( &$this, 'after_wcv_store_header' ), 16 );
			}
		} elseif( $WCFMu->is_marketplace == 'wcpvendors' ) {
			//add_filter( 'wcpv_sold_by_link_name', array( &$this, 'wcpv_sold_by_link_name_seller_badges' ), 15, 3 );
		} elseif( $WCFMu->is_marketplace == 'dokan' ) {
			add_action( 'dokan_store_header_info_fields',  array( &$this, 'after_dokan_store_header' ), 16 );
			//add_filter( 'woocommerce_product_tabs', array( &$this, 'dokan_product_tab_seller_badges' ), 9 );
		} elseif( $WCFMu->is_marketplace == 'wcfmmarketplace' ) {
			add_action( 'wcfmmp_store_follow_me',  array( &$this, 'wcfmmp_store_follow_me' ), 16 );
		}
		
	}
	
	/**
   * Followers Query Var
   */
  function wcfm_followers_query_vars( $query_vars ) {
  	$wcfm_modified_endpoints = wcfm_get_option( 'wcfm_endpoints', array() );
  	
		$query_followers_vars = array(
			'wcfm-followers'           => ! empty( $wcfm_modified_endpoints['wcfm-followers'] ) ? $wcfm_modified_endpoints['wcfm-followers'] : 'followers',
			'wcfm-followings'          => ! empty( $wcfm_modified_endpoints['wcfm-followings'] ) ? $wcfm_modified_endpoints['wcfm-followings'] : 'followings'
		);
		
		$query_vars = array_merge( $query_vars, $query_followers_vars );
		
		return $query_vars;
  }
  
  /**
   * Followers End Point Title
   */
  function wcfm_followers_endpoint_title( $title, $endpoint ) {
  	global $wp;
  	switch ( $endpoint ) {
  		case 'wcfm-followers' :
				$title = __( 'Followers', 'wc-frontend-manager-ultimate' );
			break;
			case 'wcfm-followings' :
				$title = __( 'Followings', 'wc-frontend-manager-ultimate' );
			break;
  	}
  	
  	return $title;
  }
  
  /**
   * Followers Endpoint Intialize
   */
  function wcfm_followers_init() {
  	global $WCFM_Query;
	
		// Intialize WCFM End points
		$WCFM_Query->init_query_vars();
		$WCFM_Query->add_endpoints();
		
		add_rewrite_endpoint( 'followers', EP_ROOT | EP_PAGES );
		add_rewrite_endpoint( $this->wcfm_myaccount_followings_endpoint, EP_ROOT | EP_PAGES );
		
		if( !get_option( 'wcfm_updated_end_point_followers' ) ) {
			// Flush rules after endpoint update
			flush_rewrite_rules();
			update_option( 'wcfm_updated_end_point_followers', 1 );
		}
  }
  
  /**
	 * Followers Endpoiint Edit
	 */
	function followers_wcfm_endpoints_slug( $endpoints ) {
		
		$followers_endpoints = array(
													'wcfm-followers'          => 'followers',
													'wcfm-followings'   			=> 'followings',
													);
		
		$endpoints = array_merge( $endpoints, $followers_endpoints );
		
		return $endpoints;
	}
	
	/**
   * WCFM Followers Menu
   */
  function wcfm_followers_menus( $menus ) {
  	global $WCFM;
  		
		$menus = array_slice($menus, 0, 3, true) +
												array( 'wcfm-followers' => array( 'label'  => __( 'Followers', 'wc-frontend-manager-ultimate' ),
																										 'url'        => wcfm_followers_url(),
																										 'icon'       => 'child',
																										 'priority'   => 69.4
																										) )	 +
													array_slice($menus, 3, count($menus) - 3, true) ;
  	return $menus;
  }  
  
  /**
   * Followers Scripts
   */
  public function load_scripts( $end_point ) {
	  global $WCFM, $WCFMu;
    
	  switch( $end_point ) {
	  	case 'wcfm-followers':
      	$WCFM->library->load_datatable_lib();
      	$WCFM->library->load_select2_lib();
      	wp_enqueue_script( 'wcfmu_followers_js', $WCFMu->library->js_lib_url . 'followers/wcfmu-script-followers.js', array('jquery'), $WCFMu->version, true );
      	
      	$wcfm_screen_manager_data = array();
      	if( !apply_filters( 'wcfm_allow_view_customer_email', true ) ) {
      		$wcfm_screen_manager_data[1] = 'yes';
      	}
      	if( wcfm_is_vendor() ) {
      		$wcfm_screen_manager_data[2] = 'yes';
      	}
	    	$wcfm_screen_manager_data = apply_filters( 'wcfm_followers_screen_manage', $wcfm_screen_manager_data );
	    	wp_localize_script( 'wcfmu_followers_js', 'wcfm_followers_screen_manage', $wcfm_screen_manager_data );
      break;
      
      case 'wcfm-followings':
      	wp_enqueue_script( 'wcfmu_followings_js', $WCFMu->library->js_lib_url . 'followers/wcfmu-script-followings.js', array('jquery'), $WCFMu->version, true );
      	// Localized Script
        //$wcfm_messages = get_wcfm_followers_manage_messages();
			  //wp_localize_script( 'wcfmu_followers_manage_js', 'wcfm_followers_manage_messages', $wcfm_messages );
      break;
	  }
	}
	
	/**
   * Followers Styles
   */
	public function load_styles( $end_point ) {
	  global $WCFM, $WCFMu;
		
	  switch( $end_point ) {
	  	case 'wcfm-followers':
		    wp_enqueue_style( 'wcfmu_followers_css',  $WCFMu->library->css_lib_url . 'followers/wcfmu-style-followers.css', array(), $WCFMu->version );
		  break;
		  
		  case 'wcfm-followings':
		  	wp_enqueue_style( 'wcfmu_followings_css',  $WCFMu->library->css_lib_url . 'followers/wcfmu-style-followings.css', array(), $WCFMu->version );
		  break;
	  }
	}
	
	/**
   * Followers Views
   */
  public function load_views( $end_point ) {
	  global $WCFM, $WCFMu;
	  
	  switch( $end_point ) {
	  	case 'wcfm-followers':
        $WCFMu->template->get_template( 'followers/wcfmu-view-followers.php' );
      break;
      
      case 'wcfm-followings':
        $WCFMu->template->get_template( 'followers/wcfmu-view-followings.php' );
      break;
	  }
	}
	
	/**
   * Followers Ajax Controllers
   */
  public function ajax_controller() {
  	global $WCFM, $WCFMu;
  	
  	$controllers_path = $WCFMu->plugin_path . 'controllers/followers/';
  	
  	$controller = '';
  	if( isset( $_POST['controller'] ) ) {
  		$controller = $_POST['controller'];
  		
  		switch( $controller ) {
  			case 'wcfm-followers':
					include_once( $controllers_path . 'wcfmu-controller-followers.php' );
					new WCFMu_Followers_Controller();
				break;
				
				case 'wcfm-followings':
					include_once( $controllers_path . 'wcfmu-controller-followings.php' );
					new WCFMu_Followings_Controller();
				break;
  		}
  	}
  }
  
  function wcfm_followers_my_account_endpoints() {
  	add_rewrite_endpoint( 'followers', EP_ROOT | EP_PAGES );
		add_rewrite_endpoint( $this->wcfm_myaccount_followings_endpoint, EP_ROOT | EP_PAGES );
	}
	
	function wcfm_followers_my_account_query_vars( $vars ) {
		$vars[] = 'followers';
		$vars[] = $this->wcfm_myaccount_followings_endpoint;
		return $vars;
	}
	
	function wcfm_followers_my_account_flush_rewrite_rules() {
		add_rewrite_endpoint( 'followers', EP_ROOT | EP_PAGES );
		add_rewrite_endpoint( $this->wcfm_myaccount_followings_endpoint, EP_ROOT | EP_PAGES );
		flush_rewrite_rules();
	}
	
	function wcfm_followers_my_account_menu_items( $items ) {
		// Remove the logout menu item.
		//$logout = $items['customer-logout'];
		//unset( $items['customer-logout'] );
		
		// Insert your custom endpoint.
		$items = array_slice($items, 0, count($items) - 2, true) +
																	array(
																				$this->wcfm_myaccount_followings_endpoint => __( 'Followings', 'wc-frontend-manager-ultimate' )
																				) +
																	array_slice($items, count($items) - 2, count($items) - 1, true) ;
		//$items['followings'] = __( 'Followings', 'wc-frontend-manager-ultimate' );
	
		// Insert back the logout item.
		//$items['customer-logout'] = __( 'Logout', 'wc-frontend-manager-ultimate' );
	
		return $items;
	}
	
	function wcfm_followers_my_account_endpoint_title( $title ) {
		global $wp_query;
	
		$is_endpoint = isset( $wp_query->query_vars[$this->wcfm_myaccount_followings_endpoint] );
	
		if ( $is_endpoint && ! is_admin() && is_main_query() && in_the_loop() && is_account_page() ) {
			// New page title.
			$title = __( 'Followings', 'wc-frontend-manager-ultimate' );
	
			remove_filter( 'the_title', array( $this, 'wcfm_followers_my_account_endpoint_title' ) );
		}
	
		return $title;
	}
	
	function wcfm_followings_my_account_endpoint_content() {
		global $WCFM, $WCFMu, $wpdb;
		include_once( $WCFMu->library->views_path . 'followers/wcfmu-view-my-account-followings.php' );
	}
	
	function wcfmu_vendors_followers_notify_on_new_product( $product_id, $vendor_id ) {
		global $WCFM, $WCFMu, $wpdb;
		
		$is_wcfm_followers_notified = get_post_meta( $product_id, '_wcfm_followers_notified', true );
		
		if( $is_wcfm_followers_notified ) return;
		
		$sql  = "SELECT * FROM {$wpdb->prefix}wcfm_following_followers WHERE `user_id` = " . $vendor_id;
		$vendor_followers = $wpdb->get_results( $sql );
		if( !empty( $vendor_followers ) ) {
			foreach( $vendor_followers as $vendor_follower ) {
				if( $vendor_follower->follower_email ) {
					if( !defined( 'DOING_WCFM_EMAIL' ) ) 
						define( 'DOING_WCFM_EMAIL', true );
			
					$notificaton_mail_subject = "{site_name}: " . __( "New Product", "wc-frontend-manager-ultimate" ) . " - {product_title}";
					$notification_mail_body =  '<br/>' . __( 'Hi', 'wc-frontend-manager' ) . ' {follower_name}' .
																		 ',<br/><br/>' . 
																		 __( 'A new product submitted by ', 'wc-frontend-manager-ultimate' ) . ' <b>{vendor_store}</b>' . 
																		 '<br/><br/>' .
																		 sprintf( __( 'Check the product %s{product_title}%s.', 'wc-frontend-manager-ultimate' ), '<a href="{product_url}">', '</a>' ) .
																		 '<br /><br/>' . __( 'Thank You', 'wc-frontend-manager' ) .
																     '<br/><br/>';
															 
					$subject = str_replace( '{site_name}', get_bloginfo( 'name' ), $notificaton_mail_subject );
					$subject = apply_filters( 'wcfm_email_subject_wrapper', $subject );
					$subject = str_replace( '{product_title}', get_the_title( $product_id ), $subject );
					$message = str_replace( '{product_title}', get_the_title( $product_id ), $notification_mail_body  );
					$message = str_replace( '{follower_name}', $vendor_follower->follower_name, $message );
					$message = str_replace( '{vendor_store}', $WCFM->wcfm_vendor_support->wcfm_get_vendor_store_by_vendor( $vendor_id ), $message );
					$message = str_replace( '{product_url}', get_permalink( $product_id ), $message );
					$message = apply_filters( 'wcfm_email_content_wrapper', $message, __( "New Product", "wc-frontend-manager-ultimate" ) );
					
					wp_mail( $vendor_follower->follower_email, $subject, $message );
				}
			}
		}
		
		update_post_meta( $product_id, '_wcfm_followers_notified', 'yes' );
	}
	
	function wcfmu_followers_settings( $wcfm_options ) {
		global $WCFM, $WCFMu;
		
		$followers_icon = isset( $this->wcfm_followers_options['followers_icon'] ) ? $this->wcfm_followers_options['followers_icon'] : '';
		if( !$followers_icon ) $followers_icon = $WCFMu->plugin_url . 'assets/images/verification_badge.png';
		
		?>
		<!-- collapsible -->
		<div class="page_collapsible" id="wcfm_settings_form_followers_head">
			<label class="wcfmfa fa-user-plus"></label>
			<?php _e('Following / Followers', 'wc-frontend-manager-ultimate'); ?><span></span>
		</div>
		<div class="wcfm-container">
			<div id="wcfm_settings_form_followers_expander" class="wcfm-content">
				<?php
					$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfmu_settings_fields_followers_general', array(
																																																"followers_icon" => array('label' => __('Followers Icon', 'wc-frontend-manager-ultimate'), 'name' => 'wcfm_followers_options[followers_icon]', 'type' => 'upload', 'class' => 'wcfm_ele', 'prwidth' => 64, 'label_class' => 'wcfm_title wcfm_ele', 'hints' => __( 'Upload badge image 32x32 size for best view.', '' ), 'value' => $followers_icon ),
																																																) ) );
			  ?>
			</div>
		</div>
		<div class="wcfm_clearfix"></div>
		<!-- end collapsible -->
		
		<?php
		
	}
	
	function wcfmu_followers_settings_update( $wcfm_settings_form ) {
		global $WCFM, $WCFMu, $_POST;
		
		if( isset( $wcfm_settings_form['wcfm_followers_options'] ) ) {
			$wcfm_followers_options = $wcfm_settings_form['wcfm_followers_options'];
			update_option( 'wcfm_followers_options',  $wcfm_followers_options );
		}
	}
	
	function wcfmu_vendors_followers_list() {
		global $WCFM, $WCFMu, $wpdb, $_POST;
		
		$vendor_id = absint( $_POST['vendor_id'] );
		
		$followers_arr = get_user_meta( $vendor_id, '_wcfm_followers_list', true );
		if( $followers_arr && is_array( $followers_arr ) ) {
			echo '<table class="wcfm_vendor_followers"><tbody>';
			$tr_started = false;
			foreach( $followers_arr as $findex => $follower ) {
				if( ( $findex == 0 ) || ( $findex % 2 == 0 ) ) {
					echo '<tr>';
					$tr_started = true;
				}
				echo '<td width="50%">';
				echo '<div class="wcfm_vendor_follower">';
				$finfo = get_userdata( $follower );
				$wp_user_avatar_id = get_user_meta( $follower, 'wp_user_avatar', true );
				$wp_user_avatar = wp_get_attachment_url( $wp_user_avatar_id );
				if ( !$wp_user_avatar ) {
					$wp_user_avatar = $WCFM->plugin_url . 'assets/images/user.png';
				}
				echo '<img src="' . $wp_user_avatar . '" />';
				echo '<br /><strong>' . $finfo->display_name . '</strong>';
				echo '</div>';
				echo '</td>';
				if( ( $findex != 0 ) && ( $findex % 2 != 0 ) ) {
					echo '</tr>';
					$tr_started = false;
				}
			}
			if( $tr_started ) echo '</tr>';
			echo '</tbody></table>';
		}
		
		die;
	}
	
	function wcfmu_vendors_followers_update() {
		global $WCFM, $WCFMu, $wpdb, $_POST;
		
		$user_id   = absint( $_POST['user_id'] );
		$vendor_id = absint( $_POST['vendor_id'] );
		$count     = absint( $_POST['count'] );
				      
		if( $user_id && $vendor_id ) {
			if( !apply_filters( 'wcfm_validate_user_can_follow_vendor', true, $user_id, $vendor_id ) ) {
				echo 'fail';
				die;
			}
			
			$user_data   = get_userdata( $user_id );
			$vendor_data = get_userdata( $vendor_id );
			
			// Update Vendor Followers List
			$followers_arr = get_user_meta( $vendor_id, '_wcfm_followers_list', true );
			if( $followers_arr && is_array( $followers_arr ) ) {
				$followers_arr[] = $user_id;
			} else {
				$followers_arr = array($user_id);
			}
			update_user_meta( $vendor_id, '_wcfm_followers_list', $followers_arr );
			
			// Update User Following List
			$user_following_arr = get_user_meta( $user_id, '_wcfm_following_list', true );
			if( $user_following_arr && is_array( $user_following_arr ) ) {
				$user_following_arr[] = $vendor_id;
			} else {
				$user_following_arr = array( $vendor_id );
			}
			update_user_meta( $user_id, '_wcfm_following_list', $user_following_arr );
			
			$vendor_display_name = esc_sql( $vendor_data->display_name );
			$user_display_name   = esc_sql( $user_data->display_name );
			
			// Update WCfM Followers Table
			$wcfm_add_follower    = "INSERT into {$wpdb->prefix}wcfm_following_followers 
																( `user_id`, `user_name`, `user_email`, `follower_id`, `follower_name`, `follower_email`, `notify` )
																VALUES
																( {$vendor_id}, '{$vendor_display_name}', '{$vendor_data->user_email}', {$user_id}, '{$user_display_name}', '{$user_data->user_email}', 1 )";
			$wpdb->query($wcfm_add_follower);
			
			// Direct message
			$wcfm_messages = apply_filters( 'wcfm_vendor_new_follower_message', __( 'Congrats! Recently you got a new follower.', 'wc-frontend-manager-ultimate' ), $user_id, $vendor_id );
			$WCFM->wcfm_notification->wcfm_send_direct_message( -1, $vendor_id, 1, 0, $wcfm_messages, 'new_follower' );
		}
		echo "done";
		die;
	}
	
	function wcfmu_vendors_followers_delete() {
		global $WCFM, $WCFMu, $wpdb, $_POST;
		
		$line_id       = absint( $_POST['lineid'] );
		$vendor_id     = absint( $_POST['userid'] );
		$user_id       = absint( $_POST['followersid'] );
		
		if( $line_id && $vendor_id && $user_id ) {
			
			// Update Vendor Followers List
			$followers_arr = get_user_meta( $vendor_id, '_wcfm_followers_list', true );
			if( $followers_arr && is_array( $followers_arr ) ) {
				if( ( $key = array_search( $user_id, $followers_arr ) ) !== false ) {
					unset( $followers_arr[$key] );
				}
			}
			update_user_meta( $vendor_id, '_wcfm_followers_list', $followers_arr );
			
			// Update User Following List
			$user_following_arr = get_user_meta( $user_id, '_wcfm_following_list', true );
			if( $user_following_arr && is_array( $user_following_arr ) ) {
				if( ( $key = array_search( $vendor_id, $user_following_arr ) ) !== false ) {
					unset( $user_following_arr[$key] );
				}
			}
			update_user_meta( $user_id, '_wcfm_following_list', $user_following_arr );
			
			// Update WCfM Followers Table
			$wcfm_delete_follower    = "DELETE FROM {$wpdb->prefix}wcfm_following_followers WHERE `ID` = {$line_id}"; 
			$wpdb->query($wcfm_delete_follower);
		}
		echo "done";
		die;
	}
	
	function wcfmu_vendors_followings_delete() {
		global $WCFM, $WCFMu, $wpdb, $_POST;
		
		$vendor_id     = absint( $_POST['userid'] );
		$user_id       = absint( $_POST['followersid'] );
		
		if( $vendor_id && $user_id ) {
			
			// Update Vendor Followers List
			$followers_arr = get_user_meta( $vendor_id, '_wcfm_followers_list', true );
			if( $followers_arr && is_array( $followers_arr ) ) {
				if( ( $key = array_search( $user_id, $followers_arr ) ) !== false ) {
					unset( $followers_arr[$key] );
				}
			}
			update_user_meta( $vendor_id, '_wcfm_followers_list', $followers_arr );
			
			// Update User Following List
			$user_following_arr = get_user_meta( $user_id, '_wcfm_following_list', true );
			if( $user_following_arr && is_array( $user_following_arr ) ) {
				if( ( $key = array_search( $vendor_id, $user_following_arr ) ) !== false ) {
					unset( $user_following_arr[$key] );
				}
			}
			update_user_meta( $user_id, '_wcfm_following_list', $user_following_arr );
			
			// Update WCfM Followers Table
			$wcfm_delete_follower    = "DELETE FROM {$wpdb->prefix}wcfm_following_followers WHERE `follower_id` = {$user_id} AND `user_id` = {$vendor_id}"; 
			$wpdb->query($wcfm_delete_follower);
		}
		echo "done";
		die;
	}
	
	function wcfm_followers_message_types( $message_types ) {
  	$message_types['new_follower']         = __( 'New Follower', 'wc-frontend-manager-ultimate' );
		return $message_types;
	}
	
	/**
	 * WCFM Followers JS
	 */
	function wcfm_followers_scripts() {
 		global $WCFM, $WCFMu, $wp, $WCFM_Query;
 		
 		if( !is_user_logged_in() ) {
 			$WCFM->library->load_wcfm_login_popup_lib();
 		}
 		
 		if( is_account_page() ) {
 			if( is_user_logged_in() ) {
				if( isset( $wp->query_vars[$this->wcfm_myaccount_followings_endpoint] ) ) {
					$WCFM->library->load_blockui_lib();
					wp_enqueue_script( 'wcfmu_my_account_followings_js', $WCFMu->library->js_lib_url . 'followers/wcfmu-script-my-account-followings.js', array('jquery'), $WCFMu->version, true );
					$wcfm_dashboard_messages = get_wcfm_dashboard_messages();
					wp_localize_script( 'wcfmu_my_account_followings_js', 'wcfm_dashboard_messages', $wcfm_dashboard_messages );
				}
			}
 		}
 	}
 	
 	/**
 	 * WCFM Followers CSS
 	 */
 	function wcfm_followers_styles() {
 		global $WCFM, $WCFMu, $wp, $WCFM_Query;
 		
 		if( is_account_page() ) {
 			if( is_user_logged_in() ) {
 				//if( isset( $wp->query_vars[$this->wcfm_myaccount_followings_endpoint] ) ) {
 					wp_enqueue_style( 'wcfmu_my_account_support_manage_css',  $WCFMu->library->css_lib_url . 'followers/wcfmu-style-my-account-followings.css', array(), $WCFMu->version );
 				//}
 			}
 		}
 	}
	
	function show_wcfm_vendor_followers( $vendor_id ) {
		global $WCFM, $WCFMu, $wpdb;
		
		$followers = 0;
		$following = 0;
		
		$followers_arr = get_user_meta( $vendor_id, '_wcfm_followers_list', true );
		$following_arr = get_user_meta( $vendor_id, '_wcfm_following_list', true );
		
		if( $followers_arr && is_array( $followers_arr ) ) {
			$followers = count( $followers_arr );
		}
		
		if( $following_arr && is_array( $following_arr ) ) {
			$following = count( $following_arr );
		}
		
		$user_id = 0;
		if( is_user_logged_in() ) {
		  $user_id = get_current_user_id();
		  $is_following = false;
		  $user_following_arr = get_user_meta( $user_id, '_wcfm_following_list', true );
		  if( $user_id == $vendor_id ) $is_following = true;
		  if( $user_following_arr && is_array( $user_following_arr ) && in_array( $vendor_id, $user_following_arr ) ) {
				$is_following = true;
			}
		}
		?>
		<div id="wcfm_followers">
		  <span class="wcfm_followers_count"><?php echo $followers; ?></span>&nbsp;&nbsp;
		  <span class="wcfm_followers_label" style="cursor: pointer;" data-vendor_id="<?php echo $vendor_id; ?>"><?php _e( 'Followers', 'wc-frontend-manager-ultimate' ); ?></span>
		  <?php if( $user_id && !$is_following ) { ?>
		  	<a id="wcfm_follow_now" href="#" data-count="<?php echo $followers; ?>" data-vendor_id="<?php echo $vendor_id; ?>" data-user_id="<?php echo $user_id; ?>">
					&nbsp;&nbsp;|&nbsp;&nbsp;
					<span class="wcfm_follow_icon wcfmfa fa-user-plus text_tip" data-tip="<?php _e( 'Follow Now', 'wc-frontend-manager-ultimate' ); ?>"></span>
				</a>
				<script>
				  jQuery(document).ready(function($) {
				    $('#wcfm_follow_now').click(function(event) {
				      event.preventDefault();
				      
				      $user_id   = $(this).data('user_id');
				      $vendor_id = $(this).data('vendor_id');
				      $count     = $(this).data('count');
				      
				      $('#wcfm_followers').block({
								message: null,
								overlayCSS: {
									background: '#fff',
									opacity: 0.6
								}
							});
				      var data = {
								action    : 'wcfmu_vendors_followers_update',
								user_id   : $user_id,
								vendor_id : $vendor_id,
								count     : $count
							}	
							$.post(wcfm_params.ajax_url, data, function(response) {
								if(response) {
									$count = $count + 1;
									$('.wcfm_followers_count').text( $count );
									$('#wcfm_follow_now').hide();
									$('#wcfm_followers').unblock();
								}
							});
				      
				      return false;
				    });
				  });
				</script>
			<?php } ?>
			<?php if( apply_filters( 'wcfm_is_allow_followers_list', true ) ) { ?>
				<script>
					jQuery(document).ready(function($) {
						$('.wcfm_followers_label').click(function() {
							$vendor_id = $(this).data('vendor_id');
							var data = {
								action    : 'wcfmu_vendors_followers_list',
								vendor_id : $vendor_id
							}	
							$.post(wcfm_params.ajax_url, data, function(response) {
								if(response) {
									jQuery.colorbox( { html: response, innerWidth: '525', innerHeight: '400',
										onComplete:function() {
											
										}
									});
								}
							});
						});
					});
				</script>
				<style>
				 .wcfm_vendor_follower {
						border: 1px solid #ccc;
						border-radius: 5px;
						-moz-border-radius: 5px;
						padding: 20px;
						text-align: center;
				 }
				 
				 .wcfm_vendor_follower img {
					 height: 175px;
					 display:inline;
				 }
				</style>
			<?php } ?>
		</div>
		<?php
	}
	
	function before_wcmp_vendor_information( $vendor_id ) {
		global $WCFM, $WCFMu;
		$this->show_wcfm_vendor_followers( $vendor_id );
	}
	
	function after_wcv_pro_store_header() {
		global $WCFM, $WCFMu;
		
		$vendor_id = 0;
		if ( WCV_Vendors::is_vendor_page() ) { 
			$vendor_shop 		= urldecode( get_query_var( 'vendor_shop' ) );
			$vendor_id   		= WCV_Vendors::get_vendor_id( $vendor_shop ); 
		} else {
			global $product; 
			$post = get_post( $product->get_id() ); 
			if ( WCV_Vendors::is_vendor_product_page( $post->post_author ) )  { 
				$vendor_id   		= $post->post_author; 
			}
		}
		
		$this->show_wcfm_vendor_followers( $vendor_id, true );
	}
	
	function after_wcv_store_header( $vendor_id ) {
		global $WCFM, $WCFMu;
		$this->show_wcfm_vendor_followers( $vendor_id, true );
	}
	
	function after_wcv_cart_sold_by( $sold_by_label, $product_id, $vendor_id ) {
		global $WCFM, $WCFMu;
		if( apply_filters( 'wcfm_is_allow_followers_in_loop', true ) ) {
			$this->show_wcfm_vendor_followers( $vendor_id );
		}
		return $sold_by_label;
	}
	
	function after_dokan_store_header( $vendor_id ) {
		global $WCFM, $WCFMu;
		echo '<li class="dokan-store-followers">';
		$this->show_wcfm_vendor_followers( $vendor_id );
		echo '</li>';
	}
	
	function wcfmmp_store_follow_me( $vendor_id ) {
		global $WCFM, $WCFMu, $WCFMmp;
		
		$followers = 0;
		$followers_arr = get_user_meta( $vendor_id, '_wcfm_followers_list', true );
		if( $followers_arr && is_array( $followers_arr ) ) {
			$followers = count( $followers_arr );
		}

		$user_id = 0;
		$is_following = false;
		if( is_user_logged_in() ) {
			$user_id = get_current_user_id();
			$user_following_arr = get_user_meta( $user_id, '_wcfm_following_list', true );
			if( $user_id == $vendor_id ) $is_following = true;
			if( $user_following_arr && is_array( $user_following_arr ) && in_array( $vendor_id, $user_following_arr ) ) {
				$is_following = true;
			}
			if( $user_id && !$is_following ) {
				?>
				<div class="lft bd_icon_box"><a id="wcfm_follow_now" title="<?php _e( 'Click to Follow', 'wc-frontend-manager-ultimate' ); ?>" data-count="<?php echo $followers; ?>" data-vendor_id="<?php echo $vendor_id; ?>" data-user_id="<?php echo $user_id; ?>" href="#" class="follow"><i class="wcfmfa fa-user-plus"></i>&nbsp;<span><?php echo apply_filters( 'wcfm_store_follow_button_label', __( 'Follow', 'wc-frontend-manager-ultimate' ) ); ?></span></a></div>
				<script>
					jQuery(document).ready(function($) {
						$('#wcfm_follow_now').click(function(event) {
							event.preventDefault();
							
							$user_id   = $(this).data('user_id');
							$vendor_id = $(this).data('vendor_id');
							$count     = $(this).data('count');
							
							$('#wcfm_store_header').block({
								message: null,
								overlayCSS: {
									background: '#fff',
									opacity: 0.6
								}
							});
							var data = {
								action    : 'wcfmu_vendors_followers_update',
								user_id   : $user_id,
								vendor_id : $vendor_id,
								count     : $count
							}	
							$.post(wcfm_params.ajax_url, data, function(response) {
								if(response) {
									$count = $count + 1;
									$('.wcfm_followers_count').text( $count );
									$('#wcfm_follow_now').hide();
									$('#wcfm_store_header').unblock();
									window.location = window.location.href;
								}
							});
							
							return false;
						});
					});
				</script>
				<?php 
			} else {
				?>
				<div class="lft bd_icon_box"><a id="wcfm_follow_delete" title="<?php _e( 'Click to Un-follow', 'wc-frontend-manager-ultimate' ); ?>" class="follow wcfm_followings_delete" data-followersid="<?php echo $user_id; ?>" data-userid="<?php echo $vendor_id; ?>" href="#"><i class="wcfmfa fa-user-plus"></i>&nbsp;<span><?php _e( 'Following', 'wc-frontend-manager-ultimate' ); ?></span></a></div>
				<?php
				wp_enqueue_script( 'wcfmu_my_account_followings_js', $WCFMu->library->js_lib_url . 'followers/wcfmu-script-my-account-followings.js', array('jquery'), $WCFMu->version, true );
				$wcfm_dashboard_messages = get_wcfm_dashboard_messages();
				wp_localize_script( 'wcfmu_my_account_followings_js', 'wcfm_dashboard_messages', $wcfm_dashboard_messages );
			}
		} else {
			?>
			<div class="lft bd_icon_box"><a id="wcfm_follow_now" title="<?php _e( 'Click to Follow', 'wc-frontend-manager-ultimate' ); ?>" href="#" class="follow wcfm_login_popup"><i class="wcfmfa fa-user-plus"></i>&nbsp;<span><?php echo apply_filters( 'wcfm_store_follow_button_label', __( 'Follow', 'wc-frontend-manager-ultimate' ) ); ?></span></a></div>
			<?php
		}
	}
	
}