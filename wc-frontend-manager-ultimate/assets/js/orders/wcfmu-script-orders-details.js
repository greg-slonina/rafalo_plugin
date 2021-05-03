jQuery(document).ready(function($) {
	// PDF Invoice
	$('.wcfm_pdf_invoice').each(function() {
		$(this).click(function(event) {
			event.preventDefault();
			downloadPDFInvoiceWCFMOrder($(this));
			return false;
		});
	});
	
	function downloadPDFInvoiceWCFMOrder(item) {
		if (wcfm_params.ajax_url.indexOf("?") != -1) {
			url = wcfm_params.ajax_url + '&action=wcfm_order_pdf_invoice&template_type=invoice&order_id='+item.data('orderid');
		} else {
			url = wcfm_params.ajax_url + '?action=wcfm_order_pdf_invoice&template_type=invoice&order_id='+item.data('orderid')
		}
		window.open(url,'_blank');
	}
	
	// PDF Packing Slip
	$('.wcfm_pdf_packing_slip').each(function() {
		$(this).click(function(event) {
			event.preventDefault();
			downloadPDFPackingSlipWCFMOrder($(this));
			return false;
		});
	});
	
	function downloadPDFPackingSlipWCFMOrder(item) {
		if (wcfm_params.ajax_url.indexOf("?") != -1) {
			url = wcfm_params.ajax_url + '&action=wcfm_order_pdf_packing_slip&template_type=packing_slip&order_id='+item.data('orderid');
		} else {
			url = wcfm_params.ajax_url + '?action=wcfm_order_pdf_packing_slip&template_type=packing-slip&order_id='+item.data('orderid')
		}
		window.open(url, '_blank');
	}
	
	// Order Add Note
	$('#wcfm_add_order_note').click(function(event) {
		event.preventDefault();
		addWCFMOrderNote();
		return false;
	});
		
	function addWCFMOrderNote() {
		$('#wcfm_add_order_note_form').block({
			message: null,
			overlayCSS: {
				background: '#fff',
				opacity: 0.6
			}
		});
		var data = {
			action      : 'wcfm_add_order_note',
			note        : $('#add_order_note').val(),
			note_data   : $('#wcfm_add_order_note_form').serialize()
		}	
		$.ajax({
			type:		'POST',
			url: wcfm_params.ajax_url,
			data: data,
			success:	function(response) {
				$('#notes_holder').append(response);
				$('#add_order_note').val('');
				$('#wcfm_add_order_note_form').unblock();
			}
		});
	}
	
	$('.wcfm_order_mark_shipped').each(function() {
		$(this).click(function(event) {
			event.preventDefault();
			manageVendorShippingTracking( $(this) );
			return false;
		});
	});
	
	function manageVendorShippingTracking( item ) {
		var data = {
							  action  : 'wcfmu_shipment_tracking_html',
							  orderid       : item.data('orderid'),
								productid     : item.data('productid'),
								orderitemid   : item.data('orderitemid'),
							}
		jQuery.ajax({
			type    :		'POST',
			url     : wcfm_params.ajax_url,
			data    : data,
			success :	function(response) {
														 
				// Intialize colorbox
				$.colorbox( { html: response, height: 400, width: $popup_width,
					onComplete:function() {
						$('#wcfm_tracking_button').click(function(e) {
							e.preventDefault();
							
							$('#wcfm_shipping_tracking_form').block({
									message: null,
									overlayCSS: {
										background: '#fff',
										opacity: 0.6
									}
								});
							
							jQuery( document.body ).trigger( 'wcfm_form_validate', jQuery('#wcfm_shipping_tracking_form') );
							if( !$wcfm_is_valid_form ) {
								wcfm_notification_sound.play();
								jQuery('#wcfm_shipping_tracking_form').unblock();
							} else {
								var tracking_url  = $('#wcfm_tracking_url').val();
								var tracking_code = $('#wcfm_tracking_code').val();
								
								item.hide();
								
								$('#wcfm_tracking_button').hide();
								$('#wcfm_shipping_tracking_form .wcfm-message').html('').removeClass('wcfm-error').removeClass('wcfm-success').slideUp();
								
								var data = {
									action        : item.data('shipped_action'),
									orderid       : item.data('orderid'),
									productid     : item.data('productid'),
									orderitemid   : item.data('orderitemid'),
									tracking_url  : tracking_url,
									tracking_code : tracking_code,
									tracking_data : $('#wcfm_shipping_tracking_form').serialize()
								}	
								$.ajax({
									type:		'POST',
									url: wcfm_params.ajax_url,
									data: data,
									success:	function(response) {
										wcfm_notification_sound.play();
										$('#wcfm_shipping_tracking_form').unblock();
										$('#wcfm_shipping_tracking_form .wcfm-message').html( '<span class="wcicon-status-completed"></span>' + wcfm_shipping_tracking_labels.tracking_saved ).addClass('wcfm-success').slideDown();
										setTimeout(function() {
											$.colorbox.remove();
											if( !window.location.hash ) {
												window.location = window.location.href + '#sm_order_shipment_options';
											}
											window.location.reload();
										}, 2000);
									}
								});
							}
						});
					}
				});
			}
		});
	}
	
	
	
	// Order Edit
	$('.wcfm_order_edit_request').each(function() {
		$(this).click(function(event) {
			event.preventDefault();
			$order_id = $(this).data('order');
			initEditOrderPopup( $order_id );
		});
	});
	
	function initEditOrderPopup( $order_id ) {
		
		var data = {
			action        : 'wcfm_edit_order_form_html',
			order_id      : $order_id,
		}	
		
		jQuery.ajax({
			type    :		'POST',
			url     : wcfm_params.ajax_url,
			data    : data,
			success :	function(response) {
				// Intialize colorbox
				jQuery.colorbox( { html: response, width: $popup_width,
					onComplete:function() {
						
						initOrderEditInputEle();
						
						// Intialize Quick Update Action
						jQuery('#wcfm_order_edit_submit_button').click(function(event) {
							event.preventDefault();
							jQuery('#wcfm_order_edit_form').block({
								message: null,
								overlayCSS: {
									background: '#fff',
									opacity: 0.6
								}
							});
							jQuery('.wcfm-message').html('').removeClass('wcfm-error').removeClass('wcfm-success').slideUp();
							var data = {
								action                 : 'wcfm_ajax_controller',
								controller             : 'wcfm-orders-edit', 
								wcfm_orders_edit_form  : jQuery('#wcfm_order_edit_form').serialize()
							}	
							jQuery.post(wcfm_params.ajax_url, data, function(response) {
								if(response) {
									jQueryresponse_json = jQuery.parseJSON(response);
									jQuery('.wcfm-message').html('').removeClass('wcfm-error').removeClass('wcfm-success').slideUp();
									wcfm_notification_sound.play();
									if(jQueryresponse_json.status) {
										jQuery('#wcfm_order_edit_form .wcfm-message').html('<span class="wcicon-status-completed"></span>' + jQueryresponse_json.message).addClass('wcfm-success').slideDown();
										jQuery('#wcfm_order_edit_submit_button').hide();
										window.location.reload();
										setTimeout(function() {
											jQuery.colorbox.remove();
										}, 2000);
									} else {
										jQuery('#wcfm_order_edit_form .wcfm-message').html('<span class="wcicon-status-cancelled"></span>' + jQueryresponse_json.message).addClass('wcfm-error').slideDown();
									}
									jQuery('#wcfm_order_edit_form').unblock();
								}
							} );
							return false;
						});
					}
				});
			}
		});
	}
	
	function initOrderEditInputEle() {
		$('.wcfm_order_edit_input_qty').each(function() {
			$(this).change(function() {
				$item_id = $(this).data('item');
				$input_qty = $(this).val();
				$order_line_item = $('.order_line_item_'+$item_id);
				
				$item_cost = $(this).data('item_cost');
				$total_cost = $item_cost * $input_qty;
				$order_line_item.find('.wcfm_order_edit_input_total').val($total_cost);
				
				$order_line_item.find('.wcfm_order_edit_input_tax').each(function() {
					$tax_cost = $(this).data('item_tax');
					$total_tax = $tax_cost * $input_qty;
					$(this).val($total_tax);
				});
			});
		});
	}
});