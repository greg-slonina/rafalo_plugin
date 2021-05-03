<?php 
global $WCFM, $WCFMu, $wpo_wcpdf, $order, $order_id, $vendor_id, $document, $document_type; 

$post = get_post($order_id);

if ( WC()->payment_gateways() ) {
	$payment_gateways = WC()->payment_gateways->payment_gateways();
} else {
	$payment_gateways = array();
}

if( !is_a( $order, 'WC_Order' ) ) $payment_method = '';
else $payment_method = ! empty( $order->get_payment_method() ) ? $order->get_payment_method() : '';

$order_type_object = get_post_type_object( $post->post_type );

// Get line items
$line_items          = $order->get_items( apply_filters( 'woocommerce_admin_order_item_types', 'line_item' ) );
$line_items_fee      = $order->get_items( 'fee' );
$line_items_shipping = $order->get_items( 'shipping' );

$order_taxes = $classes_options = array();
if( $is_wcfm_order_details_tax_line_item = apply_filters( 'wcfm_order_details_tax_line_item', true ) ) {
	if ( wc_tax_enabled() ) {
		$order_taxes         = $order->get_taxes();
		$tax_classes         = WC_Tax::get_tax_classes();
		$classes_options[''] = __( 'Standard', 'wc-frontend-manager-ultimate' );
	
		if ( ! empty( $tax_classes ) ) {
			foreach ( $tax_classes as $class ) {
				$classes_options[ sanitize_title( $class ) ] = $class;
			}
		}
	
		// Older orders won't have line taxes so we need to handle them differently :(
		$tax_data = '';
		if ( $line_items ) {
			$check_item = current( $line_items );
			$tax_data   = maybe_unserialize( isset( $check_item['line_tax_data'] ) ? $check_item['line_tax_data'] : '' );
		} elseif ( $line_items_shipping ) {
			$check_item = current( $line_items_shipping );
			$tax_data = maybe_unserialize( isset( $check_item['taxes'] ) ? $check_item['taxes'] : '' );
		} elseif ( $line_items_fee ) {
			$check_item = current( $line_items_fee );
			$tax_data   = maybe_unserialize( isset( $check_item['line_tax_data'] ) ? $check_item['line_tax_data'] : '' );
		}
	
		$legacy_order     = ! empty( $order_taxes ) && empty( $tax_data ) && ! is_array( $tax_data );
		$show_tax_columns = ! $legacy_order || sizeof( $order_taxes ) === 1;
	}
}

// Marketplace Filters
$line_items          = apply_filters( 'wcfm_valid_line_items', $line_items, $order->get_id() );
$line_items_shipping = apply_filters( 'wcfm_valid_shipping_items', $line_items_shipping, $order->get_id() );

?>
<?php do_action( 'wcfm_pdf_invoice_before_document', $document_type, $document, $order ); ?>

<table class="head container">
	<tr>
		<td class="header">
		<?php
		if( $document->has_header_logo() ) {
			$document->header_logo();
		} else {
			echo apply_filters( 'wcfm_pdf_invoice_invoice_title', __( 'Invoice', 'wc-frontend-manager-ultimate' ) );
		}
		?>
		</td>
		<td class="shop-info">
			<div class="shop-name"><h3><?php $document->shop_name(); ?></h3></div>
			<div class="shop-address"><?php $document->shop_address(); ?></div>
		</td>
	</tr>
</table>

<h1 class="document-type-label">
<?php 
if( $document->has_header_logo() ) {
	if( wcfm_is_vendor() ) {
		echo apply_filters( 'wcfm_pdf_invoice_invoice_title', __( 'Commission Invoice', 'wc-frontend-manager-ultimate' ) );
	} else {
		echo apply_filters( 'wcfm_pdf_invoice_invoice_title', __( 'Invoice', 'wc-frontend-manager-ultimate' ) );
	}
}
?>
</h1>

<?php do_action( 'wcfm_pdf_invoice_after_document_label', $document_type, $document, $order ); ?>

