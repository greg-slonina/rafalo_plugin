jQuery(document).ready(function($) {
	if( $('#wcfm_product_popup_container').length > 0 ) { 
		// Select wrapper fix
		function unwrapSelect() {
			$('#wcfm_product_popup_container').find('select').each(function() {
				if ( $(this).parent().is( "span" ) ) {
					$(this).unwrap( "span" );
				}
				if ( $(this).parent().hasClass( "select-option" ) || $(this).parent().hasClass( "buddyboss-select-inner" ) || $(this).parent().hasClass( "buddyboss-select" ) ) {
					$(this).parent().find('.ti-angle-down').remove();
					$(this).parent().find('span').remove();
					$(this).unwrap( "div" );
				}
			});
			setTimeout( function() {  unwrapSelect(); }, 500 );
		}
		
		setTimeout( function() { 
			$('#wcfm_product_popup_container').find('select').each(function() {
				if ( $(this).parent().is( "span" ) ) {
				 $(this).css( 'padding', '5px' ).css( 'min-width', '15px' ).css( 'min-height', '35px' ).css( 'padding-top', '5px' ).css( 'padding-right', '5px' ); //.change();
				}
			});
			unwrapSelect();
		}, 500 );
		
		$product_popup_width = '75%';
		if( jQuery(window).width() <= 960 ) {
			$product_popup_width = '95%';
		}
		
		$active_product_field = 'products';
		$('.wcfm-add-product').each(function() {
			$(this).click(function( event ) {
				event.preventDefault();
				$active_product_field = $(this).data('product_field');
				if( !$active_product_field ) $active_product_field = 'products';
				jQuery.colorbox( { inline:true, href: "#wcfm_product_popup_container", height: 525, width: $product_popup_width,
					onComplete:function() {
						$('#wcfm_product_popup_container').find('.wcfm-collapse-content').attr('id', 'wcfm-main-contentainer');
					}
				});
				$('#wcfm-main-contentainer').find('#pro_title').focus();
				return false;
			});
		});
		
		
		function wcfm_products_manage_form_validate( $is_publish ) {
			product_form_is_valid = true;
			$('.wcfm-message').html('').removeClass('wcfm-error').removeClass('wcfm-success').slideUp();
			var title = $.trim($('#wcfm_products_manage_form').find('#pro_title').val());
			if(title.length == 0) {
				product_form_is_valid = false;
				$('#wcfm_products_manage_form .wcfm-message').html('<span class="wcicon-status-cancelled"></span>' + wcfm_products_manage_messages.no_title).addClass('wcfm-error').slideDown();
				wcfm_notification_sound.play();
			}
			
			if( $is_publish ) {
				$( document.body ).trigger( 'wcfm_products_manage_form_validate', $('#wcfm_products_manage_form') );
				
				$wcfm_is_valid_form = product_form_is_valid;
				$( document.body ).trigger( 'wcfm_form_validate', $('#wcfm_products_manage_form') );
				product_form_is_valid = $wcfm_is_valid_form;
			}
			
			return product_form_is_valid;
		}
		
		// Draft Product
		$('#wcfm_products_simple_draft_button').off('click').on('click', function(event) {
			event.preventDefault();
			// Validations
			$is_valid = wcfm_products_manage_form_validate(false);
			
			if($is_valid) {
				$('#wcfm-main-contentainer').block({
					message: null,
					overlayCSS: {
						background: '#fff',
						opacity: 0.6
					}
				});
				
				var excerpt = '';
				if( $('#excerpt').hasClass('rich_editor') ) {
					if( tinymce.get('excerpt') != null ) excerpt = tinymce.get('excerpt').getContent({format: 'raw'});
				} else {
					excerpt = $('#excerpt').val();
				}
				
				var description = '';
				if( $('#description').hasClass('rich_editor') ) {
					if( tinymce.get('description') != null ) description = tinymce.get('description').getContent({format: 'raw'});
				} else {
					description = $('#description').val();
				}
				var data = {
					action : 'wcfm_ajax_controller',
					controller : 'wcfm-products-manage', 
					wcfm_products_manage_form : $('#wcfm_products_manage_form').serialize(),
					excerpt     : excerpt,
					description : description,
					status : 'draft',
					removed_variations : removed_variations,
					removed_person_types : removed_person_types
				}	
				$.post(wcfm_params.ajax_url, data, function(response) {
					if(response) {
						$response_json = $.parseJSON(response);
						$('.wcfm-message').html('').removeClass('wcfm-error').removeClass('wcfm-success').slideUp();
						wcfm_notification_sound.play();
						if($response_json.status) {
							$('#wcfm_products_manage_form .wcfm-message').html('<span class="wcicon-status-completed"></span>' + $response_json.message).addClass('wcfm-success').slideDown( "slow", function() {
								//if( $response_json.redirect ) window.location = $response_json.redirect;	
								$('#wcfm-content').unblock();
							} );
						} else {
							$('#wcfm_products_manage_form .wcfm-message').html('<span class="wcicon-status-cancelled"></span>' + $response_json.message).addClass('wcfm-error').slideDown();
						}
						if($response_json.id) $('#pro_id').val($response_json.id);
						wcfmMessageHide();
						$('#wcfm-main-contentainer').unblock();
					}
				});	
			}
		});
		
		// Submit Product
		$('#wcfm_products_simple_submit_button').off('click').on('click', function(event) {
			event.preventDefault();
			
			// Validations
			$is_valid = wcfm_products_manage_form_validate(true);
			
			if($is_valid) {
				$('#wcfm-main-contentainer').block({
					message: null,
					overlayCSS: {
						background: '#fff',
						opacity: 0.6
					}
				});
				
				var excerpt = '';
				if( $('#excerpt').hasClass('rich_editor') ) {
					if( tinymce.get('excerpt') != null ) excerpt = tinymce.get('excerpt').getContent({format: 'raw'});
				} else {
					excerpt = $('#excerpt').val();
				}
				
				var description = '';
				if( $('#description').hasClass('rich_editor') ) {
					if( tinymce.get('description') != null ) description = tinymce.get('description').getContent({format: 'raw'});
				} else {
					description = $('#description').val();
				}
				
				var data = {
					action : 'wcfm_ajax_controller',
					controller : 'wcfm-products-manage',
					wcfm_products_manage_form : $('#wcfm_products_manage_form').serialize(),
					excerpt     : excerpt,
					description : description,
					status : 'submit',
					removed_variations : removed_variations,
					removed_person_types : removed_person_types
				}	
				$.post(wcfm_params.ajax_url, data, function(response) {
					if(response) {
						$response_json = $.parseJSON(response);
						$('.wcfm-message').html('').removeClass('wcfm-error').removeClass('wcfm-success').slideUp();
						wcfm_notification_sound.play();
						if($response_json.status) {
							$('#wcfm_products_manage_form .wcfm-message').html('<span class="wcicon-status-completed"></span>' + $response_json.message).addClass('wcfm-success').slideDown( "slow", function() {
								//if( $response_json.redirect ) window.location = $response_json.redirect;
								if( $('#submit-job-form').find('#'+$active_product_field).length > 0 ) {
									$('#submit-job-form').find("#"+$active_product_field).append('<option value="' + $response_json.id + '" selected>' + $response_json.title  + '</option>');
									$('#submit-job-form').find('#'+$active_product_field).change();
									$('#submit-job-form').find("#"+$active_product_field).trigger('chosen:updated'); 
								}
								setTimeout(function() {
									$.colorbox.remove();
								}, 2000);
								
								// Reset Product Form
								$('#wcfm_products_manage_form').find('#pro_title').val('');
								$('#wcfm_products_manage_form').find('#excerpt').val('');
								$('#wcfm_products_manage_form').find('#description').val('');
								//$('#wcfm_products_manage_form').find('.wcfm-text, .wcfm-select').val('');
								$('#pro_id').val(0);
								
								$( document.body ).trigger( 'wcfm_listings_products_manage_form_save' );
							} );
						} else {
							$('#wcfm_products_manage_form .wcfm-message').html('<span class="wcicon-status-cancelled"></span>' + $response_json.message).addClass('wcfm-error').slideDown();
						}
						wcfmMessageHide();
						$('#wcfm-main-contentainer').unblock();
					}
				});
			}
		});
		
	}
});