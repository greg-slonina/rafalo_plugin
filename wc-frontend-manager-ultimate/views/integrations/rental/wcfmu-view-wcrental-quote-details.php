<?php
/**
 * WCFM plugin view
 *
 * WCFM Request Quote Details View
 *
 * @author 		WC Lovers
 * @package 	wcfmu/views/thrdparty
 * @version   3.2.3
 */
 
global $wp, $WCFM, $WCFMu, $wpdb;

if( !$wcfm_is_allow_rental = apply_filters( 'wcfm_is_allow_rental', true ) ) {
	wcfm_restriction_message_show( "Request Quote" );
	return;
}


$quote_id = '';
if( isset( $wp->query_vars['wcfm-rental-quote-details'] ) && !empty( $wp->query_vars['wcfm-rental-quote-details'] ) ) {
	$quote_id = $wp->query_vars['wcfm-rental-quote-details'];
}

if( !$quote_id ) return;

$post = get_post($quote_id);

$quote_statuses = apply_filters( 'redq_get_request_quote_post_statuses',
          array(
            'quote-pending'    => _x( 'Pending', 'Quote status', 'redq-rental' ),
            'quote-processing' => _x( 'Processing', 'Quote status', 'redq-rental' ),
            'quote-on-hold'    => _x( 'On Hold', 'Quote status', 'redq-rental' ),
            'quote-accepted'  => _x( 'Accepted', 'Quote status', 'redq-rental' ),
            'quote-completed'  => _x( 'Completed', 'Quote status', 'redq-rental' ),
            'quote-cancelled'  => _x( 'Cancelled', 'Quote status', 'redq-rental' ),
          )
        );
$price = get_post_meta($post->ID, '_quote_price', true);

do_action( 'before_wcfm_quotes_details' );
?>

