<?php
/**
 * WCFM plugin view
 *
 * WCFM WC PDF Vouchers Product Manage View
 *
 * @author 		WC Lovers
 * @package 	wcfmu/views/thirdparty
 * @version   4.0.0
 */
 
global $wp, $WCFM, $WCFMu, $woo_vou_voucher, $woo_vou_model;

if( !apply_filters( 'wcfm_is_allow_wc_pdf_vouchers', true ) ) {
	return;
}

$product_id = '';
$_woo_vou_enable = '';

$_woo_vou_voucher_price = '';

$_woo_vou_product_start_date = '';
$_woo_vou_product_exp_date = '';

$_woo_vou_voucher_delivery = '';
$_woo_vou_enable_pdf_preview = '';
$_woo_vou_enable_coupon_code = '';
$_woo_vou_enable_multiple_pdf = '';
$_woo_vou_using_type = '';
$_woo_vou_codes = '';

$_woo_vou_enable_recipient_name = '';
$_woo_vou_recipient_name_label = '';
$_woo_vou_recipient_name_max_length = '';
$_woo_vou_recipient_name_is_required = '';
$_woo_vou_recipient_name_desc = '';

$_woo_vou_enable_recipient_email = '';
$_woo_vou_recipient_email_label = '';
$_woo_vou_recipient_email_is_required = '';
$_woo_vou_recipient_email_desc = '';

$_woo_vou_enable_recipient_message = '';
$_woo_vou_recipient_message_label = '';
$_woo_vou_recipient_message_max_length = '';
$_woo_vou_recipient_message_is_required = '';
$_woo_vou_recipient_message_desc = '';

$_woo_vou_enable_recipient_giftdate = '';
$_woo_vou_recipient_giftdate_label = '';
$_woo_vou_recipient_giftdate_is_required = '';
$_woo_vou_recipient_giftdate_desc = '';

$_woo_vou_enable_recipient_delivery_method = '';
$_woo_vou_recipient_delivery_label = '';
$_woo_vou_recipient_delivery_email_enable = 'yes';
$_woo_vou_recipient_delivery_email_label = '';
$_woo_vou_recipient_delivery_email_desc = '';
$_woo_vou_recipient_delivery_email_recipient = array();
$_woo_vou_recipient_delivery_offline_enable = '';
$_woo_vou_recipient_delivery_offline_label = '';
$_woo_vou_recipient_delivery_offline_desc = '';
$_woo_vou_recipient_delivery_offline_recipient = array();

$_woo_vou_enable_pdf_template_selection = '';
$_woo_vou_pdf_template_selection_label = '';
$_woo_vou_pdf_template_selection = array();
$_woo_vou_pdf_selection_desc = '';
$_woo_vou_pdf_template = '';

$_woo_vou_vendor_user = '';
$_woo_vou_sec_vendor_users = array();

$_woo_vou_exp_type = 'specific_date';
$_woo_vou_start_date = '';
$_woo_vou_exp_date = '';
$_woo_vou_days_diff = '';
$_woo_vou_custom_days = '';

$_woo_vou_disable_redeem_day = array();
$_woo_vou_logo = '';
$_woo_vou_address_phone = '';
$_woo_vou_website = '';
$_woo_vou_how_to_use = '';

$avail_locations = array();

if( isset( $wp->query_vars['wcfm-products-manage'] ) && !empty( $wp->query_vars['wcfm-products-manage'] ) ) {
	$product_id = $wp->query_vars['wcfm-products-manage'];
	if( $product_id ) {
		$_woo_vou_enable = get_post_meta( $product_id, '_woo_vou_enable', true );
		
		$_woo_vou_voucher_price = get_post_meta( $product_id, '_woo_vou_voucher_price', true );
		
		$_woo_vou_product_start_date = get_post_meta( $product_id, '_woo_vou_product_start_date', true );
		if( !empty($_woo_vou_product_start_date) ) {
			$_woo_vou_product_start_date = date('Y-m-d H:i',strtotime($_woo_vou_product_start_date));
		} else {
			$_woo_vou_product_start_date = '';
		}
		$_woo_vou_product_exp_date = get_post_meta( $product_id, '_woo_vou_product_exp_date', true );
		if( !empty($_woo_vou_product_exp_date) ) {
			$_woo_vou_product_exp_date = date('Y-m-d H:i',strtotime($_woo_vou_product_exp_date));
		} else {
			$_woo_vou_product_exp_date = '';
		}
		
		$_woo_vou_voucher_delivery = get_post_meta( $product_id, '_woo_vou_voucher_delivery', true );
		$_woo_vou_enable_pdf_preview = get_post_meta( $product_id, '_woo_vou_enable_pdf_preview', true );
		$_woo_vou_enable_coupon_code = get_post_meta( $product_id, '_woo_vou_enable_coupon_code', true );
		$_woo_vou_enable_multiple_pdf = get_post_meta( $product_id, '_woo_vou_enable_multiple_pdf', true );
		$_woo_vou_using_type = get_post_meta( $product_id, '_woo_vou_using_type', true );
		$_woo_vou_codes = get_post_meta( $product_id, '_woo_vou_codes', true );
		
		
		$_woo_vou_enable_recipient_name = get_post_meta( $product_id, '_woo_vou_enable_recipient_name', true );
		$_woo_vou_recipient_name_label = get_post_meta( $product_id, '_woo_vou_recipient_name_label', true );
		$_woo_vou_recipient_name_max_length = get_post_meta( $product_id, '_woo_vou_recipient_name_max_length', true );
		$_woo_vou_recipient_name_is_required = get_post_meta( $product_id, '_woo_vou_recipient_name_is_required', true );
		$_woo_vou_recipient_name_desc = get_post_meta( $product_id, '_woo_vou_recipient_name_desc', true );
		
		$_woo_vou_enable_recipient_email = get_post_meta( $product_id, '_woo_vou_enable_recipient_email', true );
		$_woo_vou_recipient_email_label = get_post_meta( $product_id, '_woo_vou_recipient_email_label', true );
		$_woo_vou_recipient_email_is_required = get_post_meta( $product_id, '_woo_vou_recipient_email_is_required', true );
		$_woo_vou_recipient_email_desc = get_post_meta( $product_id, '_woo_vou_recipient_email_desc', true );
		
		$_woo_vou_enable_recipient_message = get_post_meta( $product_id, '_woo_vou_enable_recipient_message', true );
		$_woo_vou_recipient_message_label = get_post_meta( $product_id, '_woo_vou_recipient_message_label', true );
		$_woo_vou_recipient_message_max_length = get_post_meta( $product_id, '_woo_vou_recipient_message_max_length', true );
		$_woo_vou_recipient_message_is_required = get_post_meta( $product_id, '_woo_vou_recipient_message_is_required', true );
		$_woo_vou_recipient_message_desc = get_post_meta( $product_id, '_woo_vou_recipient_message_desc', true );
		
		$_woo_vou_enable_recipient_giftdate = get_post_meta( $product_id, '_woo_vou_enable_recipient_giftdate', true );
		$_woo_vou_recipient_giftdate_label = get_post_meta( $product_id, '_woo_vou_recipient_giftdate_label', true );
		$_woo_vou_recipient_giftdate_is_required = get_post_meta( $product_id, '_woo_vou_recipient_giftdate_is_required', true );
		$_woo_vou_recipient_giftdate_desc = get_post_meta( $product_id, '_woo_vou_recipient_giftdate_desc', true );
		
		
		$_woo_vou_enable_recipient_delivery_method = get_post_meta( $product_id, '_woo_vou_enable_recipient_delivery_method', true );
		$_woo_vou_recipient_delivery_label = get_post_meta( $product_id, '_woo_vou_recipient_delivery_label', true );
		
		$_woo_vou_recipient_delivery = (array) get_post_meta( $product_id, '_woo_vou_recipient_delivery', true );
		$_woo_vou_recipient_delivery_email_enable = isset( $_woo_vou_recipient_delivery['enable_email'] ) ? $_woo_vou_recipient_delivery['enable_email'] : 'yes';
		$_woo_vou_recipient_delivery_email_label = isset( $_woo_vou_recipient_delivery['label_email'] ) ? $_woo_vou_recipient_delivery['label_email'] : '';
		$_woo_vou_recipient_delivery_email_desc = isset( $_woo_vou_recipient_delivery['desc_email'] ) ? $_woo_vou_recipient_delivery['desc_email'] : '';
		$_woo_vou_recipient_delivery_email_recipient = isset( $_woo_vou_recipient_delivery['email'] ) ? $_woo_vou_recipient_delivery['email'] : array();
		$_woo_vou_recipient_delivery_offline_enable = isset( $_woo_vou_recipient_delivery['enable_offline'] ) ? $_woo_vou_recipient_delivery['enable_offline'] : 'no';
		$_woo_vou_recipient_delivery_offline_label = isset( $_woo_vou_recipient_delivery['label_offline'] ) ? $_woo_vou_recipient_delivery['label_offline'] : '';
		$_woo_vou_recipient_delivery_offline_desc = isset( $_woo_vou_recipient_delivery['desc_offline'] ) ? $_woo_vou_recipient_delivery['desc_offline'] : '';
		$_woo_vou_recipient_delivery_offline_recipient = isset( $_woo_vou_recipient_delivery['offline'] ) ? $_woo_vou_recipient_delivery['offline'] : array();
		
		$_woo_vou_enable_pdf_template_selection = get_post_meta( $product_id, '_woo_vou_enable_pdf_template_selection', true );
		$_woo_vou_pdf_template_selection_label = get_post_meta( $product_id, '_woo_vou_pdf_template_selection_label', true );
		$_woo_vou_pdf_template_selection = get_post_meta( $product_id, '_woo_vou_pdf_template_selection', true );
		$_woo_vou_pdf_selection_desc = get_post_meta( $product_id, '_woo_vou_pdf_selection_desc', true );
		if( !$_woo_vou_pdf_template_selection ) $_woo_vou_pdf_template_selection = array();
		$_woo_vou_pdf_template = get_post_meta( $product_id, '_woo_vou_pdf_template', true );
		
		$_woo_vou_vendor_user = get_post_meta( $product_id, '_woo_vou_vendor_user', true );
		$_woo_vou_sec_vendor_users = get_post_meta( $product_id, '_woo_vou_sec_vendor_users', true );
		if( !$_woo_vou_sec_vendor_users ) $_woo_vou_sec_vendor_users = array();
		
		$_woo_vou_exp_type = get_post_meta( $product_id, '_woo_vou_exp_type', true );
		$_woo_vou_start_date = get_post_meta( $product_id, '_woo_vou_start_date', true );
		if(isset($_woo_vou_start_date) && !empty($_woo_vou_start_date) && !is_array($_woo_vou_start_date)) {
			$_woo_vou_start_date = date('Y-m-d H:i',strtotime($_woo_vou_start_date));
		} else {
			$_woo_vou_start_date = '';
		}
		$_woo_vou_exp_date = get_post_meta( $product_id, '_woo_vou_exp_date', true );
		if(isset($_woo_vou_exp_date) && !empty($_woo_vou_exp_date) && !is_array($_woo_vou_exp_date)) {
			$_woo_vou_exp_date = date('Y-m-d H:i',strtotime($_woo_vou_exp_date));
		} else {
			$_woo_vou_exp_date = '';
		}
		$_woo_vou_days_diff = get_post_meta( $product_id, '_woo_vou_days_diff', true );
		$_woo_vou_custom_days = get_post_meta( $product_id, '_woo_vou_custom_days', true );
		
		$_woo_vou_disable_redeem_day = get_post_meta( $product_id, '_woo_vou_disable_redeem_day', true );
		if( !$_woo_vou_disable_redeem_day ) $_woo_vou_disable_redeem_day = array();
		$_woo_vou_logo = get_post_meta( $product_id, '_woo_vou_logo', true );
		if( $_woo_vou_logo && isset( $_woo_vou_logo['src'] ) ) $_woo_vou_logo = $_woo_vou_logo['src'];
		$_woo_vou_address_phone = get_post_meta( $product_id, '_woo_vou_address_phone', true );
		$_woo_vou_website = get_post_meta( $product_id, '_woo_vou_website', true );
		$_woo_vou_how_to_use = get_post_meta( $product_id, '_woo_vou_how_to_use', true );
		
		$avail_locations = get_post_meta( $product_id, '_woo_vou_avail_locations', true );
		if( !$avail_locations ) $avail_locations = array();
	}
}

