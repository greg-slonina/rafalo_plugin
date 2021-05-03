jQuery(document).ready(function($) {
		// Collapsible
	$('.page_collapsible').collapsible({
		defaultOpen: 'wcfm_om_shipping_head',
		speed: 'slow',
		loadOpen: function (elem) { //replace the standard open state with custom function
				elem.next().show();
		},
		loadClose: function (elem, opts) { //replace the close state with custom function
				elem.next().hide();
		},
		animateOpen: function(elem, opts) {
			$('.collapse-open').addClass('collapse-close').removeClass('collapse-open');
			elem.addClass('collapse-open');
			$('.collapse-close').find('span').removeClass('fa-arrow-circle-o-right block-indicator');
			elem.find('span').addClass('fa-arrow-circle-o-right block-indicator');
			$('.wcfm-tabWrap').find('.wcfm-container').stop(true, true).slideUp(opts.speed);
			elem.next().stop(true, true).slideDown(opts.speed);
		},
		animateClose: function(elem, opts) {
			elem.find('span').removeClass('fa-arrow-circle-o-up block-indicator');
			elem.next().stop(true, true).slideUp(opts.speed);
		}
	});
	$('.page_collapsible').each(function() {
		$(this).html('<div class="page_collapsible_content_holder">' + $(this).html() + '</div>');
		$(this).find('.page_collapsible_content_holder').after( $(this).find('span') );
	});
	$('.page_collapsible').find('span').addClass('fa');
	$('.collapse-open').addClass('collapse-close').removeClass('collapse-open');
	$('.wcfm-tabWrap').find('.wcfm-container').hide();
	setTimeout(function() {
		$('.wcfm-tabWrap').find('.page_collapsible:first').click();
	}, 500 );
	
	// Tabheight  
	$('.page_collapsible').each(function() {
		if( !$(this).hasClass('wcfm_head_hide') ) {
			collapsHeight += $(this).height() + 50;
		}
	}); 
	
	if( $("#associate_products").length > 0 ) {
		$("#associate_products").find('.associate_product').select2( $wcfm_simple_product_select_args );
	}
	
	$('#associate_products').find('.add_multi_input_block').click(function() {
	  $('#associate_products').find('.multi_input_block:last').find('.associate_product').val('').select2( $wcfm_simple_product_select_args );
	  $('#associate_products').find('.multi_input_block:last').find('.associate_product_variation').html('').addClass('wcfm_ele_hide');
		$('#associate_products').find('.multi_input_block:last').find('.associate_product_variation_label').addClass('wcfm_ele_hide');
		$('#associate_products').find('.multi_input_block:last').find('.associate_product_qty').val('1');
		variationSelectProperty( $("#associate_products").find('.associate_product') );
	});
	
	variationSelectProperty( $('#associate_products').find('.multi_input_block:last').find('.associate_product') );
	
	// Check is Variable Product
	function variationSelectProperty( $element ) {
		$element.on('change', function() {
			$associate_product = $(this);
			$selected_product = $(this).val();
			$variations_html  = '';
			if( $selected_product ) {
				jQuery.each( $wcfm_search_products_list, function( id, product ) {
					if( $selected_product == id ) {
						$variations = product.variations;
						if( !jQuery.isEmptyObject( $variations ) ) {
							$.each($variations, function( $variation_id, $variation_label ) {
								$variations_html += '<option value="' + $variation_id + '">' + $variation_label + '</option>';
							});
							$associate_product.parent().find('.associate_product_variation').html($variations_html).removeClass('wcfm_ele_hide');
							$associate_product.parent().find('.associate_product_variation_label').removeClass('wcfm_ele_hide');
						} else {
							$associate_product.parent().find('.associate_product_variation').html($variations_html).addClass('wcfm_ele_hide');
							$associate_product.parent().find('.associate_product_variation_label').addClass('wcfm_ele_hide');
						}
					}
				});
			} else {
				$associate_product.parent().find('.associate_product_variation').html($variations_html).addClass('wcfm_ele_hide');
				$associate_product.parent().find('.associate_product_variation_label').addClass('wcfm_ele_hide');
			}
		});
	}
	
	$("#customer_id").select2({ });
	
	// Load Customer Address
	$("#customer_id").on('change', function() {
		$('#wcfm_customer_address_expander').block({
			message: null,
			overlayCSS: {
				background: '#fff',
				opacity: 0.6
			}
		});
		
		$customer_id = $("#customer_id").val();
		var data = {
			action      : 'wcfm_orders_manage_customer_address',
			customer_id : $customer_id
		}	
		
		jQuery.ajax({
			type    :		'POST',
			url     : wcfm_params.ajax_url,
			data    : data,
			success :	function(response) {
				$.each(response, function( addr_field, addr_value ) {
					$('#'+addr_field).val(addr_value);
				});
				$('#wcfm_customer_address_expander').unblock();
			}
		});
	});
	
	$('#sadd_as_billing').on('change', function() {
    if( $(this).is(':checked') ) {
    	$('.store_shipping_address_wrap').addClass('wcfm_ele_hide');
    } else {
    	$('.store_shipping_address_wrap').removeClass('wcfm_ele_hide');
    }
    resetCollapsHeight($('#bfirst_name').parent());
  }).change();
		
  $('.wcfm_order_add_new_customer_box .wcfm_order_add_new_customer').click(function(event) {
		event.preventDefault();
		jQueryquick_edit = $(this);
		
		// Ajax Call for Fetching Quick Edit HTML
		$('#wcfm_orders_manage_expander').block({
			message: null,
			overlayCSS: {
				background: '#fff',
				opacity: 0.6
			}
		});
		var data = {
			action  : 'wcfm_order_add_customer_html'
		}	
		
		jQuery.ajax({
			type    :		'POST',
			url     : wcfm_params.ajax_url,
			data    : data,
			success :	function(response) {
				// Intialize colorbox
				jQuery.colorbox( { html: response, height: 525, width: $popup_width,
					onComplete:function() {
				
						// Intialize Quick Update Action
						$('#wcfm_order_add_customer_button').click(function() {
							$wcfm_is_valid_form = true;
							$('#wcfm_order_add_customer_form').block({
								message: null,
								overlayCSS: {
									background: '#fff',
									opacity: 0.6
								}
							});
							$('.wcfm-message').html('').removeClass('wcfm-error').removeClass('wcfm-success').slideUp();
							
							$( document.body ).trigger( 'wcfm_form_validate', $('#wcfm_order_add_customer_form') );
							if( !$wcfm_is_valid_form ) {
								wcfm_notification_sound.play();
								$('#wcfm_order_add_customer_form').unblock();
							} else {
								$('.wcfm_order_add_customer_button').hide();
								var data = {
									action : 'wcfm_ajax_controller',
									controller : 'wcfm-orders-manage-add-customer', 
									wcfm_order_add_customer_form : $('#wcfm_order_add_customer_form').serialize()
								}	
								jQuery.post(wcfm_params.ajax_url, data, function(response) {
									if(response) {
										jQueryresponse_json = jQuery.parseJSON(response);
										$('.wcfm-message').html('').removeClass('wcfm-error').removeClass('wcfm-success').slideUp();
										if(jQueryresponse_json.status) {
											$('#wcfm_order_add_customer_form .wcfm-message').html('<span class="wcicon-status-completed"></span>' + jQueryresponse_json.message).addClass('wcfm-success').slideDown();
											if( $('#wcfm_orders_manage_expander').find('#customer_id').length > 0 ) {
												$('#wcfm_orders_manage_expander').find('#customer_id').append('<option value="' + jQueryresponse_json.customer_id + '" selected>#' + jQueryresponse_json.customer_id + ' ' + jQueryresponse_json.username  + '</option>');
												$('#wcfm_orders_manage_expander').find('#customer_id').change();
												$('#wcfm_orders_manage_expander').find('#customer_id').trigger('chosen:updated'); 
											}
											setTimeout(function() {
												jQuery.colorbox.remove();
											}, 2000);
										} else {
											$('#wcfm_order_add_customer_form .wcfm-message').html('<span class="wcicon-status-cancelled"></span>' + jQueryresponse_json.message).addClass('wcfm-error').slideDown();
										}
										wcfm_notification_sound.play();
										$('.wcfm_order_add_customer_button').show();
										$('#wcfm_order_add_customer_form').unblock();
									}
								} );
							}
						});
					}
				});
				$('#wcfm_orders_manage_expander').unblock();
			}
		});
		
		return false;
  });
  
  function wcfm_orders_manage_form_validate() {
		$is_valid = true;
		$('.wcfm-message').html('').removeClass('wcfm-error').slideUp();
		var associate_product = $.trim($('#wcfm_orders_manage_form').find('#associate_products').find('.associate_product:first').val());
		if(associate_product.length == 0) {
			$is_valid = false;
			$('#wcfm_orders_manage_form .wcfm-message').html('<span class="wcicon-status-cancelled"></span>' + wcfm_orders_manage_messages.no_product).addClass('wcfm-error').slideDown();
			audio.play();
		}
		return $is_valid;
	}
  
  // Submit Order
	$('#wcfm_orders_manage_submit_button').click(function(event) {
	  event.preventDefault();
	  
	  // Validations
	  $is_valid = wcfm_orders_manage_form_validate();
	  if( $is_valid ) {
			$wcfm_is_valid_form = true;
			$( document.body ).trigger( 'wcfm_form_validate', $('#wcfm_orders_manage_form') );
			$is_valid = $wcfm_is_valid_form;
		}
	  
	  if($is_valid) {
			$('#wcfm-content').block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
			var data = {
				action                   : 'wcfm_ajax_controller',
				controller               : 'wcfm-orders-manage',
				wcfm_orders_manage_form : $('#wcfm_orders_manage_form').serialize(),
				status                   : 'submit'
			}	
			$.post(wcfm_params.ajax_url, data, function(response) {
				if(response) {
					$response_json = $.parseJSON(response);
					$('.wcfm-message').html('').removeClass('wcfm-success').removeClass('wcfm-error').slideUp();
					wcfm_notification_sound.play();
					if($response_json.redirect) {
						$('#wcfm_orders_manage_form .wcfm-message').html('<span class="wcicon-status-completed"></span>' + $response_json.message).addClass('wcfm-success').slideDown( "slow", function() {
						  if( $response_json.redirect ) window.location = $response_json.redirect;	
						} );
					} else {
						$('#wcfm_orders_manage_form .wcfm-message').html('<span class="wcicon-status-cancelled"></span>' + $response_json.message).addClass('wcfm-error').slideDown();
					}
					if($response_json.id) $('#customer_id').val($response_json.id);
					wcfmMessageHide();
					$('#wcfm-content').unblock();
				}
			});
		}
	});
});