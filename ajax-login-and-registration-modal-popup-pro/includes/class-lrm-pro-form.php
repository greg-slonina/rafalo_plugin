<?php

/**
 * Form hooks
 *
 * @since 1.23
 *
 * Class LRM_Pro_Form
 */
class LRM_Pro_Form {

    protected static $instance;

	function __construct() {

        $form_hook_position = LRM_Settings::get()->setting('general_pro/all/form_hook_position');
        $form_hook_action = '';
        if ( 'before_form' == $form_hook_position ) {
            $form_hook_action = '/before';
        } elseif ( 'after_form' == $form_hook_position ) {
            $form_hook_action = '/after';
        }

        add_action('lrm/login_form'.$form_hook_action, array($this, 'login__action'));
        add_action('lrm/register_form'.$form_hook_action, array($this, 'register__action'));
        if ( has_action('lrm_lostpassword_form') ) {
            add_action('lrm_lostpassword_form', array($this, 'lostpassword__action'));
        } else {
            add_action('lrm/lostpassword_form', array($this, 'lostpassword__action'));
        }

        add_action('lrm/login_form/before', array($this, 'before_login_form__action'), 9);
        add_action('lrm/login_form', array($this, 'login_form_before_button__action'), 9);
        add_action('lrm/login_form/after', array($this, 'after_login_form__action'), 9);

        add_action('lrm/register_form/before', array($this, 'before_registration_form__action'), 9);
        add_action('lrm/register_form/before_button', array($this, 'before_registration_form_button__action'), 9);
        add_action('lrm/register_form/after', array($this, 'after_registration_form__action'), 9);

        //add_action('lrm/register_form/render_fields', array($this, 'register_form_render_fields__action'), 9);

        // WC Form replace
        // Allow 3rd party plugin filter template file from their plugin.
        add_filter( 'wc_get_template', function( $located, $template_name, $args, $template_path, $default_path ) {
            if ( lrm_setting('integrations/woo/replace_form') && 'myaccount/form-login.php' == $template_name ) {
                return LRM_PRO_PATH . 'templates/wc-form.php';
            }

            return $located;
        }, 10, 5 );

    }

    public function register_form_render_fields__action() {

	    $form = LRM_Form::instance();

	    if ( $form->is_empty() ) {

            if( ! lrm_setting('general_pro/all/hide_username') ) {
                $form->register('username', new LRM_Form_Field_User_Login());
            }

            if( lrm_setting('general/registration/display_first_and_last_name') ) {
                $form->register_group(
                    [
                        'first-name' => new LRM_Form_Field_First_Name(),
                        'last-name' => new LRM_Form_Field_Last_Name()
                    ]
                );
            }

            $form->register('email', new LRM_Form_Field_Email());

            $form = apply_filters('lrm/register_form/fields', $form);

        }

        $form->render();
//
//        if( lrm_setting('general_pro/all/allow_user_set_password') ) {
//            $form->register('password');
//        }

    }

    /*
     * Call default WP action to enable integrations
     */
    public function lostpassword__action() {
        do_action('lostpassword_form');
    }

/*
     * Call default WP action to enable integrations
     */
    public function login__action() {

//        if ( class_exists("Jetpack_Protect_Module") ) {
//            Jetpack_Protect_Module::instance()->block_with_math();
//        }

        do_action('login_form');
        //$this->show_ReallySimpleCaptcha();
    }

    /**
     * hook: lrm/register_form/before
     */
    public function before_registration_form__action() {
        $this->_form_info( 'messages_pro/info/registration_before_form' );
    }

    /**
     * hook: lrm/register_form
     */
    public function register__action() {


        if ( class_exists('WC_Vendors') ) {
            /**
             * Tweaks for WC Vendors plugin
             * @since 1.38
             */
            do_action('woocommerce_register_form');
        } else {
            do_action('register_form');
        }

        if ( function_exists('acf_enqueue_scripts') ) {
            acf_enqueue_scripts();
        }
    }

    /**
     * hooK: lrm/register_form/before_button
     */
    public function before_registration_form_button__action() {
        $this->_form_info( 'messages_pro/info/registration_before_button' );
    }

    /**
     * hooK: lrm/register_form/after
     */
    public function after_registration_form__action() {
        $this->_form_info( 'messages_pro/info/registration_after_form' );
    }

    /**
     * @hook: lrm/login_form
     */
    public function before_login_form__action() {
        $this->_form_info( 'messages_pro/info/login_before_form' );

        if ( class_exists("Jetpack_SSO") ) {
            echo '<div id="jetpack-sso-wrap"><div id="jetpack-sso-wrap__action">';
            echo Jetpack_SSO::get_instance()->build_sso_button( array(), 'is_primary' );
            echo '<div class="jetpack-sso-or"> <span>' . __( 'Or', 'jetpack' ) . '</span> </div>';
            echo '</div></div>';
        }

    }
    /**
     * hook: lrm/login_form
     */
    public function login_form_before_button__action() {
        $this->_form_info( 'messages_pro/info/login_before_button' );
    }

    /**
     * hook: lrm/login_form/after
     */
    public function after_login_form__action() {
        $this->_form_info( 'messages_pro/info/login_after_form' );
    }

    public function _form_info( $slug ) {
        $info = LRM_Settings::get()->setting( $slug, true );
        if ($info) {
            echo do_shortcode(balanceTags( $info));
        }
    }

    /**
     * @return self
     */
    public static function get(){
        if ( ! isset( self::$instance ) ) {
            return self::$instance = new self();
        }

        return self::$instance;
    }

}