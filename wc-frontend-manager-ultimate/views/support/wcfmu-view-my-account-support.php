<?php
/**
 * WCFMu plugin view
 *
 * WCFM Support view
 *
 * @author 		WC Lovers
 * @package 	wcfm/views/support
 * @version   4.0.3
 */
 
global $WCFM, $WCFMu, $wpdb;

if( !apply_filters( 'wcfm_is_pref_support', true ) || !apply_filters( 'wcfm_is_allow_support', true ) ) {
	wcfm_restriction_message_show( "Supports" );
	return;
}

if( is_user_logged_in() ) {
	$support_query = "SELECT * FROM {$wpdb->prefix}wcfm_support AS commission";
	$support_query .= " WHERE 1 = 1";
	$support_query .= " AND `customer_id` = " . get_current_user_id();
	$support_query .= " ORDER BY commission.`ID` DESC";
	
	$wcfm_supports = $wpdb->get_results( $support_query );
	
	$myaccount_page_id = get_option( 'woocommerce_myaccount_page_id' );
	if ( $myaccount_page_id ) {
		$myaccount_page_url = trailingslashit( get_permalink( $myaccount_page_id ) );
	}
	$support_priority_types = $WCFMu->wcfmu_support->wcfm_support_priority_types();
	
	$wcfm_myac_modified_endpoints = wcfm_get_option( 'wcfm_myac_endpoints', array() );
	$wcfm_myaccount_support_ticket_endpoint = ! empty( $wcfm_myac_modified_endpoints['support-tickets'] ) ? $wcfm_myac_modified_endpoints['support-tickets'] : 'support-tickets';
	$wcfm_myaccount_view_support_ticket_endpoint = ! empty( $wcfm_myac_modified_endpoints['view-support-ticket'] ) ? $wcfm_myac_modified_endpoints['view-support-ticket'] : 'view-support-ticket';
	?>
	<div class="touch-scroll-table">
		<table class="woocommerce-support-tickets-table woocommerce-MyAccount-support-tickets shop_table shop_table_responsive my_account_support-tickets account-support-tickets-table">
			<thead>
				<tr>
					<th class="woocommerce-support-tickets-table__header woocommerce-support-tickets-table__header-support-tickets-status"><span class="nobr"><?php _e( 'Status', 'wc-frontend-manager-ultimate' ); ?></span></th>
					<th class="woocommerce-support-tickets-table__header woocommerce-support-tickets-table__header-support-tickets-number"><span class="nobr"><?php _e( 'Ticket(s)', 'wc-frontend-manager-ultimate' ); ?></span></th>
					<?php if( !wcfm_is_mobile() ) { ?>
						<th class="woocommerce-support-tickets-table__header woocommerce-support-tickets-table__header-support-tickets-category"><span class="nobr"><?php _e( 'Category', 'wc-frontend-manager-ultimate' ); ?></span></th>
					<?php } ?>
					<th class="woocommerce-support-tickets-table__header woocommerce-support-tickets-table__header-support-tickets-priority"><span class="nobr"><?php _e( 'Priority', 'wc-frontend-manager-ultimate' ); ?></span></th>
					<th class="woocommerce-support-tickets-table__header woocommerce-support-tickets-table__header-support-tickets-actions"><span class="nobr"><?php _e( 'Actions', 'wc-frontend-manager-ultimate' ); ?></span></th>
				</tr>
			</thead>
			<tbody>
				<?php if( !empty( $wcfm_supports ) ) { foreach( $wcfm_supports as $wcfm_support ) { ?>
					<tr class="woocommerce-support-tickets-table__row woocommerce-support-tickets-table__row--status-completed support-tickets">
						<td class="woocommerce-support-tickets-table__cell woocommerce-support-tickets-table__cell-support-tickets-status" data-title="<?php _e( 'Status', 'wc-frontend-manager-ultimate' ); ?>">
							<?php 
							if( $wcfm_support->status == 'open' ) {
								echo '<span class="support-status tips wcicon-status-processing text_tip" data-tip="' . __( 'Open', 'wc-frontend-manager-ultimate' ) . '"></span>';
							} else {
								echo '<span class="support-status tips wcicon-status-completed text_tip" data-tip="' . __( 'Closed', 'wc-frontend-manager-ultimate' ) . '"></span>';
							} 
							?>
						</td>
						<td class="woocommerce-support-tickets-table__cell woocommerce-support-tickets-table__cell-support-tickets-number" data-title="<?php _e( 'Ticket(s)', 'wc-frontend-manager-ultimate' ); ?>">
							<a href="<?php echo $myaccount_page_url . $wcfm_myaccount_view_support_ticket_endpoint . '/' . $wcfm_support->ID; ?>"><?php echo __( 'Ticket', 'wc-frontend-manager-ultimate' ) . ' #' . sprintf( '%06u', $wcfm_support->ID ); ?></a>
						</td>
						<?php if( !wcfm_is_mobile() ) { ?>
							<td class="woocommerce-support-tickets-table__cell woocommerce-support-tickets-table__cell-support-tickets-category" data-title="<?php _e( 'Category', 'wc-frontend-manager-ultimate' ); ?>">
								<?php echo $wcfm_support->category; ?>
							</td>
						<?php } ?>
						<td class="woocommerce-support-tickets-table__cell woocommerce-support-tickets-table__cell-support-tickets-priority" data-title="<?php _e( 'Priority', 'wc-frontend-manager-ultimate' ); ?>">
							<?php echo '<span class="support-priority support-priority-' . $wcfm_support->priority . '">' . $support_priority_types[$wcfm_support->priority] . '</span>'; ?>
						</td>
						<td class="woocommerce-support-tickets-table__cell woocommerce-support-tickets-table__cell-support-tickets-actions" data-title="<?php _e( 'Actions', 'wc-frontend-manager-ultimate' ); ?>">
							<a href="<?php echo $myaccount_page_url . $wcfm_myaccount_view_support_ticket_endpoint . '/' . $wcfm_support->ID; ?>" class="woocommerce-button button view"><?php _e( 'View', 'wc-frontend-manager-ultimate' ); ?></a>													
						</td>
					</tr>
				<?php } } else { ?>
					<tr class="woocommerce-support-tickets-table__row woocommerce-support-tickets-table__row--status-completed support-tickets">
						<td class="woocommerce-support-tickets-table__cell woocommerce-support-tickets-table__cell-followings-no" colspan="4" data-title="<?php _e( 'Ticket(s)', 'wc-frontend-manager-ultimate' ); ?>">
							<?php _e( 'You do not have any support ticket yet!', 'wc-frontend-manager-ultimate' ); ?>
						</td>
						<td></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
	<?php
}