<?php
	$bkap_intervals = array();

	$bkap_intervals['months'] = array(
		'1'  => __( 'January', 'woocommerce-booking' ),
		'2'  => __( 'February', 'woocommerce-booking' ),
		'3'  => __( 'March', 'woocommerce-booking' ),
		'4'  => __( 'April', 'woocommerce-booking' ),
		'5'  => __( 'May', 'woocommerce-booking' ),
		'6'  => __( 'June', 'woocommerce-booking' ),
		'7'  => __( 'July', 'woocommerce-booking' ),
		'8'  => __( 'August', 'woocommerce-booking' ),
		'9'  => __( 'September', 'woocommerce-booking' ),
		'10' => __( 'October', 'woocommerce-booking' ),
		'11' => __( 'November', 'woocommerce-booking' ),
		'12' => __( 'December', 'woocommerce-booking' )
	);

	$bkap_intervals['days'] = array(
		'1' => __( 'Monday', 'woocommerce-booking' ),
		'2' => __( 'Tuesday', 'woocommerce-booking' ),
		'3' => __( 'Wednesday', 'woocommerce-booking' ),
		'4' => __( 'Thursday', 'woocommerce-booking' ),
		'5' => __( 'Friday', 'woocommerce-booking' ),
		'6' => __( 'Saturday', 'woocommerce-booking' ),
		'7' => __( 'Sunday', 'woocommerce-booking' )
	);

	for ( $i = 1; $i <= 53; $i ++ ) {
		$bkap_intervals['weeks'][ $i ] = sprintf( __( 'Week %s', 'woocommerce-booking' ), $i );
	}

	if ( ! isset( $availability['type'] ) ) {
		$availability['type'] = 'custom';
	}

	if ( ! isset( $availability['priority'] ) ) {
		$availability['priority'] = 10;
	}
