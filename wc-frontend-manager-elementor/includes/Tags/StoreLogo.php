<?php

use Elementor\Controls_Manager;

class StoreLogo extends WCFM_Elementor_DataTagBase {

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
        return 'wcfmem-store-logo';
    }

    /**
     * Tag title
     *
     * @since 1.0.0
     *
     * @return string
     */
    public function get_title() {
        return __( 'Store Logo', 'wc-frontend-manager-elementor' );
    }

    /**
     * Store profile picture
     *
     * @since 1.0.0
     *
     * @return void
     */
    protected function get_value( array $options = [] ) {
    		global $WCFM, $WCFMem;
    		
        $picture = $WCFMem->get_wcfmem_store_data( 'logo' );

        if ( empty( $picture['id'] ) ) {
            $settings = $this->get_settings();

            if ( ! empty( $settings['fallback']['id'] ) ) {
                $picture = $settings['fallback'];
            }
        }

        return $picture;
    }

    /**
     * Register tag controls
     *
     * @since 1.0.0
     *
     * @return void
     */
    protected function _register_controls() {
    		global $WCFM, $WCFMem;
    		
        $this->add_control(
            'fallback',
            [
                'label' => __( 'Fallback', 'wc-frontend-manager-elementor' ),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'id'  => 0,
                    'url' => $WCFMem->plugin_url . 'assets/images/default-logo.png',
                ]
            ]
        );
    }
}
