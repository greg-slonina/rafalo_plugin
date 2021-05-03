<?php

class StoreInquiry extends WCFM_Elementor_TagBase {

    /**
     * Class constructor
     *
     * @since 1.0.0
     *
     * @param array $data
     */
    public function __construct( $data = [] ) {
        parent::__construct( $data );
    }

    /**
     * Tag name
     *
     * @since 1.0.0
     *
     * @return string
     */
    public function get_name() {
        return 'wcfmem-store-inquiry-tag';
    }

    /**
     * Tag title
     *
     * @since 1.0.0
     *
     * @return string
     */
    public function get_title() {
        return __( 'Store Inquiry Button', 'wc-frontend-manager-elementor' );
    }

    /**
     * Render tag
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function render() {
    		global $WCFM, $WCFMem;
    	
        if( !apply_filters( 'wcfm_is_pref_enquiry', true ) || !apply_filters( 'wcfm_is_pref_enquiry_button', true ) || !apply_filters( 'wcfmmp_is_allow_store_header_enquiry', true ) ) {
            echo __( 'Inquiry module is not active', 'wc-frontend-manager-elementor' );
            return;
        }

        $wcfm_enquiry_button_label = __( 'Inquiry', 'wc-frontend-manager' );

        if( apply_filters( 'wcfm_is_allow_store_inquiry_custom_button_label', false ) ) {
					$wcfm_options = $WCFM->wcfm_options;
					$wcfm_enquiry_button_label  = isset( $wcfm_options['wcfm_enquiry_button_label'] ) ? $wcfm_options['wcfm_enquiry_button_label'] : __( 'Inquiry', 'wc-frontend-manager' );
				}

        echo $wcfm_enquiry_button_label;
    }
}
