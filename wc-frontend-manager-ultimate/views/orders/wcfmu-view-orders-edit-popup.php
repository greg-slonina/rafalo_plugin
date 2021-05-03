<?php
/**
 * WCFM plugin view
 *
 * WCfM Edit Order popup View
 *
 * @author 		WC Lovers
 * @package 	wcfmu/views/orders
 * @version   5.2.1
 */
 
global $wp, $WCFM, $WCFMmp, $_POST, $wpdb;

if( !$order_id ) return;

$order                  = wc_get_order( $order_id );

$order_taxes            = $order->get_taxes();
$currency               = $order->get_currency();

$line_items             = $order->get_items( 'line_item' );
$line_items             = apply_filters( 'wcfm_valid_line_items', $line_items, $order_id );

$product_items          = array();
foreach ( $line_items as $item_id => $item ) {
	$order_item_id = $item->get_id();
	
	$refunded_amount = $order->get_total_refunded_for_item( $order_item_id );
	$refunded_qty    = $order->get_qty_refunded_for_item( $order_item_id );
	if( $refunded_qty ) $refunded_qty = ( $refunded_qty * -1 );
	
	$product_items[$order_item_id] = array( 'name' => $item->get_name(), 'cost' => $order->get_item_subtotal( $item, false, true ), 'qty' => ( $item->get_quantity() - $refunded_qty ), 'total' => ( $item->get_total() - $refunded_amount ), 'tax' => $item->get_taxes() );
}
?>

