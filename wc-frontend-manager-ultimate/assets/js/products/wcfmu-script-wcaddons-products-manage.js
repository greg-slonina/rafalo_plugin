jQuery(document).ready(function($) {
	$( document.body ).on( 'wcfm_product_type_changed', function() {
  	$('.wcaddons').removeClass('wcfm_ele_hide wcfm_block_hide wcfm_head_hide');
  });
  
  // WC Addons Tab Height manage
  $('#product_addons_data').find('select').addClass('wcfm-select');
  $('#product_addons_data').find('input[type="checkbox"]').addClass('wcfm-checkbox').css( 'margin-right', '10px' );
	$('#product_addons_data').find('input[type="checkbox"]').parent().css( 'width', '200px' );
	$('#product_addons_data').find('.wc-pao-remove-addon').css( 'padding', '5px' );
	$('#product_addons_data').find('h2.wc-pao-addon-name').css( 'display', 'inline' );
	$('#product_addons_data').find('h2.wc-pao-addon-name').css( 'float', 'none' );
  $('.wc-pao-expand-all, .wc-pao-close-all, .wc-pao-import-addons, .wc-pao-export-addons' ).click(function () {
  	setTimeout(function() {
  		resetCollapsHeight($('#product_addons_data'));
  	}, 500 );
	});
	$( document.body ).on( 'init_tooltips', function() {
		resetCollapsHeight($('#product_addons_data'));
		$('#product_addons_data').find('select').addClass('wcfm-select');
		$('#product_addons_data').find('input[type="text"]').addClass('wcfm-text');
		$('#product_addons_data').find('input[type="number"]').addClass('wcfm-text');
		$('#product_addons_data').find('input[type="checkbox"]').addClass('wcfm-checkbox').css( 'margin-right', '10px' );
		//$('#product_addons_data').find('input[type="checkbox"]').parent().css( 'width', '200px' );
		$('#product_addons_data').find('.wc-pao-remove-addon').css( 'padding', '5px' );
		$('#product_addons_data').find('h2.wc-pao-addon-name').css( 'display', 'inline' );
		$('#product_addons_data').find('h2.wc-pao-addon-name').css( 'float', 'none' );
		$('#product_addons_data').find('.wc-pao-addon-content-option-rows').addClass('style_if_appointment');
	});
	function initWCaddonHeight() {
		$('.wc-pao-addon-header, .wc-pao-addon-toggle, .wc-pao-add-option' ).click(function () {
			setTimeout(function() {
				$('#product_addons_data').find('select').addClass('wcfm-select');
				$('#product_addons_data').find('input[type="text"]').addClass('wcfm-text');
				$('#product_addons_data').find('input[type="number"]').addClass('wcfm-text');
				$('#product_addons_data').find('input[type="checkbox"]').addClass('wcfm-checkbox').css( 'margin-right', '10px' );
				resetCollapsHeight($('#product_addons_data'));
			}, 500 );
		});
	}
  $( document ).ajaxComplete(function() {
		initWCaddonHeight();
	});
  
  // Addons Field Controller
	function addonFields() {
		$('#_product_addons').find('.multi_input_block').each(function() {
			$(this).find('.addon_fields_option').change(function() {
				$addon_fields_option = $(this).val();
				$(this).parent().find('.addon_fields').addClass('wcfm_ele_hide');
				if( $addon_fields_option == 'input_multiplier' || $addon_fields_option == 'custom_textarea' || $addon_fields_option == 'custom' || $addon_fields_option == 'custom_letters_only' || $addon_fields_option == 'custom_letters_or_digits' || $addon_fields_option == 'custom_digits_only' ) {
					$(this).parent().find('.addon_price').removeClass('wcfm_ele_hide');
					$(this).parent().find('.addon_minmax').removeClass('wcfm_ele_hide');
				} else if( $addon_fields_option == 'checkbox' || $addon_fields_option == 'custom_email' || $addon_fields_option == 'file_upload' || $addon_fields_option == 'radiobutton' || $addon_fields_option == 'select' ) {
					$(this).parent().find('.addon_price').removeClass('wcfm_ele_hide');
				} else if( $addon_fields_option == 'custom_price' ) {
					$(this).parent().find('.addon_minmax').removeClass('wcfm_ele_hide');
				}
			}).change();
		});
	}
	addonFields();
	$('#_product_addons').find('.add_multi_input_block').click(function() {
	  addonFields();
	  // Fields Collaper
		$('#_product_addons').find('.fields_collapser').each(function() {
			$(this).off('click').on('click', function() {
				$(this).parent().parent().parent().find('.multi_input_holder').toggleClass('wcfm_ele_hide');
				$(this).toggleClass('fa-arrow-circle-up');
				resetCollapsHeight($('#_product_addons'));
			} );
		} );
	});
	// Fields Collapser
	$('.wcfm_title').find('.fields_collapser').each(function() {
		$(this).addClass('fa-arrow-circle-up');
		$(this).off('click').on('click', function() {
			$(this).parent().parent().parent().find('.multi_input_holder').toggleClass('wcfm_ele_hide');
			$(this).toggleClass('fa-arrow-circle-up');
			resetCollapsHeight($('#_product_addons'));
		} ).click();
	} );
});