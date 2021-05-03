<?php
/**
 * WCFMu plugin view
 *
 * WCFM Followings view
 *
 * @author 		WC Lovers
 * @package 	wcfm/views/followings
 * @version   4.0.3
 */
 
global $WCFM, $WCFMu, $wpdb;

if( !apply_filters( 'wcfm_is_pref_vendor_followers', true ) || !apply_filters( 'wcfm_is_allow_followings', true ) ) {
	wcfm_restriction_message_show( "Followings" );
	return;
}

if( is_user_logged_in() ) {
	$followings_query = "SELECT * FROM {$wpdb->prefix}wcfm_following_followers AS followings";
	$followings_query .= " WHERE 1 = 1";
	$followings_query .= " AND `follower_id` = " . get_current_user_id();
	$followings_query .= " ORDER BY followings.`ID` DESC";
	
	$wcfm_followings = $wpdb->get_results( $followings_query );
	?>
	<table class="woocommerce-followings-table woocommerce-MyAccount-followings shop_table shop_table_responsive my_account_followings account-followings-table">
		<thead>
			<tr>
				<th class="woocommerce-followings-tickets-table__header woocommerce-followings-tickets-table__header-followings-name"><span class="nobr"><?php echo apply_filters( 'wcfm_sold_by_label', '', __( 'Store', 'wc-frontend-manager' ) ); ?></span></th>
				<?php if( apply_filters( 'wcfm_is_allow_followings_email', true ) ) { ?>
				  <th class="woocommerce-followings-tickets-table__header woocommerce-followings-tickets-table__header-followings-Email"><span class="nobr"><?php _e( 'Email', 'wc-frontend-manager-ultimate' ); ?></span></th>
				<?php } ?>
				<th class="woocommerce-followings-tickets-table__header woocommerce-followings-tickets-table__header-followings-actions"><span class="nobr"><?php _e( 'Actions', 'wc-frontend-manager-ultimate' ); ?></span></th>
			</tr>
		</thead>
		<tbody>
			<?php if( !empty( $wcfm_followings ) ) { foreach( $wcfm_followings as $wcfm_following_single ) { ?>
				<tr class="woocommerce-followings-table__row woocommerce-followings-table__row--status-completed followings">
					<td class="woocommerce-followings-table__cell woocommerce-followings-table__cell-followings-name" data-title="<?php echo apply_filters( 'wcfm_sold_by_label', '', __( 'Store', 'wc-frontend-manager' ) ); ?>">
						<?php if( apply_filters( 'wcfmmp_is_allow_sold_by_linked', true ) ) { echo $WCFM->wcfm_vendor_support->wcfm_get_vendor_store_by_vendor( $wcfm_following_single->user_id ); } else { echo $WCFM->wcfm_vendor_support->wcfm_get_vendor_store_name_by_vendor( absint($wcfm_following_single->user_id) ); } ?>
					</td>
					<?php if( apply_filters( 'wcfm_is_allow_followings_email', true ) ) { ?>
						<td class="woocommerce-followings-table__cell woocommerce-followings-table__cell-followings-email" data-title="<?php _e( 'Email', 'wc-frontend-manager-ultimate' ); ?>">
						  <?php if( $WCFM->wcfm_vendor_support->wcfm_vendor_has_capability( $wcfm_following_single->user_id, 'vendor_email' ) ) { ?>
							  <a href="mailto:<?php echo $wcfm_following_single->user_email; ?>"><?php echo $wcfm_following_single->user_email; ?></a>
							<?php } else { ?>
								&ndash;
							<?php } ?>
						</td>
					<?php } ?>
					<td class="woocommerce-followings-table__cell woocommerce-followings-table__cell-followings-actions" data-title="<?php _e( 'Actions', 'wc-frontend-manager-ultimate' ); ?>">
						<a class="wcfm_followings_delete wcfm-action-icon" href="#" data-lineid="<?php echo $wcfm_following_single->ID; ?>" data-followersid="<?php echo $wcfm_following_single->follower_id; ?>" data-userid="<?php echo $wcfm_following_single->user_id; ?>" class="woocommerce-button button delete"><span class="wcfmfa fa-trash-alt text_tip" data-tip="<?php echo esc_attr__( 'Delete', 'wc-frontend-manager-ultimate' ); ?>"></span></a>													
					</td>
				</tr>
			<?php } } else { ?>
				<tr class="woocommerce-followings-table__row woocommerce-followings-table__row--status-completed followings">
				  <td class="woocommerce-followings-table__cell woocommerce-followings-table__cell-followings-no" data-title="<?php _e( 'NO Followings', 'wc-frontend-manager-ultimate' ); ?>" colspan="2">
				    <?php _e( 'You are not following any vendor yet!', 'wc-frontend-manager-ultimate' ); ?>
				  </td>
				  <td></td>
				</tr>
			<?php } ?>
		</tbody>
	</table>
	<?php
}