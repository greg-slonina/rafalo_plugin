<?php
/**
 * WCFMu plugin core
 *
 * Plugin Vendor Invoice Controler
 *
 * @author 		WC Lovers
 * @package 	wcfmu/core
 * @version   4.1.9
 */
 
class WCFMu_Vendor_Invoice {
	
	public function __construct() {
		global $WCFM, $WCFMu;
		
		if( !WCFM_Dependencies::wcfm_wc_pdf_invoices_packing_slips_plugin_active_check() ) return;
			
		if( $WCFMu->is_marketplace ) {
			// Invoice Settings
			add_action( 'end_wcfm_settings', array( &$this, 'wcfmu_vendor_invoice_settings' ), 16 );
			add_action( 'wcfm_settings_update', array( &$this, 'wcfmu_vendor_invoice_settings_update' ), 16 );
			
			add_action( 'end_wcfm_vendor_settings', array( &$this, 'wcfmu_vendor_invoice_store_settings' ), 16 );
			add_action( 'end_wcfm_vendors_manage_form', array( &$this, 'wcfmmp_vendor_manage_invoice_setting' ), 13 );
			add_action( 'wcfm_vendor_settings_update', array( &$this, 'wcfmu_vendor_invoice_store_settings_update' ), 16, 2 );
			
			
			// Store Invoice in Customer Email attachment
			add_filter( 'woocommerce_email_attachments', array( $this, 'wcfmu_attach_vendor_pdf_to_email' ), 95, 3 );
			
			// Membership Subscription Invoice
			add_filter( 'wcfm_membership_subscription_attachment', array( $this, 'wcfmu_membership_subscription_invoice' ), 95, 4 );
		}
		
		if( apply_filters( 'wcfm_is_allow_pdf_invoice', true ) || apply_filters( 'wcfm_is_allow_pdf_packing_slip', true ) || apply_filters( 'wcfm_is_allow_store_invoice', true ) ) {
			add_filter( 'wcfm_orders_module_actions', array( &$this, 'wcfmu_pdf_invoice_orders_actions' ), 10, 4 );
		}
		
		// WooCommerce PDF Invoices & Packing Slips Support
		add_action( 'wp_ajax_wcfm_order_pdf_invoice', array( &$this, 'wcfm_order_pdf_invoice' ) );
		add_action( 'wp_ajax_wcfm_order_pdf_packing_slip', array( &$this, 'wcfm_order_pdf_packing_slip' ) );
		
		// Store Payment Invoice 
		add_action( 'wp_ajax_store_payment_invoice', array( &$this, 'wcfm_store_payment_invoice' ) );
		
		// WC My Account Order action
		add_filter( 'woocommerce_my_account_my_orders_actions', array( &$this, 'wcfm_my_account_store_order_pdf_invoice_download' ), 100, 2 );
		
	}
	
