import metadata from './block.json';
import { Button, extensionCartUpdate  } from '@woocommerce/blocks-checkout';
import { __ } from '@wordpress/i18n';
import { useMemo } from '@wordpress/element';

// Global import
const { registerCheckoutBlock } = wc.blocksCheckout;
const { useSelect, useDispatch  } = window.wp.data;
const { VALIDATION_STORE_KEY } = window.wc.wcBlocksData;

const Block = ({ cart }) => { 
	const [validatedAddress, setValidatedAddress] = React.useState(null);
	const validationError = useSelect( ( select ) => {
		const store = select( VALIDATION_STORE_KEY );
		return store.getValidationError( 'btn-address-validation' );
	}, [] );
	const [validatedMessage, setValidatedMessage] = React.useState('');

	const { clearValidationError, setValidationErrors } =
		useDispatch( VALIDATION_STORE_KEY );

		const isCountryApplicableForValidation = useMemo( () => {
			const currentBillingCountry = cart.billingAddress.country;
			const isCountryAllowed = wc_avatax_frontend.address_validation_countries.includes(currentBillingCountry) ? 'true' : 'false';
			if(isCountryAllowed === 'false')
			{
				clearValidationError( 'btn-address-validation' );
			}
			else
			{
				setValidationErrors( {
					'btn-address-validation': {
						message: 'Validate Address',
						hidden: true,
					},
				} );
			}
			return isCountryAllowed;
		}, [ cart.billingAddress.country ] );
		useMemo( () => {
			setValidatedMessage('');
			const isAddressSame = (validatedAddress!=null && 
									cart.billingAddress.country == validatedAddress["country"] &&
									cart.billingAddress.state == validatedAddress["state"] &&
									cart.billingAddress.city == validatedAddress["city"] &&
									cart.billingAddress.postcode == validatedAddress["postcode"] &&
									cart.billingAddress.address_1 == validatedAddress["address_1"] &&
									cart.billingAddress.address_2 == validatedAddress["address_2"])
			if(!isAddressSame)
			{
				setValidationErrors( {
					'btn-address-validation': {
						message: 'Validate Address',
						hidden: true,
					},
				} );
			}
			if (isAddressSame) {
				setValidatedMessage('Address Validated');
				clearValidationError( 'btn-address-validation' );
			}
			return isAddressSame;
		}, [ cart.billingAddress, validatedAddress ] );

		const handleAddressValidation = () => {
		clearValidationError( 'btn-address-validation' );
		setValidatedMessage('');

		extensionCartUpdate( {
			namespace: 'address-validation-block',
			data: {
				action : "billing_validate_address",
				address : cart.billingAddress,
			},
		} )
		.then( (res) => {
            setValidatedMessage('Address Validated');
			if(res && res.billing_address)
			{
				setValidatedAddress(res.billing_address);
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
			{ isCountryApplicableForValidation === 'true' && (
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