<?php
/**
 * WCFM plugin controllers
 *
 * Plugin WC PDF Vouchers Products Manage Controller
 *
 * @author 		WC Lovers
 * @package 	wcfmu/controllers/thirdparty
 * @version   4.0.0
 */

class WCFMu_WC_PDF_Vouchers_Products_Manage_Controller {
	
	public function __construct() {
		global $WCFM;
		
		// Third Party Product Meta Data Save
    add_action( 'after_wcfm_products_manage_meta_save', array( &$this, 'wcfm_wc_pdf_vouchers_products_manage_meta_save' ), 160, 2 );
    
    add_filter( 'after_wcfm_product_variation_meta_save', array( &$this, 'wcfmu_wc_pdf_vouchers_product_variation_save' ), 160, 4 );
	}
	
	/**
	 * ACF Field Product Meta data save
	 */
	function wcfm_wc_pdf_vouchers_products_manage_meta_save( $new_product_id, $wcfm_products_manage_form_data ) {
		global $WCFM, $WCFMu, $woo_vou_model;
		
    // get prefix
    $prefix = WOO_VOU_META_PREFIX;

    //is downloadable
    $is_downloadable = get_post_meta($new_product_id, '_downloadable', true);

    // Getting product type
    $product_type = !empty($wcfm_products_manage_form_data['product_type']) ? $wcfm_products_manage_form_data['product_type'] : '';

    // Enable Voucher Codes
    $woo_vou_enable = !empty($wcfm_products_manage_form_data[$prefix . 'enable']) ? 'yes' : '';
    
    // Vouvher Price
    $woo_vou_voucher_price = !empty($wcfm_products_manage_form_data[$prefix . 'voucher_price']) ? $wcfm_products_manage_form_data[$prefix . 'voucher_price'] : '';

    // get Pdf template
    $woo_vou_pdf_template = isset($wcfm_products_manage_form_data[$prefix . 'pdf_template']) ? $wcfm_products_manage_form_data[$prefix . 'pdf_template'] : '';

    // Usability
    $woo_vou_using_type = isset($wcfm_products_manage_form_data[$prefix . 'using_type']) ? $wcfm_products_manage_form_data[$prefix . 'using_type'] : '';

    // get logo
    $woo_vou_logo = isset($wcfm_products_manage_form_data[$prefix . 'logo']) ? $wcfm_products_manage_form_data[$prefix . 'logo'] : '';
    if( $woo_vou_logo ) $woo_vou_logo = array( 'src' => $woo_vou_logo, 'id' => $WCFM->wcfm_get_attachment_id( $woo_vou_logo ) );

    // get address
    $woo_vou_address_phone = isset($wcfm_products_manage_form_data[$prefix . 'address_phone']) ? $wcfm_products_manage_form_data[$prefix . 'address_phone'] : '';

    // get website
    $woo_vou_website = isset($wcfm_products_manage_form_data[$prefix . 'website']) ? $wcfm_products_manage_form_data[$prefix . 'website'] : '';

    // get redeem instructions
    $woo_vou_how_to_use = isset($wcfm_products_manage_form_data[$prefix . 'how_to_use']) ? $wcfm_products_manage_form_data[$prefix . 'how_to_use'] : '';
    
    // enable pdf template selection
    $enable_pdf_preview = !empty($wcfm_products_manage_form_data[$prefix . 'enable_pdf_preview']) ? $wcfm_products_manage_form_data[$prefix . 'enable_pdf_preview'] : '';

    // enable coupon code generation
    $enable_coupon_code = !empty($wcfm_products_manage_form_data[$prefix . 'enable_coupon_code']) ? $wcfm_products_manage_form_data[$prefix . 'enable_coupon_code'] : '';
    
    // get enable 1 voucher per pdf
    $enable_multiple_pdf = !empty($wcfm_products_manage_form_data[$prefix . 'enable_multiple_pdf']) ? $wcfm_products_manage_form_data[$prefix . 'enable_multiple_pdf'] : '';

    // enable recipient name
    $enable_recipient_name = !empty($wcfm_products_manage_form_data[$prefix . 'enable_recipient_name']) ? 'yes' : '';
    $recipient_name_max_length = !empty($wcfm_products_manage_form_data[$prefix . 'recipient_name_max_length']) && is_numeric($wcfm_products_manage_form_data[$prefix . 'recipient_name_max_length']) ? trim(round($wcfm_products_manage_form_data[$prefix . 'recipient_name_max_length'])) : '';
    $recipient_name_label = !empty($wcfm_products_manage_form_data[$prefix . 'recipient_name_label']) ? trim($wcfm_products_manage_form_data[$prefix . 'recipient_name_label']) : '';
    $recipient_name_is_required = !empty($wcfm_products_manage_form_data[$prefix . 'recipient_name_is_required']) ? 'yes' : '';
    $recipient_name_desc = !empty($wcfm_products_manage_form_data[$prefix . 'recipient_name_desc']) ? trim($wcfm_products_manage_form_data[$prefix . 'recipient_name_desc']) : '';

    // enable recipient email
    $woo_vou_recipient_email = !empty($wcfm_products_manage_form_data[$prefix . 'enable_recipient_email']) ? 'yes' : '';
    $recipient_email_label = !empty($wcfm_products_manage_form_data[$prefix . 'recipient_email_label']) ? trim($wcfm_products_manage_form_data[$prefix . 'recipient_email_label']) : '';
    $recipient_email_is_required = !empty($wcfm_products_manage_form_data[$prefix . 'recipient_email_is_required']) ? 'yes' : '';
    $recipient_email_desc = !empty($wcfm_products_manage_form_data[$prefix . 'recipient_email_desc']) ? trim($wcfm_products_manage_form_data[$prefix . 'recipient_email_desc']) : '';

    // enable recipient message
    $enable_recipient_message = !empty($wcfm_products_manage_form_data[$prefix . 'enable_recipient_message']) ? 'yes' : '';
    $recipient_message_max_length = !empty($wcfm_products_manage_form_data[$prefix . 'recipient_message_max_length']) && is_numeric($wcfm_products_manage_form_data[$prefix . 'recipient_message_max_length']) ? trim(round($wcfm_products_manage_form_data[$prefix . 'recipient_message_max_length'])) : '';
    $recipient_message_label = !empty($wcfm_products_manage_form_data[$prefix . 'recipient_message_label']) ? trim($wcfm_products_manage_form_data[$prefix . 'recipient_message_label']) : '';
    $recipient_message_is_required = !empty($wcfm_products_manage_form_data[$prefix . 'recipient_message_is_required']) ? 'yes' : '';
    $recipient_message_desc = !empty($wcfm_products_manage_form_data[$prefix . 'recipient_message_desc']) ? trim($wcfm_products_manage_form_data[$prefix . 'recipient_message_desc']) : '';

    // enable recipient gift date field
    $woo_vou_recipient_gift_date = !empty($wcfm_products_manage_form_data[$prefix . 'enable_recipient_giftdate']) ? 'yes' : '';
    $recipient_giftdate_label = !empty($wcfm_products_manage_form_data[$prefix . 'recipient_giftdate_label']) ? trim($wcfm_products_manage_form_data[$prefix . 'recipient_giftdate_label']) : '';
    $recipient_giftdate_is_required = !empty($wcfm_products_manage_form_data[$prefix . 'recipient_giftdate_is_required']) ? 'yes' : '';
    $recipient_giftdate_desc = !empty($wcfm_products_manage_form_data[$prefix . 'recipient_giftdate_desc']) ? trim($wcfm_products_manage_form_data[$prefix . 'recipient_giftdate_desc']) : '';

    // enable recipient delivery methods
    $delivery_methods = woo_vou_voucher_delivery_methods();
    $woo_vou_enable_recipient_delivery_method = !empty($wcfm_products_manage_form_data[$prefix . 'enable_recipient_delivery_method']) ? 'yes' : '';
    $woo_vou_recipient_delivery_data = !empty($wcfm_products_manage_form_data[$prefix.'recipient_delivery']) ? $wcfm_products_manage_form_data[$prefix.'recipient_delivery'] : '';
    $woo_vou_recipient_delivery_label = !empty($wcfm_products_manage_form_data[$prefix.'recipient_delivery_label']) ? $wcfm_products_manage_form_data[$prefix.'recipient_delivery_label'] : '';

    foreach( $delivery_methods as $delivery_method_key => $delivery_method_val ){
    	if( !empty( $woo_vou_recipient_delivery_data['enable_'.$delivery_method_key] ) ) {
    		$woo_vou_recipient_delivery_data['enable_'.$delivery_method_key] = 'yes';
    	} else {
    		$woo_vou_recipient_delivery_data['enable_'.$delivery_method_key] = 'no';
    	}

    	if( !isset( $woo_vou_recipient_delivery_data[$delivery_method_key] ) ) {
    		$woo_vou_recipient_delivery_data[$delivery_method_key] = 'no';
    	}
    }
    
    
    // enable pdf template selection
    $woo_vou_pdf_template_selection = !empty($wcfm_products_manage_form_data[$prefix . 'enable_pdf_template_selection']) ? 'yes' : '';
    $pdf_template_selection_label = !empty($wcfm_products_manage_form_data[$prefix . 'pdf_template_selection_label']) ? trim($wcfm_products_manage_form_data[$prefix . 'pdf_template_selection_label']) : '';
    $pdf_template_selection_is_required = !empty($wcfm_products_manage_form_data[$prefix . 'pdf_template_selection_is_required']) ? 'yes' : '';
    $pdf_template_selection = !empty($wcfm_products_manage_form_data[$prefix . 'pdf_template_selection']) ? $wcfm_products_manage_form_data[$prefix . 'pdf_template_selection'] : '';
    $pdf_template_selection_desc = !empty($wcfm_products_manage_form_data[$prefix . 'pdf_selection_desc']) ? $wcfm_products_manage_form_data[$prefix . 'pdf_selection_desc'] : '';

    $disable_redeem_day = !empty($wcfm_products_manage_form_data[$prefix . 'disable_redeem_day']) ? $wcfm_products_manage_form_data[$prefix . 'disable_redeem_day'] : '';

    // Get voucher amount
    $woo_vou_voucher_price = isset($wcfm_products_manage_form_data[$prefix . 'voucher_price']) ? $wcfm_products_manage_form_data[$prefix . 'voucher_price'] : '';

    // Check if downloadable is on or variable product then set voucher enable option otherwise not set
    if ($is_downloadable == 'yes' || $product_type == 'variable') {

        $enable_voucher = $woo_vou_enable;
    } else {
        $enable_voucher = '';
    }

    // Getting downloadable variable
    //$variable_is_downloadable = !empty($wcfm_products_manage_form_data['variable_is_downloadable']) ? $wcfm_products_manage_form_data['variable_is_downloadable'] : array();

    update_post_meta($new_product_id, $prefix . 'enable', $enable_voucher);
    
    $price_options = get_option('vou_voucher_price_options'); // Get voucher price options
    if (!empty($price_options) && $price_options == 2) {
    	update_post_meta($new_product_id, $prefix . 'voucher_price', wc_format_decimal( $woo_vou_voucher_price ));
    }
    	
    //Recipient Name Detail Update
    update_post_meta($new_product_id, $prefix . 'enable_recipient_name', $enable_recipient_name);
    update_post_meta($new_product_id, $prefix . 'recipient_name_max_length', $recipient_name_max_length);
    update_post_meta($new_product_id, $prefix . 'recipient_name_label', $recipient_name_label);
    update_post_meta($new_product_id, $prefix . 'recipient_name_is_required', $recipient_name_is_required);
    update_post_meta($new_product_id, $prefix . 'recipient_name_desc', $recipient_name_desc);

    //Recipient Email Detail Update
    update_post_meta($new_product_id, $prefix . 'enable_recipient_email', $woo_vou_recipient_email);
    update_post_meta($new_product_id, $prefix . 'recipient_email_label', $recipient_email_label);
    update_post_meta($new_product_id, $prefix . 'recipient_email_is_required', $recipient_email_is_required);
    update_post_meta($new_product_id, $prefix . 'recipient_email_desc', $recipient_email_desc);

    //Recipient Message Detail Update
    update_post_meta($new_product_id, $prefix . 'enable_recipient_message', $enable_recipient_message);
    update_post_meta($new_product_id, $prefix . 'recipient_message_max_length', $recipient_message_max_length);
    update_post_meta($new_product_id, $prefix . 'recipient_message_label', $recipient_message_label);
    update_post_meta($new_product_id, $prefix . 'recipient_message_is_required', $recipient_message_is_required);
    update_post_meta($new_product_id, $prefix . 'recipient_message_desc', $recipient_message_desc);

    //Recipient Email Detail Update
    update_post_meta($new_product_id, $prefix . 'enable_recipient_giftdate', $woo_vou_recipient_gift_date);
    update_post_meta($new_product_id, $prefix . 'recipient_giftdate_label', $recipient_giftdate_label);
    update_post_meta($new_product_id, $prefix . 'recipient_giftdate_is_required', $recipient_giftdate_is_required);
    update_post_meta($new_product_id, $prefix . 'recipient_giftdate_desc', $recipient_giftdate_desc);
    
    //Delivery method detail update
    update_post_meta($new_product_id, $prefix . 'enable_recipient_delivery_method', $woo_vou_enable_recipient_delivery_method);
    update_post_meta($new_product_id, $prefix . 'recipient_delivery', $woo_vou_recipient_delivery_data);
    update_post_meta($new_product_id, $prefix . 'recipient_delivery_label', $woo_vou_recipient_delivery_label);

    //Pdf Template Selection Detail Update
    update_post_meta($new_product_id, $prefix . 'enable_pdf_template_selection', $woo_vou_pdf_template_selection);
    update_post_meta($new_product_id, $prefix . 'pdf_template_selection_label', $pdf_template_selection_label);
    update_post_meta($new_product_id, $prefix . 'pdf_template_selection_is_required', $pdf_template_selection_is_required);
    update_post_meta($new_product_id, $prefix . 'pdf_template_selection', $pdf_template_selection);
    update_post_meta($new_product_id, $prefix . 'pdf_selection_desc', $pdf_template_selection_desc);
    update_post_meta($new_product_id, $prefix . 'enable_pdf_preview', $enable_pdf_preview);
    update_post_meta($new_product_id, $prefix . 'enable_coupon_code', $enable_coupon_code);
    update_post_meta($new_product_id, $prefix . 'enable_multiple_pdf', $enable_multiple_pdf);

    update_post_meta($new_product_id, $prefix . 'disable_redeem_day', $disable_redeem_day); // disbale reedem days

    // wc_format_decimal function is used to take care for decimal seperator setting
    update_post_meta($new_product_id, $prefix . 'voucher_price', wc_format_decimal($woo_vou_voucher_price)); // Voucher Price Update
    // PDF Template
    update_post_meta($new_product_id, $prefix . 'pdf_template', $woo_vou_pdf_template);

    // Vendor User
    if( isset( $wcfm_products_manage_form_data[$prefix . 'vendor_user'] ) ) 
    	update_post_meta($new_product_id, $prefix . 'vendor_user', $wcfm_products_manage_form_data[$prefix . 'vendor_user']);

    // Voucher Delivery
    $woo_vou_voucher_delivery = isset($wcfm_products_manage_form_data[$prefix . 'voucher_delivery']) ? ($wcfm_products_manage_form_data[$prefix . 'voucher_delivery']) : '';
    if ( $woo_vou_voucher_delivery == 'default' ){
        $woo_vou_voucher_delivery = get_option('vou_voucher_delivery_options');
    }
    update_post_meta($new_product_id, $prefix . 'voucher_delivery', $woo_vou_voucher_delivery);
    
    $secondary_vendor_users = isset($wcfm_products_manage_form_data[$prefix . 'sec_vendor_users']) ? $wcfm_products_manage_form_data[$prefix . 'sec_vendor_users'] : '';
    // Secondary Vendor Users
    $secondary_vendor_users = isset($wcfm_products_manage_form_data[$prefix . 'sec_vendor_users']) && !empty($wcfm_products_manage_form_data[$prefix . 'sec_vendor_users']) ? $wcfm_products_manage_form_data[$prefix . 'sec_vendor_users'] : '';
    update_post_meta($new_product_id, $prefix . 'sec_vendor_users', $secondary_vendor_users);

    //expire type
    if (isset($wcfm_products_manage_form_data[$prefix . 'exp_type'])) {
      update_post_meta($new_product_id, $prefix . 'exp_type', $wcfm_products_manage_form_data[$prefix . 'exp_type']);
    }

    update_post_meta($new_product_id, $prefix . 'days_diff', $wcfm_products_manage_form_data[$prefix . 'days_diff']);

    $custom_days = !empty($wcfm_products_manage_form_data[$prefix . 'custom_days']) && is_numeric($wcfm_products_manage_form_data[$prefix . 'custom_days']) ? trim(round($wcfm_products_manage_form_data[$prefix . 'custom_days'])) : '';
    update_post_meta($new_product_id, $prefix . 'custom_days', $custom_days);


    // Product Start Date
    $product_start_date = $wcfm_products_manage_form_data[$prefix . 'product_start_date'];

    if (!empty($product_start_date)) {
        $product_start_date = strtotime($woo_vou_model->woo_vou_escape_slashes_deep(strtoupper($product_start_date)));
        $product_start_date = date('Y-m-d H:i:s', $product_start_date);
    }
    update_post_meta($new_product_id, $prefix . 'product_start_date', $product_start_date);

    // Expiration Date
    $product_exp_date = $wcfm_products_manage_form_data[$prefix . 'product_exp_date'];

    if (!empty($product_exp_date)) {
        $product_exp_date = strtotime($woo_vou_model->woo_vou_escape_slashes_deep(strtoupper($product_exp_date)));
        $product_exp_date = date('Y-m-d H:i:s', $product_exp_date);
    }
    update_post_meta($new_product_id, $prefix . 'product_exp_date', $product_exp_date);


    // Start Date
    $start_date = $wcfm_products_manage_form_data[$prefix . 'start_date'];

    if (!empty($start_date)) {
        $start_date = strtotime($woo_vou_model->woo_vou_escape_slashes_deep($start_date));
        $start_date = date('Y-m-d H:i:s', $start_date);
    }
    update_post_meta($new_product_id, $prefix . 'start_date', $start_date);

    // Expiration Date
    $exp_date = $wcfm_products_manage_form_data[$prefix . 'exp_date'];

    if (!empty($exp_date)) {
        $exp_date = strtotime($woo_vou_model->woo_vou_escape_slashes_deep($exp_date));
        $exp_date = date('Y-m-d H:i:s', $exp_date);
    }
    update_post_meta($new_product_id, $prefix . 'exp_date', $exp_date);

    // Voucher Codes
    $voucher_codes = isset($wcfm_products_manage_form_data[$prefix . 'codes']) ? $woo_vou_model->woo_vou_escape_slashes_deep($wcfm_products_manage_form_data[$prefix . 'codes']) : '';
    update_post_meta($new_product_id, $prefix . 'codes', html_entity_decode( $voucher_codes ) );

    $usability = $woo_vou_using_type;

    if (isset($wcfm_products_manage_form_data[$prefix . 'vendor_user']) && !empty($wcfm_products_manage_form_data[$prefix . 'vendor_user']) && $usability == '') {//if vendor user is set and usability is default 
        $usability = get_user_meta($wcfm_products_manage_form_data[$prefix . 'vendor_user'], $prefix . 'using_type', true);
    }

    // If usability is default then take it from setting
    if ($usability == '') {
        $usability = get_option('vou_pdf_usability');
    }

    update_post_meta($new_product_id, $prefix . 'using_type', $usability);

    // vendor's Logo
    update_post_meta($new_product_id, $prefix . 'logo', $woo_vou_logo);

    // Vendor's Address
    update_post_meta($new_product_id, $prefix . 'address_phone', $woo_vou_model->woo_vou_escape_slashes_deep($woo_vou_address_phone, true, true));

    // Website URL
    update_post_meta($new_product_id, $prefix . 'website', $woo_vou_model->woo_vou_escape_slashes_deep($woo_vou_website));

    // Redeem Instructions
    update_post_meta($new_product_id, $prefix . 'how_to_use', $woo_vou_model->woo_vou_escape_slashes_deep($woo_vou_how_to_use, true, true));

    // update available products count on bases of entered voucher codes
    if (isset($wcfm_products_manage_form_data[$prefix . 'codes']) && $enable_voucher == 'yes') {

			$voucount = '';
			$vouchercodes = trim($wcfm_products_manage_form_data[$prefix . 'codes'], ',');
			if (!empty($vouchercodes)) {
				$vouchercodes = explode(',', $vouchercodes);
				$voucount = count($vouchercodes);
			}



			if (empty($usability)) {// using type is only one time
				$avail_total = empty($voucount) ? '0' : $voucount;


				// If product is variable and id's are not blank then update their quantity with blank
				if ($product_type == 'variable') {
					$product = wc_get_product( $new_product_id );
					$variation_ids = $product->get_children();

					// set flag false
					$variable_code_flag = false;

					if(!empty($variation_ids)) {
						foreach( $variation_ids as $variation_id_key => $variation_id ) {
							$variable_is_downloadable = get_post_meta($variation_id, '_downloadable', true);
							$variable_codes = get_post_meta($variation_id, $prefix . 'codes', true);
	
							if ($variable_is_downloadable == 'yes' && !empty($variable_codes)) {
									// if variation is set as downloadable and vochers codes set at variation level
									$variable_code_flag = true;
							}
						}
					}

					if ($variable_code_flag == true) {
						// mark this product as variable voucher so we consider it to take vouchers from variations 
						update_post_meta($new_product_id, $prefix . 'is_variable_voucher', '1');
					} else {
						update_post_meta($new_product_id, $prefix . 'is_variable_voucher', '');
					}

					// default variable auto enable is true
					$variable_auto_enable = true;

					// get auto download option
					$disable_variations_auto_downloadable = get_option('vou_disable_variations_auto_downloadable');
					if ($disable_variations_auto_downloadable == 'yes') { // if disable option
						$variable_auto_enable = false;
					}

					// disable auto enable
					$auto_enable = apply_filters('woo_vou_auto_enable_downloadable_variations', $variable_auto_enable, $new_product_id);

					if(!empty($variation_ids)) {
						foreach( $variation_ids as $variation_id_key => $variation_id ) {
	
							if ($variable_code_flag != true) { // if there no voucher codes set on variation level
								// get voucher codes
								$var_vou_codes = get_post_meta($variation_id, $prefix . 'codes', true);
	
								if ($auto_enable || !empty($var_vou_codes)) {
									// update variation manage stock as no
									update_post_meta($variation_id, '_manage_stock', 'no');
	
									// Update variation stock qty with blank
									update_post_meta($variation_id, '_stock', '');
	
									// Update variation downloadable with yes
									update_post_meta($variation_id, '_downloadable', 'yes');
								}
							} else {
								//update manage stock with yes
								update_post_meta($variation_id, '_manage_stock', 'yes');
	
								$variable_voucount = '';
								$variable_codes = get_post_meta($variation_id, $prefix . 'codes', true);
	
								$vouchercodes = trim($variable_codes, ',');
								if (!empty($vouchercodes)) {
									$vouchercodes = explode(',', $vouchercodes);
									$variable_voucount = count($vouchercodes);
								}
	
								$variable_avail_total = empty($variable_voucount) ? '0' : $variable_voucount;
								//update available count on bases of 
								//update_post_meta( $variable_post, '_stock', $variable_avail_total );
								wc_update_product_stock($variation_id, $variable_avail_total);
							}
						}
					}
				}

				 $enabled_stock_mgmt = get_option( 'woocommerce_manage_stock' );
				// When product is variable and global manage stock is disable then no need to update stock
				// To resolve out of stock when adding new variation
				if( $product_type == 'variable' && $enabled_stock_mgmt == "no" ) {} else {
					//update manage stock with yes
					update_post_meta($new_product_id, '_manage_stock', 'yes');
				}

				//update available count on bases of 
				//update_post_meta( $new_product_id, '_stock', $avail_total );
				wc_update_product_stock($new_product_id, $avail_total);
			}
    }

    //update location and map links
    $availlocations = array();
    if (isset($wcfm_products_manage_form_data['avail_locations'])) {

			$locations = $wcfm_products_manage_form_data['avail_locations'];
			for ($i = 0; $i < count($locations); $i++) {
				if (!empty($locations[$i]) || !empty($maplinks[$i])) { //if location or map link is not empty then
					$availlocations[$i][$prefix . 'locations'] = $woo_vou_model->woo_vou_escape_slashes_deep($locations[$i][$prefix . 'locations'], true, true);
					$availlocations[$i][$prefix . 'map_link'] = $woo_vou_model->woo_vou_escape_slashes_deep($locations[$i][$prefix . 'map_link']);
				}
			}
    }

    //update location and map links
    update_post_meta($new_product_id, $prefix . 'avail_locations', $availlocations);
	}
	
	function wcfmu_wc_pdf_vouchers_product_variation_save( $new_product_id, $variation_id, $variations, $wcfm_products_manage_form_data ) {
	 	global $wpdb, $WCFM, $WCFMu;
	 	
	 	if( isset( $variations['_woo_vou_variable_pdf_template'] ) ) {
	 		update_post_meta( $variation_id, '_woo_vou_pdf_template', $variations['_woo_vou_variable_pdf_template'] );
	 		update_post_meta( $variation_id, '_woo_vou_voucher_delivery', $variations['_woo_vou_variable_voucher_delivery'] );
	 		update_post_meta( $variation_id, '_woo_vou_codes', $variations['_woo_vou_variable_codes'] );
	 		update_post_meta( $variation_id, '_woo_vou_vendor_address', $variations['_woo_vou_variable_vendor_address'] );
	 	}
	}
}