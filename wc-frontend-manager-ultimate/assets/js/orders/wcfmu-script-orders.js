jQuery(document).ready(function($) {
	// PDF Invoice
	$( document.body ).on( 'updated_wcfm-orders', function() {
		$('.wcfm_pdf_invoice').each(function() {
			$(this).click(function(event) {
				event.preventDefault();
				downloadPDFInvoiceWCFMOrder($(this));
				return false;
			});
		});
	});
	
	function downloadPDFInvoiceWCFMOrder(item) {
		if (wcfm_params.ajax_url.indexOf("?") != -1) {
			url = wcfm_params.ajax_url + '&action=wcfm_order_pdf_invoice&template_type=invoice&order_id='+item.data('orderid')+'&vendor_id='+item.data('vendorid');
		} else {
			url = wcfm_params.ajax_url + '?action=wcfm_order_pdf_invoice&template_type=invoice&order_id='+item.data('orderid')+'&vendor_id='+item.data('vendorid');
		}
		window.open(url, '_blank');
	}
	
	// PDF Packing Slip
	$( document.body ).on( 'updated_wcfm-orders', function() {
		$('.wcfm_pdf_packing_slip').each(function() {
			$(this).click(function(event) {
				event.preventDefault();
				downloadPDFPackingSlipWCFMOrder($(this));
				return false;
			});
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
		
	// Delete Order
	$( document.body ).on( 'updated_wcfm-orders', function() {
		$('.wcfm_order_delete').each(function() {
			$(this).click(function(event) {
				event.preventDefault();
				var rconfirm = confirm( wcfm_dashboard_messages.order_delete_confirm );
				if(rconfirm) deleteWCFMOrder($(this));
				return false;
			});
		});
	});
	
	function deleteWCFMOrder(item) {
		$('#wcfm-orders_wrapper').block({
			message: null,
			overlayCSS: {
				background: '#fff',
				opacity: 0.6
			}
		});
		var data = {
			action : 'delete_wcfm_order',
			orderid : item.data('orderid')
		}	
		$.ajax({
			type:		'POST',
			url: wcfm_params.ajax_url,
			data: data,
			success:	function(response) {
				$wcfm_orders_table.ajax.reload();
				$('#wcfm-orders_wrapper').unblock();
			}
		});
	}
	
	$( document.body ).on( 'updated_wcfm-orders', function() {
		//WC Vendors Mark Order Shipped
		$('.wcfm_wcvendors_order_mark_shipped').each(function() {
			$(this).click(function(event) {
				event.preventDefault();
				manageVendorShippingTracking( $(this), 'wcfm_wcvendors_order_mark_shipped' );
				return false;
			});
		});
		
		// WC Product Vendors Mark Order Shipped
		$('.wcfm_wcpvendors_order_mark_fulfilled').each(function() {
			$(this).click(function(event) {
				event.preventDefault();
				manageVendorShippingTracking( $(this), 'wcfm_wcpvendors_order_mark_fulfilled' );
				return false;
			});
		});
		
		// WC Marketplace Mark Order Shipped
		$('.wcfm_wcmarketplace_order_mark_shipped').each(function() {
			$(this).click(function(event) {
				event.preventDefault();
				manageVendorShippingTracking( $(this), 'wcfm_wcmarketplace_order_mark_shipped' );
				return false;
			});
		});
		
		// WCfM Marketplace Mark Order Shipped
		$('.wcfm_wcfmmarketplace_order_mark_shipped').each(function() {
			$(this).click(function(event) {
				event.preventDefault();
				manageVendorShippingTracking( $(this), 'wcfm_wcfmmarketplace_order_mark_shipped' );
				return false;
			});
		});
		
		// Dokan Mark Order Shipped
		$('.wcfm_dokan_order_mark_shipped').each(function() {
			$(this).click(function(event) {
				event.preventDefault();
				manageVendorShippingTracking( $(this), 'wcfm_dokan_order_mark_shipped' );
				return false;
			});
		});
	});
	
	function manageVendorShippingTracking( item, mark_shipped_action ) {
		
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
					
								$('#wcfm_tracking_button').hide();
								$('#wcfm_shipping_tracking_form .wcfm-message').html('').removeClass('wcfm-error').removeClass('wcfm-success').slideUp();
								
								var data = {
									action        : mark_shipped_action,
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
										$wcfm_orders_table.ajax.reload();
										$('#wcfm_shipping_tracking_form').unblock();
										$('#wcfm_shipping_tracking_form .wcfm-message').html( '<span class="wcicon-status-completed"></span>' + wcfm_shipping_tracking_labels.tracking_saved ).addClass('wcfm-success').slideDown();
										setTimeout(function() {
											$.colorbox.remove();
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
} );