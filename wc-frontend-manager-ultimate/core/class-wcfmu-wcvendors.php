<?php

/**
 * WCFMu plugin core
 *
 * Marketplace WC Vendors Support
 *
 * @author 		WC Lovers
 * @package 	wcfmu/core
 * @version   1.0.1
 */
 
class WCFMu_WCVendors {
	
	public $vendor_id;
	
	public function __construct() {
    global $WCFM;
    
    if( wcfm_is_vendor() ) {
    	
    	$this->vendor_id   = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );
    	
    	// Manage Vendor Product Import Vendor Association - 2.4.2
			add_action( 'woocommerce_product_import_inserted_product_object', array( &$this, 'wcvendors_product_import_vendor_association' ), 10, 2 );
    	
    	// WCV Pro My-account message
    	add_filter( 'wcv_my_account_msg', array( &$this, 'wcvendors_my_account_msg' ) );
    	
			// Orders Menu
			add_filter( 'wcfmu_orders_menus', array( &$this, 'wcvendors_orders_menu' ) );
			
			// Orders Filter
			add_action( 'before_wcfm_orders', array( &$this, 'wcvendors_orders_filter' ) );
			
			// Order Invoice
			add_filter( 'wcfm_order_details_shipping_line_item_invoice', array( &$this, 'wcvendors_is_allow_order_details_shipping_line_item_invoice' ) );
			add_filter( 'wcfm_order_details_tax_line_item_invoice', array( &$this, 'wcvendors_is_allow_order_details_tax_line_item_invoice' ) );
			
			// Order Notes
			add_filter( 'wcfm_order_notes', array( &$this, 'wcvendors_order_notes' ), 10, 2 );
			
			// WCFMu Report Menu
			add_filter( 'wcfm_reports_menus', array( &$this, 'wcvendors_reports_menus' ), 100 );
			
			// Report Filter
			add_filter( 'woocommerce_reports_get_order_report_data_args', array( &$this, 'wcvendors_reports_get_order_report_data_args'), 100 );
			add_filter( 'wcfm_report_low_in_stock_query_from', array( &$this, 'wcvendors_report_low_in_stock_query_from' ), 100, 3 );
			
			// Subscription Filter
			add_filter( 'wcfm_wcs_include_subscriptions', array( &$this, 'wcvendors_wcs_include_subscription' ) );
			
			// Booking Filter resources for specific vendor
    	add_filter( 'get_booking_resources_args', array( $this, 'wcvendors_filter_resources' ), 20 );
    	
			// Booking filter products from booking calendar
			add_filter( 'woocommerce_bookings_in_date_range_query', array( $this, 'wcvendors_filter_bookings_calendar' ) );
			
			// Appointment Filter
			add_filter( 'wcfm_wca_include_appointments', array( &$this, 'wcvendors_wca_include_appointments' ) );
			
			// Appointment filter products from appointment calendar
			add_filter( 'woocommerce_appointments_in_date_range_query', array( $this, 'wcvendors_filter_appointments_calendar' ) );
			
			// Appointment Staffs args
			add_filter( 'get_appointment_staff_args', array( &$this, 'wcvendors_filter_appointment_staffs' ) );
			
			// Appointment Manage Staff
			add_action( 'wcfm_staffs_manage', array( &$this, 'wcvendors_wcfm_staffs_manage' ) );
			
			// Auctions Filter
			add_filter( 'wcfm_valid_auctions', array( &$this, 'wcvendors_wcfm_valid_auctions' ) );
			
			// Rental Request Quote Filter
			add_filter( 'wcfm_rental_include_quotes', array( &$this, 'wcvendors_rental_include_quotes' ) );
			
			// WC Vendors Pro Settings Fields Rules
			add_filter( 'wcfm_wcvendors_settings_fields_general', array( &$this, 'wcvendors_settings_fields_general' ) );
			add_filter( 'wcfm_wcvendors_settings_fields_identity', array( &$this, 'wcvendors_settings_fields_pro' ) );
			
			// Settings Update
			add_filter( 'wcfm_vendors_settings_fields_shipping', array( &$this, 'wcvendors_settings_fields_shipping' ), 10 );
			add_action( 'wcfm_wcvendors_settings_update', array( &$this, 'wcvendors_settings_update' ), 10, 2 );
			
			// Product Specific Shipping Settings
			add_filter( 'wcfm_product_manage_fields_shipping', array( &$this, 'wcvendors_product_manage_fields_shipping' ), 10, 2 );
			add_action( 'after_wcfm_products_manage_meta_save', array( &$this, 'wcvendors_shipping_product_meta_save' ), 150, 2 );
    }
  }
  
  // Product Vendor association on Product Import - 2.4.2
  function wcvendors_product_import_vendor_association( $product_obj ) {
  	global $WCFM, $WCFMu, $WCMp;
  	
  	if( $product_obj->get_type() == 'product_variation' ) return;
  	
  	$new_product_id = $product_obj->get_id();
  	
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
  
  // WCV Pro My-account message
  function wcvendors_my_account_msg( $message ) {
  	$message = sprintf( __( '%sTo add or edit products, view sales and orders for your vendor account, or to configure your store, visit your %sVendor Dashboard%s.%s', 'wc-frontend-manager-ultimate' ), '<p>', '<a href="' . get_wcfm_url() . '">', '</a>', '</p>' );
  	return $message;
  }
  
  // Orders Menu
  function wcvendors_orders_menu( $menus ) {
  	return array();
  }
  
  // Orders Filter
  function wcvendors_orders_filter() {
  	global $WCFM, $WCFMu, $wpdb, $wp_locale;
  	?>
  	<h2><?php _e('Orders Listing', 'wc-frontend-manager' ); ?></h2>
		<div class="wcfm_orders_filter_wrap wcfm_filters_wrap">
			<?php $WCFM->library->wcfm_date_range_picker_field(); ?>
			
			<select name="commission-status" id="commission-status" style="width: 150px;">
				<option value=''><?php esc_html_e( 'Show all', 'wc-frontend-manager-ultimate' ); ?></option>
				<option value="due"><?php esc_html_e( 'Due', 'wc-frontend-manager-ultimate' ); ?></option>
				<option value="paid"><?php esc_html_e( 'Paid', 'wc-frontend-manager-ultimate' ); ?></option>
				<option value="reversed"><?php esc_html_e( 'Reversed', 'wc-frontend-manager-ultimate' ); ?></option>
			</select>
		</div>
  	<?php
  }
  
  // Order Details Shipping Line Item Invoice
  function wcvendors_is_allow_order_details_shipping_line_item_invoice( $allow ) {
  	if( version_compare( WCV_VERSION, '2.0.0', '<' ) ) {
  		if ( !WC_Vendors::$pv_options->get_option( 'give_shipping' ) ) $allow = false;
  	} else {
  		if ( !get_option('wcvendors_vendor_give_shipping') ) $allow = false;
  	}
  	return $allow;
  }
  
  // Order Details Tax Line Item Invoice
  function wcvendors_is_allow_order_details_tax_line_item_invoice( $allow ) {
  	if( version_compare( WCV_VERSION, '2.0.0', '<' ) ) {
  		if ( !WC_Vendors::$pv_options->get_option( 'give_tax' ) ) $allow = false;
  	} else {
  		if ( !get_option('wcvendors_vendor_give_taxes') ) $allow = false;
  	}
  	return $allow;
  }
  
  // Order Notes
  function wcvendors_order_notes( $notes, $order_id ) {
  	$order    = wc_get_order( $order_id );
		$notes = $order->get_customer_order_notes();
  	return $notes;
  }
  
  // Filter Comment User as Vendor
  public function filter_wcfm_vendors_comment( $commentdata, $order ) {
		$user_id = $this->vendor_id;

		$commentdata[ 'user_id' ]              = $user_id;
		$commentdata[ 'comment_author' ]       = WCV_Vendors::get_vendor_shop_name( $user_id );
		$commentdata[ 'comment_author_url' ]   = WCV_Vendors::get_vendor_shop_page( $user_id );
		$commentdata[ 'comment_author_email' ] = wp_get_current_user()->user_email;

		return $commentdata;
	}
	
	/**
	 * WCFMu WCV Reports Menu
	 */
	function wcvendors_reports_menus( $reports_menus ) {
		global $WCFM, $WCFMu;
		
		unset($reports_menus['coupons-by-date']);
		return $reports_menus;
	}
	
	// Report Data args filter as per vendor
  function wcvendors_reports_get_order_report_data_args( $args ) {
  	global $WCFM, $wpdb, $_POST, $wp;
  	
  	if ( !isset( $wp->query_vars['wcfm-reports-sales-by-product'] ) ) return $args;
  	if( $args['query_type'] != 'get_results' ) return $args;
  	
  	$user_id = $this->vendor_id;
  	
  	$products = $WCFM->wcfm_marketplace->wcv_get_vendor_products( $this->vendor_id );
		
		//$args['order_types'] = wc_get_order_types( 'sales-reports' );
		$args['where'][] = array( 'key' => 'order_item_meta__product_id.meta_value', 'operator' => 'in', 'value' => $products );
  	
  	return $args;
  }
	
	// Report Vendor Filter
  function wcvendors_report_low_in_stock_query_from( $query_from, $stock, $nostock ) {
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
   * WC Vendors Subscription
   */
  function wcvendors_wcs_include_subscription( ) {
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
  function wcvendors_filter_resources( $query_args ) {
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
	public function wcvendors_filter_bookings_calendar( $booking_ids ) {
		global $WCFM;
		
		$filtered_ids = array();
		
		$product_ids = $WCFM->wcfm_marketplace->wcv_get_vendor_products( $this->vendor_id );

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
   * WC Vendors Appointments
   */
  function wcvendors_wca_include_appointments( ) {
  	global $WCFM, $WCFMu, $wpdb, $_POST;
  	
  	$vendor_products = $WCFM->wcfm_marketplace->wcv_get_vendor_products( $this->vendor_id );
		
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
	public function wcvendors_filter_appointments_calendar( $appointment_ids ) {
		global $WCFM;
		
		$filtered_ids = array();
		
		$product_ids = $WCFM->wcfm_marketplace->wcv_get_vendor_products( $this->vendor_id );

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
	
	// WC Vendors Filter Staffs
	function wcvendors_filter_appointment_staffs( $args ) {
		$args['meta_key'] = '_wcfm_vendor';
		$args['meta_value'] = $this->vendor_id;
		return $args;
	}
	
	// WC Vendors Appointment Staff Manage
	function wcvendors_wcfm_staffs_manage( $staff_id ) {
		update_user_meta( $staff_id, '_wcfm_vendor', $this->vendor_id );
	}
	
	// WC Vendors Valid Auction
	function wcvendors_wcfm_valid_auctions( $valid_actions ) {
		global $WCFM, $WCFMu;
		
		if ($this->vendor_id) {
			$valid_actions = $WCFM->wcfm_marketplace->wcv_get_vendor_products( $this->vendor_id );
		}
		
		if( empty($valid_actions) ) return array(0);
		
		return $valid_actions; 
	}
	
	/**
   * WC Vendors Rental Quotes
   */
  function wcvendors_rental_include_quotes( ) {
  	global $WCFM, $wpdb, $_POST;
  	
  	$vendor_products = $WCFM->wcfm_marketplace->wcv_get_vendor_products( $this->vendor_id );
		
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
	
  /**
   * WC Vendors Pro settinds fields rule
   */
  function wcvendors_settings_fields_general( $fields ) {
  	
  	if( version_compare( WCV_VERSION, '2.0.0', '<' ) ) {
			$settings_general		= (array) WC_Vendors::$pv_options->get_option( 'hide_settings_general' );
			$settings_store 		= (array) WC_Vendors::$pv_options->get_option( 'hide_settings_store' );
			$settings_payment 	= (array) WC_Vendors::$pv_options->get_option( 'hide_settings_payment' );
			$settings_branding 	= (array) WC_Vendors::$pv_options->get_option( 'hide_settings_branding' );
			
			if ( isset( $settings_store[ 'pv_seller_info' ] ) && $settings_store[ 'pv_seller_info' ] ) unset( $fields['seller_info'] );
			if ( isset( $settings_store[ 'pv_shop_description' ] ) && $settings_store[ 'pv_shop_description' ] ) unset( $fields['shop_description'] );
			if ( isset( $settings_branding[ 'store_icon' ] ) && $settings_branding[ 'store_icon' ] ) unset( $fields['logo'] );
			if ( isset( $settings_payment[ 'paypal' ] ) && $settings_payment[ 'paypal' ] ) unset( $fields['paypal'] );
		} else {
			$hide_store_desc   	= get_option( 'wcvendors_hide_settings_store_description', 'no' );
			$hide_store_info   	= get_option( 'wcvendors_hide_settings_store_seller_info', 'no' );
			$hide_paypal      	= get_option( 'wcvendors_hide_settings_payment_paypal', 'no' );
			$hide_store_icon   	= get_option( 'wcvendors_hide_settings_branding_store_icon', 'no' );
			
			if ( $hide_store_info == 'yes' ) unset( $fields['seller_info'] );
			if ( $hide_store_desc == 'yes' ) unset( $fields['shop_description'] );
			if ( $hide_store_icon == 'yes' ) unset( $fields['logo'] );
			if ( $hide_paypal == 'yes' ) unset( $fields['paypal'] );
		}
		
  	return $fields;
  }
  
  function wcvendors_settings_fields_pro( $fields ) {
  	
  	if( version_compare( WCV_VERSION, '2.0.0', '<' ) ) {
			$settings_general		= (array) WC_Vendors::$pv_options->get_option( 'hide_settings_general' );
			$settings_store 		= (array) WC_Vendors::$pv_options->get_option( 'hide_settings_store' );
			$settings_payment 	= (array) WC_Vendors::$pv_options->get_option( 'hide_settings_payment' );
			$settings_branding 	= (array) WC_Vendors::$pv_options->get_option( 'hide_settings_branding' );
			
			if ( isset($settings_store[ '_wcv_company_url' ] ) && $settings_store[ '_wcv_company_url' ] ) unset( $fields['_wcv_company_url'] );
			if ( isset($settings_store[ '_wcv_store_phone' ] ) && $settings_store[ '_wcv_store_phone' ] ) unset( $fields['_wcv_store_phone'] );
			if ( isset($settings_branding[ 'store_banner' ] ) && $settings_branding[ 'store_banner' ] ) unset( $fields['banner'] );
			if ( isset($settings_store[ 'vacation_mode' ] ) && $settings_store[ 'vacation_mode' ] ) unset( $fields['_wcv_vacation_mode'] );
			if ( isset($settings_store[ 'vacation_mode' ] ) && $settings_store[ 'vacation_mode' ] ) unset( $fields['_wcv_vacation_mode_msg'] );
		
		} else {
			$hide_company_url 	= get_option( 'wcvendors_hide_settings_store_company_url', 'no' );
			$hide_store_phone 	= get_option( 'wcvendors_hide_settings_store_phone', 'no' );
			$hide_store_banner 	= get_option( 'wcvendors_hide_settings_branding_store_banner', 'no' );
			
			if ( $hide_company_url == 'yes' ) unset( $fields['_wcv_company_url'] );
			if ( $hide_store_phone == 'yes' ) unset( $fields['_wcv_store_phone'] );
			if ( $hide_store_banner == 'yes' ) unset( $fields['banner'] );
		}
		
  	return $fields;
  }
  
  function wcvendors_settings_fields_shipping( $shipping_setting_fields ) {
  	
  	if( version_compare( WCV_VERSION, '2.0.0', '<' ) ) {
  		$settings_shipping 	= (array) WC_Vendors::$pv_options->get_option( 'hide_settings_shipping' );
  		if( !empty( $shipping_setting_fields ) ) {
				foreach( $shipping_setting_fields as $shipping_setting_field_key => $shipping_setting_field ) {
					if( isset( $settings_shipping[$shipping_setting_field_key] ) && $settings_shipping[$shipping_setting_field_key] ) unset( $shipping_setting_fields[$shipping_setting_field_key] );
				}
			}
  	} else {
  		if( !empty( $shipping_setting_fields ) ) {
				foreach( $shipping_setting_fields as $shipping_setting_field_key => $shipping_setting_field ) {
					if( get_option( 'wcvendors_hide_settings_shipping_'.$shipping_setting_field_key, 'no' ) == 'yes' ) unset( $shipping_setting_fields[$shipping_setting_field_key] );
				}
			}
  	}
  	
  	
  	return $shipping_setting_fields;
  }
  
  // WCV Settings Update
  function wcvendors_settings_update( $user_id, $wcfm_settings_form ) {
  	global $WCFM, $wpdb, $_POST;
  	
  	update_user_meta( $user_id, 'wcfm_vacation_mode', isset( $wcfm_settings_form['wcfm_vacation_mode'] ) ? 'yes' : 'no' );
  	update_user_meta( $user_id, 'wcfm_disable_vacation_purchase', isset( $wcfm_settings_form['wcfm_disable_vacation_purchase'] ) ? 'yes' : 'no' );
  	update_user_meta( $user_id, 'wcfm_vacation_mode_type', $wcfm_settings_form['wcfm_vacation_mode_type'] );
  	update_user_meta( $user_id, 'wcfm_vacation_start_date', $wcfm_settings_form['wcfm_vacation_start_date'] );
  	update_user_meta( $user_id, 'wcfm_vacation_end_date', $wcfm_settings_form['wcfm_vacation_end_date'] );
		update_user_meta( $user_id, 'wcfm_vacation_mode_msg', $wcfm_settings_form['wcfm_vacation_mode_msg'] );
  	
  	if( WCFM_Dependencies::wcvpro_plugin_active_check() ) {
  		// Set Vendor Store Banner
			if(isset($wcfm_settings_form['banner']) && !empty($wcfm_settings_form['banner'])) {
				$wcfm_settings_form['banner'] = $WCFM->wcfm_get_attachment_id($wcfm_settings_form['banner']);
			} else {
				$wcfm_settings_form['banner'] = '';
			}
			update_user_meta( $user_id, '_wcv_store_banner_id', $wcfm_settings_form['banner'] );
			
			update_user_meta( $user_id, '_wcv_company_url', $wcfm_settings_form['_wcv_company_url'] );
			update_user_meta( $user_id, '_wcv_store_phone', $wcfm_settings_form['_wcv_store_phone'] );
  		
			$wcfm_profile_store_fields = array( 
																						'_wcv_store_address1'  => 'addr_1',
																						'_wcv_store_address2'  => 'addr_2',
																						'_wcv_store_country'   => 'country',
																						'_wcv_store_city'      => 'city',
																						'_wcv_store_state'     => 'state',
																						'_wcv_store_postcode'  => 'zip'
																					);
			
			foreach( $wcfm_profile_store_fields as $wcfm_profile_store_key => $wcfm_profile_store_field ) {
				update_user_meta( $user_id, $wcfm_profile_store_key, $wcfm_settings_form[$wcfm_profile_store_field] );
			}
			
			update_user_meta( $user_id, '_wcv_shipping_rates', $wcfm_settings_form['_wcv_shipping_rates'] );
			
			$wcv_shipping = (array) get_user_meta( $user_id, '_wcv_shipping', true );
			
			$wcfm_setting_shipping_flat_fields = array( 
																						'national'                      => 'national',
																						'national_qty_override'         => 'national_qty_override',
																						'national_free'                 => 'national_free',
																						'national_disable'              => 'national_disable',
																						'international'                 => 'international',
																						'international_free'            => 'international_free',
																						'international_qty_override'    => 'international_qty_override',
																						'international_disable'         => 'international_disable',
																					);
			
			foreach( $wcfm_setting_shipping_flat_fields as $wcfm_setting_shipping_flat_key => $wcfm_setting_shipping_flat_field ) {
				if( isset( $wcfm_settings_form[$wcfm_setting_shipping_flat_field] ) ) {
					$wcv_shipping[$wcfm_setting_shipping_flat_key] = $wcfm_settings_form[$wcfm_setting_shipping_flat_field];
				} else {
					unset( $wcv_shipping[$wcfm_setting_shipping_flat_key] );
				}
			}
			
			$wcfm_setting_shipping_fields = array( 
																						'product_handling_fee'  => 'product_handling_fee',
																						'max_charge'            => 'max_charge',
																						'min_charge'            => 'min_charge',
																						'free_shipping_order'   => 'free_shipping_order',
																						'max_charge_product'    => 'max_charge_product',
																						'free_shipping_product' => 'free_shipping_product',
																						'shipping_policy'       => 'shipping_policy',
																						'return_policy'         => 'return_policy',
																					);
			
			foreach( $wcfm_setting_shipping_fields as $wcfm_setting_shipping_key => $wcfm_setting_shipping_field ) {
				if( isset( $wcfm_settings_form[$wcfm_setting_shipping_field] ) ) {
					$wcv_shipping[$wcfm_setting_shipping_key] = $wcfm_settings_form[$wcfm_setting_shipping_field];
				}
			}
			
			$wcfm_setting_shipping_addr_fields = array( 
																						'address1'  => 'saddr_1',
																						'address2'  => 'saddr_2',
																						'country'   => 'scountry',
																						'city'      => 'scity',
																						'state'     => 'sstate',
																						'zip'       => 'szip'
																					);
			
			foreach( $wcfm_setting_shipping_addr_fields as $wcfm_setting_shipping_addr_key => $wcfm_setting_shipping_addr_field ) {
				$wcv_shipping['shipping_address'][$wcfm_setting_shipping_addr_key] = $wcfm_settings_form[$wcfm_setting_shipping_addr_field];
			}
			update_user_meta( $user_id, '_wcv_shipping', $wcv_shipping );
		}
  }
  
  function wcvendors_product_manage_fields_shipping( $shipping_fields, $product_id ) {
  	global $wp, $WCFM, $WCFMu, $wcvendors_pro, $wpdb;
  	
  	if( apply_filters( 'wcfm_is_allow_shipping', true ) && WCFM_Dependencies::wcvpro_plugin_active_check() ) {
  		if ( $wcvendors_pro->is_vendor_shipping_method_enabled() ) {
  			
  			$shipping_settings 		= get_option( 'woocommerce_wcv_pro_vendor_shipping_settings' ); 
				$store_shipping_type	= get_user_meta( $this->vendor_id, '_wcv_shipping_type', true ); 
				$shipping_type 			= ( $store_shipping_type != '' ) ? $store_shipping_type : $shipping_settings[ 'shipping_system' ]; 
  			
				$wcv_shipping_rates = array();
				
				$national = '';
				$national_qty_override = '';
				$national_free = '';
				$national_disable = '';
				
				$international = '';
				$international_free = '';
				$international_qty_override = '';
				$international_disable = '';
				
				$max_charge_product = '';
				$free_shipping_product = '';
				$product_handling_fee = '';
				
				if( $product_id ) {
					$wcv_shipping_rates = (array) get_post_meta( $product_id, '_wcv_shipping_rates', true );
					
					$wcv_shipping = (array) get_post_meta( $product_id, '_wcv_shipping_details', true );
					
					$national = ( isset( $wcv_shipping['national'] ) ) ? $wcv_shipping['national'] : '';
					$national_qty_override = ( isset( $wcv_shipping['national_qty_override'] ) ) ? $wcv_shipping['national_qty_override'] : '';
					$national_free = ( isset( $wcv_shipping['national_free'] ) ) ? $wcv_shipping['national_free'] : '';
					$national_disable = ( isset( $wcv_shipping['national_disable'] ) ) ? $wcv_shipping['national_disable'] : '';
					
					$international = ( isset( $wcv_shipping['international'] ) ) ? $wcv_shipping['international'] : '';
					$international_free = ( isset( $wcv_shipping['international_free'] ) ) ? $wcv_shipping['international_free'] : '';
					$international_qty_override = ( isset( $wcv_shipping['international_qty_override'] ) ) ? $wcv_shipping['international_qty_override'] : '';
					$international_disable = ( isset( $wcv_shipping['international_disable'] ) ) ? $wcv_shipping['international_disable'] : '';
					
					$max_charge_product = ( isset( $wcv_shipping['max_charge_product'] ) ) ? $wcv_shipping['max_charge_product'] : '';
					$free_shipping_product = ( isset( $wcv_shipping['free_shipping_product'] ) ) ? $wcv_shipping['free_shipping_product'] : '';
					$product_handling_fee = ( isset( $wcv_shipping['handling_fee'] ) ) ? $wcv_shipping['handling_fee'] : '';
				}
				
				if ( $shipping_type == 'flat' ) { 
				
					$wcv_shipping_fileds =  array(
																				"national" => array('label' => __('Default National Shipping Fee', 'wcvendors-pro'), 'placeholder' => __( 'Change to override store defaults.', 'wcvendors-pro' ), 'type' => 'text', 'class' => 'wcfm-text', 'label_class' => 'wcfm_title', 'value' => $national, 'hints' => __( 'The cost to ship this product within your country.', 'wcvendors-pro' ) ),
																				"national_free" => array('label' => __( 'Free national shipping', 'wcvendors-pro' ), 'type' => 'checkbox', 'class' => 'wcfm-checkbox', 'label_class' => 'wcfm_title checkbox_title', 'value' => 'yes', 'dfvalue' => $national_free, 'hints' => __( 'Free national shipping.', 'wcvendors-pro' ) ),
																				"national_qty_override" => array('label' => __( 'Charge once per product', 'wc-frontend-manager' ), 'type' => 'checkbox', 'class' => 'wcfm-checkbox', 'label_class' => 'wcfm_title checkbox_title', 'value' => 'yes', 'dfvalue' => $national_qty_override, 'hints' => __( 'Charge once per product for national shipping, even if more than one is purchased.', 'wcvendors-pro' ) ),
																				"national_disable" => array('label' => __( 'Disable national shipping', 'wcvendors-pro' ), 'type' => 'checkbox', 'class' => 'wcfm-checkbox', 'label_class' => 'wcfm_title checkbox_title', 'value' => 'yes', 'dfvalue' => $national_disable, 'hints' => __( 'Disable national shipping.', 'wcvendors-pro' ) ),
																				
																				"international" => array('label' => __( 'Default International Shipping Fee', 'wcvendors-pro' ), 'placeholder' => __( 'Change to override store defaults.', 'wcvendors-pro' ), 'type' => 'text', 'class' => 'wcfm-text', 'label_class' => 'wcfm_title', 'value' => $international, 'hints' => __( 'The cost to ship this product outside your country.', 'wcvendors-pro' ) ),
																				"international_free" => array('label' => __( 'Free international shipping', 'wcvendors-pro' ), 'type' => 'checkbox', 'class' => 'wcfm-checkbox', 'label_class' => 'wcfm_title checkbox_title', 'value' => 'yes', 'dfvalue' => $international_free, 'hints' => __( 'Free international shipping.', 'wcvendors-pro' ) ),
																				"international_qty_override" => array('label' => __( 'Charge once per product', 'wc-frontend-manager' ), 'type' => 'checkbox', 'class' => 'wcfm-checkbox', 'label_class' => 'wcfm_title checkbox_title', 'value' => 'yes', 'dfvalue' => $international_qty_override, 'hints' => __( 'Charge once per product for international shipping, even if more than one is purchased.', 'wcvendors-pro' ) ),
																				"international_disable" => array('label' => __( 'Disable international shipping', 'wcvendors-pro' ), 'type' => 'checkbox', 'class' => 'wcfm-checkbox', 'label_class' => 'wcfm_title checkbox_title', 'value' => 'yes', 'dfvalue' => $international_disable, 'hints' => __( 'Disable international shipping.', 'wcvendors-pro' ) ),
																			 );
					
				} else {
					$wcv_shipping_fileds = array( 
																				"_wcv_shipping_rates"      => array('label' => __('Shipping Rates', 'wc-frontend-manager') , 'type' => 'multiinput', 'class' => 'wcfm-text', 'label_class' => 'wcfm_title', 'value' => $wcv_shipping_rates, 'options' => array(
																																						"country" => array('label' => __('Country', 'wc-frontend-manager'), 'type' => 'country', 'class' => 'wcfm-select', 'label_class' => 'wcfm_title' ),
																																						"state" => array( 'label' => __('State', 'wc-frontend-manager'), 'type' => 'text', 'class' => 'wcfm-text', 'label_class' => 'wcfm_title' ),
																																						"postcode" => array('label' => __('Postcode', 'wc-frontend-manager'), 'type' => 'text', 'class' => 'wcfm-text', 'label_class' => 'wcfm_title' ),
																																						"fee" => array('label' => __('Shipping Fee', 'wc-frontend-manager'), 'type' => 'text', 'class' => 'wcfm-text', 'label_class' => 'wcfm_title' ),
																																						"qty_override" => array('label' => __('Override Qty', 'wc-frontend-manager'), 'type' => 'checkbox', 'class' => 'wcfm-checkbox', 'label_class' => 'wcfm_title checkbox_title', 'value' => 'yes' ),
																																						) )
																				);
				}
				
				$wcv_shipping_general_fileds = array( 
																			"handling_fee" => array('label' => __('Product handling fee', 'wc-frontend-manager'), 'placeholder' => __( 'Leave empty to disable', 'wc-frontend-manager' ), 'type' => 'text', 'class' => 'wcfm-text', 'label_class' => 'wcfm_title', 'value' => $product_handling_fee, 'hints' => __('The product handling fee, this can be overridden on a per product basis. Amount (5.00) or Percentage (5%).', 'wc-frontend-manager') ),
																			"max_charge_product" => array('label' => __('Max Charge Product', 'wc-frontend-manager'), 'placeholder' => '0', 'type' => 'text', 'class' => 'wcfm-text', 'label_class' => 'wcfm_title', 'value' => $max_charge_product, 'hints' => __('The maximum shipping charged per product no matter the quantity.', 'wc-frontend-manager') ),
																			"free_shipping_product" => array('label' => __('Free Shipping Product', 'wc-frontend-manager'), 'placeholder' => '0', 'type' => 'text', 'class' => 'wcfm-text', 'label_class' => 'wcfm_title', 'value' => $free_shipping_product, 'hints' => __('Free shipping if the spend per product is over this amount. This will override the max shipping charge above.', 'wc-frontend-manager') ),
																		);
				
				$wcv_shipping_fileds = array_merge( $wcv_shipping_fileds, $wcv_shipping_general_fileds );
				
				$shipping_fields = array_merge( $wcv_shipping_fileds, $shipping_fields );
				
				if( version_compare( WCV_VERSION, '2.0.0', '<' ) ) {
					$shipping_options 		= (array) WC_Vendors::$pv_options->get_option( 'hide_product_shipping' );
					if( !empty( $shipping_fields ) ) {
						foreach( $shipping_fields as $shipping_fields_key => $shipping_field ) {
							if( isset( $shipping_options[$shipping_fields_key] ) && $shipping_options[$shipping_fields_key] ) unset( $shipping_fields[$shipping_fields_key] );
						}
					}
				} else {
					if( !empty( $shipping_fields ) ) {
						foreach( $shipping_fields as $shipping_fields_key => $shipping_field ) {
							if( get_option( 'wcvendors_hide_product_shipping_'.$shipping_fields_key, 'no' ) == 'yes' ) unset( $shipping_fields[$shipping_fields_key] );
						}
					}
				}
				
			}
		}
  	
  	return $shipping_fields;
  }
  
  function wcvendors_shipping_product_meta_save( $new_product_id, $wcfm_products_manage_form_data ) {
		global $wpdb, $WCFM, $_POST, $wpdb;
		
		if( apply_filters( 'wcfm_is_allow_shipping', true ) && WCFM_Dependencies::wcvpro_plugin_active_check() ) {
			if( version_compare( WCV_VERSION, '2.0.0', '<' ) ) {
				$shipping_disabled	= wc_string_to_bool( WCVendors_Pro::get_option( 'shipping_management_cap' ) );
			} else {
				$shipping_disabled	= wc_string_to_bool( get_option( 'wcvendors_shipping_management_cap', 'no' ) );
			}
			if(  !$shipping_disabled ) {
				delete_post_meta( $new_product_id, '_wcv_shipping_rates' ); 
				if( isset( $wcfm_products_manage_form_data['_wcv_shipping_rates'] ) ) {
					$has_shipping_rate = false;
					foreach( $wcfm_products_manage_form_data['_wcv_shipping_rates'] as $wcv_shipping_rates ) {
						if( isset( $wcv_shipping_rates['country'] ) && !empty( $wcv_shipping_rates['country'] ) && isset( $wcv_shipping_rates['fee'] ) && !empty( $wcv_shipping_rates['fee'] ) ) {
							$has_shipping_rate = true;
						}
					}
					if( $has_shipping_rate ) {
						update_post_meta( $new_product_id, '_wcv_shipping_rates', $wcfm_products_manage_form_data['_wcv_shipping_rates'] );
					}
				}
				
				$wcv_shipping_details = (array) get_post_meta( $new_product_id, '_wcv_shipping_details', true );
				
				$wcfm_setting_shipping_flat_fields = array( 
																						'national'                      => 'national',
																						'national_qty_override'         => 'national_qty_override',
																						'national_free'                 => 'national_free',
																						'national_disable'              => 'national_disable',
																						'international'                 => 'international',
																						'international_free'            => 'international_free',
																						'international_qty_override'    => 'international_qty_override',
																						'international_disable'         => 'international_disable',
																					);
				
				foreach( $wcfm_setting_shipping_flat_fields as $wcfm_setting_shipping_flat_key => $wcfm_setting_shipping_flat_field ) {
					if( isset( $wcfm_products_manage_form_data[$wcfm_setting_shipping_flat_field] ) ) {
						$wcv_shipping_details[$wcfm_setting_shipping_flat_key] = $wcfm_products_manage_form_data[$wcfm_setting_shipping_flat_field];
					} else {
						unset( $wcv_shipping_details[$wcfm_setting_shipping_flat_key] );
					}
				}
				
				if( isset( $wcfm_products_manage_form_data['handling_fee'] ) ) {
					$wcv_shipping_details['handling_fee'] = $wcfm_products_manage_form_data['handling_fee'];
				}
				if( isset( $wcfm_products_manage_form_data['max_charge_product'] ) ) {
					$wcv_shipping_details['max_charge_product'] = $wcfm_products_manage_form_data['max_charge_product'];
				}
				if( isset( $wcfm_products_manage_form_data['free_shipping_product'] ) ) {
					$wcv_shipping_details['free_shipping_product'] = $wcfm_products_manage_form_data['free_shipping_product'];
				}
				update_post_meta( $new_product_id, '_wcv_shipping_details', $wcv_shipping_details );
			}
		}
  }
}