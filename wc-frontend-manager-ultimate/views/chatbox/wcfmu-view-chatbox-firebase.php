<?php
/**
 * WCFMu plugin view
 *
 * WCFM Firebase Chatbox view
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
			<h2><?php _e( 'Chat Box', 'wc-frontend-manager-ultimate' ); ?></h2>
			
			<?php
			echo '<a class="add_new_wcfm_ele_dashboard text_tip" href="'.wcfm_chats_offline_url().'" data-tip="'. __('Chat Offline Messages', 'wc-frontend-manager-ultimate') .'"><span class="wcfmfa fa-user-clock"></span><span class="text">' . __('Offline Messages', 'wc-frontend-manager-ultimate' ) . '</span></a>';
			echo '<a class="add_new_wcfm_ele_dashboard text_tip" href="'.wcfm_chats_history_url().'" data-tip="'. __('Chat History', 'wc-frontend-manager-ultimate') .'"><span class="wcfmfa fa-history"></span><span class="text">' . __('Chat History', 'wc-frontend-manager-ultimate' ) . '</span></a>';
			?>
			
			<div class="wcfm-clearfix"></div>
		</div>
	  <div class="wcfm-clearfix"></div><br />
		
	  <?php do_action( 'before_wcfm_chatbox' ); ?>
	  
		<div class="wcfm-container">
			<div id="wcfm_chatbox_listing_expander" class="wcfm-content">
			  
			  <div class="wcfm-live-chat-console-container">
					<div id="FBC_console" class="wcfm-live-chat-console">
							<div id="FBC_sidebar_left" class="console-sidebar-left">
									<div class="sidebar-header">
							<?php _e( 'Users', 'wc-frontend-manager-ultimate' ); ?>
											<a href="" id="FBC_connect" class="connect button button-disabled">
								<?php _e( 'Please wait', 'wc-frontend-manager-ultimate' ); ?>
											</a>
									</div>
									<div id="FBC_users" class="sidebar-users">
											<div id="FBC_queue" class="sidebar-queue"></div>
											<div id="FBC_notify" class="sidebar-notify">
								<?php _e( 'Please wait', 'wc-frontend-manager-ultimate' ); ?>...
											</div>
									</div>
							</div>
							<div class="console-footer">
							</div>
							<div id="FBC_popup_cnv" class="chat-content chat-welcome">
									<div id="FBC_cnv" class="chat-wrapper">
											<div id="FBC_load_msg" class="chat-load-msg">
								<?php _e( 'Please wait', 'wc-frontend-manager-ultimate' ) ?>
											</div>
									</div>
									<div id="FBC_cnv_bottom" class="chat-bottom">
											<div class="chat-notify">
													<div id="FBC_popup_ntf"></div>
											</div>
											<div class="chat-cnv-reply">
													<div class="user-avatar">
															<img src="" />
													</div>
													<div class="chat-cnv-input">
															<textarea name="msg" class="chat-reply-input" id="FBC_cnv_reply" placeholder="<?php _e( 'Type here and hit enter to chat', 'wc-frontend-manager-ultimate' ) ?>"></textarea>
													</div>
											</div>
									</div>
							</div>
							<div id="FBC_sidebar_right" class="console-sidebar-right">
									<div class="sidebar-header">
							
											<button id="YLC_save" data-cnv-id="0" class="button">
													<i class="wcfmfa fa-floppy-o"></i>
													<?php _e( 'Save chat', 'wc-frontend-manager-ultimate' ); ?>
											</button>
					
											<button id="FBC_end_chat" data-cnv-id="0" class="button">
													<i class="wcfmfa fa-times"></i>
													<?php _e( 'End chat', 'wc-frontend-manager-ultimate' ) ?>
											</button>
											<input type="hidden" id="FBC_active_cnv" />
											<br />
											<span id="FBC_save_ntf"></span>
									</div>
									<div class="sidebar-info info-name">
											<strong><?php _e( 'User Name', 'wc-frontend-manager-ultimate' ) ?></strong>
											<span></span>
									</div>
									
									<?php if( apply_filters( 'wcfm_allow_view_chat_user_info', true ) ) { ?>
										<div class="sidebar-info info-ip">
												<strong><?php _e( 'IP Address', 'wc-frontend-manager-ultimate' ) ?></strong>
												<span></span>
										</div>
										<?php if( apply_filters( 'wcfm_allow_view_customer_email', true ) ) { ?>
											<div class="sidebar-info info-email">
													<strong><?php _e( 'User Email', 'wc-frontend-manager-ultimate' ) ?></strong>
													<a href="">
													</a>
											</div>
										<?php } ?>
									<?php } ?>
									<div class="sidebar-info info-page">
											<strong><?php _e( 'Current Page', 'wc-frontend-manager-ultimate' ) ?></strong>
											<a id="FBC_active_page" href="" target="_blank">
											</a>
									</div>
									
									<div class="sidebar-info timer">
										<strong>
												<?php _e( 'Elapsed time', 'wc-frontend-manager-ultimate' ) ?>
										</strong>
										<span id="YLC_timer">
										</span>
								</div>
								<div class="sidebar-info macro">
										<select class="macro-select" style="width:100%;">
												<option value=""></option>
												<?php echo apply_filters( 'fbc_macro_options', '' ) ?>
										</select>
								</div>
			
							</div>
							<div id="FBC_firebase_offline" class="firebase-offline">
									<div><?php _e( 'Firebase offline or not available. Please wait...', 'wc-frontend-manager-ultimate' ); ?></div>
							</div>
					</div>
					<div class="wcfm-clearfix"></div>
			  </div>

				<div class="wcfm-clearfix"></div>
			</div>
		</div>
			
		<?php do_action( 'after_wcfm_chatbox' ); ?>
	</div>
</div>
