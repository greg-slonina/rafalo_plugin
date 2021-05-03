<?php
/**
 * WCFM plugin core
 *
 * Chatbox board core
 *
 * @author 		WC Lovers
 * @package 	wcfmu/core
 * @version   5.1.5
 */
 
class WCFMu_Vendor_Chatbox {
	
	public $wcfm_myaccount_view_chatbox_endpoint = 'chatbox';
	
	public $app_lib;
	
	/**
	 * API endpoint url
	 *
	 * @var string
	 */
	protected $api_endpoint;

	/**
	 * Hold the app_id
	 *
	 * @var string
	 */
	public $app_id;

	/**
	 * Hold the app_secret
	 *
	 * @var string
	 */
	public $app_secret;
	
	Public $fbc_user;
		

	public function __construct() {
		global $WCFM, $WCFMu, $WCFMmp;
		
		if ( !function_exists( 'wcfm_is_store_page' ) ) return;
		
		$wcfm_chatbox_setting = get_option( 'wcfm_chatbox_setting', array() );
		
		$chat_lib = !empty( $wcfm_chatbox_setting['lib'] ) ? $wcfm_chatbox_setting['lib'] : '';
		
		$fbc_app_id = !empty( $wcfm_chatbox_setting['fbc_app_id'] ) ? $wcfm_chatbox_setting['fbc_app_id'] : '';
		$fbc_secret = !empty( $wcfm_chatbox_setting['fbc_secret'] ) ? $wcfm_chatbox_setting['fbc_secret'] : '';
		
		$app_id = !empty( $wcfm_chatbox_setting['app_id'] ) ? $wcfm_chatbox_setting['app_id'] : '';
		$secret = !empty( $wcfm_chatbox_setting['secret'] ) ? $wcfm_chatbox_setting['secret'] : '';
		$label  = !empty( $wcfm_chatbox_setting['label'] ) ? $wcfm_chatbox_setting['label'] : __( 'Chat Now', 'wc-frontend-manager-ultimate' );
		
		if( !$chat_lib && $app_id ) {
			$chat_lib = 'talkjs';
		}
		
		$this->app_lib        = $chat_lib;
		
		if( $chat_lib == 'firebase' ) {
			$this->app_id       = $fbc_app_id;
			$this->app_secret   = $fbc_secret;
			
			require_once( $WCFMu->plugin_path . 'includes/libs/firebase/firebase-token-generator.php' );
			
			add_action( 'init', array( $this, 'fbc_user_init' ), 25 );
		} else {
			$this->app_id       = $app_id;
			$this->app_secret   = $secret;
		}
		
		if( empty( $this->app_id ) || empty( $this->app_secret ) ) {
			$this->app_lib = '';
			$this->app_id = '';
			$this->app_secret = '';
		}
		
		$this->api_endpoint = 'https://api.talkjs.com/';
		
		$wcfm_myac_modified_endpoints = wcfm_get_option( 'wcfm_myac_endpoints', array() );
		$this->wcfm_myaccount_chatbox_endpoint = ! empty( $wcfm_myac_modified_endpoints['chatbox'] ) ? $wcfm_myac_modified_endpoints['chatbox'] : 'chatbox';
		
		add_filter( 'wcfm_query_vars', array( &$this, 'wcfm_chatbox_query_vars' ), 20 );
		add_filter( 'wcfm_endpoint_title', array( &$this, 'wcfm_chatbox_endpoint_title' ), 20, 2 );
		add_action( 'init', array( &$this, 'wcfm_chatbox_init' ), 20 );
		
		// Chatbox Endpoint Edit
		add_filter( 'wcfm_endpoints_slug', array( $this, 'chatbox_wcfm_endpoints_slug' ) );
		
		if( !empty( $this->app_id ) && !empty( $this->app_secret ) ) {
		
			// Chatbox menu on WCfM dashboard
			if( apply_filters( 'wcfm_is_allow_chatbox', true ) ) {
				add_filter( 'wcfm_menus', array( &$this, 'wcfm_chatbox_menus' ), 30 );
			}
		
			// Chat Now Shortcode
			add_shortcode( 'wcfm_chat_now', array(&$this, 'wcfm_chatnow_shortcode') );
			
			// Single Product page chat now button
			if( apply_filters( 'wcfm_is_pref_enquiry', true ) && apply_filters( 'wcfm_is_pref_enquiry_button', true ) ) {
				add_action( 'wcfm_after_product_catalog_enquiry_button',	array( &$this, 'wcfm_chatbox_button' ), 35 );
			} else {
				add_action( 'woocommerce_single_product_summary',	array( &$this, 'wcfm_chatbox_button' ), 35 );
			}
			
			// WCFM Marketplace Store chat noe button
			add_action( 'wcfmmp_store_before_enquiry',	array( &$this, 'wcfm_store_chatbox_button' ), 50 );
			
			// Support Load Scripts
			add_action( 'wcfm_load_scripts', array( &$this, 'load_scripts' ), 30 );
			add_action( 'after_wcfm_load_scripts', array( &$this, 'load_scripts' ), 30 );
			
			// Support Load Styles
			add_action( 'wcfm_load_styles', array( &$this, 'load_styles' ), 30 );
			add_action( 'after_wcfm_load_styles', array( &$this, 'load_styles' ), 30 );
		
			// Chatbox Load views
			add_action( 'wcfm_load_views', array( &$this, 'load_views' ), 30 );
			
			// Chatbox Ajax Controllers
			add_action( 'after_wcfm_ajax_controller', array( &$this, 'wcfm_chatbox_ajax_controller' ), 30 );
			
			// Generate Chat Conversation Html
			add_action('wp_ajax_wcfmu_show_conversation_html', array( &$this, 'wcfmu_show_conversation_html' ) );
		
			if( $chat_lib == 'talkjs' ) {
				// My Account Chatbox End Point
				add_action( 'init', array( &$this, 'wcfm_chatbox_my_account_endpoints' ) );
				
				// My Account Chatbox Query Vars
				add_filter( 'query_vars', array( &$this, 'wcfm_chatbox_my_account_query_vars' ), 0 );
				
				// My Account Chatbox Rule Flush
				register_activation_hook( $WCFMu->file, array( &$this,'wcfm_chatbox_my_account_flush_rewrite_rules' ) );
				register_deactivation_hook( $WCFMu->file, array( &$this, 'wcfm_chatbox_my_account_flush_rewrite_rules' ) );
				
				// My Account Chatbox Menu
				add_filter( 'woocommerce_account_menu_items', array( &$this, 'wcfm_chatbox_my_account_menu_items' ), 195 );
				
				// My Account Chatbox End Point Title
				add_filter( 'the_title', array( &$this, 'wcfm_chatbox_my_account_endpoint_title' ) );
				
				// My Account Chatbox End Point Content
				add_action( 'woocommerce_account_'.$this->wcfm_myaccount_chatbox_endpoint.'_endpoint', array( &$this, 'wcfm_chatbox_my_account_endpoint_content' ) );
			
			} elseif( $chat_lib == 'firebase' ) {
				add_action( 'wp_enqueue_scripts', array( $this, 'firebase_chatbox_enqueue_scripts' ), 100 );
				add_action( 'wp_footer', array( $this, 'add_firebase_chatbox' ) );
			
				add_action( 'wp_ajax_fbc_ajax_callback', array(&$this, 'fbc_ajax_callback' ) );
				add_action( 'wp_ajax_nopriv_fbc_ajax_callback', array(&$this, 'fbc_ajax_callback' ) );
				
				// Chats Offline Message Delete
				add_action( 'wp_ajax_wcfm_chats_offline_delete', array( &$this, 'wcfm_chats_offline_delete' ) );
				
				// Chats History Conversation Delete
				add_action( 'wp_ajax_wcfm_chats_history_delete', array( &$this, 'wcfm_chats_history_delete' ) );
			}
			
			// Enqueue scripts
			add_action( 'wp_head', array(&$this, 'wcfm_chatbox_scripts'), 999 );
		}
		
		// WCFM Live Chat Setting
		add_action( 'end_wcfm_settings_form_menu_manager', array( &$this, 'wcfm_chatbox_setting' ), 12 );
		
		// WCFM Live Chat Setting Save
		add_action( 'wcfm_settings_update', array( &$this, 'wcfm_chatbox_setting_save' ), 50 );
	}
	
	/**
   * Chatbox Query Var
   */
  function wcfm_chatbox_query_vars( $query_vars ) {
  	$wcfm_modified_endpoints = wcfm_get_option( 'wcfm_endpoints', array() );
  	
		$query_chatbox_vars = array(
			'wcfm-chatbox'                 => ! empty( $wcfm_modified_endpoints['wcfm-chatbox'] ) ? $wcfm_modified_endpoints['wcfm-chatbox'] : 'chatbox',
			'wcfm-chats-offline'           => ! empty( $wcfm_modified_endpoints['wcfm-chats-offline'] ) ? $wcfm_modified_endpoints['wcfm-chatbox'] : 'chats-offline',
			'wcfm-chats-history'           => ! empty( $wcfm_modified_endpoints['wcfm-chats-history'] ) ? $wcfm_modified_endpoints['wcfm-chats-history'] : 'chats-history',
		);
		
		$query_vars = array_merge( $query_vars, $query_chatbox_vars );
		
		return $query_vars;
  }
  
  /**
   * Chatbox End Point Title
   */
  function wcfm_chatbox_endpoint_title( $title, $endpoint ) {
  	global $wp;
  	switch ( $endpoint ) {
  		case 'wcfm-chatbox' :
				$title = __( 'Chat Box', 'wc-frontend-manager-ultimate' );
			break;
			
			case 'wcfm-chats-offline' :
				$title = __( 'Chats Offline', 'wc-frontend-manager-ultimate' );
			break;
			
			case 'wcfm-chats-history' :
				$title = __( 'Chats History', 'wc-frontend-manager-ultimate' );
			break;
  	}
  	
  	return $title;
  }
  
  /**
   * Chatbox Endpoint Intialize
   */
  function wcfm_chatbox_init() {
  	global $WCFM_Query;
	
		// Intialize WCFM End points
		$WCFM_Query->init_query_vars();
		$WCFM_Query->add_endpoints();
		
		add_rewrite_endpoint( $this->wcfm_myaccount_chatbox_endpoint, EP_ROOT | EP_PAGES );
		
		if( !get_option( 'wcfm_updated_end_point_chatbox' ) ) {
			// Flush rules after endpoint update
			flush_rewrite_rules();
			update_option( 'wcfm_updated_end_point_chatbox', 1 );
		}
  }
  
  /**
	 * Chatbox Endpoiint Edit
	 */
	function chatbox_wcfm_endpoints_slug( $endpoints ) {
		
		$chatbox_endpoints = array(
													'wcfm-chatbox'          => 'chatbox',
													'wcfm-chats-offline'    => 'chats-offline',
													'wcfm-chats-history'    => 'chats-history',
													);
		
		$endpoints = array_merge( $endpoints, $chatbox_endpoints );
		
		return $endpoints;
	}
	
