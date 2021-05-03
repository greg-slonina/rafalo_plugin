<?php
/**
 * WCFM plugin core
 *
 * Custom Field Support Controller
 *
 * @author 		WC Lovers
 * @package 	wcfm/core
 * @version   3.5.5
 */
 
class WCFMu_Custom_Field_Support {

	public function __construct() {
		global $WCFM;
		
		// Custom fields visibility options
		add_filter( 'wcfm_product_custom_visibility_options', array( &$this, 'wcfm_product_custom_visibility_options' ) );
		
    // Product custom fields display
    add_action( 'woocommerce_single_product_summary',	array( &$this, 'wcfm_custom_field_display_after_title' ),  7 );
    add_action( 'woocommerce_single_product_summary',	array( &$this, 'wcfm_custom_field_display_after_price' ),  12 );
    add_action( 'woocommerce_single_product_summary',	array( &$this, 'wcfm_custom_field_display_with_summery' ),  25 );
    add_action( 'woocommerce_product_meta_start',	array( &$this, 'wcfm_custom_field_display_before_meta' ),  25 );
    add_action( 'woocommerce_product_meta_end',	array( &$this, 'wcfm_custom_field_display_with_meta' ),  25 );
    add_action( 'the_content',	array( &$this, 'wcfm_custom_field_display_with_description' ),  25 );
    
    // WCFM Product Custom Field Display Short code
		add_shortcode( 'wcfm_product_custom_field_show', array( &$this, 'wcfm_product_custom_field_show_shortcode' ) );
    
  }
  
  /**
   * Custom fileds visibility options
   */
  function wcfm_product_custom_visibility_options( $visibility_options ) {
  	$more_visibility_options = array( 'after_title' => __( 'After product title', 'wc-frontend-manager-ultimate' ), 'after_price' => __( 'After product price', 'wc-frontend-manager-ultimate' ), 'with_summery' => __( 'With product summery', 'wc-frontend-manager-ultimate' ), 'before_meta' => __( 'Before product meta', 'wc-frontend-manager-ultimate' ), 'with_meta' => __( 'After product meta', 'wc-frontend-manager-ultimate' ), 'with_desctiption' => __( 'With product description', 'wc-frontend-manager-ultimate' ) );
  	//, 'new_tab' => __( 'As new tab', 'wc-frontend-manager-ultimate' )
  	$visibility_options = array_merge( $visibility_options, $more_visibility_options );
  	return $visibility_options;
  }
  
