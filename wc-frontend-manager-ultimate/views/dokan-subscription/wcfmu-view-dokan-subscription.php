<?php
/**
 * WCFM plugin views
 *
 * Plugin Dokan Subscription Packs Views
 *
 * @author 		WC Lovers
 * @package 	wcfmu/views/doakn-subscription
 * @version   4.1.1
 */
 
global $WCFM, $WCFMu;

?>
<div class="collapse wcfm-collapse" id="wcfm_subscription_packs_listing">
  <div class="wcfm-page-headig">
		<span class="wcfmfa fa-user-plus"></span>
		<span class="wcfm-page-heading-text"><?php _e( 'Membership', 'wc-frontend-manager-ultimate' ); ?></span>
		<?php do_action( 'wcfm_page_heading' ); ?>
	</div>
	<div class="wcfm-collapse-content">
	  <div id="wcfm_page_load"></div>
	  
	  <div class="wcfm-container wcfm-top-element-container">
	    <h2><?php _e( 'Subscription Packs', 'wc-frontend-manager-ultimate' ); ?></h2>
			<div class="wcfm-clearfix"></div>
		</div>
	  <div class="wcfm-clearfix"></div><br />
		
		<?php do_action( 'before_wcfm_subscription_packs' ); ?>
		
		<div class="wcfm-container">
			<div id="wwcfm_subscription_packs_listing_expander" class="wcfm-content">
				<?php
	        if ( dokan_is_seller_enabled( apply_filters( 'wcfm_current_vendor_id', get_current_user_id() ) ) ) {
	          echo do_shortcode( '[dps_product_pack]' );
	          ?>
	          <style>
	          .dokan-subscription-content .pack_content_wrapper .product_pack_item .pack_content h2 {width:100%;}
	          </style>
	          <?php
	        } else {
	          dokan_seller_not_enabled_notice();
	        }
	        ?>
				<div class="wcfm-clearfix"></div>
			</div>
		</div>
		<?php
		do_action( 'after_wcfm_subscription_packs' );
		?>
	</div>
</div>