<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit( 'Direct access forbidden.' );
}

add_filter( 'yith_wcmv_do_404_redirect', '__return_false' );
add_action( 'yith_wcmv_404_redirect', 'yith_wcmv_404_redirect', 10, 1 );

if( ! function_exists( 'yith_wcmv_404_redirect' ) ){
	function yith_wcmv_404_redirect( $vendor ){
		if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'single' ) ) {
			get_header();
			get_template_part( 'template-parts/404' );
			get_footer();
			exit;
		}
	}
}

