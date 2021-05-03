<?php
/**
 * WCFM plugin views
 *
 * Plugin Tych Booking Resources Manage Views
 *
 * @author 		WC Lovers
 * @package 	wcfmu/views/tych_bookings
 * @version   5.4.7
 */
global $wp, $WCFM, $WCFMu;

$wcfm_is_allow_manage_resource = apply_filters( 'wcfm_is_allow_manage_resource', true );
if( !apply_filters( 'wcfm_is_allow_manage_booking', true ) || !$wcfm_is_allow_manage_resource ) {
	wcfm_restriction_message_show( "Bookings Resources" );
	return;
}

$resource_id = 0;
$title = '';
$qty = 1;

$availability_rules = array();

if( isset( $wp->query_vars['wcfm-booking-resources-manage'] ) && !empty( $wp->query_vars['wcfm-booking-resources-manage'] ) ) {
	$resource_id = $wp->query_vars['wcfm-booking-resources-manage'];
	
	$resource = new BKAP_Product_Resource( $resource_id );
	
	$title = $resource->get_title();
	$qty = max( $resource->get_resource_qty(), 1 );
	$availability_rules = $resource->get_resource_availability();
}

do_action( 'before_wcfm_resources_manage' );

?>

<div class="collapse wcfm-collapse">
  <div class="wcfm-page-headig">
		<span class="wcfmfa fa-briefcase"></span>
		<span class="wcfm-page-heading-text"><?php _e( 'Manage Resource', 'wc-frontend-manager-ultimate' ); ?></span>
		<?php do_action( 'wcfm_page_heading' ); ?>
	</div>
	<div class="wcfm-collapse-content">
	  <div id="wcfm_page_load"></div>
			
		<div class="wcfm-container wcfm-top-element-container">
			<h2><?php if( $resource_id ) { _e('Edit Resource', 'wc-frontend-manager-ultimate' ); } else { _e('Add Resource', 'wc-frontend-manager-ultimate' ); } ?></h2>
			
			<?php
			if( $allow_wp_admin_view = apply_filters( 'wcfm_allow_wp_admin_view', true ) ) {
				?>
				<a target="_blank" class="wcfm_wp_admin_view text_tip" href="<?php echo admin_url('post-new.php?post_type=bookable_resource'); ?>" data-tip="<?php _e( 'WP Admin View', 'wc-frontend-manager-ultimate' ); ?>"><span class="fab fa-wordpress fa-wordpress-simple"></span></a>
				<?php
			}
			
			if( $wcfm_is_allow_booking_calendar = apply_filters( 'wcfm_is_allow_booking_calendar', true ) ) {
				echo '<a class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_tych_booking_calendar_url().'" data-tip="'. __('Calendar View', 'wc-frontend-manager-ultimate') .'"><span class="wcfmfa fa-calendar-alt"></span></a>';
			}
			
			echo '<a class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_tych_booking_url().'" data-tip="' . __( 'Bookings List', 'wc-frontend-manager-ultimate' ) . '"><span class="wcfmfa fa-calendar"></span></a>';
			
			if( $wcfm_is_allow_manage_resource = apply_filters( 'wcfm_is_allow_manage_resource', true ) ) {
				echo '<a class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_tych_booking_resources_url().'" data-tip="' . __( 'Manage Resources', 'wc-frontend-manager-ultimate' ) . '"><span class="wcfmfa fa-briefcase"></span></a>';
			}
			
			if( apply_filters( 'wcfm_add_new_product_sub_menu', true ) && apply_filters( 'wcfm_is_allow_create_bookable', true ) ) {
				echo '<a class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_edit_product_url().'" data-tip="' . __('Create Bookable', 'wc-frontend-manager-ultimate') . '"><span class="wcfmfa fa-cube"></span></a>';
			}
		
			?>
			<div class="wcfm-clearfix"></div>
		</div>
		<div class="wcfm-clearfix"></div><br />
			
		<?php do_action( 'begin_wcfm_resources_manage' ); ?>
		
		<form id="wcfm_resources_manage_form" class="wcfm">
			<?php do_action( 'begin_wcfm_resources_manage_form' ); ?>
				
			<!-- collapsible -->
			<div class="wcfm-container">
				<div id="resources_manage_general_expander" class="wcfm-content">
					<?php
						$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'resource_manager_fields_general', array(  
																																														"title" => array( 'label' => __( 'Resource Title', 'wc-frontend-manager-ultimate') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele ', 'label_class' => 'wcfm_ele wcfm_title', 'value' => $title),
																																														"_bkap_booking_qty" => array('label' => __( 'Available Quantity', 'wc-frontend-manager-ultimate'), 'type' => 'number', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_ele wcfm_title', 'value' => $qty ),
																																														"resource_id" => array('type' => 'hidden', 'value' => $resource_id)
																																				) ) );
					?>
					
					<div id="bkap_resource_availability" class="options_group">
						<table class="widefat">
							<thead>
								<tr>
									<th><b><?php esc_html_e( 'Range type', 'woocommerce-booking' ); ?></b></th>
									<th><b><?php esc_html_e( 'From', 'woocommerce-booking' ); ?></b></th>
									<th></th>
									<th><b><?php esc_html_e( 'To', 'woocommerce-booking' ); ?></b></th>
									<th><b><?php esc_html_e( 'Bookable', 'woocommerce-booking' ); ?></b></th>
									<th><b><?php esc_html_e( 'Priority', 'woocommerce-booking' ); ?></b></th>
									<th class="remove" width="1%">&nbsp;</th>
								</tr>
							</thead>
							
							<tfoot>
								<tr >
									<th colspan="4" style="text-align: left;font-size: 11px;font-style: italic;">
										<?php esc_html_e( 'Rules with lower priority numbers will override rules with a higher priority (e.g. 9 overrides 10 ).', 'woocommerce-booking' ); ?>
									</th>	
									<th colspan="3" style="text-align: right;">
										<a href="#" class="button button-primary bkap_add_row_resource" style="text-align: right;" data-row="<?php
											ob_start();
											include( 'resource-manage/html_resource_availability_table.php' );
											$html = ob_get_clean();
											echo esc_attr( $html );
										?>"><?php esc_html_e( 'Add Range', 'woocommerce-booking' ); ?></a>
									</th>
								</tr>
							</tfoot>
							
							<tbody id="availability_rows">
								<?php
									if ( ! empty( $availability_rules ) && is_array( $availability_rules ) ) {
										foreach ( $availability_rules as $availability ) {
											include( 'resource-manage/html_resource_availability_table.php' );
										}
									}
								?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="wcfm_clearfix"></div><br />
			<!-- end collapsible -->
			 
			<?php do_action( 'end_wcfm_resources_manage_form' ); ?>
			
			<div class="wcfm-message" tabindex="-1"></div>
			
			<div id="wcfm_resource_manager_submit">
				<input type="submit" name="submit-data" value="<?php _e( 'Submit', 'wc-frontend-manager-ultimate' ); ?>" id="wcfm_resource_manager_submit_button" class="wcfm_submit_button" />
			</div>
			<?php
			do_action( 'after_wcfm_resources_manage' );
			?>
		</form>
	</div>
</div>