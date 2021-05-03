<?php

/**
 * Get Order data for a vendor
 *
 * @param int   $vendor_id
 * @param array $args
 *
 * @return array
 */
function wcfmu_shipstation_get_orders( $vendor_id, $args = array() ) {
    global $wpdb;

    $current_time = current_time( 'mysql' );

    $defaults = array(
        'count' => false,
        'start_date' => date( 'Y-m-d 00:00:00', strtotime( $current_time ) ),
        'end_date' => $current_time,
        'status' => null,
        'page' => 1,
        'fields' => array( 'wcfm_orders.*', 'p.post_date_gmt' ),
        'limit' => WCFMu_SHIPSTATION_EXPORT_LIMIT * ( $args['page'] - 1 ),
        'offset' => WCFMu_SHIPSTATION_EXPORT_LIMIT,
    );

    $args = wp_parse_args( $args, $defaults );

    $cache_group = 'wcfmu_vendor_data_' . $vendor_id;
    $cache_key   = 'wcfmu-vendor-orders-' . md5( serialize( $args ) ) . '-' . $vendor_id;
    $orders      = wp_cache_get( $cache_key, $cache_group );

    if ( ! $orders ) {
        $select = implode( ', ', $args['fields'] );

        $where = $wpdb->prepare(
            'wcfm_orders.vendor_id = %d AND p.post_status != %s', $vendor_id, 'trash'
        );

        if ( is_array( $args['status'] ) ) {
            $where .= sprintf( " AND order_status IN ('%s')", implode( "', '", $args['status'] ) );
        } else if ( $args['status'] ) {
            $where .= $wpdb->prepare( ' AND order_status = %s', $args['status'] );
        }

        $where .= $wpdb->prepare( ' AND p.post_date_gmt >= %s AND p.post_date_gmt <= %s', $args['start_date'], $args['end_date'] );

        $select = ! $args['count'] ? "SELECT $select" : "SELECT COUNT(p.ID) as count";
        $from = " FROM {$wpdb->prefix}wcfm_marketplace_orders AS wcfm_orders";
        $join = " LEFT JOIN $wpdb->posts p ON wcfm_orders.order_id = p.ID";
        $where = " WHERE $where";

        if ( ! $args['count'] ) {
            $group_by = ' GROUP BY wcfm_orders.order_id';
            $order_by = ' ORDER BY p.post_date_gmt ASC';
            $limit = $wpdb->prepare( ' LIMIT %d, %d', $args['limit'], $args['offset'] );
        } else {
            $group_by = '';
            $order_by = '';
            $limit = '';
        }

        $sql = $select . $from . $join . $where . $group_by . $order_by . $limit;

        $orders = $wpdb->get_results( $sql );

        wp_cache_set( $cache_key, $orders, $cache_group, HOUR_IN_SECONDS * 2 );
        wcfmu_cache_update_group( $cache_key, $cache_group );
    }

    return $orders;
}

/**
 * Keep record of keys by group name
 *
 * @param string $key
 *
 * @param string $group
 *
 * @return void
 */
function wcfmu_cache_update_group( $key, $group ) {
    $keys = get_option( $group, array() );

    if ( in_array( $key, $keys ) ) {
        return;
    }

    $keys[] = $key;
    update_option( $group, $keys );
}

/**
 * get all commission ids from a order by vendor
 *
 * @param int $vendor_id
 *
 * @param int $order_id
 *
 * @return array
 */
function get_order_commission_ids_by_vendor( $vendor_id, $order_id ) {
    global $wpdb;

    $commission_ids = array();

    $sql = $wpdb->prepare( "SELECT `ID` FROM {$wpdb->prefix}wcfm_marketplace_orders WHERE 1=1 AND `vendor_id` = %d AND `order_id` = %d", $vendor_id, $order_id );

    $result = $wpdb->get_results( $sql, ARRAY_A );

    if( ! empty( $result ) ) {
        $commission_ids = wp_list_pluck( $result, 'ID' );
    }

    return $commission_ids;
}

/**
 * Vendor Order - Main Order Status Update
 *
 * @param int $vendor_id
 *
 * @param int $order_id
 *
 * @param string $order_status
 *
 * @return string json_format
 */
