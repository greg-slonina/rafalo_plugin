(function($) {
	jQuery('.woocommerce-events-color-field').wpColorPicker();
	if ( jQuery( "#WooCommerceEventsEvent" ).length ) {
		checkEventForm();
		
		jQuery('#WooCommerceEventsEvent').change(function() {
			checkEventForm();
		})
                
    if( (typeof localObj === "object") && (localObj !== null) ) {
                
			jQuery('#WooCommerceEventsDate').datepicker({
				showButtonPanel: true,
				closeText: localObj.closeText,
				currentText: localObj.currentText,
				monthNames: localObj.monthNames,
				monthNamesShort: localObj.monthNamesShort,
				dayNames: localObj.dayNames,
				dayNamesShort: localObj.dayNamesShort,
				dayNamesMin: localObj.dayNamesMin,
				dateFormat: localObj.dateFormat,
				firstDay: localObj.firstDay,
				isRTL: localObj.isRTL,
			});

    } else {
      jQuery('#WooCommerceEventsDate').datepicker();
    }
                
    if( (typeof localObj === "object") && (localObj !== null) ) {
			jQuery('#WooCommerceEventsEndDate').datepicker({
				showButtonPanel: true,
				closeText: localObj.closeText,
				currentText: localObj.currentText,
				monthNames: localObj.monthNames,
				monthNamesShort: localObj.monthNamesShort,
				dayNames: localObj.dayNames,
				dayNamesShort: localObj.dayNamesShort,
				dayNamesMin: localObj.dayNamesMin,
				dateFormat: localObj.dateFormat,
				firstDay: localObj.firstDay,
				isRTL: localObj.isRTL,
			});
    } else {
			jQuery('#WooCommerceEventsEndDate').datepicker();
		}
                
		var fileInput = '';

		jQuery('.wrap').on('click', '.upload_image_button_woocommerce_events', function(e) {
			e.preventDefault();

			var button = jQuery(this);
			var id = jQuery(this).parent().prev('input.uploadfield');
			wp.media.editor.send.attachment = function(props, attachment) {
				id.val(attachment.url);
			};
			wp.media.editor.open(button);
			return false;
		});

		jQuery('.upload_reset').click(function() {
				jQuery(this).parent().prev('input.uploadfield').val('');
		});

		// user inserts file into post. only run custom if user started process using the above process
		// window.send_to_editor(html) is how wp would normally handle the received data

		window.original_send_to_editor = window.send_to_editor;
		window.send_to_editor = function(html){
			window.original_send_to_editor(html);
		};
                
		jQuery('.wrap').on('change', '#WooCommerceEventsExportUnpaidTickets', function(e) {
				showUpdateMessageBadges();
		});
		
		jQuery('.wrap').on('change', '#WooCommerceEventsExportBillingDetails', function(e) {
				showUpdateMessageBadges();
		});

		jQuery('.wrap').on('change', '#WooCommerceBadgeSize', function(e) {
				showUpdateMessageBadges();
		});

		jQuery('.wrap').on('change', '#WooCommerceBadgeField1', function(e) {
				showUpdateMessageBadges();
		});

		jQuery('.wrap').on('change', '#WooCommerceBadgeField2', function(e) {
				showUpdateMessageBadges();
		});

		jQuery('.wrap').on('change', '#WooCommerceBadgeField3', function(e) {
				showUpdateMessageBadges();
		});

		jQuery('input[type=radio][name=WooCommerceEventsPrintTicketLogoOption]').on('change', function(e) {
				showUpdateMessagePrintTickets();
		});

		jQuery('.wrap').on('change', '#WooCommerceEventsPrintTicketLogo', function(e) {
				showUpdateMessagePrintTickets();
		});

		jQuery('.wrap').on('change', '#WooCommercePrintTicketField1', function(e) {
				showUpdateMessagePrintTickets();
		});

		jQuery('.wrap').on('change', '#WooCommercePrintTicketField1_font', function(e) {
				showUpdateMessagePrintTickets();
		});

		jQuery('.wrap').on('change', '#WooCommercePrintTicketField2', function(e) {
				showUpdateMessagePrintTickets();
		});

		jQuery('.wrap').on('change', '#WooCommercePrintTicketField2_font', function(e) {
				showUpdateMessagePrintTickets();
		});

		jQuery('.wrap').on('change', '#WooCommercePrintTicketField3', function(e) {
				showUpdateMessagePrintTickets();
		});

		jQuery('.wrap').on('change', '#WooCommercePrintTicketField3_font', function(e) {
				showUpdateMessagePrintTickets();
		});

		jQuery('.wrap').on('change', '#WooCommercePrintTicketField4', function(e) {
				showUpdateMessagePrintTickets();
		});

		jQuery('.wrap').on('change', '#WooCommercePrintTicketField4_font', function(e) {
				showUpdateMessagePrintTickets();
		});

		jQuery('.wrap').on('change', '#WooCommercePrintTicketField5', function(e) {
				showUpdateMessagePrintTickets();
		});

		jQuery('.wrap').on('change', '#WooCommercePrintTicketField5_font', function(e) {
				showUpdateMessagePrintTickets();
		});

		jQuery('.wrap').on('change', '#WooCommercePrintTicketField6', function(e) {
				showUpdateMessagePrintTickets();
		});

		jQuery('.wrap').on('change', '#WooCommercePrintTicketField6_font', function(e) {
				showUpdateMessagePrintTickets();
		});
        

  }
    
	function showUpdateMessageBadges() {
		jQuery('#WooCommerceBadgeMessage').html('Update product for attendee badge options to take affect.');
	}

	function showUpdateMessagePrintTickets() {
		jQuery('#WooCommercePrintTicketMessage').html('Update product for ticket printing options to take affect.');
	}
	
	// Start functions 
	function checkEventForm() {
		var WooCommerceEventsEvent = jQuery('#WooCommerceEventsEvent').val();
		if(WooCommerceEventsEvent == 'Event') {
			jQuery('#WooCommerceEventsForm').show();
		} else {
			jQuery('#WooCommerceEventsForm').hide();
		}
		resetCollapsHeight(jQuery('#WooCommerceEventsForm'));
	} 
	
})(jQuery);

