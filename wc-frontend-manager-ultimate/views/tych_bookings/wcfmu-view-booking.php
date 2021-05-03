<?php
/**
 * WCFM plugin views
 *
 * Plugin Tych Bookings List Views
 *
 * @author 		WC Lovers
 * @package 	wcfmu/views/tych_bookings
 * @version   5.4.7
 */
 
global $WCFM, $WCFMu;

if( !apply_filters( 'wcfm_is_allow_manage_booking', true ) || !apply_filters( 'wcfm_is_allow_booking_list', true ) ) {
	wcfm_restriction_message_show( "Bookings" );
	return;
}

$wcfmu_bookings_menus = apply_filters( 'wcfmu_bookings_menus', array( 'all' => __( 'All', 'wc-frontend-manager'), 
																																			'complete' => __('Complete', 'wc-frontend-manager' ), 
																																			'paid' => __('Paid & Confirmed', 'wc-frontend-manager' ),
																																			'confirmed' => __('Confirmed', 'wc-frontend-manager' ),
																																			'pending-confirmation' => __('Pending Confirmation', 'wc-frontend-manager' ),
																																			'cancelled' => __('Cancelled', 'wc-frontend-manager' ),
																																			//'unpaid' => __('Un-paid', 'wc-frontend-manager' ), 
																																			) );

$booking_status = ! empty( $_GET['booking_status'] ) ? sanitize_text_field( $_GET['booking_status'] ) : 'all';

//include_once( WC_BOOKINGS_ABSPATH . 'includes/admin/class-wc-bookings-admin.php' );

function wcfm_bkap_check_booking_present( $product_id ) {
	$bookings_present = false; // assume no bookings are present for this product
	global $wpdb;
	
	$query = "SELECT post_id FROM `" . $wpdb->prefix . "postmeta`
				WHERE meta_key = %s
				AND meta_value = %d
				ORDER BY post_id DESC LIMIT 1";
	
	$results_query = $wpdb->get_results( $wpdb->prepare( $query, '_bkap_product_id', $product_id ) );
	
	if ( isset( $results_query ) && count( $results_query ) > 0 ) {
		$bookings_present = true;
	}
	
	return $bookings_present;
}

$product_filters = array();

$product_args = array( 
            'post_type'         => array( 'product' ), 
            'posts_per_page'    => -1,
            'post_status'       => array( 'publish' ),
            'meta_query'        => array(
                                        array(
                                          'key'     => '_bkap_enable_booking',
                                          'value'   => 'on',
                                          'compare' => '=',
                                        ),
                                    )
        );
$product_args   = apply_filters( 'wcfm_products_args', $product_args );
$product_list   = get_posts( $product_args );    
if( !empty( $product_list ) ) {
	foreach ( $product_list as $k => $value ) {
		$theid          = $value->ID;            
		$present = wcfm_bkap_check_booking_present( $theid );
		if ( $present ) {
			$product_filters[ $theid ] = get_the_title( $value->ID );
		}
	} 
}

$views = array( 'today_onwards'  => 'Bookings From Today Onwards',
							'today_checkin'  => 'Today Check-ins',
							'today_checkout' => 'Today Checkouts',
							'gcal'           => 'Imported Bookings',
			 );

