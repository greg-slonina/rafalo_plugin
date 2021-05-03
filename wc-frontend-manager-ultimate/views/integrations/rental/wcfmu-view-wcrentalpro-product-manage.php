<?php
/**
 * WCFM plugin views
 *
 * Plugin WC Rental Pro Products Manage Views
 *
 * @author 		WC Lovers
 * @package 	wcfmu/views/thirdparty
 * @version   2.3.7
 */
global $wp, $WCFM, $WCFMu;

$pricing_type = '';
$wcfm_redq_quantity = 1;
$perkilo_price = '';
$hourly_price = '';
$general_price = '';

$redq_daily_pricing = array();
$friday_price = '';
$saturday_price = '';
$sunday_price = '';
$monday_price = '';
$tuesday_price = '';
$wednesday_price = '';
$thursday_price = '';

$redq_monthly_pricing = array();
$january_price = '';
$february_price = '';
$march_price = '';
$april_price = '';
$may_price = '';
$june_price = '';
$july_price = '';
$august_price = '';
$september_price = '';
$october_price = '';
$november_price = '';
$december_price = '';


$redq_day_ranges_cost = array();

$redq_price_discount_cost = array();

// Show/Hide
$rnb_settings_for_display = 'global';
$redq_rental_local_show_pickup_date = 'open';
$redq_rental_local_show_pickup_time = 'open';
$redq_rental_local_show_dropoff_date = 'open';
$redq_rental_local_show_dropoff_time = 'open';
$redq_rental_local_show_pricing_flip_box = 'open';
$redq_rental_local_show_price_discount_on_days = 'open';
$redq_rental_local_show_price_instance_payment = 'open';
$redq_rental_local_show_request_quote = 'closed';
$redq_rental_local_show_book_now = 'open';

// Titles
$rnb_settings_for_labels = 'global';
$redq_show_pricing_flipbox_text = '';
$redq_flip_pricing_plan_text = '';
$redq_pickup_location_heading_title = '';
$redq_dropoff_location_heading_title = '';
$redq_pickup_date_heading_title = '';
$redq_pickup_date_placeholder = '';
$redq_pickup_time_placeholder = '';
$redq_dropoff_date_heading_title = '';
$redq_dropoff_date_placeholder = '';
$redq_dropoff_time_placeholder = '';
$redq_rnb_cat_heading = '';
$redq_resources_heading_title = '';
$redq_adults_heading_title = '';
$redq_adults_placeholder = '';
$redq_childs_heading_title = '';
$redq_childs_placeholder = '';
$redq_security_deposite_heading_title = '';
$redq_discount_text_title = '';
$redq_instance_pay_text_title = '';
$redq_total_cost_text_title = '';
$redq_book_now_button_text = '';
$redq_rfq_button_text = '';

// Logical
$rnb_settings_for_conditions = 'global';
$block_rental_dates = 'yes';
$choose_date_format = 'm/d/y';
$max_time_late = '';
$redq_rental_local_enable_single_day_time_based_booking = 'open';
$redq_max_rental_days = '';
$redq_min_rental_days = '';
$redq_rental_starting_block_dates = '';
$redq_rental_post_booking_block_dates = '';
$redq_time_interval = '';
$redq_rental_off_days = array();

// Validation
$rnb_settings_for_validations = 'global';
$redq_rental_local_required_pickup_location = 'closed';
$redq_rental_local_required_return_location = 'closed';
$redq_rental_local_required_person = 'closed';
$redq_rental_required_local_pickup_time = 'closed';
$redq_rental_required_local_return_time = 'closed';

// Opening & Closing Times
$redq_rental_fri_min_time = '';
$redq_rental_fri_max_time = '';
$redq_rental_sat_min_time = '';
$redq_rental_sat_max_time = '';
$redq_rental_sun_min_time = '';
$redq_rental_sun_max_time = '';
$redq_rental_mon_min_time = '';
$redq_rental_mon_max_time = '';
$redq_rental_thu_min_time = '';
$redq_rental_thu_max_time = '';
$redq_rental_wed_min_time = '';
$redq_rental_wed_max_time = '';
$redq_rental_thur_min_time = '';
$redq_rental_thur_max_time = '';

