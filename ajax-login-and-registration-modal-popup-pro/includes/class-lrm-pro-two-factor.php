<?php

/**
 * https://wordpress.org/plugins/two-factor/ integration
 *
 * @since 1.74
 *
 * Class LRM_Pro_Two_Factor
 */
class LRM_Pro_Two_Factor {

    /**
     * Add all necessary hooks
     */
    static function init() {
        if ( !class_exists('Two_Factor_Core') || class_exists('ITSEC_Core') ) {
            return;
        }

        if ( isset( $_POST['wp-auth-id'] ) ) {
            return;
        }

        add_action('lrm/login_pre_signon/after_user_check',  __CLASS__.'::login_pre_signon', 10, 2);
        add_action('lrm/login_successful',  __CLASS__ . '::login_successful', 1);

        // Stop Loading ALL default frontend GoogleAuthenticator actions
        if ( 'wp-login.php' === $GLOBALS['pagenow'] && isset($_GET['action'], $_GET['user_id'], $_GET['login_nonce']) && 'two_auth' === $_GET['action'] ) {
            $user_ID = $_GET['user_id'];
            $login_nonce = $_GET['login_nonce'];
            $redirect_url = $_GET['redirect_to'];

	        $user = get_user_by('ID', $user_ID);
            if ( $user && ! Two_Factor_Core::verify_login_nonce( $user_ID, $login_nonce ) ) {
            	wp_die( 'LRM Two_Factor :: Invalid login nonce! ' . make_clickable( get_bloginfo( 'url' ) ) );
            }

	        Two_Factor_Core::login_html( $user, $login_nonce, $redirect_url );
            die;
        }
    }

    /**
     * @param $info
     * @param $user
     */
    static function login_pre_signon($info, $user) {

        if ( isset( $user->ID ) && Two_Factor_Core::is_user_using_two_factor( $user->ID ) ) {
	        remove_action( 'wp_login', array( 'Two_Factor_Core', 'wp_login' ), 10 );
        }

    }
    /**
     * @param WP_User|WP_Error $user
     */
    static function login_successful($user) {

    	if ( is_wp_error($user) ) {
    		return;
	    }

	    if ( ! Two_Factor_Core::is_user_using_two_factor( $user->ID ) ) {
		    return;
	    }

	    $login_nonce = Two_Factor_Core::create_login_nonce( $user->ID );
	    if ( ! $login_nonce ) {
		    wp_clear_auth_cookie();
		    wp_send_json_error(array(
			    'message'=> 'Failed to create a Two_Factor login nonce.',
		    ));
	    }

	    $redirect_url = LRM_Redirects_Manager::get_redirect( 'login', $user->ID );

	    $redirect_url = $redirect_url ? $redirect_url : home_url('/');

	    wp_clear_auth_cookie();

	    $login_url = site_url('wp-login.php', 'login');

	    if ( !empty($redirect_url) ) {
		    $login_url = add_query_arg( 'redirect_to', urlencode( $redirect_url ), $login_url );
	    }
	    $login_url = add_query_arg( 'action', 'two_auth', $login_url );
	    $login_url = add_query_arg( 'user_id', $user->ID, $login_url );
	    $login_url = add_query_arg( 'login_nonce', $login_nonce['key'], $login_url );

	    wp_send_json_success(apply_filters('lrm/login/success_response', array(
		    'logged_in' => true,
		    'user_id'   => $user->ID,
		    'message'   => lrm_setting('messages_pro/integrations/two_factor_redirecting', true),
		    'action'    => 'redirect',
		    'redirect_url'=> $login_url,
	    )));

    }

}