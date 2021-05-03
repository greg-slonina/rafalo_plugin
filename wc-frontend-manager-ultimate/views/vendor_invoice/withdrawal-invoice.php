<?php 
global $WCFM, $WCFMu, $WCFMmp, $wpo_wcpdf, $withdrawal_id, $document, $document_type, $wpdb; 

$transaction_id = $withdrawal_id;

$transaction = '';
$transaction_metas = array();
$paid_amount = 0;
$vendor_id = 0;
$pay_currency = get_woocommerce_currency(); 

$sql = 'SELECT * FROM ' . $wpdb->prefix . 'wcfm_marketplace_withdraw_request';
$sql .= ' WHERE 1=1';
$sql .= " AND ID = " . $transaction_id;
$withdrawal_infos = $wpdb->get_results( $sql );
if( !empty( $withdrawal_infos ) ) {
	foreach( $withdrawal_infos as $withdrawal_info ) {
		$transaction = $withdrawal_info;
	}
}

$paid_amount = (float)$transaction->withdraw_amount - (float)$transaction->withdraw_charges;
$vendor_id   = $transaction->vendor_id;

$sql = 'SELECT * FROM ' . $wpdb->prefix . 'wcfm_marketplace_withdraw_request_meta';
$sql .= ' WHERE 1=1';
$sql .= " AND `withdraw_id` = " . $transaction_id;
$withdrawal_metas = $wpdb->get_results( $sql );
if( !empty( $withdrawal_metas ) ) {
	foreach( $withdrawal_metas as $withdrawal_meta ) {
		if( in_array( $withdrawal_meta->key, array('sender_batch_id') ) ) continue;
		if( $withdrawal_meta->key == 'withdraw_amount' ) {
			$paid_amount = $withdrawal_meta->value;
		} elseif( $withdrawal_meta->key == 'currency' ) {
			$pay_currency = $withdrawal_meta->value;
		} else {
			$transaction_metas[$withdrawal_meta->key] = $withdrawal_meta->value;
		}
	}
}

if ( WC()->payment_gateways() ) {
	$payment_gateways = WC()->payment_gateways->payment_gateways();
} else {
	$payment_gateways = array();
}

?>

