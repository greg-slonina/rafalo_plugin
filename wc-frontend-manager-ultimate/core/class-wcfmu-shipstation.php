<?php
/**
* WCFM plugin core
*
* Shipstation core
*
* @author 		WC Lovers
* @package 	wcfmu/core
* @version   5.1.5
*/

class WCFMu_Shipstation {

    public function __construct() {
        $this->define_constants();

        $wcfm_shipstation_setting = get_option( 'wcfm_shipstation_setting', array() );

        // WCFM Shipstation Setting
        add_action( 'end_wcfm_vendor_settings', array( &$this, 'wcfm_shipstation_setting' ) );

        // WCFM Shipstation Setting Save
        add_action( 'wcfm_vendor_settings_update', array( &$this, 'wcfm_shipstation_setting_save' ), 150, 2 );

        // webhook listener to connect to ShipStation
        // see https://docs.woocommerce.com/document/wc_api-the-woocommerce-api-callback/
        add_action( 'woocommerce_api_wc_shipstation', array( &$this, 'wcfmu_shipstation_api' ) );
    }

    /**
    * Module constants
    *
    * @return void
    */
    private function define_constants() {
        define( 'WCFMu_SHIPSTATION_EXPORT_LIMIT', 100 );
    }

    /**
    * Generate read-only auth key for ShipStation
    *
    * @param int $user_id
    *
    * @return string
    */
    public function generate_key( $user_id ) {
        $to_hash = $user_id . date( 'U' ) . mt_rand();
        return apply_filters( 'wcfm_shipstation_auth_key', 'MARKETPLACESS-' . hash_hmac( 'md5', $to_hash, wp_hash( $to_hash ) ), $user_id );
    }

    /**
    * Shipstation Admin Setting
    */
    public function wcfm_shipstation_setting( $user_id ) {
        global $WCFM;

        $wcfm_shipstation_setting = get_user_meta( $user_id, 'wcfm_shipstation_setting', true );
        $wcfm_shipstation_setting = $wcfm_shipstation_setting ? $wcfm_shipstation_setting : array();

        $statuses = wcfmu_shipstation_get_order_status();

        $auth_key = get_user_meta( $user_id, 'shipstation_auth_key', true );

        if ( ! $auth_key ) {
            $auth_key = $this->generate_key( $user_id );
            update_user_meta( $user_id, 'shipstation_auth_key', $auth_key );
        }

        $export_statuses = !empty( $wcfm_shipstation_setting['export_statuses'] ) ? $wcfm_shipstation_setting['export_statuses'] : array();
        // $shipped_status = !empty( $wcfm_shipstation_setting['shipped_status'] ) ? $wcfm_shipstation_setting['shipped_status'] : 'completed';

        ?>
        <!-- collapsible -->
        <div class="page_collapsible" id="wcfm_settings_form_shipstation_head">
            <label class="wcfmfa fa-cog"></label>
            <?php _e('ShipStation', 'wc-frontend-manager-ultimate'); ?><span></span>
        </div>
        <div class="wcfm-container">
            <div id="wcfm_settings_form_shipstation_expander" class="wcfm-content">
                <h2><?php _e('ShipStation Setting', 'wc-frontend-manager-ultimate'); ?></h2>
                <?php //wcfm_video_tutorial( 'https://docs.wclovers.com/shipstation/' ); ?>
                <div class="wcfm_clearfix"></div>

                <?php
                $WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_settings_fields_shipstation', array(
                    "wcfm_shipstation_setting_auth_key" => array(
                        'label'       => __('Authentication Key', 'wc-frontend-manager-ultimate') ,
                        'name'        => 'wcfm_shipstation_setting[auth_key]',
                        'type'        => 'text',
                        'class'       => 'wcfm-text wcfm_ele wcfm_shipstation_field',
                        'value'       => $auth_key,
                        'label_class' => 'wcfm_title wcfm_shipstation_field',
                        'desc_class'  => 'wcfm_page_options_desc wcfm_shipstation_field',
                        'desc'        => __( 'Copy and paste this key into ShipStation during setup.', 'wc-frontend-manager-ultimate' ),
                        'attributes'  => array(
                            'readonly'  => 'readonly',
                        ),
                        'hints'       => __( 'This is the <code>Auth Key</code> you set in ShipStation and allows ShipStation to communicate with your store.', 'wc-frontend-manager-ultimate' ),
                    ),
                    "wcfm_shipstation_setting_export_statuses"     => array(
                        'label'       => __('Export Order Statuses', 'wc-frontend-manager-ultimate') ,
                        'name'        => 'wcfm_shipstation_setting[export_statuses]',
                        'type'        => 'select',
                        'options'     => $statuses,
                        'class'       => 'wcfm-select wcfm_ele',
                        'value'       => $export_statuses,
                        'label_class' => 'wcfm_title',
                        'attributes'  => array(
                            'multiple'  => 'multiple'
                        ),
                        'custom_attributes'  => array(
                            'required'  => 'required',
                        ),
                        'hints'       => __( 'Define the order statuses you wish to export to ShipStation.', 'wc-frontend-manager-ultimate' ),
                    ),
                    // "wcfm_shipstation_setting_shipped_status"     => array(
                    //     'label'       => __('Shipped Order Status', 'wc-frontend-manager-ultimate') ,
                    //     'name'        => 'wcfm_shipstation_setting[shipped_status]',
                    //     'type'        => 'select',
                    //     'options'     => $statuses,
                    //     'class'       => 'wcfm-select wcfm_ele',
                    //     'value'       => $shipped_status,
                    //     'label_class' => 'wcfm_title',
                    //     'custom_attributes'  => array(
                    //         'required'  => 'required',
                    //     ),
                    //     'hints'       => __( 'Define the order status you wish to update to once an order has been shipping via ShipStation. By default this is Completed.', 'wc-frontend-manager-ultimate' ),
                    // ),
                ) ) );
                ?>
            </div>
        </div>
        <div class="wcfm_clearfix"></div>
        <!-- end collapsible -->
        <?php
    }

    public function wcfm_shipstation_setting_save( $user_id, $wcfm_settings_form  ) {
        if( isset( $wcfm_settings_form['wcfm_shipstation_setting'] ) ) {
            // auth key generated for the first time only & can not be changed later
            unset( $wcfm_settings_form['wcfm_shipstation_setting']['auth_key'] );
            update_user_meta( $user_id, 'wcfm_shipstation_setting', $wcfm_settings_form['wcfm_shipstation_setting'] );
        }
    }

    /**
     * Listen for API requests.
     *
     * @return void
     */
    public function wcfmu_shipstation_api() {
        global $WCFMu;

        include_once $WCFMu->plugin_path . 'includes/shipstation/functions.php';
        include_once $WCFMu->plugin_path . 'includes/shipstation/abstract-class-wcfmu-shipstation-api-request.php';
        include_once $WCFMu->plugin_path . 'includes/shipstation/class-wcfmu-shipstation-api.php';

        new WCFMu_ShipStation_Api();
    }
}
