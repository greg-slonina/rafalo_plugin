<?php
/**
 * WCFM plugin view
 *
 * WCFM WC Measurement and Price Calculator Product Manage View
 *
 * @author 		WC Lovers
 * @package 	wcfmu/views/thirdparty
 * @version   5.4.1
 */

global $wp, $WCFM, $WCFMu, $post, $woocommerce;

if( !class_exists( '\\SkyVerge\\WooCommerce\\PluginFramework\\v5_5_0\\SV_WC_Plugin' ) ) return;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

if( !apply_filters( 'wcfm_is_allow_wc_measurement_price_calculator', true ) ) {
	return;
}

$product_id = 0;
$users  = array();

if( isset( $wp->query_vars['wcfm-products-manage'] ) && !empty( $wp->query_vars['wcfm-products-manage'] ) ) {
	$product_id = $wp->query_vars['wcfm-products-manage'];
	
	$post = get_post( $product_id );
	
	/*$product  = wc_get_product( $product_id );
	if( $product && !empty($product) && is_object($product) ) {
		$waitlist = new Pie_WCWL_Waitlist( $product );
		$users    = $waitlist->waitlist;
	}*/
}

include_once dirname( WC_PLUGIN_FILE ) . '/includes/admin/wc-meta-box-functions.php';
if ( Framework\SV_WC_Plugin_Compatibility::is_wc_version_gte( '3.3' ) ) {
	include_once( wc_measurement_price_calculator()->get_plugin_path() . '/includes/admin/post-types/writepanels/writepanel-product_data.php' );
	include_once( wc_measurement_price_calculator()->get_plugin_path() . '/includes/admin/post-types/writepanels/writepanel-product_data-calculator.php' );
	include_once( wc_measurement_price_calculator()->get_plugin_path() . '/includes/admin/post-types/writepanels/writepanel-product-type-variable.php' );
} else { 
	include_once( wc_measurement_price_calculator()->get_plugin_path() . '/admin/post-types/writepanels/writepanel-product_data.php' );
	include_once( wc_measurement_price_calculator()->get_plugin_path() . '/admin/post-types/writepanels/writepanel-product_data-calculator.php' );
	include_once( wc_measurement_price_calculator()->get_plugin_path() . '/admin/post-types/writepanels/writepanel-product-type-variable.php' );
}

// add additional physical property units/measurements
add_filter( 'woocommerce_products_general_settings', 'wcfm_measurement_price_calculator_woocommerce_catalog_settings' );


/**
 * Modify the WooCommerce > Settings > Catalog page to add additional
 * units of measurement, and physical properties to the config
 *
 * TODO: Perhaps the additional weight/dimension units should be added to the core, unless there was some reason they weren't there to begin with.  Then there's the core woocommerce_get_dimension() and woocommerce_get_dimension() functions to consider
 *
 * @param array $settings
 * @return array new settings
 */
function wcfm_measurement_price_calculator_woocommerce_catalog_settings( $settings ) {
	$new_settings = array();
	foreach ( $settings as &$setting ) {

		// safely add metric ton and english ton units to the weight units, in the correct order
		if ( 'woocommerce_weight_unit' === $setting['id'] ) {
			$options = array();
			if ( ! isset( $setting['options']['t'] ) ) $options['t'] = _x( 't', 'metric ton', 'woocommerce-measurement-price-calculator' );  // metric ton
			foreach ( $setting['options'] as $key => $value ) {
				if ( 'lbs' === $key ) {
					if ( ! isset( $setting['options']['tn'] ) ) $options['tn'] = _x( 'tn', 'english ton', 'woocommerce-measurement-price-calculator' );  // english ton
					$options[ $key ] = $value;
				} else {
					if ( ! isset( $options[ $key ] ) ) $options[ $key ] = $value;
				}
			}
			$setting['options'] = $options;
		}

		// safely add kilometer, foot, mile to the dimensions units, in the correct order
		if ( 'woocommerce_dimension_unit' === $setting['id'] ) {
			$options = array();
			if ( ! isset( $setting['options']['km'] ) ) $options['km'] = _x( 'km', 'kilometer', 'woocommerce-measurement-price-calculator' );  // kilometer
			foreach ( $setting['options'] as $key => $value ) {
				if ( 'in' === $key ) {
					$options[ $key ] = $value;
					if ( ! isset( $setting['options']['ft'] ) ) $options['ft'] = _x( 'ft', 'foot', 'woocommerce-measurement-price-calculator' );  // foot
					if ( ! isset( $options['yd'] ) ) $options['yd'] = _x( 'yd', 'yard', 'woocommerce-measurement-price-calculator' );  // yard (correct order)
					if ( ! isset( $setting['options']['mi'] ) ) $options['mi'] = _x( 'mi', 'mile', 'woocommerce-measurement-price-calculator' );  // mile
				} else {
					if ( ! isset( $options[ $key ] ) ) $options[ $key ] = $value;
				}
			}
			$setting['options'] = $options;
		}

		// add the setting into our new set of settings
		$new_settings[] = $setting;

		// add our area and volume units
		if ( 'woocommerce_dimension_unit' === $setting['id'] ) {

			$new_settings[] = array(
				'name'    => __( 'Area Unit', 'woocommerce-measurement-price-calculator' ),
				'desc'    => __( 'This controls what unit you can define areas in for the Measurements Price Calculator.', 'woocommerce-measurement-price-calculator' ),
				'id'      => 'woocommerce_area_unit',
				'css'     => 'min-width:300px;',
				'std'     => 'sq cm',
				'type'    => 'select',
				'class'   => 'wc-enhanced-select',
				'options' => array(
					'ha'      => _x( 'ha',      'hectare',           'woocommerce-measurement-price-calculator' ),
					'sq km'   => _x( 'sq km',   'square kilometer',  'woocommerce-measurement-price-calculator' ),
					'sq m'    => _x( 'sq m',    'square meter',      'woocommerce-measurement-price-calculator' ),
					'sq cm'   => _x( 'sq cm',   'square centimeter', 'woocommerce-measurement-price-calculator' ),
					'sq mm'   => _x( 'sq mm',   'square millimeter', 'woocommerce-measurement-price-calculator' ),
					'acs'     => _x( 'acs',     'acre',              'woocommerce-measurement-price-calculator' ),
					'sq. mi.' => _x( 'sq. mi.', 'square mile',       'woocommerce-measurement-price-calculator' ),
					'sq. yd.' => _x( 'sq. yd.', 'square yard',       'woocommerce-measurement-price-calculator' ),
					'sq. ft.' => _x( 'sq. ft.', 'square foot',       'woocommerce-measurement-price-calculator' ),
					'sq. in.' => _x( 'sq. in.', 'square inch',       'woocommerce-measurement-price-calculator' ),
				),
				'desc_tip'	=>  true,
			);

			// Note: 'cu mm' and 'cu km' are left out because they aren't really all that useful
			$new_settings[] = array(
				'name'    => __( 'Volume Unit', 'woocommerce-measurement-price-calculator' ),
				'desc'    => __( 'This controls what unit you can define volumes in for the Measurements Price Calculator.', 'woocommerce-measurement-price-calculator' ),
				'id'      => 'woocommerce_volume_unit',
				'css'     => 'min-width:300px;',
				'std'     => 'ml',
				'type'    => 'select',
				'class'   => 'wc-enhanced-select',
				'options' => array(
					'cu m'    => _x( 'cu m',    'cubic meter', 'woocommerce-measurement-price-calculator' ),
					'l'       => _x( 'l',       'liter',       'woocommerce-measurement-price-calculator' ),
					'ml'      => _x( 'ml',      'milliliter',  'woocommerce-measurement-price-calculator' ),  // aka 'cu cm'
					'gal'     => _x( 'gal',     'gallon',      'woocommerce-measurement-price-calculator' ),
					'qt'      => _x( 'qt',      'quart',       'woocommerce-measurement-price-calculator' ),
					'pt'      => _x( 'pt',      'pint',        'woocommerce-measurement-price-calculator' ),
					'cup'     => __( 'cup',     'woocommerce-measurement-price-calculator' ),
					'fl. oz.' => _x( 'fl. oz.', 'fluid ounce', 'woocommerce-measurement-price-calculator' ),
					'cu. yd.' => _x( 'cu. yd.', 'cubic yard',  'woocommerce-measurement-price-calculator' ),
					'cu. ft.' => _x( 'cu. ft.', 'cubic foot',  'woocommerce-measurement-price-calculator' ),
					'cu. in.' => _x( 'cu. in.', 'cubic inch',  'woocommerce-measurement-price-calculator' ),
				),
				'desc_tip' => true,
			);
		}
	}

	return $new_settings;
}

/**
 * Returns the WooCommerce product settings, containing measurement units
 *
 * @since 3.3
 */
if( !function_exists( 'wcfm_measurement_price_calculator_get_wc_settings' ) ) {
	function wcfm_measurement_price_calculator_get_wc_settings() {
	
		$plugin_path = wc()->plugin_path();

		if ( Framework\SV_WC_Plugin_Compatibility::is_wc_version_gte( '3.3' ) ) {
	
			if (    ! class_exists( 'WC_Settings_Page', false )
					 || ! class_exists( 'WC_Settings_Products', false ) ) {
	
				if ( ! class_exists( 'WC_Admin_Settings', false ) ) {
					include_once( $plugin_path . '/includes/admin/class-wc-admin-settings.php' );
				}
	
				\WC_Admin_Settings::get_settings_pages();
			}
	
			$settings_products = new \WC_Settings_Products();
	
		} else {
	
			include_once( $plugin_path . '/includes/admin/settings/class-wc-settings-page.php' );
	
			$settings_products = include( $plugin_path . '/includes/admin/settings/class-wc-settings-products.php' );
		}
		return $settings_products->get_settings();
	}
}

/**
 * Returns all available weight units
 *
 * @since 3.0
 * @return array of weight units
 */
if( !function_exists( 'wcfm_measurement_price_calculator_get_weight_units' ) ) {
	function wcfm_measurement_price_calculator_get_weight_units() {
	
		$settings = wcfm_measurement_price_calculator_get_wc_settings();
	
		foreach ( $settings as $setting ) {
			if ( 'woocommerce_weight_unit' === $setting['id'] ) {
				return $setting['options'];
			}
		}
	
		// default in case the woocommerce settings are not available
		return array(
			__( 'g', 'woocommerce-measurement-price-calculator' )   => __( 'g', 'woocommerce-measurement-price-calculator' ),
			__( 'kg', 'woocommerce-measurement-price-calculator' )  => __( 'kg', 'woocommerce-measurement-price-calculator' ),
			__( 't', 'woocommerce-measurement-price-calculator' )   => __( 't', 'woocommerce-measurement-price-calculator' ),
			__( 'oz', 'woocommerce-measurement-price-calculator' )  => __( 'oz', 'woocommerce-measurement-price-calculator' ),
			__( 'lbs', 'woocommerce-measurement-price-calculator' ) => __( 'lbs', 'woocommerce-measurement-price-calculator' ),
			__( 'tn', 'woocommerce-measurement-price-calculator' )  => __( 'tn', 'woocommerce-measurement-price-calculator' ),
		);
	}
}


/**
 * Returns all available dimension units
 *
 * @since 3.0
 * @return array of dimension units
 */
if( !function_exists( 'wcfm_measurement_price_calculator_get_dimension_units' ) ) {
	function wcfm_measurement_price_calculator_get_dimension_units() {
	
		$settings = wcfm_measurement_price_calculator_get_wc_settings();
		
		if ( $settings ) {
			foreach ( $settings as $setting ) {
				if ( 'woocommerce_dimension_unit' === $setting['id'] ) {
					return $setting['options'];
				}
			}
		}
	
		// default in case the woocommerce settings are not available
		return array(
			__( 'mm', 'woocommerce-measurement-price-calculator' ) => __( 'mm', 'woocommerce-measurement-price-calculator' ),
			__( 'cm', 'woocommerce-measurement-price-calculator' ) => __( 'cm', 'woocommerce-measurement-price-calculator' ),
			__( 'm',  'woocommerce-measurement-price-calculator' ) => __( 'm',  'woocommerce-measurement-price-calculator' ),
			__( 'km', 'woocommerce-measurement-price-calculator' ) => __( 'km', 'woocommerce-measurement-price-calculator' ),
			__( 'in', 'woocommerce-measurement-price-calculator' ) => __( 'in', 'woocommerce-measurement-price-calculator' ),
			__( 'ft', 'woocommerce-measurement-price-calculator' ) => __( 'ft', 'woocommerce-measurement-price-calculator' ),
			__( 'yd', 'woocommerce-measurement-price-calculator' ) => __( 'yd', 'woocommerce-measurement-price-calculator' ),
			__( 'mi', 'woocommerce-measurement-price-calculator' ) => __( 'mi', 'woocommerce-measurement-price-calculator' ),
		);
	}
}


/**
 * Returns all available area units
 *
 * @since 3.0
 * @return array of area units
 */
