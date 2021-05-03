<?php 
global $WCFM, $WCFMu, $wpo_wcpdf, $order, $order_id, $document, $document_type; 

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
$line_items = apply_filters( 'wcfm_valid_line_items', $line_items, $order->get_id() );
$line_items_shipping = apply_filters( 'wcfm_valid_shipping_items', $line_items_shipping, $order->get_id() );

?>
<?php do_action( 'wcfm_packing_slip_before_document', $document_type, $document, $order ); ?>

<table class="head container">
	<tr>
	  <?php if( wcfm_is_vendor() ) { ?>
	  	<td class="header">
				<?php
				$vendor_id = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );
				
				$wcfm_vendor_invoice_options = get_option( 'wcfm_vendor_invoice_options', array() );
				$wcfm_vendor_invoice_active = isset( $wcfm_vendor_invoice_options['enable'] ) ? 'yes' : '';
				$wcfm_vendor_invoice_logo = isset( $wcfm_vendor_invoice_options['logo'] ) ? 'yes' : '';
				$wcfm_vendor_invoice_store = isset( $wcfm_vendor_invoice_options['store'] ) ? 'yes' : '';
				$wcfm_vendor_invoice_address = isset( $wcfm_vendor_invoice_options['address'] ) ? 'yes' : '';
				$wcfm_vendor_invoice_email = isset( $wcfm_vendor_invoice_options['email'] ) ? 'yes' : '';
				$wcfm_vendor_invoice_phone = isset( $wcfm_vendor_invoice_options['phone'] ) ? 'yes' : '';
				$wcfm_vendor_invoice_policies = isset( $wcfm_vendor_invoice_options['policies'] ) ? 'yes' : '';
				$wcfm_vendor_invoice_disclaimer = isset( $wcfm_vendor_invoice_options['disclaimer'] ) ? 'yes' : '';
				$wcfm_vendor_invoice_signature = isset( $wcfm_vendor_invoice_options['signature'] ) ? 'yes' : '';
				$wcfm_vendor_invoice_fields = isset( $wcfm_vendor_invoice_options['fields'] ) ? $wcfm_vendor_invoice_options['fields'] : array();
				$wcfm_vendor_invoice_data = (array) get_user_meta( $vendor_id, 'wcfm_vendor_invoice_options', true );
				$wcfm_vendor_disclaimer = isset( $wcfm_vendor_invoice_data['disclaimer'] ) ? $wcfm_vendor_invoice_data['disclaimer'] : '';
				$wcfm_vendor_signature = isset( $wcfm_vendor_invoice_data['signature'] ) ? $wcfm_vendor_invoice_data['signature'] : '';
				
				$store_name = wcfm_get_vendor_store_name( $vendor_id );
				$store_logo = wcfm_get_vendor_store_logo_by_vendor( $vendor_id );
				if( $store_logo && $wcfm_vendor_invoice_logo ) {
					printf('<img style="max-width:150px;" src="%1$s" alt="%2$s" />', $store_logo, $store_name );
				} elseif( $document->has_header_logo() ) {
					$document->header_logo();
				} else {
					echo apply_filters( 'wcfm_packing_slip_invoice_title', __( 'Packing Slip', 'wc-frontend-manager-ultimate' ) );
				}
				?>
			</td>
			<td class="shop-info">
				<?php if( $wcfm_vendor_invoice_store ) { ?>
					<div class="vendor-shop-name"><h3><?php echo $store_name; ?></h3></div>
				<?php } ?>
				<?php if( $wcfm_vendor_invoice_address ) { ?>
					<div class="vendor-shop-address"><?php echo wcfm_get_vendor_store_address_by_vendor( $vendor_id ); ?></div>
				<?php } ?>
				<?php if( $wcfm_vendor_invoice_email ) { ?>
					<div class="vendor-shop-email"><?php echo wcfm_get_vendor_store_email_by_vendor( $vendor_id ); ?></div>
				<?php } ?>
				<?php if( $wcfm_vendor_invoice_phone ) { ?>
					<div class="vendor-shop-phone"><?php echo wcfm_get_vendor_store_phone_by_vendor( $vendor_id ); ?></div>
				<?php } ?>
				<?php
				if( !empty( $wcfm_vendor_invoice_fields ) ) {
					foreach( $wcfm_vendor_invoice_fields as $wvif_key => $wcfm_vendor_invoice_field ) {
						if( isset($wcfm_vendor_invoice_field['is_active']) && $wcfm_vendor_invoice_field['field'] ) {
							if( isset( $wcfm_vendor_invoice_data[$wvif_key] ) && $wcfm_vendor_invoice_data[$wvif_key] ) {
								?>
									<div class="vendor-shop-phone">
									  <?php _e( $wcfm_vendor_invoice_field['field'], 'wc-frontend-manager-ultimate' ); ?>:
									  <?php echo $wcfm_vendor_invoice_data[$wvif_key]; ?>
									</div>
								<?php
							}
						}
					}
				}
				?>
			</td>
	  <?php } else { ?>
			<td class="header">
			<?php
			if( $document->has_header_logo() ) {
				$document->header_logo();
			} else {
				echo apply_filters( 'wcfm_packing_slip_invoice_title', __( 'Packing Slip', 'wc-frontend-manager-ultimate' ) );
			}
			?>
			</td>
			<td class="shop-info">
				<div class="shop-name"><h3><?php $document->shop_name(); ?></h3></div>
				<div class="shop-address"><?php $document->shop_address(); ?></div>
			</td>
		<?php } ?>
	</tr>
