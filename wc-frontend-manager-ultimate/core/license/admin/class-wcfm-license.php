<?php

class WCFM_License {
	
	private $current_tab;
	
	public function __construct($tab) {
		global $WCFMu, $WCFM, $GLOBALS;
		
		$this->current_tab = $tab;
		
		add_action( 'admin_init', array( $this, 'license_page_init' ) );
		add_action('admin_enqueue_scripts', array(&$this, 'enqueue_admin_script'), 50);		
		
		add_menu_page(
				__('WCFM License', 'wc-frontend-manager-ultimate'), 
				__('WCFM License', 'wc-frontend-manager-ultimate'), 
				'manage_options', 
				'wcfm-license', 
				array( $this, 'create_wcfm_license_settings' ),
				$WCFMu->plugin_url . 'assets/images/wcfm_ico.png'
		);
	}
	
	/**
   * Register and add settings
   */
  public function license_page_init() { 
    do_action('befor_settings_page_init');
    
    // Register each tab settings
    foreach( $this->get_wcfm_license_tabs() as $tab => $name ) :
      do_action("settings_page_{$tab}_tab_init", $tab);
    endforeach;
    
    do_action('after_settings_page_init');
  }
	
	function get_wcfm_license_tabs() {
    global $WCFMu, $WCFM;
    $tabs = apply_filters('wcfm_license_tabs', array(
       
    ));
    return $tabs;
  }
  
  function get_wcfm_license_tab_desc() {
  	global $WCFMu, $WCFM;
    $tab_desc = apply_filters('wcfm_license_tabs_desc', array(
        
    ));
    return $tab_desc;
  }
  
  function wcfm_license_tabs( $current = '' ) {
  	global $WCFM, $WCFM;
    if ( isset ( $_GET['tab'] ) ) :
      $current = $_GET['tab'];
    else:
      $current = $this->current_tab;
    endif;
    
    $links = array();
    foreach( $this->get_wcfm_license_tabs() as $tab => $name ) :
      if ( $tab == $current ) :
        $links[] = "<a class='nav-tab nav-tab-active' href='?page=wcfm-license&tab=$tab'>$name</a>";
      else :
        $links[] = "<a class='nav-tab' href='?page=wcfm-license&tab=$tab'>$name</a>";
      endif;
    endforeach;
    echo '<div class="icon32" id="wcfm_menu_ico"><br></div>';
    echo '<h2 class="nav-tab-wrapper">';
    foreach ( $links as $link )
      echo $link;
    echo '</h2>';
    
    foreach( $this->get_wcfm_license_tabs() as $tab => $name ) :
      if ( $tab == $current ) :
        printf( __( "<h2>%s License</h2>", 'wc-frontend-manager-ultimate') , $name);
      endif;
    endforeach;
    
    $tab_desc = $this->get_wcfm_license_tab_desc();
    foreach( $this->get_wcfm_license_tabs() as $tabd => $named ) :
      if ( $tabd == $current && !empty($tab_desc[$tabd]) ) :
        printf( __( "<h4 style='border-bottom: 1px solid rgb(215, 211, 211);padding-bottom: 21px;'>%s</h4>", 'wc-frontend-manager-ultimate') , $tab_desc[$tabd]);
      endif;
    endforeach;
  }
  
  /**
   * Options page callback
   */
  public function create_wcfm_license_settings() {
    global $WCFMu, $WCFM;
    ?>
    <div class="wrap">
      <?php $this->wcfm_license_tabs(); ?>
      <?php
      $tab = ( isset( $_GET['tab'] ) ? $_GET['tab'] : 'wcfmu_license' );
      $this->options = get_option( "wcfm_{$tab}_settings_name" );
      //print_r($this->options);
      
      // This prints out all hidden setting errors
      settings_errors("wcfm_{$tab}_settings_name");
      ?>
      <form class='wcfm_license_settings' method="post" action="options.php">
      <?php
        // This prints out all hidden setting fields
        settings_fields( "wcfm_{$tab}_settings_group" );   
        do_settings_sections( "wcfm-{$tab}-settings-admin" );
        submit_button(); 
      ?>
      </form>
    </div>
    <?php
    do_action('wcfm_admin_footer');
  }
  
  /**
	 * Admin Scripts
	*/
	public function enqueue_admin_script() {
		global $WCFMu, $WCFM;
		$screen = get_current_screen();
		
		// Enqueue admin script and stylesheet from here
		if (in_array( $screen->id, array( 'toplevel_page_wcfm-license' ))) : 
		  wp_enqueue_style('wcfm_admin_css',  $WCFM->plugin_url.'assets/admin/css/admin.css', array(), $WCFMu->version);
		endif;
	}
	
}