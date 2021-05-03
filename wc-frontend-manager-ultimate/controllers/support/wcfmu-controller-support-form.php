<?php
/**
 * WCFM plugin controllers
 *
 * Plugin Support Form Controller
 *
 * @author 		WC Lovers
 * @package 	wcfmu/controllers/support
 * @version   4.0.3
 */

class WCFMu_Support_Form_Controller {
	
	public function __construct() {
		global $WCFM, $WCFMu;
		
		$this->processing();
	}
	
	public function processing() {
		global $WCFM, $WCFMu, $wpdb;
		
		$wcfm_support_tab_form_data = array();
	  parse_str($_POST['wcfm_support_form'], $wcfm_support_tab_form_data);
	  
	  $wcfm_support_messages = get_wcfm_support_manage_messages();
	  $has_error = false;
	  
	  // Google reCaptcha support
	  if ( function_exists( 'gglcptch_init' ) ) {
			if(isset($wcfm_support_tab_form_data['g-recaptcha-response']) && !empty($wcfm_support_tab_form_data['g-recaptcha-response'])) {
				$_POST['g-recaptcha-response'] = $wcfm_support_tab_form_data['g-recaptcha-response'];
			}
			$check_result = apply_filters( 'gglcptch_verify_recaptcha', true, 'string', 'wcfm_support_form' );
			if ( true === $check_result ) {
					/* do necessary action */
			} else { 
				echo '{"status": false, "message": "' . $check_result . '"}';
				die;
			}
		} elseif ( class_exists( 'anr_captcha_class' ) && function_exists( 'anr_captcha_form_field' ) ) {
			$check_result = anr_verify_captcha( $wcfm_support_tab_form_data['g-recaptcha-response'] );
			if ( true === $check_result ) {
					/* do necessary action */
			} else { 
				echo '{"status": false, "message": "' . __( 'Captcha failed, please try again.', 'wc-frontend-manager' ) . '"}';
				die;
			}
		}
	  
	  if(isset($wcfm_support_tab_form_data['wcfm_support_query']) && !empty($wcfm_support_tab_form_data['wcfm_support_query'])) {
	  	$support_categories    = $WCFMu->wcfmu_support->wcfm_support_categories();
	  	
	  	$support_query       = apply_filters( 'wcfm_editor_content_before_save', wcfm_stripe_newline( $wcfm_support_tab_form_data['wcfm_support_query'] ) );
	  	
	  	$order_id            = absint( $wcfm_support_tab_form_data['wcfm_support_order_id'] );
	  	$support_category    = $wcfm_support_tab_form_data['wcfm_support_category'];
	  	if( isset( $support_categories[$support_category] ) ) $support_category = $support_categories[$support_category];
	  	$support_priority = $wcfm_support_tab_form_data['wcfm_support_priority'];
	  	$support_item_id  = absint( $wcfm_support_tab_form_data['wcfm_support_product'] );
	  	$product_id       = 0;
	  	$support_item     = '';
	  	
	  	$order                  = wc_get_order( $order_id );
			$line_items             = $order->get_items( 'line_item' );
			$product_items          = array();
			foreach ( $line_items as $item_id => $item ) {
				if( $item_id == $support_item_id ) {
					$product_id       = absint( $item->get_product_id() );
					$support_item     = $item;
				}
			}
	  	
	  	$product_post = get_post( $product_id );
	  	$author_id = $product_post->post_author;
	  	
	  	$vendor_id = 0;
	  	if( wcfm_is_marketplace() ) {
	  		$vendor_id = wcfm_get_vendor_id_by_post( $product_id );
	  	}
	  	
	  	
			$customer_id = get_current_user_id();
			$userdata = get_userdata( $customer_id );
			$first_name = $userdata->first_name;
			$last_name  = $userdata->last_name;
			$display_name  = $userdata->display_name;
			if( $first_name ) {
				$customer_name = $first_name . ' ' . $last_name;
			} else {
				$customer_name = $display_name;
			}
			$customer_email = $userdata->user_email;
			
			$support_query       = apply_filters( 'wcfm_support_query_content', $support_query, $order_id, $product_id, $vendor_id, $customer_id );
			$support_query_mail  = $support_query;
	  	$support_query       = esc_sql( $support_query );
			
			$current_time = date( 'Y-m-d H:i:s', current_time( 'timestamp', 0 ) );
	  	
			if( !defined( 'DOING_WCFM_EMAIL' ) ) 
	  	  define( 'DOING_WCFM_EMAIL', true );
	  	
	  	$wcfm_create_support    = "INSERT into {$wpdb->prefix}wcfm_support 
																(`query`, `order_id`, `item_id`, `product_id`, `author_id`, `vendor_id`, `customer_id`, `customer_name`, `customer_email`, `category`, `priority`, `status`, `posted`)
																VALUES
																('{$support_query}', {$order_id}, {$support_item_id}, {$product_id}, {$author_id}, {$vendor_id}, {$customer_id}, '{$customer_name}', '{$customer_email}', '{$support_category}', '{$support_priority}', 'open', '{$current_time}')";
															
			$wpdb->query($wcfm_create_support);
			$support_ticket_id = $wpdb->insert_id;
			
			if( $support_ticket_id ) {
			
				// Send mail to admin
				$mail_to = apply_filters( 'wcfm_admin_email_notification_receiver', get_bloginfo( 'admin_email' ), 'support' );
				$reply_mail_subject = '{site_name}: ' . __( 'New Support Request', 'wc-frontend-manager-ultimate' ) . ' - ' . __( 'Ticket', 'wc-frontend-manager-ultimate' ) . ' #{support_ticket_id}';
				$reply_mail_body =   '<br/>' . __( 'Hi', 'wc-frontend-manager-ultimate' ) .
														 ',<br/><br/>' . 
														 sprintf( __( 'You have recently received a support request for "%s". Please check below for the details: ', 'wc-frontend-manager-ultimate' ), '{product_title}' ) .
														 '<br/><br/><strong><i>' . 
														 '"{support}"' . 
														 '</i></strong><br/><br/>' .
														 __( 'Check more details here', 'wc-frontend-manager-ultimate' ) . ': <a href="{support_url}">' . __( 'Ticket', 'wc-frontend-manager-ultimate' ) . ' #{support_ticket_id}</a>' .
														 '<br /><br/>' . __( 'Thank You', 'wc-frontend-manager-ultimate' ) .
														 '<br/><br/>';
				
				//$headers[] = 'From: [' . get_bloginfo( 'name' ) . '] ' . __( 'Support Ticket', 'wc-frontend-manager-ultimate' ) . ': ' . $customer_name . ' <' . $customer_email . '>';
				if( apply_filters( 'wcfm_is_allow_support_customer_reply', true ) ) {
					$headers[] = 'Reply-to: ' . $customer_name . ' <' . $customer_email . '>';
				}
				
				$subject = str_replace( '{site_name}', get_bloginfo( 'name' ), $reply_mail_subject );
				$subject = apply_filters( 'wcfm_email_subject_wrapper', $subject );
				$subject = str_replace( '{support_url}', get_wcfm_support_manage_url( $support_ticket_id ), $subject );
				$subject = str_replace( '{support_ticket_id}', sprintf( '%06u', $support_ticket_id ), $subject );
				$subject = str_replace( '{product_title}', get_the_title( $product_id ), $subject );
				$message = str_replace( '{product_title}', get_the_title( $product_id ), $reply_mail_body );
				$message = str_replace( '{support_url}', get_wcfm_support_manage_url( $support_ticket_id ), $message );
				$message = str_replace( '{support_ticket_id}', sprintf( '%06u', $support_ticket_id ), $message );
				$message = str_replace( '{support}', $support_query_mail, $message );
				$message = apply_filters( 'wcfm_email_content_wrapper', $message, __( 'New Support Ticket', 'wc-frontend-manager-ultimate' ) . ' #' . sprintf( '%06u', $support_ticket_id ) );
				
				if( apply_filters( 'wcfm_is_allow_notification_email', true, 'support', 0 ) ) {
					if( apply_filters( 'wcfm_is_allow_support_customer_reply', true ) ) {
						wp_mail( $mail_to, $subject, $message, $headers );
					} else {
						wp_mail( $mail_to, $subject, $message );
					}
				}
				
				// Direct message
				if( apply_filters( 'wcfm_is_allow_notification_message', true, 'support', 0 ) ) {
					$wcfm_messages = sprintf( __( 'You have recently received a support request. Ticket <b>%s</b>', 'wc-frontend-manager-ultimate' ), '<a target="_blank" class="wcfm_dashboard_item_title" href="' . get_wcfm_support_manage_url( $support_ticket_id ) . '">#' . sprintf( '%06u', $support_ticket_id ) . '</a>' );
					$WCFM->wcfm_notification->wcfm_send_direct_message( -2, 0, 1, 0, $wcfm_messages, 'support', false );
				}
				
				// Semd email to vendor
				if( wcfm_is_marketplace() ) {
					if( $vendor_id ) {
						$is_allow_support = $WCFM->wcfm_vendor_support->wcfm_vendor_has_capability( $vendor_id, 'support_ticket' );
						if( $is_allow_support && apply_filters( 'wcfm_is_allow_support_vendor_notification', true ) ) {
							$vendor_email = $WCFM->wcfm_vendor_support->wcfm_get_vendor_email_from_product( $product_id );
							if( $vendor_email && apply_filters( 'wcfm_is_allow_notification_email', true, 'support', $vendor_id ) ) {
								if( $WCFM->wcfm_vendor_support->wcfm_vendor_has_capability( $vendor_id, 'view_email' ) ) {
									wp_mail( $vendor_email, $subject, $message, $headers );
								} else {
									wp_mail( $vendor_email, $subject, $message );
								}
							}
							
							// Direct message
							if( apply_filters( 'wcfm_is_allow_notification_message', true, 'support', $vendor_id ) ) {
								$wcfm_messages = sprintf( __( 'You have recently received a support request. Ticket <b>%s</b>', 'wc-frontend-manager-ultimate' ), '<a target="_blank" class="wcfm_dashboard_item_title" href="' . get_wcfm_support_manage_url( $support_ticket_id ) . '">#' . sprintf( '%06u', $support_ticket_id ) . '</a>' );
								$WCFM->wcfm_notification->wcfm_send_direct_message( -1, $vendor_id, 1, 0, $wcfm_messages, 'support', false );
							}
						}
					}
				}
				
				do_action( 'wcfm_after_support_request',  $support_ticket_id, $order_id, $product_id, $customer_id, $vendor_id, $support_query, $wcfm_support_tab_form_data );
				
			}
			
			echo '{"status": true, "message": "' . $wcfm_support_messages['support_saved'] . ' #' . sprintf( '%06u', $support_ticket_id ) . '"}';
		} else {
			echo '{"status": false, "message": "' . $wcfm_support_messages['no_query'] . '"}';
		}
		
		die;
	}
}