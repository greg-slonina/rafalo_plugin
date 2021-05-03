<?php

use ElementorPro\Modules\ThemeBuilder\Documents\Single;

class StorePage extends Single {

	/**
	 * Class constructor
	 *
	 * @param array $data
	 *
	 * @return void
	 */
	public function __construct( $data = [] ) {
			parent::__construct( $data );

			add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ], 11 );
	}

	/**
	 * Enqueue document related scripts
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		global $WCFM, $WCFMem;
		
		wp_enqueue_style(
				'wcfmem-doc-store',
				$WCFMem->plugin_url . 'assets/css/wcfmem-elementor-document-store.css',
				[],
				WCFMem_VERSION
		);
	}

	/**
	 * Document properties
	 *
	 * @return array
	 */
	public static function get_properties() {
		$properties = parent::get_properties();

		$properties['location']       = 'single';
		$properties['condition_type'] = 'general';

		return $properties;
	}

	/**
	 * Document name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'wcfmem-store';
	}

	/**
	 * Document title
	 *
	 * @return string
	 */
	public static function get_title() {
		return __( 'Store Page', 'wc-frontend-manager-elementor' );
	}

	/**
	 * Elementor builder panel categories
	 *
	 * @return array
	 */
	protected static function get_editor_panel_categories() {
		$categories = [
				'wcfmem-store-elements-single' => [
						'title'  => __( 'Marketplace', 'wc-frontend-manager-elementor' ),
						'active' => true,
				],
		];

		$categories += parent::get_editor_panel_categories();

		return $categories;
	}

	/**
	 * Document library type
	 *
	 * From elementor pro v2.4.0 it is deprecated
	 *
	 * @return string
	 */
	public function get_remote_library_type() {
			return 'single store';
	}
	
	/**
	 * Remote library config
	 *
	 * From elementor pro v2.4.0 `get_remote_library_config` is used
	 * instead of `get_remote_library_type`
	 *
	 * @since 2.9.13
	 *
	 * @return array
	 */
	public function get_remote_library_config() {
			$config = parent::get_remote_library_config();

			$config['category'] = 'single store';

			return $config;
	}
}
