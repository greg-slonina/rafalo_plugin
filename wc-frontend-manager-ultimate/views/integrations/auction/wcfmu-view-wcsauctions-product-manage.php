<?php
/**
 * WCFM plugin views
 *
 * Plugin WC Simple Auctions Products Manage Views
 *
 * @author 		WC Lovers
 * @package 	wcfmu/views/thirdparty
 * @version   2.4.0
 */
global $wp, $WCFM, $WCFMu;

$product_id = 0;
$_auction_item_condition = 'new';
$_auction_type = 'normal';
$_auction_proxy = get_option('simple_auctions_proxy_auction_on', 'no');
$_auction_sealed = 'no';
$_auction_start_price = '';
$_auction_bid_increment = '';
$_auction_reserved_price = '';
$_regular_price = '';
$_auction_dates_from = '';
$_auction_dates_to = '';

$relist_auction_dates_from = '';
$relist_auction_dates_to = '';

$_auction_automatic_relist = '';
$_auction_relist_fail_time = '';
$_auction_relist_not_paid_time = '';
$_auction_relist_duration = '';

if( isset( $wp->query_vars['wcfm-products-manage'] ) && !empty( $wp->query_vars['wcfm-products-manage'] ) ) {
	$product_id = $wp->query_vars['wcfm-products-manage'];
	if( $product_id ) {
		$_auction_item_condition = get_post_meta( $product_id, '_auction_item_condition', true );
		$_auction_type = get_post_meta( $product_id, '_auction_type', true );
		$_auction_proxy = in_array( get_post_meta( $product_id, '_auction_proxy', true ), array( '0', 'yes' ) ) ? get_post_meta( $product_id, '_auction_proxy', true ) : $_auction_proxy;
		$_auction_sealed = get_post_meta( $product_id, '_auction_sealed', true );
		$_auction_start_price = get_post_meta( $product_id, '_auction_start_price', true );
		$_auction_bid_increment = get_post_meta( $product_id, '_auction_bid_increment', true );
		$_auction_reserved_price = get_post_meta( $product_id, '_auction_reserved_price', true );
		$_regular_price = get_post_meta( $product_id, '_regular_price', true );
		$_auction_dates_from = get_post_meta( $product_id, '_auction_dates_from', true );
		$_auction_dates_to = get_post_meta( $product_id, '_auction_dates_to', true );
		
		$_auction_automatic_relist = get_post_meta( $product_id, '_auction_automatic_relist', true );
		$_auction_relist_fail_time = get_post_meta( $product_id, '_auction_relist_fail_time', true );
		$_auction_relist_not_paid_time = get_post_meta( $product_id, '_auction_relist_not_paid_time', true );
		$_auction_relist_duration = get_post_meta( $product_id, '_auction_relist_duration', true );
	}
}

