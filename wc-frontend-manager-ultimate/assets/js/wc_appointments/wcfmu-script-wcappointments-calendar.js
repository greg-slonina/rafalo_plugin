jQuery(document).ready(function($) {
	$( '.tablenav select, .tablenav input' ).change(function() {
		$( '#mainform' ).submit();
	});

	$( '.calendar_day' ).datepicker({
		dateFormat: 'yy-mm-dd',
		numberOfMonths: 1,
		showOtherMonths: true,
		changeMonth: true,
		showButtonPanel: true,
		minDate: null
	});

	// Display current time on calendar
	var current_date = $( '.calendar_day' ).val();
	var d = new Date();
	var month = d.getMonth()+1;
	var day = d.getDate();
	var today = d.getFullYear() + '-' + ( month < 10 ? '0' : '' ) + month + '-' + ( day < 10 ? '0' : '' ) + day;
	var calendar_h = $( '.bytime.appointments' ).height();

	if ( current_date == today ) {
		var current_time = d.getHours() * 60 + d.getMinutes();
		var current_time_locale = d.toLocaleTimeString('en-US', {hour: '2-digit', minute:'2-digit'}).toLowerCase();
		var indicator_top = Math.round( calendar_h / ( 60 * 24 ) * current_time );
		$( '.bytime.appointments' ).append( '<div class=\"time_indicator tips\" title=\"'+ current_time_locale +'\"></div>' );
		$( '.time_indicator' ).css( {top: indicator_top} );
		$( '.time_indicator' ).tipTip();
	}

	setInterval( set_indicator, 60000 );

	function set_indicator() {
		var dt = new Date();
		var current_time = dt.getHours() * 60 + dt.getMinutes();
		var current_time_locale_updated = dt.toLocaleTimeString('en-US', {hour: '2-digit', minute:'2-digit'}).toLowerCase();
		var indicator_top = Math.round( calendar_h / ( 60 * 24 ) * current_time);
		$( '.time_indicator' ).css( {top: indicator_top} );
		$( '.time_indicator' ).attr( 'title', current_time_locale_updated );
		$( '.time_indicator' ).tipTip();
	}

	// Scroll to clicked hours label
	$('.hours label').click(function(){
		var e = $(this);
		$('.calendar_wrapper').animate({
			scrollTop: e.position().top
		}, 300);
	});
	
	$('.wcfm_appointment_card').each(function() {
	  $(this).click(function(event) {
	  	event.preventDefault();
	  	$card = $(this);
			jQuery("#aptProduct").html($card.data('product-title'));
			jQuery("#aptStart").html($card.data('appointment-when'));
			jQuery("#aptDuration").html($card.data('appointment-duration'));
			jQuery("#aptQuantity").html($card.data('appointment-qty'));
			jQuery("#aptStaff").html($card.data('appointment-staff'));
			jQuery("#aptCustName").html($card.data('customer-name'));
			jQuery("#aptCustEmail").html($card.data('customer-email'));
			jQuery("#aptCustPhone").html($card.data('customer-phone'));
			jQuery("#aptLink").attr('href', $card.find('a').attr('href'));
			
			jQuery.colorbox( { html: jQuery('#aptContent').html(), innerWidth: '425' } );
	  });
	});
});