<?php
/**
 * WCFM plugin views
 *
 * Plugin YITH Auctions Products Manage Views
 *
 * @author 		WC Lovers
 * @package 	wcfmu/thirdparty/views
 * @version   2.4.0
 */
global $wp, $WCFM, $WCFMu;

$product_id = 0;
$_yith_auction_start_price = '';
$_yith_auction_bid_increment = '';
$_yith_auction_minimum_increment_amount = '';
$_yith_auction_reserve_price = '';
$_yith_auction_buy_now = '';
$_yith_auction_for = '';
$_yith_auction_to = '';
$_yith_check_time_for_overtime_option = '';
$_yith_overtime_option = '';
$_yith_wcact_auction_automatic_reschedule = '';
$_yith_wcact_automatic_reschedule_auction_unit = '';
$_yith_wcact_upbid_checkbox = '';
$_yith_wcact_overtime_checkbox = '';

if( isset( $wp->query_vars['wcfm-products-manage'] ) && !empty( $wp->query_vars['wcfm-products-manage'] ) ) {
	$product_id = $wp->query_vars['wcfm-products-manage'];
	if( $product_id ) {
		$_yith_auction_start_price = get_post_meta( $product_id, '_yith_auction_start_price', true );
		$_yith_auction_bid_increment = get_post_meta( $product_id, '_yith_auction_bid_increment', true );
		$_yith_auction_minimum_increment_amount = get_post_meta( $product_id, '_yith_auction_minimum_increment_amount', true );
		$_yith_auction_reserve_price = get_post_meta( $product_id, '_yith_auction_reserve_price', true );
		$_yith_auction_buy_now = get_post_meta( $product_id, '_yith_auction_buy_now', true );
		$_yith_auction_for = get_post_meta( $product_id, '_yith_auction_for', true );
		$_yith_auction_to = get_post_meta( $product_id, '_yith_auction_to', true );
		$_yith_check_time_for_overtime_option = get_post_meta( $product_id, '_yith_check_time_for_overtime_option', true );
		$_yith_overtime_option = get_post_meta( $product_id, '_yith_overtime_option', true );
		$_yith_wcact_auction_automatic_reschedule = get_post_meta( $product_id, '_yith_wcact_auction_automatic_reschedule', true );
		$_yith_wcact_automatic_reschedule_auction_unit = get_post_meta( $product_id, '_yith_wcact_automatic_reschedule_auction_unit', true );
		$_yith_wcact_upbid_checkbox = get_post_meta( $product_id, '_yith_wcact_upbid_checkbox', true );
		$_yith_wcact_overtime_checkbox = get_post_meta( $product_id, '_yith_wcact_overtime_checkbox', true );
		
		if( $_yith_auction_for ) $_yith_auction_for = get_date_from_gmt( date( 'Y-m-d H:i:s', absint( $_yith_auction_for ) ) );
		if( $_yith_auction_to ) $_yith_auction_to = get_date_from_gmt( date( 'Y-m-d H:i:s', absint( $_yith_auction_to ) ) );
	}
}

?>

