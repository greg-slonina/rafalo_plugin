<?php
/**
 * WCFM plugin view
 *
 * Plugin License Manager for WooCommerce Views
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

<div class="collapse wcfm-collapse" id="wcfm_license_generators">
  <div class="wcfm-page-headig">
		<span class="wcfmfa fa-key"></span>
		<span class="wcfm-page-heading-text"><?php _e( 'License Generators', 'wc-frontend-manager-ultimate' ); ?></span>
		<?php do_action( 'wcfm_page_heading' ); ?>
	</div>
	<div class="wcfm-collapse-content">
	  <div id="wcfm_page_load"></div>
		
	  <div class="wcfm-container wcfm-top-element-container">
	    <h2><?php _e('License Generators', 'wc-frontend-manager' ); ?></h2>
			<?php
			if( $allow_wp_admin_view = apply_filters( 'wcfm_allow_wp_admin_view', true ) ) {
				?>
				<a target="_blank" class="wcfm_wp_admin_view text_tip" href="<?php echo admin_url('admin.php?page=lmfwc_generators'); ?>" data-tip="<?php _e( 'WP Admin View', 'wc-frontend-manager' ); ?>"><span class="fab fa-wordpress fa-wordpress-simple"></span></a>
				<?php
			}
			if( $has_new = apply_filters( 'wcfm_is_allow_add_license_generator', true ) ) {
				echo '<a class="add_new_wcfm_ele_dashboard wcfm_license_generator_manage text_tip" href="#" data-generatorid="" data-tip="' . __('Add New Generator', 'wc-frontend-manager-ultimate') . '"><span class="wcfmfa fa-key"></span><span class="text">' . __('Add New Generator', 'wc-frontend-manager-ultimate') . '</span></a>';
			}
			?>
			
			<div class="wcfm-clearfix"></div>
		</div>
	  <div class="wcfm-clearfix"></div><br />
	  
	  <?php do_action( 'before_wcfm_license_generators' ); ?>
	  
	  <div class="wcfm_license_generators_filter_wrap wcfm_filters_wrap">
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
			<div id="wwcfm_license_generators_expander" class="wcfm-content">
				<table id="wcfm-license-generators" class="display" cellspacing="0" width="100%">
					<thead>
						<tr>
							<th><?php _e( 'Name', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php echo apply_filters( 'wcfm_sold_by_label', '', __( 'Store', 'wc-frontend-manager' ) ); ?></th>
							<th><?php _e( 'Maximum activation count', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Expires in', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Actions', 'wc-frontend-manager-ultimate' ); ?></th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th><?php _e( 'Name', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php echo apply_filters( 'wcfm_sold_by_label', '', __( 'Store', 'wc-frontend-manager' ) ); ?></th>
							<th><?php _e( 'Maximum activation count', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Expires in', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Actions', 'wc-frontend-manager-ultimate' ); ?></th>
						</tr>
					</tfoot>
				</table>
				<div class="wcfm-clearfix"></div>
			</div>
		</div>
		<?php
		do_action( 'after_wcfm_license_generators' );
		?>
	</div>
</div>