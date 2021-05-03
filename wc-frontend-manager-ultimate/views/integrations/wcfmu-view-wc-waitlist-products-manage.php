<?php
/**
 * WCFM plugin view
 *
 * WCFM WC Waitlist Product Manage View
 *
 * @author 		WC Lovers
 * @package 	wcfmu/views/thirdparty
 * @version   4.1.5
 */
 
global $wp, $WCFM, $WCFMu, $post, $woocommerce;

if( !apply_filters( 'wcfm_is_allow_wc_waitlist', true ) ) {
	return;
}

$product_id = 0;
$users  = array();

if( isset( $wp->query_vars['wcfm-products-manage'] ) && !empty( $wp->query_vars['wcfm-products-manage'] ) ) {
	$product_id = $wp->query_vars['wcfm-products-manage'];
	
	$product  = wc_get_product( $product_id );
	if( $product && !empty($product) && is_object($product) ) {
		$waitlist = new Pie_WCWL_Waitlist( $product );
		$users    = $waitlist->waitlist;
	}
}

?>

<div class="page_collapsible products_manage_wc_waitlist simple variable external grouped booking" id="wcfm_products_manage_form_wc_waitlist_head"><label class="wcfmfa fa-users"></label><?php _e('Waitlists', 'wc-frontend-manager-ultimate'); ?><span></span></div>
<div class="wcfm-container simple variable external grouped booking">
	<div id="wcfm_products_manage_form_wc_waitlist_expander" class="wcfm-content">
	  <h2><?php _e('Product Waitlists', 'wc-frontend-manager-ultimate'); ?></h2>
	  <div class="wcfm_clearfix"></div>
	  <div class="waitlist wcwl_tab_content current" data-panel="waitlist">
	  
	    <?php if( empty( $users ) ) { ?>
				<div class="wcwl_no_users">
					<p class="wcwl_no_users_text">
						<?php esc_html_e( apply_filters( 'wcwl_empty_waitlist_introduction', __( 'There are no users on the waiting list for this product.', 'woocommerce-waitlist' ) ) ); ?>
					</p>
				</div>
			<?php } else { ?>

				<table class="widefat wcwl_waitlist_table">
					<tr>
						<th><?php _e( 'User', 'woocommerce-waitlist' ); ?></th>
						<th><?php _e( 'Added', 'woocommerce-waitlist' ); ?></th>
					</tr>
					<?php
					foreach ( $users as $user_id => $date ) {
						$user = get_user_by( 'id', $user_id );
						if ( $user ) {
							?>
							<tr class="wcwl_user_row" data-user-id="<?php echo $user_id; ?>">
							<?php if ( $user ) { ?>
								<td>
									<strong><?php echo $user->user_email; ?></strong>
								</td>
								<td>
									<?php echo date( wc_date_format(), $date ); ?>
								</td>
							<?php } else { ?>
								<td>
									<input class="wcwl_user_checkbox" type="checkbox" name="wcwl_user_checkbox wcwl_removed_user" value="0" data-user-email="0" data-date-added="<?php echo $date ?>"/>
								</td>
								<td>
									<strong><?php _e( 'User removed themselves', 'woocommerce-waitlist' ); ?></strong>
								</td>
								<td>
									<?php echo date( wc_date_format(), $date ); ?>
								</td>
							<?php } ?>
						</tr>
						<?php
						}
					} 
					?>
				</table>
			<?php } ?>
		</div>
	</div>
</div>