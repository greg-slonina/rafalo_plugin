<?php
/**
 * WCFM plugin view
 *
 * WCFM Appointments Dashboard View
 *
 * @author 		WC Lovers
 * @package 	wcfmu/view
 * @version   2.4.0
 */

global $WCFM;

if( !current_user_can( 'manage_appointments' ) ) {
	wcfm_restriction_message_show( "Appointments" );
	return;
}

?>

<div class="collapse wcfm-collapse" id="">
  <div class="wcfm-page-headig">
		<span class="wcfmfa fa-clockcalendar-check"></span>
		<span class="wcfm-page-heading-text"><?php _e( 'Appointments', 'wc-frontend-manager-ultimate' ); ?></span>
		<?php do_action( 'wcfm_page_heading' ); ?>
	</div>
	
	<div class="wcfm-collapse-content">
		<div id="wcfm_page_load"></div>
		<?php do_action( 'before_wcfm_wcvendors_appointments_dashboard' ); ?>
		
		<div class="wcfm-container-box">
		  <?php if( $allow_wp_admin_view = apply_filters( 'wcfm_allow_wp_admin_view', true ) ) { ?>
				<div>
					<a class="wcfm_appointment_gloabl_settings wcfm_gloabl_settings text_tip" href="<?php echo get_wcfm_appointment_settings_url(); ?>" data-tip="<?php _e( 'Global Availability', 'woocommerce-appointments' ); ?>"><span class="wcfmfa fa-cog"></span></a>
				</div>
				<div class="wcfm_clearfix"></div>
			<?php } ?>
			
			<?php if( $wcfm_is_allow_manual_appointment = apply_filters( 'wcfm_is_allow_manual_appointment', true ) ) { ?>
				<div class="wcfm-container">
					<?php if( WCFM_Dependencies::wcfmu_plugin_active_check() ) { ?>
						<a href="<?php echo get_wcfm_create_appointments_url(); ?>">
					<?php } ?>
						<div id="wcfm_appointments_product_add_expander" class="wcfm-content">
							<div class="appointment_dashboard_section_icon"><span class="wcfmfa fa-calendar-plus"></span></div>
							<div class="appointment_dashboard_section_label">
								<h2 title="<?php if( !WCFM_Dependencies::wcfmu_plugin_active_check() ) { wcfmu_feature_help_text_show( 'Manual Create Appointment', false, true ); } ?>"><?php _e( 'Create Appointment', 'wc-frontend-manager-ultimate' ); ?></h2>
							</div>
						</div>
					<?php if( WCFM_Dependencies::wcfmu_plugin_active_check() ) { ?></a><?php } ?>
				</div>
			<?php } ?>
			
			<?php if( apply_filters( 'wcfm_is_allow_manage_products', true ) && apply_filters( 'wcfm_is_allow_add_products', true ) ) { ?>
				<div class="wcfm-container">
					<a href="<?php echo get_wcfm_edit_product_url(); ?>">
						<div id="wcfm_appointments_product_add_expander" class="wcfm-content">
							<div class="appointment_dashboard_section_icon"><span class="wcfmfa fa-edit"></span></div>
							<div class="appointment_dashboard_section_label"><h2><?php _e( 'Create Appointable', 'wc-frontend-manager-ultimate' ); ?></h2></div>
						</div>
					</a>
				</div>
			<?php } ?>
			
		</div>
		
		<?php if( apply_filters( 'wcfm_is_allow_manage_appointment_staff', true ) ) { ?>
			<div class="wcfm-container-box">
				<div class="wcfm-container wcfm-container-single">
					<?php if( WCFM_Dependencies::wcfmu_plugin_active_check() ) { ?>
						<a href="<?php echo get_wcfm_appointments_staffs_url(); ?>">
					<?php } ?>
						<div id="wcfm_appointments_staffs_expander" class="wcfm-content">
							<div class="appointment_dashboard_section_icon"><span class="wcfmfa fa-user"></span></div>
							<div class="appointment_dashboard_section_label">
								<h2 title="<?php if( !WCFM_Dependencies::wcfmu_plugin_active_check() ) { wcfmu_feature_help_text_show( 'Manage Staff', false, true ); } ?>"><?php _e( 'Manage Staff', 'wc-frontend-manager-ultimate' ); ?></h2>
							</div>
						</div>
					<?php if( WCFM_Dependencies::wcfmu_plugin_active_check() ) { ?></a><?php } ?>
				</div>
			</div>
		<?php } ?>
			
		
		<div class="wcfm-container-box">
		
		  <?php if( $wcfm_is_allow_appointment_list = apply_filters( 'wcfm_is_allow_appointment_list', true ) ) { ?>
				<div class="wcfm-container">
					<?php if( WCFM_Dependencies::wcfmu_plugin_active_check() ) { ?>
						<a href="<?php echo get_wcfm_appointments_url(); ?>">
					<?php } ?>
						<div id="wcfm_appointments_list_expander" class="wcfm-content">
							<div class="appointment_dashboard_section_icon"><span class="wcfmfa fa-calendar"></span></div>
							<div class="appointment_dashboard_section_label">
								<h2 title="<?php if( !WCFM_Dependencies::wcfmu_plugin_active_check() ) { wcfmu_feature_help_text_show( 'Appointments List', false, true ); } ?>"><?php _e( 'Appointments List', 'wc-frontend-manager-ultimate' ); ?></h2>
							</div>
						</div>
					<?php if( WCFM_Dependencies::wcfmu_plugin_active_check() ) { ?></a><?php } ?>
				</div>
			<?php } ?>
			
		  <?php if( $wcfm_is_allow_appointment_calendar = apply_filters( 'wcfm_is_allow_appointment_calendar', true ) ) { ?>
				<div class="wcfm-container">
					<?php if( WCFM_Dependencies::wcfmu_plugin_active_check() ) { ?>
						<a href="<?php echo get_wcfm_appointments_calendar_url(); ?>">
					<?php } ?>
						<div id="wcfm_appointments_calendar_expander" class="wcfm-content">
							<div class="appointment_dashboard_section_icon"><span class="wcfmfa fa-calendar-alt"></span></div>
							<div class="appointment_dashboard_section_label">
								<h2 title="<?php if( !WCFM_Dependencies::wcfmu_plugin_active_check() ) { wcfmu_feature_help_text_show( 'Appointments Calendar', false, true ); } ?>"><?php _e( 'Appointments Calendar', 'wc-frontend-manager-ultimate' ); ?></h2>
							</div>
						</div>
					<?php if( WCFM_Dependencies::wcfmu_plugin_active_check() ) { ?></a><?php } ?>
				</div>
			<?php } ?>
			
		</div>
		<div class="wcfm_clearfix"></div><br />
		
		<?php do_action( 'after_wcfm_wcvendors_appointments_dashboard' ); ?>
	</div>
</div>