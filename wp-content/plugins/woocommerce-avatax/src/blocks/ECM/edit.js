/**
 * Internal dependencies
 */

import {
	useBlockProps
} from '@wordpress/block-editor';

import { __ } from '@wordpress/i18n'; 

export const Edit = ({ attributes, setAttributes }) => {
	const blockProps = useBlockProps();
	return (
		<div {...blockProps}>
			<div className="wp-block-woocommerce-checkout-order-summary-ecm-links-block wc-block-components-totals-wrapper">
				<div className="wc-block-components-totals-ecm-links">
					<a
						role="button"
						id="cert_link"
						href="#"
						className="wc-block-components-totals-ecm-link"
						aria-label={ __(
							'Add Certificates',
							'woo-gutenberg-products-block'
						) }
					>
						{ __( 'Add Certificates', 'woo-gutenberg-products-block' ) }
					</a>
					<div class="test"><a
						role="button"
						href="#"
						className="wc-block-components-totals-ecm-link"
						aria-label={ __(
							'Manage existing certificates',
							'woo-gutenberg-products-block'
						) }
					>
						{ __( 'Manage existing certificates', 'woo-gutenberg-products-block' ) }
					</a></div>
				</div>
			</div>
		</div>
	);
};

export const Save = () => {
	return <div { ...useBlockProps.save() } />;
};