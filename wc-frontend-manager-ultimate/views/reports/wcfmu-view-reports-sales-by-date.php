<?php
/**
 * WCFM plugin view
 *
 * WCFM Reports - Sales by Date View
 *
 * @author 		WC Lovers
 * @package 	wcfmu/view
 * @version   1.0.0
 */
 
$wcfm_is_allow_reports = apply_filters( 'wcfm_is_allow_reports', true );
if( !$wcfm_is_allow_reports ) {
	//wcfm_restriction_message_show( "Reports" );
	return;
}

global $wp, $WCFM, $WCFMu, $wpdb;

$current_range = ! empty( $_GET['range'] ) ? sanitize_text_field( $_GET['range'] ) : '7day';
?>

<li class="custom <?php echo $current_range == 'custom' ? 'active' : ''; ?>">
		<?php _e( 'Custom:', 'wc-frontend-manager-ultimate' ); ?>
		<form method="GET">
			<div>
				<?php
					// Maintain query string
					foreach ( $_GET as $key => $value ) {
						if ( is_array( $value ) ) {
							foreach ( $value as $v ) {
								echo '<input type="hidden" name="' . esc_attr( sanitize_text_field( $key ) ) . '[]" value="' . esc_attr( sanitize_text_field( $v ) ) . '" />';
							}
						} else {
							echo '<input type="hidden" name="' . esc_attr( sanitize_text_field( $key ) ) . '" value="' . esc_attr( sanitize_text_field( $value ) ) . '" />';
						}
					}
				?>
				<input type="hidden" name="range" value="custom" />
				<input type="text" size="9" placeholder="<?php echo apply_filters( 'wcfm_date_filter_format', wc_date_format() ); ?>" data-date_format="<?php echo str_replace( 'mmmm', 'mm', str_replace( 'yyyy', 'yy', strtolower( wcfm_wp_date_format_to_js( wc_date_format() ) ) ) ); ?>" value="<?php if ( ! empty( $_GET['start_date'] ) ) echo esc_attr( $_GET['start_date'] ); ?>" name="start_date" class="range_datepicker from" />
				<input type="text" size="9" placeholder="<?php echo apply_filters( 'wcfm_date_filter_format', wc_date_format() ); ?>" data-date_format="<?php echo str_replace( 'mmmm', 'mm', str_replace( 'yyyy', 'yy', strtolower( wcfm_wp_date_format_to_js( wc_date_format() ) ) ) ); ?>" value="<?php if ( ! empty( $_GET['end_date'] ) ) echo esc_attr( $_GET['end_date'] ); ?>" name="end_date" class="range_datepicker to" />
				<input type="submit" class="button wcfm_add_attribute" value="<?php esc_attr_e( 'Go', 'wc-frontend-manager-ultimate' ); ?>" />
			</div>
		</form>
	</li>