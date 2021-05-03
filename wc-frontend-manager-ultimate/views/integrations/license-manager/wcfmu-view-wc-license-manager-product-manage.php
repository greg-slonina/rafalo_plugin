<?php
/**
 * WCFM plugin views
 *
 * Plugin WC License Manager Products Manage Views
 *
 * @author 		WC Lovers
 * @package 	wcfmu/views/integration/license-manager
 * @version   6.4.0
 */
 
use LicenseManagerForWooCommerce\Repositories\Resources\Generator as GeneratorResourceRepository;

global $wp, $WCFM, $WCFMu;

$product_id = 0;

$licensed = 'normal';
$deliveredQuantity = '';
$generatorId = '';
$useGenerator = 1;
$useStock = '';

if( wcfm_is_vendor() ) {
	$vendor_id = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );
	$generators = GeneratorResourceRepository::instance()->findAllBy( array( 'created_by' => $vendor_id ) );
} else {
	$generators = GeneratorResourceRepository::instance()->findAll();
}

$generatorOptions  = array('' => __('Please select a generator', 'lmfwc'));
if ($generators) {
		/** @var GeneratorResourceModel $generator */
		foreach ($generators as $generator) {
				$generatorOptions[$generator->getId()] = sprintf(
						'(#%d) %s',
						$generator->getId(),
						$generator->getName()
				);
		}
}


if( isset( $wp->query_vars['wcfm-products-manage'] ) && !empty( $wp->query_vars['wcfm-products-manage'] ) ) {
	$product_id = $wp->query_vars['wcfm-products-manage'];
	if( $product_id ) {
		$licensed = get_post_meta( $product_id, 'lmfwc_licensed_product', true );
		$deliveredQuantity = get_post_meta( $product_id, 'lmfwc_licensed_product_delivered_quantity', true );
		$generatorId = get_post_meta( $product_id, 'lmfwc_licensed_product_assigned_generator', true );
		$useGenerator = get_post_meta( $product_id, 'lmfwc_licensed_product_use_generator', true );
		$useStock = get_post_meta( $product_id, 'lmfwc_licensed_product_use_stock', true );
	}
}

?>
<div class="page_collapsible products_manage_wclicense_manager simple non-variable-subscription" id="wcfm_products_manage_form_wclicense_manager_head"><label class="wcfmfa fa-key"></label><?php _e('License Manager', 'wc-frontend-manager-ultimate'); ?><span></span></div>
<div class="wcfm-container simple non-variable-subscription">
	<div id="wcfm_products_manage_form_wclicense_manager_expander" class="wcfm-content">
		<?php
		$wclicense_manager_fields = apply_filters( 'wcfm_product_manage_wc_license_manager_fields', array( 
					"lmfwc_licensed_product" => array( 'label' => __( 'Sell license keys', 'lmfwc' ) , 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele simple', 'label_class' => 'wcfm_title checkbox_title', 'value' => 1, 'dfvalue' => $licensed, 'hints' => __('Sell license keys for this product', 'lmfwc') ),
					"lmfwc_licensed_product_delivered_quantity" => array( 'label' => __('Delivered quantity', 'lmfwc') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele simple', 'label_class' => 'wcfm_title simple', 'value' => $deliveredQuantity ? $deliveredQuantity : 1, 'hints' => __('Defines the amount of license keys to be delivered upon purchase.', 'lmfwc') ),
					"lmfwc_licensed_product_use_generator" => array( 'label' => __('Generate license keys', 'lmfwc') , 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele simple', 'label_class' => 'wcfm_title checkbox_title simple', 'value' => 1, 'dfvalue' => $useGenerator, 'hints' => __('Automatically generate license keys with each sold product', 'lmfwc') ),
					"lmfwc_licensed_product_assigned_generator" => array( 'label' => __('Assign generator', 'lmfwc'), 'type' => 'select', 'class' => 'wcfm-select wcfm_ele simple', 'label_class' => 'wcfm_title simple', 'options' => $generatorOptions, 'value' => $generatorId ),
					"lmfwc_licensed_product_use_stock" => array( 'label' => __('Sell from stock', 'lmfwc') , 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele simple', 'label_class' => 'wcfm_title checkbox_title simple', 'value' => 1, 'dfvalue' => $useStock, 'hints' => __('Sell license keys from the available stock.', 'lmfwc') ),
		), $product_id );
		
		$WCFM->wcfm_fields->wcfm_generate_form_field( $wclicense_manager_fields );
		
		?>
	</div>
</div>