<table class="order-data-addresses">
	<tr>
		<td class="address billing-address">
		  <?php if( wcfm_is_vendor( $vendor_id ) ) { ?>
		  	<div class="vendor-shop-name"><h3><?php echo wcfm_get_vendor_store_name( $vendor_id ); ?></h3></div>
		  	<div class="vendor-shop-address"><?php echo wcfm_get_vendor_store_address_by_vendor( $vendor_id ); ?></div>
		  	<div class="vendor-shop-email"><?php echo wcfm_get_vendor_store_email_by_vendor( $vendor_id ); ?></div>
		  	<div class="vendor-shop-phone"><?php echo wcfm_get_vendor_store_phone_by_vendor( $vendor_id ); ?></div>
		  	<?php do_action( 'wcfm_commission_invoice_after_vendor_details', $vendor_id ); ?>
		  <?php } else { ?> 
				<!-- <h3><?php _e( 'Billing Address:', 'wc-frontend-manager-ultimate' ); ?></h3> -->
				<?php if( apply_filters( 'wcfm_allow_customer_billing_details', true ) ) { ?>
					<?php echo wp_kses( $order->get_formatted_billing_address(), array( 'br' => array() ) ); ?>
				<?php } ?>
				<?php if ( apply_filters( 'wcfm_allow_order_customer_details', true ) ) { ?>
					<div class="billing-email"><?php echo $order->get_billing_email(); ?></div>
				<?php } ?>
				<?php if ( apply_filters( 'wcfm_allow_order_customer_details', true ) ) { ?>
					<div class="billing-phone"><?php echo $order->get_billing_phone(); ?></div>
				<?php } ?>
				<?php do_action( 'wpo_wcpdf_after_billing_address', $document_type, $order ); ?>
			<?php } ?>
		</td>
		<td class="address shipping-address">
		  <?php if( !wcfm_is_vendor( $vendor_id ) ) { ?>
				<?php if ( apply_filters( 'wcfm_allow_customer_shipping_details', true ) && ( ( $order->needs_shipping_address() &&  $order->get_formatted_shipping_address() ) || apply_filters( 'wcfm_is_force_shipping_address', false ) ) ) { ?>
				<h3><?php _e( 'Ship To:', 'wc-frontend-manager-ultimate' ); ?></h3>
				<?php echo wp_kses( $order->get_formatted_shipping_address(), array( 'br' => array() ) ); ?>
				<?php } ?>
				<?php 
				if( apply_filters( 'wcfm_is_allow_order_data_after_shipping_address', true ) ) {
					do_action( 'woocommerce_admin_order_data_after_shipping_address', $order ); 
				}
				?>
			<?php } ?>
		</td>
		<td class="order-data">
			<table>
				<?php do_action( 'wcfm_pdf_invoice_before_order_data', $document_type, $order ); ?>
				<?php if( wcfm_is_vendor( $vendor_id ) ) { ?>
					<tr class="invoice-number">
						<th><?php _e( 'Invoice Number:', 'woocommerce-pdf-invoices-packing-slips' ); ?></th>
						<td><?php echo __( 'commission-invoice', 'wc-frontend-manager-ultimate' ) . '-'.$vendor_id.'-'.$order->get_order_number(); ?></td>
					</tr>
				<?php } ?>
				<tr class="order-number">
					<th><?php _e( 'Order Number:', 'wc-frontend-manager-ultimate' ); ?></th>
					<td>#<?php echo $order->get_order_number(); ?></td>
				</tr>
				<tr class="order-date">
					<th><?php _e( 'Order Date:', 'wc-frontend-manager-ultimate' ); ?></th>
					<td><?php echo date_i18n( wc_date_format(), strtotime( $post->post_date ) ); ?> @<?php echo date_i18n( wc_time_format(), strtotime( $post->post_date ) ); ?></td>
				</tr>
				<tr class="payment-method">
					<th><?php _e( 'Payment Method:', 'wc-frontend-manager-ultimate' ); ?></th>
					<td><?php printf( __( '%s', 'wc-frontend-manager-ultimate' ), ( isset( $payment_gateways[ $payment_method ] ) ? esc_html( $payment_gateways[ $payment_method ]->get_title() ) : esc_html( $payment_method ) ) ); ?></td>
				</tr>
				<?php do_action( 'wpo_wcpdf_after_order_data', $document_type, $order ); ?>
				<?php do_action( 'wcfm_pdf_invoice_after_order_data', $document_type, $order ); ?>
			</table>			
		</td>
	</tr>
</table>

<?php do_action( 'wpo_wcpdf_before_order_details', $document_type, $order ); ?>
<?php do_action( 'wcfm_pdf_invoice_before_order_details', $document_type, $order ); ?>

