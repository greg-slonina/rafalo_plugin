<?php

use Elementor\Controls_Manager;

class StoreInfo extends WCFM_Elementor_TagBase {

    /**
     * Tag name
     *
     * @since 1.0.0
     *
     * @return string
     */
    public function get_name() {
        return 'wcfmem-store-info';
    }

    /**
     * Tag title
     *
     * @since 1.0.0
     *
     * @return string
     */
    public function get_title() {
        return __( 'Store Info', 'wc-frontend-manager-elementor' );
    }

    /**
     * Render Tag
     *
     * @since 1.0.0
     *
     * @return void
     */
    protected function get_value() {
    	  global $WCFMem;
    	  
        $store_data = $WCFMem->get_wcfmem_store_data();

        $store_info = [
            [
                'key'         => 'address',
                'title'       => __( 'Address', 'wc-frontend-manager-elementor' ),
                'text'        => $store_data['address'],
                'icon'        => 'fa fa-map-marker',
                'show'        => true,
                '__dynamic__' => [
                    'text' => $store_data['address'],
                ]
            ],
            [
                'key'         => 'email',
                'title'       => __( 'Email', 'wc-frontend-manager-elementor' ),
                'text'        => $store_data['email'],
                'icon'        => 'fa fa-envelope-o',
                'show'        => true,
                '__dynamic__' => [
                    'text' => $store_data['email'],
                ]
            ],
            [
                'key'         => 'phone',
                'title'       => __( 'Phone No', 'wc-frontend-manager-elementor' ),
                'text'        => $store_data['phone'],
                'icon'        => 'fa fa-mobile',
                'show'        => true,
                '__dynamic__' => [
                    'text' => $store_data['phone'],
                ]
            ],
        ];

        return apply_filters( 'wcfmem_elementor_tags_store_info_value', $store_info );
    }

    protected function render() {
        echo json_encode( $this->get_value() );
    }
}
