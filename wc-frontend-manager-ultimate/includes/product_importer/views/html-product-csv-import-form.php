<?php
/**
 * Admin View: Product import form
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<form class="wc-progress-form-content woocommerce-importer" enctype="multipart/form-data" method="post">
	<header>
		<p><?php esc_html_e( 'This tool allows you to import (or merge) product data to your store from a CSV file.', 'woocommerce' ); ?></p>
	</header>
	<section>
		<table class="form-table woocommerce-importer-options">
			<tbody>
				<tr>
					<th scope="row">
						<label for="upload" class="wcfm_title">
							<?php _e( 'Choose a CSV file from your computer:', 'woocommerce' ); ?>
						</label>
					</th>
					<td>
						<?php
						if ( ! empty( $upload_dir['error'] ) ) {
							?><div class="wcfm-message wcfm-error">
							  <span class="wcicon-status-cancelled"></span>
								<p><?php esc_html_e( 'Before you can upload your import file, you will need to fix the following error:', 'woocommerce' ); ?></p>
								<p><strong><?php echo esc_html( $upload_dir['error'] ); ?></strong></p>
							</div><?php
						} else {
							?>
							<input type="file" id="upload" name="import" size="25" />
							<input type="hidden" name="action" value="save" />
							<input type="hidden" name="max_file_size" value="<?php echo esc_attr( $bytes ); ?>" />
							<br><small><?php
								/* translators: %s: maximum upload size */
								printf(
									__( 'Maximum size: %s', 'woocommerce' ),
									$size
								);
							?></small>
							<?php
						}
					?>
					</td>
				</tr>
				<tr>
					<th><label for="woocommerce-importer-update-existing" class="wcfm_title"><?php _e( 'Update existing products', 'woocommerce' ); ?></label><br/></th>
					<td>
						<input type="hidden" name="update_existing" value="0" />
						<input type="checkbox" id="woocommerce-importer-update-existing" class="wcfm-checkbox" name="update_existing" value="1" />
						<label for="woocommerce-importer-update-existing"><?php esc_html_e( 'Existing products that match by ID or SKU will be updated. Products that do not exist will be skipped.', 'woocommerce' ); ?></label>
					</td>
				</tr>
				<?php if( !wcfm_is_vendor() && apply_filters( 'wcfm_is_allow_server_file_import', true ) ) { ?>
					<tr class="woocommerce-importer-advanced wcfm_hide">
						<th>
							<label for="woocommerce-importer-file-url" class="wcfm_title"><?php _e( 'Alternatively, enter the path to a CSV file on your server:', 'woocommerce' ); ?></label>
						</th>
						<td>
							<label for="woocommerce-importer-file-url" class="woocommerce-importer-file-url-field-wrapper wcfm_title">
								<code><?php echo esc_html( ABSPATH ) . ' '; ?></code><input type="text" id="woocommerce-importer-file-url" name="file_url" />
							</label>
						</td>
					</tr>
				<?php } ?>
				<tr class="woocommerce-importer-advanced wcfm_hide">
					<th><label class="wcfm_title"><?php _e( 'CSV Delimiter', 'woocommerce' ); ?></label><br/></th>
					<td><input type="text" name="delimiter" placeholder="," size="2" /></td>
				</tr>
			</tbody>
		</table>
	</section>
	<script type="text/javascript">
		jQuery(function() {
			jQuery( '.woocommerce-importer-toggle-advanced-options' ).on( 'click', function() {
				var elements = jQuery( '.woocommerce-importer-advanced' );
				if ( elements.is( '.wcfm_hide' ) ) {
					elements.removeClass( 'wcfm_hide' );
					jQuery( this ).text( jQuery( this ).data( 'hidetext' ) );
				} else {
					elements.addClass( 'wcfm_hide' );
					jQuery( this ).text( jQuery( this ).data( 'showtext' ) );
				}
				return false;
			} );
		});
	</script>
	<div class="wc-actions">
		<a href="#" class="woocommerce-importer-toggle-advanced-options" data-hidetext="<?php esc_html_e( 'Hide advanced options', 'woocommerce' ); ?>" data-showtext="<?php esc_html_e( 'Hide advanced options', 'woocommerce' ); ?>"><?php esc_html_e( 'Show advanced options', 'woocommerce' ); ?></a>
		<input type="submit" class="wcfm_submit_button button-next" value="<?php esc_attr_e( 'Continue', 'woocommerce' ); ?>" name="save_step" />
		<?php wp_nonce_field( 'woocommerce-csv-importer' ); ?>
	</div>
</form>
