<div class="specific_date" style="">
	<table class="specific">
		<tr>
			<th style="width:20%"><?php _e( 'Range Type'                , 'woocommerce-booking' );?></th>
			<th style="width:20%"><?php _e( 'From'                      , 'woocommerce-booking' );?></th>
			<th style="width:20%"><?php _e( 'To'                        , 'woocommerce-booking' );?></th>
			<th style="width:10%"><?php _e( 'Bookable'                  , 'woocommerce-booking' );?></th>
			<th style="border-right:0px;width:25%"><?php _e( 'Max bookings/<br>No. of Years' , 'woocommerce-booking' );?></th>
			<th style="border-left:0px;"></th>
		</tr>  
		
		<!-- We are fetching below tr when add new range is clicked -->
		<tr class="added_specific_date_range_row" style="display: none;">
			 <td>
					<select style="width:100%;" id="range_dropdown" class="wcfm-select">
						<?php 
						foreach( $bkap_dates_months_availability as $d_value => $d_name ) {
								printf( "<option value='%s'>%s</option>\n", $d_value, $d_name );
						}?>
					</select>
			 </td>
			 
			 
		 <!-- From Custom-->
		 <td class="date_selection_textbox1" style="width:20%;">
				<div class="fake-input">
					<input type="text" id="datepicker_textbox1" class="datepicker_start_date date_selection_textbox wcfm-text" style="width:100%;" />
				</div>
		 </td>
		 <!-- To Custom-->
		 <td class="date_selection_textbox2">
				<div class="fake-input" >
					<input type="text" id="datepicker_textbox2" class="datepicker_end_date date_selection_textbox wcfm-text" style="width:100%;" />
				</div>
		 </td>
		 
		 <!-- Specific Date Textarea -->
		 <td class="date_selection_textbox3" colspan="2" style="display:none;" >
			 <div class="fake-textarea" >
				 <textarea id="textareamultidate_cal1" class="textareamultidate_cal wcfm-textarea" rows="1" col="30" style="width:100%;height:auto;"></textarea>
			 </div>
		 </td>
		 
		 <!-- From Month-->
		 <td class="date_selection_textbox4" style="display:none;">
				<select id="bkap_availability_from_month" style="width:100%;" class="wcfm-select">
					<?php
					foreach( $bkap_months as $m_number => $m_name ) {
							printf( "<option value='%d'>%s</option>\n", $m_number, $m_name );
					} 
					?>
				</select>
		 </td>
		 <!-- To Month-->
		 <td class="date_selection_textbox5" style="display:none;">
				<select id="bkap_availability_to_month" style="width:100%;" class="wcfm-select">
						<?php
						foreach( $bkap_months as $m_number => $m_name ) {
								printf( "<option value='%d'>%s</option>\n", $m_number, $m_name );
						} 
						?>
				</select>
		 </td>
		 
		 <!-- Holiday Textarea -->
		 <td class="date_selection_textbox6" colspan="2" style="display:none;" >
				 <div class="fake-textarea" >
						 <textarea id="textareamultidate_cal2" class="textareamultidate_cal wcfm-textarea" rows="1" col="30" style="width:100%;height:auto;"></textarea>
				 </div>
		 </td>
 
		 <td style="padding-left:2%;">
				<div class="bkap_popup">
					<span class="bkap_popuptext" id="bkap_myPopup"></span>
					<label class="bkap_switch">
					
						<input id="bkap_bookable_nonbookable" type="checkbox" name="bkap_bookable_nonbookable" class="wcfm-checkbox">
						<div class="bkap_slider round"></div>
					</label>
				<div>
		 </td>
		 
		 <td class="bkap_lockout_column_data_1" >
			<input id="bkap_number_of_year_to_recur_custom_range" title="Please enter number of years you want to recur this custom range" class="wcfm-text" type="number" min="0" style="width:65%;font-size:11px;margin-left: 15%;" placeholder="No. of Years">
			&nbsp;
			<i id="bkap_recurring" class="fa fa-refresh" aria-hidden="true" title="Recurring yearly"></i>
		 </td>
		 <td class="bkap_lockout_column_data_2"  style="display:none;">
					<input id="bkap_number_of_year_to_recur_holiday" title="Please enter number of years you want to recur selected holidays" class="wcfm-text" type="number" min="0" style="width:65%;font-size:11px;margin-left: 15%;" placeholder="No. of Years">
					&nbsp;
					<i id="bkap_recurring" class="fa fa-refresh" aria-hidden="true" title="Recurring yearly"></i>
		 </td>
		 <td class="bkap_lockout_column_data_3"  style="display:none;">
					<input id="bkap_number_of_year_to_recur_month" title="Please enter number of years you want to recur selected month" class="wcfm-text" type="number" min="0" style="width:65%;font-size:11px;margin-left: 15%;" placeholder="No. of Years">
					&nbsp;
			<i id="bkap_recurring" class="fa fa-refresh" aria-hidden="true" title="Recurring yearly"></i>
		 </td>    
		 <td class="bkap_lockout_column_data_4" style="display:none;">
					<input id="bkap_specific_date_lockout" title="This field is for maximum booking for selected specific dates." class="wcfm-text" type="number" min="0" style="width:47%;font-size:11px;" placeholder="Max bookings">
					<input id="bkap_specific_date_price" title="This field is for price of selected specific dates." type="number" class="wcfm-text" min="0" style="width:45%;float:right;font-size:11px;" placeholder="Price">
		 </td>
		 
		 <td id="bkap_close" style="text-align: center;cursor:pointer;"><i class="wcfmfa fa-trash-alt" aria-hidden="true"></i></td>
	 </tr>
	 <!-- We are fetching above tr when add new range is clicked -->
     
	 <?php
		$i = 0;
              
		while ( $i < count( $array_of_all_added_ranges ) ) {
					
				$range_type               = $array_of_all_added_ranges[$i]['bkap_type'];
				$custom_range_disaply     = $holidays_disaply = $range_of_months_disaply = $specific_dates_disaply = "";
				
				$bkap_start               = ( isset( $array_of_all_added_ranges[$i]['bkap_start'] )            && !is_null( $array_of_all_added_ranges[$i]['bkap_start'] ) )            ? $array_of_all_added_ranges[$i]['bkap_start']            : "";
				$bkap_end                 = ( isset( $array_of_all_added_ranges[$i]['bkap_end'] )              && !is_null( $array_of_all_added_ranges[$i]['bkap_end'] ) )              ? $array_of_all_added_ranges[$i]['bkap_end']              : "";
				$bkap_years_to_recur      = ( isset( $array_of_all_added_ranges[$i]['bkap_years_to_recur'] )   && !is_null( $array_of_all_added_ranges[$i]['bkap_years_to_recur'] ) )   ? $array_of_all_added_ranges[$i]['bkap_years_to_recur']   : "";
				$bkap_bookable            = 'checked="checked"';
				$custom_bkap_start        = $custom_bkap_end = $month_bkap_start = $month_bkap_end = $bkap_holiday_date = $custom_bkap_years_to_recur = $holiday_bkap_years_to_recur = $month_bkap_years_to_recur = $bkap_specific_price = $bkap_specific_lockout = $bkap_specific_date = "";
				
				switch ( $range_type ) {
						case "custom_range":
								
								$holidays_disaply             = $range_of_months_disaply = $specific_dates_disaply = "display:none;";
								$custom_bkap_start            = $bkap_start;
								$custom_bkap_end              = $bkap_end;
								$custom_bkap_years_to_recur   = $bkap_years_to_recur;
								if( isset( $array_of_all_added_ranges[$i]['bkap_bookable'] ) && $array_of_all_added_ranges[$i]['bkap_bookable'] == "off" ){
										$bkap_bookable = "";
								}
								
								break;
								
						case "holidays":
								
								$custom_range_disaply = $range_of_months_disaply = $specific_dates_disaply = "display:none;";
								$bkap_holiday_date            = ( isset( $array_of_all_added_ranges[$i]['bkap_holiday_date'] )              && !is_null( $array_of_all_added_ranges[$i]['bkap_holiday_date'] ) )              ? $array_of_all_added_ranges[$i]['bkap_holiday_date']              : "";
								$holiday_bkap_years_to_recur  = $bkap_years_to_recur;
								$bkap_bookable                = "";
								break;
								
						case "range_of_months":
								
								$custom_range_disaply         = $holidays_disaply = $specific_dates_disaply = "display:none;";
								$month_bkap_start             = date( "F", strtotime( $bkap_start ) );
								$month_bkap_end               = date( "F", strtotime( $bkap_end ) );
								$month_bkap_years_to_recur    = $bkap_years_to_recur;                          
								if( isset( $array_of_all_added_ranges[$i]['bkap_bookable'] ) && $array_of_all_added_ranges[$i]['bkap_bookable'] == "off" ){
										$bkap_bookable = "";
								}   
								break;
								
						case "specific_dates":
								
								$custom_range_disaply     = $holidays_disaply = $range_of_months_disaply = "display:none;";
								$bkap_specific_date       = ( isset( $array_of_all_added_ranges[$i]['bkap_specific_date'] )    && !is_null( $array_of_all_added_ranges[$i]['bkap_specific_date'] ) )    ? $array_of_all_added_ranges[$i]['bkap_specific_date']    : "";
								$bkap_specific_lockout    = ( isset( $array_of_all_added_ranges[$i]['bkap_specific_lockout'] ) && !is_null( $array_of_all_added_ranges[$i]['bkap_specific_lockout'] ) ) ? $array_of_all_added_ranges[$i]['bkap_specific_lockout'] : "";
								$bkap_specific_price      = ( isset ( $array_of_all_added_ranges[ $i ][ 'bkap_specific_price' ] ) ) ? $array_of_all_added_ranges[ $i ][ 'bkap_specific_price' ] : '';
								break;
								
						default:
								break;
				}
				
				$bkap_row_toggle = '';
				$bkap_row_toggle_display = '';
				if( $i > 4 ){
					$bkap_row_toggle = "bkap_row_toggle";
					$bkap_row_toggle_display = 'style="display:none;"'; 
				}
				?>
				
				<tr class="added_specific_date_range_row_<?php echo $i;?> <?php echo $bkap_row_toggle;?>" <?php echo $bkap_row_toggle_display;?>>
				
					<td style="width:20%;">
						<select style="width:100%;" id="range_dropdown_<?php echo $i;?>" class="wcfm-select">
							<?php 
								foreach( $bkap_dates_months_availability as $d_value => $d_name ) {
									$bkap_range_selected = '';
									if( $d_value == $range_type ){
											$bkap_range_selected = "selected";
									}
									printf( "<option value='%s' %s>%s</option>\n", $d_value,$bkap_range_selected, $d_name );
								}
							?>
						</select>
					</td>
					
					<td class="date_selection_textbox1" style="width:20%;<?php echo $custom_range_disaply;?>">
						 <div class="fake-input">
							 <input type="text" id="datepicker_textbox_<?php echo $i;?>" class="datepicker_start_date date_selection_textbox" style="width:100%;" value="<?php echo $custom_bkap_start;?>"/>
						 </div>
					</td>
							 
					<td class="date_selection_textbox2" style="width:20%;<?php echo $custom_range_disaply;?>">
						 <div class="fake-input" >
							 <input type="text" id="datepicker_textbox__<?php echo $i;?>" class="datepicker_end_date date_selection_textbox" style="width:100%;" value="<?php echo $custom_bkap_end;?>" />
						 </div>
					</td>
					
					<td class="date_selection_textbox3" colspan="2" style="<?php echo $specific_dates_disaply;?>width:40%;" >
						 <div class="fake-textarea" >
							 <textarea id="specific_dates_multidatepicker_<?php echo $i;?>" class="textareamultidate_cal" rows="1" col="30" style="width:100%;height:auto;"><?php echo $bkap_specific_date;?></textarea>
						 </div>
					</td>
			
					<!-- From Month-->
					<td class="date_selection_textbox4" style="<?php echo $range_of_months_disaply;?>width:20%;">
						 <select id="bkap_availability_from_month_<?php echo $i;?>" style="width:100%;" class="wcfm-select">
							<?php
								foreach ( $bkap_months as $m_number => $m_name ) {
									if ( $m_name == $month_bkap_start ) {
										$month_bkap_start_selected = "selected";
										printf( "<option value='%d' %s>%s</option>\n", $m_number, $month_bkap_start_selected, $m_name );
									} else {
										printf( "<option value='%d'>%s</option>\n", $m_number, $m_name );
									}
								} 
								?>
							</select>
					</td>
					 <!-- To Month-->
					 <td class="date_selection_textbox5" style="<?php echo $range_of_months_disaply;?>width:20%;">
							<select id="bkap_availability_to_month_<?php echo $i;?>" style="width:100%;" class="wcfm-select">
								<?php
									foreach ( $bkap_months as $m_number => $m_name ) {
										if ( $m_name == $month_bkap_end ) {
											$month_bkap_end_selected = "selected";
											printf( "<option value='%d' %s>%s</option>\n", $m_number, $month_bkap_end_selected, $m_name );
										} else {
											printf( "<option value='%d'>%s</option>\n", $m_number, $m_name );
										}
									} 
								?>
							</select>
					 </td>
					 
					 <!-- Holiday Textarea -->
					 <td class="date_selection_textbox6" colspan="2" style="<?php echo $holidays_disaply;?>width:40%" >
							<div class="fake-textarea" >
								<textarea id="holidays_multidatepicker_<?php echo $i;?>" class="textareamultidate_cal wcfm-textarea" rows="1" col="30" style="width:100%;height:auto;" style="overflow:hidden" onkeyup="auto_grow(this)"><?php echo $bkap_holiday_date;?></textarea>
							</div>
					 </td>
					 
					 <td style="padding-left:2%;width:10%;">
							<div class="bkap_popup">
							<span class="bkap_popuptext" id="bkap_myPopup_<?php echo $i;?>"></span>
							<label class="bkap_switch">
								 <input id="bkap_bookable_nonbookable_<?php echo $i;?>" type="checkbox" class="wcfm-checkbox" name="bkap_bookable_nonbookable" <?php echo $bkap_bookable;?>>
								 <div class="bkap_slider round"></div>
							</label>
							</div>
					 </td>
					 
					 <td class="bkap_lockout_column_data_1" style="<?php echo $custom_range_disaply;?>">
							<input id="bkap_number_of_year_to_recur_custom_range_<?php echo $i;?>" class="wcfm-text" value="<?php echo $custom_bkap_years_to_recur;?>" title="Please enter number of years you want to recur this custom range" type="number" min="0" style="width:65%;font-size:11px;margin-left: 15%;" placeholder="No. of Years">

							&nbsp;
							<i id="bkap_recurring" class="fa fa-refresh" aria-hidden="true" title="Recurring yearly"></i>
					 </td>
					 
					 <td class="bkap_lockout_column_data_2"  style="<?php echo $holidays_disaply;?>">
							<input id="bkap_number_of_year_to_recur_holiday_<?php echo $i;?>" class="wcfm-text" value="<?php echo $holiday_bkap_years_to_recur;?>"  title="Please enter number of years you want to recur selected holidays" type="number" min="0" style="width:65%;font-size:11px;margin-left: 15%;" placeholder="No. of Years">
							&nbsp;
							<i id="bkap_recurring" class="fa fa-refresh" aria-hidden="true" title="Recurring yearly"></i>
					 </td>
					 
					 <td class="bkap_lockout_column_data_3"  style="<?php echo $range_of_months_disaply;?>">
							<input id="bkap_number_of_year_to_recur_month_<?php echo $i;?>" class="wcfm-text" value="<?php echo $month_bkap_years_to_recur;?>" title="Please enter number of years you want to recur selected month" type="number" min="0" style="width:65%;font-size:11px;margin-left:15%;" placeholder="No. of Years">
							&nbsp;
							<i id="bkap_recurring" class="fa fa-refresh" aria-hidden="true" title="Recurring yearly"></i>
					 </td>    
					 
					 <td class="bkap_lockout_column_data_4" style="<?php echo $specific_dates_disaply;?>">
							<input id="bkap_specific_date_lockout_<?php echo $i;?>" class="wcfm-text" value="<?php echo $bkap_specific_lockout;?>" title="This is number of maximum bookings for selected specific dates." type="number" min="0"style="width:47%;font-size:11px;" placeholder="Max bookings">
							<input id="bkap_specific_date_price_<?php echo $i;?>" class="wcfm-text" value="<?php echo $bkap_specific_price; ?>" title="This is price for selected specific dates." type="number" min="0" style="width:45%;float:right;font-size:11px;" placeholder="Price">
					 </td>
					 
					 <td style="width:4%;" id="bkap_close_<?php echo $i;?>" style="text-align: center;cursor:pointer;"><i class="wcfmfa fa-trash-alt" aria-hidden="true"></i></td>
						 
				</tr>
				<?php 
				$i++;
			}
		?>
		
		<tr style="padding:5px; border-top:2px solid #eee">
		  <td colspan="4" style="border-right: 0px;"><i><small><?php _e( 'Create custom ranges, holidays and more here.', 'woocommerce-booking' ); ?></small><i></td>
		  <td colspan="2" align="right" style="border-left: none;"><button type="button" class="button-primary bkap_add_new_range wcfm_submit_button"><?php _e( 'Add New Range' , 'woocommerce-booking' );?></button></td>
		</tr>
	</table>
</div>