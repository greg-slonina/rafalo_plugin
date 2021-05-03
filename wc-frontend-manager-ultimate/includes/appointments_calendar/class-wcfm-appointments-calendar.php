<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
/**
 * WC_Appointments_Admin_Calendar class.
 */
class WCFM_Appointments_Calendar {

	/**
	 * Stores Appointments.
	 *
	 * @var array
	 */
	private $appointments;

	/**
	 * Output the calendar view
	 */
	public function output() {
		$filter_view  = apply_filters( 'woocommerce_appointments_calendar_view', 'week' );
		$user_view    = get_user_meta( get_current_user_id(), 'calendar_view', true );
		$default_view = $user_view ? $user_view : $filter_view;
		$view         = isset( $_REQUEST['view'] ) ? $_REQUEST['view'] : $default_view;
		$staff_list   = WC_Appointments_Admin::get_appointment_staff();

		$product_filter = isset( $_REQUEST['filter_appointable_product'] ) ? absint( $_REQUEST['filter_appointable_product'] ) : '';
		$staff_filter   = isset( $_REQUEST['filter_appointable_staff'] ) ? absint( $_REQUEST['filter_appointable_staff'] ) : '';

		// Override to only show appointments for current staff member.
		if ( ! current_user_can( 'manage_others_appointments' ) ) {
			$staff_filter = get_current_user_id();
		}

		// Update calendar view seletion.
		if ( isset( $_REQUEST['view'] ) ) {
			update_user_meta( get_current_user_id(), 'calendar_view', $_REQUEST['view'] );
		}

		if ( in_array( $view, array( 'day', 'staff' ) ) ) {
			$day           = isset( $_REQUEST['calendar_day'] ) ? wc_clean( $_REQUEST['calendar_day'] ) : date( 'Y-m-d' );
			$day_formatted = date( 'Y-m-d', strtotime( $day ) );
			$prev_day      = date( 'Y-m-d', strtotime( '-1 day', strtotime( $day ) ) );
			$next_day      = date( 'Y-m-d', strtotime( '+1 day', strtotime( $day ) ) );

			$args_filters = array(
				'order_by' => 'start_date',
				'order'    => 'ASC',
			);

			if( defined( 'WC_APPOINTMENTS_VERSION' ) && version_compare( WC_APPOINTMENTS_VERSION, '4.7.0', '>=' ) ) {
				$this->appointments = WC_Appointments_Availability_Data_Store::get_events_in_date_range(
					strtotime( 'midnight', strtotime( $day ) ),
					strtotime( 'midnight +1 day', strtotime( $day ) ),
					$product_filter,
					$staff_filter,
					false,
					$args_filters
				);
			} else {
				$this->appointments = WC_Appointments_Controller::get_appointments_in_date_range(
					strtotime( 'midnight', strtotime( $day ) ),
					strtotime( 'midnight +1 day', strtotime( $day ) ),
					$product_filter,
					$staff_filter,
					false,
					$args_filters
				);
			}
		} elseif ( 'week' === $view ) {
			$day            = isset( $_REQUEST['calendar_day'] ) ? wc_clean( $_REQUEST['calendar_day'] ) : date( 'Y-m-d' );
			$day_formatted  = date( 'Y-m-d', strtotime( $day ) );
			$week           = date( 'w', strtotime( $day ) );
			$start_of_week  = absint( get_option( 'start_of_week', 1 ) );
			$week_start     = strtotime( "previous sunday +{$start_of_week} day", strtotime( $day ) );
			$week_end       = strtotime( '+1 week -1 min', $week_start );
			$week_formatted = date( wc_date_format(), $week_start ) . ' &mdash; ' . date( wc_date_format(), $week_end );
			$prev_week      = date( 'Y-m-d', strtotime( '-1 week', strtotime( $day ) ) );
			$next_week      = date( 'Y-m-d', strtotime( '+1 week', strtotime( $day ) ) );

			#$prev_day = date_i18n( wc_date_format(), strtotime( '-1 day', strtotime( $day ) ) );

			$args_filters = array(
				'order_by' => 'start_date',
				'order'    => 'ASC',
			);

			if( defined( 'WC_APPOINTMENTS_VERSION' ) && version_compare( WC_APPOINTMENTS_VERSION, '4.7.0', '>=' ) ) {
				$this->appointments = WC_Appointments_Availability_Data_Store::get_events_in_date_range(
					$week_start,
					$week_end,
					$product_filter,
					$staff_filter,
					false,
					$args_filters
				);
			} else {
				$this->appointments = WC_Appointments_Controller::get_appointments_in_date_range(
					$week_start,
					$week_end,
					$product_filter,
					$staff_filter,
					false,
					$args_filters
				);
			}
		} else {
			$month = isset( $_REQUEST['calendar_month'] ) ? absint( $_REQUEST['calendar_month'] ) : date( 'n' );
			$year  = isset( $_REQUEST['calendar_year'] ) ? absint( $_REQUEST['calendar_year'] ) : date( 'Y' );

			if ( $year < ( date( 'Y' ) - 10 ) || $year > 2100 )
				$year = date( 'Y' );

			if ( $month > 12 ) {
				$month = 1;
				$year ++;
			}

			if ( $month < 1 ) {
				$month = 12;
				$year --;
			}

			$start_of_week = absint( get_option( 'start_of_week', 1 ) );
			$last_day      = date( 't', strtotime( "$year-$month-01" ) );
			$start_date_w  = absint( date( 'w', strtotime( "$year-$month-01" ) ) );
			$end_date_w    = absint( date( 'w', strtotime( "$year-$month-$last_day" ) ) );

			// Calc day offset
			$day_offset = $start_date_w - $start_of_week;
			$day_offset = $day_offset >= 0 ? $day_offset : 7 - abs( $day_offset );

			// Calc end day offset
			$end_day_offset = 7 - ( $last_day % 7 ) - $day_offset;
			$end_day_offset = $end_day_offset >= 0 && $end_day_offset < 7 ? $end_day_offset : 7 - abs( $end_day_offset );

			// We want to get the last minute of the day, so we will go forward one day to midnight and subtract a min
			$end_day_offset = $end_day_offset + 1;

			$start_time = strtotime( "-{$day_offset} day", strtotime( "$year-$month-01" ) );
			$end_time   = strtotime( "+{$end_day_offset} day midnight -1 min", strtotime( "$year-$month-$last_day" ) );

			$args_filters = array(
				'order_by' => 'start_date',
				'order'    => 'ASC',
			);

			if( defined( 'WC_APPOINTMENTS_VERSION' ) && version_compare( WC_APPOINTMENTS_VERSION, '4.7.0', '>=' ) ) {
				$this->appointments = WC_Appointments_Availability_Data_Store::get_events_in_date_range(
					$start_time,
					$end_time,
					$product_filter,
					$staff_filter,
					false,
					$args_filters
				);
			} else {
				$this->appointments = WC_Appointments_Controller::get_appointments_in_date_range(
					$start_time,
					$end_time,
					$product_filter,
					$staff_filter,
					false,
					$args_filters
				);
			}
		}

		include( 'views/html-calendar-' . $view . '.php' );
	}

