<?php

use underDEV\Utils\Settings\CoreFields;

/**
 * @since 1.28
 *
 * Class LRM_Pro_Auto_Trigger
 */
class LRM_Pro_Auto_Trigger extends LRM_Post_Config_Abstract {

    protected static $instance;

	function __construct(  ) {

        parent::__construct(
            'auto_trigger',
            'auto-trigger',
            [
                'enabled' => 'auto_trigger/general/on',
                'timeout' => 'auto_trigger/general/timeout',
                'tab'     => 'auto_trigger/general/tab',
                'after_n_pages' => 'auto_trigger/general/after_n_pages',
                'stop_after_n_displays' => 'auto_trigger/general/stop_after_n_displays',
            ],
            [
                'enabled' => 'lrm_auto_trigger_on',
                'timeout' => 'lrm_auto_trigger_timeout',
                'tab'     => 'lrm_auto_trigger_tab',
                'after_n_pages' => 'lrm_auto_trigger_after_n_pages',
                'stop_after_n_displays' => 'lrm_auto_trigger_stop_after_n_displays',
            ]
        );


        if ( ! is_admin() ) {
            add_action('wp_enqueue_scripts', [$this, 'wp_enqueue_scripts'], 6);
        }

    }

    function wp_enqueue_scripts() {

        if ( is_user_logged_in() || ! $this->_get_option('enabled') ) {
            return;
        }

        if ( $this->check_url_exclusion() ) {
            echo '<!-- LRM Auto-trigger :: Excluded by url -->' . PHP_EOL;
            return;
        }

        $stop_after_n_displays = $this->_get_option('stop_after_n_displays');
        $after_n_pages = $this->_get_option('after_n_pages');
        $trigger_in = $this->_get_option('timeout');
        $tab = $this->_get_option('tab');
        if ( !$after_n_pages ) {
	        $after_n_pages = 1;
        } else {
	        $after_n_pages = absint($after_n_pages);
        }

        if ( false === $stop_after_n_displays ) {
	        $stop_after_n_displays = 3;
        } else {
	        $stop_after_n_displays = absint($stop_after_n_displays);
        }

        if ( !$trigger_in ) {
            $trigger_in = 1;
        } else {
            $trigger_in = absint($trigger_in);
        }

        if ( !$tab ) {
            $tab = 'login';
        }

        ob_start();
        ?>
        +(function($) {
            var triggerAfterPages = parseInt(<?php echo $after_n_pages; ?>);
            var stopAfterDisplays = parseInt(<?php echo $stop_after_n_displays; ?>);
            var triggerInSeconds = parseInt(<?php echo $trigger_in; ?>);
            if ( triggerInSeconds ) {
                if (stopAfterDisplays) {
                    var displaysCount = parseInt( localStorage.getItem('lrm_displays') ) || 0;
                    if ( displaysCount > stopAfterDisplays ) {
                        return;
                    }
                }

                if ( triggerAfterPages > 1 ) {
                    var viewedPages = parseInt( localStorage.getItem("lrm_viewed_pages") ) || 0;
                    viewedPages++;
                    localStorage.setItem("lrm_viewed_pages", viewedPages);
                    if ( viewedPages < triggerAfterPages ) {
                        // Stop here
                        return;
                    }
                }
                setTimeout(function () {
                    if ( $("body").hasClass("logged-in") ) return; // Skip if logged in
                    // Skip if modal is active
                    if ( ! $(".lrm-user-modal").hasClass("is-visible") && (undefined == LRM_Pro.modal_is_shown || !LRM_Pro.modal_is_shown) )  {
                        LRM_Pro.has_auto_trigger = true;
                        $(document).triggerHandler("lrm_show_<?= esc_attr($tab); ?>");
                    }
                    if (stopAfterDisplays) {
                        displaysCount++;
                        localStorage.setItem("lrm_displays", displaysCount);
                    }

                }, triggerInSeconds * 1000);
            }
        })(jQuery);<?php

        wp_add_inline_script('lrm-modal', ob_get_clean());
    }

    function check_url_exclusion() {
        $exclude_by_slug = lrm_setting('auto_trigger/advanced/exclude_by_slug');

	    if ( !$exclude_by_slug || empty($_SERVER['REQUEST_URI']) ) {
            return false;
        }

        $exclude_by_slug_arr = explode(',', $exclude_by_slug);

	    // Split string by ,
	    if ( !$exclude_by_slug_arr ) {
	        return false;
        }
        //$exclude_by_slug = str_replace('/', '\/', $exclude_by_slug);

        foreach ($exclude_by_slug_arr as $exclude_by_slug_one) {
            if ( $exclude_by_slug_one === $_SERVER['REQUEST_URI'] ) {
                return true;
            }
            if (preg_match($exclude_by_slug_one, $_SERVER['REQUEST_URI'])) {
                return true;
            }
        }
    }

