$wcfm_auctions_table = '';

jQuery(document).ready(function($) {
	
	$wcfm_auctions_table = $('#wcfm-auctions').DataTable( {
		"processing": true,
		"serverSide": true,
		"pageLength": parseInt(dataTables_config.pageLength),
		"responsive": true,
		"language"  : $.parseJSON(dataTables_language),
		"columns"   : [
										{ responsivePriority: 1 },
										{ responsivePriority: 2 },
										{ responsivePriority: 1 },
										{ responsivePriority: 3 },
										{ responsivePriority: 4 },
								],
		"columnDefs": [ { "targets": 0, "orderable" : false }, 
									  { "targets": 1, "orderable" : false }, 
										{ "targets": 2, "orderable" : false },
										{ "targets": 3, "orderable" : false },
										{ "targets": 2, "orderable" : false },
									],
		'ajax': {
			"type"   : "POST",
			"url"    : wcfm_params.ajax_url,
			"data"   : function( d ) {
				d.action       = 'wcfm_ajax_controller',
				d.controller   = 'wcfm-auctions'
			},
			"complete" : function () {
				initiateTip();
				
				// Fire wcfm-auctions table refresh complete
				$( document.body ).trigger( 'updated_wcfm-auctions' );
			}
		}
	} );
	
	// Delete Bid
	$( document.body ).on( 'updated_wcfm-auctions', function() {
		$('.wcfm_auction_bid_delete').each(function() {
			$(this).click(function(event) {
				event.preventDefault();
				var rconfirm = confirm(wcfm_dashboard_messages.auction_bid_delete_confirm);
				if(rconfirm) deleteWCFMAuctionBid($(this));
				return false;
			});
		});
	});
	
	function deleteWCFMAuctionBid(item) {
		jQuery('#wwcfm_auctions_expander').block({
			message: null,
			overlayCSS: {
				background: '#fff',
				opacity: 0.6
			}
		});
		var data = {
			action      : 'delete_wcfm_auction_bid',
			logid       : item.data('bidid'),
			postid      : item.data('postid'),
			plugin 			: item.data('plugin')
		}	
		jQuery.ajax({
			type:		'POST',
			url: wcfm_params.ajax_url,
			data: data,
			success:	function(response) {
				if($wcfm_auctions_table) $wcfm_auctions_table.ajax.reload();
				jQuery('#wwcfm_auctions_expander').unblock();
			}
		});
	}
} );