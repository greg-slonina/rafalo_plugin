<form method="post" class="form-check-gift-card-balance" name="form-check-gift-card-balance">
        <label for="ywgc-check-code" style="margin-right: 15px;"><?php _e ( "Gift Card code: ", 'yith-woocommerce-gift-cards' ); ?></label>
        <input type="text" name="ywgc-check-code" id="ywgc-check-code" style="width: 300px; margin-right: 5px;" value="" placeholder="<?php _e ( "Enter code", 'yith-woocommerce-gift-cards' ); ?>">
        <button type="submit"><?php _e ( "Submit", 'yith-woocommerce-gift-cards' ); ?></button>
</form>

<?php
if ( isset( $_POST["ywgc-check-code"] ) ){

	$code =  $_POST["ywgc-check-code"];

	$args = array(
		'gift_card_number' => $code,
	);

	$gift_card = new YITH_YWGC_Gift_Card( $args );

	if ( is_object( $gift_card ) && $gift_card->ID != 0 ){
		echo '<div class="ywgc-check-code-gift-card-balance" style="background-color: #edf8c0; padding: 15px; width: fit-content; margin-top: 10px;">' . esc_html__( "Your gift card balance: ", 'yith-woocommerce-gift-cards' ) . '<span style="font-weight: bold">' . wc_price( $gift_card->get_balance() ) . '</span></div>';
		echo '<br>';
	}
	else{
		echo '<div style="color: red; margin-top: 10px;">' . esc_html__( "This code is not associated to any existing gift card. Try again.", 'yith-woocommerce-gift-cards' ) . '</div>';
		echo '<br>';
	}
}
