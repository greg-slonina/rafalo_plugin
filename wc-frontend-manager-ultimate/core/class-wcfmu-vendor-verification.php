<?php
/**
 * WCFMu plugin core
 *
 * Plugin Vendor Verification Controler
 *
 * @author 		WC Lovers
 * @package 	wcfmu/core
 * @version   3.3.1
 */
 
class WCFMu_Vendor_Verification {
	
	public function __construct() {
		global $WCFM, $WCFMu, $wp;
		
		// Verification Settings
		add_action( 'end_wcfm_settings', array( &$this, 'wcfmu_vendor_verification_settings' ), 18 );
		add_action( 'wcfm_settings_update', array( &$this, 'wcfmu_vendor_verification_settings_update' ), 18 );
		
		// Verification Profile Option
		if( wcfm_is_vendor() && apply_filters( 'wcfm_is_pref_profile', true ) && apply_filters( 'wcfm_is_allow_profile', true ) && apply_filters( 'wcfm_is_allow_vendor_verification', true ) ) {
			//$this->wcfm_hybridauth_init();
			//$this->wcfm_vendor_auth_requests();
			
			//add_action( 'end_wcfm_vendor_settings', array( &$this, 'wcfmu_vendor_verification_user_setting_block' ), 15 );
			add_action( 'wcfm_verification_product_limit_reached', array( &$this, 'wcfmu_verification_product_limit_reached_message' ), 15 );
			add_action( 'wcfm_vendor_setting_header_after', array( &$this, 'wcfmu_vendor_verification_user_setting_header' ), 15 );
			add_action( 'end_wcfm_user_profile', array( &$this, 'wcfmu_vendor_verification_user_profile_fields' ), 15 );
			add_action( 'wcfm_profile_update', array( &$this, 'wcfmu_vendor_verification_user_profile_meta_save' ), 15, 2 );
		}
		
		// Generate Verification Response Html
    add_action( 'wp_ajax_wcfmu_seller_verification_html', array( &$this, 'wcfmu_seller_verification_html' ) );
    
    // Update Verification Response
    add_action( 'wp_ajax_wcfmu_verification_response_update', array( &$this, 'wcfmu_verification_response_update' ) );
    
    // Vendor Manage Verification Data
		add_action( 'after_wcfm_vendor_membership_details', array( &$this, 'wcfmu_vendor_verification_manage' ), 9 );
		
		// Update Vendor Manage Verification Response
    add_action( 'wp_ajax_wcfmu_vendors_manage_verification', array( &$this, 'wcfmu_vendor_manage_verification_response_update' ) );
    
    // Vendor Verification Product Limit
    add_filter( 'wcfm_vendor_verification_product_limit', array( &$this, 'wcfmu_vendor_verification_product_limit' ) );
		
		// Verification direct message type
		add_filter( 'wcfm_message_types', array( &$this, 'wcfm_verification_message_types' ), 100 );
		
		// Show verified seller badge
		add_filter( 'wcfm_dashboard_after_username', array( &$this, 'after_wcfm_dashboard_user' ) );
		
		// Show verified seller badge before custom badges 
		add_action( 'before_wcfm_vendor_badges', array( &$this, 'show_verified_seller_badge' ), 10, 2 );
		
		if( $WCFMu->is_marketplace == 'wcpvendors' ) {
			add_filter( 'before_wcv_wcfm_vendor_badges', array( &$this, 'show_verified_seller_badge_by_name' ), 10, 3 );
		} elseif( $WCFMu->is_marketplace == 'dokan' ) {
			add_filter( 'before_dokan_wcfm_vendor_badges', array( &$this, 'show_verified_seller_badge_by_name' ), 10, 3 );
		}
	}
	
	function wcfm_hybridauth_init() {
		global $WCFM, $WCFMu, $wp;
		
		if ( !class_exists( 'Hybrid_Auth' ) ) {
			require_once $WCFMu->plugin_path . 'includes/libs/Hybrid/Auth.php';
		}
		if ( !class_exists( 'Hybrid_Endpoint' ) ) {
			require_once $WCFMu->plugin_path . 'includes/libs/Hybrid/Endpoint.php';
		}
  }
  
  function wcfm_vendor_auth_requests() {
		global $WCFM, $WCFMu;
		
		// Social Config
		$vendor_verification_options = get_option( 'wcfm_vendor_verification_options', array() );
		
		$verify_google_client_id = isset( $vendor_verification_options['verify_google_client_id'] ) ? $vendor_verification_options['verify_google_client_id'] : '';
		$verify_google_client_secret = isset( $vendor_verification_options['verify_google_client_secret'] ) ? $vendor_verification_options['verify_google_client_secret'] : '';
		
		$verify_facebook_client_id = isset( $vendor_verification_options['verify_facebook_client_id'] ) ? $vendor_verification_options['verify_facebook_client_id'] : '';
		$verify_facebook_client_secret = isset( $vendor_verification_options['verify_facebook_client_secret'] ) ? $vendor_verification_options['verify_facebook_client_secret'] : '';
		
		$verify_linkedin_client_id = isset( $vendor_verification_options['verify_linkedin_client_id'] ) ? $vendor_verification_options['verify_linkedin_client_id'] : '';
		$verify_linkedin_client_secret = isset( $vendor_verification_options['verify_linkedin_client_secret'] ) ? $vendor_verification_options['verify_linkedin_client_secret'] : '';
		
		$verify_twitter_client_id = isset( $vendor_verification_options['verify_twitter_client_id'] ) ? $vendor_verification_options['verify_twitter_client_id'] : '';
		$verify_twitter_client_secret = isset( $vendor_verification_options['verify_twitter_client_secret'] ) ? $vendor_verification_options['verify_twitter_client_secret'] : '';
		
		$wcfm_social_providers = array ( 
			 "Google"   => array(
								"enabled" => true,
								"keys"    => array( "id" => $verify_google_client_id, "secret" => $verify_google_client_secret ),
								"scope"   => "https://www.googleapis.com/auth/userinfo.profile "
						),
			 "Facebook" => array(
								"enabled" => true,
								"keys"    => array( "id" => $verify_facebook_client_id, "secret" => $verify_facebook_client_secret ),
								"scope"   => "email, public_profile, user_friends",
								"trustForwarded" => true
						),
			 "Twitter"  => array(
								"enabled" => true,
								"keys"    => array( "key" => $verify_twitter_client_id, "secret" => $verify_twitter_client_secret ),
						),
			 "LinkedIn" => array(
								"enabled" => true,
								"keys"    => array( "key" => $verify_linkedin_client_id, "secret" => $verify_linkedin_client_secret ),
						)
		);
		
		$wcfm_social_config = array(   "base_url" 	=> get_wcfm_profile_url(),
																	 "debug_mode" => false ,
																	 "debug_file" => $WCFMu->plugin_path . "includes/libs/Hybrid/hybridauth.log",
																	 "providers"  => $wcfm_social_providers
																	 );

		$hybridauth = new Hybrid_Auth( $wcfm_social_config );
		$params = array( 'hauth_return_to' => get_wcfm_profile_url() );

		if(isset($_REQUEST['hauth_start'])) {
			Hybrid_Endpoint::process();
		}

		if(isset($_REQUEST['hauth_done'])) {
			if(isset($_REQUEST['code']) || isset($_REQUEST['oauth_token'])) {
				Hybrid_Endpoint::process();
			} elseif(isset($_REQUEST['oauth_problem']) || isset($_REQUEST['denied']) || isset($_REQUEST['error']) || isset($_REQUEST['error_code'])) {
				if(isset($params['hauth_return_to']))
    		  $hybridauth->redirect( $params['hauth_return_to'] );
			}
		}

		if(isset($_REQUEST['auth_out'])) {
    	$provider_logout = ucfirst( sanitize_text_field( $_GET['auth_out'] ) );
    	try {
				$adapter = $hybridauth->authenticate( $provider_logout, $params );
				$vendor_id   = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );
				$vendor_verification_data = (array) get_user_meta( $vendor_id, 'wcfm_vendor_verification_data', true );
				if( isset( $vendor_verification_data['social'] ) && isset( $vendor_verification_data['social'][$provider_logout] ) ) { 
					unset( $vendor_verification_data['social'][$provider_logout] ); 
				}
				update_user_meta( $vendor_id, 'wcfm_vendor_verification_data', $vendor_verification_data );
				$adapter->logout();
			} catch( Exception $e ) {
				wc_add_notice($e->getMessage(), 'error');
			}
    }

		if(isset($_REQUEST['hybridauth'])) {

	  }