?>
<tr>
	<td>
		<div class="select wc_booking_availability_type">
			<select name="wc_booking_availability_type[]" class="wcfm-select" style="width:100%;">
				<option value="custom" <?php selected( $availability['type'], 'custom' ); ?>><?php _e( 'Date range', 'woocommerce-booking' ); ?></option>
				<option value="months" <?php selected( $availability['type'], 'months' ); ?>><?php _e( 'Range of months', 'woocommerce-booking' ); ?></option>
				<option value="weeks" <?php selected( $availability['type'], 'weeks' ); ?>><?php _e( 'Range of weeks', 'woocommerce-booking' ); ?></option>
				<option value="days" <?php selected( $availability['type'], 'days' ); ?>><?php _e( 'Range of days', 'woocommerce-booking' ); ?></option>
				
			</select>
		</div>
	</td>
	<td style="border-right:0;">
	<div class="bookings-datetime-select-from">
		<div class="select from_day_of_week">
			<select name="wc_booking_availability_from_day_of_week[]" class="wcfm-select" style="width:100%;">
				<?php foreach ( $bkap_intervals['days'] as $key => $label ) : ?>
					<option value="<?php echo $key; ?>" <?php selected( isset( $availability['from'] ) && $availability['from'] == $key, true ) ?>><?php echo $label; ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="select from_month">
			<select name="wc_booking_availability_from_month[]" class="wcfm-select" style="width:100%;">
				<?php foreach ( $bkap_intervals['months'] as $key => $label ) : ?>
					<option value="<?php echo $key; ?>" <?php selected( isset( $availability['from'] ) && $availability['from'] == $key, true ) ?>><?php echo $label; ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="select from_week">
			<select name="wc_booking_availability_from_week[]" class="wcfm-select" style="width:100%;">
				<?php foreach ( $bkap_intervals['weeks'] as $key => $label ) : ?>
					<option value="<?php echo $key; ?>" <?php selected( isset( $availability['from'] ) && $availability['from'] == $key, true ) ?>><?php echo $label; ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="from_date fake-input">
			<?php
			$from_date = '';
			if ( 'custom' === $availability['type'] && ! empty( $availability['from'] ) ) {
				$from_date = $availability['from'];
			} else if ( 'time:range' === $availability['type'] && ! empty( $availability['from_date'] ) ) {
				$from_date = $availability['from_date'];
			}
			?>
			<input type="text" class="date-picker wcfm-text" style="width:100%;" name="wc_booking_availability_from_date[]" value="<?php echo esc_attr( $from_date ); ?>" />
		</div>
		<div class="from_time">
			<input type="time" class="time-picker wcfm-text" style="width:100%;" name="wc_booking_availability_from_time[]" value="<?php if ( strrpos( $availability['type'], 'time' ) === 0 && ! empty( $availability['from'] ) ) echo $availability['from'] ?>" placeholder="HH:MM" />
		</div>
	</div>
	</td>
	<td style="border-right:0;" class="bookings-to-label-row">
		<p><?php _e( 'to', 'woocommerce-booking' ); ?></p>
		<p class="bookings-datetimerange-second-label"><?php _e( 'to', 'woocommerce-booking' ); ?></p>
	</td>
	<td>
	<div class='bookings-datetime-select-to'>
		<div class="select to_day_of_week">
			<select name="wc_booking_availability_to_day_of_week[]" class="wcfm-select" style="width:100%;">
				<?php foreach ( $bkap_intervals['days'] as $key => $label ) : ?>
					<option value="<?php echo $key; ?>" <?php selected( isset( $availability['to'] ) && $availability['to'] == $key, true ) ?>><?php echo $label; ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="select to_month">
			<select name="wc_booking_availability_to_month[]" class="wcfm-select" style="width:100%;">
				<?php foreach ( $bkap_intervals['months'] as $key => $label ) : ?>
					<option value="<?php echo $key; ?>" <?php selected( isset( $availability['to'] ) && $availability['to'] == $key, true ) ?>><?php echo $label; ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="select to_week">
			<select name="wc_booking_availability_to_week[]" class="wcfm-select" style="width:100%;">
				<?php foreach ( $bkap_intervals['weeks'] as $key => $label ) : ?>
					<option value="<?php echo $key; ?>" <?php selected( isset( $availability['to'] ) && $availability['to'] == $key, true ) ?>><?php echo $label; ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="to_date fake-input">
			<?php
			$to_date = '';
			if ( 'custom' === $availability['type'] && ! empty( $availability['to'] ) ) {
				$to_date = $availability['to'];
			} else if ( 'time:range' === $availability['type'] && ! empty( $availability['to_date'] ) ) {
				$to_date = $availability['to_date'];
			}
			?>
			<input type="text" class="date-picker wcfm-text" style="width:100%;" name="wc_booking_availability_to_date[]" value="<?php echo esc_attr( $to_date ); ?>" />
		</div>

		<div class="to_time">
			<input type="time" class="time-picker wcfm-text" style="width:100%;" name="wc_booking_availability_to_time[]" value="<?php if ( strrpos( $availability['type'], 'time' ) === 0 && ! empty( $availability['to'] ) ) echo $availability['to']; ?>" placeholder="HH:MM" />
		</div>
	</div>
	</td>
	<td>
		<label class="bkap_switch">
			<input type="checkbox" class="bkap_checkbox wcfm-checkbox" name="wc_booking_availability_bookable[]" value="1" <?php checked( ( isset( $availability['bookable'] ) ? $availability['bookable'] : 0 ), true ); ?> />

            <div class="bkap_slider round"></div>
	 		<?php 
			 	if( isset( $availability['bookable'] ) && $availability['bookable'] == "1" ){
			 		$test = 1;
			 	}else{
			 		$test = 0;
			 	}

			?>
	        <input type="hidden" class="bkap_hidden_checkbox" name="wc_booking_availability_bookable_hidden[]" value="<?php echo $test; ?>" />
        </label>

	</td>
	<td>
	<div class="priority">
		<input type="number" class="wcfm-text" style="width:100%;" name="wc_booking_availability_priority[]" value="<?php echo esc_attr( $availability['priority'] ); ?>" placeholder="10" />
	</div>
	</td>
	<td id="bkap_close_resource" style="text-align: center;cursor:pointer;"><i class="wcfmfa fa-trash" aria-hidden="true"></i></td>
</tr>
