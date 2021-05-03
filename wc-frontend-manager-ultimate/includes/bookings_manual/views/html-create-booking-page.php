<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<div class="wrap woocommerce">
	<p><?php _e( 'You can create a new booking for a customer here. This form will create a booking for the user, and optionally an associated order. Created orders will be marked as pending payment.', 'woocommerce-bookings' ); ?></p>

	<?php $this->show_errors(); ?>

	<form method="POST">
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row">
						<span for="customer_id" class="wcfm_title" style="width:99%;"><strong><?php _e( 'Customer', 'woocommerce-bookings' ); ?></strong></span>
					</th>
					<td>
						<select name="customer_id" id="customer_id" class="wc-customer-search" data-placeholder="<?php _e( 'Guest', 'woocommerce-bookings' ); ?>" data-allow_clear="true" style="width:100%;">
						  <option value="0"><?php _e( 'Guest', 'woocommerce-bookings' ); ?></option>
						  <?php
						  //if( apply_filters( 'wcfm_allow_customers_for_booking', false ) ) {
								$args = array(
									//'role__in'     => array( 'customer' ),
									'orderby'      => 'ID',
									'order'        => 'ASC',
									'count_total'  => false,
									//'fields'       => array( 'ID', 'display_name', 'user_email', 'first_name', 'last_name' )
								 ); 
								$args = apply_filters( 'wcfm_get_customers_args', $args );
								$all_users = get_users( $args );
								if( !empty( $all_users ) ) {
									foreach( $all_users as $all_user ) {
										?>
										<option value="<?php echo $all_user->ID; ?>"><?php echo $all_user->first_name . ' ' . $all_user->last_name .  ' (#' . $all_user->ID . ' ' . $all_user->display_name . ' - ' . $all_user->user_email . ')'; ?></option>
										<?php
									}
								}
							//}
						  ?>
						</select>
						<?php if( apply_filters( 'wcfm_is_allow_add_customer', true ) && apply_filters( 'wcfm_is_allow_orders_manage_add_customer', true ) ) { ?>
							<?php do_action( 'wcfm_orders_manage_after_customers_list' ); ?>
						<?php } ?>
						<?php do_action( 'wcfm_wcb_after_customers_list' ); ?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<span for="bookable_product_id" class="wcfm_title" style="width:99%;"><strong><?php _e( 'Bookable Product', 'woocommerce-bookings' ); ?></strong></span>
					</th>
					<td>
						<select id="bookable_product_id" name="bookable_product_id" class="chosen_select" style="width:100%;">
							<option value=""><?php _e( 'Select a bookable product...', 'woocommerce-bookings' ); ?></option>
							<?php foreach ( WC_Bookings_Admin::get_booking_products() as $product ) : ?>
								<option value="<?php echo $product->get_id(); ?>"><?php echo sprintf( '%s (#%s)', $product->get_name(), $product->get_id() ); ?></option>
							<?php endforeach; ?>
						</select>
						<?php do_action( 'wcfm_wcb_after_products_list' ); ?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<span for="create_order" class="wcfm_title" style="width:99%;"><strong><?php _e( 'Create Order', 'woocommerce-bookings' ); ?></strong></span>
					</th>
					<td>
						<p>
							<label class="wcfm_title">
								<input type="radio" name="booking_order" value="new" class="checkbox" checked="checked" />
								<?php _e( 'Create a new corresponding order for this new booking. Please note - the booking will not be active until the order is processed/completed.', 'woocommerce-bookings' ); ?>
							</label>
						</p>
						<?php if( apply_filters( 'wcfm_is_allow_manual_booking_assign_order', true ) ) { ?>
							<p>
								<label class="wcfm_title">
									<input type="radio" name="booking_order" value="existing" class="checkbox" />
									<?php _e( 'Assign this booking to an existing order with this ID:', 'woocommerce-bookings' ); ?>
									<input type="number" name="booking_order_id" value="" class="text" size="3" style="width: 80px;" />
								</label>
							</p>
						<?php } ?>
						<p style="display:none;">
							<label class="wcfm_title">
								<input type="radio" name="booking_order" value="" class="checkbox" />
								<?php _e( 'Don\'t create an order for this booking.', 'woocommerce-bookings' ); ?>
							</label>
						</p>
					</td>
				</tr>
				<?php do_action( 'woocommerce_bookings_after_create_booking_page' ); ?>
				<tr valign="top">
					<th scope="row">&nbsp;</th>
					<td>
						<input type="submit" name="create_booking" class="wcfm_submit_button" value="<?php _e( 'Next', 'woocommerce-bookings' ); ?>" />
						<?php wp_nonce_field( 'create_booking_notification' ); ?>
					</td>
				</tr>
			</tbody>
		</table>
	</form>
</div>
<?php
