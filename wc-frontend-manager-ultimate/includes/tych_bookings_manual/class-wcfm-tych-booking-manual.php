<?php 
/**
 * Bookings and Appointment Plugin for WooCommerce
 *
 * Class for handling Manual Bookings using Bookings->Create Booking
 *
 * @author   Tyche Softwares
 * @package  BKAP/Admin-Bookings
 * @category Classes
 *
 */
if ( !class_exists( 'WCFM_Tych_Manual_Bookings' ) ) {

    /**
     * Class for creating Manual Bookings using Bookings->Create Booking
     * @class WCFM_Tych_Manual_Bookings
     */

	class WCFM_Tych_Manual_Bookings {

	    /**
	     * Stores errors.
	     *
	     * @var array
	     */

	    private $errors = array();

        /**
         * Default Constructor.
         *
         * @since 4.1.0
         */
	     
		public function __construct() {
		    add_action( 'woocommerce_order_after_calculate_totals', array( &$this, 'woocommerce_order_after_calculate_totals_callback' ), 10, 2 );
		}

        /**
         * Updating the price in the booking when discount is appied from Edit Order page.
         *
         * @param bool $and_taxes true if calculation for taxes else false.
         * @param Object $order Shop Order post
         * @since 4.9.0
         *
         * @hook woocommerce_order_after_calculate_totals
         */

        function woocommerce_order_after_calculate_totals_callback( $and_taxes, $order ) {
            
            $item_values = $order->get_items();

            foreach ( $item_values as $cart_item_key => $values ) {
                
                $product_id = $values['product_id'];
                $bookable   = bkap_common::bkap_get_bookable_status( $product_id );

                if( ! $bookable ) {
                    continue;
                }
                    
                $booking_id     = bkap_common::get_booking_id( $cart_item_key );
                $item_quantity  = $values->get_quantity(); // Get the item quantity
                $item_total     = $values->get_total();
                $item_tax       = $values->get_total_tax();
                $item_total     = $item_total + $item_tax;
                $item_total     = $item_total / $item_quantity;
                
                // update booking post meta
                update_post_meta( $booking_id, '_bkap_cost', $item_total );
            }
        }

		/**
		 * Loads the Create Booking Pages or saves the booking based on
		 * the data passed in $_POST
		 *
		 * @since 4.1.0
		 */

		static function bkap_create_booking_page() {
		    
		    $bookable_product_id = 0;		    
		    $WCFM_Tych_Manual_Bookings = new WCFM_Tych_Manual_Bookings();
		    
		    $step = 1;

		    try {
           if ( ! empty( $_POST[ 'bkap_create_booking' ] ) ) {
    		        
							$customer_id         = isset( $_POST[ 'customer_id' ] ) ? absint( $_POST[ 'customer_id' ] ) : 0;    				
							$bookable_product_id = absint( $_POST[ 'bkap_product_id' ] );
							$booking_order       = wc_clean( $_POST[ 'bkap_order' ] );
			
							if ( ! $bookable_product_id ) {
								throw new Exception( __( 'Please choose a bookable product', 'woocommerce-booking' ) );
							}
			
							if ( 'existing' === $booking_order ) {
								$order_id      = absint( $_POST[ 'bkap_order_id' ] );
								$booking_order = $order_id;
			
								if ( ! $booking_order || get_post_type( $booking_order ) !== 'shop_order' ) {
									throw new Exception( __( 'Invalid order ID provided', 'woocommerce-booking' ) );
								}
							}
									
							$bkap_data[ 'customer_id' ] = $customer_id;
							$bkap_data[ 'product_id' ]  = $bookable_product_id;
							$bkap_data[ 'order_id' ]    = $booking_order;
							$bkap_data[ 'bkap_order' ]  = $_POST[ 'bkap_order' ];
    		      $step++;
    		        
    		    } else if ( ! empty( $_POST[ 'bkap_create_booking_2' ] ) ) {
    		        
    		        $create_order = ( 'new' === $_POST[ 'bkap_order' ] ) ? true : false;
    		    
                    // validate the booking data
    		        $validations  = true;
    		        $_product     = wc_get_product( $_POST[ 'bkap_product_id' ] );

    		        if ( $_product->post_type === 'product_variation' ) {
    		            $settings_id = $_product->get_parent_id();
    		        } else {
    		            $settings_id = $_POST[ 'bkap_product_id' ];
    		        }

    		        if ( $_POST[ 'wapbk_hidden_date' ] === '' ) {
    		            $validations = false;
    		        }
    		        
    		        $booking_type = get_post_meta( $settings_id, '_bkap_booking_type', true );

                    switch ( $booking_type ) {
                        case 'multiple_days':
                            if ( $_POST[ 'wapbk_hidden_date_checkout' ] === '' ) {
                                $validations = false;
                            }
                            break;
                        case 'date_time':
                            if ( $_POST[ 'time_slot' ] === '' ) {
                                $validations = false;
                            }
                            break;
                        case 'duration_time':
                            if ( $_POST[ 'duration_time_slot' ] === '' ) {
                                $validations = false;
                            }
                            break;
                    }
    		        
    		        if ( ! $validations ) {
    		            throw new Exception( __( 'Please select the Booking Details.', 'woocommerce-booking' ) );
    		        }
    		        
    		        // setup the data
    		        $time_slot          = ( isset( $_POST[ 'time_slot' ] ) ) ? $_POST[ 'time_slot' ] : '';

                    $duration_time_slot = ( isset( $_POST[ 'duration_time_slot' ] ) ) ? $_POST[ 'duration_time_slot' ] : '';
    		        
                    $checkout_date      = ( isset( $_POST[ 'wapbk_hidden_date_checkout' ] ) && '' != $_POST[ 'wapbk_hidden_date_checkout' ] ) ? $_POST[ 'wapbk_hidden_date_checkout' ] : '';
    		        
    		        $booking_details[ 'product_id' ]  = $_POST[ 'bkap_product_id' ];
    		        $booking_details[ 'customer_id' ] = $_POST[ 'bkap_customer_id' ];
    		        
    		        if ( $time_slot !== '' ) {
    		            $times        = explode( '-', $time_slot );
    		            $start_time   = ( isset( $times[ 0 ] ) && '' !== $times[ 0 ] ) ? date( 'H:i', strtotime( $times[ 0 ] ) ) : '00:00';
    		            $end_time     = ( isset( $times[ 1 ] ) && '' !== $times[ 1 ] ) ? date( 'H:i', strtotime( $times[ 1 ] ) ) : '00:00';
    
    		            $booking_details[ 'start' ]   = strtotime( $_POST[ 'wapbk_hidden_date' ] . $start_time );
    		            $booking_details[ 'end' ]     = strtotime( $_POST[ 'wapbk_hidden_date' ] . $end_time );
    		             
    		        } else if ( $checkout_date !== '' ) {
    		            $booking_details[ 'start' ]   = strtotime( $_POST[ 'wapbk_hidden_date' ] );
    		            $booking_details[ 'end' ]     = strtotime( $checkout_date );
    		        } else if( $duration_time_slot !== '' ) {

                        $d_setting = get_post_meta( $settings_id, '_bkap_duration_settings', true );

                        $start_date                 = $_POST[ 'wapbk_hidden_date' ]; // hiddendate
                        $booking_details[ 'start' ] = strtotime( $start_date." ".$duration_time_slot ); // creating start date based on date and time

                        $selected_duration          = $_POST['bkap_duration_field']; // selected duration
                        $duration                   = $d_setting['duration']; // Numbers of hours set for product

                        $hour                       = $selected_duration * $duration; // calculating numbers of duration by customer
                        $d_type                     = $d_setting['duration_type']; // hour/min

                        $booking_details[ 'end' ]   = bkap_common::bkap_add_hour_to_date( $start_date, $duration_time_slot, $hour, $settings_id, $d_type );

                        $booking_details['duration'] = $hour."-".$d_type;

                    } else {
    		            $booking_details[ 'start' ]   = strtotime( $_POST[ 'wapbk_hidden_date' ] );
    		            $booking_details[ 'end' ]     = strtotime( $_POST[ 'wapbk_hidden_date' ] );
    		        }

    		        $booking_details[ 'price' ] = $_POST[ 'bkap_price_charged' ];
                    
                    if ( isset( $_POST['bkap_front_resource_selection'] ) && $_POST['bkap_front_resource_selection'] != "" ) {
                        $booking_details[ 'bkap_resource_id' ] = $_POST[ 'bkap_front_resource_selection' ];
                    }
    
    		        if ( 'new' == $_POST[ 'bkap_order' ] ) {
    		            // create a new order
    		            $status = import_bookings::bkap_create_order( $booking_details, false );
    		            // get the new order ID
    		            $order_id = ( absint( $status[ 'order_id' ] ) > 0 ) ? $status[ 'order_id' ] : 0;
    		            
    		        } else {
    		            $order_id = ( isset( $_POST[ 'bkap_order_id' ] ) ) ? $_POST[ 'bkap_order_id' ] : 0;
    		            
                        if ( $order_id > 0 ) {
    		                $booking_details[ 'order_id' ] = $order_id;
    		                $status = import_bookings::bkap_create_booking( $booking_details, false );
    		            }
    		        }
    		        
    		        if ( isset( $status[ 'new_order' ] ) && $status[ 'new_order' ] ) {
    		        	?>
    		        	<script>
										window.location = '<?php echo apply_filters( 'wcfm_manual_tych_booking_redirect', get_wcfm_view_order_url( $order_id ), $order_id ); ?>';
									</script>
									<?php
    		        } else if ( isset( $status[ 'item_added' ] ) && $status[ 'item_added' ] ) {
    		        	?>
    		        	<script>
										window.location = '<?php echo apply_filters( 'wcfm_manual_tych_booking_redirect', get_wcfm_view_order_url( $order_id ), $order_id ); ?>';
									</script>
									<?php
    		        } else {
    		            if ( 1 == $status[ 'backdated_event' ] ) {
    		                throw new Exception( __( 'Back Dated bookings cannot be created. Please select a future date.', 'woocommerce-booking' ) );
    		            }
    		            
    		            if ( 1 == $status[ 'validation_check' ] ) {
    		                throw new Exception( __( 'The product is not available for the given date for the desired quantity.', 'woocommerce-booking' ) );
    		            }
    		            
    		            if ( 1 == $status[ 'grouped_product' ] ) {
    		                throw new Exception( __( 'Bookings cannot be created for grouped products.', 'woocommerce-booking' ) );
    		            }
    		            
    		        } 
                    
    		    } 
		    } catch ( Exception $e ) {
		        $WCFM_Tych_Manual_Bookings = new WCFM_Tych_Manual_Bookings();
                $WCFM_Tych_Manual_Bookings->errors[] = $e->getMessage();
		    }
		    
		    switch( $step ) {
		        case '1':
		            $WCFM_Tych_Manual_Bookings->create_bookings_1();
		            break;
		        case '2':
		            $WCFM_Tych_Manual_Bookings->create_bookings_2( $bkap_data );
		            break;
		        default:
		            $WCFM_Tych_Manual_Bookings->create_bookings_1();
		            break;
		    }
		     
		}
		
		/**
		 * Output any warnings/errors that occur when creating a manual booking.
         *
         * @since 4.1.0
         */

		public function show_errors() {
		    foreach ( $this->errors as $error ) {
		        echo '<div class="error"><p>' . esc_html( $error ) . '</p></div>';
		    }
		}
		
		/**
		 * Display the first page for manual bookings
		 *
		 * @since 4.1.0
         * @todo Change to function name as per its functionality
		 */

		function create_bookings_1() {
            $this->show_errors();
            
            $customers = array();
             
            $args1 = array( 'role' => 'customer',
                'fields' => array( 'id', 'display_name', 'user_email' )
            );
            
            if( !wcfm_is_vendor() ) {
							$args2 = array( 'role' => 'administrator',
									'fields' => array( 'id', 'display_name', 'user_email' )
							);
							
							$wp_users = array_merge( get_users( $args1 ), get_users( $args2 ) );
						} else {
							$wp_users = get_users( $args1 );
						}
            
            foreach( $wp_users as $users ) {
                $customer_id = $users->id;
                $user_email = $users->user_email;
                $user_name = $users->display_name;
                $customers[ $customer_id ] = "$user_name (#$customer_id - $user_email )";
            }
		    ?>
		    <div class="wrap woocommerce">
		    <h2><?php _e( 'Create Booking', 'woocommerce-booking' ); ?></h2>
		    <div class="wcfm_clearfix"></div>
		    	<p><?php _e( 'You can create a new booking for a customer here. This form will create a booking for the user, and optionally an associated order. Created orders will be marked as processing.', 'woocommerce-booking' ); ?></p>
		    
		    	<?php
		    	$WCFM_Tych_Manual_Bookings = new WCFM_Tych_Manual_Bookings();
		    	$WCFM_Tych_Manual_Bookings->show_errors(); ?>
		    	<form method="POST">
		    		<table class="form-table">
		    			<tbody>
		    				<tr valign="top">
		    					<th scope="row" style="width:30%;">
		    						<span for="customer_id" class="wcfm_title" style="width:100%;"><strong><?php _e( 'Customer', 'woocommerce-booking' ); ?></strong></span>
		    					</th>
		    					<td>
										<select id="customer_id" name="customer_id" class="wc-customer-search wcfm-select">
												<option value="0"><?php _e( 'Guest', 'woocommerce-booking' ); ?></option> 
												<?php
												foreach ( $customers as $c_id => $c_data ) {
														echo '<option value="' . esc_attr( $c_id ) . '">' . sanitize_text_field( $c_data ) . '</option>';
												}
												?>
										</select>
		    					</td>
		    				</tr>
		    				<tr valign="top">
		    					<th scope="row">
		    						<span for="bkap_product_id" class="wcfm_title" style="width:100%;"><strong><?php _e( 'Bookable Product', 'woocommerce-booking' ); ?></strong></span>
		    					</th>
		    					<td>
		    						<select id="bkap_product_id" name="bkap_product_id" class="chosen_select wcfm-select" style="width: 300px">
		    							<option value=""><?php _e( 'Select a bookable product...', 'woocommerce-booking' ); ?></option>
		    							<?php 
		    							$product_args = array( 
																	'post_type'         => array( 'product' ), 
																	'posts_per_page'    => -1,
																	'post_status'       => array( 'publish' ),
																	'meta_query'        => array(
																															array(
																																'key'     => '_bkap_enable_booking',
																																'value'   => 'on',
																																'compare' => '=',
																															),
																													)
															);
											$product_args   = apply_filters( 'wcfm_products_args', $product_args );
											$product_list   = get_posts( $product_args );    
											if( !empty( $product_list ) ) {
												foreach ( $product_list as $k => $value ) {
													?>
													<option value="<?php echo $value->ID; ?>"><?php echo get_the_title( $value->ID ); ?></option>
		    							<?php }
		    								}
		    								?>
		    						</select>
		    					</td>
		    				</tr>
		    				<tr valign="top">
		    					<th scope="row">
		    						<span for="bkap_create_order" class="wcfm_title" style="width:100%;"><strong><?php _e( 'Create Order', 'woocommerce-booking' ); ?></strong></span>
		    					</th>
		    					<td>
		    						<p>
		    							<label>
		    								<input type="radio" name="bkap_order" value="new" class="checkbox" />
		    								<?php _e( 'Create a new corresponding order for this new booking. Please note - the booking will not be active until the order is processed/completed.', 'woocommerce-booking' ); ?>
		    							</label>
		    						</p>
		    						<p>
		    							<label>
		    								<input type="radio" name="bkap_order" value="existing" class="checkbox" />
		    								<?php _e( 'Assign this booking to an existing order with this ID:', 'woocommerce-booking' ); ?>
		    								<input type="number" name="bkap_order_id" value="" class="text" size="3" style="width: 80px;" />
		    							</label>
		    						</p>
		    					</td>
		    				</tr>
		    				<?php do_action( 'bkap_after_create_booking_page' ); ?>
		    				<tr valign="top">
		    					<th scope="row">&nbsp;</th>
		    					<td>
		    						<input type="submit" name="bkap_create_booking" class="button-primary wcfm_submit_button" value="<?php _e( 'Next', 'woocommerce-booking' ); ?>" />
		    						<?php wp_nonce_field( 'bkap_create_notification' ); ?>
		    					</td>
		    				</tr>
		    			</tbody>
		    		</table>
		    	</form>
		    </div>
		    		    
		    <?php
		}
		
		/**
		 * Display the second page for manual bookings.
		 *
		 * @since 4.1.0
         * @todo Change to function name as per its functionality
		 */

		function create_bookings_2( $booking_data ) {
		    $this->show_errors();
		    // check if the passed product ID is a variation ID
		    $_product = wc_get_product( $booking_data[ 'product_id' ] );
		    $variation_id = 0;
		    
		    $parent_id = ( version_compare( WOOCOMMERCE_VERSION, "3.0.0" ) < 0 ) ? $_product->parent->id : $_product->get_parent_id();
		    $product_id = $booking_data[ 'product_id' ];
		    ?>
		    <h2><?php _e( 'Create Booking', 'woocommerce-booking' ); ?></h2>
		    <div class="wcfm_clearfix"></div>
		    <form method="POST">
	    		<table class="form-table">
	    			<tbody>
	    				<tr valign="top">
	    					<th scope="row" width='10%' style="width:30%">
	    						<span wcfm="wcfm_title" style="width:100%"><strong><?php _e( 'Booking Data:', 'woocommerce-booking' ); ?></strong></span>
	    					</th>
	    					<td width='70%'>
                		    <?php 
		                      if ( $parent_id > 0 ) {
                                    $settings_id = $parent_id;
                                } else {
                                    $settings_id = $product_id;
                                }
                    		    $duplicate_of = bkap_common::bkap_get_product_id( $settings_id );
                    		    // CSS scripts
                    		    bkap_load_scripts_class::inlcude_frontend_scripts_css( $settings_id );
                    		    // JS scripts
                    		    bkap_load_scripts_class::include_frontend_scripts_js( $settings_id );
                    		    // localize the scripts
                    		    $hidden_dates = bkap_booking_process::bkap_localize_process_script( $duplicate_of );
                    		    // print the hidden fields
                    		    // print the booking form
                    		    $booking_settings = get_post_meta( $duplicate_of, 'woocommerce_booking_settings', true );
                    		    $global_settings = json_decode( get_option( 'woocommerce_booking_global_settings' ) );
                    		    
                    		    wc_get_template(
                        		    'bookings/bkap-bookings-box.php',
                        		    array(
                            		    'product_id'		=> $duplicate_of,
                            		    'product_obj'		=> $_product,
                            		    'booking_settings' 	=> $booking_settings,
                            		    'global_settings'	=> $global_settings,
                            		    'hidden_dates'      => $hidden_dates ),
                            		    'woocommerce-booking/',
                        		    BKAP_BOOKINGS_TEMPLATE_PATH );
                                
                    		    // price display
                    		    bkap_booking_process::bkap_price_display();
                    		    ?>
                		    </td>
                		    <td>
                		    </td>
            		    </tr>
            		    <tr valign="top">
	    					<th scope="row">&nbsp;</th>
	    					<td>
	    						<input type="submit" name="bkap_create_booking_2" class="bkap_create_booking button-primary wcfm_submit_button" value="<?php _e( 'Create Booking', 'woocommerce-booking' ); ?>" disabled="disabled"/>
	    						<input type="hidden" name="bkap_customer_id" value="<?php echo esc_attr( $booking_data[ 'customer_id' ] ); ?>" />
        						<input type="hidden" name="bkap_product_id" value="<?php echo esc_attr( $product_id ); ?>" />
        						<input type="hidden" name="bkap_order" value="<?php echo esc_attr( $booking_data[ 'bkap_order' ] ); ?>" />
        						<input type="hidden" name="bkap_order_id" value="<?php echo esc_attr( $booking_data[ 'order_id' ] ); ?>" />
        						<?php if ( $parent_id > 0 ) { ?>
        						<input type="hidden" class="variation_id" value="<?php echo $product_id; ?>" />
        						<?php 
            						$variation_class = new WC_Product_Variation( $product_id );
            						$get_attributes =   $variation_class->get_variation_attributes();
            						
            						if( is_array( $get_attributes ) && count( $get_attributes ) > 0 ) {
            						    foreach( $get_attributes as $attr_name => $attr_value ) {
            						        $attr_value = htmlspecialchars( $attr_value, ENT_QUOTES );
            						        // print a hidden field for each of these
            						        print( "<input type='hidden' name='$attr_name' value='$attr_value' />" );
            						    }
            						}
        						}
        						?>
        						        						
	    						<?php wp_nonce_field( 'bkap_create_booking' ); ?>
	    					</td>
	    					<td></td>
	    				</tr>
        		    </tbody>
    		    </table>
    		 </form>
    		 <?php     
		}		
	} // end of class

    //$WCFM_Tych_Manual_Bookings = new WCFM_Tych_Manual_Bookings();
}
