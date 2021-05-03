<?php
if( !defined('ABSPATH')){
	exit;
}

if( !class_exists('YITH_Vendor_Endpoints')){

	class YITH_Vendor_Endpoints{

		public function __construct() {

			/* Endpoint management */

			$revision_management = get_option( 'yith_wpv_manage_terms_and_privacy_revision', 'no' );
			$privacy_required    = get_option( 'yith_wpv_vendors_registration_required_privacy_policy', 'no' );
			$terms_required      = get_option( 'yith_wpv_vendors_registration_required_terms_and_conditions', 'no' );
			if ( 'yes' == $revision_management && ( 'yes' == $privacy_required || 'yes' == $terms_required ) ) {
				add_filter( 'query_vars', array( $this, 'add_vendor_query_vars' ), 0, 1 );
				add_filter( 'woocommerce_get_query_vars', array( $this, 'add_vendor_query_vars' ), 20, 1 );
				add_filter( 'woocommerce_account_menu_items', array( $this, 'add_vendor_menu_items' ), 20, 1 );
				add_action( 'woocommerce_account_terms-of-service_endpoint', array(
					$this,
					'show_term_of_service_content'
				) );
				add_filter( 'woocommerce_endpoint_terms-of-service_title', array(
					$this,
					'show_term_of_service_endpoint_title'
				) );
			}
		}

		public function add_vendor_query_vars( $query_vars ) {

			if ( 'query_vars' == current_filter() ) {
				$query_vars[] = 'terms-of-service';
			} else {
				$query_vars['terms-of-service'] = 'terms-of-service';

			}


			return $query_vars;
		}

		/**
		 * @param $menu_items
		 */
		public function add_vendor_menu_items( $menu_items ) {

			$vendor = yith_get_vendor( 'current', 'user' );

			if ( $vendor->is_valid() ) {
				if ( isset( $menu_items['customer-logout'] ) ) {
					$logout = $menu_items['customer-logout'];
					unset( $menu_items['customer-logout'] );
				}
				$menu_items['terms-of-service'] = __( 'Terms of Service', 'yith-woocommerce-product-vendors' );

				if ( isset( $logout ) ) {
					$menu_items['customer-logout'] = $logout;
				}
			}

			return $menu_items;
		}

		/**
		 * show the content
		 * @author YITH
		 */
		public function show_term_of_service_content() {
			$vendor = yith_get_vendor( 'current', 'user' );

			if ( $vendor->is_valid() ) {

				wc_get_template( 'terms-of-service.php', array( 'vendor' => $vendor ), '', YITH_WPV_TEMPLATE_PATH . '/woocommerce/myaccount/' );
			}
		}

		/**
		 * show the endpoint title
		 * @author YITH
		 */
		public function show_term_of_service_endpoint_title() {

			$vendor = yith_get_vendor( 'current', 'user' );

			if ( $vendor->is_valid() ) {
				return  esc_html__( 'Terms of Service', 'yith-woocommerce-product-vendors' );
			}
		}
	}
}

function YITH_Vendor_Endpoints() {
	return new YITH_Vendor_Endpoints();
}

YITH_Vendor_Endpoints();
