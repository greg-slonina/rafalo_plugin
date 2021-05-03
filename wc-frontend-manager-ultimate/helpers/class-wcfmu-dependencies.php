<?php
/**
 * WC Dependency Checker
 *
 */
class WCFMu_Dependencies {

	private static $active_plugins;

	static function init() {
		self::$active_plugins = (array) get_option( 'active_plugins', array() );
		if ( is_multisite() )
			self::$active_plugins = array_merge( self::$active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
	}

	// WooCommerce
	static function woocommerce_plugin_active_check() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'woocommerce/woocommerce.php', self::$active_plugins ) || array_key_exists( 'woocommerce/woocommerce.php', self::$active_plugins );
		return false;
	}

	// WC Frontend Manager
	static function wcfm_plugin_active_check() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'wc-frontend-manager/wc_frontend_manager.php', self::$active_plugins ) || array_key_exists( 'wc-frontend-manager/wc_frontend_manager.php', self::$active_plugins );
		return false;
	}

	// WP Resume Manager Support
	static function wcfm_resume_manager_active_check() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'wp-job-manager-resumes/wp-job-manager-resumes.php', self::$active_plugins ) || array_key_exists( 'wp-job-manager-resumes/wp-job-manager-resumes.php', self::$active_plugins );
		return false;
	}

	// YITH Auction Premium Support
	static function wcfm_yith_auction_active_check() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'yith-woocommerce-auctions-premium/init.php', self::$active_plugins ) || array_key_exists( 'yith-woocommerce-auctions-premium/init.php', self::$active_plugins );
		return false;
	}

	// WooCommerce Simple Auction Support
	static function wcfm_wcs_auction_active_check() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'woocommerce-simple-auctions/woocommerce-simple-auctions.php', self::$active_plugins ) || array_key_exists( 'woocommerce-simple-auctions/woocommerce-simple-auctions.php', self::$active_plugins );
		return false;
	}

	// WC Rental & Booking Pro Support
	static function wcfm_wc_rental_pro_active_check() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'woocommerce-rental-and-booking/redq-rental-and-bookings.php', self::$active_plugins ) || array_key_exists( 'woocommerce-rental-and-booking/redq-rental-and-bookings.php', self::$active_plugins );
		return false;
	}

	// WC Appointments Support
	static function wcfm_wc_appointments_active_check() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'woocommerce-appointments/woocommerce-appointments.php', self::$active_plugins ) || array_key_exists( 'woocommerce-appointments/woocommerce-appointments.php', self::$active_plugins );
		return false;
	}

	// WC Product Addons Support
	static function wcfm_wc_addons_active_check() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'woocommerce-product-addons/woocommerce-product-addons.php', self::$active_plugins ) || array_key_exists( 'woocommerce-product-addons/woocommerce-product-addons.php', self::$active_plugins );
		return false;
	}

	// WC Bookings Accommodation Support
	static function wcfm_wc_accommodation_active_check() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'woocommerce-accommodation-bookings/woocommerce-accommodation-bookings.php', self::$active_plugins ) || array_key_exists( 'woocommerce-accommodation-bookings/woocommerce-accommodation-bookings.php', self::$active_plugins );
		return false;
	}

	// WC Per Product Shipping Support - 2.5.0
	static function wcfm_wc_per_peroduct_shipping_active_check() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'woocommerce-shipping-per-product/woocommerce-shipping-per-product.php', self::$active_plugins ) || array_key_exists( 'woocommerce-shipping-per-product/woocommerce-shipping-per-product.php', self::$active_plugins );
		return false;
	}

	// Toolset Types Support - 2.5.0
	static function wcfm_toolset_types_active_check() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'types/wpcf.php', self::$active_plugins ) || array_key_exists( 'types/wpcf.php', self::$active_plugins );
		return false;
	}

	// MapPress - 2.6.2
	static function wcfm_mappress_active_check() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'mappress-google-maps-for-wordpress/mappress.php', self::$active_plugins ) || array_key_exists( 'mappress-google-maps-for-wordpress/mappress.php', self::$active_plugins );
		return false;
	}

	// WooCommerce Additional Variation Images - 3.0.2
	static function wcfm_wc_variation_gallery_active_check() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'woocommerce-additional-variation-images/woocommerce-additional-variation-images.php', self::$active_plugins ) || array_key_exists( 'woocommerce-additional-variation-images/woocommerce-additional-variation-images.php', self::$active_plugins );
		return false;
	}

	// Advanced Custom Fields(ACF) - 3.0.4
	static function wcfm_acf_active_check() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'advanced-custom-fields/acf.php', self::$active_plugins ) || array_key_exists( 'advanced-custom-fields/acf.php', self::$active_plugins );
		return false;
	}

	// Address Geocoder - 3.1.1
	static function wcfm_address_geocoder_active_check() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'address-geocoder/address-geocoder.php', self::$active_plugins ) || array_key_exists( 'address-geocoder/address-geocoder.php', self::$active_plugins );
		return false;
	}

	// Sitepress WPML - 3.2.0
	static function wcfm_sitepress_wpml_active_check() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'sitepress-multilingual-cms/sitepress.php', self::$active_plugins ) || array_key_exists( 'sitepress-multilingual-cms/sitepress.php', self::$active_plugins );
		return false;
	}

	// Toolset Maps Support - 3.2.4
	static function wcfm_toolset_address_map_active_check() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'toolset-maps/toolset-maps-loader.php', self::$active_plugins ) || array_key_exists( 'toolset-maps/toolset-maps-loader.php', self::$active_plugins );
		return false;
	}

	// WooCommerce Box Office Support - 3.3.3
	static function wcfm_wc_box_office_active_check() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'woocommerce-box-office/woocommerce-box-office.php', self::$active_plugins ) || array_key_exists( 'woocommerce-box-office/woocommerce-box-office.php', self::$active_plugins );
		return false;
	}

	// Advanced Custom Fields(ACF) Pro - 3.3.7
	static function wcfm_acf_pro_active_check() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'advanced-custom-fields-pro/acf.php', self::$active_plugins ) || array_key_exists( 'advanced-custom-fields-pro/acf.php', self::$active_plugins );
		return false;
	}

	// WooCommerce Lottery - 3.5.0
	static function wcfm_wc_lottery_active_check() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'woocommerce-lottery/wc-lottery.php', self::$active_plugins ) || array_key_exists( 'woocommerce-lottery/wc-lottery.php', self::$active_plugins );
		return false;
	}

	// WooCommerce Deposit - 3.5.9
	static function wcfm_wc_deposits_active_check() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'woocommerce-deposits/woocommmerce-deposits.php', self::$active_plugins ) || array_key_exists( 'woocommerce-deposits/woocommmerce-deposits.php', self::$active_plugins ) || in_array( 'woocommerce-deposits/woocommerce-deposits.php', self::$active_plugins ) || array_key_exists( 'woocommerce-deposits/woocommerce-deposits.php', self::$active_plugins );
		return false;
	}

	// WooCommerce Deposit - 4.0.0
	static function wcfm_wc_pdf_voucher_active_check() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'woocommerce-pdf-vouchers/woocommerce-pdf-vouchers.php', self::$active_plugins ) || array_key_exists( 'woocommerce-pdf-vouchers/woocommerce-pdf-vouchers.php', self::$active_plugins );
		return false;
	}

	// WooCommerce Custom Product Tabs Manager Support - 4.1.0
	static function wcfm_wc_tabs_manager_plugin_active_check() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'woocommerce-tab-manager/woocommerce-tab-manager.php', self::$active_plugins ) || array_key_exists( 'woocommerce-tab-manager/woocommerce-tab-manager.php', self::$active_plugins ) || class_exists( 'WC_Tab_Manager' );
		return false;
	}

	// WooCommerce Warranty & Request Support - 4.1.5
	static function wcfm_wc_warranty_plugin_active_check() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'woocommerce-warranty/woocommerce-warranty.php', self::$active_plugins ) || array_key_exists( 'woocommerce-warranty/woocommerce-warranty.php', self::$active_plugins ) || class_exists( 'WooCommerce_Warranty' );
		return false;
	}

	// WooCommerce Waitlist Support - 4.1.5
	static function wcfm_wc_waitlist_plugin_active_check() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'woocommerce-waitlist/woocommerce-waitlist.php', self::$active_plugins ) || array_key_exists( 'woocommerce-waitlist/woocommerce-waitlist.php', self::$active_plugins ) || class_exists( 'WooCommerce_Waitlist_Plugin' );
		return false;
	}

	// WooCommerce Foo Events - 5.4.0
	static function wcfm_wc_fooevents() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'fooevents/fooevents.php', self::$active_plugins ) || array_key_exists( 'fooevents/fooevents.php', self::$active_plugins ) || class_exists( 'FooEvents' );
		return false;
	}

	// WooCommerce Foo Events Calendar - 5.4.0
	static function wcfm_wc_fooevents_calendar() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'fooevents-calendar/fooevents-calendar.php', self::$active_plugins ) || array_key_exists( 'fooevents-calendar/fooevents-calendar.php', self::$active_plugins ) || class_exists( 'FooEvents_Calendar' );
		return false;
	}

	// WooCommerce Foo Events Multi Day - 5.4.0
	static function wcfm_wc_fooevents_multiday() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'fooevents_multi_day/fooevents-multi-day.php', self::$active_plugins ) || array_key_exists( 'fooevents_multi_day/fooevents-multi-day.php', self::$active_plugins ) || class_exists( 'Fooevents_Multiday_Events' );
		return false;
	}

	// WooCommerce Foo Events Custom Attendee Fields - 5.4.0
	static function wcfm_wc_fooevents_custom_atendee() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'fooevents_custom_attendee_fields/fooevents-custom-attendee-fields.php', self::$active_plugins ) || array_key_exists( 'fooevents_custom_attendee_fields/fooevents-custom-attendee-fields.php', self::$active_plugins ) || class_exists( 'Fooevents_Custom_Attendee_Fields' );
		return false;
	}

	// WooCommerce Foo Events Seating - 5.4.0
	static function wcfm_wc_fooevents_seating() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'fooevents_seating/fooevents-seating.php', self::$active_plugins ) || array_key_exists( 'fooevents_seating/fooevents-seating.php', self::$active_plugins ) || class_exists( 'Fooevents_Seating' );
		return false;
	}

	// WooCommerce Foo Events PDF Ticket - 5.4.0
	static function wcfm_wc_fooevents_pdfticket() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'fooevents_pdf_tickets/fooevents-pdf-tickets.php', self::$active_plugins ) || array_key_exists( 'fooevents_pdf_tickets/fooevents-pdf-tickets.php', self::$active_plugins ) || class_exists( 'FooEvents_PDF_Tickets' );
		return false;
	}

	// WooCommerce Measurement Price Calculator - 5.4.1
	static function wcfm_wc_measurement_price_calculator() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'woocommerce-measurement-price-calculator/woocommerce-measurement-price-calculator.php', self::$active_plugins ) || array_key_exists( 'woocommerce-measurement-price-calculator/woocommerce-measurement-price-calculator.php', self::$active_plugins );
		return false;
	}

	// WooCommerce Fancy Products  - 6.0.0
	static function wcfm_wc_fancy_product_designer_active_check() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'fancy-product-designer/fancy-product-designer.php', self::$active_plugins ) || array_key_exists( 'fancy-product-designer/fancy-product-designer.php', self::$active_plugins );
		return false;
	}

	// WooCommerce Advanced Product Labels - 6.0.0
	static function wcfm_wc_advanced_product_labels_active_check() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'woocommerce-advanced-product-labels/woocommerce-advanced-product-labels.php', self::$active_plugins ) || array_key_exists( 'woocommerce-advanced-product-labels/woocommerce-advanced-product-labels.php', self::$active_plugins );
		return false;
	}

	// Tych Bookings  - 6.0.1
	static function wcfm_tych_booking_active_check() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'woocommerce-booking/woocommerce-booking.php', self::$active_plugins ) || array_key_exists( 'woocommerce-booking/woocommerce-booking.php', self::$active_plugins );
		return false;
	}

	// WC Wholesale  - 6.0.3
	static function wcfm_wholesale_active_check() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'woocommerce-wholesale-prices/woocommerce-wholesale-prices.bootstrap.php', self::$active_plugins ) || array_key_exists( 'woocommerce-wholesale-prices/woocommerce-wholesale-prices.bootstrap.php', self::$active_plugins );
		return false;
	}

	// WC Wholesale Premium  - 6.0.3
	static function wcfm_wholesale_premium_active_check() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'woocommerce-wholesale-prices-premium/woocommerce-wholesale-prices-premium.bootstrap.php', self::$active_plugins ) || array_key_exists( 'woocommerce-wholesale-prices-premium/woocommerce-wholesale-prices-premium.bootstrap.php', self::$active_plugins );
		return false;
	}

	// WooCommerce Product Badge Manager - 6.0.5
	static function wcfm_wc_product_badge_manager_active_check() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'woocommerce-product-badge-manager/woocommerce-product-badge-manager.php', self::$active_plugins ) || array_key_exists( 'woocommerce-product-badge-manager/woocommerce-product-badge-manager.php', self::$active_plugins );
		return false;
	}

	// WooCommerce Min/Max Quantities  - 6.0.5
	static function wcfm_wc_min_max_quantities_active_check() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'woocommerce-min-max-quantities/woocommerce-min-max-quantities.php', self::$active_plugins ) || array_key_exists( 'woocommerce-min-max-quantities/woocommerce-min-max-quantities.php', self::$active_plugins );
		return false;
	}

	// WooCommerce 360 images - 6.0.5
	static function wcfm_wc_360_images_active_check() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'woocommerce-360-image/woocommerce-360-image.php', self::$active_plugins ) || array_key_exists( 'woocommerce-360-image/woocommerce-360-image.php', self::$active_plugins );
		return false;
	}

	// WooCommerce Variation Swatch - 6.2.7
	static function wcfm_wc_variaton_swatch_active_check() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'woo-variation-swatches/woo-variation-swatches.php', self::$active_plugins ) || array_key_exists( 'woo-variation-swatches/woo-variation-swatches.php', self::$active_plugins );
		return false;
	}

	// WooCommerce Variation Swatch Pro - 6.2.7
	static function wcfm_wc_variaton_swatch_pro_active_check() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'woo-variation-swatches-pro/woo-variation-swatches-pro.php', self::$active_plugins ) || array_key_exists( 'woo-variation-swatches-pro/woo-variation-swatches-pro.php', self::$active_plugins );
		return false;
	}

	// WooCommerce Quotation - 6.2.7
	static function wcfm_wc_quotation_active_check() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'woocommerce-quotation/woocommerce-quotation.php', self::$active_plugins ) || array_key_exists( 'woocommerce-quotation/woocommerce-quotation.php', self::$active_plugins );
		return false;
	}

	// WooCommerce Dynamic Pricing - 6.2.9
	static function wcfm_wc_dynamic_pricing_active_check() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'woocommerce-dynamic-pricing/woocommerce-dynamic-pricing.php', self::$active_plugins ) || array_key_exists( 'woocommerce-dynamic-pricing/woocommerce-dynamic-pricing.php', self::$active_plugins );
		return false;
	}

	// MSRP for WooCommerce (Algotitmika) - 6.2.9
	static function wcfm_msrp_for_wc_plugin_active_check() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'msrp-for-woocommerce/msrp-for-woocommerce.php', self::$active_plugins ) || array_key_exists( 'msrp-for-woocommerce/msrp-for-woocommerce.php', self::$active_plugins );
		return false;
	}

	// Cost of Goods for WooCommerce (Algotitmika) - 6.2.9
	static function wcfm_wc_cost_of_goods_plugin_active_check() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'cost-of-goods-for-woocommerce/cost-of-goods-for-woocommerce.php', self::$active_plugins ) || array_key_exists( 'cost-of-goods-for-woocommerce/cost-of-goods-for-woocommerce.php', self::$active_plugins );
		return false;
	}

	// License Manager for WooCommerce - 6.4.0
	static function wcfm_wc_license_manager_plugin_active_check() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'license-manager-for-woocommerce/license-manager-for-woocommerce.php', self::$active_plugins ) || array_key_exists( 'license-manager-for-woocommerce/license-manager-for-woocommerce.php', self::$active_plugins );
		return false;
	}

	// ELEX WooCommerce Role-based Pricing Plugin & WooCommerce Catalog Mode - 6.4.0
	static function wcfm_elex_rolebased_price_plugin_active_check() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'elex-catmode-rolebased-price/elex-catmode-rolebased-price.php', self::$active_plugins ) || array_key_exists( 'elex-catmode-rolebased-price/elex-catmode-rolebased-price.php', self::$active_plugins );
		return false;
	}

	// WooCommerce PW Gift Cards - 6.4.5
	static function wcfm_wc_pw_gift_cards_plugin_active_check() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'pw-woocommerce-gift-cards/pw-gift-cards.php', self::$active_plugins ) || array_key_exists( 'pw-woocommerce-gift-cards/pw-gift-cards.php', self::$active_plugins );
		return false;
	}

	// WooCommerce Smart Coupons - 6.4.5
	static function wcfm_wc_smart_coupons_plugin_active_check() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'woocommerce-smart-coupons/woocommerce-smart-coupons.php', self::$active_plugins ) || array_key_exists( 'woocommerce-smart-coupons/woocommerce-smart-coupons.php', self::$active_plugins );
		return false;
	}

	// YiTH Request a Quote Premium - 6.2.9
	static function wcfm_yith_request_quote_active_check() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'yith-woocommerce-request-a-quote-premium/init.php', self::$active_plugins ) || array_key_exists( 'yith-woocommerce-request-a-quote-premium/init.php', self::$active_plugins );
		return false;
	}

	// Facebook for WooCommerce
	static function wcfm_facebook_for_woocommerce_active_check() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'facebook-for-woocommerce/facebook-for-woocommerce.php', self::$active_plugins ) || array_key_exists( 'facebook-for-woocommerce/facebook-for-woocommerce.php', self::$active_plugins );
		return false;
	}

}
