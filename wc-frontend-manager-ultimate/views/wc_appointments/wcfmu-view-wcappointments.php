<?php
/**
 * WCFM plugin view
 *
 * WCFM Appointments List View
 *
 * @author 		WC Lovers
 * @package 	wcfmu/view
 * @version   2.4.0
 */
 
global $WCFM, $WCFMu;

if( !current_user_can( 'manage_appointments' ) || !apply_filters( 'wcfm_is_allow_appointment_list', true ) ) {
	wcfm_restriction_message_show( "Appointments" );
	return;
}

$wcfmu_appointments_menus = apply_filters( 'wcfmu_appointments_menus', array( 'all' => __( 'All', 'wc-frontend-manager-ultimate'), 
																																			'complete' => __('Complete', 'wc-frontend-manager-ultimate' ), 
																																			'paid' => __('Paid', 'wc-frontend-manager-ultimate' ),
																																			'confirmed' => __('Confirmed', 'wc-frontend-manager-ultimate' ),
																																			'pending-confirmation' => __('Pending Confirmation', 'wc-frontend-manager-ultimate' ),
																																			'cancelled' => __('Cancelled', 'wc-frontend-manager-ultimate' ),
																																			'unpaid' => __('Un-paid', 'wc-frontend-manager-ultimate' ), 
																																			) );

if ( class_exists( 'WC_Deposits' ) ) {
	$wcfmu_appointments_menus['wc-partial-payment'] = __( 'Partial Paid', 'wc-frontend-manager-ultimate' );
}

$appointment_status = ! empty( $_GET['appointment_status'] ) ? sanitize_text_field( $_GET['appointment_status'] ) : 'all';

include_once( WC_APPOINTMENTS_ABSPATH . 'includes/admin/class-wc-appointments-admin.php' );

