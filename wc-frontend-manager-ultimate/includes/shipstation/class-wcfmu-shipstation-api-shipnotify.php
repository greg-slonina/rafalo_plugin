<?php

class WCFMu_ShipStation_Api_ShipNotify extends WCFMu_ShipStation_Api_Request {

    /**
    * WCFM Vendor object
    *
    * @var null|WP_User
    */
    public $vendor = null;

    /**
    * Class constructor
    *
    * @param bool    $authenticated
    * @param WP_User $vendor
    */
    public function __construct( $authenticated, $vendor ) {
        if ( ! $authenticated ) {
            exit;
        }

        $this->vendor = $vendor;
    }

    /**
    * Handling the request.
    *
    * @return void
    */
    public function request() {
        $this->validate_input( array( 'order_number', 'carrier' ) );

        $wcfm_shipstation_setting = get_user_meta( $this->vendor->ID, 'wcfm_shipstation_setting', true );
        $wcfm_shipstation_setting = $wcfm_shipstation_setting ? $wcfm_shipstation_setting : array();
        // $shipped_status = !empty( $wcfm_shipstation_setting['shipped_status'] ) ? $wcfm_shipstation_setting['shipped_status'] : 'completed';

        $timestamp          = current_time( 'timestamp' );
        $shipstation_xml    = $this->get_raw_post_data();
        $shipped_items      = array();
        $shipped_item_count = 0;
        $order_shipped      = false;
        $xml_order_id       = 0;

        $can_parse_xml = true;

        if ( empty( $shipstation_xml ) ) {
            $can_parse_xml = false;
            $this->log( __( 'Missing ShipNotify XML input.', 'wc-frontend-manager-ultimate' ) );

            // For unknown reason raw post data can be empty. Log all requests
            // information might help figuring out the culprit.
            //
            // @see https://github.com/woocommerce/woocommerce-shipstation/issues/80.
            $this->log( '$_REQUEST: ' . print_r( $_REQUEST, true ) );
        }

        if ( ! function_exists( 'simplexml_import_dom' ) ) {
            $can_parse_xml = false;
            $this->log( __( 'Missing SimpleXML extension for parsing ShipStation XML.', 'wc-frontend-manager-ultimate' ) );
        }

        // Try to parse XML first since it can contain the real OrderID.
        if ( $can_parse_xml ) {
            $this->log( __( 'ShipNotify XML: ', 'wc-frontend-manager-ultimate' ) . print_r( $shipstation_xml, true ) );

            $xml = $this->get_parsed_xml( $shipstation_xml );

            if ( ! $xml ) {
                $this->log( __( 'Cannot parse XML', 'wc-frontend-manager-ultimate' ) );
                status_header( 500 );
            }

            if ( isset( $xml->ShipDate ) ) {
                $timestamp = strtotime( (string) $xml->ShipDate );
            }

            if ( isset( $xml->OrderID ) && $_GET['order_number'] !== (string) $xml->OrderID ) {
                $xml_order_id = (int) $xml->OrderID;
            }
        }

        // Get real order ID from XML otherwise try to convert it from the order number.
        $order_id        = ! $xml_order_id ? $this->get_order_id( wc_clean( $_GET['order_number'] ) ) : $xml_order_id;
        $tracking_number = empty( $_GET['tracking_number'] ) ? '' : wc_clean( $_GET['tracking_number'] );
        $carrier         = empty( $_GET['carrier'] ) ? '' : wc_clean( $_GET['carrier'] );
        $order           = wc_get_order( $order_id );
        $shop_name       = wcfm_get_vendor_store( absint( $this->vendor->ID ) );

        if ( false === $order || ! is_object( $order ) ) {
            $this->log( sprintf( __( 'Order %s can not be found.', 'wc-frontend-manager-ultimate' ), $order_id ) );
            exit;
        }

        // Get real order ID from order object.
        $order_id = version_compare( WC_VERSION, '3.0.0', '<' ) ? $order->id : $order->get_id();
        if ( empty( $order_id ) ) {
            $this->log( sprintf( __( 'Invalid order ID: %s', 'wc-frontend-manager-ultimate' ), $order_id ) );
            exit;
        }

        // Maybe parse items from posted XML (if exists).
        if ( $can_parse_xml && isset( $xml->Items ) ) {
            $items = $xml->Items;
            if ( $items ) {
                foreach ( $items->Item as $item ) {
                    $this->log( __( 'ShipNotify Item: ', 'wc-frontend-manager-ultimate' ) . print_r( $item, true ) );

                    $item_sku    = wc_clean( (string) $item->SKU );
                    $item_name   = wc_clean( (string) $item->Name );
                    $qty_shipped = absint( $item->Quantity );

                    if ( $item_sku ) {
                        $item_sku = ' (' . $item_sku . ')';
                    }

                    $item_id = wc_clean( (int) $item->LineItemID );
                    if ( ! $this->is_shippable_item( $order, $item_id ) ) {
                        $this->log( sprintf( __( 'Item %s is not shippable product. Skipping.', 'wc-frontend-manager-ultimate' ), $item_name ) );
                        continue;
                    }

                    $shipped_item_count += $qty_shipped;
                    $shipped_items[] = $item_name . $item_sku . ' x ' . $qty_shipped;
                }
            }
        }

        // Number of items in WC order.
        $total_item_count = $this->order_items_to_ship_count( $order, $this->vendor->ID );

        // If we have a list of shipped items, we can customise the note + see
        // if the order is not yet complete.
        if ( sizeof( $shipped_items ) > 0 ) {
            $order_note = sprintf(
                /* translators: 1) shipped items 2) carrier's name 3) shipped date, 4) tracking number */
                __( '%1$s shipped via %2$s on %3$s with tracking number %4$s by <b>%5$s</b>.', 'wc-frontend-manager-ultimate' ),
                esc_html( implode( ', ', $shipped_items ) ),
                esc_html( $carrier ),
                date_i18n( get_option( 'date_format' ), $timestamp ),
                $tracking_number,
                $shop_name
            );

            $current_shipped_items = max( (int) get_post_meta( $order_id, '_shipstation_shipped_item_count_'.$this->vendor->ID, true ), 0 );

            if ( ( $current_shipped_items + $shipped_item_count ) >= $total_item_count ) {
                $order_shipped = true;
            }

            $this->log(
                sprintf(
                    /* translators: 1) number of shipped items 2) total shipped items 3) order ID */
                    __( 'Shipped %1$d out of %2$d items in order %3$s by <b>%4$s</b>', 'wc-frontend-manager-ultimate' ),
                    $shipped_item_count,
                    $total_item_count,
                    $order_id,
                    $shop_name
                    )
                );

                update_post_meta( $order_id, '_shipstation_shipped_item_count_'.$this->vendor->ID, $current_shipped_items + $shipped_item_count );

        } else {
            // If we don't have items from SS and order items in WC, or cannot parse
            // the XML, just complete the order as a whole.
            $order_shipped = 0 === $total_item_count || ! $can_parse_xml;

            $order_note = sprintf(
                /* translators: 1) carrier's name 2) shipped date, 3) tracking number */
                __( 'Items shipped via %1$s on %2$s with tracking number %3$s by <b>%4$s</b> (ShipStation).', 'wc-frontend-manager-ultimate' ),
                esc_html( $carrier ),
                date_i18n( get_option( 'date_format' ), $timestamp ),
                $tracking_number,
                $shop_name
            );

            $this->log( sprintf( __( 'No items found - shipping entire order %d by <b>%s</b>.', 'wc-frontend-manager-ultimate' ), $order_id, $shop_name ) );
        }

            // // Tracking information - WC Shipment Tracking extension.
            // if ( class_exists( 'WC_Shipment_Tracking' ) ) {
            //     if ( function_exists( 'wc_st_add_tracking_number' ) ) {
            //         wc_st_add_tracking_number( $order_id, $tracking_number, strtolower( $carrier ), $timestamp );
            //     } else {
            //         // You're using Shipment Tracking < 1.4.0. Please update!
            //         update_post_meta( $order_id, '_tracking_provider', strtolower( $carrier ) );
            //         update_post_meta( $order_id, '_tracking_number', $tracking_number );
            //         update_post_meta( $order_id, '_date_shipped', $timestamp );
            //     }
            //
            //     $is_customer_note = 0;
            // } else {
            //     $is_customer_note = 1;
            // }

            $order->add_order_note( $order_note, 1 );

            $shipstation_data = array(
                'tracking_number' => $tracking_number,
                'carrier'         => $carrier,
                'ship_date'       => $timestamp,
                'xml'             => $shipstation_xml,
            );

            // Update order status.
            if ( $order_shipped ) {
                shipstation_order_mark_shipped( $order_id, $this->vendor->ID, $shipstation_data );

                // shipstation_vendor_order_status_update( $this->vendor->ID, $order_id, $shipped_status );

                /* translators: 1) order ID 2) shipment status */
                // $this->log( sprintf( __( 'Updated order %1$s to status %2$s by <b>%3$s</b>', 'wc-frontend-manager-ultimate' ), $order_id, $shipped_status, $shop_name ) );
                $this->log( sprintf( __( 'Order %1$s marked shipped by <b>%2$s</b>', 'wc-frontend-manager-ultimate' ), $order_id, $shop_name ) );
            }

            // Trigger action for other integrations.
            do_action( 'wcfmu_shipstation_shipnotify', $order, $shipstation_data, $this->vendor->ID );

            status_header( 200 );
        }

