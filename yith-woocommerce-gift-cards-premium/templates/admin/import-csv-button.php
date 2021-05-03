<?php
if (!defined('ABSPATH')) {
	exit;
}

$import_url = esc_url( add_query_arg( array( 'action' => 'import_gift_cards' ), admin_url( 'admin.php' ) ) );


?>


<tr valign="top" class="ywgc-import-button-tr">
	<th scope="row" class="titledesc">
		<label><?php esc_html_e( 'Import CSV file', 'yith-woocommerce-gift-cards' ); ?></label>
	</th>

	<td class="forminp forminp-upload">
		<button id="ywgc_file_import_csv_btn" class="button button-primary"><?php esc_html_e( 'Import CSV', 'yith-woocommerce-gift-cards' ); ?></button>
		<span class="ywbc_file_name"></span>
		<input type="file" id="ywgc_file_import_csv" name="file_import_csv" style="display: none" accept=".csv">
		<span class="description"><?php esc_html_e( 'Import gift cards in CSV format, following the structure from ', 'yith-woocommerce-gift-cards' ); ?><a
				href="<?php echo YITH_YWGC_ASSETS_URL . '/csv_samples/yith-gift-card-import-sample.csv'; ?>"><?php esc_html_e( 'this example', 'yith-woocommerce-gift-cards' ); ?></a>.</span>
	</td>

</tr>

<tr valign="top" class="ywgc-start-import-button-tr">
	<td class="forminp forminp-upload-start">
		<input type="hidden" class="ywgc_safe_submit_field" name="ywgc_safe_submit_field" value="" data-std="">
		<button class="button button-primary"
		        id="ywgc_import_gift_cards"><?php esc_html_e( 'Start import', 'yith-woocommerce-gift-cards' ); ?></button>
	</td>
</tr>


