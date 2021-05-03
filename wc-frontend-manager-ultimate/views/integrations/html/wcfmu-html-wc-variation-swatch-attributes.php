<?php
	
	defined( 'ABSPATH' ) or die( 'Keep Silent' );
	
	// $saved_product_attributes
	
	foreach ( $wvs_pro_attributes as $attribute_key => $wvs_pro_attribute ): ?>
		<?php
		$saved_type       = isset( $saved_product_attributes[ $attribute_key ] ) ? $saved_product_attributes[ $attribute_key ][ 'type' ] : $wvs_pro_attribute[ 'taxonomy' ][ 'attribute_type' ];
		$saved_style      = isset( $saved_product_attributes[ $attribute_key ] ) ? $saved_product_attributes[ $attribute_key ][ 'style' ] : '';
		$saved_tooltip    = isset( $saved_product_attributes[ $attribute_key ] ) ? $saved_product_attributes[ $attribute_key ][ 'show_tooltip' ] : '';
		$saved_image_size = isset( $saved_product_attributes[ $attribute_key ] ) ? $saved_product_attributes[ $attribute_key ][ 'image_size' ] : '';
		?>
        <div class="wc-metabox closed wvs-pro-variable-swatches-attribute-wrapper <?php echo( empty( $wvs_pro_attribute[ 'taxonomy_exists' ] ) ? 'not_a_taxonomy' : 'is_a_taxonomy' ) ?> visible_if_<?php echo $saved_type ?>">
            <h3 class="variable-swatches-attribute-header">
                <div class="handlediv" title="<?php esc_attr_e( 'Click to toggle', 'woo-variation-swatches-pro' ) ?>"></div>
                <strong class="attribute_name"><?php echo $wvs_pro_attribute[ 'taxonomy' ][ 'attribute_label' ] ?></strong>
                <div class="attribute-type-wrapper">
                    <strong><?php esc_html_e( 'Attribute Type', 'woo-variation-swatches-pro' ) ?></strong>
                    <input type="hidden" name="_wvs_pro_swatch_option[<?php echo $attribute_key ?>][default_type]" value="<?php echo $wvs_pro_attribute[ 'taxonomy' ][ 'attribute_type' ] ?>">
                    <select class="wcfm-select wvs-pro-swatch-option-type" name="_wvs_pro_swatch_option[<?php echo $attribute_key ?>][type]">
											<?php
												foreach ( $attribute_types as $key => $attribute_type ): ?>
													<?php if ( $wvs_pro_attribute[ 'taxonomy' ][ 'attribute_type' ] === $key ): ?>
															<option <?php selected( $saved_type, $key ) ?> value="<?php echo $key ?>"><?php echo $attribute_type ?> (<?php esc_html_e( 'Default', 'woo-variation-swatches-pro' ) ?>)</option>
													<?php else: ?>
															<option <?php selected( $saved_type, $key ) ?> value="<?php echo $key ?>"><?php echo $attribute_type ?></option>
													<?php endif; ?>
											<?php endforeach; ?>
                    </select>
                </div>
            </h3>
            <div class="variable-swatches-attribute-data wc-metabox-content wcfm_custom_hide">
                <table cellpadding="0" cellspacing="0">
                    <tbody>
                    <tr class="visible_if_custom visible_if_image visible_if_color visible_if_button">
                        <td class="wvs-pro-global-label-td">
                            <p class="wcfm_title wcfm_full_ele"><strong><?php esc_html_e( 'Shape style', 'woo-variation-swatches-pro' ) ?></strong></p>
                        </td>
                        <td>
                            <select class="wcfm-select" name="_wvs_pro_swatch_option[<?php echo $attribute_key ?>][style]">
                                <option <?php selected( $saved_style, '' ) ?> value=""><?php esc_html_e( 'Global', 'woo-variation-swatches-pro' ) ?></option>
                                <option <?php selected( $saved_style, 'rounded' ) ?> value="rounded"><?php esc_html_e( 'Rounded Shape', 'woo-variation-swatches-pro' ) ?></option>
                                <option <?php selected( $saved_style, 'squared' ) ?> value="squared"><?php esc_html_e( 'Squared Shape', 'woo-variation-swatches-pro' ) ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr class="visible_if_custom visible_if_image visible_if_color visible_if_button visible_if_radio">
                        <td class="wvs-pro-global-label-td">
                            <p class="wcfm_title wcfm_full_ele"><strong><?php esc_html_e( 'Enable tooltip', 'woo-variation-swatches-pro' ) ?></strong></p>
                        </td>
                        <td>
                            <select class="wcfm-select" name="_wvs_pro_swatch_option[<?php echo $attribute_key ?>][show_tooltip]">
                                <option <?php selected( $saved_tooltip, '' ) ?> value=""><?php esc_html_e( 'Global', 'woo-variation-swatches-pro' ) ?></option>
                                <option <?php selected( $saved_tooltip, 'no' ) ?> value="no"><?php esc_html_e( 'Hide', 'woo-variation-swatches-pro' ) ?></option>
                                <option <?php selected( $saved_tooltip, 'text' ) ?> value="text"><?php esc_html_e( 'Text', 'woo-variation-swatches-pro' ) ?></option>
                                <option <?php selected( $saved_tooltip, 'image' ) ?> value="image"><?php esc_html_e( 'Image', 'woo-variation-swatches-pro' ) ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr class="visible_if_custom visible_if_image">
                        <td class="wvs-pro-global-label-td">
                            <p class="wcfm_title wcfm_full_ele"><strong><?php esc_html_e( 'Attribute image size', 'woo-variation-swatches-pro' ) ?></strong></p>
                        </td>
                        <td>
                            <select class="wcfm-select" name="_wvs_pro_swatch_option[<?php echo $attribute_key ?>][image_size]">
                                <option <?php selected( $saved_image_size, '' ) ?> value=""><?php esc_html_e( 'Global', 'woo-variation-swatches-pro' ) ?></option>
                                <?php foreach ( wvs_get_all_image_sizes() as $key => $value ): ?>
                                    <option <?php selected( $saved_image_size, $key ) ?> value="<?php echo $key ?>"><?php echo $value ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <tr class="visible_if_custom visible_if_image visible_if_color visible_if_button visible_if_radio">
                        <td class="wvs-pro-variable-swatches-tax-wrapper-td" colspan="2">
							
							<?php foreach ( $wvs_pro_attribute[ 'terms' ] as $term_id => $term ): ?>
								<?php
								// terms
								if ( isset( $saved_product_attributes[ $attribute_key ] ) ) {
									// print_r( $saved_product_attributes[ $attribute_key ][ 'terms' ][ $term_id ]);
									$saved_term_type          = isset( $saved_product_attributes[ $attribute_key ][ 'terms' ][ $term_id ] ) ? $saved_product_attributes[ $attribute_key ][ 'terms' ][ $term_id ][ 'type' ] : $saved_type;
									$saved_term_tooltip       = isset( $saved_product_attributes[ $attribute_key ][ 'terms' ][ $term_id ] ) ? $saved_product_attributes[ $attribute_key ][ 'terms' ][ $term_id ][ 'tooltip_text' ] : '';
									$saved_term_tooltip_type  = isset( $saved_product_attributes[ $attribute_key ][ 'terms' ][ $term_id ] ) ? $saved_product_attributes[ $attribute_key ][ 'terms' ][ $term_id ][ 'tooltip_type' ] : '';
									$saved_term_tooltip_image = isset( $saved_product_attributes[ $attribute_key ][ 'terms' ][ $term_id ] ) ? $saved_product_attributes[ $attribute_key ][ 'terms' ][ $term_id ][ 'tooltip_image' ] : FALSE;
									$saved_term_image_id      = isset( $saved_product_attributes[ $attribute_key ][ 'terms' ][ $term_id ] ) ? $saved_product_attributes[ $attribute_key ][ 'terms' ][ $term_id ][ 'image_id' ] : FALSE;
									$saved_term_color         = isset( $saved_product_attributes[ $attribute_key ][ 'terms' ][ $term_id ] ) ? $saved_product_attributes[ $attribute_key ][ 'terms' ][ $term_id ][ 'color' ] : '';
								} else {
									$saved_term_type          = $saved_type;
									$saved_term_tooltip       = '';
									$saved_term_image_id      = FALSE;
									$saved_term_tooltip_image = FALSE;
									$saved_term_color         = '';
									$saved_term_tooltip_type  = '';
								}
								
								?>
                                <div class="wc-metabox wvs-pro-variable-swatches-attribute-tax-wrapper closed visible_if_tax_<?php echo $saved_term_type ?>">
                                    <h3 class="variable-swatches-taxonomy-header">
                                        <div class="handlediv" title="<?php esc_attr_e( 'Click to toggle', 'woo-variation-swatches-pro' ) ?>"></div>
                                        <strong class="attribute_name"><?php echo $term ?></strong>
                                        <div class="attribute-type-wrapper">
                                            <strong><?php esc_html_e( 'Type', 'woo-variation-swatches-pro' ) ?></strong>
                                            <select class="wvs-pro-swatch-tax-type wcfm-select" name="_wvs_pro_swatch_option[<?php echo $attribute_key ?>][terms][<?php echo $term_id ?>][type]">

                                                <!-- ADDING RADIO TYPE TO SELECT DEFAULT RADIO WHEN NO TYPE SELECTED -->
												<?php if ( in_array( 'radio', array_keys( $attribute_types ) ) ): ?>
                                                    <option value="radio" style="display: none"></option>
												<?php endif; ?>
												
												<?php foreach ( $attribute_types_configurable as $key => $attribute_type ): ?>
													<?php if ( $wvs_pro_attribute[ 'taxonomy' ][ 'attribute_type' ] === $key ): ?>
                                                        <option <?php selected( $saved_term_type, $key ) ?> value="<?php echo $key ?>"><?php echo $attribute_type ?> (<?php esc_html_e( 'Default', 'woo-variation-swatches-pro' ) ?>)</option>
													<?php else: ?>
                                                        <option <?php selected( $saved_term_type, $key ) ?> value="<?php echo $key ?>"><?php echo $attribute_type ?></option>
													<?php endif; ?>
												<?php endforeach; ?>
                                            </select>
                                        </div>
                                    </h3>
                                    <div class="variable-swatches-taxonomy-data wc-metabox-content">
                                        <table cellpadding="0" cellspacing="0">
                                            <tbody>
                                            <tr class="visible_if_tax_color visible_if_tax_image visible_if_tax_button visible_if_tax_radio">
                                                <td class="wvs-pro-global-label-td">
                                                    <p class="wcfm_title wcfm_full_ele"><strong><?php esc_html_e( 'Show Tooltip', 'woo-variation-swatches-pro' ) ?></strong></p>
                                                </td>
                                                <td>
                                                    <select class="wvs-pro-item-tooltip-type wcfm-select" name="_wvs_pro_swatch_option[<?php echo $attribute_key ?>][terms][<?php echo $term_id ?>][tooltip_type]">
                                                        <option <?php selected( $saved_term_tooltip_type, '' ) ?> value=""><?php esc_html_e( 'Default', 'woo-variation-swatches-pro' ) ?></option>
                                                        <option <?php selected( $saved_term_tooltip_type, 'text' ) ?> value="text"><?php esc_html_e( 'Text', 'woo-variation-swatches-pro' ) ?></option>
                                                        <option <?php selected( $saved_term_tooltip_type, 'image' ) ?> value="image"><?php esc_html_e( 'Image', 'woo-variation-swatches-pro' ) ?></option>
                                                        <option <?php selected( $saved_term_tooltip_type, 'no' ) ?> value="no"><?php esc_html_e( 'No', 'woo-variation-swatches-pro' ) ?></option>
                                                    </select>
                                                </td>
                                            </tr>

                                            <tr class="wvs-pro-item-tooltip-type-item wvs-pro-item-tooltip-type-text visible_if_tax_color visible_if_tax_image visible_if_tax_button visible_if_tax_radio">
                                                <td class="wvs-pro-global-label-td">
                                                    <p class="wcfm_title wcfm_full_ele"><strong><?php esc_html_e( 'Tooltip Text', 'woo-variation-swatches-pro' ) ?></strong></p>
                                                </td>
                                                <td>
                                                    <input class="wcfm-text wcfm_full_ele" value="<?php echo esc_attr( $saved_term_tooltip ) ?>" type="text" name="_wvs_pro_swatch_option[<?php echo $attribute_key ?>][terms][<?php echo $term_id ?>][tooltip_text]">
                                                </td>
                                            </tr>
                                            <tr class="wvs-pro-item-tooltip-type-item wvs-pro-item-tooltip-type-image visible_if_tax_color visible_if_tax_image visible_if_tax_button visible_if_tax_radio">
                                                <td class="wvs-pro-global-label-td">
                                                    <p class="wcfm_title wcfm_full_ele"><strong><?php esc_html_e( 'Tooltip Image', 'woo-variation-swatches-pro' ) ?></strong></p>
                                                </td>
                                                <td>

                                                    <div class="meta-image-field-wrapper">
                                                        <div class="button-wrapper">
                                                          <?php
                                                          $WCFM->wcfm_fields->wcfm_generate_form_field( array(  "_wvs_pro_swatch_option_".$attribute_key."_".$term_id."_tooltip_image" => array( 'type' => 'upload', 'wcfm_uploader_by_id' => true, 'name' => '_wvs_pro_swatch_option[' . $attribute_key . '][terms][' . $term_id . '][tooltip_image]', 'primage' => esc_url( $this->get_img_src( $saved_term_tooltip_image ) ), 'value' => $saved_term_tooltip_image )
                                                          	                                           ) );
                                                          ?>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr class="visible_if_tax_image">
                                                <td class="wvs-pro-global-label-td">
                                                    <p class="wcfm_title wcfm_full_ele"><strong><?php esc_html_e( 'Choose Image', 'woo-variation-swatches-pro' ) ?></strong></p>
                                                </td>
                                                <td>
                                                    <div class="meta-image-field-wrapper">
                                                        <div class="button-wrapper">
                                                          <?php
                                                          $WCFM->wcfm_fields->wcfm_generate_form_field( array(  "_wvs_pro_swatch_option_".$attribute_key."_".$term_id."_image_id" => array( 'type' => 'upload', 'wcfm_uploader_by_id' => true,  'name' => '_wvs_pro_swatch_option[' . $attribute_key . '][terms][' . $term_id . '][image_id]', 'primage' => esc_url( $this->get_img_src( $saved_term_image_id ) ), 'value' => $saved_term_image_id )
                                                          	                                           ) );
                                                          ?>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr class="visible_if_tax_color">
                                                <td class="wvs-pro-global-label-td">
                                                    <p class="wcfm_title wcfm_full_ele"><strong><?php esc_html_e( 'Color', 'woo-variation-swatches-pro' ) ?></strong></p>
                                                </td>
                                                <td class="wvs-color-picker-container">
                                                    <input name="_wvs_pro_swatch_option[<?php echo $attribute_key ?>][terms][<?php echo $term_id ?>][color]" type="text" class="wvs-color-picker" data-default-color="" value="<?php echo sanitize_hex_color( $saved_term_color ) ?>">
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
							<?php endforeach; ?>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
	<?php endforeach; ?>
