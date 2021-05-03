jQuery( document ).ready( function( $ ) {
	if( $('#wcfm_vacation_mode_type').length > 0 ) {
		$('#wcfm_vacation_mode_type').change(function() {
			$('.date_wise_vacation_ele').addClass('wcfm_ele_hide');
			if( $(this).val() == 'date_wise' ) {
				$('.date_wise_vacation_ele').removeClass('wcfm_ele_hide');
			}
		}).change();
		$( "#wcfm_vacation_start_date" ).datepicker({
			changeMonth: true,
			changeYear: true,
			dateFormat: 'yy-mm-dd',
			onClose: function( selectedDate ) {
				$( "#wcfm_vacation_end_date" ).datepicker( "option", "minDate", selectedDate );
			}
		});
		$( "#wcfm_vacation_end_date" ).datepicker({
			changeMonth: true,
			changeYear: true,
			dateFormat: 'yy-mm-dd',
			onClose: function( selectedDate ) {
				$( "#wcfm_vacation_start_date" ).datepicker( "option", "maxDate", selectedDate );
			}
		});
	}
});