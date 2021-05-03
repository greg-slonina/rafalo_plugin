<div class="wrap woocommerce">
	<form method="get" id="mainform" enctype="multipart/form-data" class="wc_appointments_calendar_form month_view">
		<input type="hidden" name="calendar_month" value="<?php echo absint( $month ); ?>" />
		<input type="hidden" name="view" value="<?php echo esc_attr( $view ); ?>" />
		<input type="hidden" name="tab" value="calendar" />
		<div class="tablenav">
			<div class="filters">
				<select id="calendar-appointments-filter" name="filter_appointable_product" class="wcfm-select" style="width:150px">
					<option value=""><?php _e( 'All Products', 'woocommerce-appointments' ); ?></option>
					<?php if ( $product_filters = $this->product_filters() ) : ?>
						<?php foreach ( $product_filters as $filter_id => $filter_name ) : ?>
							<option value="<?php echo $filter_id; ?>" <?php selected( $product_filter, $filter_id ); ?>><?php echo $filter_name; ?></option>
						<?php endforeach; ?>
					<?php endif; ?>
				</select>
				
				<select id="calendar-appointments-filter" name="filter_appointable_staff" class="wcfm-select" style="width:150px">
				  <option value=""><?php _e( 'All Staff', 'woocommerce-appointments' ); ?></option>
					<?php if ( $staff_filters = $this->staff_filters() ) : ?>
						<?php foreach ( $staff_filters as $filter_id => $filter_name ) : ?>
							<option value="<?php echo $filter_id; ?>" <?php selected( $staff_filter, $filter_id ); ?>><?php echo $filter_name; ?></option>
						<?php endforeach; ?>
					<?php endif; ?>
				</select>
			</div>
			<div class="date_selector">
				<a class="prev" href="<?php echo esc_url( add_query_arg( array( 'calendar_year' => $year, 'calendar_month' => $month - 1 ) ) ); ?>"></a>
				<div class="month_view">
					<select name="calendar_month" class="wcfm-select">
						<?php for ( $i = 1; $i <= 12; $i ++ ) : ?>
							<option value="<?php echo $i; ?>" <?php selected( $month, $i ); ?>><?php echo ucfirst( date_i18n( 'M', strtotime( '2013-' . $i . '-01' ) ) ); ?></option>
						<?php endfor; ?>
					</select>
				</div>
				<div class="month_view">
					<select name="calendar_year" class="wcfm-select">
						<?php $current_year = date( 'Y' ); ?>
						<?php for ( $i = ( $current_year - 1 ); $i <= ( $current_year + 5 ); $i ++ ) : ?>
							<option value="<?php echo $i; ?>" <?php selected( $year, $i ); ?>><?php echo $i; ?></option>
						<?php endfor; ?>
					</select>
				</div>
				<a class="next" href="<?php echo esc_url( add_query_arg( array( 'calendar_year' => $year, 'calendar_month' => $month + 1 ) ) ); ?>"></a>
			</div>
			<div class="views">
				<a class="week text_tip" href="<?php echo esc_url( add_query_arg( 'view', 'week' ) ); ?>" data-tip="<?php _e( 'Week View', 'woocommerce-appointments' ); ?>"><?php _e( 'Week View', 'woocommerce-appointments' ); ?></a>
				<a class="day text_tip" href="<?php echo esc_url( add_query_arg( 'view', 'day' ) ); ?>" data-tip="<?php _e( 'Day View', 'woocommerce-appointments' ); ?>"><?php _e( 'Day View', 'woocommerce-appointments' ); ?></a>
			</div>
			<div class="wcfm-clearfix"></div>
		</div>
		<div class="wcfm-clearfix"></div>
		<table class="wc_appointments_calendar widefat">
			<thead>
				<tr>
					<?php $start_of_week = get_option( 'start_of_week', 1 ); ?>
					<?php for ( $ii = $start_of_week; $ii < $start_of_week + 7; $ii ++ ) : ?>
						<th>
						  <span class="day_label_long"><?php echo date_i18n( _x( 'l', 'date format', 'woocommerce-appointments' ), strtotime( "next sunday +{$ii} day" ) ); ?></span>
						  <span class="day_label_short"><?php echo date_i18n( _x( 'D', 'date format', 'woocommerce-appointments' ), strtotime( "next sunday +{$ii} day" ) ); ?></span>
						</th>
					<?php endfor; ?>
				</tr>
			</thead>
			<tbody>
				<tr>
					<?php
					$timestamp    = $start_time;
					$current_date = date( 'Y-m-d', current_time( 'timestamp' ) );
					$index        = 0;
					while ( $timestamp <= $end_time ) :
						$timestamp_date = date( 'Y-m-d', $timestamp );
						$is_today       = $timestamp_date === $current_date;
						?>
						<td width="14.285%" class="<?php
						if ( date( 'n', $timestamp ) != absint( $month ) ) {
							echo 'calendar-diff-month';
						}
						if ( ( $timestamp + DAY_IN_SECONDS ) < current_time( 'timestamp' ) ) {
							echo ' calendar-passed-day';
						}
						if ( $is_today ) {
							echo ' calendar-current-day';
						}
						?>">
							<a href="<?php echo esc_url( get_wcfm_appointments_calendar_url( 'day', date( 'Y-m-d', $timestamp ) ) ); ?>" class="datenum">
								<?php echo esc_attr( date( 'd', $timestamp ) ); ?>
							</a>
							<div class="appointments bymonth">
								<?php
								$this->list_appointments(
									date( 'd', $timestamp ),
									date( 'm', $timestamp ),
									date( 'Y', $timestamp ),
									'by_month'
								);
								?>
							</div>
						</td>
						<?php
						$timestamp = strtotime( '+1 day', $timestamp );
						$index ++;

						if ( 0 === $index % 7 ) {
							echo '</tr><tr>';
						}
					endwhile;
					?>
				</tr>
			</tbody>
		</table>
	</form>
</div>