(function($) {
    
	if ( jQuery('input[name=WooCommerceEventsMultiDayType]').length ) {
			
		if( (typeof localObj === "object") && (localObj !== null) ) {
		
			jQuery('.WooCommerceEventsSelectDate').datepicker({
					showButtonPanel: true,
					closeText: localObj.closeText,
					currentText: localObj.currentText,
					monthNames: localObj.monthNames,
					monthNamesShort: localObj.monthNamesShort,
					dayNames: localObj.dayNames,
					dayNamesShort: localObj.dayNamesShort,
					dayNamesMin: localObj.dayNamesMin,
					dateFormat: localObj.dateFormat,
					firstDay: localObj.firstDay,
					isRTL: localObj.isRTL,
			});
		
		} else {
			jQuery('.WooCommerceEventsSelectDate').datepicker();
		}
	
		var multiDayType = jQuery('input[name=WooCommerceEventsMultiDayType]:checked').val();
			
		if(multiDayType == 'select') {
			hide_start_end_date();
			display_select_date_inputs_np();
		}

		if(multiDayType == 'sequential') {
			show_start_end_date();
			hide_select_date_inputs();
		}

		jQuery('input[name=WooCommerceEventsMultiDayType]').change(function(){
				
			var multiDayType = this.value;

			if(multiDayType == 'select') {
				hide_start_end_date();
				display_select_date_inputs(localObj.dayTerm);
			}
			
			if(multiDayType == 'sequential') {
				show_start_end_date();
				hide_select_date_inputs();
			}
		});
			
		jQuery('#WooCommerceEventsNumDays').change(function(){
				
				var multiDayType = jQuery('input[name=WooCommerceEventsMultiDayType]:checked').val();

				if(multiDayType == 'select') {
					hide_start_end_date();
					display_select_date_inputs(localObj.dayTerm);
				}
		});
	}
	
	function hide_start_end_date() {
		jQuery('#WooCommerceEventsEndDateContainer').hide();
		jQuery('#WooCommerceEventsDateContainer').hide();
	}
	
	function show_start_end_date() {
		jQuery('#WooCommerceEventsEndDateContainer').show();
		jQuery('#WooCommerceEventsDateContainer').show();
	}
	
	function show_select_date_inputs() {
		jQuery('#WooCommerceEventsDateContainer').hide();
	}
	
	function hide_select_date_inputs() {
		jQuery('#WooCommerceEventsSelectDateContainer').hide();
	}
	
	function display_select_date_inputs_np() {
		jQuery('#WooCommerceEventsSelectDateContainer').show();
	}
	
	function display_select_date_inputs(dayTerm) {
			
		jQuery('#WooCommerceEventsSelectDateContainer').show();
		
		var numDays = jQuery('#WooCommerceEventsNumDays').val();
		//alert(numDays);
		//jQuery('#WooCommerceEventsMultiDayTypeHolder').after('<div id="space">Test</div>');
		
		var dateFields = '';
		for (var i = 1; i <= numDays; i++) {
			dateFields += '<p class="form-field">';
			dateFields += '<span class="wcfm_title"><strong>'+dayTerm+' '+i+'</strong></span>';
			dateFields += '<input type="text" class="WooCommerceEventsSelectDate wcfm-text" name="WooCommerceEventsSelectDate[]" value=""/>';
			dateFields += '</p>';
		}
		
		jQuery('#WooCommerceEventsSelectDateContainer').html(dateFields);
			
		if( (typeof localObj === "object") && (localObj !== null) ) {
			jQuery('.WooCommerceEventsSelectDate').datepicker({
					showButtonPanel: true,
					closeText: localObj.closeText,
					currentText: localObj.currentText,
					monthNames: localObj.monthNames,
					monthNamesShort: localObj.monthNamesShort,
					dayNames: localObj.dayNames,
					dayNamesShort: localObj.dayNamesShort,
					dayNamesMin: localObj.dayNamesMin,
					dateFormat: localObj.dateFormat,
					firstDay: localObj.firstDay,
					isRTL: localObj.isRTL,
			});
		} else {
			jQuery('.WooCommerceEventsSelectDate').datepicker();
		}
	}
    
})(jQuery);


