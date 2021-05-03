<?php
/**
 * WCFM plugin controllers
 *
 * Plugin License Generators Form Controller
 *
 * @author 		WC Lovers
 * @package 	wcfmu/controllers/integrations/license-manager
 * @version   6.4.0
 */

use LicenseManagerForWooCommerce\Repositories\Resources\Generator as GeneratorResourceRepository;

class WCFMu_License_Generators_Manage_Controller {
	
	public function __construct() {
		global $WCFM, $WCFMu;
		
		$this->processing();
	}
	
	public function processing() {
		global $WCFM, $WCFMu, $wpdb;
		
		$wcfm_license_generators_form_data = array();
	  parse_str($_POST['wcfm_license_generator_form'], $wcfm_license_generators_form_data);
	  
	  $has_error = false;
	  
	  if( isset( $wcfm_license_generators_form_data['lmfwc_name'] ) && !empty( $wcfm_license_generators_form_data['lmfwc_name'] ) && is_string( $wcfm_license_generators_form_data['lmfwc_name'] ) ) {
	  	if( isset( $wcfm_license_generators_form_data['lmfwc_charset'] ) && !empty( $wcfm_license_generators_form_data['lmfwc_charset'] ) && is_string( $wcfm_license_generators_form_data['lmfwc_charset'] ) ) {
				if( isset( $wcfm_license_generators_form_data['lmfwc_chunks'] ) && !empty( $wcfm_license_generators_form_data['lmfwc_chunks'] ) && is_numeric( $wcfm_license_generators_form_data['lmfwc_chunks'] ) ) {
					if( isset( $wcfm_license_generators_form_data['lmfwc_chunk_length'] ) && !empty( $wcfm_license_generators_form_data['lmfwc_chunk_length'] ) && is_numeric( $wcfm_license_generators_form_data['lmfwc_chunk_length'] ) ) {
	  	
						$generatorid = !empty( $wcfm_license_generators_form_data['wcfm_license_generator_id'] ) ? absint( $wcfm_license_generators_form_data['wcfm_license_generator_id'] ) : '';
						
						if( $generatorid ) {
							// Update the generator.
							$generator = GeneratorResourceRepository::instance()->update(
									$generatorid,
									array(
											'name'                => $wcfm_license_generators_form_data['lmfwc_name'],
											'charset'             => $wcfm_license_generators_form_data['lmfwc_charset'],
											'chunks'              => $wcfm_license_generators_form_data['lmfwc_chunks'],
											'chunk_length'        => $wcfm_license_generators_form_data['lmfwc_chunk_length'],
											'times_activated_max' => $wcfm_license_generators_form_data['lmfwc_times_activated_max'],
											'separator'           => $wcfm_license_generators_form_data['lmfwc_separator'],
											'prefix'              => $wcfm_license_generators_form_data['lmfwc_prefix'],
											'suffix'              => $wcfm_license_generators_form_data['lmfwc_suffix'],
											'expires_in'          => $wcfm_license_generators_form_data['lmfwc_expires_in']
									)
							);
							
							if ($generator) {
								echo '{"status": true, "message": "' . __('The Generator was updated successfully.', 'lmfwc') . '"}';
							} else {
								echo '{"status": false, "message": "' . __('There was a problem updating the generator.', 'lmfwc') . '"}';
							}
						} else {
							// Save the generator.
							$generator = GeneratorResourceRepository::instance()->insert(
									array(
											'name'                => $wcfm_license_generators_form_data['lmfwc_name'],
											'charset'             => $wcfm_license_generators_form_data['lmfwc_charset'],
											'chunks'              => $wcfm_license_generators_form_data['lmfwc_chunks'],
											'chunk_length'        => $wcfm_license_generators_form_data['lmfwc_chunk_length'],
											'times_activated_max' => $wcfm_license_generators_form_data['lmfwc_times_activated_max'],
											'separator'           => $wcfm_license_generators_form_data['lmfwc_separator'],
											'prefix'              => $wcfm_license_generators_form_data['lmfwc_prefix'],
											'suffix'              => $wcfm_license_generators_form_data['lmfwc_suffix'],
											'expires_in'          => $wcfm_license_generators_form_data['lmfwc_expires_in']
									)
							);
							
							if ($generator) {
								echo '{"status": true, "message": "' . __('The generator was added successfully.', 'lmfwc') . '"}';
							} else {
								echo '{"status": false, "message": "' . __('There was a problem adding the generator.', 'lmfwc') . '"}';
							}
						}
					}  else {
						echo '{"status": false, "message": "' . __('The Generator chunk length is invalid.', 'lmfwc') . '"}';
					}
				}  else {
						echo '{"status": false, "message": "' . __('The Generator chunks are invalid.', 'lmfwc') . '"}';
					}
			}  else {
				echo '{"status": false, "message": "' . __('The Generator charset is invalid.', 'lmfwc') . '"}';
			}
		} else {
			echo '{"status": false, "message": "' . __('The Generator name is invalid.', 'lmfwc') . '"}';
		}
		
		die;
	}
}

class WCFMu_License_Generators_Delete_Controller {
	
	public function __construct() {
		global $WCFM, $WCFMu;
		
		$this->processing();
	}
	
	public function processing() {
		global $WCFM, $WCFMu, $wpdb;
		
	  $generatorid = wc_clean($_POST['generatorid']);
	  
	  $result = GeneratorResourceRepository::instance()->delete(array($generatorid));

		$message = sprintf(esc_html__('%d generator(s) permanently deleted.', 'lmfwc'), $result);
		
		echo '{"status": true, "message": "' . $message. '" }';
		
		die;
	  	
	}
}