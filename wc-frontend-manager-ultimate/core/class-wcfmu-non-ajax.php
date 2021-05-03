<?php
/**
 * WCFMu plugin core
 *
 * Plugin non Ajax Controler
 *
 * @author 		WC Lovers
 * @package 	wcfmu/core
 * @version   2.4.3
 */
 
class WCFMu_Non_Ajax {

	public function __construct() {
		global $WCFM, $WCFMu;
		
		// Plugins page help links
		add_filter( 'plugin_action_links_' . $WCFMu->plugin_base_name, array( &$this, 'wcfmu_plugin_action_links' ) );
		add_filter( 'plugin_row_meta', array( &$this, 'wcfmu_plugin_row_meta' ), 10, 2 );
	}
	
	/**
	 * Show action links on the plugin screen.
	 *
	 * @param	mixed $links Plugin Action links
	 * @return	array
	 */
	public function wcfmu_plugin_action_links( $links ) {
		global $WCFMu;
		$action_links = array(
			'wcfmu_license' => '<a href="' . esc_url( admin_url( 'admin.php?page=wcfm-license&tab=' . str_replace('-', '_', esc_attr($WCFMu->token)) . '_license' ) ) . '" aria-label="' . esc_attr__( 'Set WCFMu License', 'wc-frontend-manager-ultimate' ) . '">' . esc_html__( 'License', 'wc-frontend-manager-ultimate' ) . '</a>',
		);

		return array_merge( $action_links, $links );
	}
	
	/**
	 * Show row meta on the plugin screen.
	 *
	 * @param	mixed $links Plugin Row Meta
	 * @param	mixed $file  Plugin Base file
	 * @return	array
	 */
	public function wcfmu_plugin_row_meta( $links, $file ) {
		global $WCFM, $WCFMu;
		if ( $WCFMu->plugin_base_name == $file ) {
			$row_meta = array(
				'changelog'      => '<a target="_blank" href="' . esc_url( apply_filters( 'wcfmu_changelog_url', 'https://wclovers.com/wcfm-ultimate-changelog/' ) ) . '" aria-label="' . esc_attr__( 'View WCFMu Change Log', 'wc-frontend-manager-ultimate' ) . '">' . esc_html__( 'Change Log', 'wc-frontend-manager-ultimate' ) . '</a>',
				//'docs'         => '<a target="_blank" href="' . esc_url( apply_filters( 'wcfm_docs_url', 'http://wclovers.com/knowledgebase/' ) ) . '" aria-label="' . esc_attr__( 'View WCFM documentation', 'wc-frontend-manager' ) . '">' . esc_html__( 'Documentation', 'wc-frontend-manager' ) . '</a>',
				'guide'          => '<a target="_blank" href="' . esc_url( apply_filters( 'wcfm_guide_url', 'https://wclovers.com/documentation/developers-guide/' ) ) . '" aria-label="' . esc_attr__( 'View WCFM Developer Guide', 'wc-frontend-manager' ) . '">' . esc_html__( 'Developer Guide', 'wc-frontend-manager' ) . '</a>',
				'supportu'      => '<a target="_blank" href="' . esc_url( apply_filters( 'wcfm_support_url', 'https://wclovers.com/premium-support/' ) ) . '" aria-label="' . esc_attr__( 'Visit premium customer support', 'woocommerce' ) . '">' . esc_html__( 'Premium Support', 'woocommerce' ) . '</a>',
				//'contactus'      => '<a target="_blank" href="' . esc_url( apply_filters( 'wcfm_contactus_url', 'https://wclovers.com/contact-us/' ) ) . '" aria-label="' . esc_attr__( 'Any WC help feel free to contact us', 'wc-frontend-manager' ) . '">' . esc_html__( 'Contact US', 'wc-frontend-manager' ) . '</a>',
				'customizationu' => '<a target="_blank" href="' . esc_url( apply_filters( 'wcfm_customization_url', 'https://wclovers.com/woocommerce-multivendor-customization/' ) ) . '" aria-label="' . esc_attr__( 'Any WC help feel free to contact us', 'wc-frontend-manager' ) . '">' . esc_html__( 'Customization Help', 'wc-frontend-manager' ) . '</a>'
			);
			return array_merge( $links, $row_meta );
		}

		return (array) $links;
	}
}