	/**
	 * product custom field display
	 */
	function get_wcfm_custom_field_display_data( $product_id, $wcfm_product_custom_field ) {
		global $WCFM, $product, $post;
		
		$display_data = '';
		$block_name = !empty( $wcfm_product_custom_field['block_name'] ) ? $wcfm_product_custom_field['block_name'] : '';
		if( !$block_name ) return '';
		$exclude_product_types = isset( $wcfm_product_custom_field['exclude_product_types'] ) ? $wcfm_product_custom_field['exclude_product_types'] : array();
		$is_group = !empty( $wcfm_product_custom_field['is_group'] ) ? 'yes' : 'no';
		$is_group = !empty( $wcfm_product_custom_field['group_name'] ) ? $is_group : 'no';
		$group_name = !empty( $wcfm_product_custom_field['group_name'] ) ? $wcfm_product_custom_field['group_name'] : '';
		$group_value = array();
		if( $product_id && $is_group && $group_name ) {
			$group_value = (array) get_post_meta( $product_id, $group_name, true );		
			$group_value = apply_filters( 'wcfm_custom_field_group_data_value', $group_value, $group_name );
		}
		
		$product = wc_get_product( $product_id );
		$product_type = $product->get_type();
		
		$is_virtual = ( get_post_meta( $product_id, '_virtual', true) == 'yes' ) ? 'enable' : '';
		if( $is_virtual && in_array( 'virtual', $exclude_product_types ) ) return '';
		
		$wcfm_product_custom_block_fields = $wcfm_product_custom_field['wcfm_product_custom_block_fields'];
		if( !empty( $wcfm_product_custom_block_fields ) && !in_array( $product_type, $exclude_product_types ) ) {
			
			$vendor_id = wcfm_get_vendor_id_by_post( $product_id );
			if( $vendor_id ) {
				if( !$WCFM->wcfm_vendor_support->wcfm_vendor_allowed_element_capability( $vendor_id, 'allowed_custom_fields', sanitize_title($block_name) ) ) return;
			}
			
			$display_data .= '<div class="wcfm_custom_field_display wcfm_custom_field_display_'.sanitize_title($block_name).'">';
			if( $block_name && apply_filters( 'wcfm_is_allow_custom_field_block_name_display', true ) ) {
				$display_data .= "<h4 class='wcfm_custom_field_display_heading'>" . wcfm_removeslashes( __( $block_name, 'wc-frontend-manager' ) ) . "</h4>";
			}
			if( apply_filters( 'wcfm_is_allow_custom_field_display_as_table', false ) ) { $display_data .= "<table class='wcfm_custom_field_display_table'><tr>"; }
			foreach( $wcfm_product_custom_block_fields as $wcfm_product_custom_block_field ) {
				if( !$wcfm_product_custom_block_field['name'] ) continue;
				$field_value = '';
				$field_name = $wcfm_product_custom_block_field['name'];
				if( $is_group == 'yes' ) {
					$field_name = $group_name . '[' . $wcfm_product_custom_block_field['name'] . ']';
					if( $product_id ) {
						if( $wcfm_product_custom_block_field['type'] == 'checkbox' ) {
							$field_value = isset( $group_value[$wcfm_product_custom_block_field['name']] ) ? 'yes' : 'no';
						} elseif( $wcfm_product_custom_block_field['type'] == 'upload' ) {
							if( isset( $group_value[$wcfm_product_custom_block_field['name']] ) ) {
								$field_value = '<a class="wcfm_linked_images" href="' . wcfm_get_attachment_url( $group_value[$wcfm_product_custom_block_field['name']] ) . '" target="_blank">' . wcfm_removeslashes( __( $wcfm_product_custom_block_field['label'], 'wc-frontend-manager') ) . '</a>';
							}
						} else {
							if( isset( $group_value[$wcfm_product_custom_block_field['name']] )) {
								$field_value = $group_value[$wcfm_product_custom_block_field['name']];
							}
						}
					}
				} else {
					if( $product_id ) {
						if( $wcfm_product_custom_block_field['type'] == 'checkbox' ) {
							$field_value = get_post_meta( $product_id, $field_name, true ) ? get_post_meta( $product_id, $field_name, true ) : 'no';
						} elseif( $wcfm_product_custom_block_field['type'] == 'upload' ) {
							if( get_post_meta( $product_id, $field_name, true ) ) {
								$field_value = '<a class="wcfm_linked_images" href="' . wcfm_get_attachment_url( get_post_meta( $product_id, $field_name, true ) ) . '" target="_blank">' . wcfm_removeslashes( __( $wcfm_product_custom_block_field['label'], 'wc-frontend-manager') ) . '</a>';
							}
						} else {
							$field_value = get_post_meta( $product_id, $field_name, true );
						}
					}
				}
				
				$field_value =  apply_filters( 'wcfm_custom_field_value', $field_value, $field_name, $product_id, $wcfm_product_custom_block_field['type'], $wcfm_product_custom_block_field );
				
				if( ( $wcfm_product_custom_block_field['type'] == 'checkbox' ) && apply_filters( 'wcfm_is_allow_custom_field_display_by_icon', true ) ) {
					if( $field_value == 'no' ) {
						$field_value = '<span class="wcfmfa fa-times-circle" style="color:#f86c6b"></span>';
					} else {
						$field_value = '<span class="wcfmfa fa-check-circle" style="color:#20c997"></span>';
					}
				}
				
				if( $wcfm_product_custom_block_field['type'] == 'textarea' ) {
					$field_value = wcfm_stripe_newline( $field_value );
				}
				
				if( !$field_value ) continue;
				
				if( is_array( $field_value ) ) $field_value = implode( ', ', $field_value );
				
				if( apply_filters( 'wcfm_is_allow_custom_field_display_as_table', false ) ) { $display_data .= "<td>"; }
				if( ( $wcfm_product_custom_block_field['type'] != 'upload' ) && apply_filters( 'wcfm_is_allow_custom_field_label_display', true ) ) {
				  $display_data .= "<label class='wcfm_custom_field_display_label'>" . wcfm_removeslashes( __( $wcfm_product_custom_block_field['label'], 'wc-frontend-manager') ) . ": </label>";
				}
				if( apply_filters( 'wcfm_is_allow_custom_field_display_as_table', false ) ) { $display_data .= "<br />"; }
				$display_data .= "<span class='wcfm_custom_field_display_value'>" . $field_value . "</span><br />";
				if( apply_filters( 'wcfm_is_allow_custom_field_display_as_table', false ) ) { $display_data .= "</td>"; }
			}
			if( apply_filters( 'wcfm_is_allow_custom_field_display_as_table', false ) ) { $display_data .= "</tr></table>"; }
			$display_data .= '</div><div class="wcfm-clearfix"></div><br />';
		}
		return $display_data;
	}
	
