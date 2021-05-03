<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! isset( $vendor ) ) {
	$vendor = yith_get_vendor( 'current', 'user' );
}

$privacy_page_id      = get_option( 'yith_wpv_privacy_page', 0 );
$terms_page_id        = get_option( 'yith_wpv_terms_and_conditions_page_id', 0 );
$terms_page           = get_post( $terms_page_id );
$privacy_page         = get_post( $privacy_page_id );
$terms_page_content   = apply_filters( 'the_content', $terms_page->post_content );
$privacy_page_content = apply_filters( 'the_content', $privacy_page->post_content );

$terms_req        = YITH_Vendors()->is_terms_and_conditions_require();
$terms_accepted   = $vendor->has_terms_and_conditions_accepted();
$privacy_req      = YITH_Vendors()->is_privacy_policy_require();
$privacy_accepted = $vendor->has_privacy_policy_accepted();
?>

<div id="yith_multi_vendor_terms_of_service_container">
	<form class="yith_mv_save_terms" method="post">
		<div id="yith_mv_terms_content">
			<div class="yith_mv_title">
				<h3><?php echo $terms_page->post_title; ?></h3>
			</div>
			<div class="yith_mv_content">
				<?php echo $terms_page_content; ?>
			</div>
			<div class="yith_mv_acceptance">
				<?php
				if ( $terms_req ) {
					if ( $terms_accepted ) {
						$info = _x( 'Accepted terms and conditions policy,  published on', 'Accepted terms and conditions policy,  published on 2020-04-22', 'yith-woocommerce-product-vendors' );
						$info = sprintf( '%s <span class="yith_mv_accept_data">%s</span>', $info, wc_format_datetime( new WC_DateTime( $vendor->data_terms_and_condition ) ) );
						?>
						<span class="yith_mv_accept_info">
						<?php echo $info; ?>
					</span>
						<?php
					} else { ?>
						<p class="form-field form-row-wide">
							<label
								for="yith_mv_accept_terms"><?php esc_html_e( 'I read and accept the terms and conditions', 'yith-woocommerce-product-vendors' ); ?></label>
							<input type="checkbox" required name="yith_mv_accept_terms" id="yith_mv_accept_terms">
						</p>
						<?php
					}
				}
				?>
			</div>
		</div>
		<div id="yith_mv_privacy_content">
			<div class="yith_mv_title">
				<h3><?php echo $privacy_page->post_title; ?></h3>
			</div>
			<div class="yith_mv_content">
				<?php echo $privacy_page_content; ?>
			</div>
			<div class="yith_mv_acceptance">
				<?php
				if ( $privacy_req ) {
					if ( $privacy_accepted ):
						$info = _x( 'Accepted privacy policy, published on', 'Accepted privacy policy, published on 2020-04-22',
							'yit	h-woocommerce-product-vendors' );
						$info = sprintf( '%s <span class="yith_mv_accept_data">%s</span>', $info, wc_format_datetime( new
						WC_DateTime( $vendor->data_privacy_policy ) ) );
						?>
						<span class="yith_mv_accept_info">
						<?php echo $info; ?>
					</span>
					<?php
					else :?>
						<p class="form-field form-row-wide">
							<label
								for="yith_mv_accept_privacy"><?php esc_html_e( 'I read and accept the vendor privacy policy', 'yith-woocommerce-product-vendors' ); ?></label>
							<input type="checkbox" required name="yith_mv_accept_privacy" id="yith_mv_accept_privacy">
						</p>
					<?php endif;
				} ?>
			</div>
		</div>
		<?php echo wp_nonce_field( 'yith-mv-accept-terms-and-privacy', 'yith_mv_accept_temrs_and_privacy_nonce' ); ?>
		<input type="hidden" name="yith_vendor_id" value="<?php esc_attr_e( $vendor->id ); ?>">
		<?php if ( ($terms_req && !$terms_accepted) ||( $privacy_req && !$privacy_accepted )): ?>
			<button type="submit"
					id="yith_mv_save_terms_privacy"><?php esc_html_e( 'Save', 'yith-woocommerce-product-vendors' ); ?></button>
		<?php endif; ?>
	</form>
</div>
