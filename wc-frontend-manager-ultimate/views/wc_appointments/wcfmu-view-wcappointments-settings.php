<?php
/**
 * WCFM plugin views
 *
 * Plugin WC Appointment Settings Views
 *
 * @author 		WC Lovers
 * @package 	wcfmu/views
 * @version   2.4.2
 */
global $wp, $WCFM, $WCFMu;

if( !current_user_can( 'manage_appointments' ) ) {
	wcfm_restriction_message_show( "Appointments" );
	return;
}

$availability_rule_values = array();
$availability_default_rules = array(  "type"         => 'custom',
	                                    "avail_id"     => '',
	                                    "title"        => '',
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
																			"priority"     => '10'
																		);
$availability_rule_values[0] = $availability_default_rules;


if( isset( $wp->query_vars['wcfm-appointments-settings'] ) ) {
	//$availability_rules = get_option( 'wc_global_appointment_availability' );
	
	$availability_rules = WC_Data_Store::load( 'appointments-availability' )->get_all(
		array(
			array(
				'key'     => 'kind',
				'compare' => '=',
				'value'   => 'availability#global',
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
			$availability_rule_values[$a_index]['title'] = isset( $availability_rule['title'] ) ? $availability_rule['title'] : '';
			$availability_rule_values[$a_index]['priority'] = isset( $availability_rule['priority'] ) ? $availability_rule['priority'] : '10';
			$availability_rule_values[$a_index]['appointable'] = $availability_rule['appointable'];
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
											'custom:daterange' => __( 'Date range with time', 'woocommerce-appointments' ),
											'months'     => __( 'Range of months', 'woocommerce-appointments' ),
											'weeks'      => __( 'Range of weeks', 'woocommerce-appointments' ),
											'days'       => __( 'Range of days', 'woocommerce-appointments' ),
											'quant'      => __( 'Capacity count', 'woocommerce-appointments' ),
											//'slots'     => __( 'Slot count', 'woocommerce-appointments' ),
											'time'       => '&nbsp;&nbsp;&nbsp;' .  __( 'Time Range', 'woocommerce-appointments' ),
											'time:range' => '&nbsp;&nbsp;&nbsp;' . __( 'Date Range with time', 'woocommerce-appointments' )
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

do_action( 'before_wcfm_wcappointments_settings' );

?>

<div class="collapse wcfm-collapse">
  <div class="wcfm-page-headig">
		<span class="wcfmfa fa-cog"></span>
		<span class="wcfm-page-heading-text"><?php _e( 'Appointment Settings', 'wc-frontend-manager-ultimate' ); ?></span>
		<?php do_action( 'wcfm_page_heading' ); ?>
	</div>
	<div class="wcfm-collapse-content">
	  <div id="wcfm_page_load"></div>
			
		<div class="wcfm-container wcfm-top-element-container">
			<h2><?php _e('Global Availability', 'woocommerce-appointments' ); ?></h2>
			
			<?php
			if( $allow_wp_admin_view = apply_filters( 'wcfm_allow_wp_admin_view', true ) ) {
				?>
				<a target="_blank" class="wcfm_wp_admin_view text_tip" href="<?php echo admin_url('admin.php?page=wc-settings&tab=appointments'); ?>" data-tip="<?php _e( 'WP Admin View', 'wc-frontend-manager-ultimate' ); ?>"><span class="fab fa-wordpress fa-wordpress-simple"></span></a>
				<?php
			}
			
			if( $wcfm_is_allow_appointment_calendar = apply_filters( 'wcfm_is_allow_appointment_calendar', true ) ) {
				echo '<a class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_appointments_calendar_url().'" data-tip="'. __('Calendar View', 'wc-frontend-manager-ultimate') .'"><span class="wcfmfa fa-calendar-alt"></span></a>';
			}
			
			echo '<a class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_appointments_url().'" data-tip="' . __( 'Appointments List', 'wc-frontend-manager-ultimate' ) . '"><span class="wcfmfa fa-calendar"></span></a>';
			
			if( $wcfm_is_allow_manage_staff = apply_filters( 'wcfm_is_allow_manage_staff', true ) ) {
				echo '<a class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_appointments_staffs_url().'" data-tip="' . __( 'Manage Staff', 'wc-frontend-manager-ultimate' ) . '"><span class="wcfmfa fa-user"></span></a>';
			}
			
			if( $has_new = apply_filters( 'wcfm_add_new_product_sub_menu', true ) ) {
				echo '<a class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_edit_product_url().'" data-tip="' . __('Create Appointable', 'wc-frontend-manager-ultimate') . '"><span class="wcfmfa fa-cube"></span></a>';
			}
			?>
			<div class="wcfm-clearfix"></div>
		</div>
		<div class="wcfm-clearfix"></div><br />
		
		<?php do_action( 'begin_wcfm_wcappointments_settings' ); ?>
		
		<form id="wcfm_wcappointments_settings_form" class="wcfm">
			
		  <?php do_action( 'begin_wcfm_wcappointments_settings_form' ); ?>
	  
			<!-- collapsible -->
			<div class="wcfm-container">
				<div id="wcappointments_settings_general_expander" class="wcfm-content">
						<?php
							$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_wcappointments_settings_fields_availability', array(  
										"wc_global_appointment_availability"  => array('label' => __('The availability rules you define here will affect all appointable products. You can override them for each product, staff.', 'woocommerce-appointments') , 'type' => 'multiinput', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title', 'desc' => esc_attr( get_wc_appointment_rules_explanation() ), 'desc_class' => 'avail_rules_desc', 'value' => $availability_rule_values, 'options' => array(
																														"type" => array('label' => __('Type', 'wc-frontend-manager-ultimate'), 'type' => 'select', 'options' => $availability_range_types, 'class' => 'wcfm-select wcfm_ele avail_range_type appointment', 'label_class' => 'wcfm_title avail_rules_ele avail_rules_label appointment' ),
																														"avail_id" => array( 'type' => 'hidden', 'class' => 'avail_id' ),
																														"title" => array('label' => __('Title', 'wc-frontend-manager-ultimate'), 'type' => 'text', 'class' => 'wcfm-text wcfm_ele avail_rule_title appointment', 'label_class' => 'wcfm_title avail_rules_ele avail_rules_label appointment' ),
																														"from_custom" => array('label' => __('From', 'wc-frontend-manager-ultimate'), 'type' => 'text', 'placeholder' => 'YYYY-MM-DD', 'custom_attributes' => array( 'date_format' => 'yy-mm-dd'), 'class' => 'wcfm-text wcfm_datepicker avail_rule_field avail_rule_custom avail_rules_ele avail_rules_text', 'label_class' => 'wcfm_title avail_rule_field avail_rule_custom avail_rules_ele avail_rules_label' ),
																														"to_custom" => array('label' => __('To', 'wc-frontend-manager-ultimate'), 'type' => 'text', 'placeholder' => 'YYYY-MM-DD', 'custom_attributes' => array( 'date_format' => 'yy-mm-dd'), 'class' => 'wcfm-text wcfm_datepicker avail_rule_field avail_rule_custom avail_rules_ele avail_rules_text', 'label_class' => 'wcfm_title avail_rule_field avail_rule_custom avail_rules_ele avail_rules_label' ),
																														"from_months" => array('label' => __('From', 'wc-frontend-manager-ultimate'), 'type' => 'select', 'options' => $intervals['months'], 'class' => 'wcfm-select avail_rule_field avail_rule_months avail_rules_ele avail_rules_text', 'label_class' => 'wcfm_title avail_rule_field avail_rule_months avail_rules_ele avail_rules_label' ),
																														"to_months" => array('label' => __('To', 'wc-frontend-manager-ultimate'), 'type' => 'select', 'options' => $intervals['months'], 'class' => 'wcfm-select avail_rule_field avail_rule_months avail_rules_ele avail_rules_text', 'label_class' => 'wcfm_title avail_rule_field avail_rule_months avail_rules_ele avail_rules_label' ),
																														"from_weeks" => array('label' => __('From', 'wc-frontend-manager-ultimate'), 'type' => 'select', 'options' => $intervals['weeks'], 'class' => 'wcfm-select avail_rule_field avail_rule_weeks avail_rules_ele avail_rules_text', 'label_class' => 'wcfm_title avail_rule_field avail_rule_weeks avail_rules_ele avail_rules_label' ),
																														"to_weeks" => array('label' => __('To', 'wc-frontend-manager-ultimate'), 'type' => 'select', 'options' => $intervals['weeks'], 'class' => 'wcfm-select avail_rule_field avail_rule_weeks avail_rules_ele avail_rules_text', 'label_class' => 'wcfm_title avail_rule_field avail_rule_weeks avail_rules_ele avail_rules_label' ),
																														"from_days" => array('label' => __('From', 'wc-frontend-manager-ultimate'), 'type' => 'select', 'options' => $intervals['days'], 'class' => 'wcfm-select avail_rule_field avail_rule_days avail_rules_ele avail_rules_text', 'label_class' => 'wcfm_title avail_rule_field avail_rule_days avail_rules_ele avail_rules_label' ),
																														"to_days" => array('label' => __('To', 'wc-frontend-manager-ultimate'), 'type' => 'select', 'options' => $intervals['days'], 'class' => 'wcfm-select avail_rule_field avail_rule_days avail_rules_ele avail_rules_text', 'label_class' => 'wcfm_title avail_rule_field avail_rule_days avail_rules_ele avail_rules_label' ),
																														"from_time" => array('label' => __('From', 'wc-frontend-manager-ultimate'), 'type' => 'time', 'placeholder' => 'HH:MM', 'class' => 'wcfm-text avail_rule_field avail_rule_time avail_rules_ele avail_rules_text', 'label_class' => 'wcfm_title avail_rule_field avail_rule_time avail_rules_ele avail_rules_label' ),
																														"to_time" => array('label' => __('To', 'wc-frontend-manager-ultimate'), 'type' => 'time', 'placeholder' => 'HH:MM', 'class' => 'wcfm-text avail_rule_field avail_rule_time avail_rules_ele avail_rules_text', 'label_class' => 'wcfm_title avail_rule_field avail_rule_time avail_rules_ele avail_rules_label' ),
																														"priority" => array('label' => __('Priority', 'wc-frontend-manager-ultimate'), 'type' => 'number', 'class' => 'wcfm-text wcfm_ele avail_rules_ele avail_rule_capacity avail_rules_text appointment', 'label_class' => 'wcfm_title avail_rules_ele avail_rules_label appointment', 'hints' => esc_attr( get_wc_appointment_priority_explanation() ) ),
																														"appointable" => array('label' => __('Appointable', 'woocommerce-appointments'), 'type' => 'select', 'options' => array( 'yes' => __( 'YES', 'woocommerce-appointments'), 'no' => __( 'NO', 'woocommerce-appointments') ), 'class' => 'wcfm-select wcfm_ele avail_rules_ele avail_rules_text appointment', 'label_class' => 'wcfm_title avail_rules_ele avail_rules_label', 'hints' => __( 'If not appointable, users won\'t be able to choose slots in this range for their appointment.', 'woocommerce-appointments' ) ),
																														)	)
																														
																													) ) );
						?>
				</div>
			</div>
			<div class="wcfm_clearfix"></div><br />
			<!-- end collapsible -->
			 
			<?php do_action( 'end_wcfm_wcappointments_settings_form' ); ?>
			
			<div class="wcfm-message" tabindex="-1"></div>
			
			<div id="wcfm_wcappointments_settings_submit">
				<input type="submit" name="submit-data" value="<?php _e( 'Submit', 'wc-frontend-manager-ultimate' ); ?>" id="wcfm_wcappointments_settings_submit_button" class="wcfm_submit_button" />
			</div>
			<?php
			do_action( 'after_wcfm_wcappointments_settings' );
			?>
		</form>
	</div>
</div>