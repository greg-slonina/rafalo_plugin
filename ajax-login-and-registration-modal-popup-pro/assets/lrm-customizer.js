wp.customize.bind( 'ready', function() { // Ready?

	var customize = this; // WordPress customize object alias.

	jQuery(".lrm-open-modal").on("click", function(){
		wp.customize.control( 'lrm_show_modal' ).setting.set( Math.random() );
		//wp.customize.previewer.send( "lrm", "lrm_show_modal" );
	});
	//
	// customize( 'lrm_show_modal', function( setting ) {
	// 	console.log( setting.get()	 );
	// 	console.log( customize.control( 'lrm_show_modal	' ).container.find( 'button' ) );
	// 	console.log( customize.control( 'lrm_show_modal	' ).container.find( 'button' ) );
	//
	// 	customize.control( 'lrm_show_modal' ).section.find( 'button' ).on("click", function(){
	// 		alert("asdas");
	// 	});
	// } );
} );