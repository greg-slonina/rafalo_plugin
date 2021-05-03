<?php
/**
 * WCFM plugin shortcode
 *
 * Plugin Shortcode output
 *
 * @author 		WC Lovers
 * @package 	wcfm/includes/shortcode
 * @version   1.0.0
 */
 
class WCFM_Frontend_Manager_Shortcode {

	public function __construct() {

	}

	/**
	 * Output the WC Frontend Manager shortcode.
	 *
	 * @access public
	 * @param array $atts
	 * @return void
	 */
	static public function output( $attr ) {
		global $WCFM, $wp, $WCFM_Query;
		$WCFM->nocache();
		
		$end_point = 'wcfm-dashboard';
		
		echo '<div id="wcfm-main-contentainer">';
		do_action( 'wcfm_main_contentainer_before' );
		echo  '<div id="wcfm-content">';
		
		if ( isset( $wp->query_vars['page'] ) ) {
			$WCFM->library->load_views( 'wcfm-dashboard' );
		} else {
			$wcfm_endpoints = $WCFM_Query->get_query_vars();
			$is_endpoint = false;
			foreach ( $wcfm_endpoints as $key => $value ) {
				if ( isset( $wp->query_vars[ $key ] ) ) {
					$WCFM->library->load_views( $key );
					$end_point = $key;
					$is_endpoint = true;
				}
			}
			
			if( !$is_endpoint && is_wcfm_page() ) {
				$WCFM->library->load_views( 'wcfm-dashboard' );
			}
		}
		
		echo '</div>';
		do_action( 'wcfm_main_contentainer_after', $end_point );
		
		// Since WP 5.7
		$dataTables_language = '{"processing": "' . __('Processing...', 'wc-frontend-manager' ) . '" , "search": "' . __('Search:', 'wc-frontend-manager' ) . '", "lengthMenu": "' . __('Show _MENU_ entries', 'wc-frontend-manager' ) . '", "info": " ' . __('Showing _START_ to _END_ of _TOTAL_ entries', 'wc-frontend-manager' ) . '", "infoEmpty": "' . __('Showing 0 to 0 of 0 entries', 'wc-frontend-manager' ) . '", "infoFiltered": "' . __('(filtered _MAX_ entries of total)', 'wc-frontend-manager' ) . '", "loadingRecords": "' . __('Loading...', 'wc-frontend-manager' ) . '", "zeroRecords": "' . __('No matching records found', 'wc-frontend-manager' ) . '", "emptyTable": "' . __('No data in the table', 'wc-frontend-manager' ) . '", "paginate": {"first": "' . __('First', 'wc-frontend-manager' ) . '", "previous": "' . __('Previous', 'wc-frontend-manager' ) . '", "next": "' . __('Next', 'wc-frontend-manager' ) . '", "last": "' .  __('Last', 'wc-frontend-manager') . '"}, "buttons": {"print": "' . __('Print', 'wc-frontend-manager' ) . '", "pdf": "' . __('PDF', 'wc-frontend-manager' ) . '", "excel": "' . __('Excel', 'wc-frontend-manager' ) . '", "csv": "' . __('CSV', 'wc-frontend-manager' ) . '"}}';
		?>
		<script>
		var dataTables_language = '<?php echo $dataTables_language; ?>';
		</script>
		<?php
		echo '</div>';
	}
}
