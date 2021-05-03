<?php
/**
 * WCFM plugin controllers
 *
 * Plugin Dokan Reviews Dashboard Controller
 *
 * @author 		WC Lovers
 * @package 	wcfm/reviews/dokan/controllers
 * @version   4.0.2
 */

class WCFM_Reviews_Controller {
	
	public function __construct() {
		global $WCFM;
		
		$this->processing();
	}
	
	public function processing() {
		global $WCFM, $wpdb, $_POST, $WCFMu;
		
		$length = $_POST['length'];
		$offset = $_POST['start'];
		
    $status_filter = '1';
    if( isset($_POST['status_type']) && ( $_POST['status_type'] != '' ) ) {
    	$status_filter = $_POST['status_type'];
    	if( $status_filter == 'approved' ) {
    		$status_filter = '1';
    	} elseif( $status_filter == 'pending' ) {
    		$status_filter = '0';
    	}
    }
    
    $vendor_id   = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );
    
		$wcfm_review_items = $wpdb->get_var( "SELECT COUNT( c.comment_ID )
																				FROM $wpdb->comments as c, $wpdb->posts as p
																				WHERE p.post_author='$vendor_id' AND
																						p.post_status='publish' AND
																						c.comment_post_ID=p.ID AND
																						c.comment_approved='$status_filter' AND
																						p.post_type='product'" 
																				);
		if( !$wcfm_review_items ) $wcfm_review_items = 0;
		
		$wcfm_reviews_array = $wpdb->get_results(
																	"SELECT c.comment_content, c.comment_ID, c.comment_author,
																			c.comment_author_email, c.comment_author_url,
																			p.post_title, c.user_id, c.comment_post_ID, c.comment_approved,
																			c.comment_date
																	FROM $wpdb->comments as c, $wpdb->posts as p
																	WHERE p.post_author='$vendor_id' AND
																			p.post_status='publish' AND
																			c.comment_post_ID=p.ID AND
																			c.comment_approved='$status_filter' AND
																			p.post_type='product' ORDER BY c.comment_ID DESC
																	LIMIT $offset,$length"
															);
		
