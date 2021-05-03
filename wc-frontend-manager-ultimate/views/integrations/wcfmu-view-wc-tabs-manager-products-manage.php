<?php
/**
 * WCFM plugin view
 *
 * WCFM WC Tabs Manager Product Manage View
 *
 * @author 		WC Lovers
 * @package 	wcfmu/views/thirdparty
 * @version   4.1.0
 */
 
global $wp, $WCFM, $WCFMu, $post;

if( !apply_filters( 'wcfm_is_allow_wc_tabs_manager', true ) ) {
	return;
}

$product_id = 0;
$tabs = '';

if( isset( $wp->query_vars['wcfm-products-manage'] ) && !empty( $wp->query_vars['wcfm-products-manage'] ) ) {
	$product_id = $wp->query_vars['wcfm-products-manage'];
	$tabs = get_post_meta( $product_id, '_product_tabs', true );
}

$post->ID = $product_id;

$style = '';

wp_enqueue_script(
	'wc_tab_manager_admin',
	wc_tab_manager()->get_plugin_url() . '/assets/js/admin/wc-tab-manager-admin.min.js',
	array( 'jquery' ),
	WC_Tab_Manager::VERSION,
	true
);

wp_localize_script( 'wc_tab_manager_admin', 'wc_tab_manager_admin_params', array(
	'remove_product_tab' => __( 'Remove this product tab?', 'woocommerce-tab-manager' ),
	'remove_label'       => __( 'Remove', 'woocommerce-tab-manager' ),
	'click_to_toggle'    => __( 'Click to toggle', 'woocommerce-tab-manager' ),
	'title_label'        => __( 'Title', 'woocommerce-tab-manager' ),
	'title_description'  => __( 'The tab title, this appears in the tab', 'woocommerce-tab-manager' ),
	'content_label'      => __( 'Content', 'woocommerce-tab-manager' ),
	'ajax_url'           => admin_url( 'admin-ajax.php' ),
	'get_editor_nonce'   => wp_create_nonce( 'get-editor' ),
) );

wp_enqueue_style(
	'wc_tab_manager_admin_styles',
	wc_tab_manager()->get_plugin_url() . '/assets/css/admin/wc-tab-manager-admin.min.css',
	array(),
	WC_Tab_Manager::VERSION
);

//wp_enqueue_style( 'woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css' );

?>

