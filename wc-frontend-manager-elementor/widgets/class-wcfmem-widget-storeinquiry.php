<?php

use Elementor\Controls_Manager;
use Elementor\Widget_Button;

class WCFM_Elementor_StoreInquiry extends Widget_Button {

    /**
     * Widget name
     *
     * @since 1.0.0
     *
     * @return string
     */
    public function get_name() {
        return 'wcfmem-store-inquiry';
    }

    /**
     * Widget title
     *
     * @since 1.0.0
     *
     * @return string
     */                                                  
    public function get_title() {
        return __( 'Store Inquiry Button', 'wc-frontend-manager-elementor' );
    }

    /**
     * Widget icon class
     *
     * @since 1.0.0
     *
     * @return string
     */
    public function get_icon() {
        return 'fa fa-question';
    }
    
    /**
     * Widget categories
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function get_categories() {
        return [ 'wcfmem-store-elements-single' ];
    }

    /**
     * Widget keywords
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function get_keywords() {
        return [ 'wcfm', 'store', 'vendor', 'button', 'inquiry', 'enquiry', 'support' ];
    }

    /**
     * Register widget controls
     *
     * @since 1.0.0
     *
     * @return void
     */
    protected function _register_controls() {
    	  global $WCFMem;
    	  
        parent::_register_controls();
        
        $this->update_control(
            'icon_align',
            [
                'default' => 'left',
            ]
        );

        $this->update_control(
            'button_text_color',
            [
                'default' => '#ffffff',
            ]
        );

        $this->update_control(
            'background_color',
            [
                'default' => '#17a2b8',
            ]
        );

        $this->update_control(
            'border_color',
            [
                'default' => '#17a2b8',
            ]
        );

        $this->update_control(
            'text',
            [
                'dynamic'   => [
                    'default' => $WCFMem->wcfmem_elementor()->dynamic_tags->tag_data_to_tag_text( null, 'wcfmem-store-inquiry-tag' ),
                    'active'  => true,
                ],
                'selectors' => [
                    '{{WRAPPER}} > .elementor-widget-container > .elementor-button-wrapper > .wcfm-store-inquiry-btn' => 'width: auto; margin: 0;',
                ]
            ]
        );
        
        //$this->remove_control( 'link' );

        $this->update_control(
            'link',
									[
										'type' => Controls_Manager::URL,
										'default' => [
											'is_external' => 'true',
										],
										'dynamic' => [
											'active' => false,
										],
										'placeholder' => __( 'No link required.', 'elementor' ),
									]
        );
    }

    /**
     * Button wrapper class
     *
     * @since 1.0.0
     *
     * @return string
     */
    protected function get_button_wrapper_class() {
        return parent::get_button_wrapper_class() . ' wcfmem-store-inquiry-wrap';
    }
    /**
     * Button class
     *
     * @since 1.0.0
     *
     * @return string
     */
    protected function get_button_class() {
        return 'wcfmem-store-inquiry';
    }

    /**
     * Render button
     *
     * @since 1.0.0
     *
     * @return void
     */
    protected function render() {
    	  global $WCFM, $WCFMem;
    	  
				if( !apply_filters( 'wcfm_is_pref_enquiry', true ) || !apply_filters( 'wcfm_is_pref_enquiry_button', true ) || !apply_filters( 'wcfmmp_is_allow_store_header_enquiry', true ) ) {
					return;
				}
				
				if ( ! wcfmmp_is_store_page() ) {
					return;
        }
				
				$this->add_render_attribute( 'button', 'class', 'wcfm_catalog_enquiry' );
				if( !is_user_logged_in() && apply_filters( 'wcfm_is_allow_enquiry_with_login', false ) ) { $this->add_render_attribute( 'button', 'class', 'wcfm_login_popup' ); }
				$this->add_render_attribute( 'button', 'data-store', $WCFMem->get_wcfmem_store_data( 'id' ) );

				parent::render();
        //echo do_shortcode( '[wcfm_inquiry]' );
    }
    
    /**
     * Render button text.
     *
     * Render button widget text.
     *
     * @since 1.0.3
     * @access protected
     */
    /*protected function render_text() {
    	  global $WCFM, $WCFMem;
    	  
    	  if( !apply_filters( 'wcfm_is_pref_enquiry', true ) || !apply_filters( 'wcfm_is_pref_enquiry_button', true ) || !apply_filters( 'wcfmmp_is_allow_store_header_enquiry', true ) ) {
					return;
				}
    	  
        if ( ! wcfmmp_is_store_page() ) {
            parent::render_text();
            return;
        }

        $settings = $this->get_settings_for_display();

        $this->add_render_attribute( [
            'content-wrapper' => [
                'class' => 'elementor-button-content-wrapper',
            ],
            'icon-align' => [
                'class' => [
                    'elementor-button-icon',
                    'elementor-align-icon-' . $settings['icon_align'],
                ],
            ],
            'text' => [
                'class' => 'elementor-button-text',
            ],
        ] );

        $this->add_inline_editing_attributes( 'text', 'none' );
        
        $wcfm_enquiry_button_label = __( 'Inquiry', 'wc-frontend-manager' );

        if( apply_filters( 'wcfm_is_allow_store_inquiry_custom_button_label', false ) ) {
					$wcfm_options = $WCFM->wcfm_options;
					$wcfm_enquiry_button_label  = isset( $wcfm_options['wcfm_enquiry_button_label'] ) ? $wcfm_options['wcfm_enquiry_button_label'] : __( 'Inquiry', 'wc-frontend-manager' );
				}
				
        ?>
        <span class="wcfmem-store-inquiry-button-label-current"><?php echo $wcfm_enquiry_button_label; ?></span>

        <?php
    }*/
}
