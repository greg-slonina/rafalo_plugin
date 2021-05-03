<?php
if (!defined('ABSPATH')) {
    exit;
}

$export_url = esc_url( add_query_arg( array( 'action' => 'export_gift_cards' ), admin_url( 'admin.php' ) ) );

$button_text = __( 'Export CSV', 'yith-woocommerce-gift-cards' );

?>

<tr valign="top" class="ywbc-export-button-tr">
	<th scope="row" class="titledesc">
		<label><?php esc_html_e( 'Export CSV file', 'yith-woocommerce-gift-cards' ); ?></label>
	</th>
    <td class="forminp" colspan="1">

	    <input type="hidden" class="ywgc_hidden_date_from" name="ywgc_hidden_date_from" value="">
	    <input type="hidden" class="ywgc_hidden_date_to" name="ywgc_hidden_date_to" value="">


	    <?php echo sprintf( '<a class="button button-primary export ywbc-export-button" href="%s" title="%2$s">' . $button_text . '</a>', $export_url, $button_text ); ?>

        <span class="description"><?php esc_attr_e($desc); ?></span>
	</td>
</tr>
