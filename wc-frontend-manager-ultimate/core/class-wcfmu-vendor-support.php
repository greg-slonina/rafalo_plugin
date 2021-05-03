<?php
/**
 * WCFMu plugin core
 *
 * Plugin Vendor Support Controller
 *
 * @author 		WC Lovers
 * @package 	wcfm/core
 * @version   2.0.5
 */
 
class WCFMu_Vendor_Support {

	public function __construct() {
		global $WCFM, $WCFMu;
		
		add_filter( 'wcfm_capability_settings_fields_product_types', array( &$this, 'wcfmcap_product_types' ), 10, 3 );
		
		// Associate Store For Imported Products
		if( $WCFMu->is_marketplace && ( $WCFMu->is_marketplace == 'wcfmmarketplace' ) ) {
			add_filter( 'woocommerce_csv_product_import_mapping_options', array( &$this, 'wcfmu_product_import_vendor_options' ), 100, 2 );
			add_action( 'woocommerce_product_import_inserted_product_object', array( &$this, 'wcfmu_product_import_vendor_association' ), 100, 2 );
		}
		
		if( !is_admin() ) {
			add_filter( 'get_edit_post_link', array( &$this, 'wfm_edit_post_link' ), 100, 3 );
		}
		
		if( $is_marketplace = wcfm_is_marketplace() ) {
			// WCFMu WCMp Endpoint Edit - 2.5.3
			add_filter( 'wcfm_endpoints_slug', array( &$this, 'wcmarketplace_wcfmu_endpoints_slug' ) );
			
			if( apply_filters( 'wcfm_is_allow_commission_manage', true ) ) {
				if( $is_marketplace == 'wcvendors' ) {
					if( wcfm_is_wcvpro() ) {
						if( !wcfm_is_vendor() ) {
							add_action( 'end_wcfm_products_manage', array( &$this, 'wcvendorspro_product_commission' ), 499 );
							
							// Commision Save
							add_action( 'after_wcfm_products_manage_meta_save', array( &$this, 'wcvendorspro_product_commission_save' ), 500, 2 );
						}
					}
				}
			}
		}
		
		add_filter( 'wcfm_navigation_url', array( &$this, 'wcfmu_get_navigation_url' ), 10, 2 );
	}
	
	
	/**
	 * WCFM Capability Product Types
	 */
	function wcfmcap_product_types( $product_types, $handler = 'wcfm_capability_options', $wcfm_capability_options = array() ) {
		global $WCFM, $WCFMu;
		
		$virtual = ( isset( $wcfm_capability_options['virtual'] ) ) ? $wcfm_capability_options['virtual'] : 'no';
		$downloadable = ( isset( $wcfm_capability_options['downloadable'] ) ) ? $wcfm_capability_options['downloadable'] : 'no';
		
		$product_types["virtual"]      = array('label' => __('Virtual', 'wc-frontend-manager-ultimate') , 'name' => $handler . '[virtual]','type' => 'checkboxoffon', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $virtual);
		$product_types["downloadable"] = array('label' => __('Downloadable', 'wc-frontend-manager-ultimate') , 'name' => $handler . '[downloadable]','type' => 'checkboxoffon', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $downloadable);
		
		return $product_types;
	}
	
	/**
	 * Imported Product Store Mapping Option
	 */
	function wcfmu_product_import_vendor_options( $options, $item ) {
		$options['pwb-brand'] = __('Brand', 'perfect-woocommerce-brands');
		
		if( !wcfm_is_vendor() ) {
			$options['marketplace']  = array(
																					'name'    => __( 'Marketplace', 'wc-frontend-manager-ultimate' ),
																					'options' => array(
																						'store'     => __( 'Store', 'wc-frontend-manager-ultimate' )
																					)
																				);
		}
		
		return $options;
	}
	
	/**
	 * Imported Product Store Association
	 */
	function wcfmu_product_import_vendor_association( $product_obj, $data ) {
  	global $WCFM, $WCFMu;
  	
  	$new_product_id = $product_obj->get_id();
  	
  	if( !wcfm_is_vendor() ) {
			if( isset( $data['Store'] ) || isset( $data['store'] ) ) { 
				$update_product = array(
																'ID'           => $new_product_id,
																'post_author'  => absint( $data['store'] ),
															);
				wp_update_post( $update_product, true );
			}
		}
  	
  	if( isset( $data['pwb-brand'] ) ) {
			wp_delete_object_term_relationships( $new_product_id, 'pwb-brand' );
			$brands = explode( ',', $data['pwb-brand'] );
			foreach( $brands as $brand ) wp_set_object_terms( $new_product_id, $brand, 'pwb-brand', true );
		}
  }
	
