<?php
/**
 * WCFM plugin view
 *
 * WCFM Product Import view
 *
 * @author 		WC Lovers
 * @package 	wcfm/views
 * @version   2.4.2
 */
 
global $WCFM, $WCFMu;

$wcfm_is_allow_manage_products = apply_filters( 'wcfm_is_allow_manage_products', true );
$wcfm_is_allow_add_products = apply_filters( 'wcfm_is_allow_add_products', true );
$wcfm_is_allow_products_import = apply_filters( 'wcfm_is_allow_products_import', true );
if( !apply_filters( 'wcfm_is_pref_products_import', true ) || !$wcfm_is_allow_manage_products || !$wcfm_is_allow_add_products || !$wcfm_is_allow_products_import ) {
	wcfm_restriction_message_show( "Products Import" );
	return;
}

if( !isset( $_GET['step'] ) ) {
	if( !apply_filters( 'wcfm_is_allow_product_limit', true ) ) {
		if( WCFM_Dependencies::wcfmvm_plugin_active_check() ) {
			?>
			<div class="wcfm-clearfix"></div><br />
			<div class="collapse wcfm-collapse">
				<div class="wcfm-page-headig">
					<span class="wcfmfa fa-cube"></span>
					<span class="wcfm-page-heading-text"><?php _e( 'Product Import', 'wc-frontend-manager' ); ?></span>
					<?php do_action( 'wcfm_page_heading' ); ?>
				</div>
				<div class="wcfm-collapse-content wcfm-nolimit-content">
					<div class="wcfm-container">
						<div class="wcfm-clearfix"></div><br />
						<h2><?php _e( 'You have reached your product limit!', 'wc-frontend-manager' ); ?></h2>
						<div class="wcfm-clearfix"></div><br />
						<?php do_action( 'wcfm_product_limit_reached' ); ?>
						<div class="wcfm-clearfix"></div><br />
					</div>
				</div>
			</div>
			<?php
		} else {
			wcfm_restriction_message_show( "Product Limit Reached" );
		}
		return;
	}
	
	if( !apply_filters( 'wcfm_is_allow_space_limit', true ) ) {
		wcfm_restriction_message_show( "Space Limit Reached" );
		return;
	}
	
	if( !apply_filters( 'wcfm_is_allow_pm_add_products', true ) ) {
		return;
	}
}

wp_register_script( 'wcfm-product-import', $WCFMu->plugin_url . 'assets/js/' . 'products/wcfmu-script-products-import.js', array('jquery'), $WCFMu->version, true );
include_once WC_ABSPATH . 'includes/import/class-wc-product-csv-importer.php';
include_once WC_ABSPATH . 'includes/admin/importers/class-wc-product-csv-importer-controller.php';

if( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '3.7.0', '>=' ) ) { 
	include_once $WCFMu->plugin_path . 'includes/product_importer/3.7/class-wcfm-product-csv-importer-controller.php';
} else {
	include_once $WCFMu->plugin_path . 'includes/product_importer/class-wcfm-product-csv-importer-controller.php';
}

$importer = new WCFM_Product_CSV_Importer_Controller();
?>

<div class="collapse wcfm-collapse" id="wcfm_products_listing">
	
	<div class="wcfm-page-headig">
		<span class="wcfmfa fa-upload"></span>
		<span class="wcfm-page-heading-text"><?php _e( 'Products Import', 'wc-frontend-manager' ); ?></span>
		<?php do_action( 'wcfm_page_heading' ); ?>
	</div>
	<div class="wcfm-collapse-content">
		<div id="wcfm_page_load"></div>
		
		<div class="wcfm-container wcfm-top-element-container">
			<h2><?php esc_html_e( 'Import products from a CSV file', 'woocommerce' ); ?></h2>
			
			<?php
			if( $allow_wp_admin_view = apply_filters( 'wcfm_allow_wp_admin_view', true ) ) {
				?>
				<a target="_blank" class="wcfm_wp_admin_view text_tip" href="<?php echo admin_url('edit.php?post_type=product&page=product_importer'); ?>" data-tip="<?php _e( 'WP Admin View', 'wc-frontend-manager' ); ?>"><span class="fab fa-wordpress fa-wordpress-simple"></span></a>
				<?php
			}
			
			if( $is_allow_products_export = apply_filters( 'wcfm_is_allow_products_export', true ) ) {
				?>
				<a class="wcfm_import_export text_tip" href="<?php echo get_wcfm_export_product_url(); ?>" data-screen="product" data-tip="<?php _e( 'Products Export', 'wc-frontend-manager' ); ?>"><span class="wcfmfa fa-download"></span></a>
				<?php
			}
			
			if( $has_new = apply_filters( 'wcfm_add_new_product_sub_menu', true ) ) {
				echo '<a id="add_new_product_dashboard" class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_edit_product_url().'" data-tip="' . __('Add New Product', 'wc-frontend-manager') . '"><span class="wcfmfa fa-cube"></span><span class="text">' . __( 'Add New', 'wc-frontend-manager') . '</span></a>';
			}
			?>
			<div class="wcfm-clearfix"></div>
		</div>
	  <div class="wcfm-clearfix"></div><br />
	  
	  <?php do_action( 'before_wcfm_products_import' ); ?>
	  
		<div class="wcfm-container">
			<div id="wcfm_products_export_expander" class="wcfm-content">
			  <?php
					$importer->dispatch();
				?>
				<div class="wcfm-clearfix"></div>
			</div>
		</div>
		<?php
		do_action( 'after_wcfm_products_import' );
		?>
	</div>
</div>