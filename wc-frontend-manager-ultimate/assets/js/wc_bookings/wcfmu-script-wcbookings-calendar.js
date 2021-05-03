jQuery(function() {
	jQuery(".tablenav select, .tablenav input").change(function() {
	  jQuery("#mainform").submit();
	});
  jQuery( '.calendar_day' ).datepicker({
	 dateFormat: 'yy-mm-dd',
	 numberOfMonths: 1,
  });
  
	// Tooltips
	jQuery(".bookings li").each(function() {
		jQuery(this).qtip({
			content: jQuery(this).attr('data-tip'),
			position: {
				my: 'top center',
				at: 'bottom center',
				viewport: jQuery(window)
			},
			show: {
				event: 'mouseover',
				solo: true,
			},
			hide: {
				inactive: 6000,
				fixed: true
			},
			style: {
				classes: 'qtip-dark qtip-shadow qtip-rounded qtip-wcfm-css qtip-wcfm-core-css'
			}
		});
	});
});