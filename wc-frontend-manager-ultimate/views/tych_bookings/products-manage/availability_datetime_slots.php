<!-- Table for adding Date/Day and time table -->
<div class="bkap_duration_date_timeslot_div booking_type_sub_block booking_type_sub_block_booking_enable_duration_time">

	<div>
		<h2><?php _e( 'Set Duration Based Bookings', 'woocommerce-booking' ); ?></h2>
	</div>
	
	<table id="bkap_duration_date_timeslot_table">                    
			
		<tr>
			<td><span class="wcfm_title" style="width:100%;"><strong><?php _e( 'Label:', 'woocommerce-booking' ); ?></strong></span></td>
			<td>
				<input  type="text"
								id="bkap_duration_label"
								name=""
								placeholder="Label for duration"
								class="wcfm-text"
								style="width:90%"
								value="<?php echo sanitize_text_field( $duration_label, true );?>"/>
			</td>
			<td>
				<span class="wcfmfa fa-question img_tip" data-tip="<?php _e( 'Set label for the duration field on the front end', 'woocommerce-booking' );?>"></span>
			</td>                        
		</tr>
		<tr>
			<td><span class="wcfm_title" style="width:100%;"><strong><?php _e( 'Duration', 'woocommerce-booking' ); ?></strong></span></td>
			<td>
				<input type="number" 
								style="width:90px"
								name=""
								id="bkap_duration"
								min="1"
								class="wcfm-text"
								value="<?php echo sanitize_text_field( $duration, true );?>">

				<select id="bkap_duration_type" name= "duration_times[duration_type]" style="max-width:70%;" class="wcfm-select">
				
				<?php                                                                     

				foreach ( $duration_type_array as $key => $value ) {
						$selected_duration = "";

						if ( $duration_type == $key ){
								$selected_duration = "selected";
						}
						?>
						<option value='<?php echo $key; ?>' <?php echo $selected_duration;?> ><?php echo $value; ?></option>
						<?php
				}

				?>
				</select>
			</td>
			<td>
				<span class="wcfmfa fa-question img_tip" data-tip="<?php _e( 'Lengh of the time. Set value to 2 hours/minutes if the duration of your service is 2 hours/minutes. All the 2 hours/minutes durations will be created from mindnight till end of the day.' , 'woocommerce-booking' );?>"></span>
			</td>                        
		</tr>
		<tr>
			<td><span class="wcfm_title" style="width:100%;"><strong><?php _e( 'Minimum duration', 'woocommerce-booking' ); ?></strong></span></td>
			<td>
				<input  type="number"
								style="width:90px" 
								name="duration_times[duration_min]" 
								id="bkap_duration_min" 
								min="1"
								class="wcfm-text"
								value="<?php echo sanitize_text_field( $duration_min, true );?>">
			</td>
			<td>
				<span class="wcfmfa fa-question img_tip" data-tip="<?php _e( 'Minimum duration value a customer can select to book the service.', 'woocommerce-booking' );?>"></span>
			</td>                        
		</tr>
		<tr>
			<td><span class="wcfm_title" style="width:100%;"><strong><?php _e( 'Maximum duration', 'woocommerce-booking' ); ?></strong></span></td>
			<td>
				<input  type="number" 
								style="width:90px" 
								name="duration_times[duration_max]" 
								id="bkap_duration_max" 
								min="1" 
								max="24" 
								class="wcfm-text"
								value="<?php echo sanitize_text_field( $duration_max, true );?>">
			</td>
			<td>
				<span class="wcfmfa fa-question img_tip" data-tip="<?php _e( 'Maximum duration value a customer can select to book the service.', 'woocommerce-booking' );?>"></span>
			</td>                        
		</tr>
		<tr>
			<td><span class="wcfm_title" style="width:100%;"><strong><?php _e( 'Maximum booking', 'woocommerce-booking' ); ?></strong></span></td>
			<td>
				<input  type="number"
								style="width:90px"
								name="duration_times[duration_max_booking]" 
								id="bkap_duration_max_booking" 
								min="0" 
								max="24" 
								class="wcfm-text"
								value="<?php echo sanitize_text_field( $duration_max_booking, true );?>">
			</td>
			<td>
				<span class="wcfmfa fa-question img_tip" data-tip="<?php _e( 'Set this field if you want to place a limit on maximum bookings on the duration. If you can manage up to 15 bookings in a duration, set this value to 15. Once 15 orders have been booked, then that duration will not be available for further bookings.', 'woocommerce-booking' );?>"></span>
			</td>                        
		</tr>
		<tr>
			<td><span class="wcfm_title" style="width:100%;"><strong><?php _e( 'Duration price', 'woocommerce-booking' ); ?></strong></span></td>
			<td>
				<input  type="text"
								id="bkap_duration_price"
								style="width:90px"
								name="duration_times[duration_price]"
								placeholder="Price"
								class="wcfm-text"
								value="<?php echo sanitize_text_field( $duration_price, true );?>"/>
			</td>
			<td>
				<span class="wcfmfa fa-question img_tip" data-tip="<?php _e( 'Price for the duration. ', 'woocommerce-booking' );?>"></span>
			</td>                        
		</tr>
		<tr>
			<td><span class="wcfm_title" style="width:100%;"><strong><?php _e( 'First duration starts at', 'woocommerce-booking' ); ?></strong></span></td>
			<td>
				<input  type="text"
								id="bkap_duration_start"
								style="width:90px"
								name="duration_times[first_duration]"
								placeholder="HH:MM"
								class="wcfm-text"
								value="<?php echo sanitize_text_field( $first_duration, true );?>"/>
			</td>
			<td>
				<span class="wcfmfa fa-question img_tip" data-tip="<?php _e( 'Set this field if you want to start the duration from perticular time. If your day starts at 10:00am then you can set this value to 10:00. All the durations will be created from 10:00am till the value set in the Duration ends at option. If the Duration ends at option is blank then duration end time will be considered till end of the day.', 'woocommerce-booking' );?>"></span>
			</td>                        
		</tr>
		<tr>
			<td><span class="wcfm_title" style="width:100%;"><strong><?php _e( 'Duration end at', 'woocommerce-booking' ); ?></strong></span></td>
			<td>
				<input  type="text"
								id="bkap_duration_end"
								style="width:90px"
								name="duration_times[end_duration]"
								placeholder="HH:MM"
								class="wcfm-text"
								value="<?php echo sanitize_text_field( $end_duration, true );?>"/>
			</td>
			<td>
				<span class="wcfmfa fa-question img_tip" data-tip="<?php _e( 'Set this field if you want to end the duration at perticular time. If you want your customer to choose last duration as 06:00pm then set value to 18:00. All the durations will be created from as per the value set to First duration starts at opton to the value set in Duration ends at field. If this option is set to then duration end time will be considered till end of the day.', 'woocommerce-booking' );?>"></span>
			</td>                        
		</tr>

	</table>
</div>