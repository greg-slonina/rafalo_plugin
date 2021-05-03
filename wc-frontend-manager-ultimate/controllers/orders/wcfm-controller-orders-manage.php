<?php
/**
 * WCFM plugin controllers
 *
 * Plugin Orders Manage Controller
 *
 * @author 		WC Lovers
 * @package 	wcfmu/controllers/orders
 * @version   5.2.0
 */

class WCFMu_Orders_Manage_Controller {
	
	public function __construct() {
		global $WCFM, $WCFMu;
		
		$this->processing();
	}
	
	public function processing() {
		global $WCFM, $WCFMu, $wpdb, $wcfm_orders_manage_form_data;
		
		$wcfm_orders_manage_form_data = array();
	  parse_str($_POST['wcfm_orders_manage_form'], $wcfm_orders_manage_form_data);
	  
	  $wcfm_orders_manage_messages = get_wcfm_orders_manage_messages();
	  $has_error = false;
	  
	  if(isset($wcfm_orders_manage_form_data['associate_products']) && !empty($wcfm_orders_manage_form_data['associate_products'])) {
	  	
	  	$new_order_id = '';
	  	
	  	$customer_id           = absint( $wcfm_orders_manage_form_data['customer_id'] );
	  	$associate_products    = $wcfm_orders_manage_form_data['associate_products'];
	  	$product_ids           = array(); 
	  	
	  	$current_user_id = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );
	  	if( wcfm_is_vendor() ) {
				$vendor_id = $current_user_id;
			}
			
			define( 'WCFM_MANUAL_ORDER', TRUE );
	  	
	  	try {
				$order = new WC_Order();
				$order->save();
				
				$order->set_customer_id( $customer_id );
				$order->set_status( 'pending' );
				
				$order->set_currency( get_woocommerce_currency() );
				$order->set_prices_include_tax( 'yes' === get_option( 'woocommerce_prices_include_tax' ) );
				$order->set_customer_ip_address( WC_Geolocation::get_ip_address() );
				$order->set_customer_user_agent( wc_get_user_agent() );
				
				// Set Product Line Item
				foreach( $associate_products as $associate_product ) {
					$product_id    = absint( $associate_product['product'] );
					
					if( $product_id ) {
						$product_ids[] = $product_id;
						
						$variation_id  = isset( $associate_product['variation'] ) ? absint( $associate_product['variation'] ) : '';
						$quantity      = $associate_product['quantity'];
						$quantity      = $quantity ? $quantity : 1;
						
						$product = wc_get_product( $variation_id ? $variation_id : $product_id );
						$line_item = new WC_Order_Item_Product();
						$line_item->set_product( $product );
						$line_item->set_order_id( $order->get_id() );
						$line_item->set_quantity( $quantity );
						$total = wc_get_price_excluding_tax( $product, array( 'qty' => $quantity ) );
						$line_item->set_total( $total );
						$line_item->set_subtotal( $total );
						
						if ( $variation_id ) {
							$line_item->set_variation_id( $variation_id );
						}
						$order->add_item( $line_item );
					}
				}
				
				if( empty( $product_ids ) ) {
					echo '{"status": false, "message": "' . $wcfm_orders_manage_messages['no_product'] . '"}';
					die;
				}
				
				// Set Shipping Method
				if( isset( $wcfm_orders_manage_form_data['wcfm_om_shipping_method'] ) && !empty( $wcfm_orders_manage_form_data['wcfm_om_shipping_method'] ) ) {
					$shipping_rate = new WC_Shipping_Rate( $wcfm_orders_manage_form_data['wcfm_om_shipping_method'], '', isset( $wcfm_orders_manage_form_data['wcfm_om_shipping_cost'] ) ? floatval( $wcfm_orders_manage_form_data['wcfm_om_shipping_cost'] ) : 0, array(), $wcfm_orders_manage_form_data['wcfm_om_shipping_method'] );
					$shipping_item = new WC_Order_Item_Shipping();
					$shipping_item->set_order_id( $order->get_id() );
					$shipping_item->set_shipping_rate( $shipping_rate );
					
					if( wcfm_is_vendor() ) {
						$shipping_item->add_meta_data( 'vendor_id', $vendor_id, true );
					}
					$shipping_item->add_meta_data( 'package_qty', $quantity, true );
					
					$order->add_item( $shipping_item );
					$order->set_shipping_total( isset( $wcfm_orders_manage_form_data['wcfm_om_shipping_cost'] ) ? floatval( $wcfm_orders_manage_form_data['wcfm_om_shipping_cost'] ) : 0 );
				}
				
				// Set Customer Note
				$order->set_customer_note( __( 'Manual Order', 'wc-frontend-manager-ultimate' ) );
				
				// Set Payment Method
				if( isset( $wcfm_orders_manage_form_data['wcfm_om_payment_method'] ) && !empty( $wcfm_orders_manage_form_data['wcfm_om_payment_method'] ) ) {
					update_post_meta( $order->get_id(), '_payment_method', $wcfm_orders_manage_form_data['wcfm_om_payment_method'] );
					$order->set_payment_method( $wcfm_orders_manage_form_data['wcfm_om_payment_method'] );
					if ( WC()->payment_gateways() ) {
						$payment_gateways = WC()->payment_gateways->payment_gateways();
					} else {
						$payment_gateways = array();
					}
					$payment_method_title = ( isset( $payment_gateways[ $wcfm_orders_manage_form_data['wcfm_om_payment_method'] ] ) ? esc_html( $payment_gateways[ $wcfm_orders_manage_form_data['wcfm_om_payment_method'] ]->get_title() ) : esc_html( $payment_method ) );
					update_post_meta( $order->get_id(), '_payment_method_title', $payment_method_title );
				}
				
				if( isset( $wcfm_orders_manage_form_data['wcfm_om_payment_details'] ) && !empty( $wcfm_orders_manage_form_data['wcfm_om_payment_details'] ) ) {
					$order->payment_complete( isset( $wcfm_orders_manage_form_data['wcfm_om_payment_details'] ) ? $wcfm_orders_manage_form_data['wcfm_om_payment_details'] : '' );
				}
				
				do_action( 'before_wcfm_orders_manage_save', $order->get_id(), $order, $wcfm_orders_manage_form_data );
				
				// calculate totals and set them
				$order->calculate_totals();
				
				$new_order_id = $order->get_id();
				
				$order->set_created_via( __( 'Manual Order', 'wc-frontend-manager-ultimate' ) );
				
				// Create & Apply Discount
				if( wc_coupons_enabled() && apply_filters( 'wcfm_orders_manage_discount', true ) && isset( $wcfm_orders_manage_form_data['wcfm_om_discount'] ) && !empty( $wcfm_orders_manage_form_data['wcfm_om_discount'] ) ) {
					
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
																					'amount'                      => wc_format_decimal( $wcfm_orders_manage_form_data['wcfm_om_discount'] ),
																					//'date_expires'                => strtotime( 'midnight', current_time( 'timestamp' ) ),
																					'free_shipping'               => '',
																					'individual_use'              => 'yes',
																					'product_ids'                 => $product_ids,
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
				
				// Set Custom Billing & Shipping Address
				if( apply_filters( 'wcfm_allow_customer_billing_details', true ) ) {
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
						if ( is_callable( array( $order, "set_{$wcfm_order_default_key}" ) ) ) {
							$order->{"set_{$wcfm_order_default_key}"}( $wcfm_orders_manage_form_data[$wcfm_order_default_field] );
						}
						if ( $order->get_user_id() ) {
							update_user_meta( $order->get_user_id(), $wcfm_order_default_key, $wcfm_orders_manage_form_data[$wcfm_order_default_field] );
						}
					}
				}
				
				if( apply_filters( 'wcfm_allow_customer_shipping_details', true ) ) {
					
					if( isset( $wcfm_orders_manage_form_data['sadd_as_billing'] ) ) {
						$wcfm_order_shipping_fields = array( 
																					'shipping_first_name'  => 'bfirst_name',
																					'shipping_last_name'   => 'blast_name',
																					'shipping_address_1'   => 'baddr_1',
																					'shipping_address_2'   => 'baddr_2',
																					'shipping_country'     => 'bcountry',
																					'shipping_city'        => 'bcity',
																					'shipping_state'       => 'bstate',
																					'shipping_postcode'    => 'bzip'
																				);
					} else {
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
					}
					
					foreach( $wcfm_order_shipping_fields as $wcfm_order_default_key => $wcfm_order_default_field ) {
						if ( is_callable( array( $order, "set_{$wcfm_order_default_key}" ) ) ) {
							$order->{"set_{$wcfm_order_default_key}"}( $wcfm_orders_manage_form_data[$wcfm_order_default_field] );
						}
						if ( $order->get_user_id() ) {
							update_user_meta( $order->get_user_id(), $wcfm_order_default_key, $wcfm_orders_manage_form_data[$wcfm_order_default_field] );
						}
					}
				}
				
				// Add Order Note
				if( wcfm_is_vendor() ) {
					$wcfm_messages = sprintf( __( '<b>%s</b> order added by <b>%s</b> for customer <b>%s</b>.', 'wc-frontend-manager-ultimate' ), '#<a target="_blank" class="wcfm_dashboard_item_title" href="' . get_wcfm_view_order_url( $new_order_id ) . '">' . $new_order_id . '</a>', $WCFM->wcfm_vendor_support->wcfm_get_vendor_store_by_vendor( $vendor_id ), ( $customer_id ) ? '<a target="_blank" class="wcfm_dashboard_item_title" href="' . get_wcfm_customers_details_url( $customer_id ) . '">' . get_userdata($customer_id)->display_name . '</a>' : '' );
					add_filter( 'woocommerce_new_order_note_data', array( $WCFM->wcfm_marketplace, 'wcfm_update_comment_vendor' ), 10, 2 );
					$order->add_order_note( $wcfm_messages, 0 );
					remove_filter( 'woocommerce_new_order_note_data', array( $WCFM->wcfm_marketplace, 'wcfm_update_comment_vendor' ), 10, 2 );
				}
				
				// Set Customer Note
				if( isset( $wcfm_orders_manage_form_data['wcfm_om_comments'] ) && !empty( $wcfm_orders_manage_form_data['wcfm_om_comments'] ) ) {
					$order->add_order_note( $wcfm_orders_manage_form_data['wcfm_om_comments'], true, true );
				}
				
				$order->save();
				
				wc_delete_shop_order_transients( $order );
				
				// Order Real Author
				update_post_meta( $order->get_id(), '_wcfm_order_author', get_current_user_id() );
				
				do_action( 'woocommerce_new_order', $order->get_id() );
				
				// Send the customer invoice email.
				do_action( 'woocommerce_before_resend_order_emails', $order, 'customer_invoice' );

				WC()->payment_gateways();
				WC()->shipping();
				WC()->mailer()->customer_invoice( $order );

				// Note the event.
				$order->add_order_note( __( 'Order details manually sent to customer.', 'woocommerce' ), false, true );

				do_action( 'woocommerce_after_resend_order_email', $order, 'customer_invoice' );
				
				// Send Admin new order email.
				do_action( 'woocommerce_before_resend_order_emails', $order, 'new_order' );

				WC()->payment_gateways();
				WC()->shipping();
				WC()->mailer()->emails['WC_Email_New_Order']->trigger( $order->get_id(), $order );

				do_action( 'woocommerce_after_resend_order_email', $order, 'new_order' );
				
			} catch ( Exception $e ) {
				$has_error = true;
				wcfm_log( $e->getMessage() );
			}
			
			do_action( 'after_wcfm_orders_manage_save', $new_order_id, $order, $wcfm_orders_manage_form_data );
			
			$order_posted = get_post( $order->get_id() );
			do_action( 'wcfm_manual_order_processed', $new_order_id, $order_posted, $order );
			
			do_action( 'wcfm_manual_orders_manage_complete', $new_order_id );
	  	
	  	if(!$has_error) { echo '{"status": true, "message": "' . $wcfm_orders_manage_messages['order_published'] . '", "redirect": "' . get_wcfm_view_order_url( $new_order_id ) . '"}'; }
	  	else { echo '{"status": false, "message": "' . $wcfm_orders_manage_messages['order_failed'] . '"}'; }
	  	
	  } else {
	  	echo '{"status": false, "message": "' . $wcfm_orders_manage_messages['no_product'] . '"}';
	  }
	  
	  die;
	}
}