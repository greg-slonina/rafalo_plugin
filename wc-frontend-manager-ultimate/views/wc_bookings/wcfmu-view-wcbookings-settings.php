<?php
/**
 * WCFM plugin views
 *
 * Plugin WC Booking Resources Manage Views
 *
 * @author 		WC Lovers
 * @package 	wcfmu/views
 * @version   2.3.5
 */
global $wp, $WCFM, $WCFMu, $WCFMmp, $wpdb;

if( !current_user_can( 'manage_bookings_settings' ) && !current_user_can( 'manage_bookings' ) ) {
	wcfm_restriction_message_show( "Bookings" );
	return;
}

$intervals = array();

$intervals['months'] = array(
	'1'  => __( 'January', 'woocommerce-bookings' ),
	'2'  => __( 'February', 'woocommerce-bookings' ),
	'3'  => __( 'March', 'woocommerce-bookings' ),
	'4'  => __( 'April', 'woocommerce-bookings' ),
	'5'  => __( 'May', 'woocommerce-bookings' ),
	'6'  => __( 'June', 'woocommerce-bookings' ),
	'7'  => __( 'July', 'woocommerce-bookings' ),
	'8'  => __( 'August', 'woocommerce-bookings' ),
	'9'  => __( 'September', 'woocommerce-bookings' ),
	'10' => __( 'October', 'woocommerce-bookings' ),
	'11' => __( 'November', 'woocommerce-bookings' ),
	'12' => __( 'December', 'woocommerce-bookings' )
);

$intervals['days'] = array(
	'1' => __( 'Monday', 'woocommerce-bookings' ),
	'2' => __( 'Tuesday', 'woocommerce-bookings' ),
	'3' => __( 'Wednesday', 'woocommerce-bookings' ),
	'4' => __( 'Thursday', 'woocommerce-bookings' ),
	'5' => __( 'Friday', 'woocommerce-bookings' ),
	'6' => __( 'Saturday', 'woocommerce-bookings' ),
	'7' => __( 'Sunday', 'woocommerce-bookings' )
);

for ( $i = 1; $i <= 53; $i ++ ) {
	$intervals['weeks'][ $i ] = sprintf( __( 'Week %s', 'woocommerce-bookings' ), $i );
}

$range_types = array(
											'custom'     => __( 'Date range', 'woocommerce-bookings' ),
											'months'     => __( 'Range of months', 'woocommerce-bookings' ),
											'weeks'      => __( 'Range of weeks', 'woocommerce-bookings' ),
											'days'       => __( 'Range of days', 'woocommerce-bookings' ),
											'time'       => '&nbsp;&nbsp;&nbsp;' .  __( 'Time Range (all week)', 'woocommerce-bookings' ),
											'time:range' => '&nbsp;&nbsp;&nbsp;' . __( 'Date Range with time', 'woocommerce-bookings' )
										);
foreach ( $intervals['days'] as $key => $label ) :
	$range_types['time:' . $key] = '&nbsp;&nbsp;&nbsp;' . $label;
endforeach;



$availability_rule_values = array();
$availability_default_rules = array(  "type"         => 'custom',
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
																			"bookable"     => '',
																			"priority"     => 10,
																			"av_id"        => '', 
																			"vendor"       => ''
																		);
$availability_rule_values[0] = $availability_default_rules;

