<?php
class WCFMu_Settings_License {
  /**
   * Holds the values to be used in the fields callbacks
   */
  private $options;
  
  private $tab;
  
  private $api_manager_license_key;

  /**
   * Start up
   */
  public function __construct($tab) {
    global $WCFMu;
    
    $this->tab = $tab;
    $this->options = get_option( "wcfm_{$this->tab}_settings_name" );
    $this->settings_page_init();
    
    $this->api_manager_license_key = $WCFMu->license->api_manager_license_key;
  }
  
  /**
   * Register and add settings
   */
  public function settings_page_init() {
    global $WCFMu, $WCFMu;
    
    $license_api_key = '';
    $license_activation_email = '';
    if(isset($this->options[$WCFMu->license->license_api_key])) $license_api_key = $this->options[$WCFMu->license->license_api_key];
    if(isset($this->options[$WCFMu->license->license_activation_email])) $license_activation_email = $this->options[$WCFMu->license->license_activation_email];
    
    if ( $this->options && is_array($this->options) && $this->options[$WCFMu->license->license_api_key] ) {
			$api_key_ico = "<span class='icon-pos'><img src='" . $WCFMu->plugin_url . "core/license/assets/images/complete.png' title='' style='padding-bottom: 4px; vertical-align: middle; margin-right:3px;' /></span>";
		} else {
			$api_key_ico = "<span class='icon-pos'><img src='" . $WCFMu->plugin_url . "core/license/assets/images/warn.png' title='' style='padding-bottom: 4px; vertical-align: middle; margin-right:3px;' /></span>";
		}
		
		if ( $this->options && is_array($this->options) && $this->options[$WCFMu->license->license_activation_email] ) {
			$api_email_ico = "<span class='icon-pos'><img src='" . $WCFMu->plugin_url . "core/license/assets/images/complete.png' title='' style='padding-bottom: 4px; vertical-align: middle; margin-right:3px;' /></span>";
		} else {
			$api_email_ico = "<span class='icon-pos'><img src='" . $WCFMu->plugin_url . "core/license/assets/images/warn.png' title='' style='padding-bottom: 4px; vertical-align: middle; margin-right:3px;' /></span>";
		}
    
    $settings_tab_options = array("tab" => "{$this->tab}",
                                  "ref" => &$this,
                                  "sections" => array(
                                                      "activation_settings_section" => array("title" =>  __('License Activation', 'wc-frontend-manager-ultimate'),
                                                                                         "fields" => array($WCFMu->license->license_api_key => array('title' => __('API License Key', 'wc-frontend-manager-ultimate'), 'type' => 'password', 'value' => $license_api_key, 'desc' => $api_key_ico),
                                                                                                           $WCFMu->license->license_activation_email => array('title' => __('API License email', 'wc-frontend-manager-ultimate'), 'type' => 'text', 'value' => $license_activation_email, 'desc' => $api_email_ico),
                                                                                                           )
                                                                                         ),
                                                      "deactivation_settings_section" => array("title" =>  __('License Deactivation', 'wc-frontend-manager-ultimate'),
                                                                                         "fields" => array($WCFMu->license->license_deactivate_checkbox => array('title' => __('Deactivate API License Key', 'wc-frontend-manager-ultimate'), 'type' => 'checkbox', 'id' => $WCFMu->license->license_deactivate_checkbox, 'name' => $WCFMu->license->license_deactivate_checkbox, 'value' => 'on', 'desc' => __( 'Deactivates an API License Key so it can be used on another blog.', 'wc-frontend-manager-ultimate' ))
                                                                                                           )
                                                                                         )
                                                      )
                                  );
    
    $this->settings_field_init(apply_filters("settings_{$this->tab}_tab_options", $settings_tab_options));
  }
  
