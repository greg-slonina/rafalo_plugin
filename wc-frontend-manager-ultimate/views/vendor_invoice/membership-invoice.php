<?php 
global $WCFM, $WCFMu, $wpo_wcpdf, $vendor_id, $wcfm_membership_id, $wcfm_register_member, $document, $document_type, $wpdb; 

$membership_post = get_post( $wcfm_membership_id );
$title = htmlspecialchars($membership_post->post_title);
$description = $membership_post->post_excerpt;

$subscription = (array) get_post_meta( $wcfm_membership_id, 'subscription', true );
$features = (array) get_post_meta( $wcfm_membership_id, 'features', true );

$is_free = isset( $subscription['is_free'] ) ? 'yes' : 'no';
$subscription_type = isset( $subscription['subscription_type'] ) ? $subscription['subscription_type'] : 'one_time';
$one_time_amt = isset( $subscription['one_time_amt'] ) ? floatval($subscription['one_time_amt']) : '1';
$trial_amt = isset( $subscription['trial_amt'] ) ? $subscription['trial_amt'] : '';
$trial_period = isset( $subscription['trial_period'] ) ? $subscription['trial_period'] : '';
$trial_period_type = isset( $subscription['trial_period_type'] ) ? $subscription['trial_period_type'] : 'M';
$billing_amt = isset( $subscription['billing_amt'] ) ? floatval($subscription['billing_amt']) : '1';
$billing_period = isset( $subscription['billing_period'] ) ? $subscription['billing_period'] : '1';
$billing_period_type = isset( $subscription['billing_period_type'] ) ? $subscription['billing_period_type'] : 'M';
$period_options = array( 'D' => __( 'Day(s)', 'wc-multivendor-membership' ), 'M' => __( 'Month(s)', 'wc-multivendor-membership' ), 'Y' => __( 'Year(s)', 'wc-multivendor-membership' ) );

$wcfm_membership_options = get_option( 'wcfm_membership_options', array() );
$membership_feature_lists = array();
if( isset( $wcfm_membership_options['membership_features'] ) ) $membership_feature_lists = $wcfm_membership_options['membership_features'];

$membership_tax_settings = array();
if( isset( $wcfm_membership_options['membership_tax_settings'] ) ) $membership_tax_settings = $wcfm_membership_options['membership_tax_settings'];
$tax_enable  = isset( $membership_tax_settings['enable'] ) ? 'yes' : 'no';
$tax_name    = isset( $membership_tax_settings['name'] ) ? $membership_tax_settings['name'] : __( 'Tax', 'wc-multivendor-membership' );
$tax_percent = isset( $membership_tax_settings['percent'] ) ? $membership_tax_settings['percent'] : '';

?>

