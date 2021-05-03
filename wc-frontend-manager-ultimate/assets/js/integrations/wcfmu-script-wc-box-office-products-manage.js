jQuery(document).ready(function($) {
	// Ticket
	$('#_ticket').change(function() {
		if($(this).is(':checked')) {
			$('.wc-box-office-ticket').removeClass('wcfm_ele_hide wcfm_block_hide wcfm_head_hide');
			resetCollapsHeight($('.collapse-open').next('.wcfm-container').find('.wcfm_ele:not(.wcfm_title):first'));
		} else {
			$('.wc-box-office-ticket').addClass('wcfm_ele_hide wcfm_block_hide wcfm_head_hide');
		}
	}).change();
	
	$( document.body ).on( 'wcfm_product_type_changed', function() {
	  $('#_ticket').change();
	});
		
	// Ticket_field options
	function ticket_email_field_rules() {
		$('#_ticket_fields').find('.multi_input_block').each(function() {
			$(this).find('.ticket_type_field').change(function() {
				$ticket_type_field_type = $(this).val();
				$(this).parent().find('.ticket_email_field_options').addClass('wcfm_ele_hide');
				$(this).parent().find('.ticket_additional_field_options').addClass('wcfm_ele_hide');
				if( $ticket_type_field_type == 'email' ) {
					$(this).parent().find('.ticket_email_field_options').removeClass('wcfm_ele_hide');
				} else if( $ticket_type_field_type == 'select' || $ticket_type_field_type == 'radio' || $ticket_type_field_type == 'checkbox' ) {
					$(this).parent().find('.ticket_additional_field_options').removeClass('wcfm_ele_hide');
				}
			}).change();
		});
	}
	ticket_email_field_rules();
	$('#_ticket_fields').find('.add_multi_input_block').click(function() {
	  ticket_email_field_rules();
	});
	
	// TinyMCE intialize - Ticket Content
	if( $('#_ticket_content').hasClass('rich_editor') ) {
		var ticket_content = tinymce.init({
																	selector: '#_ticket_content',
																	height: 75,
																	menubar: false,
																	plugins: [
																		'advlist autolink lists link charmap print preview anchor',
																		'searchreplace visualblocks code fullscreen',
																		'insertdatetime image media table contextmenu paste code directionality',
																		'autoresize'
																	],
																	toolbar: tinyMce_toolbar,
																	content_css: '//www.tinymce.com/css/codepen.min.css',
																	statusbar: false,
																	browser_spellcheck: true,
																	entity_encoding: "raw"
																});
	}
	
	// TinyMCE intialize - Ticket Email Content
	if( $('#_ticket_email_html').hasClass('rich_editor') ) {
		var ticket_email_html = tinymce.init({
																	selector: '#_ticket_email_html',
																	height: 75,
																	menubar: false,
																	plugins: [
																		'advlist autolink lists link charmap print preview anchor',
																		'searchreplace visualblocks code fullscreen',
																		'insertdatetime image media table contextmenu paste code directionality',
																		'autoresize'
																	],
																	toolbar: tinyMce_toolbar,
																	content_css: '//www.tinymce.com/css/codepen.min.css',
																	statusbar: false,
																	browser_spellcheck: true,
																	entity_encoding: "raw"
																});
	}
});