<?php
/**
 * WCFM plugin views
 *
 * Plugin WC Appointment Products Manage Views
 *
 * @author 		WC Lovers
 * @package 	wcfmu/views
 * @version   2.4.0
 */
global $wp, $WCFM, $WCFMu;

$duration = 1;
$duration_unit = 'hour';
$interval = 1;
$interval_unit = 'hour';
$padding_duration = 0;
$padding_duration_unit = 'hour';
$min_date = 0;
$min_date_unit = 'day';
$max_date = 12;
$max_date_unit = 'month';
$requires_confirmation = 'no';
$user_can_cancel = 'no';
$cancel_limit = '1';
$cancel_limit_unit = 'day';
$customer_timezones = '';

$has_price_label = 'no';
$price_label = '';
$has_pricing = 'no';
$cost_rule_values = array();
$cost_default_rules = array(  "type"            => 'custom',
															"from_custom"     => '',
															"to_custom"       => '',
															"from_months"     => '',
															"to_months"       => '',
															"from_weeks"      => '',
															"to_weeks"        => '',
															"from_days"       => '',
															"to_days"         => '', 
															"from_time"       => '',
															"to_time"         => '',
															"from_count"      => '',
															"to_count"        => '',
															"base_modifier"   => '',
															"base_cost"       => '',
															"block_modifier"  => '',
															"block_cost"      => ''
														);
$cost_rule_values[0] = $cost_default_rules;

$availability_span = '';
$availability_autoselect = 'no';
$has_restricted_days = 'no';
$restricted_days = array();
$availability_rule_values = array();
$availability_default_rules = array(  "type"         => 'custom',
	                                    "avail_id"     => '',
	                                    "kind_id"      => '',
																			"from_custom"  => '',
																			"to_custom"    => '',
																			"from_months"  => '',
																			"to_months"    => '',
																			"from_weeks"   => '',
																			"to_weeks"     => '',
																			"from_days"    => '',
																			"to_days"      => '', 
																			"from_time"    => '',
																			"to_time"      => '', 
																			"appointable"  => '',
																			"priority"     => '10',
																			"qty"          => ''
																		);
$availability_rule_values[0] = $availability_default_rules;

$staff_label = '';
$staff_assignment = '';
$staffs = array();

