<?php
if( !defined('ABSPATH')){
	exit;
}

$radio_args = array(
	'type' => 'radio',
	'id' => 'ywgc_export_option_date',
	'name' => 'ywgc_export_option_date',
	'options' => array(
		'all' => __('All','yith-woocommerce-gift-cards'),
		'by_date' => __('Only gift cards in a specific date range','yith-woocommerce-gift-cards')
	),
	'value' => 'all'
);

?>

<tr valign="top" class="ywbc-date-from-to-tr">
	<th scope="row" class="titledesc">
		<label><?php esc_html_e( 'Gift cards to export', 'yith-woocommerce-gift-cards' ); ?></label>
	</th>

	<td class="forminp">

		<div id="ywbc-date-from-to-container" class="yith-plugin-fw-metabox-field-row">

			<div class="yith-plugin-fw-field-wrapper">
				<?php echo yith_plugin_fw_get_field( $radio_args); ?>
				<div class="yith-plugin-fw-field-schedule ywbc-date-from-to-date-selectors">
					<span class="from_datepicker">
						<label for="ywgc_export_option_date_from" style="line-height: 35px;"><?php _e('From','yith-woocommerce-gift-cards');?></label>
						<input type="text" id="ywgc_export_option_date_from" autocomplete="off" class="yith-plugin-fw-text-input" value="" name="ywgc_export_option_date_from"/>
					</span>
					<span class="to_datepicker">
						<label for="ywgc_export_option_date_to" style="line-height: 35px;"><?php _e('To','yith-woocommerce-gift-cards');?></label>
						<input type="text" id="ywgc_export_option_date_to" autocomplete="off" class="yith-plugin-fw-text-input" value="" name="ywgc_export_option_date_to"/>
					</span>

				</div>
			</div>
			<div class="clear"></div>
			<span class="description"><?php echo $desc;?></span>
		</div>

	</td>
</tr>
