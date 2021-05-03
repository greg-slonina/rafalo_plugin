<?php
/**
 * WCFMu plugin view
 *
 * WCFM TalkJS Chatbox view
 *
 * @author 		WC Lovers
 * @package 	wcfm/views/chatbox
 * @version   4.0.3
 */
 
global $WCFM, $WCFMu;


if( !apply_filters( 'wcfm_is_pref_chatbox', true ) || !apply_filters( 'wcfm_is_allow_chatbox', true ) ) {
	wcfm_restriction_message_show( "Chatboxs" );
	return;
}

?>

<div class="collapse wcfm-collapse" id="wcfm_chatbox_listing">
	
	<div class="wcfm-page-headig">
		<span class="wcfmfa fa-comments"></span>
		<span class="wcfm-page-heading-text"><?php _e( 'Chat Box', 'wc-frontend-manager-ultimate' ); ?></span>
		<?php do_action( 'wcfm_page_heading' ); ?>
	</div>
	<div class="wcfm-collapse-content">
	
		<div class="wcfm-container wcfm-top-element-container">
			<h2><?php echo __( 'Chat Box', 'wc-frontend-manager-ultimate' ); ?></h2>
			<div class="wcfm-clearfix"></div>
		</div>
	  <div class="wcfm-clearfix"></div><br />
		
	  <?php do_action( 'before_wcfm_chatbox' ); ?>
	  
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
			
		<?php do_action( 'after_wcfm_chatbox' ); ?>
	</div>
</div>
