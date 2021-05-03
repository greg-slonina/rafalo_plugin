<?php
/**
 * WCFM plugin views
 *
 * Plugin Order Manual Views
 *
 * @author 		WC Lovers
 * @package 	wcfmu/views/orders
 * @version   5.2.0
 */
global $WCFM, $WCFMu;

if( !apply_filters( 'wcfm_is_allow_orders', true ) || !apply_filters( 'wcfm_is_allow_manage_order', true ) ) {
	wcfm_restriction_message_show( "Manual Order" );
	return;
}

$bfirst_name = '';
$blast_name  = '';
$bphone = '';
$baddr_1 = '';
$baddr_2 = '';
$bcountry = '';
$bcity = '';
$bstate = '';
$bzip = '';

$sfirst_name = ''; 
$slast_name = '';
$saddr_1 = '';
$saddr_2 = '';
$scountry = '';
$scity = '';
$sstate = '';
$szip = '';

$shipping_methods = WC()->shipping->load_shipping_methods();
$shipping_method_array = array( '' => __( 'Select Shipping Method', 'wc-frontend-manager-ultimate' ) );
if( !empty( $shipping_methods ) ) {
	foreach( $shipping_methods as $shipping_method ) {
		$shipping_method_array[$shipping_method->id] = esc_attr( $shipping_method->get_method_title() ); 
	}
}

if ( WC()->payment_gateways() ) {
	$payment_gateways = WC()->payment_gateways->get_available_payment_gateways();
} else {
	$payment_gateways = array();
}
$payment_gateways_array = array( '' => __( 'Select Payment Method', 'wc-frontend-manager-ultimate' ) );
foreach( $payment_gateways as $payment_gateway_key => $payment_gateway ) {
	$payment_gateways_array[$payment_gateway_key] = esc_html( $payment_gateway->get_title() );
}