if( isset( $wp->query_vars['wcfm-products-manage'] ) && !empty( $wp->query_vars['wcfm-products-manage'] ) ) {
	$product_id = $wp->query_vars['wcfm-products-manage'];
	if( $product_id ) {
		$pricing_type = get_post_meta( $product_id, 'pricing_type', true );
		$wcfm_redq_quantity = get_post_meta( $product_id, 'wcfm_redq_quantity', true );
		$perkilo_price = get_post_meta( $product_id, 'perkilo_price', true );
		$hourly_price = get_post_meta( $product_id, 'hourly_price', true );
		$general_price = get_post_meta( $product_id, 'general_price', true );
		
		$redq_daily_pricing = (array) get_post_meta( $product_id, 'redq_daily_pricing', true );
		$friday_price = isset( $redq_daily_pricing['friday'] ) ? $redq_daily_pricing['friday'] : '';
		$saturday_price = isset( $redq_daily_pricing['saturday'] ) ? $redq_daily_pricing['saturday'] : '';
		$sunday_price = isset( $redq_daily_pricing['sunday'] ) ? $redq_daily_pricing['sunday'] : '';
		$monday_price = isset( $redq_daily_pricing['monday'] ) ? $redq_daily_pricing['monday'] : '';
		$tuesday_price = isset( $redq_daily_pricing['tuesday'] ) ? $redq_daily_pricing['tuesday'] : '';
		$wednesday_price = isset( $redq_daily_pricing['wednesday'] ) ? $redq_daily_pricing['wednesday'] : '';
		$thursday_price = isset( $redq_daily_pricing['thursday'] ) ? $redq_daily_pricing['thursday'] : '';
		
		$redq_monthly_pricing = (array) get_post_meta( $product_id, 'redq_monthly_pricing', true );
		$january_price = isset( $redq_monthly_pricing['january'] ) ? $redq_monthly_pricing['january'] : '';
		$february_price = isset( $redq_monthly_pricing['february'] ) ? $redq_monthly_pricing['february'] : '';
		$march_price = isset( $redq_monthly_pricing['march'] ) ? $redq_monthly_pricing['march'] : '';
		$april_price = isset( $redq_monthly_pricing['april'] ) ? $redq_monthly_pricing['april'] : '';
		$may_price = isset( $redq_monthly_pricing['may'] ) ? $redq_monthly_pricing['may'] : '';
		$june_price = isset( $redq_monthly_pricing['june'] ) ? $redq_monthly_pricing['june'] : '';
		$july_price = isset( $redq_monthly_pricing['july'] ) ? $redq_monthly_pricing['july'] : '';
		$august_price = isset( $redq_monthly_pricing['august'] ) ? $redq_monthly_pricing['august'] : '';
		$september_price = isset( $redq_monthly_pricing['september'] ) ? $redq_monthly_pricing['september'] : '';
		$october_price = isset( $redq_monthly_pricing['october'] ) ? $redq_monthly_pricing['october'] : '';
		$november_price = isset( $redq_monthly_pricing['november'] ) ? $redq_monthly_pricing['november'] : '';
		$december_price = isset( $redq_monthly_pricing['december'] ) ? $redq_monthly_pricing['december'] : '';
		
		$redq_day_ranges_cost = (array) get_post_meta( $product_id, 'redq_day_ranges_cost', true );
		
		$redq_price_discount_cost = (array) get_post_meta( $product_id, 'redq_price_discount_cost', true );
		
		// Show/Hide
		$rnb_settings_for_display = get_post_meta( $product_id, 'rnb_settings_for_display', true );
		$redq_rental_local_show_pickup_date = get_post_meta( $product_id, 'redq_rental_local_show_pickup_date', true ) ? get_post_meta( $product_id, 'redq_rental_local_show_pickup_date', true ) : 'open';
		$redq_rental_local_show_pickup_time = get_post_meta( $product_id, 'redq_rental_local_show_pickup_time', true ) ? get_post_meta( $product_id, 'redq_rental_local_show_pickup_time', true ) : 'open';
		$redq_rental_local_show_dropoff_date = get_post_meta( $product_id, 'redq_rental_local_show_dropoff_date', true ) ? get_post_meta( $product_id, 'redq_rental_local_show_dropoff_date', true ) : 'open';
		$redq_rental_local_show_dropoff_time = get_post_meta( $product_id, 'redq_rental_local_show_dropoff_time', true ) ? get_post_meta( $product_id, 'redq_rental_local_show_dropoff_time', true ) : 'open';
		$redq_rental_local_show_pricing_flip_box = get_post_meta( $product_id, 'redq_rental_local_show_pricing_flip_box', true ) ? get_post_meta( $product_id, 'redq_rental_local_show_pricing_flip_box', true ) : 'open';
		$redq_rental_local_show_price_discount_on_days = get_post_meta( $product_id, 'redq_rental_local_show_price_discount_on_days', true ) ? get_post_meta( $product_id, 'redq_rental_local_show_price_discount_on_days', true ) : 'open';
		$redq_rental_local_show_price_instance_payment = get_post_meta( $product_id, 'redq_rental_local_show_price_instance_payment', true ) ? get_post_meta( $product_id, 'redq_rental_local_show_price_instance_payment', true ) : 'open';
		$redq_rental_local_show_request_quote = get_post_meta( $product_id, 'redq_rental_local_show_request_quote', true ) ? get_post_meta( $product_id, 'redq_rental_local_show_request_quote', true ) : 'closed';
		$redq_rental_local_show_book_now = get_post_meta( $product_id, 'redq_rental_local_show_book_now', true ) ? get_post_meta( $product_id, 'redq_rental_local_show_book_now', true ) : 'open';
		
		// Titles
		$rnb_settings_for_labels = get_post_meta( $product_id, 'rnb_settings_for_labels', true );
		$redq_show_pricing_flipbox_text = get_post_meta( $product_id, 'redq_show_pricing_flipbox_text', true );
		$redq_flip_pricing_plan_text = get_post_meta( $product_id, 'redq_flip_pricing_plan_text', true );
		$redq_pickup_location_heading_title = get_post_meta( $product_id, 'redq_pickup_location_heading_title', true );
		$redq_dropoff_location_heading_title = get_post_meta( $product_id, 'redq_dropoff_location_heading_title', true );
		$redq_pickup_date_heading_title = get_post_meta( $product_id, 'redq_pickup_date_heading_title', true );
		$redq_pickup_date_placeholder = get_post_meta( $product_id, 'redq_pickup_date_placeholder', true );
		$redq_pickup_time_placeholder = get_post_meta( $product_id, 'redq_pickup_time_placeholder', true );
		$redq_dropoff_date_heading_title = get_post_meta( $product_id, 'redq_dropoff_date_heading_title', true );
		$redq_dropoff_date_placeholder = get_post_meta( $product_id, 'redq_dropoff_date_placeholder', true );
		$redq_dropoff_time_placeholder = get_post_meta( $product_id, 'redq_dropoff_time_placeholder', true );
		$redq_rnb_cat_heading = get_post_meta( $product_id, 'redq_rnb_cat_heading', true );
		$redq_resources_heading_title = get_post_meta( $product_id, 'redq_resources_heading_title', true );
		$redq_adults_heading_title = get_post_meta( $product_id, 'redq_adults_heading_title', true );
		$redq_adults_placeholder = get_post_meta( $product_id, 'redq_adults_placeholder', true );
		$redq_childs_heading_title = get_post_meta( $product_id, 'redq_childs_heading_title', true );
		$redq_childs_placeholder = get_post_meta( $product_id, 'redq_childs_placeholder', true );
		$redq_security_deposite_heading_title = get_post_meta( $product_id, 'redq_security_deposite_heading_title', true );
		$redq_discount_text_title = get_post_meta( $product_id, 'redq_discount_text_title', true );
		$redq_instance_pay_text_title = get_post_meta( $product_id, 'redq_instance_pay_text_title', true );
		$redq_total_cost_text_title = get_post_meta( $product_id, 'redq_total_cost_text_title', true );
		$redq_book_now_button_text = get_post_meta( $product_id, 'redq_book_now_button_text', true );
		$redq_rfq_button_text = get_post_meta( $product_id, 'redq_rfq_button_text', true );
		
		// Logical
		$rnb_settings_for_conditions = get_post_meta( $product_id, 'rnb_settings_for_conditions', true );
		$block_rental_dates = get_post_meta( $product_id, 'redq_block_general_dates', true );
		$choose_date_format = get_post_meta( $product_id, 'redq_calendar_date_format', true );
		$max_time_late = get_post_meta( $product_id, 'redq_max_time_late', true );
		$redq_rental_local_enable_single_day_time_based_booking = get_post_meta( $product_id, 'redq_rental_local_enable_single_day_time_based_booking', true ) ? get_post_meta( $product_id, 'redq_rental_local_enable_single_day_time_based_booking', true ) : 'open';
		$redq_max_rental_days = get_post_meta( $product_id, 'redq_max_rental_days', true );
		$redq_min_rental_days = get_post_meta( $product_id, 'redq_min_rental_days', true );
		$redq_rental_starting_block_dates = get_post_meta( $product_id, 'redq_rental_starting_block_dates', true );
		$redq_rental_post_booking_block_dates = get_post_meta( $product_id, 'redq_rental_post_booking_block_dates', true );
		$redq_time_interval = get_post_meta( $product_id, 'redq_time_interval', true );
		$redq_rental_off_days = get_post_meta( $product_id, 'redq_rental_off_days', true );
		if( !$redq_rental_off_days ) $redq_rental_off_days = array();
		
		// Validation
		$rnb_settings_for_validations = get_post_meta( $product_id, 'rnb_settings_for_validations', true );
		$redq_rental_local_required_pickup_location = get_post_meta( $product_id, 'redq_rental_local_required_pickup_location', true ) ? get_post_meta( $product_id, 'redq_rental_local_required_pickup_location', true ) : 'closed';
		$redq_rental_local_required_return_location = get_post_meta( $product_id, 'redq_rental_local_required_return_location', true ) ? get_post_meta( $product_id, 'redq_rental_local_required_return_location', true ) : 'closed';
		$redq_rental_local_required_person = get_post_meta( $product_id, 'redq_rental_local_required_person', true ) ? get_post_meta( $product_id, 'redq_rental_local_required_person', true ) : 'closed';
		$redq_rental_required_local_pickup_time = get_post_meta( $product_id, 'redq_rental_required_local_pickup_time', true ) ? get_post_meta( $product_id, 'redq_rental_required_local_pickup_time', true ) : 'closed';
		$redq_rental_required_local_return_time = get_post_meta( $product_id, 'redq_rental_required_local_return_time', true ) ? get_post_meta( $product_id, 'redq_rental_required_local_return_time', true ) : 'closed';
		
		// Opening & Closing Times
		$redq_rental_fri_min_time = get_post_meta( $product_id, 'redq_rental_fri_min_time', true );
		$redq_rental_fri_max_time = get_post_meta( $product_id, 'redq_rental_fri_max_time', true );
		$redq_rental_sat_min_time = get_post_meta( $product_id, 'redq_rental_sat_min_time', true );
		$redq_rental_sat_max_time = get_post_meta( $product_id, 'redq_rental_sat_max_time', true );
		$redq_rental_sun_min_time = get_post_meta( $product_id, 'redq_rental_sun_min_time', true );
		$redq_rental_sun_max_time = get_post_meta( $product_id, 'redq_rental_sun_max_time', true );
		$redq_rental_mon_min_time = get_post_meta( $product_id, 'redq_rental_mon_min_time', true );
		$redq_rental_mon_max_time = get_post_meta( $product_id, 'redq_rental_mon_max_time', true );
		$redq_rental_thu_min_time = get_post_meta( $product_id, 'redq_rental_thu_min_time', true );
		$redq_rental_thu_max_time = get_post_meta( $product_id, 'redq_rental_thu_max_time', true );
		$redq_rental_wed_min_time = get_post_meta( $product_id, 'redq_rental_wed_min_time', true );
		$redq_rental_wed_max_time = get_post_meta( $product_id, 'redq_rental_wed_max_time', true );
		$redq_rental_thur_min_time = get_post_meta( $product_id, 'redq_rental_thur_min_time', true );
		$redq_rental_thur_max_time = get_post_meta( $product_id, 'redq_rental_thur_max_time', true );
	}
}