<table class="order-details">
	<thead>
		<tr>
			<td class="product" style="width:30%"><?php _e('Product', 'wc-frontend-manager-ultimate' ); ?></td>
			<td class="price"><?php _e('Price', 'wc-frontend-manager-ultimate' ); ?></td>
			<?php if( $is_wcfm_order_details_line_total_head = apply_filters( 'wcfm_order_details_line_total_head', true ) ) { ?>
				<td class="line_cost"><?php _e( 'Total', 'wc-frontend-manager-ultimate' ); ?></td>
			<?php } ?>
			<?php if( $is_wcfm_order_details_tax_line_item = apply_filters( 'wcfm_order_details_tax_line_item', true ) ) { ?>
				<?php
					if ( empty( $legacy_order ) && ! empty( $order_taxes ) ) :
						foreach ( $order_taxes as $tax_id => $tax_item ) :
							$tax_class      = wc_get_tax_class_by_tax_id( $tax_item['rate_id'] );
							$tax_class_name = isset( $classes_options[ $tax_class ] ) ? $classes_options[ $tax_class ] : __( 'Tax', 'wc-frontend-manager' );
							$column_label   = ! empty( $tax_item['label'] ) ? $tax_item['label'] : __( 'Tax', 'wc-frontend-manager' );
							$column_tip     = $tax_item['name'] . ' (' . $tax_class_name . ')';
							?>
							<td class="line_tax text_tip" data-tip="<?php echo esc_attr( $column_tip ); ?>">
								<?php echo esc_attr( $column_label ); ?>
							</td>
							<?php
						endforeach;
					endif;
				?>
			<?php } ?>
			<?php do_action( 'wcfm_order_details_after_line_total_head', $order ); ?>
		</tr>
	</thead>
	<tbody>
		<?php foreach ( $line_items as $item_id => $item ) : $_product  = $item->get_product(); ?>
		<tr class="<?php echo apply_filters( 'wcfm_pdf_invoice_item_row_class', $item_id, $document_type, $order, $item_id ); ?>">
			<td class="product" style="width:30%">
				<span class="item-name"><?php echo esc_html( apply_filters( 'wcfm_order_item_name', $item->get_name(), $item ) ); ?></span>
				<?php
				if ( $_product && $_product->get_sku() ) {
					echo '<div class="wc-order-item-sku"><strong>' . __( 'SKU:', 'wc-frontend-manager' ) . '</strong> ' . esc_html( $_product->get_sku() ) . '</div>';
				}
	
				if ( ! empty( $item->get_variation_id() ) ) {
					echo '<div class="wc-order-item-variation"><strong>' . __( 'Variation ID:', 'wc-frontend-manager' ) . '</strong> ';
					if ( ! empty( $item->get_variation_id() ) && 'product_variation' === get_post_type( $item->get_variation_id() ) ) {
						echo esc_html( $item->get_variation_id() );
					} elseif ( ! empty( $item->get_variation_id() ) ) {
						echo esc_html( $item->get_variation_id() ) . ' (' . __( 'No longer exists', 'wc-frontend-manager' ) . ')';
					}
					echo '</div>';
				}
				?>
				
				<div class="view">
				  <?php do_action( 'woocommerce_before_order_itemmeta', $item_id, $item, $_product ) ?>
				  <?php do_action( 'wcfm_pdf_invoice_before_item_meta', $document_type, $item, $order  ); ?>
					<?php wc_display_item_meta( $item ); ?>
					<?php do_action( 'wcfm_pdf_invoice_after_item_meta', $document_type, $item, $order  ); ?>
					<?php //do_action( 'woocommerce_after_order_itemmeta', $item_id, $item, $_product ) ?>
				</div>
			</td>
			<td class="price">
			  <?php
					if ( $item->get_total() ) {
						echo wc_price( $order->get_item_subtotal( $item, false, true ), array( 'currency' => $order->get_currency() ) );
						echo '<small class="times">&times;</small> ' . ( $item->get_quantity() ? esc_html( $item->get_quantity() ) : '1' );
						
						if ( $item->get_subtotal() != $item->get_total() ) {
							echo '<small class="discount">-' . wc_price( wc_format_decimal( $order->get_item_subtotal( $item, false, false ) - $order->get_item_total( $item, false, false ), '' ), array( 'currency' => $order->get_currency() ) ) . '</small>';
						}
					}
				?>
				
					<?php
						if ( $refunded_qty = $order->get_qty_refunded_for_item( $item_id ) ) {
							echo '<small class="refunded">-' . ( $refunded_qty * -1 ) . '</small>';
						}
					?>
			</td>
			<?php if( $is_wcfm_order_details_line_total = apply_filters( 'wcfm_order_details_line_total', true ) ) { ?>
				<td class="line_cost" data-sort-value="<?php echo esc_attr( ( $item->get_total() ) ? $item->get_total() : '' ); ?>">
					<div class="view">
						<?php
							if ( $item->get_total() ) {
								echo wc_price( $item->get_subtotal(), array( 'currency' => $order->get_currency() ) );
							}
			
							if ( $item->get_subtotal() !== $item->get_total() ) {
								echo '<small class="discount">-' . wc_price( wc_format_decimal( $item->get_subtotal() - $item->get_total(), '' ), array( 'currency' => $order->get_currency() ) ) . '</small>';
							}
			
							if ( $refunded = $order->get_total_refunded_for_item( $item_id ) ) {
								echo '<small class="refunded">-' . wc_price( $refunded, array( 'currency' => $order->get_currency() ) ) . '</small>';
							}
						?>
					</div>
				</td>
			<?php } ?>
			
			<?php if( $is_wcfm_order_details_tax_line_item = apply_filters( 'wcfm_order_details_tax_line_item', true ) ) { ?>
				<?php
				if ( ( $tax_data = $item->get_taxes() ) && wc_tax_enabled() ) {
						foreach ( $order_taxes as $tax_item ) {
							$tax_item_id       = $tax_item['rate_id'];
							$tax_item_total    = isset( $tax_data['total'][ $tax_item_id ] ) ? $tax_data['total'][ $tax_item_id ] : '';
							$tax_item_subtotal = isset( $tax_data['subtotal'][ $tax_item_id ] ) ? $tax_data['subtotal'][ $tax_item_id ] : '';
							?>
							<td class="line_tax">
								<div class="view">
									<?php
										if ( '' != $tax_item_total ) {
											echo wc_price( wc_round_tax_total( $tax_item_subtotal ), array( 'currency' => $order->get_currency() ) );
										} else {
											echo '&ndash;';
										}
			
										if ( $item->get_subtotal() !== $item->get_total() ) {
											echo '<small class="discount">-' . wc_price( wc_round_tax_total( $tax_item_subtotal - $tax_item_total ), array( 'currency' => $order->get_currency() ) ) . '</small>';
										}
			
										if ( $refunded = $order->get_tax_refunded_for_item( $item_id, $tax_item_id ) ) {
											echo '<small class="refunded">-' . wc_price( $refunded, array( 'currency' => $order->get_currency() ) ) . '</small>';
										}
									?>
								</div>
							</td>
							<?php
						}
					}
				?>
			<?php } ?>
			
			<?php do_action( 'wcfm_after_order_details_line_total', $item, $order ); ?>
								
		</tr>
		<?php endforeach; ?>
	</tbody>
	
	
	<?php if( $is_wcfm_order_details_shipping_line_item = apply_filters( 'wcfm_order_details_shipping_line_item', true ) ) { ?>
	<tbody id="order_shipping_line_items">
	<?php
		$shipping_methods = WC()->shipping() ? WC()->shipping->load_shipping_methods() : array();
		foreach ( $line_items_shipping as $item_id => $item ) {
			?>
			<tr class="shipping <?php echo ( ! empty( $class ) ) ? $class : ''; ?>" data-order_item_id="<?php echo $item_id; ?>">
				<td class="name" style="width:30%">
					<div class="view">
						<?php echo ! empty( $item->get_name() ) ? wc_clean( $item->get_name() ) : __( 'Shipping', 'wc-frontend-manager-ultimate' ); ?>
					</div>
			
					<div class="view">
					  <?php do_action( 'woocommerce_before_order_itemmeta', $item_id, $item, null ) ?>
						<?php wc_display_item_meta( $item ); ?>
						<?php do_action( 'woocommerce_after_order_itemmeta', $item_id, $item, null ) ?>
					</div>
				</td>
				
				<td class="no-borders"></td>
			
				<?php do_action( 'woocommerce_admin_order_item_values', null, $item, absint( $item_id ) ); ?>
			
				<td class="line_cost">
					<div class="view">
						<?php
							echo ( isset( $item['cost'] ) ) ? wc_price( wc_round_tax_total( $item['cost'] ), array( 'currency' => $order->get_currency() ) ) : '';
			
							if ( $refunded = $order->get_total_refunded_for_item( $item_id, 'shipping' ) ) {
								echo '<small class="refunded">-' . wc_price( $refunded, array( 'currency' => $order->get_currency() ) ) . '</small>';
							}
						?>
					</div>
				</td>
				
				<?php if( $is_wcfm_order_details_tax_line_item = apply_filters( 'wcfm_order_details_tax_line_item', true ) ) { ?>
					<?php
						if ( ( $tax_data = $item->get_taxes() ) && wc_tax_enabled() ) {
							foreach ( $order_taxes as $tax_item ) {
								$tax_item_id    = $tax_item->get_rate_id();
								$tax_item_total = isset( $tax_data['total'][ $tax_item_id ] ) ? $tax_data['total'][ $tax_item_id ] : '';
								?>
									<td class="line_tax no_ipad no_mob" >
										<div class="view">
											<?php
												echo ( '' != $tax_item_total ) ? wc_price( wc_round_tax_total( $tax_item_total ), array( 'currency' => $order->get_currency() ) ) : '&ndash;';
				
												if ( $refunded = $order->get_tax_refunded_for_item( $item_id, $tax_item_id, 'shipping' ) ) {
													echo '<small class="refunded">-' . wc_price( $refunded, array( 'currency' => $order->get_currency() ) ) . '</small>';
												}
											?>
										</div>
									</td>
				
								<?php
							}
						}
					?>
				<?php } ?>
				
				<?php do_action( 'wcfm_after_order_details_shipping_total', $item, $order ); ?>
			
			</tr>
			<?php
		}
		do_action( 'woocommerce_admin_order_items_after_shipping', $order->get_id() );
	?>
	</tbody>
	<?php } ?>
	
	<?php if( $is_wcfm_order_details_fee_line_item = apply_filters( 'wcfm_order_details_fee_line_item', true ) ) { ?>
	<tbody id="order_fee_line_items">
	<?php
		foreach ( $line_items_fee as $item_id => $item ) {
			?>
			<tr class="fee <?php echo ( ! empty( $class ) ) ? $class : ''; ?>" data-order_item_id="<?php echo $item_id; ?>">
				<td class="name" style="width:30%">
					<div class="view">
						<?php echo ! empty( $item->get_name() ) ? esc_html( $item->get_name() ) : __( 'Fee', 'wc-frontend-manager-ultimate' ); ?>
					</div>
				</td>
				
				<td class="no-borders"></td>
			
				<?php do_action( 'woocommerce_admin_order_item_values', null, $item, absint( $item_id ) ); ?>
			
				<td class="line_cost">
					<div class="view">
						<?php
							echo ( $item->get_total() ) ? wc_price( wc_round_tax_total( $item->get_total() ), array( 'currency' => $order->get_currency() ) ) : '';
			
							if ( $refunded = $order->get_total_refunded_for_item( $item_id, 'fee' ) ) {
								echo '<small class="refunded">-' . wc_price( $refunded, array( 'currency' => $order->get_currency() ) ) . '</small>';
							}
						?>
					</div>
				</td>
				
				<?php if( $is_wcfm_order_details_tax_line_item = apply_filters( 'wcfm_order_details_tax_line_item', true ) ) { ?>
					<?php
						if ( empty( $legacy_order ) && wc_tax_enabled() ) :
							$line_tax_data = isset( $item['line_tax_data'] ) ? $item['line_tax_data'] : '';
							$tax_data      = maybe_unserialize( $line_tax_data );
				
							foreach ( $order_taxes as $tax_item ) :
								$tax_item_id       = $tax_item['rate_id'];
								$tax_item_total    = isset( $tax_data['total'][ $tax_item_id ] ) ? $tax_data['total'][ $tax_item_id ] : '';
								?>
									<td class="line_tax no_ipad no_mob" >
										<div class="view">
											<?php
												echo ( '' != $tax_item_total ) ? wc_price( wc_round_tax_total( $tax_item_total ), array( 'currency' => $order->get_currency() ) ) : '&ndash;';
				
												if ( $refunded = $order->get_tax_refunded_for_item( $item_id, $tax_item_id, 'fee' ) ) {
													echo '<small class="refunded">-' . wc_price( $refunded, array( 'currency' => $order->get_currency() ) ) . '</small>';
												}
											?>
										</div>
									</td>
				
								<?php
							endforeach;
						endif;
					?>
				<?php } ?>
				
			</tr>
			<?php
		}
		do_action( 'woocommerce_admin_order_items_after_fees', $order->get_id() );
	?>
	</tbody>
	<?php } ?>
	
	<?php if( $is_wcfm_order_details_refund_line_item = apply_filters( 'wcfm_order_details_refund_line_item', true ) ) { ?>
	<tbody id="order_refunds">
	<?php
		if ( $refunds = $order->get_refunds() ) {
			$cur_vendor_id   = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );
			foreach ( $refunds as $refund ) {
				$who_refunded = new WP_User( $refund->get_refunded_by() );
				if( wcfm_is_vendor( $vendor_id ) && ( !$who_refunded || ( $who_refunded && ( $who_refunded->ID != $cur_vendor_id ) ) ) ) continue;
				?>
				<tr class="refund <?php echo ( ! empty( $class ) ) ? $class : ''; ?>" data-order_refund_id="<?php echo $refund->get_id(); ?>">
					<td class="name" style="width:30%">
						<?php
							/* translators: 1: refund id 2: date */
							printf( __( 'Refund #%1$s - %2$s', 'woocommerce' ), $refund->get_id(), wc_format_datetime( $order->get_date_created(), get_option( 'date_format' ) . ', ' . get_option( 'time_format' ) ) );
				
							if ( $who_refunded->exists() ) {
								echo '<br />' . esc_attr_x( 'by', 'Ex: Refund - $date >by< $username', 'woocommerce' ) . ' ' . '<abbr class="refund_by" title="' . sprintf( esc_attr__( 'ID: %d', 'woocommerce' ), absint( $who_refunded->ID ) ) . '">' . esc_attr( $who_refunded->display_name ) . '</abbr>' ;
							}
						?>
						<?php if ( $refund->get_reason() ) : ?>
							<br /><p class="description"><?php echo esc_html( $refund->get_reason() ); ?></p>
						<?php endif; ?>
					</td>
					
					<td class="no-borders"></td>
				
					<?php do_action( 'woocommerce_admin_order_item_values', null, $refund, $refund->get_id() ); ?>
				
					<td class="line_cost refunded-total">
						<div class="view">
							<?php echo wc_price( '-' . $refund->get_amount() ); ?>
						</div>
					</td>
				
					<?php if ( wc_tax_enabled() ) : $total_taxes = count( $order_taxes ); ?>
						<?php for ( $i = 0;  $i < $total_taxes; $i++ ) : ?>
							<td class="line_tax no_ipad no_mob" ></td>
						<?php endfor; ?>
					<?php endif; ?>
					
					<?php do_action( 'wcfm_after_order_details_refund_total', $item, $order ); ?>
				</tr>
				<?php
			}
			do_action( 'woocommerce_admin_order_items_after_refunds', $order->get_id() );
		}
	?>
	</tbody>
	<?php } ?>
