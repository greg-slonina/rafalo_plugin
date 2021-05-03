<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


if ( ! class_exists( 'YITH_YWGC_Backend_Premium' ) ) {

	/**
	 *
	 * @class   YITH_YWGC_Backend_Premium
	 *
	 * @since   1.0.0
	 * @author  Lorenzo Giuffrida
	 */
	class YITH_YWGC_Backend_Premium extends YITH_YWGC_Backend {
		/**
		 * Single instance of the class
		 *
		 * @since 1.0.0
		 */
		protected static $instance;

		public $admin_notices = array();

		/**
		 * Returns single instance of the class
		 *
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * Initialize plugin and registers actions and filters to be used
		 *
		 * @since  1.0
		 * @author Lorenzo Giuffrida
		 */
		protected function __construct() {

			parent::__construct();

			/**
			 * Set the CSS class 'show_if_gift-card in 'sold indidually' section
			 */
			add_action( 'woocommerce_product_options_inventory_product_data', array(
				$this,
				'show_sold_individually_for_gift_cards'
			) );

			/**
			 * manage CSS class for the gift cards table rows
			 */
			add_filter( 'post_class', array( $this, 'add_cpt_table_class' ), 10, 3 );

			add_action( 'init', array( $this, 'redirect_gift_cards_link' ) );

			add_action( 'load-upload.php', array( $this, 'set_gift_card_category_to_media' ) );

			add_action( 'edited_term_taxonomy', array( $this, 'update_taxonomy_count' ), 10, 2 );

			/**
			 * Show icon that prompt the admin for a pre-printed gift cards buyed and whose code is not entered
			 */
			add_action( 'manage_shop_order_posts_custom_column', array(
				$this,
				'show_warning_for_pre_printed_gift_cards'
			) );

			/*
			 * Save additional product attribute when a gift card product is saved
			 */
			add_action( 'yith_gift_cards_after_product_save', array(
				$this,
				'save_gift_card_product'
			) );

			/**
			 * Show inventory tab in product tabs
			 */
			add_filter( 'woocommerce_product_data_tabs', array(
				$this,
				'show_inventory_tab'
			) );

			add_action( 'yith_ywgc_gift_card_email_sent', array(
				$this,
				'manage_bcc'
			) );

			add_action( 'yith_ywgc_product_settings_after_amount_list', array(
				$this,
				'show_advanced_product_settings'
			) );

			/**
			 * Show gift cards code and amount in order's totals section, in edit order page
			 */
			add_action( 'woocommerce_admin_order_totals_after_tax', array(
				$this,
				'show_gift_cards_total_before_order_totals'
			) );

			/**
			 * Add filters on the Gift Card Post Type page
			 */
			add_filter( 'views_edit-gift_card', array( $this, 'add_gift_cards_filters' ) );
			add_action( 'pre_get_posts', array( $this, 'filter_gift_card_page_query' ) );

			/*
			 * Filter display order item meta key to show
			 */
			add_filter( 'woocommerce_order_item_display_meta_key',array($this,'show_as_string_order_item_meta_key'),10,1 );

			/*
			 * Filter display order item meta value to show
			 */
			add_filter( 'woocommerce_order_item_display_meta_value', array( $this,'show_formatted_date' ),10,3 );


			add_action('woocommerce_order_status_changed', array($this,'update_gift_card_amount_on_order_status_change'),10,4);
			add_action( 'woocommerce_order_status_changed', array( $this, 'update_gift_card_as_coupon_amount_on_order_status_change' ), 10, 4 );

			add_action( 'woocommerce_admin_field_yith_ywgc_transform_smart_coupons_html', array($this, 'yith_ywgc_transform_smart_coupons_buttons' ) );


			add_filter( 'yith_ywgc_general_options_array', array( $this, 'yith_ywgc_general_options_array_custom' ), 10, 1 ) ;


			//Ajax methods for Apply buttons
			add_action( 'wp_ajax_yith_convert_smart_coupons_button', array( $this, 'ywgc_convert_smart_coupons_to_gift_cards' ) );
			add_action( 'wp_ajax_nopriv_yith_convert_smart_coupons_button', array( $this, 'ywgc_convert_smart_coupons_to_gift_cards' ) );


			add_action( 'wp_ajax_ywgc_toggle_enabled_action', array( $this, 'ywgc_toggle_enabled_action' ) );
			add_action( 'wp_ajax_nopriv_ywgc_toggle_enabled_action', array( $this, 'ywgc_toggle_enabled_action' ) );


			add_action( 'wp_ajax_ywgc_update_cron', array( $this, 'ywgc_update_cron' ) );
			add_action( 'wp_ajax_nopriv_ywgc_update_cron', array( $this, 'ywgc_update_cron' ) );

			add_action( 'add_meta_boxes' ,  array( $this, 'ywgc_remove_product_meta_boxes' ), 40 );

			add_filter('woocommerce_hidden_order_itemmeta', array( $this, 'ywgc_hidden_order_item_meta' ), 10, 1);

			/*
			 * Recalculate order totals on save order items (in order to show always the correct total for the order)
			 */
			add_action( 'woocommerce_before_order_object_save', array( $this,'update_totals_on_save_order_items' ),10, 2 );

			add_action( 'woocommerce_saved_order_items', array( $this, 'ywgc_recalculate_totals_gift_cards_as_fees' ), 100, 2 );

			/*
			 * Show the default shipping in the order summary, not the discounted one
			 */
			if ( apply_filters( 'ywgc_display_default_shipping_value_condition', false ) ){
				add_action( 'woocommerce_order_shipping_to_display', array( $this,'ywgc_display_default_shipping_value' ),10, 3 );
			}

			add_action( 'admin_action_export_gift_cards', array( $this, 'ywgc_export_gift_card_data' ) );

			add_action( 'admin_init', array( $this, 'import_actions_from_settings_panel' ), 9 );

//			add_filter( 'get_search_query', array( $this, 'gift_card_search_label' ) );
//			add_action( 'parse_query', array( $this, 'gift_card_search' ) );
//			add_filter( 'query_vars', array( $this, 'add_custom_query_var_gift_cards' ) );

			add_action( 'admin_notices', array( $this, 'ywgc_show_upload_error') );

			add_action( 'giftcard-category_edit_form', array( $this, 'ywgc_include_upload_image_categories'), 10, 2 );

			add_action( 'admin_init', array( $this, 'ywgc_upload_image_actions_from_settings_panel' ), 9 );

			add_action( 'wp_ajax_ywgc_delete_image_from_category', array( $this, 'ywgc_delete_image_from_category' ) );
			add_action( 'wp_ajax_nopriv_ywgc_delete_image_from_category', array( $this, 'ywgc_delete_image_from_category' ) );

			add_action( 'giftcard-category_add_form_fields', array( $this, 'ywgc_add_form_fields_category_creation'), 10, 1 );
			add_action( 'created_term', array( $this, 'ywgc_save_images_on_category_creation' ), 10, 3 );
			add_action( 'edit_term', array( $this, 'ywgc_save_images_on_category_creation' ), 10, 3 );


			/*
			 * Compatibility with YITH WooCommerce Subscription Premium
			 */
			if( defined('YITH_YWSBS_PREMIUM') ){
				add_action( 'ywsbs_renew_subscription', array( $this, 'apply_gift_cards_on_subscription_renew' ), 10, 2 );
			}



		}


		/**
		 * Show gift cards code and amount in order's totals section, in edit order page
		 *
		 * @param int $order_id
		 */
		public function show_gift_cards_total_before_order_totals( $order_id ) {

			$order            = wc_get_order( $order_id );
			$order_gift_cards = yit_get_prop( $order, '_ywgc_applied_gift_cards', true );
			$currency         = $order->get_currency();

			if ( $order_gift_cards ) :
				foreach ( $order_gift_cards as $code => $amount ): ?>
					<?php $amount = apply_filters('ywgc_gift_card_amount_order_total_item', $amount, YITH_YWGC()->get_gift_card_by_code( $code ) ); ?>
					<tr>
						<td class="label"><?php _e( 'Gift card: ' . $code, 'yith-woocommerce-gift-cards' ); ?>:</td>
						<td width="1%"></td>
						<td class="total">
							<?php echo wc_price( $amount, array( 'currency' => $currency ) ); ?>
						</td>
					</tr>
				<?php endforeach;
			endif;
		}

		/**
		 * Send a copy of gift card email to additional recipients, if set
		 *
		 * @param $gift_card
		 */
		public function manage_bcc( $gift_card ) {

			$this->notify_customer_if_gift_cards_is_delivered( $gift_card );

			$order = new WC_Order( $gift_card->order_id );

			$recipients = array();

			//  Check if the option is set to add the admin email
			if ( get_option ( "ywgc_blind_carbon_copy", 'no' ) == "yes" ) {
				$recipients[] = get_option( 'admin_email' );
			}

			//  Check if the option is set to add the gift card buyer email
			if ( get_option ( "ywgc_blind_carbon_copy_to_buyer", 'no' ) == "yes" && $gift_card->recipient != $order->get_billing_email()) {
				$recipients[] = $order->get_billing_email();
			}

			$additional_emails = get_option ( "ywgc_blind_carbon_copy_additionals", '' );

			if ( $additional_emails != "" ){
				$emails_array = explode(',', $additional_emails);
				foreach ( $emails_array as $email ){
					$recipients[] = $email;
				}
			}

			$recipients = apply_filters( 'yith_ywgc_bcc_additional_recipients', $recipients );
			if ( empty( $recipients ) )
				return;

			WC()->mailer();

			foreach ( $recipients as $recipient ) {
				//  Send a copy of the gift card to the recipient
				$gift_card->recipient = $recipient;
				do_action( 'ywgc-email-send-gift-card_notification', $gift_card, 'BCC' );
			}
		}

		public function notify_customer_if_gift_cards_is_delivered( $gift_card ) {

			if ( "yes" == get_option ( 'ywgc_delivery_notify_customer' , 'no' ) && $gift_card->delivery_notification == 'on' ) {

				if ( $gift_card->exists() ) {
					WC()->mailer();
					do_action( 'ywgc-email-delivered-gift-card', $gift_card );
				}
			}
		}

		/**
		 * Show inventory section for gift card products
		 *
		 * @param array $tabs
		 *
		 * @return mixed
		 */
		public function show_inventory_tab( $tabs ) {
			if ( isset( $tabs['inventory'] ) ) {

				array_push( $tabs['inventory']['class'], 'show_if_gift-card' );
			}

			return $tabs;

		}

		/**
		 * Save additional product attribute when a gift card product is saved
		 *
		 * @param int $post_id current product id
		 */
		public function save_gift_card_product( $post_id ) {

			$product = new WC_Product_Gift_Card( $post_id );


			if ( isset( $_POST['ywgc-override-product-settings-' . $product->get_id()] ) ) {

				$product->update_override_global_settings_status( $_POST['ywgc-override-product-settings-' . $product->get_id()] );
			}
			else{
				$product->update_override_global_settings_status( false );
			}

			//	Save the flag for manual amounts when the product is saved
			if ( isset( $_POST['manual_amount_mode-' . $product->get_id()] ) ) {
				$product->update_manual_amount_status( $_POST['manual_amount_mode-' . $product->get_id()] );
			}
			else{
				$product->update_manual_amount_status( 'disabled-product-level' );
			}


			//Discount settings update
			if ( isset( $_POST['ywgc-add-discount-settings-' . $product->get_id()] ) ) {
				$product->update_add_discount_settings_status( $_POST['ywgc-add-discount-settings-' . $product->get_id()] );
			}
			else{
				$product->update_add_discount_settings_status( false );
			}


			if ( isset( $_POST["gift_card-sale-discount"] ) ) {
				update_post_meta( $post_id, '_ywgc_sale_discount_value', $_POST["gift_card-sale-discount"] );
			}

			if ( isset( $_POST["gift_card-sale-discount-text"] ) ) {
				update_post_meta( $post_id, '_ywgc_sale_discount_text', $_POST["gift_card-sale-discount-text"] );
			}



			//Expiration settings update
			if ( isset( $_POST['ywgc-expiration-settings-' . $product->get_id()] ) ) {
				$product->update_expiration_settings_status( $_POST['ywgc-expiration-settings-' . $product->get_id()] );
			}
			else{
				$product->update_expiration_settings_status( false );
			}


			if ( isset( $_POST["gift-card-expiration-date"] ) ) {

				$date_format = apply_filters('yith_wcgc_date_format','Y-m-d');

				$expiration_date = is_string($_POST["gift-card-expiration-date"]) ? strtotime($_POST["gift-card-expiration-date"]) : $_POST["gift-card-expiration-date"];

				$expiration_date_formatted = !empty( $expiration_date ) ? date_i18n( $date_format, $expiration_date ) : '';

				update_post_meta( $post_id, '_ywgc_expiration', $expiration_date );

				update_post_meta( $post_id, '_ywgc_expiration_date', $expiration_date_formatted );
			}


			if ( isset( $_POST["ywgc-minimal-amount"] ) ) {
				update_post_meta( $post_id, '_ywgc_minimal_manual_amount', $_POST["ywgc-minimal-amount"] );
			}

		}

		/**
		 * Show icon on backend page "orders" for order where there is file uploaded and waiting to be confirmed.
		 *
		 * @param string $column current column being shown
		 */
		public function show_warning_for_pre_printed_gift_cards( $column ) {
			//  If column is not of type order_status, skip it
			if ( 'order_status' !== $column ) {
				return;
			}

			global $the_order;
			if ( !empty( $the_order ) && ( $the_order instanceof WC_Order ) ){
				$count = $this->pre_printed_cards_waiting_count( $the_order );
				if ( $count ) {
					$message = _n( "This order contains one pre-printed gift card that needs to be filled", sprintf( "This order contains %d pre-printed gift cards that needs to be filled", $count ), $count, 'yith-woocommerce-gift-cards' );
					echo '<img class="ywgc-pre-printed-waiting" src="' . YITH_YWGC_ASSETS_IMAGES_URL . 'waiting.png" title="' . $message . '" />';
				}
			}
		}

		/**
		 * Retrieve the number of pre-printed gift cards that are not filled
		 *
		 * @param WC_Order $order
		 *
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 * @return int
		 */
		private function pre_printed_cards_waiting_count( $order ) {
			$order_items = $order->get_items( 'line_item' );
			$count       = 0;

			foreach ( $order_items as $order_item_id => $order_data ) {
				$gift_ids = ywgc_get_order_item_giftcards( $order_item_id );

				if ( empty( $gift_ids ) ) {
					return;
				}

				foreach ( $gift_ids as $gift_id ) {

					$gc = new YWGC_Gift_Card_Premium( array( 'ID' => $gift_id ) );

					if ( $gc->is_pre_printed() ) {
						$count ++;
					}
				}
			}

			return $count;
		}

		/**
		 * Fix the taxonomy count of items
		 *
		 * @param $term_id
		 * @param $taxonomy_name
		 *
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function update_taxonomy_count( $term_id, $taxonomy_name ) {
			//  Update the count of terms for attachment taxonomy
			if ( YWGC_CATEGORY_TAXONOMY != $taxonomy_name ) {
				return;
			}

			//  update now
			global $wpdb;
			$count = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $wpdb->term_relationships, $wpdb->posts p1 WHERE p1.ID = $wpdb->term_relationships.object_id AND ( post_status = 'publish' OR ( post_status = 'inherit' AND (post_parent = 0 OR (post_parent > 0 AND ( SELECT post_status FROM $wpdb->posts WHERE ID = p1.post_parent ) = 'publish' ) ) ) ) AND post_type = 'attachment' AND term_taxonomy_id = %d", $term_id ) );

			$wpdb->update( $wpdb->term_taxonomy, compact( 'count' ), array( 'term_taxonomy_id' => $term_id ) );
		}


		public function set_gift_card_category_to_media() {

			//  Skip all request without an action
			if ( ! isset( $_REQUEST['action'] ) && ! isset( $_REQUEST['action2'] ) ) {
				return;
			}

			//  Skip all request without a valid action
			if ( ( '-1' == $_REQUEST['action'] ) && ( '-1' == $_REQUEST['action2'] ) ) {
				return;
			}

			$action = '-1' != $_REQUEST['action'] ? $_REQUEST['action'] : $_REQUEST['action2'];

			//  Skip all request that do not belong to gift card categories
			if ( ( 'ywgc-set-category' != $action ) && ( 'ywgc-unset-category' != $action ) ) {
				return;
			}

			//  Skip all request without a media list
			if ( ! isset( $_REQUEST['media'] ) ) {
				return;
			}

			$media_ids = $_REQUEST['media'];

			//  Check if the request if for set or unset the selected category to the selected media
			$action_set_category = ( 'ywgc-set-category' == $action ) ? true : false;

			//  Retrieve the category to be applied to the selected media
			$category_id = '-1' != $_REQUEST['action'] ? intval( $_REQUEST['categories1_id'] ) : intval( $_REQUEST['categories2_id'] );

			foreach ( $media_ids as $media_id ) {

				// Check whether this user can edit this post
				//if ( ! current_user_can ( 'edit_post', $media_id ) ) continue;

				if ( $action_set_category ) {
					$result = wp_set_object_terms( $media_id, $category_id, YWGC_CATEGORY_TAXONOMY, true );
				} else {
					$result = wp_remove_object_terms( $media_id, $category_id, YWGC_CATEGORY_TAXONOMY );
				}

				if ( is_wp_error( $result ) ) {
					return $result;
				}
			}
		}

		/**
		 * manage CSS class for the gift cards table rows
		 *
		 * @param array  $classes
		 * @param string $class
		 * @param int    $post_id
		 *
		 * @return array|mixed|void
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function add_cpt_table_class( $classes, $class, $post_id ) {

			if ( YWGC_CUSTOM_POST_TYPE_NAME != get_post_type( $post_id ) ) {
				return $classes;
			}

			$gift_card = new YWGC_Gift_Card_Premium( array( 'ID' => $post_id ) );

			if ( ! $gift_card->exists() ) {
				return $class;
			}

			$classes[] = $gift_card->status;

			return apply_filters( 'yith_gift_cards_table_class', $classes, $post_id );
		}


		/**
		 * Make some redirect based on the current action being performed
		 *
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function redirect_gift_cards_link() {

			/**
			 * Check if the user ask for downloading the gift pdf file
			 */
			if ( isset( $_GET[ YWGC_ACTION_DOWNLOAD_PDF ] ) &&  isset( $_GET[ "gift-card-nonce" ] ) && wp_verify_nonce( $_GET[ "gift-card-nonce" ], "gift-card-nonce" ) ) {

				$gift_id = $_GET[ 'id' ];
				$gift_card = new YWGC_Gift_Card_Premium( array( 'ID' => $gift_id ) );

				$new_file = YITH_YWGC()->create_gift_card_pdf_file( $gift_card );

				header('Content-type:  application/pdf');
				header('Content-Length: ' . filesize( $new_file ) );
				header('Content-Disposition: attachment; filename="' . basename( $new_file ) . '"');
				readfile( $new_file );

				ignore_user_abort( true );
				( connection_aborted() ? unlink( $new_file ) : unlink( $new_file ) );

				exit;

			}

			/**
			 * Check if the user ask for retrying sending the gift card email that are not shipped yet
			 */
			if ( isset( $_GET[ YWGC_ACTION_RETRY_SENDING ] ) ) {

				$gift_card_id = $_GET['id'];

				YITH_YWGC_Emails::get_instance()->send_gift_card_email( $gift_card_id, false );
				$redirect_url = remove_query_arg( array( YWGC_ACTION_RETRY_SENDING, 'id' ) );

				wp_redirect( $redirect_url );
				exit;
			}

			/**
			 * Check if the user ask for enabling/disabling a specific gift cards
			 */
			if ( isset( $_GET[ YWGC_ACTION_ENABLE_CARD ] ) || isset( $_GET[ YWGC_ACTION_DISABLE_CARD ] ) ) {

				$gift_card_id = $_GET['id'];
				$enabled      = isset( $_GET[ YWGC_ACTION_ENABLE_CARD ] );

				$gift_card = new YWGC_Gift_Card_Premium( array( 'ID' => $gift_card_id ) );

				if ( ! $gift_card->is_dismissed() ) {

					$current_status = $gift_card->is_enabled();

					if ( $current_status != $enabled ) {

						$gift_card->set_enabled_status( $enabled );
						do_action( 'yith_gift_cards_status_changed', $gift_card, $enabled );
					}

					wp_redirect( remove_query_arg( array(
						YWGC_ACTION_ENABLE_CARD,
						YWGC_ACTION_DISABLE_CARD,
						'id'
					) ) );
					die();
				}
			}

			if ( ! isset( $_GET["post_type"] ) || ! isset( $_GET["s"] ) ) {
				return;
			}


			if ( 'shop_coupon' != ( $_GET["post_type"] ) ) {
				return;
			}

			if ( preg_match( "/(\w{4}-\w{4}-\w{4}-\w{4})(.*)/i", $_GET["s"], $matches ) ) {
				wp_redirect( admin_url( 'edit.php?s=' . $matches[1] . '&post_type=gift_card' ) );
				die();
			}
		}


		public function show_sold_individually_for_gift_cards() {
			?>
			<script>
				jQuery("#_sold_individually").closest(".options_group").addClass("show_if_gift-card");
				jQuery("#_sold_individually").closest(".form-field").addClass("show_if_gift-card");
			</script>
			<?php
		}

		/**
		 * Show advanced product settings
		 *
		 * @param int $thepostid
		 */
		public function show_advanced_product_settings( $thepostid ) {

			$this->show_manual_amount_settings( $thepostid );
			$this->show_sale_discount_settings( $thepostid );
			$this->show_gift_card_expiration_date_settings( $thepostid );


		}

		/**
		 * Add filters on the Gift Card Post Type page
		 *
		 * @param $views
		 *
		 * @return mixed
		 */

		public function add_gift_cards_filters( $views ) {
			global $wpdb;
			$args = array(
				'post_status' => 'published',
				'post_type'   => 'gift_card',
				'balance'     => 'active'
			);

			$count_active = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT( DISTINCT( post_id ) ) FROM {$wpdb->postmeta} AS pm LEFT JOIN {$wpdb->posts} AS p ON p.ID = pm.post_id WHERE meta_key = %s AND meta_value <> 0 AND p.post_type= %s", '_ywgc_balance_total', 'gift_card' ) );
			$count_used   = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT( DISTINCT( post_id ) ) FROM {$wpdb->postmeta} AS pm LEFT JOIN {$wpdb->posts} AS p ON p.ID = pm.post_id WHERE meta_key = %s AND ROUND(meta_value, %d) = 0 AND p.post_type= %s", '_ywgc_balance_total', wc_get_price_decimals(), 'gift_card' ) );

			$views['active'] = sprintf( '<a href="%s">%s <span class="count">(%d)</span></a>', add_query_arg( $args, admin_url( 'edit.php' ) ), esc_html__( 'Active', 'yith-woocommerce-gift-cards' ), $count_active );
			$args['balance'] = 'used';
			$views['used']   = sprintf( '<a href="%s">%s <span class="count">(%d)</span></a>', add_query_arg( $args, admin_url( 'edit.php' ) ), esc_html__( 'Used', 'yith-woocommerce-gift-cards' ), $count_used );

			return $views;
		}


		/**
		 * Add filters on the Gift Card Post Type page
		 *
		 * @param $query
		 */

		public function filter_gift_card_page_query( $query ) {
			global $pagenow, $post_type;

			if ( $pagenow == 'edit.php' && $post_type == 'gift_card' && isset( $_GET['balance'] ) && in_array( $_GET['balance'], array(
					'used',
					'active'
				) ) ) {
				if ( 'active' == $_GET['balance'] ) {
					$meta_query = array(
						array(
							'key'     => '_ywgc_balance_total',
							'value'   => 0,
							'compare' => '>'
						)
					);
				} else {
					$meta_query = array(
						array(
							'key'     => '_ywgc_balance_total',
							'value'   => pow( 10, - wc_get_price_decimals() ),
							'compare' => '<'
						)
					);
				}

				$query->set( 'meta_query', $meta_query );
			}
		}

		/**
		 * Booking Search
		 *
		 * @param WP_Query $wp
		 */
		public function gift_card_search( $wp ) {
			global $pagenow, $wpdb;

			if ( 'edit.php' != $pagenow || empty( $wp->query_vars[ 's' ] ) || $wp->query_vars[ 'post_type' ] != 'gift_card' ) {
				return;
			}

			// Query to check if the search value is a postmeta included in the gift cards, name, email, etc
			$query_by_meta_value = "SELECT DISTINCT post_id FROM {$wpdb->posts} wposts, {$wpdb->postmeta} wpostmeta
						WHERE meta_value LIKE '%%%s%%'";

			// Query to get the gift cards by the code/title
			$query_by_code = "SELECT DISTINCT ID FROM {$wpdb->posts} wposts
						WHERE wposts.post_type = 'gift_card'
						AND wposts.post_title LIKE '%%%s%%' ";

			$gift_by_meta_array = $wpdb->get_col( $wpdb->prepare( $query_by_meta_value , $_GET[ 's' ] ) );

			$gift_by_code_array = $wpdb->get_col( $wpdb->prepare( $query_by_code , $_GET[ 's' ] ) );

			$gift_array =  array_unique( array_merge( $gift_by_meta_array, $gift_by_code_array ) );

			if ( is_array( $gift_array ) ) {

				unset( $wp->query_vars[ 's' ] );

				$wp->query_vars[ 'gift_card_search' ] = true;

				$wp->query_vars[ 'post__in' ] = array_merge( $gift_array, array( 0 ) );
			}

		}

		public function gift_card_search_label( $query ) {
			global $pagenow, $typenow;

			if ( 'edit.php' != $pagenow ) {
				return $query;
			}

			if ( $typenow != 'gift_card' ) {
				return $query;
			}

			if ( !get_query_var( 'gift_card_search' ) ) {
				return $query;
			}

			return wp_unslash( $_GET[ 's' ] );
		}


		/**
		 * Query vars for custom searches.
		 *
		 * @param mixed $public_query_vars
		 * @return array
		 */
		public function add_custom_query_var_gift_cards( $public_query_vars ) {
			$public_query_vars[] = 'gift_card_search';

			return $public_query_vars;
		}

		/**
		 * Hide item meta from the orders.
		 */
		public function ywgc_hidden_order_item_meta( $meta_array ) {
			$meta_array[] = '_ywgc_design';

			return apply_filters( 'yith_cog_order_item_meta', $meta_array );
		}


		/**
		 * Localize order item meta and show theme as strings
		 *
		 * @param $display_key
		 * @param $meta
		 * @param $order_item
		 * @return string|void
		 */
		public function show_as_string_order_item_meta_key($display_key){
			if( strpos($display_key,'ywgc') !== false){
				if( $display_key == '_ywgc_product_id' ){
					$display_key = esc_html__('Product ID','yith-woocommerce-gift-cards');
				}
				elseif( $display_key == '_ywgc_product_as_present' ){
					$display_key = esc_html__('Product as a present','yith-woocommerce-gift-cards');
				}
				elseif( $display_key == '_ywgc_present_product_id' ){
					$display_key = esc_html__('Present product ID','yith-woocommerce-gift-cards');
				}
				elseif( $display_key == '_ywgc_present_variation_id' ){
					$display_key = esc_html__('Present variation ID','yith-woocommerce-gift-cards');
				}
				elseif( $display_key == '_ywgc_amount' ){
					$display_key = esc_html__('Amount','yith-woocommerce-gift-cards');
				}
				elseif( $display_key == '_ywgc_is_digital' ){
					$display_key = esc_html__('Digital','yith-woocommerce-gift-cards');
				}
				elseif( $display_key == '_ywgc_sender_name' ){
					$display_key = esc_html__('Sender\'s name','yith-woocommerce-gift-cards');
				}
				elseif( $display_key == '_ywgc_recipient_name' ){
					$display_key = esc_html__('Recipient\'s name','yith-woocommerce-gift-cards');
				}
				elseif( $display_key == '_ywgc_message' ){
					$display_key = esc_html__('Message','yith-woocommerce-gift-cards');
				}
				elseif( $display_key == '_ywgc_design_type' ){
					$display_key = esc_html__('Design type','yith-woocommerce-gift-cards');
				}
				elseif( $display_key == '_ywgc_design' ){
					$display_key = esc_html__('Design','yith-woocommerce-gift-cards');
				}
				elseif( $display_key == '_ywgc_subtotal' ){
					$display_key = esc_html__('Subtotal','yith-woocommerce-gift-cards');
				}
				elseif( $display_key == '_ywgc_subtotal_tax' ){
					$display_key = esc_html__('Subtotal tax','yith-woocommerce-gift-cards');
				}
				elseif( $display_key == '_ywgc_version' ){
					$display_key = esc_html__('Version','yith-woocommerce-gift-cards');
				}
				elseif( $display_key == '_ywgc_delivery_date' ){
					$display_key = esc_html__('Delivery date','yith-woocommerce-gift-cards');
				}
				elseif( $display_key == '_ywgc_postdated' ){
					$display_key = esc_html__('Postdated','yith-woocommerce-gift-cards');
				}


			}
			return $display_key;
		}

		/**
		 * Format date to show as meta value in order page
		 * @param $meta_value
		 * @param $meta
		 * @return mixed
		 */
		public function show_formatted_date( $meta_value, $meta ="", $item="" ){

			if( '_ywgc_delivery_date' == $meta->key ){
				$date_format = apply_filters( 'yith_wcgc_date_format','Y-m-d' );
				$meta_value = date_i18n( $date_format,$meta_value ) . ' (' . $date_format . ')';
			}

			return $meta_value;

		}

		/**
		 * Update gift card amount in case the order is cancelled or refunded
		 * @param $order_id
		 * @param $from_status
		 * @param $to_status
		 * @param bool $order
		 */
		public function update_gift_card_amount_on_order_status_change( $order_id, $from_status, $to_status, $order = false ){
			$is_gift_card_amount_refunded = yit_get_prop($order,'_ywgc_is_gift_card_amount_refunded');
			if( ($to_status == 'cancelled' || ( $to_status == 'refunded' ) || ( $to_status == 'failed' )) && $is_gift_card_amount_refunded != 'yes' ){
				$gift_card_applied = yit_get_prop( $order,'_ywgc_applied_gift_cards',true );
				if (empty($gift_card_applied)) {
					return;
				}

				foreach ($gift_card_applied as $gift_card_code => $gift_card_value  ){
					$args = array(
						'gift_card_number' => $gift_card_code
					);
					$gift_card = new YITH_YWGC_Gift_Card( $args );
					$new_amount = $gift_card->get_balance() + $gift_card_value;

					if ( apply_filters( 'yith_ywgc_restore_gift_card_balance', true, $gift_card ) ) {
						$gift_card->update_balance( $new_amount );
					}
				}

				yit_save_prop($order,'_ywgc_is_gift_card_amount_refunded','yes');
			}
		}

		public function update_gift_card_as_coupon_amount_on_order_status_change( $order_id, $from_status, $to_status, $order = false ) {
			if ( $to_status == 'cancelled' || $to_status == 'refunded' || $to_status == 'failed' ) {
				$order = wc_get_order( $order_id );
				$coupons = $order->get_coupons();

				foreach ( $coupons as $coupon ) {
					$args = array(
						'gift_card_number' => $coupon->get_code()
					);

					$gift_card = new YITH_YWGC_Gift_Card( $args );

					if ( ! $gift_card->exists() ) {
						continue;
					}

					$new_amount = $gift_card->get_balance() + $coupon->get_discount() + $coupon->get_discount_tax();

					if ( apply_filters( 'yith_ywgc_restore_gift_card_balance', true, $gift_card ) ) {
						$gift_card->update_balance( $new_amount );
					}
				}

				$gc_applied_as_fees = yit_get_prop( $order, '_ywgc_applied_gift_cards_as_fees');

				if ( $gc_applied_as_fees ){

					$coupons_array = get_post_meta( $order_id, 'ywgc_applied_coupons_array' );

					foreach ( $coupons_array as $coupons ) {

						foreach ( $coupons as $code ) {

							$args = array(
								'gift_card_number' => $code
							);

							$gift_card = new YITH_YWGC_Gift_Card( $args );

							if ( ! $gift_card->exists() ) {
								continue;
							}
							$total_fees_amount = get_post_meta( $order_id, 'ywgc_applied_coupons_as_fees_total' );

							$new_amount = $gift_card->get_balance() + $total_fees_amount[0];
							$gift_card->update_balance( $new_amount );
						}
					}

				}

			}
		}

		public function update_totals_on_save_order_items( $order, $data_store ){

			if( $order->get_status() == 'wc-refunded' )
				return;

				$order_id = $order->get_id();

				$used_gift_cards       = get_post_meta( $order_id, '_ywgc_applied_gift_cards', true );
				$used_gift_cards_total = get_post_meta( $order_id, '_ywgc_applied_gift_cards_totals', true );

				if ( ! $used_gift_cards ) {
					return;
				}

				$applied_codes = array();
				foreach ( $used_gift_cards as $code => $amount ){
					$applied_codes[] = $code;
				}

				$applied_codes_string = implode(', ', $applied_codes );

				$order_total = $order->get_total();
				$order_aux_total = get_post_meta( $order_id, '_ywgc_applied_gift_cards_order_total', true );

				if ( ! get_post_meta( $order_id, 'ywgc_gift_card_updated_as_fee', true ) && !empty( $order_aux_total ) && $order_total != $order_aux_total   ){

					foreach ( $order->get_items( 'tax' ) as $item_id => $item_tax ) {
						$tax_data = $item_tax->get_data();
						$tax_rate = $tax_data['rate_percent'];
					}

					$tax_rate = isset( $tax_rate ) ? $tax_rate : '0';

					$rate_aux = '0.' . $tax_rate;

					$item = new WC_Order_Item_Fee();

					$amount = round( - 1 * ( (float)$used_gift_cards_total / ( 1 + (float)$rate_aux ) ), 2 );

					// add coupons as fees
					$item->set_props( array(
						'id'       => '_ywgc_fee',
						'name'     => 'Gift Card (' . $applied_codes_string . ')',
						'total'    => floatval( $amount ),
						'order_id' => $order_id,
					) );

					$order->add_item( $item );

					update_post_meta( $order_id, 'ywgc_gift_card_updated_as_fee', true );
				}

		}


		public function ywgc_convert_smart_coupons_to_gift_cards(){

			global $wpdb;

			$date_format = apply_filters('yith_wcgc_date_format','Y-m-d');

			$this->offset  = intval( $_POST['offset'] );
			$this->limit   = intval( $_POST['limit'] );

			if ( $this->limit == 0 ) {
				$this->limit = 50 ;
			}

			$query_coupons = "SELECT DISTINCT post_id FROM {$wpdb->postmeta} WHERE meta_key= 'discount_type' AND meta_value= 'smart_coupon'";
			$coupons_array = $wpdb->get_results($query_coupons);
			$total_coupons_number = count($coupons_array);

			if ( $this->limit > $total_coupons_number ){
				$counter = $total_coupons_number;
			}
			else{
				$counter = $this->offset + $this->limit;
			}

			foreach ($coupons_array as $coupons) {

				$coupon_id = $coupons->post_id;

				for ($i = $this->offset; $i < $counter; $i++) {
					$coupon_amount = get_post_meta($coupon_id, 'coupon_amount', true);
					$recipient_emails_array = get_post_meta($coupon_id, 'customer_email', true);
					$expiration_timestamp = get_post_meta($coupon_id, 'date_expires', true);
					$expiration_formatted = $expiration_timestamp != '0' ? date_i18n ( $date_format, $expiration_timestamp ) : '';
					$coupon_code = get_the_title($coupon_id);
				}

				$query_duplicated_post = "SELECT ID FROM {$wpdb->posts} WHERE post_title = '{$coupon_code}' AND post_type = 'gift_card' ";
				$duplicated_post_array = $wpdb->get_results($query_duplicated_post);

				foreach ($duplicated_post_array as $duplicated_post) {
					$duplicated_post_id = $duplicated_post->ID;
				}
				if ( $coupon_code == get_the_title( $duplicated_post_id ) ) {
					continue;
				}
				else{
					$new_draft_post = array(
						'post_title' => $coupon_code,
						'post_status' => 'draft',
						'post_type' => 'gift_card',
					);

					$post_id = wp_insert_post($new_draft_post);

					$updated_post = array(
						'ID' => $post_id,
						'post_title' => $coupon_code,
						'post_status' => 'publish',
						'post_type' => 'gift_card'
					);

					wp_update_post($updated_post);
					update_post_meta($post_id, '_ywgc_amount_total', $coupon_amount);
					update_post_meta($post_id, '_ywgc_balance_total', $coupon_amount);
					update_post_meta($post_id, '_ywgc_is_digital', '1');
					update_post_meta($post_id, '_ywgc_expiration', $expiration_timestamp);
					update_post_meta($post_id, '_ywgc_expiration_date_formatted', $expiration_formatted);
					update_post_meta($post_id, '_ywgc_recipient', $recipient_emails_array['0']);

				}
			}

			$new_offset = $this->offset + $this->limit;

			if (($total_coupons_number - $new_offset) < $this->limit){
				$this->limit = $total_coupons_number - $new_offset;
			}

			if ( $new_offset < $total_coupons_number ){
				$data=array(
					"limit"=> "$this->limit",
					"offset" => "$new_offset",
					"loop" => "1",
				);

				wp_send_json( $data );
			}
			else{
				$data=array(
					"limit"=> "$this->limit",
					"offset" => "$new_offset",
					"loop" => "0",
				);
				wp_send_json( $data );
			}
		}

		/**
		 * Render the import cost buttons.
		 */
		public function yith_ywgc_transform_smart_coupons_buttons(){

			if ( class_exists( 'WC_Smart_Coupons' ) ) {
				?>
				<h2><?php _e( 'WooCommerce Smart Coupons integration', 'yith-woocommerce-gift-cards' );?></h2>
				<tr id="ywgc_ajax_zone_transform_smart_coupons">
					<th>
						<label><?php _e( 'Transfer WooCommerce Smart Coupons to YITH Gift Cards ', 'yith-woocommerce-gift-cards' );?></label>
					</th>
					<td>
						<button type="button" class="ywgc_transform_smart_coupons_class button button-primary" id="yith_ywgc_transform_smart_coupons" ><?php _e( 'Transfer', 'yith-woocommerce-gift-cards' );?></button>
						<span class="description"><?php _e( 'Transfer "Store Credit / Gift Certificate" coupons. This action cannot be undone', 'yith-woocommerce-gift-cards' ); ?></span>
					</td>
				</tr>
				<?php
			}
		}


		/**
		 * Currency Switchers options
		 */
		public function yith_ywgc_general_options_array_custom( $general_options ) {

			if ( class_exists('WC_Aelia_CurrencySwitcher') ) {

				$aux = array(
					'aelia_currency_switchers_tab_start'    => array(
						'type' => 'sectionstart',
						'id'   => 'yith_aelia_currency_switchers_settings_tab_start'
					),
					'aelia_currency_switchers_tab_title'    => array(
						'type'  => 'title',
						'name' => esc_html__( 'Aelia Currency Switcher integration', 'yith-woocommerce-gift-cards' ),
						'desc'  => '',
						'id'    => 'yith_aelia_currency_switchers_tab'
					),
					'enable_aelia_option' => array(
						'name'    => esc_html__( 'Enable Aelia integration', 'yith-woocommerce-gift-cards' ),
						'type'    => 'checkbox',
						'id'      => 'ywgc_aelia_integration_option',
						'default' => 'yes',
					),

					'aelia_currency_switchers_tab_end'      => array(
						'type' => 'sectionend',
						'id'   => 'yith_aelia_currency_switchers_settings_tab_end'
					),
				);

				$general_options['general'] = array_merge( $general_options['general'], $aux );

			}

			global $woocommerce_wpml;
			if ( $woocommerce_wpml ) {

				$aux = array(
					'wpml_currency_switchers_tab_start' => array(
						'type' => 'sectionstart',
						'id' => 'yith_wpml_currency_switchers_settings_tab_start'
					),
					'wpml_currency_switchers_tab_title' => array(
						'type' => 'title',
						'name' => esc_html__('WPML Currency Switcher integration', 'yith-woocommerce-gift-cards'),
						'desc' => '',
						'id' => 'yith_wpml_currency_switchers_tab'
					),
					'enable_wpml_option' => array(
						'name' => esc_html__('Enable WPML integration', 'yith-woocommerce-gift-cards'),
						'type' => 'checkbox',
						'id' => 'ywgc_wpml_integration_option',
						'default' => 'yes',
					),

					'wpml_currency_switchers_tab_end' => array(
						'type' => 'sectionend',
						'id' => 'yith_wpml_currency_switchers_settings_tab_end'
					),
				);

				$general_options['general'] = array_merge( $general_options['general'], $aux );
			}

			return $general_options;
		}

		function ywgc_remove_product_meta_boxes(){

			$product = wc_get_product(get_the_ID());

			if (is_object($product) &&  $product->get_type() == 'gift-card' && apply_filters( 'ywgc_remove_gallery_metabox_condition' , true ) ) {
				remove_meta_box('woocommerce-product-images', 'product', 'side');
			}
			if (is_object($product) && $product->get_type() != 'gift-card' ) {
				remove_meta_box('giftcard-categorydiv', 'product', 'side');
			}
		}


		public function ywgc_toggle_enabled_action(){

			if ( isset( $_POST['id'] ) && isset( $_POST['enabled'] ) && $_POST['enabled'] == 'no' ){
				$gift_card = new YWGC_Gift_Card_Premium( array( 'ID' => $_POST['id'] ) );
				$gift_card->set_enabled_status(false);
			}
			else if ( isset( $_POST['id'] ) && isset( $_POST['enabled'] ) && $_POST['enabled'] == 'yes') {
				$gift_card = new YWGC_Gift_Card_Premium( array( 'ID' => $_POST['id'] ) );
				$gift_card->set_enabled_status(true);
			}

		}

		public function ywgc_update_cron(){

			if ( $_POST['interval_mode'] == 'hourly' ){

				update_option( 'ywgc_delivery_mode', 'hourly' );

				wp_clear_scheduled_hook ( 'ywgc_start_gift_cards_sending' );

				wp_schedule_event( time() , 'hourly', 'ywgc_start_gift_cards_sending' );

			}
			else{

				update_option( 'ywgc_delivery_mode', 'daily' );
				update_option( 'ywgc_delivery_hour', $_POST['hour'] );

				$hour = strtotime( get_option( 'ywgc_delivery_hour', '00:00' ) );
				wp_clear_scheduled_hook ( 'ywgc_start_gift_cards_sending' );

				wp_schedule_event(strtotime('-' . get_option( 'gmt_offset' ) . ' hours', $hour ) , 'daily', 'ywgc_start_gift_cards_sending' );
			}

		}


		public function ywgc_recalculate_totals_gift_cards_as_fees( $order_id, $items ){

			$order = wc_get_order( $order_id );

			$gift_cards_fees = get_post_meta( $order_id, '_ywgc_applied_gift_cards_as_fees', true );

			$aux_cart_total = get_post_meta( $order_id, '_ywgc_aux_cart_total', true );
			$shipping_total = $order->get_shipping_total() + $order->get_shipping_tax();


			if ( $gift_cards_fees && $aux_cart_total < $shipping_total ){ //Only if the gift card is added as a fee and the shipping is higher than the order total
				$aux_cart_tax = get_post_meta($order->get_id(), '_ywgc_aux_cart_total_tax', true );
				$aux_order_total = get_post_meta($order->get_id(), '_ywgc_aux_cart_total', true );

				update_post_meta($order->get_id(), '_order_tax', $aux_cart_tax );
				update_post_meta($order->get_id(), '_order_total', $aux_order_total );

				foreach( $order->get_items( 'fee' ) as $item_id => $item_fee ){

					$aux_line_tax = wc_get_order_item_meta( $item_id, '_ywgc_aux_line_total', true );
					if ( $aux_line_tax != '' )
						wc_update_order_item_meta( $item_id, '_line_total', $aux_line_tax);

				}

				foreach( $order->get_items( 'tax' ) as $item_id => $item_tax ){
					wc_update_order_item_meta( $item_id, 'tax_amount', $aux_cart_tax );
					wc_update_order_item_meta( $item_id, 'shipping_tax_amount', '0' );
				}

				$aux_shipping = $order->get_shipping_tax() + $order->get_total_shipping();
				update_post_meta($order->get_id(), '_order_shipping', $aux_shipping );
				update_post_meta($order->get_id(), '_order_shipping_tax', 0 );
			}

		}

		/*
		 * Show the default shipping in the order summary, not the discounted one
		*/
		public function ywgc_display_default_shipping_value( $shipping, $order, $tax_display ){

			$order_gift_cards = yit_get_prop( $order, '_ywgc_applied_gift_cards', true );

			$order = wc_get_order( $order->get_id() );

			$shipping = 0;

			foreach( $order->get_items( 'shipping' ) as $item_id => $item_shipping ){

				$shipping_data = $item_shipping->get_data();

				$shipping += $shipping_data['total'] + $shipping_data['total_tax'];


			}

			if ( $order_gift_cards ){

				$shipping = wc_price( $shipping, array( 'currency' => $order->get_currency() ) );

				$shipping .= apply_filters( 'woocommerce_order_shipping_to_display_tax_label', '&nbsp;<small class="tax_label">' . WC()->countries->inc_tax_or_vat() . '</small>', $order, $tax_display );

				$shipping .= apply_filters( 'woocommerce_order_shipping_to_display_shipped_via', '&nbsp;<small class="shipped_via">' . sprintf( __( 'via %s', 'woocommerce' ), $order->get_shipping_method() ) . '</small>', $order );

			}

			return $shipping;
		}


		/**
		 * Export gift cards in CSV
		 *
		 */
		public function ywgc_export_gift_card_data() {

			global $wpdb;
			$prepare_query = 'SELECT COUNT(ID) FROM ' . $wpdb->posts . ' WHERE post_type = \'gift_card\'';
			$n_posts       = $wpdb->get_var( $prepare_query );

			if ( isset( $_REQUEST['from'] ) && isset( $_REQUEST['to'] ) ) {
				$start_filter = $_REQUEST['from'];
				$end_filter   = $_REQUEST['to'];

				$saved_format = get_option( 'ywgc_plugin_date_format_option', 'yy-mm-dd' );

				if ( $saved_format == 'MM d, yy' ) {
					$start_filter_formatted = strtotime( $start_filter );
					$end_filter_formatted   = strtotime( $end_filter );
				} else {
					$search                 = array( '.', ', ', '/', ' ', ',', 'MM', 'yy', 'mm', 'dd' );
					$replace                = array( '-', '-', '-', '-', '-', 'M', 'y', 'm', 'd' );
					$saved_format_formatted = str_replace( $search, $replace, $saved_format );

					$start_filter_formatted = str_replace( $search, $replace, $start_filter );
					$end_filter_formatted = str_replace( $search, $replace, $end_filter );

					$start_filter           = '' !== $start_filter_formatted ? 'mm/dd/yy' !== $saved_format ? date( $saved_format_formatted, strtotime( $start_filter_formatted ) ) : date( $saved_format_formatted, strtotime( $start_filter ) ) : '';

					$end_filter           = '' !== $end_filter_formatted ? 'mm/dd/yy' !== $saved_format ? date( $saved_format_formatted, strtotime( $end_filter_formatted ) ) : date( $saved_format_formatted, strtotime( $end_filter ) ) : '';

					if ( $start_filter = ! empty( $start_filter ) ? DateTime::createFromFormat( $saved_format_formatted, $start_filter ) : '' ) {
						$start_filter_formatted = $start_filter->getTimestamp();
						$start_filter_formatted = date_i18n( 'Y-m-d', $start_filter_formatted );
					}

					if ( $end_filter = ! empty( $end_filter ) ? DateTime::createFromFormat( $saved_format_formatted, $end_filter ) : '' ) {
						$end_filter_formatted = $end_filter->getTimestamp();
						$end_filter_formatted = date_i18n( 'Y-m-d', $end_filter_formatted );
					}
				}

			}
			else{
				$start_filter_formatted = '';
				$end_filter_formatted = '';
			}


			$offset     = 0;
			$gift_cards = array();
			while ( $offset < $n_posts ) {
				$args               = array(
					'posts_per_page' => 100,
					'orderby'        => 'title',
					'order'          => 'asc',
					'date_query'     => array(
						array(
							'after'     => $start_filter_formatted,
							'before'    => $end_filter_formatted,
							'inclusive' => true,
						),
					),
					'post_type'      => 'gift_card',
					'post_status'    => 'publish',
					'offset'         => $offset,
				);
				$gift_cards_to_push = get_posts( $args );

				foreach ( $gift_cards_to_push as $gift_card ) {

					$gift_cards[] = $gift_card;
				}
				$offset = $offset + 100;
			}

			$date_format = apply_filters('yith_wcgc_date_format','Y-m-d');


			if ( get_option( 'ywgc_export_option_order_id', 'yes' ) == 'yes' )
				$gift_cards_columns_labels['0'][] = __( 'Order ID', 'yith-woocommerce-gift-cards' );

			if ( get_option( 'ywgc_export_option_gift_card_id', 'yes' ) == 'yes' )
				$gift_cards_columns_labels['0'][] = __( 'Gift Card ID', 'yith-woocommerce-gift-cards' );

			if ( get_option( 'ywgc_export_option_gift_card_code', 'yes' ) == 'yes' )
				$gift_cards_columns_labels['0'][] = __( 'Code', 'yith-woocommerce-gift-cards' );

			if ( get_option( 'ywgc_export_option_gift_card_amount', 'yes' ) == 'yes' )
				$gift_cards_columns_labels['0'][] = __( 'Purchased amount', 'yith-woocommerce-gift-cards' );

			if ( get_option( 'ywgc_export_option_gift_card_balance', 'yes' ) == 'yes' )
				$gift_cards_columns_labels['0'][] = __( 'Current balance', 'yith-woocommerce-gift-cards' );

			if ( get_option( 'ywgc_export_option_sender_name', 'yes' ) == 'yes' )
				$gift_cards_columns_labels['0'][] = __( 'Sender\'s name', 'yith-woocommerce-gift-cards' );

			if ( get_option( 'ywgc_export_option_recipient_name', 'yes' ) == 'yes' )
				$gift_cards_columns_labels['0'][] = __( 'Recipient\'s name', 'yith-woocommerce-gift-cards' );

			if ( get_option( 'ywgc_export_option_recipient_email', 'yes' ) == 'yes' )
				$gift_cards_columns_labels['0'][] = __( 'Recipient\'s email', 'yith-woocommerce-gift-cards' );

			if ( get_option( 'ywgc_export_option_message', 'yes' ) == 'yes' )
				$gift_cards_columns_labels['0'][] = __( 'Message', 'yith-woocommerce-gift-cards' );

			if ( get_option( 'ywgc_export_option_expiration_date', 'yes' ) == 'yes' )
				$gift_cards_columns_labels['0'][] = __( 'Expiration date', 'yith-woocommerce-gift-cards' );

			if ( get_option( 'ywgc_export_option_delivery_date', 'yes' ) == 'yes' )
				$gift_cards_columns_labels['0'][] = __( 'Delivery date', 'yith-woocommerce-gift-cards' );

			if ( get_option( 'ywgc_export_option_internal_note', 'yes' ) == 'yes' )
				$gift_cards_columns_labels['0'][] = __( 'Internal note', 'yith-woocommerce-gift-cards' );

			$sitename = sanitize_key( get_bloginfo( 'name' ) );
			$sitename .= ( ! empty( $sitename ) ) ? '-' : '';
			$filename = $sitename . 'gift-card-export-' . gmdate( 'Y-m-d-H-i' ) . '.csv';

			$counter = 0;

			$formatted_gift_card_data = array();

			foreach ( $gift_cards as $gift_card ) {

				$gift_card_object = new YWGC_Gift_Card_Premium( array( 'ID' => yit_get_prop( $gift_card, 'ID' ) ) );

				if ( get_option( 'ywgc_export_option_order_id', 'yes' ) == 'yes' )
					$formatted_gift_card_data[$counter][] = $gift_card_object->order_id;

				if ( get_option( 'ywgc_export_option_gift_card_id', 'yes' ) == 'yes' )
					$formatted_gift_card_data[$counter][] = yit_get_prop( $gift_card, 'ID' );

				if ( get_option( 'ywgc_export_option_gift_card_code', 'yes' ) == 'yes' )
					$formatted_gift_card_data[$counter][] = $gift_card_object->get_code();

				if ( get_option( 'ywgc_export_option_gift_card_amount', 'yes' ) == 'yes' )
					$formatted_gift_card_data[$counter][] = $gift_card_object->total_amount;

				if ( get_option( 'ywgc_export_option_gift_card_balance', 'yes' ) == 'yes' )
					$formatted_gift_card_data[$counter][] = $gift_card_object->get_balance();

				if ( get_option( 'ywgc_export_option_sender_name', 'yes' ) == 'yes' )
					$formatted_gift_card_data[$counter][] = $gift_card_object->sender_name;

				if ( get_option( 'ywgc_export_option_recipient_name', 'yes' ) == 'yes' )
					$formatted_gift_card_data[$counter][] = $gift_card_object->recipient_name;

				if ( get_option( 'ywgc_export_option_recipient_email', 'yes' ) == 'yes' )
					$formatted_gift_card_data[$counter][] = $gift_card_object->recipient;

				if ( get_option( 'ywgc_export_option_message', 'yes' ) == 'yes' )
					$formatted_gift_card_data[$counter][] = $gift_card_object->message;

				if ( get_option( 'ywgc_export_option_expiration_date', 'yes' ) == 'yes' )
					if ( $gift_card_object->expiration > 0)
						$formatted_gift_card_data[$counter][] = date_i18n ( $date_format, $gift_card_object->expiration );

				if ( get_option( 'ywgc_export_option_delivery_date', 'yes' ) == 'yes' )
					if ( $gift_card_object->delivery_date > 0){
						$formatted_gift_card_data[$counter][] = date_i18n ( $date_format, $gift_card_object->delivery_date );
					}
					else {
					if ( $gift_card_object->delivery_send_date > 0 )
						$formatted_gift_card_data[$counter][] = date_i18n ( $date_format, $gift_card_object->delivery_send_date );
					}

				if ( get_option( 'ywgc_export_option_internal_note', 'yes' ) == 'yes' )
					$formatted_gift_card_data[$counter][] = $gift_card_object->internal_notes;

				$counter++;
			}

			$formatted_gift_card_data = array_merge( $gift_cards_columns_labels, $formatted_gift_card_data );

			header('Content-Type: text/csv');
			header('Content-Disposition: attachment; filename="' . $filename . '";');

			$df = fopen( 'php://output', 'w' );

			foreach ( $formatted_gift_card_data as $row ) {
				fputcsv( $df, $row, get_option( 'ywgc_csv_delimitier', ';' ));
			}

			fclose( $df );
			die();

		}


		public function import_actions_from_settings_panel() {

			if ( ! isset( $_REQUEST['page'] ) || 'yith_woocommerce_gift_cards_panel' != $_REQUEST['page'] || ! isset( $_REQUEST['ywgc_safe_submit_field'] ) ) {
				return;
			}

			if ( $_REQUEST['ywgc_safe_submit_field'] == 'importing_gift_cards' ) {


				if ( ! isset( $_FILES['file_import_csv'] ) || ! is_uploaded_file( $_FILES['file_import_csv']['tmp_name'] ) ) {
					return;
				}

				$uploaddir = wp_upload_dir();

				$temp_name = $_FILES['file_import_csv']['tmp_name'];
				$file_name = $_FILES['file_import_csv']['name'];

				if ( ! move_uploaded_file( $temp_name, $uploaddir['basedir'] . '\\' . $file_name ) ) {
					return;
				}

				$this->import_from_csv( $uploaddir['basedir'] . '\\' . $file_name, get_option( 'ywgc_csv_delimitier', ';' ) );

			}

		}


		/**
		 * Import points from a csv file
		 *
		 * @param $file
		 *
		 * @param $delimiter
		 * @param $format
		 * @param $action
		 *
		 * @return mixed|void
		 */
		public function import_from_csv( $file, $delimiter ) {

			$response = '';
			$this->import_start();

			$loop = 0;

			if ( ( $handle = fopen( $file, 'r' ) ) !== false ) {

				$header = fgetcsv( $handle, 0, $delimiter );

				if ( sizeof( $header ) == 12 ) {

					while ( ( $row = fgetcsv( $handle, 0, $delimiter ) ) !== false ) {


						if ( ! is_array( $row ) || count( $row ) < 12 ) {
							continue;
						}

						$order_id = $row[0];
						$gift_card_id = $row[1];
						$gift_card_code = $row[2];
						$purchased_amount = $row[3];
						$current_balance = $row[4];
						$sender_name = $row[5];
						$recipient_name = $row[6];
						$recipient_email = $row[7];
						$message = $row[8];
						$expiration_date = $row[9];
						$delivery_date = $row[10];
						$internal_note = $row[11];


						if ( $gift_card_id == '' ){
							$gift_card = new YWGC_Gift_Card_Premium();

							// For the new imported gift cards, we check if the code exist
							$check_code = get_page_by_title ( $gift_card_code, OBJECT, YWGC_CUSTOM_POST_TYPE_NAME );

							if ( ! $check_code ) {
								$gift_card->gift_card_number = $gift_card_code;
							}
							else{
								$gift_card->gift_card_number = YITH_YWGC ()->generate_gift_card_code();
							}

						}
						else{
							$gift_card = new YWGC_Gift_Card_Premium( array( 'ID' => $gift_card_id ) );
						}


						$saved_format = get_option( 'ywgc_plugin_date_format_option', 'yy-mm-dd' );

						if ( $saved_format == 'MM d, yy' ) {
							$delivery_date_timestamp   = strtotime( $delivery_date );
							$expiration_date_timestamp = strtotime( $expiration_date );
						} else {
							$search                 = array( '.', ', ', '/', ' ', ',', 'MM', 'yy', 'mm', 'dd' );
							$replace                = array( '-', '-', '-', '-', '-', 'M', 'y', 'm', 'd' );
							$saved_format_formatted = str_replace( $search, $replace, $saved_format );

							// Delivery date.
							$date_formatted = str_replace( $search, $replace, $delivery_date );
							$delivery_date  = '' !== $date_formatted ? 'mm/dd/yy' !== $saved_format ? date( $saved_format_formatted, strtotime( $date_formatted ) ) : date( $saved_format_formatted, strtotime( $delivery_date ) ) : '';

							if ( $delivery_date = ! empty( $delivery_date ) ? DateTime::createFromFormat( $saved_format_formatted, $delivery_date ) : '' ) {
								$delivery_date_timestamp = $delivery_date->getTimestamp();
							}

							// Expiration date.
							$date_formatted  = str_replace( $search, $replace, $expiration_date );
							$expiration_date = '' !== $date_formatted ? 'mm/dd/yy' !== $saved_format ? date( $saved_format_formatted, strtotime( $date_formatted ) ) : date( $saved_format_formatted, strtotime( $expiration_date ) ) : '';

							if ( $expiration_date = ! empty( $expiration_date ) ? DateTime::createFromFormat( $saved_format_formatted, $expiration_date ) : '' ) {
								$expiration_date_timestamp = $expiration_date->getTimestamp();
							}
						}


						$gift_card->order_id          = $order_id;
						$gift_card->total_amount      = $purchased_amount;
						$gift_card->update_balance( $current_balance );
						$gift_card->sender_name       = $sender_name;
						$gift_card->recipient_name    = $recipient_name;
						$gift_card->recipient         = $recipient_email;
						if ( $gift_card->recipient )
							$gift_card->is_digital = true;
						$gift_card->message            = $message;
						$gift_card->expiration         = $expiration_date_timestamp;
						$gift_card->delivery_send_date = $delivery_date_timestamp;
						$gift_card->internal_notes     = $internal_note;

						$gift_card->save ();

						$loop++;

					}

					$response = $loop;
					$this->admin_notices[] = array(
						'class'   => 'ywgc_import_result success',
						'message' => esc_html__( 'The CSV has been imported.', 'yith-woocommerce-gift-cards' ),
					);
				} else {

					$this->admin_notices[] = array(
						'class'   => 'ywgc_import_result error',
						'message' => esc_html__( 'The CSV is invalid. Check the sample file.', 'yith-woocommerce-gift-cards' ),
					);
				}

				fclose( $handle );
			}

			return $response;
		}

		/**
		 * Start import
		 *
		 * @return void
		 * @since 1.0.0
		 */
		private function import_start() {
			if ( function_exists( 'gc_enable' ) ) {
				gc_enable();
			}
			@set_time_limit( 0 );
			@ob_flush();
			@flush();
			@ini_set( 'auto_detect_line_endings', '1' );
		}


		/**
		 * Shows messages if there are update errors
		 */
		public function ywgc_show_upload_error() {

			if ( ! $this->admin_notices ) {
				return;
			}

			foreach ( $this->admin_notices as $admin_notice ) {
				printf( '<div class="ywgc_notices %s"><p>%s</p></div>', esc_attr( $admin_notice['class'] ), wp_kses_post( $admin_notice['message'] ) );
			}

		}

		/**
		 * Shows the category images and upload button in the category edit pages
		 */
		public function ywgc_include_upload_image_categories( $tag, $taxonomy ) {

			$object_ids = get_objects_in_term ( $tag->term_id, YWGC_CATEGORY_TAXONOMY );

			$term = $_REQUEST['tag_ID'];

			$term_obj = get_term( $term );

			if ( $term_obj->slug == 'all' ||  $term_obj->slug == 'none' ){
				return;
			}

			?>
			<label class="ywgc-category-image-title"><?php esc_html_e( 'Images in this category', 'yith-woocommerce-gift-cards' ); ?></label>
			<div class="ywgc-category-images-main-container">
			<?php

			foreach ( array_reverse( $object_ids ) as $item_id ){
				?><div class="ywgc-category-image" data-design-id="<?php echo $item_id; ?>" data-design-cat="<?php echo $term; ?>">
					<span class="dashicons dashicons-no ywgc-category-image-delete" title="<?php esc_html_e( 'Remove image from this category', 'yith-woocommerce-gift-cards' ); ?>"></span>
					<?php echo wp_get_attachment_image( intval( $item_id ), apply_filters( 'yith_ywgc_preset_image_size', 'thumbnail' ) ); ?>
				</div><?php
			}
			?></div><?php


			$field = array(
				'id'      => 'ywgc-upload-images-cat-edit',
				'type'    => 'image-gallery',
				'name'    => 'ywgc-edit-images-ids',
			);

			yith_plugin_fw_get_field( $field, true );

			?><p class="ywgc-category-image-description description"><?php esc_html_e( 'Select the images to be included in this category.', 'yith-woocommerce-gift-cards' ); ?></p><?php

			}


		/**
		 * Manage the image upload in the category edit pages
		 */
		public function ywgc_upload_image_actions_from_settings_panel() {

			if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'editedtag'  ) {

				if ( isset( $_REQUEST['ywgc-edit-images-ids'] ) && isset( $_REQUEST['tag_ID'] ) ){

					$images_ids = $_REQUEST['ywgc-edit-images-ids'];

					$images_ids_array = explode(',', $images_ids);

					$term_id = array( $_REQUEST['tag_ID'] );

					foreach ( $images_ids_array as $image_id ){
						wp_set_post_terms( $image_id, $term_id, YWGC_CATEGORY_TAXONOMY );
					}

				}
			}

		}

		/**
		 * ajax method to delete images from categories in the category edit pages
		 */
		public function ywgc_delete_image_from_category() {

			$image_id = isset( $_POST['image_id'] ) ? $_POST['image_id'] : '' ;
			$cat_id = isset( $_POST['cat_id'] ) ? $_POST['cat_id'] : '' ;

			if ( $image_id == '' ||  $cat_id == '' ){
				return;
			}

			wp_remove_object_terms( (int)$image_id, (int)$cat_id, YWGC_CATEGORY_TAXONOMY );

			wp_send_json ( array( "code"  => 1 ) );
		}



		/**
		 * Shows the category images and upload button in the category creation
		 */
		public function ywgc_add_form_fields_category_creation( $taxonomy ) {

			$field = array(
				'id'      => 'ywgc-upload-images-cat-creation',
				'type'    => 'image-gallery',
				'name'    => 'ywgc-uploaded-images-ids',
			);


			?>
			<div class="form-field term-image-wrap">
				<label for="parent"><?php esc_html_e( 'Images in this category', 'yith-woocommerce-gift-cards' ); ?></label>
				<?php yith_plugin_fw_get_field( $field, true ); ?>
				<p><?php esc_html_e( 'Select the images to be included in this category.', 'yith-woocommerce-gift-cards' ); ?></p>
			</div>
			<?php
		}

		/**
		 * Save the category images on category creation
		 */
		public function ywgc_save_images_on_category_creation( $term_id, $tt_id = '', $taxonomy = '' ) {

			if ( isset( $_POST['ywgc-uploaded-images-ids'] ) ) {

				$images_ids = $_POST['ywgc-uploaded-images-ids'];

				$images_ids_array = explode( ',', $images_ids );

				foreach ( $images_ids_array as $image_id ) {
					wp_set_post_terms( $image_id, array( $term_id ), YWGC_CATEGORY_TAXONOMY );
				}
			}
		}

		/**
		 * Apply the gift card in the subscription renew
		 */

		public function apply_gift_cards_on_subscription_renew( $order_id, $subscription_id ){

			$renew_order = wc_get_order( $order_id );
			$subscription   = ywsbs_get_subscription( $subscription_id );
			$parent_order   = $subscription->get_order();

			$parent_order_gift_cards = get_post_meta( $parent_order->get_id(), '_ywgc_applied_gift_cards', true );

			$parent_coupons = $parent_order->get_coupons();

			foreach ( $parent_coupons as $coupon ) {
				$args = array(
					'gift_card_number' => $coupon->get_code()
				);

				$gift_card = new YITH_YWGC_Gift_Card( $args );

				if ( $gift_card->exists() ) {

					if ( is_array( $parent_order_gift_cards ) ){
						$parent_order_gift_cards[ $coupon->get_code()] = (float)$coupon->get_discount() + (float)$coupon->get_discount_tax();
					}
					else{
						$parent_order_gift_cards = array();
						$parent_order_gift_cards[ $coupon->get_code()] = (float)$coupon->get_discount() + (float)$coupon->get_discount_tax();
					}

				}

			}

			if ( $parent_order_gift_cards ){

				foreach ( $parent_order_gift_cards as $code => $amount ){
					$gift_card = YITH_YWGC()->get_gift_card_by_code( $code );

					$gift_card_balance = $gift_card->get_balance();
					$renew_order_total = $renew_order->get_total();

					if ( $gift_card_balance >= $renew_order_total ){

						$amount_to_substract_to_gift_card = $renew_order_total;
						$new_balance = $gift_card_balance - $amount_to_substract_to_gift_card;

						$order_note = sprintf( __( 'Renew order paid with the gift card "%s" applied to the main subscription order with a value of %s. Remaining balance in the gift card: %s' ), $code,  wc_price( $amount_to_substract_to_gift_card ) , wc_price( $new_balance ) );

						$gift_card->update_balance( $new_balance );

						$renew_order->set_total( $renew_order->get_total() - $amount_to_substract_to_gift_card );
						$renew_order->set_status( 'processing' );
						$renew_order->add_order_note( $order_note );
						$renew_order->save();
					}

				}

			}

		}






	}
}
