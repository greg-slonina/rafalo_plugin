<?php
/**
 * WCFM plugin controllers
 *
 * Plugin Orders Edit Controller
 *
 * @author 		WC Lovers
 * @package 	wcfmu/controllers/orders
 * @version   5.2.1
 */

class WCFMu_Orders_Edit_Controller {
	
	public function __construct() {
		global $WCFM, $WCFMu;
		
		$this->processing();
	}
	
	public function processing() {
		global $WCFM, $WCFMu, $wpdb, $WCFMmp, $wcfm_orders_edit_form_data;
		
		$wcfm_orders_edit_form_data = array();
	  parse_str($_POST['wcfm_orders_edit_form'], $wcfm_orders_edit_form_data);
	  
	  $wcfm_orders_manage_messages = get_wcfm_orders_manage_messages();
	  $has_error = false;
	  
	  if(isset($wcfm_orders_edit_form_data['wcfm_order_edit_id']) && !empty($wcfm_orders_edit_form_data['wcfm_order_edit_id'])) {
	  	
	  	$new_order_id = absint( $wcfm_orders_edit_form_data['wcfm_order_edit_id'] );
	  	$order_id     = $new_order_id;
	  	$order        = wc_get_order( $new_order_id );
	  	
	  	$current_user_id = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );
	  	if( wcfm_is_vendor() ) {
				$vendor_id = $current_user_id;
			}
			
			define( 'WCFM_MANUAL_ORDER', TRUE );
			
			do_action( 'before_wcfm_orders_edit', $order->get_id(), $order, $wcfm_orders_edit_form_data );
	  	
	  	try {
				
	  		$order_items = $wcfm_orders_edit_form_data['wcfm_order_edit_input'];
	  		
	  		$products = array();
	  		
	  		foreach( $order_items as $order_edit_input_id => $order_edit_input ) {
	  			
	  			$order_item_id = absint( $order_edit_input['item'] );
	  		
	  			if( !$order_item_id ) continue;
	  			
	  			$line_item  = new WC_Order_Item_Product( $order_item_id );
	  			
	  			// Reset Item Processed BIt
	  			$order_item_processed = wc_get_order_item_meta( $order_item_id, '_wcfmmp_order_item_processed', true );
					if( $order_item_processed ) {
						wc_delete_order_item_meta( $order_item_id, '_wcfmmp_order_item_processed' );
					}
	  			
	  			$product    = $line_item->get_product();
	  			$products[] = $line_item->get_product_id();
	  			
	  			//$prev_total     = (float) $line_item->get_total();
	  			//$prev_sub_total = (float) $line_item->get_subtotal();
	  			//$discount       = ( $prev_sub_total - $prev_total );
	  			
	  			$quantity       = $order_edit_input['qty'];
	  			
	  			$line_item->set_quantity( $quantity );
	  			$total = wc_get_price_excluding_tax( $product, array( 'qty' => $quantity ) );
	  			$line_item->set_subtotal( $total );
	  			
	  			$edited_total   = $order_edit_input['total'];
	  			
	  			$line_item->set_total( $edited_total );
	  			$line_item->save();
	  		}
				
				// calculate totals and set them
				$order->calculate_totals();
				
				// Create & Apply Discount
				if( wc_coupons_enabled() && apply_filters( 'wcfm_orders_manage_discount', true ) && isset( $wcfm_orders_edit_form_data['wcfm_om_discount'] ) && !empty( $wcfm_orders_edit_form_data['wcfm_om_discount'] ) ) {
					
					// Creating Coupon for New Order
					$coupon_title = __( 'Discount', 'wc-frontend-manager-ultimate' ) . ' #' . $order->get_id() . ' (' . date_i18n( wc_date_format() . ' ' . wc_time_format(), current_time( 'timestamp' ) ) . ')';
					$new_coupon = array(
															'post_title'   => wc_clean( $coupon_title ),
															'post_status'  => 'publish',
															'post_type'    => 'shop_coupon',
															'post_excerpt' => $coupon_title,
															'post_author'  => $current_user_id,
															'post_name'    => sanitize_title($coupon_title)
														);
					
					$new_coupon_id = wp_insert_post( $new_coupon, true );
					
					if(!is_wp_error($new_coupon_id)) {
						$coupon_code  = wc_format_coupon_code( wc_clean( $coupon_title ) );
						
						$wc_coupon = new WC_Coupon( $new_coupon_id );
						$wc_coupon->set_props( array(
																					'code'                        => $coupon_code,
																					'discount_type'               => wc_clean( 'fixed_product' ),
																					'amount'                      => wc_format_decimal( wc_format_decimal( $wcfm_orders_edit_form_data['wcfm_om_discount'] ) / count($products) ),
																					//'date_expires'                => strtotime( 'midnight', current_time( 'timestamp' ) ),
																					'free_shipping'               => '',
																					'individual_use'              => 'yes',
																					'product_ids'                 => $products,
																					'usage_limit'                 => 1,
																					'usage_limit_per_user'        => 1
																				) );
						
						$wc_coupon->save();
						update_post_meta( $new_coupon_id, 'show_on_store', 'no' );
						
						// Applying coupon on Order
						try {
							$result = $order->apply_coupon( $wc_coupon );
							if ( is_wp_error( $result ) ) {
								wcfm_log( $result->get_error_message() );
							}
						} catch ( Exception $e ) {
							wcfm_log( $e->getMessage() );
						}
					}
				}
				
				$order->save();
				
			} catch ( Exception $e ) {
				$has_error = true;
				wcfm_log( $e->getMessage() );
			}
			
			do_action( 'after_wcfm_orders_edit', $new_order_id, $order, $wcfm_orders_edit_form_data );
			
			// Reset WCFMmp Comission Orders
			delete_post_meta( $order_id, '_wcfmmp_order_processed' );
			delete_post_meta( $order_id, '_wcfm_store_invoices' );
			delete_post_meta( $order_id, '_wcfm_store_invoice_ids' );
			do_action( 'wcfm_manual_order_reset', $order_id, true );
			//$WCFMmp->wcfmmp_commission->wcfmmp_commission_order_reset( $order_id );
			
			// Order Item Meta Reset - Order rest already performing this
			/*$line_items = $order->get_items( 'line_item' );
			if( !empty( $line_items ) ) {
				foreach( $line_items as $order_item_id => $item ) {
					wc_delete_order_item_meta( $order_item_id, '_wcfmmp_order_item_processed' );
				}
			}*/
			
			$order_posted = get_post( $order->get_id() );
			do_action( 'wcfm_manual_order_processed', $order_id, $order_posted, $order );
			
			// Add Order Note
			if( wcfm_is_vendor() ) {
				$wcfm_messages = sprintf( __( '<b>%s</b> order updated by <b>%s</b>.', 'wc-frontend-manager-ultimate' ), '#<a target="_blank" class="wcfm_dashboard_item_title" href="' . get_wcfm_view_order_url( $order_id ) . '">' . $order_id . '</a>', $WCFM->wcfm_vendor_support->wcfm_get_vendor_store_by_vendor( $vendor_id ) );
				add_filter( 'woocommerce_new_order_note_data', array( $WCFM->wcfm_marketplace, 'wcfm_update_comment_vendor' ), 10, 2 );
				$order->add_order_note( $wcfm_messages, 0 );
				remove_filter( 'woocommerce_new_order_note_data', array( $WCFM->wcfm_marketplace, 'wcfm_update_comment_vendor' ), 10, 2 );
			}
			
			// Set Customer Note
			if( isset( $wcfm_orders_edit_form_data['wcfm_om_comments'] ) && !empty( $wcfm_orders_edit_form_data['wcfm_om_comments'] ) ) {
				$order->add_order_note( $wcfm_orders_edit_form_data['wcfm_om_comments'], true, true );
			}
			
			// Send the customer invoice email.
			if( apply_filters( 'wcfm_is_allow_order_update_resend_invoice_email', true ) ) {
				do_action( 'woocommerce_before_resend_order_emails', $order, 'customer_invoice' );
	
				WC()->payment_gateways();
				WC()->shipping();
				WC()->mailer()->customer_invoice( $order );
	
				do_action( 'woocommerce_after_resend_order_email', $order, 'customer_invoice' );
			}
			
	  	if(!$has_error) { echo '{"status": true, "message": "' . $wcfm_orders_manage_messages['order_updated'] . '", "redirect": "' . get_wcfm_view_order_url( $new_order_id ) . '"}'; }
	  	else { echo '{"status": false, "message": "' . $wcfm_orders_manage_messages['order_failed'] . '"}'; }
	  	
	  } else {
	  	echo '{"status": false, "message": "' . $wcfm_orders_manage_messages['no_order'] . '"}';
	  }
	  
	  die;
	}
}