<?php
/**
 * WCFM plugin view
 *
 * WCFM WC Foo Events Product Manage View
 *
 * @author 		WC Lovers
 * @package 	wcfmu/views/thirdparty
 * @version   5.3.4
 */
 
global $wp, $WCFM, $WCFMu, $woocommerce;

if( !apply_filters( 'wcfm_is_allow_wc_fooevents', true ) ) {
	return;
}

$product_id = 0;

$WooCommerceEventsEvent                     = '';
$WooCommerceEventsDate                      = '';
$WooCommerceEventsHour                      = '';
$WooCommerceEventsPeriod                    = '';
$WooCommerceEventsMinutes                   = '';
$WooCommerceEventsHourEnd                   = '';
$WooCommerceEventsMinutesEnd                = '';
$WooCommerceEventsEndPeriod                 = '';
$WooCommerceEventsLocation                  = '';
$WooCommerceEventsTicketLogo                = '';
$WooCommerceEventsPrintTicketLogo           = '';
$WooCommerceEventsTicketHeaderImage         = '';
$WooCommerceEventsSupportContact            = '';
$WooCommerceEventsGPS                       = '';
$WooCommerceEventsGoogleMaps                = '';
$WooCommerceEventsDirections                = '';
$WooCommerceEventsEmail                     = '';
$WooCommerceEventsTicketBackgroundColor     = '';
$WooCommerceEventsTicketButtonColor         = '';
$WooCommerceEventsTicketTextColor           = '';
$WooCommerceEventsTicketPurchaserDetails    = '';
$WooCommerceEventsTicketAddCalendar         = '';
$WooCommerceEventsTicketDisplayDateTime     = '';
$WooCommerceEventsTicketDisplayBarcode      = '';

$WooCommerceEventsTicketDisplayPrice            = '';
$WooCommerceEventsTicketText                    = '';
$WooCommerceEventsThankYouText                  = '';
$WooCommerceEventsEventDetailsText              = '';
$WooCommerceEventsCaptureAttendeeDetails        = '';
$WooCommerceEventsEmailAttendee                 = '';
$WooCommerceEventsSendEmailTickets              = '';
$WooCommerceEventsCaptureAttendeeTelephone      = '';
$WooCommerceEventsCaptureAttendeeCompany        = '';
$WooCommerceEventsCaptureAttendeeDesignation    = '';

$WooCommerceEventsViewSeatingChart              = '';

$WooCommerceEventsExportUnpaidTickets           = '';
$WooCommerceEventsExportBillingDetails          = '';

$WooCommerceBadgeSize                           = '';
$WooCommerceBadgeField1                         = '';
$WooCommerceBadgeField2                         = '';
$WooCommerceBadgeField3                         = '';

$WooCommercePrintTicketSize                     = '';
$WooCommercePrintTicketField1                   = '';
$WooCommercePrintTicketField1_font              = '';
$WooCommercePrintTicketField2                   = '';
$WooCommercePrintTicketField2_font              = '';
$WooCommercePrintTicketField3                   = '';
$WooCommercePrintTicketField3_font              = '';
$WooCommercePrintTicketField4                   = '';
$WooCommercePrintTicketField4_font              = '';
$WooCommercePrintTicketField5                   = '';
$WooCommercePrintTicketField5_font              = '';
$WooCommercePrintTicketField6                   = '';
$WooCommercePrintTicketField6_font              = '';

$WooCommerceEventsPrintTicketLogoOption         = '';
$WooCommerceEventsCutLinesPrintTicket           = '';


$WooCommerceEventsCutLines                      = '';

$WooCommerceEventsEmailSubjectSingle            = '';
$WooCommerceEventsTicketTheme                   = '';

$WooCommerceEventsAttendeeOverride              = '';
$WooCommerceEventsTicketOverride                = '';

$WooCommerceEventsViewSeatingChart              = '';

$globalWooCommerceEventsGoogleMapsAPIKey        = '';

$WooCommerceEventsEmailSubjectSingle = __('{OrderNumber} Ticket', 'woocommerce-events');

$globalWooCommerceEventsTicketBackgroundColor   = '';
$globalWooCommerceEventsTicketButtonColor       = '';
$globalWooCommerceEventsTicketTextColor         = '';
$globalWooCommerceEventsTicketLogo              = '';
$globalWooCommerceEventsTicketHeaderImage       = '';

$WooCommerceEventsEndDate      = '';
$WooCommerceEventsNumDays      = '';
$WooCommerceEventsMultiDayType = '';
$WooCommerceEventsSelectDate   = '';
$WooCommerceEventsDayOverride  = '';

$WooCommerceEventsBackgroundColor = '';
$WooCommerceEventsTextColor = '';
$eventbrite_option = '';
$globalFooEventsEventbriteToken = get_option( 'globalFooEventsEventbriteToken', '' );
$WooCommerceEventsAddEventbrite = '';

$WooCommerceEventsIncludeCustomAttendeeDetails = '';
$fooevents_custom_attendee_fields_options = array();
$fooevents_custom_attendee_fields_options_serialized = '';
$cf_array = [];

$FooEventsPDFTicketsEmailText   = '';
$FooEventsTicketFooterText      = '';

$fooevents_seating_options_serialized = '';
$fooevents_seating_options = array();

if( isset( $wp->query_vars['wcfm-products-manage'] ) && !empty( $wp->query_vars['wcfm-products-manage'] ) ) {
	$product_id = $wp->query_vars['wcfm-products-manage'];
	
	$product  = wc_get_product( $product_id );
	
	$event_post = get_post( $product_id );
	
	if( $product && !empty($product) && is_object($product) ) {
		
		$WooCommerceEventsEvent                     = get_post_meta($product_id, 'WooCommerceEventsEvent', true);
		$WooCommerceEventsDate                      = get_post_meta($product_id, 'WooCommerceEventsDate', true);
		$WooCommerceEventsHour                      = get_post_meta($product_id, 'WooCommerceEventsHour', true);
		$WooCommerceEventsPeriod                    = get_post_meta($product_id, 'WooCommerceEventsPeriod', true);
		$WooCommerceEventsMinutes                   = get_post_meta($product_id, 'WooCommerceEventsMinutes', true);
		$WooCommerceEventsHourEnd                   = get_post_meta($product_id, 'WooCommerceEventsHourEnd', true);
		$WooCommerceEventsMinutesEnd                = get_post_meta($product_id, 'WooCommerceEventsMinutesEnd', true);
		$WooCommerceEventsEndPeriod                 = get_post_meta($product_id, 'WooCommerceEventsEndPeriod', true);
		$WooCommerceEventsLocation                  = get_post_meta($product_id, 'WooCommerceEventsLocation', true);
		$WooCommerceEventsTicketLogo                = get_post_meta($product_id, 'WooCommerceEventsTicketLogo', true);
		$WooCommerceEventsPrintTicketLogo           = get_post_meta($product_id, 'WooCommerceEventsPrintTicketLogo', true);
		$WooCommerceEventsTicketHeaderImage         = get_post_meta($product_id, 'WooCommerceEventsTicketHeaderImage', true);
		$WooCommerceEventsSupportContact            = get_post_meta($product_id, 'WooCommerceEventsSupportContact', true);
		$WooCommerceEventsGPS                       = get_post_meta($product_id, 'WooCommerceEventsGPS', true);
		$WooCommerceEventsGoogleMaps                = get_post_meta($product_id, 'WooCommerceEventsGoogleMaps', true);
		$WooCommerceEventsDirections                = get_post_meta($product_id, 'WooCommerceEventsDirections', true);
		$WooCommerceEventsEmail                     = get_post_meta($product_id, 'WooCommerceEventsEmail', true);
		$WooCommerceEventsTicketBackgroundColor     = get_post_meta($product_id, 'WooCommerceEventsTicketBackgroundColor', true);
		$WooCommerceEventsTicketButtonColor         = get_post_meta($product_id, 'WooCommerceEventsTicketButtonColor', true);
		$WooCommerceEventsTicketTextColor           = get_post_meta($product_id, 'WooCommerceEventsTicketTextColor', true);
		$WooCommerceEventsTicketPurchaserDetails    = get_post_meta($product_id, 'WooCommerceEventsTicketPurchaserDetails', true);
		$WooCommerceEventsTicketAddCalendar         = get_post_meta($product_id, 'WooCommerceEventsTicketAddCalendar', true);
		$WooCommerceEventsTicketDisplayDateTime     = get_post_meta($product_id, 'WooCommerceEventsTicketDisplayDateTime', true);
		$WooCommerceEventsTicketDisplayBarcode      = get_post_meta($product_id, 'WooCommerceEventsTicketDisplayBarcode', true);
		$WooCommerceEventsTicketDisplayPrice            = get_post_meta($product_id, 'WooCommerceEventsTicketDisplayPrice', true);
		$WooCommerceEventsTicketText                    = get_post_meta($product_id, 'WooCommerceEventsTicketText', true);
		$WooCommerceEventsThankYouText                  = get_post_meta($product_id, 'WooCommerceEventsThankYouText', true);
		$WooCommerceEventsEventDetailsText              = get_post_meta($product_id, 'WooCommerceEventsEventDetailsText', true);
		$WooCommerceEventsCaptureAttendeeDetails        = get_post_meta($product_id, 'WooCommerceEventsCaptureAttendeeDetails', true);
		$WooCommerceEventsEmailAttendee                 = get_post_meta($product_id, 'WooCommerceEventsEmailAttendee', true);
		$WooCommerceEventsSendEmailTickets              = get_post_meta($product_id, 'WooCommerceEventsSendEmailTickets', true);
		$WooCommerceEventsCaptureAttendeeTelephone      = get_post_meta($product_id, 'WooCommerceEventsCaptureAttendeeTelephone', true);
		$WooCommerceEventsCaptureAttendeeCompany        = get_post_meta($product_id, 'WooCommerceEventsCaptureAttendeeCompany', true);
		$WooCommerceEventsCaptureAttendeeDesignation    = get_post_meta($product_id, 'WooCommerceEventsCaptureAttendeeDesignation', true);

		$WooCommerceEventsViewSeatingChart              = get_post_meta($product_id, 'WooCommerceEventsViewSeatingChart', true);

		$WooCommerceEventsExportUnpaidTickets           = get_post_meta($product_id, 'WooCommerceEventsExportUnpaidTickets', true);
		$WooCommerceEventsExportBillingDetails          = get_post_meta($product_id, 'WooCommerceEventsExportBillingDetails', true);
		
		$WooCommerceBadgeSize                           = get_post_meta($product_id, 'WooCommerceBadgeSize', true);
		$WooCommerceBadgeField1                         = get_post_meta($product_id, 'WooCommerceBadgeField1', true);
		$WooCommerceBadgeField2                         = get_post_meta($product_id, 'WooCommerceBadgeField2', true);
		$WooCommerceBadgeField3                         = get_post_meta($product_id, 'WooCommerceBadgeField3', true);

		$WooCommercePrintTicketSize                     = get_post_meta($product_id, 'WooCommercePrintTicketSize', true);
		$WooCommercePrintTicketField1                   = get_post_meta($product_id, 'WooCommercePrintTicketField1', true);
		$WooCommercePrintTicketField1_font              = get_post_meta($product_id, 'WooCommercePrintTicketField1_font', true);
		$WooCommercePrintTicketField2                   = get_post_meta($product_id, 'WooCommercePrintTicketField2', true);
		$WooCommercePrintTicketField2_font              = get_post_meta($product_id, 'WooCommercePrintTicketField2_font', true);
		$WooCommercePrintTicketField3                   = get_post_meta($product_id, 'WooCommercePrintTicketField3', true);
		$WooCommercePrintTicketField3_font              = get_post_meta($product_id, 'WooCommercePrintTicketField3_font', true);
		$WooCommercePrintTicketField4                   = get_post_meta($product_id, 'WooCommercePrintTicketField4', true);
		$WooCommercePrintTicketField4_font              = get_post_meta($product_id, 'WooCommercePrintTicketField4_font', true);
		$WooCommercePrintTicketField5                   = get_post_meta($product_id, 'WooCommercePrintTicketField5', true);
		$WooCommercePrintTicketField5_font              = get_post_meta($product_id, 'WooCommercePrintTicketField5_font', true);
		$WooCommercePrintTicketField6                   = get_post_meta($product_id, 'WooCommercePrintTicketField6', true);
		$WooCommercePrintTicketField6_font              = get_post_meta($product_id, 'WooCommercePrintTicketField6_font', true);
		
		$WooCommerceEventsPrintTicketLogoOption         = get_post_meta($product_id, 'WooCommerceEventsPrintTicketLogoOption', true);
		$WooCommerceEventsCutLinesPrintTicket           = get_post_meta($product_id, 'WooCommerceEventsCutLinesPrintTicket', true);

		
		$WooCommerceEventsCutLines                      = get_post_meta($product_id, 'WooCommerceEventsCutLines', true);

		$WooCommerceEventsEmailSubjectSingle            = get_post_meta($product_id, 'WooCommerceEventsEmailSubjectSingle', true);
		$WooCommerceEventsTicketTheme                   = get_post_meta($product_id, 'WooCommerceEventsTicketTheme', true);
		
		$WooCommerceEventsAttendeeOverride              = get_post_meta($product_id, 'WooCommerceEventsAttendeeOverride', true);
		$WooCommerceEventsTicketOverride                = get_post_meta($product_id, 'WooCommerceEventsTicketOverride', true);

		$WooCommerceEventsViewSeatingChart              = get_post_meta($product_id, 'WooCommerceEventsViewSeatingChart', true);

		$globalWooCommerceEventsGoogleMapsAPIKey        = get_option('globalWooCommerceEventsGoogleMapsAPIKey', true);

		if($globalWooCommerceEventsGoogleMapsAPIKey == 1) {
			$globalWooCommerceEventsGoogleMapsAPIKey = '';
		}

		if(empty($WooCommerceEventsEmailSubjectSingle)) {
			$WooCommerceEventsEmailSubjectSingle = __('{OrderNumber} Ticket', 'woocommerce-events');
		}

		$globalWooCommerceEventsTicketBackgroundColor   = get_option('globalWooCommerceEventsTicketBackgroundColor', true);
		$globalWooCommerceEventsTicketButtonColor       = get_option('globalWooCommerceEventsTicketButtonColor', true);
		$globalWooCommerceEventsTicketTextColor         = get_option('globalWooCommerceEventsTicketTextColor', true);
		$globalWooCommerceEventsTicketLogo              = get_option('globalWooCommerceEventsTicketLogo', true);
		$globalWooCommerceEventsTicketHeaderImage       = get_option('globalWooCommerceEventsTicketHeaderImage', true);

		if( WCFMu_Dependencies::wcfm_wc_fooevents_multiday() ) {
			$WooCommerceEventsEndDate      = get_post_meta($product_id, 'WooCommerceEventsEndDate', true);
			$WooCommerceEventsNumDays      = get_post_meta($product_id, 'WooCommerceEventsNumDays', true);
			$WooCommerceEventsMultiDayType = get_post_meta($product_id, 'WooCommerceEventsMultiDayType', true);
      $WooCommerceEventsSelectDate   = get_post_meta($product_id, 'WooCommerceEventsSelectDate', true);
      $WooCommerceEventsDayOverride  = get_post_meta($product_id, 'WooCommerceEventsDayOverride', true);
		}
		
		
		if ( WCFMu_Dependencies::wcfm_wc_fooevents_calendar() ) {
			$WooCommerceEventsBackgroundColor = get_post_meta($product_id, 'WooCommerceEventsBackgroundColor', true);
			$WooCommerceEventsTextColor       = get_post_meta($product_id, 'WooCommerceEventsTextColor', true);
			
			if(!empty($globalFooEventsEventbriteToken)) {
				$WooCommerceEventsAddEventbrite = get_post_meta($product_id, 'WooCommerceEventsAddEventbrite', true);
			}
			
		}
		
		if ( WCFMu_Dependencies::wcfm_wc_fooevents_custom_atendee() ) {
			$WooCommerceEventsIncludeCustomAttendeeDetails = get_post_meta($product_id, 'WooCommerceEventsIncludeCustomAttendeeDetails', true);
			
			$fooevents_custom_attendee_fields_options_serialized = get_post_meta($product_id,'fooevents_custom_attendee_fields_options_serialized', true);
			$fooevents_custom_attendee_fields_options = json_decode($fooevents_custom_attendee_fields_options_serialized, true);
			
			if(empty($fooevents_custom_attendee_fields_options)) {
				$fooevents_custom_attendee_fields_options = array();
			}
		}
		
		if ( WCFMu_Dependencies::wcfm_wc_fooevents_seating() ) {
			$fooevents_seating_options_serialized = get_post_meta($product_id,'fooevents_seating_options_serialized', true);
      $fooevents_seating_options = json_decode($fooevents_seating_options_serialized, true);
        
			if(empty($fooevents_seating_options)) {
				$fooevents_seating_options = array();
			}
		}
		
		if ( WCFMu_Dependencies::wcfm_wc_fooevents_pdfticket() ) {
			$FooEventsPDFTicketsEmailText   = get_post_meta($product_id, 'FooEventsPDFTicketsEmailText', true);
			$FooEventsTicketFooterText      = get_post_meta($product_id, 'FooEventsTicketFooterText', true);
			
			if(empty($FooEventsPDFTicketsEmailText)) {
				$FooEventsPDFTicketsEmailText = __('Your tickets are attached. Please print them and bring them to the event. ', 'fooevents-pdf-tickets');
			}
			
			if(empty($FooEventsTicketFooterText)) {
				$FooEventsTicketFooterText = __("Cut out the tickets or keep them together. Don't forget to take them to the event. When printing please use a standard A4 portrait size. Incorrect sizing could effect the reading of the barcode.", 'fooevents-pdf-tickets');
			}
		}
		
	}
}