if( isset( $wp->query_vars['wcfm-products-manage'] ) && !empty( $wp->query_vars['wcfm-products-manage'] ) ) {
	$product_id = $wp->query_vars['wcfm-products-manage'];
	if( $product_id ) {
		
		$appointable_product = new WC_Product_Appointment( $product_id );
		
		$duration      = max( absint( $appointable_product->get_duration( 'edit' ) ), 1 );
		$duration_unit = $appointable_product->get_duration_unit( 'edit' );
		if ( '' == $duration_unit ) {
			$duration_unit = 'hour';
		}
		
		$interval_s    = $appointable_product->get_interval( 'edit' );
		if ( '' == $interval_s ) {
			$interval = $duration;
		} else {
			$interval = max( absint( $interval_s ), 1 );
		}
		$interval_unit = $appointable_product->get_interval_unit( 'edit' );
		if ( '' == $interval_unit ) {
			$interval_unit = $duration_unit;
		} elseif ( 'day' == $interval_unit ) {
			$interval_unit = 'hour';
		}
	}
	
	$padding_duration      = absint( $appointable_product->get_padding_duration( 'edit' ) );
	$padding_duration_unit = $appointable_product->get_padding_duration_unit( 'edit' );
	if ( '' == $padding_duration_unit ) {
		$padding_duration_unit = 'hour';
	}
	
	$min_date      = absint( $appointable_product->get_min_date( 'edit' ) );
	$min_date_unit = $appointable_product->get_min_date_unit( 'edit' );
	if ( '' == $min_date_unit ) {
		$min_date_unit = 'day';
	}
	
	$max_date = $appointable_product->get_max_date( 'edit' );
	if ( '' == $max_date ) {
		$max_date = 12;
	}
	$max_date      = max( absint( $max_date ), 1 );
	$max_date_unit = $appointable_product->get_max_date_unit( 'edit' );
	if ( '' == $max_date_unit ) {
		$max_date_unit = 'month';
	}
	
	$requires_confirmation = $appointable_product->get_requires_confirmation( 'edit' ) ? 'yes' : 'no';
	$user_can_cancel = $appointable_product->get_user_can_cancel( 'edit' ) ? 'yes' : 'no';
	
	$cancel_limit      = max( absint( $appointable_product->get_cancel_limit( 'edit' ) ), 1 );
	$cancel_limit_unit = $appointable_product->get_cancel_limit_unit( 'edit' );
	if ( '' == $cancel_limit_unit ) {
		$cancel_limit_unit = 'day';
	}
	
	$customer_timezones = $appointable_product->get_customer_timezones( 'edit' ) ? 'yes' : 'no';
	
	// Pricing
	$has_price_label = $appointable_product->get_has_price_label( 'edit' ) ? 'yes' : 'no';
	$price_label = $appointable_product->get_price_label( 'edit' );
	$has_pricing = $appointable_product->get_has_pricing( 'edit' ) ? 'yes' : 'no';
	$cost_rules = $appointable_product->get_pricing( 'edit' );
	
	if( !empty( $cost_rules ) ) {
		foreach( $cost_rules as $a_index => $cost_rule ) {
			$cost_rule_values[$a_index] = $cost_default_rules;
			$cost_rule_values[$a_index]['type'] = $cost_rule['type'];
			if($cost_rule['type'] == 'custom' ) {
				$cost_rule_values[$a_index]['from_custom'] = $cost_rule['from'];
				$cost_rule_values[$a_index]['to_custom']   = $cost_rule['to'];
			} elseif($cost_rule['type'] == 'months' ) {
				$cost_rule_values[$a_index]['from_months'] = $cost_rule['from'];
				$cost_rule_values[$a_index]['to_months']   = $cost_rule['to'];
			} elseif($cost_rule['type'] == 'weeks' ) {
				$cost_rule_values[$a_index]['from_weeks'] = $cost_rule['from'];
				$cost_rule_values[$a_index]['to_weeks']   = $cost_rule['to'];
			} elseif($cost_rule['type'] == 'days' ) {
				$cost_rule_values[$a_index]['from_days'] = $cost_rule['from'];
				$cost_rule_values[$a_index]['to_days']   = $cost_rule['to'];
			} elseif($cost_rule['type'] == 'quant' ) {
				$cost_rule_values[$a_index]['from_count'] = $cost_rule['from'];
				$cost_rule_values[$a_index]['to_count']   = $cost_rule['to'];
			} elseif($cost_rule['type'] == 'blocks' ) {
				$cost_rule_values[$a_index]['from_count'] = $cost_rule['from'];
				$cost_rule_values[$a_index]['to_count']   = $cost_rule['to'];
			} elseif($cost_rule['type'] == 'time:range' ) {
				$cost_rule_values[$a_index]['from_custom'] = $cost_rule['from_date'];
				$cost_rule_values[$a_index]['to_custom']   = $cost_rule['to_date'];
				$cost_rule_values[$a_index]['from_time'] = $cost_rule['from'];
				$cost_rule_values[$a_index]['to_time']   = $cost_rule['to'];
			} else {
				$cost_rule_values[$a_index]['from_time'] = $cost_rule['from'];
				$cost_rule_values[$a_index]['to_time']   = $cost_rule['to'];
			}
			$cost_rule_values[$a_index]['base_modifier']  = $cost_rule['base_modifier'];
			$cost_rule_values[$a_index]['base_cost']      = $cost_rule['base_cost'];
			$cost_rule_values[$a_index]['block_modifier'] = $cost_rule['modifier'];
			$cost_rule_values[$a_index]['block_cost']     = $cost_rule['cost'];
		}
	}
	
	// Availability
	$availability_span = $appointable_product->get_availability_span( 'edit' );
	$availability_autoselect = $appointable_product->get_availability_autoselect( 'edit' ) ? 'yes' : 'no';
	$has_restricted_days     = $appointable_product->has_restricted_days( 'edit' ) ? 'yes' : 'no';
	$restricted_days         = $appointable_product->get_restricted_days( 'edit' );
	if( !$restricted_days ) $restricted_days = array();
	
	$availability_rules = WC_Data_Store::load( 'appointments-availability' )->get_all(
			array(
				array(
					'key'     => 'kind',
					'compare' => '=',
					'value'   => 'availability#product',
				),
				array(
					'key'     => 'kind_id',
					'compare' => '=',
					'value'   => $product_id,
				),
			)
		);
			
	if( !empty( $availability_rules ) ) {
		foreach( $availability_rules as $a_index => $availability_rule ) {
			$availability_rule_values[$a_index] = $availability_default_rules;
			$availability_rule_values[$a_index]['type'] = $availability_rule['type'];
			if($availability_rule['type'] == 'custom' ) {
				$availability_rule_values[$a_index]['from_custom'] = $availability_rule['from'];
				$availability_rule_values[$a_index]['to_custom']   = $availability_rule['to'];
			} elseif($availability_rule['type'] == 'months' ) {
				$availability_rule_values[$a_index]['from_months'] = $availability_rule['from'];
				$availability_rule_values[$a_index]['to_months']   = $availability_rule['to'];
			} elseif($availability_rule['type'] == 'weeks' ) {
				$availability_rule_values[$a_index]['from_weeks'] = $availability_rule['from'];
				$availability_rule_values[$a_index]['to_weeks']   = $availability_rule['to'];
			} elseif($availability_rule['type'] == 'days' ) {
				$availability_rule_values[$a_index]['from_days'] = $availability_rule['from'];
				$availability_rule_values[$a_index]['to_days']   = $availability_rule['to'];
			} elseif($availability_rule['type'] == 'custom:daterange' ) {
				$availability_rule_values[$a_index]['from_custom'] = $availability_rule['from_date'];
				$availability_rule_values[$a_index]['to_custom']   = $availability_rule['to_date'];
				$availability_rule_values[$a_index]['from_time'] = $availability_rule['from'];
				$availability_rule_values[$a_index]['to_time']   = $availability_rule['to'];
			} elseif($availability_rule['type'] == 'time:range' ) {
				$availability_rule_values[$a_index]['from_custom'] = $availability_rule['from_date'];
				$availability_rule_values[$a_index]['to_custom']   = $availability_rule['to_date'];
				$availability_rule_values[$a_index]['from_time'] = $availability_rule['from'];
				$availability_rule_values[$a_index]['to_time']   = $availability_rule['to'];
			} else {
				$availability_rule_values[$a_index]['from_time'] = $availability_rule['from'];
				$availability_rule_values[$a_index]['to_time']   = $availability_rule['to'];
			}
			$availability_rule_values[$a_index]['avail_id'] = isset( $availability_rule['ID'] ) ? $availability_rule['ID'] : '';
			$availability_rule_values[$a_index]['kind_id']  = $product_id;
			$availability_rule_values[$a_index]['qty']      = $availability_rule['qty'];
			$availability_rule_values[$a_index]['priority'] = $availability_rule['priority'];
			$availability_rule_values[$a_index]['appointable'] = $availability_rule['appointable'];
		}
	}
	
	// Staff
	$staff_label = $appointable_product->get_staff_label( 'edit' );
	$staff_assignment = $appointable_product->get_staff_assignment( 'edit' );
	$product_staffs		= $appointable_product->get_staff_ids( 'edit' );
	$staff_base_costs	= $appointable_product->get_staff_base_costs( 'edit' );
	$staff_qtys       = $appointable_product->get_staff_qtys( 'edit' );
	
	$loop                 = 0;

	if ( $product_staffs ) {
		foreach ( $product_staffs as $staff_id ) {
			$staff            = new WC_Product_Appointment_Staff( $staff_id );
			$staffs[$loop]['staff_id'] = $staff->get_id();
			$staffs[$loop]['staff_title'] = $staff->display_name;
			$staffs[$loop]['staff_base_cost'] = isset( $staff_base_costs[ $staff_id ] ) ? $staff_base_costs[ $staff_id ] : '';
			$staffs[$loop]['staff_qty']       = isset( $staff_qtys[ $staff_id ] ) ? $staff_qtys[ $staff_id ] : '';
			$loop++;
		}
	}
}