$recipient_details 		= woo_vou_voucher_recipient_details();
$_recipient_details = array();
if( !empty( $recipient_details ) ) {
	foreach( $recipient_details as $recipient_key => $recipient_val ) {
		if( is_array( $recipient_val ) && array_key_exists( 'label', $recipient_val ) ) {
			$_recipient_details[$recipient_key] = $recipient_val['label'];
		}
	}
}

$based_on_purchase_opt  = array(
																'7' 		=> '7 Days',
																'15' 		=> '15 Days',
																'30' 		=> '1 Month (30 Days)',
																'90' 		=> '3 Months (90 Days)',
																'180' 		=> '6 Months (180 Days)',
																'365' 		=> '1 Year (365 Days)',
																'cust'		=> 'Custom',
															);
		
$using_type_opt 		= array(
														'' 	=> __( 'Default', 'woovoucher' ), 
														'0' => __( 'One time only', 'woovoucher' ), 
														'1' => __( 'Unlimited', 'woovoucher' )
													);					

$voucher_delivery_opt 	= array(
																'default' 	=> __( 'Default', 'woovoucher' ), 
																'email' 	=> __( 'Email', 'woovoucher' ), 
																'offline' 	=> __( 'Offline', 'woovoucher' )
															);
$voucher_preview_opt = $multiple_pdf_opt = $coupon_code_opt = array(
																																		'' 		=> __( 'Default', 'woovoucher' ), 
																																		'yes' 	=> __( 'Yes', 'woovoucher' ), 
																																		'no' 	=> __( 'No', 'woovoucher' )
																																		);

$redeem_days = array( 
				'Monday' => __( 'Monday', 'woovoucher' ), 
				'Tuesday' => __( 'Tuesday', 'woovoucher' ), 
				'Wednesday' => __( 'Wednesday', 'woovoucher' ),
				'Thursday' => __( 'Thursday', 'woovoucher' ), 
				'Friday' => __( 'Friday', 'woovoucher' ),
				'Saturday' => __( 'Saturday', 'woovoucher' ),
				'Sunday' => __( 'Sunday', 'woovoucher' )
			);

$expdate_types = apply_filters('woo_vou_exp_date_types', array( 'default' => __( 'Default', 'woovoucher' ), 'specific_date' => __( 'Specific Time', 'woovoucher' ), 'based_on_purchase' => __( 'Based on Purchase', 'woovoucher' ) ));

$voucher_options 	= array( '' => __( 'Please Select', 'woovoucher' ) );
$multiple_voucher_options = array();
$voucher_data 		= woo_vou_get_vouchers();
foreach ( $voucher_data as $voucher ) {
	if( isset( $voucher['ID'] ) && !empty( $voucher['ID'] ) ) { // Check voucher id is not empty
		$voucher_options[$voucher['ID']] = $voucher['post_title'];
		$multiple_voucher_options[$voucher['ID']] = $voucher['post_title'];
	}
}

$vendor_options = $WCFM->wcfm_vendor_support->wcfm_get_vendor_list();
$vendor_user_ele_class = '';
if( wcfm_is_vendor() ) {
	$vendor_options = array();
	$_woo_vou_vendor_user = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );
	$vendor_options = array( $_woo_vou_vendor_user => 'ME' );
	$vendor_user_ele_class = 'wcfm_ele_hide';
}

$woo_vou_pro_start_end_date_format = apply_filters( 'woo_vou_pro_start_end_date_format', 'yy-mm-dd' );
$woo_vou_vou_start_end_date_format = apply_filters( 'woo_vou_vou_start_end_date_format', 'yy-mm-dd' );
?>