function shipstation_update_order_status( $vendor_id, $order_id, $order_status ) {
    global $WCFM;

    if ( wc_is_order_status( $order_status ) && $order_id ) {
        $order = wc_get_order( $order_id );
        $order->update_status( str_replace('wc-', '', $order_status), '', true );

        // Add Order Note for Log
        $vendor_id = apply_filters( 'wcfm_current_vendor_id', $vendor_id );
        $shop_name =  get_user_by( 'ID', $vendor_id )->display_name;
        if( wcfm_is_vendor( $vendor_id ) ) {
            $shop_name =  wcfm_get_vendor_store( absint( $vendor_id ) );
        }
        $wcfm_messages = sprintf( __( 'Order status updated to <b>%s</b> by <b>%s</b>', 'wc-frontend-manager-ultimate' ), wc_get_order_status_name( str_replace('wc-', '', $order_status) ), $shop_name );
        $is_customer_note = apply_filters( 'wcfm_is_allow_order_update_note_for_customer', '1' );

        if( wcfm_is_vendor( $vendor_id ) ) add_filter( 'woocommerce_new_order_note_data', array( $WCFM->wcfm_marketplace, 'wcfm_update_comment_vendor' ), 10, 2 );
        $comment_id = $order->add_order_note( $wcfm_messages, $is_customer_note);
        if( wcfm_is_vendor( $vendor_id ) ) { add_comment_meta( $comment_id, '_vendor_id', $vendor_id ); }
        if( wcfm_is_vendor( $vendor_id ) ) remove_filter( 'woocommerce_new_order_note_data', array( $WCFM->wcfm_marketplace, 'wcfm_update_comment_vendor' ), 10, 2 );

        $wcfm_messages = sprintf( __( '<b>%s</b> order status updated to <b>%s</b> by <b>%s</b>', 'wc-frontend-manager-ultimate' ), '#<a target="_blank" class="wcfm_dashboard_item_title" href="' . get_wcfm_view_order_url($order_id) . '">' . $order->get_order_number() . '</a>', wc_get_order_status_name( str_replace('wc-', '', $order_status) ), $shop_name );
        $WCFM->wcfm_notification->wcfm_send_direct_message( -2, 0, 1, 0, $wcfm_messages, 'status-update' );

        do_action( 'woocommerce_order_edit_status', $order_id, str_replace('wc-', '', $order_status) );
        do_action( 'wcfm_order_status_updated', $order_id, str_replace('wc-', '', $order_status) );

        return '{"status": true, "message": "' . __( 'Order status updated.', 'wc-frontend-manager-ultimate' ) . '"}';
    }

    return '{"status": false, "message": "' . __( 'Failed to update Order status.', 'wc-frontend-manager-ultimate' ) . '"}';
}

/**
 * Vendor Order - Commission Status Update
 *
 * @param int $vendor_id
 *
 * @param int $order_id
 *
 * @param string $order_status
 *
 * @return string json_format
 */