?>

<div class="page_collapsible products_manage_redq_rental redq_rental non-variable-subscription" id="wcfm_products_manage_form_redq_rental_head"><label class="wcfmfa fa-taxi"></label><?php _e('Rental', 'wc-frontend-manager-ultimate'); ?><span></span></div>
<div class="wcfm-container redq_rental non-variable-subscription">
	<div id="wcfm_products_manage_form_redq_rental_expander" class="wcfm-content">
		<?php
		$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_redq_rental_fields_general', array( 
			"pricing_type" => array( 'label' => __('Set Price Type', 'redq-rental') , 'type' => 'select', 'options' => apply_filters( 'wcfm_redq_rental_pricing_options', array( 'general_pricing' => __( 'General Pricing', 'redq-rental' ), 'daily_pricing' => __( 'Daily Pricing', 'redq-rental' ), 'monthly_pricing' => __( 'Monthly Pricing', 'redq-rental' ), 'days_range' => __( 'Days Range Pricing', 'redq-rental' ) ) ), 'class' => 'wcfm-select wcfm_ele redq_rental', 'label_class' => 'wcfm_title redq_rental', 'value' => $pricing_type, 'hints' => __( 'Choose a price type - this controls the schema.', 'redq-rental' ) ),
			"wcfm_redq_quantity" => array( 'label' => __('Set Quantity', 'redq-rental'), 'type' => 'number', 'class' => 'wcfm-text wcfm_ele redq_rental', 'label_class' => 'wcfm_title redq_rental', 'value' => $wcfm_redq_quantity, 'placeholder' => __( 'Add inventory quantity', 'redq-rental' ) ),
			"perkilo_price" => array( 'label' => __('Per Kilometer Price', 'redq-rental') . '(' . get_woocommerce_currency_symbol() . ')' , 'type' => 'number', 'class' => 'wcfm-text wcfm_ele redq_rental', 'label_class' => 'wcfm_title redq_rental', 'value' => $perkilo_price, 'hints' => __( 'If you select booking layout two then for location price it will be applied', 'redq-rental' ), 'placeholder' => __( 'Per Kilometer Price', 'redq-rental' ) ),
			"hourly_price" => array( 'label' => __('Hourly Price', 'wc-frontend-manager-ultimate') . '(' . get_woocommerce_currency_symbol() . ')' , 'type' => 'number', 'class' => 'wcfm-text wcfm_ele redq_rental', 'label_class' => 'wcfm_title redq_rental', 'value' => $hourly_price, 'hints' => __( 'Hourly price will be applicabe if booking or rental days min 1day', 'redq-rental' ), 'placeholder' => __( 'Enter price here', 'redq-rental' ) ),
			"general_price" => array( 'label' => __('General Price', 'wc-frontend-manager-ultimate') . '(' . get_woocommerce_currency_symbol() . ')' , 'type' => 'number', 'class' => 'wcfm-text rentel_pricing rental_general_pricing redq_rental', 'label_class' => 'wcfm_title rentel_pricing rental_general_pricing redq_rental', 'value' => $general_price, 'placeholder' => __( 'Enter price here', 'redq-rental' ) ),
			
			"friday_price" => array( 'label' => __('Friday Price', 'redq-rental') . '(' . get_woocommerce_currency_symbol() . ')', 'name' => 'redq_daily_pricing[friday]', 'type' => 'number', 'class' => 'wcfm-text rentel_pricing rental_daily_pricing redq_rental', 'label_class' => 'wcfm_title rentel_pricing rental_daily_pricing redq_rental', 'value' => $friday_price, 'placeholder' => __( 'Enter price here', 'redq-rental' ) ),
			"saturday_price" => array( 'label' => __('Saturday Price', 'redq-rental') . '(' . get_woocommerce_currency_symbol() . ')', 'name' => 'redq_daily_pricing[saturday]', 'type' => 'number', 'class' => 'wcfm-text rentel_pricing rental_daily_pricing redq_rental', 'label_class' => 'wcfm_title rentel_pricing rental_daily_pricing redq_rental', 'value' => $saturday_price, 'placeholder' => __( 'Enter price here', 'redq-rental' ) ),
			"sunday_price" => array( 'label' => __('Sunday Price', 'redq-rental') . '(' . get_woocommerce_currency_symbol() . ')', 'name' => 'redq_daily_pricing[sunday]', 'type' => 'number', 'class' => 'wcfm-text rentel_pricing rental_daily_pricing redq_rental', 'label_class' => 'wcfm_title rentel_pricing rental_daily_pricing redq_rental', 'value' => $sunday_price, 'placeholder' => __( 'Enter price here', 'redq-rental' ) ),
			"monday_price" => array( 'label' => __('Monday Price', 'redq-rental') . '(' . get_woocommerce_currency_symbol() . ')', 'name' => 'redq_daily_pricing[monday]', 'type' => 'number', 'class' => 'wcfm-text rentel_pricing rental_daily_pricing redq_rental', 'label_class' => 'wcfm_title rentel_pricing rental_daily_pricing redq_rental', 'value' => $monday_price, 'placeholder' => __( 'Enter price here', 'redq-rental' ) ),
			"tuesday_price" => array( 'label' => __('Tuesday Price', 'redq-rental') . '(' . get_woocommerce_currency_symbol() . ')', 'name' => 'redq_daily_pricing[tuesday]', 'type' => 'number', 'class' => 'wcfm-text rentel_pricing rental_daily_pricing redq_rental', 'label_class' => 'wcfm_title rentel_pricing rental_daily_pricing redq_rental', 'value' => $tuesday_price, 'placeholder' => __( 'Enter price here', 'redq-rental' ) ),
			"wednesday_price" => array( 'label' => __('Wednesday Price', 'redq-rental') . '(' . get_woocommerce_currency_symbol() . ')', 'name' => 'redq_daily_pricing[wednesday]', 'type' => 'number', 'class' => 'wcfm-text rentel_pricing rental_daily_pricing redq_rental', 'label_class' => 'wcfm_title rentel_pricing rental_daily_pricing redq_rental', 'value' => $wednesday_price, 'placeholder' => __( 'Enter price here', 'redq-rental' ) ),
			"thursday_price" => array( 'label' => __('Thursday Price', 'redq-rental') . '(' . get_woocommerce_currency_symbol() . ')', 'name' => 'redq_daily_pricing[thursday]', 'type' => 'number', 'class' => 'wcfm-text rentel_pricing rental_daily_pricing redq_rental', 'label_class' => 'wcfm_title rentel_pricing rental_daily_pricing redq_rental', 'value' => $thursday_price, 'placeholder' => __( 'Enter price here', 'redq-rental' ) ),
			
			"january_price" => array( 'label' => __('January Price', 'redq-rental') . '(' . get_woocommerce_currency_symbol() . ')', 'name' => 'redq_monthly_pricing[january]', 'type' => 'number', 'class' => 'wcfm-text rentel_pricing rental_monthly_pricing redq_rental', 'label_class' => 'wcfm_title rentel_pricing rental_monthly_pricing redq_rental', 'value' => $january_price, 'placeholder' => __( 'Enter price here', 'redq-rental' ) ),
			"february_price" => array( 'label' => __('February Price', 'redq-rental') . '(' . get_woocommerce_currency_symbol() . ')', 'name' => 'redq_monthly_pricing[february]', 'type' => 'number', 'class' => 'wcfm-text rentel_pricing rental_monthly_pricing redq_rental', 'label_class' => 'wcfm_title rentel_pricing rental_monthly_pricing redq_rental', 'value' => $february_price, 'placeholder' => __( 'Enter price here', 'redq-rental' ) ),
			"march_price" => array( 'label' => __('March Price', 'redq-rental') . '(' . get_woocommerce_currency_symbol() . ')', 'name' => 'redq_monthly_pricing[march]', 'type' => 'number', 'class' => 'wcfm-text rentel_pricing rental_monthly_pricing redq_rental', 'label_class' => 'wcfm_title rentel_pricing rental_monthly_pricing redq_rental', 'value' => $march_price, 'placeholder' => __( 'Enter price here', 'redq-rental' ) ),
			"april_price" => array( 'label' => __('April Price', 'redq-rental') . '(' . get_woocommerce_currency_symbol() . ')', 'name' => 'redq_monthly_pricing[april]', 'type' => 'number', 'class' => 'wcfm-text rentel_pricing rental_monthly_pricing redq_rental', 'label_class' => 'wcfm_title rentel_pricing rental_monthly_pricing redq_rental', 'value' => $april_price, 'placeholder' => __( 'Enter price here', 'redq-rental' ) ),
			"may_price" => array( 'label' => __('May Price', 'redq-rental') . '(' . get_woocommerce_currency_symbol() . ')', 'name' => 'redq_monthly_pricing[may]', 'type' => 'number', 'class' => 'wcfm-text rentel_pricing rental_monthly_pricing redq_rental', 'label_class' => 'wcfm_title rentel_pricing rental_monthly_pricing redq_rental', 'value' => $may_price, 'placeholder' => __( 'Enter price here', 'redq-rental' ) ),
			"june_price" => array( 'label' => __('June Price', 'redq-rental') . '(' . get_woocommerce_currency_symbol() . ')', 'name' => 'redq_monthly_pricing[june]', 'type' => 'number', 'class' => 'wcfm-text rentel_pricing rental_monthly_pricing redq_rental', 'label_class' => 'wcfm_title rentel_pricing rental_monthly_pricing redq_rental', 'value' => $june_price, 'placeholder' => __( 'Enter price here', 'redq-rental' ) ),
			"july_price" => array( 'label' => __('July Price', 'redq-rental') . '(' . get_woocommerce_currency_symbol() . ')', 'name' => 'redq_monthly_pricing[july]', 'type' => 'number', 'class' => 'wcfm-text rentel_pricing rental_monthly_pricing redq_rental', 'label_class' => 'wcfm_title rentel_pricing rental_monthly_pricing redq_rental', 'value' => $july_price, 'placeholder' => __( 'Enter price here', 'redq-rental' ) ),
			"august_price" => array( 'label' => __('August Price', 'redq-rental') . '(' . get_woocommerce_currency_symbol() . ')', 'name' => 'redq_monthly_pricing[august]', 'type' => 'number', 'class' => 'wcfm-text rentel_pricing rental_monthly_pricing redq_rental', 'label_class' => 'wcfm_title rentel_pricing rental_monthly_pricing redq_rental', 'value' => $august_price, 'placeholder' => __( 'Enter price here', 'redq-rental' ) ),
			"september_price" => array( 'label' => __('September Price', 'redq-rental') . '(' . get_woocommerce_currency_symbol() . ')', 'name' => 'redq_monthly_pricing[september]', 'type' => 'number', 'class' => 'wcfm-text rentel_pricing rental_monthly_pricing redq_rental', 'label_class' => 'wcfm_title rentel_pricing rental_monthly_pricing redq_rental', 'value' => $september_price, 'placeholder' => __( 'Enter price here', 'redq-rental' ) ),
			"october_price" => array( 'label' => __('October Price', 'redq-rental') . '(' . get_woocommerce_currency_symbol() . ')', 'name' => 'redq_monthly_pricing[october]', 'type' => 'number', 'class' => 'wcfm-text rentel_pricing rental_monthly_pricing redq_rental', 'label_class' => 'wcfm_title rentel_pricing rental_monthly_pricing redq_rental', 'value' => $october_price, 'placeholder' => __( 'Enter price here', 'redq-rental' ) ),
			"november_price" => array( 'label' => __('November Price', 'redq-rental') . '(' . get_woocommerce_currency_symbol() . ')', 'name' => 'redq_monthly_pricing[november]', 'type' => 'number', 'class' => 'wcfm-text rentel_pricing rental_monthly_pricing redq_rental', 'label_class' => 'wcfm_title rentel_pricing rental_monthly_pricing redq_rental', 'value' => $november_price, 'placeholder' => __( 'Enter price here', 'redq-rental' ) ),
			"december_price" => array( 'label' => __('December Price', 'redq-rental') . '(' . get_woocommerce_currency_symbol() . ')', 'name' => 'redq_monthly_pricing[december]', 'type' => 'number', 'class' => 'wcfm-text rentel_pricing rental_monthly_pricing redq_rental', 'label_class' => 'wcfm_title rentel_pricing rental_monthly_pricing redq_rental', 'value' => $december_price, 'placeholder' => __( 'Enter price here', 'redq-rental' ) ),
			
			"redq_day_ranges_cost" =>   array('label' => __('Daye Range', 'wc-frontend-manager-ultimate') , 'type' => 'multiinput', 'class' => 'wcfm-text wcfm_ele rentel_pricing rental_days_range redq_rental', 'label_class' => 'wcfm_title rentel_pricing rental_days_range redq_rental', 'value' => $redq_day_ranges_cost, 'options' => array(
									"min_days" => array('label' => __('Min Days', 'redq-rental'), 'type' => 'number', 'class' => 'wcfm-text wcfm_ele days_range_text redq_rental', 'label_class' => 'wcfm_title days_range_label redq_rental' ),
									"max_days" => array('label' => __('Max Days', 'redq-rental'), 'type' => 'number', 'class' => 'wcfm-text days_range_text redq_rental', 'label_class' => 'wcfm_title days_range_label' ),
									"range_cost" => array('label' => __('Days Range Cost', 'redq-rental') . '(' . get_woocommerce_currency_symbol() . ')', 'type' => 'text', 'class' => 'wcfm-text days_range_text redq_rental', 'label_class' => 'wcfm_title days_range_label' ),
									"cost_applicable" => array('label' => __('Applicable', 'redq-rental'), 'type' => 'select', 'options' => array( 'per_day' => __( 'Per Day', 'redq-rental' ), 'fixed' => __( 'Fixed', 'redq-rental' ) ), 'class' => 'wcfm-select days_range_text redq_rental', 'label_class' => 'wcfm_title days_range_label' ),
									)	)
			
			) ) );
		?>
	</div>