	/**
   * WCFM Chatbox Menu
   */
  function wcfm_chatbox_menus( $menus ) {
  	global $WCFM;
  		
  	if( wcfm_is_vendor() || $this->app_lib == 'firebase' ) {
			$menus = array_slice($menus, 0, 3, true) +
													array( 'wcfm-chatbox' => array( 'label'  => __( 'Chatbox', 'wc-frontend-manager-ultimate' ),
																											 'url'        => wcfm_chatbox_url(),
																											 'icon'       => 'comments',
																											 'menu_for'   => 'vendor',
																											 'priority'   => 69.2
																											) )	 +
														array_slice($menus, 3, count($menus) - 3, true) ;
		}
  	return $menus;
  } 
  
  function wcfm_chatnow_shortcode( $attr ) {
   	 global $WCFM, $WCFMu, $post;
   	 
   	 if( !function_exists( 'wcfmmp_is_store_page' ) ) return;
   	 
   	 ob_start();
   	 $this->wcfm_chatbox_button();
   	 return ob_get_clean();
   }
  
  /**
   * Chat now Button on Single Product Page
   *
   * @since 5.1.5
   */
	function wcfm_chatbox_button() {
		global $WCFM, $WCFMu, $product, $post;
		
		if( wcfm_is_vendor() && ( $this->app_lib == 'talkjs' ) ) return;
		
		$store_id = '';
		if( is_product() && $product && method_exists( $product, 'get_id' ) ) {
			$product_id = $product->get_id();
			$store_id   = wcfm_get_vendor_id_by_post( $product_id );
		} elseif (  wcfm_is_store_page() ) {
			$wcfm_store_url = get_option( 'wcfm_store_url', 'store' );
			$store_name = get_query_var( $wcfm_store_url );
			$store_id  = 0;
			if ( !empty( $store_name ) ) {
				$store_user = get_user_by( 'slug', $store_name );
			}
			$store_id   		= $store_user->ID;
		} elseif( is_single() && $post && is_object( $post ) && wcfm_is_vendor( $post->post_author ) ) {
			$store_id = $post->post_author;
		}
		
		if( !$store_id ) return;
		if( !$WCFM->wcfm_vendor_support->wcfm_vendor_has_capability( $store_id, 'chatbox' ) ) return; 
			
		$button_style = '';
		$wcfm_chatbox_setting = get_option( 'wcfm_chatbox_setting', array() );
		
		$background = !empty( $wcfm_chatbox_setting['background'] ) ? $wcfm_chatbox_setting['background'] : '#1C2B36';
		$hover      = !empty( $wcfm_chatbox_setting['hover'] ) ? $wcfm_chatbox_setting['hover'] : '#00798b';
		$text       = !empty( $wcfm_chatbox_setting['text'] ) ? $wcfm_chatbox_setting['text'] : '#b0bec5';
		$text_hover = !empty( $wcfm_chatbox_setting['text_hover'] ) ? $wcfm_chatbox_setting['text_hover'] : '#b0bec5';
		
		
		$button_style .= 'position:relative;padding:5px 10px;background: ' . $background . ';border-bottom-color: ' . $background . ';';
		$button_style .= 'color: ' . $text . ';';
		
		$wcfm_chatnow_label  = !empty( $wcfm_chatbox_setting['label'] ) ? $wcfm_chatbox_setting['label'] : __( 'Chat Now', 'wc-frontend-manager-ultimate' );
		
		$button_class = '';
		if( !is_user_logged_in() && ( apply_filters( 'wcfm_chat_require_login', false ) || ( $this->app_lib == 'talkjs' ) ) ) { $button_class = ' wcfm_login_popup'; }
		$button_class = apply_filters( 'wcfm_chatnow_button_class', $button_class );
		
		?>
		<?php if( !apply_filters( 'wcfm_is_pref_enquiry', true ) || !apply_filters( 'wcfm_is_pref_enquiry_button', true ) || !apply_filters( 'wcfm_is_allow_product_enquiry_bubtton', true ) ) { ?>
		  <div class="wcfm_ele_wrapper wcfm_chat_now_button_wrapper">
			<div class="wcfm-clearfix"></div>
		  <?php } ?>
		  
				<a href="#" onclick="return false;" class="wcfm-chat-now wcfm_chat_now_button <?php echo $button_class; ?>" style="<?php echo $button_style; ?>"><i class="wcfmfa fa-comments"></i>&nbsp;&nbsp;<span class="chat_now_label"><?php _e( $wcfm_chatnow_label, 'wc-frontend-manager' ); ?></span></a>
				<style>a.wcfm-chat-now:hover{background: <?php echo $hover; ?> !important;border-bottom-color: <?php echo $hover; ?> !important;color: <?php echo $text_hover; ?> !important;}</style>
		
		  <?php if( !apply_filters( 'wcfm_is_pref_enquiry', true ) || !apply_filters( 'wcfm_is_pref_enquiry_button', true ) || !apply_filters( 'wcfm_is_allow_product_enquiry_bubtton', true ) ) { ?>
			<div class="wcfm-clearfix"></div>
			</div>
		<?php } ?>
		<?php
		if( $this->app_lib == 'talkjs' ) {
			if ( !$this->wcfm_is_store_vendor_online( $store_id ) ) {
				return;
			}
	
			$this->make_store_vendor_online();
		}
	}
	
	/**
	 * Chat Now Button at Store Page
	 */
	function wcfm_store_chatbox_button( $store_id ) {
		global $WCFM, $WCFMu;
		
		if( wcfm_is_vendor() && ( $this->app_lib == 'talkjs' ) ) return;
		
		if( !$WCFM->wcfm_vendor_support->wcfm_vendor_has_capability( $store_id, 'chatbox' ) ) return;
			
		$button_style = '';
		$wcfm_chatbox_setting = get_option( 'wcfm_chatbox_setting', array() );
		$wcfm_chatnow_label  = !empty( $wcfm_chatbox_setting['label'] ) ? $wcfm_chatbox_setting['label'] : __( 'Chat Now', 'wc-frontend-manager-ultimate' );
		
		$button_class = '';
		if( !is_user_logged_in() && ( apply_filters( 'wcfm_chat_require_login', false ) || ( $this->app_lib == 'talkjs' ) ) ) { $button_class = ' wcfm_login_popup'; }
		$button_class = apply_filters( 'wcfm_chatnow_button_class', $button_class );
		?>
		<div class="lft bd_icon_box"><a onclick="return false;" class="wcfm_store_chatnow wcfm-chat-now <?php echo $button_class; ?>" href="#"><i class="wcfmfa fa-comments" aria-hidden="true"></i><span><?php _e( $wcfm_chatnow_label, 'wc-frontend-manager' ); ?></span></a></div>
		<?php
		if( $this->app_lib == 'talkjs' ) {
			if ( !$this->wcfm_is_store_vendor_online( $store_id ) ) {
				return;
			}
	
			$this->make_store_vendor_online();
		}
	}
	
	public function wcfm_is_store_vendor_online( $store_id ) {
		if ( empty( $store_id ) ) {
			return false;
		}
		
		if ( get_transient( 'wcfm_is_store_vendor_online_'.$store_id ) == 'maybe' ) {
			return false;
		}

		if ( get_transient( 'wcfm_is_store_vendor_online_'.$store_id ) == 'yes' ) {
			return true;
		}

		$url = $this->api_endpoint . 'v1/' . $this->app_id . '/users/' . $store_id . '/sessions' ;

		$response = wp_remote_get( $url, array(
				'sslverify' => false,
				'headers' => array(
						'Authorization' => 'Bearer '. $this->app_secret
				)
		) );

		set_transient( 'wcfm_is_store_vendor_online_'.$store_id, 'maybe', 10 );

		if ( is_wp_error( $response ) ) {
			return new WP_Error( 'chatbox-error', __( 'Something went wrong', 'wc-frontend-manager-ultimate' ) );
		}

		$api_response = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( ! is_array( $api_response ) || empty( $api_response ) ) {
			return false;
		}

		// currentConversationId exists means user is online
		if ( ! array_key_exists( 'currentConversationId', $api_response[0] ) ) {
			return false;
		}

		set_transient( 'wcfm_is_store_vendor_online_'.$store_id, 'yes', 15 );

		return true;
	}
	
	public function make_store_vendor_online() {
		?>
		<script type="text/javascript">
		
			var wcfm_chats = document.querySelectorAll( '.wcfm-chat-now' );
			
			wcfm_chats.forEach(function(wcfm_chat) {
				var span = document.createElement( 'label' );
		
				wcfm_chat.appendChild( span );
				wcfm_chat.style.paddingLeft = '23px';
			});
		
			var wcfm_chat_bts = document.querySelectorAll( '.wcfm-chat-now label' );
			
			wcfm_chat_bts.forEach(function(wcfm_chat_bt) {
				wcfm_chat_bt.style.position = 'absolute';
				wcfm_chat_bt.style.top = '9px';
				wcfm_chat_bt.style.left = '7px';
				wcfm_chat_bt.style.width = '9px';
				wcfm_chat_bt.style.height = '9px';
				wcfm_chat_bt.style.borderRadius = '50%';
				wcfm_chat_bt.style.background = '#79e379';
				wcfm_chat_bt.style.zIndex = '999';
			});
		</script>
		<?php
	}
	
	function fbc_user_init() {
		if ( is_wcfm_page() && apply_filters( 'wcfm_is_allow_chatbox', true ) && ( current_user_can( 'administrator' ) || current_user_can( 'shop_manager' ) || wcfm_is_vendor() ) ) {
			define( 'FBC_OPERATOR', true );
		} else {
			define( 'FBC_GUEST', true );
		}

		$display_name = '';
		$user_email   = '';

		if ( is_user_logged_in() ) {
			$current_user = wp_get_current_user();
			$user_id      = $current_user->ID;
			if( is_wcfm_page() && wcfm_is_vendor() ) {
				$display_name = wcfm_get_vendor_store_name( $current_user->ID );
			} else {
				$display_name = $current_user->display_name;
			}
			$user_email   = $current_user->user_email;

		} else {
			$user_id = isset( $_COOKIE['fbc_user_session'] ) ? $_COOKIE['fbc_user_session'] : '';

			if ( empty( $user_id ) ) {
				$user_id = uniqid( rand(), false );
				@setcookie( 'fbc_user_session', $user_id, time() + ( 3600 * 24 ), COOKIEPATH ? COOKIEPATH : '/', COOKIE_DOMAIN );

			}

		}

		$this->fbc_user = ( object ) array(
			'ID'           => $user_id,
			'display_name' => $display_name,
			'user_email'   => $user_email,
			'user_ip'      => $this->fbc_get_ip_address(),
			'current_page' => $this->fbc_get_current_page_url(),
		);
	}
	
