<?php
/**
 * WCFMu plugin core
 *
 * Plugin Shipment Tracking Controler
 *
 * @author 		WC Lovers
 * @package 	wcfmu/core
 * @version   4.1.8
 */
 
class WCFMu_Shipment_Tracking {
	
	public function __construct() {
		// WC Vendors Mark as Shipped
    add_action( 'wp_ajax_wcfm_wcvendors_order_mark_shipped', array( &$this, 'wcfm_wcvendors_order_mark_shipped' ) );
    
    // WC Product Vendors Mark as Fulfilled
    add_action( 'wp_ajax_wcfm_wcpvendors_order_mark_fulfilled', array( &$this, 'wcfm_wcpvendors_order_mark_fulfilled' ) );
    
    // WC Marketplace Mark as Shipped
    add_action( 'wp_ajax_wcfm_wcmarketplace_order_mark_shipped', array( &$this, 'wcfm_wcmarketplace_order_mark_shipped' ) );
    
    // WCfM Marketplace Mark as Shipped
    add_action( 'wp_ajax_wcfm_wcfmmarketplace_order_mark_shipped', array( &$this, 'wcfm_wcfmmarketplace_order_mark_shipped' ) );
    
    // Dokan Mark as Shipped
    add_action( 'wp_ajax_wcfm_dokan_order_mark_shipped', array( &$this, 'wcfm_dokan_order_mark_shipped' ) );
    
    // WCFM Mark as Received
    add_action( 'wp_ajax_wcfm_mark_as_recived', array( &$this, 'wcfm_mark_as_recived' ) );
    
    if( apply_filters( 'wcfm_is_allow_shipping_tracking', true ) ) {
			if( !wcfm_is_vendor() ) {
				add_filter( 'wcfm_orders_actions', array( &$this, 'wcfmu_shipping_tracking_orders_actions' ), 20, 3 );
			} else {
				add_filter( 'dokan_orders_actions', array( &$this, 'wcfmu_dokan_shipment_tracking_orders_actions' ), 20, 3 );
				add_filter( 'wcmarketplace_orders_actions', array( &$this, 'wcfmu_wcmarketplace_shipping_tracking_orders_actions' ), 20, 4 );
				add_filter( 'wcfmmarketplace_orders_actions', array( &$this, 'wcfmu_wcfmmarketplace_shipping_tracking_orders_actions' ), 20, 4 );
				add_filter( 'wcvendors_orders_actions', array( &$this, 'wcfmu_wcvendors_shipment_tracking_orders_actions' ), 20, 4 );
				add_filter( 'wcpvendors_orders_actions', array( &$this, 'wcfmu_wcpvendors_shipment_tracking_orders_actions' ), 20, 4 );
			}
		}
		
		// Vendor Order Shippment Tracking
		add_filter( 'woocommerce_order_item_display_meta_key', array( &$this, 'wcfm_tracking_url_display_label' ) );
		add_action( 'woocommerce_order_item_meta_end', array( &$this, 'wcfm_order_tracking_response' ), 20, 3 );
    
    // Shipment Tracking message type
		add_filter( 'wcfm_message_types', array( &$this, 'wcfm_shipment_tracking_message_types' ), 75 );
		
	}
	
	/**
	 * Mark WC Vendors order as Shipped
	 */
	function wcfm_wcvendors_order_mark_shipped() {
		global $WCFM, $WCFMu, $woocommerce, $wpdb;
		
		$user_id = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );
		
