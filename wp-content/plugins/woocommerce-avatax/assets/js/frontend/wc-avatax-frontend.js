jQuery( function( $ ) {

	'use-strict';

	var wc_avatax_frontend = window.wc_avatax_frontend;
	var wc_avatax_current_validate_button;
	var validatedAddress;
	/**
	 * WooCommerce AvaTax Frontend scripts
	 */

	// Show/hide the address validation button when the address country is changed
	$( document ).on( 'change', 'form.woocommerce-checkout .country_select', function() {

		var button;
		$("#wc-avatax-address-validation").hide();
		if ( $( this ).is( '#billing_country' ) ) {

			// If set to shipping address, leave the billing validation button as-is
			if ( $( '#ship-to-different-address-checkbox' ).is( ':checked' ) ) {
				return;
			}

			button = $( '.wc_avatax_validate_address[data-address-type="billing"]' );

		} else {

			button = $( '.wc_avatax_validate_address[data-address-type="shipping"]' );
		}

		// Check if the newly selected country supports address validation
		if ( $.inArray( $( this ).val(), wc_avatax_frontend != null ? wc_avatax_frontend.address_validation_countries : [] ) > -1 ) {
			$( button ).show();
		} else {
			$( button ).hide();
		}

	} );

	// Hide/show the billing address validation button when shipping address is toggled
	$( document ).on( 'change', 'form.woocommerce-checkout #ship-to-different-address-checkbox', function() {

		if ( $( this ).is( ':checked' ) && wc_avatax_frontend.tax_based_on!="billing") {
			$( '.wc_avatax_validate_address[data-address-type="billing"]' ).hide();
			$( '.wc_avatax_validate_address[data-address-type="shipping"]' ).show();
		}
		else if ( $( this ).is( ':checked' ) && wc_avatax_frontend.tax_based_on=="billing") {
			$( '.wc_avatax_validate_address[data-address-type="billing"]' ).show();
			$( '.wc_avatax_validate_address[data-address-type="shipping"]' ).hide();
		} 
		else if ( $.inArray( $( '#billing_country' ).val(), wc_avatax_frontend != null ? wc_avatax_frontend.address_validation_countries : [] )  > -1 ) {
			$( '.wc_avatax_validate_address[data-address-type="billing"]' ).show();
		}
		invalidate_address();

	} );
	
	$( document ).on( 'change','form.woocommerce-checkout #billing_wc_avatax_vat_id', function() {
		$( document.body ).trigger( 'update_checkout' );
	 });

	remove_retail_delievery_fee();

	$( document ).on( 'updated_checkout',function(){
		remove_retail_delievery_fee();
	});

	function remove_retail_delievery_fee(){
		$( 'form.woocommerce-checkout .woocommerce-checkout-review-order-table tr.fee , div.cart-collaterals table.shop_table_responsive tr.fee').each(function() {
			var lineLabel = $(this).html().indexOf('Retail Delivery Fee') != -1; 
			if (lineLabel)
			{
				$(this).remove();
			}   
		
		 });
		 
	}

	// force the country and "different address" checkbox fields to change
 	$( 'form.woocommerce-checkout .country_select' ).change();
 	$( 'form.woocommerce-checkout #ship-to-different-address-checkbox' ).change();
	$( 'form.woocommerce-checkout #billing_wc_avatax_vat_id' ).change();
	$( document ).on( 'click', '#place_order', function(e) {
		if ($('.wc_avatax_validate_address').length > 0 ) {
			if(!(validatedAddress && 
				($( 'input#' + validatedAddress["type"] + '_address_1' ).val()?.trim() == undefined || $( 'input#' + validatedAddress["type"] + '_address_1' ).val() == validatedAddress[validatedAddress["type"] + "_address_1"]) &&
				($( 'input#' + validatedAddress["type"] + '_address_2' ).val()?.trim() == undefined || $( 'input#' + validatedAddress["type"] + '_address_2' ).val() == validatedAddress[validatedAddress["type"] + "_address_2"]) &&
				($( '#' + validatedAddress["type"] + '_city' ).val()?.trim() == undefined || $( '#' + validatedAddress["type"] + '_city' ).val().trim() == validatedAddress[validatedAddress["type"] + "_city"]) &&
				($( '#' + validatedAddress["type"] + '_state' ).val()?.trim() == undefined || $( '#' + validatedAddress["type"] + '_state' ).val().trim() == validatedAddress[validatedAddress["type"] + "_state"]) &&
				($( '#' + validatedAddress["type"] + '_country' ).val()?.trim() == undefined || $( '#' + validatedAddress["type"] + '_country' ).val().trim() == validatedAddress[validatedAddress["type"] + "_country"]) &&
				($( 'input#' + validatedAddress["type"] + '_postcode' ).val()?.trim() == undefined || $( 'input#' + validatedAddress["type"] + '_postcode' ).val().trim() == validatedAddress[validatedAddress["type"] + "_postcode"])))
			{
				$("#wc-avatax-address-validation").hide();
				invalidate_address();
			}
		}
	} );
	function invalidate_address()
	{
		var data = {
			action:   'wc_avatax_revalidate_customer_address_on_addresschange',
			nonce:     wc_avatax_frontend.address_validation_nonce
		};
			
		$.ajax( {
			type:     'POST',
			async :   false,
			url:      wc_avatax_frontend.ajax_url,
			data:     data,
			dataType: 'json',
			success:  function( response ) {
				console.log(response);
			}
		} );

	}
	// Validate an address
	$( '.wc_avatax_validate_address' ).on( 'click', function( e ) {
		validatedAddress = null;
		wc_avatax_current_validate_button = this;
		e.preventDefault();

		var form   = $( 'form.woocommerce-checkout' ),
			type,
			address_1,
			address_2,
			city,
			state,
			country,
			postcode;

		// Block the checkout form
		var form_data = form.data();

		if ( 1 !== form_data['blockUI.isBlocked'] ) {
			form.block( {
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			} );
		}

		type      = $( this ).data( 'address-type' );
		address_1 = $( 'input#' + type + '_address_1' ).val();
		address_2 = $( 'input#' + type + '_address_2' ).val();
		city      = $( '#' + type + '_city' ).val();
		state     = $( '#' + type + '_state' ).val();
		country   = $( '#' + type + '_country' ).val();
		postcode  = $( 'input#' + type + '_postcode' ).val();
		
		// Build request data
		var data = {
			action:   'wc_avatax_validate_customer_address',
			nonce:     wc_avatax_frontend.address_validation_nonce,
			type:      type,
			address_1: address_1,
			address_2: address_2,
			city:      city,
			state:     state,
			country:   country,
			postcode:  postcode
		};

		$.ajax( {
			type:     'POST',
			url:      wc_avatax_frontend.ajax_url,
			data:     data,
			dataType: 'json',
			success:  function( response ) {

				var notice = false;

				if ( response.code === 200 ) {

					$.each( response.address, function( field, value ) {
						$( '#' + field ).val( value ).trigger( 'change' );
					} );

					notice = '<div id="wc-avatax-address-validation" class="wc-avatax-address-validation-result wc-avatax-address-validation-success">' + wc_avatax_frontend.i18n.address_validated + '</div>';
					validatedAddress = response.address;
					validatedAddress["type"] = type;

				} else if ( response.error ) {

					notice = response.error;
				}

				if ( notice ) {
					$( '.woocommerce-error, .woocommerce-message, .wc-avatax-address-validation-result' ).remove();
					$(wc_avatax_current_validate_button).css( 'margin-bottom', '20px' ).after( notice );
				}

				// Unblock the checkout form
				form.unblock();

				// Update the order review
				$( document.body ).trigger( 'update_checkout' );
			}
		} );

	} );

} );
