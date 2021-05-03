<?php

/**
 * WCFMu plugin core
 *
 * Marketplace WC Marketplace Support
 *
 * @author 		WC Lovers
 * @package 	wcfmu/core
 * @version   1.1.0
 */
 
class WCFMu_WCMarketplace {
	
	private $vendor_id;
	private $vendor_term;
	
	public function __construct() {
    global $WCFM;
    
    if( wcfm_is_vendor() ) {
    	
    	$this->vendor_id   = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );
    	$this->vendor_term = get_user_meta( $this->vendor_id, '_vendor_term_id', true );
    	
    	// Manage Vendor Product Import Vendor Association - 2.4.2
			add_action( 'woocommerce_product_import_inserted_product_object', array( &$this, 'wcmarketplace_product_import_vendor_association' ), 10, 2 );
			
			// Orders Menu
			add_filter( 'wcfmu_orders_menus', array( &$this, 'wcmarketplace_orders_menu' ) );
			
			// Orders Filter
			add_action( 'before_wcfm_orders', array( &$this, 'wcmarketplace_orders_filter' ) );
			
			// Order Invoice
			add_filter( 'wcfm_order_details_shipping_line_item_invoice', array( &$this, 'wcmarketplace_is_allow_order_details_shipping_line_item_invoice' ) );
			add_filter( 'wcfm_order_details_tax_line_item_invoice', array( &$this, 'wcmarketplace_is_allow_order_details_tax_line_item_invoice' ) );
			
			// Order Notes
			add_filter( 'wcfm_order_notes', array( &$this, 'wcmarketplace_order_notes' ), 10, 2 );
			
			// WCFMu Report Menu
			add_filter( 'wcfm_reports_menus', array( &$this, 'wcmarketplace_reports_menus' ), 100 );
			
			// Report Filter
			add_filter( 'woocommerce_reports_get_order_report_data_args', array( &$this, 'wcmarketplace_reports_get_order_report_data_args'), 100 );
			add_filter( 'wcfm_report_low_in_stock_query_from', array( &$this, 'wcmarketplace_report_low_in_stock_query_from' ), 100, 3 );
			
			// Subscription Filter
			add_filter( 'wcfm_wcs_include_subscriptions', array( &$this, 'wcmarketplace_wcs_include_subscription' ) );
			
			// Booking Filter products for specific vendor
			//add_filter( 'get_booking_products_args', array( $this, 'wcmarketplace_filter_resources' ) );
			
			// Booking Filter resources for specific vendor
    	add_filter( 'get_booking_resources_args', array( $this, 'wcmarketplace_filter_resources' ), 20 );
			
			// Booking filter products from booking calendar
			add_filter( 'woocommerce_bookings_in_date_range_query', array( $this, 'wcmarketplace_filter_bookings_calendar' ) );
			
			// Appointment Filter
			add_filter( 'wcfm_wca_include_appointments', array( &$this, 'wcmarketplace_wca_include_appointments' ) );
			
			// Appointment filter products from appointment calendar
			add_filter( 'woocommerce_appointments_in_date_range_query', array( &$this, 'wcmarketplace_filter_appointments_calendar' ) );
			
			// Appointment Staffs args
			add_filter( 'get_appointment_staff_args', array( &$this, 'wcmarketplace_filter_appointment_staffs' ) );
			
			// Appointment Manage Staff
			add_action( 'wcfm_staffs_manage', array( &$this, 'wcmarketplace_wcfm_staffs_manage' ) );
			
			// Auctions Filter
			add_filter( 'wcfm_valid_auctions', array( &$this, 'wcmarketplace_wcfm_valid_auctions' ) );
			
			// Rental Request Quote Filter
			add_filter( 'wcfm_rental_include_quotes', array( &$this, 'wcmarketplace_rental_include_quotes' ) );
			
			// Settings Update
			add_action( 'wcfm_wcmarketplace_settings_update', array( &$this, 'wcmarketplace_settings_update' ), 10, 2 );
    }
  }
  
  // Product Vendor association on Product Import - 2.4.2
  function wcmarketplace_product_import_vendor_association( $product_obj ) {
  	global $WCFM, $WCFMu, $WCMp;
  	
  	$new_product_id = $product_obj->get_id();
  	
  	$vendor_term = get_user_meta( $this->vendor_id, '_vendor_term_id', true );
		$term = get_term( $vendor_term , 'dc_vendor_shop' );
		wp_delete_object_term_relationships( $new_product_id, 'dc_vendor_shop' );
		wp_set_post_terms( $new_product_id, $term->name , 'dc_vendor_shop', true );
		
		if( $product_obj->get_type() == 'product_variation' ) return;
		
		// Admin Message for Pending Review
		if( !current_user_can( 'publish_products' ) || !apply_filters( 'wcfm_is_allow_publish_products', true ) ) {
			$update_product = array(
															'ID'           => $new_product_id,
															'post_status'  => 'pending',
															'post_type'    => 'product',
														);
  		wp_update_post( $update_product, true );
			$WCFM->wcfm_notification->wcfm_admin_notification_product_review( $this->vendor_id, $new_product_id );
		}
  }
  
  // Orders Menu
  function wcmarketplace_orders_menu( $menus ) {
  	return array();
  }
  
  // Orders Filter
  function wcmarketplace_orders_filter() {
  	global $WCFM, $WCFMu, $wpdb, $wp_locale;
  	?>
  	<h2><?php _e('Orders Listing', 'wc-frontend-manager' ); ?></h2>
		<div class="wcfm_orders_filter_wrap wcfm_filters_wrap">
			<?php $WCFM->library->wcfm_date_range_picker_field(); ?>
			
			<select name="commission-status" id="commission-status" style="width: 150px;">
				<option value=''><?php esc_html_e( 'Show all', 'wc-frontend-manager-ultimate' ); ?></option>
				<option value="unpaid"><?php esc_html_e( 'Unpaid', 'wc-frontend-manager-ultimate' ); ?></option>
				<option value="paid"><?php esc_html_e( 'Paid', 'wc-frontend-manager-ultimate' ); ?></option>
				<option value="reversed"><?php esc_html_e( 'Reversed', 'wc-frontend-manager-ultimate' ); ?></option>
			</select>
		</div>
  	<?php
  }
  
  // Order Details Shipping Line Item Invoice
  function wcmarketplace_is_allow_order_details_shipping_line_item_invoice( $allow ) {
  	global $WCFM, $WCMp;
  	if ( !$WCMp->vendor_caps->vendor_payment_settings('give_shipping') ) $allow = false;
  	return $allow;
  }
  
  // Order Details Tax Line Item Invoice
  function wcmarketplace_is_allow_order_details_tax_line_item_invoice( $allow ) {
  	global $WCFM, $WCMp;
  	if ( !$WCMp->vendor_caps->vendor_payment_settings('give_tax') ) $allow = false;
  	return $allow;
  }
  
  // Order Notes
  function wcmarketplace_order_notes( $notes, $order_id ) {
  	$order    = wc_get_order( $order_id );
		$notes = $order->get_customer_order_notes();
  	return $notes;
  }
  
  // Filter Comment User as Vendor
  public function filter_wcfm_vendors_comment( $commentdata, $order ) {
		$user_id = $this->vendor_id;
		$vendor = get_wcmp_vendor( $this->vendor_id );
		$vendor_data = get_userdata( $user_id );
		
		$commentdata[ 'user_id' ]              = $user_id;
		$commentdata[ 'comment_author' ]       = get_user_meta( $user_id , '_vendor_page_title', true);
		$commentdata[ 'comment_author_url' ]   = $vendor->permalink;
		$commentdata[ 'comment_author_email' ] = $vendor_data->user_email;

		return $commentdata;
	}
  
	/**
	 * WCFMu Reports Menu
	 */
	function wcmarketplace_reports_menus( $reports_menus ) {
		global $WCFM, $WCFMu;
		
		unset($reports_menus['coupons-by-date']);
		return $reports_menus;
	}
	
	// Report Data args filter as per vendor
  function wcmarketplace_reports_get_order_report_data_args( $args ) {
  	global $WCFM, $wpdb, $_POST, $wp;
  	
  	if ( !isset( $wp->query_vars['wcfm-reports-sales-by-product'] ) ) return $args;
  	if( $args['query_type'] != 'get_results' ) return $args;
  	
  	$user_id = $this->vendor_id;
  	
  	$products = $WCFM->wcfm_marketplace->wcmarketplace_get_vendor_products();
		if( empty($products) ) return array(0);
		
		$args['where'][] = array( 'key' => 'order_item_meta__product_id.meta_value', 'operator' => 'in', 'value' => $products );
  	
  	return $args;
  }
	
	// Report Vendor Filter
  function wcmarketplace_report_low_in_stock_query_from( $query_from, $stock, $nostock ) {
  	global $WCFM, $wpdb, $_POST;
  	
  	$user_id = $this->vendor_id;
  	
  	$query_from = "FROM {$wpdb->posts} as posts
			INNER JOIN {$wpdb->postmeta} AS postmeta ON posts.ID = postmeta.post_id
			INNER JOIN {$wpdb->postmeta} AS postmeta2 ON posts.ID = postmeta2.post_id
			WHERE 1=1
			AND posts.post_type IN ( 'product', 'product_variation' )
			AND posts.post_status = 'publish'
			AND posts.post_author = {$user_id}
			AND postmeta2.meta_key = '_manage_stock' AND postmeta2.meta_value = 'yes'
			AND postmeta.meta_key = '_stock' AND CAST(postmeta.meta_value AS SIGNED) <= '{$stock}'
			AND postmeta.meta_key = '_stock' AND CAST(postmeta.meta_value AS SIGNED) > '{$nostock}'
		";
		
		return $query_from;
  }
  
  /**
   * WC Marketplace Subscription
   */
  function wcmarketplace_wcs_include_subscription( ) {
  	global $WCFM, $WCFMu, $wpdb, $_POST;
  	
  	$products = $WCFM->wcfm_vendor_support->wcfm_get_products_by_vendor( $this->vendor_id );
		if( empty($products) ) return array(0);
		
		if( wcfm_is_xa_subscription() ) {
			$vendor_subscriptions_arr = hforce_get_subscriptions_for_product( array_keys( $products ) );
		} else {
			$vendor_subscriptions_arr = wcs_get_subscriptions_for_product( array_keys( $products ) );
		}
		if( !empty($vendor_subscriptions_arr) ) return $vendor_subscriptions_arr;
		return array(0);
  }
  
  // Filter resources for specific vendor
  function wcmarketplace_filter_resources( $query_args ) {
		unset($query_args['post__in']);
		$query_args['author'] = $this->vendor_id;	
  	return $query_args;
  }
  
  /**
	 * Filter products booking calendar to specific vendor
	 *
	 * @since 2.2.6
	 * @param array $booking_ids booking ids
	 * @return array
	 */
	public function wcmarketplace_filter_bookings_calendar( $booking_ids ) {
		global $WCFM;
		
		$filtered_ids = array();
		
		$product_ids = $WCFM->wcfm_marketplace->wcmarketplace_get_vendor_products();
		
		if ( ! empty( $product_ids ) ) {
			foreach ( $booking_ids as $id ) {
				$booking = get_wc_booking( $id );

				if ( in_array( $booking->product_id, $product_ids ) ) {
					$filtered_ids[] = $id;
				}
			}

			$filtered_ids = array_unique( $filtered_ids );

			return $filtered_ids;
		} else {
			return array();
		}

		return $booking_ids;
	}
	
	/**
   * WC Marketplace Appointments
   */
  function wcmarketplace_wca_include_appointments( ) {
  	global $WCFM, $WCFMu, $wpdb, $_POST;
  	
		$products = $WCFM->wcfm_marketplace->wcmarketplace_get_vendor_products();
		if( empty($products) ) return array(0);
		
  	$query = "SELECT ID FROM {$wpdb->posts} as posts
							INNER JOIN {$wpdb->postmeta} AS postmeta ON posts.ID = postmeta.post_id
							WHERE 1=1
							AND posts.post_type IN ( 'wc_appointment' )
							AND postmeta.meta_key = '_appointment_product_id' AND postmeta.meta_value in (" . implode(',', $products) . ")";
		
		$vendor_appointments = $wpdb->get_results($query);
		if( empty($vendor_appointments) ) return array(0);
		$vendor_appointments_arr = array();
		foreach( $vendor_appointments as $vendor_appointment ) {
			$vendor_appointments_arr[] = $vendor_appointment->ID;
		}
		if( !empty($vendor_appointments_arr) ) return $vendor_appointments_arr;
		return array(0);
  }
  
  /**
	 * Filter products appointment calendar to specific vendor
	 *
	 * @since 2.4.0
	 * @param array $appointment_ids appointment ids
	 * @return array
	 */
	public function wcmarketplace_filter_appointments_calendar( $appointment_ids ) {
		global $WCFM;
		
		$filtered_ids = array();
		
		$product_ids = $WCFM->wcfm_marketplace->wcmarketplace_get_vendor_products();
		
		if ( ! empty( $product_ids ) ) {
			foreach ( $appointment_ids as $id ) {
				$appointment = get_wc_appointment( $id );

				if ( in_array( $appointment->product_id, $product_ids ) ) {
					$filtered_ids[] = $id;
				}
			}

			$filtered_ids = array_unique( $filtered_ids );

			return $filtered_ids;
		} else {
			return array();
		}

		return $appointment_ids;
	}
	
	// WCMp Filter Staffs
	function wcmarketplace_filter_appointment_staffs( $args ) {
		$args['meta_key'] = '_wcfm_vendor';
		$args['meta_value'] = $this->vendor_id;
		return $args;
	}
	
	// WCMp Appointment Staff Manage
	function wcmarketplace_wcfm_staffs_manage( $staff_id ) {
		update_user_meta( $staff_id, '_wcfm_vendor', $this->vendor_id );
	}
	
	// WCMp Valid Auction
	function wcmarketplace_wcfm_valid_auctions( $valid_actions ) {
		global $WCFM, $WCFMu;
		
		$valid_actions = $WCFM->wcfm_marketplace->wcmarketplace_get_vendor_products();
		if( empty($valid_actions) ) return array(0);
		
		return $valid_actions; 
	}
	
	/**
   * WC Marketplace Rental Quotes
   */
	function wcmarketplace_rental_include_quotes( ) {
  	global $WCFM, $wpdb, $_POST;
  	
  	$products = $WCFM->wcfm_marketplace->wcmarketplace_get_vendor_products();
		if( empty($products) ) return array(0);
		
  	$query = "SELECT ID FROM {$wpdb->posts} as posts
							INNER JOIN {$wpdb->postmeta} AS postmeta ON posts.ID = postmeta.post_id
							WHERE 1=1
							AND posts.post_type IN ( 'request_quote' )
							AND postmeta.meta_key = 'add-to-cart' AND postmeta.meta_value in (" . implode(',', $products) . ")";
		
		$vendor_quotes = $wpdb->get_results($query);
		if( empty($vendor_quotes) ) return array(0);
		$vendor_quotes_arr = array();
		foreach( $vendor_quotes as $vendor_quote ) {
			$vendor_quotes_arr[] = $vendor_quote->ID;
		}
		if( !empty($vendor_quotes_arr) ) return $vendor_quotes_arr;
		return array(0);
  }
	
  // WCMp Settings Update
  function wcmarketplace_settings_update( $user_id, $wcfm_settings_form ) {
  	global $WCFM, $WCFMu, $wpdb, $_POST;
  	
  	update_user_meta( $user_id, 'wcfm_vacation_mode', isset( $wcfm_settings_form['wcfm_vacation_mode'] ) ? 'yes' : 'no' );
  	update_user_meta( $user_id, 'wcfm_disable_vacation_purchase', isset( $wcfm_settings_form['wcfm_disable_vacation_purchase'] ) ? 'yes' : 'no' );
  	update_user_meta( $user_id, 'wcfm_vacation_mode_type', $wcfm_settings_form['wcfm_vacation_mode_type'] );
  	update_user_meta( $user_id, 'wcfm_vacation_start_date', $wcfm_settings_form['wcfm_vacation_start_date'] );
  	update_user_meta( $user_id, 'wcfm_vacation_end_date', $wcfm_settings_form['wcfm_vacation_end_date'] );
		update_user_meta( $user_id, 'wcfm_vacation_mode_msg', $wcfm_settings_form['wcfm_vacation_mode_msg'] );
  }
}