	/**
	 * List appointments for a day
	 *
	 * @param  [type] $day
	 * @param  [type] $month
	 * @param  [type] $year
	 * @return [type]
	 */
	public function list_appointments( $day, $month, $year, $list = 'by_time', $staff_id = '' ) {
		$date_start = strtotime( "$year-$month-$day midnight" ); // Midnight today.
		$date_end   = strtotime( "$year-$month-$day tomorrow" ); // Midnight next day.

		foreach ( $this->appointments as $appointment ) {
			
			$event_type       = is_a( $appointment, 'WC_Appointment' ) ? 'appointment' : 'availability';
			$event_is_all_day = $appointment->is_all_day();
			// Get start and end timestamps.
			if ( 'appointment' === $event_type ) {
				$event_start = $appointment->get_start();
				$event_end   = $appointment->get_end();
			} else {
				$range = $appointment->get_time_range_for_date( $date_start );
				if ( is_null( $range ) ) {
					continue;
				}
				$event_start = $range['start'];
				$event_end   = $range['end'];
				$event_is_all_day = false; #Set all availability to be displayed as hourly.
			}
			
			if ( 'all_day' === $list && $event_is_all_day && $event_start < $date_end && $event_end > $date_start ) {
				if ( $staff_id ) {
					$staff_ids = $appointment->get_staff_ids();
					$staff_ids = ! is_array( $staff_ids ) ? array( $staff_ids ) : $staff_ids;
					if ( in_array( $staff_id, $staff_ids ) ) {
						$this->single_appointment_card( $appointment, $event_start, $event_end, $list = 'all_day' );
					} elseif ( ! $staff_ids && 'unassigned' === $staff_id ) {
						$this->single_appointment_card( $appointment, $event_start, $event_end, $list = 'all_day' );
					}
				} else {
					$this->single_appointment_card( $appointment, $event_start, $list = 'all_day' );
				}
			} elseif ( 'by_time' === $list && ! $event_is_all_day && $event_start < $date_end && $event_end > $date_start ) {
				if ( $staff_id ) {
					$staff_ids = $appointment->get_staff_ids();
					$staff_ids = ! is_array( $staff_ids ) ? array( $staff_ids ) : $staff_ids;
					if ( in_array( $staff_id, $staff_ids ) ) {
						$this->single_appointment_card( $appointment, $event_start, $event_end, $list = 'by_time' );
					} elseif ( ! $staff_ids && 'unassigned' === $staff_id ) {
						$this->single_appointment_card( $appointment, $event_start, $event_end, $list = 'by_time' );
					}
				} else {
					$this->single_appointment_card( $appointment, $event_start, $event_end, $list = 'by_time' );
				}
			} elseif ( 'by_month' === $list && $event_start < $date_end && $event_end > $date_start ) {
				if ( $staff_id ) {
					$staff_ids = $appointment->get_staff_ids();
					$staff_ids = ! is_array( $staff_ids ) ? array( $staff_ids ) : $staff_ids;
					if ( in_array( $staff_id, $staff_ids ) ) {
						$this->single_appointment_card( $appointment, $event_start, $event_end, $list = 'by_month' );
					} elseif ( ! $staff_ids && 'unassigned' === $staff_id ) {
						$this->single_appointment_card( $appointment, $event_start, $event_end, $list = 'by_month' );
					}
				} else {
					$this->single_appointment_card( $appointment, $event_start, $event_end, $list = 'by_month' );
				}
			}
		}
	}