</table>

<table class="notes-totals" style="width:100%">
	<tbody>
		<tr class="no-borders">
			<td class="no-borders" style="width:60%">
				<div class="customer-notes">
					<?php do_action( 'wcfm_pdf_invoice_before_customer_notes', $document_type, $order ); ?>
					<?php if ( $document->get_shipping_notes() ) : ?>
						<h3><?php _e( 'Customer Notes', 'wc-frontend-manager-ultimate' ); ?></h3>
						<?php //$document->shipping_notes(); ?>
					<?php endif; ?>
					<?php do_action( 'wcfm_pdf_invoice_after_customer_notes', $document_type, $order ); ?>
				</div>				
			</td>
		  
			<td class="no-borders" style="width:40%">
				<table class="totals">
					<tfoot>
					  <?php if( $is_wcfm_order_details_coupon_line_item = apply_filters( 'wcfm_order_details_coupon_line_item', true ) ) { ?>
							<tr>
								<td class="label description" style="text-align:left;"><span class="img_tip" data-tip="<?php _e( 'This is the total discount. Discounts are defined per line item.', 'wc-frontend-manager-ultimate' ) ; ?>"></span> <?php _e( 'Discount', 'wc-frontend-manager-ultimate' ); ?>:</td>
								<td class="total price" style="text-align:center;">
									<?php echo wc_price( $order->get_total_discount(), array( 'currency' => $order->get_currency() ) ); ?>
								</td>
							</tr>
						<?php } ?>
				
						<?php //do_action( 'woocommerce_admin_order_totals_after_discount', $order->get_id() ); ?>
				
						<?php if( apply_filters( 'wcfm_order_details_shipping_line_item', true ) && apply_filters( 'wcfm_order_details_shipping_total', true ) && $order->get_formatted_shipping_address() ) { ?>
							<tr>
								<td class="label description" style="text-align:left;"><span class="img_tip" data-tip="<?php _e( 'This is the shipping and handling total costs for the order.', 'wc-frontend-manager-ultimate' ) ; ?>"></span> <?php _e( 'Shipping', 'wc-frontend-manager-ultimate' ); ?>:</td>
								<td class="total price" style="text-align:center;"><?php
									if ( ( $refunded = $order->get_total_shipping_refunded() ) > 0 ) {
										echo '<del>' . strip_tags( wc_price( $order->get_total_shipping(), array( 'currency' => $order->get_currency() ) ) ) . '</del> <ins>' . wc_price( $order->get_total_shipping() - $refunded, array( 'currency' => $order->get_currency() ) ) . '</ins>';
									} else {
										echo wc_price( $order->get_total_shipping(), array( 'currency' => $order->get_currency() ) );
									}
								?></td>
							</tr>
						<?php } ?>
				
						<?php //do_action( 'woocommerce_admin_order_totals_after_shipping', $order->get_id() ); ?>
				
						<?php if( $is_wcfm_order_details_tax_total = apply_filters( 'wcfm_order_details_tax_total', true ) ) { ?>
							<?php if ( wc_tax_enabled() ) : ?>
								<?php foreach ( $order->get_tax_totals() as $code => $tax ) : ?>
									<tr>
										<td class="label description" style="text-align:left;"><?php echo $tax->label; ?>:</td>
										<td class="total price" style="text-align:center;"><?php
											if ( ( $refunded = $order->get_total_tax_refunded_by_rate_id( $tax->rate_id ) ) > 0 ) {
												echo '<del>' . strip_tags( $tax->formatted_amount ) . '</del> <ins>' . wc_price( WC_Tax::round( $tax->amount, wc_get_price_decimals() ) - WC_Tax::round( $refunded, wc_get_price_decimals() ), array( 'currency' => $order->get_currency() ) ) . '</ins>';
											} else {
												echo $tax->formatted_amount;
											}
										?></td>
									</tr>
								<?php endforeach; ?>
							<?php endif; ?>
						<?php } ?>
				
						<?php //do_action( 'woocommerce_admin_order_totals_after_tax', $order->get_id() ); ?>
				
						<?php if( apply_filters( 'wcfm_order_details_total', true ) ) { ?>
						<tr>
							<td class="label description" style="text-align:left;"><?php _e( 'Order Total', 'wc-frontend-manager-ultimate' ); ?>:</td>
							<td class="total price" style="text-align:center;">
								<div class="view"><?php echo $order->get_formatted_order_total(); ?></div>
							</td>
						</tr>
						<?php } ?>
				
						<?php do_action( 'wcfm_order_totals_after_total', $order->get_id() ); ?>
				
						<?php if( apply_filters( 'wcfm_order_details_refund_line_item', true ) && apply_filters( 'wcfm_order_details_refund_total', true ) ) { ?>
							<?php if ( $order->get_total_refunded() ) : ?>
								<tr>
									<td class="label refunded-total description" style="text-align:left;"><?php _e( 'Refunded', 'wc-frontend-manager-ultimate' ); ?>:</td>
									<td class="total refunded-total price" style="text-align:center;">-<?php echo wc_price( $order->get_total_refunded(), array( 'currency' => $order->get_currency() ) ); ?></td>
								</tr>
							<?php endif; ?>
						<?php } ?>
						
					</tfoot>
				</table>
			</td>
		</tr>
	</tbody>
</table>

<?php do_action( 'wcfm_pdf_invoice_after_order_details', $document_type, $order ); ?>

<?php if ( $document->get_footer() ): ?>
<div id="footer">
	<?php $document->footer(); ?>
</div><!-- #letter-footer -->
<?php endif; ?>
<?php do_action( 'wcfm_pdf_invoice_after_document', $document_type, $order ); ?>
