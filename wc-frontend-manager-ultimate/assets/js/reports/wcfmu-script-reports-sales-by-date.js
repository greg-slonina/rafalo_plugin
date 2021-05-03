jQuery( document ).ready( function($) {
	$( document.body ).on( 'wcfm-date-range-refreshed', function() {
		$('input[name="start_date"]').val($filter_date_form);
		$('input[name="end_date"]').val($filter_date_to);
		$('input[name="end_date"]').parent().submit();
	});
} );