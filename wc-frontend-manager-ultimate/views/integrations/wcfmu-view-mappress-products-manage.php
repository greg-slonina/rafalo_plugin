<?php
/**
 * WCFM plugin views
 *
 * Plugin MapPress Products Manage Views
 *
 * @author 		WC Lovers
 * @package 	wcfmu/views/thirdparty
 * @version   3.0.0
 */
 
global $wp, $WCFM, $WCFMu, $mappress;

if( !$wcfm_allow_mappress = apply_filters( 'wcfm_is_allow_mappress', true ) ) {
	return;
}

$product_id = 0;
if( isset( $wp->query_vars['wcfm-products-manage'] ) && !empty( $wp->query_vars['wcfm-products-manage'] ) ) {
	$product_id = $wp->query_vars['wcfm-products-manage'];
	if( $product_id ) {
		
	}
}

Mappress::load('editor');
$map = new Mappress_Map(array('editable' => true, 'layout' => 'left', 'poiList' => true));

?>

<div class="page_collapsible products_manage_mappress simple variable external grouped booking" id="wcfm_products_manage_form_mappress_head"><label class="wcfmfa fa-map-marker"></label><?php _e( 'Address', 'wc-frontend-manager-ultimate' ); ?><span></span></div>
<div class="wcfm-container simple variable external grouped booking">
	<div id="wcfm_products_manage_form_mappress_expander" class="wcfm-content">
	  <?php
	  if( !$product_id ) {
	  	echo "<h2>";
		  _e( 'Save the product, then you will have option to associate Map with this.', 'wc-frontend-manager-ultimate' );
		  echo "</h2>";
	  } else {
	  	?>
	  	<div class='mapp-m-panel'>
	  	  <h2><?php _e('Maps for this Product', 'wc-frontend-manager-ultimate')?></h2>
	  	  <div class="wcfm_clearfix"></div>
				<div id='mapp_m_list_panel' style='display:none'>
					<input class='wcfm_submit_button' type='button' id='mapp_m_add_map' value='<?php esc_attr_e('New Map', 'mappress-google-maps-for-wordpress')?>' />
					<div id='mapp_m_maplist'>
						<?php Mappress_Map::get_list($product_id); ?>
					</div>
				</div>
			
				<div id='mapp_m_edit_panel' style='display:none'>
					<table class='mapp-settings'>
						<tr>
							<td><?php _e('Map ID', 'mappress-google-maps-for-wordpress');?>:</td>
							<td><span id='mapp_m_mapid'></span></td>
						</tr>
		
						<tr>
							<td><?php _e('Map Title', 'mappress-google-maps-for-wordpress');?>:</td>
							<td><input id='mapp_m_title' class="wcfm-text" type='text' size='40' /></td>
						</tr>
		
						<tr>
							<td><?php _e('Size', 'mappress-google-maps-for-wordpress');?>:</td>
							<td>
								<?php
									$sizes = array();
									foreach(Mappress::$options->sizes as $i => $size)
										$sizes[] = "<a href='#' class='mapp-m-size' data-width='{$size['width']}' data-height='{$size['height']}'>" . $size['width'] . 'x' . $size['height'] . "</a>";
									echo implode(' | ', $sizes);
								?>
								<input type='text' id='mapp_m_width' class="wcfm-text" style="width: 50px;" size='2' value='' /> x <input type='text' id='mapp_m_height' class="wcfm-text" style="width: 50px;" size='2' value='' />
							</td>
						</tr>
					</table>
					<div>
						<input class='wcfm_submit_button' type='button' id='mapp_m_save' value='<?php esc_attr_e('Save', 'mappress-google-maps-for-wordpress'); ?>' />
						<input class='wcfm_submit_button' type='button' id='mapp_m_cancel' value='<?php esc_attr_e('Cancel', 'mappress-google-maps-for-wordpress'); ?>' />
					</div>
					<div class="wcfm_clearfix"></div>
					<hr/>
					<div id='mapp_m_editor'>
						<?php require Mappress::$basedir . "/forms/map_editor.php"; ?>
					</div>
				</div>
			</div>
			<script>
				jQuery(document).ready(function() {
					mappl10n.options.postid = <?php echo $product_id; ?>;
					jQuery('#mapp_m_add_map').click(function() {
						jQuery('.wcfm-tabWrap').css( 'height', '775px' );
					});
					setTimeout(function() { 
						wcfmMapInsert();
					}, 5000 );
					
					jQuery( document ).ajaxComplete(function() {
						 wcfmMapInsert();
					});
					function wcfmMapInsert() {
						jQuery('.mapp-maplist-insert').off('click').removeClass('mapp-maplist-insert').addClass('wcfm-mapp-maplist-insert');
						if( jQuery('#description').hasClass('rich_editor') ) {
							jQuery('.wcfm-mapp-maplist-insert').off('click').on('click', function(e) {
								e.preventDefault();
								tinymce.get('description').insertContent( '[mappress mapid="' + jQuery(this).parents('tr').data('mapid') + '"]' );
							});
						} else {
							jQuery('.wcfm-mapp-maplist-insert').remove();
						}
						jQuery('.mapp-maplist-edit').click(function() {
							jQuery('.wcfm-tabWrap').css( 'height', '775px' );
						});
					}
				});
			</script>
		<?php
		}
		?>
		<div class="wcfm_clearfix"></div>
	</div>
</div>