</div>
	
<div class="page_collapsible products_manage_redq_rental_discount redq_rental" id="wcfm_products_manage_form_redq_rental_discount_head"><label class="wcfmfa fa-moon"></label><?php _e('Discount', 'wc-frontend-manager-ultimate'); ?><span></span></div>
<div class="wcfm-container redq_rental">
	<div id="wcfm_products_manage_form_redq_rental_discount_expander" class="wcfm-content">
		<?php
		$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_redq_rental_fields_discount', array( 
			"redq_price_discount_cost" =>   array('label' => __('Discount depending on day length', 'wc-frontend-manager-ultimate') , 'type' => 'multiinput', 'class' => 'wcfm-text wcfm_ele redq_rental', 'label_class' => 'wcfm_title redq_rental', 'value' => $redq_price_discount_cost, 'options' => array(
										"min_days" => array('label' => __('Min Days', 'redq-rental'), 'type' => 'number', 'class' => 'wcfm-text wcfm_ele rental_discount_text redq_rental', 'label_class' => 'wcfm_title rental_discount_label redq_rental' ),
										"max_days" => array('label' => __('Max Days', 'redq-rental'), 'type' => 'number', 'class' => 'wcfm-text rental_discount_text redq_rental', 'label_class' => 'wcfm_title rental_discount_label' ),
										"discount_type" => array('label' => __('Discount Type', 'redq-rental'), 'type' => 'select', 'options' => array( 'percentage' => __( 'Percentage', 'redq-rental' ), 'fixed' => __( 'Fixed Price', 'redq-rental' ) ), 'class' => 'wcfm-select rental_discount_text redq_rental', 'label_class' => 'wcfm_title rental_discount_label' ),
										"discount_amount" => array('label' => __('Discount Amount', 'wc-frontend-manager-ultimate') . '(' . get_woocommerce_currency_symbol() . ')', 'type' => 'text', 'class' => 'wcfm-text rental_discount_text redq_rental', 'label_class' => 'wcfm_title rental_discount_label' ),
										)	)
			) ) );
		?>
	</div>