function shipstation_vendor_order_status_update( $vendor_id, $order_id, $order_status ) {
    global $WCFM, $WCFMmp, $wpdb;

    $order_status = 'wc-' === substr( $order_status, 0, 3 ) ? $order_status : 'wc-'.$order_status;

    if( !wcfm_is_vendor( $vendor_id ) ) return;

    if( !$order_id ) {
        return '{"status": false, "message": "' . __( 'No Order ID found.', 'wc-frontend-manager-ultimate' ) . '"}';
    }

    if( $order_status == 'wc-refunded' ) {
        return '{"status": false, "message": "' . __( 'This status not allowed, please go through Refund Request.', 'wc-frontend-manager-ultimate' ) . '"}';
    }

    if( $order_status == 'wc-shipped' ) {
        return '{"status": false, "message": "' . __( 'This status not allowed, please go through Shipment Tracking.', 'wc-frontend-manager-ultimate' ) . '"}';
    }

    $wcfmmp_marketplace_options   = wcfm_get_option( 'wcfm_marketplace_options', array() );
    $order_sync  = isset( $wcfmmp_marketplace_options['order_sync'] ) ? $wcfmmp_marketplace_options['order_sync'] : 'no';
    if( $order_sync == 'yes' ) {
        return shipstation_update_order_status( $vendor_id, $order_id, $order_status );
    }

    if( $vendor_id ) {
        $order = wc_get_order( $order_id );
        $status = str_replace('wc-', '', $order_status);
        $wpdb->update("{$wpdb->prefix}wcfm_marketplace_orders", array('commission_status' => $status), array('order_id' => $order_id, 'vendor_id' => $vendor_id), array('%s'), array('%d', '%d') );

        // Withdrawal Threshold check by Order Completed date
        if( apply_filters( 'wcfm_is_allow_withdrwal_check_by_order_complete_date', false ) && ( $status == 'completed' ) ) {
            $wpdb->update( "{$wpdb->prefix}wcfm_marketplace_orders", array( 'created' => date( 'Y-m-d H:i:s', current_time( 'timestamp', 0 ) ) ), array( 'order_id' => $order_id, 'vendor_id' => $vendor_id ), array('%s'), array('%d', '%d') );
        }

        do_action( 'wcfmmp_vendor_order_status_updated', $order_id, $order_status, $vendor_id );

        // Add Order Note for Log
        if( apply_filters( 'wcfmmp_is_allow_sold_by_linked', true ) ) {
            $shop_name = wcfm_get_vendor_store( absint($vendor_id) );
        } else {
            $shop_name = wcfm_get_vendor_store_name( absint($vendor_id) );
        }

        // Fetch Product ID
        $is_all_complete = true;
        if( apply_filters( 'wcfm_is_allow_itemwise_notification', true ) ) {
            $sql = 'SELECT product_id  FROM ' . $wpdb->prefix . 'wcfm_marketplace_orders AS commission';
            $sql .= ' WHERE 1=1';
            $sql .= " AND `order_id` = " . $order_id;
            $sql .= " AND `vendor_id` = " . $vendor_id;
            $commissions = $wpdb->get_results( $sql );
            $product_id = 0;
            if( !empty( $commissions ) ) {
                foreach( $commissions as $commission ) {
                    $product_id = $commission->product_id;

                    $wcfm_messages = sprintf( __( 'Order item <b>%s</b> status updated to <b>%s</b> by <b>%s</b>', 'wc-frontend-manager-ultimate' ), '<a target="_blank" class="wcfm_dashboard_item_title" href="' . get_permalink($product_id) . '">' . get_the_title( $product_id ) . '</a>', $WCFMmp->wcfmmp_vendor->wcfmmp_vendor_order_status_name( $order_status ), $shop_name );

                    add_filter( 'woocommerce_new_order_note_data', array( $WCFM->wcfm_marketplace, 'wcfm_update_comment_vendor' ), 10, 2 );
                    $is_customer_note = apply_filters( 'wcfm_is_allow_order_update_note_for_customer', '1' );
                    $comment_id = $order->add_order_note( apply_filters( 'wcfm_order_item_status_update_message', $wcfm_messages, $order_id, $vendor_id, $product_id ), $is_customer_note );
                    add_comment_meta( $comment_id, '_vendor_id', $vendor_id );
                    remove_filter( 'woocommerce_new_order_note_data', array( $WCFM->wcfm_marketplace, 'wcfm_update_comment_vendor' ), 10, 2 );

                    $wcfm_messages = apply_filters( 'wcfm_order_item_status_update_admin_message', sprintf( __( '<b>%s</b> order item <b>%s</b> status updated to <b>%s</b> by <b>%s</b>', 'wc-frontend-manager-ultimate' ), '#<a target="_blank" class="wcfm_dashboard_item_title" href="' . get_wcfm_view_order_url($order_id) . '">' . wcfm_get_order_number( $order_id ) . '</a>', '<a target="_blank" class="wcfm_dashboard_item_title" href="' . get_permalink($product_id) . '">' . get_the_title( $product_id ) . '</a>', $WCFMmp->wcfmmp_vendor->wcfmmp_vendor_order_status_name( $order_status ), $shop_name ), $order_id, $vendor_id, $product_id );
                    $WCFM->wcfm_notification->wcfm_send_direct_message( $vendor_id, 0, 0, 1, $wcfm_messages, 'status-update' );
                }
            }
        } else {
            $wcfm_messages = sprintf( __( 'Order status updated to <b>%s</b> by <b>%s</b>', 'wc-frontend-manager-ultimate' ), $WCFMmp->wcfmmp_vendor->wcfmmp_vendor_order_status_name( $order_status ), $shop_name );

            add_filter( 'woocommerce_new_order_note_data', array( $WCFM->wcfm_marketplace, 'wcfm_update_comment_vendor' ), 10, 2 );
            $is_customer_note = apply_filters( 'wcfm_is_allow_order_update_note_for_customer', '1' );
            $comment_id = $order->add_order_note( apply_filters( 'wcfm_order_item_status_update_message', $wcfm_messages, $order_id, $vendor_id, 0 ), $is_customer_note);
            add_comment_meta( $comment_id, '_vendor_id', $vendor_id );
            remove_filter( 'woocommerce_new_order_note_data', array( $WCFM->wcfm_marketplace, 'wcfm_update_comment_vendor' ), 10, 2 );

            $wcfm_messages = apply_filters( 'wcfm_order_item_status_update_admin_message', sprintf( __( '<b>%s</b> order status updated to <b>%s</b> by <b>%s</b>', 'wc-frontend-manager-ultimate' ), '#<a target="_blank" class="wcfm_dashboard_item_title" href="' . get_wcfm_view_order_url($order_id) . '">' . $order->get_order_number() . '</a>', $WCFMmp->wcfmmp_vendor->wcfmmp_vendor_order_status_name( $order_status ), $shop_name ), $order_id, $vendor_id, 0 );
            $WCFM->wcfm_notification->wcfm_send_direct_message( -2, 0, 1, 0, $wcfm_messages, 'status-update' );
        }

        // Update Main Order status on all Commission Order Status Update
        if( in_array( $status, apply_filters( 'wcfm_change_main_order_on_child_order_statuses', array( 'completed', 'processing' ) ) ) && apply_filters( 'wcfm_is_allow_mark_complete_main_order_on_all_child_order_complete', true ) ) {
            if ( wc_is_order_status( 'wc-'.$status ) && $order_id ) {

                // Check is all vendor orders completed or not
                $is_all_complete = true;
                $sql = 'SELECT commission_status  FROM ' . $wpdb->prefix . 'wcfm_marketplace_orders AS commission';
                $sql .= ' WHERE 1=1';
                $sql .= " AND `order_id` = " . $order_id;
                $commissions = $wpdb->get_results( $sql );
                if( !empty( $commissions ) ) {
                    foreach( $commissions as $commission ) {
                        if( $commission->commission_status != $status ) {
                            $is_all_complete = false;
                        }
                    }
                }

                if( $is_all_complete ) {
                    $order->update_status( $status, '', true );

                    // Add Order Note for Log
                    $wcfm_messages = sprintf( __( '<b>%s</b> order status updated to <b>%s</b>', 'wc-frontend-manager-ultimate' ), '#' . $order->get_order_number(), wc_get_order_status_name( $status ) );
                    $is_customer_note = apply_filters( 'wcfm_is_allow_order_update_note_for_customer', '1' );

                    $comment_id = $order->add_order_note( $wcfm_messages, $is_customer_note );

                    $WCFM->wcfm_notification->wcfm_send_direct_message( -2, 0, 1, 0, $wcfm_messages, 'status-update' );

                    do_action( 'woocommerce_order_edit_status', $order_id, $status );
                    do_action( 'wcfm_order_status_updated', $order_id, $status );
                }
            }
        }

        return '{"status": true, "message": "' . __( 'Order status updated.', 'wc-frontend-manager-ultimate' ) . '"}';
    }
}

