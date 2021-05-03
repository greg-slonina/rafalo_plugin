<?php
/**
 * WCFM plugin view
 *
 * WCFM ScreenManager View
 *
 * @author 		WC Lovers
 * @package 	wcfmu/view
 * @version   2.3.7
 */
global $WCFM, $WCFMu;

$screen = '';
if( isset($_POST['screen']) ) {
	$screen = $_POST['screen'];
	$wcfm_screen_manager = (array) get_option( 'wcfm_screen_manager' );
	$wcfm_screen_manager_data = array();
	$screen_manager_options = array();
	if( $screen) {
		switch( $screen ) {
		  case 'product':
			  $screen_manager_options = array( 1  => __( 'Image', 'wc-frontend-manager-ultimate' ),
			  																 2  => __( 'Name', 'wc-frontend-manager-ultimate' ),
			  																 3  => __( 'SKU', 'wc-frontend-manager-ultimate' ),
			  																 4  => __( 'Status', 'wc-frontend-manager-ultimate' ),
			  																 5  => __( 'Stock', 'wc-frontend-manager-ultimate' ),
			  																 6  => __( 'Price', 'wc-frontend-manager-ultimate' ),
			  																 7  => __( 'Taxonomies', 'wc-frontend-manager-ultimate' ),
			  																 8  => __( 'Type', 'wc-frontend-manager-ultimate' ),
			  																 9  => __( 'Views', 'wc-frontend-manager-ultimate' ),
			  																 10  => __( 'Date', 'wc-frontend-manager-ultimate' ),
			  																 11 => __( 'Store', 'wc-frontend-manager' ),
			  																 12 => __( apply_filters( 'wcfm_products_additional_info_column_label', __( 'Additional Info', 'wc-frontend-manager' ) ) ),
			  																 13 => __( 'Actions', 'wc-frontend-manager-ultimate' ),
			  																);
		  break;
		  
		  case 'coupon':
			  $screen_manager_options = array( 0 => __( 'Code', 'wc-frontend-manager-ultimate' ),
			  																 1 => __( 'Type', 'wc-frontend-manager-ultimate' ),
			  																 2 => __( 'Amt', 'wc-frontend-manager-ultimate' ),
			  																 3 => __( 'Store', 'wc-frontend-manager-ultimate' ),
			  																 4 => __( 'Usage Limit', 'wc-frontend-manager-ultimate' ),
			  																 5 => __( 'Expiry date', 'wc-frontend-manager-ultimate' ),
			  																 6 => __( 'Actions', 'wc-frontend-manager-ultimate' ),
			  																);
		  break;
		  
		  case 'order':
			  $screen_manager_options = array( 0 => __( 'Status', 'wc-frontend-manager-ultimate' ),
			  																 1 => __( 'Order', 'wc-frontend-manager-ultimate' ),
			  																 2 => __( 'Purchased', 'wc-frontend-manager-ultimate' ),
			  																 3 => __( 'Quantity', 'wc-frontend-manager' ) . ' (' . __( 'Hidden', 'wc-frontend-manager-ultimate' ) . ')',
			  																 4 => __( 'Billing Address', 'wc-frontend-manager' ),
			  																 5 => __( 'Shipping Address', 'wc-frontend-manager' ),
			  																 6 => __( 'Gross Sales', 'wc-frontend-manager-ultimate' ),
			  																 7 => __( 'Gross Sales Amount', 'wc-frontend-manager' ) . ' (' . __( 'Hidden', 'wc-frontend-manager-ultimate' ) . ')' ,
			  																 8 => __( 'Commission', 'wc-frontend-manager-ultimate' ),
			  																 9 => __( 'Commission Amount', 'wc-frontend-manager-ultimate' ) . ' (' . __( 'Hidden', 'wc-frontend-manager-ultimate' ) . ')',
			  																 10 => __( apply_filters( 'wcfm_orders_additional_info_column_label', __( 'Additional Info', 'wc-frontend-manager' ) ) ),
			  																 11 => __( 'Date', 'wc-frontend-manager-ultimate' ),
			  																 12 => __( 'Actions', 'wc-frontend-manager-ultimate' ),
			  																);
		  break;
		  
		  case 'booking':
			  $screen_manager_options = array( 0 => __( 'Status', 'wc-frontend-manager-ultimate' ),
			  																 1 => __( 'Booking', 'wc-frontend-manager-ultimate' ),
			  																 2 => __( 'Product', 'wc-frontend-manager-ultimate' ),
			  																 3 => __( 'Order', 'wc-frontend-manager-ultimate' ),
			  																 4 => __( 'Start Date', 'wc-frontend-manager-ultimate' ),
			  																 5 => __( 'End Date', 'wc-frontend-manager-ultimate' ),
			  																 6 => __( apply_filters( 'wcfm_bookings_additional_info_column_label', __( 'Additional Info', 'wc-frontend-manager' ) ) ),
			  																 7 => __( 'Actions', 'wc-frontend-manager-ultimate' ),
			  																);
			 break;
			 
			 case 'appointment':
			  $screen_manager_options = array( 0 => __( 'Status', 'wc-frontend-manager-ultimate' ),
			  																 1 => __( 'Appointment', 'wc-frontend-manager-ultimate' ),
			  																 2 => __( 'Product', 'wc-frontend-manager-ultimate' ),
			  																 3 => __( 'Order', 'wc-frontend-manager-ultimate' ),
			  																 4 => __( 'Staff', 'wc-frontend-manager-ultimate' ),
			  																 5 => __( 'Start Date', 'wc-frontend-manager-ultimate' ),
			  																 6 => __( 'End Date', 'wc-frontend-manager-ultimate' ),
			  																 7 => __( apply_filters( 'wcfm_appointments_additional_info_column_label', __( 'Additional Info', 'wc-frontend-manager' ) ) ),
			  																 8 => __( 'Actions', 'wc-frontend-manager-ultimate' ),
			  																);
		  break;
		  
		  case 'subscription':
			  $screen_manager_options = array( 0  => __( 'Status', 'wc-frontend-manager-ultimate' ),
			  																 1  => __( 'Subscription', 'wc-frontend-manager-ultimate' ),
			  																 2  => __( 'Order', 'wc-frontend-manager-ultimate' ),
			  																 3  => __( 'Items', 'wc-frontend-manager-ultimate' ),
			  																 4  => __( 'Total', 'wc-frontend-manager-ultimate' ),
			  																 5  => __( 'Start Date', 'wc-frontend-manager-ultimate' ),
			  																 6  => __( 'Trial End', 'wc-frontend-manager-ultimate' ),
			  																 7  => __( 'Next Payment', 'wc-frontend-manager-ultimate' ),
			  																 8  => __( 'Last Order', 'wc-frontend-manager-ultimate' ),
			  																 9  => __( 'End Date', 'wc-frontend-manager-ultimate' ),
			  																 10 => __( apply_filters( 'wcfm_subscriptions_additional_info_column_label', __( 'Additional Info', 'wc-frontend-manager' ) ) ),
			  																 11 => __( 'Actions', 'wc-frontend-manager-ultimate' ),
			  																);
		  break;
		  
		  case 'listing':
			  $screen_manager_options = array( 0 => __( 'Listing', 'wc-frontend-manager' ),
			  																 1 => __( 'Store', 'wc-frontend-manager-ultimate' ),
			  																 2 => __( 'Status', 'wc-frontend-manager-ultimate' ),
			  																 3 => __( 'Products', 'wc-frontend-manager' ),
			  																 4 => __( 'Applications', 'wc-frontend-manager' ),
			  																 5 => __( 'Filled?', 'wp-job-manager' ),
			  																 6 => __( 'Views', 'wp-job-manager' ),
			  																 7 => __( 'Date Posted', 'wp-job-manager' ),
			  																 8 => __( 'Listing Expires', 'wp-job-manager' ),
			  																 9 => __( apply_filters( 'wcfm_listings_additional_info_column_label', __( 'Additional Info', 'wc-frontend-manager' ) ) ),
			  																 10 => __( 'Actions', 'wc-frontend-manager-ultimate' ),
			  																);
		}
		$screen_manager_options = apply_filters( 'wcfm_screen_manager_columns', $screen_manager_options, $screen  );
		if( isset( $wcfm_screen_manager[$screen] ) ) $wcfm_screen_manager_data = $wcfm_screen_manager[$screen];
		if( !isset( $wcfm_screen_manager_data['admin'] ) ) {
			$wcfm_screen_manager_data['admin'] = $wcfm_screen_manager_data;
			$wcfm_screen_manager_data['vendor'] = $wcfm_screen_manager_data;
		}
	}
	
	
	
	if( !empty( $screen_manager_options ) ) {
	  ?>
	  <form id="wcfm_screen_manager_form" class="wcfm_popup_wrapper">
	    <div style="margin-bottom: 15px;"><h2 style="float: none;"><?php _e( 'Choose columns want to hide.', 'wc-frontend-manager-ultimate' ); ?></h2></div>
	    <table>
	      <thead>
	        <tr>
	          <th><?php _e( 'Columns', 'wc-frontend-manager-ultimate' ); ?></th>
	          <th><?php _e( 'Admin', 'wc-frontend-manager-ultimate' ); ?></th>
	          <?php if( wcfm_is_marketplace() ) { ?>
	            <th><?php _e( 'Vendor', 'wc-frontend-manager-ultimate' ); ?></th>
	          <?php } ?>
	        </tr>
	      </thead>
	      <tbody>
	        <?php foreach( $screen_manager_options as $screen_manager_option_index => $screen_manager_option ) { ?>
						<tr>
							<td class="wcfm_screen_manager_form_label wcfm_popup_label"><?php echo $screen_manager_option; ?></td>
							<td><input type="checkbox" <?php if( in_array( $screen_manager_option_index, $wcfm_screen_manager_data['admin'] ) ) echo 'checked="checked"'; ?> name="wcfm_screen_manager[<?php echo $screen; ?>][admin][<?php echo $screen_manager_option_index; ?>]" value="<?php echo $screen_manager_option_index; ?>" /></td>
							<?php if( wcfm_is_marketplace() ) { ?>
								<td><input type="checkbox" <?php if( in_array( $screen_manager_option_index, $wcfm_screen_manager_data['vendor'] ) ) echo 'checked="checked"'; ?> name="wcfm_screen_manager[<?php echo $screen; ?>][vendor][<?php echo $screen_manager_option_index; ?>]" value="<?php echo $screen_manager_option_index; ?>" /></td>
						  <?php } ?>
						</tr>
					<?php } ?>
	      </tbody>
	    </table>
	    <input type="hidden" name="wcfm_screen" value="<?php echo $screen; ?>" />
	    <div class="wcfm-message" tabindex="-1"></div>
	    <input type="button" class="wcfm_screen_manager_button wcfm_popup_button" id="wcfm_screen_manager_button" value="Update" />
	    <div class="wcfm_clearfix"></div>
	  </form>
	  <?php
	}
}