</div>

<div class="page_collapsible products_manage_redq_rental_settings redq_rental" id="wcfm_products_manage_form_redq_rental_settings_head"><label class="wcfmfa fa-cog"></label><?php _e('Settings', 'wc-frontend-manager-ultimate'); ?><span></span></div>
<div class="wcfm-container redq_rental">
	<div id="wcfm_products_manage_form_redq_rental_settings_expander" class="wcfm-content">
		<h2><?php _e( 'Display', 'redq-rental' ); ?></h2><div class="wcfm_clearfix"></div>
		<div class="redq_rental_settings_block">
			<?php
			$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_redq_rental_fields_display', array(
				"rnb_settings_for_display" => array('label' => __('Choose Settings For Display Tab', 'redq-rental'), 'type' => 'select', 'options' => array( 'global' => __( 'Global Settings', 'redq-rental' ), 'local' => __( 'Local Settings', 'redq-rental' ) ), 'class' => 'wcfm-select redq_rental_settings_type redq_rental', 'label_class' => 'wcfm_title', 'hints' => __( 'If you choose local setting then these following options will work, If you chooose Global Setting then  Global Settings  Of This Plugin will work', 'redq-rental' ), 'value' => $rnb_settings_for_display ),
			) ) );
			?>
			<div class="redq_rental_settings_block_local">
			  <?php
				$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_redq_rental_fields_display_local', array( 
					"redq_rental_local_show_pickup_date" => array( 'label' => __('Show Pickup Date', 'redq-rental') , 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele redq_rental', 'label_class' => 'wcfm_title checkbox_title redq_rental', 'value' => 'open', 'dfvalue' => $redq_rental_local_show_pickup_date ),
					"redq_rental_local_show_pickup_time" => array( 'label' => __('Show Pickup Time', 'redq-rental') , 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele redq_rental', 'label_class' => 'wcfm_title checkbox_title redq_rental', 'value' => 'open', 'dfvalue' => $redq_rental_local_show_pickup_time ),
					"redq_rental_local_show_dropoff_date" => array( 'label' => __('Show Dropoff Date', 'redq-rental') , 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele redq_rental', 'label_class' => 'wcfm_title checkbox_title redq_rental', 'value' => 'open', 'dfvalue' => $redq_rental_local_show_dropoff_date ),
					"redq_rental_local_show_dropoff_time" => array( 'label' => __('Show Dropoff Time', 'redq-rental') , 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele redq_rental', 'label_class' => 'wcfm_title checkbox_title redq_rental', 'value' => 'open', 'dfvalue' => $redq_rental_local_show_dropoff_time ),
					"redq_rental_local_show_pricing_flip_box" => array( 'label' => __('Show Pricing Flip Box', 'redq-rental') , 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele redq_rental', 'label_class' => 'wcfm_title checkbox_title redq_rental', 'value' => 'open', 'dfvalue' => $redq_rental_local_show_pricing_flip_box ),
					"redq_rental_local_show_price_discount_on_days" => array( 'label' => __('Show Price Discount', 'redq-rental') , 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele redq_rental', 'label_class' => 'wcfm_title checkbox_title redq_rental', 'value' => 'open', 'dfvalue' => $redq_rental_local_show_price_discount_on_days ),
					"redq_rental_local_show_price_instance_payment" => array( 'label' => __('Show Instance Payment', 'redq-rental') , 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele redq_rental', 'label_class' => 'wcfm_title checkbox_title redq_rental', 'value' => 'open', 'dfvalue' => $redq_rental_local_show_price_instance_payment ),
					"redq_rental_local_show_request_quote" => array( 'label' => __('Show Request Quote', 'redq-rental') , 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele redq_rental', 'label_class' => 'wcfm_title checkbox_title redq_rental', 'value' => 'open', 'dfvalue' => $redq_rental_local_show_request_quote ),
					"redq_rental_local_show_book_now" => array( 'label' => __('Show Book Now', 'redq-rental') , 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele redq_rental', 'label_class' => 'wcfm_title checkbox_title redq_rental', 'value' => 'open', 'dfvalue' => $redq_rental_local_show_book_now ),
					) ) );
				?>
			</div>
		</div>
		<div class="wcfm_clearfix"></div>
		
		<h2><?php _e( 'Labels', 'redq-rental' ); ?></h2><div class="wcfm_clearfix"></div>
		<div class="redq_rental_settings_block">
			<?php
			$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_redq_rental_fields_label', array( 
				"rnb_settings_for_labels" => array('label' => __('Choose Settings For Labels Tab', 'redq-rental'), 'type' => 'select', 'options' => array( 'global' => __( 'Global Settings', 'redq-rental' ), 'local' => __( 'Local Settings', 'redq-rental' ) ), 'class' => 'wcfm-select redq_rental_settings_type redq_rental', 'label_class' => 'wcfm_title', 'hints' => __( 'If you choose local setting then these following options will work, If you chooose Global Setting then  Global Settings  Of This Plugin will work', 'redq-rental' ), 'value' => $rnb_settings_for_labels ),
			) ) );
			?>
			<div class="redq_rental_settings_block_local">
			  <?php
				$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_redq_rental_fields_label_local', array( 
					"redq_show_pricing_flipbox_text" => array( 'label' => __('Show Pricing Text', 'redq-rental'), 'placeholder' => __('Show Pricing Text', 'redq-rental') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele redq_rental', 'label_class' => 'wcfm_title redq_rental', 'value' => $redq_show_pricing_flipbox_text ),
					"redq_flip_pricing_plan_text" => array( 'label' => __('Show Pricing Info Heading Text', 'redq-rental'), 'placeholder' => __('Show Pricing Info Heading Text', 'redq-rental') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele redq_rental', 'label_class' => 'wcfm_title redq_rental', 'value' => $redq_flip_pricing_plan_text ),
					"redq_pickup_location_heading_title" => array( 'label' => __('Pickup Location Heading Title', 'redq-rental'), 'placeholder' => __('Pickup Location Heading Title', 'redq-rental') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele redq_rental', 'label_class' => 'wcfm_title redq_rental', 'value' => $redq_pickup_location_heading_title ),
					"redq_dropoff_location_heading_title" => array( 'label' => __('Dropoff Location Heading Title', 'redq-rental'), 'placeholder' => __('Dropoff Location Heading Title', 'redq-rental') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele redq_rental', 'label_class' => 'wcfm_title redq_rental', 'value' => $redq_dropoff_location_heading_title ),
					"redq_pickup_date_heading_title" => array( 'label' => __('Pickup Date Heading Title', 'redq-rental'), 'placeholder' => __('Pickup Date Heading Title', 'redq-rental') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele redq_rental', 'label_class' => 'wcfm_title redq_rental', 'value' => $redq_pickup_date_heading_title ),
					"redq_pickup_date_placeholder" => array( 'label' => __('Pickup Date Placeholder', 'redq-rental'), 'placeholder' => __('Pickup Date Placeholder', 'redq-rental') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele redq_rental', 'label_class' => 'wcfm_title redq_rental', 'value' => $redq_pickup_date_placeholder ),
					"redq_pickup_time_placeholder" => array( 'label' => __('Pickup Time Placeholder', 'redq-rental'), 'placeholder' => __('Pickup Time Placeholder', 'redq-rental') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele redq_rental', 'label_class' => 'wcfm_title redq_rental', 'value' => $redq_pickup_time_placeholder ),
					"redq_dropoff_date_heading_title" => array( 'label' => __('Dropoff Date Heading Title', 'redq-rental'), 'placeholder' => __('Dropoff Date Heading Title', 'redq-rental') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele redq_rental', 'label_class' => 'wcfm_title redq_rental', 'value' => $redq_dropoff_date_heading_title ),
					"redq_dropoff_date_placeholder" => array( 'label' => __('Drop-off Date Placeholder', 'redq-rental'), 'placeholder' => __('Drop-off Date Placeholder', 'redq-rental') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele redq_rental', 'label_class' => 'wcfm_title redq_rental', 'value' => $redq_dropoff_date_placeholder ),
					"redq_dropoff_time_placeholder" => array( 'label' => __('Drop-off Time Placeholder', 'redq-rental'), 'placeholder' => __('Drop-off Time Placeholder', 'redq-rental') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele redq_rental', 'label_class' => 'wcfm_title redq_rental', 'value' => $redq_dropoff_time_placeholder ),
					"redq_rnb_cat_heading" => array( 'label' => __('Category Heading Title', 'redq-rental'), 'placeholder' => __('Category Heading Title', 'redq-rental') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele redq_rental', 'label_class' => 'wcfm_title redq_rental', 'value' => $redq_rnb_cat_heading ),
					"redq_resources_heading_title" => array( 'label' => __('Resources Heading Title', 'redq-rental'), 'placeholder' => __('Resources Heading Title', 'redq-rental') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele redq_rental', 'label_class' => 'wcfm_title redq_rental', 'value' => $redq_resources_heading_title ),
					"redq_adults_heading_title" => array( 'label' => __('Adults Heading Title', 'redq-rental'), 'placeholder' => __('Adults Heading Title', 'redq-rental') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele redq_rental', 'label_class' => 'wcfm_title redq_rental', 'value' => $redq_adults_heading_title ),
					"redq_adults_placeholder" => array( 'label' => __('Adults Placeholder', 'redq-rental'), 'placeholder' => __('Adults Placeholder', 'redq-rental') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele redq_rental', 'label_class' => 'wcfm_title redq_rental', 'value' => $redq_adults_placeholder ),
					"redq_childs_heading_title" => array( 'label' => __('Childs Heading Title', 'redq-rental'), 'placeholder' => __('Childs Heading Title', 'redq-rental') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele redq_rental', 'label_class' => 'wcfm_title redq_rental', 'value' => $redq_childs_heading_title ),
					"redq_childs_placeholder" => array( 'label' => __('Childs Placeholder', 'redq-rental'), 'placeholder' => __('Childs Placeholder', 'redq-rental') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele redq_rental', 'label_class' => 'wcfm_title redq_rental', 'value' => $redq_childs_placeholder ),
					"redq_security_deposite_heading_title" => array( 'label' => __('Security Deposite Heading Title', 'redq-rental'), 'placeholder' => __('Security Deposite Heading Title', 'redq-rental') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele redq_rental', 'label_class' => 'wcfm_title redq_rental', 'value' => $redq_security_deposite_heading_title ),
					"redq_discount_text_title" => array( 'label' => __('Discount Text', 'redq-rental'), 'placeholder' => __('Discount Text', 'redq-rental') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele redq_rental', 'label_class' => 'wcfm_title redq_rental', 'value' => $redq_discount_text_title ),
					"redq_instance_pay_text_title" => array( 'label' => __('Instance Payment Text', 'redq-rental'), 'placeholder' => __('Instance Payment Text', 'redq-rental') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele redq_rental', 'label_class' => 'wcfm_title redq_rental', 'value' => $redq_instance_pay_text_title ),
					"redq_total_cost_text_title" => array( 'label' => __('Total Cost Text', 'redq-rental'), 'placeholder' => __('Total Cost Text', 'redq-rental') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele redq_rental', 'label_class' => 'wcfm_title redq_rental', 'value' => $redq_total_cost_text_title ),
					"redq_book_now_button_text" => array( 'label' => __('Book Now Button Text', 'redq-rental'), 'placeholder' => __('Book Now Button Text', 'redq-rental') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele redq_rental', 'label_class' => 'wcfm_title redq_rental', 'value' => $redq_book_now_button_text ),
					"redq_rfq_button_text" => array( 'label' => __('Request For Quote Button Text', 'redq-rental'), 'placeholder' => __('Request For Quote Button Text', 'redq-rental') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele redq_rental', 'label_class' => 'wcfm_title redq_rental', 'value' => $redq_rfq_button_text ),
					) ) );
				?>
			</div>
		</div>
	
		<h2><?php _e( 'Conditions', 'redq-rental' ); ?></h2><div class="wcfm_clearfix"></div>
		<div class="redq_rental_settings_block">
			<?php
			$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_redq_rental_fields_condition', array( 
				"rnb_settings_for_conditions" => array('label' => __('Choose Settings For Conditions Tab', 'redq-rental'), 'type' => 'select', 'options' => array( 'global' => __( 'Global Settings', 'redq-rental' ), 'local' => __( 'Local Settings', 'redq-rental' ) ), 'class' => 'wcfm-select redq_rental_settings_type redq_rental', 'label_class' => 'wcfm_title', 'hints' => __( 'If you choose local setting then these following options will work, If you chooose Global Setting then  Global Settings  Of This Plugin will work', 'redq-rental' ), 'value' => $rnb_settings_for_conditions ),
			) ) );
			?>
			<div class="redq_rental_settings_block_local">
			  <?php
				$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_redq_rental_fields_condition_local_date', array( 
					"block_rental_dates" => array( 'label' => __('Block Rental Dates', 'redq-rental'), 'hints' => __('This will be applicable for calendar date blocks', 'redq-rental') , 'type' => 'select', 'options' => array( 'yes' => __( 'Yes', 'redq-rental' ), 'no' => __( 'NO', 'redq-rental' ) ), 'class' => 'wcfm-select wcfm_ele redq_rental', 'label_class' => 'wcfm_title redq_rental', 'value' => $block_rental_dates ),
					"choose_date_format" => array( 'label' => __('Date Format Settings', 'redq-rental'), 'hints' => __('This will be applicable for all date calendar', 'redq-rental') , 'type' => 'select', 'options' => array( 'm/d/Y' => __( 'm/d/Y', 'redq-rental' ), 'd/m/Y' => __( 'd/m/Y', 'redq-rental' ), 'Y/m/d' => __( 'Y/m/d', 'redq-rental' ) ), 'class' => 'wcfm-select wcfm_ele redq_rental', 'label_class' => 'wcfm_title redq_rental', 'value' => $choose_date_format ),
					"max_time_late" => array( 'label' => __('Maximum time late (Hours)', 'redq-rental'), 'hints' => __('Another day will be count if anyone being late during departure', 'redq-rental') , 'type' => 'number', 'class' => 'wcfm-text wcfm_ele redq_rental', 'label_class' => 'wcfm_title redq_rental', 'value' => $max_time_late ),
					"redq_rental_local_enable_single_day_time_based_booking" => array( 'label' => __('Single Day Booking', 'redq-rental'), 'hints' => __('Checked : If pickup and return date are same then it counts as 1-day. Also select this for single date. FYI : Set max time late as at least 0 for this. UnChecked : If pickup and return date are same then it counts as 0-day. Also select this for single date.', 'redq-rental'), 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele redq_rental', 'label_class' => 'wcfm_title checkbox_title redq_rental', 'value' => 'open', 'dfvalue' => $redq_rental_local_enable_single_day_time_based_booking ),
					) ) );
				?>
				<?php
				$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_redq_rental_fields_condition_local_days', array( 
					"redq_max_rental_days" => array( 'label' => __('Maximum Booking Days', 'redq-rental'), 'placeholder' => __('Max Days', 'redq-rental'), 'attributes' => array( 'step' => 1, 'min' => 0) , 'type' => 'number', 'class' => 'wcfm-text wcfm_ele redq_rental', 'label_class' => 'wcfm_title redq_rental', 'value' => $redq_max_rental_days ),
					"redq_min_rental_days" => array( 'label' => __('Minimum Booking Days', 'redq-rental'), 'placeholder' => __('Min Days', 'redq-rental'), 'attributes' => array( 'step' => 1, 'min' => 0) , 'type' => 'number', 'class' => 'wcfm-text wcfm_ele redq_rental', 'label_class' => 'wcfm_title redq_rental', 'value' => $redq_min_rental_days ),
					"redq_rental_starting_block_dates" => array( 'label' => __('No. of Block Days Before Booking Started', 'redq-rental'), 'placeholder' => __('No. of Block Days Before Booking Started', 'redq-rental'), 'attributes' => array( 'step' => 1, 'min' => 0) , 'type' => 'number', 'class' => 'wcfm-text wcfm_ele redq_rental', 'label_class' => 'wcfm_title redq_rental', 'value' => $redq_rental_starting_block_dates ),
					"redq_rental_post_booking_block_dates" => array( 'label' => __('No. of Block Days After a Booking', 'redq-rental'), 'placeholder' => __('No. of Block Days After a Booking', 'redq-rental'), 'attributes' => array( 'step' => 1, 'min' => 0) , 'type' => 'number', 'class' => 'wcfm-text wcfm_ele redq_rental', 'label_class' => 'wcfm_title redq_rental', 'value' => $redq_rental_post_booking_block_dates ),
					"redq_time_interval" => array( 'label' => __('Time Inverval', 'redq-rental'), 'placeholder' => __('Time Inverval in mins E.X - 20', 'redq-rental'), 'attributes' => array( 'step' => 1, 'min' => 0, 'max' => 60), 'type' => 'number', 'class' => 'wcfm-text wcfm_ele redq_rental', 'label_class' => 'wcfm_title redq_rental', 'value' => $redq_time_interval )
					) ) );
				?>
				
				<p class="wcfm_title"><strong><?php _e( 'Select Weekends', 'redq-rental' ); ?></strong></p><label class="screen-reader-text" for="redq_rental_off_days"><?php _e( 'Select Weekends', 'redq-rental' ); ?></label>
				<select id="redq_rental_off_days" name="redq_rental_off_days[]" class="wcfm-select wcfm_ele redq_rental" multiple="multiple" style="width: 60%; margin-bottom: 10px;">
					<option value="0" <?php if( in_array( 0, $redq_rental_off_days ) ) echo 'selected="selected"'; ?>><?php _e( 'Sunday', 'redq-rental' ); ?></option>
					<option value="1" <?php if( in_array( 1, $redq_rental_off_days ) ) echo 'selected="selected"'; ?>><?php _e( 'Monday', 'redq-rental' ); ?></option>
					<option value="2" <?php if( in_array( 2, $redq_rental_off_days ) ) echo 'selected="selected"'; ?>><?php _e( 'Tuesday', 'redq-rental' ); ?></option>
					<option value="3" <?php if( in_array( 3, $redq_rental_off_days ) ) echo 'selected="selected"'; ?>><?php _e( 'Wednesday', 'redq-rental' ); ?></option>
					<option value="4" <?php if( in_array( 4, $redq_rental_off_days ) ) echo 'selected="selected"'; ?>><?php _e( 'Thursday', 'redq-rental' ); ?></option>
					<option value="5" <?php if( in_array( 5, $redq_rental_off_days ) ) echo 'selected="selected"'; ?>><?php _e( 'Friday', 'redq-rental' ); ?></option>
					<option value="6" <?php if( in_array( 6, $redq_rental_off_days ) ) echo 'selected="selected"'; ?>><?php _e( 'Saturday', 'redq-rental' ); ?></option>
				</select>
			</div>
		</div>

		<h2><?php _e( 'Validations', 'redq-rental' ); ?></h2><div class="wcfm_clearfix"></div>
		<div class="redq_rental_settings_block">
			<?php
			$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_redq_rental_fields_validation', array( 
				"rnb_settings_for_validations" => array('label' => __('Choose Settings For Validations Tab', 'redq-rental'), 'type' => 'select', 'options' => array( 'global' => __( 'Global Settings', 'redq-rental' ), 'local' => __( 'Local Settings', 'redq-rental' ) ), 'class' => 'wcfm-select redq_rental_settings_type redq_rental', 'label_class' => 'wcfm_title', 'hints' => __( 'If you choose local setting then these following options will work, If you chooose Global Setting then  Global Settings  Of This Plugin will work', 'redq-rental' ), 'value' => $rnb_settings_for_validations ),
			) ) );
			?>
			<div class="redq_rental_settings_block_local">
			  <?php
				$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_redq_rental_fields_validation_local_location', array( 
					"redq_rental_local_required_pickup_location" => array( 'label' => __('Required Pickup Location', 'redq-rental') , 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele redq_rental', 'label_class' => 'wcfm_title checkbox_title redq_rental', 'value' => 'open', 'dfvalue' => $redq_rental_local_required_pickup_location ),
					"redq_rental_local_required_return_location" => array( 'label' => __('Required Return Location', 'redq-rental') , 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele redq_rental', 'label_class' => 'wcfm_title checkbox_title redq_rental', 'value' => 'open', 'dfvalue' => $redq_rental_local_required_return_location ),
					"redq_rental_local_required_person" => array( 'label' => __('Required Person', 'redq-rental') , 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele redq_rental', 'label_class' => 'wcfm_title checkbox_title redq_rental', 'value' => 'open', 'dfvalue' => $redq_rental_local_required_person ),
					"redq_rental_required_local_pickup_time" => array( 'label' => __('Required Pickup Time', 'redq-rental') , 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele redq_rental', 'label_class' => 'wcfm_title checkbox_title redq_rental', 'value' => 'open', 'dfvalue' => $redq_rental_required_local_pickup_time ),
					"redq_rental_required_local_return_time" => array( 'label' => __('Required Return Time', 'redq-rental') , 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele redq_rental', 'label_class' => 'wcfm_title checkbox_title redq_rental', 'value' => 'open', 'dfvalue' => $redq_rental_required_local_return_time ),
					) ) );
				?>
			</div>
		</div>

		<h2><?php _e( 'Daily Basis Openning & Closing Time', 'redq-rental' ); ?></h2><div class="wcfm_clearfix"></div>
		<?php
		$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_redq_rental_fields_daily_times', array( 
			"redq_rental_fri_min_time" => array( 'label' => __('Friday', 'redq-rental'), 'placeholder' => __('Min Time', 'redq-rental'), 'type' => 'time', 'class' => 'wcfm-text wcfm_ele opening_closing_text redq_rental', 'label_class' => 'wcfm_title opening_closing_label redq_rental', 'value' => $redq_rental_fri_min_time ),
			"redq_rental_fri_max_time" => array( 'placeholder' => __('Max Time', 'redq-rental'), 'type' => 'time', 'class' => 'wcfm-text wcfm_ele opening_closing_text redq_rental', 'value' => $redq_rental_fri_max_time ),
			"redq_rental_sat_min_time" => array( 'label' => __('Saturday', 'redq-rental'), 'placeholder' => __('Min Time', 'redq-rental'), 'type' => 'time', 'class' => 'wcfm-text wcfm_ele opening_closing_text redq_rental', 'label_class' => 'wcfm_title opening_closing_label redq_rental', 'value' => $redq_rental_sat_min_time ),
			"redq_rental_sat_max_time" => array( 'placeholder' => __('Max Time', 'redq-rental'), 'type' => 'time', 'class' => 'wcfm-text wcfm_ele opening_closing_text redq_rental', 'value' => $redq_rental_sat_max_time ),
			"redq_rental_sun_min_time" => array( 'label' => __('Sunday', 'redq-rental'), 'placeholder' => __('Min Time', 'redq-rental'), 'type' => 'time', 'class' => 'wcfm-text wcfm_ele opening_closing_text redq_rental', 'label_class' => 'wcfm_title opening_closing_label redq_rental', 'value' => $redq_rental_sun_min_time ),
			"redq_rental_sun_max_time" => array( 'placeholder' => __('Max Time', 'redq-rental'), 'type' => 'time', 'class' => 'wcfm-text wcfm_ele opening_closing_text redq_rental', 'value' => $redq_rental_sun_max_time ),
			"redq_rental_mon_min_time" => array( 'label' => __('Monday', 'redq-rental'), 'placeholder' => __('Min Time', 'redq-rental'), 'type' => 'time', 'class' => 'wcfm-text wcfm_ele opening_closing_text redq_rental', 'label_class' => 'wcfm_title opening_closing_label redq_rental', 'value' => $redq_rental_mon_min_time ),
			"redq_rental_mon_max_time" => array( 'placeholder' => __('Max Time', 'redq-rental'), 'type' => 'time', 'class' => 'wcfm-text wcfm_ele opening_closing_text redq_rental', 'value' => $redq_rental_mon_max_time ),
			"redq_rental_thu_min_time" => array( 'label' => __('Tuesday', 'redq-rental'), 'placeholder' => __('Min Time', 'redq-rental'), 'type' => 'time', 'class' => 'wcfm-text wcfm_ele opening_closing_text redq_rental', 'label_class' => 'wcfm_title opening_closing_label redq_rental', 'value' => $redq_rental_thu_min_time ),
			"redq_rental_thu_max_time" => array( 'placeholder' => __('Max Time', 'redq-rental'), 'type' => 'time', 'class' => 'wcfm-text wcfm_ele opening_closing_text redq_rental', 'value' => $redq_rental_thu_max_time ),
			"redq_rental_wed_min_time" => array( 'label' => __('Wednesday', 'redq-rental'), 'placeholder' => __('Min Time', 'redq-rental'), 'type' => 'time', 'class' => 'wcfm-text wcfm_ele opening_closing_text redq_rental', 'label_class' => 'wcfm_title opening_closing_label redq_rental', 'value' => $redq_rental_wed_min_time ),
			"redq_rental_wed_max_time" => array( 'placeholder' => __('Max Time', 'redq-rental'), 'type' => 'time', 'class' => 'wcfm-text wcfm_ele opening_closing_text redq_rental', 'value' => $redq_rental_wed_max_time ),
			"redq_rental_thur_min_time" => array( 'label' => __('Thursday', 'redq-rental'), 'placeholder' => __('Min Time', 'redq-rental'), 'type' => 'time', 'class' => 'wcfm-text wcfm_ele opening_closing_text redq_rental', 'label_class' => 'wcfm_title opening_closing_label redq_rental', 'value' => $redq_rental_thur_min_time ),
			"redq_rental_thur_max_time" => array( 'placeholder' => __('Max Time', 'redq-rental'), 'type' => 'time', 'class' => 'wcfm-text wcfm_ele opening_closing_text redq_rental', 'value' => $redq_rental_thur_max_time ),
			) ) );
		?>
	</div>
</div>