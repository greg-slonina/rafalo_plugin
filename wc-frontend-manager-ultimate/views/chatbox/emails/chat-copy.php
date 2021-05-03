<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

$content_css    = 'width: 100%; -webkit-box-shadow: 0 1px 12px rgba(0, 0, 0, 0.1); -moz-box-shadow: 0 1px 12px rgba(0, 0, 0, 0.1); box-shadow: 0 1px 12px rgba(0, 0, 0, 0.1);';
$mailbody_css   = 'padding: 20px; font-size:14px; color: #656565; line-height: 25px; border-width: 0px;';
$user_info_css  = 'font-size:11px; font-style: italic;';
$mailbody_p_css      = 'line-height: normal; margin: 7px 0; background: #e5f6fd; -webkit-border-radius: 4px; -moz-border-radius: 4px; border-radius: 4px; padding: 5px 10px; ';
$mailbody_p_op_css   = 'line-height: normal; margin: 7px 0; background: #f2f2f2; -webkit-border-radius: 4px; -moz-border-radius: 4px; border-radius: 4px; padding: 5px 10px; ';
$mailbody_p_date_css = 'display:block; font-size: 11px; font-style: italic;';

$gmt_offset = get_option( 'gmt_offset' );
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
						<?php echo $mail_body; ?>
						<br />
						<br />
						<?php if ( $chat_data != array() ) : ?>
							<b>
								<?php _e( 'User Name', 'wc-frontend-manager-ultimate' ) ?>:
							</b>
							<?php echo $chat_data['user_name']; ?>
							<br />
							<?php if( apply_filters( 'wcfm_allow_view_chat_user_info', true ) ) { ?>
								<?php if ( empty( $chat_data['vendor_id'] ) || ( !empty( $chat_data['vendor_id'] ) && wcfm_vendor_has_capability( $chat_data['vendor_id'], 'view_email' ) ) ) { ?>
									<b>
										<?php _e( 'User e-mail', 'wc-frontend-manager-ultimate' ) ?>:
									</b>
									<?php echo $chat_data['user_email']; ?>
									<br />
								<?php } ?>
								<b>
									<?php _e( 'IP Address', 'wc-frontend-manager-ultimate' ) ?>:
								</b>
								<?php echo $chat_data['user_ip']; ?>
								<br />
							<?php } ?>
							<br />
							<b>
								<?php _e( 'Operator Name', 'wc-frontend-manager-ultimate' ) ?>:
							</b>
							<?php echo $chat_data['operator']; ?>
							<br />
							<b>
								<?php _e( 'Chat Duration', 'wc-frontend-manager-ultimate' ) ?>:
							</b>
							<?php echo $chat_data['duration']; ?>
							<br />
							<b>
								<?php _e( 'Chat closed by', 'wc-frontend-manager-ultimate' ) ?>:
							</b>
							<?php echo $chat_data['closed_by']; ?>
							<br />
							<b>
								<?php _e( 'Chat Evaluation', 'wc-frontend-manager-ultimate' ) ?>:
							</b>
							<?php echo $chat_data['evaluation']; ?>
							<br />
							<br />
						<?php endif; ?>
						<?php foreach ( $chat_logs as $log ): ?>
							<p style="<?php echo( ( $log['user_type'] == 'operator' ) ? $mailbody_p_op_css : $mailbody_p_css ); ?>">
                                <span style="<?php echo $mailbody_p_date_css; ?>">
                                    <?php 
                                    $timestamp  = ( $log['msg_time'] / 1000 ) + ( $gmt_offset * 3600 );
                                    echo  date_i18n( wc_date_format() . ' ' . wc_time_format(), $timestamp );
                                    ?>
                                </span>
                                <span>
                                    <b><?php echo $log['user_name']; ?>: </b>
	                                <?php echo stripslashes( $log['msg'] ); ?>
                                </span>
							</p>
						<?php endforeach; ?>
					</td>
				</tr>
			</table>
			<!--[if (gte mso 9)|(IE)]></td></tr></table><![endif]-->
		</td>
	</tr>
</table>