<?php
/**
 * WCFM plugin views
 *
 * Plugin WC Booking Products Manage Views
 *
 * @author 		WC Lovers
 * @package 	wcfmu/views
 * @version   2.0.0
 */
global $wp, $WCFM, $WCFMu;

$product_id = 0;

$min_persons_group = 1;
$max_persons_group = '';
$person_cost_multiplier = '';
$person_qty_multiplier = '';
$has_person_types = '';
$person_types = array();

$resource_label = '';
$resources_assignment = '';
$resources = array();

if( isset( $wp->query_vars['wcfm-products-manage'] ) && !empty( $wp->query_vars['wcfm-products-manage'] ) ) {
	$product_id = $wp->query_vars['wcfm-products-manage'];
	if( $product_id ) {
		//$product = wc_get_product( $product_id );
		$bookable_product = new WC_Product_Booking( $product_id );
		
		$min_persons_group = $bookable_product->get_min_persons( 'edit' );
		$max_persons_group = $bookable_product->get_max_persons( 'edit' ) ? $bookable_product->get_max_persons( 'edit' ) : '';
		$person_cost_multiplier = $bookable_product->get_has_person_cost_multiplier( 'edit' ) ? 'yes' : 'no';
		$person_qty_multiplier = $bookable_product->get_has_person_qty_multiplier( 'edit' ) ? 'yes' : 'no';
		$has_person_types = $bookable_product->get_has_person_types( 'edit' ) ? 'yes' : 'no';
		$person_types_object = $bookable_product->get_person_types( 'edit' );
		if ( $person_types_object ) {
			foreach ( $person_types_object as $person_type_object ) {
				$person_types[] = array('person_id'   => esc_attr( $person_type_object->get_id() ),
																'person_name' => esc_attr( $person_type_object->get_name( 'edit' ) ),
																'person_description' => esc_attr( $person_type_object->get_description( 'edit' ) ),
																'person_cost' => esc_attr( $person_type_object->get_cost( 'edit' ) ),
																'person_block_cost' => esc_attr( $person_type_object->get_block_cost( 'edit' ) ),
																'person_min' => esc_attr( $person_type_object->get_min( 'edit' ) ),
																'person_max' => esc_attr( $person_type_object->get_max( 'edit' ) )
															);
			}
		}
		
		$resource_label = $bookable_product->get_resource_label( 'edit' );
		$resources_assignment = $bookable_product->get_resources_assignment( 'edit' );
		$product_resources    = $bookable_product->get_resource_ids( 'edit' );
		$resource_base_costs  = $bookable_product->get_resource_base_costs( 'edit' );
		$resource_block_costs = $bookable_product->get_resource_block_costs( 'edit' );
		$loop                 = 0;

		if ( $product_resources ) {
			foreach ( $product_resources as $resource_id ) {
				$resource            = new WC_Product_Booking_Resource( $resource_id );
				$resources[$loop]['resource_id'] = $resource->get_id();
				$resources[$loop]['resource_title'] = $resource->get_title();
				$resources[$loop]['resource_base_cost']  = isset( $resource_base_costs[ $resource_id ] ) ? $resource_base_costs[ $resource_id ] : '';
				$resources[$loop]['resource_block_cost'] = isset( $resource_block_costs[ $resource_id ] ) ? $resource_block_costs[ $resource_id ] : '';
				$resources[$loop]['resource_quantity']   = get_post_meta( $resource_id, 'qty', true );
				$loop++;
			}
		}
		
	}
}

$resource_ids       = WC_Data_Store::load( 'product-booking-resource' )->get_bookable_product_resource_ids();
$all_resources = array( -1 => __( 'Choose Resource', 'wc-frontend-manager-ultimate' ) );
if ( $resource_ids ) {
	foreach ( $resource_ids as $resource_id ) {
		$resource = new WC_Product_Booking_Resource( $resource_id );
	  $all_resources[esc_attr( $resource->ID )] = esc_html( $resource->post_title );
	}
}

$all_resources = apply_filters( 'wcfm_bookings_available_resource_list', $all_resources );
$resources     = apply_filters( 'wcfm_bookings_product_edit_resources', $resources );
?>

