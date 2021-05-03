<?php

/**
 * Security actions (captcha, etc)
 *
 * @since 1.31
 *
 * Class LRM_Pro_Security
 */
class LRM_Pro_Security {

    /**
     * Add all necessary hooks
     */
    static function init() {

        $security_class = self::_get_security_class();

        if ( !$security_class ) {
            return;
        }

        $security_class::init();

        if ( lrm_setting( 'security/general/secure_login' ) ) {
            add_action('lrm/login_form', [$security_class, 'render']);
        }

        if ( lrm_setting( 'security/general/secure_register' ) ) {
            add_action('lrm/register_form/before_button', [$security_class, 'render']);
        }

        if ( lrm_setting( 'security/general/secure_lostpass' ) ) {
            add_action('lrm/lostpassword_form', [$security_class, 'render']);
        }
    }

    /**
     * Validate POST params
     */
    static function validate( $action ) {

        $actions_mappins = [
            'login'     => 'login',
            'signup'    => 'register',
            'lostpassword'  => 'lostpass',
        ];

        if ( !isset($actions_mappins[$action]) ) {
            return;
        }

        if ( !lrm_setting( 'security/general/secure_' . $actions_mappins[$action] ) ) {
            return;
        }

        $security_class = self::_get_security_class();

        if ( !$security_class ) {
            return;
        }

        $security_class::validate( $actions_mappins[$action] );

    }

    /**
     * Get a Captcha class based on a settings
     *
     * @return string|void
     */
    static function _get_security_class() {
        $security_type = lrm_setting( 'security/general/type' );
        $security_class = '';

        if ( ! $security_type ) {
            return;
        }

        switch ($security_type) {
            case 'reCaptcha':
                $security_class = 'LRM_Pro_reCaptcha';
                break;
            case 'MatchCaptcha':
                $security_class = 'LRM_Pro_MatchCaptcha';
                break;
        }

        return $security_class;
    }

    /**
     * Get a user IP (real or from CloudFlare)
     *
     * @return string
     */
    static function get_user_ip() {

        $ipaddress = '';
        if (isset($_SERVER['HTTP_CF_CONNECTING_IP']))
            $ipaddress = $_SERVER['HTTP_CF_CONNECTING_IP'];
        else if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if(isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;

    }

}