//$FooEvents = new FooEvents();
$fooevent_Config = new FooEvents_Config();

//WooHelper
require_once($fooevent_Config->classPath.'woohelper.php');
$fooevent_WooHelper = new FooEvents_Woo_Helper($fooevent_Config);
$themes    = $fooevent_WooHelper->get_ticket_themes();

$dayTerm = __('Day', 'fooevents-multiday-events');

$rich_editor = apply_filters( 'wcfm_is_allow_rich_editor', 'rich_editor' );
$wpeditor = apply_filters( 'wcfm_is_allow_product_wpeditor', 'wpeditor' );
if( $wpeditor && $rich_editor ) {
	$rich_editor = 'wcfm_wpeditor';
} else {
	$wpeditor = 'textarea';
}

?>

<div class="page_collapsible products_manage_wc_fooevents <?php echo apply_filters( 'wcfm_pm_block_class_fooevents', 'simple variable' ); ?>" id="wcfm_products_manage_form_wc_fooevents_head"><label class="wcfmfa fa-calendar-alt"></label><?php _e('Event', 'wc-frontend-manager-ultimate'); ?><span></span></div>
<div class="wcfm-container <?php echo apply_filters( 'wcfm_pm_block_class_fooevents', 'simple variable' ); ?>">
	<div id="wcfm_products_manage_form_wc_fooevents_expander" class="wcfm-content">
	  <h2><?php _e('Event Setup', 'wc-frontend-manager-ultimate'); ?></h2>
	  <div class="wcfm_clearfix"></div>
	  
	  <div id="woocommerce_events_data" class="panel woocommerce_options_panel">
    
			<div class="options_group">
				<p class="form-field">
				  <span class="wcfm_title"><strong><?php _e('Is this product an event?:', 'woocommerce-events'); ?></strong>
						<?php 
						printf(
										'<span class="img_tip wcfmfa fa-question" data-tip="%s"></span>',
										__('Enable this option to add event and ticketing features.', 'woocommerce-events')
									);
						?>
					</span>
					<select name="WooCommerceEventsEvent" id="WooCommerceEventsEvent" class="wcfm-select">
					  <option value="NotEvent" <?php echo ($WooCommerceEventsEvent == 'NotEvent')? 'SELECTED' : '' ?>><?php _e('No', 'woocommerce-events'); ?></option>
						<option value="Event" <?php echo ($WooCommerceEventsEvent == 'Event')? 'SELECTED' : '' ?>><?php _e('Yes', 'woocommerce-events'); ?></option>
					</select>
				</p>
		  </div>
    
		  <div id="WooCommerceEventsForm" style="display:none;">
		    <?php if( WCFMu_Dependencies::wcfm_wc_fooevents_multiday() ) { ?>
					<div class="options_group">
						<p class="form-field">
							 <span class="wcfm_title"><strong><?php _e('Number of days:', 'woocommerce-events'); ?></strong>
								<?php 
								printf(
												'<span class="img_tip wcfmfa fa-question" data-tip="%s"></span>',
												__('The number of days the event spans. This is uses by the FooEvents Check-ins app to manage daily check-ins.', 'woocommerce-events')
											);
								?>
							 </span>
							 <select name="WooCommerceEventsNumDays" id="WooCommerceEventsNumDays" class="wcfm-select">
									<?php for($x=1; $x<=30; $x++) :?>
										<option value="<?php echo $x; ?>" <?php echo ($WooCommerceEventsNumDays == $x)? 'SELECTED' : '' ?>><?php echo $x; ?></option>
									<?php endfor; ?>
							 </select>
						</p>
					</div>
				
					<div class="options_group" id ="WooCommerceEventsMultiDayTypeHolder">
						<p class="form-field">
							<span class="wcfm_title"><strong><?php _e('Multi-day type:', 'woocommerce-events'); ?></strong></span>
							<input type="radio" name="WooCommerceEventsMultiDayType" value="sequential" <?php echo ($WooCommerceEventsMultiDayType !== 'select')? 'CHECKED' : '' ?>> <?php _e('Sequential days', 'fooevents-multiday-events'); ?><br>
							<span class="wcfm_title"><strong></strong></span>
							<input type="radio" name="WooCommerceEventsMultiDayType" value="select" <?php echo ($WooCommerceEventsMultiDayType == 'select')? 'CHECKED' : '' ?>> <?php _e('Select days', 'fooevents-multiday-events'); ?><br>
						</p>
					</div>
				
					<div class="options_group" id ="WooCommerceEventsSelectDateContainer">
						<?php if(!empty($WooCommerceEventsSelectDate)) :?>
							<?php $x = 1; ?>
							<?php foreach($WooCommerceEventsSelectDate as $eventDate) :?>
								<p class="form-field">
									<span class="wcfm_title"><strong><?php echo $dayTerm; ?> <?php echo $x; ?></strong></span>
									<input type="text" class="wcfm-text" class="WooCommerceEventsSelectDate" name="WooCommerceEventsSelectDate[]" value="<?php echo $eventDate; ?>"/>
								</p>
								<?php $x++; ?>
							<?php endforeach; ?>
						<?php endif; ?>
					</div>
				<?php } ?>
				
        <div class="options_group" id="WooCommerceEventsDateContainer">
					<p class="form-field">
						 <span class="wcfm_title"><strong><?php _e('Start Date:', 'woocommerce-events'); ?></strong>
							 <?php 
								printf(
												'<span class="img_tip wcfmfa fa-question" data-tip="%s"></span>',
												__('The date that the event is scheduled to take place. This is used as a span on the frontend of the website. FooEvents Calendar uses this to display the event.', 'woocommerce-events')
											);
								?>
							</span>
						 <input type="text" class="wcfm-text" id="WooCommerceEventsDate" name="WooCommerceEventsDate" value="<?php echo $WooCommerceEventsDate; ?>"/>
					</p>
        </div>
       
        <?php if( WCFMu_Dependencies::wcfm_wc_fooevents_multiday() ) { ?>
					<div class="options_group" id="WooCommerceEventsEndDateContainer">
						<p class="form-field">
							 <span class="wcfm_title"><strong><?php _e('End Date:', 'woocommerce-events'); ?></strong>
								 <?php 
									printf(
													'<span class="img_tip wcfmfa fa-question" data-tip="%s"></span>',
													__('The date that the event is scheduled to end. This is used as a label on the frontend of the website. FooEvents Calendar uses this to display an event spanning multiple days.', 'woocommerce-events')
												);
									?>
								</span>
							 <input type="text" class="wcfm-text" id="WooCommerceEventsEndDate" name="WooCommerceEventsEndDate" value="<?php echo $WooCommerceEventsEndDate; ?>"/>
						</p>
					</div>
				<?php } ?>
        
        <div class="options_group">
					<p class="form-field">
						<span class="wcfm_title"><strong><?php _e('Start time:', 'woocommerce-events'); ?></strong>
							<?php 
							printf(
											'<span class="img_tip wcfmfa fa-question" data-tip="%s"></span>',
											__('The time that the event is scheduled to start', 'woocommerce-events')
										);
							?>
						</span>
						<select name="WooCommerceEventsHour" id="WooCommerceEventsHour">
							<?php for($x=0; $x<=23; $x++) :?>
								<?php $x = sprintf("%02d", $x); ?>
								<option value="<?php echo $x; ?>" <?php echo ($WooCommerceEventsHour == $x) ? 'SELECTED' : ''; ?>><?php echo $x; ?></option>
							<?php endfor; ?>
						</select>
						<select name="WooCommerceEventsMinutes" id="WooCommerceEventsMinutes">
							<?php for($x=0; $x<=59; $x++) :?>
							  <?php $x = sprintf("%02d", $x); ?>
							  <option value="<?php echo $x; ?>" <?php echo ($WooCommerceEventsMinutes == $x) ? 'SELECTED' : ''; ?>><?php echo $x; ?></option>
							<?php endfor; ?>
						</select>
						<select name="WooCommerceEventsPeriod" id="WooCommerceEventsPeriod">
							<option value="">-</option>
							<option value="a.m." <?php echo ($WooCommerceEventsPeriod == 'a.m.') ? 'SELECTED' : ''; ?>>a.m.</option>
							<option value="p.m." <?php echo ($WooCommerceEventsPeriod == 'p.m.') ? 'SELECTED' : ''; ?>>p.m.</option>
						</select>
					</p>
        </div>
        <div class="options_group">
					<p class="form-field">
						<span class="wcfm_title"><strong><?php _e('End time:', 'woocommerce-events'); ?></strong>
							<?php 
							printf(
											'<span class="img_tip wcfmfa fa-question" data-tip="%s"></span>',
											__('The time that the event is scheduled to end', 'woocommerce-events')
										);
							?>
						</span>
						<select name="WooCommerceEventsHourEnd" id="WooCommerceEventsHourEnd">
							<?php for($x=0; $x<=23; $x++) :?>
								<?php $x = sprintf("%02d", $x); ?>
								<option value="<?php echo $x; ?>" <?php echo ($WooCommerceEventsHourEnd == $x) ? 'SELECTED' : ''; ?>><?php echo $x; ?></option>
							<?php endfor; ?>
						</select>
						<select name="WooCommerceEventsMinutesEnd" id="WooCommerceEventsMinutesEnd">
							<?php for($x=0; $x<=59; $x++) :?>
								<?php $x = sprintf("%02d", $x); ?>
								<option value="<?php echo $x; ?>" <?php echo ($WooCommerceEventsMinutesEnd == $x) ? 'SELECTED' : ''; ?>><?php echo $x; ?></option>
							<?php endfor; ?>
						</select>
						<select name="WooCommerceEventsEndPeriod" id="WooCommerceEventsEndPeriod">
							<option value="">-</option>
							<option value="a.m." <?php echo ($WooCommerceEventsEndPeriod == 'a.m.') ? 'SELECTED' : ''; ?>>a.m.</option>
							<option value="p.m." <?php echo ($WooCommerceEventsEndPeriod == 'p.m.') ? 'SELECTED' : ''; ?>>p.m.</option>
						</select>
					</p>
        </div>
        
        <?php if ( WCFMu_Dependencies::wcfm_wc_fooevents_calendar() && !empty($globalFooEventsEventbriteToken) ) { ?>
					<div class="options_group">
						<p class="form-field">
						 <span class="wcfm_title checkbox_title"><strong><?php _e('Add event to EventBrite', 'woocommerce-events'); ?></strong>
						 <?php 
							printf(
											'<span class="img_tip wcfmfa fa-question" data-tip="%s"></span>',
											__('The date that the event is scheduled to take place. This is used as a label on the frontend of the website. FooEvents Calendar uses this to display the event.', 'woocommerce-events')
										);
							?>
							</span>
							<input class="wcfm-checkbox" type="checkbox" id="WooCommerceEventsMetaBoxAddEventbrite" name="WooCommerceEventsAddEventbrite" value="1" <?php echo $WooCommerceEventsAddEventbrite; ?>/>
						</p>
					</div>
				<?php } ?>
        
        <div class="options_group">
					<p class="form-field">
						 <span class="wcfm_title"><strong><?php _e('Venue:', 'woocommerce-events'); ?></strong>
							 <?php 
								printf(
												'<span class="img_tip wcfmfa fa-question" data-tip="%s"></span>',
												__('The venue where the event will be held', 'woocommerce-events')
											);
								?>
						 </span>
						 <input type="text" class="wcfm-text" id="WooCommerceEventsLocation" name="WooCommerceEventsLocation" value="<?php echo $WooCommerceEventsLocation; ?>"/>
					</p>
        </div>
        <div class="options_group">
					<p class="form-field">
						 <span class="wcfm_title"><strong><?php _e('GPS Coordinates:', 'woocommerce-events'); ?></strong>
							 <?php 
								printf(
												'<span class="img_tip wcfmfa fa-question" data-tip="%s"></span>',
												__('The venue\'s GPS coordinates ', 'woocommerce-events')
											);
								?>
						 </span>
						 <input type="text" class="wcfm-text" id="WooCommerceEventsGPS" name="WooCommerceEventsGPS" value="<?php echo $WooCommerceEventsGPS; ?>"/>
					</p>
        </div>
        <div class="options_group">
					<p class="form-field">
						 <span class="wcfm_title"><strong><?php _e('Google Map Coordinates:', 'woocommerce-events'); ?></strong>
							 <?php 
								printf(
												'<span class="img_tip wcfmfa fa-question" data-tip="%s"></span>',
												__('The GPS coordinates used to determine the pin position on the Google map that is displayed on the event page. A valid Google Maps API key is required under WooCommerce -> Settigns -> Events.', 'woocommerce-events')
											);
								?>
						 </span>
						 <input type="text" class="wcfm-text" id="WooCommerceEventsGoogleMaps" name="WooCommerceEventsGoogleMaps" value="<?php echo $WooCommerceEventsGoogleMaps; ?>"/>
						 <?php if( !wcfm_is_vendor() && empty($globalWooCommerceEventsGoogleMapsAPIKey)) :?>
							 <p class="description">
							   <?php _e('Google Maps API key not set.','woocommerce-events'); ?> <a href="admin.php?page=wc-settings&tab=settings_woocommerce_events"><?php _e('Please set one in your global event options.', 'woocommerce-events'); ?></a>
							 </p>
						 <?php endif; ?>
					</p>
        </div>
        <div class="options_group">
					<p class="form-field">
						 <span class="wcfm_title"><strong><?php _e('Directions:', 'woocommerce-events'); ?></strong></span>
						 <textarea name="WooCommerceEventsDirections" id="WooCommerceEventsDirections" class="wcfm-textarea"><?php echo $WooCommerceEventsDirections ?></textarea>
					</p>
        </div>
        <div class="options_group">
					<p class="form-field">
						 <span class="wcfm_title"><strong><?php _e('Phone:', 'woocommerce-events'); ?></strong>
						 <?php 
							printf(
											'<span class="img_tip wcfmfa fa-question" data-tip="%s"></span>',
											__('Event organizer\'s landline or mobile phone number', 'woocommerce-events')
										);
							?>
						 </span>
						 <input type="text" class="wcfm-text" id="WooCommerceEventsSupportContact" name="WooCommerceEventsSupportContact" value="<?php echo $WooCommerceEventsSupportContact; ?>"/>
					</p>
        </div>
        <div class="options_group">
					<p class="form-field">
					 <span class="wcfm_title"><strong><?php _e('Email:', 'woocommerce-events'); ?></strong>
					 <?php 
						printf(
										'<span class="img_tip wcfmfa fa-question" data-tip="%s"></span>',
										__('Event organizer\'s email address', 'woocommerce-events')
									);
						?>
					 </span>
					 <input type="text" class="wcfm-text" id="WooCommerceEventsEmail" name="WooCommerceEventsEmail" value="<?php echo $WooCommerceEventsEmail; ?>"/>
					</p>
        </div>
        <div class="options_group">
					<p class="form-field">
						<span class="wcfm_title"><strong><?php _e('HTML ticket theme:', 'woocommerce-events'); ?></strong>
						<?php 
						printf(
										'<span class="img_tip wcfmfa fa-question" data-tip="%s"></span>',
										__('Select the ticket theme that will be used to style the embedded HTML tickets within ticket emails.', 'woocommerce-events')
									);
						?>
					 </span>
						<select name="WooCommerceEventsTicketTheme" id="WooCommerceEventsTicketTheme" class="wcfm-select">
							<?php foreach($themes as $theme => $theme_details) :?>
								<option value="<?php echo $theme_details['path']; ?>" <?php echo ($WooCommerceEventsTicketTheme == $theme_details['path'])? 'SELECTED' : '' ?>><?php echo $theme_details['name']; ?></option>
							<?php endforeach; ?>
						</select>
					</p> 
        </div>
        <div class="options_group">
					<?php $WooCommerceEventsTicketLogo = (empty($WooCommerceEventsTicketLogo))? $globalWooCommerceEventsTicketLogo : $WooCommerceEventsTicketLogo; ?>
					<p class="form-field">
					  <?php
					  $WCFM->wcfm_fields->wcfm_generate_form_field( array( "WooCommerceEventsTicketLogo" => array( 'label' => __( 'Ticket logo:', 'woocommerce-events' ), 'wcfm_uploader_by_url' => true, 'type' => 'upload', 'label_class' => 'wcfm_title', 'value' => $WooCommerceEventsTicketLogo, 'hints' => __('The logo which will be displayed on the ticket in JPG or PNG format', 'woocommerce-events') ) ) );
					  ?>
					</p>
        </div>
        <div class="options_group">
					<?php $WooCommerceEventsTicketHeaderImage = (empty($WooCommerceEventsTicketHeaderImage))? $globalWooCommerceEventsTicketHeaderImage : $WooCommerceEventsTicketHeaderImage; ?>
					<p class="form-field">
					  <?php
					  $WCFM->wcfm_fields->wcfm_generate_form_field( array( "WooCommerceEventsTicketHeaderImage" => array( 'label' => __( 'Ticket header image:', 'woocommerce-events' ), 'wcfm_uploader_by_url' => true, 'type' => 'upload', 'label_class' => 'wcfm_title', 'value' => $WooCommerceEventsTicketHeaderImage, 'hints' => __('The main image which will be displayed on the ticket in JPG or PNG format', 'woocommerce-events') ) ) );
					  ?>
					</p>
        </div>
        <div class="options_group">
					<p class="form-field">
						 <span class="wcfm_title"><strong><?php _e('Ticket subject:', 'woocommerce-events'); ?></strong>
						 <?php 
							printf(
											'<span class="img_tip wcfmfa fa-question" data-tip="%s"></span>',
											__('Subject of ticket emails sent out. Insert {OrderNumber} to dispay order number.', 'woocommerce-events')
										);
							?>
						 </span>
						 <input type="text" class="wcfm-text" id="WooCommerceEventsEmailSubjectSingle" name="WooCommerceEventsEmailSubjectSingle" value="<?php echo $WooCommerceEventsEmailSubjectSingle; ?>"/>
					</p>
        </div>
        <div class="options_group">
					<p class="form-field">
						<?php 
						$WCFM->wcfm_fields->wcfm_generate_form_field( array( "WooCommerceEventsTicketText" => array('label' => __('Ticket text:', 'woocommerce-events') , 'type' => $wpeditor, 'class' => 'wcfm-textarea wcfm_ele wcfm_full_ele simple variable external grouped booking wcfm_custom_field_editor ' . $rich_editor , 'label_class' => 'wcfm_title wcfm_full_ele', 'rows' => 5, 'value' => $WooCommerceEventsTicketText, 'teeny' => true ) ) );
						?>
					</p>
        </div>
        <div class="options_group">
					<p class="form-field">
					  <?php 
						$WCFM->wcfm_fields->wcfm_generate_form_field( array( "WooCommerceEventsThankYouText" => array('label' => __('Thank you page text:', 'woocommerce-events') , 'type' => $wpeditor, 'class' => 'wcfm-textarea wcfm_ele wcfm_full_ele simple variable external grouped booking wcfm_custom_field_editor ' . $rich_editor , 'label_class' => 'wcfm_title wcfm_full_ele', 'rows' => 5, 'value' => $WooCommerceEventsThankYouText, 'teeny' => true ) ) );
						?>
					</p>
        </div>
        <div class="options_group">
					<p class="form-field">
					  <?php 
						$WCFM->wcfm_fields->wcfm_generate_form_field( array( "WooCommerceEventsEventDetailsText" => array('label' => __('Event details tab text:', 'woocommerce-events') , 'type' => $wpeditor, 'class' => 'wcfm-textarea wcfm_ele wcfm_full_ele simple variable external grouped booking wcfm_custom_field_editor ' . $rich_editor , 'label_class' => 'wcfm_title wcfm_full_ele', 'rows' => 5, 'value' => $WooCommerceEventsEventDetailsText, 'teeny' => true ) ) );
						?>
					</p>
        </div>
        <div class="options_group">
					<?php $globalWooCommerceEventsTicketBackgroundColor = (empty($globalWooCommerceEventsTicketBackgroundColor))? '' : $globalWooCommerceEventsTicketBackgroundColor; ?>
					<?php $WooCommerceEventsTicketBackgroundColor = (empty($WooCommerceEventsTicketBackgroundColor))? $globalWooCommerceEventsTicketBackgroundColor : $WooCommerceEventsTicketBackgroundColor; ?>
					<p class="form-field">
						 <span class="wcfm_title"><strong><?php _e('Ticket border:', 'woocommerce-events'); ?></strong>
						 <?php 
							printf(
											'<span class="img_tip wcfmfa fa-question" data-tip="%s"></span>',
											__('The color of the ticket border', 'woocommerce-events')
										);
							?>
						 </span>
						 <input class="woocommerce-events-color-field" type="text" name="WooCommerceEventsTicketBackgroundColor" value="<?php echo ''.$WooCommerceEventsTicketBackgroundColor; ?>"/>
					</p>
        </div>
        <div class="options_group">
					<?php $globalWooCommerceEventsTicketButtonColor = (empty($globalWooCommerceEventsTicketButtonColor))? '' : $globalWooCommerceEventsTicketButtonColor; ?>
					<?php $WooCommerceEventsTicketButtonColor = (empty($WooCommerceEventsTicketButtonColor))? $globalWooCommerceEventsTicketButtonColor : $WooCommerceEventsTicketButtonColor; ?>
					<p class="form-field">
						 <span class="wcfm_title"><strong><?php _e('Ticket buttons:', 'woocommerce-events'); ?></strong>
						 <?php 
							printf(
											'<span class="img_tip wcfmfa fa-question" data-tip="%s"></span>',
											__('The color of the ticket button', 'woocommerce-events')
										);
							?>
						 </span>
						 <input class="woocommerce-events-color-field" type="text" name="WooCommerceEventsTicketButtonColor" value="<?php echo ''.$WooCommerceEventsTicketButtonColor; ?>"/>
					</p>
        </div>
        
        <div class="options_group">
					<?php $globalWooCommerceEventsTicketTextColor = (empty($globalWooCommerceEventsTicketTextColor))? '' : $globalWooCommerceEventsTicketTextColor; ?>
					<?php $WooCommerceEventsTicketTextColor = (empty($WooCommerceEventsTicketTextColor))? $globalWooCommerceEventsTicketTextColor : $WooCommerceEventsTicketTextColor; ?>
					<p class="form-field">
						 <span class="wcfm_title"><strong><?php _e('Ticket button text:', 'woocommerce-events'); ?></strong>
						 <?php 
							printf(
											'<span class="img_tip wcfmfa fa-question" data-tip="%s"></span>',
											__('The color of the ticket buttons text', 'woocommerce-events')
										);
							?>
						 </span>
						 <input class="woocommerce-events-color-field" type="text" name="WooCommerceEventsTicketTextColor" value="<?php echo ''.$WooCommerceEventsTicketTextColor; ?>"/>
					</p>
        </div>
        
        <?php if ( WCFMu_Dependencies::wcfm_wc_fooevents_calendar() ) { ?>
					<div class="options_group">
						<?php $globalWooCommerceEventsTicketTextColor = (empty($globalWooCommerceEventsTicketTextColor))? '' : $globalWooCommerceEventsTicketTextColor; ?>
						<?php $WooCommerceEventsBackgroundColor = (empty($WooCommerceEventsBackgroundColor))? $globalWooCommerceEventsTicketTextColor : $WooCommerceEventsBackgroundColor; ?>
						<p class="form-field">
							 <span class="wcfm_title"><strong><?php _e('Calendar background color:', 'woocommerce-events'); ?></strong>
							 <?php 
								printf(
												'<span class="img_tip wcfmfa fa-question" data-tip="%s"></span>',
												__('Background color of event on calendar', 'woocommerce-events')
											);
								?>
							 </span>
							 <input class="woocommerce-events-color-field" type="text" name="WooCommerceEventsBackgroundColor" value="<?php echo ''.$WooCommerceEventsBackgroundColor; ?>"/>
						</p>
					</div>
					
					<div class="options_group">
						<?php $globalWooCommerceEventsTicketTextColor = (empty($globalWooCommerceEventsTicketTextColor))? '' : $globalWooCommerceEventsTicketTextColor; ?>
						<?php $WooCommerceEventsTextColor = (empty($WooCommerceEventsTextColor))? $globalWooCommerceEventsTicketTextColor : $WooCommerceEventsTextColor; ?>
						<p class="form-field">
							 <span class="wcfm_title"><strong><?php _e('Calendar text color:', 'woocommerce-events'); ?></strong>
							 <?php 
								printf(
												'<span class="img_tip wcfmfa fa-question" data-tip="%s"></span>',
												__('Text color of event on calendar', 'woocommerce-events')
											);
								?>
							 </span>
							 <input class="woocommerce-events-color-field" type="text" name="WooCommerceEventsTextColor" value="<?php echo ''.$WooCommerceEventsTextColor; ?>"/>
						</p>
					</div>
				<?php } ?>
        
        <div class="options_group">
					<p class="form-field">
						 <span class="wcfm_title checkbox_title"><strong><?php _e('Include purchaser or attendee details on ticket?:', 'woocommerce-events'); ?></strong>
						 <?php 
							printf(
											'<span class="img_tip wcfmfa fa-question" data-tip="%s"></span>',
											__('Selecting this will display the purchaser or attendee details on the ticket', 'woocommerce-events')
										);
							?>
						 </span>
						 <input type="checkbox" class="wcfm-checkbox" name="WooCommerceEventsTicketPurchaserDetails" value="on" <?php echo (empty($WooCommerceEventsTicketPurchaserDetails) || $WooCommerceEventsTicketPurchaserDetails == 'on')? 'CHECKED' : ''; ?>>
					</p>
        </div>
        <div class="options_group">
					<p class="form-field">
						 <span class="wcfm_title checkbox_title"><strong><?php _e('Display "Add to calendar" on ticket?:', 'woocommerce-events'); ?></strong>
						 <?php 
							printf(
											'<span class="img_tip wcfmfa fa-question" data-tip="%s"></span>',
											__('Selecting this will display an - Add to calendar - button on the ticket. Clicking this will generate a .ics file', 'woocommerce-events')
										);
							?>
						 </span>
						 <input type="checkbox" class="wcfm-checkbox" name="WooCommerceEventsTicketAddCalendar" value="on" <?php echo (empty($WooCommerceEventsTicketAddCalendar) || $WooCommerceEventsTicketAddCalendar == 'on')? 'CHECKED' : ''; ?>>
					</p>
        </div>
        <div class="options_group">
					<p class="form-field">
						 <span class="wcfm_title checkbox_title"><strong><?php _e('Display date and time on ticket?:', 'woocommerce-events'); ?></strong>
						 <?php 
							printf(
											'<span class="img_tip wcfmfa fa-question" data-tip="%s"></span>',
											__('Selecting this will display the time and date of the event, on the ticket', 'woocommerce-events')
										);
							?>
						 </span>
						 <input type="checkbox" class="wcfm-checkbox" name="WooCommerceEventsTicketDisplayDateTime" value="on" <?php echo (empty($WooCommerceEventsTicketDisplayDateTime) || $WooCommerceEventsTicketDisplayDateTime == 'on')? 'CHECKED' : ''; ?>>
					</p>
        </div>
        <div class="options_group">
					<p class="form-field">
						 <span class="wcfm_title checkbox_title"><strong><?php _e('Display barcode on ticket?:', 'woocommerce-events'); ?></strong>
						 <?php 
							printf(
											'<span class="img_tip wcfmfa fa-question" data-tip="%s"></span>',
											__('Selecting this will display the barcode on the ticket', 'woocommerce-events')
										);
							?>
						 </span>
						 <input type="checkbox" class="wcfm-checkbox" name="WooCommerceEventsTicketDisplayBarcode" value="on" <?php echo (empty($WooCommerceEventsTicketDisplayBarcode) || $WooCommerceEventsTicketDisplayBarcode == 'on')? 'CHECKED' : ''; ?>>
					</p>
        </div>
        <div class="options_group">
					<p class="form-field">
						 <span class="wcfm_title checkbox_title"><strong><?php _e('Display price on ticket?:', 'woocommerce-events'); ?></strong>
						 <?php 
							printf(
											'<span class="img_tip wcfmfa fa-question" data-tip="%s"></span>',
											__('Selecting this will display the ticket price, on the ticket', 'woocommerce-events')
										);
							?>
						 </span>
						 <input type="checkbox" class="wcfm-checkbox" name="WooCommerceEventsTicketDisplayPrice" value="on" <?php echo ($WooCommerceEventsTicketDisplayPrice == 'on')? 'CHECKED' : ''; ?>>
					</p>
        </div>
        <div class="options_group">
					<p class="form-field">
						 <span class="wcfm_title checkbox_title"><strong><?php _e('Capture individual attendee details?:', 'woocommerce-events'); ?></strong>
						 <?php 
							printf(
											'<span class="img_tip wcfmfa fa-question" data-tip="%s"></span>',
											__('Selecting this will add attendee capture fields on the checkout screen', 'woocommerce-events')
										);
							?>
						 </span>
						 <input type="checkbox" class="wcfm-checkbox" name="WooCommerceEventsCaptureAttendeeDetails" value="on" <?php echo ($WooCommerceEventsCaptureAttendeeDetails == 'on')? 'CHECKED' : ''; ?>>
					</p>
        </div>
        
        <div class="options_group">
					<p class="form-field">
						<span class="wcfm_title checkbox_title"><strong><?php _e('Display custom attendee details on ticket?:', 'woocommerce-events'); ?></strong>
						 <?php 
							printf(
											'<span class="img_tip wcfmfa fa-question" data-tip="%s"></span>',
											__('Selecting this will display custom attendee fields on the ticket', 'woocommerce-events')
										);
							?>
						 </span>
						<input type="checkbox" name="WooCommerceEventsIncludeCustomAttendeeDetails" value="on" <?php echo ($WooCommerceEventsIncludeCustomAttendeeDetails == 'on')? 'CHECKED' : ''; ?>>
					</p>
				</div>
        
        <div class="options_group">
					<p class="form-field">
						 <span class="wcfm_title checkbox_title"><strong><?php _e('Email attendee rather than purchaser?:', 'woocommerce-events'); ?></strong>
						 <?php 
							printf(
											'<span class="img_tip wcfmfa fa-question" data-tip="%s"></span>',
											__('Selecting this will email the ticket to the attendee', 'woocommerce-events')
										);
							?>
						 </span>
						 <input type="checkbox" class="wcfm-checkbox" name="WooCommerceEventsEmailAttendee" value="on" <?php echo ($WooCommerceEventsEmailAttendee == 'on')? 'CHECKED' : ''; ?>>
					</p>
        </div>
        <div class="options_group">
					<p class="form-field">
						 <span class="wcfm_title checkbox_title"><strong><?php _e('Capture attendee telephone?:', 'woocommerce-events'); ?></strong>
						 <?php 
							printf(
											'<span class="img_tip wcfmfa fa-question" data-tip="%s"></span>',
											__('Selecting this will add a telephone number field to the attendee capture fields on the checkout screen', 'woocommerce-events')
										);
							?>
						 </span>
						 <input type="checkbox" class="wcfm-checkbox" name="WooCommerceEventsCaptureAttendeeTelephone" value="on" <?php echo ($WooCommerceEventsCaptureAttendeeTelephone == 'on')? 'CHECKED' : ''; ?>>
					</p>
        </div>
        <div class="options_group">
					 <p class="form-field">
						 <span class="wcfm_title checkbox_title"><strong><?php _e('Capture attendee company?:', 'woocommerce-events'); ?></strong>
						 <?php 
							printf(
											'<span class="img_tip wcfmfa fa-question" data-tip="%s"></span>',
											__('Selecting this will add a company field to the attendee capture fields on the checkout screen', 'woocommerce-events')
										);
							?>
						 </span>
						 <input type="checkbox" class="wcfm-checkbox" name="WooCommerceEventsCaptureAttendeeCompany" value="on" <?php echo ($WooCommerceEventsCaptureAttendeeCompany == 'on')? 'CHECKED' : ''; ?>>
					 </p>
        </div>
        <div class="options_group">
					<p class="form-field">
						 <span class="wcfm_title checkbox_title"><strong><?php _e('Capture attendee designation?:', 'woocommerce-events'); ?></strong>
						 <?php 
							printf(
											'<span class="img_tip wcfmfa fa-question" data-tip="%s"></span>',
											__('Selecting this will add a designation field to the attendee capture fields on the checkout screen', 'woocommerce-events')
										);
							?>
						 </span>
						 <input type="checkbox" class="wcfm-checkbox" name="WooCommerceEventsCaptureAttendeeDesignation" value="on" <?php echo ($WooCommerceEventsCaptureAttendeeDesignation == 'on')? 'CHECKED' : ''; ?>>
					</p>
        </div>
        <div class="options_group">
					<p class="form-field">
						 <span class="wcfm_title checkbox_title"><strong><?php _e('Email tickets?:', 'woocommerce-events'); ?></strong>
						 <?php 
							printf(
											'<span class="img_tip wcfmfa fa-question" data-tip="%s"></span>',
											__('Selecting this will email out tickets once the order has been completed', 'woocommerce-events')
										);
							?>
						 </span>
						 <input type="checkbox" class="wcfm-checkbox" name="WooCommerceEventsSendEmailTickets" value="on" <?php echo (empty($WooCommerceEventsSendEmailTickets) || $WooCommerceEventsSendEmailTickets == 'on')? 'CHECKED' : ''; ?>>
					</p>
        </div>
        
        <?php if ( WCFMu_Dependencies::wcfm_wc_fooevents_seating() ) : ?>
					<div class="options_group">
						 <p class="form-field">
								<span class="wcfm_title checkbox_title"><strong><?php _e('Display "View seating chart" option on checkout page?:', 'woocommerce-events'); ?></strong>
								<?php 
								printf(
												'<span class="img_tip wcfmfa fa-question" data-tip="%s"></span>',
												__('Selecting this will display a - View seating chart - link on the checkout page. If you enable this option, please make sure that you have set up a seating chart under the "Seating" tab.', 'woocommerce-events')
											);
								?>
								</span>
								<input type="checkbox" class="wcfm-checkbox" name="WooCommerceEventsViewSeatingChart" value="on" <?php echo (empty($WooCommerceEventsViewSeatingChart) || $WooCommerceEventsViewSeatingChart == 'on')? 'CHECKED' : ''; ?>>
						 </p>
					</div>
        <?php endif; ?>
        
        <div class="options_group">
          <p><h2><?php _e('Override terminology', 'woocommerce-events'); ?></h2><div class="wcfm-clearfix"></div></p>
					<p class="form-field">
							<span class="wcfm_title"><strong><?php _e('Attendee:', 'woocommerce-events'); ?></strong>
							<?php 
							printf(
											'<span class="img_tip wcfmfa fa-question" data-tip="%s"></span>',
											__('Subject of ticket emails sent out. Insert {OrderNumber} to dispay order number.', 'woocommerce-events')
										);
							?>
							</span>
							<input type="text" class="wcfm-text" id="WooCommerceEventsAttendeeOverride" name="WooCommerceEventsAttendeeOverride" value="<?php echo $WooCommerceEventsAttendeeOverride; ?>"/>
					</p>
					<p class="form-field">
						<span class="wcfm_title"><strong><?php _e('Book ticket:', 'woocommerce-events'); ?></strong>
						<?php 
						printf(
										'<span class="img_tip wcfmfa fa-question" data-tip="%s"></span>',
										__('Subject of ticket emails sent out. Insert {OrderNumber} to dispay order number.', 'woocommerce-events')
									);
						?>
						</span>
						<input type="text" class="wcfm-text" id="WooCommerceEventsTicketOverride" name="WooCommerceEventsTicketOverride" value="<?php echo $WooCommerceEventsTicketOverride; ?>"/>
					</p>
					
					<?php if( WCFMu_Dependencies::wcfm_wc_fooevents_multiday() ) { ?>
						<p class="form-field">
							<span class="wcfm_title"><strong><?php _e('Day:', 'fooevents-multiday-events'); ?></strong>
							<?php 
							printf(
											'<span class="img_tip wcfmfa fa-question" data-tip="%s"></span>',
											__("Subject of ticket emails sent out. Insert {OrderNumber} to dispay order number.", 'woocommerce-events')
										);
							?>
							</span>
							<input type="text" class="wcfm-text" id="WooCommerceEventsDayOverride" name="WooCommerceEventsDayOverride" value="<?php echo $WooCommerceEventsDayOverride; ?>"/>
						</p>
					<?php } ?>
					
        </div>
        
        <?php if(!empty($product_id)) :?>
					<div class="options_group">
						<p><h2><?php _e('Export attendees', 'woocommerce-events'); ?></h2><div class="wcfm-clearfix"></div></p>
						<div id="WooCommerceEventsExportMessage"></div>
						<p class="form-field">
							<span class="wcfm_title"><strong><?php _e('Include unpaid tickets:', 'woocommerce-events'); ?></strong></span><input type="checkbox" class="wcfm-checkbox" id="WooCommerceEventsExportUnpaidTickets" name="WooCommerceEventsExportUnpaidTickets" value="on" <?php echo ($WooCommerceEventsExportUnpaidTickets == 'on')? 'CHECKED' : ''; ?>><br />
							<span class="wcfm_title"><strong><?php _e('Include billing details:', 'woocommerce-events'); ?></strong></span><input type="checkbox" class="wcfm-checkbox" id="WooCommerceEventsExportBillingDetails" name="WooCommerceEventsExportBillingDetails" value="on" <?php echo ($WooCommerceEventsExportBillingDetails == 'on')? 'CHECKED' : ''; ?>><br /><br />
							<a href="<?php echo site_url(); ?>/wp-admin/admin-ajax.php?action=woocommerce_events_csv&event=<?php echo $product_id; ?><?php echo ($WooCommerceEventsExportUnpaidTickets == 'on')? '&exportunpaidtickets=true' : ''; ?><?php echo ($WooCommerceEventsExportBillingDetails == 'on')? '&exportbillingdetails=true' : ''; ?>" class="button wcfm_submit_button" target="_BLANK"><?php _e('Download CSV of attendees', 'woocommerce-events'); ?></a>
							<div class="wcfm-clearfix"></div>
						</p>
					</div>

					<div class="options_group">
            <p><h2><?php _e('Attendee badge options', 'woocommerce-events'); ?></h2><div class="wcfm-clearfix"></div></p>
            <div id="WooCommerceBadgeMessage"></div>
            <p class="form-field">
							<span class="wcfm_title"><strong><?php _e('Choose a badge size:', 'woocommerce-events'); ?></strong></span>
							<select name="WooCommerceBadgeSize" id="WooCommerceBadgeSize" class="wcfm-select">
								<option value="letter_10"<?php echo ($WooCommerceBadgeSize == 'letter_10')? 'SELECTED' : ''; ?>><?php _e( "10 badges per sheet 4.025in x 2in (Avery 5163/8163 Letter size)", 'woocommerce-events'); ?></option>
								<option value="a4_12" <?php echo ($WooCommerceBadgeSize == 'a4_12')? 'SELECTED' : ''; ?>><?php _e( "12 badges per sheet 63.5mm x 72mm (Microsoft W233 A4 size)", 'woocommerce-events'); ?></option>
								<option value="a4_16" <?php echo ($WooCommerceBadgeSize == 'a4_16')? 'SELECTED' : ''; ?>><?php _e( "16 badges per sheet 99mm x 33.9mm (Microsoft W121 A4 size)", 'woocommerce-events'); ?></option>
								<option value="a4_24" <?php echo ($WooCommerceBadgeSize == 'a4_24')? 'SELECTED' : ''; ?>><?php _e( "24 badges per sheet 35mm x 70mm (Microsoft W110 A4 size)", 'woocommerce-events'); ?></option>
								<option value="letter_30" <?php echo ($WooCommerceBadgeSize == 'letter_30')? 'SELECTED' : ''; ?>><?php _e( "30 badges per sheet 2.625in x 1in (Avery 5160/8160 Letter size)", 'woocommerce-events'); ?></option>
								<option value="a4_39" <?php echo ($WooCommerceBadgeSize == 'a4_39')? 'SELECTED' : ''; ?>><?php _e( "39 badges per sheet 66mm x 20.60mm (Microsoft W239 A4 size)", 'woocommerce-events'); ?></option>
								<option value="a4_45" <?php echo ($WooCommerceBadgeSize == 'a4_45')? 'SELECTED' : ''; ?>><?php _e( "45 badges per sheet 38.5mm x 29.9mm (Microsoft W115 A4 size)", 'woocommerce-events'); ?></option>
							</select>
							
							<span class="wcfm_title"><strong><?php _e('Choose field 1:', 'woocommerce-events'); ?></strong></span>
							<select name="WooCommerceBadgeField1" id="WooCommerceBadgeField1" class="wcfm-select">
								<option value="nothing" <?php echo ($WooCommerceBadgeField1 == 'nothing')? 'SELECTED' : ''; ?>><?php _e( "(Nothing)", 'woocommerce-events'); ?></option>
								<option value="barcode" <?php echo ($WooCommerceBadgeField1 == 'barcode')? 'SELECTED' : ''; ?>><?php _e( "Barcode", 'woocommerce-events'); ?></option>
								<option value="event" <?php echo ($WooCommerceBadgeField1 == 'event')? 'SELECTED' : ''; ?>><?php _e( "Event Name Only", 'woocommerce-events'); ?></option>
								<option value="event_var" <?php echo ($WooCommerceBadgeField1 == 'event_var')? 'SELECTED' : ''; ?>><?php _e( "Event Name and Variations", 'woocommerce-events'); ?></option>
								<option value="var_only" <?php echo ($WooCommerceBadgeField1 == 'var_only')? 'SELECTED' : ''; ?>><?php _e( "Variations Only", 'woocommerce-events'); ?></option>
								<option value="ticketnr" <?php echo ($WooCommerceBadgeField1 == 'ticketnr')? 'SELECTED' : ''; ?>><?php _e( "Ticket Number", 'woocommerce-events'); ?></option>
								<option value="name" <?php echo ($WooCommerceBadgeField1 == 'name')? 'SELECTED' : ''; ?>><?php _e( "Attendee Name", 'woocommerce-events'); ?></option>
								<option value="email" <?php echo ($WooCommerceBadgeField1 == 'email')? 'SELECTED' : ''; ?>><?php _e( "Attendee Email", 'woocommerce-events'); ?></option>
								<option value="telephone" <?php echo ($WooCommerceBadgeField1 == 'telephone')? 'SELECTED' : ''; ?>><?php _e( "Attendee Telephone", 'woocommerce-events'); ?></option>
								<option value="company" <?php echo ($WooCommerceBadgeField1 == 'company')? 'SELECTED' : ''; ?>><?php _e( "Attendee Company", 'woocommerce-events'); ?></option>
								<option value="designation" <?php echo ($WooCommerceBadgeField1 == 'designation')? 'SELECTED' : ''; ?>><?php _e( "Attendee Designation", 'woocommerce-events'); ?></option>
								<option value="seat" <?php echo ($WooCommerceBadgeField1 == 'seat')? 'SELECTED' : ''; ?>><?php _e( "Attendee Seat", 'woocommerce-events'); ?></option>
								<?php foreach( $cf_array as $key => $value) {
										echo '<option value="' . $key . '"';
										echo ($WooCommerceBadgeField1 == $key)? 'SELECTED' : '';
										echo '>' . $value . '</option>';

								} ?>
							</select>
							
							<span class="wcfm_title"><strong><?php _e('Choose field 2:', 'woocommerce-events'); ?></strong></span>
							<select name="WooCommerceBadgeField2" id="WooCommerceBadgeField2" class="wcfm-select">
								<option value="nothing" <?php echo ($WooCommerceBadgeField2 == 'nothing')? 'SELECTED' : ''; ?>><?php _e( "(Nothing)", 'woocommerce-events'); ?></option>
								<option value="barcode" <?php echo ($WooCommerceBadgeField2 == 'barcode')? 'SELECTED' : ''; ?>><?php _e( "Barcode", 'woocommerce-events'); ?></option>
								<option value="event" <?php echo ($WooCommerceBadgeField2 == 'event')? 'SELECTED' : ''; ?>><?php _e( "Event Name Only", 'woocommerce-events'); ?></option>
								<option value="event_var" <?php echo ($WooCommerceBadgeField2 == 'event_var')? 'SELECTED' : ''; ?>><?php _e( "Event Name and Variations", 'woocommerce-events'); ?></option>
								<option value="var_only" <?php echo ($WooCommerceBadgeField2 == 'var_only')? 'SELECTED' : ''; ?>><?php _e( "Variations Only", 'woocommerce-events'); ?></option>
								<option value="ticketnr" <?php echo ($WooCommerceBadgeField2 == 'ticketnr')? 'SELECTED' : ''; ?>><?php _e( "Ticket Number", 'woocommerce-events'); ?></option>
								<option value="name" <?php echo ($WooCommerceBadgeField2 == 'name')? 'SELECTED' : ''; ?>><?php _e( "Attendee Name", 'woocommerce-events'); ?></option>
								<option value="email" <?php echo ($WooCommerceBadgeField2 == 'email')? 'SELECTED' : ''; ?>><?php _e( "Attendee Email", 'woocommerce-events'); ?></option>
								<option value="telephone" <?php echo ($WooCommerceBadgeField2 == 'telephone')? 'SELECTED' : ''; ?>><?php _e( "Attendee Telephone", 'woocommerce-events'); ?></option>
								<option value="company" <?php echo ($WooCommerceBadgeField2 == 'company')? 'SELECTED' : ''; ?>><?php _e( "Attendee Company", 'woocommerce-events'); ?></option>
								<option value="designation" <?php echo ($WooCommerceBadgeField2 == 'designation')? 'SELECTED' : ''; ?>><?php _e( "Attendee Designation", 'woocommerce-events'); ?></option>
								<option value="seat" <?php echo ($WooCommerceBadgeField2 == 'seat')? 'SELECTED' : ''; ?>><?php _e( "Attendee Seat", 'woocommerce-events'); ?></option>
								<?php foreach( $cf_array as $key => $value) {
									echo '<option value="' . $key . '"';
									echo ($WooCommerceBadgeField2 == $key)? 'SELECTED' : '';
									echo '>' . $value . '</option>';
								} ?>
							</select>
							
							<span class="wcfm_title"><strong><?php _e('Choose field 3:', 'woocommerce-events'); ?></strong></span>
							<select name="WooCommerceBadgeField3" id="WooCommerceBadgeField3" class="wcfm-select">
								<option value="nothing" <?php echo ($WooCommerceBadgeField3 == 'nothing')? 'SELECTED' : ''; ?>><?php _e( "(Nothing)", 'woocommerce-events'); ?></option>
								<option value="barcode" <?php echo ($WooCommerceBadgeField3 == 'barcode')? 'SELECTED' : ''; ?>><?php _e( "Barcode", 'woocommerce-events'); ?></option>
								<option value="event" <?php echo ($WooCommerceBadgeField3 == 'event')? 'SELECTED' : ''; ?>><?php _e( "Event Name Only", 'woocommerce-events'); ?></option>
								<option value="event_var" <?php echo ($WooCommerceBadgeField3 == 'event_var')? 'SELECTED' : ''; ?>><?php _e( "Event Name and Variations", 'woocommerce-events'); ?></option>
								<option value="var_only" <?php echo ($WooCommerceBadgeField3 == 'var_only')? 'SELECTED' : ''; ?>><?php _e( "Variations Only", 'woocommerce-events'); ?></option>
								<option value="ticketnr" <?php echo ($WooCommerceBadgeField3 == 'ticketnr')? 'SELECTED' : ''; ?>><?php _e( "Ticket Number", 'woocommerce-events'); ?></option>
								<option value="name" <?php echo ($WooCommerceBadgeField3 == 'name')? 'SELECTED' : ''; ?>><?php _e( "Attendee Name", 'woocommerce-events'); ?></option>
								<option value="email" <?php echo ($WooCommerceBadgeField3 == 'email')? 'SELECTED' : ''; ?>><?php _e( "Attendee Email", 'woocommerce-events'); ?></option>
								<option value="telephone" <?php echo ($WooCommerceBadgeField3 == 'telephone')? 'SELECTED' : ''; ?>><?php _e( "Attendee Telephone", 'woocommerce-events'); ?></option>
								<option value="company" <?php echo ($WooCommerceBadgeField3 == 'company')? 'SELECTED' : ''; ?>><?php _e( "Attendee Company", 'woocommerce-events'); ?></option>
								<option value="designation" <?php echo ($WooCommerceBadgeField3 == 'designation')? 'SELECTED' : ''; ?>><?php _e( "Attendee Designation", 'woocommerce-events'); ?></option>
								<option value="seat" <?php echo ($WooCommerceBadgeField3 == 'seat')? 'SELECTED' : ''; ?>><?php _e( "Attendee Seat", 'woocommerce-events'); ?></option>
								<?php foreach( $cf_array as $key => $value) {
										echo '<option value="' . $key . '"';
										echo ($WooCommerceBadgeField3 == $key)? 'SELECTED' : '';
										echo '>' . $value . '</option>';
								} ?>
							</select>
							
							<span class="wcfm_title checkbox_title"><strong><?php _e('Include cut lines?:', 'woocommerce-events'); ?></strong></span>
							<input type="checkbox" class="wcfm-checkbox" name="WooCommerceEventsCutLines" value="on" <?php echo (empty($WooCommerceEventsCutLines) || $WooCommerceEventsCutLines == 'on')? 'CHECKED' : ''; ?>>
							<br /><br />
              <a href="<?php echo site_url(); ?>/wp-admin/admin-ajax.php?action=woocommerce_events_attendee_badges&attendee_show=badges&event=<?php echo $product_id; ?><?php echo '&size=' . $WooCommerceBadgeSize; ?><?php echo '&badgefield1=' . $WooCommerceBadgeField1; ?><?php echo '&badgefield2=' . $WooCommerceBadgeField2; ?><?php echo '&badgefield3=' . $WooCommerceBadgeField3 . '&cutlines=' . $WooCommerceEventsCutLines; ?>" class="button wcfm_submit_button" target="_BLANK"><?php _e('Print attendee badges', 'woocommerce-events'); ?></a>
              <div class="wcfm-clearfix"></div>
            </p>
          </div>

          <div class="options_group">
            <p><h2><?php _e('Print tickets', 'woocommerce-events'); ?></h2><div class="wcfm-clearfix"></div></p>
            <div id="WooCommercePrintTicketMessage"></div>
            <p class="form-field">
							<span class="wcfm_title"><strong><?php _e('Choose a ticket size:', 'woocommerce-events'); ?></strong></span>
							<select name="WooCommercePrintTicketSize" id="WooCommercePrintTicketSize" class="wcfm-select">
								<option value="tickets_avery_letter_10"<?php echo ($WooCommercePrintTicketSize == 'tickets_avery_letter_10')? 'SELECTED' : ''; ?>><?php _e( "10 tickets per sheet (Letter size)", 'woocommerce-events'); ?></option>
								<option value="tickets_letter_10"<?php echo ($WooCommercePrintTicketSize == 'tickets_letter_10')? 'SELECTED' : ''; ?>><?php _e( "10 tickets per sheet 5.5in x 1.75in (Avery 16154 Tickets Letter size)", 'woocommerce-events'); ?></option>
								<option value="tickets_a4_10"<?php echo ($WooCommercePrintTicketSize == 'tickets_a4_10')? 'SELECTED' : ''; ?>><?php _e( "10 tickets per sheet (A4 size)", 'woocommerce-events'); ?></option>
							</select>
            </p>
            
            <p class="form-field">

							<span class="wcfm_title checkbox_title"><strong><?php _e('Logo options:', 'woocommerce-events'); ?></strong></span>
							<input type="radio" name="WooCommerceEventsPrintTicketLogoOption" value="no_logo" <?php echo (empty($WooCommerceEventsPrintTicketLogoOption) || $WooCommerceEventsPrintTicketLogoOption == 'no_logo')? 'CHECKED' : ''; ?>>&nbsp;<?php _e("Don't show the event logo", 'woocommerce-events'); ?>
							<br />
							<span class="wcfm_title checkbox_title"><strong></strong></span>
							<input type="radio" name="WooCommerceEventsPrintTicketLogoOption" value="current_logo" <?php echo (empty($WooCommerceEventsPrintTicketLogoOption) || $WooCommerceEventsPrintTicketLogoOption == 'current_logo')? 'CHECKED' : ''; ?>>&nbsp;<?php _e('Show current event logo', 'woocommerce-events'); ?>
							<br />
							<span class="wcfm_title checkbox_title"><strong></strong></span>
							<input type="radio" name="WooCommerceEventsPrintTicketLogoOption" value="new_logo" <?php echo (empty($WooCommerceEventsPrintTicketLogoOption) || $WooCommerceEventsPrintTicketLogoOption == 'new_logo')? 'CHECKED' : ''; ?>>&nbsp;<?php _e('Upload and show new logo', 'woocommerce-events'); ?>
							<br />
							<br />    
							<?php $WooCommerceEventsPrintTicketLogo = (empty($WooCommerceEventsPrintTicketLogo))? $globalWooCommerceEventsTicketLogo : $WooCommerceEventsPrintTicketLogo; ?>
							<?php
							$WCFM->wcfm_fields->wcfm_generate_form_field( array( "WooCommerceEventsPrintTicketLogo" => array( 'label' => '', 'wcfm_uploader_by_url' => true, 'type' => 'upload', 'label_class' => 'wcfm_title', 'value' => $WooCommerceEventsPrintTicketLogo ) ) );
							?>
							
						 <span class="wcfm_title"><strong><?php _e('Fields to display on the main ticket:', 'woocommerce-events'); ?></strong></span>    
						 <select name="WooCommercePrintTicketField1" id="WooCommercePrintTicketField1" class="wcfm-select">
								<option value="nothing" <?php echo ($WooCommercePrintTicketField1 == 'nothing')? 'SELECTED' : ''; ?>><?php _e( "(Nothing)", 'woocommerce-events'); ?></option>
								<option value="barcode" <?php echo ($WooCommercePrintTicketField1 == 'barcode')? 'SELECTED' : ''; ?>><?php _e( "Barcode", 'woocommerce-events'); ?></option>
								<option value="event" <?php echo ($WooCommercePrintTicketField1 == 'event')? 'SELECTED' : ''; ?>><?php _e( "Event Name Only", 'woocommerce-events'); ?></option>
								<option value="event_var" <?php echo ($WooCommercePrintTicketField1 == 'event_var')? 'SELECTED' : ''; ?>><?php _e( "Event Name and Variations", 'woocommerce-events'); ?></option>
								<option value="location" <?php echo ($WooCommercePrintTicketField1 == 'location')? 'SELECTED' : ''; ?>><?php _e( "Location", 'woocommerce-events'); ?></option>
								<option value="ticketnr" <?php echo ($WooCommercePrintTicketField1 == 'ticketnr')? 'SELECTED' : ''; ?>><?php _e( "Ticket Number", 'woocommerce-events'); ?></option>
								<option value="name" <?php echo ($WooCommercePrintTicketField1 == 'name')? 'SELECTED' : ''; ?>><?php _e( "Attendee Name", 'woocommerce-events'); ?></option>
								<option value="seat" <?php echo ($WooCommercePrintTicketField1 == 'seat')? 'SELECTED' : ''; ?>><?php _e( "Attendee Seat", 'woocommerce-events'); ?></option>
								<?php foreach( $cf_array as $key => $value) {
										echo '<option value="' . $key . '"';
										echo ($WooCommercePrintTicketField1 == $key)? 'SELECTED' : '';
										echo '>' . $value . '</option>';
								} ?>
							 </select>
									
							 <span class="wcfm_title"><strong></span>
							 <select name="WooCommercePrintTicketField1_font" id="WooCommercePrintTicketField1_font" class="wcfm-select">
									<option value="small" <?php echo ($WooCommercePrintTicketField1_font == 'small')? 'SELECTED' : ''; ?>><?php _e( "Small regular text", 'woocommerce-events'); ?></option>
									<option value="small_uppercase" <?php echo ($WooCommercePrintTicketField1_font == 'small_uppercase')? 'SELECTED' : ''; ?>><?php _e( "Small uppercase text", 'woocommerce-events'); ?></option>
									<option value="medium" <?php echo ($WooCommercePrintTicketField1_font == 'medium')? 'SELECTED' : ''; ?>><?php _e( "Medium regular text", 'woocommerce-events'); ?></option>
									<option value="medium_uppercase" <?php echo ($WooCommercePrintTicketField1_font == 'medium_uppercase')? 'SELECTED' : ''; ?>><?php _e( "Medium uppercase text", 'woocommerce-events'); ?></option>
									<option value="large" <?php echo ($WooCommercePrintTicketField1_font == 'large')? 'SELECTED' : ''; ?>><?php _e( "Large regular text", 'woocommerce-events'); ?></option>       
									<option value="large_uppercase" <?php echo ($WooCommercePrintTicketField1_font == 'large_uppercase')? 'SELECTED' : ''; ?>><?php _e( "Large uppercase text", 'woocommerce-events'); ?></option>       
							 </select>
									
							 <span class="wcfm_title"><strong></span>
							 <select name="WooCommercePrintTicketField2" id="WooCommercePrintTicketField2" class="wcfm-select">
								<option value="nothing" <?php echo ($WooCommercePrintTicketField2 == 'nothing')? 'SELECTED' : ''; ?>><?php _e( "(Nothing)", 'woocommerce-events'); ?></option>
								<option value="barcode" <?php echo ($WooCommercePrintTicketField2 == 'barcode')? 'SELECTED' : ''; ?>><?php _e( "Barcode", 'woocommerce-events'); ?></option>
								<option value="event" <?php echo ($WooCommercePrintTicketField2 == 'event')? 'SELECTED' : ''; ?>><?php _e( "Event Name Only", 'woocommerce-events'); ?></option>
								<option value="event_var" <?php echo ($WooCommercePrintTicketField2 == 'event_var')? 'SELECTED' : ''; ?>><?php _e( "Event Name and Variations", 'woocommerce-events'); ?></option>
								<option value="location" <?php echo ($WooCommercePrintTicketField2 == 'location')? 'SELECTED' : ''; ?>><?php _e( "Location", 'woocommerce-events'); ?></option>
								<option value="ticketnr" <?php echo ($WooCommercePrintTicketField2 == 'ticketnr')? 'SELECTED' : ''; ?>><?php _e( "Ticket Number", 'woocommerce-events'); ?></option>
								<option value="name" <?php echo ($WooCommercePrintTicketField2 == 'name')? 'SELECTED' : ''; ?>><?php _e( "Attendee Name", 'woocommerce-events'); ?></option>
								<option value="seat" <?php echo ($WooCommercePrintTicketField2 == 'seat')? 'SELECTED' : ''; ?>><?php _e( "Attendee Seat", 'woocommerce-events'); ?></option>
								<?php foreach( $cf_array as $key => $value) {
										echo '<option value="' . $key . '"';
										echo ($WooCommercePrintTicketField2 == $key)? 'SELECTED' : '';
										echo '>' . $value . '</option>';
								} ?>
							 </select>
									
							 <span class="wcfm_title"><strong></span>
							 <select name="WooCommercePrintTicketField2_font" id="WooCommercePrintTicketField2_font" class="wcfm-select">
								<option value="small" <?php echo ($WooCommercePrintTicketField2_font == 'small')? 'SELECTED' : ''; ?>><?php _e( "Small regular text", 'woocommerce-events'); ?></option>
								<option value="small_uppercase" <?php echo ($WooCommercePrintTicketField2_font == 'small_uppercase')? 'SELECTED' : ''; ?>><?php _e( "Small uppercase text", 'woocommerce-events'); ?></option>
								<option value="medium" <?php echo ($WooCommercePrintTicketField2_font == 'medium')? 'SELECTED' : ''; ?>><?php _e( "Medium regular text", 'woocommerce-events'); ?></option>
								<option value="medium_uppercase" <?php echo ($WooCommercePrintTicketField2_font == 'medium_uppercase')? 'SELECTED' : ''; ?>><?php _e( "Medium uppercase text", 'woocommerce-events'); ?></option>
								<option value="large" <?php echo ($WooCommercePrintTicketField2_font == 'large')? 'SELECTED' : ''; ?>><?php _e( "Large regular text", 'woocommerce-events'); ?></option>       
								<option value="large_uppercase" <?php echo ($WooCommercePrintTicketField2_font == 'large_uppercase')? 'SELECTED' : ''; ?>><?php _e( "Large uppercase text", 'woocommerce-events'); ?></option>
							 </select>
							
							 <span class="wcfm_title"><strong></span>
							 <select name="WooCommercePrintTicketField3" id="WooCommercePrintTicketField3" class="wcfm-select">
								<option value="nothing" <?php echo ($WooCommercePrintTicketField3 == 'nothing')? 'SELECTED' : ''; ?>><?php _e( "(Nothing)", 'woocommerce-events'); ?></option>
								<option value="barcode" <?php echo ($WooCommercePrintTicketField3 == 'barcode')? 'SELECTED' : ''; ?>><?php _e( "Barcode", 'woocommerce-events'); ?></option>
								<option value="event" <?php echo ($WooCommercePrintTicketField3 == 'event')? 'SELECTED' : ''; ?>><?php _e( "Event Name Only", 'woocommerce-events'); ?></option>
								<option value="event_var" <?php echo ($WooCommercePrintTicketField3 == 'event_var')? 'SELECTED' : ''; ?>><?php _e( "Event Name and Variations", 'woocommerce-events'); ?></option>
								<option value="location" <?php echo ($WooCommercePrintTicketField3 == 'location')? 'SELECTED' : ''; ?>><?php _e( "Location", 'woocommerce-events'); ?></option>
								<option value="ticketnr" <?php echo ($WooCommercePrintTicketField3 == 'ticketnr')? 'SELECTED' : ''; ?>><?php _e( "Ticket Number", 'woocommerce-events'); ?></option>
								<option value="name" <?php echo ($WooCommercePrintTicketField3 == 'name')? 'SELECTED' : ''; ?>><?php _e( "Attendee Name", 'woocommerce-events'); ?></option>
								<option value="seat" <?php echo ($WooCommercePrintTicketField3 == 'seat')? 'SELECTED' : ''; ?>><?php _e( "Attendee Seat", 'woocommerce-events'); ?></option>
								<?php foreach( $cf_array as $key => $value) {
										echo '<option value="' . $key . '"';
										echo ($WooCommercePrintTicketField3 == $key)? 'SELECTED' : '';
										echo '>' . $value . '</option>';
								} ?>
							 </select>
									
							<span class="wcfm_title"><strong></span>
							 <select name="WooCommercePrintTicketField3_font" id="WooCommercePrintTicketField3_font" class="wcfm-select">
								<option value="small" <?php echo ($WooCommercePrintTicketField3_font == 'small')? 'SELECTED' : ''; ?>><?php _e( "Small regular text", 'woocommerce-events'); ?></option>
								<option value="small_uppercase" <?php echo ($WooCommercePrintTicketField3_font == 'small_uppercase')? 'SELECTED' : ''; ?>><?php _e( "Small uppercase text", 'woocommerce-events'); ?></option>
								<option value="medium" <?php echo ($WooCommercePrintTicketField3_font == 'medium')? 'SELECTED' : ''; ?>><?php _e( "Medium regular text", 'woocommerce-events'); ?></option>
								<option value="medium_uppercase" <?php echo ($WooCommercePrintTicketField3_font == 'medium_uppercase')? 'SELECTED' : ''; ?>><?php _e( "Medium uppercase text", 'woocommerce-events'); ?></option>
								<option value="large" <?php echo ($WooCommercePrintTicketField3_font == 'large')? 'SELECTED' : ''; ?>><?php _e( "Large regular text", 'woocommerce-events'); ?></option>       
								<option value="large_uppercase" <?php echo ($WooCommercePrintTicketField3_font == 'large_uppercase')? 'SELECTED' : ''; ?>><?php _e( "Large uppercase text", 'woocommerce-events'); ?></option>
							 </select>
											
							<span class="wcfm_title"><strong><?php _e('Fields to display on the ticket stub:', 'woocommerce-events'); ?></strong></span>    
							<select name="WooCommercePrintTicketField4" id="WooCommercePrintTicketField4" class="wcfm-select">
								<option value="nothing" <?php echo ($WooCommercePrintTicketField4 == 'nothing')? 'SELECTED' : ''; ?>><?php _e( "(Nothing)", 'woocommerce-events'); ?></option>
								<option value="barcode" <?php echo ($WooCommercePrintTicketField4 == 'barcode')? 'SELECTED' : ''; ?>><?php _e( "Barcode", 'woocommerce-events'); ?></option>
								<option value="event" <?php echo ($WooCommercePrintTicketField4 == 'event')? 'SELECTED' : ''; ?>><?php _e( "Event Name Only", 'woocommerce-events'); ?></option>
								<option value="event_var" <?php echo ($WooCommercePrintTicketField4 == 'event_var')? 'SELECTED' : ''; ?>><?php _e( "Event Name and Variations", 'woocommerce-events'); ?></option>
								<option value="location" <?php echo ($WooCommercePrintTicketField4 == 'location')? 'SELECTED' : ''; ?>><?php _e( "Location", 'woocommerce-events'); ?></option>
								<option value="ticketnr" <?php echo ($WooCommercePrintTicketField4 == 'ticketnr')? 'SELECTED' : ''; ?>><?php _e( "Ticket Number", 'woocommerce-events'); ?></option>
								<option value="name" <?php echo ($WooCommercePrintTicketField4 == 'name')? 'SELECTED' : ''; ?>><?php _e( "Attendee Name", 'woocommerce-events'); ?></option>
								<option value="seat" <?php echo ($WooCommercePrintTicketField4 == 'seat')? 'SELECTED' : ''; ?>><?php _e( "Attendee Seat", 'woocommerce-events'); ?></option>
								<?php foreach( $cf_array as $key => $value) {
										echo '<option value="' . $key . '"';
										echo ($WooCommercePrintTicketField4 == $key)? 'SELECTED' : '';
										echo '>' . $value . '</option>';
								} ?>
							</select>
									
							<span class="wcfm_title"><strong></span>
							<select name="WooCommercePrintTicketField4_font" id="WooCommercePrintTicketField4_font" class="wcfm-select">
								<option value="small" <?php echo ($WooCommercePrintTicketField4_font == 'small')? 'SELECTED' : ''; ?>><?php _e( "Small regular text", 'woocommerce-events'); ?></option>
								<option value="small_uppercase" <?php echo ($WooCommercePrintTicketField4_font == 'small_uppercase')? 'SELECTED' : ''; ?>><?php _e( "Small uppercase text", 'woocommerce-events'); ?></option>
								<option value="medium" <?php echo ($WooCommercePrintTicketField4_font == 'medium')? 'SELECTED' : ''; ?>><?php _e( "Medium regular text", 'woocommerce-events'); ?></option>
								<option value="medium_uppercase" <?php echo ($WooCommercePrintTicketField4_font == 'medium_uppercase')? 'SELECTED' : ''; ?>><?php _e( "Medium uppercase text", 'woocommerce-events'); ?></option>
								<option value="large" <?php echo ($WooCommercePrintTicketField4_font == 'large')? 'SELECTED' : ''; ?>><?php _e( "Large regular text", 'woocommerce-events'); ?></option>       
								<option value="large_uppercase" <?php echo ($WooCommercePrintTicketField4_font == 'large_uppercase')? 'SELECTED' : ''; ?>><?php _e( "Large uppercase text", 'woocommerce-events'); ?></option>
							</select>
							
							<span class="wcfm_title"><strong></span>
							<select name="WooCommercePrintTicketField5" id="WooCommercePrintTicketField5" class="wcfm-select">
								<option value="nothing" <?php echo ($WooCommercePrintTicketField5 == 'nothing')? 'SELECTED' : ''; ?>><?php _e( "(Nothing)", 'woocommerce-events'); ?></option>
								<option value="barcode" <?php echo ($WooCommercePrintTicketField5 == 'barcode')? 'SELECTED' : ''; ?>><?php _e( "Barcode", 'woocommerce-events'); ?></option>
								<option value="event" <?php echo ($WooCommercePrintTicketField5 == 'event')? 'SELECTED' : ''; ?>><?php _e( "Event Name Only", 'woocommerce-events'); ?></option>
								<option value="event_var" <?php echo ($WooCommercePrintTicketField5 == 'event_var')? 'SELECTED' : ''; ?>><?php _e( "Event Name and Variations", 'woocommerce-events'); ?></option>
								<option value="location" <?php echo ($WooCommercePrintTicketField5 == 'location')? 'SELECTED' : ''; ?>><?php _e( "Location", 'woocommerce-events'); ?></option>
								<option value="ticketnr" <?php echo ($WooCommercePrintTicketField5 == 'ticketnr')? 'SELECTED' : ''; ?>><?php _e( "Ticket Number", 'woocommerce-events'); ?></option>
								<option value="name" <?php echo ($WooCommercePrintTicketField5 == 'name')? 'SELECTED' : ''; ?>><?php _e( "Attendee Name", 'woocommerce-events'); ?></option>
								<option value="seat" <?php echo ($WooCommercePrintTicketField5 == 'seat')? 'SELECTED' : ''; ?>><?php _e( "Attendee Seat", 'woocommerce-events'); ?></option>
								<?php foreach( $cf_array as $key => $value) {
										echo '<option value="' . $key . '"';
										echo ($WooCommercePrintTicketField5 == $key)? 'SELECTED' : '';
										echo '>' . $value . '</option>';
								} ?>
							</select>
									
							<span class="wcfm_title"><strong></span>
							<select name="WooCommercePrintTicketField5_font" id="WooCommercePrintTicketField5_font" class="wcfm-select">
								<option value="small" <?php echo ($WooCommercePrintTicketField5_font == 'small')? 'SELECTED' : ''; ?>><?php _e( "Small regular text", 'woocommerce-events'); ?></option>
								<option value="small_uppercase" <?php echo ($WooCommercePrintTicketField5_font == 'small_uppercase')? 'SELECTED' : ''; ?>><?php _e( "Small uppercase text", 'woocommerce-events'); ?></option>
								<option value="medium" <?php echo ($WooCommercePrintTicketField5_font == 'medium')? 'SELECTED' : ''; ?>><?php _e( "Medium regular text", 'woocommerce-events'); ?></option>
								<option value="medium_uppercase" <?php echo ($WooCommercePrintTicketField5_font == 'medium_uppercase')? 'SELECTED' : ''; ?>><?php _e( "Medium uppercase text", 'woocommerce-events'); ?></option>
								<option value="large" <?php echo ($WooCommercePrintTicketField5_font == 'large')? 'SELECTED' : ''; ?>><?php _e( "Large regular text", 'woocommerce-events'); ?></option>       
								<option value="large_uppercase" <?php echo ($WooCommercePrintTicketField5_font == 'large_uppercase')? 'SELECTED' : ''; ?>><?php _e( "Large uppercase text", 'woocommerce-events'); ?></option>
							</select>
							
							<span class="wcfm_title"><strong></span>
							<select name="WooCommercePrintTicketField6" id="WooCommercePrintTicketField6" class="wcfm-select">
								<option value="nothing" <?php echo ($WooCommercePrintTicketField6 == 'nothing')? 'SELECTED' : ''; ?>><?php _e( "(Nothing)", 'woocommerce-events'); ?></option>
								<option value="barcode" <?php echo ($WooCommercePrintTicketField6 == 'barcode')? 'SELECTED' : ''; ?>><?php _e( "Barcode", 'woocommerce-events'); ?></option>
								<option value="event" <?php echo ($WooCommercePrintTicketField6 == 'event')? 'SELECTED' : ''; ?>><?php _e( "Event Name Only", 'woocommerce-events'); ?></option>
								<option value="event_var" <?php echo ($WooCommercePrintTicketField6 == 'event_var')? 'SELECTED' : ''; ?>><?php _e( "Event Name and Variations", 'woocommerce-events'); ?></option>
								<option value="location" <?php echo ($WooCommercePrintTicketField6 == 'location')? 'SELECTED' : ''; ?>><?php _e( "Location", 'woocommerce-events'); ?></option>
								<option value="ticketnr" <?php echo ($WooCommercePrintTicketField6 == 'ticketnr')? 'SELECTED' : ''; ?>><?php _e( "Ticket Number", 'woocommerce-events'); ?></option>
								<option value="name" <?php echo ($WooCommercePrintTicketField6 == 'name')? 'SELECTED' : ''; ?>><?php _e( "Attendee Name", 'woocommerce-events'); ?></option>
								<option value="seat" <?php echo ($WooCommercePrintTicketField6 == 'seat')? 'SELECTED' : ''; ?>><?php _e( "Attendee Seat", 'woocommerce-events'); ?></option>
								<?php foreach( $cf_array as $key => $value) {
										echo '<option value="' . $key . '"';
										echo ($WooCommercePrintTicketField6 == $key)? 'SELECTED' : '';
										echo '>' . $value . '</option>';
								} ?>
							</select>
								
							<span class="wcfm_title"><strong></span>
							<select name="WooCommercePrintTicketField6_font" id="WooCommercePrintTicketField6_font" class="wcfm-select">
								<option value="small" <?php echo ($WooCommercePrintTicketField6_font == 'small')? 'SELECTED' : ''; ?>><?php _e( "Small regular text", 'woocommerce-events'); ?></option>
								<option value="small_uppercase" <?php echo ($WooCommercePrintTicketField6_font == 'small_uppercase')? 'SELECTED' : ''; ?>><?php _e( "Small uppercase text", 'woocommerce-events'); ?></option>
								<option value="medium" <?php echo ($WooCommercePrintTicketField6_font == 'medium')? 'SELECTED' : ''; ?>><?php _e( "Medium regular text", 'woocommerce-events'); ?></option>
								<option value="medium_uppercase" <?php echo ($WooCommercePrintTicketField6_font == 'medium_uppercase')? 'SELECTED' : ''; ?>><?php _e( "Medium uppercase text", 'woocommerce-events'); ?></option>
								<option value="large" <?php echo ($WooCommercePrintTicketField6_font == 'large')? 'SELECTED' : ''; ?>><?php _e( "Large regular text", 'woocommerce-events'); ?></option>       
								<option value="large_uppercase" <?php echo ($WooCommercePrintTicketField6_font == 'large_uppercase')? 'SELECTED' : ''; ?>><?php _e( "Large uppercase text", 'woocommerce-events'); ?></option>
							</select>
                      
            </p>
            
            <p class="form-field">     
							<span class="wcfm_title checkbox_title"><?php _e('Include cut lines?:', 'woocommerce-events'); ?></span>
							<input type="checkbox" class="wcfm-checkbox" name="WooCommerceEventsCutLinesPrintTicket" value="on" <?php echo (empty($WooCommerceEventsCutLinesPrintTicket) || $WooCommerceEventsCutLinesPrintTicket == 'on')? 'CHECKED' : ''; ?>>
							<br /><br />
							<a href="<?php echo site_url(); ?>/wp-admin/admin-ajax.php?action=woocommerce_events_attendee_badges&attendee_show=tickets&event=<?php echo $product_id; ?><?php echo '&size=' . $WooCommercePrintTicketSize . '&ticketfield1=' . $WooCommercePrintTicketField1 . '&font1=' . $WooCommercePrintTicketField1_font . '&font2=' . $WooCommercePrintTicketField2_font . '&font3=' . $WooCommercePrintTicketField3_font . '&font4=' . $WooCommercePrintTicketField4_font . '&font5=' . $WooCommercePrintTicketField5_font . '&font6=' . $WooCommercePrintTicketField6_font . '&ticketfield2=' . $WooCommercePrintTicketField2 . '&ticketfield3=' . $WooCommercePrintTicketField3 . '&ticketfield4=' . $WooCommercePrintTicketField4 . '&ticketfield5=' . $WooCommercePrintTicketField5 . '&ticketfield6=' . $WooCommercePrintTicketField6 . '&cutlines=' . $WooCommerceEventsCutLinesPrintTicket . '&showlogo=' . $WooCommerceEventsPrintTicketLogoOption; ?>" class="button wcfm_submit_button" target="_BLANK"><?php _e('Print tickets', 'woocommerce-events'); ?></a>
							<div class="wcfm-clearfix"></div>
            </p>
          </div>
        <?php endif; ?>
        
        <?php if ( WCFMu_Dependencies::wcfm_wc_fooevents_custom_atendee() ) { ?>
        	<div class="fooevents_custom_attendee_fields_wrap">
        	  <div class="wcfm-clearfix"></div><br />
						<div class="options_group">
						  <h2><?php _e( 'Custom Attendee Fields', 'woocommerce-events' ); ?></h2>
						  <div class="wcfm-clearfix"></div>
							<table id="fooevents_custom_attendee_fields_options_table" cellpadding="0" cellspacing="0">
								<thead>
									<tr>
										<th><?php _e( 'Label', 'woocommerce-events'); ?></th>
										<th><?php _e( 'Type', 'woocommerce-events'); ?></th>
										<th><?php _e( 'Options', 'woocommerce-events'); ?></th>
										<th><?php _e( 'Required', 'woocommerce-events'); ?></th>
										<th></th>
									</tr>
								</thead>
								<tbody>
									<?php foreach($fooevents_custom_attendee_fields_options as $option_key => $option) :?>
										<?php $option_ids = array_keys($option); ?>
										<?php $option_values = array_values($option); ?>
										<?php $x = 0; $num_option_ids = count($option_ids); $num_option_values = count($option_values); ?>
										<?php if($num_option_ids == $num_option_values): ?>
											<tr id="<?php echo $option_key; ?>">
												<td><input type="text" id="<?php echo $option_ids[0]; ?>" name="<?php echo $option_ids[0]; ?>" class="fooevents_custom_attendee_fields_label" value="<?php echo $option_values[0]; ?>" autocomplete="off" maxlength="150"/></td>
												<td>
													<select id="<?php echo $option_ids[1]; ?>" name="<?php echo $option_ids[1]; ?>" class="fooevents_custom_attendee_fields_type" autocomplete="off">
														<option value="text" <?php echo ($option_values[1] == 'text')? 'SELECTED' : ''; ?>>Text</option>
														<option value="textarea" <?php echo ($option_values[1] == 'textarea')? 'SELECTED' : ''; ?>>Textarea</option>
														<option value="select" <?php echo ($option_values[1] == 'select')? 'SELECTED' : ''; ?>>Select</option>
													</select>
												</td>
												<td>
													<input id="<?php echo $option_ids[2]; ?>" name="<?php echo $option_ids[2]; ?>" class="fooevents_custom_attendee_fields_options" type="text" value="<?php echo $option_values[2]; ?>" <?php echo ($option_values[1] == 'select')? '' : 'disabled'; ?> autocomplete="off" />    
												</td>
												<td>
													<select id="<?php echo $option_ids[3]; ?>" name="<?php echo $option_ids[3]; ?>" class="fooevents_custom_attendee_fields_req" autocomplete="off">
														<option value="true" <?php echo ($option_values[3] == 'true')? 'SELECTED' : ''; ?>>Yes</option>
														<option value="false" <?php echo ($option_values[3] == 'false')? 'SELECTED' : ''; ?>>No</option>
													</select>
												</td>
												<td><a href="#" class="fooevents_custom_attendee_fields_remove" class="fooevents_custom_attendee_fields_remove">[X]</a></td>
											</tr>
										<?php endif; ?>
									<?php endforeach; ?>
								</tbody>
							</table>    
						</div>
				  </div>
					<div id="fooevents_custom_attendee_fields_info">
							<p><a href="#" id="fooevents_custom_attendee_fields_new_field" class='button button-primary'>+ New field</a></p>
							<p class="description"><?php _e( 'When using the \'Select\' option, seperate the options using a pipe symbol. Example: Small|Medium|Large.', 'woocommerce-events'); ?></p>
					</div>
				  <input type="hidden" id="fooevents_custom_attendee_fields_options_serialized" name="fooevents_custom_attendee_fields_options_serialized" value="<?php echo $fooevents_custom_attendee_fields_options_serialized; ?>" autocomplete="off" />
        <?php } ?>
        
        <?php if ( WCFMu_Dependencies::wcfm_wc_fooevents_seating() ) { ?>
        	 <div class="fooevents_seating_wrap">
        	   <div class="wcfm-clearfix"></div><br />
						 <div class="options_group">
							 <h2><?php _e( 'Seating', 'woocommerce-events' ); ?></h2>
							 <div class="wcfm-clearfix"></div>
							 <table id="fooevents_seating_options_table" cellpadding="0" cellspacing="0">
									<thead>
										<tr>
											<th><?php _e( 'Area Name (e.g. Row 1, Table 1, etc.)', 'woocommerce-events'); ?></th>
											<th><?php _e( 'Available Seats / Spaces', 'woocommerce-events'); ?></th>
											<th><?php _e( 'Variation', 'woocommerce-events'); ?></th>
											<th></th>
										</tr>
									</thead>
									<tbody>
										<?php foreach($fooevents_seating_options as $option_key => $option) : ?>
											<?php $option_ids = array_keys($option); ?>
											<?php $option_values = array_values($option); ?>
											<?php $x = 0; $num_option_ids = count($option_ids); $num_option_values = count($option_values); ?>
											<?php if($num_option_ids == $num_option_values): ?>
												<tr id="<?php echo $option_key; ?>">
													<td><input type="text" id="<?php echo $option_ids[0]; ?>" name="<?php echo $option_ids[0]; ?>" class="fooevents_seating_row_name" value="<?php echo $option_values[0]; ?>" autocomplete="off" maxlength="70"/></td>
													<td>
														<input type="number" min="1" max="50" id="<?php echo $option_ids[1]; ?>" name="<?php echo $option_ids[1]; ?>" class="fooevents_seating_number_seats"  value="<?php echo ($option_values[1]); ?>" >
													</td>
													<td>
													  <select id="<?php echo $option_ids[2]; ?>" name="<?php echo $option_ids[2]; ?>" class="fooevents_seating_variations">
														  <?php
															echo '<option value="default"';
															echo ($option_values[2] == "default")? ' SELECTED' : '';       
															echo '>Default</option>';
															$handle = new WC_Product_Variable($product_id);
															$variations1 = $handle->get_children();
															foreach ($variations1 as $value) {
															$single_variation=new WC_Product_Variation($value);
																
																//  if (!empty($single_variation->get_price())) {
																			echo '<option  value="'.$value.'"';
																			echo ($option_values[2] == $value)? ' SELECTED' : ''; 
																			echo '>'.implode(" / ", $single_variation->get_variation_attributes()).' - '.get_woocommerce_currency_symbol().$single_variation->get_price().'</option>';
															 //   }
															}
													    ?>
													  </select>
													</td>
													
													<td><a href="#" class="fooevents_seating_remove" class="fooevents_seating_remove">[X]</a></td>
												</tr>
											<?php endif; ?>
										<?php endforeach; ?>
									</tbody>
								</table>    
							</div>
						</div>
							 
						<div id="fooevents_seating_dialog" title="Seating Chart">
								
						</div>
			
						<div id="fooevents_seating_info">
							<p><a href="#" id="fooevents_seating_new_field" class='button button-primary'>+ New row</a><a id="fooevents_seating_chart" class='button button-primary'>View seating chart</a></p>
						</div>
						<input type="hidden" id="fooevents_seating_options_serialized" name="fooevents_seating_options_serialized" value="<?php echo $fooevents_seating_options_serialized; ?>" autocomplete="off" />
						<input type="hidden" id="fooevents_seats_unavailable_serialized" name="fooevents_seats_unavailable_serialized" value="<?php echo get_post_meta($product_id, 'fooevents_seats_unavailable_serialized', true); ?>" autocomplete="off" />
						<div id="fooevents_variations" style="display:none">
							<?php
							$handle=new WC_Product_Variable($product_id);
							$variations1=$handle->get_children();
							echo '<option value="default">Default</option>';
							foreach ($variations1 as $value) {
									$single_variation = new WC_Product_Variation($value);
								//  if (!empty($single_variation->get_price())) {
											echo '<option  value="'.$value.'">'.implode(" / ", $single_variation->get_variation_attributes()).' - '.get_woocommerce_currency_symbol().$single_variation->get_price().'</option>';
							//    }
							}
							?>
						</div>
          <?php } ?>
        
          <?php if ( WCFMu_Dependencies::wcfm_wc_fooevents_pdfticket() ) { ?>
          	<div id="fooevents_pdf_ticket_settings" class="panel woocommerce_options_panel">
          	  <div class="wcfm-clearfix"></div><br />
          	  <h2><?php _e( 'PDF Ticket Setting', 'woocommerce-events' ); ?></h2>
							<div class="wcfm-clearfix"></div>
							<div class="options_group">
								<p class="form-field">
								  <span clas="wcfm_title wcfm_full_ele"><strong><?php _e('Email text:', 'fooevents-pdf-tickets'); ?></strong></span>
								  <textarea class="wcfm-textarea wcfm_full_ele" name="FooEventsPDFTicketsEmailText" id="FooEventsPDFTicketsEmailText"><?php echo $FooEventsPDFTicketsEmailText ?></textarea>
								</p>
							</div>
							<div class="options_group">
								<p class="form-field">
								  <span clas="wcfm_title wcfm_full_ele"><strong><?php _e('Ticket footer text:', 'fooevents-pdf-tickets'); ?></strong></span>
								  <textarea class="wcfm-textarea wcfm_full_ele" name="FooEventsTicketFooterText" id="FooEventsTicketFooterText"><?php echo $FooEventsTicketFooterText ?></textarea>
								</p>
							</div>
						</div>
          <?php } ?>
      </div>
	  </div>
	</div>
</div>