<!-- Collapsible Booking 4  -->
<div class="page_collapsible products_manage_persons persons booking accommodation-booking" id="wcfm_products_manage_form_persons_head"><label class="wcfmfa fa-user"></label><?php _e('Persons', 'woocommerce-bookings'); ?><span></span></div>
<div class="wcfm-container persons booking accommodation-booking">
	<div id="wcfm_products_manage_form_persons_expander" class="wcfm-content">
		<?php
		$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_wcbokings_person_types_fields', array(
					
					"_wc_booking_min_persons_group" => array('label' => __('Min persons', 'woocommerce-bookings') , 'type' => 'number', 'class' => 'wcfm-text wcfm_ele booking accommodation-booking', 'label_class' => 'wcfm_title booking accommodation-booking', 'value' => $min_persons_group, 'hints' => __( 'The minimum number of persons per booking.', 'woocommerce-bookings' ), 'attributes' => array( 'min' => '1', 'step' => '1' ) ),
					"_wc_booking_max_persons_group" => array('label' => __('Max persons', 'woocommerce-bookings') , 'type' => 'number', 'class' => 'wcfm-text wcfm_ele booking accommodation-booking', 'label_class' => 'wcfm_title booking accommodation-booking', 'value' => $max_persons_group, 'hints' => __( 'The maximum number of persons per booking.', 'woocommerce-bookings' ), 'attributes' => array( 'min' => '1', 'step' => '1' ) ),
					"_wc_booking_person_cost_multiplier" => array('label' => __('Multiply all costs by person count', 'woocommerce-bookings') , 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele booking accommodation-booking', 'label_class' => 'wcfm_title checkbox_title booking accommodation-booking', 'value' => 'yes', 'dfvalue' => $person_cost_multiplier, 'hints' => __( 'Enable this to multiply the entire cost of the booking (block and base costs) by the person count.', 'woocommerce-bookings' ) ),
					"_wc_booking_person_qty_multiplier" => array('label' => __('Count persons as bookings', 'woocommerce-bookings') , 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele booking accommodation-booking', 'label_class' => 'wcfm_title checkbox_title booking accommodation-booking', 'value' => 'yes', 'dfvalue' => $person_qty_multiplier, 'hints' => __( 'Enable this to count each person as a booking until the max bookings per block (in availability) is reached.', 'woocommerce-bookings' ) ),
					"_wc_booking_has_person_types" => array('label' => __('Enable person types', 'woocommerce-bookings') , 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele booking accommodation-booking', 'label_class' => 'wcfm_title checkbox_title booking accommodation-booking', 'value' => 'yes', 'dfvalue' => $has_person_types, 'hints' => __( 'Person types allow you to offer different booking costs for different types of individuals, for example, adults and children.', 'woocommerce-bookings' ) ),
					"_wc_booking_person_types" =>     array('label' => __('Person Types', 'wc-frontend-manager-ultimate') , 'type' => 'multiinput', 'class' => 'wcfm-text person_types', 'label_class' => 'wcfm_title person_types', 'value' => $person_types, 'options' => array(
																									"person_name" => array('label' => __('Type Name', 'wc-frontend-manager-ultimate'), 'type' => 'text', 'class' => 'wcfm-text person_types_text', 'label_class' => 'wcfm_title person_types_label' ),
																									"person_cost" => array('label' => __('Base Cost', 'woocommerce-bookings'), 'type' => 'number', 'class' => 'wcfm-text person_types_text', 'label_class' => 'wcfm_title person_types_label' ),
																									"person_min" => array('label' => __('Min', 'woocommerce-bookings'), 'type' => 'number', 'class' => 'wcfm-text person_types_text', 'label_class' => 'wcfm_title person_types_label' ),
																									"person_description" => array('label' => __('Description', 'woocommerce-bookings'), 'type' => 'text', 'class' => 'wcfm-text person_types_text', 'label_class' => 'wcfm_title person_types_label' ),
																									"person_block_cost" => array('label' => __('Block Cost', 'woocommerce-bookings'), 'type' => 'number', 'class' => 'wcfm-text person_types_text', 'label_class' => 'wcfm_title person_block_cost_label person_types_label' ),
																									"person_max" => array('label' => __('Max', 'woocommerce-bookings'), 'type' => 'number', 'class' => 'wcfm-text person_types_text', 'label_class' => 'wcfm_title person_types_label' ),
																									"person_id" => array('type' => 'hidden', 'class' => 'person_id' )
																							    )	)																		
																						), $product_id ) );
		?>
	</div>
