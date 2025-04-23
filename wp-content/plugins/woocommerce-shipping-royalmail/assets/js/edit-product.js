jQuery( function($) {
	var init_tube_interaction = function( parent_string = '' ) {
		var inp_tube = jQuery( parent_string + 'input[name*="_shipping-royalmail-tube"]' );

		inp_tube.each( function( idx ) {
			var var_width  = jQuery( this ).closest( 'div' ).find( '[name*="_width"]' );
			var var_height = jQuery( this ).closest( 'div' ).find( '[name*="_height"]' );

			if ( jQuery( this ).is( ':checked' ) ) {
				var_height.prop( 'readonly', true );
				var_height.val( var_width.val() );
			} else {
				var_height.prop( 'readonly', false );
			}
		} );

		inp_tube.on( 'change', function( evt ) {
			var var_width  = jQuery( this ).closest( 'div' ).find( '[name*="_width"]' );
			var var_height = jQuery( this ).closest( 'div' ).find( '[name*="_height"]' );

			if( jQuery( this ).is( ':checked' ) ) {
				var_height.prop( 'readonly', true );
				var_height.val( var_width.val() );
			} else {
				var_height.prop( 'readonly', false );
			}
		} );

		var inp_width = jQuery( parent_string + 'input[name*="_width"]' );

		inp_width.on( 'keypress keyup keydown', function( evt ) {
			var var_tube   = jQuery( this ).closest( 'div' ).find( '[name*="_shipping-royalmail-tube"]' );
			var var_height = jQuery( this ).closest( 'div' ).find( '[name*="_height"]' );
			if ( var_tube.is( ':checked' ) ) {
				var_height.val( jQuery( this ).val() );
			}
		} );
	}

	// Need to separate between simple product and variable using specific parent.
	// To avoid double event handler attachment.

	// Simple product need to be assigned when document ready.
	jQuery( document ).ready( function() {
		init_tube_interaction( '#shipping_product_data ' );
	} );

	// Variable product need to be assigned when variations is loaded.
	jQuery( '#woocommerce-product-data' ).on( 'woocommerce_variations_added woocommerce_variations_loaded', function() {
		init_tube_interaction( '.woocommerce_variations ' );
	} );
} );