		// Generate Reviews JSON
		$wcfm_reviews_json = '';
		$wcfm_reviews_json = '{
															"draw": ' . $_POST['draw'] . ',
															"recordsTotal": ' . $wcfm_review_items . ',
															"recordsFiltered": ' . $wcfm_review_items . ',
															"data": ';
		if(!empty($wcfm_reviews_array)) {
			$index = 0;
			$wcfm_reviews_json_arr = array();
			foreach($wcfm_reviews_array as $comment) {
				
				$comment_date       = get_comment_date( 'Y/m/d \a\t g:i a', $comment->comment_ID );
        $comment_author_img = get_avatar( $comment->comment_author_email, 32 );
        $eidt_post_url      = get_edit_post_link( $comment->comment_post_ID );
        $permalink          = get_comment_link( $comment );
        $comment_status     =  $comment->comment_approved;
				
        // Author
        $wcfm_reviews_json_arr[$index][] =  '<div class="dokan-author-img">' . $comment_author_img. '</div><div class="dokan-author-meta">' . $comment->comment_author . '<br />' . $comment->comment_author_email . '</div>';
        
        // Comment
        $wcfm_reviews_json_arr[$index][] =  '<div class="dokan-comments-subdate">' . __( 'Submitted on ', 'dokan' ) . $comment_date . '</div><br /><div class="dokan-comments-content">' . $comment->comment_content . '</div>';
        
        // Rating
        if ( get_option( 'woocommerce_enable_review_rating' ) == 'yes' ) {
        	$rating =  intval( get_comment_meta( $comment->comment_ID, 'rating', true ) );
        	$wcfm_reviews_json_arr[$index][] =  '<div class="dokan-rating"><div style="margin: auto;" itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating" class="star-rating" title="' . sprintf( __( 'Rated %d out of 5', 'dokan' ), $rating ) . '"><span style="width:' . ( ( $rating / 5 ) * 100 ) . '%"><strong itemprop="ratingValue">' . $rating . '</strong> ' . __( 'out of 5', 'dokan' ). '</span></div></div>';
        } else {
        	$wcfm_reviews_json_arr[$index][] =  '&ndash;';
        }
        
				// Status
				$actions = '';
				if( $comment_status == '1' ) {
					$actions .= '<a class="wcfm-action-icon" target="_blank" href="' . $permalink . '"><span class="wcfmfa fa-eye text_tip" data-tip="' . esc_attr__( 'View Comment', 'dokan' ) . '"></span></a>';
					if ( dokan_get_option( 'seller_review_manage', 'dokan_general', 'on' ) == 'on' ) {
						$actions .= '<a class="wcfm_review_status_update wcfm-action-icon" href="#" data-status="0" data-reviewid="' . $comment->comment_ID . '"><span class="wcfmfa fa-times-circle-o text_tip" data-tip="' . esc_attr__( 'Unapprove', 'dokan' ) . '"></span></a>';
						$actions .= '<a class="wcfm_review_status_update wcfm-action-icon" href="#" data-status="spam" data-reviewid="' . $comment->comment_ID . '"><span class="wcfmfa fa-dot-circle-o text_tip" data-tip="' . esc_attr__( 'Spam', 'dokan' ) . '"></span></a>';
						$actions .= '<a class="wcfm_review_status_update wcfm-action-icon" href="#" data-status="trash" data-reviewid="' . $comment->comment_ID . '"><span class="wcfmfa fa-trash-alt text_tip" data-tip="' . esc_attr__( 'Trash', 'dokan' ) . '"></span></a>';
					}
				} elseif( $comment_status == '0' ) {
					if ( dokan_get_option( 'seller_review_manage', 'dokan_general', 'on' ) == 'on' ) {
						$actions .= '<a class="wcfm_review_status_update wcfm-action-icon" href="#" data-status="1" data-reviewid="' . $comment->comment_ID . '"><span class="wcfmfa fa-check-circle text_tip" data-tip="' . esc_attr__( 'Approve', 'dokan' ) . '"></span></a>';
						$actions .= '<a class="wcfm_review_status_update wcfm-action-icon" href="#" data-status="spam" data-reviewid="' . $comment->comment_ID . '"><span class="wcfmfa fa-dot-circle-o text_tip" data-tip="' . esc_attr__( 'Spam', 'dokan' ) . '"></span></a>';
						$actions .= '<a class="wcfm_review_status_update wcfm-action-icon" href="#" data-status="trash" data-reviewid="' . $comment->comment_ID . '"><span class="wcfmfa fa-trash-alt text_tip" data-tip="' . esc_attr__( 'Trash', 'dokan' ) . '"></span></a>';
					}
				} elseif( $comment_status == 'spam' ) {
					if ( dokan_get_option( 'seller_review_manage', 'dokan_general', 'on' ) == 'on' ) {
						$actions .= '<a class="wcfm_review_status_update wcfm-action-icon" href="#" data-status="1" data-reviewid="' . $comment->comment_ID . '"><span class="wcfmfa fa-check-circle text_tip" data-tip="' . esc_attr__( 'Not Spam', 'dokan' ) . '"></span></a>';
						$actions .= '<a class="wcfm_review_status_update wcfm-action-icon" href="#" data-status="delete" data-reviewid="' . $comment->comment_ID . '"><span class="wcfmfa fa-trash-alt text_tip" data-tip="' . esc_attr__( 'Delete Permanently', 'dokan' ) . '"></span></a>';
					}
				} elseif( $comment_status == 'trash' ) {
					if ( dokan_get_option( 'seller_review_manage', 'dokan_general', 'on' ) == 'on' ) {
						$actions .= '<a class="wcfm_review_status_update wcfm-action-icon" href="#" data-status="1" data-reviewid="' . $comment->comment_ID . '"><span class="wcfmfa fa-check-circle text_tip" data-tip="' . esc_attr__( 'Restore', 'dokan' ) . '"></span></a>';
						$actions .= '<a class="wcfm_review_status_update wcfm-action-icon" href="#" data-status="delete" data-reviewid="' . $comment->comment_ID . '"><span class="wcfmfa fa-trash-alt text_tip" data-tip="' . esc_attr__( 'Delete Permanently', 'dokan' ) . '"></span></a>';
					}
				}
				$wcfm_reviews_json_arr[$index][] =  $actions;
				
				$index++;
			}												
		}
		if( !empty($wcfm_reviews_json_arr) ) $wcfm_reviews_json .= json_encode($wcfm_reviews_json_arr);
		else $wcfm_reviews_json .= '[]';
		$wcfm_reviews_json .= '
													}';
													
		echo $wcfm_reviews_json;
	}
}