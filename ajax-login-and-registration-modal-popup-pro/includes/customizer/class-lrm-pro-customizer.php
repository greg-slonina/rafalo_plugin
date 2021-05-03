<?php

// If this file is called directly, abort.
if (!class_exists('WP')) {
	die();
}


/**
 * Class LRM_Pro_Customizer
 * @since 1.14
 */

class LRM_Pro_Customizer {
	
	static function init() {
		add_action('customize_register', array('LRM_Pro_Customizer', 'customize_register') );
		add_action( 'customize_preview_init', array('LRM_Pro_Customizer', 'customizer_live_preview') );
		//add_action( 'customize_controls_enqueue_scripts ', array('LRM_Pro_Customizer', 'customize_controls_enqueue_scripts') );
		//add_action( 'wp_footer', array('LRM_Pro_Customizer', 'customizer_css'), 11 );
	}

	static function customize_register( $wp_customize ) {

		require LRM_PRO_PATH . 'includes/customizer/class-lrm-pro-customizer--button.php';

		/**
		 * Add our Header & Navigation Panel
		 */
		$wp_customize->add_panel( 'lrm_panel',
			array(
				'title' => __( 'AJAX Login & Registration' ),
				//'description' => esc_html__( 'Adjust your Header and Navigation sections.' ), // Include html tags such as

				'priority' => 160, // Not typically needed. Default is 160
				'capability' => 'edit_theme_options', // Not typically needed. Default is edit_theme_options
				'theme_supports' => '', // Rarely needed
				'active_callback' => '', // Rarely needed
			)
		);

		/**
		 * Add our Sample Section
		 */
		$wp_customize->add_section( 'lrm_controls_section',
			array(
				'title' => __( 'Select Skin' ),
				'description' => esc_html__( 'Here you can customize modal styles.' ),
				'panel' => 'lrm_panel', // Only needed if adding your Section to a Panel
				'priority' => 160, // Not typically needed. Default is 160
				'capability' => 'edit_theme_options', // Not typically needed. Default is edit_theme_options
				'theme_supports' => '', // Rarely needed
				'active_callback' => '', // Rarely needed
				'description_hidden' => 'false', // Rarely needed. Default is False
			)
		);
//
//		$wp_customize->add_setting( 'sample_default_text',
//			array(
//				'default' => '', // Optional.
//				'transport' => 'refresh', // Optional. 'refresh' or 'postMessage'. Default: 'refresh'
//				'type' => 'option', // Optional. 'theme_mod' or 'option'. Default: 'theme_mod'
//				'capability' => 'edit_theme_options', // Optional. Default: 'edit_theme_options'
//				'theme_supports' => '', // Optional. Rarely needed
//				'validate_callback' => '', // Optional. The name of the function that will be called to validate Customizer settings
//				'sanitize_callback' => '', // Optional. The name of the function that will be called to sanitize the input data before saving it to the database
//				'sanitize_js_callback' => '', // Optional. The name of the function that will be called to sanitize the data before outputting to javascript code. Basically to_json.
//				'dirty' => false, // Optional. Rarely needed. Whether or not the setting is initially dirty when created. Default: False
//			)
//		);

		$wp_customize->add_setting( 'lrm_show_modal',
			array(
				'default' => 1,
				'type' => 'option', // Optional. 'theme_mod' or 'option'. Default: 'theme_mod'
				'transport' => 'postMessage',
			)
		);
//
//		$wp_customize->add_control(
//			'lrm_show_modal',
//			[
//				'label'      => __( 'Show modal', 'starcresc' ),
//				'section' => 'lrm_controls_section',
//				'settings'   => 'lrm_show_modal',
//				'type'       => 'button',
//				'input_attrs' => array(
//					'value' => __( 'Edit Pages', 'textdomain' ), // 👈
//					'class' => 'button button-primary', // 👈
//				),
//			]
//		);

		$wp_customize->add_control(
			new LRM_Pro_WP_Customize_Control_Button($wp_customize, 'lrm_show_modal', array(
				'label'      => __( 'Display modal for customize', 'ajax-login-and-registration-modal-popup' ),
				'description'=> __( 'Open modal >>', 'ajax-login-and-registration-modal-popup' ),
				'section' => 'lrm_controls_section',
				'settings'   => 'lrm_show_modal',
			))
		);


        /**
         * @since 1.40
         */
		$wp_customize->add_setting( 'lrm_skins[skin][current]',
			array(
				'default' => '#ffffff',
				'type' => 'option', // Optional. 'theme_mod' or 'option'. Default: 'theme_mod'
				'transport' => 'postMessage',
				//'sanitize_callback' => 'sanitize_hex_color'
			)
		);

		$wp_customize->add_control( 'lrm_skins[skin][current]',
			array(
				//'description' => esc_html__( 'Sample description' ),
				'section' => 'lrm_controls_section',
				'priority' => 10, // Optional. Order priority to load the control. Default: 10
				'capability' => 'edit_theme_options', // Optional. Default: 'edit_theme_options'

                'setting_type' => 'option',
                'setting_transport' => 'refresh',
                'sanitize_callback' => 'sanitize_text_field',

                'label' => __( 'Skin', 'fv' ),
                'type' => 'select',
                'choices' => LRM_Skins::i()->get_list(),
                'use_original_key' => true,

			)
		);
//
//		$wp_customize->add_setting( 'lrm_btn_bg',
//			array(
//				'default' => '#2f889a',
//				'type' => 'option', // Optional. 'theme_mod' or 'option'. Default: 'theme_mod'
//				'transport' => 'postMessage',
//				'sanitize_callback' => 'sanitize_hex_color'
//			)
//		);
//
//		$wp_customize->add_control( 'lrm_btn_bg',
//			array(
//				'label' => __( 'Buttons background color' ),
//				//'description' => esc_html__( 'Sample description' ),
//				'section' => 'lrm_controls_section',
//				'priority' => 10, // Optional. Order priority to load the control. Default: 10
//				'type' => 'color',
//				'capability' => 'edit_theme_options', // Optional. Default: 'edit_theme_options'
//			)
//		);


        $wp_4_9_required =  ! class_exists('WP_Customize_Code_Editor_Control') ? ' [WP 4.9.0 is required]' : '';

        /**
         * Add our Sample Section
         */
        $wp_customize->add_section( 'lrm_custom_js_section',
            array(
                'title' => __( 'Custom Javascript' ) . $wp_4_9_required,
                //'description' => esc_html__( 'Here you can customize modal styles.' ),
                'panel' => 'lrm_panel', // Only needed if adding your Section to a Panel
                'priority' => 180, // Not typically needed. Default is 160
                'capability' => 'edit_theme_options', // Not typically needed. Default is edit_theme_options
                'theme_supports' => '', // Rarely needed
                'active_callback' => '', // Rarely needed
                'description_hidden' => 'false', // Rarely needed. Default is False
            )
        );

        $wp_customize->add_setting( 'lrm_custom_js',
            array(
                'default' => '/* PUT YOUR JS HERE */',
                'type'    => 'option', // Optional. 'theme_mod' or 'option'. Default: 'theme_mod'
                //'transport' => 'postMessage',
                //'sanitize_callback' => 'sanitize_hex_color'
            )
        );

		if ( ! $wp_4_9_required ) {
	        $wp_customize->add_control( new WP_Customize_Code_Editor_Control( $wp_customize, 'lrm_custom_js', array(
		        'label'     => 'Custom Javascript (for advanced users!)',
		        'code_type' => 'text/javascript',
		        'settings'  => 'lrm_custom_js',
		        'section'   => 'lrm_custom_js_section',
	        ) ) );
        } else {
			$wp_customize->add_control( 'lrm_custom_js', array(
				'type' => 'textarea',
				'section' => 'lrm_custom_js_section', // // Add a default or your own section
				'label' => 'Custom Javascript (for advanced users!)',
			) );
        }

	}


