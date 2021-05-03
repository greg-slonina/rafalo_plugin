jQuery(document).ready(function($) {

    var startDateTextBox = $('#_lottery_dates_from');
    var endDateTextBox = $('#_lottery_dates_to');

    $.timepicker.datetimeRange(
        startDateTextBox,
        endDateTextBox,
        {
            minInterval: (1000*60), // 1min
            dateFormat: 'yy-mm-dd',
            timeFormat: 'HH:mm:ss',
            start: {}, // start picker options
            end: {} // end picker options
        }
    );

});