<?php
/**
 * WCFM plugin controllers
 *
 * Plugin XA Subscription Dashboard Controller
 *
 * @author 		WC Lovers
 * @package 	wcfm/controllers/wc_subscriptions
 * @version   4.1.0
 */

class WCFMu_XASubscriptions_Controller {
	
	public function __construct() {
		global $WCFM;
		
		$this->processing();
	}
	
	public function processing() {
		global $WCFM, $wpdb, $_POST;
		
		$wc_get_subscription_status_name = apply_filters( 'wcfmu_subscriptions_menus', array( 'all' => __( 'All', 'wc-frontend-manager-ultimate'), 
																																													'active' => __('Active', 'wc-frontend-manager-ultimate' ), 
																																													'on-hold' => __('On Hold', 'wc-frontend-manager-ultimate' ),
																																													'pending' => __('Pending', 'wc-frontend-manager-ultimate' ),
																																													'cancelled' => __('Cancelled', 'wc-frontend-manager-ultimate' ),
																																													'expired' => __('Expired', 'wc-frontend-manager-ultimate' ),
																																													) );
		
		$length = $_POST['length'];
		$offset = $_POST['start'];
		
		if( isset( $_POST['subscription_product'] ) && !empty( $_POST['subscription_product'] ) ) {
			$include_subscriptions = hforce_get_subscriptions_for_product( $_POST['subscription_product'] );
		} else {
			$include_subscriptions = apply_filters( 'wcfm_wcs_include_subscriptions', '' );
		}
		
		$args = array(
							'posts_per_page'   => $length,
							'offset'           => $offset,
							'category'         => '',
							'category_name'    => '',
							'orderby'          => 'date',
							'order'            => 'DESC',
							'include'          => $include_subscriptions,
							'exclude'          => '',
							'meta_key'         => '',
							'meta_value'       => '',
							'post_type'        => 'hf_shop_subscription',
							'post_mime_type'   => '',
							'post_parent'      => '',
							//'author'	   => get_current_user_id(),
							'post_status'      => array( 'any' ),
							//'suppress_filters' => 0 
						);
		if( isset( $_POST['search'] ) && !empty( $_POST['search']['value'] )) $args['s'] = $_POST['search']['value'];
		if( isset( $_POST['subscription_status'] ) && !empty( $_POST['subscription_status'] ) ) { $args['post_status'] = 'wc-' . $_POST['subscription_status']; }
		if( isset( $_POST['subscription_filter'] ) && !empty( $_POST['subscription_filter'] ) ) { $args['meta_key'] = '_subscription_product_id'; $args['meta_value'] = $_POST['subscription_filter']; }
		
		$args = apply_filters( 'wcfm_subscriptions_args', $args );
		
		$wcfm_subscriptions_array = get_posts( $args );
		
		// Get Product Count
		$subscription_count = 0;
		$filtered_subscription_count = 0;
		$wcfm_subscriptions_count = wp_count_posts('shop_subscription');
		$subscription_count = count($wcfm_subscriptions_array);
		// Get Filtered Post Count
		$args['posts_per_page'] = -1;
		$args['offset'] = 0;
		$wcfm_filterd_subscriptions_array = get_posts( $args );
		$filtered_subscription_count = count($wcfm_filterd_subscriptions_array);
		
		
		// Generate Products JSON
		$wcfm_subscriptions_json = '';
		$wcfm_subscriptions_json = '{
															"draw": ' . $_POST['draw'] . ',
															"recordsTotal": ' . $subscription_count . ',
															"recordsFiltered": ' . $filtered_subscription_count . ',
															"data": ';
		if(!empty($wcfm_subscriptions_array)) {
			$index = 0;
			$wcfm_subscriptions_json_arr = array();
			foreach($wcfm_subscriptions_array as $wcfm_subscriptions_single) {
				$the_subscription = hforce_get_subscription( $wcfm_subscriptions_single->ID );
				$the_order   = wc_get_order( $wcfm_subscriptions_single->post_parent );
				
				// Status
				$wcfm_subscriptions_json_arr[$index][] =  '<span class="subscription-status tips wcicon-status-' . sanitize_title( $the_subscription->get_status( ) ) . ' text_tip" data-tip="' . $wc_get_subscription_status_name[$the_subscription->get_status()] . '"></span>';
				
				// Subscription
				if( apply_filters( 'wcfm_is_allow_subscription_details', true ) ) {
					$subscription_label =  '<a href="' . get_wcfm_subscriptions_manage_url($wcfm_subscriptions_single->ID, $the_subscription) . '" class="wcfm_dashboard_item_title">' . __( '#', 'wc-frontend-manager' ) . $wcfm_subscriptions_single->ID . '</a>';
				} else {
					$subscription_label =  __( '#', 'wc-frontend-manager' ) . $wcfm_subscriptions_single->ID;
				}
				
				if ( $the_subscription->get_user_id() && ( false !== ( $user_info = get_userdata( $the_subscription->get_user_id() ) ) ) ) {
					$subscription_label .= ' by ';
					if( $the_subscription->get_billing_first_name() ) {
						if ( $the_subscription->get_billing_first_name() || $the_subscription->get_billing_last_name() ) {
							$guest_name = esc_html( ucfirst( $the_subscription->get_billing_first_name() ) . ' ' . ucfirst( $the_subscription->get_billing_last_name() ) );
						} elseif ( $user_info->first_name || $user_info->last_name ) {
							$guest_name = esc_html( ucfirst( $user_info->first_name ) . ' ' . ucfirst( $user_info->last_name ) );
						} else {
							$guest_name = esc_html( ucfirst( $user_info->display_name ) );
						}
						$guest_name = apply_filters( 'wcfm_subscription_by_user', $guest_name, $wcfm_subscriptions_single->ID, $wcfm_subscriptions_single->post_parent ); 
						if( apply_filters( 'wcfm_allow_view_customer_email', true ) && $the_subscription->get_billing_email() ) {
							$subscription_label .= '<a href="mailto:' .  $the_subscription->get_billing_email() . '">' . $guest_name . '</a>';
						} else {
							$subscription_label .= $guest_name;
						}
					} else {
						$subscription_label .= sprintf( _x( 'Guest (%s)', 'Guest string with name from subscription order in brackets', 'wc-frontend-manager' ), '&ndash;' );
					}
				} elseif ( $the_subscription->get_billing_first_name() || $the_subscription->get_billing_last_name() ) {
					$guest_name = trim( $the_subscription->get_billing_first_name() . ' ' . $the_subscription->get_billing_last_name() );
					$subscription_label .= ' by ';
					if( apply_filters( 'wcfm_allow_view_customer_email', true ) ) {
						$subscription_label .= '<a href="mailto:' .  $the_subscription->get_billing_email() . '">' . $guest_name . '</a>';
					} else {
						$subscription_label .= $guest_name;
					}
				}
				$wcfm_subscriptions_json_arr[$index][] = $subscription_label;
				
				// Order
				if ( $the_order ) {
					if( apply_filters( 'wcfm_is_allow_order_details', true ) && $WCFM->wcfm_vendor_support->wcfm_is_order_for_vendor( $wcfm_subscriptions_single->post_parent ) ) {
						$wcfm_subscriptions_json_arr[$index][] = '<span class="subscription-orderno"><a href="' . get_wcfm_view_order_url( $wcfm_subscriptions_single->post_parent, $the_order ) . '">#' . $wcfm_subscriptions_single->post_parent . '</a></span><br />' . esc_html( wc_get_order_status_name( $the_order->get_status() ) );
					} else {
						$wcfm_subscriptions_json_arr[$index][] = '<span class="subscription-orderno">#' . $wcfm_subscriptions_single->post_parent . '</span><br /> ' . esc_html( wc_get_order_status_name( $the_order->get_status() ) );
					}
				} else {
					$wcfm_subscriptions_json_arr[$index][] = '&ndash;';
				}
				
				// Items
				$subscription_items = $the_subscription->get_items();
				$item_data = '';
				switch ( count( $subscription_items ) ) {
					case 0 :
						$item_data .= '&ndash;';
						break;
					case 1 :
						foreach ( $subscription_items as $item ) {
							$_product       = apply_filters( 'woocommerce_order_item_product', $the_subscription->get_product_from_item( $item ), $item );
							$product_post   = get_post($_product->get_ID());
							$item_data .= $product_post->post_title;
						}
						break;
					default :
						$item_data .= '<a href="#" class="show_order_items">' . esc_html( apply_filters( 'woocommerce_admin_order_item_count', sprintf( _n( '%d item', '%d items', $the_subscription->get_item_count(), 'xa-woocommerce-subscriptions' ), $the_subscription->get_item_count() ), $the_subscription ) ) . '</a>';
						$item_data .= '<table class="order_items" cellspacing="0">';

						foreach ( $subscription_items as $item ) {
							$_product       = apply_filters( 'woocommerce_order_item_product', $the_subscription->get_product_from_item( $item ), $item );
							$product_post   = get_post($_product->get_ID());
							$item_data .= '<tr><td>'. $product_post->post_title . '</td></tr>';
						}

						$item_data .= '</table>';
						break;
				}
				$wcfm_subscriptions_json_arr[$index][] = $item_data;
				
				// Total
				$total_data = esc_html( strip_tags( $the_subscription->get_formatted_order_total() ) );
				$total_data .= '<br/><small class="meta">' . esc_html( sprintf( __( 'Via %s', 'xa-woocommerce-subscriptions' ), $the_subscription->get_payment_method_to_display() ) ) . '</small>';
				$wcfm_subscriptions_json_arr[$index][] = $total_data;
				
				// Start Date
				$wcfm_subscriptions_json_arr[$index][] = esc_html( $the_subscription->get_date_to_display( 'date_created' ) );
				
				// Trial End
				$wcfm_subscriptions_json_arr[$index][] = esc_html( $the_subscription->get_date_to_display( 'trial_end_date' ) );
				
				// Next Payment Date
				$wcfm_subscriptions_json_arr[$index][] = esc_html( $the_subscription->get_date_to_display( 'next_payment_date' ) );
				
				// Last Payment Date
				$wcfm_subscriptions_json_arr[$index][] = esc_html( $the_subscription->get_date_to_display( 'last_order_date_created' ) );
				
				// End Date
				$wcfm_subscriptions_json_arr[$index][] = esc_html( $the_subscription->get_date_to_display( 'end_date' ) );
				
				// Additional Info
				if ( $the_order ) {
					$wcfm_subscriptions_json_arr[$index][] = apply_filters( 'wcfm_subscriptions_additonal_data', '&ndash;', $wcfm_subscriptions_single->ID, $wcfm_subscriptions_single->post_parent );
				} else {
					$wcfm_subscriptions_json_arr[$index][] = apply_filters( 'wcfm_subscriptions_additonal_data', '&ndash;', $wcfm_subscriptions_single->ID, 0 );
				}
				
				// Action
				$actions = '&ndash;';
				if( apply_filters( 'wcfm_is_allow_subscription_details', true ) ) {
					$actions = apply_filters ( 'wcfm_subscriptions_actions', '<a class="wcfm-action-icon" href="' . get_wcfm_subscriptions_manage_url( $wcfm_subscriptions_single->ID, $the_subscription ) . '"><span class="wcfmfa fa-eye text_tip" data-tip="' . esc_attr__( 'View Details', 'wc-frontend-manager' ) . '"></span></a>', $wcfm_subscriptions_single, $the_subscription );
				}
				$wcfm_subscriptions_json_arr[$index][] = $actions;  
				
				
				$index++;
			}												
		}
		if( !empty($wcfm_subscriptions_json_arr) ) $wcfm_subscriptions_json .= json_encode($wcfm_subscriptions_json_arr);
		else $wcfm_subscriptions_json .= '[]';
		$wcfm_subscriptions_json .= '
													}';
													
		echo $wcfm_subscriptions_json;
	}
}