        /**
        * See how many items in the order need shipping.
        *
        * @param WC_Order $order Order object.
        *
        * @return int
        */
        private function order_items_to_ship_count( $order, $vendor_id ) {
            $needs_shipping = 0;

            $line_items = shipstation_valid_line_items( $order->get_items( 'line_item' ), $order->get_id(), $vendor_id );

            foreach ( $line_items as $item_id => $item ) {
                if( version_compare( WC_VERSION, '4.4', '<' ) ) {
                    $product = $order->get_product_from_item( $item );
                } else {
                    $product = $item->get_product();
                }

                if ( is_a( $product, 'WC_Product' ) && $product->needs_shipping() ) {
                    $needs_shipping += $item['qty'];
                }
            }

            return $needs_shipping;
        }

        /**
        * Check whether a given item ID is shippable item.
        *
        * @param WC_Order $order   Order object.
        * @param int      $item_id Item ID.
        *
        * @return bool Returns true if item is shippable product.
        */
        private function is_shippable_item( $order, $item_id ) {
            if ( version_compare( WC_VERSION, '3.0', '>=' ) ) {
                $item = $order->get_item( $item_id );
                if ( ! is_callable( array( $item, 'get_product' ) ) ) {
                    return false;
                }

                $product = $item->get_product();
            } else {
                $items = $order->get_items();
                if ( ! isset( $items[ $item_id ] ) ) {
                    return false;
                }

                $product = $order->get_product_from_item( $items[ $item_id ] );
            }

            if ( ! $product ) {
                return false;
            }

            return $product->needs_shipping();
        }

