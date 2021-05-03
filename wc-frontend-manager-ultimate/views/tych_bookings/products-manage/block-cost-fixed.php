<!-- Table for Fixed Block Booking -->
<div class="bkap_fixed_block_booking bkap_enable_block_pricing_type_block bkap_enable_block_pricing_type_block_booking_fixed_block_enable">

	<div>
		<h2><?php _e( 'Fixed Blocks Booking :', 'woocommerce-booking' ); ?></h2>
	</div>

	<table id="bkap_fixed_block_booking_table" >
	
	  <tr>
			<th width="25%"><?php _e( 'Block Name', 'woocommerce-booking' );?></th>
			<th width="10%"><?php _e( 'Days', 'woocommerce-booking' );?></th>
			<th width="20%"><?php _e( 'Start Day', 'woocommerce-booking' );?></th>
			<th width="20%"><?php _e( 'End Day', 'woocommerce-booking' );?></th>
			<th width="20"><?php _e( "Price (".$currency_symbol.")", 'woocommerce-booking' );?></th>
			<td width="4%" id="bkap_fixed_block_all_close" class="bkap_remove_all_fixed_blocks" style="text-align: center;cursor:pointer;"><i class="fa fa-trash" aria-hidden="true"></i></th>
		</tr>
		
		<tr id="bkap_default_fixed_block_row" style="display: none;">
			<td width="25%">
				<input type="text" id="booking_block_name" class="wcfm-text" name="booking_block_name" style="width:100%" placeholder="Enter Name of Block"></input>
			</td>
			<td width="10%">
				<input type="number" id="number_of_days" class="wcfm-text" name="number_of_days" min=0 style="width:100%"></input>
			</td>
			<td width="20%">
				<select id="start_day" name="start_day" class="wcfm-select" style="width:100%">
					<?php 
					$days = $bkap_fixed_days;
					foreach ( $days as $dkey => $dvalue ) {
					?>
					<option value="<?php echo $dkey; ?>"><?php echo $dvalue; ?></option>
					<?php 
					}
					?>
				</select>
			</td>
			<td width="20%">
				<select id="end_day" name="end_day" class="wcfm-select" style="width:100%">
					<?php 
					foreach ( $days as $dkey => $dvalue ) {
						?>
						<option value="<?php echo $dkey; ?>"><?php echo $dvalue; ?></option>
						<?php 
					}
					?>
				</select>
		  </td>
			<td width="20%"><input type="text" class="wcfm-text" id="fixed_block_price" name="fixed_block_price" style="width:100%" placeholder="Block Price"></input></td>
			
			<td width="4%" id="bkap_fixed_block_close" class="" style="text-align: center;cursor:pointer;"><i class="wcfmfa fa-trash-alt" aria-hidden="true"></i></td>
		</tr>
		
		<?php
		  $result      = get_post_meta( $product_id, '_bkap_fixed_blocks_data', true );
			$results     = ( isset( $result ) && $result != "" ) ? $result : array();
			$block_count = count( $results );
			$max_key     = 0;
			
			if( $block_count != 0 ){
				$max_key     = max( array_keys( $results ) );
			}
			 
			$row_number = 0;
			$i = 0;
			
			while( $row_number <= $max_key ) {
					
				if( !in_array( $row_number, array_keys( $results ) ) ){
					$row_number++;
					continue;
				}
				
				$block_name = $number_of_days = $start_day = $end_day = $price = "";
				
				$block_name      =   $results[$row_number]['block_name'];
				$number_of_days  =   $results[$row_number]['number_of_days'];
				$start_day       =   $results[$row_number]['start_day'];
				$end_day         =   $results[$row_number]['end_day'];
				$price           =   $results[$row_number]['price'];
				
				
				$bkap_row_toggle         = '';
				$bkap_row_toggle_display = '';
				
				if( $i > 4 ){
					$bkap_row_toggle = "bkap_fixed_row_toggle";
					$bkap_row_toggle_display = 'style="display:none;"';
				}
					
			?>
					
			<tr id="bkap_fixed_block_row_<?php echo $row_number; ?>" class="<?php echo $bkap_row_toggle;?>" <?php echo $bkap_row_toggle_display;?>>
				<td width="25%">
					<input type="text" id="booking_block_name_<?php echo $row_number; ?>" name="booking_block_name" class="wcfm-text" style="width:100%" placeholder="Enter Name of Block" value="<?php echo $block_name;?>"></input>
				</td>
				<td width="10%">
					<input type="number" id="number_of_days_<?php echo $row_number; ?>" name="number_of_days" class="wcfm-text" min=0 style="width:100%" value="<?php echo $number_of_days;?>"></input>
				</td>
				<td width="20%">
							
				  <select id="start_day_<?php echo $row_number; ?>" name="start_day" class="wcfm-select" style="width:100%">
					<?php 
					$days = $bkap_fixed_days;
					foreach ( $days as $dkey => $dvalue ) {
							$start_selected = "";
							//echo gettype( $dkey ) . ' - ' . gettype( $start_day );
							if( (string)$dkey == (string)$start_day ) {
									$start_selected = "selected";
							}
							?><option value="<?php echo $dkey; ?>" <?php echo $start_selected; ?>><?php echo $dvalue; ?></option> <?php 
					}
					?>
					</select>
				</td>
				<td width="20%">
					<select id="end_day_<?php echo $row_number; ?>" name="end_day" class="wcfm-select" style="width:100%">
						<?php 
						foreach ( $days as $dkey => $dvalue ) {
								$end_selected = "";
								if( (string)$dkey == (string)$end_day ){
									$end_selected = "selected";
								
								}
								?><option value="<?php echo $dkey; ?>" <?php echo $end_selected; ?>><?php echo $dvalue; ?></option> <?php
						}
						?>
					</select>
				</td>
				<td width="20%"><input type="text" id="fixed_block_price_<?php echo $row_number; ?>" name="fixed_block_price" class="wcfm-text" style="width:100%" placeholder="Block Price" value="<?php echo $price; ?>"></input></td>
				
				<td width="4%" id="bkap_fixed_block_close_<?php echo $row_number; ?>" class="" style="text-align: center;cursor:pointer;"><i class="wcfmfa fa-trash-alt" aria-hidden="true"></i></td>
			</tr>
		
			<?php
			$row_number++;
			$i++;
		}
		?>
		
		<tr style="padding:5px; border-top:2px solid #eee">
		  <td colspan="3" style="border-right: 0px;">
				 <i>
					 <small><?php _e( 'Create fixed blocks of booking and its price.', 'woocommerce-booking' ); ?></small>
				 <i>
		  </td>
		  <td colspan="3" align="right" style="border-left: none;">
		  <button type="button" class="button-primary bkap_add_new_fixed_block wcfm_submit_button"><i class="wcfmfa fa-plus" aria-hidden="true"></i> <?php _e( 'Add New Block' , 'woocommerce-booking' );?></button></td>
		</tr>
	</table>

</div>