<?php

/**
 * reCaptcha
 *
 * @since 1.31
 *
 * Class LRM_Pro_reCaptcha
 */
class LRM_Pro_reCaptcha {

    /**
     * Add all necessary hooks
     */
    static function init() {

        if ( ! lrm_setting( 'security/recaptcha/site_key' ) || ! lrm_setting( 'security/recaptcha/secret_key' ) ) {
            return;
        }

    }

    /**
     * Render reCaptcha & enqueue assets
     */
    static function render() {
        $invisible_params = '';
        if ( 'invisible' === lrm_setting( 'security/recaptcha/type' ) ) {
            $invisible_params = 'data-size="invisible" data-badge="inline" data-callback="LRM_reCaptcha_submitCallback"';
        }

        printf('<div class="lrm-grecaptcha" %s data-sitekey="%s"></div>',
            $invisible_params, esc_attr( lrm_setting( 'security/recaptcha/site_key' ) ));

        wp_enqueue_script('lrm-grecaptcha', 'https://www.google.com/recaptcha/api.js?onload=LRM_reCaptcha_onloadCallback&render=explicit', ['lrm-modal-pro'], 1);
    }

    /**
     * Validate reCaptcha response (Make a request to the Google servers)
     *
     * @return string
     */
    static function validate() {

        $remote_ip = LRM_Pro_Security::get_user_ip();
        $secret = lrm_setting( 'security/recaptcha/secret_key' );

        if ( empty($secret) ) {
            //fv_log('Recaptcha wrong $secret!', $secret, __FILE__, __LINE__);
            wp_send_json_error(array(
                'message' => 'reCaptcha empty API Keys!'
            ));
        }

        $response = isset($_POST['g-recaptcha-response']) ? $_POST['g-recaptcha-response'] : '';
        if ( empty($response) ) {
            wp_send_json_error(array(
                'message' => LRM_Settings::get()->setting('messages_pro/integrations/recaptcha_error'),
                'code' => 'no g-recaptcha-response'
            ));
        }

        // make a GET request to the Google reCAPTCHA Server
        $request = wp_remote_get(
            'https://www.google.com/recaptcha/api/siteverify?secret=' . $secret . '&response=' . $response . '&remoteip=' . $remote_ip
        );

        // Check for error
        if ( is_wp_error( $request ) ) {

            wp_send_json_error(array(
                'message' => 'Can\'t verify reCaptcha - server error!',
                'code' => current_user_can('manage_options') ? print_r($request, true) : ''
            ));
            return 'error';
        }

        // get the request response body
        $response_body = wp_remote_retrieve_body( $request );
        $resultArr = json_decode( $response_body, true );
        //var_dump($resultArr);

        if ( $resultArr['success'] == false && isset($resultArr['error-codes']) ) {
            wp_send_json_error(array(
                'message' => 'Can\'t verify reCaptcha - response error!',
                'code' => current_user_can('manage_options') ? print_r($resultArr['error-codes'], true) : ''
            ));

            //fv_log('Recaptcha error!', $resultArr['error-codes'], __FILE__, __LINE__);
        }
        /*
         {
              "success": false,
              "error-codes": [
                "invalid-input-response",
                "invalid-input-secret"
              ]
            }
         */

        if ( !$resultArr['success'] ) {
            wp_send_json_error(array(
                'message' => LRM_Settings::get()->setting('messages_pro/integrations/recaptcha_error')
            ));

        }

        return $resultArr['success'];

    }

}