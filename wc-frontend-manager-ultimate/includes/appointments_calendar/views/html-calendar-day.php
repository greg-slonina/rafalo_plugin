<div class="wrap woocommerce">
	<form method="get" id="mainform" enctype="multipart/form-data" class="wc_appointments_calendar_form day_view">
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
				<a class="prev" href="<?php echo esc_url( add_query_arg( 'calendar_day', $prev_day ) ); ?>"></a>
				<div style="width:150px;">
					<input type="text" name="calendar_day" class="calendar_day date-picker wcfm-text" style="text-align:center;" value="<?php echo esc_attr( $day_formatted ); ?>" placeholder="<?php echo wc_date_format(); ?>" autocomplete="off" />
				</div>
				<a class="next" href="<?php echo esc_url( add_query_arg( 'calendar_day', $next_day ) ); ?>"></a>
			</div>
			<div class="views">
			  <a class="week text_tip" href="<?php echo esc_url( add_query_arg( 'view', 'week' ) ); ?>" data-tip="<?php _e( 'Week View', 'woocommerce-appointments' ); ?>"><?php _e( 'Week View', 'woocommerce-appointments' ); ?></a>
				<a class="month text_tip" href="<?php echo esc_url( add_query_arg( 'view', 'month' ) ); ?>" data-tip="<?php _e( 'Month View', 'woocommerce-appointments' ); ?>"><?php _e( 'Month View', 'woocommerce-appointments' ); ?></a>
			</div>
			<div class="wcfm-clearfix"></div>
		</div>
		<div class="wcfm-clearfix"></div><br />
		
		<?php
		wc_enqueue_js(
			"
			// -------------------------------------
			// Calendar filters
			// -------------------------------------
			$( '.tablenav select, .tablenav input' ).change(function() {
				$( '#mainform' ).submit();
			});

			// -------------------------------------
			// Calendar date picker
			// -------------------------------------
			$( '.calendar_day' ).datepicker({
				dateFormat: 'yy-mm-dd',
				numberOfMonths: 1,
				showOtherMonths: true,
				changeMonth: true,
				showButtonPanel: true,
				minDate: null
			});

			// -------------------------------------
			// Display current time on calendar
			// -------------------------------------
			var current_date = $( '.body_wrapper .current' );
			var d = new Date();
			var calendar_h = $( '.hours' ).height();

			if ( current_date.length ) {
				var current_time = d.getHours() * 60 + d.getMinutes();
				var current_time_locale = d.toLocaleTimeString('en-US', {hour: '2-digit', minute:'2-digit'}).toLowerCase();
				var indicator_top = Math.round( calendar_h / ( 60 * 24 ) * current_time );
				current_date.append( '<div class=\"indicator tips\" title=\"'+ current_time_locale +'\"></div>' );
				$( '.indicator' ).css( {top: indicator_top} );
				$( '.indicator' ).tipTip();

				$( 'html, body' ).animate({
					scrollTop: $( '.indicator' ).offset().top - 300
				}, 'slow' );
			}

			setInterval( set_indicator, 60000 );

			function set_indicator() {
				var dt = new Date();
				var current_time = dt.getHours() * 60 + dt.getMinutes();
				var current_time_locale_updated = dt.toLocaleTimeString('en-US', {hour: '2-digit', minute:'2-digit'}).toLowerCase();
				var indicator_top = Math.round( calendar_h / ( 60 * 24 ) * current_time );
				$( '.indicator' ).css( {top: indicator_top} );
				$( '.indicator' ).attr( 'title', current_time_locale_updated );
				$( '.indicator' ).tipTip();
			}

			// -------------------------------------
			// Fixed header on scroll.
			// -------------------------------------
			$( window ).on( 'load resize scroll', function() {
				var el              = $( '.calendar_presentation' ),
				    floatingHeader  = $( '.calendar_header' ),
					floatingBody    = $( '.calendar_body' ),
					offset          = el.offset(),
					scrollTop       = $( window ).scrollTop(),
					windowWidth     = $( window ).width(),
					adminBarHeight  = ( windowWidth > 600 ? $( '#wpadminbar' ).outerHeight() : 0 ),
					scrollTopOffset = ( scrollTop + adminBarHeight );

					//console.log( scrollTopOffset + ' > ' + offset.top );

					if ((scrollTopOffset > offset.top) && (scrollTopOffset < offset.top + el.height())) {
						fixed_header();
					} else {
						floatingHeader.removeAttr( 'style' );
						floatingBody.removeAttr( 'style' );
					}
			});

			function fixed_header() {
				var floatingHeader   = $( '.calendar_header' ),
					floatingBody     = $( '.calendar_body' ),
				    windowWidth      = $( window ).width(),
				    adminBarHeight   = ( windowWidth > 600 ? $( '#wpadminbar' ).outerHeight() : 0 ),
					contentWrapWidth = $( '.calendar_presentation' ).width(),
					headerHeight     = floatingHeader.outerHeight();

				floatingHeader.css( {
					'background': '#fff',
					'box-shadow': '0 0 0 1px #ddd',
					'position'  : 'fixed',
					'top'       : adminBarHeight,
					'width'     : contentWrapWidth
				} );
				floatingBody.css( {
					'margin-top': headerHeight
				} );
			}

			// -------------------------------------
			// Scroll to clicked hours label
			// -------------------------------------
			$('.hours label').click(function(){
				var e = $(this);
				$('html,body').animate({
					scrollTop: e.position().top
				}, 300);
			});

			// -------------------------------------
			// Overlapping appointments algorythm.
			// -------------------------------------
			$('.appointments:not(.allday)').each( function( index, el ) {
				var by_time_events = $(el).find('.single_appointment');
				set_overlapping_width( by_time_events, el );
			});

			$('.appointments.allday').each( function( index, el ) {
				var all_day_events = $(el).find('.single_appointment');
				set_overlapping_width( all_day_events, el );
			});

			function set_overlapping_width( events = [], el ) {
				// Map overlapping events.
				var eventArray = jQuery.map(events, function (element, index) {
		            var event  = $(element);
		            var id     = event.data('appointment-id');
					var start  = event.data('appointment-start');
					var end    = event.data('appointment-end') - 1;
		            var complexEvent = {
		                'id'   : id,
						'start': start,
						'end'  : end
		            };
		            return complexEvent;
		        }).sort(function (a, b) {
		            return a.start - b.start;
		        });

				// Get overlapping events
				var results = []; // list of all events
				var index = []; // array of overlapped events
				var skip = []; // array of overlapped events to skip
			    for (var i = 0, l = eventArray.length; i < l; i++) {
			        var oEvent    = eventArray[i];
			        var nOverlaps = 0;
					var xOverlaps = 0;
			        for (var j = 0; j < l; j++) {
			            var oCompareEvent = eventArray[j];
						if ( (oEvent.start <= oCompareEvent.end) && (oEvent.end >= oCompareEvent.start) ) {
							nOverlaps++;
							index.push( oCompareEvent.id );
							if ( (oEvent.start === oCompareEvent.end) || (oEvent.end === oCompareEvent.start) ) {
								xOverlaps++;
								skip.push( oCompareEvent.id );
				            }
			            }

			        }

					// Skip events that have all overlaps
					// with same start/end times.
					if ((nOverlaps-1) === xOverlaps && 1 < nOverlaps) {
						continue;
					}

					// Modify overlapped events.
			        if (1 < nOverlaps) {
						var event_id        = oEvent.id;
						var event_count     = nOverlaps;
						var event_index     = index.filter(i => i === event_id).length;
						var event_new_index = event_index - 1; // reduce by one to skip first event in index.

						var event           = $(el).find('.single_appointment[data-appointment-id='+event_id+']');
						var event_width     = event.width();
						var event_new_width = Math.floor(((100 / event_count) * 10) / 10);
						var event_left      = event.position().left;
						var event_new_left  = Math.abs(event_left + (event_new_width * event_new_index));

						event.css({
					        'width': event_new_width + '%',
					        'left' : event_new_left + '%'
					    });

						/*
						results.push({
			                id         : event_id,
			                eventCount : event_count,
							eventIndex : event_index,
							eventWidth : event_width,
							eventNWidth: event_new_width,
							eventLeft  : event_left,
							eventNLeft : event_new_left
			            });
						*/
			        }

			    }

		        //console.log(results);
			}
			"
		);
		?>
		
		<div class="calendar_wrapper">
			<?php
			// Variables.
			$calendar_scale    = apply_filters( 'woocommerce_appointments_calendar_view_day_scale', 60 );
			$current_timestamp = current_time( 'timestamp' );
			?>
			<div class="calendar_presentation">
				<div class="calendar_header">
					<div class="header_labels">
						<label class="empty_label"></label>
						<label class="allday_label"><?php esc_html_e( 'All Day', 'woocommerce-appointments' ); ?></label>
					</div>
					<div class="header_days">
						<?php $index = 0; ?>
						<div class="header_wrapper">
							<?php
							$current_on_cal = date( 'Y-m-d', strtotime( $day ) ) === date( 'Y-m-d', $current_timestamp );
							$current_class  = $current_on_cal ? ' current' : '';
							$past_on_cal    = date( 'Y-m-d', strtotime( $day ) ) < date( 'Y-m-d', $current_timestamp );
							$current_class .= $past_on_cal ? ' past' : '';
							echo "<div class='header_column$current_class' data-time='" . date( 'Y-m-d', strtotime( $day ) ) . "'>";
								echo '<div class="header_label"><a href="' . esc_url( get_wcfm_appointments_calendar_url( 'day', date( 'Y-m-d', strtotime( $day ) ) ) ) . '" title="' . esc_attr( date( wc_date_format(), strtotime( $day ) ) ) . '">' . esc_attr( date( 'D', strtotime( $day ) ) ) . ' <span class="daynum">' . esc_attr( date( 'j', strtotime( $day ) ) ) . '</span></a></div>';
								echo '<div class="header_allday">';
									echo '<div class="appointments allday">';
										$this->list_appointments(
											date( 'd', strtotime( $day ) ),
											date( 'm', strtotime( $day ) ),
											date( 'Y', strtotime( $day ) ),
											'all_day'
										);
									echo '</div>';
								echo '</div>';
							echo '</div>';
							?>
						</div>
					</div>
				</div>
				<div class="calendar_body">
					<div class="body_labels">
						<div class="hours">
							<?php
							for ( $i = 0; $i < 24; $i ++ ) :
								if ( 24 != $i ) {
									echo '<div class="hour_label"><label>' . esc_attr( date_i18n( wc_time_format(), strtotime( "midnight +{$i} hour" ) ) ) . '</label></div>';
								}
							endfor;
							?>
						</div>
					</div>
					<div class="body_days">
						<?php $index = 0; ?>
						<div class="body_wrapper">
							<?php
							$current_on_cal = date( 'Y-m-d', strtotime( $day ) ) === date( 'Y-m-d', $current_timestamp );
							$current_class  = $current_on_cal ? ' current' : '';
							$past_on_cal    = date( 'Y-m-d', strtotime( $day ) ) < date( 'Y-m-d', $current_timestamp );
							$current_class .= $past_on_cal ? ' past' : '';
							echo "<div class='body_column$current_class' data-time='" . date( 'Y-m-d', strtotime( $day ) ) . "'>";
								echo '<div class="appointments bytime">';
									$this->list_appointments(
										date( 'd', strtotime( $day ) ),
										date( 'm', strtotime( $day ) ),
										date( 'Y', strtotime( $day ) ),
										'by_time'
									);
								echo '</div>';
							echo '</div>';
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>
