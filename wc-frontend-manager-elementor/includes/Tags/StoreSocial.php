<?php

class StoreSocial extends WCFM_Elementor_TagBase {

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
        return 'wcfmem-store-social-tag';
    }

    /**
     * Tag title
     *
     * @since 1.0.0
     *
     * @return string
     */
    public function get_title() {
        return __( 'Store Social', 'wc-frontend-manager-elementor' );
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
    	  
        $links       = [];
        $network_map = $WCFMem->get_social_networks_map();

        if ( wcfmmp_is_store_page() ) {
        		$store_id = 0;
            $store_user       = wcfmmp_get_store( get_query_var( 'author' ) );
            
            if ( $store_user->id ) {
							$store_id = $store_user->id;
						}
						
						if( $store_id ) {
							$store_info = $store_user->get_shop_info();
            
							$social_info = isset( $store_info['social'] ) ? $store_info['social'] : array();
							
							foreach ( $network_map as $wcfm_name => $elementor_name ) {
									if ( ! empty( $social_info[ $wcfm_name ] ) ) {
											$links[ $elementor_name ] = wcfmmp_generate_social_url( $social_info[ $wcfm_name ], $wcfm_name );
									}
							}
							
						}
        } else {
            foreach ( $network_map as $wcfm_name => $elementor_name ) {
                $links[ $elementor_name ] = '#';
            }
        }

        echo json_encode( $links );
    }
}