?>
<div class="collapse wcfm-collapse" id="wcfm_appointments_listing">
  <div class="wcfm-page-headig">
		<span class="wcfmfa fa-calendar"></span>
		<span class="wcfm-page-heading-text"><?php _e( 'Appointments List', 'wc-frontend-manager-ultimate' ); ?></span>
		<?php do_action( 'wcfm_page_heading' ); ?>
	</div>
	<div class="wcfm-collapse-content">
	  <div id="wcfm_page_load"></div>
	  
	  <div class="wcfm-container wcfm-top-element-container">
			<ul class="wcfm_appointments_menus">
				<?php
				$is_first = true;
				foreach( $wcfmu_appointments_menus as $wcfmu_appointments_menu_key => $wcfmu_appointments_menu) {
					?>
					<li class="wcfm_appointments_menu_item">
						<?php
						if($is_first) $is_first = false;
						else echo " | ";
						?>
						<a class="<?php echo ( $wcfmu_appointments_menu_key == $appointment_status ) ? 'active' : ''; ?>" href="<?php echo get_wcfm_appointments_url( $wcfmu_appointments_menu_key ); ?>"><?php echo $wcfmu_appointments_menu; ?></a>
					</li>
					<?php
				}
				?>
			</ul>
			
			<?php
			if( $allow_wp_admin_view = apply_filters( 'wcfm_allow_wp_admin_view', true ) ) {
				?>
				<a class="wcfm_screen_manager text_tip" href="#" data-screen="appointment" data-tip="<?php _e( 'Screen Manager', 'wc-frontend-manager-ultimate' ); ?>"><span class="wcfmfa fa-tv"></span></a>
				<a target="_blank" class="wcfm_wp_admin_view text_tip" href="<?php echo admin_url('edit.php?post_type=wc_appointment'); ?>" data-tip="<?php _e( 'WP Admin View', 'wc-frontend-manager-ultimate' ); ?>"><span class="fab fa-wordpress fa-wordpress-simple"></span></a>
				<?php
			}
			
			if( $wcfm_is_allow_manual_appointment = apply_filters( 'wcfm_is_allow_manual_appointment', true ) ) {
				echo '<a class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_create_appointments_url().'" data-tip="' . __( 'Create Appointment', 'wc-frontend-manager-ultimate' ) . '"><span class="wcfmfa fa-calendar-plus"></span></a>';
			}
			
			if( $wcfm_is_allow_manage_staff = apply_filters( 'wcfm_is_allow_manage_staff', true ) ) {
				echo '<a class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_appointments_staffs_url().'" data-tip="' . __( 'Manage Staff', 'wc-frontend-manager-ultimate' ) . '"><span class="wcfmfa fa-user"></span></a>';
			}
			
			if( $has_new = apply_filters( 'wcfm_add_new_product_sub_menu', true ) ) {
				echo '<a class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_edit_product_url().'" data-tip="' . __('Create Appointable', 'wc-frontend-manager-ultimate') . '"><span class="wcfmfa fa-cube"></span></a>';
			}
			
			if( $wcfm_is_allow_appointment_calendar = apply_filters( 'wcfm_is_allow_appointment_calendar', true ) ) {
				echo '<a class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_appointments_calendar_url().'" data-tip="'. __('Calendar View', 'wc-frontend-manager-ultimate') .'"><span class="wcfmfa fa-calendar-alt"></span></a>';
			}
			?>
			<div class="wcfm-clearfix"></div>
		</div>
	  <div class="wcfm-clearfix"></div><br />
	  
	  <div class="wcfm_appointments_filter_wrap wcfm_filters_wrap">
			<select id="dropdown_appointment_filter" name="filter_appointments" style="width:200px">
				<option value=""><?php _e( 'Filter Appointments', 'woocommerce-appointments' ); ?></option>
				<?php if ( $product_filters = WC_Appointments_Admin::get_appointment_products() ) : ?>
					<?php foreach ( $product_filters as $product_filter ) : ?>
						<option value="<?php echo $product_filter->get_id(); ?>"><?php echo $product_filter->get_title(); ?></option>
					<?php endforeach; ?>
				<?php endif; ?>
			</select>
			
			<select id="dropdown_appointment_staff_filter" name="filter_staff" style="width:200px">
				<option value=""><?php _e( 'All Staff', 'woocommerce-appointments' ); ?></option>
				<?php if ( $staff_filters = WC_Appointments_Admin::get_appointment_staff() ) : ?>
					<?php foreach ( $staff_filters as $staff_member ) : ?>
						<option value="<?php echo $staff_member->ID; ?>"><?php echo $staff_member->display_name; ?></option>
					<?php endforeach; ?>
				<?php endif; ?>
			</select>
			<?php $WCFM->library->wcfm_date_range_picker_field(); ?>
		</div>
	
		<?php do_action( 'before_wcfm_appointments' ); ?>
			
		<div class="wcfm-container">
			<div id="wwcfm_appointments_listing_expander" class="wcfm-content">
				<table id="wcfm-appointments" class="display" cellspacing="0" width="100%">
					<thead>
						<tr>
							<th><span class="wcicon-status-processing text_tip" data-tip="<?php _e( 'Status', 'wc-frontend-manager-ultimate' ); ?>"></span></th>
							<th><?php _e( 'Appointment', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Product', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Order', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Staff', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Start Date', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'End Date', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( apply_filters( 'wcfm_appointments_additional_info_column_label', __( 'Additional Info', 'wc-frontend-manager' ) ) ); ?></th>
							<th><?php _e( 'Actions', 'wc-frontend-manager-ultimate' ); ?></th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th><span class="wcicon-status-processing text_tip" data-tip="<?php _e( 'Status', 'wc-frontend-manager-ultimate' ); ?>"></span></th>
							<th><?php _e( 'Appointment', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Product', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Order', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Staff', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Start Date', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'End Date', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( apply_filters( 'wcfm_appointments_additional_info_column_label', __( 'Additional Info', 'wc-frontend-manager' ) ) ); ?></th>
							<th><?php _e( 'Actions', 'wc-frontend-manager-ultimate' ); ?></th>
						</tr>
					</tfoot>
				</table>
				<div class="wcfm-clearfix"></div>
			</div>
		</div>
		<?php
		do_action( 'after_wcfm_appointments' );
		?>
	</div>
</div>