(function($) {
    
	var typing_timer;
	var done_typing_interval = 800;
	
	jQuery('#fooevents_custom_attendee_fields_new_field').on('click', function(){
		fooevents_new_attendee_field();
		resetCollapsHeight(jQuery('#WooCommerceEventsForm'));
		return false;
	});
	
	jQuery('#fooevents_custom_attendee_fields_options_table').on('click', '.fooevents_custom_attendee_fields_remove', function(event) {
		fooevents_delete_attendee_field(jQuery(this));
		return false;
	});

	jQuery('#fooevents_custom_attendee_fields_options_table').on('keyup', '.fooevents_custom_attendee_fields_label', function(event) {
		clearTimeout(typing_timer);
		typing_timer = setTimeout(fooevents_update_attendee_row_ids, done_typing_interval, jQuery(this));
		return false;
	});
	
	jQuery('#fooevents_custom_attendee_fields_options_table').on('keyup', '.fooevents_custom_attendee_fields_options', function(event) {
		fooevents_serialize_options();
		return false;
	});

	
	jQuery('#fooevents_custom_attendee_fields_options_table').on('keydown', '.fooevents_custom_attendee_fields_label', function(event) {
		clearTimeout(typing_timer);
	});

	jQuery('#fooevents_custom_attendee_fields_options_table').on('change', '.fooevents_custom_attendee_fields_req', function(event) {
		fooevents_serialize_options();
	});
	
	jQuery('#fooevents_custom_attendee_fields_options_table').on('change', '.fooevents_custom_attendee_fields_type', function(event) {
		fooevents_serialize_options();
		fooevents_enable_disable_options(jQuery(this));
	});
	
	fooevents_serialize_options();
    
})(jQuery);

