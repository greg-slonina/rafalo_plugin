<?php

/**
 * @see https://help.shipstation.com/hc/en-us/articles/205928478#2 Documentation on ShipStation request endpoints
 */
class WCFMu_ShipStation_Api extends WCFMu_ShipStation_Api_Request {

    /**
     * Stores whether or not shipstation has been authenticated
     *
     * @var bool
     */
    private static $authenticated = false;

    /**
     * Class constructor
     *
     * @return void
     */
    public function __construct() {
        nocache_headers();

        if ( ! defined( 'DONOTCACHEPAGE' ) ) {
            define( "DONOTCACHEPAGE", "true" );
        }

        if ( ! defined( 'DONOTCACHEOBJECT' ) ) {
            define( "DONOTCACHEOBJECT", "true" );
        }

        if ( ! defined( 'DONOTCACHEDB' ) ) {
            define( "DONOTCACHEDB", "true" );
        }

        self::$authenticated = false;

        $this->request();
    }

    /**
     * Has API been authenticated?
     *
     * @return bool
     */
    public static function authenticated() {
        return self::$authenticated;
    }

    /**
     * Handle the request
     *
     * @return void
     */
    public function request() {
        global $WCFMu;

        if ( empty( $_GET['auth_key'] ) ) {
            $this->trigger_error( __( 'Authentication key is required!', 'wc-frontend-manager-ultimate' ) );
        }

        $auth_key = sanitize_text_field( $_GET['auth_key'] );

        $args = array(
            'role'       => 'wcfm_vendor',
            'meta_key'   => 'shipstation_auth_key',
            'meta_value' => $auth_key,
        );

        $user_query = new WP_User_Query( $args );
        $vendors = $user_query->get_results();

        if ( empty( $vendors ) ) {
            $this->trigger_error( __( 'Invalid authentication key', 'wc-frontend-manager-ultimate' ) );
        }

        $vendor = array_pop( $vendors );

        $request = $_GET;

        if ( isset( $request['action'] ) ) {
            $this->request = array_map( 'sanitize_text_field', $request );
        } else {
            $this->trigger_error( __( 'Invalid request', 'wc-frontend-manager-ultimate' ) );
        }

        self::$authenticated = true;

        if ( in_array( $this->request['action'], array( 'export', 'shipnotify' ) ) ) {
            $this->log( sprintf( __( 'Input params: %s', 'wc-frontend-manager-ultimate' ), http_build_query( $this->request ) ) );

            if ( 'export' === $this->request['action'] ) {
                require_once $WCFMu->plugin_path . 'includes/shipstation/class-wcfmu-shipstation-api-export.php';
                $request_class = new WCFMu_ShipStation_Api_Export( self::$authenticated, $vendor );

            } else {
                require_once $WCFMu->plugin_path . 'includes/shipstation/class-wcfmu-shipstation-api-shipnotify.php';
                $request_class = new WCFMu_ShipStation_Api_ShipNotify( self::$authenticated, $vendor );
            }

            $request_class->request();

        } else {
            $this->trigger_error( __( 'Invalid request', 'wc-frontend-manager-ultimate' ) );
        }

        exit;
    }
}