<div class="page_collapsible products_manage_wc_pdf_vouchers simple variable downlodable" id="wcfm_products_manage_form_wc_pdf_vouchers_head"><label class="wcfmfa fa-paw"></label><?php _e('PDF Vouchers', 'wc-frontend-manager-ultimate'); ?><span></span></div>
<div class="wcfm-container simple variable downlodable">
	<div id="wcfm_products_manage_form_wc_pdf_vouchers_expander" class="wcfm-content">
		<?php
		$woo_vou_fields = apply_filters( 'wcfm_product_manage_fields_wc_pdf_vouchers', array(  
																																												"_woo_vou_heading_1" => array( 'type' => 'html', 'value' => '<h2>' . __( 'Voucher General Setting', 'wc-frontend-manager-ultimate' ) . '</h2><div class="wcfm_clearfix"></div>' ),
																																												
																																												"_woo_vou_enable" => array( 'label' => __( 'Enable Voucher Codes:', 'woovoucher' ) , 'type' => 'checkbox', 'class' => 'wcfm-checkbox', 'label_class' => 'wcfm_title checkbox_title', 'hints' => __( 'To enable the voucher for this product check the "Enable Voucher Codes" check box.', 'woovoucher' ), 'dfvalue' => $_woo_vou_enable, 'value' => 'yes' ),
																																												
																																												"_woo_vou_voucher_price" => array( 'label' => __( 'Voucher price', 'woovoucher' ) . '(' . get_woocommerce_currency_symbol() . ')', 'type' => 'number', 'class' => 'wcfm-text wcfm_non_negative_input', 'label_class' => 'wcfm_title', 'value' => $_woo_vou_voucher_price ),
																																												
																																												"_woo_vou_product_start_date" => array( 'label' => __( 'Product Start Date:', 'woovoucher' ), 'type' => 'text', 'class' => 'wcfm-text', 'label_class' => 'wcfm_title', 'hints' => __( 'If you want to make the product valid for a specific time only, you can enter an start date here.', 'woovoucher' ), 'custom_attributes' => array( 'date_format' => $woo_vou_pro_start_end_date_format ), 'value' => $_woo_vou_product_start_date ),
																																												"_woo_vou_product_exp_date" => array( 'label' => __( 'Product End Date:', 'woovoucher' ), 'type' => 'text', 'class' => 'wcfm-text', 'label_class' => 'wcfm_title', 'hints' => __( 'If you want to make the product valid for a specific time only, you can enter an end date here.', 'woovoucher' ), 'custom_attributes' => array( 'date_format' => $woo_vou_pro_start_end_date_format ), 'value' => $_woo_vou_product_exp_date ),
																																												"_break_ele2" => array( 'type' => 'html', 'value' => '<div class="wcfm-clearfix"></div>' ),
																																												
																																												"_woo_vou_voucher_delivery" => array( 'label' => __( 'Voucher Delivery:', 'woovoucher' ), 'type' => 'select', 'class' => 'wcfm-select ', 'label_class' => 'wcfm_title ', 'desc_class' => 'wc_pdf_vouchers_desc', 'desc' => sprintf( __( 'Choose how your customer receives the "PDF Voucher" %sEmail%s - Customer receives "PDF Voucher" through email. %sOffline%s - You will have to send voucher through physical mode, via post or on-shop. %sThis setting modifies the global voucher delivery setting and overrides voucher\'s delivery value. Set delivery "%sDefault%s" to use the global/voucher settings.', 'woovoucher' ), '<br /><b>', '</b>', '<br /><b>', '</b>', '<br />', '<b>', '</b>' ), 'options' => $voucher_delivery_opt, 'value' => $_woo_vou_voucher_delivery ),
																																												"_break_ele3" => array( 'type' => 'html', 'value' => '<div class="wcfm-clearfix"></div>' ),
																																												
																																												"_woo_vou_enable_pdf_preview" => array( 'label' => __( 'Enable Voucher Preview:', 'woovoucher' ), 'type' => 'select', 'class' => 'wcfm-select ', 'label_class' => 'wcfm_title ', 'desc_class' => 'wc_pdf_vouchers_desc', 'desc' => __( 'Choose Yes / No to allow / disallow users to preview the voucher on product detail page before placing the order. Leave it empty to use global settings.', 'woovoucher' ), 'options' => $voucher_preview_opt, 'value' => $_woo_vou_enable_pdf_preview ),
																																												"_break_ele4" => array( 'type' => 'html', 'value' => '<div class="wcfm-clearfix"></div>' ),
																																												
																																												//"_woo_vou_enable_coupon_code" => array( 'label' => __( 'Auto Coupon Code Generation:', 'woovoucher' ), 'type' => 'select', 'class' => 'wcfm-select ', 'label_class' => 'wcfm_title ', 'desc_class' => 'wc_pdf_vouchers_desc', 'desc' => __( 'Choose Yes / No to allow / disallow coupon code generation when a voucher code gets generated. This will allow you to use voucher codes on online store. Leave it empty to use global settings.', 'woovoucher' ), 'options' => $coupon_code_opt, 'value' => $_woo_vou_enable_coupon_code ),
																																												//"_break_ele5" => array( 'type' => 'html', 'value' => '<div class="wcfm-clearfix"></div>' ),
																																												
																																												"_woo_vou_enable_multiple_pdf" => array( 'label' => __( 'Enable 1 voucher per PDF:', 'woovoucher' ), 'type' => 'select', 'class' => 'wcfm-select ', 'label_class' => 'wcfm_title ', 'desc_class' => 'wc_pdf_vouchers_desc', 'desc' => __( 'Choose Yes if you want to generate 1 PDF for 1 voucher code, choose No if you want to generate 1 combined PDF for all vouchers, choose Default to use global settings.', 'woovoucher' ), 'options' => $multiple_pdf_opt, 'value' => $_woo_vou_enable_multiple_pdf ),
																																												"_break_ele6" => array( 'type' => 'html', 'value' => '<div class="wcfm-clearfix"></div>' ),
																																												
																																												"_woo_vou_using_type" => array( 'label' => __( 'Usability:', 'woovoucher' ), 'type' => 'select', 'class' => 'wcfm-select ', 'label_class' => 'wcfm_title ', 'desc_class' => 'wc_pdf_vouchers_desc', 'desc' => sprintf( __( 'Choose how you wanted to use vouchers codes. %sIf you set usability "%sOne time only%s" then it will automatically set product quantity equal to a number of voucher codes entered and it will automatically decrease quantity  by 1 when it gets purchased. If you set usability "%sUnlimited%s" then the plugin will automatically generate unique voucher codes when the product purchased. %sThis setting modifies the global usability setting and overrides vendor\'s usability value. Set usability "%sDefault%s" to use the global/vendor settings.', 'woovoucher' ), '<br />', '<b>', '</b>', '<b>', '</b>', '<br />', '<b>', '</b>' ), 'options' => $using_type_opt, 'value' => $_woo_vou_using_type ),
																																												"_break_ele7" => array( 'type' => 'html', 'value' => '<div class="wcfm-clearfix"></div>' ),
																																												
																																												"_woo_vou_codes" => array( 'label' => __( 'Voucher Codes:', 'woovoucher' ), 'type' => 'textarea', 'class' => 'wcfm-textarea ', 'label_class' => 'wcfm_title ', 'hints' => __( 'If you have a list of voucher codes you can copy and paste them into this option. Make sure, that they are comma separated.', 'woovoucher' ), 'value' => $_woo_vou_codes ),
																																												"_woo_vou_generate_codes" => array( 'label' => __( 'Generate Codes', 'woovoucher' ), 'label_class' => 'wcfm_title ', 'type' => 'html', 'value' => '<input type="button" class="button wcfm_submit_button wcfm_voucher_code_popup" value="' .  __( 'Generate Codes', 'woovoucher' ) . '" />' ),
																																												"_break_ele8" => array( 'type' => 'html', 'value' => '<div class="wcfm-clearfix"></div>' ),
																																												"_woo_vou_purchased_codes" => array( 'label' => __( 'Purchased Voucher Code:', 'woovoucher' ), 'label_class' => 'wcfm_title woo_vou_purchased_codes_ele wcfm_ele_hide', 'type' => 'html', 'value' => '<input type="button" class="button wcfm_submit_button wcfm_voucher_purchased_code_popup woo_vou_purchased_codes_ele wcfm_ele_hide" value="' .  __( 'Purchased Voucher Codes', 'woovoucher' ) . '" />' ),
																																												"_break_ele8a" => array( 'type' => 'html', 'value' => '<div class="wcfm-clearfix"></div>' ),
																																												"_woo_vou_used_codes" => array( 'label' => __( 'Used Voucher Code:', 'woovoucher' ), 'label_class' => 'wcfm_title woo_vou_used_codes_ele wcfm_ele_hide', 'type' => 'html', 'value' => '<input type="button" class="button wcfm_submit_button wcfm_voucher_used_codes_popup woo_vou_used_codes_ele wcfm_ele_hide" value="' .  __( 'Used Voucher Codes', 'woovoucher' ) . '" />' ),
																																												"_break_ele8b" => array( 'type' => 'html', 'value' => '<div class="wcfm-clearfix"></div>' ),
																																												
																																												"_woo_vou_exp_type" => array( 'label' => __( 'Expiration Date Type:', 'woovoucher' ), 'type' => 'select', 'class' => 'wcfm-select ', 'label_class' => 'wcfm_title ', 'desc_class' => 'wc_pdf_vouchers_desc', 'desc' => sprintf( __( 'Please select expiration date type either a %sSpecific Time%s or set date %sBased on Purchased%s voucher date like after 7 days, 30 days, 1 year etc. %sThis setting modifies the global voucher expiration date setting and overrides voucher\'s expiration date value. Set expiration date type "%sDefault%s" to use the global/voucher settings.', 'woovoucher' ), '<b>', '</b>', '<b>', '</b>','<br />', '<b>', '</b>' ), 'options' => $expdate_types, 'value' => $_woo_vou_exp_type ),
																																												"_break_ele9" => array( 'type' => 'html', 'value' => '<div class="wcfm-clearfix"></div>' ),
																																												
																																												"_woo_vou_start_date" => array( 'label' => __( 'Start Date:', 'woovoucher' ), 'type' => 'text', 'class' => 'wcfm-text specific_date_ele', 'label_class' => 'wcfm_title specific_date_ele', 'hints' => __( 'If you want to make the voucher codes valid for a specific time only, you can enter a start date here.', 'woovoucher' ), 'custom_attributes' => array( 'date_format' => $woo_vou_vou_start_end_date_format ), 'value' => $_woo_vou_start_date ),
																																												"_woo_vou_exp_date" => array( 'label' => __( 'Expiration Date:', 'woovoucher' ), 'type' => 'text', 'class' => 'wcfm-text specific_date_ele', 'label_class' => 'wcfm_title specific_date_ele', 'hints' => __( 'If you want to make the voucher codes valid for a specific time only, you can enter a expiration date here. If the Voucher Code never expires, then leave that option blank.', 'woovoucher' ), 'custom_attributes' => array( 'date_format' => $woo_vou_vou_start_end_date_format ), 'value' => $_woo_vou_exp_date ),
																																												"_woo_vou_days_diff" => array( 'label' => __( 'Expiration Days:', 'woovoucher' ), 'type' => 'select', 'class' => 'wcfm-select based_on_purchase_ele', 'label_class' => 'wcfm_title based_on_purchase_ele', 'desc' => __( ' After purchase', 'woovoucher' ), 'desc_class' => 'woo_vou_days_diff_desc woo_vou_days_diff_custom_non_ele', 'options' => $based_on_purchase_opt, 'value' => $_woo_vou_days_diff ),
																																												"_woo_vou_custom_days" => array( 'type' => 'text', 'class' => 'wcfm-text woo_vou_days_diff_custom_ele', 'label_class' => 'wcfm_title woo_vou_days_diff_custom_ele', 'desc' => __( ' Days after purchase', 'woovoucher' ), 'desc_class' => 'woo_vou_days_diff_desc woo_vou_days_diff_custom_ele', 'value' => $_woo_vou_custom_days ),
																																												
																																												"_woo_vou_disable_redeem_day" => array( 'label' => __( 'Choose Which Days Voucher can not be Used:', 'woovoucher' ), 'type' => 'select', 'class' => 'wcfm-select', 'label_class' => 'wcfm_title', 'attributes' => array( 'multiple' => true ), 'hints' => __( 'If you want to restrict  use of voucher codes  for specific days, you can select days here. Leave it blank for no restriction. ', 'woovoucher' ), 'options' => $redeem_days, 'value' => $_woo_vou_disable_redeem_day ),
																																												
																																												"_woo_vou_heading_2" => array( 'type' => 'html', 'value' => '<h2 style="padding-top:50px;">' . __( 'Gift Voucher Setting', 'wc-frontend-manager-ultimate' ) . '</h2><div class="wcfm_clearfix"></div>' ),
																																												
																																												"_woo_vou_enable_recipient_name" => array( 'label' => __( 'Enable Recipient Name:', 'woovoucher' ) , 'type' => 'checkbox', 'class' => 'wcfm-checkbox', 'label_class' => 'wcfm_title checkbox_title', 'hints' => __( 'To enable the recipient name on the product page.', 'woovoucher' ), 'dfvalue' => $_woo_vou_enable_recipient_name, 'value' => 'yes' ),
																																												"_woo_vou_recipient_name_label" => array( 'label' => '&nbsp;&nbsp;', 'type' => 'text', 'class' => 'wcfm-text _woo_vou_enable_recipient_name_ele', 'label_class' => 'wcfm_title _woo_vou_enable_recipient_name_ele', 'placeholder' => __( 'Label:', 'woovoucher' ), 'value' => $_woo_vou_recipient_name_label ),
																																												"_woo_vou_recipient_name_max_length" => array( 'type' => 'text', 'class' => 'wcfm-text _woo_vou_enable_recipient_name_ele', 'label_class' => 'wcfm_title checkbox_title', 'placeholder' => __( 'Max Length:', 'woovoucher' ), 'value' => $_woo_vou_recipient_name_max_length ),
																																												"_woo_vou_recipient_name_is_required" => array( 'type' => 'checkbox', 'class' => 'wcfm-checkbox _woo_vou_enable_recipient_name_ele', 'label_class' => 'wcfm_title', 'dfvalue' => $_woo_vou_recipient_name_is_required, 'value' => 'yes', 'attributes' => array( 'title' => __( 'Enable to make this required.', 'wc-frontend-manager-ultimate' ) ) ),
																																												"_woo_vou_recipient_name_desc" => array( 'label' => __( 'Description:', 'woovoucher' ), 'type' => 'textarea', 'class' => 'wcfm-textarea _woo_vou_enable_recipient_name_ele', 'label_class' => 'wcfm_title _woo_vou_enable_recipient_name_ele','value' => $_woo_vou_recipient_name_desc, 'hints' => __( 'Enter the description which you want to show on product page.', 'woovoucher' ) ),
																																												"_break_ele_gvs_1" => array( 'type' => 'html', 'value' => '<div class="wcfm-clearfix _woo_vou_enable_recipient_name_ele" style="margin-bottom:25px;"></div>' ),
																																												
																																												"_woo_vou_enable_recipient_email" => array( 'label' => __( 'Enable Recipient Email:', 'woovoucher' ) , 'type' => 'checkbox', 'class' => 'wcfm-checkbox', 'label_class' => 'wcfm_title checkbox_title', 'hints' => __( 'To enable the recipient email on the product page.', 'woovoucher' ), 'dfvalue' => $_woo_vou_enable_recipient_email, 'value' => 'yes' ),
																																												"_woo_vou_recipient_email_label" => array( 'label' => '&nbsp;&nbsp;', 'type' => 'text', 'class' => 'wcfm-text _woo_vou_enable_recipient_email_ele', 'label_class' => 'wcfm_title _woo_vou_enable_recipient_email_ele', 'placeholder' => __( 'Label:', 'woovoucher' ), 'value' => $_woo_vou_recipient_email_label ),
																																												"_woo_vou_recipient_email_is_required" => array( 'type' => 'checkbox', 'class' => 'wcfm-checkbox _woo_vou_enable_recipient_email_ele', 'label_class' => 'wcfm_title', 'dfvalue' => $_woo_vou_recipient_email_is_required, 'value' => 'yes', 'attributes' => array( 'title' => __( 'Enable to make this required.', 'wc-frontend-manager-ultimate' ) ) ),
																																												"_woo_vou_recipient_email_desc" => array( 'label' => __( 'Description:', 'woovoucher' ), 'type' => 'textarea', 'class' => 'wcfm-textarea _woo_vou_enable_recipient_email_ele', 'label_class' => 'wcfm_title _woo_vou_enable_recipient_email_ele','value' => $_woo_vou_recipient_email_desc, 'hints' => __( 'Enter the description which you want to show on product page.', 'woovoucher' ) ),
																																												"_break_ele_gvs_2" => array( 'type' => 'html', 'value' => '<div class="wcfm-clearfix _woo_vou_enable_recipient_email_ele" style="margin-bottom:25px;"></div>' ),
																																												
																																												"_woo_vou_enable_recipient_message" => array( 'label' => __( 'Enable Recipient Message:', 'woovoucher' ) , 'type' => 'checkbox', 'class' => 'wcfm-checkbox', 'label_class' => 'wcfm_title checkbox_title', 'hints' => __( 'To enable the recipient message on the product page.', 'woovoucher' ), 'dfvalue' => $_woo_vou_enable_recipient_message, 'value' => 'yes' ),
																																												"_woo_vou_recipient_message_label" => array( 'label' => '&nbsp;&nbsp;', 'type' => 'text', 'class' => 'wcfm-text _woo_vou_enable_recipient_message_ele', 'label_class' => 'wcfm_title _woo_vou_enable_recipient_message_ele', 'placeholder' => __( 'Label:', 'woovoucher' ), 'value' => $_woo_vou_recipient_message_label ),
																																												"_woo_vou_recipient_message_max_length" => array( 'type' => 'text', 'class' => 'wcfm-text _woo_vou_enable_recipient_message_ele', 'label_class' => 'wcfm_title checkbox_title', 'placeholder' => __( 'Max Length:', 'woovoucher' ), 'value' => $_woo_vou_recipient_message_max_length ),
																																												"_woo_vou_recipient_message_is_required" => array( 'type' => 'checkbox', 'class' => 'wcfm-checkbox _woo_vou_enable_recipient_message_ele', 'label_class' => 'wcfm_title', 'dfvalue' => $_woo_vou_recipient_message_is_required, 'value' => 'yes', 'attributes' => array( 'title' => __( 'Enable to make this required.', 'wc-frontend-manager-ultimate' ) ) ),
																																												"_woo_vou_recipient_message_desc" => array( 'label' => __( 'Description:', 'woovoucher' ), 'type' => 'textarea', 'class' => 'wcfm-textarea _woo_vou_enable_recipient_message_ele', 'label_class' => 'wcfm_title _woo_vou_enable_recipient_message_ele','value' => $_woo_vou_recipient_message_desc, 'hints' => __( 'Enter the description which you want to show on product page.', 'woovoucher' ) ),
																																												"_break_ele_gvs_3" => array( 'type' => 'html', 'value' => '<div class="wcfm-clearfix _woo_vou_enable_recipient_message_ele" style="margin-bottom:25px;"></div>' ),
																																												
																																												"_woo_vou_enable_recipient_giftdate" => array( 'label' => __( 'Enable Recipient Gift Date:', 'woovoucher' ) , 'type' => 'checkbox', 'class' => 'wcfm-checkbox', 'label_class' => 'wcfm_title checkbox_title', 'hints' => __( 'To enable the recipient\'s gift date selection on the product page.', 'woovoucher' ), 'dfvalue' => $_woo_vou_enable_recipient_giftdate, 'value' => 'yes' ),
																																												"_woo_vou_recipient_giftdate_label" => array( 'label' => '&nbsp;&nbsp;', 'type' => 'text', 'class' => 'wcfm-text _woo_vou_enable_recipient_giftdate_ele', 'label_class' => 'wcfm_title _woo_vou_enable_recipient_giftdate_ele', 'placeholder' => __( 'Label:', 'woovoucher' ), 'value' => $_woo_vou_recipient_giftdate_label ),
																																												"_woo_vou_recipient_giftdate_is_required" => array( 'type' => 'checkbox', 'class' => 'wcfm-checkbox _woo_vou_enable_recipient_giftdate_ele', 'label_class' => 'wcfm_title', 'dfvalue' => $_woo_vou_recipient_giftdate_is_required, 'value' => 'yes', 'attributes' => array( 'title' => __( 'Enable to make this required.', 'wc-frontend-manager-ultimate' ) ) ),
																																												"_woo_vou_recipient_giftdate_desc" => array( 'label' => __( 'Description:', 'woovoucher' ), 'type' => 'textarea', 'class' => 'wcfm-textarea _woo_vou_enable_recipient_giftdate_ele', 'label_class' => 'wcfm_title _woo_vou_enable_recipient_giftdate_ele','value' => $_woo_vou_recipient_giftdate_desc, 'hints' => __( 'Enter the description which you want to show on product page.', 'woovoucher' ) ),
																																												"_break_ele_gvs_4" => array( 'type' => 'html', 'value' => '<div class="wcfm-clearfix _woo_vou_enable_recipient_giftdate_ele" style="margin-bottom:25px;"></div>' ),
																																												
																																												"_woo_vou_enable_recipient_delivery_method" => array( 'label' => __( 'Enable Delivery Method:', 'woovoucher' ) , 'type' => 'checkbox', 'class' => 'wcfm-checkbox', 'label_class' => 'wcfm_title checkbox_title', 'hints' => __( 'To enable the recipient\'s delivery method on the product page.', 'woovoucher' ), 'dfvalue' => $_woo_vou_enable_recipient_delivery_method, 'value' => 'yes' ),
																																												"_woo_vou_recipient_delivery_label" => array( 'label' => '&nbsp;&nbsp;', 'type' => 'text', 'class' => 'wcfm-text _woo_vou_enable_recipient_delivery_ele', 'label_class' => 'wcfm_title _woo_vou_enable_recipient_delivery_ele', 'placeholder' => __( 'Label:', 'woovoucher' ), 'value' => $_woo_vou_recipient_delivery_label ),
																																												"_break_ele_gvs_5" => array( 'type' => 'html', 'value' => '<div class="wcfm-clearfix _woo_vou_enable_recipient_delivery_ele" style="margin-bottom:25px;"></div>' ),
																																												
																																												"_woo_vou_recipient_delivery_email_enable" => array( 'label' => __( 'Email to Recipient', 'woovoucher' ) , 'type' => 'checkbox', 'name' => '_woo_vou_recipient_delivery[enable_email]', 'class' => 'wcfm-checkbox _woo_vou_enable_recipient_delivery_ele', 'label_class' => 'wcfm_title _woo_vou_enable_recipient_delivery_ele', 'dfvalue' => $_woo_vou_recipient_delivery_email_enable, 'value' => 'yes' ),
																																												"_woo_vou_recipient_delivery_email_label" => array( 'label' => '&nbsp;&nbsp;', 'name' => '_woo_vou_recipient_delivery[label_email]', 'type' => 'text', 'class' => 'wcfm-text _woo_vou_enable_recipient_delivery_ele _woo_vou_enable_recipient_delivery_email_ele', 'label_class' => 'wcfm_title _woo_vou_enable_recipient_delivery_ele _woo_vou_enable_recipient_delivery_email_ele', 'placeholder' => __( 'Label:', 'woovoucher' ), 'value' => $_woo_vou_recipient_delivery_email_label ),
																																												"_break_ele_gvs_6" => array( 'type' => 'html', 'value' => '<div class="wcfm-clearfix _woo_vou_enable_recipient_delivery_ele _woo_vou_enable_recipient_delivery_email_ele"></div>' ),
																																												"_woo_vou_recipient_delivery_email_desc" => array( 'label' => __( 'Description:', 'woovoucher' ), 'name' => '_woo_vou_recipient_delivery[desc_email]', 'type' => 'textarea', 'class' => 'wcfm-textarea _woo_vou_enable_recipient_delivery_ele _woo_vou_enable_recipient_delivery_email_ele', 'label_class' => 'wcfm_title _woo_vou_enable_recipient_delivery_ele _woo_vou_enable_recipient_delivery_email_ele','value' => $_woo_vou_recipient_delivery_email_desc, 'hints' => __( 'Enter the description which you want to show on product page.', 'woovoucher' ) ),
																																												"_woo_vou_recipient_delivery_email_recipient" => array( 'label' => __( 'Recipient Fields:', 'wc-frontend-manager-ultimate' ), 'name' => '_woo_vou_recipient_delivery[email]', 'type' => 'select', 'options' => $_recipient_details, 'attributes' => array( 'multiple' => true ), 'class' => 'wcfm-select _woo_vou_enable_recipient_delivery_ele _woo_vou_enable_recipient_delivery_email_ele', 'label_class' => 'wcfm_title _woo_vou_enable_recipient_delivery_ele _woo_vou_enable_recipient_delivery_email_ele','value' => $_woo_vou_recipient_delivery_email_recipient, 'hints' => __( 'Select recipient fields that you want to show in Email to Recipient block.', 'wc-frontend-manager-ultimate' ) ),
																																												"_break_ele_gvs_7" => array( 'type' => 'html', 'value' => '<div class="wcfm-clearfix _woo_vou_enable_recipient_delivery_ele _woo_vou_enable_recipient_delivery_email_ele" style="margin-bottom:25px;"></div>' ),
																																												
																																												"_woo_vou_recipient_delivery_offline_enable" => array( 'label' => __( 'Offline', 'woovoucher' ) , 'type' => 'checkbox', 'name' => '_woo_vou_recipient_delivery[enable_offline]', 'class' => 'wcfm-checkbox _woo_vou_enable_recipient_delivery_ele', 'label_class' => 'wcfm_title _woo_vou_enable_recipient_delivery_ele', 'dfvalue' => $_woo_vou_recipient_delivery_offline_enable, 'value' => 'yes' ),
																																												"_woo_vou_recipient_delivery_offline_label" => array( 'label' => '&nbsp;&nbsp;', 'name' => '_woo_vou_recipient_delivery[label_offline]', 'type' => 'text', 'class' => 'wcfm-text _woo_vou_enable_recipient_delivery_ele _woo_vou_enable_recipient_delivery_offline_ele', 'label_class' => 'wcfm_title _woo_vou_enable_recipient_delivery_ele _woo_vou_enable_recipient_delivery_offline_ele', 'placeholder' => __( 'Label:', 'woovoucher' ), 'value' => $_woo_vou_recipient_delivery_offline_label ),
																																												"_break_ele_gvs_8" => array( 'type' => 'html', 'value' => '<div class="wcfm-clearfix _woo_vou_enable_recipient_delivery_ele _woo_vou_enable_recipient_delivery_offline_ele"></div>' ),
																																												"_woo_vou_recipient_delivery_offline_desc" => array( 'label' => __( 'Description:', 'woovoucher' ), 'name' => '_woo_vou_recipient_delivery[desc_offline]', 'type' => 'textarea', 'class' => 'wcfm-textarea _woo_vou_enable_recipient_delivery_ele _woo_vou_enable_recipient_delivery_offline_ele', 'label_class' => 'wcfm_title _woo_vou_enable_recipient_delivery_ele _woo_vou_enable_recipient_delivery_offline_ele','value' => $_woo_vou_recipient_delivery_offline_desc, 'hints' => __( 'Enter the description which you want to show on product page.', 'woovoucher' ) ),
																																												"_woo_vou_recipient_delivery_offline_recipient" => array( 'label' => __( 'Recipient Fields:', 'wc-frontend-manager-ultimate' ), 'name' => '_woo_vou_recipient_delivery[offline]', 'type' => 'select', 'options' => $_recipient_details, 'attributes' => array( 'multiple' => true ), 'class' => 'wcfm-select _woo_vou_enable_recipient_delivery_ele _woo_vou_enable_recipient_delivery_offline_ele', 'label_class' => 'wcfm_title _woo_vou_enable_recipient_delivery_ele _woo_vou_enable_recipient_delivery_offline_ele','value' => $_woo_vou_recipient_delivery_offline_recipient, 'hints' => __( 'Select recipient fields that you want to show in Offline to Recipient block.', 'wc-frontend-manager-ultimate' ) ),
																																												
																																												
																																												"_woo_vou_heading_3" => array( 'type' => 'html', 'value' => '<h2 style="padding-top:50px;">' . __( 'Voucher Template Setting', 'wc-frontend-manager-ultimate' ) . '</h2><div class="wcfm_clearfix"></div>' ),
																																												
																																												"_woo_vou_enable_pdf_template_selection" => array( 'label' => __( 'Enable Template Selection:', 'woovoucher' ) , 'type' => 'checkbox', 'class' => 'wcfm-checkbox', 'label_class' => 'wcfm_title checkbox_title', 'hints' => __( 'To enable the PDF template selection on the product page.', 'woovoucher' ), 'dfvalue' => $_woo_vou_enable_pdf_template_selection, 'value' => 'yes' ),
																																												"_woo_vou_pdf_template_selection_label" => array( 'label' => '&nbsp;&nbsp;', 'type' => 'text', 'class' => 'wcfm-text _woo_vou_enable_pdf_template_selection_ele', 'label_class' => 'wcfm_title _woo_vou_enable_pdf_template_selection_ele', 'placeholder' => __( 'Label:', 'woovoucher' ), 'value' => $_woo_vou_pdf_template_selection_label ),
																																												"_woo_vou_pdf_template_selection" => array( 'label' => __( 'Select PDF Template:', 'woovoucher' ), 'type' => 'select', 'class' => 'wcfm-select _woo_vou_enable_pdf_template_selection_ele', 'label_class' => 'wcfm_title _woo_vou_enable_pdf_template_selection_ele', 'attributes' => array( 'multiple' => true ), 'options' => $multiple_voucher_options, 'value' => $_woo_vou_pdf_template_selection ),
																																												"_woo_vou_pdf_selection_desc" => array( 'label' => __( 'Description:', 'woovoucher' ), 'type' => 'textarea', 'class' => 'wcfm-textarea _woo_vou_enable_pdf_template_selection_ele', 'label_class' => 'wcfm_title _woo_vou_enable_pdf_template_selection_ele','value' => $_woo_vou_pdf_selection_desc, 'hints' => __( 'Enter the description which you want to show on product page.', 'woovoucher' ) ),
																																												"_break_ele_gvs_70" => array( 'type' => 'html', 'value' => '<div class="wcfm-clearfix _woo_vou_enable_pdf_template_selection_non_ele"></div>' ),
																																												"_woo_vou_pdf_template" => array( 'label' => __( 'PDF Template:', 'woovoucher' ), 'type' => 'select', 'class' => 'wcfm-select _woo_vou_enable_pdf_template_selection_non_ele', 'label_class' => 'wcfm_title _woo_vou_enable_pdf_template_selection_non_ele', 'hints' => __( 'Select a PDF template. This setting modifies the global PDF template setting and overrides vendor\'s PDF template value. Leave it empty to use the global/vendor settings.', 'woovoucher' ), 'options' => $voucher_options, 'value' => $_woo_vou_pdf_template ),
																																												
																																												"_woo_vou_heading_4" => array( 'type' => 'html', 'value' => '<h2 style="padding-top:50px;">' . __( 'Voucher Vendor Setting', 'wc-frontend-manager-ultimate' ) . '</h2><div class="wcfm_clearfix"></div>' ),
																																												
																																												"_woo_vou_vendor_user" => array( 'label' => __( 'Primary Vendor User:', 'woovoucher' ), 'type' => 'select', 'class' => 'wcfm-select ' . $vendor_user_ele_class, 'label_class' => 'wcfm_title ' . $vendor_user_ele_class, 'hints' => __( 'Please select the primary vendor user.', 'woovoucher' ), 'options' => $vendor_options, 'value' => $_woo_vou_vendor_user ),
																																												"_woo_vou_sec_vendor_users" => array( 'label' => __( 'Secondary Vendor Users:', 'woovoucher' ), 'type' => 'select', 'class' => 'wcfm-select ' . $vendor_user_ele_class, 'label_class' => 'wcfm_title ' . $vendor_user_ele_class, 'attributes' => array( 'multiple' => true ), 'hints' => __( 'Please select the secondary vendor users. You can select multiple users as secondary vendor users.', 'woovoucher' ), 'options' => $vendor_options, 'value' => $_woo_vou_sec_vendor_users ),
																																												
																																												"_woo_vou_logo" => array( 'label' => __( 'Vendor\'s Logo:', 'woovoucher' ), 'type' => 'upload', 'class' => 'wcfm-text', 'label_class' => 'wcfm_title', 'prwidth' => 150, 'wcfm_uploader_by_url' => true, 'hints' => __( 'Allows you to upload a logo of the vendor for which this voucher is valid. The logo will also be displayed on the PDF document. Leave it empty to use the vendor logo from the vendor settings.', 'woovoucher' ), 'value' => $_woo_vou_logo ),
																																												"_woo_vou_address_phone" => array( 'label' => __( 'Vendor\'s Address:', 'woovoucher' ), 'type' => 'textarea', 'class' => 'wcfm-textarea ', 'label_class' => 'wcfm_title ', 'hints' => __( 'Here you can enter the complete vendor\'s address. This will be displayed on the PDF document sent to the customers so that they know where to redeem this voucher. Limited HTML is allowed. Leave it empty to use address from the vendor settings.', 'woovoucher' ), 'value' => $_woo_vou_address_phone ),
																																												"_woo_vou_website" => array( 'label' => __( 'Website URL:', 'woovoucher' ), 'type' => 'text', 'class' => 'wcfm-text', 'label_class' => 'wcfm_title', 'hints' => __( 'Enter the vendor\'s website URL here. This will be displayed on the PDF document sent to the customer. Leave it empty to use website URL from the vendor settings.', 'woovoucher' ), 'value' => $_woo_vou_website ),
																																												"_woo_vou_how_to_use" => array( 'label' => __( 'Redeem Instructions:', 'woovoucher' ), 'type' => 'textarea', 'class' => 'wcfm-textarea ', 'label_class' => 'wcfm_title ', 'hints' => __( 'Within this option, you can enter instructions on how this voucher can be redeemed. This instruction will then be displayed on the PDF document sent to the customer after successful purchase. Limited HTML is allowed. Leave it empty to use redeem instructions from the vendor settings.', 'woovoucher' ), 'value' => $_woo_vou_how_to_use ),
																																												
																																												"avail_locations" => array( 'label' => __( 'Locations:', 'woovoucher' ), 'type' => 'multiinput', 'label_class' => 'wcfm_title', 'hints' => __( 'If the vendor of the voucher has more than one location where the voucher can be redeemed, then you can add all the locations within this option. Leave it empty to use locations from the vendor settings.', 'woovoucher' ), 'value' => $avail_locations, 'options' => array(
																																													                          "_woo_vou_locations" => array( 'label' => __( 'Location:', 'woovoucher' ), 'type' => 'text', 'class' => 'wcfm-text', 'label_class' => 'wcfm_title', 'hints' => __( 'Enter the address of the location where the voucher code can be redeemed. This will be displayed on the PDF document sent to the customer. Limited HTML is allowed.', 'woovoucher' ) ),
																																													                          "_woo_vou_map_link" => array( 'label' => __( 'Location Map Link:', 'woovoucher' ), 'type' => 'text', 'class' => 'wcfm-text', 'label_class' => 'wcfm_title', 'hints' => __( 'Enter a link to a google map for the location here. This will be displayed on the PDF document sent to the customer.', 'woovoucher' ) )
																																													                        ) )
																																												//"_wc_deposit_type" => array( 'label' => __('Deposit Type', 'woocommerce-deposits') , 'type' => 'select', 'options'     => array( '' => $inherit_wc_deposit_type, 'percent' => __( 'Percentage', 'woocommerce-deposits' ), 'fixed'   => __( 'Fixed Amount', 'woocommerce-deposits' ) ), 'class' => 'wcfm-select', 'label_class' => 'wcfm_title', 'hints' => __( 'Choose how customers can pay for this product using a deposit.', 'woocommerce-deposits' ), 'value' => $_wc_deposit_type),
																																												//"_wc_deposit_multiple_cost_by_booking_persons" => array( 'label' => __('Booking Persons', 'woocommerce-deposits') , 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele booking', 'label_class' => 'wcfm_title checkbox_title wcfm_ele booking', 'hints' => __( 'Multiply fixed deposits by the number of persons booking', 'woocommerce-deposits' ), 'value' => 'yes', 'dfvalue' => $_wc_deposit_multiple_cost_by_booking_persons),
																																												//"_wc_deposit_amount" => array('label' => __('Deposit Amount', 'woocommerce-deposits') , 'type' => 'number', 'placeholder' => wc_format_localized_price( 0 ), 'class' => 'wcfm-text', 'label_class' => 'wcfm_title', 'hints' => __( 'The amount of deposit needed. Do not include currency or percent symbols.', 'woocommerce-deposits' ), 'value' => $_wc_deposit_amount ),
																																												//"_wc_deposit_selected_type" => array( 'label' => __('Default Deposit Selected Type', 'woocommerce-deposits') , 'type' => 'select', 'options'     => array( '' => $inherit_wc_deposit_selected_type, 'deposit' => __( 'Pay Deposit', 'woocommerce-deposits' ), 'full'   => __( 'Pay in Full', 'woocommerce-deposits' ) ), 'class' => 'wcfm-select', 'label_class' => 'wcfm_title', 'hints' => __( 'Choose the default selected type of payment on page load.', 'woocommerce-deposits' ), 'value' => $_wc_deposit_selected_type),
																																							), $product_id );
		
		$price_options = get_option('vou_voucher_price_options');
    if( empty($price_options) || ( !empty($price_options) && $price_options != 2 ) ) {
     	unset( $woo_vou_fields['_woo_vou_voucher_price'] );
    }
		
		$WCFM->wcfm_fields->wcfm_generate_form_field( $woo_vou_fields );
		?>
	</div>
