<?php

$username = $usermail = '';

if ( apply_filters( 'wcfm_fbc_prefill_login_logged_user', true ) && is_user_logged_in() ) {
	$user     = wp_get_current_user();
	$username = $user->display_name;
	$usermail = $user->user_email;
}

?>
<div class="wcfm_fbc_chatwindow wcfm_custom_hide">
	<div id="FBC">
	
			<div id="FBC_chat_btn" class="chat-chat-btn btn-<?php echo $args['button_type']; ?>">
					<div class="chat-ico chat wcfmfa fa-comments"></div>
					<div class="chat-ico fbc-toggle wcfmfa fa-angle-<?php echo( $args['button_pos'] == 'bottom' ? 'up' : 'down' ) ?>"></div>
					<div class="chat-title">
						<?php _e( 'Chat Now', 'wc-frontend-manager-ultimate' ); ?>
					</div>
			</div>
	
			<div id="FBC_chat" class="chat-widget">
	
					<div id="FBC_chat_header" class="chat-header">
							<div class="chat-ico chat wcfmfa fa-comments"></div>
							<div class="chat-ico fbc-toggle wcfmfa fa-angle-<?php echo( $args['button_pos'] == 'bottom' ? 'down' : 'up' ) ?>"></div>
							<div class="chat-title">
								<?php _e( 'Chat Now', 'wc-frontend-manager-ultimate' ); ?>
							</div>
							<div class="chat-clear"></div>
					</div>
	
					<div id="FBC_chat_body" class="chat-body chat-online" style="<?php echo $args['chat_width'] ?>">
							<div class="chat-cnv" id="FBC_cnv">
									<div class="chat-welc">
										<?php _e( 'Questions, doubts, issues? We\'re here to help you!', 'wc-frontend-manager-ultimate' ); ?>
									</div>
							</div>
							<div class="chat-tools">
									<a id="FBC_tool_end_chat" href="javascript:void(0)">
											<i class="wcfmfa fa-times"></i>
											<?php _e( 'End chat', 'wc-frontend-manager-ultimate' ) ?>
									</a>
									<div id="FBC_popup_ntf" class="chat-ntf"></div>
							</div>
							<div class="chat-cnv-reply">
									<div class="chat-cnv-input">
											<textarea id="FBC_cnv_reply" name="msg" class="chat-reply-input" placeholder="<?php _e( 'Type here and hit enter to chat', 'wc-frontend-manager-ultimate' ) ?>"></textarea>
									</div>
							</div>
					</div>
	
					<div id="FBC_connecting" class="chat-body chat-form" style="<?php echo $args['form_width'] ?>">
							<div class="chat-sending chat-conn">
					<?php _e( 'Connecting', 'wc-frontend-manager-ultimate' ) ?>...
							</div>
					</div>
	
					<div id="FBC_offline" class="chat-body chat-form" style="<?php echo $args['form_width'] ?>">
							<div class="chat-lead op-offline">
								<?php _e( 'None of our operators are available at the moment. Please, try again later.', 'wc-frontend-manager-ultimate' ); ?>
							</div>
							<div class="chat-lead op-busy">
								<?php _e( 'Our operators are busy. Please try again later', 'wc-frontend-manager-ultimate' ); ?>
							</div>
							
							<form id="FBC_popup_form" action="">
								<label for="FBC_msg_name">
								<?php _e( 'Your Name', 'wc-frontend-manager-ultimate' ) ?>
								</label>:
								<div class="form-line">
										<input type="text" name="name" id="FBC_msg_name" placeholder="<?php _e( 'Please enter your name', 'wc-frontend-manager-ultimate' ) ?>" value="<?php echo $username ?>">
										<i class="chat-ico wcfmfa fa-user"></i>
								</div>
								<label for="FBC_msg_email">
								<?php _e( 'Your Email', 'wc-frontend-manager-ultimate' ) ?>
								</label>:
								<div class="form-line">
										<input type="email" name="email" id="FBC_msg_email" placeholder="<?php _e( 'Please enter your email', 'wc-frontend-manager-ultimate' ) ?>" value="<?php echo $usermail ?>">
										<i class="chat-ico wcfmfa fa-envelope-o"></i>
								</div>
								<label for="FBC_msg_message">
								<?php _e( 'Your Message', 'wc-frontend-manager-ultimate' ) ?>
								</label>:
								<div class="form-line">
										<textarea id="FBC_msg_message" name="message" placeholder="<?php _e( 'Write your question', 'wc-frontend-manager-ultimate' ) ?>" class="chat-field"></textarea>
								</div>
							
								<div class="chat-send">
										<div id="FBC_offline_ntf" class="chat-ntf"></div>
										<a href="javascript:void(0)" id="FBC_send_btn" class="chat-form-btn">
											<?php _e( 'Send', 'wc-frontend-manager-ultimate' ) ?>
										</a>
								</div>
						</form>
					</div>
	
					<div id="FBC_login" class="chat-body chat-form" style="<?php echo $args['form_width'] ?>">
							<div class="chat-lead">
								<?php _e( 'Have you got question? Write to us!', 'wc-frontend-manager-ultimate' ) ?>
							</div>
							<form id="FBC_login_form" action="">
									<label for="FBC_field_name">
										<?php _e( 'Your Name', 'wc-frontend-manager-ultimate' ) ?>
									</label>:
									<div class="form-line">
											<input type="text" name="user_name" id="FBC_field_name" placeholder="<?php _e( 'Please enter your name', 'wc-frontend-manager-ultimate' ) ?>" value="<?php echo $username ?>">
											<i class="chat-ico wcfmfa fa-user"></i>
									</div>
									<label for="FBC_field_email">
										<?php _e( 'Your Email', 'wc-frontend-manager-ultimate' ) ?>
									</label>:
									<div class="form-line">
											<input type="email" name="user_email" id="FBC_field_email" placeholder="<?php _e( 'Please enter your email', 'wc-frontend-manager-ultimate' ) ?>" value="<?php echo $usermail ?>">
											<i class="chat-ico wcfmfa fa-envelope-o"></i>
									</div>
					
									<div class="chat-send">
											<div id="FBC_login_ntf" class="chat-ntf"></div>
											<a href="javascript:void(0)" id="FBC_login_btn" class="chat-form-btn">
												<?php _e( 'Start Chat', 'wc-frontend-manager-ultimate' ) ?>
											</a>
									</div>
							</form>
					</div>
	
					<div id="FBC_end_chat" class="chat-body chat-form" style="<?php echo $args['form_width'] ?>">
							<div class="chat-lead">
								<?php _e( 'This chat session has ended', 'wc-frontend-manager-ultimate' ) ?>
							</div>
					
							<div class="chat-evaluation">
								<?php _e( 'Was this conversation useful? Vote this chat session.', 'wc-frontend-manager-ultimate' ); ?>
				
								<div id="FBC_end_chat_ntf" class="chat-ntf"></div>
								<a href="javascript:void(0)" id="FBC_good_btn" class="good">
										<i class="wcfmfa fa-thumbs-up"></i>
										<?php _e( 'Good', 'wc-frontend-manager-ultimate' ) ?>
								</a>
								<a href="javascript:void(0)" id="FBC_bad_btn" class="bad">
										<i class="wcfmfa fa-thumbs-down"></i>
										<?php _e( 'Bad', 'wc-frontend-manager-ultimate' ) ?>
								</a>
				
								<?php if ( apply_filters( 'wcfm_is_allow_chat_message_as_mail', true ) ): ?>
				
										<div class="chat-checkbox">
												<input type="checkbox" name="request_chat" id="FBC_request_chat">
												<label for="FBC_request_chat">
														<?php _e( 'Receive the copy of the chat via e-mail', 'wc-frontend-manager-ultimate' ) ?>
												</label>
										</div>
				
								<?php endif; ?>
				
						</div>
					</div>
	
			</div>
	
	</div>
</div>