if( !function_exists( 'wcfm_measurement_price_calculator_get_area_units' ) ) {
	function wcfm_measurement_price_calculator_get_area_units() {
	
		$settings = wcfm_measurement_price_calculator_get_wc_settings();
	
		if ( $settings ) {
			foreach ( $settings as $setting ) {
				if ( 'woocommerce_area_unit' === $setting['id'] ) {
					return $setting['options'];
				}
			}
		}
	
		// default in case the woocommerce settings are not available
		return array(
			__( 'sq mm',   'woocommerce-measurement-price-calculator' ) => __( 'sq mm',   'woocommerce-measurement-price-calculator' ),
			__( 'sq cm',   'woocommerce-measurement-price-calculator' ) => __( 'sq cm',   'woocommerce-measurement-price-calculator' ),
			__( 'sq m',    'woocommerce-measurement-price-calculator' ) => __( 'sq m',    'woocommerce-measurement-price-calculator' ),
			__( 'ha',      'woocommerce-measurement-price-calculator' ) => __( 'ha',      'woocommerce-measurement-price-calculator' ),
			__( 'sq km',   'woocommerce-measurement-price-calculator' ) => __( 'sq km',   'woocommerce-measurement-price-calculator' ),
			__( 'sq. in.', 'woocommerce-measurement-price-calculator' ) => __( 'sq. in.', 'woocommerce-measurement-price-calculator' ),
			__( 'sq. ft.', 'woocommerce-measurement-price-calculator' ) => __( 'sq. ft.', 'woocommerce-measurement-price-calculator' ),
			__( 'sq. yd.', 'woocommerce-measurement-price-calculator' ) => __( 'sq. yd.', 'woocommerce-measurement-price-calculator' ),
			__( 'acs',     'woocommerce-measurement-price-calculator' ) => __( 'acs',     'woocommerce-measurement-price-calculator' ),
			__( 'sq. mi.', 'woocommerce-measurement-price-calculator' ) => __( 'sq. mi.', 'woocommerce-measurement-price-calculator' ),
		);
	}
}

/**
 * Render attributes inputs based on the measurement calculator option.
 *
 * @since 3.12.0
 *
 * @param array $args
 * @return void
 */
if( !function_exists( 'wcfm_measurement_price_calculator_attributes_inputs' ) ) {
	function wcfm_measurement_price_calculator_attributes_inputs( $args ) {
	
		$args = wp_parse_args( $args, array(
			'measurement'   => '',
			'input_name'    => '',
			'input_label'   => '',
			'settings'      => array(),
			'limited_field' => '',
		) );
	
		$settings    = $args['settings'];
		$measurement = $args['measurement'];
		$input_name  = $args['input_name'];
	
		if ( ! isset( $settings[ $measurement ] ) || ! isset( $settings[ $measurement ][ $input_name ] ) ) {
			return;
		}
	
		$inputs_id_prefix = $measurement === $input_name ? "_measurement_{$measurement}" : "_measurement_{$measurement}_{$input_name}";
	
		// for backwards compat to set an initial value; remove empty strings
		$original_options = array_filter( $settings[ $measurement ][ $input_name ]['options'] );
	
		woocommerce_wp_select( array(
			'id'                => "{$inputs_id_prefix}_accepted_input",
			'value'             => isset( $settings[$measurement][$input_name]['accepted_input'] ) ? $settings[$measurement][$input_name]['accepted_input'] : ( ! empty( $original_options ) ? 'limited' : 'free' ),
			'class'             => 'short small-text _measurement_accepted_input',
			'wrapper_class'     => '_measurement_pricing_calculator_fields',
			'label'             => sprintf( __( '%s Input', 'woocommerce-measurement-price-calculator' ), $args['input_label'] ),
			'options'           => array(
				'free'    => __( 'Accept free-form customer input', 'woocommerce-measurement-price-calculator' ),
				'limited' => __( 'Accept a limited set of customer inputs', 'woocommerce-measurement-price-calculator' ),
			),
			'custom_attributes' => array(
				'data-free'    => ".{$inputs_id_prefix}_input_attributes_field",
				'data-limited' => ".{$args['limited_field']}_field",
			),
		) );
	
		// these won't be set for stores upgrading to 3.12.0, have a sanity check
		$min_value  = isset( $settings[ $measurement ][ $input_name ]['input_attributes']['min'] )  ? $settings[ $measurement ][ $input_name ]['input_attributes']['min']  : '';
		$max_value  = isset( $settings[ $measurement ][ $input_name ]['input_attributes']['max'] )  ? $settings[ $measurement ][ $input_name ]['input_attributes']['max']  : '';
		$step_value = isset( $settings[ $measurement ][ $input_name ]['input_attributes']['step'] ) ? $settings[ $measurement ][ $input_name ]['input_attributes']['step'] : '';
	
		?>
		<p class="form-field <?php echo $inputs_id_prefix; ?>_input_attributes_field _measurement_pricing_calculator_fields _measurement_input_attributes dimensions_field">
			<label><?php printf( __( '%s Options', 'woocommerce-measurement-price-calculator' ), $args['input_label'] ); ?></label>
			<span class="wrap">
			<input placeholder="<?php esc_attr_e( 'Min value', 'woocommerce-measurement-price-calculator' ); ?>"
						 class="input-text wc_input_decimal" size="6" type="number" step="any"
						 name="<?php echo $inputs_id_prefix; ?>_input_attributes[min]"
						 value="<?php echo esc_attr( $min_value ); ?>"/>
			<input placeholder="<?php esc_attr_e( 'Max value', 'woocommerce-measurement-price-calculator' ); ?>"
						 class="input-text wc_input_decimal" size="6" type="number" step="any"
						 name="<?php echo $inputs_id_prefix; ?>_input_attributes[max]"
						 value="<?php echo esc_attr( $max_value ); ?>"/>
			<input placeholder="<?php esc_attr_e( 'Increment', 'woocommerce-measurement-price-calculator' ); ?>"
						 class="input-text wc_input_decimal last" size="6" type="number" step="any"
						 name="<?php echo $inputs_id_prefix; ?>_input_attributes[step]"
						 value="<?php echo esc_attr( $step_value ); ?>" />
			</span>
			<?php echo wc_help_tip( __( 'If applicable, enter limits to restrict customer input, such as an accepted increment and/or maximum value.', 'woocommerce-measurement-price-calculator' ) ); ?>
		</p>
		<?php
	}
}

/**
 * Output a radio input box.
 *
 * @access public
 * @param array $field with required fields 'id' and 'rbvalue'
 * @return void
 */
if( !function_exists( 'wcfm_measurement_price_calculator_wp_radio' ) ) {
	function wcfm_measurement_price_calculator_wp_radio( $field ) {
		global $thepostid, $post;
	
		if ( ! $thepostid ) {
			$thepostid = $product_id;
		}
		if ( ! isset( $field['class'] ) ) {
			$field['class'] = 'radio';
		}
		if ( ! isset( $field['wrapper_class'] ) ) {
			$field['wrapper_class'] = '';
		}
		if ( ! isset( $field['name'] ) ) {
			$field['name'] = $field['id'];
		}
		if ( ! isset( $field['value'] ) ) {
			$product        = wc_get_product( $thepostid );
			$field['value'] = $product ? SV_WC_Product_Compatibility::get_meta( $product, $field['name'], true ) : '';
		}
	
		echo '<p class="form-field ' . $field['id'] . '_field ' . $field['wrapper_class'] . '"><label for="' . $field['id'].'">' . $field['label'] . '</label><input type="radio" class="' . $field['class'] . '" name="' . $field['name'] . '" id="' . $field['id'] . '" value="' . $field['rbvalue'] . '" ';
	
		checked( $field['value'], $field['rbvalue'] );
	
		echo ' /> ';
	
		if ( isset( $field['description'] ) && $field['description'] ) echo '<span class="description">' . $field['description'] . '</span>';
	
		echo '</p>';
	}
}

/**
 * Render pricing overage input based on the measurement calculator option.
 *
 * @since 3.12.0
 *
 * @param string $measurement_type
 * @param array $settings
 * @return void
 */
if( !function_exists( 'wcfm_measurement_price_calculator_overage_input' ) ) {
	function wcfm_measurement_price_calculator_overage_input( $measurement_type, $settings ) {
	
		$id    = "_measurement_{$measurement_type}_pricing_overage";
		$value = isset( $settings[ $measurement_type ]['pricing']['overage'] ) ? $settings[ $measurement_type ]['pricing']['overage'] : '';
	
		woocommerce_wp_text_input( array(
			'id'                => $id,
			'value'             => $value,
			'type'              => 'number',
			'decimal'           => 'decimal',
			'class'             => 'short small-text _measurement_pricing_overage',
			'wrapper_class'     => '_measurement_pricing_calculator_fields',
			'placeholder'       => '%',
			'label'             => __( 'Add Overage ', 'woocommerce-measurement-price-calculator' ),
			'description'       => __( 'If you need to add and charge for a cut or overage estimate in addition to the customer input, enter the percentage of the total measurement to use.', 'woocommerce-measurement-price-calculator' ),
			'desc_tip'          => true,
			'custom_attributes' => array(
				'min'  => '0',
				'max'  => '100',
				'step' => '1',
			),
		) );
	}
}


/**
 * Returns all available volume units
 *
 * @since 3.0
 * @return array of volume units
 */
if( !function_exists( 'wcfm_measurement_price_calculator_get_volume_units' ) ) {
	function wcfm_measurement_price_calculator_get_volume_units() {
	
		$settings = wcfm_measurement_price_calculator_get_wc_settings();
	
		if ( $settings ) {
			foreach ( $settings as $setting ) {
				if ( 'woocommerce_volume_unit' === $setting['id'] ) {
					return $setting['options'];
				}
			}
		}
	
		// default in case the woocommerce settings are not available
		return array(
			__( 'ml',      'woocommerce-measurement-price-calculator' ) => __( 'ml',      'woocommerce-measurement-price-calculator' ),
			__( 'l',       'woocommerce-measurement-price-calculator' ) => __( 'l',       'woocommerce-measurement-price-calculator' ),
			__( 'cu m',    'woocommerce-measurement-price-calculator' ) => __( 'cu m',    'woocommerce-measurement-price-calculator' ),
			__( 'cup',     'woocommerce-measurement-price-calculator' ) => __( 'cup',     'woocommerce-measurement-price-calculator' ),
			__( 'pt',      'woocommerce-measurement-price-calculator' ) => __( 'pt',      'woocommerce-measurement-price-calculator' ),
			__( 'qt',      'woocommerce-measurement-price-calculator' ) => __( 'qt',      'woocommerce-measurement-price-calculator' ),
			__( 'gal',     'woocommerce-measurement-price-calculator' ) => __( 'gal',     'woocommerce-measurement-price-calculator' ),
			__( 'fl. oz.', 'woocommerce-measurement-price-calculator' ) => __( 'fl. oz.', 'woocommerce-measurement-price-calculator' ),
			__( 'cu. in.', 'woocommerce-measurement-price-calculator' ) => __( 'cu. in.', 'woocommerce-measurement-price-calculator' ),
			__( 'cu. ft.', 'woocommerce-measurement-price-calculator' ) => __( 'cu. ft.', 'woocommerce-measurement-price-calculator' ),
			__( 'cu. yd.', 'woocommerce-measurement-price-calculator' ) => __( 'cu. yd.', 'woocommerce-measurement-price-calculator' ),
		);
	}
}

/**
 * Helper function to output limited option set.
 *
 * @since 3.12.8
 *
 * @param string[] $options original options array
 * @return string delimited options
 */
if( !function_exists( 'wcfm_measurement_price_calculator_get_options_value' ) ) {
	function wcfm_measurement_price_calculator_get_options_value( $options ) {
	
		$value = null;
	
		if ( ',' === trim( wc_get_price_decimal_separator() ) ) {
			$value = implode( '; ', $options );
		}
	
		return $value ? $value : implode( ', ', $options );
	}
}

/**
 * Helper to get the "options" input description.
 *
 * @since 3.12.8
 *
 * @return string description text
 */
if( !function_exists( 'wcfm_measurement_price_calculator_get_options_tooltip' ) ) {
	function wcfm_measurement_price_calculator_get_options_tooltip() {
	
		// use semi-colons if commas are used as the decimal separator
		$delimiter = ',' === trim( wc_get_price_decimal_separator() ) ? 'semicolon' : 'comma';
	
		/* translators: Placeholder: %s - delimiter to use in the input */
		$description = sprintf( __( 'Use a single number to set a fixed value for this field on the frontend, or a %s-separated list of numbers to create a select box for the customer to choose between.', 'woocommerce-measurement-price-calculator' ), $delimiter );
	
		if ( 'semicolon' === $delimiter ) {
			$description .= ' ' . __( 'Example: 1/8; 0,5; 2', 'woocommerce-measurement-price-calculator' );
		} else {
			$description .= ' ' . __( 'Example: 1/8, 0.5, 2', 'woocommerce-measurement-price-calculator' );
		}
	
		return $description;
	}
}

$measurement_units = array(
		'weight'    => wcfm_measurement_price_calculator_get_weight_units(),
		'dimension' => wcfm_measurement_price_calculator_get_dimension_units(),
		'area'      => wcfm_measurement_price_calculator_get_area_units(),
		'volume'    => wcfm_measurement_price_calculator_get_volume_units(),
	);

