<?php
/**
 * WCFM plugin views
 *
 * Plugin WC Appointment Calendar Views
 *
 * @author 		WC Lovers
 * @package 	wcfmu/views
 * @version   2.4.0
 */
global $WCFM, $WCFMu, $wc_appointments;

$wcfm_is_allow_appointment_calendar = apply_filters( 'wcfm_is_allow_appointment_calendar', true );
if( !current_user_can( 'manage_appointments' ) || !$wcfm_is_allow_appointment_calendar ) {
	wcfm_restriction_message_show( "Appointments Calendar" );
	return;
}

$view           = isset( $_REQUEST['view'] ) ? ucfirst( $_REQUEST['view'] ) : 'Month';
$view           = __( $view, 'woocommerce-appointments' ); 
?>
<div class="collapse wcfm-collapse" id="wcfm_appointments_listing">
  <div class="wcfm-page-headig">
		<span class="wcfmfa fa-calendar-alt"></span>
		<span class="wcfm-page-heading-text"><?php _e( 'Appointments Calendar', 'wc-frontend-manager-ultimate' ); ?></span>
		<?php do_action( 'wcfm_page_heading' ); ?>
	</div>
	<div class="wcfm-collapse-content">
	  <div id="wcfm_page_load"></div>
	  
	  <div class="wcfm-container wcfm-top-element-container">
			<h2><?php printf( __( 'Appointments by %s', 'wc-frontend-manager-ultimate' ), $view ); ?></h2>
			<?php
			if( $allow_wp_admin_view = apply_filters( 'wcfm_allow_wp_admin_view', true ) ) {
				?>
				<a target="_blank" class="wcfm_wp_admin_view text_tip" href="<?php echo admin_url('edit.php?post_type=wc_appointment&page=appointment_calendar'); ?>" data-tip="<?php _e( 'WP Admin View', 'wc-frontend-manager-ultimate' ); ?>"><span class="fab fa-wordpress fa-wordpress-simple"></span></a>
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
			echo '<a class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_appointments_url().'" data-tip="' . __( 'Appointments List', 'wc-frontend-manager-ultimate' ) . '"><span class="wcfmfa fa-calendar"></span></a>';
			?>
			<div class="wcfm-clearfix"></div>
		</div>
	  <div class="wcfm-clearfix"></div><br />
	  
	  <?php do_action( 'before_wcfm_appointments_calendar' ); ?>
			
		<div class="wcfm-container">
		  <div id="wwcfm_appointments_listing_expander" class="wcfm-content">
		    <?php
		    include_once( WC_APPOINTMENTS_ABSPATH . 'includes/admin/class-wc-appointments-admin.php' );
		    include_once( $WCFMu->plugin_path . 'includes/appointments_calendar/class-wcfm-appointments-calendar.php' );
				$calendar_view = new WCFM_Appointments_Calendar();
				$calendar_view->output();
		    ?>
		    <div class="wcfm-clearfix"></div>
		    <div id="aptContent" class="popup-modal white-popup-block mfp-hide" style="display: none;">
				<div class="white-popup wcfm_popup_wrapper">
					<h2 style="color: #00897b; font-weight: 600;"><span id="aptProduct"></span>&nbsp;<?php _e( 'Appointment', 'wc-frontend-manager-ultimate' ); ?></h2>
					<table>
					  <tbody>
							<tr><td><strong><?php esc_html_e('Start:', 'wc-frontend-manager-ultimate') ?></strong></td> <td><span id="aptStart"></span></td></tr>
							<tr><td><strong><?php esc_html_e('Duration:', 'wc-frontend-manager-ultimate') ?></strong></td> <td><span id="aptDuration"></span></td></tr>
							<tr><td><strong><?php esc_html_e('Quantity:', 'wc-frontend-manager-ultimate') ?></strong></td> <td><span id="aptQuantity"></span></td></tr>
							<tr><td><strong><?php esc_html_e('Staff:', 'wc-frontend-manager-ultimate') ?></strong></td> <td><span id="aptStaff"></span></td></tr>
							<?php if( apply_filters( 'wcfm_is_allow_view_customer', true ) ) { ?>
								<tr><td><strong><?php esc_html_e('Customer Name:', 'wc-frontend-manager-ultimate') ?></strong></td> <td><span id="aptCustName"></span></td></tr>
								<?php if( apply_filters( 'wcfm_allow_view_customer_email', true ) ) { ?>
									<tr><td><strong><?php esc_html_e('Customer Email:', 'wc-frontend-manager-ultimate') ?></strong></td> <td><span id="aptCustEmail"></span></td></tr>
									<tr><td><strong><?php esc_html_e('Customer Phone:', 'wc-frontend-manager-ultimate') ?></strong></td> <td><span id="aptCustPhone"></span></td></tr>
								<?php } ?>
							<?php } ?>
							<tr><td colspan="2"><p><strong><a id="aptLink" href="" class="button wcfm_popup_button" target="_blank"><?php esc_html_e('View Appointment', 'wc-frontend-manager-ultimate') ?></a></strong></p></td></tr>
					  </tbody>
					</table>
				</div>
			</div>
		</div>
		<?php
		do_action( 'after_wcfm_appointments_calendar' );
		?>
	</div>
</div>