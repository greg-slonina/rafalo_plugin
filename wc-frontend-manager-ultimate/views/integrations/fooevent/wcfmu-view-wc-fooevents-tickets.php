<?php
/**
 * WCFM plugin view
 *
 * WCFM FooEvents Tickets View
 *
 * @author 		WC Lovers
 * @package 	wcfmu/views/thirdparty
 * @version   6.1.1
 */

global $WCFM, $WCFMu;

if( !apply_filters( 'wcfm_is_allow_wc_fooevents', true ) ) {
	wcfm_restriction_message_show( "Tickets" );
	return;
}

$eventsArray = array();
$args = array(
				'post_type' => 'product',
				'order' => 'ASC',
				'posts_per_page' => -1,
				'meta_query' => array(
								array(
												'key' => 'WooCommerceEventsEvent',
												'value' => 'Event',
												'compare' => '=',
								),
				),
);
$args = apply_filters( 'wcfm_fooevents_args', $args );
$events = get_posts( $args );
foreach ( $events as &$event ) {
	$eventsArray[$event->ID] = $event->post_title;
}

?>

<div class="collapse wcfm-collapse" id="wcfm_event_tickets_listing">
  <div class="wcfm-page-headig">
		<span class="wcfmfa fa-ticket-alt"></span>
		<span class="wcfm-page-heading-text"><?php _e( 'Tickets', 'wc-frontend-manager-ultimate' ); ?></span>
		<?php do_action( 'wcfm_page_heading' ); ?>
	</div>
	<div class="wcfm-collapse-content">
	  <div id="wcfm_page_load"></div>
		
	  <div class="wcfm-container wcfm-top-element-container">
			<h2><?php _e( 'Tickets', 'wc-frontend-manager-ultimate' ); ?></h2>
			
			<?php
			if( $allow_wp_admin_view = apply_filters( 'wcfm_allow_wp_admin_view', true ) ) {
				?>
				<a target="_blank" class="wcfm_wp_admin_view text_tip" href="<?php echo admin_url('edit.php?post_type=event_magic_tickets'); ?>" data-tip="<?php _e( 'WP Admin View', 'wc-frontend-manager-ultimate' ); ?>"><span class="fab fa-wordpress fa-wordpress-simple"></span></a>
				<?php
			}
			?>
			
			<div class="wcfm-clearfix"></div>
		</div>
	  <div class="wcfm-clearfix"></div><br />
	  
	  <div class="wcfm_articles_filter_wrap wcfm_products_filter_wrap  wcfm_filters_wrap">
			<?php	
			// Event Filtering
			if( apply_filters( 'wcfm_is_event_tickets_event_filter', true ) ) {
				echo '<select id="dropdown_event" name="dropdown_event" class="dropdown_event" style="width: 150px;">';
					echo '<option value="" selected="selected">' . __( 'Select an event', 'wc-frontend-manager-ultimate' ) . '</option>';
					if( !empty( $eventsArray ) ) {
						foreach ( $eventsArray as $event_id => $event_title ) {
							echo '<option value="' . $event_id . '">' . $event_title . '</option>';
						}
					}
				echo '</select>';
			}
			?>
		</div>
	  
	  <?php do_action( 'before_wcfm_event_tickets' ); ?>
	  
		<div class="wcfm-container">
			<div id="wwcfm_event_tickets_expander" class="wcfm-content">
				<table id="wcfm-event_tickets" class="display" cellspacing="0" width="100%">
					<thead>
						<tr>
							<th><?php _e( 'Ticket', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Event', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Purchaser', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Attendee', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Status', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Date', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Action', 'wc-frontend-manager' ); ?></th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th><?php _e( 'Ticket', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Event', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Purchaser', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Attendee', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Status', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Date', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Action', 'wc-frontend-manager' ); ?></th>
						</tr>
					</tfoot>
				</table>
				<div class="wcfm-clearfix"></div>
			</div>
		</div>
		<?php
		do_action( 'after_wcfm_event_tickets' );
		?>
	</div>
</div>