	/**
	 * product custom field display after title
	 */
	function wcfm_custom_field_display_after_title() {
		global $WCFM, $product, $post;
	
		$product_id = 0;
		if ( is_object( $product ) && method_exists( $product, 'get_id' ) ) { 
			$product_id   		= $product->get_id(); 
		} else if ( is_product() ) {
			$product_id   		= $post->ID;
		}
			
		if( $product_id ) {
			$wcfm_product_custom_fields = get_option( 'wcfm_product_custom_fields', array() );
			if( $wcfm_product_custom_fields && is_array( $wcfm_product_custom_fields ) && !empty( $wcfm_product_custom_fields ) ) {
				foreach( $wcfm_product_custom_fields as $wpcf_index => $wcfm_product_custom_field ) {
					if( !isset( $wcfm_product_custom_field['enable'] ) ) continue;
					
					$visibility = isset( $wcfm_product_custom_field['visibility'] ) ? $wcfm_product_custom_field['visibility'] : '';
					if( !$visibility ) continue;
					if( $visibility != 'after_title' ) continue;
					
					$display_data = $this->get_wcfm_custom_field_display_data( $product_id, $wcfm_product_custom_field );
					echo $display_data;
				}
			}
		}
	}
	
	/**
	 * product custom field display after price
	 */
	function wcfm_custom_field_display_after_price() {
		global $WCFM, $product, $post;
	
		$product_id = 0;
		if ( is_object( $product ) && method_exists( $product, 'get_id' ) ) { 
			$product_id   		= $product->get_id(); 
		} else if ( is_product() ) {
			$product_id   		= $post->ID;
		}
			
		if( $product_id ) {
			$wcfm_product_custom_fields = (array) get_option( 'wcfm_product_custom_fields' );
			if( $wcfm_product_custom_fields && is_array( $wcfm_product_custom_fields ) && !empty( $wcfm_product_custom_fields ) ) {
				foreach( $wcfm_product_custom_fields as $wpcf_index => $wcfm_product_custom_field ) {
					if( !isset( $wcfm_product_custom_field['enable'] ) ) continue;
					
					$visibility = isset( $wcfm_product_custom_field['visibility'] ) ? $wcfm_product_custom_field['visibility'] : '';
					if( !$visibility ) continue;
					if( $visibility != 'after_price' ) continue;
					
					$display_data = $this->get_wcfm_custom_field_display_data( $product_id, $wcfm_product_custom_field );
					echo $display_data;
				}
			}
		}
	}
	
	/**
	 * product custom field display with summery
	 */
	function wcfm_custom_field_display_with_summery() {
		global $WCFM, $product, $post;
	
		$product_id = 0;
		if ( is_object( $product ) && method_exists( $product, 'get_id' ) ) { 
			$product_id   		= $product->get_id(); 
		} else if ( is_product() ) {
			$product_id   		= $post->ID;
		}
			
		if( $product_id ) {
			$wcfm_product_custom_fields = (array) get_option( 'wcfm_product_custom_fields' );
			if( $wcfm_product_custom_fields && is_array( $wcfm_product_custom_fields ) && !empty( $wcfm_product_custom_fields ) ) {
				foreach( $wcfm_product_custom_fields as $wpcf_index => $wcfm_product_custom_field ) {
					if( !isset( $wcfm_product_custom_field['enable'] ) ) continue;
					
					$visibility = isset( $wcfm_product_custom_field['visibility'] ) ? $wcfm_product_custom_field['visibility'] : '';
					if( !$visibility ) continue;
					if( $visibility != 'with_summery' ) continue;
					
					$display_data = $this->get_wcfm_custom_field_display_data( $product_id, $wcfm_product_custom_field );
					echo $display_data;
				}
			}
		}
	}
	
