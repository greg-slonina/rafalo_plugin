/**
 * File for configuring Full Calendar
 * @namespace bkap_calendar
 * @since 2.5.0
 */
jQuery(document).ready(function($) {
	
	var date = new Date();
	var d = date.getDate();
	var m = date.getMonth();
	var y = date.getFullYear();
	
    $('#calendar').fullCalendar({
    	/* SETTINGS */ 
    	header: {
    		left: 'prev, next today',
    		center: 'title',
    		right: 'month, agendaWeek, agendaDay',
    		ignoreTimezone: false    		
    	},
    	month: true,
    	week: true,
    	day: true,
    	agenda: false,
    	basic: true,
    	eventLimit: true, // If you set a number it will hide the itens
        eventLimitText: "More", // Default is `more` (or "more" in the lang you pick in the option)
        theme: true,
    	
    	/*
			defaultView option used to define which view to show by default,
			for example we have used agendaWeek.
		*/
		defaultView: 'agendaWeek',
		
		/*
			selectable:true will enable user to select datetime slot
			selectHelper will add helpers for selectable.
		*/
		selectable: false,
		selectHelper: false,
		
		/*
			editable: true allow user to edit events.
		*/
		editable: false,
		/*
			events is the main option for calendar.
			for demo we have added predefined events in json object.
		*/
		
		//options
		aspectRatio: 1.9,

		events: bkap.pluginurl,
		
	    loading: function( isLoading, view ) {
	    	if( isLoading == true ) {
	    		$( "#bkap_events_loader" ).show();
	    	} else if( isLoading == false ) {
	    		$( "#bkap_events_loader" ).hide();
	    	}
	    },
	    
	    timeFormat: {
			   agenda: 'h(:mm)t'
			},
		
		eventRender: function(event, element) {
			
				element.css('cursor', 'pointer'); // this is used to disply the cursor as a hand for events.
				var event_data = { action : 'wcfm_tych_booking_calender_content', order_id : event.id, event_value : event.value  };
				element.qtip({
					content:{
						text : 'Loading...',
						button: 'Close', // It will disply Close button on the tool tip.
						ajax : {
							url : bkap.ajaxurl,
							type : "POST",
							data : event_data
						}
					},
					show: {
                        event: 'click', // Show tooltip only on click of the vent
                        solo: true // Disply only One tool tip at time, hide other all tool tip
                     },
					position: {
						my: 'bottom right', // this is used for positioning the bottom "V" icon of tool tip.
						at: 'top right' // this is used for postioning the content box of tool tip.

					},
					hide: 'unfocus', //this is used to keep the hover effect untill click outside on calender. For clicking the order number
					
				     style: {
					      classes: 'qtip-light qtip-shadow'
					   }
				});
		}
	});
    
});