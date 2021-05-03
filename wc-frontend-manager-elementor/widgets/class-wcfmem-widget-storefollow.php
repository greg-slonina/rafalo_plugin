<?php

use Elementor\Controls_Manager;
use Elementor\Widget_Button;

class WCFM_Elementor_StoreFollow extends Widget_Button {

    /**
     * Widget name
     *
     * @since 1.0.0
     *
     * @return string
     */
    public function get_name() {
        return 'wcfmem-store-follow';
    }

    /**
     * Widget title
     *
     * @since 1.0.0
     *
     * @return string
     */                                                  
    public function get_title() {
        return __( 'Store Follow Button', 'wc-frontend-manager-elementor' );
    }

    /**
     * Widget icon class
     *
     * @since 1.0.0
     *
     * @return string
     */
    public function get_icon() {
        return 'fa fa-child';
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
        return [ 'wcfm', 'store', 'vendor', 'button', 'follower', 'follow', 'following' ];
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
                    'default' => $WCFMem->wcfmem_elementor()->dynamic_tags->tag_data_to_tag_text( null, 'wcfmem-store-follow-tag' ),
                    'active'  => true,
                ],
                'selectors' => [
                    '{{WRAPPER}} > .elementor-widget-container > .elementor-button-wrapper > .wcfm-store-follow-btn' => 'width: auto; margin: 0;',
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
        return parent::get_button_wrapper_class() . ' wcfmem-store-follow-wrap';
    }
    /**
     * Button class
     *
     * @since 1.0.0
     *
     * @return string
     */
    protected function get_button_class() {
        return 'wcfmem-store-follow';
    }

    /**
     * Render button
     *
     * @since 1.0.0
     *
     * @return void
     */
    protected function render() {
			global $WCFM, $WCFMem, $WCFMu, $WCFMmp;
			
			if( !WCFM_Dependencies::wcfmu_plugin_active_check() ) {
				return;
			}
			
			if( !apply_filters( 'wcfm_is_pref_vendor_followers', true ) || !apply_filters( 'wcfmmp_is_allow_store_header_follow', true ) ) {
				return;
			}
			
			if ( ! wcfmmp_is_store_page() ) {
				return;
			}
			
			$vendor_id = $WCFMem->get_wcfmem_store_data( 'id' );
			
			if( !$vendor_id ) return;
			
			$followers = 0;
			$followers_arr = get_user_meta( $vendor_id, '_wcfm_followers_list', true );
			if( $followers_arr && is_array( $followers_arr ) ) {
				$followers = count( $followers_arr );
			}
	
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
					$this->add_render_attribute( 'button', 'class', 'wcfm_follow_me' );
					$this->add_render_attribute( 'button', 'class', 'wcfmem_follow_now' );
					$this->add_render_attribute( 'button', 'data-count', $followers );
					$this->add_render_attribute( 'button', 'data-vendor_id', $vendor_id );
					$this->add_render_attribute( 'button', 'data-user_id', $user_id );
					?>
					<script>
						jQuery(document).ready(function($) {
							$('.wcfmem_follow_now').click(function(event) {
								event.preventDefault();
								
								$user_id   = $(this).data('user_id');
								$vendor_id = $(this).data('vendor_id');
								$count     = $(this).data('count');
								
								$('#wcfmem_store_header').block({
									message: null,
									overlayCSS: {
										background: '#fff',
										opacity: 0.6
									}
								});
								var data = {
									action    : 'wcfmu_vendors_followers_update',
									user_id   : $user_id,
									vendor_id : $vendor_id,
									count     : $count
								}	
								$.post(wcfm_params.ajax_url, data, function(response) {
									if(response) {
										$count = $count + 1;
										$('.wcfm_followers_count').text( $count );
										$('.wcfmem_follow_now').hide();
										$('#wcfmem_store_header').unblock();
										window.location = window.location.href;
									}
								});
								
								return false;
							});
						});
					</script>
					<?php 
				} else {
					$this->add_render_attribute( 'button', 'class', 'wcfm_follow_me' );
					$this->add_render_attribute( 'button', 'class', 'wcfm_followings_delete' );
					$this->add_render_attribute( 'button', 'data-userid', $vendor_id );
					$this->add_render_attribute( 'button', 'data-followersid', $user_id );
					?>
					<?php
					wp_enqueue_script( 'wcfmu_my_account_followings_js', $WCFMu->library->js_lib_url . 'followers/wcfmu-script-my-account-followings.js', array('jquery'), $WCFMu->version, true );
					$wcfm_dashboard_messages = get_wcfm_dashboard_messages();
					wp_localize_script( 'wcfmu_my_account_followings_js', 'wcfm_dashboard_messages', $wcfm_dashboard_messages );
				}
			} else {
				$this->add_render_attribute( 'button', 'class', 'wcfm_follow_me' );
				$this->add_render_attribute( 'button', 'class', 'wcfm_login_popup' );
			}
			
			parent::render();
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
        <i class="wcfmfa fa-user-plus"></i>
        <span class="wcfmem-store-inquiry-button-label-current"><?php echo $wcfm_enquiry_button_label; ?></span>

        <?php
    }*/
}
