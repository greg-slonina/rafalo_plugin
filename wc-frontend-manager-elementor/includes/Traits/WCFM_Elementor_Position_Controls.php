<?php

use Elementor\Controls_Manager;

trait PositionControls {

	/**
	 * Add css position controls
	 *
	 * @return void
	 */
	protected function add_position_controls() {
			$this->start_injection( [
					'type' => 'section',
					'at'   => 'start',
					'of'   => '_section_style',
			] );

			$this->start_controls_section(
					'section_position',
					[
							'label' => __( 'Position', 'wc-frontend-manager-elementor' ),
							'tab'   => Controls_Manager::TAB_ADVANCED,
					]
			);

			$this->add_responsive_control(
					'_wcfmem_position',
					[
							'label'   => __( 'Position', 'wc-frontend-manager-elementor' ),
							'type'    => Controls_Manager::SELECT,
							'options' => [
									'static'   => __( 'Static', 'wc-frontend-manager-elementor' ),
									'relative' => __( 'Relative', 'wc-frontend-manager-elementor' ),
									'absolute' => __( 'Absolute', 'wc-frontend-manager-elementor' ),
									'sticky'   => __( 'Sticky', 'wc-frontend-manager-elementor' ),
									'fixed'    => __( 'Fixed', 'wc-frontend-manager-elementor' ),
							],
							'desktop_default' => 'relative',
							'tablet_default'  => 'relative',
							'mobile_default'  => 'relative',
							'selectors' => [
									'{{WRAPPER}}' => 'position: relative; min-height: 1px',
									'{{WRAPPER}} > .elementor-widget-container' => 'position: {{VALUE}};',
							],
					]
			);

			$this->add_responsive_control(
					'_wcfmem_position_top',
					[
							'label'     => __( 'Top', 'wc-frontend-manager-elementor' ),
							'type'      => Controls_Manager::TEXT,
							'default'   => '',
							'selectors' => [
									'{{WRAPPER}} > .elementor-widget-container' => 'top: {{VALUE}};',
							],
					]
			);

			$this->add_responsive_control(
					'_wcfmem_position_right',
					[
							'label'     => __( 'Right', 'wc-frontend-manager-elementor' ),
							'type'      => Controls_Manager::TEXT,
							'default'   => '',
							'selectors' => [
									'{{WRAPPER}} > .elementor-widget-container' => 'right: {{VALUE}};',
							],
					]
			);

			$this->add_responsive_control(
					'_wcfmem_position_bottom',
					[
							'label'     => __( 'Bottom', 'wc-frontend-manager-elementor' ),
							'type'      => Controls_Manager::TEXT,
							'default'   => '',
							'selectors' => [
									'{{WRAPPER}} > .elementor-widget-container' => 'bottom: {{VALUE}};',
							],
					]
			);

			$this->add_responsive_control(
					'_wcfmem_position_left',
					[
							'label'     => __( 'Left', 'wc-frontend-manager-elementor' ),
							'type'      => Controls_Manager::TEXT,
							'default'   => '',
							'selectors' => [
									'{{WRAPPER}} > .elementor-widget-container' => 'left: {{VALUE}};',
							],
					]
			);

			$this->end_controls_section();

			$this->end_injection();
	}
}
