<?php
/**
* WCFM plugin view
*
* WCFM Marketplace Settings View
*
* @author WC Lovers
* @package wcfm/view
* @version 1.0.0
*/

global $WCFM, $WCFMu, $WCFMmp;

$wcfm_is_allow_manage_facebook_marketplace = apply_filters( 'wcfm_is_allow_manage_facebook_marketplace', true );

if( !$wcfm_is_allow_manage_facebook_marketplace ) {
	wcfm_restriction_message_show( "Facebook for Marketplace" );
	return;
}

$user_id = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );

$vendor_data = get_user_meta( $user_id, 'wcfm_facebook_marketplace_settings', true );

$product_sync = isset( $vendor_data['product_sync'] ) ? $vendor_data['product_sync'] : array();

$is_connected = $WCFMu->wcfmu_facebook_marketplace->get_connection_handler( $user_id )->is_connected();

if ( $is_connected ) {
	$title = __( 'Reach the Right People and Sell More Online', 'wc-frontend-manager-ultimate' );
} else {
	$title = __( 'Grow your business on Facebook', 'wc-frontend-manager-ultimate' );
}

$subtitle = __( 'Use this WooCommerce, WCFM and Facebook integration to:', 'wc-frontend-manager-ultimate' );

$benefits = [
	__( 'Create an ad in a few steps', 'wc-frontend-manager-ultimate'),
	__( 'Use built-in best practices for online sales', 'wc-frontend-manager-ultimate'),
	__( 'Get reporting on sales and revenue', 'wc-frontend-manager-ultimate'),
];

if ( $is_connected ) {

	$actions = [
		'create-ad' => [
			'label' => __( 'Create Ad', 'wc-frontend-manager-ultimate' ),
			'type'  => 'primary',
			'url'   => 'https://www.facebook.com/ad_center/create/ad/?entry_point=facebook_ads_extension&page_id=' . $WCFMu->wcfmu_facebook_marketplace->get_integration( $user_id )->get_facebook_page_id(),
		],
		'manage' => [
			'label' => __( 'Manage Connection', 'wc-frontend-manager-ultimate' ),
			'type'  => 'secondary',
			'url'   => $WCFMu->wcfmu_facebook_marketplace->get_connection_handler( $user_id )->get_manage_url(),
		],
	];

} else {

	$actions = [
		'get-started' => [
			'label' => __( 'Get Started', 'wc-frontend-manager-ultimate' ),
			'type'  => 'primary',
			'url'   => $WCFMu->wcfmu_facebook_marketplace->get_connection_handler( $user_id )->get_connect_url(),
		],
	];
}

$term_query = new \WP_Term_Query( apply_filters( 'wcfm_facebook_marketplace_product_categories_args', [
	'taxonomy'   => 'product_cat',
	'hide_empty' => false,
	'fields'     => 'id=>name',
], $user_id ) );

$product_categories = $term_query->get_terms();

$term_query = new \WP_Term_Query( apply_filters( 'wcfm_facebook_marketplace_product_tags_args', [
	'taxonomy'     => 'product_tag',
	'hide_empty'   => false,
	'hierarchical' => false,
	'fields'       => 'id=>name',
], $user_id ) );

$product_tags = $term_query->get_terms();
?>