<div class="page_collapsible products_manage_yithauction auction non-variable-subscription" id="wcfm_products_manage_form_auction_head"><label class="wcfmfa fa-gavel"></label><?php _e('Auction', 'wc-frontend-manager-ultimate'); ?><span></span></div>
<div class="wcfm-container auction non-variable-subscription">
	<div id="wcfm_products_manage_form_yithauction_expander" class="wcfm-content">
		<?php
		$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_product_manage_yithauction_fields', array( 
			"_yith_auction_start_price" => array( 'label' => __('Starting Price', 'wc-frontend-manager-ultimate') . '(' . get_woocommerce_currency_symbol() . ')' , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele auction', 'label_class' => 'wcfm_title auction', 'value' => $_yith_auction_start_price ),
			"_yith_auction_bid_increment" => array( 'label' => __('Bid up', 'wc-frontend-manager-ultimate') . '(' . get_woocommerce_currency_symbol() . ')' , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele auction', 'label_class' => 'wcfm_title auction', 'value' => $_yith_auction_bid_increment ),
			"_yith_auction_minimum_increment_amount" => array( 'label' => __('Minimum increment amount', 'wc-frontend-manager-ultimate') . '(' . get_woocommerce_currency_symbol() . ')' , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele auction', 'label_class' => 'wcfm_title auction', 'value' => $_yith_auction_minimum_increment_amount ),
			"_yith_auction_reserve_price" => array( 'label' => __('Reserve price', 'wc-frontend-manager-ultimate') . '(' . get_woocommerce_currency_symbol() . ')' , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele auction', 'label_class' => 'wcfm_title auction', 'value' => $_yith_auction_reserve_price ),
			"_yith_auction_buy_now" => array( 'label' => __('Buy it now price', 'wc-frontend-manager-ultimate') . '(' . get_woocommerce_currency_symbol() . ')' , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele auction', 'label_class' => 'wcfm_title auction', 'value' => $_yith_auction_buy_now ),
			"_yith_auction_for" => array( 'label' => __('Auction Date From', 'wc-frontend-manager-ultimate') , 'type' => 'text', 'placeholder' => 'YYYY-MM-DD hh:mm:ss', 'class' => 'wcfm-text wcfm_ele auction', 'label_class' => 'wcfm_title auction', 'value' => $_yith_auction_for ),
			"_yith_auction_to" => array( 'label' => __('Auction Date To', 'wc-frontend-manager-ultimate') , 'type' => 'text', 'placeholder' => 'YYYY-MM-DD hh:mm:ss', 'class' => 'wcfm-text wcfm_ele auction', 'label_class' => 'wcfm_title auction', 'value' => $_yith_auction_to ),
			"_yith_check_time_for_overtime_option" => array( 'label' => __('Time to add overtime', 'wc-frontend-manager-ultimate') , 'type' => 'number', 'class' => 'wcfm-text wcfm_ele auction', 'label_class' => 'wcfm_title auction', 'value' => $_yith_check_time_for_overtime_option, 'hints' => __( 'Number of minutes before auction ends to check if overtime added. (Override the settings option)', 'wc-frontend-manager-ultimate' ) ),
			"_yith_overtime_option" => array( 'label' => __('Overtime', 'wc-frontend-manager-ultimate') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele auction', 'label_class' => 'wcfm_title auction', 'value' => $_yith_overtime_option, 'hints' => __( 'Number of minutes by which the auction will be extended. (Overrride the settings option)', 'wc-frontend-manager-ultimate' ) ),
			"_yith_wcact_auction_automatic_reschedule" => array( 'label' => __('Time value for automatic rescheduling', 'wc-frontend-manager-ultimate') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele auction', 'label_class' => 'wcfm_title auction', 'value' => $_yith_wcact_auction_automatic_reschedule ),
			"_yith_wcact_automatic_reschedule_auction_unit" => array( 'label' => __('Select unit for automatic rescheduling', 'wc-frontend-manager-ultimate') , 'type' => 'select', 'class' => 'wcfm-select wcfm_ele auction', 'label_class' => 'wcfm_title auction', 'options' => array( 'days' => __( 'days', 'wc-frontend-manager-ultimate' ), 'hours' => __( 'hours', 'wc-frontend-manager-ultimate' ), 'minutes' => __( 'minutes', 'wc-frontend-manager-ultimate' ) ), 'value' => $_yith_wcact_automatic_reschedule_auction_unit ),
			"_yith_wcact_upbid_checkbox" => array( 'label' => __('Show bid up', 'wc-frontend-manager-ultimate') , 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele auction', 'label_class' => 'wcfm_title auction', 'value' => 'yes', 'dfvalue' => $_yith_wcact_upbid_checkbox ),
			"_yith_wcact_overtime_checkbox" => array( 'label' => __('Show overtime', 'wc-frontend-manager-ultimate') , 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele auction', 'label_class' => 'wcfm_title auction', 'value' => 'yes', 'dfvalue' => $_yith_wcact_overtime_checkbox ),
			), $product_id) );
		?>
	</div>
</div>