<?php
/**
 * WCFM plugin views
 *
 * Plugin Tych Booking Calendar Views
 *
 * @author 		WC Lovers
 * @package 	wcfmu/views/tych_bookings
 * @version   5.4.7
 */
global $WCFM, $WCFMu, $wc_bookings;

$wcfm_is_allow_booking_calendar = apply_filters( 'wcfm_is_allow_booking_calendar', true );
if( !apply_filters( 'wcfm_is_allow_manage_booking', true ) || !$wcfm_is_allow_booking_calendar ) {
	wcfm_restriction_message_show( "Bookings Calendar" );
	return;
}

do_action( 'delete_booking_dr_transients' );
$view           = isset( $_REQUEST['view'] ) && 'day' === $_REQUEST['view'] ? 'Day' : 'Month';
?>
<div class="collapse wcfm-collapse" id="wcfm_bookings_listing">
  <div class="wcfm-page-headig">
		<span class="wcfmfa fa-calendar-alt"></span>
		<span class="wcfm-page-heading-text"><?php _e( 'Bookings Calendar', 'wc-frontend-manager-ultimate' ); ?></span>
		<?php do_action( 'wcfm_page_heading' ); ?>
	</div>
	<div class="wcfm-collapse-content">
	  <div id="wcfm_page_load"></div>
	  
	  <div class="wcfm-container wcfm-top-element-container">
			<h2><?php printf( __( 'Bookings Calendar', 'wc-frontend-manager-ultimate' ), $view ); ?></h2>
			<?php
			if( $allow_wp_admin_view = apply_filters( 'wcfm_allow_wp_admin_view', true ) ) {
				?>
				<a target="_blank" class="wcfm_wp_admin_view text_tip" href="<?php echo admin_url('edit.php?post_type=wc_booking&page=booking_calendar'); ?>" data-tip="<?php _e( 'WP Admin View', 'wc-frontend-manager-ultimate' ); ?>"><span class="fab fa-wordpress fa-wordpress-simple"></span></a>
				<?php
			}
			
			if( $wcfm_is_allow_manual_booking = apply_filters( 'wcfm_is_allow_manual_booking', true ) ) {
				echo '<a class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_create_tych_booking_url().'" data-tip="' . __( 'Create Booking', 'wc-frontend-manager-ultimate' ) . '"><span class="wcfmfa fa-calendar-plus"></span></a>';
			}
			
			if( $wcfm_is_allow_manage_resource = apply_filters( 'wcfm_is_allow_manage_resource', true ) ) {
				echo '<a class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_tych_booking_resources_url().'" data-tip="' . __( 'Manage Resources', 'wc-frontend-manager-ultimate' ) . '"><span class="wcfmfa fa-briefcase"></span></a>';
			}
			
			if( apply_filters( 'wcfm_add_new_product_sub_menu', true ) && apply_filters( 'wcfm_is_allow_create_bookable', true ) ) {
				echo '<a class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_edit_product_url().'" data-tip="' . __('Create Bookable', 'wc-frontend-manager-ultimate') . '"><span class="wcfmfa fa-cube"></span></a>';
			}
			echo '<a class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_tych_booking_url().'" data-tip="' . __( 'Bookings List', 'wc-frontend-manager-ultimate' ) . '"><span class="wcfmfa fa-calendar"></span></a>';
			?>
			<div class="wcfm-clearfix"></div>
		</div>
	  <div class="wcfm-clearfix"></div><br />
	  
	  <?php do_action( 'before_wcfm_bookings_calendar' ); ?>
			
		<div class="wcfm-container">
		  <div id="wwcfm_bookings_listing_expander" class="wcfm-content">
		    <div id='calendar'></div>
		    <div class="wcfm-clearfix"></div>
			</div>
		</div>
		<?php
		do_action( 'after_wcfm_bookings_calendar' );
		?>
	</div>
</div>