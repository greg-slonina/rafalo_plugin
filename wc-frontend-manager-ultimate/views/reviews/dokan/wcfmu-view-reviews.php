<?php
/**
 * WCFM plugin view
 *
 * WCFM Dokan Reviews List View
 *
 * @author 		WC Lovers
 * @package 	wcfm/withdrawal/dokan/view
 * @version   3.3.0
 */
 
global $WCFM;

$wcfm_is_allow_reviews = apply_filters( 'wcfm_is_allow_reviews', true );
if( !$wcfm_is_allow_reviews ) {
	wcfm_restriction_message_show( "Reviews" );
	return;
}

$vendor_id   = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );
$wcfmu_reviews_menus = apply_filters( 'wcfmu_reviews_menus', array( 'approved' => __( 'Approved', 'wc-frontend-manager-ultimate'), 
																																		'pending'  => __( 'Pending', 'wc-frontend-manager-ultimate'),
																																		'spam'     => __( 'Spam', 'wc-frontend-manager-ultimate'),
																																		'trash'    => __( 'Trash', 'wc-frontend-manager-ultimate'),
																																	) );
	
$reviews_status = ! empty( $_GET['reviews_status'] ) ? sanitize_text_field( $_GET['reviews_status'] ) : 'approved';
$review_counts = dokan_count_comments( 'product', $vendor_id );
$review_counts->pending = $review_counts->moderated;
?>
<div class="collapse wcfm-collapse" id="wcfm_reviews_listing">
  <div class="wcfm-page-headig">
		<span class="wcfmfa fa-comment-alt"></span>
		<span class="wcfm-page-heading-text"><?php _e( 'Reviews', 'wc-frontend-manager' ); ?></span>
		<?php do_action( 'wcfm_page_heading' ); ?>
	</div>
	<div class="wcfm-collapse-content">
	  <div id="wcfm_page_load"></div>
	  
	  <div class="wcfm-container wcfm-top-element-container">
			<ul class="wcfm_reviews_menus">
				<?php
				$is_first = true;
				foreach( $wcfmu_reviews_menus as $wcfmu_reviews_menu_key => $wcfmu_reviews_menu) {
					?>
					<li class="wcfm_reviews_menu_item">
						<?php
						if($is_first) $is_first = false;
						else echo " | ";
						?>
						<a class="<?php echo ( $wcfmu_reviews_menu_key == $reviews_status ) ? 'active' : ''; ?>" href="<?php echo wcfm_reviews_url( $wcfmu_reviews_menu_key ); ?>"><?php echo $wcfmu_reviews_menu . ' ('.$review_counts->$wcfmu_reviews_menu_key.')'; ?></a>
					</li>
					<?php
				}
				?>
			</ul>
			<div class="wcfm-clearfix"></div>
		</div>
	  <div class="wcfm-clearfix"></div><br />
	  
	  <?php do_action( 'before_wcfm_reviews' ); ?>
			
		<div class="wcfm-container">
			<div id="wcfm_reviews_listing_expander" class="wcfm-content">
				<table id="wcfm-reviews" class="display" cellspacing="0" width="100%">
					<thead>
						<tr>
							<th><?php _e( 'Author', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Comment', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Rating', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Actions', 'wc-frontend-manager' ); ?></th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th><?php _e( 'Author', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Comment', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Rating', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Actions', 'wc-frontend-manager' ); ?></th>
						</tr>
					</tfoot>
				</table>
				<div class="wcfm-clearfix"></div>
			</div>
		</div>
		<?php
		do_action( 'after_wcfm_reviews' );
		?>
	</div>
</div>