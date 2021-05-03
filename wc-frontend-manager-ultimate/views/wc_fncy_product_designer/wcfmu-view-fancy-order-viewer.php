<?php
global $wp, $WCFM, $WCFMu, $wp_query;

$order_id = 0;
if( isset( $wp->query_vars['wcfm-orders-details'] ) && !empty( $wp->query_vars['wcfm-orders-details'] ) ) {
	$order_id = absint($wp->query_vars['wcfm-orders-details']);
} else {
	return;
}

if( !$order_id ) return;

$order = wc_get_order( $order_id );

if( !is_a( $order, 'WC_Order' ) ) return;

?>

<div class="wcfm-clearfix"></div>
<br />
<!-- collapsible -->
<div class="page_collapsible orders_details_fancy_product" id="sm_order_fancy_product_options"><?php _e('Fancy Product Design', 'wc-frontend-manager-ultimate'); ?><span></span></div>
<div class="wcfm-container orders_details_fancy_product_expander_container">
	<div id="wcfm_fpd_order_details_expander" class="wcfm-content">
		<div class="wcfm-clearfix"></div>
		
		<div id="fpd-order">
		  <div id="fpd-react-root"></div>
		</div>
		
	</div>
</div>