        /**
        * Get the order ID from the order number.
        *
        * @param string $order_number Order number.
        *
        * @return integer
        */
        private function get_order_id( $order_number ) {
            // Try to match an order number in brackets.
            preg_match( '/\((.*?)\)/', $order_number, $matches );
            if ( is_array( $matches ) && isset( $matches[1] ) ) {
                $order_id = $matches[1];

                // Try to convert number for Sequential Order Number.
            } elseif ( function_exists( 'wc_sequential_order_numbers' ) ) {
                $order_id = wc_sequential_order_numbers()->find_order_by_order_number( $order_number );

                // Try to convert number for Sequential Order Number Pro.
            } elseif ( function_exists( 'wc_seq_order_number_pro' ) ) {
                $order_id = wc_seq_order_number_pro()->find_order_by_order_number( $order_number );

                // Default to not converting order number.
            } else {
                $order_id = $order_number;
            }

            if ( 0 === $order_id ) {
                $order_id = $order_number;
            }

            return apply_filters( 'wcfmu_shipstation_get_order_id', absint( $order_id ) );
        }

        /**
        * Retrieves the raw request data (body).
        *
        * `$HTTP_RAW_POST_DATA` is deprecated in PHP 5.6 and removed in PHP 5.7,
        * it's used here for server that has issue with reading `php://input`
        * stream.
        *
        * @return string Raw request data.
        */
        private function get_raw_post_data() {
            global $HTTP_RAW_POST_DATA;

            if ( ! isset( $HTTP_RAW_POST_DATA ) ) {
                $HTTP_RAW_POST_DATA = file_get_contents( 'php://input' );
            }

            return $HTTP_RAW_POST_DATA;
        }

        /**
        * Get Parsed XML response.
        *
        * @param  string $xml XML.
        *
        * @return string|bool
        */
        private function get_parsed_xml( $xml ) {
            global $WCFMu;

            if ( ! class_exists( 'WCFMu_Safe_DOMDocument' ) ) {
                include_once( $WCFMu->plugin_path . 'includes/shipstation/class-wcfmu-safe-domdocument.php' );
            }

            libxml_use_internal_errors( true );

            $dom     = new WCFMu_Safe_DOMDocument;
            $success = $dom->loadXML( $xml );

            if ( ! $success ) {
                $this->log( 'wpcom_safe_simplexml_load_string(): Error loading XML string' );
                return false;
            }

            if ( isset( $dom->doctype ) ) {
                $this->log( 'wpcom_safe_simplexml_import_dom(): Unsafe DOCTYPE Detected' );
                return false;
            }

            return simplexml_import_dom( $dom, 'SimpleXMLElement' );
        }
    }