	/**
	 * Single appointments card
	 */
	public function single_appointment_card( $appointment, $event_start, $event_end, $list = '' ) {
		if ( ! is_a( $appointment, 'WC_Appointment' ) ) {
			echo '';
		}

		$style = '';

		// Array
		$datarray = array();
		$datarray['type']           = is_a( $appointment, 'WC_Appointment' ) ? 'appointment' : 'availability';
		
		// Data
		if ( 'all_day' == $list ) {
			$datarray['start_time'] = date( 'Y-m-d', $event_start );
			$datarray['end_time']   = date( 'Y-m-d', $event_end );
		} else {
			$datarray['start_time'] = date( 'Hi', $event_start );
			$datarray['end_time']   = date( 'Hi', $event_end );
		}
		$datarray['appointment_start'] = $event_start; //$appointment->get_start();
		$datarray['appointment_end']   = $event_end; //$appointment->get_end();
		$datarray['order_id']          = wp_get_post_parent_id( $appointment->get_id() );
		if ( 'appointment' === $datarray['type'] ) {
			$datarray['staff_id']          = $appointment->get_staff_ids();
			if ( ! is_array( $datarray['staff_id'] ) ) {
				$datarray['staff_id'] = array( $datarray['staff_id'] );
			}
			$datarray['staff_name']           = $appointment->get_staff_members( true ) ? htmlentities( $appointment->get_staff_members( $names = true, $with_link = true ) ) : '';
			
			if ( $datarray['order_id'] ) {
				$order                    = wc_get_order( $datarray['order_id'] );
				$datarray['appointment_status'] = is_a( $order, 'WC_Order' ) ? $order->get_status() : '';
			}
			$datarray['appointment_status_label'] = wc_appointments_get_status_label( $appointment->get_status() );
			
			$datarray['customer_status']          = $appointment->get_customer_status();
			$datarray['customer_status']          = $datarray['customer_status'] ? $datarray['customer_status'] : 'expected';
			$customer                             = $appointment->get_customer();
			$datarray['customer_id']              = '';
			$datarray['customer_name']            = __( 'Guest', 'woocommerce-appointments' );
			$datarray['customer_phone']           = '';
			$datarray['customer_email']           = '';
			$datarray['customer_url']             = '';
			$datarray['customer_avatar']          = get_avatar_url(
				'',
				array(
					'size'    => 100,
					'default' => 'mm',
				)
			);
			if ( $customer && $customer->user_id ) {
				$user                    = get_user_by( 'id', $customer->user_id );
				$datarray['customer_id'] = $customer->user_id;
				if ( '' != $user->first_name || '' != $user->last_name ) {
					$datarray['customer_name'] = $user->first_name . ' ' . $user->last_name;
				} else {
					$datarray['customer_name'] = $user->display_name;
				}
				$datarray['customer_phone']  = preg_replace( '/\s+/', '', $customer->phone );
				$datarray['customer_email']  = $customer->email;
				$datarray['customer_url']    = get_edit_user_link( $datarray['customer_id'] );
				$datarray['customer_avatar'] = get_avatar_url(
					$datarray['customer_id'],
					array(
						'size'    => 110,
						'default' => 'mm',
					)
				);
			}
			
			$appointment_product       = $appointment->get_product();
			$datarray['product_id']    = $appointment->get_product_id();
			$datarray['product_title'] = is_object( $appointment_product ) ? $appointment_product->get_title() : '';
			$appointment_color         = is_object( $appointment_product ) && $appointment_product->get_cal_color() ? $appointment_product->get_cal_color() : '#0073aa';
			
			$customer = $appointment->get_customer();
			if ( $customer && isset( $customer->name ) && ! empty( $customer->name ) ) {
				$datarray['appointment_customer'] = $customer->name;
			} else {
				$datarray['appointment_customer'] = __( 'Guest', 'woocommerce-appointments' );
			}
		}
		$datarray['appointment_when']     = wc_appointment_format_timestamp( $event_start, $appointment->is_all_day() ); //$appointment->get_start_date();
		$datarray['appointment_duration'] = wc_appointment_duration_in_minutes( $event_start, $event_end); //$appointment->get_duration();
		$datarray['appointment_qty']      = $appointment->get_qty();
		$datarray['appointment_cost']     = '';
		$datarray['order_status']         = '';
		
		if ( $datarray['order_id'] = wp_get_post_parent_id( $appointment->get_id() ) ) {
			$order                        = wc_get_order( $datarray['order_id'] );
			$datarray['appointment_cost'] = is_a( $order, 'WC_Order' ) ? esc_html( $order->get_formatted_order_total() ) : '';
			$datarray['order_status']     = is_a( $order, 'WC_Order' ) ? $order->get_status() : '';
			$datarray['customer_phone']   = is_a( $order, 'WC_Order' ) ? esc_html( $order->get_billing_phone() ) : '';
		}
		
		$calendar_scale            = apply_filters( 'woocommerce_appointments_calendar_view_day_scale', 60 );
		$appointment_top           = ( ( intval( substr( $datarray['start_time'], 0, 2 ) ) * 60 ) + intval( substr( $datarray['start_time'], -2 ) ) ) / 60 * $calendar_scale;

		if ( $appointment->is_all_day() ) {
			$datarray['appointment_datetime'] = date( 'Y-m-d', $event_start );
		} else {
			$datarray['appointment_datetime'] = date( 'H:i', $event_start ); //$appointment->get_start_date( '', wc_time_format() );
		}

		if ( 'by_time' === $list ) {
			$duration_minutes = ( function_exists( 'wc_appointment_duration_in_minutes' ) ) ? wc_appointment_duration_in_minutes( $event_start, $event_end, false ) : $appointment->get_duration( true );
			$height           = intval( ( $duration_minutes / 60 ) * $calendar_scale );
			$style           .= ' background: ' . $appointment_color . '; top: ' . $appointment_top . 'px; height: ' . $height . 'px;';
		} else {
			$style .= ' background: ' . $appointment_color;
		}

		// Add appointment info into data- attributes.
		$card_data_attr = '
			data-appointment-id="' . $appointment->get_id() . '"
			data-product-id="' . $datarray['product_id'] . '"
			data-product-title="' . $datarray['product_title'] . '"
			data-order-id="' . $datarray['order_id'] . '"
			data-order-status="' . $datarray['order_status'] . '"
			data-appointment-cost="' . $datarray['appointment_cost'] . '"
			data-appointment-start="' . $datarray['appointment_start'] . '"
			data-appointment-end="' . $datarray['appointment_end'] . '"
			data-appointment-when="' . $datarray['appointment_when'] . '"
			data-appointment-duration="' . $datarray['appointment_duration'] . '"
			data-appointment-qty="' . $datarray['appointment_qty'] . '"
			data-appointment-status="' . $datarray['appointment_status'] . '"
			data-appointment-status-label="' . $datarray['appointment_status_label'] . '"
			data-appointment-staff="' . $datarray['staff_name'] . '"
			data-customer-status="' . $datarray['customer_status'] . '"
			data-customer-id="' . $datarray['customer_id'] . '"
			data-customer-url="' . $datarray['customer_url'] . '"
			data-customer-name="' . $datarray['customer_name'] . '"
			data-customer-phone="' . $datarray['customer_phone'] . '"
			data-customer-email="' . $datarray['customer_email'] . '"
			data-customer-avatar="' . $datarray['customer_avatar'] . '"
		';
		$card_data_html = apply_filters( 'woocommerce_appointments_calendar_single_card_data', $card_data_attr, $datarray, $appointment );

		// Build card variables.
		$product_title            = $datarray['product_title'];
		$appointment_status       = $datarray['appointment_status'];
		$appointment_status_label = $datarray['appointment_status_label'];
		$customer_status          = $datarray['customer_status'];
		$appointment_link         = get_wcfm_view_appointment_url( $appointment->get_id() );
		$card_title               = __( 'View / Edit', 'woocommerce-appointments' );
		$appointment_datetime     = $datarray['appointment_datetime'];
		
		$appointment_customer     = '';
		if( apply_filters( 'wcfm_allow_order_customer_details', true ) ) {
			$appointment_customer     = $datarray['appointment_customer'];
		}

		// Build card html.
		$card_html = "
			<div class='single_appointment wcfm_appointment_card status_$appointment_status customer_status_$customer_status' title='$card_title' $card_data_html style='$style'>
				<a href='$appointment_link'>
					<strong class='appointment_datetime'>$appointment_datetime</strong>
					<ul>
					  <li class='appointment_product'>$product_title</li>
						<li class='appointment_customer status-$customer_status'>$appointment_customer</li>
						<li class='appointment_status status-$appointment_status data-tip='$appointment_status_label'></li>
					</ul>
				</a>
			</div>
		";

		echo apply_filters( 'woocommerce_appointments_calendar_view_single_card', $card_html, $datarray, $appointment );
	}