?>

<div class="page_collapsible products_manage_wc_measurement_price_calculator simple variable" id="wcfm_products_manage_form_wc_measurement_price_calculator_head"><label class="wcfmfa fa-weight"></label><?php _e('Measurement', 'wc-frontend-manager-ultimate'); ?><span></span></div>
<div class="wcfm-container simple variable">
	<div id="wcfm_products_manage_form_wc_measurement_price_calculator_expander" class="wcfm-content">
	  <h2><?php _e('Measurement Price Setting', 'wc-frontend-manager-ultimate'); ?></h2>
	  <div class="wcfm_clearfix"></div>
	  
	  <div id="measurement_product_data" class="panel woocommerce_options_panel">
			<style type="text/css">
				#measurement_product_data hr { height:2px; border-style:none; border-bottom:solid 1px white; color:#DFDFDF; background-color:#DFDFDF; }
				.measurement-subnav { margin:14px 12px; }
				.measurement-subnav a { text-decoration:none; }
				.measurement-subnav a.active { color:black; font-weight:bold; }
				.measurement-subnav a.disabled { color: #8A7F7F; cursor: default; }
				#measurement_product_data .wc-calculator-pricing-table td.wc-calculator-pricing-rule-range input { float:none; width:auto; }
				#measurement_product_data table.wc-calculator-pricing-table { margin: 12px; width: 95%; }
				#measurement_product_data table.wc-calculator-pricing-table td { padding: 10px 7px 10px; cursor: move; }
				#measurement_product_data table.wc-calculator-pricing-table button { font-family: sans-serif; }
				#measurement_product_data table.wc-calculator-pricing-table button.wc-calculator-pricing-table-delete-rules { float: right; }
				#measurement_product_data input._measurement_pricing_overage { width: 65px !important; }
			</style>
			<div class="measurement-subnav">
				<a class="active" href="#calculator-settings"><?php esc_html_e( 'Calculator Settings', 'woocommerce-measurement-price-calculator' ); ?></a> |
				<a class="wc-measurement-price-calculator-pricing-table" href="#calculator-pricing-table"><?php esc_html_e( 'Pricing Table', 'woocommerce-measurement-price-calculator' ); ?></a>
			</div>
			<hr/>
			<?php
			$settings = new WC_Price_Calculator_Settings( $product_id );
	
			$pricing_weight_wrapper_class = '';
			if ( 'no' === get_option( 'woocommerce_enable_weight', true ) ) {
				$pricing_weight_wrapper_class = 'hidden';
			}
	
			$settings = $settings->get_raw_settings();  // we want the underlying raw settings array
	
			$calculator_options = array(
				''                 => __( 'None',                         'woocommerce-measurement-price-calculator' ),
				'dimension'        => __( 'Dimensions',                   'woocommerce-measurement-price-calculator' ),
				'area'             => __( 'Area',                         'woocommerce-measurement-price-calculator' ),
				'area-dimension'   => __( 'Area (LxW)',                   'woocommerce-measurement-price-calculator' ),
				'area-linear'      => __( 'Perimeter (2L + 2W)',          'woocommerce-measurement-price-calculator' ),
				'area-surface'     => __( 'Surface Area 2(LW + LH + WH)', 'woocommerce-measurement-price-calculator' ),
				'volume'           => __( 'Volume',                       'woocommerce-measurement-price-calculator' ),
				'volume-dimension' => __( 'Volume (LxWxH)',               'woocommerce-measurement-price-calculator' ),
				'volume-area'      => __( 'Volume (AxH)',                 'woocommerce-measurement-price-calculator' ),
				'weight'           => __( 'Weight',                       'woocommerce-measurement-price-calculator' ),
				'wall-dimension'   => __( 'Room Walls',                   'woocommerce-measurement-price-calculator' ),
			);
	
			echo '<div id="calculator-settings" class="calculator-subpanel">';
	
			// Measurement select
			woocommerce_wp_select( array(
				'id'          => '_measurement_price_calculator',
				'value'       => $settings['calculator_type'],
				'label'       => __( 'Measurement', 'woocommerce-measurement-price-calculator' ),
				'options'     => $calculator_options,
				'description' => __( 'Select the product measurement to calculate quantity by or define pricing within.', 'woocommerce-measurement-price-calculator' ),
				'desc_tip'    => true,
			) );
	
			echo '<p id="area-dimension_description" class="measurement_description" style="display:none;">' .   __( "Use this measurement to have the customer prompted for a length and width to calculate the area required.  When pricing is disabled (no custom dimensions) this calculator uses the product area attribute or otherwise the length and width attributes to determine the product area.", 'woocommerce-measurement-price-calculator' ) . '</p>';
			echo '<p id="area-linear_description" class="measurement_description" style="display:none;">' .      __( "Use this measurement to have the customer prompted for a length and width to calculate the linear distance (L * 2 + W * 2).", 'woocommerce-measurement-price-calculator' ) . '</p>';
			echo '<p id="area-surface_description" class="measurement_description" style="display:none;">' .     __( "Use this measurement to have the customer prompted for a length, width and height to calculate the surface area 2 * (L * W + W * H + L * H).", 'woocommerce-measurement-price-calculator' ) . '</p>';
			echo '<p id="volume-dimension_description" class="measurement_description" style="display:none;">' . __( "Use this measurement to have the customer prompted for a length, width and height to calculate the volume required.  When pricing is disabled (no custom dimensions) this calculator uses the product volume attribute or otherwise the length, width and height attributes to determine the product volume.", 'woocommerce-measurement-price-calculator' ) . '</p>';
			echo '<p id="volume-area_description" class="measurement_description" style="display:none;">' .      __( "Use this measurement to have the customer prompted for an area and height to calculate the volume required.  When pricing is disabled (no custom dimensions) this calculator uses the product volume attribute or otherwise the length, width and height attributes to determine the product volume.", 'woocommerce-measurement-price-calculator' ) . '</p>';
			echo '<p id="wall-dimension_description" class="measurement_description" style="display:none;">' .   __( "Use this measurement for applications such as wallpaper; the customer will be prompted for the wall height and distance around the room.  When pricing is disabled (no custom dimensions) this calculator uses the product area attribute or otherwise the length and width attributes to determine the wall surface area.", 'woocommerce-measurement-price-calculator' ) . '</p>';
	
			echo '<div id="dimension_measurements" class="measurement_fields">';
				woocommerce_wp_checkbox( array(
					'id'            => '_measurement_dimension_pricing',
					'value'         => $settings['dimension']['pricing']['enabled'],
					'class'         => 'checkbox _measurement_pricing',
					'label'         => __( 'Show Product Price Per Unit', 'woocommerce-measurement-price-calculator' ),
					'description'   => __( 'Check this box to display product pricing per unit on the frontend', 'woocommerce-measurement-price-calculator' ),
				) );
				echo '<div id="_measurement_dimension_pricing_fields" class="_measurement_pricing_fields" style="display:none;">';
					woocommerce_wp_text_input( array(
						'id'          => '_measurement_dimension_pricing_label',
						'value'       => $settings['dimension']['pricing']['label'],
						'label'       => __( 'Pricing Label', 'woocommerce-measurement-price-calculator' ),
						'description' => __( 'Label to display next to the product price (defaults to pricing unit)', 'woocommerce-measurement-price-calculator' ),
						'desc_tip'    => true,
					) );
					woocommerce_wp_select( array(
						'id'          => '_measurement_dimension_pricing_unit',
						'value'       => $settings['dimension']['pricing']['unit'],
						'class'       => '_measurement_pricing_unit',
						'label'       => __( 'Pricing Unit', 'woocommerce-measurement-price-calculator' ),
						'options'     => $measurement_units['dimension'],
						'description' => __( 'Unit to define pricing in', 'woocommerce-measurement-price-calculator' ),
						'desc_tip'    => true,
					) );
					woocommerce_wp_checkbox( array(
						'id'            => '_measurement_dimension_pricing_calculator_enabled',
						'class'         => 'checkbox _measurement_pricing_calculator_enabled',
						'value'         => $settings['dimension']['pricing']['calculator']['enabled'],
						'label'         => __( 'Calculated Price', 'woocommerce-measurement-price-calculator' ),
						'description'   => __( 'Check this box to define product pricing per unit and allow customers to provide custom measurements', 'woocommerce-measurement-price-calculator' ),
					) );
					woocommerce_wp_checkbox( array(
						'id'            => '_measurement_dimension_pricing_weight_enabled',
						'value'         => $settings['dimension']['pricing']['weight']['enabled'],
						'class'         => 'checkbox _measurement_pricing_weight_enabled',
						'wrapper_class' => $pricing_weight_wrapper_class . ' _measurement_pricing_calculator_fields',
						'label'         => __( 'Calculated Weight', 'woocommerce-measurement-price-calculator' ),
						'description'   => __( 'Check this box to define the product weight per unit and calculate the item weight based on the product dimension', 'woocommerce-measurement-price-calculator' ),
					) );
					woocommerce_wp_checkbox( array(
						'id'            => '_measurement_dimension_pricing_inventory_enabled',
						'value'         => $settings['dimension']['pricing']['inventory']['enabled'],
						'class'         => 'checkbox _measurement_pricing_inventory_enabled',
						'wrapper_class' => 'stock_fields _measurement_pricing_calculator_fields',
						'label'         => __( 'Calculated Inventory', 'woocommerce-measurement-price-calculator' ),
						'description'   => __( 'Check this box to define inventory per unit and calculate inventory based on the product dimension', 'woocommerce-measurement-price-calculator' ),
					) );
					wcfm_measurement_price_calculator_overage_input( 'dimension', $settings );
				echo '</div>';
				echo '<hr/>';
	
				// Dimension - Length
				wcfm_measurement_price_calculator_wp_radio( array(
					'name'        => '_measurement_dimension',
					'id'          => '_measurement_dimension_length',
					'rbvalue'     => 'length',
					'value'       => 'yes' == $settings['dimension']['length']['enabled'] ? 'length' : '',
					'class'       => 'checkbox _measurement_dimension',
					'label'       => __( 'Length', 'woocommerce-measurement-price-calculator' ),
					'description' => __( 'Select to display the product length in the price calculator', 'woocommerce-measurement-price-calculator' ),
				) );
				echo '<div id="_measurement_dimension_length_fields" style="display:none;">';
					woocommerce_wp_text_input( array(
						'id'          => '_measurement_dimension_length_label',
						'value'       => $settings['dimension']['length']['label'],
						'label'       => __( 'Length Label', 'woocommerce-measurement-price-calculator' ),
						'description' => __( 'Length input field label to display on the frontend', 'woocommerce-measurement-price-calculator' ),
						'desc_tip'    => true,
					) );
					woocommerce_wp_select( array(
						'id'          => '_measurement_dimension_length_unit',
						'value'       => $settings['dimension']['length']['unit'] ,
						'label'       => __( 'Length Unit', 'woocommerce-measurement-price-calculator' ),
						'options'     => $measurement_units['dimension'],
						'description' => __( 'The frontend length input field unit', 'woocommerce-measurement-price-calculator' ),
						'desc_tip'    => true,
					) );
					woocommerce_wp_checkbox( array(
						'id'          => '_measurement_dimension_length_editable',
						'value'       => $settings['dimension']['length']['editable'],
						'label'       => __( 'Length Editable', 'woocommerce-measurement-price-calculator' ),
						'class'       => 'checkbox _measurement_editable',
						'description' => __( 'Check this box to allow the needed length to be entered by the customer', 'woocommerce-measurement-price-calculator' ),
					) );
					wcfm_measurement_price_calculator_attributes_inputs( array(
						'measurement'   => 'dimension',
						'input_name'    => 'length',
						'input_label'   => __( 'Length', 'woocommerce-measurement-price-calculator' ),
						'settings'      => $settings,
						'limited_field' => '_measurement_dimension_length_options',
					) );
					woocommerce_wp_text_input( array(
						'id'            => '_measurement_dimension_length_options',
						'value'         => wcfm_measurement_price_calculator_get_options_value( $settings['dimension']['length']['options'] ),
						'wrapper_class' => '_measurement_pricing_calculator_fields',
						'label'         => __( 'Length Options', 'woocommerce-measurement-price-calculator' ),
						'description'   => wcfm_measurement_price_calculator_get_options_tooltip(),
						'desc_tip'      => true,
					) );
				echo '</div>';
				echo '<hr/>';
	
				// Dimension - Width
				wcfm_measurement_price_calculator_wp_radio( array(
					'name'        => '_measurement_dimension',
					'id'          => '_measurement_dimension_width',
					'rbvalue'     => 'width',
					'value'       => 'yes' == $settings['dimension']['width']['enabled'] ? 'width' : '',
					'class'       => 'checkbox _measurement_dimension',
					'label'       => __( 'Width', 'woocommerce-measurement-price-calculator' ),
					'description' => __( 'Select to display the product width in the price calculator', 'woocommerce-measurement-price-calculator' ),
				) );
				echo '<div id="_measurement_dimension_width_fields" style="display:none;">';
					woocommerce_wp_text_input( array(
						'id'          => '_measurement_dimension_width_label',
						'value'       => $settings['dimension']['width']['label'],
						'label'       => __( 'Width Label', 'woocommerce-measurement-price-calculator' ),
						'description' => __( 'Width input field label to display on the frontend', 'woocommerce-measurement-price-calculator' ),
						'desc_tip'    => true,
					) );
					woocommerce_wp_select( array(
						'id'          => '_measurement_dimension_width_unit',
						'value'       => $settings['dimension']['width']['unit'],
						'label'       => __( 'Width Unit', 'woocommerce-measurement-price-calculator' ),
						'options'     => $measurement_units['dimension'],
						'description' => __( 'The frontend width input field unit', 'woocommerce-measurement-price-calculator' ),
						'desc_tip'    => true,
					) );
					woocommerce_wp_checkbox( array(
						'id'          => '_measurement_dimension_width_editable',
						'value'       => $settings['dimension']['width']['editable'],
						'label'       => __( 'Width Editable', 'woocommerce-measurement-price-calculator' ),
						'class'       => 'checkbox _measurement_editable',
						'description' => __( 'Check this box to allow the needed width to be entered by the customer', 'woocommerce-measurement-price-calculator' ),
					) );
					wcfm_measurement_price_calculator_attributes_inputs( array(
						'measurement'   => 'dimension',
						'input_name'    => 'width',
						'input_label'   => __( 'Width', 'woocommerce-measurement-price-calculator' ),
						'settings'      => $settings,
						'limited_field' => '_measurement_dimension_width_options',
					) );
					woocommerce_wp_text_input( array(
						'id'            => '_measurement_dimension_width_options',
						'value'         => wcfm_measurement_price_calculator_get_options_value( $settings['dimension']['width']['options'] ),
						'wrapper_class' => '_measurement_pricing_calculator_fields',
						'label'         => __( 'Width Options', 'woocommerce-measurement-price-calculator' ),
						'description'   => wcfm_measurement_price_calculator_get_options_tooltip(),
						'desc_tip'      => true,
					) );
				echo '</div>';
				echo '<hr/>';
	
				// Dimension - Height
				wcfm_measurement_price_calculator_wp_radio( array(
					'name'        => '_measurement_dimension',
					'id'          => '_measurement_dimension_height',
					'rbvalue'     => 'height',
					'value'       => 'yes' == $settings['dimension']['height']['enabled'] ? 'height' : '',
					'class'       => 'checkbox _measurement_dimension',
					'label'       => __( 'Height', 'woocommerce-measurement-price-calculator' ),
					'description' => __( 'Select to display the product height in the price calculator', 'woocommerce-measurement-price-calculator' ),
				) );
				echo '<div id="_measurement_dimension_height_fields" style="display:none;">';
					woocommerce_wp_text_input( array(
						'id'          => '_measurement_dimension_height_label',
						'value'       => $settings['dimension']['height']['label'],
						'label'       => __( 'Height Label', 'woocommerce-measurement-price-calculator' ),
						'description' => __( 'Height input field label to display on the frontend', 'woocommerce-measurement-price-calculator' ),
						'desc_tip'    => true,
					) );
					woocommerce_wp_select( array(
						'id'          => '_measurement_dimension_height_unit',
						'value'       => $settings['dimension']['height']['unit'],
						'label'       => __( 'Height Unit', 'woocommerce-measurement-price-calculator' ),
						'options'     => $measurement_units['dimension'],
						'description' => __( 'The frontend height input field unit', 'woocommerce-measurement-price-calculator' ),
						'desc_tip'    => true,
					) );
					woocommerce_wp_checkbox( array(
						'id'          => '_measurement_dimension_height_editable',
						'value'       => $settings['dimension']['height']['editable'],
						'label'       => __( 'Height Editable', 'woocommerce-measurement-price-calculator' ),
						'class'       => 'checkbox _measurement_editable',
						'description' => __( 'Check this box to allow the needed height to be entered by the customer', 'woocommerce-measurement-price-calculator' ),
					) );
					wcfm_measurement_price_calculator_attributes_inputs( array(
						'measurement'   => 'dimension',
						'input_name'    => 'height',
						'input_label'   => __( 'Height', 'woocommerce-measurement-price-calculator' ),
						'settings'      => $settings,
						'limited_field' => '_measurement_dimension_height_options',
					) );
					woocommerce_wp_text_input( array(
						'id'            => '_measurement_dimension_height_options',
						'value'         => wcfm_measurement_price_calculator_get_options_value( $settings['dimension']['height']['options'] ),
						'wrapper_class' => '_measurement_pricing_calculator_fields',
						'label'         => __( 'Height Options', 'woocommerce-measurement-price-calculator' ),
						'description'   => wcfm_measurement_price_calculator_get_options_tooltip(),
						'desc_tip'      => true,
					) );
				echo '</div>';
			echo '</div>';
	
			// Area
			echo '<div id="area_measurements" class="measurement_fields">';
				woocommerce_wp_checkbox( array(
					'id'            => '_measurement_area_pricing',
					'value'         => $settings['area']['pricing']['enabled'],
					'class'         => 'checkbox _measurement_pricing',
					'label'         => __( 'Show Product Price Per Unit', 'woocommerce-measurement-price-calculator' ),
					'description'   => __( 'Check this box to display product pricing per unit on the frontend', 'woocommerce-measurement-price-calculator' )
				) );
				echo '<div id="_measurement_area_pricing_fields" class="_measurement_pricing_fields" style="display:none;">';
					woocommerce_wp_text_input( array(
						'id'          => '_measurement_area_pricing_label',
						'value'       => $settings['area']['pricing']['label'],
						'label'       => __( 'Pricing Label', 'woocommerce-measurement-price-calculator' ),
						'description' => __( 'Label to display next to the product price (defaults to pricing unit)', 'woocommerce-measurement-price-calculator' ),
						'desc_tip'    => true,
					) );
					woocommerce_wp_select( array(
						'id'          => '_measurement_area_pricing_unit',
						'value'       => $settings['area']['pricing']['unit'],
						'class'       => '_measurement_pricing_unit',
						'label'       => __( 'Pricing Unit', 'woocommerce-measurement-price-calculator' ),
						'options'     => $measurement_units['area'],
						'description' => __( 'Unit to define pricing in', 'woocommerce-measurement-price-calculator' ),
						'desc_tip'    => true,
					) );
					woocommerce_wp_checkbox( array(
						'id'            => '_measurement_area_pricing_calculator_enabled',
						'class'         => 'checkbox _measurement_pricing_calculator_enabled',
						'value'         => $settings['area']['pricing']['calculator']['enabled'],
						'label'         => __( 'Calculated Price', 'woocommerce-measurement-price-calculator' ),
						'description'   => __( 'Check this box to define product pricing per unit and allow customers to provide custom measurements', 'woocommerce-measurement-price-calculator' ),
					) );
					woocommerce_wp_checkbox( array(
						'id'            => '_measurement_area_pricing_weight_enabled',
						'value'         => $settings['area']['pricing']['weight']['enabled'],
						'class'         => 'checkbox _measurement_pricing_weight_enabled',
						'wrapper_class' => $pricing_weight_wrapper_class . ' _measurement_pricing_calculator_fields',
						'label'         => __( 'Calculated Weight', 'woocommerce-measurement-price-calculator' ),
						'description'   => __( 'Check this box to define the product weight per unit and calculate the item weight based on the product area', 'woocommerce-measurement-price-calculator' ),
					) );
					woocommerce_wp_checkbox( array(
						'id'            => '_measurement_area_pricing_inventory_enabled',
						'value'         => $settings['area']['pricing']['inventory']['enabled'],
						'class'         => 'checkbox _measurement_pricing_inventory_enabled',
						'wrapper_class' => 'stock_fields _measurement_pricing_calculator_fields',
						'label'         => __( 'Calculated Inventory', 'woocommerce-measurement-price-calculator' ),
						'description'   => __( 'Check this box to define inventory per unit and calculate inventory based on the product area', 'woocommerce-measurement-price-calculator' ),
					) );
					wcfm_measurement_price_calculator_overage_input( 'area', $settings );
				echo '</div>';
				echo '<hr/>';
				woocommerce_wp_text_input( array(
					'id'          => '_measurement_area_label',
					'value'       => $settings['area']['area']['label'],
					'label'       => __( 'Area Label', 'woocommerce-measurement-price-calculator' ),
					'description' => __( 'Area input field label to display on the frontend', 'woocommerce-measurement-price-calculator' ),
					'desc_tip'    => true,
				) );
				woocommerce_wp_select( array(
					'id'          => '_measurement_area_unit',
					'value'       => $settings['area']['area']['unit'],
					'label'       => __( 'Area Unit', 'woocommerce-measurement-price-calculator' ),
					'options'     => $measurement_units['area'],
					'description' => __( 'The frontend area input field unit', 'woocommerce-measurement-price-calculator' ),
					'desc_tip'    => true,
				) );
				woocommerce_wp_checkbox( array(
					'id'          => '_measurement_area_editable',
					'value'       => $settings['area']['area']['editable'],
					'label'       => __( 'Editable', 'woocommerce-measurement-price-calculator' ),
					'class'       => 'checkbox _measurement_editable',
					'description' => __( 'Check this box to allow the needed measurement to be entered by the customer', 'woocommerce-measurement-price-calculator' ),
				) );
				wcfm_measurement_price_calculator_attributes_inputs( array(
					'measurement'   => 'area',
					'input_name'    => 'area',
					'input_label'   => __( 'Area', 'woocommerce-measurement-price-calculator' ),
					'settings'      => $settings,
					'limited_field' => '_measurement_area_options',
				) );
				woocommerce_wp_text_input( array(
					'id'            => '_measurement_area_options',
					'value'         => wcfm_measurement_price_calculator_get_options_value( $settings['area']['area']['options'] ),
					'wrapper_class' => '_measurement_pricing_calculator_fields',
					'label'         => __( 'Area Options', 'woocommerce-measurement-price-calculator' ),
					'description'   => wcfm_measurement_price_calculator_get_options_tooltip(),
					'desc_tip'      => true,
				) );
			echo '</div>';
	
			// Area (LxW)
			echo '<div id="area-dimension_measurements" class="measurement_fields">';
				woocommerce_wp_checkbox( array(
					'id'            => '_measurement_area-dimension_pricing',
					'value'         => $settings['area-dimension']['pricing']['enabled'],
					'class'         => 'checkbox _measurement_pricing',
					'label'         => __( 'Show Product Price Per Unit', 'woocommerce-measurement-price-calculator' ),
					'description'   => __( 'Check this box to display product pricing per unit on the frontend', 'woocommerce-measurement-price-calculator' ),
				) );
				echo '<div id="_measurement_area-dimension_pricing_fields" class="_measurement_pricing_fields" style="display:none;">';
					woocommerce_wp_text_input( array(
						'id'          => '_measurement_area-dimension_pricing_label',
						'value'       => $settings['area-dimension']['pricing']['label'],
						'label'       => __( 'Pricing Label', 'woocommerce-measurement-price-calculator' ),
						'description' => __( 'Label to display next to the product price (defaults to pricing unit)', 'woocommerce-measurement-price-calculator' ),
						'desc_tip'    => true,
					) );
					woocommerce_wp_select( array(
						'id'          => '_measurement_area-dimension_pricing_unit',
						'value'       => $settings['area-dimension']['pricing']['unit'],
						'class'       => '_measurement_pricing_unit',
						'label'       => __( 'Pricing Unit', 'woocommerce-measurement-price-calculator' ),
						'options'     => $measurement_units['area'],
						'description' => __( 'Unit to define pricing in', 'woocommerce-measurement-price-calculator' ),
						'desc_tip'    => true,
					) );
					woocommerce_wp_checkbox( array(
						'id'            => '_measurement_area-dimension_pricing_calculator_enabled',
						'class'         => 'checkbox _measurement_pricing_calculator_enabled',
						'value'         => $settings['area-dimension']['pricing']['calculator']['enabled'],
						'label'         => __( 'Calculated Price', 'woocommerce-measurement-price-calculator' ),
						'description'   => __( 'Check this box to define product pricing per unit and allow customers to provide custom measurements', 'woocommerce-measurement-price-calculator' ),
					) );
					woocommerce_wp_checkbox( array(
						'id'            => '_measurement_area-dimension_pricing_weight_enabled',
						'value'         => $settings['area-dimension']['pricing']['weight']['enabled'],
						'class'         => 'checkbox _measurement_pricing_weight_enabled',
						'wrapper_class' => $pricing_weight_wrapper_class . ' _measurement_pricing_calculator_fields',
						'label'         => __( 'Calculated Weight', 'woocommerce-measurement-price-calculator' ),
						'description'   => __( 'Check this box to define the product weight per unit and calculate the item weight based on the product area', 'woocommerce-measurement-price-calculator' ),
					) );
					woocommerce_wp_checkbox( array(
						'id'            => '_measurement_area-dimension_pricing_inventory_enabled',
						'value'         => $settings['area-dimension']['pricing']['inventory']['enabled'],
						'class'         => 'checkbox _measurement_pricing_inventory_enabled',
						'wrapper_class' => 'stock_fields _measurement_pricing_calculator_fields',
						'label'         => __( 'Calculated Inventory', 'woocommerce-measurement-price-calculator' ),
						'description'   => __( 'Check this box to define inventory per unit and calculate inventory based on the product area', 'woocommerce-measurement-price-calculator' ),
					) );
					wcfm_measurement_price_calculator_overage_input( 'area-dimension', $settings );
				echo '</div>';
				echo '<hr/>';
				woocommerce_wp_text_input( array(
					'id'          => '_measurement_area_length_label',
					'value'       => $settings['area-dimension']['length']['label'],
					'label'       => __( 'Length Label', 'woocommerce-measurement-price-calculator' ),
					'description' => __( 'Length input field label to display on the frontend', 'woocommerce-measurement-price-calculator' ),
					'desc_tip'    => true,
				) );
				woocommerce_wp_select( array(
					'id'          => '_measurement_area_length_unit',
					'value'       => $settings['area-dimension']['length']['unit'],
					'label'       => __( 'Length Unit', 'woocommerce-measurement-price-calculator' ),
					'options'     => $measurement_units['dimension'],
					'description' => __( 'The frontend length input field unit', 'woocommerce-measurement-price-calculator' ),
					'desc_tip'    => true,
				) );
				wcfm_measurement_price_calculator_attributes_inputs( array(
					'measurement'   => 'area-dimension',
					'input_name'    => 'length',
					'input_label'   => __( 'Length', 'woocommerce-measurement-price-calculator' ),
					'settings'      => $settings,
					'limited_field' => '_measurement_area_length_options',
				) );
				woocommerce_wp_text_input( array(
					'id'            => '_measurement_area_length_options',
					'value'         => wcfm_measurement_price_calculator_get_options_value( $settings['area-dimension']['length']['options'] ),
					'wrapper_class' => '_measurement_pricing_calculator_fields',
					'label'         => __( 'Length Options', 'woocommerce-measurement-price-calculator' ),
					'description'   => wcfm_measurement_price_calculator_get_options_tooltip(),
					'desc_tip'      => true,
				) );
				echo '<hr/>';
	
				woocommerce_wp_text_input( array(
					'id'          => '_measurement_area_width_label',
					'value'       => $settings['area-dimension']['width']['label'],
					'label'       => __( 'Width Label', 'woocommerce-measurement-price-calculator' ),
					'description' => __( 'Width input field label to display on the frontend', 'woocommerce-measurement-price-calculator' ),
					'desc_tip'    => true,
				) );
				woocommerce_wp_select( array(
					'id'          => '_measurement_area_width_unit',
					'value'       => $settings['area-dimension']['width']['unit'],
					'label'       => __( 'Width Unit', 'woocommerce-measurement-price-calculator' ),
					'options'     => $measurement_units['dimension'],
					'description' => __( 'The frontend width input field unit', 'woocommerce-measurement-price-calculator' ),
					'desc_tip'    => true,
				) );
				wcfm_measurement_price_calculator_attributes_inputs( array(
					'measurement'   => 'area-dimension',
					'input_name'    => 'width',
					'input_label'   => __( 'Width', 'woocommerce-measurement-price-calculator' ),
					'settings'      => $settings,
					'limited_field' => '_measurement_area_width_options',
				) );
				woocommerce_wp_text_input( array(
					'id'            => '_measurement_area_width_options',
					'value'         => wcfm_measurement_price_calculator_get_options_value( $settings['area-dimension']['width']['options'] ),
					'wrapper_class' => '_measurement_pricing_calculator_fields',
					'label'         => __( 'Width Options', 'woocommerce-measurement-price-calculator' ),
					'description'   => wcfm_measurement_price_calculator_get_options_tooltip(),
					'desc_tip'      => true,
				) );
			echo '</div>';
	
			// Perimeter (2 * L + 2 * W)
			echo '<div id="area-linear_measurements" class="measurement_fields">';
				woocommerce_wp_checkbox( array(
					'id'            => '_measurement_area-linear_pricing',
					'value'         => $settings['area-linear']['pricing']['enabled'],
					'class'         => 'checkbox _measurement_pricing',
					'label'         => __( 'Show Product Price Per Unit', 'woocommerce-measurement-price-calculator' ),
					'description'   => __( 'Check this box to display product pricing per unit on the frontend', 'woocommerce-measurement-price-calculator' ),
				) );
				echo '<div id="_measurement_area-linear_pricing_fields" class="_measurement_pricing_fields" style="display:none;">';
					woocommerce_wp_text_input( array(
						'id'          => '_measurement_area-linear_pricing_label',
						'value'       => $settings['area-linear']['pricing']['label'],
						'label'       => __( 'Pricing Label', 'woocommerce-measurement-price-calculator' ),
						'description' => __( 'Label to display next to the product price (defaults to pricing unit)', 'woocommerce-measurement-price-calculator' ),
						'desc_tip'    => true,
					) );
					woocommerce_wp_select( array(
						'id'          => '_measurement_area-linear_pricing_unit',
						'value'       => $settings['area-linear']['pricing']['unit'],
						'class'       => '_measurement_pricing_unit',
						'label'       => __( 'Pricing Unit', 'woocommerce-measurement-price-calculator' ),
						'options'     => $measurement_units['dimension'],
						'description' => __( 'Unit to define pricing in', 'woocommerce-measurement-price-calculator' ),
						'desc_tip'    => true,
					) );
					woocommerce_wp_checkbox( array(
						'id'            => '_measurement_area-linear_pricing_calculator_enabled',
						'class'         => 'checkbox _measurement_pricing_calculator_enabled',
						'value'         => $settings['area-linear']['pricing']['calculator']['enabled'],
						'label'         => __( 'Calculated Price', 'woocommerce-measurement-price-calculator' ),
						'description'   => __( 'Check this box to define product pricing per unit and allow customers to provide custom measurements', 'woocommerce-measurement-price-calculator' ),
					) );
					woocommerce_wp_checkbox( array(
						'id'            => '_measurement_area-linear_pricing_weight_enabled',
						'value'         => $settings['area-linear']['pricing']['weight']['enabled'],
						'class'         => 'checkbox _measurement_pricing_weight_enabled',
						'wrapper_class' => $pricing_weight_wrapper_class . ' _measurement_pricing_calculator_fields',
						'label'         => __( 'Calculated Weight', 'woocommerce-measurement-price-calculator' ),
						'description'   => __( 'Check this box to define the product weight per unit and calculate the item weight based on the product area', 'woocommerce-measurement-price-calculator' ),
					) );
					woocommerce_wp_checkbox( array(
						'id'            => '_measurement_area-linear_pricing_inventory_enabled',
						'value'         => $settings['area-linear']['pricing']['inventory']['enabled'],
						'class'         => 'checkbox _measurement_pricing_inventory_enabled',
						'wrapper_class' => 'stock_fields _measurement_pricing_calculator_fields',
						'label'         => __( 'Calculated Inventory', 'woocommerce-measurement-price-calculator' ),
						'description'   => __( 'Check this box to define inventory per unit and calculate inventory based on the product area', 'woocommerce-measurement-price-calculator' ),
					) );
					wcfm_measurement_price_calculator_overage_input( 'area-linear', $settings );
				echo '</div>';
				echo '<hr/>';
				woocommerce_wp_text_input( array(
					'id'          => '_measurement_area-linear_length_label',
					'value'       => $settings['area-linear']['length']['label'],
					'label'       => __( 'Length Label', 'woocommerce-measurement-price-calculator' ),
					'description' => __( 'Length input field label to display on the frontend', 'woocommerce-measurement-price-calculator' ),
					'desc_tip'    => true,
				) );
				woocommerce_wp_select( array(
					'id'          => '_measurement_area-linear_length_unit',
					'value'       => $settings['area-linear']['length']['unit'],
					'label'       => __( 'Length Unit', 'woocommerce-measurement-price-calculator' ),
					'options'     => $measurement_units['dimension'],
					'description' => __( 'The frontend length input field unit', 'woocommerce-measurement-price-calculator' ),
					'desc_tip'    => true,
				) );
				wcfm_measurement_price_calculator_attributes_inputs( array(
					'measurement'   => 'area-linear',
					'input_name'    => 'length',
					'input_label'   => __( 'Length', 'woocommerce-measurement-price-calculator' ),
					'settings'      => $settings,
					'limited_field' => '_measurement_area-linear_length_options',
				) );
				woocommerce_wp_text_input( array(
					'id'            => '_measurement_area-linear_length_options',
					'value'         => wcfm_measurement_price_calculator_get_options_value( $settings['area-linear']['length']['options'] ),
					'wrapper_class' => '_measurement_pricing_calculator_fields',
					'label'         => __( 'Length Options', 'woocommerce-measurement-price-calculator' ),
					'description'   => wcfm_measurement_price_calculator_get_options_tooltip(),
					'desc_tip'      => true,
				) );
				echo '<hr/>';
	
				woocommerce_wp_text_input( array(
					'id'          => '_measurement_area-linear_width_label',
					'value'       => $settings['area-linear']['width']['label'],
					'label'       => __( 'Width Label', 'woocommerce-measurement-price-calculator' ),
					'description' => __( 'Width input field label to display on the frontend', 'woocommerce-measurement-price-calculator' ),
					'desc_tip'    => true,
				) );
				woocommerce_wp_select( array(
					'id'          => '_measurement_area-linear_width_unit',
					'value'       => $settings['area-linear']['width']['unit'],
					'label'       => __( 'Width Unit', 'woocommerce-measurement-price-calculator' ),
					'options'     => $measurement_units['dimension'],
					'description' => __( 'The frontend width input field unit', 'woocommerce-measurement-price-calculator' ),
					'desc_tip'    => true,
				) );
				wcfm_measurement_price_calculator_attributes_inputs( array(
					'measurement'   => 'area-linear',
					'input_name'    => 'width',
					'input_label'   => __( 'Width', 'woocommerce-measurement-price-calculator' ),
					'settings'      => $settings,
					'limited_field' => '_measurement_area-linear_width_options',
				) );
				woocommerce_wp_text_input( array(
					'id'            => '_measurement_area-linear_width_options',
					'value'         => wcfm_measurement_price_calculator_get_options_value( $settings['area-linear']['width']['options'] ),
					'wrapper_class' => '_measurement_pricing_calculator_fields',
					'label'         => __( 'Width Options', 'woocommerce-measurement-price-calculator' ),
					'description'   => wcfm_measurement_price_calculator_get_options_tooltip(),
					'desc_tip'      => true,
				) );
			echo '</div>';
	
			// Surface Area 2 * (L * W + W * H + L * H)
			echo '<div id="area-surface_measurements" class="measurement_fields">';
				woocommerce_wp_checkbox( array(
					'id'            => '_measurement_area-surface_pricing',
					'value'         => $settings['area-surface']['pricing']['enabled'],
					'class'         => 'checkbox _measurement_pricing',
					'label'         => __( 'Show Product Price Per Unit', 'woocommerce-measurement-price-calculator' ),
					'description'   => __( 'Check this box to display product pricing per unit on the frontend', 'woocommerce-measurement-price-calculator' ),
				) );
				echo '<div id="_measurement_area-surface_pricing_fields" class="_measurement_pricing_fields" style="display:none;">';
					woocommerce_wp_text_input( array(
						'id'          => '_measurement_area-surface_pricing_label',
						'value'       => $settings['area-surface']['pricing']['label'],
						'label'       => __( 'Pricing Label', 'woocommerce-measurement-price-calculator' ),
						'description' => __( 'Label to display next to the product price (defaults to pricing unit)', 'woocommerce-measurement-price-calculator' ),
						'desc_tip'    => true,
					) );
					woocommerce_wp_select( array(
						'id'          => '_measurement_area-surface_pricing_unit',
						'value'       => $settings['area-surface']['pricing']['unit'],
						'class'       => '_measurement_pricing_unit',
						'label'       => __( 'Pricing Unit', 'woocommerce-measurement-price-calculator' ),
						'options'     => $measurement_units['area'],
						'description' => __( 'Unit to define pricing in', 'woocommerce-measurement-price-calculator' ),
						'desc_tip'    => true,
					) );
					woocommerce_wp_checkbox( array(
						'id'            => '_measurement_area-surface_pricing_calculator_enabled',
						'class'         => 'checkbox _measurement_pricing_calculator_enabled',
						'value'         => $settings['area-surface']['pricing']['calculator']['enabled'],
						'label'         => __( 'Calculated Price', 'woocommerce-measurement-price-calculator' ),
						'description'   => __( 'Check this box to define product pricing per unit and allow customers to provide custom measurements', 'woocommerce-measurement-price-calculator' ),
					) );
					woocommerce_wp_checkbox( array(
						'id'            => '_measurement_area-surface_pricing_weight_enabled',
						'value'         => $settings['area-surface']['pricing']['weight']['enabled'],
						'class'         => 'checkbox _measurement_pricing_weight_enabled',
						'wrapper_class' => $pricing_weight_wrapper_class . ' _measurement_pricing_calculator_fields',
						'label'         => __( 'Calculated Weight', 'woocommerce-measurement-price-calculator' ),
						'description'   => __( 'Check this box to define the product weight per unit and calculate the item weight based on the product area', 'woocommerce-measurement-price-calculator' ),
					) );
					woocommerce_wp_checkbox( array(
						'id'            => '_measurement_area-surface_pricing_inventory_enabled',
						'value'         => $settings['area-surface']['pricing']['inventory']['enabled'],
						'class'         => 'checkbox _measurement_pricing_inventory_enabled',
						'wrapper_class' => 'stock_fields _measurement_pricing_calculator_fields',
						'label'         => __( 'Calculated Inventory', 'woocommerce-measurement-price-calculator' ),
						'description'   => __( 'Check this box to define inventory per unit and calculate inventory based on the product area', 'woocommerce-measurement-price-calculator' ),
					) );
					wcfm_measurement_price_calculator_overage_input( 'area-surface', $settings );
				echo '</div>';
				echo '<hr/>';
				woocommerce_wp_text_input( array(
					'id'          => '_measurement_area-surface_length_label',
					'value'       => $settings['area-surface']['length']['label'],
					'label'       => __( 'Length Label', 'woocommerce-measurement-price-calculator' ),
					'description' => __( 'Length input field label to display on the frontend', 'woocommerce-measurement-price-calculator' ),
					'desc_tip'    => true,
				) );
				woocommerce_wp_select( array(
					'id'          => '_measurement_area-surface_length_unit',
					'value'       => $settings['area-surface']['length']['unit'],
					'label'       => __( 'Length Unit', 'woocommerce-measurement-price-calculator' ),
					'options'     => $measurement_units['dimension'],
					'description' => __( 'The frontend length input field unit', 'woocommerce-measurement-price-calculator' ),
					'desc_tip'    => true,
				) );
				wcfm_measurement_price_calculator_attributes_inputs( array(
					'measurement'   => 'area-surface',
					'input_name'    => 'length',
					'input_label'   => __( 'Length', 'woocommerce-measurement-price-calculator' ),
					'settings'      => $settings,
					'limited_field' => '_measurement_area-surface_length_options',
				) );
				woocommerce_wp_text_input( array(
					'id'            => '_measurement_area-surface_length_options',
					'value'         => wcfm_measurement_price_calculator_get_options_value( $settings['area-surface']['length']['options'] ),
					'wrapper_class' => '_measurement_pricing_calculator_fields',
					'label'         => __( 'Length Options', 'woocommerce-measurement-price-calculator' ),
					'description'   => wcfm_measurement_price_calculator_get_options_tooltip(),
					'desc_tip'      => true,
				) );
				echo '<hr/>';
	
				woocommerce_wp_text_input( array(
					'id'          => '_measurement_area-surface_width_label',
					'value'       => $settings['area-surface']['width']['label'],
					'label'       => __( 'Width Label', 'woocommerce-measurement-price-calculator' ),
					'description' => __( 'Width input field label to display on the frontend', 'woocommerce-measurement-price-calculator' ),
					'desc_tip'    => true,
				) );
				woocommerce_wp_select( array(
					'id'          => '_measurement_area-surface_width_unit',
					'value'       => $settings['area-surface']['width']['unit'],
					'label'       => __( 'Width Unit', 'woocommerce-measurement-price-calculator' ),
					'options'     => $measurement_units['dimension'],
					'description' => __( 'The frontend width input field unit', 'woocommerce-measurement-price-calculator' ),
					'desc_tip'    => true,
				) );
				wcfm_measurement_price_calculator_attributes_inputs( array(
					'measurement'   => 'area-surface',
					'input_name'    => 'width',
					'input_label'   => __( 'Width', 'woocommerce-measurement-price-calculator' ),
					'settings'      => $settings,
					'limited_field' => '_measurement_area-surface_width_options',
				) );
				woocommerce_wp_text_input( array(
					'id'            => '_measurement_area-surface_width_options',
					'value'         => wcfm_measurement_price_calculator_get_options_value( $settings['area-surface']['width']['options'] ),
					'wrapper_class' => '_measurement_pricing_calculator_fields',
					'label'         => __( 'Width Options', 'woocommerce-measurement-price-calculator' ),
					'description'   => wcfm_measurement_price_calculator_get_options_tooltip(),
					'desc_tip'      => true,
				) );
				echo '<hr/>';
	
				woocommerce_wp_text_input( array(
					'id'          => '_measurement_area-surface_height_label',
					'value'       => $settings['area-surface']['height']['label'],
					'label'       => __( 'Height Label', 'woocommerce-measurement-price-calculator' ),
					'description' => __( 'Height input field label to display on the frontend', 'woocommerce-measurement-price-calculator' ),
					'desc_tip'    => true,
				) );
				woocommerce_wp_select( array(
					'id'          => '_measurement_area-surface_height_unit',
					'value'       => $settings['area-surface']['height']['unit'],
					'label'       => __( 'Height Unit', 'woocommerce-measurement-price-calculator' ),
					'options'     => $measurement_units['dimension'],
					'description' => __( 'The frontend height input field unit', 'woocommerce-measurement-price-calculator' ),
					'desc_tip'    => true,
				) );
				wcfm_measurement_price_calculator_attributes_inputs( array(
					'measurement'   => 'area-surface',
					'input_name'    => 'height',
					'input_label'   => __( 'Height', 'woocommerce-measurement-price-calculator' ),
					'settings'      => $settings,
					'limited_field' => '_measurement_area-surface_height_options',
				) );
				woocommerce_wp_text_input( array(
					'id'            => '_measurement_area-surface_height_options',
					'value'         => wcfm_measurement_price_calculator_get_options_value( $settings['area-surface']['height']['options'] ),
					'wrapper_class' => '_measurement_pricing_calculator_fields',
					'label'         => __( 'Height Options', 'woocommerce-measurement-price-calculator' ),
					'description'   => wcfm_measurement_price_calculator_get_options_tooltip(),
					'desc_tip'      => true,
				) );
			echo '</div>';
	
			// Volume
			echo '<div id="volume_measurements" class="measurement_fields">';
				woocommerce_wp_checkbox( array(
					'id'            => '_measurement_volume_pricing',
					'value'         => $settings['volume']['pricing']['enabled'],
					'class'         => 'checkbox _measurement_pricing',
					'label'         => __( 'Show Product Price Per Unit', 'woocommerce-measurement-price-calculator' ),
					'description'   => __( 'Check this box to display product pricing per unit on the frontend', 'woocommerce-measurement-price-calculator' ),
				) );
				echo '<div id="_measurement_volume_pricing_fields" class="_measurement_pricing_fields" style="display:none;">';
					woocommerce_wp_text_input( array(
						'id'          => '_measurement_volume_pricing_label',
						'value'       => $settings['volume']['pricing']['label'],
						'label'       => __( 'Pricing Label', 'woocommerce-measurement-price-calculator' ),
						'description' => __( 'Label to display next to the product price (defaults to pricing unit)', 'woocommerce-measurement-price-calculator' ),
						'desc_tip'    => true,
					) );
					woocommerce_wp_select( array(
						'id'          => '_measurement_volume_pricing_unit',
						'value'       => $settings['volume']['pricing']['unit'],
						'class'       => '_measurement_pricing_unit',
						'label'       => __( 'Pricing Unit', 'woocommerce-measurement-price-calculator' ),
						'options'     => $measurement_units['volume'],
						'description' => __( 'Unit to define pricing in', 'woocommerce-measurement-price-calculator' ),
						'desc_tip'    => true,
					) );
					woocommerce_wp_checkbox( array(
						'id'            => '_measurement_volume_pricing_calculator_enabled',
						'class'         => 'checkbox _measurement_pricing_calculator_enabled',
						'value'         => $settings['volume']['pricing']['calculator']['enabled'],
						'label'         => __( 'Calculated Price', 'woocommerce-measurement-price-calculator' ),
						'description'   => __( 'Check this box to define product pricing per unit and allow customers to provide custom measurements', 'woocommerce-measurement-price-calculator' ),
					) );
					woocommerce_wp_checkbox( array(
						'id'            => '_measurement_volume_pricing_weight_enabled',
						'value'         => $settings['volume']['pricing']['weight']['enabled'],
						'class'         => 'checkbox _measurement_pricing_weight_enabled',
						'wrapper_class' => $pricing_weight_wrapper_class . ' _measurement_pricing_calculator_fields',
						'label'         => __( 'Calculated Weight', 'woocommerce-measurement-price-calculator' ),
						'description'   => __( 'Check this box to define the product weight per unit and calculate the item weight based on the product volume', 'woocommerce-measurement-price-calculator' ),
					) );
					woocommerce_wp_checkbox( array(
						'id'            => '_measurement_volume_pricing_inventory_enabled',
						'value'         => $settings['volume']['pricing']['inventory']['enabled'],
						'class'         => 'checkbox _measurement_pricing_inventory_enabled',
						'wrapper_class' => 'stock_fields _measurement_pricing_calculator_fields',
						'label'         => __( 'Calculated Inventory', 'woocommerce-measurement-price-calculator' ),
						'description'   => __( 'Check this box to define inventory per unit and calculate inventory based on the product volume', 'woocommerce-measurement-price-calculator' ),
					) );
					wcfm_measurement_price_calculator_overage_input( 'volume', $settings );
				echo '</div>';
				echo '<hr/>';
				woocommerce_wp_text_input( array(
					'id'          => '_measurement_volume_label',
					'value'       => $settings['volume']['volume']['label'],
					'label'       => __( 'Volume Label', 'woocommerce-measurement-price-calculator' ),
					'description' => __( 'Volume input field label to display on the frontend', 'woocommerce-measurement-price-calculator' ),
					'desc_tip'    => true,
				) );
				woocommerce_wp_select( array(
					'id'          => '_measurement_volume_unit',
					'value'       => $settings['volume']['volume']['unit'],
					'label'       => __( 'Volume Unit', 'woocommerce-measurement-price-calculator' ),
					'options'     => $measurement_units['volume'],
					'description' => __( 'The frontend volume input field unit', 'woocommerce-measurement-price-calculator' ),
					'desc_tip'    => true,
				) );
				woocommerce_wp_checkbox( array(
					'id'          => '_measurement_volume_editable',
					'value'       => $settings['volume']['volume']['editable'],
					'label'       => __( 'Editable', 'woocommerce-measurement-price-calculator' ),
					'class'       => 'checkbox _measurement_editable',
					'description' => __( 'Check this box to allow the needed measurement to be entered by the customer', 'woocommerce-measurement-price-calculator' ),
				) );
				wcfm_measurement_price_calculator_attributes_inputs( array(
					'measurement'   => 'volume',
					'input_name'    => 'volume',
					'input_label'   => __( 'Volume', 'woocommerce-measurement-price-calculator' ),
					'settings'      => $settings,
					'limited_field' => '_measurement_volume_options',
				) );
				woocommerce_wp_text_input( array(
					'id'            => '_measurement_volume_options',
					'value'         => wcfm_measurement_price_calculator_get_options_value( $settings['volume']['volume']['options'] ),
					'wrapper_class' => '_measurement_pricing_calculator_fields',
					'label'         => __( 'Volume Options', 'woocommerce-measurement-price-calculator' ),
					'description'   => wcfm_measurement_price_calculator_get_options_tooltip(),
					'desc_tip'      => true,
				) );
			echo '</div>';
	
			// Volume (LxWxH)
			echo '<div id="volume-dimension_measurements" class="measurement_fields">';
				woocommerce_wp_checkbox( array(
					'id'            => '_measurement_volume-dimension_pricing',
					'value'         => $settings['volume-dimension']['pricing']['enabled'],
					'class'         => 'checkbox _measurement_pricing',
					'label'         => __( 'Show Product Price Per Unit', 'woocommerce-measurement-price-calculator' ),
					'description'   => __( 'Check this box to display product pricing per unit on the frontend', 'woocommerce-measurement-price-calculator' ),
				) );
				echo '<div id="_measurement_volume-dimension_pricing_fields" class="_measurement_pricing_fields" style="display:none;">';
					woocommerce_wp_text_input( array(
						'id'          => '_measurement_volume-dimension_pricing_label',
						'value'       => $settings['volume-dimension']['pricing']['label'],
						'label'       => __( 'Pricing Label', 'woocommerce-measurement-price-calculator' ),
						'description' => __( 'Label to display next to the product price (defaults to pricing unit)', 'woocommerce-measurement-price-calculator' ),
						'desc_tip'    => true,
					) );
					woocommerce_wp_select( array(
						'id'          => '_measurement_volume-dimension_pricing_unit',
						'value'       => $settings['volume-dimension']['pricing']['unit'],
						'class'       => '_measurement_pricing_unit',
						'label'       => __( 'Pricing Unit', 'woocommerce-measurement-price-calculator' ),
						'options'     => $measurement_units['volume'],
						'description' => __( 'Unit to define pricing in', 'woocommerce-measurement-price-calculator' ),
						'desc_tip'    => true,
					) );
					woocommerce_wp_checkbox( array(
						'id'            => '_measurement_volume-dimension_pricing_calculator_enabled',
						'class'         => 'checkbox _measurement_pricing_calculator_enabled',
						'value'         => $settings['volume-dimension']['pricing']['calculator']['enabled'],
						'label'         => __( 'Calculated Price', 'woocommerce-measurement-price-calculator' ),
						'description'   => __( 'Check this box to define product pricing per unit and allow customers to provide custom measurements', 'woocommerce-measurement-price-calculator' ),
					) );
					woocommerce_wp_checkbox( array(
						'id'            => '_measurement_volume-dimension_pricing_weight_enabled',
						'value'         => $settings['volume-dimension']['pricing']['weight']['enabled'],
						'class'         => 'checkbox _measurement_pricing_weight_enabled',
						'wrapper_class' => $pricing_weight_wrapper_class . ' _measurement_pricing_calculator_fields',
						'label'         => __( 'Calculated Weight', 'woocommerce-measurement-price-calculator' ),
						'description'   => __( 'Check this box to define the product weight per unit and calculate the item weight based on the product volume', 'woocommerce-measurement-price-calculator' ),
					) );
					woocommerce_wp_checkbox( array(
						'id'            => '_measurement_volume-dimension_pricing_inventory_enabled',
						'value'         => $settings['volume-dimension']['pricing']['inventory']['enabled'],
						'class'         => 'checkbox _measurement_pricing_inventory_enabled',
						'wrapper_class' => 'stock_fields _measurement_pricing_calculator_fields',
						'label'         => __( 'Calculated Inventory', 'woocommerce-measurement-price-calculator' ),
						'description'   => __( 'Check this box to define inventory per unit and calculate inventory based on the product volume', 'woocommerce-measurement-price-calculator' ),
					) );
					wcfm_measurement_price_calculator_overage_input( 'volume-dimension', $settings );
				echo '</div>';
				echo '<hr/>';
				woocommerce_wp_text_input( array(
					'id'          => '_measurement_volume_length_label',
					'value'       => $settings['volume-dimension']['length']['label'],
					'label'       => __( 'Length Label', 'woocommerce-measurement-price-calculator' ),
					'description' => __( 'Length input field label to display on the frontend', 'woocommerce-measurement-price-calculator' ),
					'desc_tip'    => true,
				) );
				woocommerce_wp_select( array(
					'id'          => '_measurement_volume_length_unit',
					'value'       => $settings['volume-dimension']['length']['unit'],
					'label'       => __( 'Length Unit', 'woocommerce-measurement-price-calculator' ),
					'options'     => $measurement_units['dimension'],
					'description' => __( 'The frontend length input field unit', 'woocommerce-measurement-price-calculator' ),
					'desc_tip'    => true,
				) );
				wcfm_measurement_price_calculator_attributes_inputs( array(
					'measurement'   => 'volume-dimension',
					'input_name'    => 'length',
					'input_label'   => __( 'Length', 'woocommerce-measurement-price-calculator' ),
					'settings'      => $settings,
					'limited_field' => '_measurement_volume_length_options',
				) );
				woocommerce_wp_text_input( array(
					'id'            => '_measurement_volume_length_options',
					'value'         => wcfm_measurement_price_calculator_get_options_value( $settings['volume-dimension']['length']['options'] ),
					'wrapper_class' => '_measurement_pricing_calculator_fields',
					'label'         => __( 'Length Options', 'woocommerce-measurement-price-calculator' ),
					'description'   => wcfm_measurement_price_calculator_get_options_tooltip(),
					'desc_tip'      => true,
				) );
				echo '<hr/>';
	
				woocommerce_wp_text_input( array(
					'id'          => '_measurement_volume_width_label',
					'value'       => $settings['volume-dimension']['width']['label'],
					'label'       => __( 'Width Label', 'woocommerce-measurement-price-calculator' ),
					'description' => __( 'Width input field label to display on the frontend', 'woocommerce-measurement-price-calculator' ),
					'desc_tip'    => true,
				) );
				woocommerce_wp_select( array(
					'id'          => '_measurement_volume_width_unit',
					'value'       => $settings['volume-dimension']['width']['unit'],
					'label'       => __( 'Width Unit', 'woocommerce-measurement-price-calculator' ),
					'options'     => $measurement_units['dimension'],
					'description' => __( 'The frontend width input field unit', 'woocommerce-measurement-price-calculator' ),
					'desc_tip'    => true,
				) );
				wcfm_measurement_price_calculator_attributes_inputs( array(
					'measurement'   => 'volume-dimension',
					'input_name'    => 'width',
					'input_label'   => __( 'Width', 'woocommerce-measurement-price-calculator' ),
					'settings'      => $settings,
					'limited_field' => '_measurement_volume_width_options',
				) );
				woocommerce_wp_text_input( array(
					'id'            => '_measurement_volume_width_options',
					'value'         => wcfm_measurement_price_calculator_get_options_value( $settings['volume-dimension']['width']['options'] ),
					'wrapper_class' => '_measurement_pricing_calculator_fields',
					'label'         => __( 'Width Options', 'woocommerce-measurement-price-calculator' ),
					'description'   => wcfm_measurement_price_calculator_get_options_tooltip(),
					'desc_tip'      => true,
				) );
				echo '<hr/>';
	
				woocommerce_wp_text_input( array(
					'id'          => '_measurement_volume_height_label',
					'value'       => $settings['volume-dimension']['height']['label'],
					'label'       => __( 'Height Label', 'woocommerce-measurement-price-calculator' ),
					'description' => __( 'Height input field label to display on the frontend', 'woocommerce-measurement-price-calculator' ),
					'desc_tip'    => true,
				) );
				woocommerce_wp_select( array(
					'id'          => '_measurement_volume_height_unit',
					'value'       => $settings['volume-dimension']['height']['unit'],
					'label'       => __( 'Height Unit', 'woocommerce-measurement-price-calculator' ),
					'options'     => $measurement_units['dimension'],
					'description' => __( 'The frontend height input field unit', 'woocommerce-measurement-price-calculator' ),
					'desc_tip'    => true,
				) );
				wcfm_measurement_price_calculator_attributes_inputs( array(
					'measurement'   => 'volume-dimension',
					'input_name'    => 'height',
					'input_label'   => __( 'Height', 'woocommerce-measurement-price-calculator' ),
					'settings'      => $settings,
					'limited_field' => '_measurement_volume_height_options',
				) );
				woocommerce_wp_text_input( array(
					'id'            => '_measurement_volume_height_options',
					'value'         => wcfm_measurement_price_calculator_get_options_value( $settings['volume-dimension']['height']['options'] ),
					'wrapper_class' => '_measurement_pricing_calculator_fields',
					'label'         => __( 'Height Options', 'woocommerce-measurement-price-calculator' ),
					'description'   => wcfm_measurement_price_calculator_get_options_tooltip(),
					'desc_tip'      => true,
				) );
			echo '</div>';
	
			// Volume (AxH)
			echo '<div id="volume-area_measurements" class="measurement_fields">';
				woocommerce_wp_checkbox( array(
					'id'            => '_measurement_volume-area_pricing',
					'value'         => $settings['volume-area']['pricing']['enabled'],
					'class'         => 'checkbox _measurement_pricing',
					'label'         => __( 'Show Product Price Per Unit', 'woocommerce-measurement-price-calculator' ),
					'description'   => __( 'Check this box to display product pricing per unit on the frontend', 'woocommerce-measurement-price-calculator' ),
				) );
				echo '<div id="_measurement_volume-area_pricing_fields" class="_measurement_pricing_fields" style="display:none;">';
					woocommerce_wp_text_input( array(
						'id'          => '_measurement_volume-area_pricing_label',
						'value'       => $settings['volume-area']['pricing']['label'],
						'label'       => __( 'Pricing Label', 'woocommerce-measurement-price-calculator' ),
						'description' => __( 'Label to display next to the product price (defaults to pricing unit)', 'woocommerce-measurement-price-calculator' ),
						'desc_tip'    => true,
					) );
					woocommerce_wp_select( array(
						'id'          => '_measurement_volume-area_pricing_unit',
						'value'       => $settings['volume-area']['pricing']['unit'],
						'class'       => '_measurement_pricing_unit',
						'label'       => __( 'Pricing Unit', 'woocommerce-measurement-price-calculator' ),
						'options'     => $measurement_units['volume'],
						'description' => __( 'Unit to define pricing in', 'woocommerce-measurement-price-calculator' ),
						'desc_tip'    => true,
					) );
					woocommerce_wp_checkbox( array(
						'id'            => '_measurement_volume-area_pricing_calculator_enabled',
						'class'         => 'checkbox _measurement_pricing_calculator_enabled',
						'value'         => $settings['volume-area']['pricing']['calculator']['enabled'],
						'label'         => __( 'Calculated Price', 'woocommerce-measurement-price-calculator' ),
						'description'   => __( 'Check this box to define product pricing per unit and allow customers to provide custom measurements', 'woocommerce-measurement-price-calculator' ),
					) );
					woocommerce_wp_checkbox( array(
						'id'            => '_measurement_volume-area_pricing_weight_enabled',
						'value'         => $settings['volume-area']['pricing']['weight']['enabled'],
						'class'         => 'checkbox _measurement_pricing_weight_enabled',
						'wrapper_class' => $pricing_weight_wrapper_class . ' _measurement_pricing_calculator_fields',
						'label'         => __( 'Calculated Weight', 'woocommerce-measurement-price-calculator' ),
						'description'   => __( 'Check this box to define the product weight per unit and calculate the item weight based on the product volume', 'woocommerce-measurement-price-calculator' ),
					) );
					woocommerce_wp_checkbox( array(
						'id'            => '_measurement_volume-area_pricing_inventory_enabled',
						'value'         => $settings['volume-area']['pricing']['inventory']['enabled'],
						'class'         => 'checkbox _measurement_pricing_inventory_enabled',
						'wrapper_class' => 'stock_fields _measurement_pricing_calculator_fields',
						'label'         => __( 'Calculated Inventory', 'woocommerce-measurement-price-calculator' ),
						'description'   => __( 'Check this box to define inventory per unit and calculate inventory based on the product volume', 'woocommerce-measurement-price-calculator' ),
					) );
					wcfm_measurement_price_calculator_overage_input( 'volume-area', $settings );
				echo '</div>';
				echo '<hr/>';
				woocommerce_wp_text_input( array(
					'id'          => '_measurement_volume_area_label',
					'value'       => $settings['volume-area']['area']['label'],
					'label'       => __( 'Area Label', 'woocommerce-measurement-price-calculator' ),
					'description' => __( 'Area input field label to display on the frontend', 'woocommerce-measurement-price-calculator' ),
					'desc_tip'    => true,
				) );
				woocommerce_wp_select( array(
					'id'          => '_measurement_volume_area_unit',
					'value'       => $settings['volume-area']['area']['unit'],
					'label'       => __( 'Area Unit', 'woocommerce-measurement-price-calculator' ),
					'options'     => $measurement_units['area'],
					'description' => __( 'The frontend area input field unit', 'woocommerce-measurement-price-calculator' ),
					'desc_tip'    => true,
				) );
				wcfm_measurement_price_calculator_attributes_inputs( array(
					'measurement'   => 'volume-area',
					'input_name'    => 'area',
					'input_label'   => __( 'Area', 'woocommerce-measurement-price-calculator' ),
					'settings'      => $settings,
					'limited_field' => '_measurement_volume_area_options',
				) );
				woocommerce_wp_text_input( array(
					'id'            => '_measurement_volume_area_options',
					'value'         => wcfm_measurement_price_calculator_get_options_value( $settings['volume-area']['area']['options'] ),
					'wrapper_class' => '_measurement_pricing_calculator_fields',
					'label'         => __( 'Area Options', 'woocommerce-measurement-price-calculator' ),
					'description'   => wcfm_measurement_price_calculator_get_options_tooltip(),
					'desc_tip'      => true,
				) );
				echo '<hr/>';
	
				woocommerce_wp_text_input( array(
					'id'          => '_measurement_volume_area_height_label',
					'value'       => $settings['volume-area']['height']['label'],
					'label'       => __( 'Height Label', 'woocommerce-measurement-price-calculator' ),
					'description' => __( 'Height input field label to display on the frontend', 'woocommerce-measurement-price-calculator' ),
					'desc_tip'    => true,
				) );
				woocommerce_wp_select( array(
					'id'          => '_measurement_volume_area_height_unit',
					'value'       => $settings['volume-area']['height']['unit'],
					'label'       => __( 'Height Unit', 'woocommerce-measurement-price-calculator' ),
					'options'     => $measurement_units['dimension'],
					'description' => __( 'The frontend height input field unit', 'woocommerce-measurement-price-calculator' ),
					'desc_tip'    => true,
				) );
				wcfm_measurement_price_calculator_attributes_inputs( array(
					'measurement'   => 'volume-area',
					'input_name'    => 'height',
					'input_label'   => __( 'Height', 'woocommerce-measurement-price-calculator' ),
					'settings'      => $settings,
					'limited_field' => '_measurement_volume_area_height_options',
				) );
				woocommerce_wp_text_input( array(
					'id'            => '_measurement_volume_area_height_options',
					'value'         => wcfm_measurement_price_calculator_get_options_value( $settings['volume-area']['height']['options'] ),
					'wrapper_class' => '_measurement_pricing_calculator_fields',
					'label'         => __( 'Height Options', 'woocommerce-measurement-price-calculator' ),
					'description'   => wcfm_measurement_price_calculator_get_options_tooltip(),
					'desc_tip'      => true,
				) );
			echo '</div>';
	
			// Weight
			echo '<div id="weight_measurements" class="measurement_fields">';
				woocommerce_wp_checkbox( array(
					'id'            => '_measurement_weight_pricing',
					'value'         => $settings['weight']['pricing']['enabled'],
					'class'         => 'checkbox _measurement_pricing',
					'label'         => __( 'Show Product Price Per Unit', 'woocommerce-measurement-price-calculator' ),
					'description'   => __( 'Check this box to display product pricing per unit on the frontend', 'woocommerce-measurement-price-calculator' ),
				) );
				echo '<div id="_measurement_weight_pricing_fields" class="_measurement_pricing_fields" style="display:none;">';
					woocommerce_wp_text_input( array(
						'id'          => '_measurement_weight_pricing_label',
						'value'       => $settings['weight']['pricing']['label'],
						'label'       => __( 'Pricing Label', 'woocommerce-measurement-price-calculator' ),
						'description' => __( 'Label to display next to the product price (defaults to pricing unit)', 'woocommerce-measurement-price-calculator' ),
						'desc_tip'    => true,
					) );
					woocommerce_wp_select( array(
						'id'          => '_measurement_weight_pricing_unit',
						'value'       => $settings['weight']['pricing']['unit'],
						'class'       => '_measurement_pricing_unit',
						'label'       => __( 'Pricing Unit', 'woocommerce-measurement-price-calculator' ),
						'options'     => $measurement_units['weight'],
						'description' => __( 'Unit to define pricing in', 'woocommerce-measurement-price-calculator' ),
						'desc_tip'    => true,
					) );
					woocommerce_wp_checkbox( array(
						'id'            => '_measurement_weight_pricing_calculator_enabled',
						'class'         => 'checkbox _measurement_pricing_calculator_enabled',
						'value'         => $settings['weight']['pricing']['calculator']['enabled'],
						'label'         => __( 'Calculated Price', 'woocommerce-measurement-price-calculator' ),
						'description'   => __( 'Check this box to define product pricing per unit and allow customers to provide custom measurements', 'woocommerce-measurement-price-calculator' ),
					) );
					woocommerce_wp_checkbox( array(
						'id'            => '_measurement_weight_pricing_weight_enabled',
						'value'         => $settings['weight']['pricing']['weight']['enabled'],
						'class'         => 'checkbox _measurement_pricing_weight_enabled',
						'wrapper_class' => $pricing_weight_wrapper_class . ' _measurement_pricing_calculator_fields',
						'label'         => __( 'Calculated Weight', 'woocommerce-measurement-price-calculator' ),
						'description'   => __( 'Check this box to use the customer-configured product weight as the item weight', 'woocommerce-measurement-price-calculator' ),
					) );
					woocommerce_wp_checkbox( array(
						'id'            => '_measurement_weight_pricing_inventory_enabled',
						'value'         => $settings['weight']['pricing']['inventory']['enabled'],
						'class'         => 'checkbox _measurement_pricing_inventory_enabled',
						'wrapper_class' => 'stock_fields _measurement_pricing_calculator_fields',
						'label'         => __( 'Calculated Inventory', 'woocommerce-measurement-price-calculator' ),
						'description'   => __( 'Check this box to define inventory per unit and calculate inventory based on the product weight', 'woocommerce-measurement-price-calculator' ),
					) );
					wcfm_measurement_price_calculator_overage_input( 'weight', $settings );
				echo '</div>';
				echo '<hr/>';
				woocommerce_wp_text_input( array(
					'id'          => '_measurement_weight_label',
					'value'       => $settings['weight']['weight']['label'],
					'label'       => __( 'Weight Label', 'woocommerce-measurement-price-calculator' ),
					'description' => __( 'Weight input field label to display on the frontend', 'woocommerce-measurement-price-calculator' ),
					'desc_tip'    => true,
				) );
				woocommerce_wp_select( array(
					'id'          => '_measurement_weight_unit',
					'value'       => $settings['weight']['weight']['unit'],
					'label'       => __( 'Weight Unit', 'woocommerce-measurement-price-calculator' ),
					'options'     => $measurement_units['weight'],
					'description' => __( 'The frontend weight input field unit', 'woocommerce-measurement-price-calculator' ),
					'desc_tip'    => true,
				) );
				woocommerce_wp_checkbox( array(
					'id'          => '_measurement_weight_editable',
					'value'       => $settings['weight']['weight']['editable'],
					'label'       => __( 'Editable', 'woocommerce-measurement-price-calculator' ),
					'class'       => 'checkbox _measurement_editable',
					'description' => __( 'Check this box to allow the needed measurement to be entered by the customer', 'woocommerce-measurement-price-calculator' ),
				) );
				wcfm_measurement_price_calculator_attributes_inputs( array(
					'measurement'   => 'weight',
					'input_name'    => 'weight',
					'input_label'   => __( 'Weight', 'woocommerce-measurement-price-calculator' ),
					'settings'      => $settings,
					'limited_field' => '_measurement_weight_options',
				) );
				woocommerce_wp_text_input( array(
					'id'            => '_measurement_weight_options',
					'value'         => wcfm_measurement_price_calculator_get_options_value( $settings['weight']['weight']['options'] ),
					'wrapper_class' => '_measurement_pricing_calculator_fields',
					'label'         => __( 'Weight Options', 'woocommerce-measurement-price-calculator' ),
					'description'   => wcfm_measurement_price_calculator_get_options_tooltip(),
					'desc_tip'      => true,
				) );
			echo '</div>';
	
	
			// wall dimension is just the area-dimension calculator with different labels
			echo '<div id="wall-dimension_measurements" class="measurement_fields">';
				woocommerce_wp_checkbox( array(
					'id'            => '_measurement_wall-dimension_pricing',
					'value'         => $settings['wall-dimension']['pricing']['enabled'],
					'class'         => 'checkbox _measurement_pricing',
					'label'         => __( 'Show Product Price Per Unit', 'woocommerce-measurement-price-calculator' ),
					'description'   => __( 'Check this box to display product pricing per unit on the frontend', 'woocommerce-measurement-price-calculator' ),
				) );
				echo '<div id="_measurement_wall-dimension_pricing_fields" class="_measurement_pricing_fields" style="display:none;">';
					woocommerce_wp_text_input( array(
						'id'          => '_measurement_wall-dimension_pricing_label',
						'value'       => $settings['wall-dimension']['pricing']['label'],
						'label'       => __( 'Pricing Label', 'woocommerce-measurement-price-calculator' ),
						'description' => __( 'Label to display next to the product price (defaults to pricing unit)', 'woocommerce-measurement-price-calculator' ),
						'desc_tip'    => true,
					) );
					woocommerce_wp_select( array(
						'id'          => '_measurement_wall-dimension_pricing_unit',
						'value'       => $settings['wall-dimension']['pricing']['unit'],
						'class'       => '_measurement_pricing_unit',
						'label'       => __( 'Pricing Unit', 'woocommerce-measurement-price-calculator' ),
						'options'     => $measurement_units['area'],
						'description' => __( 'Unit to define pricing in', 'woocommerce-measurement-price-calculator' ),
						'desc_tip'    => true,
					) );
					woocommerce_wp_checkbox( array(
						'id'            => '_measurement_wall-dimension_pricing_calculator_enabled',
						'class'         => 'checkbox _measurement_pricing_calculator_enabled',
						'value'         => $settings['wall-dimension']['pricing']['calculator']['enabled'],
						'label'         => __( 'Calculated Price', 'woocommerce-measurement-price-calculator' ),
						'description'   => __( 'Check this box to define product pricing per unit and allow customers to provide custom measurements', 'woocommerce-measurement-price-calculator' ),
					) );
					woocommerce_wp_checkbox( array(
						'id'            => '_measurement_wall-dimension_pricing_weight_enabled',
						'value'         => $settings['wall-dimension']['pricing']['weight']['enabled'],
						'class'         => 'checkbox _measurement_pricing_weight_enabled',
						'wrapper_class' => $pricing_weight_wrapper_class . ' _measurement_pricing_calculator_fields',
						'label'         => __( 'Calculated Weight', 'woocommerce-measurement-price-calculator' ),
						'description'   => __( 'Check this box to define the product weight per unit and calculate the item weight based on the product area', 'woocommerce-measurement-price-calculator' ),
					) );
					woocommerce_wp_checkbox( array(
						'id'            => '_measurement_wall-dimension_pricing_inventory_enabled',
						'value'         => $settings['wall-dimension']['pricing']['inventory']['enabled'],
						'class'         => 'checkbox _measurement_pricing_inventory_enabled',
						'wrapper_class' => 'stock_fields _measurement_pricing_calculator_fields',
						'label'         => __( 'Calculated Inventory', 'woocommerce-measurement-price-calculator' ),
						'description'   => __( 'Check this box to define inventory per unit and calculate inventory based on the product area', 'woocommerce-measurement-price-calculator' ),
					) );
					wcfm_measurement_price_calculator_overage_input( 'wall-dimension', $settings );
				echo '</div>';
				echo '<hr/>';
				woocommerce_wp_text_input( array(
					'id'          => '_measurement_wall_length_label',
					'value'       => $settings['wall-dimension']['length']['label'],
					'label'       => __( 'Length Label', 'woocommerce-measurement-price-calculator' ),
					'description' => __( 'Wall length input field label to display on the frontend', 'woocommerce-measurement-price-calculator' ),
					'desc_tip'    => true,
				) );
				woocommerce_wp_select( array(
					'id'          => '_measurement_wall_length_unit',
					'value'       => $settings['wall-dimension']['length']['unit'],
					'label'       => __( 'Length Unit', 'woocommerce-measurement-price-calculator' ),
					'options'     => $measurement_units['dimension'],
					'description' => __( 'The frontend wall length input field unit', 'woocommerce-measurement-price-calculator' ),
					'desc_tip'    => true,
				) );
				wcfm_measurement_price_calculator_attributes_inputs( array(
					'measurement'   => 'wall-dimension',
					'input_name'    => 'length',
					'input_label'   => __( 'Length', 'woocommerce-measurement-price-calculator' ),
					'settings'      => $settings,
					'limited_field' => '_measurement_wall_length_options',
				) );
				woocommerce_wp_text_input( array(
					'id'            => '_measurement_wall_length_options',
					'value'         => wcfm_measurement_price_calculator_get_options_value( $settings['wall-dimension']['length']['options'] ),
					'wrapper_class' => '_measurement_pricing_calculator_fields',
					'label'         => __( 'Length Options', 'woocommerce-measurement-price-calculator' ),
					'description'   => wcfm_measurement_price_calculator_get_options_tooltip(),
					'desc_tip'      => true,
				) );
				echo '<hr/>';
	
				woocommerce_wp_text_input( array(
					'id'          => '_measurement_wall_width_label',
					'value'       => $settings['wall-dimension']['width']['label'],
					'label'       => __( 'Height Label', 'woocommerce-measurement-price-calculator' ),
					'description' => __( 'Room wall height input field label to display on the frontend', 'woocommerce-measurement-price-calculator' ),
					'desc_tip'    => true,
				) );
				woocommerce_wp_select( array(
					'id'          => '_measurement_wall_width_unit',
					'value'       => $settings['wall-dimension']['width']['unit'],
					'label'       => __( 'Height Unit', 'woocommerce-measurement-price-calculator' ),
					'options'     => $measurement_units['dimension'],
					'description' => __( 'The frontend room wall height input field unit', 'woocommerce-measurement-price-calculator' ),
					'desc_tip'    => true,
				) );
				wcfm_measurement_price_calculator_attributes_inputs( array(
					'measurement'   => 'wall-dimension',
					'input_name'    => 'width',
					'input_label'   => __( 'Height', 'woocommerce-measurement-price-calculator' ),
					'settings'      => $settings,
					'limited_field' => '_measurement_wall_width_options',
				) );
				woocommerce_wp_text_input( array(
					'id'            => '_measurement_wall_width_options',
					'value'         => wcfm_measurement_price_calculator_get_options_value( $settings['wall-dimension']['width']['options'] ),
					'wrapper_class' => '_measurement_pricing_calculator_fields',
					'label'         => __( 'Height Options', 'woocommerce-measurement-price-calculator' ),
					'description'   => wcfm_measurement_price_calculator_get_options_tooltip(),
					'desc_tip'      => true,
				) );
			echo '</div>';
			echo '</div>'; // close the subpanel
			echo '<div id="calculator-pricing-table" class="calculator-subpanel">';
			if ( Framework\SV_WC_Plugin_Compatibility::is_wc_version_gte( '3.3' ) ) {
				require_once( wc_measurement_price_calculator()->get_plugin_path() . '/includes/admin/post-types/writepanels/writepanel-product_data-pricing_table.php' );
			} else {
				require_once( wc_measurement_price_calculator()->get_plugin_path() . '/admin/post-types/writepanels/writepanel-product_data-pricing_table.php' );
			}
			echo '</div>';
			?>
	
		</div>
	</div>
</div>