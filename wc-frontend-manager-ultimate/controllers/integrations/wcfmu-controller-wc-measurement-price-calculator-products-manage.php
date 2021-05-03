<?php
/**
 * WCFM plugin controllers
 *
 * Plugin WC Measurement Price Calculator Products Manage Controller
 *
 * @author 		WC Lovers
 * @package 	wcfmu/controllers/thirdparty
 * @version   5.4.1
 */

use SkyVerge\WooCommerce\PluginFramework\v5_4_0 as Framework;
 
class WCFMu_WC_Measurement_Price_Calculator_Products_Manage_Controller {
	
	public function __construct() {
		global $WCFM;
		
		// Third Party Product Meta Data Save
    add_action( 'after_wcfm_products_manage_meta_save', array( &$this, 'wcfm_wc_measurement_price_calculator_products_manage_meta_save' ), 160, 2 );
    
	}
	
	/**
	 * ACF Field Product Meta data save
	 */
	function wcfm_wc_measurement_price_calculator_products_manage_meta_save( $new_product_id, $wcfm_products_manage_form_data ) {
		global $WCFM, $WCFMu;
		
		$product = wc_get_product( $new_product_id );

		// skip saving the meta if this a calculator type is not set i.e. not a measurement product
		if ( isset( $wcfm_products_manage_form_data['_measurement_price_calculator'] ) && '' === $wcfm_products_manage_form_data['_measurement_price_calculator'] ) {
	
			$settings = get_post_meta( $product->get_id(), '_wc_price_calculator', true );
	
			// check if post meta is set already
			if ( ! empty( $settings ) && is_array( $settings ) ) {
	
				// only change the calculator type so none of the other fields are lost
				$settings['calculator_type'] = '';
	
				update_post_meta( $product->get_id(), '_wc_price_calculator', $settings );
			}
	
			return;
		}
		
		if ( isset( $wcfm_products_manage_form_data['_area'] ) ) {
			update_post_meta( $product->get_id(), '_area', $wcfm_products_manage_form_data['_area'] );
		}
		
		if ( isset( $wcfm_products_manage_form_data['_volume'] ) ) {
			update_post_meta( $product->get_id(), '_volume', $wcfm_products_manage_form_data['_volume'] );
		}
	
		// get product type
		$is_virtual   = isset( $wcfm_products_manage_form_data['is_virtual'] ) ? 'yes' : 'no';
		$product_type = sanitize_title( stripslashes( $wcfm_products_manage_form_data['product_type'] ) );
	
		// Dimensions: virtual and grouped products not allowed
		if ( 'no' === $is_virtual && 'grouped' !== $product_type ) {
	
			$settings = array();
	
			// the type of calculator enabled, one of 'dimension', 'area', etc or empty for disabled
			$settings['calculator_type'] = $wcfm_products_manage_form_data['_measurement_price_calculator'];
	
			$settings['dimension']['pricing'] = array(
				'enabled'        => isset( $wcfm_products_manage_form_data['_measurement_dimension_pricing'] ) && $wcfm_products_manage_form_data['_measurement_dimension_pricing'] ? 'yes' : 'no',
				'label'          => $wcfm_products_manage_form_data['_measurement_dimension_pricing_label'],
				'unit'           => $wcfm_products_manage_form_data['_measurement_dimension_pricing_unit'],
				'calculator'     => array(
					'enabled' => $this->wcfm_measurement_price_calculator_get_checkbox_post( $wcfm_products_manage_form_data,  '_measurement_dimension_pricing_calculator_enabled' ),
				),
				'inventory'      => array(
					'enabled' => $this->wcfm_measurement_price_calculator_get_checkbox_post( $wcfm_products_manage_form_data,  '_measurement_dimension_pricing_inventory_enabled' ),
				),
				'weight'         => array(
					'enabled' => $this->wcfm_measurement_price_calculator_get_checkbox_post( $wcfm_products_manage_form_data,  '_measurement_dimension_pricing_weight_enabled' ),
				),
				'overage'        => $this->wcfm_measurement_price_calculator_get_overage_post( $wcfm_products_manage_form_data,  'dimension' ),
			);
			$settings['dimension']['length'] = array(
				'enabled'          => isset( $wcfm_products_manage_form_data['_measurement_dimension'] ) && 'length' === $wcfm_products_manage_form_data['_measurement_dimension'] ? 'yes' : 'no',
				'label'            => $wcfm_products_manage_form_data['_measurement_dimension_length_label'],
				'unit'             => $wcfm_products_manage_form_data['_measurement_dimension_length_unit'],
				'editable'         => isset( $wcfm_products_manage_form_data['_measurement_dimension_length_editable'] ) && $wcfm_products_manage_form_data['_measurement_dimension_length_editable'] ? 'yes' : 'no',
				'options'          => $this->wcfm_measurement_price_calculator_get_options_post( $wcfm_products_manage_form_data,  '_measurement_dimension_length_options' ),
				'accepted_input'   => $this->wcfm_measurement_price_calculator_get_accepted_input_post( $wcfm_products_manage_form_data,  'dimension', 'length' ),
				'input_attributes' => $this->wcfm_measurement_price_calculator_get_input_attributes_post( $wcfm_products_manage_form_data,  'dimension', 'length' ),
			);
			$settings['dimension']['width'] = array(
				'enabled'          => isset( $wcfm_products_manage_form_data['_measurement_dimension'] ) && 'width' === $wcfm_products_manage_form_data['_measurement_dimension'] ? 'yes' : 'no',
				'label'            => $wcfm_products_manage_form_data['_measurement_dimension_width_label'],
				'unit'             => $wcfm_products_manage_form_data['_measurement_dimension_width_unit'],
				'editable'         => isset( $wcfm_products_manage_form_data['_measurement_dimension_width_editable'] ) && $wcfm_products_manage_form_data['_measurement_dimension_width_editable'] ? 'yes' : 'no',
				'options'          => $this->wcfm_measurement_price_calculator_get_options_post( $wcfm_products_manage_form_data,  '_measurement_dimension_width_options' ),
				'accepted_input'   => $this->wcfm_measurement_price_calculator_get_accepted_input_post( $wcfm_products_manage_form_data,  'dimension', 'width' ),
				'input_attributes' => $this->wcfm_measurement_price_calculator_get_input_attributes_post( $wcfm_products_manage_form_data,  'dimension', 'width' ),
			);
			$settings['dimension']['height'] = array(
				'enabled'          => isset( $wcfm_products_manage_form_data['_measurement_dimension'] ) && 'height' === $wcfm_products_manage_form_data['_measurement_dimension'] ? 'yes' : 'no',
				'label'            => $wcfm_products_manage_form_data['_measurement_dimension_height_label'],
				'unit'             => $wcfm_products_manage_form_data['_measurement_dimension_height_unit'],
				'editable'         => isset( $wcfm_products_manage_form_data['_measurement_dimension_height_editable'] ) && $wcfm_products_manage_form_data['_measurement_dimension_height_editable'] ? 'yes' : 'no',
				'options'          => $this->wcfm_measurement_price_calculator_get_options_post( $wcfm_products_manage_form_data,  '_measurement_dimension_height_options' ),
				'accepted_input'   => $this->wcfm_measurement_price_calculator_get_accepted_input_post( $wcfm_products_manage_form_data,  'dimension', 'height' ),
				'input_attributes' => $this->wcfm_measurement_price_calculator_get_input_attributes_post( $wcfm_products_manage_form_data,  'dimension', 'height' ),
			);
	
			// simple area calculator
			$settings['area']['pricing'] = array(
				'enabled'        => isset( $wcfm_products_manage_form_data['_measurement_area_pricing'] ) && $wcfm_products_manage_form_data['_measurement_area_pricing'] ? 'yes' : 'no',
				'label'          => $wcfm_products_manage_form_data['_measurement_area_pricing_label'],
				'unit'           => $wcfm_products_manage_form_data['_measurement_area_pricing_unit'],
				'calculator'     => array(
					'enabled' => $this->wcfm_measurement_price_calculator_get_checkbox_post( $wcfm_products_manage_form_data,  '_measurement_area_pricing_calculator_enabled' ),
				),
				'inventory'      => array(
					'enabled' => $this->wcfm_measurement_price_calculator_get_checkbox_post( $wcfm_products_manage_form_data,  '_measurement_area_pricing_inventory_enabled' ),
				),
				'weight'         => array(
					'enabled' => $this->wcfm_measurement_price_calculator_get_checkbox_post( $wcfm_products_manage_form_data,  '_measurement_area_pricing_weight_enabled' ),
				),
				'overage'        => $this->wcfm_measurement_price_calculator_get_overage_post( $wcfm_products_manage_form_data,  'area' ),
			);
			$settings['area']['area'] = array(
				'label'            => $wcfm_products_manage_form_data['_measurement_area_label'],
				'unit'             => $wcfm_products_manage_form_data['_measurement_area_unit'],
				'editable'         => isset( $wcfm_products_manage_form_data['_measurement_area_editable'] ) && $wcfm_products_manage_form_data['_measurement_area_editable'] ? 'yes' : 'no',
				'options'          => $this->wcfm_measurement_price_calculator_get_options_post( $wcfm_products_manage_form_data,  '_measurement_area_options' ),
				'accepted_input'   => $this->wcfm_measurement_price_calculator_get_accepted_input_post( $wcfm_products_manage_form_data,  'area', 'area' ),
				'input_attributes' => $this->wcfm_measurement_price_calculator_get_input_attributes_post( $wcfm_products_manage_form_data,  'area', 'area' ),
			);
	
			// area (LxW) calculator
			$settings['area-dimension']['pricing'] = array(
				'enabled'        => isset( $wcfm_products_manage_form_data['_measurement_area-dimension_pricing'] ) && $wcfm_products_manage_form_data['_measurement_area-dimension_pricing'] ? 'yes' : 'no',
				'label'          => $wcfm_products_manage_form_data['_measurement_area-dimension_pricing_label'],
				'unit'           => $wcfm_products_manage_form_data['_measurement_area-dimension_pricing_unit'],
				'calculator'     => array(
					'enabled' => $this->wcfm_measurement_price_calculator_get_checkbox_post( $wcfm_products_manage_form_data,  '_measurement_area-dimension_pricing_calculator_enabled' ),
				),
				'inventory'      => array(
					'enabled' => $this->wcfm_measurement_price_calculator_get_checkbox_post( $wcfm_products_manage_form_data,  '_measurement_area-dimension_pricing_inventory_enabled' ),
				),
				'weight'         => array(
					'enabled' => $this->wcfm_measurement_price_calculator_get_checkbox_post( $wcfm_products_manage_form_data,  '_measurement_area-dimension_pricing_weight_enabled' ),
				),
				'overage'        => $this->wcfm_measurement_price_calculator_get_overage_post( $wcfm_products_manage_form_data,  'area-dimension' ),
			);
			$settings['area-dimension']['length'] = array(
				'label'            => $wcfm_products_manage_form_data['_measurement_area_length_label'],
				'unit'             => $wcfm_products_manage_form_data['_measurement_area_length_unit'],
				'editable'         => 'yes',
				'options'          => $this->wcfm_measurement_price_calculator_get_options_post( $wcfm_products_manage_form_data,  '_measurement_area_length_options' ),
				'accepted_input'   => $this->wcfm_measurement_price_calculator_get_accepted_input_post( $wcfm_products_manage_form_data,  'area-dimension', 'length' ),
				'input_attributes' => $this->wcfm_measurement_price_calculator_get_input_attributes_post( $wcfm_products_manage_form_data,  'area-dimension', 'length' ),
			);
			$settings['area-dimension']['width'] = array(
				'label'            => $wcfm_products_manage_form_data['_measurement_area_width_label'],
				'unit'             => $wcfm_products_manage_form_data['_measurement_area_width_unit'],
				'editable'         => 'yes',
				'options'          => $this->wcfm_measurement_price_calculator_get_options_post( $wcfm_products_manage_form_data,  '_measurement_area_width_options' ),
				'accepted_input'   => $this->wcfm_measurement_price_calculator_get_accepted_input_post( $wcfm_products_manage_form_data,  'area-dimension', 'width' ),
				'input_attributes' => $this->wcfm_measurement_price_calculator_get_input_attributes_post( $wcfm_products_manage_form_data,  'area-dimension', 'width' ),
			);
	
			// Perimeter (2L + 2W) calculator
			$settings['area-linear']['pricing'] = array(
				'enabled'        => isset( $wcfm_products_manage_form_data['_measurement_area-linear_pricing'] ) && $wcfm_products_manage_form_data['_measurement_area-linear_pricing'] ? 'yes' : 'no',
				'label'          => $wcfm_products_manage_form_data['_measurement_area-linear_pricing_label'],
				'unit'           => $wcfm_products_manage_form_data['_measurement_area-linear_pricing_unit'],
				'calculator'     => array(
					'enabled' => $this->wcfm_measurement_price_calculator_get_checkbox_post( $wcfm_products_manage_form_data,  '_measurement_area-linear_pricing_calculator_enabled' ),
				),
				'inventory'      => array(
					'enabled' => $this->wcfm_measurement_price_calculator_get_checkbox_post( $wcfm_products_manage_form_data,  '_measurement_area-linear_pricing_inventory_enabled' ),
				),
				'weight'         => array(
					'enabled' => $this->wcfm_measurement_price_calculator_get_checkbox_post( $wcfm_products_manage_form_data,  '_measurement_area-linear_pricing_weight_enabled' ),
				),
				'overage'        => $this->wcfm_measurement_price_calculator_get_overage_post( $wcfm_products_manage_form_data,  'area-linear' ),
			);
			$settings['area-linear']['length'] = array(
				'label'            => $wcfm_products_manage_form_data['_measurement_area-linear_length_label'],
				'unit'             => $wcfm_products_manage_form_data['_measurement_area-linear_length_unit'],
				'editable'         => 'yes',
				'options'          => $this->wcfm_measurement_price_calculator_get_options_post( $wcfm_products_manage_form_data,  '_measurement_area-linear_length_options' ),
				'accepted_input'   => $this->wcfm_measurement_price_calculator_get_accepted_input_post( $wcfm_products_manage_form_data,  'area-linear', 'length' ),
				'input_attributes' => $this->wcfm_measurement_price_calculator_get_input_attributes_post( $wcfm_products_manage_form_data,  'area-linear', 'length' ),
			);
			$settings['area-linear']['width'] = array(
				'label'            => $wcfm_products_manage_form_data['_measurement_area-linear_width_label'],
				'unit'             => $wcfm_products_manage_form_data['_measurement_area-linear_width_unit'],
				'editable'         => 'yes',
				'options'          => $this->wcfm_measurement_price_calculator_get_options_post( $wcfm_products_manage_form_data,  '_measurement_area-linear_width_options' ),
				'accepted_input'   => $this->wcfm_measurement_price_calculator_get_accepted_input_post( $wcfm_products_manage_form_data,  'area-linear', 'width' ),
				'input_attributes' => $this->wcfm_measurement_price_calculator_get_input_attributes_post( $wcfm_products_manage_form_data,  'area-linear', 'width' ),
			);
	
			// Surface Area 2(LW + WH + LH) calculator
			$settings['area-surface']['pricing'] = array(
				'enabled'        => isset( $wcfm_products_manage_form_data['_measurement_area-surface_pricing'] ) && $wcfm_products_manage_form_data['_measurement_area-surface_pricing'] ? 'yes' : 'no',
				'label'          => $wcfm_products_manage_form_data['_measurement_area-surface_pricing_label'],
				'unit'           => $wcfm_products_manage_form_data['_measurement_area-surface_pricing_unit'],
				'calculator'     => array(
					'enabled' => $this->wcfm_measurement_price_calculator_get_checkbox_post( $wcfm_products_manage_form_data,  '_measurement_area-surface_pricing_calculator_enabled' ),
				),
				'inventory'      => array(
					'enabled' => $this->wcfm_measurement_price_calculator_get_checkbox_post( $wcfm_products_manage_form_data,  '_measurement_area-surface_pricing_inventory_enabled' ),
				),
				'weight'         => array(
					'enabled' => $this->wcfm_measurement_price_calculator_get_checkbox_post( $wcfm_products_manage_form_data,  '_measurement_area-surface_pricing_weight_enabled' ),
				),
				'overage'        => $this->wcfm_measurement_price_calculator_get_overage_post( $wcfm_products_manage_form_data,  'area-surface' ),
			);
			$settings['area-surface']['length'] = array(
				'label'            => $wcfm_products_manage_form_data['_measurement_area-surface_length_label'],
				'unit'             => $wcfm_products_manage_form_data['_measurement_area-surface_length_unit'],
				'editable'         => 'yes',
				'options'          => $this->wcfm_measurement_price_calculator_get_options_post( $wcfm_products_manage_form_data,  '_measurement_area-surface_length_options' ),
				'accepted_input'   => $this->wcfm_measurement_price_calculator_get_accepted_input_post( $wcfm_products_manage_form_data,  'area-surface', 'length' ),
				'input_attributes' => $this->wcfm_measurement_price_calculator_get_input_attributes_post( $wcfm_products_manage_form_data,  'area-surface', 'length' ),
			);
			$settings['area-surface']['width'] = array(
				'label'            => $wcfm_products_manage_form_data['_measurement_area-surface_width_label'],
				'unit'             => $wcfm_products_manage_form_data['_measurement_area-surface_width_unit'],
				'editable'         => 'yes',
				'options'          => $this->wcfm_measurement_price_calculator_get_options_post( $wcfm_products_manage_form_data,  '_measurement_area-surface_width_options' ),
				'accepted_input'   => $this->wcfm_measurement_price_calculator_get_accepted_input_post( $wcfm_products_manage_form_data,  'area-surface', 'width' ),
				'input_attributes' => $this->wcfm_measurement_price_calculator_get_input_attributes_post( $wcfm_products_manage_form_data,  'area-surface', 'width' ),
			);
			$settings['area-surface']['height'] = array(
				'label'            => $wcfm_products_manage_form_data['_measurement_area-surface_height_label'],
				'unit'             => $wcfm_products_manage_form_data['_measurement_area-surface_height_unit'],
				'editable'         => 'yes',
				'options'          => $this->wcfm_measurement_price_calculator_get_options_post( $wcfm_products_manage_form_data,  '_measurement_area-surface_height_options' ),
				'accepted_input'   => $this->wcfm_measurement_price_calculator_get_accepted_input_post( $wcfm_products_manage_form_data,  'area-surface', 'height' ),
				'input_attributes' => $this->wcfm_measurement_price_calculator_get_input_attributes_post( $wcfm_products_manage_form_data,  'area-surface', 'height' ),
			);
	
			// Simple volume calculator
			$settings['volume']['pricing'] = array(
				'enabled'        => isset( $wcfm_products_manage_form_data['_measurement_volume_pricing'] ) && $wcfm_products_manage_form_data['_measurement_volume_pricing'] ? 'yes' : 'no',
				'label'          => $wcfm_products_manage_form_data['_measurement_volume_pricing_label'],
				'unit'           => $wcfm_products_manage_form_data['_measurement_volume_pricing_unit'],
				'calculator'     => array(
					'enabled' => $this->wcfm_measurement_price_calculator_get_checkbox_post( $wcfm_products_manage_form_data,  '_measurement_volume_pricing_calculator_enabled' ),
				),
				'inventory'      => array(
					'enabled' => $this->wcfm_measurement_price_calculator_get_checkbox_post( $wcfm_products_manage_form_data,  '_measurement_volume_pricing_inventory_enabled' ),
				),
				'weight'         => array(
					'enabled' => $this->wcfm_measurement_price_calculator_get_checkbox_post( $wcfm_products_manage_form_data,  '_measurement_volume_pricing_weight_enabled' ),
				),
				'overage'        => $this->wcfm_measurement_price_calculator_get_overage_post( $wcfm_products_manage_form_data,  'volume' ),
			);
			$settings['volume']['volume'] = array(
				'label'            => $wcfm_products_manage_form_data['_measurement_volume_label'],
				'unit'             => $wcfm_products_manage_form_data['_measurement_volume_unit'],
				'editable'         => isset( $wcfm_products_manage_form_data['_measurement_volume_editable'] ) && $wcfm_products_manage_form_data['_measurement_volume_editable'] ? 'yes' : 'no',
				'options'          => $this->wcfm_measurement_price_calculator_get_options_post( $wcfm_products_manage_form_data,  '_measurement_volume_options' ),
				'accepted_input'   => $this->wcfm_measurement_price_calculator_get_accepted_input_post( $wcfm_products_manage_form_data,  'volume', 'volume' ),
				'input_attributes' => $this->wcfm_measurement_price_calculator_get_input_attributes_post( $wcfm_products_manage_form_data,  'volume', 'volume' ),
			);
	
			// volume (L x W x H) calculator
			$settings['volume-dimension']['pricing'] = array(
				'enabled'        => isset( $wcfm_products_manage_form_data['_measurement_volume-dimension_pricing'] ) && $wcfm_products_manage_form_data['_measurement_volume-dimension_pricing'] ? 'yes' : 'no',
				'label'          => $wcfm_products_manage_form_data['_measurement_volume-dimension_pricing_label'],
				'unit'           => $wcfm_products_manage_form_data['_measurement_volume-dimension_pricing_unit'],
				'calculator'     => array(
					'enabled' => $this->wcfm_measurement_price_calculator_get_checkbox_post( $wcfm_products_manage_form_data,  '_measurement_volume-dimension_pricing_calculator_enabled' ),
				),
				'inventory'      => array(
					'enabled' => $this->wcfm_measurement_price_calculator_get_checkbox_post( $wcfm_products_manage_form_data,  '_measurement_volume-dimension_pricing_inventory_enabled' ),
				),
				'weight'         => array(
					'enabled' => $this->wcfm_measurement_price_calculator_get_checkbox_post( $wcfm_products_manage_form_data,  '_measurement_volume-dimension_pricing_weight_enabled' ),
				),
				'overage'        => $this->wcfm_measurement_price_calculator_get_overage_post( $wcfm_products_manage_form_data,  'volume-dimension' ),
			);
			$settings['volume-dimension']['length'] = array(
				'label'            => $wcfm_products_manage_form_data['_measurement_volume_length_label'],
				'unit'             => $wcfm_products_manage_form_data['_measurement_volume_length_unit'],
				'editable'         => 'yes',
				'options'          => $this->wcfm_measurement_price_calculator_get_options_post( $wcfm_products_manage_form_data,  '_measurement_volume_length_options' ),
				'accepted_input'   => $this->wcfm_measurement_price_calculator_get_accepted_input_post( $wcfm_products_manage_form_data,  'volume-dimension', 'length' ),
				'input_attributes' => $this->wcfm_measurement_price_calculator_get_input_attributes_post( $wcfm_products_manage_form_data,  'volume-dimension', 'length' ),
			);
			$settings['volume-dimension']['width'] = array(
				'label'            => $wcfm_products_manage_form_data['_measurement_volume_width_label'],
				'unit'             => $wcfm_products_manage_form_data['_measurement_volume_width_unit'],
				'editable'         => 'yes',
				'options'          => $this->wcfm_measurement_price_calculator_get_options_post( $wcfm_products_manage_form_data,  '_measurement_volume_width_options' ),
				'accepted_input'   => $this->wcfm_measurement_price_calculator_get_accepted_input_post( $wcfm_products_manage_form_data,  'volume-dimension', 'width' ),
				'input_attributes' => $this->wcfm_measurement_price_calculator_get_input_attributes_post( $wcfm_products_manage_form_data,  'volume-dimension', 'width' ),
			);
			$settings['volume-dimension']['height'] = array(
				'label'            => $wcfm_products_manage_form_data['_measurement_volume_height_label'],
				'unit'             => $wcfm_products_manage_form_data['_measurement_volume_height_unit'],
				'editable'         => 'yes',
				'options'          => $this->wcfm_measurement_price_calculator_get_options_post( $wcfm_products_manage_form_data,  '_measurement_volume_height_options' ),
				'accepted_input'   => $this->wcfm_measurement_price_calculator_get_accepted_input_post( $wcfm_products_manage_form_data,  'volume-dimension', 'height' ),
				'input_attributes' => $this->wcfm_measurement_price_calculator_get_input_attributes_post( $wcfm_products_manage_form_data,  'volume-dimension', 'height' ),
			);
	
			// volume (A x H) calculator
			$settings['volume-area']['pricing'] = array(
				'enabled'        => isset( $wcfm_products_manage_form_data['_measurement_volume-area_pricing'] ) && $wcfm_products_manage_form_data['_measurement_volume-area_pricing'] ? 'yes' : 'no',
				'label'          => $wcfm_products_manage_form_data['_measurement_volume-area_pricing_label'],
				'unit'           => $wcfm_products_manage_form_data['_measurement_volume-area_pricing_unit'],
				'calculator'     => array(
					'enabled' => $this->wcfm_measurement_price_calculator_get_checkbox_post( $wcfm_products_manage_form_data,  '_measurement_volume-area_pricing_calculator_enabled' ),
				),
				'inventory'      => array(
					'enabled' => $this->wcfm_measurement_price_calculator_get_checkbox_post( $wcfm_products_manage_form_data,  '_measurement_volume-area_pricing_inventory_enabled' ),
				),
				'weight'         => array(
					'enabled' => $this->wcfm_measurement_price_calculator_get_checkbox_post( $wcfm_products_manage_form_data,  '_measurement_volume-area_pricing_weight_enabled' ),
				),
				'overage'        => $this->wcfm_measurement_price_calculator_get_overage_post( $wcfm_products_manage_form_data,  'volume-area' ),
			);
			$settings['volume-area']['area'] = array(
				'label'            => $wcfm_products_manage_form_data['_measurement_volume_area_label'],
				'unit'             => $wcfm_products_manage_form_data['_measurement_volume_area_unit'],
				'editable'         => 'yes',
				'options'          => $this->wcfm_measurement_price_calculator_get_options_post( $wcfm_products_manage_form_data,  '_measurement_volume_area_options' ),
				'accepted_input'   => $this->wcfm_measurement_price_calculator_get_accepted_input_post( $wcfm_products_manage_form_data,  'volume-area', 'area' ),
				'input_attributes' => $this->wcfm_measurement_price_calculator_get_input_attributes_post( $wcfm_products_manage_form_data,  'volume-area', 'area' ),
			);
			$settings['volume-area']['height'] = array(
				'label'            => $wcfm_products_manage_form_data['_measurement_volume_area_height_label'],
				'unit'             => $wcfm_products_manage_form_data['_measurement_volume_area_height_unit'],
				'editable'         => 'yes',
				'options'          => $this->wcfm_measurement_price_calculator_get_options_post( $wcfm_products_manage_form_data,  '_measurement_volume_area_height_options' ),
				'accepted_input'   => $this->wcfm_measurement_price_calculator_get_accepted_input_post( $wcfm_products_manage_form_data,  'volume-area', 'height' ),
				'input_attributes' => $this->wcfm_measurement_price_calculator_get_input_attributes_post( $wcfm_products_manage_form_data,  'volume-area', 'height' ),
			);
	
			// simple weight calculator
			$settings['weight']['pricing'] = array(
				'enabled'        => isset( $wcfm_products_manage_form_data['_measurement_weight_pricing'] ) && $wcfm_products_manage_form_data['_measurement_weight_pricing'] ? 'yes' : 'no',
				'label'          => $wcfm_products_manage_form_data['_measurement_weight_pricing_label'],
				'unit'           => $wcfm_products_manage_form_data['_measurement_weight_pricing_unit'],
				'calculator'     => array(
					'enabled' => $this->wcfm_measurement_price_calculator_get_checkbox_post( $wcfm_products_manage_form_data,  '_measurement_weight_pricing_calculator_enabled' ),
				),
				'inventory'      => array(
					'enabled' => $this->wcfm_measurement_price_calculator_get_checkbox_post( $wcfm_products_manage_form_data,  '_measurement_weight_pricing_inventory_enabled' ),
				),
				'weight'         => array(
					'enabled' => $this->wcfm_measurement_price_calculator_get_checkbox_post( $wcfm_products_manage_form_data,  '_measurement_weight_pricing_weight_enabled' ),
				),
				'overage'        => $this->wcfm_measurement_price_calculator_get_overage_post( $wcfm_products_manage_form_data,  'weight' ),
			);
			$settings['weight']['weight'] = array(
				'label'            => $wcfm_products_manage_form_data['_measurement_weight_label'],
				'unit'             => $wcfm_products_manage_form_data['_measurement_weight_unit'],
				'editable'         => isset( $wcfm_products_manage_form_data['_measurement_weight_editable'] ) && $wcfm_products_manage_form_data['_measurement_weight_editable'] ? 'yes' : 'no',
				'options'          => $this->wcfm_measurement_price_calculator_get_options_post( $wcfm_products_manage_form_data,  '_measurement_weight_options' ),
				'accepted_input'   => $this->wcfm_measurement_price_calculator_get_accepted_input_post( $wcfm_products_manage_form_data,  'weight', 'weight' ),
				'input_attributes' => $this->wcfm_measurement_price_calculator_get_input_attributes_post( $wcfm_products_manage_form_data,  'weight', 'weight' ),
			);
	
			// the wall calculator is just a bit of syntactic sugar on top of the Area (LxW) calculator
			$settings['wall-dimension']['pricing'] = array(
				'enabled'        => isset( $wcfm_products_manage_form_data['_measurement_wall-dimension_pricing'] ) && $wcfm_products_manage_form_data['_measurement_wall-dimension_pricing'] ? 'yes' : 'no',
				'label'          => $wcfm_products_manage_form_data['_measurement_wall-dimension_pricing_label'],
				'unit'           => $wcfm_products_manage_form_data['_measurement_wall-dimension_pricing_unit'],
				'calculator'     => array(
					'enabled' => $this->wcfm_measurement_price_calculator_get_checkbox_post( $wcfm_products_manage_form_data,  '_measurement_wall-dimension_pricing_calculator_enabled' ),
				),
				'inventory'      => array(
					'enabled' => $this->wcfm_measurement_price_calculator_get_checkbox_post( $wcfm_products_manage_form_data,  '_measurement_wall-dimension_pricing_inventory_enabled' ),
				),
				'weight'         => array(
					'enabled' => $this->wcfm_measurement_price_calculator_get_checkbox_post( $wcfm_products_manage_form_data,  '_measurement_wall-dimension_pricing_weight_enabled' ),
				),
				'overage'        => $this->wcfm_measurement_price_calculator_get_overage_post( $wcfm_products_manage_form_data,  'wall-dimension' ),
			);
			$settings['wall-dimension']['length'] = array(
				'label'            => $wcfm_products_manage_form_data['_measurement_wall_length_label'],
				'unit'             => $wcfm_products_manage_form_data['_measurement_wall_length_unit'],
				'editable'         => 'yes',
				'options'          => $this->wcfm_measurement_price_calculator_get_options_post( $wcfm_products_manage_form_data,  '_measurement_wall_length_options' ),
				'accepted_input'   => $this->wcfm_measurement_price_calculator_get_accepted_input_post( $wcfm_products_manage_form_data,  'wall-dimension', 'length' ),
				'input_attributes' => $this->wcfm_measurement_price_calculator_get_input_attributes_post( $wcfm_products_manage_form_data,  'wall-dimension', 'length' ),
			);
			$settings['wall-dimension']['width'] = array(
				'label'            => $wcfm_products_manage_form_data['_measurement_wall_width_label'],
				'unit'             => $wcfm_products_manage_form_data['_measurement_wall_width_unit'],
				'editable'         => 'yes',
				'options'          => $this->wcfm_measurement_price_calculator_get_options_post( $wcfm_products_manage_form_data,  '_measurement_wall_width_options' ),
				'accepted_input'   => $this->wcfm_measurement_price_calculator_get_accepted_input_post( $wcfm_products_manage_form_data,  'wall-dimension', 'width' ),
				'input_attributes' => $this->wcfm_measurement_price_calculator_get_input_attributes_post( $wcfm_products_manage_form_data,  'wall-dimension', 'width' ),
			);
	
			// save settings
			update_post_meta( $product->get_id(), '_wc_price_calculator', $settings );
	
			// persist any pricing rules
			$rules = array();
	
			// persist any rules assigned to this product, only if the current pricing calculator is enabled
			if ( isset( $wcfm_products_manage_form_data["_measurement_{$settings['calculator_type']}_pricing_calculator_enabled"] ) && ! empty( $wcfm_products_manage_form_data['_wc_measurement_pricing_rule_range_start'] ) && is_array( $wcfm_products_manage_form_data['_wc_measurement_pricing_rule_range_start'] ) ) {
	
				$regular_prices = $sale_prices = $prices = array();
	
				foreach ( $wcfm_products_manage_form_data['_wc_measurement_pricing_rule_range_start'] as $index => $pricing_rule_range_start ) {
	
					$pricing_rule_range_end     = $wcfm_products_manage_form_data['_wc_measurement_pricing_rule_range_end'][ $index ];
					$pricing_rule_regular_price = $wcfm_products_manage_form_data['_wc_measurement_pricing_rule_regular_price'][ $index ];
					$pricing_rule_sale_price    = $wcfm_products_manage_form_data['_wc_measurement_pricing_rule_sale_price'][ $index ];
					$pricing_rule_price         = '' !== $pricing_rule_sale_price ? $pricing_rule_sale_price : $pricing_rule_regular_price;
	
					if ( $pricing_rule_range_start || $pricing_rule_range_end || $pricing_rule_price ) {
	
						if ( is_numeric( $pricing_rule_sale_price ) ) {
							$sale_prices[]    = abs( $pricing_rule_sale_price );
						}
	
						if ( is_numeric( $pricing_rule_regular_price ) ) {
							$regular_prices[] = abs( $pricing_rule_regular_price );
						}
	
						if ( is_numeric( $pricing_rule_price ) ) {
							$prices[]         = abs( $pricing_rule_price );
						}
	
						$rules[] = array(
							'range_start'   => $pricing_rule_range_start,
							'range_end'     => $pricing_rule_range_end,
							'price'         => $pricing_rule_price,
							'regular_price' => $pricing_rule_regular_price,
							'sale_price'    => $pricing_rule_sale_price,
						);
					}
				}
	
				$meta_prices = array(
					'_price'         => ! empty( $prices )         ? min( $prices )         : '',
					'_regular_price' => ! empty( $regular_prices ) ? min( $regular_prices ) : '',
						'_sale_price'    => ! empty( $sale_prices )    ? min( $sale_prices )    : '',
				);
	
				// this tricks WC core to show the product in sale product listings when using direct MySQL queries
				foreach ( $meta_prices as $meta_key => $value ) {
					update_post_meta( $new_product_id, $meta_key, $value );
				}
			}
	
			// save settings
			update_post_meta( $new_product_id, '_wc_price_calculator_pricing_rules', $rules );
		}
	}
	