	/**
	 * product custom field display Before Meta
	 */
	function wcfm_custom_field_display_before_meta() {
		global $WCFM, $product, $post;
	
		$product_id = 0;
		if ( is_object( $product ) && method_exists( $product, 'get_id' ) ) { 
			$product_id   		= $product->get_id(); 
		} else if ( is_product() ) {
			$product_id   		= $post->ID;
		}
			
		if( $product_id ) {
			$wcfm_product_custom_fields = (array) get_option( 'wcfm_product_custom_fields' );
			if( $wcfm_product_custom_fields && is_array( $wcfm_product_custom_fields ) && !empty( $wcfm_product_custom_fields ) ) {
				foreach( $wcfm_product_custom_fields as $wpcf_index => $wcfm_product_custom_field ) {
					if( !isset( $wcfm_product_custom_field['enable'] ) ) continue;
					
					$visibility = isset( $wcfm_product_custom_field['visibility'] ) ? $wcfm_product_custom_field['visibility'] : '';
					if( !$visibility ) continue;
					if( $visibility != 'before_meta' ) continue;
					
					$display_data = $this->get_wcfm_custom_field_display_data( $product_id, $wcfm_product_custom_field );
					echo $display_data;
				}
			}
		}
	}
	
	/**
	 * product custom field display After Meta
	 */
	function wcfm_custom_field_display_with_meta() {
		global $WCFM, $product, $post;
	
		$product_id = 0;
		if ( is_object( $product ) && method_exists( $product, 'get_id' ) ) { 
			$product_id   		= $product->get_id(); 
		} else if ( is_product() ) {
			$product_id   		= $post->ID;
		}
			
		if( $product_id ) {
			$wcfm_product_custom_fields = (array) get_option( 'wcfm_product_custom_fields' );
			if( $wcfm_product_custom_fields && is_array( $wcfm_product_custom_fields ) && !empty( $wcfm_product_custom_fields ) ) {
				foreach( $wcfm_product_custom_fields as $wpcf_index => $wcfm_product_custom_field ) {
					if( !isset( $wcfm_product_custom_field['enable'] ) ) continue;
					
					$visibility = isset( $wcfm_product_custom_field['visibility'] ) ? $wcfm_product_custom_field['visibility'] : '';
					if( !$visibility ) continue;
					if( $visibility != 'with_meta' ) continue;
					
					$display_data = $this->get_wcfm_custom_field_display_data( $product_id, $wcfm_product_custom_field );
					echo $display_data;
				}
			}
		}
	}
	
	/**
	 * product custom field display with description
	 */
	function wcfm_custom_field_display_with_description( $description ) {
		global $WCFM, $product, $post;
	
		$product_id = 0;
		if ( is_object( $product ) && method_exists( $product, 'get_id' ) ) { 
			$product_id   		= $product->get_id(); 
		} else if ( is_product() ) {
			$product_id   		= $post->ID;
		}
			
		if( $product_id ) {
			$wcfm_product_custom_fields = (array) get_option( 'wcfm_product_custom_fields' );
			if( $wcfm_product_custom_fields && is_array( $wcfm_product_custom_fields ) && !empty( $wcfm_product_custom_fields ) ) {
				foreach( $wcfm_product_custom_fields as $wpcf_index => $wcfm_product_custom_field ) {
					if( !isset( $wcfm_product_custom_field['enable'] ) ) continue;
					
					$visibility = isset( $wcfm_product_custom_field['visibility'] ) ? $wcfm_product_custom_field['visibility'] : '';
					if( !$visibility ) continue;
					if( $visibility != 'with_desctiption' ) continue;
					
					$display_data = $this->get_wcfm_custom_field_display_data( $product_id, $wcfm_product_custom_field );
					$description .= $display_data;
				}
			}
		}
		
		return $description;
	}
	
	/**
	 * Product Custom Field Show using Short Code
	 */
	function wcfm_product_custom_field_show_shortcode( $atts ) {
		global $WCFM, $product, $post;
		
		if( !is_product() ) return;
		
		$product_id = 0;
		if ( is_object( $product ) && method_exists( $product, 'get_id' ) ) { 
			$product_id   		= $product->get_id(); 
		} else if ( is_product() ) {
			$product_id   		= $post->ID;
		}
		
		ob_start();
			
		if( $product_id ) {
			$wcfm_product_custom_fields = (array) get_option( 'wcfm_product_custom_fields' );
			if( $wcfm_product_custom_fields && is_array( $wcfm_product_custom_fields ) && !empty( $wcfm_product_custom_fields ) ) {
				foreach( $wcfm_product_custom_fields as $wpcf_index => $wcfm_product_custom_field ) {
					if( !isset( $wcfm_product_custom_field['enable'] ) ) continue;
					
					$visibility = isset( $wcfm_product_custom_field['visibility'] ) ? $wcfm_product_custom_field['visibility'] : '';
					if( !$visibility ) continue;
					
					$display_data = $this->get_wcfm_custom_field_display_data( $product_id, $wcfm_product_custom_field );
					echo $display_data;
				}
			}
		}
		
		$custom_field_show = ob_get_clean();
		
		return $custom_field_show;
	}
}