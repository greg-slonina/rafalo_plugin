<?php
/**
 * WCFM plugin view
 *
 * Plugin PW Gift Catds for WooCommerce views
 *
 * @author 		WC Lovers
 * @package 	wcfmu/views/integrations/pw-gift-cards
 * @version   6.4.5
 */

global $WCFM, $wp;

if( !$wcfm_is_allow_rental = apply_filters( 'wcfm_is_allow_wc_pw_gift_cards', true ) ) {
	wcfm_restriction_message_show( "Gift Cards" );
	return;
}
?>

<div class="collapse wcfm-collapse" id="wcfm_pw_gift_cards">
  <div class="wcfm-page-headig">
		<span class="wcfmfa fa-gift"></span>
		<span class="wcfm-page-heading-text"><?php _e( 'Gift Cards', 'wc-frontend-manager-ultimate' ); ?></span>
		<?php do_action( 'wcfm_page_heading' ); ?>
	</div>
	<div class="wcfm-collapse-content">
	  <div id="wcfm_page_load"></div>
		
	  <div class="wcfm-container wcfm-top-element-container">
		  <h2><?php _e( 'Gift Cards', 'wc-frontend-manager-ultimate' ); ?></h2>
			<?php
			if( $allow_wp_admin_view = apply_filters( 'wcfm_allow_wp_admin_view', true ) ) {
				?>
				<a target="_blank" class="wcfm_wp_admin_view text_tip" href="<?php echo admin_url('admin.php?page=wc-pw-gift-cards'); ?>" data-tip="<?php _e( 'WP Admin View', 'wc-frontend-manager' ); ?>"><span class="fab fa-wordpress fa-wordpress-simple"></span></a>
				<?php
			}
			?>
			
			<div class="wcfm-clearfix"></div>
		</div>
	  <div class="wcfm-clearfix"></div><br />
	  
	  <?php do_action( 'before_wcfm_pw_gift_cards' ); ?>
	  
	  <div class="wcfm_pw_gift_cards_filter_wrap wcfm_filters_wrap">
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
			<div id="wwcfm_pw_gift_cards_expander" class="wcfm-content">
				<table id="wcfm-pw-gift-cards" class="display" cellspacing="0" width="100%">
					<thead>
						<tr>
						  <th><?php _e( 'Card Number', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Balance', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Expiraton Date', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Actions', 'wc-frontend-manager-ultimate' ); ?></th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th><?php _e( 'Card Number', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Balance', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Expiraton Date', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Actions', 'wc-frontend-manager-ultimate' ); ?></th>
						</tr>
					</tfoot>
				</table>
				<div class="wcfm-clearfix"></div>
			</div>
		</div>
		<?php
		do_action( 'after_wcfm_pw_gift_cards' );
		?>
	</div>
</div>