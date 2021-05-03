<?php
/**
 * WCFMu plugin core
 *
 * Plugin Vendor Badges Controler
 *
 * @author 		WC Lovers
 * @package 	wcfmu/core
 * @version   3.5.3
 */
 
class WCFMu_Vendor_Badges {
	
	public $wcfm_vendor_badges_options = array();
	
	public function __construct() {
		global $WCFM, $WCFMu;
		
		$this->wcfm_vendor_badges_options = get_option( 'wcfm_vendor_badges_options', array() );
		
		// Badges Settings
		add_action( 'end_wcfm_settings', array( &$this, 'wcfmu_vendor_badges_settings' ), 17 );
		add_action( 'wcfm_settings_update', array( &$this, 'wcfmu_vendor_badges_settings_update' ), 17 );
		
		// Membership Badges Association
		add_action( 'wcfm_membership_badges', array( &$this, 'wcfmu_membership_badges_manage' ) );
		add_action( 'wcfm_memberships_manage_from_process', array( &$this, 'wcfmu_membership_badges_update' ),10, 2 );
		
		// Vendor Manager Badges Assign
		add_action( 'after_wcfm_vendor_membership_details', array( &$this, 'wcfmu_vendor_badges_manage' ) );
		
		// Show Badges with Membership Description
		add_action( 'after_wcfm_membership_description_content', array( &$this, 'show_wcfm_membership_badges' ) );
		
		// Show verified seller badge
		add_filter( 'wcfm_dashboard_after_username', array( &$this, 'after_wcfm_dashboard_user' ), 11 );
		
		if( $WCFMu->is_marketplace == 'wcmarketplace' ) {
			add_action( 'before_wcmp_vendor_information', array( &$this, 'before_wcmp_vendor_information' ), 15 );
			add_action( 'after_sold_by_text_shop_page', array( &$this, 'after_sold_by_text_shop_page'), 15 );
			//add_action( 'woocommerce_after_shop_loop_item', array( &$this, 'template_loop_seller_badges' ), 90 );
			add_action( 'after_wcmp_singleproductmultivendor_vendor_name', array( &$this, 'wcmp_singleproductmultivendor_table_name' ), 15, 2 );
		} elseif( $WCFMu->is_marketplace == 'wcvendors' ) {
			if( version_compare( WCV_VERSION, '2.0.0', '<' ) ) {
				if ( WC_Vendors::$pv_options->get_option( 'sold_by' ) ) { 
					add_action( 'woocommerce_after_shop_loop_item', array( &$this, 'template_loop_seller_badges' ), 9 );
				}
			} else {
				if ( get_option('wcvendors_display_label_sold_by_enable') ) { 
					add_action( 'woocommerce_after_shop_loop_item', array( &$this, 'template_loop_seller_badges' ), 9 );
				}
			}
			//add_filter( 'wcvendors_cart_sold_by', array( &$this, 'after_wcv_cart_sold_by' ), 15, 3 );
			add_filter( 'wcvendors_cart_sold_by_meta', array( &$this, 'after_wcv_cart_sold_by' ), 15, 3 );
			if( WCFM_Dependencies::wcvpro_plugin_active_check() ) {
				add_action( 'wcv_after_vendor_store_title', array( &$this, 'after_wcv_pro_store_header' ), 15 );
			} else {
				add_action( 'wcv_after_main_header', array( &$this, 'after_wcv_store_header' ), 15 );
				add_action( 'wcv_after_mini_header', array( &$this, 'after_wcv_store_header' ), 15 );
			}
		} elseif( $WCFMu->is_marketplace == 'wcpvendors' ) {
			add_filter( 'wcpv_sold_by_link_name', array( &$this, 'wcpv_sold_by_link_name_seller_badges' ), 15, 3 );
		} elseif( $WCFMu->is_marketplace == 'dokan' ) {
			add_action( 'dokan_store_header_info_fields',  array( &$this, 'after_dokan_store_header' ), 15 );
			//add_filter( 'woocommerce_product_tabs', array( &$this, 'dokan_product_tab_seller_badges' ), 9 );
		} elseif( $WCFMu->is_marketplace == 'wcfmmarketplace' ) {
			add_action( 'wcfmmp_single_product_sold_by_badges', array( &$this, 'after_wcfmmp_sold_by_label_product_page'), 15 );
			//add_action( 'after_wcfmmp_sold_by_label_product_page', array( &$this, 'after_wcfmmp_sold_by_label_product_page'), 15 );
			add_action( 'wcfmmp_store_mobile_badges', array( &$this, 'after_wcfmmp_sold_by_label_product_page'), 15 );
			add_action( 'wcfmmp_store_desktop_badges', array( &$this, 'after_wcfmmp_sold_by_label_product_page'), 15 );
			add_action( 'after_wcfmmp_store_list_rating', array( &$this, 'after_wcfmmp_sold_by_label_product_page'), 15 );
			//add_action( 'after_wcmp_singleproductmultivendor_vendor_name', array( &$this, 'wcmp_singleproductmultivendor_table_name' ), 15, 2 );
		}
		
	}
	
