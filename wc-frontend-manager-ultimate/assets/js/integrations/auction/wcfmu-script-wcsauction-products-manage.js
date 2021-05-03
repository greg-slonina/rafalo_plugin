jQuery(document).ready(function($) {

    var startDateTextBox = $('#_auction_dates_from');
    var endDateTextBox = $('#_auction_dates_to');
    
    $.timepicker.datetimeRange(
			startDateTextBox,
			endDateTextBox,
			{
				minInterval: (1000*60), // 1min
				dateFormat: 'yy-mm-dd',
				timeFormat: 'HH:mm',
				start: {}, // start picker options
				end: {} // end picker options
			}
    );
    
    /*startDateTextBox.datetimepicker({ 
			minInterval: (1000*60), // 1min
			dateFormat: 'yy-mm-dd',
			timeFormat: 'HH:mm',
			onClose: function(dateText, inst) {
				if (endDateTextBox.val() != '') {
					var testStartDate = startDateTextBox.datetimepicker('getDate');
					var testEndDate = endDateTextBox.datetimepicker('getDate');
					if (testStartDate > testEndDate)
						endDateTextBox.datetimepicker('setDate', testStartDate);
				}
				else {
					endDateTextBox.val(dateText);
				}
			},
			onSelect: function (selectedDateTime) {
				var date = new Date();
				date.setDate(startDateTextBox.datetimepicker('getDate'));
				console.log(startDateTextBox.datetimepicker('getDate').date +"::"+date);
				endDateTextBox.datetimepicker('option', 'minDate', startDateTextBox.datetimepicker('getDate') );
				endDateTextBox.datetimepicker('option', 'maxDate', startDateTextBox.datetimepicker('getDate') );
			}
		});
		endDateTextBox.datetimepicker({ 
			minInterval: (1000*60), // 1min
			dateFormat: 'yy-mm-dd',
			timeFormat: 'HH:mm',
			onClose: function(dateText, inst) {
				if (startDateTextBox.val() != '') {
					var testStartDate = startDateTextBox.datetimepicker('getDate');
					var testEndDate = endDateTextBox.datetimepicker('getDate');
					if (testStartDate > testEndDate)
						startDateTextBox.datetimepicker('setDate', testEndDate);
				}
				else {
					startDateTextBox.val(dateText);
				}
			},
			onSelect: function (selectedDateTime){
				startDateTextBox.datetimepicker('option', 'maxDate', endDateTextBox.datetimepicker('getDate') );
			}
		});*/
    
    if( $('#_relist_auction_dates_from').length > 0 ) {
			var startRelistDateTextBox = $('#_relist_auction_dates_from');
			var endRelistDateTextBox = $('#_relist_auction_dates_to');
	
			$.timepicker.datetimeRange(
				startRelistDateTextBox,
				endRelistDateTextBox,
				{
					minInterval: (1000*60), // 1min
					dateFormat: 'yy-mm-dd',
					timeFormat: 'HH:mm',
					start: {}, // start picker options
					end: {} // end picker options
				}
			);
		}

});