$intervals = array();

$intervals['months'] = array(
	'1'  => __( 'January', 'woocommerce-appointments' ),
	'2'  => __( 'February', 'woocommerce-appointments' ),
	'3'  => __( 'March', 'woocommerce-appointments' ),
	'4'  => __( 'April', 'woocommerce-appointments' ),
	'5'  => __( 'May', 'woocommerce-appointments' ),
	'6'  => __( 'June', 'woocommerce-appointments' ),
	'7'  => __( 'July', 'woocommerce-appointments' ),
	'8'  => __( 'August', 'woocommerce-appointments' ),
	'9'  => __( 'September', 'woocommerce-appointments' ),
	'10' => __( 'October', 'woocommerce-appointments' ),
	'11' => __( 'November', 'woocommerce-appointments' ),
	'12' => __( 'December', 'woocommerce-appointments' ),
);

$intervals['days'] = array(
	'1' => __( 'Monday', 'woocommerce-appointments' ),
	'2' => __( 'Tuesday', 'woocommerce-appointments' ),
	'3' => __( 'Wednesday', 'woocommerce-appointments' ),
	'4' => __( 'Thursday', 'woocommerce-appointments' ),
	'5' => __( 'Friday', 'woocommerce-appointments' ),
	'6' => __( 'Saturday', 'woocommerce-appointments' ),
	'7' => __( 'Sunday', 'woocommerce-appointments' ),
);

for ( $i = 1; $i <= 53; $i ++ ) {
	$intervals['weeks'][ $i ] = sprintf( __( 'Week %s', 'woocommerce-appointments' ), $i );
}

$range_types = array(
											'custom'     => __( 'Date range', 'woocommerce-appointments' ),
											'months'     => __( 'Range of months', 'woocommerce-appointments' ),
											'weeks'      => __( 'Range of weeks', 'woocommerce-appointments' ),
											'days'       => __( 'Range of days', 'woocommerce-appointments' ),
											'quant'      => __( 'Quantity count', 'woocommerce-appointments' ),
											//'slots'     => __( 'Slot count', 'woocommerce-appointments' ),
											'time'       => '&nbsp;&nbsp;&nbsp;' .  __( 'Recurring Time (all week)', 'woocommerce-appointments' ),
											'time:range' => '&nbsp;&nbsp;&nbsp;' . __( 'Recurring Time (date range)', 'woocommerce-appointments' )
										);

$availability_range_types = array(
											'custom'     => __( 'Date range', 'woocommerce-appointments' ),
											'custom:daterange' => __( 'Date range with time', 'woocommerce-appointments' ),
											'months'     => __( 'Range of months', 'woocommerce-appointments' ),
											'weeks'      => __( 'Range of weeks', 'woocommerce-appointments' ),
											'days'       => __( 'Range of days', 'woocommerce-appointments' ),
											'time'       => '&nbsp;&nbsp;&nbsp;' .  __( 'Recurring Time (all week)', 'woocommerce-appointments' ),
											'time:range' => '&nbsp;&nbsp;&nbsp;' . __( 'Recurring Time (date range)', 'woocommerce-appointments' )
										);
		
foreach ( $intervals['days'] as $key => $label ) :
	$range_types['time:' . $key] = '&nbsp;&nbsp;&nbsp;' . $label;
	$availability_range_types['time:' . $key] = '&nbsp;&nbsp;&nbsp;' . $label;
endforeach;

$args = array(
							'role__in'     => array( 'shop_staff' ),
							'orderby'      => 'ID',
							'order'        => 'ASC',
							'offset'       => 0,
							'number'       => -1,
							'count_total'  => false
						 ); 

$args = apply_filters( 'get_appointment_staff_args', $args );
$wcfm_appointments_staffs = get_users( $args );
$all_staffs = array( -1 => __( 'Choose Staff', 'wc-frontend-manager-ultimate' ) );
if( !empty( $wcfm_appointments_staffs ) ) {
	foreach( $wcfm_appointments_staffs as $wcfm_appointments_staff ) {
		$all_staffs[$wcfm_appointments_staff->ID] = $wcfm_appointments_staff->display_name;
	}
}

?>

