$wcfm_appointments_table = '';
$appointment_status = '';	
$appointment_filter = '';	
$appointment_staff_filter = '';

jQuery(document).ready(function($) {
	
	$wcfm_appointments_table = $('#wcfm-appointments').DataTable( {
		"processing": true,
		"serverSide": true,
		"pageLength": parseInt(dataTables_config.pageLength),
		"bFilter"   : false,
		"responsive": true,
		"language"  : $.parseJSON(dataTables_language),
		"columns"   : [
										{ responsivePriority: 2 },
										{ responsivePriority: 1 },
										{ responsivePriority: 3 },
										{ responsivePriority: 5 },
										{ responsivePriority: 4 },
										{ responsivePriority: 3 },
										{ responsivePriority: 3 },
										{ responsivePriority: 6 },
										{ responsivePriority: 1 }
								],
		"columnDefs": [ { "targets": 0, "orderable" : false }, 
									  { "targets": 1, "orderable" : false }, 
										{ "targets": 2, "orderable" : false }, 
										{ "targets": 3, "orderable" : false }, 
										{ "targets": 4, "orderable" : false }, 
										{ "targets": 5, "orderable" : false },
										{ "targets": 6, "orderable" : false },
										{ "targets": 7, "orderable" : false },
										{ "targets": 8, "orderable" : false },
									],
		'ajax': {
			"type"   : "POST",
			"url"    : wcfm_params.ajax_url,
			"data"   : function( d ) {
				d.action                   = 'wcfm_ajax_controller',
				d.controller               = 'wcfm-appointments',
				d.appointment_status       = GetURLParameter( 'appointment_status' ),
				d.appointment_filter       = $appointment_filter,
				d.appointment_staff_filter = $appointment_staff_filter,
				d.filter_date_form         = $filter_date_form,
				d.filter_date_to           = $filter_date_to
			},
			"complete" : function () {
				initiateTip();
				
				// Fire wcfm-appointments table refresh complete
				$( document.body ).trigger( 'updated_wcfm-appointments' );
			}
		}
	} );
	
	if( $('#dropdown_appointment_filter').length > 0 ) {
		$('#dropdown_appointment_filter').on('change', function() {
		  $appointment_filter = $('#dropdown_appointment_filter').val();
		  $wcfm_appointments_table.ajax.reload();
		});
	}
	
	if( $('#dropdown_appointment_staff_filter').length > 0 ) {
		$('#dropdown_appointment_staff_filter').on('change', function() {
		  $appointment_staff_filter = $('#dropdown_appointment_staff_filter').val();
		  $wcfm_appointments_table.ajax.reload();
		});
	}
	
	$( document.body ).on( 'wcfm-date-range-refreshed', function() {
		$wcfm_appointments_table.ajax.reload();
	});
	
	// Mark Appointment as Confirmed
	$( document.body ).on( 'updated_wcfm-appointments', function() {
		$('.wcfm_appointment_mark_confirm').each(function() {
			$(this).click(function(event) {
				event.preventDefault();
				var rconfirm = confirm(wcfm_dashboard_messages.appointment_mark_complete_confirm);
				if(rconfirm) markCompleteWCFMAppointment($(this));
				return false;
			});
		});
	});
	
	function markCompleteWCFMAppointment(item) {
		$('#wcfm-appointments_wrapper').block({
			message: null,
			overlayCSS: {
				background: '#fff',
				opacity: 0.6
			}
		});
		var data = {
			action : 'wcfm_appointment_mark_confirm',
			appointmentid : item.data('appointmentid')
		}	
		$.ajax({
			type:		'POST',
			url: wcfm_params.ajax_url,
			data: data,
			success:	function(response) {
				$wcfm_appointments_table.ajax.reload();
				$('#wcfm-appointments_wrapper').unblock();
			}
		});
	}
	
	// Dashboard FIlter
	if( $('.wcfm_filters_wrap').length > 0 ) {
		$('.dataTable').before( $('.wcfm_filters_wrap') );
		$('.wcfm_filters_wrap').css( 'display', 'inline-block' );
	}
	
	// Screen Manager
	$( document.body ).on( 'updated_wcfm-appointments', function() {
		$.each(wcfm_appointments_screen_manage, function( column, column_val ) {
		  $wcfm_appointments_table.column(column).visible( false );
		} );
	});
} );