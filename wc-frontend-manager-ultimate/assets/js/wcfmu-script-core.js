//var $ = jQuery.noConflict();
function intiateWCFMuQuickEdit() {
  jQuery('.wcfmu_product_quick_edit').each( function() {
  	jQuery(this).click( function( event ) {
			event.preventDefault();
			jQueryquick_edit = jQuery(this);
			jQueryproduct = jQueryquick_edit.data('product');
			
			// Ajax Call for Fetching Quick Edit HTML
			jQuery('.products').block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
			var data = {
				action  : 'wcfmu_quick_edit_html',
				product : jQueryproduct
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
							jQuery('#wcfm_quick_edit_button').click(function() {
								$wcfm_is_valid_form = true;
								jQuery('#wcfm_quick_edit_form').block({
									message: null,
									overlayCSS: {
										background: '#fff',
										opacity: 0.6
									}
								});
								jQuery('.wcfm-message').html('').removeClass('wcfm-error').removeClass('wcfm-success').slideUp();
								if( jQuery('input[name=wcfm_quick_edit_title]').val().length == 0 ) {
									//alert(wcfmu_products_manage_messages.no_title);
									jQuery('#wcfm_quick_edit_form .wcfm-message').html('<span class="wcicon-status-cancelled"></span>' + wcfmu_products_manage_messages.no_title).addClass('wcfm-error').slideDown();
									wcfm_notification_sound.play();
									jQuery('#wcfm_quick_edit_form').unblock();
								} else {
									jQuery( document.body ).trigger( 'wcfm_form_validate', jQuery('#wcfm_quick_edit_form') );
									if( !$wcfm_is_valid_form ) {
										wcfm_notification_sound.play();
										jQuery('#wcfm_quick_edit_form').unblock();
									} else {
										var data = {
											action : 'wcfm_ajax_controller',
											controller : 'wcfm-products-quick-manage', 
											wcfm_quick_edit_form : jQuery('#wcfm_quick_edit_form').serialize()
										}	
										jQuery.post(wcfm_params.ajax_url, data, function(response) {
											if(response) {
												jQueryresponse_json = jQuery.parseJSON(response);
												jQuery('.wcfm-message').html('').removeClass('wcfm-error').removeClass('wcfm-success').slideUp();
												if(jQueryresponse_json.status) {
													jQuery('#wcfm_quick_edit_form .wcfm-message').html('<span class="wcicon-status-completed"></span>' + jQueryresponse_json.message).addClass('wcfm-success').slideDown();
													jQuery('#wcfm_quick_edit_button').hide();
												} else {
													jQuery('#wcfm_quick_edit_form .wcfm-message').html('<span class="wcicon-status-cancelled"></span>' + jQueryresponse_json.message).addClass('wcfm-error').slideDown();
												}
												wcfm_notification_sound.play();
												jQuery('#wcfm_quick_edit_form').unblock();
												setTimeout(function() {
													if($wcfm_products_table) $wcfm_products_table.ajax.reload();
													jQuery.colorbox.remove();
												}, 2000);
											}
										} );
									}
								}
							});
						}
					});
					jQuery('.products').unblock();
				}
			});
			
			return false;
		} );
  } );
}

function intiateWCFMuScreenManager() {
	jQuery('.wcfm_screen_manager').each( function() {
  	jQuery(this).click( function( event ) {
			event.preventDefault();
			jQueryScreen_Manager = jQuery(this);
			jQueryScreen = jQueryScreen_Manager.data('screen');
			
			var data = {
				action  : 'wcfmu_screen_manager_html',
				screen  : jQueryScreen
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
							jQuery('#wcfm_screen_manager_button').click(function() {
								jQuery('#wcfm_screen_manager_form').block({
									message: null,
									overlayCSS: {
										background: '#fff',
										opacity: 0.6
									}
								});
								jQuery('.wcfm-message').html('').removeClass('wcfm-error').removeClass('wcfm-success').slideUp();
								var data = {
									action : 'wcfm_ajax_controller',
									controller : 'wcfm-screen-manage', 
									wcfm_screen_manager_form : jQuery('#wcfm_screen_manager_form').serialize()
								}	
								jQuery.post(wcfm_params.ajax_url, data, function(response) {
									if(response) {
										jQueryresponse_json = jQuery.parseJSON(response);
										jQuery('.wcfm-message').html('').removeClass('wcfm-error').removeClass('wcfm-success').slideUp();
										if(jQueryresponse_json.status) {
											jQuery('#wcfm_screen_manager_form .wcfm-message').html('<span class="wcicon-status-completed"></span>' + jQueryresponse_json.message).addClass('wcfm-success').slideDown();
										} else {
											jQuery('#wcfm_screen_manager_form .wcfm-message').html('<span class="wcicon-status-cancelled"></span>' + jQueryresponse_json.message).addClass('wcfm-error').slideDown();
										}
										jQuery('#wcfm_screen_manager_form').unblock();
										setTimeout(function() {
											jQuery.colorbox.remove();
											window.location = window.location.href; 
										}, 2000);
									}
								} );
							});
						}
					});
				}
			});
			
			return false;
		} );
  } );
}

jQuery( document ).ready( function( $ ) {
	intiateWCFMuQuickEdit();
	intiateWCFMuScreenManager();
	
	// Order item Mark as Received
	$('.wcfm_mark_as_recived').click(function(e) {
	  e.preventDefault();
	  $(this).hide();
	  $('.woocommerce-order-details').block({
			message: null,
			overlayCSS: {
				background: '#fff',
				opacity: 0.6
			}
		});
		var data = {
			action       : 'wcfm_mark_as_recived',
			orderid      : $(this).data('orderid'),
			productid    : $(this).data('productid'),
			orderitemid  : $(this).data('orderitemid'),
		}	
		$.ajax({
			type:		'POST',
			url: wcfm_params.ajax_url,
			data: data,
			success:	function(response) {
				window.location = window.location.href;
				$('.woocommerce-order-details').unblock();
			}
		});
	});
} );