		if(isset($_REQUEST['auth_in'])) {
      $provider = ucfirst( sanitize_text_field( $_GET['auth_in'] ) );
      		
			if(!empty($provider)) {
				try {
					$adapter = $hybridauth->authenticate( $provider );
					$user_profile = $adapter->getUserProfile();
					$vendor_id   = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );
					$vendor_verification_data = (array) get_user_meta( $vendor_id, 'wcfm_vendor_verification_data', true );
					$vendor_verification_data['social'][$provider] = (array) $user_profile;
					update_user_meta( $vendor_id, 'wcfm_vendor_verification_data', $vendor_verification_data );
					if(isset($params['hauth_return_to']))
						$hybridauth->redirect( $params['hauth_return_to'] );
	      } catch( Exception $e ) {
					wc_add_notice( $e->getMessage(), 'error' );
				}
			}
		}
	}
	
	function wcfmu_vendor_verification_settings( $wcfm_options ) {
		global $WCFM, $WCFMu;
		
		$vendor_verification_options = get_option( 'wcfm_vendor_verification_options', array() );
		
		$verification_badge = isset( $vendor_verification_options['verification_badge'] ) ? $vendor_verification_options['verification_badge'] : '';
		if( !$verification_badge ) $verification_badge = $WCFMu->plugin_url . 'assets/images/verification_badge.png';
		
		$verification_product_limit = isset( $vendor_verification_options['verification_product_limit'] ) ? $vendor_verification_options['verification_product_limit'] : '';
		
		$wcfm_vendor_verification_identity_types = $this->wcfm_vendor_verification_identity_types();
		
		$verify_by_google = isset( $vendor_verification_options['verify_by_google'] ) ? $vendor_verification_options['verify_by_google'] : 'no';
		$verify_google_redirect_url = add_query_arg( 'hauth.done', 'Google', get_wcfm_profile_url() );
		$verify_google_client_id = isset( $vendor_verification_options['verify_google_client_id'] ) ? $vendor_verification_options['verify_google_client_id'] : '';
		$verify_google_client_secret = isset( $vendor_verification_options['verify_google_client_secret'] ) ? $vendor_verification_options['verify_google_client_secret'] : '';
		
		$verify_by_facebook = isset( $vendor_verification_options['verify_by_facebook'] ) ? $vendor_verification_options['verify_by_facebook'] : 'no';
		$verify_facebook_redirect_url = add_query_arg( 'hauth.done', 'Facebook', get_wcfm_profile_url() );
		$verify_facebook_client_id = isset( $vendor_verification_options['verify_facebook_client_id'] ) ? $vendor_verification_options['verify_facebook_client_id'] : '';
		$verify_facebook_client_secret = isset( $vendor_verification_options['verify_facebook_client_secret'] ) ? $vendor_verification_options['verify_facebook_client_secret'] : '';
		
		$verify_by_linkedin = isset( $vendor_verification_options['verify_by_linkedin'] ) ? $vendor_verification_options['verify_by_linkedin'] : 'no';
		$verify_linkedin_redirect_url = add_query_arg( 'hauth.done', 'LinkedIn', get_wcfm_profile_url() );
		$verify_linkedin_client_id = isset( $vendor_verification_options['verify_linkedin_client_id'] ) ? $vendor_verification_options['verify_linkedin_client_id'] : '';
		$verify_linkedin_client_secret = isset( $vendor_verification_options['verify_linkedin_client_secret'] ) ? $vendor_verification_options['verify_linkedin_client_secret'] : '';
		
		$verify_by_twitter = isset( $vendor_verification_options['verify_by_twitter'] ) ? $vendor_verification_options['verify_by_twitter'] : 'no';
		$verify_twitter_redirect_url = add_query_arg( 'hauth.done', 'Twitter', get_wcfm_profile_url() );
		$verify_twitter_client_id = isset( $vendor_verification_options['verify_twitter_client_id'] ) ? $vendor_verification_options['verify_twitter_client_id'] : '';
		$verify_twitter_client_secret = isset( $vendor_verification_options['verify_twitter_client_secret'] ) ? $vendor_verification_options['verify_twitter_client_secret'] : '';
		?>
		<!-- collapsible -->
		<div class="page_collapsible" id="wcfm_settings_form_vendor_verification_head">
			<label class="fab fa-angellist"></label>
			<?php echo '&nbsp;&nbsp;&nbsp;' . apply_filters( 'wcfm_sold_by_label', '', __( 'Vendor', 'wc-frontend-manager' ) ) . ' ' . __('Verification', 'wc-frontend-manager-ultimate'); ?><span></span>
		</div>
		<div class="wcfm-container">
			<div id="wcfm_settings_form_vendor_verification_expander" class="wcfm-content">
			  <h2><?php echo apply_filters( 'wcfm_sold_by_label', '', __( 'Vendor', 'wc-frontend-manager' ) ) . ' ' . __('Verification', 'wc-frontend-manager-ultimate'); ?></h2>
				<?php wcfm_video_tutorial( 'https://wclovers.com/knowledgebase/wcfm-vendor-verification/' ); ?>
				<div class="wcfm_clearfix"></div>
				<?php
					$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfmu_settings_fields_vendor_verification_general', array(
																																																"verification_badge"         => array('label' => __('Verification Badge', 'wc-frontend-manager-ultimate'), 'name' => 'wcfm_vendor_verification_options[verification_badge]', 'type' => 'upload', 'class' => 'wcfm_ele', 'prwidth' => 64, 'label_class' => 'wcfm_title wcfm_ele', 'hints' => __( 'Upload badge image 32x32 size for best view.', '' ), 'value' => $verification_badge ),
																																																"verification_product_limit" => array('label' => __('Product Limit Restriction', 'wc-frontend-manager-ultimate'), 'name' => 'wcfm_vendor_verification_options[verification_product_limit]', 'type' => 'number', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'hints' => __( 'This limit will be applicable till vendor profile not verified.', '' ), 'placeholder' => __( 'As per capability', '' ), 'value' => $verification_product_limit )
																																																) ) );
					
					$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfmu_settings_fields_vendor_verification_identity_types', array(
																																										"wcfm_vendor_verification_identity_types" => array('label' => __('Identity Types', 'wc-frontend-manager-ultimate') , 'type' => 'multiinput', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title', 'value' => $wcfm_vendor_verification_identity_types, 'options' => array(
																																																"is_active"     => array( 'label' => __('Enable', 'wc-frontend-manager-ultimate'), 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'label_class' => 'wcfm_title checkbox_title wcfm_ele', 'value' => 'yes' ),
																																																"identity_name" => array( 'label' => __('Identity Name', 'wc-frontend-manager-ultimate'), 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele' ),
																																																"identity_id"   => array( 'type' => 'hidden' )
																																											) ) ) ) );
			  ?>
			  
			  <div class="wcfm_clearfix"></div><h2>Google</h2><div class="wcfm_clearfix"></div>
			  <p><?php printf( _x( 'Generate your Client ID & Secret Key from %s.', 'wc-frontend-manager-ultimate' ), '<a target="_blank" href="https://console.developers.google.com/project">here</a>' ); ?></p>
				<?php
					$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfmu_settings_fields_vendor_verification_google', array(
																																																"verify_by_google" => array('label' => __('Enable', 'wc-frontend-manager-ultimate'), 'name' => 'wcfm_vendor_verification_options[verify_by_google]', 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'label_class' => 'wcfm_title checkbox_title wcfm_ele', 'value' => 'yes', 'dfvalue' => $verify_by_google ),
																																																"verify_google_redirect_url" => array('label' => __('Redirect URL', 'wc-frontend-manager-ultimate'), 'name' => 'wcfm_vendor_verification_options[verify_google_redirect_url]', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'hints' => __( 'User will be redirect to the URL after successful authentication.', 'wc-frontend-manager-ultimate' ), 'attributes' => array( 'readonly' => true ), 'value' => $verify_google_redirect_url ),
																																																"verify_google_client_id" => array('label' => __('Client ID', 'wc-frontend-manager-ultimate'), 'name' => 'wcfm_vendor_verification_options[verify_google_client_id]', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'hints' => __( 'App generated Client ID required.', 'wc-frontend-manager-ultimate' ), 'value' => $verify_google_client_id ),
																																																"verify_google_client_secret" => array('label' => __('Client Secret', 'wc-frontend-manager-ultimate'), 'name' => 'wcfm_vendor_verification_options[verify_google_client_secret]', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'hints' => __( 'App generated Client Secret key required.', 'wc-frontend-manager-ultimate' ), 'value' => $verify_google_client_secret ),
																																																) ) );
				?>
				
				<div class="wcfm_clearfix"></div><h2>Facebook</h2><div class="wcfm_clearfix"></div>
				<p><?php printf( _x( 'Generate your Client ID & Secret Key from %s.', 'wc-frontend-manager-ultimate' ), '<a target="_blank" href="https://developers.facebook.com/apps/">here</a>' ); ?></p>
				<?php
					$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfmu_settings_fields_vendor_verification_facebook', array(
																																																"verify_by_facebook" => array('label' => __('Enable', 'wc-frontend-manager-ultimate'), 'name' => 'wcfm_vendor_verification_options[verify_by_facebook]', 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'label_class' => 'wcfm_title checkbox_title wcfm_ele', 'value' => 'yes', 'dfvalue' => $verify_by_facebook ),
																																																"verify_facebook_redirect_url" => array('label' => __('Redirect URL', 'wc-frontend-manager-ultimate'), 'name' => 'wcfm_vendor_verification_options[verify_facebook_redirect_url]', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'hints' => __( 'User will be redirect to the URL after successful authentication.', 'wc-frontend-manager-ultimate' ), 'attributes' => array( 'readonly' => true ), 'value' => $verify_facebook_redirect_url ),
																																																"verify_facebook_client_id" => array('label' => __('Client ID', 'wc-frontend-manager-ultimate'), 'name' => 'wcfm_vendor_verification_options[verify_facebook_client_id]', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'hints' => __( 'App generated Client ID required.', 'wc-frontend-manager-ultimate' ), 'value' => $verify_facebook_client_id ),
																																																"verify_facebook_client_secret" => array('label' => __('Client Secret', 'wc-frontend-manager-ultimate'), 'name' => 'wcfm_vendor_verification_options[verify_facebook_client_secret]', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'hints' => __( 'App generated Client Secret key required.', 'wc-frontend-manager-ultimate' ), 'value' => $verify_facebook_client_secret ),
																																																) ) );
				?>
				
				<div class="wcfm_clearfix"></div><h2>LinkedIn</h2><div class="wcfm_clearfix"></div>
				<p><?php printf( _x( 'Generate your Client ID & Secret Key from %s.', 'wc-frontend-manager-ultimate' ), '<a target="_blank" href="https://www.linkedin.com/developer/apps">here</a>' ); ?></p>
				<?php
					$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfmu_settings_fields_vendor_verification_linkedin', array(
																																																"verify_by_linkedin" => array('label' => __('Enable', 'wc-frontend-manager-ultimate'), 'name' => 'wcfm_vendor_verification_options[verify_by_linkedin]', 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'label_class' => 'wcfm_title checkbox_title wcfm_ele', 'value' => 'yes', 'dfvalue' => $verify_by_linkedin ),
																																																"verify_linkedin_redirect_url" => array('label' => __('Redirect URL', 'wc-frontend-manager-ultimate'), 'name' => 'wcfm_vendor_verification_options[verify_linkedin_redirect_url]', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'hints' => __( 'User will be redirect to the URL after successful authentication.', 'wc-frontend-manager-ultimate' ), 'attributes' => array( 'readonly' => true ), 'value' => $verify_linkedin_redirect_url ),
																																																"verify_linkedin_client_id" => array('label' => __('Client ID', 'wc-frontend-manager-ultimate'), 'name' => 'wcfm_vendor_verification_options[verify_linkedin_client_id]', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'hints' => __( 'App generated Client ID required.', 'wc-frontend-manager-ultimate' ), 'value' => $verify_linkedin_client_id ),
																																																"verify_linkedin_client_secret" => array('label' => __('Client Secret', 'wc-frontend-manager-ultimate'), 'name' => 'wcfm_vendor_verification_options[verify_linkedin_client_secret]', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'hints' => __( 'App generated Client Secret key required.', 'wc-frontend-manager-ultimate' ), 'value' => $verify_linkedin_client_secret ),
																																																) ) );
				?>
				
				<div class="wcfm_clearfix"></div><h2>Twitter</h2><div class="wcfm_clearfix"></div>
				<p><?php printf( _x( 'Generate your Consumer Key & Consumer Secret from %s.', 'wc-frontend-manager-ultimate' ), '<a target="_blank" href="https://apps.twitter.com/">here</a>' ); ?></p>
				<?php
					$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfmu_settings_fields_vendor_verification_twitter', array(
																																																"verify_by_twitter" => array('label' => __('Enable', 'wc-frontend-manager-ultimate'), 'name' => 'wcfm_vendor_verification_options[verify_by_twitter]', 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'label_class' => 'wcfm_title checkbox_title wcfm_ele', 'value' => 'yes', 'dfvalue' => $verify_by_twitter ),
																																																"verify_twitter_redirect_url" => array('label' => __('Redirect URL', 'wc-frontend-manager-ultimate'), 'name' => 'wcfm_vendor_verification_options[verify_twitter_redirect_url]', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'hints' => __( 'User will be redirect to the URL after successful authentication.', 'wc-frontend-manager-ultimate' ), 'attributes' => array( 'readonly' => true ), 'value' => $verify_twitter_redirect_url ),
																																																"verify_twitter_client_id" => array('label' => __('Consumer key', 'wc-frontend-manager-ultimate'), 'name' => 'wcfm_vendor_verification_options[verify_twitter_client_id]', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'hints' => __( 'App generated Consumer Key required.', 'wc-frontend-manager-ultimate' ), 'value' => $verify_twitter_client_id ),
																																																"verify_twitter_client_secret" => array('label' => __('Consumer Secret', 'wc-frontend-manager-ultimate'), 'name' => 'wcfm_vendor_verification_options[verify_twitter_client_secret]', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'hints' => __( 'App generated Consumer Secret required.', 'wc-frontend-manager-ultimate' ), 'value' => $verify_twitter_client_secret ),
																																																) ) );
				?>
			</div>
		</div>
		<div class="wcfm_clearfix"></div>
		<!-- end collapsible -->
		
		<?php
		
	}
	
	function wcfmu_vendor_verification_settings_update( $wcfm_settings_form ) {
		global $WCFM, $WCFMu, $_POST;
		
		if( isset( $wcfm_settings_form['wcfm_vendor_verification_options'] ) ) {
			$wcfm_vendor_verification_options = $wcfm_settings_form['wcfm_vendor_verification_options'];
			update_option( 'wcfm_vendor_verification_options',  $wcfm_vendor_verification_options );
		} else {
			update_option( 'wcfm_vendor_verification_options',  array() );
		}
		
		if( isset( $wcfm_settings_form['wcfm_vendor_verification_identity_types'] ) ) {
			$wcfm_vendor_verification_identity_types = $wcfm_settings_form['wcfm_vendor_verification_identity_types'];
			$wcfmvv_identity_types = array();
			if( !empty( $wcfm_vendor_verification_identity_types ) ) {
				foreach( $wcfm_vendor_verification_identity_types as $wcfm_vendor_verification_identity_type ) {
					if( !empty( $wcfm_vendor_verification_identity_type['identity_name'] ) ) {
						if( empty( $wcfm_vendor_verification_identity_type['identity_id'] ) ) {
							$wcfm_vendor_verification_identity_type['identity_id'] = rand( 100000, 999999999 ); //sanitize_title( $wcfm_vendor_verification_identity_type['identity_name'] );
						}
						$wcfmvv_identity_types[] = $wcfm_vendor_verification_identity_type;
					}
				}
			}
			update_option( 'wcfm_vendor_verification_identity_types',  $wcfmvv_identity_types );
		} else {
			update_option( 'wcfm_vendor_verification_identity_types',  array() );
		}
	}
	
	public function wcfm_vendor_verification_identity_types() {
		$default_ids = array(
													array(
														     'is_active'      => 'yes',
														     'identity_name'  => __('National ID Card', 'wc-frontend-manager-ultimate'),
														     'identity_id'    => 'national_id'
														    ),
													array(
														     'is_active'      => 'yes',
														     'identity_name'  => __('Business Card', 'wc-frontend-manager-ultimate'),
														     'identity_id'    => 'business_card'
														    ),
													array(
														     'is_active'      => 'yes',
														     'identity_name'  => __('Passport', 'wc-frontend-manager-ultimate'),
														     'identity_id'    => 'passport'
														    ),
													array(
														     'is_active'      => 'yes',
														     'identity_name'  => __('Driver\'s License', 'wc-frontend-manager-ultimate'),
														     'identity_id'    => 'driving_license'
														    )
			);
		$wcfm_vendor_verification_identity_types = get_option( 'wcfm_vendor_verification_identity_types',  $default_ids );
		
		return apply_filters( 'wcfm_vendor_verification_identity_types', $wcfm_vendor_verification_identity_types );
	}
	
	function wcfmu_vendor_verification_user_setting_block( $user_id ) {
		?>
		<a href="<?php echo get_wcfm_profile_url(); ?>#sm_profile_manage_form_verification_head" class="page_collapsible page_collapsible_dummy"><label class="fab fa-angellist"></label><?php _e( 'Verification', 'wc-frontend-manager-ultimate' ); ?><span></span></a>
		<div class="wcfm-container">
			<div id="wcfm_profile_manage_form_verification_expander" class="wcfm-content"></div>
		</div>
		<?php
	}
	
	function wcfmu_verification_product_limit_reached_message() {
		printf( __( '%sVerify your profile to add more products >>%s', 'wc-frontend-manager-ultimate' ), '<a style="text-decoration: underline; margin-left: 10px; color: #00897b;" href="'. apply_filters( 'wcfm_verify_profile_url', get_wcfm_profile_url().'#sm_profile_manage_form_verification_head' ) .'">', '</a>' );
	}
	
	function wcfmu_vendor_verification_user_setting_header( $user_id ) {
		echo '<a id="wcfm_verification_settings" class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_profile_url().'#sm_profile_manage_form_verification_head" data-tip="' . __( 'Verification', 'wc-frontend-manager-ultimate' ) . '"><span class="fab fa-angellist"></span><span class="text">' . __( 'Verification', 'wc-frontend-manager-ultimate' ) . '</span></a>';
	}
	
	function wcfmu_vendor_verification_user_profile_fields() {
		global $WCFM, $WCFMu;
		
		$vendor_id   = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );
		$vendor_verification_data = get_user_meta( $vendor_id, 'wcfm_vendor_verification_data', true );
		if( !$vendor_verification_data ) $vendor_verification_data = array();
		
		$verification_status = 'noprompt';
		if( !empty( $vendor_verification_data ) && isset( $vendor_verification_data['verification_status'] ) ) $verification_status = $vendor_verification_data['verification_status'];
		
		
		$vendor_verification_options = (array) get_option( 'wcfm_vendor_verification_options' );
		
		$verification_badge = isset( $vendor_verification_options['verification_badge'] ) ? $vendor_verification_options['verification_badge'] : '';
		if( !$verification_badge ) $verification_badge = $WCFMu->plugin_url . 'assets/images/verification_badge.png';
		
		$verify_by_google = isset( $vendor_verification_options['verify_by_google'] ) ? $vendor_verification_options['verify_by_google'] : '';
		$verify_google_client_id = isset( $vendor_verification_options['verify_google_client_id'] ) ? $vendor_verification_options['verify_google_client_id'] : '';
		$verify_google_client_secret = isset( $vendor_verification_options['verify_google_client_secret'] ) ? $vendor_verification_options['verify_google_client_secret'] : '';
		
		$verify_by_facebook = isset( $vendor_verification_options['verify_by_facebook'] ) ? $vendor_verification_options['verify_by_facebook'] : '';
		$verify_facebook_client_id = isset( $vendor_verification_options['verify_facebook_client_id'] ) ? $vendor_verification_options['verify_facebook_client_id'] : '';
		$verify_facebook_client_secret = isset( $vendor_verification_options['verify_facebook_client_secret'] ) ? $vendor_verification_options['verify_facebook_client_secret'] : '';
		
		$verify_by_linkedin = isset( $vendor_verification_options['verify_by_linkedin'] ) ? $vendor_verification_options['verify_by_linkedin'] : '';
		$verify_linkedin_client_id = isset( $vendor_verification_options['verify_linkedin_client_id'] ) ? $vendor_verification_options['verify_linkedin_client_id'] : '';
		$verify_linkedin_client_secret = isset( $vendor_verification_options['verify_linkedin_client_secret'] ) ? $vendor_verification_options['verify_linkedin_client_secret'] : '';
		
		$verify_by_twitter = isset( $vendor_verification_options['verify_by_twitter'] ) ? $vendor_verification_options['verify_by_twitter'] : '';
		$verify_twitter_client_id = isset( $vendor_verification_options['verify_twitter_client_id'] ) ? $vendor_verification_options['verify_twitter_client_id'] : '';
		$verify_twitter_client_secret = isset( $vendor_verification_options['verify_twitter_client_secret'] ) ? $vendor_verification_options['verify_twitter_client_secret'] : '';
		
		$verification_note = isset( $vendor_verification_data['verification_note'] ) ? $vendor_verification_data['verification_note'] : '';
		
		$is_social_verification_enabled = false;
		if( $verify_by_google || $verify_by_facebook || $verify_by_linkedin || $verify_by_twitter ) { $is_social_verification_enabled = true; }
		
		// Check Social Pending
		if( $is_social_verification_enabled ) {
			if( $verification_status == 'approve' ) {
				if( !isset( $vendor_verification_data['social'] ) ) {
					$verification_status = 'social_pending';
				} else {

					if( $verify_by_google && !isset( $vendor_verification_data['social']['Google'] ) ) $verification_status = 'social_pending';
					
					elseif( $verify_by_twitter && !isset( $vendor_verification_data['social']['Twitter'] ) ) $verification_status = 'social_pending';
					elseif( $verify_by_linkedin && !isset( $vendor_verification_data['social']['Linkedin'] ) ) $verification_status = 'social_pending';
					elseif( $verify_by_facebook && !isset( $vendor_verification_data['social']['Facebook'] ) ) $verification_status = 'social_pending';
					else {
						$vendor_verification_data['social_verification_status'] = 'approve';
						update_user_meta( $vendor_id, 'wcfm_vendor_verification_data', $vendor_verification_data );
					}
				}
			}
		} else {
			$vendor_verification_data['social_verification_status'] = 'approve';
			update_user_meta( $vendor_id, 'wcfm_vendor_verification_data', $vendor_verification_data );
		}
		
		?>
		<div class="page_collapsible profile_manage_verification" id="sm_profile_manage_form_verification_head"><label class="fab fa-angellist"></label>&nbsp;<?php _e( 'Verification', 'wc-frontend-manager-ultimate' ); ?><span></span></div>
		<div class="wcfm-container">
			<div id="wcfm_profile_manage_form_verification_expander" class="wcfm-content">
				
				<?php
				$this->show_verification_status_message( $verification_status );
				
				if( $verification_status != 'pending' ) {
					
					if( $verification_note ) {
						echo '<div class="verification_status_note"><span class="wcfmfa fa-sticky-note"></span><span>' . __( 'Admin Note', 'wc-frontend-manager-ultimate' ) . ': ' . $verification_note . '</span></div><br />';
					}
					
					if( $verification_status != 'social_pending' ) {
						$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_vendor_verification_prompt_fields', array(
																																"prompt_verify" => array( 'label' => __( 'Prompt Verify', 'wc-frontend-manager-ultimate'), 'name' => 'vendor_verification[prompt_verify]', 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'label_class' => 'wcfm_title checkbox_title wcfm_ele', 'value' => 'yes', 'hints' => __( 'Check this to submit your verification data to admin.', 'wc-frontend-manager-ultimate') )
																														) ) );
					}
					?>
				
					<?php if( apply_filters( 'wcfm_is_allow_vendor_identity_verification', true ) ) { ?>
						<h2><?php _e( 'Identity Verification', 'wc-frontend-manager-ultimate' ); ?></h2><br />
						<div class="wcfm_clearfix"></div>
						<?php
						$identity_types = $this->wcfm_vendor_verification_identity_types();
						if( !empty( $identity_types ) ) {
							foreach( $identity_types as $identity_type ) {
								if( isset( $identity_type['is_active'] ) ) {
									$identity_type_value = '';
									if( !empty( $vendor_verification_data ) && isset( $vendor_verification_data['identity'] ) && isset( $vendor_verification_data['identity'][$identity_type['identity_id']] ) ) $identity_type_value = $vendor_verification_data['identity'][$identity_type['identity_id']];
									$WCFM->wcfm_fields->wcfm_generate_form_field( array( 
																																		$identity_type['identity_id'] => array( 'label' => __( $identity_type['identity_name'], 'wc-frontend-manager-ultimate' ), 'name' => 'vendor_verification[identity]['.$identity_type['identity_id'].']', 'type' => 'upload', 'mime' => 'Uploads', 'class' => 'wcfm-text', 'label_class' => 'wcfm_title', 'prwidth' => 32, 'value' => $identity_type_value ) 
																																		) );
								}
							}
						}
						?>
						<div class="wcfm_clearfix"></div><br />
					<?php } ?>
					
					<?php if( apply_filters( 'wcfm_is_allow_vendor_address_verification', true ) ) { ?>
						<h2><?php _e( 'Address Verification', 'wc-frontend-manager-ultimate' ); ?></h2><br />
						<div class="wcfm_clearfix"></div>
						<?php
						$street_1 = isset( $vendor_verification_data['address']['street_1'] ) ? $vendor_verification_data['address']['street_1'] : '';
						$street_2 = isset( $vendor_verification_data['address']['street_2'] ) ? $vendor_verification_data['address']['street_2'] : '';
						$city    = isset( $vendor_verification_data['address']['city'] ) ? $vendor_verification_data['address']['city'] : '';
						$zip     = isset( $vendor_verification_data['address']['zip'] ) ? $vendor_verification_data['address']['zip'] : '';
						$country = isset( $vendor_verification_data['address']['country'] ) ? $vendor_verification_data['address']['country'] : '';
						$state   = isset( $vendor_verification_data['address']['state'] ) ? $vendor_verification_data['address']['state'] : '';
						
						if( !$street_1 || !$country ) {
							$street_1  = get_user_meta( $vendor_id, 'billing_address_1', true );
							$street_2  = get_user_meta( $vendor_id, 'billing_address_2', true );
							$country   = get_user_meta( $vendor_id, 'billing_country', true );
							$city      = get_user_meta( $vendor_id, 'billing_city', true );
							$state     = get_user_meta( $vendor_id, 'billing_state', true );
							$zip       = get_user_meta( $vendor_id, 'billing_postcode', true );
						}
						
						$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_vendor_verification_profile_fields_address', array(
																																																		"vstreet_1" => array('label' => __('Street', 'wc-frontend-manager'), 'placeholder' => __('Street address', 'wc-frontend-manager'), 'name' => 'vendor_verification[address][street_1]', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $street_1 ),
																																																		"vstreet_2" => array('label' => __('Street 2', 'wc-frontend-manager'), 'placeholder' => __('Apartment, suite, unit etc. (optional)', 'wc-frontend-manager'), 'name' => 'vendor_verification[address][street_2]', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $street_2 ),
																																																		"vcity" => array('label' => __('City/Town', 'wc-frontend-manager'), 'placeholder' => __('Town / City', 'wc-frontend-manager'), 'name' => 'vendor_verification[address][city]', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $city ),
																																																		"vzip" => array('label' => __('Postcode/Zip', 'wc-frontend-manager'), 'placeholder' => __('Postcode / Zip', 'wc-frontend-manager'), 'name' => 'vendor_verification[address][zip]', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $zip ),
																																																		"vcountry" => array('label' => __('Country', 'wc-frontend-manager'), 'name' => 'vendor_verification[address][country]', 'attributes' => array( 'style' => 'width: 60%;' ), 'type' => 'country', 'class' => 'wcfm-select wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $country ),
																																																		"vstate" => array('label' => __('State/County', 'wc-frontend-manager'), 'name' => 'vendor_verification[address][state]', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $state ),
																																																		) ) );
						?>
						<div class="wcfm_clearfix"></div><br />
					<?php } ?>
				<?php } ?>
				
				<?php if( $is_social_verification_enabled ) { ?>
					<?php if( apply_filters( 'wcfm_is_allow_vendor_social_verification', true ) ) { ?>
						<h2><?php _e( 'Social Verification', 'wc-frontend-manager-ultimate' ); ?></h2><br />
						<div class="wcfm_clearfix"></div>
						
						<div class="wcfm_social_buttons">
							<?php
							// Google
							if( $verify_by_google && apply_filters( 'wcfm_is_allow_vendor_social_verification_google', true ) ) {
								$google_verification_data = isset( $vendor_verification_data['social']['Google'] ) ? $vendor_verification_data['social']['Google'] : '';
								if( is_array( $google_verification_data ) && count( $google_verification_data ) > 1 ) { 
									?>
									<div class="box">
										<div class="box-icon">
											<a href="<?php echo $google_verification_data['profileURL']; ?>"><img src="<?php echo $google_verification_data['photoURL']; ?>" /></a>
											<span class="social_ico"><img src="<?php echo $WCFMu->plugin_url . 'assets/images/google.png'; ?>"/></span>
										</div>
										<div class="info">
											<h4 class="put_the_name"><?php echo $google_verification_data['displayName']; ?></h4>
											<a class="btn" href="<?php echo add_query_arg( 'auth_out', 'google', get_wcfm_profile_url() ); ?>"><?php echo _e( 'Logout', 'wc-frontend-manager-ultimate' ); ?></a>
											<div class="wcfm_clearfix"></div>
											<p><?php echo wp_trim_words( $google_verification_data['description'], 20, '...' ); ?></p>
										</div>
									</div>
									<?php
								} elseif( $verify_google_client_id && $verify_google_client_secret ) {
									?>
									<a href="<?php echo add_query_arg( 'auth_in', 'google', get_wcfm_profile_url() ); ?>" class="social_btn_cnnct button GoogleConnect rounded large">
										<em><img src="<?php echo $WCFMu->plugin_url . 'assets/images/google.png'; ?>" style="height: 95%;"/></em>
										<span class="buttonText"><?php echo __( 'Connect to', 'wc-frontend-manager-ultimate' ). ' Google'; ?></span>
									</a>
									<?php
								}
							}
							
							// Linkedin
							if( $verify_by_linkedin && apply_filters( 'wcfm_is_allow_vendor_social_verification_linkedin', true ) ) {
								$linkedin_verification_data = isset( $vendor_verification_data['social']['Linkedin'] ) ? $vendor_verification_data['social']['Linkedin'] : '';
								if( is_array( $linkedin_verification_data ) && count( $linkedin_verification_data ) > 1 ) { 
									?>
									<div class="box">
										<div class="box-icon">
											<a href="<?php echo $linkedin_verification_data['profileURL']; ?>"><img src="<?php echo $linkedin_verification_data['photoURL']; ?>" /></a>
											<span class="social_ico"><img src="<?php echo $WCFMu->plugin_url . 'assets/images/linkedin.png'; ?>"/></span>
										</div>
										<div class="info">
											<h4 class="put_the_name"><?php echo $linkedin_verification_data['displayName']; ?></h4>
											<a class="btn" href="<?php echo add_query_arg( 'auth_out', 'linkedin', get_wcfm_profile_url() ); ?>"><?php echo _e( 'Logout', 'wc-frontend-manager-ultimate' ); ?></a>
											<div class="wcfm_clearfix"></div>
											<p><?php echo wp_trim_words( $linkedin_verification_data['description'], 20, '...' ); ?></p>
										</div>
									</div>
									<?php
								} elseif( $verify_linkedin_client_id && $verify_linkedin_client_secret ) {
									?>
									<a href="<?php echo add_query_arg( 'auth_in', 'linkedin', get_wcfm_profile_url() ); ?>" class="social_btn_cnnct button LinkedInConnect rounded large">
										<em><img src="<?php echo $WCFMu->plugin_url . 'assets/images/linkedin.png'; ?>" style="height: 95%;"/></em>
										<span class="buttonText"><?php echo __( 'Connect to', 'wc-frontend-manager-ultimate' ). ' LinkedIn'; ?></span>
									</a>
									<?php
								}
							}
							
							// Facebook
							if( $verify_by_facebook && apply_filters( 'wcfm_is_allow_vendor_social_verification_facebook', true ) ) {
								$facebook_verification_data = isset( $vendor_verification_data['social']['Facebook'] ) ? $vendor_verification_data['social']['Facebook'] : '';
								if( is_array( $facebook_verification_data ) && count( $facebook_verification_data ) > 1 ) { 
									?>
									<div class="box">
										<div class="box-icon">
											<a href="<?php echo $facebook_verification_data['profileURL']; ?>"><img src="<?php echo $facebook_verification_data['photoURL']; ?>" /></a>
											<span class="social_ico"><img src="<?php echo $WCFMu->plugin_url . 'assets/images/facebook.png'; ?>"/></span>
										</div>
										<div class="info">
											<h4 class="put_the_name"><?php echo $facebook_verification_data['displayName']; ?></h4>
											<a class="btn" href="<?php echo add_query_arg( 'auth_out', 'facebook', get_wcfm_profile_url() ); ?>"><?php echo _e( 'Logout', 'wc-frontend-manager-ultimate' ); ?></a>
											<div class="wcfm_clearfix"></div>
											<p><?php echo wp_trim_words( $facebook_verification_data['description'], 20, '...' ); ?></p>
										</div>
									</div>
									<?php
								} elseif( $verify_facebook_client_id && $verify_facebook_client_secret ) {
									?>
									<a href="<?php echo add_query_arg( 'auth_in', 'facebook', get_wcfm_profile_url() ); ?>" class="social_btn_cnnct button FacebookConnect rounded large">
										<em><img src="<?php echo $WCFMu->plugin_url . 'assets/images/facebook.png'; ?>" style="height: 95%;"/></em>
										<span class="buttonText"><?php echo __( 'Connect to', 'wc-frontend-manager-ultimate' ). ' Facebook'; ?></span>
									</a>
									<?php
								}
							}
							
							// Twitter
							if( $verify_by_twitter && apply_filters( 'wcfm_is_allow_vendor_social_verification_twitter', true ) ) {
								$twitter_verification_data = isset( $vendor_verification_data['social']['Twitter'] ) ? $vendor_verification_data['social']['Twitter'] : '';
								if( is_array( $twitter_verification_data ) && count( $twitter_verification_data ) > 1 ) { 
									?>
									<div class="box">
										<div class="box-icon">
											<a href="<?php echo $twitter_verification_data['profileURL']; ?>"><img src="<?php echo $twitter_verification_data['photoURL']; ?>" /></a>
											<span class="social_ico"><img src="<?php echo $WCFMu->plugin_url . 'assets/images/twitter.png'; ?>"/></span>
										</div>
										<div class="info">
											<h4 class="put_the_name"><?php echo $twitter_verification_data['displayName']; ?></h4>
											<a class="btn" href="<?php echo add_query_arg( 'auth_out', 'twitter', get_wcfm_profile_url() ); ?>"><?php echo _e( 'Logout', 'wc-frontend-manager-ultimate' ); ?></a>
											<div class="wcfm_clearfix"></div>
											<p><?php echo wp_trim_words( $twitter_verification_data['description'], 20, '...' ); ?></p>
										</div>
									</div>
									<?php
								} elseif( $verify_twitter_client_id && $verify_twitter_client_secret ) {
									?>
									<a href="<?php echo add_query_arg( 'auth_in', 'twitter', get_wcfm_profile_url() ); ?>" class="social_btn_cnnct button TwitterConnect rounded large">
										<em><img src="<?php echo $WCFMu->plugin_url . 'assets/images/twitter.png'; ?>" style="height: 95%;"/></em>
										<span class="buttonText"><?php echo __( 'Connect to', 'wc-frontend-manager-ultimate' ). ' Twitter'; ?></span>
									</a>
									<?php
								}
							}
							?>
						</div>
						<div class="wcfm_clearfix"></div><br />
					<?php } ?>
				<?php } ?>
				
			</div>
		</div>
		<?php
	}
	
	function wcfmu_vendor_verification_user_profile_meta_save( $user_id, $wcfm_profile_form ) {
		global $WCFM;
		
		$vendor_verification_data = (array) get_user_meta( $user_id, 'wcfm_vendor_verification_data', true );
		
		if( isset( $wcfm_profile_form['vendor_verification'] ) && ! empty( $wcfm_profile_form['vendor_verification'] ) ) {
			$vendor_verification_data = array_merge( $vendor_verification_data, $wcfm_profile_form['vendor_verification'] );
			update_user_meta( $user_id, 'wcfm_vendor_verification_data', $vendor_verification_data );
		}
		
		if( isset( $wcfm_profile_form['vendor_verification'] ) && isset( $wcfm_profile_form['vendor_verification']['prompt_verify'] ) ) {
			// Verification Admin Notification
			$author_id = $user_id;
			$author_is_admin = 0;
			$author_is_vendor = 1;
			$message_to = 0;
			$wcfm_messages = sprintf( __( '<b>%s</b> - verification pending for review', 'wc-frontend-manager-ultimate' ), get_user_by( 'id', $user_id )->display_name );
			$WCFM->wcfm_notification->wcfm_send_direct_message( $author_id, $message_to, $author_is_admin, $author_is_vendor, $wcfm_messages, 'verification', false );
			
			// Verification mail to admin
			if( apply_filters( 'wcfm_is_allow_verification_email', true ) ) {
				if( !defined( 'DOING_WCFM_EMAIL' ) ) 
					define( 'DOING_WCFM_EMAIL', true );
				
				$mail_to = apply_filters( 'wcfm_admin_email_notification_receiver', get_bloginfo( 'admin_email' ), 'verification' );
				$verification_mail_subject = "{site_name}: " . __( "Verification pending for review", "wc-frontend-manager-ultimate" );
				$verification_mail_body    = '<br/>' . __( 'Hi', 'wc-frontend-manager' ) .
																		 ',<br/><br/>' . 
																		 sprintf( __( '<b>%s</b> - verification pending for review', 'wc-frontend-manager-ultimate' ), get_user_by( 'id', $user_id )->display_name ) .
																		 ',<br/><br/>' . 
																		 sprintf( __( 'Check here to take your decision - %s', 'wc-frontend-manager-ultimate' ), get_wcfm_messages_url() ) .
																		 '<br /><br/>' . __( 'Thank You', 'wc-frontend-manager' ) .
																		 '<br/><br/>';
				
				$subject = str_replace( '{site_name}', get_bloginfo( 'name' ), $verification_mail_subject );
				$subject = apply_filters( 'wcfm_email_subject_wrapper', $subject );
				$message = apply_filters( 'wcfm_email_content_wrapper', $verification_mail_body, __( 'Verification Pending', 'wc-frontend-manager-ultimate' ) );
				
				wp_mail( $mail_to, $subject, $message );
			}
			
			// Verification Status Update
			$vendor_verification_data['verification_status'] = 'pending';
			update_user_meta( $user_id, 'wcfm_vendor_verification_data', $vendor_verification_data );
			
			do_action( 'wcfm_after_vendor_verfication_prompt', $user_id, $vendor_verification_data, $wcfm_profile_form );
		}
	}
	
	function show_verification_status_message( $verification_status ) {
		
		$verification_status_class = '';
		$verification_status_icon = '';
		$verification_status_message = '';
		switch( $verification_status ) {
			case 'approve':
				  $verification_status_class = 'verification_approve';
				  $verification_status_icon = 'check-circle';
				  $verification_status_message = __( 'Congratulations! You are already a verified seller.', 'wc-frontend-manager-ultimate' );
			break;
			
			case 'pending':
				  $verification_status_class = 'verification_pending';
				  $verification_status_icon = 'exclamation-circle';
				  $verification_status_message = __( 'Ahh! Your request still under review.', 'wc-frontend-manager-ultimate' );
			break;
			
			case 'social_pending':
				  $verification_status_class = 'verification_pending';
				  $verification_status_icon = 'exclamation-circle';
				  $verification_status_message = __( 'Hey! Complete social verification now and be a verified seller.', 'wc-frontend-manager-ultimate' );
			break;
			
			case 'reject':
				  $verification_status_class = 'verification_reject';
				  $verification_status_icon = 'times-circle';
				  $verification_status_message = __( 'Opps! Your verification rejected, please try again.', 'wc-frontend-manager-ultimate' );
			break;
			
			default:
				  $verification_status_class = 'verification_noprompt';
				  $verification_status_icon = 'info-circle';
				  $verification_status_message = __( 'Hey! Prompt for verification now and be a verified seller.', 'wc-frontend-manager-ultimate' );
			break;
		}
		
		echo '<div class="verification_status_block '.$verification_status_class.'"><span class="wcfmfa fa-' . $verification_status_icon . '"></span><span>' . $verification_status_message . '</span></div>';
	}
	
	/**
	 * Generate Seller Verification Vacation HTMl
	 */
	function wcfmu_seller_verification_html() {
		global $WCFM, $WCFMu;
		
		if( isset( $_POST['messageid'] ) && isset($_POST['vendorid']) ) {
			$message_id = absint( $_POST['messageid'] );
			$vendor_id = absint( $_POST['vendorid'] );
			
			if( $vendor_id && $message_id ) {
				
				$vendor_verification_data = (array) get_user_meta( $vendor_id, 'wcfm_vendor_verification_data', true );
				$identity_types = $this->wcfm_vendor_verification_identity_types();
				
				$address = isset( $vendor_verification_data['address']['street_1'] ) ? $vendor_verification_data['address']['street_1'] : '';
				$address .= isset( $vendor_verification_data['address']['street_2'] ) ? ' ' . $vendor_verification_data['address']['street_2'] : '';
				$address .= isset( $vendor_verification_data['address']['city'] ) ? '<br />' . $vendor_verification_data['address']['city'] : '';
				$address .= isset( $vendor_verification_data['address']['zip'] ) ? ' '  . $vendor_verification_data['address']['zip'] : '';
				$address .= isset( $vendor_verification_data['address']['country'] ) ? '<br />' . $vendor_verification_data['address']['country'] : '';
				$address .= isset( $vendor_verification_data['address']['state'] ) ? ', ' . $vendor_verification_data['address']['state'] : '';
				
				$verification_note = isset( $vendor_verification_data['verification_note'] ) ? $vendor_verification_data['verification_note'] : '';
				
				?>
				<form id="wcfm_verification_response_form" class="wcfm_popup_wrapper">
				  <div style="margin-bottom: 15px;"><h2 style="float: none;"><?php _e( 'Vendor Verification', 'wc-frontend-manager-ultimate' ); ?></h2></div>
					<table>
						<tbody>
						  <?php
							if( !empty( $identity_types ) ) {
								foreach( $identity_types as $identity_type ) {
									if( isset( $identity_type['is_active'] ) ) {
										$identity_type_value = '';
										if( !empty( $vendor_verification_data ) && isset( $vendor_verification_data['identity'] ) && isset( $vendor_verification_data['identity'][$identity_type['identity_id']] ) ) $identity_type_value = $vendor_verification_data['identity'][$identity_type['identity_id']];
										?>
											<tr>
												<td class="wcfm_verification_response_form_label wcfm_popup_label"><?php echo wcfm_removeslashes( $identity_type['identity_name'] ); ?></td>
												<td>
												  <?php if( $identity_type_value ) { ?>
												  	<a class="wcfm-wp-fields-uploader" target="_blank" style="width: 32px; height: 32px;" href="<?php echo wcfm_get_attachment_url( $identity_type_value ); ?>"><span style="width: 32px; height: 32px; display: inline-block;" class="placeHolderDocs"></span></a>
												  <?php } else { ?>
												  	&ndash;
												  <?php } ?>
												</td>
											</tr>
										<?php
									}
								}
							}
							?>
							<tr>
								<td class="wcfm_verification_response_form_label wcfm_popup_label"><?php _e( 'Address', 'wc-frontend-manager-ultimate' ); ?></td>
								<td><?php echo $address; ?></td>
							</tr>
							<tr>
								<td class="wcfm_verification_response_form_label wcfm_popup_label"><?php _e( 'Note to Vendor', 'wc-frontend-manager-ultimate' ); ?></td>
								<td><textarea class="wcfm-textarea" name="wcfm_verification_response_note"></textarea></td>
							</tr>
							<tr>
								<td class="wcfm_verification_response_form_label wcfm_popup_label"><?php _e( 'Status Update', 'wc-frontend-manager-ultimate' ); ?></td>
								<td>
								  <label for="wcfm_verification_response_status_approve"><input type="radio" id="wcfm_verification_response_status_approve" name="wcfm_verification_response_status" value="approve" checked /><?php _e( 'Approve', 'wc-frontend-manager-ultimate' ); ?></label>
								  <label for="wcfm_verification_response_status_reject"><input type="radio" id="wcfm_verification_response_status_reject" name="wcfm_verification_response_status" value="reject" /><?php _e( 'Reject', 'wc-frontend-manager-ultimate' ); ?></label>
								</td>
							</tr>
						</tbody>
					</table>
					<input type="hidden" name="wcfm_verification_vendor_id" value="<?php echo $vendor_id; ?>" />
					<input type="hidden" name="wcfm_verification_message_id" value="<?php echo $message_id; ?>" />
					<div class="wcfm-message" tabindex="-1"></div>
					<input type="button" class="wcfm_verification_response_button wcfm_submit_button wcfm_popup_button" id="wcfm_verification_response_button" value="<?php _e( 'Update', 'wc-frontend-manager-ultimate' ); ?>" />
					<div class="wcfm_clearfix"></div>
				</form>
				<?php
			}
		}
		die;
	}
	
	function wcfmu_verification_response_update() {
		global $WCFM, $WCFMu, $_POST, $wpdb;
		
		$wcfm_verification_response_form_data = array();
	  parse_str($_POST['wcfm_verification_response_form'], $wcfm_verification_response_form_data);
		
		if( isset( $wcfm_verification_response_form_data['wcfm_verification_message_id'] ) && isset($wcfm_verification_response_form_data['wcfm_verification_vendor_id']) ) {
			$message_id = absint( $wcfm_verification_response_form_data['wcfm_verification_message_id'] );
			$vendor_id  = absint( $wcfm_verification_response_form_data['wcfm_verification_vendor_id'] );
			
			if( $vendor_id && $message_id ) {
				$vendor_verification_data = (array) get_user_meta( $vendor_id, 'wcfm_vendor_verification_data', true );
				
				$verification_note   = wcfm_stripe_newline( $wcfm_verification_response_form_data['wcfm_verification_response_note'] );
				$verification_note   = esc_sql( $verification_note );
				$verification_status = $wcfm_verification_response_form_data['wcfm_verification_response_status'];
				
				$vendor_verification_data['verification_status'] = $verification_status;
				$vendor_verification_data['verification_note']   = $verification_note;
				update_user_meta( $vendor_id, 'wcfm_verification_status', $verification_status );
				update_user_meta( $vendor_id, 'wcfm_vendor_verification_data', $vendor_verification_data );
				
				// Verification Vendor Notification
				$author_id = -1;
				$author_is_admin = 1;
				$author_is_vendor = 0;
				$message_to = $vendor_id;
				if( $verification_status == 'reject' ) {
					$wcfm_messages = __( '<b>Opps!!!</b> Your verification rejected, please try again. <br />Added note: ', 'wc-frontend-manager-ultimate' ) . $verification_note;
				} else {
					$wcfm_messages = __( '<b>Congratulation!!!</b> Your verification approved. <br />Added note: ', 'wc-frontend-manager-ultimate' ) . $verification_note;
				}
				$WCFM->wcfm_notification->wcfm_send_direct_message( $author_id, $message_to, $author_is_admin, $author_is_vendor, $wcfm_messages, 'verification' );
				
				// Verification message mark read
				$author_id = apply_filters( 'wcfm_message_author', get_current_user_id() );
				$todate = date('Y-m-d H:i:s');
				
				$wpdb->query( "DELETE FROM {$wpdb->prefix}wcfm_messages WHERE ID = {$message_id}" );
				
				echo '{"status": true, "message": "' . __( 'Verification status successfully updated.', 'wc-frontend-manager-ultimate' ) . '"}';
				die;
			}
		}
		echo '{"status": false, "message": "' . __( 'Verification status update failed.', 'wc-frontend-manager-ultimate' ) . '"}';
		die;
	}
	
	function wcfmu_vendor_verification_manage( $vendor_id ) {
		global $WCFM, $WCFMu;
		
		$disable_vendor = get_user_meta( $vendor_id, '_disable_vendor', true );
		if( $disable_vendor ) return;
		
		$vendor_verification_data = (array) get_user_meta( $vendor_id, 'wcfm_vendor_verification_data', true );
		
		$verification_status = 'noprompt';
		if( !empty( $vendor_verification_data ) && isset( $vendor_verification_data['verification_status'] ) ) $verification_status = $vendor_verification_data['verification_status'];
		
		
		$vendor_verification_options = (array) get_option( 'wcfm_vendor_verification_options' );
		
		$verification_badge = isset( $vendor_verification_options['verification_badge'] ) ? $vendor_verification_options['verification_badge'] : '';
		if( !$verification_badge ) $verification_badge = $WCFMu->plugin_url . 'assets/images/verification_badge.png';
		
		$verify_by_google = isset( $vendor_verification_options['verify_by_google'] ) ? $vendor_verification_options['verify_by_google'] : '';
		$verify_google_client_id = isset( $vendor_verification_options['verify_google_client_id'] ) ? $vendor_verification_options['verify_google_client_id'] : '';
		$verify_google_client_secret = isset( $vendor_verification_options['verify_google_client_secret'] ) ? $vendor_verification_options['verify_google_client_secret'] : '';
		
		$verify_by_facebook = isset( $vendor_verification_options['verify_by_facebook'] ) ? $vendor_verification_options['verify_by_facebook'] : '';
		$verify_facebook_client_id = isset( $vendor_verification_options['verify_facebook_client_id'] ) ? $vendor_verification_options['verify_facebook_client_id'] : '';
		$verify_facebook_client_secret = isset( $vendor_verification_options['verify_facebook_client_secret'] ) ? $vendor_verification_options['verify_facebook_client_secret'] : '';
		
		$verify_by_linkedin = isset( $vendor_verification_options['verify_by_linkedin'] ) ? $vendor_verification_options['verify_by_linkedin'] : '';
		$verify_linkedin_client_id = isset( $vendor_verification_options['verify_linkedin_client_id'] ) ? $vendor_verification_options['verify_linkedin_client_id'] : '';
		$verify_linkedin_client_secret = isset( $vendor_verification_options['verify_linkedin_client_secret'] ) ? $vendor_verification_options['verify_linkedin_client_secret'] : '';
		
		$verify_by_twitter = isset( $vendor_verification_options['verify_by_twitter'] ) ? $vendor_verification_options['verify_by_twitter'] : '';
		$verify_twitter_client_id = isset( $vendor_verification_options['verify_twitter_client_id'] ) ? $vendor_verification_options['verify_twitter_client_id'] : '';
		$verify_twitter_client_secret = isset( $vendor_verification_options['verify_twitter_client_secret'] ) ? $vendor_verification_options['verify_twitter_client_secret'] : '';
		
		$verification_note = isset( $vendor_verification_data['verification_note'] ) ? $vendor_verification_data['verification_note'] : '';
		
		$is_social_verification_enabled = false;
		if( $verify_by_google || $verify_by_facebook || $verify_by_linkedin || $verify_by_twitter ) { $is_social_verification_enabled = true; }
		
		// Check Social Pending
		if( $is_social_verification_enabled ) {
			if( $verification_status == 'approve' ) {
				if( !isset( $vendor_verification_data['social'] ) ) {
					$verification_status = 'social_pending';
				} else {
					if( $verify_by_google && !isset( $vendor_verification_data['social']['Google'] ) ) $verification_status = 'social_pending';
					elseif( $verify_by_twitter && !isset( $vendor_verification_data['social']['Twitter'] ) ) $verification_status = 'social_pending';
					elseif( $verify_by_linkedin && !isset( $vendor_verification_data['social']['Linkedin'] ) ) $verification_status = 'social_pending';
					elseif( $verify_by_facebook && !isset( $vendor_verification_data['social']['Facebook'] ) ) $verification_status = 'social_pending';
					else {
						$vendor_verification_data['social_verification_status'] = 'approve';
						update_user_meta( $vendor_id, 'wcfm_vendor_verification_data', $vendor_verification_data );
					}
				}
			}
		} else {
			$vendor_verification_data['social_verification_status'] = 'approve';
			update_user_meta( $vendor_id, 'wcfm_vendor_verification_data', $vendor_verification_data );
		}
		
		?>
		<!-- collapsible -->
		<div class="page_collapsible vendor_manage_verification" id="wcfm_vendor_manage_form_verification_head"><label class="fab fa-angellist"></label>&nbsp;<?php _e( 'Verification', 'wc-frontend-manager-ultimate' ); ?><span></span></div>
		<div class="wcfm-container">
			<div id="wcfm_vendor_manage_form_verification_expander" class="wcfm-content">
			  <div class="wcfmvm_verification_details">
					<div class="wcfmvm_verification_identity_details">
						<h2><?php _e( 'Identity Details', 'wc-frontend-manager-ultimate' ); ?></h2><br />
						<div class="wcfm_clearfix"></div>
						<?php
						$identity_types = $this->wcfm_vendor_verification_identity_types();
						if( !empty( $identity_types ) ) {
							foreach( $identity_types as $identity_type ) {
								if( isset( $identity_type['is_active'] ) ) {
									$identity_type_value = '&ndash;';
									if( !empty( $vendor_verification_data ) && isset( $vendor_verification_data['identity'] ) && isset( $vendor_verification_data['identity'][$identity_type['identity_id']] ) ) $identity_type_value = $vendor_verification_data['identity'][$identity_type['identity_id']];
									$WCFM->wcfm_fields->wcfm_generate_form_field( array( 
																																		$identity_type['identity_id'] => array( 'label' => $identity_type['identity_name'], 'name' => 'vendor_verification[identity]['.$identity_type['identity_id'].']', 'type' => 'upload', 'mime' => 'Uploads', 'mime_class' => 'wcfm_linked_attached', 'class' => 'wcfm-text', 'label_class' => 'wcfm_title', 'prwidth' => 32, 'value' => $identity_type_value ) 
																																		) );
								}
							}
						}
						?>
						<div class="wcfm_clearfix"></div><br />
					</div>
					<div class="wcfmvm_verification_address_details">
						<h2><?php _e( 'Address Details', 'wc-frontend-manager-ultimate' ); ?></h2><br />
						<div class="wcfm_clearfix"></div>
						<?php
						if( !isset( $vendor_verification_data['address'] ) ) $vendor_verification_data['address'] = array();
						$street_1 = isset( $vendor_verification_data['address']['street_1'] ) ? $vendor_verification_data['address']['street_1'] : '&ndash;';
						$street_2 = isset( $vendor_verification_data['address']['street_2'] ) ? $vendor_verification_data['address']['street_2'] : '&ndash;';
						$city    = isset( $vendor_verification_data['address']['city'] ) ? $vendor_verification_data['address']['city'] : '&ndash;';
						$zip     = isset( $vendor_verification_data['address']['zip'] ) ? $vendor_verification_data['address']['zip'] : '&ndash;';
						$country = isset( $vendor_verification_data['address']['country'] ) ? $vendor_verification_data['address']['country'] : '&ndash;';
						$state   = isset( $vendor_verification_data['address']['state'] ) ? $vendor_verification_data['address']['state'] : '&ndash;';
						
						$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_vendor_verification_profile_fields_address', array(
																																																		"vstreet_1" => array('label' => __('Street', 'wc-frontend-manager'), 'placeholder' => __('Street address', 'wc-frontend-manager'), 'name' => 'vendor_verification[address][street_1]', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'attributes' => array( 'readonly' => true), 'value' => $street_1 ),
																																																		"vstreet_2" => array('label' => __('Street 2', 'wc-frontend-manager'), 'placeholder' => __('Apartment, suite, unit etc. (optional)', 'wc-frontend-manager'), 'name' => 'vendor_verification[address][street_2]', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'attributes' => array( 'readonly' => true),'value' => $street_2 ),
																																																		"vcity" => array('label' => __('City/Town', 'wc-frontend-manager'), 'placeholder' => __('Town / City', 'wc-frontend-manager'), 'name' => 'vendor_verification[address][city]', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'attributes' => array( 'readonly' => true), 'value' => $city ),
																																																		"vzip" => array('label' => __('Postcode/Zip', 'wc-frontend-manager'), 'placeholder' => __('Postcode / Zip', 'wc-frontend-manager'), 'name' => 'vendor_verification[address][zip]', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'attributes' => array( 'readonly' => true), 'value' => $zip ),
																																																		"vcountry" => array('label' => __('Country', 'wc-frontend-manager'), 'name' => 'vendor_verification[address][country]', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'attributes' => array( 'readonly' => true), 'value' => $country ),
																																																		"vstate" => array('label' => __('State/County', 'wc-frontend-manager'), 'name' => 'vendor_verification[address][state]', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'attributes' => array( 'readonly' => true), 'value' => $state ),
																																																		) ) );
						?>
						<div class="wcfm_clearfix"></div><br />
					</div>
				</div>
				
				<div class="wcfm_vendor_verification_manage">
				  <form id="wcfm_vendor_manage_verification_form" class="wcfm">
						<?php 
						if( $verification_status == 'approve' ) {
							$WCFM->wcfm_fields->wcfm_generate_form_field(  array( "wcfm_verification_response_status" => array( 'type' => 'hidden', 'value' => 'reject' ) ) );
						} else {
							$WCFM->wcfm_fields->wcfm_generate_form_field(  array( "wcfm_verification_response_status" => array( 'type' => 'hidden', 'value' => 'approve' ) ) );
						}
						$WCFM->wcfm_fields->wcfm_generate_form_field(  array( "wcfm_verification_vendor_id" => array( 'type' => 'hidden', 'value' => $vendor_id ) ) );
						$WCFM->wcfm_fields->wcfm_generate_form_field(  array( "wcfm_verification_response_note" => array( 'label' => __( 'Note to Vendor', 'wc-frontend-manager-ultimate' ), 'type' => 'textarea', 'value' => '', 'custom_attributes' => array( 'required' => true ), 'placeholder' => __( 'Add some note ...', 'wc-frontend-manager-ultimate' ) ) ) );
						?>
						<div class="wcfm-clearfix"></div>
						<div class="wcfm-message" tabindex="-1"></div>
						<div class="wcfm-clearfix"></div>
						<div id="wcfm_verification_submit">
							<input type="submit" name="save-data" value="<?php if( $verification_status == 'approve' ) { _e( 'Mark Unapproved', 'wc-frontend-manager-ultimate' ); } else { _e( 'Mark Approved', 'wc-frontend-manager-ultimate' ); } ?>" id="wcfm_vendor_verification_save_button" class="wcfm_submit_button" />
						</div>
						<div class="wcfm-clearfix"></div>
					</form>
				</div>
			</div>
		</div>
		<div class="wcfm_clearfix"></div><br />
		<!-- end collapsible -->
		<?php
	}
	
	function wcfmu_vendor_manage_verification_response_update() {
		global $WCFM, $WCFMu, $_POST, $wpdb;
		
		$wcfm_verification_response_form_data = array();
	  parse_str($_POST['wcfm_vendor_manage_verification_form'], $wcfm_verification_response_form_data);
		
		if( isset($wcfm_verification_response_form_data['wcfm_verification_vendor_id']) ) {
			$vendor_id  = absint( $wcfm_verification_response_form_data['wcfm_verification_vendor_id'] );
			
			if( $vendor_id ) {
				$vendor_verification_data = (array) get_user_meta( $vendor_id, 'wcfm_vendor_verification_data', true );
				
				$verification_note   = wcfm_stripe_newline( $wcfm_verification_response_form_data['wcfm_verification_response_note'] );
				$verification_note   = esc_sql( $verification_note );
				$verification_status = $wcfm_verification_response_form_data['wcfm_verification_response_status'];
				
				$vendor_verification_data['verification_status'] = $verification_status;
				$vendor_verification_data['verification_note']   = $verification_note;
				update_user_meta( $vendor_id, 'wcfm_verification_status', $verification_status );
				update_user_meta( $vendor_id, 'wcfm_vendor_verification_data', $vendor_verification_data );
				
				// Verification Vendor Notification
				$author_id = -1;
				$author_is_admin = 1;
				$author_is_vendor = 0;
				$message_to = $vendor_id;
				if( $verification_status == 'reject' ) {
					$wcfm_messages = __( '<b>Opps!!!</b> Your verification has been rejected, please try again. <br />Added note: ', 'wc-frontend-manager-ultimate' ) . $verification_note;
				} else {
					$wcfm_messages = __( '<b>Congratulation!!!</b> Your verification has been approved. <br />Added note: ', 'wc-frontend-manager-ultimate' ) . $verification_note;
				}
				$WCFM->wcfm_notification->wcfm_send_direct_message( $author_id, $message_to, $author_is_admin, $author_is_vendor, $wcfm_messages, 'verification' );
				
				echo '{"status": true, "message": "' . __( 'Verification ststus successfully updated.', 'wc-frontend-manager-ultimate' ) . '"}';
				die;
			}
		}
		echo '{"status": false, "message": "' . __( 'Verification ststus update failed.', 'wc-frontend-manager-ultimate' ) . '"}';
		die;
	}
	
	function wcfmu_vendor_verification_product_limit( $product_limit, $vendor_id = 0 ) {
		global $WCFM, $WCFMu;
		
		if( wcfm_is_vendor() ) {
			if( !$vendor_id ) {
				$vendor_id   = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );
			}
		
			if( !$this->is_verified_vendor( $vendor_id ) ) {
				$vendor_verification_options = get_option( 'wcfm_vendor_verification_options', array() );
				$verification_product_limit = isset( $vendor_verification_options['verification_product_limit'] ) ? $vendor_verification_options['verification_product_limit'] : '';
				if( $verification_product_limit ) $product_limit = $verification_product_limit;
				if( $verification_product_limit == '-1' ) $product_limit = -1;
				if( $verification_product_limit == '0' ) $product_limit = 1989;
			}
		}
		
		return $product_limit;
	}
	
	function wcfm_verification_message_types( $message_types ) {
		$message_types['verification'] =  apply_filters( 'wcfm_sold_by_label', '', __( 'Vendor', 'wc-frontend-manager' ) ) . ' ' . __( 'Verification', 'wc-frontend-manager-ultimate' );
		return $message_types;
	}
	
	function get_wcfm_verification_badge() {
		global $WCFM, $WCFMu;
		
		$vendor_verification_options = (array) get_option( 'wcfm_vendor_verification_options' );
		
		$verification_badge = isset( $vendor_verification_options['verification_badge'] ) ? wcfm_get_attachment_url( $vendor_verification_options['verification_badge'] ) : '';
		if( !$verification_badge ) $verification_badge = $WCFMu->plugin_url . 'assets/images/verification_badge.png';
		
		return $verification_badge;
	}
	
	public function is_verified_vendor( $vendor_id ) {
		global $WCFM, $WCFMu;
		
		$vendor_verification_data = (array) get_user_meta( $vendor_id, 'wcfm_vendor_verification_data', true );
		
		$verification_status = 'noprompt';
		if( !empty( $vendor_verification_data ) && isset( $vendor_verification_data['verification_status'] ) ) $verification_status = $vendor_verification_data['verification_status'];
		
		$social_verification_status = 'pending';
		if( !empty( $vendor_verification_data ) && isset( $vendor_verification_data['social_verification_status'] ) ) $social_verification_status = $vendor_verification_data['social_verification_status'];
		
		if( ( $verification_status == 'approve' ) && ( $social_verification_status == 'approve' ) ) return true;
		
		return false;
	}
	
	function after_wcfm_dashboard_user( $vendor_id ) {
		global $WCFM, $WCFMu;
		if( !$vendor_id ) {
			$vendor_id   = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );
		}
		if( $this->is_verified_vendor( $vendor_id ) ) {
			$badge = $this->get_wcfm_verification_badge();
			if( $badge ) {
				 echo '<img class="wcfm_vendor_badge text_tip"  data-tip="' . __( 'Verified Vendor', 'wc-frontend-manager-ultimate' ) . '" src="' . $badge . '" />';
			}
		}
	}
	
	public function show_verified_seller_badge( $vendor_id, $badge_classses, $context = 'view' ) {
		global $WCFM, $WCFMu;
		if( $vendor_id ) {
			if( $this->is_verified_vendor( $vendor_id ) ) {
				$badge = $this->get_wcfm_verification_badge();
				if( $badge ) {
					if( $context == 'view' ) {
						echo '<div class="'.$badge_classses.' text_tip" data-tip="' . __( 'Verified Vendor', 'wc-frontend-manager-ultimate' ) . '"><img src="' . $badge . '" /></div>';
					} else {
						return '<span class="'.$badge_classses.' text_tip" data-tip="' . __( 'Verified Vendor', 'wc-frontend-manager-ultimate' ) . '"><img style="display: inline-block;" src="' . $badge . '" /></span>';
					}
				}
			}
		}
	}
	
	function show_verified_seller_badge_by_name( $name, $vendor_id, $badge_classses ) {
		global $WCFM, $WCFMu;
		if( $vendor_id ) {
			if( $this->is_verified_vendor( $vendor_id ) ) {
				$badge = $this->get_wcfm_verification_badge();
				if( $badge ) {
					$name .= '<div class="'.$badge_classses.' text_tip" data-tip="' . __( 'Verified Vendor', 'wc-frontend-manager-ultimate' ) . '"><img src="' . $badge . '" /></div>';
				}
			}
		}
		return $name;
	}
}