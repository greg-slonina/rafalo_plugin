jQuery(document).ready( function($) {
	// Delete Followings
	$('.wcfm_followings_delete').each(function() {
		$(this).click(function(event) {
			event.preventDefault();
			var rconfirm = confirm(wcfm_dashboard_messages.following_delete_confirm);
			if(rconfirm) deleteWCFMFollowing($(this));
			return false;
		});
	});
	
	function deleteWCFMFollowing(item) {
		jQuery('.woocommerce-MyAccount-followings').block({
			message: null,
			overlayCSS: {
				background: '#fff',
				opacity: 0.6
			}
		});
		var data = {
			action      : 'delete_wcfm_followings',
			userid 			: item.data('userid'),
			followersid : item.data('followersid')
		}	
		jQuery.ajax({
			type:		'POST',
			url: wcfm_params.ajax_url,
			data: data,
			success:	function(response) {
				window.location = window.location.href;
				jQuery('.woocommerce-MyAccount-followings').unblock();
			}
		});
	}
});