<table class="head container">
	<tr>
		<td class="header">
		<?php
		if( $document->has_header_logo() ) {
			$document->header_logo();
		} else {
			echo apply_filters( 'wcfm_withdrawal_invoice_invoice_title', __( 'Withdrawal Invoice', 'wc-frontend-manager-ultimate' ) );
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
<?php if( $document->has_header_logo() ) echo apply_filters( 'wcfm_withdrawal_invoice_invoice_title', __( 'Withdrawal Invoice', 'wc-frontend-manager-ultimate' ) ); ?>
</h1>

<table class="order-data-addresses">
	<tr>
		<td class="address billing-address">
			<div class="vendor-shop-name"><h3><?php echo wcfm_get_vendor_store_name( $vendor_id ); ?></h3></div>
			<div class="vendor-shop-address"><?php echo wcfm_get_vendor_store_address_by_vendor( $vendor_id ); ?></div>
			<div class="vendor-shop-email"><?php echo wcfm_get_vendor_store_email_by_vendor( $vendor_id ); ?></div>
			<div class="vendor-shop-phone"><?php echo wcfm_get_vendor_store_phone_by_vendor( $vendor_id ); ?></div>
			<?php do_action( 'wcfm_withdrawal_invoice_after_vendor_details', $vendor_id ); ?>
			<?php
			$wcfm_vendor_invoice_options = get_option( 'wcfm_vendor_invoice_options', array() );
			$wcfm_vendor_invoice_fields = isset( $wcfm_vendor_invoice_options['fields'] ) ? $wcfm_vendor_invoice_options['fields'] : array();
			$wcfm_vendor_invoice_data = (array) wcfm_get_user_meta( $vendor_id, 'wcfm_vendor_invoice_options', true );
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
		<td class="address shipping-address">
		</td>
		<td class="order-data">
			<table>
				<tr class="order-number">
					<th><?php _e( 'Invoice Number:', 'wc-frontend-manager-ultimate' ); ?></th>
					<td><?php echo __( 'payment-invoice', 'wc-frontend-manager-ultimate' ) . '-' . $vendor_id . '-' . sprintf( '%06u', $transaction_id ); ?></td>
				</tr>
				<tr class="order-date">
					<th><?php _e( 'Invoice Date:', 'wc-frontend-manager-ultimate' ); ?></th>
					<td>
					  <?php 
					  echo date_i18n( wc_date_format(), strtotime( $transaction->created ) ); ?> @<?php echo date_i18n( wc_time_format(), strtotime( $transaction->created ) );
					  ?>
					</td>
				</tr>
				<tr class="payment-method">
					<th><?php _e( 'Payment Method:', 'wc-frontend-manager-ultimate' ); ?></th>
					<td>
					  <?php 
						$wcfm_marketplace_withdrwal_payment_methods = get_wcfm_marketplace_withdrwal_payment_methods();
						if ( isset( $wcfm_marketplace_withdrwal_payment_methods[$transaction->payment_method] ) ) {
							_e( $wcfm_marketplace_withdrwal_payment_methods[$transaction->payment_method], 'wc-frontend-manager' );
						} else {
							echo ( isset( $payment_gateways[ $transaction->payment_method ] ) ? esc_html( $payment_gateways[ $transaction->payment_method ]->get_title() ) : esc_html( $transaction->payment_method ) );
						} 
					?>
				  </td>
			  </tr>
			  <tr class="payment-method">
			    <th><?php _e( 'Pay Mode:', 'wc-frontend-manager-ultimate' ); ?></th>
				  <td>
						<?php 
						if( $transaction->is_auto_withdrawal ) {
							_e( 'Auto Withdrawal', 'wc-frontend-manager' ) . "<br/>";
						} else {
							if( $transaction->withdraw_mode == 'by_paymode' ) {
								_e( 'By Payment Type', 'wc-frontend-manager' );
							} elseif( $transaction->withdraw_mode == 'by_request' ) {
								_e( 'By Request', 'wc-frontend-manager' );
							} elseif( $transaction->withdraw_mode == 'by_auto_request' ) {
								_e( 'By Auto Request', 'wc-frontend-manager' );
							} elseif( $transaction->withdraw_mode == 'by_split_pay' ) {
								 _e( 'By Split Pay', 'wc-frontend-manager' );
							}
						} 
						?>
					</td>
				</tr>
				</tr>
				<tr class="payment-method">
					<th><?php _e( 'Payment Status:', 'wc-frontend-manager-ultimate' ); ?></th>
					<td>
					  <?php 
					  if( $transaction->withdraw_status == 'completed' ) {
					  	echo '<div style="background:#4dbd74;color:#ffffff;padding:10px;font-size:18px;font-weight:800;">';
					  	_e( 'Paid', 'wc-frontend-manager' );
					  } else {
					  	echo '<div style="background:#f86c6b;color:#ffffff;padding:10px;font-size:18px;font-weight:800;">';
					  	_e( 'Pending', 'wc-frontend-manager' );
					  }
					  echo '</div>';
					  ?>
					</td>
				</tr>
				<?php if( $transaction->withdraw_status == 'completed' ) { ?>
					<tr class="payment-method">
						<th><?php _e( 'Payment Date:', 'wc-frontend-manager-ultimate' ); ?></th>
						<td><?php echo date_i18n( wc_date_format(), strtotime( $transaction->withdraw_paid_date ) ); ?> @<?php echo date_i18n( wc_time_format(), strtotime( $transaction->withdraw_paid_date ) ); ?></td>
					</tr>
				<?php } ?>
			</table>			
		</td>
	</tr>
</table>

<table class="order-details">
	<thead>
		<tr>
			<th colspan="4"><?php _e( 'Specification', 'wc-frontend-manager-ultimate' ); ?></th>
			<th style="text-align:center;"><?php _e( 'Item Total', 'wc-frontend-manager-ultimate' ); ?></th>
			<?php if ( wc_tax_enabled() ) { ?>
			<th style="text-align:center;"><?php _e( 'Tax', 'wc-frontend-manager-ultimate' ); ?></th>
			<?php } ?>
			<th style="text-align:center;"><?php _e( 'Shipping', 'wc-frontend-manager-ultimate' ); ?></th>
			<?php if ( wc_tax_enabled() ) { ?>
			  <th style="text-align:center;"><?php _e( 'Shipping Tax', 'wc-frontend-manager-ultimate' ); ?></th>
			<?php } ?>
			<th style="text-align:center;"><?php _e( 'Earning', 'wc-frontend-manager-ultimate' ); ?></th>
		</tr>
	</thead>
	<tbody>
	  <?php
	  $item_total           = 0;
	  $tax_total            = 0;
	  $total_shipping       = 0;
	  $total_shipping_tax   = 0;
	  $total_commission_tax = 0;
	  $total_aff_commission = 0;
	  $total_trans_charge   = 0;
	  $total_commission     = 0;
	  $commission_rule      = array();
	  $wi_commission_ids  = explode( ",", $transaction->commission_ids );
	  if( !empty( $wi_commission_ids ) ) {
	  	foreach( $wi_commission_ids as $wi_commission_id ) {
				$sql = 'SELECT order_id, GROUP_CONCAT(item_id) order_item_ids, GROUP_CONCAT(product_id) product_id, GROUP_CONCAT( commission.quantity ) AS order_item_count, COALESCE( SUM( commission.item_total ), 0 ) AS item_total, COALESCE( SUM( commission.item_sub_total ), 0 ) AS item_sub_total, COALESCE( SUM( commission.shipping ), 0 ) AS shipping, COALESCE( SUM( commission.tax ), 0 ) AS tax, COALESCE( SUM( commission.shipping_tax_amount ), 0 ) AS shipping_tax_amount, COALESCE( SUM( commission.total_commission ), 0 ) AS total_commission FROM ' . $wpdb->prefix . 'wcfm_marketplace_orders AS commission';
				$sql .= ' WHERE 1=1';
				$sql .= " AND `vendor_id` = {$vendor_id}";
				$sql .= " AND `ID`  = ({$wi_commission_id})";
				$orders_details = $wpdb->get_results( $sql );
				
				$commission_rule         = unserialize( $WCFMmp->wcfmmp_commission->wcfmmp_get_commission_meta( $wi_commission_id, 'commission_rule' ) );
				$commission_tax          = (float) $WCFMmp->wcfmmp_commission->wcfmmp_get_commission_meta( $wi_commission_id, 'commission_tax' );
				$total_commission_tax   += $commission_tax;
				
				$aff_commission          = (float) $WCFMmp->wcfmmp_commission->wcfmmp_get_commission_meta( $wi_commission_id, '_wcfm_affiliate_commission' );
				$total_aff_commission   += $aff_commission;
				
				$transaction_charge      = (float) $WCFMmp->wcfmmp_commission->wcfmmp_get_commission_meta( $wi_commission_id, 'transaction_charge' );
				$total_trans_charge     += $transaction_charge;
				
				if( !empty( $orders_details ) ) {
					foreach( $orders_details as $order_details ) {
						$the_order = wc_get_order( $order_details->order_id );
						if( !is_a( $the_order, 'WC_Order' ) ) continue;
						
						$order_currency = $the_order->get_currency();
						
						$order_item_details = '';
						$order_item_ids = explode( ",", $order_details->order_item_ids );
						try {
							foreach( $order_item_ids as $order_item_id ) {
								$line_item = new WC_Order_Item_Product( $order_item_id );
								
								// Refunded Items Skipping
								if( $refunded_qty = $the_order->get_qty_refunded_for_item( absint( $order_item_id ) ) ) {
									$refunded_qty = $refunded_qty * -1;
									if( $line_item->get_quantity() == $refunded_qty ) {
										continue;
									}
								}
								
								$product   = $line_item->get_product();
								$item_meta_html = strip_tags( wc_display_item_meta( $line_item, array(
																																							'before'    => "\n- ",
																																							'separator' => "\n- ",
																																							'after'     => "",
																																							'echo'      => false,
																																							'autop'     => false,
																																						) ) );
						
								$order_item_details .= '<div class=""><span class="qty">' . $line_item->get_quantity() . '&nbsp;x&nbsp;</span><span class="name">' . $line_item->get_name();
								if ( $product && $product->get_sku() ) {
									$order_item_details .= ' (' . __( 'SKU:', 'wc-frontend-manager' ) . ' ' . esc_html( $product->get_sku() ) . ')';
								}
								//if ( ! empty( $item_meta_html ) && apply_filters( 'wcfm_is_allow_order_list_item_meta', false ) ) $order_item_details .= '<br />(' . $item_meta_html . ')';
								$order_item_details .= '</span></div>';
							}
						} catch (Exception $e) {
							continue;
						}
					?>
					<tr>
						<td colspan="4">
							<?php
							if( $order_item_details ) {
								$item_total         += $order_details->item_total;
								$tax_total          += $order_details->tax;
								$total_shipping     += $order_details->shipping;
								$total_shipping_tax += $order_details->shipping_tax_amount;
								$total_commission   += $order_details->total_commission;
								?><h3><?php _e( 'Order #', 'wc-frontend-manager' ); echo esc_attr( $the_order->get_order_number() ); ?></h3><?php
								$order_item_details = '<div class="order_items order_items_visible" cellspacing="0">' . $order_item_details . '</div>';
								echo $order_item_details;
							}
							?>
						</td>
						<td style="text-align:center;"><?php echo wc_price( $order_details->item_total, array( 'currency' => $order_currency ) ); ?></td>
						<?php if ( wc_tax_enabled() ) { ?>
						<td style="text-align:center;"><?php echo wc_price( $order_details->tax, array( 'currency' => $order_currency ) ); ?></td>
						<?php } ?>
						<td style="text-align:center;"><?php echo wc_price( $order_details->shipping, array( 'currency' => $order_currency ) ); ?></td>
						<?php if ( wc_tax_enabled() ) { ?>
							<td style="text-align:center;"><?php echo wc_price( $order_details->shipping_tax_amount, array( 'currency' => $order_currency ) ); ?></td>
						<?php } ?>
						<td style="text-align:center;"><?php echo wc_price( ( $commission_tax + $transaction_charge + $aff_commission + $order_details->total_commission ), array( 'currency' => $order_currency ) ); ?></td>
					</tr>
					<?php
					}
				}
			}
		}
		?>
	</tbody>
</table>

<table style="width:100%;">
	<tbody class="no-borders">
		<tr class="no-borders">
		  <td style="width:40%;">
		  </td>
		  <td style="width:60%;">
				<table style="width:100%;">
					<tbody class="no-borders" style="float:right;">
					  <tr class="no-borders">
							<td style="text-align:right;float:right;"><?php _e( 'Line Total', 'wc-frontend-manager' ); ?></td>
							<td style="text-align:right;float:right;"><?php echo wc_price( $item_total, array( 'currency' => $pay_currency ) ); ?></td>
						</tr>
						<?php if ( wc_tax_enabled() ) { ?>
							<tr class="no-borders">
								<td style="text-align:right;float:right;"><?php _e( 'Tax', 'wc-frontend-manager' ); ?></td>
								<td style="text-align:right;float:right;"><?php echo wc_price( $tax_total, array( 'currency' => $pay_currency ) ); ?></td>
							</tr>
						<?php } ?>
						<tr class="no-borders">
							<td style="text-align:right;float:right;"><?php _e( 'Shipping', 'wc-frontend-manager' ); ?></td>
							<td style="text-align:right;float:right;"><?php echo wc_price( $total_shipping, array( 'currency' => $pay_currency ) ); ?></td>
						</tr>
						<?php if ( wc_tax_enabled() ) { ?>
							<tr class="no-borders">
								<td style="text-align:right;float:right;"><?php _e( 'Shipping Tax', 'wc-frontend-manager' ); ?></td>
								<td style="text-align:right;float:right;"><?php echo wc_price( $total_shipping_tax, array( 'currency' => $pay_currency ) ); ?></td>
							</tr>
						<?php } ?>
						
						<tr class="no-borders">
							<td style="text-align:right;float:right;padding-top:10px;"><?php _e( 'Gross Earning', 'wc-frontend-manager' ); ?></td>
							<td style="text-align:right;float:right;padding-top:10px;"><?php echo wc_price( ( $total_commission_tax + $total_trans_charge + $total_aff_commission + $total_commission ), array( 'currency' => $pay_currency ) ); ?></td>
						</tr>
						
						<?php if( $total_trans_charge ) { ?>
							<tr class="no-borders">
								<td style="text-align:right;float:right;padding-top:10px;"><?php _e( 'Transaction Charge', 'wc-frontend-manager' ); ?></td>
								<td style="text-align:right;float:right;padding-top:10px;"><?php echo '-' . wc_price( $total_trans_charge, array( 'currency' => $pay_currency ) ); ?></td>
							</tr>
						<?php } ?>
						
						<?php if( $total_aff_commission ) { ?>
							<tr class="no-borders">
								<td style="text-align:right;float:right;padding-top:10px;"><?php _e( 'Affiliate Commissiion', 'wc-frontend-manager' ); ?></td>
								<td style="text-align:right;float:right;padding-top:10px;"><?php echo '-' . wc_price( $total_aff_commission, array( 'currency' => $pay_currency ) ); ?></td>
							</tr>
						<?php } ?>
						
						<?php if( isset( $commission_rule['tax_enable'] ) && ( $commission_rule['tax_enable'] == 'yes' ) ) { ?>
							<tr class="no-borders">
								<td style="text-align:right;float:right;padding-top:10px;"><?php echo $commission_rule['tax_name'] . ' ('. $commission_rule['tax_percent'] .'%)'; ?></td>
								<td style="text-align:right;float:right;padding-top:10px;"><?php echo '-' . wc_price( $total_commission_tax, array( 'currency' => $pay_currency ) ); ?></td>
							</tr>
						<?php } ?>
						
						<tr class="no-borders">
							<td style="text-align:right;float:right;padding-top:10px;"><?php _e( 'Total Earning', 'wc-frontend-manager' ); ?></td>
							<td style="text-align:right;float:right;padding-top:10px;"><?php echo wc_price( $total_commission, array( 'currency' => $pay_currency ) ); ?></td>
						</tr>
						<tr class="no-borders">
							<td style="text-align:right;"><?php _e( 'Withdrawal Charges', 'wc-frontend-manager' ); ?></td>
							<td style="text-align:right;"><?php echo wc_price( $transaction->withdraw_charges, array( 'currency' => $pay_currency ) ); ?></td>
						</tr>
						<tr class="no-borders">
							<td style="text-align:right;font-weight:400;font-size:18px;padding-top:10px;vertical-align:middle;"><?php if( $transaction->withdraw_status == 'completed' ) { _e( 'Paid Amount', 'wc-frontend-manager' ); } else { _e( 'Payable Amount', 'wc-frontend-manager' ); } ?></td>
							<td style="text-align:right;font-weight:400;font-size:18px;padding-top:10px;"><strong>
							<?php 
							$paid_amount = (float)$total_commission - (float)$transaction->withdraw_charges;
							echo wc_price( $paid_amount, array( 'currency' => $pay_currency ) ); 
							if( apply_filters( 'wcfm_is_allow_earning_in_words', false ) ) {
								echo "<br/><small style='text-align:right;font-weight:200;font-size:14px;'>" . wcfm_number_to_words($paid_amount) . "</small>";
							}
							?>
							</strong></td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table>

<?php do_action( 'wcfm_withdrawal_invoice_after_order_details', $document_type ); ?>

<?php if ( $document->get_footer() ): ?>
<div id="footer" style="width: 100%;display:block;">
	<?php $document->footer(); ?>
</div><!-- #letter-footer -->
<?php endif; ?>
<?php do_action( 'wcfm_withdrawal_invoice_after_document', $document_type ); ?>