function fooevents_new_attendee_field() {
    
	var opt_num = jQuery('#fooevents_custom_attendee_fields_options_table tr').length;
	
	var label   = '<input type="text" id="'+opt_num+'_label" name="'+opt_num+'_label" class="fooevents_custom_attendee_fields_label" value="Label_'+opt_num+'" autocomplete="off" maxlength="50" />';
	var type    = '<select id="'+opt_num+'_type" name="'+opt_num+'_type" class="fooevents_custom_attendee_fields_type"><option value="text">Text</option><option value="textarea">Textarea</option><option value="select">Select</option></select>';
	var options = '<input id="'+opt_num+'_options" name="'+opt_num+'_options" class="fooevents_custom_attendee_fields_options" type="text" disabled autocomplete="off" />';
	var req     = '<select id="'+opt_num+'_req" name="'+opt_num+'_req" class="fooevents_custom_attendee_fields_req"><option value="true">Yes</option><option value="false">No</option></select>';
	var remove  = '<a href="#" id="'+opt_num+'_remove" name="'+opt_num+'_remove" class="fooevents_custom_attendee_fields_remove" class="fooevents_custom_attendee_fields_remove">[X]</a>';
	
	var new_field = '<tr id="'+opt_num+'_option" class="fooevents_custom_attendee_fields_option"><td>'+label+'</td><td>'+type+'</td><td>'+options+'</td><td>'+req+'</td><td>'+remove+'</td></tr>';
	jQuery('#fooevents_custom_attendee_fields_options_table tbody').append(new_field);
}

function fooevents_delete_attendee_field(row) {
	row.closest('tr').remove();
	fooevents_serialize_options();
}

function fooevents_change_attendee_field_type(row) {
 row.closest('.fooevents_custom_attendee_fields_options').remove();
}

function fooevents_update_attendee_row_ids(row){
    
	var row_num = row.closest('tr').index()+1;
	var value = fooevents_encode_input(row.val());

	var new_label_id = value+'_label';
	var new_type_id = value+'_type';
	var new_options_id = value+'_options';
	var new_req_id = value+'_req';
	var new_remove_id = value+'_remove';
	var new_option_id = value+'_option';

	fooevents_check_if_label_exists(value);

	jQuery('#fooevents_custom_attendee_fields_options_table tr:eq('+row_num+') .fooevents_custom_attendee_fields_label').attr("id", new_label_id);
	jQuery('#fooevents_custom_attendee_fields_options_table tr:eq('+row_num+') .fooevents_custom_attendee_fields_label').attr("name", new_label_id);
	jQuery('#fooevents_custom_attendee_fields_options_table tr:eq('+row_num+') .fooevents_custom_attendee_fields_type').attr("id", new_type_id);
	jQuery('#fooevents_custom_attendee_fields_options_table tr:eq('+row_num+') .fooevents_custom_attendee_fields_type').attr("name", new_type_id);
	jQuery('#fooevents_custom_attendee_fields_options_table tr:eq('+row_num+') .fooevents_custom_attendee_fields_options').attr("id", new_options_id);
	jQuery('#fooevents_custom_attendee_fields_options_table tr:eq('+row_num+') .fooevents_custom_attendee_fields_options').attr("name", new_options_id);
	jQuery('#fooevents_custom_attendee_fields_options_table tr:eq('+row_num+') .fooevents_custom_attendee_fields_req').attr("id", new_req_id);
	jQuery('#fooevents_custom_attendee_fields_options_table tr:eq('+row_num+') .fooevents_custom_attendee_fields_req').attr("name", new_req_id);
	jQuery('#fooevents_custom_attendee_fields_options_table tr:eq('+row_num+') .fooevents_custom_attendee_fields_remove').attr("id", new_remove_id);
	jQuery('#fooevents_custom_attendee_fields_options_table tr:eq('+row_num+') .fooevents_custom_attendee_fields_remove').attr("name", new_remove_id);
	jQuery('#fooevents_custom_attendee_fields_options_table tr:eq('+row_num+')').attr("id", new_option_id);
	
	fooevents_serialize_options();
}

