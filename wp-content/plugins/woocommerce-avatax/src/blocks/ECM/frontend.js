/**
 * External dependencies
 */
import { registerCheckoutBlock } from '@woocommerce/blocks-checkout';
import { __ } from '@wordpress/i18n';
import $ from 'jquery';
/**
 * Internal dependencies
 */
import metadata from './block.json';

const Block = ({ children, checkoutExtensionData }) => {
	const handleAddCertificateClick = (e) => {
		
			$(".container").css({
				"overflow-y": "",
				"height": ""
			});
			$('#pop').show();
			$('#overlay').show();
			return false;
		
	};
	const handleManageCertificateClick = (e) => {
		var data = {
            action: 'wc_avatax_manage_certificate_link',
        };
        jQuery.post(wc_avatax_frontend.ajax_url, data, function(response) {
			window.location = response.data;
            return;
        })
		 
   };

	return (
		<div className="wp-block-woocommerce-checkout-order-summary-ecm-links-block wc-block-components-totals-wrapper">
			<div className="wc-block-components-totals-ecm-links">
				<a
					role="button"
					id="cert_link"
					href="#"
					className="wc-block-components-totals-ecm-link"
					aria-label={__(
						'Add Certificates',
						'woo-gutenberg-products-block'
					)}
					onClick={handleAddCertificateClick}
				>
					{__('Add Certificates', 'woo-gutenberg-products-block')}
				</a>
				<div><a
					role="button"
					href="#"
					className="wc-block-components-totals-ecm-link"
					aria-label={__(
						'Manage existing certificates',
						'woo-gutenberg-products-block'
					)}
					onClick={handleManageCertificateClick}
				>
					{__('Manage existing certificates', 'woo-gutenberg-products-block')}
				</a></div>
			</div>
		</div>
	)
}

registerCheckoutBlock({
	metadata,
	component: Block,
});