</div>
<div class="wcfm_clearfix"></div>

<?php 
if( $product_id ) {
	global $woo_vou_model, $woo_vou_voucher;
	
	$prefix = WOO_VOU_META_PREFIX;
	$postid = apply_filters( 'woo_vou_edit_product_id', $product_id, get_post( $product_id ) );
	
	// Get Voucher Details by post id
	$purchased_posts_per_page 	= apply_filters( 'woo_vou_used_code_popup_per_page', 10 ); // Apply filter to change per page records
	$purchased_paged 			= 1; // Declare paged to default 1
	
	// Get purchased codes for current page and total
	$purchasedcodes 	= woo_vou_get_purchased_codes_by_product_id( $postid, $purchased_posts_per_page, $purchased_paged );
	?>
	<!-- HTML for purchased codes popup -->
	<div id="wcfm-woo-vou-purchased-codes-popup">
		<div class="woo-vou-popup-content woo-vou-purchased-codes-popup wcfm_popup_wrapper">
		  <div style="margin-bottom: 15px;"><h2 style="float: none;"><?php _e( 'Purchased Voucher Codes', 'woovoucher' ); ?></h2></div>
		  
			<?php
			$generatpdfurl 	= add_query_arg( array( 'woo-vou-used-gen-pdf' => '1', 'product_id' => $postid ) );
			$exportcsvurl 	= add_query_arg( array( 'woo-vou-used-exp-csv' => '1', 'product_id' => $postid ) );
		
			// Purchase codes table columns
			$purchasedcodes_columns	= apply_filters( 'woo_vou_product_purchasedcodes_columns', array(
															'voucher_code'	=> __( 'Voucher Code', 'woovoucher' ),
															'buyer_info'	=> __( 'Buyer\'s Information', 'woovoucher' ),
															'order_info'	=> __( 'Order Information', 'woovoucher' ),
														), $postid );
			?>
		
			<div class="woo-vou-popup used-codes">
				<div>
					<a href="<?php echo $exportcsvurl;?>" id="woo-vou-export-csv-btn" class="button-secondary wcfm_popup_button" title="<?php echo __( 'Export CSV', 'woovoucher' );?>"><?php echo __('Export CSV','woovoucher');?></a>
					<a href="<?php echo $generatpdfurl;?>" id="woo-vou-pdf-btn" class="button-secondary wcfm_popup_button" title="<?php echo __( 'Generate PDF', 'woovoucher' );?>"><?php echo __( 'Generate PDF', 'woovoucher' );?></a>
				</div>
				<div class="wcfm-clearfix"></div><br />
		
				<table id="woo_vou_purchased_codes_table" class="form-table" border="1">
					<tbody>
						<tr>
						<?php
							if( !empty( $purchasedcodes_columns ) ) {
								foreach ( $purchasedcodes_columns as $column_key => $column ) {?>
		
									<th scope="row" class="<?php echo $column_key ?>"><?php echo $column;?></th><?php
								}
							}
						?>
						</tr><?php 
						if( !empty( $purchasedcodes ) &&  count( $purchasedcodes ) > 0 ) { 
							
							foreach ( $purchasedcodes as $key => $voucodes_data ) { 
								
								$voucher_codes = explode(',', $voucodes_data['vou_codes'] );
								foreach( $voucher_codes as $voucher_code ) {                        
									//voucher order id
									$orderid 		= $voucodes_data['order_id'];

									if( !empty( $purchasedcodes_columns ) ) {?>
										<tr><?php 
										foreach ( $purchasedcodes_columns as $column_key => $column ) {

												$column_value = '';

												switch( $column_key ) {

														case 'voucher_code': // voucher code purchased
																$column_value	= $voucher_code;
																break;
														case 'buyer_info': // buyer's info who has purchased voucher code
																$column_value 	= '<div id="buyer_voucher_'.$voucodes_data['voucode_id'].'">';
																$buyer_info 	= $woo_vou_model->woo_vou_get_buyer_information( $orderid );
																$column_value 	.= woo_vou_display_buyer_info_html( $buyer_info );
																$column_value 	.= '<a class="woo-vou-show-buyer" data-voucherid="'.$voucodes_data['voucode_id'].'">'.__( 'Show', 'woovoucher' ).'</a>';
																$column_value 	.= '</div>';
																break;
														case 'order_info': // voucher order info
																$column_value 	= '<div id="order_voucher_'.$voucodes_data['voucode_id'].'">';
																$column_value 	.= woo_vou_display_order_info_html( $orderid );
																$column_value 	.= '<a class="woo-vou-show-order" data-voucherid="'.$voucodes_data['voucode_id'].'">'.__( 'Show', 'woovoucher' ).'</a>';
																$column_value 	.= '</div>';
																break;
												}
												$column_value = apply_filters( 'woo_vou_product_purchasedcodes_column_value', $column_value, $voucodes_data, $postid );
												?>

												<td><?php echo $column_value;?></td><?php 
											}?>
											</tr><?php
									}
								}
							}
						} else { ?>
							<tr>
								<td colspan="4"><?php echo __( 'No voucher codes purchased yet.','woovoucher' );?></td>
							</tr><?php 
						}?>
					</tbody>
				</table>
				<?php
				// Generating HTML for loading more purchased codes with all required information
				if( !empty( $purchasedcodes ) ) { ?>
		
					<div class="woo-vou-purchased-load-more woo-vou-load-more-wrap">
						<input type="hidden" id="woo_vou_purchased_post_id" value="<?php echo $postid; ?>">
						<input type="hidden" id="woo_vou_purchased_paged" value="<?php echo $purchased_paged; ?>">
						<input type="hidden" id="woo_vou_purchased_postsperpage" value="<?php echo $purchased_posts_per_page; ?>">
						<input id="woo_vou_purchased_load_more_btn" class="woo-vou-purchased-load-more-btn button-primary wcfm_popup_button" value="<?php echo __( 'Load More', 'woovoucher' );?>" id="woo_vou_purchased_load_more_btn" type="button">
					</div>
				<?php } ?>
			</div><!--.woo-vou-popup-->
		</div><!--.woo-vou-purchased-codes-popup-->
	</div>
	<?php
	
	$used_paged = 1;
	$used_posts_per_page = -1; 
	
	// Get used codes for current page and total
	$usedcodes 		= woo_vou_get_used_codes_by_product_id( $postid, $used_posts_per_page, $used_paged );
?>

  <div id="wcfm-woo-vou-used-codes-popup">
		<!-- HTML for USED codes popup -->
		<div class="woo-vou-popup-content woo-vou-used-codes-popup wcfm_popup_wrapper">
		  <div style="margin-bottom: 15px;"><h2 style="float: none;"><?php _e( 'Used Voucher Codes', 'woovoucher' ); ?></h2></div>
			<?php
			$generatpdfurl 	= add_query_arg( array( 'woo-vou-used-gen-pdf' => '1', 'product_id' => $postid, 'woo_vou_action'	=> 'used' ) );
			$exportcsvurl 	= add_query_arg( array( 'woo-vou-used-exp-csv' => '1', 'product_id' => $postid, 'woo_vou_action' => 'used' ) );
		
			//used codes table columns
			$usedcodes_columns	= apply_filters( 'woo_vou_product_usedcodes_columns', array(
															'voucher_code'	=> __( 'Voucher Code', 'woovoucher' ),
															'buyer_info'	=> __( 'Buyer\'s Information', 'woovoucher' ),
															'order_info'	=> __( 'Order Information', 'woovoucher' ),
															'redeem_info'		=> __( 'Redeem Information', 'woovoucher' )
														), $postid );
			?>
			<div class="woo-vou-popup used-codes">
				<div>
					<a href="<?php echo $exportcsvurl;?>" id="woo-vou-export-csv-btn" class="button-secondary wcfm_popup_button" title="<?php echo __( 'Export CSV', 'woovoucher' );?>"><?php echo __( 'Export CSV', 'woovoucher' );?></a>
					<a href="<?php echo $generatpdfurl;?>" id="woo-vou-pdf-btn" class="button-secondary wcfm_popup_button" title="<?php echo __( 'Generate PDF', 'woovoucher' );?>"><?php echo __( 'Generate PDF', 'woovoucher' );?></a>
				</div>
				<div class="wcfm-clearfix"></div><br />
				
				<table id="woo_vou_used_codes_table" class="form-table" border="1">
					<tbody>
						<tr>
						<?php
							if( !empty( $usedcodes_columns ) ) {
								foreach ( $usedcodes_columns as $column_key => $column ) {
									echo '<th scope="row" class="' . $column_key . '">' . $column . '</th>';
								}
							}?>
						</tr><?php 
						
						if( !empty( $usedcodes ) &&  count( $usedcodes ) > 0 ) { 
							
							foreach ( $usedcodes as $key => $voucodes_data ) { 
		
								$orderid	= $voucodes_data['order_id']; // voucher order id
								$user_id	= $voucodes_data['redeem_by']; // get user id
		
								if( !empty( $usedcodes_columns ) ) {
									echo '<tr>';
		
									// Looping on used codes array
									foreach ( $usedcodes_columns as $column_key => $column ) {
										
										$column_value = '';
										
										switch( $column_key ) {
		
											case 'voucher_code': // voucher code purchased
												$column_value	= $voucodes_data['vou_codes'];
												break;
											case 'buyer_info': // buyer's info who has used voucher code
												$column_value 	= '<div id="buyer_voucher_'.$voucodes_data['voucode_id'].'">';
												$buyer_info 	= $woo_vou_model->woo_vou_get_buyer_information( $orderid );
												$column_value 	.= woo_vou_display_buyer_info_html( $buyer_info );
												$column_value 	.= '<a class="woo-vou-show-buyer" data-voucherid="'.$voucodes_data['voucode_id'].'">'.__( 'Show', 'woovoucher' ).'</a>';
												$column_value 	.= '</div>';
												break;
											case 'order_info': // voucher order info
												$column_value 	= '<div id="order_voucher_'.$voucodes_data['voucode_id'].'">';
												$column_value 	.= woo_vou_display_order_info_html( $orderid );
												$column_value 	.= '<a class="woo-vou-show-order" data-voucherid="'.$voucodes_data['voucode_id'].'">'.__( 'Show', 'woovoucher' ).'</a>';
												$column_value 	.= '</div>';
												break;
											case 'redeem_info' :
												$column_value 	= woo_vou_display_redeem_info_html( $voucodes_data['voucode_id'], $orderid, '' );
												break;
										}
										
										$column_value = apply_filters( 'woo_vou_product_usedcodes_column_value', $column_value, $voucodes_data, $postid );?>
										<td><?php echo $column_value;?></td><?php 
									}
									echo '</tr>';
								}
							}
						} else { ?>
							<tr>
								<td colspan="5"><?php echo __( 'No voucher codes used yet.', 'woovoucher' );?></td>
							</tr><?php
						}?>
					</tbody>
				</table>
				<?php 
		
				// Generating HTML for loading more used codes with all required information
				if( !empty( $usedcodes ) ) { ?>
		
					<div class="woo-vou-used-load-more woo-vou-load-more-wrap">
						<input type="hidden" id="woo_vou_used_post_id" value="<?php echo $postid; ?>">
						<input type="hidden" id="woo_vou_used_paged" value="<?php echo $used_paged; ?>">
						<input type="hidden" id="woo_vou_used_postsperpage" value="<?php echo $used_posts_per_page; ?>">
						<input id="woo_vou_used_load_more_btn" class="woo-vou-used-load-more-btn button-primary wcfm_popup_button" value="<?php echo __( 'Load More', 'woovoucher' );?>" id="woo_vou_used_load_more_btn" type="button">
					</div>
				<?php } ?>
			</div><!--.woo-vou-popup-->
		</div><!--.woo-vou-used-codes-popup-->
	</div>
	<?php
}
?>