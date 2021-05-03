jQuery( document ).ready( function( $ ) {
		
	$('.bulk_action_checkbox_all').click(function() {
		if( $(this).is(':checked') ) {
			$('.bulk_action_checkbox_all').attr( 'checked', true );
			$('.bulk_action_checkbox_single').attr( 'checked', true );
		}	else {
			$('.bulk_action_checkbox_all').attr( 'checked', false );
			$('.bulk_action_checkbox_single').attr( 'checked', false );
		}
	});
		
	// Bulk Edit
	$('#wcfm_bulk_edit').click( function( event ) {
		event.preventDefault();
		
		$selected_products = [];
		$('.bulk_action_checkbox_single').each(function() {
		  if( $(this).is(':checked') ) {
		  	$selected_products.push( $(this).val() );
		  }
		});
		
		if ( $selected_products.length === 0 ) {
			alert( wcfm_dashboard_messages.bulk_no_itm_selected );
			return false;
		}
		
		var data = {
			action            : 'wcfmu_bulk_edit_html',
			selected_products : $selected_products
		}	
		
		$.ajax({
			type    :		'POST',
			url     : wcfm_params.ajax_url,
			data    : data,
			success :	function(response) {
				if( response ) {
					// Intialize colorbox
					$.colorbox( { html: response, width: $popup_width,
						onComplete:function() {
				
							$( '#wcfm_bulk_edit_form' ).on( 'change', '#woocommerce-fields-bulk .inline-edit-group .change_to', function() {
								if ( 0 < $( this ).val() ) {
									$( this ).closest( 'div' ).find( '.change-input' ).show().css( 'display', 'inline-block' );
								} else {
									$( this ).closest( 'div' ).find( '.change-input' ).hide();
								}
							});
							
							// Intialize Quick Update Action
							$('#wcfm_bulk_edit_button').click(function() {
								$('#wcfm_bulk_edit_form').block({
									message: null,
									overlayCSS: {
										background: '#fff',
										opacity: 0.6
									}
								});
								$('.wcfm-message').html('').removeClass('wcfm-error').removeClass('wcfm-success').slideUp();
								var data = {
									action : 'wcfm_ajax_controller',
									controller : 'wcfm-products-bulk-manage', 
									wcfm_bulk_edit_form : $('#wcfm_bulk_edit_form').serialize()
								}	
								$.post(wcfm_params.ajax_url, data, function(response) {
									if(response) {
										$response_json = $.parseJSON(response);
										$('.wcfm-message').html('').removeClass('wcfm-error').removeClass('wcfm-success').slideUp();
										if($response_json.status) {
											$('#wcfm_bulk_edit_button').hide();
											$('#wcfm_bulk_edit_form .wcfm-message').html('<span class="wcicon-status-completed"></span>' + $response_json.message).addClass('wcfm-success').slideDown();
										} else {
											$('#wcfm_bulk_edit_form .wcfm-message').html('<span class="wcicon-status-cancelled"></span>' + $response_json.message).addClass('wcfm-error').slideDown();
										}
										$('#wcfm_bulk_edit_form').unblock();
										setTimeout(function() {
											if($wcfm_products_table) $wcfm_products_table.ajax.reload();
											$.colorbox.remove();
										}, 2000);
									}
								} );
							});
						}
					});
				}
			}
		});
		
		return false;
	} );
	
	// Bulk Delete
	$('#wcfm_bulk_delete').click( function( event ) {
		event.preventDefault();
		
		$selected_products = [];
		$('.bulk_action_checkbox_single').each(function() {
		  if( $(this).is(':checked') ) {
		  	$selected_products.push( $(this).val() );
		  }
		});
		
		if ( $selected_products.length === 0 ) {
			alert( wcfm_dashboard_messages.bulk_no_itm_selected );
			return false;
		}
		
		var rconfirm = confirm(wcfm_dashboard_messages.product_delete_confirm);
		if(rconfirm) {
		
			var data = {
				action            : 'wcfmu_bulk_delete',
				selected_products : $selected_products
			}	
			
			$.ajax({
				type    :		'POST',
				url     : wcfm_params.ajax_url,
				data    : data,
				success :	function(response) {
					if( response ) {
						if($wcfm_products_table) $wcfm_products_table.ajax.reload();
					}
				}
			});
			
		}
	});
} );