		if ( !empty( $_POST['orderid'] ) ) {
			$wcfm_tracking_data = array();
			parse_str($_POST['tracking_data'], $wcfm_tracking_data);
			$order_id       = $wcfm_tracking_data['wcfm_tracking_order_id'];
			$product_id     = $wcfm_tracking_data['wcfm_tracking_product_id'];
			$order_item_id  = $wcfm_tracking_data['wcfm_tracking_order_item_id'];
			$tracking_url   = $wcfm_tracking_data['wcfm_tracking_url'];
			$tracking_code  = $wcfm_tracking_data['wcfm_tracking_code'];
			$order          = wc_get_order( $order_id );
			
			$tracking_url = apply_filters( 'wcfm_tracking_url', $tracking_url, $tracking_code, $order_id );
			
			if( $tracking_code && $tracking_url ) {
				if( wcfm_is_vendor() ) {
					$vendors = WCV_Vendors::get_vendors_from_order( $order );
					$vendor_ids = array_keys( $vendors );
					if ( !in_array( $user_id, $vendor_ids ) ) {
						_e( 'You are not allowed to modify this order.', 'wc-frontend-manager-ultimate' );
						die; 
					}
					$shippers = (array) get_post_meta( $order_id, 'wc_pv_shipped', true );
		
					// If not in the shippers array mark as shipped otherwise do nothing. 
					if( !in_array($user_id, $shippers)) {
						$shippers[] = $user_id;
						//$mails = $woocommerce->mailer()->get_emails();
						//if ( !empty( $mails ) ) {
						//	$mails[ 'WC_Email_Notify_Shipped' ]->trigger( $order_id, $user_id );
						//}
						//do_action('wcvendors_vendor_ship', $order_id, $user_id);
						_e( 'Order marked shipped.', 'wc-frontend-manager-ultimate' );
					} elseif ( false != ( $key = array_search( $user_id, $shippers) ) ) {
						unset( $shippers[$key] ); // Remove user from the shippers array
					}
					
					$shop_name =  $WCFM->wcfm_vendor_support->wcfm_get_vendor_store_by_vendor( absint($user_id) );
					$wcfm_messages = sprintf( __( 'Vendor <b>%s</b> has shipped <b>%s</b> to customer.<br/>Tracking Code : %s <br/>Tracking URL : <a class="wcfm_dashboard_item_title" target="_blank" href="%s">%s</a>', 'wc-frontend-manager-ultimate' ), $shop_name, get_the_title( $product_id ), $tracking_code, $tracking_url, $tracking_url );
					$WCFM->wcfm_notification->wcfm_send_direct_message( $user_id, 0, 0, 1, $wcfm_messages, 'shipment_tracking' );
					$comment_id = $order->add_order_note( $wcfm_messages, '1');
					
					update_post_meta( $order_id, 'wc_pv_shipped', $shippers );
				} else {
					$comment_id = $order->add_order_note( sprintf( __( 'Product <b>%s</b> has been shipped to customer.<br/>Tracking Code : %s <br/>Tracking URL : <a href="%s">%s</a>', 'wc-frontend-manager-ultimate' ), get_the_title( $product_id ), $tracking_code, $tracking_url, $tracking_url ), '1');
				}
				
				// Update Shipping Tracking Info
				$this->updateShippingTrackingInfo( $order_id, $order_item_id, $tracking_code, $tracking_url, $product_id );
			}
			
			do_action( 'wcfm_after_order_mark_shipped', $order_id, $order_item_id, $tracking_code, $tracking_url, $product_id, $wcfm_tracking_data );
		}
	}
	
	/**
	 * Mark WC Product Vendors order as Fulfilled
	 */
	function wcfm_wcpvendors_order_mark_fulfilled() {
		global $WCFM, $WCFMu, $woocommerce, $wpdb;
		
		$user_id = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );
		
		if ( !empty( $_POST['orderid'] ) ) {
			$wcfm_tracking_data = array();
			parse_str($_POST['tracking_data'], $wcfm_tracking_data);
			$order_id       = $wcfm_tracking_data['wcfm_tracking_order_id'];
			$product_id     = $wcfm_tracking_data['wcfm_tracking_product_id'];
			$order_item_id  = $wcfm_tracking_data['wcfm_tracking_order_item_id'];
			$tracking_url   = $wcfm_tracking_data['wcfm_tracking_url'];
			$tracking_code  = $wcfm_tracking_data['wcfm_tracking_code'];
			$order          = wc_get_order( $order_id );
			
			$tracking_url = apply_filters( 'wcfm_tracking_url', $tracking_url, $tracking_code, $order_id );
			
			if( $order_item_id ) {
				if( $tracking_code && $tracking_url ) {
					if( wcfm_is_vendor() ) {
						$vendor_data = WC_Product_Vendors_Utils::get_vendor_data_from_user();
						
						WC_Product_Vendors_Utils::set_fulfillment_status( absint( $order_item_id ), 'fulfilled' );
				
						WC_Product_Vendors_Utils::send_fulfill_status_email( $vendor_data, 'fulfilled', $order_item_id );
						
						WC_Product_Vendors_Utils::clear_reports_transients();
						
						$shop_name = ! empty( $vendor_data['shop_name'] ) ? $vendor_data['shop_name'] : '';
						$wcfm_messages = sprintf( __( 'Vendor <b>%s</b> has shipped <b>%s</b> to customer.<br/>Tracking Code : %s <br/>Tracking URL : <a class="wcfm_dashboard_item_title" target="_blank" href="%s">%s</a>', 'wc-frontend-manager-ultimate' ), $shop_name, get_the_title( $product_id ), $tracking_code, $tracking_url, $tracking_url );
						$WCFM->wcfm_notification->wcfm_send_direct_message( $user_id, 0, 0, 1, $wcfm_messages, 'shipment_tracking' );
						$comment_id = $order->add_order_note( $wcfm_messages, '1');
					} else {
						$comment_id = $order->add_order_note( sprintf( __( 'Product <b>%s</b> has been shipped to customer.<br/>Tracking Code : %s <br/>Tracking URL : <a href="%s">%s</a>', 'wc-frontend-manager-ultimate' ), get_the_title( $product_id ), $tracking_code, $tracking_url, $tracking_url ), '1');
					}
					
					// Update Shipping Tracking Info
					$this->updateShippingTrackingInfo( $order_id, $order_item_id, $tracking_code, $tracking_url, $product_id );
				}
				
				do_action( 'wcfm_after_order_mark_shipped', $order_id, $order_item_id, $tracking_code, $tracking_url, $product_id, $wcfm_tracking_data );
			}
		}
		
		echo "complete";
		die;
	}
	
	/**
	 * Mark WC Marketplace order as Shipped
	 */
	function wcfm_wcmarketplace_order_mark_shipped() {
		global $WCFM, $WCFMu, $woocommerce, $wpdb;
		
		$user_id = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );
		
		if ( !empty( $_POST['orderid'] ) ) {
			$wcfm_tracking_data = array();
			parse_str($_POST['tracking_data'], $wcfm_tracking_data);
			$order_id       = absint( $wcfm_tracking_data['wcfm_tracking_order_id'] );
			$product_id     = absint( $wcfm_tracking_data['wcfm_tracking_product_id'] );
			$order_item_id  = $wcfm_tracking_data['wcfm_tracking_order_item_id'];
			$tracking_url   = $wcfm_tracking_data['wcfm_tracking_url'];
			$tracking_code  = $wcfm_tracking_data['wcfm_tracking_code'];
			$order          = wc_get_order( $order_id );
      
      $tracking_url = apply_filters( 'wcfm_tracking_url', $tracking_url, $tracking_code, $order_id );
      
      if( $tracking_code && $tracking_url ) {
				if( wcfm_is_vendor() ) {
					$vendor = get_wcmp_vendor($user_id);
					$user_id = apply_filters('wcmp_mark_as_shipped_vendor', $user_id);
					$shippers = (array) get_post_meta($order_id, 'dc_pv_shipped', true);
					
					if (!in_array($user_id, $shippers)) {
						$shippers[] = $user_id;
						//$mails = WC()->mailer()->emails['WC_Email_Notify_Shipped'];
						//if (!empty($mails)) {
							//$customer_email = get_post_meta($order_id, '_billing_email', true);
							//$mails->trigger($order_id, $customer_email, $vendor->term_id, array( 'tracking_code' => $tracking_code, 'tracking_url' => $tracking_url ) );
						//}
						do_action('wcmp_vendors_vendor_ship', $order_id, $vendor->term_id);
						array_push($shippers, $user_id);
					}
						
					$wpdb->query("UPDATE {$wpdb->prefix}wcmp_vendor_orders SET shipping_status = '1' WHERE order_id = $order_id and vendor_id = $user_id and order_item_id = $order_item_id");
					$shop_name =  $WCFM->wcfm_vendor_support->wcfm_get_vendor_store_by_vendor( absint($user_id) );
					$wcfm_messages = sprintf( __( 'Vendor <b>%s</b> has shipped <b>%s</b> to customer.<br/>Tracking Code : %s <br/>Tracking URL : <a class="wcfm_dashboard_item_title" target="_blank" href="%s">%s</a>', 'wc-frontend-manager-ultimate' ), $shop_name, get_the_title( $product_id ), $tracking_code, $tracking_url, $tracking_url );
					$WCFM->wcfm_notification->wcfm_send_direct_message( $user_id, 0, 0, 1, $wcfm_messages, 'shipment_tracking' );
					$comment_id = $order->add_order_note( $wcfm_messages, '1');
					add_comment_meta( $comment_id, '_vendor_id', $user_id );
					
					update_post_meta($order_id, 'dc_pv_shipped', $shippers);
				} else {
					$comment_id = $order->add_order_note( sprintf( __( 'Product <b>%s</b> has been shipped to customer.<br/>Tracking Code : %s <br/>Tracking URL : <a href="%s">%s</a>', 'wc-frontend-manager-ultimate' ), get_the_title( $product_id ), $tracking_code, $tracking_url, $tracking_url ), '1');
				}
				
				// Update Shipping Tracking Info
				$this->updateShippingTrackingInfo( $order_id, $order_item_id, $tracking_code, $tracking_url, $product_id );
			}
			
			do_action( 'wcfm_after_order_mark_shipped', $order_id, $order_item_id, $tracking_code, $tracking_url, $product_id, $wcfm_tracking_data );
		}
		die;
	}
	
	/**
	 * Mark WCfM Marketplace order as Shipped
	 */
	function wcfm_wcfmmarketplace_order_mark_shipped() {
		global $WCFM, $WCFMu, $WCFMmp, $woocommerce, $wpdb;
		
		$user_id = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );
		
		if ( !empty( $_POST['orderid'] ) ) {
			
			$wcfm_tracking_data = array();
			parse_str($_POST['tracking_data'], $wcfm_tracking_data);
			$order_id       = absint( $wcfm_tracking_data['wcfm_tracking_order_id'] );
			$product_ids    = $wcfm_tracking_data['wcfm_tracking_product_id'];
			$product_ids    = explode( ",", $product_ids );
			$order_item_ids = $wcfm_tracking_data['wcfm_tracking_order_item_id'];
			$order_item_ids = explode( ",", $order_item_ids );
			$tracking_url   = $wcfm_tracking_data['wcfm_tracking_url'];
			$tracking_code  = $wcfm_tracking_data['wcfm_tracking_code'];
			$order          = wc_get_order( $order_id );
			
      $tracking_url = apply_filters( 'wcfm_tracking_url', $tracking_url, $tracking_code, $order_id );
      
      if( $tracking_code && $tracking_url ) {
				if( wcfm_is_vendor() ) {
					$shop_name =  $WCFM->wcfm_vendor_support->wcfm_get_vendor_store_by_vendor( absint($user_id) );
					
					$order_sync  = isset( $WCFMmp->wcfmmp_marketplace_options['order_sync'] ) ? $WCFMmp->wcfmmp_marketplace_options['order_sync'] : 'no';
					if( $order_sync != 'yes' ) {
						foreach( $order_item_ids as $order_item_id ) {
							$wpdb->query("UPDATE {$wpdb->prefix}wcfm_marketplace_orders SET commission_status = 'shipped', shipping_status = 'shipped' WHERE order_id = $order_id and vendor_id = $user_id and item_id = $order_item_id");
						}
					}
					
					if( apply_filters( 'wcfm_is_allow_itemwise_notification', true ) ) {
						foreach( $product_ids as $product_id ) {
							$wcfm_messages = apply_filters( 'wcfm_shipment_tracking_message', sprintf( __( 'Vendor <b>%s</b> has shipped <b>%s</b> to customer.<br/>Tracking Info : <a class="wcfm_dashboard_item_title" target="_blank" href="%s">%s</a>', 'wc-frontend-manager-ultimate' ), $shop_name, get_the_title( $product_id ), $tracking_url, $tracking_code ), $tracking_code, $tracking_url, $order_id, $product_id, $user_id );
							$WCFM->wcfm_notification->wcfm_send_direct_message( $user_id, 0, 0, 1, $wcfm_messages, 'shipment_tracking' );
							
							add_filter( 'woocommerce_new_order_note_data', array( $WCFM->wcfm_marketplace, 'wcfm_update_comment_vendor' ), 10, 2 );
							$comment_id = $order->add_order_note( $wcfm_messages, apply_filters( 'wcfm_is_allow_shipment_note_to_customer', 0 ) );
							add_comment_meta( $comment_id, '_vendor_id', $user_id );
							remove_filter( 'woocommerce_new_order_note_data', array( $WCFM->wcfm_marketplace, 'wcfm_update_comment_vendor' ), 10, 2 );
						}
					} else {
						$wcfm_messages = apply_filters( 'wcfm_shipment_tracking_message', sprintf( __( 'Vendor <b>%s</b> has shipped <b>%s</b> to customer.<br/>Tracking Info : <a class="wcfm_dashboard_item_title" target="_blank" href="%s">%s</a>', 'wc-frontend-manager-ultimate' ), $shop_name, esc_attr( $order->get_order_number() ), $tracking_url, $tracking_code ), $tracking_code, $tracking_url, $order_id, '', $user_id );
						$WCFM->wcfm_notification->wcfm_send_direct_message( $user_id, 0, 0, 1, $wcfm_messages, 'shipment_tracking' );
						
						add_filter( 'woocommerce_new_order_note_data', array( $WCFM->wcfm_marketplace, 'wcfm_update_comment_vendor' ), 10, 2 );
						$comment_id = $order->add_order_note( $wcfm_messages, apply_filters( 'wcfm_is_allow_shipment_note_to_customer', 0 ) );
						add_comment_meta( $comment_id, '_vendor_id', $user_id );
						remove_filter( 'woocommerce_new_order_note_data', array( $WCFM->wcfm_marketplace, 'wcfm_update_comment_vendor' ), 10, 2 );
					}
				} else {
					foreach( $product_ids as $product_id ) {
						$comment_id = $order->add_order_note( apply_filters( 'wcfm_shipment_tracking_order_note', sprintf( __( 'Product <b>%s</b> has been shipped to customer.<br/>Tracking Info : <a href="%s">%s</a>', 'wc-frontend-manager-ultimate' ), get_the_title( $product_id ), $tracking_url, $tracking_code ), $tracking_code, $tracking_url, $order_id, $product_id, $user_id ), apply_filters( 'wcfm_is_allow_shipment_note_to_customer', '1' ) );
					}
				}
			}
			
			// Update Shipping Tracking Info
			foreach( $order_item_ids as $index => $order_item_id ) {
				$this->updateShippingTrackingInfo( $order_id, $order_item_id, $tracking_code, $tracking_url, $product_ids[$index] );
			
				if( !apply_filters( 'wcfm_is_allow_itemwise_notification', true ) )
					break;
				
				do_action( 'wcfm_after_order_mark_shipped', $order_id, $order_item_id, $tracking_code, $tracking_url, $product_ids[$index], $wcfm_tracking_data );
			}
			
			if( !apply_filters( 'wcfm_is_allow_itemwise_notification', true ) ) {
				delete_post_meta( $order_id, '_wcfm_order_delivery_assigned_'.$user_id );
				foreach( $order_item_ids as $index => $order_item_id ) {
					// Keep Tracking Code as Order Item Meta
					wc_update_order_item_meta( $order_item_id, 'wcfm_tracking_code', $tracking_code );
					
					// Keep Tracking URL as Order Item Meta
					wc_update_order_item_meta( $order_item_id, 'wcfm_tracking_url', $tracking_url );
					
					do_action( 'wcfm_after_order_mark_shipped', $order_id, $order_item_id, $tracking_code, $tracking_url, $product_ids[$index], $wcfm_tracking_data );
				}
			}
			
			// Change Main Order Status if All Item Shipped
			if( apply_filters( 'wcfm_is_allow_change_main_order_status_on_all_item_shipped', false ) ) {
				$traking_added = false;
				$products = $order->get_items();
				foreach( $products as $product => $item ) {                  
					$traking_added = false;
					foreach ( $item->get_formatted_meta_data() as $meta_id => $meta ) {
						if( $meta->key == 'wcfm_tracking_url' ) {
							$traking_added = true;
						}
					}
					if( !$traking_added ) break;
				}
				
				if( $traking_added ) {
					$order->update_status( apply_filters( 'wcfm_main_order_status_on_all_item_shipped', 'completed' ), '', true );
					do_action( 'woocommerce_order_edit_status', $order_id, apply_filters( 'wcfm_main_order_status_on_all_item_shipped', 'completed' ) );
				}
			}
		}
		die;
	}
	
	/**
	 * Mark Dokan order as Shipped
	 */
	function wcfm_dokan_order_mark_shipped() {
		global $WCFM, $WCFMu, $woocommerce, $wpdb;
		
		$user_id = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );
		
		if ( !empty( $_POST['orderid'] ) ) {
			$wcfm_tracking_data = array();
			parse_str($_POST['tracking_data'], $wcfm_tracking_data);
			$order_id       = absint( $wcfm_tracking_data['wcfm_tracking_order_id'] );
			$product_id     = absint( $wcfm_tracking_data['wcfm_tracking_product_id'] );
			$order_item_id  = $wcfm_tracking_data['wcfm_tracking_order_item_id'];
			$tracking_url   = $wcfm_tracking_data['wcfm_tracking_url'];
			$tracking_code  = $wcfm_tracking_data['wcfm_tracking_code'];
			$order          = wc_get_order( $order_id );
      
      $tracking_url = apply_filters( 'wcfm_tracking_url', $tracking_url, $tracking_code, $order_id );
      
      if( $tracking_code && $tracking_url ) {
				if( wcfm_is_vendor() ) {
					$shop_name = $WCFM->wcfm_vendor_support->wcfm_get_vendor_store_by_vendor( absint($user_id) );
					$wcfm_messages = sprintf( __( 'Vendor <b>%s</b> has shipped <b>%s</b> to customer.<br/>Tracking Code : %s <br/>Tracking URL : <a class="wcfm_dashboard_item_title" target="_blank" href="%s">%s</a>', 'wc-frontend-manager-ultimate' ), $shop_name, get_the_title( $product_id ), $tracking_code, $tracking_url, $tracking_url );
					$WCFM->wcfm_notification->wcfm_send_direct_message( $user_id, 0, 0, 1, $wcfm_messages, 'shipment_tracking' );
					$comment_id = $order->add_order_note( $wcfm_messages, '1');
				} else {
					$comment_id = $order->add_order_note( sprintf( __( 'Product <b>%s</b> has been shipped to customer.<br/>Tracking Code : %s <br/>Tracking URL : <a href="%s">%s</a>', 'wc-frontend-manager-ultimate' ), get_the_title( $product_id ), $tracking_code, $tracking_url, $tracking_url ), '1');
				}
				
				// Update Shipping Tracking Info
				$this->updateShippingTrackingInfo( $order_id, $order_item_id, $tracking_code, $tracking_url, $product_id );
			}
			
			do_action( 'wcfm_after_order_mark_shipped', $order_id, $order_item_id, $tracking_code, $tracking_url, $product_id, $wcfm_tracking_data );
		}
		die;
	}
	
	function updateShippingTrackingInfo( $order_id, $order_item_id, $tracking_code, $tracking_url, $product_id ) {
		global $WCFM, $WCFMu, $woocommerce, $wpdb;
		
		if( !$tracking_code || !$tracking_url ) return;
		
		$order = wc_get_order( $order_id );
		
		// Keep Tracking Code as Order Item Meta
		wc_update_order_item_meta( $order_item_id, 'wcfm_tracking_code', $tracking_code );
		
		// Keep Tracking URL as Order Item Meta
		wc_update_order_item_meta( $order_item_id, 'wcfm_tracking_url', $tracking_url );
		
		// Shipment Tracking Notification to Customer
		if( apply_filters( 'wcfm_is_allow_shipment_tracking_customer_email', true ) ) {
			if( !defined( 'DOING_WCFM_EMAIL' ) ) 
				define( 'DOING_WCFM_EMAIL', true );
			
			if( apply_filters( 'wcfm_is_allow_itemwise_notification', true ) ) {
				$shipment_message = apply_filters( 'wcfm_shipment_tracking_email_content', sprintf( __( 'Product <b>%s</b> has been shipped to you.<br/>Tracking Code : %s <br/>Tracking URL : <a target="_blank" href="%s">%s</a>', 'wc-frontend-manager-ultimate' ), get_the_title( $product_id ), $tracking_code, $tracking_url, $tracking_url ), $tracking_code, $tracking_url, $order_id, $product_id );
				$notificaton_mail_subject = "[{site_name}] " . __( "Shipment Tracking Update", "wc-frontend-manager-ultimate" ) . " - {product_title}";
				$notification_mail_body =  '<br/>' . __( 'Hi', 'wc-frontend-manager-ultimate' ) . ' {customer_name}' .
																	 ',<br/><br/>' . 
																	 __( 'Product Shipment update:', 'wc-frontend-manager-ultimate' ) .
																	 '<br/><br/>' .
																	 '{shipment_message}' .
																	 '<br/><br/>' .
																	 sprintf( __( 'Track your package %shere%s.', 'wc-frontend-manager-ultimate' ), '<a href="{tracking_url}">', '</a>' ) .
																	 '<br /><br/>' . __( 'Thank You', 'wc-frontend-manager' ) .
																	 '<br/><br/>';
			} else {
				$shipment_message = apply_filters( 'wcfm_shipment_tracking_email_content', sprintf( __( 'Order <b>%s</b> has been shipped to you.<br/>Tracking Code : %s <br/>Tracking URL : <a target="_blank" href="%s">%s</a>', 'wc-frontend-manager-ultimate' ), esc_attr( $order->get_order_number() ), $tracking_code, $tracking_url, $tracking_url ), $tracking_code, $tracking_url, $order_id, $product_id );
				$notificaton_mail_subject = "[{site_name}] " . __( "Shipment Tracking Update", "wc-frontend-manager-ultimate" ) . " - " . $order_id;
				$notification_mail_body =  '<br/>' . __( 'Hi', 'wc-frontend-manager-ultimate' ) . ' {customer_name}' .
																	 ',<br/><br/>' . 
																	 __( 'Order Shipment update:', 'wc-frontend-manager-ultimate' ) .
																	 '<br/><br/>' .
																	 '{shipment_message}' .
																	 '<br/><br/>' .
																	 sprintf( __( 'Track your package %shere%s.', 'wc-frontend-manager-ultimate' ), '<a href="{tracking_url}">', '</a>' ) .
																	 '<br /><br/>' . __( 'Thank You', 'wc-frontend-manager' ) .
																	 '<br/><br/>';
			}
													 
			$subject = str_replace( '{site_name}', get_bloginfo( 'name' ), $notificaton_mail_subject );
			$subject = apply_filters( 'wcfm_email_subject_wrapper', $subject );
			$subject = str_replace( '{product_title}', get_the_title( $product_id ), $subject );
			$message = str_replace( '{shipment_message}', $shipment_message, $notification_mail_body );
			$message = str_replace( '{tracking_url}', $tracking_url, $message );
			$message = str_replace( '{customer_name}', get_post_meta( $order_id, '_billing_first_name', true ), $message );
			$message = apply_filters( 'wcfm_email_content_wrapper', $message, __( "Shipment Tracking Update", "wc-frontend-manager-ultimate" ) );
			
			$customer_email = get_post_meta( $order_id, '_billing_email', true );
			if( $customer_email ) {
				wp_mail( $customer_email, $subject, $message );
			}
		}
		
		return;
	}
	
	/**
	 * Mark Order item as Received
	 */
	function wcfm_mark_as_recived() {
		global $WCFM, $WCFMu, $woocommerce, $wpdb;
		
		if ( !empty( $_POST['orderitemid'] ) ) {
      $order_id = $_POST['orderid'];                   
			$order = wc_get_order( $order_id );
			$product_id = $_POST['productid'];
      $order_item_id = $_POST['orderitemid'];
      
      //$comment_id = $order->add_order_note( sprintf( __( 'Item(s) <b>%s</b> received by customer.', 'wc-frontend-manager-ultimate' ), get_the_title( $product_id ) ), '1');
      
      // Keep Tracking URL as Order Item Meta
			$sql = "INSERT INTO {$wpdb->prefix}woocommerce_order_itemmeta";
			$sql .= ' ( `meta_key`, `meta_value`, `order_item_id` )';
			$sql .= ' VALUES ( %s, %s, %s )';
			
			$confirm_message = __( 'YES', 'wc-frontend-manager-ultimate' );
	
			$wpdb->get_var( $wpdb->prepare( $sql, 'wcfm_mark_as_recived', $confirm_message, $order_item_id  ) );
			
			$vendor_id = wcfm_get_vendor_id_by_post( $product_id );
			
			// WCfM Marketplace Table Update
			if( $vendor_id  && (wcfm_is_marketplace() == 'wcfmmarketplace') ) {
				$wpdb->query("UPDATE {$wpdb->prefix}wcfm_marketplace_orders SET shipping_status = 'completed' WHERE order_id = $order_id and vendor_id = $vendor_id and item_id = $order_item_id");
			}
			
			// Notification
			$wcfm_messages = sprintf( __( 'Customer marked <b>%s</b> received for order <b>#%s</b>.', 'wc-frontend-manager-ultimate' ), get_the_title( $product_id ), '<a target="_blank" class="wcfm_dashboard_item_title" href="'.get_wcfm_view_order_url($order->get_id()).'">'. $order->get_order_number() .'</a>' );
			$WCFM->wcfm_notification->wcfm_send_direct_message( -1, 0, 0, 1, $wcfm_messages, 'shipment_received' );
			
			// Vendor Notification
			if( $vendor_id ) {
				$WCFM->wcfm_notification->wcfm_send_direct_message( -2, $vendor_id, 0, 1, $wcfm_messages, 'shipment_received' );
			}
			
			// WC Order Note
			$comment_id = $order->add_order_note( $wcfm_messages, apply_filters( 'wcfm_is_allow_shipment_mark_received_note_to_customer', 1 ) );
			
			// Order mark completed on Item Received
			if( apply_filters( 'wcfm_is_allow_order_complete_on_receive', false ) ) {
				if( $vendor_id  && (wcfm_is_marketplace() == 'wcfmmarketplace') ) {
					$wcfmmp_marketplace_options   = get_option( 'wcfm_marketplace_options', array() );
					$order_sync  = isset( $wcfmmp_marketplace_options['order_sync'] ) ? $wcfmmp_marketplace_options['order_sync'] : 'no';
					if( $order_sync == 'yes' ) {
						$order->update_status( 'completed', '', true );
						
						do_action( 'woocommerce_order_edit_status', $order_id, 'completed' );
						do_action( 'wcfm_order_status_updated', $order_id, 'completed' );
					} else {
						$wpdb->update("{$wpdb->prefix}wcfm_marketplace_orders", array('commission_status' => 'completed'), array('order_id' => $order_id, 'vendor_id' => $vendor_id), array('%s'), array('%d', '%d') );
						
						// Withdrawal Threshold check by Order Completed date 
						if( apply_filters( 'wcfm_is_allow_withdrwal_check_by_order_complete_date', false ) ) {
							$wpdb->update( "{$wpdb->prefix}wcfm_marketplace_orders", array( 'created' => date( 'Y-m-d H:i:s', current_time( 'timestamp', 0 ) ) ), array( 'order_id' => $order_id, 'vendor_id' => $vendor_id ), array('%s'), array('%d', '%d') );
						}
						
						do_action( 'wcfmmp_vendor_order_status_updated', $order_id, 'completed', $vendor_id );
					}
				} else {
					$order->update_status( 'completed', '', true );
						
					do_action( 'woocommerce_order_edit_status', $order_id, 'completed' );
					do_action( 'wcfm_order_status_updated', $order_id, 'completed' );
				}
				
				$wcfm_messages = sprintf( __( '<b>%s</b> order status updated to <b>%s</b> on item receive by customer.', 'wc-frontend-manager-ultimate' ), '#<a target="_blank" class="wcfm_dashboard_item_title" href="' . get_wcfm_view_order_url($order_id) . '">' . $order->get_order_number() . '</a>', wc_get_order_status_name( 'completed' ) );
				$comment_id = $order->add_order_note( $wcfm_messages, 0 );
				
				$WCFM->wcfm_notification->wcfm_send_direct_message( -1, 0, 1, 0, $wcfm_messages, 'status-update' );
			}
			
			do_action( 'wcfm_after_order_mark_received', $order_id, $order_item_id, $product_id );
    }
    die;
	}
	
  public function wcfmu_shipping_tracking_orders_actions( $actions, $user_id, $order ) {
  	global $WCFM, $WCFMu;
  	
  	// Virtual Order Handling
  	if( !$order->needs_shipping_address() || !$order->get_formatted_shipping_address() ) return $actions;
  	
  	// Renewal Order Handaling
  	if( function_exists( 'wcs_order_contains_subscription' ) && ( ( wcs_order_contains_subscription( $order->get_id(), 'renewal' ) || wcs_order_contains_subscription( $order->get_id(), 'renewal' ) ) && !apply_filters( 'wcfm_is_allow_renew_order_shipment', true ) ) ) return $actions;
  	
  	$order_status = sanitize_title( $order->get_status() );
		if( in_array( $order_status, apply_filters( 'wcfm_shipment_disable_order_status', array( 'failed', 'cancelled', 'refunded', 'pending' ) ) ) ) return $actions;
  	
		$items = $order->get_items();
		$order_item_id = 0;
		foreach ( $items as $item_id => $item ) {
			$needs_shipping = $WCFM->frontend->is_wcfm_needs_shipping( $item->get_product() );
		}

		if ( $needs_shipping ) {
			$actions .= '<a class="wcfm-action-icon" href="' . get_wcfm_view_order_url($order->get_id()) . '#sm_order_shipment_options"><span class="wcfmfa fa-truck text_tip" data-tip="' . esc_attr__( 'Mark Shipped', 'wc-frontend-manager-ultimate' ) . '"></span></a>';
		} 
		
  	
  	return $actions;
  }
  
  public function wcfmu_dokan_shipment_tracking_orders_actions( $actions, $user_id, $order ) {
  	global $WCFM, $WCFMu;
  	
  	// Virtual Order Handling
  	if( !$order->needs_shipping_address() || !$order->get_formatted_shipping_address() ) return $actions;
  	
  	// Renewal Order Handaling
  	if( function_exists( 'wcs_order_contains_subscription' ) && ( ( wcs_order_contains_subscription( $order->get_id(), 'renewal' ) || wcs_order_contains_subscription( $order->get_id(), 'renewal' ) )  && !apply_filters( 'wcfm_is_allow_renew_order_shipment', true ) ) ) return $actions;
  	
  	$order_status = sanitize_title( $order->get_status() );
		if( in_array( $order_status, apply_filters( 'wcfm_shipment_disable_order_status', array( 'failed', 'cancelled', 'refunded', 'pending' ) ) ) ) return $actions;
  	
		$needs_shipping = true;
		
		$items = $order->get_items();
		$order_item_id = 0;
		foreach ( $items as $item_id => $item ) {
			$needs_shipping = $WCFM->frontend->is_wcfm_needs_shipping( $item->get_product() );
		}

		if ( $needs_shipping ) {
			$actions .= '<a class="wcfm-action-icon" href="' . get_wcfm_view_order_url($order->get_id()) . '#sm_order_shipment_options"><span class="wcfmfa fa-truck text_tip" data-tip="' . esc_attr__( 'Mark Shipped', 'wc-frontend-manager-ultimate' ) . '"></span></a>';
		}
  	
  	return $actions;
  }
  
  public function wcfmu_wcmarketplace_shipping_tracking_orders_actions( $actions, $user_id, $order, $the_order ) {
  	global $WCFM, $WCFMu;
  	
  	// Virtual Order Handling
  	if( !$the_order->needs_shipping_address() || !$the_order->get_formatted_shipping_address() ) return $actions;
  	
  	// Renewal Order Handaling
  	if( function_exists( 'wcs_order_contains_subscription' ) && ( ( wcs_order_contains_subscription( $the_order->get_id(), 'renewal' ) || wcs_order_contains_subscription( $the_order->get_id(), 'renewal' ) ) && !apply_filters( 'wcfm_is_allow_renew_order_shipment', true ) ) ) return $actions;
  	
		$needs_shipping = true; 
		if( !$order->product_id ) return $actions;
		
		$order_status = sanitize_title( $the_order->get_status() );
		if( in_array( $order_status, apply_filters( 'wcfm_shipment_disable_order_status', array( 'failed', 'cancelled', 'refunded', 'pending' ) ) ) ) return $actions;
		
		// See if product needs shipping 
		$shipped = $order->shipping_status;
		$product = wc_get_product( $order->product_id ); 
		$needs_shipping = $WCFM->frontend->is_wcfm_needs_shipping( $product ); 

		if ( $needs_shipping ) {
			$actions .= '<a class="wcfm_wcmarketplace_order_mark_shipped wcfm-action-icon" href="#" data-productid="' . $order->product_id . '" data-orderitemid="' . $order->order_item_id . '" data-orderid="' . $order->order_id . '"><span class="wcfmfa fa-truck text_tip" data-tip="' . esc_attr__( 'Mark Shipped', 'wc-frontend-manager-ultimate' ) . '"></span></a>';
		} 
		
  	
  	return $actions;
  }
  
  public function wcfmu_wcfmmarketplace_shipping_tracking_orders_actions( $actions, $user_id, $order, $the_order ) {
  	global $WCFM, $WCFMu;
  	
  	// Virtual Order Handling
  	if( ( !$the_order->needs_shipping_address() || !$the_order->get_formatted_shipping_address() ) && !apply_filters( 'wcfm_is_force_shipping_address', false ) ) return $actions;
  	
  	// Renewal Order Handaling
  	if( function_exists( 'wcs_order_contains_subscription' ) && ( ( wcs_order_contains_subscription( $the_order->get_id(), 'renewal' ) || wcs_order_contains_subscription( $the_order->get_id(), 'renewal' ) ) && !apply_filters( 'wcfm_is_allow_renew_order_shipment', true ) ) ) return $actions;
  	
		$needs_shipping = true; 
		if( !$order->product_id ) return $actions;
		
		$refund_statuses = explode( ",", $order->refund_statuses );
		if( in_array( 'requested', $refund_statuses ) ) return $actions;
		
		$is_refundeds = explode( ",", $order->is_refundeds );
		if( !in_array( 0, $is_refundeds ) ) return $actions;
		
		$order_status = sanitize_title( $the_order->get_status() );
		$order_status = apply_filters( 'wcfm_current_order_status', $order_status, $the_order->get_id() );
		if( in_array( $order_status, apply_filters( 'wcfm_shipment_disable_order_status', array( 'failed', 'cancelled', 'refunded', 'pending' ) ) ) ) return $actions;
		
		// See if product needs shipping 
		$shipped = $order->shipping_status;
		$product_ids = explode( ",", $order->product_id );
		foreach( $product_ids as $product_id ) {
			$product = wc_get_product( $product_id ); 
			$needs_shipping = $WCFM->frontend->is_wcfm_needs_shipping( $product ); 
			if( $needs_shipping ) break;
		}

		if ( $needs_shipping ) {
			if( $order->order_item_ids ) $order->item_id = $order->order_item_ids;
			$actions .= '<a class="wcfm_wcfmmarketplace_order_mark_shipped wcfm-action-icon" href="#" data-productid="' . $order->product_id . '" data-orderitemid="' . $order->item_id . '" data-orderid="' . $order->order_id . '"><span class="wcfmfa fa-truck text_tip" data-tip="' . esc_attr__( 'Mark Shipped', 'wc-frontend-manager-ultimate' ) . '"></span></a>';
		} 
		
  	
  	return $actions;
  }
  
  public function wcfmu_wcvendors_shipment_tracking_orders_actions( $actions, $user_id, $the_order, $product_id ) {
  	global $WCFM, $WCFMu;
  	
		$needs_shipping = true;
		$shipped = false;
		
		// Virtual Order Handling
		if( !$the_order->needs_shipping_address() || !$the_order->get_formatted_shipping_address() ) return $actions;
		
		// Renewal Order Handaling
  	if( function_exists( 'wcs_order_contains_subscription' ) && ( ( wcs_order_contains_subscription( $the_order->get_id(), 'renewal' ) || wcs_order_contains_subscription( $the_order->get_id(), 'renewal' ) ) && !apply_filters( 'wcfm_is_allow_renew_order_shipment', true ) ) ) return $actions;
		
		// See if product needs shipping 
		$product = wc_get_product( $product_id ); 
		$needs_shipping = $WCFM->frontend->is_wcfm_needs_shipping( $product );
			
		if ( $needs_shipping ) {
			$actions .= '<a class="wcfm_wcvendors_order_mark_shipped wcfm-action-icon" href="#" data-productid="' . $product_id . '" data-orderitemid="' . $order_item_id . '" data-orderid="' . $the_order->get_id() . '"><span class="wcfmfa fa-truck text_tip" data-tip="' . esc_attr__( 'Mark Shipped', 'wc-frontend-manager-ultimate' ) . '"></span></a>';
		} 
		
  	
  	return $actions;
  }
  
  public function wcfmu_wcpvendors_shipment_tracking_orders_actions( $actions, $user_id, $the_order, $order ) {
  	global $WCFM, $WCFMu, $wpdb;
  	
  	$vendor_id   = $this->vendor_id;
		$valid_items = array();
		
		// Virtual Order Handling
		if( !$the_order->needs_shipping_address() || !$the_order->get_formatted_shipping_address() ) return $actions;
		
		// Renewal Order Handaling
  	if( function_exists( 'wcs_order_contains_subscription' ) && ( ( wcs_order_contains_subscription( $the_order->get_id(), 'renewal' ) || wcs_order_contains_subscription( $the_order->get_id(), 'renewal' ) ) && !apply_filters( 'wcfm_is_allow_renew_order_shipment', true ) ) ) return $actions;
  	
		// See if product needs shipping 
		$needs_shipping = true; 

		$status = WC_Product_Vendors_Utils::get_fulfillment_status( $order->order_item_id );
		$product = wc_get_product( $order->product_id );
		$needs_shipping = $WCFM->frontend->is_wcfm_needs_shipping( $product );
  	
		if ( $needs_shipping ) {
			$actions .= '<a class="wcfm_wcpvendors_order_mark_fulfilled wcfm-action-icon" href="#" data-productid="' . $order->product_id . '" data-orderid="' . $order->order_id . '" data-orderitemid="' . $order->order_item_id . '"><span class="wcfmfa fa-truck text_tip" data-tip="' . esc_attr__( 'Mark Fulfilled', 'wc-frontend-manager-ultimate' ) . '"></span></a>';
		} 
		
  	
  	return $actions;
  }
  
  // Order item meta Tracking URL label
	function wcfm_tracking_url_display_label( $display_key ) {
		global $WCFM, $WCFMu;
		
		if( $display_key == 'wcfm_tracking_code' ) {
			$display_key = __( 'Tracking Code', 'wc-frontend-manager-ultimate' );
		}
		
		if( $display_key == 'wcfm_tracking_url' ) {
			$display_key = __( 'Tracking URL', 'wc-frontend-manager-ultimate' );
		}
		
		if( $display_key == 'wcfm_mark_as_recived' ) {
			$display_key = __( 'Item(s) Received', 'wc-frontend-manager-ultimate' );
		}
		
		return $display_key;
	}
	
	// Order Tracking reponse at View Order by Customer
	function wcfm_order_tracking_response( $item_id, $item, $order ) {
		global $WCFM, $WCFMu;
		
		if( !function_exists( 'wc_get_endpoint_url' ) || !wc_get_page_id( 'myaccount' ) || !is_page( wc_get_page_id( 'myaccount' ) ) ) return;
		
		if( ( !$order->needs_shipping_address() || !$order->get_formatted_shipping_address() ) && !apply_filters( 'wcfm_is_force_shipping_address', false ) ) return;
		
		$order_status = sanitize_title( $order->get_status() );
		if( in_array( $order_status, apply_filters( 'wcfm_shipment_disable_order_status', array( 'failed', 'cancelled', 'refunded', 'pending' ) ) ) ) return;
		
		// See if product needs shipping 
		$product = $item->get_product(); 
		$needs_shipping = $WCFM->frontend->is_wcfm_needs_shipping( $product ); 
		
		if( $WCFMu->is_marketplace ) {
			if( $WCFMu->is_marketplace == 'wcvendors' ) {
				if( version_compare( WCV_VERSION, '2.0.0', '<' ) ) {
					if( !WC_Vendors::$pv_options->get_option( 'give_shipping' ) ) $needs_shipping = false;
				} else {
					if( !get_option('wcvendors_vendor_give_shipping') ) $needs_shipping = false;
				}
			} elseif( $WCFMu->is_marketplace == 'wcmarketplace' ) {
				global $WCMp;
				if( !$WCMp->vendor_caps->vendor_payment_settings('give_shipping') ) $needs_shipping = false;
			}
		}
		
		if( $needs_shipping ) {
			$traking_added = false;
			$package_received = false;
			foreach ( $item->get_formatted_meta_data() as $meta_id => $meta ) {
				if( $meta->key == 'wcfm_tracking_url' ) {
					$traking_added = true;
				}
				if( $meta->key == 'wcfm_mark_as_recived' ) {
					$package_received = true;
				}
			}
			echo "<p>";
			printf( __( 'Shipment Tracking: ', 'wc-frontend-manager-ultimate' ) );
			if( $package_received ) {
				printf( __( 'Item(s) already received.', 'wc-frontend-manager-ultimate' ) );
			} elseif( $traking_added ) {
				if( apply_filters( 'wcfm_is_allow_mark_as_recived', true ) ) {
					?>
					<a href="#" class="wcfm_mark_as_recived" data-orderitemid="<?php echo $item_id; ?>" data-orderid="<?php echo $order->get_id(); ?>" data-productid="<?php echo $item->get_product_id(); ?>"><?php printf( __( 'Mark as Received', 'wc-frontend-manager-ultimate' ) ); ?></a>
					<?php
				}
			} else {
				printf( __( 'Item(s) will be shipped soon.', 'wc-frontend-manager-ultimate' ) );
			}
			echo "</p>";
		}
	}
	
	function wcfm_shipment_tracking_message_types( $message_types ) {
		$message_types['shipment_tracking'] = __( 'Shipment Tracking', 'wc-frontend-manager-ultimate' );
		$message_types['shipment_received'] = __( 'Shipment Received', 'wc-frontend-manager-ultimate' );
		return $message_types;
	}
}