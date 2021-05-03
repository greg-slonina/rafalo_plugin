<?php
/**
 * WCFM plugin view
 *
 * wcfm Support Manage View
 *
 * @author 		WC Lovers
 * @package 	wcfmu/views/support
 * @version   4.0.3
 */
 
global $wp, $WCFM, $WCFMu, $wpdb, $blog_id;

if( !apply_filters( 'wcfm_is_pref_support', true ) || !apply_filters( 'wcfm_is_allow_support', true ) || !apply_filters( 'wcfm_is_allow_manage_support', true ) ) {
	wcfm_restriction_message_show( "Manage Support" );
	return;
}

$support_id = 0;
$support_ticket_title = '';
$support_ticket_content = '';
$allow_reply = 'no';
$close_new_reply = 'no';

if( isset( $wp->query_vars['wcfm-support-manage'] ) && !empty( $wp->query_vars['wcfm-support-manage'] ) ) {
	$support_id = $wp->query_vars['wcfm-support-manage'];
	$support_post = $wpdb->get_row( "SELECT * from {$wpdb->prefix}wcfm_support WHERE `ID` = " . $support_id );
	// Fetching Support Data
	if($support_post && !empty($support_post)) {
		$support_ticket_content = $support_post->query;
		$support_order_id = $support_post->order_id;
		$support_item_id = $support_post->item_id;
		$support_product_id = $support_post->product_id;
		$support_vendor_id = $support_post->vendor_id;
		$support_customer_id = $support_post->customer_id;
		$support_customer_name = $support_post->customer_name;
		$support_customer_email = $support_post->customer_email;
	} else {
		wcfm_restriction_message_show( "Invalid Ticket" );
		return;
	}
} else {
	wcfm_restriction_message_show( "Invalid Ticket" );
	return;
}
$support_categories     = $WCFMu->wcfmu_support->wcfm_support_categories();
$support_priority_types = $WCFMu->wcfmu_support->wcfm_support_priority_types();
$support_status_types   = $WCFMu->wcfmu_support->wcfm_support_status_types();

if( wcfm_is_vendor() ) {
	$is_ticket_for_vendor = $WCFM->wcfm_vendor_support->wcfm_is_component_for_vendor( $support_id, 'support' );
	if( !$is_ticket_for_vendor ) {
		if( apply_filters( 'wcfm_is_show_support_ticket_restrict_message', true, $support_id ) ) {
			wcfm_restriction_message_show( "Restricted Ticket" );
		} else {
			echo apply_filters( 'wcfm_show_custom_support_ticket_restrict_message', '', $support_id );
		}
		return;
	}
}

$wcfm_options = $WCFM->wcfm_options;
$wcfm_support_allow_attachment = isset( $wcfm_options['wcfm_support_allow_attachment'] ) ? $wcfm_options['wcfm_support_allow_attachment'] : 'yes';

do_action( 'before_wcfm_support_manage' );

?>