<div class="page_collapsible products_manage_wc_tabs_manager simple variable external grouped booking" id="wcfm_products_manage_form_wc_tabs_manager_head"><label class="wcfmfa fa-list-alt"></label><?php _e('Tabs Manager', 'wc-frontend-manager-ultimate'); ?><span></span></div>
<div class="wcfm-container simple variable external grouped booking">
	<div id="wcfm_products_manage_form_wc_tabs_manager_expander" class="wcfm-content">
		<style type="text/css">
			#woocommerce-product-data ul.product_data_tabs li.product_tabs_tab a { <?php echo $style; ?> }
			.wc-metaboxes-wrapper .wc-metabox table.woocommerce_product_tab_data td label { display: inline; line-height: inherit; }
			.wc-metaboxes-wrapper .woocommerce_product_tabs .wc-metabox table td input { font-size: inherit; }
			#woocommerce_product_tabs .toolbar { margin-bottom: 15px; }
			#woocommerce_product_tabs .toolbar label { line-height:22px; }
			#woocommerce_product_tabs .toolbar input { margin-top:0; }
			#woocommerce_product_tabs .toolbar input { margin-top:0; }
	
			#woocommerce_product_tabs .quicktags-toolbar input {
				background-color: #EEEEEE;
				background-image: -moz-linear-gradient(center bottom , #E3E3E3, #FFFFFF);
				border: 1px solid #C3C3C3;
				border-radius: 3px 3px 3px 3px;
				color: #464646;
				display: inline-block;
				font: 12px/18px Arial,Helvetica,sans-serif normal;
				margin: 2px 1px 4px;
				width:auto;
				min-width: 26px;
				padding: 2px 4px;
				float: none;
			}
	
			#woocommerce_product_tabs .wp-editor-area {
				-moz-box-sizing: border-box;
				border: 0 none;
				font-family: Consolas,Monaco,monospace;
				line-height: 150%;
				outline: medium none;
				padding: 10px;
				resize: vertical;
				font-size:inherit;
				color:#333333;
			}
	
			#wc_tab_manager_block {
				background-color: white;
				height: 100%;
				left: 0;
				opacity: 0.6;
				position: absolute;
				top: 0;
				width: 100%;
				display:none;
			}
		</style>
		<div id="woocommerce_product_tabs" class="panel wc-metaboxes-wrapper">
			<p class="toolbar">
				<?php 
				$override_tab_layout = get_post_meta( $product_id, '_override_tab_layout', true );
				?>
				<label for="_override_tab_layout"><?php esc_html_e( 'Override default tab layout:', 'woocommerce-tab-manager' ); ?></label> <input type="checkbox" name="_override_tab_layout" class="wcfm-checkbox" id="_override_tab_layout" <?php checked( $override_tab_layout, "yes" ); ?> />
			</p>
	
			<div style="position:relative;">
			<div class="woocommerce_product_tabs wc-metaboxes">
	
				<?php
	
					// the core woocommerce tabs
					$core_tabs = wc_tab_manager()->get_core_tabs();
					$product_terms = get_the_terms( $product_id, 'product_cat' );
					$product_cats  = array();

					if ( ! is_wp_error( $product_terms ) ) {
						$product_cats = wp_list_pluck( (array) $product_terms, 'term_id' );
					}
	
					// get any global tabs
					$global_tabs = array();
					$posts       = get_posts( array( 'numberposts' => -1, 'post_type' => 'wc_product_tab', 'post_parent' => 0, 'post_status' => 'publish', 'suppress_filters' => false ) );
	
					foreach ( $posts as $post_obj ) {
	
						// compare selected categories for the tab vs the product categories
						$tab_cats  = get_post_meta( $post_obj->ID, '_wc_tab_categories', true );
	
						// don't add global tabs that won't be shown for this product
						if ( isset( $product_cats ) && ! empty( $tab_cats ) && ! array_intersect( $product_cats, $tab_cats ) ) {
							continue;
						}
	
						$tab = array( 'id' => $post_obj->ID, 'position' => 0, 'type' => 'global', 'title' => $post_obj->post_title );
						list( $tab['content'] ) = explode( "\n", wordwrap( str_replace( "\n", "", strip_shortcodes( strip_tags( $post_obj->post_content ) ) ), 155 ) );
	
						// content excerpt
						if ( strlen( $post_obj->post_content ) > 155 ) {
							$tab['content'] .= '...';
						}
	
						if ( $tab['content'] ) {
							$tab['content'] .= ' - ';
						}
	
						$tab['content'] .= ' <a href="' . get_edit_post_link( $post_obj->ID ) . '">' . __( 'Edit Global Tab Content', 'woocommerce-tab-manager' ) . '</a>';
	
						$global_tabs[ 'global_tab_' . $post_obj->ID ] = $tab;
					}
	
					// get any 3rd party tabs
					$third_party_tabs = array();
	
					foreach ( wc_tab_manager()->get_third_party_tabs() as $id => $tab ) {
						if ( ! isset( $tab['ignore'] ) || false === $tab['ignore'] ) {
							$description = isset($tab['description']) ? $tab['description'] : '';
							$third_party_tabs[ $id ] = array( 'id' => $tab['id'], 'position' => 0, 'type' => 'third_party', 'title' => $tab['title'], 'description' => $description );
						}
					}
	
					// if no tabs are set (for this product) try defaulting to the default tab layout, if it exists
					if ( ! is_array( $tabs ) ) {
						$tabs = get_option( 'wc_tab_manager_default_layout', false );
					}
	
					// if no default tab layout either, default to the core + 3rd party tabs
					if ( ! is_array( $tabs ) ) {
						$tabs = $core_tabs + $third_party_tabs;
					} else {
						// otherwise, get the content and title for any product/global tabs, and verify that any global/3rd party tabs still exist
						foreach ( $tabs as $id => $tab ) {
	
							if ( 'global' === $tab['type'] ) {
								// global tab: get an excerpt of the content to display if any, or if the tab has been removed or trashed, remove it from view
	
								if ( isset( $global_tabs[ $id ] ) ) {
									$tabs[ $id ]['title']   = $global_tabs[ $id ]['title'];
									$tabs[ $id ]['content'] = $global_tabs[ $id ]['content'];
								} else {
									// global tab is gone
									unset( $tabs[ $id ] );
								}
	
							} elseif ( 'third_party' === $tab['type'] ) {
								// 3rd party tab, does the plugin still exist?
	
								if ( isset( $third_party_tabs[ $id ] ) ) {
									$tabs[ $id ]['title']       = $third_party_tabs[ $id ]['title'];
									$tabs[ $id ]['description'] = $third_party_tabs[ $id ]['description'];
								} else {
									unset( $tabs[ $id ] );
								}
	
							} elseif ( 'product' === $tab['type'] ) {
								// get any custom product tab content from the underlying post
	
								$tab_post = get_post( $tab['id'] );
	
								if ( $tab_post && 'publish' === $tab_post->post_status ) {
									$tabs[ $id ]['content'] = $tab_post->post_content;
									$tabs[ $id ]['title']   = $tab_post->post_title;
								} else {
									// product tab is gone
									unset( $tabs[ $id ] );
								}
	
							}
						}
					}
	
					// markup for all core, global and 3rd party tabs will be rendered, and if not currently added to the product, they will be hidden until added
					$combined_tabs = array_merge( $core_tabs, $global_tabs, $third_party_tabs, $tabs );
	
					$i = 0;
					foreach ( $combined_tabs as $id => $tab ) {
	
						$position = $tab['position'];
	
						$active = isset( $tabs[ $id ] );
	
						// for the core tabs, even if the title is changed, keep the displayed name the same in the bar so there's less confusion
						$name = 'core' === $tab['type'] ? $core_tabs[ $id ]['title'] : $tab['title'];
						// handle the Reviews tab specially by cutting off the ' (%d)' which looks like garbage in the sortable tab list
						if ( 'core' === $tab['type'] && 'reviews' === $tab['id'] ) {
							$name = substr( $name, 0, -4 );
						}
	
						?>
						<div class="woocommerce_product_tab wc-metabox <?php echo sanitize_html_class( 'product_tab_' . $tab['type'] ); ?> <?php echo sanitize_html_class( $id ); ?>" rel="<?php echo esc_attr( $position ); ?>" <?php if ( ! $active ) echo ' style="display:none;"'; ?>>
							<h3>
								<button type="button" class="remove_row button wcfm_submit_button"><?php esc_html_e( 'Remove', 'woocommerce-tab-manager' ); ?></button>
								<strong class="product_tab_name"><?php echo esc_html( $name ); ?></strong>
							</h3>
							<table class="woocommerce_product_tab_data wc-metabox-content">
								<tr>
									<td>
										<?php if ( isset( $core_tabs[ $id ]['description'] ) ) : ?>
											<p><em><?php echo esc_html( $core_tabs[ $id ]['description'] ); ?></em></p>
										<?php endif; ?>
										<?php if ( 'third_party' === $tab['type'] ) : ?>
											<p><em><?php echo esc_html( $tab['description'] ? $tab['description'] : __( 'The title/content for this tab will be provided by a third party plugin', 'woocommerce-tab-manager' ) ); ?></em></p>
										<?php endif; ?>
										<div class="options_group">
											<?php if ( 'third_party' !== $tab['type'] ) : ?>
												<p class="form-field product_tab_title_field">
													<p class="wcfm_title" for="product_tab_title_<?php echo $i; ?>"><?php esc_html_e( 'Title', 'woocommerce-tab-manager' ); ?></p>
													<?php if ( 'global' === $tab['type'] ) : ?>
														<span><?php echo esc_html( $tab['title'] ); ?></span>
														<input type="hidden" name="product_tab_title[<?php echo $i; ?>]" value="<?php echo esc_attr( $tab['title'] ); ?>" />
													<?php else: ?>
														<input type="text" value="<?php echo esc_attr( $tab['title'] ); ?>" id="product_tab_title_<?php echo $i; ?>" name="product_tab_title[<?php echo $i; ?>]" class="wcfm-text"> <p class="description"><?php esc_html_e( "The tab title, this appears in the tab", 'woocommerce-tab-manager' ); ?></p>
													<?php endif; ?>
												</p>
											<?php endif; ?>
											<?php if ( isset( $core_tabs[ $id ]['heading'] ) && $core_tabs[ $id ]['heading'] ) : ?>
												<p class="form-field product_tab_heading_field">
													<p class="wcfm_title" for="product_tab_heading_<?php echo $i; ?>"><?php esc_html_e( 'Heading', 'woocommerce-tab-manager' ); ?></p>
													<input type="text" value="<?php echo esc_attr( $tab['heading'] ); ?>" id="product_tab_heading_<?php echo $i; ?>" name="product_tab_heading[<?php echo $i; ?>]" class="wcfm-text"> <p class="description"><?php esc_html_e( "The tab heading, this appears just before the tab content", 'woocommerce-tab-manager' ); ?></p>
												</p>
											<?php endif; ?>
											<?php if ( 'global' === $tab['type'] ) : ?>
												<p class="form-field product_tab_heading_field">
													<p class="wcfm_title" for="product_tab_content_<?php echo $i; ?>"><?php esc_html_e( 'Content', 'woocommerce-tab-manager' ); ?></p>
													<span><?php echo wp_kses_post( $tab['content'] ); ?></span>
												</p>
											<?php endif; ?>
										</div>
										<?php if ( 'product' === $tab['type'] && isset( $tab['content'] ) ) : ?>
											<?php /* Because the editor is within a movable block, we must disable the rich visual MCE editor, and use only the quicktags editor */
												wp_editor( $tab['content'], 'producttabcontent' . $i, array( 'textarea_name' => 'product_tab_content[' . $i . ']', 'tinymce' => false, 'textarea_rows' => 10 ) ); ?>
										<?php endif; ?>
										<input type="hidden" name="product_tab_active[<?php echo $i; ?>]" class="product_tab_active" value="<?php echo esc_attr( $active ); ?>" />
										<input type="hidden" name="product_tab_position[<?php echo $i; ?>]" class="product_tab_position" value="<?php echo esc_attr( $position ); ?>" />
										<input type="hidden" name="product_tab_type[<?php echo $i; ?>]" class="product_tab_type" value="<?php echo esc_attr( $tab['type'] ); ?>" />
										<input type="hidden" name="product_tab_id[<?php echo $i; ?>]" class="product_tab_id" value="<?php echo esc_attr( $tab['id'] ); ?>" />
									</td>
								</tr>
							</table>
						</div>
						<?php
						$i++;
					}
				?>
			</div>
	
			<?php $select_tabs = $core_tabs + $global_tabs + $third_party_tabs; ?>
			<p class="toolbar" style="text-align: right;">
				<button type="button" class="button button-primary add_product_tab wcfm_submit_button"><?php esc_html_e( 'Add', 'woocommerce-tab-manager' ); ?></button>
				<select name="product_tab" class="product_tab" style="margin-top: 10px;">
					<option value=""><?php esc_html_e( 'Custom Tab', 'woocommerce-tab-manager' ); ?></option>
					<?php
					foreach ( $select_tabs as $id => $tab ) :
						echo '<option value="' . esc_attr( $id ) . '">' . esc_html( $tab['title'] ) . '</option>';
					endforeach;
					?>
				</select>
			</p>
	
			<div class="clear"></div>
			<div id="wc_tab_manager_block"></div>
			</div>
		</div>
	</div>
</div>
<div class="wcfm_clearfix"></div>