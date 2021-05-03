<?php

/**
 * WCFMu plugin core
 *
 * WCFM Multivendor Marketplace Support
 *
 * @author 		WC Lovers
 * @package 	wcfmu/core
 * @version   5.0.0
 */
 
class WCFMu_Marketplace {
	
	public $vendor_id;
	
	public function __construct() {
    global $WCFM;
    
    if( wcfm_is_vendor() ) {
    	
    	$this->vendor_id   = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );
    	
    	// Manage Vendor Product Import Vendor Association - 2.4.2
    	//add_filter( 'woocommerce_product_import_process_item_data', array( &$this, 'wcfmmp_product_import_vendor_limit_validation' ) );
			add_action( 'woocommerce_product_import_inserted_product_object', array( &$this, 'wcfmmp_product_import_vendor_association' ), 10, 2 );
    	
			// Orders Menu
			//add_filter( 'wcfmu_orders_menus', array( &$this, 'wcfmmp_orders_menu' ) );
			
			// Order Invoice
			add_filter( 'wcfm_order_details_shipping_line_item_invoice', array( &$this, 'wcfmmp_is_allow_order_details_shipping_line_item_invoice' ) );
			add_filter( 'wcfm_order_details_tax_line_item_invoice', array( &$this, 'wcfmmp_is_allow_order_details_tax_line_item_invoice' ) );
			//add_filter( 'wcfm_invoice_order_total_column_width', array( &$this, 'wcfmmp_invoice_order_total_column_width' ) );
			//add_filter( 'wcfm_order_details_total_earning_invoice', array( &$this, 'wcfmmp_order_details_total_earning_invoice' ) );
			
			// Order Notes
			add_filter( 'wcfm_order_notes_args', array( &$this, 'wcfmmp_order_notes_args' ), 10 );
			add_filter( 'wcfm_order_notes', array( &$this, 'wcfmmp_order_notes' ), 10, 2 );
			
			// WCFMu Report Menu
			add_filter( 'wcfm_reports_menus', array( &$this, 'wcfmmp_reports_menus' ), 100 );
			
			// Report Filter
			add_filter( 'woocommerce_reports_get_order_report_data_args', array( &$this, 'wcfmmp_reports_get_order_report_data_args'), 100 );
			add_filter( 'wcfm_report_low_in_stock_query_from', array( &$this, 'wcfmmp_report_low_in_stock_query_from' ), 100, 3 );
			
			// Subscription Filter
			add_filter( 'wcfm_wcs_include_subscriptions', array( &$this, 'wcfmmp_wcs_include_subscription' ) );
			
			// Booking Filter resources for specific vendor
    	add_filter( 'get_booking_resources_args', array( $this, 'wcfmmp_filter_resources' ), 20 );
    	
			// Booking filter products from booking calendar
			add_filter( 'woocommerce_bookings_in_date_range_query', array( $this, 'wcfmmp_filter_bookings_calendar' ) );
			
			// Appointment Filter
			add_filter( 'wcfm_wca_include_appointments', array( &$this, 'wcfmmp_wca_include_appointments' ) );
			
			// Appointment filter products from appointment calendar
			add_filter( 'woocommerce_appointments_in_date_range_query', array( $this, 'wcfmmp_filter_appointments_calendar' ) );
			
			// Appointment Staffs args
			add_filter( 'get_appointment_staff_args', array( &$this, 'wcfmmp_filter_appointment_staffs' ) );
			
			// Appointment Manage Staff
			add_action( 'wcfm_staffs_manage', array( &$this, 'wcfmmp_wcfm_staffs_manage' ) );
			
			// Auctions Filter
			add_filter( 'wcfm_valid_auctions', array( &$this, 'wcfmmp_wcfm_valid_auctions' ) );
			
			// FooEvents Filter
			add_filter( 'wcfm_fooevents_args', array( $this, 'wcfmmp_fooevents_args' ), 20 );
			
			// FooEvents Tickets Filter
			add_filter( 'wcfm_event_tickets_args', array( $this, 'wcfmmp_event_tickets_args' ), 20 );
			
			// Rental Request Quote Filter
			add_filter( 'wcfm_rental_include_quotes', array( &$this, 'wcfmmp_rental_include_quotes' ) );
    }
  }
  
  // Product Import Restrict by Available Product Limit
  function wcfmmp_product_import_vendor_limit_validation( $parsed_data ) {
  	global $WCFM, $WCFMu;
  	
  	if( !apply_filters( 'wcfm_is_allow_product_limit', true ) || !apply_filters( 'wcfm_is_allow_space_limit', true ) ) {
  		$parsed_data = array();
  	}
		
  	return $parsed_data;
  }
  
  // Product Vendor association on Product Import - 2.4.2
  function wcfmmp_product_import_vendor_association( $product_obj, $data ) {
  	global $WCFM, $WCFMu;
  	
  	if( $product_obj->get_type() == 'variation' ) return;
  	
  	$new_product_id = $product_obj->get_id();
  	
  	// Without product limit save product as Draft
  	if( !apply_filters( 'wcfm_is_allow_product_limit', true ) || !apply_filters( 'wcfm_is_allow_space_limit', true ) ) {
  		$update_product = array(
															'ID'           => $new_product_id,
															'post_status'  => 'draft',
															'post_type'    => 'product',
														);
  		wp_update_post( $update_product, true );
  	}
  	
		// Admin Message for Pending Review
		if( !apply_filters( 'wcfm_is_allow_publish_products', true ) ) {
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
  function wcfmmp_orders_menu( $menus ) {
  	return array();
  }
  
  // Order Details Shipping Line Item Invoice
  function wcfmmp_is_allow_order_details_shipping_line_item_invoice( $allow ) {
  	global $WCFM, $wpdb, $WCFMmp;
  	//if( !$WCFMmp->wcfmmp_vendor->is_vendor_get_shipping( $this->vendor_id ) ) 
  	$allow = false;
  	return $allow;
  }
  
  // Order Details Tax Line Item Invoice
  function wcfmmp_is_allow_order_details_tax_line_item_invoice( $allow ) {
  	global $WCFM, $wpdb, $WCFMmp;
  	//if( !$WCFMmp->wcfmmp_vendor->is_vendor_get_tax( $this->vendor_id ) || !$WCFMmp->wcfmmp_vendor->is_vendor_get_shipping( $this->vendor_id ) ) 
  	$allow = false;
  	return $allow;
  }
  
  // Invoice Column width
  function wcfmmp_invoice_order_total_column_width( $width ) {
  	$width = 2;
  	return $width;
  }
  
  function wcfmmp_order_details_total_earning_invoice( $is_allow ) {
  	if( defined('DOING_AJAX') ) {
  		return true;
  	}
  	return $is_allow;
  }
  
  // Order Notes Args
  function wcfmmp_order_notes_args( $args ) {
  	$args['user_id'] = $this->vendor_id;
  	return $args;
  }
  
  // Order Notes
  function wcfmmp_order_notes( $notes, $order_id ) {
  	if( apply_filters( 'wcfm_is_allow_vendor_order_notes_filter', false ) ) {
  		$order    = wc_get_order( $order_id );
  		$notes = $order->get_customer_order_notes();
  	}
  	return $notes;
  }
  
  // Filter Comment User as Vendor
  public function filter_wcfm_vendors_comment( $commentdata, $order ) {
		$user_id = $this->vendor_id;

		$commentdata[ 'user_id' ]              = $user_id;
		$commentdata[ 'comment_author' ]       = wp_get_current_user()->display_name;
		$commentdata[ 'comment_author_email' ] = wp_get_current_user()->user_email;

		return $commentdata;
	}
	
	/**
	 * WCFMu Marketplace Reports Menu
	 */
	function wcfmmp_reports_menus( $reports_menus ) {
		global $WCFM, $WCFMu;
		
		unset($reports_menus['coupons-by-date']);
		return $reports_menus;
	}
	
	// Report Data args filter as per vendor
  function wcfmmp_reports_get_order_report_data_args( $args ) {
  	global $WCFM, $wpdb, $_POST, $wp;
  	
  	if ( !isset( $wp->query_vars['wcfm-reports-sales-by-product'] ) ) return $args;
  	if( $args['query_type'] != 'get_results' ) return $args;
  	
  	$user_id = $this->vendor_id;
  	
  	$products = $WCFM->wcfm_marketplace->wcfmmp_get_vendor_products( $this->vendor_id );
		
		//$args['order_types'] = wc_get_order_types( 'sales-reports' );
		$args['where'][] = array( 'key' => 'order_item_meta__product_id.meta_value', 'operator' => 'in', 'value' => $products );
  	
  	return $args;
  }
	
	// Report Vendor Filter
  function wcfmmp_report_low_in_stock_query_from( $query_from, $stock, $nostock ) {
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
   * WCFM Marketplace Subscription
   */
  function wcfmmp_wcs_include_subscription( ) {
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
  
  // Filter resources for specific vendor - Fixing Product Vendors bug
  function wcfmmp_filter_resources( $query_args ) {
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
	public function wcfmmp_filter_bookings_calendar( $booking_ids ) {
		global $WCFM;
		
		$filtered_ids = array();
		
		$product_ids = $WCFM->wcfm_marketplace->wcfmmp_get_vendor_products( $this->vendor_id );

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
   * WCFM Marketplace Appointments
   */
  function wcfmmp_wca_include_appointments( ) {
  	global $WCFM, $WCFMu, $wpdb, $_POST;
  	
  	$vendor_products = $WCFM->wcfm_marketplace->wcfmmp_get_vendor_products( $this->vendor_id );
		
		if( empty($vendor_products) ) return array(0);
		
  	$query = "SELECT ID FROM {$wpdb->posts} as posts
							INNER JOIN {$wpdb->postmeta} AS postmeta ON posts.ID = postmeta.post_id
							WHERE 1=1
							AND posts.post_type IN ( 'wc_appointment' )
							AND postmeta.meta_key = '_appointment_product_id' AND postmeta.meta_value in (" . implode(',', $vendor_products) . ")";
		
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
	public function wcfmmp_filter_appointments_calendar( $appointment_ids ) {
		global $WCFM;
		
		$filtered_ids = array();
		
		$product_ids = $WCFM->wcfm_marketplace->wcfmmp_get_vendor_products( $this->vendor_id );

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
	
	// WCFM Marketplace Filter Staffs
	function wcfmmp_filter_appointment_staffs( $args ) {
		$args['meta_key'] = '_wcfm_vendor';
		$args['meta_value'] = $this->vendor_id;
		return $args;
	}
	
	// WCFM Marketplace Appointment Staff Manage
	function wcfmmp_wcfm_staffs_manage( $staff_id ) {
		update_user_meta( $staff_id, '_wcfm_vendor', $this->vendor_id );
	}
	
	// WCFM Marketplace Valid Auction
	function wcfmmp_wcfm_valid_auctions( $valid_actions ) {
		global $WCFM, $WCFMu;
		
		if ($this->vendor_id) {
			$valid_actions = $WCFM->wcfm_marketplace->wcfmmp_get_vendor_products( $this->vendor_id );
		}
		
		if( empty($valid_actions) ) return array(0);
		
		return $valid_actions; 
	}
	
	// WCFM Marketplace FooEvents Args
	function wcfmmp_fooevents_args( $args ) {
  	$args['author'] = $this->vendor_id;
  	return $args;
  }
  
  // WCFM Marketplace FooEvents Tickets Args
  function wcfmmp_event_tickets_args( $args ) {
  	global $WCFM, $WCFMu;
  	$vendor_products = $WCFM->wcfm_marketplace->wcfmmp_get_vendor_products( $this->vendor_id );
  	$args['meta_query'] = array(
																	array(
																					'key' => 'WooCommerceEventsProductID',
																					'value' => $vendor_products,
																					'compare' => 'IN',
																	),
													     );
  	return $args;
  }
	
	
	/**
   * WCFM Marketplace Rental Quotes
   */
  function wcfmmp_rental_include_quotes( ) {
  	global $WCFM, $wpdb, $_POST;
  	
  	$vendor_products = $WCFM->wcfm_marketplace->wcfmmp_get_vendor_products( $this->vendor_id );
		
		if( empty($vendor_products) ) return array(0);
		
  	$query = "SELECT ID FROM {$wpdb->posts} as posts
							INNER JOIN {$wpdb->postmeta} AS postmeta ON posts.ID = postmeta.post_id
							WHERE 1=1
							AND posts.post_type IN ( 'request_quote' )
							AND postmeta.meta_key = 'add-to-cart' AND postmeta.meta_value in (" . implode(',', $vendor_products) . ")";
		
		$vendor_quotes = $wpdb->get_results($query);
		if( empty($vendor_quotes) ) return array(0);
		$vendor_quotes_arr = array();
		foreach( $vendor_quotes as $vendor_quote ) {
			$vendor_quotes_arr[] = $vendor_quote->ID;
		}
		if( !empty($vendor_quotes_arr) ) return $vendor_quotes_arr;
		return array(0);
  }
}