	/**
	 * Sanitizes the incoming input and returns it prior to serialization.
	 *
	 * @param      string    $input    The string to sanitize
	 * @return     string              The sanitized string
	 */
	static function sanitize_input( $input ) {
		return strip_tags( stripslashes( $input ) );
	}

	/**
	 * Registers the Theme Customizer Preview with WordPress.
	 */
	static function customizer_live_preview() {
		wp_enqueue_script(
			'lrm-customizer',
			LRM_PRO_URL . '/assets/lrm-customizer-preview.js',
			array( 'customize-preview' ),
			LRM_PRO_VERSION,
			true
		);
	}

	/**
	 * Writes styles out the footer of the page based on the configuration options
	 * saved in the Theme Customizer.
	 *
	 * @since      1.14
     * @deprecated
	 */
	static function customizer_css() {
		?>
		<style type="text/css">
			<?php if( $btn_color = get_option( 'lrm_btn_color' ) ) : ?>
				.lrm-form button[type=submit] { color: <?php echo sanitize_hex_color($btn_color); ?>; }
			<?php endif; ?>
			<?php if( $btn_bg = get_option( 'lrm_btn_bg' ) ) : ?>
				.lrm-form button[type=submit] { background-color: <?php echo sanitize_hex_color($btn_bg); ?>; }
			<?php endif; ?>
		</style>
		<?php
	}

    /**
     * @deprecated
     */
	static function customize_controls_enqueue_scripts() {
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script( 'wp-color-picker');
    }
	
}