<div class="collapse wcfm-collapse">
  <div class="wcfm-page-headig">
		<span class="wcfmfa fa-life-ring"></span>
		<span class="wcfm-page-heading-text"><?php _e( 'Support Ticket', 'wc-frontend-manager-ultimate' ); ?></span>
		<?php do_action( 'wcfm_page_heading' ); ?>
	</div>
	<div class="wcfm-collapse-content">
	  <div id="wcfm_page_load"></div>
		
		<div class="wcfm-container wcfm-top-element-container">
			<h2><?php echo __( 'Ticket', 'wc-frontend-manager-ultimate' ) . ' #' . sprintf( '%06u', $support_id ); ?></h2>
			<span class="support-priority support-priority-<?php echo $support_post->priority;?>"><?php echo $support_priority_types[$support_post->priority]; ?></span>
			
			<?php
			echo '<a id="add_new_support_dashboard" class="add_new_wcfm_ele_dashboard text_tip" href="'.wcfm_support_url().'" data-tip="' . __('Support Tickets', 'wc-frontend-manager-ultimate') . '"><span class="wcfmfa fa-life-ring"></span><span class="text">' . __( 'Tickets', 'wc-frontend-manager-ultimate') . '</span></a>';
			?>
			<div class="wcfm-clearfix"></div>
		</div>
	  <div class="wcfm-clearfix"></div><br />
	  
	  <?php do_action( 'begin_wcfm_support_manage_form' ); ?>
	  
		<!-- collapsible -->
		<div class="wcfm-container">
			<div id="support_manage_general_expander" class="wcfm-content">
			  <div class="support_ticket_content">
			    <?php echo $support_ticket_content; ?>
			  <div class="wcfm_clearfix"></div>
				</div>
			
			  <div class="support_ticket_content_details">
			    <div class="support_ticket_content_for">
			      <?php
			      echo "<div style=\"width:auto;min-width:350px;\"><h2>" . __( 'Support Ticket For', 'wc-frontend-manager-ultimate' ) . "</h2><div class=\"wcfm_clearfix\"></div>";
			      
			      if( $support_product_id ) {
							$post_obj = get_post( $support_product_id );
							if( $post_obj->post_type == 'product' ) {
								$the_product = wc_get_product( $support_product_id );
								$thumbnail = $the_product->get_image( 'thumbnail' );
								$datatip_msg = __( 'Ticket for Product', 'wc-frontend-manager-ultimate' );
							} else {
								$thumbnail = '';
								$datatip_msg = sprintf( __( 'Ticket for %s', 'wc-frontend-manager-ultimate' ), $post_obj->post_type );
							}
							echo '<div class="wcfm_product_for_support">' . $thumbnail . '&nbsp;<a class="img_tip" data-tip="'. $datatip_msg .'" href="'. get_permalink($support_product_id) .'" target="_blank">'.get_the_title($support_product_id).'</a></div>';
						}
						
						if( $support_vendor_id ) {
							if( apply_filters( 'wcfmmp_is_allow_sold_by_linked', true ) ) {
								$store_name = $WCFM->wcfm_vendor_support->wcfm_get_vendor_store_by_vendor( absint($support_vendor_id) );
							} else {
								$store_name = $WCFM->wcfm_vendor_support->wcfm_get_vendor_store_name_by_vendor( absint($support_vendor_id) );
							}
							$store_logo = $WCFM->wcfm_vendor_support->wcfm_get_vendor_logo_by_vendor( absint($support_vendor_id) );
							echo '<div class="wcfm_store_for_support"><img class="wcfmmp_sold_by_logo img_tip" src="' . $store_logo . '" data-tip="'. __( 'Support Ticket for', 'wc-frontend-manager-ultimate' ) . ' ' . apply_filters( 'wcfm_sold_by_label', $support_vendor_id, __( 'Store', 'wc-frontend-manager' ) ) .'" />&nbsp;' . $store_name . '</div>';
						}
						
						echo "</div><div class=\"wcfm_clearfix\"></div><br />";
						?>
						
						<div class="support_ticket_content_order">
							<span class="support_ticket_content_order_title"><?php _e( 'Order', 'wc-frontend-manager-ultimate' ); ?></span>:
							<?php
							if( apply_filters( 'wcfm_is_allow_order_details', true ) && $WCFM->wcfm_vendor_support->wcfm_is_order_for_vendor( $support_post->order_id ) ) {
								echo '&nbsp;<a class="wcfm_order_for_support img_tip" data-tip="'. __( 'Support Ticket for Order', 'wc-frontend-manager-ultimate' ) . '" target="_blank" href="' . get_wcfm_view_order_url( $support_order_id ) . '">#' . $support_order_id . '</a>';
							} else {
								echo '&nbsp;<span class="wcfm_order_for_support img_tip" data-tip="'. __( 'Support Ticket for Order', 'wc-frontend-manager-ultimate' ) . '">#' . $support_order_id . '</span>';
							}
							?>
						</div>
						<div class="wcfm_clearfix"></div>
						<div class="support_ticket_content_category"><span class="support_ticket_content_category_title"><?php _e( 'Category', 'wc-frontend-manager-ultimate' ); ?></span>:&nbsp;<?php echo $support_post->category; ?></div>
						<div class="wcfm_clearfix"></div><br />
					</div>
					<div class="support_ticket_info">
						<div class="support_ticket_status">
							<?php
							if( $support_post->status == 'open' ) {
								echo '<span class="support-status tips wcicon-status-processing text_tip" data-tip="' . __( 'Open', 'wc-frontend-manager-ultimate' ) . '"></span>&nbsp;' . __( 'Open', 'wc-frontend-manager-ultimate' );
							} else {
								echo '<span class="support-status tips wcicon-status-completed text_tip" data-tip="' . __( 'Closed', 'wc-frontend-manager-ultimate' ) . '"></span>&nbsp;' . __( 'Closed', 'wc-frontend-manager-ultimate' );
							}
							?>
						</div>
						<?php if( apply_filters( 'wcfm_allow_view_customer_name', true ) ) { ?>
							<div class="support_ticket_by">
								<span class="wcfmfa fa-user"></span>&nbsp;
								<span class="support_ticket_by_customer">
								<?php if( $support_customer_id && apply_filters( 'wcfm_is_allow_view_customer', true ) ) { ?>
									<?php echo '<a target="_blank" href="' . get_wcfm_customers_details_url($support_customer_id) . '" class="wcfm_support_by_customer support_ticket_by_customer">' . $support_customer_name . '</a>'; ?>
								<?php } else { ?>
									<?php echo $support_customer_name; ?>
								<?php } ?>
								<?php if( apply_filters( 'wcfm_allow_view_customer_email', true ) ) { ?>
									 <br /><?php echo $support_customer_email; ?>
								<?php } ?>
								</span>
							</div>
							<div class="wcfm_clearfix"></div>
						<?php } ?>
						<div class="support_ticket_date"><span class="wcfmfa fa-clock"></span>&nbsp;<?php echo date_i18n( wc_date_format() . ' ' . wc_time_format(), strtotime( $support_post->posted ) ); ?></div>
						<div class="wcfm_clearfix"></div><br />
					</div>
				</div>
				<div class="wcfm_clearfix"></div>
			</div>
		</div>
		<div class="wcfm_clearfix"></div><br />
		<!-- end collapsible -->
		
		<?php 
		if( $wcfm_is_allow_view_support_reply_view = apply_filters( 'wcfmcap_is_allow_support_reply_view', true ) ) {
			$wcfm_support_replies = $wpdb->get_results( "SELECT * from {$wpdb->prefix}wcfm_support_response WHERE `support_id` = " . $support_id );
			
			echo '<h2>' . __( 'Replies', 'wc-frontend-manager-ultimate' ) . ' (' . count( $wcfm_support_replies ) . ')</h2><div class="wcfm_clearfix"></div>';
			
			if( !empty( $wcfm_support_replies ) ) {
				foreach( $wcfm_support_replies as $wcfm_support_reply ) {
				?>
				<!-- collapsible -->
				<div class="wcfm-container">
					<div id="support_ticket_reply_<?php echo $wcfm_support_reply->ID; ?>" class="support_ticket_reply wcfm-content">
						<div class="support_ticket_reply_author">
							<?php
							$author_id = $wcfm_support_reply->reply_by;
							if( wcfm_is_vendor( $author_id ) ) {
								$wp_user_avatar = $WCFM->wcfm_vendor_support->wcfm_get_vendor_logo_by_vendor( $author_id );
								if( !$wp_user_avatar ) {
									$wp_user_avatar = apply_filters( 'wcfmmp_store_default_logo', $WCFM->plugin_url . 'assets/images/wcfmmp.png' );
								}
							} else {
								$wp_user_avatar_id = get_user_meta( $author_id, $wpdb->get_blog_prefix($blog_id).'user_avatar', true );
								$wp_user_avatar = wp_get_attachment_url( $wp_user_avatar_id );
								if ( !$wp_user_avatar ) {
									$wp_user_avatar = apply_filters( 'wcfm_default_user_image', $WCFM->plugin_url . 'assets/images/user.png' );
								}
							}
							?>
							<img src="<?php echo $wp_user_avatar; ?>" /><br />
							<?php
							if( apply_filters( 'wcfm_allow_view_customer_name', true ) || ( $author_id == $support_vendor_id ) ) {
								if( wcfm_is_vendor( $author_id ) ) {
									echo $WCFM->wcfm_vendor_support->wcfm_get_vendor_store_name_by_vendor( $author_id );
								} elseif( $author_id != $wcfm_support_reply->customer_id ) {
									echo get_bloginfo( 'name' );
								} else {
									$userdata = get_userdata( $author_id );
									$first_name = $userdata->first_name;
									$last_name  = $userdata->last_name;
									$display_name  = $userdata->display_name;
									if( $first_name ) {
										echo $first_name . ' ' . $last_name;
									} else {
										echo $display_name;
									}
								}
							}
							?>
							<br /><?php echo date_i18n( wc_date_format() . ' ' . wc_time_format(), strtotime( $wcfm_support_reply->posted ) ); ?>
						</div>
						<div class="support_ticket_reply_content">
							<?php echo $wcfm_support_reply->reply; ?>
							
							<?php
							// Attachments
							$WCFMu->wcfmu_support->wcfm_support_reply_attachments( $wcfm_support_reply->ID );
							?>
						</div>
					</div>
				</div>
				<div class="wcfm_clearfix"></div><br />
				<!-- end collapsible -->
				<?php
				}
			}
		} 
		?>
		
		<?php if( $wcfm_is_allow_view_support_reply = apply_filters( 'wcfmcap_is_allow_support_reply', true ) ) { ?>
			<?php do_action( 'before_wcfm_support_reply_form' ); ?>
			<form id="wcfm_support_ticket_reply_form" class="wcfm">
				<h2><?php _e('New Reply', 'wc-frontend-manager-ultimate' ); ?></h2>
				<div class="wcfm-clearfix"></div>
				<div class="wcfm-container">
					<div id="wcfm_new_reply_listing_expander" class="wcfm-content">
						<?php
						$rich_editor = apply_filters( 'wcfm_is_allow_rich_editor', 'rich_editor' );
						$wpeditor = apply_filters( 'wcfm_is_allow_profile_wpeditor', 'wpeditor' );
						if( $wpeditor && $rich_editor ) {
							$rich_editor = 'wcfm_wpeditor';
						} else {
							$wpeditor = 'textarea';
						}
						$wcfm_support_ticket_reply_fields = apply_filters( 'wcfm_support_ticket_reply_fields', array(
																																																		"support_ticket_reply" => array( 'label' => __( 'Message', 'wc-frontend-manager'), 'type' => $wpeditor, 'class' => 'wcfm-textarea wcfm_ele wcfm_full_ele ' . $rich_editor, 'label_class' => 'wcfm_title wcfm_full_ele_title', 'media_buttons' => false, 'teeny' => true ),
																																																		"support_reply_break1" => array( 'type' => 'html', 'value' => '<div class="wcfm-clearfix" style="margin-bottom: 25px;"></div>' ),
																																																		"support_attachments"  => array( 'label' => __( 'Attachment(s)', 'wc-frontend-manager'), 'type' => 'multiinput', 'class' => 'wcfm-text wcfm_ele wcfm_non_sortable', 'label_class' => 'wcfm_title', 'value' => array(), 'options' => array(
																																																																		"file" => array( 'label' => __('Add File', 'wc-frontend-manager'), 'type' => 'file', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title'),
																																																							                 ), 'desc' => sprintf( __( 'Please upload any of these file types: %1$s', 'wc-frontend-manager' ), '<b style="color:#f86c6b;">' . implode( ', ', array_keys( wcfm_get_allowed_mime_types() ) ) . '</b>' ) ),
																																																		"support_reply_break2" => array( 'type' => 'html', 'value' => '<div class="wcfm-clearfix" style="margin-bottom: 15px;"></div>' ),
																																																		"support_priority"     => array( 'label' => __( 'Priority', 'wc-frontend-manager-ultimate' ), 'type' => 'select', 'class' => 'wcfm-select wcfm_ele', 'label_class' => 'wcfm_title', 'options' => $support_priority_types, 'value' => $support_post->priority ),
																																																		"support_status"       => array( 'label' => __( 'Status', 'wc-frontend-manager-ultimate' ), 'type' => 'select', 'class' => 'wcfm-select wcfm_ele', 'label_class' => 'wcfm_title', 'options' => $support_status_types, 'value' => $support_post->status ),
																																																		"support_ticket_id"    => array( 'type' => 'hidden', 'value' => $support_id ),
																																																		"support_order_id"     => array( 'type' => 'hidden', 'value' => $support_order_id ),
																																																		"support_item_id"      => array( 'type' => 'hidden', 'value' => $support_item_id ),
																																																		"support_product_id"   => array( 'type' => 'hidden', 'value' => $support_product_id ),
																																																		"support_vendor_id"    => array( 'type' => 'hidden', 'value' => $support_vendor_id ),
																																																		"support_customer_id"  => array( 'type' => 'hidden', 'value' => $support_customer_id ),
																																																		"support_customer_name"  => array( 'type' => 'hidden', 'value' => $support_customer_name ),
																																																		"support_customer_email"  => array( 'type' => 'hidden', 'value' => $support_customer_email )
																																																		), $support_id );
						
						if( ( $wcfm_support_allow_attachment == 'no' ) || !apply_filters( 'wcfm_is_allow_support_reply_attachment', true ) ) {
							if( isset( $wcfm_enquiry_reply_fields['support_attachments'] ) ) unset( $wcfm_enquiry_reply_fields['support_attachments'] );
							if( isset( $wcfm_enquiry_reply_fields['support_reply_break2'] ) ) unset( $wcfm_enquiry_reply_fields['support_reply_break2'] );
						}
						
						$WCFM->wcfm_fields->wcfm_generate_form_field( $wcfm_support_ticket_reply_fields );
						?>
						<div class="wcfm-clearfix"></div>
						<div class="wcfm-message" tabindex="-1"></div>
						<div class="wcfm-clearfix"></div>
						<div id="wcfm_support_reply_submit">
							<input type="submit" name="save-data" value="<?php _e( 'Send', 'wc-frontend-manager-ultimate' ); ?>" id="wcfm_reply_send_button" class="wcfm_submit_button" />
						</div>
						<div class="wcfm-clearfix"></div>
					</div>
				</div>
			</form>
			<?php do_action( 'after_wcfm_support_reply_form' ); ?>
			<div class="wcfm-clearfix"></div><br />
		<?php } ?>
		
		<?php do_action( 'end_wcfm_support_manage_form' ); ?>
		
		<?php
		do_action( 'after_wcfm_support_manage' );
		?>
	</div>
</div>