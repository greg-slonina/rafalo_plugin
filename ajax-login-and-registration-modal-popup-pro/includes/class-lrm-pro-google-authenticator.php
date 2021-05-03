<?php

/**
 * Google_Authenticator Registration form to modal
 * https://wordpress.org/plugins/google-authenticator/ integration
 *
 * @since 1.73
 *
 * Class LRM_Pro_Google_Authenticator
 */
class LRM_Pro_Google_Authenticator {

    /**
     * Add all necessary hooks
     */
    static function init() {
        if ( !class_exists('GoogleAuthenticator') ) {
            return;
        }

        if ( isset( $_POST['googleotp'] ) ) {
            return;
        }

        add_action('lrm/login_pre_signon/after_user_check', 'LRM_Pro_Google_Authenticator::run', 10, 2);

        // Stop Loading ALL default frontend GoogleAuthenticator actions
        if ( ! is_admin() && 'wp-login.php' !== $GLOBALS['pagenow'] ) {
            remove_action( 'init', array( GoogleAuthenticator::$instance, 'init' ) );
        }
    }

    /**
     * Show OTP field, if this is required
     *
     * @param $info
     * @param $user
     */
    static function run($info, $user) {

        if ( isset( $user->ID ) && trim(get_user_option( 'googleauthenticator_enabled', $user->ID ) ) !== 'enabled' ) {
            return;
        }

        ob_start();

        require LRM_PRO_PATH . 'templates/google-otp-pass.php';

        $opt_html = ob_get_clean();

        wp_send_json_error(array(
            'message'=> lrm_setting('messages_pro/integrations/googleauthenticator_required', true),
            'custom_html'=> $opt_html,
            'custom_html_selector'=> '.lrm-integrations-otp',
        ));

    }

}