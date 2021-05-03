<?php

namespace candm\Widgets\Items\Elementor;
use Elementor\Controls_Manager as Controls;
use Elementor\Widget_Base as Base;

class Readmore extends Base {

    public function get_name() {
        return 'candm-read-more';
    }

    public function get_title() {
        return __( 'Read more', 'candm' );
    }

    public function get_icon() {
        return 'eicon-dropdown';
    }

    public function get_categories() {
        return ['candm'];
    }

    public function get_keywords() {
        return ['candm', 'rafalo', 'read more', 'drop down'];
    }

    /**
     * Registers controls for
     *
     * @return void
     */
    protected function _register_controls() {
        $this->start_controls_section(
            'contents',
            [
                'label' => __( 'Contents', 'candm' ),
                'tab'   => Controls::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'text_content',
            [
                'label' => __( 'Text content', 'candm' ),
                'type'  => \Elementor\Controls_Manager::WYSIWYG,
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name'     => 'content_typography',
                'label'    => __( 'Typography', 'plugin-domain' ),
                'selector' => '{{WRAPPER}} .read-more-content',
            ]
        );

        $this->add_control(
            'text_color',
            [
                'label'     => __( 'Text color', 'geomify' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#000',
                'selectors' => [
                    '{{WRAPPER}} .read-more-content' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Renders the element to the frontend
     *
     * @return void
     */
    protected function render() {
        $s = $this->get_settings_for_display();
        $this->add_inline_editing_attributes(
            'text_content'
        );
        $attr = $this->get_render_attribute_string( 'text_content' );

        $el = sprintf( '<div class="candm-read-more" data-collapsed="true">
        <div class="read-more-content" %s >%s</div>
        <div class="read-more-button"><i class="fas fa-plus"></i>&nbsp;Read more</div>
        </div>',
            $attr,
            $s['text_content']
        );

        echo $el;
    }

    protected function _content_template() {

    }
}