<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>
<div class="wrap woocommerce wcfm_manual_appointment_wrap">

	<p><?php _e( 'You can add a new appointment for a customer here. This form will create an appointment for the user, and optionally an associated order. Created orders will be marked as pending payment.', 'woocommerce-appointments' ); ?></p>

	<?php $this->show_errors(); ?>

	<form method="POST">
		<table class="form-table">
			<tbody>
				<tr valign="top" class="wcfm_manual_appointment_customer">
					<th scope="row">
						<span for="customer_id" class="wcfm_title" style="width:99%;"><strong><?php _e( 'Customer', 'woocommerce-appointments' ); ?></strong></span>
					</th>
					<td>
						<select name="customer_id" id="customer_id" class="wc-customer-search" data-placeholder="<?php _e( 'Guest', 'woocommerce-appointments' ); ?>" data-allow_clear="true" style="width: 100%;">
						  <option value="0"><?php _e( 'Guest', 'woocommerce-appointments' ); ?></option>
						  <?php
						  //if( !wcfm_is_vendor()  || apply_filters( 'wcfm_allow_customers_for_appointment', false ) ) {
								$args = array(
									//'role__in'     => array( 'customer' ),
									'orderby'      => 'ID',
									'order'        => 'ASC',
									'count_total'  => false,
									'fields'       => array( 'ID', 'display_name' )
								 ); 
								$args = apply_filters( 'wcfm_get_customers_args', $args );
								$all_users = get_users( $args );
								if( !empty( $all_users ) ) {
									foreach( $all_users as $all_user ) {
										?>
										<option value="<?php echo $all_user->ID; ?>"><?php echo '#' . $all_user->ID . ' ' . $all_user->display_name; ?></option>
										<?php
									}
								}
							//}
						  ?>
						</select>
						<?php if( apply_filters( 'wcfm_is_allow_add_customer', true ) && apply_filters( 'wcfm_is_allow_orders_manage_add_customer', true ) ) { ?>
							<?php do_action( 'wcfm_orders_manage_after_customers_list' ); ?>
						<?php } ?>
					</td>
				</tr>
				<tr valign="top" class="wcfm_manual_appointment_product">
					<th scope="row">
						<span for="appointable_product_id" class="wcfm_title" style="width:99%;"><strong><?php _e( 'Appointable Product', 'woocommerce-appointments' ); ?></strong></span>
					</th>
					<td>
						<select id="appointable_product_id" name="appointable_product_id" class="chosen_select" style="width: 100%;">
							<option value=""><?php _e( 'Select an appointable product...', 'woocommerce-appointments' ); ?></option>
							<?php foreach ( WC_Appointments_Admin::get_appointment_products() as $product ) : ?>
								<option value="<?php echo $product->get_id(); ?>"><?php echo sprintf( '%s (#%s)', $product->get_name(), $product->get_id() ); ?></option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
				<tr valign="top" class="wcfm_manual_appointment_create_order">
					<th scope="row">
						<span for="create_order" class="wcfm_title" style="width:99%;"><strong><?php _e( 'Create Order', 'woocommerce-appointments' ); ?></strong></span>
					</th>
					<td>
						<?php
						$always_create_order = apply_filters( 'woocommerce_appointments_always_create_order', false );
						if ( true == $always_create_order ) {
							?>
							<p class="wcfm_manual_appointment_new_order">
								<label class="disabled wcfm_title">
									<input type="radio" name="appointment_order" value="new" class="checkbox disabled" checked="checked" readonly />
									<?php _e( 'Create a new corresponding order for this new appointment.<br /> Please note - the appointment will not be active until the order is processed/completed.', 'woocommerce-appointments' ); ?>
								</label>
							</p>
							<?php
						} else {
						?>
							<p class="wcfm_manual_appointment_new_order">
								<label class="wcfm_title">
									<input type="radio" name="appointment_order" value="new" class="checkbox" checked="checked" />
									<?php _e( 'Create a new corresponding order for this new appointment. Please note - the appointment will not be active until the order is processed/completed.', 'woocommerce-appointments' ); ?>
								</label>
							</p>
							<p class="wcfm_manual_appointment_existing_order">
								<label class="wcfm_title">
									<input type="radio" name="appointment_order" value="existing" class="checkbox" />
									<?php _e( 'Assign this appointment to an existing order with this ID:', 'woocommerce-appointments' ); ?>
									<input type="number" name="appointment_order_id" value="" class="text" size="3" style="width: 80px;" />
								</label>
							</p>
							<p class="wcfm_manual_appointment_no_order" style="display:none;">
								<label class="wcfm_title">
									<input type="radio" name="appointment_order" value="" class="checkbox" />
									<?php _e( 'Don\'t create an order for this appointment.', 'woocommerce-appointments' ); ?>
								</label>
							</p>
						<?php } ?>
					</td>
				</tr>
				<?php do_action( 'woocommerce_appointments_after_create_appointment_page' ); ?>
				<tr valign="top">
					<th scope="row">&nbsp;</th>
					<td>
						<input type="submit" name="add_appointment" class="wcfm_submit_button" value="<?php _e( 'Next', 'woocommerce-appointments' ); ?>" />
						<?php wp_nonce_field( 'add_appointment_notification' ); ?>
					</td>
				</tr>
			</tbody>
		</table>
	</form>
</div>