	/**
	 * Firebase Chatbox Frontend Scripts
	 */
	function firebase_chatbox_enqueue_scripts() {
		global $WCFM, $WCFMu, $post;
		
		$store_id  = 0;
		if (  wcfm_is_store_page() ) {
			$wcfm_store_url = get_option( 'wcfm_store_url', 'store' );
			$store_name = apply_filters( 'wcfmmp_store_query_var', get_query_var( $wcfm_store_url ) );
			if ( !empty( $store_name ) ) {
				$store_user = get_user_by( 'slug', $store_name );
			}
			$store_id  = $store_user->ID;
			if( !$store_id || !wcfm_vendor_has_capability( $store_id, 'chatbox' ) ) return;
		}
		
		if( is_product() ) {
			if ( $post && 'product' == $post->post_type ) {
				$store_id = wcfm_get_vendor_id_by_post( $post->ID );
				if( !$store_id || !wcfm_vendor_has_capability( $store_id, 'chatbox' ) ) return;
			}
		}
		
		if( $store_id ) {
			$fbc_lib_url = $WCFMu->plugin_url . 'includes/libs/firebase';
			
			$store_logo = wcfm_get_vendor_store_logo_by_vendor( $this->fbc_user->ID );
			if( !$store_logo ) {
				$store_logo = apply_filters( 'wcfmmp_store_default_logo', $WCFM->plugin_url . 'assets/images/wcfmmp-blue.png' );
			}
						
			$WCFM->library->load_select2_lib();
	
			// AutoSize Plug-in
			wp_register_script( 'jquery-autosize', $fbc_lib_url . '/js/jquery.autosize.min.js', array( 'jquery' ), '1.17.1', true );
			wp_enqueue_script( 'jquery-autosize' );
	
			//Firebase Engine
			wp_register_script( 'fbc-firebase', $fbc_lib_url . '/js/firebase.js', array(), false, true );
			wp_enqueue_script( 'fbc-firebase' );
			
			wp_register_style( 'fbc-frontend', $fbc_lib_url . '/css/fbc-frontend.min.css' );
			wp_enqueue_style( 'fbc-frontend' );
	
			wp_register_script( 'fbc-engine-frontend', $fbc_lib_url . '/js/fbc-engine-frontend.min.js', array( 'jquery', 'fbc-firebase' ), false, true );
			wp_enqueue_script( 'fbc-engine-frontend' );
			
			$user_prefix = '';
			$user_type   = 'visitor';
	
			//if ( apply_filters( 'wcfm_is_allow_chatbox', true ) && ( current_user_can( 'administrator' ) || current_user_can( 'shop_manager' ) || wcfm_is_vendor() ) ) {
				//$user_prefix = 'fbc-op-';
				//$user_type   = 'operator';
			//} 
			if ( is_user_logged_in() ) {
				$user_prefix = 'usr-';
			}
			
			$options = array(
							'app_id'    => esc_html( $this->app_id ),
							'user_info' => array(
								'user_id'      => $user_prefix . $this->fbc_user->ID,
								'user_name'    => apply_filters( 'wcfm_fbc_nickname', $this->fbc_user->display_name ),
								'user_email'   => $this->fbc_user->user_email,
								'gravatar'     => md5( $this->fbc_user->user_email ),
								'user_type'    => $user_type,
								'avatar_type'  => apply_filters( 'wcfm_fbc_avatar_type', 'default' ),
								'avatar_image' => apply_filters( 'wcfm_fbc_avatar_image', '' ),
								'current_page' => $this->fbc_user->current_page,
								'user_ip'      => $this->fbc_user->user_ip
							),
						);
	
			$js_vars = array(
				'defaults'             => $options,
				'ajax_url'             => str_replace( array( 'https:', 'http:' ), '', admin_url( 'admin-ajax.php' ) ),
				'plugin_url'           => $fbc_lib_url,
				'frontend_op_access'   => true,
				'is_premium'           => true,
				'show_busy_form'       => apply_filters( 'wcfm_fbc_busy_form', false ),
				'show_delay'           => apply_filters( 'wcfm_fbc_show_delay', 1000 ),
				'max_guests'           => apply_filters( 'wcfm_fbc_max_guests', 5 ),
				'company_avatar'       => apply_filters( 'wcfm_fbc_company_avatar', $store_logo ),
				'default_user_avatar'  => apply_filters( 'wcfm_fbc_default_avatar', '', 'user' ),
				'default_admin_avatar' => apply_filters( 'wcfm_fbc_default_avatar', $store_logo, 'admin' ),
				'autoplay_opts'        => apply_filters( 'wcfm_fbc_autoplay_opts', array() ),
				'wcfm_wpv_active'      => true,
				'active_vendor'        => apply_filters( 'wcfm_fbc_vendor', array(
					'vendor_id'          => absint( $store_id ),
					'vendor_name'        => wcfm_get_vendor_store_name( $store_id )
				) ),
				'gdpr'                 => apply_filters( 'wcfm_fbc_gdpr_compliance', false ),
				'chat_gdpr'            => apply_filters( 'wcfm_fbc_chat_gdpr_compliance', false ),
				'vendor_only_chat'     => apply_filters( 'wcfm_fbc_vendor_only', false ),
				'button_animation'     => apply_filters( 'wcfm_fbc_round_btn_animation', true ),
				'strings'              => $this->get_firebase_chat_strings( 'frontend' )
			);
			
			wp_localize_script( 'fbc-engine-frontend', 'fbc', $js_vars );
		}
	}
	
	/**
   * Chatbox Scripts
   */
  public function load_scripts( $end_point ) {
	  global $WCFM, $WCFMu;
    
	  switch( $end_point ) {
	  	case 'wcfm-chatbox':
				if( $this->app_lib == 'firebase' ) {
					if( wcfm_is_vendor() && !apply_filters( 'wcfm_is_allow_chatbox', true ) ) return;
					
					$fbc_lib_url = $WCFMu->plugin_url . 'includes/libs/firebase';
					
					$WCFM->library->load_select2_lib();
	
					wp_register_script( 'fbc-engine-console', $fbc_lib_url . '/js/fbc-engine-console.min.js', array( 'jquery', 'fbc-firebase' ), false, true );
					
					$user_prefix = '';
					$user_type   = 'visitor';
			
					if ( apply_filters( 'wcfm_is_allow_chatbox', true ) && ( current_user_can( 'administrator' ) || current_user_can( 'shop_manager' ) || wcfm_is_vendor() ) ) {
						$user_prefix = 'fbc-op-';
						$user_type   = 'operator';
					} elseif ( is_user_logged_in() ) {
						$user_prefix = 'usr-';
					}
			
					$store_logo = apply_filters( 'wcfmmp_store_default_logo', $WCFM->plugin_url . 'assets/images/wcfmmp-blue.png' );
					
					$options = array(
						'app_id'    => esc_html( $this->app_id ),
						'user_info' => array(
							'user_id'      => $user_prefix . $this->fbc_user->ID,
							'user_name'    => apply_filters( 'wcfm_fbc_nickname', $this->fbc_user->display_name ),
							'user_email'   => $this->fbc_user->user_email,
							'gravatar'     => md5( $this->fbc_user->user_email ),
							'user_type'    => $user_type,
							'avatar_type'  => apply_filters( 'wcfm_fbc_avatar_type', 'image' ),
							'avatar_image' => apply_filters( 'wcfm_fbc_avatar_image', $store_logo ),
							'current_page' => $this->fbc_user->current_page,
							'user_ip'      => $this->fbc_user->user_ip
						),
					);
					
					if( wcfm_is_vendor() ) {
						$store_logo = $WCFM->wcfm_vendor_support->wcfm_get_vendor_logo_by_vendor( $this->fbc_user->ID );
						if( !$store_logo ) {
							$store_logo = apply_filters( 'wcfmmp_store_default_logo', $WCFM->plugin_url . 'assets/images/wcfmmp-blue.png' );
						}
						$options['user_info']['user_name']    = wcfm_get_vendor_store_name( $this->fbc_user->ID );
						$options['user_info']['avatar_image'] = $store_logo;
					}
	
					$js_vars = array(
						'defaults'             => $options,
						'ajax_url'             => str_replace( array( 'https:', 'http:' ), '', admin_url( 'admin-ajax.php' ) ),
						'plugin_url'           => $fbc_lib_url,
						'frontend_op_access'   => true,
						'is_premium'           => true,
						'company_avatar'       => apply_filters( 'wcfm_fbc_company_avatar', '' ),
						'default_user_avatar'  => apply_filters( 'wcfm_fbc_default_avatar', '', 'user' ),
						'default_admin_avatar' => apply_filters( 'wcfm_fbc_default_avatar', '', 'admin' ),
						'wcfm_wpv_active'      => true,
						'active_vendor'        => apply_filters( 'wcfm_fbc_vendor', array(
							'vendor_id'   => 0,
							'vendor_name' => ''
						) ),
						'vendor_only_chat'     => apply_filters( 'wcfm_fbc_vendor_only', false ),
						'strings'              => $this->get_firebase_chat_strings( 'console' )
					);
					
					if( wcfm_is_vendor() )  {
						$js_vars['active_vendor']['vendor_id'] = absint( $this->fbc_user->ID );
						$js_vars['active_vendor']['vendor_name'] = wcfm_get_vendor_store_name( $this->fbc_user->ID );
						$store_logo = $WCFM->wcfm_vendor_support->wcfm_get_vendor_logo_by_vendor( $this->fbc_user->ID );
						if( !$store_logo ) {
							$store_logo = apply_filters( 'wcfmmp_store_default_logo', $WCFM->plugin_url . 'assets/images/wcfmmp-blue.png' );
						}
						$js_vars['company_avatar'] = $store_logo;
						$js_vars['default_admin_avatar'] = $store_logo;
					}
					
					wp_localize_script( 'fbc-engine-console', 'fbc', $js_vars );
					
					// AutoSize Plug-in
					wp_register_script( 'jquery-autosize', $fbc_lib_url . '/js/jquery.autosize.min.js', array( 'jquery' ), '1.17.1', true );
					wp_enqueue_script( 'jquery-autosize' );
		
					//Firebase Engine
					wp_register_script( 'fbc-firebase', $fbc_lib_url . '/js/firebase.js', array(), false, true );
					wp_enqueue_script( 'fbc-firebase' );
					
					//FBC Console Engine
					wp_enqueue_script( 'fbc-engine-console' );
				}
      break;
      
      case 'wcfm-chats-offline':
      	$WCFM->library->load_select2_lib();
      	$WCFM->library->load_datatable_lib();
      	wp_enqueue_script( 'wcfmu_chats_offline_js', $WCFMu->library->js_lib_url . 'chatbox/wcfmu-script-chats-offline.js', array('jquery'), $WCFMu->version, true );
      	
      	// Screen manager
	    	$wcfm_screen_manager_data = array();
	    	if( wcfm_is_vendor() ) {
	    		$wcfm_screen_manager_data[3] = 'yes';
	    	}
	    	$wcfm_screen_manager_data    = apply_filters( 'wcfm_screen_manager_data_columns', $wcfm_screen_manager_data, 'chats-offline' );
      	wp_localize_script( 'wcfmu_chats_offline_js', 'wcfm_chats_offline_screen_manage', $wcfm_screen_manager_data );
      	
      	// Localized Script
        $wcfm_messages = array( "message_delete_confirm" => __( "Are you sure and want to delete this 'Offline Message'?\nYou can't undo this action ...", "wc-frontend-manager-ultimate" ), );
			  wp_localize_script( 'wcfmu_chats_offline_js', 'wcfm_chats_offline_messages', $wcfm_messages );
      break;
      
      case 'wcfm-chats-history':
      	$WCFM->library->load_select2_lib();
      	$WCFM->library->load_datatable_lib();
      	wp_enqueue_script( 'wcfmu_chats_history_js', $WCFMu->library->js_lib_url . 'chatbox/wcfmu-script-chats-history.js', array('jquery'), $WCFMu->version, true );
      	
      	// Screen manager
	    	$wcfm_screen_manager_data = array();
	    	if( wcfm_is_vendor() ) {
	    		$wcfm_screen_manager_data[3] = 'yes';
	    	}
	    	if( !apply_filters( 'wcfm_allow_view_customer_email', true ) ) {
	    		$wcfm_screen_manager_data[1] = 'yes';
	    	}
	    	$wcfm_screen_manager_data    = apply_filters( 'wcfm_screen_manager_data_columns', $wcfm_screen_manager_data, 'chats-history' );
      	wp_localize_script( 'wcfmu_chats_history_js', 'wcfm_chats_history_screen_manage', $wcfm_screen_manager_data );
      	
      	// Localized Script
        $wcfm_messages = array( "message_delete_confirm" => __( "Are you sure and want to delete this 'Conversation'?\nYou can't undo this action ...", "wc-frontend-manager-ultimate" ), );
			  wp_localize_script( 'wcfmu_chats_history_js', 'wcfm_chats_history_messages', $wcfm_messages );
      break;
	  }
	}
	