</table>

<h1 class="document-type-label">
<?php if( $document->has_header_logo() ) echo apply_filters( 'wcfm_packing_slip_invoice_title', __( 'Packing Slip', 'wc-frontend-manager-ultimate' ) ); ?>
</h1>

<?php do_action( 'wcfm_packing_slip_after_document_label', $document_type, $document, $order ); ?>

<table class="order-data-addresses">
	<tr>
		<td class="address billing-address">
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
		</td>
		<td class="address shipping-address">
			<?php if ( apply_filters( 'wcfm_allow_customer_shipping_details', true ) && ( ( $order->needs_shipping_address() && $order->get_formatted_shipping_address() ) || apply_filters( 'wcfm_is_force_shipping_address', false ) ) ) { ?>
			<h3><?php _e( 'Ship To:', 'wc-frontend-manager-ultimate' ); ?></h3>
			<?php echo wp_kses( $order->get_formatted_shipping_address(), array( 'br' => array() ) ); ?>
			<?php } ?>
			<?php 
			if( apply_filters( 'wcfm_is_allow_order_data_after_shipping_address', true ) ) {
				do_action( 'woocommerce_admin_order_data_after_shipping_address', $order ); 
			}
			?>
		</td>
		<td class="order-data">
			<table>
				<?php do_action( 'wcfm_packing_slip_before_order_data', $document_type, $order ); ?>
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
				<?php do_action( 'wcfm_packing_slip_after_order_data', $document_type, $order ); ?>
			</table>			
		</td>
	</tr>
</table>

