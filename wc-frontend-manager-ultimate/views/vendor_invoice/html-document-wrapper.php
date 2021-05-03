<?php 

global $WCFM, $WCFMu, $wpo_wcpdf, $document, $document_type, $plugin_path, $plugin_url; 

$wcfm_vendor_invoice_options = get_option( 'wcfm_vendor_invoice_options', array() );
$wcfm_vendor_invoice_advance_font = isset( $wcfm_vendor_invoice_options['advance_font'] ) ? 'yes' : '';
$wcfm_vendor_invoice_advance_currency = isset( $wcfm_vendor_invoice_options['advance_currency'] ) ? 'yes' : '';

$wcfm_vendor_invoice_advance_font = apply_filters( 'wcfm_invoice_advance_font', $wcfm_vendor_invoice_advance_font );
$wcfm_vendor_invoice_advance_currency = apply_filters( 'wcfm_invoice_advance_currency', $wcfm_vendor_invoice_advance_currency );
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title><?php echo $document_type; ?></title>
	<style type="text/css"><?php $WCFMu->wcfmu_vendor_invoice->invoice_styles(); ?></style>
	<style type="text/css"><?php do_action( 'wcfm_pdf_invoice_custom_styles', $document_type ); ?></style>
	<style type="text/css"><?php do_action( 'wpo_wcpdf_custom_styles', $document_type, $document ); ?></style>
	<style>
	  <?php if( !is_rtl() && $wcfm_vendor_invoice_advance_font ) { ?>
			
		<?php } ?>
		<?php if( $wcfm_vendor_invoice_advance_currency ) { ?>
			.wcpdf-currency-symbol {
				font-family: 'DejaVu Sans';
			}
	  <?php } ?>
	</style>
</head>
<body class="<?php echo $document_type; ?>">
<?php echo $output_body; ?>
</body>
</html>