	/**
	 * Frontend Edit Post Link for Vendors
	 */
	function wfm_edit_post_link( $link, $post_id, $context ) {
		
		$wcfm_post = get_post( $post_id );
		$post_type = $wcfm_post->post_type;
		
		switch( $post_type ) {
			case 'shop_order':
			  $link = get_wcfm_view_order_url( $post_id );	
			break;
		}
		
		return $link;
	}
	
	/**
	 * WCMp Endpoiint Edit
	 */
	function wcmarketplace_wcfmu_endpoints_slug( $endpoints ) {
		global $WCFM, $WCFMu;
		
		if( $WCFMu->is_marketplace == 'wcmarketplace' ) {
			$wcmp_endpoints = array(
														'wcfm-payments'  		   => 'payments',
														'wcfm-withdrawal'  		 => 'withdrawal',
														);
			$endpoints = array_merge( $endpoints, $wcmp_endpoints );
		}
		
		return $endpoints;
	}
	
	// WCV Pro Vendor Product Commission
	function wcvendorspro_product_commission( $product_id ) {
		global $WCFM, $WCFMu;
		
		remove_action( 'end_wcfm_products_manage', array( &$WCFM->wcfm_vendor_support, 'wcvendors_product_commission' ), 500 );
		
		$wcv_commission_type = '';
		$wcv_commission_amount = '';
		$wcv_commission_percent = '';
		$wcv_commission_fee = '';
		if( $product_id  ) {
			$wcv_commission_type = get_post_meta( $product_id, 'wcv_commission_type', true);
			$wcv_commission_amount = get_post_meta( $product_id, 'wcv_commission_amount', true);
			$wcv_commission_percent = get_post_meta( $product_id, 'wcv_commission_percent', true);
			$wcv_commission_fee = get_post_meta( $product_id, 'wcv_commission_fee', true);
		}
		?>
		<!-- collapsible 12 - WCMp Commission Support -->
		<div class="page_collapsible products_manage_commission simple variable grouped external booking" id="wcfm_products_manage_form_commission_head"><label class="wcfmfa fa-percent"></label><?php _e('Commission', 'wc-frontend-manager-ultimate'); ?><span></span></div>
		<div class="wcfm-container simple variable external grouped booking">
			<div id="wcfm_products_manage_form_commission_expander" class="wcfm-content">
				<?php
				$commission_types = WCVendors_Pro_Commission_Controller::commission_types();
				$empty = array( '' => '&nbsp;&nbsp;' );
				$commission_types = array_merge( $empty, $commission_types );
				$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'product_manage_fields_commission', array(  
																																														"wcv_commission_type" => array('label' => __('Commission Type', 'wc-frontend-manager-ultimate') , 'type' => 'select', 'options' => $commission_types,'class' => 'wcfm-select wcfm_ele simple variable external grouped booking', 'label_class' => 'wcfm_title wcfm_ele simple variable external grouped booking', 'value' => $wcv_commission_type ),
																																														"wcv_commission_percent" => array('label' => __('Commission(%)', 'wc-frontend-manager-ultimate') , 'type' => 'number', 'attributes' => array( 'min' => '', 'steps' => 1 ), 'class' => 'wcfm-text wcfm_ele simple variable external grouped booking', 'label_class' => 'wcfm_title wcfm_ele simple variable external grouped booking', 'value' => $wcv_commission_percent ),
																																														"wcv_commission_amount" => array('label' => __('Commission Fixed', 'wc-frontend-manager-ultimate') , 'type' => 'number', 'attributes' => array( 'min' => '', 'steps' => 1 ), 'class' => 'wcfm-text wcfm_ele simple variable external grouped booking', 'label_class' => 'wcfm_title wcfm_ele simple variable external grouped booking', 'value' => $wcv_commission_amount ),
																																														"wcv_commission_fee" => array('label' => __('Commission Fee', 'wc-frontend-manager-ultimate') , 'type' => 'number', 'attributes' => array( 'min' => '', 'steps' => 1 ), 'class' => 'wcfm-text wcfm_ele simple variable external grouped booking', 'label_class' => 'wcfm_title wcfm_ele simple variable external grouped booking', 'value' => $wcv_commission_fee )
																																									)) );
				?>
			</div>
		</div>
		<!-- end collapsible -->
		<div class="wcfm_clearfix"></div>
		<?php
	}
	
	// WCV Pro Vendor Product Commision Save
	function wcvendorspro_product_commission_save( $new_product_id, $wcfm_products_manage_form_data ) {
		global $WCFM, $WCFMu;
		
		if( isset( $wcfm_products_manage_form_data['wcv_commission_type'] ) && !empty( $wcfm_products_manage_form_data['wcv_commission_type'] ) ) {
			update_post_meta( $new_product_id, 'wcv_commission_type', $wcfm_products_manage_form_data['wcv_commission_type'] );
		} else {
			delete_post_meta( $new_product_id, 'wcv_commission_type' );
		}
		
		if( isset( $wcfm_products_manage_form_data['wcv_commission_percent'] ) && !empty( $wcfm_products_manage_form_data['wcv_commission_percent'] ) ) {
			update_post_meta( $new_product_id, 'wcv_commission_percent', $wcfm_products_manage_form_data['wcv_commission_percent'] );
		}
		if( isset( $wcfm_products_manage_form_data['wcv_commission_amount'] ) && !empty( $wcfm_products_manage_form_data['wcv_commission_amount'] ) ) {
			update_post_meta( $new_product_id, 'wcv_commission_amount', $wcfm_products_manage_form_data['wcv_commission_amount'] );
		}
		if( isset( $wcfm_products_manage_form_data['wcv_commission_fee'] ) && !empty( $wcfm_products_manage_form_data['wcv_commission_fee'] ) ) {
			update_post_meta( $new_product_id, 'wcv_commission_fee', $wcfm_products_manage_form_data['wcv_commission_fee'] );
		}
	}
	
	function wcfmu_get_navigation_url( $navigation_url, $endpoint ) {
		global $WCFM, $WCFMu;
		$wcfm_page = get_wcfm_page();
		
		switch( $endpoint ) {
			case 'reviews':
			  $navigation_url = wcfm_get_endpoint_url( 'wcfm-reviews', '', $wcfm_page );
			break;
			
			case 'support':
			  $navigation_url = wcfm_get_endpoint_url( 'wcfm-support', '', $wcfm_page );
			break;
		}
		
		return $navigation_url;
	}
	
	// Verified Vendor Check
  function get_wcfm_verification_badge() {
		global $WCFM, $WCFMu;
		
		$vendor_verification_options = (array) get_option( 'wcfm_vendor_verification_options' );
		
		$verification_badge = isset( $vendor_verification_options['verification_badge'] ) ? $vendor_verification_options['verification_badge'] : '';
		if( !$verification_badge ) $verification_badge = $WCFMu->plugin_url . 'assets/images/verification_badge.png';
		
		return $verification_badge;
	}
	
	function wcfm_is_verified_vendor( $vendor_id ) {
		global $WCFM, $WCFMu;
		
		$vendor_verification_data = (array) get_user_meta( $vendor_id, 'wcfm_vendor_verification_data', true );
		
		$verification_status = 'noprompt';
		if( !empty( $vendor_verification_data ) && isset( $vendor_verification_data['verification_status'] ) ) $verification_status = $vendor_verification_data['verification_status'];
		
		$social_verification_status = 'pending';
		if( !empty( $vendor_verification_data ) && isset( $vendor_verification_data['social_verification_status'] ) ) $social_verification_status = $vendor_verification_data['social_verification_status'];
		
		if( ( $verification_status == 'approve' ) && ( $social_verification_status == 'approve' ) ) return true;
		
		return false;
	}
	
	public function wcfm_show_verified_seller_badge( $vendor_id, $badge_classses, $context = 'view' ) {
		global $WCFM, $WCFMu;
		if( $vendor_id ) {
			if( $this->wcfm_is_verified_vendor( $vendor_id ) ) {
				$badge = $this->get_wcfm_verification_badge();
				if( $badge ) {
					if( $context == 'view' ) {
						echo '<div class="'.$badge_classses.' text_tip" data-tip="' . __( 'Verified Vendor', 'wc-frontend-manager-ultimate' ) . '"><img src="' . $badge . '" /></div>';
					} else {
						return '<span class="'.$badge_classses.' text_tip" data-tip="' . __( 'Verified Vendor', 'wc-frontend-manager-ultimate' ) . '"><img style="display: inline-block;" src="' . $badge . '" /></span>';
					}
				}
			}
		}
	}
}