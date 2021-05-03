<?php
/**
 * WCFM plugin view
 *
 * Plugin License Manager for WooCommerce views
 *
 * @author 		WC Lovers
 * @package 	wcfmu/views/integrations/license-manager
 * @version   6.4.0
 */

global $WCFM;

if( !$wcfm_is_allow_rental = apply_filters( 'wcfm_is_allow_wc_license_manager', true ) ) {
	wcfm_restriction_message_show( "License Manager" );
	return;
}

?>

<div class="collapse wcfm-collapse" id="wcfm_license_keys_quote">
  <div class="wcfm-page-headig">
		<span class="wcfmfa fa-key"></span>
		<span class="wcfm-page-heading-text"><?php _e( 'License Keys', 'wc-frontend-manager-ultimate' ); ?></span>
		<?php do_action( 'wcfm_page_heading' ); ?>
	</div>
	<div class="wcfm-collapse-content">
	  <div id="wcfm_page_load"></div>
		
	  <div class="wcfm-container wcfm-top-element-container">
			<?php
			$wcfm_license_keys_menus = apply_filters( 'wcfm_lmfwc_licenses_statuses',
																																								array(
																																									'all'  => __( 'All', 'wc-frontend-manager'), 
																																									'1'    => __( 'Sold', 'wc-frontend-manager-ultimate'), 
																																									'2'    => __( 'Delivered', 'wc-frontend-manager-ultimate'), 
																																									'3'    => __( 'Active', 'wc-frontend-manager-ultimate'), 
																																									'4'    => __( 'Inactive', 'wc-frontend-manager-ultimate'), 
																																								)
																																							);
		
			$license_status = ! empty( $_GET['license_status'] ) ? sanitize_text_field( $_GET['license_status'] ) : 'all';
			
			?>
			<ul class="wcfm_license_keys_menus">
				<?php
				$is_first = true;
				foreach( $wcfm_license_keys_menus as $wcfm_license_keys_menus_key => $wcfm_license_keys_menu ) {
					?>
					<li class="wcfm_license_keys_menu_item">
						<?php
						if($is_first) $is_first = false;
						else echo " | ";
						?>
						<a class="<?php echo ( $wcfm_license_keys_menus_key == $license_status ) ? 'active' : ''; ?>" href="<?php echo get_wcfm_license_keys_url( $wcfm_license_keys_menus_key ); ?>"><?php echo $wcfm_license_keys_menu; ?></a>
					</li>
					<?php
				}
				?>
			</ul>
		
			<?php
			if( $allow_wp_admin_view = apply_filters( 'wcfm_allow_wp_admin_view', true ) ) {
				?>
				<a target="_blank" class="wcfm_wp_admin_view text_tip" href="<?php echo admin_url('admin.php?page=lmfwc_licenses'); ?>" data-tip="<?php _e( 'WP Admin View', 'wc-frontend-manager' ); ?>"><span class="fab fa-wordpress fa-wordpress-simple"></span></a>
				<?php
			}
			?>
			
			<div class="wcfm-clearfix"></div>
		</div>
	  <div class="wcfm-clearfix"></div><br />
	  
	  <?php do_action( 'before_wcfm_license_keys' ); ?>
	  
	  <div class="wcfm_license_keys_filter_wrap wcfm_filters_wrap">
			<?php
			if( apply_filters( 'wcfm_is_license_manager_vendor_filter', true ) ) {
				$is_marketplace = wcfm_is_marketplace();
				if( $is_marketplace ) {
					if( !wcfm_is_vendor() ) {
						$WCFM->wcfm_fields->wcfm_generate_form_field( array(
																											"dropdown_vendor" => array( 'type' => 'select', 'options' => array(), 'attributes' => array( 'style' => 'width: 150px;' ) )
																											 ) );
					}
				}
			}
			?>
		</div>
			
		<div class="wcfm-container">
			<div id="wwcfm_license_keys_expander" class="wcfm-content">
				<table id="wcfm-license-keys" class="display" cellspacing="0" width="100%">
					<thead>
						<tr>
						  <th><?php _e( 'Status', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Key', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Order', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Product', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Activation', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Valid For', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Expires At', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Created', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Updated', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Actions', 'wc-frontend-manager-ultimate' ); ?></th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th><?php _e( 'Status', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Key', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Order', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Product', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Activation', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Valid For', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Expires At', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Created', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Updated', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Actions', 'wc-frontend-manager-ultimate' ); ?></th>
						</tr>
					</tfoot>
				</table>
				<div class="wcfm-clearfix"></div>
			</div>
		</div>
		<?php
		do_action( 'after_wcfm_license_keys' );
		?>
	</div>
</div>