<table class="head container">
	<tr>
		<td class="header">
		<?php
		if( $document->has_header_logo() ) {
			$document->header_logo();
		} else {
			echo apply_filters( 'wcfm_membership_invoice_invoice_title', __( 'Subscription Invoice', 'wc-frontend-manager-ultimate' ) );
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
<?php if( $document->has_header_logo() ) echo apply_filters( 'wcfm_membership_invoice_invoice_title', __( 'Subscription Invoice', 'wc-frontend-manager-ultimate' ) ); ?>
</h1>

<table class="order-data-addresses">
	<tr>
		<td class="address billing-address">
			<div class="vendor-shop-name"><h3><?php echo wcfm_get_vendor_store_name( $vendor_id ); ?></h3></div>
			<div class="vendor-shop-address"><?php echo wcfm_get_vendor_store_address_by_vendor( $vendor_id ); ?></div>
			<div class="vendor-shop-email"><?php echo wcfm_get_vendor_store_email_by_vendor( $vendor_id ); ?></div>
			<div class="vendor-shop-phone"><?php echo wcfm_get_vendor_store_phone_by_vendor( $vendor_id ); ?></div>
			<?php do_action( 'wcfm_membership_invoice_after_vendor_details', $vendor_id ); ?>
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
			  <tr class="invoice-number">
					<th><?php _e( 'Invoice Number:', 'woocommerce-pdf-invoices-packing-slips' ); ?></th>
					<td><?php echo __( 'membership-invoice', 'wc-frontend-manager-ultimate' ) . '-'.$vendor_id.'-'.$wcfm_membership_id.'-'.current_time( 'timestamp', 0 ); ?></td>
				</tr>
				<tr class="order-date">
					<th><?php _e( 'Invoice Date:', 'wc-frontend-manager-ultimate' ); ?></th>
					<td>
					  <?php 
					  echo date_i18n( wc_date_format(), current_time( 'timestamp', 0 ) ); ?> @<?php echo date_i18n( wc_time_format(), current_time( 'timestamp', 0 ) );
					  ?>
					</td>
				</tr>
				<tr class="payment-method">
					<th><?php _e( 'Payment Method:', 'wc-frontend-manager-ultimate' ); ?></th>
					<td>
					  <?php 
					  if( $is_free == 'yes' ) {
					  	$paymode = __( 'FREE', 'wc-multivendor-membership' );
					  } else {
							$wcfm_membership_payment_methods = get_wcfm_membership_payment_methods();
							$paymode = get_user_meta( $vendor_id, 'wcfm_membership_paymode', true );
							if( in_array( $paymode, array( 'paypal_subs', 'paypal_subs_subs' ) ) ) $paymode = 'paypal';
							if( in_array( $paymode, array( 'stripe', 'stripe_subs', 'stripe_subs_subs' ) ) ) $paymode = 'stripe';
							if( in_array( $paymode, array( 'bank_transfer', 'bank_transfer_subs' ) ) ) $paymode = 'bank_transfer';
							if( !$paymode ) $paymode = 'bank_transfer';
							$payment_method = $paymode;
							if( isset( $wcfm_membership_payment_methods[$paymode] ) ) {
								$paymode = $wcfm_membership_payment_methods[$paymode];
							} else {
								if ( function_exists('icl_object_id') ) {
									global $sitepress;
									remove_filter('get_terms_args', array( $sitepress, 'get_terms_args_filter'));
									remove_filter('get_term', array($sitepress,'get_term_adjust_id'));
									remove_filter('terms_clauses', array($sitepress,'terms_clauses'));
								}
								if ( WC()->payment_gateways() ) {
									$payment_gateways = WC()->payment_gateways->payment_gateways();
									$paymode = isset( $payment_gateways[ $paymode ] ) ? esc_html( $payment_gateways[ $paymode ]->get_title() ) : __( 'FREE', 'wc-multivendor-membership' );
								}
							}
						}
						echo $paymode;
					?>
				  </td>
			  </tr>
			</table>			
		</td>
	</tr>
</table>

<table class="order-details">
	<thead>
		<tr>
			<th colspan="4"><?php _e( 'Membership Plan', 'wc-frontend-manager-ultimate' ); ?></th>
			<th style="text-align:right;"><?php _e( 'Cost', 'wc-frontend-manager-ultimate' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td colspan="4">
				<h3><?php echo $title; ?></h3>
			</td>
			<td style="text-align:right;">
			  <?php 
			  if( $is_free == 'yes' ) {
			  	echo wc_price(0);
			  } else {
					if( $subscription_type == 'one_time' ) {
						echo wc_price( $one_time_amt );
						//echo '<br /><small class="wcfm_membership_price_description">' . __( 'One time payment', 'wc-multivendor-membership' ) . '</small>';
					} else {
						$is_recurring = true;
						if( $wcfm_register_member ) {
							echo wc_price( $billing_amt );
						} else {
							if( !empty( $trial_period ) && !empty( $trial_amt ) ) {
								if( $payment_method == 'stripe' ) {
									echo wc_price(0);
								} else {
									echo wc_price( $trial_amt );
								}
							} elseif( !empty( $trial_period ) && empty( $trial_amt ) ) {
								if( $payment_method == 'paypal' ) {
									echo wc_price(1);
								} else {
									echo wc_price(0);
								}
							} elseif( empty( $trial_period ) ) {
								echo wc_price( $billing_amt );
							}
						}
					}
				}
			  ?>
			</td>
		</tr>
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
							<td style="text-align:right;float:right;">
							  <?php 
							  $subscription_price = 0;
								if( $is_free == 'yes' ) {
									echo wc_price(0);
								} else {
									if( $subscription_type == 'one_time' ) {
										$subscription_price = $one_time_amt;
										echo wc_price( $one_time_amt );
										//echo '<br /><small class="wcfm_membership_price_description">' . __( 'One time payment', 'wc-multivendor-membership' ) . '</small>';
									} else {
										$is_recurring = true;
										if( $wcfm_register_member ) {
											$subscription_price = $billing_amt;
											echo wc_price( $billing_amt );
										} else {
											if( !empty( $trial_period ) && !empty( $trial_amt ) ) {
												if( $payment_method == 'stripe' ) {
													$subscription_price = 0;
													echo wc_price(0);
												} else {
													$subscription_price = $trial_amt;
													echo wc_price( $trial_amt );
												}
											} elseif( !empty( $trial_period ) && empty( $trial_amt ) ) {
												if( $payment_method == 'paypal' ) {
													$subscription_price = 1;
													echo wc_price(1);
												} else {
													$subscription_price = 0;
													echo wc_price(0);
												}
											} elseif( empty( $trial_period ) ) {
												$subscription_price = $billing_amt;
												echo wc_price( $billing_amt );
											}
										}
									}
								}
								?>
							</td>
						</tr>
						<?php if( $tax_enable == 'yes' ) { ?>
							<tr class="no-borders">
								<td style="text-align:right;float:right;"><?php echo $tax_name; if( $tax_percent ) { echo ' (' . $tax_percent . '%)'; } ?></td>
								<td style="text-align:right;float:right;">
								  <?php if( !$tax_percent ) { echo wc_price(0); } else { echo wc_price( wc_format_decimal( $subscription_price * ($tax_percent/100) ) ); }; ?>
								</td>
							</tr>
						<?php } ?>
						<tr class="no-borders">
							<td style="text-align:right;font-weight:400;padding-top:10px;font-size:18px;vertical-align:middle;"><?php _e( 'Total', 'wc-multivendor-membership' ); ?></td>
							<td style="text-align:right;font-weight:400;padding-top:10px;font-size:18px;"><strong>
							  <?php 
								if( $is_free == 'yes' ) {
									echo wc_price(0);
								} else {
									if( $subscription_type == 'one_time' ) {
										echo wc_price( wcfmvm_membership_tax_price( $one_time_amt ) );
										echo '<br /><small class="wcfm_membership_price_description">' . __( 'One time payment', 'wc-multivendor-membership' ) . '</small>';
									} else {
										$is_recurring = true;   
										if( $wcfm_register_member ) {
											echo wc_price( wcfmvm_membership_tax_price( $billing_amt ) );
											$price_description = sprintf( __( '%s for each %s %s', 'wc-multivendor-membership' ), get_woocommerce_currency_symbol() . $billing_amt, $billing_period, $period_options[$billing_period_type] );
										} else {
											if( !empty( $trial_period ) && !empty( $trial_amt ) ) {
												if( $payment_method == 'stripe' ) {
													echo wc_price(0);
													$price_description = sprintf( __( '%s for each %s %s', 'wc-multivendor-membership' ), get_woocommerce_currency_symbol() . $billing_amt, $billing_period, $period_options[$billing_period_type] );
													$price_description .= ' ' . sprintf( __( 'with %s %s free trial', 'wc-multivendor-membership' ), $trial_period, $period_options[$trial_period_type] );
												} else {
													echo wc_price( wcfmvm_membership_tax_price( $trial_amt ) );
													$price_description = ' ' . sprintf( __( '%s for first %s %s', 'wc-multivendor-membership' ), get_woocommerce_currency_symbol() . $trial_amt, $trial_period, $period_options[$trial_period_type] );
													$price_description .= ' ' . sprintf( __( 'and then %s for each %s %s', 'wc-multivendor-membership' ), get_woocommerce_currency_symbol() . $billing_amt, $billing_period, $period_options[$billing_period_type] );
												}
											} elseif( !empty( $trial_period ) && empty( $trial_amt ) ) {
												if( $payment_method == 'paypal' ) {
													echo wc_price( wcfmvm_membership_tax_price( 1 ) );
													$price_description = ' ' . sprintf( __( '%s for first %s %s', 'wc-multivendor-membership' ), get_woocommerce_currency_symbol() . 1, $trial_period, $period_options[$trial_period_type] );
													$price_description .= ' ' . sprintf( __( 'and then %s for each %s %s', 'wc-multivendor-membership' ), get_woocommerce_currency_symbol() . $billing_amt, $billing_period, $period_options[$billing_period_type] );
												} else {
													echo wc_price(0);
													$price_description = sprintf( __( '%s for each %s %s', 'wc-multivendor-membership' ), get_woocommerce_currency_symbol() . $billing_amt, $billing_period, $period_options[$billing_period_type] );
													$price_description .= ' ' . sprintf( __( 'with %s %s free trial', 'wc-multivendor-membership' ), $trial_period, $period_options[$trial_period_type] );
												}
											} elseif( empty( $trial_period ) ) {
												echo wc_price( wcfmvm_membership_tax_price( $billing_amt ) );
												$price_description = sprintf( __( '%s for each %s %s', 'wc-multivendor-membership' ), get_woocommerce_currency_symbol() . $billing_amt, $billing_period, $period_options[$billing_period_type] );
											}
										}
										echo '<br /><small class="wcfm_membership_price_description">' . $price_description . '</small>';
									}
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

<br /><br /><br /><br />
<h2><?php _e( 'Subscription details:', 'wc-multivendor-membership' ); ?></h2>
<div class="wcfm_clearfix"></div><br />
<div class="wcfm_membership_review_pay">
	<div class="wcfm_membership_review_plan">
		<h3 class="wcfm_review_plan_title"><?php echo $title; ?></h3><br />
		<div class="wcfm_review_plan_description"><?php echo $description; ?></div><br />
		<?php echo wcfm_membership_features_table( $wcfm_membership_id, false ); ?>
		</table>
	</div>
</div>

<?php do_action( 'wcfm_membership_invoice_after_details', $document_type ); ?>

<?php if ( $document->get_footer() ): ?>
<div id="footer" style="width: 100%;display:block;">
	<?php $document->footer(); ?>
</div><!-- #letter-footer -->
<?php endif; ?>
<?php do_action( 'wcfm_membership_invoice_after_document', $document_type ); ?>
