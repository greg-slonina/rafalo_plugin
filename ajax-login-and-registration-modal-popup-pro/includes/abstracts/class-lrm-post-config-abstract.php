<?php

/**
 * @since 1.64
 *
 * Class LRM_Pro_Auto_Trigger
 */
abstract class LRM_Post_Config_Abstract
{

    /** @var string $slug */
    public $slug;

    /** @var string $slug */
    public $title;

    /** @var array $global_options */
    protected $global_options;

    /** @var array $post_options */
    protected $post_options;

    /**
     * LRM_Post_Config_Abstract constructor.
     * @param string $slug
     * @param string $title
     * @param array $global_options
     * @param array $post_options
     */
    function __construct($slug, $title, $global_options, $post_options ) {

        $this->slug = 'lrm_' . $slug;

        $this->title = $title;

        $this->global_options = $global_options;

        $this->post_options = $post_options;


        if ( is_admin() ) {
            add_action('add_meta_boxes', [$this, 'register_meta_boxes'], 10, 2);
            add_action('save_post', [$this, 'save_meta_box'], 10, 2);
        }
    }


    /**
     * @param string $option
     *
     * @return bool
     */
    function _get_option($option ) {

        if ( isset($this->post_options[$option]) && get_the_ID() ) {
            $value = get_post_meta( get_the_ID(), $this->post_options[$option], true );

            if ( 'disabled' == $value ) {
                return false;
            }
            if ( 'enabled' == $value ) {
                return true;
            }

            if ( !$value & isset($this->global_options[$option]) ) {
                return lrm_setting($this->global_options[$option] );
            }

            return $value;
        }

        return false;
    }

    /**
     * Adds a metabox to the right side of the screen under the “Publish” box
     */
    function register_meta_boxes($post_type, $post) {

        // CHeck if Post type if public
//        var_dump( $post_type );
//        var_dump( get_post_types(array( 'public' => true ), 'names') );

        if ( ! in_array( $post_type, get_post_types(array( 'public' => true ), 'names') ) ) {
            return;
        }

        add_meta_box(
            'lrm_auto_trigger',
            'Login/Registration modal auto-trigger',
            [$this, 'render_metabox'],
            $post_type,
            'normal',
            'default'
        );
    }

    /**
     * Output the HTML for the metabox.
     */
    function render_metabox() {
        // Nonce field to validate form request came from current site
        wp_nonce_field( basename( __FILE__ ) . $this->slug, $this->slug );
    }
    /**
     * Save the metabox data
     */
    function save_meta_box( $post_id, $post ) {
        // Return if the user doesn't have edit permissions.
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return $post_id;
        }
        // Verify this came from the our screen and with proper authorization,
        // because save_post can be triggered at other times.
        if ( ! isset( $_POST[$this->slug] ) || ! wp_verify_nonce( $_POST[$this->slug], basename(__FILE__) . $this->slug ) ) {
            return $post_id;
        }


        foreach ( $this->post_options as $option ) :


            if ( !isset($_POST[$option]) ) {
                continue;
            }

            update_post_meta( $post_id, $option, sanitize_text_field($_POST[$option]) );
        endforeach;
    }


}