  /**
	 * Register and add settings fields
	 */
	public function settings_field_init($tab_options) {
			global $WCFM, $WCFMu;

			if (!empty($tab_options) && isset($tab_options['tab']) && isset($tab_options['ref']) && isset($tab_options['sections'])) {
					// Register tab options
					register_setting(
									"wcfm_{$tab_options['tab']}_settings_group", // Option group
									"wcfm_{$tab_options['tab']}_settings_name", // Option name
									array($tab_options['ref'], "wcfm_{$tab_options['tab']}_settings_sanitize") // Sanitize
					);

					foreach ($tab_options['sections'] as $sectionID => $section) {
							// Register section
							if (method_exists($tab_options['ref'], "{$sectionID}_info")) {
									add_settings_section(
													$sectionID, // ID
													$section['title'], // Title
													array($tab_options['ref'], "{$sectionID}_info"), // Callback
													"wcfm-{$tab_options['tab']}-settings-admin" // Page
									);
							} else {
									add_settings_section(
													$sectionID, // ID
													$section['title'], // Title
													array($section['ref'], "{$sectionID}_info"), // Callback
													"wcfm-{$tab_options['tab']}-settings-admin" // Page
									);
							}

							// Register fields
							if (isset($section['fields'])) {
									foreach ($section['fields'] as $fieldID => $field) {
											if (isset($field['type'])) {
													$field['tab'] = $tab_options['tab'];
													$callbak = $this->get_field_callback_type($field['type']);
													if (!empty($callbak)) {
															add_settings_field(
																			$fieldID, $field['title'], array($this, $callbak), "wcfm-{$tab_options['tab']}-settings-admin", $sectionID, $this->process_fields_args($field, $fieldID)
															);
													}
											}
									}
							}
					}
			}
	}
	
	/**
	 * function process_fields_args
	 * @param $fields
	 * @param $fieldId
	 * @return Array
	 */
	function process_fields_args($field, $fieldID) {

			if (!isset($field['id'])) {
					$field['id'] = $fieldID;
			}

			if (!isset($field['label_for'])) {
					$field['label_for'] = $fieldID;
			}

			if (!isset($field['name'])) {
					$field['name'] = $fieldID;
			}

			return $field;
	}
	
	function get_field_callback_type($fieldType) {
			$callBack = '';
			switch ($fieldType) {
					case 'input':
					case 'text':
					case 'email':
					case 'url':
					case 'password':
							$callBack = 'text_field_callback';
							break;

					case 'hidden':
							$callBack = 'hidden_field_callback';
							break;

					case 'textarea':
							$callBack = 'textarea_field_callback';
							break;

					case 'wpeditor':
							$callBack = 'wpeditor_field_callback';
							break;

					case 'checkbox':
							$callBack = 'checkbox_field_callback';
							break;

					case 'radio':
							$callBack = 'radio_field_callback';
							break;

					case 'select':
							$callBack = 'select_field_callback';
							break;

					case 'upload':
							$callBack = 'upload_field_callback';
							break;

					case 'colorpicker':
							$callBack = 'colorpicker_field_callback';
							break;

					case 'datepicker':
							$callBack = 'datepicker_field_callback';
							break;

					case 'multiinput':
							$callBack = 'multiinput_callback';
							break;

					default:
							$callBack = '';
							break;
			}

			return $callBack;
	}
	
	/**
	 * Get the text field display
	 */
	public function text_field_callback($field) {
			global $WCFM, $WCFMu;
			$field['dfvalue'] = isset($field['dfvalue']) ? esc_attr($field['dfvalue']) : '';
			$field['value'] = isset($field['value']) ? esc_attr($field['value']) : $field['dfvalue'];
			$field['value'] = isset($this->options[$field['name']]) ? esc_attr($this->options[$field['name']]) : $field['value'];
			$field['name'] = "wcfm_{$field['tab']}_settings_name[{$field['name']}]";
			$WCFM->wcfm_fields->text_input($field);
	}
	
	/**
	 * Get the checkbox field display
	 */
	public function checkbox_field_callback($field) {
			global $WCFM, $WCFMu;
			$field['value'] = isset($field['value']) ? esc_attr($field['value']) : '';
			$field['value'] = isset($this->options[$field['name']]) ? esc_attr($this->options[$field['name']]) : $field['value'];
			$field['dfvalue'] = isset($this->options[$field['name']]) ? esc_attr($this->options[$field['name']]) : '';
			$field['name'] = "wcfm_{$field['tab']}_settings_name[{$field['name']}]";
			$WCFM->wcfm_fields->checkbox_input($field);
	}

