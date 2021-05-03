<?php
/**
 * WCFM plugin view
 *
 * WCFM Reports - Low in Stock View
 *
 * @author 		WC Lovers
 * @package 	wcfmu/view
 * @version   1.0.0
 */
 
$wcfm_is_allow_reports = apply_filters( 'wcfm_is_allow_reports', true );
if( !$wcfm_is_allow_reports ) {
	wcfm_restriction_message_show( "Reports" );
	return;
}

global $wp, $WCFM, $WCFMu, $wpdb;

if( isset( $wp->query_vars['wcfm-reports-low-in-stock'] ) && !empty( $wp->query_vars['wcfm-reports-low-in-stock'] ) ) {
	$wcfmu_report_type = $wp->query_vars['wcfm-reports-low-in-stock'];
}

?>

<div class="collapse wcfm-collapse" id="wcfm_report_details">

  <div class="wcfm-page-headig">
		<span class="wcfmfa fa-sort-amount-down"></span>
		<span class="wcfm-page-heading-text"><?php _e( 'Low in Stock', 'wc-frontend-manager-ultimate' ); ?></span>
		<?php do_action( 'wcfm_page_heading' ); ?>
	</div>
	<div class="wcfm-collapse-content">
		<div id="wcfm_page_load"></div>
		
		<div class="wcfm-container wcfm-top-element-container">
			<?php $WCFM->template->get_template( 'reports/wcfm-view-reports-menu.php' ); ?>
			<?php
			if( $allow_wp_admin_view = apply_filters( 'wcfm_allow_wp_admin_view', true ) ) {
				?>
				<a target="_blank" class="wcfm_wp_admin_view text_tip" href="<?php echo admin_url('admin.php?page=wc-reports&tab=stock&report=low_in_stock'); ?>" data-tip="<?php _e( 'WP Admin View', 'wc-frontend-manager-ultimate' ); ?>"><span class="fab fa-wordpress fa-wordpress-simple"></span></a>
				<?php
			}
			?>
			<div class="wcfm-clearfix"></div>
		</div>
	  <div class="wcfm-clearfix"></div><br />
	  
		<div class="wcfm-container">
			<div id="wcfm_report_details_expander" class="wcfm-content">
				<table id="wcfm-reports-low-in-stock" class="display" cellspacing="0" width="100%">
					<thead>
						<tr>
							<th><?php _e( 'Product', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Parent', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Unit in stock', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Stock Status', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Actions', 'wc-frontend-manager-ultimate' ); ?></th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th><?php _e( 'Product', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Parent', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Unit in stock', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Stock Status', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Actions', 'wc-frontend-manager-ultimate' ); ?></th>
						</tr>
					</tfoot>
				</table>
				<div class="wcfm-clearfix"></div>
			</div>
		</div>
	</div>
</div>