function fooevents_get_row_option_names() {
	var IDs = [];
	jQuery("#fooevents_custom_attendee_fields_options_table").find("tr").each(function(){ IDs.push(this.id); });
	
	return IDs;
}

function fooevents_serialize_options() {
    
	var data={};
	var item_num = 0;
	jQuery('#fooevents_custom_attendee_fields_options_table').find('tr').each(function(){
			var id=jQuery(this).attr('id');
			if(id) {
					var row={};
					jQuery(this).find('input,select,textarea').each(function(){
							row[jQuery(this).attr('name')]=jQuery(this).val();
					});
					data[id]=row;
			}
			
			item_num++;
	});
	
	data = JSON.stringify(data);
	
	jQuery('#fooevents_custom_attendee_fields_options_serialized').val(data);
}

function fooevents_enable_disable_options(row) {
    
	var row_num = row.closest('tr').index()+1;
	var option_type = jQuery('#fooevents_custom_attendee_fields_options_table tr:eq('+row_num+') .fooevents_custom_attendee_fields_type').val();
	if(option_type == 'select') {
		jQuery('#fooevents_custom_attendee_fields_options_table tr:eq('+row_num+') .fooevents_custom_attendee_fields_options').prop("disabled", false);
	} else {
		jQuery('#fooevents_custom_attendee_fields_options_table tr:eq('+row_num+') .fooevents_custom_attendee_fields_options').prop("disabled", true);
		jQuery('#fooevents_custom_attendee_fields_options_table tr:eq('+row_num+') .fooevents_custom_attendee_fields_options').val("");
	}
	fooevents_serialize_options();
}


