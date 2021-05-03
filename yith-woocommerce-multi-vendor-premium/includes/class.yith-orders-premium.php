<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct access forbidden.' );
}

/**
 *
 *
 * @class      YITH_Orders_Premium
 * @package    Yithemes
 * @since      Version 1.6
 * @author     Your Inspiration Themes
 *
 */
if ( ! class_exists( 'YITH_Order_Premium' ) ) {

	class YITH_Orders_Premium extends YITH_Orders {

		/**
		 * Suborder Sync Enabled
		 *
		 * @var bool
		 * @since 1.4.0
		 */
		public $suborder_sync_enabled = null;

		/**
		 * Suborder Sync Enabled
		 *
		 * @var bool
		 * @since 1.4.0
		 */
		public $refund_managemnet = null;

		/**
		 * construct
		 */
		public function __construct() {
			parent::__construct();

			//Enabled commission paid email
			add_filter( 'yith_wcmv_send_commission_paid_email', '__return_true' );

			$this->refund_managemnet = 'yes' == get_option( 'yith_wpv_vendors_option_order_refund_synchronization', 'no' );

			$this->suborder_sync_enabled = 'yes' == get_option( 'yith_wpv_vendors_option_suborder_synchronization', 'no' );
			$new_order_email_callback    = ! $this->sync_enabled ? '__return_true' : '__return_false';
			add_filter( 'yith_wcmv_force_to_trigger_new_order_email_action', $new_order_email_callback );

			if ( $this->refund_managemnet ) {
				add_action( 'woocommerce_order_refunded', array( $this, 'child_order_refunded' ), 10, 2 );
				//add_action( 'woocommerce_refund_deleted', array( $this, 'before_delete_child_refund' ), 10, 2 );
				add_action( 'before_delete_post', array( $this, 'before_delete_child_refund' ), 5, 1 );

			}

			add_action( 'woocommerce_after_order_itemmeta', array(
				$this,
				'commission_info_in_order_line_item'
			), 10, 3 );

			$woocommerce_order_actions_hook = YITH_Vendors()->is_wc_3_2_or_greather ? 'woocommerce_order_actions' : 'woocommerce_resend_order_emails_available';
			add_filter( $woocommerce_order_actions_hook, array( $this, 'resend_order_emails_available' ) );

			add_filter( 'default_hidden_meta_boxes', array( $this, 'hidden_meta_boxes' ), 10, 2 );

			if ( $this->suborder_sync_enabled ) {
				add_action( 'woocommerce_order_status_changed', array(
					$this,
					'parent_order_status_synchronization'
				), 35, 3 );
			}
			$order_actions = array(
				'new_order_to_vendor',
				'cancelled_order_to_vendor',
			);

			foreach ( $order_actions as $action ) {
				add_action( "woocommerce_order_action_{$action}", array( $this, 'woocommerce_order_action' ), 10, 1 );
			}

			//Filter the preview order data for vendors
			add_filter( 'woocommerce_admin_order_preview_get_order_details', array(
				$this,
				'order_preview_get_order_details'
			), 99, 2 );

			//Add vendor information to parent shipping method
			add_action( 'woocommerce_checkout_create_order_shipping_item', array(
				$this,
				'add_vendor_information_to_parent_shipping_item'
			), 10, 4 );
		}

		public function woocommerce_order_action( $order ) {
			// Handle button actions
			if ( ! empty( $_POST['wc_order_action'] ) && $order instanceof WC_Order ) {

				$action = wc_clean( $_POST['wc_order_action'] );

				$order_actions = array(
					'new_order_to_vendor',
					'cancelled_order_to_vendor',
				);

				if ( in_array( $action, $order_actions ) ) {

					// Switch back to the site locale.
					wc_switch_to_site_locale();

					// Ensure gateways are loaded in case they need to insert data into the emails.
					WC()->payment_gateways();
					WC()->shipping();

					// Load mailer.
					$mailer        = WC()->mailer();
					$email_to_send = $action;
					$mails         = $mailer->get_emails();

					if ( ! empty( $mails ) ) {
						foreach ( $mails as $mail ) {
							if ( $mail->id == $email_to_send ) {
								$mail->trigger( $order->get_id(), $order );
								/* translators: %s: email title */
								$order->add_order_note( sprintf( __( '%s email notification manually sent.', 'woocommerce' ), $mail->title ), false, true );
							}
						}
					}

					// Restore user locale.
					wc_restore_locale();
				}
			}
		}

		/**
		 * Hidden default Meta-Boxes.
		 *
		 * @param array $hidden
		 * @param object $screen
		 *
		 * @return array
		 */
		public function hidden_meta_boxes( $hidden, $screen ) {
			$vendor             = yith_get_vendor( 'current', 'user' );
			$is_shop_order_page = 'shop_order' === $screen->post_type && 'post' === $screen->base;
			$vendor_is_valid    = $vendor->is_valid() && $vendor->has_limited_access();
			$hide_custom_fields = 'yes' == get_option( 'yith_wpv_vendors_option_order_prevent_edit_custom_fields', 'no' );

			if ( $hide_custom_fields && $is_shop_order_page && $vendor_is_valid ) {
				$hidden = array_merge( $hidden, array( 'postcustom' ) );
			}

			return $hidden;
		}

		/**
		 * Handle a refund via the edit order screen.
		 * Called after wp_ajax_woocommerce_refund_line_items action
		 *
		 * @use woocommerce_order_refunded action
		 * @see woocommerce\includes\class-wc-ajax.php:2295
		 */
		public function order_refunded( $order_id, $parent_refund_id ) {
			remove_action( 'woocommerce_order_refunded', array( $this, 'child_order_refunded' ), 10, 2 );
			parent::order_refunded( $order_id, $parent_refund_id );
			add_action( 'woocommerce_order_refunded', array( $this, 'child_order_refunded' ), 10, 2 );
		}


		/**
		 * Handle a refund via the edit order screen.
		 * Called after wp_ajax_woocommerce_refund_line_items action
		 *
		 * @use woocommerce_order_refunded action
		 * @see woocommerce\includes\class-wc-ajax.php:2295
		 */
		public function child_order_refunded( $order_id, $child_refund_id ) {
			$parent_order_id = wp_get_post_parent_id( $order_id );
			$refund_reason   = sanitize_text_field( $_POST['refund_reason'] );
			remove_action( 'woocommerce_order_refunded', array( $this, 'order_refunded' ), 10, 2 );
			remove_action( 'woocommerce_order_refunded', array( $this, 'child_order_refunded' ), 10, 2 );
			if ( $parent_order_id ) {

				$create_refund = true;
				$parent_order  = wc_get_order( $parent_order_id );
				$line_items    = array();
				$child_refund  = wc_get_order( $child_refund_id );
				$child_order   = wc_get_order( $order_id );
				/**
				 * @var WC_Order_Refund $child_refund ;
				 */

				$refund_child_items = $child_refund->get_items( array( 'line_item', 'shipping' ) );

				/**
				 * @var WC_Order_Item $refund_child_item
				 **/

				$total_refund = 0;
				foreach ( $refund_child_items as $refund_child_item_id => $refund_child_item ) {

					$item_id = $refund_child_item->get_meta( '_refunded_item_id', true );

					$parent_item_id = self::get_parent_item_id( $child_order, $item_id );

					if ( $parent_item_id && ! isset( $line_items[ $parent_item_id ] ) ) {

						$child_refund_taxes = $refund_child_item->get_taxes();
						$refund_taxes       = array();
						$total_tax          = 0;

						foreach ( $child_refund_taxes as $key => $tax ) {
							foreach ( $tax as $tax_id => $value ) {
								$refund_taxes[ $tax_id ] = abs( $value );

								if ( 'total' == $key ) {
									$total_tax += $refund_taxes[ $tax_id ];
								}
							}
						}

						$line_items[ $parent_item_id ] = array(
							'qty'          => abs( $refund_child_item->get_quantity() ),
							'refund_total' => abs( $refund_child_item->get_total() ),
							'refund_tax'   => $refund_taxes
						);

						$total_refund += abs( $refund_child_item->get_total() ) + $total_tax;
					}
				}


				if ( count( $line_items ) == 0 ) {
					$create_refund = false;
				}

				if ( $create_refund ) {
					// Create the refund object
					$refund = wc_create_refund( array(
							'amount'     => $total_refund,
							'reason'     => $refund_reason,
							'order_id'   => $parent_order_id,
							'line_items' => $line_items,
						)
					);

					if ( $refund instanceof WC_Order_Refund ) {
						$child_order = wc_get_order( $child_refund_id );
						if ( $child_order instanceof WC_Order_Refund ) {
							$child_order->add_meta_data( '_parent_refund_id', $refund->get_id(), true );
							$child_order->save_meta_data();
						}

						$refund->add_meta_data( '_child_refund_id', $child_refund_id );
						$refund->save_meta_data();
					}
				}
			}
			add_action( 'woocommerce_order_refunded', array( $this, 'order_refunded' ), 10, 2 );
			add_action( 'woocommerce_order_refunded', array( $this, 'child_order_refunded' ), 10, 2 );
		}

		/**
		 * Handle a refund via the edit order screen.
		 * Need to delete parent refund from child order
		 * Called in wp_ajax_woocommerce_delete_refund action
		 *
		 * @use before_delete_post
		 * @see post.php:2634
		 */
		public function before_delete_child_refund( $refund_id ) {

			if ( 'shop_order_refund' == get_post_type( $refund_id ) ) {

				$order_id = wp_get_post_parent_id( $refund_id );

				$order = wc_get_order( $order_id );

				//if is a child order , we are deleting a child refund
				if ( 'yith_wcmv_vendor_suborder' === $order->get_created_via() ) {

					$parent_order_id = wp_get_post_parent_id( $order_id );
					$child_refund = wc_get_order($refund_id);
					$parent_refund_id = $child_refund->get_meta('_parent_refund_id');
					$parent_refund = wc_get_order( $parent_refund_id );

					if ( $parent_order_id && $parent_refund ) {


						$delete_commission_refund = YITH_Commissions()->delete_commission_refund( $refund_id, $order_id, $parent_order_id );

						wc_delete_shop_order_transients( $parent_order_id );
						wp_delete_post( $parent_refund_id );
					}
				}
			}
		}

		/**
		 * Add the commission information to order line item
		 *
		 * @param $item_id
		 * @param $item
		 * @param $_product
		 *
		 * @use woocommerce_after_order_itemmeta hook
		 * @since 1.9.12
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function commission_info_in_order_line_item( $item_id, $item, $_product ) {
			/** @var $theorder WC_Order */
			global $theorder;

			if ( $theorder && ( ! empty( $item['commission_id'] ) || ! empty( $item['child__commission_id'] ) ) && apply_filters( 'yith_wcmv_show_commission_info_in_order_line_item', true ) ) {
				$commission_meta_id         = ! empty( $item['commission_id'] ) ? 'commission_id' : 'child__commission_id';
				$commission                 = YITH_Commission( $item[ $commission_meta_id ] );
				$commission_included_tax    = wc_get_order_item_meta( $item_id, '_commission_included_tax', true );
				$commission_included_coupon = wc_get_order_item_meta( $item_id, '_commission_included_coupon', true );


				$tax_string = array(
					'website' => _x( 'credited to the website admin', '[Admin]: Option description', 'yith-woocommerce-product-vendors' ),
					'split'   => _x( 'split by percentage between admin and vendor', '[Admin]: Option description', 'yith-woocommerce-product-vendors' ),
					'vendor'  => _x( 'credited to the vendor', '[Admin]: Option description', 'yith-woocommerce-product-vendors' ),
				);

				$on_product_price_text  = _x( 'on product price', 'part of: Commission: 19,00$ (50% on product price)', 'yith-woocommerce-product-vendors' );
				$on_shipping_price_text = _x( 'on shipping price', 'part of: Commission: 19,00$ (50% on product price)', 'yith-woocommerce-product-vendors' );

				if ( 'yes' == $commission_included_tax ) {
					$commission_included_tax = 'split';
				} elseif ( 'no' == $commission_included_tax ) {
					$commission_included_tax = 'website';
				}

				$tax = isset( $tax_string[ $commission_included_tax ] ) ? $tax_string[ $commission_included_tax ] : '';

				/**
				 * Support for old tax management commission
				 */
				if ( 'yes' == $commission_included_tax ) {
					$tax = _x( 'included', 'means: Vendor commission have been calculated: tax included', 'yith-woocommerce-product-vendors' );
				} elseif ( 'no' == $commission_included_tax ) {
					$tax = _x( 'excluded', 'means: Vendor commission have been calculated: tax excluded', 'yith-woocommerce-product-vendors' );
				}

				$coupon                  = 'yes' == $commission_included_coupon ? _x( 'included', 'means: Vendor commission have been calculated: tax included', 'yith-woocommerce-product-vendors' ) : _x( 'excluded', 'means: Vendor commission have been calculated: tax excluded', 'yith-woocommerce-product-vendors' );
				$refunded_amount_message = ! empty( (float) $commission->get_amount_refunded( 'edit' ) ) ? '%s: <strong class="commission-amount-refunded">%s</strong><br/>' : '';
				$refunded_amount_message = sprintf( $refunded_amount_message,
					_x( 'Refunded amount', 'Single order label', 'yith-woocommerce-product-vendors' ),
					$commission->get_amount_refunded( 'display', array( 'currency' => $theorder->get_currency() ) ) );

				$msg = sprintf( '%s: <strong>%s</strong> (%s %s)<br/>%s%s: <strong>%s</strong>',
					__( 'Commission amount', 'yith-woocommerce-product-vendors' ),
					$commission->get_amount( 'display', array( 'currency' => $theorder->get_currency() ) ),
					$commission->get_rate( 'display' ),
					'shipping' == $commission->type ? $on_shipping_price_text : $on_product_price_text,
					$refunded_amount_message,
					_x( 'Amount to pay', 'Single order label', 'yith-woocommerce-product-vendors' ),
					$commission->get_amount_to_pay( 'display', array( 'currency' => $theorder->get_currency() ) )
				);

				if ( 'product' == $commission->type ) {
					$msg .= sprintf( '<br/><small><em>%s: %s <strong>%s</strong> - %s <strong>%s</strong></em></small>',
						_x( 'Vendor commission has been calculated', 'part of: Vendor commission have been calculated: tax included', 'yith-woocommerce-product-vendors' ),
						_x( 'with tax', 'part of: tax included or tax excluded', 'yith-woocommerce-product-vendors' ),
						$tax,
						_x( 'coupon', 'part of: coupon included or coupon excluded', 'yith-woocommerce-product-vendors' ),
						$coupon );
				}

				$msg = apply_filters( 'yith_wcmv_order_details_page_commission_message', $msg, $item_id );

				printf( '<span class="yith-order-item-commission-details">%s</span>', $msg );
			}
		}

		/**
		 * Add Order actions for vendors
		 *
		 * @param $email object email id
		 *
		 * @use    woocommerce_resend_order_emails_available hook
		 * @return array
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since  1.9.14
		 */
		public function resend_order_emails_available( $emails ) {
			$prevent_resend_email = 'no' == get_option( 'yith_wpv_vendors_option_order_prevent_resend_email', 'no' ) ? false : true;
			if ( $prevent_resend_email ) {
				$vendor = yith_get_vendor( 'current', 'user' );
				if ( $vendor->is_valid() && $vendor->has_limited_access() ) {
					$emails = array();
				}
			} else {
				$available_emails = array(
					'new_order_to_vendor'       => __( 'New order (to vendor)', 'yith-woocommerce-product-vendors' ),
					'cancelled_order_to_vendor' => __( 'Cancelled order (to vendor)', 'yith-woocommerce-product-vendors' )
				);

				/**
				 * Support for WooCommerce 3.1 or lower
				 */
				if ( 'woocommerce_resend_order_emails_available' == current_action() ) {
					$available_emails = array_keys( $available_emails );
				}

				$emails = array_merge( $emails, $available_emails );
			}

			return $emails;
		}

		/**
		 * Add input hidden with customer id
		 *
		 * @param $order WC_Order object
		 *
		 * @return void
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since  1.9.18
		 */
		public function hide_customer_info( $order ) {
			if ( $order instanceof WC_Order ) {
				$user_id = absint( $order->get_user_id() );
				ob_start(); ?>
				<input type="hidden" name="customer_user" value="<?php echo $user_id; ?>"/>
				<?php
				echo ob_get_clean();
			}
		}

		/**
		 * Parent to Child synchronization
		 *
		 *
		 * @param $order_id     int The parent id order
		 * @param $old_status   string Old Status
		 * @param $new_status   string New Status
		 *
		 *
		 * @return void
		 * @since    2.0.8
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function parent_order_status_synchronization( $order_id, $old_status, $new_status ) {
			$parent_order_id = wp_get_post_parent_id( $order_id );
			$status_to_sync  = array(
				'completed',
				'refunded'
			);

			if ( $parent_order_id ) {

				remove_action( 'woocommerce_order_status_changed', array(
					$this,
					'suborder_status_synchronization'
				), 30, 3 );

				$suborder_ids      = YITH_Vendors()->orders->get_suborder( $parent_order_id, true );
				$new_status_count  = 0;
				$suborder_count    = count( $suborder_ids );
				$suborder_statuses = array();

				foreach ( $suborder_ids as $suborder_id ) {
					$suborder        = wc_get_order( $suborder_id );
					$suborder_status = $suborder->get_status( 'edit' );
					if ( $new_status == $suborder_status ) {
						$new_status_count ++;
					}

					if ( ! isset( $suborder_statuses[ $suborder_status ] ) ) {
						$suborder_statuses[ $suborder_status ] = 1;
					} else {
						$suborder_statuses[ $suborder_status ] ++;
					}
				}

				$parent_order = wc_get_order( $parent_order_id );

				if ( $suborder_count == $new_status_count ) {
					if ( 'refunded' != $new_status ) {
						$parent_order->update_status( $new_status, _x( "Sync with vendor's suborders: ", 'Order note', 'yith-woocommerce-product-vendors' ) );
					}
				} elseif ( $suborder_count != 0 ) {
					/**
					 * If the parent order have only 1 suborder I can sync it with the same status.
					 * Otherwise I set the parent order to processing
					 */
					if ( $suborder_count == 1 ) {
						if ( 'refunded' != $new_status ) {
							$parent_order->update_status( $new_status, _x( "Sync with vendor's suborders: ", 'Order note', 'yith-woocommerce-product-vendors' ) );
						}
					}
				}

				add_action( 'woocommerce_order_status_changed', array(
					$this,
					'suborder_status_synchronization'
				), 30, 3 );
			}
		}

		/**
		 * Filtered the order preview data
		 *
		 * @param $data mixed|array The order preview data
		 * @param $order WC_Order Current order object
		 *
		 * @return mixed|array Filtered preview data
		 * @since 3.4.1
		 * @author Anrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function order_preview_get_order_details( $data, $order ) {
			if ( $this->is_vendor_order_page() ) {
				if ( 'yes' == get_option( 'yith_wpv_vendors_option_order_hide_customer', 'no' ) ) {
					$data['data']['billing']['phone'] = $data['data']['billing']['email'] = '';
				}

				if ( 'yes' == get_option( 'yith_wpv_vendors_option_order_hide_payment', 'no' ) ) {
					$data['payment_via'] = '';
				}

				if ( 'yes' == get_option( 'yith_wpv_vendors_option_order_hide_shipping_billing', 'no' ) ) {
					$data['formatted_shipping_address'] = $data['formatted_billing_address'] = '';
				}
			}

			return $data;
		}

		/**
		 * @param $item WC_Order_Item_Shipping
		 * @param $package_key
		 * @param $package
		 * @param $order WC_Order
		 */
		public function add_vendor_information_to_parent_shipping_item( $item, $package_key, $package, $order ) {
			if ( $order instanceof WC_Order && 'checkout' == $order->get_created_via() && ! empty( $package['yith-vendor'] ) && $package['yith-vendor'] instanceof YITH_Vendor ) {
				$checkout = wc()->checkout();
				if ( ! empty( $checkout ) ) {
					$package_id = $package['rates'][ $checkout->shipping_methods[ $package_key ] ]->get_id();
					$vendor     = $package['yith-vendor'];
					$item->add_meta_data( '_vendor_package_id', $package_id, true );
					$item->add_meta_data( 'vendor_id', $vendor->id, true );
					$item->save();
				}
			}
		}
	}
}