	/**
   * Support Styles
   */
	public function load_styles( $end_point ) {
	  global $WCFM, $WCFMu;
		
	  switch( $end_point ) {
	  	case 'wcfm-chatbox':
		    if( $this->app_lib == 'firebase' ) {
					$fbc_lib_url = $WCFMu->plugin_url . 'includes/libs/firebase';
					
					$WCFM->library->load_select2_lib();
					wp_register_style( 'fbc-styles', $fbc_lib_url . '/css/fbc-styles.min.css' );
					wp_register_style( 'fbc-console', $fbc_lib_url . '/css/fbc-console.min.css' );
					
					// Console stylesheet
					wp_enqueue_style( 'fbc-console' );
				}
		  break;
		  
		  case 'wcfm-chats-offline':
		  	wp_enqueue_style( 'collapsible_css',  $WCFM->library->css_lib_url . 'wcfm-style-collapsible.css', array(), $WCFM->version );
		  	wp_enqueue_style( 'wcfmu_chats_offline_css',  $WCFMu->library->css_lib_url . 'chatbox/wcfmu-style-chats-offline.css', array(), $WCFMu->version );
		  break;
		  
		  case 'wcfm-chats-history':
		  	wp_enqueue_style( 'collapsible_css',  $WCFM->library->css_lib_url . 'wcfm-style-collapsible.css', array(), $WCFM->version );
		  	wp_enqueue_style( 'wcfmu_chats_history_css',  $WCFMu->library->css_lib_url . 'chatbox/wcfmu-style-chats-history.css', array(), $WCFMu->version );
		  break;
	  }
	}
	
	/**
   * Chatbox Views
   */
  public function load_views( $end_point ) {
	  global $WCFM, $WCFMu;
	  
	  switch( $end_point ) {
	  	case 'wcfm-chatbox':
				$WCFMu->template->get_template( 'chatbox/wcfmu-view-chatbox-'.$this->app_lib.'.php' );
      break;
      
      case 'wcfm-chats-offline':
				$WCFMu->template->get_template( 'chatbox/wcfmu-view-chats-offline.php' );
      break;
      
      case 'wcfm-chats-history':
				$WCFMu->template->get_template( 'chatbox/wcfmu-view-chats-history.php' );
      break;
	  }
	}
	
	/**
   * Chatbox Ajax Controllers
   */
  public function wcfm_chatbox_ajax_controller() {
  	global $WCFM, $WCFMu;
  	
  	$controllers_path = $WCFMu->plugin_path . 'controllers/chatbox/';
  	
  	$controller = '';
  	if( isset( $_POST['controller'] ) ) {
  		$controller = wc_clean( $_POST['controller'] );
  		
  		switch( $controller ) {
  			case 'wcfm-chats-offline':
					include_once( $controllers_path . 'wcfmu-controller-chats-offline.php' );
					new WCFMu_Chats_Offline_Controller();
				break;
				
				case 'wcfm-chats-history':
					include_once( $controllers_path . 'wcfmu-controller-chats-history.php' );
					new WCFMu_Chats_History_Controller();
				break;
  		}
  	}
  }
  
  /**
   * Chat Conversation HTML
   */
  function wcfmu_show_conversation_html() {
  	global $WCFM, $WCFMu, $_POST;
  	if( isset( $_POST['conversation'] ) && !empty( $_POST['conversation'] ) ) {
  		$WCFMu->template->get_template( 'chatbox/wcfmu-view-chats-conversation.php', array( 'conversation' => $_POST['conversation'] ) );
  	}
  	die;
  }
	
	/**
	 * Firebox Chat Box
	 */
	function add_firebase_chatbox() {
		global $WCFM, $WCFMu, $post;
		
		$store_id  = 0;
		if (  wcfm_is_store_page() ) {
			$wcfm_store_url = get_option( 'wcfm_store_url', 'store' );
			$store_name = apply_filters( 'wcfmmp_store_query_var', get_query_var( $wcfm_store_url ) );
			if ( !empty( $store_name ) ) {
				$store_user = get_user_by( 'slug', $store_name );
			}
			$store_id  = $store_user->ID;
			if( !$store_id || !$WCFM->wcfm_vendor_support->wcfm_vendor_has_capability( $store_id, 'chatbox' ) ) return;
		}
		
		if( is_product() ) {
			if ( $post && 'product' == $post->post_type ) {
				$store_id = wcfm_get_vendor_id_by_post( $post->ID );
				if( !$store_id || !$WCFM->wcfm_vendor_support->wcfm_vendor_has_capability( $store_id, 'chatbox' ) ) return;
			}
		}
		
		if( $store_id ) {
			$opts = apply_filters( 'fbc_frontend_opts', array(
																													'button_type' => 'classic',
																													'button_pos'  => 'bottom',
																													'form_width'  => '',
																													'chat_width'  => '',
																												) );
			$WCFMu->template->get_template( 'chatbox/wcfmu-view-fbc-chatbox.container.php', $opts );
		}
	}
	
  function wcfm_chatbox_my_account_endpoints() {
		add_rewrite_endpoint( $this->wcfm_myaccount_chatbox_endpoint, EP_ROOT | EP_PAGES );
	}
	
	function wcfm_chatbox_my_account_query_vars( $vars ) {
		$vars[] = $this->wcfm_myaccount_chatbox_endpoint;
	
		return $vars;
	}
	
	function wcfm_chatbox_my_account_flush_rewrite_rules() {
		add_rewrite_endpoint( $this->wcfm_myaccount_chatbox_endpoint, EP_ROOT | EP_PAGES );
		flush_rewrite_rules();
	}
	
	function wcfm_chatbox_my_account_menu_items( $items ) {
		
		if( !wcfm_is_vendor() ) {
			$items = array_slice($items, 0, count($items) - 3, true) +
																		array(
																					$this->wcfm_myaccount_chatbox_endpoint => __( 'Chat Box', 'wc-frontend-manager-ultimate' )
																					) +
																		array_slice($items, count($items) - 3, count($items) - 1, true) ;
		}
		
		return $items;
	}
	
	function wcfm_chatbox_my_account_endpoint_title( $title ) {
		global $wp_query;
	
		$is_endpoint = isset( $wp_query->query_vars[$this->wcfm_myaccount_chatbox_endpoint] );
	
		if ( $is_endpoint && ! is_admin() && is_main_query() && in_the_loop() && is_account_page() ) {
			// New page title.
			$title = __( 'Chat Box', 'wc-frontend-manager-ultimate' );
			remove_filter( 'the_title', array( $this, 'wcfm_chatbox_my_account_endpoint_title' ) );
		}
		
		return $title;
	}
	
	function wcfm_chatbox_my_account_endpoint_content() {
		global $WCFM, $WCFMu, $wpdb;
		$WCFMu->template->get_template( 'chatbox/wcfmu-view-my-account-chatbox.php' );
	}
	
	public function get_unread_message_count() {
		?>
		<script type="text/javascript">
		Talk.ready.then( function() {
			var unreadMessage = document.createElement( 'span' );

			window.talkSession.unreads.on( 'change', function ( conversationId ) {
				var unreadCount = conversationId.length;

				if ( unreadCount > 0) {
					var inboxMenu = document.querySelector( '#wcfm_menu .wcfm_menu_wcfm-chatbox a span.text' );

					inboxMenu.appendChild( unreadMessage );
					unreadMessage.innerText = unreadCount;

					var inboxCount = document.querySelector( '#wcfm_menu .wcfm_menu_wcfm-chatbox a span.text span' );

					inboxCount.style.position       = 'absolute';
					inboxCount.style.top            = '14px';
					inboxCount.style.right          = '23px';
					inboxCount.style.color          = 'white';
					inboxCount.style.fontSize       = '12px';
					inboxCount.style.background     = 'rgb(242, 5, 37) none repeat scroll 0% 0%';
					inboxCount.style.borderRadius   = '50%';
					inboxCount.style.width          = '18px';
					inboxCount.style.height         = '18px';
					inboxCount.style.textAlign      = 'center';
					inboxCount.style.lineHeight     = '17px';
					inboxCount.style.fontWeight     = 'bold';
				}
			} );
		} );
		</script>
		<?php
	}
	
	/**
	 * WCFM Store Chat JS
	 */
	public function load_store_chatjs( $store ) {
		$locale = function_exists( 'get_user_locale' ) ? get_user_locale() : get_locale();
		$locale = str_replace( '_', '-', $locale );
		$locale = apply_filters( 'wcfm_is_allow_chat_locale', $locale );
		?>
		<script type="text/javascript">
		Talk.ready.then( function() {
			var customer = new Talk.User({
				id       : "<?php echo $store->ID ?>",
				name     : "<?php echo $store->display_name ?>",
				email    : "<?php echo $store->user_email ?>",
				photoUrl : "<?php echo esc_url( get_avatar_url( $store->ID ) ) ?>",
				locale   : "<?php echo $locale; ?>"
			});

			window.talkSession = new Talk.Session( {
				appId: "<?php echo esc_attr( $this->app_id ); ?>",
				me: customer
			} );

			var inbox = window.talkSession.createInbox();

			/*window.talkSession.unreads.on('change', function (conversation) {
					var unreadCount = conversation.length;
					
					//console.log(unreadCount);

					if (unreadCount > 0) {
						var popup = talkSession.createPopup();
					}

					if (popup != '') {
						if (unreadCount > 0) {
							popup.mount();
						}
					}
				});*/

		} );
		</script>
		<?php

		//$this->get_unread_message_count();
  }
  