<table class="order-details">
	<thead>
		<tr>
			<td class="product" style="width:30%"><?php _e('Product', 'wc-frontend-manager-ultimate' ); ?></td>
			<td class="quantity"><?php _e('Quantity', 'wc-frontend-manager-ultimate '); ?></td>
			<td class="price"><?php _e('Price', 'wc-frontend-manager-ultimate' ); ?></td>
			<td class="line_cost"><?php _e( 'Total', 'wc-frontend-manager-ultimate' ); ?></td>
			<?php if( wc_tax_enabled() && apply_filters( 'wcfm_order_details_tax_line_item', true ) ) { ?>
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
		</tr>
	</thead>
	<?php
	$tax_total = 0;
	$invoice_total = 0;
	?>
	<tbody>
		<?php foreach ( $line_items as $item_id => $item ) : $_product  = $item->get_product(); ?>
		<tr class="<?php echo apply_filters( 'wcfm_packing_slip_item_row_class', $item_id, $document_type, $order, $item_id ); ?>">
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
					<?php 
					if( !class_exists( 'WC_Deposits_Order_Item_Manager' ) || ( class_exists( 'WC_Deposits_Order_Item_Manager' ) && !WC_Deposits_Order_Item_Manager::is_deposit( $item ) ) ) {
						do_action( 'woocommerce_after_order_itemmeta', $item_id, $item, $_product );
					}
					?>
				</div>
			</td>
			<td class="quantity">
			  <div class="view">
					<?php
						echo '<small class="times">&times;</small> ' . ( $item->get_quantity() ? esc_html( $item->get_quantity() ) : '1' );
		
						if ( $refunded_qty = $order->get_qty_refunded_for_item( $item_id ) ) {
							echo '<small class="refunded">' . ( $refunded_qty * -1 ) . '</small>';
						}
					?>
				</div>
			</td>
			
			<td class="price">
			  <?php
					if ( $item->get_total() ) {
						echo wc_price( $order->get_item_total( $item, false, true ), array( 'currency' => $order->get_currency() ) );
						echo '<small class="times">&times;</small> ' . ( $item->get_quantity() ? esc_html( $item->get_quantity() ) : '1' );
					}
				?>
			</td>
			<td class="line_cost" data-sort-value="<?php echo esc_attr( ( $item->get_total() ) ? $item->get_total() : '' ); ?>">
				<div class="view">
					<?php
						if ( $item->get_total() ) {
							$invoice_total += (float)$item->get_total();
							echo wc_price( $item->get_total(), array( 'currency' => $order->get_currency() ) );
						}
					?>
				</div>
			</td>
			
			<?php if( apply_filters( 'wcfm_order_details_tax_line_item', true ) ) { ?>
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
											$invoice_total += (float)$tax_item_total;
											$tax_total += (float)$tax_item_total;
											echo wc_price( wc_round_tax_total( $tax_item_total ), array( 'currency' => $order->get_currency() ) );
										} else {
											echo '&ndash;';
										}
									?>
								</div>
							</td>
							<?php
						}
					}
				?>
			<?php } ?>
		</tr>
		<?php endforeach; ?>
	</tbody>
	
	<?php if( apply_filters( 'wcfm_order_details_shipping_line_item', true ) ) { ?>
	<tbody id="order_shipping_line_items">
	<?php
		$shipping_amt = 0;
		$shipping_tax = 0;
		$shipping_methods = WC()->shipping() ? WC()->shipping->load_shipping_methods() : array();
		foreach ( $line_items_shipping as $item_id => $item ) {
			?>
			<tr class="shipping <?php echo ( ! empty( $class ) ) ? $class : ''; ?>" data-order_item_id="<?php echo $item_id; ?>">
				<td class="name" style="width:30%">
					<div class="view">
						<?php echo ! empty( $item->get_name() ) ? wc_clean( $item->get_name() ) : __( 'Shipping', 'wc-frontend-manager-ultimate' ); ?>
					</div>
			
					<?php do_action( 'woocommerce_before_order_itemmeta', $item_id, $item, null ) ?>
					<div class="view">
						<?php
						wc_display_item_meta( $item );
						?>
					</div>
					<?php do_action( 'woocommerce_after_order_itemmeta', $item_id, $item, null ) ?>
				</td>
				
				<td class="no-borders"></td>
				<td class="no-borders"></td>
			
				<?php do_action( 'woocommerce_admin_order_item_values', null, $item, absint( $item_id ) ); ?>
				
				<td class="line_cost">
					<div class="view">
						<?php
							$shipping_amt += wc_round_tax_total( $item['cost'] );
							echo ( isset( $item['cost'] ) ) ? wc_price( wc_round_tax_total( $item['cost'] ), array( 'currency' => $order->get_currency() ) ) : '';
			
							if ( $refunded = $order->get_total_refunded_for_item( $item_id, 'shipping' ) ) {
								echo '<small class="refunded">-' . wc_price( $refunded, array( 'currency' => $order->get_currency() ) ) . '</small>';
							}
						?>
					</div>
				</td>
			
				<?php
					if ( ( $tax_data = $item->get_taxes() ) && wc_tax_enabled() ) {
						foreach ( $order_taxes as $tax_item ) {
							$tax_item_id    = $tax_item->get_rate_id();
							$tax_item_total = isset( $tax_data['total'][ $tax_item_id ] ) ? $tax_data['total'][ $tax_item_id ] : '';
							?>
								<td class="line_tax no_ipad no_mob" >
									<div class="view">
										<?php
											$shipping_tax += wc_round_tax_total( $tax_item_total );
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
			</tr>
			<?php
		}
		do_action( 'woocommerce_admin_order_items_after_shipping', $order->get_id() );
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
						<?php $document->shipping_notes(); ?>
					<?php endif; ?>
					<?php do_action( 'wcfm_pdf_invoice_after_customer_notes', $document_type, $order ); ?>
				</div>				
			</td>
		  
			<td class="no-borders" style="width:40%">
				<table class="totals">
					<tfoot>
				
						<?php if ( wc_tax_enabled() ) : ?>
							<tr>
								<th class="label description" colspan="2" style="text-align:left;"><?php _e( 'Tax', 'wc-frontend-manager-ultimate' ); ?>:</th>
								<td class="total price" style="text-align:center;"><?php
									echo wc_price( $tax_total, array( 'currency' => $order->get_currency() ) );
								?></td>
							</tr>
						<?php endif; ?>
						
						<?php if( $order->get_formatted_shipping_address() ) { ?>
							<tr>
								<th class="label description" colspan="2" style="text-align:left;"><?php _e( 'Shipping', 'wc-frontend-manager-ultimate' ); ?>:</th>
								<td class="total price" style="text-align:center;">
									<?php
									if( wcfm_is_marketplace() == 'wcfmmarketplace' ) {
										$invoice_total += $shipping_amt;
										echo wc_price( $shipping_amt, array( 'currency' => $order->get_currency() ) );
									} else {		
										$invoice_total += (float)$order->get_total_shipping();
										echo wc_price( $order->get_total_shipping(), array( 'currency' => $order->get_currency() ) );
									}
								?></td>
							</tr>
							
							<tr>
								<th class="label description" colspan="2" style="text-align:left;"><?php _e( 'Shipping Tax', 'wc-frontend-manager-ultimate' ); ?>:</th>
								<td class="total price" style="text-align:center;">
									<?php
									if( wcfm_is_marketplace() == 'wcfmmarketplace' ) {
										$invoice_total += $shipping_tax;
										echo wc_price( $shipping_tax, array( 'currency' => $order->get_currency() ) );
									} else {
										$invoice_total += (float)$order->get_shipping_tax();
										echo wc_price( $order->get_shipping_tax(), array( 'currency' => $order->get_currency() ) );
									}
								?></td>
							</tr>
						<?php } ?>
				
						<tr>
							<th class="label description" colspan="2" style="text-align:left;"><?php _e( 'Total', 'wc-frontend-manager-ultimate' ); ?>:</th>
							<td class="total price" style="text-align:center;">
								<div class="view"><?php echo wc_price( $invoice_total, array( 'currency' => $order->get_currency() ) ); ?></div>
							</td>
						</tr>
				
					</tfoot>
				</table>
			</td>
		</tr>
	</tbody>
</table>

<?php do_action( 'wpo_wcpdf_before_order_details', $document_type, $order ); ?>
<?php do_action( 'wcfm_packing_slip_before_order_details', $document_type, $order ); ?>
<?php 
if( apply_filters( 'wcfm_is_allow_order_details_after_order_table', false ) ) { 
  echo '<table class="order-details"><tbody><tr><td>'; do_action('woocommerce_order_details_after_order_table', $order ); echo '</td></tr></tbody></table>'; 
} else { ?>

	<?php
	$vendor_id = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );
	
	$wcfm_vendor_invoice_options = get_option( 'wcfm_vendor_invoice_options', array() );
	$wcfm_vendor_invoice_active = isset( $wcfm_vendor_invoice_options['enable'] ) ? 'yes' : '';
	$wcfm_vendor_invoice_logo = isset( $wcfm_vendor_invoice_options['logo'] ) ? 'yes' : '';
	$wcfm_vendor_invoice_store = isset( $wcfm_vendor_invoice_options['store'] ) ? 'yes' : '';
	$wcfm_vendor_invoice_address = isset( $wcfm_vendor_invoice_options['address'] ) ? 'yes' : '';
	$wcfm_vendor_invoice_policies = isset( $wcfm_vendor_invoice_options['policies'] ) ? 'yes' : '';
	$wcfm_vendor_invoice_disclaimer = isset( $wcfm_vendor_invoice_options['disclaimer'] ) ? 'yes' : '';
	$wcfm_vendor_invoice_signature = isset( $wcfm_vendor_invoice_options['signature'] ) ? 'yes' : '';
	$wcfm_vendor_invoice_fields = isset( $wcfm_vendor_invoice_options['fields'] ) ? $wcfm_vendor_invoice_options['fields'] : array();
	$wcfm_vendor_invoice_data = (array) wcfm_get_user_meta( $vendor_id, 'wcfm_vendor_invoice_options', true );
	$wcfm_vendor_disclaimer = isset( $wcfm_vendor_invoice_data['disclaimer'] ) ? $wcfm_vendor_invoice_data['disclaimer'] : '';
	$wcfm_vendor_signature = isset( $wcfm_vendor_invoice_data['signature'] ) ? $wcfm_vendor_invoice_data['signature'] : '';
	
	if( apply_filters( 'wcfm_is_pref_policies', true ) && apply_filters( 'wcfm_is_packing_slip_policies', true ) && $WCFM->wcfm_vendor_support->wcfm_vendor_has_capability( $vendor_id, 'policy' ) && $wcfm_vendor_invoice_policies ) {
		$shipping_policy     = $WCFM->wcfm_policy->get_shipping_policy( 1 );
		$refund_policy       = $WCFM->wcfm_policy->get_refund_policy( 1 );
		$cancellation_policy = $WCFM->wcfm_policy->get_cancellation_policy( 1 );
		if( !wcfm_empty($shipping_policy) || !wcfm_empty($refund_policy) || !wcfm_empty($cancellation_policy) ) {
			?>
			<br/><br/>
			<table width="100%" style="width:100%;">
				<tbody>
					<?php if( !wcfm_empty($shipping_policy) ) { ?>
						<tr>
							<td colspan="3" style="background-color: #eeeeee;padding: 1em 1.41575em;line-height: 1.5;"><?php echo apply_filters('wcfm_shipping_policies_heading', __('Shipping Policy', 'wc-frontend-manager')); ?></td>
							<td colspan="5" style="background-color: #f8f8f8;padding: 1em;"><?php echo $shipping_policy; ?></td>
						</tr>
					<?php } ?>
					<?php if( !wcfm_empty($refund_policy) ) { ?>
						<tr>
							<td colspan="3" style="background-color: #eeeeee;padding: 1em 1.41575em;line-height: 1.5;"><?php echo apply_filters('wcfm_refund_policies_heading', __('Refund Policy', 'wc-frontend-manager')); ?></td>
							<td colspan="5" style="background-color: #f8f8f8;padding: 1em;"><?php echo $refund_policy; ?></td>
						</tr>
					<?php } ?>
					<?php if( !wcfm_empty($cancellation_policy) ) { ?>
						<tr>
							<td colspan="3" style="background-color: #eeeeee;padding: 1em 1.41575em;line-height: 1.5;"><?php echo apply_filters('wcfm_cancellation_policies_heading', __('Cancellation / Return / Exchange Policy', 'wc-frontend-manager')); ?></td>
							<td colspan="5" style="background-color: #f8f8f8;padding: 1em;"><?php echo $cancellation_policy; ?></td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
			<br/><br/>
			<?php
		}
	}
	?>

<?php } ?>


<?php do_action( 'wcfm_packing_slip_after_order_details', $document_type, $order ); ?>

<?php if ( $document->get_footer() ): ?>
<div id="footer">
	<?php $document->footer(); ?>
</div><!-- #letter-footer -->
<?php endif; ?>
<?php do_action( 'wcfm_packing_slip_after_document', $document_type, $order ); ?>
