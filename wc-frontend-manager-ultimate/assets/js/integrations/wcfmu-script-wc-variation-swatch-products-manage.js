jQuery(document).ready(function($) {

	$.fn.wvs_pro_product_attribute_type = function (options) {
		return this.each(function () {
			var _this = this;

			var $wrapper = $(this).closest('.wvs-pro-variable-swatches-attribute-wrapper');

			var change_classes = function change_classes() {
				var value = $(_this).val();
				var visible_class = 'visible_if_' + value;

				var existing_classes = Object.keys(wvs_pro_product_variation_data.attribute_types).map(function (type) {
						return 'visible_if_' + type;
				}).join(' ');

				$wrapper.removeClass(existing_classes).removeClass('visible_if_custom').addClass(visible_class);
				return value;
			};

			$(this).on('change', function (e) {
				var value = change_classes();
				$wrapper.find('.wvs-pro-swatch-tax-type').val(value).trigger('change.taxonomy');
				resetCollapsHeight($('#wvs-pro-product-variable-swatches-options'));
			});

			$(this).on('change.attribute', function (e) {
				change_classes();
				resetCollapsHeight($('#wvs-pro-product-variable-swatches-options'));
			});
		});
	};

	$.fn.wvs_pro_product_taxonomy_type = function (options) {
		return this.each(function () {
			var _this2 = this;

			var $wrapper = $(this).closest('.wvs-pro-variable-swatches-attribute-tax-wrapper');
			var $main_wrapper = $(this).closest('.wvs-pro-variable-swatches-attribute-wrapper');

			var change_classes = function change_classes() {
				var value = $(_this2).val();
				var visible_class = 'visible_if_tax_' + value;

				var existing_classes = Object.keys(wvs_pro_product_variation_data.attribute_types).map(function (type) {
						return 'visible_if_tax_' + type;
				}).join(' ');

				$wrapper.removeClass(existing_classes).addClass(visible_class);
				return value;
			};

			$(this).on('change', function (e) {

				change_classes();

				var allValues = [];
				$main_wrapper.find('.wvs-pro-swatch-tax-type').each(function () {
						allValues.push($(this).val());
				});

				var uniqueValues = _.uniq(allValues);
				var is_all_tax_same = uniqueValues.length === 1;

				if (is_all_tax_same) {
						$main_wrapper.find('.wvs-pro-swatch-option-type').val(uniqueValues.toString()).trigger('change.attribute');
				} else {
						$main_wrapper.find('.wvs-pro-swatch-option-type').val('custom').trigger('change.attribute');
				}
				
				resetCollapsHeight($('#wvs-pro-product-variable-swatches-options'));
			});

			$(this).on('change.taxonomy', function (e) {
				change_classes();
				resetCollapsHeight($('#wvs-pro-product-variable-swatches-options'));
			});
		});
	};

	$.fn.wvs_pro_product_taxonomy_item_tooltip_type = function (options) {
		return this.each(function () {
			var _this3 = this;

			var $wrapper = $(this).closest('tbody');

			var change_classes = function change_classes() {
				var value = $(_this3).val();
				var visible_class = 'visible_if_item_tooltip_type_' + value;

				var existing_classes = ['', 'text', 'image', 'no'].map(function (type) {
						return 'visible_if_item_tooltip_type_' + type;
				}).join(' ');

				$wrapper.find('.wvs-pro-item-tooltip-type-item').removeClass(existing_classes).addClass(visible_class);
				
				return value;
			};

			$(this).on('change', function (e) {
				change_classes();
				resetCollapsHeight($('#wvs-pro-product-variable-swatches-options'));
			});
		
			$(this).trigger('change');
		});
	};

	$('.wvs-pro-swatch-option-type').wvs_pro_product_attribute_type();
	$('.wvs-pro-swatch-tax-type').wvs_pro_product_taxonomy_type();
	$('.wvs-pro-item-tooltip-type').wvs_pro_product_taxonomy_item_tooltip_type();
	
	jQuery('.wvs-color-picker').wpColorPicker();
	
	$('.variable-swatches-attribute-header').click(function() {
		$(this).parent().find('.variable-swatches-attribute-data').toggleClass('wcfm_custom_hide');
		resetCollapsHeight($('#wvs-pro-product-variable-swatches-options'));
	});
});