if( isset( $wp->query_vars['wcfm-bookings-settings'] ) ) {
	remove_all_filters( 'pre_option_wc_global_booking_availability' );
	remove_all_filters( 'pre_update_option_wc_global_booking_availability' );
	
	$vendor_id = 0;
	if( wcfm_is_vendor() && ( in_array( $WCFM->is_marketplace, array( 'wcfmmarketplace', 'wcpvendors' ) ) ) )  {
		if( $WCFM->is_marketplace == 'wcfmmarketplace' ) {
			$vendor_id = $WCFMmp->vendor_id;
		} elseif( $WCFM->is_marketplace == 'wcpvendor' ) {
			$vendor_id = (int) WC_Product_Vendors_Utils::get_logged_in_vendor();
		}
	}
	
	$global_availabilities = WC_Data_Store::load( 'booking-global-availability' )->get_all();
	
	if( $global_availabilities ) {
		if ( $vendor_id ) {
			// filter rules that belong to this vendor's product.
			$filtered_global_availabilities = array_filter(
				$global_availabilities,
				function ( WC_Global_Availability $availability ) use ( $vendor_id ) {
					return (int) $availability->get_meta( 'vendor_id' ) === (int) $vendor_id;
				}
			);
		} else {
			// filter rules that don't belong to any vendor.
			$filtered_global_availabilities = array_filter(
				$global_availabilities,
				function ( WC_Global_Availability $availability ) {
					return empty( $availability->get_meta( 'vendor_id' ) );
				}
			);
		}
		
		//print_r($filtered_global_availabilities);
		
		if( !empty( $filtered_global_availabilities ) ) {
			$availability_rule_values = array();
			foreach( $filtered_global_availabilities as $a_index => $availability_rule ) {
				
				if( !$availability_rule->get_id() ) continue;
					
				$availability_rule_values[$a_index] = $availability_default_rules;
				$availability_rule_values[$a_index]['type'] = $availability_rule->get_range_type( 'edit' );
				if($availability_rule_values[$a_index]['type'] == 'custom' ) {
					$availability_rule_values[$a_index]['from_custom'] = $availability_rule->get_from_range( 'edit' );
					$availability_rule_values[$a_index]['to_custom']   = $availability_rule->get_to_range( 'edit' );
				} elseif($availability_rule_values[$a_index]['type'] == 'months' ) {
					$availability_rule_values[$a_index]['from_months'] = $availability_rule->get_from_range( 'edit' );
					$availability_rule_values[$a_index]['to_months']   = $availability_rule->get_to_range( 'edit' );
				} elseif($availability_rule_values[$a_index]['type'] == 'weeks' ) {
					$availability_rule_values[$a_index]['from_weeks'] = $availability_rule->get_from_range( 'edit' );
					$availability_rule_values[$a_index]['to_weeks']   = $availability_rule->get_to_range( 'edit' );
				} elseif($availability_rule_values[$a_index]['type'] == 'days' ) {
					$availability_rule_values[$a_index]['from_days'] = $availability_rule->get_from_range( 'edit' );
					$availability_rule_values[$a_index]['to_days']   = $availability_rule->get_to_range( 'edit' );
				} elseif($availability_rule_values[$a_index]['type'] == 'time:range' ) {
					$availability_rule_values[$a_index]['from_custom'] = $availability_rule->get_from_date( 'edit' );
					$availability_rule_values[$a_index]['to_custom']   = $availability_rule->get_to_date( 'edit' );
					$availability_rule_values[$a_index]['from_time'] = $availability_rule->get_from_range( 'edit' );
					$availability_rule_values[$a_index]['to_time']   = $availability_rule->get_to_range( 'edit' );
				} else {
					$availability_rule_values[$a_index]['from_time'] = $availability_rule->get_from_range( 'edit' );
					$availability_rule_values[$a_index]['to_time']   = $availability_rule->get_to_range( 'edit' );
				}
				$availability_rule_values[$a_index]['av_title'] = $availability_rule->get_title( 'edit' );
				$availability_rule_values[$a_index]['bookable'] = $availability_rule->get_bookable( 'edit' );
				$availability_rule_values[$a_index]['priority'] = $availability_rule->get_priority( 'edit' );
				$availability_rule_values[$a_index]['av_id']    = $availability_rule->get_id();
				$availability_rule_values[$a_index]['vendor']   = $vendor_id;
			}
		}
	}
}

do_action( 'before_wcfm_settings' );

?>

