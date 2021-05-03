<?php
/**
 * WCFM plugin controllers
 *
 * Plugin Support Manage Form Controller
 *
 * @author 		WC Lovers
 * @package 	wcfmu/controllers/support
 * @version   4.0.3
 */

class WCFMu_Support_Manage_Controller {
	
	public function __construct() {
		global $WCFM, $WCFMu;
		
		$this->processing();
	}
	
	public function processing() {
		global $WCFM, $WCFMu, $wpdb, $_POST;
		
		$wcfm_support_reply_form_data = array();
	  parse_str($_POST['wcfm_support_ticket_reply_form'], $wcfm_support_reply_form_data);
	  
	  $wcfm_support_messages = get_wcfm_support_manage_messages();
	  $has_error = false;
	  
	  if(isset($_POST['support_ticket_reply']) && !empty($_POST['support_ticket_reply'])) {
	  	$support_categories    = $WCFMu->wcfmu_support->wcfm_support_categories();
	  	
	  	$wcfm_myac_modified_endpoints = wcfm_get_option( 'wcfm_myac_endpoints', array() );
	  	$wcfm_myaccount_support_ticket_endpoint = ! empty( $wcfm_myac_modified_endpoints['support-tickets'] ) ? $wcfm_myac_modified_endpoints['support-tickets'] : 'support-tickets';
	  	$wcfm_myaccount_view_support_ticket_endpoint = ! empty( $wcfm_myac_modified_endpoints['view-support-ticket'] ) ? $wcfm_myac_modified_endpoints['view-support-ticket'] : 'view-support-ticket';
	  	
	  	// Handle Attachment Uploads - 6.1.5
			$attchments = wcfm_handle_file_upload();
	  	
	  	$support_reply       = apply_filters( 'wcfm_editor_content_before_save', stripslashes( html_entity_decode( $_POST['support_ticket_reply'], ENT_QUOTES, 'UTF-8' ) ) );
	  	$support_priority    = $wcfm_support_reply_form_data['support_priority'];
	  	$support_status      = $wcfm_support_reply_form_data['support_status'];
	  	$support_reply_by    = apply_filters( 'wcfm_message_author', get_current_user_id() );
	  	$support_ticket_id   = absint( $wcfm_support_reply_form_data['support_ticket_id'] );
	  	$support_order_id    = absint( $wcfm_support_reply_form_data['support_order_id'] );
	  	$support_item_id     = absint( $wcfm_support_reply_form_data['support_item_id'] );
	  	$support_product_id  = absint( $wcfm_support_reply_form_data['support_product_id'] );
	  	$support_vendor_id   = absint( $wcfm_support_reply_form_data['support_vendor_id'] );
	  	$support_customer_id = absint( $wcfm_support_reply_form_data['support_customer_id'] );
	  	$support_customer_name = $wcfm_support_reply_form_data['support_customer_name'];
	  	$support_customer_email = $wcfm_support_reply_form_data['support_customer_email'];
	  	
	  	$support_reply           = apply_filters( 'wcfm_support_reply_content', $support_reply, $support_ticket_id, $support_order_id, $support_product_id, $support_vendor_id, $support_customer_id );
	  	$support_reply_mail      = $support_reply;
	  	$support_reply           = esc_sql( $support_reply );
	  	
	  	$current_time = date( 'Y-m-d H:i:s', current_time( 'timestamp', 0 ) );
	  	
	  	if( !defined( 'DOING_WCFM_EMAIL' ) ) 
	  		define( 'DOING_WCFM_EMAIL', true );
	  	
	  	$wcfm_create_support_reply = "INSERT into {$wpdb->prefix}wcfm_support_response 
																	(`reply`, `support_id`, `order_id`, `item_id`, `product_id`, `vendor_id`, `customer_id`, `reply_by`, `posted`)
																	VALUES
																	('{$support_reply}', {$support_ticket_id}, {$support_order_id}, {$support_item_id}, {$support_product_id}, {$support_vendor_id}, {$support_customer_id}, {$support_reply_by}, '{$current_time}')";
													
			$wpdb->query($wcfm_create_support_reply);
			$support_ticket_reply_id = $wpdb->insert_id;
			
			if( $support_ticket_reply_id ) {
			
				// Attachment Update
				$mail_attachments = array();
				if( !empty( $attchments ) && isset( $attchments['support_attachments'] ) && !empty( $attchments['support_attachments'] ) ) {
					$support_attachments = maybe_serialize( $attchments['support_attachments'] );
					$wcfm_support_reply_meta_update = "INSERT into {$wpdb->prefix}wcfm_support_response_meta 
																						(`support_response_id`, `key`, `value`)
																						VALUES
																						({$support_ticket_reply_id}, 'attchment', '{$support_attachments}' )";
					$wpdb->query($wcfm_support_reply_meta_update);
					
					// Prepare Mail Attachment
					$upload_dir = wp_upload_dir();
					foreach( $attchments['support_attachments'] as $support_attachment ) {
						if (empty($upload_dir['error'])) {
							$upload_base = trailingslashit( $upload_dir['basedir'] );
							$upload_url = trailingslashit( $upload_dir['baseurl'] );
							$support_attachment = str_replace( $upload_url, $upload_base, $support_attachment );
							$mail_attachments[] = $support_attachment;
						}
					}
				}
				
				// Priority & Status update
				$wcfm_support_update = "UPDATE {$wpdb->prefix}wcfm_support SET `priority` = '{$support_priority}', `status` = '{$support_status}' 
																WHERE `ID` = {$support_ticket_id}";
										
				$wpdb->query($wcfm_support_update);
				
				$myaccount_page_id = get_option( 'woocommerce_myaccount_page_id' );
				if ( $myaccount_page_id ) {
					$myaccount_page_url = trailingslashit( get_permalink( $myaccount_page_id ) );
				}
				$support_ticket_url = $myaccount_page_url .$wcfm_myaccount_view_support_ticket_endpoint.'/' . $support_ticket_id;
				
				// Send mail to Customer
				$mail_to = apply_filters( 'wcfm_admin_email_notification_receiver', get_bloginfo( 'admin_email' ), 'support' );
				$reply_mail_subject = '{site_name}: ' . __( 'Support Ticket Reply', 'wc-frontend-manager-ultimate' ) . ' - ' . __( 'Ticket', 'wc-frontend-manager-ultimate' ) . ' #{support_ticket_id}';
				$reply_mail_body =   '<br/>' .  __( 'Hi', 'wc-frontend-manager-ultimate' ) .
														 ',<br/><br/>' . 
														 sprintf( __( 'You have received reply for your "%s" support request. Please see our response below: ', 'wc-frontend-manager-ultimate' ), '{product_title}' ) .
														 '<br/><br/><strong><i>' . 
														 '"{support_reply}"' . 
														 '</i></strong><br/><br/>' .
														 __( 'See details here', 'wc-frontend-manager-ultimate' ) . ': <a href="{support_url}">' . __( 'Ticket', 'wc-frontend-manager-ultimate' ) . ' #{support_ticket_id}</a>' .
														 '<br /><br/>' . __( 'Thank You', 'wc-frontend-manager-ultimate' ) .
														 '<br/><br/>';
				
				$subject = str_replace( '{site_name}', get_bloginfo( 'name' ), $reply_mail_subject );
				$subject = apply_filters( 'wcfm_email_subject_wrapper', $subject );
				$subject = str_replace( '{support_url}', $support_ticket_url, $subject );
				$subject = str_replace( '{support_ticket_id}', sprintf( '%06u', $support_ticket_id ), $subject );
				$subject = str_replace( '{product_title}', get_the_title( $support_product_id ), $subject );
				$message = str_replace( '{product_title}', get_the_title( $support_product_id ), $reply_mail_body );
				$message = str_replace( '{support_url}', $support_ticket_url, $message );
				$message = str_replace( '{support_reply}', $support_reply_mail, $message );
				$message = str_replace( '{support_ticket_id}', sprintf( '%06u', $support_ticket_id ), $message );
				$message = apply_filters( 'wcfm_email_content_wrapper', $message, __( 'Reply to Support Ticket', 'wc-frontend-manager-ultimate' ) . ' #' . sprintf( '%06u', $support_ticket_id ) );
				
				$vendor_reply = false;
				if( wcfm_is_marketplace() ) {
					if( $support_vendor_id ) {
						$is_allow_enquiry = $WCFM->wcfm_vendor_support->wcfm_vendor_has_capability( $support_vendor_id, 'support_ticket' );
						if( $is_allow_enquiry && apply_filters( 'wcfm_is_allow_support_vendor_notification', true ) ) {
							$vendor_email = $WCFM->wcfm_vendor_support->wcfm_get_vendor_email_by_vendor( $support_vendor_id );
							if( $vendor_email ) {
								if( apply_filters( 'wcfm_is_allow_support_customer_reply', true ) && $WCFM->wcfm_vendor_support->wcfm_vendor_has_capability( $support_vendor_id, 'view_email' ) ) {
									$vendor_reply = true;
									$headers[] = 'Reply-to: ' . $WCFM->wcfm_vendor_support->wcfm_get_vendor_store_name_by_vendor( $support_vendor_id ) . ' ' . apply_filters( 'wcfm_sold_by_label', $support_vendor_id, __( 'Store', 'wc-frontend-manager' ) ) . ' <' . $vendor_email . '>';
								}
							}
						}
					}
				}
					
				if( $vendor_reply ) {
					wp_mail( $support_customer_email, $subject, $message, $headers, $mail_attachments );
				} else {
					wp_mail( $support_customer_email, $subject, $message, '', $mail_attachments );
				}
					
				
				// Direct message
				/*$wcfm_messages = sprintf( __( 'You have received reply for Support Ticket <b>%s</b>', 'wc-frontend-manager-ultimate' ), '<a target="_blank" class="wcfm_dashboard_item_title" href="' . get_wcfm_support_manage_url( $support_ticket_id ) . '">#' . $support_ticket_id . '</a>' );
				$WCFM->wcfm_notification->wcfm_send_direct_message( -2, 0, 1, 0, $wcfm_messages, 'support' );
				
				// Semd email to vendor
				if( wcfm_is_marketplace() ) {
					if( $support_vendor_id ) {
						$vendor_email = $WCFM->wcfm_vendor_support->wcfm_get_vendor_email_from_product( $support_product_id );
						if( $vendor_email ) {
							wp_mail( $vendor_email, $subject, $message, $headers );
						}
						
						// Direct message
						$wcfm_messages = sprintf( __( 'You have received reply for Support Ticket <b>%s</b>', 'wc-frontend-manager-ultimate' ), '<a target="_blank" class="wcfm_dashboard_item_title" href="' . get_wcfm_support_manage_url( $support_ticket_id ) . '">#' . $support_ticket_id . '</a>' );
						$WCFM->wcfm_notification->wcfm_send_direct_message( -1, $support_vendor_id, 1, 0, $wcfm_messages, 'support' );
					}
				}*/
				
				do_action( 'wcfm_after_support_vendor_reply',  $support_ticket_id, $support_order_id, $support_product_id, $support_customer_id, $support_vendor_id, $support_reply );
				
			}
			
			echo '{"status": true, "message": "' . $wcfm_support_messages['support_reply_saved'] . '", "redirect": "' . get_wcfm_support_manage_url( $support_ticket_id ) . '#support_ticket_reply_' . $support_ticket_reply_id . '"}';
		} else {
			echo '{"status": false, "message": "' . $wcfm_support_messages['no_reply'] . '"}';
		}
		
		die;
	}
}