	/**
	 * Helper function to safely get a checkbox post value
	 *
	 * @access private
	 * @since 3.0
	 * @param string $name the checkbox name
	 * @return string "yes" or "no" depending on whether the checkbox named $name
	 *         was set
	 */
	function wcfm_measurement_price_calculator_get_checkbox_post( $wcfm_products_manage_form_data,  $name ) {
		
		return isset( $wcfm_products_manage_form_data[ $name ] ) && $wcfm_products_manage_form_data[ $name ] ? 'yes' : 'no';
	}
	
	
	/**
	 * Helper function to safely get overage post value
	 *
	 * @since 3.12.0
	 *
	 * @param string $measurement_type
	 * @return int positive number between 0 & 100
	 */
	function wcfm_measurement_price_calculator_get_overage_post( $wcfm_products_manage_form_data,  $measurement_type ) {
	  
		$input_name  = "_measurement_{$measurement_type}_pricing_overage";
		$input_value = isset( $wcfm_products_manage_form_data[ $input_name ] ) ? absint( $wcfm_products_manage_form_data[ $input_name ] ) : 0;
	
		if ( $input_value > 100 ) {
			return 100;
		}
	
		if ( $input_value < 0 ) {
			return 0;
		}
	
		return $input_value;
	}
	
	
	/**
	 * Helper function to safely get accepted input post value
	 *
	 * @since 3.12.0
	 *
	 * @param string $measurement_type
	 * @param string $input_name
	 * @return string
	 */
	function wcfm_measurement_price_calculator_get_accepted_input_post( $wcfm_products_manage_form_data,  $measurement_type, $input_name ) {
	  
		$post_name      = $measurement_type === $input_name ? "_measurement_{$measurement_type}_accepted_input" : "_measurement_{$measurement_type}_{$input_name}_accepted_input";
		$accepted_input = isset( $wcfm_products_manage_form_data[ $post_name ] ) ? sanitize_key( $wcfm_products_manage_form_data[ $post_name ] ) : '';
	
		if ( ! in_array( $accepted_input, array( 'free', 'limited' ) ) ) {
			$accepted_input = 'free';
		}
	
		return $accepted_input;
	}
	
	
	/**
	 * Helper function to safely get input attributes post values
	 *
	 * @since 3.12.0
	 *
	 * @param string $measurement_type
	 * @param string $input_name
	 * @return array
	 */
	function wcfm_measurement_price_calculator_get_input_attributes_post( $wcfm_products_manage_form_data,  $measurement_type, $input_name ) {
	  
		$post_name        = $measurement_type === $input_name ? "_measurement_{$measurement_type}_input_attributes" : "_measurement_{$measurement_type}_{$input_name}_input_attributes";
		$input_attributes = isset( $wcfm_products_manage_form_data[ $post_name ] ) && is_array( $wcfm_products_manage_form_data[ $post_name ] ) ? array_map( 'abs', $wcfm_products_manage_form_data[ $post_name ] ) : array();
	
		return wp_parse_args( array_filter( $input_attributes ), array(
			'min'  => '',
			'max'  => '',
			'step' => '',
		) );
	}
	
	
	/**
	 * Helper function to safely get measurement options post values
	 *
	 * @since 3.12.0
	 *
	 * @param string $input_name
	 * @return array
	 */
	function wcfm_measurement_price_calculator_get_options_post( $wcfm_products_manage_form_data,  $input_name ) {
	  
		$input_value = sanitize_text_field( isset( $wcfm_products_manage_form_data[ $input_name ] ) ? $wcfm_products_manage_form_data[ $input_name ] : '' );
	
		if ( empty( $input_value ) ) {
			 $values = array();
	
		// try to explode based on a semi-colon if a semi-colon exists in the input
		} elseif ( Framework\SV_WC_Helper::str_exists( $input_value, ';' ) ) {
	
			$values = array_map( 'trim', explode( ';', $input_value ) );
	
		} else {
			$values = array_map( 'trim', explode( ',', $input_value ) );
		}
	
		return $values;
	}
	
	
	/**
	 * Helper function to output limited option set.
	 *
	 * @since 3.12.8
	 *
	 * @param string[] $options original options array
	 * @return string delimited options
	 */
	function wcfm_measurement_price_calculator_get_options_value( $options ) {
	  global $WCFM, $WCFMu, $wcfm_products_manage_form_data;
		$value = null;
	
		if ( ',' === trim( wc_get_price_decimal_separator() ) ) {
			$value = implode( '; ', $options );
		}
	
		return $value ? $value : implode( ', ', $options );
	}
}