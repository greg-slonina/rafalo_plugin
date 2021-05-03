<?php

/**
 * WCFMu plugin core
 *
 * Booking WC Booking Accommodation Support
 *
 * @author 		WC Lovers
 * @package 	wcfmu/core
 * @version   2.4.4
 */
 
class WCFM_WCAccommodation {
	
	public function __construct() {
    global $WCFM, $WCFMu;
    
    if( $wcfm_is_allow_accommodation = apply_filters( 'wcfm_is_allow_accommodation' , true ) ) {
			if( wcfm_is_booking() ) {
				if ( current_user_can( 'manage_bookings_settings' ) || current_user_can( 'manage_bookings' ) ) {
					// Booking Accommodation Product Type
					add_filter( 'wcfm_product_types', array( &$this, 'wcba_product_types' ), 30 );
					
					// Booking Accommodation Product Type Capability
					add_filter( 'wcfm_capability_settings_fields_product_types', array( &$this, 'wcfmcap_product_types' ), 30, 3 );
					
					// Booking Accommodation General Block
					add_action( 'after_wcfm_products_manage_general', array( &$this, 'wcba_product_manage_general' ), 20, 2 );
					
					// Booking Accommodation Product Manage View
					add_action( 'end_wcfm_products_manage', array( &$this, 'wcba_wcfm_products_manage_form_load_views' ), 20 );
					
					// Bookings Accommodation Ajax Controllers
					add_action( 'after_wcfm_ajax_controller', array( &$this, 'wcba_ajax_controller' ) );
				}
			}
		}
  }
  
  /**
   * WC Booking Accommodation Product Type
   */
  function wcba_product_types( $pro_types ) {
  	global $WCFM;
  	if ( current_user_can( 'manage_bookings_settings' ) || current_user_can( 'manage_bookings' ) ) {
  		$pro_types['accommodation-booking'] = __( 'Accommodation product', 'woocommerce-accommodation-bookings' );
  	}
  	
  	return $pro_types;
  }
  
  /**
	 * WCFM Capability Product Types
	 */
	function wcfmcap_product_types( $product_types, $handler = 'wcfm_capability_options', $wcfm_capability_options = array() ) {
		global $WCFM, $WCFMu;
		
		$accommodation = ( isset( $wcfm_capability_options['accommodation'] ) ) ? $wcfm_capability_options['accommodation'] : 'no';
		
		$product_types["accommodation"] = array('label' => __('Accommodation', 'wc-frontend-manager-ultimate') , 'name' => $handler . '[accommodation]','type' => 'checkboxoffon', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $accommodation);
		
		return $product_types;
	}
	
