<?php
/**
 * WCFM plugin views
 *
 * Plugin WC Box Office Product manager View
 *
 * @author 		WC Lovers
 * @package 	wcfmu/views
 * @version   3.3.3
 */
global $wp, $WCFM, $WCFMu;

if( !apply_filters( 'wcfm_is_allow_wc_box_office', true ) ) {
	//wcfm_restriction_message_show( "Appointments" );
	return;
}

$ticket_field_values = array();
$ticket_field_default_values = array( array(
																						'label'          => __( 'First Name', 'woocommerce-box-office' ),
																						'type'           => 'first_name',
																						'options'        => '',
																						'autofill'       => 'none',
																						'email_contact'  => 'yes',
																						'email_gravatar' => 'yes',
																						'required'       => 'yes',
																						),
																			array(
																						'label'          => __( 'Last Name', 'woocommerce-box-office' ),
																						'type'           => 'last_name',
																						'options'        => '',
																						'autofill'       => 'none',
																						'email_contact'  => 'yes',
																						'email_gravatar' => 'yes',
																						'required'       => 'yes',
																						),
																			array(
																						'label'          => __( 'Email address', 'woocommerce-box-office' ),
																						'type'           => 'email',
																						'options'        => '',
																						'autofill'       => 'none',
																						'email_contact'  => 'yes',
																						'email_gravatar' => 'yes',
																						'required'       => 'yes',
																						)
																			);
																						
									
$ticket_field_values = $ticket_field_default_values;

$_print_tickets = 'no';
$_print_barcode = 'no';
$_ticket_content = '<h1>{post_title}</h1>
										<h2>Attendee Detail</h2>
										<table class="td" style="width: 100%; font-family: \'Helvetica Neue\', Helvetica, Roboto, Arial, sans-serif;" border="1" cellspacing="0" cellpadding="6">
										<tbody>
										<tr>
										<td class="td" style="text-align: left; vertical-align: middle; border: 1px solid #eee; font-family: \'Helvetica Neue\', Helvetica, Roboto, Arial, sans-serif; word-wrap: break-word;"><strong>First Name</strong></td>
										<td class="td" style="text-align: left; vertical-align: middle; border: 1px solid #eee; font-family: \'Helvetica Neue\', Helvetica, Roboto, Arial, sans-serif; word-wrap: break-word;">{First Name}</td>
										</tr>
										<tr>
										<td class="td" style="text-align: left; vertical-align: middle; border: 1px solid #eee; font-family: \'Helvetica Neue\', Helvetica, Roboto, Arial, sans-serif; word-wrap: break-word;"><strong>Last Name</strong></td>
										<td class="td" style="text-align: left; vertical-align: middle; border: 1px solid #eee; font-family: \'Helvetica Neue\', Helvetica, Roboto, Arial, sans-serif; word-wrap: break-word;">{Last Name}</td>
										</tr>
										<tr>
										<td class="td" style="text-align: left; vertical-align: middle; border: 1px solid #eee; font-family: \'Helvetica Neue\', Helvetica, Roboto, Arial, sans-serif; word-wrap: break-word;"><strong>Email</strong></td>
										<td class="td" style="text-align: left; vertical-align: middle; border: 1px solid #eee; font-family: \'Helvetica Neue\', Helvetica, Roboto, Arial, sans-serif; word-wrap: break-word;">{Email}</td>
										</tr>
										</tbody>
										</table>
										{post_content}';
										
$_email_tickets = 'no';
$_email_ticket_subject = '';
$_ticket_email_html =  'Hi there!

												Thank you so much for purchasing a ticket and hope to see you soon at our event. You can edit your information at any time before the event, by visiting the following link:
												
												{ticket_link}
												
												Let us know if you have any questions!';


$product_id = 0;

if( isset( $wp->query_vars['wcfm-products-manage'] ) && !empty( $wp->query_vars['wcfm-products-manage'] ) ) {
	$product_id = $wp->query_vars['wcfm-products-manage'];
	
	$ticket_fields = get_post_meta( $product_id, '_ticket_fields', true );
	
	if( $ticket_fields && is_array( $ticket_fields ) && !empty( $ticket_fields ) ) {
		$ticket_field_values = $ticket_fields;
	}
	
	$_print_tickets  = get_post_meta( $product_id, '_print_tickets', true ) ? 'yes' : 'no';
	$_print_barcode  = get_post_meta( $product_id, '_print_barcode', true ) ? 'yes' : 'no';
	$_ticket_content = get_post_meta( $product_id, '_ticket_content', true );
	
	$_email_tickets = get_post_meta( $product_id, '_email_tickets', true ) ? 'yes' : 'no';
	$_email_ticket_subject = get_post_meta( $product_id, '_email_ticket_subject', true );
	$_ticket_email_html = get_post_meta( $product_id, '_ticket_email_html', true );
}

$field_types      = wc_box_office_ticket_field_types();
$autofill_options = wc_box_office_autofill_options();

?>