class WCFMu_My_Account_Support_Manage_Controller {
	
	public function __construct() {
		global $WCFM, $WCFMu;
		
		$this->processing();
	}
	
	public function processing() {
		global $WCFM, $WCFMu, $wpdb, $_POST;
		
		$wcfm_support_reply_form_data = array();
	  parse_str($_POST['wcfm_support_ticket_reply_form'], $wcfm_support_reply_form_data);
	  
	  $wcfm_support_messages = get_wcfm_support_manage_messages();
	  $has_error = false;
	  
	  if(isset($_POST['support_ticket_reply']) && !empty($_POST['support_ticket_reply'])) {
	  	$support_categories    = $WCFMu->wcfmu_support->wcfm_support_categories();
	  	
	  	$wcfm_myac_modified_endpoints = wcfm_get_option( 'wcfm_myac_endpoints', array() );
	  	$wcfm_myaccount_support_ticket_endpoint = ! empty( $wcfm_myac_modified_endpoints['support-tickets'] ) ? $wcfm_myac_modified_endpoints['support-tickets'] : 'support-tickets';
	  	$wcfm_myaccount_view_support_ticket_endpoint = ! empty( $wcfm_myac_modified_endpoints['view-support-ticket'] ) ? $wcfm_myac_modified_endpoints['view-support-ticket'] : 'view-support-ticket';
	  	
	  	// Handle Attachment Uploads - 6.1.5
			$attchments = wcfm_handle_file_upload();
	  	
	  	$support_reply       = apply_filters( 'wcfm_editor_content_before_save', stripslashes( html_entity_decode( $_POST['support_ticket_reply'], ENT_QUOTES, 'UTF-8' ) ) );
	  	$support_priority    = $wcfm_support_reply_form_data['support_priority'];
	  	$support_status      = $wcfm_support_reply_form_data['support_status'];
	  	$support_reply_by    = apply_filters( 'wcfm_message_author', get_current_user_id() );
	  	$support_ticket_id   = absint( $wcfm_support_reply_form_data['support_ticket_id'] );
	  	$support_order_id    = absint( $wcfm_support_reply_form_data['support_order_id'] );
	  	$support_item_id     = absint( $wcfm_support_reply_form_data['support_item_id'] );
	  	$support_product_id  = absint( $wcfm_support_reply_form_data['support_product_id'] );
	  	$support_vendor_id   = absint( $wcfm_support_reply_form_data['support_vendor_id'] );
	  	$support_customer_id = absint( $wcfm_support_reply_form_data['support_customer_id'] );
	  	$support_customer_name = $wcfm_support_reply_form_data['support_customer_name'];
	  	$support_customer_email = $wcfm_support_reply_form_data['support_customer_email'];
	  	
	  	$support_reply       = apply_filters( 'wcfm_support_reply_content', $support_reply, $support_ticket_id, $support_order_id, $support_product_id, $support_vendor_id, $support_customer_id );
	  	$support_reply_mail      = $support_reply;
	  	$support_reply       = esc_sql( $support_reply );
	  	
	  	$current_time = date( 'Y-m-d H:i:s', current_time( 'timestamp', 0 ) );
	  	
	  	if( !defined( 'DOING_WCFM_EMAIL' ) ) 
	  		define( 'DOING_WCFM_EMAIL', true );
	  	
	  	$wcfm_create_support_reply = "INSERT into {$wpdb->prefix}wcfm_support_response 
																	(`reply`, `support_id`, `order_id`, `item_id`, `product_id`, `vendor_id`, `customer_id`, `reply_by`, `posted`)
																	VALUES
																	('{$support_reply}', {$support_ticket_id}, {$support_order_id}, {$support_item_id}, {$support_product_id}, {$support_vendor_id}, {$support_customer_id}, {$support_reply_by}, '{$current_time}')";
													
			$wpdb->query($wcfm_create_support_reply);
			$support_ticket_reply_id = $wpdb->insert_id;
			
			if( $support_ticket_reply_id ) {
			
				// Attachment Update
				$mail_attachments = array();
				if( !empty( $attchments ) && isset( $attchments['support_attachments'] ) && !empty( $attchments['support_attachments'] ) ) {
					$support_attachments = maybe_serialize( $attchments['support_attachments'] );
					$wcfm_support_reply_meta_update = "INSERT into {$wpdb->prefix}wcfm_support_response_meta 
																						(`support_response_id`, `key`, `value`)
																						VALUES
																						({$support_ticket_reply_id}, 'attchment', '{$support_attachments}' )";
					$wpdb->query($wcfm_support_reply_meta_update);
					
					// Prepare Mail Attachment
					$upload_dir = wp_upload_dir();
					foreach( $attchments['support_attachments'] as $support_attachment ) {
						if (empty($upload_dir['error'])) {
							$upload_base = trailingslashit( $upload_dir['basedir'] );
							$upload_url = trailingslashit( $upload_dir['baseurl'] );
							$support_attachment = str_replace( $upload_url, $upload_base, $support_attachment );
							$mail_attachments[] = $support_attachment;
						}
					}
				}
				
				// Priority & Status Update
				$wcfm_support_update = "UPDATE {$wpdb->prefix}wcfm_support SET `priority` = '{$support_priority}', `status` = '{$support_status}' 
																WHERE `ID` = {$support_ticket_id}";
										
				$wpdb->query($wcfm_support_update);
				
				// Send mail to admin
				$mail_to = apply_filters( 'wcfm_admin_email_notification_receiver', get_bloginfo( 'admin_email' ), 'support' );
				$reply_mail_subject = '{site_name}: ' . __( 'Support Ticket Reply', 'wc-frontend-manager-ultimate' ) . ' - ' . __( 'Ticket', 'wc-frontend-manager-ultimate' ) . ' #{support_ticket_id}';
				$reply_mail_body =   '<br/>' . __( 'Hi', 'wc-frontend-manager-ultimate' ) .
														 ',<br/><br/>' . 
														 __( 'You have received reply for your "{product_title}" support request. Please see our response below: ', 'wc-frontend-manager-ultimate' ) .
														 '<br/><br/><strong><i>' . 
														 '"{support_reply}"' . 
														 '</i></strong><br/><br/>' .
														 __( 'See details here', 'wc-frontend-manager-ultimate' ) . ': <a href="{support_url}">' . __( 'Ticket', 'wc-frontend-manager-ultimate' ) . ' #{support_ticket_id}</a>' .
														 '<br /><br/>' . __( 'Thank You', 'wc-frontend-manager-ultimate' ) .
														 '<br/><br/>';
				
				//$headers[] = 'From: [' . get_bloginfo( 'name' ) . '] ' . __( 'Support Ticket Reply', 'wc-frontend-manager-ultimate' );
				//$headers[] = 'Cc: ' . $mail_to;
				if( apply_filters( 'wcfm_is_allow_support_customer_reply', true ) ) {
					$headers[] = 'Reply-to: ' . $support_customer_name . ' <' . $support_customer_email . '>';
				}
				$subject = str_replace( '{site_name}', get_bloginfo( 'name' ), $reply_mail_subject );
				$subject = apply_filters( 'wcfm_email_subject_wrapper', $subject );
				$subject = str_replace( '{support_url}', get_wcfm_support_manage_url( $support_ticket_id ), $subject );
				$subject = str_replace( '{support_ticket_id}', sprintf( '%06u', $support_ticket_id ), $subject );
				$subject = str_replace( '{product_title}', get_the_title( $support_product_id ), $subject );
				$message = str_replace( '{product_title}', get_the_title( $support_product_id ), $reply_mail_body );
				$message = str_replace( '{support_url}', get_wcfm_support_manage_url( $support_ticket_id ), $message );
				$message = str_replace( '{support_reply}', $support_reply_mail, $message );
				$message = str_replace( '{support_ticket_id}', sprintf( '%06u', $support_ticket_id ), $message );
				$message = apply_filters( 'wcfm_email_content_wrapper', $message, __( 'Reply to Support Ticket', 'wc-frontend-manager-ultimate' ) . ' #' . sprintf( '%06u', $support_ticket_id ) );
				
				if( apply_filters( 'wcfm_is_allow_notification_email', true, 'support', 0 ) ) {
					if( apply_filters( 'wcfm_is_allow_support_customer_reply', true ) ) {
						wp_mail( $mail_to, $subject, $message, $headers, $mail_attachments );
					} else {
						wp_mail( $mail_to, $subject, $message );
					}
				}
				
				// Direct message
				if( apply_filters( 'wcfm_is_allow_notification_message', true, 'support', 0 ) ) {
					$wcfm_messages = sprintf( __( 'You have received reply for Support Ticket <b>%s</b>', 'wc-frontend-manager-ultimate' ), '<a target="_blank" class="wcfm_dashboard_item_title" href="' . get_wcfm_support_manage_url( $support_ticket_id ) . '">#' . sprintf( '%06u', $support_ticket_id ) . '</a>' );
					$WCFM->wcfm_notification->wcfm_send_direct_message( -2, 0, 1, 0, $wcfm_messages, 'support', false );
				}
				
				// Semd email to vendor
				if( wcfm_is_marketplace() ) {
					if( $support_vendor_id ) {
						$is_allow_support = $WCFM->wcfm_vendor_support->wcfm_vendor_has_capability( $support_vendor_id, 'support_ticket_manage' );
						if( $is_allow_support && apply_filters( 'wcfm_is_allow_support_vendor_notification', true ) ) {
							$vendor_email = $WCFM->wcfm_vendor_support->wcfm_get_vendor_email_from_product( $support_product_id );
							if( $vendor_email && apply_filters( 'wcfm_is_allow_notification_email', true, 'support', $support_vendor_id ) ) {
								if( apply_filters( 'wcfm_is_allow_support_customer_reply', true ) ) {
									wp_mail( $vendor_email, $subject, $message, $headers, $mail_attachments );
								} else {
									wp_mail( $vendor_email, $subject, $message, $headers, $mail_attachments );
								}
							}
							
							// Direct message
							if( apply_filters( 'wcfm_is_allow_notification_message', true, 'support', $support_vendor_id ) ) {
								$wcfm_messages = sprintf( __( 'You have received reply for Support Ticket <b>%s</b>', 'wc-frontend-manager-ultimate' ), '<a target="_blank" class="wcfm_dashboard_item_title" href="' . get_wcfm_support_manage_url( $support_ticket_id ) . '">#' . sprintf( '%06u', $support_ticket_id ) . '</a>' );
								$WCFM->wcfm_notification->wcfm_send_direct_message( -1, $support_vendor_id, 1, 0, $wcfm_messages, 'support', false );
							}
						}
					}
				}
				
				do_action( 'wcfm_after_support_customer_reply',  $support_ticket_id, $support_order_id, $support_product_id, $support_customer_id, $support_vendor_id, $support_reply );
				
			}
			
	  	$myaccount_page_id = get_option( 'woocommerce_myaccount_page_id' );
			if ( $myaccount_page_id ) {
				$myaccount_page_url = trailingslashit( get_permalink( $myaccount_page_id ) );
			}
			echo '{"status": true, "message": "' . $wcfm_support_messages['support_reply_saved'] . '", "redirect": "' . $myaccount_page_url .$wcfm_myaccount_view_support_ticket_endpoint.'/' . $support_ticket_id . '#support_ticket_reply_' . $support_ticket_reply_id . '"}';
		} else {
			echo '{"status": false, "message": "' . $wcfm_support_messages['no_reply'] . '"}';
		}
		
		die;
	}
}