<?php
/**
 * WCFM plugin view
 *
 * WCfM License Generators popup View
 *
 * @author 		WC Lovers
 * @package 	wcfmu/views/integrations/license-manager
 * @version   6.4.0
 */
 
use LicenseManagerForWooCommerce\Repositories\Resources\Generator as GeneratorResourceRepository;

global $wp, $WCFM, $WCFMu, $_POST, $wpdb;

?>

<div class="license_generator_form_wrapper_hide">
	<div id="license_generator_form_wrapper" class="wcfm_popup_wrapper">
	  <div style="margin-bottom: 15px;"><h2 style="float: none;"><?php _e( 'License Generator', 'wc-frontend-manager-ultimate' ); ?></h2></div>
		<div id="wcfm_license_generator_form_wrapper">
			<form action="" method="post" id="wcfm_license_generator_form" class="license_generator_form" novalidate="">
			
				<?php 
				if( $generatorid ) {
					$generator = GeneratorResourceRepository::instance()->find( $generatorid );
					$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_license_generator_manage_popup_fields', array( 
																																				"lmfwc_name" => array( 'label' => __( 'Name', 'lmfwc' ), 'type' => 'text', 'class' => 'wcfm-text wcfm_ele wcfm_popup_input', 'label_class' => 'wcfm_title wcfm_popup_label', 'value' => esc_html($generator->getName()), 'custom_attributes' => array( 'required' => 1 ), 'desc_class' => 'wcfm_popup_descripton', 'desc' => __('A short name to describe the generator.', 'lmfwc') ),
																																				"lmfwc_times_activated_max" => array( 'label' => __( 'Maximum activation count', 'lmfwc' ), 'type' => 'text', 'class' => 'wcfm-text wcfm_ele wcfm_popup_input', 'label_class' => 'wcfm_title wcfm_popup_label', 'value' => esc_html($generator->getTimesActivatedMax()), 'desc_class' => 'wcfm_popup_descripton', 'desc' => __('Define how many times the license key can be marked as "activated" by using the REST API. Leave blank if you do not use the API.', 'lmfwc') ),
																																				"lmfwc_charset" => array( 'label' => __( 'Character map', 'lmfwc' ), 'type' => 'text', 'class' => 'wcfm-text wcfm_ele wcfm_popup_input', 'label_class' => 'wcfm_title wcfm_popup_label', 'value' => esc_html($generator->getCharset()), 'custom_attributes' => array( 'required' => 1 ), 'desc_class' => 'wcfm_popup_descripton', 'desc' => __('The characters which will be used for generating a license key, i.e. for <code>12-AB-34-CD</code> the character map is <code>ABCD1234</code>.', 'lmfwc') ),
																																				"lmfwc_chunks" => array( 'label' => __( 'Number of chunks', 'lmfwc' ), 'type' => 'number', 'class' => 'wcfm-text wcfm_ele wcfm_non_negative_input wcfm_popup_input', 'label_class' => 'wcfm_title wcfm_popup_label', 'value' => esc_html($generator->getChunks()), 'custom_attributes' => array( 'required' => 1 ), 'desc_class' => 'wcfm_popup_descripton', 'desc' => __('The number of separated character sets, i.e. for <code>12-AB-34-CD</code> the number of chunks is <code>4</code>.', 'lmfwc') ),
																																				"lmfwc_chunk_length" => array( 'label' => __( 'Chunk length', 'lmfwc' ), 'type' => 'number', 'class' => 'wcfm-text wcfm_ele wcfm_non_negative_input wcfm_popup_input', 'label_class' => 'wcfm_title wcfm_popup_label', 'value' => esc_html($generator->getChunkLength()), 'custom_attributes' => array( 'required' => 1 ), 'desc_class' => 'wcfm_popup_descripton', 'desc' => __('The character length of an individual chunk, i.e. for <code>12-AB-34-CD</code> the chunk length is <code>2</code>.', 'lmfwc') ),
																																				"lmfwc_separator" => array( 'label' => __( 'Separator', 'lmfwc' ), 'type' => 'text', 'class' => 'wcfm-text wcfm_ele wcfm_popup_input', 'label_class' => 'wcfm_title wcfm_popup_label', 'value' => esc_html($generator->getSeparator()), 'desc_class' => 'wcfm_popup_descripton', 'desc' => __('The special character separating the individual chunks, i.e. for <code>12-AB-34-CD</code> the separator is <code>-</code>.', 'lmfwc') ),
																																				"lmfwc_prefix" => array( 'label' => __( 'Prefix', 'lmfwc' ), 'type' => 'text', 'class' => 'wcfm-text wcfm_ele wcfm_popup_input', 'label_class' => 'wcfm_title wcfm_popup_label', 'value' => esc_html($generator->getPrefix()), 'desc_class' => 'wcfm_popup_descripton', 'desc' => __('Adds a character set at the start of a license key (separator <b>not</b> included), i.e. for <code>PRE-12-AB-34-CD</code> the prefix is <code>PRE-</code>.', 'lmfwc') ),
																																				"lmfwc_suffix" => array( 'label' => __( 'Suffix', 'lmfwc' ), 'type' => 'text', 'class' => 'wcfm-text wcfm_ele wcfm_popup_input', 'label_class' => 'wcfm_title wcfm_popup_label', 'value' => esc_html($generator->getSuffix()), 'desc_class' => 'wcfm_popup_descripton', 'desc' => __('Adds a character set at the end of a license key (separator <b>not</b> included), i.e. for <code>12-AB-34-CD-SUF</code> the suffix is <code>-SUF</code>.', 'lmfwc') ),
																																				"lmfwc_expires_in" => array( 'label' => __( 'Expires in', 'lmfwc' ), 'type' => 'text', 'class' => 'wcfm-text wcfm_ele wcfm_popup_input', 'label_class' => 'wcfm_title wcfm_popup_label', 'value' => esc_html($generator->getExpiresIn()), 'desc_class' => 'wcfm_popup_descripton', 'desc' => __('The number of days for which the license key is valid after purchase. Leave blank if it doesn\'t expire.', 'lmfwc') ),
																																				) ) ) ; 
				} else {
					$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_license_generator_manage_popup_fields', array( 
																																			"lmfwc_name" => array( 'label' => __( 'Name', 'lmfwc' ), 'type' => 'text', 'class' => 'wcfm-text wcfm_ele wcfm_popup_input', 'label_class' => 'wcfm_title wcfm_popup_label', 'custom_attributes' => array( 'required' => 1 ), 'desc_class' => 'wcfm_popup_descripton', 'desc' => __('A short name to describe the generator.', 'lmfwc') ),
																																			"lmfwc_times_activated_max" => array( 'label' => __( 'Maximum activation count', 'lmfwc' ), 'type' => 'text', 'class' => 'wcfm-text wcfm_ele wcfm_popup_input', 'label_class' => 'wcfm_title wcfm_popup_label', 'desc_class' => 'wcfm_popup_descripton', 'desc' => __('Define how many times the license key can be marked as "activated" by using the REST API. Leave blank if you do not use the API.', 'lmfwc') ),
																																			"lmfwc_charset" => array( 'label' => __( 'Character map', 'lmfwc' ), 'type' => 'text', 'class' => 'wcfm-text wcfm_ele wcfm_popup_input', 'label_class' => 'wcfm_title wcfm_popup_label', 'custom_attributes' => array( 'required' => 1 ), 'desc_class' => 'wcfm_popup_descripton', 'desc' => __('The characters which will be used for generating a license key, i.e. for <code>12-AB-34-CD</code> the character map is <code>ABCD1234</code>.', 'lmfwc') ),
																																			"lmfwc_chunks" => array( 'label' => __( 'Number of chunks', 'lmfwc' ), 'type' => 'number', 'class' => 'wcfm-text wcfm_ele wcfm_non_negative_input wcfm_popup_input', 'label_class' => 'wcfm_title wcfm_popup_label', 'custom_attributes' => array( 'required' => 1 ), 'desc_class' => 'wcfm_popup_descripton', 'desc' => __('The number of separated character sets, i.e. for <code>12-AB-34-CD</code> the number of chunks is <code>4</code>.', 'lmfwc') ),
																																			"lmfwc_chunk_length" => array( 'label' => __( 'Chunk length', 'lmfwc' ), 'type' => 'number', 'class' => 'wcfm-text wcfm_ele wcfm_non_negative_input wcfm_popup_input', 'label_class' => 'wcfm_title wcfm_popup_label', 'custom_attributes' => array( 'required' => 1 ), 'desc_class' => 'wcfm_popup_descripton', 'desc' => __('The character length of an individual chunk, i.e. for <code>12-AB-34-CD</code> the chunk length is <code>2</code>.', 'lmfwc') ),
																																			"lmfwc_separator" => array( 'label' => __( 'Separator', 'lmfwc' ), 'type' => 'text', 'class' => 'wcfm-text wcfm_ele wcfm_popup_input', 'label_class' => 'wcfm_title wcfm_popup_label', 'desc_class' => 'wcfm_popup_descripton', 'desc' => __('The special character separating the individual chunks, i.e. for <code>12-AB-34-CD</code> the separator is <code>-</code>.', 'lmfwc') ),
																																			"lmfwc_prefix" => array( 'label' => __( 'Prefix', 'lmfwc' ), 'type' => 'text', 'class' => 'wcfm-text wcfm_ele wcfm_popup_input', 'label_class' => 'wcfm_title wcfm_popup_label', 'desc_class' => 'wcfm_popup_descripton', 'desc' => __('Adds a character set at the start of a license key (separator <b>not</b> included), i.e. for <code>PRE-12-AB-34-CD</code> the prefix is <code>PRE-</code>.', 'lmfwc') ),
																																			"lmfwc_suffix" => array( 'label' => __( 'Suffix', 'lmfwc' ), 'type' => 'text', 'class' => 'wcfm-text wcfm_ele wcfm_popup_input', 'label_class' => 'wcfm_title wcfm_popup_label', 'desc_class' => 'wcfm_popup_descripton', 'desc' => __('Adds a character set at the end of a license key (separator <b>not</b> included), i.e. for <code>12-AB-34-CD-SUF</code> the suffix is <code>-SUF</code>.', 'lmfwc') ),
																																			"lmfwc_expires_in" => array( 'label' => __( 'Expires in', 'lmfwc' ), 'type' => 'text', 'class' => 'wcfm-text wcfm_ele wcfm_popup_input', 'label_class' => 'wcfm_title wcfm_popup_label', 'desc_class' => 'wcfm_popup_descripton', 'desc' => __('The number of days for which the license key is valid after purchase. Leave blank if it doesn\'t expire.', 'lmfwc') ),
																																			) ) ) ; 
				}
				
				?>
				
				<div class="wcfm_clearfix"></div>
				<div class="wcfm-message" tabindex="-1"></div>
				<div class="wcfm_clearfix"></div><br />
				
				<p class="form-submit">
					<input name="submit" type="submit" id="wcfm_license_generator_submit_button" class="submit wcfm_popup_button" value="<?php _e( 'Submit', 'wc-frontend-manager-ultimate' ); ?>"> 
					<input type="hidden" name="wcfm_license_generator_id" value="<?php echo $generatorid; ?>" id="wcfm_license_generator_id">
				</p>	
			</form>
			<div class="wcfm_clearfix"></div>
		</div>
	</div>
</div>
<div class="wcfm-clearfix"></div>