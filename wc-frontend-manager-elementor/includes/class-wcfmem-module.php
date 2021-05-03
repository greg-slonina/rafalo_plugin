<?php

use Elementor\Controls_Manager;

class WCFM_Elementor_Module extends WCFM_Elementor_ModuleBase {

    public function __construct() {
        parent::init();

        add_action( 'elementor/documents/register', [ $this, 'register_documents' ] );
        add_action( 'elementor/dynamic_tags/register_tags', [ $this, 'register_tags' ] );
        add_action( 'elementor/controls/controls_registered', [ $this, 'register_controls' ] );
        add_action( 'elementor/editor/footer', [ $this, 'add_editor_templates' ], 9 );
        add_action( 'elementor/theme/register_conditions', [ $this, 'register_conditions' ] );
        add_filter( 'wcfmem_locate_store_template', [ $this, 'locate_template_for_store_page' ], 999 );
        //add_action( 'elementor/element/before_section_end', [ $this, 'add_column_wrapper_padding_control' ], 10, 3 );
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_editor_scripts' ] );
    }

    /**
     * Name of the elementor module
     *
     * @return string
     */
    public function get_name() {
			return 'wcfm';
    }

    /**
     * Module widgets
     *
     * @return array
     */
    public function get_widgets() {
    	global $WCFM, $WCFMem;
			$widgets = [
											'StoreBanner',
											'StoreName',
											'StoreLogo',
											'StoreInfo',
											'StoreRating',
											'StoreTabs',
											'StoreTabContents',
											'StoreSocial',
											'StoreInquiry',
									];
			
			if( WCFM_Dependencies::wcfmu_plugin_active_check() ) {
				$widgets[] = 'StoreFollow';
				$widgets[] = 'StoreChat';
			}
			 
			return $widgets;
    }

    /**
     * Register module documents
     *
     * @param Elementor\Core\Documents_Manager $documents_manager
     *
     * @return void
     */
    public function register_documents( $documents_manager ) {
    	global $WCFM, $WCFMem;
    	
			$this->docs_types = [
					'wcfmem-store' => StorePage::get_class_full_name(),
			];

			foreach ( $this->docs_types as $type => $class_name ) {
				$documents_manager->register_document_type( $type, $class_name );
			}
    }

    /**
     * Register module tags
     *
     * @return void
     */
    public function register_tags() {
    	global $WCFM, $WCFMem;
    	
			$tags = [
					'StoreBanner',
					'StoreName',
					'StoreLogo',
					'StoreInfo',
					'StoreRating',
					'StoreInquiry',
					'StoreTabs',
					'StoreDummyProducts',
					'StoreSocial',
					'StoreFollow',
					'StoreChat',
			];

			$module = $WCFMem->wcfmem_elementor()->dynamic_tags;

			$module->register_group( WCFM_ELEMENTOR_GROUP, [
					'title' => __( 'WCFM', 'wc-frontend-manager-elementor' ),
			] );

			foreach ( $tags as $tag ) {
				require_once ( $WCFMem->plugin_path . 'includes/Tags/' . esc_attr($tag) . '.php');
				$module->register_tag( "{$tag}" );
			}
    }

    /**
     * Register controls
     *
     * @return void
     */
    public function register_controls() {
    	global $WCFM, $WCFMem;
    	
			$controls = [
					'WCFM_Elementor_SortableList',
					'WCFM_Elementor_DynamicHidden',
			];

			$controls_manager = $WCFMem->wcfmem_elementor()->controls_manager;

			foreach ( $controls as $control ) {
				$control_class = "{$control}";
				$controls_manager->register_control( $control_class::CONTROL_TYPE, new $control_class() );
			}
    }

    /**
     * Add editor templates
     *
     * @return void
     */
    public function add_editor_templates() {
    	global $WCFM, $WCFMem;
    	
			$template_names = [
					'sortable-list-row',
			];

			foreach ( $template_names as $template_name ) {
				$WCFMem->wcfmem_elementor()->common->add_template( $WCFMem->plugin_path . "views/editor-templates/$template_name.php" );
			}
    }

    /**
     * Register condition for the module
     *
     * @param \ElementorPro\Modules\ThemeBuilder\Classes\Conditions_Manager $conditions_manager
     *
     * @return void
     */
    public function register_conditions( $conditions_manager ) {
    	global $WCFM, $WCFMem;
			$condition = new StoreCondition();
			$conditions_manager->get_condition( 'general' )->register_sub_condition( $condition );
    }

    /**
     * Filter to show the elementor built store template
     *
     * @return string
     */
    public static function locate_template_for_store_page( $template ) {
    	global $WCFM, $WCFMem;
    	
			if ( wcfmmp_is_store_page() ) {
				$documents = \ElementorPro\Modules\ThemeBuilder\Module::instance()->get_conditions_manager()->get_documents_for_location( 'single' );

				if ( empty( $documents ) ) {
					return $template;
				}

				$page_templates_module = $WCFMem->wcfmem_elementor()->modules_manager->get_modules( 'page-templates' );

				$page_templates_module->set_print_callback( function() {
						\ElementorPro\Modules\ThemeBuilder\Module::instance()->get_locations_manager()->do_location( 'single' );
				} );

				$template_path = $page_templates_module->get_template_path( $page_templates_module::TEMPLATE_HEADER_FOOTER );

				return $template_path;
			}

			return $template;
    }

    /**
     * Add column wrapper padding control for sections
     *
     * @return void
     */
    public static function add_column_wrapper_padding_control( $control_stack, $section_id, $args ) {
    	global $WCFM, $WCFMem;
    	
			if ( 'section' === $control_stack->get_name() && 'section_advanced' === $section_id ) {
				$control_stack->add_responsive_control(
						'column_wrapper_padding',
						[
								'label'      => __( 'Column Wrapper Padding', 'wc-frontend-manager-elementor' ),
								'type'       => Controls_Manager::DIMENSIONS,
								'size_units' => [ 'px', 'em', '%' ],
								'selectors'  => [
										'{{WRAPPER}} .elementor-column-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
								],
						],
						[
								'position' => [ 'of' => 'padding' ]
						]
				);
			}
    }

    /**
     * Enqueue scripts in editing or preview mode
     *
     * @return void
     */
    public function enqueue_editor_scripts() {
    	global $WCFM, $WCFMem;
			if ( $WCFMem->is_edit_or_preview_mode() ) {
				$scheme  = is_ssl() ? 'https' : 'http';
				$api_key = isset( $WCFMmp->wcfmmp_marketplace_options['wcfm_google_map_api'] ) ? $WCFMmp->wcfmmp_marketplace_options['wcfm_google_map_api'] : '';
				if ( $api_key ) {
					wp_enqueue_script( 'wcfmem-store-google-maps', $scheme . '://maps.googleapis.com/maps/api/js?key=' . $api_key . '&libraries=places' );
				}
			}
    }
}