  /**
   * Sanitize each setting field as needed
   *
   * @param array $input Contains all settings fields as array keys
   */
  public function wcfm_WCFMu_license_settings_sanitize( $input ) {
    global $WCFMu;
	  
		// Load existing options, validate, and update with changes from input before returning
		$new_input = array();
		
		$hasError = false;
		
		if(!isset($input[$WCFMu->license->license_deactivate_checkbox])) $input[$WCFMu->license->license_deactivate_checkbox] = 'off';
		$new_input[$WCFMu->license->license_api_key] = trim( $input[$WCFMu->license->license_api_key] );
		$new_input[$WCFMu->license->license_activation_email] = trim( $input[$WCFMu->license->license_activation_email] );
		$new_input[$WCFMu->license->license_deactivate_checkbox] = ( $input[$WCFMu->license->license_deactivate_checkbox] == 'on' ? 'on' : 'off' );
		
		$api_email = trim( $input[$WCFMu->license->license_activation_email] );
		$api_key = trim( $input[$WCFMu->license->license_api_key] );
		
		if($api_key == '') {
		  add_settings_error(
        "wcfm_{$this->tab}_settings_name",
        esc_attr( "wcfm_{$this->tab}_settings_name" ),
        __('Please insert your license key.', 'wc-frontend-manager-ultimate'),
        'error'
      );
      $hasError = true;
		}
		
		if($api_email == '') {
		  add_settings_error(
        "wcfm_{$this->tab}_settings_name",
        esc_attr( "wcfm_{$this->tab}_settings_name" ),
        __('Please insert your license email.', 'wc-frontend-manager-ultimate'),
        'error'
      );
      $hasError = true;
		}

		if(!$hasError) {

      $activation_status = get_option( $WCFMu->license->license_activated_key );
      $checkbox_status = get_option( $WCFMu->license->license_deactivate_checkbox );
      $current_api_key = $this->options[$WCFMu->license->license_api_key];
		
		  $args = array(
        'email' => $api_email,
        'licence_key' => $api_key,
      );
      
      if ( 'off' == $new_input[$WCFMu->license->license_deactivate_checkbox] ) {
  
        // Plugin Activation
        if ( $activation_status == 'Deactivated' || $activation_status == '' || $checkbox_status == 'on' || $current_api_key != $api_key  ) {

          if ( $current_api_key != $api_key )
            $this->replace_license_key( $current_api_key );
  
          $activate_results = json_decode( $this->api_manager_license_key->activate( $args ), true );
          
          if ( $activate_results['activated'] == true ) {
          	if(!isset($activate_results['message'])) $activate_results['message'] = '';
            add_settings_error( "wcfm_{$this->tab}_settings_name", esc_attr( "wcfm_{$this->tab}_settings_name" ), __( 'Plugin activated. ', 'wc-frontend-manager-ultimate' ) . "{$activate_results['message']}.", 'updated' );
            update_option( $WCFMu->license->license_activated_key, 'Activated' );
            update_option( $WCFMu->license->license_deactivate_checkbox, 'off' );
            
            //$WCFMu->license->wcfmu_plugin_tracker('license_activate', $api_key, $api_email);
          }
  
          if ( $activate_results == false ) {
            add_settings_error( "wcfm_{$this->tab}_settings_name", esc_attr( "wcfm_{$this->tab}_settings_name" ), __( 'Connection failed to the License Key API server. Try again later.', 'wc-frontend-manager-ultimate' ), 'error' );
            $new_input[$WCFMu->license->license_api_key] = '';
            $new_input[$WCFMu->license->license_activation_email] = '';
            update_option( $WCFMu->license->license_activated_key, 'Deactivated' );
          }
  
          if ( isset( $activate_results['code'] ) ) {
          	if(!isset($activate_results['additional info'])) $activate_results['additional info'] = '';
  
            switch ( $activate_results['code'] ) {
              case '100':
                add_settings_error( "wcfm_{$this->tab}_settings_name", esc_attr( "wcfm_{$this->tab}_settings_name" ), "{$activate_results['error']}. {$activate_results['additional info']}", 'error' );
                $new_input[$WCFMu->license->license_activation_email] = '';
                $new_input[$WCFMu->license->license_api_key] = '';
                update_option( $WCFMu->license->license_activated_key, 'Deactivated' );
              break;
              case '101':
                add_settings_error( "wcfm_{$this->tab}_settings_name", esc_attr( "wcfm_{$this->tab}_settings_name" ), "{$activate_results['error']}. {$activate_results['additional info']}", 'error' );
                $new_input[$WCFMu->license->license_api_key] = '';
                $new_input[$WCFMu->license->license_activation_email] = '';
                update_option( $WCFMu->license->license_activated_key, 'Deactivated' );
              break;
              case '102':
                add_settings_error( "wcfm_{$this->tab}_settings_name", esc_attr( "wcfm_{$this->tab}_settings_name" ), "{$activate_results['error']}. {$activate_results['additional info']}", 'error' );
                $new_input[$WCFMu->license->license_api_key] = '';
                $new_input[$WCFMu->license->license_activation_email] = '';
                update_option( $WCFMu->license->license_activated_key, 'Deactivated' );
              break;
              case '103':
                  add_settings_error( "wcfm_{$this->tab}_settings_name", esc_attr( "wcfm_{$this->tab}_settings_name" ), "{$activate_results['error']}. {$activate_results['additional info']}", 'error' );
                  $new_input[$WCFMu->license->license_api_key] = '';
                  $new_input[$WCFMu->license->license_activation_email] = '';
                  update_option( $WCFMu->license->license_activated_key, 'Deactivated' );
              break;
              case '104':
                  add_settings_error( "wcfm_{$this->tab}_settings_name", esc_attr( "wcfm_{$this->tab}_settings_name" ), "{$activate_results['error']}. {$activate_results['additional info']}", 'error' );
                  $new_input[$WCFMu->license->license_api_key] = '';
                  $new_input[$WCFMu->license->license_activation_email] = '';
                  update_option( $WCFMu->license->license_activated_key, 'Deactivated' );
              break;
              case '105':
                  add_settings_error( "wcfm_{$this->tab}_settings_name", esc_attr( "wcfm_{$this->tab}_settings_name" ), "{$activate_results['error']}. {$activate_results['additional info']}", 'error' );
                  $new_input[$WCFMu->license->license_api_key] = '';
                  $new_input[$WCFMu->license->license_activation_email] = '';
                  update_option( $WCFMu->license->license_activated_key, 'Deactivated' );
              break;
              case '106':
                  add_settings_error( "wcfm_{$this->tab}_settings_name", esc_attr( "wcfm_{$this->tab}_settings_name" ), "{$activate_results['error']}. {$activate_results['additional info']}", 'error' );
                  $new_input[$WCFMu->license->license_api_key] = '';
                  $new_input[$WCFMu->license->license_activation_email] = '';
                  update_option( $WCFMu->license->license_activated_key, 'Deactivated' );
              break;
            }
          }
        } // End Plugin Activation
      } else {
        if ( $activation_status == 'Activated' ) {
          $reset = $this->api_manager_license_key->deactivate( $args ); // reset license key activation
   
          if ( $reset == true ) {
            $new_input[$WCFMu->license->license_api_key] = '';
            $new_input[$WCFMu->license->license_activation_email] = '';
            update_option( $WCFMu->license->license_activated_key, 'Deactivated' );
            //$WCFMu->license->wcfmu_plugin_tracker('license_deactivate', $api_key, $api_email);
    
            add_settings_error( "wcfm_{$this->tab}_settings_name", esc_attr( "wcfm_{$this->tab}_settings_name" ), __( 'Plugin license deactivated.', 'wc-frontend-manager-ultimate' ), 'updated' );
          }
        }
      }
    }

    unset($new_input[$WCFMu->license->license_deactivate_checkbox]);
    return $new_input;
  }
  
  // Deactivate the current license key before activating the new license key
	public function replace_license_key( $current_api_key ) {
	  global $WCFMu;
	  
		$args = array(
			'email' => $this->options[$WCFMu->license->license_activation_email],
			'licence_key' => $current_api_key,
			);

		$reset = $this->api_manager_license_key->deactivate( $args ); // reset license key activation

		if ( $reset == true )
			return true;

		return add_settings_error( "wcfm_{$this->tab}_settings_name", esc_attr( "wcfm_{$this->tab}_settings_name" ), __( 'The license could not be deactivated. Use the License Deactivation tab to manually deactivate the license before activating a new license.', 'wc-frontend-manager-ultimate' ), 'updated' );
	}
  
  /** 
   * Print the Section text
   */
  public function activation_settings_section_info() {
    global $WCFMu;
    //_e('Enter your default settings below', 'wc-frontend-manager-ultimate');
  }
  
  /** 
   * Print the Section text
   */
  public function deactivation_settings_section_info() {
    global $WCFMu;
    //_e('Enter your custom settings below', 'wc-frontend-manager-ultimate');
  }
}