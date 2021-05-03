<?php
global $WCFM, $WCFMu, $wp_query, $wpdb;

$page_links = false;

$vendor_id   = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );
	
if( isset($_POST['fpd_filter_by']) )
	update_option('fpd_admin_filter_by', $_POST['fpd_filter_by']);

if( isset($_POST['fpd_order_by']) )
	update_option('fpd_admin_order_by', $_POST['fpd_order_by']);

$filter_by = get_option('fpd_admin_filter_by', 'title');
$order_by = get_option('fpd_admin_order_by', 'ASC');

/*$where = '';
if( wcfm_is_vendor() )
	$where = "user_id={$vendor_id }";

$categories = FPD_Category::get_categories( array(
	'order_by' => 'title ASC'
) );

$pagenum = isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1;
$limit = 20;
$offset = ( $pagenum - 1 ) * $limit;
if( wcfm_is_vendor() ) {
	$total = sizeof( FPD_Product::get_products( array( 'where' => "user_id={$vendor_id }")) );
} else {
	$total = sizeof( FPD_Product::get_products() );
}
$num_of_pages = ceil( $total / $limit );

$page_links = paginate_links( array(
		'base' 		=> add_query_arg( 'paged', '%#%' ),
		'format' 	=> '',
		'prev_text' => '&laquo;',
		'next_text' => '&raquo;',
		'total' 	=> $num_of_pages,
		'current' 	=> $pagenum
) );

$products = FPD_Product::get_products( array(
	'where' 	=> $where,
	'order_by' 	=> $filter_by . ' '. $order_by,
	'limit' 	=> $limit,
	'offset' 	=> $offset
) );

//select by category
if( isset($_GET['category_id']) ) {

	$page_links = false;
	$products = FPD_Product::get_products( array(
		'where' 	=> "ID IN (SELECT product_id FROM ".FPD_CATEGORY_PRODUCTS_REL_TABLE." WHERE category_id={$_GET['category_id']})",
	) );

}

if ( isset($_GET['info']) ) {
	require_once(FPD_PLUGIN_ADMIN_DIR.'/modals/modal-updated-installed-info.php');
}

$total_product_templates = 0;
require_once(FPD_PLUGIN_ADMIN_DIR.'/modals/modal-load-demo.php');
require_once(FPD_PLUGIN_ADMIN_DIR.'/modals/modal-load-template.php');
require_once(FPD_PLUGIN_ADMIN_DIR.'/modals/modal-edit-product-options.php');
require_once(FPD_PLUGIN_ADMIN_DIR.'/modals/modal-shortcodes.php');
require_once(FPD_PLUGIN_ADMIN_DIR.'/modals/modal-templates-library.php');*/


$_GET['page'] = 'fpd_product_designer';
?>

<div class="collapse wcfm-collapse" id="wcfm_build_listing">
	
	<div class="wcfm-page-headig">
		<span class="wcfmfa fa-object-group"></span>
		<span class="wcfm-page-heading-text"><?php _e( 'Product Designer', 'wc-frontend-manager-ultimate' ); ?></span>
		<?php do_action( 'wcfm_page_heading' ); ?>
	</div>
	<div class="wcfm-collapse-content">
		<div id="wcfm_page_load"></div>
		<?php do_action( 'before_wcfm_fancy_product_designer' ); ?>
		
		<div class="wcfm-container wcfm-top-element-container">
			<h2><?php _e('Product Designer', 'wc-frontend-manager-ultimate' ); ?></h2>
			
			<?php
			echo '<a id="add_new_product_dashboard" class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_fncy_product_builder_url().'" data-tip="' . __('Product Builder', 'wc-frontend-manager-ultimate') . '"><span class="wcfmfa fa-cube"></span><span class="text">' . __( 'Product Builder', 'wc-frontend-manager-ultimate' ) . '</span></a>';
			?>
			
			<div class="wcfm-clearfix"></div>
	  </div>
	  <div class="wcfm-clearfix"></div><br />
		

		<div class="wcfm-container">
			<div id="wcfm_fpd_product_designer_expander" class="wcfm-content">
			
				<?php do_action( 'fpd_backend_react_page_start' ); ?>
				<div id="fpd-react-root"></div>
			  <?php do_action( 'fpd_backend_react_page_end' ); ?>
				
			  <div class="wcfm-clearfix"></div>
			</div>
			<div class="wcfm-clearfix"></div>
		</div>
	
		<div class="wcfm-clearfix"></div>
		<?php
		do_action( 'after_wcfm_fancy_product_designer' );
		?>
	</div>
</div>