<!-- collapsible Appointment 1 -->
<div class="page_collapsible products_manage_appointment appointment" id="wcfm_products_manage_form_appointment_options_head"><label class="wcfmfa fa-calendar"></label><?php _e('Appointable', 'wc-frontend-manager-ultimate'); ?><span></span></div>
<div class="wcfm-container appointment">
	<div id="wcfm_products_manage_form_appointment_expander" class="wcfm-content">
		<?php
			$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_wcappointments_general_fields', array(  
				"_wc_appointment_duration" => array( 'label' => __('Duration', 'woocommerce-appointments'), 'type' => 'number', 'class' => 'wcfm-text wcfm_ele wcfm_appointment_unit_ele appointment', 'label_class' => 'wcfm_title wcfm_appointment_unit_label appointment', 'value' => $duration, 'hints' => __( 'How long do you plan this appointment to last?', 'woocommerce-appointments' ) ),
				"_wc_appointment_duration_unit" => array('type' => 'select', 'options' => array( 'minute' => __( 'Minute(s)', 'woocommerce-appointments' ), 'hour' => __( 'Hour(s)', 'woocommerce-appointments' ), 'day' => __( 'Day(s)', 'woocommerce-appointments' ), 'month' => __( 'Month(s)', 'woocommerce-appointments' ) ), 'class' => 'wcfm-select wcfm_ele wcfm_appointment_unit_ele appointment', 'value' => $duration_unit ),
				"_wc_appointment_interval" => array( 'label' => __('Interval', 'woocommerce-appointments'), 'type' => 'number', 'class' => 'wcfm-text wcfm_ele wcfm_appointment_unit_ele appointment', 'label_class' => 'wcfm_title wcfm_appointment_unit_label appointment', 'value' => $interval, 'hints' => __( 'Select intervals when each appointment slot is available for scheduling.', 'woocommerce-appointments' ) ),
				"_wc_appointment_interval_unit" => array('type' => 'select', 'options' => array( 'minute' => __( 'Minute(s)', 'woocommerce-appointments' ), 'hour' => __( 'Hour(s)', 'woocommerce-appointments' ) ), 'class' => 'wcfm-select wcfm_ele wcfm_appointment_unit_ele appointment', 'value' => $interval_unit ),
				"_wc_appointment_padding_duration" => array( 'label' => __('Padding Time', 'woocommerce-appointments'), 'type' => 'number', 'class' => 'wcfm-text wcfm_ele wcfm_appointment_unit_ele appointment', 'label_class' => 'wcfm_title wcfm_appointment_unit_label appointment', 'value' => $padding_duration, 'hints' => __( 'Specify the padding time you need between appointments.', 'woocommerce-appointments' ) ),
				"_wc_appointment_padding_duration_unit" => array('type' => 'select', 'options' => array( 'minute' => __( 'Minute(s)', 'woocommerce-appointments' ), 'hour' => __( 'Hour(s)', 'woocommerce-appointments' ), 'month' => __( 'Month(s)', 'woocommerce-appointments' ) ), 'class' => 'wcfm-select wcfm_ele wcfm_appointment_unit_ele appointment', 'value' => $padding_duration_unit ),
				"_wc_appointment_min_date" => array( 'label' => __('Lead Time', 'woocommerce-appointments'), 'type' => 'number', 'class' => 'wcfm-text wcfm_ele wcfm_appointment_unit_ele appointment', 'label_class' => 'wcfm_title wcfm_appointment_unit_label appointment', 'value' => $min_date, 'hints' => __( 'How much in advance do you need before a client schedules an appointment?', 'woocommerce-appointments' ) ),
				"_wc_appointment_min_date_unit" => array('type' => 'select', 'options' => array( 'hour' => __( 'Hour(s)', 'woocommerce-appointments' ), 'day' => __( 'Day(s)', 'woocommerce-appointments' ), 'week' => __( 'Week(s)', 'woocommerce-appointments' ), 'month' => __( 'Month(s)', 'woocommerce-appointments' ) ), 'class' => 'wcfm-select wcfm_ele wcfm_appointment_unit_ele appointment', 'value' => $min_date_unit ),
				"_wc_appointment_max_date" => array( 'label' => __('Scheduling Window', 'woocommerce-appointments'), 'type' => 'number', 'class' => 'wcfm-text wcfm_ele wcfm_appointment_unit_ele appointment', 'label_class' => 'wcfm_title wcfm_appointment_unit_label appointment', 'value' => $max_date, 'hints' => __( 'How far in advance are customers allowed to schedule an appointment?', 'woocommerce-appointments' ) ),
				"_wc_appointment_max_date_unit" => array('type' => 'select', 'options' => array( 'hour' => __( 'Hour(s)', 'woocommerce-appointments' ), 'day' => __( 'Day(s)', 'woocommerce-appointments' ), 'week' => __( 'Week(s)', 'woocommerce-appointments' ), 'month' => __( 'Month(s)', 'woocommerce-appointments' ) ), 'class' => 'wcfm-select wcfm_ele wcfm_appointment_unit_ele appointment', 'value' => $max_date_unit ),
				"_wc_appointment_requires_confirmation" => array( 'label' => __('Requires confirmation?', 'woocommerce-appointments'), 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele appointment', 'label_class' => 'wcfm_title checkbox_title appointment', 'value' => 'yes', 'dfvalue' => $requires_confirmation, 'hints' => __( 'Check this box if the appointment requires approval/confirmation. Payment will not be taken during checkout.', 'woocommerce-appointments' ) ),
				"_wc_appointment_user_can_cancel" => array( 'label' => __('Can be cancelled?', 'woocommerce-appointments'), 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele appointment', 'label_class' => 'wcfm_title checkbox_title appointment', 'value' => 'yes', 'dfvalue' => $user_can_cancel, 'hints' => __( 'Check this box if the appointment can be cancelled by the customer. A refund will not be sent automatically.', 'woocommerce-appointments' ) ),
				"_wc_appointment_cancel_limit" => array( 'label' => __('Cancelled at least', 'woocommerce-appointments'), 'type' => 'number', 'class' => 'wcfm-text wcfm_ele wcfm_appointment_unit_ele wc_appointment_cancel_limit appointment', 'label_class' => 'wcfm_title wcfm_appointment_unit_label wc_appointment_cancel_limit appointment', 'value' => $cancel_limit, 'hints' => __( 'before the start date.', 'woocommerce-appointments' ) ),
				"_wc_appointment_cancel_limit_unit" => array('type' => 'select', 'options' => array( 'month' => __( 'Month(s)', 'woocommerce-appointments' ), 'day' => __( 'Day(s)', 'woocommerce-appointments' ), 'hour' => __( 'Hour(s)', 'woocommerce-appointments' ), 'minute' => __( 'Minute(s)', 'woocommerce-appointments' ) ), 'class' => 'wcfm-select wcfm_ele wc_appointment_cancel_limit wcfm_appointment_unit_ele appointment', 'value' => $cancel_limit_unit ),
				"_wc_appointment_customer_timezones" => array( 'label' => __('Customer timezones?', 'woocommerce-appointments'), 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele appointment', 'label_class' => 'wcfm_title checkbox_title appointment', 'value' => 'yes', 'dfvalue' => $customer_timezones, 'hints' => __( 'Check this box if can be converted to customer\'s timezone.', 'woocommerce-appointments' ) ),
																													) ) );
		
		?>
	</div>
