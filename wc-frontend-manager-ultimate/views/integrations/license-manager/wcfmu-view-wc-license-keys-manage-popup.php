<?php
/**
 * WCFM plugin view
 *
 * WCfM License Keys Manage popup View
 *
 * @author 		WC Lovers
 * @package 	wcfmu/views/integrations/license-manager
 * @version   6.4.0
 */
 
use LicenseManagerForWooCommerce\Enums\LicenseStatus;
use LicenseManagerForWooCommerce\Repositories\Resources\License as LicenseResourceRepository;
use LicenseManagerForWooCommerce\Models\Resources\License as LicenseResourceModel;

global $wp, $WCFM, $WCFMu, $_POST, $wpdb;

$statusOptions = LicenseStatus::dropdown();
$status_select_optons = array();
foreach($statusOptions as $option) {
	$status_select_optons[$option['value']] = esc_html($option['name']);
}
?>

<div class="license_keys_form_wrapper_hide">
	<div id="license_keys_form_wrapper" class="wcfm_popup_wrapper">
	  <div style="margin-bottom: 15px;"><h2 style="float: none;"><?php _e( 'License Key Manage', 'wc-frontend-manager-ultimate' ); ?></h2></div>
		<div id="wcfm_license_keys_form_wrapper">
			<form action="" method="post" id="wcfm_license_keys_form" class="license_keys_form" novalidate="">
			
				<?php 
				if( $licenseid ) {
					$license = LicenseResourceRepository::instance()->find( $licenseid );
					if ($license) {
						$expiresAt = null;
	
						if ($license->getExpiresAt()) {
								try {
										$expiresAtDateTime = new \DateTime($license->getExpiresAt());
										$expiresAt = $expiresAtDateTime->format('Y-m-d');
								} catch (\Exception $e) {
										$expiresAt = null;
								}
						}
						$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_license_keys_manage_popup_fields', array( 
																																					"edit__license_key" => array( 'label' => __( 'License key', 'lmfwc' ), 'type' => 'text', 'class' => 'wcfm-text wcfm_ele wcfm_popup_input', 'label_class' => 'wcfm_title wcfm_popup_label', 'value' => esc_html($license->getDecryptedLicenseKey()), 'custom_attributes' => array( 'required' => 1 ), 'desc_class' => 'wcfm_popup_descripton', 'desc' => __('The license key will be encrypted before it is stored inside the database.', 'lmfwc') ),
																																					"edit__valid_for" => array( 'label' => __( 'Valid for (days)', 'lmfwc' ), 'type' => 'text', 'class' => 'wcfm-text wcfm_ele wcfm_popup_input', 'label_class' => 'wcfm_title wcfm_popup_label', 'value' => esc_html($license->getValidFor()), 'desc_class' => 'wcfm_popup_descripton', 'desc' => __('Number of days for which the license key is valid after purchase. Leave blank if the license key does not expire. Cannot be used at the same time as the "Expires at" field.', 'lmfwc') ),
																																					"edit__expires_at" => array( 'label' => __( 'Expires at', 'lmfwc' ), 'type' => 'text', 'class' => 'wcfm-text wcfm_ele wcfm_popup_input', 'label_class' => 'wcfm_title wcfm_popup_label', 'value' => esc_html($expiresAt), 'desc_class' => 'wcfm_popup_descripton', 'desc' => __('The exact date this license key expires on. Leave blank if the license key does not expire. Cannot be used at the same time as the "Valid for (days)" field.', 'lmfwc') ),
																																					"edit__times_activated_max" => array( 'label' => __( 'Maximum activation count', 'lmfwc' ), 'type' => 'number', 'class' => 'wcfm-text wcfm_ele wcfm_non_negative_input wcfm_popup_input', 'label_class' => 'wcfm_title wcfm_popup_label', 'value' => esc_html($license->getTimesActivatedMax()), 'desc_class' => 'wcfm_popup_descripton', 'desc' => __('Define how many times the license key can be marked as "activated" by using the REST API. Leave blank if you do not use the API.', 'lmfwc') ),
																																					"edit__status" => array( 'label' => __( 'Status', 'lmfwc' ), 'type' => 'select', 'class' => 'wcfm-select wcfm_ele wcfm_popup_input', 'label_class' => 'wcfm_title wcfm_popup_label', 'options' => $status_select_optons, 'value' => $license->getStatus() ),
																																					
																																					"edit__order" => array( 'type' => 'hidden', 'value' => $license->getOrderId() ),
																																					"edit__product" => array( 'type' => 'hidden', 'value' => $license->getProductId() ),
																																					"edit__source" => array( 'type' => 'hidden', 'value' => esc_html($license->getSource()) ),
																																					) ) ) ; 
					} else {
						_e('Invalid license key ID', 'lmfwc');
					}
				}
				?>
				
				<div class="wcfm_clearfix"></div>
				<div class="wcfm-message" tabindex="-1"></div>
				<div class="wcfm_clearfix"></div><br />
				
				<p class="form-submit">
					<input name="submit" type="submit" id="wcfm_license_keys_submit_button" class="submit wcfm_popup_button" value="<?php _e( 'Submit', 'wc-frontend-manager-ultimate' ); ?>"> 
					<input type="hidden" name="wcfm_license_keys_id" value="<?php echo $licenseid; ?>" id="wcfm_license_keys_id">
				</p>	
			</form>
			<div class="wcfm_clearfix"></div>
		</div>
	</div>
</div>
<div class="wcfm-clearfix"></div>