<?php
/**
 * WCFMu plugin core
 *
 * Plugin Vendor Verification Auth Controler
 *
 * @author 		WC Lovers
 * @package 	wcfmu/core
 * @version   6.0.7
 */
 
class WCFMu_Vendor_Verification_Auth {
	
	public function __construct() {
		global $WCFM, $WCFMu, $wp;
		
		// Verification Profile Option
		if( wcfm_is_vendor() && apply_filters( 'wcfm_is_allow_profile', true ) && apply_filters( 'wcfm_is_allow_vendor_verification', true ) ) {
			if( isset( $wp->query_vars['wcfm-profile'] ) ) {
				$this->wcfm_hybridauth_init();
				$this->wcfm_vendor_auth_requests();
			}
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
}