<div class="collapse wcfm-collapse" id="wcfm_quote_details">

  <div class="wcfm-page-headig">
		<span class="wcfmfa fa-snowflake"></span>
		<span class="wcfm-page-heading-text"><?php _e( 'Quote Details', 'wc-frontend-manager-ultimate' ); ?></span>
		<?php do_action( 'wcfm_page_heading' ); ?>
	</div>
	<div class="wcfm-collapse-content">
		<div id="wcfm_page_load"></div>
		
		<div class="wcfm-container wcfm-top-element-container">
			<h2><?php _e( 'Quote Request #', 'wc-frontend-manager-ultimate' ); echo $quote_id; ?></h2>
			<span class="quote-status quote-status-<?php echo $post->post_status; ?>"><?php echo ucfirst( substr( $post->post_status, 6 ) ); ?></span>
			
			<?php
			if( $allow_wp_admin_view = apply_filters( 'wcfm_allow_wp_admin_view', true ) ) {
				?>
				<a target="_blank" class="wcfm_wp_admin_view text_tip" href="<?php echo admin_url('post.php?post='.$quote_id.'&action=edit'); ?>" data-tip="<?php _e( 'WP Admin View', 'wc-frontend-manager-ultimate' ); ?>"><span class="fab fa-wordpress fa-wordpress-simple"></span></a>
				<?php
			}
			
			echo '<a class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_rental_url().'" data-tip="' . __('Calendar View', 'wc-frontend-manager-ultimate') . '"><span class="wcfmfa fa-calendar-check"></span></a>';
			
			echo '<a class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_rental_quote_url().'" data-tip="' . __('Quote Requests', 'wc-frontend-manager-ultimate') . '"><span class="wcfmfa fa-snowflake-o"></span></a>';
			
			if( $has_new = apply_filters( 'wcfm_add_new_product_sub_menu', true ) ) {
				echo '<a class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_edit_product_url().'" data-tip="' . __('Add New Product', 'wc-frontend-manager-ultimate') . '"><span class="wcfmfa fa-cube"></span><span class="text">' . __('Add New', 'wc-frontend-manager-ultimate') . '</span></a>';
			}
			?>
			<div class="wcfm_clearfix"></div>
		</div>
	  <div class="wcfm-clearfix"></div><br />
	  
	  <?php do_action( 'begin_wcfm_quotes_details' ); ?>
		
		<!-- collapsible -->
		<div class="page_collapsible quotes_details_general" id="wcfm_general_options">
			<?php _e('Quote Actions', 'wc-frontend-manager-ultimate'); ?><span></span>
		</div>
		<div class="wcfm-container">
			<div id="quotes_details_general_expander" class="wcfm-content">
			
				<p class="form-field form-field-wide">
					<label class="wcfm_title" for="quote_price"><?php esc_html_e('Price', 'redq-rental')?> (<?php echo esc_attr( get_post_meta( $post->ID, 'currency-symbol', true ) ) ?>)</label>
					<input type="text" id="wcfm_quote_price" class="wcfm-text" value="<?php echo $price; ?>" />
				</p>
				
				<p class="form-field form-field-wide">
					<label class="wcfm_title" for="wcfm_quote_status"><?php _e( 'Quote Status:', 'redq-rental' ); ?></label>
					<select id="wcfm_quote_status" class="wcfm-select" name="quote_status">
						<?php
							foreach ( $quote_statuses as $key => $value ) {
								echo '<option value="' . esc_attr( $key ) . '" ' . selected( $key, $post->post_status, false ) . '>' . $value . '</option>';
							}
						?>
					</select>
					<input type="submit" class="wcfm_modify_quote_status wcfm_submit_button button" id="wcfm_modify_quote_status" data-quoteid="<?php echo $quote_id; ?>" value="<?php _e( 'Update', 'wc-frontend-manager-ultimate' ); ?>" />
					<div class="wcfm_clearfix"></div>
				</p>
				<div class="wcfm_clearfix"></div>
				<div class="wcfm-message" tabindex="-1"></div>
				<div class="wcfm_clearfix"></div>
			</div>
		</div>
		<div class="wcfm_clearfix"></div>
		<br />
		<!-- collapsible End -->
		
		<!-- collapsible -->
		<div class="page_collapsible quotes_details_quote" id="wcfm_quote_options">
			<?php _e('Quote Management', 'wc-frontend-manager-ultimate'); ?><span></span>
		</div>
		<div class="wcfm-container">
			<div id="quotes_details_quote_expander" class="wcfm-content">
				
			  <div id="request-a-quote-data">
					<h2><?php esc_html_e('Quote', 'redq-rental') ?> <?php echo '#' . $post->ID ?> <?php esc_html_e('Details', 'redq-rental') ?></h2>
					<div class="wcfm-clearfix"></div>
					<p class="quote_number">
							<?php
								$product_id = get_post_meta($post->ID, 'add-to-cart', true);
								$product_title = get_the_title($product_id);
								$product_url = get_the_permalink($product_id);
			
								if( function_exists( 'redq_rental_get_settings' ) ) {
									$get_labels = redq_rental_get_settings( $product_id, 'labels', array('pickup_location', 'return_location', 'pickup_date', 'return_date', 'resources', 'categories', 'person', 'deposites') );
								} else {
									$get_labels = reddq_rental_get_settings( $product_id, 'labels', array('pickup_location', 'return_location', 'pickup_date', 'return_date', 'resources', 'categories', 'person', 'deposites') );
								}
								$labels = $get_labels['labels'];
			
								$order_quote_meta = json_decode( get_post_meta($post->ID, 'order_quote_meta', true), true );
			
							?>
							<?php esc_html_e('Request for:', 'redq-rental') ?> <a class="quote_items" href="<?php echo esc_url( $product_url ) ?>" target="_blank"><?php echo $product_title ?></a>
					</p>
			
			
					<?php foreach ($order_quote_meta as $meta) { ?>
						<?php
									if( isset( $meta['name'] ) ) {
			
										switch ($meta['name']) {
											case 'add-to-cart':
												# code...
												break;
			
											case 'currency-symbol':
												# code...
												break;
			
											case 'pickup_location':
													if(!empty($meta['value'])):
														$pickup_location_title = $labels['pickup_location'];
														$dval = explode('|', $meta['value'] );
														$pickup_value = $dval[0].' ( '.wc_price($dval[2]). ' )'; ?>
														<dt style="float: left;margin-right: 10px;"><?php echo esc_attr( $pickup_location_title ) ?>:</dt>
														<dd>
																<p><strong><?php echo $pickup_value; ?></strong></p>
														</dd>
											<?php
													endif;
													break;
			
											case 'dropoff_location':
													if(!empty($meta['value'])):
														$return_location_title = $labels['return_location'];
														$dval = explode('|', $meta['value'] );
														$return_value = $dval[0].' ( '.wc_price($dval[2]). ' )'; ?>
														<dt style="float: left;margin-right: 10px;"><?php echo esc_attr( $return_location_title ) ?>:</dt>
														<dd>
																<p><strong><?php echo $return_value; ?></strong></p>
														</dd>
											<?php
												endif;
												break;
			
											case 'pickup_date':
													if(!empty($meta['value'])):
														$pickup_date_title = $labels['pickup_date'];
														$pickup_date_value = $meta['value']; ?>
														<dt style="float: left;margin-right: 10px;"><?php echo esc_attr( $pickup_date_title ) ?>:</dt>
														<dd>
																<p><strong><?php echo $pickup_date_value; ?></strong></p>
														</dd>
											<?php
												endif;
												break;
			
											case 'pickup_time':
													if(!empty($meta['value'])):
														$pickup_time_title = $labels['pickup_time'];
														$pickup_time_value = $meta['value'] ? $meta['value'] : '' ; ?>
														<dt style="float: left;margin-right: 10px;"><?php echo esc_attr( $pickup_time_title ) ?>:</dt>
														<dd>
																<p><strong><?php echo $pickup_time_value; ?></strong></p>
														</dd>
											<?php
													endif; break;
			
											case 'dropoff_date':
													if(!empty($meta['value'])):
														$return_date_title = $labels['return_date'];
														$return_date_value = $meta['value'] ? $meta['value'] : '' ; ?>
														<dt style="float: left;margin-right: 10px;"><?php echo esc_attr( $return_date_title ) ?>:</dt>
														<dd>
																<p><strong><?php echo $return_date_value; ?></strong></p>
														</dd>
											<?php
												endif;
												break;
			
											case 'dropoff_time':
													if(!empty($meta['value'])):
									$return_time_title = $labels['return_time'];
									$return_time_value = $meta['value'] ? $meta['value'] : '' ; ?>
									<dt style="float: left;margin-right: 10px;"><?php echo esc_attr( $return_time_title ) ?>:</dt>
									<dd>
										<p><strong><?php echo $return_time_value; ?></strong></p>
									</dd>
											<?php
												endif;
												break;
			
											case 'additional_adults_info':
													if(!empty($meta['value'])):
														$person_title = $labels['adults'];
														$dval = explode('|', $meta['value'] );
														$person_value = $dval[0].' ( '.wc_price($dval[1]).' - '.$dval[2]. ' )'; ?>
														<dt style="float: left;margin-right: 10px;"><?php echo esc_attr( $person_title ) ?>:</dt>
														<dd>
																<p><strong><?php echo $person_value; ?></strong></p>
														</dd>
											<?php
												endif;
												break;
			
											case 'extras': ?>
												<?php
														$resources_title = $labels['resource'];
														$resource_name = '';
														$payable_resource = array();
														foreach ($meta['value'] as $key => $value) {
										$extras = explode('|', $value);
										$payable_resource[$key]['resource_name'] = $extras[0];
										$payable_resource[$key]['resource_cost'] = $extras[1];
										$payable_resource[$key]['cost_multiply'] = $extras[2];
										$payable_resource[$key]['resource_hourly_cost'] = $extras[3];
														}
														foreach ($payable_resource as $key => $value) {
										if($value['cost_multiply'] === 'per_day'){
											$resource_name .= $value['resource_name'].' ( '.wc_price($value['resource_cost']).' - '.__('Per Day','redq-rental').' )'.' , <br> ';
										}else{
											$resource_name .= $value['resource_name'].' ( '.wc_price($value['resource_cost']).' - '.__('One Time','redq-rental').' )'.' , <br> ';
										}
														}
													?>
													<dt style="float: left;margin-right: 10px;"><?php echo esc_attr($resources_title);  ?></dt>
													<dd>
														<p><strong><?php echo $resource_name; ?></strong></p>
													</dd>
											<?php
												break;
											case 'security_deposites': ?>
													<?php
														$deposits_title = $labels['deposite'];
														$deposite_name = '';
														$payable_deposits = array();
														foreach ($meta['value'] as $key => $value) {
										$extras = explode('|', $value);
										$payable_deposits[$key]['deposite_name'] = $extras[0];
										$payable_deposits[$key]['deposite_cost'] = $extras[1];
										$payable_deposits[$key]['cost_multiply'] = $extras[2];
										$payable_deposits[$key]['deposite_hourly_cost'] = $extras[3];
														}
														foreach ($payable_deposits as $key => $value) {
																if($value['cost_multiply'] === 'per_day'){
																	$deposite_name .= $value['deposite_name'].' ( '.wc_price($value['deposite_cost']).' - '.__('Per Day','redq-rental').' )'.' , <br> ';
																}else{
																	$deposite_name .= $value['deposite_name'].' ( '.wc_price($value['deposite_cost']).' - '.__('One Time','redq-rental').' )'.' , <br> ';
																}
														}
													?>
													<dt style="float: left;margin-right: 10px;">
														<?php echo esc_attr($deposits_title); ?>
													</dt>
													<dd>
														<p><strong><?php echo $deposite_name; ?></strong></p>
													</dd>
											<?php
												break;
			
											default: ?>
													<dt style="float: left;margin-right: 10px;"><?php echo esc_attr( $meta['name'] ) ?>:</dt>
													<dd>
														<p><strong><?php echo esc_attr( $meta['value'] ) ?></strong></p>
													</dd>
											<?php
												break;
										}
									}
							?>
			
						<?php
								if( isset( $meta['forms'] ) ) {
									$contacts = $meta['forms'];  ?>
									<h2><?php esc_html_e('Customer information','redq-rental'); ?></h2>
									<div class="wcfm-clearfix"></div>
									<?php foreach ($contacts as $key => $value) { ?>
											<?php if( $key !== 'quote_message' ) : ?>
											<p>
												<strong><?php echo ucfirst( substr( $key, 6) ) ?> : </strong><?php echo $value ?>
											</p>
											<?php endif ?>
									<?php } ?>
			
								<?php } ?>
			
					<?php } ?>
				</div>

		 </div>
		</div>
		<div class="wcfm_clearfix"></div><br />
		
		<!-- collapsible -->
		<div class="page_collapsible quotes_details_general" id="wcfm_general_options">
			<?php _e('Quote Messages', 'wc-frontend-manager-ultimate'); ?><span></span>
		</div>
		<div class="wcfm-container">
			<div id="quotes_details_general_expander" class="wcfm-content">
			
			 <?php if( apply_filters( 'wcfm_add_order_notes', true ) ) { ?>
				<div class="add_note">
					<h2><?php _e( 'Send Message', 'wc-frontend-manager-ultimate' ); ?></h2>
					<div class="wcfm-clearfix"></div>
					<p>
						<textarea type="text" name="order_note" id="add_order_note" class="input-text" cols="20" rows="5"></textarea>
					</p>
					<p>
						<div class="wcfm-clearfix"></div>
						<a href="#" class="add_note button wcfm_submit_button" id="wcfm_add_order_note" data-quote_id="<?php echo $post->ID; ?>"><?php _e( 'Send', 'wc-frontend-manager-ultimate' ); ?></a>
						<div class="wcfm-clearfix"></div>
					</p>
				</div>
			 <?php } ?>
			
				<?php
					$quote_id = $post->ID;
					// Remove the comments_clauses where query here.
					remove_filter( 'comments_clauses', 'exclude_request_quote_comments_clauses' );
					$args = array(
					'post_id'   => $quote_id,
					'orderby'   => 'comment_ID',
					'order'     => 'DESC',
					'approve'   => 'approve',
					'type'      => 'quote_message'
					);
					$comments = get_comments($args); ?>
				<ul class="quote-message">
				<?php foreach($comments as $comment) : ?>
					<?php
					if( $comment->comment_content ) {
						$list_class = 'message-list';
						$content_class = 'quote-message-content';
						if($comment->user_id === get_post_field( 'post_author', $quote_id ) ) {
							$list_class .= ' customer';
							$content_class .= ' customer';
						}
					?>
					<li class="<?php echo $list_class ?>">
							<div class="<?php echo $content_class ?>">
								<?php echo wpautop( wptexturize( wp_kses_post( $comment->comment_content ) ) ); ?>
							</div>
							<p class="meta">
								<abbr class="exact-date" title="<?php echo $comment->comment_date; ?>"><?php printf( __( 'added on %1$s at %2$s', 'redq-rental' ), date_i18n( wc_date_format(), strtotime( $comment->comment_date ) ), date_i18n( wc_time_format(), strtotime( $comment->comment_date ) ) ); ?></abbr>
							<?php printf( ' ' . __( 'by %s', 'redq-rental' ), $comment->comment_author ); ?>
							<!-- <a href="#" class="delete-message"><?php _e( 'Delete', 'redq-rental' ); ?></a> -->
							</p>
					</li>
					<?php } ?>
				<?php endforeach; ?>
				</ul>
				<div class="wcfm_clearfix"></div>
			</div>
		</div>
		<div class="wcfm_clearfix"></div>
		<br />
		<!-- collapsible End -->
		
		<?php do_action( 'after_wcfm_quotes_details', $quote_id ); ?>
	</div>
</div>