</div>
<!-- end collapsible Appointment -->
<div class="wcfm_clearfix"></div>

<?php if( apply_filters( 'wcfm_is_allow_manage_appointment_pricing', true ) ) { ?>
	<!-- Collapsible Appointment 2  -->
	<div class="page_collapsible products_manage_appointment_costs appointment" id="wcfm_products_manage_form_appointment_costs_head"><label class="wcfmfa fa-currency"><?php echo get_woocommerce_currency_symbol() ; ?></label><?php _e('Pricing', 'woocommerce-appointments'); ?><span></span></div>
	<div class="wcfm-container appointment">
		<div id="wcfm_products_manage_form_costs_expander" class="wcfm-content">
			<?php
			$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_wcappointments_cost_fields', array(  
						
						"_wc_appointment_has_price_label" => array( 'label' => __('Label instead of price?', 'woocommerce-appointments'), 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele appointment', 'label_class' => 'wcfm_title checkbox_title appointment', 'value' => 'yes', 'dfvalue' => $has_price_label, 'hints' => __( 'Check this box if the appointment should display text label instead of fixed price amount.', 'woocommerce-appointments' ) ),
						"_wc_appointment_price_label"     => array( 'label' => __('Price Label', 'woocommerce-appointments'), 'type' => 'text', 'class' => 'wcfm-text wcfm_ele appointment', 'label_class' => 'wcfm_title appointment', 'value' => $price_label, 'hints' => __( 'Show this label instead of fixed price amount. Payment will not be taken during checkout.', 'woocommerce-appointments' ) ),
						"_wc_appointment_has_pricing"     => array( 'label' => __('Custom pricing rules?', 'woocommerce-appointments'), 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele appointment', 'label_class' => 'wcfm_title checkbox_title appointment', 'value' => 'yes', 'dfvalue' => $has_pricing, 'hints' => __( 'Check this box if the appointment has custom pricing rules.', 'woocommerce-appointments' ) ),
						"_wc_appointment_cost_rules"      => array('label' => __('Rules', 'woocommerce-appointments') , 'type' => 'multiinput', 'class' => 'wcfm-text wcfm_ele appointment', 'label_class' => 'wcfm_title appointment', 'desc' => __( 'All matching rules will be applied to the appointment.', 'woocommerce-appointments' ), 'desc_class' => 'cost_rules_desc', 'value' => $cost_rule_values, 'options' => array(
																										"type" => array('label' => __('Type', 'wc-frontend-manager-ultimate'), 'type' => 'select', 'options' => $range_types, 'class' => 'wcfm-select wcfm_ele cost_range_type appointment', 'label_class' => 'wcfm_title cost_rules_ele cost_rules_label appointment' ),
																										"from_custom" => array('label' => __('From', 'wc-frontend-manager-ultimate'), 'type' => 'text', 'placeholder' => __( 'YYYY-MM-DD', 'wc-frontend-manager-ultimate' ), 'custom_attributes' => array( 'date_format' => 'yy-mm-dd'), 'class' => 'wcfm-text wcfm_datepicker cost_rule_field cost_rule_custom cost_rules_ele cost_rules_text', 'label_class' => 'wcfm_title cost_rule_field cost_rule_custom cost_rules_ele cost_rules_label' ),
																										"to_custom" => array('label' => __('To', 'wc-frontend-manager-ultimate'), 'type' => 'text', 'placeholder' => __( 'YYYY-MM-DD', 'wc-frontend-manager-ultimate' ), 'custom_attributes' => array( 'date_format' => 'yy-mm-dd'), 'class' => 'wcfm-text wcfm_datepicker cost_rule_field cost_rule_custom cost_rules_ele cost_rules_text', 'label_class' => 'wcfm_title cost_rule_field cost_rule_custom cost_rules_ele cost_rules_label' ),
																										"from_months" => array('label' => __('From', 'wc-frontend-manager-ultimate'), 'type' => 'select', 'options' => $intervals['months'], 'class' => 'wcfm-select cost_rule_field cost_rule_months cost_rules_ele cost_rules_text', 'label_class' => 'wcfm_title cost_rule_field cost_rule_months cost_rules_ele cost_rules_label' ),
																										"to_months" => array('label' => __('To', 'wc-frontend-manager-ultimate'), 'type' => 'select', 'options' => $intervals['months'], 'class' => 'wcfm-select cost_rule_field cost_rule_months cost_rules_ele cost_rules_text', 'label_class' => 'wcfm_title cost_rule_field cost_rule_months cost_rules_ele cost_rules_label' ),
																										"from_weeks" => array('label' => __('From', 'wc-frontend-manager-ultimate'), 'type' => 'select', 'options' => $intervals['weeks'], 'class' => 'wcfm-select cost_rule_field cost_rule_weeks cost_rules_ele cost_rules_text', 'label_class' => 'wcfm_title cost_rule_field cost_rule_weeks cost_rules_ele cost_rules_label' ),
																										"to_weeks" => array('label' => __('To', 'wc-frontend-manager-ultimate'), 'type' => 'select', 'options' => $intervals['weeks'], 'class' => 'wcfm-select cost_rule_field cost_rule_weeks cost_rules_ele cost_rules_text', 'label_class' => 'wcfm_title cost_rule_field cost_rule_weeks cost_rules_ele cost_rules_label' ),
																										"from_days" => array('label' => __('From', 'wc-frontend-manager-ultimate'), 'type' => 'select', 'options' => $intervals['days'], 'class' => 'wcfm-select cost_rule_field cost_rule_days cost_rules_ele cost_rules_text', 'label_class' => 'wcfm_title cost_rule_field cost_rule_days cost_rules_ele cost_rules_label' ),
																										"to_days" => array('label' => __('To', 'wc-frontend-manager-ultimate'), 'type' => 'select', 'options' => $intervals['days'], 'class' => 'wcfm-select cost_rule_field cost_rule_days cost_rules_ele cost_rules_text', 'label_class' => 'wcfm_title cost_rule_field cost_rule_days cost_rules_ele cost_rules_label' ),
																										"from_time" => array('label' => __('From', 'wc-frontend-manager-ultimate'), 'type' => 'time', 'placeholder' => 'HH:MM', 'class' => 'wcfm-text cost_rule_field cost_rule_time cost_rules_ele cost_rules_text', 'label_class' => 'wcfm_title cost_rule_field cost_rule_time cost_rules_ele cost_rules_label' ),
																										"to_time" => array('label' => __('To', 'wc-frontend-manager-ultimate'), 'type' => 'time', 'placeholder' => 'HH:MM', 'class' => 'wcfm-text cost_rule_field cost_rule_time cost_rules_ele cost_rules_text', 'label_class' => 'wcfm_title cost_rule_field cost_rule_time cost_rules_ele cost_rules_label' ),
																										"from_count" => array('label' => __('From', 'wc-frontend-manager-ultimate'), 'type' => 'number', 'class' => 'wcfm-text cost_rule_field cost_rule_count cost_rules_ele cost_rules_text', 'label_class' => 'wcfm_title cost_rule_field cost_rule_count cost_rules_ele cost_rules_label' ),
																										"to_count" => array('label' => __('To', 'wc-frontend-manager-ultimate'), 'type' => 'number', 'class' => 'wcfm-text cost_rule_field cost_rule_count cost_rules_ele cost_rules_text', 'label_class' => 'wcfm_title cost_rule_field cost_rule_count cost_rules_ele cost_rules_label' ),
																										"base_modifier" => array('label' => __('Base Cost', 'woocommerce-appointments'), 'type' => 'select', 'options' => array( '' => '+', 'minus' => '-', 'times' => '&times;', 'divide' => '&divide;' ), 'class' => 'wcfm-select wcfm_ele cost_rules_ele cost_rules_text cost_price_ele appointment', 'label_class' => 'wcfm_title cost_rules_ele cost_rules_label', 'hints' => __( 'Applied to the appointment as a whole. Must be inside range rules to be applied.', 'woocommerce-appointments' ) ),
																										"base_cost" => array( 'type' => 'number', 'class' => 'wcfm-text wcfm_ele cost_rules_ele cost_rule_base_cost cost_rules_text cost_price_ele cost_price_field appointment', 'label_class' => 'wcfm_title cost_rules_ele cost_rules_label appointment' ),
																										"block_modifier" => array('label' => __('Slot Cost', 'woocommerce-appointments'), 'type' => 'select', 'options' => array( '' => '+', 'minus' => '-', 'times' => '&times;', 'divide' => '&divide;' ), 'class' => 'wcfm-select wcfm_ele cost_rules_ele cost_rules_text cost_price_ele appointment', 'label_class' => 'wcfm_title cost_rules_ele cost_rules_label', 'hints' => __( 'Applied to each appointment slot separately. When appointment lasts for 2 days or more, this cost applies to each day in range separately.', 'woocommerce-appointments' ) ),
																										"block_cost" => array( 'type' => 'number', 'class' => 'wcfm-text wcfm_ele cost_rules_ele cost_rule_block_cost cost_rules_text cost_price_ele cost_price_field appointment', 'label_class' => 'wcfm_title cost_rules_ele cost_rules_label appointment' ),
																										)	)																					
																						), $product_id ) );
			?>
		</div>
	</div>
	<!-- end collapsible Appointment -->
	<div class="wcfm_clearfix"></div>
