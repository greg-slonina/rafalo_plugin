<?php

/**
 * WCFMu plugin core
 *
 * WCFM Marketplace Orders Support
 *
 * @author 		WC Lovers
 * @package 	wcfmu/core
 * @version   5.2.0
 */
 
class WCFMu_Orders {
	
	public function __construct() {
    global $WCFMu;
    
    // Orders Manage Query Var Filter
		add_filter( 'wcfm_query_vars', array( &$this, 'wcorder_wcfm_query_vars' ), 20 );
		add_filter( 'wcfm_endpoint_title', array( &$this, 'wcorder_wcfm_endpoint_title' ), 20, 2 );
		add_action( 'init', array( &$this, 'wcorder_wcfm_init' ), 20 );
		
		add_filter( 'wcfm_menus', array( &$this, 'wcorder_wcfm_menus' ), 20 );
		
		// Order Edit Button
		add_action( 'after_wcfm_orders_details_items', array( &$this, 'wcorder_order_edit_button' ), 50, 3 );
		
		// Generate Order Edit Form Html
    add_action('wp_ajax_wcfm_edit_order_form_html', array( &$this, 'wcorder_order_edit_form_html' ) );
		
		// Orders Quick Action - Add Order
		add_action( 'wcfm_orders_quick_actions', array( &$this, 'wcorder_order_manage' ) );
		add_action( 'wcfm_after_order_quick_actions', array( &$this, 'wcorder_order_manage' ) );
		
		// Orders Manage Load WCFMu Scripts
		add_action( 'wcfm_load_scripts', array( &$this, 'wcorder_load_scripts' ), 30 );
		add_action( 'after_wcfm_load_scripts', array( &$this, 'wcorder_load_scripts' ), 30 );
		
		// Orders Manage Load WCFMu Styles
		add_action( 'wcfm_load_styles', array( &$this, 'wcorder_load_styles' ), 30 );
		add_action( 'after_wcfm_load_styles', array( &$this, 'wcorder_load_styles' ), 30 );
		
		// Orders Manage Load WCFMu views
		add_action( 'wcfm_load_views', array( &$this, 'wcorder_load_views' ), 30 );
		
    // Get Customer Address
    add_action('wp_ajax_wcfm_orders_manage_customer_address', array( &$this, 'wcfm_orders_manage_customer_address' ) );
    
    // Orders Manage Add Customer Ajax Controllers
		add_action( 'after_wcfm_ajax_controller', array( &$this, 'wcorder_ajax_controller' ), 30 );
		
		// WC Deposit Order Actions
		add_action( 'wcfm_after_order_itemmeta', array( $this, 'wc_deposit_order_actions' ), 100, 4 );
		
		// WC Deposit Order Actions Handlers
		add_action( 'wcfm_init', array( $this, 'wc_deposit_order_action_handler' ), 20 );
    
  }
  
  /**
   * Order Manage Query Var
   */
  function wcorder_wcfm_query_vars( $query_vars ) {
  	$wcfm_modified_endpoints = wcfm_get_option( 'wcfm_endpoints', array() );
  	
		$query_orders_vars = array(
			'wcfm-orders-manage'       => ! empty( $wcfm_modified_endpoints['wcfm-orders-manage'] ) ? $wcfm_modified_endpoints['wcfm-orders-manage'] : 'orders-manage',
		);
		
		$query_vars = array_merge( $query_vars, $query_orders_vars );
		
		return $query_vars;
  }
  
  /**
   * Order Manage End Point Title
   */
  function wcorder_wcfm_endpoint_title( $title, $endpoint ) {
  	global $wp;
  	switch ( $endpoint ) {
			case 'wcfm-orders-manage' :
				$title = __( 'Create Order', 'wc-frontend-manager-ultimate' );
			break;
  	}
  	
  	return $title;
  }
  
  /**
   * Order Manage Endpoint Intialize
   */
  function wcorder_wcfm_init() {
  	global $WCFM_Query;
	
		// Intialize WCFM End points
		$WCFM_Query->init_query_vars();
		$WCFM_Query->add_endpoints();
		
		if( !get_option( 'wcfm_updated_end_point_wc_orderss' ) ) {
			// Flush rules after endpoint update
			flush_rewrite_rules();
			update_option( 'wcfm_updated_end_point_wc_orderss', 1 );
		}
  }
  
