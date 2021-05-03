<div id="set_weekdays" class="weekdays_flex_main" style="width:100%;" >
	<div class="weekdays_flex_child" >
		<p class="weekdays_flex_child_1 bkap_weekdays_heading wcfm_title" style="max-width:27%;font-style:normal;"><strong><?php _e( 'Weekday', 'woocommerce-booking' );?></strong></p>
		<p class="weekdays_flex_child_2 bkap_weekdays_heading wcfm_title" style="max-width:20%;font-style:normal;"><strong><?php _e( 'Bookable', 'woocommerce-booking' ); ?></strong></p>
		<p class="weekdays_flex_child_3 bkap_weekdays_heading wcfm_title booking_type_sub_block booking_type_sub_block_booking_enable_single_day" style="max-width:26%;font-style:normal;"><strong><?php _e( 'Maximum bookings', 'woocommerce-booking' );?></strong></p>
		<p class="weekdays_flex_child_4 bkap_weekdays_heading wcfm_title" style="max-width:25%;font-style:normal;text-align:center;float:right;"><strong><?php _e( "Price ($currency_symbol)", 'woocommerce-booking' );?> </strong></p>
	</div>
				
	<?php 
	$i = 0;
	foreach( $bkap_weekdays as $w_key => $w_value ) {
	?>
		<div class="weekdays_flex_child">
			<p class="weekdays_flex_child_1 wcfm_title" style="padding-top:5px; min-width:27%; width: auto; float:left;"><strong><?php echo $w_value; ?></strong></p>
			
			<?php 
			$weekday_status = 'checked';
			$fields_status = '';
			if ( isset( $recurring_weekdays[ $w_key ] ) && empty( $recurring_weekdays[ $w_key ] ) ) {
				$weekday_status = '';
				$fields_status = 'disabled="disabled"';
			}   
			
			if ( !$lockout ) {
				$fields_status = 'disabled="disabled"';
			}
			
			?>
			<div class="weekdays_flex_child_2" style="padding-top:5px; min-width:20%; float:left; text-align:center;">
				<label class="bkap_switch">
					<input id="<?php echo $w_key;?>" type="checkbox" class="wcfm-checkbox" name="<?php echo $w_value; ?>" value="on" <?php echo $weekday_status; ?> />
					<div class="bkap_slider round"></div> 
				</label>  
			</div>
			
			<?php
			$weekday_lockout = isset( $recurring_lockout[ $w_key ] ) ? $recurring_lockout[ $w_key ] : '';
			?>
			<div class="weekdays_flex_child_3 booking_type_sub_block booking_type_sub_block_booking_enable_single_day" style="padding-top:5px; min-width:26%;text-align:center;"> <input style="float:left;width:130px;" type="number" class="wcfm-text" id="weekday_lockout_<?php echo $i;?>" name="weekday_lockout_<?php echo $i;?>" min="0" max="9999" placeholder="Max bookings" value="<?php echo $weekday_lockout; ?>" <?php echo $fields_status; ?>/></div>
			
			
			<?php 
			$special_price = '';
			if ( is_array( $special_prices ) && count( $special_prices ) > 0 && array_key_exists( $w_key, $special_prices ) ) {
				$special_price = $special_prices[ $w_key ];
			}        
			?>
			<div class="weekdays_flex_child_4" style="min-width:25%;text-align:center;float:right;"> <input style="width:110px;" type="text" class="wcfm-text" id="weekday_price_<?php echo $i;?>" name="weekday_price_<?php echo $i;?>" min="0" placeholder="Special Price" value="<?php echo $special_price;?>"/> </div>
			<div class="wcfm_clearfix"></div>
		</div>    
				
		<?php 
		$i++;
	}
	?>
	<div class="wcfm-clearfix"></div>
</div>