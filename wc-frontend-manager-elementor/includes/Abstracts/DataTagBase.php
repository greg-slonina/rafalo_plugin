<?php

use Elementor\Core\DynamicTags\Data_Tag;

abstract class WCFM_Elementor_DataTagBase extends Data_Tag {

    public function get_group() {
        return WCFM_ELEMENTOR_GROUP;
    }

    public function get_categories() {
        return [ \Elementor\Modules\DynamicTags\Module::BASE_GROUP ];
    }
}
