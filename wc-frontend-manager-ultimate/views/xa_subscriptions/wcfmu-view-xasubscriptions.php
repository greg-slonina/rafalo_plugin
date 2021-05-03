<?php
/**
 * WCFM plugin views
 *
 * Plugin WC Subscriptions List Views
 *
 * @author 		WC Lovers
 * @package 	wcfmu/views/wc_subscriptions
 * @version   4.0.7
 */
 
global $WCFM, $WCFMu;

if( !apply_filters( 'wcfm_is_allow_subscriptions', true ) || !apply_filters( 'wcfm_is_allow_subscription_list', true ) ) {
	wcfm_restriction_message_show( "Subscription" );
	return;
}

$wcfmu_subscriptions_menus = apply_filters( 'wcfmu_subscriptions_menus', array( 'all' => __( 'All', 'wc-frontend-manager-ultimate'), 
																																			'active' => __('Active', 'wc-frontend-manager-ultimate' ), 
																																			'on-hold' => __('On Hold', 'wc-frontend-manager-ultimate' ),
																																			'cancelled' => __('Cancelled', 'wc-frontend-manager-ultimate' ),
																																			'expired' => __('Expired', 'wc-frontend-manager-ultimate' ),
																																			) );

$subscription_status = ! empty( $_GET['subscription_status'] ) ? sanitize_text_field( $_GET['subscription_status'] ) : 'all';

?>
<div class="collapse wcfm-collapse" id="wcfm_subscriptions_listing">
  <div class="wcfm-page-headig">
		<span class="wcfmfa fa-paypal"></span>
		<span class="wcfm-page-heading-text"><?php _e( 'Subscriptions List', 'wc-frontend-manager-ultimate' ); ?></span>
		<?php do_action( 'wcfm_page_heading' ); ?>
	</div>
	<div class="wcfm-collapse-content">
	  <div id="wcfm_page_load"></div>
	  
	  <div class="wcfm-container wcfm-top-element-container">
			<ul class="wcfm_subscriptions_menus">
				<?php
				$is_first = true;
				foreach( $wcfmu_subscriptions_menus as $wcfmu_subscriptions_menu_key => $wcfmu_subscriptions_menu) {
					?>
					<li class="wcfm_subscriptions_menu_item">
						<?php
						if($is_first) $is_first = false;
						else echo " | ";
						?>
						<a class="<?php echo ( $wcfmu_subscriptions_menu_key == $subscription_status ) ? 'active' : ''; ?>" href="<?php echo get_wcfm_subscriptions_url( $wcfmu_subscriptions_menu_key ); ?>"><?php echo $wcfmu_subscriptions_menu; ?></a>
					</li>
					<?php
				}
				?>
			</ul>
			
			<?php
			if( $allow_wp_admin_view = apply_filters( 'wcfm_allow_wp_admin_view', true ) ) {
				?>
				<a class="wcfm_screen_manager text_tip" href="#" data-screen="subscription" data-tip="<?php _e( 'Screen Manager', 'wc-frontend-manager-ultimate' ); ?>"><span class="wcfmfa fa-tv"></span></a>
				<a target="_blank" class="wcfm_wp_admin_view text_tip" href="<?php echo admin_url('edit.php?post_type=shop_subscription'); ?>" data-tip="<?php _e( 'WP Admin View', 'wc-frontend-manager' ); ?>"><span class="fab fa-wordpress fa-wordpress-simple"></span></a>
				<?php
			}
			?>
			<div class="wcfm-clearfix"></div>
		</div>
	  <div class="wcfm-clearfix"></div><br />
		
		<?php do_action( 'before_wcfm_subscriptions' ); ?>
		
		<div class="wcfm_subscription_filter_wrap wcfm_filters_wrap">
		  <?php 
		    $WCFM->wcfm_fields->wcfm_generate_form_field( array( "subscription_product" => array( 'type' => 'select', 'attributes' => array( 'style' => 'width: 250px; margin-left: 25px;' ), 'class' => 'wcfm-select wcfm_ele', 'label_class' => 'wcfm_title', 'options' => array() ) ) );
		  ?>
		</div>
	
		<div class="wcfm-container">
			<div id="wwcfm_subscriptions_listing_expander" class="wcfm-content">
				<table id="wcfm-subscriptions" class="display" cellspacing="0" width="100%">
					<thead>
						<tr>
							<th><span class="wcicon-status-processing text_tip" data-tip="<?php _e( 'Status', 'wc-frontend-manager-ultimate' ); ?>"></span></th>
							<th><?php _e( 'Subscription', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Order', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Items', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Total', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Start Date', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Trial End', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Next Payment', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Last Order', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'End Date', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( apply_filters( 'wcfm_subscriptions_additional_info_column_label', __( 'Additional Info', 'wc-frontend-manager-ultimate' ) ) ); ?></th>
							<th><?php _e( 'Actions', 'wc-frontend-manager-ultimate' ); ?></th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th><span class="wcicon-status-processing text_tip" data-tip="<?php _e( 'Status', 'wc-frontend-manager-ultimate' ); ?>"></span></th>
							<th><?php _e( 'Subscription', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Order', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Items', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Total', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Start Date', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Trial End', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Next Payment', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Last Order', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'End Date', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( apply_filters( 'wcfm_subscriptions_additional_info_column_label', __( 'Additional Info', 'wc-frontend-manager-ultimate' ) ) ); ?></th>
							<th><?php _e( 'Actions', 'wc-frontend-manager-ultimate' ); ?></th>
						</tr>
					</tfoot>
				</table>
				<div class="wcfm-clearfix"></div>
			</div>
		</div>
		<?php
		do_action( 'after_wcfm_subscriptions' );
		?>
	</div>
</div>