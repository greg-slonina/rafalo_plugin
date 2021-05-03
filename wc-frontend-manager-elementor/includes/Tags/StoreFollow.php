<?php

class StoreFollow extends WCFM_Elementor_TagBase {

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
        return 'wcfmem-store-follow-tag';
    }

    /**
     * Tag title
     *
     * @since 1.0.0
     *
     * @return string
     */
    public function get_title() {
        return __( 'Store Follow Button', 'wc-frontend-manager-elementor' );
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
    	
        if( !apply_filters( 'wcfm_is_pref_vendor_followers', true ) || !apply_filters( 'wcfmmp_is_allow_store_header_follow', true ) ) {
            echo __( 'Follower module is not active', 'wc-frontend-manager-elementor' );
            return;
        }
        
        if ( wcfmmp_is_store_page() ) {
        	$vendor_id = $WCFMem->get_wcfmem_store_data( 'id' );
        	
        	$user_id = 0;
        	$is_following = false;
        	if( is_user_logged_in() ) {
						$user_id = get_current_user_id();
						$user_following_arr = get_user_meta( $user_id, '_wcfm_following_list', true );
						if( $user_id == $vendor_id ) $is_following = true;
						if( $user_following_arr && is_array( $user_following_arr ) && in_array( $vendor_id, $user_following_arr ) ) {
							$is_following = true;
						}
						if( $user_id && !$is_following ) {
							echo apply_filters( 'wcfm_store_follow_button_label', __( 'Follow', 'wc-frontend-manager-ultimate' ) );
						} else {
							echo apply_filters( 'wcfm_store_unfollow_button_label', __( 'Un-follow', 'wc-frontend-manager-ultimate' ) );
						}
					} else {
						echo apply_filters( 'wcfm_store_follow_button_label', __( 'Follow', 'wc-frontend-manager-ultimate' ) );
					}
        } else {
        	echo apply_filters( 'wcfm_store_follow_button_label', __( 'Follow', 'wc-frontend-manager-ultimate' ) );
        }
    }
}
