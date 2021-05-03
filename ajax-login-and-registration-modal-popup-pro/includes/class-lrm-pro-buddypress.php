<?php

/**
 * BuddyPress Registration class
 *
 * @since 1.19
 *
 * Class LRM_Pro_BuddyPress
 */
class LRM_Pro_BuddyPress {

    static function is_buddypress_active() {
        return function_exists("buddypress");
    }

    /**
     * Display Form
     */
    static function render_registration_form() {

        add_filter( 'bp_is_current_component', function($is_current_component, $component) {
            return 'register' == $component ? true : $is_current_component;
        }, 20, 2 );

        add_action( 'bp_custom_signup_steps', function() {
            echo '<input type="hidden" name="lrm_action" value="bp_signup">',
            PHP_EOL,
            '<input type="hidden" name="signup_submit" value="1">',
            '<input type="hidden" name="wp-submit" value="1">';
        } );


        require_once buddypress()->plugin_dir . 'bp-members/screens/register.php';
        buddypress()->signup->step = 'request-details';

        bp_core_load_template( apply_filters( 'bp_core_template_register', array( 'register', 'registration/register' ) ) );

        echo '<div class="lrm-form" action="#0" data-action="bp-registration">
                <div id="buddypress" class="buddypress-wrap extended-default-reg">';
            echo bp_buffer_template_part( 'members/register', null, false );
        echo    '</div>
             </div>';

        do_action("bp_enqueue_scripts");

    }

    /**
     * Process AJAX registration
     */
	static function AJAX_signup() {

        add_filter( 'bp_is_current_component', function($is_current_component, $component) {
            return 'register' === $component ? true : $is_current_component;
        }, 20, 2 );

        $bp = buddypress();

        require_once $bp->plugin_dir . 'bp-members/screens/register.php';
        //add_filter( 'bp_is_current_component', '__return_true' );

        $bp->signup->step = 'request-details';

        add_action('bp_signup_validate', function() {
            $bp = buddypress();
            if ( !empty( $bp->signup->errors ) ) {

                $message = '';

                foreach ( (array) $bp->signup->errors as $fieldname => $error_message ) {
                    /**
                     * Filters the error message in the loop.
                     *
                     * @since 1.5.0
                     *
                     * @param string $value Error message wrapped in html.
                     */
                    $message .= apply_filters( 'bp_members_signup_error_message', "<div class=\"error\">" . $error_message . "</div>" );
                }

                wp_send_json_error( array(
                    'logged_in' => false,
                    'message'   => $message,
                ) );

            }
        });

        add_action('bp_complete_signup', function() {
            $bp = buddypress();

            if ( $bp->signup->step == 'completed-confirmation' ) {
				if ( bp_registration_needs_activation() ) :
                    $message = __('You have successfully created your account! To begin using this site you will need to activate your account via the email we have just sent to your address.', 'buddypress' );
				else :
					$message = __( 'You have successfully created your account! Please log in using the username and password you have just created.', 'buddypress' );
				endif;

				// Mya be do not use "bp_registration_needs_activation"?
                //if ( ! LRM_Settings::get()->setting('general/registration/user_must_confirm_email') ) {

//                    $username = $bp->signup->username;
//                    $user = get_user_by( 'login', $username );
//
//                    wp_clear_auth_cookie();
//                    wp_set_current_user ( $user->ID );
//                    wp_set_auth_cookie  ( $user->ID );
//
//                    wp_send_json_success( array(
//                        'logged_in' => true,
//                        'message'   => $message,
//                    ) );


                    wp_send_json_success( array(
                        'logged_in' => false,
                        'message'   => $message,
                    ) );

            } elseif ( $bp->signup->step == 'request-details' ) {

                wp_send_json_error( array(
                    'logged_in' => false,
                    'message'   => $bp->template_message,
                ) );

            }
        });

        bp_core_screen_signup();
	}

}