(function($) {
    
	var typing_timer;
	var done_typing_interval = 800;
	
	jQuery('#fooevents_seating_new_field').on('click', function(){
		fooevents_seating_new_row_field();
		resetCollapsHeight(jQuery('#WooCommerceEventsForm'));
		return false;
	});
	
	jQuery('#fooevents_seating_options_table').on('click', '.fooevents_seating_remove', function(event) {
		fooevents_seating_delete_row_field(jQuery(this));
		return false;
	});

	jQuery('#fooevents_seating_options_table').on('keyup', '.fooevents_seating_row_name, .fooevents_seating_options', function(event) {
		clearTimeout(typing_timer);
		typing_timer = setTimeout(fooevents_update_row_row_ids, done_typing_interval, jQuery(this));
		return false;
	});

	
	jQuery('#fooevents_seating_options_table').on('keydown', '.fooevents_seating_row_name, .fooevents_seating_options', function(event) {
		clearTimeout(typing_timer);
	});

	jQuery('#fooevents_seating_options_table').on('change', '.fooevents_seating_variations', function(event) {
		fooevents_serialize_options_seating();
	});
	
	jQuery('#fooevents_seating_options_table').on('change', '.fooevents_seating_number_seats', function(event) {
		fooevents_serialize_options_seating();
	});
	
	fooevents_serialize_options_seating();
    
})(jQuery);


      
jQuery('#fooevents_seating_chart').on('click', function(){
	var viewportWidth = window.innerWidth-20;
	var viewportHeight = window.innerHeight-20;
	if (viewportWidth > 1000) viewportWidth = 1000;
	if (viewportHeight > 500) viewportHeight = 500;

	jQuery("#fooevents_seating_dialog").html("");
	
	var rowName = "";
	var rowID = "";
	var numberSeats = 0;
	var seats = "";
	var unavailableSeatsID = "";
	var unavailableSeats = "";
	var currentRow = "";
	var seatClass = "available";
	
	jQuery("#fooevents_seating_options_table tbody tr").each(function(){
			rowName = jQuery(this).find(".fooevents_seating_row_name").val();
			rowID = jQuery(this).find(".fooevents_seating_row_name").attr("id");
			numberSeats = jQuery(this).find(".fooevents_seating_number_seats").val();
			jQuery("#fooevents_seating_dialog").append("<div class='fooevents_seating_chart_view_row_name' id='" + rowID + "'>" + rowName + "</div>");
			seats = jQuery('<div>', { 'class': 'fooevents_seating_chart_view_row' });
			
			unavailableSeatsID = jQuery('input[value="fooevents_seats_unavailable_serialized"]').attr("id");
			if (unavailableSeatsID !== undefined) {
					unavailableSeatsID = unavailableSeatsID.substr(0, unavailableSeatsID.lastIndexOf("-")) + "-value";
					unavailableSeats = jQuery("#" + unavailableSeatsID).html();
			} else {
					unavailableSeatsID = "";
			}
	
			
			currentRow = rowID.substr(0, rowID.indexOf("_row_name")) + "_number_seats_";
			
			for(var i = 1; i <= numberSeats; i++) {
					if ((unavailableSeats !== undefined) && (unavailableSeats.indexOf(currentRow + i) > -1))
							seatClass = "unavailable";
					
					jQuery(seats).append("<span class='" + seatClass + "'>" + i + "</span>");
					seatClass = "available";
			}

			
			jQuery("#fooevents_seating_dialog").append(seats);
		
			
	});
	if( jQuery("#fooevents_seating_dialog").is(':empty') ) {
			jQuery("#fooevents_seating_dialog").append("<div style='margin-top:20px'>No seats to show. Add rows and seats by clicking on the '+ New Row' button.</div>");
	}
	
	jQuery("#fooevents_seating_dialog").dialog({
			width: "50%",
			maxWidth: "768px",
			height: "auto",
			maxHeight: "768px",
	});
});
    
     
  
function get_variations(opt_num) {
	var productID = jQuery("#post_ID").val();
	var the_variations = "";
	var dataVariations = {
							'action': 'fetch_woocommerce_variations',
							'productID': productID,
              'dataType': 'json'
							};

	the_variations = jQuery.post(ajaxurl, dataVariations, function(response) {
		if(response) {
			return response;
		}
	}).done(function(data){
		option_pos_start = data.indexOf("<option");
		option_pos_end = data.lastIndexOf("</select>");
		data = data.substring(option_pos_start, option_pos_end);
	  jQuery("#" + opt_num + "_WooCommerceEventsSelectedVariation").append(data);
	});
}

function fooevents_seating_new_row_field() {
	var opt_num = jQuery('#fooevents_seating_options_table tr').length;
	
	var row_name   = '<input type="text" id="'+opt_num+'_row_name" name="'+opt_num+'_row_name" class="fooevents_seating_row_name" value="Row'+opt_num+'" autocomplete="off" maxlength="50" />';
	var number_seats    = '<input class="fooevents_seating_number_seats" type="number" id="'+opt_num+'_number_seats" name="'+opt_num+'_number_seats" min="1" max="50" value="1">';
	
	var variations = '<select class="fooevents_seating_variations" id="'+opt_num+'_variations" name="'+opt_num+'_variations">' + jQuery("#fooevents_variations").html() + '</select>';
	
	var remove  = '<a href="#" id="'+opt_num+'_remove" name="'+opt_num+'_remove" class="fooevents_seating_remove" class="fooevents_seating_remove">[X]</a>';
	
	var new_field = '<tr id="'+opt_num+'_option" class="fooevents_seating_option"><td>'+row_name+'</td><td>'+number_seats+'</td><td>'+variations+'</td><td>'+remove+'</td></tr>';
	jQuery('#fooevents_seating_options_table tbody').append(new_field);
}


