<?php
global $WCFM, $WCFMu;

?>

<form id="wcfm_order_add_customer_form" class="wcfm_popup_wrapper">
	<div style="margin-bottom: 15px;"><h2 style="float: none;"><?php _e( 'Add Customer', 'wc-frontend-manager-ultimate' ); ?></h2></div>
	
	<table>
		<tbody>
			<tr>
				<td>
					<p class="wcfm_order_add_customer_form_label wcfm_popup_label"><?php _e( 'Email', 'wc-frontend-manager' ); ?> <span class="required">*</span></p>
					<input type="text" class="wcfm_popup_input" name="wcbc_user_email" value="" />
				</td>
			</tr>
			<tr>
				<td>
					<p class="wcfm_order_add_customer_form_label wcfm_popup_label"><?php _e( 'Phone', 'wc-frontend-manager' ); ?></p>
					<input type="text" class="wcfm_popup_input" name="wcbc_phone" value="" />
				</td>
			</tr>
			<tr>
				<td>
					<p class="wcfm_order_add_customer_form_label wcfm_popup_label"><?php _e( 'First Name', 'wc-frontend-manager' ); ?></p>
					<input type="text" class="wcfm_popup_input" name="wcbc_first_name" value="" />
				</td>
			</tr>
			<tr>
				<td>
					<p class="wcfm_order_add_customer_form_label wcfm_popup_label"><?php _e( 'Last Name', 'wc-frontend-manager' ); ?></p>
					<input type="text" class="wcfm_popup_input" name="wcbc_last_name" value="" />
				</td>
			</tr>
			
			<?php do_action( 'wcfm_order_add_customer_end' ); ?>
			
		</tbody>
	</table>
	<div class="wcfm-message" tabindex="-1"></div>
	<input type="button" class="wcfm_order_add_customer_button wcfm_popup_button wcfm_submit_button" id="wcfm_order_add_customer_button" value="<?php _e( 'Submit', 'wc-frontend-manager-ultimate' ); ?>" />
	
	<div class="wcfm_clearfix"></div>
</form>