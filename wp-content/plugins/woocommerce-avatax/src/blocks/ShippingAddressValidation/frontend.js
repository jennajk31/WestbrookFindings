import metadata from './block.json';
import { Button, extensionCartUpdate  } from '@woocommerce/blocks-checkout';
import { __ } from '@wordpress/i18n';
import { useMemo } from '@wordpress/element';

// Global import
const { registerCheckoutBlock } = wc.blocksCheckout;
const { useSelect, useDispatch  } = window.wp.data;
const { VALIDATION_STORE_KEY, CHECKOUT_STORE_KEY} = window.wc.wcBlocksData;

const Block = ({ cart }) => { 
	const [validatedAddress, setValidatedAddress] = React.useState(null);
	const validationError = useSelect( ( select ) => {
		const store = select( VALIDATION_STORE_KEY );
		return store.getValidationError( 'btn-address-validation' );
	}, [] );
	const [validatedMessage, setValidatedMessage] = React.useState('');
	const useShippingAsBilling = useSelect( ( select ) => {
		const checkout = select( CHECKOUT_STORE_KEY );
		return checkout.getUseShippingAsBilling();
	}, [] );

	const { clearValidationError, setValidationErrors } =
		useDispatch( VALIDATION_STORE_KEY );

		const isApplicableForValidation = useMemo( () => {
			setValidatedMessage('');
			const currentShippingCountry = cart.shippingAddress.country;
			const isCountryAllowed = wc_avatax_frontend.address_validation_countries.includes(currentShippingCountry) ? 'true' : 'false';
			if(isCountryAllowed === 'false')
			{
				clearValidationError( 'btn-address-validation' );
			}
			//(is country allowed and setting is enabled for shipping) || (is country allowed and useShippingAsBilling and setting is enabled for billing)
			const isValidationApplicable = (isCountryAllowed === 'true' && wc_avatax_frontend.tax_based_on == "shipping") || (isCountryAllowed === 'true' && useShippingAsBilling && wc_avatax_frontend.tax_based_on == "billing");
			if(isValidationApplicable)
			{
				setValidationErrors( {
					'btn-address-validation': {
						message: 'Validate Address',
						hidden: true,
					},
				} );
			}
			return isValidationApplicable;
		}, [ cart.shippingAddress.country, useShippingAsBilling ] );

		useMemo( () => {
			setValidatedMessage('');
			const currentShippingCountry = cart.shippingAddress.country;
			const isCountryAllowed = wc_avatax_frontend.address_validation_countries.includes(currentShippingCountry) ? 'true' : 'false';
			//(is country allowed and setting is enabled for shipping) || (is country allowed and useShippingAsBilling and setting is enabled for billing)
			const isValidationApplicable = (isCountryAllowed === 'true' && wc_avatax_frontend.tax_based_on == "shipping") || (isCountryAllowed === 'true' && useShippingAsBilling && wc_avatax_frontend.tax_based_on == "billing");
			const isAddressSame = (validatedAddress!=null && 
									cart.shippingAddress.country == validatedAddress["country"] &&
									cart.shippingAddress.state == validatedAddress["state"] &&
									cart.shippingAddress.city == validatedAddress["city"] &&
									cart.shippingAddress.postcode == validatedAddress["postcode"] &&
									cart.shippingAddress.address_1 == validatedAddress["address_1"] &&
									cart.shippingAddress.address_2 == validatedAddress["address_2"])
			if(isValidationApplicable && !isAddressSame)
			{
				setValidationErrors( {
					'btn-address-validation': {
						message: 'Validate Address',
						hidden: true,
					},
				} );
			}
			if (isValidationApplicable && isAddressSame) {
				setValidatedMessage('Address Validated');
				clearValidationError( 'btn-address-validation' );
			}
			return isValidationApplicable && !isAddressSame;
		}, [ cart.shippingAddress, useShippingAsBilling, validatedAddress ] );
	
		const handleAddressValidation = () => {
		clearValidationError( 'btn-address-validation' );
		setValidatedMessage('');
		extensionCartUpdate( {
			namespace: 'address-validation-block',
			data: {
				action : "shipping_validate_address",
				address : cart.shippingAddress,
			},
		} )
		.then( (res) => {
            setValidatedMessage('Address Validated');
			if (res && res.shipping_address) {
				setValidatedAddress(res.shipping_address);
			}
        })
		.catch( ( { message } ) => {
			setValidationErrors( {
				'btn-address-validation': {
					message,
					hidden: false,
				},
			} );
		} );
	};
	return (
		<div className={ 'example-fields' }>
			{ isApplicableForValidation  && (
				<>
					<Button
						className="wc_avatax_validate_address button"
						id="btn-address-validation"
						onClick={ handleAddressValidation }
					>
						Validate Address
					</Button>
					{ validationError?.hidden ? null : (
						<div className="wc-block-components-validation-error">
							<p dangerouslySetInnerHTML={{ __html:  validationError?.message  }}></p>
							<p>{ validatedMessage }</p>
						</div>
					) }
				</>
			) }
		</div>
	);
}

const options = {
	metadata,
	component: Block
};

registerCheckoutBlock( options );