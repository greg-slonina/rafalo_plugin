<?php
/**
 * WCFM plugin view
 *
 * WCFM WC Warranty Product Manage View
 *
 * @author 		WC Lovers
 * @package 	wcfmu/views/thirdparty
 * @version   4.1.5
 */
 
global $wp, $WCFM, $WCFMu, $post, $woocommerce;

if( !apply_filters( 'wcfm_is_allow_wc_warranty', true ) ) {
	return;
}

$product_id = 0;
$warranty_type_value = 'no_warranty';
$warranty_duration_value = 0;
$warranty_unit_value = 'day';
$warranty       = array( 'type' => '', 'length' => '', 'duration' => '' );
$default_warranty = true;
$warranty_label = __('Warranty', 'wc_warranty');
$control_type = 'parent';

if( isset( $wp->query_vars['wcfm-products-manage'] ) && !empty( $wp->query_vars['wcfm-products-manage'] ) ) {
	$product_id = $wp->query_vars['wcfm-products-manage'];
	
	if( $product_id ) {
		$warranty_type_value = get_post_meta($product_id, '_warranty_type', true);
		$warranty_duration_value = get_post_meta($product_id, '_warranty_duration', true);
		$warranty_unit_value = get_post_meta($product_id, '_warranty_unit', true);
		$warranty       = warranty_get_product_warranty( $product_id );
		$default_warranty = isset( $warranty['default'] ) ? $warranty['default'] : false;
		$control_type = get_post_meta( $product_id, '_warranty_control', true );
		
	}
}

$currency = get_woocommerce_currency_symbol();
$inline = '
		var warranty_fields_toggled = false;
		$("#product_warranty_default").change(function() {

				if ($(this).is(":checked")) {
						$(".warranty_field").attr("disabled", true);
				} else {
						$(".warranty_field").attr("disabled", false);
				}

		}).change();

		$("#product_warranty_type").change(function() {
				$(".show_if_included_warranty, .show_if_addon_warranty").hide();

				if ($(this).val() == "included_warranty") {
						$(".show_if_included_warranty").show();
				} else if ($(this).val() == "addon_warranty") {
						$(".show_if_addon_warranty").show();
				}
		}).change();

		$("#included_warranty_length").change(function() {
				if ($(this).val() == "limited") {
						$(".limited_warranty_length_field").show();
				} else {
						$(".limited_warranty_length_field").hide();
				}
		}).change();

		var tmpl = "<tr>\
										<td valign=\"middle\">\
												<span class=\"input\"><b>+</b> '. $currency .'</span>\
												<input type=\"text\" name=\"addon_warranty_amount[]\" class=\"input-text sized\" size=\"4\" value=\"\" />\
										</td>\
										<td valign=\"middle\">\
												<input type=\"text\" class=\"input-text sized\" size=\"3\" name=\"addon_warranty_length_value[]\" value=\"\" />\
												<select name=\"addon_warranty_length_duration[]\">\
														<option value=\"days\">'. __('Days', 'wc_warranty') .'</option>\
														<option value=\"weeks\">'. __('Weeks', 'wc_warranty') .'</option>\
														<option value=\"months\">'. __('Months', 'wc_warranty') .'</option>\
														<option value=\"years\">'. __('Years', 'wc_warranty') .'</option>\
												</select>\
										</td>\
										<td><a class=\"button warranty_addon_remove\" href=\"#\">&times;</a></td>\
								</tr>";

		$(".btn-add-warranty").click(function(e) {
				e.preventDefault();

				$("#warranty_addons").append(tmpl);
		});

		$(".warranty_addon_remove").live("click", function(e) {
				e.preventDefault();

				$(this).parents("tr").remove();
		});

		$("#variable_warranty_control").change(function() {
				if ($(this).val() == "variations") {
						$(".hide_if_control_variations").hide();
						$(".show_if_control_variations").show();
				} else {
						$(".hide_if_control_variations").show();
						$(".show_if_control_variations").hide();
						$("#warranty_product_data :input[id!=variable_warranty_control]").change();
				}
		}).change();

		$("#variable_product_options").on("woocommerce_variations_added", function() {
				$("#variable_warranty_control").change();
		});

		$("#woocommerce-product-data").on("woocommerce_variations_loaded", function() {
				$("#variable_warranty_control").change();
		});
		';

