<?php

abstract class WCFMu_ShipStation_Api_Request {

    /**
     * Is logging enabled or not
     *
     * @var null
     */
    private $logging_enabled = null;

    /**
     * Stores logger class
     *
     * @since 1.0.0
     *
     * @var WC_Logger
     */
    private $log = null;

    /**
     * Log something
     *
     * @param string $message
     */
    public function log( $message ) {
        if ( is_null ( $this->logging_enabled ) ) {
            $this->logging_enabled = wcfm_get_option( 'enable_shipstation_logging', 'yes' );
        }

        if ( 'no' === $this->logging_enabled ) {
            return;
        }

        if ( is_null( $this->log ) ) {
            $this->log = new WC_Logger();
        }

        $this->log->add( 'wcfm-shipstation', $message );
    }

    /**
     * Run the request
     *
     * @return string
     */
    public function request() {}

    /**
     * Validate data
     *
     * @param array $required_fields fields to look for
     *
     * @return void
     */
    public function validate_input( $required_fields ) {
        foreach ( $required_fields as $required ) {
            if ( empty( $_GET[ $required ] ) ) {
                $this->trigger_error( sprintf( __( 'Missing required param: %s', 'wc-frontend-manager-ultimate' ), $required ) );
            }
        }
    }

    /**
     * Trigger and log an error
     *
     * @param string $message
     *
     * @return void
     */
    public function trigger_error( $message ) {
        $this->log( $message );
        wp_send_json_error( $message );
    }
}
