<?php
/**
 * WCFMu plugin view
 *
 * WCFM Followers view
 *
 * @author 		WC Lovers
 * @package 	wcfm/views/followers
 * @version   4.0.6
 */
 
global $WCFM;


if( !apply_filters( 'wcfm_is_pref_vendor_followers', true ) || !apply_filters( 'wcfm_is_allow_followers', true ) ) {
	wcfm_restriction_message_show( "Followers" );
	return;
}

?>

<div class="collapse wcfm-collapse" id="wcfm_followers_listing">
	
	<div class="wcfm-page-headig">
		<span class="wcfmfa fa-child"></span>
		<span class="wcfm-page-heading-text"><?php _e( 'Followers', 'wc-frontend-manager-ultimate' ); ?></span>
		<?php do_action( 'wcfm_page_heading' ); ?>
	</div>
	<div class="wcfm-collapse-content">
		<div id="wcfm_page_load"></div>
		
		<div class="wcfm-container wcfm-top-element-container">
			<h2><?php _e( 'Followers List', 'wc-frontend-manager-ultimate' ); ?></h2>
			<div class="wcfm-clearfix"></div>
		</div>
	  <div class="wcfm-clearfix"></div><br />
	  
	  <div class="wcfm_articles_filter_wrap wcfm_products_filter_wrap  wcfm_filters_wrap">
			<?php	
			if( apply_filters( 'wcfm_is_followers_vendor_filter', true ) ) {
				if( !wcfm_is_vendor() ) {
					$vendor_arr = array(); //$WCFM->wcfm_vendor_support->wcfm_get_vendor_list();
					$WCFM->wcfm_fields->wcfm_generate_form_field( array(
																										"dropdown_vendor" => array( 'type' => 'select', 'options' => $vendor_arr, 'attributes' => array( 'style' => 'width: 150px;' ) )
																										 ) );
				}
			}
			?>
		</div>
	  
	  <?php do_action( 'before_wcfm_followers' ); ?>
	  
		<div class="wcfm-container">
			<div id="wcfm_followers_listing_expander" class="wcfm-content">
				<table id="wcfm-followers" class="display" cellspacing="0" width="100%">
					<thead>
						<tr>
						  <th><?php _e( 'Name', 'wc-frontend-manager-ultimate' ); ?></th>
						  <th><?php _e( 'Email', 'wc-frontend-manager-ultimate' ); ?></th>
						  <th><?php echo apply_filters( 'wcfm_sold_by_label', '', __( 'Store', 'wc-frontend-manager' ) ); ?></th>
							<th><?php _e( 'Actions', 'wc-frontend-manager-ultimate' ); ?></th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th><?php _e( 'Name', 'wc-frontend-manager-ultimate' ); ?></th>
						  <th><?php _e( 'Email', 'wc-frontend-manager-ultimate' ); ?></th>
						  <th><?php echo apply_filters( 'wcfm_sold_by_label', '', __( 'Store', 'wc-frontend-manager' ) ); ?></th>
							<th><?php _e( 'Actions', 'wc-frontend-manager-ultimate' ); ?></th>
						</tr>
					</tfoot>
				</table>
				<div class="wcfm-clearfix"></div>
			</div>
		</div>
			
		<?php do_action( 'after_wcfm_followers' ); ?>
	</div>
</div>