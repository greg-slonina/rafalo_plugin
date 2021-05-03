<?php

/**
 * Wordfence 2FA to modal
 * Wordfence integration
 *
 * @since 1.91
 *
 * Class LRM_Pro_iThemes_Security_2FA
 */
class LRM_Pro_iThemes_Security_2FA {

    /**
     * Add all necessary hooks
     */
    static function init() {
        if ( !class_exists('ITSEC_Core')|| !class_exists('ITSEC_Two_Factor_Helper') ) {
        	return;
        }
        $enabled_providers = ITSEC_Two_Factor_Helper::get_instance()->get_enabled_providers();
        if ( !$enabled_providers || !isset($enabled_providers['Two_Factor_Totp']) ) {
            return;
        }

        add_action('lrm/login_pre_signon/after_user_check', [__CLASS__,'run'], 10, 2);
//        add_action('lrm/login_successful', [__CLASS__,'record_last_login'], 10, 1);
    }

    /**
     * Show OTP field, if this is required
     *
     * @param $info
     * @param WP_User $user
     */
    static function run($info, $user) {

        if ( isset( $user->ID ) && !Two_Factor_Totp::get_instance()->is_available_for_user($user) ) {
            return;
        }

	    if ( isset( $_POST['authcode'] ) ) {
		    if (Two_Factor_Totp::get_instance()->validate_authentication($user)) {
			    // 2FA is ok, stop validation from the Wordfence
			    remove_filter('authenticate', array('ITSEC_Application_Passwords', '_authenticate'), 50);
			    return true;
		    }

		    wp_send_json_error(array(
			    'message' => lrm_setting('messages_pro/integrations/2fa_code_invalid', true),
		    ));
	    }

        ob_start();

	    $field_name = 'authcode';
        require LRM_PRO_PATH . 'templates/2fa-otp.php';

        $opt_html = ob_get_clean();

        wp_send_json_error(array(
            'message'=> lrm_setting('messages_pro/integrations/googleauthenticator_required', true),
            'custom_html'=> $opt_html,
            'custom_html_selector'=> '.lrm-integrations-otp',
        ));

    }

//	/**
//	 * @param WP_User $user
//	 * Manually record last login time for the Wordfence
//	 */
//	static function record_last_login($user) {
//		if ( ! lrm_setting('advanced/troubleshooting/call_wp_login_action') ) {
//			Controller_WordfenceLS::shared()->_record_login($user->user_login);
//		}
//	}

}