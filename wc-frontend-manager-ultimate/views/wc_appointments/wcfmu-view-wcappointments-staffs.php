<?php
/**
 * WCFM plugin view
 *
 * WCFM Appointments Staffs View
 *
 * @author 		WC Lovers
 * @package 	wcfmu/view
 * @version   2.4.0
 */

global $WCFM;

$wcfm_is_allow_manage_staff = apply_filters( 'wcfm_is_allow_manage_staff', true );
if( !current_user_can( 'manage_appointments' ) || !$wcfm_is_allow_manage_staff || !apply_filters( 'wcfm_is_allow_manage_appointment_staff', true ) ) {
	wcfm_restriction_message_show( "Appointments Staffs" );
	return;
}

?>

<div class="collapse wcfm-collapse" id="wcfm_appointments_listing">
  <div class="wcfm-page-headig">
		<span class="wcfmfa fa-user"></span>
		<span class="wcfm-page-heading-text"><?php _e( 'Appointments Staffs', 'wc-frontend-manager-ultimate' ); ?></span>
		<?php do_action( 'wcfm_page_heading' ); ?>
	</div>
	<div class="wcfm-collapse-content">
	  <div id="wcfm_page_load"></div>
		
	  <div class="wcfm-container wcfm-top-element-container">
			<h2><?php _e( 'Manage Staff', 'wc-frontend-manager-ultimate' ); ?></h2>
			
			<?php
			if( $allow_wp_admin_view = apply_filters( 'wcfm_allow_wp_admin_view', true ) ) {
				?>
				<a target="_blank" class="wcfm_wp_admin_view text_tip" href="<?php echo admin_url('edit.php?post_type=appointable_staff'); ?>" data-tip="<?php _e( 'WP Admin View', 'wc-frontend-manager-ultimate' ); ?>"><span class="fab fa-wordpress fa-wordpress-simple"></span></a>
				<?php
			}
			
			if( $wcfm_is_allow_appointment_calendar = apply_filters( 'wcfm_is_allow_appointment_calendar', true ) ) {
				echo '<a class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_appointments_calendar_url().'" data-tip="'. __('Calendar View', 'wc-frontend-manager-ultimate') .'"><span class="wcfmfa fa-calendar-alt"></span></a>';
			}
			
			echo '<a class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_appointments_url().'" data-tip="' . __( 'Appointments List', 'wc-frontend-manager-ultimate' ) . '"><span class="wcfmfa fa-calendar"></span></a>';
			
			if( $has_new = apply_filters( 'wcfm_add_new_product_sub_menu', true ) ) {
				echo '<a class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_edit_product_url().'" data-tip="' . __('Create Appointable', 'wc-frontend-manager-ultimate') . '"><span class="wcfmfa fa-cube"></span></a>';
			}
			
			if( $has_new = apply_filters( 'wcfm_add_new_staff_sub_menu', true ) ) {
				echo '<a class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_appointments_staffs_manage_url().'" data-tip="' . __('Add New Staff', 'wc-frontend-manager-ultimate') . '"><span class="wcfmfa fa-user-plus"></span></a>';
			}
			?>
			
			<div class="wcfm-clearfix"></div>
		</div>
	  <div class="wcfm-clearfix"></div><br />
	  
	  <?php do_action( 'before_wcfm_appointments_staffs' ); ?>
			
		<div class="wcfm-container">
			<div id="wwcfm_appointments_staffs_expander" class="wcfm-content">
				<table id="wcfm-appointments-staffs" class="display" cellspacing="0" width="100%">
					<thead>
						<tr>
							<th><?php _e( 'Staff', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Name', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Email', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Actions', 'wc-frontend-manager-ultimate' ); ?></th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th><?php _e( 'Staff', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Name', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Email', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Actions', 'wc-frontend-manager-ultimate' ); ?></th>
						</tr>
					</tfoot>
				</table>
				<div class="wcfm-clearfix"></div>
			</div>
		</div>
		<?php
		do_action( 'after_wcfm_appointments_staffs' );
		?>
	</div>
</div>