<div class="collapse wcfm-collapse" id="">
	<div class="wcfm-page-headig">
		<span class="wcfmfa fab fa-facebook"></span>
		<span class="wcfm-page-heading-text"><?php _e( 'Facebook for Matketplace', 'wc-frontend-manager-ultimate' ); ?></span>
		<?php do_action( 'wcfm_page_heading' ); ?>
	</div>
	<div class="wcfm-collapse-content">
		<div id="wcfm_page_load"></div>

		<div class="wcfm-container wcfm-top-element-container">
			<?php do_action( 'wcfm_vendor_facebook_marketplace_setting_header_before', $user_id ); ?>
			<h2><?php _e('Facebook for Matketplace Settings', 'wc-frontend-manager-ultimate' ); ?></h2>
			<?php do_action( 'wcfm_vendor_facebook_marketplace_setting_header_after', $user_id ); ?>
			<div class="wcfm-clearfix"></div>
		</div>
		<div class="wcfm-clearfix"></div><br />

		<?php do_action( 'before_wcfm_facebook_marketplace_settings', $user_id ); ?>

		<form id="wcfm_facebook_marketplace_settings_form" class="wcfm">

			<?php do_action( 'begin_wcfm_facebook_marketplace_settings_form', $user_id ); ?>

			<div class="wcfm-tabWrap">

				<?php do_action( 'begin_wcfm_facebook_marketplace_settings', $user_id ); ?>

				<!-- collapsible -->
				<div class="page_collapsible" id="wcfm_facebook_marketplace_settings_connection_head">
					<label class="wcfmfa fa-plug"></label>
					<?php _e('Connection', 'wc-frontend-manager-ultimate'); ?><span></span>
				</div>
				<div class="wcfm-container wcfm_facebook_marketplace_settings_connection">
					<div class="wcfm-facebook-connection-box">

						<div class="logo"></div>

						<h1><?php echo esc_html( $title ); ?></h1>
						<h2><?php echo esc_html( $subtitle ); ?></h2>

						<ul class="benefits">
							<?php foreach ( $benefits as $key => $benefit ) : ?>
								<li class="benefit benefit-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $benefit ); ?></li>
							<?php endforeach; ?>
						</ul>

						<div class="actions">

							<?php foreach ( $actions as $action_id => $action ) : ?>

								<a
									href="<?php echo esc_url( $action['url'] ); ?>"
									class="button button-<?php echo esc_attr( $action['type'] ); ?>"
									<?php echo ( 'get-started' !== $action_id ) ? 'target="_blank"' : ''; ?>
								>
									<?php echo esc_html( $action['label'] ); ?>
								</a>

							<?php endforeach; ?>

							<?php if ( $is_connected ) : ?>

								<a href="<?php echo esc_url( $WCFMu->wcfmu_facebook_marketplace->get_connection_handler( $user_id )->get_disconnect_url() ); ?>" class="uninstall">
									<?php esc_html_e( 'Uninstall', 'wc-frontend-manager-ultimate' ); ?>
								</a>

							<?php endif; ?>

						</div>

					</div>

					<?php
					// don't proceed further if not connected
					if ( $is_connected ) {
						/**
						 * Build the basic static elements.
						 *
						 * At a minimum, we display their raw ID. If they have an API resource, we replace that ID with whatever data
						 * we can get our hands on, with an external link if possible. Current abilities:
						 *
						 * + Page: just the ID
						 * + Pixel: just the ID
						 * + Catalog: name, full URL
						 * + Business manager: name, full URL
						 * + Ad account: not currently available
						 *
						 * TODO: add pixel & ad account API retrieval when we gain the ads_management permission
						 * TODO: add the page name and link when we gain the manage_pages permission
						 */
						$static_items = [
							'page' => [
								'label' => __( 'Page', 'wc-frontend-manager-ultimate' ),
								'value' => $WCFMu->wcfmu_facebook_marketplace->get_integration( $user_id )->get_facebook_page_id(),
							],
							'pixel' => [
								'label' => __( 'Pixel', 'wc-frontend-manager-ultimate' ),
								'value' => $WCFMu->wcfmu_facebook_marketplace->get_integration( $user_id )->get_facebook_pixel_id(),
							],
							'catalog' => [
								'label' => __( 'Catalog', 'wc-frontend-manager-ultimate' ),
								'value' => $WCFMu->wcfmu_facebook_marketplace->get_integration( $user_id )->get_product_catalog_id(),
								'url'   => 'https://facebook.com/products',
							],
							'business-manager' => [
								'label' => __( 'Business Manager account', 'wc-frontend-manager-ultimate' ),
								'value' => $WCFMu->wcfmu_facebook_marketplace->get_connection_handler( $user_id )->get_business_manager_id(),
							],
							'ad-account' => [
								'label' => __( 'Ad Manager account', 'wc-frontend-manager-ultimate' ),
								'value' => $WCFMu->wcfmu_facebook_marketplace->get_connection_handler( $user_id )->get_ad_account_id(),
							],
						];

						// if the catalog ID is set, update the URL and try to get its name for display
						if ( $catalog_id = $static_items['catalog']['value'] ) {

							$static_items['catalog']['url'] = "https://facebook.com/products/catalogs/{$catalog_id}";

							try {

								$response = $WCFMu->wcfmu_facebook_marketplace->get_api( $user_id )->get_catalog( $catalog_id );

								if ( $name = $response->get_name() ) {
									$static_items['catalog']['value'] = $name;
								}

							} catch ( SV_WC_API_Exception $exception ) {}
						}

						// if the business manager ID is set, try and get its name for display
						if ( $static_items['business-manager']['value'] ) {

							try {

								$response = $WCFMu->wcfmu_facebook_marketplace->get_api( $user_id )->get_business_manager( $static_items['business-manager']['value'] );

								if ( $name = $response->get_name() ) {
									$static_items['business-manager']['value'] = $name;
								}

								if ( $url = $response->get_url() ) {
									$static_items['business-manager']['url'] = $url;
								}

							} catch ( SV_WC_API_Exception $exception ) {}
						}

						?>

						<table class="form-table">
							<tbody>

								<?php foreach ( $static_items as $id => $item ) :

									$item = wp_parse_args( $item, [
										'label' => '',
										'value' => '',
										'url'   => '',
									] );

									?>

									<tr valign="top" class="wc-facebook-connected-<?php echo esc_attr( $id ); ?>">

										<th scope="row" class="titledesc">
											<?php echo esc_html( $item['label'] ); ?>
										</th>

										<td class="forminp">

											<?php if ( $item['url'] ) : ?>

												<a href="<?php echo esc_url( $item['url'] ); ?>" target="_blank">

													<?php echo esc_html( $item['value'] ); ?>

													<span
														class="dashicons dashicons-external"
														style="margin-right: 8px; vertical-align: bottom; text-decoration: none;"
													></span>

												</a>

											<?php elseif ( is_numeric( $item['value'] ) ) : ?>

												<code><?php echo esc_html( $item['value'] ); ?></code>

											<?php elseif ( ! empty( $item['value'] ) ) : ?>

												<?php echo esc_html( $item['value'] ); ?>

											<?php else : ?>

												<?php echo '-' ?>

											<?php endif; ?>

										</td>
									</tr>

								<?php endforeach; ?>

							</tbody>
						</table>

						<?php
					}
					?>

				</div>
				<!-- collapsible end -->

				<!-- collapsible -->
				<?php if( apply_filters( 'wcfm_is_allow_facebook_marketplace_product_sync', true ) ) { ?>
					<div class="page_collapsible" id="wcfm_facebook_marketplace_settings_product_sync_head">
						<label class="wcfmfa fa-sync"></label>
						<?php _e('Product Sync', 'wc-frontend-manager-ultimate'); ?><span></span>
					</div>
					<div class="wcfm-container wcfm_facebook_marketplace_settings_product_sync">
						<div class="wcfm-content wcfm_facebook_marketplace_settings_product_sync_expander">

							<div class="wcfm_clearfix"></div>
							<div class="wcfm_facebook_marketplace_settings_product_sync_heading">
								<h2>
									<?php _e( 'Product Sync', 'wc-frontend-manager-ultimate' ); ?>
								</h2>
								<?php if ( $is_connected ) : ?>
									<a
										id="wcfm_facebook_marketplace_product_sync"
										class="button product-sync-field"
										href="#"
										style="vertical-align: middle; margin-left: 20px;"
									><?php esc_html_e( 'Sync products', 'wc-frontend-manager-ultimate' ); ?></a>
								<?php endif; ?>
								<div><p id="sync_progress" style="display: none;"></p></div>
							</div>
							<div class="wcfm_clearfix"></div>
							<div class="product_sync product_sync_wrap">
								<?php
								$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_facebook_marketplace_settings_fields_product_sync', array(
									'enable' => array(
										'label' 		=> __('Enable product sync', 'wc-frontend-manager-ultimate'),
										'name' 			=> 'product_sync[enable]',
										'type' 			=> 'checkbox',
										'class' 		=> 'wcfm-checkbox product-sync-field',
										'label_class' 	=> 'wcfm_title',
										'value' 		=> 'yes',
										'dfvalue' 		=> isset( $product_sync['enable'] ) ? $product_sync['enable'] : 'no',
									),
									'excluded_product_category_ids' => array(
				                        'label'       		=> __('Exclude categories from sync', 'wc-frontend-manager-ultimate') ,
				                        'name'        		=> 'product_sync[excluded_product_category_ids]',
				                        'type'        		=> 'select',
				                        'class'       		=> 'wcfm-select wcfm-select2 product-sync-field',
										'label_class' 		=> 'wcfm_title',
										'options'     		=> $product_categories,
				                        'value'       		=> isset( $product_sync['excluded_product_category_ids'] ) ? $product_sync['excluded_product_category_ids'] : array(),
				                        'attributes'  		=> array(
				                            'multiple'  	=> 'multiple',
				                        ),
										'custom_attributes' => array(
				                            'placeholder'  	=> __( 'Search for a product category&hellip;', 'wc-frontend-manager-ultimate' ),
				                        ),
				                        'hints'       		=> __( 'Products in one or more of these categories will not sync to Facebook.', 'wc-frontend-manager-ultimate' ),
				                    ),
									'excluded_product_tag_ids' => array(
				                        'label'       		=> __('Exclude tags from sync', 'wc-frontend-manager-ultimate') ,
				                        'name'        		=> 'product_sync[excluded_product_tag_ids]',
				                        'type'        		=> 'select',
										'class'       		=> 'wcfm-select wcfm-select2 product-sync-field',
										'label_class' 		=> 'wcfm_title',
				                        'options'     		=> $product_tags,
				                        'value'       		=> isset( $product_sync['excluded_product_tag_ids'] ) ? $product_sync['excluded_product_tag_ids'] : array(),
				                        'attributes'  		=> array(
				                            'multiple'  	=> 'multiple',
				                        ),
										'custom_attributes' => array(
				                            'placeholder'  	=> __( 'Search for a product tag&hellip;', 'wc-frontend-manager-ultimate' ),
				                        ),
				                        'hints'       		=> __( 'Products with one or more of these tags will not sync to Facebook.', 'wc-frontend-manager-ultimate' ),
				                    ),
									'product_description_mode' => array(
										'label' 		=> __('Product description sync', 'wc-frontend-manager-ultimate'),
										'name' 			=> 'product_sync[product_description_mode]',
										'type' 			=> 'select',
										'class' 		=> 'wcfm-select wcfm-select2 product-sync-field',
										'label_class' 	=> 'wcfm_title',
										'options' 		=> array(
											'standard' 	=> __( 'Standard description', 'wc-frontend-manager-ultimate' ),
											'short'    	=> __( 'Short description', 'wc-frontend-manager-ultimate' ),
										),
										'value' 		=> isset( $product_sync['product_description_mode'] ) ? $product_sync['product_description_mode'] : 'standard',
										'hints'			=> __( 'Choose which product description to display in the Facebook catalog.', 'wc-frontend-manager-ultimate' ),
									),
								), $user_id ) );
								?>
							</div>

						</div>
					</div>
					<div class="wcfm_clearfix"></div>
				<?php } ?>
				<!-- end collapsible -->

				<?php do_action( 'end_wcfm_facebook_marketplace_settings', $user_id ); ?>

			</div>

			<?php do_action( 'after_wcfm_facebook_marketplace_settings_form', $user_id ); ?>

			<div id="wcfm_facebook_marketplace_settings_submit" class="wcfm_form_simple_submit_wrapper">
				<div class="wcfm-message" tabindex="-1"></div>

				<input type="submit" name="save-data" value="<?php _e( 'Save', 'wc-frontend-manager-ultimate' ); ?>" id="wcfm_facebook_marketplace_settings_save_button" class="wcfm_submit_button" />
			</div>
			<input type="hidden" name="wcfm_nonce" value="<?php echo wp_create_nonce( 'wcfm_facebook_marketplace_settings' ); ?>" />
		</form>

		<?php do_action( 'after_wcfm_facebook_marketplace_settings', $user_id ); ?>

	</div>
</div>
