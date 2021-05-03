<?php

/**
 * Admin Menu Class
 *
 * @package Update API Manager/Admin
 * @author Todd Lahman LLC
 * @copyright   Copyright (c) Todd Lahman LLC
 * @since 1.3
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class LRM_API_Manager_MENU {

	// Load admin menu
	public function __construct() {

		add_action( 'admin_menu', array( $this, 'add_menu' ) );
		add_action( 'admin_init', array( $this, 'load_settings' ) );
	}

	// Add option page menu
	public function add_menu() {

		$page = add_options_page( __( LRM_API_Manager()->ame_settings_menu_title, LRM_API_Manager()->text_domain ), __( LRM_API_Manager()->ame_settings_menu_title, LRM_API_Manager()->text_domain ),
						'manage_options', LRM_API_Manager()->ame_activation_tab_key, array( $this, 'config_page')
		);
		add_action( 'admin_print_styles-' . $page, array( $this, 'css_scripts' ) );
	}

	// Draw option page
	public function config_page() {

		$settings_tabs = array( LRM_API_Manager()->ame_activation_tab_key => __( LRM_API_Manager()->ame_menu_tab_activation_title, LRM_API_Manager()->text_domain ), LRM_API_Manager()->ame_deactivation_tab_key => __( LRM_API_Manager()->ame_menu_tab_deactivation_title, LRM_API_Manager()->text_domain ) );
		$current_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : LRM_API_Manager()->ame_activation_tab_key;
		$tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : LRM_API_Manager()->ame_activation_tab_key;
		?>
		<div class='wrap'>
			<h2><?php _e( LRM_API_Manager()->ame_settings_title, LRM_API_Manager()->text_domain ); ?></h2>

			<h2 class="nav-tab-wrapper">
			<?php
				foreach ( $settings_tabs as $tab_page => $tab_name ) {
					$active_tab = $current_tab == $tab_page ? 'nav-tab-active' : '';
					echo '<a class="nav-tab ' . $active_tab . '" href="?page=' . LRM_API_Manager()->ame_activation_tab_key . '&tab=' . $tab_page . '">' . $tab_name . '</a>';
				}
			?>
			</h2>
				<form action='options.php' method='post'>
					<div class="main">
				<?php
					if( $tab == LRM_API_Manager()->ame_activation_tab_key ) {
							settings_fields( LRM_API_Manager()->ame_data_key );
							do_settings_sections( LRM_API_Manager()->ame_activation_tab_key );
							submit_button( __( 'Save Changes', LRM_API_Manager()->text_domain ) );
					} else {
							settings_fields( LRM_API_Manager()->ame_deactivate_checkbox );
							do_settings_sections( LRM_API_Manager()->ame_deactivation_tab_key );
							submit_button( __( 'Save Changes', LRM_API_Manager()->text_domain ) );
					}
				?>
					</div>
				</form>
            <div class="sidebar">
                <ol class="nav">
                    <li class="active"><a href="https://docs.maxim-kaminsky.com/lrm/kb/manage-your-license/#where-to-find-your-license-key" target="_blank">Where to find your license key?</a></li>
                    <li class=""><a href="https://docs.maxim-kaminsky.com/lrm/kb/manage-your-license/#where-to-download-plugin-zip" target="_blank">Where to download plugin zip?</a></li>
                    <li class=""><a href="https://docs.maxim-kaminsky.com/lrm/kb/manage-your-license/#where-to-put-the-license-key" target="_blank">Where to put the license key?</a></li>
                    <li class=""><a href="https://docs.maxim-kaminsky.com/lrm/kb/manage-your-license/#how-to-deactivate-your-license-key" target="_blank">How to deactivate your license key?</a></li>
                    <li class=""><a href="https://docs.maxim-kaminsky.com/lrm/kb/manage-your-license/#how-to-increase-my-activations-count" target="_blank">How to increase my activations count?</a></li>
                </ol>
            </div>

			</div>
			<?php
	}

	// Register settings
	public function load_settings() {

	    /**
         * Regenerate Instance ID
         * Useful in case of copying website
         * @since 1.71
         **/
	    if ( isset($_GET['wc_am_action']) && 'lrm_api_manager_regenerate_instance_id' === $_GET['wc_am_action'] ) {
            $lrm_api_manager_password_management = new LRM_API_Manager_Password_Management();
	        update_option( LRM_API_Manager()->ame_instance_key, $lrm_api_manager_password_management->generate_password( 12, false ) );
            add_settings_error('general', 'settings_updated', __('Settings saved.'), 'updated');
            set_transient('settings_errors', get_settings_errors(), 30);

	        wp_redirect( add_query_arg( 'settings-updated' , true, $this->admin_page_url() ) );
	        exit;
        }

		register_setting( LRM_API_Manager()->ame_data_key, LRM_API_Manager()->ame_data_key, array( $this, 'validate_options' ) );

		// API Key
		add_settings_section( LRM_API_Manager()->ame_api_key, __( 'API License Activation', LRM_API_Manager()->text_domain ), array( $this, 'wc_am_api_key_text' ), LRM_API_Manager()->ame_activation_tab_key );
		add_settings_field( 'status', __( 'API License Key Status', LRM_API_Manager()->text_domain ), array( $this, 'wc_am_api_key_status' ), LRM_API_Manager()->ame_activation_tab_key, LRM_API_Manager()->ame_api_key );
		add_settings_field( LRM_API_Manager()->ame_api_key, __( 'API License Key', LRM_API_Manager()->text_domain ), array( $this, 'wc_am_api_key_field' ), LRM_API_Manager()->ame_activation_tab_key, LRM_API_Manager()->ame_api_key );
		add_settings_field( LRM_API_Manager()->ame_activation_email, __( 'API License email', LRM_API_Manager()->text_domain ), array( $this, 'wc_am_api_email_field' ), LRM_API_Manager()->ame_activation_tab_key, LRM_API_Manager()->ame_api_key );

		// Activation settings
		register_setting( LRM_API_Manager()->ame_deactivate_checkbox, LRM_API_Manager()->ame_deactivate_checkbox, array( $this, 'wc_am_license_key_deactivation' ) );
		add_settings_section( 'deactivate_button', __( 'API License Deactivation', LRM_API_Manager()->text_domain ), array( $this, 'wc_am_deactivate_text' ), LRM_API_Manager()->ame_deactivation_tab_key );
		add_settings_field( 'deactivate_button', __( 'Deactivate API License Key', LRM_API_Manager()->text_domain ), array( $this, 'wc_am_deactivate_textarea' ), LRM_API_Manager()->ame_deactivation_tab_key, 'deactivate_button' );

	}

	// Provides text for api key section
	public function wc_am_api_key_text() {
		//
	}

	// Returns the API License Key status from the WooCommerce API Manager on the server
	public function wc_am_api_key_status() {

	    //echo get_option( LRM_API_Manager()->ame_instance_key ), '#';

		$license_status = $this->license_key_status();
		$license_status_check = ( ! empty( $license_status['status_check'] ) && $license_status['status_check'] == 'active' ) ? 'Activated' : 'Deactivated';
		if ( ! empty( $license_status_check ) ) {
		    if ( $license_status_check == 'Deactivated' && lrm_pro_is_license_active() ) {
                echo 'Activated, but probably Deactivated on the remote server. Please login to your cabinet and check.';
            } else {
                echo $license_status_check, ',';
            }
		}

		if ( !empty($license_status['data']) && !empty($license_status['data']['activations_remaining']) ) {
            echo ' Never expire';
		    echo ' [', $license_status['data']['activations_remaining'], ' activations remaining ', ' from ', $license_status['data']['total_activations_purchased'], ']';
        }
	}

	// Returns API License text field
	public function wc_am_api_key_field() {

		echo "<input id='api_key' name='" . LRM_API_Manager()->ame_data_key . "[" . LRM_API_Manager()->ame_api_key ."]' size='25' type='text' value='" . LRM_API_Manager()->ame_options[LRM_API_Manager()->ame_api_key] . "' />";
		if ( LRM_API_Manager()->ame_options[LRM_API_Manager()->ame_api_key] ) {
			echo "<span class='icon-pos'><img src='" . LRM_API_Manager()->plugin_url() . "am/assets/images/complete.png' title='' style='padding-bottom: 4px; vertical-align: middle; margin-right:3px;' /></span>";
		} else {
			echo "<span class='icon-pos'><img src='" . LRM_API_Manager()->plugin_url() . "am/assets/images/warn.png' title='' style='padding-bottom: 4px; vertical-align: middle; margin-right:3px;' /></span>";
		}
	}

	// Returns API License email text field
	public function wc_am_api_email_field() {
		echo "<input id='activation_email' name='" . LRM_API_Manager()->ame_data_key . "[" . LRM_API_Manager()->ame_activation_email ."]' size='25' type='text' value='" . LRM_API_Manager()->ame_options[LRM_API_Manager()->ame_activation_email] . "' />";
		if ( LRM_API_Manager()->ame_options[LRM_API_Manager()->ame_activation_email] ) {
			echo "<span class='icon-pos'><img src='" . LRM_API_Manager()->plugin_url() . "am/assets/images/complete.png' title='' style='padding-bottom: 4px; vertical-align: middle; margin-right:3px;' /></span>";
		} else {
			echo "<span class='icon-pos'><img src='" . LRM_API_Manager()->plugin_url() . "am/assets/images/warn.png' title='' style='padding-bottom: 4px; vertical-align: middle; margin-right:3px;' /></span>";
		}
	}

	// Sanitizes and validates all input and output for Dashboard
	public function validate_options( $input ) {

		// Load existing options, validate, and update with changes from input before returning
		$options = LRM_API_Manager()->ame_options;

        $options[LRM_API_Manager()->ame_api_key] = trim( $input[LRM_API_Manager()->ame_api_key] );
		$options[LRM_API_Manager()->ame_activation_email] = trim( $input[LRM_API_Manager()->ame_activation_email] );


        /**
		  * Plugin Activation
		  */
		$api_email = trim( $input[LRM_API_Manager()->ame_activation_email] );
		$api_key = trim( $input[LRM_API_Manager()->ame_api_key] );

		$activation_status = get_option( LRM_API_Manager()->ame_activated_key );
		$checkbox_status = get_option( LRM_API_Manager()->ame_deactivate_checkbox );

		$current_api_key = LRM_API_Manager()->ame_options[LRM_API_Manager()->ame_api_key];



		// Should match the settings_fields() value
		if ( $_REQUEST['option_page'] != LRM_API_Manager()->ame_deactivate_checkbox ) {

			if ( $activation_status == 'Deactivated' || $activation_status == '' || $api_key == '' || $api_email == '' || $checkbox_status == 'on' || $current_api_key != $api_key  ) {

				/**
				 * If this is a new key, and an existing key already exists in the database,
				 * deactivate the existing key before activating the new key.
				 */
				if ( $current_api_key != $api_key )
					$this->replace_license_key( $current_api_key );

				$args = array(
					'email' => $api_email,
					'licence_key' => $api_key,
                );

				$activate_results_raw = LRM_API_Manager()->key()->activate( $args );

                $activate_results = [];
				if ( is_array($activate_results_raw) ) {
                    $activate_results = $activate_results_raw;
                    if ( !isset($activate_results['activated']) ) {
                        $activate_results['activated'] = false;
                    }
                } elseif ( empty($activate_results_raw) ) {
                    $activate_results['activated'] = false;
                } else {
                    $activate_results = json_decode($activate_results_raw , true );
                    if ( JSON_ERROR_NONE !== json_last_error() ) {
                        $activate_results = [
                            'activated' => false,
                            'body' => $activate_results_raw,
                        ];
                    }
                }

				if ( $activate_results['activated'] === true ) {
				    lrm_log( 'Plugin license activated. ' );
					add_settings_error( 'activate_text', 'activate_msg', __( 'Plugin license activated. ', LRM_API_Manager()->text_domain ) . "{$activate_results['message']}.", 'updated' );
					update_option( LRM_API_Manager()->ame_activated_key, 'Activated' );
					update_option( LRM_API_Manager()->ame_deactivate_checkbox, 'off' );
					LRM_API_Manager()->ame_options[LRM_API_Manager()->ame_api_key] = $api_key;
					// Reset the transient so "Automatic update is unavailable for this plugin." should gone
                    set_site_transient( 'update_plugins', null );
				}

				if ( $activate_results['activated'] === false || $activate_results === false ) {
					lrm_log( 'Connection failed to the License Key API server. Try again later.', $activate_results );

					$debug_info[] = '<br><strong>Debug info:</strong>';
					$debug_info[] = 'Instance ID: <code>' . LRM_API_Manager()->ame_instance_id . '</code>';
					$debug_info[] = isset($activate_results['response_code']) ? 'Response code: <code>' . $activate_results['response_code'] . '</code>' : '';
					$debug_info[] = isset($activate_results['server_ip']) ? 'Server IP: <code>' . $activate_results['server_ip'] . '</code>' : '';
					$debug_info[] = isset($activate_results['body']) ? 'Response: <code>' . esc_html($activate_results['body']) . '</code>' : '';

					add_settings_error( 'api_key_check_text', 'api_key_check_error',
                        __( 'Connection failed to the License Key API server. Try again later.', LRM_API_Manager()->text_domain )
                        . implode('<br>', $debug_info)
                    , 'error' );

					$options[LRM_API_Manager()->ame_api_key] = '';
					//$options[LRM_API_Manager()->ame_activation_email] = '';
					update_option( LRM_API_Manager()->ame_activated_key, 'Deactivated' );
				}

				if ( isset( $activate_results['code'] ) ) {

					lrm_log( 'Plugin license activation error, code:', $activate_results['code'] );

					switch ( $activate_results['code'] ) {
						case '100':
						    $regenerate_instance_link = esc_attr( add_query_arg( 'wc_am_action', 'lrm_api_manager_regenerate_instance_id', $this->admin_page_url() ) );

							add_settings_error( 'api_email_text', 'api_email_error', "{$activate_results['error']}. {$activate_results['additional info']}. If you have copied the website - you could <a href='{$regenerate_instance_link}'>regenerate instance ID</a>.", 'error' );
							//$options[LRM_API_Manager()->ame_activation_email] = '';
							$options[LRM_API_Manager()->ame_api_key] = '';
							update_option( LRM_API_Manager()->ame_options[LRM_API_Manager()->ame_activated_key], 'Deactivated' );
						break;
						case '101':
							add_settings_error( 'api_key_text', 'api_key_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error' );
							$options[LRM_API_Manager()->ame_api_key] = '';
							//$options[LRM_API_Manager()->ame_activation_email] = '';
							update_option( LRM_API_Manager()->ame_options[LRM_API_Manager()->ame_activated_key], 'Deactivated' );
						break;
						case '102':
							add_settings_error( 'api_key_purchase_incomplete_text', 'api_key_purchase_incomplete_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error' );
							$options[LRM_API_Manager()->ame_api_key] = '';
							//$options[LRM_API_Manager()->ame_activation_email] = '';
							update_option( LRM_API_Manager()->ame_options[LRM_API_Manager()->ame_activated_key], 'Deactivated' );
						break;
						case '103':
                            add_settings_error( 'api_key_exceeded_text', 'api_key_exceeded_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error' );
                            $options[LRM_API_Manager()->ame_api_key] = '';
                            //$options[LRM_API_Manager()->ame_activation_email] = '';
                            update_option( LRM_API_Manager()->ame_options[LRM_API_Manager()->ame_activated_key], 'Deactivated' );
						break;
						case '104':
                            add_settings_error( 'api_key_not_activated_text', 'api_key_not_activated_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error' );
                            $options[LRM_API_Manager()->ame_api_key] = '';
                            //$options[LRM_API_Manager()->ame_activation_email] = '';
                            update_option( LRM_API_Manager()->ame_options[LRM_API_Manager()->ame_activated_key], 'Deactivated' );
						break;
						case '105':
                            add_settings_error( 'api_key_invalid_text', 'api_key_invalid_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error' );
                            $options[LRM_API_Manager()->ame_api_key] = '';
                            //$options[LRM_API_Manager()->ame_activation_email] = '';
                            update_option( LRM_API_Manager()->ame_options[LRM_API_Manager()->ame_activated_key], 'Deactivated' );
						break;
						case '106':
                            add_settings_error( 'sub_not_active_text', 'sub_not_active_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error' );
                            $options[LRM_API_Manager()->ame_api_key] = '';
                            $options[LRM_API_Manager()->ame_activation_email] = '';
                            update_option( LRM_API_Manager()->ame_options[LRM_API_Manager()->ame_activated_key], 'Deactivated' );
						break;
					}

				}

			} // End Plugin Activation

		}

		return $options;
	}

	// Returns the API License Key status from the WooCommerce API Manager on the server
	public function license_key_status() {

        if ( ! LRM_API_Manager()->ame_options[LRM_API_Manager()->ame_api_key] || ! LRM_API_Manager()->ame_options[LRM_API_Manager()->ame_activation_email] ) {
            return false;
        }

        $activation_status = get_option( LRM_API_Manager()->ame_activated_key );

        $args = array(
			'email' => LRM_API_Manager()->ame_options[LRM_API_Manager()->ame_activation_email],
			'licence_key' => LRM_API_Manager()->ame_options[LRM_API_Manager()->ame_api_key],
        );

		return json_decode( LRM_API_Manager()->key()->status( $args ), true );
	}

	// Deactivate the current license key before activating the new license key
	public function replace_license_key( $current_api_key ) {

		$args = array(
			'email' => LRM_API_Manager()->ame_options[LRM_API_Manager()->ame_activation_email],
			'licence_key' => $current_api_key,
			);

		$reset = LRM_API_Manager()->key()->deactivate( $args ); // reset license key activation

		if ( $reset == true )
			return true;

		return add_settings_error( 'not_deactivated_text', 'not_deactivated_error', __( 'The license could not be deactivated. Use the License Deactivation tab to manually deactivate the license before activating a new license.', LRM_API_Manager()->text_domain ), 'updated' );
	}

	// Deactivates the license key to allow key to be used on another blog
	public function wc_am_license_key_deactivation( $input ) {

		$activation_status = get_option( LRM_API_Manager()->ame_activated_key );

		$args = array(
			'email' => LRM_API_Manager()->ame_options[LRM_API_Manager()->ame_activation_email],
			'licence_key' => LRM_API_Manager()->ame_options[LRM_API_Manager()->ame_api_key],
			);

		// For testing activation status_extra data
		// $activate_results = json_decode( LRM_API_Manager()->key()->status( $args ), true );
		// print_r($activate_results); exit;

		$options = ( $input == 'on' ? 'on' : 'off' );

		if ( $options == 'on' && $activation_status == 'Activated' && LRM_API_Manager()->ame_options[LRM_API_Manager()->ame_api_key] != '' && LRM_API_Manager()->ame_options[LRM_API_Manager()->ame_activation_email] != '' ) {

			// deactivates license key activation
			$activate_results = json_decode( LRM_API_Manager()->key()->deactivate( $args ), true );

			// Used to display results for development
			//print_r($activate_results); exit();

			if ( isset($activate_results['deactivated']) && $activate_results['deactivated'] === true ) {
				lrm_log( 'Plugin license deactivated. ' );

				$update = array(
					LRM_API_Manager()->ame_api_key => '',
					LRM_API_Manager()->ame_activation_email => ''
                );

				$merge_options = array_merge( LRM_API_Manager()->ame_options, $update );

				update_option( LRM_API_Manager()->ame_data_key, $merge_options );

				update_option( LRM_API_Manager()->ame_activated_key, 'Deactivated' );

				add_settings_error( 'wc_am_deactivate_text', 'deactivate_msg', __( 'Plugin license deactivated. ', LRM_API_Manager()->text_domain ) . "{$activate_results['activations_remaining']}.", 'updated' );

				return $options;
			}

			if ( isset( $activate_results['code'] ) ) {

				lrm_log( 'Plugin license deactivation error, code:', $activate_results['code'] );

                $activate_results['additional info'] = !empty($activate_results['additional info']) ? $activate_results['additional info'] : '';

				switch ( $activate_results['code'] ) {
					case '100':
						add_settings_error( 'api_email_text', 'api_email_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error' );
						$options[LRM_API_Manager()->ame_api_key] = '';
						//$options[LRM_API_Manager()->ame_activation_email] = '';
						update_option( LRM_API_Manager()->ame_options[LRM_API_Manager()->ame_activated_key], 'Deactivated' );
					break;
					case '101':
						add_settings_error( 'api_key_text', 'api_key_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error' );
						$options[LRM_API_Manager()->ame_api_key] = '';
						//$options[LRM_API_Manager()->ame_activation_email] = '';
						update_option( LRM_API_Manager()->ame_options[LRM_API_Manager()->ame_activated_key], 'Deactivated' );
					break;
					case '102':
						add_settings_error( 'api_key_purchase_incomplete_text', 'api_key_purchase_incomplete_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error' );
						$options[LRM_API_Manager()->ame_api_key] = '';
						//$options[LRM_API_Manager()->ame_activation_email] = '';
						update_option( LRM_API_Manager()->ame_options[LRM_API_Manager()->ame_activated_key], 'Deactivated' );
					break;
					case '103':
                        add_settings_error( 'api_key_exceeded_text', 'api_key_exceeded_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error' );
                        $options[LRM_API_Manager()->ame_api_key] = '';
                        //$options[LRM_API_Manager()->ame_activation_email] = '';
                        update_option( LRM_API_Manager()->ame_options[LRM_API_Manager()->ame_activated_key], 'Deactivated' );
                    break;
					case '104':
                        add_settings_error( 'api_key_not_activated_text', 'api_key_not_activated_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error' );
                        $options[LRM_API_Manager()->ame_api_key] = '';
                        //$options[LRM_API_Manager()->ame_activation_email] = '';
                        update_option( LRM_API_Manager()->ame_options[LRM_API_Manager()->ame_activated_key], 'Deactivated' );
					break;
					case '105':
                        add_settings_error( 'api_key_invalid_text', 'api_key_invalid_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error' );
                        $options[LRM_API_Manager()->ame_api_key] = '';
                        //$options[LRM_API_Manager()->ame_activation_email] = '';
                        update_option( LRM_API_Manager()->ame_options[LRM_API_Manager()->ame_activated_key], 'Deactivated' );
					break;
					case '106':
                        add_settings_error( 'sub_not_active_text', 'sub_not_active_error', "{$activate_results['error']}. {$activate_results['additional info']}", 'error' );
                        $options[LRM_API_Manager()->ame_api_key] = '';
                        //$options[LRM_API_Manager()->ame_activation_email] = '';
                        update_option( LRM_API_Manager()->ame_options[LRM_API_Manager()->ame_activated_key], 'Deactivated' );
					break;
				}

			}

		} else {

			return $options;
		}

	}

	public function wc_am_deactivate_text() {}

	public function wc_am_deactivate_textarea() {

		echo '<input type="checkbox" id="' . LRM_API_Manager()->ame_deactivate_checkbox . '" name="' . LRM_API_Manager()->ame_deactivate_checkbox . '" value="on"';
		echo checked( get_option( LRM_API_Manager()->ame_deactivate_checkbox ), 'on' );
		echo '/>';
		?><span class="description"><?php _e( 'Deactivates an API License Key so it can be used on another blog.', LRM_API_Manager()->text_domain ); ?></span>
		<?php
	}

	// Loads admin style sheets
	public function css_scripts() {

		wp_register_style( LRM_API_Manager()->ame_data_key . '-css', LRM_API_Manager()->plugin_url() . 'am/assets/css/admin-settings.css', array(), LRM_API_Manager()->version, 'all');
		wp_enqueue_style( LRM_API_Manager()->ame_data_key . '-css' );
	}

	public function admin_page_url() {
        return admin_url( 'options-general.php?page=' . LRM_API_Manager()->ame_activation_tab_key );

        $this->ame_instance_id 			= get_option( $this->ame_instance_key ); // Instance ID (unique to each blog activation)
    }

}

new LRM_API_Manager_MENU();
