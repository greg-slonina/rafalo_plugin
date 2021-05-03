$wcfm_followers_table = '';
$followers_vendor = '';

jQuery(document).ready(function($) {
	
	$wcfm_followers_table = $('#wcfm-followers').DataTable( {
		"processing": true,
		"serverSide": true,
		"pageLength": parseInt(dataTables_config.pageLength),
		"bFilter"   : false,
		"responsive": true,
		"language"  : $.parseJSON(dataTables_language),
		"columns"   : [
										{ responsivePriority: 1 },
										{ responsivePriority: 3 },
										{ responsivePriority: 2 },
										{ responsivePriority: 1 },
								],
		"columnDefs": [ { "targets": 0, "orderable" : false }, 
										{ "targets": 1, "orderable" : false },
										{ "targets": 2, "orderable" : false },
										{ "targets": 3, "orderable" : false },
									],
		'ajax': {
			"type"   : "POST",
			"url"    : wcfm_params.ajax_url,
			"data"   : function( d ) {
				d.action           = 'wcfm_ajax_controller',
				d.controller       = 'wcfm-followers',
				d.followers_vendor = $followers_vendor
			},
			"complete" : function () {
				initiateTip();
				
				// Fire wcfm-groups table refresh complete
				$( document.body ).trigger( 'updated_wcfm-followers' );
			}
		}
	} );
	
	// Delete followers
	$( document.body ).on( 'updated_wcfm-followers', function() {
		$('.wcfm_followers_delete').each(function() {
			$(this).click(function(event) {
				event.preventDefault();
				var rconfirm = confirm(wcfm_dashboard_messages.follower_delete_confirm);
				if(rconfirm) deleteWCFMFollowers($(this));
				return false;
			});
		});
	});
	
	function deleteWCFMFollowers(item) {
		jQuery('#wcfm_followers_listing_expander').block({
			message: null,
			overlayCSS: {
				background: '#fff',
				opacity: 0.6
			}
		});
		var data = {
			action      : 'delete_wcfm_followers',
			lineid      : item.data('lineid'),
			userid 			: item.data('userid'),
			followersid : item.data('followersid')
		}	
		jQuery.ajax({
			type:		'POST',
			url: wcfm_params.ajax_url,
			data: data,
			success:	function(response) {
				if($wcfm_followers_table) $wcfm_followers_table.ajax.reload();
				jQuery('#wcfm_followers_listing_expander').unblock();
			}
		});
	}
	
	if( $('#dropdown_vendor').length > 0 ) {
		$('#dropdown_vendor').on('change', function() {
			$followers_vendor = $('#dropdown_vendor').val();
			$wcfm_followers_table.ajax.reload();
		}).select2( $wcfm_vendor_select_args );
	}
	
	// Screen Manager
	$( document.body ).on( 'updated_wcfm-followers', function() {
		$.each(wcfm_followers_screen_manage, function( column, column_val ) {
		  $wcfm_followers_table.column(column).visible( false );
		} );
	});
	
	// Dashboard FIlter
	if( $('.wcfm_filters_wrap').length > 0 ) {
		$('.dataTable').before( $('.wcfm_filters_wrap') );
		$('.wcfm_filters_wrap').css( 'display', 'inline-block' );
	}
	
} );