  /**
   * Order Manage Menu
   */
  function wcorder_wcfm_menus( $menus ) {
  	global $WCFM;
  	
  	if( apply_filters( 'wcfm_is_allow_add_new_order', true ) ) {
			$menus['wcfm-orders'] = array( 'label'  => __( 'Orders', 'wc-frontend-manager'),
																		 'url'        => get_wcfm_orders_url(),
																		 'icon'       => 'shopping-cart',
																		 'has_new'    => 'yes',
																		 'new_class'  => 'wcfm_sub_menu_items_order_manage',
																		 'new_url'    => get_wcfm_manage_order_url(),
																		 'capability' => 'wcfm_is_allow_orders',
																		 'submenu_capability' => 'wcfm_is_allow_manage_order',
																		 'priority'   => 35
																		);
		}
  	return $menus;
  }
  
  /**
   * Orders Edit Button
   */
  function wcorder_order_edit_button( $order_id, $order, $line_items ) {
  	global $WCFM, $WCFMu;
  	
  	$order_status = sanitize_title( $order->get_status() );
		if( in_array( $order_status, apply_filters( 'wcfm_edit_order_block_status', array( 'failed', 'cancelled', 'refunded', 'processing', 'completed' ) ) ) ) return;
  	
		echo '<br /><a class="wcfm_order_edit_request add_new_wcfm_ele_dashboard" href="#" data-order="' . $order_id . '"><span class="wcfmfa fa-pencil-alt text_tip"></span><span class="text">' . __( 'Edit Order', 'wc-frontend-manager-ultimate' ) . '</span></a>';
  }
  
  /**
   * Order Edit Form HTML
   */
  function wcorder_order_edit_form_html() {
  	global $WCFM, $WCFMu, $_POST;
  	if( isset( $_POST['order_id'] ) && !empty( $_POST['order_id'] ) ) {
  		$WCFMu->template->get_template( 'orders/wcfmu-view-orders-edit-popup.php', array( 'order_id' => sanitize_text_field( $_POST['order_id'] ) ) );
  	}
  	die;
  }
  
  /**
   * Orders Dashaboard Manage Order Link
   */
  function wcorder_order_manage( $order_id = '' ) {
  	if( apply_filters( 'wcfm_is_allow_add_new_order', true ) ) {
  		echo '<a id="add_new_order_dashboard" class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_manage_order_url().'" data-tip="' . __('Add New Order', 'wc-frontend-manager-ultimate') . '"><span class="wcfmfa fa-cart-plus"></span><span class="text">' . __( 'Add New', 'wc-frontend-manager') . '</span></a>';
  	}
  }
  
  /**
   * Order Manage Scripts
   */
  public function wcorder_load_scripts( $end_point ) {
	  global $WCFM, $WCFMu;
    
	  switch( $end_point ) {
      case 'wcfm-orders-manage':
      	$WCFM->library->load_select2_lib();
      	$WCFM->library->load_collapsible_lib();
      	$WCFM->library->load_multiinput_lib();
	    	wp_enqueue_script( 'wcfm_orders_manage_js', $WCFMu->library->js_lib_url . 'orders/wcfmu-script-orders-manage.js', array('jquery'), $WCFMu->version, true );
	    	
	    	// Localized Script
        $wcfm_messages = get_wcfm_orders_manage_messages();
			  wp_localize_script( 'wcfm_orders_manage_js', 'wcfm_orders_manage_messages', $wcfm_messages );
      break;
	  }
	}
	
	/**
   * Order Manage Styles
   */
	public function wcorder_load_styles( $end_point ) {
	  global $WCFM, $WCFMu;
		
	  switch( $end_point ) {
	  	case 'wcfm-orders-manage':
	  		wp_enqueue_style( 'wcfm_orders_manage_css',  $WCFMu->library->css_lib_url . 'orders/wcfmu-style-orders-manage.css', array(), $WCFMu->version );
	  	break;
	  }
	}
	
