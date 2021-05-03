<?php
/**
 * WCFM plugin views
 *
 * Plugin WC Lottery Products Manage Views
 *
 * @author 		WC Lovers
 * @package 	wcfmu/thirdparty/views
 * @version   3.5.0
 */
global $wp, $WCFM, $WCFMu;

$_min_tickets = '';
$_max_tickets = '';
$_max_tickets_per_user = '';
$_lottery_num_winners = '';
$_lottery_multiple_winner_per_user = '';
$_lottery_price = '';
$_lottery_sale_price = ''; 
$_lottery_dates_from = '';
$_lottery_dates_to = '';


if( isset( $wp->query_vars['wcfm-products-manage'] ) && !empty( $wp->query_vars['wcfm-products-manage'] ) ) {
	$product_id = $wp->query_vars['wcfm-products-manage'];
	if( $product_id ) {
		$_min_tickets = ( $min_tickets = get_post_meta( $product_id, '_min_tickets', true ) ) ?  $min_tickets  : '';
		$_max_tickets = ( $max_tickets = get_post_meta( $product_id, '_max_tickets', true ) ) ?  $max_tickets  : '';
		$_max_tickets_per_user = ( $tickets_per_user = get_post_meta( $product_id, '_max_tickets_per_user', true ) ) ?  $tickets_per_user  : '';
		$_lottery_num_winners = ( $num_winners = get_post_meta( $product_id, '_lottery_num_winners', true ) ) ?  $num_winners  : '';
		$_lottery_multiple_winner_per_user = ( $multiple_winner_per_user = get_post_meta( $product_id, '_lottery_multiple_winner_per_user', true ) ) ?  $multiple_winner_per_user  : 'no';
		$_lottery_price = ( $lottery_price = get_post_meta( $product_id, '_lottery_price', true ) ) ?  $lottery_price  : '';
		$_lottery_sale_price = ( $sale_price = get_post_meta( $product_id, '_lottery_sale_price', true ) ) ?  $sale_price  : '';
		$_lottery_dates_from = ( $date = get_post_meta( $product_id, '_lottery_dates_from', true ) ) ?  $date  : '';
		$_lottery_dates_to = ( $date = get_post_meta( $product_id, '_lottery_dates_to', true ) ) ?  $date  : '';
	}
}

?>

<div class="page_collapsible products_manage_wc_lottery lottery non-variable-subscription" id="wcfm_products_manage_form_lottery_head"><label class="wcfmfa fa-dribbble"></label><?php _e('Lottery', 'wc-frontend-manager-ultimate'); ?><span></span></div>
<div class="wcfm-container lottery non-variable-subscription">
	<div id="wcfm_products_manage_form_wc_lottery_expander" class="wcfm-content">
		<?php
		$WCFM->wcfm_fields->wcfm_generate_form_field( array( 
			"_min_tickets" => array( 'label' => __( 'Min tickets', 'wc_lottery' ), 'type' => 'text', 'class' => 'wcfm-text wcfm_ele lottery', 'label_class' => 'wcfm_title lottery', 'hints' => __( 'Minimum tickets to be sold', 'wc_lottery' ), 'value' => $_min_tickets ),
			"_max_tickets" => array( 'label' => __( 'Max tickets', 'wc_lottery' ), 'type' => 'text', 'class' => 'wcfm-text wcfm_ele lottery', 'label_class' => 'wcfm_title lottery', 'hints' => __( 'Minimum tickets to be sold', 'wc_lottery' ), 'value' => $_max_tickets ),
			"_max_tickets_per_user" => array( 'label' => __( 'Max tickets per user', 'wc_lottery' ), 'type' => 'text', 'class' => 'wcfm-text wcfm_ele lottery', 'label_class' => 'wcfm_title lottery', 'hints' => __( 'Max tickets sold per user', 'wc_lottery' ), 'value' => $_max_tickets_per_user ),
			"_lottery_num_winners" => array( 'label' => __( 'Number of winners', 'wc_lottery' ), 'type' => 'text', 'class' => 'wcfm-text wcfm_ele lottery', 'label_class' => 'wcfm_title lottery', 'hints' => __( 'Number of possible winners', 'wc_lottery' ), 'value' => $_lottery_num_winners ),
			"_lottery_multiple_winner_per_user" => array( 'label' => __( 'Multiple prizes per user?', 'wc_lottery' ), 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele lottery', 'label_class' => 'wcfm_title checkbox_title lottery', 'hints' => __( 'Allow multiple prizes for single user if there are multiple lottery winners', 'wc_lottery' ), 'dfvalue' => $_lottery_multiple_winner_per_user, 'value' => 'yes' ),
			"_lottery_price" => array( 'label' => __( 'Price', 'wc_lottery' ). ' ('.get_woocommerce_currency_symbol().')', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele lottery', 'label_class' => 'wcfm_title lottery', 'hints' => __( 'Lottery Price', 'wc_lottery' ), 'value' => $_lottery_price ),
			"_lottery_sale_price" => array( 'label' => __( 'Sale Price', 'wc_lottery' ). ' ('.get_woocommerce_currency_symbol().')', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele lottery', 'label_class' => 'wcfm_title lottery', 'hints' => __( 'Lottery Sale Price', 'wc_lottery' ), 'value' => $_lottery_sale_price ),
			
			"_lottery_dates_from" => array( 'label' => __( 'Lottery from date', 'wc_lottery' ) , 'type' => 'text', 'placeholder' => 'YYYY-MM-DD hh:mm:ss', 'class' => 'wcfm-text wcfm_ele lottery', 'label_class' => 'wcfm_title lottery', 'value' => $_lottery_dates_from ),
			"_lottery_dates_to" => array( 'label' => __( 'Lottery to date', 'wc_lottery' ) , 'type' => 'text', 'placeholder' => 'YYYY-MM-DD hh:mm:ss', 'class' => 'wcfm-text wcfm_ele lottery', 'label_class' => 'wcfm_title lottery', 'value' => $_lottery_dates_to ),
			
			) );
		?>
	</div>
</div>