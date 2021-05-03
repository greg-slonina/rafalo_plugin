<?php
global $wp, $WCFM, $WCFMu, $wp_query;

$order_id = 0;
if( isset( $wp->query_vars['wcfm-orders-details'] ) && !empty( $wp->query_vars['wcfm-orders-details'] ) ) {
	$order_id = absint($wp->query_vars['wcfm-orders-details']);
} else {
	return;
}

if( !$order_id ) return;

$order = wc_get_order( $order_id );

if( !is_a( $order, 'WC_Order' ) ) return;

$order_status = sanitize_title( $order->get_status() );
$order_status = apply_filters( 'wcfm_current_order_status', $order_status, $order_id );

// WooCommerce Quotation - 6.2.7
if( apply_filters( 'wcfm_is_allow_wc_quotation', true ) ) {
	if( WCFMu_Dependencies::wcfm_wc_quotation_active_check() ) {
		global $post, $theorder;

		if ( !empty( $theorder ) ) {
				$order = $theorder;
				$status = $order_status;
		} else if ( $order = wc_get_order( $post ) ) {
				$status = $order_status;
		}

		?>
		<div class="wcfm-clearfix"></div>
		<br />
		<!-- collapsible -->
		<div class="page_collapsible orders_details_shipment" id="sm_order_wc_quotation_options"><?php _e('Qutation', 'wc-frontend-manager-ultimate'); ?><span></span></div>
		<div class="wcfm-container orders_details_wc_quotation_expander_container">
			<div id="orders_details_wc_quotation_expander" class="wcfm-content">
		    <form id="wcfm_wc_quotation_form" method="POST">
					<div id="woocommerce-quotation-addons" class="order_data_column">
							<?php
			
							do_action( 'adq_add_order_detail_before', $order_id );
			
							//Add Send Proposal button
							if ( !empty( $status ) ) :
									if ( $status == "proposal" ) {
											?>
											<p class="form-field">
													<input type="submit" value="<?php _e( 'Send Proposal', 'woocommerce-quotation' ) ?>"
																 name="send_proposal" class="button send_proposal button-primary wcfm_submit_button">
											</p>
											<div class="wcfm-clearfix"></div>
											<?php
									} elseif ( $status == "request" ) {
											?>
											<p class="form-field">
													<input type="submit" value="<?php _e( 'Create proposal', 'woocommerce-quotation' ) ?>"
																 name="create_proposal" class="button create_proposal button-primary wcfm_submit_button">
											</p>
											<div class="wcfm-clearfix"></div>
											<?php
									} elseif ( $status == "proposal-sent" ) {
											?>
											<p class="form-field">
													<input type="submit" value="<?php _e( 'Accept proposal', 'woocommerce-quotation' ) ?>"
																 name="accept_proposal" class="button accept_proposal button-primary wcfm_submit_button">
											</p>
											<div class="wcfm-clearfix"></div>
											<p class="form-field">
													<input type="submit" value="<?php _e( 'Reject proposal', 'woocommerce-quotation' ) ?>"
																 name="reject_proposal" class="button reject_proposal button-primary wcfm_submit_button">
											</p>
											<div class="wcfm-clearfix"></div>
											<?php
									}
							endif;
			
							//Related order/quotes                        
							$_order_id = get_post_meta( $order_id, '_order_id', true );
							$_quotation_id = get_post_meta( $order_id, '_quotation_id', true );
			
							if ( $_quotation_id && $_quotation_id != "" ) { ?>
									<p class="form-field">
											<a href="<?php echo admin_url( 'post.php?post=' . $_quotation_id . '&action=edit' ); ?>"><?php _e( 'Related quote proposal', 'woocommerce-quotation' ) ?></a>
									</p>
							<?php }
			
							/*** DEPRECATED since 2.4.0 ***/
							/* if( $_order_id && $_order_id != "" ) {  ?>
									<p class="form-field">
											<a href="<?php echo admin_url( 'post.php?post=' . $_order_id . '&action=edit' ); ?>"><?php _e('Related order','woocommerce-quotation') ?></a>
									</p>
							<?php } */
			
							$readonly = '';
							if ( $status != "proposal" && $status != "proposal-sent" ) {
									$readonly = 'readonly';
							}
			
							//Add custom fields
							$validity_date = get_post_meta( $order_id, '_validity_date', true );
							if ( $validity_date == "" ) {
									$validity_date = date( 'Y-m-d H:i:s', strtotime( "+" . get_option( 'adq_proposal_validity' ) . " day" ) ) . "\n";
							}
							$reminder_date = get_post_meta( $order_id, '_reminder_date', true );
							if ( $reminder_date == "" ) {
									$reminder_date = date( 'Y-m-d H:i:s', strtotime( "+" . get_option( 'adq_proposal_reminder' ) . " day" ) ) . "\n";
							}
			
							$additional_info = get_post_meta( $order_id, '_adq_additional_info', true );
							$additional_info = preg_replace( '/\<br(\s*)?\/?\>/i', '', $additional_info );
			
							if ($status == "request") { ?>
							<div style="display:none">
									<?php } ?>
			
									<p class="form-field form-field-wide">
											<p for="validity_date" class="wcfm_title"><strong><?php _e( 'Validity date:', 'woocommerce-quotation' ) ?></strong></p>
											<input <?php echo $readonly ?> type="text" class="date-picker-field<?php echo $readonly ?> "
																										 name="_validity_date" id="validity_date" maxlength="10"
																										 value="<?php echo date_i18n( 'Y-m-d', strtotime( $validity_date ) ); ?>"
																										 pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])"/>
											@<input <?php echo $readonly ?> type="text" class="hour"
																											placeholder="<?php _e( 'h', 'woocommerce-quotation' ) ?>"
																											name="_validity_date_hour" id="validity_date_hour" maxlength="2"
																											size="2"
																											value="<?php echo date_i18n( 'H', strtotime( $validity_date ) ); ?>"
																											pattern="\-?\d+(\.\d{0,})?"/>
											:<input <?php echo $readonly ?> type="text" class="minute"
																											placeholder="<?php _e( 'm', 'woocommerce-quotation' ) ?>"
																											name="_validity_date_minute" id="validity_date_minute" maxlength="2"
																											size="2"
																											value="<?php echo date_i18n( 'i', strtotime( $validity_date ) ); ?>"
																											pattern="\-?\d+(\.\d{0,})?"/>
									</p>
									<p class="form-field form-field-wide">
											<p for="reminder_date" class="wcfm_title"><strong><?php _e( 'Reminder date:', 'woocommerce-quotation' ) ?></strong></p>
											<input <?php echo $readonly ?> type="text" class="date-picker-field<?php echo $readonly ?> "
																										 name="_reminder_date" id="reminder_date" maxlength="10"
																										 value="<?php echo date_i18n( 'Y-m-d', strtotime( $reminder_date ) ); ?>"
																										 pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])"/>
											@<input <?php echo $readonly ?> type="text" class="hour"
																											placeholder="<?php _e( 'h', 'woocommerce-quotation' ) ?>"
																											name="_reminder_date_hour" id="reminder_date_hour" maxlength="2"
																											size="2"
																											value="<?php echo date_i18n( 'H', strtotime( $reminder_date ) ); ?>"
																											pattern="\-?\d+(\.\d{0,})?"/>
											:<input <?php echo $readonly ?> type="text" class="minute"
																											placeholder="<?php _e( 'm', 'woocommerce-quotation' ) ?>"
																											name="_reminder_date_minute" id="reminder_date_minute" maxlength="2"
																											size="2"
																											value="<?php echo date_i18n( 'i', strtotime( $reminder_date ) ); ?>"
																											pattern="\-?\d+(\.\d{0,})?"/>
									</p>
									<p class="form-field form-field-wide">
											<p for="reminder_date" class="wcfm_title"><strong><?php _e( 'Additional information:', 'woocommerce-quotation' ) ?></strong></p>
											<textarea cols="25" rows="4" class="wcfm-textarea" name="_adq_additional_info"
																id="adq_additional_info" <?php echo $readonly ?> ><?php echo $additional_info; ?></textarea>
									</p>
									<div class="form-field form-field-wide downloadable_files">
											<?php
											$downloadable_files = get_post_meta( $order_id, '_attached_files', true );
											if( !$downloadable_files ) $downloadable_files = array();
											
											$WCFM->wcfm_fields->wcfm_generate_form_field( array( "wc_quotation_attached_files"  => array( 'label' => __( 'File(s)', 'wc-frontend-manager-ultimate'), 'type' => 'multiinput', 'class' => 'wcfm-text wcfm_ele wcfm_non_sortable', 'label_class' => 'wcfm_title', 'value' => array(), 'value' => $downloadable_files, 'options' => array(
																																																																				"name" => array('label' => __('Name', 'wc-frontend-manager'), 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_ele wcfm_title' ),
																																																																				"file" => array('label' => __('File', 'wc-frontend-manager'), 'type' => 'upload', 'mime' => 'Uploads', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_ele wcfm_title' ),
																																																																	 ), 'desc' => sprintf( __( 'Please upload any of these file types: %1$s', 'wc-frontend-manager' ), '<b style="color:#f86c6b;">' . implode( ', ', array_keys( wcfm_get_allowed_mime_types() ) ) . '</b>' ) )
																													) );
											?>
									</div>
									<?php
									if ($status == "request") { ?>
							</div>
					<?php } ?>
			
							<?php do_action( 'adq_add_order_detail_after', $order_id ) ?>
			
					</div>
					<input type="hidden" name="wc_quotation_order_id" value="<?php echo $order_id; ?>" />
				</form>
			</div>
		</div>
		<?php
	}
}



