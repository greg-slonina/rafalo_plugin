<?php
/**
 * WCFM plugin controllers
 *
 * Plugin WC Foo Events Products Manage Controller
 *
 * @author 		WC Lovers
 * @package 	wcfmu/controllers/thirdparty
 * @version   5.3.4
 */
class WCFMu_WC_Fooevents_Products_Manage_Controller {
	
	public function __construct() {
		global $WCFM;
		
		// Third Party Product Meta Data Save
    add_action( 'after_wcfm_products_manage_meta_save', array( &$this, 'wcfm_wc_fooevents_products_manage_meta_save' ), 225, 2 );
	}
	
	/**
	 * WC Warranty Field Product Meta data save
	 */
	function wcfm_wc_fooevents_products_manage_meta_save( $new_product_id, $wcfm_products_manage_form_data ) {
		global $WCFM;
		
		global $woocommerce_errors;
		global $wp_locale;
		
		if(isset($wcfm_products_manage_form_data['WooCommerceEventsEvent'])) {
			update_post_meta($new_product_id, 'WooCommerceEventsEvent', $wcfm_products_manage_form_data['WooCommerceEventsEvent']);
		}
		$format = get_option( 'date_format' );
		
		$min = 60 * get_option( 'gmt_offset' );
		$sign = $min < 0 ? "-" : "+";
		$absmin = abs($min);
		try {
			$tz = new DateTimeZone(sprintf("%s%02d%02d", $sign, $absmin/60, $absmin%60));
		} catch(Exception $e) {
			$serverTimezone = date_default_timezone_get();
			$tz = new DateTimeZone($serverTimezone);
		}
		
		$event_date = $wcfm_products_manage_form_data['WooCommerceEventsDate'];
		
		if(isset($event_date)) {
				
			if(isset($wcfm_products_manage_form_data['WooCommerceEventsSelectDate'][0]) && isset($wcfm_products_manage_form_data['WooCommerceEventsMultiDayType']) && $wcfm_products_manage_form_data['WooCommerceEventsMultiDayType'] == 'select') {
				$event_date = $wcfm_products_manage_form_data['WooCommerceEventsSelectDate'][0];
			}
			
			$event_date = str_replace('/', '-', $event_date);
			//$event_date = str_replace(',', '', $event_date);
			
			update_post_meta($new_product_id, 'WooCommerceEventsDate', $wcfm_products_manage_form_data['WooCommerceEventsDate']);
			
			$dtime = DateTime::createFromFormat($format, $event_date, $tz);
			$timestamp = '';
			if ($dtime instanceof DateTime) {
				if(isset($wcfm_products_manage_form_data['WooCommerceEventsHour']) && isset($wcfm_products_manage_form_data['WooCommerceEventsMinutes'])) {
					$dtime->setTime((int)$wcfm_products_manage_form_data['WooCommerceEventsHour'], (int)$wcfm_products_manage_form_data['WooCommerceEventsMinutes']);
				}
				$timestamp = $dtime->getTimestamp();
			} else {
				$timestamp = 0;
			}
			
			update_post_meta($new_product_id, 'WooCommerceEventsDateTimestamp', $timestamp);
		}
		
		if(isset($wcfm_products_manage_form_data['WooCommerceEventsEndDate'])) {
				
			update_post_meta($new_product_id, 'WooCommerceEventsEndDate', $wcfm_products_manage_form_data['WooCommerceEventsEndDate']);
			
			$dtime = DateTime::createFromFormat($format, $wcfm_products_manage_form_data['WooCommerceEventsEndDate'], $tz);
			$timestamp = '';
			if ($dtime instanceof DateTime) {
				if(isset($wcfm_products_manage_form_data['WooCommerceEventsHourEnd']) && isset($wcfm_products_manage_form_data['WooCommerceEventsMinutesEnd'])) {
					$dtime->setTime((int)$wcfm_products_manage_form_data['WooCommerceEventsHourEnd'], (int)$wcfm_products_manage_form_data['WooCommerceEventsMinutesEnd']);
				}
				$timestamp = $dtime->getTimestamp();
			} else {
				$timestamp = 0;
			}
			update_post_meta($new_product_id, 'WooCommerceEventsEndDateTimestamp', $timestamp);
		}
		
		if(isset($wcfm_products_manage_form_data['WooCommerceEventsMultiDayType'])) {
			update_post_meta($new_product_id, 'WooCommerceEventsMultiDayType', $wcfm_products_manage_form_data['WooCommerceEventsMultiDayType']);
		}
		
		if(isset($wcfm_products_manage_form_data['WooCommerceEventsSelectDate'])) {
			update_post_meta($new_product_id, 'WooCommerceEventsSelectDate', $wcfm_products_manage_form_data['WooCommerceEventsSelectDate']);
		}
		
		if(isset($wcfm_products_manage_form_data['WooCommerceEventsNumDays'])) {
			update_post_meta($new_product_id, 'WooCommerceEventsNumDays', $wcfm_products_manage_form_data['WooCommerceEventsNumDays']);
		}
		
		if(isset($wcfm_products_manage_form_data['WooCommerceEventsHour'])) {
			update_post_meta($new_product_id, 'WooCommerceEventsHour', $wcfm_products_manage_form_data['WooCommerceEventsHour']);
		}
		
		if(isset($wcfm_products_manage_form_data['WooCommerceEventsMinutes'])) {
			update_post_meta($new_product_id, 'WooCommerceEventsMinutes', $wcfm_products_manage_form_data['WooCommerceEventsMinutes']);
		}
		
		if(isset($wcfm_products_manage_form_data['WooCommerceEventsPeriod'])) {
			update_post_meta($new_product_id, 'WooCommerceEventsPeriod', $wcfm_products_manage_form_data['WooCommerceEventsPeriod']);
		}
		
		if(isset($wcfm_products_manage_form_data['WooCommerceEventsLocation'])) {
			$WooCommerceEventsLocation = htmlentities(stripslashes($wcfm_products_manage_form_data['WooCommerceEventsLocation']));
			update_post_meta($new_product_id, 'WooCommerceEventsLocation', $WooCommerceEventsLocation);
		}
		
		if(isset($wcfm_products_manage_form_data['WooCommerceEventsTicketLogo'])) {
			update_post_meta($new_product_id, 'WooCommerceEventsTicketLogo', $wcfm_products_manage_form_data['WooCommerceEventsTicketLogo']);
		}
		
		if(isset($wcfm_products_manage_form_data['WooCommerceEventsPrintTicketLogo'])) {
			update_post_meta($new_product_id, 'WooCommerceEventsPrintTicketLogo', $wcfm_products_manage_form_data['WooCommerceEventsPrintTicketLogo']);
		}
		
		if(isset($wcfm_products_manage_form_data['WooCommerceEventsTicketHeaderImage'])) {
			update_post_meta($new_product_id, 'WooCommerceEventsTicketHeaderImage', $wcfm_products_manage_form_data['WooCommerceEventsTicketHeaderImage']);
		}
		
		if(isset($wcfm_products_manage_form_data['WooCommerceEventsTicketText'])) {
			update_post_meta($new_product_id, 'WooCommerceEventsTicketText', $wcfm_products_manage_form_data['WooCommerceEventsTicketText']);
		}
		
		if(isset($wcfm_products_manage_form_data['WooCommerceEventsThankYouText'])) {
			update_post_meta($new_product_id, 'WooCommerceEventsThankYouText', $wcfm_products_manage_form_data['WooCommerceEventsThankYouText']);
		}
		
		if(isset($wcfm_products_manage_form_data['WooCommerceEventsEventDetailsText'])) {
			update_post_meta($new_product_id, 'WooCommerceEventsEventDetailsText', $wcfm_products_manage_form_data['WooCommerceEventsEventDetailsText']);
		}
		
		if(isset($wcfm_products_manage_form_data['WooCommerceEventsSupportContact'])) {
			$WooCommerceEventsSupportContact = htmlentities(stripslashes($wcfm_products_manage_form_data['WooCommerceEventsSupportContact']));
			update_post_meta($new_product_id, 'WooCommerceEventsSupportContact', $WooCommerceEventsSupportContact);
		}
		
		if(isset($wcfm_products_manage_form_data['WooCommerceEventsHourEnd'])) {
			update_post_meta($new_product_id, 'WooCommerceEventsHourEnd', $wcfm_products_manage_form_data['WooCommerceEventsHourEnd']);
		}
		
		if(isset($wcfm_products_manage_form_data['WooCommerceEventsMinutesEnd'])) {
			update_post_meta($new_product_id, 'WooCommerceEventsMinutesEnd', $wcfm_products_manage_form_data['WooCommerceEventsMinutesEnd']);
		}
		
		if(isset($wcfm_products_manage_form_data['WooCommerceEventsEndPeriod'])) {
			update_post_meta($new_product_id, 'WooCommerceEventsEndPeriod', $wcfm_products_manage_form_data['WooCommerceEventsEndPeriod']);
		}
		
		if( isset($wcfm_products_manage_form_data['WooCommerceEventsAddEventbrite']) ) {
			if ( WCFMu_Dependencies::wcfm_wc_fooevents_calendar() ) {
		    update_post_meta($new_product_id, 'WooCommerceEventsAddEventbrite', $wcfm_products_manage_form_data['WooCommerceEventsAddEventbrite']);
				$FooEvents_Calendar = new FooEvents_Calendar();
				$FooEvents_Calendar->process_eventbrite($new_product_id);
			}
		}
		
		if(isset($wcfm_products_manage_form_data['WooCommerceEventsGPS'])) {
			$WooCommerceEventsGPS = htmlentities(stripslashes($wcfm_products_manage_form_data['WooCommerceEventsGPS']));
			update_post_meta($new_product_id, 'WooCommerceEventsGPS',  htmlentities(stripslashes($wcfm_products_manage_form_data['WooCommerceEventsGPS'])));
		}
		
		if(isset($wcfm_products_manage_form_data['WooCommerceEventsDirections'])) {
			$WooCommerceEventsDirections = htmlentities(stripslashes($wcfm_products_manage_form_data['WooCommerceEventsDirections']));
			update_post_meta($new_product_id, 'WooCommerceEventsDirections', $WooCommerceEventsDirections);
		}
		
		if(isset($wcfm_products_manage_form_data['WooCommerceEventsEmail'])) {
			$WooCommerceEventsEmail = esc_textarea($wcfm_products_manage_form_data['WooCommerceEventsEmail']);
			update_post_meta($new_product_id, 'WooCommerceEventsEmail', $WooCommerceEventsEmail);
		}
		
		if(isset($wcfm_products_manage_form_data['WooCommerceEventsTicketBackgroundColor'])) {
			update_post_meta($new_product_id, 'WooCommerceEventsTicketBackgroundColor', $wcfm_products_manage_form_data['WooCommerceEventsTicketBackgroundColor']);
		}
		
		if(isset($wcfm_products_manage_form_data['WooCommerceEventsTicketButtonColor'])) {
			update_post_meta($new_product_id, 'WooCommerceEventsTicketButtonColor', $wcfm_products_manage_form_data['WooCommerceEventsTicketButtonColor']);
		}
		
		if(isset($wcfm_products_manage_form_data['WooCommerceEventsTicketTextColor'])) {
			update_post_meta($new_product_id, 'WooCommerceEventsTicketTextColor', $wcfm_products_manage_form_data['WooCommerceEventsTicketTextColor']);
		}
		
		if(isset($wcfm_products_manage_form_data['WooCommerceEventsBackgroundColor'])) {
			update_post_meta($new_product_id, 'WooCommerceEventsBackgroundColor', $wcfm_products_manage_form_data['WooCommerceEventsBackgroundColor']);
		}
		
		if(isset($wcfm_products_manage_form_data['WooCommerceEventsTextColor'])) {
			update_post_meta($new_product_id, 'WooCommerceEventsTextColor', $wcfm_products_manage_form_data['WooCommerceEventsTextColor']);
		}
		
		if(isset($wcfm_products_manage_form_data['WooCommerceEventsGoogleMaps'])) {
			update_post_meta($new_product_id, 'WooCommerceEventsGoogleMaps', $wcfm_products_manage_form_data['WooCommerceEventsGoogleMaps']);
		}
		if(isset($wcfm_products_manage_form_data['WooCommerceEventsTicketPurchaserDetails'])) {
			update_post_meta($new_product_id, 'WooCommerceEventsTicketPurchaserDetails', $wcfm_products_manage_form_data['WooCommerceEventsTicketPurchaserDetails']);
		} else {
			update_post_meta($new_product_id, 'WooCommerceEventsTicketPurchaserDetails', 'off');
		}
		
		if(isset($wcfm_products_manage_form_data['WooCommerceEventsTicketAddCalendar'])) {
			update_post_meta($new_product_id, 'WooCommerceEventsTicketAddCalendar', $wcfm_products_manage_form_data['WooCommerceEventsTicketAddCalendar']);
		} else {
			update_post_meta($new_product_id, 'WooCommerceEventsTicketAddCalendar', 'off');
		}
		
		if(isset($wcfm_products_manage_form_data['WooCommerceEventsTicketDisplayDateTime'])) {
			update_post_meta($new_product_id, 'WooCommerceEventsTicketDisplayDateTime', $wcfm_products_manage_form_data['WooCommerceEventsTicketDisplayDateTime']);
		} else {
			update_post_meta($new_product_id, 'WooCommerceEventsTicketDisplayDateTime', 'off');
		}
		
		if(isset($wcfm_products_manage_form_data['WooCommerceEventsTicketDisplayBarcode'])) {
			update_post_meta($new_product_id, 'WooCommerceEventsTicketDisplayBarcode', $wcfm_products_manage_form_data['WooCommerceEventsTicketDisplayBarcode']);
		} else {
			update_post_meta($new_product_id, 'WooCommerceEventsTicketDisplayBarcode', 'off');
		}
		
		if(isset($wcfm_products_manage_form_data['WooCommerceEventsTicketDisplayPrice'])) {
			update_post_meta($new_product_id, 'WooCommerceEventsTicketDisplayPrice', $wcfm_products_manage_form_data['WooCommerceEventsTicketDisplayPrice']);
		} else {
			update_post_meta($new_product_id, 'WooCommerceEventsTicketDisplayPrice', 'off');
		}
		
		if(isset($wcfm_products_manage_form_data['WooCommerceEventsIncludeCustomAttendeeDetails'])) {
			update_post_meta($new_product_id, 'WooCommerceEventsIncludeCustomAttendeeDetails', $wcfm_products_manage_form_data['WooCommerceEventsIncludeCustomAttendeeDetails']);
		} else {
			update_post_meta($new_product_id, 'WooCommerceEventsIncludeCustomAttendeeDetails', 'off');
		}
		
		if(isset($wcfm_products_manage_form_data['WooCommerceEventsCaptureAttendeeDetails'])) {
			update_post_meta($new_product_id, 'WooCommerceEventsCaptureAttendeeDetails', $wcfm_products_manage_form_data['WooCommerceEventsCaptureAttendeeDetails']);
		} else {
			update_post_meta($new_product_id, 'WooCommerceEventsCaptureAttendeeDetails', 'off');
		}
		
		if(isset($wcfm_products_manage_form_data['WooCommerceEventsEmailAttendee'])) {
			update_post_meta($new_product_id, 'WooCommerceEventsEmailAttendee', $wcfm_products_manage_form_data['WooCommerceEventsEmailAttendee']);
		} else {
			update_post_meta($new_product_id, 'WooCommerceEventsEmailAttendee', 'off');
		}
		
		if(isset($wcfm_products_manage_form_data['WooCommerceEventsCaptureAttendeeTelephone'])) {
			update_post_meta($new_product_id, 'WooCommerceEventsCaptureAttendeeTelephone', $wcfm_products_manage_form_data['WooCommerceEventsCaptureAttendeeTelephone']);
		} else {
			update_post_meta($new_product_id, 'WooCommerceEventsCaptureAttendeeTelephone', 'off');
		}
		
		if(isset($wcfm_products_manage_form_data['WooCommerceEventsCaptureAttendeeCompany'])) {
			update_post_meta($new_product_id, 'WooCommerceEventsCaptureAttendeeCompany', $wcfm_products_manage_form_data['WooCommerceEventsCaptureAttendeeCompany']);
		} else {
			update_post_meta($new_product_id, 'WooCommerceEventsCaptureAttendeeCompany', 'off');
		}
		
		if(isset($wcfm_products_manage_form_data['WooCommerceEventsCaptureAttendeeDesignation'])) {
			update_post_meta($new_product_id, 'WooCommerceEventsCaptureAttendeeDesignation', $wcfm_products_manage_form_data['WooCommerceEventsCaptureAttendeeDesignation']);
		} else {
			update_post_meta($new_product_id, 'WooCommerceEventsCaptureAttendeeDesignation', 'off');
		}
		
		if(isset($wcfm_products_manage_form_data['WooCommerceEventsSendEmailTickets'])) {
			update_post_meta($new_product_id, 'WooCommerceEventsSendEmailTickets', $wcfm_products_manage_form_data['WooCommerceEventsSendEmailTickets']);
		} else {
			update_post_meta($new_product_id, 'WooCommerceEventsSendEmailTickets', 'off');
		}
		
		if(isset($wcfm_products_manage_form_data['WooCommerceEventsEmailSubjectSingle'])) {
			$WooCommerceEventsEmailSubjectSingle = htmlentities($wcfm_products_manage_form_data['WooCommerceEventsEmailSubjectSingle']);
			//$WooCommerceEventsEmailSubjectSingle = sanitize_text_field($wcfm_products_manage_form_data['WooCommerceEventsEmailSubjectSingle']);
			
			update_post_meta($new_product_id, 'WooCommerceEventsEmailSubjectSingle', $WooCommerceEventsEmailSubjectSingle);
		} else {
			update_post_meta($new_product_id, 'WooCommerceEventsEmailSubjectSingle', '{OrderNumber} Ticket');
		}
		
		if(isset($wcfm_products_manage_form_data['WooCommerceEventsExportUnpaidTickets'])) {
			update_post_meta($new_product_id, 'WooCommerceEventsExportUnpaidTickets', $wcfm_products_manage_form_data['WooCommerceEventsExportUnpaidTickets']);
		} else {
			update_post_meta($new_product_id, 'WooCommerceEventsExportUnpaidTickets', 'off');
		}
		
		if(isset($wcfm_products_manage_form_data['WooCommerceEventsExportBillingDetails'])) {
			update_post_meta($new_product_id, 'WooCommerceEventsExportBillingDetails', $wcfm_products_manage_form_data['WooCommerceEventsExportBillingDetails']);
		} else {
			update_post_meta($new_product_id, 'WooCommerceEventsExportBillingDetails', 'off');
		}
		
		if(isset($wcfm_products_manage_form_data['WooCommerceBadgeSize'])) {
			update_post_meta($new_product_id, 'WooCommerceBadgeSize', $wcfm_products_manage_form_data['WooCommerceBadgeSize']);
		} 
		if(isset($wcfm_products_manage_form_data['WooCommerceBadgeField1'])) {
			update_post_meta($new_product_id, 'WooCommerceBadgeField1', $wcfm_products_manage_form_data['WooCommerceBadgeField1']);
		} 
		
		if(isset($wcfm_products_manage_form_data['WooCommerceBadgeField2'])) {
			update_post_meta($new_product_id, 'WooCommerceBadgeField2', $wcfm_products_manage_form_data['WooCommerceBadgeField2']);
		} 
		
		if(isset($wcfm_products_manage_form_data['WooCommerceBadgeField3'])) {
			update_post_meta($new_product_id, 'WooCommerceBadgeField3', $wcfm_products_manage_form_data['WooCommerceBadgeField3']);
		} 
		
		if(isset($wcfm_products_manage_form_data['WooCommerceEventsCutLines'])) {
			update_post_meta($new_product_id, 'WooCommerceEventsCutLines', $wcfm_products_manage_form_data['WooCommerceEventsCutLines']);
		} else {
			update_post_meta($new_product_id, 'WooCommerceEventsCutLines', 'off');
		}
		
		if(isset($wcfm_products_manage_form_data['WooCommercePrintTicketSize'])) {
			update_post_meta($new_product_id, 'WooCommercePrintTicketSize', $wcfm_products_manage_form_data['WooCommercePrintTicketSize']);
		} 
		
		if(isset($wcfm_products_manage_form_data['WooCommercePrintTicketField1'])) {
			update_post_meta($new_product_id, 'WooCommercePrintTicketField1', $wcfm_products_manage_form_data['WooCommercePrintTicketField1']);
		}
		
		if(isset($wcfm_products_manage_form_data['WooCommercePrintTicketField1_font'])) {
			update_post_meta($new_product_id, 'WooCommercePrintTicketField1_font', $wcfm_products_manage_form_data['WooCommercePrintTicketField1_font']);
		}
		
		if(isset($wcfm_products_manage_form_data['WooCommercePrintTicketField2'])) {
			update_post_meta($new_product_id, 'WooCommercePrintTicketField2', $wcfm_products_manage_form_data['WooCommercePrintTicketField2']);
		}
		
		if(isset($wcfm_products_manage_form_data['WooCommercePrintTicketField2_font'])) {
			update_post_meta($new_product_id, 'WooCommercePrintTicketField2_font', $wcfm_products_manage_form_data['WooCommercePrintTicketField2_font']);
		}
		
		if(isset($wcfm_products_manage_form_data['WooCommercePrintTicketField3'])) {
			update_post_meta($new_product_id, 'WooCommercePrintTicketField3', $wcfm_products_manage_form_data['WooCommercePrintTicketField3']);
		}
		
		if(isset($wcfm_products_manage_form_data['WooCommercePrintTicketField3_font'])) {
			update_post_meta($new_product_id, 'WooCommercePrintTicketField3_font', $wcfm_products_manage_form_data['WooCommercePrintTicketField3_font']);
		}
		if(isset($wcfm_products_manage_form_data['WooCommercePrintTicketField4'])) {
			update_post_meta($new_product_id, 'WooCommercePrintTicketField4', $wcfm_products_manage_form_data['WooCommercePrintTicketField4']);
		}
		
		if(isset($wcfm_products_manage_form_data['WooCommercePrintTicketField4_font'])) {
			update_post_meta($new_product_id, 'WooCommercePrintTicketField4_font', $wcfm_products_manage_form_data['WooCommercePrintTicketField4_font']);
		}
		
		if(isset($wcfm_products_manage_form_data['WooCommercePrintTicketField5'])) {
			update_post_meta($new_product_id, 'WooCommercePrintTicketField5', $wcfm_products_manage_form_data['WooCommercePrintTicketField5']);
		}
		
		if(isset($wcfm_products_manage_form_data['WooCommercePrintTicketField5_font'])) {
			update_post_meta($new_product_id, 'WooCommercePrintTicketField5_font', $wcfm_products_manage_form_data['WooCommercePrintTicketField5_font']);
		}
		
		if(isset($wcfm_products_manage_form_data['WooCommercePrintTicketField6'])) {
		 update_post_meta($new_product_id, 'WooCommercePrintTicketField6', $wcfm_products_manage_form_data['WooCommercePrintTicketField6']);
		}
		
		if(isset($wcfm_products_manage_form_data['WooCommercePrintTicketField6_font'])) {
			update_post_meta($new_product_id, 'WooCommercePrintTicketField6_font', $wcfm_products_manage_form_data['WooCommercePrintTicketField6_font']);
		}
		
		if(isset($wcfm_products_manage_form_data['WooCommerceEventsPrintTicketLogoOption'])) {
			update_post_meta($new_product_id, 'WooCommerceEventsPrintTicketLogoOption', $wcfm_products_manage_form_data['WooCommerceEventsPrintTicketLogoOption']);
		}
		
		if(isset($wcfm_products_manage_form_data['WooCommerceEventsCutLinesPrintTicket'])) {
			update_post_meta($new_product_id, 'WooCommerceEventsCutLinesPrintTicket', $wcfm_products_manage_form_data['WooCommerceEventsCutLinesPrintTicket']);
		} else {
			update_post_meta($new_product_id, 'WooCommerceEventsCutLinesPrintTicket', 'off');
		}
		
		if(isset($wcfm_products_manage_form_data['WooCommerceEventsTicketTheme'])) {
			update_post_meta($new_product_id, 'WooCommerceEventsTicketTheme', $wcfm_products_manage_form_data['WooCommerceEventsTicketTheme']);
		} 
		
		if(isset($wcfm_products_manage_form_data['WooCommerceEventsAttendeeOverride'])) {
			update_post_meta($new_product_id, 'WooCommerceEventsAttendeeOverride', $wcfm_products_manage_form_data['WooCommerceEventsAttendeeOverride']);
		}
		
		if(isset($wcfm_products_manage_form_data['WooCommerceEventsTicketOverride'])) {
			update_post_meta($new_product_id, 'WooCommerceEventsTicketOverride', $wcfm_products_manage_form_data['WooCommerceEventsTicketOverride']);
		}
		
		if(isset($wcfm_products_manage_form_data['WooCommerceEventsDayOverride'])) {
			update_post_meta($new_product_id, 'WooCommerceEventsDayOverride', $wcfm_products_manage_form_data['WooCommerceEventsDayOverride']);
		}
		
		if(isset($wcfm_products_manage_form_data['WooCommerceEventsViewSeatingChart'])) {
			update_post_meta($new_product_id, 'WooCommerceEventsViewSeatingChart', $wcfm_products_manage_form_data['WooCommerceEventsViewSeatingChart']);
		} else {
			update_post_meta($new_product_id, 'WooCommerceEventsViewSeatingChart', 'off');
		}
		
		if(isset($wcfm_products_manage_form_data['fooevents_custom_attendee_fields_options_serialized'])) {
			$fooevents_custom_attendee_fields_options_serialized = $wcfm_products_manage_form_data['fooevents_custom_attendee_fields_options_serialized'];
			update_post_meta($new_product_id, 'fooevents_custom_attendee_fields_options_serialized', $fooevents_custom_attendee_fields_options_serialized);
		}
		
		if(isset($wcfm_products_manage_form_data['fooevents_seating_options_serialized'])) {
			$fooevents_seating_options_serialized = $wcfm_products_manage_form_data['fooevents_seating_options_serialized'];
			update_post_meta($new_product_id, 'fooevents_seating_options_serialized', $fooevents_seating_options_serialized);
		}
		
		if(isset($wcfm_products_manage_form_data['FooEventsTicketFooterText'])) {
			update_post_meta($new_product_id, 'FooEventsTicketFooterText', $wcfm_products_manage_form_data['FooEventsTicketFooterText']);
		}
		
		if(isset($wcfm_products_manage_form_data['FooEventsPDFTicketsEmailText'])) {
			update_post_meta($new_product_id, 'FooEventsPDFTicketsEmailText', $wcfm_products_manage_form_data['FooEventsPDFTicketsEmailText']);
		}
	}
}