	/**
	 * Filters products for narrowing search
	 */
	public function product_filters() {
		$filters = array();

		foreach ( WC_Appointments_Admin::get_appointment_products() as $product ) {
			$filters[ $product->get_id() ] = $product->get_title();
		}

		return $filters;
	}

	/**
	 * Filters staff for narrowing search
	 */
	public function staff_filters() {
		$filters = array();

		// Only show staff filter if current user can see other staff's appointments.
		if ( ! current_user_can( 'manage_others_appointments' ) ) {
			return $filters;
		}

		$staff = WC_Appointments_Admin::get_appointment_staff();

		foreach ( $staff as $staff_member ) {
			$filters[ $staff_member->ID ] = $staff_member->display_name;
		}

		return $filters;
	}

	/**
	 * Calendar Head: Create columns for staff
	 */
	public function staff_columns( $count = 0 ) {

		$current_user = wp_get_current_user();
		$user_name = $current_user->user_login;

		$staff = WC_Appointments_Admin::get_appointment_staff();

		switch ( $count ) {
			case 1:
			$staff_count = count( $staff );
			return $staff_count;

			case 0:
			foreach ( $staff as $user ) {
				$staff_name = esc_html( $user->display_name );
				$staff_url = get_edit_user_link( $user->ID );
				$staff_id = $user->ID;
				echo '<li class="header_column" data-staff-id="' . $staff_id . '"';
				if ( $user_name == $staff_name ) {
					echo 'id="current_user"';
				}
				echo '><a href="' . $staff_url . '#staff-details" title="' . __( 'Edit User and Availability', 'woocommerce-appointments' ) . '">' . get_avatar( $staff_id, 40, 'mm' ) . '<span class="staffname">' . $staff_name . '</span></a></li>';
			}
			// Unassigned Appointments
			echo '<li id="unassigned_staff" class="secondary"><span class="staffname">' . __( 'Unassigned', 'woocommerce-appointments' ) . '</span></li>';
		}
	}

}
