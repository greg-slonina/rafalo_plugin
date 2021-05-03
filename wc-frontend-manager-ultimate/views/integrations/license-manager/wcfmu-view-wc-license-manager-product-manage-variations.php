<?php
/**
 * WCFM plugin views
 *
 * Plugin WC License Manager Variations Products Manage Views
 *
 * @author 		WC Lovers
 * @package 	wcfmu/views/integration/license-manager
 * @version   6.4.0
 */
 
use LicenseManagerForWooCommerce\Repositories\Resources\Generator as GeneratorResourceRepository;

global $wp, $WCFM, $WCFMu, $generatorOptions;
		
if( wcfm_is_vendor() ) {
	$vendor_id = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );
	$generators = GeneratorResourceRepository::instance()->findAllBy( array( 'created_by' => $vendor_id ) );
} else {
	$generators = GeneratorResourceRepository::instance()->findAll();
}

$generatorOptions  = array('' => __('Please select a generator', 'lmfwc'));
if ($generators) {
		/** @var GeneratorResourceModel $generator */
		foreach ($generators as $generator) {
				$generatorOptions[$generator->getId()] = sprintf(
						'(#%d) %s',
						$generator->getId(),
						$generator->getName()
				);
		}
}
?>