$wcfm_license_generators_table = '';
$license_generators_vendor = '';

jQuery(document).ready(function($) {
	
	$wcfm_license_generators_table = $('#wcfm-license-generators').DataTable( {
		"processing": true,
		"serverSide": true,
		"pageLength": parseInt(dataTables_config.pageLength),
		"responsive": true,
		"language"  : $.parseJSON(dataTables_language),
		"columns"   : [
										{ responsivePriority: 1 },
										{ responsivePriority: 3 },
										{ responsivePriority: 4 },
										{ responsivePriority: 2 },
										{ responsivePriority: 1 },
								],
		"columnDefs": [ { "targets": 0, "orderable" : false }, 
									  { "targets": 1, "orderable" : false }, 
										{ "targets": 2, "orderable" : false },
										{ "targets": 3, "orderable" : false },
										{ "targets": 4, "orderable" : false },
									],
		'ajax': {
			"type"   : "POST",
			"url"    : wcfm_params.ajax_url,
			"data"   : function( d ) {
				d.action         = 'wcfm_ajax_controller',
				d.controller     = 'wcfm-license-generators',
				d.license_vendor = $license_generators_vendor
			},
			"complete" : function () {
				initiateTip();
				
				// Fire wcfm-license_generators table refresh complete
				$( document.body ).trigger( 'updated_wcfm_license_generators' );
			}
		}
	} );
	
	if( $('#dropdown_vendor').length > 0 ) {
		$('#dropdown_vendor').on('change', function() {
			$license_generators_vendor = $('#dropdown_vendor').val();
			$wcfm_license_generators_table.ajax.reload();
		}).select2( $wcfm_vendor_select_args );
	}
	
	// License Generator Manage
	$( document.body ).on( 'updated_wcfm_license_generators', function() {
		$('.wcfm_license_generator_manage').each(function() {
			$(this).click(function(event) {
				event.preventDefault();
				manageLicenseGenerators( $(this) );
				return false;
			});
		});
	});
	
	function manageLicenseGenerators( item ) {
		
		var data = {
							  action        : 'wcfmu_license_generator_manage_html',
							  generatorid   : item.data('generatorid'),
							}
		jQuery.ajax({
			type    :		'POST',
			url     : wcfm_params.ajax_url,
			data    : data,
			success :	function(response) {
														 
				// Intialize colorbox
				$.colorbox( { html: response, height: 600, width: $popup_width,
					onComplete:function() {
						$('#wcfm_license_generator_submit_button').click(function(e) {
							e.preventDefault();
							
							$('#wcfm_license_generator_form').block({
									message: null,
									overlayCSS: {
										background: '#fff',
										opacity: 0.6
									}
								});
							
							jQuery( document.body ).trigger( 'wcfm_form_validate', jQuery('#wcfm_license_generator_form') );
							if( !$wcfm_is_valid_form ) {
								wcfm_notification_sound.play();
								jQuery('#wcfm_license_generator_form').unblock();
							} else {
								$('#wcfm_license_generator_submit_button').hide();
								$('#wcfm_license_generator_form .wcfm-message').html('').removeClass('wcfm-error').removeClass('wcfm-success').slideUp();
								
								var data = {
									action : 'wcfm_ajax_controller',
									controller : 'wcfm-license-generators-manage', 
									wcfm_license_generator_form : $('#wcfm_license_generator_form').serialize()
								}	
								$.post(wcfm_params.ajax_url, data, function(response) {
									if(response) {
										$response_json = $.parseJSON(response);
										wcfm_notification_sound.play();
										$('#wcfm_license_generator_form').unblock();
										if( $response_json.status ) {
											$wcfm_license_generators_table.ajax.reload();
											$('#wcfm_license_generator_form .wcfm-message').html( '<span class="wcicon-status-completed"></span>' + $response_json.message ).addClass('wcfm-success').slideDown();
											setTimeout(function() {
												$.colorbox.remove();
											}, 2000);
										} else {
											$('#wcfm_license_generator_submit_button').show();
											$('#wcfm_license_generator_form .wcfm-message').html( '<span class="wcicon-status-cancelled"></span>' + $response_json.message ).addClass('wcfm-error').slideDown();
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
	
	// License Generator Delete
	$( document.body ).on( 'updated_wcfm_license_generators', function() {
		$('.wcfm_license_generator_delete').each(function() {
			$(this).click(function(event) {
				event.preventDefault();
				deleteLicenseGenerators( $(this) );
				return false;
			});
		});
	});
	
	function deleteLicenseGenerators( item ) {
		
		var data = {
								action        : 'wcfm_ajax_controller',
								controller    : 'wcfm-license-generators-delete', 
								generatorid   : item.data('generatorid'),
							}	
		$.post(wcfm_params.ajax_url, data, function(response) {
			if(response) {
				$wcfm_license_generators_table.ajax.reload();
			}
		});
	}
								
	
	// Dashboard FIlter
	if( $('.wcfm_filters_wrap').length > 0 ) {
		$('.dataTable').before( $('.wcfm_filters_wrap') );
		$('.wcfm_filters_wrap').css( 'display', 'inline-block' );
	}
	
	// Screen Manager
	$( document.body ).on( 'updated_wcfm_license_generators', function() {
		$.each(wcfm_license_generators_screen_manage, function( column, column_val ) {
		  $wcfm_license_generators_table.column(column).visible( false );
		} );
	});
} );