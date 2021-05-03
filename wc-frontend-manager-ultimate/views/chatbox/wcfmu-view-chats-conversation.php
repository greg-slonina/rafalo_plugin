<?php
global $WCFM, $WCFMu, $wpdb;

$mailbody_p_css      = 'line-height: normal; margin: 7px 0; background: #e5f6fd; -webkit-border-radius: 4px; -moz-border-radius: 4px; border-radius: 4px; padding: 5px 10px; ';
$mailbody_p_op_css   = 'line-height: normal; margin: 7px 0; background: #f2f2f2; -webkit-border-radius: 4px; -moz-border-radius: 4px; border-radius: 4px; padding: 5px 10px; ';
$mailbody_p_date_css = 'display:block; font-size: 11px; font-style: italic;';

$gmt_offset = get_option( 'gmt_offset' );
?>

<form class="wcfm_popup_wrapper">
	<div style="margin-bottom: 15px;"><h2 style="float: none;"><?php _e( 'Conversaton Details', 'wc-frontend-manager-ultimate' ); ?></h2></div>
			
	<?php
	if( $conversation ) {
		$conversation_messages = $wpdb->get_results(
																		$wpdb->prepare( "
																										SELECT      a.message_id,
																																a.conversation_id,
																																a.user_id,
																																a.user_name,
																																a.msg,
																																a.msg_time,
																																IFNULL( b.user_type, 'operator' ) AS user_type
																										FROM        {$wpdb->prefix}wcfm_fbc_chat_rows a LEFT JOIN {$wpdb->prefix}wcfm_fbc_chat_visitors b ON a.user_id = b.user_id
																										WHERE       a.conversation_id = %s
																										ORDER BY    a.msg_time
																										", $conversation ), ARRAY_A );
		if( !empty( $conversation_messages ) ) {
			?>
			<?php foreach( $conversation_messages as $log ) { ?>
					<p style="<?php echo( ( $log['user_type'] == 'operator' ) ? $mailbody_p_op_css : $mailbody_p_css ); ?>">
						<span>
								<b><?php echo $log['user_name']; ?>: </b>
							<?php echo stripslashes( $log['msg'] ); ?>
						</span>
						<span style="<?php echo $mailbody_p_date_css; ?>">
						  <i class="wcfmfa fa-clock"></i>
							<?php 
							$timestamp  = ( $log['msg_time'] / 1000 ) + ( $gmt_offset * 3600 );
							echo  date_i18n( wc_date_format() . ' ' . wc_time_format(), $timestamp );
							?>
						</span>
					</p>
			<?php } ?>
		<?php
		} else {
			_e( 'No conversation yet!', 'wc-frontend-manager-ultimate' );
		}
	} else {
		_e( 'No conversation found!', 'wc-frontend-manager-ultimate' );
	}
	?>
	<div class="wcfm_clearfix"></div>
</form>