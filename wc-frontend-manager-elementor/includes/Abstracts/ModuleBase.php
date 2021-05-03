<?php

abstract class WCFM_Elementor_ModuleBase {

	/**
	 * Runs after first instance
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'elementor/widgets/widgets_registered', [ $this, 'init_widgets' ] );
	}

	/**
	 * Module name
	 *
	 * @return void
	 */
	abstract public function get_name();

	/**
	 * Module widgets
	 *
	 * @return array
	 */
	public function get_widgets() {
		return [];
	}

	/**
	 * Register module widgets
	 *
	 * @return void
	 */
	public function init_widgets() {
		global $WCFM, $WCFMem;
		
		$widget_manager = $WCFMem->wcfmem_elementor()->widgets_manager;

		foreach ( $this->get_widgets() as $widget ) {
			$this->load_class( $widget );
			
			$class_name = "WCFM_Elementor_{$widget}";

			if ( class_exists( $class_name ) ) {
				$widget_manager->register_widget_type( new $class_name() );
			}
		}
	}
	
	public function load_class($class_name = '') {
		global $WCFM, $WCFMem;
		if ('' != $class_name && '' != $WCFMem->token) {
			require_once ( $WCFMem->plugin_path .  'widgets/class-' . esc_attr($WCFMem->token) . '-widget-' . strtolower(esc_attr($class_name)) . '.php');
		} // End If Statement
	}
}