<div class="collapse wcfm-collapse">
  <div class="wcfm-page-headig">
		<span class="wcfmfa fa-cog"></span>
		<span class="wcfm-page-heading-text"><?php _e( 'Bookings Settings', 'wc-frontend-manager-ultimate' ); ?></span>
		<?php do_action( 'wcfm_page_heading' ); ?>
	</div>
	<div class="wcfm-collapse-content">
	  <div id="wcfm_page_load"></div>
			
		<div class="wcfm-container wcfm-top-element-container">
			<h2><?php _e('Global Availability', 'wc-frontend-manager-ultimate' ); ?></h2>
			
			<?php
			if( $allow_wp_admin_view = apply_filters( 'wcfm_allow_wp_admin_view', true ) ) {
				?>
				<a target="_blank" class="wcfm_wp_admin_view text_tip" href="<?php echo admin_url('edit.php?post_type=wc_booking&page=wc_bookings_global_availability'); ?>" data-tip="<?php _e( 'WP Admin View', 'wc-frontend-manager-ultimate' ); ?>"><span class="fab fa-wordpress fa-wordpress-simple"></span></a>
				<?php
			}
			
			if( $wcfm_is_allow_booking_calendar = apply_filters( 'wcfm_is_allow_booking_calendar', true ) ) {
				echo '<a class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_bookings_calendar_url().'" data-tip="'. __('Calendar View', 'wc-frontend-manager-ultimate') .'"><span class="wcfmfa fa-calendar-alt"></span></a>';
			}
			
			echo '<a class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_bookings_url().'" data-tip="' . __( 'Bookings List', 'wc-frontend-manager-ultimate' ) . '"><span class="wcfmfa fa-calendar"></span></a>';
			
			if( $wcfm_is_allow_manage_resource = apply_filters( 'wcfm_is_allow_manage_resource', true ) ) {
				echo '<a class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_bookings_resources_url().'" data-tip="' . __( 'Manage Resources', 'wc-frontend-manager-ultimate' ) . '"><span class="wcfmfa fa-briefcase"></span></a>';
			}
			
			if( $has_new = apply_filters( 'wcfm_add_new_product_sub_menu', true ) ) {
				echo '<a class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_edit_product_url().'" data-tip="' . __('Create Bookable', 'wc-frontend-manager-ultimate') . '"><span class="wcfmfa fa-cube"></span></a>';
			}
		
			?>
			<div class="wcfm-clearfix"></div>
		</div>
	<div class="wcfm-clearfix"></div><br />
	
	<?php do_action( 'begin_wcfm_wcbookings_settings' ); ?>
	  
  <form id="wcfm_wcbookings_settings_form" class="wcfm">
  
	  <?php do_action( 'begin_wcfm_wcbookings_settings_form' ); ?>
				
			<!-- collapsible -->
			<div class="wcfm-container">
				<div id="wcbookings_settings_general_expander" class="wcfm-content">
						<?php
							$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcbookings_settings_fields_availability', array (
																				"wc_global_booking_availability" =>     array('label' => __('Resource Details', 'woocommerce-bookings') , 'type' => 'multiinput', 'class' => 'wcfm-text wcfm_ele booking', 'label_class' => 'wcfm_title booking', 'desc' => __( 'Rules with lower priority numbers will override rules with a higher priority (e.g. 9 overrides 10 ). Ordering is only applied within the same priority and higher order overrides lower order.', 'woocommerce-bookings' ), 'desc_class' => 'avail_rules_desc', 'value' => $availability_rule_values, 'options' => array(
																									"type" => array('label' => __('Type', 'wc-frontend-manager-ultimate'), 'type' => 'select', 'options' => $range_types, 'class' => 'wcfm-select wcfm_ele avail_range_type booking', 'label_class' => 'wcfm_title avail_rules_ele avail_rules_label booking' ),
																									"av_title" => array('label' => __('Title', 'wc-frontend-manager-ultimate'), 'type' => 'text', 'class' => 'wcfm-text wcfm_ele avail_range_title booking', 'label_class' => 'wcfm_title avail_rules_label booking' ),
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
																									"bookable" => array('label' => __('Bookable', 'woocommerce-bookings'), 'type' => 'select', 'options' => array( 'yes' => 'YES', 'no' => 'NO' ), 'class' => 'wcfm-select wcfm_ele avail_rules_ele avail_rules_text booking', 'label_class' => 'wcfm_title avail_rules_ele avail_rules_label', 'hints' => __( 'If not bookable, users won\'t be able to choose this block for their booking.', 'woocommerce-bookings' ) ),
																									"priority" => array('label' => __('Priority', 'woocommerce-bookings'), 'type' => 'number', 'class' => 'wcfm-text wcfm_ele avail_rules_ele avail_rule_priority avail_rules_text booking', 'label_class' => 'wcfm_title avail_rules_ele avail_rules_label booking', 'hints' => esc_attr( get_wc_booking_priority_explanation() ) ),
																									"av_id" => array( 'type' => 'hidden' ),
																									"vendor" => array( 'type' => 'hidden' ),
																							    )	)
																				) ) );
						?>
				</div>
			</div>
			<div class="wcfm_clearfix"></div><br />
			<!-- end collapsible -->
			 
			<?php do_action( 'end_wcfm_settings_form' ); ?>
			
			<div class="wcfm-message" tabindex="-1"></div>
			
			<div id="wcfm_wcbookings_settings_submit">
				<input type="submit" name="submit-data" value="<?php _e( 'Submit', 'wc-frontend-manager-ultimate' ); ?>" id="wcfm_wcbookings_settings_submit_button" class="wcfm_submit_button" />
			</div>
			<?php
			do_action( 'after_wcfm_settings' );
			?>
		</form>
	</div>
</div>