import { __ } from '@wordpress/i18n';

const { registerPlugin } = window.wp.plugins;
const { ExperimentalOrderMeta } = wc.blocksCheckout;

const CheckoutMessageComponent = ( { cart, extensions } ) => {
	return <div dangerouslySetInnerHTML={{ __html:  extensions['avatax-checkout-message-namespace']!=null ? extensions['avatax-checkout-message-namespace']['messages']  : "" }}></div>;
};

const render = () => {
    
	return (
		<ExperimentalOrderMeta>
			<CheckoutMessageComponent />
		</ExperimentalOrderMeta>
	);
};

registerPlugin( 'avatax-checkout-message-namespace', {
	render,
	scope: 'woocommerce-checkout',
} );