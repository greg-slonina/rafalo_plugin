<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

$content_css    = 'width: 100%; -webkit-box-shadow: 0 1px 12px rgba(0, 0, 0, 0.1); -moz-box-shadow: 0 1px 12px rgba(0, 0, 0, 0.1); box-shadow: 0 1px 12px rgba(0, 0, 0, 0.1);';
$mailbody_css   = 'padding: 20px; font-size:14px; color: #656565; line-height: 25px; border-width: 0px;';
$user_info_css  = 'font-size:11px; font-style: italic;';

?>

<table width="100%" bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td>
			<!--[if (gte mso 9)|(IE)]>
			<table width="600" align="center" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td><![endif]-->
			<table style="<?php echo $content_css; ?>" align="center" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td bgcolor="#ffffff" style="<?php echo $mailbody_css; ?>">
						<?php echo wpautop( $mail_body); ?>
						<br />
						<b>
							<?php _e( 'Name', 'wcfm-live-chat' ) ?>:
						</b>
						<?php echo $name; ?>
						<br />
						<?php if( apply_filters( 'wcfm_allow_view_chat_user_info', true ) ) { ?>
							<b>
								<?php _e( 'E-mail', 'wcfm-live-chat' ) ?>:
							</b>
							<a href="mailto:<?php echo $email; ?>">
								<?php echo $email; ?>
							</a>
							<br />
						<?php } ?>
						<b>
							<?php _e( 'Message', 'wcfm-live-chat' ) ?>:
						</b>
						<br />
						<?php echo str_replace( "\n", '<br />', htmlspecialchars( stripslashes( $message ) ) ); ?>
						<br />
						<br />
						<?php if( apply_filters( 'wcfm_allow_view_chat_user_info', true ) ) { ?>
							<span style="<?php echo $user_info_css ?>">
									<?php _e( 'User information', 'wcfm-live-chat' ) ?>: <?php echo $ip_address . ' - ' . $os . ', ' . $browser . ' ' . $version ?>
								<br />
								<?php echo $page ?>
							</span>
						<?php } ?>
					</td>
				</tr>
			</table>
			<!--[if (gte mso 9)|(IE)]></td></tr></table><![endif]-->
		</td>
	</tr>
</table>