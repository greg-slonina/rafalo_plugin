<?php
global $wp, $WCFM, $WCFMu, $wp_query;

$order_id = 0;
if( isset( $wp->query_vars['wcfm-orders-details'] ) && !empty( $wp->query_vars['wcfm-orders-details'] ) ) {
	$order_id = absint($wp->query_vars['wcfm-orders-details']);
} else {
	return;
}

if( !$order_id ) return;

$order = wc_get_order( $order_id );

if( !is_a( $order, 'WC_Order' ) ) return;

$order_status = sanitize_title( $order->get_status() );
$order_status = apply_filters( 'wcfm_current_order_status', $order_status, $order_id );

// WooCommerce Quotation - 6.2.7
if( apply_filters( 'wcfm_is_allow_yith_request_quote', true ) ) {
	if( WCFMu_Dependencies::wcfm_yith_request_quote_active_check() ) {
		global $post, $theorder;

		if ( !empty( $theorder ) ) {
				$order = $theorder;
				$status = $order_status;
		} else if ( $order = wc_get_order( $post ) ) {
				$status = $order_status;
		}
		
		$customer_name            = '';
		$customer_message         = '';
		$customer_email           = '';
		$additional_field         = '';
		$additional_field_2       = '';
		$additional_field_3       = '';
		$additional_email_content = '';
		$customer_attachments      = '';
		$status                   = '';
		$button_disabled          = '';
		$pdf_file                 = '';
		$attachment_text          = '';
		$optional_attachment      = '';
		$request_expire           = '';
		$ywraq_checkout_info      = '';
		
		$billing_address = '';
		$billing_phone   = '';
		$billing_vat     = '';
		
		$customer_name            = get_post_meta( $order_id, 'ywraq_customer_name', true );
		$customer_message         = get_post_meta( $order_id, 'ywraq_customer_message', true );
		$request_response         = get_post_meta( $order_id, '_ywraq_request_response', true );
		$request_response_after   = get_post_meta( $order_id, '_ywraq_request_response_after', true );
		$optional_attachment      = get_post_meta( $order_id, '_ywraq_optional_attachment', true );
		$request_expire           = get_post_meta( $order_id, '_ywcm_request_expire', true );
		$customer_email           = get_post_meta( $order_id, 'ywraq_customer_email', true );
		$additional_field         = get_post_meta( $order_id, 'ywraq_customer_additional_field', true );
		$additional_field_2       = get_post_meta( $order_id, 'ywraq_customer_additional_field_2', true );
		$additional_field_3       = get_post_meta( $order_id, 'ywraq_customer_additional_field_3', true );
		$customer_attachments     = get_post_meta( $order_id, 'ywraq_customer_attachment', true );
		$additional_email_content = get_post_meta( $order_id, 'ywraq_other_email_content', true );
		$billing_address          = get_post_meta( $order_id, 'ywraq_billing_address', true );
		$billing_phone            = get_post_meta( $order_id, 'ywraq_billing_phone', true );
		$billing_vat              = get_post_meta( $order_id, 'ywraq_billing_vat', true );
		
		$ywraq_checkout_info      = get_post_meta( $order_id, '_ywraq_checkout_info', true );
	
	
	
		if( $billing_address != ''){
			$additional_email_content .= sprintf('<strong>%s</strong>: %s</br>', __('Billing Address', 'yith-woocommerce-request-a-quote'), $billing_address) ;
		}
	
		if( $billing_phone != ''){
			$additional_email_content .= sprintf('<strong>%s</strong>: %s</br>', __('Billing Phone', 'yith-woocommerce-request-a-quote'), $billing_phone) ;
		}
	
		if( $billing_vat != ''){
			$additional_email_content .= sprintf('<strong>%s</strong>: %s</br>', __('Billing Vat', 'yith-woocommerce-request-a-quote'), $billing_vat) ;
		}
	
		if( $customer_message != ''){
			//$customer_message =  '<strong>'. __( 'Message', 'yith-woocommerce-request-a-quote' ). '</strong>: '.  $customer_message;
		}
	
		if( $additional_field != ''){
			$additional_field =  '<strong>'. get_option('ywraq_additional_text_field_label') .'</strong>: '. $additional_field;
		}
	
		if( $additional_field_2 != ''){
			$additional_field_2 =  '<strong>'. get_option('ywraq_additional_text_field_label_2') .'</strong>: '. $additional_field_2;
		}
	
		if( $additional_field_3 != ''){
			$additional_field_3 =  '<strong>'. get_option('ywraq_additional_text_field_label_3') .'</strong>: '. $additional_field_3;
		}
	
	
		if ( ! empty( $customer_attachments ) ) {
			if ( isset( $customer_attachments['url'] ) ) {
				$attachment_text = '<strong>' . __( 'Attachment', 'yith-woocommerce-request-a-quote' ) . '</strong>:  <a href="' . $customer_attachments['url'] . '" target="_blank">' . $customer_attachments['url'] . '</a>';
			} else {
				foreach ( $customer_attachments as $key => $item ) {
						$attachment_text .= '<div><strong>' . $key . '</strong>:  <a href="' . $item . '" target="_blank">' . $item . '</a></div>';
				}
			}
	
		}
		
		$accepted_statuses = apply_filters( 'ywraq_quote_accepted_statuses_send', array( 'ywraq-new', 'ywraq-rejected' ) );

		if ( !empty( $order ) ) {
			$status = $order->get_status();
			if ( ! ( defined( 'WP_DEBUG' ) && WP_DEBUG ) && ! $order->has_status( $accepted_statuses ) ) {
				$button_disabled = 'disabled="disabled"';
			}
			if ( file_exists( YITH_Request_Quote_Premium()->get_pdf_file_path( $order_id ) ) ) {
				$pdf_file = YITH_Request_Quote_Premium()->get_pdf_file_url( $order_id );
			}
		}

		?>
		<div class="wcfm-clearfix"></div>
		<br />
		<!-- collapsible -->
		<div class="page_collapsible orders_details_yith_request_quote" id="sm_order_yith_request_quote"><?php _e( 'Request a Quote Order Settings', 'yith-woocommerce-request-a-quote' ); ?><span></span></div>
		<div class="wcfm-container orders_details_yith_request_quote_expander_container">
			<div id="orders_details_yith_request_quote_expander" class="wcfm-content">
		    <form id="wcfm_yith_request_quote_form" method="POST">
					<?php
					$fields =  array(
														'ywraq_customer_name' => array(
															'label' => __( 'Customer\'s name', 'yith-woocommerce-request-a-quote' ),
															'desc'  => '',
															'type'  => 'text',
															'name' => 'yit_metaboxes[ywraq_customer_name]',
															'class' => 'wcfm-text',
															'label_class' => 'wcfm_title',
															'value' => $customer_name
														),
												
														'ywraq_customer_email' => array(
															'label' => __( 'Customer\'s email', 'yith-woocommerce-request-a-quote' ),
															'desc'  => '',
															'type'  => 'text',
															'name' => 'yit_metaboxes[ywraq_customer_email]',
															'class' => 'wcfm-text',
															'label_class' => 'wcfm_title',
															'value' => $customer_email
														),
												
														'ywraq_customer_message' => array(
															'label' => __( 'Customer\'s message', 'yith-woocommerce-request-a-quote' ),
															'desc'  =>  '',
															'type'  => 'textarea',
															'private'  => false,
															'name' => 'yit_metaboxes[ywraq_customer_message]',
															'class' => 'wcfm-textarea',
															'label_class' => 'wcfm_title',
															'value' => $customer_message
														)
													)
												;
												
												
												if ( ! empty( $additional_email_content ) ) {
													$fields['ywraq_additional_email_content_title'] = array(
														'value'  => '<strong>' . __( 'Additional email content', 'yith-woocommerce-request-a-quote' ) . '</strong>',
														'type'  => 'html',
													);
												
													$fields['ywraq_customer_additional_email_content'] = array(
														'value'  => $additional_email_content,
														'type'  => 'html'
													);
												}
												
												if ( ! empty( $additional_field ) ) {
													$fields['ywraq_customer_additional_field'] = array(
														'value'  => $additional_field,
														'type'  => 'html'
													);
												}
												
												if ( ! empty( $additional_field ) ) {
													$fields['ywraq_customer_additional_field_2'] = array(
														'value'  => $additional_field_2,
														'type'  => 'html'
													);
												}
												
												if ( ! empty( $additional_field_3 ) ) {
													$fields['ywraq_customer_additional_field_3'] = array(
														'value'  => $additional_field_3,
														'type'  => 'html'
													);
												}
												
												if ( ! empty( $attachment_text ) ) {
													$fields['ywraq_customer_attachment'] = array(
														'value'  => $attachment_text,
														'type'  => 'html'
													);
												}
												
												$fields['ywraq_customer_sep1'] = array(
														'value'  => '<br /><hr /><br />',
														'type'  => 'html'
													);
												
												$group_2 = array(
												
													//@since 1.3.0
													'ywraq_request_response'        => array(
														'label' => __( 'Attach message to the quote before the table list (optional)', 'yith-woocommerce-request-a-quote' ),
														'type'  => 'textarea',
														'desc'  => __( 'Write a message that will be attached to the quote', 'yith-woocommerce-request-a-quote' ),
														'std'   => '',
														'name' => 'yit_metaboxes[_ywraq_request_response]',
														'class' => 'wcfm-textarea',
														'label_class' => 'wcfm_title',
														'desc_class' => 'wcfm_page_options_desc',
														'value' => $request_response
													),
												
													//@since 1.3.0
													'ywraq_request_response_after' => array(
														'label' => __( 'Attach message to the quote after the table list (optional)', 'yith-woocommerce-request-a-quote' ),
														'type'  => 'textarea',
														'desc'  => __( 'Write a message that will be attached to the quote after the list', 'yith-woocommerce-request-a-quote' ),
														'std'   => '',
														'name' => 'yit_metaboxes[_ywraq_request_response_after]',
														'class' => 'wcfm-textarea',
														'label_class' => 'wcfm_title',
														'desc_class' => 'wcfm_page_options_desc',
														'value' => $request_response_after
													),
												
													//@since 1.3.0
													'ywraq_optional_attachment'    => array(
														'label' => __( 'Optional Attachment', 'yith-woocommerce-request-a-quote' ),
														'type'  => 'upload',
														'desc'  => __( 'Use this field to add additional attachment to the email', 'yith-woocommerce-request-a-quote' ),
														'std'   => '',
														'name' => 'yit_metaboxes[_ywraq_optional_attachment]',
														'wcfm_uploader_by_url' => true,
														'class' => 'wcfm-text',
														'label_class' => 'wcfm_title',
														'desc_class' => 'wcfm_page_options_desc',
														'value'   => $optional_attachment 
													),
												
													'ywcm_request_expire' => array(
														'label' => __( 'Expire date (optional)', 'yith-woocommerce-request-a-quote' ),
														'desc'  => __( 'Set an expiration date for this quote', 'yith-woocommerce-request-a-quote' ),
														'type'  => 'datepicker',
														'std'   => apply_filters( 'ywraq_set_default_expire_date', '' ),
														'name' => 'yit_metaboxes[_ywcm_request_expire]',
														'class' => 'wcfm-text',
														'label_class' => 'wcfm_title',
														'desc_class' => 'wcfm_page_options_desc',
														'custom_attributes' => array( 'date_format' => 'yy-mm-dd' ),
														'value' => $request_expire
													),
													
													'ywraq_customer_sep2' => array(
														'value'  => '<br /><hr /><br />',
														'type'  => 'html'
													),
												
													//@since 1.6.3
													'ywraq_pay_quote_now'    => array(
														'label' => __( 'Send the customer to "Pay for Quote"', 'yith-woocommerce-request-a-quote' ),
														'type'  => 'checkbox',
														'desc'  => __( 'If billing and shipping fields are filled, you can send the customer to Pay for Quote Page. In this page, neither billing nor shipping information will be requested.', 'yith-woocommerce-request-a-quote' ),
														'dfvalue'   =>  apply_filters( 'ywraq_set_default_pay_quote_now', 'no' ),
														'name' => 'yit_metaboxes[_ywraq_pay_quote_now]',
														'class' => 'wcfm-checkbox',
														'label_class' => 'wcfm_title checkbox_title checkbox-title',
														'value' => 'yes',
														'desc_class' => 'wcfm_page_options_desc'
													),
												
													//@since 1.6.3
													'ywraq_checkout_info'    => array(
														'label' => __( 'Override checkout fields', 'yith-woocommerce-request-a-quote' ),
														'type'  => 'select',
														'desc'  => __( 'Select an option if you want to override checkout fields.', 'yith-woocommerce-request-a-quote' ),
														'value'   => '',
														'options' => array(
															'' => __('Do not override Billing and Shipping Info', 'yith-woocommerce-request-a-quote'),
															'both' => __('Override Billing and Shipping Info', 'yith-woocommerce-request-a-quote'),
															'billing' => __('Override Billing Info', 'yith-woocommerce-request-a-quote'),
															'shipping' => __('Override Shipping Info', 'yith-woocommerce-request-a-quote'),
														),
													  'name' => 'yit_metaboxes[_ywraq_checkout_info]',
														'class' => 'wcfm-select',
														'label_class' => 'wcfm_title',
														'desc_class' => 'wcfm_page_options_desc',
														'value' => $ywraq_checkout_info
													),
												
													//@since 1.6.3
													'ywraq_lock_editing'    => array(
														'label' => __( 'Lock the editing of fields selected above', 'yith-woocommerce-request-a-quote' ),
														'type'  => 'checkbox',
														'desc'  => __( 'Check this option if you want to disable the editing of the checkout fields.', 'yith-woocommerce-request-a-quote' ),
														'dfvalue'   => 'no',
														'name' => 'yit_metaboxes[_ywraq_lock_editing]',
														'class' => 'wcfm-checkbox',
														'label_class' => 'wcfm_title checkbox_title checkbox-title',
														'value' => 'yes',
														'desc_class' => 'wcfm_page_options_desc'
													),
												
												
													//@since 1.6.3
													'ywraq_disable_shipping_method'    => array(
														'label' => __( 'Override shipping', 'yith-woocommerce-request-a-quote' ),
														'type'  => 'checkbox',
														'desc'  => __( 'Check this option if you want to use only the shipping method in the quote.', 'yith-woocommerce-request-a-quote' ),
														'dfvalue'   => apply_filters('override_shipping_option_default_value','no'),
														'name' => 'yit_metaboxes[_ywraq_disable_shipping_method]',
														'class' => 'wcfm-checkbox',
														'label_class' => 'wcfm_title checkbox_title checkbox-title',
														'value' => 'yes',
														'desc_class' => 'wcfm_page_options_desc'
													),
												
													'ywraq_safe_submit_field' => array(
														'desc' => __( 'Set an expiration date for this quote', 'yith-woocommerce-request-a-quote' ),
														'type' => 'hidden',
														'std'  => '',
														'name' => 'yit_metaboxes[ywraq_safe_submit_field]',
														'value'  => ''
													),
												
													'ywraq_raq' => array(
														'desc' => '',
														'type' => 'hidden',
														'private'  => false,
														'std'  => 'no',
														'name' => 'yit_metaboxes[ywraq_raq]',
														'value'  => 'yes'
													),
												);
												
												
					$fields = array_merge( $fields, $group_2  );
					
					$WCFM->wcfm_fields->wcfm_generate_form_field( $fields );
					
					if( $pdf_file ) {
						echo '<a class="wcfm_submit_button" id="ywraq_pdf_preview" target="_blank" href="'.esc_url($pdf_file).'">'.__('View PDF','yith-woocommerce-request-a-quote').'</a>';
					}
					?>
					<div class="wcfm_clearfix wcfm-clearfix"></div>
					<input type="submit" class="wcfm_submit_button" id="yith_quote_reset_response" value="<?php _e('Send Quote','yith-woocommerce-request-a-quote') ?>" />
					<div class="wcfm_clearfix wcfm-clearfix"></div>
				</form>
				<div class="wcfm_clearfix wcfm-clearfix"></div>
			</div>
			<div class="wcfm_clearfix wcfm-clearfix"></div>
		</div>
		<?php
	}
}
?>						