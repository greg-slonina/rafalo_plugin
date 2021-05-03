jQuery(document).ready(function($) {
	// Third Party Plugin Support - WC Rental & Booking
	$( '#pricing_type' ).change(function() {
		$('.rentel_pricing').addClass('wcfm_ele_hide');
		$('.rental_' + $(this).val() ).removeClass('wcfm_ele_hide');
		resetCollapsHeight($('#pricing_type'));
	}).change();
	
	$( document.body ).on( 'wcfm_product_type_changed', function() {
		if( $('#product_type').val() == 'redq_rental' ) {
			$('#pricing_type').change();
			resetCollapsHeight($('.collapse-open').next('.wcfm-container').find('.wcfm_ele:not(.wcfm_title):first'));
		}
	});
	if( $('#product_type').val() == 'redq_rental' ) {
		$('#pricing_type').change();
		resetCollapsHeight($('.collapse-open').next('.wcfm-container').find('.wcfm_ele:not(.wcfm_title):first'));
	}
	
	$("#redq_rental_off_days").select2();
	
	$('#redq_inventory_products').find('select:not(.wcfm_half_ele)').each(function() { $(this).select2({ placeholder: $(this).data('placeholder') }); } );
	setTimeout(function() {
		$('#redq_inventory_products').find('.add_multi_input_block').click(function() {
			$('#redq_inventory_products').find('.multi_input_block:last').find('select:not(.wcfm_half_ele)').each(function() {  $(this).select2(); } );
		});
		
		$('#redq_inventory_products').find('.redq_rental_availability').find('.add_multi_input_block').click(function() {
			resetCollapsHeight($('#redq_inventory_products'));
		});
	}, 1000 );
	
	// Creating Inventory Collapser
	$('#redq_inventory_products').children('.multi_input_block').each(function() {
		$multi_input_block = $(this);
		$multi_input_block.prepend('<div class="wcfm_clearfix"></div>');
		$multi_input_block.prepend('<span class="fields_collapser variations_collapser wcfmfa fa-arrow-circle-down"></span>');
	});
	
	// Inventory Collapser
	$('#redq_inventory_products').children('.multi_input_block').children('.add_multi_input_block').click(function() {
	  $('#redq_inventory_products').children('.multi_input_block').children('.variations_collapser').each(function() {
			$(this).off('click').on('click', function() {
				$(this).parent().find('.wcfm_ele:not(.redq_rental_unique_name), .wcfm_title:not(.redq_rental_unique_name), .select2-container').toggleClass('variation_ele_hide');
				$(this).toggleClass('fa-arrow-circle-up');
				resetCollapsHeight($('#redq_inventory_products'));
			} );
			$(this).parent().find('.wcfm_ele:not(.redq_rental_unique_name), .wcfm_title:not(.redq_rental_unique_name), .select2-container').addClass('variation_ele_hide');
			$(this).removeClass('fa-arrow-circle-up');
			resetCollapsHeight($('#redq_inventory_products'));
		} );
		$('#redq_inventory_products').children('.multi_input_block:last').children('.variations_collapser').click();
		$('#redq_inventory_products').children('.multi_input_block:last').find('select:not(.wcfm_half_ele)').each(function() { $(this).val('').select2({ placeholder: $(this).data('placeholder') }); } );
	});
	$('#redq_inventory_products').children('.multi_input_block').children('.variations_collapser').each(function() {
		$(this).addClass('fa-arrow-circle-up');
		$(this).off('click').on('click', function() {
			$(this).parent().find('.wcfm_ele:not(.redq_rental_unique_name), .wcfm_title:not(.redq_rental_unique_name), .select2-container').toggleClass('variation_ele_hide');
			$(this).toggleClass('fa-arrow-circle-up');
			resetCollapsHeight($('#redq_inventory_products'));
		} ).click();
	} );
	
	// Global Settings Cloppaser
	$('.redq_rental_settings_type').each(function() {
	  $(this).change(function() {
	    $setting_type = $(this).val();
	    if( $setting_type == 'global' ) {
	    	$(this).parent().find('.redq_rental_settings_block_local').addClass('wcfm_custom_hide');
	    } else {
	    	$(this).parent().find('.redq_rental_settings_block_local').removeClass('wcfm_custom_hide');
	    }
	    resetCollapsHeight($('.redq_rental_settings_block'));
	  }).change();
	});
	
} );