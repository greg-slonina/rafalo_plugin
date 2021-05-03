<?php

/**
 * WCFMu plugin core
 *
 * Marketplace Dokan Support
 *
 * @author 		WC Lovers
 * @package 	wcfmu/core
 * @version   3.3.0
 */
 
class WCFMu_Dokan {
	
	public $vendor_id;
	
	public function __construct() {
    global $WCFM;
    
    if( wcfm_is_vendor() ) {
    	
    	$this->vendor_id   = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );
    	
    	// Manage Vendor Product Import Vendor Association - 2.4.2
			add_action( 'woocommerce_product_import_inserted_product_object', array( &$this, 'dokan_product_import_vendor_association' ), 10, 2 );
    	
			// Orders Menu
			//add_filter( 'wcfmu_orders_menus', array( &$this, 'dokan_orders_menu' ) );
			
			// Orders Filter
			//add_action( 'before_wcfm_orders', array( &$this, 'dokan_orders_filter' ) );
			
			// Order Invoice
			add_filter( 'wcfm_order_details_shipping_line_item_invoice', array( &$this, 'dokan_is_allow_order_details_shipping_line_item_invoice' ) );
			add_filter( 'wcfm_order_details_tax_line_item_invoice', array( &$this, 'dokan_is_allow_order_details_tax_line_item_invoice' ) );
			add_filter( 'wcfm_invoice_order_total_column_width', array( &$this, 'dokan_invoice_order_total_column_width' ) );
			add_filter( 'wcfm_order_details_total_earning_invoice', array( &$this, 'dokan_order_details_total_earning_invoice' ) );
			
			// Order Notes
			add_filter( 'wcfm_order_notes', array( &$this, 'dokan_order_notes' ), 10, 2 );
			
			// WCFMu Report Menu
			add_filter( 'wcfm_reports_menus', array( &$this, 'dokan_reports_menus' ), 100 );
			
			// Report Filter
			add_filter( 'woocommerce_reports_get_order_report_data_args', array( &$this, 'dokan_reports_get_order_report_data_args'), 100 );
			add_filter( 'wcfm_report_low_in_stock_query_from', array( &$this, 'dokan_report_low_in_stock_query_from' ), 100, 3 );
			
			// Subscription Filter
			add_filter( 'wcfm_wcs_include_subscriptions', array( &$this, 'dokan_wcs_include_subscription' ) );
			
			// Booking Filter resources for specific vendor
    	add_filter( 'get_booking_resources_args', array( $this, 'dokan_filter_resources' ), 20 );
    	
			// Booking filter products from booking calendar
			add_filter( 'woocommerce_bookings_in_date_range_query', array( $this, 'dokan_filter_bookings_calendar' ) );
			
			// Appointment Filter
			add_filter( 'wcfm_wca_include_appointments', array( &$this, 'dokan_wca_include_appointments' ) );
			
			// Appointment filter products from appointment calendar
			add_filter( 'woocommerce_appointments_in_date_range_query', array( $this, 'dokan_filter_appointments_calendar' ) );
			
			// Appointment Staffs args
			add_filter( 'get_appointment_staff_args', array( &$this, 'dokan_filter_appointment_staffs' ) );
			
			// Appointment Manage Staff
			add_action( 'wcfm_staffs_manage', array( &$this, 'dokan_wcfm_staffs_manage' ) );
			
			// Auctions Filter
			add_filter( 'wcfm_valid_auctions', array( &$this, 'dokan_wcfm_valid_auctions' ) );
			
			// Rental Request Quote Filter
			add_filter( 'wcfm_rental_include_quotes', array( &$this, 'dokan_rental_include_quotes' ) );
			
			// Settings Update
			add_action( 'wcfm_dokan_settings_update', array( &$this, 'dokan_settings_update' ), 10, 2 );
			
			// Product Specific Shipping Settings
			add_filter( 'wcfm_product_manage_fields_shipping', array( &$this, 'dokan_product_manage_fields_shipping' ), 10, 2 );
			add_action( 'after_wcfm_products_manage_meta_save', array( &$this, 'dokan_shipping_product_meta_save' ), 150, 2 );
    }
  }
  
  // Product Vendor association on Product Import - 2.4.2
  function dokan_product_import_vendor_association( $product_obj ) {
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
  
  // Orders Menu
  function dokan_orders_menu( $menus ) {
  	return array();
  }
  
  // Orders Filter
  function dokan_orders_filter() {
  	global $WCFM, $WCFMu, $wpdb, $wp_locale;
  	?>
  	<h2><?php _e('Orders Listing', 'wc-frontend-manager' ); ?></h2>
  	
  	<?php
  	return;
  	$months = $wpdb->get_results( $wpdb->prepare( '
				SELECT DISTINCT YEAR( shop_orders.post_date ) AS year, MONTH( shop_orders.post_date ) AS month
				FROM ' . $wpdb->prefix . 'posts AS shop_orders
				WHERE shop_orders.post_type = %s
				ORDER BY shop_orders.post_date DESC
			', 'shop_order' ) );
	
		$month_count = count( $months );
	
		if ( ! $month_count || ( 1 === $month_count && 0 === $months[0]->month ) ) {
			return;
		}
	
		$m = isset( $_REQUEST['m'] ) ? (int) $_REQUEST['m'] : 0;
		?>
	
		<div class="wcfm_orders_filter_wrap wcfm_filters_wrap">
			<select name="m" id="filter-by-date" style="width: 150px;">
				<option<?php selected( $m, 0 ); ?> value='0'><?php esc_html_e( 'Show all dates', 'wc-frontend-manager-ultimate' ); ?></option>
				<?php
				foreach ( $months as $arc_row ) {
					if ( 0 === $arc_row->year ) {
						continue;
					}
	
					$month = zeroise( $arc_row->month, 2 );
					$year  = $arc_row->year;
	
					if ( '00' === $month || '0' === $year ) {
						continue;
					}
	
					printf( "<option %s value='%s'>%s</option>\n",
						selected( $m, $year . $month, false ),
						esc_attr( $arc_row->year . $month ),
						/* translators: 1: month name, 2: 4-digit year */
						sprintf( __( '%1$s %2$d', 'wc-frontend-manager-ultimate' ), $wp_locale->get_month( $month ), $year )
					);
				}
				?>
			</select>
		</div>
		<?php
  }
  
  // Order Details Shipping Line Item Invoice
  function dokan_is_allow_order_details_shipping_line_item_invoice( $allow ) {
  	$allow = false;
  	return $allow;
  }
  
  // Order Details Tax Line Item Invoice
  function dokan_is_allow_order_details_tax_line_item_invoice( $allow ) {
  	$allow = false;
  	return $allow;
  }
  
  // Invoice Column width
  function dokan_invoice_order_total_column_width( $width ) {
  	$width = 2;
  	return $width;
  }
  
  function dokan_order_details_total_earning_invoice( $is_allow ) {
  	if( defined('DOING_AJAX') ) {
  		return true;
  	}
  	return $is_allow;
  }
  
  // Order Notes
  function dokan_order_notes( $notes, $order_id ) {
  	$order    = wc_get_order( $order_id );
		$notes = $order->get_customer_order_notes();
  	return $notes;
  }
  
  // Filter Comment User as Vendor
  public function filter_wcfm_vendors_comment( $commentdata, $order ) {
		$user_id = $this->vendor_id;

		$commentdata[ 'user_id' ]              = $user_id;
		$commentdata[ 'comment_author' ]       = wp_get_current_user()->display_name;
		//$commentdata[ 'comment_author_url' ]   = WCV_Vendors::get_vendor_shop_page( $user_id );
		$commentdata[ 'comment_author_email' ] = wp_get_current_user()->user_email;

		return $commentdata;
	}
	
	/**
	 * WCFMu Dokan Reports Menu
	 */
	function dokan_reports_menus( $reports_menus ) {
		global $WCFM, $WCFMu;
		
		unset($reports_menus['coupons-by-date']);
		return $reports_menus;
	}
	
	// Report Data args filter as per vendor
  function dokan_reports_get_order_report_data_args( $args ) {
  	global $WCFM, $wpdb, $_POST, $wp;
  	
  	if ( !isset( $wp->query_vars['wcfm-reports-sales-by-product'] ) ) return $args;
  	if( $args['query_type'] != 'get_results' ) return $args;
  	
  	$user_id = $this->vendor_id;
  	
  	$products = $WCFM->wcfm_marketplace->dokan_get_vendor_products( $this->vendor_id );
		
		//$args['order_types'] = wc_get_order_types( 'sales-reports' );
		$args['where'][] = array( 'key' => 'order_item_meta__product_id.meta_value', 'operator' => 'in', 'value' => $products );
  	
  	return $args;
  }
	
	// Report Vendor Filter
  function dokan_report_low_in_stock_query_from( $query_from, $stock, $nostock ) {
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
   * Dokan Subscription
   */
  function dokan_wcs_include_subscription( ) {
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
  function dokan_filter_resources( $query_args ) {
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
	public function dokan_filter_bookings_calendar( $booking_ids ) {
		global $WCFM;
		
		$filtered_ids = array();
		
		$product_ids = $WCFM->wcfm_marketplace->dokan_get_vendor_products( $this->vendor_id );

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
   * Dokan Appointments
   */
  function dokan_wca_include_appointments( ) {
  	global $WCFM, $WCFMu, $wpdb, $_POST;
  	
  	$vendor_products = $WCFM->wcfm_marketplace->dokan_get_vendor_products( $this->vendor_id );
		
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
	public function dokan_filter_appointments_calendar( $appointment_ids ) {
		global $WCFM;
		
		$filtered_ids = array();
		
		$product_ids = $WCFM->wcfm_marketplace->dokan_get_vendor_products( $this->vendor_id );

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
	
	// Dokan Filter Staffs
	function dokan_filter_appointment_staffs( $args ) {
		$args['meta_key'] = '_wcfm_vendor';
		$args['meta_value'] = $this->vendor_id;
		return $args;
	}
	
	// Dokan Appointment Staff Manage
	function dokan_wcfm_staffs_manage( $staff_id ) {
		update_user_meta( $staff_id, '_wcfm_vendor', $this->vendor_id );
	}
	
	// Dokan Valid Auction
	function dokan_wcfm_valid_auctions( $valid_actions ) {
		global $WCFM, $WCFMu;
		
		if ($this->vendor_id) {
			$valid_actions = $WCFM->wcfm_marketplace->dokan_get_vendor_products( $this->vendor_id );
		}
		
		if( empty($valid_actions) ) return array(0);
		
		return $valid_actions; 
	}
	
	/**
   * Dokan Rental Quotes
   */
  function dokan_rental_include_quotes( ) {
  	global $WCFM, $wpdb, $_POST;
  	
  	$vendor_products = $WCFM->wcfm_marketplace->dokan_get_vendor_products( $this->vendor_id );
		
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
	
  // Dokan Settings Update
  function dokan_settings_update( $user_id, $wcfm_settings_form ) {
  	global $WCFM, $wpdb, $_POST;
  	
  	if( WCFM_Dependencies::dokanpro_plugin_active_check() ) {
			
  		// Set Facebook Image
			if(isset($wcfm_settings_form['store_seo']) && !empty($wcfm_settings_form['store_seo']['dokan-seo-og-image'])) {
				$wcfm_settings_form['store_seo']['dokan-seo-og-image'] = $WCFM->wcfm_get_attachment_id($wcfm_settings_form['store_seo']['dokan-seo-og-image']);
			} else {
				$wcfm_settings_form['store_seo']['dokan-seo-og-image'] = '';
			}
			
			// Set Banner
			if(isset($wcfm_settings_form['store_seo']) && !empty($wcfm_settings_form['store_seo']['dokan-seo-twitter-image'])) {
				$wcfm_settings_form['store_seo']['dokan-seo-twitter-image'] = $WCFM->wcfm_get_attachment_id($wcfm_settings_form['store_seo']['dokan-seo-twitter-image']);
			} else {
				$wcfm_settings_form['store_seo']['dokan-seo-twitter-image'] = '';
			}
			
			update_user_meta( $user_id, 'dokan_profile_settings', $wcfm_settings_form );
			
			// Shipping Settings
			if(isset($wcfm_settings_form['shipping']) && !empty($wcfm_settings_form['shipping'])) {
				if( !isset( $wcfm_settings_form['shipping']['_dps_shipping_enable'] ) ) $wcfm_settings_form['shipping']['_dps_shipping_enable'] = 'no';
				foreach( $wcfm_settings_form['shipping'] as $wcfm_setting_shipping_key => $wcfm_setting_shipping_field ) {
					update_user_meta( $user_id, $wcfm_setting_shipping_key, $wcfm_setting_shipping_field );
				}
			}
			
			// Shipping Rates
			if(isset($wcfm_settings_form['dps_shipping_rates']) && !empty($wcfm_settings_form['dps_shipping_rates'])) {
				$dps_country_rates = array();
				$dps_state_rates   = array(); 
				foreach( $wcfm_settings_form['dps_shipping_rates'] as $dps_shipping_rates ) {
					if( $dps_shipping_rates['dps_country_to'] ) {
						if( $dps_shipping_rates['dps_shipping_state_rates'] && !empty( $dps_shipping_rates['dps_shipping_state_rates'] ) ) {
							foreach( $dps_shipping_rates['dps_shipping_state_rates'] as $dps_shipping_state_rates ) {
								if( $dps_shipping_state_rates['dps_state_to'] ) {
									$dps_state_rates[$dps_shipping_rates['dps_country_to']][$dps_shipping_state_rates['dps_state_to']] = $dps_shipping_state_rates['dps_state_to_price'];
								}
							}
						}
						$dps_country_rates[$dps_shipping_rates['dps_country_to']] = $dps_shipping_rates['dps_country_to_price'];
					}
				}
				update_user_meta( $user_id, '_dps_country_rates', $dps_country_rates );
				update_user_meta( $user_id, '_dps_state_rates', $dps_state_rates );
			}
		}
  }
  
  function dokan_product_manage_fields_shipping( $shipping_fields, $product_id ) {
  	global $wp, $WCFM, $WCFMu, $wpdb;
  	
  	if( apply_filters( 'wcfm_is_allow_shipping', true ) && WCFM_Dependencies::dokanpro_plugin_active_check() ) {
  		$processing_time = dokan_get_shipping_processing_times();
  		$disable_shipping = 'no';
  		$overwrite_shipping = 'no';
			$additional_price = '';
			$additional_qty = '';
			$dps_processing_time = '';
			
			if( $product_id ) {
				$disable_shipping = get_post_meta( $product_id, '_disable_shipping', true ) ? get_post_meta( $product_id, '_disable_shipping', true ) : 'no';
				$overwrite_shipping = get_post_meta( $product_id, '_overwrite_shipping', true ) ? get_post_meta( $product_id, '_overwrite_shipping', true ) : 'no';
				$additional_price = get_post_meta( $product_id, '_additional_price', true ) ? get_post_meta( $product_id, '_additional_price', true ) : '';
				$additional_qty = get_post_meta( $product_id, '_additional_qty', true ) ? get_post_meta( $product_id, '_additional_qty', true ) : '';
				$dps_processing_time = get_post_meta( $product_id, '_dps_processing_time', true ) ? get_post_meta( $product_id, '_dps_processing_time', true ) : '';
			}
			
			$wcv_shipping_fileds = array( 
																		"_disable_shipping" => array('label' => __('Disable Shipping', 'wc-frontend-manager') , 'type' => 'checkbox', 'class' => 'wcfm-checkbox', 'label_class' => 'wcfm_title checkbox_title', 'value' => 'yes', 'dfvalue' => $disable_shipping, 'hints' => __('Disable shipping for this product', 'dokan') )
																	);
			$shipping_fields = array_merge( $wcv_shipping_fileds, $shipping_fields );
			
			$wcv_shipping_fileds = apply_filters( 'wfm_dokan_product_manager_shipping_fileds', array( 
																		"_overwrite_shipping" => array('label' => __('Override Shipping', 'wc-frontend-manager') , 'type' => 'checkbox', 'class' => 'wcfm-checkbox', 'label_class' => 'wcfm_title checkbox_title', 'value' => 'yes', 'dfvalue' => $overwrite_shipping, 'hints' => __('Override your store\'s default shipping cost for this product', 'dokan') ),
																		"_additional_price" => array('label' => __('Additional Price', 'dokan'), 'placeholder' => '0.00', 'type' => 'text', 'class' => 'wcfm-text', 'label_class' => 'wcfm_title', 'value' => $additional_price, 'hints' => __('If a customer buys more than one type product from your store, first product of the every second type will be charged with this price', 'dokan') ),
																		"_additional_qty" => array('label' => __('Per Qty Additional Price', 'dokan'), 'placeholder' => '0.00', 'type' => 'text', 'class' => 'wcfm-text', 'label_class' => 'wcfm_title', 'value' => $additional_qty, 'hints' => __('Every second product of same type will be charged with this price', 'dokan') ),
																		"_dps_processing_time" => array('label' => __('Processing Time', 'dokan'), 'type' => 'select', 'class' => 'wcfm-select', 'label_class' => 'wcfm_title', 'options' => $processing_time, 'value' => $dps_processing_time, 'hints' => __('The time required before sending the product for delivery', 'dokan') ),
																	) );
			$shipping_fields = array_merge( $shipping_fields, $wcv_shipping_fileds );
			
			if( isset( $shipping_fields['shipping_class'] ) ) {
				$shipping_fields['shipping_class']['hints'] = __( 'Shipping classes are used by certain shipping methods to group similar products.', 'dokan' );
			}
		}
  	
  	return $shipping_fields;
  }
  
  function dokan_shipping_product_meta_save( $new_product_id, $wcfm_products_manage_form_data ) {
		global $wpdb, $WCFM, $_POST, $wpdb;
		
		if( apply_filters( 'wcfm_is_allow_shipping', true ) && WCFM_Dependencies::dokanpro_plugin_active_check() ) {
			if( isset( $wcfm_products_manage_form_data['_disable_shipping'] ) ) {
				update_post_meta( $new_product_id, '_disable_shipping', $wcfm_products_manage_form_data['_disable_shipping'] );
			} else {
				delete_post_meta( $new_product_id, '_disable_shipping' );
			}
			if( isset( $wcfm_products_manage_form_data['_overwrite_shipping'] ) ) {
				update_post_meta( $new_product_id, '_overwrite_shipping', $wcfm_products_manage_form_data['_overwrite_shipping'] );
			} else {
				delete_post_meta( $new_product_id, '_overwrite_shipping' );
			}
			if( isset( $wcfm_products_manage_form_data['_additional_price'] ) ) {
				update_post_meta( $new_product_id, '_additional_price', $wcfm_products_manage_form_data['_additional_price'] );
			}
			if( isset( $wcfm_products_manage_form_data['_additional_qty'] ) ) {
				update_post_meta( $new_product_id, '_additional_qty', $wcfm_products_manage_form_data['_additional_qty'] );
			}
			if( isset( $wcfm_products_manage_form_data['_dps_processing_time'] ) ) {
				update_post_meta( $new_product_id, '_dps_processing_time', $wcfm_products_manage_form_data['_dps_processing_time'] );
			}
		}
  }
}