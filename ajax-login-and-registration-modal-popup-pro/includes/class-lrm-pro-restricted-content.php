<?php

/**
 * @since 1.64
 *
 * Class LRM_Pro_Auto_Trigger
 */
// extends LRM_Post_Config_Abstract
class LRM_Pro_Restricted_Content  {

    protected static $instance;

	function __construct(  ) {

//        parent::__construct(
//            'restricted_content',
//            'restricted content',
//            [
//                'enabled' => 'auto_trigger/general/on',
//                'timeout' => 'auto_trigger/general/timeout',
//                'tab'     => 'auto_trigger/general/tab',
//                'after_n_pages' => 'auto_trigger/general/after_n_pages',
//                'stop_after_n_displays' => 'auto_trigger/general/stop_after_n_displays',
//            ],
//            [
//                'enabled' => 'lrm_auto_trigger_on',
//                'timeout' => 'lrm_auto_trigger_timeout',
//                'tab'     => 'lrm_auto_trigger_tab',
//                'after_n_pages' => 'lrm_auto_trigger_after_n_pages',
//                'stop_after_n_displays' => 'lrm_auto_trigger_stop_after_n_displays',
//            ]
//        );
//

        add_shortcode('lrm_restricted', [$this, 'shortcode_is_logged']);
        add_shortcode('lrm_for_not_logged', [$this, 'shortcode_is_not_logged']);
    }

    function shortcode_is_logged($atts, $content = null) {
        $args = wp_parse_args($atts, array(
            'restricted_text' => '<h2>Please login/register to see this content!</h2>',
            'role'      => false,
            'capacity'  => false,
            'default_tab' => 'register',
        ));

        if ( is_user_logged_in() && !is_null($content) ) {
            return apply_filters('the_content', $content);
        } else {
            return apply_filters('the_content', $args['restricted_text'])
                    . LRM_Core::get()->shortcode( [ 'default_tab' => $args['default_tab'] ] );
        }
    }

    function shortcode_is_not_logged($atts, $content = null) {
        if ( (!is_user_logged_in() && !is_null($content) ) || is_feed() ) {
            return apply_filters('the_content', $content);
        }
        return '';
    }

    /**
     * @return self
     */
    public static function get(){
        if ( ! isset( self::$instance ) ) {
            return self::$instance = new self();
        }

        return self::$instance;
    }

}