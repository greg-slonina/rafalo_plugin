<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;
global $woocommerce;

$default_gift_product = wc_get_product( get_option ( YWGC_PRODUCT_PLACEHOLDER ) );

do_action( 'yith_ywgc_gift_card_preview_end', $default_gift_product ); //Load the modal content

?>

<form class="gift-cards_form cart" method="post" enctype='multipart/form-data' data-product_id="<?php echo absint( yit_get_prop($default_gift_product, 'id' )); ?>">

	<input type="hidden" name="ywgc-is-digital" value="1" />
	<input type='hidden' name='ywgc_has_custom_design' value='1' />


	<?php if ( ! $product->is_purchasable() ) : ?>
		<p class="gift-card-not-valid">
			<?php _e( "This product cannot be purchased", 'yith-woocommerce-gift-cards' ); ?>
		</p>
	<?php else : ?>

		<?php do_action( 'yith_ywgc_gift_card_design_section', $default_gift_product ); ?>

		<?php do_action( 'yith_ywgc_gift_card_delivery_info_section', $default_gift_product ); ?>


		<?php if ( 'yes' == get_option('ywgc_gift_this_product_include_shipping', 'no') ) : ?>
			<div class="ywgc-include-shipping-container">
				<input type="checkbox" id="ywgc-include-shipping-checkbox" name="ywgc-include-shipping-checkbox">
				<label for="ywgc-include-shipping-checkbox"><?php echo apply_filters('ywgc_include_shipping_label',esc_html__( "Pay also the shipping cost for this item", 'yith-woocommerce-gift-cards' )); ?></label>
			</div>

			<div class="ywgc-country-select-main-container ywgc-hidden">
				<div class="ywgc-country-select-container">

					<div class="ywgc-country-select-title">
						<p><?php echo esc_html__( "Ship to:", 'yith-woocommerce-gift-cards' ); ?></p>
					</div>

					<div class="ywgc-country-select-div">
						<p class="form-row form-row-wide" id="ywgc-country-select_field">
							<select name="ywgc-country-select" id="ywgc-country-select" class="country_to_state country_select" rel="ywgc-country-select">
								<option value="default"><?php esc_html_e( 'Select a country / region&hellip;', 'woocommerce' ); ?></option>
								<?php
								foreach ( WC()->countries->get_shipping_countries() as $key => $value ) {
									echo '<option value="' . esc_attr( $key ) . '"' . selected( WC()->customer->get_shipping_country(), esc_attr( $key ), false ) . '>' . esc_html( $value ) . '</option>';
								}
								?>
							</select>
						</p>
					</div>

					<div class="ywgc-postal-code-select">
						<input type="text" id="ywgc-postal-code-input" placeholder="<?php echo esc_html__( "Postal code", 'yith-woocommerce-gift-cards' ); ?>">
					</div>
				</div>

				<div class="ywgc-gift-this-product-totals" >
					<p class="ywgc-gift-card-product-total"><?php echo esc_html__( "Product value: ", 'yith-woocommerce-gift-cards' ); ?>
						<span class="ywgc-gift-card-product-total-value"><?php echo wc_price( $product->get_price() )?></span></p>

					<?php
					$frontend_class = YITH_YWGC_Frontend_Premium::get_instance();
					$default_shipping_cost = ywgc_string_to_float( $frontend_class->ywgc_get_shipping_cost_by_country( WC()->customer->get_shipping_country() ) );

					?>

					<p class="ywgc-gift-card-shipping-total"><?php echo esc_html__( "Shipping cost: ", 'yith-woocommerce-gift-cards' ); ?>
						<span class="ywgc-gift-card-shipping-total-value"><?php echo wc_price( $default_shipping_cost )?></span></p>

					<p class="ywgc-gift-card-total"><?php echo esc_html__( "Gift Card total: ", 'yith-woocommerce-gift-cards' ); ?>
						<span class="ywgc-gift-card-total-value"><?php echo wc_price( $product->get_price() + $default_shipping_cost ) ?></span></p>
					<input type="hidden" name="ywgc-gift-this-product-total-value" class="ywgc-gift-this-product-total-value" value="<?php echo (float)$product->get_price() + (float)$default_shipping_cost; ?>">
				</div>
			</div>




		<?php endif; ?>

	<?php endif; ?>

</form>
