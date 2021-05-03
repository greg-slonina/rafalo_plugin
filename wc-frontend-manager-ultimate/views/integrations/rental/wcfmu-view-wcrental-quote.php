<?php
/**
 * WCFM plugin view
 *
 * WCFM Rental Request Quote View
 *
 * @author 		WC Lovers
 * @package 	wcfmu/views/thirdparty
 * @version   3.2.3
 */

global $WCFM;

if( !$wcfm_is_allow_rental = apply_filters( 'wcfm_is_allow_rental', true ) ) {
	wcfm_restriction_message_show( "Request Quote" );
	return;
}

?>

<div class="collapse wcfm-collapse" id="wcfm_quotes_quote">
  <div class="wcfm-page-headig">
		<span class="wcfmfa fa-snowflake"></span>
		<span class="wcfm-page-heading-text"><?php _e( 'Quote Request', 'wc-frontend-manager-ultimate' ); ?></span>
		<?php do_action( 'wcfm_page_heading' ); ?>
	</div>
	<div class="wcfm-collapse-content">
	  <div id="wcfm_page_load"></div>
		
	  <div class="wcfm-container wcfm-top-element-container">
			<?php
			$wcfm_quotes_menus = apply_filters( 'redq_get_request_quote_post_statuses',
																																								array(
																																									'all'              => __( 'All', 'wc-frontend-manager'), 
																																									'quote-pending'    => _x( 'Pending', 'Quote status', 'redq-rental' ),
																																									'quote-processing' => _x( 'Processing', 'Quote status', 'redq-rental' ),
																																									'quote-on-hold'    => _x( 'On Hold', 'Quote status', 'redq-rental' ),
																																									'quote-accepted'   => _x( 'Accepted', 'Quote status', 'redq-rental' ),
																																									'quote-completed'  => _x( 'Completed', 'Quote status', 'redq-rental' ),
																																									'quote-cancelled'  => _x( 'Cancelled', 'Quote status', 'redq-rental' ),
																																								)
																																							);
		
			$quote_status = ! empty( $_GET['quote_status'] ) ? sanitize_text_field( $_GET['quote_status'] ) : 'all';
			
			?>
			<ul class="wcfm_quotes_menus">
				<?php
				$is_first = true;
				foreach( $wcfm_quotes_menus as $wcfm_quotes_menus_key => $wcfm_quotes_menu ) {
					?>
					<li class="wcfm_quotes_menu_item">
						<?php
						if($is_first) $is_first = false;
						else echo " | ";
						?>
						<a class="<?php echo ( $wcfm_quotes_menus_key == $quote_status ) ? 'active' : ''; ?>" href="<?php echo get_wcfm_rental_quote_url( $wcfm_quotes_menus_key ); ?>"><?php echo $wcfm_quotes_menu; ?></a>
					</li>
					<?php
				}
				?>
			</ul>
		
			<?php
			if( $allow_wp_admin_view = apply_filters( 'wcfm_allow_wp_admin_view', true ) ) {
				?>
				<a target="_blank" class="wcfm_wp_admin_view text_tip" href="<?php echo admin_url('edit.php?post_type=request_quote'); ?>" data-tip="<?php _e( 'WP Admin View', 'wc-frontend-manager-ultimate' ); ?>"><span class="fab fa-wordpress fa-wordpress-simple"></span></a>
				<?php
			}
			
			echo '<a class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_rental_url().'" data-tip="' . __('Calendar View', 'wc-frontend-manager-ultimate') . '"><span class="wcfmfa fa-calendar-check"></span></a>';
			
			if( $has_new = apply_filters( 'wcfm_add_new_product_sub_menu', true ) ) {
				echo '<a class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_edit_product_url().'" data-tip="' . __('Add New Product', 'wc-frontend-manager-ultimate') . '"><span class="wcfmfa fa-cube"></span><span class="text">' . __('Add New', 'wc-frontend-manager-ultimate') . '</span></a>';
			}
			?>
			
			<div class="wcfm-clearfix"></div>
		</div>
	  <div class="wcfm-clearfix"></div><br />
	  
	  <?php do_action( 'before_wcfm_quotes' ); ?>
			
		<div class="wcfm-container">
			<div id="wwcfm_quotes_expander" class="wcfm-content">
				<table id="wcfm-quotes" class="display" cellspacing="0" width="100%">
					<thead>
						<tr>
							<th><?php _e( 'Quote', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Product', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Status', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Email', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Date', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Actions', 'wc-frontend-manager-ultimate' ); ?></th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th><?php _e( 'Quote', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Product', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Status', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Email', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Date', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Actions', 'wc-frontend-manager-ultimate' ); ?></th>
						</tr>
					</tfoot>
				</table>
				<div class="wcfm-clearfix"></div>
			</div>
		</div>
		<?php
		do_action( 'after_wcfm_quotes' );
		?>
	</div>
</div>