if( ( ( $order->needs_shipping_address() && $order->get_formatted_shipping_address() ) || apply_filters( 'wcfm_is_force_shipping_address', false ) ) && ( !function_exists( 'wcs_order_contains_subscription' ) || ( !wcs_order_contains_subscription( $order_id, 'renewal' ) && !wcs_order_contains_subscription( $order_id, 'renewal' ) ) ) && apply_filters( 'wcfm_is_pref_shipment_tracking', true ) && apply_filters( 'wcfm_is_allow_shipping_tracking', true ) && !in_array( $order_status, apply_filters( 'wcfm_shipment_disable_order_status', array( 'failed', 'cancelled', 'refunded', 'pending' ) ) ) ) {
	$needs_shipping_tracking = false; 
	$product_ids = array();
	$order_item_ids = array();
	?>
	<div class="wcfm-clearfix"></div>
	<br />
	<!-- collapsible -->
	<div class="page_collapsible orders_details_shipment" id="sm_order_shipment_options"><?php _e('Shipment Tracking', 'wc-frontend-manager-ultimate'); ?><span></span></div>
	<div class="wcfm-container orders_details_shipment_expander_container">
		<div id="orders_details_shipment_expander" class="wcfm-content">
		  <h2><?php _e( 'Mark item(s) as shipped and provide tracking information', 'wc-frontend-manager-ultimate' ); ?></h2>
		  <div class="wcfm-clearfix"></div>
		  <table cellpadding="0" cellspacing="0" class="woocommerce_order_items">
				<tbody id="order_line_items">
				<?php
				  $line_items = $order->get_items( 'line_item' );
					$line_items = apply_filters( 'wcfm_valid_line_items', $line_items, $order->get_id() );
					
					$shipped_action = 'wcfm_wcvendors_order_mark_shipped';
					$is_marketplace = wcfm_is_marketplace();
					if( $is_marketplace == 'wcvendors' ) $shipped_action = 'wcfm_wcvendors_order_mark_shipped';
					elseif( $is_marketplace == 'wcpvendors' ) $shipped_action = 'wcfm_wcpvendors_order_mark_fulfilled';
					elseif( $is_marketplace == 'wcmarketplace' ) $shipped_action = 'wcfm_wcmarketplace_order_mark_shipped';
					elseif( $is_marketplace == 'wcfmmarketplace' ) $shipped_action = 'wcfm_wcfmmarketplace_order_mark_shipped';
					elseif( $is_marketplace == 'dokan' ) $shipped_action = 'wcfm_dokan_order_mark_shipped';
					
					foreach ( $line_items as $item_id => $item ) {
						$_product  = $item->get_product();
						
						$needs_shipping = $WCFM->frontend->is_wcfm_needs_shipping( $_product );
						$shipped = true;
						$tracking_url  = '';
						$tracking_code = '';
						$delivery_boy  = '';
						if( $needs_shipping ) {
							$shipped = false;
							foreach ( $item->get_formatted_meta_data() as $meta_id => $meta ) {
								if( $meta->key == 'wcfm_tracking_url' ) {
									$tracking_url  = $meta->value;
									$shipped = true;
								} elseif( $meta->key == 'wcfm_tracking_code' ) {
									$tracking_code  = $meta->value;
								} elseif( $meta->key == 'wcfm_delivery_boy' ) {
									$delivery_boy  = $meta->value;
								}
							}
						} else {
							continue;
						}
						
						$order_item_ids[] = $item->get_id();
						$product_ids[]    = $item->get_product_id();
						
						//if( $shipped ) continue;
						$needs_shipping_tracking = true;
		
						if( apply_filters( 'wcfm_is_allow_edit_products', true ) && apply_filters( 'wcfm_is_allow_edit_specific_products', true, $item->get_product_id() ) ) {
							$product_link  = $_product ? get_wcfm_edit_product_url( $item->get_product_id(), $_product ) : '';
						} else {
							$product_link  = $_product ? get_permalink( $item->get_product_id() ) : '';
						}
						
						if( ( !empty( $product_ids ) && ( count( $product_ids ) == 1 ) ) || apply_filters( 'wcfm_is_allow_itemwise_notification', false ) ) {
							?>
							<tr class="item <?php echo apply_filters( 'woocommerce_admin_html_order_item_class', ( ! empty( $class ) ? $class : '' ), $item, $order ); ?>" data-order_item_id="<?php echo $item_id; ?>">
								<td class="name" data-sort-value="<?php echo esc_attr( $item->get_name() ); ?>">
									<?php
										echo $product_link ? '<a href="' . esc_url( $product_link ) . '" class="wc-order-item-name">' .  esc_html( $item->get_name() ) . '</a>' : '<div "class="wc-order-item-name"">' . esc_html( $item->get_name() ) . '</div>';
							
										if ( $_product && $_product->get_sku() ) {
											echo '<div class="wc-order-item-sku"><strong>' . __( 'SKU:', 'wc-frontend-manager' ) . '</strong> ' . esc_html( $_product->get_sku() ) . '</div>';
										}
										
										if ( $tracking_code ) {
											echo '<div class="wc-order-item-sku"><strong>' . __( 'Tracking Code', 'wc-frontend-manager-ultimate' ) . ':</strong> ' . $tracking_code . '</div>';
										}
										
										if ( $tracking_url ) {
											echo '<div class="wc-order-item-sku"><strong>' . __( 'Tracking URL', 'wc-frontend-manager-ultimate' ) . ':</strong> ' . $tracking_url . '</div>';
										}
										
										if ( $delivery_boy && function_exists( 'wcfm_get_delivery_boy_label' ) ) {
											$delivery_boy_label = '';
											$is_order_delivered = wcfm_is_order_delivered( $order_id, $item_id );
											if( $is_order_delivered ) {
												$delivery_boy_label = wcfm_get_delivery_boy_label( $delivery_boy, 'completed' );
											} else {
												$delivery_boy_label = wcfm_get_delivery_boy_label( $delivery_boy, 'pending' );
											}
											
											if( $delivery_boy_label ) {
												echo '<div class="wc-order-item-sku"><strong>' . __( 'Delivery Boy', 'wc-frontend-manager-ultimate' ) . ':</strong> ' . $delivery_boy_label . '</div>';
											}
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
								</td>
								<td>
									<?php if( $_product ) { ?>
										<a class="wcfm_order_mark_shipped" href="#" data-shipped_action="<?php echo $shipped_action; ?>" data-productid="<?php echo $_product->get_id(); ?>" data-orderitemid="<?php echo $item->get_id(); ?>" data-orderid="<?php echo $order_id; ?>"><span class="wcfmfa fa-truck text_tip" data-tip="<?php echo esc_attr__( 'Mark Shipped', 'wc-frontend-manager-ultimate' ); ?>"></span></a>
									<?php } ?>
								</td>
							</tr>
							<?php
						}
					}
					
					if( ( $is_marketplace == 'wcfmmarketplace' ) && !empty( $product_ids ) && ( count( $product_ids ) > 1 ) ) {
						?>
						<tr class="item">
						  <td class="item" style="border-top:2px solid #c1c1c1;">
						    <div class="wc-order-item-name"><?php _e( 'Update all items tracking info:', 'wc-frontend-manager-ultimate' ); ?></div>
						  </td>
						  <td style="border-top:2px solid #c1c1c1;">
								<a class="wcfm_order_mark_shipped" href="#" data-shipped_action="<?php echo $shipped_action; ?>" data-productid="<?php echo implode( ",", $product_ids ); ?>" data-orderitemid="<?php echo implode( ",", $order_item_ids ); ?>" data-orderid="<?php echo $order_id; ?>"><span class="wcfmfa fa-truck text_tip" data-tip="<?php echo esc_attr__( 'Mark Shipped', 'wc-frontend-manager-ultimate' ); ?>"></span></a>
							</td>
						</tr>
						<?php
					}
					?>
				</tbody>
		  </table>
		</div>
	</div>
	<?php
	if( !$needs_shipping_tracking ) {
		?>
		<style>
		#sm_order_shipment_options, .orders_details_shipment_expander_container, #orders_details_shipment_expander { display: none; }
		</style>
		<?php
	}
}

if( apply_filters( 'wcfm_allow_order_notes', true ) ) {
	?>
	<div class="wcfm-clearfix"></div>
	<br />
	<!-- collapsible -->
	<div class="page_collapsible orders_details_notes" id="wcfm_order_notes_options"><?php _e('Order Notes', 'wc-frontend-manager-ultimate'); ?><span></span></div>
	<div class="wcfm-container">
		<div id="orders_details_notes_expander" class="wcfm-content">
			<?php
				if( $view_view_order_notes = apply_filters( 'wcfm_view_order_notes', true ) ) {
					$args = array(
						'post_id'   => $wp->query_vars['wcfm-orders-details'],
						'orderby'   => 'comment_ID',
						'order'     => 'DESC',
						'approve'   => 'approve',
						'type'      => 'order_note'
					);
					
					$args = apply_filters( 'wcfm_order_notes_args', $args );
			
					remove_filter( 'comments_clauses', array( 'WC_Comments', 'exclude_order_comments' ), 10, 1 );
			
					$notes = apply_filters( 'wcfm_order_notes', get_comments( $args ), $wp->query_vars['wcfm-orders-details'] );
			
					add_filter( 'comments_clauses', array( 'WC_Comments', 'exclude_order_comments' ), 10, 1 );
			
					echo '<table id="notes_holder"><tbody>';
			
					if ( $notes ) {
			
						foreach( $notes as $note ) {
			
							$note_classes   = array( 'note' );
							$note_classes[] = get_comment_meta( $note->comment_ID, 'is_customer_note', true ) ? 'customer-note' : '';
							$note_classes[] = $note->comment_author === __( 'WooCommerce', 'wc-frontend-manager-ultimate' ) ? 'system-note' : '';
							$note_classes   = apply_filters( 'woocommerce_order_note_class', array_filter( $note_classes ), $note );
							?>
							<tr class="<?php echo esc_attr( implode( ' ', $note_classes ) ); ?>">
								<td>
									<?php echo wpautop( wptexturize( wp_kses_post( $note->comment_content ) ) ); ?>
								</td>
								<td>
									<span style="cursor: help;" class="exact-date text_tip" data-tip="<?php echo $note->comment_date; ?>"><?php printf( __( 'added on %1$s at %2$s', 'wc-frontend-manager-ultimate' ), date_i18n( wc_date_format(), strtotime( $note->comment_date ) ), date_i18n( wc_time_format(), strtotime( $note->comment_date ) ) ); ?></span>
									<?php if ( $note->comment_author !== __( 'WooCommerce', 'wc-frontend-manager-ultimate' ) ) printf( ' ' . __( 'by %s', 'wc-frontend-manager-ultimate' ), $note->comment_author ); ?>
								</td>
							</tr>
							<?php
						}
			
					} else {
						//echo '<li>' . __( 'There are no notes yet.', 'wc-frontend-manager-ultimate' ) . '</li>';
					}
			
					echo '</tbody></table>';
				}
			?>
			
			<?php if( $view_add_order_notes = apply_filters( 'wcfm_add_order_notes', true ) ) { ?>
				<div class="add_note">
				  <form name="wcfm_add_order_note_form" id="wcfm_add_order_note_form" action="" method="POST">
						<h2><?php _e( 'Add note', 'wc-frontend-manager-ultimate' ); ?> <span class="wcfmfa fa-question-circle img_tip" data-tip="<?php _e( 'Add a note for your reference, or add a customer note (the user will be notified).', 'wc-frontend-manager-ultimate' ); ?>"></span></h2>
						<div class="wcfm-clearfix"></div>
						<p>
							<textarea type="text" name="order_note" id="add_order_note" class="input-text wcfm-textarea wcfm_full_ele" cols="20" rows="5"></textarea>
						</p>
						<p>
						<?php
						if( apply_filters( 'wcfm_is_allow_order_note_attachments', true ) ) {
							$WCFM->wcfm_fields->wcfm_generate_form_field( array( "order_note_attachments"  => array( 'label' => __( 'Attachment(s)', 'wc-frontend-manager'), 'type' => 'multiinput', 'class' => 'wcfm-text wcfm_ele wcfm_non_sortable', 'label_class' => 'wcfm_title', 'value' => array(), 'options' => array(
																																																																				"name" => array('label' => __('Name', 'wc-frontend-manager'), 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_ele wcfm_title' ),
																																																																				"file" => array('label' => __('File', 'wc-frontend-manager'), 'type' => 'upload', 'mime' => 'Uploads', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_ele wcfm_title' ),
																																																																	 ), 'desc' => sprintf( __( 'Please upload any of these file types: %1$s', 'wc-frontend-manager' ), '<b style="color:#f86c6b;">' . implode( ', ', array_keys( wcfm_get_allowed_mime_types() ) ) . '</b>' ) )
																													) );
						}
						
						do_action( 'wcfm_order_add_note_form_end', $wp->query_vars['wcfm-orders-details'] );
						?>
						</p>
						<p>
							<select name="order_note_type" id="order_note_type" class="wcfm-select">
								<option class="order_note_type_private" value=""><?php _e( 'Private note', 'wc-frontend-manager-ultimate' ); ?></option>
								<option class="order_note_type_customer" value="customer"><?php _e( 'Note to customer', 'wc-frontend-manager-ultimate' ); ?></option>
							</select>
							<div class="wcfm-clearfix"></div>
							<input type="hidden" name="add_order_note_id" value="<?php echo $wp->query_vars['wcfm-orders-details']; ?>">
							<a href="#" class="add_note button" id="wcfm_add_order_note" data-orderid="<?php echo $wp->query_vars['wcfm-orders-details']; ?>"><?php _e( 'Add', 'wc-frontend-manager-ultimate' ); ?></a>
							<div class="wcfm-clearfix"></div>
						</p>
					</form>
				</div>
			<?php } ?>
		</div>
	</div>
	<!-- end collapsible -->
	<?php
}
?>