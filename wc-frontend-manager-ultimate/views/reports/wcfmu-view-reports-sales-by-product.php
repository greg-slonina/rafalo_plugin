<?php
/**
 * WCFM plugin view
 *
 * WCFM Reports - Sales by Product View
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

if( isset( $wp->query_vars['wcfm-reports-sales-by-product'] ) && !empty( $wp->query_vars['wcfm-reports-sales-by-product'] ) ) {
	$wcfm_report_type = $wp->query_vars['wcfm-reports-sales-by-product'];
}

include_once( $WCFMu->plugin_path . '/includes/reports/class-wcfm-report-sales-by-product.php' );

$wcfm_report_sales_by_product = new WCFM_Report_Sales_By_Product();

$ranges = array(
	'year'         => __( 'Year', 'wc-frontend-manager' ),
	'last_month'   => __( 'Last Month', 'wc-frontend-manager' ),
	'month'        => __( 'This Month', 'wc-frontend-manager' ),
	'7day'         => __( 'Last 7 Days', 'wc-frontend-manager' )
);

$wcfm_report_sales_by_product->chart_colours = apply_filters( 'wcfm_sales_by_product_bar_chart_colors', array(
	'sales_amount' => '#3498db',
	'item_count'   => '#d4d9dc',
) );

$current_range = ! empty( $_GET['range'] ) ? sanitize_text_field( $_GET['range'] ) : '7day';

if ( ! in_array( $current_range, array( 'custom', 'year', 'last_month', 'month', '7day' ) ) )
	$current_range = '7day';


$wcfm_report_sales_by_product->calculate_current_range( $current_range );

?>

<div class="collapse wcfm-collapse" id="wcfm_report_details">

  <div class="wcfm-page-headig">
		<span class="wcfmfa fa-chart-pie"></span>
		<span class="wcfm-page-heading-text">
		  <?php if ( 'custom' === $current_range && isset( $_GET['start_date'], $_GET['end_date'] ) ) : ?>
			<?php _e( 'Sales by Product', 'wc-frontend-manager-ultimate' ); ?> - <?php echo esc_html( sprintf( _x( 'From %s to %s', 'start date and end date', 'wc-frontend-manager-ultimate' ), wc_clean( $_GET['start_date'] ), wc_clean( $_GET['end_date'] ) ) ); ?><span></span>
			<?php else : ?>
				<?php _e( 'Sales by Product', 'wc-frontend-manager-ultimate' ); ?> - <?php echo esc_html( $ranges[ $current_range ] ); ?><span></span>
			<?php endif; ?>
		</span>
		<?php do_action( 'wcfm_page_heading' ); ?>
	</div>
	<div class="wcfm-collapse-content">
		<div id="wcfm_page_load"></div>
		
		<div class="wcfm-container wcfm-top-element-container">
			<?php $WCFM->template->get_template( 'reports/wcfm-view-reports-menu.php' ); ?>
			<?php
			if( $allow_wp_admin_view = apply_filters( 'wcfm_allow_wp_admin_view', true ) ) {
				?>
				<a target="_blank" class="wcfm_wp_admin_view text_tip" href="<?php echo admin_url('admin.php?page=wc-reports&tab=orders&report=sales_by_product'); ?>" data-tip="<?php _e( 'WP Admin View', 'wc-frontend-manager-ultimate' ); ?>"><span class="fab fa-wordpress fa-wordpress-simple"></span></a>
				<?php
			}
			?>
			<div class="wcfm-clearfix"></div>
		</div>
	  <div class="wcfm-clearfix"></div><br />

		<div class="wcfm-container">
			<div id="wcfm_reports_sales_by_product_expander" class="wcfm-content">
			
				<?php
					include( $WCFMu->plugin_path . '/views/reports/wcfmu-html-report-sales-by-product.php');
				?>
				<div class="wcfm-clearfix"></div>
			</div>
		</div>
		<script>
		jQuery(function(){
			if( jQuery("#product_ids").length > 0 ) {
				jQuery("#product_ids").select2( $wcfm_product_select_args );
			}
			jQuery('input[name="page"]').remove();
			jQuery('input[name="tab"]').remove();
		});
		</script>
	</div>
</div>