if ( function_exists('wc_enqueue_js') ) {
		wc_enqueue_js( $inline );
} else {
		$woocommerce->add_inline_js( $inline );
}

?>

<div class="page_collapsible products_manage_wc_warrenty simple variable external grouped booking" id="wcfm_products_manage_form_wc_warrenty_head"><label class="wcfmfa fa-exclamation-triangle"></label><?php _e('Warranty', 'wc-frontend-manager-ultimate'); ?><span></span></div>
<div class="wcfm-container simple variable external grouped booking">
	<div id="wcfm_products_manage_form_wc_warrenty_expander" class="wcfm-content">
	  <h2><?php _e('Product Warranty', 'wc-frontend-manager-ultimate'); ?></h2>
	  <div class="wcfm-clearfix"></div>
	  <div id="warranty_product_data" class="panel woocommerce_options_panel">

			<div class="options_group wcfm_custom_hide">
					<p class="form-field">
							<p for="variable_warranty_control" class="wcfm_title">
									<strong><?php _e('Warranty Control', 'wc_warranty'); ?></strong>
							</p>
							<select id="variable_warranty_control" name="variable_warranty_control" class="wcfm-select">
									<option value="parent" <?php selected( $control_type, 'parent' ); ?>><?php _e('Define warranty for all variations', 'wc_warranty'); ?></option>
									<option value="variations" <?php selected( $control_type, 'variations' ); ?>><?php _e('Define warranty per variation', 'wc_warranty'); ?></option>
							</select>
					</p>
			</div>
	
			<div class="options_group grouping hide_if_control_variations">
					<p class="form-field">
							<p for="product_warranty_default" class="wcfm_title checkbox_title">
									<strong><?php _e('Default Product Warranty', 'wc_warranty'); ?></strong>
							</p>
							<input class="wcfm-checkbox" type="checkbox" name="product_warranty_default" id="product_warranty_default" <?php checked(true, $default_warranty); ?> value="yes" />
					</p>
	
					<p class="form-field product_warranty_type_field">
							<p for="product_warranty_type" class="wcfm_title"><strong><?php _e('Product Warranty', 'wc_warranty'); ?></strong></p>
	
							<select id="product_warranty_type" name="product_warranty_type" class="select warranty_field wcfm-select">
									<option value="no_warranty" <?php if ($warranty['type'] == 'no_warranty') echo 'selected'; ?>><?php _e('No Warranty', 'wc_warranty'); ?></option>
									<option value="included_warranty" <?php if ($warranty['type'] == 'included_warranty') echo 'selected'; ?>><?php _e('Warranty Included', 'wc_warranty'); ?></option>
									<option value="addon_warranty" <?php if ($warranty['type'] == 'addon_warranty') echo 'selected'; ?>><?php _e('Warranty as Add-On', 'wc_warranty'); ?></option>
							</select>
					</p>
	
					<p class="form-field show_if_included_warranty show_if_addon_warranty">
							<p for="warranty_label" class="wcfm_title"><strong><?php _e('Warranty Label', 'wc_warranty'); ?></strong></p>
	
							<input type="text" name="warranty_label" value="<?php echo esc_attr($warranty_label); ?>" class="input-text sized warranty_field wcfm-text" />
					</p>
			</div>
	
			<div class="options_group grouping show_if_included_warranty hide_if_control_variations">
					<p class="form-field included_warranty_length_field">
							<p for="included_warranty_length" class="wcfm_title"><strong><?php _e('Warranty Length', 'wc_warranty'); ?></strong></p>
	
							<select id="included_warranty_length" name="included_warranty_length" class="select short warranty_field wcfm-select">
									<option value="lifetime" <?php if ($warranty['type'] == 'included_warranty' && $warranty['length'] == 'lifetime') echo 'selected'; ?>><?php _e('Lifetime', 'wc_warranty'); ?></option>
									<option value="limited" <?php if ($warranty['type'] == 'included_warranty' && $warranty['length'] == 'limited') echo 'selected'; ?>><?php _e('Limited', 'wc_warranty'); ?></option>
							</select>
					</p>
	
					<p class="form-field limited_warranty_length_field">
							<p for="limited_warranty_length_value" class="wcfm_title"><strong><?php _e('Warranty Duration', 'wc_warranty'); ?></strong></p>
							<input style="width: 25%;margin-right:5px;" type="text" class="input-text sized warranty_field wcfm-text" size="3" name="limited_warranty_length_value" value="<?php if ($warranty['type'] == 'included_warranty') echo $warranty['value']; ?>" />
							<select style="width: 25%;" name="limited_warranty_length_duration" class=" warranty_field wcfm-select">
									<option value="days" <?php if ($warranty['type'] == 'included_warranty' && $warranty['duration'] == 'days') echo 'selected'; ?>><?php _e('Days', 'wc_warranty'); ?></option>
									<option value="weeks" <?php if ($warranty['type'] == 'included_warranty' && $warranty['duration'] == 'weeks') echo 'selected'; ?>><?php _e('Weeks', 'wc_warranty'); ?></option>
									<option value="months" <?php if ($warranty['type'] == 'included_warranty' && $warranty['duration'] == 'months') echo 'selected'; ?>><?php _e('Months', 'wc_warranty'); ?></option>
									<option value="years" <?php if ($warranty['type'] == 'included_warranty' && $warranty['duration'] == 'years') echo 'selected'; ?>><?php _e('Years', 'wc_warranty'); ?></option>
							</select>
					</p>
			</div>
	
			<div class="options_group grouping show_if_addon_warranty hide_if_control_variations">
					<p class="form-field">
							<p for="addon_no_warranty" class="wcfm_title checkbox_title">
									<strong><?php _e( '"No Warranty" option', 'wc_warranty'); ?></strong>
							</p>
							<input type="checkbox" name="addon_no_warranty" id="addon_no_warranty" value="yes" <?php if (isset($warranty['no_warranty_option']) && $warranty['no_warranty_option'] == 'yes') echo 'checked'; ?> class="checkbox warranty_field wcfm-checkbox" />
					</p>
	
					<table class="widefat">
							<thead>
							<tr>
									<th><?php _e('Cost', 'wc_warranty'); ?></th>
									<th><?php _e('Duration', 'wc_warranty'); ?></th>
									<th width="50">&nbsp;</th>
							</tr>
							</thead>
							<tfoot>
							<tr>
									<th colspan="3">
											<a href="#" class="button btn-add-warranty"><?php _e('Add Row', 'wc_warranty'); ?></a>
									</th>
							</tr>
							</tfoot>
							<tbody id="warranty_addons">
							<?php
							if ( isset($warranty['addons']) ) foreach ( $warranty['addons'] as $addon ):
									?>
									<tr>
											<td valign="middle">
													<span class="input"><b>+</b> <?php echo $currency; ?></span>
													<input type="text" name="addon_warranty_amount[]" class="input-text sized warranty_field" size="4" value="<?php echo $addon['amount']; ?>" />
											</td>
											<td valign="middle">
													<input type="text" class="input-text sized warranty_field" size="3" name="addon_warranty_length_value[]" value="<?php echo $addon['value']; ?>" />
													<select name="addon_warranty_length_duration[]" class=" warranty_field">
															<option value="days" <?php if ($addon['duration'] == 'days') echo 'selected'; ?>><?php _e('Days', 'wc_warranty'); ?></option>
															<option value="weeks" <?php if ($addon['duration'] == 'weeks') echo 'selected'; ?>><?php _e('Weeks', 'wc_warranty'); ?></option>
															<option value="months" <?php if ($addon['duration'] == 'months') echo 'selected'; ?>><?php _e('Months', 'wc_warranty'); ?></option>
															<option value="years" <?php if ($addon['duration'] == 'years') echo 'selected'; ?>><?php _e('Years', 'wc_warranty'); ?></option>
													</select>
											</td>
											<td><a class="button warranty_addon_remove" href="#">&times;</a></td>
									</tr>
							<?php endforeach; ?>
							</tbody>
	
					</table>
			</div>
			<style type="text/css">
				span.input {float: left; margin-top: 4px;}
				p.addon-row {margin-left: 25px;}
			</style>
	  </div>
	</div>
</div>