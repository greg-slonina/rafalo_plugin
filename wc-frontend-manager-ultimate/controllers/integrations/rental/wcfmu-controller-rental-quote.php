<?php
/**
 * WCFM plugin controllers
 *
 * Plugin Request Quote Controller
 *
 * @author 		WC Lovers
 * @package 	wcfmu/controllers/thirdparty
 * @version   3.2.3
 */

class WCFMu_Rental_Quote_Controller {
	
	public function __construct() {
		global $WCFM;
		
		$this->processing();
	}
	
	public function processing() {
		global $WCFM, $wpdb, $_POST;
		
		$length = $_POST['length'];
		$offset = $_POST['start'];
		
		$include_quotes = apply_filters( 'wcfm_rental_include_quotes', '' );
		
		$args = array(
							'posts_per_page'   => $length,
							'offset'           => $offset,
							'category'         => '',
							'category_name'    => '',
							'orderby'          => 'date',
							'order'            => 'DESC',
							'include'          => $include_quotes,
							'exclude'          => '',
							'meta_key'         => '',
							'meta_value'       => '',
							'post_type'        => 'request_quote',
							'post_mime_type'   => '',
							'post_parent'      => '',
							//'author'	   => get_current_user_id(),
							'post_status'      => 'any',
							'suppress_filters' => 0 
						);
		if( isset( $_POST['search'] ) && !empty( $_POST['search']['value'] )) $args['s'] = $_POST['search']['value'];
		
		if( isset($_POST['quote_status']) && !empty($_POST['quote_status']) && ( $_POST['quote_status'] != 'all' ) ) $args['post_status'] = $_POST['quote_status'];
		
		$args = apply_filters( 'wcfm_rental_quote_args', $args );
		
		$wcfm_quotes_array = get_posts( $args );
		
		// Get Filtered Post Count
		$filtered_quote_count = 0;
		$args['posts_per_page'] = -1;
		$args['offset'] = 0;
		$wcfm_filterd_quotes_array = get_posts( $args );
		$filtered_quote_count = count($wcfm_filterd_quotes_array);
		
		// Generate Quotes JSON
		$wcfm_quotes_json = '';
		$wcfm_quotes_json = '{
															"draw": ' . $_POST['draw'] . ',
															"recordsTotal": ' . $filtered_quote_count . ',
															"recordsFiltered": ' . $filtered_quote_count . ',
															"data": ';
		if(!empty($wcfm_quotes_array)) {
			$index = 0;
			$wcfm_quotes_json_arr = array();
			foreach($wcfm_quotes_array as $wcfm_quotes_single) {
				
				$order_quote_meta = json_decode( get_post_meta($wcfm_quotes_single->ID, 'order_quote_meta', true), true );
				$forms = array();
		
				foreach ($order_quote_meta as $key => $meta) {
						if( array_key_exists('forms', $meta) ) {
							$forms = $meta['forms'];
						}
				}
				
				// Quote
				$wcfm_quotes_json_arr[$index][] =  '<a href="' . get_wcfm_rental_quote_details_url( $wcfm_quotes_single->ID ) . '" class="wcfm_quote_title wcfm_dashboard_item_title">#' . $wcfm_quotes_single->ID . '</a> ' . esc_html('by', 'redq-rental' ) . ' ' . $forms['quote_first_name'] . ' ' . $forms['quote_last_name'];
				
				// Product
				$product_id = get_post_meta( $wcfm_quotes_single->ID, 'add-to-cart', true);
				$product_title = get_the_title( $product_id );
				$product_url = get_the_permalink( $product_id );
				$wcfm_quotes_json_arr[$index][] =  '<a class="quote_items" href="' . $product_url . '" class="wcfm_quote_title">' . $product_title . '</a>';
				
				// Status
				$wcfm_quotes_json_arr[$index][] =  '<span class="quote-status quote-status-' . $wcfm_quotes_single->post_status . '">' . ucfirst( substr( $wcfm_quotes_single->post_status, 6 ) ) . '</span>';
				
				// Email
				if( isset( $forms['email'] ) && !empty( $forms['email'] ) ) {
					$wcfm_quotes_json_arr[$index][] =  '<a href="mailto:' . $forms['email'] . '">' . $forms['email'] . '</a>';
				} else {
					$wcfm_quotes_json_arr[$index][] =  '&ndash;';
				}
				
				// Date Posted
				$wcfm_quotes_json_arr[$index][] = date_i18n( get_option( 'date_format' ), strtotime( $wcfm_quotes_single->post_date ) );

				// Action
				$actions = '';
				$actions .= '<a class="wcfm-action-icon" href="' . get_wcfm_rental_quote_details_url( $wcfm_quotes_single->ID ) . '"><span class="wcfmfa fa-eye text_tip" data-tip="' . esc_attr__( 'Details', 'wc-frontend-manager-ultimate' ) . '"></span></a>';
				
				$wcfm_quotes_json_arr[$index][] = apply_filters ( 'wcfm_rental_quotes_actions', $actions, $wcfm_quotes_single );
				
				$index++;
			}												
		}
		if( !empty($wcfm_quotes_json_arr) ) $wcfm_quotes_json .= json_encode($wcfm_quotes_json_arr);
		else $wcfm_quotes_json .= '[]';
		$wcfm_quotes_json .= '
													}';
													
		echo $wcfm_quotes_json;
	}
}