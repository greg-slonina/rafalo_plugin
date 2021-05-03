<?php

use WordfenceLS\Controller_Users;
use WordfenceLS\Controller_TOTP;
use WordfenceLS\Controller_WordfenceLS;

/**
 * Wordfence 2FA to modal
 * Wordfence integration
 *
 * @since 1.88
 *
 * Class LRM_Pro_Wordfence_2FA
 */
class LRM_Pro_Wordfence_2FA {

    /**
     * Add all necessary hooks
     */
    static function init() {
        if ( !class_exists('\WordfenceLS\Controller_Users') ) {
            return;
        }

        add_action('lrm/login_pre_signon/after_user_check', [__CLASS__,'run'], 10, 2);
        add_action('lrm/login_successful', [__CLASS__,'record_last_login'], 10, 1);
    }

    /**
     * Show OTP field, if this is required
     *
     * @param $info
     * @param WP_User $user
     */
    static function run($info, $user) {

        if ( isset( $user->ID ) && !Controller_Users::shared()->has_2fa_active($user) ) {
            return;
        }

	    if ( isset( $_POST['wfls-token'] ) ) {
		    if (Controller_TOTP::shared()->validate_2fa($user, $_POST['wfls-token'])) {
			    define('WORDFENCE_LS_COMBINED_IS_VALID', true); //AJAX call will use this to generate a different JWT that authenticates for the account _and_ code
			    // 2FA is ok, stop validation from the Wordfence
			    remove_filter('authenticate', array(Controller_WordfenceLS::shared(), '_authenticate'), 25);
			    return true;
		    }

		    wp_send_json_error(array(
			    'message' => lrm_setting('messages_pro/integrations/2fa_code_invalid', true),
		    ));
	    }

        if ( Controller_Users::shared()->has_remembered_2fa($user) ) {
	        return;
        }

        ob_start();

        $field_name = 'wfls-token';
        require LRM_PRO_PATH . 'templates/2fa-otp.php';

        $opt_html = ob_get_clean();

        wp_send_json_error(array(
            'message'=> lrm_setting('messages_pro/integrations/googleauthenticator_required', true),
            'custom_html'=> $opt_html,
            'custom_html_selector'=> '.lrm-integrations-otp',
        ));

    }

	/**
	 * @param WP_User $user
	 * Manually record last login time for the Wordfence
	 */
	static function record_last_login($user) {
		if ( ! lrm_setting('advanced/troubleshooting/call_wp_login_action') ) {
			Controller_WordfenceLS::shared()->_record_login($user->user_login);
		}
	}

}