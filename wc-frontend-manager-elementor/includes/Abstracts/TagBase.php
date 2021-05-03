<?php

use Elementor\Core\DynamicTags\Tag;

abstract class WCFM_Elementor_TagBase extends Tag {
    
	public function get_group() {
			return WCFM_ELEMENTOR_GROUP;
	}

	public function get_categories() {
			return [ \Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY ];
	}
}