	function wcfmu_vendor_invoice_settings( $wcfm_options ) {
		global $WCFM, $WCFMu;
		$wcfm_vendor_invoice_options = get_option( 'wcfm_vendor_invoice_options', array() );
		$wcfm_vendor_invoice_active = isset( $wcfm_vendor_invoice_options['enable'] ) ? 'yes' : '';
		$wcfm_vendor_invoice_advance_font = isset( $wcfm_vendor_invoice_options['advance_font'] ) ? 'yes' : '';
		$wcfm_vendor_invoice_advance_currency = isset( $wcfm_vendor_invoice_options['advance_currency'] ) ? 'yes' : '';
		$wcfm_vendor_invoice_logo = isset( $wcfm_vendor_invoice_options['logo'] ) ? 'yes' : '';
		$wcfm_vendor_invoice_store = isset( $wcfm_vendor_invoice_options['store'] ) ? 'yes' : '';
		$wcfm_vendor_invoice_address = isset( $wcfm_vendor_invoice_options['address'] ) ? 'yes' : '';
		$wcfm_vendor_invoice_email = isset( $wcfm_vendor_invoice_options['email'] ) ? 'yes' : '';
		$wcfm_vendor_invoice_phone = isset( $wcfm_vendor_invoice_options['phone'] ) ? 'yes' : '';
		$wcfm_vendor_invoice_policies = isset( $wcfm_vendor_invoice_options['policies'] ) ? 'yes' : '';
		$wcfm_vendor_invoice_disclaimer = isset( $wcfm_vendor_invoice_options['disclaimer'] ) ? 'yes' : '';
		$wcfm_vendor_invoice_signature = isset( $wcfm_vendor_invoice_options['signature'] ) ? 'yes' : '';
		$wcfm_vendor_invoice_fields = isset( $wcfm_vendor_invoice_options['fields'] ) ? $wcfm_vendor_invoice_options['fields'] : array();
		?>
		<!-- collapsible -->
		<div class="page_collapsible" id="wcfm_settings_form_vendor_invoice_head">
			<label class="wcfmfa fa-file-invoice"></label>
			<?php echo apply_filters( 'wcfm_sold_by_label', '', __( 'Store', 'wc-frontend-manager' ) ) . ' ' . __( 'Invoice', 'wc-frontend-manager-ultimate'); ?><span></span>
		</div>
		<div class="wcfm-container">
			<div id="wcfm_settings_form_vendor_invoice_expander" class="wcfm-content">
			  <h2><?php echo apply_filters( 'wcfm_sold_by_label', '', __( 'Store', 'wc-frontend-manager' ) ) . ' ' . __('Store Invoice Setting', 'wc-frontend-manager-ultimate'); ?></h2>
				<?php wcfm_video_tutorial( 'https://wclovers.com/knowledgebase/wcfm-store-invoice/' ); ?>
				<div class="wcfm_clearfix"></div>
				<?php
					$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfmu_settings_fields_vendor_invoice', array(
																																										"wcfm_vendor_invoice_active" => array('label' => __('Enable', 'wc-frontend-manager-ultimate'), 'type' => 'checkbox', 'name' => 'wcfm_vendor_invoice_options[enable]', 'class' => 'wcfm-checkbox wcfm_ele', 'label_class' => 'wcfm_title checkbox_title wcfm_ele', 'value' => 'yes', 'dfvalue' => $wcfm_vendor_invoice_active, 'hints' => __( 'Send out vendor store specific invoice to customer.', 'wc-frontend-manager-ultimate' ) ),
																																										"wcfm_vendor_invoice_advance_font" => array('label' => __('Advance Font', 'wc-frontend-manager-ultimate'), 'type' => 'checkbox', 'name' => 'wcfm_vendor_invoice_options[advance_font]', 'class' => 'wcfm-checkbox wcfm_ele', 'label_class' => 'wcfm_title checkbox_title wcfm_ele', 'value' => 'yes', 'dfvalue' => $wcfm_vendor_invoice_advance_font, 'hints' => __( 'First check with default font, if not working then only enable this.', 'wc-frontend-manager-ultimate' ), 'desc_class' => 'wcfm_page_options_desc', 'desc' => sprintf( __( 'For having RTL support please install %sPDF Invoice mPDF library%s', 'wc-frontend-manager-ultimate' ), '<a href="https://github.com/wpovernight/woocommerce-pdf-ips-mpdf/releases/download/v1.0.1/woocommerce-pdf-ips-mpdf.zip">', '</a>' ) ),
																																										"wcfm_vendor_invoice_advance_currency" => array('label' => __('Advance Currency', 'wc-frontend-manager-ultimate'), 'type' => 'checkbox', 'name' => 'wcfm_vendor_invoice_options[advance_currency]', 'class' => 'wcfm-checkbox wcfm_ele', 'label_class' => 'wcfm_title checkbox_title wcfm_ele', 'value' => 'yes', 'dfvalue' => $wcfm_vendor_invoice_advance_currency, 'hints' => __( 'First check with default setting, if not working then only enable this.', 'wc-frontend-manager-ultimate' ) ),
																																										"wcfm_vendor_invoice_logo" => array('label' => __('Vendor Logo', 'wc-frontend-manager-ultimate'), 'type' => 'checkbox', 'name' => 'wcfm_vendor_invoice_options[logo]', 'class' => 'wcfm-checkbox wcfm_ele', 'label_class' => 'wcfm_title checkbox_title wcfm_ele', 'value' => 'yes', 'dfvalue' => $wcfm_vendor_invoice_logo ),
																																										"wcfm_vendor_invoice_store" => array('label' => __('Vendor Store Name', 'wc-frontend-manager-ultimate'), 'type' => 'checkbox', 'name' => 'wcfm_vendor_invoice_options[store]', 'class' => 'wcfm-checkbox wcfm_ele', 'label_class' => 'wcfm_title checkbox_title wcfm_ele', 'value' => 'yes', 'dfvalue' => $wcfm_vendor_invoice_store ),
																																										"wcfm_vendor_invoice_address" => array('label' => __('Vendor Address', 'wc-frontend-manager-ultimate'), 'type' => 'checkbox', 'name' => 'wcfm_vendor_invoice_options[address]', 'class' => 'wcfm-checkbox wcfm_ele', 'label_class' => 'wcfm_title checkbox_title wcfm_ele', 'value' => 'yes', 'dfvalue' => $wcfm_vendor_invoice_address ),
																																										"wcfm_vendor_invoice_email" => array('label' => __('Vendor Email', 'wc-frontend-manager-ultimate'), 'type' => 'checkbox', 'name' => 'wcfm_vendor_invoice_options[email]', 'class' => 'wcfm-checkbox wcfm_ele', 'label_class' => 'wcfm_title checkbox_title wcfm_ele', 'value' => 'yes', 'dfvalue' => $wcfm_vendor_invoice_email ),
																																										"wcfm_vendor_invoice_phone" => array('label' => __('Vendor Phone', 'wc-frontend-manager-ultimate'), 'type' => 'checkbox', 'name' => 'wcfm_vendor_invoice_options[phone]', 'class' => 'wcfm-checkbox wcfm_ele', 'label_class' => 'wcfm_title checkbox_title wcfm_ele', 'value' => 'yes', 'dfvalue' => $wcfm_vendor_invoice_phone ),
																																										"wcfm_vendor_invoice_policies" => array('label' => __('Vendor policies', 'wc-frontend-manager-ultimate'), 'type' => 'checkbox', 'name' => 'wcfm_vendor_invoice_options[policies]', 'class' => 'wcfm-checkbox wcfm_ele', 'label_class' => 'wcfm_title checkbox_title wcfm_ele', 'value' => 'yes', 'dfvalue' => $wcfm_vendor_invoice_policies ),
																																										"wcfm_vendor_invoice_disclaimer" => array('label' => __('Vendor Disclaimer', 'wc-frontend-manager-ultimate'), 'type' => 'checkbox', 'name' => 'wcfm_vendor_invoice_options[disclaimer]', 'class' => 'wcfm-checkbox wcfm_ele', 'label_class' => 'wcfm_title checkbox_title wcfm_ele', 'value' => 'yes', 'dfvalue' => $wcfm_vendor_invoice_disclaimer ),
																																										"wcfm_vendor_invoice_signature" => array('label' => __('Vendor Signature', 'wc-frontend-manager-ultimate'), 'type' => 'checkbox', 'name' => 'wcfm_vendor_invoice_options[signature]', 'class' => 'wcfm-checkbox wcfm_ele', 'label_class' => 'wcfm_title checkbox_title wcfm_ele', 'value' => 'yes', 'dfvalue' => $wcfm_vendor_invoice_signature ),
																																										"wcfm_vendor_invoice_fields" => array('label' => __('Additional Info', 'wc-frontend-manager-ultimate') , 'type' => 'multiinput', 'name' => 'wcfm_vendor_invoice_options[fields]', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title', 'value' => $wcfm_vendor_invoice_fields, 'options' => array(
																																																"is_active" => array('label' => __('Enable', 'wc-frontend-manager-ultimate'), 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'label_class' => 'wcfm_title checkbox_title wcfm_ele', 'value' => 'yes' ),
																																																"field" => array('label' => __('Field', 'wc-frontend-manager-ultimate'), 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele' ),
																																											) ) ) ) );
			  ?>
			</div>
		</div>
		<div class="wcfm_clearfix"></div>
		<!-- end collapsible -->
		
		<?php
		
	}
	
	function wcfmu_vendor_invoice_settings_update( $wcfm_settings_form ) {
		global $WCFM, $WCFMu, $_POST;
		
		if( isset( $wcfm_settings_form['wcfm_vendor_invoice_options'] ) ) {
			$wcfm_vendor_invoice_options = $wcfm_settings_form['wcfm_vendor_invoice_options'];
			update_option( 'wcfm_vendor_invoice_options',  $wcfm_vendor_invoice_options );
		}
	}
	
	function wcfmu_vendor_invoice_store_settings( $vendor_id ) {
		global $WCFM, $WCFMu;
		$wcfm_vendor_invoice_options = get_option( 'wcfm_vendor_invoice_options', array() );
		$wcfm_vendor_invoice_active = isset( $wcfm_vendor_invoice_options['enable'] ) ? 'yes' : '';
		$wcfm_vendor_invoice_fields = isset( $wcfm_vendor_invoice_options['fields'] ) ? $wcfm_vendor_invoice_options['fields'] : array();
		$wcfm_vendor_invoice_data = (array) wcfm_get_user_meta( $vendor_id, 'wcfm_vendor_invoice_options', true );
		$wcfm_vendor_invoice_prefix = isset( $wcfm_vendor_invoice_data['prefix'] ) ? $wcfm_vendor_invoice_data['prefix'] : '';
		$wcfm_vendor_invoice_sufix = isset( $wcfm_vendor_invoice_data['sufix'] ) ? $wcfm_vendor_invoice_data['sufix'] : '';
		$wcfm_vendor_invoice_digit = isset( $wcfm_vendor_invoice_data['digit'] ) ? $wcfm_vendor_invoice_data['digit'] : '';
		$wcfm_vendor_invoice_disclaimer = isset( $wcfm_vendor_invoice_data['disclaimer'] ) ? $wcfm_vendor_invoice_data['disclaimer'] : '';
		$wcfm_vendor_invoice_signature = isset( $wcfm_vendor_invoice_data['signature'] ) ? $wcfm_vendor_invoice_data['signature'] : '';
		
		if( $wcfm_vendor_invoice_active && apply_filters( 'wcfm_is_allow_store_invoice', true ) ) {
			?>
			<!-- collapsible -->
			<div class="page_collapsible" id="wcfm_settings_form_vendor_invoice_head">
				<label class="wcfmfa fa-file-invoice"></label>
				<?php _e('Store Invoice', 'wc-frontend-manager-ultimate'); ?><span></span>
			</div>
			<div class="wcfm-container">
				<div id="wcfm_settings_form_vendor_invoice_expander" class="wcfm-content">
					<?php
					$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfmu_settings_fields_vendor_store_invoice', array(
																																										"wcfm_vendor_invoice_prefix" => array('label' => __('Invoice No Prefix', 'wc-frontend-manager-ultimate'), 'type' => 'text', 'name' => 'wcfm_vendor_invoice_options[prefix]', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $wcfm_vendor_invoice_prefix ),
																																										"wcfm_vendor_invoice_sufix" => array('label' => __('Invoice No. Sufix', 'wc-frontend-manager-ultimate'), 'type' => 'text', 'name' => 'wcfm_vendor_invoice_options[sufix]', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $wcfm_vendor_invoice_sufix ),
																																										"wcfm_vendor_invoice_digit" => array('label' => __('Invoice No. Digit', 'wc-frontend-manager-ultimate'), 'type' => 'number', 'name' => 'wcfm_vendor_invoice_options[digit]', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $wcfm_vendor_invoice_digit, 'hints' => __( 'enter the number of digits here - enter `6` to display 42 as 000042', 'wc-frontend-manager-ultimate' ), 'attributes' => array( 'min' => 1, 'step' => 1, 'max' => 9 ) ),
																																										), $vendor_id ) );
					if( !empty( $wcfm_vendor_invoice_fields ) ) {
						foreach( $wcfm_vendor_invoice_fields as $wvif_key => $wcfm_vendor_invoice_field ) {
							if( isset($wcfm_vendor_invoice_field['is_active']) && $wcfm_vendor_invoice_field['field'] ) {
								$invoice_field_value = '';
								if( isset( $wcfm_vendor_invoice_data[$wvif_key] ) && !empty( $wcfm_vendor_invoice_data[$wvif_key] ) ) { $invoice_field_value = $wcfm_vendor_invoice_data[$wvif_key]; }
								$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfmu_settings_fields_vendor_store_invoice_custom', array( 
																																		'wvif_key_' .$wvif_key => array( 'label' => $wcfm_vendor_invoice_field['field'], 'name' => 'wcfm_vendor_invoice_options['.$wvif_key.']', 'type' => 'text', 'class' => 'wcfm-text', 'label_class' => 'wcfm_title', 'value' => $invoice_field_value ) 
																																		), $vendor_id, $wvif_key, $wcfm_vendor_invoice_data ) );
							}
						}
					}
					
					$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfmu_settings_fields_vendor_store_invoice_more', array(
																																										"wcfm_vendor_invoice_disclaimer" => array('label' => __('Disclaimer', 'wc-frontend-manager-ultimate'), 'type' => 'textarea', 'name' => 'wcfm_vendor_invoice_options[disclaimer]', 'class' => 'wcfm-textarea wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $wcfm_vendor_invoice_disclaimer ),
																																										"wcfm_vendor_invoice_signature" => array('label' => __('Digital Signature', 'wc-frontend-manager-ultimate'), 'type' => 'upload', 'name' => 'wcfm_vendor_invoice_options[signature]', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $wcfm_vendor_invoice_signature ),
																																										), $vendor_id ) );
					?>
				</div>
			</div>
			<div class="wcfm_clearfix"></div>
			<!-- end collapsible -->
			<?php
		}
	}
	
	function wcfmmp_vendor_manage_invoice_setting( $vendor_id ) {
		global $WCFM, $WCFMu;
		$wcfm_vendor_invoice_options = get_option( 'wcfm_vendor_invoice_options', array() );
		$wcfm_vendor_invoice_active = isset( $wcfm_vendor_invoice_options['enable'] ) ? 'yes' : '';
		$wcfm_vendor_invoice_fields = isset( $wcfm_vendor_invoice_options['fields'] ) ? $wcfm_vendor_invoice_options['fields'] : array();
		$wcfm_vendor_invoice_data = (array) wcfm_get_user_meta( $vendor_id, 'wcfm_vendor_invoice_options', true );
		$wcfm_vendor_invoice_prefix = isset( $wcfm_vendor_invoice_data['prefix'] ) ? $wcfm_vendor_invoice_data['prefix'] : '';
		$wcfm_vendor_invoice_sufix = isset( $wcfm_vendor_invoice_data['sufix'] ) ? $wcfm_vendor_invoice_data['sufix'] : '';
		$wcfm_vendor_invoice_digit = isset( $wcfm_vendor_invoice_data['digit'] ) ? $wcfm_vendor_invoice_data['digit'] : '';
		$wcfm_vendor_invoice_disclaimer = isset( $wcfm_vendor_invoice_data['disclaimer'] ) ? $wcfm_vendor_invoice_data['disclaimer'] : '';
		$wcfm_vendor_invoice_signature = isset( $wcfm_vendor_invoice_data['signature'] ) ? $wcfm_vendor_invoice_data['signature'] : '';
		
		if( $wcfm_vendor_invoice_active && apply_filters( 'wcfm_is_allow_store_invoice', true ) ) {
			?>
			<!-- collapsible -->
			<div class="page_collapsible vendor_manage_store_invoice_setting" id="wcfm_vendor_manage_form_vendor_invoice_head">
				<label class="wcfmfa fa-file-invoice"></label>
				<?php _e('Store Invoice', 'wc-frontend-manager-ultimate'); ?><span></span>
			</div>
			<div class="wcfm-container">
				<div id="wcfm_vendor_manage_form_vendor_invoice_expander" class="wcfm-content">
				  <form id="wcfm_vendor_manage_store_invoice_setting_form" class="wcfm">
					
						<div class="wcfm_clearfix"></div>
						<div class=""><h2><?php _e( 'Store Invoice Setting', 'wc-frontend-manager-ultimate' ); ?></h2></div>
						<div class="wcfm_clearfix"></div><br/>
						
						<div class="store_address">
							<?php
							$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfmu_settings_fields_vendor_store_invoice', array(
																																												"wcfm_vendor_invoice_prefix" => array('label' => __('Invoice No Prefix', 'wc-frontend-manager-ultimate'), 'type' => 'text', 'name' => 'wcfm_vendor_invoice_options[prefix]', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $wcfm_vendor_invoice_prefix ),
																																												"wcfm_vendor_invoice_sufix" => array('label' => __('Invoice No. Sufix', 'wc-frontend-manager-ultimate'), 'type' => 'text', 'name' => 'wcfm_vendor_invoice_options[sufix]', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $wcfm_vendor_invoice_sufix ),
																																												"wcfm_vendor_invoice_digit" => array('label' => __('Invoice No. Digit', 'wc-frontend-manager-ultimate'), 'type' => 'number', 'name' => 'wcfm_vendor_invoice_options[digit]', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $wcfm_vendor_invoice_digit, 'hints' => __( 'enter the number of digits here - enter `6` to display 42 as 000042', 'wc-frontend-manager-ultimate' ), 'attributes' => array( 'min' => 1, 'step' => 1, 'max' => 9 ) ),
																																												), $vendor_id ) );
							if( !empty( $wcfm_vendor_invoice_fields ) ) {
								foreach( $wcfm_vendor_invoice_fields as $wvif_key => $wcfm_vendor_invoice_field ) {
									if( isset($wcfm_vendor_invoice_field['is_active']) && $wcfm_vendor_invoice_field['field'] ) {
										$invoice_field_value = '';
										if( isset( $wcfm_vendor_invoice_data[$wvif_key] ) && !empty( $wcfm_vendor_invoice_data[$wvif_key] ) ) { $invoice_field_value = $wcfm_vendor_invoice_data[$wvif_key]; }
										$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfmu_settings_fields_vendor_store_invoice_custom', array( 
																																				'wvif_key_' .$wvif_key => array( 'label' => $wcfm_vendor_invoice_field['field'], 'name' => 'wcfm_vendor_invoice_options['.$wvif_key.']', 'type' => 'text', 'class' => 'wcfm-text', 'label_class' => 'wcfm_title', 'value' => $invoice_field_value ) 
																																				), $vendor_id, $wvif_key, $wcfm_vendor_invoice_data ) );
									}
								}
							}
							
							$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfmu_settings_fields_vendor_store_invoice_more', array(
																																												"wcfm_vendor_invoice_disclaimer" => array('label' => __('Disclaimer', 'wc-frontend-manager-ultimate'), 'type' => 'textarea', 'name' => 'wcfm_vendor_invoice_options[disclaimer]', 'class' => 'wcfm-textarea wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $wcfm_vendor_invoice_disclaimer ),
																																												"wcfm_vendor_invoice_signature" => array('label' => __('Digital Signature', 'wc-frontend-manager-ultimate'), 'type' => 'upload', 'name' => 'wcfm_vendor_invoice_options[signature]', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $wcfm_vendor_invoice_signature ),
																																												), $vendor_id ) );
							?>
						</div>	
						<div class="wcfm-clearfix"></div>
						<div class="wcfm-message" tabindex="-1"></div>
						<div class="wcfm-clearfix"></div>
						<div id="wcfm_messages_submit">
							<input type="hidden" name="vendor_id" value="<?php echo $vendor_id; ?>" />
							<input type="submit" name="save-data" value="<?php _e( 'Update', 'wc-frontend-manager' ); ?>" id="wcfm_store_invoice_setting_save_button" class="wcfm_submit_button" />
						</div>
						<div class="wcfm-clearfix"></div>
					</form>
				</div>
			</div>
			<div class="wcfm_clearfix"></div><br />
			<!-- end collapsible -->
			<?php
		}
	}
	
	function wcfmu_vendor_invoice_store_settings_update( $user_id, $wcfm_settings_form ) {
		global $WCFM, $WCFMu, $_POST;
		
		if( isset( $wcfm_settings_form['wcfm_vendor_invoice_options'] ) ) {
			$wcfm_vendor_invoice_options = $wcfm_settings_form['wcfm_vendor_invoice_options'];
			wcfm_update_user_meta( $user_id, 'wcfm_vendor_invoice_options',  $wcfm_vendor_invoice_options );
		}
	}
	
	function wcfmu_pdf_invoice_orders_actions( $actions, $order_id, $the_order, $vendor_id = 0 ) {
		
		$order_status = sanitize_title( $the_order->get_status() );
		if( in_array( $order_status, apply_filters( 'wcfm_pdf_invoice_download_disable_order_status', array( 'failed', 'cancelled', 'request', 'proposal', 'proposal-sent', 'proposal-expired', 'proposal-rejected', 'proposal-canceled', 'proposal-accepted' ) ) ) ) return $actions;
		
		$pdf_actions = '';
		
		$item_class = '';
		if( !$actions ) {
		  $item_class = 'order_quick_action add_new_wcfm_ele_dashboard';
		}
		
		if( wcfm_is_vendor() ) {
			$vendor_id = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );
		}
		
		if( apply_filters( 'wcfm_is_allow_pdf_invoice', true ) && apply_filters( 'wcfm_is_allow_view_commission', true ) ) {
			if( wcfm_is_vendor() ) {
				$pdf_actions .= '<a class="wcfm_pdf_invoice wcfm-action-icon '. $item_class . ' " href="#" data-vendorid="' . $vendor_id . '" data-orderid="' . $order_id . '"><span class="wcfmfa fa-currence text_tip" data-tip="' . esc_attr__( 'Commission Invoice', 'wc-frontend-manager-ultimate' ) . '">'.get_woocommerce_currency_symbol().'</span></a>';
			} else {
				$pdf_actions .= '<a class="wcfm_pdf_invoice wcfm-action-icon '. $item_class . '" href="#" data-vendorid="' . $vendor_id . '" data-orderid="' . $order_id . '"><span class="wcfmfa fa-file-invoice text_tip" data-tip="' . esc_attr__( 'Sales Invoice', 'wc-frontend-manager-ultimate' ) . '"></span></a>';
			}
		}
		
		if( wcfm_is_vendor() && apply_filters( 'wcfm_is_allow_store_invoice', true ) ) {
			$wcfm_store_invoices = (array) get_post_meta( $order_id, '_wcfm_store_invoices', true );
			if( !empty( $wcfm_store_invoices ) && isset( $wcfm_store_invoices[$vendor_id] ) && !empty( $wcfm_store_invoices[$vendor_id] ) && !is_array( $wcfm_store_invoices[$vendor_id] ) ) {
				$pdf_path = $wcfm_store_invoices[$vendor_id];
				if( file_exists( $pdf_path ) ) {
					$upload_dir = wp_upload_dir();
					if (empty($upload_dir['error'])) {
						$upload_base = trailingslashit( $upload_dir['basedir'] );
						$upload_url = trailingslashit( $upload_dir['baseurl'] );
						$pdf_path = str_replace( $upload_base, $upload_url, $pdf_path );
						$pdf_actions .= '<a class="wcfm_store_invoice wcfm-action-icon '. $item_class . '" target="_blank" href="'.$pdf_path.'"><span class="wcfmfa fa-file-invoice text_tip" data-tip="' . esc_attr__( 'Store Invoice', 'wc-frontend-manager-ultimate' ) . '"></span></a>';
					}
				}
			}
		}
		
		if( apply_filters( 'wcfm_is_allow_pdf_packing_slip', true ) ) {
			$pdf_actions .= '<a class="wcfm_pdf_packing_slip wcfm-action-icon '. $item_class . '" href="#" data-orderid="' . $order_id . '"><span class="wcfmfa fa-file-powerpoint text_tip" data-tip="' . esc_attr__( 'Download Packing Slip', 'wc-frontend-manager-ultimate' ) . '"></span></a>';
		}
		
		if( $actions && $pdf_actions ) {
			$actions .= "<br/>" . $pdf_actions . "<br/>";
		} else {
			$actions = $pdf_actions;
		}
		return $actions;
	}
	
	public function wcfmu_attach_vendor_pdf_to_email( $attachments, $email_id, $the_order ) {
		global $WCFM, $WCFMu, $wpo_wcpdf, $order, $order_id, $document, $document_type, $vendor_id, $process_item_ids, $process_product_ids, $process_shipping_items, $wcfm_vendor_invoice_id, $product_id;
		
		$wcfm_vendor_invoice_options = get_option( 'wcfm_vendor_invoice_options', array() );
		$wcfm_vendor_invoice_active = isset( $wcfm_vendor_invoice_options['enable'] ) ? 'yes' : '';
		if( !$wcfm_vendor_invoice_active ) {
			return $attachments;
		}
		
		// check if all variables properly set
		if ( !is_object( $the_order ) || !isset( $email_id ) ) {
			return $attachments;
		}
		
		$order = $the_order;
		
		// Skip User emails
		if ( get_class( $order ) == 'WP_User' ) {
			return $attachments;
		}
		
		if ( ! ( $order instanceof \WC_Order || is_subclass_of( $order, '\WC_Abstract_Order') ) ) {
			return $attachments;
		}
		
		if ( method_exists( $order, 'get_id' ) ) {
			$order_id = $order->get_id();
		} else {
			$order_id = isset($order->id) ? $order->id : false;
		}
		
		if(  $order_id == false ) {
			return $attachments;
		}
		
		// WooCommerce Booking compatibility
		if ( get_post_type( $order_id ) == 'wc_booking' && isset($order->order) ) {
			// $order is actually a WC_Booking object!
			//$order = $order->order;
			return $attachments;
		}
		
		// do not process low stock notifications, user emails etc!
		if ( !in_array( $email_id, apply_filters( 'wcfm_allowed_store_invoice_order_emails', array( 'customer_invoice', 'new_order', 'customer_on_hold_order', 'customer_completed_order', 'customer_processing_order', 'store-new-order', 'new_renewal_order', 'customer_processing_renewal_order', 'customer_completed_renewal_order', 'customer_renewal_invoice' ) ) ) || get_post_type( $order_id ) != 'shop_order' ) {
			return $attachments;
		}
		
		$document_type = 'invoice';
		$wcfm_store_invoice_ids = get_post_meta( $order_id, '_wcfm_store_invoice_ids', true );
		if( !$wcfm_store_invoice_ids ) $wcfm_store_invoice_ids = array();
		
		$wcfm_store_invoices = get_post_meta( $order_id, '_wcfm_store_invoices', true );
		if( !$wcfm_store_invoices ) $wcfm_store_invoices = array();
		else {
			foreach( $wcfm_store_invoices as $vendor_id => $wcfm_store_invoice ) {
				$wcfm_store_invoices[$vendor_id] = esc_sql($wcfm_store_invoice);
			}
		}
		
		// Reset Site Language to Current Langugae
		if ( defined( 'ICL_SITEPRESS_VERSION' ) && ! ICL_PLUGIN_INACTIVE && class_exists( 'SitePress' ) ) {
			global $sitepress;
			$lang = get_post_meta( $order_id, 'wpml_language', true );
      if( !empty( $lang ) ) {
       	$sitepress->switch_lang( $lang, true );
      }
		}
		
		// disable deprecation notices during email sending
		add_filter( 'wcpdf_disable_deprecation_notices', '__return_true' );
		
		// reload translations because WC may have switched to site locale (by setting the plugin_locale filter to site locale in wc_switch_to_site_locale())
		WPO_WCPDF()->translations();
		do_action( 'wpo_wcpdf_reload_attachment_translations' );
		
		// prepare document
		$document = wcpdf_get_document( $document_type, (array) $order_id, true );
		if( !$document ) return;
		
		do_action( 'wpo_wcpdf_before_pdf', $document_type, $document );
		
		$upload_dir = wp_upload_dir();
		if (!empty($upload_dir['error'])) {
			$tmp_path = false;
		} else {
			$upload_base = trailingslashit( $upload_dir['basedir'] );
			$tmp_path = $upload_base . 'wcfm/vendor_invoice/';
		}

		$tmp_path = apply_filters( 'wcfm_vendor_invoice_tmp_path', $tmp_path );
		if ($tmp_path !== false) {
			$tmp_path = trailingslashit( $tmp_path );
			if ( !wp_mkdir_p( $tmp_path ) ) {
				return $attachments;
			}
		} else {
			return $attachments;
		}
		
		$invoice_settings = array(
			'paper_size'		=> 'A4',
			'paper_orientation'	=> 'portrait',
		);
		
		$cur_year  = date('Y');
		$cur_month = date('m');
		
		$wcfm_store_invoice_items = array();
		$line_items               = $order->get_items( 'line_item' );
		$line_items_shipping      = $order->get_items( 'shipping' );
		$processed_item_vendor    = array();
		
		if( !empty( $line_items ) ) {
			foreach ( $line_items as $item_id => $item ) {
				$product  = $item->get_product();
				$product_id = $item->get_product_id();
				$vendor_id = wcfm_get_vendor_id_by_post( $product_id );
				if( !wcfm_is_vendor( $vendor_id ) ) continue;
				if( in_array( $vendor_id, $processed_item_vendor ) ) continue;
				
				// Store New Order vendor check
				if( $email_id == 'store-new-order' ) {
					if( $WCFM->wcfm_marketplace && $WCFM->wcfm_marketplace->vendor_id && ( $WCFM->wcfm_marketplace->vendor_id != $vendor_id ) ) continue;
				}
				
				if( $vendor_id && $WCFM->wcfm_vendor_support->wcfm_vendor_has_capability( $vendor_id, 'store_invoice' ) ) {
					if( !empty( $wcfm_store_invoices ) && isset( $wcfm_store_invoices[$vendor_id] ) && !empty( $wcfm_store_invoices[$vendor_id] ) && !is_array( $wcfm_store_invoices[$vendor_id] ) ) {
						$pdf_path = $wcfm_store_invoices[$vendor_id];
						if( file_exists( $pdf_path ) ) {
							$attachments[] = $pdf_path;
							$processed_item_vendor[$vendor_id] = $vendor_id;
						} else {
							$wcfm_store_invoice_items[$vendor_id]['items'][] = $item_id;
							$wcfm_store_invoice_items[$vendor_id]['products'][] = $product_id;
						}
					} else {
						$wcfm_store_invoice_items[$vendor_id]['items'][] = $item_id;
						$wcfm_store_invoice_items[$vendor_id]['products'][] = $product_id;
					}
				}
			}
		}
		
		
		if( !empty( $wcfm_store_invoice_items ) ) {
		  foreach( $wcfm_store_invoice_items as $wcfm_store_invoice_item_key => $wcfm_store_invoice_item ) {
				$vendor_id             = $wcfm_store_invoice_item_key;
				$process_item_ids      = $wcfm_store_invoice_item['items'];
				$process_product_ids   = $wcfm_store_invoice_item['products'];
				$process_shipping_items = array();
				
				if( !empty( $line_items_shipping ) ) {
					foreach ( $line_items_shipping as $shipping_item_id => $shipping_item) {
						$order_item_shipping = new WC_Order_Item_Shipping($shipping_item_id);
						$shipping_vendor_id = $order_item_shipping->get_meta('vendor_id', true);
						if( $shipping_vendor_id && ( $shipping_vendor_id == $vendor_id ) ) {
							$process_shipping_items[$shipping_item_id] = $shipping_item;
						}
					}
				}
				
				if( !$process_item_ids || !is_array( $process_item_ids ) || empty( $process_item_ids ) ) continue;
				if( !$process_product_ids || !is_array( $process_product_ids ) || empty( $process_product_ids ) ) continue;
				
				$store_invoice_path = $tmp_path . md5($vendor_id) . '/' . $cur_year . '/' . $cur_month . '/' . md5($order_id);
				if ( !wp_mkdir_p( $store_invoice_path ) ) {
					continue;
				} else {
					$store_invoice_path = trailingslashit( $store_invoice_path );
				}
				
				$wcfm_vendor_invoice_data = (array) wcfm_get_user_meta( $vendor_id, 'wcfm_vendor_invoice_options', true );
				$wcfm_vendor_last_invoice_no = get_user_meta( $vendor_id, '_wcfm_vendor_last_invoice_no', true );
				if( $wcfm_vendor_last_invoice_no ) $wcfm_vendor_last_invoice_no = absint( $wcfm_vendor_last_invoice_no );
				else $wcfm_vendor_last_invoice_no = 0;
				$wcfm_vendor_last_invoice_no++;
				$wcfm_vendor_invoice_prefix = isset( $wcfm_vendor_invoice_data['prefix'] ) ? $wcfm_vendor_invoice_data['prefix'] : '';
				$wcfm_vendor_invoice_sufix = isset( $wcfm_vendor_invoice_data['sufix'] ) ? $wcfm_vendor_invoice_data['sufix'] : '';
				$wcfm_vendor_invoice_digit = isset( $wcfm_vendor_invoice_data['digit'] ) ? absint($wcfm_vendor_invoice_data['digit']) : '';
				if( $wcfm_vendor_invoice_digit ) {
					$wcfm_vendor_invoice_id = sprintf( '%0'.$wcfm_vendor_invoice_digit.'u', $wcfm_vendor_last_invoice_no );
				} else {
					$wcfm_vendor_invoice_id = sprintf( '%06u', $wcfm_vendor_last_invoice_no );
				}
				$wcfm_vendor_invoice_id = $wcfm_vendor_invoice_prefix . $wcfm_vendor_invoice_id . $wcfm_vendor_invoice_sufix;
				
				$filename = __( 'invoice', 'wc-frontend-manager-ultimate' ) . '-' . $wcfm_vendor_invoice_id . '.pdf';
					
				try {
					// Fetching Main template
					if( is_rtl() ) {
						$template = $WCFMu->template->locate_template( 'vendor_invoice/store-invoice-rtl.php' );
					} else {
						$template = $WCFMu->template->locate_template( 'vendor_invoice/store-invoice.php' );
					}
					ob_start();
					if (file_exists($template)) {
						include($template);
					}
					$output_body = ob_get_clean();
			
					// Fetching tempplate wrapper
					$template_wrapper = $WCFMu->template->locate_template( 'vendor_invoice/html-document-wrapper.php' );
					ob_start();
					if (file_exists($template_wrapper)) {
						include($template_wrapper);
					}
					$complete_document = ob_get_clean();
					
					// Try to clean up a bit of memory
					unset($output_body);
					
					// clean up special characters
					$complete_document = utf8_decode(mb_convert_encoding($complete_document, 'HTML-ENTITIES', 'UTF-8'));
					
					$pdf_maker = wcpdf_get_pdf_maker( $complete_document, $invoice_settings );
					$pdf_data = $pdf_maker->output();
					
					do_action( 'wpo_wcpdf_after_pdf', $document_type, $document );
					
					$pdf_path = $store_invoice_path . $filename;
					file_put_contents ( $pdf_path, $pdf_data );
					$attachments[] = $pdf_path;
					$wcfm_store_invoices[$vendor_id] = esc_sql($pdf_path);
					$wcfm_store_invoice_ids[$vendor_id] = $wcfm_vendor_invoice_id;
					update_user_meta( $vendor_id, '_wcfm_vendor_last_invoice_no', $wcfm_vendor_last_invoice_no );
					
					// Try to clean up a bit of memory
					unset($complete_document);
				} catch (Exception $e) {
					error_log($e->getMessage());
					continue;
				}
			}
			update_post_meta( $order_id, '_wcfm_store_invoices', $wcfm_store_invoices );
			update_post_meta( $order_id, '_wcfm_store_invoice_ids', $wcfm_store_invoice_ids );
		} else {
			return $attachments;
		}
		
		remove_filter( 'wcpdf_disable_deprecation_notices', '__return_true' );
		
		return $attachments;
	}
	
	function wcfmu_membership_subscription_invoice( $attachments, $membership_id, $member_id, $is_register_member = false ) {
		global $WCFM, $WCFMu, $wpo_wcpdf, $document, $document_type, $vendor_id, $wcfm_membership_id, $wcfm_register_member;
		
		if ( !apply_filters( 'wcfm_is_allow_membership_subscription_invoice', true, $membership_id, $member_id ) ) {
			return $attachments;
		}
		
		$vendor_id            = $member_id;
		$wcfm_membership_id   = $membership_id;
		$wcfm_register_member = $is_register_member;
		$document_type      = 'packing-slip';
		
		// disable deprecation notices during email sending
		add_filter( 'wcpdf_disable_deprecation_notices', '__return_true' );
		
		// reload translations because WC may have switched to site locale (by setting the plugin_locale filter to site locale in wc_switch_to_site_locale())
		WPO_WCPDF()->translations();
		do_action( 'wpo_wcpdf_reload_attachment_translations' );
		
		// prepare document
		$document = wcpdf_get_document( $document_type, (array) $member_id, true );
		if( !$document ) return;
		
		do_action( 'wpo_wcpdf_process_template', $document_type, $document );
		
		do_action( 'wpo_wcpdf_before_pdf', $document_type, $document );
		
		$upload_dir = wp_upload_dir();
		if (!empty($upload_dir['error'])) {
			$tmp_path = false;
		} else {
			$upload_base = trailingslashit( $upload_dir['basedir'] );
			$tmp_path = $upload_base . 'wcfm/vendor_invoice/';
		}

		$tmp_path = apply_filters( 'wcfm_vendor_invoice_tmp_path', $tmp_path );
		if ($tmp_path !== false) {
			$tmp_path = trailingslashit( $tmp_path );
			if ( !wp_mkdir_p( $tmp_path ) ) {
				return $attachments;
			}
		} else {
			return $attachments;
		}
		
		$membership_invoice_path = $tmp_path . md5($vendor_id);
		if ( !wp_mkdir_p( $membership_invoice_path ) ) {
			return $attachments;
		} else {
			$membership_invoice_path = trailingslashit( $membership_invoice_path );
		}
		
		$filename = __( 'membership-invoice', 'wc-frontend-manager-ultimate' ) . '-' . $membership_id . '.pdf';
		
		$invoice_settings = array(
			'paper_size'		=> 'A4',
			'paper_orientation'	=> 'portrait',
		);
		
		try {
			// Fetching Main template
			if( is_rtl() ) {
				$template = $WCFMu->template->locate_template( 'vendor_invoice/membership-invoice.php' );
			} else {
				$template = $WCFMu->template->locate_template( 'vendor_invoice/membership-invoice.php' );
			}
			ob_start();
			if (file_exists($template)) {
				include($template);
			}
			$output_body = ob_get_clean();
	
			// Fetching tempplate wrapper
			$template_wrapper = $WCFMu->template->locate_template( 'vendor_invoice/html-document-wrapper.php' );
			ob_start();
			if (file_exists($template_wrapper)) {
				include($template_wrapper);
			}
			$complete_document = ob_get_clean();
			
			// Try to clean up a bit of memory
			unset($output_body);
			
			// clean up special characters
			$complete_document = utf8_decode(mb_convert_encoding($complete_document, 'HTML-ENTITIES', 'UTF-8'));
			
			$pdf_maker = wcpdf_get_pdf_maker( $complete_document, $invoice_settings );
			$pdf_data = $pdf_maker->output();
			
			do_action( 'wpo_wcpdf_after_pdf', $document_type, $document );
					
			$pdf_path = $membership_invoice_path . $filename;
			file_put_contents ( $pdf_path, $pdf_data );
			if( !$attachments ) $attachments = array();
			$attachments[] = $pdf_path;
			
			// Try to clean up a bit of memory
			unset($complete_document);
		} catch (Exception $e) {
			error_log($e->getMessage());
		}
		
		return $attachments;
	}
	
	/**
	 * WooCommerce PDF Invoices & Packing Slips Support
	 * Download PDF Invoice
	 */
	function wcfm_order_pdf_invoice() {
		global $WCFM, $WCFMu, $wpo_wcpdf, $order, $order_id, $vendor_id, $document, $document_type, $plugin_path, $plugin_url;
		
  	$order_id  = $_GET['order_id'];
  	$vendor_id = isset( $_GET['vendor_id'] ) ? $_GET['vendor_id'] : apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );
		$document_type = 'invoice';
		
		if( !wcfm_is_vendor() && wcfm_is_vendor( $vendor_id ) ) {
			$WCFM->load_class( 'wcfmmarketplace' );
			$WCFM->wcfm_marketplace = new WCFM_Marketplace();
			$WCFM->wcfm_marketplace->vendor_id = $vendor_id;
		}
		
		$plugin_path = $WCFMu->plugin_path;
		$plugin_url = $WCFMu->plugin_url;
		
		// disable deprecation notices during email sending
		add_filter( 'wcpdf_disable_deprecation_notices', '__return_true' );
		
		// WooCommerce Cost of Good Fix
		if ( class_exists( 'Alg_WC_Cost_of_Goods_Core' ) ) {
			remove_all_actions( 'woocommerce_before_order_itemmeta' );
		}
		
		// Process Template
		$order = wc_get_order( $order_id );
		
		// reload translations because WC may have switched to site locale (by setting the plugin_locale filter to site locale in wc_switch_to_site_locale())
		WPO_WCPDF()->translations();
		do_action( 'wpo_wcpdf_reload_attachment_translations' );
		
		// prepare document
		$document = wcpdf_get_document( $document_type, (array) $order_id, true );
		if( !$document ) return;
		
		do_action( 'wpo_wcpdf_process_template', $document_type, $document );
		
		do_action( 'wpo_wcpdf_before_pdf', $document_type, $document );
		
		// Fetching Main template
		if( is_rtl() ) {
			$template = $WCFMu->template->locate_template( 'vendor_invoice/commission-invoice-rtl.php' );
		} else {
			$template = $WCFMu->template->locate_template( 'vendor_invoice/commission-invoice.php' );
		}
		ob_start();
		if (file_exists($template)) {
			include($template);
		}
		$output_body = ob_get_clean();

		// Fetching tempplate wrapper
		$template_wrapper = $WCFMu->template->locate_template( 'vendor_invoice/html-document-wrapper.php' );
		ob_start();
		if (file_exists($template_wrapper)) {
			include($template_wrapper);
		}
		$complete_document = ob_get_clean();
		
		// Try to clean up a bit of memory
		unset($output_body);
		
		// clean up special characters
		$complete_document = utf8_decode(mb_convert_encoding($complete_document, 'HTML-ENTITIES', 'UTF-8'));
		
		$invoice_settings = array(
			'paper_size'		    => 'A4',
			'paper_orientation'	=> 'portrait',
			'font_subsetting'	  => true
		);
		
		$pdf_maker = wcpdf_get_pdf_maker( $complete_document, $invoice_settings );
		$pdf = $pdf_maker->output();
		
		do_action( 'wpo_wcpdf_after_pdf', $document_type, $document );
		
		$filename = __( 'invoice', 'wc-frontend-manager-ultimate' ) . '-' . $order_id . '.pdf';

		do_action( 'wpo_wcpdf_created_manually', $pdf, $filename );

		// Get output setting
		$output_mode = 'download'; //isset($general_settings['download_display']) ? $general_settings['download_display'] : '';

		// Set PDF output header 
		wcpdf_pdf_headers( $filename, $output_mode, $pdf );

		// output PDF data
		echo($pdf);
  	
		die;
	}
	
	/**
	 * WooCommerce PDF Invoices & Packing Slips Support
	 * Download PDF Packing Slip
	 */
	function wcfm_order_pdf_packing_slip() {
		global $WCFM, $WCFMu, $wpo_wcpdf, $order, $order_id, $document, $document_type;
		
  	$order_id = $_GET['order_id'];
		$document_type = 'packing-slip';
		
		// disable deprecation notices during email sending
		add_filter( 'wcpdf_disable_deprecation_notices', '__return_true' );
		
		// WooCommerce Cost of Good Fix
		if ( class_exists( 'Alg_WC_Cost_of_Goods_Core' ) ) {
			remove_all_actions( 'woocommerce_before_order_itemmeta' );
		}
		
		// Process Template
		$order = wc_get_order( $order_id );
		
		// reload translations because WC may have switched to site locale (by setting the plugin_locale filter to site locale in wc_switch_to_site_locale())
		WPO_WCPDF()->translations();
		do_action( 'wpo_wcpdf_reload_attachment_translations' );
		
		// prepare document
		$document = wcpdf_get_document( $document_type, (array) $order_id, true );
		if( !$document ) return;
		
		do_action( 'wpo_wcpdf_process_template', $document_type, $document );
		
		do_action( 'wpo_wcpdf_before_pdf', $document_type, $document );
		
		// Fetching Main template
		if( is_rtl() ) {
			$template = $WCFMu->template->locate_template( 'vendor_invoice/packing-slip-rtl.php' );
		} else {
			$template = $WCFMu->template->locate_template( 'vendor_invoice/packing-slip.php' );
		}
		ob_start();
		if (file_exists($template)) {
			include($template);
		}
		$output_body = ob_get_clean();

		// Fetching tempplate wrapper
		$template_wrapper = $WCFMu->template->locate_template( 'vendor_invoice/html-document-wrapper.php' );
		ob_start();
		if (file_exists($template_wrapper)) {
			include($template_wrapper);
		}
		$complete_document = ob_get_clean();
		
		// Try to clean up a bit of memory
		unset($output_body);
		
		// clean up special characters
		$complete_document = utf8_decode(mb_convert_encoding($complete_document, 'HTML-ENTITIES', 'UTF-8'));
		
		$invoice_settings = array(
			'paper_size'		=> 'A4',
			'paper_orientation'	=> 'portrait',
		);
		
		$pdf_maker = wcpdf_get_pdf_maker( $complete_document, $invoice_settings );
		$pdf = $pdf_maker->output();
		
		do_action( 'wpo_wcpdf_after_pdf', $document_type, $document );
		
		$filename = __( 'packing-slip', 'wc-frontend-manager-ultimate' ) . '-' . $order_id . '.pdf';

		do_action( 'wpo_wcpdf_created_manually', $pdf, $filename );

		// Get output setting
		$output_mode = 'download'; //isset($general_settings['download_display']) ? $general_settings['download_display'] : '';

		// Set PDF output header
		wcpdf_pdf_headers( $filename, $output_mode, $pdf );

		// output PDF data
		echo($pdf);
  	
		die;
	}
	
	/**
	 * Store Invoice ID
	 */
	function wcfm_store_payment_invoice() {
		global $WCFM, $WCFMu, $wpo_wcpdf, $withdrawal_id, $document, $document_type;
		
  	$withdrawal_id = $_GET['withdraw_id'];
		$document_type = 'packing-slip';
		
		// disable deprecation notices during email sending
		add_filter( 'wcpdf_disable_deprecation_notices', '__return_true' );
		
		// reload translations because WC may have switched to site locale (by setting the plugin_locale filter to site locale in wc_switch_to_site_locale())
		WPO_WCPDF()->translations();
		do_action( 'wpo_wcpdf_reload_attachment_translations' );
		
		// prepare document
		$document = wcpdf_get_document( $document_type, (array) $withdrawal_id, true );
		if( !$document ) return;
		
		do_action( 'wpo_wcpdf_process_template', $document_type, $document );
		
		do_action( 'wpo_wcpdf_bfore_pdf', $document_type, $document );
		
		// Fetching Main template
		if( is_rtl() ) {
			$template = $WCFMu->template->locate_template( 'vendor_invoice/withdrawal-invoice.php' );
		} else {
			$template = $WCFMu->template->locate_template( 'vendor_invoice/withdrawal-invoice.php' );
		}
		ob_start();
		if (file_exists($template)) {
			include($template);
		}
		$output_body = ob_get_clean();

		// Fetching tempplate wrapper
		$template_wrapper = $WCFMu->template->locate_template( 'vendor_invoice/html-document-wrapper.php' );
		ob_start();
		if (file_exists($template_wrapper)) {
			include($template_wrapper);
		}
		$complete_document = ob_get_clean();
		
		// Try to clean up a bit of memory
		unset($output_body);
		
		// clean up special characters
		$complete_document = utf8_decode(mb_convert_encoding($complete_document, 'HTML-ENTITIES', 'UTF-8'));
		
		$invoice_settings = array(
			'paper_size'		=> 'A4',
			'paper_orientation'	=> 'portrait',
		);
		
		$pdf_maker = wcpdf_get_pdf_maker( $complete_document, $invoice_settings );
		$pdf = $pdf_maker->output();
		
		do_action( 'wpo_wcpdf_after_pdf', $document_type, $document );
		
		$filename = __( 'withdrawal-invoice', 'wc-frontend-manager-ultimate' ) . '-' . sprintf( '%06u', $withdrawal_id ) . '.pdf';

		do_action( 'wpo_wcpdf_created_manually', $pdf, $filename );

		// Get output setting
		$output_mode = 'download'; //isset($general_settings['download_display']) ? $general_settings['download_display'] : '';

		// Set PDF output header
		wcpdf_pdf_headers( $filename, $output_mode, $pdf );

		// output PDF data
		echo($pdf);
  	
		die;
	}
	
	/**
	 * Output template styles
	 */
	public function invoice_styles() {
		global $WCFM, $WCFMu;
		
		if( is_rtl() ) {
			$css = $WCFMu->template->locate_template( 'vendor_invoice/style-rtl.css' );
		} else {
			$css = $WCFMu->template->locate_template( 'vendor_invoice/style.css' );
		}

		ob_start();
		if (file_exists($css)) {
			include($css);
		}
		$css = ob_get_clean();
		$css = apply_filters( 'wcfm_pdf_invoice_styles', $css );
		
		echo $css;
	}
	
	/**
	 * My Account Store Invoice Download Option
	 */
	function wcfm_my_account_store_order_pdf_invoice_download( $actions, $order ) {
  	global $WCFM, $WCFMu, $WCFMmp;
  	
  	if( !apply_filters( 'wcfm_is_allow_my_account_store_order_pdf_invoice_download', true ) ) return $actions;
  	
  	$order_status = sanitize_title( $order->get_status() );
  	if( in_array( $order_status, apply_filters( 'wcfm_pdf_invoice_download_disable_order_status', array( 'failed', 'cancelled', 'request', 'proposal', 'proposal-sent', 'proposal-expired', 'proposal-rejected', 'proposal-canceled', 'proposal-accepted' ) ) ) ) return $actions;
  	
  	$wcfm_store_invoices = get_post_meta( $order->get_id(), '_wcfm_store_invoices', true );
  	if( $wcfm_store_invoices  && is_array( $wcfm_store_invoices ) ) {
  		$upload_dir = wp_upload_dir();
  		foreach( $wcfm_store_invoices as $vendor_id => $wcfm_store_invoice ) {
				if( file_exists( $wcfm_store_invoice ) ) {
					if (empty($upload_dir['error'])) {
						$upload_base = trailingslashit( $upload_dir['basedir'] );
						$upload_url = trailingslashit( $upload_dir['baseurl'] );
						$invoice_path = str_replace( $upload_base, $upload_url, $wcfm_store_invoice );
						
						$sold_by_text = __( 'Store', 'wc-frontend-manager-ultimate' );
						if( $WCFMmp ) {
							$sold_by_text = $WCFMmp->wcfmmp_vendor->sold_by_label( absint($vendor_id) );
						}
						$actions['wcfm-store-invoice-'.$vendor_id] = array( 'name' => apply_filters( 'wcfm_store_invoice_download_label', wcfm_get_vendor_store_name( absint($vendor_id) ) . ' ' . $sold_by_text . ' ' . __( 'Invoice', 'wc-frontend-manager-ultimate' ), $order->get_id(), $vendor_id ), 'url' => $invoice_path );
					}
				}
			}
		}
  	return $actions;
  }
}