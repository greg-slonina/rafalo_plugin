<?php
/**
 * WCFM plugin view
 *
 * WCFM WC Deposits Product Manage View
 *
 * @author 		WC Lovers
 * @package 	wcfmu/views/thirdparty
 * @version   3.5.9
 */
 
global $wp, $WCFM, $WCFMu;

if( !apply_filters( 'wcfm_is_allow_wc_deposits', true ) ) {
	return;
}

$product_id = '';
$_wc_deposit_enabled = '';
$_wc_deposit_type = '';
$_wc_deposit_multiple_cost_by_booking_persons = 'no';
$_wc_deposit_amount = '';
$_wc_deposit_payment_plans = array();
$_wc_deposit_selected_type = '';

if( isset( $wp->query_vars['wcfm-products-manage'] ) && !empty( $wp->query_vars['wcfm-products-manage'] ) ) {
	$product_id = $wp->query_vars['wcfm-products-manage'];
	if( $product_id ) {
		$_wc_deposit_enabled = get_post_meta( $product_id, '_wc_deposit_enabled', true );
		$_wc_deposit_type = get_post_meta( $product_id, '_wc_deposit_type', true );
		$_wc_deposit_multiple_cost_by_booking_persons = get_post_meta( $product_id, '_wc_deposit_multiple_cost_by_booking_persons', true );
		$_wc_deposit_amount = get_post_meta( $product_id, '_wc_deposit_amount', true );
		$_wc_deposit_payment_plans = (array) get_post_meta( $product_id, '_wc_deposit_payment_plans', true );
		$_wc_deposit_selected_type = get_post_meta( $product_id, '_wc_deposit_selected_type', true );
	}
}

$inherit_wc_deposit_enabled = $inherit_wc_deposit_type = $inherit_wc_deposit_selected_type = esc_html__( 'Inherit storewide settings', 'woocommerce-deposits' );

switch ( get_option( 'wc_deposits_default_type', 'percent' ) ) {
	case 'percent' :
		$inherit_wc_deposit_type .= ' (' . esc_html__( 'percent', 'woocommerce-deposits' ) . ')';
		break;
	case 'fixed' :
		$inherit_wc_deposit_type .= ' (' . esc_html__( 'fixed amount', 'woocommerce-deposits' ) . ')';
		break;
	case 'plan' :
		$inherit_wc_deposit_type .= ' (' . esc_html__( 'payment plan', 'woocommerce-deposits' ) . ')';
		break;
	case 'none' :
		$inherit_wc_deposit_type .= ' (' . esc_html__( 'none', 'woocommerce-deposits' ) . ')';
		break;
}
switch ( get_option( 'wc_deposits_default_enabled', 'no' ) ) {
	case 'optional' :
		$inherit_wc_deposit_enabled .= ' (' . esc_html__( 'yes, optional', 'woocommerce-deposits' ) . ')';
		break;
	case 'forced' :
		$inherit_wc_deposit_enabled .= ' (' . esc_html__( 'yes, required', 'woocommerce-deposits' ) . ')';
		break;
	case 'no' :
		$inherit_wc_deposit_enabled .= ' (' . esc_html__( 'no', 'woocommerce-deposits' ) . ')';
		break;
}
switch ( get_option( 'wc_deposits_default_selected_type', 'deposit' ) ) {
	case 'deposit' :
		$inherit_wc_deposit_selected_type .= ' (' . esc_html__( 'pay deposit', 'woocommerce-deposits' ) . ')';
		break;
	case 'full' :
		$inherit_wc_deposit_selected_type .= ' (' . esc_html__( 'pay in full', 'woocommerce-deposits' ) . ')';
		break;
}

$plan_ids = WC_Deposits_Plans_Manager::get_plan_ids();
$default_payment_plans = get_option( 'wc_deposits_default_plans', array() );

if( !$plan_ids ) {
	$plan_ids = array();
}
?>

