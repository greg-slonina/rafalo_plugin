<!-- Table for Price by range of days -->
<div class="bkap_price_range_booking bkap_enable_block_pricing_type_block bkap_enable_block_pricing_type_block_booking_block_price_enable">
		 
	<div>
		 <h2><?php _e( 'Price by range of nights :', 'woocommerce-booking' ); ?></h2>
	</div>
	
	<table id="bkap_price_range_booking_table" >
	
		<tr>
			<?php 
			if ( $product_attributes != '' && $product_type == "variable" ) {
				foreach ( $product_attributes as $k => $v ) {
					$attribute_name = wc_attribute_label( $v["name"] );
					?>
					<th <?php echo $width; ?>><?php _e( $attribute_name, 'woocommerce-booking' ); ?></th>
				<?php
				}
			}
			?>
			<th <?php echo $width; ?>><?php _e( 'Minimum Day', 'woocommerce-booking' );?></th>
			<th <?php echo $width; ?>><?php _e( 'Maximum Day', 'woocommerce-booking' );?></th>
			<th <?php echo $width; ?>><?php _e( "Per Day ($currency_symbol)", 'woocommerce-booking' );?></th>
			<th <?php echo $width; ?>><?php _e( "Fixed ($currency_symbol)", 'woocommerce-booking' );?></th>
			
			<th width="4%" id="bkap_price_range_all_close" class="bkap_remove_all_price_ranges" style="text-align: center;cursor:pointer;"><i class="fa fa-trash" aria-hidden="true"></i></th>
		</tr>
		
		<tr id="bkap_default_price_range_row" style="display: none;">
			<?php 
			$i                     = 1;
			$j                     = 1;
			if( $product_attributes != '' && $product_type == "variable" ) {
				foreach( $product_attributes as $key => $value ) {
					if ( $value['is_taxonomy'] ) {
						$value_array = wc_get_product_terms( $product_id, $value['name'], array( 'fields' => 'names' ) );
					} else{
						$value_array  =  explode( ' | ', $value['value'] );
					}
						
					print( '<td><select class="wcfm-select" name="attribute_'.$i.'" id="attribute_'.$i.'" value="" style="width:100%">' );
	
					$j            =  1;
	
					foreach( $value_array as $k => $v ) {
						$attr_value  = trim( $v );
						print( '<option name="option_attribute_'.$i.'_'.$j.'" id="option_attribute_'.$i.'_'.$j.'" value="'.htmlspecialchars( $attr_value ).'">'.$v.'</option>' );
					}
					
					print( '</select></td>' );
					$i++;
					$j++;			           
				}
			}
			?>
			<td>
					<input type="number" id="number_of_start_days" name="number_of_start_days" class="wcfm-text" min=0 style="width:100%"></input>
			</td>
			<td>
					<input type="number" id="number_of_end_days" name="number_of_end_days" class="wcfm-text" min=0 style="width:100%"></input>
			</td>
			<td >
					<input type="text" id="per_day_price" name="per_day_price" class="wcfm-text" style="width:100%"></input>
			</td>
			<td>
					<input type="text" id="fixed_price" name="fixed_price" class="wcfm-text" style="width:100%"></input>
			</td>
			
			<td width="4%" id="bkap_price_range_close" class="" style="text-align: center;cursor:pointer;"><i class="wcfmfa fa-trash-alt" aria-hidden="true"></i></td>
		</tr>
	
		<?php
		$block_count = count( $price_range_booking_data );
		$row_number  = 0;
		$max_key     = 0;
		
		if( $block_count != 0 ){
			$max_key     = max( array_keys( $price_range_booking_data ) );
		}
		 
		if( $product_type == 'variable' ) {
			if( $product_attributes != "" )
				$product_attributes_keys = array_keys( $product_attributes );
		}
		
		$c = 0;
		while( $row_number <= $max_key ) {
				
				if( !in_array( $row_number, array_keys( $price_range_booking_data ) ) ){
					$row_number++;
					continue;
				}
				
				$i = 0;
				$number_of_columns = count( $price_range_booking_data[$row_number] );
				$min_number = $max_number = $per_day_price = $fixed_price = "";
				 
				$min_number          =   $price_range_booking_data[$row_number]['min_number'];
				$max_number          =   $price_range_booking_data[$row_number]['max_number'];
				$per_day_price       =   $price_range_booking_data[$row_number]['per_day_price'];
				$fixed_price         =   $price_range_booking_data[$row_number]['fixed_price'];
				
				$bkap_row_toggle         = '';
				$bkap_row_toggle_display = '';
				 
				if( $c > 4 ){
					$bkap_row_toggle = "bkap_price_range_row_toggle";
					$bkap_row_toggle_display = 'style="display:none;"';
				}
		?>
		<tr id="bkap_price_range_row_<?php echo $row_number; ?>" class="<?php echo $bkap_row_toggle; ?>" <?php echo $bkap_row_toggle_display; ?>>
			<?php 
			if( $product_attributes != '' && ( $product_type == 'variable' ) ) {
					
				foreach( $product_attributes as $key => $value ) {
					 
					if ( $value['is_taxonomy'] ) {
						$value_array = wc_get_product_terms( $product_id, $value['name'], array( 'fields' => 'names' ) );
					} else{
						$value_array  =  explode( ' | ', $value['value'] );
					}
						
					print( '<td><select class="wcfm-select" name="attribute_'.$i.'_'.$row_number.'" id="attribute_'.$i.'_'.$row_number.'" value="" style="width:100%">' );
	
					$j            =  1;
	
					foreach( $value_array as $k => $v ) {
						$attr_value      = trim( $v );
						$attribute_key   = $product_attributes_keys[$i];
						$selected        = "";
					
						if( $attr_value == $price_range_booking_data[$row_number][$attribute_key] ){
							$selected = "selected";  
						}
						print( '<option name="option_attribute_'.$i.'_'.$j.'" id="option_attribute_'.$i.'_'.$j.'" value="'.htmlspecialchars( $attr_value ).'" '.$selected.'>'.$v.'</option>' );
					}
					
					print( '</select></td>' );
					$i++;
					$j++;			           
				}
			}
			?>
			<td>
				<input type="number" id="number_of_start_days_<?php echo $row_number; ?>" class="wcfm-text" name="number_of_start_days" min=0 style="width:100%" value="<?php echo $min_number; ?>"></input>
			</td>
			<td>
				<input type="number" id="number_of_end_days_<?php echo $row_number; ?>" class="wcfm-text" name="number_of_end_days" min=0 style="width:100%" value="<?php echo $max_number; ?>"></input>
			</td>
			<td >
				<input type="text" id="per_day_price_<?php echo $row_number; ?>" class="wcfm-text" name="per_day_price" style="width:100%" value="<?php echo $per_day_price; ?>"></input>
			</td>
			<td>
				<input type="text" id="fixed_price_<?php echo $row_number; ?>" class="wcfm-text" name="fixed_price" style="width:100%" value="<?php echo $fixed_price; ?>"></input>
			</td>
			
			<td width="4%" id="bkap_price_range_close_<?php echo $row_number; ?>" style="text-align: center;cursor:pointer;"><i class="wcfmfa fa-trash-alt" aria-hidden="true"></i></td>
		</tr>
		
		<?php 
			$row_number++;
			$c++;
		}
		?>
	
		<tr style="padding:5px; border-top:2px solid #eee">
			 <td colspan="<?php echo ($attribute_count+2); ?>" style="border-right: 0px;">
				 <i>
					 <small><?php _e( 'Create block ranges and its per day and/or fixed price.', 'woocommerce-booking' ); ?></small>
				 <i>
			 </td>
			 <td colspan="2" align="right" style="border-left: none;">
			 <button type="button" class="button-primary bkap_add_new_price_range wcfm_submit_button"><i class="wcfmfa fa-plus" aria-hidden="true"></i> <?php _e( 'Add New Range' , 'woocommerce-booking' );?></button></td>
		</tr>
	
	</table>
</div>