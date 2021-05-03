<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly


$recipient_delivery_options = array(

    'export_import' => array(
        /**
         *
         * Recipient & Delivery settings for virtual Gift Cards
         *
         */
        array(
            'name' => esc_html__( 'Import/Export gift cards', 'yith-woocommerce-gift-cards' ),
            'type' => 'title',
        ),
	    'ywgc_export_or_import_option' => array(
		    'name' => esc_html__('Action to execute', 'yith-woocommerce-gift-cards'),
		    'desc'      => __( 'Choose if you want to export or import', 'yith-woocommerce-gift-cards' ),
		    'type'    => 'yith-field',
		    'yith-type' => 'radio',
		    'id' => 'ywgc_export_or_import_option',
		    'options' => array(
			    'export' => esc_html__( 'Export gift cards into a CSV file', 'yith-woocommerce-gift-cards' ),
			    'import' => esc_html__( 'Import gift cards from a CSV file', 'yith-woocommerce-gift-cards' ),
		    ),
		    'default' => 'export',
	    ),
	    'ywgc_export_option_date_field'    => array(
		    'type'  =>'date-from-to',
		    'name'  => __('Gift cards to export', 'yith-woocommerce-gift-cards' ),
		    'desc'      => __( 'Info to export', 'yith-woocommerce-gift-cards' ),
		    'id'    =>  'ywgc_export_option_date_field',
	    ),
	    'ywgc_export_option_order_id' => array(
		    'name'      => __('Select the information to be exported', 'yith-woocommerce-gift-cards' ),
		    'desc'      => __( 'Order ID', 'yith-woocommerce-gift-cards' ),
		    'type'      => 'checkbox',
		    'id'        => 'ywgc_export_option_order_id',
		    'default'   => 'yes',
		    'checkboxgroup' => 'start'
	    ),
	    'ywgc_export_option_gift_card_id' => array(
		    'desc'      => __( 'Gift card ID', 'yith-woocommerce-gift-cards' ),
		    'type'      => 'checkbox',
		    'id'        => 'ywgc_export_option_gift_card_id',
		    'default'   => 'yes',
		    'checkboxgroup' => ''
	    ),
	    'ywgc_export_option_gift_card_code' => array(
		    'desc'      => __( 'Code', 'yith-woocommerce-gift-cards' ),
		    'type'      => 'checkbox',
		    'id'        => 'ywgc_export_option_gift_card_code',
		    'default'   => 'yes',
		    'checkboxgroup' => ''
	    ),
	    'ywgc_export_option_gift_card_amount' => array(
		    'desc'      => __( 'Purchased amount', 'yith-woocommerce-gift-cards' ),
		    'type'      => 'checkbox',
		    'id'        => 'ywgc_export_option_gift_card_amount',
		    'default'   => 'yes',
		    'checkboxgroup' => ''
	    ),
	    'ywgc_export_option_gift_card_balance' => array(
		    'desc'      => __( 'Current balance', 'yith-woocommerce-gift-cards' ),
		    'type'      => 'checkbox',
		    'id'        => 'ywgc_export_option_gift_card_balance',
		    'default'   => 'yes',
		    'checkboxgroup' => ''
	    ),
	    'ywgc_export_option_sender_name' => array(
		    'desc'      => __( 'Sender\'s name', 'yith-woocommerce-gift-cards' ),
		    'type'      => 'checkbox',
		    'id'        => 'ywgc_export_option_sender_name',
		    'default'   => 'yes',
		    'checkboxgroup' => ''
	    ),
	    'ywgc_export_option_recipient_name' => array(
		    'desc'      => __( 'Recipient\'s name', 'yith-woocommerce-gift-cards' ),
		    'type'      => 'checkbox',
		    'id'        => 'ywgc_export_option_recipient_name',
		    'default'   => 'yes',
		    'checkboxgroup' => ''
	    ),
	    'ywgc_export_option_recipient_email' => array(
		    'desc'      => __( 'Recipient\'s email', 'yith-woocommerce-gift-cards' ),
		    'type'      => 'checkbox',
		    'id'        => 'ywgc_export_option_recipient_email',
		    'default'   => 'yes',
		    'checkboxgroup' => ''
	    ),
	    'ywgc_export_option_message' => array(
		    'desc'      => __( 'Message', 'yith-woocommerce-gift-cards' ),
		    'type'      => 'checkbox',
		    'id'        => 'ywgc_export_option_message',
		    'default'   => 'yes',
		    'checkboxgroup' => ''
	    ),
	    'ywgc_export_option_expiration_date' => array(
		    'desc'      => __( 'Expiration date', 'yith-woocommerce-gift-cards' ),
		    'type'      => 'checkbox',
		    'id'        => 'ywgc_export_option_expiration_date',
		    'default'   => 'yes',
		    'checkboxgroup' => ''
	    ),
	    'ywgc_export_option_delivery_date' => array(
		    'desc'      => __( 'Delivery date', 'yith-woocommerce-gift-cards' ),
		    'type'      => 'checkbox',
		    'id'        => 'ywgc_export_option_delivery_date',
		    'default'   => 'yes',
		    'checkboxgroup' => ''
	    ),
	    'ywgc_export_option_internal_note' => array(
		    'desc'      => __( 'Internal note', 'yith-woocommerce-gift-cards' ),
		    'type'      => 'checkbox',
		    'id'        => 'ywgc_export_option_internal_note',
		    'default'   => 'yes',
		    'checkboxgroup' => 'end'
	    ),
	    'ywgc_csv_delimitier' => array(
		    'name'      => __('Delimiter type', 'yith-woocommerce-gift-cards' ),
		    'desc'      => __( 'Enter the delimiter type. You can use for example , / ;', 'yith-woocommerce-gift-cards' ),
		    'type'    => 'yith-field',
		    'yith-type' => 'text',
		    'id'        => 'ywgc_csv_delimitier',
		    'default' => ';',
	    ),
	    'export_gift_cards_buttons'    => array(
		    'type'  =>'export-csv-button',
		    'desc' => __('Click on the button to generate a CSV with the gift cards.', 'yith-woocommerce-gift-cards' ),
		    'id'    =>  'ywgc_export_gift_cards_button',
	    ),
	    'import_gift_cards_buttons'    => array(
		    'type'  =>'import-csv-button',
		    'desc' => __('Click on the button to import a CSV with the gift cards.', 'yith-woocommerce-gift-cards' ),
		    'id'    =>  'ywgc_import_gift_cards_button',
	    ),

        array(
            'type' => 'sectionend',
        ),


    ),
);

return apply_filters( 'yith_ywgc_recipient_delivery_options_array', $recipient_delivery_options );