<div class="wcfm-clearfix"></div><br />
<div id="wcfm_order_edit_form_wrapper">
	<form action="" method="post" id="wcfm_order_edit_form" class="order_edit-form wcfm_popup_wrapper" novalidate="">
		<div style="margin-bottom: 15px;"><h2 style="float: none;"><?php echo __( 'Order Edit', 'wc-frontend-manager-ultimate' ) . ' #' . $order_id; ?></h2></div>
		
		<?php if( !empty( $product_items ) ) { ?>
			<table cellpadding="0" cellspacing="0" class="woocommerce_order_items wcfm_refund_items_ele">
				<thead>
					<tr>
						<th class="item sortable" data-sort="string-ins"><?php _e( 'Item', 'wc-frontend-manager' ); ?></th>
						<th class="item_cost sortable no_mob" data-sort="float" style="text-align:center;"><?php _e( 'Cost', 'wc-frontend-manager' ); ?></th>
						<th class="item_quantity wcfm_item_qty_heading sortable" data-sort="int" style="text-align:center;"><?php _e( 'Qty', 'wc-frontend-manager' ); ?></th>
						<th class="line_cost sortable" data-sort="float" style="text-align:center;"><?php _e( 'Total', 'wc-frontend-manager' ); ?></th>
						<?php
							if ( wc_tax_enabled() && ! empty( $order_taxes ) ) :
								foreach ( $order_taxes as $tax_id => $tax_item ) :
									$column_label   = ! empty( $tax_item['label'] ) ? $tax_item['label'] : __( 'Tax', 'wc-frontend-manager' );
									?>
									<th class="line_tax text_tip no_ipad no_mob" style="display:none;text-align:center;">
										<?php echo esc_attr( $column_label ); ?>
									</th>
									<?php
								endforeach;
							endif;
						?>
					</tr>
				</thead>
				<tbody id="order_line_items">
					<?php if( !empty( $product_items ) ) { ?>
						<?php foreach( $product_items as $item_id => $product_item ) { ?>
							<tr class="order_line_item_<?php echo $item_id; ?>">
								<td class="item sortable" data-sort="string-ins">
								  <?php 
								  echo $product_item['name']; 
								  do_action( 'woocommerce_order_item_meta_start', $item_id, new WC_Order_Item_Product( $item_id ), $order, false );
									wc_display_item_meta( $item );
									do_action( 'woocommerce_order_item_meta_end', $item_id, new WC_Order_Item_Product( $item_id ), $order, false );
								  ?>
								</td>
								
								<td class="item_cost sortable no_mob" data-sort="float" style="text-align:center;"><?php echo wc_price( $product_item['cost'], array( 'currency' => $currency ) ); ?></td>
								
								<td class="item_quantity wcfm_item_qty_heading sortable" data-sort="int" style="text-align:center;">
								  <input type="number" class="wcfm_popup_input wcfm_order_edit_input_qty wcfm_order_edit_input_ele" value="<?php echo $product_item['qty']; ?>" data-item="<?php echo $item_id; ?>" data-item_cost="<?php echo round( ($product_item['total']/$product_item['qty'] ), 2 ); ?>" name="wcfm_order_edit_input[<?php echo $item_id; ?>][qty]" min="0" step="1"  />
								  <input type="hidden" value="<?php echo $item_id; ?>" name="wcfm_order_edit_input[<?php echo $item_id; ?>][item]">
								</td>
								
								<td class="line_cost sortable" data-sort="float" style="text-align:center;">
								  <input type="number" class="wcfm_popup_input wcfm_order_edit_input_total wcfm_order_edit_input_ele" name="wcfm_order_edit_input[<?php echo $item_id; ?>][total]" min="0" step="1" value="<?php echo $product_item['total']; ?>"  />
								</td>
								
								<?php
									if ( wc_tax_enabled() ) {
										$tax_data = $product_item['tax'];
										if ( ! empty( $tax_data ) ) {
											foreach ( $order_taxes as $tax_item ) {
												$tax_item_id       = $tax_item['rate_id'];
												$tax_item_total    = isset( $tax_data['total'][ $tax_item_id ] ) ? $tax_data['total'][ $tax_item_id ] : 0;
												$tax_item_subtotal = isset( $tax_data['subtotal'][ $tax_item_id ] ) ? $tax_data['subtotal'][ $tax_item_id ] : 0;
												$refunded = $order->get_tax_refunded_for_item( $item_id, $tax_item_id );
												$tax_cost = ( $tax_item_total - $refunded );
												?>
												<td class="line_tax no_ipad no_mob" style="display:none;text-align:center;">
													<div class="view">
														<?php
															if ( '' != $tax_item_total ) {
																?>
																<input type="number" class="wcfm_popup_input wcfm_order_edit_input_tax wcfm_order_edit_input_ele" data-item_tax="<?php echo round( ($tax_cost/$product_item['qty'] ), 2 ); ?>" value="<?php echo wc_round_tax_total( $tax_cost ); ?>" name="wcfm_order_edit_tax_input[<?php echo $item_id; ?>][<?php echo $tax_item_id; ?>]" min="0" step="1" />
																<?php
															} else {
																echo '&ndash;';
															}
														?>
													</div>
												</td>
												<?php
											}
										}
									}
								?>
							</tr>
						<?php } ?>
					<?php } ?>
				</tbody>
			</table>
		<?php } ?>
		
		<?php if( wc_coupons_enabled() && apply_filters( 'wcfm_orders_manage_discount', true ) ) { ?>
			<div class="wcfm_clearfix"></div>
			<p class="wcfm-order_edit-form-request-amount wcfm_popup_label">
				<strong for="wcfm_order_edit_discount_amount"><?php _e( 'Apply Discount', 'wc-frontend-manager-ultimate' ); ?></strong> 
			</p>
			<?php $WCFM->wcfm_fields->wcfm_generate_form_field( array( "wcfm_om_discount" => array( 'type' => 'number', 'attributes' => array( 'min' => '1', 'step' => '1' ), 'class' => 'wcfm-text wcfm_ele wcfm_non_negative_input wcfm-order_edit-form-discount-amount wcfm_popup_input', 'label_class' => 'wcfm_title', 'value' => '' ) ) ); ?>
		<?php } ?>
		
		<div class="wcfm_clearfix"></div>
		<p class="wcfm-order_edit-form-reason wcfm_popup_label">
			<strong for="comment"><?php _e( 'Note to Customer', 'wc-frontend-manager-ultimate' ); ?></strong>
		</p>
		<textarea id="wcfm_om_comments" name="wcfm_om_comments" class="wcfm_popup_input wcfm_popup_textarea"></textarea>
	
		<div class="wcfm_clearfix"></div>
		<div class="wcfm-message" tabindex="-1"></div>
		<div class="wcfm_clearfix"></div><br />
		
		<p class="form-submit">
			<input name="submit" type="submit" id="wcfm_order_edit_submit_button" class="submit wcfm_popup_button" value="<?php _e( 'Submit', 'wc-frontend-manager-ultimate' ); ?>"> 
			<input type="hidden" name="wcfm_order_edit_id" value="<?php echo $order_id; ?>" id="wcfm_order_edit_order_id">
		</p>	
	</form>
</div>
<div class="wcfm-clearfix"></div>