</div>
<!-- end collapsible Booking -->
<div class="wcfm_clearfix"></div>

<!-- Collapsible Booking 5  -->
<div class="page_collapsible products_manage_resources resources booking accommodation-booking" id="wcfm_products_manage_form_resources_head"><label class="wcfmfa fa-briefcase"></label><?php _e('Resources', 'woocommerce-bookings'); ?><span></span></div>
<div class="wcfm-container resources booking accommodation-booking">
	<div id="wcfm_products_manage_form_resources_expander" class="wcfm-content">
		<?php
		$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_wcbokings_resource_fields', array(
					
					"_wc_booking_resource_label" => array( 'label' => __('Label', 'woocommerce-bookings'), 'placeholder' => __('Type', 'woocommerce-bookings'), 'type' => 'text', 'class' => 'wcfm-text wcfm_ele booking accommodation-booking', 'label_class' => 'wcfm_title booking accommodation-booking', 'value' => $resource_label, 'hints' => __( 'The label shown on the frontend if the resource is customer defined.', 'woocommerce-bookings' ) ),
					"_wc_booking_resources_assignment" => array( 'label' => __('Resources are...', 'woocommerce-bookings'), 'type' => 'select', 'options' => array( 'customer' => __( 'Customer selected', 'woocommerce-bookings'), 'automatic' => __( 'Automatically assigned', 'woocommerce-bookings' ) ), 'class' => 'wcfm-select wcfm_ele booking accommodation-booking', 'label_class' => 'wcfm_title booking accommodation-booking', 'value' => $resources_assignment, 'hints' => __( 'Customer selected resources allow customers to choose one from the booking form.', 'woocommerce-bookings' ) ),
					"_wc_booking_all_resources" => array( 'label' => __('Available for Resources', 'woocommerce-bookings'), 'type' => 'select', 'options' => $all_resources, 'class' => 'wcfm-select wcfm_ele booking accommodation-booking', 'label_class' => 'wcfm_title booking accommodation-booking', 'hints' => __( 'Resources are used if you have multiple bookable items, e.g. room types, instructors or ticket types. Availability for resources is global across all bookable products. Choose to associate with your product.', 'woocommerce-bookings' ) ),
					"_wc_booking_resources" =>     array('label' => __('Resources', 'woocommerce-bookings') , 'type' => 'multiinput', 'class' => 'wcfm-text wcfm_ele resource_types booking accommodation-booking', 'label_class' => 'wcfm_title resource_types booking accommodation-booking', 'value' => $resources, 'options' => array(
																									"resource_title" => array('label' => __('Title', 'woocommerce-bookings'), 'type' => 'text', 'class' => 'wcfm-text wcfm_ele resource_types_text booking accommodation-booking', 'label_class' => 'wcfm_title resource_types_label booking accommodation-booking' ),
																									"resource_base_cost" => array('label' => __('Base Cost', 'woocommerce-bookings'), 'type' => 'text', 'class' => 'wcfm-text wcfm_ele resource_types_text booking accommodation-booking', 'label_class' => 'wcfm_title resource_types_label booking accommodation-booking' ),
																									"resource_block_cost" => array('label' => __('Block Cost', 'woocommerce-bookings'), 'type' => 'number', 'class' => 'wcfm-text wcfm_ele resource_types_text booking accommodation-booking', 'label_class' => 'wcfm_title resource_types_label booking accommodation-booking' ),
																									"resource_quantity" => array('label' => __('Quantity', 'woocommerce-bookings'), 'type' => 'number', 'class' => 'wcfm-text wcfm_ele resource_types_text booking accommodation-booking', 'label_class' => 'wcfm_title resource_types_label booking accommodation-booking' ),
																									"resource_id" => array('type' => 'hidden', 'class' => 'resource_id' )
																									) )
																						) ) );
		?>
	</div>
</div>
<!-- end collapsible Booking -->
<div class="wcfm_clearfix"></div>