?>
<div class="collapse wcfm-collapse" id="wcfm_bookings_listing">
  <div class="wcfm-page-headig">
		<span class="wcfmfa fa-calendar"></span>
		<span class="wcfm-page-heading-text"><?php _e( 'Bookings List', 'wc-frontend-manager' ); ?></span>
		<?php do_action( 'wcfm_page_heading' ); ?>
	</div>
	<div class="wcfm-collapse-content">
	  <div id="wcfm_page_load"></div>
	  
	  <div class="wcfm-container wcfm-top-element-container">
			<ul class="wcfm_bookings_menus">
				<?php
				$is_first = true;
				foreach( $wcfmu_bookings_menus as $wcfmu_bookings_menu_key => $wcfmu_bookings_menu) {
					?>
					<li class="wcfm_bookings_menu_item">
						<?php
						if($is_first) $is_first = false;
						else echo " | ";
						?>
						<a class="<?php echo ( $wcfmu_bookings_menu_key == $booking_status ) ? 'active' : ''; ?>" href="<?php echo get_wcfm_tych_booking_url( $wcfmu_bookings_menu_key ); ?>"><?php echo $wcfmu_bookings_menu; ?></a>
					</li>
					<?php
				}
				?>
			</ul>
			
			<?php
			if( $allow_wp_admin_view = apply_filters( 'wcfm_allow_wp_admin_view', true ) ) {
				?>
				<a target="_blank" class="wcfm_wp_admin_view text_tip" href="<?php echo admin_url('edit.php?post_type=bkap_booking'); ?>" data-tip="<?php _e( 'WP Admin View', 'wc-frontend-manager' ); ?>"><span class="fab fa-wordpress fa-wordpress-simple"></span></a>
				<?php
			}
			
			if( $wcfm_is_allow_manual_booking = apply_filters( 'wcfm_is_allow_manual_booking', true ) ) {
				if( WCFM_Dependencies::wcfmu_plugin_active_check() ) {
					echo '<a class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_create_tych_booking_url().'" data-tip="' . __( 'Create Booking', 'wc-frontend-manager' ) . '"><span class="wcfmfa fa-calendar-plus"></span></a>';
				}
			}
			
			if( $wcfm_is_allow_manage_resource = apply_filters( 'wcfm_is_allow_manage_resource', true ) ) {
				if( WCFM_Dependencies::wcfmu_plugin_active_check() ) {
					echo '<a class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_tych_booking_resources_url().'" data-tip="' . __( 'Manage Resources', 'wc-frontend-manager' ) . '"><span class="wcfmfa fa-briefcase"></span></a>';
				}
			}
			
			if( apply_filters( 'wcfm_add_new_product_sub_menu', true ) && apply_filters( 'wcfm_is_allow_create_bookable', true ) ) {
				echo '<a class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_edit_product_url().'" data-tip="' . __('Create Bookable', 'wc-frontend-manager') . '"><span class="wcfmfa fa-cube"></span></a>';
			}
			
			if( $wcfm_is_allow_booking_calendar = apply_filters( 'wcfm_is_allow_booking_calendar', true ) ) {
				if( WCFM_Dependencies::wcfmu_plugin_active_check() ) {
					echo '<a class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_tych_booking_calendar_url().'" data-tip="'. __('Calendar View', 'wc-frontend-manager') .'"><span class="wcfmfa fa-calendar-alt"></span></a>';
				}
			}
			?>
			<div class="wcfm-clearfix"></div>
		</div>
	  <div class="wcfm-clearfix"></div><br />
		
		<div class="wcfm_bookings_filter_wrap wcfm_filters_wrap">
		  <select id="dropdown_booking_filter" name="filter_bookings" style="width:200px">
				<option value=""><?php _e( 'Filter Bookings', 'woocommerce-bookings' ); ?></option>
				<?php if ( $product_filters ) : ?>
					<optgroup label="<?php _e( 'All Bookable Products', 'woocommerce-booking' ); ?>">
						<?php foreach ( $product_filters as $filter_id => $filter ) : ?>
							<option value="<?php echo absint( $filter_id ); ?>"><?php echo esc_html( $filter ); ?></option>
						<?php endforeach; ?>
					</optgroup>
				<?php endif; ?>
			</select>
			
			<select id="dropdown_booking_view_filter" name="filter_views">
				<?php
			  $output .= '<option value="">' . __( 'Select Booking Type', 'woocommerce-booking' ) . '</option>';
				
				foreach( $views as $v_key => $v_value ) {
		
					$output .= '<option value="' . $v_key . '" ';
				
					if ( isset( $_REQUEST['filter_views'] ) ) {
						$output .= selected( $v_key, $_REQUEST['filter_views'], false );
					}
				
					$output .= '>' . esc_html( $v_value ) . '</option>';
				}
				echo $output;
				?>
			</select>
			
			<?php $WCFM->library->wcfm_date_range_picker_field(); ?>
		</div>
		
		<?php do_action( 'before_wcfm_bookings' ); ?>
	
		<div class="wcfm-container">
			<div id="wwcfm_bookings_listing_expander" class="wcfm-content">
				<table id="wcfm-bookings" class="display" cellspacing="0" width="100%">
					<thead>
						<tr>
							<th><span class="wcicon-status-processing text_tip" data-tip="<?php _e( 'Status', 'wc-frontend-manager' ); ?>"></span></th>
							<th><?php _e( 'Booking', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Product', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Order', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Start Date', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'End Date', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Quantity', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Amount', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Order date', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( apply_filters( 'wcfm_bookings_additional_info_column_label', __( 'Additional Info', 'wc-frontend-manager' ) ) ); ?></th>
							<th><?php _e( 'Actions', 'wc-frontend-manager' ); ?></th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th><span class="wcicon-status-processing text_tip" data-tip="<?php _e( 'Status', 'wc-frontend-manager' ); ?>"></span></th>
							<th><?php _e( 'Booking', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Product', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Order', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Start Date', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'End Date', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Quantity', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Amount', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Order date', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( apply_filters( 'wcfm_bookings_additional_info_column_label', __( 'Additional Info', 'wc-frontend-manager' ) ) ); ?></th>
							<th><?php _e( 'Actions', 'wc-frontend-manager' ); ?></th>
						</tr>
					</tfoot>
				</table>
				<div class="wcfm-clearfix"></div>
			</div>
		</div>
		<?php
		do_action( 'after_wcfm_bookings' );
		?>
	</div>
</div>