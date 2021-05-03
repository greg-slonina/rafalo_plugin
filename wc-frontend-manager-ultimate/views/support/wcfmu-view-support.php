<?php
/**
 * WCFMu plugin view
 *
 * WCFM Support view
 *
 * @author 		WC Lovers
 * @package 	wcfm/views/support
 * @version   4.0.3
 */
 
global $WCFM, $WCFMu;


if( !apply_filters( 'wcfm_is_pref_support', true ) || !apply_filters( 'wcfm_is_allow_support', true ) ) {
	wcfm_restriction_message_show( "Supports" );
	return;
}

$products_array = array();

$ranges = array(
	'7day'         => __( 'Last 7 Days', 'wc-frontend-manager-ultimate' ),
	'month'        => __( 'This Month', 'wc-frontend-manager-ultimate' ),
	'last_month'   => __( 'Last Month', 'wc-frontend-manager-ultimate' ),
	'year'         => __( 'Year', 'wc-frontend-manager-ultimate' ),
);

$support_status = ! empty( $_GET['support_status'] ) ? sanitize_text_field( $_GET['support_status'] ) : 'all';
$support_status_types = $WCFMu->wcfmu_support->wcfm_support_status_types( true );

$support_priority_types = $WCFMu->wcfmu_support->wcfm_support_priority_types( true );

?>

<div class="collapse wcfm-collapse" id="wcfm_support_listing">
	
	<div class="wcfm-page-headig">
		<span class="wcfmfa fa-life-ring"></span>
		<span class="wcfm-page-heading-text"><?php _e( 'Support Tickets', 'wc-frontend-manager-ultimate' ); ?></span>
		<?php do_action( 'wcfm_page_heading' ); ?>
	</div>
	<div class="wcfm-collapse-content">
		<div id="wcfm_page_load"></div>
		
		<div class="wcfm-container wcfm-top-element-container">
			<ul class="wcfm_support_menus">
				<?php
				$is_first = true;
				foreach( $support_status_types as $wcfmu_status_menu_key => $wcfmu_status_menu) {
					?>
					<li class="wcfm_support_menu_item">
						<?php
						if($is_first) $is_first = false;
						else echo " | ";
						?>
						<a class="<?php echo ( $wcfmu_status_menu_key == $support_status ) ? 'active' : ''; ?>" href="<?php echo wcfm_support_url( $wcfmu_status_menu_key ); ?>"><?php echo $wcfmu_status_menu; ?></a>
					</li>
					<?php
				}
				?>
			</ul>
			
			<?php
			//echo '<a class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_support_manage_url().'" data-tip="' . __('Add New Topic', 'wc-frontend-manager-ultimate') . '"><span class="wcfmfa fa-question-circle-o"></span><span class="text">' . __( 'Add New', 'wc-frontend-manager-ultimate' ) . '</span></a>';
			?>
			<div class="wcfm-clearfix"></div>
		</div>
	  <div class="wcfm-clearfix"></div><br />
	  
	  <?php do_action( 'before_wcfm_support' ); ?>
	  
		<div class="wcfm_support_filter_wrap wcfm_enquiry_filter_wrap wcfm_filters_wrap">
		  <label style="margin-left: 10px;">
				<?php
				$WCFM->wcfm_fields->wcfm_generate_form_field( array( "support_priority" => array( 'type' => 'select', 'attributes' => array( 'style' => 'width: 150px;' ), 'class' => 'wcfm-select wcfm_ele', 'label_class' => 'wcfm_title', 'options' => $support_priority_types ) ) );
				?>
			</label>
		  <?php 
		  $WCFM->wcfm_fields->wcfm_generate_form_field( array( "support_product" => array( 'type' => 'select', 'attributes' => array( 'style' => 'width: 150px;' ), 'class' => 'wcfm-select wcfm_ele', 'label_class' => 'wcfm_title', 'options' => $products_array ) ) ); 
		  
		  if( $wcfm_is_products_vendor_filter = apply_filters( 'wcfm_is_products_vendor_filter', true ) ) {
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
		  <?php $WCFM->library->wcfm_date_range_picker_field(); ?>
		</div>

		<div class="wcfm-container">
			<div id="wcfm_support_listing_expander" class="wcfm-content">
				<table id="wcfm-support" class="display" cellspacing="0" width="100%">
					<thead>
						<tr>
						  <th><span class="wcicon-status-processing text_tip" data-tip="<?php _e( 'Status', 'wc-frontend-manager' ); ?>"></span></th>
						  <th><?php _e( 'Ticket', 'wc-frontend-manager-ultimate' ); ?></th>
						  <th><?php _e( 'Category', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Issue', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Item', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Customer', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Priority', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php echo apply_filters( 'wcfm_sold_by_label', '', __( 'Store', 'wc-frontend-manager' ) ); ?></th>
							<th><?php _e( 'Date', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Actions', 'wc-frontend-manager-ultimate' ); ?></th>
						</tr>
					</thead>
					<tfoot>
						<tr>
						  <th><span class="wcicon-status-processing text_tip" data-tip="<?php _e( 'Status', 'wc-frontend-manager' ); ?>"></span></th>
							<th><?php _e( 'Ticket', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Category', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Issue', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Item', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Customer', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Priority', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php echo apply_filters( 'wcfm_sold_by_label', '', __( 'Store', 'wc-frontend-manager' ) ); ?></th>
							<th><?php _e( 'Date', 'wc-frontend-manager-ultimate' ); ?></th>
							<th><?php _e( 'Actions', 'wc-frontend-manager-ultimate' ); ?></th>
						</tr>
					</tfoot>
				</table>
				<div class="wcfm-clearfix"></div>
			</div>
		</div>
			
		<?php do_action( 'after_wcfm_support' ); ?>
	</div>
</div>
