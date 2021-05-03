<?php
/**
 * WCFM plugin controllers
 *
 * Plugin FooEvents Tickets Controller
 *
 * @author 		WC Lovers
 * @package 	wcfmu/controllers/thirdparty
 * @version   6.1.1
 */

class WCFMu_Event_Tickets_Controller {
	
	public function __construct() {
		global $WCFM;
		
		$this->processing();
	}
	
	public function processing() {
		global $WCFM, $WCFMu, $wpdb, $_POST;
		
		$length = $_POST['length'];
		$offset = $_POST['start'];
		
		$args = array(
							'posts_per_page'   => $length,
							'offset'           => $offset,
							'category'         => '',
							'category_name'    => '',
							'orderby'          => 'date',
							'order'            => 'DESC',
							'include'          => '',
							'exclude'          => '',
							'meta_key'         => '',
							'meta_value'       => '',
							'post_type'        => 'event_magic_tickets',
							'post_mime_type'   => '',
							'post_parent'      => '',
							//'author'	   => get_current_user_id(),
							'post_status'      => array('draft', 'pending', 'publish'),
							'suppress_filters' => 0 
						);
		
		if( isset( $_POST['search'] ) && !empty( $_POST['search']['value'] )) {
			$args['s'] = $_POST['search']['value'];
		}
		
		// Event Filter
		if( isset($_POST['ticket_event']) && !empty($_POST['ticket_event']) ) {
			$args['meta_key'] = 'WooCommerceEventsProductID';
			$args['meta_value'] = absint( $_POST['ticket_event'] );
		}
		
		$for_count_args = $args;
		
		$args = apply_filters( 'wcfm_event_tickets_args', $args );
		
		remove_all_filters( 'pre_get_posts' );
		
		$wcfm_event_tickets_array = get_posts( $args );
		
		$event_ticket_count = 0;
		$filtered_event_ticket_count = 0;
		if( wcfm_is_vendor() ) {
			// Get Event_ticket Count
			$for_count_args['posts_per_page'] = -1;
			$for_count_args['offset'] = 0;
			$for_count_args = apply_filters( 'wcfm_event_tickets_args', $for_count_args );
			$wcfm_event_tickets_count = get_posts( $for_count_args );
			$event_ticket_count = count($wcfm_event_tickets_count);
			
			// Get Filtered Post Count
			$args['posts_per_page'] = -1;
			$args['offset'] = 0;
			$wcfm_filterd_event_tickets_array = get_posts( $args );
			$filtered_event_ticket_count = count($wcfm_filterd_event_tickets_array);
		} else {
			// Get Event_ticket Count
			$wcfm_event_tickets_counts = wp_count_posts('event_magic_tickets');
			foreach($wcfm_event_tickets_counts as $wcfm_event_tickets_type => $wcfm_event_tickets_count ) {
				if( in_array( $wcfm_event_tickets_type, array( 'publish', 'draft', 'pending' ) ) ) {
					$event_ticket_count += $wcfm_event_tickets_count;
				}
			}
			
			// Get Filtered Post Count
			$filtered_event_ticket_count = $event_ticket_count; 
		}
		
		$config = new FooEvents_Config();
		require_once($config->classPath.'barcodehelper.php');
    $barcodeHelper = new FooEvents_Barcode_Helper($config);
		
		// Generate Products JSON
		$wcfm_event_tickets_json = '';
		$wcfm_event_tickets_json = '{
														"draw": ' . $_POST['draw'] . ',
														"recordsTotal": ' . $event_ticket_count . ',
														"recordsFiltered": ' . $filtered_event_ticket_count . ',
														"data": ';
		
		if ( !empty( $wcfm_event_tickets_array ) ) {
			$index = 0;
			$totals = 0;
			$wcfm_event_tickets_json_arr = array();
			
			foreach ( $wcfm_event_tickets_array as $wcfm_event_tickets_single ) {
				
				$productID   = get_post_meta( $wcfm_event_tickets_single->ID, 'WooCommerceEventsProductID', true );
				$order_id    = get_post_meta( $wcfm_event_tickets_single->ID, 'WooCommerceEventsOrderID', true );
        $customer_id = get_post_meta( $wcfm_event_tickets_single->ID, 'WooCommerceEventsCustomerID', true );
        $order = array();
        try {
            $order = new WC_Order( $order_id );
        } catch (Exception $e) {
            
        }   
        
        $WooCommerceEventsTicketID    = get_post_meta( $wcfm_event_tickets_single->ID, 'WooCommerceEventsTicketID', true );
        $WooCommerceEventsTicketHash  = get_post_meta( $wcfm_event_tickets_single->ID, 'WooCommerceEventsTicketHash', true );
        
        $barcodeFileName = '';
        
        if(!empty($WooCommerceEventsTicketHash)) {
					$barcodeFileName = $WooCommerceEventsTicketHash.'-'.$WooCommerceEventsTicketID;
        } else {
					$barcodeFileName = $WooCommerceEventsTicketID;
        }
	
				// Ticket
				$wcfm_event_tickets_json_arr[$index][] =  apply_filters( 'wcfm_fooevents_ticket_status', '<span class="wcfm_dashboard_item_title">' . $wcfm_event_tickets_single->post_title . '</span>', $order_id, $productID );
				
				// Event
				$wcfm_event_tickets_json_arr[$index][] =  '<a class="wcfm_event_tickets_event" target="_blank" href="' . get_wcfm_edit_product_url( $productID ) . '">' . get_the_title( $productID ) . '</a>';
				
				// Purchaser
				$purchaser_str = '';
				if(empty($order)) {
					 $purchaser_str = "<i>Warning: WooCommerce order has been deleted.</i><br />"; 
				}
				
				if(!empty($customer_id) && !($customer_id instanceof WP_Error)) {
					$WooCommerceEventsPurchaserFirstName = get_post_meta( $wcfm_event_tickets_single->ID, 'WooCommerceEventsPurchaserFirstName', true );
					$WooCommerceEventsPurchaserLastName = get_post_meta( $wcfm_event_tickets_single->ID, 'WooCommerceEventsPurchaserLastName', true );
					$WooCommerceEventsPurchaserEmail = get_post_meta( $wcfm_event_tickets_single->ID, 'WooCommerceEventsPurchaserEmail', true );
					if( apply_filters( 'wcfm_is_allow_view_customer', true ) ) {
						$purchaser_str .= '<a class="wcfm_event_tickets_customer" href="' . get_wcfm_customers_details_url($customer_id) . '">' . apply_filters( 'wcfm_customers_display_name_data', $WooCommerceEventsPurchaserFirstName.' '.$WooCommerceEventsPurchaserLastName, $customer_id ) . '</a>';
					} else {
						$purchaser_str .= apply_filters( 'wcfm_customers_display_name_data', $WooCommerceEventsPurchaserFirstName.' '.$WooCommerceEventsPurchaserLastName, $customer_id );
					}
					$purchaser_str .= '<br />('.$WooCommerceEventsPurchaserEmail.')';
				} else {
					//guest account
					try {
						if(!empty($order)) {
							$purchaser_str .= $order->get_billing_first_name().' '.$order->get_billing_last_name();
							if( apply_filters( 'wcfm_allow_view_customer_email', true ) ) {
								$purchaser_str .= '<br />('.$order->get_billing_email().')';
							}
						}
					} catch (Exception $e) {
					}   
				}
				$wcfm_event_tickets_json_arr[$index][] = $purchaser_str;
				
				// Attendee
				$WooCommerceEventsAttendeeName = get_post_meta( $wcfm_event_tickets_single->ID, 'WooCommerceEventsAttendeeName', true );
				$WooCommerceEventsAttendeeLastName = get_post_meta( $wcfm_event_tickets_single->ID, 'WooCommerceEventsAttendeeLastName', true );
				$WooCommerceEventsAttendeeEmail = get_post_meta( $wcfm_event_tickets_single->ID, 'WooCommerceEventsAttendeeEmail', true );
				$wcfm_event_tickets_json_arr[$index][] = apply_filters( 'wcfm_fooevents_ticket_attendee_details', $WooCommerceEventsAttendeeName . ' ' . $WooCommerceEventsAttendeeLastName . ' - ' . $WooCommerceEventsAttendeeEmail, $wcfm_event_tickets_single->ID );
				
				// Status
				$WooCommerceEventsNumDays = (int)get_post_meta( $productID, 'WooCommerceEventsNumDays', true );
				$WooCommerceEventsMultidayStatus = '';
				$WooCommerceEventsStatus = get_post_meta( $wcfm_event_tickets_single->ID, 'WooCommerceEventsStatus', true );
				
				if( WCFMu_Dependencies::wcfm_wc_fooevents_multiday() && $WooCommerceEventsNumDays > 1 ) {
					$Fooevents_Multiday_Events = new Fooevents_Multiday_Events();
					$WooCommerceEventsMultidayStatus = $Fooevents_Multiday_Events->display_multiday_status_ticket_meta_all( $wcfm_event_tickets_single->ID );
				}
				
				if(empty($WooCommerceEventsMultidayStatus) || $WooCommerceEventsStatus == 'Unpaid' || $WooCommerceEventsStatus == 'Canceled' || $WooCommerceEventsStatus == 'Cancelled') {
					$wcfm_event_tickets_json_arr[$index][] = $WooCommerceEventsStatus;
				} else {
					$wcfm_event_tickets_json_arr[$index][] = $WooCommerceEventsMultidayStatus;
				}
				
				// Date
				$wcfm_event_tickets_json_arr[$index][] = date_i18n( wc_date_format() . ' ' . wc_time_format(), strtotime( $wcfm_event_tickets_single->post_date ) );
				
				// Barcode Option
				$barcodeURL =  $config->barcodeURL;
        if (!file_exists($config->barcodePath.$WooCommerceEventsTicketID.'.png')) {
          $barcodeHelper->generate_barcode($WooCommerceEventsTicketID, $WooCommerceEventsTicketHash);
        }
        $barcodeHelper->generate_barcode($WooCommerceEventsTicketID, $WooCommerceEventsTicketHash);
				
				$actions = '<a class="wcfm-action-icon wcfm_linked_images" target="_blank" href="'.$barcodeURL.$barcodeFileName.'.png"><span class="wcfmfa fa-barcode text_tip" data-tip="' . esc_attr__( 'Barcode', 'wc-frontend-manager-ultimate' ) . '"></span></a>';
				
				// Ticket Download
				if ( WCFMu_Dependencies::wcfm_wc_fooevents_pdfticket() ) {
					$FooEvents_PDF_Tickets = new FooEvents_PDF_Tickets();
					$WooCommerceEventsTicketID = get_post_meta( $wcfm_event_tickets_single->ID, 'WooCommerceEventsTicketID', true );
					$WooCommerceEventsTicketHash = get_post_meta( $wcfm_event_tickets_single->ID, 'WooCommerceEventsTicketHash', true );
	
					$fileName = '';
					if(!empty($WooCommerceEventsTicketHash)) {
						$fileName = $WooCommerceEventsTicketHash.'-'.$WooCommerceEventsTicketID.'-'.$WooCommerceEventsTicketHash.'-'.$WooCommerceEventsTicketID;
					} else {
						$fileName = $WooCommerceEventsTicketID.'-'.$WooCommerceEventsTicketID;
					}
					
					$urlPath = $config->pdfTicketURL.$fileName.'.pdf';
					$filePath = $config->pdfTicketPath.$fileName.'.pdf';
	
					if( !file_exists( $filePath ) ) {
						$ticket = array();
						$FooEvents = new FooEvents();
						$ticket_data = $FooEvents->get_ticket_data( $wcfm_event_tickets_single->ID );
						$ticket[] = $ticket_data;
						
						$FooEvents_PDF_Tickets->generate_ticket( $productID, $ticket, $config->barcodePath, $config->eventPluginURL );
					}
					
					$actions .= '<a class="wcfm-action-icon wcfm_linked_tickets" target="_blank" href="'.$urlPath.'"><span class="wcfmfa fa-download text_tip" data-tip="' . esc_attr__( 'Download Ticket', 'wc-frontend-manager-ultimate' ) . '"></span></a>';
				}
				
				$actions .= '<a class="wcfm-action-icon wcfm_resend_tickets" target="_blank" data-ticket="'.$wcfm_event_tickets_single->ID.'" href="#"><span class="wcfmfa fa-envelope text_tip" data-tip="' . esc_attr__( 'Resend Ticket', 'wc-frontend-manager-ultimate' ) . '"></span></a>';
				
				// Action
				$wcfm_event_tickets_json_arr[$index][] =  apply_filters ( 'wcfm_event_tickets_actions', $actions, $wcfm_event_tickets_single );
				
				$index++;
			}
		}
		if( !empty($wcfm_event_tickets_json_arr) ) $wcfm_event_tickets_json .= json_encode($wcfm_event_tickets_json_arr);
		else $wcfm_event_tickets_json .= '[]';
		$wcfm_event_tickets_json .= '
													}';
													
		echo $wcfm_event_tickets_json;
	}
}