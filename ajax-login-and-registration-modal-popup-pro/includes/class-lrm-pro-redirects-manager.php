<?php

defined( 'ABSPATH' ) || exit;

use underDEV\Utils\Settings\CoreFields;
/**
 * Actions/Redirects manager
 *
 * @since      1.50
 * @author     Maxim K <woo.order.review@gmail.com>
 */
class LRM_PRO_Redirects_Manager {

    /**
     * @param string $action    One of: 'login', 'registration', 'logout'
     *
     * @return integer
     */
    public static function get_redirect ( $action = 'login', $user_ID )
    {
        $redirect_to = '';;

        $needed_action = lrm_setting('redirects/' . $action . '/action');

        if ( !in_array($needed_action, ['redirect', 'email-verification-pro', 'email-verification-pro-w-redirect']) ) {
            return '';
        }

        if ( lrm_setting('redirects/' . $action . '/redirect') ) {
            $redirect_settings = LRM_Field_Redirects::_corrected_value( lrm_setting('redirects/' . $action . '/redirect') );

            $user = get_user_by('ID', $user_ID );

            if ( is_wp_error($user) ) {
                do_action('plain_logger', 'Wrong $user object!', __FILE__);
            }

            $roles = (array) $user->roles;

            foreach ($redirect_settings['redirect'] as $redirect_key => $redirect_data) {
                if ( 'default' === $redirect_key || empty($redirect_settings['roles'][$redirect_key]) ) {
                    continue;
                }

                // Check USER Roles
                $role_match = !empty($redirect_settings['role_match'][$redirect_key]) ? $redirect_settings['role_match'][$redirect_key] : 'any_of';

                //echo PHP_EOL, PHP_EOL, "===== comapre ", $role_match, ' for roles ', implode("#" ,$redirect_settings['roles'][$redirect_key]);

                if ( 'any_of' == $role_match && array_intersect($redirect_settings['roles'][$redirect_key], $roles) ) {
                    //var_dump( array_intersect($redirect_settings['roles'][$redirect_key], $roles) );
                    $redirect_to = self::_redirect_url_from_setting( $redirect_settings, $redirect_key, $user );
                    break;
                } elseif ( 'all' == $role_match && ! array_diff($redirect_settings['roles'][$redirect_key], $roles) ) {
                    //var_dump( array_diff($redirect_settings['roles'][$redirect_key], $roles) );
                    $redirect_to = self::_redirect_url_from_setting( $redirect_settings, $redirect_key, $user );
                    break;
                }

            }

            if ( ! $redirect_to ) {
                $redirect_to = self::_redirect_url_from_setting( $redirect_settings, 'default', $user );
            }
        }

        return $redirect_to;
    }


    /**
     * @param string $redirect_settings
     * @param string $key
     * @param WP_User $user
     * @return false|string
     */
    public static function _redirect_url_from_setting( $redirect_settings, $key, $user ) {
        $redirect_to = '';

        if ( 'url' === $redirect_settings['redirect'][$key] && !empty($redirect_settings['redirect_url'][$key]) ) {
            return $redirect_settings['redirect_url'][$key];
        } elseif ( 'page' === $redirect_settings['redirect'][$key] && !empty($redirect_settings['redirect_page'][$key]) ) {
            $page_id = absint( $redirect_settings['redirect_page'][$key] );
            if ( !$page_id ) {
                return $redirect_to;
            }
            return get_permalink($page_id);
        } elseif ( 'wc_account' === $redirect_settings['redirect'][$key] && function_exists('wc_get_account_endpoint_url') ) {
            return wc_get_account_endpoint_url( 'dashboard' );
        } elseif ( 'bp_profile' === $redirect_settings['redirect'][$key] && function_exists('bp_core_get_user_domain') ) {
            return bp_core_get_user_domain( $user->ID );
        }

        return $redirect_to;

    }


    /**
     * @since 1.21
     */
    public static function maybe_logout() {
        if ( isset($_GET['lrm_logout']) && is_user_logged_in() ) {

            do_action("lrm/pre_logout");

//            if ( ! LRM_Settings::get()->setting('general_pro/redirects/silent_logout') ) {
//                add_filter('logout_url', [$this, 'logout_url__filter'], 10, 2);
//                check_admin_referer('log-out');
//            }

            $user = wp_get_current_user();

            $redirect_to = self::_logout_redirect_url($user);
            $redirect_to = apply_filters( 'logout_redirect', $redirect_to, $redirect_to, $user );

            wp_logout();

            wp_safe_redirect( $redirect_to );
            exit();
        }
    }
//
//    /**
//     * @since 1.22
//     */
//    public function logout_url__filter($logout_url, $redirect) {
//        $logout_url = add_query_arg([
//            'lrm_logout'  => true,
//            'redirect_to' => $this->_logout_redirect_url(),
//        ]);
//
//        return wp_nonce_url( $logout_url, 'log-out' );
//    }

    /**
     * @since 1.50
     * @param $user
     * @return int|string|void
     */
    public static function _logout_redirect_url($user) {
        if ( ! empty( $_REQUEST['redirect_to'] ) ) {
            $redirect_to = $requested_redirect_to = $_REQUEST['redirect_to'];
        }
//        else {
//            $redirect_to = LRM_Settings::get()->setting('general_pro/redirects/url_after_logout');
//            $requested_redirect_to = '';
//        }

        $redirect_to = false;
        $logout_action = lrm_setting('redirects/logout/action');
        if ( 'none' === $logout_action ) {
            $redirect_to = add_query_arg( 'lrm_logout', false );
        } elseif ( 'home' === $logout_action ) {
            $redirect_to = home_url('/');
        } elseif ( 'redirect' === $logout_action ) {
            $redirect_to = LRM_Redirects_Manager::get_redirect('logout', $user->ID);
        }

        $redirect_to = add_query_arg( 'lrm_loggedout', 'true', $redirect_to );

        return $redirect_to;
    }

}