/**
* Mark WCfM Marketplace order as Shipped
*/
function shipstation_order_mark_shipped( $order_id, $vendor_id, $shipstation_data ) {
    global $WCFM, $WCFMu, $WCFMmp, $woocommerce, $wpdb;

    if ( !empty( $order_id ) ) {

        $order          = wc_get_order( $order_id );

        $order_item_ids = $product_ids = [];

        $line_items = shipstation_valid_line_items( $order->get_items( 'line_item' ), $order_id, $vendor_id );
        foreach ( $line_items as $item_id => $item ) {
            $order_item_ids[] = $item->get_id();
            $product_ids[]    = $item->get_product_id();
        }

        $tracking_url   = '#';
        $tracking_code  = $shipstation_data['tracking_number'];

        $tracking_url = apply_filters( 'wcfm_tracking_url', $tracking_url, $tracking_code, $order_id );

        if( $tracking_code && $tracking_url ) {
            if( wcfm_is_vendor( $vendor_id ) ) {
                $shop_name =  $WCFM->wcfm_vendor_support->wcfm_get_vendor_store_by_vendor( absint($vendor_id) );

                $order_sync  = isset( $WCFMmp->wcfmmp_marketplace_options['order_sync'] ) ? $WCFMmp->wcfmmp_marketplace_options['order_sync'] : 'no';
                if( $order_sync != 'yes' ) {
                    foreach( $order_item_ids as $order_item_id ) {
                        $wpdb->query("UPDATE {$wpdb->prefix}wcfm_marketplace_orders SET commission_status = 'shipped', shipping_status = 'shipped' WHERE order_id = $order_id and vendor_id = $vendor_id and item_id = $order_item_id");
                    }
                }

                if( apply_filters( 'wcfm_is_allow_itemwise_notification', true ) ) {
                    foreach( $product_ids as $product_id ) {
                        $wcfm_messages = apply_filters( 'wcfm_shipment_tracking_message', sprintf( __( 'Vendor <b>%s</b> has shipped <b>%s</b> to customer.<br/>Tracking Info : <a class="wcfm_dashboard_item_title" target="_blank" href="%s">%s</a>', 'wc-frontend-manager-ultimate' ), $shop_name, get_the_title( $product_id ), $tracking_url, $tracking_code ), $tracking_code, $tracking_url, $order_id, $product_id, $vendor_id );
                        $WCFM->wcfm_notification->wcfm_send_direct_message( $vendor_id, 0, 0, 1, $wcfm_messages, 'shipment_tracking' );

                        add_filter( 'woocommerce_new_order_note_data', array( $WCFM->wcfm_marketplace, 'wcfm_update_comment_vendor' ), 10, 2 );
                        $comment_id = $order->add_order_note( $wcfm_messages, apply_filters( 'wcfm_is_allow_shipment_note_to_customer', 0 ) );
                        add_comment_meta( $comment_id, '_vendor_id', $vendor_id );
                        remove_filter( 'woocommerce_new_order_note_data', array( $WCFM->wcfm_marketplace, 'wcfm_update_comment_vendor' ), 10, 2 );
                    }
                } else {
                    $wcfm_messages = apply_filters( 'wcfm_shipment_tracking_message', sprintf( __( 'Vendor <b>%s</b> has shipped <b>%s</b> to customer.<br/>Tracking Info : <a class="wcfm_dashboard_item_title" target="_blank" href="%s">%s</a>', 'wc-frontend-manager-ultimate' ), $shop_name, esc_attr( $order->get_order_number() ), $tracking_url, $tracking_code ), $tracking_code, $tracking_url, $order_id, '', $vendor_id );
                    $WCFM->wcfm_notification->wcfm_send_direct_message( $vendor_id, 0, 0, 1, $wcfm_messages, 'shipment_tracking' );

                    add_filter( 'woocommerce_new_order_note_data', array( $WCFM->wcfm_marketplace, 'wcfm_update_comment_vendor' ), 10, 2 );
                    $comment_id = $order->add_order_note( $wcfm_messages, apply_filters( 'wcfm_is_allow_shipment_note_to_customer', 0 ) );
                    add_comment_meta( $comment_id, '_vendor_id', $vendor_id );
                    remove_filter( 'woocommerce_new_order_note_data', array( $WCFM->wcfm_marketplace, 'wcfm_update_comment_vendor' ), 10, 2 );
                }
            } else {
                foreach( $product_ids as $product_id ) {
                    $comment_id = $order->add_order_note( apply_filters( 'wcfm_shipment_tracking_order_note', sprintf( __( 'Product <b>%s</b> has been shipped to customer.<br/>Tracking Info : <a href="%s">%s</a>', 'wc-frontend-manager-ultimate' ), get_the_title( $product_id ), $tracking_url, $tracking_code ), $tracking_code, $tracking_url, $order_id, $product_id, $vendor_id ), apply_filters( 'wcfm_is_allow_shipment_note_to_customer', '1' ) );
                }
            }
        }

        // Update Shipping Tracking Info
        foreach( $order_item_ids as $index => $order_item_id ) {
            $WCFMu->wcfmu_shipment_tracking->updateShippingTrackingInfo( $order_id, $order_item_id, $tracking_code, $tracking_url, $product_ids[$index] );

            if( !apply_filters( 'wcfm_is_allow_itemwise_notification', true ) )
            break;

            do_action( 'wcfm_after_order_mark_shipped', $order_id, $order_item_id, $tracking_code, $tracking_url, $product_ids[$index], $wcfm_tracking_data );
        }

        if( !apply_filters( 'wcfm_is_allow_itemwise_notification', true ) ) {
            delete_post_meta( $order_id, '_wcfm_order_delivery_assigned_'.$vendor_id );
            foreach( $order_item_ids as $index => $order_item_id ) {
                // Keep Tracking Code as Order Item Meta
                wc_update_order_item_meta( $order_item_id, 'wcfm_tracking_code', $tracking_code );

                // Keep Tracking URL as Order Item Meta
                wc_update_order_item_meta( $order_item_id, 'wcfm_tracking_url', $tracking_url );

                do_action( 'wcfm_after_order_mark_shipped', $order_id, $order_item_id, $tracking_code, $tracking_url, $product_ids[$index], $wcfm_tracking_data );
            }
        }

        // Change Main Order Status if All Item Shipped
        if( apply_filters( 'wcfm_is_allow_change_main_order_status_on_all_item_shipped', false ) ) {
            $tracking_added = false;
            $products = $order->get_items();
            foreach( $products as $product => $item ) {
                $tracking_added = false;
                foreach ( $item->get_formatted_meta_data() as $meta_id => $meta ) {
                    if( $meta->key == 'wcfm_tracking_code' ) {
                        $tracking_added = true;
                    }
                }
                if( !$tracking_added ) break;
            }

            if( $tracking_added ) {
                $order->update_status( apply_filters( 'wcfm_main_order_status_on_all_item_shipped', 'completed' ), '', true );
                do_action( 'woocommerce_order_edit_status', $order_id, apply_filters( 'wcfm_main_order_status_on_all_item_shipped', 'completed' ) );
            }
        }
    }
    die;
}

// Filter Order Details Line Items as Per Vendor
function shipstation_valid_line_items( $items, $order_id, $vendor_id ) {
    global $WCFM, $wpdb;

    $sql = "SELECT `product_id`, `item_id` FROM {$wpdb->prefix}wcfm_marketplace_orders WHERE `vendor_id` = {$vendor_id} AND `order_id` = {$order_id}";
    $valid_products = $wpdb->get_results($sql);
    $valid_items = array();
    if( !empty($valid_products) ) {
        foreach( $valid_products as $valid_product ) {
            $valid_items[] = $valid_product->item_id;
            $valid_items[] = $valid_product->product_id;
        }
    }

    $valid = array();
    foreach ($items as $key => $value) {
        if ( in_array( $value->get_variation_id(), $valid_items ) || in_array( $value->get_product_id(), $valid_items ) || in_array( $value->get_id(), $valid_items ) ) {
            $valid[$key] = $value;
        } elseif( $value->get_product_id() == 0 ) {
            $_product_id = wc_get_order_item_meta( $key, '_product_id', true );
            if ( in_array( $_product_id, $valid_items ) ) {
                $valid[$key] = $value;
            }
        }
    }
    return $valid;
}
