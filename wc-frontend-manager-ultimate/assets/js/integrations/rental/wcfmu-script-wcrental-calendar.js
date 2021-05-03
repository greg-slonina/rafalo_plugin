(function() {
  'use_strict';

  var events = [];
  for(var key in WCFMRENTALFULLCALENDER) {
    events.push(WCFMRENTALFULLCALENDER[key]);
  }
  
  jQuery('#eventContent').hide();

  jQuery('#wcfm-rental-calendar').fullCalendar({
    header: {
      left: 'prev,next today',
      center: 'title',
      right: 'month,agendaWeek,agendaDay'
    },
    events: events,
    eventRender: function (event, element) {
    	qtipDescription = '';
      if(event.post_status === 'wc-pending') {
        qtipDescription = 'Pending Payment';
      }
      if(event.post_status === 'wc-processing') {
        qtipDescription = 'Processing';
      }
      if(event.post_status === 'wc-on-hold') {
        qtipDescription = 'On Hold';
      }
      if(event.post_status === 'wc-completed') {
        qtipDescription = 'Completed';
      }
      if(event.post_status === 'wc-cancelled') {
        qtipDescription = 'Cancelled';
      }
      if(event.post_status === 'wc-refunded') {
        qtipDescription = 'Refunded';
      }
      if(event.post_status === 'wc-failed') {
        qtipDescription = 'Failed';
      }
      if(event.post_status === 'wc-declined') {
        qtipDescription = 'Declined';
      }
      if(event.post_status === 'wc-booking-complete') {
        qtipDescription = 'Booking Complete';
      }
      if(event.post_status === 'wc-posted') {
        qtipDescription = 'Posted';
      }
      element.qtip({
        content: qtipDescription,
        style: {
          classes: 'qtip-dark qtip-shadow qtip-rounded qtip-wcfm-css qtip-wcfm-core-css'
        },
        position: {
          my: 'bottom left',  // Position my top left...
          at: 'top right', // at the bottom right of...
          target: element // my target
        },
      });
      element.attr('href', 'javascript:void(0);');
      element.click(function() {
        jQuery("#eventProduct").html(event.title);
        jQuery("#eventProduct").attr('href', event.link);
        jQuery("#startTime").html(moment(event.start).format('MMM Do h:mm A'));
        jQuery("#endTime").html(moment(event.end).format('MMM Do h:mm A'));
        jQuery("#eventInfo").html(event.description);
        jQuery("#eventLink").attr('href', event.url);
        
        jQuery.colorbox( { html: jQuery('#eventContent').html(), innerWidth: '425' } );
        /*jQuery.magnificPopup.open({
          items: {
            src: '#eventContent',
            type: 'inline'
          }
        });*/
      });
    },
    eventAfterRender: function (event, element, view) {

      if (event.post_status === 'wc-pending') {
        element.css('background-color', '#7266BA');
      }
      if (event.post_status === 'wc-processing') {
        element.css('background-color', '#23B7E5');
      }
      if (event.post_status === 'wc-on-hold') {
        element.css('background-color', '#FAD733');
        element.css('color', '#000');
      }
      if (event.post_status === 'wc-completed') {
        element.css('background-color', '#27C24C');
      }
      if (event.post_status === 'wc-cancelled') {
        element.css('background-color', '#a00');
      }
      if (event.post_status === 'wc-refunded') {
        element.css('background-color', '#DDD');
      }
      if (event.post_status === 'wc-failed') {
        element.css('background-color', '#EE3939');
      }
    },
  });

})(jQuery);