jQuery(document).ready(function($) {
	$('#_woo_vou_disable_redeem_day').select2();
	$('#_woo_vou_recipient_delivery_email_recipient').select2({ containerCssClass : "_woo_vou_enable_recipient_delivery_ele _woo_vou_enable_recipient_delivery_email_ele" });
	$('#_woo_vou_recipient_delivery_offline_recipient').select2({ containerCssClass : "_woo_vou_enable_recipient_delivery_ele _woo_vou_enable_recipient_delivery_offline_ele" });
	
  $('#_woo_vou_enable_recipient_name').change(function() {
    if( $(this).is(':checked') ) {
    	$('._woo_vou_enable_recipient_name_ele').removeClass('wcfm_ele_hide');
    } else {
    	$('._woo_vou_enable_recipient_name_ele').addClass('wcfm_ele_hide');
    }
    resetCollapsHeight($('#_woo_vou_enable_recipient_name'));
  }).change();
  
   $('#_woo_vou_enable_recipient_email').change(function() {
    if( $(this).is(':checked') ) {
    	$('._woo_vou_enable_recipient_email_ele').removeClass('wcfm_ele_hide');
    } else {
    	$('._woo_vou_enable_recipient_email_ele').addClass('wcfm_ele_hide');
    }
    resetCollapsHeight($('#_woo_vou_enable_recipient_name'));
  }).change();
  
   $('#_woo_vou_enable_recipient_message').change(function() {
    if( $(this).is(':checked') ) {
    	$('._woo_vou_enable_recipient_message_ele').removeClass('wcfm_ele_hide');
    } else {
    	$('._woo_vou_enable_recipient_message_ele').addClass('wcfm_ele_hide');
    }
    resetCollapsHeight($('#_woo_vou_enable_recipient_name'));
  }).change();
  
   $('#_woo_vou_enable_recipient_giftdate').change(function() {
    if( $(this).is(':checked') ) {
    	$('._woo_vou_enable_recipient_giftdate_ele').removeClass('wcfm_ele_hide');
    } else {
    	$('._woo_vou_enable_recipient_giftdate_ele').addClass('wcfm_ele_hide');
    }
    resetCollapsHeight($('#_woo_vou_enable_recipient_name'));
  }).change();
  
  $('#_woo_vou_enable_recipient_delivery_method').change(function() {
    if( $(this).is(':checked') ) {
    	$('._woo_vou_enable_recipient_delivery_ele').removeClass('wcfm_ele_hide');
    } else {
    	$('._woo_vou_enable_recipient_delivery_ele').addClass('wcfm_ele_hide');
    }
    resetCollapsHeight($('#_woo_vou_recipient_delivery_label'));
  }).change();
  
  $('#_woo_vou_recipient_delivery_email_enable').change(function() {
    if( $(this).is(':checked') ) {
    	$('._woo_vou_enable_recipient_delivery_email_ele').removeClass('wcfm_custom_hide');
    } else {
    	$('._woo_vou_enable_recipient_delivery_email_ele').addClass('wcfm_custom_hide');
    }
    resetCollapsHeight($('#_woo_vou_recipient_delivery_label'));
  }).change();
  
  $('#_woo_vou_recipient_delivery_offline_enable').change(function() {
    if( $(this).is(':checked') ) {
    	$('._woo_vou_enable_recipient_delivery_offline_ele').removeClass('wcfm_custom_hide');
    } else {
    	$('._woo_vou_enable_recipient_delivery_offline_ele').addClass('wcfm_custom_hide');
    }
    resetCollapsHeight($('#_woo_vou_recipient_delivery_label'));
  }).change();
  
  if( $('#_woo_vou_product_start_date').length > 0 ) {
		var startDateTextBox = $('#_woo_vou_product_start_date');
		var endDateTextBox = $('#_woo_vou_product_exp_date');
	
		$.timepicker.datetimeRange(
				startDateTextBox,
				endDateTextBox,
				{
						minInterval: (1000*60*60), // 1hr
						ampm: true,
						dateFormat: startDateTextBox.data( 'date_format' ),
						timeFormat: 'HH:mm',
				}
		);
	}
	
	$('#_woo_vou_pdf_template_selection').select2({ containerCssClass : "_woo_vou_enable_pdf_template_selection_ele" });
	
	$('#_woo_vou_enable_pdf_template_selection').change(function() {
    if( $(this).is(':checked') ) {
    	$('._woo_vou_enable_pdf_template_selection_non_ele').addClass('wcfm_ele_hide');
    	$('._woo_vou_enable_pdf_template_selection_ele').removeClass('wcfm_ele_hide');
    } else {
    	$('._woo_vou_enable_pdf_template_selection_non_ele').removeClass('wcfm_ele_hide');
    	$('._woo_vou_enable_pdf_template_selection_ele').addClass('wcfm_ele_hide');
    }
    resetCollapsHeight($('#_woo_vou_enable_recipient_name'));
  }).change();
  
  if( !$('#_woo_vou_sec_vendor_users').hasClass('wcfm_ele_hide') ) { 
  	$('#_woo_vou_sec_vendor_users').select2();
  }
  
  $('#_woo_vou_days_diff').change(function() {
  	$_woo_vou_days_diff = $(this).val();
  	$('.woo_vou_days_diff_custom_ele').addClass('wcfm_ele_hide');
  	$('.woo_vou_days_diff_custom_non_ele').addClass('wcfm_ele_hide');
  	if( $_woo_vou_days_diff == 'cust' ) {
  		$('.woo_vou_days_diff_custom_ele').removeClass('wcfm_ele_hide');
  	} else {
  		$('.woo_vou_days_diff_custom_non_ele').removeClass('wcfm_ele_hide');
  	}
  	resetCollapsHeight($('#_woo_vou_enable_recipient_name'));
  }).change();
  
  $('#_woo_vou_exp_type').change(function() {
  	$_woo_vou_exp_type = $(this).val();
  	$('.specific_date_ele').addClass('wcfm_ele_hide');
  	$('.based_on_purchase_ele').addClass('wcfm_ele_hide');
  	$('.woo_vou_days_diff_custom_non_ele').addClass('wcfm_ele_hide');
  	if( $_woo_vou_exp_type == 'specific_date' ) {
  		$('.specific_date_ele').removeClass('wcfm_ele_hide');
  	} else if( $_woo_vou_exp_type == 'based_on_purchase' ) {
  		$('.based_on_purchase_ele').removeClass('wcfm_ele_hide');
  		$('.woo_vou_days_diff_custom_non_ele').removeClass('wcfm_ele_hide');
  	} else {
  		
  	}
  	resetCollapsHeight($('#_woo_vou_enable_recipient_name'));
  }).change();
  
  if( $('#_woo_vou_start_date').length > 0 ) {
		var woo_vou_start_date = $('#_woo_vou_start_date');
		var woo_vou_exp_date = $('#_woo_vou_exp_date');
	
		$.timepicker.datetimeRange(
				woo_vou_start_date,
				woo_vou_exp_date,
				{
						minInterval: (1000*60*60), // 1hr
						ampm: true,
						dateFormat: woo_vou_start_date.data('date_format'),
						timeFormat: 'HH:mm',
				}
		);
	}
	
	// Generate Voucher Codes
	$('.wcfm_voucher_code_popup').click(function(event) {
	  event.preventDefault();
	  
	  var data = {
			action  : 'wcfm_generate_voucher_code_html',
		}	
		
		jQuery.ajax({
			type    :		'POST',
			url     : wcfm_params.ajax_url,
			data    : data,
			success :	function(response) {
				// Intialize colorbox
				jQuery.colorbox( { html: response, width: $popup_width,
					onComplete:function() {
				
						// Intialize Quick Update Action
						jQuery('#wcfm_generate_voucher_code_button').click(function() {
							jQuery('#wcfm_generate_voucher_code_form').block({
								message: null,
								overlayCSS: {
									background: '#fff',
									opacity: 0.6
								}
							});
							jQuery('.wcfm-message').html('').removeClass('wcfm-error').removeClass('wcfm-success').slideUp();
							
							var existing_code = $('#_woo_vou_codes').val();
							var delete_code = '';
							var no_of_voucher = $( 'input[name=woo-vou-no-of-voucher]' ).val();
							var code_prefix = $( 'input[name=woo-vou-code-prefix]' ).val();
							var code_seperator = $( 'input[name=woo-vou-code-seperator]' ).val();
							var code_pattern = $( 'input[name=woo-vou-code-pattern]' ).val();
							
							if( no_of_voucher == '' ) {
								jQuery('#wcfm_generate_voucher_code_form .wcfm-message').html('<span class="wcicon-status-cancelled"></span>' + WooVouMeta.noofvouchererror).addClass('wcfm-error').slideDown();
								jQuery('#wcfm_generate_voucher_code_form').unblock();
							} else if( code_pattern == '' ) {
								jQuery('#wcfm_generate_voucher_code_form .wcfm-message').html('<span class="wcicon-status-cancelled"></span>' + WooVouMeta.patternemptyerror).addClass('wcfm-error').slideDown();
								jQuery('#wcfm_generate_voucher_code_form').unblock();
							} else if( code_pattern.indexOf('l') == '-1' && code_pattern.indexOf('d') == '-1' && code_pattern.indexOf('L') == '-1' && code_pattern.indexOf('D') == '-1' ) {
								jQuery('#wcfm_generate_voucher_code_form .wcfm-message').html('<span class="wcicon-status-cancelled"></span>' + WooVouMeta.generateerror).addClass('wcfm-error').slideDown();
								jQuery('#wcfm_generate_voucher_code_form').unblock();
							} else {
								var data = {
									action			  : 'woo_vou_import_code',
									noofvoucher		: no_of_voucher,
									codeprefix		: code_prefix,
									codeseperator	: code_seperator,
									codepattern		: code_pattern,
									existingcode	: existing_code,
									deletecode		: delete_code
								};
								jQuery.post(wcfm_params.ajax_url, data, function(response) {
									if(response) {
										var import_code = response;
										$( '#_woo_vou_codes' ).val(import_code);
										jQuery('.wcfm-message').html('').removeClass('wcfm-error').removeClass('wcfm-success').slideUp();
										jQuery('#wcfm_generate_voucher_code_form .wcfm-message').html('<span class="wcicon-status-completed"></span>' + WooVouMeta.vouchercodegenerated).addClass('wcfm-success').slideDown();
										jQuery('#wcfm_generate_voucher_code_form').unblock();
										setTimeout(function() {
											jQuery.colorbox.remove();
											if( $is_wcfm_product_popup_on ) {
												$('.wcfm_product_popup_button').click();
											}
										}, 2000);
									}
								} );
							}
						});
					}
				});
				jQuery('.products').unblock();
			}
		});
	});
	
	
	// Purchased Voucher Codes
	$pro_id = parseInt( $('#pro_id').val() );
	if( $pro_id != 0 ) {
		$('.woo_vou_purchased_codes_ele').removeClass( 'wcfm_ele_hide' );
	}
	$('.wcfm_voucher_purchased_code_popup').click(function(event) {
	  event.preventDefault();
	  
	  jQuery.colorbox( { html: jQuery('#wcfm-woo-vou-purchased-codes-popup').html(), width: $popup_width,
			onComplete:function() {
				woo_vou_hide_buyer_order_extra_fields();
				
				//on click of import coupon codes button, import code
				$( document ).on( "click", "#woo_vou_purchased_load_more_btn", function() {
					
					var purchased_post_id = $('#woo_vou_purchased_post_id').val();
					var purchased_paged = $('#woo_vou_purchased_paged').val();
					var purchased_postsperpage = $('#woo_vou_purchased_postsperpage').val();
					
					var data = {
							action					        : 'woo_vou_load_more_purchased_voucode',
							purchased_post_id		    : purchased_post_id,
							purchased_paged			    : purchased_paged,
							purchased_postsperpage	: purchased_postsperpage					
						};
		
					// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
					$.post( wcfm_params.ajax_url, data, function(response) {
		
						var response_data = jQuery.parseJSON(response);
		
						if( !response_data.norecfound ) {
							var purchased_code = response_data.html;
							$( '.woo-vou-purchased-codes-popup #woo_vou_purchased_codes_table' ).append( purchased_code );
							woo_vou_hide_buyer_order_extra_fields();
							$('#woo_vou_purchased_paged').val( parseInt(purchased_paged) + 1 );
						} else {
							$('#woo_vou_purchased_load_more_btn').hide();
						}
					});
				});
				
				//on click of show buyer button 
				$( document ).on( "click", ".woo-vou-purchased-codes-popup a.woo-vou-show-buyer", function() {
					var voucherid = $(this).data('voucherid');
					$(this).hide();
					$( '#buyer_voucher_'+voucherid+' .woo-vou-buyer-info-table .buyer_address').show();
					$( '#buyer_voucher_'+voucherid+' .woo-vou-buyer-info-table .buyer_phone').show();
				});
				//on click of show order button 
				$( document ).on( "click", ".woo-vou-purchased-codes-popup a.woo-vou-show-order", function() {
					var voucherid = $(this).data('voucherid');
					$(this).hide();
					$( '#order_voucher_'+voucherid+' .woo-vou-order-info-table .payment_method').show();
					$( '#order_voucher_'+voucherid+' .woo-vou-order-info-table .order_total').show();
					$( '#order_voucher_'+voucherid+' .woo-vou-order-info-table .order_discount').show();
				});
			}
		});
	});
	
	
	// Used Voucher Codes
	$pro_id = parseInt( $('#pro_id').val() );
	if( $pro_id != 0 ) {
		$('.woo_vou_used_codes_ele').removeClass( 'wcfm_ele_hide' );
	}
	$('.wcfm_voucher_used_codes_popup').click(function(event) {
	  event.preventDefault();
	  
	  jQuery.colorbox( { html: jQuery('#wcfm-woo-vou-used-codes-popup').html(), width: $popup_width,
			onComplete:function() {
				woo_vou_hide_buyer_order_extra_fields();
				
				//on click of import coupon codes button, import code
				$( document ).on( "click", "#woo_vou_used_load_more_btn", function() {
					
					var used_post_id = $('#woo_vou_used_post_id').val();
					var used_paged = $('#woo_vou_used_paged').val();
					var used_postsperpage = $('#woo_vou_used_postsperpage').val();		
			
					var data = {
							action				    : 'woo_vou_load_more_used_voucode',
							used_post_id		  : used_post_id,
							used_paged			  : used_paged,
							used_postsperpage	: used_postsperpage					
						};
		
					// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
					$.post( WooVouMeta.ajaxurl, data, function(response) {
		
						var response_data = jQuery.parseJSON(response);
		
						if( !response_data.norecfound ) {
		
							var used_code = response_data.html;
							$( '.woo-vou-used-codes-popup #woo_vou_used_codes_table' ).append( used_code );
							woo_vou_hide_buyer_order_extra_fields();
							$('#woo_vou_used_paged').val( parseInt(used_paged) + 1 );
						} else {
							$('#woo_vou_used_load_more_btn').hide();
						}
					});
				});
				
				//on click of show buyer button 
				$( document ).on( "click", ".woo-vou-used-codes-popup a.woo-vou-show-buyer", function() {
					var voucherid = $(this).data('voucherid');
					$(this).hide();
					$( '#buyer_voucher_'+voucherid+' .woo-vou-buyer-info-table .buyer_address').show();
					$( '#buyer_voucher_'+voucherid+' .woo-vou-buyer-info-table .buyer_phone').show();
				});
				//on click of show order button 
				$( document ).on( "click", ".woo-vou-used-codes-popup a.woo-vou-show-order", function() {
					var voucherid = $(this).data('voucherid');
					$(this).hide();
					$( '#order_voucher_'+voucherid+' .woo-vou-order-info-table .payment_method').show();
					$( '#order_voucher_'+voucherid+' .woo-vou-order-info-table .order_total').show();
					$( '#order_voucher_'+voucherid+' .woo-vou-order-info-table .order_discount').show();
				});
			}
		});
	});
	
	function woo_vou_hide_buyer_order_extra_fields(){
		$( '.woo-vou-purchased-codes-popup .woo-vou-buyer-info-table .buyer_address').hide();
		$( '.woo-vou-purchased-codes-popup .woo-vou-buyer-info-table .buyer_phone').hide();
		$( '.woo-vou-purchased-codes-popup .woo-vou-order-info-table .payment_method').hide();
		$( '.woo-vou-purchased-codes-popup .woo-vou-order-info-table .order_total').hide();
		$( '.woo-vou-purchased-codes-popup .woo-vou-order-info-table .order_discount').hide();

		$( '.woo-vou-used-codes-popup .woo-vou-buyer-info-table .buyer_address').hide();
		$( '.woo-vou-used-codes-popup .woo-vou-buyer-info-table .buyer_phone').hide();
		$( '.woo-vou-used-codes-popup .woo-vou-order-info-table .payment_method').hide();
		$( '.woo-vou-used-codes-popup .woo-vou-order-info-table .order_total').hide();
		$( '.woo-vou-used-codes-popup .woo-vou-order-info-table .order_discount').hide();
                
    $( '.woo-vou-unused-codes-popup .woo-vou-buyer-info-table .buyer_address').hide();
		$( '.woo-vou-unused-codes-popup .woo-vou-buyer-info-table .buyer_phone').hide();
		$( '.woo-vou-unused-codes-popup .woo-vou-order-info-table .payment_method').hide();
		$( '.woo-vou-unused-codes-popup .woo-vou-order-info-table .order_total').hide();
		$( '.woo-vou-unused-codes-popup .woo-vou-order-info-table .order_discount').hide();
		
		$( '.woo-vou-purchased-codes-popup a.woo-vou-show-buyer').show();
		$( '.woo-vou-used-codes-popup a.woo-vou-show-buyer').show();
    $( '.woo-vou-unused-codes-popup a.woo-vou-show-buyer').show();
		$( '.woo-vou-purchased-codes-popup a.woo-vou-show-order').show();
		$( '.woo-vou-used-codes-popup a.woo-vou-show-order').show();
    $( '.woo-vou-unused-codes-popup a.woo-vou-show-order').show();
	}
});