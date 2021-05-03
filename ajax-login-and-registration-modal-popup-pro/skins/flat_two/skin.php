<?php

class LRM_Skin_Flat_Two extends LRM_Skin_Base {

    public function __construct() {
	    $this->slug = 'flat_two';
	    $this->title = 'Flat Two';
	    $this->url = LRM_PRO_URL . 'skins/';

	    $this->supports_customizer = true;

	    $this->customizer_section_title = '[skin] Flat Two';

        parent::__construct();
    }

    public function register_customizer_settings() {

        $this->_register_customizer_setting( "open_modal", array(
            'default' => '1',
            'setting_type' => 'option',
            'setting_transport' => 'postMessage',
            //'sanitize_callback' => 'sanitize_hex_color',
            'type_class' => 'LRM_Pro_WP_Customize_Control_Button',

            'label'      => __( 'Display modal for customize', 'ajax-login-and-registration-modal-popup' ),
            'description'=> __( 'Open modal >>', 'ajax-login-and-registration-modal-popup' ),

            'type' => 'button',
        ) );

	    $this->_register_customizer_setting( "border_radius", array(
		    'default' => '0',
		    'setting_type' => 'option',
		    'setting_transport' => 'postMessage',
		    'sanitize_callback' => 'absint',

		    'label' => 'Form & elements border radius',
		    'type' => 'number',
		    'input_attrs' => array(
			    'min'   => 0,
			    'max'   => 20,
			    'step'  => 1,
		    ),
	    ), array(
		    ':root' => array('attribute' => '--lrm-border-radius', 'type' => 'css', 'units' => 'px',),
	    ) );

	    $this->_register_customizer_setting( "input_bg", array(
		    'default' => '#f7f7f7',
		    'setting_type' => 'option',
		    'setting_transport' => 'postMessage',
		    'sanitize_callback' => 'sanitize_hex_color',

		    'label' => 'Input bottom border color',
		    'type' => 'color',
	    ), array(
		    ':root' => array('attribute' => '--lrm-input-bg','type' => 'css',),
	    ) );

	    $this->_register_customizer_setting( "input_bottom_border_color", array(
		    'default' => '#d2d8d8',
		    'setting_type' => 'option',
		    'setting_transport' => 'postMessage',
		    'sanitize_callback' => 'sanitize_hex_color',

		    'label' => 'Input bottom border color',
		    'type' => 'color',
	    ), array(
		    ':root' => array('attribute' => '--lrm-input-bottom-border-color','type' => 'css',),
	    ) );

	    $this->_register_customizer_setting( "input_active_bottom_border_color", array(
		    'default' => '#2980b9',
		    'setting_type' => 'option',
		    'setting_transport' => 'postMessage',
		    'sanitize_callback' => 'sanitize_hex_color',

		    'label' => 'Input bottom border color',
		    'type' => 'color',
	    ), array(
		    ':root' => array('attribute' => '--lrm-input-active-bottom-border-color','type' => 'css'),
	    ) );


        $this->_register_customizer_setting( "btn_color", array(
            'default' => '#ffffff',
            'setting_type' => 'option',
            'setting_transport' => 'postMessage',
            'sanitize_callback' => 'sanitize_hex_color',

            'label' => 'Buttons color',
            'type' => 'color',
        ), array(
            '.lrm-form a.button,.lrm-form button,.lrm-form button[type=submit],.lrm-form #buddypress input[type=submit],.lrm-form input[type=submit]' => array('attribute' => 'color','type' => 'css',),
        ) );

        $this->_register_customizer_setting( "btn_bg", array(
            'default' => '#2980b9',
            'setting_type' => 'option',
            'setting_transport' => 'postMessage',
            'sanitize_callback' => 'sanitize_hex_color',

            'label' => 'Buttons background color',
            'type' => 'color',
        ), array(
            '.lrm-form a.button,.lrm-form button,.lrm-form button[type=submit],.lrm-form #buddypress input[type=submit],.lrm-form input[type=submit]' => array('attribute' => 'background-color','type' => 'css',),
        ) );

        $this->_register_customizer_setting( "inactive_tab_bg", array(
            'default' => '#d2d8d8',
            'setting_type' => 'option',
            'setting_transport' => 'postMessage',
            'sanitize_callback' => 'sanitize_hex_color',

            'label' => 'Inactive tab background',
            'type' => 'color',
        ), array(
            '.lrm-user-modal-container .lrm-switcher a' => array('attribute' => 'background-color','type' => 'css',),
        ) );

        $this->_register_customizer_setting( "inactive_tab_color", array(
            'default' => '#809191',
            'setting_type' => 'option',
            'setting_transport' => 'postMessage',
            'sanitize_callback' => 'sanitize_hex_color',

            'label' => 'Inactive tab color',
            'type' => 'color',
        ), array(
            '.lrm-user-modal-container .lrm-switcher a' => array('attribute' => 'color','type' => 'css',),
        ) );

    }

}