jQuery(document).ready(function ($) {
	var set_index = 0;
	var rule_indexes = new Array();

	$('.woocommerce_pricing_ruleset').each(function () {
		var length = $('table tbody tr', $(this)).length;
		if (length == 1) {
				$('.delete_pricing_rule', $(this)).hide();
		}
		
		$(this).find('label').each(function() {
			var content = $(this).html();
			$(this).replaceWith( '<span class="wcfm_title" style="float:left;"><strong>'+content+'</strong></span>' );
		});
		$(this).find('select').addClass('wcfm-select');
		$(this).find('input[type="text"]').addClass('wcfm-text');
		$(this).find('input[type="number"]').addClass('wcfm-text');
		$(this).find('.short').addClass('wcfm_small_ele');
		$(this).find('.multiselect').select2();
		$(this).find('input[type="checkbox"]').addClass('wcfm-checkbox').css( 'margin-right', '10px' );
		$(this).find('table').find('.wcfm-text').css( 'width', '100%' );
		$(this).find('table').find('.wcfm-select').css( 'width', '100%' );
	});


	$("#woocommerce-pricing-add-ruleset").click(function (event) {
		event.preventDefault();

		var set_index = $("#woocommerce-pricing-rules-wrap").data('setindex') + 1;
		$("#woocommerce-pricing-rules-wrap").data('setindex', set_index);

		var data = {
			set_index: set_index,
			post: wcfm_dynamic_pricing_args.product_id,
			action: 'create_empty_ruleset'
		};

		$.post(ajaxurl, data, function (response) {
			$('#woocommerce-pricing-rules-wrap').append(response);
			
			$('#woocommerce-pricing-rules-wrap').children('.woocommerce_pricing_ruleset:last').find('label').each(function() {
				var content = $(this).html();
				$(this).replaceWith( '<span class="wcfm_title" style="float:left;"><strong>'+content+'</strong></span>' );
			});
			$('#woocommerce-pricing-rules-wrap').children('.woocommerce_pricing_ruleset:last').find('select').addClass('wcfm-select');
			$('#woocommerce-pricing-rules-wrap').children('.woocommerce_pricing_ruleset:last').find('input[type="text"]').addClass('wcfm-text');
			$('#woocommerce-pricing-rules-wrap').children('.woocommerce_pricing_ruleset:last').find('input[type="number"]').addClass('wcfm-text');
			$('#woocommerce-pricing-rules-wrap').children('.woocommerce_pricing_ruleset:last').find('.short').addClass('wcfm_small_ele');
			$('#woocommerce-pricing-rules-wrap').children('.woocommerce_pricing_ruleset:last').find('.multiselect').select2();
			$('#woocommerce-pricing-rules-wrap').children('.woocommerce_pricing_ruleset:last').find('input[type="checkbox"]').addClass('wcfm-checkbox').css( 'margin-right', '10px' );
			$('#woocommerce-pricing-rules-wrap').children('.woocommerce_pricing_ruleset:last').find('table').find('.wcfm-text').css( 'width', '100%' );
			$('#woocommerce-pricing-rules-wrap').children('.woocommerce_pricing_ruleset:last').find('table').find('.wcfm-select').css( 'width', '100%' );
			resetCollapsHeight($('#woocommerce-pricing-rules-wrap'));
			
			$(document.body).trigger('wc-enhanced-select-init');
		});
	});

	$('#woocommerce-pricing-rules-wrap').delegate('.pricing_rule_apply_to', 'change', function (event) {
		var value = $(this).val();
		if (value != 'roles' && $('.roles', $(this).parent()).is(':visible')) {
			$('.roles', $(this).parent()).fadeOut();
			$('.roles input[type=checkbox]', $(this).closest('div')).removeAttr('checked');
		}

		if (value == 'roles') {
			$('.roles', $(this).parent()).fadeIn();
		}
	});

	$('#woocommerce-pricing-rules-wrap').delegate('.pricing_rule_variations', 'change', function (event) {
		var value = $(this).val();
		if (value != 'variations') {
			$('.variations', $(this).parent()).fadeOut();
			$('.variations input[type=checkbox]', $(this).closest('div')).removeAttr('checked');
		} else {
			$('.variations', $(this).parent()).fadeIn();
		}
	});


	$('#woocommerce-pricing-rules-wrap').delegate('.pricing_rule_when', 'change', function (event) {
		var value = $(this).val();
		if (value != 'cat') {
			$('.cats', $(this).closest('div')).fadeOut();
			$('.cats input[type=checkbox]', $(this).closest('div')).removeAttr('checked');

		} else {
			$('.cats', $(this).closest('div')).fadeIn();
		}
	});

	$('#woocommerce-pricing-rules-wrap').delegate('.pricing_rule_mode', 'change', function (event) {
		var value = $(this).val();
		if (value != 'block') {
			$('table.block', $(this).closest('div.woocommerce_pricing_ruleset')).parent().fadeOut('fast', function () {
					$('table.continuous', $(this).closest('div.woocommerce_pricing_ruleset')).parent().fadeIn();
			});
		} else {

			$('table.continuous', $(this).closest('div.woocommerce_pricing_ruleset')).parent().fadeOut('fast', function () {
					$('table.block', $(this).closest('div.woocommerce_pricing_ruleset')).parent().fadeIn();
			});
		}
	});

	//Remove Pricing Set
	$('#woocommerce-pricing-rules-wrap').delegate('.delete_pricing_ruleset', 'click', function (event) {
		event.preventDefault();
		DeleteRuleSet($(this).data('name'));
	});

	//Add Button
	$('#woocommerce-pricing-rules-wrap').delegate('.add_pricing_rule', 'click', function (event) {
		event.preventDefault();
		InsertContinuousRule($(this).data('index'), $(this).data('name'));
		resetCollapsHeight($('#woocommerce-pricing-rules-wrap'));
	});

	$('#woocommerce-pricing-rules-wrap').delegate('.add_pricing_blockrule', 'click', function (event) {
		event.preventDefault();
		InsertBlockRule($(this).data('index'), $(this).data('name'));
		resetCollapsHeight($('#woocommerce-pricing-rules-wrap'));
	});


	//Remove Button
	$('#woocommerce-pricing-rules-wrap').delegate('.delete_pricing_rule', 'click', function (event) {
		event.preventDefault();
		DeleteRule($(this).data('index'), $(this).data('name'));
	});

	//Remove Button
	$('#woocommerce-pricing-rules-wrap').delegate('.delete_pricing_blockrule', 'click', function (event) {
		event.preventDefault();
		DeleteBlockRule($(this).closest('tr'), $(this).closest('table'));
	});


	$("#woocommerce-pricing-rules-wrap").sortable(
	{
			handle: 'h4.first',
			containment: 'parent',
			axis: 'y'
	});

	function InsertContinuousRule(previousRowIndex, name) {


		var $index = $("#woocommerce-pricing-rules-table-" + name).data('lastindex') + 1;
		$("#woocommerce-pricing-rules-table-" + name).data('lastindex', $index);

		var html = '';
		html += '<tr id="pricing_rule_row_' + name + '_' + $index + '">';
		html += '<td>';
		html += '<input class="int_pricing_rule wcfm-text" style="width:100%" id="pricing_rule_from_input_' + name + '_' + $index + '" type="text" name="pricing_rules[' + name + '][rules][' + $index + '][from]" value="" /> ';
		html += '</td>';
		html += '<td>';
		html += '<input class="int_pricing_rule wcfm-text" style="width:100%" id="pricing_rule_to_input_' + name + '_' + $index + '" type="text" name="pricing_rules[' + name + '][rules][' + $index + '][to]" value="" /> ';
		html += '</td>';
		html += '<td>';
		html += '<select class="wcfm-select" style="width:100%" id="pricing_rule_type_value_' + name + '_' + $index + '" name="pricing_rules[' + name + '][rules][' + $index + '][type]">';
		html += '<option value="price_discount">' + wcfm_dynamic_pricing_args.price_discount + '</option>';
		html += '<option value="percentage_discount">' + wcfm_dynamic_pricing_args.percent_discount + '</option>';
		html += '<option value="fixed_price">' + wcfm_dynamic_pricing_args.fixed_price + '</option>';
		html += '</select>';
		html += '</td>';
		html += '<td>';
		html += '<input class="float_pricing_rule wcfm-text" style="width:100%" id="pricing_rule_amount_input_' + $index + '" type="text" name="pricing_rules[' + name + '][rules][' + $index + '][amount]" value="" /> ';
		html += '</td>';
		html += '<td width="48">';
		html += '<a data-index="' + $index + '" data-name="' + name + '" class="add_pricing_rule"><img  src="' + wcfm_dynamic_pricing_args.add_img + '" title="add another rule" alt="add another rule" style="cursor:pointer; margin:0 3px;" /></a>';
		html += '<a data-index="' + $index + '" data-name="' + name + '" class="delete_pricing_rule"><img data-index="' + $index + '" src="' + wcfm_dynamic_pricing_args.remove_img + '" title="remove rule" alt="remove rule" style="cursor:pointer; margin:0 3px;" /></a>';
		html += '</td>';
		html += '</tr>';

		$('#pricing_rule_row_' + name + '_' + previousRowIndex).after(html);
		$('.delete_pricing_rule', "#woocommerce-pricing-rules-table-" + name).show();

	}

	function InsertBlockRule(previousRowIndex, name) {
		var $index = $("#woocommerce-pricing-blockrules-table-" + name).data('lastindex') + 1;
		$("#woocommerce-pricing-blockrules-table-" + name).data('lastindex', $index);

		var html = '';
		html += '<tr id="pricing_blockrule_row_' + name + '_' + $index + '">';
		html += '<td>';
		html += '<input class="int_pricing_blockrule wcfm-text" style="width:100%" type="text" name="pricing_rules[' + name + '][blockrules][' + $index + '][from]" value="" /> ';
		html += '</td>';
		html += '<td>';
		html += '<input class="int_pricing_blockrule wcfm-text" style="width:100%" type="text" name="pricing_rules[' + name + '][blockrules][' + $index + '][adjust]" value="" /> ';
		html += '</td>';
		html += '<td>';
		html += '<select class="wcfm-select" style="width:100%" name="pricing_rules[' + name + '][blockrules][' + $index + '][type]">';
		html += '<option value="price_discount">' + wcfm_dynamic_pricing_args.price_discount + '</option>';
		html += '<option value="percentage_discount">' + wcfm_dynamic_pricing_args.percent_discount + '</option>';
		html += '<option value="fixed_price">' + wcfm_dynamic_pricing_args.fixed_price + '</option>';
		html += '</select>';
		html += '</td>';
		html += '<td>';
		html += '<input class="float_pricing_rule wcfm-text" style="width:100%" id="pricing_rule_amount_input_' + $index + '" type="text" name="pricing_rules[' + name + '][blockrules][' + $index + '][amount]" value="" /> ';
		html += '</td>';
		html += '<td>';
		html += '<select class="wcfm-select" style="width:100%" name="pricing_rules[' + name + '][blockrules][' + $index + '][repeating]">';
		html += '<option value="no">' + wcfm_dynamic_pricing_args.no + '</option>';
		html += '<option value="yes">' + wcfm_dynamic_pricing_args.yes + '</option>';
		html += '</select>';
		html += '</td>';
		html += '<td width="48">';
		html += '<a data-index="' + $index + '" data-name="' + name + '" class="add_pricing_blockrule"><img  src="' + wcfm_dynamic_pricing_args.add_img + '" title="add another rule" alt="add another rule" style="cursor:pointer; margin:0 3px;" /></a>';
		html += '<a data-index="' + $index + '" data-name="' + name + '" class="delete_pricing_blockrule"><img data-index="' + $index + '" src="' + wcfm_dynamic_pricing_args.remove_img + '" title="remove rule" alt="remove rule" style="cursor:pointer; margin:0 3px;" /></a>';
		html += '</td>';
		html += '</tr>';

		$('#pricing_blockrule_row_' + name + '_' + previousRowIndex).after(html);
		$('.delete_pricing_blockrule', "#woocommerce-pricing-blockrules-table-" + name).show();
	}

	function DeleteRule(index, name) {
			if (confirm(wcfm_dynamic_pricing_args.remove_price)) {
					$('#pricing_rule_row_' + name + '_' + index).remove();

					var $index = $('tbody tr', "#woocommerce-pricing-rules-table-" + name).length;
					if ($index > 1) {
							$('.delete_pricing_rule', "#woocommerce-pricing-rules-table-" + name).show();
					} else {
							$('.delete_pricing_rule', "#woocommerce-pricing-rules-table-" + name).hide();
					}
			}
	}

	function DeleteBlockRule($tr, $table) {
		if (confirm(wcfm_dynamic_pricing_args.remove_price)) {
			$tr.remove();

			var count = $('tr', $table).length;
			if (count > 1) {
					$('.delete_pricing_blockrule', $table).show();
			} else {
					$('.delete_pricing_blockrule', $table).hide();
			}
		}
	}

	function DeleteRuleSet(name) {
		if (confirm(wcfm_dynamic_pricing_args.remove_price_set)) {
			$('#woocommerce-pricing-ruleset-' + name).slideUp().remove();
		}
	}

});