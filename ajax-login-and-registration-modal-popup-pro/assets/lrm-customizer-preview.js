(function( $ ) {
	"use strict";

	wp.customize( 'lrm_show_modal', function( value ) {

		value.bind( function( to ) {
			jQuery(document).trigger('lrm_show_signup');
		} );
	});

	// wp.customize.preview.bind( 'lrm', function( data ) {
	// 	alert( '"my-custom-event" has been received from the Previewer. Check the console for the data.' );
	//
	// 	console.log( data );
	// } );
	//
	// wp.customize.bind( 'lrm', function( data ) {
	// 	alert( '"my-custom-event" has been received from the Previewer. Check the console for the data.' );
	//
	// 	console.log( data );
	// } );
})( jQuery );