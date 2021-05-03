<?php
/**
 * Create new appointments page
 */
class WCFM_Create_Appointments_Manual {

	/**
	 * Stores errors.
	 *
	 * @var array
	 */
	private $errors = array();

	/**
	 * Output the form
	 */
	public function output() {
		$this->errors = array();
		$step         = 1;

		try {
			if ( ! empty( $_POST ) && ! check_admin_referer( 'add_appointment_notification' ) ) {
				throw new Exception( __( 'Error - please try again', 'woocommerce-appointments' ) );
			}

			if ( ! empty( $_POST['add_appointment'] ) ) {
				$customer_id			= ! empty( $_POST['customer_id'] ) ? absint( $_POST['customer_id'] ) : '';
				$appointable_product_id = absint( $_POST['appointable_product_id'] );
				$appointment_order		= wc_clean( $_POST['appointment_order'] );

				if ( ! $appointable_product_id ) {
					throw new Exception( __( 'Please choose an appointable product', 'woocommerce-appointments' ) );
				}

				if ( 'existing' === $appointment_order ) {
					$order_id			= absint( $_POST['appointment_order_id'] );
					$appointment_order	= $order_id;

					if ( ! $appointment_order || get_post_type( $appointment_order ) !== 'shop_order' ) {
						throw new Exception( __( 'Invalid order ID provided', 'woocommerce-appointments' ) );
					}
				}

				$step++;
				$product				    = wc_get_product( $appointable_product_id );
				$appointment_form		= new WC_Appointment_Form( $product );

			} elseif ( ! empty( $_POST['add_appointment_2'] ) ) {
				$customer_id            = ! empty( $_POST['customer_id'] ) ? absint( $_POST['customer_id'] ) : '';
				$appointable_product_id = absint( $_POST['appointable_product_id'] );
				$appointment_order      = wc_clean( $_POST['appointment_order'] );
				$product                = wc_get_product( $appointable_product_id );
				$appointment_form       = new WC_Appointment_Form( $product );
				if( defined( 'WC_APPOINTMENTS_VERSION' ) && version_compare( WC_APPOINTMENTS_VERSION, '4.7.0', '>=' ) ) { 
					$appointment_data       = wc_appointments_get_posted_data( $_POST, $product );
					$cost                   = WC_Appointments_Cost_Calculation::calculate_appointment_cost( $_POST, $product );
				} else {
					$appointment_data       = $appointment_form->get_posted_data();
					$cost                   = $appointment_form->calculate_appointment_cost( $_POST );
				}
				$appointment_cost       = $cost && ! is_wp_error( $cost ) ? number_format( $cost, 2, '.', '' ) : 0;
				$create_order           = false;
				$order_id               = 0;
				$item_id                = 0;

				if ( wc_prices_include_tax() ) {
					$base_tax_rates   = WC_Tax::get_base_tax_rates( $product->get_tax_class() );
					$base_taxes       = WC_Tax::calc_tax( $appointment_cost, $base_tax_rates, true );
					$appointment_cost = $appointment_cost - array_sum( $base_taxes );

					if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
						$appointment_cost = round( $appointment_cost, absint( get_option( 'woocommerce_price_num_decimals' ) ) );
					}
				}

				// Data to go into the appointment
				$staff_id             = isset( $appointment_data['_staff_id'] ) ? $appointment_data['_staff_id'] : '';
				$new_appointment_data = array(
					'customer_id' => $customer_id,
					'product_id'  => $product->get_id(),
					'staff_ids'   => isset( $appointment_data['_staff_ids'] ) ? $appointment_data['_staff_ids'] : $staff_id,
					'cost'        => $appointment_cost,
					'start'       => $appointment_data['_start_date'],
					'end'         => $appointment_data['_end_date'],
					'all_day'     => $appointment_data['_all_day'] ? 1 : 0,
					'qty'         => $appointment_data['_qty'] ? $appointment_data['_qty'] : 1,
					'timezone'    => $appointment_data['_timezone'] ? $appointment_data['_timezone'] : '',
				);

				// Create order
				if ( 'new' === $appointment_order ) {
					$create_order = true;
					$order_id     = $this->create_order( $appointment_cost, $customer_id );

					if ( ! $order_id ) {
						throw new Exception( __( 'Error: Could not create order', 'woocommerce-appointments' ) );
					}
				} elseif ( $appointment_order > 0 ) {
					if ( class_exists( 'WC_Seq_Order_Number_Pro' ) ) {
						$order_id = WC_Seq_Order_Number_Pro::find_order_by_order_number( $appointment_order );
					} else {
						$order_id = absint( $appointment_order );
					}

					if ( ! $order_id || get_post_type( $order_id ) !== 'shop_order' ) {
						throw new Exception( __( 'Invalid order ID provided', 'woocommerce-appointments' ) );
					}

					$order = wc_get_order( $order_id );

					if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
						update_post_meta( $order_id, '_order_total', $order->get_total() + $appointment_cost );
					} else {
						$order->set_total( $order->get_total( 'edit' ) + $appointment_cost );
						$order->save();
					}
				}

				if ( $order_id ) {
		           	$item_id = wc_add_order_item(
						$order_id,
						array(
					 		'order_item_name' => $product->get_title(),
					 		'order_item_type' => 'line_item',
					 	)
					);

				 	if ( ! $item_id ) {
						throw new Exception( __( 'Error: Could not create item', 'woocommerce-appointments' ) );
				 	}

					if ( ! empty( $customer_id ) ) {
						// set order address.
						$order = wc_get_order( $order_id );
						$keys  = array(
							'first_name',
							'last_name',
							'company',
							'address_1',
							'address_2',
							'phone',
							'city',
							'state',
							'postcode',
							'country',
						);
						$types = array( 'shipping', 'billing' );
						foreach ( $types as $type ) {
							$address = array();

							foreach ( $keys as $key ) {
								$address[ $key ] = (string) get_user_meta( $customer_id, $type . '_' . $key, true );
							}
							$order->set_address( $address, $type );
						}
					}

				 	// Add line item meta.
				 	wc_add_order_item_meta( $item_id, '_qty', $appointment_data['_qty'] ? $appointment_data['_qty'] : 1 );
				 	wc_add_order_item_meta( $item_id, '_tax_class', $product->get_tax_class() );
				 	wc_add_order_item_meta( $item_id, '_product_id', $product->get_id() );
				 	wc_add_order_item_meta( $item_id, '_variation_id', '' );
				 	wc_add_order_item_meta( $item_id, '_line_subtotal', $appointment_cost );
				 	wc_add_order_item_meta( $item_id, '_line_total', $appointment_cost );
				 	wc_add_order_item_meta( $item_id, '_line_tax', 0 );
				 	wc_add_order_item_meta( $item_id, '_line_subtotal_tax', 0 );

					do_action( 'woocommerce_appointments_create_appointment_page_add_order_item', $order_id, $item_id, $product );
				}

				// Calculate the order taxes.
				$order = wc_get_order( $order_id );
				if ( is_a( $order, 'WC_Order' ) ) {
					$order->calculate_totals( wc_tax_enabled() );
				}

				// New Appointment arguments.
				$new_appointment_args = apply_filters(
					'woocommerce_appointments_create_appointment_args',
					array(
						'order_id'     => $order_id,
						'create_order' => $create_order,
						'item_id'      => $item_id,
						'status'       => $create_order ? 'unpaid' : 'confirmed',
					),
					$new_appointment_data
				);

				// Create the appointment itself.
				if( function_exists( 'get_wc_appointment' ) ) {
					$new_appointment = get_wc_appointment( $new_appointment_data );
				} else {
					$new_appointment = new WC_Appointment( $new_appointment_data );
				}
				$new_appointment->set_order_id( $new_appointment_args['order_id'] );
				$new_appointment->set_order_item_id( $new_appointment_args['item_id'] );
				$new_appointment->set_status( $new_appointment_args['status'] );
				$new_appointment->save();

				// Schedule notifications.
				$new_appointment->maybe_schedule_event( 'reminder' );
				$new_appointment->maybe_schedule_event( 'complete' );
				
				do_action( 'woocommerce_appointment_added_manual_appointment', $new_appointment );

				// Make sure unpaid status is hooked the same way
				// as for the front-end.
				if ( 'unpaid' === $new_appointment_args['status'] ) {
					do_action( 'woocommerce_appointment_' . $new_appointment_args['status'], $new_appointment->get_id(), $new_appointment );
				}

				?>
				<script>
				  window.location = '<?php echo apply_filters( 'wcfm_manual_appointment_redirect', get_wcfm_view_appointment_url( $new_appointment->get_id() ), $new_appointment->get_id() ); ?>';
				</script>
				<?php
				exit;

			}
		} catch ( Exception $e ) {
			$this->errors[] = $e->getMessage();
		}

		switch ( $step ) {
			case 1 :
				include( 'views/html-add-appointment-page.php' );
			break;
			case 2 :
				add_filter( 'wc_get_template', array( $this, 'use_default_form_template' ), 10, 5 );
				include( 'views/html-add-appointment-page-2.php' );
				remove_filter( 'wc_get_template', array( $this, 'use_default_form_template' ), 10 );
			break;
		}
	}

	/**
	 * Create order
	 *
	 * @param  float $total
	 * @param  int $customer_id
	 * @return int
	 */
	public function create_order( $total, $customer_id ) {
		if ( function_exists( 'wc_create_order' ) ) {
			$order = wc_create_order( array(
				'customer_id' => absint( $customer_id ),
			) );
			$order_id = $order->get_id();
			$order->set_total( $total );
			update_post_meta( $order_id, '_appointment_order', '1' );
		} else {
			$order = new WC_Order();
			$order->set_customer_id( $customer_id );
			$order->set_total( $total );
			$order->set_created_via( 'appointments' );
			$order_id = $order->save();
		}

		do_action( 'woocommerce_new_appointment_order', $order_id );

		return $order_id;
	}

	/**
	 * Output any errors
	 */
	public function show_errors() {
		foreach ( $this->errors as $error )
			echo '<div class="wcfm-message wcfm-error" style="display: block;"><span class="wcicon-status-cancelled"></span>' . esc_html( $error ) . '</div>';
	}

	/**
	 * Use default template form from the extension.
	 *
	 * This prevents any overridden template via theme being used in
	 * add appointment screen.
	 *
	 * @since 2.1.4
	 */
	public function use_default_form_template( $located, $template_name, $args, $template_path, $default_path ) {
		if ( 'woocommerce-appointments' === $template_path ) {
			$located = $default_path . $template_name;
		}
		return $located;
	}
}
