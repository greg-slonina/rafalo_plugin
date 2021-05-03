jQuery(document).ready(function($) {
	// Third Party Plugin Support - Toolset Types - 3.1.7
	$( document.body ).on( 'wcfm_product_type_changed', function() {
	  wcfm_toolset_products_manager_filter();
	});
	
	// Product Type wise Toolset Field Groups Filtering - 3.1.7
	function wcfm_toolset_products_manager_filter() {
		if( $(".wcfm_toolset_products_manage_container").length > 0 ) {
			var product_type = $('#product_type').val();
			$has_toolset = false;
			$('.wcfm_toolset_products_manage_collapsible').addClass('wcfm_toolset_hide wcfm_block_hide');
			$('.wcfm_toolset_products_manage_container').addClass('wcfm_toolset_hide wcfm_block_hide');
			$.each( wcfm_product_type_toolset_fields, function( product_type_toolset, allowed_toolset_groups ) {
				if( product_type == product_type_toolset ) {
					$.each( allowed_toolset_groups, function( index, allowed_toolset_group ) {
						$('.wcfm_toolset_products_manage_' + allowed_toolset_group + '_collapsible').removeClass('wcfm_toolset_hide wcfm_block_hide');
						$('.wcfm_toolset_products_manage_' +allowed_toolset_group + '_container').removeClass('wcfm_toolset_hide wcfm_block_hide');
						$has_toolset = true;
					} );	
				}
			} );
			if( !$has_toolset ) {
				$('.wcfm_toolset_products_manage_collapsible').removeClass('wcfm_toolset_hide wcfm_block_hide');
				$('.wcfm_toolset_products_manage_container').removeClass('wcfm_toolset_hide wcfm_block_hide');
			}
			resetCollapsHeight($('.collapse-open').next('.wcfm-container').find('.wcfm_ele:not(.wcfm_title):first'));
		}
	}
	
	wcfm_toolset_products_manager_filter();
	
} );