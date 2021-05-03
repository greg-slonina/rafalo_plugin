<?php
global $WCFM, $wp_query;

$wcfm_is_allow_chats_history = apply_filters( 'wcfm_is_allow_chats_history', true );
if( !$wcfm_is_allow_chats_history ) {
	wcfm_restriction_message_show( "Chats Offline" );
	return;
}

?>

<div class="collapse wcfm-collapse" id="wcfm_chats_history_listing">
	
	<div class="wcfm-page-headig">
		<span class="wcfmfa fa-history"></span>
		<span class="wcfm-page-heading-text"><?php _e( 'Chat History', 'wc-frontend-manager' ); ?></span>
		<?php do_action( 'wcfm_page_heading' ); ?>
	</div>
	<div class="wcfm-collapse-content">
		<div id="wcfm_page_load"></div>
		<?php do_action( 'before_wcfm_chats_history' ); ?>
		
		<div class="wcfm-container wcfm-top-element-container">
			<h2><?php _e( 'Chat History', 'wc-frontend-manager-ultimate' ); ?></h2>
			
			<?php
			echo '<a class="add_new_wcfm_ele_dashboard text_tip" href="'.wcfm_chatbox_url().'" data-tip="'. __('Chat Box', 'wc-frontend-manager-ultimate') .'"><span class="wcfmfa fa-comments"></span><span class="text">' . __('Chat Box', 'wc-frontend-manager-ultimate' ) . '</span></a>';
			echo '<a class="add_new_wcfm_ele_dashboard text_tip" href="'.wcfm_chats_offline_url().'" data-tip="'. __('Chat Offline Messages', 'wc-frontend-manager-ultimate') .'"><span class="wcfmfa fa-user-clock"></span><span class="text">' . __('Offline Messages', 'wc-frontend-manager-ultimate' ) . '</span></a>';
			?>
			
			<div class="wcfm-clearfix"></div>
		</div>
		<div class="wcfm-clearfix"></div><br />
		
		<div class="wcfm_chats_history_filter_wrap wcfm_products_filter_wrap  wcfm_filters_wrap">
			<?php	
			if( $wcfm_is_chats_history_vendor_filter = apply_filters( 'wcfm_is_chats_history_vendor_filter', true ) ) {
				$is_marketplace = wcfm_is_marketplace();
				if( $is_marketplace ) {
					if( !wcfm_is_vendor() ) {
						$vendor_arr = array(); //$WCFM->wcfm_vendor_support->wcfm_get_vendor_list();
						$WCFM->wcfm_fields->wcfm_generate_form_field( array(
																											"dropdown_vendor" => array( 'type' => 'select', 'options' => $vendor_arr, 'attributes' => array( 'style' => 'width: 150px;' ) )
																											 ) );
					}
				}
			}
			?>
		</div>
		
		<div class="wcfm-container">
			<div id="wcfm_chats_history_listing_expander" class="wcfm-content">
				<table id="wcfm-chats_history" class="display" cellspacing="0" width="100%">
					<thead>
						<tr>
						  <th><?php _e( 'User', 'wc-frontend-manager-ultimate' ); ?></th>
						  <th><?php _e( 'Email', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Total Messages', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php echo apply_filters( 'wcfm_sold_by_label', '', __( 'Store', 'wc-frontend-manager' ) ); ?></th>
							<th><?php _e( 'Duration', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Evaluation', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Date', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Actons', 'wc-frontend-manager' ); ?></th>
						</tr>
					</thead>
					<tfoot>
						<tr>
						  <th><?php _e( 'User', 'wc-frontend-manager-ultimate' ); ?></th>
						  <th><?php _e( 'Email', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Total Messages', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php echo apply_filters( 'wcfm_sold_by_label', '', __( 'Store', 'wc-frontend-manager' ) ); ?></th>
							<th><?php _e( 'Duration', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Evaluation', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Date', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Actons', 'wc-frontend-manager' ); ?></th>
						</tr>
					</tfoot>
				</table>
				<div class="wcfm-clearfix"></div>
			</div>
		</div>
		<?php
		do_action( 'after_wcfm_chats_history' );
		?>
	</div>
</div>