<?php if( apply_filters( 'wcfm_is_allow_wc_box_office_ticket', true ) ) { ?>
	<div class="page_collapsible products_manage_wc_box_office_ticket_fields simple variable wc-box-office-ticket" id="wcfm_products_manage_form_wc_box_office_ticket_fields_head"><label class="wcfmfa fa-list"></label><?php _e( 'Ticket Fields', 'woocommerce-box-office' ); ?><span></span></div>
	<div class="wcfm-container simple variable wc-box-office-ticket">
		<div id="wcfm_products_manage_form_wc_box_office_ticket_fields_expander" class="wcfm-content">
			<?php
				$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_wc_box_office_fields_ticket', array(  
							"_ticket_fields"  => array('label' => __( 'Ticket Fields', 'woocommerce-box-office' ) , 'type' => 'multiinput', 'class' => 'wcfm-text', 'label_class' => 'wcfm_title', 'value' => $ticket_field_values, 'options' => array(
																											"label" => array('label' => __( 'Label', 'woocommerce-box-office' ), 'type' => 'text', 'class' => 'wcfm-text wcfm_half_ele', 'label_class' => 'wcfm_title wcfm_half_ele_title', 'hints' => __( 'The field label as it is shown to the user.', 'woocommerce-box-office' ) ),
																											"type" => array('label' => __( 'Type', 'woocommerce-box-office' ), 'type' => 'select', 'options' => $field_types, 'class' => 'wcfm-select ticket_type_field wcfm_half_ele', 'label_class' => 'wcfm_title wcfm_half_ele_title' ),
																											"autofill" => array('label' => __( 'Auto-fill', 'woocommerce-box-office' ), 'type' => 'select', 'options' => array_merge( array( 'none' => __( 'None', 'woocommerce-box-office' ) ), $autofill_options ), 'class' => 'wcfm-select wcfm_half_ele', 'label_class' => 'wcfm_title wcfm_half_ele_title', 'hints' => __( 'Choose the customer\'s billing field from which data is auto-filled as well as what options are available for applicable field types.', 'woocommerce-box-office' ) ),
																											"required" => array('label' => __('Required', 'woocommerce-box-office'), 'type' => 'select', 'options' => array( 'yes' => __( 'Yes', 'woocommerce-box-office' ), 'no' => __( 'No', 'woocommerce-box-office' ) ), 'class' => 'wcfm-select wcfm_half_ele', 'label_class' => 'wcfm_title wcfm_half_ele_title' ),
																											"email_contact" => array('label' => __('Contact', 'woocommerce-box-office'), 'type' => 'select', 'options' => array( 'yes' => __( 'Yes', 'woocommerce-box-office' ), 'no' => __( 'No', 'woocommerce-box-office' ) ), 'class' => 'wcfm-select ticket_email_field_options wcfm_half_ele', 'label_class' => 'wcfm_title ticket_email_field_options wcfm_half_ele_title', 'hints' => __( 'Use this email address to contact the ticket holder.', 'woocommerce-box-office' ) ),
																											"email_gravatar" => array('label' => __('Gravatar', 'woocommerce-box-office'), 'type' => 'select', 'options' => array( 'yes' => __( 'Yes', 'woocommerce-box-office' ), 'no' => __( 'No', 'woocommerce-box-office' ) ), 'class' => 'wcfm-select ticket_email_field_options wcfm_half_ele', 'label_class' => 'wcfm_title ticket_email_field_options wcfm_half_ele_title', 'hints' => __( 'Use this email address for the ticket holder\'s gravatar.', 'woocommerce-box-office' ) ),
																											"options" => array( 'label' => __( 'Options', 'woocommerce-box-office' ), 'type' => 'textarea', 'class' => 'wcfm-textarea ticket_additional_field_options wcfm_full_ele', 'label_class' => 'wcfm_title ticket_additional_field_options wcfm_full_ele', 'placeholder' => __( 'Comma-separated list of available options', 'woocommerce-box-office' ) ),
																											)	)
																											
																										) ) );
			?>
		</div>
	</div>
<?php } ?>