function fooevents_seating_delete_row_field(row) {
	row.closest('tr').remove();
	fooevents_serialize_options_seating();
}

function fooevents_change_row_field_type(row) {
	 row.closest('.fooevents_seating_options').remove();
}

function fooevents_update_row_row_ids(row){

	var row_num = row.closest('tr').index()+1;
	var value = fooevents_encode_input(row.val());
	
	var new_row_name_id = value+'_row_name';
	var new_number_seats_id = value+'_number_seats';
	var new_variations_id = value+'_variations';
	var new_remove_id = value+'_remove';
	var new_option_id = value+'_option';
	
	fooevents_check_if_option_exists(value);

	jQuery('#fooevents_seating_options_table tr:eq('+row_num+') .fooevents_seating_row_name').attr("id", new_row_name_id);
	jQuery('#fooevents_seating_options_table tr:eq('+row_num+') .fooevents_seating_row_name').attr("name", new_row_name_id);
	jQuery('#fooevents_seating_options_table tr:eq('+row_num+') .fooevents_seating_number_seats').attr("id", new_number_seats_id);
	jQuery('#fooevents_seating_options_table tr:eq('+row_num+') .fooevents_seating_number_seats').attr("name", new_number_seats_id);
	jQuery('#fooevents_seating_options_table tr:eq('+row_num+') .fooevents_seating_variations').attr("id", new_variations_id);
	jQuery('#fooevents_seating_options_table tr:eq('+row_num+') .fooevents_seating_variations').attr("name", new_variations_id);
	jQuery('#fooevents_seating_options_table tr:eq('+row_num+') .fooevents_seating_remove').attr("id", new_remove_id);
	jQuery('#fooevents_seating_options_table tr:eq('+row_num+') .fooevents_seating_remove').attr("name", new_remove_id);
	jQuery('#fooevents_seating_options_table tr:eq('+row_num+')').attr("id", new_option_id);
	
	fooevents_serialize_options_seating();
    
}

function fooevents_encode_input(input) {
	var output = input.toLowerCase();
	output = output.replace(/ /g,"_");
	
	return output;
}

function fooevents_get_row_option_names() {
	var IDs = [];
	jQuery("#fooevents_seating_options_table").find("tr").each(function(){ IDs.push(this.id); });
	
	return IDs;
}

function fooevents_check_if_option_exists(value){
	value = value+'_option';
	
	var IDs = fooevents_get_row_option_names();
	if(jQuery.inArray(value, IDs) !== -1) {
		alert('Row name is already in use');
	}
}

function fooevents_serialize_options_seating() {
	var data={};
	var item_num = 0;
	jQuery('#fooevents_seating_options_table tbody').find('tr').each(function(){
		var id=jQuery(this).attr('id');
		if(id) {
			var row={};
			jQuery(this).find('input,select,textarea').each(function(){
					row[jQuery(this).attr('name')]=jQuery(this).val();
			});
			data[id]=row;
		}
		item_num++;
	});
	 
	data = JSON.stringify(data);
	jQuery('#fooevents_seating_options_serialized').val(data);
}

function fooevents_check_if_label_exists(value) {
	var arr = [];
	jQuery(".fooevents_custom_attendee_fields_label").each(function(){
		var value = jQuery(this).val();
		if (arr.indexOf(value) == -1)
			arr.push(value);
		else
			alert('Label is already in use');
	});
}



/*(function( $ ) {
	jQuery('.woocommerce-events-color-field').wpColorPicker();
})( jQuery );*/

