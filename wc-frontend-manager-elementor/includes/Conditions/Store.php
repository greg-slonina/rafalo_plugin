<?php

use ElementorPro\Modules\ThemeBuilder\Conditions\Condition_Base;

class StoreCondition extends Condition_Base {

	/**
	 * Type of condition
	 *
	 * @return string
	 */
	public static function get_type() {
		return 'wcfmem-store';
	}

	/**
	 * Condition name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'wcfmem-store';
	}

	/**
	 * Condition label
	 *
	 * @return string
	 */
	public function get_label() {
		return __( 'Store Page', 'wc-frontend-manager-elementor' );
	}

	/**
	 * Condition label for all items
	 *
	 * @return string
	 */
	public function get_all_label() {
		return __( 'All Stores', 'wc-frontend-manager-elementor' );
	}

	/**
	 * Check if proper conditions are met
	 *
	 * @param array $args
	 *
	 * @return bool
	 */
	public function check( $args ) {
		return wcfmmp_is_store_page();
	}
}
