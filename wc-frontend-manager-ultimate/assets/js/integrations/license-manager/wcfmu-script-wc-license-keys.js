$wcfm_license_keys_table = '';
$license_keys_vendor = '';

jQuery(document).ready(function($) {
	
	$wcfm_license_keys_table = $('#wcfm-license-keys').DataTable( {
		"processing": true,
		"serverSide": true,
		"pageLength": parseInt(dataTables_config.pageLength),
		"bFilter"   : false,
		"dom"       : 'Bfrtip',
		"responsive": true,
		"language"  : $.parseJSON(dataTables_language),
		"buttons"   : $wcfm_datatable_button_args,
		"columns"   : [
										{ responsivePriority: 1 },
										{ responsivePriority: 1 },
										{ responsivePriority: 2 },
										{ responsivePriority: 2 },
										{ responsivePriority: 4 },
										{ responsivePriority: 3 },
										{ responsivePriority: 5 },
										{ responsivePriority: 6 },
										{ responsivePriority: 7 },
										{ responsivePriority: 1 },
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
										{ "targets": 9, "orderable" : false },
									],
		'ajax': {
			"type"   : "POST",
			"url"    : wcfm_params.ajax_url,
			"data"   : function( d ) {
				d.action       = 'wcfm_ajax_controller',
				d.controller   = 'wcfm-license-keys',
				d.license_vendor = $license_keys_vendor,
				d.license_status = GetURLParameter( 'license_status' )
			},
			"complete" : function () {
				initiateTip();
				
				// Fire wcfm-license_keys table refresh complete
				$( document.body ).trigger( 'updated_wcfm_license_keys' );
			}
		}
	} );
	
	if( $('#dropdown_vendor').length > 0 ) {
		$('#dropdown_vendor').on('change', function() {
			$license_keys_vendor = $('#dropdown_vendor').val();
			$wcfm_license_keys_table.ajax.reload();
		}).select2( $wcfm_vendor_select_args );
	}
	
	// License Key Manage
	$( document.body ).on( 'updated_wcfm_license_keys', function() {
		$('.wcfm_license_key_manage').each(function() {
			$(this).click(function(event) {
				event.preventDefault();
				manageLicenseKeys( $(this) );
				return false;
			});
		});
	});
	
	function manageLicenseKeys( item ) {
		
		var data = {
							  action        : 'wcfmu_license_key_manage_html',
							  licenseid   : item.data('licenseid'),
							}
		jQuery.ajax({
			type    :		'POST',
			url     : wcfm_params.ajax_url,
			data    : data,
			success :	function(response) {
														 
				// Intialize colorbox
				$.colorbox( { html: response, height: 600, width: $popup_width,
					onComplete:function() {
						$('#wcfm_license_keys_submit_button').click(function(e) {
							e.preventDefault();
							
							$('#wcfm_license_keys_form').block({
									message: null,
									overlayCSS: {
										background: '#fff',
										opacity: 0.6
									}
								});
							
							jQuery( document.body ).trigger( 'wcfm_form_validate', jQuery('#wcfm_license_keys_form') );
							if( !$wcfm_is_valid_form ) {
								wcfm_notification_sound.play();
								jQuery('#wcfm_license_keys_form').unblock();
							} else {
					
								$('#wcfm_license_keys_submit_button').hide();
								$('#wcfm_license_keys_form .wcfm-message').html('').removeClass('wcfm-error').removeClass('wcfm-success').slideUp();
								
								var data = {
									action : 'wcfm_ajax_controller',
									controller : 'wcfm-license-keys-manage', 
									wcfm_license_key_form : $('#wcfm_license_keys_form').serialize()
								}	
								$.post(wcfm_params.ajax_url, data, function(response) {
									if(response) {
										$response_json = $.parseJSON(response);
										wcfm_notification_sound.play();
										$('#wcfm_license_keys_form').unblock();
										if( $response_json.status ) {
											$wcfm_license_keys_table.ajax.reload();
											$('#wcfm_license_keys_form .wcfm-message').html( '<span class="wcicon-status-completed"></span>' + $response_json.message ).addClass('wcfm-success').slideDown();
											setTimeout(function() {
												$.colorbox.remove();
											}, 2000);
										} else {
											$('#wcfm_license_keys_submit_button').show();
											$('#wcfm_license_keys_form .wcfm-message').html( '<span class="wcicon-status-cancelled"></span>' + $response_json.message ).addClass('wcfm-error').slideDown();
										}
									}
								});
							}
						});
					}
				});
			}
		});
	}
	
	// License Key Delete
	$( document.body ).on( 'updated_wcfm_license_keys', function() {
		$('.wcfm_license_key_delete').each(function() {
			$(this).click(function(event) {
				event.preventDefault();
				deleteLicenseKeys( $(this) );
				return false;
			});
		});
	});
	
	function deleteLicenseKeys( item ) {
		
		var data = {
								action        : 'wcfm_ajax_controller',
								controller    : 'wcfm-license-keys-delete', 
								licenseid   : item.data('licenseid'),
							}	
		$.post(wcfm_params.ajax_url, data, function(response) {
			if(response) {
				$wcfm_license_keys_table.ajax.reload();
			}
		});
	}
	
	// Dashboard FIlter
	if( $('.wcfm_filters_wrap').length > 0 ) {
		$('.dataTable').before( $('.wcfm_filters_wrap') );
		$('.wcfm_filters_wrap').css( 'display', 'inline-block' );
	}
	
	// Screen Manager
	$( document.body ).on( 'updated_wcfm_license_keys', function() {
		$.each(wcfm_license_keys_screen_manage, function( column, column_val ) {
		  $wcfm_license_keys_table.column(column).visible( false );
		} );
	});
} );