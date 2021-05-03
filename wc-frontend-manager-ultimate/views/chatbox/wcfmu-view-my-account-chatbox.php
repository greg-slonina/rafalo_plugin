<?php
/**
 * WCFMu plugin view
 *
 * WCFM Chatbox view
 *
 * @author 		WC Lovers
 * @package 	wcfm/views/support
 * @version   5.1.5
 */
 
global $WCFM, $WCFMu, $wpdb;

if( !apply_filters( 'wcfm_is_pref_chatbox', true ) || !apply_filters( 'wcfm_is_allow_chatbox', true ) ) {
	wcfm_restriction_message_show( "Chatbox" );
	return;
}

if( is_user_logged_in() ) {
	
	?>
	<div class="wcfm-container">
		<div id="wcfm_chatbox_listing_expander" class="wcfm-content">
			<div id="wcfm-chatbox" style=""></div>
			<script>
				Talk.ready.then( function() {
					var inbox = window.talkSession.createInbox();
					inbox.mount( document.getElementById( 'wcfm-chatbox' ) );
				} );
			</script>
			<div class="wcfm-clearfix"></div>
		</div>
	</div>
	<?php
}