	/**
   * Order Manage Views
   */
  public function wcorder_load_views( $end_point ) {
	  global $WCFM, $WCFMu;
	  
	  switch( $end_point ) {
      case 'wcfm-orders-manage':
        $WCFMu->template->get_template( 'orders/wcfmu-view-orders-manage.php' );
      break;
	  }
	}
	
	/**
	 * Load Customer Address
	 */
	function wcfm_orders_manage_customer_address() {
		global $WCFM, $WCFMu;
		
		$customer_address = array();
		$customer_id      = absint( $_POST['customer_id'] );
		
		if( $customer_id ) {
			$wcfm_order_billing_fields = array( 
																					'billing_first_name'  => 'bfirst_name',
																					'billing_last_name'   => 'blast_name',
																					'billing_phone'       => 'bphone',
																					'billing_address_1'   => 'baddr_1',
																					'billing_address_2'   => 'baddr_2',
																					'billing_country'     => 'bcountry',
																					'billing_city'        => 'bcity',
																					'billing_state'       => 'bstate',
																					'billing_postcode'    => 'bzip'
																				);
			
			foreach( $wcfm_order_billing_fields as $wcfm_order_default_key => $wcfm_order_default_field ) {
				$customer_address[$wcfm_order_default_field] = get_user_meta( $customer_id, $wcfm_order_default_key, true );
			}
			
			$wcfm_order_shipping_fields = array( 
																					'shipping_first_name'  => 'sfirst_name',
																					'shipping_last_name'   => 'slast_name',
																					'shipping_address_1'   => 'saddr_1',
																					'shipping_address_2'   => 'saddr_2',
																					'shipping_country'     => 'scountry',
																					'shipping_city'        => 'scity',
																					'shipping_state'       => 'sstate',
																					'shipping_postcode'    => 'szip'
																				);
			
			foreach( $wcfm_order_shipping_fields as $wcfm_order_default_key => $wcfm_order_default_field ) {
				$customer_address[$wcfm_order_default_field] = get_user_meta( $customer_id, $wcfm_order_default_key, true );
			}
			
		}
		
		wp_send_json( $customer_address );
	}
	
	/**
   * Order Manage Ajax Controllers
   */
  public function wcorder_ajax_controller() {
  	global $WCFM, $WCFMu;
  	
  	$controllers_path = $WCFMu->plugin_path . 'controllers/orders/';
  	
  	$controller = '';
  	if( isset( $_POST['controller'] ) ) {
  		$controller = $_POST['controller'];
  		
  		switch( $controller ) {
				
				case 'wcfm-orders-manage':
					include_once( $controllers_path . 'wcfm-controller-orders-manage.php' );
					new WCFMu_Orders_Manage_Controller();
				break;
				
				case 'wcfm-orders-edit':
					include_once( $controllers_path . 'wcfm-controller-orders-edit.php' );
					new WCFMu_Orders_Edit_Controller();
				break;
  		}
  	}
  }
  