<?php } ?>

<!-- Collapsible Appointment 3  -->
<div class="page_collapsible products_manage_appointment_availability appointment" id="wcfm_products_manage_form_appointment_availability_head"><label class="wcfmfa fa-clock"></label><?php _e('Availability', 'woocommerce-appointments'); ?><span></span></div>
<div class="wcfm-container appointment">
	<div id="wcfm_products_manage_form_appointment_availability_expander" class="wcfm-content">
		<?php
		$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_wcappointments_availability_fields', array(  
					
						"_wc_appointment_availability_span"       => array('label' => __('Availability Check', 'woocommerce-appointments') , 'type' => 'select', 'options' => array( '' => __( 'All slots in availability range', 'woocommerce-appointments'), 'start' => __( 'The starting slot only', 'woocommerce-appointments' ) ), 'class' => 'wcfm-select wcfm_ele appointment', 'label_class' => 'wcfm_title appointment', 'value' => $availability_span, 'hints' => __( 'By default availability per each slot in range is checked. You can also check availability for starting slot only.', 'woocommerce-appointments' ) ),		
						"_wc_appointment_availability_autoselect" => array('label' => __('Auto-select?', 'woocommerce-appointments') , 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele appointment', 'label_class' => 'wcfm_title checkbox_title appointment', 'value' => 'yes', 'dfvalue' => $availability_autoselect, 'hints' => __( 'Check this box if you want to auto-select first available day and/or time.', 'woocommerce-appointments' ) ),
						"_wc_appointment_has_restricted_days"     => array( 'label' => __( 'Restrict start days?', 'woocommerce-appointments' ), 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele appointment', 'label_class' => 'wcfm_title checkbox_title appointment', 'value' => 'yes', 'dfvalue' => $has_restricted_days, 'hints' => __( 'Restrict appointments so that they can only start on certain days of the week. Does not affect availability.', 'woocommerce-appointments' ) ),
						"_wc_appointment_restricted_days"         => array('label' => __('Restricted days', 'wc-frontend-manager-ultimate') , 'type' => 'select', 'attributes' => array( 'multiple' => 'multiple', 'style' => 'width: 60%;' ), 'options' => array( 0 => __( 'Sunday', 'woocommerce-appointments'), 1 => __( 'Monday', 'woocommerce-appointments' ), 2 => __( 'Tuesday', 'woocommerce-appointments' ), 3 => __( 'Wednesday', 'woocommerce-appointments' ), 4 => __( 'Thursday', 'woocommerce-appointments' ), 5 => __( 'Friday', 'woocommerce-appointments' ), 6 => __( 'Saturday', 'woocommerce-appointments' ) ), 'class' => 'wcfm-select wcfm_ele appointment', 'label_class' => 'wcfm_title appointment', 'value' => $restricted_days ),
						
						"_wc_appointment_availability_rules"  => array('label' => __('Custom Availability', 'woocommerce-appointments') , 'type' => 'multiinput', 'class' => 'wcfm-text wcfm_ele appointment', 'label_class' => 'wcfm_title appointment', 'desc' => esc_attr( get_wc_appointment_rules_explanation() ), 'desc_class' => 'avail_rules_desc', 'value' => $availability_rule_values, 'options' => array(
																										"type" => array('label' => __('Type', 'wc-frontend-manager-ultimate'), 'type' => 'select', 'options' => $availability_range_types, 'class' => 'wcfm-select wcfm_ele avail_range_type appointment', 'label_class' => 'wcfm_title avail_rules_ele avail_rules_label appointment' ),
																										"qty" => array('label' => __('Capacity', 'wc-frontend-manager-ultimate'), 'type' => 'number', 'class' => 'wcfm-text wcfm_ele avail_rules_ele avail_rule_capacity appointment', 'label_class' => 'wcfm_title avail_rules_ele avail_rules_label appointment', 'hints' => __( 'The maximum number of appointments per slot. Overrides general product capacity.', 'wc-frontend-manager-ultimate' ) ),
																										"avail_id" => array( 'type' => 'hidden', 'class' => 'avail_id' ),
																										"kind_id" => array( 'type' => 'hidden', 'class' => 'kind_id' ),
																										"from_custom" => array('label' => __('From', 'wc-frontend-manager-ultimate'), 'type' => 'text', 'placeholder' => __( 'YYYY-MM-DD', 'wc-frontend-manager-ultimate' ), 'custom_attributes' => array( 'date_format' => 'yy-mm-dd'), 'class' => 'wcfm-text wcfm_datepicker avail_rule_field avail_rule_custom avail_rules_ele avail_rules_text', 'label_class' => 'wcfm_title avail_rule_field avail_rule_custom avail_rules_ele avail_rules_label' ),
																										"to_custom" => array('label' => __('To', 'wc-frontend-manager-ultimate'), 'type' => 'text', 'placeholder' => __( 'YYYY-MM-DD', 'wc-frontend-manager-ultimate' ), 'custom_attributes' => array( 'date_format' => 'yy-mm-dd'), 'class' => 'wcfm-text wcfm_datepicker avail_rule_field avail_rule_custom avail_rules_ele avail_rules_text', 'label_class' => 'wcfm_title avail_rule_field avail_rule_custom avail_rules_ele avail_rules_label' ),
																										"from_months" => array('label' => __('From', 'wc-frontend-manager-ultimate'), 'type' => 'select', 'options' => $intervals['months'], 'class' => 'wcfm-select avail_rule_field avail_rule_months avail_rules_ele avail_rules_text', 'label_class' => 'wcfm_title avail_rule_field avail_rule_months avail_rules_ele avail_rules_label' ),
																										"to_months" => array('label' => __('To', 'wc-frontend-manager-ultimate'), 'type' => 'select', 'options' => $intervals['months'], 'class' => 'wcfm-select avail_rule_field avail_rule_months avail_rules_ele avail_rules_text', 'label_class' => 'wcfm_title avail_rule_field avail_rule_months avail_rules_ele avail_rules_label' ),
																										"from_weeks" => array('label' => __('From', 'wc-frontend-manager-ultimate'), 'type' => 'select', 'options' => $intervals['weeks'], 'class' => 'wcfm-select avail_rule_field avail_rule_weeks avail_rules_ele avail_rules_text', 'label_class' => 'wcfm_title avail_rule_field avail_rule_weeks avail_rules_ele avail_rules_label' ),
																										"to_weeks" => array('label' => __('To', 'wc-frontend-manager-ultimate'), 'type' => 'select', 'options' => $intervals['weeks'], 'class' => 'wcfm-select avail_rule_field avail_rule_weeks avail_rules_ele avail_rules_text', 'label_class' => 'wcfm_title avail_rule_field avail_rule_weeks avail_rules_ele avail_rules_label' ),
																										"from_days" => array('label' => __('From', 'wc-frontend-manager-ultimate'), 'type' => 'select', 'options' => $intervals['days'], 'class' => 'wcfm-select avail_rule_field avail_rule_days avail_rules_ele avail_rules_text', 'label_class' => 'wcfm_title avail_rule_field avail_rule_days avail_rules_ele avail_rules_label' ),
																										"to_days" => array('label' => __('To', 'wc-frontend-manager-ultimate'), 'type' => 'select', 'options' => $intervals['days'], 'class' => 'wcfm-select avail_rule_field avail_rule_days avail_rules_ele avail_rules_text', 'label_class' => 'wcfm_title avail_rule_field avail_rule_days avail_rules_ele avail_rules_label' ),
																										"from_time" => array('label' => __('From', 'wc-frontend-manager-ultimate'), 'type' => 'time', 'placeholder' => 'HH:MM', 'class' => 'wcfm-text avail_rule_field avail_rule_time avail_rules_ele avail_rules_text', 'label_class' => 'wcfm_title avail_rule_field avail_rule_time avail_rules_ele avail_rules_label' ),
																										"to_time" => array('label' => __('To', 'wc-frontend-manager-ultimate'), 'type' => 'time', 'placeholder' => 'HH:MM', 'class' => 'wcfm-text avail_rule_field avail_rule_time avail_rules_ele avail_rules_text', 'label_class' => 'wcfm_title avail_rule_field avail_rule_time avail_rules_ele avail_rules_label' ),
																										"priority" => array('label' => __('Priority', 'wc-frontend-manager-ultimate'), 'type' => 'number', 'class' => 'wcfm-text wcfm_ele avail_rules_ele avail_rule_priority avail_rules_text appointment', 'label_class' => 'wcfm_title avail_rules_ele avail_rules_label appointment', 'hints' => esc_attr( get_wc_appointment_priority_explanation() ) ),
																										"appointable" => array('label' => __('Appointable', 'wc-frontend-manager-ultimate'), 'type' => 'select', 'options' => array( 'yes' => __( 'Yes', 'woocommerce-appointments'), 'no' => __( 'No', 'woocommerce-appointments') ), 'class' => 'wcfm-select wcfm_ele avail_rules_ele avail_rules_text appointment', 'label_class' => 'wcfm_title avail_rules_ele avail_rules_label', 'hints' => __( 'If not appointable, users won\'t be able to choose slots in this range for their appointment.', 'wc-frontend-manager-ultimate' ) ),
																										)	)
																										
																									), $product_id ) );
		?>
	</div>
