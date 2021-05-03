<!-- Table for adding Date/Day and time table -->
<div class="bkap_date_timeslot_div booking_type_sub_block booking_type_sub_block_booking_enable_fixed_time">

	<?php do_action( 'bkap_before_time_enabled', $product_id, $booking_settings );?>
	
	<div>
		<h2><?php _e( 'Set Weekdays/Dates And It\'s Timeslots :', 'woocommerce-booking' ); ?></h2>
	</div>
	
	<?php do_action( 'bkap_after_time_enabled', $product_id, $booking_settings );?>
	
	<table id="bkap_date_timeslot_table">
		<tr>
			<th width="20%"><?php _e( 'Weekday', 'woocommerce-booking' );?></th>
			<th width="10%"><?php _e( 'From', 'woocommerce-booking' );?></th>
			<th width="10%"><?php _e( 'To', 'woocommerce-booking' );?></th>
			<th width="10%"><?php _e( 'Maximum Bookings', 'woocommerce-booking' );?></th>
			<th width="10%"><?php _e( 'Price', 'woocommerce-booking' );?></th>
			<th width="10%"><?php _e( 'Global', 'woocommerce-booking' );?></th>
			<th width="23%"><?php _e( 'Note', 'woocommerce-booking' );?></th>
			<th width="4%"></th>
		</tr>
		
		<tr id="bkap_default_date_time_row" style="display: none;">
			<td width="20%" id="select_td">
				<select id="bkap_dateday_selector" multiple="multiple" class="wcfm-select">
					 <?php 
						foreach( $bkap_weekdays as $w_value => $w_name ) {
							if ( isset( $recurring_weekdays[ $w_value ] ) && 'on' == $recurring_weekdays[ $w_value ] ) {
								printf( "<option value='%s'>%s</option>\n", $w_value, $w_name );
							}
						}
						foreach( $specific_dates as $dates => $lockout ) {
							printf( "<option value='%s'>%s</option>\n", $dates, $dates );
						}    
						?>
					 <option name="all" value="all"><?php _e( 'All', 'woocommerce-booking' );?></option>
				</select>
			</td>
			<td width="10%"><input id="bkap_from_time" type="text" class="wcfm-text" name="quantity" style="width:100%;" title="Please enter time in 24 hour format e.g 14:00 or 03:00" placeholder="HH:MM" maxlength="5" onkeypress="return bkap_isNumberKey(event)"></td>
			<td width="10%"><input id="bkap_to_time" type="text" class="wcfm-text" name="quantity" style="width:100%;" title="Please enter time in 24 hour format e.g 14:00 or 03:00" placeholder="HH:MM" maxlength="5" onkeypress="return bkap_isNumberKey(event)"></td>
			<td width="10%"><input id="bkap_lockout_time" type="number" class="wcfm-text" name="quantity" style="width:100%;" min="0" placeholder="Max bookings"></td>
			<td width="10%"><input id="bkap_price_time"type="text" class="wcfm-text" name="quantity" style="width:100%;" placeholder="Price"></td>
			<td width="10%" style="text-align:center;">
				<label class="bkap_switch">
					 <input id="bkap_global_time" type="checkbox" name="bkap_global_timeslot" class="wcfm-checkbox" style="margin-left: 35%;">
					 <div class="bkap_slider round"></div>
				</label>
			</td>
			<td width="23%"><textarea id="bkap_note_time" rows="1" cols="2" style="width:100%;" class="wcfm-textarea"></textarea></td>
			<td width="4%" id="bkap_close" style="text-align: center;cursor:pointer;"><i class="wcfmfa fa-trash-alt" aria-hidden="true"></i></td>
		</tr>
		
		
		<?php
		if ( $bkap_total_time_slots_number > 0 ) {
			if( $number == 0 ){
			
			} else {
				$number = 1 ;
				/**
				 * This tr is a identifier, when we recive the response from ajax we will remove this tr and replace 
				 * our genrated data.
				 */
				?>
				<tr class="bkap_replace_response_data">
				</tr>
				<?php
				$bkap_end_record_on     = 50;
        $bkap_start_record_from = 1 ;
				foreach( $booking_settings['booking_time_settings'] as $bkap_weekday_key => $bkap_weekday_value ) {
		
					//usort( $bkap_weekday_value, 'bkap_sort_by_from_time');
					
					foreach ( $bkap_weekday_value as $day_key => $time_data  ) {
					 
							$bkap_from_hr      = ( isset( $time_data['from_slot_hrs'] ) && !is_null( $time_data['from_slot_hrs'] ) ) ? $time_data['from_slot_hrs'] : "";
							$bkap_from_min     = ( isset( $time_data['from_slot_min'] ) && !is_null( $time_data['from_slot_min'] ) ) ? $time_data['from_slot_min'] : "";
							$bkap_from_time    = $bkap_from_hr.":".$bkap_from_min;
							 
							$bkap_to_hr        = ( isset( $time_data['to_slot_hrs'] ) && !is_null( $time_data['to_slot_hrs'] ) ) ? $time_data['to_slot_hrs'] : "";
							$bkap_to_min       = ( isset( $time_data['to_slot_min'] ) && !is_null( $time_data['to_slot_min'] ) ) ? $time_data['to_slot_min'] : "";
							$bkap_to_time      = ( $bkap_to_hr === '0' && $bkap_to_min === '00' ) ? '' : "$bkap_to_hr:$bkap_to_min";
							 
							$bkap_lockout      = ( isset( $time_data['lockout_slot'] ) && !is_null( $time_data['lockout_slot'] ) ) ? $time_data['lockout_slot'] : "";
							$bkap_price        = ( isset( $time_data['slot_price'] ) && !is_null( $time_data['slot_price'] ) ) ? $time_data['slot_price'] : "";
							 
							$bkap_global       = ( isset( $time_data['global_time_check'] ) && !is_null( $time_data['global_time_check'] ) ) ? $time_data['global_time_check'] : "";
							$bkap_note         = ( isset( $time_data['booking_notes'] ) && !is_null( $time_data['booking_notes'] ) ) ? $time_data['booking_notes'] : "";
							
							$bkap_global_checked            = "";
							$bkap_time_row_toggle           = "";
							$bkap_time_row_toggle_display   = "";
							
							if( $bkap_global == 'on' ){
									$bkap_global_checked = "checked";
							}                        
							
						if ( $number >= $bkap_start_record_from && $number <= $bkap_end_record_on ) { 
						?>
						
						<tr id="bkap_date_time_row_<?php echo $number;?>">
							<td width="20%">
								<select id="bkap_dateday_selector_<?php echo $number;?>" class="bkap_dateday_selector" multiple="multiple" disabled class="wcfm-select">
										<?php
										// Recurring Weekdays 
										foreach( $bkap_weekdays as $w_value => $w_name ) {
											$bkap_selected = "";
											
											if( $w_value == $bkap_weekday_key ){
												$bkap_selected = "selected";
												printf( "<option value='%s' %s>%s</option>\n", $w_value, $bkap_selected, $w_name );
											} else if ( isset( $recurring_weekdays[ $w_value ] ) && 'on' == $recurring_weekdays[ $w_value ] ) {
												// add the option value only if the weekday is enabled
												printf( "<option value='%s' %s>%s</option>\n", $w_value, $bkap_selected, $w_name );
											}
										}
										// Specific Dates
										foreach( $specific_dates as $dates => $lockout ) {
											$bkap_selected = '';
													
											if ( trim( $dates ) == trim( $bkap_weekday_key ) ) {
													$bkap_selected = 'selected';
											}
											printf( "<option value='%s' %s>%s</option>\n", $dates, $bkap_selected, $dates );
										
										}?>
								
									 <option name="all" value="all"><?php _e( 'All', 'woocommerce-booking' );?></option>
								</select>
							</td>
							<td width="10%"><input id="bkap_from_time_<?php echo $number;?>" type="text" class="wcfm-text" name="quantity" style="width:100%;" title="Please enter time in 24 hour format e.g 14:00 or 03:00" placeholder="HH:MM" minlength="5" maxlength="5" onkeypress="return bkap_isNumberKey(event)" value="<?php echo $bkap_from_time;?>" readonly></td>
							<td width="10%"><input id="bkap_to_time_<?php echo $number;?>" type="text" class="wcfm-text" name="quantity" style="width:100%;" title="Please enter time in 24 hour format e.g 14:00 or 03:00" placeholder="HH:MM" minlength="5" maxlength="5" onkeypress="return bkap_isNumberKey(event)" value="<?php echo $bkap_to_time;?>" readonly></td>
							<td width="10%"><input id="bkap_lockout_time_<?php echo $number;?>" type="number" class="wcfm-text" name="quantity" style="width:100%;" min="0" placeholder="Max bookings" value="<?php echo $bkap_lockout;?>" class = "bkap_default" ></td>
							<td width="10%"><input id="bkap_price_time_<?php echo $number;?>" type="text" class="wcfm-text" name="quantity" style="width:100%;" placeholder="Price" value="<?php echo $bkap_price;?>" class = "bkap_default"></td>
							<td width="10%" style="text-align:center;">
								<label class="bkap_switch">
									<input id="bkap_global_time_<?php echo $number;?>" type="checkbox" class="wcfm-checkbox" name="bkap_global_timeslot" style="margin-left: 35%;" <?php echo $bkap_global_checked;?> class = "bkap_default">
									<div class="bkap_slider round"></div>
								</label>
							</td>
							<td width="23%"><textarea id="bkap_note_time_<?php echo $number;?>" rows="1" cols="2" style="width:100%;" class = "bkap_default" ><?php echo $bkap_note;?></textarea></td>
							<td width="4%" id="bkap_close_<?php echo $number;?>" style="text-align: center;cursor:pointer;"><i class="wcfmfa fa-trash-alt" aria-hidden="true"></i></td>
						</tr>
						
						<?php
						}
						$number++;
					}
				}
			}
		}
		?>

		<tr style="padding:5px; border-top:2px solid #eee">
			 <td colspan="5" style="border-right: 0px;">
			 <i>
					 <small><?php _e( 'Create timeslots for the days/dates selected above.', 'woocommerce-booking' ); ?>
					 <br><?php _e( 'Enter time in 24 hours format e.g. 14:00.', 'woocommerce-booking' );?>
					 <br><?php _e( 'Leave "To time" unchanged if you do not wish to create a fixed time duration slot.', 'woocommerce-booking' );?>
					 </small>
			 <i></td>
			 <td colspan="3" align="right" style="border-left: none;"><button type="button" class="button-primary bkap_add_new_date_time_range wcfm_submit_button"><?php _e( 'Add New Timeslot' , 'woocommerce-booking' );?></button></td>
		</tr>
	</table>
</div>