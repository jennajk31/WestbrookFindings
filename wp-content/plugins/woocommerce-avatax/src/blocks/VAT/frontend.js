import metadata from './block.json';
import React, { useEffect } from 'react';
import { extensionCartUpdate  } from '@woocommerce/blocks-checkout';
import { __ } from '@wordpress/i18n';

// Global import
const { registerCheckoutBlock } = wc.blocksCheckout;
const { useSelect  } = window.wp.data;
const { CHECKOUT_STORE_KEY} = window.wc.wcBlocksData;

const CheckoutPage = () => {
	const orderid = useSelect( ( select ) => select( CHECKOUT_STORE_KEY ).getOrderId());
	const isCountryApplicableForVAT = wc_avatax_frontend.vat_field_applicable;
	
	const handleVatId = (value) => {
		
		extensionCartUpdate( {
			namespace: 'address-validation-block',
			data: {
				action : "VAT_ID",
				vatid : value,
				orderid : orderid
			},
		} )
	};
	useEffect(() => {
		if (isCountryApplicableForVAT !== "1") {
			handleVatId('');
		}
	  }, []);
	

	return (
		<div className="wc-block-components-address-form">
		{ isCountryApplicableForVAT === "1" && (
			<>
		<div class="wc-block-components-text-input wc-block-components-address-form__VAT is-active"><input id="billing_wc_avatax_checkout_vat_id" autocapitalize="none" aria-label="VAT" required onBlur={(e) => handleVatId(e.target.value)} type='text'></input><label for="billing_wc_avatax_checkout_vat_id">VAT</label></div>
		</>
			) }
		</div>
	);
}

const options = {
	metadata,
	component: CheckoutPage
};

registerCheckoutBlock( options );