</div>
<!-- end collapsible Appointment -->
<div class="wcfm_clearfix"></div>

<?php if( apply_filters( 'wcfm_is_allow_manage_appointment_staff', true ) ) { ?>
	<!-- Collapsible Appointment 4  -->
	<div class="page_collapsible products_manage_staffs staffs appointment" id="wcfm_products_manage_form_staffs_head"><label class="wcfmfa fa-user"></label><?php _e('Staff', 'woocommerce-appointments'); ?><span></span></div>
	<div class="wcfm-container staffs appointment">
		<div id="wcfm_products_manage_form_staffs_expander" class="wcfm-content">
			<?php
			$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_wcappointments_staff_fields', array(  
						"_wc_appointment_staff_label" => array( 'label' => __('Label', 'woocommerce-appointments'), 'placeholder' => __('Providers', 'woocommerce-appointments'), 'type' => 'text', 'class' => 'wcfm-text wcfm_ele appointment', 'label_class' => 'wcfm_title appointment', 'value' => $staff_label, 'hints' => __( 'The label shown on the frontend if the staff is customer defined.', 'woocommerce-appointments' ) ),
						"_wc_appointment_staff_assignment" => array( 'label' => __('Staff selection', 'wc-frontend-manager-ultimate'), 'type' => 'select', 'options' => array( 'customer' => __( 'Customer selected', 'woocommerce-appointments'), 'automatic' => __( 'Automatically assigned', 'woocommerce-appointments' ), 'all' => __( 'Automatically assigned (all staff together)', 'woocommerce-appointments' ) ), 'class' => 'wcfm-select wcfm_ele appointment', 'label_class' => 'wcfm_title appointment', 'value' => $staff_assignment, 'hints' => __( 'Customer selected staff allow customers to choose one from the appointment form.', 'woocommerce-appointments' ) ),
						"_wc_appointment_all_staffs" => array( 'label' => __('Available all Staffs', 'wc-frontend-manager-ultimate'), 'type' => 'select', 'options' => $all_staffs, 'class' => 'wcfm-select wcfm_ele appointment', 'label_class' => 'wcfm_title appointment', 'hints' => __( 'Staffs are used if you have multiple bookable items, e.g. room types, instructors or ticket types. Availability for staffs is global across all bookable products. Choose to associate with your product.', 'wc-frontend-manager-ultimate' ) ),
						"_wc_appointment_staffs" =>     array('label' => __('Staffs', 'wc-frontend-manager-ultimate') , 'type' => 'multiinput', 'class' => 'wcfm-text wcfm_ele staff_types appointment', 'label_class' => 'wcfm_title staff_types appointment', 'has_dummy' => true, 'value' => $staffs, 'options' => array(
																										"staff_title" => array('label' => __('Title', 'wc-frontend-manager-ultimate'), 'type' => 'text', 'attributes' => array( 'disabled' => 'disabled' ), 'class' => 'wcfm-text wcfm_ele staff_types_text appointment', 'label_class' => 'wcfm_title staff_types_label appointment' ),
																										"staff_base_cost" => array('label' => __('Additional Cost', 'woocommerce-appointments'), 'type' => 'number', 'attributes' => array( 'step' => '0.01' ), 'class' => 'wcfm-text wcfm_ele staff_types_text appointment', 'label_class' => 'wcfm_title staff_types_label appointment' ),
																										"staff_qty"       => array('label' => __('Quantity', 'woocommerce-appointments'), 'type' => 'number', 'attributes' => array( 'step' => '1' ), 'class' => 'wcfm-text wcfm_ele staff_types_text appointment', 'label_class' => 'wcfm_title staff_types_label appointment' ),
																										"staff_id" => array('type' => 'hidden', 'class' => 'staff_id' )
																										) )
																														)	) );
			?>
		</div>
	</div>
	<!-- end collapsible Appointment -->
	<div class="wcfm_clearfix"></div>
<?php } ?>