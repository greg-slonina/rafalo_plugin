<?php
/**
 * Show a section with a product suggestion if the gift card was purchased as a gift for a product in the shop
 *
 * @author  YITHEMES
 * @package yith-woocommerce-gift-cards-premium\templates\emails
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( get_option ( 'ywgc_gift_this_product_apply_gift_card', 'yes' ) == 'yes'){
    $args = array(
        YWGC_ACTION_ADD_DISCOUNT_TO_CART => $gift_card->gift_card_number,
        YWGC_ACTION_VERIFY_CODE          => YITH_YWGC ()->hash_gift_card ( $gift_card ),
        YWGC_ACTION_PRODUCT_ID           =>  is_object( $product ) ? $product->get_id() : '',
        YWGC_ACTION_GIFT_THIS_PRODUCT    => 'yes',
    );
}
else{
    $args = array(
        YWGC_ACTION_PRODUCT_ID           =>  is_object( $product ) ? $product->get_id() : '',
        YWGC_ACTION_GIFT_THIS_PRODUCT    => 'yes',
    );
}

if ( get_option ( 'ywgc_gift_this_product_button_redirect', 'to_product_page' ) == 'to_customize_page' )
    $product_link = esc_url ( add_query_arg ( $args, get_page_link( get_option ( 'ywgc_gift_this_product_redirected_page' ) ) ) );
else
    $product_link = esc_url ( add_query_arg ( $args, get_permalink( yit_get_prop( $product, 'id' ) ) ) );

?>

<tr class="ywgc-suggested-text ywgc-product-suggested">
    <td colspan="2">
        <?php echo apply_filters('yith_ywgc_product_suggested_message', esc_html__( "Maybe you can use the gift card for this item:", 'yith-woocommerce-gift-cards' )); ?>
    </td>
</tr>

<?php if ( is_object( $product ) ) : ?>
<tr class="ywgc-product-suggested">


    <td class="ywgc-product-title-td">

        <p class="ywgc-product-title"><?php echo $product->get_name(); ?></p>

	    <?php
	    if ( 'yes' === get_option( 'ywgc_display_price', 'yes' ) && apply_filters( 'ywgc_display_price_template_suggestion', true ) ) :?>
            <p class="ywgc-product-price"><?php echo $product->get_price_html(); ?></p>
        <?php endif; ?>


        <?php if ( $context == 'email' ): ?>
            <a class="ywgc-product-link" href="<?php echo $product_link; ?>">
                <?php echo get_option ( 'ywgc_gift_this_product_email_button_label', 'Go to the product' ) ; ?></a>
        <?php endif; ?>
    </td>

    <td class="ywgc-product-image-td">
        <img class="ywgc-product-image" src="<?php echo apply_filters( 'ywgc_custom_product_suggestion_image_url', $product->get_image_id() ? current( wp_get_attachment_image_src( $product->get_image_id(), 'thumbnail' ) ) : wc_placeholder_img_src(), $context ); ?>" />
    </td>



</tr>
<?php endif; ?>