    /**
     * Output the HTML for the metabox.
     */
    function render_metabox() {
        parent::render_metabox();

        global $post;

        $status = get_post_meta($post->ID, 'lrm_auto_trigger_on', true);
        $tab = get_post_meta($post->ID, 'lrm_auto_trigger_tab', true);

        ?>
        <div class="inside lrm-inside">
            <label class="lrm-attributes-label" for="lrm_auto_trigger_on"><?php _e( 'Status', 'ajax-login-and-registration-modal-popup' ); ?></label>
            <select name="lrm_auto_trigger_on" id="lrm_auto_trigger_on">
                <option value="" <?= selected('', $status); ?>>Use global settings</option>
                <option value="enabled" <?= selected('enabled', $status); ?>>Enabled</option>
                <option value="disabled" <?= selected('disabled', $status); ?>>Disabled</option>
            </select>

            <div class="clearfix"></div>

            <label class="lrm-attributes-label" for="lrm_auto_trigger_tab"><?php _e( 'Tab', 'ajax-login-and-registration-modal-popup' ); ?></label>
            <select name="lrm_auto_trigger_tab" id="lrm_auto_trigger_tab">
                <option value="" <?= selected('', $tab); ?>>Use global settings</option>
                <option value="login" <?= selected('login', $tab); ?>>Login tab</option>
                <option value="register" <?= selected('register', $tab); ?>>Registration tab</option>
            </select>

            <div class="clearfix"></div>

            <label class="lrm-attributes-label" for="lrm_auto_trigger_timeout"><?php _e( 'Timeout', 'ajax-login-and-registration-modal-popup' ); ?></label>
            <input type="number" name="lrm_auto_trigger_timeout" value="<?= get_post_meta($post->ID, 'lrm_auto_trigger_timeout', true); ?>" class="" min="0" max="60">
            <small><?php _e( 'Use 0 to follow global settings', 'ajax-login-and-registration-modal-popup' ); ?></small>

            <style type="text/css">
                .lrm-attributes-label {
                    display: inline-block;
                    vertical-align: middle;
                    min-width: 120px;
                    font-weight: bold;
                }
                .lrm-inside select{
                    display: inline-block;
                    vertical-align: middle;
                }
            </style>
        </div>
        <?php
    }

    function register_settings( $settings_class ) {

        $AUTOTRIGGER_Section = $settings_class->add_section( __( 'Auto-trigger > PRO' ), 'auto_trigger' );

        $AUTOTRIGGER_Section->add_group( __( 'General' ), 'general' )
            ->add_field( array(
                'slug'        => 'on',
                'name'        => __('Auto-trigger modal?', 'ajax-login-and-registration-modal-popup' ),
                'addons'      => array('label' => __( 'Yes' )),
                'default'     => false,
                'render'      => array( new CoreFields\Checkbox(), 'input' ),
                'sanitize'    => array( new CoreFields\Checkbox(), 'sanitize' ),
                //'description'    => '',
            ) )
            ->add_field( array(
                'slug'        => 'timeout',
                'name'        => __('Timeout before auto-trigger', 'ajax-login-and-registration-modal-popup' ),
                'description' => __('1-600 seconds, 60 seconds = 1 minute', 'ajax-login-and-registration-modal-popup' ),
                'default'     => 15,
                'render'      => array( new CoreFields\Number(), 'input' ),
                'sanitize'    => array( new CoreFields\Number(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'after_n_pages',
                'name'        => __('Auto-trigger after N pages view', 'ajax-login-and-registration-modal-popup' ),
                'description' => __('Number between 1-10', 'ajax-login-and-registration-modal-popup' ),
                'default'     => 1,
                'render'      => array( new CoreFields\Number(), 'input' ),
                'sanitize'    => array( new CoreFields\Number(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'stop_after_n_displays',
                'name'        => __('Stop auto-trigger modal after N shows', 'ajax-login-and-registration-modal-popup' ),
                'description' => __('Number between 0-10, 0 means unlimited', 'ajax-login-and-registration-modal-popup' ),
                'default'     => 3,
                'render'      => array( new CoreFields\Number(), 'input' ),
                'sanitize'    => array( new CoreFields\Number(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'tab',
                'name'        => __('Default tab', 'ajax-login-and-registration-modal-popup'),
                'addons'      => array(
                    'options'     => array(
                        'login'   => 'Login tab',
                        'register'   => 'Registration tab',
                    ),
                ),
                'default'     => 'login',
                //'description' => __('Position of Social Buttons/reCaptcha/etc depends on this option.', 'ajax-login-and-registration-modal-popup' ),
                'render'      => array( new CoreFields\Select(), 'input' ),
                'sanitize'    => array( new CoreFields\Select(), 'sanitize' ),
            ) )
            ->description(
                __('After specified timeout modal will auto-appear. In a Post/page settings global options can be overridden.', 'ajax-login-and-registration-modal-popup')
            );


        $AUTOTRIGGER_Section->add_group( __( 'Advanced' ), 'advanced' )
            ->add_field( array(
                'slug'        => 'exclude_by_slug',
                'name'        => __('Exclude pages/posts by url from atu-trigger', 'ajax-login-and-registration-modal-popup' ),
                'description' =>
                    __('Comma separated values (regular expressions).
                            <br/><code>^</code> marks start of query string, for find only url starting from this pattern. 
                            <br/>Examples: exclude all products: <code>^/products/</code>,
                            exclude posts with "download-" part: <code>download-</code>'
                        , 'ajax-login-and-registration-modal-popup' ),
                'default'     => '',
                'render'      => array( new CoreFields\Text(), 'input' ),
                'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
            ) );

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