  /**
   * WC Booking Accommodation Product General Options
   */
  function wcba_product_manage_general( $product_id, $product_type ) {
  	global $WCFM, $WCFMu;
  	
  	$min_duration = 1;
  	$max_duration = 7;
  	$calendar_display_mode = '';
  	$requires_confirmation = 'no';
  	$user_can_cancel = 'no';
  	$cancel_limit = 1;
  	$cancel_limit_unit = '';
  	
  	if( $product_id ) {
  		$min_duration = absint( get_post_meta( $product_id, '_wc_booking_min_duration', true ) );
  		$max_duration = absint( get_post_meta( $product_id, '_wc_booking_max_duration', true ) );
  		$calendar_display_mode = get_post_meta( $product_id, '_wc_booking_calendar_display_mode', true );
  		$requires_confirmation = get_post_meta( $product_id, '_wc_booking_requires_confirmation', true );
			$user_can_cancel = get_post_meta( $product_id, '_wc_booking_user_can_cancel', true );
			$cancel_limit = max( absint( get_post_meta( $product_id, '_wc_booking_cancel_limit', true ) ), 1 );
			$cancel_limit_unit = get_post_meta( $product_id, '_wc_booking_cancel_limit_unit', true );
  	}
  	?>
  	<!-- collapsible Accommodation 1 -->
	  <div class="page_collapsible products_manage_downloadable accommodation-booking" id="wcfm_products_manage_form_accommodation_options_head"><label class="wcfmfa fa-calendar"></label><?php _e('Accommodation', 'woocommerce-accommodation-bookings'); ?><span></span></div>
		<div class="wcfm-container accommodation-booking">
			<div id="wcfm_products_manage_form_accommodation_expander" class="wcfm-content">
			  <?php
					$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_accommodation_booking_general_fields', array(  
						
						"_wc_accommodation_booking_min_duration" => array( 'label' => __( 'Minimum number of nights allowed in a booking', 'woocommerce-accommodation-bookings' ), 'type' => 'number', 'class' => 'wcfm-text wcfm_ele accommodation-booking', 'label_class' => 'wcfm_title accommodation-booking', 'value' => $min_duration ),
						"_wc_accommodation_booking_max_duration" => array( 'label' => __( 'Maximum number of nights allowed in a booking', 'woocommerce-accommodation-bookings' ), 'type' => 'number', 'class' => 'wcfm-text wcfm_ele accommodation-booking', 'label_class' => 'wcfm_title accommodation-booking', 'value' => $max_duration ),
						"_wc_accommodation_booking_calendar_display_mode" => array( 'label' => __( 'Calendar display mode', 'woocommerce-accommodation-bookings' ), 'type' => 'select', 'options' => array( '' => __( 'Display calendar on click', 'woocommerce-accommodation-bookings'), 'always_visible' => __( 'Calendar always visible', 'woocommerce-accommodation-bookings' ) ), 'class' => 'wcfm-select wcfm_ele accommodation-booking', 'label_class' => 'wcfm_title accommodation-booking', 'value' => $calendar_display_mode ),
						"_wc_accommodation_booking_requires_confirmation" => array('label' => __('Requires confirmation?', 'woocommerce-accommodation-bookings') , 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele accommodation-booking', 'label_class' => 'wcfm_title checkbox_title accommodation-booking', 'value' => 'yes', 'dfvalue' => $requires_confirmation, 'hints' => __( 'Check this box if the booking requires admin approval/confirmation. Payment will not be taken during checkout.', 'woocommerce-accommodation-bookings' ) ),
						"_wc_accommodation_booking_user_can_cancel" => array('label' => __('Can be cancelled?', 'woocommerce-accommodation-bookings') , 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele accommodation-booking', 'label_class' => 'wcfm_title checkbox_title accommodation-booking', 'value' => 'yes', 'dfvalue' => $user_can_cancel, 'hints' => __( 'Check this box if the booking can be cancelled by the customer after it has been purchased. A refund will not be sent automatically.', 'woocommerce-accommodation-bookings' ) ),
						"_wc_accommodation_booking_cancel_limit" => array('label' => __('Cancellation up till', 'woocommerce-accommodation-bookings') , 'type' => 'number', 'class' => 'wcfm-text wcfm_ele accommodation-booking', 'label_class' => 'wcfm_title accommodation-booking', 'value' => $cancel_limit ),
						"_wc_accommodation_booking_cancel_limit_unit" => array('type' => 'select', 'options' => array( 'month' => __( 'Month(s)', 'woocommerce-accommodation-bookings'), 'day' => __( 'Day(s)', 'woocommerce-accommodation-bookings' ), 'hour' => __( 'Hour(s)', 'woocommerce-accommodation-bookings' ), 'minute' => __( 'Minute(s)', 'woocommerce-accommodation-bookings' ) ), 'class' => 'wcfm-select wcfm_ele accommodation-booking', 'label_class' => 'wcfm_title accommodation-booking', 'value' => $cancel_limit_unit, 'desc' => __( 'before check-in.', 'woocommerce-accommodation-bookings' ) )
						
																															), $product_id ) );
			  
			  ?>
		  </div>
		</div>
		<!-- end collapsible Accommodation -->
		<div class="wcfm_clearfix"></div>
  	<?php
  }
  
  /**
   * WC Booking Accommodation load views
   */
  function wcba_wcfm_products_manage_form_load_views( ) {
		global $WCFM, $WCFMu;
	  
	 $WCFMu->template->get_template( 'wc_bookings/wcfmu-view-wcaccommodation-products-manage.php' );
	}
	
	/**
   * WC Booking Accommodation Ajax Controllers
   */
  public function wcba_ajax_controller() {
  	global $WCFM, $WCFMu;
  	
  	$controllers_path = $WCFMu->plugin_path . 'controllers/wc_bookings/';
  	
  	$controller = '';
  	if( isset( $_POST['controller'] ) ) {
  		$controller = $_POST['controller'];
  		
  		switch( $controller ) {
  			case 'wcfm-products-manage':
  				include_once( $controllers_path . 'wcfmu-controller-wcaccommodation-products-manage.php' );
					new WCFMu_WCAccommodation_Products_Manage_Controller();
  			break;
  		}
  	}
  }
}