/*(function( $ ) {
    
	var captureAttendee = true;
	
	jQuery('#WooCommerceEventsEvent').on("change", function() {
			
		var productID = jQuery(this).val();
		
		var dataVariations = {
			'action': 'fetch_woocommerce_variations',
			'productID': productID
		};
							
		jQuery.post(ajaxurl, dataVariations, function(response) {
			if(response) {
				jQuery('#woocommerce_events_variations').html(response);
			}
		});
			
		var dataAttendeeCapture = {
			'action': 'fetch_capture_attendee_details',
			'productID': productID
		};
			
		jQuery.post(ajaxurl, dataAttendeeCapture, function(response) {
			var details = JSON.parse(response);
			if(details.capture == 'off') {
				captureAttendee = false;
			}
		});
	});
	
	jQuery('#WooCommerceEventsClientID').on("change", function(){
			
		var userID = jQuery(this).val();
		
		jQuery('#WooCommerceEventsPurchaserFirstName').val('');
		jQuery("#WooCommerceEventsPurchaserFirstName").removeAttr("readonly"); 
		jQuery('#WooCommerceEventsPurchaserEmail').val('');
		jQuery("#WooCommerceEventsPurchaserEmail").removeAttr("readonly"); 
		jQuery('#WooCommerceEventsPurchaserUserName').val('');
		jQuery("#WooCommerceEventsPurchaserUserName").removeAttr("readonly"); 
		
		if(userID) {
					
			var data = {
										'action': 'fetch_wordpress_user',
										'userID': userID
							    };
					
			jQuery.post(ajaxurl, data, function(response) {
				var user = JSON.parse(response);
				if(user.ID) {
					jQuery('#WooCommerceEventsPurchaserUserName').val(user.data.user_login);
					jQuery("#WooCommerceEventsPurchaserUserName").prop('readonly', true);
					jQuery('#WooCommerceEventsPurchaserFirstName').val(user.data.display_name);
					jQuery("#WooCommerceEventsPurchaserFirstName").prop('readonly', true);
					jQuery('#WooCommerceEventsPurchaserEmail').val(user.data.user_email);
					jQuery("#WooCommerceEventsPurchaserEmail").prop('readonly', true);
				} 
			});
		}
	});
	
	
	jQuery('#post').submit(function() {
			
		var error = false;
		var addTicket = jQuery('#add_ticket').val();
		
		if(addTicket) {
		
			if(!addTicket) {
				error = true;
			}

			if(!jQuery('#WooCommerceEventsEvent').val()) {
				error = true;
			}

			if(!jQuery('#WooCommerceEventsPurchaserFirstName').val()) {
				error = true;
			}

			if(!jQuery('#WooCommerceEventsPurchaserUserName').val()) {
				error = true;
			}

			if(!jQuery('#WooCommerceEventsPurchaserEmail').val()) {
				error = true;
			}

			if(error) {
				alert('All fields are required');
				return false;
			}
		}
			
	});
    
})( jQuery );*/


/*(function($) {
    
	var postID = jQuery('#pro_id').val();

	jQuery('#WooCommerceEventsResendTicket').on("click", function(){    
			
		jQuery('#WooCommerceEventsResendTicketMessage').html("<div class='notice notice-info'>Sending...</div>");
		var WooCommerceEventsResendTicketEmail = jQuery('#WooCommerceEventsResendTicketEmail').val();
		if(!WooCommerceEventsResendTicketEmail) {
			jQuery('#WooCommerceEventsResendTicketMessage').html("<div class='notice notice-error'>Email address required.</div>");
		} else {
			var data = {
											'action': 'resend_ticket',
											'WooCommerceEventsResendTicketEmail': WooCommerceEventsResendTicketEmail,
											'postID': postID
							};
			
			jQuery.post(ajaxurl, data, function(response) {
				 var email = JSON.parse(response);
				 jQuery('#WooCommerceEventsResendTicketMessage').html("<div class='notice notice-success'>"+email.message+"</div>");
			});
		}
		
		return false;
	});
	
	
	function getParameter(name){
		var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
		if (results==null){
		 return null;
		} else {
		 return results[1] || 0;
		}
	}
    
})( jQuery );*/