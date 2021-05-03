<?php
/**
 * WCFM plugin controllers
 *
 * Plugin Orders Manage Add Customer Controller
 *
 * @author 		WC Lovers
 * @package 	wcfmu/controllers/orders
 * @version   5.2.0
 */

class WCFMu_Orders_Manage_Customer_Add_Controller {
	
	public function __construct() {
		global $WCFM, $WCFMu;
		
		$this->processing();
	}
	
	public function processing() {
		global $WCFM, $WCFMu,$wpdb, $wcfm_customer_form_data;
		
		$wcfm_customer_form_data = array();
	  parse_str($_POST['wcfm_order_add_customer_form'], $wcfm_customer_form_data);
	  
	  $wcfm_customer_messages = get_wcfm_customers_manage_messages();
	  $has_error = false;
	  
		if(isset($wcfm_customer_form_data['wcbc_user_email']) && !empty($wcfm_customer_form_data['wcbc_user_email'])) {
			$customer_id = 0;
			$is_update = false;
			
			// WCFM form custom validation filter
			$custom_validation_results = apply_filters( 'wcfm_form_custom_validation', $wcfm_customer_form_data, 'customer_manage' );
			if(isset($custom_validation_results['has_error']) && !empty($custom_validation_results['has_error'])) {
				$custom_validation_error = __( 'There has some error in submitted data.', 'wc-frontend-manager' );
				if( isset( $custom_validation_results['message'] ) && !empty( $custom_validation_results['message'] ) ) { $custom_validation_error = $custom_validation_results['message']; }
				echo '{"status": false, "message": "' . $custom_validation_error . '"}';
				die;
			}
			
			if ( ! is_email( $wcfm_customer_form_data['wcbc_user_email'] ) ) {
				echo '{"status": false, "message": "' . __( 'Please provide a valid email address.', 'woocommerce' ) . '"}';
				die;
			}
			
			$user_email = sanitize_email( $wcfm_customer_form_data['wcbc_user_email'] );
			$username   = sanitize_user( current( explode( '@', $user_email ) ), true );
			
			$append     = 1;
			$o_username = $username;

			while ( username_exists( $username ) ) {
				$username = $o_username . $append;
				$append++;
			}
			$wcfm_customer_form_data['user_name'] = $username;
			
			if( email_exists( $wcfm_customer_form_data['wcbc_user_email'] ) == false ) {
				
			} else {
				$has_error = true;
				echo '{"status": false, "message": "' . $wcfm_customer_messages['email_exists'] . '"}';
			}
			
			$password = wp_generate_password( $length = 12, $include_standard_special_chars=false );
			if( !$has_error ) {
				$user_data = array( 'user_login'      => $wcfm_customer_form_data['user_name'],
														'user_email'      => $wcfm_customer_form_data['wcbc_user_email'],
														'display_name'    => $wcfm_customer_form_data['user_name'],
														'nickname'        => $wcfm_customer_form_data['user_name'],
														'first_name'      => $wcfm_customer_form_data['wcbc_first_name'],
														'last_name'       => $wcfm_customer_form_data['wcbc_last_name'],
														'user_pass'       => $password,
														'role'            => 'customer',
														'ID'              => $customer_id
														);
				
				$customer_id = wp_insert_user( $user_data ) ;
					
				if( !$customer_id ) {
					$has_error = true;
				} else {
					if( apply_filters( 'wcfm_allow_customer_billing_details', true ) ) {
						$wcfm_customer_billing_fields = array( 
																					'billing_first_name'  => 'wcbc_first_name',
																					'billing_last_name'   => 'wcbc_last_name',
																					'billing_phone'       => 'wcbc_phone',
																					'billing_email'       => 'wcbc_user_email',
																					'billing_address_1'   => 'baddr_1',
																					'billing_address_2'   => 'baddr_2',
																					'billing_country'     => 'bcountry',
																					'billing_city'        => 'bcity',
																					'billing_state'       => 'bstate',
																					'billing_postcode'    => 'bzip'
																				);
						foreach( $wcfm_customer_billing_fields as $wcfm_customer_default_key => $wcfm_customer_default_field ) {
							if( isset( $wcfm_customer_form_data[$wcfm_customer_default_field] ) && !empty( $wcfm_customer_form_data[$wcfm_customer_default_field] ) ) {
								update_user_meta( $customer_id, $wcfm_customer_default_key, $wcfm_customer_form_data[$wcfm_customer_default_field] );
							}
						}
					}
					
					do_action( 'wcfm_customers_manage', $customer_id, $wcfm_customer_form_data );
				}
				
				if(!$has_error) { echo '{"status": true, "message": "' . $wcfm_customer_messages['customer_saved'] . '", "customer_id": "' . $customer_id . '", "username": "' . $wcfm_customer_form_data['user_name'] . ' (' . $user_email . ')' . '"}'; }
				else { echo '{"status": false, "message": "' . $wcfm_customer_messages['customer_failed'] . '"}'; }
			}
		} else {
			echo '{"status": false, "message": "' . $wcfm_customer_messages['no_email'] . '"}';
		}
	  	
		die;
	}
}