  function wc_deposit_order_actions( $item_id, $item, $_product, $order ) {
  	global $WCFM, $WCFMu, $wpdb;
  	
		if( class_exists( 'WC_Deposits_Order_Item_Manager' ) ) {
			if ( WC_Deposits_Order_Item_Manager::is_deposit( $item ) ) {

				if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
					global $wpdb;
					$order_id = $wpdb->get_var( $wpdb->prepare( "SELECT order_id FROM {$wpdb->prefix}woocommerce_order_items WHERE order_item_id = %d", $item_id ) );
					$currency = $order->get_order_currency();
				} else {
					$order_id = $item->get_order_id();
					$currency = $order->get_currency();
				}
	
				// Plans
				if ( $payment_plan = WC_Deposits_Order_Item_Manager::get_payment_plan( $item ) ) {
					//echo '<a href="' . esc_url( admin_url( 'edit.php?post_status=wc-scheduled-payment&post_type=shop_order&post_parent=' . $order_id ) ) . '" target="_blank" class="button button-small">' . __( 'View Scheduled Payments', 'woocommerce-deposits' ) . '</a>';
	
				// Regular deposits
				} else {
					$remaining                  = $item['deposit_full_amount'] - $order->get_line_total( $item, true );
					$remaining_balance_order_id = ! empty( $item['remaining_balance_order_id'] ) ? absint( $item['remaining_balance_order_id'] ) : 0;
					$remaining_balance_paid     = ! empty( $item['remaining_balance_paid'] );
	
					if ( $remaining_balance_order_id && ( $remaining_balance_order = wc_get_order( $remaining_balance_order_id ) ) ) {
						echo '<a href="' . esc_url( get_wcfm_view_order_url( absint( $remaining_balance_order_id ) ) ) . '" target="_blank" class="button button-small">' . sprintf( __( 'Remainder - Invoice #%1$s', 'woocommerce-deposits' ), $remaining_balance_order->get_order_number() ) . '</a>';
					} elseif( $remaining_balance_paid ) {
						printf( __( 'The remaining balance of %s (plus tax) for this item was paid offline.', 'woocommerce-deposits' ), wc_price( $remaining, array( 'currency' => $currency ) ) );
						echo ' <a href="' . esc_url( wp_nonce_url( add_query_arg( array( 'mark_deposit_unpaid' => $item_id ) ), 'mark_deposit_unpaid', 'mark_deposit_unpaid_nonce' ) ) . '" class="button button-small">' . sprintf( __( 'Unmark as Paid', 'woocommerce-deposits' ) ) . '</a>';
					} elseif ( ! $this->has_deposit_without_future_payments( $order_id ) ) {
						?>
						<a href="<?php echo esc_url( wp_nonce_url( add_query_arg( array( 'post' => $order_id, 'invoice_remaining_balance' => $item_id ), get_wcfm_view_order_url( $order_id ) ), 'invoice_remaining_balance', 'invoice_remaining_balance_nonce' ) ); ?>" class="button button-small"><?php _e( 'Invoice Remaining Balance', 'woocommerce-deposits' ); ?></a>
						<a href="<?php echo esc_url( wp_nonce_url( add_query_arg( array( 'post' => $order_id, 'mark_deposit_fully_paid' => $item_id ), get_wcfm_view_order_url( $order_id ) ), 'mark_deposit_fully_paid', 'mark_deposit_fully_paid_nonce' ) ); ?>" class="button button-small"><?php printf( __( 'Mark Paid (offline)', 'woocommerce-deposits' ) ); ?></a>
						<?php
					}
				}
			} elseif ( ! empty( $item['original_order_id'] ) ) {
				echo '<a href="' . esc_url( get_wcfm_view_order_url( absint( $item['original_order_id'] ) ) ) . '" target="_blank" class="button button-small">' . __( 'View Original Order', 'woocommerce-deposits' ) . '</a>';
			}
		}
  }
  
  /**
	 * Check if the order contains a deposit without additional payments.
	 * E.g. discount was applied.
	 *
	 * @param  int|WC_Order $order Order ID or object.
	 * @return boolean
	 */
	public static function has_deposit_without_future_payments( $order ) {
		if ( is_numeric( $order ) ) {
			$order = wc_get_order( $order );
		}

		if ( ! $order ) {
			return false;
		}

		foreach( $order->get_items() as $item ) {
			if ( 'line_item' === $item['type'] && ! empty( $item['is_deposit'] ) ) {
				$deposit_full_amount       = (int) $item['_deposit_full_amount_ex_tax'];
				$deposit_deposit_amount    = (int) $item['_deposit_deposit_amount_ex_tax'];
				$deposit_deferred_discount = (int) $item['_deposit_deferred_discount'];

				if ( $deposit_full_amount - $deposit_deposit_amount === $deposit_deferred_discount ) {
					return true;
				}
			}
		}
		return false;
	}
  
  /**
	 * Create and redirect to an invoice.
	 */
	public function wc_deposit_order_action_handler() {
		global $wpdb;

		$action  = false;
		$item_id = false;
		
		if ( ! empty( $_GET['mark_deposit_unpaid'] ) && isset( $_GET['mark_deposit_unpaid_nonce'] ) && wp_verify_nonce( $_GET['mark_deposit_unpaid_nonce'], 'mark_deposit_unpaid' ) ) {
			$action  = 'mark_deposit_unpaid';
			$item_id = absint( $_GET['mark_deposit_unpaid'] );
		}

		if ( ! empty( $_GET['mark_deposit_fully_paid'] ) && isset( $_GET['mark_deposit_fully_paid_nonce'] ) && wp_verify_nonce( $_GET['mark_deposit_fully_paid_nonce'], 'mark_deposit_fully_paid' ) ) {
			$action  = 'mark_deposit_fully_paid';
			$item_id = absint( $_GET['mark_deposit_fully_paid'] );
		}

		if ( ! empty( $_GET['invoice_remaining_balance'] ) && isset( $_GET['invoice_remaining_balance_nonce'] ) && wp_verify_nonce( $_GET['invoice_remaining_balance_nonce'], 'invoice_remaining_balance' ) ) {
			$action  = 'invoice_remaining_balance';
			$item_id  = absint( $_GET['invoice_remaining_balance'] );
		}

		if ( ! $item_id ) {
			return;
		}

		if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
			$order_id = $wpdb->get_var( $wpdb->prepare( "SELECT order_id FROM {$wpdb->prefix}woocommerce_order_items WHERE order_item_id = %d", $item_id ) );
		} else {
			$order_id = wc_get_order_id_by_order_item_id( $item_id );
		}

		$order_id = $wpdb->get_var( $wpdb->prepare( "SELECT order_id FROM {$wpdb->prefix}woocommerce_order_items WHERE order_item_id = %d", $item_id ) );
		$order    = wc_get_order( $order_id );
		$item     = false;

		foreach ( $order->get_items() as $order_item_id => $order_item ) {
			if ( $item_id === $order_item_id ) {
				$item = $order_item;
			}
		}

		if ( ! $item || empty( $item['is_deposit'] ) ) {
			return;
		}

		switch ( $action ) {
			case 'mark_deposit_unpaid' :
				wc_delete_order_item_meta( $item_id, '_remaining_balance_paid', 1, true );
				wp_redirect( get_wcfm_view_order_url( absint( $order_id ) ) );
				exit;
			case 'mark_deposit_fully_paid' :
				wc_add_order_item_meta( $item_id, '_remaining_balance_paid', 1 );
				wp_redirect( get_wcfm_view_order_url(absint( $order_id ) ) );
				exit;
			case 'invoice_remaining_balance' :
				// Used for products with fixed deposits or percentage based deposits. Not used for payment plan products
				// See WC_Deposits_Schedule_Order_Manager::schedule_orders_for_plan for creating orders for products with payment plans

				// First, get the deposit_full_amount_ex_tax - this contains the full amount for the item excluding tax - see
				// WC_Deposits_Cart_Manager::add_order_item_meta_legacy or add_order_item_meta for where we set this amount
				// Note that this is for the line quantity, not necessarily just for quantity 1
				$full_amount_excl_tax = floatval( $item['deposit_full_amount_ex_tax'] );

				// Next, get the initial deposit already paid, excluding tax
				$amount_already_paid = floatval( $item['deposit_deposit_amount_ex_tax'] );

				// Then, set the item subtotal that will be used in create order to the full amount less the amount already paid
				$subtotal = $full_amount_excl_tax - $amount_already_paid;
				
				// Add WC3.2 Coupons upgrade compatibility
				if( version_compare( WC_VERSION, '3.2', '>=' ) ){
					// Lastly, subtract the deferred discount from the subtotal to get the total to be used to create the order
					$discount_excl_tax = isset($item['deposit_deferred_discount_ex_tax']) ? floatval( $item['deposit_deferred_discount_ex_tax'] ) : 0;
					$total = $subtotal - $discount_excl_tax;
				} else {
					$discount = floatval( $item['deposit_deferred_discount'] );
					$total = empty( $discount ) ? $subtotal : $subtotal - $discount;
				}
				
				if( version_compare( WC_VERSION, '4.4', '<' ) ) {
					$product = $order->get_product_from_item( $item );
				} else {
					$product = $item->get_product();
				}
				// And then create an order with this item
				$create_item = array(
					'product'   => $product,
					'qty'       => $item['qty'],
					'subtotal'  => $subtotal,
					'total'     => $total
				);

				$new_order_id = $this->create_order( current_time( 'timestamp' ), $order_id, 2, $create_item, 'pending-deposit' );

				wc_add_order_item_meta( $item_id, '_remaining_balance_order_id', $new_order_id );

				// Email invoice
				$emails = WC_Emails::instance();
				$emails->customer_invoice( wc_get_order( $new_order_id ) );

				wp_redirect( get_wcfm_view_order_url( absint( $new_order_id ) ) );
				exit;
		}
	}
	
	/**
	 * Create a scheduled order.
	 *
	 * @param  string $payment_date
	 * @param  int    $original_order_id
	 * @param  int    $payment_number
	 * @param  array  $item
	 * @param  string $status
	 * @return id
	 */
	public static function create_order( $payment_date, $original_order_id, $payment_number, $item, $status = '' ) {

		$original_order = wc_get_order( $original_order_id );

		try {
			$new_order = new WC_Order;
			$new_order->set_props( array(
				'status'              => $status,
				'customer_id'         => $original_order->get_user_id(),
				'customer_note'       => $original_order->get_customer_note(),
				'created_via'         => 'wc_deposits',
				'billing_first_name'  => $original_order->get_billing_first_name(),
				'billing_last_name'   => $original_order->get_billing_last_name(),
				'billing_company'     => $original_order->get_billing_company(),
				'billing_address_1'   => $original_order->get_billing_address_1(),
				'billing_address_2'   => $original_order->get_billing_address_2(),
				'billing_city'        => $original_order->get_billing_city(),
				'billing_state'       => $original_order->get_billing_state(),
				'billing_postcode'    => $original_order->get_billing_postcode(),
				'billing_country'     => $original_order->get_billing_country(),
				'billing_email'       => $original_order->get_billing_email(),
				'billing_phone'       => $original_order->get_billing_phone(),
				'shipping_first_name' => $original_order->get_shipping_first_name(),
				'shipping_last_name'  => $original_order->get_shipping_last_name(),
				'shipping_company'    => $original_order->get_shipping_company(),
				'shipping_address_1'  => $original_order->get_shipping_address_1(),
				'shipping_address_2'  => $original_order->get_shipping_address_2(),
				'shipping_city'       => $original_order->get_shipping_city(),
				'shipping_state'      => $original_order->get_shipping_state(),
				'shipping_postcode'   => $original_order->get_shipping_postcode(),
				'shipping_country'    => $original_order->get_shipping_country(),
			) );
			$new_order->save();
		} catch ( Exception $e ) {
			$original_order->add_order_note( sprintf( __( 'Error: Unable to create follow up payment (%s)', 'woocommerce-deposits' ), $e->getMessage() ) );
			return;
		}

		// Handle items
		$item_id = $new_order->add_product( $item['product'], $item['qty'], array(
			'totals' => array(
				'subtotal'     => $item['subtotal'], // cost before discount (for line quantity, not just unit)
				'total'        => $item['total'], // item cost (after discount) (for line quantity, not just unit)
				'subtotal_tax' => 0, // calculated within (WC_Abstract_Order) $new_order->calculate_totals
				'tax'          => 0, // calculated within (WC_Abstract_Order) $new_order->calculate_totals
			)
		) );

		$new_order->set_parent_id( $original_order_id );
		$new_order->set_date_created( date( 'Y-m-d H:i:s', $payment_date ) );

		// (WC_Abstract_Order) Calculate totals by looking at the contents of the order. Stores the totals and returns the orders final total.
		$new_order->calculate_totals( wc_tax_enabled() );
		$new_order->save();

		wc_add_order_item_meta( $item_id, '_original_order_id', $original_order_id );

		/* translators: Payment number for product's title */
		wc_update_order_item( $item_id, array( 'order_item_name' => sprintf( __( 'Payment #%d for %s', 'woocommerce-deposits' ), $payment_number, $item['product']->get_title() ) ) );

		do_action( 'woocommerce_deposits_create_order', $new_order->get_id() );
		return $new_order->get_id();
	}
}