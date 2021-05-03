<?php
/**
 * WCFM plugin view
 *
 * WCFM Auctions View
 *
 * @author 		WC Lovers
 * @package 	wcfmu/views/thirdparty
 * @version   2.4.0
 */

global $WCFM;

if( !$wcfm_allow_auction = apply_filters( 'wcfm_is_allow_auction', true ) ) {
	wcfm_restriction_message_show( "Auctions" );
	return;
}

?>

<div class="collapse wcfm-collapse" id="wcfm_auctions_listing">
  <div class="wcfm-page-headig">
		<span class="wcfmfa fa-gavel"></span>
		<span class="wcfm-page-heading-text"><?php _e( 'Auctions', 'wc-frontend-manager-ultimate' ); ?></span>
		<?php do_action( 'wcfm_page_heading' ); ?>
	</div>
	<div class="wcfm-collapse-content">
	  <div id="wcfm_page_load"></div>
		
	  <div class="wcfm-container wcfm-top-element-container">
			<h2><?php _e( 'Auction Activity', 'wc-frontend-manager-ultimate' ); ?></h2>
			
			<?php
			if( $allow_wp_admin_view = apply_filters( 'wcfm_allow_wp_admin_view', true ) ) {
				if( WCFMu_Dependencies::wcfm_wcs_auction_active_check() ) {
					?>
					<a target="_blank" class="wcfm_wp_admin_view text_tip" href="<?php echo admin_url('admin.php?page=auctions-activity'); ?>" data-tip="<?php _e( 'WP Admin View', 'wc-frontend-manager-ultimate' ); ?>"><span class="fab fa-wordpress fa-wordpress-simple"></span></a>
					<?php
				}
			}
			
			if( $has_new = apply_filters( 'wcfm_add_new_product_sub_menu', true ) ) {
				echo '<a class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_edit_product_url().'" data-tip="' . __('Add New Product', 'wc-frontend-manager-ultimate') . '"><span class="wcfmfa fa-cube"></span><span class="text">' . __('Add New', 'wc-frontend-manager-ultimate') . '</span></a>';
			}
			?>
			
			<div class="wcfm-clearfix"></div>
		</div>
	  <div class="wcfm-clearfix"></div><br />
	  
	  <?php do_action( 'before_wcfm_auctions' ); ?>
	  
		<div class="wcfm-container">
			<div id="wwcfm_auctions_expander" class="wcfm-content">
				<table id="wcfm-auctions" class="display" cellspacing="0" width="100%">
					<thead>
						<tr>
							<th><?php _e( 'Auction', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'User', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Bid', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Date', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Actions', 'wc-frontend-manager-ultimate' ); ?></th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th><?php _e( 'Auction', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'User', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Bid', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Date', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Actions', 'wc-frontend-manager-ultimate' ); ?></th>
						</tr>
					</tfoot>
				</table>
				<div class="wcfm-clearfix"></div>
			</div>
		</div>
		<?php
		do_action( 'after_wcfm_auctions' );
		?>
	</div>
</div>