<?php if( apply_filters( 'wcfm_is_allow_wc_box_office_ticket_printing', true ) ) { ?>
	<div class="page_collapsible products_manage_wc_box_office_ticket_fields simple variable wc-box-office-ticket" id="wcfm_products_manage_form_wc_box_office_ticket_fields_head"><label class="wcfmfa fa-print"></label><?php _e( 'Ticket Printing', 'woocommerce-box-office' ); ?><span></span></div>
	<div class="wcfm-container simple variable wc-box-office-ticket">
		<div id="wcfm_products_manage_form_wc_box_office_ticket_fields_expander" class="wcfm-content">
			<?php
				$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_wc_box_office_fields_ticket_printing', array(  
																											"_print_tickets" => array('label' => __( 'Enable ticket printing', 'woocommerce-box-office' ), 'type' => 'checkbox', 'class' => 'wcfm-checkbox', 'label_class' => 'wcfm_title checkbox_title', 'hints' => __( 'This will enable the \'Print ticket\' button on the ticket edit page.', 'woocommerce-box-office' ), 'value' => 'yes', 'dfvalue' => $_print_tickets ),
																											"_print_barcode" => array('label' => __( 'Include barcode', 'woocommerce-box-office' ), 'type' => 'checkbox', 'class' => 'wcfm-checkbox', 'label_class' => 'wcfm_title checkbox_title', 'hints' => __( 'This will add the unique ticket barcode to the bottom of the ticket.', 'woocommerce-box-office' ), 'value' => 'yes', 'dfvalue' => $_print_barcode ),
																										) ) );
			?>
			<div class="wcfm_clearfix"></div><br />
			<div class="options_group">
				<p><?php _e( 'This is the content that will be shown on each printed ticket.', 'woocommerce-box-office' ); ?></p>
				<p class="ticket-label-variables-info">
					<?php _e( 'Add ticket fields to the content by using following labels: ', 'woocommerce-box-office' ); ?>
					<span class="ticket-label-variables"></span>
				</p>
				<p>
					<?php _e( 'You can also use this ticket product variables: ', 'woocommerce-box-office' ); ?>
					<span class="ticket-post-vars">
						<code>{post_title}</code>
						<code>{post_content}</code>
					</span>
				</p>
			</div>
			<div class="wcfm_clearfix"></div><br />
			<?php
				$rich_editor = apply_filters( 'wcfm_is_allow_rich_editor', 'rich_editor' );
				$wpeditor = apply_filters( 'wcfm_is_allow_product_wpeditor', 'wpeditor' );
				if( $wpeditor && $rich_editor ) {
					$rich_editor = 'wcfm_wpeditor';
				} else {
					$wpeditor = 'textarea';
				}
				$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_wc_box_office_fields_ticket_printing_content', array(  
																											"_ticket_content" => array( 'type' => $wpeditor, 'class' => 'wcfm-textarea wcfm_full_ele ' . $rich_editor, 'value' => $_ticket_content ),
																										) ) );
			?>
		</div>
	</div>
<?php } ?>

<?php if( apply_filters( 'wcfm_is_allow_wc_box_office_ticket_email', true ) ) { ?>
	<div class="page_collapsible products_manage_wc_box_office_ticket_fields simple variable wc-box-office-ticket" id="wcfm_products_manage_form_wc_box_office_ticket_fields_head"><label class="wcfmfa fa-envelope"></label><?php _e( 'Ticket Emails', 'woocommerce-box-office' ); ?><span></span></div>
	<div class="wcfm-container simple variable wc-box-office-ticket">
		<div id="wcfm_products_manage_form_wc_box_office_ticket_fields_expander" class="wcfm-content">
			<?php
				$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_wc_box_office_fields_ticket_email', array(  
																											"_email_tickets" => array('label' => __( 'Enable ticket emails', 'woocommerce-box-office' ), 'type' => 'checkbox', 'class' => 'wcfm-checkbox', 'label_class' => 'wcfm_title checkbox_title', 'hints' => __( 'This will send an email to the contact address for each ticket whenever it is changed.', 'woocommerce-box-office' ), 'value' => 'yes', 'dfvalue' => $_email_tickets ),
																											"_email_ticket_subject" => array('label' => __( 'Email subject', 'woocommerce-box-office' ), 'type' => 'text', 'class' => 'wcfm-text', 'label_class' => 'wcfm_title', 'hints' => sprintf( __( 'Add ticket fields to the subject by inserting the field label like this: %1$s<br>e.g. %2$s', 'woocommerce-box-office' ), '<code>{Label}</code>', '<code>{First Name}</code>' ), 'value' => $_email_ticket_subject ),
																										) ) );
			?>
			<div class="wcfm_clearfix"></div><br />
			<div class="options_group">
				<p class="ticket_email"><?php _e( 'This is the content that will make up each email.', 'woocommerce-box-office' ); ?>
				</p>
				<p class="ticket-label-variables-info">
					<?php _e( 'Add ticket fields to the content by using following labels: ', 'woocommerce-box-office' ); ?>
					<span class="ticket-label-variables"></span>
				</p>
				<p>
					<?php _e( 'To insert ticket link use: ', 'woocommerce-box-office' ); ?>
					<span class="ticket-link-var">
						<code>{ticket_link}</code>
					</span>
				</p>
				<p>
					<?php _e( 'To insert ticket token use: ', 'woocommerce-box-office' ); ?>
					<span class="ticket-token-var">
						<code>{token}</code>
					</span>
					<?php _e( 'Ticket token can be used to build private content link, e.g. <code>http://example.com/private?token={token}</code>', 'woocommerce-box-office' ); ?>
				</p>
				<p>
					<?php _e( 'You can also use this ticket product variables: ', 'woocommerce-box-office' ); ?>
					<span class="ticket-post-vars">
						<code>{post_title}</code>
						<code>{post_content}</code>
					</span>
				</p>
			</div>
			<div class="wcfm_clearfix"></div><br />
			<?php
				$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_wc_box_office_fields_ticket_email_content', array(  
																											"_ticket_email_html" => array( 'type' => $wpeditor, 'class' => 'wcfm-textarea wcfm_full_ele ' . $rich_editor, 'value' => $_ticket_email_html ),
																										) ) );
			?>
		</div>
	</div>
<?php } ?>