  /**
   * WCFM Custmer Chat JS
   */
  public function load_customer_chatjs( $customer ) {
  	global $WCFM, $WCFMu;
  	
  	$store_id  = 0;
  	$store_user = '';
		if ( wcfm_is_store_page() ) {
			$wcfm_store_url = get_option( 'wcfm_store_url', 'store' );
			$store_name = get_query_var( $wcfm_store_url );
			if ( !empty( $store_name ) ) {
				$store = get_user_by( 'slug', $store_name );
			}
			$store_id   		= $store->ID;
		} elseif( is_product() ) {
			$store_id = get_post_field( 'post_author', get_the_ID() );
		}
		
		$store_logo = apply_filters( 'wcfmmp_store_default_logo', $WCFM->plugin_url . 'assets/images/wcfmmp-blue.png' );
		if( $store_id ) {
			$store_user = wcfmmp_get_store( $store_id );
			$store_logo = $WCFM->wcfm_vendor_support->wcfm_get_vendor_logo_by_vendor( $store_id );
			if( !$store_logo ) {
				$store_logo = apply_filters( 'wcfmmp_store_default_logo', $WCFM->plugin_url . 'assets/images/wcfmmp-blue.png' );
			}
		}
		
		//if( !$store_user || !$store_id ) return;
		
		$locale = function_exists( 'get_user_locale' ) ? get_user_locale() : get_locale();
		$locale = str_replace( '_', '-', $locale );
		$locale = apply_filters( 'wcfm_is_allow_chat_locale', $locale );

		?>
		<script type="text/javascript">
		Talk.ready.then( function() {
			var customer = new Talk.User( {
					id             : "<?php echo $customer->ID ?>",
					name           : "<?php echo $customer->display_name ?>",
					email          : "<?php echo ! empty( $customer->user_email ) ? $customer->user_email : 'fake@email.com'; ?>",
					configuration  : "vendor",
					photoUrl       : "<?php echo esc_url( get_avatar_url( $customer->ID ) ) ?>",
					locale         : "<?php echo $locale; ?>"
			} );

			window.talkSession = new Talk.Session( {
					appId: "<?php echo esc_attr( $this->app_id ); ?>",
					me: customer
			} );
			
			<?php if (  wcfm_is_store_page() || is_product() ) { ?>
				<?php if( $store_user && $store_id && $WCFM->wcfm_vendor_support->wcfm_vendor_has_capability( $store_id, 'chatbox' ) ) { ?>

					var seller = new Talk.User( {
							id              : "<?php echo $store_user->get_id(); ?>",
							name            : "<?php echo ! empty( $store_user->get_shop_name() ) ? $store_user->get_shop_name() : 'fakename'; ?>",
							email           : "<?php echo ! empty( $store_user->get_email() ) ? $store_user->get_email() : 'fake@email.com'; ?>",
							configuration   : "vendor",
							photoUrl        : "<?php echo esc_url( $store_logo ) ?>",
							welcomeMessage  : "<?php _e( 'How may I help you?', 'wc-frontend-manager-ultimate' ) ?>",
							locale          : "<?php echo $locale; ?>"
					} );
		
					/*window.talkSession.unreads.on( 'change', function ( conversation ) {
						var unreadCount = conversation.length;
						console.log(unreadCount);
		
						if ( unreadCount > 0 ) {
							var popup = talkSession.createPopup();
						}
		
						if ( popup != '' ) {
							if ( unreadCount > 0 ) {
								popup.mount();
							}
						}
		
					} );*/
		
					setTimeout(function() {
						var chat_btns = document.querySelectorAll( '.wcfm-chat-now' );
			
						chat_btns.forEach(function(chat_btn) {
							if ( chat_btn !== null ) {
								chat_btn.addEventListener( 'click', function( e ) {
									e.preventDefault();
									
									var conversation = talkSession.getOrCreateConversation(Talk.oneOnOneId(customer, seller));
									conversation.setParticipant(customer);
									conversation.setParticipant(seller);
									var inbox = talkSession.createInbox({selected: conversation});
									var popup = talkSession.createPopup(conversation);
									popup.mount();
								});
							}
						});
					}, 2000 );
				<?php } ?>
			<?php } ?>
		} );
		</script>
		<?php
  }
	
	/**
	 * WCFM Chatbox JS
	 */
	function wcfm_chatbox_scripts() {
 		global $WCFM, $WCFMu, $wp, $WCFM_Query;
 		
 		if( $this->app_lib == 'firebase' ) {
 			if( !is_user_logged_in() && apply_filters( 'wcfm_chat_require_login', false ) ) {
 				$WCFM->library->load_wcfm_login_popup_lib();
 				return;
 			}
 			?>
 			<script type="text/javascript">
 			  jQuery(document).ready(function($) {
 			  	setTimeout(function() {
						if( $('.wcfm_fbc_chatwindow').length > 0 ) {
							$('.wcfm-chat-now').each(function() {
								$(this).click(function() {
									$('.wcfm_fbc_chatwindow').toggleClass( 'wcfm_custom_hide', function() {	
										if( $('.wcfm_fbc_chatwindow').hasClass( 'wcfm_custom_hide' ) ) {
											$('#FBC_chat_btn').hide();
											$('#FBC_chat').show();
										}
									});
								});
							});
						}
					}, 2000 );
 			  });  
 			</script>
 			<?php
 		} elseif( $this->app_lib == 'talkjs' ) {
			if( is_user_logged_in() ) {
				$current_user = wp_get_current_user();
				
				// Load Talk JS Lib
				$WCFMu->library->load_talkjs_lib();
				
				if( wcfm_is_vendor())  {
					if( apply_filters( 'wcfm_is_allow_chatbox', true ) ) {
						$this->load_store_chatjs( $current_user );
					}
				} else {
					$this->load_customer_chatjs( $current_user );
				}
				
				$this->chatbox_responsive();
			} else {
				$WCFM->library->load_wcfm_login_popup_lib();
			}
		}
 	}
 	
 	function chatbox_responsive() {
		?>
		<style type="text/css">
			@media only screen and (max-width: 600px) {
				.__talkjs_popup {
					top: 100px !important;
					height: 80% !important;
				}
			}
		</style>
		<?php
   }
   