?>
<div class="collapse wcfm-collapse" id="wcfm_orders_manage">
  <div class="wcfm-page-headig">
		<span class="wcfmfa fa-cart-plus"></span>
		<span class="wcfm-page-heading-text"><?php _e( 'Create Orders', 'wc-frontend-manager-ultimate' ); ?></span>
		<?php do_action( 'wcfm_page_heading' ); ?>
	</div>
	<div class="wcfm-collapse-content">
	  <div id="wcfm_page_load"></div>
	  
	  <div class="wcfm-container wcfm-top-element-container">
			<h2><?php _e( 'Create Orders Manually', 'wc-frontend-manager-ultimate' ); ?></h2>
			<?php
			if( $allow_wp_admin_view = apply_filters( 'wcfm_allow_wp_admin_view', true ) ) {
				?>
				<a target="_blank" class="wcfm_wp_admin_view text_tip" href="<?php echo admin_url('edit.php?post_type=shop_order'); ?>" data-tip="<?php _e( 'WP Admin View', 'wc-frontend-manager-ultimate' ); ?>"><span class="fab fa-wordpress fa-wordpress-simple"></span></a>
				<?php
			}
			
			if( apply_filters( 'wcfm_is_allow_orders', true ) ) {
				echo '<a class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_orders_url().'" data-tip="' . __('Orders', 'wc-frontend-manager') . '"><span class="wcfmfa fa-shopping-cart"></span></a>';
			}
			?>
			<div class="wcfm-clearfix"></div>
		</div>
	  <div class="wcfm-clearfix"></div><br />
	  
	  <?php do_action( 'before_wcfm_order_manual' ); ?>
	  
	  <form method="POST" id="wcfm_orders_manage_form">
			
			<div class="wcfm-container">
				<div id="wcfm_orders_manage_expander" class="wcfm-content">
		    
					<table class="form-table">
						<tbody>
							<tr valign="top">
								<th scope="row">
									<span for="customer_id" class="wcfm_title"><strong><?php _e( 'Customer', 'wc-frontend-manager-ultimate' ); ?></strong></span>
								</th>
								<td>
									<select name="customer_id" id="customer_id" class="wc-customer-search" data-placeholder="<?php _e( 'Guest', 'wc-frontend-manager-ultimate' ); ?>" data-allow_clear="true" style="width: 100%;">
									  <?php if( !WC()->checkout()->is_registration_required() ) { ?>
										  <option value="0"><?php _e( 'Guest', 'wc-frontend-manager-ultimate' ); ?></option>
										<?php } ?>
										<?php
										if( apply_filters( 'wcfm_is_allow_customers', true ) ) {
											$args = array(
												'role__in'     => apply_filters( 'wcfm_customer_user_role', array( 'customer', 'subscriber', 'client', 'bbp_participant' ) ),
												'orderby'      => 'ID',
												'order'        => 'ASC',
												'count_total'  => false,
												'fields'       => array( 'ID', 'display_name', 'user_email' )
											 ); 
											$args = apply_filters( 'wcfm_get_customers_args', $args );
											$all_users = get_users( $args );
											if( !empty( $all_users ) ) {
												foreach( $all_users as $all_user ) {
													?>
													<option value="<?php echo $all_user->ID; ?>"><?php echo '#' . $all_user->ID . ' ' . $all_user->display_name . ' (' . $all_user->user_email . ')'; ?></option>
													<?php
												}
											}
										}
										?>
									</select>
									<?php if( apply_filters( 'wcfm_is_allow_add_customer', true ) && apply_filters( 'wcfm_is_allow_orders_manage_add_customer', true ) ) { ?>
									  <?php do_action( 'wcfm_orders_manage_after_customers_list' ); ?>
									<?php } ?>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">
									<span for="wcfm_orders_manage_product_id" class="wcfm_title"><strong><?php _e( 'Products', 'wc-frontend-manager-ultimate' ); ?></stong></span>
								</th>
								<td>
									<?php
									$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_orders_manage_fields_product', array(  
										                                                                                  "associate_products" => array( 'type' => 'multiinput', 'class' => 'wcfm_non_sortable', 'options' => array( 
																																																																			"product" => array( 'label' => __( 'Product', 'wc-frontend-manager-ultimate' ), 'type' => 'select', 'attributes' => array( 'style' => 'width: 70%;' ), 'label_class' => 'wcfm_title', 'class' => 'wcfm-select wcfm_ele associate_product', 'options' => array(), 'value' => '' ),
																																																																			"variation"  => array( 'label' => __( 'Variation', 'wc-frontend-manager-ultimate' ), 'type' => 'select', 'label_class' => 'wcfm_title wcfm_ele_hide associate_product_variation_label', 'class' => 'wcfm-select wcfm_ele wcfm_ele_hide associate_product_variation', 'attributes' => array( 'style' => 'width: 70%;' ), 'option' => array(), 'value' => '' ),
																																																																			"quantity"  => array( 'label' => __( 'Quantity', 'wc-frontend-manager-ultimate' ), 'type' => 'number', 'label_class' => 'wcfm_title', 'class' => 'wcfm-text wcfm_ele wcfm_non_negative_input associate_product_qty', 'attributes' => array( 'style' => 'width: 70%;' ), 'value' => '1' )
																																																																		) )
																																														) ) );
									?>
									<?php do_action( 'wcfm_orders_manage_after_products_list' ); ?>
								</td>
							</tr>
						</tbody>
					</table>
					
				</div>
			</div>
			<div class="wcfm_clearfix"></div><br />
					
			<div class="wcfm-tabWrap">
			
			  <?php if( apply_filters( 'wcfm_orders_manage_payment', true ) ) { ?>
					<div class="page_collapsible" id="wcfm_om_payment_head">
						<label class="wcfmfa fa-credit-card"></label>
						<?php _e('Payment', 'wc-frontend-manager-ultimate'); ?><span></span>
					</div>
					<div class="wcfm-container">
						<div id="wcfm_om_payment_expander" class="wcfm-content">
							<?php
							$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_orders_manage_fields_payment', array(  
																																																	"wcfm_om_payment_method"  => array( 'label' => __( 'Payment Method', 'wc-frontend-manager-ultimate' ), 'type' => 'select', 'class' => 'wcfm-select wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'options' => $payment_gateways_array, 'value' => '' ),
																																																	"wcfm_om_payment_details"  => array( 'label' => __( 'Payment Details', 'wc-frontend-manager-ultimate' ), 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => '' )
																																												)) );
							?>
							<?php do_action( 'wcfm_orders_manage_after_payment' ); ?>
						</div>
					</div>
				<?php } ?>
			  
			  <?php if( !empty( $shipping_method_array ) && apply_filters( 'wcfm_orders_manage_shipping', true ) ) { ?>
					<div class="page_collapsible" id="wcfm_om_shipping_head">
						<label class="wcfmfa fa-truck"></label>
						<?php _e('Shipping', 'wc-frontend-manager'); ?><span></span>
					</div>
					<div class="wcfm-container">
						<div id="wcfm_on_shipping_expander" class="wcfm-content">
							<?php
							$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_orders_manage_fields_shipping', array(  
																																																	"wcfm_om_shipping_method" => array( 'label' => __( 'Shipping Method', 'wc-frontend-manager-ultimate' ), 'type' => 'select', 'class' => 'wcfm-select wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'options' => $shipping_method_array, 'value' => '' ),
																																																	"wcfm_om_shipping_cost"  => array( 'label' => __( 'Shipping Cost', 'wc-frontend-manager-ultimate' ), 'type' => 'number', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => '' )
																																												)) );
							?>
							<?php do_action( 'wcfm_orders_manage_after_shipping' ); ?>
						</div>
					</div>
				<?php } ?>
	
				<?php if( apply_filters( 'wcfm_orders_manage_address', true ) ) { ?>
					<div class="page_collapsible" id="wcfm_customer_address_head">
						<label class="wcfmfa fa-address-card"></label>
						<?php _e('Address', 'wc-frontend-manager'); ?><span></span>
					</div>
					<div class="wcfm-container">
						<div id="wcfm_customer_address_expander" class="wcfm-content">
							<?php if( apply_filters( 'wcfm_allow_customer_billing_details', true ) ) { ?>
								<div class="wcfm_customer_heading"><h2><?php _e( 'Billing', 'wc-frontend-manager' ); ?></h2></div>
								<div class="wcfm_clearfix"></div><br />
								<div class="store_address store_address_wrap">
									<?php
										$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_customer_fields_billing', array(
																																																			"bfirst_name" => array('label' => __('First Name', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $bfirst_name ),
																																																			"blast_name" => array('label' => __('Last Name', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $blast_name ),
																																																			"bphone" => array('label' => __('Phone', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $bphone ),
																																																			"baddr_1" => array('label' => __('Address 1', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $baddr_1 ),
																																																			"baddr_2" => array('label' => __('Address 2', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $baddr_2 ),
																																																			"bcountry" => array('label' => __('Country', 'wc-frontend-manager') , 'type' => 'country', 'class' => 'wcfm-select wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'attributes' => array( 'style' => 'width: 60%;' ), 'value' => $bcountry ),
																																																			"bcity" => array('label' => __('City/Town', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $bcity ),
																																																			"bstate" => array('label' => __('State/County', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $bstate ),
																																																			"bzip" => array('label' => __('Postcode/Zip', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $bzip ),
																																																			) ) );
								}
								?>
							</div>
							<?php
							if( apply_filters( 'wcfm_allow_customer_shipping_details', true ) ) {
							?>
							
							<div class="wcfm_clearfix"></div>
							<div class="wcfm_customer_heading"><h2><?php _e( 'Shipping', 'wc-frontend-manager' ); ?></h2></div>
							<div class="wcfm_clearfix"></div><br />
							<?php
							$WCFM->wcfm_fields->wcfm_generate_form_field( array(
																																	"sadd_as_billing" => array('label' => __('Shipping same as billing', 'wc-frontend-manager-ultimate') , 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'label_class' => 'wcfm_title checkbox_title wcfm_ele', 'value' => 'yes', 'dfvalue' => 'yes' )
																																	) );
							?>
							<div class="store_address store_shipping_address_wrap">
								<?php
									$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_customer_fields_shipping', array(
																																																		"sfirst_name" => array('label' => __('First Name', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $sfirst_name ),
																																																		"slast_name" => array('label' => __('Last Name', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $slast_name ),
																																																		"saddr_1" => array('label' => __('Address 1', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $saddr_1 ),
																																																		"saddr_2" => array('label' => __('Address 2', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $saddr_2 ),
																																																		"scountry" => array('label' => __('Country', 'wc-frontend-manager') , 'type' => 'country', 'class' => 'wcfm-select wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'attributes' => array( 'style' => 'width: 60%;' ), 'value' => $scountry ),
																																																		"scity" => array('label' => __('City/Town', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $scity ),
																																																		"sstate" => array('label' => __('State/County', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $sstate ),
																																																		"szip" => array( 'label' => __('Postcode/Zip', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $szip ),
																																																		) ) );
									}
								?>
							</div>
						</div>
					</div>
				<?php } ?>
				
				<?php if( wc_coupons_enabled() && apply_filters( 'wcfm_orders_manage_discount', true ) ) { ?>
					<div class="page_collapsible" id="wcfm_om_discount_head">
						<label class="wcfmfa fa-gift"></label>
						<?php _e('Discount', 'wc-frontend-manager-ultimate'); ?><span></span>
					</div>
					<div class="wcfm-container">
						<div id="wcfm_om_discount_expander" class="wcfm-content">
							<?php
							$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_orders_manage_fields_discount', array(  
																																																	"wcfm_om_discount"  => array( 'label' => __('Discount Amount', 'wc-frontend-manager-ultimate')  . ' (' . get_woocommerce_currency_symbol() . ')', 'type' => 'number', 'class' => 'wcfm-text wcfm_ele wcfm_non_negative_input', 'label_class' => 'wcfm_title wcfm_ele', 'value' => '' )
																																												)) );
							?>
							<?php do_action( 'wcfm_orders_manage_after_discount' ); ?>
						</div>
					</div>
				<?php } ?>
				
				<?php if( apply_filters( 'wcfm_orders_manage_note', true ) ) { ?>
					<div class="page_collapsible" id="wcfm_om_note_head">
						<label class="wcfmfa fa-comments"></label>
						<?php _e('Comment', 'wc-frontend-manager-ultimate'); ?><span></span>
					</div>
					<div class="wcfm-container">
						<div id="wcfm_om_note_expander" class="wcfm-content">
							<?php
							$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_orders_manage_fields_note', array(  
																																																	"wcfm_om_comments"  => array( 'label' => __('Note to Customer', 'wc-frontend-manager-ultimate'), 'type' => 'textarea', 'class' => 'wcfm-textarea wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => '' )
																																												)) );
							?>
							<?php do_action( 'wcfm_orders_manage_after_note' ); ?>
						</div>
					</div>
				<?php } ?>
				
				<?php do_action( 'wcfm_orders_manage_after_tabs' ); ?>
				
				<div class="wcfm_clearfix"></div>
			</div>
					
			<div id="wcfm_orders_manual_submit" class="wcfm_form_simple_submit_wrapper">
				<div class="wcfm-message" tabindex="-1"></div>
				
				<input type="submit" id="wcfm_orders_manage_submit_button" name="wcfm_orders_manage_submit_button" class="wcfm_submit_button wcfm_orders_manage_submit_button" value="<?php _e( 'Submit', 'wc-frontend-manager-ultimate' ); ?>" />
			</div>
			<?php
			do_action( 'end_wcfm_orders_manage_form' );
			?>
		</form>
		<div class="wcfm-clearfix"></div>
		<?php
		do_action( 'after_wcfm_orders_manual' );
		?>
	</div>
</div>