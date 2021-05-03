<?php

class WCFM_Elementor_StoreTabContents extends WCFM_Elementor_StoreName {

    /**
     * Widget name
     *
     * @since 1.0.0
     *
     * @return string
     */
    public function get_name() {
        return 'wcfmem-store-tab-contents';
    }

    /**
     * Widget title
     *
     * @since 1.0.0
     *
     * @return string
     */
    public function get_title() {
        return __( 'Store Tab Contents', 'wc-frontend-manager-elementor' );
    }

    /**
     * Widget icon class
     *
     * @since 1.0.0
     *
     * @return string
     */
    public function get_icon() {
        return 'eicon-products';
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
        return [ 'wcfm', 'store', 'vendor', 'tab', 'content', 'products' ];
    }

    /**
     * Register widget controls
     *
     * @since 1.0.0
     *
     * @return void
     */
    protected function _register_controls() {
    	  global $WCFM, $WCFMem;
    	  
        $this->add_control(
            'products',
            [
                'type' => WCFM_Elementor_DynamicHidden::CONTROL_TYPE,
                'dynamic' => [
                    'active' => true,
                    'default' => $WCFMem->wcfmem_elementor()->dynamic_tags->tag_data_to_tag_text( null, 'wcfmem-store-dummy-products' ),
                ]
            ],
            [
                'position' => [ 'of' => '_title' ],
            ]
        );
    }

    /**
     * Set wrapper classes
     *
     * @since 1.0.0
     *
     * @return void
     */
    protected function get_html_wrapper_class() {
        return parent::get_html_wrapper_class() . ' wcfmem-store-tab-content elementor-widget-' . parent::get_name();
    }

    /**
     * Frontend render method
     *
     * @since 1.0.0
     *
     * @return void
     */
    protected function render() {
    	  global $WCFM, $WCFMmp, $WCFMem;
        if ( wcfmmp_is_store_page() ) {
        	$store_id = 0;
        	
        	$store_user = wcfmmp_get_store( get_query_var( 'author' ) );

					if ( $store_user->id ) {
						$store_id = $store_user->id;
					}
					
					if( $store_id ) {
						$store_info = $store_user->get_shop_info();
						
						$store_tab = 'products';
						if ( get_query_var( $WCFMmp->wcfmmp_rewrite->store_endpoint('about') ) ) {
							$store_tab = 'about';
						} elseif ( get_query_var( $WCFMmp->wcfmmp_rewrite->store_endpoint('policies') ) ) {
							$store_tab = 'policies';
						} elseif ( get_query_var( $WCFMmp->wcfmmp_rewrite->store_endpoint('reviews') ) ) {
							$store_tab = 'reviews';
						} elseif ( get_query_var( $WCFMmp->wcfmmp_rewrite->store_endpoint('followers') ) ) {
							$store_tab = 'followers';
						} elseif ( get_query_var( $WCFMmp->wcfmmp_rewrite->store_endpoint('followings') ) ) {
							$store_tab = 'followings';
						} elseif ( get_query_var( $WCFMmp->wcfmmp_rewrite->store_endpoint('articles') ) ) {
							$store_tab = 'articles';
						} else {
							$store_tab = 'products';
						}
						
						switch( $store_tab ) {
							case 'about':
								$WCFMmp->template->get_template( 'store/wcfmmp-view-store-about.php', array( 'store_user' => $store_user, 'store_info' => $store_info ) );
								break;
								
							case 'policies':
								$WCFMmp->template->get_template( 'store/wcfmmp-view-store-policies.php', array( 'store_user' => $store_user, 'store_info' => $store_info ) );
								break;
								
							case 'reviews':
								$WCFMmp->template->get_template( 'store/wcfmmp-view-store-reviews.php', array( 'store_user' => $store_user, 'store_info' => $store_info ) );
								break;
								
							case 'followers':
								$WCFMmp->template->get_template( 'store/wcfmmp-view-store-followers.php', array( 'store_user' => $store_user, 'store_info' => $store_info ) );
								break;
								
							case 'followings':
								$WCFMmp->template->get_template( 'store/wcfmmp-view-store-followings.php', array( 'store_user' => $store_user, 'store_info' => $store_info ) );
								break;
								
						  case 'articles':
								$WCFMmp->template->get_template( 'store/wcfmmp-view-store-articles.php', array( 'store_user' => $store_user, 'store_info' => $store_info ) );
								break;
								
							default:
								// Post per Page
								$post_per_page = 12;
								if( apply_filters( 'wcfmmp_is_allow_store_ppp', true ) ) {
									$global_store_ppp = isset( $WCFMmp->wcfmmp_marketplace_options['store_ppp'] ) ? $WCFMmp->wcfmmp_marketplace_options['store_ppp'] : get_option( 'posts_per_page', 12 );
									$post_per_page = isset( $store_info['store_ppp'] ) && !empty( $store_info['store_ppp'] ) ? $store_info['store_ppp'] : $global_store_ppp;
									$post_per_page = apply_filters( 'wcfmmp_store_ppp', $post_per_page );
								}
								
								// Category Filter
								$category = '';
								if( get_query_var( 'term_section' ) ) {
									$category = get_query_var( 'term' );
								}
								
								$search_ids = '';
								if( get_query_var( 's' ) ) {
									$search_term    = wc_clean( wp_unslash( get_query_var( 's' ) ) );
									if( !empty( $search_term ) ) {
										$data_store     = WC_Data_Store::load( 'product' );
										$search_ids     = $data_store->search_products( $search_term, '', true, true );
										if( !empty( $search_ids ) ) {
											$search_ids = implode( ',', $search_ids );
										} else {
											$search_ids = '0';
										}
									}
								}
								
								echo do_shortcode( apply_filters( 'wcfmem_store_products_display', '[products store="'.$store_id.'" ids="'.$search_ids.'" limit="'.$post_per_page.'" category="'.$category.'" paginate="true"]', $store_id, $search_ids, $post_per_page, $category ) );
								//$WCFMmp->template->get_template( apply_filters( 'wcfmmp_store_default_template', apply_filters( 'wcfmp_store_default_template', 'store/wcfmmp-view-store-products.php', $store_tab ), $store_tab ), array( 'store_user' => $store_user, 'store_info' => $store_info ), '', apply_filters( 'wcfmp_store_default_template_path', '', $store_tab ) );
								break;
						}	
					}
        } else {
            $settings = $this->get_settings_for_display();

            echo $settings['products'];
        }
    }

    /**
     * Elementor builder content template
     *
     * @since 1.0.0
     *
     * @return void
     */
    protected function _content_template() {
        ?>
            <#
                print( settings.products );
            #>
        <?php
    }
}