?>
<div class="page_collapsible products_manage_yithauction auction non-variable-subscription" id="wcfm_products_manage_form_auction_head"><label class="wcfmfa fa-gavel"></label><?php _e('Auction', 'wc-frontend-manager-ultimate'); ?><span></span></div>
<div class="wcfm-container auction non-variable-subscription">
	<div id="wcfm_products_manage_form_yithauction_expander" class="wcfm-content">
		<?php
		$wcsauction_fields = apply_filters( 'wcfm_product_manage_wcsauction_fields', array( 
					"_auction_item_condition" => array( 'label' => __('Item condition', 'wc-frontend-manager-ultimate') , 'type' => 'select', 'class' => 'wcfm-select wcfm_ele auction', 'label_class' => 'wcfm_title auction', 'options' => array( 'new' => __( 'New', 'wc-frontend-manager-ultimate' ), 'used' => __( 'Used', 'wc-frontend-manager-ultimate' ) ), 'value' => $_auction_item_condition ),
					"_auction_type" => array( 'label' => __('Auction type', 'wc-frontend-manager-ultimate') , 'type' => 'select', 'class' => 'wcfm-select wcfm_ele auction', 'label_class' => 'wcfm_title auction', 'options' => array( 'normal' => __( 'Normal', 'wc-frontend-manager-ultimate' ), 'reverse' => __( 'Reverse', 'wc-frontend-manager-ultimate' ) ), 'value' => $_auction_type ),
					"_auction_proxy" => array( 'label' => __('Proxy bidding?', 'wc-frontend-manager-ultimate') , 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele auction', 'label_class' => 'wcfm_title auction', 'value' => 'yes', 'dfvalue' => $_auction_proxy, 'hints' => __( 'Enable proxy bidding', 'wc_simple_auctions' ) ),
					"_auction_sealed" => array( 'label' => __('Sealed Bid?', 'wc_simple_auctions') , 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele auction', 'label_class' => 'wcfm_title auction', 'value' => 'yes', 'dfvalue' => $_auction_sealed, 'hints' => __( 'In this type of auction all bidders simultaneously submit sealed bids so that no bidder knows the bid of any other participant. The highest bidder pays the price they submitted. If two bids with same value are placed for auction the one which was placed first wins the auction.', 'wc_simple_auctions' ) ),
					"_auction_start_price" => array( 'label' => __('Starting Price', 'wc-frontend-manager-ultimate') . '(' . get_woocommerce_currency_symbol() . ')' , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele auction', 'label_class' => 'wcfm_title auction', 'value' => $_auction_start_price ),
					"_auction_bid_increment" => array( 'label' => __('Bid up', 'wc-frontend-manager-ultimate') . '(' . get_woocommerce_currency_symbol() . ')' , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele auction', 'label_class' => 'wcfm_title auction', 'value' => $_auction_bid_increment ),
					"_auction_reserved_price" => array( 'label' => __('Reserve price', 'wc-frontend-manager-ultimate') . '(' . get_woocommerce_currency_symbol() . ')' , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele auction', 'label_class' => 'wcfm_title auction', 'value' => $_auction_reserved_price, 'hints' => __( 'A reserve price is the lowest price at which you are willing to sell your item. If you don\'t want to sell your item below a certain price, you can set a reserve price. The amount of your reserve price is not disclosed to your bidders, but they will see that your auction has a reserve price and whether or not the reserve has been met. If a bidder does not meet that price, you are not obligated to sell your item.', 'wc-frontend-manager-ultimate' ) ),
					"_regular_price" => array( 'label' => __('Buy it now price', 'wc-frontend-manager-ultimate') . '(' . get_woocommerce_currency_symbol() . ')' , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele auction', 'label_class' => 'wcfm_title auction', 'value' => $_regular_price, 'hints' => __( 'Buy it now disappears when bid exceeds the Buy now price for normal auction, or is lower than reverse auction', 'wc-frontend-manager-ultimate' ) ),
					"_auction_dates_from" => array( 'label' => __('Auction Date From', 'wc-frontend-manager-ultimate') , 'type' => 'text', 'placeholder' => _x('From&hellip; YYYY-MM-DD HH:MM', 'placeholder', 'wc_simple_auctions'), 'attributes' => array( 'maxlength' => 16, 'pattern' => '[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])[ ](0[0-9]|1[0-9]|2[0-4]):(0[0-9]|1[0-9]|2[0-9]|3[0-9]|4[0-9]|5[0-9])' ), 'class' => 'wcfm-text wcfm_ele auction', 'label_class' => 'wcfm_title auction', 'value' => $_auction_dates_from ),
					"_auction_dates_to" => array( 'label' => __('Auction Date To', 'wc-frontend-manager-ultimate') , 'type' => 'text', 'placeholder' => _x('To&hellip; YYYY-MM-DD HH:MM', 'placeholder', 'wc_simple_auctions'), 'attributes' => array( 'maxlength' => 16, 'pattern' => '[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])[ ](0[0-9]|1[0-9]|2[0-4]):(0[0-9]|1[0-9]|2[0-9]|3[0-9]|4[0-9]|5[0-9])' ), 'class' => 'wcfm-text wcfm_ele auction', 'label_class' => 'wcfm_title auction', 'value' => $_auction_dates_to ),
		), $product_id );
		
		if ( get_option( 'simple_auctions_sealed_on', 'no' ) != 'yes' ) {
			unset( $wcsauction_fields['_auction_sealed'] );
		}
		
		$WCFM->wcfm_fields->wcfm_generate_form_field( $wcsauction_fields );
		
		if( $product_id ) {
			$product = wc_get_product( $product_id );
			if ((method_exists( $product, 'get_type') && $product->get_type() == 'auction') && $product->get_auction_closed() && !$product->get_auction_payed()) {
				
				echo '<div style="margin:15px auto;"><div class="wcfm-clearfix"></div><h2>' . esc_html( __( 'Relist', 'wc_simple_auctions' ) ) . '</h2><div class="wcfm-clearfix"></div></div><div class="store_address store_address_wrap">';
				
				$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_product_manage_wcsauction_relist_fields', array( 
							"_relist_auction_dates_from" => array( 'label' => __( 'Relist Auction Dates', 'wc_simple_auctions' ), 'type' => 'text', 'placeholder' => _x('From&hellip; YYYY-MM-DD HH:MM', 'placeholder', 'wc_simple_auctions'), 'attributes' => array( 'maxlength' => 16, 'pattern' => '[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])[ ](0[0-9]|1[0-9]|2[0-4]):(0[0-9]|1[0-9]|2[0-9]|3[0-9]|4[0-9]|5[0-9])' ), 'class' => 'wcfm-text wcfm_ele auction', 'label_class' => 'wcfm_title auction', 'value' => $relist_auction_dates_from ),
							"_relist_auction_dates_to" => array( 'label' => '', 'type' => 'text', 'placeholder' => _x('To&hellip; YYYY-MM-DD HH:MM', 'placeholder', 'wc_simple_auctions'), 'attributes' => array( 'maxlength' => 16, 'pattern' => '[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])[ ](0[0-9]|1[0-9]|2[0-4]):(0[0-9]|1[0-9]|2[0-9]|3[0-9]|4[0-9]|5[0-9])' ), 'class' => 'wcfm-text wcfm_ele auction', 'label_class' => 'wcfm_title auction', 'value' => $relist_auction_dates_to )
				), $product_id ) );
				
				echo '</div><div class="wcfm-clearfix"></div>';
			}
		}
		
		echo '<div style="margin:15px auto;"><div class="wcfm-clearfix"></div><h2>' . esc_html(apply_filters('woocommerce_auction_history_heading', __('Auction automatic relist', 'wc_simple_auctions'))) . '</h2><div class="wcfm-clearfix"></div></div><div class="store_address store_address_wrap">'; 
		
		$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_product_manage_wcsauction_autorelist_fields', array( 
					"_auction_automatic_relist" => array( 'label' => __('Automatic relist auction', 'wc-frontend-manager-ultimate') , 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele auction', 'label_class' => 'wcfm_title auction', 'value' => 'yes', 'dfvalue' => $_auction_automatic_relist, 'hints' => __('Enable automatic relisting', 'wc_simple_auctions') ),
					"_auction_relist_fail_time" => array( 'label' => __('Relist if fail after n hours', 'wc-frontend-manager-ultimate') , 'type' => 'number', 'class' => 'wcfm-text wcfm_ele auction', 'label_class' => 'wcfm_title auction', 'value' => $_auction_relist_fail_time ),
					"_auction_relist_not_paid_time" => array( 'label' => __('Relist if not paid after n hours', 'wc-frontend-manager-ultimate') , 'type' => 'number', 'class' => 'wcfm-text wcfm_ele auction', 'label_class' => 'wcfm_title auction', 'value' => $_auction_relist_not_paid_time ),
					"_auction_relist_duration" => array( 'label' => __('Relist auction duration in h', 'wc-frontend-manager-ultimate') , 'type' => 'number', 'class' => 'wcfm-text wcfm_ele auction', 'label_class' => 'wcfm_title auction', 'value' => $_auction_relist_duration ),
		), $product_id ) );
		
		echo '</div><div class="wcfm-clearfix"></div>';
		?>
		<div class="wcfm-clearfix"></div>
	</div>
</div>