	function wcfmu_vendor_badges_settings( $wcfm_options ) {
		global $WCFM, $WCFMu;
		$wcfm_vendor_badges_options = get_option( 'wcfm_vendor_badges_options', array() );
		?>
		<!-- collapsible -->
		<div class="page_collapsible" id="wcfm_settings_form_vendor_badges_head">
			<label class="wcfmfa fa-certificate"></label>
			<?php echo apply_filters( 'wcfm_sold_by_label', '', __( 'Vendor', 'wc-frontend-manager' ) ) . ' ' . __('Badges', 'wc-frontend-manager-ultimate'); ?><span></span>
		</div>
		<div class="wcfm-container">
			<div id="wcfm_settings_form_vendor_badges_expander" class="wcfm-content">
			  <h2><?php echo apply_filters( 'wcfm_sold_by_label', '', __( 'Vendor', 'wc-frontend-manager' ) ) . ' ' . __('Badges', 'wc-frontend-manager-ultimate'); ?></h2>
				<?php wcfm_video_tutorial( 'https://wclovers.com/knowledgebase/wcfm-vendor-badges/' ); ?>
				<div class="wcfm_clearfix"></div>
				<?php
					$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfmu_settings_fields_vendor_badges', array(
																																										"wcfm_vendor_badges_options" => array('label' => __('Badges', 'wc-frontend-manager-ultimate') , 'type' => 'multiinput', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title', 'value' => $wcfm_vendor_badges_options, 'desc' => sprintf( __( 'You may create any type of custom badges for your vendors. <a target="_blank" href="%s">Know more.</a>', 'wc-frontend-manager-ultimate' ), 'https://wclovers.com/knowledgebase/wcfm-vendor-badges/' ), 'options' => array(
																																																"is_active" => array('label' => __('Enable', 'wc-frontend-manager-ultimate'), 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'label_class' => 'wcfm_title checkbox_title wcfm_ele', 'value' => 'yes' ),
																																																"badge_icon" => array('label' => __('Badge Icon', 'wc-frontend-manager-ultimate'), 'type' => 'upload', 'class' => 'wcfm_ele', 'prwidth' => 64, 'label_class' => 'wcfm_title wcfm_ele', 'hints' => __( 'Upload badge image 32x32 size for best view.', '' ) ),
																																																"badge_name" => array('label' => __('Badge Name', 'wc-frontend-manager-ultimate'), 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'hints' => __( 'Name of the badge visible as tooltip.', 'wc-frontend-manager-ultimate' ) ),
																																											) ) ) ) );
			  ?>
			</div>
		</div>
		<div class="wcfm_clearfix"></div>
		<!-- end collapsible -->
		
		<?php
		
	}
	
	function wcfmu_vendor_badges_settings_update( $wcfm_settings_form ) {
		global $WCFM, $WCFMu, $_POST;
		
		if( isset( $wcfm_settings_form['wcfm_vendor_badges_options'] ) ) {
			$wcfm_vendor_badges_options = $wcfm_settings_form['wcfm_vendor_badges_options'];
			update_option( 'wcfm_vendor_badges_options',  $wcfm_vendor_badges_options );
		}
	}
	
	/**
	 * Membership Badges Manage
	 */
	function wcfmu_membership_badges_manage( $membership_id ) {
		global $WCFM, $WCFMu;
		
		if( empty( $this->wcfm_vendor_badges_options ) ) {
			printf( __( 'There is no badges yet to be configured! <a target="_blank" href="%s">Know more.</a>', 'wc-frontend-manager-ultimate' ), 'https://wclovers.com/knowledgebase/wcfm-vendor-badges/' );
		} else {
			$wcfm_membership_badges = array();
			if( $membership_id ) {
				$wcfm_membership_badges = get_post_meta( $membership_id, 'wcfm_membership_badges', true );
				if( !$wcfm_membership_badges ) $wcfm_membership_badges = array();
			}
			
			foreach( $this->wcfm_vendor_badges_options as $badge_key => $wcfm_vendor_badges_option ) {
				if( isset( $wcfm_vendor_badges_option['is_active'] ) && !empty( $wcfm_vendor_badges_option['badge_name'] ) ) {
					$WCFM->wcfm_fields->wcfm_generate_form_field( array(  
																													"wcfm_membership_badges_".$badge_key => array( 'label' => '<img src="' . wcfm_get_attachment_url( $wcfm_vendor_badges_option['badge_icon'] ) . '" style="width: 32px; margin-right: 5px; display: inline-block;">'.$wcfm_vendor_badges_option['badge_name'], 'name' => 'wcfm_membership_badges['.$badge_key.']', 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'label_class' => 'wcfm_title checkbox_title', 'value' => 'yes', 'dfvalue' => ( isset( $wcfm_membership_badges[$badge_key] ) ? 'yes' : 'no' ) )
																													) );
				}
			}
		}
	}
	
	/**
	 * Membership Badges Update
	 */
	function wcfmu_membership_badges_update( $new_membership_id, $wcfm_membership_manager_form_data ) {
		global $WCFM, $WCFMu;
		
		if( isset( $wcfm_membership_manager_form_data['wcfm_membership_badges'] ) ) {
			update_post_meta( $new_membership_id, 'wcfm_membership_badges', $wcfm_membership_manager_form_data['wcfm_membership_badges'] );
		} else {
			update_post_meta( $new_membership_id, 'wcfm_membership_badges', array() );
		}
	}
	
	function wcfmu_vendor_badges_manage( $vendor_id ) {
		global $WCFM, $WCFMu;
		
		$disable_vendor = get_user_meta( $vendor_id, '_disable_vendor', true );
		if( $disable_vendor ) return;
		
		if( empty( $this->wcfm_vendor_badges_options ) ) return;
		
		$wcfm_vendor_badges = $this->get_wcfm_vendor_badges( $vendor_id );
		
		?>
		<!-- collapsible - Badges -->
		<div class="page_collapsible vendor_manage_badges" id="wcfm_vendor_manage_form_badges_head"><label class="wcfmfa fa-certificate"></label><?php _e( 'Badges', 'wc-frontend-manager-ultimate' ); ?><span></span></div>
		<div class="wcfm-container">
			<div id="wcfm_vendor_manage_form_badges_expander" class="wcfm-content">
				<div class="wcfm_vendor_badges_show">
			    <?php
			    $this->show_wcfm_vendor_badges( $vendor_id, true );
			    if( empty( $wcfm_vendor_badges ) ) {
			    	_e( 'There is no custom badges yet for this vendor!', 'wc-frontend-manager-ultimate' );
			    }
			    ?>
			    <a href="#" class="wcfm_vendor_badges_manage_link">
			      <?php _e( 'Manage vendor badges!', 'wc-frontend-manager-ultimate' ); ?>
			    </a>
				</div>
				<div class="wcfm_vendor_badges_manage">
				  <form id="wcfm_vendor_manage_badges_form" class="wcfm">
						<?php 
						foreach( $this->wcfm_vendor_badges_options as $badge_key => $wcfm_vendor_badges_option ) {
							if( isset( $wcfm_vendor_badges_option['is_active'] ) && !empty( $wcfm_vendor_badges_option['badge_name'] ) ) {
								$WCFM->wcfm_fields->wcfm_generate_form_field( array(  
																																"wcfm_vendor_badges_".$badge_key => array( 'label' => '<img src="' . wcfm_get_attachment_url( $wcfm_vendor_badges_option['badge_icon'] ) . '" style="width: 32px; margin-right: 5px; display: inline-block;">'.$wcfm_vendor_badges_option['badge_name'], 'name' => 'wcfm_vendor_badges['.$badge_key.']', 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'label_class' => 'wcfm_title checkbox_title', 'value' => 'yes', 'dfvalue' => ( isset( $wcfm_vendor_badges[$badge_key] ) ? 'yes' : 'no' ) )
																																) );
							}
						}
						$WCFM->wcfm_fields->wcfm_generate_form_field(  array( "vendor_id" => array( 'type' => 'hidden', 'value' => $vendor_id ) ) );
						?>
						<div class="wcfm-clearfix"></div>
						<div class="wcfm-message" tabindex="-1"></div>
						<div class="wcfm-clearfix"></div>
						<div id="wcfm_badges_submit">
							<input type="submit" name="save-data" value="<?php _e( 'Update', 'wc-frontend-manager' ); ?>" id="wcfm_vendor_badges_save_button" class="wcfm_submit_button" />
						</div>
						<div class="wcfm-clearfix"></div>
					</form>
				</div>
			</div>
		</div>
		<div class="wcfm_clearfix"></div><br />
		<!-- end collapsible - Badges -->
		<?php
	}
	
	/**
	 * Return Badges for a Vendor
	 */
	function get_wcfm_vendor_badges( $vendor_id ) {
		global $WCFM, $WCFMu;
		
		$wcfm_vendor_badges = array();
		if( !$vendor_id ) return $wcfm_vendor_badges;
		
		$wcfm_membership_badges = array();
		$wcfm_membership_id = get_user_meta( $vendor_id, 'wcfm_membership', true );
		if( $wcfm_membership_id ) {
			$wcfm_membership_badges = get_post_meta( $wcfm_membership_id, 'wcfm_membership_badges', true );
			if( !$wcfm_membership_badges ) $wcfm_membership_badges = array();
		}
		
		$wcfm_vendor_badges = get_user_meta( $vendor_id, 'wcfm_vendor_badges', true );
		if( !$wcfm_vendor_badges ) $wcfm_vendor_badges = $wcfm_membership_badges;
		
		return $wcfm_vendor_badges;
	}
	
	/**
	 * Display vendor Badges
	 */
	public function show_wcfm_vendor_badges( $vendor_id = 0, $is_large = false ) {
		global $WCFM, $WCFMu;
		
		if( empty( $this->wcfm_vendor_badges_options ) ) return;
		
		$is_large = apply_filters( 'wcfm_is_allow_vendor_badges_large', $is_large );
		
		if( $vendor_id ) {
			$wcfm_vendor_badges = $this->get_wcfm_vendor_badges( $vendor_id );
			$badge_classses = 'wcfm_vendor_badge';
			if( $is_large ) $badge_classses .= ' wcfm_vendor_badge_large';
			echo '<div class="wcfm_vendor_badges">';
			do_action( 'before_wcfm_vendor_badges', $vendor_id, $badge_classses );
			if( !empty( $wcfm_vendor_badges ) ) {
				foreach( $this->wcfm_vendor_badges_options as $badge_key => $wcfm_vendor_badges_option ) {
					if( isset( $wcfm_vendor_badges_option['is_active'] ) && !empty( $wcfm_vendor_badges_option['badge_name'] ) && isset( $wcfm_vendor_badges[$badge_key] ) ) {
						echo '<div class="'.$badge_classses.' text_tip"  data-tip="' . $wcfm_vendor_badges_option['badge_name'] . '"><img src="' . wcfm_get_attachment_url( $wcfm_vendor_badges_option['badge_icon'] ) . '" /></div>';
					}
				}
			}
			do_action( 'after_wcfm_vendor_badges', $vendor_id, $badge_classses );
			echo '</div>';
		}
	}
	
	/**
	 * Show Badges with Memebrship Description
	 */
	function show_wcfm_membership_badges( $membership_id ) {
		if( apply_filters( 'wcfm_is_allow_badges_in_membership_box', true ) ) {
			$wcfm_vendor_badges_options = get_option( 'wcfm_vendor_badges_options', array() );
			if( !empty( $wcfm_vendor_badges_options ) ) {
				$wcfm_membership_badges = get_post_meta( $membership_id, 'wcfm_membership_badges', true );
				if( !$wcfm_membership_badges ) $wcfm_membership_badges = array();
				if( !empty( $wcfm_membership_badges ) ) {
					echo '<div class="wcfm_vendor_badges">';
					foreach( $wcfm_vendor_badges_options as $badge_key => $wcfm_vendor_badges_option ) {
						if( isset( $wcfm_vendor_badges_option['is_active'] ) && !empty( $wcfm_vendor_badges_option['badge_name'] ) && isset( $wcfm_membership_badges[$badge_key] ) ) {
							echo '<div class="wcfm_vendor_badge wcfm_vendor_badge_large text_tip"  data-tip="' . $wcfm_vendor_badges_option['badge_name'] . '"><img src="' . wcfm_get_attachment_url( $wcfm_vendor_badges_option['badge_icon'] ) . '" /></div>';
						}
					}
					echo '</div>';
				}
			}
		}
	}
	
	function after_wcfm_dashboard_user( $vendor_id ) {
		global $WCFM, $WCFMu;
		if( empty( $this->wcfm_vendor_badges_options ) ) return;
		
		if( !$vendor_id ) {
			$vendor_id   = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );
		}
		$wcfm_vendor_badges = $this->get_wcfm_vendor_badges( $vendor_id );
		if( !empty( $wcfm_vendor_badges ) ) {
			foreach( $this->wcfm_vendor_badges_options as $badge_key => $wcfm_vendor_badges_option ) {
				if( isset( $wcfm_vendor_badges_option['is_active'] ) && !empty( $wcfm_vendor_badges_option['badge_name'] ) && isset( $wcfm_vendor_badges[$badge_key] ) ) {
					echo '<img class="wcfm_vendor_badge text_tip"  data-tip="' . $wcfm_vendor_badges_option['badge_name'] . '" src="' . wcfm_get_attachment_url( $wcfm_vendor_badges_option['badge_icon'] ) . '" />';
				}
			}
		}
	}
	
	function before_wcmp_vendor_information( $vendor_id ) {
		global $WCFM, $WCFMu;
		$this->show_wcfm_vendor_badges( $vendor_id, true );
	}
	
	function wcmp_singleproductmultivendor_table_name( $product_id, $morevendor ) {
		global $WCFM, $WCFMu;
		if( $product_id ) {
			$vendor_id = wcfm_get_vendor_id_by_post( $product_id );
			$this->show_wcfm_vendor_badges( $vendor_id );
		}
	}

	function after_sold_by_text_shop_page( $vendor ) {
		global $WCFM, $WCFMu;
		if( $vendor ) {
			if( $vendor->id ) {
				if( apply_filters( 'wcfm_is_allow_badges_in_loop', true ) ) {
					$this->show_wcfm_vendor_badges( $vendor->id );
				}
			}
		}
	}
	
	function template_loop_seller_badges( $product_id ) {
		global $WCFM, $WCFMu;
		if( $product_id ) {
			if( apply_filters( 'wcfm_is_allow_badges_in_loop', true ) ) {
				$vendor_id = wcfm_get_vendor_id_by_post( $product_id );
				$this->show_wcfm_vendor_badges( $vendor_id );
			}
		}
	}
	
	function after_wcv_pro_store_header() {
		global $WCFM, $WCFMu;
		
		$vendor_id = 0;
		if ( WCV_Vendors::is_vendor_page() ) { 
			$vendor_shop 		= urldecode( get_query_var( 'vendor_shop' ) );
			$vendor_id   		= WCV_Vendors::get_vendor_id( $vendor_shop ); 
		} else {
			global $product; 
			$post = get_post( $product->get_id() ); 
			if ( WCV_Vendors::is_vendor_product_page( $post->post_author ) )  { 
				$vendor_id   		= $post->post_author; 
			}
		}
		
		$this->show_wcfm_vendor_badges( $vendor_id, true );
	}
	
	function after_wcv_store_header( $vendor_id ) {
		global $WCFM, $WCFMu;
		$this->show_wcfm_vendor_badges( $vendor_id, true );
	}
	
	function after_wcv_cart_sold_by( $sold_by_label, $product_id, $vendor_id ) {
		global $WCFM, $WCFMu;
		if( apply_filters( 'wcfm_is_allow_badges_in_loop', true ) ) {
			$this->show_wcfm_vendor_badges( $vendor_id );
		}
		return $sold_by_label;
	}
	
	function dokan_product_tab_seller_badges( $tabs ) {
		global $WCFM, $WCFMu;
		
		if( empty( $this->wcfm_vendor_badges_options ) ) return $tabs;
		
		remove_filter( 'woocommerce_product_tabs', 'dokan_seller_product_tab' );
		
		$tabs['seller'] = array(
        'title'    => __( 'Vendor Info', 'dokan-lite' ),
        'priority' => 90,
        'callback' => array( &$this, 'wcfm_dokan_product_seller_tab' )
    );

    return $tabs;
	}
	
	/**
	 * Prints seller info in product single page
	 *
	 * @global WC_Product $product
	 * @param type $val
	 */
	function wcfm_dokan_product_seller_tab( $val ) {
		global $product;

		$vendor_id  = get_post_field( 'post_author', $product->get_id() );
		$author     = get_user_by( 'id', $vendor_id );
		$store_info = dokan_get_store_info( $author->ID );
		
		if( $vendor_id ) {
			$wcfm_vendor_badges = $this->get_wcfm_vendor_badges( $vendor_id );
			$author->display_name .= '<div class="wcfm_vendor_badges">';
			$author->display_name = apply_filters( 'before_dokan_wcfm_vendor_badges', $author->display_name, $vendor_id, 'wcfm_vendor_badge wcfm_vendor_badge_large' );
			if( !empty( $wcfm_vendor_badges ) ) {
				foreach( $this->wcfm_vendor_badges_options as $badge_key => $wcfm_vendor_badges_option ) {
					if( isset( $wcfm_vendor_badges_option['is_active'] ) && !empty( $wcfm_vendor_badges_option['badge_name'] ) && isset( $wcfm_vendor_badges[$badge_key] ) ) {
						$author->display_name .= '<div class="wcfm_vendor_badge wcfm_vendor_badge_large text_tip"  data-tip="' . $wcfm_vendor_badges_option['badge_name'] . '"><img src="' . wcfm_get_attachment_url( $wcfm_vendor_badges_option['badge_icon'] ) . '" /></div>';
					}
				}
			}
			$author->display_name .= '</div>';
		}
		
		dokan_get_template_part('global/product-tab', '', array(
				'author' => $author,
				'store_info' => $store_info,
		) );
	}
	
	function after_dokan_store_header( $vendor_id ) {
		global $WCFM, $WCFMu;
		echo '<li class="dokan-store-badges">';
		$this->show_wcfm_vendor_badges( $vendor_id, true );
		echo '</li>';
	}
	
	function wcpv_sold_by_link_name_seller_badges( $name, $product_id, $term ) {
		global $WCFM, $WCFMu;
		
		if( empty( $this->wcfm_vendor_badges_options ) ) return $name;
		
		$vendor_id = wcfm_get_vendor_id_by_post( $product_id );
		if( $vendor_id ) {
			$vendor_admin_id = 0;
			$vendor_data = WC_Product_Vendors_Utils::get_vendor_data_by_id( $vendor_id );
			
			if( is_array( $vendor_data['admins'] ) ) {
				$admin_ids = array_map( 'absint', $vendor_data['admins'] );
			} else {
				$admin_ids = array_filter( array_map( 'absint', explode( ',', $vendor_data['admins'] ) ) );
			}
			foreach( $admin_ids as $admin_id ) {
				if( $admin_id ) {
					if ( WC_Product_Vendors_Utils::is_admin_vendor( $admin_id ) ) {
						$vendor_admin_id = $admin_id;
						break;
					}
				}
			}
			if( $vendor_admin_id ) {
				$wcfm_vendor_badges = $this->get_wcfm_vendor_badges( $vendor_admin_id );
				$name .= '<div class="wcfm_vendor_badges">';
				$name = apply_filters( 'before_wcv_wcfm_vendor_badges', $name, $vendor_admin_id, 'wcfm_vendor_badge' );
				if( !empty( $wcfm_vendor_badges ) ) {
					foreach( $this->wcfm_vendor_badges_options as $badge_key => $wcfm_vendor_badges_option ) {
						if( isset( $wcfm_vendor_badges_option['is_active'] ) && !empty( $wcfm_vendor_badges_option['badge_name'] ) && isset( $wcfm_vendor_badges[$badge_key] ) ) {
							$name .= '<div class="wcfm_vendor_badge text_tip"  data-tip="' . $wcfm_vendor_badges_option['badge_name'] . '"><img src="' . wcfm_get_attachment_url( $wcfm_vendor_badges_option['badge_icon'] ) . '" /></div>';
						}
					}
				}
				$name .= '</div>';
			}
		}
		return $name;
	}
	
	function after_wcfmmp_sold_by_label_product_page( $vendor_id ) {
		global $WCFM, $WCFMu;
		if( $vendor_id ) {
			if( apply_filters( 'wcfm_is_allow_badges_in_loop', true ) ) {
				$this->show_wcfm_vendor_badges( $vendor_id );
			}
		}
	}
}