<div class="page_collapsible products_manage_wc_deposits simple variable external grouped booking" id="wcfm_products_manage_form_wc_deposits_head"><label class="wcfmfa fa-credit-card"></label><?php _e('Deposits', 'wc-frontend-manager-ultimate'); ?><span></span></div>
<div class="wcfm-container simple variable external grouped booking">
	<div id="wcfm_products_manage_form_wc_deposits_expander" class="wcfm-content">
		<?php
		$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_product_manage_fields_wc_deposits', array(  
																																												"_wc_deposit_enabled" => array( 'label' => __('Enable Deposits', 'woocommerce-deposits') , 'type' => 'select', 'options'     => array( '' => $inherit_wc_deposit_enabled, 'optional' => __( 'Yes - deposits are optional', 'woocommerce-deposits' ), 'forced'   => __( 'Yes - deposits are required', 'woocommerce-deposits' ), 'no' => __( 'No', 'woocommerce-deposits' ) ), 'class' => 'wcfm-select', 'label_class' => 'wcfm_title', 'hints' => __( 'Allow customers to pay a deposit for this product.', 'woocommerce-deposits' ), 'value' => $_wc_deposit_enabled),
																																												"_wc_deposit_type" => array( 'label' => __('Deposit Type', 'woocommerce-deposits') , 'type' => 'select', 'options'     => array( '' => $inherit_wc_deposit_type, 'percent' => __( 'Percentage', 'woocommerce-deposits' ), 'fixed'   => __( 'Fixed Amount', 'woocommerce-deposits' ), 'plan' => __( 'Payment Plan', 'woocommerce-deposits' ) ), 'class' => 'wcfm-select', 'label_class' => 'wcfm_title', 'hints' => __( 'Choose how customers can pay for this product using a deposit.', 'woocommerce-deposits' ), 'value' => $_wc_deposit_type),
																																												"_wc_deposit_multiple_cost_by_booking_persons" => array( 'label' => __('Booking Persons', 'woocommerce-deposits') , 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele booking', 'label_class' => 'wcfm_title checkbox_title wcfm_ele booking', 'hints' => __( 'Multiply fixed deposits by the number of persons booking', 'woocommerce-deposits' ), 'value' => 'yes', 'dfvalue' => $_wc_deposit_multiple_cost_by_booking_persons),
																																												"_wc_deposit_amount" => array('label' => __('Deposit Amount', 'woocommerce-deposits') , 'type' => 'number', 'placeholder' => wc_format_localized_price( 0 ), 'class' => 'wcfm-text', 'label_class' => 'wcfm_title', 'hints' => __( 'The amount of deposit needed. Do not include currency or percent symbols.', 'woocommerce-deposits' ), 'value' => $_wc_deposit_amount ),
																																												"_wc_deposit_payment_plans" => array('label' => __('Choose some plans', 'woocommerce-deposits') , 'type' => 'select', 'class' => 'wcfm-select', 'label_class' => 'wcfm_title', 'options' => $plan_ids, 'attributes' => array( 'multiple' => true, 'style' => 'width:60%' ), 'hints' => __( 'Choose which payment plans customers can use for this product.', 'woocommerce-deposits' ), 'value' => $_wc_deposit_payment_plans ),
																																												"_wc_deposit_selected_type" => array( 'label' => __('Default Deposit Selected Type', 'woocommerce-deposits') , 'type' => 'select', 'options'     => array( '' => $inherit_wc_deposit_selected_type, 'deposit' => __( 'Pay Deposit', 'woocommerce-deposits' ), 'full'   => __( 'Pay in Full', 'woocommerce-deposits' ) ), 'class' => 'wcfm-select', 'label_class' => 'wcfm_title', 'hints' => __( 'Choose the default selected type of payment on page load.', 'woocommerce-deposits' ), 'value' => $_wc_deposit_selected_type),
																																							), $product_id ) );
		?>
	</div>
</div>
<div class="wcfm_clearfix"></div>