<?php

use Elementor\Controls_Manager;

class StoreBanner extends WCFM_Elementor_DataTagBase {

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
        return 'wcfmem-store-banner';
    }

    /**
     * Tag title
     *
     * @since 1.0.0
     *
     * @return string
     */
    public function get_title() {
        return __( 'Store Banner', 'wc-frontend-manager-elementor' );
    }

    /**
     * Tag categories
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function get_categories() {
        return [ \Elementor\Modules\DynamicTags\Module::IMAGE_CATEGORY ];
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
    	  
        $banner = $WCFMem->get_wcfmem_store_data( 'banner' );
        
        if ( empty( $banner['id'] ) ) {
            $settings = $this->get_settings();

            if ( ! empty( $settings['fallback']['id'] ) ) {
                $banner = $settings['fallback'];
            }
        }

        return $banner;
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
                    'url' => $WCFMem->plugin_url . 'assets/images/default-banner.jpg',
                ]
            ]
        );
    }
}