   /**
    * Chat Box Admin Setting
    */
   function wcfm_chatbox_setting( $wcfm_options ) {
		global $WCFM, $WCFMu;
		
		$wcfm_chatbox_setting = get_option( 'wcfm_chatbox_setting', array() );
		
		$chat_lib = !empty( $wcfm_chatbox_setting['lib'] ) ? $wcfm_chatbox_setting['lib'] : '';
		
		$fbc_app_id = !empty( $wcfm_chatbox_setting['fbc_app_id'] ) ? $wcfm_chatbox_setting['fbc_app_id'] : '';
		$fbc_secret = !empty( $wcfm_chatbox_setting['fbc_secret'] ) ? $wcfm_chatbox_setting['fbc_secret'] : '';
		
		$app_id = !empty( $wcfm_chatbox_setting['app_id'] ) ? $wcfm_chatbox_setting['app_id'] : '';
		$secret = !empty( $wcfm_chatbox_setting['secret'] ) ? $wcfm_chatbox_setting['secret'] : '';
		$label  = !empty( $wcfm_chatbox_setting['label'] ) ? $wcfm_chatbox_setting['label'] : __( 'Chat Now', 'wc-frontend-manager-ultimate' );
		
		if( !$chat_lib && $app_id ) {
			$chat_lib = 'talkjs';
		}
		
		$background = !empty( $wcfm_chatbox_setting['background'] ) ? $wcfm_chatbox_setting['background'] : '#1C2B36';
		$hover      = !empty( $wcfm_chatbox_setting['hover'] ) ? $wcfm_chatbox_setting['hover'] : '#00798b';
		$text       = !empty( $wcfm_chatbox_setting['text'] ) ? $wcfm_chatbox_setting['text'] : '#b0bec5';
		$text_hover = !empty( $wcfm_chatbox_setting['text_hover'] ) ? $wcfm_chatbox_setting['text_hover'] : '#b0bec5';
		
		?>
		<!-- collapsible -->
		<div class="page_collapsible" id="wcfm_settings_form_chatbox_head">
			<label class="wcfmfa fa-comments"></label>
			<?php _e('Chat Box', 'wc-frontend-manager-ultimate'); ?><span></span>
		</div>
		<div class="wcfm-container">
			<div id="wcfm_settings_form_chatbox_expander" class="wcfm-content">
				<h2><?php _e('Live Chat Setting', 'wc-frontend-manager-ultimate'); ?></h2>
				<?php wcfm_video_tutorial( 'https://docs.wclovers.com/live-chat/' ); ?>
				<div class="wcfm_clearfix"></div>
				
				<?php
					$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_settings_fields_chatbox', array(
						                                                                                "wcfm_chatbox_setting_lib" => array('label' => __('Chat Library', 'wc-frontend-manager-ultimate') , 'name' => 'wcfm_chatbox_setting[lib]','type' => 'select', 'options' => array( 'firebase' => __( 'Firebase', 'wc-frontend-manager-ultimate' ), 'talkjs' => __( 'TalkJS', 'wc-frontend-manager-ultimate' ) ), 'class' => 'wcfm-select wcfm_ele', 'value' => $chat_lib, 'label_class' => 'wcfm_title' ),
						                                                                                "wcfm_chatbox_setting_fbc_app_id" => array('label' => __('APP ID', 'wc-frontend-manager-ultimate') , 'name' => 'wcfm_chatbox_setting[fbc_app_id]','type' => 'text', 'class' => 'wcfm-text wcfm_ele wcfm_chatbox_field wcfm_chatbox_field_firebase', 'value' => $fbc_app_id, 'label_class' => 'wcfm_title wcfm_chatbox_field wcfm_chatbox_field_firebase', 'desc_class' => 'wcfm_page_options_desc wcfm_chatbox_field wcfm_chatbox_field_firebase', 'desc' => sprintf( __( 'URL of your Firebase application. If you don\'t have one, get a free Firebase application here:  %shttp://www.firebase.com%s', 'wc-frontend-manager-ultimate' ), '<a target="_blank" href="https://console.firebase.google.com/">', '</a>' ) ),
																																														"wcfm_chatbox_setting_fbc_secret" => array('label' => __('APP Secret', 'wc-frontend-manager-ultimate') , 'name' => 'wcfm_chatbox_setting[fbc_secret]','type' => 'password', 'class' => 'wcfm-text wcfm_ele wcfm_chatbox_field wcfm_chatbox_field_firebase', 'value' => $fbc_secret, 'label_class' => 'wcfm_title wcfm_chatbox_field wcfm_chatbox_field_firebase', 'desc_class' => 'wcfm_page_options_desc wcfm_chatbox_field wcfm_chatbox_field_firebase', 'desc' => __( 'It can be found under the "Secrets" menu in your Firebase app dashboard', 'wc-frontend-manager-ultimate' ) ),
																																														"wcfm_chatbox_setting_app_id" => array('label' => __('APP ID', 'wc-frontend-manager-ultimate') , 'name' => 'wcfm_chatbox_setting[app_id]','type' => 'text', 'class' => 'wcfm-text wcfm_ele wcfm_chatbox_field wcfm_chatbox_field_talkjs', 'value' => $app_id, 'label_class' => 'wcfm_title wcfm_chatbox_field wcfm_chatbox_field_talkjs', 'desc_class' => 'wcfm_page_options_desc wcfm_chatbox_field wcfm_chatbox_field_talkjs', 'desc' => sprintf( __( 'Get your Talk JS %sAPP ID%s', 'wc-frontend-manager-ultimate' ), '<a target="_blank" href="https://talkjs.com/dashboard/signup/standard/">', '</a>' ) ),
																																														"wcfm_chatbox_setting_secret" => array('label' => __('Secret Key', 'wc-frontend-manager-ultimate') , 'name' => 'wcfm_chatbox_setting[secret]','type' => 'password', 'class' => 'wcfm-text wcfm_ele wcfm_chatbox_field wcfm_chatbox_field_talkjs', 'value' => $secret, 'label_class' => 'wcfm_title wcfm_chatbox_field wcfm_chatbox_field_talkjs', 'desc_class' => 'wcfm_page_options_desc wcfm_chatbox_field wcfm_chatbox_field_talkjs', 'desc' => sprintf( __( 'Get your Talk JS %sSecret Key%s', 'wc-frontend-manager-ultimate' ), '<a target="_blank" href="https://talkjs.com/dashboard/signup/standard/">', '</a>' )  ),
																																														"wcfm_chatnow_button_label" => array('label' => __('Chat Now Button Label', 'wc-frontend-manager-ultimate') , 'name' => 'wcfm_chatbox_setting[label]','type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'value' => $label, 'label_class' => 'wcfm_title' ),
																																														"wcfm_chatnow_button_background" => array('label' => __( 'Chat Now Button Background', 'wc-frontend-manager-ultimate') , 'name' => 'wcfm_chatbox_setting[background]','type' => 'colorpicker', 'class' => 'wcfm-text wcfm_ele colorpicker', 'value' => $background, 'label_class' => 'wcfm_title' ),
																																														"wcfm_chatnow_button_hover" => array('label' => __( 'Chat Now Button Hover', 'wc-frontend-manager-ultimate') , 'name' => 'wcfm_chatbox_setting[hover]','type' => 'colorpicker', 'class' => 'wcfm-text wcfm_ele colorpicker', 'value' => $hover, 'label_class' => 'wcfm_title' ),
																																														"wcfm_chatnow_button_text" => array('label' => __( 'Chat Now Button Text', 'wc-frontend-manager-ultimate') , 'name' => 'wcfm_chatbox_setting[text]','type' => 'colorpicker', 'class' => 'wcfm-text wcfm_ele colorpicker', 'value' => $text, 'label_class' => 'wcfm_title' ),
																																														"wcfm_chatnow_button_text_hover" => array('label' => __( 'Chat Now Button Text Hover', 'wc-frontend-manager-ultimate') , 'name' => 'wcfm_chatbox_setting[text_hover]','type' => 'colorpicker', 'class' => 'wcfm-text wcfm_ele colorpicker', 'value' => $text_hover, 'label_class' => 'wcfm_title' ),
																																														) ) );
				?>
			</div>
		</div>
		<div class="wcfm_clearfix"></div>
		<!-- end collapsible -->
		<?php
	}
	
	function wcfm_chatbox_setting_save( $wcfm_settings_form ) {
		global $WCFM, $WCFMu, $_POST;
		
		if( isset( $wcfm_settings_form['wcfm_chatbox_setting'] ) ) {
			update_option( 'wcfm_chatbox_setting', $wcfm_settings_form['wcfm_chatbox_setting'] );
		}
	}
	
	// Support Functions
	function fbc_get_ip_address() {

			if ( !empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
					$ip_addr = $_SERVER['HTTP_CLIENT_IP'];
			}
			elseif ( !empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
					$ip_addr = $_SERVER['HTTP_X_FORWARDED_FOR'];
			}
			else {
					$ip_addr = $_SERVER['REMOTE_ADDR'];
			}

			if ( $ip_addr === false ) {
					$ip_addr = '0.0.0.0';

					return $ip_addr;
			}

			if ( strpos( $ip_addr, ',' ) !== false ) {
					$x       = explode( ',', $ip_addr );
					$ip_addr = trim( end( $x ) );
			}

			if ( !$this->fbc_validate_ip( $ip_addr ) ) {
					$ip_addr = '0.0.0.0';
			}

			return $ip_addr;

	}

	function fbc_get_server_item( $index = '' ) {
			if ( !isset( $_SERVER[$index] ) ) {
					return false;
			}

			return $_SERVER[$index];
	}

	function fbc_validate_ip( $ip, $which = '' ) {

			$which = strtolower( $which );

			// First check if filter_var is available
			if ( is_callable( 'filter_var' ) ) {
					switch ( $which ) {
							case 'ipv4':
									$flag = FILTER_FLAG_IPV4;
									break;

							case 'ipv6':
									$flag = FILTER_FLAG_IPV6;
									break;

							default:
									$flag = '';
									break;
					}
					return ( bool ) filter_var( $ip, FILTER_VALIDATE_IP, $flag );
			}

			if ( $which !== 'ipv6' && $which !== 'ipv4' ) {
					if ( strpos( $ip, ':' ) !== false ) {
							$which = 'ipv6';
					}
					elseif ( strpos( $ip, '.' ) !== false ) {
							$which = 'ipv4';
					}
					else {
							return false;
					}
			}
			return call_user_func( 'validate_' . $which, $ip );
	}

	function fbc_validate_ipv4( $ip ) {

			$ip_segments = explode( '.', $ip );

			// Always 4 segments needed
			if ( count( $ip_segments ) !== 4 ) {
					return false;
			}
			// IP can not start with 0
			if ( $ip_segments[0][0] == '0' ) {
					return false;
			}

			// Check each segment
			foreach ( $ip_segments as $segment ) {
					// IP segments must be digits and can not be longer than 3 digits or greater then 255
					if ( $segment == '' || preg_match( "/[^0-9]/", $segment ) || $segment > 255 || strlen( $segment ) > 3 ) {
							return false;
					}
			}
			return true;
	}

	function fbc_validate_ipv6( $str ) {

			// 8 groups, separated by : 0-ffff per group one set of consecutive 0 groups can be collapsed to ::
			$groups    = 8;
			$collapsed = false;
			$chunks    = array_filter( preg_split( '/(:{1,2})/', $str, NULL, PREG_SPLIT_DELIM_CAPTURE ) );

			// Rule out easy nonsense
			if ( current( $chunks ) == ':' || end( $chunks ) == ':' ) {
					return false;
			}

			// PHP supports IPv4-mapped IPv6 addresses, so we'll expect those as well
			if ( strpos( end( $chunks ), '.' ) !== false ) {
					$ipv4 = array_pop( $chunks );
					if ( !$this->fbc_validate_ipv4( $ipv4 ) ) {
							return false;
					}
					$groups --;
			}

			while ( $seg = array_pop( $chunks ) ) {
					if ( $seg[0] == ':' ) {
							if ( -- $groups == 0 ) {
									return false; // too many groups
							}
							if ( strlen( $seg ) > 2 ) {
									return false; // long separator
							}
							if ( $seg == '::' ) {
									if ( $collapsed ) {
											return false; // multiple collapsed
									}
									$collapsed = true;
							}
					}
					elseif ( preg_match( "/[^0-9a-f]/i", $seg ) || strlen( $seg ) > 4 ) {
							return false; // invalid segment
					}
			}

			return $collapsed || $groups == 1;
	}

	function fbc_get_current_page_url() {
		$page_URL = 'http';

		if ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == 'on' ) {
				$page_URL .= "s";
		}

		$page_URL .= '://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];

		return $page_URL;
	}
	
	public function get_firebase_chat_strings( $context ) {

		if ( $context == 'console' ) {

			$msg = array(
				'no_msg'            => __( 'No messages found', 'wc-frontend-manager-ultimate' ),
				'connecting'        => __( 'Connecting', 'wc-frontend-manager-ultimate' ),
				'writing'           => __( '%s is writing', 'wc-frontend-manager-ultimate' ),
				'please_wait'       => __( 'Please wait', 'wc-frontend-manager-ultimate' ),
				'conn_err'          => __( 'Connection error!', 'wc-frontend-manager-ultimate' ),
				'online_btn'        => __( 'Online', 'wc-frontend-manager-ultimate' ),
				'offline_btn'       => __( 'Offline', 'wc-frontend-manager-ultimate' ),
				'connect'           => __( 'Connect', 'wc-frontend-manager-ultimate' ),
				'disconnect'        => __( 'Disconnect', 'wc-frontend-manager-ultimate' ),
				'you_offline'       => __( 'You are offline', 'wc-frontend-manager-ultimate' ),
				'ntf_close_console' => __( 'If you leave the chat, you will be logged out. However you will be able to save the conversations into your server when you will come back in the console!', 'wc-frontend-manager-ultimate' ),
				'new_msg'           => __( 'New Message', 'wc-frontend-manager-ultimate' ),
				'new_user_online'   => __( 'New User Online', 'wc-frontend-manager-ultimate' ),
				'saving'            => __( 'Saving', 'wc-frontend-manager-ultimate' ),
				'waiting_users'     => __( 'User queue: %d', 'wc-frontend-manager-ultimate' ),
				'talking_label'     => __( 'Talking with %s', 'wc-frontend-manager-ultimate' ),
				'current_shop'      => __( '%s shop', 'wc-frontend-manager-ultimate' ),
				'macro_title'       => __( 'Apply Macro', 'wc-frontend-manager-ultimate' ),
				'macro_err'         => __( 'No results match', 'wc-frontend-manager-ultimate' ),
			);
		} else {

			$msg = array(
				'close_msg_user'   => __( 'The user has closed the conversation', 'wc-frontend-manager-ultimate' ),
				'no_op'            => __( 'No operators online', 'wc-frontend-manager-ultimate' ),
				'connecting'       => __( 'Connecting', 'wc-frontend-manager-ultimate' ),
				'writing'          => __( '%s is writing', 'wc-frontend-manager-ultimate' ),
				'sending'          => __( 'Sending', 'wc-frontend-manager-ultimate' ),
				'field_empty'      => __( 'Please fill out all required fields', 'wc-frontend-manager-ultimate' ),
				'invalid_username' => __( 'Username is invalid', 'wc-frontend-manager-ultimate' ),
				'invalid_email'    => __( 'Email is invalid', 'wc-frontend-manager-ultimate' ),
				'already_logged'   => __( 'A user is already logged in with the same email address', 'wc-frontend-manager-ultimate' ),
			);

		}

		return array(
			'months'       => array(
				'jan' => __( 'January', 'wc-frontend-manager-ultimate' ),
				'feb' => __( 'February', 'wc-frontend-manager-ultimate' ),
				'mar' => __( 'March', 'wc-frontend-manager-ultimate' ),
				'apr' => __( 'April', 'wc-frontend-manager-ultimate' ),
				'may' => __( 'May', 'wc-frontend-manager-ultimate' ),
				'jun' => __( 'June', 'wc-frontend-manager-ultimate' ),
				'jul' => __( 'July', 'wc-frontend-manager-ultimate' ),
				'aug' => __( 'August', 'wc-frontend-manager-ultimate' ),
				'sep' => __( 'September', 'wc-frontend-manager-ultimate' ),
				'oct' => __( 'October', 'wc-frontend-manager-ultimate' ),
				'nov' => __( 'November', 'wc-frontend-manager-ultimate' ),
				'dec' => __( 'December', 'wc-frontend-manager-ultimate' )
			),
			'months_short' => array(
				'jan' => __( 'Jan', 'wc-frontend-manager-ultimate' ),
				'feb' => __( 'Feb', 'wc-frontend-manager-ultimate' ),
				'mar' => __( 'Mar', 'wc-frontend-manager-ultimate' ),
				'apr' => __( 'Apr', 'wc-frontend-manager-ultimate' ),
				'may' => __( 'May', 'wc-frontend-manager-ultimate' ),
				'jun' => __( 'Jun', 'wc-frontend-manager-ultimate' ),
				'jul' => __( 'Jul', 'wc-frontend-manager-ultimate' ),
				'aug' => __( 'Aug', 'wc-frontend-manager-ultimate' ),
				'sep' => __( 'Sep', 'wc-frontend-manager-ultimate' ),
				'oct' => __( 'Oct', 'wc-frontend-manager-ultimate' ),
				'nov' => __( 'Nov', 'wc-frontend-manager-ultimate' ),
				'dec' => __( 'Dec', 'wc-frontend-manager-ultimate' )
			),
			'time'         => array(
				'suffix'  => __( 'ago', 'wc-frontend-manager-ultimate' ),
				'seconds' => __( 'less than a minute', 'wc-frontend-manager-ultimate' ),
				'minute'  => __( 'about a minute', 'wc-frontend-manager-ultimate' ),
				'minutes' => __( '%d minutes', 'wc-frontend-manager-ultimate' ),
				'hour'    => __( 'about an hour', 'wc-frontend-manager-ultimate' ),
				'hours'   => __( 'about %d hours', 'wc-frontend-manager-ultimate' ),
				'day'     => __( 'a day', 'wc-frontend-manager-ultimate' ),
				'days'    => __( '%d days', 'wc-frontend-manager-ultimate' ),
				'month'   => __( 'about a month', 'wc-frontend-manager-ultimate' ),
				'months'  => __( '%d months', 'wc-frontend-manager-ultimate' ),
				'year'    => __( 'about a year', 'wc-frontend-manager-ultimate' ),
				'years'   => __( '%d years', 'wc-frontend-manager-ultimate' ),
			),
			'msg'          => $msg
		);

	}
	
	public function fbc_user_auth() {

		if ( empty( $this->app_secret ) ) {
			return;
		}

		$token_gen = new Services_FirebaseTokenGenerator( esc_html( $this->app_secret ) );
		$prefix    = ( is_user_logged_in() && ! defined( 'FBC_OPERATOR' ) ) ? 'usr-' : '';
		$data      = array(
			'uid'         => $prefix . $this->fbc_user->ID,
			'is_operator' => ( defined( 'FBC_OPERATOR' ) ) ? true : false,
		);
		$opts      = array(
			'admin' => ( current_user_can( 'adminstrator' ) ) ? true : false,
			'debug' => true
		);

		return $token_gen->createToken( $data, $opts );

	}
	
	function fbc_ajax_callback() {

		// Response var
		$resp = array();

		try {

			// Handling the supported actions:
			switch ( $_GET['mode'] ) {

					case 'get_token':
							$resp = $this->fbc_ajax_get_token();
							break;

					case 'save_chat':
							$resp = $this->fbc_ajax_save_chat( $_POST );
							break;

					case 'offline_form':
							$resp = $this->fbc_ajax_offline_form( $_REQUEST );
							break;

					case 'chat_evaluation':
							$resp = $this->fbc_ajax_evaluation( $_POST );
							break;
					default:
							throw new Exception( 'Wrong action: ' . @$_REQUEST['mode'] );
				}

			} catch ( Exception $e ) {
					$resp['err_code'] = $e->getCode();
					$resp['error']    = $e->getMessage();
			}

			// Response output
			header( "Content-Type: application/json" );

			echo json_encode( $resp );

			exit;

	}

	function fbc_ajax_get_token() {

		$token = $this->fbc_user_auth();

		return array( 'token' => $token );
	}

	function fbc_ajax_save_chat( $data ) {

		$msg = array( 'msg' => __( 'Successfully closed!', 'wc-frontend-manager-ultimate' ) );

		$msg = $this->fbc_save_chat_data( $data );

		return $msg;
	}

	function fbc_ajax_offline_form( $form_data ) {
		global $WCFMu, $wpdb;
		
		require_once( $WCFMu->plugin_path . 'includes/libs/firebase/class-fbc-user.php' );
		
		$resp = array(
			'offline-fail' => false,
			'user-fail'    => false,
			'db-fail'      => false,
		);

		$error_msg     = __( 'Something went wrong. Please try again', 'wc-frontend-manager-ultimate' );
		$default_email = get_option( 'admin_email' );
		$user          = new FBC_User;
		$ip_address    = $this->fbc_get_ip_address();
		$from          = $default_email;
		$page          = $_SERVER['HTTP_REFERER'];

		// Send offline message
		$to        = $default_email;
		$subject   = apply_filters( 'wcfm_fbc_offline_mail_subject', __( 'New offline message', 'wc-frontend-manager-ultimate' ) );
		$mail_body = __( 'You have received an offline message', 'wc-frontend-manager-ultimate' );


		$to .= $this->fbc_get_vendor_email( $form_data['vendor_id'] );
		
		if( !defined( 'DOING_WCFM_EMAIL' ) ) 
	  	 define( 'DOING_WCFM_EMAIL', true );


		if ( ! $this->fbc_send_offline_msg( $from, $to, $subject, $user, $ip_address, $form_data, $mail_body, $page ) ) {
			//$resp['offline-fail'] = true;
		}

		if ( ! $resp['offline-fail'] ) {

			$message_body = esc_html( apply_filters( 'wcfm_fbc_offline_mail_message', __( 'Thanks for contacting us. We will answer as soon as possible.', 'wc-frontend-manager-ultimate' ) ) );

			//Send a copy to user
			$to      = $form_data['email'];
			$subject = apply_filters( 'wcfm_fbc_offline_mail_subject_user', __( 'We have received your offline message', 'wc-frontend-manager-ultimate' ) );
			$mail_body = wp_strip_all_tags( $message_body ) . '<br /><br />' . apply_filters( 'wcfm_fbc_offline_mail_data_header', __( 'Here follows a recap of the details you have entered', 'wc-frontend-manager-ultimate' ) . ':' );

			if ( ! $this->fbc_send_offline_msg( $from, $to, $subject, $user, $ip_address, $form_data, $mail_body, $page, true ) ) {
				//$resp['user-fail'] = true;
			}

			// Add offline message to db
			$args = array(
				'user_name'    => $form_data['name'],
				'user_email'   => $form_data['email'],
				'user_message' => $form_data['message'],
				'user_info'    => array(
					'os'              => $user->info( 'os' ),
					'browser'         => $user->info( 'browser' ),
					'version'         => $user->info( 'version' ),
					'ip'              => $ip_address,
					'page'            => $page,
					'gdpr_acceptance' => isset( $form_data['gdpr_acceptance'] ) ? $form_data['gdpr_acceptance'] : '',
				),
				'vendor_id'    => $form_data['vendor_id']
			);

			if ( ! $this->fbc_add_offline_message( $args ) ) {
				//$resp['db-fail'] = true;
			}

			if ( $resp['db-fail'] ) {
				return array( 'error' => $error_msg );
			} else {
				if ( ! $resp['db-fail'] && $resp['user-fail'] ) {
					return array( 'warn' => __( 'An error occurred while sending a copy of your message. However, administrators received it correctly.', 'wc-frontend-manager-ultimate' ) );
				}
			}

		} else {

			return array( 'error' => $error_msg );

		}

		return array( 'msg' => __( 'Successfully sent! Thank you', 'wc-frontend-manager-ultimate' ) );

	}
	
	function fbc_send_offline_msg( $from, $mail_to, $subject, $user, $ip_address, $form_data, $mail_body, $page, $user_copy = false ) {
		global $WCFMu, $wpdb;
		
		$args = array(
			'mail_body'  => $mail_body,
			'name'       => $form_data['name'],
			'email'      => $form_data['email'],
			'message'    => $form_data['message'],
			'os'         => $user->info( 'os' ),
			'browser'    => $user->info( 'browser' ),
			'version'    => $user->info( 'version' ),
			'ip_address' => $ip_address,
			'page'       => $page,
		);
		
		ob_start();

		$WCFMu->template->get_template( 'chatbox/emails/offline-message.php', $args );

		$message    = ob_get_clean();
		
		$message = apply_filters( 'wcfm_email_content_wrapper', $message, $subject );
		//$reply_to   = ( $user_copy ) ? $from : $form_data['email'];
		//$from_name  = ( $user_copy ) ? '' : $form_data['name'];
		//$from_email = ( $user_copy ) ? $from : $form_data['email'];
		
		//$headers[] = 'Reply-to: ' . $customer_name . ' <' . $customer_email . '>';
		
		$subject = '['.get_option( 'blogname' ).'] ' . $subject;
		$subject = apply_filters( 'wcfm_email_subject_wrapper', $subject );
		
		if( !defined( 'DOING_WCFM_EMAIL' ) ) 
		  define( 'DOING_WCFM_EMAIL', true );

		return wp_mail( $mail_to, $subject, $message );

	}

	function fbc_add_offline_message( $args ) {
		
		global $wpdb;

		$result = $wpdb->insert(
			$wpdb->prefix . 'wcfm_fbc_offline_messages',
			array(
				'user_name'    => $args['user_name'],
				'user_email'   => $args['user_email'],
				'user_message' => stripslashes( $args['user_message'] ),
				'user_info'    => maybe_serialize( $args['user_info'] ),
				'mail_date'    => date( 'Y-m-d', strtotime( current_time( 'mysql' ) ) ),
				'vendor_id'    => $args['vendor_id']
			),
			array( '%s', '%s', '%s', '%s', '%s', '%d' )
		);

		if ( $result === false ) {

			return false;

		} else {

			return true;

		}

	}

	function fbc_ajax_evaluation( $data ) {
		
		$error_msg = __( 'Something went wrong. Please try again', 'wc-frontend-manager-ultimate' );

		global $wpdb;

		$resp = $wpdb->update(
			$wpdb->prefix . 'wcfm_fbc_chat_sessions',
			array(
				'evaluation'   => $data['evaluation'],
				'receive_copy' => $data['receive_copy'],
			),
			array( 'conversation_id' => $data['conversation_id'] ),
			array(
				'%s',
				'%d'
			),
			array( '%s' )
		);

		if ( $resp === false ) {
			//return array( 'error' => $error_msg );
		}

		if ( $this->fbc_count_messages( $data['conversation_id'] ) != 0 ) {

			$resp = $this->fbc_send_chat_data_user( $data['conversation_id'], $data['receive_copy'], $data['user_email'] );

			if ( $resp === false ) {
				//return array( 'error' => $error_msg );
			}

			//$resp = $this->fbc_send_chat_data_admin( $data['conversation_id'], $data['chat_with'], 'visitor' );
			 //if ( $resp === false ) {
				 //return array( 'error' => $error_msg );
			 //}

		}

		return array( 'msg' => __( 'Successfully saved!', 'wc-frontend-manager-ultimate' ) );

	}

	function fbc_save_chat_data( $data ) {
		
		$error_msg = __( 'Something went wrong. Please try again', 'wc-frontend-manager-ultimate' );

		global $wpdb;

		$user_data = array(
			'user_id'     => $data['user_id'],
			'user_type'   => $data['user_type'],
			'user_name'   => @$data['user_name'],
			'user_ip'     => sprintf( '%u', ip2long( $data['user_ip'] ) ), // Support 32bit systems as well not to show up negative val.
			'user_email'  => @$data['user_email'],
			'last_online' => @$data['last_online'] || 0,
			'vendor_id'   => @$data['vendor_id']
		);

		$resp = $wpdb->replace( $wpdb->prefix . 'wcfm_fbc_chat_visitors', $user_data, array( '%s', '%s', '%s', '%d', '%s', '%s', '%d' ) );

		if ( $resp === false ) {
			//return array( 'error' => $error_msg );
		}

		$cnv_data = array(
			'conversation_id' => $data['conversation_id'],
			'user_id'         => $data['user_id'],
			'created_at'      => $data['created_at'],
			'evaluation'      => $data['evaluation'],
			'duration'        => $data['duration'],
			'receive_copy'    => $data['receive_copy']
		);

		$resp = $wpdb->replace( $wpdb->prefix . 'wcfm_fbc_chat_sessions', $cnv_data, array( '%s', '%s', '%s', '%s', '%s', '%d' ) );

		if ( $resp === false ) {
			//return array( 'error' => $error_msg );
		}

		if ( ! empty( $data['msgs'] ) ) {

			foreach ( $data['msgs'] as $msg_id => $msg ) {

				$msg_data = array(
					'message_id'      => $msg_id,
					'conversation_id' => $msg['conversation_id'],
					'user_id'         => $msg['user_id'],
					'user_name'       => $msg['user_name'],
					'msg'             => $msg['msg'],
					'msg_time'        => $msg['msg_time']
				);

				$resp = $wpdb->replace( $wpdb->prefix . 'wcfm_fbc_chat_rows', $msg_data, array( '%s', '%s', '%s', '%s', '%s', '%s' ) );

				if ( $resp === false ) {
					//return array( 'error' => $error_msg );
				}

			}

		}

		if ( isset( $data['send_email'] ) && $data['send_email'] == 'true' && $this->fbc_count_messages( $data['conversation_id'] ) != 0 ) {

			$resp = $this->fbc_send_chat_data_user( $data['conversation_id'], $data['receive_copy'], $data['user_email'] );

			if ( $resp === false ) {
				//return array( 'error' => $error_msg );
			}

			$resp = $this->fbc_send_chat_data_admin( $data['conversation_id'], $data['chat_with'], 'operator' );

			if ( $resp === false ) {
				//return array( 'error' => $error_msg );
			}

		}

		return array( 'msg' => __( 'Successfully saved!', 'wc-frontend-manager-ultimate' ) );

	}
	
	function fbc_count_messages( $cnv_id ) {
		global $wpdb;

		return $wpdb->get_var(
			$wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}wcfm_fbc_chat_rows WHERE conversation_id = %s", $cnv_id ) );

	}

	function fbc_send_chat_data_user( $cnv_id, $receive_copy, $user_email ) {
		
		$options = array();

		if ( apply_filters( 'wcfm_is_allow_fbc_chat_transcript_send', true ) && ( $receive_copy == 'true' || $receive_copy == '1' ) ) {

			$transcript_message = esc_html( __( 'Below you can find a copy of the chat conversation you have requested.', 'wc-frontend-manager-ultimate' ) );

			return $this->fbc_send_chat_data( $cnv_id, $user_email, $transcript_message );

		} else {
			return true;
		}

	}

	function fbc_send_chat_data_admin( $cnv_id, $chat_with, $closed_by ) {
		
		$options = array();

		if ( apply_filters( 'wcfm_is_allow_fbc_chat_transcript_send', true ) ) {

			if ( $chat_with == 'free' ) {
				$op_name = __( 'No operator has replied', 'wc-frontend-manager-ultimate' );
			} else {
				$op_id       = str_replace( 'fbc-op-', '', $chat_with );
				if( wcfm_is_vendor( $op_id ) ) {
					$op_nickname = wcfm_get_vendor_store_name( $op_id );
				} else {
					$op_nickname = get_the_author_meta( 'wcfm_fbc_operator_nickname', $op_id );
				}
				$op_name     = ( $op_nickname != '' ) ? $op_nickname : get_the_author_meta( 'nickname', $op_id );
			}

			$item          = $this->fbc_get_chat_info( $cnv_id );
			$default_email = get_option( 'admin_email' );

			$to        = $default_email;
			$message   = __( 'Below you can find a copy of the chat conversation', 'wc-frontend-manager-ultimate' );
			$chat_data = array(
				'operator'   => $op_name,
				'user_name'  => $item['user_name'],
				'user_ip'    => long2ip( $item['user_ip'] ),
				'user_email' => $item['user_email'],
				'duration'   => $item['duration'],
				'vendor_id'  => $item['vendor_id'],
				'evaluation' => ( $item['evaluation'] == '' ) ? __( 'Not received', 'wc-frontend-manager-ultimate' ) : ucfirst( $item['evaluation'] ),
				'closed_by'  => ( $closed_by == 'operator' ) ? __( 'Operator', 'wc-frontend-manager-ultimate' ) : __( 'User', 'wc-frontend-manager-ultimate' )
			);

			if ( wcfm_vendor_has_capability( $item['vendor_id'], 'chatbox' ) ) {
				$to .= $this->fbc_get_vendor_email( $item['vendor_id'] );
			}

			return $this->fbc_send_chat_data( $cnv_id, $to, $message, $chat_data, $item['user_name'] );
		} else {
			return true;
		}
	}
	
	function fbc_send_chat_data( $cnv_id, $mail_to, $mail_body, $chat_data = array(), $user = '' ) {
		global $WCFMu, $wpdb;
		
		$subject = __( 'Chat Conversation Copy', 'wc-frontend-manager-ultimate' ) . ( ( $user != '' ) ? ': ' . $user : '' );
		
		$chat_logs = $wpdb->get_results(
																		$wpdb->prepare( "
																										SELECT      a.message_id,
																																a.conversation_id,
																																a.user_id,
																																a.user_name,
																																a.msg,
																																a.msg_time,
																																IFNULL( b.user_type, 'operator' ) AS user_type
																										FROM        {$wpdb->prefix}wcfm_fbc_chat_rows a LEFT JOIN {$wpdb->prefix}wcfm_fbc_chat_visitors b ON a.user_id = b.user_id
																										WHERE       a.conversation_id = %s
																										ORDER BY    a.msg_time
																										", $cnv_id ), ARRAY_A );

		$args = array(
			'subject'   => $subject,
			'mail_body' => wp_strip_all_tags( $mail_body ),
			'cnv_id'    => $cnv_id,
			'chat_data' => $chat_data,
			'chat_logs' => $chat_logs
		);
		
		ob_start();

		$WCFMu->template->get_template( 'chatbox/emails/chat-copy.php', $args );

		$message    = ob_get_clean();
		
		$message = apply_filters( 'wcfm_email_content_wrapper', $message, $subject );
		
		$subject = '['.get_option( 'blogname' ).'] ' . $subject;
		$subject = apply_filters( 'wcfm_email_subject_wrapper', $subject );
		
		if( !defined( 'DOING_WCFM_EMAIL' ) ) 
		  define( 'DOING_WCFM_EMAIL', true );

		return wp_mail( $mail_to, $subject, $message );

	}
	
	/**
	 * Get chat info
	 *
	 * @since   1.0.0
	 *
	 * @param   $cnv_id
	 *
	 * @return  array
	 * @author  Alberto ruggiero
	 */
	function fbc_get_chat_info( $cnv_id ) {
		global $wpdb;

		return $wpdb->get_row(
			$wpdb->prepare( "
														SELECT      a.conversation_id,
																				a.user_id,
																				a.evaluation,
																				a.created_at,
																				a.duration,
																				a.receive_copy,
																				b.user_id,
																				b.user_type,
																				b.user_name,
																				b.user_ip,
																				b.user_email,
																				b.last_online,
																				b.vendor_id
														FROM        {$wpdb->prefix}wcfm_fbc_chat_sessions a LEFT JOIN {$wpdb->prefix}wcfm_fbc_chat_visitors b ON a.user_id = b.user_id
														WHERE       a.conversation_id = %s
														GROUP BY    a.conversation_id
														LIMIT       1
														", $cnv_id ), ARRAY_A );
	
	}

	function fbc_get_vendor_email( $vendor_id ) {

		$vendor_emails = ', ' . wcfm_get_vendor_store_email_by_vendor( $vendor_id );

		return $vendor_emails;

	}
	
	/**
	 * Chat Offline Message Delete
	 */
	function wcfm_chats_offline_delete() {
		global $WCFM, $WCFMu, $wpdb;
		
		$messageid = $_POST['messageid'];
		
		if( $messageid ) {
			$wpdb->query( "DELETE FROM {$wpdb->prefix}wcfm_fbc_offline_messages WHERE `id` = {$messageid}" );
		}
		
		echo '{"status" true, "message": "' . __( 'Deleted successfully', 'wc-frontend-manager-ultimate' ) . '" }';
		die;
	}
	
	/**
	 * Chat History Message Delete
	 */
	function wcfm_chats_history_delete() {
		global $WCFM, $WCFMu, $wpdb;
		
		$conversation = $_POST['conversation'];
		
		if( $conversation ) {
			$wpdb->query( "DELETE FROM {$wpdb->prefix}wcfm_fbc_chat_rows WHERE `conversation_id` = '{$conversation}'" );
			$wpdb->query( "DELETE FROM {$wpdb->prefix}wcfm_fbc_chat_sessions WHERE `conversation_id` = '{$conversation}'" );
		}
		
		echo '{"status" true, "message": "' . __( 'Deleted successfully', 'wc-frontend-manager-ultimate' ) . '" }';
		die;
	}

}