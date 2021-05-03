<?php

use WOOMC\API;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'YWGC_WC_Multi_Currency_module' ) ) {

	/**
	 *
	 * @class   YWGC_WC_Multi_Currency_module
	 *
	 * @since   1.0.0
	 * @author  Francisco Mendoza
	 */
	class YWGC_WC_Multi_Currency_module {

		/**
		 * Single instance of the class
		 *
		 * @since 1.0.0
		 */
		protected static $instance;


		/**
		 * Returns single instance of the class
		 *
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		public function __construct() {

			//Don't convert the gift card amount in the add to cart
			add_filter( 'woocommerce_multicurrency_pre_product_get_price', array( $this, 'block_cart_conversion', ), 10, 4 );

			add_filter( 'yith_ywgc_gift_card_coupon_amount', array( $this, 'apply_gift_card_in_customer_currency', ), 10, 2 );

			add_filter( 'yith_ywgc_gift_card_amount_before_deduct', array( $this, 'yith_ywgc_gift_card_amount_before_deduct', ), 10, 2 );

			add_filter( 'yith_wcgc_template_formatted_price', array( $this, 'yith_wcgc_template_formatted_price', ), 10, 3 );

			add_filter( 'yith_ywgc_get_gift_card_price', array( $this, 'yith_ywgc_get_gift_card_price_call_back', ), 10, 3 );


		}

		public function block_cart_conversion( $pre_value, $value, $product, $price_type = '' ) {

			if ( is_object($product) && $product->is_type('gift-card') ){
				$pre_value = $value;
			}

			return $pre_value;

		}

		public function apply_gift_card_in_customer_currency( $gift_card_balance, $gift_card ) {

			$currency_detector = new \WOOMC\Currency\Detector();

			$conversion_api = new \WOOMC\API();

			$to   = $currency_detector->currency();
			$from = $currency_detector->getDefaultCurrency();

			$gift_card_balance= $conversion_api->convert( $gift_card_balance, $to, $from );

			return $gift_card_balance;

		}


		public function yith_ywgc_gift_card_amount_before_deduct( $amount ) {

			$currency_detector = new \WOOMC\Currency\Detector();

			$conversion_api = new \WOOMC\API();

			$from   = $currency_detector->currency();
			$to = $currency_detector->getDefaultCurrency();

			$amount = $conversion_api->convert( $amount, $to, $from );

			return $amount;

		}



		public function yith_wcgc_template_formatted_price( $formatted_price, $object, $context ) {

			$currency_detector = new \WOOMC\Currency\Detector();

			$conversion_api = new \WOOMC\API();

			$from = $currency_detector->getDefaultCurrency();
			$to = get_post_meta( $object->order_id, '_order_currency', true );

			$formatted_price = $conversion_api->convert( $object->total_amount, $to, $from );

			return wc_price( $formatted_price, array( 'currency' => $to ) ) ;

		}

		public function yith_ywgc_get_gift_card_price_call_back( $amount ) {

			$currency_detector = new \WOOMC\Currency\Detector();

			$conversion_api = new \WOOMC\API();

			$to   = $currency_detector->currency();
			$from = $currency_detector->getDefaultCurrency();

			$amount = $conversion_api->convert( $amount, $to, $from );

			return $amount ;

		}


	}
}

YWGC_WC_Multi_Currency_module::get_instance();
